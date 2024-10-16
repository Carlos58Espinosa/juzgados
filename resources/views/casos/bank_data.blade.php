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

        <form class="" action="{{action('CasosController@saveSensitiveData')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
            @csrf

        	<div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label for="">Nombre del Caso / Cliente:</label>
                  <p>{{$caso->nombre_cliente}}</p>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label for="">Configuración: </label> 
                  <p>{{$caso->configuracion->nombre}}</p>
                </div>
            </div>

            <br>

            <div style="margin-top: 50px;">    
              <div style="border: 1px solid gray;padding: 5px;border-radius: 10px" onclick="disabledDiv('camposSensibles')" style="padding: 5px;">
                <p style="float: left; width: 95%; padding: 5px">Sensibilidad de datos</p>
                <a href="#" align="right" class="btn btn-success" style="width:40px; height: 35px;"><i class="fas fa-arrow-down"></i></a>
              </div>  
                    
              <div id="camposSensibles">
              </div>
            </div>

            
            <br> 

            <input type="hidden" name="orden" id="orden"> 
            <input type="hidden" name="casoId" value="{{$caso->id}}">
            <input type="hidden" name="configuracionId" value="{{$caso->configuracionId}}">

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
  function getRowTableSensibility(nombre_campo, sensible){
      var html = "<tr>";
      html += '<td>'+nombre_campo+'</td>';
      html += '<td>';
      html += '<input style="margin-top:0px; margin-left:20px; transform: scale(1.5);" type="checkbox" ';
      html += ' class="check-active" ';
      html += 'name="'+nombre_campo+'_check" id="'+nombre_campo+'_check" ';
      if(sensible == 1)
          html += ' checked '; 
      html += '></td>';
      html += "</tr>";
      return html;
  }

  $(document).ready(function() {
      var campos = @json($campos);
      var contenedorDiv = document.getElementById('camposSensibles');
      document.getElementById("camposSensibles").innerHTML = "";  

      var cadHtml = '<div class="table-responsive table-striped table-bordered">';
      cadHtml += '<table class="table"><tr><th>Clave de uso</th><th>Sensibilidad <i style="margin-left:10px;" class="far fa-eye-slash"></i></th></tr><tbody>';
      for (var c of campos)
        cadHtml += getRowTableSensibility(c.campo, c.sensible);
      
      cadHtml += "</tbody></table></div>";
      document.getElementById("camposSensibles").innerHTML = cadHtml;
  });

  function disabledDiv(id){
    var control = document.getElementById(id);
    if(control.hidden)
      control.hidden = false;
    else
      control.hidden = true;
  }
  
</script>

@stop