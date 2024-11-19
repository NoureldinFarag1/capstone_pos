<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;

class BaseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests, DispatchesJobs;

    // Method to get low stock items
    protected function getLowStockItems($threshold = 10)
    {
        return Item::where('quantity', '<=', $threshold)->get();
    }

    // put any shared logic here
}
