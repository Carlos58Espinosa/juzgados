<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Plantilla;
use App\Models\PlantillaCampo;
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
                $res = $this->getFieldsAndTemplateByTemplateId($request->plantillaId);
                break;            
            default:
                $plantillas = DB::table('plantillas')->orderBy('nombre')->get();
                /*$notification = array(
                          'message' => 'Successful!!',
                          'alert-type' => 'success'
                    );
                session()->flash("message", "Carlangas");   
                session()->flash("alert-type", "success"); 
                session()->flash("title", "alerta"); */  
                //print_r($plantillas);
                //return true;
                $res = view('plantillas.index',compact('plantillas'));
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
        return view('plantillas.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {    
        $this->validate($request, [
            'nombre' => 'required|unique:plantillas,nombre,',                       
            'texto' => 'required',
        ], ['nombre.unique' => 'El nombre debe de ser único.']);
        if( url()->previous() != url()->current() ){
            session()->forget('urlBack');
            session(['urlBack' => url()->previous()]);
        }
        $transaction = DB::transaction(function() use($request){
            $separador = '|';
            $plantilla = Plantilla::create(['nombre'=> $request->nombre, 'texto'=> $request->texto]);
            $campos = $this->getTemplateFields($request->texto, $separador);
            $this->insertPlantillaCampos($campos, false, $plantilla->id);
            
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
        $plantilla = Plantilla::findOrFail($id);
        return view('plantillas.edit', compact('plantilla'));
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
            $separador = '|';
            $plantilla = Plantilla::findOrFail($id);
            $plantilla->nombre = $request->nombre;
            $plantilla->texto = $request->texto;
            $band = $plantilla->save();

            $campos = $this->getTemplateFields($request->texto, $separador);
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

    public function getTemplateFields($texto, $char){
        $result = [];

        $pos = 1;
        while($pos != false){
            $pos = strpos($texto, $char);
            if($pos != false){
                $texto = substr($texto, $pos + 1);
                $pos2 = strpos($texto, $char);
                if($pos2 != false){
                    array_push($result, substr($texto, 0, $pos2));
                    $texto = substr($texto, $pos2 + 1);
                }
            }
        }
        return array_unique($result);
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
        $res = $plantilla->texto;
        $view = view('pdfs.archivo', compact('res', 'estado'));
        $view = preg_replace('/>\s+</', '><', $view);
        $pdf = \PDF::loadHTML($view);
        return $pdf->stream();
    }

    /***** Función usada en el CREATE de CASOS, se usa para traer los datos de una plantilla ****/
    public function getFieldsAndTemplateByTemplateId($plantillaId){
        $res = [];

        $query = "select pc.campo from plantillas_campos pc
                where pc.plantillaId = ".$plantillaId." order by pc.campo;";
        $res['campos'] = DB::select($query);
        $plantilla = Plantilla::findOrFail($plantillaId);
        $res['texto'] = $plantilla->texto;
        return $res;
    }
}
