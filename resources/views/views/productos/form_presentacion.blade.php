
<div id="div_presentacion" hidden>

  <div class="row mt-4">

    <div class="col-xs-12 col-sm-4">
      <div class="div_th_borde_blanco etiqueta_ancho">
          <div><img src="{{ asset('images/bolsa.png') }}"><span class="span_presentacion">Presentación</span></div>
      </div>

      <div class="div_busqueda_blanco input-ancho">
        <div class="div_busqueda_azul"> 
          <select class="form-select select_productos_alta select-custom" name="presentacion_id" id="presentacion">
          @foreach($presentaciones as $r)
            <option value="{{$r->id}}" {{ ( $registro ? $registro->presentacion_id : old('presentacion_id') ) == $r->id ? 'selected' : '' }}>{{$r->nombre}}</option>
          @endforeach
          </select>
        </div>
      </div> 
    </div>

    <div class="col-xs-12 col-sm-4">
      <div class="div_th_borde_blanco etiqueta_ancho">
        <div>Valor</div>
      </div>

      <div class="div_busqueda_blanco input-ancho">
        <div class="div_busqueda_azul"> 
          <input type="text" class="select_productos_alta input-custom" name="valor" value="{{ $registro ? $registro->valor : old('valor') }}">
          @error('valor')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
          @enderror
        </div>
      </div>
        
    </div>

    <div class="col-xs-12 col-sm-4">
      <div class="div_th_borde_blanco etiqueta_ancho">
        <div>Unidad de Valor</div>
      </div>

      <div class="div_busqueda_blanco input-ancho">
        <div class="div_busqueda_azul"> 
          <select class="form-select select_productos_alta select-custom" aria-label="Default select example" name="unidad_valor_id" id="unidad_valor">
          @foreach($unidad_valores as $r)
            <option value="{{$r->id}}" {{ ( $registro ? $registro->unidad_valor_id : old('unidad_valor_id') ) == $r->id ? 'selected' : '' }}>{{$r->nombre}}</option>
          @endforeach
          </select>
        </div>
      </div>
        
    </div>
  </div>
</div>