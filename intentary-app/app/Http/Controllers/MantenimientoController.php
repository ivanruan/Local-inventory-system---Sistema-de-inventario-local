<?php

namespace App\Http\Controllers;

use App\Models\Mantenimiento;
use App\Http\Requests\StoreMantenimientoRequest;
use App\Http\Requests\UpdateMantenimientoRequest;
use Illuminate\Http\JsonResponse;

class MantenimientoController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Mantenimiento::with('producto')->latest()->get());
    }

    public function store(StoreMantenimientoRequest $request): JsonResponse
    {
        $mantenimiento = Mantenimiento::create($request->validated());
        return response()->json($mantenimiento->load('producto'), 201);
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
        $mantenimiento->delete();
        return response()->json(['message' => 'Mantenimiento eliminado con éxito.']);
    }
}

