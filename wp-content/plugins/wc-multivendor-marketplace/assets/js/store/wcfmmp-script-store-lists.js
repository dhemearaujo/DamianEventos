jQuery(document).ready(function($) {
		
	// Store Box Height Set
	function storeBoxHeightManage() {
		var store_list_footer_height = 280;
		if( $('.wcfmmp-single-store').hasClass('coloum-2') || $('.wcfmmp-single-store').hasClass('coloum-3') ) {
			$('.wcfmmp-single-store .store-footer').each(function() {
				if( $(this).outerHeight() > store_list_footer_height ) {
					store_list_footer_height = $(this).outerHeight();
				}
			});
			$('.wcfmmp-single-store .store-footer').css( 'height', store_list_footer_height );
		}
	}
	setTimeout(function() { storeBoxHeightManage(); }, 200 );
	
	if( $("#wcfmmp_store_country").length > 0 ) {
		$("#wcfmmp_store_country").select2({
			allowClear:  true,
			placeholder: wcfmmp_store_list_messages.choose_location + ' ...'
		});
	}
	
	if( $("#wcfmmp_store_category").length > 0 ) {
		$("#wcfmmp_store_category").select2({
			allowClear:  true,
			placeholder: wcfmmp_store_list_messages.choose_category + ' ...'
		});
	}
		
		
	var form = $('.wcfmmp-store-search-form');
	var xhr;
	var timer = null;
	
	if( $('.wcfmmp-store-search-form').length > 0 ) {

		form.on('keyup', '#search', function() {
			var self = $(this),
				data = {
					search_term             : self.val(),
					wcfmmp_store_category   : $('#wcfmmp_store_category').val(),
					wcfmmp_store_country    : $('#wcfmmp_store_country').val(),
					wcfmmp_store_state      : $('#wcfmmp_store_state').val(),
					action                  : 'wcfmmp_stores_list_search',
					pagination_base         : form.find('#pagination_base').val(),
					paged                   : form.find('#wcfm_paged').val(),
					per_row                 : $per_row,
					per_page                : $per_page,
					excludes                : $excludes,
					_wpnonce                : form.find('#nonce').val()
				};
	
			if (timer) {
				clearTimeout(timer);
			}
	
			if ( xhr ) {
				xhr.abort();
			}
	
			timer = setTimeout(function() {
				$('.wcfmmp-stores-listing').block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
	
				xhr = $.post(wcfm_params .ajax_url, data, function(response) {
					if (response.success) {
						$('.wcfmmp-stores-listing').unblock();
	
						var data = response.data;
						$('#wcfmmp-stores-wrap').html( $(data).find( '.wcfmmp-stores-content' ) );
						fetchMarkers();
						setTimeout(function() { storeBoxHeightManage(); }, 200 );
					}
				});
			}, 500);
		} );
		
		// Category Filter
		form.on('change', '#wcfmmp_store_category', function() {
			var self = $(this),
				data = {
					search_term             : $('.wcfmmp-store-search').val(),
					wcfmmp_store_category   : $('#wcfmmp_store_category').val(),
					wcfmmp_store_country    : $('#wcfmmp_store_country').val(),
					wcfmmp_store_state      : $('#wcfmmp_store_state').val(),
					action                  : 'wcfmmp_stores_list_search',
					pagination_base         : form.find('#pagination_base').val(),
					paged                   : form.find('#wcfm_paged').val(),
					per_row                 : $per_row,
					per_page                : $per_page,
					excludes                : $excludes,
					_wpnonce                : form.find('#nonce').val()
				};
	
			if (timer) {
				clearTimeout(timer);
			}
	
			if ( xhr ) {
				xhr.abort();
			}
	
			timer = setTimeout(function() {
				$('.wcfmmp-stores-listing').block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
	
				xhr = $.post(wcfm_params .ajax_url, data, function(response) {
					if (response.success) {
						$('.wcfmmp-stores-listing').unblock();
	
						var data = response.data;
						$('#wcfmmp-stores-wrap').html( $(data).find( '.wcfmmp-stores-content' ) );
						fetchMarkers();
						setTimeout(function() { storeBoxHeightManage(); }, 200 );
					}
				});
			}, 500);
		} );
		
		// Country Filter
		form.on('change', '#wcfmmp_store_country', function() {
			var self = $(this),
				data = {
					search_term             : $('.wcfmmp-store-search').val(),
					wcfmmp_store_category   : $('#wcfmmp_store_category').val(),
					wcfmmp_store_country    : $('#wcfmmp_store_country').val(),
					wcfmmp_store_state      : $('#wcfmmp_store_state').val(),
					action                  : 'wcfmmp_stores_list_search',
					pagination_base         : form.find('#pagination_base').val(),
					paged                   : form.find('#wcfm_paged').val(),
					per_row                 : $per_row,
					per_page                : $per_page,
					excludes                : $excludes,
					_wpnonce                : form.find('#nonce').val()
				};
	
			if (timer) {
				clearTimeout(timer);
			}
	
			if ( xhr ) {
				xhr.abort();
			}
	
			timer = setTimeout(function() {
				$('.wcfmmp-stores-listing').block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
	
				xhr = $.post(wcfm_params .ajax_url, data, function(response) {
					if (response.success) {
						$('.wcfmmp-stores-listing').unblock();
	
						var data = response.data;
						$('#wcfmmp-stores-wrap').html( $(data).find( '.wcfmmp-stores-content' ) );
						fetchMarkers();
						setTimeout(function() { storeBoxHeightManage(); }, 200 );
					}
				});
			}, 500);
		} );
		
		// State Filter
		form.on('change', '#wcfmmp_store_state', function() {
			var self = $(this),
				data = {
					search_term             : $('.wcfmmp-store-search').val(),
					wcfmmp_store_category   : $('#wcfmmp_store_category').val(),
					wcfmmp_store_country    : $('#wcfmmp_store_country').val(),
					wcfmmp_store_state      : $('#wcfmmp_store_state').val(),
					action                  : 'wcfmmp_stores_list_search',
					pagination_base         : form.find('#pagination_base').val(),
					paged                   : form.find('#wcfm_paged').val(),
					per_row                 : $per_row,
					per_page                : $per_page,
					excludes                : $excludes,
					_wpnonce                : form.find('#nonce').val()
				};
	
			if (timer) {
				clearTimeout(timer);
			}
	
			if ( xhr ) {
				xhr.abort();
			}
	
			timer = setTimeout(function() {
				$('.wcfmmp-stores-listing').block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
	
				xhr = $.post(wcfm_params .ajax_url, data, function(response) {
					if (response.success) {
						$('.wcfmmp-stores-listing').unblock();
	
						var data = response.data;
						$('#wcfmmp-stores-wrap').html( $(data).find( '.wcfmmp-stores-content' ) );
						fetchMarkers();
						setTimeout(function() { storeBoxHeightManage(); }, 200 );
					}
				});
			}, 500);
		} );
		
		// State Filter
	form.on('keyup', '#wcfmmp_store_state', function() {
			var self = $(this),
				data = {
					search_term             : $('.wcfmmp-store-search').val(),
					wcfmmp_store_category   : $('#wcfmmp_store_category').val(),
					wcfmmp_store_country    : $('#wcfmmp_store_country').val(),
					wcfmmp_store_state      : $('#wcfmmp_store_state').val(),
					action                  : 'wcfmmp_stores_list_search',
					pagination_base         : form.find('#pagination_base').val(),
					paged                   : form.find('#wcfm_paged').val(),
					per_row                 : $per_row,
					per_page                : $per_page,
					excludes                : $excludes,
					_wpnonce                : form.find('#nonce').val()
				};
	
			if (timer) {
				clearTimeout(timer);
			}
	
			if ( xhr ) {
				xhr.abort();
			}
	
			timer = setTimeout(function() {
				$('.wcfmmp-stores-listing').block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
	
				xhr = $.post(wcfm_params.ajax_url, data, function(response) {
					if (response.success) {
						$('.wcfmmp-stores-listing').unblock();
	
						var data = response.data;
						$('#wcfmmp-stores-wrap').html( $(data).find( '.wcfmmp-stores-content' ) );
						fetchMarkers();
						setTimeout(function() { storeBoxHeightManage(); }, 200 );
					}
				});
			}, 500);
		} );
	}
	
	// Store List Filter Country -> State Dropdowns
	var wcfmmp_cs_filter_wrapper = $( '.wcfmmp-store-search-form' );
	var input_csd_state = '';
	var csd_selected_state = '';
	var wcfmmo_cs_filter_select = {
			init: function () {
				wcfmmp_cs_filter_wrapper.on( 'change', 'select#wcfmmp_store_country', this.state_select );
				//jQuery('select#wcfmmp_store_country').change();
			},
			state_select: function () {
					var states_json = wc_country_select_params.countries.replace( /&quot;/g, '"' ),
							states = $.parseJSON( states_json ),
							$statebox = $( '#wcfmmp_store_state' ),
							value = $statebox.val(),
							country = $( this ).val(),
							$state_required = $statebox.data('required');

					if ( states[ country ] ) {

							if ( $.isEmptyObject( states[ country ] ) ) {

								if ( $statebox.is( 'select' ) ) {
									if( typeof $state_required != 'undefined') {
										$( 'select#wcfmmp_store_state' ).replaceWith( '<input type="text" class="wcfm-text wcfm_ele" name="wcfmmp_store_state" id="wcfmmp_store_state" placeholder="'+ wcfmmp_store_list_messages.choose_state +' ..." />' );
									} else {
										$( 'select#wcfmmp_store_state' ).replaceWith( '<input type="text" class="wcfm-text wcfm_ele" name="wcfmmp_store_state" id="wcfmmp_store_state" placeholder="'+ wcfmmp_store_list_messages.choose_state +' ..." />' );
									}
								}

								if( value ) {
									$( '#wcfmmp_store_state' ).val( value );
								} else {
									$( '#wcfmmp_store_state' ).val( '' );
								}

							} else {
									input_csd_state = '';

									var options = '',
											state = states[ country ];

									for ( var index in state ) {
											if ( state.hasOwnProperty( index ) ) {
													if ( csd_selected_state ) {
															if ( csd_selected_state == index ) {
																	var selected_value = 'selected="selected"';
															} else {
																	var selected_value = '';
															}
													}
													options = options + '<option value="' + index + '"' + selected_value + '>' + state[ index ] + '</option>';
											}
									}

									if ( $statebox.is( 'select' ) ) {
											$( 'select#wcfmmp_store_state' ).html( '<option value="">' + wcfmmp_store_list_messages.choose_state + ' ...</option>' + options );
									}
									if ( $statebox.is( 'input' ) ) {
										if( typeof $state_required != 'undefined') {
											$( 'input#wcfmmp_store_state' ).replaceWith( '<select class="wcfm-select wcfm_ele" name="wcfmmp_store_state" id="wcfmmp_store_state"></select>' );
										} else {
											$( 'input#wcfmmp_store_state' ).replaceWith( '<select class="wcfm-select wcfm_ele" name="wcfmmp_store_state" id="wcfmmp_store_state"></select>' );
										}
										$( 'select#wcfmmp_store_state' ).html( '<option value="">' + wcfmmp_store_list_messages.choose_state + ' ...</option>' + options );
									}
									//$( '#wcmarketplace_address_state' ).removeClass( 'wcmarketplace-hide' );
									//$( 'div#wcmarketplace-states-box' ).slideDown();

							}
					} else {
						if ( $statebox.is( 'select' ) ) {
							if( typeof $state_required != 'undefined') {
								$( 'select#wcfmmp_store_state' ).replaceWith( '<input type="text" class="wcfm-text wcfm_ele" name="wcfmmp_store_state" id="wcfmmp_store_state" placeholder="'+ wcfmmp_store_list_messages.choose_state +' ..." />' );
							} else {
								$( 'select#wcfmmp_store_state' ).replaceWith( '<input type="text" class="wcfm-text wcfm_ele" name="wcfmmp_store_state" id="wcfmmp_store_state" placeholder="'+ wcfmmp_store_list_messages.choose_state +' ..." />' );
							}
						}
						$( '#wcfmmp_store_state' ).val(input_csd_state);

						if ( $( '#wcfmmp_store_state' ).val() == 'N/A' ){
							$( '#wcfmmp_store_state' ).val('');
						}
						//$( '#wcmarketplace_address_state' ).removeClass( 'wcmarketplace-hide' );
						//$( 'div#wcmarketplace-states-box' ).slideDown();
					}
			}
	}
	
	wcfmmo_cs_filter_select.init();
	
	function fetchMarkers() {
		if( $('.wcfmmp-store-list-map').length > 0 ) {
			reloadMarkers();
			
			var data = {
				search_term             : $('.wcfmmp-store-search').val(),
				wcfmmp_store_category   : $('#wcfmmp_store_category').val(),
				wcfmmp_store_country    : $('#wcfmmp_store_country').val(),
				wcfmmp_store_state      : $('#wcfmmp_store_state').val(),
				action                  : 'wcfmmp_stores_list_map_markers',
				pagination_base         : form.find('#pagination_base').val(),
				paged                   : form.find('#wcfm_paged').val(),
				per_row                 : $per_row,
				per_page                : $per_page,
				excludes                : $excludes
			};
			
			xhr = $.post(wcfm_params.ajax_url, data, function(response) {
				if (response.success) {
					var locations = response.data;
					setMarkers( $.parseJSON(locations) );
				}
			});
		}
	}
	
	// Store List Map
	if( $('.wcfmmp-store-list-map').length > 0 ) {
		$('.wcfmmp-store-list-map').css( 'height', $('.wcfmmp-store-list-map').outerWidth()/2);
		
		var markers = [];
		var store_list_map = '';
		
		function setMarkers(locations) {
			var latlngbounds = new google.maps.LatLngBounds();
			$.each(locations, function( i, beach ) {
				var myLatLng = new google.maps.LatLng(beach.lat, beach.lang);
				latlngbounds.extend(myLatLng);
				var marker = new google.maps.Marker({
						position: myLatLng,
						map: store_list_map,
						animation: google.maps.Animation.DROP,
						title: beach.name,
						zIndex: i 
				});
				
				var infowindow = new google.maps.InfoWindow();
				
				var infoWindowContent = '<div class="info_content">' +
																'<a style="display: inline-block; margin-right: 20px;margin-top:10px;" target="_blank" href="'+beach.url+'"><img width="80" src="'+beach.gravatar+'" /></a>' +
																'<div style="display: inline-block;vertical-align:top;">' +
																'<a style="font-size: 25px; color: #00798b; font-weight:bold; margin-bottom: 15px; margin-top:10px;display:block;" target="_blank" href="'+beach.url+'">'+beach.name+'</a>' +
																'<p>'+beach.address+'</p>' +
																'</div>' + 
																'</div>';
				
				google.maps.event.addListener(marker, 'click', (function(marker, i) {
					return function() {
						infowindow.setContent(infoWindowContent);
						infowindow.open(store_list_map, marker);
					}
				})(marker, i));
				
				store_list_map.setCenter(marker.getPosition());

				// Push marker to markers array                                   
				markers.push(marker);
			});
			if( $auto_zoom && locations.length > 0 ) {
			  store_list_map.fitBounds(latlngbounds);
			}
		}
		
		function reloadMarkers() {
			for( var i = 0; i < markers.length; i++ ) {
				markers[i].setMap(null);
			}
			markers = [];
		}
		
		var mapOptions = {
        zoom: $map_zoom,
        center: new google.maps.LatLng(30.0599153,31.2620199,13),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    store_list_map = new google.maps.Map(document.getElementById('wcfmmp-store-list-map'), mapOptions);
    fetchMarkers();
	}
});