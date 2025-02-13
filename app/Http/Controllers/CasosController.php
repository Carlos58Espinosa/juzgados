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
use App\Models\FormatoCaso;
use App\Models\Logo;
use App\Models\CasoLogo;

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
            case "last_value":
                $res['valor'] = $this->getLastValue($request->all());
            break;            
            default:  
                ($request->exists("inactivos") == false) ? $activo = 1 : $activo = $request->inactivos;
                ($activo == 1) ? $vista = 'casos.index' : $vista = 'casos.index_inactives';
                
                $usuario = \Auth::user();
                $usuario_id = $usuario->id;
                $usuario_ctrl = new UsuariosController();
                $color = $usuario_ctrl->getColorByUser();
                $casos = Caso::with(['configuracion' => function ($query) {
                    $query->select('id', 'nombre');
                }, 
                'etapa_plantilla' => function ($query) {
                    $query->select('id', 'nombre');
                },
                'formato' => function ($query) {
                    $query->select('id', 'nombre');
                }])->where('usuarioId', $usuario->id)->where('activo', $activo)->orderBy('id', 'desc')->get();
                $res = view($vista, compact('casos', 'color', 'usuario_id'));   
            break;
        }
        return $res;               
    }

    public function getLastValue($arr){
        $res = '';
        $qry = "select valor from casos_valores where casoId=".$arr['casoId']." and campo = '".$arr['campo']."' and orden <= (select orden from configuracion_plantillas where configuracionId = ".$arr['configId']." and plantillaId=".$arr['plantillaId'].") order by orden desc;";
        $aux = DB::select($qry);
        if(count($aux) > 0)
            $res = $aux[0]->valor;
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
        $plantillas_ctrl = new PlantillasController();
        $campos = $plantillas_ctrl->getAllFields();
        return view('casos.create', compact('configuraciones', 'campos'));
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
            $usuario = \Auth::user();
            $caso = Caso::create(["nombre_cliente" => $request->nombre_cliente, "configuracionId" => $request->configuracion_id, "etapaPlantillaId" => $request->plantilla_id, 'usuarioId' => $usuario->id]);

            $this->generalSave($request->all(), $caso, false);

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
        /*$plantillas = ConfiguracionPlantilla::with(['plantilla' => function ($query) {
            $query->select('id', 'nombre');
        }])->where('configuracionId', $caso->configuracionId)->select('id', 'plantillaId', 'orden')->orderBy('orden')->get();*/
        $plantillas = DB::select("select * from (
        select p.id, p.nombre, c.orden from configuracion_plantillas c, plantillas p
        where c.configuracionId = ".$caso->configuracionId." and c.plantillaId = p.id 
        union
        select p.id, p.nombre, 1000 orden from casos_plantillas c, plantillas p 
        where c.casoId = ".$caso->id." and c.plantillaId = p.id and p.id not in (select plantillaId from configuracion_plantillas where configuracionId = ".$caso->configuracionId.")) as t1 order by t1.orden;");
        $plantillas_ctrl = new PlantillasController();
        $campos = $plantillas_ctrl->getAllFields();
        return view('casos.edit', compact('caso', 'plantillas', 'campos'));
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

            $this->generalSave($request->all(), $caso, true);

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
        /*CasosPlantillas::where('casoId', $id)->delete();
        CasosValores::where('casoId', $id)->delete();
        CasoPlantillaCampo::where('casoId', $id)->delete();
        CasosCamposSensibles::where('casoId', $id)->delete();*/
        $caso = Caso::findOrFail($id);
        ($caso->activo == 1) ? $caso->activo = 0 : $caso->activo = 1;
        $caso->save();
        return response()->json(200);
    }

    /***********************    Función de GUARDADO General   **************/

    //Guarda el VALOR deL CAMPO por PLANTILLA 
    public function saveDataBankByTemplateIdAndCaseId($campos, $arr, $plantillaId, $casoId, $orden,$eliminacion){
        //Elimina los valores del caso y de una plantilla en especifico para que no se dupliquen los registros

        CasosValores::where('casoId',$casoId)->where('plantillaId',$plantillaId)->delete();

        $arr_registros = [];
        foreach($campos as $campo){ 
            $llave_campo = str_replace(' ', '_', $campo);
            if (array_key_exists($llave_campo, $arr)) {   
                $valor = "";
                $valor_sensible = false;
                if($arr[$llave_campo] != null)
                    $valor = $arr[$llave_campo];

                array_push($arr_registros, ["valor" => $valor, "casoId" => $casoId, "campo" => $campo, "plantillaId" => $plantillaId, "orden" => $orden]);
            }
        }
        if(count($arr_registros) > 0)
            CasosValores::insert($arr_registros);
    }

    //Guarda los CAMPOS que hay en el TEXTO por PLANTILLA_ID y CASO_ID
    public function saveFieldsByTemplateIdByCaseId($casoId, $plantillaId, $campos, $eliminacion) {
        if($eliminacion)
            CasoPlantillaCampo::where("plantillaId", $plantillaId)->where("casoId", $casoId)->delete();

        $arr_registros = [];
        foreach($campos as $campo)
            array_push($arr_registros, ["campo" => $campo, "plantillaId" => $plantillaId, "casoId" => $casoId]);

        if(count($arr_registros) > 0)
            CasoPlantillaCampo::insert($arr_registros);
    }

    public function generalSave($arr_request, $caso, $band_delete){
        $plantillas_ctrl = new PlantillasController();
        $campos = $plantillas_ctrl->getTemplateFields($arr_request['texto']);

        $config_plantilla = ConfiguracionPlantilla::where('configuracionId', $caso->configuracionId)->where('plantillaId', $arr_request['plantilla_id'])->first();
        $orden = $config_plantilla->orden;

        $this->saveDataBankByTemplateIdAndCaseId($campos, $arr_request, $arr_request['plantilla_id'], $caso->id, $orden, $band_delete);

        CasosPlantillas::updateOrCreate(["plantillaId" => $arr_request['plantilla_id'], "casoId" => $caso->id],
            ["texto" => $arr_request['texto'], "plantillaId" => $arr_request['plantilla_id'], "casoId" => $caso->id]);

        $this->saveFieldsByTemplateIdByCaseId($caso->id, $arr_request['plantilla_id'], $campos, $band_delete);
    }

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
        $plantilla_id = openssl_decrypt($request->plantilla_id, 'AES-128-CTR', 'GeeksforGeeks', 0, '1234567891011121');
        $caso_id = openssl_decrypt($request->caso_id, 'AES-128-CTR', 'GeeksforGeeks', 0, '1234567891011121');
        $banco_datos = $this->getDataBankByCasoIdByTemplateId($caso_id, $plantilla_id);

        $plantilla = CasosPlantillas::where("casoId", $caso_id)
        ->where("plantillaId", $plantilla_id)->first();

        $res = $plantilla->texto;      

        foreach($banco_datos as $b){
            if($b->valor != null && $b->valor != ""){
                $valor = $b->valor;
                $valor = nl2br($valor);
                if($b->sensible == 1){
                    $valorAux = "";
                    for($pos = 0; $pos < strlen($valor); $pos++)
                        $valorAux .= "*";
                    $valor = $valorAux;
                }
                $res = str_replace('>' . $b->campo . '</', '>' . $valor . '</', $res);
            }
        }

        $res = str_replace('<button type="button" class="button_summernote" contenteditable="false" onclick="editButton(this)">', '', $res);
        $res = str_replace('</button>', '', $res);


        $caso = Caso::with(['formato' => function ($query) {
            $query->select('id', 'nombre_aux');
        }])->findOrFail($caso_id);

        $tamPapel = $caso->tamPapel == 'Carta' ? 'letter' : 'folio';
        $GLOBALS['y_paginado'] = $caso->paginado == 'Derecha' ? 60 : 300;
        $logos = ['','',''];
        $formato = 'pdfs.sin_logos';
        $caso_logos = [];   

        if($caso->formato != null){
            switch($caso->formato->nombre_aux){
                case 'federal':                    
                    $logos[0] = "../public/logos/federal.png";
                    $logos[1] = "../public/logos/federal.png";
                    $formato = 'pdfs.federal';
                break;
                case 'estatal':
                    $logos[0] = "../public/logos/mexico.png";
                    $logos[1] = "../public/logos/slp.png";
                    $formato = 'pdfs.estatal';
                break;
                case 'municipal':
                    $logos[0] = "../public/logos/municipal.png";
                    $formato = 'pdfs.municipal';
                break;
                case 'federal_logos':
                    $caso_logos = CasoLogo::with(['logo'])->where('casoId', $caso_id)->get();
                    $formato = 'pdfs.federal';
                break;
                case 'estatal_logos':
                    $caso_logos = CasoLogo::with(['logo'])->where('casoId', $caso_id)->get();
                    $formato = 'pdfs.estatal';
                break;
                case 'municipal_logos':
                    $caso_logos = CasoLogo::with(['logo'])->where('casoId', $caso_id)->get();
                    $formato = 'pdfs.municipal';                    
                break;
            }
            for($i=0; $i < count($caso_logos); $i++){
                if($caso_logos[$i]->logo != null)
                    $logos[$i] = "../public/logos/".$caso_logos[$i]->logo->nombre_final; 
            }
        }
        $compact = compact('res', 'caso', 'logos');

        $view = view($formato, $compact);
        $view = preg_replace('/>\s+</', '><', $view);
        $pdf = \PDF::loadHTML($view);  
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->set_paper($tamPapel, 'portrait');//letter
        return $pdf->stream();
    } 

    /*********************************************************************************/    

    /*** Se usa al actualizar una plantilla  **/
    public function saveCasoPlantillaCampo($casoId, $plantillaId, $texto) {
        $plantillas_ctrl = new PlantillasController();
        $campos = $plantillas_ctrl->getTemplateFields($texto, env('SEPARADOR')); 

        CasoPlantillaCampo::where("plantillaId", $plantillaId)->where("casoId", $casoId)->delete();
        foreach($campos as $campo)
            CasoPlantillaCampo::create(["campo" => $campo, "plantillaId" => $plantillaId, "casoId" => $casoId]);  
    }    

    /******************************************************************************/


    

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

    /****************************  Formato del PDF ************************/ 
    public function getFormat(Request $request){
        if( url()->previous() != url()->current() ){
            session()->forget('urlBack');
            session(['urlBack' => url()->previous()]);
        }

        $usuario = \Auth::user();
        $casoId = $request->caso_id;
        $logos_ids = [];
        $old_ids[0] = "";

        $formatos = FormatoCaso::all();
        
        /*$caso = Caso::with(['formato' => function ($query) {
            $query->select('id', 'nombre_aux');
        }])->findOrFail($casoId);*/
        $caso = Caso::with(['formato'])->findOrFail($casoId);

        $logos = [];

        $logos_ctrl = new LogosController();

        $casoLogos = $logos_ctrl->index(new Request(['option' => 'logos_caso', 'casoId' => $casoId]));
        foreach($casoLogos as $casoLogo)
            array_push($logos_ids, $casoLogo->id);
        $logos = array_merge($logos, $casoLogos);

        $logosAux = $logos_ctrl->index(new Request(['option' => 'logos_without_caso', 'casoId' => $casoId, 'usuarioId' => $usuario->id]));
        $logos = array_merge($logos, $logosAux);

        $old_ids[0] = implode(',', $logos_ids);

        $tamPapeles = [(Object)['nombre' => 'Carta'], (Object)['nombre' => 'Oficio']];

        $paginados = [(Object)['nombre' => 'Centro'], (Object)['nombre' => 'Derecha']];

        return view('casos.file_format', compact('formatos', 'casoId', "caso", 'logos', 'old_ids', 'logos_ids', 'tamPapeles', 'paginados'));
    }  

    public  function saveFormat(Request $request){
        Caso::where('id', $request->caso_id)->update(['formatoId' => $request->formato_id, 'margenArrAba' => $request->margenArrAba, 'margenDerIzq' => $request->margenDerIzq, 'tamPapel' => $request->tamPapel, 'paginado' => $request->paginado]);
        CasoLogo::where('casoId', $request->caso_id)->delete();
        if($request->old_ids[0] != ''){
            $logos_ids = explode(",", $request->old_ids[0]);
            for($i = 0; $i < count($logos_ids); $i++)
                CasoLogo::create(['casoId' => $request->caso_id, 'logoId'=> $logos_ids[$i], 'orden' => $i+1]);
        }
        
        $notification = array(
            'message' => 'Registro Guardado.',
            'alert-type' => 'success'
        );
        return redirect()->action('CasosController@index')->with($notification);
    }
}
