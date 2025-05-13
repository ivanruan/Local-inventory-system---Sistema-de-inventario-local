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

    public function update(UpdateUbicacionRequest $request, Ubicacion $ubicacion)
    {
        $ubicacion->update($request->validated());

        return response()->json($ubicacion);
    }

    public function destroy(Ubicacion $ubicacion)
    {
        $ubicacion->delete();

        return response()->json(null, 204);
    }
}
