<?php 
function styles() {
    wp_enqueue_style('estilos-principales', get_template_directory_uri() . '/dist/css/app.css');
}
add_action('wp_enqueue_scripts', 'styles');
?>
<?php if ($success): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Estado de la propiedad actualizado con éxito!',
            showConfirmButton: false,
            timer: 1500
        });
    </script>
<?php endif; ?>