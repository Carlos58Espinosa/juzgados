<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const sel   = document.getElementById('plantillas');
    const lista = document.getElementById('lista');
    const hid   = document.getElementById('old_ids');

    if (!sel || !lista || !hid) 
        return; // ahora sí es válido, porque estamos dentro de una función

    const map = new Map();
    @foreach($plantillas as $p)
        map.set(String({{ $p->id }}), @json($p->nombre));
    @endforeach

    let orden = (hid.value || '').split(',').filter(Boolean);
    if (orden.length === 0) {
        orden = Array.from(sel.selectedOptions).map(o => o.value);
    }
    render();

    sel.addEventListener('mousedown', function (e) {
        if (e.target.tagName === 'OPTION') {
            e.preventDefault();
            const opt = e.target;
            opt.selected = !opt.selected;

            const id = opt.value;
            if (opt.selected) {
                if (!orden.includes(id)) orden.push(id);
            } else {
                orden = orden.filter(x => x !== id);
            }
            render();
        }
    });

    sel.addEventListener('change', () => {
        const seleccionados = new Set(Array.from(sel.selectedOptions).map(o => o.value));
        seleccionados.forEach(id => { if (!orden.includes(id)) orden.push(id); });
        orden = orden.filter(id => seleccionados.has(id));
        render();
    });

    new Sortable(lista, {
        animation: 150,
        onUpdate: () => {
            orden = Array.from(lista.children).map(li => li.id);
            hid.value = orden.join(',');
        }
    });

    function render() {
        lista.innerHTML = '';
        orden.forEach(id => {
            const li = document.createElement('li');
            li.id = id;
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.innerHTML = `
                <span><i title="Ordenar" class="fas fa-arrows-alt-v flecha_tipo_procedimiento"></i>${map.get(id) || id}</span>
                <button class="delete-alert btn btn-sm btn-danger"><i class="far fa-trash-alt"></i></button>
            `;
            li.querySelector('button').onclick = () => {
                orden = orden.filter(x => x !== id);
                Array.from(sel.options).forEach(o => { if (o.value === id) o.selected = false; });
                render();
            };
            lista.appendChild(li);
        });
        hid.value = orden.join(',');
    }
});
</script>