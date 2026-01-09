<?php

namespace App\Http\Controllers\api\cursos;

use App\Http\Controllers\Controller;
use App\Http\Requests\cursos\CursoStoreRequest;
use App\Http\Requests\cursos\CursoUpdateRequest;
use App\Services\cursos\CursoService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class CursoController extends Controller
{
    protected $_cursoService;
    public function __construct(){
        $this->_cursoService = new CursoService();
    }

    public function index(Request $request)
    {
        try{
            $cursos = $this->_cursoService->index($request);
            if(!$cursos->getOk()) throw new Exception($cursos->getMsjError(), $cursos->getCode());
            return response()->json(['ok' => true, 'data' => $cursos->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
    
    public function comboCursos(Request $request)
    {
        try{
            $combo_cursos = $this->_cursoService->comboCursos($request);
            if(!$combo_cursos->getOk()) throw new Exception($combo_cursos->getMsjError(), $combo_cursos->getCode());
            return response()->json(['ok' => true, 'data' => $combo_cursos->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function store(CursoStoreRequest $request)
    {
        try{
            DB::beginTransaction();
            if($this->_cursoService->validacionCursoExistente($request)) throw new Exception("El curso ya existe");
            $curso = $this->_cursoService->store($request);
            if(!$curso->getOk()) throw new Exception($curso->getMsjError(), $curso->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $curso->getData()],200);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
    
    public function update(CursoUpdateRequest $request, string $id)
    {
        try{
            DB::beginTransaction();
            if($this->_cursoService->validacionCursoExistente($request)) throw new Exception("El curso ya existe");
            $curso = $this->_cursoService->update($id, $request);
            if(!$curso->getOk()) throw new Exception($curso->getMsjError(), $curso->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $curso->getData()],200);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function destroy(Request $request)
    {
        try{
            DB::beginTransaction();
            if(!$request->filled('ids')) throw new Exception('Error no se puede eliminar, campo id vacio');
            $curso = $this->_cursoService->delete($request->ids);
            if(!$curso->getOk()) throw new Exception($curso->getMsjError(), $curso->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $curso->getData()],200);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
}
