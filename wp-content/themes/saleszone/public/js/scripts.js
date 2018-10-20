;(function ($) {
  
  //register globals
  var loaderClass = 'spinner-circle';
  
  $.mlsAjax = {
    preloaderShow: function (options) {
      if (options.type === 'frame') {
        options.frame.attr('data-loader-frame', '1').append('<i class="' + loaderClass + '"></i>');
      }
      if (options.type === 'text') {
        options.frame.html(options.frame.data('loader'));
      }
    },
    
    preloaderHide: function () {
      $('[data-loader-frame]').removeAttr('data-loader-frame').find('.' + loaderClass).remove();
    },
    
    loadResponseFrame: function (data, frame) {
      var filterData = $(data).find(frame.selector).children();
      $(frame).html(filterData);
    },
    
    transferData: function (data, context) {
      context = context || $('html');
      $(data).find('[data-ajax-grab]').each(function () {
        var $this = $(this);
        var injectElement = $this.data('ajax-grab');
        var injectData = $this.html();
        context.find('[data-ajax-inject="' + injectElement + '"]').html(injectData);
      });
    }
  };
  
})(jQuery);
;(function ($) {
  
  $.mlsCart = {
    clearFragments: function () {
      sessionStorage.setItem(wc_cart_fragments_params.fragment_name, '');
    }
  };
  
})(jQuery);
;(function ($) {
  $.mlsLazzyload = {
    init: function () {
      $('[data-lazy-load]').lazyload({
        load: function () {
          $(this).parent('[data-loader-frame-permanent]').find('.spinner-circle').hide();
          $(this).parent('[data-loader-frame-permanent]').removeAttr('data-loader-frame-permanent');
        }
      });
    },
    update: function () {
      $(document).trigger('scroll');
    }
  };
  
  $.mlsLazzyload.init();
  
})(jQuery);
;(function ($) {
  
  if (typeof saleszoneLocalize === 'undefined') {
    return
  }
  
  var _ = saleszoneLocalize;
  
  /**
   * Localization for magnific popup
   */
  $.extend(true, $.magnificPopup.defaults, {
    tClose: _.tClose,
    tLoading: _.tLoading,
    gallery: {
      tPrev: _.tPrevious,
      tNext: _.tNext,
      tCounter: _.tOf
      
    }
  });
  
  
})(jQuery);
;(function ($) {
  
  $.mlsMedia = {
    zoomImage: function () {
      var selector = $('[data-zoom-image]');
      
      //Destroy previous zoom to prevent images duplication
      selector.trigger('zoom.destroy');
      
      //Init zoom to each element in list
      selector.each(function () {
        
        var zoomImage = $(this);
        var zoomedImageUrl = zoomImage.attr('data-zoom-image-url');
        var zoomedImageWrapper = zoomImage.siblings('[data-zoom-wrapper]');
        
        zoomImage.zoom({
          url: zoomedImageUrl,
          target: zoomedImageWrapper,
          touch: false,
          
          onZoomIn: function () {
            zoomedImageWrapper.removeClass('hidden');
          },
          onZoomOut: function () {
            zoomedImageWrapper.addClass('hidden');
          },
          callback: function () {
            var zoomedImage = $(this);
            
            if ((zoomImage.width() >= zoomedImage.width()) && (zoomImage.height() >= zoomedImage.height())) {
              selector.trigger('zoom.destroy');
            }
          }
        });
      });
    },
    magnificGalley: function (startIndex, outerGallery) {
      startIndex = startIndex || 0;
      outerGallery = outerGallery || $('[data-magnific-galley]');
      
      outerGallery.each(function () {
        
        var gallery = $(this);
        var mainImage = gallery.find('[data-magnific-galley-main]');
        var thumbList = gallery.find('[data-magnific-galley-thumb]');
        var imgStartArr = [];
        var imgPreArr;
        var imgShiftArr;
        
        if (thumbList.length > 0) {
          thumbList.each(function () {
            var imgSrc = {
              src: $(this).attr('href')
            };
            imgStartArr.push(imgSrc);
          });
          
          imgPreArr = imgStartArr.splice(0, startIndex);
          imgShiftArr = imgStartArr.concat(imgPreArr);
        }
        
        mainImage.magnificPopup({
          items: imgShiftArr,
          type: "image",
          gallery: {
            enabled: true
          },
          overflowY: "hidden",
          image: {
            titleSrc: 'data-magnific-galley-title'
          }
        });
        
      });
    }
  };
  
})(jQuery);
var mlsMegamenu = (function ($) {
  
  /* Activate callback function on resize event, but only if resize has been stopped */
  var _lazyResize = function (action, delay) {
    var resizeID;
    window.addEventListener('resize', function () {
      clearTimeout(resizeID);
      resizeID = setTimeout(action, delay);
    });
  };
  
  /* Find amount of coll in menu */
  var _findColsAmount = function (items) {
    return Array.prototype.reduce.call(items, function (count, item) {
      var value = item.dataset.megamenuCollItem;
      return (value > count) ? value : count;
    }, 1);
  };
  
  /* Create empty cols */
  var _createEmptyCols = function (coll, amount) {
    var emptyCols = [];
    for (var i = 2; i <= amount; i++) {
      emptyCols[i] = coll.cloneNode(false);
      emptyCols[i].dataset.megamenuColl = i;
    }
    return emptyCols;
  };
  
  /* Insert items into relative columns */
  var _moveItemsIntoCols = function (items, cols) {
    Array.prototype.forEach.call(items, function (item) {
      if (item.dataset.megamenuCollItem > 1) {
        cols[item.dataset.megamenuCollItem].appendChild(item);
      }
    });
  };
  
  /* Not allow sub menu go beyond parent menu container */
  var _fitHorizontal = function (selectors) {
    var menuContainer = document.querySelector(selectors.scope);
    var menuItems = menuContainer.querySelectorAll(selectors.items);
    var menuContainerWidth = menuContainer.offsetWidth;
    var menuPosition = menuContainer.getBoundingClientRect();
    
    Array.prototype.forEach.call(menuItems, function (item) {
      
      if (item.querySelectorAll(selectors.wrap).length === 0) {
        return
      }
      
      /* Reset menu item styles to default */
      item.style.left = '0';
      item.querySelector(selectors.wrap).dataset.megamenuWrap = 'false';
      
      var itemPosition = item.getBoundingClientRect();
      
      /* move menu item to the left if it go beyond the container */
      if (itemPosition.right > menuPosition.right) {
        item.style.left = '-' + (itemPosition.right - menuPosition.right) + 'px';
        
        /* move menu items to next row if item width exceeds container */
        if (item.offsetWidth > menuContainerWidth) {
          item.style.left = '-' + (itemPosition.left - menuPosition.left) + 'px';
          item.style.minWidth = menuContainerWidth + 'px';
          item.querySelector(selectors.wrap).dataset.megamenuWrap = 'true';
        }
      }
    });
  };
  
  var _equalHeight = function (container, items) {
    var menuHeight = container.offsetHeight;
    Array.prototype.forEach.call(items, function (item) {
      item.style.minHeight = menuHeight + 'px';
    });
  };
  
  /* Move menu items in columns */
  var _renderCols = function (scope) {
    
    var subMenus = scope.querySelectorAll('[data-megamenu-item]');
    
    /* Iterate each sub menu */
    Array.prototype.forEach.call(subMenus, function (menuItem) {
      var colsWrapper = menuItem.querySelector('[data-megamenu-wrap]');
      var coll = menuItem.querySelector('[data-megamenu-coll]');
      var collItems = menuItem.querySelectorAll('[data-megamenu-coll-item]');
      
      /* Find how much columns is needed */
      var collNum = _findColsAmount(collItems);
      
      /* Exit if we have only one column */
      if (collNum <= 1)
        return;
      
      /* Create empty cols */
      var emptyColNodes = _createEmptyCols(coll, collNum);
      
      /* Insert items into relative columns */
      _moveItemsIntoCols(collItems, emptyColNodes);
      
      /* Add cols with items into DOM */
      emptyColNodes.forEach(function (item) {
        colsWrapper.appendChild(item);
      });
      
    });
    
  };
  
  var _fitContainer = function (options) {
    var containerPosition = options.container.getBoundingClientRect();
    var menuPosition = options.wrap.getBoundingClientRect();
    var indent = menuPosition.left - containerPosition.left;
    var containerWidth = options.container.offsetWidth;
    var menuWidth = options.wrap.offsetWidth;
    var menuMaxWidth = containerWidth - indent;
    
    if (menuWidth > menuMaxWidth) {
      //'data-megamenu-wrap'
      options.wrap.style.width = menuMaxWidth + 'px';
      options.wrap.setAttribute('data-megamenu-wrap', 'true');
    } else {
      options.wrap.style.width = 'auto';
      options.wrap.setAttribute('data-megamenu-wrap', 'false');
    }
  };
  
  /* Public methods */
  return {
    renderCols: function (scope) {
      _renderCols(scope);
    },
    fitHorizontal: function (selectors) {
      /* Initial menu loading */
      _fitHorizontal(selectors);
      /* Reloading menu while window resizing */
      _lazyResize(function () {
        return _fitHorizontal(selectors);
      }, 500);
    },
    equalHeight: function (container, items) {
      _equalHeight(container, items);
    },
    fitContainer: function (options) {
      _fitContainer(options);
      _lazyResize(function () {
        return _fitContainer(options);
      }, 500);
    }
  };
  
})(jQuery);
(function ($) {
  
  $.mlsModal = function (options) {
    $.magnificPopup.close();
    $.magnificPopup.open({
      items: {
        src: options.src
      },
      type: 'ajax',
      ajax: {
        settings: {
          data: options.data
        }
      },
      callbacks: {
        parseAjax: function (mfpResponse) {
          if (options.transferData) {
            $.mlsAjax.transferData(mfpResponse.data);
          }
        }
      },
      showCloseBtn: false,
      modal: false
    });
  };
  
  $.mlsModalInline = function (data) {
    $.magnificPopup.close();
    $.magnificPopup.open({
      items: {
        src: data,
        type: 'inline'
      },
      showCloseBtn: false,
      modal: false
    });
  };
  
  /* Update current modal window content after it was replaced with woocommerce fragment */
  $(document.body).on('wc_fragments_loaded wc_fragments_refreshed', function () {
    var modalContent = $('.mfp-content');
    if (modalContent.length > 0) {
      $.magnificPopup.instance.content = modalContent.children();
    }
  });
  
})(jQuery);
(function ($) {
  
  /**
   *
   * @type {{showAddToCart: jQuery.mshProduct.showAddToCart, hideAddToCart: jQuery.mshProduct.hideAddToCart, editCartButtons: jQuery.mshProduct.editCartButtons}}
   */
  $.mshProduct = {
    setAddToCartState: function (state, products) {
      this.findOne(products, '[data-in-cart-state]').each(function (index, item) {
        if (item.getAttribute('data-in-cart-state') === state) {
          item.classList.add('hidden');
        } else {
          item.classList.remove('hidden');
        }
      });
    },
    findOne: function (product, selector) {
      return product.find(selector).filter(function () {
        return $(this).closest('[data-product]').is(product);
      });
    }
  };
})(jQuery);
;(function ($) {
  
  $.mlsSlider = {
    
    getCols: function (slide) {
      var breakpoints = [768, 992, 1200];
      var cols = slide ? slide.split(',') : false;
      
      if ($.isArray(cols)) {
        cols.shift();
        if (cols.length > 0) {
          return $.map(cols, function (value, index) {
            return {
              breakpoint: breakpoints[index],
              settings: {
                slidesToShow: parseInt(value)
              }
            };
          });
        } else {
          return false;
        }
      } else {
        return false;
      }
    },
    getFirstCol: function (slide) {
      var cols = slide ? slide.split(',') : false;
      if (cols) {
        return parseInt(slide.split(',')[0]);
      } else {
        return 2;
      }
    },
    thumbsSlider: function () {
      
      var sliders = $('[data-slider="thumbs-slider"]');
      
      sliders.each(function () {
        
        var scope = $(this);
        var slides = $('[data-slider-slides]', scope).attr('data-slider-slides');
        var breakpoints = $('[data-slider-breakpoints]', scope).attr('data-slider-breakpoints') || null;
        
        console.log($.mlsSlider.getFirstCol(slides));
        
        $('[data-slider-slides]', scope)
        .find('[data-slider-slide]')
        .css('float', 'left')
        .end()
        .slick({
          dots: false,
          arrows: true,
          adaptiveHeight: true,
          slidesToShow: $.mlsSlider.getFirstCol(slides),
          autoplay: false,
          autoplaySpeed: 3000,
          infinite: false,
          swipeToSlide: true,
          mobileFirst: true,
          rows: 1,
          prevArrow: $('[data-slider-arrow-left]', scope).removeClass('hidden'),
          nextArrow: $('[data-slider-arrow-right]', scope).removeClass('hidden'),
          responsive: $.mlsSlider.getCols(slides, breakpoints)
        });
      });
    }
  };
  
  $.mlsSlider.thumbsSlider();
  
})(jQuery);
;(function ($) {
  
  $.mlsTime = {
    
    countdown: {
      init: function (settings) {
        
        /* get collection of all countdown html components on page */
        var timers = document.querySelectorAll(settings.scope);
        
        /* update each countdown date item till expiration date */
        var updateClock = function (expirationDate, timeFrames, timerId) {
          var remainTime = $.mlsTime.countdown.getTimeLeft(expirationDate);
          
          $.mlsTime.countdown.renderTimeLeft(remainTime, timeFrames);
          if (timerId && remainTime.total <= 0) {
            clearInterval(timerId);
          }
        };
        
        for (var i = 0; i < timers.length; i++) {
          (function () {
            var expireDate = timers[i].getAttribute(settings.expireDateAttribute);
            var timerElements = timers[i].querySelectorAll(settings.item);
            var timeInterval = setInterval(function () {
              updateClock(expireDate, timerElements, timeInterval);
            }, 1000);
          })();
        }
        
      },
      getTimeLeft: function (endtime) {
        var t = Date.parse(endtime) - Date.parse(new Date());
        var seconds = Math.floor((t / 1000) % 60);
        var minutes = Math.floor((t / 1000 / 60) % 60);
        var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
        var days = Math.floor(t / (1000 * 60 * 60 * 24));
        
        return {
          'total': t,
          'days': days,
          'hours': hours,
          'minutes': minutes,
          'seconds': seconds
        };
      },
      renderTimeLeft: function (time, elems) {
        var dataAttrName;
        var frameType;
        
        for (var i = 0; i < elems.length; i++) {
          dataAttrName = Object.keys(elems[i].dataset)[0];
          frameType = elems[i].dataset[dataAttrName];
          elems[i].innerHTML = (time[frameType] < 10) ? ("0" + time[frameType]) : time[frameType];
        }
      }
    }
  };
  
})(jQuery);
;(function ($) {
  $.mshButtons = {
    addLoader: function (button) {
      /* Use timeout to prevent bug in IE-11
       * User couldn't go to order page from modal cart in
       * */
      setTimeout(function () {
        button.attr('disabled', 'disabled').find('[data-button-loader="loader"]').removeClass('hidden');
      }, 0);
    },
    removeLoader: function (button) {
      button.removeAttr('disabled').find('[data-button-loader="loader"]').addClass('hidden');
    }
  };
  
})(jQuery);
(function ($) {
  
  /**
   * polyfill for html5 form attr
   */
  // detect if browser supports this
  var sampleElement = $('[form]').get(0);
  var isIE11 = !(window.ActiveXObject) && "ActiveXObject" in window;
  if (sampleElement && window.HTMLFormElement && sampleElement.form instanceof HTMLFormElement && !isIE11) {
    // browser supports it, no need to fix
    return;
  }
  
  /**
   * Append a field to a form
   *
   */
  $.fn.appendField = function (data) {
    // for form only
    if (!this.is('form')) return;
    
    // wrap data
    if (!$.isArray(data) && data.name && data.value) {
      data = [data];
    }
    
    var $form = this;
    
    // attach new params
    $.each(data, function (i, item) {
      $('<input/>')
      .attr('type', 'hidden')
      .attr('name', item.name)
      .val(item.value).appendTo($form);
    });
    
    return $form;
  };
  
  /**
   * Find all input fields with form attribute point to jQuery object
   *
   */
  $('form[id]').submit(function (e) {
    var $form = $(this);
    // serialize data
    var data = $('[form=' + $form.attr('id') + ']').serializeArray();
    // append data to form
    $form.appendField(data);
  }).each(function () {
    var form = this,
      $form = $(form),
      $fields = $('[form=' + $form.attr('id') + ']');
    
    $fields.filter('button, input').filter('[type=reset],[type=submit]').click(function () {
      var type = this.type.toLowerCase();
      if (type === 'reset') {
        // reset form
        form.reset();
        // for elements outside form
        $fields.each(function () {
          this.value = this.defaultValue;
          this.checked = this.defaultChecked;
        }).filter('select').each(function () {
          $(this).find('option').each(function () {
            this.selected = this.defaultSelected;
          });
        });
      } else if (type.match(/^submit|image$/i)) {
        $(form).appendField({name: this.name, value: this.value}).submit();
      }
    });
  });
  
  
})(jQuery);


