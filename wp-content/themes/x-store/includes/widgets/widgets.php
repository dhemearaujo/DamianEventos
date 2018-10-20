<?php
/**
 * Custom widgets.
 *
 * @package x-Store
 */

if ( ! function_exists( 'x_store_load_widgets' ) ) :

	/**
	 * Load widgets.
	 *
	 * @since 1.0.0
	 */
	function x_store_load_widgets() {

		// Social.
		register_widget( 'X_Store_Social_Widget' );

		// Latest news.
		register_widget( 'X_Store_Latest_News_Widget' );

		// CTA widget.
		register_widget( 'X_Store_CTA_Widget' );

		// Features widget.
		register_widget( 'X_Store_Features_Widget' );


	}

endif;

add_action( 'widgets_init', 'x_store_load_widgets' );


if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	if ( ! function_exists( 'x_store_load_woo_widgets' ) ) :

		/**
		 * Load widgets for woocommerce.
		 *
		 * @since 1.0.0
		 */
		function x_store_load_woo_widgets() {

			// Latest Products widget
			register_widget( 'X_Store_Latest_Product_Widget' );

		}

	endif;

	add_action( 'widgets_init', 'x_store_load_woo_widgets' );

	// Latest Products Widget
	require get_template_directory() . '/includes/widgets/latest-products.php';

}


if ( ! class_exists( 'X_Store_Social_Widget' ) ) :

	/**
	 * Social widget class.
	 *
	 * @since 1.0.0
	 */
	class X_Store_Social_Widget extends WP_Widget {

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		function __construct() {
			$opts = array(
				'classname'   => 'x-store-social social-widgets',
				'description' => esc_html__( 'Widget to display social links with icon', 'x-store' ),
			);
			parent::__construct( 'x-store-social', esc_html__( 'x-Store: Social', 'x-store' ), $opts );
		}

		/**
		 * Echo the widget content.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args     Display arguments including before_title, after_title,
		 *                        before_widget, and after_widget.
		 * @param array $instance The settings for the particular instance of the widget.
		 */
		function widget( $args, $instance ) {

			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

			echo $args['before_widget'];

			if ( ! empty( $title ) ) {
				echo $args['before_title'] . esc_html( $title ). $args['after_title'];
			}

			if ( has_nav_menu( 'social' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'social',
					'link_before'    => '<span class="screen-reader-text">',
					'link_after'     => '</span>',
				) );
			}

			echo $args['after_widget'];

		}

		/**
		 * Update widget instance.
		 *
		 * @since 1.0.0
		 *
		 * @param array $new_instance New settings for this instance as input by the user via
		 *                            {@see WP_Widget::form()}.
		 * @param array $old_instance Old settings for this instance.
		 * @return array Settings to save or bool false to cancel saving.
		 */
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;

			$instance['title'] = sanitize_text_field( $new_instance['title'] );

			return $instance;
		}

		/**
		 * Output the settings update form.
		 *
		 * @since 1.0.0
		 *
		 * @param array $instance Current settings.
		 * @return void
		 */
		function form( $instance ) {

			$instance = wp_parse_args( (array) $instance, array(
				'title' => '',
			) );
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'x-store' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
			</p>

			<?php if ( ! has_nav_menu( 'social' ) ) : ?>
	        <p>
				<?php esc_html_e( 'Social menu is not set. Please create menu and assign it to Social Link Location.', 'x-store' ); ?>
	        </p>
	        <?php endif; ?>
			<?php
		}
	}

endif;


