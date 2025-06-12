<?php

namespace App\Http\Controllers;

use App\Models\Mantenimiento;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMantenimientoRequest;
use App\Http\Requests\UpdateMantenimientoRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class MantenimientoController extends Controller
{
    use ValidatesRequests;

    /**
     * Muestra una lista de los mantenimientos.
     *
     * @return \Illuminate\View\View
     */
    public function index(): \Illuminate\View\View
    {
        // CAMBIO CLAVE: Usa paginate() en lugar de get() para que $mantenimientos sea una instancia paginada.
        // Carga los mantenimientos, incluyendo la relación con 'producto'
        // Ordena por la fecha programada más reciente primero y los pagina (ej. 10 por página).
        $mantenimientos = Mantenimiento::with('producto')
                                     ->orderBy('fecha_programada', 'desc')
                                     ->paginate(10); // Puedes ajustar el número de elementos por página aquí.
        return view('mantenimientos.index', compact('mantenimientos'));
    }

    /**
     * Muestra el formulario para crear un nuevo mantenimiento.
     *
     * @return \Illuminate\View\View
     */
    public function create(): \Illuminate\View\View
    {
        $productos = Producto::orderBy('nombre')->get();
        return view('mantenimientos.create', compact('productos'));
    }

    /**
     * Almacena un nuevo mantenimiento en la base de datos.
     *
     * @param  \App\Http\Requests\StoreMantenimientoRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreMantenimientoRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            Mantenimiento::create($request->validated());
            return redirect()->route('mantenimientos.index')
                             ->with('success', 'Mantenimiento creado exitosamente.');
        } catch (Throwable $e) {
            Log::error("Error al crear mantenimiento: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['error' => 'No se pudo crear el mantenimiento: ' . $e->getMessage()]);
        }
    }

    /**
     * Muestra el mantenimiento especificado.
     *
     * @param  \App\Models\Mantenimiento  $mantenimiento
     * @return \Illuminate\View\View
     */
    public function show(Mantenimiento $mantenimiento): \Illuminate\View\View
    {
        $mantenimiento->load('producto');
        return view('mantenimientos.show', compact('mantenimiento'));
    }

    /**
     * Muestra el formulario para editar el mantenimiento especificado.
     *
     * @param  \App\Models\Mantenimiento  $mantenimiento
     * @return \Illuminate\View\View
     */
    public function edit(Mantenimiento $mantenimiento): \Illuminate\View\View
    {
        $productos = Producto::orderBy('nombre')->get();
        return view('mantenimientos.edit', compact('mantenimiento', 'productos'));
    }

    /**
     * Actualiza el mantenimiento especificado en la base de datos.
     *
     * @param  \App\Http\Requests\UpdateMantenimientoRequest  $request
     * @param  \App\Models\Mantenimiento  $mantenimiento
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateMantenimientoRequest $request, Mantenimiento $mantenimiento): \Illuminate\Http\RedirectResponse
    {
        try {
            $mantenimiento->update($request->validated());
            return redirect()->route('mantenimientos.index')
                             ->with('success', 'Mantenimiento actualizado exitosamente.');
        } catch (Throwable $e) {
            Log::error("Error al actualizar mantenimiento: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['error' => 'No se pudo actualizar el mantenimiento: ' . $e->getMessage()]);
        }
    }

    /**
     * Elimina el mantenimiento especificado de la base de datos.
     *
     * @param  \App\Models\Mantenimiento  $mantenimiento
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Mantenimiento $mantenimiento): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::transaction(function () use ($mantenimiento) {
                $mantenimiento->delete();
            });
            return redirect()->route('mantenimientos.index')
                             ->with('success', 'Mantenimiento eliminado exitosamente.');
        } catch (Throwable $e) {
            Log::error("Error eliminando mantenimiento: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'No se pudo eliminar el mantenimiento: ' . $e->getMessage());
        }
    }
}
