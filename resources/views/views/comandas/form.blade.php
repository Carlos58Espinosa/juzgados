  
   <div class="div_container_search_button">
    <form method="GET" action="{{action('InventarioController@descargarPDF')}}" target="_blank" class="m-0">
      <div class="d-flex flex-md-row align-items-stretch gap-2">
        <div class="container_search_button w-100 w-md-auto">
          <div class="input-group input-div h-100">
            <span class="input-group-text d-flex align-items-center">
              <p class="m-0 busqueda_total">Total ($)</p>
            </span>
            <input type="number" class="form-control input-busqueda" name="total_sin_descuento"  value="0" id="total_sin_descuento" required >
          </div>
        </div>

        <button class="button_action" title="Guardar">
          <img src="{{ asset('images/boton_guardar.png') }}">
        </button>

         
      </div>

    </form>
  </div>

  <div class="text-center">
    <div class="div_descuento ">
        <button class="btn" type="button" title="Disminuir Descuento" alt="Guardar" onclick="sumarRestarDescuento('-')"><i class="fa-solid fa-circle-minus" style="margin-bottom: 3px;"></i></button>
        <input type="text" value="20" id="descuento_porcentaje" name="descuento_porcentaje" readonly> 
        <button class="btn" type="button" title="Agregar Descuento" alt="Guardar" onclick="sumarRestarDescuento('+')"><i class="fa-solid fa-circle-plus" style="margin-bottom: 3px;"></i></button>
        <label for="check-active"><input id="con_descuento" type="checkbox" />Descuento</label> 
    </div>
  </div>
  
  
  
  <!--div class="div_container_search_button"> 

    <div class="div_busqueda_blanco">
      <div class="div_busqueda_azul">
        <div class="div_busqueda" align="center">

          <p>Total ($)</p><input placeholder="$" class="input_search input_total_comandas" type="text" name="total" value="{{$registro ? $registro->total : 0}}" id="total" required>
          <input type="text" name="total_sin_descuento" value="0" id="total_sin_descuento" required hidden=""> 

        </div> 
      </div>
    </div> 

    <button type="submit" class="button_action" title="Guardar"><img src="../images/boton_guardar.png"></button> 

    <div class="div_descuento">
        <button class="btn" type="button" title="Disminuir Descuento" alt="Guardar" onclick="sumarRestarDescuento('-')">-</button>
        <input type="text" value="20" id="descuento_porcentaje" 
        name="descuento_porcentaje" readonly> 
        <button class="btn" type="button" title="Agregar Descuento" alt="Guardar" onclick="sumarRestarDescuento('+')">+</button>
        <label for="check-active"><input id="con_descuento" type="checkbox" />Descuento</label> 
    </div>

  </div-->
  
 

  <div class="margen-derecho">
    <div class="row mt-3">
      @foreach($registros as $r)
        <div class="col-12 col-md-6 col-lg-6 col-xl-4 mb-3">
          <div class="div_container_comandas" style="width: -webkit-fill-available;">
            <div class="div_etiquetas_comandas text-center">
              @switch($r['nombre'])
                @case("Alitas")
                    <img src="{{ asset('images/etiqueta_alitas.png') }}" class="image-comanda">
                @break
                @case("Boneless")
                    <img src="{{ asset('images/etiqueta_boneless.png') }}" class="image-comanda">
                @break
                @case("Paquetes")
                    <img src="{{ asset('images/etiqueta_paquetes.png') }}" class="image-comanda">
                @break
                @case("Papas")
                    <img src="{{ asset('images/etiqueta_papas.png') }}" class="image-comanda">
                @break
                @case("Snacks")
                    <img src="{{ asset('images/etiqueta_snacks.png') }}" class="image-comanda">
                @break
                @default
                    <img src="{{ asset('images/etiqueta_extras.png') }}" class="image-comanda">
              @endswitch
              
            </div>
            
            <div class="div_table_index div_tabla_comandas"> 
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
                          <div><img src="{{ asset('images/precio.png') }}">Precio</div>
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
                  @foreach($r['productos'] as $p)
                    <tr>
                      <td>{{$p['detalle']}}</td>
                      <td>
                        <input type="number" id="precio_{{$p['id']}}" value="{{$p['precios'][0]['precio']}}" hidden>              
                        ${{$p['precios'][0]['precio']}}     
                      </td>
                      <td class="td_cantidad"> 
                        <div>
                          <button class="btn" type="button" title="Disminuir a Inventario" alt="Guardar" onclick="sumarRestarInventario('cantidad_'+{{$p['id']}}, '-')"><i class="fa-solid fa-circle-minus" style="margin-bottom: 3px;"></i></button>
                          <input type="text" value="0" id="cantidad_{{$p['id']}}" name="{{$p['id']}}" class="comandas_agregar_input input-quantity" readonly> 
                          <button class="btn" type="button" title="Agregar a Inventario" alt="Guardar" onclick="sumarRestarInventario('cantidad_'+{{$p['id']}}, '+')"><i class="fa-solid fa-circle-plus" style="margin-bottom: 3px;"></i></button>
                        </div>
                      </td> 
                      <!--td><input type="number" name="{{$p['id']}}" 
                        value="{{ (array_key_exists('detalle_comanda', $p) && $p['detalle_comanda'] ) ? $p['detalle_comanda']['cantidad'] : old($p['id'])}}" min="0" pattern="[0-9]{6}" style="width: 80px;" oninput="calcularTotal()"></td-->
                    </tr>  
                  @endforeach
                  </tbody>
                </table>
            </div>

          </div>
        </div>
      @endforeach
    </div>
  </div>