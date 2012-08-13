<?php

class kcTheme {
	const version = '0.1';
	public static $dir_theme;
	public static $url_theme;


	public static function setup() {
		self::$dir_theme = $dir = get_template_directory();
		self::$url_theme = get_template_directory_uri();

		# The mini-lib
		require_once "{$dir}/p/krr.php";

		# Set the content width based on the theme's design and stylesheet.
		global $content_width;
		if ( ! isset( $content_width ) )
			$content_width = 920;

		// Theme Hook Alliance
		define( 'THA_HOOKS_VERSION', '1.0-draft' );
		add_theme_support( 'tha_hooks', array('all') );

		# i18n
		load_theme_textdomain( 'TEXT_DOMAIN', self::$dir_theme . '/l' );

		# Menus
		register_nav_menus( array(
			'main' => __('Header Menu', 'TEXT_DOMAIN'),
			'footer' => __('Footer Menu', 'TEXT_DOMAIN')
		) );

		add_action( 'init', array(__CLASS__, 'init') );
		add_action( 'widgets_init', array(__CLASS__, 'register_sidebars') );
		add_action( 'wp_enqueue_scripts', array(__CLASS__, 'sns'), 100 );
		add_action( 'tha_entry_content_after', array(__CLASS__, 'comments') );
	}


	public static function init() {
	}


	public static function register_sidebars() {
		$sidebars = array(
			'wa-footer' => __('Footer widget area', 'TEXT_DOMAIN'),
		);

		foreach ( $sidebars as $id => $name )
			register_sidebar(array(
				'id'            => $id,
				'name'          => $name,
				'before_widget' => '<aside id="%1$s" class="widget %2$s">'."\n",
				'after_widget'  => '</aside>'."\n",
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>'
			));
	}


	public static function sns() {
		wp_enqueue_style( 'TEXT_DOMAIN', self::$url_theme.'/style.css', false, self::version );

		wp_register_script( 'html5', self::$url_theme.'/j/html5shiv.js', false, '3.6' );
		wp_register_script( 'html5-print', self::$url_theme.'/j/html5shiv-printshiv.js', false, '3.6' );

		if ( is_singular() && post_type_supports(get_post_type(), 'comments') && comments_open() && get_option('thread_comments') )
			wp_enqueue_script( 'comment-reply' );
	}


	public static function comments() {
		if ( is_singular() && get_comments_number() )
			comments_template( '', true );
	}
}
add_action( 'after_setup_theme', array('kcTheme', 'setup') );
