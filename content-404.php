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

				<?php do_action( 'kct_before_entry' ); ?>
				<article id="not-found" class="hentry entry404">
					<header class="entry-title">
						<h1><?php echo $title ?></h1>
						<?php do_action( 'kct_after_entry_title' ); ?>
					</header>

					<?php do_action( 'kct_before_entry_content' ); ?>
					<div class="entry-content">
            <p><?php echo $info ?></p>
					</div>
					<?php do_action( 'kct_after_entry_content' ); ?>

				</article>
				<?php do_action( 'kct_after_entry' ); ?>
