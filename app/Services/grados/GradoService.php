<?php
namespace App\Services\grados;

use App\Models\grado\Grado;
use App\Utils\Response;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response AS ResponseHttp;

class GradoService
{
    public function __construct(){}

    public function index($request){
        $response = new Response();
        try{
            $per_page = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $grados = Grado::activo()
            ->when($request->filled('grado'), function ($query) use ($request) {
                return $query->where('grado', $request->grado);
            })
            ->with(['user:id,username'])
            ->paginate($per_page, ['*'], 'page', $page);

            $response->setData($grados);
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }
    public function comboGrados(){
        $response = new Response();
        try{
            $grados = Grado::select("id as value","grado as label")->activo()
            ->get();

            $response->setData($grados);
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
            $grado = Grado::create([
                "grado"              => $request->grado,
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
            $grado = Grado::where("id",$id)->update([
                "grado"              => $request->grado,
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

                $gradoModel = Grado::with('cursos')
                    ->findOrFail($id);
    
                // Verificar si tiene cursos activos
                $tieneCursosActivos = $gradoModel->cursos()
                    ->where('activo', 1)
                    ->exists();
    
                if ($tieneCursosActivos) {
                    throw new Exception('No se puede eliminar el grado porque tiene cursos asociados.');
                }
    
                $gradoModel->update([
                    "activo"              => 0,
                    "id_usuario_creacion" => getUsuarioAutenticado()->id
                ]);
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