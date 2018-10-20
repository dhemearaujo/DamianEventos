<?php
/**
 * Template part for displaying results in search pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package x-Store
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( has_post_thumbnail() ) : ?>
	<div class="featured-thumb">
		<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('x-store-grid'); ?></a>
	</div>
	<?php endif; ?>

	<?php $contet_class =  ( has_post_thumbnail() ) ? 'content-with-image' : 'content-no-image'; ?>

	<div class="content-wrap <?php echo $contet_class; ?>">
		<div class="content-wrap-inner">
			<header class="entry-header">
				<?php
				the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );

				if ( 'post' === get_post_type() ) : ?>
					<div class="entry-meta">
						<?php x_store_entry_footer(); ?>
					</div><!-- .entry-meta -->
					<?php
				endif; ?>
			</header><!-- .entry-header -->

			<div class="entry-content">
				<?php the_excerpt(); ?>
				
				<div class="entry-footer">
					<?php x_store_posted_on(); ?>
				</div>
			</div><!-- .entry-content -->
		</div>
	</div>

</article><!-- #post-## -->