<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $expenses = Expense::latest()->get();
        $todayTotal = Expense::whereDate('created_at', $today)->sum('amount');
        $todayCount = Expense::whereDate('created_at', $today)->count();
        $monthlyTotal = Expense::whereMonth('created_at', now()->month)->sum('amount');
        $monthlyCount = Expense::whereMonth('created_at', now()->month)->count();
        $averageExpense = Expense::avg('amount');

        // Add this new query for reason totals
        $reasonTotals = Expense::selectRaw('reason, SUM(amount) as total')
            ->whereMonth('created_at', now()->month)
            ->groupBy('reason')
            ->orderBy('total', 'desc')
            ->get();

        $previousReasons = Expense::select('reason')
            ->distinct()
            ->pluck('reason')
            ->toArray();

        $monthlyBreakdown = Expense::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, 
            SUM(amount) as total_amount, 
            COUNT(*) as transaction_count')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($item) {
                $date = Carbon::createFromFormat('Y-m', $item->month);
                return [
                    'month_name' => $date->format('F Y'),
                    'total_amount' => $item->total_amount,
                    'transaction_count' => $item->transaction_count
                ];
            });

        return view('expenses.index', compact(
            'expenses',
            'todayTotal',
            'todayCount',
            'monthlyTotal',
            'monthlyCount',
            'averageExpense',
            'reasonTotals',
            'previousReasons',
            'monthlyBreakdown'  // Add this line
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255'
        ]);

        Expense::create($validated);

        return redirect()->back()->with('success', 'Expense added successfully!');
    }

    public function edit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255'
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully!');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully!');
    }

    // Add this new method to the ExpenseController class
    public function getMonthlyReasons($month)
    {
        $date = Carbon::createFromFormat('F Y', $month);

        $reasons = Expense::selectRaw('reason, SUM(amount) as total')
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->groupBy('reason')
            ->orderBy('total', 'desc')
            ->get();

        return response()->json($reasons);
    }
}