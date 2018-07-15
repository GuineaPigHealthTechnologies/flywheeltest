<?php

class dc_reviews_module extends ET_Builder_Module {
                function init() {
                    $this->name = __( 'Product Reviews', 'et_builder' );
					$this->slug = 'et_pb_reviews';
					$this->fb_support      = true;
            
                    $this->whitelisted_fields = array(
                        'module_id',
                        'module_class',
                        'stars_color',
                        'buttonbg',
                        'buttoncolour',
                        'buttonbordercolour',
                        'buttonborderwidth',
                        'buttonbghover',
                        'buttoncolourhover',
                        'buttonbordercolourhover',
                        'buttonborderwidthhover',
                    );
            
                    $this->fields_defaults = array();
                    $this->main_css_element = '%%order_class%%';
                    $this->advanced_options = array(
                                        'fonts' => array(
                                                'author'   => array(
                                                                'label'    => esc_html__( 'Author', 'et_builder' ),
                                                                'css'      => array(
                                                                        'main' => "{$this->main_css_element} .woocommerce-review__author",
                                                                ),
                                                                'font_size' => array('default' => '14px'),
                                                                'line_height'    => array('default' => '1.5em'),
                                                ),
                                                'date'     => array(
                                                        'label'     => esc_html__('Date', 'et_builder'),
                                                        'css'       => array(
                                                            'main'  => "{$this->main_css_element} .woocommerce-review__published-date, {$this->main_css_element} .woocommerce-review__dash",
                                                        ),
                                                        'font_size' => array('default' => '14px'),
                                                        'line_height'    => array('default' => '1.5em'),
                                                ),
                                                'comment'     => array(
                                                        'label'     => esc_html__('Comment', 'et_builder'),
                                                        'css'       => array(
                                                            'main'  => "{$this->main_css_element} .description p",
                                                        ),
                                                        'font_size' => array('default' => '14px'),
                                                        'line_height'    => array('default' => '1.5em'),
                                                ),
                                                'headings'   => array(
                                                                'label'    => esc_html__( 'Headings', 'et_builder' ),
                                                                'css'      => array(
                                                                        'main' => "{$this->main_css_element} h1, {$this->main_css_element} h2, {$this->main_css_element} h3, {$this->main_css_element} h4",
                                                                ),
                                                                'font_size' => array('default' => '30px'),
                                                                'line_height'    => array('default' => '1.5em'),
                                                ),
                                        ),
                                        
                                );
                    $this->custom_css_options = array();
                }
            
