<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SaleController;
use App\Exports\SalesPerBrandExport;
use Maatwebsite\Excel\Facades\Excel;

// Logout route
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Export sales per brand to Excel
Route::get('/sales/export', [SaleController::class, 'exportSalesPerBrand'])->name('sales.export');


// Resource routes for brands, categories, items, and sales
Route::resource('brands', BrandController::class);
Route::resource('categories', CategoryController::class);
Route::resource('items', ItemController::class);
Route::resource('sales', SaleController::class)->except(['destroy']);
Route::delete('/sales/{id}', [SaleController::class, 'destroy'])->name('sales.destroy');

// Additional item-related routes
Route::get('/categories-by-brand/{brand_id}', [CategoryController::class, 'getCategoriesByBrand'])->name('categories.byBrand');
Route::get('/items-by-category/{category_id}', [ItemController::class, 'getItemsByCategory'])->name('items.byCategory');
Route::get('/api/items/search', [ItemController::class, 'searchByBarcode']);
Route::get('/api/items/filter', [ItemController::class, 'filterItems']);
Route::get('/get-categories/{brand}', [CategoryController::class, 'getCategories'])->name('get.categories');
Route::get('/get-items/{category}', [ItemController::class, 'getItems'])->name('get.items');
Route::get('/sales/export', [SaleController::class, 'exportSalesPerBrand'])->name('sales.export');
Route::post('/sales/{id}/thermal-receipt', [SaleController::class, 'printThermalReceipt'])->name('sales.thermalReceipt');
Route::get('/sales/{id}/invoice', [SaleController::class, 'printInvoice'])->name('sales.invoice');

// Default homepage
Route::get('/', function () {
    return view('welcome');
});

// Dashboard route
Route::get('/dashboard', function () {
    return view('layouts/dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
