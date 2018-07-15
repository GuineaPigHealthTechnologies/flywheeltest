<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! defined( 'WCWL_VERSION' ) ) {
	define( 'WCWL_VERSION', '1.8.1' );
}
if ( ! defined( 'WCWL_SLUG' ) ) {
	define( 'WCWL_SLUG', 'woocommerce_waitlist' );
}
if ( ! defined( 'WCWL_ENQUEUE_PATH' ) ) {
	define( 'WCWL_ENQUEUE_PATH', untrailingslashit( plugins_url( '/', __FILE__ ) ) );
}
if ( ! defined( 'WCWL_AUTO_WAITLIST_CREATION' ) ) {
	define( 'WCWL_AUTO_WAITLIST_CREATION', true );
}
