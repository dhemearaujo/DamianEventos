<?php
/**
 * Core functions.
 *
 * @package x-Store
 */

if ( ! function_exists( 'x_store_get_option' ) ) :

	/**
	 * Get theme option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Option key.
	 * @return mixed Option value.
	 */
	function x_store_get_option( $key ) {

		if ( empty( $key ) ) {

			return;

		}

		$value 			= '';

		$default 		= x_store_get_default_theme_options();

		$default_value 	= null;

		if ( is_array( $default ) && isset( $default[ $key ] ) ) {

			$default_value = $default[ $key ];

		}

		if ( null !== $default_value ) {

			$value = get_theme_mod( $key, $default_value );

		}else {

			$value = get_theme_mod( $key );

		}

		return $value;

	}

endif;

if ( ! function_exists( 'x_store_get_default_theme_options' ) ) :

	/**
	 * Get default theme options.
	 *
	 * @since 1.0.0
	 *
	 * @return array Default theme options.
	 */
	function x_store_get_default_theme_options() {

		$defaults = array();

		// Header.
		$defaults['site_identity'] 		= 'title-text';
		$defaults['show_top_header'] 	= false;
		$defaults['left_section'] 		= 'contact';
		$defaults['right_section'] 		= 'top-social';

		// Layout.
		$defaults['global_layout']  	= 'right-sidebar';

		// Footer.
		$defaults['copyright_text'] 	= esc_html__( 'Copyright &copy; All rights reserved.', 'x-store' );

		// Blog.
		$defaults['excerpt_length'] 	= 12;

		// Breadcrumb.
		$defaults['breadcrumb_type'] 	= 'simple';

		// Slider.
		$defaults['slider_status']            		= false;
		$defaults['slider_excerpt_length']    		= 20;
		$defaults['slider_transition_effect'] 		= 'scroll';
		$defaults['slider_transition_delay']  		= 3;
		$defaults['slider_snow_status']             = true;
		$defaults['slider_arrow_status']      		= true;
		$defaults['slider_pager_status']      		= true;
		$defaults['slider_autoplay_status']   		= true;
		$defaults['slider_overlay_status']    		= true;

		$defaults['slider_readmore_status']    		= true;
		$defaults['slider_readmore_text']    		= esc_html__( 'Read More', 'x-store' );

		return $defaults;
	}

endif;

//=============================================================
// Get all options in array
//=============================================================
if ( ! function_exists( 'x_store_get_options' ) ) :

    /**
     * Get all theme options in array.
     *
     * @since 1.0.0
     *
     * @return array Theme options.
     */
    function x_store_get_options() {

        $value = array();

        $value = get_theme_mods();

        return $value;

    }

endif;