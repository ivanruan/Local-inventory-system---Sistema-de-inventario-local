<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Ubicacion;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Http\Requests\StoreProductoRequest; // Assuming you have this for create validation
use App\Http\Requests\UpdateProductoRequest; // We will define this for update validation
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // This will automatically redirect to login
    }

    /**
     * Muestra la lista de productos.
     */
    public function index(Request $request)
    {
        $query = Producto::query();

        // Aplicar filtros de búsqueda
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('codigo', 'like', '%' . $search . '%')
                  ->orWhere('nombre', 'like', '%' . $search . '%')
                  ->orWhere('especificacion', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->input('categoria'));
        }

        if ($request->filled('marca')) {
            $query->where('marca_id', $request->input('marca'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filtros de stock
        if ($request->filled('stock_filter')) {
            switch ($request->input('stock_filter')) {
                case 'bajo':
                    // Asume que tienes un campo o lógica para stock_minimo
                    $query->whereColumn('stock_actual', '<=', 'stock_minimo');
                    break;
                case 'fuera':
                    $query->where('stock_actual', 0);
                    break;
                case 'sobre':
                    // Asume que tienes un campo o lógica para stock_maximo
                    $query->whereColumn('stock_actual', '>=', 'stock_maximo');
                    break;
            }
        }

        // Ordenamiento
        $sortColumn = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortColumn, $sortDirection);

        $productos = $query->paginate(10); // O el número de elementos por página que uses

        // --- CÁLCULO DE ESTADÍSTICAS (Esto es lo que te faltaba o tenías incompleto) ---
        // Calcula estas variables ANTES de pasarlas a la vista
        $totalProductos = Producto::count();
        $stockBajo = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')
                              ->where('stock_actual', '>', 0) // Que no esté fuera de stock
                              ->count();
        $fueraStock = Producto::where('stock_actual', 0)->count();
        $valorTotal = Producto::sum(\DB::raw('stock_actual * valor_unitario')); // Asegúrate de tener stock_actual y costo
        $sobreStock = Producto::whereColumn('stock_actual', '>=', 'stock_maximo')->count();

        // Cargar datos para filtros (si no lo estás haciendo ya)
        $categorias = Categoria::all();
        $marcas = Marca::all();

        return view('productos.index', [
            'productos' => $productos,
            'categorias' => $categorias,
            'marcas' => $marcas,
            'totalProductos' => $totalProductos, // Pasando la variable
            'stockBajo' => $stockBajo, // Pasando la variable
            'fueraStock' => $fueraStock, // Pasando la variable
            'valorTotal' => $valorTotal, // Pasando la variable
            'sobreStock' => $sobreStock, // Pasando la variable
        ]);
    }

    /**
     * Formulario para crear un nuevo producto.
     */
    public function create()
    {
        $marcas = Marca::orderBy('nombre')->get();
        $categorias = Categoria::orderBy('nombre')->get();
        $ubicaciones = Ubicacion::orderBy('codigo')->get();
        $proveedores = Proveedor::orderBy('nombre')->get(); // Added proveedores for create as well

        return view('productos.create', compact('marcas', 'categorias', 'ubicaciones', 'proveedores'));
    }

    /**
     * Almacena un nuevo producto en la base de datos.
     */
    public function store(StoreProductoRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // The Producto model's 'creating' observer handles auto-generating 'codigo'
            // if it's not provided or is empty, and setting initial 'stock_actual'.
            $producto = Producto::create($data);

            DB::commit();

            return redirect()
                ->route('productos.index')
                ->with('success', 'Producto ' . $producto->codigo . ' creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Error al crear el producto: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Muestra los detalles de un producto específico.
     */
    public function show(Producto $producto)
    {
        // Load relationships if they are not eager loaded by default
        $producto->load(['marca', 'categoria', 'ubicacion', 'proveedor']);
        return view('productos.show', compact('producto'));
    }

    /**
     * Muestra el formulario para editar un producto existente.
     */
    public function edit(Producto $producto)
    {
        // Fetch related data for dropdowns
        $marcas = Marca::orderBy('nombre')->get();
        $categorias = Categoria::orderBy('nombre')->get();
        $ubicaciones = Ubicacion::orderBy('codigo')->get();
        $proveedores = Proveedor::orderBy('nombre')->get(); // Ensure suppliers are passed

        return view('productos.edit', compact(
            'producto',
            'categorias',
            'marcas',
            'ubicaciones',
            'proveedores'
        ));
    }

    /**
     * Actualiza el producto especificado en la base de datos.
     */
    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Get the original code before updating
            $originalCodigo = $producto->codigo;

            // Update the product. The 'updating' observer in Producto model
            // will handle recalculating stock_actual if stock_inicial, total_entradas,
            // or total_salidas change.
            // It also handles stock_minimo/maximo/seguridad validations.
            $producto->update($data);

            $mensaje = 'Producto actualizado correctamente.';

            // Check if the code was changed and if it conflicted with existing unique codes
            // Your existing logic for updating product code if it conflicts:
            // This part of the logic from your old 'update' method is specific.
            // If the code changed and now conflicts, you might want to re-generate it
            // or ensure the validation in UpdateProductoRequest handles uniqueness correctly.
            // My UpdateProductoRequest below uses a 'Rule::unique' to handle this at validation.
            // If you still want the auto-increment logic here after validation (e.g., if a user
            // tries to set a code that just became available), it's redundant if Rule::unique works.
            // Given Rule::unique, this block below might not be strictly necessary if your UX
            // wants an error on conflict, rather than auto-changing the code silently.
            // I'll leave it out for now, assuming validation handles conflicts.
            // If you want to keep the auto-increment on conflict post-validation, you'd re-add it here.

            DB::commit();

            return redirect()
                ->route('productos.show', $producto->id) // Redirect to show page after update
                ->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Error al actualizar el producto: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Elimina un producto.
     */
    public function destroy(Producto $producto)
    {
        try {
            $codigoProducto = $producto->codigo;
            $producto->delete();

            return redirect()
                ->route('productos.index')
                ->with('success', 'Producto ' . $codigoProducto . ' eliminado correctamente.');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al eliminar el producto: ' . $e->getMessage()]);
        }
    }
}