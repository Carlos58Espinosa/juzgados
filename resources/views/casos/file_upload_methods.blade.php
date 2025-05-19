<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.14.0/jquery-ui.js"></script>
<script>

function saveFile() {
    var url_save_file = "{{action('ArchivosController@store')}}";
    var file_data = $('#archivo').prop('files')[0];
    var caso_id = document.getElementById("caso_id").value;
    var form_data = new FormData();
    form_data.append('archivo', file_data);
    form_data.append('caso_id', caso_id);


    $.ajax({
        url: url_save_file,
        method: 'POST',
        data: form_data,
        contentType : false,
        processData : false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response){ 
        	var urlEliminarArchivo = "{{ route('casos_archivos.destroy',':id') }}";
        	var urlEliminar = urlEliminarArchivo.replace(':id', response.id);

		      var str_row = `<tr id="${response.id}">
		            <td width="20%">${response.nombre}</td>
		            <td width="10%">
                  <div class="div_btn_acciones">
		                <button
		                    class="delete-alert-archivo btn"
		                    data-reload="1"
		                    data-table="#tabla_archivos"
		                    data-message1="No podrás recuperar el registro."
		                    data-message2="Borrado"
		                    data-message3="El registro ha sido borrado."
		                    data-message4="${response.id}" 
		                    data-method="DELETE"
		                    data-action="${urlEliminar}"
		                    title="Eliminar Archivo">
		                    <i class="far fa-trash-alt"></i>
		                </button>
                    <a href="archivos/${response.nombre_final}" class="btn" download="${response.nombre}"><i class="fas fa-cloud-download-alt"></i></a>
                    <button onclick="visualizarArchivo('${response.nombre_final}')" class="btn"><i class="fa fa-eye"></i></button>
                   </div>
		            </td>
		        </tr>`;         
            $('#tabla_archivos').find('tbody').append(str_row);
            toastr.success('Archivo subido correctamente.', '', {timeOut: 3000});
        }
    });
}

$('body').on('click','.delete-alert-archivo',function(event){
	    var url = $(this).attr('data-action');
      var table = $(this).attr('data-table');
      var reload = $(this).attr('data-reload');
      var method = $(this).attr('data-method');
      var message1 = $(this).attr('data-message1');
      var message2 = $(this).attr('data-message2');
      var message3 = $(this).attr('data-message3');
      var archivoId = $(this).attr('data-message4');
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
            type: method,//"POST",
            headers:{"X-CSRF-TOKEN": to},
            url: url,
            cache: false,
            dataType: 'json',
            data: {
                "_token": to,
                "_method": method
            },
            success: function(data) {
              document.getElementById(archivoId).remove();
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
                message = '{{__("No se pudo eliminar el Archivo.")}}';
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

function visualizarArchivo(archivo){
  document.getElementById("contenido_visualizar").src = `/archivos/${archivo}`;
}
</script>