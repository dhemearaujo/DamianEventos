<?php 
/**
 * Views Class
 *
 * @package woo_mulit_layout
 * @author Saiful ( edatastyle )
 * @since 1.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

class Woo_Mulit_Layout_Views {
	 /**
     * @global $post->ID
     * @var int
     */
 	public $post_id = 0;
    
    /**
     * @var striang
     */
    protected $settings = null;
    /**
     * @var string
     */
    protected $path = null;
	
	 /**
     * @var string
     */
    protected $url = null;
	 /**
     * @var array
     */
	protected $meta_settings;
	/**
	 *  Init Loading
	 */
	public function __construct() {
		$this->path			 = ED_MULTI_PATH;
		$this->url  		 = ED_MULTI_URL;
 		$this->meta_settings = array( 'template' => 'woo_multii_layout_initial','product' => 'woo_multii_layout_product_query', 'editor' => 'woo_product_loop_element');
		
		$this->load_dependencies();
    }
	/**
	 * Register a filter hook 
	 * @return void
	 */
	private function load_dependencies() {
 
		add_shortcode('woo_mulit_layout', array(
            $this,
            'woo_mulit_layout'
        ));
		
  		
		
		add_action( 'woo_mulit_layout_add_to_cart', 'woocommerce_template_loop_add_to_cart', 10 );
		add_action( 'woo_mulit_product_link_open', 'woocommerce_template_loop_product_link_open', 10 );
		add_action( 'woo_mulit_product_link_close', 'woocommerce_template_loop_product_link_close', 10 );
		add_action( 'woo_mulit_product_rating', 'woocommerce_review_display_rating', 10 );
		
		
		add_action( 'wp_enqueue_scripts', array($this,'woo_mulit_layout_enqueue'),99 );
		add_action( 'wp_head', array($this,'load_dynamic_style') );
		
		
    }
	/**
	 * Register Shortcode
	 * @return html
	 */
	public function woo_mulit_layout( $atts  ){
		extract(shortcode_atts(array(
            'id' => 0
        ), $atts));
		  $result = '';
        wp_reset_postdata();
        wp_reset_query();
        global $wpdb, $woocommerce;
		
		// Set Up WooCommerce Query Product
        if ( get_query_var('paged') ) {
            $paged = get_query_var('paged');
        } else if ( get_query_var('page') ) {
            $paged = get_query_var('page');
        } else{
            $paged = 1;
        }
        
		 /*
         * Get Meta From ID
         */
        if($id){
			$wooCommerce_query = $this->set_wooCommerce( $id );
			
			
			if( !isset($this->meta_settings['template']) ) return false;
			
			$template = maybe_unserialize(  get_post_meta( $id, $this->meta_settings['template'], true ) );
			$editor = maybe_unserialize(  get_post_meta( $id, $this->meta_settings['editor'], true ) );
			
				 /* -------------------------
				 * WP Query 
				 * ------------------------- */
				$r = new WP_Query($wooCommerce_query);
			
				$result ='<div class="woocommerce" id="woo__multi__layout__'.$id.'">';
				//do_action('woocommerce_before_shop_loop');
				if ($r->have_posts()) {
					
					//Switch Template
					if ( isset( $template['template_list'] ) && $template['template_list'] != "" && isset($editor) && is_array($editor) ) {
						
						$template_location = apply_filters('woo_mulit_layout_template_file', $this->path . '/templates/'.$template['template_list']);
						
						if( file_exists( $template_location ) ){
							ob_start();
							include( $template_location );
							$result .= ob_get_clean();
						}else{
							$result.=__('No product Layout found.', 'ED_MULTI_LANG');	
							
						}
					
					} else {
						$result.=__('No product Layout found.', 'ED_MULTI_LANG');
					}	
				} else {
					$result.=__('No product found.', 'ED_MULTI_LANG');
				}
				wp_reset_postdata();
				wp_reset_query();
				
				if ( isset( $template['pagination'] ) && $template['pagination'] == "true" ) {
					if ($r->max_num_pages >= 1) {
						$result.='<nav class="woocommerce-pagination">';
						$result.= paginate_links(apply_filters('woocommerce_pagination_args', array(
							'base' => str_replace(99999, '%#%', html_entity_decode(get_pagenum_link(99999))),
							'current' => max(1, $paged),
							'total' => $r->max_num_pages,
							'prev_text' => '&larr;',
							'next_text' => '&rarr;',
							'type' => 'list',
							'end_size' => 3,
							'mid_size' => 3
						)));
					
						$result.='</nav>';
					}
				}
				
				
				$result.= '</div>';
				if( isset($editor) && is_array($editor) && file_exists( $template_location ) ){
					
					include_once($this->path . '/assets/frontend/woo_multi_layout_dynamic_css.php')	;
				}
			return $result ;	
		}
	}
	
	public function set_wooCommerce( $id ){
		if( !isset($this->meta_settings['product']) && !isset( $id ) ) return false;
		global $wp_query,$wpdb,$woocommerce;
		$option = maybe_unserialize(  get_post_meta( $id, $this->meta_settings['product'], true ) );
		// Set Up WooCommerce Query Product
        if ( get_query_var('paged') ) {
            $paged = get_query_var('paged');
        } else if ( get_query_var('page') ) {
            $paged = get_query_var('page');
        } else{
            $paged = 1;
        }
        /*
         * Post Pre page
         */
        $perpage = isset ( $option['perpage'] ) ?  $option['perpage'] : get_option( 'posts_per_page' );
        /*
         * Sort
         */
        if(isset($_GET['perpage']) && !empty($_GET['perpage']) && $_GET['perpage'] != 'default'){
             $perpage = $_GET['perpage'];
        }
	
		
   
		if ( isset($option['product_query']) && $option['product_query'] != "all" && isset( $option['product_query_custom'] )){
			$filter = ( is_array( $option['product_query_custom'] ) ) ? $option['product_query_custom'] : array();
		}else{
			$filter =  array();
		}
		/* -------------------------
         * Sale Product
         * ------------------------- */
        if (in_array("sales", $filter)) {
            $product_ids_on_sale = wc_get_product_ids_on_sale();
            $product_ids_on_sale[] = 0;
			
        }else{
            $product_ids_on_sale='';
        }
		 /*
         * orderby
         */
		if(isset($_GET['orderby']) && !empty($_GET['orderby'])){
             $option['sort'] = $_GET['orderby'];
        }
		
		/*
         * switch
         */
		if ( isset($option['sort']) ):
		$meta_key = '';
		$order = 'desc';
		$orderby = 'date';
		switch($option['sort']){
			
			case 'popularity':
				$meta_key = '';
				$order = 'desc';
				$orderby = 'total_sales';
				break;
			case 'lowhigh':
				$meta_key = '_price';
				$order = 'asc';
				$orderby = 'meta_value_num';
				break;
			case 'highlow':
				$meta_key = '_price';
				$order = 'desc';
				$orderby = 'meta_value_num';
				break;
			case 'oldest':
				$meta_key = '';
				$order = 'asc';
				$orderby = 'date';
				break;	
			case 'nameaz':
				$meta_key = '';
				$order = 'asc';
				$orderby = 'title';
				break;	
			case 'nameza':
				$meta_key = '';
				$order = 'desc';
				$orderby = 'title';
				break;
				
			case 'newness':
				$meta_key = '';
				$order = 'desc';
				$orderby = 'date';
				break;
			case 'rating':
				$meta_key = '';
				$order = 'desc';
				$orderby = 'rating';
				break;
				
			 default:
				$meta_key = '';
				$order = 'desc';
				$orderby = 'date';
				
		}
		endif;
		/*
         * search
         */
		 $edsearch=(!empty($_GET['edsearch']))? $_GET['edsearch'] :$dp_search='';
		/* -------------------------
         * Query Product 
         * ------------------------- */
        $query_args = array(
            'posts_per_page' => $perpage,
            'paged' => $paged,
            'post_status' => 'publish',
            'post_type' => 'product',
            'ignore_sticky_posts' => 1,
           // 's'=>$dp_search,
            'post__in' => $product_ids_on_sale,
			'orderby'   => $orderby,
			'meta_key'  => $meta_key,
			'order' => $order,
			's'=>$edsearch,
        );
		$query_args['meta_query'] = array();
        $query_args['meta_query'] = $woocommerce->query->get_meta_query();
		
		
		
		/* -------------------------
		 * In Stock Product 
		 * ------------------------- */
		
		if (in_array("instock", $filter)) {
			$query_args['meta_query'][] =
					array(
						'key' => '_manage_stock',
						'value' => "yes",
						'compare' => '=',
			);
			$query_args['meta_query'][] = array(
				'key' => '_stock_status',
				'value' => "instock",
				'compare' => '=',
					)
			;
		}
		
		if( isset( $option['stock'] ) && $option['stock'] == 'instock' && ! in_array("instock", $filter)){
			$query_args['meta_query'][] =
					array(
						'key' => '_manage_stock',
						'value' => "yes",
						'compare' => '=',
			);
			$query_args['meta_query'][] = array(
				'key' => '_stock_status',
				'value' => "instock",
				'compare' => '=',
					)
			;
		}
		 /* -------------------------
		  * Out Stock Product 
		  * ------------------------- */
		   if (in_array("outofstock", $filter)) {
			   $query_args['meta_query'][] =
					   array(
						   'key' => '_manage_stock',
						   'value' => "yes",
						   'compare' => '=',
			   );
			   $query_args['meta_query'][] = array(
				   'key' => '_stock_status',
				   'value' => "outofstock",
				   'compare' => '=',
			   );
		   } if( isset( $option['stock'] ) && $option['stock'] == 'outofstock' && ! in_array("instock", $filter)){
				  $query_args['meta_query'][] =
						   array(
							   'key' => '_manage_stock',
							   'value' => "yes",
							   'compare' => '=',
				   );
				   $query_args['meta_query'][] = array(
					   'key' => '_stock_status',
					   'value' => "outofstock",
					   'compare' => '=',
				   );
			 }
		   /* -------------------------
			* Category 
			* ------------------------- */
			   if ( isset($option['category_type']) && $option['category_type'] != "all" && isset ( $option['category_type_custom'] )){
					$category = ( is_array(  $option['category_type_custom'] ) ) ?  $option['category_type_custom'] : array();
					$category_query = array(
					   array(
						   'taxonomy' => 'product_cat',
						   'terms' => $category,
						   'field' => 'term_id',
						   'operator' => 'IN'
					   )
					); 
					$query_args['tax_query'][] = $category_query;
			   }
			   if( get_query_var('product_cat') != "" ){
					
					$category_query = array(
					   array(
						   'taxonomy' => 'product_cat',
						   'terms' => get_query_var('product_cat'),
						   'field' => 'slug',
						   'operator' => 'IN'
					   )
					);
					$query_args['tax_query'][] = $category_query;
			   }
			 
			  
			/* -------------------------
			* Tag 
			* ------------------------- */
			if ( isset($option['tag_type']) && $option['tag_type'] != "all" & isset ( $option['tag_type_custom'] )){
		
				$tag = (  is_array( $option['tag_type_custom'] ) ) ? $option['tag_type_custom'] : array(); 
				$tag_query = array(
				   array(
					   'taxonomy' => 'product_tag',
					   'terms' => $tag,
					   'field' => 'term_id',
					   'operator' => 'IN'
				   )
				);
				$query_args['tax_query'][] = $tag_query;
			
			 }
			 if( get_query_var('product_tag') != ""){
				
				$tag_query = array(
				   array(
					   'taxonomy' => 'product_tag',
					   'terms' => get_query_var('product_tag'),
					   'field' => 'slug',
					   'operator' => 'IN'
				   )
				);
				$query_args['tax_query'][] = $tag_query;
			 }
			
		   /* -------------------------
			* Featured Product 
			* ------------------------- */
		   if (in_array("featured", $filter)) {
			   $query_args['meta_query'][] = array(
				   'key' => '_featured',
				   'value' => 'yes'
			   );
		   }
		   $query_args['tax_query'][]=array('relation' => 'AND');
			
			/* -------------------------
            * Date Query Product 
            * ------------------------- */
			
            if (isset($option['date_range']) && $option['date_range']) {
                switch ($option['date_range']){
                    case 'today':
                        $today = getdate();
                        $query_args['date_query'][]['year']=$today['year'];
                        $query_args['date_query'][]['month']=$today['mon'];
                        $query_args['date_query'][]['day']=$today['mday'];
                        break;
                    case 'yesterday':
                        $query_args['date_query'][]['year']=date("Y", time() - 60 * 60 * 24);
                        $query_args['date_query'][]['month']=date("m", time() - 60 * 60 * 24);
                        $query_args['date_query'][]['day']=date("d", time() - 60 * 60 * 24);
                        break;
                    case 'this_month':
                        $today = getdate();
                        $query_args['date_query'][]['year']=$today['year'];
                        $query_args['date_query'][]['month']=$today['mon'];
                        break;
                    case 'this_year':
                        $today = getdate();
                        $query_args['date_query'][]['year']=$today['year'];
                        break;
                    case 'last7':
                        $today = getdate();
                        $query_args['date_query'][]['after']='7 days ago';
                        $query_args['date_query'][]['inclusive']=true;
                        break;
                    case '30':
                        $today = getdate();
                        $query_args['date_query'][]['after']='30 days ago';
                        $query_args['date_query'][]['inclusive']=true;
                        break;
                    case '120':
                        $today = getdate();
                        $query_args['date_query'][]['after']='120 days ago';
                        $query_args['date_query'][]['inclusive']=true;
                        break;
                    case '365':
                        $today = getdate();
                        $query_args['date_query'][]['after']='365 days ago';
                        $query_args['date_query'][]['inclusive']=true;
                        break;
                    case 'custom':
                        if(isset($option['start_date']) && $option['start_date']){
                            $query_args['date_query'][]['after']= $option['start_date'];
                        }
                        if(isset($option['end_date']) &&  $option['end_date']){
                            $query_args['date_query'][]['before']= $option['end_date'];
                        }
                        $query_args['date_query'][]['inclusive']=true;
                        break;
                }
            }
			
			return apply_filters('woo_multi_layout_query', wp_parse_args ( $query_args ) );
	}
	
	public function load_dynamic_style(){
		
	}
	
	private function woo_mulit_layout_builder( $key, $val = array() , $product = array()){
		if( !isset($key) && $key  == "" ) return false;
		
			switch ( $key ) {
			case 'product_title':
				/* useing default  woocommerce */
				$custom_css_class = isset( $val['custom_css_class'] ) ? $val['custom_css_class'] : '';
				echo '<div class="woo_mulit_layout_product_title '.$custom_css_class.'">';
			
				if( isset( $val['behavior'] ) && $val['behavior'] == 'link' ) :
				
					echo ' <a href=" '. get_permalink($product->get_id()) .' ">';
					do_action('woocommerce_shop_loop_item_title');
					echo "</a>";
				else:
					do_action('woocommerce_shop_loop_item_title');
				endif;	
				echo "</div>"; 
				break;
			case 'add_to_cart':
				/* useing default  woocommerce */
				$custom_css_class = isset( $val['custom_css_class'] ) ? $val['custom_css_class'] : '';
				echo '<div class="'.$custom_css_class.'">';
				do_action('woo_mulit_layout_add_to_cart');
				echo "</div>";
				break;
			case 'product_image':
				/* useing default  woocommerce */
				$custom_css_class = isset( $val['custom_css_class'] ) ? $val['custom_css_class'] : '';
				$image_effect = isset( $val['image_effect'] ) ? $val['image_effect'] : '';
				
				$size = ( !isset( $val['image_size'] ) || $val['image_size'] =='disable') ? 'medium' : $val['image_size'];
				
				$post_thumbnail_id = get_post_thumbnail_id( $product->get_id() );
				
				$post_thumbnail_url = wp_get_attachment_image_src( $post_thumbnail_id, $size );
				
				echo "<div class='woo_mulit_layout_product_image ". $custom_css_class ." ". $image_effect ."'>";
				do_action( 'woo_mulit_product_link_open' );
				//do_action('woocommerce_before_shop_loop_item_title');
				echo  '<img src="'.esc_url( $post_thumbnail_url[0] ).'" alt="'.get_the_title( $product->get_id() ).'" />';
				do_action( 'woo_mulit_product_link_close' );
				echo "</div>";
				break;	 
			case 'author':
				/* custom development function */
				$author_text = isset( $val['author_text'] ) ? $val['author_text'] : 'Product By :';
				$custom_css_class = isset( $val['custom_css_class'] ) ? $val['custom_css_class'] : '';
				echo '<div class="woo_mulit_author '.$custom_css_class.'">'. $author_text .'   '.
				'<a href="'. get_author_posts_url( get_the_author_meta( get_the_author_posts( $product->get_id() ) ), get_the_author_meta( 'user_nicename' ) ) .'" rel="author">'.
				get_the_author().
				'</a></div>';
				break;	
			case 'date':
				/* custom development function */
				$date = isset($val['date_text']) ? $val['date_text'] : 'Posted on : ';
				$custom_css_class = isset( $val['custom_css_class'] ) ? $val['custom_css_class'] : '';
				echo '<div class="woo_mulit_date '.$custom_css_class.'">'.$date . get_the_date( get_option('date_format'), $product_id ) .'</div>';
				break;		
			case 'excerpt':
				/* custom development function */
				$word_limit = isset( $val['word_limit'] ) ? $val['word_limit'] : '100';
				$filters_excerpt = wp_strip_all_tags( get_the_excerpt() );
				$trim =  $this->trim_text( $filters_excerpt, $word_limit) ;
				$custom_css_class = isset($val['custom_css_class']) ? $val['custom_css_class'] : '';
				echo '<div class="woo_mulit_excerpt '.$custom_css_class.'">'. $trim .'</div>';
				break;	
			case 'product_content':
				/* custom development function */
				$word_limit = isset( $val['word_limit'] ) ? $val['word_limit'] : '100';
				$filters_content = wp_strip_all_tags( get_the_content() );
				$trim_content =  $this->trim_text( $filters_content, $word_limit) ;
				$custom_css_class = isset($val['custom_css_class']) ? $val['custom_css_class'] : '';
				echo '<div class="woo_mulit_content '.$custom_css_class.'">'. $trim_content .'</div>';
				break;
			case 'product_category':
				/* custom development function */
				$custom_css_class = isset( $val['custom_css_class'] ) ? $val['custom_css_class'] : '';
				 echo '<div class="woo_mulit_categories '.$custom_css_class.'">';
				 if( isset( $val['behavior'] ) && $val['behavior'] == 'link' ) :
					 echo $product->get_categories( ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', '', 'woocommerce' ) . ' ', '</span>' );
				 else:
					 echo wp_strip_all_tags( $product->get_categories( ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', '', 'woocommerce' ) . ' ', '</span>' ) );
				 endif;	
				 echo '</div>';
				break;		
					
			case 'product_tags':
				/* custom development function */
				$custom_css_class = isset( $val['custom_css_class'] ) ? $val['custom_css_class'] : '';
				 echo '<div class="woo_mulit_tagged '.$custom_css_class.'">';
				 if( isset( $val['behavior'] ) && $val['behavior'] == 'link' ) :
					 echo $product->get_tags( ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tag:', '', 'woocommerce' ) . ' ', '</span>' );
				 else:
					 echo wp_strip_all_tags( $product->get_tags( ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tag:', '', 'woocommerce' ) . ' ', '</span>' ) );
				 endif;	
				 echo '&nbsp;</div>';
				break;	
			 case 'sku':
				/* custom development function */
				$custom_css_class = isset( $val['custom_css_class'] ) ? $val['custom_css_class'] : '';
				 echo '<div class="woo_mulit_sku '.$custom_css_class.'">' . __( 'SKU:', 'woocommerce' );
				 echo '<span class="sku" itemprop="sku"> '. ( $sku = $product->get_sku() ) ? $sku : __( 'N/A', 'woocommerce' ) .'</span>';
				 echo '&nbsp;</div>';
				break;	
			 case 'price':
				/* custom development function */
				$custom_css_class = isset( $val['custom_css_class'] ) ? $val['custom_css_class'] : '';
				 echo '<div class="woo_mulit_price '.$custom_css_class.'">'. $product->get_price_html() .'</div>';
				break;	
			 case 'readmore':
				/* custom development function */
				$custom_css_class = isset( $val['custom_css_class'] ) ? $val['custom_css_class'] : '';
				$text = isset( $val['text'] ) ? $val['text'] : ' Read More';
				 echo '<div class="woo_mulit_readmore '.$custom_css_class.'"> <a href=" '. get_permalink( $product->get_id() ) .' ">'. $text .'</a>&nbsp;</div>';
				break;	
			case 'line_break':
				/* custom development function */
				$custom_css_class = isset( $val['custom_css_class'] ) ? $val['custom_css_class'] : '';
				 echo '<hr class="'.$custom_css_class.'"/>';
				break;
			case 'stock':
				/* custom development function */
				$custom_css_class = isset( $val['custom_css_class'] ) ? $val['custom_css_class'] : '';
				if ( $product->is_in_stock() ) {
					$stock = '<div class="stock" >' . $product->get_stock_quantity() . __( ' in stock', 'envy' ) . '</div>';
				} else {
					$stock = '<div class="out-of-stock" >' . __( 'out of stock', 'envy' ) . '</div>';
				}
				 echo '<div class="woo_mulit_stock '.$custom_css_class.'"> ' . $stock . '</div>';
				break;			
			case 'stock_status':
				/* custom development function */
				$custom_css_class = isset( $val['custom_css_class'] ) ? $val['custom_css_class'] : '';
				if ( $product->is_in_stock() ) {
					$stock = '<div class="stock" >' . $product->get_stock_quantity() . __( ' in stock', 'envy' ) . '</div>';
				} else {
					$stock = '<div class="out-of-stock" >' . __( 'out of stock', 'envy' ) . '</div>';
				}
				 echo '<div class="woo_mulit_stock_status '.$custom_css_class.'"> ' . $stock . '</div>';
				break;	
					
			default:
			
			} 
			
		
		
		
	}
	
	/**
	 * Trim Text
	 * @param $input  striang, $length  integer, $ellipses  void, $strip_html  void, 
	 * @return striang
	 */
	private function trim_text($input, $length, $ellipses = true, $strip_html = true) {
		//strip tags, if desired
		if ($strip_html) {
			$input = strip_tags($input);
		}
	  
		//no need to trim, already shorter than trim length
		if (strlen($input) <= $length) {
			return $input;
		}
	  
		//find last space within length
		$last_space = strrpos(substr($input, 0, $length), ' ');
		$trimmed_text = substr($input, 0, $last_space);
	  
		//add ellipses (...)
		if ($ellipses) {
			$trimmed_text .= '...';
		}
	  
		return $trimmed_text;
	}
	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function woo_mulit_layout_enqueue() {
		/**
		* @vendor
		*/
		wp_enqueue_script('jquery');
		wp_enqueue_style( 'woo-mulit-owlcarousel-css', $this->url.'vendor/owlcarousel/owl.carousel.css');
		wp_enqueue_script('woo-mulit-owlcarousel-js', $this->url.'vendor/owlcarousel/owl.carousel.js',0,0,true);
		
		wp_enqueue_script('jquery-masonry', $this->url.'vendor/masonry/masonry.pkgd.min.js',0,0,true);
		/**
		* @Plugins Core
		*/
		wp_enqueue_style( 'woo-mulit-layout-css', $this->url.'assets/frontend/woo_multi_layout.css');
		wp_enqueue_script('woo-mulit-layout-js', $this->url.'assets/frontend/woo_multi_layout.js',0,0,true);

	}
	/**
	 * Trim Text
	 * @param $wp_query
	 * @return html
	 */
	public function woo_mulit_order_by( $r ){
		global $woocommerce;
		if ( 0 != $r->found_posts):
		
		$result ='<div class="woo__mulit__layout__sorter">';
			
			// Result Count
			$result.='<div class="woo_mulit__layout__result__count">';
		
			$paged    = max( 1, $r->get( 'paged' ) );
			$per_page = $r->get( 'posts_per_page' );
			$total    = $r->found_posts;
			$first    = ( $per_page * $paged ) - $per_page + 1;
			$last     = min( $total, $r->get( 'posts_per_page' ) * $paged );
		
			if ( 1 == $total ) {
				$result.=__( 'Showing the single result', 'ED_MULTI_LANG');
			} elseif ( $total <= $per_page || -1 == $per_page ) {
				$result.=sprintf( __( 'Showing all %d results', 'ED_MULTI_LANG'), $total );
			} else {
				$result.=sprintf( _x( 'Showing %1$d – %2$d of %3$d results', '%1$d = first, %2$d = last, %3$d = total', 'ED_MULTI_LANG'), $first, $last, $total );
			}
		
			$result.='</div>';
			
			// Per page
			$result.='<form method="get" class="sort_multi_layout_orderby woocommerce-ordering"><div class="ed__orderby">';    
				// Keep query string vars intact
				foreach ( $_GET as $key => $val ) {
						if ( 'orderby' == $key||'perpage' == $key)
								continue;
		
						if (is_array($val)) {
								foreach($val as $innerVal) {
										$result.='<input type="hidden" name="' .  $key . '[]" value="' . $innerVal . '" />';
								}
		
						} else {
								$result.='<input type="hidden" name="' . $key  . '" value="' .  $val  . '" />';
						}
				}
				$result.= '<select name="orderby" class="orderby">';
					$catalog_orderby = apply_filters( 'multi_layout_orderby', array(
						'default' => __( 'Default sorting ( newness )', 'ED_MULTI_LANG'),
						'popularity' => __( 'Sort by popularity', 'ED_MULTI_LANG'),
						'lowhigh'     => __( 'Sort by Price low to high', 'ED_MULTI_LANG'),
						'highlow'       => __( 'Sort by Price high to low', 'ED_MULTI_LANG'),
						'oldest'      => __( 'Sort by oldest', 'ED_MULTI_LANG'),
						'nameaz' => __( 'Sort by Product title a to z', 'ED_MULTI_LANG'),
						'nameza' => __( 'Sort by Product title z to a', 'ED_MULTI_LANG'),
						'rating'     => __( 'Sort by Rating', 'ED_MULTI_LANG'),
					) );
		
					if ( get_option( 'woocommerce_enable_review_rating' ) == 'no' )
						unset( $catalog_orderby['rating'] );
								$orderby=!empty($_GET['orderby'])?$_GET['orderby']:'';
					foreach ( $catalog_orderby as $id => $name ){
									
									$result.='<option value="' . esc_attr( $id ) . '" ' . selected( $orderby, $id, false ) . '>' . esc_attr( $name ) . '</option>';
								}
				
			$result.='</select></div><div class="ed__perpage">';
				$result.=__( 'Show ', 'ED_MULTI_LANG').'<select name="perpage" class="orderby">';
				
					$catalog_perpage = apply_filters( 'dp_catalog_perpage', array(
										'default' => __( 'Default', 'ED_MULTI_LANG'),
						'4' => __( '4', 'ED_MULTI_LANG'),
						'8' => __( '8', 'ED_MULTI_LANG'),
						'12'=> __( '12', 'ED_MULTI_LANG'),
						'15'  => __( '15', 'ED_MULTI_LANG'),
						'30'      => __( '30', 'ED_MULTI_LANG'),
						'80' => __( '80', 'ED_MULTI_LANG'),
										'-1' => __( 'All', 'ED_MULTI_LANG'),
					) );
		
					if ( get_option( 'woocommerce_enable_review_rating' ) == 'no' )
						unset( $catalog_perpage['rating'] );
								$perpage=!empty($_GET['perpage'])?$_GET['perpage']:'';
					foreach ( $catalog_perpage as $id => $name )
						$result.='<option value="' . esc_attr( $id ) . '" ' . selected( $perpage, $id, false ) . '>' . esc_attr( $name ) . '</option>';
				
			$result.='</select> '.__( 'per page', 'ED_MULTI_LANG').'</div>';
			
				
		//	$result.='<input type="hidden" name="dppage" value="1"/>';
			$result.='</form>';
			
			
		
		   
		$result.='<div class="clr"></div></div>';
		return $result;
		endif;		
	}
		
}
new Woo_Mulit_Layout_Views();
