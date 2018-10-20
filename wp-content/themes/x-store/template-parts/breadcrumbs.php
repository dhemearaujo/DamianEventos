<?php
/**
 * Breadcrumbs.
 *
 * @package x-Store
 */

// Bail if front page.
if ( is_front_page() || is_page_template( 'templates/home.php' ) ) {
	return;
}

$breadcrumb_type = x_store_get_option( 'breadcrumb_type' );
if ( 'disable' === $breadcrumb_type ) {
	return;
}

if ( ! function_exists( 'x_store_breadcrumb_trail' ) ) {
	require_once trailingslashit( get_template_directory() ) . '/includes/breadcrumbs/breadcrumbs.php';
}
?>

<div id="breadcrumb-items">
		<?php
		$breadcrumb_args = array(
			'container'   => 'div',
			'show_browse' => false,
		);
		x_store_breadcrumb_trail( $breadcrumb_args );
		?>
</div><!-- #breadcrumb -->
