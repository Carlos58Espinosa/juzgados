<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('plantillas');
    const lista = document.getElementById('lista');
    const hid = document.getElementById('old_ids');

    if (!sel || !lista || !hid) return;

    // Inicializar Tom Select
    const ts = new TomSelect(sel, {
        plugins: ['remove_button'],
        maxItems: null,
        placeholder: "Selecciona plantillas...",
        create: false
    });

    // Mapa de ID -> nombre
    const map = new Map([
        @foreach($plantillas as $p)
            ['{{ $p->id }}', @json($p->nombre)],
        @endforeach
    ]);

    // Estado del orden
    let orden = (hid.value || '').split(',').filter(Boolean);
    if (orden.length === 0) orden = ts.getValue();

    // Renderizar la lista
    const render = () => {
        lista.innerHTML = '';
        orden.forEach(id => {
            const li = document.createElement('li');
            li.id = id;
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.innerHTML = `
                <span><i title="Ordenar" class="fas fa-arrows-alt-v me-2"></i>${map.get(id) || id}</span>
                <button class="delete-alert btn btn-sm btn-danger"><i class="far fa-trash-alt"></i></button>
            `;
            li.querySelector('button').onclick = () => {
                orden = orden.filter(x => x !== id);
                ts.removeItem(id);  // elimina del Tom Select
                render();
            };
            lista.appendChild(li);
        });
        hid.value = orden.join(',');
    };

    // Cuando cambian los valores en Tom Select
    ts.on('change', () => {
        const seleccionados = ts.getValue();
        // Mantener orden previo, agregar nuevos
        orden = [...orden.filter(id => seleccionados.includes(id)), 
                 ...seleccionados.filter(id => !orden.includes(id))];
        render();
    });

    // Ordenable con SortableJS
    new Sortable(lista, {
        animation: 150,
        onUpdate: () => {
            orden = Array.from(lista.children).map(li => li.id);
            hid.value = orden.join(',');
        }
    });

    // Primer render
    render();
});
</script>
