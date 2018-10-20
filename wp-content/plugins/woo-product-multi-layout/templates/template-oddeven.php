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

echo '<div class="ed__row">';
echo "<ul class='woo__mulit__layout__odd_even_views woo__mulit__layout'>";
$i = 0;
while ($r->have_posts()) : $i++;
	$r->the_post();
	global $product;
	if($i % 2 == 0){ 
		$new_editor_array = array_reverse($editor);
	}else{
		$new_editor_array = $editor;
	}
	echo "<li class='". $columns .' '. $mb_columns .' '.$tb_columns. "'><div class='grid_wrp dynamic__css__class'>";
	if(isset($editor) && is_array( $editor )){
		foreach ( $new_editor_array as $key => $val ) :
			if( $key == "" ) continue;
			$this->woo_mulit_layout_builder( $key, $val, $product );
		endforeach ;
	}
	echo "</div></li>";
endwhile;
echo "</ul>";
echo '</div>';
?>