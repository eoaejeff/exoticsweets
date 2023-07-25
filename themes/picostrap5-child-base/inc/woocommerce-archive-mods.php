<?php

//ADD CUSTOM WRAPPER TO WOOCOMMERCE ARCHIVE HEADER

add_action( 'woocommerce_before_main_content', 'picostrap_archive_header_wrap_start', 10 );
add_action( 'woocommerce_before_shop_loop', 'picostrap_archive_header_wrap_end', 1 );

function picostrap_archive_header_wrap_start() {

	if ( ! is_product() ){
		echo '<section class="py-5" id="woo-archive-header">';
		echo '<div class="container">';
		echo '<div class="row">';
		echo '<div class="col col-md-10 mx-4 mx-md-auto p-5 mt-5 bg-white shadow border-top border-secondary border-5 position-relative" style="transform: rotate(-1.5deg);">';
	} else {

	}

}

function picostrap_archive_header_wrap_end() {
	if ( ! is_product() ){
  		echo '</section> ';
  		echo '</div> ';
  		echo '</div> ';
  		echo '</div> ';
	} else {

	}
}



//remove_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10 );