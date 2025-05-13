<?php

namespace App\Http\Controllers;

use App\Models\Adjunto;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAdjuntoRequest;
use App\Http\Requests\UpdateAdjuntoRequest;
use Illuminate\Support\Facades\Storage;

class AdjuntoController extends Controller
{
	public function index()
    {
        return response()->json(Adjunto::with('movimiento')->get());
    }

    public function store(StoreAdjuntoRequest $request)
    {
        $adjunto = Adjunto::create($request->validated());

        return response()->json($adjunto, 201);
    }

    public function show(Adjunto $adjunto)
    {
        return response()->json($adjunto->load('movimiento'));
    }

    public function update(UpdateAdjuntoRequest $request, Adjunto $adjunto)
    {
        $adjunto->update($request->validated());

        return response()->json($adjunto);
    }

    public function destroy(Adjunto $adjunto)
    {
        $adjunto->delete();

        return response()->json(null, 204);
    }
}

