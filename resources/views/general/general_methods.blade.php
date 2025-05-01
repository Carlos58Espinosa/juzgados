<script>

function selectedMenu(id){
    document.getElementById(id).style.color = "white";
}

function search(valor) {
  const tableReg = document.getElementById('table_index');
  const searchText = valor.toLowerCase();
  let total = 0;

  // Recorremos todas las filas con contenido de la tabla
  for (let i = 1; i < tableReg.rows.length; i++) {
      // Si el td tiene la clase "noSearch" no se busca en su cntenido
      if (tableReg.rows[i].classList.contains("noSearch")) 
          continue;

      let found = false;
      const cellsOfRow = tableReg.rows[i].getElementsByTagName('td');
      // Recorremos todas las celdas
      for (let j = 0; j < cellsOfRow.length && !found; j++) {
          const compareWith = cellsOfRow[j].innerHTML.toLowerCase();
          // Buscamos el texto en el contenido de la celda
          if (searchText.length == 0 || compareWith.indexOf(searchText) > -1) {
              found = true;
              total++;
          }
      }

      if (found) 
          tableReg.rows[i].style.display = '';
      else {
          // si no ha encontrado ninguna coincidencia, esconde la
          // fila de la tabla
          tableReg.rows[i].style.display = 'none';
      }
  }

  // mostramos las coincidencias
  const lastTR=tableReg.rows[tableReg.rows.length-1];
  const td=lastTR.querySelector("td");

  lastTR.classList.remove("hide", "red");
  if (searchText == "")
      lastTR.classList.add("hide");
  else if (total) {
      td.innerHTML="Se ha encontrado "+total+" coincidencia"+((total>1)?"s":"");
  } else {
      lastTR.classList.add("red");
      td.innerHTML="No se han encontrado coincidencias";
  }
}

function changeColorConfiguration(valor) {
    valor = 0;
    if(document.getElementById("switch_night_day").checked)
        valor = 1;
    $.ajax({
          dataType: 'json',
          type:'POST',
          url: "{{action('UsuariosController@changeColorConfig')}}",
          cache: false,
          data: {'valor':valor,'_token':"{{ csrf_token() }}"},
          success: function(data){
          console.log('exito');  
              document.getElementById("modo_color").value = valor;
              loadColor('index');
          },
          error: function(){
            toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
          }
    }); 
}

function loadColor(option_form){
    var element_modo_color = document.getElementById("modo_color");
    var element = document.getElementById("main-content");
    var table = document.getElementById("table_index");
    if(table)
      var rows = table.getElementsByTagName("td");
    var switch_mode = document.getElementById("switch_night_day");
    var color_modo = "modo_noche";

    if(element_modo_color.value == 0){
        color_modo = "modo_dia";
        switch_mode.checked = false;
    }else
        switch_mode.checked = true;
    element.className = color_modo;
    if(table)
      table.className = color_modo;
    document.body.className = color_modo;
}

function clone(id, url_clone){
    var table = '#table_index';
    $.ajax({
          dataType: 'json',
          type:'POST',
          url: url_clone,
          cache: false,
          data: {'id' : id,'_token':"{{ csrf_token() }}"},
          success: function(data){  
              $(table).load(" "+table+" > *"); 
          },
          error: function(){
            toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
          }
    });
    loadColor(); 
}

function activate_user(id){
    var table = '#table_index';
    $.ajax({
          dataType: 'json',
          type:'POST',
          url: "{{action('UsuariosController@activateUser')}}",
          cache: false,
          data: {'id' : id,'_token':"{{ csrf_token() }}"},
          success: function(data){  
              //$(table).load(" "+table); 
              $(table).load(" "+table+" > *");
              toastr.success('Usuario Activado.', '', {timeOut: 3000});
          },
          error: function(){
            toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
          }
    });
}

$('body').on('click','.delete-alert',function(event){
      var url = $(this).attr('data-action');
      var table = $(this).attr('data-table');
      var reload = $(this).attr('data-reload');

      var method = $(this).attr('data-method');
      var message1 = $(this).attr('data-message1');
      var message2 = $(this).attr('data-message2');
        var message3 = $(this).attr('data-message3');
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
            type: "POST",
            headers:{"X-CSRF-TOKEN": to},
            url: url,
            cache: false,
            dataType: 'json',
            data: {
                "_token": to,
                "_method": method
            },
            success: function(data) {
              //console.log('success');
              $(table).load(" "+table+" > *");
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
                message = '{{__("Oops! there was an error, please try again later.")}}';
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