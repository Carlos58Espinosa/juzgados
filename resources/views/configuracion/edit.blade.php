@extends('layout')

@section('content')

@include('configuracion.config_methods')

<div class="main-content">
    <div class="section__content section__content--p30">
      <div class="container-fluid">
        <div class="card" id="card-section">

          <div>
            <a href="{{session('urlBack')}}" class="btn btn-info" style="width: 40px; margin-bottom: 10px;float: left"><i class="fas fa-long-arrow-alt-left"></i></a>
          </div>

          <form class="" action="{{action('ConfiguracionController@update', $configuracion->id)}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <div class="row">

              <div hidden>
                  <input  id="old_ids" name="old_ids[]" value="{{$old_ids[0]}}">
              </div>    

              <div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label for="">Nombre: <span style="color:red">*</span></label>
                  <input style="text-transform: none;" type="text" class="form-control @error('nombre') is-invalid @enderror input100" required name="nombre" value="{{$configuracion->nombre}}">
                  @error('nombre')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                </div>
              </div>    

              <div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label for="">Plantillas: <span style="color:red">*</span></label>
                  <select class="form-control selectpicker" data-style="form-control" data-live-search="true" title="-- Selecciona las Plantillas --" multiple="multiple" name="plantillas_ids[]" id="plantillas_ids_aux" onchange="addTemplateRowListGroup()">
                  @foreach($plantillas as $plantilla)
                    <option  {{ in_array($plantilla->id, $plantillas_ids)  ? 'selected':'' }}  value="{{$plantilla->id}}">{{$plantilla->nombre}}<a href="" class="btn btn-link" style="width:40px; margin: 0"><i class="far fa-eye"></i></a></option>
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

            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  @stop