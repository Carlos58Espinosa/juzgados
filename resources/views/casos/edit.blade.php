@extends('layout')

@section('content')

@include('casos.casos_methods')
@include('editor_summernote.summernote_methods')

<div class="container-fluid">

<form action="{{ action('CasosController@update', $caso->id) }}" method="post" enctype="multipart/form-data">
@csrf

<div class="mb-3">
    <button type="submit" class="btn boton_guardar" title="Guardar Registro">
        <i class="fa fa-save"></i>
    </button>
</div>

<div class="row g-3">

    <!-- Expediente -->
    <div class="col-12 col-md-4">
        <label class="form-label">Expediente <span class="text-danger">*</span></label>
        <input type="hidden" id="caso_id" name="caso_id" value="{{ $caso->id }}">
        <input type="text"
               class="form-control @error('nombre_cliente') is-invalid @enderror"
               name="nombre_cliente"
               required
               value="{{ $caso->nombre_cliente }}">
        @error('nombre_cliente')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Tipo creación -->
    <div class="col-12 col-md-4">
        <label class="form-label">Tipo de Creación</label>
        <p class="form-control-plaintext">
            {{ $caso->tipo_creacion == "1" ? 'Libre' : 'Con Tipo de Procedimiento' }}
        </p>
    </div>

    @if($caso->tipo_creacion != "1")
    <div class="col-12 col-md-4">
        <label class="form-label">Tipo de Procedimiento</label>
        <input type="hidden" id="configuracion_id" value="{{ $caso->configuracionId }}">
        <p class="form-control-plaintext">{{ $caso->configuracion->nombre }}</p>
    </div>
    @endif

    <!-- Acciones -->
    <div class="col-12 col-md-4">
        <label class="form-label">Acciones <span class="text-danger">*</span></label>
        <select onchange="disableEditionElements(this.value)"
                class="form-select"
                name="accion_id">
            <option value="">-- Selecciona una Acción --</option>
            <option value="1">Seleccionar Plantilla</option>
            <option value="2">Editar Plantillas Contestadas</option>
        </select>
    </div>

    <!-- Plantillas nuevas -->
    <div id="div_plantillas" class="col-12 col-md-4 d-none">
        <input type="hidden" name="orden" id="orden_plantilla">
        <label class="form-label">Plantillas <span class="text-danger">*</span></label>
        <select id="select_template"
                onchange="getAndShowFieldsEditByTemplateId('nueva')"
                class="form-select"
                name="plantilla_id">
            <option value="">-- Selecciona una Plantilla --</option>
            @foreach($plantillas as $plantilla)
                <option value="{{ $plantilla->id }}">{{ $plantilla->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Plantillas contestadas -->
    <div id="div_plantillas_contestadas" class="col-12 col-md-4 d-none">
        <input type="hidden" name="orden" id="orden_contestada">
        <label class="form-label">Plantillas Contestadas <span class="text-danger">*</span></label>
        <select id="select_template_2"
                onchange="getAndShowFieldsEditByTemplateId('edicion')"
                class="form-select"
                name="caso_plantilla_id">
            <option value="">-- Selecciona una Plantilla --</option>
            @foreach($plantillas_contestadas as $plantilla)
                <option value="{{ $plantilla->id }}">{{ $plantilla->plantilla->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Detalle -->
    <div class="col-12 col-md-4">
        <label class="form-label">Detalle</label>
        <input type="text"
               class="form-control @error('detalle') is-invalid @enderror"
               name="detalle"
               id="detalle">
        @error('detalle')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>

<br>

@include('casos.form')

</form>

</div>
@stop
