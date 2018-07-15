<?php

class dc_addtocart_module extends ET_Builder_Module {
                function init() {
                    $this->name = __( 'Product Add to Cart', 'et_builder' );
					$this->slug = 'et_pb_addtocart';
					$this->fb_support      = true;
					$this->main_css_element = '%%order_class%% .et_pb_product_title h1.product_title.entry-title';
            
                    $this->whitelisted_fields = array(
                        'button_text',
			            'background_layout',
			            'button_alignment',
                        'qty_alignment',
						'qty_display',
			            'admin_label',
			            'module_id',
			            'module_class',
                        'style_addtocart',
						'buttonwidth',
						'addtocart_bg_color',
						'addtocart_border_color',
						'addtocart_text_color',
						'addtocart_hover_bg_color',
						'addtocart_hover_text_color',
						'addtocart_hover_border_color',
						'addtocart_border_radius',
						'font_icon',
                    );
            
                    $this->fields_defaults = array(
                        'background_color'  => array( et_builder_accent_color(), 'add_default_setting' ),
			            'background_layout' => array( 'light' ),
                        'style_addtocart' => array( 'off' ),
                    );
                    $this->main_css_element = '%%order_class%%';
                    
                    $this->advanced_options = array(
                      
                    );
                    $this->custom_css_options = array();
					
                }
            
