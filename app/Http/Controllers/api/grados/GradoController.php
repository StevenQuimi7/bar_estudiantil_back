<?php

namespace App\Http\Controllers\api\grados;

use App\Http\Controllers\Controller;
use App\Http\Requests\grados\GradoStoreRequest;
use App\Http\Requests\grados\GradoUpdateRequest;
use App\Services\grados\GradoService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class GradoController extends Controller
{

    protected $_gradoService;
    public function __construct(){
        // $this->middleware('can:grados.index')->only('index');
        // $this->middleware('can:grados.store')->only('store');
        // $this->middleware('can:grados.update')->only('update');
        // $this->middleware('can:grados.destroy')->only('destroy');
        $this->_gradoService = new GradoService();
    }

    public function index(Request $request)
    {
        try{
            $grados = $this->_gradoService->index($request);
            if(!$grados->getOk()) throw new Exception($grados->getMsjError(), $grados->getCode());
            return response()->json(['ok' => true, 'data' => $grados->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
    public function comboGrados()
    {
        try{
            $grados = $this->_gradoService->comboGrados();
            if(!$grados->getOk()) throw new Exception($grados->getMsjError(), $grados->getCode());
            return response()->json(['ok' => true, 'data' => $grados->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function store(GradoStoreRequest $request)
    {
        try{
            DB::beginTransaction();
            $validator = $request->validated();
            $grado = $this->_gradoService->store($request);
            if(!$grado->getOk()) throw new Exception($grado->getMsjError(), $grado->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $grado->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function update(GradoUpdateRequest $request, string $id)
    {
        try{
            DB::beginTransaction();
            $validator = $request->validated();
            $grado = $this->_gradoService->update($id, $request);
            if(!$grado->getOk()) throw new Exception($grado->getMsjError(), $grado->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $grado->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function destroy(Request $request)
    {
        try{
            DB::beginTransaction();
            if(!$request->filled('ids')) throw new Exception('Error no se puede eliminar, campo id vacio');
            $grado = $this->_gradoService->delete($request->ids);
            if(!$grado->getOk()) throw new Exception($grado->getMsjError(), $grado->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $grado->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
}
