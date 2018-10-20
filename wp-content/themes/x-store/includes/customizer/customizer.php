<?php
/**
 * x-Store Theme Customizer.
 *
 * @package x-Store
 */

/**
 * Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function x_store_customize_register( $wp_customize ) {

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'            => '.site-title a',
			'container_inclusive' => false,
			'render_callback'     => 'x_store_customize_partial_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'            => '.site-description',
			'container_inclusive' => false,
			'render_callback'     => 'x_store_customize_partial_blogdescription',
		) );
	}

	// Sanitization.
	require_once trailingslashit( get_template_directory() ) . '/includes/customizer/sanitize.php';

	// Active callback.
	require_once trailingslashit( get_template_directory() ) . '/includes/customizer/active.php';

	// Load options.
	require_once trailingslashit( get_template_directory() ) . '/includes/customizer/options.php';

	/* Load Upgrade to Pro control
	----------------------------------------------------------------------*/
	require_once trailingslashit( get_template_directory() ) . '/includes/upgrade-to-pro/control.php';

	/* Register custom section types.
	----------------------------------------------------------------------*/
	$wp_customize->register_section_type( 'X_Store_Customize_Section_Upsell' );

	// Register sections.
	$wp_customize->add_section(
		new X_Store_Customize_Section_Upsell(
			$wp_customize,
			'theme_upsell',
			array(
				'title'    => esc_html__( 'x-Store Pro', 'x-store' ),
				'pro_text' => esc_html__( 'Buy Pro', 'x-store' ),
				'pro_url'  => 'https://www.prodesigns.com/wordpress-themes/downloads/x-store-pro/',
				'priority' => 1,
			)
		)
	);

}
add_action( 'customize_register', 'x_store_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @since 1.0.0
 *
 * @return void
 */
function x_store_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @since 1.0.0
 *
 * @return void
 */
function x_store_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function x_store_customize_preview_js() {
	wp_enqueue_script( 'x-store-customizer', get_template_directory_uri() . '/assets/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'x_store_customize_preview_js' );

/**
 * Enqueue style for custom customize control.
 */
function x_store_custom_customize_enqueue() {

	wp_enqueue_script( 'x-store-customize-controls', get_template_directory_uri() . '/includes/upgrade-to-pro/customize-controls.js', array( 'customize-controls' ) );

	wp_enqueue_style( 'x-store-customize-controls', get_template_directory_uri() . '/includes/upgrade-to-pro/customize-controls.css' );
}
add_action( 'customize_controls_enqueue_scripts', 'x_store_custom_customize_enqueue' );