<?php

namespace App\Http\Controllers\api\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\AuthRequest;
use App\Http\Requests\auth\LoginRequest;
use App\Services\auth\AuthService;
use Illuminate\Http\Request;
use Exception;

class AuthController extends Controller
{

    protected $authService;

    public function  __construct()
    {
        $this->authService = new AuthService();
    }
    //
    public function register(AuthRequest $request)
    {
        try{
    
            $user = $this->authService->register($request);
    
            $token = $user->getData()->createToken('mobile')->plainTextToken;

            return response()->json(['ok' => true, 'data'=>$user->getData(),'token' => $token],$user->getCode());
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), $user->getCode()]);
        }
        
    }

    public function login(LoginRequest $request)
    {
        try{
    
            $user = $this->authService->login($request);
            if(!$user->getOk()) throw new Exception($user->getMsjError(), $user->getCode());
            $token = $user->getData()->createToken('mobile')->plainTextToken;

            return response()->json(['ok' => true, 'data'=>$user->getData(),'token' => $token],$user->getCode());
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), $user->getCode()]);
        }
    }

    public function logout(Request $request)
    {
        try{
    
            $request->user()->tokens()->delete();

            return response()->json(['ok' => true, 'data'=>"Sesion Cerrada"],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
        
    }

}
