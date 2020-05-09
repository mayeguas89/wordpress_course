<?php
    // Añadimos fichero con funciones para rutas en busqueda
    require get_theme_file_path('inc/search-route.php');

    // Funcion para cargar el estilo y los scripts
    function my_theme_files() {

        // Scripts
        wp_enqueue_script(
            'my_theme_main_scripts',
            get_theme_file_uri("/js/scripts-bundled.js"),
            NULL,
            microtime(),
            true
        );
        
        // Nos permite usar variables en el fichero pasado como primer argumento. Para acceder al root_url en javascript
        wp_localize_script( 'my_theme_main_scripts', 'themeData', array(
            'root_url' => get_site_url(),
            'nonce' => wp_create_nonce('wp_rest')
        ));
        // si tuviesemos que cargar jquery
        // wp_enqueue_script(
        //     'my_theme_main_scripts',
        //     get_theme_file_uri("/js/scripts-bundled.js"),
        //     array('jquery'),
        //     1.0,
        //     true
        // );

        wp_enqueue_style( 'custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,300i,400,,400i,700,700i');

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
    function my_theme_features() {
        // Añade el titulo de las paginas-post al title 
        // de la pagina cuando llama wp_head()
        add_theme_support('title-tag');
        // Añade las miniaturas a los posts
        add_theme_support('post-thumbnails');
        // Añadimos tamaños de imagen para recortes de imagenes customizados
        add_image_size('professorLandscape', 400, 260, true);
        add_image_size('professorPortrait', 480, 650, true);
        add_image_size('pageBanner', 500, 350, true);


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

    // Funcion para que los WP_Query se hagan de forma personalizada
    function my_theme_adjust_querys($query) {

        if(!is_admin() AND is_post_type_archive( 'event' ) AND $query->is_main_query())
        {
            $query->set('meta_key', 'event_date');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'ASC');
            $query->set('meta_query', array(
                array(
                    'key' => 'event_date',
                    'compare' => '>=',
                    'value' => date('Ymd'),
                    'type' => 'numeric'
                )
            ));
        }   

        if(!is_admin() AND is_post_type_archive( 'program' ) AND $query->is_main_query())
        {
            $query->set('orderby', 'title');
            $query->set('order', 'ASC');
            $query->set('post_per_page', -1);
        }   

    }

    add_action('pre_get_posts', 'my_theme_adjust_querys');

    // function mapKey($api) {
    //     $api['key'] = '';
    // }
    // add_filter( 'acf/fields/google_map/api','mapKey');

    // Funcion para customizar el baner de cada pagina/post en particular
    function pageBanner($args=NULL) {
        if(!$args['title']) {
            $args['title'] = get_the_title();
        }

        if(!$args['subtitle']) {
            $args['subtitle'] = get_field('page_banner_subtitle');
        }

        if(!$args['photo']) {
            if(get_field('page_banner_background_image')) {
                $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
            } else {
                $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
            }
        }
        ?>
        <div class="page-banner">
            <div 
                class="page-banner__bg-image" 
                style="background-image: url(<?php echo $args['photo']; ?>);">
            </div>
            <div class="page-banner__content container container--narrow">
                <h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
                <div class="page-banner__intro">
                <p><?php echo $args['subtitle'];?></p>
                </div>
            </div>  
        </div>
        <?php
    }

    // Creamos una funcion para customizar las peticiones a traves de wp-json y añadir propiedades personalizadas
    function theme_custom_rest() {
        // La funcion toma como argumento el tipo de post en el que vamos a crear un campo para que devuelva en el request, el nombre del campo y un array que contiene como clave 'get_callback' y valor la funcion que devuelve el resultado que queremos incluir en la peticion (en este caso creamos una inline)
        register_rest_field(
            'post',
            'authorName',
            array(
                'get_callback' => function() {
                    return get_the_author();
                }
            )
        );
    }
    add_action('rest_api_init', 'theme_custom_rest');

    // Rederict subscriber account out of dashboard and onto homepage
    add_action( 'admin_init', 'redirectSubsToFrontEnd');
    function redirectSubsToFrontEnd() {
        $ourCurrentUser = wp_get_current_user();
        if(count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
            wp_redirect(site_url('/'));
            exit;
        }
    }
    // Rederict subscriber account out of dashboard and onto homepage
    add_action( 'wp_loaded', 'noSubsAdminBar');
    function noSubsAdminBar() {
        $ourCurrentUser = wp_get_current_user();
        if(count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
            show_admin_bar( false );
        }
    }

    // Customize login screen para que el #login h1 a dirija a la url del site en lugar de a wordpress.org
    add_filter( 'login_headerurl', 'ourHeaderUrl');
    function ourHeaderUrl() {
        return esc_url(site_url('/'));
    }

    // Funcion para cargar el style.css en la pagina de login
    add_action( 'login_enqueue_scripts', 'ourLoginCss');
    function ourLoginCss() {
        // Font awesome
        wp_enqueue_style( 'custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,300i,400,,400i,700,700i');

        // style.css
        wp_enqueue_style(
            'my_theme_main_styles',
            get_stylesheet_uri(),
            NULL,
            microtime()
        );
    }

    // Funcion para cambiar el nombre del header title del login
    function ourLoginTitle() {
        return get_bloginfo('name');
    }
    add_filter( 'login_headertitle', 'ourLoginTitle' );
        ?>