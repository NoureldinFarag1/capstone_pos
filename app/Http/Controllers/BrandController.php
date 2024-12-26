<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::all();
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

}
