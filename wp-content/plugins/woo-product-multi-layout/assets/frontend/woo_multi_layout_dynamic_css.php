<?php
/**
 * Views Class
 *
 * @package woo_mulit_layout
 * @author Saiful ( edatastyle )
 * @since 1.0.0
 */
// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );
	// Loading Google fonts
	
	$enqueue_fonts  = array();
	$google_fonts   = array();
	if( isset($editor['add_to_cart']['font_family']) && $editor['add_to_cart']['font_family'] != 'none'){
		$google_fonts[] = $editor['add_to_cart']['font_family'];
	}
	if( isset($editor['product_title']['font_family']) && $editor['product_title']['font_family'] != 'none'){
		$google_fonts[] = $editor['product_title']['font_family'];
	}
	if( isset($editor['excerpt']['font_family']) && $editor['excerpt']['font_family'] != 'none'){
		$google_fonts[] = $editor['excerpt']['font_family'];
	}
	if( isset($editor['product_content']['font_family']) && $editor['product_content']['font_family'] != 'none'){
		$google_fonts[] = $editor['product_content']['font_family'];
	}
	if( isset($editor['product_category']['font_family']) && $editor['product_category']['font_family'] != 'none'){
		$google_fonts[] = $editor['product_category']['font_family'];
	}
	if( isset($editor['product_tags']['font_family']) && $editor['product_tags']['font_family'] != 'none'){
		$google_fonts[] = $editor['product_tags']['font_family'];
	}
	if( isset($editor['readmore']['font_family']) && $editor['readmore']['font_family'] != 'none'){
		$google_fonts[] = $editor['readmore']['font_family'];
	}
	
	if ( ! empty( $google_fonts ) ) {
	  foreach ( $google_fonts as $font ) {
		if( isset( $font  ) ) {
		  $enqueue_fonts[] = $font;
		}
	  }
	}
	
	if ( ! empty( $enqueue_fonts ) ) {
	  wp_enqueue_style( 'cs-google-fonts', esc_url( add_query_arg( 'family', urlencode( implode( '|', $enqueue_fonts ) ) , '//fonts.googleapis.com/css' ) ),true);
	}

?>
<!-- Custom Styling -->
<style type="text/css">
	<?php $primary_border = isset( $template['boder_color_size'] ) ? $template['boder_color_size'] : 1; ?>
    #woo__multi__layout__<?php echo $id;?> .dynamic__css__class{
		<?php if( isset( $template['primary'] ) && $template['primary'] != ''){ ?>
		background:<?php echo $template['primary'];?>;
		<?php }?><?php if( isset( $template['boder_color'] ) && $template['boder_color'] != ''){ ?>
		border:<?php echo $primary_border;?>px solid <?php echo $template['boder_color'];?>;
		<?php }?>
	}
	#woo__multi__layout__<?php echo $id;?> .dynamic__css__class:hover{
		<?php if( isset( $template['primary_hover'] ) && $template['primary_hover'] != ''){ ?>
		background:<?php echo $template['primary_hover'];?>;
		<?php }?><?php if( isset( $template['hover_boder_color'] ) && $template['hover_boder_color'] != ''){ ?>
		border:<?php echo $primary_border;?>px solid <?php echo $template['hover_boder_color'];?>;
		<?php }?>
	}
	
	table.shop_table.woo__mulit__layout__table__views thead th{
		<?php if( isset( $template['table_heading_color'] ) && $template['table_heading_color'] != ''){ ?>
		color:<?php echo $template['table_heading_color'];?>;
		<?php }?><?php if( isset( $template['table_heading_bg'] ) && $template['table_heading_bg'] != ''){ ?>
		background:<?php echo $template['table_heading_bg'];?>;
		<?php }?><?php if( isset( $template['table_heading_font_size'] ) && $template['table_heading_font_size'] != ''){ ?>
		font-size:<?php echo $template['table_heading_font_size'];?>px;
		<?php }?>
		
		
	}

<?php if( isset($editor['product_content']) ) :?>
	#woo__multi__layout__<?php echo $id;?>{
		<?php if( isset( $editor['product_content']['font_family'] ) && $editor['product_content']['font_family'] != 'none'){ ?>
		font-family:"<?php echo $editor['product_content']['font_family'];?>", Gadget, sans-serif;
		<?php }?><?php if( isset( $editor['product_content']['font_size'] ) && $editor['product_content']['font_size'] != ''){ ?>
		font-size:<?php echo $editor['product_content']['font_size'];?>px;
		<?php }?><?php if( isset( $editor['product_content']['color'] ) && $editor['product_content']['color'] != ''){ ?>
		color:<?php echo $editor['product_content']['color'];?>;
		<?php }?>
		
	}
