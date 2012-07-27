<?php

if ( is_404() ) {
	$title = __('This is somewhat embarrassing, isn&rsquo;t it?', 'TEXT_DOMAIN');
	$info = __('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help :)', 'TEXT_DOMAIN');
}
else {
	$title = __('Nothing Found', 'TEXT_DOMAIN');
	$info = __('Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'TEXT_DOMAIN');
}
?>

				<?php do_action( 'tha_entry_before' ); ?>
				<article id="not-found" class="hentry entry404">
					<?php do_action( 'tha_entry_top' ); ?>

					<header class="entry-title">
						<?php do_action( 'tha_entry_header_top' ); ?>
						<h1><?php echo $title ?></h1>
						<?php do_action( 'tha_entry_header_bottom' ); ?>
					</header>

					<?php do_action( 'tha_entry_content_before' ); ?>
					<div class="entry-content">
						<?php do_action( 'tha_entry_content_top' ); ?>
            <p><?php echo $info ?></p>
						<?php do_action( 'tha_entry_content_bottom' ); ?>
					</div>
					<?php do_action( 'tha_entry_content_after' ); ?>

					<?php do_action( 'tha_entry_bottom' ); ?>
				</article>
				<?php do_action( 'tha_entry_after' ); ?>
