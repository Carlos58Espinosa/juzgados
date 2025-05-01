@extends('layout')

@section('content')

@include('editor_summernote.summernote_methods')

  <form action="{{action('PlantillasController@store')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
  @csrf
      <div>
          <button type="submit" class="btn boton_guardar" title="Guardar Registro"><i class="fa fa-save" alt="Guardar"></i></button>
      </div>

      <div align="center">

          <label for="">Nombre: <span style="color:red">*</span></label>
          <input type="text" class="form-control @error('nombre') is-invalid @enderror input_nombre" required name="nombre" value="{{old('nombre')}}" id="nombre">
          @error('nombre')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
          @enderror

          <br>

          <label  for="">Texto / Contenido de la Plantilla: <span style="color:red">*</span></label>  
      </div>

      <div style="margin-left: 360px;"> 
          <textarea name="texto" id="summernote" value="{{old('texto')}}" required></textarea>
      </div>
      
  </form>
@stop