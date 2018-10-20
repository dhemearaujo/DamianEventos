<?php 
/**
 * Override Class
 *
 * @package Woo Mumiti Layout admin
 * @author Saiful ( edatastyle )
 * @since 1.0
 */
defined('ABSPATH') || die('No direct script access allowed!');
if( !class_exists( 'Woo_Mulit_Layout_Override' ) ){
	class Woo_Mulit_Layout_Override{
		 /**
		 * @var striang
		 */
		private $optionsName;
		
		 /**
		 * @var array
		 */
		private $options;
		/**
		 *  Init Loading
		 */
		public function __construct(){
			$this->optionsName = 'woo_mulit_layout_options';
		
			if( !get_option( $this->optionsName, false ) ) return false;
			
			$this->options = get_option($this->optionsName);
			
			 add_filter( 'term_link', array( $this, 'term_link' ), 12, 3 );
			 add_action( 'template_redirect', array( $this, 'template_redirect' ),1 );
		}
		
		public function term_link($link, $term, $taxonomy){
			if ( $term->taxonomy === 'product_cat' && isset( $this->options[$this->optionsName]['category'] ) && $this->options[$this->optionsName]['category'] != 0) {
				$redirect_to = add_query_arg( array('product_cat'=>$term->slug), get_permalink( $this->options[$this->optionsName]['category'] ) );
				if ( !empty( $redirect_to ) ) return $redirect_to;
				
			}	
			if ( $term->taxonomy === 'product_tag' && isset( $this->options[$this->optionsName]['tags'] ) && $this->options[$this->optionsName]['tags'] != 0) {
				$redirect_to = add_query_arg( array( 'product_tag' => $term->slug ), get_permalink( $this->options[$this->optionsName]['tags'] ) );
				if ( !empty( $redirect_to ) ) return $redirect_to;
				
			}	
			 return $link;
		}
		
		public function template_redirect(){
			 global $wp_query,$woocommerce,$wp,$_chosen_attributes;
			
			  $posttype = $wp_query->query_vars['post_type'];
			  
			  if ( $posttype === 'product' && !is_single() && isset( $this->options[$this->optionsName]['shop'] ) && $this->options[$this->optionsName]['shop'] != 0 ) {
					$redirect = get_permalink( $this->options[$this->optionsName]['shop'] );
			 } elseif ( $posttype === 'product' && is_search() && isset( $this->options[$this->optionsName]['search'] ) && $this->options[$this->optionsName]['search'] != 0 ){
				$query_args=array( 'edsearch' => $_GET['s']);
				$redirect = add_query_arg( $query_args, get_permalink( $this->options[$this->optionsName]['search'] ) );
		   
			 }
			  if(isset($redirect)){
				 wp_redirect($redirect);
			 }
		}
		
		
	}
	
	new Woo_Mulit_Layout_Override();	
}