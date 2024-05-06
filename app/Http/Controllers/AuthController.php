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
            $user->fill($request->all() + [
                'type' => User::TYPE_USER
                ]);
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json($user, 200);
        }catch (\Exception $e){
            return response()->json($e->getMessage(), 500);
        }
    }
    public function login(Request $request){
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

    public function update(Request $request){
        try {
            $userLogin = $request->user();
            $password = $request->get('password');
            $data = [
                'email' => $request->get('email') ?? $userLogin->email,
                'name' => $request->get('name') ?? $userLogin->name,
                'phone_number' => $request->get('phone_number') ?? "",
                'address' => $request->get('address') ?? "",
                'date_birth' => $request->get('date_birth') ?? $userLogin->date_birth,
                'type' => User::TYPE_USER
            ];
            if(!is_null($password) || $password !== ""){
                $data['password'] = Hash::make($password);
            }
            User::where('id', $userLogin->id)->update($data);
            return response()->json([
                "success" => true,
            ]);
        }catch (\Exception $e){
            return response()->json($e->getMessage(), 500);

        }
    }

    public function getListUser(Request $request){
        try {
            $name = $request->get('name') ?? '';
            $users = User::where('name','like','%'.$name.'%')->get();
            return response()->json([
                "success" => true,
                'data' => $users
            ]);
        }catch (\Exception $e){
            return response()->json($e->getMessage(), 500);
        }
    }

}
