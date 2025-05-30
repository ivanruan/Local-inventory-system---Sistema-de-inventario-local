<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;

class CategoriaController extends Controller
{
    public function index()
    {
        return Categoria::all();
    }

    public function store(StoreCategoriaRequest $request)
    {
        try {
  	      $categoria = Categoria::create($request->validated());
       		 return response()->json([
            	'id' => $categoria->id,
            	'nombre' => $categoria->nombre,
            	'message' => 'Marca creada exitosamente'
        	], 201);
    	} catch (\Exception $e) {
        	return response()->json([
            	'message' => 'Error al crear la marca',
            	'error' => $e->getMessage()
        	], 500);
    	}
    }

    public function show(Categoria $categoria)
    {
        return $categoria;
    }

    public function update(UpdateCategoriaRequest $request, Categoria $categoria)
    {
        $categoria->update($request->validated());

        return response()->json($categoria);
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();

        return response()->json(null, 204);
    }
}

