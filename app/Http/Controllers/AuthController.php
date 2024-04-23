<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        try {
            $user = new User();
            $user->fill($request->all());
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json($user, 200);
        }catch (\Exception $e){
            return response()->json($e->getMessage(), 500);
        }
    }
    public function login(RegisterRequest $request){
        try {
            if(Auth::attempt([
                'email' => $request->email,
                'password' => $request->password
            ])){
                $user = User::whereEmail($request->email)->first();
                $user->token = $user->createToken('AppAuth')->accessToken;
            }
            return response()->json($user, 200);
        }catch (\Exception $e){
            return response()->json($e->getMessage(), 500);
        }
    }
    //
}
