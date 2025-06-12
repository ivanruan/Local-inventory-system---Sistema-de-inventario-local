<?php

namespace App\Http\Controllers;

use App\Models\AlertaStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class AlertaStockController extends Controller
{
    /**
     * Muestra una lista de alertas de stock.
     *
     * @return \Illuminate\View\View
     */
    public function index(): \Illuminate\View\View
    {
        // CAMBIO CLAVE AQUÍ: Usamos paginate() para obtener una colección paginada.
        $alertas = AlertaStock::with('producto')
                              ->orderBy('fecha_generacion', 'desc')
                              ->paginate(10); // Paginar 10 alertas por página
        return view('alertas.index', compact('alertas'));
    }

    /**
     * Muestra los detalles de una alerta de stock específica.
     *
     * @param  \App\Models\AlertaStock  $alertaStock
     * @return \Illuminate\View\View
     */
    public function show(AlertaStock $alertaStock): \Illuminate\View\View
    {
        $alertaStock->load('producto');
        return view('alertas.show', compact('alertaStock'));
    }

    /**
     * Marca una alerta de stock como resuelta.
     *
     * @param  \App\Models\AlertaStock  $alertaStock
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resolver(AlertaStock $alertaStock): \Illuminate\Http\RedirectResponse
    {
        try {
            $alertaStock->marcarResuelta();

            return redirect()->route('alertas.index') // Usamos 'alertas.index'
                             ->with('success', "Alerta #{$alertaStock->id} marcada como resuelta exitosamente.");
        } catch (Throwable $e) {
            Log::error("Error al resolver alerta #{$alertaStock->id}: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'No se pudo marcar la alerta como resuelta: ' . $e->getMessage());
        }
    }
}

