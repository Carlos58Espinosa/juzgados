@include('calendario.calendario_methods')

<!-- Modal para Crear Evento -->
<div class="modal fade" id="modalEvento" tabindex="-1" aria-labelledby="modalEventoLabel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="modalEventoLabel">Nuevo Evento</h5>
        <button type="button" class="btn-close btn-cancelar" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <!-- Título -->
        <div class="mb-3">
          <label class="form-label">Título</label>
          <input id="titulo_evento" type="text" class="form-control">
          <input type="hidden" id="fecha_evento">
        </div>

        <!-- Expedientes -->
        <div class="mb-3">
          <label class="form-label">Expediente</label>
          <div class="input-group">
            <select id="select_expediente" class="form-select">
              <option value="">-- Selecciona expediente --</option>
              @foreach($expedientes as $exp)
                <option value="{{ $exp->id }}">{{ $exp->nombre_cliente }}</option>
              @endforeach
            </select>
            <button class="btn btn-outline-primary" type="button" id="btnAgregarExpediente">
              Agregar
            </button>
          </div>
        </div>

        <!-- Usuarios -->
        <div class="mb-3">
          <label class="form-label">Notificar a usuarios</label>
          <select id="usuarios_ids" class="form-select" multiple>
            @foreach($usuarios as $u)
              <option value="{{ $u->id }}">{{ $u->nombre }}</option>
            @endforeach
          </select>
        </div>

        <!-- Observaciones -->
        <div class="mb-3">
          <label class="form-label">Observaciones</label>
          <textarea id="observaciones" class="form-control" rows="3"></textarea>
        </div>        

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnGuardarEvento">Guardar</button>
      </div>

    </div>
  </div>
</div>