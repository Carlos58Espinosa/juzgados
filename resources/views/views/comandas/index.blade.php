@section('styles')
    <style>
     
      

    </style>
  @endsection
    
  
  @extends('layout')

  @section('content')


  <div class="div_container_search_button">
      <div class="d-flex flex-md-row align-items-stretch gap-2">
        <div class="container_search_button w-100 w-md-auto">
          <div class="input-group input-div h-100">
            <span class="input-group-text d-flex align-items-center">
              <img class="img_input" src="{{ asset('images/busqueda_blanco.png') }}" alt="icono">
            </span>
            <input type="text" class="form-control input-busqueda" name="busqueda_texto" placeholder="Búsqueda de comandas" oninput="busqueda(this.value)">
          </div>
        </div>
        <a href="{{action('ComandaController@create')}}" class="button_action" title="Agregar Registro"><img src="{{ asset('images/boton_agregar.png') }}"></a>

      </div>

  </div>
  <br>




  <form action="{{action('ComandaController@index')}}" method="GET" accept-charset="UTF-8" enctype="multipart/form-data">

    <div class="div_container_search_button"> 

      <div class="d-flex flex-md-row align-items-stretch gap-2">
      <div class="container_search_button w-100 w-md-auto">
          <div class="input-group input-div h-100">
            <span class="input-group-text d-flex align-items-center">
              <i class="fa-solid fa-calendar-days color-icon-white"></i>
            </span>
            <input type="date" class="form-control input-busqueda" name="fecha_ini" value="{{$fecha_ini}}" min="2025-02-01" />
            <!--input type="text" class="form-control input-busqueda" name="busqueda_texto" placeholder="Búsqueda de comandas" oninput="busqueda(this.value)"-->
          </div>
        </div>
        <div class="container_search_button w-100 w-md-auto">
          <div class="input-group input-div h-100">
            <span class="input-group-text d-flex align-items-center">
              <i class="fa-solid fa-calendar-days color-icon-white"></i>
            </span>
            <input type="date" class="form-control input-busqueda" name="fecha_fin" value="{{$fecha_fin}}" min="2025-02-01" />
            <!--input type="text" class="form-control input-busqueda" name="busqueda_texto" placeholder="Búsqueda de comandas" oninput="busqueda(this.value)"-->
          </div>
        </div>
        <button class="btn button_action" type="submit" title="Buscar"><img class="" src="{{ asset('images/boton_buscar.png') }} "></button--> 


      </div>

      <!--div class="div_busqueda_blanco">
        <div class="div_busqueda_azul">
          <div class="div_calendar" align="center">
            <img src="{{ asset('images/calendario.png') }}">
            <input type="date" class="input_search input_calendar" name="fecha_ini" value="{{$fecha_ini}}" min="2025-02-01" />
          </div>  
        </div>
      </div>

      <div class="div_busqueda_blanco">
        <div class="div_busqueda_azul">
          <div class="div_calendar" align="center">
            <img src="{{ asset('images/calendario.png') }}">
            <input type="date" class="input_search input_calendar" name="fecha_fin" value="{{$fecha_fin}}" min="2025-02-01" />
          </div> 
        </div>
      </div>
      
      <button class="btn button_action" type="submit" title="Buscar"><img class="" src="{{ asset('images/boton_buscar.png') }} "></button--> 

    </div>

  </form>
  <div style="">
    <div class="container-custom">

      <div style="">
        <div class="div_table_index" > 
          <table id="tabla_listado" class="table_index   table-responsive">
              <thead>
                <tr> 
                <th>
                    <div class="div_th_borde_blanco">
                      <div><img src="{{ asset('images/ubicacion.png') }}">Sucursal</div>
                    </div>
                  </th>          
                  <th>
                    <div class="div_th_borde_blanco th_comandas_productos">
                      <div><img src="{{ asset('images/boneless.png') }}">Productos</div>
                    </div>
                  </th>          
                  <th>
                    <div class="div_th_borde_blanco">
                      <div><img src="{{ asset('images/bolsa.png') }}">Fecha</div>
                    </div>
                  </th>            
                  <th>
                    <div class="div_th_borde_blanco">
                      <div class="div_cantidad_th">Total</div>
                    </div>
                  </th>
                  <th>
                  </th>
                </tr> 
              </thead> 
              <tbody>
              @foreach($registros as $r)
                <tr id="{{$r->id}}"> 
                  <td class="td_radius_left">
                    <div class="div_td_sucursal">{{$r->sucursal->nombre}}</div> 
                  </td>
                  <td class="th_comandas_productos">
                    @foreach($r->detalle as $i)
                      {{$i->cantidad}} - {{$i->producto->etiqueta_producto->nombre}} {{$i->producto->detalle}}
                      <br>
                    @endforeach
                  </td>
                  <td>{{$r->dia}} / {{strlen($r->mes) == 1 ? '0'.$r->mes : $r->periodo}} / {{$r->year}}</td>
                  <td class="td_radius_right">${{$r->total}}</td>
                  <td class="td_buttons">

                    <a title="Editar Registro" href="{{action('ComandaController@edit',$r->id)}}"><img src="{{ asset('images/boton_editar.png') }}" class="button-image"></a>

                    <button class="btn delete-alert" data-reload="1" data-table="#tabla_listado" data-message1="No podrás recuperar el registro." data-message2="¡Borrado!" data-message3="El registro ha sido borrado." data-method="DELETE" data-action="{{action('ComandaController@destroy',$r->id)}}" title="Eliminar Registro"><img src="{{ asset('images/boton_eliminar.png') }}"  class="button-image"></button>

                  </td>
                </tr>
              @endforeach
            </tbody>        
          </table>
        </div>
      </div>
    </div>
  </div>


  @endsection

  @include('general.metodos')

@include('general.metodos_comandas')

@section('scripts')
<script>


  $('document').ready(function(){
      let $select = $('#ids');
      $select.on('change', () => {
        let selecteds = [];

        // Buscamos los option seleccionados
        $select.children(':selected').each((idx, el) => {
            selecteds.push(el.value);                  
        });

        var registros = @json($registros);

        registros.forEach(function(r) {
            var band = null;
            (selecteds.includes(r.sucursal_id.toString())) ? band = false : band = true;
            document.getElementById(r.id).hidden = band;

        });
      });
  });
        
  </script>

  @endsection
