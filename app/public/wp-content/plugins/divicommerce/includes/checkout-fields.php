<?php
/*Remove checkout feilds*/
add_filter( 'woocommerce_checkout_fields' , 'ds_dc_override_fields' );
function ds_dc_override_fields( $fields ) {
if( false != get_theme_mod( 'dc_remove_first_name' ) ) {
unset($fields['billing']['billing_first_name']);
}
if( false != get_theme_mod( 'dc_remove_last_name' ) ) {
unset($fields['billing']['billing_last_name']);
}
if( false != get_theme_mod( 'dc_remove_company' ) ) {
unset($fields['billing']['billing_company']);
}
if( false != get_theme_mod( 'dc_remove_address_1' ) ) {
unset($fields['billing']['billing_address_1']);
}
if( false != get_theme_mod( 'dc_remove_address_2' ) ) {
unset($fields['billing']['billing_address_2']);
}
if( false != get_theme_mod( 'dc_remove_city' ) ) {
unset($fields['billing']['billing_city']);
}
if( false != get_theme_mod( 'dc_remove_post_code' ) ) {
unset($fields['billing']['billing_postcode']);
}
if( false != get_theme_mod( 'dc_remove_country' ) ) {
unset($fields['billing']['billing_country']);
}
if( false != get_theme_mod( 'dc_remove_state' ) ) {
unset($fields['billing']['billing_state']);
}
if( false != get_theme_mod( 'dc_remove_phone' ) ) {
unset($fields['billing']['billing_phone']);
}
if( false != get_theme_mod( 'dc_remove_order_comments' ) ) {
unset($fields['order']['order_comments']);
}
if( false != get_theme_mod( 'dc_remove_email' ) ) {
unset($fields['billing']['billing_email']);
}
return $fields;
}