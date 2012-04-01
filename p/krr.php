<?php

/**
 * Check requirements
 *
 * This will check or the required plugins/functions needed
 * for the theme to work. If one of the requirements doesnt exist,
 * it will activate the default theme set by WP_DEFAULT_THEME constant
 *
 * @param array $reqs Array of classes/functions to check
 */
function kct_check_req( $reqs, $message = '' ) {
	foreach ( $reqs as $req ) {
		if ( !class_exists($req) || !function_exists($req) ) {
			$message .= '<br />&laquo; <a href="'.wp_get_referer().'">'.__('Go back', 'TEXT_DOMAIN').'</a>.';
			switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
			wp_die( $message );
		}
	}
}


/**
 * Some more body classes
 */
function kct_body_class( $classes ) {
	if ( is_singular() ) {
		$classes[] = 'singular';
		$classes = array_merge( $classes, array_keys(kct_post_sidebars::$post_data) );
	}

	global $wp_registered_sidebars;
	if ( !empty($wp_registered_sidebars) ) {
		foreach ( array_keys($wp_registered_sidebars) as $sidebar )
			if ( is_active_sidebar($sidebar) )
				$classes[] = "active-sidebar-{$sidebar}";
	}

	return $classes;
}
add_filter( 'body_class', 'kct_body_class' );


/**
 * Print sidebar
 */
function kct_do_sidebar( $sidebar, $wrap = true, $class = 'sidebar' ) {
	if ( !is_active_sidebar($sidebar) ) return; ?>
<?php if ( $wrap ) { ?>
<div id="<?php echo $sidebar ?>" class="<?php echo $class ?>">
<?php } ?>
<?php do_action( "kct_before_sidebar_{$sidebar}" ); ?>
<?php dynamic_sidebar( $sidebar ); ?>
<?php do_action( "kct_after_sidebar_{$sidebar}" ); ?>
<?php if ( $wrap ) { ?>
</div>
<?php } ?>
<?php }


/**
 * Some more body classes
 */
function kct_post_class( $classes, $class, $post_id ) {
	if ( current_theme_supports('post-thumbnails') && has_post_thumbnail() )
		$classes[] = 'has-post-thumbnail';

	if ( is_singular() && get_queried_object_id() === $post_id )
		$classes[] = 'kc-current-post';

	return $classes;
}
add_filter( 'post_class', 'kct_post_class', 10, 3 );


/**
 * Document title (<title></title>)
 */
function kct_doc_title( $title ) {
	global $page, $paged;

	$sep = apply_filters( 'kct_doc_title_sep', '&laquo;' );
	$seplocation = apply_filters( 'kct_doc_title_seplocation', 'right' );
	$pg_sep = apply_filters( 'kct_doc_title_pagenum_sep', '|' );
	$home_sep = apply_filters( 'kct_doc_title_home_sep', '&mdash;' );

	$site_name = get_bloginfo( 'name', 'display' );
	$site_desc = get_bloginfo( 'description', 'display');
	$page_num = ( $paged >= 2 || $page >= 2 ) ? " ${pg_sep} " . sprintf( __('Page %s', 'TEXT_DOMAIN'), max($paged, $page) ) : '';

	# Homepage
	if ( is_home() || is_front_page() ) {
		$title = $site_name;
		if ( $site_desc )
			$title .= " ${home_sep} ${site_desc}";
		$title .= $page_num;
	} else {
		if ( $seplocation == 'right' )
			$title = "${title} ${page_num} ${sep} ${site_name}";
		else
			$title = "${site_name} ${sep} ${title} ${page_num}";
	}

	return $title;
}
if ( !defined('WPSEO_VERSION') )
	add_filter( 'wp_title', 'kct_doc_title' );


# <head /> stuff
function kct_head_stuff() { ?>
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!--[if lt IE 9]>
<?php wp_print_scripts( array('html5') ) ?>
<![endif]-->
<?php }


/**
 * Paginate Links on index pages
 */
