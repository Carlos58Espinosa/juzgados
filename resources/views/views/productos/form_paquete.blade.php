@include('general.metodos')

<div id="div_paquete" hidden> 
  <div class="mt-4" style="margin-right: 25px;">

    

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
      </div>

    </div>
    <div class="">
      <div class="div_table_index div_tabla_productos_paquet table-responsive mt-4" > 
        <table id="tabla_listado" class="table_index">
          <thead>
            <tr>
              <th>
                <div class="div_th_borde_blanco">
                  <div><img src="{{ asset('images/boneless.png') }}">Producto</div>
                </div>
              </th>                    
              <th>
                <div class="div_th_borde_blanco">
                  <div><img src="{{ asset('images/bolsa.png') }}">Presentación</div>
                </div>
              </th>
              <th>
                <div class="div_th_borde_blanco">
                  <div><img src="{{ asset('images/detalle.png') }}">Detalle</div>
                </div>
              </th>
              <th>
                <div class="div_th_borde_blanco">
                  <div>Valor - Unidad</div>
                </div>
              </th>
              <th>
                <div class="div_th_borde_blanco">
                  <div>Cantidad</div>
                </div>
              </th>
            </tr>
          </thead>
          <tbody>
            @foreach($productos as $r)
              <tr>
                <td class="td_radius_left">{{$r->etiqueta_producto->nombre}}</td>            
                <td>{{( $r->presentacion != null ) ? $r->presentacion->nombre : ''}}</td>
                <td>{{$r->detalle}} </td>
                <td class="td_radius_right">{{$r->valor}} {{( $r->unidad_valor != null ) ? $r->unidad_valor->nombre : ''}}</td>
              

                <td class="td_cantidad"> 
                  <div>
                    <button class="btn" title="Disminuir a Inventario" alt="Guardar" onclick="sumarRestarInventario({{$r->id}}, '-')"><i class="fa-solid fa-circle-minus" style="margin-bottom: 3px;"></i></button>
                    <input type="text" id="{{$r->id}}" name="{{$r->id}}" value="{{ ($r->detalle_producto) ? $r->detalle_producto->cantidad : old($r->id)}}" disabled class="input-quantity"> 

                    <button class="btn"  title="Agregar a Inventario" alt="Guardar" onclick="sumarRestarInventario({{$r->id}}, '+')"><i class="fa-solid fa-circle-plus" style="margin-bottom: 3px;"></i></button>
                  </div>
                </td>  
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div> 
  </div> 

</div>