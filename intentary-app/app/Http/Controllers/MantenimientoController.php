<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mantenimiento;
use App\Http\Requests\StoreMantenimientoRequest;
use App\Http\Requests\UpdateMantenimientoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MantenimientoController extends Controller
{
	
    use ValidatesRequests;

    public function index(): JsonResponse
    {
        return response()->json(Mantenimiento::with('producto')->latest()->get());
    }

    
    public function store(StoreMantenimientoRequest $request)
    {
        try {
            $mantenimiento = Mantenimiento::create($request->validated());
            return response()->json($mantenimiento, 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }

    public function show(Mantenimiento $mantenimiento): JsonResponse
    {
        return response()->json($mantenimiento->load('producto'));
    }

    public function update(UpdateMantenimientoRequest $request, Mantenimiento $mantenimiento): JsonResponse
    {
        $mantenimiento->update($request->validated());
        return response()->json($mantenimiento->fresh()->load('producto'));
    }

    public function destroy(Mantenimiento $mantenimiento): JsonResponse
    {
    try {
        DB::transaction(function () use ($mantenimiento) {
            $mantenimiento->delete();
        });
        
        // Cambia esto:
        return response()->json([], 204);
        // En lugar de:
        // return response()->noContent();
        
    } catch (\Exception $e) {
        Log::error("Error eliminando mantenimiento: " . $e->getMessage());
        return response()->json([
            'message' => 'No se pudo eliminar el mantenimiento',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
    return response()->noContent();
    }
}

