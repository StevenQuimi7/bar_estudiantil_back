<?php

namespace App\Http\Controllers\api\niveles;

use App\Http\Controllers\Controller;
use App\Http\Requests\niveles\NivelStoreRequest;
use App\Http\Requests\niveles\NivelUpdateRequest;
use App\Services\niveles\NivelService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class NivelController extends Controller
{

    protected $_nivelService;
    public function __construct(){
        // $this->middleware('can:niveles.index')->only('index');
        // $this->middleware('can:niveles.store')->only('store');
        // $this->middleware('can:niveles.update')->only('update');
        // $this->middleware('can:niveles.destroy')->only('destroy');
        $this->_nivelService = new NivelService();
    }

    public function index(Request $request)
    {
        try{
            $niveles = $this->_nivelService->index($request);
            if(!$niveles->getOk()) throw new Exception($niveles->getMsjError(), $niveles->getCode());
            return response()->json(['ok' => true, 'data' => $niveles->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function comboNiveles()
    {
        try{
            $niveles = $this->_nivelService->comboNiveles();
            if(!$niveles->getOk()) throw new Exception($niveles->getMsjError(), $niveles->getCode());
            return response()->json(['ok' => true, 'data' => $niveles->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function store(NivelStoreRequest $request)
    {
        $validator = $request->validated();
        try{
            DB::beginTransaction();
            $nivel = $this->_nivelService->store($request);
            if(!$nivel->getOk()) throw new Exception($nivel->getMsjError(), $nivel->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $nivel->getData()],200);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function update(NivelUpdateRequest $request, string $id)
    {
        $validator = $request->validated();
        try{
            DB::beginTransaction();
            $nivel = $this->_nivelService->update($id, $request);
            if(!$nivel->getOk()) throw new Exception($nivel->getMsjError(), $nivel->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $nivel->getData()],200);
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
            $nivel = $this->_nivelService->delete($request->ids);
            if(!$nivel->getOk()) throw new Exception($nivel->getMsjError(), $nivel->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $nivel->getData()],200);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
}
