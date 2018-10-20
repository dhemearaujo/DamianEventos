jQuery(function($) {
	"use strict";
	/*
	 * Iteam Show/hide
	 */
	if($('.ed__element__head').length){
		$(document).on('click', '.ed__element__head', function(e) {	
            e.preventDefault();
			jQuery(this).next().toggle();
        });
	}
	/*
	* Sortable
	*/

		/*
	* Sortable
	*/

	if($( "#sortable1" ).length){
		$( "#sortable1" ).sortable({
			connectWith: ".connectedSortable",
			group: '.connectedSortable',
			placeholder: "ui-sortable-placeholder",
			pullPlaceholder: false,
			helper: 'clone',
			appendTo: 'ul#sortable2',
			// animation on drop
			stop: function(evt, ui) {
	        	if(ui.item.data('action') != "" && ui.item.parents('ul.ed__sortable__editor').length){
					ui.item.addClass('active');
					ui.item.find('input,select').each(function(index, element) {
						$(this).attr('name',$(this).attr('id'));
	        		});
					$(ui.item).click(function(e) {
						e.preventDefault();
						$(this).next('.ed__load__element__attribute').toggle();
						$(this).toggleClass('closed');
					});
				}
	        },
			remove: function(event, ui) {
			
				ui.item.clone().appendTo('#sortable1');
				//$(this).sortable('cancel');
			}
			
		}).disableSelection();
	}
	if ( $( "#sortable2" ).length ){
		$( "#sortable2" ).sortable({
			connectWith: ".connectedSortable",
			group: '.connectedSortable',
			placeholder: "ui-sortable-placeholder",
			pullPlaceholder: false,
			stop: function(evt, $this) {
	           if($this.item.parents('ul.sortable_sidebar').length){
				  $this.item.removeClass('active');
				  $this.item.find('input,select').each(function(index, element) {
						$(this).attr('name','');
	        		});
			   }else{
				   $this.item.addClass('active'); 
			   }
	        }
		}).disableSelection();
	}
	
	
	
	/*
	 * Color Picker
	 */
	$('.ed_picker_color').wpColorPicker();
	/*
	 * relation__select show/hide
	 */
	if($('.relation__select').length){
		$(document).on('change', 'select.relation__select', function(e) {
			if($(this).val()!= ""){
				if($(this).parents('li').find('.'+$(this).val()).length){
					$(this).parents('li').find('.'+$(this).val()).addClass('ed__ralated__show');
				}else{
					$(this).parents('li').find('.ed__default__hide').removeClass('ed__ralated__show');
				}
			}else{
				$(this).parents('li').find('.ed__default__hide').removeClass('ed__ralated__show');
			}
			
		});
	}
		$(document).on('change', '.ed__product_settings__actions input[type=radio]', function(e) {	
			if($(this).is(':checked')){
				if($(this).val() == 'custom'){
					$(this).parents('tr').find('.choose_select_form_list').show();
				}else{
					$(this).parents('tr').find('.choose_select_form_list').hide();
				}
			}
			  
		});
	/*
	 * Layout Show/ Hide
	 */
	if($( ".ed_selectable" ).length) {
		$(document).on('click', '.ed_selectable li', function(e) {	
			
			
            var value = $(this).find('input[type=radio]').val();
				if($(this).find('input[type=radio]').length){
					$( ".ed_selectable li" ).removeClass('ed-selected');
					$(this).addClass('ed-selected');
				}else{
					return false;	
				}
				
				if( value == 'template-slider.php'){
					$('.ed__woo_style__table').addClass('hide_all_once');
				}else{
					$('.ed__woo_style__table').removeClass('hide_all_once');
				}
				
				if( value == 'template-table.php'){
					$('.for-table').addClass('ed_show');
				}else{
					$('.for-table').removeClass('ed_show');
				}
				
				if( value == 'template-listbox.php'){
					$('.use_for_list_layout').addClass('ed_show');
				}else{
					$('.use_for_list_layout').removeClass('ed_show');
				}
				if( value == 'template-grid.php' || value == 'template-listbox.php' || value == 'template-oddeven.php' || value == 'template-masonry-grid.php'){
					$('.use_for_grid_layout').addClass('ed_show');
				}else{
					$('.use_for_grid_layout').removeClass('ed_show');
				}
				
				
			  
        });
		
		$('.ed_selectable li.ed-selected input[type=radio]').each(function(index, element) {
            if( $(this).val() === 'template-table.php'){
				$('.for-table').addClass('ed_show');
			}
        });
	}
	
	 
	/*
	 * Select
	 */
	if($( ".ed_selectable" ).length) {
		$('.ed_filter_able_select').select2({
			placeholder: "Select a filter"
		});
	}
	
	/*
	 * jqery core date picker
	 */
	if($('.ed-jquery-datepicker').length){
		$('.ed-jquery-datepicker').datepicker();
	}
	/*
	 * showing date picker
	 */
	if($('.ed_daterange').length){
		$(document).on('change', 'select.ed_daterange', function(e) {
			if($(this).val() == "custom"){
				$('.related_custom_date').addClass('show');
			}else{
				$('.related_custom_date').removeClass('show');
			}
			
		});
	}
	if($('.ed_daterange').length){
		$(document).on('click', 'a.woo_item_delete', function(e) {		
			$(this).parent('li').detach();			  
		});
	}
	
	
});

