@section('styles')
    <style>
     
      

    </style>
  @endsection
  
  
  @extends('layout')

 

  @section('content')

  <div class="div_container_search_button">
    <form method="GET" action="{{action('InventarioController@descargarPDF')}}" target="_blank" class="m-0">
      <div class="d-flex flex-md-row align-items-stretch gap-2">
        <div class="container_search_button w-100 w-md-auto">
          <div class="input-group input-div h-100">
            <span class="input-group-text d-flex align-items-center">
              <img class="img_input" src="{{ asset('images/busqueda_blanco.png') }}" alt="icono">
            </span>
            <input type="text" class="form-control input-busqueda" name="busqueda_texto" placeholder="Búsqueda de productos" oninput="busqueda(this.value)">
          </div>
        </div>

        <button class="btn button_action w-md-auto" title="Ver PDF">
          <img src="{{ asset('images/boton_pdf.png') }}">
        </button>
      </div>

    </form>
  </div>
  <br>


  <!--div class="div_container_search_button"> 

    

    <div class="div_busqueda_blanco">
      <div class="div_busqueda_azul">
        <div class="div_busqueda" align="center">
          <img class="img_input" src="{{ asset('images/busqueda_blanco.png') }}">
          <input type="text" class="input_search" name="busqueda_texto" placeholder="  Búsqueda de productos" oninput="busqueda(this.value)"> 
        </div> 
      </div>
    </div>

    <form method="GET" action="{{action('InventarioController@descargarPDF')}}" target="_blank">
        @csrf
        <button class="button_action" title="Ver PDF"><img src="{{ asset('images/boton_pdf.png') }}"></button>
    </form>

  </div--> 

  <div class="container-custom">

    <div class="div_table_index table-responsive"> 
      <table id="tabla_listado" class="table_index tabla_inventario_listado">
          <thead>
            <tr>           
              <th>
                <div class="div_th_borde_blanco">
                  <div><img src="{{ asset('images/boneless.png') }}"><span>Producto</span></div>
                </div>
              </th>          
              <th>
                <div class="div_th_borde_blanco">
                  <div><img src="{{ asset('images/bolsa.png') }}"><span class="span_presentacion">Presentación</span></div>
                </div>
              </th>
              <th>
                <div class="div_th_borde_blanco">
                  <div><img src="{{ asset('images/detalle.png') }}"><span>Detalle</span></div>
                </div>
              </th>
              <th>
                <div class="div_th_borde_blanco th_sucursal">
                  <div><img src="{{ asset('images/ubicacion.png') }}"><span>Sucursal</span></div>
                </div>
              </th>
              <th class="th_cantidad">
                <div class="div_th_borde_blanco">
                  <div class="div_cantidad_th"><span>Cantidad</span></div>
                </div>
              </th>
              <th class="th_buttons_1">
              </th>
            </tr> 
          </thead> 
          <tbody>
            @foreach($registros as $r)
              <tr id="{{$r->id}}" value="{{$r->sucursal_id}}">            
                <td class="td_radius_left">{{$r->producto->etiqueta_producto->nombre}}</td>
                <td>{{( $r->producto->presentacion != null ) ? $r->producto->presentacion->nombre : ''}}</td>
                <td>{{$r->producto->detalle}}</td>
                <td class="td_radius_right">
                  <div class="div_td_sucursal">{{$r->sucursal->nombre}}</div> 
                </td> 
                <td class="td_cantidad"> 
                  <div>
                    <button class="btn" title="Disminuir a Inventario" alt="Guardar" onclick="sumarRestarInventario('cantidad_'+{{$r->id}}, '-')"><i class="fa-solid fa-circle-minus" style="margin-bottom: 3px;"></i></button>
                    <input type="text" value="{{$r->cantidad}}" id="cantidad_{{$r->id}}" disabled class="input-quantity"> 
                    <button class="btn"  title="Agregar a Inventario" alt="Guardar" onclick="sumarRestarInventario('cantidad_'+{{$r->id}}, '+')"><i class="fa-solid fa-circle-plus" style="margin-bottom: 3px;"></i></button>
                  </div>
                </td>  
                <td class="td_buttons">                          
                  <button class="btn" type="submit" title="Ajustar Inventario" alt="Guardar" onclick="ajustarInventario({{$r->id}})"><img src="{{ asset('images/boton_guardar.png') }}" class="button-image"></button>
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
  $('document').ready(function(){
      
      $('#sucursales_ids_aux').change(function() {
          var suc_ids = [];
          $('option:selected', $(this)).each(function() {
              suc_ids.push($(this).val());
          });
          
          //console.log(suc_ids);
          var trs = Array.from(document.getElementsByTagName('tr'));
          //console.log(trs);
          trs.forEach(function(element){
              if(element.attributes.value)
                (!suc_ids.includes(element.attributes.value.nodeValue)) ? element.hidden = true : element.hidden = false;
          });

      });

      //document.getElementById("div_header_sucursal").hidden = true;
  });

  function ajustarInventario(id){
      var url = "{{ action('InventarioController@update', ':id') }}";
      url = url.replace(':id', id);
      var cantidad = document.getElementById('cantidad_'+id).value;

      if(cantidad != null && cantidad != ""){
        $.ajax({
          dataType: 'json',
          type:'PUT',
          url: url,
          cache: false,
          data: {'cantidad' : parseFloat(cantidad), '_token':"{{ csrf_token() }}"},
          success: function(data){
              console.log(data);
              toastr.success('Ajuste correcto.', '', {timeOut: 3000});
          },
          error: function(){
              toastr.error('Hubo un problema. No se pudo Ajustar el Inventario', '', {timeOut: 3000});
          }
        });
      }   
  }
</script>

@endsection