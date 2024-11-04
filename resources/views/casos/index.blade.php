@extends('layout')

@section('content')

@include('general.general_methods')

  <div>
      <a href="{{action('CasosController@create')}}" class="btn boton_agregar"><i class="fas fa-plus"></i></a>
      <input type="text" class="input_search" name="busqueda_texto" placeholder="Busqueda de Expedientes" oninput="search(this.value)">               
  </div>

  <br>

  <table id="table_index" class="table" width="100%">
        <thead>
          <tr>
            <th>Nombre del Caso / Cliente</th>            
            <th>Tipo de Procedimiento</th>
            <th>Etapa</th>
            <th width="10%">Ultima Actualización</th>
            <th width="15%">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach($casos as $caso)
            <tr>
              <td>{{$caso->nombre_cliente}}</td>              
              <td>{{$caso->configuracion->nombre}}</td>
              <td>{{$caso->etapa_plantilla->nombre}}</td>
              <td>{{date("d/m/Y", strtotime($caso->updated_at))}}</td>
              <td>
                <div class="div_btn_acciones">

                  <div>
                      <a href="{{action('CasosController@show',$caso->id)}}" class="btn" style="width:40px; margin: 0"><i class="far fa-eye"></i></a>
                  </div>

                  <div>
                    <form method="POST" action="{{action('CasosController@getSensitiveData')}}">
                    @csrf
                        <input type="hidden" name="caso_id" value="{{$caso->id}}">
                        <button class="btn"><i class="far fa-keyboard"></i></button>
                    </form>
                  </div>

                  <div>
                      <a href="{{action('CasosController@edit',$caso->id)}}" class="btn"><i class="far fa-edit"></i></a>
                  </div>

                  <div class="col-4 active" style="padding: 0;">
                      <button class="delete-alert btn" data-reload="1" data-table="#table_index" data-message1="No podrás recuperar el registro." data-message2="¡Borrado!" data-message3="El registro ha sido borrado." data-method="DELETE" data-action="{{action('CasosController@destroy',$caso->id)}}"><i class="far fa-trash-alt"></i></button>
                  </div>

                </div>
              </td>
            </tr>
            @endforeach
        </tbody>
  </table>

<script>
  $(document).ready(function() {
      console.log("COLOR Plantillas:"+@json($color));
      document.getElementById("type_config").value = @json($color);
      loadColor('index');
  });
</script>
@stop