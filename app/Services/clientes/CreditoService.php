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

    public function creditoCliente($request){
        $response = new Response();
        try{
            $credito = Credito::
            with(['cliente'])
            ->where('id_cliente', $request->id_cliente)
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
    public function movimientos($request)
    {
        
        $response = new Response();

        try {
            $per_page = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            // Normalizar fechas
            $fechas = $request->fechas;

            // Si viene como string "fecha1,fecha2"
            if (!empty($fechas) && is_string($fechas)) {
                $fechas = explode(',', $fechas);
            }

            // validar que tengamos exactamente 2 fechas
            $fechaInicio = null;
            $fechaFin = null;

            if (is_array($fechas) && count($fechas) === 2) {
                $fechaInicio = Carbon::parse(trim($fechas[0]))->startOfDay();
                $fechaFin = Carbon::parse(trim($fechas[1]))->endOfDay();
            }

            $creditos = CreditoMovimiento::where('id_credito_cliente', $request->id_credito_cliente)
                ->when($fechaInicio && $fechaFin, function ($query) use ($fechaInicio, $fechaFin) {
                    return $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
                })
                ->with('user:id,username')
                ->activo()
                ->orderBy('id', 'desc')
                ->paginate($per_page, ['*'], 'page', $page);

            $response->setData($creditos);
            $response->setCode(200);

        } catch (Exception $e) {

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