/**
 * SVG sprite IE polyfill
 */
(function () {
  svg4everybody();
})();
;(function ($) {
  
  var selectors = {
    scope: '[data-accordion-tabs]',
    item: '[data-accordion-tabs-item]',
    link: '[data-accordion-tabs-link]',
    content: '[data-accordion-tabs-content]'
  };
  var tabAll = $(selectors.scope);
  
  
  /**
   * Loop all tab components on the page
   * Set active state for first tab item
   */
  tabAll.each(function () {
    var $this = $(this);
    var firstTab = $this.find(selectors.item).first();
    
    firstTab.find(selectors.link).addClass('js-active');
    firstTab.find(selectors.content).addClass('js-open');
    
    $this.find('.js-init-active').removeClass('js-init-active');
  });
  
  
  /**
   * Tab click event handler.
   * Show tab content of current item and hide other
   */
  tabAll.on('click', selectors.link, function (event) {
    event.preventDefault();
    
    var tabCurrent = $(event.delegateTarget);
    var tabLink = $(this);
    
    if (tabLink.hasClass('js-active'))
      return;
    
    tabCurrent.find('.js-open').removeClass('js-open').hide();
    tabLink.next().toggleClass('js-open').toggle();
    tabCurrent.find('.js-active').removeClass('js-active');
    tabLink.addClass('js-active');
    
    $.mlsLazzyload.update();
  });
  
  /**
   * Activate tab when clicing on star rating reviews
   */
  $(document).on('click', '[data-product-reviews-link]', function () {
    $('#product-reviews').trigger('click');
  });
  
  /**
   * Page loading
   */
  $(function () {
    var hash = location.hash;
    
    if (hash === '#product-reviews') {
      $('html, body').animate({scrollTop: $('#product-reviews').offset().top}, 800);
      $('#product-reviews').trigger('click');
    }
  });
  
}(jQuery));
;(function ($) {
  
  var sliders = $('[data-slider="bargain"]');
  
  sliders.each(function () {
    
    var scope = $(this);
    var slides = $('[data-slider-slides]', scope).attr('data-slider-slides');
    
    $('[data-slider-slides]', scope).find('[data-slider-slide]').css('float', 'left').end().slick({
      dots: false,
      arrows: true,
      infinite: false,
      adaptiveHeight: true,
      slidesToShow: $.mlsSlider.getFirstCol(slides),
      autoplay: false,
      autoplaySpeed: 3000,
      swipeToSlide: true,
      mobileFirst: true,
      rows: 1,
      prevArrow: $('[data-slider-arrow-left]', scope).removeClass('hidden'),
      nextArrow: $('[data-slider-arrow-right]', scope).removeClass('hidden'),
      responsive: $.mlsSlider.getCols(slides)
    });
    
  });
  
})(jQuery);
;(function ($) {
  $(document).on('click', '[data-button-loader="button"]', function () {
    $(this).addClass('disabled');
    $.mshButtons.addLoader($(this));
  });
})(jQuery);
;
(function ($) {
  
  /**
   * Change "Add to Cart" to "View in Cart" when WP cache is on
   */
  $(document.body).on('wc_fragments_refreshed', function () {
    var fragments = JSON.parse(sessionStorage.getItem(wc_cart_fragments_params.fragment_name));
    var inCartIds = JSON.parse(fragments['[data-cart-products-fragment]']);
    
    $.mshProduct.setAddToCartState('visible', $('[data-product-variation]'));
    
    if (inCartIds.length < 1) {
      return;
    }
    
    var inCartItems = [];
    for (var item in inCartIds) {
      if (inCartIds.hasOwnProperty(item)) {
        inCartItems.push('[data-product-variation="' + item + '"]');
      }
    }
    var inCartSelector = inCartItems.join(',');
    
    $.mshProduct.setAddToCartState('hidden', $(inCartSelector));
  });
  
  /**
   * Open Cart in modal window after adding item to cart
   */
  $(document.body).on('added_to_cart', function (event, fragments, cart_hash, button) {
    if (typeof woocs_loading_first_time === 'undefined') {
      $.mlsModalInline(fragments['[data-cart-modal-fragment]']);
    } else {
      /* Magic if Woocommerce Currency switcher plugin is active */
      var openCartModal = function () {
        $.mlsModalInline(fragments['[data-cart-modal-fragment]']);
        $(document.body).off('wc_fragments_refreshed', openCartModal);
      };
      $(document.body).on('wc_fragments_refreshed', openCartModal);
    }
  });
  
  
  /**
   * Open cart in modal window
   */
  $(document.body).on('click', '[data-cart-modal]', function (e) {
    e.preventDefault();
    $.get(wc_cart_fragments_params.wc_ajax_url.toString().replace('%%endpoint%%', 'get_refreshed_fragments'), function (data) {
      $.mlsModalInline(data.fragments['[data-cart-modal-fragment]']);
    });
  });
  
  /**
   * Delete item from cart
   */
  $(document).on('click', '.remove_from_cart_button', function () {
    $.mlsAjax.preloaderShow({
      type: 'frame',
      frame: $('[data-cart-summary]')
    });
  });
  
  /**
   * Toggle "Add to cart" and "View in Cart" buttons on product item
   */
  /* Product has been added to cart */
  $(document.body).on('wc_cart_button_updated', function (event, $addToCartBtn) {
    var addedProductId = $addToCartBtn.closest('[data-product-variation]').attr('data-product-variation');
    var addedProducts = $('[data-product-variation="' + addedProductId + '"]');
    $.mshProduct.setAddToCartState('hidden', addedProducts);
  });
  
  /* Product has been removed to cart */
  $(document.body).on('removed_from_cart', function (event, fragment) {
    $.mshProduct.setAddToCartState('visible', findRemovedProducts(fragment['[data-cart-products-fragment]']));
    $(document.body).trigger('wc_fragment_refresh');
    $(document.body).trigger('update_checkout');
  });
  
  function findRemovedProducts(inCartIds) {
    var cartItems = JSON.parse(inCartIds);
    return $('.added_to_cart').not('.hidden').closest('[data-product-variation]').filter(function (index, item) {
      return !cartItems[$(item).attr('data-product-variation')];
    });
  }
  
  
  /**
   * Adjust request data before adding items to cart
   */
  $(document.body).on('adding_to_cart', function (event, button, data) {
    
    var wrapForm = button.closest('[data-loop-add-to-cart-form]');
    var separateForm = $(document.getElementById(button.attr('form')));
    
    var addToCartForm = ( wrapForm.length > 0 ) ? wrapForm : separateForm;
    
    addToCartForm.serializeArray().forEach(function (item) {
      data[item['name']] = item['value'];
    });
    
    /* Use WC_AJAX handler instead of default to add variation to cart */
    delete data['add-to-cart'];
    
    /* Variation product */
    if (Number(data['variation_id'])) {
      data['product_id'] = data['variation_id'];
    }
  });
  
  /**
   * Change cart product quantity on cart page
   */
  $(document).on('change', '[data-cart-summary-quantity]', function (e) {
    e.preventDefault();
    var quantityField = $(this);
    var form = quantityField.closest('.woocommerce-cart-form');
    var modalWindow = quantityField.closest('[data-cart-summary="modal"]');
    if (!modalWindow.length) {
      form.find('input[name="update_cart"]').attr('disabled', false).trigger('click');
    }
  });
  
  /**
   * Change product quantity on modal window
   */
  $(document).on('change', '[data-cart-summary="modal"] [data-cart-summary-quantity]', function (e) {
    
    var quantityField = $(this);
    var form = quantityField.closest('.woocommerce-cart-form');
    var max = quantityField.attr('max') ? quantityField.attr('max') : Infinity;
    var current = quantityField.val();
    
    if (current > max) {
      quantityField.val(max);
    }
    
    var params = form.serialize();
    
    $.ajax({
      url: form.attr('action'),
      type: 'post',
      data: params,
      beforeSend: function () {
        $.mlsAjax.preloaderShow({
          type: 'frame',
          frame: $('[data-cart-summary]')
        });
      },
      success: function () {
        $(document.body).trigger('wc_fragment_refresh');
        // update checkout on pag checkout
        $(document.body).trigger('update_checkout');
        // update cart totals on page cart
        $('select.shipping_method, input[name^=shipping_method]').first().trigger('change');
      }
    });
  });
  
  /**
   * update cart totals on page cart
   */
  $(document.body).on('removed_from_cart', function () {
    $('select.shipping_method, input[name^=shipping_method]').first().trigger('change');
  });
  
  
  /**
   * Cart coupone preloader
   */
  $(document).on('submit', '.woocommerce-cart-form', function (e) {
    e.preventDefault();
    $.mlsAjax.preloaderShow({
      type: 'frame',
      frame: $('[data-cart-frame-fragment]')
    });
  });
  
  /**
   * Removed last product from cart on page cart
   */
  if (typeof wc_cart_params === 'undefined') {
    return false;
  }
  
  $(document).on('removed_from_cart', function (event, fragments) {
    if (Object.keys(JSON.parse(fragments['[data-cart-products-fragment]'])).length === 0) {
      location.reload();
    }
  });
  
  /**
   * Add preloader to shipping block
   */
  $(document).on('update_checkout', function () {
    $('[data-checkout-shipping-fragments]').block({
      message: null,
      overlayCSS: {
        background: '#fff',
        opacity: 0.6
      }
    });
  });
})(jQuery);
;
(function ($) {
  /**
   * Change Catalog View
   */
  $(document).on('click', '[data-catalog-view-item]', function (e) {
    var cookieValue = $(this).attr('data-catalog-view-item');
  
    e.preventDefault();
    document.cookie = 'catalog_view=' + cookieValue + ';path=/';
    window.location.reload();
  });
  
  /**
   * Order form onchange
   */
  $(document).on('change', '[data-catalog-order-select]', function () {
    $('#catalog-form').submit();
    $('[form="catalog-form"]').attr('disabled', true);
  });
  /**
   * Per page form onchange
   */
  $(document).on('change', '[data-catalog-perpage-select]', function () {
    $('#catalog-form').submit();
    $('[form="catalog-form"]').attr('disabled', true);
  });
  
})(jQuery);
;(function () {
  var BREAKPOINT = 991;
  
  var catalog = document.querySelector('[data-catalog-btn]');
  var overlay = document.querySelector('[data-catalog-btn-overlay]');
  var subMenu = document.querySelector('[data-catalog-btn-menu]');
  
  var subMenuHiddenClass = "is-hidden";
  
  
  if (!subMenu) return;
  
  function showMenu() {
    subMenu.classList.remove(subMenuHiddenClass);
    overlay.classList.remove('hidden');
  }
  
  function closeMenu() {
    subMenu.classList.add(subMenuHiddenClass);
    overlay.classList.add('hidden');
  }
  
  function toggleMenu() {
    subMenu.classList.toggle(subMenuHiddenClass);
    overlay.classList.toggle('hidden');
  }
  
  catalog.addEventListener('click', function () {
    toggleMenu();
  });
  
  overlay.addEventListener('click', function () {
    closeMenu();
  });
  
  function resizeHandler(breakpoint) {
    var width = window.innerWidth;
    
    if (width < breakpoint) {
      closeMenu();
    }
  }
  
  var resizeID = null;
  window.addEventListener("resize", function () {
    clearTimeout(resizeID);
    resizeID = setTimeout(resizeHandler.bind(null, BREAKPOINT), 300);
    
  });
  
})();
;
(function ($) {
  /**
   * Adding new comment
   */
  $(document).on('submit', '[data-comments-form]', function (e) {
        e.preventDefault();

        var form = $(this);
        var context = form.closest('[data-comments]');
        var formType = form.attr('data-comments-form');
        var currentForm = '[data-comments-form="' + formType + '"]';
        var successMessage = currentForm + ' [data-comments-success]';
        var errorMessageFrame = currentForm + ' [data-comments-error-frame]';
        var errorMessageList = currentForm + ' [data-comments-error-list]';

        /* Request to submit form */
        $.ajax({
            url: form.attr('data-comments-form-url'),
            type: 'post',
            data: form.serialize(),
            dataType: 'json',
            beforeSend: function () {
                /* Show loader before ajax response */
                $.mlsAjax.preloaderShow({
                    type: 'frame',
                    frame: context
                });
            },
            success: function (data) {
                if (data.answer == 'error') {
                    /* Error Message */
                    context.find(successMessage).addClass('hidden');
                    context.find(errorMessageList).html(data.validation_errors);
                    context.find(errorMessageFrame).removeClass('hidden');
                    /* Change captcha image */
                    context.find(currentForm + ' [data-captcha-img]').html(data.cap_image);
                } else {
                    /* Request to update comments list*/
                    $.ajax({
                        url: form.attr('data-comments-form-list-url'),
                        method: 'post',
                        dataType: 'json',
                        success: function (data) {
                            /* Insert response into document */
                            context.html(data.comments);
                            /* Success Message */
                            context.find(errorMessageFrame).addClass('hidden');
                            context.find(successMessage).removeClass('hidden');
                        }
                    });
                }
            }
        });
    });
  
  /**
   * Reply for existing comment
   */
  $(document).on('click', '[data-comments-reply-link]', function (e) {
    e.preventDefault();
    
    var replyLink = $(this);
    var requestData = JSON.parse(replyLink.attr('data-comments-reply-link'));
    var parentComment = replyLink.closest('[data-comments-post]');
    var placeholder = parentComment.find('[data-comments-reply-form-placeholder]').first();
    
    var is_replay_form = placeholder.find('[data-comments-reply-form]').length;
    
    if (is_replay_form) {
      placeholder.toggleClass('hidden');
    } else {
      $.ajax({
        url: requestData.url,
        type: 'post',
        data: requestData,
        beforeSend: function () {
          $.mlsAjax.preloaderShow({
            type: 'frame',
            frame: parentComment
          });
        },
        success: function (responseData) {
          var responseDOM = $(responseData);
          responseDOM.find('form').removeAttr('novalidate');
          
          placeholder.removeClass('hidden');
          
          responseDOM.find('#comment_parent').attr('value', requestData.parent_id);
          placeholder.html(responseDOM);
        }
      });
    }
  });
  
  /**
   * Rate comment
   */
  $(document).on('click', '[data-comments-vote-url]', function (e) {
    e.preventDefault();
    
    var voteLink = $(this);
    var href = voteLink.attr('data-comments-vote-url');
    var commentId = voteLink.closest('[data-comments-post]').attr('data-comments-post');
    var voteValue = voteLink.find('[data-comments-vote-value]');
    
    $.ajax({
      url: href,
      type: 'post',
      data: {comid: commentId},
      dataType: 'json',
      success: function (data) {
        voteValue.html(data.y_count ? data.y_count : data.n_count);
      }
    });
  });
  
})(jQuery);
;
(function ($) {
  
  /**
   * Add to compare button and change total items in compare
   * scope - "add_to"
   */
  $(document).on('click', '[data-add-to-compare-btn="add"]', function (e) {

        e.preventDefault();
        var button = $(this);
        var scope = button.closest('[data-add-to-compare]');
        var params = JSON.parse(scope.find('[data-add-to-compare-data]').attr('data-add-to-compare-data'));

        $.ajax({
            url: params.url,
            type: 'post',
            data: params,
            beforeSend: function () {
                if(button.attr('data-add-to-compare-btn-type') === 'link'){
                    $.mlsAjax.preloaderShow({
                        type: 'text',
                        frame: button
                    });
                }
            },
            success: function () {
                scope.find('[data-add-to-compare-btn]').toggleClass('hidden');
                $(document.body).trigger('wc_fragment_refresh');

                // Switch visibility between add and open buttons
                //buttons.toggleClass('hidden');

                // Change total amount of items
                //$('[data-compare-total]').html(data.count);

                // Add page link if total items greater then 0
                /*if (data.count > 0) {
                 $('[data-compare-removeclass]').removeClass(linkClass);
                 }*/
            }
        });

    });

    /**
     * Delete item or list
     */
    $(document).on('click', '[data-compare-delete-button]', function () {
        $.mlsCart.clearFragments();
    });
})(jQuery);
(function ($) {
  $.mlsTime.countdown.init({
    scope: '[data-countdown]',
    item: '[data-countdown-item]',
    expireDateAttribute: 'data-countdown'
  });
})(jQuery);
/* Ajax form submit */
(function ($) {
  
  $(document).on('submit', '[data-form-ajax]', function (e) {
    e.preventDefault();
    
    var target = $(this);
    
    //frames in which response data will be inputted
    var responseFrame = $('[data-form-ajax="' + target.attr('data-form-ajax') + '"]');
    
    $.ajax({
      url: target.attr('action'),
      type: target.attr('method') ? target.attr('method') : 'get',
      data: target.serialize(),
      beforeSend: function () {
        /* Loader visible before ajax response */
        $.mlsAjax.preloaderShow({
          type: 'frame',
          frame: target
        });
      },
      success: function (data) {
        /* Insert response into document */
        $.mlsAjax.loadResponseFrame(data, responseFrame);
        
        /* Grab extra data from response frame and insert it into remote places */
        $.mlsAjax.transferData(data);
      }
    });
    
  });
  
  $(document).on('change', '[data-form-self-submit]', function () {
    var formControl = $(this);
    formControl.closest('form').submit();
    formControl.attr('disabled', true);
  });
  
  $(function(){
    $('#comment_form').removeAttr('novalidate');
  });
  
})(jQuery);
;
(function ($) {
    $('.gallery').magnificPopup({
        delegate: ".gallery-icon a[href*='wp-content/uploads']",
        type: "image",
        gallery: {
            enabled: true,
            tCounter: '%curr% of %total%'
        },
        overflowY: "hidden"
    });
})(jQuery);
;
(function ($) {
  
  /**
   * Open modal window
   */
  $(document).on('click', '[data-modal]', function (e) {
    e.preventDefault();
    
    var $this = $(this);
    var modalUrl = $this.data('modal');
    modalUrl = modalUrl ? modalUrl : $this.attr('href');
    
    $.mlsModal({
      src: modalUrl
    });
    
  });
  
  
  /**
   * Close modal window
   */
  $(document).on('click', '[data-modal-close]', function (e) {
    e.preventDefault();
    $.magnificPopup.close();
  });
  
})(jQuery);
;
(function ($) {
  /**
   * Tree navigation menu
   * Right to left direction if menu doesn't fit to window size
   **/
  
  /**
   * global mlsMegamenu
   */
  $('[data-nav-hover-item]').on('mouseenter', function () {
    
    var dropList = this.querySelectorAll('[data-nav-direction]');
    var windowWidth = document.documentElement.clientWidth;
    var remoteItemPos = windowWidth;
    
    /* find max right coordinate among all drop-down menus within current hover item */
    for (var i = 0; i < dropList.length; i++) {
      var dropItem = dropList[i];
      dropItem.setAttribute('data-nav-direction', 'ltr');
      var itemPos = dropItem.getBoundingClientRect().right;
      remoteItemPos = itemPos > windowWidth ? itemPos : remoteItemPos;
    }
    
    /* apply right direction if max right coordinate is bigger then window width */
    if (remoteItemPos > windowWidth) {
      for (var j = 0; j < dropList.length; j++) {
        dropList[j].setAttribute('data-nav-direction', 'rtl');
      }
    }
    
  });
  
  /**
   * Mega menu
   * Make menu fit to container width
   **/
  var verticalMenu = document.querySelector('[data-megamenu-vertical]');
  var horizontalMenu = document.querySelector('[data-megamenu-horizontal]');
  
  if (horizontalMenu !== null) {
    mlsMegamenu.renderCols(horizontalMenu);
    mlsMegamenu.fitHorizontal({
      scope: '[data-megamenu-horizontal]',
      items: '[data-megamenu-item]',
      wrap: '[data-megamenu-wrap]'
    });
  }
  
  if (verticalMenu !== null) {
    mlsMegamenu.renderCols(verticalMenu);
    mlsMegamenu.equalHeight(verticalMenu, verticalMenu.querySelector('[data-megamenu-wrap]'));
    mlsMegamenu.fitContainer({
      container: document.querySelector('[data-megamenu-container]'),
      wrap: verticalMenu.querySelector('[data-megamenu-wrap]'),
      indent: document.querySelector('[data-megamenu-vertical]')
    });
  }
})(jQuery);
;
(function ($) {
  /* global woocommerce_params */
  
  /**
   * Handle change variation on single product
   */
  $(document).on('show_variation', '.single_variation', function (event, variation, purchasable) {
    var product = $(this).closest('[data-product]');
    
    if (!variation) {
      $.mshProduct.findOne(product, 'input[name="variation_id"]').val(0);
      $.mshProduct.findOne(product, '.add_to_cart_button').removeClass('ajax_add_to_cart').addClass('disabled');
      return;
    }
    
    $.mshProduct.findOne(product, '[data-product-loop-action-counter]').remove();
    
    if (variation.end_date && variation.end_date !== '') {
      
      $(variation.end_date).appendTo($.mshProduct.findOne(product, '[data-loop-before-title]'));
    }
    
    $.mshProduct.findOne(product, '[data-product-single-action-counter]').remove();
    
    if (variation.end_single_date && variation.end_single_date !== '') {
      
      $(variation.end_single_date).appendTo($.mshProduct.findOne(product, '[data-single-after-add-to-cart]'));
      
      $.mlsTime.countdown.init({
        scope: '[data-countdown]',
        item: '[data-countdown-item]',
        expireDateAttribute: 'data-countdown'
      });
    }
    
    product.attr('data-product-variation', variation.variation_id);
    
    $.mshProduct.findOne(product, '[data-product-permalink]').attr('href', variation.permalink).html(variation.variation_name);
    $.mshProduct.findOne(product, '.add_to_cart_button').attr('href', variation.add_to_cart_url);
    $.mshProduct.findOne(product, '[data-product-add-to-cart-text]').html(variation.add_to_cart_text);
    
    $.mshProduct.findOne(product, 'input[name="variation_id"]').val(variation.variation_id);
    
    if (purchasable) {
      $.mshProduct.findOne(product, '.add_to_cart_button').addClass('ajax_add_to_cart').removeClass('disabled');
    } else {
      $.mshProduct.findOne(product, '.add_to_cart_button').removeClass('ajax_add_to_cart').addClass('disabled');
    }
    
    var discount = Math.round(((variation.display_regular_price - variation.display_price) / variation.display_regular_price) * 100);
    if (discount > 0) {
      $.mshProduct.findOne(product, '[data-product-discount-percent]').removeClass('hidden');
      $.mshProduct.findOne(product, '[data-product-discount-percent-value]').html(discount);
    } else {
      $.mshProduct.findOne(product, '[data-product-discount-percent]').addClass('hidden');
    }
    
    
    $.mshProduct.findOne(product, '[data-product-medium-photo]').removeAttr('srcset', false);
    
    if (variation.image.medium_src) {
      $.mshProduct.findOne(product, '[data-product-medium-photo]').attr('src', variation.image.medium_src);
    } else {
      $.mshProduct.findOne(product, '[data-product-medium-photo]').attr('src', $.mshProduct.findOne(product, '[data-product-medium-photo]').attr('data-src-placeholder'));
    }
    
    if (variation.image.thumb_src) {
      $.mshProduct.findOne(product, '[data-product-thumbnail]').attr('src', variation.image.thumb_src);
    } else {
      $.mshProduct.findOne(product, '[data-product-thumbnail]').attr('src', $.mshProduct.findOne(product, '[data-product-medium-photo]').attr('data-src-placeholder'));
    }
    
    $.mshProduct.findOne(product, '[data-product-price]').html(variation.price_html);
    $.mshProduct.findOne(product, '[data-product-availability-html]').replaceWith(variation.availability_html);
    $.mshProduct.findOne(product, '[data-product-variation-description]').html(variation.variation_description);
    
    
    if (variation.in_cart) {
      $.mshProduct.setAddToCartState('hidden', product);
    } else {
      $.mshProduct.setAddToCartState('visible', product);
    }
    
    
    /* Set data for single product image gallery */
    var gallery = product.find('[data-product-photo-scope]');
    var mainImageSrc = gallery.find('[data-product-photo]').attr('src');
    var productPhoto = gallery.find('[data-product-photo]');
    
    /* Break if image didn't change */
    if (mainImageSrc === variation.image.src) {
      return;
    }
    
    /* Change main image */
    
    if (variation.image.src) {
      productPhoto.attr('src', variation.image.src);
      productPhoto.closest('[data-product-photo-link]').attr('href', variation.image.full_src);
    } else {
      productPhoto.attr('src', productPhoto.attr('data-src-placeholder'));
      productPhoto.closest('[data-product-photo-link]').attr('href', '');
    }
    
    /* Change first thumbnail */
    gallery.find('[data-product-photo-main-thumb]').each(function () {
      gallery.find('[data-product-photo-thumb]').removeAttr('data-product-photo-thumb-active');
      $(this).attr('src', variation.image.thumb_src)
      .closest('[data-product-photo-thumb]')
      .attr('href', variation.image.full_src)
      .attr('data-product-photo-thumb', variation.image.src)
      .attr('data-product-photo-thumb-active', '');
    });
    
    /* Change cloud zoom */
    gallery.find('[data-zoom-image]').each(function () {
      $(this).attr('data-zoom-image-url', variation.image.full_src);
      $.mlsMedia.zoomImage();
    });
    
    /* Reinit magnific popup */
    $.mlsMedia.magnificGalley();
  });
  
  /**
   * Handle change variation on arhive product
   */
  $(document).on('change', '[data-loop-variation]', function () {
    var currentAttribute = $(this);
    var attributesScope = currentAttribute.closest('[data-product]');
    var attributes = attributesScope.find('[data-loop-variation]');
    var product = currentAttribute.closest('[data-product]');
    var productId = product.attr('data-product');
    var responseFrame = product;
    var requestData = attributes.serializeArray();
    var addToCartButton = product.find('.add_to_cart_button');
    
    requestData.push({
      name: 'product_id', value: productId
    });
    
    
    /* Retrieve variation data. Available when all attributes are selected */
    $.ajax({
      url: woocommerce_params.wc_ajax_url.toString().replace('%%endpoint%%', 'get_variation'),
      type: 'post',
      data: requestData,
      beforeSend: function () {
        /* Activate visual loader */
        $.mlsAjax.preloaderShow({
          type: 'frame',
          frame: responseFrame
        });
        /* Invoke ajax request only if all options has value */
        if (attributes.not(function () {
            return this.value;
          }).length > 0) {
          addToCartButton.addClass('disabled wc-variation-selection-needed');
          addToCartButton.removeClass('ajax_add_to_cart');
          
          return false;
        } else {
          product.find('.add_to_cart_button.ajax_add_to_cart').removeClass('disabled wc-variation-selection-needed');
          addToCartButton.addClass('ajax_add_to_cart');
        }
      },
      success: function (variation) {
        var purchasable = true;
        
        if (!variation.is_purchasable || !variation.is_in_stock || !variation.variation_is_visible) {
          purchasable = false;
        }
        product.find('.single_variation').trigger('show_variation', [variation, purchasable]);
        
      }
    });
  });
  
  /**
   * Open modal window for in stock reminder page
   */
  $(document).on('click', '[data-product-notify]', function (e) {
    e.preventDefault();
    
    var notifyLink = $(this);
    
    $.mlsModal({
      src: $(this).attr('href'),
      data: {
        ProductId: notifyLink.attr('data-product-notify'),
        VariantId: notifyLink.attr('data-product-notify-variant')
      }
    });
  });
  
  $(document).on('click', '.add_to_cart_button', function (event) {
    
    if ($(this).is('.disabled')) {
      event.preventDefault();
      
      if ($(this).is('.wc-variation-is-unavailable')) {
        window.alert(wc_add_to_cart_variation_params.i18n_unavailable_text);
      } else if ($(this).is('.wc-variation-selection-needed')) {
        window.alert(wc_add_to_cart_variation_params.i18n_make_a_selection_text);
      }
    }
    
  });
  
  $(document).on('hide_variation', '.single_variation', function () {
    var product = $(this).closest('[data-product]');
    $.mshProduct.findOne(product, '.add_to_cart_button').removeClass('ajax_add_to_cart');
  });
})(jQuery);
;
(function ($) {
  $(document).on('submit', '[data-profile-ajax="login_form"]', function (e) {
    e.preventDefault();
    
    var form = $(this);
    
    $.ajax({
      url: form.attr('action'),
      type: form.attr('method') ? form.attr('method') : 'get',
      data: form.serialize(),
      beforeSend: function () {
        /* Submit button ico loader */
        $.mlsAjax.preloaderShow({
          type: 'frame',
          frame: form
        });
      },
      success: function (data) {
        
        //* If authentification was successful show loading button and redierct to current page */
        if ($(data).find('[data-profile-ajax-form--notices]').length === 0) {
          $.mshButtons.addLoader(form.find('[data-profile-button]'));
          // insert user name intro validation success message
          form.find('[data-profile-ajax-form--user-name]').html(form.find('input[name="username"]').val());
          form.find('[data-profile-ajax-form--content] , [data-profile-ajax-form--success-message]').toggleClass('hidden');
          location.assign(location.href);
        } else {
          /* Grab extra data from response frame and insert it into remote places */
          $.mlsAjax.transferData(data, form);
        }
        
      }
    });
    
  });
})(jQuery);
;
(function ($) {
  var pushy = $('[ data-page-pushy-mobile]'), //menu css class
    container = $('[ data-page-pushy-container]'), //container css class
    siteOverlay = $('[data-page-pushy-overlay]'), //site overlay
    pushyClass = "page__mobile--js-open", //menu position & menu open class
    containerClass = "page__body--js-pushed", //container open class
    menuBtn = $('[data-page-mobile-btn]'), //css classes to toggle the menu
    menuBtnOpen = 'ico-mobile--open', //css classes to toggle the menu
    menuBtnClose = 'ico-mobile--close'; //css classes to toggle the menu
  
  function togglePushy() {
    siteOverlay.toggleClass('hidden'); //toggle site overlay
    pushy.toggleClass(pushyClass);
    container.toggleClass(containerClass);
    menuBtn.toggleClass('hidden');
  }
  
  //Open mobile frame when clicking site mobile button
  menuBtn.click(function () {
    togglePushy();
  });
  
  //close mobile frame when clicking site overlay
  siteOverlay.click(function () {
    togglePushy();
  });
  
})(jQuery);
;
(function ($) {
  var searchOverlay = {
    showSearch: function () {
      
      var $scope = $(this).closest('[data-search-overlay-scope]');
      var $overlay = $scope.find('[data-search-overlay-overlay]');
      var $search = $scope.find('[data-search-overlay-search]');
      
      $overlay.removeClass('hidden');
      $search.removeClass('hidden');
    },
    
    hideSearch: function () {
      
      var $scope = $(this).closest('[data-search-overlay-scope]');
      var $overlay = $scope.find('[data-search-overlay-overlay]');
      var $search = $scope.find('[data-search-overlay-search]');
      
      $overlay.addClass('hidden');
      $search.addClass('hidden');
    }
  };
  
  /* Show search input and overlay if search button is clicked*/
  $(document).on('click', '[data-search-overlay-btn]', searchOverlay.showSearch);
  
  /* Hide search input and overlay if overlay clicked*/
  $(document).on('click', '[data-search-overlay-overlay]', searchOverlay.hideSearch);
  
  /* Hide search if Esc button is pressed*/
  $(document).on('keyup', function (e) {
    if (e.keyCode == 27) {
      searchOverlay.hideSearch();
    }
  });
  
})(jQuery);
;
(function ($) {
  
  /**
   * Focusing text field if relative radio is checked
   */
  $(document).on('click', '[data-wishlist-new-input]', function () {
    var radioNew = $(this).closest('[data-wishlist-new-scope]').find('[data-wishlist-new-radio]');
    $(radioNew).trigger('click');
  });
  
  $(document).on('click', '[data-wishlist-new-radio]', function () {
    var inputNew = $(this).closest('[data-wishlist-new-scope]').find('[data-wishlist-new-input]');
    $(inputNew).trigger('focus');
  });
  
  
  /**
   * Add to wishlist form
   */
  $(document).on('submit', '[data-wishlist-ajax]', function (e) {
    e.preventDefault();
    
    var form = $(this);
    var responceFrame = $('[data-wishlist-ajax]');
    
    $.ajax({
      url: form.attr('action'),
      type: form.attr('method') ? form.attr('method') : 'get',
      data: form.serialize(),
      dataType: 'json',
      beforeSend: function () {
        
        $.mlsAjax.preloaderShow({
          type: 'frame',
          frame: form
        });
        
      },
      success: function (data) {
        if (data.reload) {
          location.reload();
          
          return
        }
        
        $.mlsAjax.loadResponseFrame(data.html, responceFrame);
        
        $.mlsAjax.transferData(data.html);
        
        $(document.body).trigger('wc_fragment_refresh');
      }
    });
    
  });
  
  /**
   * Open wishlist modal
   */
  $(document).on('click', '[data-wishlist-edit-modal]', function () {
    event.preventDefault();
    
    var button = $(this);
    var url = button.attr('data-wishlist-edit-modal--url');
    
    $.mlsModal({
      src: url
    });
    
  });
  
  /**
   * Delete item or list
   */
  $(document).on('click', '[data-wishlist-delete-button]', function () {
    $.mlsCart.clearFragments();
  });
  
})(jQuery);
;(function ($) {
  
  /* Remove ajax loader */
  $(document).on('ajaxStop', function () {
    $.mlsAjax.preloaderHide();
  });
  
  /* Hover to click on touch devices. Double click to link */
  $('[data-global-doubletap]').doubleTapToGo();
  
})(jQuery);
;(function ($) {
  
  $('[data-slider="banner-simple"]').each(function () {
    
    var el = $(this);
    
    el.find('[data-slider-slides]').removeAttr('data-slider-nojs').slick({
      adaptiveHeight: false,
      slidesToShow: 1,
      dots: el.data('dots'),
      arrows: el.data('arrows'),
      speed: el.data('speed'),
      autoplay: el.data('autoplay'),
      autoplaySpeed: el.data('autoplayspeed'),
      fade: el.data('fade'),
      infinite: el.data('infinite'),
      prevArrow: el.find('[data-slider-arrow-left]').removeClass('hidden'),
      nextArrow: el.find('[data-slider-arrow-right]').removeClass('hidden'),
      responsive: [
        {
          breakpoint: 992,
          settings: {
            dots: false
          }
        }
      ]
    });
    
  });
  
})(jQuery);
;(function ($) {
  
  var scope = $('[data-slider="mainpage-brands"]');
  var slides = $('[data-slider-slides]', scope).attr('data-slider-slides');
  
  $('[data-slider-slides]', scope).find('[data-slider-slide]').css('float', 'left').end().slick({
    dots: false,
    arrows: true,
    adaptiveHeight: false,
    slidesToShow: $.mlsSlider.getFirstCol(slides),
    autoplay: false,
    autoplaySpeed: 3000,
    swipeToSlide: true,
    mobileFirst: true,
    prevArrow: $('[data-slider-arrow-left]', scope).removeClass('hidden'),
    nextArrow: $('[data-slider-arrow-right]', scope).removeClass('hidden'),
    responsive: $.mlsSlider.getCols(slides)
  });
  
})(jQuery);
;
(function ($) {
  function crossSellsSlider() {
    $('[data-slider="cross-sells"]').each(function () {
      
      var scope = $(this);
      var slider = scope.find('[data-slider-slides]');
      var responsiveCols = slider.attr('data-slider-slides');
      
      /* Exit if slider is already initialized after Ajax request */
      if (typeof event !== 'undefined' && event.type === 'ajaxComplete' && slider.hasClass('slick-initialized')) {
        return;
      }
      
      slider.slick({
        dots: false,
        arrows: true,
        infinite: false,
        adaptiveHeight: true,
        slidesToShow: $.mlsSlider.getFirstCol(responsiveCols),
        autoplay: false,
        autoplaySpeed: 3000,
        swipeToSlide: true,
        mobileFirst: true,
        rows: 1,
        prevArrow: scope.find('[data-slider-arrow-left]').removeClass('hidden'),
        nextArrow: scope.find('[data-slider-arrow-right]').removeClass('hidden'),
        responsive: $.mlsSlider.getCols(responsiveCols)
      });
    });
  }
  
  $(document).on('ajaxComplete', crossSellsSlider);
  
  crossSellsSlider();
  
})(jQuery);
(function ($) {
    $(document).on('change','[data-payments-radio--control]', function () {
        var $this = $(this);
        var btnPay = $('#place_order');
        var btnPaymentCustomText = $this.attr('data-order_button_text');

        if(btnPaymentCustomText.length > 0){
            btnPay.val(btnPaymentCustomText);
        } else {
            btnPay.val(btnPay.attr('data-value'));
        }

    });
})(jQuery);
(function ($) {
  
  var selector = {
    btn: '[data-load-more-product--button]',
    container: '[data-load-more-product--container]',
    pagination: '[data-load-more-product--pagination]',
    next: '.next.page-numbers'
  };
  
  function AjaxCatalog() {
    
    $(document).on('click', selector.btn, this.loadMorebtnClickHandler.bind(this));
    //AJAX pagination
    //$(document).on('click','.page-numbers:not(.current)', this.paginationHandler.bind(this));
    
  }
  
  AjaxCatalog.prototype.loadMorebtnClickHandler = function (e) {
    e.preventDefault();
    
    var button = $(e.target).closest(selector.btn);
    var href = $(selector.next).attr('href');
    
    this.loadProduct(href);
    
    if (typeof this.pageStartNumber === 'undefined') {
      this.pageStartNumber = button.attr('data-load-more-product-current-page');
    }
  };
  
  AjaxCatalog.prototype.btnBeforeSend = function () {
    // add preloader
    this.btnAddLoader($(selector.btn));
  };
  
  AjaxCatalog.prototype.btnAddLoader = function (button) {
    
    setTimeout(function () {
      button.attr('disabled', 'disabled').addClass('is-active');
    }, 0);
  };
  
  AjaxCatalog.prototype.btnRemoveLoader = function (button) {
    $.mshButtons.removeLoader(button);
  };
  
  AjaxCatalog.prototype.btnUpdateState = function (data) {
    if (!data.has_next_page) {
      $(selector.btn).addClass('hidden');
    }
  };
  
  AjaxCatalog.prototype.successHandler = function (data) {
    
    this.currentPage = data.current_page;
    
    // append products
    // append pagination
    this.updateHtml(data);
    
    // remove preloader
    // unblock the button
    this.btnRemoveLoader($(selector.btn));
    
    // hide button if no more products
    this.btnUpdateState(data);
    
    // show more on scroll
  };
  
  AjaxCatalog.prototype.updateHtml = function (data) {
    $(selector.container).append(data.products);
    $(selector.pagination).replaceWith(
      this.updatePagination(data.pagination)
    );
  };
  
  AjaxCatalog.prototype.updatePagination = function (pagination) {
    
    var pageStartNumber = this.pageStartNumber;
    var currentPage = this.currentPage;
    var pagination = $(pagination);
    
    pagination.find('.page-numbers').each(function () {
      
      var paginateItem = $(this);
      var thisNumber = parseInt(paginateItem.text());
      
      if (!isNaN(thisNumber) && thisNumber >= pageStartNumber && thisNumber <= currentPage) {
        paginateItem.addClass('current');
      }
      
    });
    
    return pagination;
  };
  
  AjaxCatalog.prototype.loadProduct = function (url) {
    
    $.ajax({
      url: url,
      type: 'get',
      data: {
        ajaxCatalog: true
      },
      beforeSend: this.btnBeforeSend.bind(this),
      success: this.successHandler.bind(this)
    })
    
  };
  
  AjaxCatalog.prototype.paginationHandler = function (e) {
    e.preventDefault();
    
    var href = $(e.target).attr('href');
    
    this.loadProduct(href);
    
  };
  
  var ajaxCatalog = new AjaxCatalog();
  
  
})(jQuery);
(function ($) {

    /* DOM elements */
    var $filter = $('[data-premmerce-filter]');
    var $control = $filter.find('[data-premmerce-filter-link]');

    /* Event handlers */
    $control.on('change', followLink);

    /**
     * Launch filter after clicking on radio or checkbox control
     */
    function followLink(event) {
        event.preventDefault();
        location.href = $(this).attr('data-premmerce-filter-link');
    }


    /**
     * Price slider
     */
        //Selectors init
    var fieldScope = $('[data-premmerce-filter]');
    var form = $('[data-premmerce-filter-price-form]');
    var fieldMin = 'data-premmerce-filter-min';
    var fieldMax = 'data-premmerce-filter-max';
    var slider = 'data-premmerce-filter-range-slider';

    //Default valued at start
    var initialMinVal = parseFloat(fieldScope.find('[' + fieldMin + ']').attr(fieldMin));
    var initialMaxVal = parseFloat(fieldScope.find('[' + fieldMax + ']').attr(fieldMax));

    //Values after applying filter
    var curMinVal = parseFloat(fieldScope.find('[' + fieldMin + ']').attr('value'));
    var curMaxVal = parseFloat(fieldScope.find('[' + fieldMax + ']').attr('value'));

    //Setting value into form inputs when slider is moving
    fieldScope.find('[' + slider + ']').slider({
        min: initialMinVal,
        max: initialMaxVal,
        values: [curMinVal, curMaxVal],
        range: true,
        slide: function (event, elem) {
            var instantMinVal = elem.values[0];
            var instantMaxVal = elem.values[1];

            fieldScope.find('[' + fieldMin + ']').val(instantMinVal);
            fieldScope.find('[' + fieldMax + ']').val(instantMaxVal);

        },
        change: function () {
            form.trigger('submit');
            fieldScope.find('[' + fieldMin + '], [' + fieldMax + ']').attr('readOnly', true);
        }
    });

    /**
     * Setting value slider ranges when form inputs are changing
     */
    fieldScope.find('[' + fieldMin + '], [' + fieldMax + ']').on('change', function () {
        $('[' + slider + ']').slider('values', [
            fieldScope.find('[' + fieldMin + ']').val(),
            fieldScope.find('[' + fieldMax + ']').val()
        ]);
    });


    /**
     * Toogle filter block visibility
     */
    $(document).on('click', '[data-premerce-filter-drop-handle]', function (e) {
        e.preventDefault();

        $(this).closest('[data-premmerce-filter-drop-scope]').find('[data-premmerce-filter-inner]').slideToggle(300);
        $(this).closest('[data-premmerce-filter-drop-scope]').find('[data-premmerce-filter-drop-ico]').toggleClass('hidden', 300);
    });

    /**
     * Positioning scroll into the middle of checked value
     * Working only if scroll option in filter setting is true
     */
    $('[data-filter-scroll]').each(function () {
        var frame = $(this);
        var fieldActive = frame.find('[data-filter-control]:checked').first().closest('[data-filter-control-checkgroup]').find('[data-filter-control-label]');

        if (fieldActive.length > 0) {
            var fieldActivePos = fieldActive.offset().top - frame.offset().top;
            frame.scrollTop(fieldActivePos - (frame.height() / 2 - fieldActive.height()));
        }
    });
})(jQuery);
;(function ($) {
  
  $('[data-autocomplete="product-search"]').each(function () {
    var scope = $(this); //$('[data-autocomplete="product-search"]');
    var control = $('[data-autocomplete-input]', scope);
    var responseFrame = scope.find('[data-autocomplete-frame]');
    var itemsNum = scope.find('[data-autocomplete-product]').length;
    var noItemsFrame = scope.find('[data-autocomplete-noitems]');
    var products = $('[data-autocomplete-product]');
    var productsCount;
    var dataLength;
    var productWrapper;
    
    var appendProducts = function (data) {
      
      /**
       * Binding search result products json data into DOM vai data-attrs
       */
      $.each(data, function (index, product) {
        
        productWrapper = scope.find('[data-autocomplete-product="' + index + '"]');
        
        productWrapper.find('[data-autocomplete-product-name]').html(product.label);
        productWrapper.find('[data-autocomplete-product-price]').html(product.price);
        productWrapper.find('[data-autocomplete-product-img]').attr({'src': product.image, 'alt': product.label});
        productWrapper.attr('href', product.link);
        productWrapper.removeClass('hidden');
        
      });
      
      /**
       * Calculate numbers of search results  Minus 1 filters "queryString" extra key
       * @type {Number}
       */
      dataLength = Object.keys(data).length;
      
      /**
       * Hiding saved products from previous search results
       */
      for (var i = itemsNum; i >= dataLength; i--) {
        scope.find('[data-autocomplete-product="' + i + '"]').addClass('hidden');
      }
      
      return i;
    };
    
    var getAutocompleteData = function (request) {
      
      $.ajax({
        url: scope.attr('data-autocomplete-url'),
        method: 'get',
        data: {
          term: request.term
        },
        dataType: 'json',
        beforeSend: function () {
        },
        success: function (data) {
          
          /**
           * Append products into DOM after success search.
           * Return numver of products
           */
          productsCount = appendProducts(data);
          
          /**
           * Show "no results" message if no items had been found and hide it in other case
           */
          productsCount < 0 ? noItemsFrame.removeClass('hidden') : noItemsFrame.addClass('hidden');
          
          /**
           * Show autocomplete frame after search results
           */
          responseFrame.removeClass('hidden');
          
        }
      });
      
    };
    
    var kayDownHandler = function (event) {
      var keyDownCode = 40;
      var keyUpCode = 38;
      var enterCode = 13;
      
      if (responseFrame.hasClass('hidden')) {
        return
      }
      
      if (event.keyCode === enterCode || event.keyCode === keyDownCode || event.keyCode === keyUpCode) {
        event.preventDefault();
      }
      
      if (event.keyCode === enterCode) {
        redirectToProductPage(getActiveProduct());
      }
      
      if (event.keyCode === keyDownCode) {
        setActiveProduct('next');
      }
      
      if (event.keyCode === keyUpCode) {
        setActiveProduct('prev');
      }
      
    };
    
    var redirectToProductPage = function (product) {
      location.assign(product.attr('href'));
    };
    
    var getNextProduct = function (activeProduct) {
      
      if (activeProduct.is('[data-autocomplete-product]:last-child') || activeProduct.next().is('.hidden')) {
        return scope.find('[data-autocomplete-product]:first-child');
      }
      
      return activeProduct.next();
    };
    
    var getPrevProduct = function (activeProduct) {
      
      if (activeProduct.is('[data-autocomplete-product]:first-child')) {
        var product;
        
        products.each(function () {
          var currentProduct = $(this);
          
          if (!currentProduct.hasClass('hidden')) {
            product = currentProduct;
          }
          
        });
        
        return product;
        
      } else {
        return activeProduct.prev();
      }
      
    };
    
    var setActiveProduct = function (direction) {
      var activeProduct = getActiveProduct();
      var nextproduct;
      
      if (typeof activeProduct !== 'undefined' && activeProduct.length > 0) {
        
        if (direction === 'next') {
          nextproduct = getNextProduct(activeProduct);
        } else if (direction === 'prev') {
          nextproduct = getPrevProduct(activeProduct);
        }
        
        activeProduct.removeClass('is-active ');
        nextproduct.addClass('is-active');
        
      } else {
        products.first().addClass('is-active');
      }
      
    };
    
    var getActiveProduct = function () {
      var activeProduct = responseFrame.find('.is-active');
      return activeProduct.length > 0 ? activeProduct : false;
    };
    
    /**
     * Autocomplete plugin init
     */
    control.autocomplete({
      source: getAutocompleteData,
      minLength: 3,
      delay: 300
    });
    
    /**
     * Hide search results after clicking outside input field.
     * Also prevent closing after click inside results frame
     */
    $(document).on('click', function (event) {
      
      if ($(event.target).closest(responseFrame).length > 0) {
        event.stopPropagation();
      } else {
        noItemsFrame.addClass('hidden');
        responseFrame.addClass('hidden');
        products.removeClass('is-active');
      }
      
    });
    
    /**
     * Handle search result activation and got to product
     */
    $(document).on('keydown', kayDownHandler);
    
  });
  
})(jQuery);
;
(function ($) {
  
  /**
   * Quantity change
   */
  $(document).on('click', '[data-quantity-control]', function (e) {
        e.preventDefault();

        var scope = $(this).closest('[data-quantity]');
        //text field element and value
        var textField = scope.find('[data-quantity-field]');
        var currentValue = Number(textField.val().replace(',', '.'));
        // "+" and "-" controls
        var currentControl = $(this).attr('data-quantity-control');
        //amount on which value should increase or decrease
        var step = scope.find('[data-quantity-step]').attr('data-quantity-step');
        var stepValue = (Boolean(step) !== false) ? Number(step.replace(',', '.')) : 1;
        var minValue = textField.attr('data-quantity-min') ? textField.attr('data-quantity-min') : 0;
        var maxValue = ( textField.attr('data-quantity-max') && textField.attr('data-quantity-max') > 0 ) ? textField.attr('data-quantity-max') : Infinity;

        var newValue = currentValue;

        //Calculating result value depending on "+" or "-" button was clicked
        if (currentControl === 'minus') {
            newValue = (currentValue > minValue) ? currentValue - stepValue : minValue;
        }
        if (currentControl === 'plus') {
            newValue = (currentValue < maxValue) ? currentValue + stepValue : maxValue;
        }

        //insert result value into DOM
        textField.val(newValue);

        textField.trigger('premmerce_quantity_changed', [newValue, currentValue]);

        //Imitate native input field change
        textField.trigger('change');

    });
})(jQuery);
;
(function ($) {

  function socialWindow(url) {
    var left = (screen.width - 570) / 2;
    var top = (screen.height - 570) / 2;
    var width = '570px';
    var height = '570px';

    var option = 'scrollbars=yes, width=' + width + ', height=' + height + ', top=' + top + ', left=' + left;
    window.open(url,"NewWindow",option);
  }

  $(document).on('click','[data-social-share-link]',function (e) {
    e.preventDefault();
    var url = $(this).attr('href');

    socialWindow(url);
  });

})(jQuery);
;
(function ($) {
    $.mlsTime.countdown.init({
        scope: '[data-sales-timer]',
        item: '[data-sales-timer-item]'
    });
})(jQuery);
;(function ($) {

    //Move mobile menu to the left
    $('[data-mobile-nav-link]', '[data-mobile-nav]').on('click', function (e) {
        e.preventDefault();

        var targetLink = $(this);
        var childList = targetLink.closest('[data-mobile-nav-item]').find(' > [data-mobile-nav-list]');
        var parentLists = targetLink.parents('[data-mobile-nav-list]');
        var currentList = targetLink.closest('[data-mobile-nav-list]')

        //make fitst child list visible
        childList.removeClass('hidden');

        //move all parent lists to left
        parentLists.addClass('mobile-nav__list--is-moving');

        $('.page__mobile').scrollTop(0);

    });


    //Move mobile menu to the right
    $('[data-mobile-nav-go-back]', '[data-mobile-nav]').on('click', function (e) {
        e.preventDefault();

        var targetLink = $(this);
        var parentList = targetLink.closest('[data-mobile-nav-list]').parent().closest('[data-mobile-nav-list]');
        var childLists = parentList.find('[data-mobile-nav-list]');

        //move first parent list to right
        parentList.removeClass('mobile-nav__list--is-moving');

        //hide all children lists
        setTimeout(function () {
            childLists.addClass('hidden');
        }, 300);

    });

    /* Make "View all" link */
    $('[data-mobile-nav-viewAll]').each(function () {

        var viewAllLink = $(this);

        /* get parent category url */
        var parentCategoryUrl = viewAllLink
        .closest('[data-mobile-nav-list]')
        .closest('[data-mobile-nav-item]')
        .find('[data-mobile-nav-link]')
        .attr('href');

        /* set parent category URL as View All link */
        viewAllLink.attr('href', parentCategoryUrl);

        /* Make View All link visible when href has been set */
        viewAllLink.closest('[data-mobile-nav-item]').removeClass('hidden');
    });

})(jQuery);
/**
 * Changing main photo after clicking on thumb image
 */
