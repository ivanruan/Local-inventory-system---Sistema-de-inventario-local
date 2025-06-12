<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Ubicacion;
use App\Models\Marca;
use App\Models\Producto;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use Illuminate\Http\Request;
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
        $marcas = Marca::orderBy('nombre')->get();
        $categorias = Categoria::orderBy('nombre')->get();
        $ubicaciones = Ubicacion::orderBy('codigo')->get();

        return view('productos.create', compact('marcas', 'categorias', 'ubicaciones'));
    }

    /**
     * Almacena un nuevo producto en la base de datos.
     */
    public function store(StoreProductoRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();

            // Manejar creación de nueva marca si es necesario
            if (isset($data['marca_id']) && $data['marca_id'] === 'nueva') {
                if (!empty($data['nueva_marca'])) {
                    $marca = Marca::create([
                        'nombre' => $data['nueva_marca'],
                        'activo' => true
                    ]);
                    $data['marca_id'] = $marca->id;
                }
            }

            // Manejar creación de nueva categoría si es necesario
            if (isset($data['categoria_id']) && $data['categoria_id'] === 'nueva') {
                if (!empty($data['nueva_categoria'])) {
                    $categoria = Categoria::create([
                        'nombre' => $data['nueva_categoria'],
                        'activo' => true
                    ]);
                    $data['categoria_id'] = $categoria->id;
                }
            }

            // Manejar creación de nueva ubicación si es necesario
            if (isset($data['ubicacion_id']) && $data['ubicacion_id'] === 'nueva') {
                if (!empty($data['nueva_ubicacion'])) {
                    // Parsear el formato "A1 - 2" para obtener código y nivel
                    $ubicacionData = $this->parseNuevaUbicacion($data['nueva_ubicacion']);
                    $ubicacion = Ubicacion::create([
                        'codigo' => $ubicacionData['codigo'],
                        'nivel' => $ubicacionData['nivel'],
                        'activo' => true
                    ]);
                    $data['ubicacion_id'] = $ubicacion->id;
                }
            }

            // Eliminar los campos auxiliares
            unset($data['nueva_marca'], $data['nueva_categoria'], $data['nueva_ubicacion']);

            // Crear el producto con código temporal
            $producto = new Producto();
            $producto->fill($data);
            $producto->codigo = 'TEMP_' . time() . '_' . uniqid(); // Código temporal único
            $producto->save();

            // Generar el código final con el ID
            $codigoFinal = $this->generarCodigoCompleto($producto);
            
            // Verificar que el código no exista (aunque es muy improbable)
            $contador = 1;
            $codigoOriginal = $codigoFinal;
            while (Producto::where('codigo', $codigoFinal)->where('id', '!=', $producto->id)->exists()) {
                $codigoFinal = $codigoOriginal . '_' . $contador;
                $contador++;
            }
            
            // Actualizar el producto con el código final
            $producto->codigo = $codigoFinal;
            $producto->save();

            DB::commit();

            return redirect()
                ->route('productos.index')
                ->with('success', 'Producto creado exitosamente. Código asignado: ' . $codigoFinal);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withErrors(['error' => 'Error al crear el producto: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Genera el código completo del producto basado en sus datos.
     */
    private function generarCodigoCompleto(Producto $producto)
    {
        // Cargar las relaciones necesarias
        $producto->load(['categoria', 'ubicacion']);
        
        // Obtener código de categoría (usar campo codigo si existe, sino primeras 3 letras del nombre)
        $categoriaCodigo = $producto->categoria->codigo ?? 
                          strtoupper(substr($producto->categoria->nombre, 0, 3));
        
        // Limpiar y obtener primeras 3 letras del nombre del producto
        $nombreLimpio = preg_replace('/[^A-Z0-9]/i', '', $producto->nombre);
        $nombreCodigo = strtoupper(substr($nombreLimpio, 0, 3));
        
        // Limpiar y obtener primeras 4 letras de la especificación
        $especificacionLimpio = preg_replace('/[^A-Z0-9]/i', '', $producto->especificacion ?? '');
        $especificacionCodigo = strtoupper(substr($especificacionLimpio, 0, 4));
        
        // Obtener código de ubicación + nivel
        $ubicacionCodigo = strtoupper($producto->ubicacion->codigo . $producto->ubicacion->nivel);
        
        // Construir el código final
        $codigoCompleto = $categoriaCodigo . '-' . 
                         $nombreCodigo . 
                         $especificacionCodigo . '-' . 
                         $ubicacionCodigo . '-' . 
                         $producto->id;
        
        return $codigoCompleto;
    }

    /**
     * Parsea el texto de nueva ubicación en formato "A1 - 2"
     */
    private function parseNuevaUbicacion($ubicacionTexto)
    {
        // Formato esperado: "A1 - 2" donde A1 es código y 2 es nivel
        $partes = explode(' - ', trim($ubicacionTexto));
        
        return [
            'codigo' => isset($partes[0]) ? trim($partes[0]) : 'XX',
            'nivel' => isset($partes[1]) ? (int)trim($partes[1]) : 1
        ];
    }

    /**
     * Método auxiliar para preview del código (opcional - para AJAX)
     */
    public function previewCodigo(Request $request)
    {
        try {
            $categoria = Categoria::find($request->categoria_id);
            $ubicacion = Ubicacion::find($request->ubicacion_id);
            
            if (!$categoria || !$ubicacion) {
                return response()->json(['codigo' => '']);
            }
            
            $categoriaCodigo = $categoria->codigo ?? strtoupper(substr($categoria->nombre, 0, 3));
            $nombreLimpio = preg_replace('/[^A-Z0-9]/i', '', $request->nombre ?? '');
            $nombreCodigo = strtoupper(substr($nombreLimpio, 0, 3));
            $especificacionLimpio = preg_replace('/[^A-Z0-9]/i', '', $request->especificacion ?? '');
            $especificacionCodigo = strtoupper(substr($especificacionLimpio, 0, 4));
            $ubicacionCodigo = strtoupper($ubicacion->codigo . $ubicacion->nivel);
            
            $codigoPreview = $categoriaCodigo . '-' . 
                            $nombreCodigo . 
                            $especificacionCodigo . '-' . 
                            $ubicacionCodigo . '-[ID]';
            
            return response()->json(['codigo' => $codigoPreview]);
            
        } catch (\Exception $e) {
            return response()->json(['codigo' => '']);
        }
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
            'marcas'      => Marca::where('activo', true)->orderBy('nombre')->get(),
            'categorias'  => Categoria::where('activo', true)->orderBy('nombre')->get(),
            'ubicaciones' => Ubicacion::where('activo', true)->orderBy('codigo')->get(),
        ]);
    }

    /**
     * Actualiza los datos de un producto.
     */
    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();
            
            // Guardar el código anterior para comparación
            $codigoAnterior = $producto->codigo;
            
            // Actualizar los datos del producto
            $producto->update($data);
            
            // Regenerar código si cambió alguno de los campos que lo componen
            $camposImportantes = ['nombre', 'especificacion', 'categoria_id', 'ubicacion_id'];
            $regenerarCodigo = false;
            
            foreach ($camposImportantes as $campo) {
                if (array_key_exists($campo, $data) && $producto->getOriginal($campo) != $data[$campo]) {
                    $regenerarCodigo = true;
                    break;
                }
            }
            
            if ($regenerarCodigo) {
                $codigoNuevo = $this->generarCodigoCompleto($producto);
                
                // Verificar que el nuevo código no exista
                $contador = 1;
                $codigoOriginal = $codigoNuevo;
                while (Producto::where('codigo', $codigoNuevo)->where('id', '!=', $producto->id)->exists()) {
                    $codigoNuevo = $codigoOriginal . '_' . $contador;
                    $contador++;
                }
                
                $producto->codigo = $codigoNuevo;
                $producto->save();
                
                $mensaje = 'Producto actualizado correctamente. Código actualizado: ' . $codigoNuevo;
            } else {
                $mensaje = 'Producto actualizado correctamente.';
            }
            
            DB::commit();
            
            return redirect()
                ->route('productos.index')
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