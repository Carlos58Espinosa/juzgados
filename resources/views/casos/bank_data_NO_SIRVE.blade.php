@extends('layout')

@section('content')

<div class="main-content">
    <div class="section__content section__content--p30">
      <div class="container-fluid">
        <div class="card" id="card-section">

          <div>
            <a href="{{session('urlBack')}}" class="btn btn-info" style="width: 40px; margin-bottom: 10px;float: left"><i class="fas fa-long-arrow-alt-left"></i></a>
          </div>

          <form class="" action="{{action('CasosController@saveDataBank')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
            @csrf
              <div>
                <input type="hidden" id="plantilla_id" name="plantilla_id" value="{{$plantillaId}}">
                <input type="hidden" id="caso_id" name="caso_id" value="{{$caso->id}}">
                <input type="hidden" id="configuracion_id" name="configuracion_id" value="{{$caso->configuracionId}}">


                <label for="">Banco de Datos Iniciales:</label>
                <div id="camposLlenar">
            
                </div>
              </div>

              <div style="margin-top:150px;" class="col-12">
                <div class="form-group">
                  <button type="submit" class="btn btn-success">Save</button>
                </div>
              </div>
          </form>
		    </div>
      </div>
    </div>
</div>

<script>
  $(document).ready(function() {
      var campos = @json($campos);
      var contenedorDiv = document.getElementById('camposLlenar');
      document.getElementById("camposLlenar").innerHTML = "";  
      var cadHtml = "";

      for (var campo of campos){
          //console.log(campo);
          cadHtml += '<div class="col-12 col-sm-6 col-md-6">';
          cadHtml += '<input style="text-transform:none;width:300px;float:left;" ';
          cadHtml += 'type="text" class="form-control input100" ';
          cadHtml += 'name="'+campo.nombre+'" id="'+campo.nombre+'" placeholder="'+campo.nombre+'" ';
          if(campo.valor != null)
            cadHtml += 'value = "'+campo.valor+'"';
          cadHtml += '>' ;

          cadHtml += '<input style="margin-top:15px; margin-left:20px; transform: scale(1.5);" type="checkbox" ';
          cadHtml += ' class="check-active" ';
          cadHtml += 'name="'+campo.nombre+'_check" id="'+campo.nombre+'_check" ';
          if(campo.sensible == 1)
            cadHtml += ' checked '; 
          cadHtml += '><i style="margin-left:10px;" class="far fa-eye-slash"></i>';
          cadHtml += "</div>";
      }
      document.getElementById("camposLlenar").innerHTML = cadHtml;

  });
</script>

 @stop