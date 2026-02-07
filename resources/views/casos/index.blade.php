@extends('layout')

@section('content')

@include('general.general_methods')

<style>
  .modal.show {
    opacity: 1 !important;
    display: block !important;
    margin-left: 30%;
    margin-top: 150px;
    width: 700px;
  }
</style>

  <div>
      <a href="{{action('CasosController@create')}}" class="btn boton_agregar" title="Agregar Registro"><i class="fas fa-plus"></i></a>
      <input type="text" class="input_search" name="busqueda_texto" placeholder="Busqueda de Expedientes" oninput="search(this.value)">  
      <div align="right" style="padding-right: 100px; margin-top: -30px;">
        <form method="GET" action="{{action('CasosController@index')}}">
          <input type="hidden" name="inactivos" value="0">  
          <button name=""><i class="fa fa-eye"></i> Ver Inactivos</button>
        </form>
      </div>
  </div>

  <br>

  <table id="table_index" class="table" width="100%">
        <thead>
          <tr>
            <th>Nombre del Caso / Cliente</th>
            <th>Tipo de Creación</th>             
            <th>Tipo de Procedimiento</th>
            <th>Etapa</th>
            <th>Formato</th>
            <th>Tamaño de Hoja</th>
            <th width="10%">Ultima Actualización</th>
            <th width="20%">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach($casos as $caso)
            <tr>
              <td>{{$caso->nombre_cliente}}</td> 
              @if($caso->tipo_creacion == "1")
                <td>Libre</td>
              @else
                <td>Tipo de Procedimiento</td>
              @endif       
              @if($caso->configuracion != null)
                <td>{{$caso->configuracion->nombre}}</td>
              @else
                <td></td>
              @endif            
              <td>{{$caso->etapa_plantilla->nombre}}</td>              
              @if($caso->formato != null)
                <td>{{$caso->formato->nombre}}</td>
              @else
                <td></td>
              @endif
              <td>{{$caso->tamPapel}}</td>
              <td>{{date("d/m/Y", strtotime($caso->updated_at))}}</td>
              <td>
                <div class="div_btn_acciones">

                  <div>
                    <form method="POST" action="{{action('CasosController@getFormat')}}">
                    @csrf
                        <input type="hidden" name="caso_id" value="{{$caso->id}}">
                        <button class="btn" title="Formato de PDF"><i class="fas fa-file-invoice"></i></button>
                    </form>
                  </div>

                  @if($usuario_id == 8)
                  <div>
                    <form method="GET" action="{{action('ArchivosController@index')}}">
                    @csrf
                        <input type="hidden" name="caso_id" value="{{$caso->id}}">
                        <button class="btn" title="Subir Archivos"><i class="fas fa-cloud-upload-alt"></i></button>
                    </form>
                  </div>
                  @endif

                  <div>
                      <a href="{{action('CasosController@show',$caso->id)}}" class="btn" title="Ver Registro"><i class="far fa-eye"></i></a>
                  </div>

                  <div>
                    <form method="POST" action="{{action('CasosController@getSensitiveData')}}">
                    @csrf
                        <input type="hidden" name="caso_id" value="{{$caso->id}}">
                        <button class="btn" title="Sensibilidad de Datos"><i class="far fa-keyboard"></i></button>
                    </form>
                  </div>

                  <div>
                      <a href="{{action('CasosController@edit',$caso->id)}}" class="btn" title="Editar Registro"><i class="far fa-edit"></i></a>
                  </div>

                  <div>
                      <button class="delete-alert btn" data-reload="1" data-table="#table_index" data-message1="No podrás recuperar el registro." data-message2="¡Borrado!" data-message3="El registro ha sido borrado." data-method="DELETE" data-action="{{action('CasosController@destroy',$caso->id)}}" title="Desactivar Registro"><i class="far fa-trash-alt"></i></button>
                  </div>
                  @if($tipo_usuario == 'Cliente')
                  <div>
                    <button 
                      type="button" 
                      class="btn btn-asignar" 
                      data-toggle="modal" 
                      data-target="#modalAsignarExpediente" 
                      data-caso-id="{{ $caso->id }}" 
                      data-caso-nombre="{{ $caso->nombre_cliente }}"
                      title="Asignar Expediente">
                      <i class="fas fa-user-plus"></i>
                    </button>
                  </div>
                  @endif

                </div>
              </td>
            </tr>
            @endforeach
        </tbody>
  </table>


<!-- Modal Asignar Expediente -->
<div class="modal" id="modalAsignarExpediente" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="casos_colaboradores">
      @csrf
      <input type="hidden" name="caso_id" id="modal_caso_id">

      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalLabel">Asignar Colaboradores al Expediente:<span id="modal_caso_nombre"></span></h5>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label for="usuarios_select" class="form-label">Selecciona Colaboradores:</label>
            <select name="usuarios[]" id="usuarios_select" class="selectpicker form-control" multiple data-live-search="true" required>
              @foreach($colaboradores as $colaborador)
                <option value="{{ $colaborador->id }}">{{ $colaborador->nombre }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Asignar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-asignar').forEach(function (button) {
      button.addEventListener('click', function () {
        let casoId = this.getAttribute('data-caso-id');
        console.log("CASO ID = ",casoId);
        document.getElementById('modal_caso_id').value = casoId;
        document.getElementById('modal_caso_nombre').textContent = this.getAttribute('data-caso-nombre');

        // Limpiar selección anterior
        $('#usuarios_select').val([]).selectpicker('refresh');

        // ✅ Obtener colaboradores ya asignados usando la misma ruta index
        fetch(`/casos?option=colaboradores&caso_id=${casoId}`)
          .then(res => res.json())
          .then(data => {
            $('#usuarios_select').val(data).selectpicker('refresh');
          })
          .catch(err => console.error('Error al obtener colaboradores:', err));
        });
    });
  });
</script>
@stop