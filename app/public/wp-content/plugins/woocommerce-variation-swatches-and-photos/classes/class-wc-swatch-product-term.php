<?php

class WC_Product_Swatch_Term extends WC_Swatch_Term {

	public $attribute_options;

	public function __construct( $config, $option, $taxonomy, $selected = false ) {
		global $_wp_additional_image_sizes;

		$this->attribute_options = $attribute_options = $config->get_options();

		$this->taxonomy_slug = $taxonomy;
		if ( taxonomy_exists( $taxonomy ) ) {
			$this->term       = get_term( $option, $taxonomy );
			$this->term_label = $this->term->name;
			$this->term_slug  = $this->term->slug;
			$this->term_name  = $this->term->name;
		} else {
			$this->term       = false;
			$this->term_label = $option;
			$this->term_slug  = $option;
		}

		$this->selected = $selected;

		$this->size = $attribute_options['size'];


		$the_size = isset( $_wp_additional_image_sizes[ $this->size ] ) ? $_wp_additional_image_sizes[ $this->size ] : $_wp_additional_image_sizes['swatches_image_size'];
		if ( isset( $the_size['width'] ) && ! empty( $the_size['width'] ) && isset( $the_size['height'] ) && ! empty( $the_size['height'] ) ) {
			$this->width        = $the_size['width'];
			$this->height       = $the_size['height'];

		} else {

			$image_size = get_option( 'swatches_image_size', array(
				'width'  => 32,
				'height' => 32
			) );


			$size['width']  = isset( $image_size['width'] ) && ! empty( $image_size['width'] ) ? $image_size['width'] : 32;
			$size['height'] = isset( $image_size['height'] ) && ! empty( $image_size['height'] ) ? $image_size['height'] : 32;


			$image_size = apply_filters( 'woocommerce_get_image_size_swatches_image_size', $size );


			$this->width  = apply_filters( 'woocommerce_swatches_size_width_default', $image_size['width'] );
			$this->height = apply_filters( 'woocommerce_swatches_size_height_default', $image_size['height'] );
		}


		$key     = md5( sanitize_title( $this->term_slug ) );
		$old_key = sanitize_title( $this->term_slug );

		$lookup_key = '';
		if ( isset( $attribute_options['attributes'][ $key ] ) ) {
			$lookup_key = $key;
		} elseif ( isset( $attribute_options['attributes'][ $old_key ] ) ) {
			$lookup_key = $old_key;
		}

		$this->type = $attribute_options['attributes'][ $lookup_key ]['type'];

		if ( isset( $attribute_options['attributes'][ $lookup_key ]['image'] ) && $attribute_options['attributes'][ $lookup_key ]['image'] ) {
			$this->thumbnail_id = $attribute_options['attributes'][ $lookup_key ]['image'];

			$attachment_image_src = wp_get_attachment_image_src( $this->thumbnail_id, $this->size );
			if ( $attachment_image_src ) {
				$this->thumbnail_src = current( $attachment_image_src );
			}

			$this->thumbnail_alt = trim( strip_tags( get_post_meta( $this->thumbnail_id, '_wp_attachment_image_alt', true ) ) );
		} else {
			$this->thumbnail_src = apply_filters( 'woocommerce_placeholder_img_src', WC()->plugin_url() . '/assets/images/placeholder.png' );
		}

		$this->color = isset( $attribute_options['attributes'][ $lookup_key ]['color'] ) ? $attribute_options['attributes'][ $lookup_key ]['color'] : '#FFFFFF;';
	}
}