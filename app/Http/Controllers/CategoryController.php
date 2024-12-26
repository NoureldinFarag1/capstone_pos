<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function create()
    {
        $brands = Brand::all();
        return view('categories.create', compact('brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'brand_id' => 'required|exists:brands,id',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('picture')) {
            $picturePath = $request->file('picture')->store('categories', 'public');
        } else {
            $picturePath = null;
        }

        Category::create([
            'name' => $request->input('name'),
            'brand_id' => $request->input('brand_id'), // Save brand_id
            'picture' => $picturePath,
        ]);
        return redirect()->route('categories.index');
    }

    public function index()
    {
        $categories = Category::with('items')->get();
        return view('categories.index', compact('categories'));
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $brands = Brand::all(); // To show all brands for selection
        return view('categories.edit', compact('category', 'brands'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'brand_id' => 'required|exists:brands,id',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $category = Category::findOrFail($id);

        // If a new picture is uploaded, store it
        if ($request->hasFile('picture')) {
            $picturePath = $request->file('picture')->store('categories', 'public');
            $category->picture = $picturePath;
        }

        $category->name = $request->input('name');
        $category->brand_id = $request->input('brand_id');
        $category->save();

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }

    public function getCategoriesByBrand($brand_id)
    {
        $categories = Category::where('brand_id', $brand_id)->get();
        return response()->json($categories);
    }

    public function getCategories($brandId)
{
    $categories = Category::where('brand_id', $brandId)->get();

    return response()->json([
        'categories' => $categories
    ]);
}


}