(function ($) {
  
  $.mlsMedia.magnificGalley();
  $.mlsMedia.zoomImage();
  
  var videoStream = null;
  var loadVideo = function (selector, data, target) {
    selector.html(data);
    videoStream.on('load', function () {
      target.removeAttr('data-loader-frame-permanent').find('.spinner-circle').remove();
      target.addClass('hidden');
      selector.removeClass('hidden');
    });
  };
  
  $(document).on('click', '[data-product-photo-thumb]', function (e) {
    e.preventDefault();
    
    var currThumb = $(this);
    var context = currThumb.closest('[data-product-photo-scope]');
    
    var allThumbs = '[data-product-photo-thumb]';
    var activeThumb = '[data-product-photo-thumb-active]';
    var activeThumbPosition;
    var currGallery = currThumb.closest('[data-magnific-galley]');
    
    var largePhotoUrl = currThumb.attr('href');
    var mainPhotoUrl = currThumb.attr('data-product-photo-thumb');
    var targetLink = context.find('[data-product-photo-link]');
    var targetImg = context.find('[data-product-photo]');
    var zoomedImageLink = context.find('[data-zoom-image]');
    var videoFame = context.find('[data-product-photo-video-frame]');
    
    targetLink.removeClass('product-photo__item--no-photo');
    
    /* Toggle thumbs activity */
    context.find(allThumbs).removeAttr('data-product-photo-thumb-active');
    currThumb.attr('data-product-photo-thumb-active', '');
    
    /* Setting link to large photo in the main photo */
    targetLink.attr('href', largePhotoUrl);
    targetImg.attr('src', mainPhotoUrl);
    
    targetLink.removeClass('hidden');
    videoFame.addClass('hidden');
    
    if (currThumb.is('[data-product-photo-thumb-video]')) {
      targetLink.attr('data-loader-frame-permanent', '1').append('<i class="spinner-circle"></i>');
      
      if (!videoStream) {
        $.get(woocommerce_params.ajax_url, {
          action: 'premmerce_ajax_get_video',
          src: currThumb.attr('data-product-photo-thumb-video')
        }, function (video) {
          videoStream = $(video);
          loadVideo(videoFame, videoStream, targetLink);
        });
      } else {
        loadVideo(videoFame, videoStream, targetLink);
      }
      
    } else {
      zoomedImageLink.attr('data-zoom-image-url', largePhotoUrl);
      $.mlsMedia.zoomImage();
      
      /* Calculate index of active thumb among all thumbs */
      activeThumbPosition = context.find(allThumbs).index(context.find(activeThumb));
      
      /* Call magnific gallery and set active image */
      $.mlsMedia.magnificGalley(activeThumbPosition, currGallery);
      
      /* Remove youtube video after switching to image */
      videoFame.html('');
    }
    
  });
  
})(jQuery);
/* global woocommerce_params */
(function ($) {
    $(document).on('click', '[data-product-quick-view-button]', function (e) {
        e.preventDefault();

        var currProduct = $(this);
        var productBlock = currProduct.closest('[data-product]');
        var data = JSON.parse(currProduct.attr('data-product-quick-view-button'));

        $.ajax({
            url: woocommerce_params.wc_ajax_url.toString().replace('%%endpoint%%', 'premmerce_ajax_get_quick_view'),
            data: data,
            beforeSend: function () {
                $.mlsAjax.preloaderShow({
                    type: 'frame',
                    frame: productBlock
                });
            },
            success: function (response) {
                var $response =  $(response);
                $.magnificPopup.open({
                    items: {
                        src: $response,
                        type: 'inline'
                    },
                    showCloseBtn: false,
                    callbacks: {
                        open: function () {
                            $.mlsSlider.thumbsSlider();
                        }
                    }
                });

                var magnificLink = $response.find('[data-magnific-galley-main]');
                magnificLink.on('click', function (e) {
                    e.preventDefault();
                });
                magnificLink.removeAttr('data-magnific-galley-main');

                $response.find('.variations_form').wc_variation_form();

                $.mlsMedia.zoomImage();

                $.mlsTime.countdown.init({
                    scope: '[data-countdown]',
                    item: '[data-countdown-item]',
                    expireDateAttribute: 'data-countdown'
                });
            }
        });
    });
})(jQuery);
;
(function ($) {
  function sectionPrimarySlider(event) {
    $('[data-slider="section-primary"]').each(function () {
      
      var scope = $(this);
      var slider = scope.find('[data-slider-slides]');
      var responsiveCols = slider.attr('data-slider-slides');
      
      /* Exit if slider is already initialized after Ajax request */
      if (typeof event !== 'undefined' && event.type === 'ajaxComplete' && slider.hasClass('slick-initialized')) {
        return;
      }
      
      slider.slick({
        dots: false,
        arrows: true,
        infinite: false,
        adaptiveHeight: true,
        slidesToShow: $.mlsSlider.getFirstCol(responsiveCols),
        autoplay: false,
        autoplaySpeed: 3000,
        swipeToSlide: true,
        mobileFirst: true,
        rows: 1,
        prevArrow: scope.find('[data-slider-arrow-left]').removeClass('hidden'),
        nextArrow: scope.find('[data-slider-arrow-right]').removeClass('hidden'),
        responsive: $.mlsSlider.getCols(responsiveCols)
      });
      
      slider.on('afterChange', function () {
        $.mlsLazzyload.update();
      });
    });
  }
  
  $(document).on('ajaxComplete', sectionPrimarySlider);
  
  $(sectionPrimarySlider);
  
  
})(jQuery);
;
(function ($) {
  /**
   * Open social network login page on popup window
   */
  $(document).on('click', '[data-socauth-popup]', function (e) {
    e.preventDefault();
    
    
    var link = $(this);
    var popupWindow = {
      href: link.attr('href'),
      title: link.attr('title'),
      width: 500,
      height: 600
    };
    var left = (window.innerWidth / 2) - (popupWindow.width / 2);
    var top = (window.innerHeight / 2) - (popupWindow.height / 2);
    
    
    window.open(popupWindow.href, popupWindow.title, 'width=' + popupWindow.width + ',height=' + popupWindow.height + ',left=' + left + ',top=' + top);
    
  });
})(jQuery);
/**
 * Toogle widget visibility on mobile screen
 */
