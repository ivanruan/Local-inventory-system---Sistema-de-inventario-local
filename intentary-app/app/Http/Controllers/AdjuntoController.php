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
        return Adjunto::all();
    }

    public function store(StoreAdjuntoRequest $request)
    {
        $file = $request->file('archivo');
        $nombreGuardado = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $ruta = $file->storeAs('adjuntos', $nombreGuardado, 'public');

        $adjunto = Adjunto::create([
            'tipo' => $request->tipo,
            'relacionado_id' => $request->relacionado_id,
            'nombre_original' => $file->getClientOriginalName(),
            'nombre_guardado' => $nombreGuardado,
            'extension' => $file->getClientOriginalExtension(),
            'tamanio_kb' => round($file->getSize() / 1024, 2),
            'url' => Storage::url($ruta),
        ]);

        return response()->json($adjunto, 201);
    }

    public function show(Adjunto $adjunto)
    {
        return $adjunto;
    }

    public function destroy(Adjunto $adjunto)
    {
        Storage::disk('public')->delete('adjuntos/' . $adjunto->nombre_guardado);
        $adjunto->delete();

        return response()->json(null, 204);
    }
}