function kct_paginate_links( $query = null, $echo = true ) {
	if ( !$query ) {
		global $wp_query;
		$query = $wp_query;
	}

	if ( !is_object($query) )
		return false;

	$current = max( 1, $query->query_vars['paged'] );
	$big = 999999999;

	$pagination = array(
		'base'    => str_replace( $big, '%#%', get_pagenum_link($big) ),
		'format'  => '',
		'total'   => $query->max_num_pages,
		'current' => $current,
		'type'    => 'list'
	);
	$links = paginate_links($pagination);

	if ( empty($links) )
		return false;

	if ( $echo )
		echo "<nav class='posts-nav'>\n\t{$links}</nav>\n";
	else
		return $links;
}


/**
 * Get post terms
 *
 * @param $post_object object Post object, either from global $post variable or using the get_post() function
 * @param $echo bool
 * @return array Post terms
 *
 */

function kct_post_terms( $post_object = '', $echo = true ) {
	if ( is_404() )
		return;

	if ( !$post_object ) {
		global $post;
		$post_object = $post;
	}

	if ( !is_object($post_object) )
		return false;

	$terms = array();
	$taxonomies = get_object_taxonomies( $post_object->post_type, 'objects' );

	if ( !is_array($taxonomies) || empty($taxonomies) )
		return false;

	foreach ( $taxonomies as $taxonomy ) {
		if ( !$taxonomy->public )
			continue;

		$label = apply_filters( "kct_post_terms_tax_label_{$taxonomy->name}", $taxonomy->label );
		if ( $post_tems = get_the_term_list($post_object->ID, $taxonomy->name, '', ', ') )
			$terms[$taxonomy->name] = array('label' => $label , 'terms' => $post_tems);
	}

	$terms = apply_filters( 'kct_post_meta', $terms );
	if ( !$echo )
		return $terms;

	$out  = "<ul class='entry-terms'>\n";
	foreach ( $terms as $tax => $tax_terms )
		$out .= "\t<li class='{$tax}'><span class='label'>{$tax_terms['label']}:</span> {$tax_terms['terms']}</li>";
	$out .="</ul>\n";

	echo $out;
}


/**
 * Get comments number of a post
 *
 * @param $post_id int Post ID
 * @param $type Comments type. ''|pings|comment|pingback|trackback Empty string for all types (default)
 *
 * @return int Comments number
 */
function kct_get_comments_count( $post_id = 0, $type = '' ) {
	return count(get_comments(array(
		'post_id' => $post_id,
		'status'  => 'approve',
		'type'    => $type
	)));
}


/**
 * Response list (comments & pings)
 */
function kct_response_list( $post_id = 0 ) {
	foreach ( array('comment' => __('Comments', 'TEXT_DOMAIN'), 'pings' => __('Pings', 'TEXT_DOMAIN')) as $type => $title ) {
		if ( !kct_get_comments_count($post_id, $type) )
			continue; ?>
	<h2 id="<?php echo $type ?>-title"><?php echo apply_filters( "kct_{$type}_list_title", $title, $post_id ) ?></h2>
	<?php do_action( "kct_before_{$type}_list" ) ?>

	<ol id="<?php echo $type ?>list" class="responselist">
		<?php wp_list_comments( array('callback' => "kct_{$type}_list", 'type' => $type) ); ?>
	</ol>

	<?php do_action( "kct_after_{$type}_list" ) ?>

	<?php }
}


/**
 * Comments list
 */
