<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Archivo;

class ArchivosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if( url()->previous() != url()->current() ){
            session()->forget('urlBack');
            session(['urlBack' => url()->previous()]);
        }
        $casoId = $request->caso_id;
        $archivos = Archivo::where('casoId', $casoId)->get();
        return view('casos.file_upload',compact('archivos', 'casoId'));   
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
            'archivo' => 'required'
        ]);

        if( url()->previous() != url()->current() ){
            session()->forget('urlBack');
            session(['urlBack' => url()->previous()]);
        }

        $usuario = \Auth::user();
        $file = $request->file('archivo');
        $fileName = $file->getClientOriginalName();
        $fileNameFinal = $usuario->id . '_' . date('Y_m_d_H_i_s') . '.' . $file->getClientOriginalExtension();
        $archivo = Archivo::create(['nombre' => $fileName, 'nombre_final' => $fileNameFinal, 'casoId' => $request->caso_id, 'tipo'=>$file->getClientOriginalExtension()]);
        \Storage::disk('public')->put($fileNameFinal,  \File::get($file));
        return $archivo;
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
        if( url()->previous() != url()->current() ){
            session()->forget('urlBack');
            session(['urlBack' => url()->previous()]);
        }
        $archivo = Archivo::findOrFail($id);
        \Storage::disk('public')->delete($archivo->nombre_final);
        Archivo::destroy($id);
        return true;
    }
}
