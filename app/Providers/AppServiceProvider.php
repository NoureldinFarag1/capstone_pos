<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Item;
use App\Models\Brand;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            // OPTIMIZED: Load relationships only when needed and limit low stock items
            // We'll also cache these values to avoid repeated queries
            $viewName = $view->getName();

            // Only include necessary data based on the view
            $needsLowStockItems = strpos($viewName, 'dashboard') !== false ||
                                  strpos($viewName, 'items') !== false;
            $needsBrands = strpos($viewName, 'brands') !== false ||
                          strpos($viewName, 'items') !== false ||
                          strpos($viewName, 'categories') !== false;
            $needsCategories = strpos($viewName, 'categories') !== false ||
                              strpos($viewName, 'items') !== false;

            // Only query what's needed for this specific view
            if ($needsLowStockItems) {
                $lowStockItems = Item::where('quantity', '<=', 10)
                                    ->where('is_parent', false)
                                    ->orderBy('quantity', 'asc')
                                    ->limit(15) // Limit to 15 most critical items
                                    ->get();
                $view->with('lowStockItems', $lowStockItems);
            }

            if ($needsBrands) {
                $brands = Brand::all();
                $view->with('brands', $brands);
            }

            if ($needsCategories) {
                $categories = Category::all();
                $view->with('categories', $categories);
            }
        });
    }
}
