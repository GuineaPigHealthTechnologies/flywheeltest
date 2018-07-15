<?php
class dc_cart_module extends ET_Builder_Module {
                function init() {
                    $this->name = __( 'Cart', 'et_builder' );
                    $this->slug = 'et_pb_cart';

                    $this->whitelisted_fields = array(
                        'title',
                        'module_id',
                        'module_class',
                    );
                    $this->fields_defaults = array();
					$this->options_toggles = array(
                        'general' => array(
                            'toggles' => array(
                                'display' => esc_html__('Display', 'et_builder'),
                            ),
                        ),
                        'advanced' => array(
                            'toggles' => array(
                                'notices' => esc_html__('Notices', 'et_builder'),
                                'background' => esc_html__('Background', 'et_builder'),
                                'coupons' => esc_html__('Coupons', 'et_builder'),
                                'carttotal' => esc_html__( 'Cart Total', 'et_builder' ),
                            ),
                        ),
                        'custom_css' => array(
                            'toggles' => array(

                            ),
                        ),
                    );

                    $this->main_css_element = '%%order_class%%';
                    $this->advanced_options = array(
                        'fonts' => array(
                            'headings'   => array(
                                'label' => esc_html__( 'Headings', 'et_builder' ),
                                'css'   => array(
                                    'main' => "{$this->main_css_element} *",
                                ),
                            ),
                            'tableheadings'   => array(
                                'label' => esc_html__( 'Table Headings', 'et_builder' ),
                                'css'   => array(
                                    'main' => "{$this->main_css_element} *",
                                ),
                            ),
                            'rowodd'   => array(
                                'label' => esc_html__( 'Row Odd', 'et_builder' ),
                                'css'   => array(
                                    'main' => "{$this->main_css_element} *",
                                ),
                            ),
                            'rowoddlink'   => array(
                                'label' => esc_html__( 'Row Odd Link', 'et_builder' ),
                                'css'   => array(
                                    'main' => "{$this->main_css_element} *",
                                ),
                            ),
                            'roweven'   => array(
                                'label' => esc_html__( 'Row Even', 'et_builder' ),
                                'css'   => array(
                                    'main' => "{$this->main_css_element} *",
                                ),
                            ),
                            'rowevenlink'   => array(
                                'label' => esc_html__( 'Row Even Link', 'et_builder' ),
                                'css'   => array(
                                    'main' => "{$this->main_css_element} *",
                                ),
                            ),
                            'inputfont'   => array(
                                'label' => esc_html__( 'Input', 'et_builder' ),
                                'css'   => array(
                                    'main' => "{$this->main_css_element} *",
                                ),
                            ),

                        ),
                    );
                    $this->custom_css_options = array(

                    );
                }
                function get_fields() {
                    $fields = array(
                        'module_id' => array(
				            'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				            'type'            => 'text',
				            'option_category' => 'configuration',
				            'tab_slug'        => 'custom_css',
				            'option_class'    => 'et_pb_custom_css_regular',
							'toggle_slug'        => 'classes',
			            ),
			            'module_class' => array(
				            'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				            'type'            => 'text',
				            'option_category' => 'configuration',
				            'tab_slug'        => 'custom_css',
				            'option_class'    => 'et_pb_custom_css_regular',
							'toggle_slug'        => 'classes',
                        ),
                        'main_background' => array(
                            'label'             => esc_html__( 'Main Background', 'et_builder' ),
                            'type'              => 'color-alpha',
                            'description'       => esc_html__( 'Main container backgrounds.', 'et_builder' ),
                            'tab_slug'          => 'advanced',
                            'toggle_slug'       => 'background',
                        ),
                        'table_headfoot_background' => array(
                            'label'             => esc_html__( 'Table Header & Footer', 'et_builder' ),
                            'type'              => 'color-alpha',
                            'description'       => esc_html__( 'Here you can define a custom color for your icon.', 'et_builder' ),
                            'tab_slug'          => 'advanced',
                            'toggle_slug'       => 'background',
                        ),
                        'row_odd_background' => array(
                            'label'             => esc_html__( 'Row Odd Background', 'et_builder' ),
                            'type'              => 'color-alpha',
                            'description'       => esc_html__( 'Here you can define a custom color for your icon.', 'et_builder' ),
                            'tab_slug'          => 'advanced',
                            'toggle_slug'       => 'background',
                        ),
                        'row_even_background' => array(
                            'label'             => esc_html__( 'Row Even Background', 'et_builder' ),
                            'type'              => 'color-alpha',
                            'description'       => esc_html__( 'Here you can define a custom color for your icon.', 'et_builder' ),
                            'tab_slug'          => 'advanced',
                            'toggle_slug'       => 'background',
                        ),
                        'input_background' => array(
                            'label'             => esc_html__( 'Input Background', 'et_builder' ),
                            'type'              => 'color-alpha',
                            'description'       => esc_html__( 'Here you can define a custom color for your icon.', 'et_builder' ),
                            'tab_slug'          => 'advanced',
                            'toggle_slug'       => 'background',
                        ),
                    );

                    return $fields;
                }

                function shortcode_callback( $atts, $content = null, $function_name ) {
                    $module_id          = $this->shortcode_atts['module_id'];
                    $module_class       = $this->shortcode_atts['module_class'];



                    $module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

                    ///////////////////////////////////////////////////////////////////////////
                    
                    ob_start();
                        echo do_shortcode( '[woocommerce_cart]' );
                    $content = ob_get_clean();

                    //////////////////////////////////////////////////////////////////////////

                     $output = sprintf(
                    '<div%5$s class="et_pb_module et_pb_cart %1$s%3$s">
                        %2$s
                        %4$s',
                        'clearfix ',
                        $content,
                        '</div>',
                        ( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
						( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
                    );
            
                    return $output;
                    }
                };
        
            new dc_cart_module();
?>