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

        <form class="" action="{{action('CasosController@update', $caso->id)}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
            @csrf

        	<div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label for="">Nombre del Caso / Cliente: <span style="color:red">*</span></label>
                  <input style="text-transform: none;" type="text" class="form-control @error('nombre_cliente') is-invalid @enderror input100" required name="nombre_cliente" value="{{$caso->nombre_cliente}}">
                  @error('nombre_cliente')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label for="">Configuración: </label> 
                  <p>{{$caso->configuracion->nombre}}</p>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label for="">Plantilla: <span style="color:red">*</span></label>
                  <select id="select_plantilla" onchange="showConfigInfo(this.selectedIndex)"  class="form-control selectpicker @error('plantilla_id') is-invalid @enderror input100" name="plantilla_id" title="-- Selecciona una Plantilla --" data-live-search="true">
                      @foreach($plantillas as $plantilla)
                        <option value="{{$plantilla->plantillaId}}">{{$plantilla->plantilla->nombre}}</option>
                      @endforeach
                  </select>
                  @error('plantilla_id')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                </div>
            </div>

            <div hidden>
                <input  id="orden" name="orden" >
                <input  id="plantilla_id" name="plantilla_id" >
                <input  id="index">
            </div>

            <div style="margin-top: 50px;">
                <label for="">Banco de Datos:</label>
                <div id="camposLlenar">
                </div>
            </div>

            <br>
 
              <div id="div_template" class="row" style="margin-top: 100px;">
                <div class="col-12 col-sm-6 col-md-6">
                  <div class="form-group">
                    <label for="">Vista Previa: </label>
                    <br>
                    <textarea style="height: 480px; width:300px" required name="texto_final" id="texto_final"></textarea>
                  </div>
                </div>

                <div class="col-12 col-sm-6 col-md-6">
                  <label for=""> Editar Plantilla: <input style="margin-top:15px; margin-left:10px; transform: scale(1.5);" type="checkbox" class="check-active" id="check_edit_template" onclick="hiddenTemplate()"></label>
                  <div id ="div_summernote" class="form-group">                    
                    <textarea required name="texto" id="summernote"></textarea>       
                  </div>
                </div>

                
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

<script>

 $(document).ready(function() {
      document.getElementById("div_template").hidden = true;

      $('div.note-group-select-from-files').remove();
      $('#summernote').summernote(
          {
            disableDragAndDrop:true,
            height: 350,
            toolbar: [
              ['style', ['style']],
              ['font', ['bold', 'underline', 'clear']],
              ['color', ['color']],
              ['para', ['ul', 'ol', 'paragraph']]
              //['table', ['table']],
              //['insert', ['link', 'picture', 'video']],
              //['view', ['fullscreen', 'codeview', 'help']]
            ],
            /*callbacks: {
                onChange: function(contents, $editable) {
                console.log('onChange:', contents, $editable);
              }
            }*/
          }
      );

      $('#summernote').on('summernote.change', function(we, contents, $editable) {
          //console.log('summernote.change', contents, $editable);
          replaceText("", document.getElementById("index").value);
      });

      document.getElementById("div_summernote").hidden = true;

      $('#texto_final').summernote(
          {
            disableDragAndDrop:true,
            height: 400,
            toolbar: [
            ],
          }
        );
      $('#texto_final').summernote('disable');
    });


 function hiddenTemplate(){
        if (document.getElementById('check_edit_template').checked)
          document.getElementById("div_summernote").hidden = false;
        else
          document.getElementById("div_summernote").hidden = true;
    }


function showConfigInfo(index){
	document.getElementById("div_template").hidden = false;
	var plantillas = @json($plantillas);
	document.getElementById("index").value = index;
  document.getElementById("orden").value = plantillas[index-1].orden;
    document.getElementById("plantilla_id").value = plantillas[index-1].plantillaId;
    $('#summernote').summernote('code', plantillas[index-1].plantilla.texto);
    $('#texto_final').summernote('code', plantillas[index-1].plantilla.texto);
    var contenedorDiv = document.getElementById('camposLlenar');
    document.getElementById("camposLlenar").innerHTML = "";  
    var cadHtml = "";

    for (var campo of plantillas[index-1].plantilla_campos){
      console.log(campo);
        cadHtml += '<div class="col-12 col-sm-6 col-md-6">';
        cadHtml += '<input style="text-transform:none;width:300px;float:left;" ';
        cadHtml += 'type="text" class="form-control input100" ';
        cadHtml += 'name="'+campo.nombre+'" id="'+campo.nombre+'" placeholder="'+campo.nombre+'" value="'+campo.valor+'" ';
        cadHtml += 'onkeyup="replaceText(\''+String(campo.nombre)+'\','+index+')" ';
        //cadHtml += 'onkeyup="replaceText('+index+')" value="" ';
        cadHtml += '>' ;

        cadHtml += '<input style="margin-top:15px; margin-left:20px; transform: scale(1.5);" type="checkbox" ';
        cadHtml += ' class="check-active" ';
        cadHtml += 'name="'+campo.nombre+'_check" id="'+campo.nombre+'_check" ';
        if(campo.sensible==1)
        	cadHtml += 'checked';
        cadHtml += '>';
        cadHtml += '<i style="margin-left:10px;" class="far fa-eye-slash"></i>';
        cadHtml += "</div>";
    }
    document.getElementById("camposLlenar").innerHTML = cadHtml;
    this.replaceText("",index);
}

	function replaceText(nombre,index){
      var textoAux = document.getElementById("summernote").value;
      //console.log("Texto summernote:"+textoAux);
      var plantillas = @json($plantillas);
      //var textoAux = configInfo[index-1].plantillaInfo.texto;

      for (const campo of plantillas[index-1].plantilla_campos){
        //console.log("Nombre del campo:"+campo.nombre);
        var element = document.getElementById(campo.nombre);
        //console.log(element);
        if(element != null){
            if(element.value != "")
                textoAux = textoAux.replaceAll("|"+campo.nombre+"|", element.value);
        }
      }
      $('#texto_final').summernote('code', textoAux);
    }
</script>

@stop