if ( ! class_exists( 'X_Store_Latest_News_Widget' ) ) :

	/**
	 * Latest News widget class.
	 *
	 * @since 1.0.0
	 */
	class X_Store_Latest_News_Widget extends WP_Widget {

	    function __construct() {
	    	$opts = array(
				'classname'   => 'x-store-latest-news blog-section grey-bg',
				'description' => esc_html__( 'Widget to display latest news and posts with thumbnail', 'x-store' ),
    		);

			parent::__construct( 'x-store-latest-news', esc_html__( 'x-Store: Latest News', 'x-store' ), $opts );
	    }


	    function widget( $args, $instance ) {

			$title             	= apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

			$post_category     	= ! empty( $instance['post_category'] ) ? $instance['post_category'] : 0;

			$exclude_categories = !empty( $instance[ 'exclude_categories' ] ) ? esc_attr( $instance[ 'exclude_categories' ] ) : '';

			$excerpt_length 	= !empty( $instance['excerpt_length'] ) ? $instance['excerpt_length'] : '';

			$disable_author  	= ! empty( $instance['disable_author'] ) ? $instance['disable_author'] : 0;

			$disable_date   	= ! empty( $instance['disable_date'] ) ? $instance['disable_date'] : 0;

	        echo $args['before_widget']; 

    		if ( !empty( $title ) ){ ?>

    			<div class="section-title">

    		        <h2><?php echo esc_html( $title ); ?></h2>

    	        </div>
            	<?php 
            } ?>

	        <div class="blog-wrapper blog-col-3">

		        <?php

		        $query_args = array(
					        	'posts_per_page' 		=> 3,
					        	'no_found_rows'  		=> true,
					        	'post__not_in'          => get_option( 'sticky_posts' ),
					        	'ignore_sticky_posts'   => true,
				        	);

		        if ( absint( $post_category ) > 0 ) {

		        	$query_args['cat'] = absint( $post_category );
		        	
		        }

		        if ( !empty( $exclude_categories ) ) {

		        	$exclude_ids = explode(',', $exclude_categories);

		        	$query_args['category__not_in'] = $exclude_ids;

		        }

		        $all_posts = new WP_Query( $query_args );

		        if ( $all_posts->have_posts() ) :?>

			        <div class="inner-wrapper">

						<?php 

						while ( $all_posts->have_posts() ) :

                            $all_posts->the_post(); ?>

                            	<div class="blog-item">
                            	     <div class="blog-inner">
                            	     	 <?php if ( has_post_thumbnail() ) :  ?>
	                            	         <div class="blog-thumb">
	                            	            <?php the_post_thumbnail( 'x-store-news' ); ?>
	                            	         </div>
                            	         <?php endif; ?>

                            	         <div class="blog-text-wrap">
                            	            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                            	            <?php 

                            	            if( 1 != $disable_author ){ ?>

                            	             	<span class="byline"><?php the_author(); ?></span>

                            	             	<?php 
                            	            } 

                            	            if( 1 != $disable_date ){ ?>
                            	             	<span class="posted-on"><?php echo esc_html( get_the_date() ); ?></span>
                            	             	<?php 
                            	            }

                            	            $blog_content = x_store_get_the_excerpt( absint($excerpt_length) );
                            	             
                            	            echo wp_kses_post($blog_content) ? wpautop( wp_kses_post($blog_content) ) : '';

                            	           	?>
                            	         </div>
                            	     </div> <!-- .blog-inner -->
                            	</div> <!-- .blog-item -->

			                <?php 

						endwhile; 

                        wp_reset_postdata(); ?>

                    </div>

		        <?php endif; ?>

	        </div><!-- .latest-news-widget -->

	        <?php
	        echo $args['after_widget'];

	    }

	    function update( $new_instance, $old_instance ) {
	        $instance = $old_instance;
			$instance['title']          	= sanitize_text_field( $new_instance['title'] );
			$instance['post_category']  	= absint( $new_instance['post_category'] );
			$instance['exclude_categories'] = sanitize_text_field( $new_instance['exclude_categories'] );
			$instance['excerpt_length']  	= absint( $new_instance['excerpt_length'] );
			$instance['disable_author']    	= (bool) $new_instance['disable_author'] ? 1 : 0;
			$instance['disable_date']    	= (bool) $new_instance['disable_date'] ? 1 : 0;

	        return $instance;
	    }

	    function form( $instance ) {

	        $instance = wp_parse_args( (array) $instance, array(
				'title'          		=> '',
				'post_category'  		=> '',
				'exclude_categories' 	=> '',
				'excerpt_length'  		=> 12,
				'disable_author'   		=> 0,
				'disable_date'   		=> 0,
	        ) );
	        ?>
	        <p>
	          <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><strong><?php esc_html_e( 'Title:', 'x-store' ); ?></strong></label>
	          <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
	        </p>
	        <p>
	          <label for="<?php echo  esc_attr( $this->get_field_id( 'post_category' ) ); ?>"><strong><?php esc_html_e( 'Select Category:', 'x-store' ); ?></strong></label>
				<?php
	            $cat_args = array(
	                'orderby'         => 'name',
	                'hide_empty'      => 0,
	                'class' 		  => 'widefat',
	                'taxonomy'        => 'category',
	                'name'            => $this->get_field_name( 'post_category' ),
	                'id'              => $this->get_field_id( 'post_category' ),
	                'selected'        => absint( $instance['post_category'] ),
	                'show_option_all' => esc_html__( 'All Categories','x-store' ),
	              );
	            wp_dropdown_categories( $cat_args );
				?>
	        </p>
            <p>
            	<label for="<?php echo esc_attr( $this->get_field_id( 'exclude_categories' ) ); ?>"><strong><?php esc_html_e( 'Exclude Categories:', 'x-store' ); ?></strong></label>
            	<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'exclude_categories' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'exclude_categories' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['exclude_categories'] ); ?>" />
    	        <small>
    	        	<?php esc_html_e('Enter category id seperated with comma. Posts from these categories will be excluded from latest post listing.', 'x-store'); ?>
    	        </small>
            </p>
            <p>
            	<label for="<?php echo esc_attr( $this->get_field_id( 'excerpt_length' ) ); ?>"><strong><?php esc_html_e( 'Excerpt Length:', 'x-store' ); ?></strong></label>
            	<input class="small" id="<?php echo esc_attr( $this->get_field_id( 'excerpt_length' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'excerpt_length' ) ); ?>" type="number" value="<?php echo esc_attr( $instance['excerpt_length'] ); ?>" />
            </p>
	        <p>
	            <input class="checkbox" type="checkbox" <?php checked( $instance['disable_author'] ); ?> id="<?php echo $this->get_field_id( 'disable_author' ); ?>" name="<?php echo $this->get_field_name( 'disable_author' ); ?>" />
	            <label for="<?php echo $this->get_field_id( 'disable_author' ); ?>"><?php esc_html_e( 'Hide Post Author', 'x-store' ); ?></label>
	        </p>
	        <p>
	            <input class="checkbox" type="checkbox" <?php checked( $instance['disable_date'] ); ?> id="<?php echo $this->get_field_id( 'disable_date' ); ?>" name="<?php echo $this->get_field_name( 'disable_date' ); ?>" />
	            <label for="<?php echo $this->get_field_id( 'disable_date' ); ?>"><?php esc_html_e( 'Hide Posted Date', 'x-store' ); ?></label>
	        </p>
	        <?php
	    }

	}