                function get_fields() {
                    $fields = array(
                                'admin_label' => array(
                                    'label'       => __( 'Admin Label', 'et_builder' ),
                                    'type'        => 'text',
                                    'description' => __( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
                                ),
                                'stars_color' => array(
				            'label'    => esc_html__( 'Stars Background Colour', 'et_builder' ),
				            'type'     => 'color-alpha',
				            'custom_color'      => true,
				            'tab_slug' => 'advanced',
                    ),
                    'buttonbg' => array(
				            'label'    => esc_html__( 'button Background Colour', 'et_builder' ),
				            'type'     => 'color-alpha',
				            'custom_color'      => true,
				            'tab_slug' => 'advanced',
                    ),
                    'buttoncolour' => array(
				            'label'    => esc_html__( 'Button Colour', 'et_builder' ),
				            'type'     => 'color-alpha',
				            'custom_color'      => true,
				            'tab_slug' => 'advanced',
                    ),
                    'buttonbordercolour' => array(
				            'label'    => esc_html__( 'Button Border Colour', 'et_builder' ),
				            'type'     => 'color-alpha',
				            'custom_color'      => true,
				            'tab_slug' => 'advanced',
                    ),
                    'buttonborderwidth' => array(
				'label'           => esc_html__( 'Button Border width', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'default'         => '3',
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'tab_slug'        => 'advanced',
                'font_size' => array(
						'default' => '16px',
					),
			),
                    'buttonbghover' => array(
				            'label'    => esc_html__( 'Button Hover Background Colour', 'et_builder' ),
				            'type'     => 'color-alpha',
				            'custom_color'      => true,
				            'tab_slug' => 'advanced',
                    ),
                    'buttoncolourhover' => array(
				            'label'    => esc_html__( 'Button Colour Hover', 'et_builder' ),
				            'type'     => 'color-alpha',
				            'custom_color'      => true,
				            'tab_slug' => 'advanced',
                    ),
                    'buttonbordercolourhover' => array(
				            'label'    => esc_html__( 'Button Border Hover Colour', 'et_builder' ),
				            'type'     => 'color-alpha',
				            'custom_color'      => true,
				            'tab_slug' => 'advanced',
                    ),
                    'buttonborderwidthhover' => array(
				'label'           => esc_html__( 'Button Border width Hover', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'default'         => '3',
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'tab_slug'        => 'advanced',
                'font_size' => array(
						'default' => '16px',
					),
			),
                                'module_id' => array(
                                    'label'           => __( 'CSS ID', 'et_builder' ),
                                    'type'            => 'text',
                                    'option_category' => 'configuration',
                                    'description'     => __( 'Enter an optional CSS ID to be used for this module. An ID can be used to create custom CSS styling, or to create links to particular sections of your page.', 'et_builder' ),
                                ),
                                'module_class' => array(
                                    'label'           => __( 'CSS Class', 'et_builder' ),
                                    'type'            => 'text',
                                    'option_category' => 'configuration',
                                    'description'     => __( 'Enter optional CSS classes to be used for this module. A CSS class can be used to create custom CSS styling. You can add multiple classes, separated with a space.', 'et_builder' ),
                                ),
                    );
                    
                    return $fields;
                }
            
                function shortcode_callback( $atts, $content = null, $function_name ) {
                                
                                if (get_post_type() != 'product') {
                                                return;
                                }
                                
                                $module_id          = $this->shortcode_atts['module_id'];
                                $module_class       = $this->shortcode_atts['module_class'];
                                $stars_colour       = $this->shortcode_atts['stars_color'];
                                $buttonbg           = $this->shortcode_atts['buttonbg'];
                                $buttoncolour       = $this->shortcode_atts['buttoncolour'];
                                $buttonbordercolour = $this->shortcode_atts['buttonbordercolour'];
                                $buttonborderwidth  = $this->shortcode_atts['buttonborderwidth'];
                                $buttonbghover      = $this->shortcode_atts['buttonbghover'];
                                $buttoncolourhover  = $this->shortcode_atts['buttoncolourhover'];
                                $buttonbordercolourhover = $this->shortcode_atts['buttonbordercolourhover'];
                                $buttonborderwidthhover  = $this->shortcode_atts['buttonborderwidthhover'];


                        
                                $module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );
                                
                                if ( '' !== $stars_colour ) {
			                ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% .star-rating span::before',
				                'declaration' => sprintf(
					            'color: %1$s%2$s!important;',
					        esc_html( $stars_colour ),
					        et_is_builder_plugin_active() ? ' !important' : ''
				            ),
			                ) );
		                }

                        if ( '' !== $stars_colour ) {
			                ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% .comment-form-rating .stars span a::before',
				                'declaration' => sprintf(
					            'color: %1$s%2$s!important;',
					        esc_html( $stars_colour ),
					        et_is_builder_plugin_active() ? ' !important' : ''
				            ),
			                ) );
		                }
                        if ( '' !== $buttonbg ) {
			                ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% #review_form_wrapper #submit',
				                'declaration' => sprintf(
					            'background: %1$s%2$s!important;',
					        esc_html( $buttonbg ),
					        et_is_builder_plugin_active() ? ' !important' : ''
				            ),
			                ) );
		                }
                        if ( '' !== $buttoncolour ) {
			                ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% #review_form_wrapper #submit',
				                'declaration' => sprintf(
					            'color: %1$s%2$s!important;',
					        esc_html( $buttoncolour ),
					        et_is_builder_plugin_active() ? ' !important' : ''
				            ),
			                ) );
		                }
                        if ( '' !== $buttonbordercolour ) {
			                ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% #review_form_wrapper #submit',
				                'declaration' => sprintf(
					            'border-color: %1$s%2$s!important;',
					        esc_html( $buttonbordercolour ),
					        et_is_builder_plugin_active() ? ' !important' : ''
				            ),
			                ) );
		                }
                        if ( '' !== $buttonborderwidth ) {
			                ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% #review_form_wrapper #submit',
				                'declaration' => sprintf(
					            'border-width: %1$s%2$s!important;',
					        esc_html( $buttonborderwidth ),
					        et_is_builder_plugin_active() ? ' !important' : ''
				            ),
			                ) );
		                }
                        if ( '' !== $buttonbghover ) {
			                ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% #review_form_wrapper #submit:hover',
				                'declaration' => sprintf(
					            'background: %1$s%2$s!important;',
					        esc_html( $buttonbghover ),
					        et_is_builder_plugin_active() ? ' !important' : ''
				            ),
			                ) );
		                }
                        if ( '' !== $buttoncolourhover ) {
			                ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% #review_form_wrapper #submit:hover',
				                'declaration' => sprintf(
					            'color: %1$s%2$s!important;',
					        esc_html( $buttoncolourhover ),
					        et_is_builder_plugin_active() ? ' !important' : ''
				            ),
			                ) );
		                }
                        if ( '' !== $buttonbordercolourhover ) {
			                ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% #review_form_wrapper #submit:hover',
				                'declaration' => sprintf(
					            'border-color: %1$s%2$s!important;',
					        esc_html( $buttonbordercolourhover ),
					        et_is_builder_plugin_active() ? ' !important' : ''
				            ),
			                ) );
		                }
                        if ( '' !== $buttonborderwidthhover ) {
			                ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% #review_form_wrapper #submit:hover',
				                'declaration' => sprintf(
					            'border-width: %1$s%2$s!important;',
					        esc_html( $buttonborderwidthhover ),
					        et_is_builder_plugin_active() ? ' !important' : ''
				            ),
			                ) );
		                }
                                //////////////////////////////////////////////////////////////////////
                      
                                ob_start();
                                comments_template();
                                $content = ob_get_clean();
                                  
                                 //////////////////////////////////////////////////////////////////////
                        
                                $output = sprintf(
                                    '<div%5$s class="%1$s%3$s%6$s">
                                        %2$s
                                    %4$s',
                                    'clearfix ',
                                    $content,
                                    esc_attr( 'et_pb_module' ),
                                    '</div>',
                                    ( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
                                    ( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
                                );
                        
                                return $output;
                }
            }
        
            new dc_reviews_module();

?>