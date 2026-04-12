<script>
    $(function() {
        const idsToHide = [
            "div_select_config",
            "div_plantillas2",
            "carouselExampleCaptions"
        ];

        idsToHide.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.hidden = true;
        });

        const nuevosCampos = document.getElementById("nuevos_campos_cad");
        if (nuevosCampos) nuevosCampos.value = "";

        initSelectIfExists('select_config', 'Selecciona una Configuración');
        initSelectIfExists('select_template', 'Selecciona una Plantilla');
        initSelectIfExists('select_template_2', 'Selecciona una Plantilla');

        initSummernotes();
    });

    function initSelectIfExists(id, placeholder){
        const el = document.getElementById(id);
        if (el && !el.tomselect) {
            new TomSelect(el, {
                maxItems: 1,
                placeholder: placeholder,
                create: false,
                allowEmptyOption: true,
                items: [] // 🔥 Esto evita selección automática
            });
        }
    }    

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

    /******** Combo de PLANTILLAS *******/
    function fetchTemplateFields({ plantillaId = null, casoPlantillaId = null }) {
        //console.log("fetchTemplateFields", { plantillaId, casoPlantillaId });

        if (!plantillaId && !casoPlantillaId) return;

        cleanElements();

        const casoId   = document.getElementById("caso_id")?.value ?? 0;
        const configId = document.getElementById("configuracion_id")?.value ?? 0;

        $.ajax({
            dataType: 'json',
            type:'GET',
            url: "{{action('PlantillasController@index')}}",
            cache: false,
            data: {
                option: "fields_text_by_template_id",
                plantillaId,
                casoId,
                configId,
                casoPlantillaId,
                _token:"{{ csrf_token() }}"
            },
            success: data => showFieldsAndTemplate(data, 'insert'),
            error: () => toastr.error('Hubo un problema, intenta más tarde.', '', {timeOut: 3000})
        });
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
        const selectElement = document.getElementById('select_template');

        if (!selectElement.tomselect) {
            console.error("TomSelect no está inicializado");
            return;
        }

        const select = selectElement.tomselect;

        // Limpiar selección y opciones
        select.clear();
        select.clearOptions();

        // Opción por defecto
        select.addOption({
            value: "",
            text: "-- Selecciona una plantilla --"
        });

        // Agregar nuevas opciones
        templates.forEach(template => {
            select.addOption({
                value: template.plantilla.id,
                text: template.plantilla.nombre
            });
        });

        select.refreshOptions(false);
    }

    function cleanElements(){
        document.getElementById("nuevos_campos_cad").value = "";
        document.getElementById("texto_final").value = "";
        document.getElementById("carouselExampleCaptions").hidden = true;
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
        document.getElementById('detalle').value = data['detalle'];

        $('#texto_final').summernote('code', data['texto']);
        $('#summernote').summernote('code', data['texto']);
    }

    /****  Regresa el STRING HTML para la tabla de CAMPOS  *****/
    function getStringHtmlFieldTemplate(arr){ 
        var html1 = `<ul class="nav nav-tabs">`;
        var cad_hidden = '';
        var element_id = 'aria-current="page"';
        var cad_active = 'active';

        for(let a of arr){
            html1 += `<li class="nav-item" onclick="showTable(${a["id"]})"><a id="a_${a["id"]}" class="nav-link ${cad_active}" href="#">${a["grupo"]}</a></li>`;
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
        //document.querySelectorAll('ul table').forEach(t => t.hidden = true);

        $('ul table').attr("hidden","true");
        document.getElementById("tabla_"+id).hidden = false;
    }

    function replaceTextEditTemplate() {
        //var textoAux = document.getElementById("summernote").value;
        var textoAux = $('#summernote').summernote('code');
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
        textoAux = textoAux.replaceAll('<button', '<button disabled ');
        $('#texto_final').summernote('code', textoAux);
    }  

    // Esta función se encarga de mostrar un solo carrusel y ocultar el otro, dependiendo de cuál esté activo
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

        setTimeout(() => {
            replaceTextEditTemplate();

            if ($('#summernote').length) {
                $('#summernote').summernote('refresh');
            }
        }, 100);
    }

    function getTemplatesByType(valor) {
        ["div_select_config", "div_plantillas", "div_plantillas2"].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.hidden = true;
        });

        cleanElements();
        let select;
        switch(valor) {
            case "1":
                select = document.getElementById('select_config');               
                document.getElementById("div_plantillas2").hidden = false;                 
                break;
            case "2":
                select = document.getElementById('select_template_2');  
                document.getElementById("div_select_config").hidden = false;
                break;
        }
        if (select?.tomselect) 
            select.tomselect.setValue('', true);
    }

    function clearTomSelect(id){
        const el = document.getElementById(id);
        if (el?.tomselect) el.tomselect.setValue('', true);
    }

    function disableEditionElements(valor){
        clearTomSelect('select_template');
        clearTomSelect('select_template_2');

        cleanElements();

        const divPlantillas = document.getElementById("div_plantillas");
        const divContestadas = document.getElementById("div_plantillas_contestadas");

        divPlantillas.classList.toggle('d-none', valor !== "1");
        divContestadas.classList.toggle('d-none', valor !== "2");
    } 
</script>
   
