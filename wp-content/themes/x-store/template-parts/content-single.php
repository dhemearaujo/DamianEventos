<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package x-Store
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="featured-thumb">
			<?php the_post_thumbnail( 'full' ); ?>
		</div>
	<?php endif; ?>

	<div class="content-wrap">
		<div class="content-wrap-inner">

			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			
			<div class="entry-meta entry-footer">
				<?php x_store_entry_footer(); ?>
				<?php x_store_posted_on(); ?>
			</div>

			<div class="entry-content">
				<?php
					the_content( sprintf(
						/* translators: %s: Name of current post. */
						wp_kses( esc_html__( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'x-store' ), array( 'span' => array( 'class' => array() ) ) ),
						the_title( '<span class="screen-reader-text">"', '"</span>', false )
					) );

					wp_link_pages( array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'x-store' ),
						'after'  => '</div>',
					) );
				?>
			</div><!-- .entry-content -->
		</div>
	</div>

</article><!-- #post-## -->
