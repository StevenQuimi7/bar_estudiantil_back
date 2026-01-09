<?php

namespace App\Http\Controllers\api\dashboard;

use App\Http\Controllers\Controller;
use App\Services\dashboard\DashboardService;
use Illuminate\Http\Request;
use Exception;

class DashboardController extends Controller
{

    protected $_dashboardService;
    public function __construct(){
        $this->_dashboardService = new DashboardService();
    }

    public function comparativaAnios(Request $request)
    {
        try{
            $dashboard = $this->_dashboardService->comparativaAnios($request);
            if(!$dashboard->getOk()) throw new Exception($dashboard->getMsjError(), $dashboard->getCode());
            return response()->json(['ok' => true, 'data' => $dashboard->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function ventaMeses(Request $request)
    {
        try{
            $dashboard = $this->_dashboardService->ventaMeses($request);
            if(!$dashboard->getOk()) throw new Exception($dashboard->getMsjError(), $dashboard->getCode());
            return response()->json(['ok' => true, 'data' => $dashboard->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function topFive(Request $request)
    {
        try{
            $dashboard = $this->_dashboardService->topFive();
            if(!$dashboard->getOk()) throw new Exception($dashboard->getMsjError(), $dashboard->getCode());
            return response()->json(['ok' => true, 'data' => $dashboard->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
}
