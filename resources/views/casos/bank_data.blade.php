@extends('layout')

@section('content')
  
  <form class="" action="{{action('CasosController@saveSensitiveData')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
  @csrf

      <div>
          <a href="{{session('urlBack')}}" title="Regresar" class="btn boton_agregar"><i class="fas fa-long-arrow-alt-left"></i></a>
          <button type="submit" class="btn boton_guardar" title="Guardar Registro"><i class="fa fa-save" alt="Guardar"></i></button>
      </div>

      <br>

      <div class="row">

      	  <div style="margin-left:200px;" class="col-12 col-sm-6 col-md-4">
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

      </div>

      <br>

      <div class="row" align="center">
         
          <label>Sensibilidad de datos</label>

      </div>

      <br>

      <div>    
              
        <div id="camposSensibles"></div>

      </div>

            
      <br> 

      <input type="hidden" name="orden" id="orden"> 
      <input type="hidden" name="casoId" value="{{$caso->id}}">
      <input type="hidden" name="configuracionId" value="{{$caso->configuracionId}}">

  </form>


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

      var cadHtml = '<table class="table" style="width: 50%; margin-left: 400px;"><tr><th>Párametro</th><th width="150px">Sensibilidad <i style="margin-left:10px; color:lightcoral;" class="far fa-eye-slash"></i></th></tr><tbody>';
      for (var c of campos)
        cadHtml += getRowTableSensibility(c.campo, c.sensible);
      
      cadHtml += "</tbody></table>";
      document.getElementById("camposSensibles").innerHTML = cadHtml;
  });
  
</script>

@stop