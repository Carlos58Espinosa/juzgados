<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Configuracion;
use App\Models\ConfiguracionPlantilla;
use Validator;

class ConfiguracionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $res = [];

        switch ($request->option) {
            case 'templates_by_config_id':
                $res = ConfiguracionPlantilla::with(['plantilla' => function ($query) {
                    $query->select('id', 'nombre');
                }])->where('configuracionId', $request->configId)->orderBy('orden')->get();
                break;
            
            default:
                $configuraciones = DB::table('configuracion')->orderBy('nombre')->get();
                $res = view('configuracion.index',compact('configuraciones'));
                break;
        }
        
        return $res;        
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
        $plantillas = DB::table('plantillas')->select('id','nombre')->orderBy('nombre')->get();
        return view('configuracion.create', compact('plantillas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //return $request->all();
        //print_r($request->all());return false;
        $this->validate($request, [
            'nombre' => 'required|unique:configuracion',
        ], ['nombre.unique' => 'El nombre debe de ser único.', 'nombre.required', "este campo es requerido."]);
        try{
            $transaction = DB::transaction(function() use($request){
                $arrIds = explode(',',$request->old_ids[0]);
                $config = Configuracion::create(['nombre'=> $request->nombre]);
                $this->saveConfiguracionPlantillas($config, $arrIds);

                $notification = array(
                    'message' => 'Registro Guardado.',
                    'alert-type' => 'success'
                );
                return $notification;
            });
            return redirect()->action('ConfiguracionController@index')->with($transaction);
        } catch(\Exception $e){
            $cad = 'Oops! No se pudo guardar la Configuración.';
            $message = $e->getMessage();
            $pos = strpos($message, 'configuracion.nombre');            
            if ($pos != false) 
                $cad = "El nombre de la Configuración debe ser único.";
               
            $transaction = array(
                'message' => $cad,
                'alert-type' => 'error' 
            );
            return back()->with($transaction)->withInput($request->all());
        }
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
        $config = Configuracion::findOrFail($id);
        $config_plantillas = ConfiguracionPlantilla::with(['plantilla' => function ($query) {
            $query->select('id', 'nombre');
        }])->where('configuracionId', $id)->orderBy('orden')->get();
        return view('configuracion.show', compact('config','config_plantillas'));
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

        $plantillas_ids = [];
        $plantillas_orden = [];
        $old_ids[0] = "";

        $configuracion = Configuracion::findOrFail($id);
        $plantillas = DB::table('plantillas')->select('id','nombre')->orderBy('nombre')->get();
        $config_plantillas = ConfiguracionPlantilla::with(['plantilla' => function ($query) {
            $query->select('id', 'nombre');
        }])->where('configuracionId', $id)->orderBy('orden')->get();
        
        foreach($config_plantillas as $cp){
            array_push($plantillas_orden, $cp->plantilla);
            array_push($plantillas_ids, $cp->plantillaId);
            if($old_ids[0] != "")
                $old_ids[0] .= ",";
            $old_ids[0] .= $cp->plantillaId;
        }
        return view('configuracion.edit', compact('configuracion', 'plantillas', 'plantillas_ids', 
            'old_ids', 'plantillas_orden'));
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
            'nombre' => 'required|unique:configuracion,nombre,'.$id
        ]);
        $transaction = DB::transaction(function() use($request, $id){
            $configuracion = Configuracion::findOrFail($id);
            $configuracion->nombre = $request->nombre;
            $configuracion->save();
            ConfiguracionPlantilla::where('configuracionId', $id)->delete();
            $arrIds = explode(',',$request->old_ids[0]);
            $this->saveConfiguracionPlantillas($configuracion, $arrIds);
            if ($configuracion) {
                $notification = array(
                  'message' => 'Registro Guardado.',
                  'alert-type' => 'success'
                );
            }else {
                $notification = array(
                  'message' => 'Oops! there was an error, please try again later.',
                  'alert-type' => 'error'
                );
            }
            return $notification;
        });
        return redirect()->action('ConfiguracionController@index')->with($transaction);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return DB::transaction(function() use($id){
            if(DB::table("casos")->where('configuracionId',$id)->count() == 0){
                ConfiguracionPlantilla::where("configuracionId", $id)->delete();
                Configuracion::destroy($id);
                return response()->json(200);
            }else{
                //SOLO DESACTIVAR
                return "Desactivar";
            }
        }); 
    }

    public  function saveConfiguracionPlantillas($config, $arrIds){
        $orden = 1;
        foreach($arrIds as $plantillaId){
            ConfiguracionPlantilla::create(['plantillaId'=> $plantillaId, "orden" => $orden, "configuracionId" =>$config->id]);
            $orden += 1;
        }
    }
}
