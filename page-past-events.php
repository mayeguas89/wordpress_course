<?php 
get_header(); 
pageBanner(array(
  'title' => 'Past Events',
  'subtitle' => 'A recap of our past events'
));
?>

<div class="container container--narrow page-section">
    <?php 

    $pastEventsQuery = new WP_Query(array(
                'post_type' => 'event',
                'meta_key'=> 'event_date',
                'orderby' => 'meta_value_num',
                'order' => 'ASC',
                'meta_query' => array(
                  array(
                    'key' => 'event_date',
                    'compare' => '<',
                    'value' => date('Ymd'),
                    'type' => 'numeric'
                  )
            )));
    while($pastEventsQuery->have_posts()) {
        $pastEventsQuery->the_post(); 
        get_template_part('template-parts/content', 'event');
    }
    echo paginate_links(array(
        'total' => $pastEventsQuery->max_num_pages
    ));
    ?>
</div>

<?php
get_footer();
?>