<?php
/**
 * Home widgets
 *
 * @package x-Store
 */

// Bail if not home page.
if ( ! is_page_template( 'templates/home.php' )  ) {
	return;
}
?>

<div id="home-page-widget-area" class="widget-area">
	
		<?php if ( is_active_sidebar( 'home-page-widget-area' ) ) : ?>
			<?php dynamic_sidebar( 'home-page-widget-area' ); ?>
		<?php else: ?>
			<?php
			// CTA.
			$args = array(
				'title'       => esc_html__( 'This is x-Store', 'x-store' ),
				'filter'      => true,
				'button_url'  => '#',
				'button_text' => esc_html__( 'Learn More', 'x-store' ),
				'text'        => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dignissimos dolor possimus inventore ut sint et, blanditiis nobis tempore voluptatum, autem in. Provident fugiat sunt placeat quibusdam dolore, quasi repudiandae eius.', 'x-store' ),
			);
			if ( current_user_can( 'edit_theme_options' ) ) {
				$args['button_url']  = esc_url( admin_url( 'widgets.php' ) );
				$args['button_text'] = esc_html__( 'Add Widgets', 'x-store' );
				$args['text']        = esc_html__( 'Widgets of Home Page Widget Area will be displayed here. Go to Appearance->Widgets in admin panel to add widgets. All these widgets will be replaced when you start adding widgets.', 'x-store' );
			}

			$widget_args = array(
				'before_title' => '<h2 class="widget-title">',
				'after_title'  => '</h2>',
			);
			the_widget( 'X_Store_CTA_Widget', $args, $widget_args );
			?>
		<?php endif; ?>
	
</div><!-- #home-page-widget-area -->

