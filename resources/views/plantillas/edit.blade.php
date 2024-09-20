@extends('layout')

@section('content')
<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">   
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

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
                  <input type="hidden" name="texto_aux" value="{{old('texto_aux')}}" id="texto_aux">

                  <label for="">Texto / Contenido de la Plantilla: <span style="color:red">*</span></label>
                  <textarea required name="texto" id="summernote">{{$plantilla->texto}}</textarea>                  
                </div>
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

  <script>
                    
    $(document).ready(function() {
      $('div.note-group-select-from-files').remove();
        $('#summernote').summernote(
          {
            disableDragAndDrop:true,
            height: 500,
            width: 630,
            toolbar: [
              ['style', ['style']],
              ['font', ['bold', 'underline', 'clear', 'italic', 'strikethrough']],
              ['color', ['color']],
              ['para', ['ul', 'ol', 'paragraph']],
              ['misc', ['undo', 'redo']],
              ['height', ['height']],
              //['table', ['table']],
              //['insert', ['link', 'picture', 'video']],
              //['view', ['fullscreen', 'codeview', 'help']]
            ],
            lineHeights: ['1.0', '1.2', '1.4', '1.6', '1.8', '2.0', '2.2', '2.4', '2.6', '2.8','3.0', '4.0', '5.0'],
          }
        );

        $('#summernote').on('summernote.change', function(we, contents, $editable) {
          document.getElementById("texto_aux").value = document.getElementById("summernote").value;
        });

        var valor_texto_aux = document.getElementById("texto_aux").value;
        if(valor_texto_aux != ""){
          if(document.getElementById("texto_aux"))
            $('#summernote').summernote('code', document.getElementById("texto_aux").value);
        }
    });
  </script>
  @stop