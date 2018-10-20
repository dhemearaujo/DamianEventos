<?php
/**
 * Helper functions.
 *
 * @package Business_Zone
 */

if ( ( is_front_page()) || is_page_template( 'templates/home.php' ) ) {
    return;
} ?>

<div id="breadcrumb">
	<div class="container">
		<div class="page-title">

				<?php 
				if(is_page() || is_single() ){ ?>

					<h2><?php echo esc_html( get_the_title() ); ?></h2>

					<?php
				} elseif( is_search() ){ ?>

			        <h2><?php printf( esc_html__( 'Search Results for: %s', 'x-store' ), '<span>' . get_search_query() . '</span>' ); ?></h2>

			        <?php
			    }elseif( is_404() ){ ?>

			        <h2><?php echo esc_html( 'Page Not Found: 404', 'x-store'); ?></h2>

			        <?php
			    }elseif( is_home() ){ ?>

			        <h2><?php single_post_title(); ?></h2>

			        <?php
			    } else{

					the_archive_title( '<h2>', '</h2>' );

				}
				?>
			
		</div>

		<?php get_template_part( 'template-parts/breadcrumbs' ); ?>
	</div>
</div><!-- #inner-banner -->