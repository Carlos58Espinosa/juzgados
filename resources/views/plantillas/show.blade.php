@extends('layout')

@section('content')

  <div>
    <a href="{{session('urlBack')}}" title="Regresar" class="btn boton_agregar"><i class="fas fa-long-arrow-alt-left"></i></a>
  </div>

  <div align="center">

      <label for="">Nombre:</label>
      <input style="text-transform: none; width: 600px;" type="text" class="form-control @error('nombre') is-invalid @enderror" required name="nombre" value="{{$plantilla->nombre}}" id="nombre" disabled>
      @error('nombre')
        <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
      @enderror

      <br>

      <label>Texto / Contenido de la Plantilla:</label>

  </div>

  

  <div style="margin-left: 360px;">  
      <textarea name="texto" id="summernote">{{$plantilla->texto}}</textarea>
  </div>

<script>                  
    $(document).ready(function() {
        $('#summernote').summernote(
          {
            disableDragAndDrop:true,
            height: 500,
            width: 630,
            toolbar: [
              //['style', ['style']],
              //['font', ['bold', 'underline', 'clear']],
              //['color', ['color']],
              //['para', ['ul', 'ol', 'paragraph']]
              //['table', ['table']],
              //['insert', ['link', 'picture', 'video']],
              //['view', ['fullscreen', 'codeview', 'help']]
            ]

          }
        );
        $('#summernote').summernote('disable');
    });
</script>
  @stop