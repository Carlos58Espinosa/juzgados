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

        $notification = array(
          'message' => 'Usuario o contraseña incorrectos.',
          'alert-type' => 'error'
        );

        $mensaje = "";

        $usuario = User::where('email', $request->email)->first();
        if($usuario != null){
            if($usuario->activo == 0)
                $mensaje = 'Usuario desactivado.';
        }else
            $mensaje = "Usuario no encontrado.";

        if($mensaje != "") { 
            $notification['message'] = $mensaje;      
            return redirect('/')->with($notification);
        }

        if(\Auth::attempt($request->only('email', 'password'))){
            $user = \Auth::user();
            $user->createToken('myApp')->plainTextToken;
            return redirect('/principal');
        }else
            return redirect('/')->with($notification);
    }

    public function logout(Request $request){
        $user = \Auth::user();
        $user->tokens()->delete();
        \Auth::logout();
        return redirect('/');
    }
}
