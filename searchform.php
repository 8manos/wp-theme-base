<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="s" class="assistive-text"><?php _e('Search', 'persada'); ?></label>
	<input type="text" name="s" id="s" placeholder="<?php esc_attr_e('Search', 'persada'); ?>" />
	<button type="submit" id="searchsubmit"><span><?php _e('Search', 'persada') ?></span></button>
</form>
