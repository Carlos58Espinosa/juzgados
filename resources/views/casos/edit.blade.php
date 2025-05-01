@extends('layout')

@section('content')

@include('casos.casos_methods')

@include('editor_summernote.summernote_methods')

  <form action="{{action('CasosController@update', $caso->id)}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
  @csrf
      <div>
          <button type="submit" class="btn boton_guardar" title="Guardar Registro"><i class="fa fa-save" alt="Guardar"></i></button>
      </div>

      <br>

      <!-- Contenedor de: Expediente, Configuración y Plantilla -->
      <div>

          <div class="col-12 col-sm-6 col-md-4">
            <div class="form-group">
              <label for="">Expediente: <span style="color:red">*</span></label>
              <input type="hidden" name="caso_id" id="caso_id" value="{{$caso->id}}">
              <input style="text-transform: none;" type="text" class="form-control @error('nombre_cliente') is-invalid @enderror input100" required name="nombre_cliente" value="{{$caso->nombre_cliente}}">
              @error('nombre_cliente')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
            </div>
          </div>
          @if($caso->tipo_creacion == "1")
            <div class="col-12 col-sm-6 col-md-4">
              <div class="form-group">
                <label for="">Tipo de Creación: </label> 
                <p value = "">Libre</p>
              </div>
            </div>
          @else
            <div class="col-12 col-sm-6 col-md-4">
              <div class="form-group">
                <label for="">Tipo de Creación: </label> 
                <p value = "">Con Tipo de Procedimiento</p>
              </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4">
              <div class="form-group">
                <label for="">Tipo de Procedimiento: </label> 
                <input type="hidden" id="configuracion_id" value="{{$caso->configuracionId}}">
                <p>{{$caso->configuracion->nombre}}</p>
              </div>
            </div>
          @endif

          <div class="col-12 col-sm-6 col-md-4">
              <div class="form-group">
                <label for="">Acciones: <span style="color:red">*</span></label>
                <select onchange="disableEditionElements(this.value)" class="form-control selectpicker" title="-- Selecciona una Acción --" data-live-search="true" name="accion_id">   
                  <option value="1">Seleccionar Plantilla</option>
                  <option value="2">Editar Plantillas Contestadas</option>
                </select>
              </div>
          </div>

          <div id="div_plantillas"  class="col-12 col-sm-6 col-md-4" hidden>
            <div class="form-group">
              <input type="hidden" name="orden" id="orden">
              <label for="">Plantillas: <span style="color:red">*</span></label>
              <select id="select_template" onchange="getAndShowFieldsEditByTemplateId('nueva')" class="form-control selectpicker @error('plantilla_id') is-invalid @enderror" name="plantilla_id" title="-- Selecciona una Plantilla --" data-live-search="true">
                  @foreach($plantillas as $plantilla)
                    <option value="{{$plantilla->id}}">{{$plantilla->nombre}}</option>
                  @endforeach
              </select>
              @error('plantilla_id')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
            </div>
          </div>

          <div id="div_plantillas_contestadas"  class="col-12 col-sm-6 col-md-4" hidden>
            <div class="form-group">
              <input type="hidden" name="orden" id="orden">
              <label for="">Plantillas Contestadas: <span style="color:red">*</span></label>
              <select id="select_template_2" onchange="getAndShowFieldsEditByTemplateId('edicion')" class="form-control selectpicker @error('caso_plantilla_id') is-invalid @enderror" name="caso_plantilla_id" title="-- Selecciona una Plantilla --" data-live-search="true">
                  @foreach($plantillas_contestadas as $plantilla)
                    <option value="{{$plantilla->id}}">{{$plantilla->plantilla->nombre}}</option>
                  @endforeach
              </select>
              @error('caso_plantilla_id')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
            </div>
          </div>


        </div>


      <!-- --------------------------------------------------------------- -->
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>

      @include('casos.form')

  </form>

@stop