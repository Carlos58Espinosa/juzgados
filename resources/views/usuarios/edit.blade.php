@extends('layout')

@section('content')

<form class="" action="{{action('UsuariosController@update', $usuario->id)}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
  @csrf

  <input type="hidden" name="_method" value="PUT">
  
  <div>
      <a href="{{session('urlBack')}}" title="Regresar" class="btn boton_agregar"><i class="fas fa-long-arrow-alt-left"></i></a>
      <button type="submit" class="btn boton_guardar" title="Guardar"><i class="fa fa-save" alt="Guardar"></i></button>
  </div>

  <br>

  <div align="center">
      
      <label for="">Nombre: <span style="color:red">*</span></label>
      <input style="text-transform: none;" type="text" class="form-control input_nombre" name="nombre" value="{{$usuario->nombre}}" required>
      @error('nombre')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
      @enderror

      <br>     

      <label for="">Email: <span style="color:red">*</span></label>
      <input style="text-transform: none;" type="email" class="form-control input_nombre" name="email" value="{{$usuario->email}}" required>
      @error('email')
        <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
      @enderror

      <br>

      <label for="">Password: <span style="color:red">*</span></label>
      <input style="text-transform: none;" type="password" class="form-control input_nombre" name="password" value="">
      @error('password')
        <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
      @enderror
  </div>

</form>
@stop