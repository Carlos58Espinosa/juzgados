<script>
  $(document).ready(function() {
  		selectedMenu("menu_agrupacion"); 
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
			        		var cadHtml = `<li id="${data}" onclick="getFieldsByNav(this.id)" class="nav-item"><a id="${grupo}" class="nav-link" href="#" aria-current="page" title="Eliminar Grupo">${grupo}</a></li><button id="button_${data}" class="btn" style="width:40px; color: lightcoral;" onclick="deleteGroup(${data})"><i class="far fa-trash-alt"></i></button>`;
									document.getElementById("navs").innerHTML += cadHtml;	 
									document.getElementById("grupo_id").value = data;
									document.getElementById("grupo").value = "";
									document.getElementById("tabla_campos").innerHTML = "";
									$(".nav-tabs li").removeClass("active");
      						$("#" + data).addClass("active")

									//$('#'+grupo).focus();
			        },
			        error: function(){
			          toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
			        }
		      });	
			} else
					toastr.error('No se puede repetir el nombre del grupo.', '', {timeOut: 3000});
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
		        		//console.log(data);
		        		deleteElements(data['id'], 'group');
		        		fillMultiSelectFields(data);
		        },
		        error: function(){
		          toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
		        }
		  });
  }

  function deleteElements(id, option){
			//console.log("Option:"+option);
			switch(option){
					case "field":
							getFieldsByNav(document.getElementById("grupo_id").value);
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
      $("#" + id).addClass("active")
			//console.log("ID="+id);
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
			var cad = '';
		  cad += '<tr><th width="300px">Campo</th><th  width="100px">Acción</th></tr>';

		  for(let a of arr)
		  		cad += getHtmlStringField(a["campo"]);    

  		document.getElementById("tabla_campos").innerHTML = cad;
	}

	function getHtmlStringField(field) {
  		var cadHtml = `<tr><td>${field}</td><td><button class="btn" style="width:40px;
	color:lightcoral;" onclick="deleteField('${field}')" title="Eliminar Parámetro"><i class="far fa-trash-alt"></i></button></td></tr>`;
  		return cadHtml;
  }
	
	/*************  Agrega CAMPOS ***********/
  function addFields(){
			var grupo_id = document.getElementById("grupo_id").value;
			var campos = $('#campos_ids_aux').find(':selected');

			if(campos.length > 0 && grupo_id != ""){
					var url = "{{action('AgrupacionesController@store')}}";
					var arr_campos = [];
					for(const iter of campos)
							arr_campos.push(iter["value"]);				

					$.ajax({
		        dataType: 'json',
		        type:'POST',
		        url: url,
		        cache: false,
		        data: {'grupo_id' : grupo_id, 'campos' : JSON.stringify(arr_campos), '_token':"{{ csrf_token() }}"},
		        success: function(data){		        	
		        		$('#campos_ids_aux option:selected').remove();
		        		$("#campos_ids_aux").selectpicker("refresh");
		        		document.getElementById("tabla_campos").hidden = false;
		        		addRowsTableFields(arr_campos);
		        		/*if(grupo_id == 0){
		        				document.getElementById(nombre).id = data;
		        				document.getElementById("grupo_id").value = data;
		        		}*/
		        },
		        error: function(){
		          toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
		        }
		      });
			} else
					toastr.error('Selecciona un Grupo.', '', {timeOut: 3000});
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
		 	console.log(campo);
			var url = "{{action('AgrupacionesController@deleteGroupsAndFields')}}";
			$.ajax({
		        dataType: 'json',
		        type:'POST',
		        url: url,
		        cache: false,
		        data: {'nombre' : campo, 'option' : 'field', '_token':"{{ csrf_token() }}"},
		        success: function(data){		        
		        		//console.log(data);
		        		deleteElements(data['id'], 'field');
		        		fillMultiSelectFields(data);
		        },
		        error: function(){
		          toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
		        }
		  });
	}

	function fillMultiSelectFields(data){
			$('#campos_ids_aux').empty();
  		for(let d of data['campos'])
					$('#campos_ids_aux').append(`<option value="${d['campo']}">${d['campo']}</option>`);
  		$("#campos_ids_aux").selectpicker("refresh");
	}

</script>