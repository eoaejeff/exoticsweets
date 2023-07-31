<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;


?><!doctype html>
<html <?php language_attributes(); ?>>
	<head>
		<!-- Required meta tags -->
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<!-- wp_head begin -->
		<?php wp_head(); ?>
		<!-- wp_head end -->
	</head>
    
	<body <?php body_class(); ?> >
	<?php wp_body_open(); ?>  
	
	<?php if(function_exists('lc_custom_header')) lc_custom_header(); else {
	  
	  	//STANDARD NAV
	  
		if (get_theme_mod("enable_topbar") ) : ?>
		<!-- ******************* The Topbar Area ******************* -->
		<div id="wrapper-topbar" class="py-2 <?php echo get_theme_mod('topbar_bg_color_choice','bg-light') ?> <?php echo get_theme_mod('topbar_text_color_choice','text-dark') ?>">
			<div class="container">
				<div class="row">
					<div id="topbar-content" class="col-12 text-center text-white"> <?php echo do_shortcode(get_theme_mod('topbar_content')) ?>	</div>
				</div>
			</div>
		</div>
		<?php endif; ?>


		<!-- ******************* The Navbar Area ******************* -->
		<header id="wrapper-navbar" class="sticky-top" itemscope itemtype="http://schema.org/WebSite">

		  <a class="skip-link visually-hidden-focusable" href="#theme-main"><?php esc_html_e( 'Skip to content', 'picostrap5' ); ?></a>

		  
			<nav class="bg-white text-dark pt-1 primary-nav" aria-label="Main Navigation" >
				<div class="container overflow-hidden">
					<div class="row">
						<div class="col-12 d-flex flex-row align-items-center justify-content-between">
							
							<div id="logo-tagline-wrap">
							
								<?php the_custom_logo(); ?>
							
							</div> <!-- /logo-tagline-wrap -->
							
							<div class="main-nav d-none d-lg-block" id="navbarNavDropdown">	
								<?php 
								wp_nav_menu(array(
								'theme_location' => 'primary',
								'container' => false,
								'menu_class' => '',
								'fallback_cb' => '__return_false',
								'items_wrap' => '<ul id="%1$s" class="navbar-nav d-flex flex-row mb-2 mb-md-0 %2$s">%3$s</ul>',
								'walker' => new bootstrap_5_wp_nav_menu_walker()
								));
								?>
							</div>
							
							<div class="nav-utilities">
								<div class="nav-search">
									<a href="" class="d-flex align-items-center justify-content-center text-decoration-none text-uppercase fw-bold text-secondary" data-bs-toggle="modal" data-bs-target="#searchModal">
										<svg xmlns="http://www.w3.org/2000/svg" width="24.001" height="24.001" viewBox="0 0 24.001 24.001">
							  			<path id="Path_1" data-name="Path 1" d="M23.836.98,18.145-4.711a.556.556,0,0,0-.4-.164h-.619A9.724,9.724,0,0,0,19.5-11.25,9.748,9.748,0,0,0,9.75-21,9.748,9.748,0,0,0,0-11.25,9.748,9.748,0,0,0,9.75-1.5a9.724,9.724,0,0,0,6.375-2.372v.619a.576.576,0,0,0,.164.4L21.98,2.836a.563.563,0,0,0,.8,0l1.059-1.059A.563.563,0,0,0,23.836.98ZM9.75-3.75a7.5,7.5,0,0,1-7.5-7.5,7.5,7.5,0,0,1,7.5-7.5,7.5,7.5,0,0,1,7.5,7.5A7.5,7.5,0,0,1,9.75-3.75Z" transform="translate(0 21)"/>
										</svg>
										<!--Search-->
									</a>	
								</div>
								<div class="nav-cart" >
									<a class="d-flex align-items-center justify-content-center text-decoration-none text-uppercase fw-bold text-secondary" data-bs-toggle="offcanvas" href="#cartDrawer" role="button" aria-controls="cartDrawer">
									<svg xmlns="http://www.w3.org/2000/svg" width="27" height="24" viewBox="0 0 27 24" class="me-2">
							  		<path id="Path_2" data-name="Path 2" d="M25.875-18H6.763l-.409-2.091A1.125,1.125,0,0,0,5.25-21H.562A.563.563,0,0,0,0-20.437v1.125a.563.563,0,0,0,.562.562H4.324L7.586-2.077A2.989,2.989,0,0,0,6.75,0a3,3,0,0,0,3,3,3,3,0,0,0,3-3V0a2.983,2.983,0,0,0-.4-1.5h6.8a2.97,2.97,0,0,0-.4,1.493s0,.005,0,.007a3,3,0,0,0,3,3,3,3,0,0,0,3-3,2.99,2.99,0,0,0-.922-2.163l.049-.223a1.125,1.125,0,0,0-1.1-1.364H9.551L9.111-6H23.755a1.125,1.125,0,0,0,1.1-.886l2.12-9.75A1.125,1.125,0,0,0,25.875-18ZM9.75,1.125A1.126,1.126,0,0,1,8.625,0a1.125,1.125,0,0,1,2.25,0A1.126,1.126,0,0,1,9.75,1.125Zm12,0A1.126,1.126,0,0,1,20.625,0a1.125,1.125,0,0,1,2.25,0A1.126,1.126,0,0,1,21.75,1.125Zm1.1-9.375H8.671L7.2-15.75H24.479Z" transform="translate(0 21)"/>
									</svg>
									<!--Cart-->
									</a>
									<?php
									
									?>
								</div>
								<div class="menu-toggle d-block d-lg-none">
									<a href="#mobileMenuDrawer" data-bs-toggle="offcanvas" role="button" aria-controls="mobileMenuDrawer" class="menu-toggle-button">
										<span></span>
										<span></span>
										<span></span>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div> <!-- .container -->
			</nav> <!-- .site-navigation -->
			

		</header><!-- #wrapper-navbar end -->
		
		<?php get_template_part( 'template-parts/search', 'drawer' ); ?>
		
	  
	<?php 
	} // END ELSE CASE 
	?>

<main id='theme-main'>