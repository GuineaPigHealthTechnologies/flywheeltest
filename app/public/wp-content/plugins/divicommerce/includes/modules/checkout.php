<?php
class dc_checkout_module extends ET_Builder_Module {
                function init() {
                    $this->name = __( 'Checkout', 'et_builder' );
                    $this->slug = 'et_pb_checkout';

                    $this->whitelisted_fields = array(
                        'title',
                        'module_id',
                        'module_class',
                        'remove_field_name',
                        'remove_field_surname',
                        'remove_field_company',
                        'remove_field_address_1',
                        'remove_field_address_2',
                        'remove_field_billing_city',
                        'remove_field_billing_postal_code',
                        'remove_field_billing_country',
                        'remove_field_billing_state',
                        'remove_field_phone',
                        'remove_field_email',
                        'remove_field_comments',
                    );
                    $this->fields_defaults = array(
                        'remove_field_name' => array( 'off' ),
                        'remove_field_surname' => array( 'off' ),
                        'remove_field_company' => array( 'off' ),
                        'remove_field_address_1' => array( 'off' ),
                        'remove_field_address_2' => array( 'off' ),
                        'remove_field_billing_city' => array( 'off' ),
                        'remove_field_billing_postal_code' => array( 'off' ),
                        'remove_field_billing_country' => array( 'off' ),
                        'remove_field_billing_state' => array( 'off' ),
                        'remove_field_phone' => array( 'off' ),
                        'remove_field_email' => array( 'off' ),
                        'remove_field_comments' => array( 'off' ),
                    );
					$this->options_toggles = array(
                        'general' => array(
                            'toggles' => array(
                                'remove_fields' => esc_html__('Remove Fields', 'et_builder'),
                            ),
                        ),
                        'advanced' => array(
                            'toggles' => array(
                                'notices' => esc_html__('Notices', 'et_builder'),
                                'forms' => esc_html__('Form Styler', 'et_builder'),
                                'tables' => esc_html__('Tables', 'et_builder'),
                                'payment_box' => esc_html__('Payment Box', 'et_builder'),
                            ),
                        ),
                        'custom_css' => array(
                            'toggles' => array(

                            ),
                        ),
                    );

                    $this->main_css_element = '%%order_class%%';
                    $this->advanced_options = array(

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
                        'remove_field_name' => array(
				            'label'           => esc_html__( 'Remove Name Field', 'et_builder' ),
				            'type'            => 'yes_no_button',
				            'options'         => array(
					                'on'  => esc_html__( 'Yes', 'et_builder' ),
					                'off' => esc_html__( 'No', 'et_builder' ),
				                ),
        			    	    'tab_slug'        => 'general',
		            		    'toggle_slug'     => 'remove_fields',
				                'description'     => esc_html__( 'If enabled, the field will take 100% of the width of the content area, otherwise it will take 50%', 'et_builder' ),
			            ),
                    );

                    return $fields;
                }

                function shortcode_callback( $atts, $content = null, $function_name ) {
                    $module_id          = $this->shortcode_atts['module_id'];
                    $module_class       = $this->shortcode_atts['module_class'];
                    $remove_name            = $this->shortcode_atts['remove_field_name'];



                    $module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

                    
                    ///////////////////////////////////////////////////////////////////////////
                    
                    ob_start();
                        echo do_shortcode( '[woocommerce_checkout]' );
                        
                    $content = ob_get_clean();

                    //////////////////////////////////////////////////////////////////////////

                     $output = sprintf(
                    '<div%5$s class="et_pb_module et_pb_my_account %1$s%3$s">
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
        
            new dc_checkout_module();

                
?>