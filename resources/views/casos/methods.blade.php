<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script src="https://unpkg.com/pdf-lib"></script>
<script src="{{ asset('js/sweetalert.js') }}"></script>
<script>

    $(document).ready(function() {

      document.getElementById("div_campos").hidden = true;
      document.getElementById("div_textos_summernote").hidden = true;   
      document.getElementById("nuevo_campo").value = "";
      document.getElementById('check_edit_template').checked = false;


      $('div.note-group-select-from-files').remove();
      $('#summernote').summernote(
          {
            disableDragAndDrop:true,
            height: 350,
            toolbar: [
              ['style', ['style']],
              ['font', ['bold', 'underline', 'clear']],
              ['color', ['color']],
              ['para', ['ul', 'ol', 'paragraph']],
              ['misc', ['undo', 'redo']],
              ['height', ['height']],
              //['mybutton', ['undo', 'lineHeightPlus']],
            ],
            lineHeights: ['1.0', '1.2', '1.4', '1.6', '1.8', '2.0', '2.2', '2.4', '2.6', '2.8','3.0', '4.0', '5.0']
            /*buttons: {
              undo: UndoButton, 
              lineHeightPlus: LineHeightPlusButton,
            }*/
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

    /*************** Botón de Undo (Summernote) *********/
    /*var UndoButton = function (context) {
        var ui = $.summernote.ui;

        // create button
        var button = ui.button({
          contents: '<i class="fas fa-long-arrow-rotate-left"/>',
          tooltip: 'Deshacer',
          click: function () {
            //console.log("Entre a Undo");
            document.execCommand('undo');
            // invoke insertText method with 'hello' on editor module.
            //context.invoke('editor.insertText', 'hello');
          }
        });
        return button.render();   // return button as jquery object
    }*/

    /*************** + Line Height   ***************************/
    /*var LineHeightPlusButton = function (context) {
        var ui = $.summernote.ui;

        // create button
        var button = ui.button({
          contents: '<i class="fas fa-long-arrow-rotate-left"/>Line Height +',
          tooltip: '+ Interlineado',
          click: function () {
            //console.log("Entre a Line Plus");
            var text = document.getSelection();
            let valor = +document.getElementById("line_height").value;
            valor += 1;
            $('#summernote').summernote('lineHeight', valor);
            document.getElementById("line_height").value = valor;
          }
        });
        return button.render();   // return button as jquery object
    }*/

  
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
          showFieldsAndTemplate(data, 'insert');
          //toastr.success('Información actualizada correctamente.', '', {timeOut: 3000});
        },
        error: function(){
          toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
        }
      });
    }

    /***** Muestra los campos de la plantilla y el Texto de la Plantilla. *****/
    function showFieldsAndTemplate(data, type){
        //console.log(data);
        this.hiddenTemplate();
        document.getElementById("div_campos").hidden = false;
        document.getElementById("div_textos_summernote").hidden = false;

        var contenedorDiv = document.getElementById('camposLlenar');
        document.getElementById("camposLlenar").innerHTML = "";  

        var cadHtml = '<div class="table-responsive table-striped table-bordered">';
        cadHtml += '<table id="tabla_campos" class="table"><tr><th>Clave de uso</th><th>Valor</th><th>Acción</th></tr>';

        if(type == 'insert'){
            for (var c of data['campos'])
                cadHtml += this.getStringHtmlField(c.campo);
        } else{
            for (var c of data['campos'])
                cadHtml += this.getStringHtmlFieldEdit(c);
        }
   
        cadHtml += "</table></div>";
        document.getElementById("camposLlenar").innerHTML = cadHtml;

        $('#texto_final').summernote('code', data['texto']);
        $('#summernote').summernote('code', data['texto']);
    }

    /****  Regresa la cade de Html  *****/
    function getStringHtmlField(campo){
      var html = '<tr id="'+campo+'">';
      html += '<td>|'+campo+'|';
      html += '<button type="button" style="align:left" class="btn btn-link" onclick="copyText(\''+String(campo)+'\')">';
      html += '<i class="far fa-copy"></i></button>';
      html += '</td>';
      html += '<td><input onCopy="return false;" style="text-transform:none;width:600px;float:left;" ';
      html += 'type="text" class="form-control input100" ';
      html += 'name="'+campo+'"  onkeyup="replaceText()" ';
      html += ' required></td>';
      html += '<td><button type="button" class="delete-param-alert btn btn-link" data-message1="No podrás recuperar el registro." data-message2="¡Borrado! Verifica la redacción de la Plantilla." data-message3="Verifica la redacción de la Plantilla." data-message4="'+campo+'" style="width:40px; margin: 0; padding: 0;"><i class="far fa-trash-alt"></i></button></td>';
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
      //console.log("Entre a addField");
      var nuevo_campo = document.getElementById("nuevo_campo").value;
      var elemento_html = document.getElementById(nuevo_campo);
      
      if(nuevo_campo != "" && elemento_html == null){
        const arrAux = document.getElementById("nuevos_campos_cad").value.split(",");
        if(!arrAux.includes(nuevo_campo)){
          if(document.getElementById("nuevos_campos_cad").value !== "")
              document.getElementById("nuevos_campos_cad").value += ",";
          document.getElementById("nuevos_campos_cad").value += nuevo_campo;
          document.getElementById("nuevo_campo").value = "";

          $("#camposLlenar").find('tbody').append(this.getStringHtmlField(nuevo_campo));
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


    /*************************************** Edición ************************************/

    function getAndShowFieldsByTemplateIdEdit(plantillas){
      //console.log("Entre a:getAndShowFieldsByTemplateIdEdit");
      var plantillaId = document.getElementById("select_template").value;
      var configuracionId = document.getElementById("configuracion_id").value;
      var casoId = document.getElementById("caso_id").value;
      var url = "{{action('CasosController@index')}}";

      var plantilla = plantillas.find((element) => element.plantillaId == plantillaId);
      document.getElementById("orden").value = plantilla.orden;

      $.ajax({
        dataType: 'json',
        type:'GET',
        url: url,
        cache: false,
        data: {'option' : "fields_values_template_text", 'plantillaId' : plantillaId, 'casoId' : casoId, 'configuracionId' : configuracionId ,'_token':"{{ csrf_token() }}"},
        success: function(data){
          //console.log(data);
          showFieldsAndTemplate(data, 'edit');
          //toastr.success('Información actualizada correctamente.', '', {timeOut: 3000});
        },
        error: function(){
          toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
        }
      });
    }

     function getStringHtmlFieldEdit(arr){
        //console.log("getRowTableFieldsEdit");
        var valor = '';
        if(arr.valor_plantilla != null)
            valor = arr.valor_plantilla;
        else{ 
            if(arr.valor_ultimo != null)
                valor = arr.valor_ultimo;
        }

        var html = '<tr id="'+arr.campo+'">';
        html += '<td>|'+arr.campo+'|';
        html += '<button type="button" style="align:left" class="btn btn-link" onclick="copyText(\''+String(arr.campo)+'\')">';
        html += '<i class="far fa-copy"></i></button>';
        html += '</td>';
        html += '<td><input onCopy="return false;" style="text-transform:none;width:600px;float:left;" ';
        html += 'type="text" class="form-control input100" ';
        html += 'name="'+arr.campo+'"  onkeyup="replaceText()" ';
        html += ' value="'+valor+'"';
        html += ' required></td>';
        html += '<td><button type="button" class="delete-param-alert btn btn-link" data-message1="No podrás recuperar el registro." data-message2="¡Borrado! Verifica la redacción de la Plantilla." data-message3="Verifica la redacción de la Plantilla." data-message4="'+arr.campo+'" style="width:40px; margin: 0; padding: 0;"><i class="far fa-trash-alt"></i></button></td>';
        html += "</tr>";
        return html;
    }

















    
    
 </script>
   
