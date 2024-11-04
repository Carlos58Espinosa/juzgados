<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script src="{{ asset('js/sweetalert.js') }}"></script>

<dialog id="modal" style="padding:20px; width:700px; height: 400px;position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);">
    <div class="row" style="width:420px;">
        <button id="boton_bold" style="width: 40px;" onclick="changeTextInput('bold')" type="button" class="btn btn-light"><i class="note-icon-bold"></i></button>
        <button id="boton_underline" style="width: 40px;" onclick="changeTextInput('underline')" type="button" class="btn btn-light"><i class="note-icon-underline"></i></button>
        <button style="width: 40px;" onclick="changeTextInput('eraser')" type="button" class="btn btn-light"><i class="note-icon-eraser"></i></button>
        <button id="boton_italic" style="width: 40px;" onclick="changeTextInput('italic')" type="button" class="btn btn-light"><i class="note-icon-italic"></i></button>
        <button id="boton_line" style="width: 40px;" onclick="changeTextInput('line-through')" type="button" class="btn btn-light"><i class="note-icon-strikethrough"></i></button>
    </div>

    <div class="row" style="width: 420px; float: left; margin-top: 5px;">
        <div class="form-group">
            <label>Nombre del Parámetro:</label>
            <input id="nuevo_param" style="text-transform:lowercase; width: 400px;" class="form-control" type="text" maxlength="240" name="param1" placeholder="Nombre del parámetro"  value ="" onkeydown="return /[0-9,a-z, ]/i.test(event.key)" oninput="searchFieldsAndShow(this.value, {{@json_encode($campos)}})" autofocus>

            <br>            

            <button id="saveModal" type="button" class="btn btn-success">Guardar</button>
            <button style="margin-left: 70px;" id="closeModal" type="button" class="btn btn-danger">Cerrar</button>
        </div>
    </div>

    <div id="camposLlenar" style="width:230px; height:350px;margin-left:410px; margin-top: -40px; overflow: hidden; overflow-y: scroll; border-radius: 10px;">            
    </div> 

</dialog>

