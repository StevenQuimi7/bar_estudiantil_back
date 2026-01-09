<?php
namespace App\Services\estudiantes;

use App\Models\estudiante\Estudiante;
use App\Utils\Response;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response AS ResponseHttp;

class EstudianteService
{
    public function __construct(){}

    public function index($request){
        $response = new Response();
        try{
            $per_page = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $query = Estudiante::query();
            $query->activo()
            ->when($request->filled('full_name'), function ($query) use ($request) {
                return $query->wherehas('cliente', function ($query) use ($request) {
                    $query->where('nombres', 'like', '%' . $request->full_name . '%')
                    ->orWhere('apellidos', 'like', '%' . $request->full_name . '%');
                });
            })
            ->when($request->filled('numero_identificacion'), function ($query) use ($request) {
                return $query->wherehas('cliente', function ($query) use ($request) {
                    $query->where('numero_identificacion', 'like', '%' . $request->numero_identificacion . '%');
                });
            })
            ->with([
                'cliente:id,id_tipo_cliente,nombres,apellidos,numero_identificacion',
                'cliente.tipo_cliente:id,nombre',
                'curso:id,id_grado,id_nivel,seccion,id_especialidad',
                'curso.grado:id,grado',
                'curso.nivel:id,nombre',
                'curso.especialidad:id,nombre',

            ]);
            if ($request->has('download') && ($request->download == true || $request->download == 'true')) {
                $resultado = $query->get()->map(function ($item) {
                    return $this->transformEstudiantes($item);
                });
            } else {
                $resultado = $query->paginate($per_page, ['*'], 'page', $page);
            }

            $response->setData($resultado);
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

            $estudiante = Estudiante::create([
                "id_cliente"          => $request->cliente->id,
                "id_curso"            => $request->id_curso,
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
            $estudiante = Estudiante::findOrFail($id);
            $estudiante->update([
                "id_curso"            => $request->id_curso,
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
                $estudianteModel = Estudiante::with('curso')
                    ->findOrFail($id);
                /*
                // Verificar si tiene curso activos
                $tieneCursosActivos = $estudianteModel->cursos()
                    ->where('activo', 1)
                    ->exists();
    
                if ($tieneCursosActivos) {
                    throw new Exception('No se puede eliminar la estudiante porque tiene curso activo.');
                }
                */
                // Marcar como inactivo
                $estudianteModel->update([
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
    public function transformEstudiantes($estudiante){
        return [
            "tipo_cliente"          => $estudiante->cliente->tipo_cliente->nombre,
            "nombres"               => $estudiante->cliente->nombres,
            "apellidos"             => $estudiante->cliente->apellidos,
            "numero_identificacion" => $estudiante->cliente->numero_identificacion,
            "nivel"                 => $estudiante->curso->nivel->nombre,
            "grado"                 => $estudiante->curso->grado->grado,
            "seccion"               => $estudiante->curso->seccion,
            "especialidad"          => $estudiante->curso?->especialidad?->nombre ?? '',
            "fecha_creacion"        => ($estudiante->created_at)->format('Y-m-d'),
        ];
    }

}


?>