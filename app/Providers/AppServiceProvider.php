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
        $lowStockItems = Item::where('quantity', '<=', 10)->get();
        $brands = Brand::all();
        $categories = Category::all();
        $view->with('lowStockItems', $lowStockItems);
        $view->with('categories', $categories);
        $view->with('brands', $brands);
    });
    }
}
