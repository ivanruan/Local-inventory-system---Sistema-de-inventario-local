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

class MovimientoInventarioController extends Controller
{
    /**
     * Muestra la lista paginada de movimientos.
     */
    public function index()
    {
        $movimientos = MovimientoInventario::with(['producto', 'proveedor', 'proyecto', 'usuario'])
            ->orderByDesc('fecha_hora')
            ->paginate(20);

        return view('movimientos.index', compact('movimientos'));
    }

    /**
     * Formulario para crear un nuevo movimiento.
     */
    public function create()
    {
        return view('movimientos.create', [
            'productos'  => Producto::all(),
            'proveedores'=> Proveedor::all(),
            'proyectos'  => Proyecto::all(),
            'usuarios'   => Usuario::all(),
        ]);
    }

    /**
     * Almacena un nuevo movimiento en la base de datos.
     */
    public function store(StoreMovimientoInventarioRequest $request)
    {
        MovimientoInventario::create($request->validated());

        return redirect()->route('movimientos.index')
                         ->with('success', 'Movimiento registrado correctamente.');
    }

    /**
     * Muestra el detalle de un movimiento.
     */
    public function show(MovimientoInventario $movimiento)
    {
        return view('movimientos.show', compact('movimiento'));
    }

    /**
     * Formulario para editar un movimiento existente.
     */
    public function edit(MovimientoInventario $movimiento)
    {
        return view('movimientos.edit', [
            'movimiento' => $movimiento,
            'productos'  => Producto::all(),
            'proveedores'=> Proveedor::all(),
            'proyectos'  => Proyecto::all(),
            'usuarios'   => Usuario::all(),
        ]);
    }

    /**
     * Actualiza un movimiento en la base de datos.
     */
    public function update(UpdateMovimientoInventarioRequest $request, MovimientoInventario $movimiento)
    {
        $movimiento->update($request->validated());

        return redirect()->route('movimientos.index')
                         ->with('success', 'Movimiento actualizado correctamente.');
    }

    /**
     * Elimina un movimiento.
     */
    public function destroy(MovimientoInventario $movimiento)
    {
        $movimiento->delete();

        return redirect()->route('movimientos.index')
                         ->with('success', 'Movimiento eliminado correctamente.');
    }
}

