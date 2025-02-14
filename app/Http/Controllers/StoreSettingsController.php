<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class StoreSettingsController extends Controller
{
    public function index()
    {
        $storeName = Config::get('receipt.store_name');
        $storeSlogan = Config::get('receipt.store_slogan');
        $storeInstagram = Config::get('receipt.store_instagram');
        $logoPath = Config::get('receipt.logo_path');

        return view('store_settings.index', compact('storeName', 'storeSlogan', 'storeInstagram', 'logoPath'));
    }

    public function edit()
    {
        $storeName = Config::get('receipt.store_name');
        $storeSlogan = Config::get('receipt.store_slogan');
        $storeInstagram = Config::get('receipt.store_instagram');
        $logoPath = Config::get('receipt.logo_path');

        return view('store_settings.edit', compact('storeName', 'storeSlogan', 'storeInstagram', 'logoPath'));
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_slogan' => 'nullable|string|max:255',
            'store_instagram' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = 'RECEIPTLOGO.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('images'), $logoName);
            $validatedData['logo_path'] = public_path('images/' . $logoName);
        }

        // Update configuration values using array manipulation
        $configPath = config_path('receipt.php');
        $config = require $configPath;

        $config['store_name'] = $validatedData['store_name'];
        $config['store_slogan'] = $validatedData['store_slogan'] ?? '';
        $config['store_instagram'] = $validatedData['store_instagram'] ?? '';
        if (isset($validatedData['logo_path'])) {
            $config['logo_path'] = $validatedData['logo_path'];
        }

        // Convert the array to a string
        $configString = var_export($config, true);
        $configString = "<?php\n\nreturn " . $configString . ";\n";

        // Write the updated configuration back to the file
        File::put($configPath, $configString);

        // Clear config cache
        Artisan::call('config:clear');

        return redirect()->route('dashboard');
    }
}