                function get_fields() {
                    $fields = array(
                            'button_alignment' => array(
				            'label'           => esc_html__( 'Button alignment', 'et_builder' ),
				            'type'            => 'select',
				            'option_category' => 'configuration',
				            'options'         => array(
					            'left'   => esc_html__( 'Left', 'et_builder' ),
					            'right'  => esc_html__( 'Right', 'et_builder' ),
				        ),
				    'description'     => esc_html__( 'Here you can define the alignment of Button', 'et_builder' ),
                    'css' => array(
						    'main' => "{$this->main_css_element}.et_pb_button_alignment_{}",
						    'plugin_main' => "{$this->main_css_element}.et_pb_button_alignment_right .single_add_to_cart_button.button.alt",
                            'important' => 'all',
					),
			        ),
					
                    'text_border_radius' => array(
				'label'           => esc_html__( 'Text Overlay Border Radius', 'et_builder' ),
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
			
                    'addtocart_bg_color' => array(
				            'label'    => esc_html__( 'button Background Colour', 'et_builder' ),
				            'type'     => 'color-alpha',
				            'custom_color'      => true,
				            'tab_slug' => 'advanced',
                    ),
					'addtocart_text_color' => array(
				            'label'    => esc_html__( 'Button Text Colour', 'et_builder' ),
				            'type'     => 'color-alpha',
				            'custom_color'      => true,
				            'tab_slug' => 'advanced',
                    ),
					'addtocart_border_color' => array(
				            'label'    => esc_html__( 'Button Border Colour', 'et_builder' ),
				            'type'     => 'color-alpha',
				            'custom_color'      => true,
				            'tab_slug' => 'advanced',
                    ),
					'font_icon' => array(
				'label'               => esc_html__( 'Icon', 'et_builder' ),
				'type'                => 'text',
				'option_category'     => 'basic_option',
				'class'               => array( 'et-pb-font-icon' ),
				'description'         => esc_html__( 'Choose an icon to display with your blurb.', 'et_builder' ),
				'depends_default'     => true,
			),
					'addtocart_border_radius' => array(
				'label'           => esc_html__( 'Button Border Radius', 'et_builder' ),
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
					'addtocart_hover_bg_color' => array(
				            'label'    => esc_html__( 'Button Hover Background Colour', 'et_builder' ),
				            'type'     => 'color-alpha',
				            'custom_color'      => true,
				            'tab_slug' => 'advanced',
                    ),
					'addtocart_hover_text_color' => array(
				            'label'    => esc_html__( 'Button Hover Text Colour', 'et_builder' ),
				            'type'     => 'color-alpha',
				            'custom_color'      => true,
				            'tab_slug' => 'advanced',
                    ),
					'addtocart_hover_border_color' => array(
				            'label'    => esc_html__( 'Button Hover Border Colour', 'et_builder' ),
				            'type'     => 'color-alpha',
				            'custom_color'      => true,
				            'tab_slug' => 'advanced',
                    ),
					'qty_display' => array(
				            'label'           => esc_html__( 'Display Quantity', 'et_builder' ),
				            'type'            => 'select',
				            'option_category' => 'configuration',
				            'options'         => array(
					            'block'   => esc_html__( 'Show', 'et_builder' ),
					            'none'  => esc_html__( 'Hide', 'et_builder' ),
				        ),
				    'description'     => esc_html__( 'Here you can define the alignment of Button', 'et_builder' ),
			        ),
                    'qty_alignment' => array(
				            'label'           => esc_html__( 'Quantity alignment', 'et_builder' ),
				            'type'            => 'select',
				            'option_category' => 'configuration',
				            'options'         => array(
					            'left'   => esc_html__( 'Left', 'et_builder' ),
					            'right'  => esc_html__( 'Right', 'et_builder' ),
				        ),
				    'description'     => esc_html__( 'Here you can define the alignment of Button', 'et_builder' ),
			        ),
					'buttonwidth' => array(
				'label'             => esc_html__( 'Fullwidth Button', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off'     => esc_html__( 'No', 'et_builder' ),
					'on'      => esc_html__( 'Yes', 'et_builder' ),
				),
				'description'       => esc_html__( 'Here you can choose whether or not the image should have a space below it.', 'et_builder' ),
			),
                    'background_layout' => array(
				            'label'           => esc_html__( 'Text Color', 'et_builder' ),
				            'type'            => 'select',
				            'option_category' => 'color_option',
				            'options'         => array(
					        'light' => esc_html__( 'Dark', 'et_builder' ),
					        'dark'  => esc_html__( 'Light', 'et_builder' ),
				        ),
				    'description'     => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			        ),
                            'admin_label' => array(
                            'label'       => __( 'Admin Label', 'et_builder' ),
                            'type'        => 'text',
                            'description' => __( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
                    ),
                    'module_id' => array(
				            'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				            'type'            => 'text',
				            'option_category' => 'configuration',
				            'tab_slug'        => 'custom_css',
				            'option_class'    => 'et_pb_custom_css_regular',
			        ),
			        'module_class' => array(
				            'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				            'type'            => 'text',
				            'option_category' => 'configuration',
				            'tab_slug'        => 'custom_css',
				            'option_class'    => 'et_pb_custom_css_regular',
			        ),
                    
                );
                    
                    return $fields;
                }
            
                function shortcode_callback( $atts, $content = null, $function_name ) {
                                
					if( !is_product() ) { return ''; }
					global $product;
                                $button_alignment  = $this->shortcode_atts['button_alignment'];
                                $background_layout = $this->shortcode_atts['background_layout'];
                                $module_id          = $this->shortcode_atts['module_id'];
                                $module_class       = $this->shortcode_atts['module_class'];
                                $qty_alignment      = $this->shortcode_atts['qty_alignment'];
								$qty_display 		= $this->shortcode_atts['qty_display'];
								$buttonwidth 		= $this->shortcode_atts['buttonwidth'];
								$addtocart_bg 			= $this->shortcode_atts['addtocart_bg_color'];
								$addtocart_border_color = $this->shortcode_atts['addtocart_border_color'];
								$addtocart_text_color 	= $this->shortcode_atts['addtocart_text_color'];
								$addtocart_hover_bg 	= $this->shortcode_atts['addtocart_hover_bg_color'];
								$addtocart_hover_text_color = $this->shortcode_atts['addtocart_hover_text_color'];
								$addtocart_hover_border_color = $this->shortcode_atts['addtocart_hover_border_color'];
								$addtocart_border_radius = $this->shortcode_atts['addtocart_border_radius'];
                        
                                $module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );
                                
                        if ( '' !== $button_alignment ) {
			                ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% .single_add_to_cart_button.button.alt',
				                'declaration' => sprintf(
					            'float: %1$s%2$s!important;',
					        esc_html( $button_alignment ),
					        et_is_builder_plugin_active() ? ' !important' : ''
				            ),
			                ) );
		                }
						if ( '' !== $addtocart_bg ) {
			                ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% .single_add_to_cart_button.button.alt',
				                'declaration' => sprintf(
					            'background: %1$s%2$s!important;',
					        esc_html( $addtocart_bg ),
					        et_is_builder_plugin_active() ? ' !important' : ''
				            ),
			                ) );
		                }
						if ( '' !== $addtocart_text_color ) {
			                ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% .single_add_to_cart_button.button.alt',
				                'declaration' => sprintf(
					            'color: %1$s%2$s!important;',
					        esc_html( $addtocart_text_color ),
					        et_is_builder_plugin_active() ? ' !important' : ''
				            ),
			                ) );
		                }
						if ( '' !== $addtocart_border_color ) {
			                ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% .single_add_to_cart_button.button.alt',
				                'declaration' => sprintf(
					            'border-color: %1$s%2$s!important;',
					        esc_html( $addtocart_border_color ),
					        et_is_builder_plugin_active() ? ' !important' : ''
				            ),
			                ) );
		                }
						if ( '' !== $addtocart_hover_bg ) {
			                ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% .single_add_to_cart_button.button.alt:hover',
				                'declaration' => sprintf(
					            'background: %1$s%2$s!important;',
					        esc_html( $addtocart_hover_bg ),
					        et_is_builder_plugin_active() ? ' !important' : ''
				            ),
			                ) );
		                }
						if ( '' !== $addtocart_hover_text_color ) {
			                ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% .single_add_to_cart_button.button.alt:hover',
				                'declaration' => sprintf(
					            'color: %1$s%2$s!important;',
					        esc_html( $addtocart_hover_text_color ),
					        et_is_builder_plugin_active() ? ' !important' : ''
				            ),
			                ) );
		                }
						if ( '' !== $addtocart_hover_border_color ) {
			                ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% .single_add_to_cart_button.button.alt:hover',
				                'declaration' => sprintf(
					            'border-color: %1$s%2$s!important;',
					        esc_html( $addtocart_hover_border_color ),
					        et_is_builder_plugin_active() ? ' !important' : ''
				            ),
			                ) );
		                }
                        if ( '' !== $qty_alignment ) {
			                ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% form.cart div.quantity',
				                'declaration' => sprintf(
					            'float: %1$s%2$s!important;',
					        esc_html( $qty_alignment ),
					        et_is_builder_plugin_active() ? ' !important' : ''
				            ),
			                ) );
		                }
						if ( '' !== $qty_display ) {
			                ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% form.cart div.quantity',
				                'declaration' => sprintf(
					            'display: %1$s%2$s!important;',
					        esc_html( $qty_display ),
					        et_is_builder_plugin_active() ? ' !important' : ''
				            ),
			                ) );
		                }
                        if ( 'on' === $buttonwidth ) {
							ET_Builder_Element::set_style( $function_name, array(
								'selector'    => '%%order_class%% .single_add_to_cart_button.button.alt',
								'declaration' => 'width: 100%;',
							) );
						}
						if ( ! in_array( $addtocart_border_radius, array( '', '0' ) ) ) {
							ET_Builder_Element::set_style( $function_name, array(
								'selector'    => '%%order_class%% .single_add_to_cart_button.button.alt',
								'declaration' => sprintf(
								'-moz-border-radius: %1$s; -webkit-border-radius: %1$s; border-radius: %1$s;',
								esc_html( et_builder_process_range_value( $addtocart_border_radius ) )
								),
							) );
						}
                                //////////////////////////////////////////////////////////////////////
                                  
                                 ob_start();
								 woocommerce_template_single_add_to_cart();
                                $content = ob_get_clean();
                                  
                                 //////////////////////////////////////////////////////////////////////
                                $module_class .= " et_pb_module et_pb_bg_layout_{$background_layout}";
                                $module_class .= " et_pb_module et_pb_button_alignment_{$button_alignment}";

                                $output = sprintf(
                                    '<div%5$s class="%1$s%3$s%6$s">
                                        %2$s
                                    %4$s',
                                    'clearfix ',
                                    $content,
                                    esc_attr( 'et_pb_module et_woo_addtocart ' ),
                                    '</div>',
                                    ( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
                                    ( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
                                
                                );
                        
                                return $output;
                }
            }
            new dc_addtocart_module();

?>