<?php

namespace App\Http\Controllers;

use App\Models\Autorizacion;
use App\Http\Requests\StoreAutorizacionRequest;
use App\Http\Requests\UpdateAutorizacionRequest;

class AutorizacionController extends Controller
{
    public function index()
    {
        return Autorizacion::with('movimiento')->get();
    }

    public function store(StoreAutorizacionRequest $request)
    {
        $autorizacion = Autorizacion::create($request->validated());
        return response()->json($autorizacion, 201);
    }

    public function show(Autorizacion $autorizacion)
    {
        return $autorizacion->load('movimiento');
    }

    public function update(UpdateAutorizacionRequest $request, Autorizacion $autorizacion)
    {
        $autorizacion->update($request->validated());
        return response()->json($autorizacion);
    }

    public function destroy(Autorizacion $autorizacion)
    {
        $autorizacion->delete();
        return response()->json(null, 204);
    }
}

