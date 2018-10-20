<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package x-Store
 */

get_header(); ?>

<?php if ( true === apply_filters( 'x_store_home_page_content', true ) ) : ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<div class="inner-wrapper">
				<?php
				if ( have_posts() ) :

					/* Start the Loop */
					while ( have_posts() ) : the_post();

						get_template_part( 'template-parts/content' );

					endwhile;

					the_posts_pagination();

				else :

					get_template_part( 'template-parts/content', 'none' );

				endif; ?>
			</div>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
do_action( 'x_store_action_sidebar' );

endif; // End if show home content.

get_footer();
