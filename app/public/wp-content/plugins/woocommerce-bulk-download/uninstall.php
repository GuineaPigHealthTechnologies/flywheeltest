<?php

// Stuff to do on the uninstall / deletion of the plugin

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {

    exit();

}

/**
  * Remove settings (options) on uninstall
  */

$wcbd_all_options = array(
	'wcbd_download_zip_button_text',
	'wcbd_select_all_text',
	'wcbd_download_order_downloads_text'
);

foreach ( (array) $wcbd_all_options as $wcbd_option ) {
	delete_option( $wcbd_option );
}

/**
  * Delete WCBP_ZIPS folder
  */

$upload = wp_upload_dir();
$upload_dir = $upload['basedir'] . '/wcbd_zips';

if ( is_dir( $upload_dir ) ) {

	$objects = scandir( $upload_dir );

	foreach ( $objects as $object ) {

		if ( $object != "." && $object != ".." ) {

			if ( filetype( $upload_dir."/".$object ) == "dir" ) {

				rrmdir( $upload_dir."/".$object );

			} else {

				unlink( $upload_dir."/".$object );

			}

		}

	}

	reset( $objects );

	rmdir( $upload_dir );

}
