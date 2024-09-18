@extends('layout')

@section('content')

@include('casos.methods')


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
                  <label for="">Configuración: </label> 
                  <p value = "">{{$caso->configuracion->nombre}}</p>
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <input type="hidden" name="orden" id="orden">
                  <label for="">Plantilla: <span style="color:red">*</span></label>
                  <select id="select_template" onchange="getAndShowFieldsByTemplateIdEdit({{@json_encode($plantillas)}})"  class="form-control selectpicker @error('plantilla_id') is-invalid @enderror input100" name="plantilla_id" title="-- Selecciona una Plantilla --" data-live-search="true">
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