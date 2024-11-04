@extends('layout')

@section('content')

@include('casos.casos_methods')

@include('editor_summernote.summernote_methods')

  <form action="{{action('CasosController@update', $caso->id)}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
  @csrf
      <div>
          <a href="{{session('urlBack')}}" title="Regresar" class="btn boton_agregar"><i class="fas fa-long-arrow-alt-left"></i></a>
          <button type="submit" class="btn boton_guardar" title="Guardar"><i class="fa fa-save" alt="Guardar"></i></button>
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

          <div class="col-12 col-sm-6 col-md-4">
            <div class="form-group">
              <input type="hidden" name="configuracion_id" id="configuracion_id" value="{{$caso->configuracion->id}}">
              <input type="hidden" name="caso_id" id="caso_id" value="{{$caso->id}}">
              <label for="">Tipo de Procedimiento: </label> 
              <p value = "">{{$caso->configuracion->nombre}}</p>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-md-4">
            <div class="form-group">
              <input type="hidden" name="orden" id="orden">
              <label for="">Plantilla: <span style="color:red">*</span></label>
              <select id="select_template" onchange="getAndShowFieldsByTemplateId()" class="form-control selectpicker @error('plantilla_id') is-invalid @enderror" name="plantilla_id" title="-- Selecciona una Plantilla --" data-live-search="true">
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

      </div>
      <!-- --------------------------------------------------------------- -->
      <br>
      <br>
      <br>
      <br>
      @include('casos.form')

  </form>

@stop