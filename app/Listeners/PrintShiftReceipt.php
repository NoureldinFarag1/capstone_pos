<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\Sale;
use App\Models\User as AppUser;
use Carbon\Carbon;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\EscposImage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PrintShiftReceipt
{
    public function handle(Logout $event): void
    {
        try {
            $authUser = $event->user;
            if (!$authUser) {
                return;
            }

            // Resolve to our concrete User model to avoid typed property issues
            $userId = method_exists($authUser, 'getAuthIdentifier') ? $authUser->getAuthIdentifier() : ($authUser->id ?? null);
            if (!$userId) {
                return;
            }
            $user = AppUser::find($userId);
            if (!$user) {
                return;
            }

            // Determine shift window
            $shiftStart = $user->last_login ? Carbon::parse($user->last_login) : Carbon::today();
            $shiftEnd = Carbon::now();

            // Gather sales for the user during the shift window
            $sales = Sale::where('user_id', $user->id)
                ->whereBetween('created_at', [$shiftStart, $shiftEnd])
                ->get();

            $total = (float) $sales->sum('total_amount');
            $cash = (float) $sales->where('payment_method', 'cash')->sum('total_amount');
            $credit = (float) $sales->where('payment_method', 'credit_card')->sum('total_amount');
            $mobile = (float) $sales->where('payment_method', 'mobile_pay')->sum('total_amount');
            $cod = (float) $sales->where('payment_method', 'cod')->sum('total_amount');

            // Compute duration (HH:MM)
            $interval = $shiftStart->diff($shiftEnd);
            $hours = $interval->d * 24 + $interval->h; // include days converted to hours
            $minutes = $interval->i;
            $duration = sprintf('%02dh %02dm', $hours, $minutes);

            // Printer setup
            $connector = $this->getPrinterConnector();
            $printer = new Printer($connector);
            $printer->initialize();

            // Optional store info
            $storeName = Config::get('receipt.store_name', 'Local HUB');
            $logoPath = Config::get('receipt.logo_path');

            // Header
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(2, 2);
            $printer->text($storeName . "\n");
            $printer->setTextSize(1, 1);

            // Logo (optional)
            if ($logoPath && file_exists($logoPath)) {
                try {
                    $logo = EscposImage::load($logoPath);
                    $printer->bitImage($logo);
                    $printer->feed(1);
                } catch (\Exception $e) {
                    Log::warning('Shift logo print failed: ' . $e->getMessage());
                }
            }

            $printer->text("Shift Summary Receipt\n");
            $printer->text(str_repeat('-', 32) . "\n");

            // Body
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text('User: ' . ($user->name ?? ('User#' . $user->id)) . "\n");
            $printer->text('Start: ' . $shiftStart->format('Y-m-d H:i') . "\n");
            $printer->text('End:   ' . $shiftEnd->format('Y-m-d H:i') . "\n");
            $printer->text('Duration: ' . $duration . "\n");
            $printer->text(str_repeat('-', 32) . "\n");

            $printer->text(sprintf("%-20s %11s\n", 'Total Sales:', number_format($total, 2)));
            $printer->text(str_repeat('-', 32) . "\n");
            $printer->text(sprintf("%-20s %11s\n", 'Cash:', number_format($cash, 2)));
            $printer->text(sprintf("%-20s %11s\n", 'Credit Card:', number_format($credit, 2)));
            $printer->text(sprintf("%-20s %11s\n", 'Mobile Pay:', number_format($mobile, 2)));
            $printer->text(sprintf("%-20s %11s\n", 'COD:', number_format($cod, 2)));

            $printer->feed(2);
            $printer->cut();
            $printer->close();
        } catch (\Throwable $e) {
            Log::error('Shift receipt print error: ' . $e->getMessage());
        }
    }

    private function getPrinterConnector()
    {
        $configPath = base_path('printer_config.json');
        $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : null;

        if (PHP_OS_FAMILY === 'Windows') {
            $printerName = $config['windows'] ?? 'Receipt Printer';
            return new WindowsPrintConnector($printerName);
        }

        $printerName = $config['mac'] ?? 'Receipt Printer';
        return new CupsPrintConnector($printerName);
    }
}
