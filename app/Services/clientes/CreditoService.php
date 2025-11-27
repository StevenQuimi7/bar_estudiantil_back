<?php
namespace App\Services\clientes;

use App\Models\cliente\credito\Credito;
use App\Models\cliente\credito\CreditoMovimiento;
use App\Utils\Response;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response AS ResponseHttp;

class CreditoService
{
    public function __construct(){}

    public function find($id){
        $response = new Response();
        try{
            $credito = Credito::with('user')
            ->where('id_cliente', $id)
            ->activo()
            ->first();

            $response->setData($credito);
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }
    public function movimientos($request){
        $response = new Response();
        try{
            $per_page = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $creditos = CreditoMovimiento::where('id_credito_cliente', $request->credito_cliente->id)
            ->when((!empty($request->fecha_inicio) && !empty($request->fecha_fin)), function($query) use($request) {
                $fechaInicio = Carbon::parse($request->fecha_inicio)->startOfDay();
                $fechaFin = Carbon::parse($request->fecha_fin)->endOfDay();
                return $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
            })
            ->with('user:id,username')
            ->activo()
            ->orderBy('id', 'desc')
            ->paginate($per_page, ['*'], 'page', $page);

            $response->setData($creditos);
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }
    public function store($request,$saldo,$saldo_anterior){
        $response = new Response();
        try{
            $save = Credito::updateOrCreate(
                [
                    "id_cliente" => $request->id_cliente,
                ],
                [
                    "saldo"                 => $saldo,
                    "id_usuario_creacion"   => getUsuarioAutenticado()->id
                ]
            );
            $save->movimientos()->create([
                'tipo'           => $request->tipo,
                'monto'          => $request->monto,
                'saldo_anterior' => $saldo_anterior,
                'saldo_actual'   => $saldo,
                'descripcion'    => $request->descripcion ?? null,
                "id_usuario_creacion" => getUsuarioAutenticado()->id
 
            ]);
            $response->setData($save);
            $response->setCode(201);
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