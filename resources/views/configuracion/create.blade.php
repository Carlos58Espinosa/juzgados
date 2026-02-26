@extends('layout')

@section('content')
@include('configuracion.config_methods')

<div>
    <a href="{{ session('urlBack') }}" title="Regresar" class="btn boton_agregar">
        <i class="fas fa-long-arrow-alt-left"></i>
    </a>
</div>

@include('configuracion.form', [
    'formAction' => action('ConfiguracionController@store'),
    'isEdit' => false,
    'nombreValue' => old('nombre', ''),
    'selectedPlantillas' => old('plantillas_ids', []),
    'oldIds' => ''
])
@stop