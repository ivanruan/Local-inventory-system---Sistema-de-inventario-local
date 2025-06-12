<?php

namespace App\Http\Controllers;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Proyecto;
use App\Models\Usuario;
use App\Http\Requests\StoreMovimientoInventarioRequest;
use App\Http\Requests\UpdateMovimientoInventarioRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MovimientosExport;
use Barryvdh\DomPDF\Facade\Pdf;

class MovimientoInventarioController extends Controller
{
    /**
     * Muestra la lista paginada de movimientos con filtros y estadísticas.
     */
    public function index(Request $request)
    {
        // Obtener datos para los filtros PRIMERO
        $productos = Producto::orderBy('nombre')->get();
        
        // Construir la consulta base
        $query = MovimientoInventario::with(['producto', 'proveedor', 'proyecto', 'usuario']);

        // Aplicar filtros
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_hora', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_hora', '<=', $request->fecha_hasta);
        }

        // Aplicar ordenamiento
        $sortField = $request->get('sort', 'fecha_hora');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Verificar si es una exportación
        if ($request->has('export')) {
            return $this->exportData($request, $query);
        }

        // Paginar resultados
        $movimientos = $query->paginate(20);

        // Calcular estadísticas
        $estadisticas = $this->calcularEstadisticas();

        return view('movimientos.index', compact('movimientos', 'productos', 'estadisticas'));
    }

    /**
     * Calcula las estadísticas para el dashboard.
     */
    private function calcularEstadisticas()
    {
        $hoy = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        return [
            'entradas_hoy' => MovimientoInventario::where('tipo', 'entrada')
                                ->whereDate('fecha_hora', $hoy)
                                ->count(),
            
            'salidas_hoy' => MovimientoInventario::where('tipo', 'salida')
                               ->whereDate('fecha_hora', $hoy)
                               ->count(),
            
            'total_mes' => MovimientoInventario::whereBetween('fecha_hora', [$inicioMes, $finMes])
                             ->count(),
            
            'productos_afectados' => MovimientoInventario::whereBetween('fecha_hora', [$inicioMes, $finMes])
                                       ->distinct('producto_id')
                                       ->count('producto_id'),
        ];
    }

    /**
     * Maneja la exportación de datos.
     */
    private function exportData(Request $request, $query)
    {
        $movimientos = $query->get();
        
        if ($request->export === 'excel') {
            return Excel::download(new MovimientosExport($movimientos), 'movimientos_inventario.xlsx');
        }
        
        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('movimientos.pdf', compact('movimientos'));
            return $pdf->download('movimientos_inventario.pdf');
        }
    }

    /**
     * Formulario para crear un nuevo movimiento.
     */
    public function create()
    {
        return view('movimientos.create', [
            'productos'  => Producto::orderBy('nombre')->get(),
            'proveedores'=> Proveedor::orderBy('nombre')->get(),
            'proyectos'  => Proyecto::orderBy('nombre')->get(),
            'usuarios'   => Usuario::orderBy('nombre')->get(),
        ]);
    }

    /**
     * Almacena un nuevo movimiento en la base de datos.
     */
    public function store(StoreMovimientoInventarioRequest $request)
    {
        $data = $request->validated();

        if (!isset($data['fecha_hora'])) {
            $data['fecha_hora'] = now();
        }

        if ($data['tipo'] === 'salida' && isset($data['usuario_destino']) && !empty($data['usuario_destino'])) {
            $data['usuario_id'] = (int) $data['usuario_destino']; // Aseguramos que sea entero
        } else {
            $data['usuario_id'] = auth()->id();
        }

        unset($data['usuario_destino']);

        // --- Puntos de verificación ---
        \Log::info('Datos Finales para crear movimiento:', $data);
        // O para depuración en el navegador:
        // dd($data); // Esto detendrá la ejecución y mostrará el array $data
        // --- Fin puntos de verificación ---

        $movimiento = MovimientoInventario::create($data);

        $this->actualizarStock($movimiento);

        return redirect()->route('movimientos.index')
                         ->with('success', 'Movimiento registrado correctamente.');
    }

    /**
     * Muestra el detalle de un movimiento.
     */
    public function show(MovimientoInventario $movimiento)
    {
        $movimiento->load(['producto', 'proveedor', 'proyecto', 'usuario']);
        return view('movimientos.show', compact('movimiento'));
    }

    /**
     * Formulario para editar un movimiento existente.
     */
    public function edit(MovimientoInventario $movimiento)
    {
        return view('movimientos.edit', [
            'movimiento' => $movimiento,
            'productos'  => Producto::orderBy('nombre')->get(),
            'proveedores'=> Proveedor::orderBy('nombre')->get(),
            'proyectos'  => Proyecto::orderBy('nombre')->get(),
            'usuarios'   => Usuario::orderBy('nombre')->get(),
        ]);
    }

    /**
     * Actualiza un movimiento en la base de datos.
     */
    public function update(UpdateMovimientoInventarioRequest $request, MovimientoInventario $movimiento)
    {
        $datosOriginales = $movimiento->toArray();
        
        $data = $request->validated();
        
        // CORRECCIÓN: Mapear usuario_destino a usuario_id si existe
        if (isset($data['usuario_destino'])) {
            $data['usuario_id'] = $data['usuario_destino'];
            unset($data['usuario_destino']);
        }
        
        $movimiento->update($data);

        // Si cambió el producto, tipo o cantidad, actualizar stocks
        if ($datosOriginales['producto_id'] != $movimiento->producto_id ||
            $datosOriginales['tipo'] != $movimiento->tipo ||
            $datosOriginales['cantidad'] != $movimiento->cantidad) {
            
            // Revertir el movimiento original
            $this->revertirStock($datosOriginales);
            
            // Aplicar el nuevo movimiento
            $this->actualizarStock($movimiento);
        }

        return redirect()->route('movimientos.index')
                         ->with('success', 'Movimiento actualizado correctamente.');
    }

    /**
     * Elimina un movimiento.
     */
    public function destroy(MovimientoInventario $movimiento)
    {
        // Revertir el efecto en el stock antes de eliminar
        $this->revertirStock($movimiento->toArray());
        
        $movimiento->delete();

        return redirect()->route('movimientos.index')
                         ->with('success', 'Movimiento eliminado correctamente.');
    }

    /**
     * Actualiza el stock del producto basado en el movimiento.
     */
    private function actualizarStock(MovimientoInventario $movimiento)
    {
        $producto = Producto::find($movimiento->producto_id);

        if ($producto) {
            if ($movimiento->tipo === 'entrada') {
                // Cambiar 'stock' a 'stock_actual'
                $producto->increment('stock_actual', $movimiento->cantidad);
            } else {
                // Cambiar 'stock' a 'stock_actual'
                $producto->decrement('stock_actual', $movimiento->cantidad);
            }
        }
    }

    /**
     * Revierte el efecto de un movimiento en el stock.
     */
    private function revertirStock(array $datosMovimiento)
    {
        $producto = Producto::find($datosMovimiento['producto_id']);

        if ($producto) {
            if ($datosMovimiento['tipo'] === 'entrada') {
                // Cambiar 'stock' a 'stock_actual'
                $producto->decrement('stock_actual', $datosMovimiento['cantidad']);
            } else {
                // Cambiar 'stock' a 'stock_actual'
                $producto->increment('stock_actual', $datosMovimiento['cantidad']);
            }
        }
    }
}
