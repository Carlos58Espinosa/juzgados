@extends('layout')

@section('content')

@include('casos.file_upload_methods')

@csrf
	<div>
    	<a href="{{session('urlBack')}}" title="Regresar" class="btn boton_agregar"><i class="fas fa-long-arrow-alt-left"></i></a>        
    </div>

    <input type="hidden" name="caso_id" id="caso_id" value="{{$casoId}}">
	 
    <br>      

    <div align="center">
	   	<label for="">Subir Nuevo Archivo:</label>
	   	<br>
	   	<div style="width: 300px;">
	    	<input type="file" name="logo" id="archivo" class="form-control" style="float:left; width: 350px;">
	    	<button style="margin-top:-60px; margin-left: 355px;" type="button" onclick="saveFile()" class="btn boton_guardar" title="Guardar Archivo"><i class="far fa-image" alt="Guardar Archivo"></i></button>
	    </div> 
    </div>

    <div align="center">
	    <table id="tabla_archivos" width="400px">
	        <tbody>
	        @foreach($archivos as $archivo)
	            <tr id="{{$archivo->id}}">
	                <td width="20%">
	                	{{$archivo->nombre}}
	                </td>
	                <td width="10%">
	                    <button class="delete-alert-archivo btn" data-reload="1" data-table="#tabla_archivos" data-message1="No podrás recuperar el registro." data-message2="¡Borrado!" data-message3="El registro ha sido borrado." data-method="DELETE" data-message4="{{$archivo->id}}" data-action="{{action('ArchivosController@destroy',$archivo->id)}}" title="Eliminar Archivo"><i class="far fa-trash-alt"></i></button>
	                </td>
	            </tr>   
	        @endforeach
	        </tbody>
	    </table>
    </div>

@stop