<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMarcaRequest;
use App\Http\Requests\UpdateMarcaRequest;

class MarcaController extends Controller
{
    public function index()
    {
        return response()->json(Marca::all());
    }

    public function store(StoreMarcaRequest $request)
    {
        $marca = Marca::create($request->validated());

        return response()->json($marca, 201);
    }

    public function show(Marca $marca)
    {
        return response()->json($marca);
    }

    public function update(UpdateMarcaRequest $request, Marca $marca)
    {
        $marca->update($request->validated());

        return response()->json($marca);
    }

    public function destroy(Marca $marca)
    {
        $marca->delete();

        return response()->json(['message' => 'Marca eliminada con éxito.']);
    }
}

