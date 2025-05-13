<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProyectoRequest;
use App\Http\Requests\UpdateProyectoRequest;

class ProyectoController extends Controller
{
    public function index()
    {
        return Proyecto::all();
    }

    public function store(StoreProyectoRequest $request)
    {
        $proyecto = Proyecto::create($request->validated());
        return response()->json($proyecto, 201);
    }

    public function show(Proyecto $proyecto)
    {
        return $proyecto;
    }

    public function update(UpdateProyectoRequest $request, Proyecto $proyecto)
    {
        $proyecto->update($request->validated());
        return response()->json($proyecto);
    }

    public function destroy(Proyecto $proyecto)
    {
        $proyecto->delete();
        return response()->json(null, 204);
    }
}

