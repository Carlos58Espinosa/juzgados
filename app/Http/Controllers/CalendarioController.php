<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calendario;
use App\Models\CalendarioUsuario;
use App\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
        $user = User::with('usuarios')->find($user_id);
        if($user->usuarios->isEmpty())
            $usuarios = [User::find($user->usuarioId)];
        else
            $usuarios = $user->usuarios;

        $expedientes_ctrl = new CasosController();
        $expedientes = $expedientes_ctrl->getCasesCollaborator($user_id, true);
        
        $query = Calendario::query();

        $fechaInicio = Carbon::now()->subMonth()->startOfMonth();

        $query->whereDate('fecha', '>=', $fechaInicio);


        /* =============================
        USUARIO LÍDER
        =============================*/
        if ($user->usuarioId == 0) {

            // obtener hijos
            $hijosIds = User::where('usuarioId', $user_id)->pluck('id');

            $query->where(function($q) use ($user_id, $hijosIds) {
                // eventos propios
                $q->where('usuarioId', $user_id)

                // eventos de hijos
                ->orWhereIn('usuarioId', $hijosIds)

                // eventos donde está asignado
                ->orWhereIn('id', function ($sub) use ($user_id) {
                    $sub->select('calendarioId')
                        ->from('calendario_usuarios')
                        ->where('usuarioId', $user_id);
                });
            });
        }
        /* =============================
        USUARIO NORMAL
        =============================*/
        else {
            $query->where(function($q) use ($user_id) {
                $q->where('usuarioId', $user_id)
                ->orWhereIn('id', function ($sub) use ($user_id) {
                    $sub->select('calendarioId')
                        ->from('calendario_usuarios')
                        ->where('usuarioId', $user_id);
                });
            });
        }

        /* =============================
        EJECUCIÓN + MAP
        =============================*/

        $events = $query->with('usuarios')->get()
        ->map(function($event){
            return [
                'id'    => $event->id,
                'title' => $event->titulo,
                'start' => $event->fecha,                

                'backgroundColor' => $event->estatus == 'alta' ? '#4e73df' : '#1cc88a',
                'borderColor'     => $event->estatus == 'alta' ? '#4e73df' : '#1cc88a',

                'extendedProps' => [
                    'usuarioId' => $event->usuarioId,
                    'estatus' => $event->estatus,
                    'usuarios' => $event->usuarios->pluck('usuarioId')->toArray(),
                    'observaciones' => $event->observaciones
                ]
            ];
        });
        return view('calendario.index', compact('events', 'usuarios', 'expedientes', 'user_id'));
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
        'estatus'   => 'alta',
        'observaciones' => $request->input('observaciones')
        ];

        $evento = Calendario::create($arr);
        $arr_usuarios = [['calendarioId' => $evento->id, 'usuarioId' => $evento->usuarioId]];

        foreach($request->input('usuarios', []) as $u_id){
            $arr_usuarios[] = [
                'calendarioId' => $evento->id,
                'usuarioId' => $u_id
            ];
        }

        CalendarioUsuario::insert($arr_usuarios);

        return response()->json([
            'ok' => true,
            'evento' => [
                'id'    => $evento->id,
                'title' => $evento->titulo,
                'start' => $evento->fecha,
                'estatus' => $evento->estatus,
                'observaciones' => $evento->observaciones,
                'usuarios' => array_merge(
                    [$evento->usuarioId],
                    $request->input('usuarios', [])
                ),
                'color' => '#4e73df' // color alta
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
        $user_sesion_id = \Auth::id();
        $evento = Calendario::findOrFail($id);
        //Actualiza todo por que fue quien lo creo
        if($user_sesion_id == $evento->usuarioId){
            $evento->fill($request->only(['estatus', 'titulo', 'fecha', 'observaciones']));

            CalendarioUsuario::where('calendarioId', $id)->delete();
            $arr_usuarios = [['calendarioId' => $id, 'usuarioId' => $evento->usuarioId]];

            foreach($request->input('usuarios', []) as $u_id){
                $arr_usuarios[] = [
                    'calendarioId' => $id,
                    'usuarioId' => $u_id
                ];
            }
            CalendarioUsuario::insert($arr_usuarios);
        } else{ // Es asignado solo puede editar estatus y observaciones
            $user = User::find($user_sesion_id);
            $evento->estatus = $request->input('estatus');
            if($request->input('observaciones_asignado') != "")
                $evento->observaciones .= "\n" . $user->nombre . ": " . $request->input('observaciones_asignado');
        }
        $evento->save();        

        return response()->json([
            'ok' => true,
            'evento' => [
                'id'    => $evento->id,
                'title' => $evento->titulo,
                'start' => $evento->fecha,
                'color' => $evento->estatus === 'alta' ? '#4e73df' : '#1cc88a'
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
        CalendarioUsuario::where('calendarioId', $id)->delete();
        Calendario::findOrFail($id)->delete();
        return response()->json([
            'ok' => true
        ]);
    }
}
