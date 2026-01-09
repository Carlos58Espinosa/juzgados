<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calendario;

class CalendarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = \Auth::user()->id;
        //$events = Calendario::where('usuarioId', $user_id)->get();
        $events = Calendario::where('usuarioId', $user_id)
        ->get()
        ->map(function($event){
            return [
                'id'    => $event->id,
                'title' => $event->titulo,
                'start' => $event->fecha,
                'color' => $event->estatus == 'pendiente' ? '#4e73df' : '#1cc88a' // ejemplo
            ];
        });
        return view('calendario.index', compact('events'));
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
       $arr = [
        'titulo'    => $request->input('titulo'),
        'fecha'     => $request->input('fecha'),
        'usuarioId' => \Auth::id(),
        'estatus'   => 'pendiente'
        ];

        $evento = Calendario::create($arr);

        // Devuelve el evento creado en formato compatible con FullCalendar
        return response()->json([
            'ok' => true,
            'evento' => [
                'id'    => $evento->id,
                'title' => $evento->titulo,
                'start' => $evento->fecha,
                'color' => '#4e73df' // color pendiente
            ]
        ]);
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
        $evento = Calendario::findOrFail($id);
        $evento->estatus = $request->input('estatus'); // recibe el estatus del request
        $evento->save();

        return response()->json([
            'ok' => true,
            'evento' => [
                'id'    => $evento->id,
                'title' => $evento->titulo,
                'start' => $evento->fecha,
                'color' => $evento->estatus === 'pendiente' ? '#4e73df' : '#1cc88a'
            ]
        ]);
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
}
