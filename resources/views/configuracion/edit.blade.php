@extends('layout')

@section('content')

@include('configuracion.config_methods')

<div>
    <a href="{{session('urlBack')}}" title="Regresar" class="btn boton_agregar"><i class="fas fa-long-arrow-alt-left"></i></a>
</div>

<form action="{{ action('ConfiguracionController@update', $configuracion->id) }}" method="post">
    @csrf
    <input type="hidden" name="_method" value="PUT">

    <input type="hidden" id="old_ids" name="old_ids" value="{{ implode(',', $old_ids) }}">

    <div>
        <button type="submit" class="btn boton_guardar" title="Guardar Registro"><i class="fa fa-save" alt="Guardar"></i></button>
    </div>

    <div class="container py-3" style="max-width:720px;">
        <label class="form-label">Nombre <span style="color:red">*</span></label>
        <input type="text" class="form-control" required name="nombre" value="{{ $configuracion->nombre }}">

        <br>

        <label class="form-label">Plantillas <span style="color:red">*</span></label>
        <select id="plantillas" class="form-select" multiple size="8">
            @foreach($plantillas as $p)
                <option value="{{ $p->id }}" {{ in_array($p->id, $plantillas_ids) ? 'selected' : '' }}>
                    {{ $p->nombre }}
                </option>
            @endforeach
        </select>

        <br>

        <ul id="lista" class="list-group"></ul>
    </div>
</form>
@stop