<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CashDrawer;

class CashDrawerController extends Controller
{
    public function show()
    {
        return view('cash-drawer.input');
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        // Store the cash drawer entry
        CashDrawer::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'created_at' => now(),
        ]);

        // Mark session as cash drawer set
        session(['cash_drawer_set' => true]);

        return redirect()->route('sales.index')->with('success', 'Cash drawer amount set.');
    }
}