endif;

if ( ! class_exists( 'X_Store_CTA_Widget' ) ) :

	/**
	 * CTA widget class.
	 *
	 * @since 1.0.0
	 */
	class X_Store_CTA_Widget extends WP_Widget {

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		function __construct() {
			$opts = array(
				'classname'   => 'x-store-cta advanced-cta-section overlay',
				'description' => esc_html__( 'Call to Action widget with content and parallax background image', 'x-store' ),
			);
			parent::__construct( 'x-store-cta', esc_html__( 'x-Store: CTA', 'x-store' ), $opts );
		}

		/**
		 * Echo the widget content.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args     Display arguments including before_title, after_title,
		 *                        before_widget, and after_widget.
		 * @param array $instance The settings for the particular instance of the widget.
		 */
		function widget( $args, $instance ) {

			$cta_page    = !empty( $instance['cta_page'] ) ? $instance['cta_page'] : ''; 

			$button_text = ! empty( $instance['button_text'] ) ? esc_html( $instance['button_text'] ) : '';

			$button_url  = ! empty( $instance['button_url'] ) ? esc_url( $instance['button_url'] ) : '';

			if ( ! empty( $cta_page ) ) {

				$bg_image_array = wp_get_attachment_image_src( get_post_thumbnail_id( $cta_page ), 'full' );

				$bg_image = $bg_image_array[0];

				// Add background image.
				if ( ! empty( $bg_image ) ) {
					$background_style = '';
					$background_style .= ' style="background-image:url(' . esc_url( $bg_image ) . ');" ';
					$args['before_widget'] = implode( $background_style . ' ' . 'class="bg_enabled ', explode( 'class="', $args['before_widget'], 2 ) );
				}

			}

			echo $args['before_widget']; 

			if ( $cta_page ) { 

				$cta_args = array(
								'posts_per_page' => 1,
								'page_id'	     => absint( $cta_page ),
								'post_type'      => 'page',
								'post_status'  	 => 'publish',
							);

				$cta_query = new WP_Query( $cta_args );	

				if( $cta_query->have_posts()){

					while( $cta_query->have_posts()){

						$cta_query->the_post(); ?>

						<div class="cta-content">

							<div class="cta-text">
								
								<h2><?php the_title(); ?></h2>

								<?php the_content(); ?>

							</div>

							<?php
							
							if ( ! empty( $button_text ) ) : ?>
								<div class="cta-button">
									<a href="<?php echo esc_url( $button_url ); ?>" class="button"><?php echo esc_attr( $button_text ); ?></a>
								</div>
							<?php endif; ?>
						</div>

						<?php

					}

					wp_reset_postdata();

				} 
			} 

			echo $args['after_widget'];

		}

		/**
		 * Update widget instance.
		 *
		 * @since 1.0.0
		 *
		 * @param array $new_instance New settings for this instance as input by the user via
		 *                            {@see WP_Widget::form()}.
		 * @param array $old_instance Old settings for this instance.
		 * @return array Settings to save or bool false to cancel saving.
		 */
		function update( $new_instance, $old_instance ) {

			$instance = $old_instance;

			$instance['cta_page'] 	 	= absint( $new_instance['cta_page'] );

			$instance['button_text'] 	= sanitize_text_field( $new_instance['button_text'] );
			
			$instance['button_url']  	= esc_url_raw( $new_instance['button_url'] );

			return $instance;
		}

		/**
		 * Output the settings update form.
		 *
		 * @since 1.0.0
		 *
		 * @param array $instance Current settings.
		 */
		function form( $instance ) {

			$instance = wp_parse_args( (array) $instance, array(
				'cta_page'    			=> '',
				'button_text' 			=> esc_html__( 'Find More', 'x-store' ),
				'button_url'  			=> '',
			) ); 

			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'cta_page' ); ?>">
					<strong><?php esc_html_e( 'CTA Page:', 'x-store' ); ?></strong>
				</label>
				<?php
				wp_dropdown_pages( array(
					'id'               => $this->get_field_id( 'cta_page' ),
					'class'            => 'widefat',
					'name'             => $this->get_field_name( 'cta_page' ),
					'selected'         => $instance[ 'cta_page' ],
					'show_option_none' => esc_html__( '&mdash; Select &mdash;', 'x-store' ),
					)
				);
				?>
				<small>
		        	<?php esc_html_e('Title, Content and Featured Image of this page will be used as content of CTA', 'x-store'); ?>
		        </small>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>"><strong><?php esc_html_e( 'Button Text:', 'x-store' ); ?></strong></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['button_text'] ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'button_url' ) ); ?>"><strong><?php esc_html_e( 'Button URL:', 'x-store' ); ?></strong></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_url' ) ); ?>" type="text" value="<?php echo esc_url( $instance['button_url'] ); ?>" />
			</p>

		<?php
		} 
	
	}

