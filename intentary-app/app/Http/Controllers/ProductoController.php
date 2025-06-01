<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Ubicacion;
use App\Models\Marca;
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

    public function __construct()
    {
        $this->middleware('auth'); // This will automatically redirect to login
    }


    /**
     * Formulario para crear un nuevo producto.
     */
    public function create()
    {
        $marcas = Marca::all();
        $categorias = Categoria::all();
        $ubicaciones = Ubicacion::all();

        return view('productos.create', compact('marcas', 'categorias', 'ubicaciones'));
    }


    /**
     * Almacena un nuevo producto en la base de datos.
     */
    public function store(StoreProductoRequest $request)
    {	
        $data = $request->validated();

        if ($data['marca_id'] === 'otra') {
            $marca = Marca::create(['nombre' => $data['nueva_marca']]);
            $data['marca_id'] = $marca->id;
        }

        if ($data['categoria_id'] === 'otra') {
            $categoria = Categoria::create(['nombre' => $data['nueva_categoria']]);
            $data['categoria_id'] = $categoria->id;
        }

        if ($data['ubicacion_id'] === 'otra') {
            $ubicacion = Ubicacion::create(['nombre' => $data['nueva_ubicacion']]);
            $data['ubicacion_id'] = $ubicacion->id;
    }

    // Eliminar los campos auxiliares
    unset($data['nueva_marca'], $data['nueva_categoria'], $data['nueva_ubicacion']);

	
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

