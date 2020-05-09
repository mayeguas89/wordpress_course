<?php
    get_header();
	while(have_posts())
	{
        the_post();
        echo get_post_gallery();
        
  }
  get_footer();