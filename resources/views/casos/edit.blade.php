@extends('layout')

@section('content')

@include('casos.casos_methods')

@include('editor_summernote.summernote_methods')

<div class="main-content">
    <div class="section__content section__content--p30">
      <div class="container-fluid">
        <div class="card" id="card-section">

          <div>
            <a href="{{session('urlBack')}}" class="btn btn-info" style="width: 40px; margin-bottom: 10px;float: left"><i class="fas fa-long-arrow-alt-left"></i></a>
          </div>

        <form class="" action="{{action('CasosController@update', $caso->id)}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
            @csrf

          <!-- Contenedor de: Expediente, Configuración y Plantilla -->
          <div>

              <div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label for="">Nombre del Caso / Cliente: <span style="color:red">*</span></label>
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
                  <label for="">Configuración: </label> 
                  <p value = "">{{$caso->configuracion->nombre}}</p>
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <input type="hidden" name="orden" id="orden">
                  <label for="">Plantilla: <span style="color:red">*</span></label>
                  <select id="select_template" onchange="getAndShowFieldsByTemplateId()" class="form-control selectpicker @error('plantilla_id') is-invalid @enderror input100" name="plantilla_id" title="-- Selecciona una Plantilla --" data-live-search="true">
                      @foreach($plantillas as $plantilla)
                        <option value="{{$plantilla->plantillaId}}">{{$plantilla->plantilla->nombre}}</option>
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

        </div>
      </div>
    </div>
</div>



@stop