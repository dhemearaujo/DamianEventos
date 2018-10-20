<?php
/**
 * Helper functions.
 *
 * @package x-Store
 */

// Bail if no slider.
$slider_details = x_store_get_slider_details();
if ( empty( $slider_details ) ) {
	return;
}

// Slider status.
$slider_status = x_store_get_option( 'slider_status' );
if ( 1 !== absint( $slider_status ) ) {
	return;
}

if ( ! ( is_front_page()) && ! is_page_template( 'templates/home.php' ) ) {
    return;
} 

// Slider settings.
$slider_transition_effect 		= x_store_get_option( 'slider_transition_effect' );
$slider_transition_delay  		= x_store_get_option( 'slider_transition_delay' );
$slider_snow_status             = x_store_get_option( 'slider_snow_status' );
$slider_arrow_status      		= x_store_get_option( 'slider_arrow_status' );
$slider_pager_status     		= x_store_get_option( 'slider_pager_status' );
$slider_autoplay_status   		= x_store_get_option( 'slider_autoplay_status' );
$slider_overlay_status   		= x_store_get_option( 'slider_overlay_status' );

$slider_readmore_status   		= x_store_get_option( 'slider_readmore_status' );
$slider_readmore_text   		= x_store_get_option( 'slider_readmore_text' );

$slick_args = array(
    'slidesToShow'      => 1,
    'slidesToScroll'    => 1,
);

if ( 1 === absint( $slider_autoplay_status ) ) {
    $slick_args['autoplay']      = true;
    $slick_args['autoplaySpeed'] = 1000 * absint( $slider_transition_delay );
}else{
    $slick_args['autoplay']      = false;
}

if ( 1 === absint($slider_arrow_status) ) {
    $slick_args['arrows'] = true;
    
}else{
    $slick_args['arrows'] = false;
}

if ( 1 === absint($slider_pager_status) ) {
    $slick_args['dots']      = true;
    
}else{
    $slick_args['dots']      = false;
}

if ( 'fade' === $slider_transition_effect ) {
    $slick_args['fade']      = true;
    
}else{
    $slick_args['fade']      = false;
}

$slick_args_encoded = wp_json_encode( $slick_args );

$overlay_class = ( true === $slider_overlay_status ) ? 'overlay-enabled' : 'overlay-disabled' ;

if ( 1 === absint($slider_snow_status) ) {
    $snow_class = 'snow-disabled';
}else{
    $snow_class = 'snow-enabled';
}
?>
<section id="featured-slider" class="<?php echo $snow_class; ?>">

        <div id="main-banner">

                <?php 

                $slider_number = 5;

                $page_ids = array();

                for ( $i = 1; $i <= $slider_number ; $i++ ) {
                    $page_id = x_store_get_option( "slider_page_$i" );
                    if ( absint( $page_id ) > 0 ) {
                        $page_ids[] = absint( $page_id );
                    }
                }

                if ( empty( $page_ids ) ) {
                    return $output;
                }

                $slider_args = array(
                    'posts_per_page' => count( $page_ids ),
                    'orderby'        => 'post__in',
                    'post_type'      => 'page',
                    'post__in'       => $page_ids,
                    'meta_query'     => array(
                        array( 'key' => '_thumbnail_id' ),
                    ),
                );

                $slider_posts = new WP_Query( $slider_args );

                if ( $slider_posts->have_posts() ) : ?>

                    <div class="slick-main-slider" data-slick='<?php echo $slick_args_encoded; ?>'>

                        <?php

                        if ( true === $slider_overlay_status ) {

                            $overlay_class = 'item overlay';
                            
                        }else{

                            $overlay_class = 'item';

                        } 
                        
                        while ( $slider_posts->have_posts() ) :
                            
                            $slider_posts->the_post();

                            $image_array = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );

                            $slide_url = $image_array[0]; ?>

                            <div class="<?php echo $overlay_class; ?>" style="background:url(<?php echo esc_url( $slide_url ); ?>) top center; background-size:cover;">
                                <div class="container">
                                    <div class="caption">
                                        <h2><?php the_title(); ?></h2>

                                        <?php

                                        $slider_excerpt_length  = x_store_get_option('slider_excerpt_length'); 

                                        $slider_excerpt = x_store_get_the_excerpt( absint($slider_excerpt_length), $post );

                                        ?>

                                        <p><?php echo esc_html( $slider_excerpt ); ?></p>

                                        <?php 
                                        if( true === $slider_readmore_status && !empty( $slider_readmore_text ) ){ ?>
                                            <a href="<?php the_permalink(); ?>" class="button"><?php echo esc_html( $slider_readmore_text ); ?></a>
                                            <?php 
                                        } ?>
                                    </div>
                                </div><!-- .container -->
                            </div>

                            <?php

                        endwhile; 

                        wp_reset_postdata(); ?>

                    </div>

                    <?php

                endif; ?>
                
                



        </div><!-- .main-banner -->

</section><!-- #main-banner -->