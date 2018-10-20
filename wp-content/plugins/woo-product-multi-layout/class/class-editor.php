<?php
/**
 * ed_woo_loader Class
 *
 * @package Woo Mumiti Layout
 * @author Saiful ( edatastyle )
 * @since 1.0
 */

// Prohibit direct script loading.
defined('ABSPATH') || die('No direct script access allowed!');
if( !class_exists('Eds_Input_Drag_Drop_Editor') ){
	class Eds_Input_Drag_Drop_Editor {
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
		 * @var array
		 */
		protected $default = array();
		
		public function __construct( $post_id = 0 ) {
			$this->post_id   = absint( $post_id );
			
			// Store assets meta nmae
			$this->settings = 'woo_product_loop_element';
			
			// Store assets meta nmae, it is convenient
			$this->path = ED_MULTI_PATH;
			
			// Map evolution meta box Editor
			add_action('woo_multi_layout_editor', array(
				$this,
				'woo_multi_layout_editor'
			), 10);
			
			// Store meta defautl active item
			add_filter('woo_multi_layout_editor_default_element', array(
				$this,
				'get_meta_data'
			),99 , 1);
			$this->default = apply_filters('woo_multi_layout_editor_default_element', array(
				'product_image' => array(),
				'product_title' => array(),
				'price' => array(),
				'add_to_cart' => array()
			));
			
		   
			
			// Map Editor element Element meta box Editor
			add_action('render_editor_input_element', array(
				$this,
				'render_editor_input_element'
			), 10, 2);
			
			
		}
		/**
		 * Load meta Data.
		 * @access  public
		 * @return  array
		 */
		public function get_meta_data($array){
			if( $this->post_id != 0 ){
				if(  get_post_meta( $this->post_id, $this->settings, true ) ) {
					return  maybe_unserialize(  get_post_meta( $this->post_id, $this->settings, true ) );
				}else{
					return $array;
				}
			}else{
				return $array;
			}
		}
		
		/**
		 * Get all the editor element
		 * 
		 * @return array $fields
		 */
		public function woo_mulit_layout_element() {
			$fields = apply_filters('woo_multi_layout_editor_element', array(
				/* Start add_to_cart*/
				'author' => array(
					'field_label' => 'Author',
					'field_list' => array(
						'col_name' => array(
							'label' => 'Column Name :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column name',
							'render' => 'editor_input_text'
						),
						'col_width' => array(
							'label' => 'Column width :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column width Ex. 200px or 20%',
							'render' => 'editor_input_text'
						),
						'author_text' => array(
							'label' => 'Product By Text :',
							'class' => 'ed_element-wrapper',
							'placeholder' => 'Product By :',
							'render' => 'editor_input_text'
						)
					 )
				),
				
				'date' => array(
					'field_label' => 'Date',
					'field_list' => array(
						'col_name' => array(
							'label' => 'Column Name :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column name',
							'render' => 'editor_input_text'
						),
						'col_width' => array(
							'label' => 'Column width :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column width Ex. 200px or 20%',
							'render' => 'editor_input_text'
						),
						'date_text' => array(
							'label' => 'Posted on Text:',
							'class' => 'ed_element-wrapper',
							'placeholder' => 'Posted on  :',
							'render' => 'editor_input_text'
						)
					 )
				),
				
				/* Start add_to_cart*/
				'add_to_cart' => array(
					'field_label' => 'Add to cart Button',
					/* Start Field List */
					'field_list' => array(
						'col_name' => array(
							'label' => 'Column Name :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column name',
							'render' => 'editor_input_text'
						),
						'col_width' => array(
							'label' => 'Column width :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column width Ex. 200px or 20%',
							'render' => 'editor_input_text'
						),
						'font_family' => array(
							'label' => 'Font Family :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_font_family'
						),
						'font_size' => array(
							'label' => 'Font Size :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_font_size'
						),
						'color' => array(
							'label' => 'Color :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						),
						'bg' => array(
							'label' => 'Background Color :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						),
						'hr' => array(
							'render' => 'hr'
						),
						'hover_color' => array(
							'label' => 'Hover Color :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						),
						'hover_bg' => array(
							'label' => 'Hover Background Color:',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						),
						'custom_css_class' => array(
							'label' => 'CSS Class',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text'
						)
						
					)
					/* End Field List */
				),
				/* End add_to_cart */
				
				/* Start product_title*/
				'product_title' => array(
					'field_label' => 'Product Title',
					/* Start Field List */
					'field_list' => array(
						'col_name' => array(
							'label' => 'Column Name :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column name',
							'render' => 'editor_input_text'
						),
						'col_width' => array(
							'label' => 'Column width :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column width Ex. 200px or 20%',
							'render' => 'editor_input_text'
						),
						'behavior' => array(
							'label' => 'Title behavior:',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_select',
							'options' => array(
								'Text' => 'Text',
								'link' => 'link'
							)
						),
						'font_family' => array(
							'label' => 'Font Family :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_font_family'
						),
						'font_size' => array(
							'label' => 'Font Size :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_font_size'
						),
						'color' => array(
							'label' => 'Color :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						),
						
						'hr' => array(
							'render' => 'hr'
						),
						'hover_color' => array(
							'label' => 'Hover Color :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						),
					   
						'custom_css_class' => array(
							'label' => 'CSS Class',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text'
						)
						
					)
					/* End Field List */
				),
				/* End product_title */
				
				
				/* Start product_image*/
				'product_image' => array(
					'field_label' => 'Product Image',
					/* Start Field List */
					'field_list' => array(
						'col_name' => array(
							'label' => 'Column Name :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column name',
							'render' => 'editor_input_text'
						),
						'col_width' => array(
							'label' => 'Column width :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column width Ex. 200px or 20%',
							'render' => 'editor_input_text'
						),
						
						'image_effect' => array(
							'label' => 'Image effect :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_select',
							'options' => apply_filters('woo_multi_layout_image_effects',array( 'none' => 'none', 'zoom' => 'ZoomIn' ,'fadein' => 'FadeIn'))
						),
						
						'image_size' => array(
							'label' => 'Image Size :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_select',
							'options' => woo_multi_layout_get_image_sizes_options(),
						),
						
						'custom_css_class' => array(
							'label' => 'CSS Class',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text'
						)
						
					)
					/* End Field List */
				),
				/* End product_image */
				
				 /* Start price*/
				'price' => array(
					'field_label' => 'Product Price',
					/* Start Field List */
					'field_list' => array(
						'col_name' => array(
							'label' => 'Column Name :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column name',
							'render' => 'editor_input_text'
						),
						'col_width' => array(
							'label' => 'Column width :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column width Ex. 200px or 20%',
							'render' => 'editor_input_text'
						),
						'font_size' => array(
							'label' => 'Font Size :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_font_size'
						),
						'color' => array(
							'label' => 'Color :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						),
						'custom_css_class' => array(
							'label' => 'CSS Class',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text'
						)
						
					)
					/* End Field List */
				),
				/* End price */
				
				
				/* Start excerpt*/
				'excerpt' => array(
					'field_label' => 'Product Excerpt',
					/* Start Field List */
					'field_list' => array(
						'col_name' => array(
							'label' => 'Column Name :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column name',
							'render' => 'editor_input_text'
						),
						'col_width' => array(
							'label' => 'Column width :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column width Ex. 200px or 20%',
							'render' => 'editor_input_text'
						),
						'word_limit' => array(
							'label' => 'Word Limit :',
							'class' => 'ed_element-wrapper',
							'placeholder' => '100',
							'render' => 'editor_input_text',
							'type' => 'number',
						),
						
						'font_family' => array(
							'label' => 'Font Family :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_font_family'
						),
						'font_size' => array(
							'label' => 'Font Size :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_font_size'
						),
						'color' => array(
							'label' => 'Color :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						),
						'custom_css_class' => array(
							'label' => 'CSS Class',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text'
						)
						
					)
					/* End Field List */
				),
				/* End excerpt */
				
				/* Start product_content*/
				'product_content' => array(
					'field_label' => 'Product Content',
					/* Start Field List */
					'field_list' => array(
						'col_name' => array(
							'label' => 'Column Name :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column name',
							'render' => 'editor_input_text'
						),
						'col_width' => array(
							'label' => 'Column width :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column width Ex. 200px or 20%',
							'render' => 'editor_input_text'
						),
						'word_limit' => array(
							'label' => 'Word Limit :',
							'class' => 'ed_element-wrapper',
							'placeholder' => '100',
							'render' => 'editor_input_text',
							'type' => 'number',
						),
						
						'font_family' => array(
							'label' => 'Font Family :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_font_family'
						),
						'font_size' => array(
							'label' => 'Font Size :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_font_size'
						),
						'color' => array(
							'label' => 'Color :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						),
					   
						'custom_css_class' => array(
							'label' => 'CSS Class',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text'
						)
						
					)
					/* End Field List */
				),
				/* End product_content */
				
				
				/* Start product_category*/
				'product_category' => array(
					'field_label' => 'Product Category',
					/* Start Field List */
					'field_list' => array(
						'col_name' => array(
							'label' => 'Column Name :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column name',
							'render' => 'editor_input_text'
						),
						'col_width' => array(
							'label' => 'Column width :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column width Ex. 200px or 20%',
							'render' => 'editor_input_text'
						),
						'behavior' => array(
							'label' => 'Title behavior:',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_select',
							'options' => array(
								'link' => 'link',
								'Text' => 'Text'
							)
						),
						'font_family' => array(
							'label' => 'Font Family :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_font_family'
						),
						'font_size' => array(
							'label' => 'Font Size :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_font_size'
						),
						'color' => array(
							'label' => 'Color :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						),
						'hr' => array(
							'render' => 'hr'
						),
						'hover_color' => array(
							'label' => 'Hover Color :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						),
						'custom_css_class' => array(
							'label' => 'CSS Class',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text'
						)
						
					)
					/* End Field List */
				),
				/* End product_category */
				
				 /* Start  product tag */
				'product_tags' => array(
					'field_label' => 'Product Tags',
					/* Start Field List */
					'field_list' => array(
						'col_name' => array(
							'label' => 'Column Name :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column name',
							'render' => 'editor_input_text'
						),
						'col_width' => array(
							'label' => 'Column width :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column width Ex. 200px or 20%',
							'render' => 'editor_input_text'
						),
						'behavior' => array(
							'label' => 'Title behavior:',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_select',
							'options' => array(
								'link' => 'link',
								'Text' => 'Text'
							)
						),
						'font_family' => array(
							'label' => 'Font Family :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_font_family'
						),
						'font_size' => array(
							'label' => 'Font Size :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_font_size'
						),
						'color' => array(
							'label' => 'Color :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						),
						'hr' => array(
							'render' => 'hr'
						),
						'hover_color' => array(
							'label' => 'Hover Color :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						),
						'custom_css_class' => array(
							'label' => 'CSS Class',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text'
						)
						
					)
					/* End Field List */
				),
				/* End product tag */
				/* Start sku*/
				'sku' => array(
					'field_label' => 'SKU',
					/* Start Field List */
					'field_list' => array(
						'col_name' => array(
							'label' => 'Column Name :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column name',
							'render' => 'editor_input_text'
						),
						'col_width' => array(
							'label' => 'Column width :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column width Ex. 200px or 20%',
							'render' => 'editor_input_text'
						),
						'font_size' => array(
							'label' => 'Font Size :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_font_size'
						),
						'color' => array(
							'label' => 'Color :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						),
						'custom_css_class' => array(
							'label' => 'CSS Class',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text'
						)
						
					)
					/* End Field List */
				),
				/* End sku */
				
				'stock' => array(
					'field_label' => 'Stock Quantity',
					'field_list' => array(
						'col_name' => array(
							'label' => 'Column Name :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column name',
							'render' => 'editor_input_text'
						),
						'col_width' => array(
							'label' => 'Column width :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column width Ex. 200px or 20%',
							'render' => 'editor_input_text'
						),
						'font_size' => array(
							'label' => 'Font Size :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_font_size'
						),
						'color' => array(
							'label' => 'Color :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						),
					   'custom_css_class' => array(
							'label' => 'CSS Class',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text'
						)
					 )
				),
				'stock_status' => array(
					'field_label' => 'Stock Status',
					'field_list' => array(
						'col_name' => array(
							'label' => 'Column Name :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column name',
							'render' => 'editor_input_text'
						),
						'col_width' => array(
							'label' => 'Column width :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column width Ex. 200px or 20%',
							'render' => 'editor_input_text'
						),
						'font_size' => array(
							'label' => 'Font Size :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_font_size'
						),
						'color' => array(
							'label' => 'Color :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						),
					   'custom_css_class' => array(
							'label' => 'CSS Class',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text'
						)
					 )
				),
			   /* Start readmore*/
				'readmore' => array(
					'field_label' => 'Read More Button',
					/* Start Field List */
					'field_list' => array(
						'col_name' => array(
							'label' => 'Column Name :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column name',
							'render' => 'editor_input_text'
						),
						'col_width' => array(
							'label' => 'Column width :',
							'class' => 'ed_element-wrapper for-table',
							'placeholder' => 'Column width Ex. 200px or 20%',
							'render' => 'editor_input_text'
						),
						'text' => array(
							'label' => 'Text :',
							'class' => 'ed_element-wrapper',
							'placeholder' => 'Read More',
							'render' => 'editor_input_text'
						),
						
						'font_family' => array(
							'label' => 'Font Family :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_font_family'
						),
						'font_size' => array(
							'label' => 'Font Size :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_font_size'
						),
						'color' => array(
							'label' => 'Color :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						),
						'bg' => array(
							'label' => 'Background Color :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						),
						'hr' => array(
							'render' => 'hr'
						),
						'hover_color' => array(
							'label' => 'Hover Color :',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						),
						'hover_bg' => array(
							'label' => 'Hover Background Color:',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						),
						'custom_css_class' => array(
							'label' => 'CSS Class',
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text'
						)
						
					)
					/* End Field List */
				),
				/* End readmore */
				/* Start line_break*/
				'line_break' => array(
					'field_label' => 'Line Break',
					/* Start Field List */
					'field_list' => array(
						'size' => array(
							'label' => 'Size :',
							'class' => 'ed_element-wrapper',
							'placeholder' => 'Ex. 2px',
							'render' => 'editor_input_text'
						),
						'margin_top' => array(
							'label' => 'Margin Top :',
							'class' => 'ed_element-wrapper',
							'placeholder' => 'Ex. 2px',
							'render' => 'editor_input_text'
						),
						'margin_bottom' => array(
							'label' => 'Margin Bottom :',
							'class' => 'ed_element-wrapper',
							'placeholder' => 'Ex. 2px',
							'render' => 'editor_input_text'
						),
						
						'color' => array(
							'label' => __('Color :' ,'ED_MULTI_LANG'),
							'class' => 'ed_element-wrapper',
							'render' => 'editor_input_text',
							'relation_class' => 'ed_picker_color'
						)
						
						
					)
					/* End Field List */
				)
				/* End line_break */
			));
			
			return $fields;
		}
		/**
		 * Render  editor html output.
		 * @param  array  $fields   
		 * @return do_action output.
		 */
		public function woo_multi_layout_editor($post_id) {
			$fields = $this->woo_mulit_layout_element();
			
			if(!is_array($this->default)){  $this->default = array();}
			
	?>
	
	<div class="ed__aside__element">
	  <h3><?php echo __('Product Elements' ,'ED_MULTI_LANG');?></h3>
	  <ul id="sortable1" class="sortable_sidebar connectedSortable vertical">
		<?php
			
			foreach ($fields as $key => $field):
				
				if ( !in_array($key, $this->default)):?>
		<li data-action="<?php  echo $key;?>" class="<?php echo $key;?>">
		  <div class="ed__element__head"> <?php echo $field["field_label"];?>
			<button type="button" class="handlediv button-link" aria-expanded="true"><span class="toggle-indicator" aria-hidden="true"></span></button>
		  </div>
		  <?php
				   // $this->editor_action($this->settings, $key);
					if (isset($field['field_list']) && count($field['field_list']) > 0):
						echo '<div class="ed__load__element__attribute"><div class="ed_element-body" style="display: block;">';
						foreach ($field['field_list'] as $element_key => $element) {
							$input_key_name = $this->settings . '[' . $key . '][' . $element_key . ']';
							
							do_action('render_editor_input_element', $input_key_name, $element);
						} //$field['field_list'] as $element_key => $element
						echo '</div></div>';
					endif;?>
		</li>
		<?php
				endif;
			endforeach;?>
	  </ul>
	</div>
	<div class="ed__wrapper__editor__zone">
	  <ul id="sortable2" class="connectedSortable vertical ed__sortable__editor">
		<?php
			if (count($this->default) > 0):
				foreach ($this->default as $default_key => $none_use_value):?>
		<li data-action="<?php echo $default_key;?>" class="<?php echo $default_key; ?>">
		  <div class="ed__element__head"> <?php echo $fields[$default_key]["field_label"];?>
			<button type="button" class="handlediv button-link" aria-expanded="true"><span class="toggle-indicator" aria-hidden="true"></span></button>
		  </div>
		  <?php
					if (isset($fields[$default_key]['field_list']) && count($fields[$default_key]['field_list']) > 0):
						echo '<div class="ed__load__element__attribute"><div class="ed_element-body" style="display: block;">';
						foreach ($fields[$default_key]['field_list'] as $default_element_key => $default_element) {
							$default_input_key_name = $this->settings . '[' . $default_key . '][' . $default_element_key . ']';
							$default_element['name'] = true;
							$default_element['meta_value'] = (isset($this->default[$default_key][$default_element_key]))? $this->default[$default_key][$default_element_key] : '';
							//if( is_array( $default_element['meta_value'] ) ) continue ;
						
							do_action('render_editor_input_element', $default_input_key_name, $default_element);
						} //$fields[$default_key]['field_list'] as $default_element_key => $default_element
						echo '</div></div>';
					endif;
	echo '<a href="javascript:void(0)" class="item_delete woo_item_delete">DELETE</a></li>';				
				endforeach;
			endif;
	?>
	  </ul>
	</div>
	<?php
			
		}
		/**
		 * Render editor single element.
		 * @param  array  $input_key_name    Shortcode attributes.
		 * @param  string $element 
		 * @return html output.
		 */
		public function render_editor_input_element($input_key_name, $element) {
			
			if ($element['render'] != "") {
				switch ($element['render']) {
					case 'editor_input_text':
						apply_filters('ed_editor_input_text', $this->editor_input_text($input_key_name, $element));
						break;
					case 'editor_input_select':
						apply_filters('ed_editor_input_select', $this->editor_input_select($input_key_name, $element));
						break;
					case 'hr':
						$this->hr($element);
						break;
					case 'editor_font_size':
						apply_filters('ed_editor_font_size', $this->editor_font_size($input_key_name, $element));
						break;
					case 'editor_font_family':
						apply_filters('editor_font_family', $this->editor_font_family($input_key_name, $element));
						break;
					case 'editor_checkbox':
						apply_filters('ed_editor_checkbox', $this->editor_checkbox($input_key_name, $element));
						break;
					 case apply_filters('ed_editor_elment_custom_switch', 'ed_editor_elment_custom_switch'):
						apply_filters('ed_editor_custom_elment');
						break;
					default:
						
				} //$element['render']
			} //$element['render'] != ""
		}
		/**
		 * @access render editor_action
		 */
		private function editor_action($id, $value) {
			$option_value   = isset($array['meta_value']) ? $array['meta_value'] : '';
			printf('<input type="hidden" id="%1$s" name="%2$s" value="%3$s" />', $id, $name, $value);
			
		}
		/**
		 * @access render hr
		 */
		private function hr($array = array()) {
			$class = isset($array['class']) ? $array['class'] : '';
			echo ' <hr class="' . $class . '">';
		}
		/**
		 * @access render editor_input_text
		 */
		private function editor_input_text($key, $array = array() ) {
			$class          = isset($array['class']) ? $array['class'] : '';
			$id             = isset($key) ? $key : '';
			$label          = isset($array['label']) ? $array['label'] : '';
			$placeholder    = isset($array['placeholder']) ? $array['placeholder'] : '';
			$relation_class = isset($array['relation_class']) ? $array['relation_class'] : '';
			$value          = isset( $array['meta_value'] ) ? $array['meta_value'] : '';
			$name			= isset( $array['name'] )? $key :'';
			$type          = isset( $array['type'] ) ? $array['type'] : 'text';
		
			printf('<label class="%1$s" for="%2$s">
			<span class="ed_element-label"> %4$s</span>
			<input class="%5$s" id="%2$s" name="%3$s" placeholder="%6$s" value="%7$s" type="%8$s">
			<div class="clr"></div>
			</label>', $class, $id, $name, $label, $relation_class, $placeholder, $value, $type);
		}
		/**
		 * @access render editor_input_select
		 */
		private function editor_input_select($key, $array = array()) {
			$class          = isset($array['class']) ? $array['class'] : '';
			$id             = isset($key) ? $key : '';
			$label          = isset($array['label']) ? $array['label'] : '';
			$placeholder    = isset($array['placeholder']) ? $array['placeholder'] : '';
			$options        = isset($array['options']) ? $array['options'] : array();
			$relation_class = isset($array['relation_class']) ? $array['relation_class'] : '';
			$option_value   = isset($array['meta_value']) ? $array['meta_value'] : '';
			$name			= isset( $array['name'] )? $key :'';
			
			printf('<label class="%1$s" for="%2$s">
			<span class="ed_element-label"> %3$s </span>
			<select id="%2$s" name="%4$s" class="%5$s">', $class, $id, $label, $name, $relation_class);
			
			foreach ($options as $key => $value) {
				printf('<option value="%1$s" %3$s>%2$s</option>', $key, $value, selected( $key, $option_value ));
			} //$options as $key => $value
			
			printf('</select>
			<div class="clr"></div>
			</label>');
			
			
		}
		/**
		 * @access render editor_font_size
		 */
		private function editor_font_size($key, $array = array()) {
			$class          = isset($array['class']) ? $array['class'] : '';
			$id             = isset($key) ? $key : '';
			$label          = isset($array['label']) ? $array['label'] : '';
			$placeholder    = isset($array['placeholder']) ? $array['placeholder'] : '';
			$option         = isset($array['option']) ? $array['option'] : array();
			$relation_class = isset($array['relation_class']) ? $array['relation_class'] : '';
			$option_value   = isset($array['meta_value']) ? $array['meta_value'] : 13;
			$name			= isset( $array['name'] )? $key :'';
			
			 printf('<label class="%1$s" for="%2$s">
			<span class="ed_element-label"> %3$s </span>
			<select id="%2$s" name="%4$s" class="%5$s">', $class, $id, $label, $name, $relation_class);
			
			for ($i = 11; $i <= 100; $i++) {
				printf('<option value="%1$s" %2$s>%1$s px</option>', $i,  selected( $i, $option_value ));
			} //$i = 9; $i <= 100; $i++
			printf('</select>
			<div class="clr"></div>
			</label>');
		}
		
		
		/**
		 * @access render editor_font_family
		 */
		
		private function editor_font_family($key, $array = array()) {
			$class          = isset($array['class']) ? $array['class'] : '';
			$id             = isset($key) ? $key : '';
			$label          = isset($array['label']) ? $array['label'] : '';
			$placeholder    = isset($array['placeholder']) ? $array['placeholder'] : '';
			$option         = isset($array['option']) ? $array['option'] : array();
			$relation_class = isset($array['relation_class']) ? $array['relation_class'] : '';
			$option_value   = isset($array['meta_value']) ? $array['meta_value'] : '';
			$name			= isset( $array['name'] )? $key :'';
			
			$request     = wp_remote_get(ED_MULTI_URL . '/assets/google-fonts.json');
			$response    = wp_remote_retrieve_body($request);
			$google_json = json_decode($response);
			
			  printf('<label class="%1$s" for="%2$s">
			<span class="ed_element-label"> %3$s </span>
			<select id="%2$s" name="%4$s" class="%5$s">', $class, $id, $label, $name, $relation_class);
			 printf('<option value="none">None</option>');
		  
			foreach ($google_json->items as $key => $font) {
				$selected = '';
				printf('<option value="%1$s" %2$s>%1$s </option>', $font->family, selected( $font->family, $option_value ));
				
			} //$google_json->items as $key => $font
			printf('</select>
			<div class="clr"></div>
			</label>');
		}
		/**
		 * @access render editor_checkbox
		 */
		private function editor_checkbox($key, $array = array()) {
			$class          = isset($array['class']) ? $array['class'] : '';
			$id             = isset($key) ? $key : '';
			$label          = isset($array['label']) ? $array['label'] : '';
			$placeholder    = isset($array['placeholder']) ? $array['placeholder'] : '';
			$relation_class = isset($array['relation_class']) ? $array['relation_class'] : '';
			$options        = isset($array['options']) ? $array['options'] : array();
			$option_value   = isset($array['meta_value']) ? $array['meta_value'] : '';
			$name			= isset( $array['name'] )? $key :'';
			
			
			printf('<div class="%1$s"><span class="ed_element-label"> %2$s </span>', $class, $label);
			
			
			foreach ($options as $k => $value) {
				$id_create		   = $id . '[' . $k . ']';
				$check_box_name	   = $name . '[' . $k . ']';
				$checked   = ( is_array( $option_value ) && in_array( $k, $option_value ) ) ? 'checked="checked"' : '';
				
				printf('<label for="%1$s">
				<input id="%1$s" name="%2$s" value="%3$s" type="checkbox" %4$s> %3$s
				</label>', $id_create, $check_box_name, $k, $checked);
				
			} //$options as $k => $value
			
			
			printf('<div class="clr"></div>
			</div>');
		}
		
		
		
	}


}


if ( ! function_exists( 'woo_multi_layout_get_image_sizes_options' ) ) :

	/**
	 * Returns image sizes options.
	 *
	 * @since 1.0.0
	 *
	 * @param bool  $add_disable    True for adding No Image option.
	 * @param array $allowed        Allowed image size options.
	 * @param bool  $show_dimension True for showing dimension.
	 * @return array Image size options.
	 */
	function woo_multi_layout_get_image_sizes_options( $add_disable = true, $allowed = array(), $show_dimension = true ) {

		global $_wp_additional_image_sizes;

		$choices = array();

		if ( true === $add_disable ) {
			$choices['disable'] = esc_html__( 'No Image', 'ED_MULTI_LANG' );
		}

		$choices['thumbnail'] = esc_html__( 'Thumbnail', 'ED_MULTI_LANG' );
		$choices['medium']    = esc_html__( 'Medium', 'ED_MULTI_LANG' );
		$choices['large']     = esc_html__( 'Large', 'ED_MULTI_LANG' );
		$choices['full']      = esc_html__( 'Full (original)', 'ED_MULTI_LANG' );

		if ( true === $show_dimension ) {
			foreach ( array( 'thumbnail', 'medium', 'large' ) as $key => $_size ) {
				$choices[ $_size ] = $choices[ $_size ] . ' (' . get_option( $_size . '_size_w' ) . 'x' . get_option( $_size . '_size_h' ) . ')';
			}
		}

		if ( ! empty( $_wp_additional_image_sizes ) && is_array( $_wp_additional_image_sizes ) ) {
			foreach ( $_wp_additional_image_sizes as $key => $size ) {
				$choices[ $key ] = $key;
				if ( true === $show_dimension ) {
					$choices[ $key ] .= ' (' . $size['width'] . 'x' . $size['height'] . ')';
				}
			}
		}

		if ( ! empty( $allowed ) ) {
			foreach ( $choices as $key => $value ) {
				if ( ! in_array( $key, $allowed, true ) ) {
					unset( $choices[ $key ] );
				}
			}
		}

		return $choices;

	}

endif;