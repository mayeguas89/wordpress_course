<?php
    get_header();
	while(have_posts())
	{
      the_post();
      var_dump(parse_blocks(get_the_content()));
  }
  get_footer();