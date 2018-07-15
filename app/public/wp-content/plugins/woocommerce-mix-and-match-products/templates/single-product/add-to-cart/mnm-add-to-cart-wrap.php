<?php
/**
 * Mix and Match Product Add to Cart button wrapper template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/mnm-add-to-cart-wrap.php.
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
 * @since   1.3.0
 * @version 1.3.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ){
	exit;
}
?>
<div class="mnm_cart cart" <?php echo $product->get_data_attributes(); ?>>

	<?php 

	if ( $product->is_purchasable() ) {

		/**
		 * woocommerce_before_add_to_cart_button hook.
		 */
		do_action( 'woocommerce_before_add_to_cart_button' ); 
		?>

		<div class="mnm_button_wrap" style="display:block">

			<div class="mnm_price"></div>

			<div class="mnm_message"><div class="mnm_message_content woocommerce-info"><?php echo wc_mnm_get_quantity_message( $product ); ?></div></div>
			<?php

			// MnM Availability.
			$availability = $product->get_availability();

			if ( $availability[ 'availability' ] ){
				echo apply_filters( 'woocommerce_stock_html', '<p class="stock ' . $availability[ 'class' ] . '">' . $availability[ 'availability' ] . '</p>', $availability[ 'availability' ] );
			}

	 		if ( ! $product->is_sold_individually() ){
	 			woocommerce_quantity_input( array(
	 				'min_value' => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
	 				'max_value' => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product )
	 			) );
	 		}
	 		?>

	 		<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />

			<button type="submit" class="single_add_to_cart_button mnm_add_to_cart_button button alt"><?php echo $product->single_add_to_cart_text(); ?></button>

		</div>
	

		<?php 
		/**
		 * woocommerce_after_add_to_cart_button hook.
		 */
		do_action( 'woocommerce_after_add_to_cart_button' ); 

	} else {

		echo '<div class="mnm_container_unavailable woocommerce-info">' . __( 'This product is currently unavailable.', 'woocommerce-mix-and-match-products' ) . '</div>';
	} 
?>

</div>
