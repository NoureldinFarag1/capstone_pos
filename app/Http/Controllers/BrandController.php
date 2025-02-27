<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $brands = Brand::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->get();

        return view('brands.index', compact('brands'));
    }

    public function create()
    {
        return view('brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $picturePath = $request->file('picture')->store('brands', 'public');
        Brand::create([
            'name' => $request->name,
            'picture' => $picturePath,
        ]);
        return redirect()->route('brands.index');
    }

        public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('brands.edit', compact('brand'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $brand = Brand::findOrFail($id);

        // If a new picture is uploaded, store it
        if ($request->hasFile('picture')) {
            $picturePath = $request->file('picture')->store('brands', 'public');
            $brand->picture = $picturePath;
        }

        $brand->name = $request->input('name');
        $brand->save();

        return redirect()->route('brands.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();

        return redirect()->route('brands.index')->with('success', 'Brand deleted successfully.');
    }
    public function brandCount()
    {
        $count = Brand::count();
        return response()->json(['count' => $count]);
    }

    public function toggleDiscount(Request $request, $id)
    {
        try {
            // Validate request
            if ($request->input('apply_discount') && !$request->filled('discount_value')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Please enter a discount value'
                ], 400);
            }

            DB::beginTransaction();

            $brand = Brand::findOrFail($id);
            $applyDiscount = $request->input('apply_discount');
            $discountType = $request->input('discount_type', 'percentage');

            // Get existing discount value from first item of this brand
            $existingDiscount = Item::where('brand_id', $brand->id)
                ->where('discount_value', '>', 0)
                ->value('discount_value') ?? 0;

            $discountValue = $applyDiscount ?
                ($request->input('discount_value') ?: $existingDiscount) : 0;

            // Validate discount value based on type
            if ($applyDiscount) {
                if ($discountType === 'percentage' && ($discountValue <= 0 || $discountValue > 100)) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Percentage discount must be between 1 and 100'
                    ], 400);
                }
                if ($discountType === 'fixed' && $discountValue <= 0) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Fixed discount must be greater than 0'
                    ], 400);
                }
            }

            // Update all items belonging to this brand
            Item::where('brand_id', $brand->id)->update([
                'discount_type' => $discountType,
                'discount_value' => $discountValue
            ]);

            // Update brand's discount status
            $brand->has_discount = $applyDiscount;
            $brand->save();

            DB::commit();

            $discountDisplay = $discountType === 'percentage' ?
                $discountValue . '%' :
                'EGP ' . number_format($discountValue, 2);

            return response()->json([
                'success' => true,
                'message' => $applyDiscount ?
                    "Discount of {$discountDisplay} applied to all items in {$brand->name}" :
                    "Discount removed from all items in {$brand->name}"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Failed to toggle brand discount: ' . $e->getMessage()
            ], 500);
        }
    }

}
