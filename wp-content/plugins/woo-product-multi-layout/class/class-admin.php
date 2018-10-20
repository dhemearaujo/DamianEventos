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
if( !class_exists('Woo_Multi_Layout_Admin_Manager') ){
	class Woo_Multi_Layout_Admin_Manager {
		/**
		 * @var array function
		 */
		protected $loader;
		/**
		 * @var striang
		 */
		protected $version;
		/**
		 * @var striang
		 */
		protected $path;
		/**
		 * @var striang
		 */
		protected $url;
		/**
		 * @var striang
		 */
		protected $post_type;
		/**
		 * @var striang
		 */
		protected $prefix;
	
		/**
		 * @var array
		 */
		protected $meta_settings;
		/**
		 * Initialize all controllers, by loading Plugin and User Options.
		 */
		public function __construct() {
			$this->version = '1.0';
			$this->path = ED_MULTI_PATH;
			$this->url = ED_MULTI_URL;
			$this->post_type = 'woo_multii_layout';
			$this->prefix = ED_MULTI_PREFIX;
	
			$this->meta_settings = array('template' => 'woo_multii_layout_initial', 'product' => 'woo_multii_layout_product_query');
	
			$this->load_dependencies();
			$this->define_admin_hooks();
		}
		/**
		 * dependencies class all .
		 */
		private function load_dependencies() {
	
			require_once $this->path . 'class/class-loader.php';
			$this->loader = new woo_product_table_view_loader();
	
			require_once $this->path . 'class/class-editor.php';
	
		}
		/**
		 * Register the Custom Post Type which the tables use.
		 */
		public function register() {
			$labels = array(
				'name' => __('Woo Multi Layout', 'ED_MULTI_LANG'),
				'singular_name' => __('Woo Multi Layout', 'ED_MULTI_LANG'),
				'add_new' => __('Add New Layout', 'ED_MULTI_LANG'),
				'add_new_item' => __('Add New Layout', 'ED_MULTI_LANG'),
				'edit_item' => __('Edit Layout', 'ED_MULTI_LANG'),
				'new_item' => __('New Layout', 'ED_MULTI_LANG'),
				'view_item' => __('View Layout', 'ED_MULTI_LANG'),
				'search_items' => __('Search Layout', 'ED_MULTI_LANG'),
				'not_found' => __('No Items found', 'ED_MULTI_LANG'),
				'not_found_in_trash' => __('No Items found in Trash', 'ED_MULTI_LANG'),
				'parent_item_colon' => '',
				'menu_name' => __('Woo Multi Layout', 'ED_MULTI_LANG'),
			);
			// Set other options for Custom Post Type
	
			$args = array(
				'labels' => $labels,
				// Features this CPT supports in Post Editor
				'supports' => array('title'),
	
				/* A hierarchical CPT is like Pages and can have
					* Parent and child items. A non-hierarchical CPT
					* is like Posts.
				*/
				'hierarchical' => true,
				'public' => false,
				'show_ui' => true,
				'show_in_menu' => true,
				'show_in_nav_menus' => false,
				'menu_position' => 50,
				'can_export' => true,
				'has_archive' => false,
				'exclude_from_search' => false,
				'publicly_queryable' => false,
				'capability_type' => 'page',
			);
			// Registering your Custom Post Type
			register_post_type($this->post_type, $args);
		}
		/**
		 * Register the Custom Post new Columns
		 */
		public function new_columns() {
			$columns['cb'] = '<input type="checkbox" />';
			$columns['title'] = __('Title', 'ED_MULTI_LANG');
			$columns['template'] = __('Layout', 'ED_MULTI_LANG' );
			$columns['how_to_use'] = __('Usage shortcode', 'ED_MULTI_LANG' );
			$columns['author'] = __('Author', 'ED_MULTI_LANG' );
			$columns['date'] = __('Date', 'ED_MULTI_LANG');
			return $columns; 
		}
		/**
		 * Register the Custom Post new Columns Data
		 */
		public function columns_data($columns) {
			global $post;
			switch ($columns) {
			case 'template':
				$value = maybe_unserialize(get_post_meta($post->ID, $this->meta_settings['template'], true));
				
				if( isset( $value['template_list'] ) && $value['template_list'] !=  "" ){
					$template_list = $this->default_template_list();
					if( isset( $template_list[$value['template_list']]['thumbnail'] ) ){
						echo '<img src="'.$template_list[$value['template_list']]['thumbnail'].'" class="reduce_img" />';
					}
				}
				
			break;	
			case 'how_to_use':
				echo '<input type="text" value="[woo_mulit_layout id='.$post->ID.']" readonly="readonly" style="text-align:center; width:80%;" size="120px;"  onfocus="this.select();" onmouseup="return false;" />';
			break;
		
			
			}
		}	
		/**
		 * Register All Meta Box Options.
		 */
		public function multi_layout_creator() {
			add_meta_box(
				'woo__multi__select__template__metabox',
				__(' Select Template <a target="_blank" class="wpd_free_pro" title="'.__( 'Unlock more features with PRO!', 'ED_MULTI_LANG' ).'" href="'. ED_PRO_LINK .'"><span style="color:#f05f40;font-size:15px; font-weight:400; float:right; padding-right:14px;"><span class="dashicons dashicons-lock"></span> '.__( 'Free version', 'ED_MULTI_LANG' ).'</span></a>', 'ED_MULTI_LANG'),
				array($this, 'multi_layout_template_callback'),
				$this->post_type,
				'normal',
				'high'
			);
			add_meta_box(
				'woo__multi__product__query_options__metabox',
				__('Product Query options <a target="_blank" class="wpd_free_pro" title="'.__( 'Unlock more features with PRO!', 'ED_MULTI_LANG' ).'" href="'. ED_PRO_LINK .'"><span style="color:#f05f40;font-size:15px; font-weight:400; float:right; padding-right:14px;"><span class="dashicons dashicons-lock"></span> '.__( 'Free version', 'ED_MULTI_LANG' ).'</span></a>', 'ED_MULTI_LANG'),
				array($this, 'multi_layout_product_query_callback'),
				$this->post_type,
				'normal',
				'high'
			);
			add_meta_box(
				'woo__multi__layout__editor__metabox',
				__('Create layout elements <a target="_blank" class="wpd_free_pro" title="'.__( 'Unlock more features with PRO!', 'ED_MULTI_LANG' ).'" href="'. ED_PRO_LINK .'"><span style="color:#f05f40;font-size:15px; font-weight:400; float:right; padding-right:14px;"><span class="dashicons dashicons-lock"></span> '.__( 'Free version', 'ED_MULTI_LANG' ).'</span></a>', 'ED_MULTI_LANG'),
				array($this, 'multi_layout_editor_callback'),
				$this->post_type,
				'normal',
				'high'
			);
			add_meta_box(
				'woo__multi__layout__free__metabox',
				__('PRO version', 'ED_MULTI_LANG'),
				array($this, 'pro_callback'),
				$this->post_type,
				'side',
				'low'
			);
			add_meta_box(
				'woo__multi__layout__usage__metabox',
				__('Usage', 'ED_MULTI_LANG'),
				array($this, 'usage_callback'),
				$this->post_type,
				'side',
				'low'
			);
			
			add_meta_box(
				'woo__multi__you_may_like__metabox',
				__('You May Like', 'ED_MULTI_LANG'),
				array($this, 'you_may_like'),
				$this->post_type,
				'side',
				'low'
			);
			
			
		}
		/**
		 * Metabox ( multi_layout_template_callback ) Callback function
		 *
		 * @param array $post
		 */
		public function multi_layout_editor_callback($post) {
			wp_nonce_field(basename(__FILE__), 'multi_layout_editor_nonce');
			$postid = (isset($post->ID)) ? $post->ID : 0;
			$editor = new Eds_Input_Drag_Drop_Editor($postid);
			?>
		  <div id="multi__layout__element__wrp">
			<?php do_action('woo_multi_layout_editor');?>
		  </div>
		<?php
		}
		/**
		 * default_template_list
		 *
		 * @param array 
		 */
		public function default_template_list(){
			
			return apply_filters('woo_multi_layout_get_template_list', array(
				'template-grid.php' => array(
					'label' => __('Grid', 'ED_MULTI_LANG'),
					'thumbnail' => $this->url . '/assets/admin/images/template-grid.png',
					'template' => 'template-grid.php',
				),
				'template-masonry-grid.php' => array(
					'label' => __('Masonry Grid', 'ED_MULTI_LANG'),
					'thumbnail' => $this->url . '/assets/admin/images/masonry-grid.png',
					'template' => 'template-masonry-grid.php',
				),
				
				'template-list.php' => array(
					'label' => __('List', 'ED_MULTI_LANG'),
					'thumbnail' => $this->url . '/assets/admin/images/template-list.png',
					'template' => 'template-list.php',
				),
				'template-listbox.php' => array(
					'label' => __('List And Box', 'ED_MULTI_LANG'),
					'thumbnail' => $this->url . '/assets/admin/images/template-listbox.png',
					'template' => 'template-listbox.php',
				),
				'template-oddeven.php' => array(
					'label' => __('List', 'ED_MULTI_LANG'),
					'thumbnail' => $this->url . '/assets/admin/images/template-oddeven.png',
					'template' => 'template-oddeven.php',
				),
				'template-table.php' => array(
					'label' => __('Table', 'ED_MULTI_LANG'),
					'thumbnail' => $this->url . '/assets/admin/images/template-table.png',
					'template' => 'template-table.php',
				),
				'template-boxCarousel.php' => array(
					'label' => __('Table', 'ED_MULTI_LANG'),
					'thumbnail' => $this->url . '/assets/admin/images/template-boxCarousel.png',
					'template' => 'template-boxCarousel.php',
				),
				'template-slider.php' => array(
					'label' => __('Table', 'ED_MULTI_LANG'),
					'thumbnail' => $this->url . '/assets/admin/images/template-slider.png',
					'template' => 'template-slider.php',
				),
			));
			
		}
		/**
		 * Metabox ( multi_layout_product_query_callback ) Callback function
		 *
		 * @param array $post
		 */
		public function multi_layout_template_callback($post) {
			if (!isset($this->meta_settings['template'])) {
				return false;
			}
	
			wp_nonce_field(basename(__FILE__), $this->meta_settings['template'] . '__nonce');
			/**
			 * filters All Template list
			 */
			$template_list = $this->default_template_list();
	
			$value = maybe_unserialize(get_post_meta($post->ID, $this->meta_settings['template'], true));
			$template = isset($value['template_list']) ? $value['template_list'] : 'template-grid.php';
	
			?>
		<ul id="ed__multi__layout__template__list" class="ed_selectable">
		  <?php $i=0; foreach ($template_list as $key => $info): $i++ ?>
			 <?php if( $i == 1 || $i == 5 || $i == 4): ?>
             <li class="go <?php echo ($template == $info['template']) ? 'ed-selected' : ''; ?>"> 
			  <label for="<?php echo $key; ?>"> <img src="<?php echo $info['thumbnail']; ?>" />
				<input id="<?php echo $key; ?>" type="radio" name="<?php echo $this->meta_settings['template']; ?>[template_list]" value="<?php echo $info['template']; ?>"  <?php echo ($template == $info['template']) ? 'checked="checked"' : ''; ?> />
				<input type="hidden" value="<?php echo $key;?>" name="<?php echo $this->meta_settings['template']; ?>[group]" />
			  </label>
              <?php else: ?>
              <li class="<?php echo ($template == $info['template']) ? 'ed-selected' : ''; ?>"> 
               <label class="pro" for="<?php echo $key; ?>"> <img src="<?php echo $info['thumbnail']; ?>" />
              	 <span><a href="<?php echo esc_url ( ED_PRO_LINK );?>"><?php _e('Pro version', 'ED_MULTI_LANG');?></a></span>
			  </label>
              <?php endif;?>
		  <?php endforeach;?>
		</ul>
    
		<table class="form-table <?php echo ( $template == "template-slider.php" ) ? 'ed__woo_style__table hide_all_once': '';?>">
		  <?php $table_heading_color = isset($value['table_heading_color']) ? $value['table_heading_color'] : '';?>
		  <tr class="for-table <?php echo ($template == "template-table.php")? 'ed_show':'';?>">
			<td width="25%"><?php _e('Table Heading Color:', 'ED_MULTI_LANG');?></td>
			<td><input name="<?php echo $this->meta_settings['template']; ?>[table_heading_color]" type="text" class="ed_picker_color" value="<?php echo $table_heading_color; ?>"></td>
		  </tr>
		  <?php $table_heading_bg = isset($value['table_heading_bg']) ? $value['table_heading_bg'] : '';?>
		  <tr class="for-table <?php echo ($template == "template-table.php")? 'ed_show':'';?>">
			<td><?php _e('Table Heading background Color:', 'ED_MULTI_LANG');?></td>
			<td><input name="<?php echo $this->meta_settings['template']; ?>[table_heading_bg]" type="text" class="ed_picker_color" value="<?php echo $table_heading_bg; ?>"></td>
		  </tr>
		  <?php $table_heading_font_size = isset($value['table_heading_font_size']) ? $value['table_heading_font_size'] : '14';?>
		  <tr class="for-table <?php echo ($template == "template-table.php")? 'ed_show':'';?>">
			<td><?php _e('Table Heading Font Size:', 'ED_MULTI_LANG');?></td>
			<td>
				<select name="<?php echo $this->meta_settings['template']; ?>[table_heading_font_size]">
				<?php
				  for ($i = 9; $i <= 100; $i++) {
						 printf('<option value="%1$s" %2$s>%1$s px</option>', $i,  selected( $i, $table_heading_font_size ));
					 } 
				?>
				</select>
			</td>
		  </tr>
		  <?php $primary = isset($value['primary']) ? $value['primary'] : '';?>
		  <tr class="use_for_common">
			<td width="25%"><?php _e('Pirmary Color <i class="ed_small">(  box / list / Table Columns background Color)</i> :', 'ED_MULTI_LANG');?></td>
			<td><input name="<?php echo $this->meta_settings['template']; ?>[primary]" type="text" class="ed_picker_color" value="<?php echo $primary; ?>"></td>
		  </tr>
		  <?php $primary_hover = isset($value['primary_hover']) ? $value['primary_hover'] : '';?>
		  <tr class="use_for_common">
			<td><?php _e('Pirmary Color Hover <i class="ed_small">(  box / list / Table Columns background Color)</i> :', 'ED_MULTI_LANG');?></td>
			<td><input name="<?php echo $this->meta_settings['template']; ?>[primary_hover]" type="text" value="<?php echo $primary_hover; ?>" class="ed_picker_color"></td>
		  </tr>
		  <?php $boder_color = isset($value['boder_color']) ? $value['boder_color'] : '';?>
		  <tr class="use_for_common">
			<td><?php _e('Border Color :', 'ED_MULTI_LANG');?></td>
			<td><input name="<?php echo $this->meta_settings['template']; ?>[boder_color]" type="text" value="<?php echo $boder_color; ?>" class="ed_picker_color"></td>
		  </tr>
		  <?php $hover_boder_color = isset($value['hover_boder_color']) ? $value['hover_boder_color'] : '';?>
		  <tr class="use_for_common">
			<td><?php _e('Border Hover Color :', 'ED_MULTI_LANG');?></td>
			<td><input name="<?php echo $this->meta_settings['template']; ?>[hover_boder_color]" value="<?php echo $hover_boder_color; ?>" type="text" class="ed_picker_color"></td>
		  </tr>
		  <?php $boder_color_size = isset($value['boder_color_size']) ? $value['boder_color_size'] : '1';?>
		  <tr class="use_for_common">
			<td><?php _e('Border Size :', 'ED_MULTI_LANG');?></td>
			<td><input  min="1" name="<?php echo $this->meta_settings['template']; ?>[boder_color_size]" value="<?php echo $boder_color_size; ?>" type="number"></td>
		  </tr>
		  <?php $padding = isset($value['padding']) ? $value['padding'] : '10';?>
		  
		   <?php $number_of_list = isset($value['number_of_list']) ? $value['number_of_list'] : 2;?>
		  <tr class="use_for_list_layout <?php echo ( $template == "template-list.php"  || $template == "template-listbox.php" )? 'ed_show':'';?>">
			<td><?php _e('Number of List Item\'s  :', 'ED_MULTI_LANG');?></td>
			<td><input name="<?php echo $this->meta_settings['template']; ?>[number_of_list]" value="<?php echo $number_of_list; ?>" type="number" min="1" max="9"></td>
		  </tr>
          
          
		  <?php $columns = isset($value['columns']) ? $value['columns'] : 3;?>
		  <tr class="use_for_grid_layout <?php echo ( $template == "template-grid.php"  || $template == "template-listbox.php" || $template == "template-oddeven.php"  || $template == "template-masonry-grid.php" )? 'ed_show':'';?>">
			<td><?php _e('Number of columns  :', 'ED_MULTI_LANG');?> </td>
			<td><input name="<?php echo $this->meta_settings['template']; ?>[columns]" value="<?php echo $columns; ?>" type="number" min="1" max="9"></td>
		  </tr>
           <?php $tb_columns = isset($value['tb_columns']) ? $value['tb_columns'] : 2;?>
		  <tr class="use_for_grid_layout <?php echo ( $template == "template-grid.php"  || $template == "template-listbox.php" || $template == "template-oddeven.php" || $template == "template-masonry-grid.php"  )? 'ed_show':'';?>">
			<td><?php _e('Number of columns (tablet)  :', 'ED_MULTI_LANG');?> </td>
			<td><input name="<?php echo $this->meta_settings['template']; ?>[tb_columns]" value="<?php echo $tb_columns; ?>" type="number" min="1" max="5"></td>
		  </tr>
           <?php $mb_columns = isset($value['mb_columns']) ? $value['mb_columns'] : 1;?>
		  <tr class="use_for_grid_layout <?php echo ( $template == "template-grid.php"  || $template == "template-listbox.php" || $template == "template-oddeven.php" || $template == "template-masonry-grid.php"  )? 'ed_show':'';?>">
			<td><?php _e('Number of columns (Mobile)  :', 'ED_MULTI_LANG');?> </td>
			<td><input name="<?php echo $this->meta_settings['template']; ?>[mb_columns]" value="<?php echo $mb_columns; ?>" type="number" min="1" max="2"></td>
		  </tr>
          
          
		   <?php $filter = isset($value['filter']) ? $value['filter'] : 'true';?>
		   <tr>
			<td><?php _e('Frontend Order by filter', 'ED_MULTI_LANG');?></td>
			<td>
				<select name="<?php echo $this->meta_settings['template']; ?>[filter]">
				  <option value="true" <?php selected( 'true', $filter); ?>>
				  <?php _e('Enable', 'ED_MULTI_LANG');?>
				  </option>
				  <option value="false" <?php selected( 'false', $filter); ?>>
				  <?php _e('Disable', 'ED_MULTI_LANG');?>
				  </option>
				</select>
			</td>
		  </tr>
		  <?php $pagination = isset($value['pagination']) ? $value['pagination'] : 'true';?>
		  <tr>
			<td width="25%"><?php _e('Frontend pagination', 'ED_MULTI_LANG');?></td>
			<td>
				<select name="<?php echo $this->meta_settings['template']; ?>[pagination]">
				  <option value="true" <?php selected( 'true', $pagination); ?>>
				  <?php _e('Enable', 'ED_MULTI_LANG');?>
				  </option>
				  <option value="false" <?php selected( 'false', $pagination); ?>>
				  <?php _e('Disable', 'ED_MULTI_LANG');?>
				  </option>
				</select>
			</td>
		  </tr>
		 
		   
		</table>
		<?php
	}
		/**
		 * Metabox ( multi_layout_product_query_callback ) Callback function
		 *
		 * @param array $post
		 */
		public function multi_layout_product_query_callback($post) {
			if (!isset($this->meta_settings['product'])) {
				return false;
			}
			$meta_name = $this->meta_settings['product'];
			wp_nonce_field(basename(__FILE__), $meta_name . '__nonce');
			$value = maybe_unserialize(get_post_meta($post->ID, $this->meta_settings['product'], true));
	
			?>
		<table class="form-table ed__product_settings__actions">
		  <tr>
			<td width="30%"><?php _e('Select Product :', 'ED_MULTI_LANG');?></td>
			<td><?php $product_query = isset($value['product_query']) ? $value['product_query'] : 'all';
			$product_query = ($product_query == 'all') ? 'all' : 'custom';?>
			  <label for="ed_product_type_all">
				<input  id="ed_product_type_all" name="<?php echo $meta_name; ?>[product_query]" value="all"  type="radio" <?php echo checked('all', $product_query); ?>>
				  <?php _e('All Product :', 'ED_MULTI_LANG');?>
			  </label>
			  <label for="product_type_selected">
				<input id="product_type_selected" name="<?php echo $meta_name; ?>[product_query]" value="custom" type="radio" <?php echo checked('custom', $product_query); ?>>
				<?php _e(' Select Product:', 'ED_MULTI_LANG');?>
			  </label>
			  <?php $product_query_custom = (isset($value['product_query_custom']) && is_array($value['product_query_custom'])) ? $value['product_query_custom'] : array();?>
			  <div class="choose_select_form_list <?php echo ($product_query == 'custom') ? 'active' : ''; ?>">
				<select  class="ed_filter_able_select" name="<?php echo $meta_name; ?>[product_query_custom][]" multiple size="3">
				  <option value="featured" <?php echo (in_array('featured', $product_query_custom)) ? 'selected="selected"' : ''; ?>>
				  <?php _e('Featured Product', 'ED_MULTI_LANG');?>
				  </option>
				  <option value="sales" <?php echo (in_array('sales', $product_query_custom)) ? 'selected="selected"' : ''; ?>>
				  <?php _e('Sales Product', 'ED_MULTI_LANG');?>
				  </option>
				  <option value="instock" <?php echo (in_array('instock', $product_query_custom)) ? 'selected="selected"' : ''; ?>>
				  <?php _e('In Stock Product', 'ED_MULTI_LANG');?>
				  </option>
				  <option value="outofstock" <?php echo (in_array('outofstock', $product_query_custom)) ? 'selected="selected"' : ''; ?>>
				  <?php _e(' Out of Stock Product', 'ED_MULTI_LANG');?>
				  </option>
				</select>
			  </div>
			</td>
		  </tr>
		  <tr>
			<td><?php _e('Filter by category :', 'ED_MULTI_LANG');?></td>
			<td>
			  <?php $category_type = isset($value['category_type']) ? $value['category_type'] : 'all';
			$category_type = ($category_type == 'all') ? 'all' : 'custom';
			?>
			  <label for="ed_category_type_all">
				<input checked="checked" id="ed_category_type_all" name="<?php echo $meta_name; ?>[category_type]" value="all"  <?php echo checked('all', $category_type); ?> type="radio">
				<?php _e('All Category :', 'ED_MULTI_LANG');?>
			  </label>
			  <label for="category_type_selected">
				<input id="categoryt_type_selected" name="<?php echo $meta_name; ?>[category_type]"  value="custom"  type="radio" <?php echo checked('custom', $category_type); ?>>
				<?php _e(' Select Category :', 'ED_MULTI_LANG');?>
			  </label>
			  <?php
	$args = array('hide_empty' => false);
			$product_cat = get_terms('product_cat', $args);
			$category_type_custom = (isset($value['category_type_custom']) && is_array($value['category_type_custom'])) ? $value['category_type_custom'] : array();
			?>
			  <div class="choose_select_form_list <?php echo ($category_type == 'custom') ? 'active' : ''; ?>">
				<select style="width:100%;" class="ed_filter_able_select" name="<?php echo $meta_name; ?>[category_type_custom][]" multiple size="3">
				  <?php foreach ($product_cat as $terms): ?>
				  <option value="<?php echo $terms->term_id; ?>" <?php echo (in_array($terms->term_id, $category_type_custom)) ? 'selected="selected"' : ''; ?>><?php echo $terms->name; ?></option>
				  <?php endforeach;?>
				</select>
			  </div>
			</td>
		  </tr>
		  <?php
	$tag_type = isset($value['tag_type']) ? $value['tag_type'] : 'all';
			$tag_type = ($tag_type == 'all') ? 'all' : 'custom';
			?>
		  <tr>
			<td><?php _e('Filter by tag :', 'ED_MULTI_LANG');?></td>
			<td><label for="ed_tag_type_all">
				<input checked="checked" id="ed_tag_type_all" name="<?php echo $meta_name; ?>[tag_type]" value="all"  type="radio" <?php echo checked('all', $tag_type); ?>>
				<?php _e(' All Tag :', 'ED_MULTI_LANG');?>
			  </label>
			  <label for="tag_type_selected">
				<input id="tag_type_selected" name="<?php echo $meta_name; ?>[tag_type]"  value="custom" type="radio" <?php echo checked('custom', $tag_type); ?>>
				<?php _e(' Select Tag :', 'ED_MULTI_LANG');?>
			  </label>
			  <?php
	$args = array('hide_empty' => false);
			$product_cat = get_terms('product_tag', $args);
			$tag_type_custom = (isset($value['tag_type_custom']) && is_array($value['tag_type_custom'])) ? $value['tag_type_custom'] : array();
			?>
			  <div class="choose_select_form_list <?php echo ($tag_type_custom == 'custom') ? 'active' : ''; ?>">
				<select style="width:100%;" class="ed_filter_able_select" name="<?php echo $meta_name; ?>[tag_type_custom][]" multiple size="3">
				  <?php foreach ($product_cat as $tag): ?>
				  <option value="<?php echo $tag->term_id; ?>"  <?php echo (in_array($tag->term_id, $tag_type_custom)) ? 'selected="selected"' : ''; ?>><?php echo $tag->name; ?></option>
				  <?php endforeach;?>
				</select>
			  </div></td>
		  </tr>
		  <?php $stock = isset($value['stock']) ? $value['stock'] : '';?>
		  <tr>
			<td><?php _e('Stock :', 'ED_MULTI_LANG');?></td>
			<td><select name="<?php echo $meta_name; ?>[stock]">
				<option value="all"  <?php selected('all', $stock);?> >
				<?php _e('All', 'ED_MULTI_LANG');?>
				</option>
				<option value="instock" <?php selected('instock', $stock);?> >
				<?php _e('Only in stock', 'ED_MULTI_LANG');?>
				</option>
				<option value="outofstock" <?php selected('outofstock', $stock);?> >
				<?php _e('Only Out of stock', 'ED_MULTI_LANG');?>
				</option>
			  </select></td>
		  </tr>
		  <?php $date_range = isset($value['date_range']) ? $value['date_range'] : '';?>
		  <tr>
			<td><?php _e('Date Range :', 'ED_MULTI_LANG');?></td>
			<td><select id="<?php echo $meta_name; ?>[date_range]" name="<?php echo $meta_name; ?>[date_range]" class="ed_daterange">
				<option value="all" <?php selected('all', $date_range);?> >
				<?php _e('All', 'ED_MULTI_LANG');?>
				</option>
				<option value="today" <?php selected('today', $date_range);?>>
				<?php _e('Today', 'ED_MULTI_LANG');?>
				</option>
				<option value="yesterday" <?php selected('yesterday', $date_range);?>>
				<?php _e('Yesterday', 'ED_MULTI_LANG');?>
				</option>
				<option value="this_month" <?php selected('this_month', $date_range);?>>
				<?php _e('This month', 'ED_MULTI_LANG');?>
				</option>
				<option value="this_year" <?php selected('this_year', $date_range);?>>
				<?php _e('This Year', 'ED_MULTI_LANG');?>
				</option>
				<option value="7" <?php selected('7', $date_range);?>>
				<?php _e('Last 7 Days', 'ED_MULTI_LANG');?>
				</option>
				<option value="30" <?php selected('30', $date_range);?>>
				<?php _e('Last 30 Days', 'ED_MULTI_LANG');?>
				</option>
				<option value="120" <?php selected('120', $date_range);?>>
				<?php _e('Last 120 Days', 'ED_MULTI_LANG');?>
				</option>
				<option value="365" <?php selected('365', $date_range);?>>
				<?php _e('Last 365 Days', 'ED_MULTI_LANG');?>
				</option>
				<option value="custom" <?php selected('custom', $date_range);?>>
				<?php _e('Custom Date Range', 'ED_MULTI_LANG');?>
				</option>
			  </select></td>
		  </tr>
		  <?php $start_date = isset($value['start_date']) ? $value['start_date'] : '';?>
		  <tr class="related_custom_date <?php echo ($date_range == 'custom') ? 'active' : ''; ?>">
			<td><?php _e('Start Date:', 'ED_MULTI_LANG');?></td>
			<td><input name="<?php echo $meta_name; ?>[start_date]" class="ed-jquery-datepicker" value="<?php echo $start_date; ?>" type="text"></td>
		  </tr>
		  <?php $end_date = isset($value['end_date']) ? $value['end_date'] : '';?>
		  <tr class="related_custom_date <?php echo ($date_range == 'custom') ? 'active' : ''; ?>">
			<td><?php _e('End Date :', 'ED_MULTI_LANG');?></td>
			<td><input name="<?php echo $meta_name; ?>[end_date]" class="ed-jquery-datepicker" value="<?php echo $end_date; ?>" type="text"></td>
		  </tr>
		  <?php $sort = isset($value['sort']) ? $value['sort'] : '';?>
		  <tr>
			<td ><?php _e('Sort by :', 'ED_MULTI_LANG');?></td>
			<td><select id="<?php echo $meta_name; ?>[sort]" name="<?php echo $meta_name; ?>[sort]">
				<option value="default" <?php selected('default', $sort);?>>
				<?php _e('Default sorting ( newness )', 'ED_MULTI_LANG');?>
				</option>
				<option value="popularity" <?php selected('popularity', $sort);?>>
				<?php _e('Sort by popularity', 'ED_MULTI_LANG');?>
				</option>
				<option value="lowhigh" <?php selected('lowhigh', $sort);?>>
				<?php _e('Sort by Price low to high', 'ED_MULTI_LANG');?>
				</option>
				<option value="highlow" <?php selected('highlow', $sort);?>>
				<?php _e('Sort by Price high to low', 'ED_MULTI_LANG');?>
				</option>
				<option value="oldest" <?php selected('oldest', $sort);?>>
				<?php _e('Sort by oldest', 'ED_MULTI_LANG');?>
				</option>
				<option value="nameaz" <?php selected('nameaz', $sort);?>>
				<?php _e('Sort by Product title a to z', 'ED_MULTI_LANG');?>
				</option>
				<option value="nameza" <?php selected('nameza', $sort);?>>
				<?php _e('Sort by Product title z to a', 'ED_MULTI_LANG');?>
				</option>
				<option value="rating" <?php selected('stockhighlow', $sort);?>>
				<?php _e('Sort by Rating', 'ED_MULTI_LANG');?>
				</option>
			  </select></td>
		  </tr>
		  <?php $perpage = isset($value['perpage']) ? $value['perpage'] : 12;?>
		  <tr>
			<td><?php _e('Products displayed per page:', 'ED_MULTI_LANG');?></td>
			<td><input  min="1" name="<?php echo $meta_name; ?>[perpage]" value="<?php echo $perpage; ?>" type="number"></td>
		  </tr>
		  <?php $perpage = isset($value['single_row']) ? $value['single_row'] : '';?>
		  <tr class="ed__default__hide for_listbox">
			<td ><?php _e('Number of Single row :', 'ED_MULTI_LANG');?></td>
			<td><input  min="1" name="<?php echo $meta_name; ?>[single_row]" value="<?php echo $perpage; ?>" type="number"></td>
		  </tr>
		</table>
	<?php
	
		}
		/**
		 * Metabox ( usage ) Callback function
		 *
		 * @param array $post
		 */
		public function usage_callback( $post ){
			?>
			<ul>
			  <li rel="tab-1" class="selected">
				<h4>Shortcode</h4>
				<p>Copy &amp; paste the shortcode directly into any WordPress page.</p>
				<input type="text" value="[woo_mulit_layout id=<?php echo $post->ID;?>]" readonly="readonly" style="text-align:center; width:100%;" size="40px;"  onfocus="this.select();" onmouseup="return false;" />
			  </li>
			  <li rel="tab-2">
				<h4>Template Include</h4>
				<p>Copy &amp; paste this code into a template file to include the slideshow within your theme.</p>
				<textarea class="full" readonly="readonly" style="width:100%;"  onfocus="this.select();" onmouseup="return false;" >&lt;?php echo do_shortcode("[woo_mulit_layout id=<?php echo $post->ID;?>]"); ?&gt;</textarea>
			  </li>
			</ul>
			
			
			<?php
		}
		/**
		 * Save All Meta data
		 *
		 * @param array $post
		 */
		public function save_post($post_id) {
			if (isset($_POST['multi_layout_editor_nonce']) && wp_verify_nonce($_POST['multi_layout_editor_nonce'], basename(__FILE__))) {
				$post_data = $this->validating($_POST['woo_product_loop_element']);
				update_post_meta($post_id, 'woo_product_loop_element', maybe_serialize($post_data));
			}
			if (isset($_POST[$this->meta_settings['product'] . '__nonce']) && wp_verify_nonce($_POST[$this->meta_settings['product'] . '__nonce'], basename(__FILE__))) {
				$query_data = $this->validating($_POST[$this->meta_settings['product']]);
				update_post_meta($post_id, $this->meta_settings['product'], maybe_serialize($query_data));
			}
			if (isset($_POST[$this->meta_settings['template'] . '__nonce']) && wp_verify_nonce($_POST[$this->meta_settings['template'] . '__nonce'], basename(__FILE__))) {
				$template = $this->validating($_POST[$this->meta_settings['template']]);
				update_post_meta($post_id, $this->meta_settings['template'], maybe_serialize($template));
			}
	
		}
		/**
		 * Save All Meta data
		 *
		 * @param array $data
		 * @return array
		 */
		private function validating($data) {
			$newdata = array();
			if (is_array($data)) {
				foreach ($data as $key => $val) {
					if ($val != '') {
						if (is_array($val)) {
							$newdata[$key] = $this->validating($val);
						} else {
							$newdata[$key] = sanitize_text_field(trim($val));
						}
					}
				}
				return $newdata;
			} else {
				return sanitize_text_field($data);
			}
		}
		/**
		 * Metabox ( woo__multi__layout__repalce_woocommerce_callback ) Callback function
		 *
		 * @param array $post
		 */
		public function woo__multi__layout__repalce_woocommerce_callback( $post ){
			?>
		   <table class="form-table ">
			  <tr>
			  <?php $replace = 'no';?>
				<td><?php  _e('Replace WooCommerce Shop Page', 'ED_MULTI_LANG');?><br/>
				<label for="repalce_yes">
				<input  id="repalce_yes" name="<?php echo $meta_name; ?>[product_query]" value="yes"  type="radio" <?php echo checked('yes', $replace); ?>>
				  <?php _e('Yes', 'ED_MULTI_LANG');?>
			  </label>
			  &nbsp; &nbsp;
			  <label for="repalce_no">
				<input  id="repalce_no" name="<?php echo $meta_name; ?>[product_query]" value="no"  type="radio" <?php echo checked('no', $replace); ?>>
				  <?php _e('No', 'ED_MULTI_LANG');?>
			  </label>
				</td>
			  </tr>
			  <?php $replace_category = 'no';?>
			   <tr>
				<td><?php _e('Replace WooCommerce Category Page', 'ED_MULTI_LANG');?><br/>
				 <label for="replace_category_yes">
				<input  id="replace_category_yes" name="<?php echo $meta_name; ?>[product_query]" value="yes"  type="radio" <?php echo checked('yes', $replace_category); ?>>
				  <?php _e('Yes', 'ED_MULTI_LANG');?>
			  </label>
			  &nbsp; &nbsp;
			  <label for="replace_category_no">
				<input  id="replace_category_no" name="<?php echo $meta_name; ?>[product_query]" value="no"  type="radio" <?php echo checked('no', $replace_category); ?>>
				  <?php _e('No', 'ED_MULTI_LANG');?>
			  </label>
				</td>
			  </tr>
			  <?php $replace_tags = 'no';?>
			  <tr>
				<td><?php _e('Replace WooCommerce Tags Page', 'ED_MULTI_LANG');?><br />
	
				<label for="replace_tags_yes">
				<input  id="replace_tags_yes" name="<?php echo $meta_name; ?>[product_query]" value="yes"  type="radio" <?php echo checked('yes', $replace_tags); ?>>
				  <?php _e('Yes', 'ED_MULTI_LANG');?>
			  </label>
			  &nbsp; &nbsp;
			  <label for="replace_tags_no">
				<input  id="replace_tags_no" name="<?php echo $meta_name; ?>[product_query]" value="no"  type="radio" <?php echo checked('no', $replace_tags); ?>>
				  <?php _e('No', 'ED_MULTI_LANG');?>
			  </label>
				</td>
			  </tr>
		   </table> 
			
			<?php
		}
		
		/**
		 * Register a filter hook on the admin footer.
		 */
		private function define_admin_hooks() {
			$this->loader->add_action('init', $this, 'register');
			$this->loader->add_action('add_meta_boxes', $this, 'multi_layout_creator');
			$this->loader->add_action('admin_enqueue_scripts', $this, 'enqueue_scripts');
			$this->loader->add_action('save_post', $this, 'save_post');
			$this->loader->add_action('admin_head', $this, 'product_brand_logo');
			add_filter( 'manage_edit-' .$this->post_type. '_columns', array($this, 'new_columns') ) ;
			$this->loader->add_action( 'manage_' .$this->post_type. '_posts_custom_column', $this, 'columns_data');
		}
		/**
		 * Load all css and js for wp-admin woo mulit layout options
		 */
		public function enqueue_scripts() {
			// admin utilities
			wp_enqueue_media();
			// wp core styles
			wp_enqueue_style('wp-color-picker');
			wp_enqueue_style('jquery-ui-datepicker-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
	
			// wp core scripts
			wp_enqueue_script('wp-color-picker');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('jquery-ui-accordion');
			wp_enqueue_script('jquery-ui-selectable');
			wp_enqueue_script('jquery-ui-datepicker');
	
			//vendor _scripts & style
	
			wp_enqueue_style($this->prefix . '-select2', $this->url . '/vendor/select2/select2.css', false, $this->version, 'all');
			wp_enqueue_script($this->prefix . '-select2-js', $this->url . '/vendor/select2/select2.js', false, $this->version, true);
	
			//Plugins _scripts
			wp_enqueue_style($this->prefix . '-admin-layout', $this->url . '/assets/admin/woo-product-multi-layout.css', false, $this->version, 'all');
			wp_enqueue_script($this->prefix . '-admin-layout-js', $this->url . '/assets/admin/woo-product-multi-layout.js', array('jquery', 'jquery-ui-sortable'), $this->version, true);
			wp_localize_script(
				$this->prefix . '-admin-layout-js',
				'woo_multi_layout',
				array('fields_location' => $this->url . '/fields/')
			);
		}
		
		/**
		* @return html
		*/
		function product_brand_logo(){
			echo '<style type="text/css">
				#menu-posts-woo_multii_layout .dashicons-admin-post::before,#menu-posts-woo_multii_layout .dashicons-format-standard::before{
				content:""!important;
				background:url('. $this->url . '/assets/admin/images/logo.svg) no-repeat center center;	
				}
			</style>';	
		}
		
		/**
		 * @return set
		 */
		public function run() {
			$this->loader->run();
		}
		/**
		 * @return array
		 */
		public function get_version() {
			return $this->version;
		}
		
		public function pro_callback(){
			?>
            <span class="dashicons dashicons-yes"></span> UnLock All feature and template ( MASONRY GRID, LIST VIEW , TABLE , CAROUSEL, Slider )<br>
            <span class="dashicons dashicons-yes"></span> Remove Our Marketing Ad<br>
            <span class="dashicons dashicons-yes"></span> Work with Visual Composer<br>
            <span class="dashicons dashicons-yes"></span> 24x7 Customer Support ticket, email, skype  & Teamviewer <br>
            <span class="dashicons dashicons-arrow-right"></span> And more...<br>
            <br>
            <a style="display:inline-block; background:#33b690; padding:8px 25px 8px; border-bottom:3px solid #33a583; border-radius:3px; color:white;" class="wpd_pro_btn" target="_blank" href="<?php echo ED_PRO_LINK;?>">See all PRO features</a>
            <?php
		}
		public function you_may_like(){
			$json = file_get_contents('http://eds.edatastyle.com/feed.php');
			$obj = json_decode($json);
			if( count( $obj ) > 0){
				echo "<ul class='ed_makerting_block'>";
				foreach($obj as $row){
					?>	<li>
                    	<a href="<?php echo $row->url;?>" target="_blank"><img src="<?php echo $row->image;?>" /></a>
                        <h4><?php echo $row->name;?> <span>( <?php echo $row->price;?> )</span></h4>
                        <a href="<?php echo $row->url;?>" target="_blank" class="link">Download</a>
                        <a href="<?php echo $row->live;?>" target="_blank" class="demo link">Live Demo</a>
                       </li> 
                    <?php
					
				}
				echo "</ul>";
			}
		}
	
	
	
	} // class Single_Post_Meta_Manager
	$woo_multi_layout_admin_manger = new Woo_Multi_Layout_Admin_Manager();
	$woo_multi_layout_admin_manger->run();

}
