@extends('layout')

@section('content')

@include('configuracion.config_methods')

  <form class="" action="{{action('ConfiguracionController@store')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
  @csrf

      <div>
          <a href="{{session('urlBack')}}" title="Regresar" class="btn boton_agregar"><i class="fas fa-long-arrow-alt-left"></i></a>
          <button type="submit" class="btn boton_guardar" title="Guardar"><i class="fa fa-save" alt="Guardar"></i></button>
      </div>

      <input type ="hidden" id="old_ids" name="old_ids[]" value="{{old('old_ids')[0]}}">

      <div align="center">
            
          <label for="">Nombre: <span style="color:red">*</span></label>
          <input type="text" class="form-control @error('nombre') is-invalid @enderror input_nombre" name="nombre" value="{{old('nombre')}}" required>
          @error('nombre')
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
              </span>
          @enderror

          <br>

          <label for="">Plantillas: <span style="color:red">*</span></label>

          <br>

          <select class="selectpicker input_nombre" multiple="multiple" name="plantillas_ids[]" id="plantillas_ids_aux" title="-- Selecciona las Plantillas --" data-live-search="true" onchange="addTemplateRowListGroup()">
              @foreach($plantillas as $plantilla)
                <option {{ (collect(old('plantillas_ids'))->contains($plantilla->id)) ? 'selected':'' }}  value="{{$plantilla->id}}">{{$plantilla->nombre}}</option>
              @endforeach
          </select>

          <br>
          <br>
          <br>          

          <div id="div_list_group" style="width: 50%;">
              <nav>
                  <ul onclick="reorderArrayIds()" id="list_templates" class="list-group connectedSortable"> 
                  </ul>
              </nav>
          </div>

      </div>
            
  </form>
@stop