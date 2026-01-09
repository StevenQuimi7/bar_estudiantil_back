<?php
namespace App\Services\auditorias;

use App\Models\Auditoria;
use App\Models\categoria\Categoria;
use App\Models\cliente\Cliente;
use App\Models\curso\Curso;
use App\Models\especialidad\Especialidad;
use App\Models\grado\Grado;
use App\Models\nivel\Nivel;
use App\Models\producto\Producto;
use App\Models\venta\Venta;
use App\Utils\Response;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response AS ResponseHttp;

class AuditoriaService
{
    public function __construct(){}

    public const MODULOS = [
        Venta::class,
        Producto::class,
        Cliente::class,
        Categoria::class,
        Grado::class,
        Curso::class,
        Especialidad::class,
        Nivel::class,
    ];


    public function index($request){
        $response = new Response();
        try{
            $per_page = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $auditorias = Auditoria::activo()
            ->when($request->filled('accion'), function ($query) use ($request) {
                return $query->where('accion', $request->accion );
            })
            ->when($request->filled('modulo') && in_array($request->modulo, self::MODULOS), function ($query) use ($request) {
                return $query->where('auditable_type', $request->modulo);
            })
            ->with(['user:id,username'])
            ->orderBy('id','desc')
            ->paginate($per_page, ['*'], 'page', $page);
            
            $response->setData($auditorias);
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }

    public function comboModulos(){
        $response = new Response();
        try{
            $modulos = [
                ['value' => Venta::class, 'label' => 'VENTAS'],
                ['value' => Producto::class, 'label' => 'PRODUCTOS'],
                ['value' => Cliente::class, 'label' => 'CLIENTES'],
                ['value' => Categoria::class, 'label' => 'CATEGORIAS'],
                ['value' => Grado::class, 'label' => 'GRADOS'],
                ['value' => Curso::class, 'label' => 'CURSOS'],
                ['value' => Especialidad::class, 'label' => 'ESPECIALIDADES'],
                ['value' => Nivel::class, 'label' => 'NIVELES'],
            ];
            $response->setData($modulos);
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }

    public function comboAcciones(){
        $response = new Response();
        try{
            $acciones = [
                ['label' => 'CREAR', 'value' => 'CREAR'],
                ['label' => 'ACTUALIZAR', 'value' => 'ACTUALIZAR'],
                ['label' => 'ELIMINAR', 'value' => 'ELIMINAR'],
            ];
            $response->setData($acciones);
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