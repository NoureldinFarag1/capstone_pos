<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index()
    {
        $sizes = Size::all();
        return view('sizes.index', compact('sizes'));
    }

    public function create()
    {
        return view('sizes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required|in:clothes,shoes',
        ]);

        Size::create($request->all());

        return redirect()->route('sizes.index')->with('success', 'Size created successfully.');
    }

    public function edit($id)
    {
        $size = Size::findOrFail($id);
        return view('sizes.edit', compact('size'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required|in:clothes,shoes',
        ]);

        $size = Size::findOrFail($id);
        $size->update($request->all());

        return redirect()->route('sizes.index')->with('success', 'Size updated successfully.');
    }

    public function destroy($id)
    {
        $size = Size::findOrFail($id);
        $size->delete();

        return redirect()->route('sizes.index')->with('success', 'Size deleted successfully.');
    }
}
