<?php
$plugin_settings = get_option( 'plugin_settings' );
$error = "false";

$plugin_data = get_plugin_data( __FILE__ );

$versions = array (
	"PHP" => (float)phpversion(),
	"Wordpress" => get_bloginfo('version'),
	"WooCommerce" => WC()->version,
	"WooCommerce Product Feed PRO" => WOOCOMMERCESEA_PLUGIN_VERSION
);

/**
 * Change default footer text, asking to review our plugin
 **/
function my_footer_text($default) {
    return 'If you like our <strong>WooCommerce Product Feed PRO</strong> plugin please leave us a <a href="https://wordpress.org/support/plugin/woo-product-feed-pro/reviews?rate=5#new-post" target="_blank" class="woo-product-feed-pro-ratingRequest">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. Thanks in advance!';
}
add_filter('admin_footer_text', 'my_footer_text');

/**
 * Create notification object and get message and message type as WooCommerce is inactive
 * also set variable allowed on 0 to disable submit button on step 1 of configuration
 */
$notifications_obj = new WooSEA_Get_Admin_Notifications;
if (!in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        $notifications_box = $notifications_obj->get_admin_notifications ( '9', 'false' );
} else {
        $notifications_box = $notifications_obj->get_admin_notifications ( '14', 'false' );
}

if ($versions['PHP'] < 5.6){
        $notifications_box = $notifications_obj->get_admin_notifications ( '11', 'false' );
}

if ($versions['WooCommerce'] < 3){
        $notifications_box = $notifications_obj->get_admin_notifications ( '13', 'false' );
}

if (!wp_next_scheduled( 'woosea_cron_hook' ) ) {
	$notifications_box = $notifications_obj->get_admin_notifications ( '12', 'false' );
}
?>

<div id="dialog" title="Basic dialog">
	<p>
     		<div id="dialogText"></div>
      	</p>
</div>

<div class="wrap">
        <div class="woo-product-feed-pro-form-style-2">
                <tbody class="woo-product-feed-pro-body">
                        <div class="woo-product-feed-pro-form-style-2-heading">Plugin settings</div>
                        <div class="<?php _e($notifications_box['message_type']); ?>">
                                <p><?php _e($notifications_box['message'], 'sample-text-domain' ); ?></p>
                        </div>
	
			<div class="woo-product-feed-pro-table-wrapper">
				<div class="woo-product-feed-pro-table-left">
			       		<table class="woo-product-feed-pro-table">
						<tr>
						</tr>
	
						<form action="" method="post">
						<tr>
							<td>
								<span>Grant access to AdTribes.io support:</span>
								<span class="ui-icon ui-icon-info opener" id="Grant access"></span>
							</td>
							<td>
                                                		<label class="woo-product-feed-pro-switch">
                                                        	<?php
								$woosea_support_user = get_option('woosea_support_user');
							 	if($woosea_support_user == "yes"){
                                                                	print "<input type=\"checkbox\" id=\"grant_access\" name=\"grant_access\" class=\"checkbox-field\" checked>";
                                                        	} else {
                                                                	print "<input type=\"checkbox\" id=\"grant_access\" name=\"grant_access\" class=\"checkbox-field\">";
                                                        	}
                                                        	?>
                                                        	<div class="woo-product-feed-pro-slider round"></div>
                                                		</label>
							</td>
						</tr>

						<tr>
							<td>
								<span>Fix WooCommerce (JSON-LD) structured data bug:</span>
								<span class="ui-icon ui-icon-info opener" id="Structured data fix"></span>
							</td>
							<td>
                                                		<label class="woo-product-feed-pro-switch">
                                                        	<?php
								$structured_data_fix = get_option ('structured_data_fix');
                                                        	if($structured_data_fix == "yes"){
                                                                	print "<input type=\"checkbox\" id=\"fix_json_ld\" name=\"fix_json_ld\" class=\"checkbox-field\" checked>";
                                                        	} else {
                                                                	print "<input type=\"checkbox\" id=\"fix_json_ld\" name=\"fix_json_ld\" class=\"checkbox-field\">";
                                                        	}
                                                        	?>
                                                        	<div class="woo-product-feed-pro-slider round"></div>
                                                		</label>
							</td>
						</tr>

			

						<!--
						<tr>
							<td><span>Batch-size products processing:</span></td>
							<td>
                                                        	<?php
                                                        	if(isset($plugin_settings['batch_size'])){
									print "<input type=\"text\" class=\"input-field-small\" name=\"batch_size\" value=\"$plugin_settings[batch_size]\"> products per batch";
								} else {
									print "<input type=\"text\" class=\"input-field-small\" name=\"batch_size\" value=\"250\"> products per batch";
								}
                                                        	?>
							</td>
						</tr>
						-->
						</form>
					</table>
				</div>

				<div class="woo-product-feed-pro-table-right">
			       		<table class="woo-product-feed-pro-table">
						<tr>
                                                	<td><strong>We’ve got you covered!</strong></td>
                                        	</tr>
	
						<tr>
					               	<td>
                                                        Need assistance? Check out our:
                                                        <ul>
                                                                <li><strong><a href="https://adtribes.io/support/" target="_blank">F.A.Q.</a></strong></li>
                                                                <li><strong><a href="https://www.youtube.com/channel/UCXp1NsK-G_w0XzkfHW-NZCw" target="_blank">YouTube tutorials</a></strong></li>
                                                        </ul>
                                                        Or just reach out to us at  <a href="mailto:support@adtribes.io">support@adtribes.io</a> and we'll make sure your product feeds will be up-and-running within no-time.
                        	                        </td>	
						</tr>
					</table>
				</div>
			</div>
		</tbody>
	</div>
</div>
