<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Grupo;
use App\Models\GrupoCampo;

class AgrupacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $res = [];
        $usuario = \Auth::user();
        switch($request->option){
            case 'fields_by_group':
                $res = DB::select("select campo from grupos_campos where grupoId = ".$request->grupo_id." order by campo;");
            break;
            default:
                $grupos = Grupo::where('usuarioId', $usuario->id)->get();
                $campos = $this->getAllFieldsWithoutGroup();
                $res = view('agrupacion.edit', compact('campos', 'grupos'));
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
        return DB::transaction(function() use($request){
            $grupo = Grupo::findOrFail($request->grupo_id);
            $campos = json_decode($request->campos, true);

            foreach($campos as $campo)
                GrupoCampo::create(['grupoId' => $grupo->id, 'campo' => $campo]);
            return $grupo->id;
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getAllFieldsWithoutGroup(){
        $qry = "select * from (select campo from plantillas_campos group by campo
                union
                select campo from casos_plantillas_campos group by campo) as t1 where t1.campo not in (select  campo from grupos_campos) group by campo order by campo;";
        return DB::select($qry);
    }

    public function deleteGroupsAndFields(Request $request){
        return DB::transaction(function() use($request){
            
            switch($request->option){
                case "field":  
                    $res['id'] = $request->nombre;
                    GrupoCampo::where('campo', $request->nombre)->delete();                
                break;
                case "group":
                    $res['id'] = $request->grupo_id;           
                    GrupoCampo::where('grupoId', $request->grupo_id)->delete();
                    Grupo::destroy($request->grupo_id);
                break;
            }  
            $res['campos'] = $this->getAllFieldsWithoutGroup();      
            return $res;
        });
    }

    public function addGroup(Request $request){
        return DB::transaction(function() use($request){
            $usuario = \Auth::user();
            $grupo = Grupo::create(['nombre' => $request->nombre, 'usuarioId' => $usuario->id]);
            return $grupo->id;
        });
    }

    public function addGroupsForNewUser($nuevoUsuarioId, $administradorId){
        $grupos = Grupo::with('campos')->where('usuarioId', $administradorId)->get();

        foreach($grupos as $grupo){
            $nuevoGrupo = Grupo::create(['nombre' => $grupo['nombre'], 'usuarioId' => $nuevoUsuarioId]);
            foreach($grupo->campos as $campo)
                GrupoCampo::create(['grupoId' => $nuevoGrupo->id, 'campo' => $campo->campo]);
        }
    }
}
