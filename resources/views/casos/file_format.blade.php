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

    <!-- Sección para subir un nuevo logo -->
    <div id="div_file" hidden>
	   	<label>Subir Nueva Imagen:</label>
	   	<br>
	   	<div style="width: 400px;">
	    	<input type="file" name="logo" id="logo" class="form-control" style="float:left; width: 350px;">
	    	<button style="margin-top:-60px; margin-left: 360px;" type="button" onclick="saveImage()" class="btn boton_guardar" title="Guardar Logo"><i class="far fa-image" alt="Guardar Logo"></i></button>
	    </div> 
    </div>

    <!-- Opciones para dar Formato -->

    <div class="container">
      <div class="row g-4 align-items-start">

        <!-- Columna 1 -->
        <div class="col-lg-4 col-md-6">
            <label class="form-label">Tamaño de Hoja <span class="text-danger">*</span></label>
            <select class="form-select" id="select_tam" name="tamPapel" required>
                <option value="">-- Selecciona un Tamaño --</option>
                @foreach($tamPapeles as $tamP)
                    <option value="{{$tamP->nombre}}" {{ $caso->tamPapel == $tamP->nombre ? 'selected' : '' }}>
                        {{$tamP->nombre}}
                    </option>
                @endforeach
            </select>

            <div class="mt-3">
                <label class="form-label">Margen X <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="margenDerIzq" value="{{$caso->margenDerIzq}}" required>
            </div>

            <div class="mt-3">
                <label class="form-label">Posición del Paginado <span class="text-danger">*</span></label>
                <select class="form-select" id="select_paginado" name="paginado" required>
                    <option value="">-- Selecciona el Paginado --</option>
                    @foreach($paginados as $paginado)
                        <option value="{{$paginado->nombre}}" {{ $caso->paginado == $paginado->nombre ? 'selected' : '' }}>
                            {{$paginado->nombre}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Columna 2 -->
        <div class="col-lg-4 col-md-6">
            <label class="form-label">Formato <span class="text-danger">*</span></label>
            <select class="form-select" id="select_format" onchange="seleccionFormato(this.selectedIndex)" name="formato_id" required>
                <option value="">-- Selecciona un Formato --</option>
                @foreach($formatos as $formato)
                    <option value="{{$formato->id}}" {{ $caso->formatoId == $formato->id ? 'selected' : '' }}>
                        {{$formato->nombre}}
                    </option>
                @endforeach
            </select>

            <div class="mt-3">
                <label class="form-label">Margen Y <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="margenArrAba" value="{{$caso->margenArrAba}}" required>
            </div>

            <input type="hidden" id="old_ids" name="old_ids[]" value="{{$old_ids[0]}}">
        </div>

        

        <!-- Columna 3 - Vista previa -->
        <div class="col-lg-4 text-center">
            <label class="form-label fw-bold">Vista previa</label>

            <div class="border rounded p-3 mt-2" style="min-height: 420px;">
                <img src="" id="imagen_previa" class="img-fluid" style="max-height: 380px;" hidden>
            </div>
        </div>
      </div>
    </div>

</form>

 <div id="div_logos"  style="height: 600px; width: 550px;margin-left: 300px; overflow-y: auto;" hidden>

    <table id="tabla_orden" style="float:left;" width="30%">
    </table>

    <table id="tabla_logos" width="70%">
        <tbody>
        @foreach($logos as $logo)
            <tr id="{{$logo->id}}" draggable='true' ondragstart='start()' ondragover='dragover()'>
                <td width="40%">
                    <i title="Ordenar" class="fas fa-arrows-alt-v flecha_tipo_procedimiento"></i>{{$logo->nombre}}
                </td>
                <td width="10%">
                    <div class="div_btn_acciones"> 
                        <button class="delete-alert-logo btn" data-reload="1" data-table="#tabla_logos" data-message1="No podrás recuperar el registro." data-message2="¡Borrado!" data-message3="El registro ha sido borrado." data-method="DELETE" data-message4="{{$logo->id}}" data-action="{{action('LogosController@destroy',$logo->id)}}" title="Eliminar Logo"><i class="far fa-trash-alt"></i></button>
                    </div>  
                </td>
            </tr>   
        @endforeach
        </tbody>
    </table>

</div> 
@stop