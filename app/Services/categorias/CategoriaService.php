<?php
namespace App\Services\categorias;

use App\Models\categoria\Categoria;
use App\Utils\Response;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response AS ResponseHttp;

class CategoriaService
{
    public function __construct(){}

    public function index($request){
        $response = new Response();
        try{
            $per_page = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $categorias = Categoria::activo()
            ->when($request->filled('nombre'), function ($query) use ($request) {
                return $query->where('nombre', 'like', '%' . $request->nombre . '%');
            })
            ->with(['user:id,username'])
            ->paginate($per_page, ['*'], 'page', $page);
            
            $response->setData($categorias);
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }
    public function comboCategorias(){
        $response = new Response();
        try{
            $categorias = Categoria::select("id as value","nombre as label")->activo()
            ->get();
            $response->setData($categorias);
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
            $categoria = Categoria::create([
                "nombre"              => $request->nombre,
                "id_usuario_creacion" => getUsuarioAutenticado()->id
            ]);
            $response->setData("Creado exitosamente");
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
            $categoria = Categoria::findOrFail($id);
            $categoria->update([
                "nombre"              => $request->nombre,
                "id_usuario_creacion" => getUsuarioAutenticado()->id
            ]);
            $response->setData("Actualizado exitosamente");
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }
    public function delete($ids){
        $response = new Response();
        try{
            foreach($ids as $id){
                $categoria = Categoria::withCount('productos')->find($id);
    
                if ($categoria && $categoria->productos_count === 0) {
                    $categoria->update([
                        "activo"              => 0,
                        "id_usuario_creacion" => getUsuarioAutenticado()->id
                    ]);
                }else{
                    throw new Exception('No se puede eliminar la categoría porque tiene productos asociados.');
                }
            }
            $response->setData("Eliminado exitosamente");
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }

}


?>