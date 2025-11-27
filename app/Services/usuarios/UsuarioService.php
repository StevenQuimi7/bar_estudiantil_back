<?php
namespace App\Services\usuarios;

use App\Models\User;
use App\Utils\Response;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response AS ResponseHttp;

class UsuarioService
{
    public function __construct(){}

    public function index($request){
        log::info($request);
        $response = new Response();
        try{
            $per_page = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $activo = $request->input('estado', 1);
            $usuarios = User::where('activo',$activo)
            ->when($request->filled('username'), function ($query) use ($request) {
                return $query->where('username', 'like', '%' . $request->username . '%');
            })
            ->where("username","!=",'admin')
            ->with(['roles:id,name'])
            ->orderBy('created_at','desc')
            ->paginate($per_page, ['*'], 'page', $page);

            $response->setData($usuarios);
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }
    public function store($request){
        $response = new Response();
        try{
            $usuario = User::create([
                "nombres"      => $request->nombres,
                "apellidos"    => $request->apellidos,
                "email"        => $request->email,
                "username"     => $request->username,
                "password"     => bcrypt($request->password),
            ])->assignRole($request->rol_name);
            $response->setData($usuario);
            $response->setCode(201);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }
    public function update($id,$request){
        $response = new Response();
        try{
             $usuario = User::findOrFail($id);

            $usuario->nombres   = $request->nombres;
            $usuario->apellidos = $request->apellidos;
            $usuario->email     = $request->email;
            $usuario->save();

            if ($request->rol_name_base != $request->rol_name) {
                $usuario->syncRoles($request->rol_name);
            }
            $response->setData($usuario);
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }
    public function delete($ids){
        $response = new Response();
        try{
            User::whereIn('id', $ids)->update(['activo' => 0]);
            $response->setData("Eliminado exitosamente");
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }
    public function activarUsuario($id){
        $response = new Response();
        try{
            $usuario = User::findOrFail($id);
            $usuario->update(['activo' => 1]);
            $response->setData($usuario);
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }

}


?>