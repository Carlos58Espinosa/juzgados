<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.14.0/jquery-ui.js"></script>
<script>

$(document).ready(function() {    
    var caso = @json($caso);

    if(caso.formato != null){
        formatoVistaPrevia(caso['formato']);
        reordenamientoLogos(caso['formato']['numero_logos']);
    }
});

function ocultarElementos(numero_logos){
    if(numero_logos > 0){
        document.getElementById("div_file").hidden = false;
        document.getElementById("div_logos").hidden = false;
    } else {
        document.getElementById("div_file").hidden = true;
        document.getElementById("div_logos").hidden = true;
    }
}

/**********   Muestra la vista previa de un formato ****************/
function seleccionFormato(indice) {
    var formato = @json($formatos)[indice-1];
    formatoVistaPrevia(formato);
    reordenamientoLogos(formato['numero_logos']);
}

function formatoVistaPrevia(formato){
    var img = document.getElementById("imagen_previa");
    var div_logos = document.getElementById("div_logos");
    var tabla_orden =  document.getElementById("tabla_orden");

    img.hidden = true;
    div_logos.hidden = true;
    tabla_orden.innerHTML = '';

    if(formato != null) {
        if(formato['imagen_previa'] != null && formato['imagen_previa'] != "") {
            img.hidden = false;
            img.src = "/images/"+formato['imagen_previa'];
        }
        
        if(formato['numero_logos'] > 0) 
            div_logos.hidden = false;    

        for(var i = 1; i <= formato['numero_logos']; i++){
            tabla_orden.innerHTML += '<tr style="height:45px;"><td style="border-collapse: collapse; border: none;">Logo '+i+'</th></tr>';
        }
    }
}


function saveImage() {
    var url_save_image = "{{action('LogosController@store')}}";
    var file_data = $('#logo').prop('files')[0];
    var caso_id = document.getElementById("caso_id").value;
    var form_data = new FormData();
    form_data.append('logo', file_data);
    form_data.append('caso_id', caso_id);

    $.ajax({
        url: url_save_image,
        method: 'POST',
        data: form_data,
        contentType : false,
        processData : false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response){   
            var urlEliminarArchivo = "{{ route('casos_logos.destroy',':id') }}";
            var urlEliminar = urlEliminarArchivo.replace(':id', response.id);

            var str_row = `<tr id="${response.id}" 
            draggable="true" ondragstart="start()" ondragover="dragover()">
                <td width="40%">
                    <i title="Ordenar" class="fas fa-arrows-alt-v flecha_tipo_procedimiento"></i>${response.nombre}
                </td>
                <td width="10%">
                  <div class="div_btn_acciones"> 
                    <button class="delete-alert-logo btn" 
                    data-reload="1" 
                    data-table="#tabla_logos" 
                    data-message1="No podrás recuperar el registro." 
                    data-message2="¡Borrado!" 
                    data-message3="El registro ha sido borrado." data-method="DELETE"
                    data-message4="${response.id}"
                    data-method="DELETE"
                    data-action="${urlEliminar}"
                    title="Eliminar Logo">
                    <i class="far fa-trash-alt"></i>
                    </button>
                  </div>
                </td>
            </tr>`;
            $('#tabla_logos').find('tbody').append(str_row);
            toastr.success('Imagen subida correctamente.', '', {timeOut: 3000});
            var formato = @json($formatos)[document.getElementById("select_format").selectedIndex-1];
            reordenamientoLogos(formato['numero_logos']);
        }
    });
}

function reordenamientoLogos(numero_logos){
    document.getElementById('old_ids').value = '';
    if(numero_logos > 0){
        var arrIds = [];
        var rows = document.getElementById('tabla_logos').rows;
        
        for (var i = 0; i < rows.length; i++){
            if(i < numero_logos)
                arrIds.push(rows[i].id);
        }
        document.getElementById('old_ids').value = arrIds;
    }

    ocultarElementos(numero_logos);
}

var row;

function start(){
  row = event.target;
}

function dragover(){
  var e = event;
  e.preventDefault();

  let children= Array.from(e.target.parentNode.parentNode.children);
  if(children.indexOf(e.target.parentNode)>children.indexOf(row))
    e.target.parentNode.after(row);
  else
    e.target.parentNode.before(row);
    
    var formato = @json($formatos)[document.getElementById("select_format").selectedIndex-1];
    reordenamientoLogos(formato['numero_logos']);
}

$('body').on('click','.delete-alert-logo',function(event){
      var url = $(this).attr('data-action');
      var table = $(this).attr('data-table');
      var reload = $(this).attr('data-reload');
      var method = $(this).attr('data-method');
      var message1 = $(this).attr('data-message1');
      var message2 = $(this).attr('data-message2');
      var message3 = $(this).attr('data-message3');
      var logoId = $(this).attr('data-message4');
      var to = $("#token").val();

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
          $.ajax({
            type: method,
            headers:{"X-CSRF-TOKEN": to},
            url: url,
            cache: false,
            dataType: 'json',
            data: {
                "_token": to,
                "_method": method
            },
            success: function(data) {
              document.getElementById(logoId).remove();
              var formato = @json($formatos)[document.getElementById("select_format").selectedIndex-1];
              reordenamientoLogos(formato['numero_logos']);
              Swal.fire(
               message2,
               message3,
               'success'
              );
            },
            error: function(jqXHR, textStatus, errorThrown){

              //$(table).load(" "+table);

              if(jqXHR.status == 422){
                $.parseJSON(jqXHR.responseText);
              }
              else{
                message = '{{__("No se pudo eliminar el Formato.")}}';
              }
              Swal.fire(
               'Error!',
               message,
               'error'
              );
            },
          });
        }
      });
    });

</script>