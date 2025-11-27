<?php
namespace App\Services\cursos;

use App\Models\curso\Curso;
use App\Utils\Response;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response AS ResponseHttp;

class CursoService
{
    public function __construct(){}

    public function index($request){
        $response = new Response();
        try{
            $per_page = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $cursos = Curso::when($request->filled('id_nivel'),function($subquery) use($request){
                return $subquery->where("id_nivel",$request->id_nivel);
            })
            ->when($request->filled('id_grado'),function($subquery) use($request){
                return $subquery->where("id_grado",$request->id_grado);
            })
            ->with(["grado"=>function($query){
                return $query->select("id","grado")->activo();
            }])
            ->with(["nivel"=>function($query){
                return $query->select("id","nombre")->activo();
            }])
            ->with(["especialidad"=>function($query){
                return $query->select("id","nombre")->activo();
            }])
            ->with(["user"=>function($query){
                return $query->select("id","username");
            }])
            ->orderBy('created_at','desc')
            ->activo()
            ->paginate($per_page, ['*'], 'page', $page);

            $response->setData($cursos);
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
            $curso = Curso::create([
                "id_nivel"            => $request->id_nivel,
                "id_grado"            => $request->id_grado,
                "seccion"             => $request->seccion,
                "id_especialidad"     => $request->id_especialidad ?? null,
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
            $curso = Curso::where("id",$id)->update([
                "id_nivel"            => $request->id_nivel,
                "id_grado"            => $request->id_grado,
                "seccion"             => $request->seccion,
                "id_especialidad"     => $request->id_especialidad ?? null,
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
    public function delete($id){
        $response = new Response();
        try{
            foreach($id as $id){
                $cursoModel = Curso::find($id);
                
                if ($cursoModel->curso_estudiante()->exists()) {
                    throw new Exception("No se puede eliminar el curso porque tiene estudiantes asociados.");
                }
    
                $cursoModel->update([
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
    public function validacionCursoExistente($request){
        return Curso::where('id_nivel',$request->id_nivel)
            ->where('id_grado',$request->id_grado)
            ->where('seccion',strtoupper(trim($request->seccion)))
            ->when($request->filled('id_especialidad'),function($query) use($request){
                return $query->where('id_especialidad',$request->id_especialidad);
            })
            ->when($request->filled('idCurso'),function($query) use($request){
                return $query->where('id','!=',$request->idCurso);
            })
            
            ->activo()
            ->exists();
    }

}


?>