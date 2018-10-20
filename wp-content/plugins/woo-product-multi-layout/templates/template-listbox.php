<?php
// Prohibit direct script loading.
defined('ABSPATH') || die('No direct script access allowed!');
 //woocommerce_catalog_ordering();
if ( isset( $template['filter'] ) && $template['filter'] == "true" ) {
	echo $this->woo_mulit_order_by( $r );
}

$columns =  ( isset($template['columns']) && $template['columns'] != '' )  ? 'ed__col__'.$template['columns'] : 'ed__col__3'; 
$number_of_list =  ( isset($template['number_of_list']) && $template['number_of_list'] != '' )  ? $template['number_of_list'] : '2'; 

$columns =  ( isset($template['columns']) && $template['columns'] != '' )  ? 'ed__col__'.$template['columns'] : 'ed__col__3'; 
$mb_columns =  ( isset($template['mb_columns']) && $template['mb_columns'] != '' )  ? 'ed__col__md__'.$template['mb_columns'] : 'ed__col__md__1'; 
$tb_columns =  ( isset($template['tb_columns']) && $template['tb_columns'] != '' )  ? 'ed__col__sm__'.$template['tb_columns'] : 'ed__col__sm__2'; 

echo "<ul class='woo__mulit__layout__list__views woo__mulit__layout'>";
$j = 0;
while ($r->have_posts()) : $j ++;
	$r->the_post();
	global $product;
	if( $j <= $number_of_list){
		
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
		
	}else{
		echo "<li class='". $columns .' '. $mb_columns .' '.$tb_columns. "'>";
		if(isset($editor) && is_array( $editor )){
			foreach ( $editor as $key => $val ) :
				if( $key == "" ) continue;
				$this->woo_mulit_layout_builder( $key, $val, $product );
			endforeach ;
		}
		echo "</li>";
	}
	
endwhile;
echo "</ul>";
?>
