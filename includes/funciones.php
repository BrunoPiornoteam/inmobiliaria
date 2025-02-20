<?php 
function styles() {
    wp_enqueue_style('estilos-principales', get_template_directory_uri() . '/dist/css/app.css');
}
add_action('wp_enqueue_scripts', 'styles');
?>
