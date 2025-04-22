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
        $receiptLogoPath = Config::get('receipt.logo_path');
        $navbarLogoPath = Config::get('navbar.logo_path');
        $navbarTextLogoPath = Config::get('navbar.text_logo_path');
        $siteTitle = Config::get('navbar.site_title');

        return view('store_settings.index', compact(
            'storeName',
            'storeSlogan',
            'storeInstagram',
            'receiptLogoPath',
            'navbarLogoPath',
            'navbarTextLogoPath',
            'siteTitle'
        ));
    }

    public function edit()
    {
        $storeName = Config::get('receipt.store_name');
        $storeSlogan = Config::get('receipt.store_slogan');
        $storeInstagram = Config::get('receipt.store_instagram');
        $receiptLogoPath = Config::get('receipt.logo_path');
        $navbarLogoPath = Config::get('navbar.logo_path');
        $navbarTextLogoPath = Config::get('navbar.text_logo_path');
        $siteTitle = Config::get('navbar.site_title');

        return view('store_settings.edit', compact(
            'storeName',
            'storeSlogan',
            'storeInstagram',
            'receiptLogoPath',
            'navbarLogoPath',
            'navbarTextLogoPath',
            'siteTitle'
        ));
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_slogan' => 'nullable|string|max:255',
            'store_instagram' => 'nullable|string|max:255',
            'site_title' => 'required|string|max:255',
            'receipt_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'navbar_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'navbar_text_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Handle receipt logo upload
        if ($request->hasFile('receipt_logo')) {
            $logo = $request->file('receipt_logo');
            $logoName = 'RECEIPTLOGO.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('images'), $logoName);
            $validatedData['receipt_logo_path'] = public_path('images/' . $logoName);
        }

        // Handle navbar logo upload
        if ($request->hasFile('navbar_logo')) {
            $navbarLogo = $request->file('navbar_logo');
            $navbarLogoName = 'logo.' . $navbarLogo->getClientOriginalExtension();
            $navbarLogo->move(public_path('images'), $navbarLogoName);
            $validatedData['navbar_logo_path'] = public_path('images/' . $navbarLogoName);
        }

        // Handle navbar text logo upload
        if ($request->hasFile('navbar_text_logo')) {
            $navbarTextLogo = $request->file('navbar_text_logo');
            $navbarTextLogoName = 'logo-text.' . $navbarTextLogo->getClientOriginalExtension();
            $navbarTextLogo->move(public_path('images'), $navbarTextLogoName);
            $validatedData['navbar_text_logo_path'] = public_path('images/' . $navbarTextLogoName);
        }

        // Update receipt configuration
        $receiptConfigPath = config_path('receipt.php');
        $receiptConfig = require $receiptConfigPath;

        $receiptConfig['store_name'] = $validatedData['store_name'];
        $receiptConfig['store_slogan'] = $validatedData['store_slogan'] ?? '';
        $receiptConfig['store_instagram'] = $validatedData['store_instagram'] ?? '';

        if (isset($validatedData['receipt_logo_path'])) {
            $receiptConfig['logo_path'] = $validatedData['receipt_logo_path'];
        }

        // Convert the array to a string for receipt config
        $receiptConfigString = var_export($receiptConfig, true);
        $receiptConfigString = "<?php\n\nreturn " . $receiptConfigString . ";\n";

        // Write the updated receipt configuration back to the file
        File::put($receiptConfigPath, $receiptConfigString);

        // Update navbar configuration
        $navbarConfigPath = config_path('navbar.php');
        $navbarConfig = require $navbarConfigPath;

        // Set site title
        $navbarConfig['site_title'] = $validatedData['site_title'];

        if (isset($validatedData['navbar_logo_path'])) {
            $navbarConfig['logo_path'] = $validatedData['navbar_logo_path'];
        }

        if (isset($validatedData['navbar_text_logo_path'])) {
            $navbarConfig['text_logo_path'] = $validatedData['navbar_text_logo_path'];
        }

        // Convert the array to a string for navbar config
        $navbarConfigString = var_export($navbarConfig, true);
        $navbarConfigString = "<?php\n\nreturn " . $navbarConfigString . ";\n";

        // Write the updated navbar configuration back to the file
        File::put($navbarConfigPath, $navbarConfigString);

        // Clear config cache
        Artisan::call('config:clear');

        return redirect()->route('dashboard')->with('success', 'Store settings updated successfully');
    }
}
