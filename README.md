# wordpress_course
Crear carpeta mu-plugins en C:\xampp\apps\wordpress\htdocs\wp-content para registrar los custom post types.
Fichero de ejemplo: my_theme_post_types.php

// Register post types
function my_theme_post_types() {
// Nombre y array con propiedaes del post type
	register_post_type( 'event',  array(
		'supports' => array('title', 'editor', 'excerpt'),
            	'public' => true,
            	'labels' => array(
                	'name' => 'Events'
            	),
            	'menu_icon' => 'dashicons-format-gallery',
		'has_archive' => true,
		'rewrite' => array('slug' => 'events')
        ));
}
add_action('init', 'my_theme_post_types');
