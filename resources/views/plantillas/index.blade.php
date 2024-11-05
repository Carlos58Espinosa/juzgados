@extends('layout')

@section('content')

@include('general.general_methods')

  <div>
      <a href="{{action('PlantillasController@create')}}" class="btn boton_agregar" title="Agregar Registro"><i class="fas fa-plus"></i></a>
      <input type="text" class="input_search" name="busqueda_texto" placeholder="Busqueda de Plantillas" oninput="search(this.value)">
  </div>

  <br>

  <table id="table_index" class="table" width="100%">
      <thead>
        <tr>
          <th>Nombre de la Plantilla</th>
          <th width="45%">Texto / Contenido</th>
          <th width="5%">Última Actualización</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($plantillas as $plantilla)
          <tr>
            <td>{{$plantilla->nombre}}</td>
            <td><div style="height: 150px; overflow-y: scroll;">{!!$plantilla->texto!!}</div></td>
            <td>{{date("d/m/Y", strtotime($plantilla->updated_at))}}</td>
            <td>
              <div class="div_btn_acciones">

                <div>
                  <a class="btn" title="Ver Registro" href="{{action('PlantillasController@show',$plantilla->id)}}"><i class="far fa-eye"></i></a>
                </div>

                <div>
                  <button class="btn" onclick='clone({{$plantilla->id}}, "{{action('PlantillasController@clone')}}")' title="Clonar Registro"><i class="far fa-copy"></i></button>
                </div>

                <div>
                  <form method="POST" action="{{action('PlantillasController@viewPdf')}}" target="_blank">
                  @csrf
                    <input type="hidden" name="id" value="{{$plantilla->id}}">
                    <button class="btn" title="Ver PDF"><i class="far fa-file-pdf"></i></button>
                  </form>
                </div>

                @if($plantilla->usuario->tipo != 'Administrador' || $tipo_usuario == 'Administrador')
                <div>                    
                   <a class="btn" title="Editar Registro" href="{{action('PlantillasController@edit',$plantilla->id)}}"><i class="far fa-edit"></i></a>
                </div>               

                <div>
                  <button class="delete-alert btn" data-reload="1" data-table="#table_index" data-message1="No podrás recuperar el registro." data-message2="¡Borrado!" data-message3="El registro ha sido borrado." data-method="DELETE" data-action="{{action('PlantillasController@destroy',$plantilla->id)}}" title="Eliminar Registro"><i class="far fa-trash-alt"></i></button>
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
      selectedMenu("menu_plantillas"); 
      console.log("COLOR Plantillas:"+@json($color));
      document.getElementById("type_config").value = @json($color);
      loadColor('index');
  });
</script>

@stop