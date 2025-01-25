@extends('layout')

@section('content')

@include('general.general_methods')

  <div>
      <a href="{{action('CasosController@create')}}" class="btn boton_agregar" title="Agregar Registro"><i class="fas fa-plus"></i></a>
      <input type="text" class="input_search" name="busqueda_texto" placeholder="Busqueda de Expedientes" oninput="search(this.value)">  

      <div align="right" style="padding-right: 100px; margin-top: -30px;">
        <form method="GET" action="{{action('CasosController@index')}}">
          <input type="hidden" name="inactivos" value="1">           
          <button name=""><i class="fa fa-eye" style="color:lightcoral;"></i> Ver Activos</button>
        </form>
      </div>
  </div>

  <br>

  <table id="table_index" class="table" width="100%">
        <thead>
          <tr>
            <th>Nombre del Caso / Cliente</th>            
            <th>Tipo de Procedimiento</th>
            <th>Etapa</th>
            <th>Formato</th>
            <th>Tamaño de Hoja</th>
            <th width="10%">Ultima Actualización</th>
            <th width="20%">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach($casos as $caso)
            <tr>
              <td>{{$caso->nombre_cliente}}</td>              
              <td>{{$caso->configuracion->nombre}}</td>
              <td>{{$caso->etapa_plantilla->nombre}}</td>              
              @if($caso->formato != null)
                <td>{{$caso->formato->nombre}}</td>
              @else
                <td></td>
              @endif
              <td>{{$caso->tamPapel}}</td>
              <td>{{date("d/m/Y", strtotime($caso->updated_at))}}</td>
              <td>
                    <div class="div_btn_acciones">
                      <button class="delete-alert btn btn-link" data-reload="1" data-table="#table_index" data-message1="¿Quieres activar el Expediente?" data-message2="Activado" data-message3="Expediente Activado." data-method="DELETE" data-action="{{action('CasosController@destroy',$caso->id)}}" style="width:40px; margin: 0; padding: 0" title="Activar Registro"><i class="fas fa-check"></i></button>
                    </div>
              </td>
            </tr>
            @endforeach
        </tbody>
  </table>

<script>
  $(document).ready(function() {
      selectedMenu("menu_expedientes"); 
  });
</script>
@stop