<script>
$(document).ready(function() {

    if (document.getElementById("menu_agrupacion")) {
        selectedMenu("menu_agrupacion");
    }

    if ($.fn.select2 && document.getElementById('campos_ids_aux')) {
        $('#campos_ids_aux').select2({
            placeholder: '-- Selecciona Clave --',
            width: '100%'
        });
    }

    document.getElementById("grupo").value = "";
    document.getElementById("grupo_id").value = "";
});


/***************  Agregar GRUPO  ********************/
function addGroup(){
    document.getElementById("tabla_campos").hidden = true;
    var url = "{{action('AgrupacionesController@addGroup')}}";
    var grupo = document.getElementById("grupo").value;

    if(grupo != "" && document.getElementById(grupo) == null){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url,
            cache: false,
            data: {'nombre' : grupo,'_token':"{{ csrf_token() }}"},
            success: function(data){
                var cadHtml = `<li id="${data}" onclick="getFieldsByNav(this.id)" class="nav-item">
                                <a id="${grupo}" class="nav-link" href="#">${grupo}</a>
                               </li>
                               <button id="button_${data}" class="btn" style="width:40px; color: lightcoral;" onclick="deleteGroup(${data})">
                                    <i class="far fa-trash-alt"></i>
                               </button>`;

                document.getElementById("navs").innerHTML += cadHtml;
                document.getElementById("grupo_id").value = data;
                document.getElementById("grupo").value = "";
                document.getElementById("tabla_campos").innerHTML = "";

                $(".nav-tabs li").removeClass("active");
                $("#" + data).addClass("active");
            },
            error: function(){
                toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
            }
        });
    } else {
        toastr.error('No se puede repetir el nombre del grupo.', '', {timeOut: 3000});
    }
}

/**************  Eliminar GRUPO  ********************/
function deleteGroup(id){
    document.getElementById("tabla_campos").hidden = true;
    $(".nav-tabs li").removeClass("active");

    var url = "{{action('AgrupacionesController@deleteGroupsAndFields')}}";

    $.ajax({
        dataType: 'json',
        type:'POST',
        url: url,
        cache: false,
        data: {'grupo_id' : id, 'option' : 'group', '_token':"{{ csrf_token() }}"},
        success: function(data){
			deleteElements(campo, 'field');
            fillMultiSelectFields(data);
        },
        error: function(){
            toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
        }
    });
}

function deleteElements(id, option){
    switch(option){
        case "field":
            const row = document.getElementById('row_' + id);
            if(row)
                row.remove();
        break;

        case "group":
            document.getElementById("grupo_id").value = "";
            document.getElementById(id).remove();
            document.getElementById("button_" + id).remove();
            document.getElementById("tabla_campos").innerHTML = "";
        break;
    }
}

/********  Recupera los CAMPOS de un GRUPO  ******************/
function getFieldsByNav(id){
    document.getElementById("tabla_campos").hidden = true;
    $(".nav-tabs li").removeClass("active");
    $("#" + id).addClass("active");

    document.getElementById("grupo_id").value = id;
    document.getElementById("tabla_campos").innerHTML = '';

    var url_templates = "{{action('AgrupacionesController@index')}}";

    $.ajax({
        dataType: 'json',
        type:'GET',
        url: url_templates,
        cache: false,
        data: {'option' : "fields_by_group", 'grupo_id' : id,'_token':"{{ csrf_token() }}"},
        success: function(data){
            if(data.length > 0){
                document.getElementById("tabla_campos").hidden = false;
                createTableFields(data);
            }
        },
        error: function(){
            toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
        }
    });
}

function createTableFields(arr){
    var cad = '<tr><th width="300px">Campo</th><th width="100px">Acción</th></tr>';

    for(let a of arr)
        cad += getHtmlStringField(a["campo"]);

    document.getElementById("tabla_campos").innerHTML = cad;
}

function getHtmlStringField(field) {
    return `<tr id="row_${field}">
                <td>${field}</td>
                <td>
                    <button class="btn" style="width:40px;color:lightcoral;"
                        onclick="deleteField('${field}')">
                        <i class="far fa-trash-alt"></i>
                    </button>
                </td>
            </tr>`;
}

/*************  Agrega CAMPOS *************/
function addFields(){
    var grupo_id = document.getElementById("grupo_id").value;
    var campos = $('#campos_ids_aux').find(':selected');

    if(campos.length > 0 && grupo_id != ""){

        var url = "{{action('AgrupacionesController@store')}}";
        var arr_campos = [];

        for(const iter of campos)
            arr_campos.push(iter.value);

        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url,
            cache: false,
            data: {'grupo_id' : grupo_id, 'campos' : JSON.stringify(arr_campos), '_token':"{{ csrf_token() }}"},
            success: function(data){

                $('#campos_ids_aux option:selected').remove();
                $('#campos_ids_aux').trigger('change');

                document.getElementById("tabla_campos").hidden = false;
                addRowsTableFields(arr_campos);
            },
            error: function(){
                toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
            }
        });

    } else {
        toastr.error('Selecciona un Grupo.', '', {timeOut: 3000});
    }
}

function addRowsTableFields(arr){
    var cad = '';

    if(document.getElementById("tabla_campos").innerHTML == "")
        cad += '<tr><th>Campo</th><th>Acción</th></tr>';

    for(let a of arr)
        cad += getHtmlStringField(a);

    document.getElementById("tabla_campos").innerHTML += cad;
}

/************  Elimina CAMPO  *************/
function deleteField(campo){
    var url = "{{action('AgrupacionesController@deleteGroupsAndFields')}}";

    $.ajax({
        dataType: 'json',
        type:'POST',
        url: url,
        cache: false,
        data: {'nombre' : campo, 'option' : 'field', '_token':"{{ csrf_token() }}"},
        success: function(data){
			deleteElements(campo, 'field');
            fillMultiSelectFields(data);
        },
        error: function(){
            toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
        }
    });
}

function fillMultiSelectFields(data){
    $('#campos_ids_aux').empty();

    for(let d of data['campos']){
        $('#campos_ids_aux').append(`<option value="${d['campo']}">${d['campo']}</option>`);
    }

    $('#campos_ids_aux').trigger('change');
}
</script>
