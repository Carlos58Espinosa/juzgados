<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Caso;
use App\Models\CasosValores;
use App\Models\CasosPlantillas;
use App\Models\Plantilla;
use App\Models\ConfiguracionPlantilla;
use App\Models\CasoPlantillaCampo;

class CasosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $casos = Caso::with(['configuracion' => function ($query) {
            $query->select('id', 'nombre');
        }, 
        'etapa_plantilla' => function ($query) {
            $query->select('id', 'nombre');
        }])->orderBy('created_at')->get();
        return view('casos.index',compact('casos'));
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
        $configuraciones = DB::table('configuracion')->select('id','nombre')->orderBy('nombre')->get();
        foreach($configuraciones as $config){
            $config->dataBank = $this->getAllFieldsTemplatesByConfigId($config->id, null);
            $config->plantillaInfo = $this->getInitialTemplateByConfigId($config->id);
        }
        return view('casos.create', compact('configuraciones'));
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
        $transaction = DB::transaction(function() use($request){
            $caso = Caso::create(["nombre_cliente" => $request->nombre_cliente, "configuracionId" => $request->configuracion_id, "etapaPlantillaId" => $request->plantilla_id]);

            $arr = $request->all();
            $banco_datos = $this->getAllFieldsTemplatesByConfigId($request->configuracion_id, $caso->id);
            $this->saveTemplateDataBank($banco_datos, $arr, $request->plantilla_id, $caso->id, 1,false);
            CasosPlantillas::create(["texto" => $request->texto, "plantillaId" => $request->plantilla_id, "casoId" => $caso->id]);

            $this->saveCasoPlantillaCampo($caso->id, $request->plantilla_id, $request->texto);

            $notification = array(
                  'message' => 'Registro Guardado.',
                  'alert-type' => 'success'
            );
            return $notification;
        });
        return redirect()->action('CasosController@index')->with($transaction);
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
        $separador = '|';
        $caso = Caso::findOrFail($id);
        $plantillas = CasosPlantillas::with(['plantilla'=> function ($query) {
            $query->select('id', 'nombre');
        }])->where("casoId",$id)->select('casoId','plantillaId')->get();

        /*
        $caso = Caso::with('valores')->findOrFail($id);
        $plantillas = CasosPlantillas::with(['plantilla_campos',
            'plantilla'=> function ($query) {
            $query->select('id', 'nombre');
        }])->where("casoId",$id)->get();
        foreach($plantillas  as &$plantilla){
            foreach($plantilla->plantilla_campos as $campo){
                $valor = "";
                $sensible = 0;
                foreach($caso->valores as $casoV){
                    if($campo->nombre == $casoV->campo){
                        $valor = $casoV->valor;
                        $sensible = $casoV->sensible;
                        if($plantilla->id == $casoV->plantillaId)
                            break;
                    }
                }
                if($valor != ""){
                    if($sensible == 1)
                        $valor = '<span style="background-color: #ffffff;"><font color="#ffffff">'.$valor.'</font></span>';
                    $plantilla->texto = str_replace($separador.$campo->nombre.$separador, $valor, $plantilla->texto);
                }
            }
        }*/
        return view('casos.show', compact('caso', 'plantillas'));
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

        $plantillas_ctrl = new PlantillasController();

        $casoAux = Caso::with(['configuracion'  => function ($query) {
            $query->select('id', 'nombre');
        }, 'valores', 'plantillas', 'campos'])->findOrFail($id);

        $plantillas = ConfiguracionPlantilla::with(['plantilla' => function ($query) {
            $query->select('id', 'nombre', 'texto');
        }, 'plantilla_campos' => function ($query) {
            $query->select('plantillaId', 'nombre');
        }])->where('configuracionId', $casoAux->configuracionId)->select('id', 'plantillaId', 'orden')->orderBy('orden')->get();

        foreach($plantillas as &$plantilla){
            foreach($casoAux->plantillas as $casoAuxPlantilla){
                if($plantilla->plantillaId == $casoAuxPlantilla->plantillaId) {
                    $plantilla->plantilla->texto = $casoAuxPlantilla->texto;
                    unset($plantilla->plantilla_campos);
                    $plantilla->plantilla_campos = [];
                    $arr_c = [];
                    $campos = $plantillas_ctrl->getTemplateFields($casoAuxPlantilla->texto, env('SEPARADOR')); 
                    foreach($campos as $c)
                        array_push($arr_c, (object)["nombre" => $c]);
                    $plantilla->plantilla_campos = $arr_c;
                }
            }
            foreach($plantilla->plantilla_campos as $campo){
                $valor = ""; $sensible = 0;
                $this->searchValueAndSensible($casoAux->valores, $campo->nombre, $plantilla->plantillaId, $valor, $sensible, $plantilla->orden);
                $campo->valor = $valor;
                $campo->sensible = $sensible;
            }
        }
