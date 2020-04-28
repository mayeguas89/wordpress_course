<?php

    // Funcion para cargar el estilo
    function my_theme_files() {

        // Scripts
        wp_enqueue_script(
            'my_theme_main_scripts',
            get_theme_file_uri("/js/scripts-bundled.js"),
            NULL,
            microtime(),
            true
        );

        // Font awesome
        wp_enqueue_style(
            'font-awesome',
            'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'
        );

        // style.css
        wp_enqueue_style(
            'my_theme_main_styles',
            get_stylesheet_uri(),
            NULL,
            microtime()
        );
    }

    // Funcion para añadir las características al tema
    // Tras la carga previa
    function my_theme_features()
    {
        // Añade el titulo de las paginas-post al title 
        // de la pagina cuando llama wp_head()
        add_theme_support('title-tag');

        // Añade un menu al dashboard con el objetivo de añadir
        // nuevos menus en la barra del header dinamicamente desde
        // wordpress sin hardcoded
        register_nav_menu( 'headerMenuLocation', 'Header Menu Location' );
        register_nav_menu( 'footerMenuLocation', 'Footer Menu Location' );
    }

    // add_action toma como parametro el nombre de la funcion en un momento concreto: wp_enqueue_scripts
    add_action('wp_enqueue_scripts', 'my_theme_files');

    // add action to have place after setup theme
    add_action('after_setup_theme', 'my_theme_features');

    function my_theme_adjust_querys($query) {

        if(!is_admin() AND is_post_type_archive( 'event' ) AND $query->is_main_query())
        {
            $query->set('meta_key', 'event_date');
            $query->set('meta_query', array(
                    'key' => 'event_date',
                    'compare' => '>=',
                    'value' => date('Ymd'),
                    'type' => 'numeric'
            ));
        }   

    }

    add_action('pre_get_posts', 'my_theme_adjust_querys');