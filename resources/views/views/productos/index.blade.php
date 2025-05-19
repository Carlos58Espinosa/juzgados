  @extends('layout')


  @section('styles')
    <style>
  
    </style>
  @endsection

  @section('content')




  <div class="div_container_search_button">
      <div class="d-flex flex-md-row align-items-stretch gap-2">
        <div class="container_search_button w-100 w-md-auto">
          <div class="input-group input-div h-100">
            <span class="input-group-text d-flex align-items-center">
              <img class="img_input" src="{{ asset('images/busqueda_blanco.png') }}" alt="icono">
            </span>
            <input type="text" class="form-control input-busqueda" name="busqueda_texto" placeholder="Búsqueda de productos" oninput="busqueda(this.value)">
          </div>
        </div>

        <button class="btn button_action w-md-auto" onclick="window.location.href='{{ action('ProductoController@create') }}'" >
          <img src="{{ asset('images/boton_agregar.png') }}">
        </button>

        <!--div class="btn button_action w-md-auto">
          <a href="{{action('ProductoController@create')}}" class="button_action" title="Agregar Registro"><img src="{{ asset('images/boton_agregar.png') }}"></a>
        </div-->
      </div>

  </div>


  <!--div class="div_container_search_button"> 

    <div class="div_busqueda_blanco">
      <div class="div_busqueda_azul">
        <div class="div_busqueda" align="center">
          <img class="img_input" src="{{ asset('images/busqueda_blanco.png') }}">
          <input type="text" class="input_search" name="busqueda_texto" placeholder="  Búsqueda de productos" oninput="busqueda(this.value)"> 
        </div> 
      </div>
    </div>    

  </div> 

  <div class="div_container_search_button">
    <a href="{{action('ProductoController@create')}}" class="button_action" title="Agregar Registro"><img src="{{ asset('images/boton_agregar.png') }}"></a>
  </div-->  

  <br>

  <div class="container-custom">

    <div class="div_table_index table-responsive"> 
      <table id="tabla_listado" class="table_index tabla_productos_listado">
        <thead>
          <tr>
            <th>
              <div class="div_th_borde_blanco">
                  <div><img src="{{ asset('images/boneless.png') }}">Producto</div>
              </div>
            </th>        
            <th>
              <div class="div_th_borde_blanco">
                  <div><img src="{{ asset('images/bolsa.png') }}"><span class="span_presentacion">Presentación</span></div>
              </div>
            </th>
            <th>
              <div class="div_th_borde_blanco">
                  <div>Valor - Unidad</div>
              </div>
            </th>
            <th>
              <div class="div_th_borde_blanco">
                  <div>Sucursales - Precio</div>
              </div>
            </th>
            <th class="th_buttons">

            </th>
          </tr>
        </thead>
        <tbody>
          @foreach($registros as $r)
            <tr>
              <td class="td_radius_left">{{$r->etiqueta_producto->nombre}}</td>    
              <td>{{( $r->presentacion != null ) ? $r->presentacion->nombre : ''}} {{$r->detalle}}</td>
              <td>{{$r->valor}} {{( $r->unidad_valor != null ) ? $r->unidad_valor->nombre : ''}}</td>
              <td class="td_radius_right">
                @foreach($r->precios as $i)
                  <li>{{$i->sucursal->nombre}}  ${{$i->precio}}</li>
                @endforeach
              </td>
              <td class="td_buttons">                   
                <a title="Editar Registro" href="{{action('ProductoController@edit',$r->id)}}"><img src="{{ asset('images/boton_editar.png') }}" class="button-image"></a>
                <button class="btn delete-alert" data-reload="1" data-table="#tabla_index" data-message1="No podrás recuperar el registro." data-message2="¡Borrado!" data-message3="El registro ha sido borrado." data-method="DELETE" data-action="{{action('ProductoController@destroy',$r->id)}}" title="Eliminar Registro"><img src="{{asset('/images/boton_eliminar.png')}}" class="button-image"></button>
              </td>        
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  

@endsection

@include('general.metodos')

@section('scripts')
  <script>



   
  </script>
@endsection
