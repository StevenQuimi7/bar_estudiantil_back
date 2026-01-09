<?php

namespace App\Http\Controllers\api\usuarios;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\AuthRequest;
use App\Http\Requests\usuarios\UsuarioUpdateRequest;
use App\Services\usuarios\UsuarioService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{

    protected $_usuarioService;
    public function __construct(){
        $this->_usuarioService = new UsuarioService();
    }

    public function index(Request $request)
    {
        try{
            $usuarios = $this->_usuarioService->index($request);
            if(!$usuarios->getOk()) throw new Exception($usuarios->getMsjError(), $usuarios->getCode());
            return response()->json(['ok' => true, 'data' => $usuarios->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function store(AuthRequest $request)
    {
        try{
            DB::beginTransaction();
            $usuario = $this->_usuarioService->store($request);
            if(!$usuario->getOk()) throw new Exception($usuario->getMsjError(), $usuario->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $usuario->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function update(UsuarioUpdateRequest $request, string $id)
    {
        try{
            DB::beginTransaction();
            $usuario = $this->_usuarioService->update($id, $request);
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
            $usuario = $this->_usuarioService->delete($request->ids);
            if(!$usuario->getOk()) throw new Exception($usuario->getMsjError(), $usuario->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $usuario->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
    public function activarUsuario($id )
    {
        try{
            DB::beginTransaction();
            $usuario = $this->_usuarioService->activarUsuario($id);
            if(!$usuario->getOk()) throw new Exception($usuario->getMsjError(), $usuario->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $usuario->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function perfilUsuario(Request $request)
    {
        try{
            $usuarios = $this->_usuarioService->perfilUsuario($request);
            if(!$usuarios->getOk()) throw new Exception($usuarios->getMsjError(), $usuarios->getCode());
            return response()->json(['ok' => true, 'data' => $usuarios->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

}
