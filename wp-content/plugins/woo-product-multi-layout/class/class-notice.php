<?php
/**
 * Admin Class
 *
 * @package Woo Mumiti Layout admin
 * @author Saiful ( edatastyle )
 * @since 1.0
 */
 // Prohibit direct script loading.
defined('ABSPATH') || die('No direct script access allowed!');

if( !class_exists('Single_Post_Meta_Manager') ){
	class Woo_Multi_Layout_Notice {
		/**
		 * Initialize all controllers, by loading Plugin and User Options.
		 */
		public function __construct() {
			 add_action( 'admin_notices', array($this, 'woocommerce_required') );
		}
		
		public function woocommerce_required(){
		?>	
		<div id="message" class="updated displayproduct-message dp-connect">
	<div class="squeezer">
		<p><?php _e( '<strong>Woo Product Multi Layout plugin require the WooCommerce plugin By WooThemes</strong>. If you already  have a  woocommerce please activate them.', 'ED_MULTI_LANG' ); ?></p>
        
         <?php 
                if( !is_plugin_active( 'woocommerce/woocommerce.php' )){?>
                    <p class="submit"><a href="<?php echo add_query_arg('Activated WooCommerce', 'true', admin_url('plugins.php') ); ?>" class="button-primary">Activate WooCommerce</a></p>
                <?php }else{ ?>
                    <p class="submit"><a href="<?php echo add_query_arg('Install WooCommerce', 'true', admin_url('plugin-install.php?tab=search&s=woocommerce') ); ?>" class="button-primary">Install WooCommerce</a></p>
                <?php }?>
                
	</div>
</div>
        <?php	
		}
	}
	
	new Woo_Multi_Layout_Notice();
}