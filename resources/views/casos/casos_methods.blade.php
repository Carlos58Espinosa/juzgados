<script>

    $(document).ready(function() {      
        document.getElementById('check_edit_template').checked = false;
        document.getElementById("div_textos_summernote").hidden = true; 
        initSummernotes();
    });

    function initSummernotes(){
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
    }
   
    /******** Combo de CONFIGURACIÓN *******/
    function getAndShowTemplatesByConfigId(){
          //console.log("Entre a: showConfigInfo");
          var url_templates = "{{action('ConfiguracionController@index')}}";
          var configId = document.getElementById("select_config").value;

          document.getElementById("div_plantillas").hidden = false;
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

    /******** Combo de PLANTILLAS *******/
    function getAndShowFieldsByTemplateId(){
          //console.log("Entre a:getAndShowFieldsByTemplateId");
          var templateId = document.getElementById("select_template").value;
          var elementConfig = document.getElementById("configuracion_id");
          var elementCaso = document.getElementById("caso_id");
          var configId, casoId;

          (elementConfig != null) ? configId = elementConfig.value : configId = 0;
          (elementConfig != null) ? casoId = elementCaso.value : casoId = 0;

          //console.log("CASO ID"+casoId);
          //console.log("CONFIG ID"+configId);

          var url = "{{action('PlantillasController@index')}}";

          $.ajax({
            dataType: 'json',
            type:'GET',
            url: url,
            cache: false,
            data: {'option' : "fields_text_by_template_id", 'plantillaId' : templateId, 'casoId':casoId, 'configId':configId,'_token':"{{ csrf_token() }}"},
            success: function(data){
                //console.log(data['grupos_campos']);
                showFieldsAndTemplate(data, 'insert');
            },
            error: function(){
              toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
            }
          });
    }

    /***** Muestra los CAMPOS, VALORES y TEXTO de la PLANTILLA *****/
    function showFieldsAndTemplate(data, type){
        //console.log(data);
        hiddenSummernote();
        document.getElementById("div_textos_summernote").hidden = false;

        var contenedorDiv = document.getElementById('div_campos_plantilla');
        contenedorDiv.innerHTML = this.getStringHtmlFieldTemplate(data['grupos_campos']);

        $('#texto_final').summernote('code', data['texto']);
        $('#summernote').summernote('code', data['texto']);
    }

    /****  Regresa el STRING HTML para la tabla de CAMPOS  *****/
    function getStringHtmlFieldTemplate(arr){ 
        var html1 = '<ul class="nav nav-tabs">', html2 = '<div>';
        for(let a of arr){
            html1 += `<li class="nav-item" onclick="showTable(${a["id"]})"><a class="nav-link" href="#">${a["grupo"]}</a></li>`;

            html2 += `<div id="${a["id"]}" hidden><table class="table table-info" style="margin-left: 5px; width:600px; position: absolute;"><tr><th>Nombre del Parámetro</th><th style="text-align:center">Valor</th></tr>`;
            for(let campo of a["campos"]){
                var valor = '';

                if(campo['valor_plantilla']){
                    if(campo['valor_plantilla'] != null)
                        valor = campo['valor_plantilla'];
                    else{ 
                        if(campo['valor_ultimo'] != null)
                            valor = campo['valor_ultimo'];
                    }
                }
                html2 += getRowStringHtmlFieldTemplate(campo['campo'], valor);
            }
            html2 += "</table></div>";
        }
        html1 +=  '</ul>';
        html2 += "</div>";

        return html1 + html2;
    }

    function getRowStringHtmlFieldTemplate(campo, valor){
        var html = `<tr id="${campo}">`;
        html += `<td>${campo}</td>`;
        html += `<td><input autocomplete="on" onCopy="return false;" style="text-transform:none; width:400px;float:left;" type="text" class="form-control" name="${campo}"  oninput="replaceText()" `;
        if(valor != null)
            html += ` value="${valor}" `;
        html += ' required></td></tr>';
        //html += '<td><button type="button" class="delete-param-alert btn btn-link" data-message1="No podrás recuperar el registro." data-message2="¡Borrado! Verifica la redacción de la Plantilla." data-message3="Verifica la redacción de la Plantilla." data-message4="'+campo+'" style="width:40px; margin: 0; padding: 0;"><i class="far fa-trash-alt"></i></button></td>';
        return html;
    }

    function showTable(id){
        var element = document.getElementById("grupo_id");
        if(element.value != "")
            document.getElementById(element.value).hidden = true;
        element.value = id;
        document.getElementById(id).hidden = false;
    }

    function hiddenSummernote(){
        if (document.getElementById('check_edit_template').checked)
          document.getElementById("div_summernote").hidden = false;
        else
          document.getElementById("div_summernote").hidden = true;
    }

    function replaceText(){
        var textoAux = document.getElementById("summernote").value;
        var element = document.getElementById("div_campos_plantilla");
        var inputs = element.getElementsByTagName('input');

        for(let input of inputs){
            //console.log("INPUTNAME:"+input.name);
            var campo = '<span hidden="">|</span>' + input.name + '<span hidden="">|</span>';

            if(input.value != "")
                textoAux = textoAux.replaceAll(campo, input.value);
        }
        $('#texto_final').summernote('code', textoAux);
    }  














/****************************** VERIFICARRRRRRRRRRRRRRRRR ********************/
/*

    function copyText(campo){
        navigator.clipboard.writeText('|'+campo+'|');
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


*/







    /*************************************** Edición ************************************/

   /* function getAndShowFieldsByTemplateIdEdit(plantillas){
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
    }*/

/*    function getStringHtmlFieldEdit(arr){
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
        html += 'name="'+arr.campo+'"  oninput="replaceText()" ';
        html += ' value="'+valor+'"';
        html += ' required></td>';
        html += '<td><button type="button" class="delete-param-alert btn btn-link" data-message1="No podrás recuperar el registro." data-message2="¡Borrado! Verifica la redacción de la Plantilla." data-message3="Verifica la redacción de la Plantilla." data-message4="'+arr.campo+'" style="width:40px; margin: 0; padding: 0;"><i class="far fa-trash-alt"></i></button></td>';
        html += "</tr>";
        return html;
    }  
*/
    
    
</script>
   
