jQuery(document).ready(function($){

/* Mean Menu */
jQuery('.main-navigation').meanmenu({
    meanMenuContainer: '.main-nav-holder',
    meanScreenWidth:"850"
});

/* slick slider starts */

$('.slick-main-slider').slick({
  dots: true,
  infinite: true,
  speed: 300,
  fade: true,
  arrows:true,
  autoplay: true
  
});


// Go to top.
  var $scroll_obj = $( '#btn-gotop' );
  $( window ).scroll(function(){
    if ( $( this ).scrollTop() > 100 ) {
      $scroll_obj.fadeIn();
    } else {
      $scroll_obj.fadeOut();
    }
  });

  $scroll_obj.click(function(){
    $( 'html, body' ).animate( { scrollTop: 0 }, 600 );
    return false;
  });

});