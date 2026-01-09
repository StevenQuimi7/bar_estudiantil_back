<?php

namespace App\Http\Controllers\api\ventas;

use App\Exports\admin\venta\VentaDiariaExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ventas\VentaStoreRequest;
use App\Services\ventas\VentaService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class VentaController extends Controller
{

    protected $_ventaService;
    public function __construct(){
        $this->_ventaService = new VentaService();
    }

    public function index(Request $request)
    {
        try{
            $ventas = $this->_ventaService->index($request);
            if(!$ventas->getOk()) throw new Exception($ventas->getMsjError(), $ventas->getCode());
            return response()->json(['ok' => true, 'data' => $ventas->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function store(VentaStoreRequest $request)
    {
        try{
            DB::beginTransaction();
            $venta = $this->_ventaService->store($request);
            if(!$venta->getOk()) throw new Exception($venta->getMsjError(), $venta->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $venta->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function updateEstadoGestion(Request $request, $id)
    {
        try{
            DB::beginTransaction();
            $venta = $this->_ventaService->updateEstadoGestion($id,$request);
            if(!$venta->getOk()) throw new Exception($venta->getMsjError(), $venta->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $venta->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }


    public function exportar(Request $request){
        try {

            $data = $this->_ventaService->index($request);
            if(!$data->getOk()) throw new Exception($data->getMsjError(), $data->getCode());
            return Excel::download(new VentaDiariaExport($data->getData()), 'ventas-' . time() . '.xlsx');
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }


}
