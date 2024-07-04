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

            <div class="row">

              <div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label for="">Nombre: <span style="color:red">*</span></label>
                  <input style="text-transform: none;" type="text" class="form-control @error('nombre') is-invalid @enderror input100" name="nombre" value="{{$plantilla->nombre}}" disabled>
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
                  <textarea id="summernote" disabled>{{$plantilla->texto}}</textarea>
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
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
  @stop