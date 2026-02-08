<script>
let calendar;       
let modalEvento; 
let modalEditar;   
let eventoSeleccionado;
let choicesNuevo;
let choicesEditar;

$(document).ready(function() {
    function initChoices() {
        if (!choicesNuevo) {
            choicesNuevo = new Choices('#usuarios_ids', {
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

    initChoices();

    $('#btnAgregarExpediente').on('click', function() {
        let texto = $('#select_expediente option:selected').text();
        let id = $('#select_expediente').val();
        if (!id) return;

        let obs = $('#observaciones').val();
        let nuevoTexto = obs
            ? obs + '\nExpediente: ' + texto
            : 'Expediente: ' + texto;

        $('#observaciones').val(nuevoTexto);
    });

    // Guardar evento nuevo
    $('#btnGuardarEvento').off('click').on('click', function() {
        let data = {
            fecha: $('#fecha_evento').val(),
            titulo: $('#titulo_evento').val(),
            observaciones: $('#observaciones').val(),
            usuarios: $('#usuarios_ids').val(),
            estatus: $('#select_estatus').val()
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
            estatus: $('#edit_select_estatus').val()
        };
        //console.log(data);

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
                evento.setStart(data.fecha);
                evento.setExtendedProp('observaciones', data.observaciones);
                evento.setExtendedProp('expediente_id', data.expediente_id);
                evento.setExtendedProp('usuarios', data.usuarios);

                let color = data.estatus === 'alta' ? '#4e73df' : '#1cc88a';
                evento.setProp('backgroundColor', color);
                evento.setProp('borderColor', color);

                modalEditar.hide();
            }
        })
        .catch(err => console.error("Error de fetch:", err));
    });

    $('.btn-cancelar').on('click', function () {
        this.blur(); // quita foco al botón
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
});

function limpiarControles() {
    $('#fecha_evento').val('');
    $('#titulo_evento').val('');
    $('#observaciones').val('');
    $('#select_expediente').val('').trigger('change');
    if (choicesNuevo) 
        choicesNuevo.removeActiveItems();
}

document.addEventListener("DOMContentLoaded", function () {
    modalEvento = new bootstrap.Modal(document.getElementById('modalEvento'));
    modalEditar = new bootstrap.Modal(document.getElementById('modalEditar')); // único modal de edición

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

            //console.log("Usuario en Sesion = ", USER_ID, " Usuarios Evento = ", eventoSeleccionado.extendedProps.usuarioId);

            if(USER_ID != eventoSeleccionado.extendedProps.usuarioId){
                // El líder no esta asignado y no lo creo
                if (!eventoSeleccionado.extendedProps.usuarios.includes(USER_ID)) {
                    band_creador = true;
                    band_asignado = true;  
                    choicesEditar.disable();              
                } else {  //El ayudante esta asignado
                    if(USER_ID != eventoSeleccionado.extendedProps.usuarioId){// es asignacion                    
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