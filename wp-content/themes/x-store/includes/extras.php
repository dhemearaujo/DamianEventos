<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package x-Store
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function x_store_body_classes( $classes ) {

	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Add class for global layout.
	$global_layout = x_store_get_option( 'global_layout' );
	$global_layout = apply_filters( 'x_store_filter_theme_global_layout', $global_layout );
	$classes[] = 'global-layout-' . esc_attr( $global_layout );

	$header_status = x_store_get_option( 'show_top_header' );
		
	if ( 1 == $header_status ) {

		$classes[] = 'top-header-active';

	}

	$slider_status = x_store_get_option( 'slider_status' );
		
	if ( 1 != $slider_status ) {

		$classes[] = 'slider-inactive';

	}

	// Custom image.
	$banner_image = get_header_image();

	if( empty( $banner_image ) ){

		$classes[] = 'banner-inactive';

	}

	return $classes;
}
add_filter( 'body_class', 'x_store_body_classes' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function x_store_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', bloginfo( 'pingback_url' ), '">';
	}
}
add_action( 'wp_head', 'x_store_pingback_header' );


if ( ! function_exists( 'x_store_implement_excerpt_length' ) ) :

	/**
	 * Implement excerpt length.
	 *
	 * @since 1.0.0
	 *
	 * @param int $length The number of words.
	 * @return int Excerpt length.
	 */
	function x_store_implement_excerpt_length( $length ) {

		$excerpt_length = x_store_get_option( 'excerpt_length' );
		
		if ( absint( $excerpt_length ) > 0 ) {

			$length = absint( $excerpt_length );

		}

		return $length;

	}
endif;

if ( ! function_exists( 'x_store_implement_read_more' ) ) :

	/**
	 * Implement read more in excerpt.
	 *
	 * @since 1.0.0
	 *
	 * @param string $more The string shown within the more link.
	 * @return string The excerpt.
	 */
	function x_store_implement_read_more( $more ) {
		
		$output = '&hellip;';
		
		return $output;

	}
endif;

if ( ! function_exists( 'x_store_hook_read_more_filters' ) ) :

	/**
	 * Hook read more and excerpt length filters.
	 *
	 * @since 1.0.0
	 */
	function x_store_hook_read_more_filters() {
		if ( is_home() || is_category() || is_tag() || is_author() || is_date() || is_search() ) {

			add_filter( 'excerpt_length', 'x_store_implement_excerpt_length', 999 );
			add_filter( 'excerpt_more', 'x_store_implement_read_more' );

		}
	}
endif;
add_action( 'wp', 'x_store_hook_read_more_filters' );

if ( ! function_exists( 'x_store_add_sidebar' ) ) :

	/**
	 * Add sidebar.
	 *
	 * @since 1.0.0
	 */
	function x_store_add_sidebar() {

		$global_layout = x_store_get_option( 'global_layout' );
		$global_layout = apply_filters( 'x_store_filter_theme_global_layout', $global_layout );

		// Include sidebar.
		if ( 'no-sidebar' !== $global_layout ) {
			get_sidebar();
		}

	}
endif;
add_action( 'x_store_action_sidebar', 'x_store_add_sidebar' );

//=============================================================
// Check selected category on product search
//=============================================================
if ( ! function_exists( 'x_store_category_selected' ) ) {

	function x_store_category_selected( $catname ) {

		echo $q_var = get_query_var( 'product_cat' );

		if ( $q_var === $catname ) {

			return 'selected="selected"';
		}

		return false;
	}

}

//=============================================================
// Remove rating info from featured products
//=============================================================
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );

//=============================================================
// Change number of product per row
//=============================================================
if (!function_exists('x_store_product_columns')) {

	function x_store_product_columns() {

		return 3; // 3 products per row

	}
	
}

add_filter('loop_shop_columns', 'x_store_product_columns');

//=============================================================
// Change number of related product
//=============================================================

if (!function_exists('x_store_related_products_args')) {

	function x_store_related_products_args( $args ) {
		
		$args['posts_per_page'] = 3; // 3 related products
		
		return $args;
	}

}

add_filter( 'woocommerce_output_related_products_args', 'x_store_related_products_args' );


//=============================================================
// Change number of upsell products
//=============================================================
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );

add_action( 'woocommerce_after_single_product_summary', 'x_store_output_upsells', 15 );

if ( ! function_exists( 'x_store_output_upsells' ) ) {

	function x_store_output_upsells() {

	    woocommerce_upsell_display( 3, 3 ); // Display 3 products in rows of 3
	    
	}

}