<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;

class ProveedorController extends Controller
{
    public function index()
    {
        return response()->json(Proveedor::all());
    }

    public function store(StoreProveedorRequest $request)
    {
        $proveedor = Proveedor::create($request->validated());

        return response()->json([
            'message' => 'Proveedor creado exitosamente',
            'data' => $proveedor
        ], 201);
    }

    public function show(Proveedor $proveedor)
    {
        return response()->json($proveedor);
    }

    public function update(UpdateProveedorRequest $request, Proveedor $proveedor)
    {
        $proveedor->update($request->validated());

        return response()->json([
            'message' => 'Proveedor actualizado exitosamente',
            'data' => $proveedor
        ]);
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();

        return response()->json([
            'message' => 'Proveedor eliminado exitosamente'
        ]);
    }
}

