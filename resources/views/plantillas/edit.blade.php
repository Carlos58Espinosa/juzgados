@extends('layout')

@section('content')

@include('editor_summernote.summernote_methods')

<div class="main-content">
    <div class="section__content section__content--p30">
      <div class="container-fluid">
        <div class="card" id="card-section">

          <div>
            <a href="{{session('urlBack')}}" class="btn btn-info" style="width: 40px; margin-bottom: 10px;float: left"><i class="fas fa-long-arrow-alt-left"></i></a>
          </div>

          <form class="" action="{{action('PlantillasController@update', $plantilla->id)}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" value="PUT">

            <div class="row">

              <div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label for="">Nombre: <span style="color:red">*</span></label>
                  <input style="text-transform: none;" type="text" class="form-control @error('nombre') is-invalid @enderror input100" required name="nombre" value="{{$plantilla->nombre}}">
                  @error('nombre')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-12 col-sm-6 col-md-6">
                <div class="form-group">

                  <label for="">Texto / Contenido de la Plantilla: <span style="color:red">*</span></label>
                  <textarea required name="texto" id="summernote">{{$plantilla->texto}}</textarea>                  
                </div>
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