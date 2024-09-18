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
    public function index(Request $request)
    {
        $res = [];

        switch($request->option){
            case "fields_values_template_text":
                $res = $this->getFieldsValuesTextByTemplateIdAndCaseId($request->all());
            break;
            default:
                $casos = Caso::with(['configuracion' => function ($query) {
                    $query->select('id', 'nombre');
                }, 
                'etapa_plantilla' => function ($query) {
                    $query->select('id', 'nombre');
                }])->orderBy('id', 'desc')->get();
                $res = view('casos.index',compact('casos'));   
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
        $configuraciones = DB::table('configuracion')->select('id','nombre')->orderBy('nombre')->get();
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

            $this->saveFieldsByTemplateIdByCaseId($caso->id, $request->plantilla_id, $campos, false);

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

        $plantillas = ConfiguracionPlantilla::with(['plantilla' => function ($query) {
            $query->select('id', 'nombre');
        }])->where('configuracionId', $caso->configuracionId)->select('id', 'plantillaId', 'orden')->orderBy('orden')->get();
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
            $plantillas_ctrl = new PlantillasController();
            $banco_datos = $plantillas_ctrl->getTemplateFields($request->texto, env('SEPARADOR'));

            $config_plantilla = ConfiguracionPlantilla::where('configuracionId', $caso->configuracionId)->where('plantillaId', $request->plantilla_id)->first();
            $orden = 0;
            if($config_plantilla != null)
                $orden = $config_plantilla->orden;

            $this->saveDataBankByTemplateIdAndCaseId($banco_datos, $arr, $request->plantilla_id, $id, $orden, true);

            CasosPlantillas::updateOrCreate(["plantillaId" => $request->plantilla_id, "casoId" => $id],["texto" => $request->texto, "plantillaId" => $request->plantilla_id, "casoId" => $id]);

            $this->saveFieldsByTemplateIdByCaseId($caso->id, $request->plantilla_id, $banco_datos, true);

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
        CasosCamposSensibles::where('casoId', $id)->delete();
        Caso::destroy($id);
        return response()->json(200);
    }

    /************************** Se usa en la Edición  ************************************/
    public function getDataBankByCaseIdAndTemplateId($casoId, $configuracionId, $plantillaId){
        $query = "select *, (select valor from casos_valores where casoId = ".$casoId." and campo = t1.campo and plantillaId = t1.plantillaId) as valor_plantilla,
            (select valor from casos_valores where casoId = ".$casoId." and campo = t1.campo and orden <=t1.orden and valor is not null and valor != '' order by orden desc limit 1) as valor_ultimo
                from (
                select cpc.campo, cpc.plantillaId, cp.orden
                from configuracion_plantillas cp, casos_plantillas_campos cpc
                where cp.configuracionId = ".$configuracionId." and cp.plantillaId = ".$plantillaId." and cpc.casoId = ".$casoId." and cpc.plantillaId = cp.plantillaId
                union
                select pc.campo, pc.plantillaId, cp.orden
                from configuracion_plantillas cp, plantillas_campos pc
                where cp.configuracionId = ".$configuracionId." and cp.plantillaId = ".$plantillaId." and cp.plantillaId = pc.plantillaId and  pc.plantillaId not in (select plantillaId from casos_plantillas_campos where casoId = ".$casoId.")
                )  as t1 order by t1.orden, t1.campo;";
        return DB::select($query);
    }


    public function getFieldsValuesTextByTemplateIdAndCaseId($params){
        $res = [];

        $caso_plantilla = CasosPlantillas::where('casoId', $params['casoId'])->where('plantillaId', $params['plantillaId'])->first();
        if($caso_plantilla != null)
            $res['texto'] = $caso_plantilla->texto;
        else{
            $plantilla = Plantilla::findOrFail($params['plantillaId']);
            $res['texto'] = $plantilla->texto;
        }

        $res['campos'] = $this->getDataBankByCaseIdAndTemplateId($params['casoId'], $params['configuracionId'], $params['plantillaId']);
        return $res;
    }

    //Guarda le valor de campo por plantilla 
    //Se usa en el Create y Update
    public function saveDataBankByTemplateIdAndCaseId($campos, $arr, $plantillaId, $casoId, $orden,$eliminacion){
        //Elimina los valores del caso y de una plantilla en especifico para que no se dupliquen los registros
        /*if($eliminacion)
            CasosValores::where('casoId',$casoId)->where('plantillaId',$plantillaId)->delete();*/
        foreach($campos as $campo){    
            $llave_campo = str_replace(' ', '_', $campo);
            if (array_key_exists($llave_campo, $arr)) {                
                $valor = "";
                $valor_sensible = false;
                if($arr[$llave_campo] != null)
                    $valor = $arr[$llave_campo];
                CasosValores::updateOrCreate(["casoId" => $casoId, "campo" => $campo, "plantillaId" => $plantillaId],["valor" => $valor, "casoId" => $casoId, "campo" => $campo, "plantillaId" => $plantillaId, "orden" => $orden]);
            }
        }
    }

    //Guarda los campos que hay en el texto por plantillaId y casoId
    //Se usa en el Create
    public function saveFieldsByTemplateIdByCaseId($casoId, $plantillaId, $campos, $eliminacion) {
        if($eliminacion)
            CasoPlantillaCampo::where("plantillaId", $plantillaId)->where("casoId", $casoId)->delete();

        foreach($campos as $campo)
            CasoPlantillaCampo::create(["campo" => $campo, "plantillaId" => $plantillaId, "casoId" => $casoId]); 
    }


    /***
      Se usa al actualizar una plantilla
     **/
    public function saveCasoPlantillaCampo($casoId, $plantillaId, $texto) {
        $plantillas_ctrl = new PlantillasController();
        $campos = $plantillas_ctrl->getTemplateFields($texto, env('SEPARADOR')); 

        CasoPlantillaCampo::where("plantillaId", $plantillaId)->where("casoId", $casoId)->delete();
        foreach($campos as $campo)
            CasoPlantillaCampo::create(["campo" => $campo, "plantillaId" => $plantillaId, "casoId" => $casoId]);  
    }    

    /******************************************************************************/


    /*****************************  Ver PDF *******************************/

    public function getDataBankByCasoIdByTemplateId($casoId, $plantillaId){
        $query = "select c.campo,
        (select valor from casos_valores where casoId = c.casoId and plantillaId = c.plantillaId and campo = c.campo) as valor,
        (select sensible from casos_campos_sensibles where  casoId= c.casoId and campo = c.campo) as sensible
        from casos_plantillas_campos c
        where c.casoId = ".$casoId." and c.plantillaId = ".$plantillaId.";";
        return DB::select($query);
    }

    public function viewCasosPdf(Request $request) {
        $banco_datos = $this->getDataBankByCasoIdByTemplateId($request->caso_id, $request->plantilla_id);

        $plantilla = CasosPlantillas::where("casoId", $request->caso_id)
        ->where("plantillaId", $request->plantilla_id)->first();
        $res = $plantilla->texto;

        foreach($banco_datos as $b){
            if($b->valor != null && $b->valor != ""){
                $valor = $b->valor;
                if($b->sensible == 1){
                    $valorAux = "";
                    for($pos = 0; $pos < strlen($valor); $pos++)
                        $valorAux .= "*";
                        //$valor = '<span style="background-color: #ffffff;"><font color="#ffffff">'.$valor.'</font></span>';
                    $valor = $valorAux;
                }
                $res = str_replace(env('SEPARADOR').$b->campo.env('SEPARADOR'), $valor, $res);
            }

        }
        $estado = "slp";
        $view = view('pdfs.archivo', compact('res', 'estado'));
        $view = preg_replace('/>\s+</', '><', $view);
        $pdf = \PDF::loadHTML($view);
        return $pdf->stream();
    } 

    /*********************************************************************************/

    /***************************** Datos Sensibles ***********************************/
    public function getSensitiveData(Request $request){
        if( url()->previous() != url()->current() ){
            session()->forget('urlBack');
            session(['urlBack' => url()->previous()]);
        }

        $caso = Caso::with(['configuracion'  => function ($query) {
            $query->select('id', 'nombre');
        }])->findOrFail($request->caso_id);

        $campos = $this->getAllFieldsSensibleTemplatesByConfigId($caso->configuracionId, $caso->id);
        return view('casos.bank_data', compact('caso', 'campos'));
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

    /*********************** Guardar campos sensibles *******************************/
    public function saveSensitiveData(Request $request){
        $transaction = DB::transaction(function() use($request){
            //Guarda los valores sensibles
            $stringArray = json_encode($request->all());
            $banco_datos = [];
            if (str_contains($stringArray, "_check"))
                $banco_datos = $this->getAllFieldsTemplatesByConfigId($request->configuracionId, $request->casoId);
            $this->saveSensitiveDataByCasoId($banco_datos, $request->all(), $request->casoId);            

            $notification = array(
                  'message' => 'Registro Guardado.',
                  'alert-type' => 'success'
            );
            return $notification;
        });
        return redirect()->action('CasosController@index')->with($transaction);
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

    public function saveSensitiveDataByCasoId($campos, $arr, $casoId){
        $campos_aux = [];
        foreach($campos as $c){
            $key_field = str_replace(' ', '_', $c);
            if (array_key_exists($key_field."_check",$arr)) {
                if($arr[$key_field."_check"] == "on"){
                    array_push($campos_aux, $c);
                    CasosCamposSensibles::updateOrCreate(["casoId" => $casoId, "campo" => $c], ["casoId" => $casoId, "campo" => $c, "sensible" => true]);
                }
            }
        }

        if(count($campos_aux) > 0)
            CasosCamposSensibles::where("casoId", $casoId)->whereNotIn('campo', $campos_aux)->delete();
        else{
            if(count($campos_aux) == 0)
                CasosCamposSensibles::where("casoId", $casoId)->delete();
        }
    } 

    /************************************************************************/    
}
