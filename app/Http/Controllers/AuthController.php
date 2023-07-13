<?php

namespace App\Http\Controllers;

use App\Helper\ResponseHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);

    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "email"=> "required|email",
            "password" => "required",

        ]);

        if($validator->fails()){
            $response = ResponseHelper::convertValidation($validator->errors()->getMessages());
            return response()->json($response,400);
        }

        $credentials = $request->only('email', 'password');

        if(! $token = auth()->attempt($credentials) ){
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        return response()->json(["status" => "success","token" => $token]);

    }

    public function register(Request $request){

        $validator = Validator::make($request->all(),[
            "name" => "required",
            "email"=> "required|unique:users|email",
            "password" => "required|confirmed",
        ]);

        if($validator->fails()){

            $response = ResponseHelper::convertValidation($validator->errors()->getMessages());
            return response()->json($response,400);
        }


        $user = new User($request->all());
        if($user->save()){
            return response()->json(["message" => "Usuario salvo", "data" => $user->name],200);
        }

        return response()->json(["message"=> "Erro ao salvar"],500);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Deslogado com sucesso',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorization' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

}
