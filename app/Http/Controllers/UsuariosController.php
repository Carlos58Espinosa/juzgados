<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuariosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usuario = \Auth::user();
        $id = $usuario->id;
        if($usuario->tipo == 'Administrador')
            $usuarios = User::all();
        else
            $usuarios = User::where('id', $usuario->id)->orWhere('usuarioId',$usuario->id)->get();
        $color = $this->getColorByUser();
        return view('usuarios.index',compact('usuarios', 'color', 'id'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if( url()->previous() != url()->current() ){
            session()->forget('urlBack');
            session(['urlBack' => url()->previous()]);
        }

        $tipos = $this->getArrayTypes();
        return view('usuarios.create', compact('tipos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if( url()->previous() != url()->current() ){
            session()->forget('urlBack');
            session(['urlBack' => url()->previous()]);
        }

        $this->validate($request, [
            'email' => 'required|unique:usuarios,email,',                       
            'nombre' => 'required',
            'tipo' => 'required',
        ], ['email.unique' => 'El email debe de ser único.']);

        $transaction = DB::transaction(function() use($request){
            $arr = $request->except('_token');
            $arr['password'] = Hash::make($request->password);
            if($arr['tipo'] == 'Empleado'){
                $usuario = \Auth::user();
                $arr['usuarioId'] = $usuario->id;
            }
            User::create($arr);
            $notification = array(
                  'message' => 'Registro Guardado.',
                  'alert-type' => 'success'
            );
            return $notification;
        });
        return redirect()->action('UsuariosController@index')->with($transaction);
        //return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if( url()->previous() != url()->current() ){
            session()->forget('urlBack');
            session(['urlBack' => url()->previous()]);
        }
        $usuario = User::findOrFail($id);
        return view('usuarios.show', compact('usuario'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if( url()->previous() != url()->current() ){
            session()->forget('urlBack');
            session(['urlBack' => url()->previous()]);
        }
        $usuario = User::findOrFail($id);
        return view('usuarios.edit', compact('usuario'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'email' => 'required|unique:usuarios,email,'.$id,            
            'nombre' => 'required'
        ], ['email.unique' => 'El email debe de ser único.']);

        return DB::transaction(function() use($request, $id){

            $usuario = User::findOrFail($id);
            $usuario->nombre = $request->nombre;
            $usuario->email = $request->email;
            if($request->password != "")
                $usuario->password = Hash::make($request->password);
            $band = $usuario->save();

            if ($band) {
                $notification = array(
                  'message' => 'Registro Guardado.',
                  'alert-type' => 'success'
                );
                return redirect()->action('UsuariosController@index')->with($notification);
            }else {
                $notification = array(
                  'message' => 'El Usuario no se pudo guardar.',
                  'alert-type' => 'error'
                );
                return back()->with($notification)->withInput($request->all());
            }
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->activo = 0;
        $usuario->save();
        return response()->json(200);
    }

    public function changeColorConfig(Request $request){
        $usuario = \Auth::user();
        $color_config = DB::table("color_config")->where('usuarioId', $usuario->id)->first();
        if($color_config != null){
            if($color_config->color != $request->valor)
                DB::select("update color_config set color=".$request->valor." where id=".$color_config->id);
        } else {
            $query = "insert into color_config values(default, ".$request->valor.", ".$usuario->id.");";
            DB::select($query);
        }
        $usuario_ctrl = new UsuariosController();
        $color = $usuario_ctrl->getColorByUser();
        \Session::put('color', $color);
        return $request->valor;
    }

    public function getColorByUser(){
        $usuario = \Auth::user();
        $color = 0;
        $color_config = DB::table("color_config")->where('usuarioId',$usuario->id)->first();
        $color = $color_config->color;
        return $color;
    }

    public function getArrayTypes(){
        $usuario = \Auth::user();
        $tipos = ['Empleado'];
        if($usuario->tipo == 'Administrador')
            $tipos = ['Administrador', 'Cliente', 'Empleado'];
        return $tipos;
    }
}
