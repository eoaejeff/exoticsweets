</main>
	
	<?php get_template_part( 'template-parts/cart', 'drawer' ); ?>
	
	
	<?php if (function_exists("lc_custom_footer")) lc_custom_footer(); else {
		?>
		<?php if (is_active_sidebar( 'footerfull' )): ?>
		<div class="wrapper bg-light mt-5 py-5" id="wrapper-footer-widgets">
			
			<div class="container mb-5">
				
				<div class="row">
					<?php dynamic_sidebar( 'footerfull' ); ?>
				</div>

			</div>
		</div>
		<?php endif ?>
		
		
		<div class="wrapper py-5 bg-white text-dark border-top" id="wrapper-footer-colophon">

			<footer class="site-footer" id="colophon">

				<div class="site-info">

					<div class="container">
						
						<?php //picostrap_site_info(); ?>
						
						<div class="row row-cols-2 row-cols-lg-4 gy-5 py-5">
							
							<div class="col">
								<p class="fw-bolder">Test Heading</p>
								<ul class="list-unstyled m-0 p-0">
									<li>Privacy Policy</li>
									<li>Shipping Policy</li>
									<li>Return Policy</li>
									<li>Terms & Conditions</li>
								</ul>
							</div>
							
							<div class="col">
								<p class="fw-bolder">Test Heading</p>
								<ul class="list-unstyled m-0 p-0">
									<li>Privacy Policy</li>
									<li>Shipping Policy</li>
									<li>Return Policy</li>
									<li>Terms & Conditions</li>
								</ul>
							</div>
							
							<div class="col">
								<p class="fw-bolder">Test Heading</p>
								<ul class="list-unstyled m-0 p-0">
									<li>Privacy Policy</li>
									<li>Shipping Policy</li>
									<li>Return Policy</li>
									<li>Terms & Conditions</li>
								</ul>
							</div>
							
							<div class="col">
								<p class="fw-bolder">Test Heading</p>
								<ul class="list-unstyled m-0 p-0">
									<li>Privacy Policy</li>
									<li>Shipping Policy</li>
									<li>Return Policy</li>
									<li>Terms & Conditions</li>
								</ul>
							</div>
							
						</div>
						
					</div>

				</div><!-- .site-info -->

			</footer><!-- #colophon -->
		
		
		</div><!-- wrapper end -->
		
	<?php 
	} //END ELSE CASE ?>


	<?php wp_footer(); ?>

	</body>
</html>

