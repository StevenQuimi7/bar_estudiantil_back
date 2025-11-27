<?php
namespace App\Services\productos;

use App\Models\categoria\Categoria;
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
        try{
            $per_page = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $productos = Producto::when(!empty($request->id_categoria),function($subquery) use($request){
                return $subquery->where("id_categoria",$request->id_categoria);
            })
            ->when(!empty($request->nombre), function($query) use($request) {
                $query->where("nombre","ILIKE","%".$request->nombre."%");
            })
            ->with(['categoria:id,nombre'])

            ->with(["user"=>function($query){
                return $query->select("id","username");
            }])
            ->orderBy('nombre','asc')
            ->activo()
            ->paginate($per_page, ['*'], 'page', $page);

            $response->setData($productos);
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }
    public function store($request){
        $response = new Response();
        try{
            $producto = Producto::create([
                "id_categoria"  => $request->id_categoria,
                "precio"        => $request->precio,
                "codigo"        => $request->codigo,
                "nombre"        => $request->nombre,
                "id_usuario_creacion" => getUsuarioAutenticado()->id
            ]);
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
            $producto = Producto::where("id",$id)->update([
                "id_categoria"  => $request->id_categoria,
                "precio"        => $request->precio,
                "codigo"        => $request->codigo,
                "nombre"        => $request->nombre,
                "id_usuario_creacion" => getUsuarioAutenticado()->id
            ]);
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

}


?>