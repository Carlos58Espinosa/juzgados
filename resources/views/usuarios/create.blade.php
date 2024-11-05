@extends('layout')

@section('content')

<form class="" action="{{action('UsuariosController@store')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
  @csrf

  <div>
      <a href="{{session('urlBack')}}" title="Regresar" class="btn boton_agregar"><i class="fas fa-long-arrow-alt-left"></i></a>
      <button type="submit" class="btn boton_guardar" title="Guardar Registro"><i class="fa fa-save" alt="Guardar"></i></button>
  </div>

  <br>

  <div align="center">
      <label for="">Tipo: <span style="color:red">*</span></label>
      <select class="form-control @error('tipo') is-invalid @enderror input_nombre" name="tipo" required="">
        @foreach($tipos as $tipo)
          <option value="{{$tipo}}" {{ old('tipo') == $tipo ? 'selected' : '' }}>{{$tipo}}</option>
        @endforeach
      </select>
      @error('tipo')
          <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
          </span>
      @enderror

      <br>
    
      <label for="">Nombre: <span style="color:red">*</span></label>
      <input style="text-transform: none;" type="text" class="form-control input_nombre" name="nombre" value="{{old('nombre')}}" required>
      @error('nombre')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
      @enderror

      <br>     

      <label for="">Email: <span style="color:red">*</span></label>
      <input style="text-transform: none;" type="email" class="form-control input_nombre" name="email" value="{{old('email')}}" required>
      @error('email')
        <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
      @enderror

      <br>

      <label for="">Password: <span style="color:red">*</span></label>
      <input style="text-transform: none;" type="password" class="form-control input_nombre" name="password" value="{{old('password')}}" required>
      @error('password')
        <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
      @enderror
  </div>

</form>
@stop