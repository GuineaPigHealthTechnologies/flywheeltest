<?php
/**
 * WooCommerce Nested Category Layout
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Nested Category Layout to newer
 * versions in the future. If you wish to customize WooCommerce Nested Category Layout for your
 * needs please refer to http://www.skyverge.com/product/woocommerce-nested-category-layout/ for more information.
 *
 * @package   WC-Nested-Category-Layout/Templates
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * The Template for displaying nested category products.
 *
 * Override this template by copying it to yourtheme/woocommerce/loop/nested-category.php
 *
 * @global array $woocommerce_product_category_ids An array of product id to containing category IDs.
 * @global \WP_Term $category Current category object.
 *
 * @since 1.0
 * @version 1.11.1
 */
global $woocommerce_loop;

$product_category_level   = isset( $category->depth ) ? $category->depth + 2 : 1;
$num_columns              = ! empty( $woocommerce_loop['columns'] ) ? $woocommerce_loop['columns'] : get_option( 'woocommerce_catalog_columns', 4 );
$see_more                 = false;
$woocommerce_loop['loop'] = 0;

/**
 * Filters the CSS classes applied to the <ul> products wrapper element.
 *
 * @since 1.11.1
 *
 * @param array $classes
 */
$classes = apply_filters( 'wc_nested_category_layout_loop_products_wrapper_classes', array( 'product-category-level-' . $product_category_level, 'columns-' . $num_columns ) );

?>
<ul class="subcategory-products products <?php echo implode( ' ', $classes ); ?>">
	<?php

	if ( ! is_object( $category ) ) {
		$term_id = 0;
	} else {
		$term_id = $category->term_id;
	}

	// loop through all products
	if ( have_posts() ) : while ( have_posts() ) : the_post();
		global $product;

		if ( ! $product ) {
			continue;
		}

		$product_category_ids = $woocommerce_product_category_ids[ SV_WC_Product_Compatibility::get_prop( $product, 'id' ) ];

		// ensure that the product is visible, and belongs to this category
		if ( ! $product->is_visible() || ! in_array( $term_id, $product_category_ids, false ) ) continue;

		// "view more" link
		if ( $woocommerce_loop['loop'] > get_option( 'woocommerce_subcat_posts_per_page' ) - 1 && isset( $woocommerce_loop['see_more'] ) && $woocommerce_loop['see_more'] ) {
			$see_more = true;
			break;
		}

		// display the product thumbnail content
		wc_get_template_part( 'content', 'product' );

	endwhile; endif;

	?>

</ul>

<div style="clear:both;">
	<?php if ( $see_more ) : ?>
		<a class="woocommerce-nested-category-layout-see-more" href="<?php echo esc_attr( get_term_link( $category ) ) ?>">
			<?php echo esc_html( apply_filters( 'wc_nested_category_layout_see_more_message', __( 'See more', 'woocommerce-nested-category-layout' ), $category ) ); ?>
		</a>
	<?php endif; ?>
</div>
