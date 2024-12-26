<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::all();
        return view('colors.index', compact('colors'));
    }

    public function create()
    {
        return view('colors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hex_code' => 'nullable|string|max:7',
        ]);

        Color::create($validated);
        return redirect()->route('colors.index')->with('success', 'Color added successfully');
    }

    public function edit(Color $color)
    {
        return view('colors.edit', compact('color'));
    }

    public function update(Request $request, Color $color)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hex_code' => 'nullable|string|max:7',
        ]);

        $color->update($validated);
        return redirect()->route('colors.index')->with('success', 'Color updated successfully');
    }

    public function destroy(Color $color)
    {
        $color->delete();
        return redirect()->route('colors.index')->with('success', 'Color deleted successfully');
    }
}