function kct_comment_list( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment-item">
			<footer>
				<div class="comment-author vcard">
					<?php echo get_avatar( $comment, apply_filters( 'kct_comment_avatar_size', 48) ); ?>
					<cite class="fn"><?php comment_author_link() ?></cite>
				</div>

				<div class="comment-meta commentmetadata">
					<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>" class="comment-date"><?php printf( __( '%1$s at %2$s', 'TEXT_DOMAIN' ), get_comment_date(), get_comment_time() ); ?></a>
					<?php comment_reply_link( array_merge($args, array(
						'depth'     => $depth,
						'max_depth' => $args['max_depth'],
						'before'    => '<span class="reply-link"> &ndash; ',
						'after'     => '</span>'
					)) ); ?>
					<?php edit_comment_link( __('Edit', 'TEXT_DOMAIN'), ' &ndash; ' ); ?>
				</div>
			</footer>

			<div class="comment-content">
				<?php
					if ( $comment->comment_approved == '0' )
						echo '<p><em>'.__( 'Your comment is awaiting moderation.', 'TEXT_DOMAIN' ).'</em></p>';
					comment_text();
				?>
			</div>
		</article>
	<?php
}


/**
 * Pings list
 */
function kct_pings_list( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?><?php edit_comment_link( __('Edit', 'TEXT_DOMAIN'), ' | ' ); ?>
<?php }


/**
 * Comment form fields
 */
function kct_comment_form_fields( $fields ) {
	$commenter = wp_get_current_commenter();
	$user = wp_get_current_user();
	$user_identity = ! empty( $user->ID ) ? $user->display_name : '';

	$req = get_option( 'require_name_email' );
	$aria_req = ($req ? " aria-required='true'" : '');

	$fields['author'] = '<p class="comment-form-author">' . '<label for="author">' . __('Name', 'TEXT_DOMAIN') . ($req ? ' <span class="required">*</span>' : '')  . '</label>'.
                      '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>';
	$fields['email']  = '<p class="comment-form-email"><label for="email">' . __('Email', 'TEXT_DOMAIN') . ($req ? ' <span class="required">*</span>' : '') . '</label> ' .
                      '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>';
	$fields['url']    = '<p class="comment-form-url"><label for="url">' . __('Website', 'TEXT_DOMAIN') . '</label>' .
                      '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>';

	return $fields;
}
add_filter( 'comment_form_default_fields', 'kct_comment_form_fields' );


/**
 * wp_list_pages() CSS Classes
 *
 * Append some necessary CSS classes to page lists items
 * outputted by wp_list_pages() when used for a custom post type
 */
function kct_page_css_class( $css_class, $page, $depth, $args, $current_page ) {
	if ( !isset($args['post_type']) || !is_singular($args['post_type']) )
		return $css_class;

	global $post;
	$current_page  = $post->ID;
	$_current_page = $post;
	_get_post_ancestors($_current_page);

	if ( isset($_current_page->ancestors) && in_array($page->ID, (array) $_current_page->ancestors) )
		$css_class[] = 'current_page_ancestor';
	if ( $page->ID == $current_page )
		$css_class[] = 'current_page_item';
	elseif ( $_current_page && $page->ID == $_current_page->post_parent )
		$css_class[] = 'current_page_parent';

	return $css_class;
}
add_filter( 'page_css_class', 'kct_page_css_class', 10, 5 );


/**
 * Get related terms
 *
 * @param string $tax_1 Base taxonomy name
 * @param string $tax_1_term Base taxonomy term
 * @param string $tax_2 Taxonomy name to get terms from
 * @param string $field Baste taxonomy term field
 * @return bool|array Terms on success, false on failure
 */
function kc_get_related_terms( $tax_1, $tax_1_term, $tax_2, $field = 'slug' ) {
	if ( !taxonomy_exists($tax_1) || !taxonomy_exists($tax_2) )
		return false;

	$vars = array(
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => $tax_1,
				'terms'    => $tax_1_term,
				'field'    => $field
			)
		),
		'posts_per_page' => -1
	);

	$q = new WP_Query( $vars );
	if ( !$q->have_posts() )
		return false;

	$terms = array();
	while ( $q->have_posts() ) {
		$q->the_post();
		$p_terms = get_the_terms( get_the_ID(), $tax_2 );
		if ( $p_terms )
			$terms += $p_terms;
	}

	wp_reset_postdata();
	return $terms;
}

/* Enable [embed] shortcode in text widgets */
global $wp_embed;
add_filter( 'widget_text', array( $wp_embed, 'run_shortcode' ), 8 );
add_filter( 'widget_text', array( $wp_embed, 'autoembed'), 8 );


