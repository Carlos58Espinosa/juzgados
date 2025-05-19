<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Plantilla;
use App\Models\PlantillaCampo;
use App\Models\Grupo;
use App\Models\CasosPlantillas;
use App\User;
use Validator;

class PlantillasController extends Controller
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
            case 'fields_text_by_template_id':
                $res = $this->getFieldsAndTemplateByTemplateId($request->all());
                break;           
            default:
                $tipo_usuario = \Auth::user()->tipo;                
                $arrUsuariosIds = $this->getArrayUserIds();                
                $plantillas = Plantilla::with(['usuario' => function ($query) {
                    $query->select('id', 'tipo');
                }, 'configuracion_plantillas.configuracion'=> function ($query) {
                    $query->select('configuracion.id','configuracion.nombre');
                }])->whereIn('usuarioId', $arrUsuariosIds)->orderBy('updated_at', 'desc')->get();
                /*$notification = array(
                          'message' => 'Successful!!',
                          'alert-type' => 'success'
                    );
                session()->flash("message", "Carlangas");   
                session()->flash("alert-type", "success"); 
                session()->flash("title", "alerta"); */  
                //print_r($plantillas);
                //return true;
                $res = view('plantillas.index',compact('plantillas','tipo_usuario'));
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
        $campos = $this->getDictionaryParams();
        return view('plantillas.create', compact('campos'));
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
            'nombre' => 'required|unique:plantillas,nombre,',                       
            'texto' => 'required',
        ], ['nombre.unique' => 'El nombre debe de ser único.']);


        $transaction = DB::transaction(function() use($request){
            $this->saveRegister($request);            
            $notification = array(
                  'message' => 'Registro Guardado.',
                  'alert-type' => 'success'
            );
            return $notification;
        });
        return redirect()->action('PlantillasController@index')->with($transaction);
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
        $plantilla = Plantilla::findOrFail($id);
        return view('plantillas.show', compact('plantilla'));
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
        $campos = $this->getDictionaryParams();
        $plantilla = Plantilla::findOrFail($id);
        return view('plantillas.edit', compact('plantilla', 'campos'));
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
            'nombre' => 'required|unique:plantillas,nombre,'.$id,            
            'texto' => 'required'
        ], ['nombre.unique' => 'El nombre debe de ser único.']);

        return DB::transaction(function() use($request, $id){
            $campos = $this->getParamsInText($request->texto);
            $plantilla = Plantilla::findOrFail($id);
            $plantilla->nombre = $request->nombre;
            $plantilla->texto = $request->texto;
            $band = $plantilla->save();

            $this->insertTemplateFields($campos, true, $plantilla->id);

            if ($band) {
                $notification = array(
                  'message' => 'Registro Guardado.',
                  'alert-type' => 'success'
                );
                return redirect()->action('PlantillasController@index')->with($notification);
            }else {
                $notification = array(
                  'message' => 'La Plantilla no se pudo guardar.',
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
        //Si no esta configurada y no tiene contestaciones ELIMINAR
        //Si solo esta configurada => sería quitarla de las configuraciones para poder eliminar
        //Si ya tiene contestaciones solo DESACTIVAR

        return DB::transaction(function() use($id){
            $qry = "select c.* from casos_valores c, plantillas p, plantillas_campos pc
                    where p.id = pc.plantillaId and pc.id = c.plantillaId and p.id = ".$id.";";
            $casos_valores = DB::select($qry);

            if( count($casos_valores) > 0){
                //SOLO DESACTIVAR
                return "desactivar";            
            } else{
                if(DB::table("configuracion_plantillas")->where('plantillaId',$id)->count() == 0){
                    PlantillaCampo::where("plantillaId", $id)->delete();
                    Plantilla::destroy($id);
                    return response()->json(200);
                }else
                    return response()->json(['errors' => 'No se puede eliminar la Plantilla porqué pertenece a una configuración.'], '422');
            }   
        });     
    }

    //Obtiene todas las cadenas entre las etiquetas de BUTTON
    public function getTagContents($string, $tag_open, $tag_close){
       $result = [];
       foreach (explode($tag_open, $string) as $key => $value) {
           if(strpos($value, $tag_close) !== FALSE)
                $result[] = substr($value, 0, strpos($value, $tag_close));
       }
       return $result;
    }

    //Limpia las cadenas de apariciones HTML
    public function getParamsInText($text){
        $res_arr = $this->getTagContents($text , '<button type="button" class="button_summernote" contenteditable="false" onclick="editButton(this)">' , "</button>");
        $arr_final = [];
        foreach($res_arr as $r)
            array_push($arr_final, strip_tags($r));
        $arr_final = array_unique($arr_final);
        return $arr_final;
    }
    
    //Guarda el arreglo de $campos en la tabla PlantillaCampo
    public function insertTemplateFields($campos, $band_delete, $plantillaId){
        if($band_delete)
            PlantillaCampo::where("plantillaId", $plantillaId)->delete();
        $arr_registros = [];
        foreach ($campos as $campo)
            array_push($arr_registros, ['campo' => $campo, "plantillaId" => $plantillaId]);
        if(count($arr_registros) > 0)
            PlantillaCampo::insert($arr_registros);
    }

    //Obtiene todos los parámetros que hay en la Base de Datos para tener un diccionario y no repetir nombres en los parámetros
    public function getDictionaryParams(){
        $qry = "select campo from plantillas_campos
                group by campo
                union
                select campo from casos_plantillas_campos
                group by campo;";
        return DB::select($qry);
    }

    //Ver PDF
    public function viewPdf(Request $request) { 
        $id = openssl_decrypt($request->id, 'AES-128-CTR', 'GeeksforGeeks', 0, '1234567891011121');
        $plantilla = Plantilla::findOrFail($id);
        $res = str_replace('<button type="button" class="button_summernote" contenteditable="false" onclick="editButton(this)">', '<span class="span_param">', $plantilla->texto);
        $res = str_replace('</button>', '</span>', $res);

        $caso = (object)['margenArrAba' => 10 ,'margenDerIzq' => 100];
        $GLOBALS['y_paginado'] = 60;

        $view = view('pdfs.sin_logos', compact('res', 'caso'));
        $view = preg_replace('/>\s+</', '><', $view);
        $pdf = \PDF::loadHTML($view);
        $pdf->getDomPDF()->set_option("enable_php", true);
        return $pdf->stream();
    }

    //Botón de Clonar Plantilla
    public function clone(Request $request){
        date_default_timezone_set('America/Mexico_City');
        $plantilla = Plantilla::findOrFail($request->id);
        $this->saveRegister(new Request(['nombre' => $plantilla->nombre. ' ' .date("H:i:s"), 'texto' => $plantilla->texto]));        
        return response()->json(200);
    }

    public function getArrayUserIds(){
        $arrUsuariosIds = [];
        $usuario = \Auth::user();
        array_push($arrUsuariosIds, $usuario->id);
        $usuarios = User::where('tipo', 'Administrador')->get();
        foreach($usuarios as $usuario_iter)
            array_push($arrUsuariosIds, $usuario_iter->id);
        return $arrUsuariosIds;
    }

    public function saveRegister(Request $request){
        $usuario = \Auth::user();
        $campos = $this->getParamsInText($request->texto);
        $plantilla = Plantilla::create(['nombre'=> $request->nombre, 'texto'=> $request->texto, 'usuarioId' => $usuario->id]);
        $this->insertTemplateFields($campos, false, $plantilla->id);
    }

    /***** Función usada en el CREATE de CASOS, se usa para traer los datos de una plantilla 
     * CASO 1: No Existe el CASOID ni la PLANTILLAID (NUEVO TODO)
     * CASO 2: Existe el CASOID No existe la PLANTILLAID (NUEVO PLANTILLA)
     * CASO 3: Existe el CASOID y Sí existe la PLANTILLAID (EDICIÓN PLANTILLA)
     * ****/

    public function getFieldsAndTemplateByTemplateIdCase1($plantillaId, &$res, $usuarioId, $configId, $casoId){
        $qry = "select g.id, g.nombre as grupo, gc.campo, null as valor_plantilla, 
        (select valor from casos_valores where casoId = ".$casoId." and campo = gc.campo and valor != '' order by casoPlantillaId desc limit 1) as valor_ultimo
        from grupos_campos gc, grupos g where g.usuarioId = ".$usuarioId." and gc.grupoId = g.id and gc.campo in (select campo from plantillas_campos where plantillaId = ".$plantillaId.") order by g.nombre, gc.campo;";
        $aux = DB::select($qry);
        $this->getArrayGroupFields($aux, $res);

        $qry = "select 0 as id, 'Otros' as grupo, gc.campo, null as valor_plantilla,
        (select valor from casos_valores where casoId = ".$casoId." and campo = gc.campo and valor != '' order by casoPlantillaId desc limit 1) as valor_ultimo 
        from plantillas_campos gc where gc.plantillaId = ".$plantillaId." and campo not in 
        (select gca.campo from grupos_campos gca, grupos ga where ga.id = gca.grupoId and ga.usuarioId = ".$usuarioId.");";
        $aux = DB::select($qry);
        $this->getArrayGroupFields($aux, $res);
        
        $plantilla = Plantilla::findOrFail($plantillaId);
        $res['texto'] = $plantilla->texto;
    }

    public function getFieldsAndTemplateByTemplateIdCase3(&$res, $usuarioId, $casoId, $casoPlantillaId){
        //Para lo que esta agrupado
        $qry = "select g.id, g.nombre as grupo, gc.campo ,
        (select valor from casos_valores where casoId = ". $casoId." and campo = gc.campo and casoPlantillaId = ".$casoPlantillaId." and valor != '') as valor_plantilla, 
        (select valor from casos_valores where casoId = ". $casoId." and campo = gc.campo and casoPlantillaId <= ".$casoPlantillaId." and valor is not null and valor != '' order by orden desc limit 1) as valor_ultimo 
        from grupos_campos gc, grupos g where g.usuarioId = ".$usuarioId." and gc.grupoId = g.id and gc.campo in 
        (select campo from casos_plantillas_campos where casoPlantillaId = ".$casoPlantillaId." and casoId=". $casoId.") order by g.nombre, gc.campo;";
        $aux = DB::select($qry);
        $this->getArrayGroupFields($aux, $res);  

        //Para lo que no esta agrupado
        $qry = "select 0 as id, 'Otros' as grupo, gc.campo,
        (select valor from casos_valores where casoId = ". $casoId." and campo = gc.campo and casoPlantillaId = ".$casoPlantillaId." and valor != '') as valor_plantilla, 
        (select valor from casos_valores where casoId = ". $casoId." and campo = gc.campo and casoPlantillaId <= ".$casoPlantillaId." and valor is not null and valor != '' order by orden desc limit 1) as valor_ultimo 
        from casos_plantillas_campos gc where gc.casoPlantillaId = ".$casoPlantillaId." and gc.casoId=". $casoId." and campo not in (select gca.campo from grupos_campos gca, grupos ga where ga.id = gca.grupoId and ga.usuarioId = ".$usuarioId.");";
        //echo $qry;
        $aux = DB::select($qry);
        $this->getArrayGroupFields($aux, $res);        
    }

    public function getFieldsAndTemplateByTemplateId($arr){
        $usuario = \Auth::user();
        $res["grupos_campos"] = [];
        $res['detalle'] = "";
        $casoId = $arr['casoId']; $configId = $arr['configId']; 
        (array_key_exists('casoPlantillaId', $arr)) ? $casoPlantillaId = $arr['casoPlantillaId'] : $casoPlantillaId = 0;

        //Caso 1
        if($casoId == 0)
            $this->getFieldsAndTemplateByTemplateIdCase1($arr['plantillaId'], $res, $usuario->id, $configId, $casoId);
        else {
            $caso_plantilla = null;
            if( $casoPlantillaId != null || $casoPlantillaId != '')
                $caso_plantilla = CasosPlantillas::findOrFail($casoPlantillaId);
            //Caso 2
            if($caso_plantilla == null)
                $this->getFieldsAndTemplateByTemplateIdCase1($arr['plantillaId'], $res, $usuario->id,$configId, $casoId);
            else{ //Caso 3
                $res['texto'] = $caso_plantilla->texto;
                $res['detalle'] = $caso_plantilla->detalle;
                $this->getFieldsAndTemplateByTemplateIdCase3($res, $usuario->id, $casoId, $casoPlantillaId);
            }
        }
        return $res;                        
    }

    public function getArrayGroupFields($aux, &$res){
        if(count($aux) > 0){
            $ids = array_unique(array_column($aux, 'id'));
            foreach($ids as $id){
                $raux['id'] = $id;                
                $raux["campos"] = array_filter($aux, function ($value) use ($id){
                    if ($value->id == $id) 
                        return $value;
                });
                if($id == 0){
                    $raux['grupo'] = 'Otros';
                    array_push($res["grupos_campos"], $raux);    
                }else{
                    if(count($raux["campos"]) > 0){
                        $raux['grupo'] = $raux["campos"][0]->grupo;
                        array_push($res["grupos_campos"], $raux); 
                    }
                }                   
            }
        }       
    }    
}