<script>
                    
    $(document).ready(function() {

        $('div.note-group-select-from-files').remove();
        $('#summernote').summernote(
          {
            disableDragAndDrop:true,
            height: 450,
            width: 600,
            focus: true,
            toolbar: [
              ['style', ['style']],
              ['font', ['bold', 'underline', 'clear', 'italic', 'strikethrough']],
              ['para', ['ul', 'ol', 'paragraph']],
              ['misc', ['undo', 'redo']],
              ['height', ['height']],
              ['mybutton', ['addParam','lowerCase']],
              ['view', ['codeview']],
            ],
            lineHeights: ['1.0', '1.1', '1.2', '1.3', '1.4', '1.5', '1.6', '1.7'],
            buttons: {
                addParam: addParamButton,
                lowerCase: lowerCaseButton
            }
          }
        );
    });

    /**************************  Estilo TEXTO PARAMETRO  *****************************/
    function changeTextInput(option){
        var element = document.getElementById('nuevo_param').style;

        switch(option){
            case 'bold':
                element.fontWeight == option ? element.fontWeight = "normal" : element.fontWeight = option; 
            break;
            case 'underline':
            case 'line-through':
                element.textDecoration == option ? element.textDecoration = "none" : element.textDecoration = option;
            break;
            case 'eraser':
                element.fontWeight = "normal";
                element.textDecoration = "none";
                element.fontStyle = "normal";
            break;
            case 'italic':
                element.fontStyle == option ? element.fontStyle = "normal" : element.fontStyle = option;
            break;
        }    
        borderSelectedButton(element);      
    }

    /*****************  Marca las OPCIONES seleccionadas en el nuevo PARAMETRO  *********************/
    function borderSelectedButton(element){
        var boton_bold = document.getElementById("boton_bold");
        var boton_underline = document.getElementById("boton_underline");
        var boton_line = document.getElementById("boton_line");
        var boton_italic = document.getElementById("boton_italic");

        element.fontWeight == "bold" ? boton_bold.style.border = "2px solid black" : boton_bold.style.border = "none";
        element.textDecoration == "underline" ? boton_underline.style.border = "2px solid black" : boton_underline.style.border = "none";
        element.textDecoration == "line-through" ? boton_line.style.border = "2px solid black" : boton_line.style.border = "none";
        element.fontStyle == "italic" ? boton_italic.style.border = "2px solid black" : boton_italic.style.border = "none";
    }

    /*************** Botón de Agregar Parámetro *********/
    var addParamButton = function (context) {
        var ui = $.summernote.ui;

        // create button
        var button = ui.button({
          contents: '<i class="far fa-edit"/> Agregar Parámetro',
          tooltip: 'Agregar Parámetro',
          click: function () {
                $('#summernote').summernote('editor.saveRange'); 

                const modal = document.getElementById("modal"); 
                var elemento_param = document.getElementById("nuevo_param");
                elemento_param.value = "";
                elemento_param.innerHTML = "";
                document.getElementById("camposLlenar").innerHTML = "";
                changeTextInput("eraser");
                modal.showModal();
                configurationModal('create', null);
            }
        });
        return button.render();   // return button as jquery object
    }

    /*********** Botón de Texto a Mayusculas *************/
    var lowerCaseButton = function (context) {
        var ui = $.summernote.ui;

        // create button
        var button = ui.button({
          contents: '<i class="fab fa-etsy"/> Mayusculas',
          tooltip: 'Convertir a Mayusculas',
          click: function () {
                var textSelection = window.getSelection().toString().toUpperCase();
                if(textSelection != ""){
                    $('#summernote').summernote('editor.restoreRange');
                    $('#summernote').summernote('editor.focus');
                    $('#summernote').summernote('pasteHTML', textSelection);
                }
            }
        });
        return button.render();   // return button as jquery object
    }

    /************  Configuración MODAL Nuevo Parámetro  ****************/
    function configurationModal(option, button){
        $("#saveModal").unbind().click(function() {
            var elemento_param = document.getElementById("nuevo_param");
            var valor_parametro = elemento_param.value.toLowerCase();
            modal.close();

            if(valor_parametro !== ""){
                valor_parametro = cleanParameterValue(valor_parametro, elemento_param);
                switch(option){
                    case "create":
                        addParamOnSummernote(valor_parametro);
                    break;
                    case "edit":
                        button.innerHTML = valor_parametro;
                        $('#summernote').summernote('code', $('#summernote').summernote('code'));
                    break;
                }                

                //Solo para formulario de CASOS
                if(document.getElementById("nuevos_campos_cad"))
                    addRowInTableCases(elemento_param.value.toLowerCase());
            }
        }); 
        $("#closeModal").unbind().click(function() {  
            modal.close();
            $('#summernote').summernote('editor.restoreRange');
            $('#summernote').summernote('editor.focus');
        });     
    }


    /**** Limpia el VALOR PARAMETRO de saltos de linea, tabuladores y AGREGA el estilo ****/
    function cleanParameterValue(valor_parametro, elemento_param) {
        var span_txt = '<span hidden="">|</span>';
        valor_parametro = valor_parametro.replaceAll('\n','');
        valor_parametro = valor_parametro.replaceAll('\t','');
        valor_parametro = valor_parametro.replaceAll('<br>','');

        valor_parametro = span_txt + valor_parametro + span_txt;

        elemento_param.style.fontWeight == 'bold' ? valor_parametro = '<b>' + valor_parametro + '</b>' : valor_parametro = valor_parametro;

        elemento_param.style.textDecoration == 'underline' ? valor_parametro = '<u>' + valor_parametro + '</u>' : valor_parametro = valor_parametro;

        elemento_param.style.textDecoration == 'line-through' ? valor_parametro = '<s>' + valor_parametro + '</s>' : valor_parametro = valor_parametro;

        elemento_param.style.fontStyle == 'italic' ? valor_parametro = '<i>' + valor_parametro + '</i>' : valor_parametro = valor_parametro;
        return valor_parametro;
    }
    

    /****************  Agregar PARAMETRO BOTON  *********************/
    function addParamOnSummernote(valor_parametro){
        $('#summernote').summernote('editor.restoreRange');
        $('#summernote').summernote('editor.focus');        

        var botonHtml = `<button type="button" class="button_summernote" contenteditable="false" onclick="editButton(this)">${valor_parametro}</button>`;

        $('#summernote').summernote('pasteHTML', botonHtml);
    }

    /*******************  Editar BOTON PARAMETRO  **************************/
    function editButton(button){
        $('#summernote').summernote('editor.saveRange'); 
        var span_txt = '<span hidden="">|</span>';        
        const modal = document.getElementById("modal"); 
        document.getElementById("camposLlenar").innerHTML = "";
        var elemento_param = document.getElementById("nuevo_param");
        elemento_param.value = button.innerText.replaceAll(span_txt, '');

        elemento_param.style.fontWeight = 'normal';
        elemento_param.style.textDecoration = 'none';
        elemento_param.style.fontStyle = 'normal';

        if(button.innerHTML.includes("<b>"))
            elemento_param.style.fontWeight = 'bold';
        if(button.innerHTML.includes("<u>"))
            elemento_param.style.textDecoration = 'underline';
        if(button.innerHTML.includes("<s>"))
            elemento_param.style.textDecoration = 'line-through';
        if(button.innerHTML.includes("<i>"))
            elemento_param.style.fontStyle = 'italic';

        borderSelectedButton(elemento_param.style);
        modal.showModal();        
        configurationModal('edit', button);
    }

    function searchFieldsAndShow(cadena_buscar, campos){
        cadena_buscar = cadena_buscar.toLowerCase();
        cadena_buscar = cadena_buscar.replaceAll('á','a').replaceAll('é','e').replaceAll('í','i').replaceAll('ó','o').replaceAll('ú','u');

        document.getElementById('nuevo_param').value = cadena_buscar;

        document.getElementById("camposLlenar").innerHTML = ""; 
        if(cadena_buscar !== ""){
            var campos_encontrados = campos.filter((element) => element.campo.includes(cadena_buscar));
            var cadHtml = '<table id="tabla_campos" class="table"><thead></thead><tbody>';

            for (var c of campos_encontrados)
                cadHtml += this.getStringHtmlField(c.campo);

            cadHtml += "</tbody></table>";
            document.getElementById("camposLlenar").innerHTML = cadHtml;
        }
    }

    /****  Regresa la cadena de Html  *****/
    function getStringHtmlField(campo){
        var html = '<tr id="'+campo+'">';
        html += '<td>'+campo+'</td>';
        html += `<td><div class="div_btn_acciones"><button type="button" class="btn" onclick="copyText('${campo}')">`;
        html += '<i class="far fa-copy"></i></button></div>';
        html += '</td>';
        html += '</tr>';
        return html;
    }

    /******* Pone el texto en el input en Agregar Parámetro ********/
    function copyText(campo){
        document.getElementById("nuevo_param").value = campo;
    }

    /******* Agrega un campo nuevo a la Tabla de CASOS ***********/
    function addRowInTableCases(valor_parametro){
        //console.log("Entre a:addNewFieldInCases");
        var span_txt = '<span hidden="">|</span>';

        if(!document.getElementById(valor_parametro)){
            var element = document.getElementById("nuevos_campos_cad");
            const arrAux = element.value.split(",");
            if(!arrAux.includes(valor_parametro)){
                if(element.value !== "")
                    element.value += ",";
                element.value += valor_parametro;

                $("#tabla_0").append(this.getRowStringHtmlFieldTemplate(valor_parametro));
            }
        }
        //$('#texto_final').summernote('code', document.getElementById("summernote").value);
        this.replaceText();
    }

</script>