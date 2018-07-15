<?php

// Load autoloader.
require_once( dirname( __FILE__ ) . '/../vendor/autoload.php' );

// Bootstrap WP Mock.
// WP_Mock::setUsePatchwork( true ); // FIXME - do we need this?
WP_Mock::bootstrap();
