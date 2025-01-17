<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Sale::select('customer_name', 'customer_phone', DB::raw('MAX(created_at) as latest_sale_date'))
        ->whereNotNull('customer_name')
        ->whereNotNull('customer_phone')
        ->groupBy('customer_name', 'customer_phone')
        ->paginate(10);

        return view('customers.index', compact('customers'));
    }

    public function fetchName(Request $request)
    {
        $phone = $request->query('phone');
        $customer = Sale::where('customer_phone', $phone)->first();

        if ($customer) {
            return response()->json(['success' => true, 'name' => $customer->customer_name]);
        } else {
            return response()->json(['success' => false, 'name' => null]);
        }
    }
}
