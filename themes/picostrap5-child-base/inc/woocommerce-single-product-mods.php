<?php

// REMOVE SKU FROM PRODUCT PAGE
add_filter( 'wc_product_sku_enabled', 'bbloomer_remove_product_page_sku' );
  
function bbloomer_remove_product_page_sku( $enabled ) {
	if ( ! is_admin() && is_product() ) {
		return false;
	}
	return $enabled;
}



//Change Price Priority
remove_action("woocommerce_single_product_summary" , "woocommerce_template_single_price" , 10);
add_action("woocommerce_single_product_summary" , "woocommerce_template_single_price" , 12);

//Add Stock After Price
add_action("woocommerce_single_product_summary", function() {
	global $product;
	echo wc_get_stock_html( $product ); // WPCS: XSS ok.
}, 13);

//Wrap Price and Stock
add_action("woocommerce_single_product_summary", function() {
	
	echo " <div class='d-flex align-items-center price-stock-wrap'> ";
}, 11);

//End Wrap Price and Stock
add_action("woocommerce_single_product_summary", function() {
	
	echo " </div> ";
}, 14);


// REMOVE ADDITIONAL PRODUCT INFO
add_filter( 'woocommerce_product_tabs', 'bbloomer_remove_product_tabs', 9999 );
  
function bbloomer_remove_product_tabs( $tabs ) {
	unset( $tabs['additional_information'] ); 
	return $tabs;
}




// ADD PLUS MINUS BUTTONS
  
add_action( 'woocommerce_after_quantity_input_field', 'bbloomer_display_quantity_plus' );
function bbloomer_display_quantity_plus() {
   echo '<button type="button" class="plus">+</button>';
}
  
add_action( 'woocommerce_before_quantity_input_field', 'bbloomer_display_quantity_minus' );
function bbloomer_display_quantity_minus() {
   echo '<button type="button" class="minus">-</button>';
}
   
add_action( 'wp_footer', 'bbloomer_add_cart_quantity_plus_minus' );
function bbloomer_add_cart_quantity_plus_minus() {
 
   if ( ! is_product() && ! is_cart() ) return;
	
   wc_enqueue_js( "   
		   
	  $(document).on( 'click', 'button.plus, button.minus', function() {
  
		 var qty = $( this ).parent( '.quantity' ).find( '.qty' );
		 var val = parseFloat(qty.val());
		 var max = parseFloat(qty.attr( 'max' ));
		 var min = parseFloat(qty.attr( 'min' ));
		 var step = parseFloat(qty.attr( 'step' ));
 
		 if ( $( this ).is( '.plus' ) ) {
			if ( max && ( max <= val ) ) {
			   qty.val( max ).change();
			} else {
			   qty.val( val + step ).change();
			}
		 } else {
			if ( min && ( min >= val ) ) {
			   qty.val( min ).change();
			} else if ( val > 1 ) {
			   qty.val( val - step ).change();
			}
		 }
 
	  });
		
   " );
}



// Change regular price to variation price instead of displaying price twice
add_action( 'woocommerce_variable_add_to_cart', 'bbloomer_update_price_with_variation_price' );
  
function bbloomer_update_price_with_variation_price() {
   global $product;
   $price = $product->get_price_html();
   wc_enqueue_js( "      
	  $(document).on('found_variation', 'form.cart', function( event, variation ) {   
		 if(variation.price_html) $('.summary > p.price').html(variation.price_html);
		 $('.woocommerce-variation-price').hide();
	  });
	  $(document).on('hide_variation', 'form.cart', function( event, variation ) {   
		 $('.summary > p.price').html('" . $price . "');
	  });
   " );
}




// Change add to cart text on single product page
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woocommerce_add_to_cart_button_text_single' ); 
function woocommerce_add_to_cart_button_text_single() {
	return __( 'Add to Box', 'woocommerce' ); 
}

// Change add to cart text on product archives page
add_filter( 'woocommerce_product_add_to_cart_text', 'woocommerce_add_to_cart_button_text_archives' );  
function woocommerce_add_to_cart_button_text_archives() {
	return __( 'Add to Box', 'woocommerce' );
}