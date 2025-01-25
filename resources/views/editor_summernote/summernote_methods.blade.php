<style>
.dialogo {
     padding:20px; 
     width:700px; 
     height: 400px;
     position: absolute; 
     left: 50%; 
     top: 50%; 
     transform: translate(-50%, -50%); 
     border:2px solid black; 
     border-radius:10px;
     background: white;
}

.dialogo_boton {
        width: 40px;
}

</style>

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script src="{{ asset('js/sweetalert.js') }}"></script>

<dialog id="modal" class="dialogo">

    <div class="row" style="width:400px;">
        <label style="width: 200px;">Tipo de Letra:</label>
        <label style="width: 200px; float: left;">Tamaño de Letra:</label>
    </div>

    <div class="row" style="width:400px;">
        <div style="width:200px;" class="form-group">
            <select id="select_tipo_letra" class="form-control selectpicker @error('select_tipo_letra') is-invalid @enderror" title="-- Selecciona Tipo --" data-live-search="true">               
                <option style="font-family: Arial;" value="Arial">Arial</option>
                <option style="font-family: Comic Sans MS;" value="Comic Sans MS">Comic Sans MS</option>
                <option style="font-family: Courier New;" value="Courier New">Courier New</option>
                <option style="font-family: Helvetica;" value="Helvetica">Helvetica</option>
                <option style="font-family: Tahoma;" value="Tahoma">Tahoma</option>
                <option style="font-family: Times New Roman;" value="Times New Roman">Times New Roman</option>
                <option style="font-family: Verdana;" value="Verdana">Verdana</option>
            </select>
        </div>

        <div style="width:200px;" class="form-group">
            <select id="select_tam_letra" class="form-control selectpicker @error('select_tam_letra') is-invalid @enderror input100" title="-- Selecciona Tamaño --" data-live-search="true">    
                <option value="8px">8</option>
                <option value="9px">9</option>
                <option value="10px">10</option>
                <option value="11px">11</option>
                <option value="12px">12</option>
                <option value="14px">14</option>
                <option value="18px">18</option>
                <option value="24px">24</option>
                <option value="36px">36</option>
            </select>
        </div>
    </div>

    <br>

    <div class="row" style="width:420px;">
        <button id="boton_bold" onclick="changeTextInput('bold')" type="button" class="btn btn-light dialogo_boton"><i class="note-icon-bold"></i></button>
        <button id="boton_underline" onclick="changeTextInput('underline')" type="button" class="btn btn-light dialogo_boton"><i class="note-icon-underline"></i></button>
        <button onclick="changeTextInput('eraser')" type="button" class="btn btn-light dialogo_boton"><i class="note-icon-eraser"></i></button>
        <button id="boton_italic" onclick="changeTextInput('italic')" type="button" class="btn btn-light dialogo_boton"><i class="note-icon-italic"></i></button>
        <button id="boton_line" onclick="changeTextInput('line-through')" type="button" class="btn btn-light dialogo_boton"><i class="note-icon-strikethrough"></i></button>
    </div>

    <br>

    <div class="row" style="width: 420px; float: left; margin-top: 5px;">
        <div class="form-group">
            <label>Nombre del Parámetro:</label>
            <input id="nuevo_param" style="text-transform:lowercase; width: 400px;" class="form-control" type="text" maxlength="240" name="param1" placeholder="Nombre del parámetro"  value ="" onkeydown="return /[0-9,a-z, ]/i.test(event.key)" oninput="searchFieldsAndShow(this.value, {{@json_encode($campos)}})" autofocus>

            <br>            

            <button id="saveModal" type="button" class="btn btn-success">Guardar</button>
            <button style="margin-left: 70px;" id="closeModal" type="button" class="btn btn-danger">Cerrar</button>
        </div>
    </div>

    <div id="camposLlenar" style="width:230px; height:350px;margin-left:410px; margin-top: -110px; overflow: hidden; overflow-y: scroll; border-radius: 10px;">    

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
            fontNames: ['Arial', 'Comic Sans MS', 'Courier New', 'Helvetica', 'Tahoma', 'Times New Roman', 'Verdana'],
            toolbar: [
              ['style', ['style']],
              ['fontname', ['fontname']],
              ['fontsize', ['fontsize']],
              ['font', ['bold', 'underline', 'clear', 'italic', 'strikethrough']],
              ['para', ['ul', 'ol', 'paragraph']],
              ['misc', ['undo', 'redo']],
              ['height', ['height']],
              ['mybutton', ['addParam']],
              ['mybutton2', ['lowerCase']],
              //['view', ['codeview']],
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
          contents: '<i class="fab fa-etsy"/> Mayúsculas',
          tooltip: 'Convertir a Mayúsculas',
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
        valor_parametro = valor_parametro.replaceAll('\n','');
        valor_parametro = valor_parametro.replaceAll('\t','');
        valor_parametro = valor_parametro.replaceAll('<br>','');

        var str_style = '<span style="';
        if(document.getElementById('select_tam_letra').value != '')
            str_style += 'font-size:' + document.getElementById('select_tam_letra').value + ';';
        if(document.getElementById('select_tipo_letra').value != '')
            str_style += 'font-family:' + document.getElementById('select_tipo_letra').value + ';';
        if(str_style != '<span style="')
            valor_parametro = str_style + '">' + valor_parametro +'</span>';

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
        const modal = document.getElementById("modal"); 
        document.getElementById("camposLlenar").innerHTML = "";
        var elemento_param = document.getElementById("nuevo_param");
        elemento_param.value = button.innerText;

        if(button.innerHTML.includes("span")){
            var arr = button.innerHTML.substring(
                button.innerHTML.indexOf('"') + 1, 
                button.innerHTML.lastIndexOf('"')
            ).split(";");

            for(let iter of arr){
                var arr_aux = iter.split(":");
                switch(arr_aux[0]){
                    case 'font-family':
                        document.getElementById('select_tipo_letra').value = arr_aux[1]; 
                        break; 
                    case 'font-size':
                        document.getElementById('select_tam_letra').value = arr_aux[1]; 
                        break; 
                }
                $('.selectpicker').selectpicker('refresh');
            }            
        }

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

        var elemento = document.getElementById(valor_parametro);

        if(!elemento || elemento.localName == "tr"){
            var element = document.getElementById("nuevos_campos_cad");
            const arrAux = element.value.split(",");

            if(!arrAux.includes(valor_parametro)){
                if(element.value !== "")
                    element.value += ",";
                element.value += valor_parametro;

                this.getLastValueOfParameter(valor_parametro);
                //$("#tabla_0").append(this.getRowStringHtmlFieldTemplate(valor_parametro, valor));
            }
        }
        //$('#texto_final').summernote('code', document.getElementById("summernote").value);
        this.replaceText();
    }

    function getLastValueOfParameter(valor_parametro){
        var templateId = document.getElementById("select_template").value;
        var configId = document.getElementById("configuracion_id").value;
        var casoId = document.getElementById("caso_id").value;

        var url = "{{action('CasosController@index')}}";

        $.ajax({
            dataType: 'json',
            type:'GET',
            url: url,
            cache: false,
            data: {'option' : "last_value", 'plantillaId' : templateId, 'casoId':casoId, 'configId':configId, 'campo':valor_parametro,'_token':"{{ csrf_token() }}"},
            success: function(data){
                $("#tabla_0").append(getRowStringHtmlFieldTemplate(valor_parametro, data['valor']));
            },
            error: function(){
              toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
            }
        });
    }

</script>