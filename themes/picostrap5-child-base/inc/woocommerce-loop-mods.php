<?php

//Remove woocommerce loop item classes to allow bootstrap classes to work properly

  
/*function filter_woocommerce_post_class( $classes, $product ) {
   
  if ( is_product() ) {
    
    $classes = array_values( array_diff( $classes, array( 'first', 'last') ) );
    return $classes;
    
  } else {
    
    $classes = array_values( array_diff( $classes, array( 'first', 'last', 'product') ) );
    return $classes ; 
    
  }
  
}*/
//add_filter( 'woocommerce_post_class', 'filter_woocommerce_post_class', 10, 2 );


/*function filter_woocommerce_post_class( $classes, $product ) {

  if ( is_product() ) {
    
    $classes = array_values( array_diff( $classes, array( 'first', 'last') ) );
    return $classes;
    
  } else {
    
    $classes = array_values( array_diff( $classes, array( 'first', 'last', 'product') ) );
    return $classes; 
    
  }
  
}*/
//add_filter( 'woocommerce_post_class', 'filter_woocommerce_post_class', 10, 2 );


function fix_related_upsell_classes( $classes, $product ) {
 
  global $woocommerce_loop;
  
  if ( is_product() && $woocommerce_loop['name'] == 'related' ) {
  
  $classes = array_values( array_diff( $classes, array( 'first', 'last', 'product') ) );
  return $classes ;
  
  } elseif ( is_product() && $woocommerce_loop['name'] == 'up-sells' ) {
  
  $classes = array_values( array_diff( $classes, array( 'first', 'last', 'product') ) );
  return $classes; 
  
  } elseif ( is_product() ) {
    
    $classes = array_values( array_diff( $classes, array( 'first', 'last') ) );
    return $classes;
    
  } else {
    
    $classes = array_values( array_diff( $classes, array( 'first', 'last', 'product') ) );
    return $classes; 
    
  }
  
}
add_filter('woocommerce_post_class','fix_related_upsell_classes', 10, 2);



// Change position of price in the WooCommerce Loop
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 8 );




// Add wrapper to product loop image
function es_product_before_woo_image() {
  echo '<div class="product-img-wrap position-relative mb-2">';
}
add_action( 'woocommerce_before_shop_loop_item_title', 'es_product_before_woo_image', 9 );

function es_product_after_woo_image() {
  echo '</div>';
}
add_action( 'woocommerce_before_shop_loop_item_title', 'es_product_after_woo_image', 11 );




// Add wrapper to bottom of product loop card
function es_product_before_price() {
  echo '<div class="card-bottom mt-auto d-flex flex-column align-items-start justify-content-between border-start-0 border-end-0">';
}
add_action( 'woocommerce_after_shop_loop_item', 'es_product_before_price', 6 );

function es_product_after_price() {
  echo '</div>';
}
add_action( 'woocommerce_after_shop_loop_item', 'es_product_after_price', 15 );




// Change the markup for the product loop title
function woocommerce_template_loop_product_title() {
	echo '<p class="product-title fw-bold text-dark text-decoration-none">' . get_the_title() . '</p>';
}




// ADD PRODUCT ORIGIN AFTER TITLE
add_action ('woocommerce_after_shop_loop_item' , 'entro_add_product_origin_loop' , 7);
function entro_add_product_origin_loop() {
  ?>
  <p class="product-origin d-flex align-items-center rfs-6 text-muted mb-3" >
    <img src="/wp-content/uploads/2023/06/thailand-icon.png" alt="thailand flag" style="width:25px;margin-right:0.5rem;">
    From Thailand
  </p>
  <?php
}


// Move Quick View Button
function move_barn2_button() {
  return 14;
}
add_filter('wc_quick_view_pro_shop_loop_button_hook_priority','move_barn2_button' );

// Remove Default Quick View Button Classes
function remove_barn2_button_classes() {
  return false;
}
add_filter('wc_quick_view_pro_use_default_button_classes','remove_barn2_button_classes' );