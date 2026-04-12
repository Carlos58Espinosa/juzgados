@extends('layout')

@section('content')

<div>
    <a href="{{ session('urlBack') }}" title="Regresar" class="btn boton_agregar">
        <i class="fas fa-long-arrow-alt-left"></i>
    </a>
</div>

@include('plantillas.form', [
    'formAction' => action('PlantillasController@update', $plantilla->id),
    'isEdit' => true,
    'nombreValue' => old('nombre', $plantilla->nombre),
    'textoValue' => old('texto', $plantilla->texto)
])
@include('editor_summernote.summernote_methods')
@stop