//        return $plantillas;
        $caso = (object)['id' => $casoAux->id, "nombre_cliente" => $casoAux->nombre_cliente, "configuracion" => $casoAux->configuracion];
        return view('casos.edit', compact('caso', 'plantillas'));
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
        //return $request->all();
        $transaction = DB::transaction(function() use($request, $id){
            $caso = Caso::findOrFail($id);
            $caso->nombre_cliente = $request->nombre_cliente;
            $caso->etapaPlantillaId = $request->plantilla_id;
            $caso->save();

            $arr = $request->all();
            $banco_datos = $this->getFieldsByText($request->texto);

            $this->saveTemplateDataBank($banco_datos, $arr, $request->plantilla_id, $id, $request->orden, true);
            $caso_plantilla = CasosPlantillas::where('casoId', $id)->where('plantillaId', $request->plantilla_id)->first();
            if($caso_plantilla != null){
                $caso_plantilla->texto = $request->texto;
                $caso_plantilla->save();
            }else{
                CasosPlantillas::create(["texto" => $request->texto, "plantillaId" => $request->plantilla_id, "casoId" => $id]);
            }

            $this->saveCasoPlantillaCampo($caso->id, $request->plantilla_id, $request->texto);

            $notification = array(
                  'message' => 'Registro Guardado.',
                  'alert-type' => 'success'
            );
            return $notification;            
        });
        return redirect()->action('CasosController@index')->with($transaction);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        CasosPlantillas::where('casoId', $id)->delete();
        CasosValores::where('casoId', $id)->delete();
        CasoPlantillaCampo::where('casoId', $id)->delete();
        Caso::destroy($id);
        return response()->json(200);
    }

    public function getFieldsByText($texto){
        $res = [];
        $plantillas_ctrl = new PlantillasController();
        $campos = $plantillas_ctrl->getTemplateFields($texto, env('SEPARADOR'));
        foreach($campos as $c)
            array_push($res, (object)["nombre" => $c]);
        return $res;
    }

    public function getAllFieldsTemplatesByConfigId($configId, $casoId){
        $query = "select * from (
        select pc.nombre 
        from configuracion_plantillas cp, plantillas_campos pc
        where cp.configuracionId =".$configId."  and pc.plantillaId = cp.plantillaId ";
        if($casoId != null){
            $query .= " and cp.plantillaId not in (select plantillaId from casos_plantillas_campos where casoId=".$casoId.") union select campo as nombre from casos_plantillas_campos where casoId=62";        
        }
        $query .= ") as t1 group by t1.nombre order by t1.nombre;";
        return DB::select($query);
    }

    public function getInitialTemplateByConfigId($configId){
        $query = "select cp.plantillaId, p.nombre, p.texto from configuracion_plantillas cp, plantillas p where cp.configuracionId = ".$configId." and p.id = cp.plantillaId and cp.orden = 1 order by cp.orden;";
        return DB::select($query)[0];
    }

    public function getDataBank(Request $request){
        if( url()->previous() != url()->current() ){
            session()->forget('urlBack');
            session(['urlBack' => url()->previous()]);
        }
        $caso = Caso::findOrFail($request->caso_id);
        $config_plantilla = $this->getInitialTemplateByConfigId($caso->configuracionId);
        $plantillaId = $config_plantilla->plantillaId;

        //Nota si el dato de un campo cambia en otra plantilla, SOLO SE MUESTRA EL DATO INICIAL.
        $query = "select *,
        (select valor from casos_valores where casoId=".$caso->id." and campo=t1.nombre and valor !='' order by orden limit 1) as valor,
        (select sensible from casos_valores where casoId=".$caso->id." and campo=t1.nombre order by orden limit 1) as sensible 
        from (
        select campo as nombre from casos_plantillas_campos where casoId = ".$caso->id."
        union
        select pc.nombre
        from configuracion_plantillas cp, plantillas_campos pc
        where cp.configuracionId=".$caso->configuracionId." and cp.plantillaId = pc.plantillaId
        and pc.plantillaId not in (select plantillaId from casos_plantillas_campos where casoId = ".$caso->id.")
        ) as t1 order by t1.nombre;";


        /*$query = "select *, 
        (select valor from casos_valores where casoId=".$caso->id." and campo=t1.nombre order by id desc limit 1) as valor,
        (select sensible from casos_valores where casoId=".$caso->id." and campo=t1.nombre order by id desc limit 1) as sensible  
        from (
        select pc.nombre 
        from configuracion_plantillas cp, plantillas_campos pc
        where cp.configuracionId = ".$caso->configuracionId." and pc.plantillaId = cp.plantillaId
        group by pc.nombre order by nombre) as t1;";*/
        $campos = DB::select($query);
        return view('casos.bank_data', compact('campos', 'caso', 'plantillaId'));
    }

    public function saveTemplateDataBank($banco_datos, $arr, $plantillaId, $casoId, $orden,$eliminacion=false){
        foreach($banco_datos as $campo){
            if($eliminacion)
                CasosValores::where('campo', $campo->nombre)->where('casoId', $casoId)->where('plantillaId', $plantillaId)->delete();
    
            $key_field = str_replace(' ', '_', $campo->nombre);
            if (array_key_exists($key_field,$arr)) {                
                $valor = "";
                $valor_sensible = false;
                if($arr[$key_field] != null)
                    $valor = $arr[$key_field];
                if (array_key_exists($key_field."_check",$arr)) {
                    if($arr[$key_field."_check"] == "on")
                       $valor_sensible = true; 
                }
                CasosValores::create(["valor" => $valor, "sensible" => $valor_sensible, "casoId" => $casoId, "campo" => $campo->nombre, "plantillaId" => $plantillaId, "orden" => $orden]);

                /*CasosValores::where('casoId', $casoId)->where("valor", "")->update(["valor" => $valor, "sensible" => $valor_sensible]);*/
            }
        }
    }

    public function saveDataBank(Request $request){
        //return $request->all();
        $transaction = DB::transaction(function() use($request){
            CasosValores::where('casoId',$request->caso_id)->where('plantillaId',$request->plantilla_id)->delete();
            $banco_datos = $this->getAllFieldsTemplatesByConfigId($request->configuracion_id, $request->caso_id);
            $this->saveTemplateDataBank($banco_datos, $request->all(), $request->plantilla_id, $request->caso_id, 1);
            $notification = array(
                  'message' => 'Registro Guardado.',
                  'alert-type' => 'success'
            );
            return $notification;
        });
        return redirect()->action('CasosController@index')->with($transaction);
    }   

    public function viewCasosPdf(Request $request) {
        $plantilla = CasosPlantillas::with(['plantilla_campos'])
        ->where("casoId", $request->caso_id)
        ->where("plantillaId", $request->plantilla_id)->first();
        $caso = Caso::with('valores')->findOrFail($request->caso_id);
        $config_plantilla = ConfiguracionPlantilla::where("configuracionId", $caso->configuracionId)->where("plantillaId", $request->plantilla_id)->first();

        $res = $plantilla->texto;
        foreach($plantilla->plantilla_campos as $campo){
            $valor = "";
            $sensible = 0;
            $this->searchValueAndSensible($caso->valores, $campo->nombre, $plantilla->plantillaId, $valor, $sensible, $config_plantilla->orden);
            $res = $this->replaceValueAndSensibleInText($res, $valor, $sensible, $campo->nombre);
        }

        $estado = "slp";
        $view = view('pdfs.archivo', compact('res', 'estado'));
        $view = preg_replace('/>\s+</', '><', $view);
        $pdf = \PDF::loadHTML($view);
        return $pdf->stream();
    } 

    public function searchValueAndSensible($valores, $campoNombre, $plantillaId, &$valor, &$sensible, $orden){
        $valoresAux = [];

        foreach($valores as $casoV){ 
            if($campoNombre == $casoV->campo && $casoV->orden <= $orden){
                if($plantillaId == $casoV->plantillaId){
                    if($casoV->valor != ""){
                        $valor = $casoV->valor;
                        $sensible = $casoV->sensible;
                    }
                    break;
                }else{
                    $valor = $casoV->valor;
                    $sensible = $casoV->sensible;  

                }
            }
        }
    }

    public function replaceValueAndSensibleInText($res, $valor, $sensible, $campoNombre){
        $separador = "|";
        if($valor != ""){
            $valorAux = $valor;
            if($sensible == 1){
                $valorAux = "";
                for($pos = 0; $pos < strlen($valor); $pos++)
                    $valorAux .= "*";
                //$valor = '<span style="background-color: #ffffff;"><font color="#ffffff">'.$valor.'</font></span>';
            }
            $res = str_replace($separador.$campoNombre.$separador, $valorAux, $res);
        }
        return $res;
    }

    public function saveCasoPlantillaCampo($casoId, $plantillaId, $texto) {
        $plantillas_ctrl = new PlantillasController();
        $campos = $plantillas_ctrl->getTemplateFields($texto, env('SEPARADOR')); 

        CasoPlantillaCampo::where("plantillaId", $plantillaId)->where("casoId", $casoId)->delete();
        foreach($campos as $campo)
            CasoPlantillaCampo::create(["campo" => $campo, "plantillaId" => $plantillaId, "casoId" => $casoId]);  
    }
}
