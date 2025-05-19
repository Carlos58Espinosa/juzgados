
<div id="div_sucursales" hidden>
  <div class="mt-5">

    <div class="row">

      <div class="col-xs-12 col-sm-4">
        <div class="div_th_borde_blanco etiqueta_ancho">
            <div><img src="{{ asset('images/ubicacion.png') }}"><span class="span_presentacion">Sucursales</span></div>
        </div>

        <div class="div_busqueda_blanco input-ancho">
          <div class="div_busqueda_azul multiple">
            <select  class="selectpicker" title="Selecciona las Sucursales" data-live-search="true" multiple="multiple" name="sucursales_ids[]" 
            id="sucursales_ids_aux" value="{{ old('sucursales_ids') }}">
            @foreach($sucursales as $r)
              <option value="{{$r->id}}" {{ in_array($r->id, $sucursales_ids) ? 'selected' : '' }} >{{$r->nombre}}</option>
            @endforeach
            </select>
          </div> 
        </div>
      </div>
    </div>

  </div>
</div>