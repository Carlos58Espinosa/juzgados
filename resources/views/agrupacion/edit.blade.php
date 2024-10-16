@extends('layout')

@section('content') 

<div class="main-content">
  <div class="section__content section__content--p30">
    <div class="container-fluid">
      <div class="card" id="card-section"> 
      @csrf 

      	<div class="container">
      			<input type ="hidden" id="grupo_id" name="grupo_id">

						<div class="col-12 col-sm-6 col-md-4">
							<div class="form-group">
								<label for="">Agregar Grupo:</label> 
							 	<input style="text-transform: none; float: left;" type="text" class="form-control" id="grupo" name="grupo" onkeydown="return /[0-9,a-z, ]/i.test(event.key)">
							 	<a onclick="addGroup()" class="btn btn-info" style="width: 40px; margin-left:400px; margin-top: -60px;"><i class="fas fa-plus"></i></a> 
							</div>
						</div>

				    <div style="margin-left: 230px;" class="col-12 col-sm-6 col-md-4">
				        <div class="form-group">

				            <label for="">Claves:</label>
				            <select class="form-control selectpicker" data-style="form-control" data-live-search="true" title="-- Selecciona Clave --" multiple="multiple" name="campos_ids[]" id="campos_ids_aux">
					              @foreach($campos as $campo)
					                <option value="{{$campo->campo}}">{{$campo->campo}}</option>
					              @endforeach
				            </select>
				            <a onclick="addFields()" class="btn btn-info" style="width: 40px; margin-left:400px; margin-top: -60px;"><i class="fas fa-plus"></i></a>

				        </div>
				    </div>
    		</div>

				<ul class="nav nav-tabs" id="navs" role="tablist" >
						@foreach($grupos as $grupo)
								<li id="{{$grupo->id}}" onclick="getFieldsByNav(this.id, '{{$grupo->nombre}}')" class="nav-item">
											<a id="{{$grupo->nombre}}" class="nav-link" href="#">{{$grupo->nombre}}</a>										
								</li>
								<button id="button_{{$grupo->id}}" class="btn btn-link" style="width:40px; margin: 0; padding: 0;" onclick="deleteGroup({{$grupo->id}})">
										<i class="far fa-trash-alt"></i>
								</button>
						@endforeach
				</ul>

				<div class="container">
						<table id="tabla_campos" class="table table-info">					
						</table>
				</div>

      </div>
    </div>
  </div>
</div> 

<script>
  $(document).ready(function() {
  		document.getElementById("grupo").value = "";
  		document.getElementById("grupo_id").value = "";
  });

  /**************************  Agregar GRUPO  **************************************************/
  function addGroup(){
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
			        		console.log("GRUPO ID="+data);
			        		var cadHtml = `<li id="${data}" onclick="getFieldsByNav(this.id)" class="nav-item"><a id="${grupo}" class="nav-link" href="#">${grupo}</a></li><button id="button_${data}" class="btn btn-link" style="width:40px;" onclick="deleteGroup(${data})"><i class="far fa-trash-alt"></i></button>`;
									document.getElementById("navs").innerHTML += cadHtml;	 
									document.getElementById("grupo_id").value = data;
									document.getElementById("grupo").value = "";
									document.getElementById("tabla_campos").innerHTML = "";

									$('#'+grupo).focus();
			        },
			        error: function(){
			          toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
			        }
		      });	
			} else
					toastr.error('No se puede repetir el nombre del grupo.', '', {timeOut: 3000});
	}

	/***************************************  Eliminar GRUPO  ************************************************/

  function deleteGroup(id){
  		var url = "{{action('AgrupacionesController@deleteGroupsAndFields')}}";
			$.ajax({
		        dataType: 'json',
		        type:'POST',
		        url: url,
		        cache: false,
		        data: {'grupo_id' : id, 'option' : 'group', '_token':"{{ csrf_token() }}"},
		        success: function(data){		        
		        		console.log(data);
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
			console.log("ID:"+id);
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
  
	/**************************  Recupera los CAMPOS de un GRUPO  **************************************************/

	function getFieldsByNav(id){
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
        		if(data.length > 0)       	
        			createTableFields(data);
        },
        error: function(){
          toastr.error('Hubo un problema por favor intentalo de nuevo mas tarde.', '', {timeOut: 3000});
        }
      });
	}

	function createTableFields(arr){
			var cad = '';
		  cad += '<tr class="table-info"><th scope="col">Campo</th><th scope="col">Acción</th></tr>';

		  for(let a of arr)
		  		cad += getHtmlStringField(a["campo"]);    

  		document.getElementById("tabla_campos").innerHTML = cad;
	}

	function getHtmlStringField(field) {
  		var cadHtml = `<tr class="table-info"><td>${field}</td><td><button class="btn btn-link" style="width:40px;" onclick="deleteField('${field}')"><i class="far fa-trash-alt"></i></button></td></tr>`;
  		return cadHtml;
  }
	
	/**************************  Agrega CAMPOS **************************************************/
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
		        		addRowsTableFields(arr_campos);
		        		if(grupo_id == 0){
		        				document.getElementById(nombre).id = data;
		        				document.getElementById("grupo_id").value = data;
		        		}
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
					cad += '<tr class="table-info"><th scope="col">Campo</th><th scope="col">Acción</th></tr>';
			
		  for(let a of arr)
		  		cad += getHtmlStringField(a);  
  		document.getElementById("tabla_campos").innerHTML += cad;
	}

	/***************************************  Elimina CAMPO  ************************************************/
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
@stop