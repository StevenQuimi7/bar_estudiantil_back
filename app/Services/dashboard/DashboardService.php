<?php
namespace App\Services\dashboard;

use App\Models\categoria\Categoria;
use App\Models\venta\DetalleVenta;
use App\Models\venta\Venta;
use App\Utils\Response;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response AS ResponseHttp;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function __construct(){}

    public function comparativaAnios($request) {

        $response = new Response();
        try{

            $anioInicio = $request?->anio_inicio ?? date('Y');
            $anioFin = $request?->anio_fin ?? date('Y');
            //diferente base
            /*$data = Venta::query()
                ->activo()
                ->whereIn(DB::raw('YEAR(created_at)'), [$anioInicio, $anioFin])
                ->selectRaw('YEAR(created_at) as anio, MONTH(created_at) as mes, SUM(total_venta) as monto')
                ->groupBy('anio', 'mes')
                ->get();*/

                //sqlite
            $data = Venta::query()
                ->activo()
                // En SQLite: strftime('%Y', created_at) extrae el año
                ->whereIn(DB::raw("strftime('%Y', created_at)"), [(string)$anioInicio, (string)$anioFin])
                ->selectRaw("
                    strftime('%Y', created_at) as anio, 
                    CAST(strftime('%m', created_at) AS INTEGER) as mes, 
                    SUM(total_venta) as monto
                ")
                ->groupBy('anio', 'mes')
                ->get();

            $comparativa = [];
            foreach ([$anioInicio, $anioFin] as $anio) {
                for ($i = 1; $i <= 12; $i++) {
                    $registro = $data->where('anio', $anio)->where('mes', $i)->first();
                    $comparativa[] = [
                        'mes'   => $this->nombreMes($i),
                        'monto' => $registro ? (float)$registro->monto : 0,
                        'anio'  => (string)$anio
                    ];
                }
            }
        
            $response->setData($comparativa);
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }

    public function ventaMeses($request) {
        
        $response = new Response();
        try{
            $anio = $request?->anio ?? date('Y');
    
            $ventas = Venta::query()
                ->activo()
                ->where('estado_gestion', 'PAGADO')
                ->whereYear('created_at', $anio) // Asumiendo que tienes fecha_pago
                //diferente base
                //->selectRaw('MONTH(created_at) as mes, SUM(total_venta) as monto')
                //sqlite
                ->selectRaw("CAST(strftime('%m', created_at) AS INTEGER) as mes, SUM(total_venta) as monto")
                ->groupBy('mes')
                ->orderBy('mes')
                ->get();
    
            // Esto asegura que si un mes tiene 0 ventas, aparezca en la gráfica
            $mesesData = collect(range(1, 12))->map(function ($mes) use ($ventas) {
                $ventaMes = $ventas->firstWhere('mes', $mes);
                return [
                    'mes'   => $this->nombreMes($mes),
                    'monto' => $ventaMes ? (float)$ventaMes->monto : 0
                ];
            });
            $response->setData($mesesData);
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }

    private function nombreMes($numero) {
        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        return $meses[$numero - 1];
    }

    public function topFive(){
        $response = new Response();
        try{

            $anioActual = date('Y');
            
            $query = Venta::query();
            $query->activo()->where('estado_gestion', 'PAGADO')->whereYear('created_at', $anioActual);

            $clientes = $this->clientesVentas(clone $query);
            $usuarios = $this->usuariosVentas(clone $query);
            $productos = $this->productosVentas(clone $query);
            
            $data = [
                'clientes' => $clientes,
                'usuarios' => $usuarios,
                'productos' => $productos   
            ];

            $response->setData($data);
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }

    public function clientesVentas($query) {
        return $query->selectRaw('count(*) as total, id_cliente')
            ->with(['cliente:id,nombres,apellidos'])
            ->groupBy('id_cliente')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'nombre' => $item->cliente ? $item->cliente->nombres . ' ' . $item->cliente->apellidos : 'Desconocido',
                    'total'  => $item->total
                ];
            });
    }

    public function usuariosVentas($query) {
        return $query->selectRaw('count(*) as total, id_usuario_creacion')
            ->with(['user:id,username'])
            ->groupBy('id_usuario_creacion')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'nombre' => $item->user ? $item->user->username : 'Sistema',
                    'total'  => $item->total
                ];
            });
    }

    public function productosVentas($query) {
        return DetalleVenta::whereIn('id_venta', $query->pluck('id'))
            ->selectRaw('id_producto, count(*) as total')
            ->with(['producto:id,nombre'])
            ->groupBy('id_producto')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($detalle) {
                return [
                    'nombre' => $detalle->producto->nombre ?? 'Producto no encontrado',
                    'total'  => (int) $detalle->total
                ];
            });
    }
    
}


?>