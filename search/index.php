<?php
session_start();
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3>Búsqueda Global</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar en todo el sistema...">
                    </div>
                    <div id="resultados" class="mt-4">
                        <!-- Aquí se mostrarán los resultados -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('input', function(e) {
    const termino = e.target.value;
    if (termino.length < 2) {
        document.getElementById('resultados').innerHTML = '';
        return;
    }

    fetch(`search.php?q=${encodeURIComponent(termino)}`)
        .then(response => response.json())
        .then(data => {
            let html = '';
            if (data.length === 0) {
                html = '<div class="alert alert-info">No se encontraron resultados</div>';
            } else {
                html = '<div class="list-group">';
                data.forEach(item => {
                    let titulo = '';
                    let link = '';
                    
                    switch(item.tipo) {
                        case 'propiedad':
                            titulo = `🏠 ${item.titulo}`;
                            link = `../propiedades/ver.php?id=${item.id}`;
                            break;
                        case 'inquilino':
                            titulo = `👤 ${item.nombre} ${item.apellido}`;
                            link = `../inquilinos/ver.php?id=${item.id}`;
                            break;
                        case 'pago':
                            titulo = `💰 Pago #${item.id} - ${item.fecha_pago}`;
                            link = `../pagos/ver_factura.php?id=${item.id}`;
                            break;
                    }

                    html += `
                        <a href="${link}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">${titulo}</h5>
                            </div>
                        </a>
                    `;
                });
                html += '</div>';
            }
            document.getElementById('resultados').innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('resultados').innerHTML = 
                '<div class="alert alert-danger">Error al buscar</div>';
        });
});
</script>

<?php require_once '../includes/footer.php'; ?>
