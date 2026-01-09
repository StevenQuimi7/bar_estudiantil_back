<?php
namespace App\Services\productos;

use App\Models\categoria\Categoria;
use App\Models\Image;
use App\Models\producto\Producto;
use App\Utils\Response;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response AS ResponseHttp;

class ProductoService
{
    public function __construct(){}

    public function index($request){
        $response = new Response();
        try {
            $imagenes = Image::all();
            $per_page = $request->input('per_page', 10);
            $page     = $request->input('page', 1);
            
            $query = Producto::query();

            $query->when(!empty($request->id_categoria), function($subquery) use($request){
                return $subquery->where("id_categoria", $request->id_categoria);
            })
            ->when(!empty($request->nombre), function($q) use($request) {
                $q->where("nombre", "like", "%".$request->nombre."%");
            })
            ->with([
                'categoria:id,nombre',
                "user" => function($q){
                    return $q->select("id", "username");
                },
                "image" => function($q){
                    return $q->activo();
                }
            ])
            ->orderBy('nombre', 'asc')
            ->activo();

            if ($request->has('download') && ($request->download == true || $request->download == 'true')) {
                $resultado = $query->get()->map(function ($item) {
                    return $this->transformProducto($item);
                });
            } else {
                $resultado = $query->paginate($per_page, ['*'], 'page', $page);
            }

            $response->setData($resultado);
            $response->setCode(200);

        } catch(Exception $e) {
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e->getMessage());
            $response->setOk(false);
            $response->setCode(500);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }
    public function store($request){
        $response = new Response();
        try{
            $id_usuario = getUsuarioAutenticado()->id;

            $producto = Producto::create([
                "id_categoria"        => $request->id_categoria,
                "precio"              => $request->precio,
                "codigo"              => $request->codigo,
                "nombre"              => $request->nombre,
                "id_usuario_creacion" => $id_usuario
            ]);

            if($request->filled('imagen')) {

                //subirmos la imagen al directorio public
                $ruta = $this->moveImagePublic($request->imagen);

                $producto->image()->create([
                    'path'                => $ruta,
                    'extension'           => pathinfo($request->name, PATHINFO_EXTENSION),
                    'id_usuario_creacion' => $id_usuario
                ]);
            }

            $response->setData($producto);
            $response->setCode(201);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }
    public function update($id,$request){
        $response = new Response();
        try{

            $id_usuario = getUsuarioAutenticado()->id;

            $producto = Producto::findOrFail($id);
            $producto->update([
                "id_categoria"        => $request->id_categoria,
                "precio"              => $request->precio,
                "codigo"              => $request->codigo,
                "nombre"              => $request->nombre,
                "id_usuario_creacion" => $id_usuario
            ]);

            if($request->filled('imagen')) {

                //subirmos la imagen al directorio public
                $ruta = $this->moveImagePublic($request->imagen);
                $product = Producto::find($id);
                $imageProducto = Image::where('imageable_id', $id)->where('imageable_type', Producto::class)->update([
                    'activo' => 0]);
                $product->image()->create([
                    'path'                => $ruta,
                    'extension'           => pathinfo($request->name, PATHINFO_EXTENSION),
                    'id_usuario_creacion' => $id_usuario
                ]);
            }

            $response->setData($producto);
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }

    public function moveImagePublic($ruta_file)
    {
        $rutaArchivo = storage_path('app/private/' . $ruta_file['path']);
        $ruta = "";
        try {
            // Verificar si el archivo existe en el servidor
            if (file_exists($rutaArchivo)) {
                // Obtener el contenido del archivo
                $archivo = file_get_contents($rutaArchivo);
                if ($archivo === false) {
                    throw new Exception("No se pudo leer el archivo en el servidor");
                }
                // Crear un nombre único para el archivo a mover
                $nombre = basename($rutaArchivo);
                //$nombre = time() . '_' . basename($rutaArchivo);
                $rutaDestino = public_path('images/productos');
                 
                // Verificar si la carpeta de destino existe, si no, crearla
                if (!file_exists($rutaDestino)) {
                    mkdir($rutaDestino, 0777, true);  // 0777 son permisos completos, y true es para crear directorios anidados
                }
                $ruta = $nombre;
                $nombre_archivo = $rutaDestino . '/' . $nombre;
                // Mover el archivo a la carpeta de destino
                if (!rename($rutaArchivo, $nombre_archivo)) {
                    throw new Exception("Error al mover el archivo a la carpeta de destino");
                }
            } else {
                throw new Exception("No existe el archivo en la carpeta temporal");
            }
        } catch (Exception $e) {
            log::error("error moveImagePublic: " . $e->getMessage());
        }
        return $ruta;
    }

    public function delete($id){
        $response = new Response();
        try{
            $productoModel = Producto::with('detalles_venta')
                ->findOrFail($id);

            // Verificar si el producto está en detalles_venta
            $tieneDetallesVenta = $productoModel->detalles_venta()->exists();

            if ($tieneDetallesVenta) {
                throw new Exception('No se puede eliminar el producto porque está asociado a detalles de venta.');
            }

            $productoModel->update([
                "activo"              => 0,
                "id_usuario_creacion" => getUsuarioAutenticado()->id
            ]);
            $response->setData($productoModel);
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }
    public function cargaMasiva($data){
        $response = new Response();
        try{
            $filasExcel = $this->setFilasExcel($data);

            //obtener todos las categorias del excel
            $categorias = $filasExcel->pluck('categoria')->filter()->unique()->toArray();
            
            //obtener las categorias de base con la info del excel
            $categoriasExistentes = $this->getCategoriasPorNombre($categorias);
            
            $productosToUpsert         = [];
            //iterar info de excel y setear data a guardar o actualizar

            foreach ($filasExcel as $fila) {
                $categoria     = $fila['categoria'];
                $categoria     = $categoriasExistentes->get($categoria);
                if(!$categoria) throw new Exception("Error en la fila {$fila['fila_num']}, no existe la categoría {$fila['categoria']}");
                
                $data = [
                    'id_categoria'        => $categoria->id,
                    'codigo'              => mb_strtoupper(trim($fila['codigo']), 'UTF-8'),
                    'nombre'              => mb_strtoupper(trim($fila['nombre']), 'UTF-8'),
                    'precio'              => $fila['precio'],
                    'id_usuario_creacion' => getUsuarioAutenticado()->id,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ];

                $productosToUpsert[] = $data;

            }

            if (!empty($productosToUpsert)) {
                Producto::upsert($productosToUpsert, ['codigo'], ['nombre','precio','id_usuario_creacion', 'updated_at']);
            }
            $response->setData("Productos registrados exitosamente");
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }
    public function setFilasExcel($data)
    {
        $columnas = ['categoria', 'codigo', 'nombre', 'precio'];

        // 1. Obtenemos las cabeceras, limpiamos espacios y pasamos a minúsculas para evitar errores humanos
        $cabecerasExcel = collect($data[0])
            ->slice(0, count($columnas))
            ->map(fn($item) => strtolower(trim($item)))
            ->toArray();

        // 2. Comparamos directamente (el operador === en arrays compara llaves, valores y ORDEN)
        if ($columnas !== $cabecerasExcel) {
            throw new Exception(
                "El formato del archivo no es válido. " . 
                "Se esperaba el orden: " . implode(', ', $columnas) . ". " .
                "Se recibió: " . implode(', ', $cabecerasExcel)
            );
        }

        $filas = collect($data)
            ->slice(1) // saltar encabezado
            ->map(function ($fila, $key) use ($columnas) {

                foreach ($columnas as $index => $field) {
                    if (empty($fila[$index])) {
                        throw new Exception("Error: campo {$field} vacío en fila " . ($key + 1));
                    }
                }

                return [
                    'categoria'     => mb_strtoupper(trim($fila[0] ?? ''),'UTF-8'),
                    'codigo'        => mb_strtoupper(trim($fila[1] ?? ''), 'UTF-8'),
                    'nombre'        => mb_strtoupper(trim($fila[2] ?? ''), 'UTF-8'),
                    'precio'        => (float) $fila[3],
                    'fila_num'      => $key + 1,
                ];
            });

        // Verificar si hay codigos repetidos
        $repetidos = $filas->pluck('codigo')->duplicates();
        $nombres = $filas->pluck('nombre')->duplicates();

        if ($repetidos->isNotEmpty()) {
            throw new Exception("Error: código de productos repetidos: " . $repetidos->implode(', '));
        }
        if ($nombres->isNotEmpty()) {
            throw new Exception("Error: nombres de productos repetidos: " . $nombres->implode(', '));
        }

        return $filas;
    }
    public function getCategoriasPorNombre(array $categorias)
    {
        return Categoria::select(
                'id',
                'nombre',
            )
            ->whereIn('nombre',$categorias)
            ->get()
            ->keyBy('nombre');
    }
    public function transformProducto($producto){
        return [
            "categoria"      => $producto->categoria->nombre,
            "codigo"         => $producto->codigo,
            "nombre"         => $producto->nombre,
            "precio"         => $producto->precio,
            "fecha_creacion" => ($producto->created_at)->format('Y-m-d'),
        ];
    }

}


?>