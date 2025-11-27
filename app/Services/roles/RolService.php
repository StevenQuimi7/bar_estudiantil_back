<?php
namespace App\Services\roles;

use App\Models\rol\Rol;
use App\Utils\Response;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response AS ResponseHttp;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolService
{
    public function __construct(){}

    public function index($request){
        $response = new Response();
        try{
            $per_page = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $roles = Role::where("activo",1)
            ->where("name","!=",'administrador')
            ->when($request->filled('name'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })
            ->with('permissions')
            ->orderBy('created_at','desc')
            ->paginate($per_page, ['*'], 'page', $page);

            $response->setData($roles);
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }
     public function comboRoles(){
        $response = new Response();
        try{
            $roles = Role::select('id as value','name as label')
            ->where("activo",1)
            ->where("name","!=",'administrador')
            ->get(); 
            $response->setData($roles);
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
            $rol = Role::create([
                "name"                => strtoupper(trim($request->name)),
                "guard_name"          => 'web',
                // "id_usuario_creacion" => getUsuarioAutenticado()->id
            ]);
            if(!empty($request->selectedPermisos)){
                $rol->syncPermissions($request->selectedPermisos);
            }
            $response->setData("Creado exitosamente");
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
            $rol = Role::find($id);
            if($request->name != $rol->name){
                $rol->name = strtoupper(trim($request->name));
                $rol->save();
            }
            if(count($request->selectedPermisosBase) != count($request->selectedPermisos)){
                $rol->syncPermissions($request->selectedPermisos);
            }
            $response->setData("Actualizado exitosamente");
            $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }
    public function delete($id){
        $response = new Response();
        try{
            Role::whereIn('id', $id)->where('name', '!=', 'administrador')->update([
                "activo" => 0
            ]);
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

    public function listadoPermisos(){
        $response = new Response();
        try{
            $excep = ['usuario', 'usuario.store', 'usuario.update', 'usuario.delete'];
            $roleAutenticado = Auth::user()->getRoleNames()->first();
            $rolesPermitidos = ["administrador", "operador"];

            if (in_array($roleAutenticado, $rolesPermitidos)) {
                $permisos = Permission::all();
            } else {
                $permisos = Permission::all();
                $permisos = Permission::whereNotIn('name', $excep)->get();
            }

            $response->setData($permisos);
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