<?php else: ?>
	<?php if( isset($editor['excerpt']) ) :?>
	#woo__multi__layout__<?php echo $id;?> .dynamic__css__class{
		<?php if( isset( $editor['excerpt']['font_family'] ) && $editor['excerpt']['font_family'] != 'none'){ ?>
		font-family:"<?php echo $editor['excerpt']['font_family'];?>", Gadget, sans-serif;
		<?php }?><?php if( isset( $editor['excerpt']['font_size'] ) && $editor['excerpt']['font_size'] != ''){ ?>
		font-size:<?php echo $editor['product_content']['font_size'];?>px;
		<?php }?><?php if( isset( $editor['excerpt']['color'] ) && $editor['excerpt']['color'] != ''){ ?>
		color:<?php echo $editor['excerpt']['color'];?>;
		<?php }?>
		
	}
	<?php endif; ?>
<?php endif; ?>
	#woo__multi__layout__<?php echo $id;?> .woo_mulit_excerpt{
		<?php if( isset( $editor['excerpt']['font_family'] ) && $editor['excerpt']['font_family'] != 'none'){ ?>
		font-family:"<?php echo $editor['excerpt']['font_family'];?>", Gadget, sans-serif;
		<?php }?><?php if( isset( $editor['excerpt']['font_size'] ) && $editor['excerpt']['font_size'] != ''){ ?>
		font-size:<?php echo $editor['excerpt']['font_size'];?>px;
		<?php }?><?php if( isset( $editor['excerpt']['color'] ) && $editor['excerpt']['color'] != ''){ ?>
		color:<?php echo $editor['excerpt']['color'];?>;
		<?php }?>
	}
 
	#woo__multi__layout__<?php echo $id;?> .woo_mulit_layout_product_title h3,
	#woo__multi__layout__<?php echo $id;?> .woo_mulit_layout_product_title a,
	#woo__multi__layout__<?php echo $id;?> h2.woocommerce-loop-product__title,
	#woo__multi__layout__<?php echo $id;?> h2.woocommerce-loop-product__title a{
	
		<?php if( isset( $editor['product_title']['font_family'] ) && $editor['product_title']['font_family'] != 'none'){ ?>
		font-family:"<?php echo $editor['product_title']['font_family'];?>", Gadget, sans-serif;
		<?php if( isset( $editor['product_title']['font_size'] ) && $editor['product_title']['font_size'] != ''){ ?>
		font-size:<?php echo $editor['product_title']['font_size'];?>px;
		<?php }?><?php }?><?php if( isset( $editor['product_title']['color'] ) && $editor['product_title']['color'] != ''){ ?>
		color:<?php echo $editor['product_title']['color'];?>;
		<?php }?>
	}
	#woo__multi__layout__<?php echo $id;?> .woo_mulit_layout_product_title h3:hover,
	#woo__multi__layout__<?php echo $id;?> .woo_mulit_layout_product_title a:hover{
		<?php if( isset( $editor['product_title']['hover_color'] ) && $editor['product_title']['hover_color'] != ''){ ?>
		color:<?php echo $editor['product_title']['hover_color'];?>;
		<?php }?><?php if( isset( $editor['product_title']['hover_bg'] ) && $editor['product_title']['hover_bg'] != ''){ ?>
		background:<?php echo $editor['product_title']['hover_bg'];?>;
		<?php }?>
	}

	#woo__multi__layout__<?php echo $id;?> .button,
	#woo__multi__layout__<?php echo $id;?> .added_to_cart{
		<?php if( isset( $editor['add_to_cart']['font_family'] ) && $editor['add_to_cart']['font_family'] != 'none'){ ?>
		font-family:"<?php echo $editor['add_to_cart']['font_family'];?>", Gadget, sans-serif;
		<?php }?><?php if( isset( $editor['add_to_cart']['font_size'] ) && $editor['add_to_cart']['font_size'] != ''){ ?>
		font-size:<?php echo $editor['add_to_cart']['font_size'];?>px;
		<?php }?><?php if( isset( $editor['add_to_cart']['color'] ) && $editor['add_to_cart']['color'] != ''){ ?>
		color:<?php echo $editor['add_to_cart']['color'];?>;
		<?php }?>
		<?php if( isset( $editor['add_to_cart']['bg'] ) && $editor['add_to_cart']['bg'] != ''){ ?>
		background:<?php echo $editor['add_to_cart']['bg'];?>;
		<?php }?>
	}
	#woo__multi__layout__<?php echo $id;?> .button:hover,
	#woo__multi__layout__<?php echo $id;?> .added_to_cart:hover{
		<?php if( isset( $editor['add_to_cart']['hover_color'] ) && $editor['add_to_cart']['hover_color'] != ''){ ?>
		color:<?php echo $editor['add_to_cart']['hover_color'];?>;
		<?php }?><?php if( isset( $editor['add_to_cart']['hover_bg'] ) && $editor['add_to_cart']['hover_bg'] != ''){ ?>
		background:<?php echo $editor['add_to_cart']['hover_bg'];?>;
		<?php }?>
	}
	
	#woo__multi__layout__<?php echo $id;?> .woo_mulit_categories a,
	#woo__multi__layout__<?php echo $id;?> .woo_mulit_categories{
		<?php if( isset( $editor['product_category']['font_family'] ) && $editor['product_category']['font_family'] != 'none'){ ?>
		font-family:"<?php echo $editor['product_category']['font_family'];?>", Gadget, sans-serif;
		<?php }?><?php if( isset( $editor['product_category']['font_size'] ) && $editor['product_category']['font_size'] != ''){ ?>
		font-size:<?php echo $editor['product_category']['font_size'];?>px;
		<?php }?><?php if( isset( $editor['product_category']['color'] ) && $editor['product_category']['color'] != ''){ ?>
		color:<?php echo $editor['product_category']['color'];?>;
		<?php }?>
	}
	#woo__multi__layout__<?php echo $id;?> .woo_mulit_categories a:hover{
		<?php if( isset( $editor['product_category']['hover_color'] ) && $editor['product_category']['hover_color'] != ''){ ?>
		color:<?php echo $editor['product_category']['hover_color'];?>;
		<?php }?>
	}
	
	#woo__multi__layout__<?php echo $id;?> .woo_mulit_tagged  a,
	#woo__multi__layout__<?php echo $id;?> .woo_mulit_tagged {
		<?php if( isset( $editor['product_tags']['font_family'] ) && $editor['product_tags']['font_family'] != 'none'){ ?>
		font-family:"<?php echo $editor['product_tags']['font_family'];?>", Gadget, sans-serif;
		<?php }?><?php if( isset( $editor['product_tags']['font_size'] ) && $editor['product_tags']['font_size'] != ''){ ?>
		font-size:<?php echo $editor['product_tags']['font_size'];?>px;
		<?php }?><?php if( isset( $editor['product_tags']['color'] ) && $editor['product_tags']['color'] != ''){ ?>
		color:<?php echo $editor['product_tags']['color'];?>;
		<?php }?>
	}
	#woo__multi__layout__<?php echo $id;?> .woo_mulit_tagged  a:hover{
		<?php if( isset( $editor['product_tags']['hover_color'] ) && $editor['product_category']['hover_color'] != ''){ ?>
		color:<?php echo $editor['product_tags']['hover_color'];?>;
		<?php }?>
	}
	
	#woo__multi__layout__<?php echo $id;?> .woo_mulit_readmore a{
		<?php if( isset( $editor['readmore']['font_family'] ) && $editor['readmore']['font_family'] != 'none'){ ?>
		font-family:"<?php echo $editor['readmore']['font_family'];?>", Gadget, sans-serif;
		<?php }?><?php if( isset( $editor['readmore']['font_size'] ) && $editor['readmore']['font_size'] != ''){ ?>
		font-size:<?php echo $editor['readmore']['font_size'];?>px;
		<?php }?><?php if( isset( $editor['readmore']['color'] ) && $editor['readmore']['color'] != ''){ ?>
		color:<?php echo $editor['readmore']['color'];?>;
		<?php }?>
		<?php if( isset( $editor['readmore']['bg'] ) && $editor['readmore']['bg'] != ''){ ?>
		background:<?php echo $editor['readmore']['bg'];?>;
		<?php }?>
	}
	#woo__multi__layout__<?php echo $id;?> .woo_mulit_readmore a:hover{
		<?php if( isset( $editor['readmore']['hover_color'] ) && $editor['readmore']['hover_color'] != ''){ ?>
		color:<?php echo $editor['readmore']['hover_color'];?>;
		<?php }?><?php if( isset( $editor['readmore']['hover_bg'] ) && $editor['readmore']['hover_bg'] != ''){ ?>
		background:<?php echo $editor['readmore']['hover_bg'];?>;
		<?php }?>
	}
 	#woo__multi__layout__<?php echo $id;?> .woo_mulit_sku {
		<?php if( isset( $editor['sku']['font_size'] ) && $editor['sku']['font_size'] != ''){ ?>
		font-size:<?php echo $editor['sku']['font_size'];?>px;
		<?php }?><?php if( isset( $editor['sku']['color'] ) && $editor['sku']['color'] != ''){ ?>
		color:<?php echo $editor['sku']['color'];?>;
		<?php }?>
	}
	#woo__multi__layout__<?php echo $id;?> .woo_mulit_stock {
		<?php if( isset( $editor['stock']['font_size'] ) && $editor['stock']['font_size'] != ''){ ?>
		font-size:<?php echo $editor['stock']['font_size'];?>px;
		<?php }?><?php if( isset( $editor['stock']['color'] ) && $editor['stock']['color'] != ''){ ?>
		color:<?php echo $editor['stock']['color'];?>;
		<?php }?>
	}

	#woo__multi__layout__<?php echo $id;?> .woo_mulit_stock_status {
		<?php if( isset( $editor['woo_mulit_stock_status']['font_size'] ) && $editor['woo_mulit_stock_status']['font_size'] != ''){ ?>
		font-size:<?php echo $editor['woo_mulit_stock_status']['font_size'];?>px;
		<?php }?><?php if( isset( $editor['woo_mulit_stock_status']['color'] ) && $editor['woo_mulit_stock_status']['color'] != ''){ ?>
		color:<?php echo $editor['woo_mulit_stock_status']['color'];?>;
		<?php }?>
	}

</style>
<!-- Custom Styling -->