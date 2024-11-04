@extends('layout')

@section('content')

@include('general.general_methods')

<div>
    <a href="{{action('UsuariosController@create')}}" class="btn boton_agregar" style="width: 40px;"><i class="fas fa-plus"></i></a>
    <input type="text" class="input_search" name="busqueda_texto" placeholder="Busqueda de Usuario" oninput="search(this.value)">            
</div>

<br>

<table id="table_index" class="table" width="100%">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Email</th>
        <th>Última Actualización</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($usuarios as $usuario)
        <tr>
          <td>{{$usuario->nombre}}</td>
          <td>{{$usuario->email}}</td>
          <td>{{date("d/m/Y", strtotime($usuario->updated_at))}}</td>
          <td>
            <div class="div_btn_acciones">

              <div>
                <a class="btn" title="Ver Registro" href="{{action('UsuariosController@show',$usuario->id)}}"><i class="far fa-eye"></i></a>
              </div>

              <div>
                 <a class="btn" title="Editar Registro" href="{{action('UsuariosController@edit',$usuario->id)}}"><i class="far fa-edit"></i></a>
              </div>
              @if($usuario->id != $id)
              <div>
                <button class="delete-alert btn" data-reload="1" data-table="#table_index" data-message1="No podrás recuperar el registro." data-message2="¡Borrado!" data-message3="El registro ha sido borrado." data-method="DELETE" data-action="{{action('UsuariosController@destroy',$usuario->id)}}" title="Eliminar Registro"><i class="far fa-trash-alt"></i></button>
              </div>
              @endif

            </div>
          </td>
        </tr>
        @endforeach
    </tbody>
</table>
     

<script>
  $(document).ready(function() {
      document.getElementById("type_config").value = @json($color);
      loadColor('index');
  });
</script>

@stop