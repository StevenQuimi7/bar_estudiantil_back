<?php
namespace App\Services\ventas;

use App\Models\cliente\credito\Credito;
use App\Models\cliente\credito\CreditoMovimiento;
use App\Models\venta\Venta;
use App\Utils\Response;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response AS ResponseHttp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VentaService
{
    public function __construct(){}

    public function index($request){
        $response = new Response();
        try{
            $per_page = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            $fechas = $request->fechas;
            if (!empty($fechas) && is_string($fechas)) {
                $fechas = explode(',', $fechas);
            }
            $fechaInicio = null;
            $fechaFin = null;
            if (is_array($fechas) && count($fechas) === 2) {
                $fechaInicio = Carbon::parse(trim($fechas[0]))->startOfDay();
                $fechaFin = Carbon::parse(trim($fechas[1]))->endOfDay();
            }

            $query = Venta::query();
            $query->when($request->filled('descripcion'),function($subquery) use($request){
                return $subquery->whereHas('cliente',function($q) use($request) {
                    return $q->whereLike('nombres','%'.$request->descripcion.'%')
                    ->orWhereLike('apellidos','%'.$request->descripcion.'%')
                    ->orWhereLike("numero_identificacion",'%'.$request->descripcion.'%');
                });
            })

            ->when($fechaInicio && $fechaFin, function ($query) use ($fechaInicio, $fechaFin) {
                return $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
            })
            ->when($request->filled('estado_gestion'),function($subquery) use($request){
                return $subquery->where('estado_gestion',$request->estado_gestion);
            })

            ->with([
                "user:id,username",
                "cliente:id,id_tipo_cliente,nombres,apellidos,numero_identificacion",
                "cliente.tipo_cliente:id,nombre",
                "detalles_venta"=>function($query){
                    return $query->select('id_venta','id_producto','cantidad','subtotal')
                    ->activo()
                    ->with('producto:id,nombre,codigo,precio');
                }
            ])
            ->orderBy('id','desc')
            ->where('activo',1);

            if ($request->has('download') && ($request->download == true || $request->download == 'true')) {
                $resultado = $query->get();
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

    public function totalVenta($detalles){
        return collect($detalles)->sum(function($x){
            return $x['precio'] * $x['cantidad'];
        });
    }

    public function setDetalles($detalles)
    {
        return collect($detalles)->map(function ($detalle) {
            return [
                'id_producto'         => $detalle['id_producto'],
                'cantidad'            => $detalle['cantidad'],
                'subtotal'            => $detalle['precio'] * $detalle['cantidad'],
                'id_usuario_creacion' => getUsuarioAutenticado()->id,
            ];
        })->toArray();
    }

    public function store($request){
        $response = new Response();
        try{

            $total_venta    = $this->totalVenta($request->detalles);
            $saldo_venta    = $total_venta;
            $credito        = Credito::where('id_cliente', $request->id_cliente)->activo()->first();
            $estado_gestion = $request->input('estado_gestion', 'PENDIENTE');

            

            if(!empty($credito) && $credito->saldo > 0){

                $nuevo_saldo_credito = $credito->saldo - $total_venta;

                if ($nuevo_saldo_credito >= 0) {
                    $saldo_venta = 0;
                    $estado_gestion = 'PAGADO';
                } else {
                    $saldo_venta = abs($nuevo_saldo_credito);
                    $nuevo_saldo_credito = 0;
                }
                
                $createMovimiento = CreditoMovimiento::create([
                    'id_credito_cliente'  => $credito->id,
                    'tipo'                => 'CONSUMO',
                    'monto'               => $total_venta,
                    'descripcion'         => 'consumo en venta fecha '. now() .' Total $'. $total_venta,
                    'saldo_anterior'      => $credito->saldo,
                    'saldo_actual'        => $nuevo_saldo_credito,
                    'id_usuario_creacion' => getUsuarioAutenticado()->id,
                    
                ]);
                $credito = Credito::findOrFail($credito->id);
                $credito->update(['saldo' => $nuevo_saldo_credito]);
            }

            $venta = Venta::create([
                "id_cliente"          => $request->id_cliente,
                "total_pagar"         => $saldo_venta,
                "descuento_credito"   => $credito?->saldo ?? 0,
                "total_venta"         => $total_venta,
                "estado_gestion"      => $estado_gestion,
                "id_usuario_creacion" => getUsuarioAutenticado()->id
            ]);

            $venta->detalles_venta()->createMany($this->setDetalles($request->detalles));
            if($credito && $credito->saldo > 0){
                $moviemiento = CreditoMovimiento::findOrFail($createMovimiento->id);
                $moviemiento->update(['id_venta'=>$venta->id]);
            }

            $response->setData("Registrado existosamente");
            $response->setCode(201);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }

    public function updateEstadoGestion($id,$request){
        $response = new Response();
        try{
            $venta = Venta::findOrFail($id);
            $venta->update([
                "estado_gestion"      => $request->estado_gestion,
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


}


?>