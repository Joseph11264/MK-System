<?php

namespace App\Http\Controllers;

use App\Models\Familia;
use Illuminate\Http\Request;

class FamiliaController
{
    public function index()
    {
        $familias = Familia::orderBy('nombre', 'asc')->get();
        return view('familias.index', compact('familias'));
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|unique:familias,nombre|max:100']);
        Familia::create($request->only('nombre'));
        return back()->with('success', 'Familia registrada correctamente.');
    }

    public function destroy($id)
    {
        Familia::findOrFail($id)->delete();
        return back()->with('success', 'Familia eliminada.');
    }
}