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

        <form class="" action="{{action('CasosController@saveDataBank')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
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

            <div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label for="">Plantilla:</label>
                  <select id="select_plantilla" onchange="showDataBank(this.selectedIndex-1)"  class="form-control selectpicker @error('plantilla_id') is-invalid @enderror input100" name="plantilla_id" title="-- Selecciona una Plantilla --" data-live-search="true">
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

            <br>

            <div style="margin-top: 50px;">    
              <div style="border: 1px solid gray;padding: 5px;border-radius: 10px" onclick="disabledDiv('camposSensibles')" style="padding: 5px;">
                <p style="float: left; width: 95%; padding: 5px">Sensibilidad de datos</p>
                <a href="#" align="right" class="btn btn-success" style="width:40px; height: 35px;"><i class="fas fa-arrow-down"></i></a>
              </div>  
                    
              <div id="camposSensibles" hidden>
              </div>
            </div>

            <div id="contenedor_camposLLenar" style="margin-top: 20px;" hidden> 
                <div style="border: 1px solid gray;padding: 5px;border-radius: 10px" onclick="disabledDiv('camposLlenar')" style="padding: 5px;">
                  <p style="float: left; width: 95%; padding: 5px">Banco de datos de la Plantilla</p>
                  <a href="#" align="right" class="btn btn-success" style="width:40px; height: 35px;"><i class="fas fa-arrow-down"></i></a>
                </div>    

                <div style="margin-top:20px;" class="input-group mb-2">
                  <input id="nuevo_campo" type="text" placeholder="   Clave del campo" style="text-transform:none;width:300px; height: 30px;float:left;">
                  <a onclick="addField()" class="btn btn-info" style="width: 40px; margin-left:20px;"><i class="fas fa-plus"></i></a>                  
                </div>  

                <div id="camposLlenar">
                </div>
            </div>

            <br> 

            <input type="hidden" name="orden" id="orden"> 
            <input type="hidden" name="casoId" value="{{$caso->id}}">
            <input type="hidden" name="configuracionId" value="{{$caso->configuracionId}}">
            <input type="hidden" name="nuevos_campos_cad" id="nuevos_campos_cad" value="">

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
      html += '<td>|'+nombre_campo+'|</td>';
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

  function showDataBank(index){
    var control = document.getElementById('contenedor_camposLLenar').hidden = false;
    var plantillas = @json($plantillas);
    document.getElementById('orden').value = plantillas[index].orden;
    document.getElementById("nuevos_campos_cad").value = "";
    var campos_valores = plantillas[index].campos_valores;
    var htmlTable = '';

    if(campos_valores.length > 0){
      htmlTable += '<div class="table-responsive table-striped table-bordered">';
      htmlTable += '<table class="table"><tr><th>Clave de uso</th><th>Valor</th><th>Sin Guardar</th></tr>';
      for(var campo_val of campos_valores){
        htmlTable += "<tr>";
        htmlTable += '<td>|'+campo_val.campo+'|</td>';
        htmlTable += '<td>';
        htmlTable += '<input style="text-transform:none;width:600px;float:left;" ';
        htmlTable += 'type="text" class="form-control input100" ';
        htmlTable += 'name="'+campo_val.campo+'" id="'+campo_val.campo+'" ';

        if(campo_val.valor_plantilla == null || campo_val.valor_plantilla == ""){         
          if(campo_val.valor_ultimo != null && campo_val.valor_ultimo != "")
            htmlTable += 'value = "'+campo_val.valor_ultimo+'"';
        } else
            htmlTable += 'value = "'+campo_val.valor_plantilla+'"';
        htmlTable += ' required></td>';


        ///Chechar cuando los  dos son nulos
        if(campo_val.valor_ultimo == null && campo_val.valor_plantilla == null)
            htmlTable += '<td><i class="fas fa-times"></i></td>';
        else{
          if(campo_val.valor_ultimo != campo_val.valor_plantilla)
            htmlTable += '<td><i class="fas fa-times"></i></td>';
          else
            htmlTable += '<td></td>';
        }
        htmlTable += "</tr>";
      }
      htmlTable += "</table></div>";      
    }
    document.getElementById("camposLlenar").innerHTML = htmlTable;
    //console.log(plantillas[index].campos_valores);
  }

  function getRowTableFields(campo){
      var html = '<tr><td>|'+campo+"|</td>";
      html += '<td><input style="text-transform:none;width:600px;float:left;" ';
      html += 'type="text" class="form-control input100" ';
      html += 'name="'+campo+'" id="'+campo+'" required></td>';
      html += '<td><i class="fas fa-times"></i></td>';
      html += "</tr>";
      return html;
  }

  function addField(){
    var nuevo_campo = document.getElementById("nuevo_campo").value;
    var elemento_html = document.getElementById(nuevo_campo);
    
    if(nuevo_campo != "" && elemento_html == null){
      const arrAux = document.getElementById("nuevos_campos_cad").value.split(",");
      if(!arrAux.includes(nuevo_campo)){
        if(document.getElementById("nuevos_campos_cad").value !== "")
          document.getElementById("nuevos_campos_cad").value += ",";
        document.getElementById("nuevos_campos_cad").value += nuevo_campo;
        document.getElementById("nuevo_campo").value = "";

        document.getElementById("camposLlenar").getElementsByTagName('tbody')[0].insertRow().innerHTML = this.getRowTableFields(nuevo_campo);
        var value = this.getValueByKey(nuevo_campo);
        if(value != null)
            document.getElementById(nuevo_campo).value = value;

        var elemento_check_html = document.getElementById(nuevo_campo+"_check");
        if(elemento_check_html == null)
            document.getElementById("camposSensibles").getElementsByTagName('tbody')[0].insertRow().innerHTML = this.getRowTableSensibility(nuevo_campo, 0);
      }
    }
  }

  function getValueByKey(busqueda_campo){
    var res = null;
    var plantillas = @json($plantillas);
    var orden = document.getElementById('orden').value;
    for(var plantilla of plantillas){
      if(plantilla.orden <= orden){
          for(var campo_val of plantilla.campos_valores){
              if(campo_val.campo == busqueda_campo){
                  if(campo_val.valor_ultimo != null && campo_val.valor_ultimo != "")
                      res = campo_val.valor_ultimo;
              }
          }
      }else
        break;
    }
    return res;
  }

  
</script>

@stop