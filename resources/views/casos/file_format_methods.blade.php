<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.14.0/jquery-ui.js"></script>
<script>

$(document).ready(function() {
    var formato = @json($formatos)[document.getElementById("select_format").selectedIndex+1];
	imageView(formato);
	var arrIds = [];
    if($('#old_ids').val() != ""){
        arrIds = $('#old_ids').val().split(',');
        rebuildListGroup(arrIds);
    }
});

function saveImage() {
	var url_save_image = "{{action('CasosController@saveLogo')}}";
	var file_data = $('#logo').prop('files')[0];
	var caso_id = document.getElementById("caso_id").value;
    var form_data = new FormData();
    form_data.append('logo', file_data);
    form_data.append('caso_id', caso_id);

	$.ajax({
	    url: url_save_image,
	    method: 'post',
	    data: form_data,
	    contentType : false,
	    processData : false,
	    headers: {
	        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	    },
	    success: function(response){
	    	$('#logos_ids_aux').append('<option val="'+response.id+'">'+response.nombre+'</option>');
	    	$('#logos_ids_aux').selectpicker('refresh');
	    	toastr.success('Imagen subida correctamente.', '', {timeOut: 3000});
	    }
	});
}

function rebuildListGroup(arrIds) {
      var cad = '';
      var logos = @json($logos);
      let logos_aux = logos.filter(logo => arrIds.indexOf(String(logo['id'])) > -1 );

      for (const iter of arrIds){
          var aux = logos_aux.filter(logo => String(logo['id']) == iter);
          if(aux.length > 0)
              cad += getHStringtmlLi(aux[0]['id'], aux[0]['nombre']);
      }
      document.getElementById("list_templates").innerHTML += cad;
}

function getHStringtmlLi(id, value){
    return '<li id="'+ id +'" class="list-group-item list-group-item-info sortable-itemc ui-state-default"><i title="Ordenar" class="fas fa-arrows-alt-v flecha_tipo_procedimiento"></i>' + value + '</li>';
}

function seleccionLogos(){
	var options_selected = $('#logos_ids_aux').find(':selected');
    var arrIds = [];
    var band = false;    

    if($('#old_ids').val() != "")
        arrIds = $('#old_ids').val().split(',');

    for (const iter of options_selected){
        if(document.getElementById(iter['value']) == null){
            document.getElementById("list_templates").innerHTML += getHStringtmlLi(iter['value'], iter['innerText']);
            arrIds.push(iter['value']);
            band = true;
        } 
    }

    if(!band){ //Deselecciono una opción    
        var options_unselected = $("#logos_ids_aux").find('option').not(':selected');

        for (const iter of options_unselected){
            var indice = arrIds.indexOf(iter['value']);
            if(indice > -1){
                arrIds.splice(indice, 1);
                document.getElementById(iter['value']).remove();
            }
        }
    }
    document.getElementById('old_ids').value = arrIds;

	/*if ($("#logos_ids_aux option:selected").length > 2) {
	}*/
}

function reorderArrayIds() {
    var arrIds = [];
    var items = document.getElementsByClassName("list-group-item");

    for (var i = 0; i < items.length; i++)
        arrIds.push(items[i]["id"]);
    document.getElementById('old_ids').value = arrIds;
}


/**********   Muestra la vista previa de un formato ****************/
function seleccionFormato(indice) {
    var formato = @json($formatos)[indice-1];
    imageView(formato);
    document.getElementById("list_templates").innerHTML = '';
    document.getElementById('old_ids').value = '';
    //$("#logos_ids_aux option:selected").removeAttr("selected");
    $("#logos_ids_aux option").prop("selected", false);
    $('#logos_ids_aux').selectpicker('refresh');
}

function imageView(formato){
    var img = document.getElementById("imagen_previa");
    img.hidden = true;
    if(formato['imagen_previa'] != null && formato['imagen_previa'] != "") {
        img.hidden = false;
        img.src = "/images/"+formato['imagen_previa'];
    }
    $('#logos_ids_aux').data('max-options', formato['numero_logos']);
    $('#logos_ids_aux').selectpicker('refresh');

    var div_logos = document.getElementById("div_logos");
    div_logos.hidden = false;
    if(formato['numero_logos'] == 0) 
        div_logos.hidden = true;    

    var tabla_orden =  document.getElementById("tabla_orden");
    tabla_orden.innerHTML = '';
    for(var i = 1; i <= formato['numero_logos']; i++){
        tabla_orden.innerHTML += '<tr><td style="border-collapse: collapse; border: none;">Logo '+i+'</th></tr>';
    }
}

$(function() {
    $( "#list_templates" ).sortable({
        connectWith: ".connectedSortable"
    }).disableSelection();
});  

</script>