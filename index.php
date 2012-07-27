<?php get_header() ?>

		<?php do_action( 'tha_content_before' ); ?>
		<div id="content" role="main">
			<?php do_action( 'tha_content_top' ); ?>
			<?php
				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();
						get_template_part( 'content', apply_filters('kct_content_template', get_post_type()) );
					}

					kct_paginate_links();
				}
				else {
					get_template_part( 'content', apply_filters('kct_content_template', '404') );
				}
			?>
			<?php do_action( 'tha_content_bottom' ); ?>
		</div>
		<?php do_action( 'tha_content_after' ); ?>

<?php get_footer() ?>
