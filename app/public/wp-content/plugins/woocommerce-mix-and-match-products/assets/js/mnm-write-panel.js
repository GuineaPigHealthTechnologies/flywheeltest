/* global wc_mnm_admin_params */

jQuery( function($){

	// Hide the "Grouping" field.
	$( '#linked_product_data .grouping.show_if_simple, #linked_product_data .form-field.show_if_grouped' ).addClass( 'hide_if_mix-and-match' );

	// Simple type options are valid for mnm.
	$( '.show_if_simple:not(.hide_if_mix-and-match)' ).addClass( 'show_if_mix-and-match' );

	// Mix and Match type specific options
	$( 'body' ).on( 'woocommerce-product-type-change', function( event, select_val, select ) {

		if ( select_val === 'mix-and-match' ) {

			$( '.show_if_external' ).hide();
			$( '.show_if_mix-and-match' ).show();

			$( 'input#_manage_stock' ).change();

		}

	} );

	$( 'select#product-type' ).change();

} );
