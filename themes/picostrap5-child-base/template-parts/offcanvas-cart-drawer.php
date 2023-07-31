<div class="offcanvas offcanvas-end" tabindex="-1" id="cartDrawer" aria-labelledby="cartDrawerLabel">
	<div class="offcanvas-header bg-gradient-secondary text-white">
		<h5 id="cartDrawerLabel" class="fw-light">Your Cart</h5>
		<button type="button" class="btn-close text-reset btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
	</div>
	<div class="offcanvas-body woocommerce">
		<div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
	</div>
</div>

<!-- OPEN CART DRAWER WHEN PRODUCT ADDED TO CART -->
<script type="text/javascript">
	(function($){
		$('body').on( 'added_to_cart', function(){
			// Testing output on browser JS console
			console.log('added_to_cart'); 
			
			var myOffcanvas = document.getElementById('cartDrawer')
			var bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas)
			bsOffcanvas.show()
			
		});
	})(jQuery);
</script>