/* Single post sidebar setting */
class kct_post_sidebars {
	private static $post_types;
	public static $post_data = array();


	public static function init() {
		$post_types = apply_filters( 'kct_post_sidebars', array() );
		if ( !$post_types )
			return;

		self::$post_types = $post_types;
		add_filter( 'kc_post_settings', array(__CLASS__, 'metadata_post') );
		add_action( 'get_header', array(__CLASS__, 'post_data') );
		foreach ( array('kct_before_main', 'kct_before_loop', 'kct_after_loop', 'kct_after_main', 'kct_after_content') as $hook )
			add_action( $hook, array(__CLASS__, 'post_widget_area') );
	}


	public static function metadata_post( $group ) {
		$sidebars = kcSettings_options::$sidebars;

		foreach ( self::$post_types as $pt ) {
			$groups[] = array(
				$pt => array(
					array(
						'id'      => 'misc',
						'title'   => __('Widget areas', 'TEXT_DOMAIN'),
						'metabox' => array( 'context' => 'side' ),
						'fields'  => array(
							array(
								'id'      => 'wa-after-header',
								'title'   => __('After header', 'TEXT_DOMAIN'),
								'type'    => 'select',
								'options' => $sidebars
							),
							array(
								'id'      => 'wa-before-page',
								'title'   => __('Before page', 'TEXT_DOMAIN'),
								'type'    => 'select',
								'options' => $sidebars
							),
							array(
								'id'      => 'wa-before-content',
								'title'   => __('Before content', 'TEXT_DOMAIN'),
								'type'    => 'select',
								'options' => $sidebars
							),
							array(
								'id'      => 'wa-after-content',
								'title'   => __('After content', 'TEXT_DOMAIN'),
								'type'    => 'select',
								'options' => $sidebars
							),
							array(
								'id'      => 'wa-side',
								'title'   => __('Side', 'TEXT_DOMAIN'),
								'type'    => 'select',
								'options' => $sidebars
							),
							array(
								'id'      => 'wa-after-page',
								'title'   => __('After page', 'TEXT_DOMAIN'),
								'type'    => 'select',
								'options' => $sidebars
							),
							array(
								'id'      => 'wa-before-footer',
								'title'   => __('Before footer', 'TEXT_DOMAIN'),
								'type'    => 'select',
								'options' => $sidebars
							)
						)
					)
				)
			);
		}

		return $groups;
	}


	public static function post_data() {
		if ( !is_singular() )
			return;

		global $post;
		$meta = get_post_custom( $post->ID );
		foreach ( array('wa-after-header', 'wa-before-page', 'wa-before-content', 'wa-side', 'wa-after-content', 'wa-after-page', 'wa-before-footer') as $wa ) {
			if ( isset($meta["_{$wa}"][0]) && $meta["_{$wa}"][0] && is_active_sidebar($meta["_{$wa}"][0]) )
				self::$post_data["has-{$wa}"] = $meta["_{$wa}"][0];
		}
	}


	public static function post_widget_area() {
		$filter = current_filter();
		$pair = array(
			'kct_before_content' => array('wa-after-header'),
			'kct_before_main'    => array('wa-before-page'),
			'kct_before_loop'    => array('wa-before-content'),
			'kct_after_loop'     => array('wa-after-content'),
			'kct_after_main'     => array('wa-side', 'wa-after-page'),
			'kct_after_content'  => array('wa-before-footer')
		);
		if ( !isset($pair[$filter]) )
			return;

		foreach( $pair[$filter] as $wa ) {
			if ( isset(self::$post_data["has-$wa"]) ) { ?>
<div id="wa-<?php echo self::$post_data["has-$wa"] ?>" class="widget-area <?php echo $wa ?>">
	<?php kct_do_sidebar( self::$post_data["has-$wa"], false ) ?>
</div>
			<?php }
		}
	}
}
add_action( 'init', array('kct_post_sidebars', 'init') );

?>
