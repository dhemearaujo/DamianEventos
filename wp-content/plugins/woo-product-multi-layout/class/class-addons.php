<?php
/**
 * Addons Class
 *
 * @package Woo Mumiti Layout admin
 * @author Saiful ( edatastyle )
 * @since 1.0
 */
 // Prohibit direct script loading.
defined('ABSPATH') || die('No direct script access allowed!');
if( !class_exists('Woo_Mulit_Layout_Addons') ){
	class Woo_Mulit_Layout_Addons {
		/**
		 * @var striang
		 */
		protected $path;
		/**
		 * @var striang
		 */
		protected $url;
		
		/**
		 * Initialize all controllers, by loading Plugin and User Options.
		 */
		public function __construct() {
			$this->path = ED_MULTI_PATH;
			$this->url = ED_MULTI_URL;
			add_action( 'vc_before_init', array( $this, 'vc_map' ) );
		} 
		/**
		 * Initialize visual composer
		 */
		public function vc_map(){
			$args = array(
				'posts_per_page'   => -1,
				'post_type'        => 'woo_multii_layout'
				);
			$posts = get_posts( $args );
			$array = array();
			if( count( $posts ) > 0 ){
				foreach ( $posts as $row ){
					$array[ $row->post_title ] = $row->ID;
					//$array[ $row->ID ] = $row->post_title;	
				}
			}
			 vc_map( array(
				"name" => __('Woo Product Multi Layout', 'ED_MULTI_LANG'),
				"base" => "woo_mulit_layout",
				"class" => "",
				"icon"=>$this->url.'/assets/admin/images/logo.svg',
				"params" => array(
					array(
						"type" => "dropdown",
						"holder" => "div",
						"class" => "",
						'admin_label' => true,
						"heading" => __("Select Layout",'ED_MULTI_LANG'),
						"param_name" => "id",
						"value" => $array
					)
				)
			 ) );	
		}
		
	}
	new Woo_Mulit_Layout_Addons();
}