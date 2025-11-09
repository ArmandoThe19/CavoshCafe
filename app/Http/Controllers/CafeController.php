<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cafe;

class CafeController extends Controller
{
   // Obtener todos los cafés
    public function index()
    {
        return Cafe::all();
    }

    // Crear un nuevo café
    public function store(Request $request)
    {
        $cafe = Cafe::create($request->only('price'));
        return response()->json($cafe, 201);
    }

    // Mostrar un café por ID
    public function show($id)
    {
        return Cafe::findOrFail($id);
    }

    // Actualizar un café
    public function update(Request $request, $id)
    {
        $cafe = Cafe::findOrFail($id);
        $cafe->update($request->only('price'));
        return response()->json($cafe, 200);
    }

    // Eliminar un café
    public function destroy($id)
    {
        Cafe::destroy($id);
        return response()->json(null, 204);
    }
    
}
