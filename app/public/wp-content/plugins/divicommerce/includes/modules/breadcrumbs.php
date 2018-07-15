<?php

class ET_Builder_Module_Product_Breadcrumbs extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Product Breadcrumbs', 'et_builder' );
		$this->slug            = 'et_pb_product_breadcrumbs';
        $this->fb_support      = true;

        $this->whitelisted_fields = array(
            'module_id',
			'module_class',
			'background_color',
			'content_orientation',
        );

        $this->fields_defaults = array(
			'content_orientation' => array( 'center' ),
        );

        $this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'background' => esc_html__( 'Background', 'et_builder' ),
					'alignment' => esc_html__( 'Alignment', 'et_builder' ),
                ),
			),
			'advanced' => array(
				'toggles' => array(
					'text' => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 45,
						'tabbed_subtoggles' => true,
						'bb_icons_support' => true,
						'sub_toggles' => array(
							'p' => array( 'name' => 'P', 'icon' => 'text-left'),
							'a' => array( 'name' => 'A', 'icon' => 'text-link'),
						),
					),
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
			'fonts' => array(
				'text'   => array(
					'label'    => esc_html__( 'Text', 'et_builder' ),
					'css'      => array(
						'line_height' => "{$this->main_css_element} .woocommerce-breadcrumb",
						'color' => "{$this->main_css_element} .woocommerce-breadcrumb",
					),
					'line_height' => array(
						'default' => '1.7em',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'toggle_slug' => 'text',
					'sub_toggle'  => 'p',
					'hide_text_align' => true,
				),
				'link'   => array(
					'label'    => esc_html__( 'Link', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .woocommerce-breadcrumb a",
						'color' => "{$this->main_css_element} .woocommerce-breadcrumb a",
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'toggle_slug' => 'text',
					'sub_toggle'  => 'a',
				),
			),
			'background' => array(
			),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
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
			'content_orientation' => array(
				'label'           => esc_html__( 'Text Vertical Alignment', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'Left' => esc_html__( 'Left', 'et_builder' ),
					'center'  => esc_html__( 'Center', 'et_builder' ),
					'Right' => esc_html__( 'Right', 'et_builder' ),
				),
				'toggle_slug'     => 'alignment',
				'description'     => esc_html__( 'This setting determines the vertical alignment of your content. Your content can either be vertically centered, or aligned to the bottom.', 'et_builder' ),
			),
			'background_color' => array(
				'label'             => esc_html__( 'Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'toggle_slug'       => 'background',
				'description'       => esc_html__( 'Here you can define a custom background color for your CTA.', 'et_builder' ),
			),
        );
        
        return $fields;
    }

    function shortcode_callback( $atts, $content = null, $function_name ) {

		if (get_post_type() != 'product') {
			return;
}

        $module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$content_orientation          = $this->shortcode_atts['content_orientation'];
        
		$module_class              = ET_Builder_Element::add_module_order_class( $module_class, $function_name );
		
		if ( '' !== $content_orientation ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_module.product_breadcrumbs',
				'declaration' => sprintf(
					'text-align: %1$s;',
					esc_html( $content_orientation )
				),
			) ); 
		}
		ob_start();
		woocommerce_breadcrumb();
		$content = ob_get_clean();

        $output = sprintf(
            '<div%2$s class="et_pb_module product_breadcrumbs%1$s">	
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

new ET_Builder_Module_Product_Breadcrumbs;