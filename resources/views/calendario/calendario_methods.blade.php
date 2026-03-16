<script>

// ✅ Declaración única de variables globales
let calendar;
let modalEvento;
let modalEditar;
let eventoSeleccionado;
let choicesNuevo;
let choicesEditar;

$(document).ready(function() {
    initChoices();
    initSelectIfExists('create_select_expediente', '-- Selecciona expediente --');
    initSelectIfExists('edit_select_expediente', '-- Selecciona expediente --');

    // Delegación para ambos botones de expediente
    $(document).on('click', '[id^=btnAgregarExpediente]', function() {
        const prefix = this.id.includes('Create') ? 'create' : 'edit';
        let texto = $(`#${prefix}_select_expediente option:selected`).text();
        let id = $(`#${prefix}_select_expediente`).val();
        if (!id) return;

        let obs = $(`#${prefix}_observaciones`).val();
        let nuevoTexto = obs
            ? obs + '\nExpediente: ' + texto
            : 'Expediente: ' + texto;

        $(`#${prefix}_observaciones`).val(nuevoTexto);
    });

    // Guardar evento nuevo
    $('#btnGuardarEvento').off('click').on('click', function() {
        let data = {
            fecha: $('#fecha_evento').val(),
            titulo: $('#create_titulo_evento').val(),
            observaciones: $('#create_observaciones').val(),
            usuarios: $('#create_usuarios_ids').val(),
            expediente_id: $('#create_select_expediente').val(),
            estatus: 'alta'
        };

        fetch("{{ route('calendario.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(r => {
            if (r.ok) {
                calendar.addEvent(r.evento);
                modalEvento.hide();
                limpiarControles();
            }
        })
        .catch(err => console.error("Error de fetch:", err));
    });

    // Guardar edición
    $('#btnGuardarEdicion').off('click').on('click', function() {
        let id = $('#edit_evento_id').val();
        let data = {
            titulo: $('#edit_titulo_evento').val(),
            fecha: $('#edit_fecha_evento').val(),
            observaciones: $('#edit_observaciones').val(),
            observaciones_asignado: $('#edit_observaciones_asignado').val(),
            usuarios: $('#edit_usuarios_ids').val(),
            expediente_id: $('#edit_select_expediente').val(),
            estatus: $('#edit_select_estatus').val()
        };

        fetch(`{{ url('calendario') }}/${id}`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify(data)
        })        
        .then(r => {
            if (r.ok) {
                let evento = calendar.getEventById(id);
                evento.setProp('title', data.titulo);
                evento.setDates(data.fecha, null, { allDay: true });
                evento.setExtendedProp('observaciones', data.observaciones);
                evento.setExtendedProp('expediente_id', data.expediente_id);
                evento.setExtendedProp('usuarios', data.usuarios);
                evento.setExtendedProp('estatus', data.estatus);

                let color = data.estatus === 'alta' ? '#4e73df' : '#1cc88a';
                evento.setProp('backgroundColor', color);
                evento.setProp('borderColor', color);

                modalEditar.hide();
            }
        })
        .catch(err => console.error("Error de fetch:", err));
    });

    $('#btnEliminarEvento').on('click', function(){
        let id = $('#edit_evento_id').val();

        fetch(`{{ url('calendario') }}/${id}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        })
        .then(r => r.json())
        .then(r => {
            if(r.ok){
                let evento = calendar.getEventById(id);
                evento.remove();
                modalEditar.hide();
            }
        })
        .catch(err => console.error(err));
    });

    $('.btn-cancelar').on('click', function () {
        this.blur();
    });
});

function initChoices() {
    if (!choicesNuevo) {
        choicesNuevo = new Choices('#create_usuarios_ids', {
            removeItemButton: true,
            shouldSort: false,
            placeholderValue: 'Selecciona usuarios'
        });
    }

    if (!choicesEditar) {
        choicesEditar = new Choices('#edit_usuarios_ids', {
            removeItemButton: true,
            shouldSort: false,
            placeholderValue: 'Selecciona usuarios'
        });
    }
}

function initSelectIfExists(id, placeholder){
    const el = document.getElementById(id);
    if (el && !el.tomselect) {
        new TomSelect(el, {
            maxItems: 1,
            placeholder: placeholder,
            create: false,
            allowEmptyOption: true,
            items: []
        });
    }
} 

function limpiarControles() {
    $('#fecha_evento').val('');
    $('#create_titulo_evento').val('');
    $('#create_observaciones').val('');
    $('#create_select_expediente').val('').trigger('change');
    if (choicesNuevo) 
        choicesNuevo.removeActiveItems();
}


    document.addEventListener("DOMContentLoaded", function () {
    modalEvento = new bootstrap.Modal(document.getElementById('modalCreate'));
    modalEditar = new bootstrap.Modal(document.getElementById('modalEdit'));

    // ✅ Solo asignación, sin "let"
    calendar = new FullCalendar.Calendar(document.getElementById("calendar"), {
        initialView: "dayGridMonth",
        selectable: true,
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: ""
        },
        dateClick: function(info) {
            limpiarControles();
            $('#fecha_evento').val(info.dateStr); 
            modalEvento.show();
        },
        eventClick: function(info) {
            eventoSeleccionado = info.event;
            const USER_ID = {{ $user_id }};
            let band_creador = false;
            let band_asignado = false;
            choicesEditar.enable();
            $('#div_edit_observaciones_asignado').hide();

            if(USER_ID != eventoSeleccionado.extendedProps.usuarioId){
                if (!eventoSeleccionado.extendedProps.usuarios.includes(USER_ID)) {
                    band_creador = true;
                    band_asignado = true;  
                    choicesEditar.disable();              
                } else {  
                    if(USER_ID != eventoSeleccionado.extendedProps.usuarioId){                    
                        band_creador = true;
                        choicesEditar.disable();
                        $('#div_edit_observaciones_asignado').show();
                    }
                }
            }

            $('#btnGuardarEdicion').prop('disabled', band_asignado);
            $('#edit_evento_id').val(eventoSeleccionado.id);
            $('#edit_titulo_evento').val(eventoSeleccionado.title).prop('disabled', band_creador);
            $('#edit_fecha_evento').val(eventoSeleccionado.startStr).prop('disabled', band_creador);
            $('#edit_observaciones').val(eventoSeleccionado.extendedProps.observaciones || '').prop('disabled', band_creador);
            $('#edit_select_expediente').val(eventoSeleccionado.extendedProps.expediente_id || '').trigger('change');
            $('#edit_select_estatus').val(eventoSeleccionado.extendedProps.estatus).prop('disabled', band_asignado);
            $('#btnEliminarEvento').toggle(!band_creador);

            let usuarios = (eventoSeleccionado.extendedProps.usuarios || []).map(String);
            choicesEditar.removeActiveItems();
            usuarios.forEach(id => { choicesEditar.setChoiceByValue(id); });          

            modalEditar.show(); 
        },
        events: @json($events)
    });

    calendar.render();
    
});
</script>