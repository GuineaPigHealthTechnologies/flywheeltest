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
 * My Account - Zip All Downloads Checkbox
 */
?>
<?php if ( $show_cb ) : ?>
<input type="checkbox" name="dwn-<?php echo esc_attr( $download['download_id'] ); ?>" <?php echo esc_attr( $disabled ); ?> value="yes" class="wcbd_checkbox" />
<?php endif; ?>

<?php if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.6.0', '>=' ) ) : ?>
	<a href="<?php echo esc_url( $download['download_url'] ); ?>"><?php echo esc_html( $download['download_name'] ); ?></a>
<?php endif; ?>
