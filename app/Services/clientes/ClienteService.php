<?php
namespace App\Services\clientes;

use App\Models\cliente\Cliente;
use App\Models\cliente\TipoCliente;
use App\Models\curso\Curso;
use App\Models\curso\CursoEstudiante;
use App\Utils\Response;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response AS ResponseHttp;
use Illuminate\Support\Facades\DB;

class ClienteService
{
    public function __construct(){}

    public function index($request){
        $response = new Response();
        try{
            $per_page = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $clientes = Cliente::when(!empty($this->numero_identificacion),function($subquery) use($request){
                return $subquery->where("numero_identificacion",$request->numero_identificacion);
            })
            ->when(!empty($request->nombres), function($query) use($request){
                $query->where('nombres','ilike','%'.$request->nombres.'%');
            })
            ->when(!empty($request->apellidos), function($query) use($request){
                $query->where('apellidos','ilike','%'.$request->apellidos.'%');
            })
            ->whereRelation('tipo_cliente', 'nombre', $request->tipo_cliente)//CLIENTE O ESTUDIANTE

            ->with(["user"=>function($query){
                return $query->select("id","username");
            }])
            ->with(["tipo_cliente"=>function($query){
                return $query->select("id","nombre");
            }])
            ->with(["estudiante_curso"=>function($query){
                return $query->select("id","id_curso", "id_cliente")
                ->with(['curso' => function ($query) {
                    $query->select(
                        'cursos.id',
                        DB::raw("
                            grados.grado || cursos.seccion ||
                            CASE 
                                WHEN especialidades.id IS NOT NULL THEN ' - ' || especialidades.nombre 
                                ELSE ' - ' || niveles.nombre 
                            END AS curso
                        ")
                    )
                    ->join('grados', 'grados.id', '=', 'cursos.id_grado')
                    ->join('niveles', 'niveles.id', '=', 'cursos.id_nivel')
                    ->leftJoin('especialidades', 'especialidades.id', '=', 'cursos.id_especialidad')
                    ->where("cursos.activo",1);
                }]);
            }])
            ->orderBy('apellidos','asc')
            ->where('activo',$request->activo)
            ->paginate($per_page, ['*'], 'page', $page);

            $response->setData($clientes);
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
            $cliente = Cliente::create([
                "id_tipo_cliente"       => $request->id_tipo_cliente,
                "nombres"               => $request->nombres,
                "apellidos"             => $request->apellidos,
                "numero_identificacion" => $request->numero_identificacion,
                "id_usuario_creacion" => getUsuarioAutenticado()->id
            ]);
            if($request->filled("id_curso")){
                $cliente->estudiante_curso()->create([
                    "id_curso"=> $request->id_curso,
                    "id_usuario_creacion"  => getUsuarioAutenticado()->id
                ]);
            }
            $response->setData($cliente);
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
            $estudiante_curso = CursoEstudiante::where('id_cliente',$id)->orderBy('id','desc')->first();
            $cliente = Cliente::where("id",$id)->update([
                "id_tipo_cliente"       => $request->id_tipo_cliente,
                "nombres"               => $request->nombres,
                "apellidos"             => $request->apellidos,
                "numero_identificacion" => $request->numero_identificacion,
                "id_usuario_creacion" => getUsuarioAutenticado()->id
            ]);
            if ($estudiante_curso->id_curso !== $request->id_curso) {

                $estudiante_curso->id_curso = $request->id_curso;
                $estudiante_curso->id_usuario_creacion = getUsuarioAutenticado()->id;
                $estudiante_curso->updated_at = now();
                $estudiante_curso->save();
            }
            $response->setData($cliente);
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
            $cliente = Cliente::with(['ventas', 'credito'])
            ->findOrFail($id);

            // Verificar ventas pendientes o en proceso
            $tieneVentasPendientes = $cliente->ventas()
                ->whereIn('estado_gestion', ['PENDIENTE', 'PROCESO'])
                ->exists();

            if ($tieneVentasPendientes) {
                throw new Exception('No se puede eliminar el cliente porque tiene ventas pendientes o en proceso.');
            }

            //Verificar crédito con saldo mayor a 0
            if ($cliente->credito && $cliente->credito->saldo > 0) {
                throw new Exception("No se puede eliminar el cliente porque tiene un crédito con saldo {$cliente->credito->saldo}");
            }
            $cliente->update(['activo' => 0]);
            $response->setData($cliente);
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }
    public function activarCliente($id){
        $response = new Response();
        try{
            $cliente = Cliente::findOrFail($id);
            $cliente->update(['activo' => 1]);
            $response->setData($cliente);
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }

    public function cargaMasivaCliente($data, $tipo_cliente){
        $response = new Response();
        try{
            $filasExcel = $this->setFilasExcel($data, $tipo_cliente);

            //obtener todas las cedulas del excel
            $cedulas = $filasExcel->pluck('numero_identificacion')->filter()->unique()->toArray();
            //obtener el id del cliente estudiante
            $id_tipo_cliente  = TipoCliente::where('nombre', 'OTROS')->firstOrFail()->id;

            $id_tipo_cliente_estudiante  = TipoCliente::where('nombre', 'ESTUDIANTE')->firstOrFail()->id;
            $validacionEstudiante = $this->validaIdentificacionCliente($cedulas,$id_tipo_cliente_estudiante);
            if($validacionEstudiante->ok) throw new Exception($validacionEstudiante->msj);

            
            $clientesToUpsert = [];
            //iterar info de excel y setear data a guardar o actualizar
            foreach ($filasExcel as $fila) {
                $data = [
                    'numero_identificacion' => $fila['numero_identificacion'],
                    'nombres'               => mb_strtoupper(trim($fila['nombres']), 'UTF-8'),
                    'apellidos'             => mb_strtoupper(trim($fila['apellidos']), 'UTF-8'),
                    'id_tipo_cliente'       => $id_tipo_cliente,
                    'id_usuario_creacion'   => getUsuarioAutenticado()->id,
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ];

                $clientesToUpsert[] = $data;

            }
            if (!empty($clientesToUpsert)) {
                Cliente::upsert($clientesToUpsert, ['numero_identificacion'], ['nombres', 'apellidos', 'id_usuario_creacion', 'updated_at']);
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
    public function cargaMasivaEstudiante($data, $tipo_cliente){
        $response = new Response();
        try{
            $filasExcel = $this->setFilasExcel($data, $tipo_cliente);

            //obtener todas las cedulas del excel
            $cedulas         = $filasExcel->pluck('numero_identificacion')->filter()->unique()->toArray();
            //obtener todos los cursos del excel
            $cursosCompletos = $filasExcel->map(function($fila) {
                $base = "{$fila['nivel']}-{$fila['grado']}-{$fila['seccion']}";
                if (!empty($fila['especialidad'])) {
                    $base .= "-" . $fila['especialidad'];
                }
                return $base;
            })->unique()->toArray();

            //obtener info de base de los cursos del excel
            $cursos             = $this->getCursosPorNombre($cursosCompletos);

            //obtener el id del cliente estudiante
            $id_tipo_cliente    = TipoCliente::where('nombre', 'ESTUDIANTE')->first()->id;; 
            
            $clientesToUpsert         = [];
            $cursoEstudiantesToUpsert = [];
            //iterar info de excel y setear data a guardar o actualizar
            foreach ($filasExcel as $fila) {
                
                $data = [
                    'numero_identificacion' => $fila['numero_identificacion'],
                    'nombres'               => mb_strtoupper(trim($fila['nombres']), 'UTF-8'),
                    'apellidos'             => mb_strtoupper(trim($fila['apellidos']), 'UTF-8'),
                    'id_tipo_cliente'       => $id_tipo_cliente,
                    'id_usuario_creacion'   => getUsuarioAutenticado()->id,
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ];
                $clientesToUpsert[] = $data;

            }
            if (!empty($clientesToUpsert)) {
                Cliente::upsert($clientesToUpsert, ['numero_identificacion'], ['nombres', 'apellidos', 'id_usuario_creacion', 'updated_at']);
            }

            // Volvemos a obtener todos los clientes. Esto incluye a los nuevos con sus IDs
            $clientesActualizados = $this->obtenerClientesExistentes($cedulas);
            foreach ($filasExcel as $fila) {
                $numero_identificacion = $fila['numero_identificacion'];
                $curso_row = "{$fila['nivel']}-{$fila['grado']}-{$fila['seccion']}";

                if (strtoupper(trim($fila['nivel'])) === 'BACHILLERATO') {
                    $curso_row .= '-' . strtoupper(trim($fila['especialidad'] ?? ''));
                }

                $curso = $cursos->get($curso_row);
                $cliente = $clientesActualizados->get($numero_identificacion);

                if (!$curso) throw new Exception("Error en fila {$fila['fila_num']}: no existe el curso '$curso_row'.");
                
                $cursoEstudiantesToUpsert[] = [
                    'id_cliente'          => $cliente->id,
                    'id_curso'            => $curso['id'],
                    'id_usuario_creacion' => getUsuarioAutenticado()->id,
                ];
            }

            if (!empty($cursoEstudiantesToUpsert)) {
                CursoEstudiante::upsert($cursoEstudiantesToUpsert, ['id_cliente'], ['id_curso','updated_at']);
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

    public function setFilasExcel($data, $tipo_cliente)
    {

        $columnas_cliente    = ['numero_identificacion', 'apellidos', 'nombres'];
        $columnas_estudiante = ['numero_identificacion', 'apellidos', 'nombres', 'nivel', 'grado', 'especialidad', 'seccion'];
        $columnas = $tipo_cliente == "ESTUDIANTE" ? $columnas_estudiante : $columnas_cliente;
        $filas = collect($data)
            ->slice(1) // saltar encabezado
            ->map(function ($fila, $key) use ($columnas, $tipo_cliente) {

                foreach ($columnas as $index => $field) {
                    if (empty($fila[$index])) {
                        throw new Exception("Error: campo {$field} vacío en fila " . ($key + 1));
                    }
                }
                if($tipo_cliente == "ESTUDIANTE"){
                    return [
                        'numero_identificacion' => strtoupper(trim($fila[0] ?? '')),
                        'apellidos'             => mb_strtoupper(trim($fila[1] ?? ''), 'UTF-8'),
                        'nombres'               => mb_strtoupper(trim($fila[2] ?? ''), 'UTF-8'),
                        'nivel'                 => mb_strtoupper(trim($fila[3] ?? ''), 'UTF-8'),
                        'grado'                 => strtoupper(trim($fila[4] ?? '')),
                        'especialidad'          => strtoupper(trim($fila[5] ?? '')),
                        'seccion'               => strtoupper(trim($fila[6] ?? '')),
                        'fila_num'              => $key + 1,
                    ];
                }
                return [
                    'numero_identificacion' => strtoupper(trim($fila[0] ?? '')),
                    'apellidos'             => mb_strtoupper(trim($fila[1] ?? ''), 'UTF-8'),
                    'nombres'               => mb_strtoupper(trim($fila[2] ?? ''), 'UTF-8'),
                    'fila_num'              => $key + 1,
                ];
            });

        // Verificar si hay número de identificación repetidas
        $repetidos = $filas->pluck('numero_identificacion')->duplicates();

        if ($repetidos->isNotEmpty()) {
            throw new Exception("Error: números de identificaciones repetidos: " . $repetidos->implode(', '));
        }

        return $filas;
    }

    public function validaIdentificacionCliente($cedulas,$id_tipo_cliente){
        $msj ='';
        $ok=false;
        
        $clientes_estudiantes = Cliente::where('id_tipo_cliente',$id_tipo_cliente)
                ->whereIn('numero_identificacion',$cedulas)
                ->pluck('numero_identificacion');
        if(count($clientes_estudiantes)>0){
            $msj= implode(", ",$clientes_estudiantes->toArray());
            $ok=true;
        }
        
        return (object)[
            "ok"=>$ok,
            "msj"=>"Error, las siguientes identificaciones se encuentran registradas como estudiantes: ".$msj
        ];
    }

    public function getCursosPorNombre(array $cursosCompletos)
    {
        return Curso::select(
                'cursos.id',
                'cursos.seccion',
                'niveles.nombre as nivel',
                'grados.grado as grado',
                DB::raw("
                    CONCAT(
                        niveles.nombre, '-', 
                        grados.grado, '-', 
                        cursos.seccion,
                        CASE 
                            WHEN especialidades.nombre IS NOT NULL THEN CONCAT('-', especialidades.nombre) 
                            ELSE '' 
                        END
                    ) as curso_completo
                ")
            )
            ->join('niveles', 'niveles.id', '=', 'cursos.id_nivel')
            ->join('grados', 'grados.id', '=', 'cursos.id_grado')
            ->leftJoin('especialidades', 'especialidades.id', '=', 'cursos.id_especialidad')
            ->where('cursos.activo', 1)
            ->whereIn(DB::raw("
                CONCAT(
                    niveles.nombre, '-', 
                    grados.grado, '-', 
                    cursos.seccion,
                    CASE 
                        WHEN especialidades.nombre IS NOT NULL THEN CONCAT('-', especialidades.nombre) 
                        ELSE '' 
                    END
                )
            "), $cursosCompletos)
            ->get()
            ->keyBy('curso_completo');
    }

    public function obtenerClientesExistentes($cedulas){
        return Cliente::whereIn('numero_identificacion', $cedulas)
                    ->whereRelation('tipo_cliente', 'nombre', '=', 'ESTUDIANTE')
                    ->get()
                    ->keyBy('numero_identificacion');
    }


}


?>