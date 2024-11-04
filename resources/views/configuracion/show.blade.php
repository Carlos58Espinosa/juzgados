@extends('layout')

@section('content')

@include('general.general_methods')

  <div>
      <a href="{{session('urlBack')}}" title="Regresar" class="btn boton_agregar"><i class="fas fa-long-arrow-alt-left"></i></a>
  </div>

  <div align="center">

      <label for="">Nombre:</label>
      <input style="text-transform: none;" type="text" class="form-control @error('nombre') is-invalid @enderror input_nombre" name="nombre" value="{{$config->nombre}}" disabled>
      @error('nombre')
          <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
          </span>
      @enderror

      <br>

      <label style="text-align: center">Plantillas en el Procedimiento:</label>

      <br>
      <br>

      <table id="table_index" class="table" width="90%">
          <thead>
             <tr>
                <th width="25%">Nombre de la Plantilla</th>
                <th>Texto de la Plantilla</th>
                <th width="8%">Orden</th>
              </tr>
          </thead>
          <tbody>
           @foreach($config_plantillas as $cp)
              <tr>
                <td>{{$cp->plantilla->nombre}}</td>
                <td><div style="height: 150px; overflow-y: scroll;">{!!$cp->plantilla->texto!!}</div></td>
                <td>{{$cp->orden}}</td>
              </tr>
            @endforeach
          </tbody>
      </table>   

  </div>

  <script>
  $(document).ready(function() {
      document.getElementById("type_config").value = 0;
      loadColor('index');
  });
</script>       
  @stop