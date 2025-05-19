<!--input type="text" id="sucursal_id_bd" name="sucursal_id_bd" hidden -->





<div class="div-button-save">
    <button type="submit" title="Guardar" class="btn"><img class="button-save" src="{{ asset('images/boton_guardar.png') }}"></button>         
</div>

<br>


<div class="row">

  <div class="col-xs-12 col-sm-4">
    <div class="div_th_borde_blanco etiqueta_ancho">
        <div><img src="../images/boneless.png">Tipo</div>
    </div>

    <div class="div_busqueda_blanco input-ancho">
      <div class="div_busqueda_azul">      
        <select class="form-select select_productos_alta select-custom" id="tipo_producto" name="tipo_producto_id" onchange="mostrarFormularios(this.selectedIndex)" required>
        @foreach($tipos_productos as $r)
          <option value="{{$r->id}}" {{ ( $registro ? $registro->tipo_producto_id : old('tipo_producto_id') ) == $r->id ? 'selected' : '' }}>{{$r->nombre}}</option>
        @endforeach
        </select>
      </div>
    </div>  
  </div>

  <div class="col-xs-12 col-sm-4">

    <div class="div_th_borde_blanco etiqueta_ancho">
      <div><img src="{{ asset('images/boneless.png') }}">Producto</div>
    </div>

    <div class="div_busqueda_blanco input-ancho">
      <div class="div_busqueda_azul"> 
        <select class="form-select select_productos_alta  select-custom" name="etiqueta_producto_id" required>
        @foreach($etiquetas_productos as $r)
          <option value="{{$r->id}}" {{ ( $registro ? $registro->etiqueta_producto_id : old('etiqueta_producto_id') ) == $r->id ? 'selected' : '' }}>{{$r->nombre}}</option>
        @endforeach
        </select>
      </div>  
    </div>
  </div> 

  <div class="col-xs-12 col-sm-4">

    <div class="div_th_borde_blanco etiqueta_ancho">
      <div><img src="{{ asset('images/detalle.png') }}">Detalle</div>
    </div> 

    <div class="div_busqueda_blanco input-ancho">
      <div class="div_busqueda_azul">    
        <input type="text" class="select_productos_alta input-custom" name="detalle" value="{{$registro ? $registro->detalle : old('detalle')}}">
        @error('detalle')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
        @enderror 
      </div>  
    </div> 
  </div>  