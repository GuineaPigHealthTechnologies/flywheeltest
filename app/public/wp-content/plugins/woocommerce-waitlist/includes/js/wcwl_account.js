// JS required for the user account waitlist tab frontend
jQuery( document ).ready( function( $ ) {

    // Remove user from chosen waitlist
    $( '.waitlist-single-product' ).on( 'click', '.wcwl_remove_product', function( e ) {
        e.preventDefault();
        var url = $( this ).data( 'url' );
        var data = {
            action: 'wcwl_user_remove_self_waitlist',
            product_id: $( this ).data( 'product-id' ),
            user_id: wcwl_account.user_id,
            wcwl_remove_user_nonce: $( this ).data( 'nonce' )
        };
        $.post( wcwl_account.ajaxurl, data, function( response ) {
            window.location = url;
        });
    });
    // Delete user from all archives
    $( '.waitlist-user-waitlist-archive-wrapper' ).on( 'click', '#wcwl_remove_archives', function( e ) {
        e.preventDefault();
        var url = $( this ).data( 'url' );
        var data = {
            action: 'wcwl_user_remove_self_archives',
            user_id: wcwl_account.user_id,
            wcwl_remove_user_archive_nonce: $( this ).data( 'nonce' )
        };
        $.post( wcwl_account.ajaxurl, data, function( response ) {
            window.location = url;
        });
    });
} );