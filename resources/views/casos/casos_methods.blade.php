<script>

    $(document).ready(function() { 
        if(document.getElementById("div_select_config"))
            document.getElementById("div_select_config").hidden = true;  
        document.getElementById("div_plantillas").hidden = true; 
        if(document.getElementById("div_plantillas2")) 
            document.getElementById("div_plantillas2").hidden = true;         
        document.getElementById("carouselExampleCaptions").hidden = true;
        document.getElementById("nuevos_campos_cad").value = "";
        initSummernotes();
    });

    function initSummernotes(){
        $('#summernote').on('summernote.change', function(we, contents, $editable) {
          //console.log('summernote.change', contents, $editable);
            replaceTextEditTemplate();
        });

        $('#texto_final').summernote(
          {
            disableDragAndDrop:true,
            height: 500,
            width: 600,
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
        document.getElementById("nuevos_campos_cad").value = "";
        document.getElementById("carouselExampleCaptions").hidden = true;

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

    function cleanElements(){
        document.getElementById("nuevos_campos_cad").value = "";
        document.getElementById("texto_final").value = "";
        document.getElementById("carouselExampleCaptions").hidden = true;
    }

    /******** Combo de PLANTILLAS *******/
    function getAndShowFieldsByTemplateId(option){
        //console.log("Entre en la seleccion de Plantillas= " + option);
        var templateId = null, elementConfig = null, elementCaso = document.getElementById("caso_id"), configId, casoId;
        if(option == 'libre')
            templateId = document.getElementById("select_template_2").value;
        else{
            templateId = document.getElementById("select_template").value;
            elementConfig = document.getElementById("configuracion_id");
        }

        cleanElements();

        (elementConfig != null) ? configId = elementConfig.value : configId = 0;
        (elementCaso != null) ? casoId = elementCaso.value : casoId = 0;


        var url = "{{action('PlantillasController@index')}}";

        $.ajax({
            dataType: 'json',
            type:'GET',
            url: url,
            cache: false,
            data: {'option' : "fields_text_by_template_id", 'plantillaId' : templateId, 'casoId':casoId, 'configId':configId,'_token':"{{ csrf_token() }}"},
            success: function(data){
                //console.log(data);
                //console.log(data['grupos_campos'][0]['campos']);
                showFieldsAndTemplate(data, 'insert');
            },
            error: function(){
              toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
            }
        });
    }

    function getAndShowFieldsEditByTemplateId(option){
        console.log("Entre a getAndShowFieldsEditByTemplateId "+ option);
        var templateId = null, caseTemplateId = null, elementConfig = document.getElementById("configuracion_id"), casoId = document.getElementById("caso_id").value, configId;
        (option == 'nueva') ? templateId = document.getElementById("select_template").value :          caseTemplateId = document.getElementById("select_template_2").value;
        
        console.log(elementConfig);
        console.log(elementConfig.value);
        (elementConfig != null) ? configId = elementConfig.value : configId = 0;

        cleanElements();

        console.log("Template = " + templateId + "  Caso = "+casoId + "  Config = "+ configId);

        var url = "{{action('PlantillasController@index')}}";

        $.ajax({
            dataType: 'json',
            type:'GET',
            url: url,
            cache: false,
            data: {'option' : "fields_text_by_template_id", 'plantillaId' : templateId, 'casoId':casoId, 'configId':configId, 'casoPlantillaId': caseTemplateId,'_token':"{{ csrf_token() }}"},
            success: function(data){
                //console.log(data);
                //console.log(data['grupos_campos'][0]['campos']);
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
        document.getElementById("carouselExampleCaptions").hidden = false;

        var contenedorDiv = document.getElementById('div_campos_plantilla');
        contenedorDiv.innerHTML = this.getStringHtmlFieldTemplate(data['grupos_campos']);
        if(data['grupos_campos'].length > 0){
            document.getElementById('a_'+data['grupos_campos'][0]['id']).style.cursor = "default"; 

            $('#a'+data['grupos_campos'][0]['id']).focus();
        }

        $('#texto_final').summernote('code', data['texto']);
        $('#summernote').summernote('code', data['texto']);
    }

    /****  Regresa el STRING HTML para la tabla de CAMPOS  *****/
    function getStringHtmlFieldTemplate(arr){ 
        var html1 = `<ul class="nav nav-tabs">`, html2 = '<div>';
        var cad_hidden = '';
        var element_id = 'aria-current="page"';
        var cad_active = 'active';

        for(let a of arr){
            html1 += `<li class="nav-item" onclick="showTable(${a["id"]})"><a id="a_${a["id"]}" class="nav-link ${cad_active}" href="#">${a["grupo"]}</a></li>`;
            /*html1 += `<li class="nav-item" onclick="showTable(${a["id"]})"><a id="a_${a["id"]}" class="nav-link" href="#" ${element_id}>${a["grupo"]}</a></li>`;*/

            /*html2 += `<div id="${a["id"]}" ${cad_hidden}><table id="tabla_${a["id"]}" class="table table-info" style="margin-left: 5px; width:600px; position: absolute; background:black;"><tr><th>Nombre del Parámetro</th><th style="text-align:center">Valor</th></tr>`;*/

            html1 += `<table class="tabla_expedientes" id="tabla_${a["id"]}" ${cad_hidden}><thead><tr><th width="200px;">Nombre del Parámetro</th><th style="text-align:center; width="400px;">Valor</th></tr></thead><tbody style="overflow: auto;">`;

            for(let campo of a["campos"]){
                var valor = '';

                if(campo['valor_plantilla'] != null)
                    valor = campo['valor_plantilla'];
                else{ 
                    if(campo['valor_ultimo'] != null)
                        valor = campo['valor_ultimo'];
                }

                html1 += getRowStringHtmlFieldTemplate(campo['campo'], valor);
            }
            html1 += `</tbody></table>`;
            cad_hidden = "hidden";
            element_id = '';
            cad_active = "";
            html1 += "";
        }
        return html1;
    }

    function getRowStringHtmlFieldTemplate(campo, valor){
        var html = `<tr id="${campo}">`;
        html += `<td width="200px;">${campo}</td>`;
        html += `<td style="padding-right: 5px;">`;
        //Cuando se usaban INPUTS
        //html += `<input autocomplete="on" onCopy="return false;" style="text-transform:none; width:400px;float:left;" type="text" class="form-control" name="${campo}" oninput="replaceText()" `;

        html += `<textarea style="width:400px; height:25px; float:left;" class="form-control" name="${campo}" oninput="replaceText()" `;
        /*if(valor != null)
            html += ` value="${valor}" `;*/
        //html += ' required>'
        html += `>${valor}</textarea>`;
        html += '</td></tr>';
        
        //html += '<td><button type="button" class="delete-param-alert btn btn-link" data-message1="No podrás recuperar el registro." data-message2="¡Borrado! Verifica la redacción de la Plantilla." data-message3="Verifica la redacción de la Plantilla." data-message4="'+campo+'" style="width:40px; margin: 0; padding: 0;"><i class="far fa-trash-alt"></i></button></td>';
        return html;
    }

    function showTable(id){
        $(".nav-tabs li a").removeClass("active");
        $("#a_" + id).addClass("active")
        $('ul table').attr("hidden","true");
        document.getElementById("tabla_"+id).hidden = false;
    }

    function replaceTextEditTemplate() {
        var textoAux = document.getElementById("summernote").value;
        $('#texto_final').summernote('code', textoAux);
        replaceText();
    }

    function replaceText(){
        var textoAux = document.getElementById("summernote").value;
        var element = document.getElementById("div_campos_plantilla");
        //var inputs = element.getElementsByTagName('input');
        var inputs = element.getElementsByTagName('textarea');

        for(let input of inputs){
            //console.log("INPUTNAME:"+input.name);
            var campo = '>' + input.name + '</';
            var valor_final = '>' + input.value + '</';
            valor_final = valor_final.replaceAll("\n", "<br>");
            
            if(input.value != "")
                textoAux = textoAux.replaceAll(campo, valor_final);
        }
        var textoAux = textoAux.replaceAll('<button', '<button disabled ');
        $('#texto_final').summernote('code', textoAux);
    }  

    function disableCarousel(){
        var carrusel1 = document.getElementById("carrusel1");
        var carrusel2 = document.getElementById("carrusel2");
        if(carrusel1.classList.contains("active")){
            carrusel2.style.visibility = "visible";
            carrusel1.style.visibility = "collapse";
        }
        if(carrusel2.classList.contains("active")){
            carrusel1.style.visibility = "visible";
            carrusel2.style.visibility = "collapse";
        }
        replaceTextEditTemplate();
    }

    function getTemplatesByType(valor) {
        document.getElementById("div_select_config").hidden = true;
        document.getElementById("div_plantillas").hidden = true;
        document.getElementById("div_plantillas2").hidden = true;

        cleanElements();

        switch(valor) {
            case "1":
                document.getElementById("div_plantillas2").hidden = false;
                break;
            case "2":
                document.getElementById("div_select_config").hidden = false;
                break;
        }
    }

    function disableEditionElements(valor){
        $('#select_template').val('').selectpicker('refresh');
        $('#select_template_2').val('').selectpicker('refresh');

        cleanElements();

        if(valor == "1"){
            document.getElementById("div_plantillas").hidden = false;
            document.getElementById("div_plantillas_contestadas").hidden = true;     
        } else {
            document.getElementById("div_plantillas").hidden = true;
            document.getElementById("div_plantillas_contestadas").hidden = false;
        }
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

    
    
</script>
   
