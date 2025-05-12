<?php

namespace App\Http\Controllers;

use App\Models\AlertaStock;
use App\Models\Producto;
use App\Http\Requests\StoreAlertaStockRequest;
use App\Http\Requests\UpdateAlertaStockRequest;
use Illuminate\Http\Request;

class AlertaStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $alertas = AlertaStock::with('producto')
            ->orderByDesc('fecha_generacion')
            ->paginate(20);

        return view('alertas.index', compact('alertas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('alertas.create', [
            'productos' => Producto::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAlertaStockRequest $request)
    {
        AlertaStock::create($request->validated());

        return redirect()->route('alertas.index')
                         ->with('success', 'Alerta creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AlertaStock $alerta)
    {
        return view('alertas.show', compact('alerta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AlertaStock $alerta)
    {
        return view('alertas.edit', [
            'alerta'    => $alerta,
            'productos' => Producto::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAlertaStockRequest $request, AlertaStock $alerta)
    {
        $alerta->update($request->validated());

        return redirect()->route('alertas.index')
                         ->with('success', 'Alerta actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AlertaStock $alerta)
    {
        $alerta->delete();

        return redirect()->route('alertas.index')
                         ->with('success', 'Alerta eliminada correctamente.');
    }
}
