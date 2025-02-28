document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("status-form");

    form.addEventListener("submit", function(event) {
        event.preventDefault(); 

        Swal.fire({
            title: '¡Éxito!',
            text: 'El estado de la propiedad ha sido actualizado correctamente.',
            icon: 'success',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); 
            }
        });
    });
});