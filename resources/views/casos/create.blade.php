@extends('layout')

@section('content')

@include('casos.casos_methods')

@include('editor_summernote.summernote_methods')

  <form action="{{action('CasosController@store')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
  @csrf

      <div>
          <button type="submit" class="btn boton_guardar" title="Guardar Registro"><i class="fa fa-save" alt="Guardar"></i></button>         
      </div>

      <br>

      <!-- Contenedor de: Expediente, Configuración y Plantilla -->
      <div class="container">

        <div class="row">

          <div class="col-12 col-sm-6 col-md-4 mb-3">
            <label>Expediente: <span class="text-danger">*</span></label>
            <input type="text"
                  class="form-control @error('nombre_cliente') is-invalid @enderror input100"
                  required name="nombre_cliente"
                  value="{{old('nombre_cliente')}}">
            @error('nombre_cliente')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-12 col-sm-6 col-md-4 mb-3">
            <label>Tipo de Creación: <span class="text-danger">*</span></label>
            <select id="select_tipo"
                    onchange="getTemplatesByType(this.value)"
                    class="form-select @error('select_tipo') is-invalid @enderror"
                    name="tipo_creacion">
              <option value="">-- Tipo de Creación --</option>
              <option value="1">Libre (Todas las Plantillas)</option>
              <option value="2">Tipo de Procedimiento</option>
            </select>
          </div>

          <div class="col-12 col-sm-6 col-md-4 mb-3" id="div_select_config">
            <label>Tipo de Procedimiento: <span class="text-danger">*</span></label>
            <select id="select_config"
                    onchange="getAndShowTemplatesByConfigId()"
                    class="form-select @error('configuracion_id') is-invalid @enderror"
                    name="configuracion_id">
              @foreach($configuraciones as $config)
                <option value="{{$config->id}}" {{ old('configuracion_id') == $config->id ? 'selected' : '' }}>
                  {{$config->nombre}}
                </option>
              @endforeach
            </select>
          </div>

          <div id="div_plantillas" class="col-12 col-sm-6 col-md-4 mb-3">
            <label>Plantillas: <span class="text-danger">*</span></label>
            <select id="select_template"
                    onchange="fetchTemplateFields({ plantillaId: this.value })"
                    class="form-select"
                    name="plantilla_id">
            </select>
          </div>

          <div id="div_plantillas2" class="col-12 col-sm-6 col-md-4 mb-3">
            <label>Plantillas: <span class="text-danger">*</span></label>
            <select id="select_template_2"
                    onchange="fetchTemplateFields({ plantillaId: this.value })"
                    class="form-select"
                    name="plantilla_id_2">
              @foreach($plantillas_all as $plantilla)
                <option value="{{$plantilla->id}}" {{ old('plantilla_id_2') == $plantilla->id ? 'selected' : '' }}>
                  {{$plantilla->nombre}}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-sm-6 col-md-4 mb-3">
            <label>Detalle:</label>
            <input type="text"
                  class="form-control @error('detalle') is-invalid @enderror input100"
                  name="detalle"
                  id="detalle"
                  value="{{old('detalle')}}">
            @error('detalle')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

        </div>

      </div>

      @include('casos.form')
      
 </form>

@stop