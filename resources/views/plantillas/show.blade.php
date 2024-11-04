@extends('layout')

@section('content')
<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">   
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

  <div>
      <a href="{{session('urlBack')}}" title="Regresar" class="btn boton_agregar"><i class="fas fa-long-arrow-alt-left"></i></a>
  </div>

  <div align="center">

      <label for="">Nombre: <span style="color:red">*</span></label>
      <input style="text-transform: none; width: 600px;" type="text" class="form-control @error('nombre') is-invalid @enderror" required name="nombre" value="{{$plantilla->nombre}}" id="nombre" disabled>
      @error('nombre')
        <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
      @enderror

      <br>

      <label>Texto / Contenido de la Plantilla: <span style="color:red">*</span></label>

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