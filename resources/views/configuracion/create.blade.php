@extends('layout')

@section('content')

@include('configuracion.config_methods')

<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>

<div class="main-content">
    <div class="section__content section__content--p30">
      <div class="container-fluid">
        <div class="card" id="card-section">

          <div>
            <a href="{{session('urlBack')}}" class="btn btn-info" style="width: 40px; margin-bottom: 10px;float: left"><i class="fas fa-long-arrow-alt-left"></i></a>
          </div>

          <form class="" action="{{action('ConfiguracionController@store')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
            @csrf

            <div class="row">

              <input type ="hidden" id="old_ids" name="old_ids[]" value="{{old('old_ids')[0]}}">

              <div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label for="">Nombre: <span style="color:red">*</span></label>
                  <input style="text-transform: none;" type="text" class="form-control @error('nombre') is-invalid @enderror input100" required name="nombre" value="{{old('nombre')}}">
                  @error('nombre')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                </div>
              </div> 

              <div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label for="">Plantillas:</label>
                  <select class="form-control selectpicker" multiple="multiple" name="plantillas_ids[]" id="plantillas_ids_aux" title="-- Selecciona las Plantillas --" data-live-search="true"
                  onchange="addTemplateRowListGroup()">
                  @foreach($plantillas as $plantilla)
                    <option {{ (collect(old('plantillas_ids'))->contains($plantilla->id)) ? 'selected':'' }}  value="{{$plantilla->id}}">{{$plantilla->nombre}}</option>
                  @endforeach
                  </select>
                </div>
              </div>              

            </div>

            <div id="div_list_group">
              <nav>
                <ul onclick="reorderArrayIds()" id="list_templates" class="list-group connectedSortable"> </ul>
              </nav>
            </div>

            <div class="col-12">
              <div class="form-group">
                <button type="submit" class="btn btn-success">Guardar</button>
              </div>
            </div>
            
          </form>
        </div>
      </div>
    </div>
  </div>
@stop