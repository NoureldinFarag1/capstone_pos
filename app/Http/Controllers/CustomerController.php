<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Customer::query()
            ->when($search, function($q) use ($search) {
                return $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderByDesc('last_visit');

        $customers = $query->paginate(15);

        return view('customers.index', compact('customers', 'search'));
    }

    /**
     * Show the form for creating a new customer
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20|unique:customers,phone',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'address' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer added successfully');
    }

    /**
     * Display the specified customer and their history
     */
    public function show(Customer $customer)
    {
        $customer->load(['sales' => function($query) {
            $query->orderByDesc('created_at')->limit(10);
        }]);

        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20|unique:customers,phone,'.$customer->id,
            'email' => 'nullable|email|max:255|unique:customers,email,'.$customer->id,
            'address' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully');
    }

    /**
     * Remove the specified customer
     */
    public function destroy(Customer $customer)
    {
        // Check if customer has associated sales
        $hasSales = $customer->sales()->exists();

        if ($hasSales) {
            return back()->with('error', 'Cannot delete customer with sales history. Consider anonymizing instead.');
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully');
    }

    /**
     * Fetch customer by phone number (AJAX)
     */
    public function fetchByPhone(Request $request)
    {
        $phone = $request->query('phone');
        $customer = Customer::where('phone', $phone)->first();

        if ($customer) {
            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                    'address' => $customer->address
                ]
            ]);
        }

        return response()->json(['success' => false]);
    }

    /**
     * Legacy method to maintain compatibility with existing code
     */
    public function fetchName(Request $request)
    {
        $phone = $request->query('phone');
        $customer = Customer::where('phone', $phone)->first();

        if (!$customer) {
            // Fallback to old logic for backward compatibility
            $saleRecord = Sale::where('customer_phone', $phone)->first();

            if ($saleRecord) {
                return response()->json(['success' => true, 'name' => $saleRecord->customer_name]);
            }

            return response()->json(['success' => false, 'name' => null]);
        }

        return response()->json(['success' => true, 'name' => $customer->name]);
    }

    /**
     * Search customers by phone or name (AJAX)
     */
    public function search(Request $request)
    {
        // Get query from either 'query' parameter or 'phone' parameter
        $query = $request->input('query') ?? $request->input('phone');
        $limit = $request->input('limit', 5);

        // Debug the input
        \Log::info('Customer search query:', [
            'query' => $query,
            'phone' => $request->input('phone'),
            'all_params' => $request->all()
        ]);

        if (empty($query)) {
            return response()->json(['success' => false, 'message' => 'No search query provided', 'customers' => []]);
        }

        // Try finding an exact match by phone number first
        $exactMatchCustomer = Customer::where('phone', $query)->first();

        if ($exactMatchCustomer) {
            // If we have an exact match, return just that customer
            return response()->json([
                'success' => true,
                'exact_match' => true,
                'customers' => [$exactMatchCustomer->only(['id', 'name', 'phone', 'total_spent', 'total_visits'])]
            ]);
        }

        // If no exact match was found, do a partial search
        $customers = Customer::where('phone', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->orderByDesc('last_visit')
            ->limit($limit)
            ->get(['id', 'name', 'phone', 'total_spent', 'total_visits']);

        return response()->json([
            'success' => true,
            'exact_match' => false,
            'customers' => $customers
        ]);
    }
}
