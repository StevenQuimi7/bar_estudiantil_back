<?php

namespace App\Http\Controllers\api\auditorias;

use App\Http\Controllers\Controller;
use App\Services\auditorias\AuditoriaService;
use Illuminate\Http\Request;
use Exception;

class AuditoriaController extends Controller
{

    protected $_auditoriaService;
    public function __construct(){
        // $this->middleware('can:auditorias.index')->only('index');
        $this->_auditoriaService = new AuditoriaService();
    }

    public function index(Request $request)
    {
        try{
            $auditorias = $this->_auditoriaService->index($request);
            if(!$auditorias->getOk()) throw new Exception($auditorias->getMsjError(), $auditorias->getCode());
            return response()->json(['ok' => true, 'data' => $auditorias->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function comboModulos()
    {
        try{
            $modulos = $this->_auditoriaService->comboModulos();
            if(!$modulos->getOk()) throw new Exception($modulos->getMsjError(), $modulos->getCode());
            return response()->json(['ok' => true, 'data' => $modulos->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
    public function comboAcciones()
    {
        try{
            $acciones = $this->_auditoriaService->comboAcciones();
            if(!$acciones->getOk()) throw new Exception($acciones->getMsjError(), $acciones->getCode());
            return response()->json(['ok' => true, 'data' => $acciones->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
   
}
