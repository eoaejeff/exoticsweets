( function( $, window, document, params, undefined ) {
    'use strict';

    if ( !$.fn.jQueryModal || 'undefined' === typeof params ) {
        return false;
    }

    let QuickViewPro = ( function( debugOn ) {

		const debugMode = Number( params.debug ) || debugOn;

        const modalClass = '.wc-quick-view-modal',
            modalOptions = {
                escapeClose: false,
                clickClose: false,
                showClose: false
            },
            $spinner = $( '<div class="' + $.modal.defaults.modalClass + '-spinner"></div>' ).append( $.modal.defaults.spinnerHtml ),
            $html = $( 'html' );

        let modalTimeout = null;

        let modalIsOpen = false;
        let galleryIsOpen = false;
        let $pswp;

        window.dontCloseQVP = false;

        function initialize() {
            bindEvents();
            window.wp = window.wp || { };
        }

        function bindEvents() {

            $pswp = $( '.pswp' );

            $( document.body )
                .on( 'click', closeOpenModals )
                .on( 'keydown', checkEscapeClose )
                .on( 'click', 'a[rel="qvp-modal:close"]', closeOpenModals )
                .on( 'click', '[data-action="quick-view"]', onQuickViewClick )
                .on( 'quick_view_pro:before_open', onBeforeOpen )
                .on( 'quick_view_pro:open_complete', onOpenComplete )
                .on( $.modal.BEFORE_OPEN, modalClass, onModalBeforeOpen )
                .on( $.modal.BEFORE_CLOSE, modalClass, onModalBeforeClose )
                .on( 'quick_view_pro:load', onLoad )
                .on( 'quick_view_pro:open', onOpen )
                .on( 'quick_view_pro:close', onClose )
                .on( 'quick_view_pro:open_fail', onOpenFail )
                .on( 'quick_view_pro:before_add_to_cart', onBeforeAddToCart )
                .on( 'quick_view_pro:add_to_cart_complete', onAddToCartComplete )
                .on( 'submit', `${modalClass} .cart, ${modalClass} .wcbvp-cart`, onAddToCart )
                .on( 'quick_view_pro:add_to_cart_fail', onAddToCartFail )
				.on( 'click', '.woocommerce-product-gallery__trigger', galleryOpened )
				.on( 'updated_checkout', closeOpenModals );

            $pswp.on( 'click', preventModalClose );

			// if Bulk Variations is installed prevent modal close when pool item delete icon is clicked
            $( '.wcbvp-variation-pool-item .action-delete' ).on( 'click', preventModalClose );

            if ( params.enable_product_link && params.product_link_selector ) {
                $( document.body ).on( 'click', params.product_link_selector, onQuickViewClick );
            }
        }

        function preventModalClose( e ) {
            window.dontCloseQVP = true;
        }

        function galleryOpened( e ) {
            galleryIsOpen = true;
        }

        function checkEscapeClose( e ) {
            if ( e.key === 'Escape' && ! $pswp.hasClass( 'pswp--open' ) ) {
                window.dontCloseQVP = false;
                closeOpenModals();
            }
            if ( e.key === 'Escape' && ( galleryIsOpen || $pswp.hasClass( 'pswp--open' ) ) ) {
                galleryIsOpen = false;
                window.dontCloseQVP = false;
            }
        }

        function closeOpenModals( e ) {
            if ( galleryIsOpen && ! $pswp.hasClass( 'pswp--open' ) ) {
                galleryIsOpen = false;
                window.dontCloseQVP = true;
                setTimeout( function() {
                    window.dontCloseQVP = false;
                }, 100 );
                return;
            }

            if ( window.dontCloseQVP ) {
                setTimeout( function() {
                    window.dontCloseQVP = false;
                }, 100 );
                return;
            }

            if ( modalIsOpen ) {
                let $isClickWithinModal = e ? $( e.target ).closest( modalClass ) : [];
                if ( ! e || $(e.target).attr('rel') === 'qvp-modal:close' || $isClickWithinModal.length === 0 ) {
                    if ( e ) {
                        e.preventDefault();
                        e.stopPropagation();
                    }

                    $( document.body ).trigger( 'quick_view_pro:closed' );
                    $.modal.close();
                    galleryIsOpen = false;
                    window.dontCloseQVP = false;
                }
            }
        }

        function openQuickView( productId, $trigger ) {
            productId = parseInt( productId, 10 );
            $trigger = $trigger || [];

            if ( !productId ) {
                // Bail if we couldn't find a product ID.
                return false;
            }

            // Bail if already clicked but not opened (prevents opening twice if double-clicked).
            if ( $trigger.data( 'clicked' ) ) {
                return false;
            }

            $trigger.data( 'clicked', true );
            let $modal = $( '#quick-view-' + productId );

            if ( $modal.length ) {
                // Modal was previoulsy opened, so let's re-open it.
                $modal.jQueryModal( modalOptions );
                $trigger.data( 'clicked', false );
                modalIsOpen = true;
            } else {
                $( document.body ).trigger( 'quick_view_pro:before_open', [productId] );

                // Otherwise fetch modal data from server.
                $.ajax( {
                    url: params.rest_url + '/' + params.rest_endpoints.view + '/' + productId,
                    method: 'GET',
                    dataType: 'json',
                    beforeSend: function( xhr ) {
                        // Send the nonce so the REST API can authenticate the user if logged in.
                        xhr.setRequestHeader( 'X-WP-Nonce', params.rest_nonce );
                    }
                } )
                    .done( function( response, status, xhr ) {
                        // Check for refreshed nonce returned from REST API and update our global param.
                        const nonce = xhr.getResponseHeader( 'X-WP-Nonce' );

                        if ( nonce ) {
                            params.rest_nonce = nonce;
                        }

                        if ( !( 'quick_view' in response ) ) {
                            $( document.body ).trigger( 'quick_view_pro:open_fail', [productId] );
                            return;
                        }

                        // Add response to body and open modal.
                        $( response.quick_view )
                            .jQueryModal( modalOptions );

                        const modal = $.modal.getCurrent();
                        modal.$elm.append( $('<a href="#close-modal" rel="qvp-modal:close" class="close-modal">Close</a>' ) );

                        if ( modal ) {
                            modal.$elm.trigger( 'quick_view_pro:load', [modal.$elm] );
                        }
                    } )
                    .fail( function( xhr, status, error ) {
                        $( document.body ).trigger( 'quick_view_pro:open_fail', [productId] );
                    } )
                    .always( function() {
                        $trigger.data( 'clicked', false );
                        $( document.body ).trigger( 'quick_view_pro:open_complete', [productId] );
                    } );
            }

            return true;
        }

        function initializeModal( $modal ) {
            if ( $modal.find( '.wc-quick-view-product-summary' ).length ) {
                initializeVariations( $modal );
                initializeMedia( $modal );
                initializePlugins( $modal );
            }

            if ( $modal.find( '.wc-quick-view-product-gallery' ).length ) {
                initializeGallery( $modal );
            }

            if ( $modal.find( '.wc-quick-view-product-tabs' ).length ) {
                initializeTabs( $modal );
            }
        }

        function initializeGallery( $modal ) {
            if ( !$.fn.wc_product_gallery ) {
                return;
            }

            let $gallery = $modal.find( '.woocommerce-product-gallery' );

            if ( $gallery.length ) {
                // Initialise product gallery.

                // pre-ES6 clone to make sure we don't change the global params
                var wasEnabled = wc_single_product_params.flexslider_enabled !== false;
                wc_single_product_params.flexslider_enabled = true;

                wc_single_product_params.flexslider_enabled = true;
                wc_single_product_params.flexslider.controlNav = params.navStyle;

                $gallery.first().wc_product_gallery( wc_single_product_params );

                wc_single_product_params.flexslider_enabled = wasEnabled;

                // Ensure correct width for gallery images.
                let imageWidth = $modal.data( 'imageWidth' );

                if ( imageWidth ) {
                    if ( $modal.hasClass( 'with-thumbnail' ) && !$modal.hasClass( 'with-details' ) ) {
                        $modal.css( 'max-width', imageWidth + 'px' );
                    }

                    $gallery.css( 'width', imageWidth + 'px' );
                }
            }
        }

        function initializeMedia( $modal ) {
            // Initialise audio and video playlists
            if ( 'undefined' !== typeof window.WPPlaylistView ) {
                $modal.find( '.wp-playlist:not(:has(.mejs-container))' ).each( function() {
                    new window.WPPlaylistView( { el: this } );
                } );
            }

            // Initialise audio and video shortcodes
            if ( 'mediaelement' in window.wp ) {
                window.wp.mediaelement.initialize();
            }
        }

        function initializeTabs( $modal ) {
            $( '.wc-tabs-wrapper, .woocommerce-tabs' ).trigger( 'init' );
        }

        function stopMedia( $modal ) {
            // Pause any currently playing audio/video.
            $modal.find( '.mejs-container' ).find( 'audio, video' ).each( function() {
                if ( this.pause ) {
                    this.pause();
                }
            } );
        }

        function initializePlugins( $modal ) {
            // WooCommerce Product Table.
            if ( $.fn.productTable ) {
                $modal.find( '.wc-product-table' ).productTable();
            }

            if ( $modal.find( '.cart' ).length ) {
                // WooCommerce Product Addons -initialise by triggering quick-view-displayed.
                $modal.trigger( 'quick-view-displayed' );

                // WooCommerce Bundled Products.
                if ( $.fn.wc_pb_bundle_form ) {
                    $modal.find( '.bundle_form .bundle_data' ).each( function() {

                        let $bundleData = $( this ),
                            $compositeForm = $bundleData.closest( '.composite_form' );

                        // If not part of a composite, initialize, otherwise let composite products handle it.
                        if ( !$compositeForm.length ) {
                            $bundleData.wc_pb_bundle_form();
                        }
                    } );
                }

                // WooCommerce Composite Products.
                if ( $.fn.wc_composite_form ) {
                    $modal.find( '.composite_form .composite_data' ).each( function() {
                        $( this ).wc_composite_form();
                    } );
                }
            }
        }

        function initializeVariations( $modal ) {
            if ( !$.fn.wc_variation_form ) {
                return;
            }

            // Initialise variations form.
            $modal.find( '.variations_form' ).each( function() {
                $( this ).wc_variation_form();
            } ).on( 'woocommerce_variation_has_changed', function() {
                let $variationIdInput = $( 'input[name="variation_id"]', this );

                // Ensure 'variation_id' input is not blank - it must be 0 or a valid product ID, otherwise it breaks the product addons subtotal.
                if ( $variationIdInput.length && '' === $variationIdInput.val() ) {
                    $variationIdInput.val( '0' );
                }
            } ).on( 'reset_data', function() {
                clearNotices( $modal );
            } );
        }

        function getCartData( $cartForm ) {
            let data = serializeForm( $cartForm ),
                productId = $cartForm.find( '[name="add-to-cart"]' ).val();

            // Remove add-to-cart (if present) as we use product_id.
            delete data['add-to-cart'];
            data.product_id = productId;

            return data;
        }

		/**
         * Serialize a form to an object, in this format: { input1: 'value', input2: 'some value', etc }.
         *
         * @param jQuery $form The form to parse
         * @returns Object The serialized form
         */
        function serializeForm( $form ) {
            const data           = {},
				  serializedForm = $form.serializeArray(),
                  addonCount     = 0;

			for ( const input of serializedForm ) {
				let m = input.name.match( /^quantity\[(\d+)]$/ );
				if ( m ) {
					// the input is part of an array of quantities
					// i.e. GROUPED PRODUCT or BULK VARIATIONS
					if ( ! data.quantity ) {
						data.quantity = {};
					}
					data.quantity[ Number( m[1] ) ] = Number( input.value );
                    continue;
                }

                m = input.name.match( /(addon.*)\[(\d*)]/ );
                if ( m ) {
                    // the input is part of an array of addons
                    // i.e. WooCommerce Product Addons
					if ( ! data[ m[1] ] ) {
						data[ m[1] ] = [];
					}
					data[ m[1] ].push( input.value );
                    continue;
                }

                if ( input.name.endsWith( '[]' ) ) {
                    let name = input.name.substring( 0, input.name.length - 2 );
                    if ( ! ( name in data ) ) {
                        data[ name ] = [];
                    }

                    data[ name ].push( input.value );
                    continue;
                }

                data[ input.name ] = input.value;
			}

            return data;
        }

        function validateCart( $cartForm ) {
            let cartData = getCartData( $cartForm ),
                result = {
                    isValid: false,
                    message: false,
                    cartData: null
                };

            if ( $.isEmptyObject( cartData ) ) {
                result.message = params.messages.cart_error;
                return result;
            }

			// if product_id is undefined and the current form is not from WooCommerce Bulk Variations
			if ( ! ( 'product_id' in cartData ) && ! $cartForm.closest('.wc-quick-view-modal.has-quick-view-pro').length ) {
                result.message = params.messages.no_product_id;
                return result;
            }

			// For single and variable products, check if the quantity input is greater than zero
            if ( 0 === cartData.quantity ) {
                result.message = params.messages.zero_quantity;
                return result;
            }

			// For grouped products of Bulk Variations submissions,
			// check if quantity is an object
			// and the sum of the quantity values is greater than zero
            if ( Object.prototype.isPrototypeOf( cartData.quantity ) && 0 === Object.values( cartData.quantity ).reduce( (s, a) => s + Number( a ), 0 ) ) {
                result.message = params.messages.zero_quantity;
                return result;
            }

            // Check variation has been selected.
            if ( 'variation_id' in cartData && ( 0 === parseInt( cartData.variation_id, 10 ) ) ) {
                result.message = params.messages.option_required;
                return result;
            }

            // Validate required options/addons.
            $( ':input', $cartForm ).each( function() {
                let name = $( this ).prop( 'name' );

                // Strip [] from input name if present, to match properties in formData.
                // if ( -1 !== name.indexOf( '[]' ) ) {
                //     name = name.replace( '[]', '' );
                // }

                if ( $( this ).prop( 'required' ) ) {
                    if ( !( name in cartData ) || '' === cartData[name] ) {
                        result.message = params.messages.option_required;
                        return result;
                    }
                }
            } );

            if ( !result.message ) {
                result.isValid = true;
            }

            result.cartData = cartData;
            return result;
        }

        function showCartError( $cartForm, error ) {
            if ( !$cartForm || !error || !$cartForm.length ) {
                return;
            }

            $cartForm.append( `<div class="wc-quick-view-notice error-notice">${error}</div>` );
        }

        function clearNotices( $modal ) {
            $modal.find( '.wc-quick-view-notice, .wc-quick-view-success-wrap' ).remove();
        }

        function resetCartForm( $modal ) {
            let $cartForm = $modal.find( '.cart' );

            if ( !$cartForm.length ) {
                return;
            }

            // Reset quantity.
            let $quantity = $cartForm.find( 'input[name^="quantity"]' );

            if ( $quantity.length ) {
                $quantity.val( $quantity.attr( 'value' ) );
            }

			$cartForm.find('select').each( ( i, e ) => {
				$( e ).val( $( e ).find( 'option:selected' ).val() );
			});

            // Reset addons.
            let $addons = $cartForm.find( '.wc-pao-addon' );

            if ( $addons.length ) {
                $addons.find( 'input' ).each( function() {
                    let $type = $( this ).attr( 'type' );

                    if ( 'checkbox' === $type || 'radio' === $type ) {
                        $( this ).prop( 'checked', false );
                    } else {
                        $( this ).val( '' );
                    }
                    $( this ).change();
                } );

                $addons.find( 'select' ).find( 'option:first' ).prop( 'selected', true ).change();
                $addons.find( 'textarea' ).val( '' ).change();
            }
        }

        // EVENTS

        function onQuickViewClick( event ) {

            // We use currentTarget rather than target to get the clicked link, in case click event bubbled up.

            let $trigger = $( event.currentTarget ),
                productId = $trigger.data( 'product_id' ) || 0;

            let $isDisabled = $trigger.closest( '.qvp-disabled' );
            if ( $isDisabled.length ) {
                // this product has been disabled from Quick View, probably by a category
                return true;
            }

            if ( !productId ) {
                // If product ID not set on link/button, look in outer product.
                let $product = $trigger.closest( '.product' );

                if ( !$product.length ) {
                    return true;
                }

                // Search for product ID within outer product.
                productId = $product.find( '[data-product_id]' ).data( 'product_id' );

                if ( !productId ) {
                    // Still not found, so look in surrounding post class (usually a <li>).
                    const classMatch = $product.attr( 'class' ).match( /post\-(\d+)/ );

                    if ( 1 in classMatch ) {
                        productId = classMatch[1];
                    }
                }

                // Bail if we couldn't find a product ID.
                if ( !productId ) {
                    return true;
                }
            }

            // Remove focus from clicked link or button.
            $trigger.blur();

            // Open the quick view.
            openQuickView( productId, $trigger );

            event.preventDefault();
            event.stopPropagation();

            return false;
        }

        // Triggered before we call AJAX to open the QV.
        function onBeforeOpen() {
            $spinner.appendTo( document.body ).show();
        }

        // Triggered after the AJAX open QV call is complete.
        function onOpenComplete() {
            $spinner.remove();
            modalIsOpen = true;
        }

        // Triggered by jQuery Modal just before the modal is displayed, and after AJAX complete.
        function onModalBeforeOpen( event, modal ) {
            modal.$elm.trigger( 'quick_view_pro:open', [modal.$elm] );
        }

        // Triggered by jQuery Modal just before the modal is closed.
        function onModalBeforeClose( event, modal ) {
            modal.$elm.trigger( 'quick_view_pro:close', [modal.$elm] );
            modalIsOpen = false;
        }

        // Triggered each time the QV is opened.
        function onOpen( event, $modal ) {
            // Prevent scroll on <html> element (jQuery Modal handles <body>)
            $html.addClass( 'qvp-modal-is-open' );

            if ( $modal.find( '.cart' ).length ) {
                clearNotices( $modal );
                resetCartForm( $modal );
            }
        }

        // Triggered each time the QV is closed.
        function onClose( event, $modal ) {
            // Reset scroll on <html>
            $html.removeClass( 'qvp-modal-is-open' );
            stopMedia( $modal );

            // If modal timeout (add to cart) is set, clear it on close.
            if ( modalTimeout ) {
                clearTimeout( modalTimeout );
                modalTimeout = null;
            }
        }

        // Triggered once when the QV is first loaded via AJAX.
        function onLoad( event, $modal ) {
            initializeModal( $modal );
        }

        // Triggered when the QV fails to open.
        function onOpenFail() {
            $( '<div class="wc-quick-view-modal wc-quick-view-error wc-quick-view-message-only"><p>' + params.messages.open_error + '</p></div>' ).jQueryModal( modalOptions );
        }

        function onAddToCart( event ) {
			let $cartForm = $( event.target ),
                $modal = $cartForm.closest( modalClass ),
                $cartButton = $cartForm.find( '.single_add_to_cart_button' ) || [];

            // Fallback for themes which don't use the .single_add_to_cart_button class.
            if ( !$cartButton.length ) {
                $cartButton = $cartForm.find( 'button[type="submit"]' );
            }

            if ( $cartButton.hasClass( 'loading' ) || $cartButton.hasClass( 'disabled' ) ) {
                return false;
            }

            // Clear previous cart errors.
            clearNotices( $modal );

            if ( $modal.hasClass( 'external-product' ) ) {
                window.location.href = $cartForm.attr( 'action' );
                return false;
            }

            // Validate the cart form.
            let formValidation = validateCart( $cartForm ),
                cartData = formValidation.cartData;

            if ( !formValidation.isValid ) {
                showCartError( $cartForm, formValidation.message );
                return false;
            }

            $modal.trigger( 'quick_view_pro:before_add_to_cart', [cartData] );

			// if Fast Cart is installed and the autoOpen option is active
			// let Fast Cart manage the add-to-cart event
			if ( window.wc_fast_cart_params && wc_fast_cart_params.options.autoOpen ) {
				return false;
			}

			$cartButton.addClass( 'loading' );

            // Trigger WooCommerce 'adding_to_cart' event.
            $( document.body ).trigger( 'adding_to_cart', [$cartButton, cartData] );

            $.ajax( {
                url: params.rest_url + '/' + params.rest_endpoints.cart,
                type: 'POST',
                data: cartData,
                dataType: 'json',
                beforeSend: function( xhr ) {
                    // Send the nonce so the REST API can authenticate the user if logged in.
                    xhr.setRequestHeader( 'X-WP-Nonce', params.rest_nonce );
                }
            } )
                .done( function( response, status, xhr ) {
                    // Check for errors.
                    if ( response.error ) {
                        showCartError( $cartForm, response.error );
                        return;
                    }

                    // Check 'redirect after adding to cart' option.
                    if ( wc_add_to_cart_params && 'yes' === wc_add_to_cart_params.cart_redirect_after_add ) {
                        window.location = wc_add_to_cart_params.cart_url;
                        return;
                    }

                    // Add success message.
                    if ( response.cart_message ) {
                        $modal.append( $( '<div class="wc-quick-view-success-wrap" />' ).append( `<p class="wc-quick-view-notice success-notice">${response.cart_message}</p>` ) );

                        $modal.trigger( 'quick_view_pro:added_to_cart', [response] );

                        // Trigger WooCommerce 'added_to_cart' event so theme can refresh fragments.
                        $( document.body ).trigger( 'added_to_cart', [response.fragments, response.cart_hash] );

                        modalTimeout = setTimeout( function() {
							resetCartForm( $modal );
                            $( document.body ).trigger( 'quick_view_pro:closed' );
                            $.modal.close();
                        }, 1500 );
                    }
                } )
                .fail( function() {
                    $modal.trigger( 'quick_view_pro:add_to_cart_fail', [cartData] );
                } )
                .always( function() {
                    $cartButton.removeClass( 'loading' );
                    $modal.trigger( 'quick_view_pro:add_to_cart_complete', [cartData] );
                } );

            return false;
        }

        function onBeforeAddToCart() {
            const modal = $.modal.getCurrent();

            // Prevent modal close action while add to cart is running.
            if ( modal ) {
                $( document ).off( 'keydown.modal' );
                modal.$elm.find( 'a.close-modal' ).attr( 'rel', '' );
            }
        }

        function onAddToCartComplete() {
            const modal = $.modal.getCurrent();

            // Reset close modal action.
            if ( modal ) {
                $( document ).on( 'keydown.modal', function( event ) {
                    if ( 27 === event.which && modal.options.escapeClose ) {
                        $( document.body ).trigger( 'quick_view_pro:closed' );
                        modal.close();
                    }
                } );
                modal.$elm.find( 'a.close-modal' ).attr( 'rel', 'qvp-modal:close' );
            }
        }

        function onAddToCartFail( event, cartData ) {
            let $modal = $( event.target ),
                $cartForm = $modal.find( '.cart' );

            showCartError( $cartForm, params.messages.cart_error );
        }

		function debug( ...params ) {
            if ( ! debugMode ) {
                return;
            }
            console.trace( 'WQV Event:', ...params );
        }

        // Public API.
        return {
            initialize: initialize,
            openQuickView: openQuickView,
            handleQuickViewClick: onQuickViewClick
        };

    } )();

    // Expose API.
    window.WCQuickViewPro = QuickViewPro;

    $( () => {
        QuickViewPro.initialize();
    } );

} )( jQuery, window, document, wc_quick_view_pro_params );
