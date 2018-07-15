<?php
if( true != get_theme_mod( 'dc_remove_dc_builder' ) ) {
// Adding the Divi Builder
function dc_et_builder_post_types( $post_types ) {
    $post_types[] = 'product';
    $post_types[] = 'product-layout';

    return $post_types;
}
add_filter( 'et_builder_post_types', 'dc_et_builder_post_types' );
}