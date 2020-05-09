<?php
    get_header();
	while(have_posts())
	{
    the_post();
    
  
    pageBanner();
    ?>
	<div class="container container--narrow page-section">
        <div class="generic-content">
          <div class="row group">
            <div class="one-third">
            <?php the_post_thumbnail('professorPortrait'); ?>
            </div>
            <div class="two-third">
            <?php the_content(); ?>
            </div>
          </div>
            
            <?php 
                $relatedPrograms = get_field('related_program'); 
                if($relatedPrograms) 
                {
                    echo "<hr class='section-break'>";
                    echo '<h3 class="headline headline--medium"> Subject(s) Tought</h3>';
                    echo '<ul class="link-list min-list">';
                foreach($relatedPrograms as $program) : 
            ?>
                <li><a href="<?php echo get_the_permalink( $program ); ?>"><?php echo get_the_title( $program ); ?></a></li>

            <?php endforeach;
            echo '</ul>';
                }
            ?>			
		</div>
	</div>
		<?php
    }
    get_footer();