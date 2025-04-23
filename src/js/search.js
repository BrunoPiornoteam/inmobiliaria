document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const searchResults = document.getElementById('search-results');
    let searchTimeout;

    function performSearch(termino) {
        if (termino.length < 2) {
            searchResults.innerHTML = '';
            searchResults.classList.remove('active');
            return;
        }

        fetch(`/inmobiliaria/search/search.php?q=${encodeURIComponent(termino)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.message || 'Error en la búsqueda');
                }

                let html = '';
                if (data.length === 0) {
                    html = '<div class="search-result-item">No se encontraron resultados</div>';
                } else {
                    data.forEach(item => {
                        let titulo = '';
                        let link = '';
                        
                        switch(item.tipo) {
                            case 'propiedad':
                                titulo = `🏠 ${item.titulo}`;
                                link = `/inmobiliaria/propiedades/single_propiedad.php?id=${item.id}`;
                                break;
                            case 'cliente':
                                titulo = `👤 ${item.nombre} ${item.apellido}`;
                                link = `/inmobiliaria/clientes/editar_cliente.php?id=${item.id}`;
                                break;
                            case 'contrato':
                                titulo = `📄 Contrato #${item.numero_contrato}`;
                                link = `/inmobiliaria/contratos/ver_contrato.php?id=${item.id}`;
                                break;
                        }

                        html += `
                            <a href="${link}" class="search-result-item">
                                <div class="result-title">${titulo}</div>
                                <div class="result-type">${item.tipo.charAt(0).toUpperCase() + item.tipo.slice(1)}</div>
                            </a>
                        `;
                    });
                }
                searchResults.innerHTML = html;
                searchResults.classList.add('active');
            })
            .catch(error => {
                console.error('Error:', error);
                searchResults.innerHTML = `<div class="search-result-item">Error: ${error.message}</div>`;
                searchResults.classList.add('active');
            });
    }

    // Manejar la búsqueda en tiempo real mientras se escribe
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const termino = e.target.value;
        searchTimeout = setTimeout(() => {
            performSearch(termino);
        }, 300);
    });

    // Manejar el envío del formulario (cuando se hace clic en el botón o se presiona Enter)
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        clearTimeout(searchTimeout);
        performSearch(searchInput.value);
    });

    // Cerrar resultados al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target) && !searchButton.contains(e.target)) {
            searchResults.classList.remove('active');
        }
    });
});
