/**
 * JS required for the waitlist settings screen
 *
 * Adding a custom update count button and hooking up the functionality for it
 */
jQuery( document ).ready( function( $ ) {
    // Create extra buttons
    var update_counts_button   = '<tr valign="top" class=""><th scope="row" class="titledesc">' + wcwl_settings.update_counts_desc + '</th><td class="forminp forminp-checkbox"><fieldset><legend class="screen-reader-text"><span>' + wcwl_settings.update_counts_desc + '</span></legend><button name="woocommerce_waitlist_update_counts" id="woocommerce_waitlist_update_counts" type="button" class="button">' + wcwl_settings.update_counts_button_text + '</button><span class="wcwl_settings_update_counts_text">' + wcwl_settings.update_warning + '</span></fieldset></td></tr>';
    var update_meta_button     = '<tr valign="top" class=""><th scope="row" class="titledesc">' + wcwl_settings.update_meta_desc + '</th><td class="forminp forminp-checkbox"><fieldset><legend class="screen-reader-text"><span>' + wcwl_settings.update_meta_desc + '</span></legend><button name="woocommerce_waitlist_update_meta" id="woocommerce_waitlist_update_meta" type="button" class="button">' + wcwl_settings.update_meta_button_text + '</button><span class="wcwl_settings_update_meta_text">' + wcwl_settings.update_warning + '</span></fieldset></td></tr>';
    // Insert buttons into appropriate table
    var settings_table = $( 'h2:contains("Waitlist Update Options")' ).next( 'table' );
    $( update_meta_button ).prependTo( settings_table );
    $( update_counts_button ).prependTo( settings_table );
    $( '.form-table' ).show();
    $( '#woocommerce_waitlist_update_counts' ).click( updateWaitlistCounts );
    $( '#woocommerce_waitlist_update_meta' ).click( updateWaitlistMeta );

    var type = '';
    function updateWaitlistCounts() {
        if ( window.confirm( wcwl_settings.confirmation_message ) ) {
            type = 'count';
            var data = {
                action: 'wcwl_get_products',
                wcwl_get_products: wcwl_settings.get_products_nonce
            };
            $.post( ajaxurl, data, function( products ) {
                products = $.parseJSON( products );
                if( products.length > 0 ) {
                    outputUpdatingCounts( 1, products.length );
                    doCountUpdates( products );
                } else {
                    alert( wcwl_settings.no_products_message );
                }
            } );
        }
    }
    function doCountUpdates( products ) {
        var products_total = products.length;
        var current_total = 0;

        // Run ajax request
        function runRequest() {
            // Check to make sure there are more products to update
            if( products.length > 0 ) {

                var current = products.splice( 0, 10 );

                // Make the AJAX request with the given products
                var data = {
                    action: 'wcwl_update_counts',
                    products: current,
                    wcwl_update_counts: wcwl_settings.update_counts_nonce
                };
                $.post( ajaxurl, data, function( response ) {
                    console.log( response );
                    current_total = parseInt( current.length ) + parseInt( current_total );
                    if( current_total == products_total ) {
                        outputSuccess( products_total );
                    } else {
                        updateMessageCounts( current_total );
                    }
                } ).done( function() {
                    runRequest();
                });
            }
        }
        runRequest();
    }
    function updateWaitlistMeta() {
        if ( window.confirm( wcwl_settings.confirmation_message ) ) {
            type = 'meta';
            var data = {
                action: 'wcwl_get_products',
                wcwl_get_products: wcwl_settings.get_products_nonce
            };
            $.post( ajaxurl, data, function( products ) {
                products = $.parseJSON( products );
                if( products.length > 0 ) {
                    outputUpdatingCounts( 1, products.length );
                    doMetaUpdates( products );
                } else {
                    alert( 'No products found.' );
                }
            } );
        }
    }
    function doMetaUpdates( products ) {
        var products_total = products.length;
        var current_total = 0;

        // Run ajax request
        function runRequest() {
            // Check to make sure there are more products to update
            if( products.length > 0 ) {

                var current = products.splice( 0, 10 );

                // Make the AJAX request with the given products
                var data = {
                    action: 'wcwl_update_meta',
                    products: current,
                    wcwl_update_meta: wcwl_settings.update_meta_nonce
                };
                $.post( ajaxurl, data, function( response ) {
                    console.log( response );
                    current_total = parseInt( current.length ) + parseInt( current_total );
                    if( current_total == products_total ) {
                        outputSuccess( products_total );
                    } else {
                        updateMessageCounts( current_total );
                    }
                } ).done( function() {
                    runRequest();
                } );
            }
        }
        runRequest();
    }
    // Add notice and loader to let user know we're running updates and disable buttons
    function outputUpdatingCounts( current, total ) {
        $( '#woocommerce_waitlist_update_counts, #woocommerce_waitlist_update_meta, .woocommerce-save-button' ).prop( 'disabled', true );
        var message = '';
        if ( 'count' === type ) {
            message = wcwl_settings.update_counts_message;
        }
        if ( 'meta' === type ) {
            message = wcwl_settings.update_meta_message;
        }
        var html = '<div id="message" class="updated inline"><p><strong>' + message + '</strong></p></div>';
        $( html ).insertBefore( settings_table );
        $( '.wcwl_current_update' ).text( current );
        $( '.wcwl_total_updates' ).text( total );
    }
    // Update notice to reflect current product updates
    function updateMessageCounts( current ) {
        $( '.wcwl_current_update' ).text( current );
    }
    // Update notice, remove loader and re-enable buttons on complete
    function outputSuccess( total ) {
        var message = '';
        if ( 'count' === type ) {
            message = wcwl_settings.update_counts_message_complete
        }
        if ( 'meta' === type ) {
            message = wcwl_settings.update_meta_message_complete;
        }
        $( '#message p strong' ).html( message );
        $( '.wcwl_total_updates' ).text( total );
        $( '#woocommerce_waitlist_update_counts, #woocommerce_waitlist_update_meta, .woocommerce-save-button' ).prop( 'disabled', false );
    }
    // Only show required options based on selections
    var options = [ '#woocommerce_waitlist_notify_admin' ];
    $( options ).each( function( key, option ) {
        maybeShowOption( option );
        $( '.form-table' ).on( 'change', option, function() {
            maybeShowOption( option );
        });
    });
    function maybeShowOption( option ) {
        if( $( option ).is( ':checked' ) ) {
            $( option ).closest( 'tr' ).next( 'tr' ).show();
        } else {
            $( option ).closest( 'tr' ).next( 'tr' ).hide();
        }
    }
} );
