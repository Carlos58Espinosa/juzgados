@extends('layout')

@section('content')

@include('general.general_methods')

  <div>
    <a href="{{action('ConfiguracionController@create')}}" class="btn boton_agregar"><i class="fas fa-plus"></i></a>
    <input type="text" class="input_search" name="busqueda_texto" placeholder="Busqueda de Tipo de Procedimientos" oninput="search(this.value)">               
  </div>

  <br>

  <table id="table_index" class="table" width="100%">
      <thead>
        <tr>
          <th>Nombre del Tipo de Procedimiento</th>
          <th width="10%">Última Actualización</th>
          <th width="15%">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($configuraciones as $config)
          <tr>
            <td>{{$config->nombre}}</td>
            <td>{{date("d/m/Y", strtotime($config->updated_at))}}</td>
            <td>
              <div class="div_btn_acciones">
                  <div>
                    <a class="btn" title="Ver Registro" href="{{action('ConfiguracionController@show',$config->id)}}"><i class="far fa-eye"></i></a>
                  </div>

                  <div>
                      <button class="btn" onclick='clone({{$config->id}}, "{{action('ConfiguracionController@clone')}}")' title="Clonar Registro"><i class="far fa-copy"></i></button>
                  </div>

                  @if($config->usuario->tipo != 'Administrador' || $tipo_usuario == 'Administrador')
                  <div>
                    <a href="{{action('ConfiguracionController@edit',$config->id)}}" class="btn btn-link" style="width:40px; margin: 0"><i class="far fa-edit"></i></a>
                  </div>

                  <div>
                    <button class="delete-alert btn btn-link" data-reload="1" data-table="#table_index" data-message1="No podrás recuperar el registro." data-message2="¡Borrado!" data-message3="El registro ha sido borrado." data-method="DELETE" data-action="{{action('ConfiguracionController@destroy',$config->id)}}"><i class="far fa-trash-alt"></i></button>
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
      selectedMenu("menu_procedimientos");      
      document.getElementById("type_config").value = @json($color);
      loadColor('index');
  });
</script>          
@stop