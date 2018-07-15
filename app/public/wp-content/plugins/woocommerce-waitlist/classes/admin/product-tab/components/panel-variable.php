<?php
/**
 * HTML required for the waitlist panel on the product edit screen for variable products
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} ?>

<div id="wcwl_waitlist_data" class="panel woocommerce_options_panel">
	<?php
	$children = $this->product->get_children();
	foreach ( $children as $product_id ) { ?>
		<div class="wcwl_variation_tab" id="wcwl_variation_<?php echo $product_id; ?>">
			<div class="wcwl_header_wrap">
				<h3>
					<?php echo $this->return_variation_tab_title( $product_id ); ?>
				</h3>
			</div>
			<div class="wcwl_body_wrap" data-product-id="<?php echo $product_id ?>">
				<?php
				include 'panel-tabs.php';
				include 'panel-waitlist.php';
				include 'panel-archive.php';
				include 'panel-options.php';
				?>
			</div>
		</div>
	<?php } ?>
</div>
