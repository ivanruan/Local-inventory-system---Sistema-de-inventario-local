<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Muestra la lista de productos.
     */
    public function index()
    {
        $productos = Producto::with(['marca', 'categoria', 'ubicacion'])->paginate(15);
        return view('productos.index', compact('productos'));
    }

    /**
     * Formulario para crear un nuevo producto.
     */
    public function create()
    {
        // Asume que pasas las colecciones de marcas, categorías y ubicaciones
        return view('productos.create', [
            'marcas'      => \App\Models\Marca::all(),
            'categorias'  => \App\Models\Categoria::all(),
            'ubicaciones' => \App\Models\Ubicacion::all(),
        ]);
    }

    /**
     * Almacena un nuevo producto en la base de datos.
     */
    public function store(StoreProductoRequest $request)
    {
        $data = $request->validated();
        Producto::create($data);
        return redirect()->route('productos.index')
                         ->with('success', 'Producto creado correctamente.');
    }

    /**
     * Muestra un producto en detalle.
     */
    public function show(Producto $producto)
    {
        return view('productos.show', compact('producto'));
    }

    /**
     * Formulario para editar un producto existente.
     */
    public function edit(Producto $producto)
    {
        return view('productos.edit', [
            'producto'    => $producto,
            'marcas'      => \App\Models\Marca::all(),
            'categorias'  => \App\Models\Categoria::all(),
            'ubicaciones' => \App\Models\Ubicacion::all(),
        ]);
    }

    /**
     * Actualiza los datos de un producto.
     */
    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        $data = $request->validated();
        $producto->update($data);
        return redirect()->route('productos.index')
                         ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Elimina un producto.
     */
    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index')
                         ->with('success', 'Producto eliminado correctamente.');
    }
}