endif;

if ( ! class_exists( 'X_Store_Features_Widget' ) ) :

	/**
	 * Features widget class.
	 *
	 * @since 1.0.0
	 */
	class X_Store_Features_Widget extends WP_Widget {

		function __construct() {
			$opts = array(
					'classname'   => 'x-store-features about-us-section grey-bg',
					'description' => esc_html__( 'Widget to display features with icon', 'x-store' ),
			);
			parent::__construct( 'x-store-features', esc_html__( 'x-Store: Features', 'x-store' ), $opts );
		}

		function widget( $args, $instance ) {

			$excerpt_length	= !empty( $instance['excerpt_length'] ) ? $instance['excerpt_length'] : 20;

			$disable_link  	= ! empty( $instance['disable_link'] ) ? $instance['disable_link'] : 0;

			$features_ids 	= array();

			$item_number 	= 5;

			for ( $i = 1; $i <= $item_number; $i++ ) {
				if ( ! empty( $instance["item_id_$i"] ) && absint( $instance["item_id_$i"] ) > 0 ) {
					$id = absint( $instance["item_id_$i"] );
					$features_ids[ $id ]['id']   = $id;
					$features_ids[ $id ]['icon'] = $instance["item_icon_$i"];
				}
			}

			$feature_pic  = ! empty( $instance['feature_pic'] ) ? esc_url( $instance['feature_pic'] ) : '';

			echo $args['before_widget']; ?>

			<div class="about-us-wrapper">

				<div class="company-key-infos">
					
					<?php

					if ( ! empty( $features_ids ) ) {
						$query_args = array(
							'posts_per_page' => count( $features_ids ),
							'post__in'       => wp_list_pluck( $features_ids, 'id' ),
							'orderby'        => 'post__in',
							'post_type'      => 'page',
							'no_found_rows'  => true,
						);
						$all_features = get_posts( $query_args ); ?>

						<?php if ( ! empty( $all_features ) ) : ?>
							<?php global $post; ?>
							
								<?php foreach ( $all_features as $post ) : ?>
									<?php setup_postdata( $post ); ?>

									<div class="key-info-item">

									    <div class="info-inner">

									        <div class="info-icon">
									            <span class="<?php echo esc_attr( $features_ids[ $post->ID ]['icon'] ); ?>"></span>
									        </div> <!-- .feature-icon -->

									        <div class="key-info-text-wrapper">
									            
								            	<?php

								            	if( 1 === $disable_link){ ?>

								            		<h2><?php the_title(); ?></h2>

								            		<?php

								            	}else{ ?>

									            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

									              	<?php 
									            } 
									            
									            $content = x_store_get_the_excerpt( absint( $excerpt_length ), $post );
									            
									            echo $content ? wpautop( wp_kses_post( $content ) ) : ''; 
									            ?>
									        </div>

									    </div> <!-- .features-inner -->

									</div> <!-- .key-info-item -->

								<?php endforeach; ?>

							<?php wp_reset_postdata(); ?>

						<?php endif;
					} ?>

				</div><!-- .company-key-infos -->

				<?php if ( ! empty( $feature_pic ) ) : ?>

					<div class="image-holder">
					    <img src="<?php echo esc_url( $feature_pic ); ?>" alt="<?php esc_attr_e('feature image', 'x-store'); ?>"/>
					</div> <!-- .image-holder -->

				<?php endif; ?>

			</div>

			<?php

			echo $args['after_widget'];

		}

		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;

			$instance['excerpt_length'] = absint( $new_instance['excerpt_length'] );

			$instance['disable_link']   = (bool) $new_instance['disable_link'] ? 1 : 0;

			$item_number = 5;

			for ( $i = 1; $i <= $item_number; $i++ ) {
				$instance["item_id_$i"]   = absint( $new_instance["item_id_$i"] );
				$instance["item_icon_$i"] = sanitize_text_field( $new_instance["item_icon_$i"] );
			}

			$instance['feature_pic']  	  = esc_url_raw( $new_instance['feature_pic'] );

			return $instance;
		}

		function form( $instance ) {

			// Defaults.
			$defaults = array(
							'excerpt_length'	=> 20,
							'disable_link'   	=> 1,
							'feature_pic'       => '',
						);

			$item_number = 5;

			for ( $i = 1; $i <= $item_number; $i++ ) {
				$defaults["item_id_$i"]   = '';
				$defaults["item_icon_$i"] = 'icon-pencil';
			}

			$feature_pic = '';

            if ( ! empty( $instance['feature_pic'] ) ) {

                $feature_pic = $instance['feature_pic'];

            }

            $wrap_style = '';

            if ( empty( $feature_pic ) ) {

                $wrap_style = ' style="display:none;" ';
            }

            $image_status = false;

            if ( ! empty( $feature_pic ) ) {
                $image_status = true;
            }

            $delete_button = 'display:none;';

            if ( true === $image_status ) {
                $delete_button = 'display:inline-block;';
            }

			$instance = wp_parse_args( (array) $instance, $defaults );
			?>
			
			<p>
				<label for="<?php echo esc_attr( $this->get_field_name('excerpt_length') ); ?>">
					<?php esc_html_e('Excerpt Length:', 'x-store'); ?>
				</label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('excerpt_length') ); ?>" name="<?php echo esc_attr( $this->get_field_name('excerpt_length') ); ?>" type="number" value="<?php echo absint( $instance['excerpt_length'] ); ?>" />
			</p>

			<p>
			    <input class="checkbox" type="checkbox" <?php checked( $instance['disable_link'] ); ?> id="<?php echo $this->get_field_id( 'disable_link' ); ?>" name="<?php echo $this->get_field_name( 'disable_link' ); ?>" />
			    <label for="<?php echo $this->get_field_id( 'disable_link' ); ?>"><?php esc_html_e( 'Disable link to detail page', 'x-store' ); ?></label>
			</p>

	        <p>
		        <small>
		        	
		        	<?php /*printf( esc_html__( '%1$s %2$s', 'x-store' ), 'ICONS ET-LINE is used for icon of each feature. You can find icon code', '<a href="http://rhythm.nikadevs.com/content/icons-et-line" target="_blank">here</a>' ); */?>
		        	<?php _e('ICONS ET-LINE is used for icon of each feature. You can find icon code', 'x-store') ?>
                        <a href="<?php echo esc_url(__('http://rhythm.nikadevs.com/content/icons-et-line', 'x-store')); ?>">
                            <?php _e('here', 'x-store'); ?>
                        </a>
		        </small>
	        </p>

			<?php
				for ( $i = 1; $i <= $item_number; $i++ ) {
					?>
					<hr>
					<p>
						<label for="<?php echo $this->get_field_id( "item_id_$i" ); ?>"><strong><?php esc_html_e( 'Page:', 'x-store' ); ?>&nbsp;<?php echo $i; ?></strong></label>
						<?php
						wp_dropdown_pages( array(
							'id'               => $this->get_field_id( "item_id_$i" ),
							'class'            => 'widefat',
							'name'             => $this->get_field_name( "item_id_$i" ),
							'selected'         => $instance["item_id_$i"],
							'show_option_none' => esc_html__( '&mdash; Select &mdash;', 'x-store' ),
							)
						);
						?>
					</p>
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( "item_icon_$i" ) ); ?>"><strong><?php esc_html_e( 'Icon:', 'x-store' ); ?>&nbsp;<?php echo $i; ?></strong></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( "item_icon_$i" ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( "item_icon_$i" ) ); ?>" type="text" value="<?php echo esc_attr( $instance["item_icon_$i"] ); ?>" />
					</p>
					<?php 
				} ?>

				<div class="cover-image">
	                <label for="<?php echo esc_attr( $this->get_field_id( 'feature_pic' ) ); ?>">
	                    <strong><?php esc_html_e( 'Feature Image:', 'x-store' ); ?></strong>
	                </label>
	                <input type="text" class="img widefat" name="<?php echo esc_attr( $this->get_field_name( 'feature_pic' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'feature_pic' ) ); ?>" value="<?php echo esc_url( $instance['feature_pic'] ); ?>" />
	                <div class="rtam-preview-wrap" <?php echo $wrap_style; ?>>
	                    <img src="<?php echo esc_url( $feature_pic ); ?>" alt="<?php esc_attr_e( 'Preview', 'x-store' ); ?>" />
	                </div><!-- .rtam-preview-wrap -->
	                <input type="button" class="select-img button button-primary" value="<?php esc_html_e( 'Upload', 'x-store' ); ?>" data-uploader_title="<?php esc_html_e( 'Select Background Image', 'x-store' ); ?>" data-uploader_button_text="<?php esc_html_e( 'Choose Image', 'x-store' ); ?>" />
	                <input type="button" value="<?php echo esc_attr_x( 'X', 'Remove Button', 'x-store' ); ?>" class="button button-secondary btn-image-remove" style="<?php echo esc_attr( $delete_button ); ?>" />
	            </div>

			<?php
		}
	}

endif;