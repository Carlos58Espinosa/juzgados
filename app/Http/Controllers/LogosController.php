<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Logo;

class LogosController extends Controller
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
            case 'logos_caso':
                $query = "select l.* from logos l, casos_logos cl where l.id = cl.logoId and cl.casoId = " . $request->casoId;
                $res = DB::select($query);
            break;
            case 'logos_without_caso':
                $query = "select l.* from logos l where l.usuarioId = ".$request->usuarioId." and l.id not in (select logoId from casos_logos where casoId = ".$request->casoId.");";
                $res = DB::select($query);
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
        //
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
            'logo' => 'required|image|mimes:jpg,jpeg,png'
        ]);
        $usuario = \Auth::user();
        $file = $request->file('logo');
        $fileName = $file->getClientOriginalName();
        $fileNameFinal = $usuario->id . '_' . date('Y_m_d_H_i_s') . '.' . $file->getClientOriginalExtension();
        $logo = Logo::create(['nombre' => $fileName, 'usuarioId' => $usuario->id, 'nombre_final' => $fileNameFinal]);
        \Storage::disk('logos')->put($fileNameFinal,  \File::get($file));
        return $logo;
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $logo = Logo::findOrFail($id);
        \Storage::disk('logos')->delete($logo->nombre_final);
        Logo::destroy($id);
        return true;
    }
}
