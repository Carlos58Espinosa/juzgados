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

    <div id="div_file" hidden>
	   	<label for="">Subir Nueva Imagen:</label>
	   	<br>
	   	<div style="width: 400px;">
	    	<input type="file" name="logo" id="logo" class="form-control" style="float:left; width: 350px;">
	    	<button style="margin-top:-60px; margin-left: 360px;" type="button" onclick="saveImage()" class="btn boton_guardar" title="Guardar Logo"><i class="far fa-image" alt="Guardar Logo"></i></button>
	    </div> 
    </div>

    <div style="height: 270px; width: 66%; float: left;">

        <div align="center" style="width: 50%; float: left;">
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

            <label for="">Margen X: <span style="color:red">*</span></label>
            <br>
            <input type="number" name="margenDerIzq" value="{{$caso->margenDerIzq}}" required>
           
            <br>
            <br>
            <label for="">Posición del Paginado: <span style="color:red">*</span></label>           
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
        </div>

        <div align="center" style="width: 50%; margin-left: 50%;">
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

            <label for="">Margen Y: <span style="color:red">*</span></label>
            <br>
            <input type="number" name="margenArrAba" value="{{$caso->margenArrAba}}" required>

            <input type ="hidden" id="old_ids" name="old_ids[]" value="{{$old_ids[0]}}">
        </div>

    </div>

    <div align="center" style="width: 33%; height: 600px; margin-left: 67%;">
        <label for="">Vista Previa:</label> 
        <br>
        <img src="" id="imagen_previa" style="position: relative;height: 400px; width: 300px;" hidden />
    </div>

 </form>

 <div id="div_logos"  style="height: 600px; width: 550px; margin-top: -400px; margin-left: 350px; overflow-y: auto;" hidden>

    

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