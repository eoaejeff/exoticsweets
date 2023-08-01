(function( $, document ) {
	var jckqv = {
		cache: function() {
			jckqv.els = {};
			jckqv.vars = {};
			jckqv.modal = {};
			jckqv.plugins = {};

			jckqv.vars.loaded_class = "jckqv-loaded";
			jckqv.vars.add_to_cart_params = false;
			jckqv.vars.add_to_cart_variation_params = false;

			jckqv.plugins.swatches = (typeof variation_calculator === 'function') ? true : false;
		},

		on_ready: function() {
			// on ready stuff here
			jckqv.cache();
			jckqv.setup_modal();
			jckqv.setup_qty_buttons();

			if ( jckqv.is_true( jckqv_vars.settings.popup_content_ajaxcart ) ) {
				jckqv.setup_add_to_cart();
			}

			if ( jckqv.is_true( jckqv_vars.settings.trigger_styling_autohide ) ) {
				jckqv.setup_modal_on_hover();
			}
		},

		/**
		 * Setup the quickview modal
		 */
		setup_modal: function() {

			$( 'body' ).on( jckqv_vars.settings.trigger_general_method, '[data-jckqvpid]', function( event ) {

				var products = jckqv.get_products(),
					product_id = $( this ).attr( 'data-jckqvpid' ),
					index = jckqv.get_index_by_product_id( products, product_id );

				jckqv.vars.add_to_cart_params = typeof wc_add_to_cart_params !== "undefined" ? wc_add_to_cart_params : false;
				jckqv.vars.add_to_cart_variation_params = typeof wc_add_to_cart_variation_params !== "undefined" ? wc_add_to_cart_variation_params : false;

				$.magnificPopup.open( {
					items: products,
					type: 'ajax',
					tLoading: jckqv_vars.text.loading,
					ajax: {
						settings: {
							type: 'POST'
						}
					},
					gallery: {
						enabled: jckqv.is_true( jckqv_vars.settings.popup_general_gallery ),
						preload: false
					},
					closeOnContentClick: false,
					callbacks: {
						ajaxContentAdded: function() {
							jckqv.cache_modal();
							jckqv.setup_modal_slider();
							jckqv.setup_modal_thumbnail_slider();
							jckqv.watch_variations();
							jckqv.watch_reset();

							$( document.body ).trigger( 'jckqv_open' );
							$( document.body ).trigger( 'quick-view-displayed' );

						},
						close: function() {
							jckqv.reset_modal();

							$( document.body ).trigger( 'jckqv_close' );
						},
						afterChange: function() {
							this.items = jckqv.get_products();
						},
						elementParse: function( item ) {
							this.st.ajax.settings.data = {
								action: 'jckqv',
								nonce: jckqv.nonce,
								product_id: this.items[ this.index ].data.product_id
							};
						}
					}
				}, index );

				return false;
			} );

		},

		/**
		 * Show modal on hover of button
		 */
		setup_modal_on_hover: function() {

			$( document ).on( {
				mouseenter: function() {

					// If there are no child products in this .product wrap
					// Primarily, the single product page
					if ( $( this ).find( jckqv_vars.settings.trigger_styling_hoverel ).length <= 0 ) {

						var qvBtn = $( this ).find( '*[data-jckqvpid]' );

						if ( qvBtn.length > 0 ) {
							qvBtn.css( { visibility: 'visible' } ).stop().animate( { opacity: 1 }, 150 );
						}

					}

				},
				mouseleave: function() {

					var qvBtn = $( this ).find( '*[data-jckqvpid]' );

					if ( qvBtn.length > 0 ) {
						qvBtn.stop().animate( { opacity: 0 }, 150, function() {
							$( this ).css( { visibility: 'hidden' } );
						} );
					}

				},
			}, jckqv_vars.settings.trigger_styling_hoverel );

		},

		/**
		 * Setup modal slider
		 */
		setup_modal_slider: function() {

			jckqv.modal.slider_wrap.css( { 'opacity': 0, 'max-height': 500 } );

			jckqv.modal.slider.imagesLoaded( function() {

				var fade = jckqv.is_true( jckqv_vars.settings.popup_imagery_imgtransition, "fade" ),
					autoplay = jckqv.is_true( jckqv_vars.settings.popup_imagery_autoplay ),
					infinite = jckqv.is_true( jckqv_vars.settings.popup_imagery_infinite ),
					arrows = jckqv.is_true( jckqv_vars.settings.popup_imagery_navarr ),
					asNavFor = ( jckqv_vars.settings.popup_imagery_thumbnails === "thumbnails" ) ? '#jckqv_thumbs' : null,
					dots = jckqv.is_true( jckqv_vars.settings.popup_imagery_thumbnails, "bullets" );

				jckqv.modal.slider.on( {
					mouseenter: function() {
						$( this ).addClass( 'jckqv-images--hovered' );
					},
					mouseleave: function() {
						$( this ).removeClass( 'jckqv-images--hovered' );
					},
					beforeChange: function( event, slick, current_slide_index, next_slide_index ) {
						// remove all active class
						$( '#jckqv_thumbs .slick-slide' ).removeClass( 'slick-main-active' );
						// set active class for current slide
						$( '#jckqv_thumbs .slick-slide[data-index=' + next_slide_index + ']' ).addClass( 'slick-main-active' );
					},
					init: function( event, slick ) {
						setTimeout( function() {
							$( '#jckqv select' ).trigger( 'change' );
						}, 500 );

						jckqv.modal.slider_wrap.hide().css( { 'opacity': 1, 'max-height': '' } ).fadeIn();
					}
				} ).slick( {
					adaptiveHeight: true,
					asNavFor: asNavFor,
					prevArrow: '<a href="javascript:void(0)" class="jckqv-images__arr jckqv-images__arr--prev"><i class="iconic-wqv-icon-chevron-with-circle-left"></i></a>',
					nextArrow: '<a href="javascript:void(0)" class="jckqv-images__arr jckqv-images__arr--next"><i class="iconic-wqv-icon-chevron-with-circle-right"></i></a>',
					infinite: infinite,
					fade: fade,
					speed: jckqv_vars.settings.popup_imagery_transitionspeed,
					autoplay: autoplay,
					autoplaySpeed: jckqv_vars.settings.popup_imagery_autoplaySpeed,
					arrows: arrows,
					dots: dots,
					easing: "easeInOutQuad"
				} );

			} );

		},

		/**
		 * Setup thumbnail slider
		 */
		setup_modal_thumbnail_slider: function() {

			if ( jckqv.is_true( jckqv_vars.settings.popup_imagery_thumbnails, "thumbnails" ) ) {

				if ( jckqv.modal.thumbnails.length > 0 ) {

					jckqv.modal.thumbnails.slick( {
						adaptiveHeight: true,
						asNavFor: '#jckqv_images',
						slidesToShow: 4,
						slidesToScroll: 1,
						focusOnSelect: true,
						arrows: false,
						infinite: jckqv.is_true( jckqv_vars.settings.popup_imagery_infinite ),
						speed: jckqv_vars.settings.popup_imagery_transitionspeed
					} );

				}

			}

		},

		/**
		 * Cache modal
		 */
		cache_modal: function() {

			jckqv.modal.slider_wrap = $( '#jckqv_images_wrap' );
			jckqv.modal.slider = $( '#jckqv #jckqv_images' );
			jckqv.modal.slider_nav = $( '#jckqv #jckqv_thumbs' );
			jckqv.modal.slider_clone = jckqv.modal.slider.clone();
			jckqv.modal.variations_form = $( '#jckqv form.variations_form' );
			jckqv.modal.thumbnails = $( '#jckqv #jckqv_thumbs' );
			jckqv.modal.sku = $( '#jckqv .sku' );
			jckqv.modal.available_variations = typeof jck_available_variations !== "undefined" ? jck_available_variations : false;

		},

		/**
		 * Get products on page
		 *
		 * @return obj
		 */
		get_products: function() {

			var products = [],
				product_ids = [];

			$( '[data-jckqvpid]' ).each( function( index, value ) {

				var product_id = $( this ).attr( 'data-jckqvpid' );

				if ( $.inArray( product_id, product_ids ) === - 1 ) {

					product_ids.push( product_id );
					products.push( {
						src: jckqv_vars.ajaxurl,
						product_id: product_id
					} );

				}

			} );

			$( document.body ).trigger( 'jckqv_getProducts' );

			return products;

		},

		/**
		 * Watch variations
		 */
		watch_variations: function() {

			// This triggers a show_variations event, if it hasn't
			// happened already and a default var is set for the product

			variation_loaded = jckqv.modal.variations_form.hasClass( jckqv.vars.loaded_class );

			jckqv.modal.variations_form.on( 'change', function() {

				if ( $( 'input[name=variation_id]' ).val() !== "" && ! variation_loaded ) {

					var purchasable = true,
						variation_id = parseInt( $( 'input[name=variation_id]' ).val() ),
						chosen_variation = [ { 'variation_id': $( 'input[name=variation_id]' ).val() }, purchasable ];

					//jckqv.get_variation_data( variation_id );

					jckqv.modal.variations_form.trigger( 'show_variation', chosen_variation );
					jckqv.modal.variations_form.addClass( jckqv.vars.loaded_class );

				}

			} );

			jckqv.modal.variations_form.on( 'show_variation', function( event, variation ) {

				if ( ! jckqv.modal.slider.hasClass( 'loading' ) ) {

					variation_id = ( jckqv.plugins.swatches ) ? $( 'input[name=variation_id]' ).val() : variation.variation_id;

					jckqv.go_to_image( variation_id );

					jckqv.modal.variations_form.addClass( jckqv.vars.loaded_class );

				}

			} );

			jckqv.modal.variations_form.on( 'found_variation', function( event, variation ) {

				if ( ! variation ) {
					return;
				}

				if ( variation.sku ) {
					jckqv.modal.sku.html( variation.sku );
				} else {
					jckqv.modal.sku.html( 'n/a' );
				}

			} );

			if ( jckqv.plugins.swatches ) {
				if ( $( 'input[name=variation_id]' ).val() !== "" ) {

					jckqv.modal.variations_form.trigger( 'show_variation' );

				}
			}

		},

		/**
		 * Watch for variation reset
		 */
		watch_reset: function() {

			jckqv.modal.variations_form.on( 'reset_image', function() {

				if ( ! jckqv.modal.slider.hasClass( 'loading' ) ) {
					if ( ! jckqv.modal.slider.hasClass( 'reset' ) ) {

						jckqv.go_to_image( 0 );

					}
				}

			} );

		},

		/**
		 * Go to image
		 *
		 * @param int variation_id
		 */
		go_to_image: function( variation_id ) {

			if ( ! jckqv.modal.slider.hasClass( 'slick-initialized' ) ) {
				return;
			}

			var $chosen_slide = $( jckqv.modal.slider_clone ).find( '[data-jckqv~="-' + variation_id + '-"]' ),
				slide_index = ($chosen_slide.length > 0) ? $chosen_slide.index() : 0;

			jckqv.modal.slider.slick( 'slickGoTo', slide_index );
			jckqv.modal.thumbnails.slick( 'slickGoTo', slide_index );

		},

		/**
		 * Setup quantity buttons
		 */
		setup_qty_buttons: function() {

			$( document ).on( 'click', '.jckqv-qty-spinner', function() {

				var $the_button = $( this ),
					$qty_wrapper = $the_button.closest( '.quantity' ),
					$qty_field = $qty_wrapper.find( '.qty' ),
					dir = $the_button.attr( 'data-dir' );

				if ( dir === "plus" ) {

					$qty_field.val( function( i, oldval ) {

						return ++ oldval;

					} );

				} else {

					$qty_field.val( function( i, oldval ) {

						if ( oldval > 1 ) {
							return oldval - 1;
						} else {
							return 1;
						}

					} );

				}

			} );

		},

		/**
		 * Setup Ajax add to cart functionality
		 */
		setup_add_to_cart: function() {

			$( "body" ).on( 'submit', "#jckqv form.cart", function( e ) {

				e.preventDefault();

				var $the_form = $( this ),
					$the_button = $the_form.find( 'button[type=submit]' ),
					query = $the_form.serialize(),
					atcOverlay = $( '#addingToCart' ),
					atcText = atcOverlay.find( 'span' ),
					atcIcon = atcOverlay.find( 'i' ),
					loadingIconClass = 'iconic-wqv-icon-cw animate-spin',
					addedIconClass = 'iconic-wqv-icon-ok',
					errorIconClass = 'iconic-wqv-icon-error';

				atcIcon.addClass( loadingIconClass ).removeClass( addedIconClass ).removeClass( errorIconClass );
				atcText.text( jckqv_vars.text.adding );
				atcOverlay.fadeIn();

				$( '#jckqv .onsale' ).fadeOut();

				jckqv.add_to_cart( query, $the_button, function( response ) {

					if ( response.fragments !== undefined ) { // Successful

						atcIcon.addClass( addedIconClass ).removeClass( loadingIconClass );
						atcText.text( jckqv_vars.text.added );

					} else {

						atcIcon.addClass( errorIconClass ).removeClass( loadingIconClass );
						atcText.text( response.message );

					}

					$( '#jckqv .onsale' ).delay( 2000 ).fadeIn();
					$the_form.find( ':disabled' ).prop( 'disabled', false );

					if ( jckqv_vars.settings.popup_content_autohidepopup === "1" && response.result !== "fail" ) {

						setTimeout( function() {
							var magnificPopup = $.magnificPopup.instance;
							magnificPopup.close();
						}, 2000 );

					} else {

						atcOverlay.delay( 2000 ).fadeOut();

					}

					return false;
				} );
			} );

		},

		/**
		 * Add to cart
		 *
		 * @param arr query
		 * @param obj $the_button
		 * @param function callback
		 */
		add_to_cart: function( query, $the_button, callback ) {
			var data = $.getQueryParameters( query );

			data.action = 'jckqv_add_to_cart';
			data.nonce = jckqv_vars.nonce;
			data.product_id = typeof data[ 'add-to-cart' ] === "undefined" ? $the_button.val() : data[ 'add-to-cart' ];

			delete data[ 'add-to-cart' ];

			$( document.body ).trigger( 'adding_to_cart', [ $the_button ] );

			$.get( jckqv_vars.ajaxurl, data, function( response ) {
				if ( response.fragments !== undefined ) {

					jckqv.update_cart_widget( response.fragments, response.cart_hash, $the_button );

				}

				if ( typeof callback === 'function' ) {
					callback( response );
				}
			} );
		},

		/**
		 * Update cart widget after adding to cart
		 *
		 * @param fragments
		 * @param cart_hash
		 */
		update_cart_widget: function( fragments, cart_hash, $the_button ) {
			var this_page = window.location.toString().replace( 'add-to-cart', 'added-to-cart' );

			// Block fragments class
			if ( fragments ) {
				$.each( fragments, function( key, value ) {
					$( key ).addClass( 'updating' );
				} );
			}

			// Block widgets and fragments
			$( '.shop_table.cart, .updating, .cart_totals' ).fadeTo( '400', '0.6' ).block( {
				message: null,
				overlayCSS: { opacity: 0.6 }
			} );

			// Replace fragments
			if ( fragments ) {
				$.each( fragments, function( key, value ) {
					$( key ).replaceWith( value );
				} );
			}

			// Unblock
			$( '.widget_shopping_cart, .updating' ).stop( true ).css( 'opacity', '1' ).unblock();

			// Cart page elements
			$( '.shop_table.cart' ).load( this_page + ' .shop_table.cart:eq(0) > *', function() {

				$( '.shop_table.cart' ).stop( true ).css( 'opacity', '1' ).unblock();

				$( document.body ).trigger( 'cart_page_refreshed' );
			} );

			$( '.cart_totals' ).load( this_page + ' .cart_totals:eq(0) > *', function() {
				$( '.cart_totals' ).stop( true ).css( 'opacity', '1' ).unblock();
			} );

			// Trigger event so themes can refresh other areas
			$( document.body ).trigger( 'added_to_cart', [ fragments, cart_hash, $the_button ] );
		},

		/**
		 * Helper: Get index in array by product ID
		 *
		 * @param array array
		 * @param int product_id
		 * @return int
		 */
		get_index_by_product_id: function( array, product_id ) {

			for ( var i = 0; i < array.length; i ++ ) {
				if ( array[ i ].product_id === product_id ) {
					return i;
				}
			}

			return 1;

		},

		/**
		 * Helper: Is true?
		 *
		 * @param mixed value
		 * @param mixed comparison
		 * @return bool
		 */
		is_true: function( value, comparison ) {

			comparison = typeof comparison !== "undefined" ? comparison : 1;
			value = $.isNumeric( value ) ? parseInt( value ) : value;

			return (value === comparison) ? true : false;

		},

		/**
		 * Reset modal
		 */
		reset_modal: function() {

			if ( jckqv.vars.add_to_cart_params ) {
				wc_add_to_cart_params = jckqv.vars.add_to_cart_params;
			}
			if ( jckqv.vars.add_to_cart_variation_params ) {
				wc_add_to_cart_variation_params = jckqv.vars.add_to_cart_variation_params;
			}

		}

	};

	$( document ).ready( jckqv.on_ready() );

}( jQuery, document ));
(function($) {

    $.extend({

        getQueryParameters : function(str) {
            return (str || document.location.search).replace(/(^\?)/,'').split("&").map(function(n){return n = n.split("="),this[n[0]] = n[1],this;}.bind({}))[0];
        }

    });

})(jQuery);