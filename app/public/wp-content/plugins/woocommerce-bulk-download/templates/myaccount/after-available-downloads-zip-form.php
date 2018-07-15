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
 * My Account - Zip All Downloads After
 */
?>
<input type="submit" name="submit" value="<?php echo esc_attr( $zip_button_text ) ?>" id="wcbd_zip_button" disabled="disabled" />
<input type="hidden" name="create-zip" value="1" />
<input type="checkbox" id="wcbd_select_all" class="wcbd_checkbox" /><label for="wcbd_select_all"><?php echo esc_html( $select_all_text ) ?></label>
</form>
