@extends('layout')

@section('content')

<!--
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">   
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
-->

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>


<div class="main-content">
    <div class="section__content section__content--p30">
      <div class="container-fluid">
        <div class="card" id="card-section">

          <div>
            <a href="{{session('urlBack')}}" class="btn btn-info" style="width: 40px; margin-bottom: 10px;float: left"><i class="fas fa-long-arrow-alt-left"></i></a>
          </div>

<!--
          <div class="col-4" align="right" style="margin-left:550px;">
            <form method="POST" action="{{action('PlantillasController@viewPdf')}}" target="_blank">
            @csrf
              <input type="hidden" id="textoPdf" name="texto" value="">
              <button class="btn btn-link m-0 p-0" style="width:200px;"><i class="far fa-file-pdf"></i></button>
            </form>
          </div>
-->


          <form class="" action="{{action('CasosController@store')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
            @csrf

            <!-- Contenedor de: Expediente, Configuración y Plantilla -->

            <div>

              <div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label for="">Expediente: <span style="color:red">*</span></label>
                  <input style="text-transform: none;" type="text" class="form-control @error('nombre_cliente') is-invalid @enderror input100" required name="nombre_cliente" value="{{old('nombre_cliente')}}">
                  @error('nombre_cliente')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label for="">Configuración: <span style="color:red">*</span></label>
                  <select id="select_config" onchange="getAndShowTemplatesByConfigId()"  class="form-control selectpicker @error('configuracion_id') is-invalid @enderror input100" name="configuracion_id" title="-- Selecciona una Configuración --" data-live-search="true">
                      @foreach($configuraciones as $config)
                        <option value="{{$config->id}}" {{ old('configuracion_id') == $config->id ? 'selected' : '' }}>{{$config->nombre}}</option>
                      @endforeach
                  </select>
                  @error('configuracion_id')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                </div>
              </div>

              <div id="div_plantillas" class="col-12 col-sm-6 col-md-4" hidden>
                <div class="form-group">
                  <label for="">Plantillas: <span style="color:red">*</span></label>
                  <select id="select_template" onchange="getAndShowFieldsByTemplateId()"  class="form-control selectpicker" name="plantilla_id" title="-- Selecciona una Plantilla --" data-live-search="true">   
                  </select>
                </div>
              </div>

            </div>

            <!-- --------------------------------------------------------------- -->


            <!-- Contenedor de: Campos y Nuevo Campo-->

            <div id="div_campos">

              <br>
              <br>
              <br>
              
              <input type="hidden" name="nuevos_campos_cad" id="nuevos_campos_cad" value="">

              <label>Banco de Datos</label>

              <div id="div_nuevo_campo" style="margin-top: 20px;"> 
                <div style="margin-top:20px;" class="input-group mb-2">
                  <input id="nuevo_campo" type="text" placeholder="   Agregar Dato" style="text-transform:none;width:300px; height: 30px;float:left;">
                  <a onclick="addField()" class="btn btn-info" style="width: 40px; margin-left:20px;"><i class="fas fa-plus"></i></a>                  
                </div>  

                <div id="camposLlenar">
                </div>
              </div>

            </div>
            <!-- --------------------------------------------------------------- -->

            
            <!-- Contenedor de: Texto de Plantillas (SummerNote) -->
            <div id="div_textos_summernote" class="row" style="margin-top: 0px;" hidden>

                <div class="col-12 col-sm-6 col-md-6">
                  <div class="form-group">
                    <label for="">Vista Previa: </label>
                    <br>
                    <textarea style="height: 480px; width:300px" required name="texto_final" id="texto_final"></textarea>
                  </div>
                </div>

                <div class="col-12 col-sm-6 col-md-6">
                  <label for=""> Editar Plantilla: <input style="margin-top:20px; margin-left:10px; transform: scale(1.5);" type="checkbox" class="check-active" id="check_edit_template" onclick="hiddenTemplate()"></label>
                  <div id ="div_summernote" class="form-group">                    
                    <textarea required name="texto" id="summernote"></textarea>       
                  </div>
                </div>  

                <br>
                <br> 
                <br>        

                <div class="row">
                  <div class="form-group">
                    <button type="submit" class="btn btn-success">Guardar</button>
                  </div>
                </div>        

            </div>
            <!-- --------------------------------------------------------------- -->
          </form>   

        </div>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/pdf-lib"></script>
  <script src="{{ asset('js/sweetalert.js') }}"></script>

  <script>
    $(document).ready(function() {

      document.getElementById("div_campos").hidden = true;
      document.getElementById("div_textos_summernote").hidden = true;      


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
          replaceText();
      });

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

    /******** Combo de Configuración *******/
    function getAndShowTemplatesByConfigId(){
      //console.log("Entre a: showConfigInfo");
      var url_templates = "{{action('ConfiguracionController@index')}}";
      var configId = document.getElementById("select_config").value;

      document.getElementById("div_plantillas").hidden = false;
      document.getElementById("div_campos").hidden = true;
      document.getElementById("div_textos_summernote").hidden = true;      


      $.ajax({
        dataType: 'json',
        type:'GET',
        url: url_templates,
        cache: false,
        data: {'option' : "templates_by_config_id", 'configId' : configId,'_token':"{{ csrf_token() }}"},
        success: function(data){
          fillSelectTemplates(data);
          //toastr.success('Información actualizada correctamente.', '', {timeOut: 3000});
        },
        error: function(){
          toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
        }
      });
    }

    function fillSelectTemplates(templates){
      //console.log("Entre a:fillSelectTemplates");
      $("#select_template").empty();
      for(template of templates){        
        var cad = template.plantilla.nombre;
        var id = template.plantilla.id;
        $("#select_template").append('<option value="'+id+'">'+cad+'</option>');
      }
      $("#select_template").selectpicker("refresh");
    }

    /******** Combo de Templates *******/
    function getAndShowFieldsByTemplateId(){
      //console.log("Entre a:getAndShowFieldsByTemplateId");
      var templateId = document.getElementById("select_template").value;
      var url = "{{action('PlantillasController@index')}}";

      $.ajax({
        dataType: 'json',
        type:'GET',
        url: url,
        cache: false,
        data: {'option' : "fields_text_by_template_id", 'plantillaId' : templateId,'_token':"{{ csrf_token() }}"},
        success: function(data){
          showFieldsAndTemplate(data);
          //toastr.success('Información actualizada correctamente.', '', {timeOut: 3000});
        },
        error: function(){
          toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
        }
      });
    }

    /***** Muestra los campos de la plantilla y el Texto de la Plantilla. *****/
    function showFieldsAndTemplate(data){
        //console.log(data);
        this.hiddenTemplate();
        document.getElementById("div_campos").hidden = false;
        document.getElementById("div_textos_summernote").hidden = false;

        var contenedorDiv = document.getElementById('camposLlenar');
        document.getElementById("camposLlenar").innerHTML = "";  

        var cadHtml = '<div class="table-responsive table-striped table-bordered">';
        cadHtml += '<table id="tabla_campos" class="table"><tr><th>Clave de uso</th><th>Valor</th><th>Acción</th></tr>';

        for (var c of data['campos'])
            cadHtml += this.getRowTableFields(c.campo);
   
        cadHtml += "</table></div>";
        document.getElementById("camposLlenar").innerHTML = cadHtml;

        $('#texto_final').summernote('code', data['texto']);
        $('#summernote').summernote('code', data['texto']);
    }

    /****  Agrega un campo a la Tabla  *****/
    function getRowTableFields(campo){
      var html = '<tr id="'+campo+'">';
      html += '<td>|'+campo+'|';
      html += '<button style="align:left" class="btn btn-link" onclick="copyText(\''+String(campo)+'\')">';
      html += '<i class="far fa-copy"></i></button>';
      html += '</td>';
      html += '<td><input style="text-transform:none;width:600px;float:left;" ';
      html += 'type="text" class="form-control input100" ';
      html += 'name="'+campo+'"  onkeyup="replaceText()" ';
      html += ' required></td>';
      html += '<td><button class="delete-param-alert btn btn-link" data-message1="No podrás recuperar el registro." data-message2="¡Borrado! Verifica la redacción de la Plantilla." data-message3="Verifica la redacción de la Plantilla." data-message4="'+campo+'" style="width:40px; margin: 0; padding: 0;"><i class="far fa-trash-alt"></i></button></td>';
      html += "</tr>";
      return html;
    }

    function hiddenTemplate(){
        if (document.getElementById('check_edit_template').checked)
          document.getElementById("div_summernote").hidden = false;
        else
          document.getElementById("div_summernote").hidden = true;
    }

    function copyText(campo){
        navigator.clipboard.writeText('|'+campo+'|');
    }

    function replaceText(){
      //console.log("Entre a: replaceText");
      var resume_table = document.getElementById("tabla_campos");
      var textoAux = document.getElementById("summernote").value;
      
      for (var i = 1, row; row = resume_table.rows[i]; i++) {
        var campo = row.cells[0].innerText;
        var value_input = row.cells[1].getElementsByTagName('input')[0].value;
        if(value_input != "")
            textoAux = textoAux.replaceAll(campo, value_input);
      }
      $('#texto_final').summernote('code', textoAux);
    }  

    /******* Agrega un campo nuevo a la Tabla ***********/
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

          $("#camposLlenar").find('tbody').append(this.getRowTableFields(nuevo_campo));

        }
      }
    }

    function deleteParamFromTable(campo){
        //console.log("deleteDataParam:"+campo);
        var fila = document.getElementById(campo);
        if (fila) 
            fila.parentNode.removeChild(fila);
        
        var textoAux = document.getElementById("summernote").value;
        textoAux = textoAux.replaceAll('|' + campo + '|', "");
        $('#summernote').summernote('code', textoAux);    

        var arrAux = document.getElementById("nuevos_campos_cad").value.split(",");
        if(arrAux.includes(campo)){
            var indice = arrAux.indexOf(campo);
            arrAux.splice(indice, 1);
            document.getElementById("nuevos_campos_cad").value = arrAux.join();
        }
    }

    $('body').on('click','.delete-param-alert',function(event){
        var message1 = $(this).attr('data-message1');
        var message2 = $(this).attr('data-message2');
        var message3 = $(this).attr('data-message3');
        var campo = $(this).attr('data-message4');

        Swal.fire({
          title: '{{__("¿Estás seguro de ELIMINAR?")}}',
          text: message1,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: '{{__("Sí")}}',
          cancelButtonText: '{{__("No")}}'
        }).then((result) => {
            if (result.isConfirmed) {
              deleteParamFromTable(campo);

              Swal.fire(
                 message2,
                 message3,
                 'success'
              );
            }
        });      
    }); //body
    
    
  </script>
  @stop