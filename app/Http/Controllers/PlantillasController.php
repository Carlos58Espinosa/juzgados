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
        $campos = $this->getAllFields();
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
        $campos = $this->getAllFields();
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
            $campos = $this->getTemplateFields($request->texto);

            $plantilla = Plantilla::findOrFail($id);
            $plantilla->nombre = $request->nombre;
            $plantilla->texto = $request->texto;
            $band = $plantilla->save();

            $this->insertPlantillaCampos($campos, true, $plantilla->id);

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

    public function getAllFields(){
        $qry = "select campo from plantillas_campos
                group by campo
                union
                select campo from casos_plantillas_campos
                group by campo;";
        return DB::select($qry);
    }

    public function getTemplateFields($texto){
        $campos_plantilla = [];
        $span_txt = '<span hidden="">|</span>';
        $arr = explode($span_txt, $texto);

        array_filter($arr, function($iter) use(&$campos_plantilla){
            if(!str_contains($iter, '<') || !str_contains($iter, '>'))
                array_push($campos_plantilla, $iter);
        });
        return array_unique($campos_plantilla);
    }

    public function insertPlantillaCampos($campos, $band_delete, $plantillaId){
        if($band_delete)
            PlantillaCampo::where("plantillaId", $plantillaId)->delete();
        foreach ($campos as $campo) 
            PlantillaCampo::create(['campo' => $campo, "plantillaId" => $plantillaId]);
    }

    public function viewPdf(Request $request) {        
        $plantilla = Plantilla::findOrFail($request->id);
        $estado = "slp";
        $res = str_replace('<button type="button" class="button_summernote" contenteditable="false" onclick="editButton(this)">', '<span class="span_param">', $plantilla->texto);
        $res = str_replace('</button>', '</span>', $res);
        $res = str_replace('<span hidden="">|</span>', '', $res);   
        $view = view('pdfs.archivo', compact('res', 'estado'));
        $view = preg_replace('/>\s+</', '><', $view);
        $pdf = \PDF::loadHTML($view);
        return $pdf->stream();
    }

    /***** Función usada en el CREATE de CASOS, se usa para traer los datos de una plantilla ****/
    public function getFieldsAndTemplateByTemplateId($arr){
        $res["grupos_campos"] = [];
        $qry2 = ""; $casoId = $arr['casoId']; $configId = $arr['configId'];

        if($casoId != 0) { 
            $qry2 = ",(select valor from casos_valores where casoId = ".$casoId." and campo = gc.campo 
            and plantillaId = ".$arr['plantillaId'].") as valor_plantilla, (select valor from casos_valores where casoId = ".$casoId." and campo = gc.campo and orden <= (select orden from configuracion_plantillas where configuracionId=".$configId." and plantillaId = ".$arr['plantillaId'].") and valor is not null and valor != '' order by orden desc limit 1) as valor_ultimo";
        }

        $qry = "select g.id, g.nombre, gc.campo ". $qry2 . " from grupos_campos gc, grupos g
                where gc.grupoId = g.id and gc.campo in (select campo from plantillas_campos where plantillaId = ".$arr['plantillaId'].") order by g.nombre, gc.campo;";                 
        $this->getArrayGroupFields($qry, $res, '');       

        $qry = "select 0 as id, 'Otros' as nombre, gc.campo ". $qry2 . " from plantillas_campos gc where plantillaId = ".$arr['plantillaId']." and campo not in (select campo from grupos_campos)";
        $this->getArrayGroupFields($qry, $res, 'Otros');

        $caso_plantilla = CasosPlantillas::where('casoId', $casoId)->where('plantillaId', $arr['plantillaId'])->first();
        if($caso_plantilla != null)
            $res['texto'] = $caso_plantilla->texto;
        else{
            $plantilla = Plantilla::findOrFail($arr['plantillaId']);
            $res['texto'] = $plantilla->texto;
        }

        return $res;
    }

    public function getArrayGroupFields($qry, &$res, $option){
        $aux = DB::select($qry);

        $cad = "";
        if(count($aux) > 0){
            $cad = $aux[0]->id;
            $raux['id'] = $cad;
            $raux['grupo'] = $aux[0]->nombre;
            $raux["campos"] = [];  
            foreach($aux as $a){
                if($a->id != $cad){
                    array_push($res["grupos_campos"], $raux);
                    $cad = $a->id;
                    $raux['id'] = $cad;
                    $raux['grupo'] = $a->nombre;
                    $raux["campos"] = [];
                }
                $row = ['campo' => $a->campo, 
                'valor_plantilla' => (array_key_exists('valor_plantilla',$a)) ? $a->valor_plantilla : null, 
                'valor_ultimo' => (array_key_exists('valor_ultimo',$a)) ? $a->valor_ultimo : null];
                array_push($raux["campos"], $row);
            } 
            if($aux[count($aux)-1]->id == $cad)
                array_push($res["grupos_campos"], $raux);         
        }else{
            if($option == "Otros"){
                $raux['id'] = 0;
                $raux['grupo'] = 'Otros';
                $raux["campos"] = [];
                array_push($res["grupos_campos"], $raux);
            }
        }
    }

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
        $campos = $this->getTemplateFields($request->texto);
        $plantilla = Plantilla::create(['nombre'=> $request->nombre, 'texto'=> $request->texto, 'usuarioId' => $usuario->id]);
        $this->insertPlantillaCampos($campos, false, $plantilla->id);
    }
}
