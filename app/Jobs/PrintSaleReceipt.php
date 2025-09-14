<?php

namespace App\Jobs;

use App\Http\Controllers\SaleController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PrintSaleReceipt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $saleId;

    public function __construct(int $saleId)
    {
        $this->saleId = $saleId;
        $this->onQueue('printing');
    }

    public function handle(): void
    {
        try {
            // Invoke the existing printing logic; ignore the returned response object
            app(SaleController::class)->printThermalReceipt($this->saleId);
        } catch (\Throwable $e) {
            Log::error('PrintSaleReceipt job failed: ' . $e->getMessage());
        }
    }
}
