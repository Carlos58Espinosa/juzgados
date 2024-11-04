<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class AuthController extends Controller
{
    public function login(Request $request){
         $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $usuario = User::where('email', $request->email)->first();
        if($usuario != null){
            if($usuario->activo == 0){
                $notification = array(
                  'message' => 'Usuario desactivado.',
                  'alert-type' => 'success'
                );
                return redirect('/')->with($notification);
            }
        }

        if(\Auth::attempt($request->only('email', 'password'))){
            $user = \Auth::user();
            $user->createToken('myApp')->plainTextToken;
            return redirect('/principal');
        }else
            return redirect('/');
    }

    public function logout(Request $request){
        $user = \Auth::user();
        $user->tokens()->delete();
        \Auth::logout();
        return redirect('/');
    }
}
