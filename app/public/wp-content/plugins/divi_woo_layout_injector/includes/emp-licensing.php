<?php

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}

function sb_et_woo_li_plugin_updater() {

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'sb_et_woo_li_license_key' ) );

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( SB_ET_WOO_LI_STORE_URL, SB_ET_WOO_LI_FILE, array(
			'version' 	=> SB_ET_WOO_LI_VERSION, 				// current version number
			'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
			'item_name' => SB_ET_WOO_LI_ITEM_NAME, 	// name of this plugin
            'item_id'   => SB_ET_WOO_LI_ITEM_ID,
			'author' 	=> SB_ET_WOO_LI_AUTHOR_NAME,  // author of this plugin
			'beta'		=> false
		)
	);

}
add_action( 'admin_init', 'sb_et_woo_li_plugin_updater', 0 );

function sb_et_woo_li_license_page() {
	
	if (isset($_POST['sb_et_woo_li_update_licensing'])) {
		$old = get_option( 'sb_et_woo_li_license_key' );
		$new = $_POST['sb_et_woo_li_license_key'];
		
		update_option('sb_et_woo_li_license_key', $new);
		
		if( $old && $old != $new ) {
			delete_option( 'sb_et_woo_li_license_status' ); // new license has been entered, so must reactivate
		}
		
		sb_et_woo_li_activate_license(true);
		
	}
	
	$license = get_option( 'sb_et_woo_li_license_key' );
	$status  = get_option( 'sb_et_woo_li_license_status' );
	$data  = get_option( 'sb_et_woo_li_license_data' );
	
	echo sb_et_woo_li_box_start('Plugin Licensing');
	
	echo '<p>Before you can start using the plugin, you first need to enter your license and activate it. Do so using the box below. Once you have filled in your valid license key, the plugin will be able to be updated from within the plugins panel without the need to upload it via FTP.</p>';
	
	echo '<table class="widefat">
				<tbody>
					<tr valign="top">
						<th scope="row" valign="top">' . __('License Key') . '</th>
						<td>
							<input id="sb_et_woo_li_license_key" name="sb_et_woo_li_license_key" type="text" class="regular-text" value="' . esc_attr__( $license ) . '" />
						</td>
					</tr>';
					
					if( false !== $license ) {
						echo '<tr valign="top">
							<th scope="row" valign="top">' . __('License Status') . '</th>
							<td>';
							
							if ( $status !== false && $status == 'valid' ) {
									echo '<p><span style="color:green;">' . __('License Active') . '</span></p>';
									echo '<p>Expiry: ' . $data->expires . '</p>';
							} else {
								echo '<span style="color:red;">' . __('License NOT Active') . '</span>';
							}
							
							echo '</td>
						</tr>';
					}
				echo '</tbody>
			</table>
			
			<p><input type="submit" name="sb_et_woo_li_update_licensing" class="button-primary" value="Save License Key" /></p>';
			
	echo sb_et_woo_li_box_end();
	
}

function sb_et_woo_li_has_license() {
	$status  = get_option( 'sb_et_woo_li_license_status', false );
	
	return ($status && $status == 'valid');
}

function sb_et_woo_li_activate_license($bypass=false) {

	if( isset( $_POST['sb_et_woo_li_activate'] ) || $bypass) {

		$license = trim( get_option( 'sb_et_woo_li_license_key' ) );

		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( SB_ET_WOO_LI_ITEM_NAME ), // the name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( SB_ET_WOO_LI_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch( $license_data->error ) {

					case 'expired' :

						$message = sprintf(
							__( 'Your license key expired on %s.' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'revoked' :

						$message = __( 'Your license key has been disabled.' );
						break;

					case 'missing' :

						$message = __( 'Invalid license.' );
						break;

					case 'invalid' :
					case 'site_inactive' :

						$message = __( 'Your license is not active for this URL.' );
						break;

					case 'item_name_mismatch' :

						$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), SB_ET_WOO_LI_ITEM_NAME );
						break;

					case 'no_activations_left':

						$message = __( 'Your license key has reached its activation limit.' );
						break;

					default :

						$message = __( 'An error occurred, please try again.' );
						break;
				}

			}

		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			echo '<p class="updated fade">' . $message . '</p>';
		} else {
			update_option( 'sb_et_woo_li_license_status', $license_data->license );
			update_option( 'sb_et_woo_li_license_data', $license_data );
		}

	}
}