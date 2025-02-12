<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    public function editPrinter()
    {
        return view('settings.printer');
    }

    public function updatePrinter(Request $request)
    {
        $request->validate([
            'printer_name' => 'required|string|max:255',
        ]);

        $printerName = $request->input('printer_name');

        // Update the .env file
        $this->setEnv('PRINTER_NAME', $printerName);

        // Clear the config cache
        Artisan::call('config:cache');

        return redirect()->route('settings.printer.edit')->with('success', 'Printer name updated successfully.');
    }

    private function setEnv($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                "$key=" . env($key),
                "$key=$value",
                file_get_contents($path)
            ));
        }
    }
}
