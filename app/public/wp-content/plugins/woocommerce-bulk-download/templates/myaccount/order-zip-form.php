<?php
/**
 * WooCommerce Bulk Downloads
 *
 * @package     WC-Bulk-Downloads/Templates
 * @author      WooThemes
 * @copyright   Copyright (c) 2015, WooThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * My Account - View Order - Order Zip Form
 */
?>
<form method="POST" action="" class="wcbd-zip-form wcbd-order-zip-form">
<input type="hidden" name="wcbd-download-data" value="<?php echo esc_attr( $order_id ); ?>" />
<input type="submit" name="submit" value="<?php echo esc_attr( $zip_button_text ); ?>" id="wcbd_zip_button" />
<input type="hidden" name="create-order-zip" value="1" />
</form>
