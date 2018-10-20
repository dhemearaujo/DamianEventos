<?php
/**
 * Options Class
 *
 * @package Woo Mumiti Layout admin
 * @author Saiful ( edatastyle )
 * @since 1.0
 */
defined('ABSPATH') || die('No direct script access allowed!');
if ( !class_exists('Woo_Mulit_Layout_Options_Maker')){
	class Woo_Mulit_Layout_Options_Maker{
		 /**
		 * @var striang
		 */
		private $options;
		/**
		 *  Init Loading
		 */
		public function __construct(){
			$this->options = 'woo_mulit_layout_options';
			add_action('admin_menu', array(
				$this,
				'admin_menu'
			));
			add_action('admin_init', array(
				$this,
				'settings_init'
			));
	
			// add_action( 'admin_notices', array( $this, 'admin_notices' ), 99 );
	 		add_filter( 'plugin_action_links_' . ED_MULTI_FILE, array( $this, 'woo_action_links' ) );
			
		}
		/**
		 * Register admin settings menu
		 * @return void
		 */
		public function admin_menu(){
			add_submenu_page('edit.php?post_type=woo_multii_layout', 'Settings', 'Settings', 'manage_options', 'woo_mulit_layout_options', array(
				$this,
				'options_pages'
			));
		}
		/**
		 * Register settings input
		 * @return void
		 */
		public function settings_init(){
			$ed_options = get_option($this->options);
			$settings_name = $this->options;
			register_setting($settings_name, $this->options);
			add_settings_section($this->options . '_section', '', array(
				$this,
				'WC_VAR_settings_section_callback'
			) , $settings_name
			/* action recieve */);
			
			add_settings_field($this->options . '_shop', __('Shop Page <span>Redirect WooCommerce Shop page to display product for woocommerce page.</span>', 'WC_VAR_LANG') , array(
				$this,
				'select_box'
			) , $settings_name, $this->options . '_section', $args = array(
				'name' => 'shop',
				'options' => $this->get_list_of_pages(),
				'settings' => $settings_name,
			));
			
			add_settings_field($this->options . '_category', __('Category Page <span>Redirect WooCommerce Category page to display product for woocommerce page.</span>', 'WC_VAR_LANG') , array(
				$this,
				'select_box'
			) , $settings_name, $this->options . '_section', $args = array(
				'name' => 'category',
				'options' => $this->get_list_of_pages(),
				'settings' => $settings_name,
			));
			
			add_settings_field($this->options . '_tags', __('Tag Page <span>Redirect WooCommerce Tag page to display product for woocommerce page.</span>', 'WC_VAR_LANG') , array(
				$this,
				'select_box'
			) , $settings_name, $this->options . '_section', $args = array(
				'name' => 'tags',
				'options' => $this->get_list_of_pages(),
				'settings' => $settings_name,
			));
			
			add_settings_field($this->options . '_search', __('Search Page <span>Redirect WooCommerce Search page to display product for woocommerce page.</span>', 'WC_VAR_LANG') , array(
				$this,
				'select_box'
			) , $settings_name, $this->options . '_section', $args = array(
				'name' => 'search',
				'options' => $this->get_list_of_pages(),
				'settings' => $settings_name,
			));
			
			
			
		}
		/**
		 * @param array $args
		 * @return html
		 */
		public function select_box(array $args) {
			$options = get_option($this->options);
			$group = (isset($args['group']) && $args['group'] != "") ? $args['group'] : '';
			
			?>
			
				<select class="<?php
						echo $group; ?>" name='<?php
						echo $this->options; ?>[<?php
						echo $args['settings']; ?>][<?php
						echo $args['name']; ?>]'>
				  <option value="0"><?php _e('Disable', 'ED_MULTI_LANG');?></option>      
				  <?php
						if (isset($args['options']) && count($args['options']) > 0):
							foreach($args['options'] as $key => $val):
				?>
				  <option value='<?php
								echo $key; ?>' <?php
								selected($options[$args['settings']][$args['name']], $key); ?>><?php
								echo $val; ?></option>
				  <?php
							endforeach;
						endif; ?>
				</select>
			<?php
		}
		
		public function WC_VAR_settings_section_callback()
			{
			?>
			<?php
			}
		/**
		 * @param array $args
		 * @return html
		 */
		public function options_pages(){
				?>
				<div class="wrap">
				  <div id="wpcom-stats-meta-box-container" class="metabox-holder">
					<form method="post" action="options.php">
					  <div class="postbox-container" style="width:95%;">
						<div id="normal-sortables" class="meta-box-sortables ui-sortable">
						  <div class="postbox" id="<?php echo $this->options;?>">
							<h3 class="hndle"><span><?php _e('Replace WooCommerce Page', 'ED_MULTI_LANG');?></span></h3>
							<div class="inside ed__settings__loops">
								<?php
								$settings_name = $this->options;
								settings_fields($settings_name);
								do_settings_sections($settings_name);
								
								?>
								
							</div>
							<div id="major-publishing-actions">
							  <div id="publishing-action"> <?php echo submit_button(); ?> </div>
							  <div class="clear"></div>
							</div>
						  </div>
						</div>
					  </div>
					</form>
				  </div>
				</div>
				<?php
		}
		/**
		 * 
		 * @return array page 
		 */
		private function get_list_of_pages(){
			$ex_page_id=array();
			$ex_page_id[]=get_option('woocommerce_shop_page_id'); 
			$ex_page_id[]=get_option('woocommerce_cart_page_id'); 
			$ex_page_id[]=get_option('woocommerce_checkout_page_id');
			$ex_page_id[]=get_option(' woocommerce_pay_page_id '); 
			$ex_page_id[]=get_option(' woocommerce_thanks_page_id '); 
			$ex_page_id[]=get_option(' woocommerce_myaccount_page_id '); 
			$ex_page_id[]=get_option(' woocommerce_edit_address_page_id '); 
			$ex_page_id[]=get_option(' woocommerce_view_order_page_id '); 
			$ex_page_id[]=get_option(' woocommerce_terms_page_id '); 
			$ex_page_id[]=get_option(' woocommerce_logout_page_id '); 
			$ex_page_id[]=get_option(' woocommerce_lost_password_page_id '); 
			
			$args = array(
				'posts_per_page'   => -1,
				'orderby'          => 'modified',
				'order'            => 'DESC',
				'post_type'        => 'page',
				'post_status'      => 'publish',
				'exclude'           => $ex_page_id);
			$posts = get_posts( $args );
			return wp_list_pluck( $posts, 'post_title', 'ID');
		}
		public function woo_action_links( $links ){
	
		$plugin_links = array(
			'<a target="_blank" href="' . admin_url( 'edit.php?post_type=woo_multii_layout' ) . '">' . __( 'Settings', 'ED_MULTI_LANG' ) . '</a>',
			'<a target="_blank" href="#">' . __( 'Docs', 'ED_MULTI_LANG' ) . '</a>',
			'<a target="_blank" href="#">' . __( 'Support', 'ED_MULTI_LANG' ) . '</a>'
		);
		

		return array_merge( $plugin_links, $links );
		
		}
		
	}
	
	

	new Woo_Mulit_Layout_Options_Maker();
}


 

 
