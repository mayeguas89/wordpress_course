<?php
    get_header();
	while(have_posts())
	{
    the_post();
    pageBanner();
  ?>

		
	<div class="container container--narrow page-section">
    <div class="metabox metabox--position-up metabox--with-home-link">
      <p><a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link( 'program' ) ?>"><i class="fa fa-home" aria-hidden="true"></i>All Programs</a><span class="metabox__main"><?php the_title(); ?></span></p>
    </div>
		<div class="generic-content">
			<?php the_content(); ?>
      <?php 
        // Query para encontrar los profesores
        // que tienen una relacion con el programa actual
        $proffesorsQuery = new WP_Query(array(
          'posts_per_page' => -1,
          'post_type' => 'professor',
          'orderby' => 'title',
          'order' => 'ASC',
          'meta_query' => array(
            array(
              'key' => 'related_program',
              'compare' => 'LIKE',
              'value' => '"'.get_the_ID().'"'
            )
          )
        ));
        if($proffesorsQuery->have_posts()) :
          echo '<hr class="section-break">';
          echo '<h3 class="headline headline--medium">'. get_the_title() . ' Professors</h3>';
          echo '<ul class="professor-cards">';
          while($proffesorsQuery->have_posts()) :
            $proffesorsQuery->the_post();
      ?>
      <li class="professor-card__list-item">
        <a 
          class="professor-card" 
          href="<?php the_permalink(); ?>">
            <img src="<?php the_post_thumbnail_url('professorLandscape'); ?>" alt="" class="professor-card__image">
            <span class="professor-card__name"><?php the_title(); ?></span>
        </a>
      </li>
      <?php
          endwhile;
          echo '</ul>';
        endif;
        wp_reset_postdata();
      ?>
      <?php
      // Query para encontrar los campuses
      // que tienen una relacion con el programa actual
      $campusesQuery = new WP_Query(array(
        'posts_per_page' => -1,
        'post_type' => 'campus',
        'orderby' => 'title',
        'order' => 'ASC',
        'meta_query' => array(
          array(
            'key' => 'related_program',
            'compare' => 'LIKE',
            'value' => '"'.get_the_ID().'"'
          )
        )
      ));
      if($campusesQuery->have_posts()) { 
        echo "<hr class='section-break'>";
        echo '<h3 class="headline headline--medium"> Where to Learn this Program </h3>';
        echo '<ul class="link-list min-list">';
        while($campusesQuery->have_posts()) :
          $campusesQuery->the_post(); ?>
          <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
        <?php endwhile;
        echo '</ul>';
      }
      wp_reset_postdata();
      ?>			
      <?php 
        // Query para encontrar los eventos
        // que tienen una relacion con el programa actual
        $eventQuery = new WP_Query(array(
          'posts_per_page' => -1,
          'post_type' => 'event',
          'meta_key'=> 'event_date',
          'orderby' => 'meta_value_num',
          'order' => 'ASC',
          'meta_query' => array(
            array(
              'key' => 'event_date',
              'compare' => '>=',
              'value' => date('Ymd'),
              'type' => 'numeric'
            ),
            array(
              'key' => 'related_program',
              'compare' => 'LIKE',
              'value' => '"'.get_the_ID().'"'
            )
        )));
        if($eventQuery->have_posts()) {
          echo '<hr class="section-break">';
          echo '<h3 class="headline headline--medium">Upcoming ' . get_the_title() . ' Events</h3>';
          while($eventQuery->have_posts()) {
            $eventQuery->the_post();
            $eventDate = new DateTime(get_field('event_date'));
            get_template_part('template-parts/content', 'event');
          }
        }
        wp_reset_postdata();
      ?>
		</div>
	</div>
		<?php
    }
    get_footer();