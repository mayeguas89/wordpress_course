<?php

  function themeRegisterSearch() {
    // Primer argumento namespace, segundo ruta, tercero un array con method GET(WP_REST_Server::READABLE) y funcion de salida de la visita a la ruta ns_my_theme/v1/search
    register_rest_route(
      'ns_my_theme/v1',
      'search', 
      array(
        'method' => WP_REST_Server::READABLE,
        'callback' => 'myThemeSearchResults'
      )
    );
  }

  function myThemeSearchResults($data) {
    $mainQuery = new WP_Query(
      array(
        'post_type' => array('professor', 'page', 'post', 'program', 'campus', 'event'),
        's' => sanitize_text_field($data['term']))
    );

    $mainQueryResults = array(
      'generalInfo' => array(),
      'professors' => array(),
      'programs' => array(),
      'campuses' => array(),
      'events' => array(),
    );

    while($mainQuery->have_posts())
    {
      $mainQuery->the_post();
      if(get_post_type() == 'professor') {
        array_push(
          $mainQueryResults['professors'], 
          array(
            'title' => get_the_title(),
            'link' => get_the_permalink(),
            'thumbnail' => get_the_post_thumbnail_url('professorLandscape')
          )
        );
      }
      else if(get_post_type() == 'event') {
        $eventDate = new DateTime(get_field('event_date'));
        array_push(
          $mainQueryResults['events'], 
          array(
            'title' => get_the_title(),
            'link' => get_the_permalink(),
            'date' => array(
              'year' => $eventDate->format('Y'),
              'month' => $eventDate->format('M'),
              'day' => $eventDate->format('d')
            )
          )
        );
      }
      else if(get_post_type() == 'campus') {
        array_push(
          $mainQueryResults['campuses'], 
          array(
            'title' => get_the_title(),
            'link' => get_the_permalink(),
          )
        );
      }
      else if(get_post_type() == 'program') {
        array_push(
          $mainQueryResults['programs'], 
          array(
            'title' => get_the_title(),
            'link' => get_the_permalink()
          )
        );
      }
      else {
        array_push(
          $mainQueryResults['generalInfo'], 
          array(
            'title' => get_the_title(),
            'link' => get_the_permalink(),
            'authorName' => get_the_author(),
            'postType' => get_post_type()
          )
        );
      }
      


      // $relatedPrograms = get_field('related_program');
      // $programs = array();
      // if($relatedPrograms) {
      //   foreach($relatedPrograms as $program) {
      //     array_push($programs, array(
      //       'name' => get_the_title($program),
      //       'link' => get_the_permalink($program)
      //     ));
      //   }
      // }
      // array_push($professorsResult, array(
      //   'name' => get_the_title(),
      //   'link' => get_the_permalink(),
      //   'programs' => $programs
      // ));
    }
    
    return $mainQueryResults;
  }
  add_action('rest_api_init', 'themeRegisterSearch');

