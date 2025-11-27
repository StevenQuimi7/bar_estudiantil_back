<?php
namespace App\Services\especialidades;

use App\Models\especialidad\Especialidad;
use App\Utils\Response;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response AS ResponseHttp;

class EspecialidadService
{
    public function __construct(){}

    public function index($request){
        $response = new Response();
        try{
            $per_page = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $especialidades = Especialidad::activo()
            ->when($request->filled('nombre'), function ($query) use ($request) {
                return $query->where('nombre', 'like', '%' . $request->nombre . '%');
            })
            ->with(['user:id,username'])
            ->paginate($per_page, ['*'], 'page', $page);

            $response->setData($especialidades);
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }
    public function comboEspecialidades(){
        $response = new Response();
        try{
            $especialidades = Especialidad::select("id as value","nombre as label")->activo()
            ->get();

            $response->setData($especialidades);
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
            $especialidad = Especialidad::create([
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
            $especialidad = Especialidad::where("id",$id)->update([
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
                $especialidadModel = Especialidad::with('cursos')
                    ->findOrFail($id);
    
                // Verificar si tiene cursos activos
                $tieneCursosActivos = $especialidadModel->cursos()
                    ->where('activo', 1)
                    ->exists();
    
                if ($tieneCursosActivos) {
                    throw new Exception('No se puede eliminar la especialidad porque tiene cursos asociados.');
                }
    
                // Marcar como inactivo
                $especialidadModel->update([
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