<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrandTraceController;
use App\Http\Controllers\CashDrawerController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExpenseController; // Add this line
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ItemTraceController;
use App\Http\Controllers\VerificationController;

Route::get('/connection-test', function () {
    try {
        $pdo = DB::connection()->getPdo();
        $processlist = DB::select('SHOW PROCESSLIST');
        return [
            'connection_info' => [
                'database' => DB::connection()->getDatabaseName(),
                'host' => config('database.connections.mysql.host'),
                'port' => config('database.connections.mysql.port'),
                'socket' => config('database.connections.mysql.unix_socket'),
            ],
            'process_list' => $processlist
        ];
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

Route::get('/test-write', function () {
    try {
        DB::beginTransaction();

        // Try to insert a test record
        DB::table('items')->insert([
            'name' => 'test_item',
            'category_id' => '6',
            'brand_id' => '8',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::commit();
        return "Write test successful";
    } catch (\Exception $e) {
        DB::rollBack();
        return "Error: " . $e->getMessage();
    }
});

// Logout route (controller not available, use closure)
Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Keepalive endpoint to refresh session activity
Route::get('/keepalive', function () {
    // Touch the session to keep it alive
    Session::put('last_keepalive', now());
    return response()->noContent();
})->middleware(['web','auth'])->name('session.keepalive');

// Update the daily report route to use query parameters
Route::get('/sales/daily-report', [SaleController::class, 'generateDailyReport'])->name('sales.dailyReport');
Route::get('/sales/export', [SaleController::class, 'exportSalesPerBrand'])->name('sales.export');
Route::get('/sales/daily-totals-report', [SaleController::class, 'generateDailyTotalsReport'])->name('sales.dailyTotalsReport');


// COD tracking routes - moved higher up to avoid conflicts
Route::get('/sales/cod', [SaleController::class, 'codSales'])->name('sales.cod')->middleware('auth');
Route::post('/sales/{sale}/update-cod-status', [SaleController::class, 'updateCodStatus'])->name('sales.updateCodStatus')->middleware('auth');

Route::get('/items/export-inventory-csv/{brand_id?}', [ItemController::class, 'exportInventoryCSV'])
    ->name('items.exportInventoryCSV')
    ->middleware(['web', 'auth']);

Route::get('/items/export-sales-csv/{brand_id?}', [ItemController::class, 'exportSalesCSV'])
    ->name('items.exportSalesCSV')
    ->middleware(['web', 'auth']);

// Single route for CSV export
Route::get('/items/export-csv/{brand_id?}', [ItemController::class, 'exportCSV'])
    ->name('items.exportCSV')
    ->middleware(['web', 'auth']);

// XLSX multi-sheet inventory export (Inventory, Parent Items, Updates)
Route::get('/items/export-xlsx', [ItemController::class, 'itemsExport'])
    ->name('items.exportXlsx')
    ->middleware(['web', 'auth']);

Route::get('/items/sample-file', function () {
    $headers = ['name', 'brand_id', 'category_id', 'code', 'quantity', 'buying_price', 'selling_price', 'sale_price'];
    return response()->streamDownload(function () use ($headers) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $headers);
        fclose($file);
    }, 'sample_items.csv');
})->name('items.sampleFile');

Route::get('/sales/payment-method-report', [SaleController::class, 'generatePaymentMethodReport'])->name('sales.paymentMethodReport');
Route::get('/sales/hourly-report', [SaleController::class, 'generateHourlySalesReport'])->name('sales.hourlyReport');
Route::get('/sales/refunds-report', [SaleController::class, 'generateRefundsReport'])->name('sales.refundsReport');
Route::get('/sales/overview', [SaleController::class, 'overview'])->name('sales.overview');

// IMPORTANT: Customer specific routes must be defined BEFORE the resource route
// Customer search route for sales form
Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
Route::get('/customers/fetch-name', [CustomerController::class, 'fetchName']);

// Resource routes for brands, categories, items, and sales
// Brand trash management routes (must be before resource to avoid /brands/trash being treated as {brand})
Route::get('/brands/trash', [BrandController::class, 'trash'])->name('brands.trash');
Route::post('/brands/{id}/restore', [BrandController::class, 'restore'])->name('brands.restore');
Route::delete('/brands/{id}/force-delete', [BrandController::class, 'forceDelete'])->name('brands.forceDelete');
Route::resource('brands', BrandController::class);
Route::get('/api/brands/count', [BrandController::class, 'brandCount']);
// Brand print labels route
Route::get('/brands/{id}/print-labels', [BrandController::class, 'printLabels'])->name('brands.printLabels');

Route::resource('categories', CategoryController::class);

// Custom item routes (must be before resource routes)
Route::get('/items/bulk-import', [ItemController::class, 'bulkImportPage'])->name('items.bulkImportPage');
Route::post('/items/bulk-upload', [ItemController::class, 'bulkUpload'])->name('items.bulkUpload');
Route::get('/items/download-template', [ItemController::class, 'downloadTemplate'])->name('items.downloadTemplate');
Route::get('/items/download-demo-csv', [ItemController::class, 'downloadDemoCSV'])->name('items.downloadDemoCSV');
Route::post('/items/add-variant', [ItemController::class, 'addVariant'])->name('items.addVariant');
// Item print labels routes
Route::post('/items/print-labels', [ItemController::class, 'printItemLabels'])->name('items.printLabels');
Route::get('/items/{id}/print-label', [ItemController::class, 'printSingleItemLabel'])->name('items.printSingleLabel');

Route::resource('items', ItemController::class);
Route::resource('customers', CustomerController::class); // Add proper resource route for customers
Route::resource('sales', SaleController::class)->except(['destroy']);
Route::delete('/sales/{id}', [SaleController::class, 'destroy'])->name('sales.destroy');
Route::delete('/sales/{sale}/delete-all-items', [SaleController::class, 'deleteAllItems'])->name('sales.deleteAllItems');
Route::put('/items/{id}', [ItemController::class, 'update'])->name('items.update');

// Additional item-related routes
Route::get('/categories-by-brand/{brand_id}', [CategoryController::class, 'getCategoriesByBrand'])->name('categories.byBrand');
Route::get('/items-by-category/{category_id}', [ItemController::class, 'getItemsByCategory'])->name('items.byCategory');
Route::post('/sales/search-barcode', [SaleController::class, 'searchByBarcode'])->name('sales.searchBarcode');
Route::get('/api/items/filter', [ItemController::class, 'filterItems']);
Route::get('/get-categories/{brand}', [CategoryController::class, 'getCategories'])->name('get.categories');
Route::get('/get-items/{category}', [ItemController::class, 'getItems'])->name('get.items');
Route::post('/sales/{id}/thermal-receipt', [SaleController::class, 'printThermalReceipt'])->name('sales.thermalReceipt');
Route::get('/sales/{id}/invoice', [SaleController::class, 'printInvoice'])->name('sales.invoice');
Route::resource('sizes', SizeController::class);
Route::get('/items/findByBarcode/{barcode}', function ($barcode) {
    $item = App\Models\Item::where('code', $barcode)->first();
    if ($item) {
        return response()->json(['item' => $item]);
    }
    return response()->json(['item' => null]);
});
Route::post('/items/{id}/print-label', [ItemController::class, 'printLabel'])->name('items.print-label');
Route::get('/refund', [RefundController::class, 'index'])->name('refunds.index');
// Use a single canonical route for showing the refund form for a specific sale
Route::get('/refund/create/{sale_id}', [RefundController::class, 'create'])->name('refund.create');
// Process refund submissions
Route::post('/refund', [RefundController::class, 'store'])->name('refund.store');
Route::middleware(['auth'])->group(function () {
    Route::get('/cash-drawer', [CashDrawerController::class, 'showForm'])->name('cash-drawer.form');
    Route::post('/cash-drawer', [CashDrawerController::class, 'store'])->name('cash-drawer.store');
});
// Keep only one named route for refund creation to avoid ambiguity
// Route::get('/sales/{sale}/refund', [RefundController::class, 'create'])->name('refund.create'); // Removed duplicate
Route::resource('colors', ColorController::class);
Route::post('/sales/print-gift-receipt', [SaleController::class, 'printGiftReceipt'])->name('sales.print-gift-receipt');
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::post('/customers/create', [CustomerController::class, 'store'])->name('customers.store');
Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');

// Customer search route for sales form
Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');

Route::post('/items/update-variants-quantity', [ItemController::class, 'updateVariantsQuantity'])
    ->name('items.updateVariantsQuantity')
    ->middleware('auth');

// Make sure this route is placed before any resource or generic routes
Route::post('/items/generate-barcodes', [ItemController::class, 'generateBarcodes'])
    ->name('items.generate-barcodes')
    ->middleware(['web', 'auth']);

// Add this route for handling exports
Route::get('/settings/printer', [SettingsController::class, 'editPrinter'])->name('settings.printer.edit');
Route::put('/settings/printer', [SettingsController::class, 'updatePrinter'])->name('settings.printer.update');

Route::get('/store-settings', [App\Http\Controllers\StoreSettingsController::class, 'index'])->name('store-settings.index');
Route::get('/store-settings/edit', [App\Http\Controllers\StoreSettingsController::class, 'edit'])->name('store-settings.edit');
Route::put('/store-settings', [App\Http\Controllers\StoreSettingsController::class, 'update'])->name('store-settings.update');

Route::get('/loyal-customers', [SaleController::class, 'loyalCustomers'])->name('sales.loyal-customers');
Route::get('/sales/{period}/{method}', [SaleController::class, 'paymentMethodSales'])
    ->name('sales.by-payment-method')
    ->where('period', 'daily|monthly')
    ->where('method', 'cash|credit_card|mobile_pay|cod');
Route::get('/brands/most-selling', [DashboardController::class, 'mostSellingBrands'])->name('brands.most-selling');
Route::get('/customers/fetch-name', [CustomerController::class, 'fetchName']);
Route::post('/backup/download', [BackupController::class, 'download'])->name('backup.download');

// Default homepage
Route::get('/', function () {
    return view('welcome');
})->middleware(['auth', 'role.redirect']);

Route::get('/items/export-brand-sales', [ItemController::class, 'exportBrandSales'])->name('items.exportBrandSales');

// Update the authenticated routes group
Route::middleware(['auth'])->group(function () {
    // Add trace items routes at the beginning of the auth middleware group
    Route::get('/trace-items', [ItemTraceController::class, 'index'])->name('items.trace');
    Route::post('/trace-items', [ItemTraceController::class, 'trace'])->name('items.trace.search');

    // Add these routes with the other trace routes
    Route::get('/trace-brands', [BrandTraceController::class, 'index'])->name('brands.trace');
    Route::post('/trace-brands', [BrandTraceController::class, 'trace'])->name('brands.trace.search');

    // Routes accessible by all authenticated users
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin/moderator only routes
    Route::middleware(['role:admin|moderator'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});

Route::get('/sales/{sale}/exchange', [SaleController::class, 'showExchangeForm'])->name('sales.showExchangeForm');
Route::post('/sales/{sale}/exchange', [SaleController::class, 'exchange'])->name('sales.exchange');

Route::post('/items/{id}/toggle-discount', [ItemController::class, 'toggleDiscount'])
    ->name('items.toggleDiscount')
    ->middleware(['web', 'auth']);

Route::post('/brands/{id}/toggle-discount', [BrandController::class, 'toggleDiscount'])
    ->name('brands.toggleDiscount')
    ->middleware(['web', 'auth']);

Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
Route::resource('expenses', ExpenseController::class);

// Add this with your other expense routes
Route::get('/expenses/monthly-reasons/{month}', [ExpenseController::class, 'getMonthlyReasons'])->name('expenses.monthly-reasons');

Route::post('/verify-access', [VerificationController::class, 'verify'])->name('verify.access');

require __DIR__ . '/auth.php';
