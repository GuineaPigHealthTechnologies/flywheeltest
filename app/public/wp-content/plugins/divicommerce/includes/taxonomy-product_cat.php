<?php/*
get_header( 'shop' );
$terms = rwmb_meta( 'category_layout_picker' );
$content = '';
if ( !empty( $terms ) ) {
    $content .= '<ul>';
    foreach ( $terms as $term ) {
        $content .= sprintf(
            '<li><a title="%s" href="%s">%s</a></li>',
            esc_attr( $term->name ),
            esc_url( get_term_link( $term, 'tax_slug' ) ),
            esc_html( $term->name )
        );
    }
}
echo $content;
print_r($term);

$value = get_post_meta( get_the_ID(), 'category_layout_picker', true ); // Last param should be 'false' if field is multiple
print_r( $value );
?>
<?php get_footer( 'shop' ); ?>*/