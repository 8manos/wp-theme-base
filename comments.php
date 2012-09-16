	<div id="comments">
	<?php if ( post_password_required() ) { ?>
		<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'TEXT_DOMAIN' ); ?></p>
	</div>
	<?php return; } ?>

	<?php if ( have_comments() ) { ?>
		<?php kct_response_list( get_the_ID() ); ?>

		<?php if ( !comments_open() ) { ?>
			<p class="nocomments"><?php _e( 'Comments are closed.', 'TEXT_DOMAIN' ); ?></p>
		<?php } ?>

	<?php } ?>
	<?php comment_form(); ?>
	</div>