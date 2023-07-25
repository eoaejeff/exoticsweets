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


// CHANGE RELATED PRODUCTS TEXT
add_filter( 'woocommerce_product_related_products_heading', 'bbloomer_rename_related_products' );
 
function bbloomer_rename_related_products() {
   return "Customers Also Viewed";
}


// MOVE PRODUCT DESCRIPTION AND ADD WRAPPER FOR REVIEWS
remove_action('woocommerce_after_single_product_summary' , 'woocommerce_output_product_data_tabs' , 10);

add_action('woocommerce_after_single_product_summary' , 'entro_clear_floats' , 10);
function entro_clear_floats() {
	echo '<div style="clear:both;"> </div>' ;
}

add_action( 'woocommerce_after_single_product_summary', 'bbloomer_wc_output_long_description', 10 );
function bbloomer_wc_output_long_description() {
?>
   <div class="row">
	   <div class="col-lg-6 ms-auto mb-5 order-lg-2">
		  <h2 class="fw-bold mb-4 bg-light p-4">Description</h2>
			  <?php the_content(); ?>
			  <?php do_action('entro_after_product_description'); ?>
	  </div>
	   <div class="col-lg-6 mb-5 order-lg-1">
		   <div class="h-100 bg-light p-4">
			   <h2 class="fw-bold">Reviews</h2>
		   </div>
	   </div>
   </div>
<?php
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

// MOVE PRODUCT META DATA
remove_action('woocommerce_single_product_summary' , 'woocommerce_template_single_meta' , 40);
add_action('entro_after_product_description' , 'woocommerce_template_single_meta' , 10);


// ADD PRODUCT ORIGIN AFTER TITLE
add_action ('woocommerce_single_product_summary' , 'entro_add_product_origin' , 19);
function entro_add_product_origin() {
	?>
	<div class="product-origin d-flex align-items-center rfs-8 text-muted" style="margin-bottom:2rem;">
		<img src="/wp-content/uploads/2023/06/thailand-icon.png" alt="thailand flag" style="width:35px;margin-right:0.5rem;">
		From Thailand
	</div>
	<?php
}