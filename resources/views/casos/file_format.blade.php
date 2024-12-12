@extends('layout')

@section('content')

@include('casos.file_format_methods')

<form action="{{action('CasosController@saveFormat')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
@csrf

      <div>
          <a href="{{session('urlBack')}}" title="Regresar" class="btn boton_agregar"><i class="fas fa-long-arrow-alt-left"></i></a>
          <button type="submit" class="btn boton_guardar" title="Guardar Registro"><i class="fa fa-save" alt="Guardar"></i></button>         
      </div>

      <input type="hidden" name="caso_id" id="caso_id" value="{{$casoId}}">

      <input type="hidden" value="{{$caso->formatoId}}">
	 
      <br>      

      <div>

      	<div>
		   	<label for="">Subir Nuevo Archivo:</label>
		   	<br>
		   	<div style="width: 300px;">
		    	<input type="file" name="logo" id="logo" class="form-control" style="float:left; width: 250px;">
		    	<button style="margin-top:-60px; margin-left: 260px;" type="button" onclick="saveImage()" class="btn boton_guardar" title="Guardar Logo"><i class="far fa-image" alt="Guardar Logo"></i></button>
		    </div> 

	  	</div>

      	<div align="center" style="height: 200px; width: 300px; float: left; margin-left: 400px;">

      		<label for="">Formato: <span style="color:red">*</span></label>      		
      		<br>
            <select class="selectpicker input_nombre" id="select_format" onchange="seleccionFormato(this.selectedIndex)" name="formato_id" title="-- Selecciona un Formato --" data-live-search="true" required>
                @foreach($formatos as $formato)
                	<option value="{{$formato->id}}" {{ $caso->formatoId == $formato->id ? 'selected' : '' }}>{{$formato->nombre}}</option>
                @endforeach
            </select>
            @error('formato_id')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror

            <br>
            <br>
            <label for="">Tamaño de Hoja: <span style="color:red">*</span></label>      		
      		<br>
            <select class="selectpicker input_nombre" id="select_tam" name="tamPapel" title="-- Selecciona un Tamaño --" data-live-search="true" required>
                @foreach($tamPapeles as $tamP)
                	<option value="{{$tamP->nombre}}" {{ $caso->tamPapel == $tamP->nombre ? 'selected' : '' }}>{{$tamP->nombre}}</option>
                @endforeach
            </select>
            @error('tamPapel')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror

            <br>
            <br>

            <label for="">Margen Arriba - Abajo: <span style="color:red">*</span></label>
            <br>
            <input type="number" name="margenArrAba" value="{{$caso->margenArrAba}}" required>
      		
	        <br>
            <br>

            <label for="">Margen Izquierda - Derecha: <span style="color:red">*</span></label>
            <br>
            <input type="number" name="margenDerIzq" value="{{$caso->margenDerIzq}}" required>

            <br>
            <br>
            <label for="">Selecciona la Posición del Paginado: <span style="color:red">*</span></label>      		
      		<br>
            <select class="selectpicker input_nombre" id="select_paginado" name="paginado" title="-- Selecciona el Paginado --" data-live-search="true" required>
                @foreach($paginados as $paginado)
                	<option value="{{$paginado->nombre}}" {{ $caso->paginado == $paginado->nombre ? 'selected' : '' }}>{{$paginado->nombre}}</option>
                @endforeach
            </select>
            @error('paginado')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror

            <br>
            <br>
            <div id="div_logos">

            	<input type ="hidden" id="old_ids" name="old_ids[]" value="{{$old_ids[0]}}">

		        <label for="">Logos: <span style="color:red">*</span></label>
		        <select class="form-control selectpicker" data-style="form-control" data-live-search="true" title="-- Selecciona los Logos --" multiple="multiple" name="logos_ids[]" id="logos_ids_aux" onchange="seleccionLogos()">
		            @foreach($logos as $logo)
		        	    <option  {{ in_array($logo->id, $logos_ids)  ? 'selected':'' }}   value="{{$logo->id}}">{{$logo->nombre}}</option>
		            @endforeach
		        </select>

		        <br>
            	<br>

            	<table id="tabla_orden" style="float: left; width: 70px; padding: 8px; border: none;">
				</table>

		        <div id="div_list_group" style="width: 80%;">		        	 
		            <nav>
		                <ul onclick="reorderArrayIds()" id="list_templates" class="list-group connectedSortable"> 
		                </ul>
		            </nav>
		        </div>
	        </div>

      	</div>	

      	<div align="center" style="height: 400px; width: 350px; margin-left:800px;">
      		<label for="">Vista Previa:</label>   
      		<img src="" id="imagen_previa" style="position: relative;height: 400px; width: 350;" hidden />
      	</div>

      </div>

 </form>
@stop