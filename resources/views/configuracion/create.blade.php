@extends('layout')

@section('content')

@include('configuracion.config_methods')

    <div>
      <a href="{{session('urlBack')}}" title="Regresar" class="btn boton_agregar"><i class="fas fa-long-arrow-alt-left"></i></a>
    </div>

  <form class="" action="{{action('ConfiguracionController@store')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
  @csrf

      <div>
          <button type="submit" class="btn boton_guardar" title="Guardar Registro"><i class="fa fa-save" alt="Guardar"></i></button>
      </div>

      <input type="hidden" id="old_ids" name="old_ids" value="">

      <div class="container py-3" style="max-width:720px;">
            
        <label>Nombre: <span style="color:red">*</span></label>
        <input type="text" class="form-control @error('nombre') is-invalid @enderror input_nombre" name="nombre" value="{{old('nombre')}}" required>
        @error('nombre')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

        <br>

        <label>Plantillas: <span style="color:red">*</span></label>
        <select id="plantillas" class="form-select" multiple size="8">
            @foreach($plantillas as $p)
                <option value="{{ $p->id }}" {{ collect(old('plantillas_ids'))->contains($p->id) ? 'selected' : '' }}>
                    {{ $p->nombre }}
                </option>
            @endforeach
        </select>

        <br>

        <ul id="lista" class="list-group mt-2"></ul>

      </div>
            
  </form>
@stop