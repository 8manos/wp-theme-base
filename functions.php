<?php


class incpTheme {
	const version = '0.1';
	public static $dir_theme;
	public static $url_theme;


	public static function setup() {
		self::$dir_theme = $dir = get_template_directory();
		self::$url_theme = get_template_directory_uri();

		# The mini-lib
		require_once "{$dir}/p/krr.php";

		# Set the content width based on the theme's design and stylesheet.
		if ( ! isset( $content_width ) )
			$content_width = 620;

		self::init();
	}


	public static function init() {
		# i18n
		load_theme_textdomain( 'incp', self::$dir_theme . '/l' );

		# Menus
		register_nav_menus( array(
			'header' => __('Header Menu', 'virje'),
			'footer' => __('Footer Menu', 'virje')
		) );

		add_action( 'wp_enqueue_scripts', array(__CLASS__, 'sns'), 99 );
	}


	public static function sns() {
		wp_enqueue_style( 'virje', self::$url_theme.'/style.css', false, self::version );

		if ( is_singular() && post_type_supports(get_post_type(), 'comments') && comments_open() && get_option('thread_comments') )
			wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'after_setup_theme', array('incpTheme', 'setup') );


?>
