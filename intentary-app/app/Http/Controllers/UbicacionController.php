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
public function update(UpdateUbicacionRequest $request, $id)
{
    // Buscar manualmente para mejor diagnóstico
    $ubicacion = Ubicacion::find($id);
    
    if (!$ubicacion) {
        return response()->json([
            'error' => 'Ubicación no encontrada',
            'received_id' => $id,
            'existing_ids' => Ubicacion::all()->pluck('id')
        ], 404);
    }
    
    $ubicacion->update($request->validated());
    
    return response()->json([
        'success' => true,
        'data' => $ubicacion->fresh()
    ], 200);
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
