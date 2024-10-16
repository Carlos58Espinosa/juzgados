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

<!--
          <div class="col-4" align="right" style="margin-left:550px;">
            <form method="POST" action="{{action('PlantillasController@viewPdf')}}" target="_blank">
            @csrf
              <input type="hidden" id="textoPdf" name="texto" value="">
              <button class="btn btn-link m-0 p-0" style="width:200px;"><i class="far fa-file-pdf"></i></button>
            </form>
          </div>
-->


          <form class="" action="{{action('CasosController@store')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
            @csrf

            <!-- Contenedor de: Expediente, Configuración y Plantilla -->

            <div>

              <div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label for="">Expediente: <span style="color:red">*</span></label>
                  <input style="text-transform: none;" type="text" class="form-control @error('nombre_cliente') is-invalid @enderror input100" required name="nombre_cliente" value="{{old('nombre_cliente')}}">
                  @error('nombre_cliente')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label for="">Configuración: <span style="color:red">*</span></label>
                  <select id="select_config" onchange="getAndShowTemplatesByConfigId()"  class="form-control selectpicker @error('configuracion_id') is-invalid @enderror input100" name="configuracion_id" title="-- Selecciona una Configuración --" data-live-search="true">
                      @foreach($configuraciones as $config)
                        <option value="{{$config->id}}" {{ old('configuracion_id') == $config->id ? 'selected' : '' }}>{{$config->nombre}}</option>
                      @endforeach
                  </select>
                  @error('configuracion_id')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                </div>
              </div>

              <div id="div_plantillas" class="col-12 col-sm-6 col-md-4" hidden>
                <div class="form-group">
                  <label for="">Plantillas: <span style="color:red">*</span></label>
                  <select id="select_template" onchange="getAndShowFieldsByTemplateId()" class="form-control selectpicker" name="plantilla_id" title="-- Selecciona una Plantilla --" data-live-search="true">   
                  </select>
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