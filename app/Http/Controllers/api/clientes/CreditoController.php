<?php

namespace App\Http\Controllers\api\clientes;

use App\Http\Controllers\Controller;
use App\Http\Requests\clientes\CreditoStoreRequest;
use App\Services\clientes\CreditoService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class CreditoController extends Controller
{

    protected $_creditoService;
    public function __construct(){
        $this->_creditoService = new CreditoService();
    }

    public function creditoCliente(Request $request)
    {
        try{
            $creditos = $this->_creditoService->creditoCliente($request);
            if(!$creditos->getOk()) throw new Exception($creditos->getMsjError(), $creditos->getCode());
            return response()->json(['ok' => true, 'data' => $creditos->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
    public function movimientos(Request $request)
    {
        try{
            $creditos = $this->_creditoService->movimientos($request);
            if(!$creditos->getOk()) throw new Exception($creditos->getMsjError(), $creditos->getCode());
            return response()->json(['ok' => true, 'data' => $creditos->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function store(CreditoStoreRequest $request)
    {
        try{
            DB::beginTransaction();
            $saldo = 0;
            $saldo_anterior = 0;
            //consultar saldo cliente
            if(empty($request->credito_cliente)){
                if($request->tipo == 'ABONO') {
                    $saldo = $saldo + $request->monto;
                }else{
                    throw new Exception("El cliente no tiene saldo disponible");
                }
                
            }else{
                $saldo_anterior = $request->credito_cliente['saldo'];
                if($request->tipo == 'ABONO') {
                    $saldo = $request->credito_cliente['saldo'] + $request->monto;
                }else{
                    $saldo = $request->credito_cliente['saldo'] - $request->monto;
                }
                
            }

            $Credito = $this->_creditoService->store($request, $saldo, $saldo_anterior);
            if(!$Credito->getOk()) throw new Exception($Credito->getMsjError(), $Credito->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $Credito->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
}
