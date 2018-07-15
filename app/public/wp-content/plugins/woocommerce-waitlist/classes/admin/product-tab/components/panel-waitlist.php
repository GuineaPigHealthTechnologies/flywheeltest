<?php
/**
 * HTML required for each single waitlist on the waitlist tab
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="waitlist wcwl_tab_content current" data-panel="waitlist">
	<?php
	include 'panel-add-new.php';
	include 'panel-actions-waitlist.php';
	?>
	<div class="wcwl_no_users">
		<p class="wcwl_no_users_text">
			<?php esc_html_e( apply_filters( 'wcwl_empty_waitlist_introduction', __( 'There are no users on the waiting list for this product.', 'woocommerce-waitlist' ) ) ); ?>
		</p>
	</div>

	<table class="widefat wcwl_waitlist_table">
		<tr>
			<th><input name="wcwl_select_all" type="checkbox"/></th>
			<th><?php _e( 'User', 'woocommerce-waitlist' ); ?></th>
			<th><?php _e( 'Added', 'woocommerce-waitlist' ); ?></th>
		</tr>
		<?php
		$product  = wc_get_product( $product_id );
		$waitlist = new Pie_WCWL_Waitlist( $product );
		$users    = $waitlist->waitlist;
		foreach ( $users as $user_id => $date ) {
			$user = get_user_by( 'id', $user_id );
			if ( $user ) {
				include 'panel-table-row.php';
			}
		} ?>
	</table>
</div>
