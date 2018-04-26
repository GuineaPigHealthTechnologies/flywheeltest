<?php

/**
 * Backwards compat.
 *
 * @since 2.4.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active_plugins = get_option( 'active_plugins', array() );
foreach ( $active_plugins as $key => $active_plugin ) {
	if ( strstr( $active_plugin, '/gateway-gocardless.php' ) ) {
		$active_plugins[ $key ] = str_replace( '/gateway-gocardless.php', '/woocommerce-gateway-gocardless.php', $active_plugin );
	}
}
update_option( 'active_plugins', $active_plugins );