(function ($) {
  $(document).on('click', '[data-sidebar-widget--button]', function () {
    
    var scope = $(this).closest('[data-sidebar-widget--scope]');
    var toggleElements = scope.find('[data-sidebar-widget--toggle]');
    var body = scope.find('[data-sidebar-widget--toggle-body]');
    
    toggleElements.toggleClass('hidden');
    body.toggleClass('hidden-xs');
  });
  
  $('[data-sidebar-widget--header]').each(function () {
    var widgetHeader = $(this);
    var widgetBody = widgetHeader.next();
    
    widgetBody.addClass('hidden-xs');
    widgetBody.attr('data-sidebar-widget--toggle-body', '');
    
    var widgetContent = widgetBody.find('*').length;
    
    if (widgetContent === 0) {
      widgetBody.closest('[data-sidebar-widget--scope]').addClass('hidden');
    }
  });
  
  
})(jQuery);
;
(function ($) {
  
  //Selectors init
  var scope = $('.widget_price_filter');
  var form = scope.find('form');
  
  var fieldMin = '#min_price';
  var fieldMax = '#max_price';
  
  var sliderScope = '.price_slider_wrapper .clear'; //'data-range-slider';
  
  //Default valued at start
  var initialMinVal = parseFloat(scope.find(fieldMin).attr('data-min'));
  var initialMaxVal = parseFloat(scope.find(fieldMax).attr('data-max'));
  
  //Values after applying filter
  var curMinVal = parseFloat(scope.find(fieldMin).val());
  var curMaxVal = parseFloat(scope.find(fieldMax).val());
  
  //Setting value into form inputs when slider is moving
  $(sliderScope).slider({
    min: initialMinVal,
    max: initialMaxVal,
    values: [curMinVal, curMaxVal],
    range: true,
    slide: function (event, elem) {
      var instantMinVal = elem.values[0];
      var instantMaxVal = elem.values[1];
      
      scope.find(fieldMin).val(instantMinVal);
      scope.find(fieldMax).val(instantMaxVal);
      
    },
    change: function () {
      form.trigger('submit');
      scope.find(fieldMin + ',' + fieldMax).attr('readOnly', true);
    }
  });
  
  //Setting value slider ranges when form inputs are changing
  scope.find(fieldMin + ',' + fieldMax).on('change', function () {
    $(sliderScope).slider('values', [
      scope.find(fieldMin).val(),
      scope.find(fieldMax).val()
    ]);
  });
    
})(jQuery);