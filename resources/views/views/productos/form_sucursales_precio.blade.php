
<div id="div_sucursales_precios" align="center" hidden>
  <div class="mt-4">

    <div class="div_table_index_ventas div_productos_precios"> 
      <table id="tabla_listado_precios" class="table_index">
        <thead>
          <tr>
            <th>
              <div class="div_th_borde_blanco th_sucursal">
                <div><img src="{{ asset('images/ubicacion.png') }}">Sucursal</div>
              </div>
            </th>                   
            <th>
              <div class="div_th_borde_blanco">
                <div><img src="{{ asset('images/precio.png') }}">Precio</div>
              </div>
            </th>
          </tr>
        </thead>
        <tbody>
          @foreach($sucursales as $r)
            <tr>
              <td class="td_radius">
                <div class="div_td_sucursal">{{$r->nombre}}</div>
              </td>            
              <td class="td_radius">
                <div>
                  <input type="text" name="sucursal_{{$r->id}}" value="{{old($r->id)}}" placeholder="$" class="input_table_ventas">
                </div>
              </td> 
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

  </div>
</div>