<?php

class ET_Builder_Module_Product_Thumbnail extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Product Thumbnails', 'et_builder' );
		$this->slug            = 'et_pb_product_thumbnails';
        $this->fb_support      = true;
        $this->main_css_element = '%%order_class%% img.attachment-shop_single.size-shop_single';

        $this->whitelisted_fields = array(
            'module_id',
            'module_class',
            'column_layout',
            'column_spacing',
        );

        $this->fields_defaults = array(
            'column_layout' => array( 'column-4' ),
        );

        $this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
                    'layout' => esc_html__( 'Layout', 'et_builder' ),
                ),
			),
			'advanced' => array(
				'toggles' => array(

                ),
			),
			'custom_css' => array(
				'toggles' => array(
                    'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
					'attributes' => array(
						'title'    => esc_html__( 'Attributes', 'et_builder' ),
						'priority' => 95,
                    ),  
                ),
			),
        );
        
        $this->advanced_options = array(

        );
    }

    function get_fields() {
		$fields = array(
            'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
            ),
            'column_layout' => array(
				'label'           => esc_html__( 'Columns', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'column-1' => esc_html__( 'One Column', 'et_builder' ),
					'column-2'  => esc_html__( 'Two Column', 'et_builder' ),
                    'column-3' => esc_html__( 'Three Column', 'et_builder' ),
                    'column-4' => esc_html__( 'Four Column', 'et_builder' ),
				),
				'toggle_slug'     => 'layout',
			),
            'column_spacing' => array(
				'label'           => esc_html__( 'Gutter', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'' => esc_html__( 'On', 'et_builder' ),
					'no-spaceing'  => esc_html__( 'Off', 'et_builder' ),
				),
				'toggle_slug'     => 'layout',
			),
        );
        
        return $fields;
    }

    function shortcode_callback( $atts, $content = null, $function_name ) {
        global $post, $product, $html, $attachment_id;

        if( !is_product() ) { return ''; }
        global $product;
        
        $module_id               = $this->shortcode_atts['module_id'];
        $module_class            = $this->shortcode_atts['module_class'];
        $column_layout           = $this->shortcode_atts['column_layout'];
        $column_spacing          = $this->shortcode_atts['column_spacing'];
        
        $module_class              = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

        add_filter( 'woocommerce_single_product_image_html', 'ds_dc_et_woo_li_link_images', 99, 2 );

        ob_start();
        woocommerce_show_product_thumbnails();
        $content = ob_get_clean();

        $output = sprintf(
            '<div%2$s class="et_pb_module et_pb_thumbnails%4$s%5$s%1$s">	
            %3$s
            </div>',
            ( '' !== $module_class ? sprintf( ' %1$s', esc_attr( ltrim( $module_class ) ) ) : '' ),
            ( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
            $content,
            ( '' !== $column_layout ? sprintf( ' %1$s', $column_layout ) : '' ),
            ( '' !== $column_spacing ? sprintf( ' %1$s', $column_spacing ) : '' )
        );
        
        return $output; 

    }

    public function _add_additional_shadow_fields() {
        return false;
    }
        
    protected function _add_additional_border_fields() {
        return false;
    }
        
    function process_advanced_border_options( $function_name ) {
        return false;
    }
    
}

new ET_Builder_Module_Product_Thumbnail;   