<?php

namespace App\Http\Controllers\api\especialidades;

use App\Http\Controllers\Controller;
use App\Http\Requests\especialidades\EspecialidadStoreRequest;
use App\Http\Requests\especialidades\EspecialidadUpdateRequest;
use App\Services\especialidades\EspecialidadService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class EspecialidadController extends Controller
{

    protected $_especialidadService;
    public function __construct(){
        $this->_especialidadService = new EspecialidadService();
    }

    public function index(Request $request)
    {
        try{
            $especialidades = $this->_especialidadService->index($request);
            if(!$especialidades->getOk()) throw new Exception($especialidades->getMsjError(), $especialidades->getCode());
            return response()->json(['ok' => true, 'data' => $especialidades->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
    public function comboEspecialidades()
    {
        try{
            $especialidades = $this->_especialidadService->comboEspecialidades();
            if(!$especialidades->getOk()) throw new Exception($especialidades->getMsjError(), $especialidades->getCode());
            return response()->json(['ok' => true, 'data' => $especialidades->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function store(EspecialidadStoreRequest $request)
    {
        try{
            DB::beginTransaction();
            $especialidad = $this->_especialidadService->store($request);
            if(!$especialidad->getOk()) throw new Exception($especialidad->getMsjError(), $especialidad->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $especialidad->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function update(EspecialidadUpdateRequest $request, string $id)
    {
        try{
            DB::beginTransaction();
            $especialidad = $this->_especialidadService->update($id, $request);
            if(!$especialidad->getOk()) throw new Exception($especialidad->getMsjError(), $especialidad->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $especialidad->getData()],200);
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
            $especialidad = $this->_especialidadService->delete($request->ids);
            if(!$especialidad->getOk()) throw new Exception($especialidad->getMsjError(), $especialidad->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $especialidad->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
}
