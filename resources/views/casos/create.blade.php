@extends('layout')

@section('content')
<!--
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">   
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
-->


@include('casos.methods')




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
                  <select id="select_template" onchange="getAndShowFieldsByTemplateId()"  class="form-control selectpicker" name="plantilla_id" title="-- Selecciona una Plantilla --" data-live-search="true">   
                  </select>
                </div>
              </div>

            </div>

            <!-- --------------------------------------------------------------- -->


            <!-- Contenedor de: Campos y Nuevo Campo-->

            <div id="div_campos">

              <br>
              <br>
              <br>
              
              <input type="hidden" name="nuevos_campos_cad" id="nuevos_campos_cad" value="">

              <label>Banco de Datos</label>

              <div id="div_nuevo_campo" style="margin-top: 20px;"> 
                <div style="margin-top:20px;" class="input-group mb-2">
                  <input id="nuevo_campo" type="text" placeholder="   Agregar Dato" style="text-transform:none;width:300px; height: 30px;float:left;">
                  <a onclick="addField()" class="btn btn-info" style="width: 40px; margin-left:20px;"><i class="fas fa-plus"></i></a>                  
                </div>  

                <div id="camposLlenar">
                </div>
              </div>

            </div>
            <!-- --------------------------------------------------------------- -->

            
            <!-- Contenedor de: Texto de Plantillas (SummerNote) -->
            <div id="div_textos_summernote" class="row" style="margin-top: 0px;" hidden>

                <div class="col-12 col-sm-6 col-md-6">
                  <div class="form-group">
                    <label for="">Vista Previa: </label>
                    <br>
                    <textarea style="height: 480px; width:300px" required name="texto_final" id="texto_final"></textarea>
                  </div>
                </div>

                <div class="col-12 col-sm-6 col-md-6">
                  <label for=""> Editar Plantilla: <input style="margin-top:20px; margin-left:10px; transform: scale(1.5);" type="checkbox" class="check-active" id="check_edit_template" onclick="hiddenTemplate()"></label>
                  <div id ="div_summernote" class="form-group">                    
                    <textarea required name="texto" id="summernote"></textarea>       
                  </div>
                </div>  

                <br>
                <br> 
                <br>        

                <div class="row">
                  <div class="form-group">
                    <button type="submit" class="btn btn-success">Guardar</button>
                  </div>
                </div>        

            </div>
            <!-- --------------------------------------------------------------- -->
          </form>   

        </div>
      </div>
    </div>
  </div>

  @stop