<?php

namespace App\Http\Controllers\api\categorias;

use App\Http\Controllers\Controller;
use App\Http\Requests\categorias\CategoriaStoreRequest;
use App\Http\Requests\categorias\CategoriaUpdateRequest;
use App\Services\categorias\CategoriaService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoriaController extends Controller
{

    protected $_categoriaService;
    public function __construct(){
        // $this->middleware('can:categorias.index')->only('index');
        // $this->middleware('can:categorias.comboCategorias')->only('comboCategorias');
        // $this->middleware('can:categorias.store')->only('store');
        // $this->middleware('can:categorias.update')->only('update');
        // $this->middleware('can:categorias.destroy')->only('destroy');
        $this->_categoriaService = new CategoriaService();
    }

    public function index(Request $request)
    {
        try{
            $categorias = $this->_categoriaService->index($request);
            if(!$categorias->getOk()) throw new Exception($categorias->getMsjError(), $categorias->getCode());
            return response()->json(['ok' => true, 'data' => $categorias->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
    public function comboCategorias()
    {
        try{
            $categorias = $this->_categoriaService->comboCategorias();
            if(!$categorias->getOk()) throw new Exception($categorias->getMsjError(), $categorias->getCode());
            return response()->json(['ok' => true, 'data' => $categorias->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function store(CategoriaStoreRequest $request)
    {
        $validator = $request->validated();
        try{
            DB::beginTransaction();
            $categoria = $this->_categoriaService->store($request);
            if(!$categoria->getOk()) throw new Exception($categoria->getMsjError(), $categoria->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $categoria->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function update(CategoriaUpdateRequest $request, string $id)
    {
        $validator = $request->validated();
        try{
            DB::beginTransaction();
            $categoria = $this->_categoriaService->update($id, $request);
            if(!$categoria->getOk()) throw new Exception($categoria->getMsjError(), $categoria->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $categoria->getData()],200);
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
            $categoria = $this->_categoriaService->delete($request->ids);
            if(!$categoria->getOk()) throw new Exception($categoria->getMsjError(), $categoria->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $categoria->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
}
