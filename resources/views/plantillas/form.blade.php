<form action="{{ $formAction }}" method="post" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div>
        <button type="submit" class="btn boton_guardar" title="Guardar Registro">
            <i class="fa fa-save" alt="Guardar"></i>
        </button>
    </div>

    <div align="center">
        <label>Nombre: <span style="color:red">*</span></label>
        <input type="text"
               class="form-control @error('nombre') is-invalid @enderror input_nombre"
               required
               name="nombre"
               value="{{ $nombreValue }}">
        @error('nombre')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

        <br>        
    </div>

    <label style="display:block; text-align:center;">Texto / Contenido de la Plantilla: <span style="color:red">*</span></label>

    <div style="margin-left:360px">        
        <textarea style="text-align:center;" name="texto" id="summernote" required>{{ $textoValue }}</textarea>
    </div>
</form>