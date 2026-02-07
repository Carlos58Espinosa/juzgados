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
use App\Models\CasoPlantillaLog;
use App\Models\CasosUsuarios;
use App\User;

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
            case "colaboradores":            
                $casoId = $request->caso_id;
                $colaboradores = CasosUsuarios::where('casoId', $casoId)->pluck('usuarioId');
                return response()->json($colaboradores);
            break;         
            default:  
                $colaboradores = [];
                ($request->exists("inactivos") == false) ? $activo = 1 : $activo = $request->inactivos;
                ($activo == 1) ? $vista = 'casos.index' : $vista = 'casos.index_inactives';                 
                $usuario = \Auth::user();
                $usuario_id = $usuario->id;
                $tipo_usuario = $usuario->tipo;
                $usuario_ctrl = new UsuariosController();
                $color = $usuario_ctrl->getColorByUser();                   
                //Casos del Líder    
                if($usuario->tipo == 'Cliente') {
                    $colaboradores = User::where('usuarioId', $usuario_id)->select('id', 'nombre')->get();
                    $casos = $this->getCasesLeader($activo);
                } else //Casos del Colaborador
                    $casos = $this->getCasesCollaborator($usuario_id, $activo);
                $res = view($vista, compact('casos', 'color', 'usuario_id', 'colaboradores', 'tipo_usuario'));       
            break;
        }
        return $res;               
    }

    private function queryCasos($ids, $activo) {
        return Caso::with([
            'configuracion:id,nombre',
            'etapa_plantilla:id,nombre',
            'formato:id,nombre'
        ])
        ->whereIn('id', $ids)
        ->where('activo', $activo)
        ->orderByDesc('id')
        ->get();
    }

    public function getCasesLeader($activo)  {
        $plantillas_ctrl = new PlantillasController();
        $arrUsuariosIds = $plantillas_ctrl->getArrayUserIds(); 

        $ids = Caso::whereIn('usuarioId', $arrUsuariosIds)
            ->where('activo', $activo)
            ->pluck('id');

        return $this->queryCasos($ids, $activo);
    }

    public function getCasesCollaborator($usuarioId, $activo)  {
        $casosCreados = Caso::where('usuarioId', $usuarioId)
            ->where('activo', $activo)
            ->pluck('id');

        $casosColaborador = CasosUsuarios::where('usuarioId', $usuarioId)
            ->pluck('casoId');

        $ids = $casosCreados->merge($casosColaborador)->unique();

        return $this->queryCasos($ids, $activo);
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
        $plantillas_ctrl = new PlantillasController();
        $arrUsuariosIds = $plantillas_ctrl->getArrayUserIds();
        $plantillas_all = DB::table('plantillas')->whereIn('usuarioId', $arrUsuariosIds)->select('id','nombre', 'texto')->orderBy('nombre')->get();
        $configuraciones = DB::table('configuracion')->whereIn('usuarioId', $arrUsuariosIds)->select('id','nombre')->orderBy('nombre')->get();
        $campos = $plantillas_ctrl->getDictionaryParams();
        return view('casos.create', compact('configuraciones', 'campos', 'plantillas_all'));
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
            $arr_request = $request->all();
            $usuario = \Auth::user();

            $plantilla_id = $request->plantilla_id_2;
            if($request->tipo_creacion == "2")
                $plantilla_id = $request->plantilla_id;
            $arr_request['plantilla_id'] = $plantilla_id;

            //1.- Genera el registro de Caso
            $caso = Caso::create(["nombre_cliente" => $request->nombre_cliente, "configuracionId" => $request->configuracion_id, "etapaPlantillaId" => $plantilla_id, 'usuarioId' => $usuario->id, "tipo_creacion"=> $request->tipo_creacion]);

            //2.- Genera el registro de Caso Plantilla
            $caso_plantilla = CasosPlantillas::updateOrCreate(["plantillaId" => $arr_request['plantilla_id'], "casoId" => $caso->id],
            ["texto" => $arr_request['texto'], "plantillaId" => $arr_request['plantilla_id'], "casoId" => $caso->id, 'detalle' => $arr_request['detalle']]);

            //3.- Genera los campos del Caso
            $plantillas_ctrl = new PlantillasController();
            $campos = $plantillas_ctrl->getParamsInText($arr_request['texto']);
            $orden = 1;
            $this->saveCaseValuesFields($campos, $arr_request, $plantilla_id, $caso->id, $caso_plantilla->id, $orden, false);

            //4.- Guarda en la tabla casos_plantillas_campos
            $this->saveCaseTemplateFields($caso->id, $plantilla_id, $caso_plantilla->id, $campos, false);

            CasoPlantillaLog::create(['usuarioId' => $usuario->id, 'texto_final' => $request->texto_final, 'casoId' =>  $caso->id, 'plantillaId' => $plantilla_id, 'casoPlantillaId' => $caso_plantilla->id]);

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
        $plantillas_ctrl = new PlantillasController();

        $caso = Caso::with(['configuracion' => function ($query) {
            $query->select('id', 'nombre');
        }, 'plantillas'])->findOrFail($id);
        /*$plantillas = ConfiguracionPlantilla::with(['plantilla' => function ($query) {
            $query->select('id', 'nombre');
        }])->where('configuracionId', $caso->configuracionId)->select('id', 'plantillaId', 'orden')->orderBy('orden')->get();*/        

        if($caso->tipo_creacion == "1"){
            $usuario = \Auth::user();
            $arrUsuariosIds = $plantillas_ctrl->getArrayUserIds(); 
            $plantillas = DB::table('plantillas')->whereIn('usuarioId', $arrUsuariosIds)->select('id','nombre', 'texto')->orderBy('nombre')->get();
        }
        else {
            $plantillas = DB::select("select * from (
            select p.id, p.nombre, c.orden from configuracion_plantillas c, plantillas p
            where c.configuracionId = ".$caso->configuracionId." and c.plantillaId = p.id 
            union
            select p.id, p.nombre, 1000 orden from casos_plantillas c, plantillas p 
            where c.casoId = ".$caso->id." and c.plantillaId = p.id and p.id not in (select plantillaId from configuracion_plantillas where configuracionId = ".$caso->configuracionId.")) as t1 order by t1.orden;");
        }
        
        $campos = $plantillas_ctrl->getDictionaryParams();  
        $plantillas_contestadas = CasosPlantillas::with(['plantilla' => function ($query) {
            $query->select('id', 'nombre');
        }])->where('casoId', $caso->id)->select('id', 'plantillaId')->get();  
        return view('casos.edit', compact('caso', 'plantillas', 'campos', 'plantillas_contestadas'));
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
        $transaction = DB::transaction(function() use($request, $id){
            $arr_request = $request->all();

            $caso = Caso::findOrFail($id);           

            //1.- Genera un registro nuevo (accion_id = 1) y si es una actualizacion (accion_id = 2)
            if($request->accion_id == "1"){ //Create
                $caso_plantilla = CasosPlantillas::create(["texto" => $arr_request['texto'], "plantillaId" => $arr_request['plantilla_id'], "casoId" => $caso->id, "detalle" => $arr_request['detalle']]);
                $plantilla_id = $arr_request['plantilla_id'];
            } else { //Edit
                $caso_plantilla = CasosPlantillas::findOrFail($arr_request['caso_plantilla_id']);
                $caso_plantilla->texto = $arr_request['texto'];
                $caso_plantilla->save();
                $plantilla_id = $caso_plantilla->plantillaId;
            }

            //2.- Actualiza los campos del Caso
            $caso->nombre_cliente = $request->nombre_cliente;
            $caso->etapaPlantillaId = $plantilla_id;
            $caso->save();

            //3.- Genera los campos del Caso
            $plantillas_ctrl = new PlantillasController();
            $campos = $plantillas_ctrl->getParamsInText($arr_request['texto']);
            $orden = 1;
            $this->saveCaseValuesFields($campos, $arr_request, $caso_plantilla->plantillaId, $caso->id, $caso_plantilla->id, $orden, true);

            //4.- Guarda en la tabla casos_plantillas_campos
            $this->saveCaseTemplateFields($caso->id, $caso_plantilla->plantillaId, $caso_plantilla->id, $campos, true);

            $usuario = \Auth::user();
            CasoPlantillaLog::create(['usuarioId' => $usuario->id, 'texto_final' => $request->texto_final, 'casoId' =>  $caso->id, 'plantillaId' => $plantilla_id, 'casoPlantillaId' => $caso_plantilla->id]);

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

    public function saveCaseValuesFields($campos, $arr_valores, $plantillaId, $casoId, $casoPlantillaId, $orden, $eliminacion){
        //Elimina los valores del caso y de una plantilla en especifico para que no se dupliquen los registros
        if($eliminacion)
            CasosValores::where('casoPlantillaId', $casoPlantillaId)->delete();

        $arr_registros = [];
        foreach($campos as $campo){ 
            $llave_campo = str_replace(' ', '_', $campo);
            if (array_key_exists($llave_campo, $arr_valores)) {   
                $valor = "";
                $valor_sensible = false;
                if($arr_valores[$llave_campo] != null)
                    $valor = $arr_valores[$llave_campo];

                array_push($arr_registros, ["valor" => $valor, "casoId" => $casoId, "campo" => $campo, "plantillaId" => $plantillaId, "orden" => $orden, "casoPlantillaId" => $casoPlantillaId]);
            }
        }
        if(count($arr_registros) > 0)
            CasosValores::insert($arr_registros);
    }

    //Guarda los CAMPOS que hay en el TEXTO por PLANTILLA_ID y CASO_ID
    public function saveCaseTemplateFields($casoId, $plantillaId, $casoPlantillaId, $campos, $eliminacion) {
        if($eliminacion)
            CasoPlantillaCampo::where("casoPlantillaId", $casoPlantillaId)->delete();

        $arr_registros = [];
        foreach($campos as $campo)
            array_push($arr_registros, ["campo" => $campo, "plantillaId" => $plantillaId, "casoId" => $casoId, "casoPlantillaId" => $casoPlantillaId]);

        if(count($arr_registros) > 0)
            CasoPlantillaCampo::insert($arr_registros);
    }

    //Agrega los colaboradores a un caso
    public function addCollaborators(Request $request){
        $params = $request->all();
        CasosUsuarios::where('casoId', $params['caso_id'])->delete();
        foreach($params['usuarios'] as $usuario)
            CasosUsuarios::create(['casoId' => $params['caso_id'], "usuarioId" => $usuario]);
        return redirect()->back()->with('success', 'Colaboradores asignados correctamente.');
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
        $resultado = CasoPlantillaCampo::from('casos_plantillas_campos as cpc')
        ->leftJoin('casos_campos_sensibles as ccs', function ($join) {
            $join->on('ccs.campo', '=', 'cpc.campo')
                ->on('ccs.casoId', '=', 'cpc.casoId');
        })
        ->select(
            'cpc.campo',
            DB::raw('MAX(ccs.sensible) as sensible')
        )
        ->where('cpc.casoId', $casoId)
        ->groupBy('cpc.campo')
        ->orderBy('cpc.campo')
        ->get();
        return $resultado;
    }

    /*********************** Guardar campos sensibles *******************************/
    public function saveSensitiveData(Request $request)
    {
        $banco_datos = [];
        $hasChecks = collect($request->keys())->contains(fn($k) => str_ends_with($k, '_check'));

        if ($hasChecks) 
            $banco_datos = $this->getAllFieldsSensibleTemplatesByConfigId($request->configuracionId, $request->casoId);

        $this->saveSensitiveDataByCasoId($banco_datos, $request->all(), $request->casoId);

        return redirect()
            ->action('CasosController@index')
            ->with([
                'message' => 'Registro Guardado.',
                'alert-type' => 'success'
            ]);
    }

    public function saveSensitiveDataByCasoId($campos, $arr, $casoId)
    {
        DB::transaction(function () use ($campos, $arr, $casoId) {
            $camposSeleccionados = [];
            foreach ($campos as $c) {
                $key = str_replace(' ', '_', $c->campo) . '_check';
                if (!empty($arr[$key]) && $arr[$key] === 'on') {
                    $camposSeleccionados[] = $c->campo;
                    CasosCamposSensibles::updateOrCreate(
                        [
                            'casoId' => $casoId,
                            'campo'  => $c->campo
                        ],
                        [
                            'sensible' => 1
                        ]
                    );
                }
            }

            // Borrar los que ya no están seleccionados
            if (!empty($camposSeleccionados)) {
                CasosCamposSensibles::where('casoId', $casoId)
                    ->whereNotIn('campo', $camposSeleccionados)
                    ->delete();

            } else {
                // Si no hay ninguno sensible → borrar todos
                CasosCamposSensibles::where('casoId', $casoId)->delete();
            }
        });
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
