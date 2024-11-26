<?php 
function styles() {
    wp_enqueue_style('estilos-principales', get_template_directory_uri() . '/css/style.css');
}
add_action('wp_enqueue_scripts', 'styles');
?>
