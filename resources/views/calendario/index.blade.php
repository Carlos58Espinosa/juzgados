@extends('layout')

@section('content')

<!-- Leyenda de colores -->
<div class="container mt-3 mb-2">
    <div class="d-flex gap-3 align-items-center">
        <div class="d-flex align-items-center gap-1">
            <div style="width:20px; height:20px; background-color:#4e73df; border-radius:3px;"></div>
            <span>Pendiente</span>
        </div>
        <div class="d-flex align-items-center gap-1">
            <div style="width:20px; height:20px; background-color:#1cc88a; border-radius:3px;"></div>
            <span>Terminado</span>
        </div>
    </div>
</div>

<!-- Modal para Crear Evento -->
<div class="modal fade" id="modalEvento" tabindex="-1" aria-labelledby="modalEventoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEventoLabel">Nuevo Evento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input id="titulo_evento" type="text" class="form-control" placeholder="Título">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnGuardarEvento">Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para cambiar estatus -->
<div class="modal fade" id="modalEstatus" tabindex="-1" aria-labelledby="modalEstatusLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEstatusLabel">Cambiar estatus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <select id="select_estatus" class="form-select">
            <option value="pendiente">Pendiente</option>
            <option value="terminado">Terminado</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnGuardarEstatus">Guardar</button>
      </div>
    </div>
  </div>
</div>

<div id="calendar" style="max-width:900px; margin:auto;"></div>

<style>
/* Forzar que el modal quede arriba por si acaso */
.modal {
  z-index: 20000 !important;
}
.modal-backdrop {
  z-index: 19999 !important;
}
.modal.show {
  display: block !important;
  opacity: 1 !important;
  transform: none !important;
}
</style>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

<script>

document.addEventListener("DOMContentLoaded", function () {

    let fechaSeleccionada = null;
    let eventoSeleccionado = null;

    let modalEvento = new bootstrap.Modal(document.getElementById('modalEvento'));
    let modalEstatus = new bootstrap.Modal(document.getElementById('modalEstatus'));

    // Inicializar calendario
    let calendar = new FullCalendar.Calendar(document.getElementById("calendar"), {
        initialView: "dayGridMonth",
        selectable: true,

        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: ""
        },

        dateClick: function(info) {
            fechaSeleccionada = info.dateStr;
            document.getElementById("titulo_evento").value = ""; 
            modalEvento.show();
        },

        eventClick: function(info) {
            eventoSeleccionado = info.event;
            document.getElementById("select_estatus").value = 
                eventoSeleccionado.backgroundColor === '#4e73df' ? 'pendiente' : 'terminado';
            modalEstatus.show();           
        },

        events: @json($events)
    });

    calendar.render();

    // Guardar Evento
    document.getElementById("btnGuardarEvento").onclick = function () {

        let titulo = document.getElementById("titulo_evento").value;

        fetch("{{ route('calendario.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                titulo: titulo,
                fecha: fechaSeleccionada
            })
        })
        .then(r => r.json())
        .then(r => {
            if (r.ok) {
                // Agregar evento directamente al calendario
                calendar.addEvent(r.evento);
                modalEvento.hide();
            } else {
                console.error("Error en la respuesta:", r);
            }
        })
        .catch(err => console.error("Error de fetch:", err));
    };


    // Guardar cambio de estatus
    document.getElementById("btnGuardarEstatus").onclick = function () {
        let nuevoEstatus = document.getElementById("select_estatus").value;

        fetch(`{{ url('calendario') }}/${eventoSeleccionado.id}`, {
            method: "PUT", // usa PUT para update
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ estatus: nuevoEstatus })
        })
        .then(r => r.json())
        .then(r => {
            if(r.ok){
                let color = nuevoEstatus === 'pendiente' ? '#4e73df' : '#1cc88a';
                eventoSeleccionado.setProp('backgroundColor', color);
                eventoSeleccionado.setProp('borderColor', color);
                modalEstatus.hide();
            } else {
                alert("Error al actualizar el estatus");
            }
        })
        .catch(err => console.error("Error de fetch:", err));
    };

});
</script>

@stop
