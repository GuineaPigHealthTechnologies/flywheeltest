<?php
/**
 * WooCommerce Product Reviews Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Product Reviews Pro for your
 * needs please refer to http://docs.woothemes.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @package   WC-Product-Reviews-Pro/Functions
 * @author    SkyVerge
 * @category  Functions
 * @copyright Copyright (c) 2015-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Returns correct count of reviews for products.
 *
 * @see \wp_count_comments() wrapper
 *
 * @since 1.0.0
 *
 * @param string $type one of all|review|question|photo|video|contribution_comment
 * @param string $status optional status for comments to be counted
 * @param array $opt_args optional arguments passed to retrieve comments to be counted
 * @return int count
 */
function wc_count_reviews( $type = 'all', $status = '', $opt_args = array() ) {

	$args = wp_parse_args( $opt_args, array(
		'type'      => $type,
		'status'    => $status,
		'post_type' => 'product',
		'count'     => true
	) );

	$prp_comment_types = array( 'review', 'contribution_comment', 'photo', 'video', 'question' );

	if ( 'all' === $type ) {
		$args = array_merge( $args, array(
			'type__in' => $prp_comment_types,
		) );
	}

	$GLOBALS['wc_counting_reviews'] = true;

	// count reviews introduced by Product Reviews Pro
	$count_prp_reviews = get_comments( $args );

	/* @see WC_Contribution::get_moderation() */
	if ( is_numeric( $status ) ) {
		$status = '0' === (string) $status ? 'hold' : 'approve';
	}

	// count standard WooCommerce reviews
	$count_wc_reviews = get_comments( wp_parse_args( $opt_args, array(
		'type'         => 'all',
		'type__not_in' => $prp_comment_types,
		'post_type'    => 'product',
		'status'       => $status,
		'count'        => true,
	) ) );

	$GLOBALS['wc_counting_reviews'] = false;

	return $count_prp_reviews + $count_wc_reviews;
}
