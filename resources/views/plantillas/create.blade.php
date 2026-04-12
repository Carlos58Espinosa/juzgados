@extends('layout')

@section('content')

<div>
    <a href="{{ session('urlBack') }}" title="Regresar" class="btn boton_agregar">
        <i class="fas fa-long-arrow-alt-left"></i>
    </a>
</div>

@include('plantillas.form', [
    'formAction' => action('PlantillasController@store'),
    'isEdit' => false,
    'nombreValue' => old('nombre', ''),
    'textoValue' => old('texto', '')
])

@include('editor_summernote.summernote_methods')

@stop