<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ubicacion;
use App\Http\Requests\StoreUbicacionRequest;
use App\Http\Requests\UpdateUbicacionRequest;


class UbicacionController extends Controller
{
    public function index()
    {
        return response()->json(Ubicacion::all());
    }

    public function store(StoreUbicacionRequest $request)
    {
        $ubicacion = Ubicacion::create($request->validated());

        return response()->json($ubicacion, 201);
    }

    public function show(Ubicacion $ubicacion)
    {
        return response()->json($ubicacion);
    }

    // app/Http/Controllers/UbicacionController.php
public function update(UpdateUbicacionRequest $request, Ubicacion $ubicacion)
{
    try {
        $ubicacion->update($request->validated());
        return response()->json($ubicacion);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error al actualizar la ubicación',
            'error' => $e->getMessage()
        ], 500);
    }
} 

   public function destroy(Ubicacion $ubicacion)
{
    try {
        $ubicacion->delete();
        return response()->noContent();
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error al eliminar la ubicación',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
