<?php

class ET_Builder_Module_Related_Product extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Product Related Products' );
		$this->slug            = 'et_pb_related_products';
        $this->fb_support      = true;

        $this->whitelisted_fields = array(
            'module_id',
            'module_class',
        );

        $this->fields_defaults = array(

        );

        $this->options_toggles = array(
			'general'  => array(
				'toggles' => array(

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
        );
        
        return $fields;
    }

    function shortcode_callback( $atts, $content = null, $function_name ) {

        if( !is_product() ) { return ''; }
        global $product;
        
        $module_id               = $this->shortcode_atts['module_id'];
        $module_class            = $this->shortcode_atts['module_class'];
        
        $module_class              = ET_Builder_Element::add_module_order_class( $module_class, $function_name );
        
        ob_start();
        $args = array();
        $defaults = array(
              'posts_per_page' => 6,
              'columns'        => 3,
              'orderby'        => 'rand' 
        );
            
        $args = wp_parse_args( $args, $defaults );

        woocommerce_related_products( $args );
        $content = ob_get_clean();

        $output = sprintf(
            '<div%2$s class="et_pb_module et_pb_tighten_blurb%1$s">
            %3$s
            </div>',
            ( '' !== $module_class ? sprintf( ' %1$s', esc_attr( ltrim( $module_class ) ) ) : '' ),
            ( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
            $content
        );

        return $output;

    }

    public function process_box_shadow( $function_name ) {
		$boxShadow = ET_Builder_Module_Fields_Factory::get( 'BoxShadow' );

		self::set_style( $function_name, $boxShadow->get_style(
			'.' . self::get_module_order_class( $function_name ),
			$this->shortcode_atts
		) );
    }
    
}

new ET_Builder_Module_Related_Product;