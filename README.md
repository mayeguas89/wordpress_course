# wordpress_course
Crear carpeta mu-plugins en C:\xampp\apps\wordpress\htdocs\wp-content para registrar los custom post types.
Fichero de ejemplo: my_theme_post_types.php

<?php
function my_theme_post_types() {
	// Nombre y array con propiedaes del post type
	register_post_type( 'event',  array(
		'show_in_rest' => true,
		'supports' => array('title', 'editor', 'excerpt'),
        'public' => true,
        'labels' => array(
            'name' => 'Events',
			'add_new_item' => 'Add new Event',
			'edit_item' => 'Edit Event',
			'all_items' => 'All Events',
			'singular_name' => 'Event'
        ),
        'menu_icon' => 'dashicons-calendar',
		'has_archive' => true,
		'rewrite' => array('slug' => 'events'),
		'capability_type' => 'event',
		'map_meta_cap' => true
		)
	);
		
	// Program Post Type
	register_post_type( 'program,',  array(
		'show_in_rest' => true,
		'supports' => array('title', 'editor'),
        'public' => true,
        'labels' => array(
            'name' => 'Programs',
			'add_new_item' => 'Add new Program',
			'edit_item' => 'Edit Program',
			'all_items' => 'All Programs',
			'singular_name' => 'Program'
        ),
        'menu_icon' => 'dashicons-awards',
		'has_archive' => true,
		'rewrite' => array('slug' => 'programs')
		)
	);
	// Professors Post Type
	register_post_type( 'professor,',  array(
		'show_in_rest' => true,
		'supports' => array('title', 'editor', 'thumbnail'),
        'public' => true,
        'labels' => array(
            'name' => 'Professor',
			'add_new_item' => 'Add new Professor',
			'edit_item' => 'Edit Professor',
			'all_items' => 'All Professors',
			'singular_name' => 'Professor'
        ),
        'menu_icon' => 'dashicons-welcome-learn-more'
		)
	);
	
	// Nombre y array con propiedaes del post type
	register_post_type( 'campus',  array(
		'show_in_rest' => true,
		'capability_type' => 'campus',
		'map_meta_cap' => true,
		'supports' => array('title', 'editor', 'excerpt'),
        'public' => true,
        'labels' => array(
            'name' => 'Campuses',
			'add_new_item' => 'Add new Campus',
			'edit_item' => 'Edit Campus',
			'all_items' => 'All Campuses',
			'singular_name' => 'Campus'
        ),
        'menu_icon' => 'dashicons-location-alt',
		'has_archive' => true,
		'rewrite' => array('slug' => 'campuses')
		)
	);
	
	// Nombre y array con propiedaes del post type
	register_post_type( 'note',  array(
		'show_in_rest' => true,
		'supports' => array('title', 'editor'),
		// Public false para que sean privadas en cada user
        'public' => false,
		// La propiedad de arriba no muestra para el admin, si ponemos show_ui a true la muestra
		'show_ui' => true,
        'labels' => array(
            'name' => 'Notes',
			'add_new_item' => 'Add new Note',
			'edit_item' => 'Edit Note',
			'all_items' => 'All Notes',
			'singular_name' => 'Notes'
        ),
        'menu_icon' => 'dashicons-welcome-write-blog',
		)
	);
}
add_action('init', 'my_theme_post_types');
?>
