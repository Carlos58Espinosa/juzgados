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
use App\Models\CasosCamposSensibles;

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
            $config->campos = $this->getFieldsFirstTemplateByConfigurationId($config->id);
            $config->plantillaInfo = $this->getInitialTemplateByConfigurationId($config->id);
        }
        //return $configuraciones;
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
        $transaction = DB::transaction(function() use($request){
            $caso = Caso::create(["nombre_cliente" => $request->nombre_cliente, "configuracionId" => $request->configuracion_id, "etapaPlantillaId" => $request->plantilla_id]);

            $arr = $request->all();
            $plantillas_ctrl = new PlantillasController();
            $campos = $plantillas_ctrl->getTemplateFields($request->texto, env('SEPARADOR'));
            $this->saveDataBankByTemplateIdAndCaseId($campos, $arr, $request->plantilla_id, $caso->id, 1,false);

            CasosPlantillas::create(["texto" => $request->texto, "plantillaId" => $request->plantilla_id, "casoId" => $caso->id]);

            $this->saveFieldsByTemplateIdByCaseId($caso->id, $request->plantilla_id, $campos);

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

        $caso = Caso::with(['configuracion' => function ($query) {
            $query->select('id', 'nombre');
        }, 'plantillas'])->findOrFail($id);

        $campos_valores = $this->getAllDataBankByCasoId($caso->id, $caso->configuracionId);

        $plantillas = ConfiguracionPlantilla::with(['plantilla' => function ($query) {
            $query->select('id', 'nombre', 'texto');
        }])->where('configuracionId', $caso->configuracionId)->select('id', 'plantillaId', 'orden')->orderBy('orden')->get();

        $arr_plantillas = json_decode(json_encode($caso->plantillas), true);

        foreach($plantillas as &$plantilla){
            $plantilla->campos_valores = array_values(array_filter($campos_valores, function($iter) use ($plantilla){
                   if($iter->plantillaId == $plantilla->plantillaId)
                        return $iter;
                }));

            $caso_plantilla = array_values(array_filter($arr_plantillas, function($iter) use ($plantilla){
                   if($iter['plantillaId'] == $plantilla->plantillaId)
                        return $iter;
                }));

            if(count($caso_plantilla))
                $plantilla->plantilla->texto = $caso_plantilla[0]['texto'];
        }
        //return $plantillas;
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

    /**** Obtiene los campos y valores para el botón de Banco de Datos ****************/
    public function getDataBank(Request $request){
        if( url()->previous() != url()->current() ){
            session()->forget('urlBack');
            session(['urlBack' => url()->previous()]);
        }

        $caso = Caso::with(['configuracion'  => function ($query) {
            $query->select('id', 'nombre');
        }])->findOrFail($request->caso_id);

        $campos = $this->getAllFieldsSensibleTemplatesByConfigId($caso->configuracionId, $caso->id);

        $campos_valores = $this->getAllDataBankByCasoId($caso->id, $caso->configuracionId);

        $plantillas = ConfiguracionPlantilla::with(['plantilla' => function ($query) {
            $query->select('id', 'nombre');
        }])->where("configuracionId", $caso->configuracionId)->select('id', 'plantillaId', 'orden')->orderBy("orden")->get();

        foreach($plantillas as &$plantilla){
            $plantilla->campos_valores = array_values(array_filter($campos_valores, function($iter) use ($plantilla){
               if($iter->plantillaId == $plantilla->plantillaId)
                    return $iter;
            }));
        }
        //return $plantillas;
        return view('casos.bank_data', compact('plantillas', 'caso', 'campos'));
    }

    public function getAllDataBankByCasoId($casoId, $configuracionId){
        $query = "select *, (select valor from casos_valores where casoId = ".$casoId." and campo = t1.campo and plantillaId = t1.plantillaId) as valor_plantilla,
            (select valor from casos_valores where casoId = ".$casoId." and campo = t1.campo and orden <=t1.orden and valor is not null and valor != '' order by orden limit 1) as valor_ultimo
                from (
                select cpc.campo, cpc.plantillaId, cp.orden
                from configuracion_plantillas cp, casos_plantillas_campos cpc
                where cp.configuracionId = ".$configuracionId." 
                and cpc.casoId = ".$casoId." and cpc.plantillaId = cp.plantillaId
                union
                select pc.campo, pc.plantillaId, cp.orden
                from configuracion_plantillas cp, plantillas_campos pc
                where cp.configuracionId = ".$configuracionId." and cp.plantillaId = pc.plantillaId
                and  pc.plantillaId not in (select plantillaId from casos_plantillas_campos where casoId = ".$casoId.")
                )  as t1 order by t1.orden, t1.campo;";
        return DB::select($query);
    }

    public function getAllFieldsTemplatesByConfigId($configId, $casoId){
        $query = "select * from (
        select pc.campo 
        from configuracion_plantillas cp, plantillas_campos pc
        where cp.configuracionId =".$configId."  and pc.plantillaId = cp.plantillaId ";
        if($casoId != null){
            $query .= " and cp.plantillaId not in (select plantillaId from casos_plantillas_campos where casoId=".$casoId.") union select campo from casos_plantillas_campos where casoId=".$casoId;        
        }
        $query .= ") as t1 group by t1.campo order by t1.campo;";
        $campos_plantilla = DB::select($query);
        $arr_campos = [];
        array_filter($campos_plantilla, function($iter) use(&$arr_campos){
           array_push($arr_campos, $iter->campo);
        });
        return $arr_campos;
    }

    public function getFieldsByTemplateIdAndCaseId($casoId, $templateId){
        $query = "select campo 
                from casos_plantillas_campos 
                where plantillaId = ".$templateId." and casoId = ".$casoId." 
                union
                select campo 
                from plantillas_campos 
                where plantillaId = ".$templateId." and plantillaId not in 
                (select plantillaId from casos_plantillas_campos where casoId = ".$casoId.")";
        $campos_plantilla = DB::select($query);
        $arr_campos = [];
        array_filter($campos_plantilla, function($iter) use(&$arr_campos){
           array_push($arr_campos, $iter->campo);
        });
        return $arr_campos;
    }

    public function getAllFieldsSensibleTemplatesByConfigId($configId, $casoId){
        $query = "select *, (select sensible from casos_campos_sensibles where campo = t1.campo and casoId = ".$casoId.") as sensible  
        from (
        select pc.campo 
        from configuracion_plantillas cp, plantillas_campos pc
        where cp.configuracionId =".$configId."  and pc.plantillaId = cp.plantillaId ";
        if($casoId != null){
            $query .= " and cp.plantillaId not in (select plantillaId from casos_plantillas_campos where casoId=".$casoId.") union select campo from casos_plantillas_campos where casoId=".$casoId;        
        }
        $query .= ") as t1 group by t1.campo order by t1.campo;";
        return DB::select($query);
    }

    public function saveDataBank(Request $request){
        //return $request->all();
        $transaction = DB::transaction(function() use($request){
            //Guarda el Banco de Datos de una sola Plantilla
            if($request->all()['plantilla_id'] != null){
               $campos_plantilla = $this->getFieldsByTemplateIdAndCaseId($request->casoId, $request->plantilla_id);
                $this->saveDataBankByTemplateIdAndCaseId($campos_plantilla, $request->all(), $request->plantilla_id, $request->casoId, $request->orden, true);
                
                //Guarda Nuevos Campos
                if($request->nuevos_campos_cad != ""){
                    $nuevos_campos = explode('.', $request->nuevos_campos_cad);
                    if(count($nuevos_campos) > 0){
                       $this->saveFieldsByTemplateIdByCaseId($request->casoId, $request->plantilla_id, $nuevos_campos, false);
                       $this->saveDataBankByTemplateIdAndCaseId($nuevos_campos, $request->all(), $request->plantilla_id, $request->casoId, $request->orden, false);
                    }
                }
            }

            //Guarda los valores sensibles
            $banco_datos = $this->getAllFieldsTemplatesByConfigId($request->configuracionId, $request->casoId);
            $this->saveDataSensibleByCasoId($banco_datos, $request->all(), $request->casoId);


            
            $notification = array(
                  'message' => 'Registro Guardado.',
                  'alert-type' => 'success'
            );
            return $notification;
        });
        return redirect()->action('CasosController@index')->with($transaction);
    } 

    public function saveDataSensibleByCasoId($campos, $arr, $casoId){
        CasosCamposSensibles::where("casoId", $casoId)->delete();
        foreach($campos as $c){
            $key_field = str_replace(' ', '_', $c);
            if (array_key_exists($key_field."_check",$arr)) {
                if($arr[$key_field."_check"] == "on"){
                    CasosCamposSensibles::create(["casoId" => $casoId, "campo" => $c, "sensible" => true]);
                }
            }
        }
    } 


    /************************************************************************/

    /***** Función usada en el CREATE, se usa para traer los datos de una plantilla ****/
    public function getFieldsFirstTemplateByConfigurationId($configuracionId){
        $query = "select pc.campo 
                from configuracion_plantillas cp, plantillas_campos pc
                where cp.configuracionId = ".$configuracionId." and pc.plantillaId = cp.plantillaId and cp.orden = 1 order by pc.campo;";
        return DB::select($query);
    }

    public function getInitialTemplateByConfigurationId($configId){
        $query = "select cp.plantillaId, p.nombre, p.texto from configuracion_plantillas cp, plantillas p where cp.configuracionId = ".$configId." and p.id = cp.plantillaId and cp.orden = 1 order by cp.orden;";
        return DB::select($query)[0];
    }

    /************************************************************************/

    /***** Función usada en el STORE, se usa para traer los campos de un texto ****/
   /* public function getFieldsByText($texto){
        $res = [];
        $plantillas_ctrl = new PlantillasController();
        $campos = $plantillas_ctrl->getTemplateFields($texto, env('SEPARADOR'));
        foreach($campos as $c)
            array_push($res, (object)["campo" => $c]);
        return $res;
    }*/

    //Guarda le valor de campo por plantilla
    public function saveDataBankByTemplateIdAndCaseId($campos, $arr, $plantillaId, $casoId, $orden,$eliminacion=false){
        //Elimina los valores del caso y de una plantilla en especifico para que no se dupliquen los registros
        if($eliminacion)
            CasosValores::where('casoId',$casoId)->where('plantillaId',$plantillaId)->delete();
        foreach($campos as $campo){    
            $llave_campo = str_replace(' ', '_', $campo);
            if (array_key_exists($llave_campo, $arr)) {                
                $valor = "";
                $valor_sensible = false;
                if($arr[$llave_campo] != null)
                    $valor = $arr[$llave_campo];
                CasosValores::create(["valor" => $valor, "casoId" => $casoId, "campo" => $campo, "plantillaId" => $plantillaId, "orden" => $orden]);
            }
        }
    }
    //Guarda los campos que hay en el texto por plantillaId y casoId
    public function saveFieldsByTemplateIdByCaseId($casoId, $plantillaId, $campos, $eliminacion=true) {
        if($eliminacion)
            CasoPlantillaCampo::where("plantillaId", $plantillaId)->where("casoId", $casoId)->delete();
        foreach($campos as $campo)
            CasoPlantillaCampo::create(["campo" => $campo, "plantillaId" => $plantillaId, "casoId" => $casoId]);  
    }

    /*************************************************************/






    


    
    
 

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
