<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request){
    try{
        $fields = $request->validate([
            'name' =>['required', 'string', 'max:255'],
            'email' =>['required', 'string', 'email', 'max:255',],
            'password' =>['required', 'string', 'min:8'],
        ]);
        $user = User::create($fields);
        $createToken = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'user'=>$user,
            'token'=>$createToken,
        ], 201);
    }catch(\Exception $e){
        return response()->json([
            'message'=>$e->getMessage(),
            'line'=>$e->getLine(),
        ], 500);
    }
    }
    public function login(Request $request){
        try{
            $fields = $request->validate([
                'email' =>['required', 'string', 'email', 'max:255',],
                'password' =>['required', 'string', 'min:8'],
            ]);
            if(!Auth::attempt($fields)){
                return response()->json([
                    'response_code'=>401,
                    'status'=>'error',
                    'message'=>"Unauthorized",
                ],401);
            }
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $createToken = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
            'response_code' => 200,
            'status' => 'success',
            'message' => 'Login successful',
            'user_info' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                // 'role'=>$user->role,
            ],
            'token' => $createToken,
            'token_type' => 'Bearer',
            ]);
        }catch(\Exception $e){
            return response()->json([
            'response_code' => 500,
            'status' => 'error',
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
        ], 500);
        }
    }
    public function Logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'response_code' => 200,
            'status' => 'success',
            'message' => 'Logout successful',
        ], 200);
    }
}
