<?php
/**
 * Mix and Match Product Add to Cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/mnm.php.
 *
 * HOWEVER, on occasion WooCommerce Mix and Match will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  Kathy Darling
 * @package WooCommerce Mix and Match/Templates
 * @since   1.0.0
 * @version 1.3.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ){
	exit;
}
 
global $product;

/**
 * woocommerce_before_add_to_cart_form hook.
 */
do_action( 'woocommerce_before_add_to_cart_form' ); 
?>

<form method="post" enctype="multipart/form-data" class="mnm_form cart cart_group <?php echo esc_attr( $classes ); ?>"">

<?php

	/**
	 * 'woocommerce_before_mnm_items' action.
	 *
	 * @param WC_Mix_and_Match $product
	 * @since  1.3.0
	 *
	 * @hooked wc_mnm_template_child_items_wrapper_open - 0
	 */
	do_action( 'woocommerce_before_mnm_items', $product );

	foreach ( $product->get_available_children() as $mnm_item ) {

		/**
		 * 'woocommerce_mnm_item_details' action.
		 *
		 * @param WC_Product $mnm_item
	 	 * @since  1.3.0
		 *
		 * @hooked wc_mnm_template_child_item_details_wrapper_open  -   0
		 * @hooked wc_mnm_template_child_item_thumbnail             -   5
		 * @hooked wc_mnm_template_child_item_details_open          -  10
		 * @hooked wc_mnm_template_child_item_title                 -  15
		 * @hooked wc_mnm_template_child_item_description           -  20
		 * @hooked wc_mnm_template_child_item_product_details       -  25
		 * @hooked wc_mnm_template_child_item_details_close         -  30
		 * @hooked wc_mnm_template_child_item_details_wrapper_close - 100
		 *
		 *
		 * @hooked wc_mnm_template_child_item_details_wrapper_open 	-   0
		 * @hooked wc_mnm_template_child_item_thumbnail_open 		-  10
		 * @hooked wc_mnm_template_child_item_thumbnail 			-  20
		 * @hooked wc_mnm_template_child_item_section_close 		-  30
		 * @hooked wc_mnm_template_child_item_details_open 			-  40
		 * @hooked wc_mnm_template_child_item_title 				-  50
		 * @hooked wc_mnm_template_child_item_attributes			-  60
		 * @hooked wc_mnm_template_child_item_section_close 		-  70
		 * @hooked wc_mnm_template_child_item_quantity_open 		-  80
		 * @hooked wc_mnm_template_child_item_quantity 				-  90
		 * @hooked wc_mnm_template_child_item_section_close			- 100
		 * @hooked wc_mnm_template_child_item_details_wrapper_close	- 110
		 */
		do_action( 'woocommerce_mnm_child_item_details', $mnm_item, $product );
	}

	/**
	 * 'woocommerce_after_mnm_items' action.
	 *
	 * @param  WC_Mix_and_Match  $product
	 * @since  1.3.0
	 *
	 * @hooked wc_mnm_template_child_items_wrapper_close 		- 100
	 */
	do_action( 'woocommerce_after_mnm_items', $product );

	/**
	 * 'woocommerce_mnm_add_to_cart_wrap' action.
	 *
	 * @param  WC_Mix_and_Match  $product
	 * @since  1.3.0
	 *
	 * @hooked wc_mnm_template_reset_link 		- 10
	 * @hooked wc_mnm_template_add_to_cart_wrap - 20
	 */
	do_action( 'woocommerce_mnm_add_to_cart_wrap', $product );

?>

</form>

<?php 
/**
 * woocommerce_after_add_to_cart_form hook.
 */
do_action( 'woocommerce_after_add_to_cart_form' ); 
?>