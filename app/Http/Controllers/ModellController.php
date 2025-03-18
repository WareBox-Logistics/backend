<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modell;

class ModellController extends Controller
{
    public function index()
    {
        return response()->json(Modell::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'is_truck' => 'boolean',
            'is_trailer' => 'boolean',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
        ]);

        $modell = Modell::create($validated);
        return response()->json($modell, 201);
    }

    public function show($id)
    {
        return response()->json(Modell::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $modell = Modell::findOrFail($id);
        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'is_truck' => 'boolean',
            'is_trailer' => 'boolean',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
        ]);

        $modell->update($validated);
        return response()->json($modell);
    }

    public function destroy($id)
    {
        $modell = Modell::findOrFail($id);
        $modell->delete();
        return response()->json(['message' => 'Modell deleted successfully']);
    }
}
