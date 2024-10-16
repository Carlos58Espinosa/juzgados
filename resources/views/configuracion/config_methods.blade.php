<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://code.jquery.com/ui/1.14.0/jquery-ui.js"></script>
<script>
  $(document).ready(function() {
      var arrIds = [];
      if($('#old_ids').val() != ""){
          arrIds = $('#old_ids').val().split(',');
          rebuildListGroup(arrIds);
      }
  });

  function rebuildListGroup(arrIds) {
      var cad = '';
      var plantillas = @json($plantillas);
      let plantillas_aux = plantillas.filter(plantilla => arrIds.indexOf(String(plantilla['id'])) > -1 );

      for (const iter of arrIds){
          var aux = plantillas_aux.filter(plantilla => String(plantilla['id']) == iter);
          if(aux.length > 0){
            cad += '<li id="'+aux[0]['id']+'" class="list-group-item list-group-item-info sortable-itemc ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'+aux[0]['nombre']+'</li>';
          }
      }
      document.getElementById("list_templates").innerHTML += cad;
  }
  
  function reorderArrayIds() {
      var arrIds = [];
      var items = document.getElementsByClassName("list-group-item");

      for (var i = 0; i < items.length; i++)
          arrIds.push(items[i]["id"]);
      document.getElementById('old_ids').value = arrIds;
  }

  function addTemplateRowListGroup(){
    //console.log("entre a:addRowTableTemplates");
    //console.log($('#plantillas_ids_aux').options);
    //var plantillas_ids_selected = $('#plantillas_ids_aux').val();     
    var options_selected = $('#plantillas_ids_aux').find(':selected');
    var arrIds = [];
    var band = false;

    if($('#old_ids').val() != "")
        arrIds = $('#old_ids').val().split(',');

    for (const iter of options_selected){
        if(document.getElementById(iter['value']) == null){
            var cad = '<li id="'+iter['value']+'" class="list-group-item list-group-item-info sortable-itemc ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'+iter['innerText']+'</li>';
            document.getElementById("list_templates").innerHTML += cad;
            arrIds.push(iter['value']);
            band = true;
        } 
    }

    if(!band){ //Deselecciono una opción        
        var options_unselected = $("#plantillas_ids_aux").find('option').not(':selected');

        for (const iter of options_unselected){
            var indice = arrIds.indexOf(iter['value']);
            if(indice > -1){
                arrIds.splice(indice, 1);
                document.getElementById(iter['value']).remove();
            }
        }
    }
    document.getElementById('old_ids').value = arrIds;
  }

  $( function() {
      $( "#list_templates" ).sortable({
        connectWith: ".connectedSortable"
      }).disableSelection();
  });  
</script>