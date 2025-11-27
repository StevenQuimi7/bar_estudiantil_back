<?php

namespace App\Http\Controllers\api\roles;

use App\Http\Controllers\Controller;
use App\Http\Requests\roles\RolStoreRequest;
use App\Http\Requests\roles\RolUpdateRequest;
use App\Services\roles\RolService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class RolController extends Controller
{

    protected $_rolService;
    public function __construct(){
        // $this->middleware('can:roles.index')->only('index');
        // $this->middleware('can:roles.store')->only('store');
        // $this->middleware('can:roles.update')->only('update');
        // $this->middleware('can:roles.destroy')->only('destroy');
        $this->_rolService = new RolService();
    }

    public function index(Request $request)
    {
        try{
            $roles = $this->_rolService->index($request);
            if(!$roles->getOk()) throw new Exception($roles->getMsjError(), $roles->getCode());
            return response()->json(['ok' => true, 'data' => $roles->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
    public function comboRoles()
    {
        try{
            $roles = $this->_rolService->comboRoles();
            if(!$roles->getOk()) throw new Exception($roles->getMsjError(), $roles->getCode());
            return response()->json(['ok' => true, 'data' => $roles->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function store(RolStoreRequest $request)
    {
        $validator = $request->validated();
        try{
            DB::beginTransaction();
            $rol = $this->_rolService->store($request);
            if(!$rol->getOk()) throw new Exception($rol->getMsjError(), $rol->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $rol->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function update(RolUpdateRequest $request, string $id)
    {
        $validator = $request->validated();
        try{
            DB::beginTransaction();
            $rol = $this->_rolService->update($id, $request);
            if(!$rol->getOk()) throw new Exception($rol->getMsjError(), $rol->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $rol->getData()],200);
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
            $rol = $this->_rolService->delete($request->ids);
            if(!$rol->getOk()) throw new Exception($rol->getMsjError(), $rol->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $rol->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
    public function listadoPermisos()
    {
        try{
            DB::beginTransaction();
            $permisos = $this->_rolService->listadoPermisos();
            if(!$permisos->getOk()) throw new Exception($permisos->getMsjError(), $permisos->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $permisos->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
}
