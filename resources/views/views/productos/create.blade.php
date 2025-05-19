@extends('layout')

@section('content')

@include('general.metodos_productos')
<div class="container-custom p-0">

  <form class="" action="{{action('ProductoController@store')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
  @csrf

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
    });
  </script>
</div>
@stop