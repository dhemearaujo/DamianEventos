<?php
/**
 * The Template for displaying store list.
 *
 * @package WCfM Markeplace Views Store Lists
 *
 * For edit coping this to yourtheme/wcfm/store/shortcodes
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $WCFM, $WCFMmp, $post;

$pagination_base = str_replace( $post->ID, '%#%', esc_url( get_pagenum_link( $post->ID ) ) );

$search_query     = isset( $_GET['wcfmmp_store_search'] ) ? sanitize_text_field( $_GET['wcfmmp_store_search'] ) : '';
$search_category  = isset( $_GET['wcfmmp_store_category'] ) ? sanitize_text_field( $_GET['wcfmmp_store_category'] ) : '';
$search_country   = isset( $_GET['wcfmmp_store_country'] ) ? sanitize_text_field( $_GET['wcfmmp_store_country'] ) : '';
$search_state     = isset( $_GET['wcfmmp_store_state'] ) ? sanitize_text_field( $_GET['wcfmmp_store_state'] ) : '';

$args = array(
		'stores'          => $stores,
		'limit'           => $limit,
		'offset'          => $offset,
		'paged'           => $paged,
		'search'          => $search,
		'category'        => $category,
		'country'         => $country,
		'state'           => $state,
		'map'             => $map,
		'map_zoom'        => $map_zoom,
		'auto_zoom'       => $auto_zoom,
		'search_query'    => $search_query,
		'search_category' => $search_category,
		'search_country'  => $search_country,
		'search_state'    => $search_state,
		'pagination_base' => $pagination_base,
		'per_row'         => $per_row,
		'search_enabled'  => $search,
		'image_size'      => $image_size,
		'excludes'        => $excludes
);

?>

<div class="wcfmmp-stores-listing">

  <?php  if( $map ) { $WCFMmp->template->get_template( 'shortcodes/wcfmmp-view-store-lists-map.php', $args ); } ?>

	<?php if( $search || $category || $country || $state ) { $WCFMmp->template->get_template( 'shortcodes/wcfmmp-view-store-lists-search-form.php', $args ); } ?>
	

	<?php $WCFMmp->template->get_template( 'shortcodes/wcfmmp-view-store-lists-loop.php', $args ); ?>
	
</div>