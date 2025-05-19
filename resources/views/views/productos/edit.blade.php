@extends('layout')

@section('content')

@include('general.metodos_productos')

  <form action="{{action('ProductoController@update', $registro->id)}}" method="POST" accept-charset="UTF-8" enctype="multipart/form-data">
  @csrf

      <input type="hidden" name="_method" value="PUT">

      @include('productos.form_inicio')
      <br>
      @include('productos.form_presentacion')  
      <br>
      @include('productos.form_sucursales') 
      <br> 
      @include('productos.form_sucursales_precio') 
      <br> 
      @include('productos.form_paquete')  
            
  </form>
  <script>
    $(document).ready(function() {
        mostrarFormularios(document.getElementById("tipo_producto").selectedIndex);
        
        var precios = @json($precios);
        precios.forEach(function(iter) {
            document.getElementsByName('sucursal_'+iter['sucursal_id'])[0].value = iter['precio'];
        });
    });    
  </script>
@stop
