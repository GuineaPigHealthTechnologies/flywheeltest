<?php
/**
 * HTML required for the waitlist panel on the product edit screen for simple products
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$product_id = $this->product->get_id(); ?>

<div id="wcwl_waitlist_data" class="panel woocommerce_options_panel">
		<div class="wcwl_body_wrap" data-product-id="<?php echo $product_id ?>">
			<?php
			include 'panel-tabs.php';
			include 'panel-waitlist.php';
			include 'panel-archive.php';
			include 'panel-options.php';
			?>
		</div>
</div>
