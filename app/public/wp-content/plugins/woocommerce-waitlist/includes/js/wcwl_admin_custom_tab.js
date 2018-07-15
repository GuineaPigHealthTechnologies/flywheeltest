// JS required for the waitlist custom tab on the product edit screen
jQuery( document ).ready( function( $ ) {

    // Toggle display of variations
    $( '.wcwl_variation_tab' ).on( 'click', '.wcwl_header_wrap', function() {
        $( this ).next( $( '.wcwl_body_wrap' ) ).slideToggle();
    } );

    // Update content shown on tab transition
    $( 'ul.wcwl_tabs' ).on( 'click', 'li', function() {
        $( '.wcwl_notice' ).remove();
        var panel  = $( this ).closest( '.wcwl_body_wrap' );
        $( panel ).find( 'ul.wcwl_tabs li, .wcwl_tab_content' ).removeClass( 'current' );
        var tab = $( this ).data( 'tab' );
        $( this ).addClass( 'current' );
        $( panel ).find( '.wcwl_tab_content.'+tab ).addClass( 'current' );
    } );

    // Check whether the given panel has any users in its table currently
    var panelHasUsers = function( panel ) {
        var users = $( panel ).find( 'tr.wcwl_user_row' );
        return users.length > 0;
    };

    // Hide unnecessary elements when there are no users in this panels table
    var hidePanelElements = function( panel ) {
        $( panel ).find( '.wcwl_actions' ).hide();
        $( panel ).find( '.wcwl_waitlist_table' ).hide();
        $( panel ).find( '.wcwl_no_users_text ' ).show();
        if ( 'archive' == $( panel ).data( 'panel' ) ) {
            $( panel ).find( '.wcwl_add_user_wrap' ).hide();
        }
    };

    // Show necessary elements when there are users in this panels table
    var showPanelElements = function( panel ) {
        $( panel ).find( '.wcwl_waitlist_table' ).show();
        $( panel ).find( '.wcwl_no_users_text ' ).hide();
        if ( ! $( panel ).find( 'button.wcwl_email_add_user' ).is( ':visible' ) ) {
            $( panel ).find( '.wcwl_actions' ).show();
        }
        if ( 'archive' == $( panel ).data( 'panel' ) ) {
            $( panel ).find( '.wcwl_add_user_wrap ' ).show();
        }
    };

    // Toggle panel elements based on users in the table
    var togglePanelElements = function( panel ) {
        if ( ! panelHasUsers( panel ) ) {
            hidePanelElements( panel );
        } else {
            showPanelElements( panel );
        }
    };

    // On page load, show/hide elements depending on users in the table
    $( '.wcwl_tab_content' ).each( function() {
        togglePanelElements( this );
    });

    // Toggle visible fields field when user clicks 'Add new user' button
    $( '.wcwl_add_user_wrap' ).on( 'click', '.wcwl_add', function() {
        var waitlist_tab = $( this ).closest( '.wcwl_tab_content' );
        $( '.wcwl_notice' ).remove();
        $( this ).hide();
        waitlist_tab.find( '.wcwl_no_users' ).hide();
        waitlist_tab.find( '.wcwl_add_user_content' ).show();
        waitlist_tab.find( '.wcwl_actions' ).hide();
    } );

    // Toggle visible fields when user presses the back button
    $( '.wcwl_add_user_wrap' ).on( 'click', '.wcwl_back', function( e ) {
        $( '.wcwl_notice' ).remove();
        hideEmailField( this );
    } );

    // Hide add email field and show actions if required
    var hideEmailField = function( element ) {
        var waitlist_tab = $( element ).closest( '.wcwl_tab_content' );
        waitlist_tab.find( '.wcwl_add_user_content' ).hide();
        waitlist_tab.find( '.wcwl_add' ).show();
        waitlist_tab.find( '.wcwl_email' ).val( '' );
        if ( panelHasUsers( waitlist_tab ) ) {
            waitlist_tab.find( '.wcwl_actions' ).show();
            waitlist_tab.find( '.wcwl_no_users' ).hide();
        } else {
            waitlist_tab.find( '.wcwl_no_users' ).show();
        }
    };

    // Validate email, add email to waitlist and notify user of success/failure
    $( '.wcwl_add_user_wrap' ).on( 'click', '.wcwl_email_add_user', function() {
        var emails     = $( this ).closest( '.wcwl_tab_content' ).find( 'input.wcwl_email' ).val().split( ',' );
        var product_id = getProductID( this );
        var nonce      = $( this ).data( 'nonce' );
        var button     = $( '.wcwl_email_add_user[data-product-id="' + product_id + '"]' );
        var panel      = $( this ).closest( '.wcwl_tab_content' ).data( 'panel' );
        var error      = false;
        addLoadingElements( button );
        if ( $.isArray( emails ) ) {
            $.each( emails, function( key, email ) {
                email = email.replace(/ /g,'');
                if ( ! validateEmail( email ) ) {
                    addNotice( 'error', wcwl_tab.invalid_email, product_id, panel );
                    error = true;
                    removeLoadingElements( panel, 'add_user', wcwl_tab.add_text );
                    return false;
                }
            });
            if ( ! error ) {
                addUsers( emails, product_id, nonce, panel );
            }
        } else {
            if ( ! validateEmail( emails ) ) {
                addNotice( 'error', wcwl_tab.invalid_email, product_id, panel );
                error = true;
                removeLoadingElements( panel, 'add_user', wcwl_tab.add_text );
                return false;
            }
            addUsers( emails, product_id, nonce, panel );
        }
    } );

    // Toggle select all when top checkbox is clicked
    $( '.wcwl_waitlist_table' ).on( 'click', 'input[name="wcwl_select_all"]', function() {
        var checkboxes = $( this ).closest( 'tbody' ).find( '.wcwl_user_checkbox' );
        var is_checked = $( this ).prop( 'checked' );
        $( checkboxes ).each( function() {
            $( this ).prop( 'checked', is_checked );
        } );
    } );

    // Fire post request to add user to waitlist
    var addUsers = function( emails, product_id, nonce, panel ) {
        var data = {
            action: 'wcwl_add_user_to_waitlist',
            emails: emails,
            product_id: product_id,
            wcwl_add_user_nonce: nonce
        };
        $.post( ajaxurl, data, function( response ) {
            $( '.wcwl_notice' ).remove();
            var response_data = $.parseJSON( response );
            var table = $( '.wcwl_body_wrap[data-product-id="' + product_id +'"] .' + panel + ' .wcwl_waitlist_table' );
            addUsersToTable( response_data.users, product_id, table, panel );
            if( $( '#wcwl_variation_' + product_id ).length ) {
                updateVariationWaitlistCount( product_id );
            }
            addNotice( response_data.type, response_data.message, product_id, panel );
            panel = $( '.wcwl_body_wrap[data-product-id="' + product_id + '"]' ).find( '.' + panel );
            updatePanel( panel, '.wcwl_email_add_user' );
            hideEmailField( panel.find( '.wcwl_waitlist_table' ) );
        } );
    };

    // Add the newly added user to the waitlist table and make sure it is visible
    var appendUserToTable = function( userdata, product_id, table, panel ) {
        var date = userdata['join_date'];
        if ( 'archive' == panel ) {
            date = userdata['date'];
        }
        var new_row = '<tr class="wcwl_user_row" data-user-id="' + userdata[ "id" ] + '">';
        new_row += '<td><input class="wcwl_user_checkbox" type="checkbox" name="wcwl_user_checkbox" value="' + userdata[ "id" ] + '" data-user-email="' + userdata[ "email" ] + '" data-date-added="' + date  + '"/></td>';
        new_row += '<td><strong><a title="' + wcwl_tab.view_profile_text + '" href="' + userdata[ "link" ] + '">' + userdata[ "email" ] + '</a></strong></td>';
        new_row += '<td>' + userdata[ "join_date" ] + '</td></tr>';
        table.append( new_row );
    };

    // Add multiple users to the waitlist table
    var addUsersToTable = function( users, product_id, table, panel ) {
        $.each( users, function( key, user ) {
            if( 0 == table.find( 'input[data-user-email="' + user.email + '"]' ).length && 0 !== user[ "id" ] ) {
                appendUserToTable( user, product_id, table, panel );
                var variation = $( '#wcwl_variation_' + product_id );
                if( variation.length > 0 ) {
                    updateVariationWaitlistCount( product_id );
                    variation.removeClass( 'wcwl_hidden' );
                }
            }
        } );
    };

    // Update the waitlist count for users on variation header tab
    var updateVariationWaitlistCount = function( product_id ) {
        var count_span = $( '#wcwl_variation_' + product_id + ' .wcwl_count' );
        var count = $( '.wcwl_body_wrap[data-product-id="'+product_id+'"] .waitlist tr.wcwl_user_row' ).length;
        count_span.text( parseInt( count ) );
    };

    // Check action request is valid and organise required data for request
    $( '.wcwl_actions' ).on( 'click', 'button.wcwl_action', function() {
        var action = $( this ).prev( 'select.wcwl_action' ).val();
        var panel = $( this ).closest( '.wcwl_tab_content' ).data( 'panel' );
        var product_id = getProductID( this );
        if( action ) {
            var checked_boxes = $( '.wcwl_body_wrap[data-product-id="' + product_id + '"] .' + panel + ' .wcwl_user_checkbox:checkbox:checked' );
            if( 'wcwl_email_custom' == action ) {
                var emails = getSelectedUserEmails( checked_boxes );
                if( emails.length > 0 ) {
                    window.location = 'mailto:' + wcwl_tab.admin_email + '?bcc=' + emails;
                } else {
                    addNotice( 'error', wcwl_tab.no_users_text, product_id, panel );
                }
            } else {
                var users = getUserIDs( checked_boxes );
                var nonce = $( this ).data( 'nonce' );
                addLoadingElements( this );
                processAction( action, product_id, users, nonce, panel );
            }
        } else {
            addNotice( 'error', wcwl_tab.no_action_text, product_id, panel );
        }
    } );

    // Return the email addresses of all the selected used in csv format
    var getSelectedUserEmails = function( checkboxes ) {
        var emails = '';
        $( checkboxes ).each( function() {
            emails += $( this ).data( 'user-email' ) + ',';
        } );
        return emails;
    };

    // Create an array with all checked users
    var getUserIDs = function( checkboxes ) {
        var users = [];
        $( checkboxes ).each( function() {
            var user = {
                date: $( this ).data( 'date-added' ),
                id: $( this ).val()
            };
            users.push( user );
        } );
        return users;
    };

    // Fire the appropriate ajax call for the given action
    var processAction = function( action, product_id, users, nonce, panel ) {
        var data = {
            action: action,
            product_id: product_id,
            users: users,
            wcwl_action_nonce: nonce
        };
        $.post( ajaxurl, data, function( response ) {
            $( '.wcwl_notice' ).remove();
            var response_data = $.parseJSON( response );
            var table = '';
            if( response_data.type == 'success' ) {
                if( 'wcwl_remove_waitlist' == action || 'wcwl_remove_archive' == action ) {
                    table = $( '.wcwl_body_wrap[data-product-id="' + product_id + '"] .' + panel + ' .wcwl_waitlist_table' );
                    removeUsersFromTable( response_data.users, product_id, panel, table );
                    table.find( 'input' ).prop( 'checked', false );
                }
                if( 'wcwl_return_to_waitlist' == action ) {
                    table = $( '.wcwl_body_wrap[data-product-id="' + product_id + '"] .waitlist .wcwl_waitlist_table' );
                    addUsersToTable( response_data.users, product_id, table, 'waitlist' );
                    var waitlist_panel = $( '.wcwl_body_wrap[data-product-id="' + product_id + '"] .waitlist' );
                    updatePanel( waitlist_panel, '.wcwl_action' );
                }
                if ( 'wcwl_email_instock' == action && 'waitlist' == panel ) {
                    table = $( '.wcwl_body_wrap[data-product-id="' + product_id + '"] .archive .wcwl_waitlist_table' );
                    addUsersToTable( response_data.users, product_id, table, 'archive' );
                    archive_panel = $( '.wcwl_body_wrap[data-product-id="' + product_id + '"]' ).find( '.archive' );
                    updatePanel( archive_panel, '.wcwl_action' );
                }
            }
            addNotice( response_data.type, response_data.message, product_id, panel );
            var panel_to_update = $( '.wcwl_body_wrap[data-product-id="' + product_id + '"]' ).find( '.' + panel );
            updatePanel( panel_to_update, '.wcwl_action' );
        } );
    };

    // Remove each user from the table that has been removed from the waitlist
    var removeUsersFromTable = function( users, product_id, panel, table ) {
        $.each( users, function( key, user ) {
            table.find( 'tr[data-user-id="' + user[ 'id' ] + '"]' ).first().remove();
            if( 'waitlist' == panel && $( '#wcwl_variation_' + product_id ).length ) {
                updateVariationWaitlistCount( product_id );
            }
        } );
        if( table.find( 'tr.wcwl_user_row' ).length == 0 ) {
            table.hide();
            table.closest( '.wcwl_tab_content' ).find( '.wcwl_no_users' ).show();
            if( 'archive' == panel ) {
                $( '.wcwl_body_wrap[data-product-id="' + product_id + '"] .' + panel + ' .wcwl_add_user_wrap' ).hide();
            }
        }
    };

    // Add notice to waitlist panel
    var addNotice = function( type, message, product_id, panel ) {
        $( '.wcwl_notice' ).remove();
        var notice = createNotice( type, message );
        if ( 'options' == panel ) {
            $( notice ).insertBefore( $( '.wcwl_body_wrap[data-product-id="' + product_id + '"] .options' ) );
        } else {
            $( notice ).insertAfter( $( '.wcwl_body_wrap[data-product-id="' + product_id + '"] .' + panel + ' .wcwl_actions' ) );
        }
        if ( 'success' == type ) {
            $( '.wcwl_notice' ).delay( 3000 ).slideToggle();
        }
        dismissNotice();
    };

    // Create dismissable notice
    var createNotice = function( type, message ) {
        var notice = '<div class="wcwl_notice wcwl_notice_' + type + '">';
        notice += '<p>' + message + '</p>';
        notice += '<span class="wcwl_dismiss dashicons dashicons-dismiss"></span></div>';
        return notice;
    };

    // Dismiss notices. Archiving notice dismissal updates usermeta to avoid permanent nag
    var dismissNotice = function() {
        $( '.wcwl_notice' ).on( 'click', '.wcwl_dismiss', function() {
            $( this ).closest( '.wcwl_notice' ).remove();
            $( 'ul.waitlist-tabs' ).css( 'margin-top', '0' );
        } );
    };
    dismissNotice();

    // Run a check to catch variation updates to notify user waitlists may not be accurate until page refresh
    if( $( '.wcwl_header_wrap' ).length > 0 ) {
        var variation_updates = 0;
        $( document ).ajaxSuccess( function( event, xhr, options, data ) {
            if( 'string' == typeof data && data.substring( 0, 35 ).includes( 'woocommerce_variation' ) ) {
                $( '.wcwl_notice' ).remove();
                variation_updates ++;
                if( variation_updates > 1 ) {
                    $( '#wcwl_waitlist_data' ).prepend( createNotice( 'info', wcwl_tab.update_waitlist_notice ) );
                    dismissNotice();
                }
            }
        } );
    }

    // Enable minimum stock field when user checks stock option
    $( '.options' ).on( 'change', 'input[name="enable_stock_trigger"]', function() {
        if ( $( this ).is( ':checked' ) ) {
            $( this ).closest( '.options' ).find( 'input[name="minimum_stock"]' ).removeAttr( 'disabled' );
            $( this ).closest( '.options' ).find( 'label[for="minimum_stock"]' ).removeClass( 'wcwl_disabled' );
        } else {
            $( this ).closest( '.options' ).find( 'input[name="minimum_stock"]' ).attr( 'disabled', 'disabled' );
            $( this ).closest( '.options' ).find( 'label[for="minimum_stock"]' ).addClass( 'wcwl_disabled' );
        }
    });

    // Update options for waitlist when user clicks "update" button
    $( '.options' ).on( 'click', 'button', function() {
        addLoadingElements( this );
        var panel              = $( this ).closest( '.wcwl_tab_content' );
        var product_id         = getProductID( this );
        var manage_stock_level = panel.find( 'input[name="enable_stock_trigger"]' ).is( ':checked' );
        var options     = {
            'enable_waitlist'      : panel.find( 'input[name="enable_waitlist"]' ).is( ':checked' ),
            'enable_stock_trigger' : manage_stock_level
        };
        var stock_field = panel.find( 'input[name="minimum_stock"]' );
        if ( manage_stock_level ) {
            options.minimum_stock = stock_field.val();
        } else {
            options.minimum_stock = stock_field.data( 'default-stock' );
        }
        stock_field.val( options.minimum_stock );
        updateOptions( panel, product_id, options );
    } );

    // Update options for the given product waitlist
    var updateOptions = function( panel, product_id, options ) {
        var button = panel.find( 'button' );
        var data = {
            action: 'wcwl_update_waitlist_options',
            product_id: product_id,
            options: options,
            wcwl_update_nonce: button.data( 'nonce' )
        };
        $.post( ajaxurl, data, function( response ) {
            $( '.wcwl_notice' ).remove();
            var response_data = $.parseJSON( response );

            addNotice( response_data.type, response_data.message, product_id, 'options' );
            removeLoadingElements( panel, 'update_options', wcwl_tab.update_button_text );
        } );
    };

    // Validate given email address
    var validateEmail = function (email) {
        var valid = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return valid.test(email);
    };

    // Add spinner and disable buttons to proceed with ajax request
    var addLoadingElements = function ( button ) {
        $( button ).text( '' );
        $( button ).append( '<span class="wcwl_loading spinner is-active"></span>' );
        $( button ).attr( 'disabled', 'disabled' );
    };

    // Remove spinner, replace text and enable button
    var removeLoadingElements = function ( panel, type, text ) {
        var button = '';
        if ( 'add_user' == type ) {
            button = 'button.wcwl_email_add_user';
        } else if ( 'update_options' == type ) {
            button = '.options button';
        } else {
            button = 'button.wcwl_action';
        }
        $( panel ).find( $( button ) ).text( text );
        $( button ).removeAttr( 'disabled' );
        $( button ).find( 'span.wcwl_loading' ).remove();
    };

    // Reset loaders and buttons after action
    var updatePanel = function( panel, button_class ) {
        var button = $( panel ).find( 'button' + button_class );
        var text = '';
        if ( '.wcwl_email_add_user' == button_class ) {
            text = wcwl_tab.add_text;
            type = 'add_user';
        } else {
            text = wcwl_tab.go_text;
            type = 'action';
        }
        removeLoadingElements( panel, type, text );
        $( button ).prev( '.wcwl_email' ).val( '' );
        $( button ).prev( 'select.wcwl_action' ).val( '0' );
        togglePanelElements( panel );
    };

    // Return product ID for the current panel
    var getProductID = function( element ) {
        return $( element ).closest( '.wcwl_body_wrap' ).data( 'product-id' );
    };

    // Prevent post update button being pressed if email/minimum stock fields are focussed
    $( '#wcwl_waitlist_data' ).on( 'keyup keypress', 'input.wcwl_email, input[name="minimum_stock"]', function(e) {
        var keyCode = e.keyCode || e.which;
        if ( keyCode === 13 ) {
            e.preventDefault();
            if ( 'minimum_stock' == $( this ).attr( 'name' ) ) {
                $( this ).closest( '.options' ).find( 'button' ).trigger( 'click' );
            } else {
                $( this ).next( 'button' ).trigger( 'click' );
            }
        }
    });
} );