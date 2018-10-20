<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package x-Store
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<meta name="format-detection" content="telephone=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">

	<header id="masthead" class="site-header">

		<?php 
		// For top header
		$header_status = x_store_get_option( 'show_top_header' );
		
		if ( 1 == $header_status ) {
		
			$top_address    = x_store_get_option( 'top_address' );
			$top_phone      = x_store_get_option( 'top_phone' );
			$top_email      = x_store_get_option( 'top_email' );

			$left_section  	= x_store_get_option( 'left_section' );
			$right_section  = x_store_get_option( 'right_section' );

			?>
		    <div class="top-header">

		        <div class="container">

		            <div class="top-header-content">
		                
		                <div class="top-info-left">

		                	<?php 
		                	if( 'contact' == $left_section && ( !empty( $top_address ) || !empty( $top_phone ) || !empty( $top_email ) ) ){ ?>

		                	    <div class="top-contact-info">
		                	        <?php if( !empty( $top_address ) ){ ?>
		                	            <span class="address"><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_html( $top_address ); ?></span>
		                	        <?php } ?>

		                	        <?php if( !empty( $top_phone ) ){ ?>
		                	            <span class="phone"><i class="fa fa-phone" aria-hidden="true"></i> <?php echo esc_html( $top_phone ); ?></span>
		                	        <?php } ?>

		                	        <?php if( !empty( $top_email ) ){ ?>
		                	            <span class="email"><i class="fa fa-envelope-o" aria-hidden="true"></i> <?php echo esc_html( $top_email ); ?></span>
		                	        <?php } ?>
		                	        
		                	    </div>
		                	    <?php
		                	} elseif( 'top-menu' == $left_section && has_nav_menu( 'top' ) ){ ?>
		                	    <div class="top-menu-warp">
		                	        <?php
		                	        wp_nav_menu(
		                	            array(
		                	            'theme_location' => 'top',
		                	            'menu_id'        => 'top-menu',
		                	            'depth'          => 1,                                   
		                	            )
		                	        ); ?>
		                	    </div><!-- .menu-content -->
		                	    <?php
		                	} elseif( 'top-social' == $left_section && has_nav_menu( 'social' ) ){ ?>

		                	    <div class="top-social-menu-container"> 

		                	        <?php the_widget( 'X_Store_Social_Widget' ); ?>

		                	    </div>
		                	    <?php
		                	} ?>
		                </div><!-- .top-info-left -->

		                <div class="top-info-right">

		                	<?php 
		                	if( 'contact' == $right_section && ( !empty( $top_address ) || !empty( $top_phone ) || !empty( $top_email ) ) ){ ?>

		                	    <div class="top-contact-info">
		                	        <?php if( !empty( $top_address ) ){ ?>
		                	            <span class="address"><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_html( $top_address ); ?></span>
		                	        <?php } ?>

		                	        <?php if( !empty( $top_phone ) ){ ?>
		                	            <span class="phone"><i class="fa fa-phone" aria-hidden="true"></i> <?php echo esc_html( $top_phone ); ?></span>
		                	        <?php } ?>

		                	        <?php if( !empty( $top_email ) ){ ?>
		                	            <span class="email"><i class="fa fa-envelope-o" aria-hidden="true"></i> <?php echo esc_html( $top_email ); ?></span>
		                	        <?php } ?>
		                	        
		                	    </div>
		                	    <?php
		                	} elseif( 'top-menu' == $right_section && has_nav_menu( 'top' ) ){ ?>
		                	    <div class="top-menu">
		                	        <?php
		                	        wp_nav_menu(
		                	            array(
		                	            'theme_location' => 'top',
		                	            'menu_id'        => 'top-menu',
		                	            'depth'          => 1,                                   
		                	            )
		                	        ); ?>
		                	    </div><!-- .menu-content -->
		                	    <?php
		                	} elseif( 'top-social' == $right_section && has_nav_menu( 'social' ) ){ ?>

		                	    <div class="top-social-menu-container"> 

		                	        <?php the_widget( 'X_Store_Social_Widget' ); ?>

		                	    </div>
		                	    <?php
		                	} ?>
		                </div><!-- .top-info-right -->

		           </div><!-- .top-header-content --> 

		        </div> <!-- .container -->

		    </div> <!-- .top-header -->

	    <?php } ?>

	    <div class="bottom-header">
	        
	        <div class="container">

            	<div class="site-branding">
            		<?php 

                    $site_identity = x_store_get_option( 'site_identity' ); 

                    if( 'logo-only' == $site_identity ){  

                        x_store_the_custom_logo(); 

                    }else{ ?>

                        <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>

                		<?php
                		$description = get_bloginfo( 'description', 'display' );

                        if ( $description || is_customize_preview() ) : ?>

                            <p class="site-description"><?php echo $description; /* WPCS: xss ok. */ ?></p>

                            <?php
                        endif; 
            		} ?>
            	</div><!-- .site-branding -->

            	<?php

            	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) { ?>

		            <div class="product-search-wrapper">
		            	
		            		<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		            			<input type="hidden" name="post_type" value="product" />

		            			<input type="text" class="search-field products-search" placeholder="<?php echo esc_attr_x( 'Search Products &hellip;', 'placeholder', 'x-store' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />

		            			<select class="product-cat" name="product_cat">

		            				<option value=""><?php esc_html_e( 'All Categories', 'x-store' ); ?></option> 

		            				<?php

		            				$categories = get_categories( 'taxonomy=product_cat' );

		            				foreach ( $categories as $category ) {

		            					$option = '<option value="' . esc_attr( $category->category_nicename ) . '"'.x_store_category_selected( $category->category_nicename ).'>';

		            					$option .= esc_html( $category->cat_name );

		            					$option .= ' (' . absint( $category->category_count ) . ')';
		            					
		            					$option .= '</option>';

		            					echo $option;

		            				} ?>

		            			</select>
		            			
		            			<button type="submit" class="search-submit"><span class="screen-reader-text"><?php echo _x( 'Search', 'submit button', 'x-store' ); ?></span><i class="fa fa-search" aria-hidden="true"></i></button>
		            		</form>

		            		<div class="header-cart">
		            			<span class="cart-value"><?php echo wp_kses_post( WC()->cart->get_cart_total() ); ?></span>
		            			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>">
		            				<i class="fa fa-shopping-bag" aria-hidden="true"></i>
		            				<span class="cart-items"><?php echo absint( WC()->cart->get_cart_contents_count() ); ?></span>
		            			</a>
		            		</div>
		            </div> <!-- .product-search-wrapper -->
	            <?php } ?>

	        </div> <!-- .container -->

	    </div> <!-- .bottom-header -->

	    <div class="main-nav-holder">
	        
	        <div class="container">

	            <div class="main-navigation-wrapper">
                    <div id="main-nav" class="clear-fix">
                        <nav id="site-navigation" class="main-navigation" role="navigation">
                            <div class="wrap-menu-content">
                				<?php
                				wp_nav_menu(
                					array(
                					'theme_location' => 'primary',
                					'menu_id'        => 'primary-menu',
                					'fallback_cb'    => 'x_store_primary_navigation_fallback',
                					)
                				);
                				?>
                            </div><!-- .menu-content -->
                        </nav><!-- #site-navigation -->
                    </div> <!-- #main-nav -->

	            </div> <!-- .main-navigation-wrapper -->

	        </div> <!-- .container -->

	    </div> <!-- .main-nav-holder -->

	</header><!-- #masthead -->

	<?php get_template_part( 'template-parts/slider' ); ?>

	<?php get_template_part( 'template-parts/home-widgets' ); ?>

	<?php get_template_part( 'template-parts/banner' ); ?>

	<div id="content" class="site-content">

		<div class="container">
			<div class="inner-wrapper">