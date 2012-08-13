				<?php do_action( 'tha_entry_before' ); ?>
				<article id="post-<?php the_ID() ?>" <?php post_class() ?>>
					<?php do_action( 'tha_entry_top' ); ?>

					<header class="entry-title">
						<?php do_action( 'tha_entry_header_top' ); ?>
						<?php
							if ( $_title = get_the_title() ) {
								$title = is_singular() ? "<h1>{$_title}</h1>\n" : "<h1><a href='".get_permalink()."' title='".the_title_attribute(array('echo' => false))."'>{$_title}</a></h1>\n";
								echo apply_filters( 'kct_entry_title', $title, $_title );
							}
						?>
						<?php do_action( 'tha_entry_header_bottom' ); ?>
					</header>

					<?php do_action( 'tha_entry_content_before' ); ?>
					<div class="entry-content">
						<?php do_action( 'tha_entry_content_top' ); ?>
						<?php the_content(__('Continue&hellip;', 'TEXT_DOMAIN')) ?>
						<?php do_action( 'tha_entry_content_bottom' ); ?>
					</div>
					<?php do_action( 'tha_entry_content_after' ); ?>

					<?php do_action( 'tha_entry_bottom' ); ?>
				</article>
				<?php do_action( 'tha_entry_after' ); ?>