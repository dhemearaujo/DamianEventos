jQuery(function($) {
	//"use strict";
	/*
	 * woo__mulit__layout__carousel__views
	 */
	if($('.woo__mulit__layout__carousel__views').length){
		$(".woo__mulit__layout__carousel__views").owlCarousel({
			autoPlay : 4000,
			slideSpeed : 800,
			stopOnHover : true,
			navigation : true,
			pagination : true,
			mouseDrag : true,
			lazyLoad : true,
			items : 5,
			itemsDesktopSmall : [979, 4],
			itemsTablet : [768, 3],
			itemsMobile : [479, 2],
       
      });
	}
	
	if( $(".woo__mulit__layout__slider__views").length){
			 // Home Page Slider
			$(".woo__mulit__layout__slider__views").owlCarousel({
				singleItem            : true,
				responsive            : true,
				autoHeight            : false,
				mouseDrag             : false,
				touchDrag             : false,
				responsiveRefreshRate : 0,
				//transitionStyle       : 'fadeUp',
				pagination        : false,
				navigation        : true,
				
	
			});
		}
	
	
	/* ============== masonry Grid ============= */
	if( $(".masonry_grid").length){
		$('.masonry_grid').masonry({
		  // set itemSelector so .grid-sizer is not used in layout
		  itemSelector: '.woo__mulit__layout__grid__item',
		  // use element for option
		  columnWidth: '.woo__mulit__layout_grid__sizer',
		  percentPosition: true
		});
	}
	
	
});

