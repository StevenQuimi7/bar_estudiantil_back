<?php

namespace App\Http\Controllers\api\estudiantes;

use App\Http\Controllers\Controller;
use App\Http\Requests\estudiantes\EstudiantesStoreRequest;
use App\Http\Requests\estudiantes\EstudiantesUpdateRequest;
use App\Services\estudiantes\EstudianteService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class EstudianteController extends Controller
{
    protected $_estudianteService;
    public function __construct(){
        $this->_estudianteService = new EstudianteService();
    }

    public function index(Request $request)
    {
        try{
            $estudiantes = $this->_estudianteService->index($request);
            if(!$estudiantes->getOk()) throw new Exception($estudiantes->getMsjError(), $estudiantes->getCode());
            return response()->json(['ok' => true, 'data' => $estudiantes->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function store(EstudiantesStoreRequest $request)
    {
        try{
            DB::beginTransaction();
            $usuario = $this->_estudianteService->store($request);
            if(!$usuario->getOk()) throw new Exception($usuario->getMsjError(), $usuario->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $usuario->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function update(EstudiantesUpdateRequest $request, string $id)
    {
        try{
            DB::beginTransaction();
            $usuario = $this->_estudianteService->update($id, $request);
            if(!$usuario->getOk()) throw new Exception($usuario->getMsjError(), $usuario->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $usuario->getData()],200);
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
            $usuario = $this->_estudianteService->delete($request->ids);
            if(!$usuario->getOk()) throw new Exception($usuario->getMsjError(), $usuario->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $usuario->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
}
