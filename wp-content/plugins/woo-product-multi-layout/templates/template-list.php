<?php
// Prohibit direct script loading.
defined('ABSPATH') || die('No direct script access allowed!');
 //woocommerce_catalog_ordering();
if ( isset( $template['filter'] ) && $template['filter'] == "true" ) {
	echo $this->woo_mulit_order_by( $r );
}

echo "<ul class='woo__mulit__layout__list__views woo__mulit__layout'>";
while ($r->have_posts()) :
	$r->the_post();
	global $product;
	
	echo "<li class='item dynamic__css__class'>";
		if( isset( $editor['product_image'] )){
			$this->woo_mulit_layout_builder( 'product_image' ,$editor['product_image'], $product );
			echo '<div class="list_view_pull_right">';
		}else{
			echo '<div class="list_view_pull_right fullwidth">';
		}
		if(isset($editor) && is_array( $editor )){
			foreach ( $editor as $key => $val ) :
				if( $key == "" || $key  == 'product_image' ) continue;
				$this->woo_mulit_layout_builder( $key, $val, $product );
			endforeach ;
		}
		echo '</div>';
	
	echo "<div class='clr'></div></li>";
	
endwhile;
echo "</ul>";
?>
