<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script src="https://unpkg.com/pdf-lib"></script>
<script src="{{ asset('js/sweetalert.js') }}"></script>

<dialog id="modal" style="padding:30px; width: 700px; height: 400px;position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);">
    <div class="row">
        <button style="width: 40px;" onclick="changeTextInput('bold')" type="button" class="btn btn-light"><i class="note-icon-bold"></i></button>
        <button style="width: 40px;" onclick="changeTextInput('underline')" type="button" class="btn btn-light"><i class="note-icon-underline"></i></button>
        <button style="width: 40px;" onclick="changeTextInput('eraser')" type="button" class="btn btn-light"><i class="note-icon-eraser"></i></button>
        <button style="width: 40px;" onclick="changeTextInput('italic')" type="button" class="btn btn-light"><i class="note-icon-italic"></i></button>
        <button style="width: 40px;" onclick="changeTextInput('line-through')" type="button" class="btn btn-light"><i class="note-icon-strikethrough"></i></button>
    </div>
    <div class="row">
        <div class="form-group">
            <label>Nombre del Parámetro:</label>
            <input id="nuevo_param" style="text-transform:lowercase; width: 400px;" class="form-control" type="text" maxlength="240" name="param1" placeholder="Nombre del parámetro"  value ="" onkeydown="return /[0-9,a-z, ]/i.test(event.key)" oninput="searchFieldsAndShow(this.value, {{@json_encode($campos)}})">

            <br>            

            <button id="closeModal" type="button" class="btn btn-success">Guardar</button>
            <button style="margin-left: 70px;" id="closeModal2" type="button" class="btn btn-danger">Cerrar</button>
        </div>
 
         
    </div>

    <div style="margin-left:400px; margin-top: -180px;">
            
            <div id="camposLlenar"></div>
            
    </div> 

</dialog>

<script>
                    
    $(document).ready(function() {

        $('div.note-group-select-from-files').remove();
        $('#summernote').summernote(
          {
            disableDragAndDrop:true,
            height: 500,
            width: 600,
            toolbar: [
              ['style', ['style']],
              ['font', ['bold', 'underline', 'clear', 'italic', 'strikethrough']],
              ['para', ['ul', 'ol', 'paragraph']],
              ['misc', ['undo', 'redo']],
              ['height', ['height']],
              ['mybutton', ['addParam']]
            ],
            lineHeights: ['1.0', '1.1', '1.2', '1.3', '1.4', '1.5', '1.6', '1.7'],
            buttons: {
              addParam: addParamButton
            }
          }
        );

    });

    /**** Limpia el VALOR PARAMETRO de saltos de linea, tabuladores ****/
    function cleanParameterValue(valor_parametro) {
        valor_parametro = valor_parametro.replace('\n','');
        valor_parametro = valor_parametro.replace('\t','');
        valor_parametro = valor_parametro.replace('<br>','');
        return valor_parametro;
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
                document.getElementById("nuevo_param").value = "";
                document.getElementById("nuevo_param").innerHTML = "";
                changeTextInput("eraser");
                modal.showModal();

                $("#closeModal").unbind().click(function() {
                    var valor_parametro = document.getElementById("nuevo_param").value;
                    var outerhtml = document.getElementById("nuevo_param").outerHTML;
                    modal.close();

                    if(valor_parametro !== ""){
                        valor_parametro = cleanParameterValue(valor_parametro);
                        addParamOnSummernote(valor_parametro, outerhtml);

                        //Solo para formulario de CASOS
                        if(document.getElementById("nuevos_campos_cad"))
                            addRowInTableCases(valor_parametro);
                    }
                });

                $("#closeModal2").unbind().click(function() {  
                    modal.close();
                    $('#summernote').summernote('editor.restoreRange');
                    $('#summernote').summernote('editor.focus');
                });          
            }
        });
        return button.render();   // return button as jquery object
    }

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
    }

    function addParamOnSummernote(valor_parametro, outerhtml){
        $('#summernote').summernote('editor.restoreRange');
        $('#summernote').summernote('editor.focus');

        var span_txt = '<span hidden="">|</span>';

        valor_parametro = span_txt + valor_parametro + span_txt;

        (outerhtml.includes("font-weight: bold")) ? valor_parametro = '<b>' + valor_parametro + '</b>' : valor_parametro = valor_parametro;
        (outerhtml.includes("text-decoration: underline")) ? valor_parametro = '<u>' + valor_parametro + '</u>' : valor_parametro = valor_parametro;
        (outerhtml.includes("font-style: italic")) ? valor_parametro = '<i>' + valor_parametro + '</i>' : valor_parametro = valor_parametro;
        (outerhtml.includes("text-decoration: line-through")) ? valor_parametro = '<s>' + valor_parametro + '</s>' : valor_parametro = valor_parametro;

        $('#summernote').summernote('pasteHTML', '<button type="button" class="button_summernote" contenteditable="false">' + valor_parametro + '</button>');
    }

    function searchFieldsAndShow(cadena_buscar, campos){
        document.getElementById("camposLlenar").innerHTML = ""; 
        if(cadena_buscar !== ""){
            var campos_encontrados = campos.filter((element) => element.campo.includes(cadena_buscar));
            var cadHtml = '<table id="tabla_campos" class="table-info">';

            for (var c of campos_encontrados)
                cadHtml += this.getStringHtmlField(c.campo);

            cadHtml += "</table>";
            document.getElementById("camposLlenar").innerHTML = cadHtml;
        }
    }

    /****  Regresa la cadena de Html  *****/
    function getStringHtmlField(campo){
        var html = '<tr class="table table-info" id="'+campo+'">';
        html += '<td class="table-info">'+campo;
        html += `<button type="button" style="align:left" class="btn btn-link" onclick="copyText('${campo}')">`;
        html += '<i class="far fa-copy"></i></button>';
        html += '</td>';
        html += '</tr>';
        return html;
    }

    /******* Pone el texto en el input en Agregar Parámetro ********/
    function copyText(campo){
        document.getElementById("nuevo_param").value = campo;
    }

    /******* Agrega un campo nuevo a la Tabla ***********/
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
                this.replaceText();
            }
        }
    }

</script>