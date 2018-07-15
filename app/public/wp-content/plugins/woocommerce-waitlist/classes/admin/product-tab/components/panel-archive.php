<?php
/**
 * HTML required for each single archive on the waitlist tab
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="archive wcwl_tab_content" data-panel="archive">
	<div class="wcwl_add_user_wrap">
	</div>
	<?php include 'panel-actions-archive.php'; ?>
	<p class="wcwl_no_users_text">
		<?php _e( 'There are no saved users for this product.', 'woocommerce-waitlist' ); ?>
	</p>
	<table class="widefat wcwl_waitlist_table">
		<tr>
			<th><input name="wcwl_select_all" type="checkbox"/></th>
			<th><?php _e( 'User', 'woocommerce-waitlist' ); ?></th>
			<th><?php _e( 'Mailed', 'woocommerce-waitlist' ); ?></th>
		</tr>
		<?php
		$archives = $this->retrieve_and_sort_archives( $product_id );
		foreach ( $archives as $date => $users ) { ?>
			<?php foreach ( $users as $user_id ) {
				$user = get_user_by( 'id', $user_id );
				include 'panel-table-row.php';
			}
		} ?>
	</table>
</div>
