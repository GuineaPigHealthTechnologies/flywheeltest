// JS required for the front end
jQuery( document ).ready( function( $ ){

    //Support for WooCommerce Quick View (check quick view elements exist and we are on the shop page)
    if ( $( '.post-type-archive, .archive.tax-product_cat' ).length ) {
            load_waitlist();
    } else if ( $( '.single-product' ).length ) {
        $( window ).on( 'load', function() {
            load_waitlist();
        });
    }

    function load_waitlist() {
        /* SUBSCRIPTIONS */
        // Hack to add the email input for simple subscriptions to the page.
        // WCS has the filter we require wrapped with wp_kses which removes out input field
        if( $( 'div.product-type-subscription' ).length && ! $( '.wcwl_email' ).length ) {
            $( 'div.wcwl_email_field' ).append( '<input type="email" name="wcwl_email" class="wcwl_email" />' );
        }

        /* GROUPED */
        if( $( '.product-type-grouped' ).length || $( '.woocommerce_waitlist_label' ).length ) {
            //Grab href of join waitlist button
            var href = $( "a.woocommerce_waitlist" ).attr( "href" );
            var product_id = $( '.wcwl_control a' ).data( 'id' );
            // Modify the buttons href attribute to include the updated array of checkboxes and user email
            $( '#wcwl-product-' + product_id ).on( 'click', function( e ) {
                $( "a.woocommerce_waitlist" ).prop( "href", href + "&wcwl_leave=" + leave_array + "&wcwl_join=" + join_array );
            } );
            // Create two arrays, showing products user wishes to join/leave the waitlist for
            var join_array = $( "input:checkbox:checked.wcwl_checkbox" ).map( function() {
                return $( this ).attr( "id" ).replace( 'wcwl_checked_', '' );
            } ).get();
            if( ! $( '.wcwl_email' ).length > 0 ) {
                var leave_array = $( "input:checkbox:not(:checked).wcwl_checkbox" ).map( function() {
                    return $( this ).attr( "id" ).replace( 'wcwl_checked_', '' );
                } ).get();
            } else {
                var leave_array = [];
            }
            // When a checkbox is clicked, add/remove the product ID to/from the appropriate arrays
            $( ".wcwl_checkbox" ).change( function() {
                // If user is logged in create a join and leave array otherwise we only want a join array
                var changed = $( this ).attr( "id" ).replace( 'wcwl_checked_', '' );
                if( this.checked ) {
                    if( ! $( '.wcwl_email' ).length > 0 ) {
                        leave_array.splice( $.inArray( changed, leave_array ), 1 );
                    }
                    join_array.push( changed );
                }
                if( ! this.checked ) {
                    join_array.splice( $.inArray( changed, join_array ), 1 );
                    if( ! $( '.wcwl_email' ).length > 0 ) {
                        leave_array.push( changed );
                    }
                }
            } );
        }

        /* VARIABLE */
        $( "form.variations_form" ).change( function() {
            update_waitlist_link_with_email();
        } );

        /* GENERIC */
        // Load on page load
        update_waitlist_link_with_email();

        /* SHOP PAGE */
        // Show waitlist elements when user clicks join waitlist button
        $( 'li.product' ).on( 'click', '.wcwl_toggle_email', function( e ) {
            e.preventDefault();
            $( '.wcwl_frontend_wrap' ).hide();
            $( '.wcwl_toggle_email' ).show();
            $( 'input[name="wcwl_email"], .wcwl_optin' ).removeClass( 'wcwl_error_highlight' );
            $( this ).hide();
            if ( $( '.logged-in' ).length === 0 ) {
                $( this ).prev( '.wcwl_frontend_wrap' ).prepend( $( '.wcwl_email_elements' ) );
                $( '.wcwl_email_elements' ).show();
            }
            $( this ).prev( '.wcwl_frontend_wrap' ).show();
            $( this ).prev( '.wcwl_frontend_wrap' ).find( 'input[name="wcwl_email"]' ).focus();
        });
        // Enable return to trigger waitlist buttons
        $( '.wcwl_email_elements' ).on( 'keyup keypress', 'input.wcwl_email', function(e) {
            var keyCode = e.keyCode || e.which;
            if ( keyCode === 13 ) {
                $( this ).closest( '.wcwl_frontend_wrap' ).find( '.woocommerce_waitlist.confirm' ).trigger( 'click' );
            }
        });


        /* FUNCTIONS */
        // When email input is changed update the buttons href attribute to include the email
        function update_waitlist_link_with_email() {
            $( '.wcwl_control' ).on( 'click', 'a.woocommerce_waitlist', function( event ) {
                event.preventDefault();
                var errors = [];
                var email = $( this ).closest( '.wcwl_frontend_wrap' ).find( 'input[name="wcwl_email"]' );
                if( email.length > 0 && ( ! email.val() || ! validate_email( email.val() ) )  ) {
                    email.addClass( 'wcwl_error_highlight' );
                    errors.push( 'email' );
                } else {
                    email.removeClass( 'wcwl_error_highlight' );
                }
                var checkbox = $( this ).closest( '.wcwl_frontend_wrap' ).find( 'input[name="wcwl_optin"]' );
                if ( checkbox.length > 0 && ! checkbox.is( ':checked' ) ) {
                    checkbox.closest( '.wcwl_optin' ).addClass( 'wcwl_error_highlight' );
                    errors.push( 'optin' );
                } else {
                    checkbox.closest( '.wcwl_optin' ).removeClass( 'wcwl_error_highlight' );
                }
                if ( errors.length == 0 ) {
                    window.location = $( this ).attr( 'href' ) + '&wcwl_email=' + email.val();
                } else {
                    if ( 'email' == errors[0] ) {
                        email.focus();
                    } else {
                        checkbox.focus();
                    }
                }
            });
        };
        // Validate given email address
        function validate_email( email ) {
            var valid = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return valid.test( email );
        };
    }
});
