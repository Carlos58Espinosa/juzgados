@extends('layout')

@section('content')

@include('configuracion.config_methods')

  <form class="" action="{{action('ConfiguracionController@update', $configuracion->id)}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
  @csrf

      <div>
          <button type="submit" class="btn boton_guardar" title="Guardar Registro"><i class="fa fa-save" alt="Guardar"></i></button>
      </div>

      <br>
            
      <input type="hidden" name="_method" value="PUT">
      <input type="hidden"  id="old_ids" name="old_ids[]" value="{{$old_ids[0]}}">

      <div align="center">

          <label for="">Nombre: <span style="color:red">*</span></label>
          <input style="text-transform: none;" type="text" class="form-control @error('nombre') is-invalid @enderror input_nombre" required name="nombre" value="{{$configuracion->nombre}}">
          @error('nombre')
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
              </span>
          @enderror  

          <br> 

          <label for="">Plantillas: <span style="color:red">*</span></label>

          <br>

          <select class="selectpicker input_nombre" data-style="form-control" data-live-search="true" title="-- Selecciona las Plantillas --" multiple="multiple" name="plantillas_ids[]" id="plantillas_ids_aux" onchange="addTemplateRowListGroup()">
              @foreach($plantillas as $plantilla)
                <option  {{ in_array($plantilla->id, $plantillas_ids)  ? 'selected':'' }}  value="{{$plantilla->id}}">{{$plantilla->nombre}}<a href="" class="btn btn-link" style="width:40px; margin: 0"><i class="far fa-eye"></i></a></option>
              @endforeach
          </select>

          <br>
          <br>
          <br>

            <div id="div_list_group" style="width: 50%;">
                <nav>
                  <ul onclick="reorderArrayIds()" id="list_templates" class="list-group connectedSortable"> </ul>
                </nav>
            </div> 

      </div>
  </form>

  @stop