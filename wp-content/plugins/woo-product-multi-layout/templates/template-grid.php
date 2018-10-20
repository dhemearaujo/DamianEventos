<?php
// Prohibit direct script loading.
defined('ABSPATH') || die('No direct script access allowed!');
 //woocommerce_catalog_ordering();
if ( isset( $template['filter'] ) && $template['filter'] == "true" ) {
	echo $this->woo_mulit_order_by( $r );
}
	
$columns =  ( isset($template['columns']) && $template['columns'] != '' )  ? 'ed__col__'.$template['columns'] : 'ed__col__3'; 
$mb_columns =  ( isset($template['mb_columns']) && $template['mb_columns'] != '' )  ? 'ed__col__md__'.$template['mb_columns'] : 'ed__col__md__1'; 
$tb_columns =  ( isset($template['tb_columns']) && $template['tb_columns'] != '' )  ? 'ed__col__sm__'.$template['tb_columns'] : 'ed__col__sm__2'; 	
echo "<ul class='woo__mulit__layout__grid woo__mulit__layout'>";
while ($r->have_posts()) :
	$r->the_post();
	global $product;
	echo "<li class='". $columns .' '. $mb_columns .' '.$tb_columns. "'><div class='dynamic__css__class'>";
	if(isset($editor) && is_array( $editor )){
		foreach ( $editor as $key => $val ) :
			if( $key == "" ) continue;
			$this->woo_mulit_layout_builder( $key, $val, $product );
		endforeach ;
	}
	echo "</div></li>";
endwhile;
echo "</ul>";
?>