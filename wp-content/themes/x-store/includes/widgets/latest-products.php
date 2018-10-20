<?php
/**
 * Latest products widgets.
 *
 * @package x-Store
 */

if ( ! class_exists( 'X_Store_Latest_Product_Widget' ) ) :

	/**
	 * Latest Products widget class.
	 *
	 * @since 1.0.0
	 */
	class X_Store_Latest_Product_Widget extends WP_Widget {

	    function __construct() {
	    	$opts = array(
				'classname'   => 'x_store_widget_latest_products latest-product-section',
				'description' => esc_html__( 'Widget to display latest or featured products', 'x-store' ),
    		);

			parent::__construct( 'x-store-latest-products', esc_html__( 'x-Store: Woo Products', 'x-store' ), $opts );
	    }


	    function widget( $args, $instance ) {

			$title             	= apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

			$product_type      	= !empty( $instance['product_type'] ) ? $instance['product_type'] : '';

			$product_category   = ! empty( $instance['product_category'] ) ? $instance['product_category'] : 0;

			$product_number     = ! empty( $instance['product_number'] ) ? $instance['product_number'] : 4;

	        echo $args['before_widget'];

    		if ( !empty( $title ) ){ ?>

    			<div class="section-title">

    		        <h2><?php echo esc_html( $title ); ?></h2>

    	        </div>
            	<?php 
            } ?>

	        <div class="latest-product-holder">

		        <?php

		        $meta_query = WC()->query->get_meta_query();

		        $tax_query  = WC()->query->get_tax_query();

		        if( 'featured' == $product_type ){

		        	$tax_query[] = array(
		        		'taxonomy' => 'product_visibility',
		        		'field'    => 'name',
		        		'terms'    => 'featured',
		        		'operator' => 'IN',
		        	);

		        }else{

			        $tax_query[] = array(
			        	'taxonomy' => 'product_cat',
			        	'field'    => 'id',
			        	'terms'    => absint( $product_category ),
			        	'operator' => 'IN',
			        );

		    	}

		        $query_args = array(
		        	'post_type'           => 'product',
		        	'post_status'         => 'publish',
		        	'ignore_sticky_posts' => 1,
		        	'posts_per_page'      => absint( $product_number ),
		        	'meta_query'          => $meta_query,
		        	'no_found_rows'       => true,

		        );



		        if ( absint( $product_category ) > 0 || 'featured' == $product_type ) {

		        	$query_args['tax_query'] = $tax_query;
		        	
		        }

		        global $woocommerce_loop;

		        $latest_products = new WP_Query( $query_args );

		        if ( $latest_products->have_posts() ) :?>

		        	<div class="inner-wrapper">

				        <div class="xstore-woocommerce">
				        	
				        	<ul class="products">

								<?php 

								while ( $latest_products->have_posts() ) :

	                                $latest_products->the_post(); 

	                            	wc_get_template_part( 'content', 'product' );

								endwhile; 

								woocommerce_reset_loop();

	                            wp_reset_postdata(); ?>

	                        </ul>

	                    </div>

	                </div>

		        <?php endif; ?>

	         </div><!-- .latest-news-widget -->

	        <?php
	        echo $args['after_widget'];

	    }

	    function update( $new_instance, $old_instance ) {
	        $instance = $old_instance;
			$instance['title']          	= sanitize_text_field( $new_instance['title'] );
			$instance['product_type'] 	    = sanitize_text_field( $new_instance['product_type'] );
			$instance['product_category']  	= absint( $new_instance['product_category'] );
			$instance['product_number']  	= absint( $new_instance['product_number'] );
			
	        return $instance;
	    }

	    function form( $instance ) {

	        $instance = wp_parse_args( (array) $instance, array(
				'title'          		=> '',
				'product_type'          => 'latest',
				'product_category' 		=> '',
				'product_number' 		=> 4,

	        ) );
	        ?>
	        <p>
	          <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><strong><?php esc_html_e( 'Title:', 'x-store' ); ?></strong></label>
	          <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
	        </p>

            <p>
              <label for="<?php echo esc_attr( $this->get_field_id( 'product_type' ) ); ?>"><strong><?php _e( 'Product Type:', 'x-store' ); ?></strong></label>
    			<?php
                $this->dropdown_product_type( array(
    				'id'       => $this->get_field_id( 'product_type' ),
    				'name'     => $this->get_field_name( 'product_type' ),
    				'selected' => esc_attr( $instance['product_type'] ),
    				)
                );
    			?>
            </p>
	        
            <p>
				<label for="<?php echo  esc_attr( $this->get_field_id( 'product_category' ) ); ?>"><strong><?php esc_html_e( 'Select Category:', 'x-store' ); ?></strong></label>
				<?php
				$cat_args = array(
				    'orderby'         => 'name',
				    'hide_empty'      => 0,
				    'class' 		  => 'widefat',
				    'taxonomy'        => 'product_cat',
				    'name'            => $this->get_field_name( 'product_category' ),
				    'id'              => $this->get_field_id( 'product_category' ),
				    'selected'        => absint( $instance['product_category'] ),
				    'show_option_all' => esc_html__( 'All Categories','x-store' ),
				  );
				wp_dropdown_categories( $cat_args );
				?>
            </p>
	        <p>
	        	<label for="<?php echo esc_attr( $this->get_field_name('product_number') ); ?>">
	        		<?php esc_html_e('Number of Products:', 'x-store'); ?>
	        	</label>
	        	<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('product_number') ); ?>" name="<?php echo esc_attr( $this->get_field_name('product_number') ); ?>" type="number" value="<?php echo absint( $instance['product_number'] ); ?>" />
	        </p>
	        <?php
	    }

        function dropdown_product_type( $args ) {
    		$defaults = array(
    	        'id'       => '',
    	        'class'    => 'widefat',
    	        'name'     => '',
    	        'selected' => 'latest',
    		);

    		$r = wp_parse_args( $args, $defaults );
    		$output = '';

    		$choices = array(
    			'latest' 	=> esc_html__( 'Latest', 'x-store' ),
    			'featured' 	=> esc_html__( 'Featured', 'x-store' ),
    		);

    		if ( ! empty( $choices ) ) {

    			$output = "<select name='" . esc_attr( $r['name'] ) . "' id='" . esc_attr( $r['id'] ) . "' class='" . esc_attr( $r['class'] ) . "'>\n";
    			foreach ( $choices as $key => $choice ) {
    				$output .= '<option value="' . esc_attr( $key ) . '" ';
    				$output .= selected( $r['selected'], $key, false );
    				$output .= '>' . esc_html( $choice ) . '</option>\n';
    			}
    			$output .= "</select>\n";
    		}

    		echo $output;
        }

	}

endif;