@extends('layout')

@section('content') 

@include('agrupacion.agrupacion_methods')

@csrf 
  <div class="row">

		 	<input type ="hidden" id="grupo_id" name="grupo_id">

			<div class="col-12 col-sm-6 col-md-4">
				<div class="form-group">
					<label for="">Agregar Grupo:</label> 
				 	<input style="text-transform: none; float: left;" type="text" class="form-control" id="grupo" name="grupo" onkeydown="return /[0-9,a-z, ]/i.test(event.key)">
				 	<a onclick="addGroup()" class="btn boton_guardar" title="Agregar Grupo" style="margin-left:430px; margin-top: -60px;"><i class="fas fa-plus"></i></a>				 	 
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
		          <a onclick="addFields()" class="btn boton_guardar" title="Agregar Parámetros al Grupo" style="margin-left:430px; margin-top: -60px;"><i class="fas fa-plus"></i></a>
		      </div>
		  </div>
  </div>

	<ul class="nav nav-tabs" id="navs" role="tablist" >
			@foreach($grupos as $grupo)
					<li id="{{$grupo->id}}" class="nav-item" onclick="getFieldsByNav(this.id, '{{$grupo->nombre}}')">
							<a id="{{$grupo->nombre}}" class="nav-link" href="#">{{$grupo->nombre}}</a>
					</li>
					 <button id="button_{{$grupo->id}}" class="btn" title="Eliminar Grupo" style="width:40px; margin: 0; padding: 0; color: lightcoral;" onclick="deleteGroup({{$grupo->id}})">
							<i class="far fa-trash-alt"></i>
					</button>
			@endforeach
	</ul>

	<br>

	<div style="margin-left:200px; width: 50%;">
			<table id="tabla_campos" class="table" style="width:50%;" hidden>		
			</table>
	</div>

@stop