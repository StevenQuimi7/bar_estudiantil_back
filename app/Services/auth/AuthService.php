<?php
namespace App\Services\auth;

use App\Models\User;
use App\Utils\Response;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response AS ResponseHttp;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(){}

    public function register($request){
        $response = new Response();
        try{
            $user = User::create([
                'username' => $request['username'],
                'nombres' => $request->filled('nombres') ?  $request['nombres'] : null,
                'apellidos' => $request->filled('apellidos') ? $request['apellidos'] : null,
                'email' => $request['email'],
                'password' => bcrypt($request['password'])
             ]);
             $response->setData($user);
             $response->setCode(200);
        }catch(Exception $e){
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $response->setOk(false);
            $response->setCode(ResponseHttp::HTTP_INTERNAL_SERVER_ERROR);
            $response->setMsjError($e->getMessage());
        }
        return $response;
    }

    public function login($request){
        $response = new Response();
        try{

            $user = User::where('username', $request->username)
                ->orWhere('email', $request->username)
                ->first();
            if($user && !$user->activo){
                $response->setCode(401);
                throw new Exception("El usuario no se encuentra habilitado en el sistema");
            }

            if (! $user || ! Hash::check($request->password, $user->password)) {
                $response->setCode(401);
                throw new Exception("credenciales incorrectas");
            }
             $response->setData($user);
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