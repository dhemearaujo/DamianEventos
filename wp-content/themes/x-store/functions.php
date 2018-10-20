<?php
/**
 * x-Store functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package x-Store
 */

if ( ! function_exists( 'x_store_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function x_store_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on x-Store, use a find and replace
	 * to change 'x-store' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'x-store' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for custom logo.
	 */
	add_theme_support( 'custom-logo' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );
	add_image_size('x-store-grid', 375, 250, true);
	add_image_size('x-store-news', 370, 240, true);

	// Register navigation menu locations.
	register_nav_menus( array(
		'top' 		=> esc_html__( 'Top Header', 'x-store' ),
		'primary' 	=> esc_html__( 'Primary Header', 'x-store' ),
		'social'  	=> esc_html__( 'Social Links', 'x-store' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'x_store_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	// Add woocommerce support, gallery zoom, lightbox and thumbnail slider.
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	/*
     * This theme styles the visual editor to resemble the theme style,
     * specifically font, colors, and column width.
     */
    add_editor_style(array(get_template_directory_uri() . '/assets/css/editor-style.css'));
}
endif;
add_action( 'after_setup_theme', 'x_store_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function x_store_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'x_store_content_width', 810 );
}
add_action( 'after_setup_theme', 'x_store_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function x_store_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'x-store' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here to appear in Sidebar.', 'x-store' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Home Page Widget Area', 'x-store' ),
		'id'            => 'home-page-widget-area',
		'description'   => esc_html__( 'Add widgets here to appear in Home Page Widget Area.', 'x-store' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="container">',
		'after_widget'  => '</div></section>',
		'before_title'  => '<h2 class="home-widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => sprintf( esc_html__( 'Footer %d', 'x-store' ), 1 ),
		'id'            => 'footer-1',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => sprintf( esc_html__( 'Footer %d', 'x-store' ), 2 ),
		'id'            => 'footer-2',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => sprintf( esc_html__( 'Footer %d', 'x-store' ), 3 ),
		'id'            => 'footer-3',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => sprintf( esc_html__( 'Footer %d', 'x-store' ), 4 ),
		'id'            => 'footer-4',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'x_store_widgets_init' );

/**
* Enqueue scripts and styles.
*/
function x_store_scripts() {

	wp_enqueue_style( 'x-store-fonts', x_store_fonts_url(), array(), null );

	wp_enqueue_style( 'jquery-meanmenu', get_template_directory_uri() . '/assets/css/meanmenu.css' );

	wp_enqueue_style( 'jquery-slick', get_template_directory_uri() . '/assets/css/slick.css', '', '1.6.0' );

	wp_enqueue_style( 'x-store-icons', get_template_directory_uri() . '/assets/css/icons.css', '', '1.0.0' );

	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.min.css', '', '4.7.0' );

	wp_enqueue_style( 'x-store-style', get_stylesheet_uri() );

	wp_enqueue_script( 'x-store-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), '20151215', true );

	wp_enqueue_script( 'x-store-skip-link-focus-fix', get_template_directory_uri() . '/assets/js/skip-link-focus-fix.js', array(), '20151215', true );

	wp_enqueue_script( 'jquery-meanmenu', get_template_directory_uri() . '/assets/js/jquery.meanmenu.js', array('jquery'), '2.0.2', true );

	wp_enqueue_script( 'jquery-slick', get_template_directory_uri() . '/assets/js/slick.js', array('jquery'), '1.6.0', true );

	wp_enqueue_script( 'x-store-custom', get_template_directory_uri() . '/assets/js/custom.js', array( 'jquery' ), '1.0.4', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'x_store_scripts' );

/**
* Enqueue scripts and styles for admin >> widget page only.
*/
function x_store_admin_scripts( $hook ) {

	if( 'widgets.php' === $hook ){

		wp_enqueue_style( 'x-store-admin', get_template_directory_uri() . '/includes/widgets/css/admin.css', array(), '1.0.4' );

		wp_enqueue_media();

		wp_enqueue_script( 'x-store-admin', get_template_directory_uri() . '/includes/widgets/js/admin.js', array( 'jquery' ), '1.0.4' );

	}

}

add_action( 'admin_enqueue_scripts', 'x_store_admin_scripts' );

// Load main file.
require_once trailingslashit( get_template_directory() ) . '/includes/main.php';