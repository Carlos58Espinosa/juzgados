

<div class="modal fade" id="modal{{ ucfirst($prefix) }}" tabindex="-1">
  <div class="modal-dialog {{ $prefix === 'create' ? 'modal-lg' : '' }}">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          {{ $prefix === 'create' ? 'Nuevo Evento' : 'Editar Evento' }}
        </h5>
        <button type="button" class="btn-close btn-cancelar" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        @if($prefix === 'edit')
          <input type="hidden" id="edit_evento_id">
        @endif

        <!-- Título -->
        <div class="mb-3">
          <label class="form-label">Título</label>
          <input type="text" class="form-control" id="{{ $prefix }}_titulo_evento">
          @if($prefix === 'create')
            <input type="hidden" id="fecha_evento">
          @endif
        </div>

        <!-- Fecha (solo en edición) -->
        @if($prefix === 'edit')
        <div class="mb-3">
          <label class="form-label">Fecha</label>
          <input type="date" class="form-control" id="edit_fecha_evento">
        </div>
        @endif

        <!-- Estatus (solo en edición) -->
        @if($prefix === 'edit')
        <div class="mb-3">
          <label class="form-label">Estatus</label>
          <select id="edit_select_estatus" class="form-select">
            <option value="alta">Alta</option>
            <option value="completado">Terminado</option>
          </select>
        </div>
        @endif

        <!-- Expedientes -->
        <div class="mb-3">
          <label class="form-label">Expediente</label>
          <div class="input-group">
            <select id="{{ $prefix }}_select_expediente" class="form-select">
              @foreach($expedientes as $exp)
                <option value="{{ $exp['id'] }}">{{ $exp['nombre_cliente'] }}</option>
              @endforeach
            </select>
            <button class="btn btn-outline-primary" type="button" id="btnAgregarExpediente{{ ucfirst($prefix) }}">
              Agregar
            </button>
          </div>
        </div>

        <!-- Usuarios -->
        <div class="mb-3">
          <label class="form-label">Notificar a usuarios</label>
          <select id="{{ $prefix }}_usuarios_ids" class="form-select" multiple>
            @foreach($usuarios as $u)
              <option value="{{ $u->id }}">{{ $u->nombre }}</option>
            @endforeach
          </select>
        </div>

        <!-- Observaciones -->
        <div class="mb-3">
          <label class="form-label">Observaciones</label>
          <textarea class="form-control" id="{{ $prefix }}_observaciones"></textarea>
        </div>

        <!-- Observaciones asignado (solo en edición) -->
        @if($prefix === 'edit')
        <div class="mb-3" id="div_edit_observaciones_asignado">
          <label class="form-label">Observaciones (Asignado)</label>
          <textarea class="form-control" id="edit_observaciones_asignado"></textarea>
        </div>
        @endif
      </div>

      <div class="modal-footer">
        @if($prefix === 'edit')
          <button type="button" class="btn btn-danger" id="btnEliminarEvento">Eliminar</button>
          <button type="button" class="btn btn-secondary btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" id="btnGuardarEdicion">Guardar cambios</button>
        @else
          <button type="button" class="btn btn-secondary btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" id="btnGuardarEvento">Guardar</button>
        @endif
      </div>

    </div>
  </div>
</div>