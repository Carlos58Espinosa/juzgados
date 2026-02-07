<!-- Modal para editar evento -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarLabel">Editar evento</h5>
        <button type="button" class="btn-close btn-cancelar" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit_evento_id">
        <div class="mb-3">
          <label for="edit_titulo_evento" class="form-label">Título</label>
          <input type="text" class="form-control" id="edit_titulo_evento">
        </div>
        <div class="mb-3">
          <label for="edit_fecha_evento" class="form-label">Fecha</label>
          <input type="date" class="form-control" id="edit_fecha_evento">
        </div>         
        <div class="mb-3">
          <label for="edit_select_estatus" class="form-label">Estatus</label>
          <select id="edit_select_estatus" class="form-select">
            <option value="alta">Alta</option>
            <option value="completado">Terminado</option>
          </select>
        </div>
        <!-- Expedientes -->
        <div class="mb-3">
          <label class="form-label">Expediente</label>
          <div class="input-group">
            <select id="select_expediente" class="form-select">
              <option value="">-- Selecciona expediente --</option>
              @foreach($expedientes as $exp)
                <option value="{{ $exp->id }}">{{ $exp->etapa_plantilla->nombre }}</option>
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
          <select id="edit_usuarios_ids" class="form-select" multiple>
            @foreach($usuarios as $u)
              <option value="{{ $u->id }}">{{ $u->nombre }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label for="edit_observaciones" class="form-label">Observaciones</label>
          <textarea class="form-control" id="edit_observaciones"></textarea>
        </div>

        <div class="mb-3" id="div_edit_observaciones_asignado">
          <label for="edit_observaciones" class="form-label">Observaciones (Asignado)</label>
          <textarea class="form-control" id="edit_observaciones_asignado"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnGuardarEdicion">Guardar cambios</button>
      </div>
    </div>
  </div>
</div>