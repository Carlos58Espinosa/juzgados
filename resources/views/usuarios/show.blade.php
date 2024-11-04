@extends('layout')

@section('content')

  <div>
      <a href="{{session('urlBack')}}" title="Regresar" class="btn boton_agregar"><i class="fas fa-long-arrow-alt-left"></i></a>
  </div>

  <br>

  <div align="center">
      <label for="">Tipo:</label>
      <input style="text-transform: none;" type="text" class="form-control input_nombre"  name="nombre" value="{{$usuario->tipo}}" disabled>

      <br>
    
      <label for="">Nombre:</label>
      <input style="text-transform: none;" type="text" class="form-control input_nombre"  name="nombre" value="{{$usuario->nombre}}" disabled>

      <br>     

      <label for="">Email:</label>
      <input style="text-transform: none;" type="email" class="form-control input_nombre"  name="email" value="{{$usuario->email}}" disabled>

  </div>

@stop