<?php

class dc_additionalinfo_module extends ET_Builder_Module {
                function init() {
                    $this->name = __( 'Product Additional Info', 'et_builder' );
					$this->slug = 'et_pb_additional_info_meta';
					$this->fb_support      = true;
            
                    $this->whitelisted_fields = array(
                        'module_id',
                        'module_class',
                        'rowoddbg',
                        'rowevenbg',
                        'rowoddbordercolour',
                        'rowevenbordercolor',
                        'roweventextcolor',
                        'font',
                        'rowoddtextcolor',
                    );
            
                    $this->fields_defaults = array();
                    $this->main_css_element = '.et_pb_additional_information';
                    $this->advanced_options = array(
                        
                    );
                    $this->custom_css_options = array();
                }
            
                function get_fields() {
                    $fields = array(
                        'rowoddbg' => array(
				            'label'    => esc_html__( 'Row Odd Background Colour', 'et_builder' ),
				            'type'     => 'color-alpha',
				            'custom_color'      => true,
                    ),
                    'rowevenbg' => array(
				            'label'    => esc_html__( 'Row Even Background Colour', 'et_builder' ),
				            'type'     => 'color-alpha',
				            'custom_color'      => true,
                    ),
                    'rowoddbordercolour' => array(
				            'label'    => esc_html__( 'Row Odd Border Colour', 'et_builder' ),
				            'type'     => 'color-alpha',
				            'custom_color'      => true,
                    ),
                    'rowevenbordercolor' => array(
				            'label'    => esc_html__( 'Row Even Border Colour', 'et_builder' ),
				            'type'     => 'color-alpha',
				            'custom_color'      => true,
                    ),
                    'roweventextcolor' => array(
				            'label'    => esc_html__( 'Row Even Text Colour', 'et_builder' ),
				            'type'     => 'color-alpha',
				            'custom_color'      => true,
                            'tab_slug' => 'advanced',
                    ),
                    'rowoddtextcolor' => array(
				            'label'    => esc_html__( 'Row Odd Text Colour', 'et_builder' ),
				            'type'     => 'color-alpha',
				            'custom_color'      => true,
                            'tab_slug' => 'advanced',
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
                                
                                $module_id          = $this->shortcode_atts['module_id'];
                                $module_class       = $this->shortcode_atts['module_class'];
                                $rowoddbg           = $this->shortcode_atts['rowoddbg'];
                                $rowevenbg          = $this->shortcode_atts['rowevenbg'];
                                $rowoddbordercolour = $this->shortcode_atts['rowoddbordercolour'];
                                $rowevenbordercolour = $this->shortcode_atts['rowevenbordercolor'];
                                $roweventextcolour  = $this->shortcode_atts['roweventextcolor'];
                                $rowoddtextcolour  = $this->shortcode_atts['rowoddtextcolor'];
                        
                                $module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );
                                
                                if ( '' !== $rowoddbg ) {
			                    ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% tr:nth-child(odd)',
				                'declaration' => sprintf(
					            'background: %1$s%2$s!important;',
					            esc_html( $rowoddbg ),
					            et_is_builder_plugin_active() ? ' !important' : ''
				                 ),
			                     ) );
		                        }
                                if ( '' !== $rowevenbg ) {
			                    ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% tr:nth-child(even)',
				                'declaration' => sprintf(
					            'background: %1$s%2$s!important;',
					            esc_html( $rowevenbg ),
					            et_is_builder_plugin_active() ? ' !important' : ''
				                 ),
			                     ) );
		                        }
                                if ( '' !== $rowoddbordercolour ) {
			                    ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% tr:nth-child(odd)',
				                'declaration' => sprintf(
					            'border-bottom: %1$s%2$s 1px solid!important;',
					            esc_html( $rowoddbordercolour ),
					            et_is_builder_plugin_active() ? ' !important' : ''
				                 ),
			                     ) );
		                        }
                                if ( '' !== $rowoddbordercolour ) {
			                    ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% tr:first-child',
				                'declaration' => sprintf(
					            'border-top: %1$s%2$s 1px solid!important;',
					            esc_html( $rowoddbordercolour ),
					            et_is_builder_plugin_active() ? ' !important' : ''
				                 ),
			                     ) );
		                        }
                                if ( '' !== $rowevenbordercolour ) {
			                    ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% tr:nth-child(even)',
				                'declaration' => sprintf(
					            'border-bottom: %1$s%2$s 1px solid!important;',
					            esc_html( $rowevenbordercolour ),
					            et_is_builder_plugin_active() ? ' !important' : ''
				                 ),
			                     ) );
		                        }
                                if ( '' !== $roweventextcolour ) {
			                    ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% tr:nth-child(even)',
				                'declaration' => sprintf(
					            'color: %1$s%2$s!important;',
					            esc_html( $roweventextcolour ),
					            et_is_builder_plugin_active() ? ' !important' : ''
				                 ),
			                     ) );
		                        }
                                if ( '' !== $roweventextcolour ) {
			                    ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% tr:nth-child(even) th',
				                'declaration' => sprintf(
					            'color: %1$s%2$s!important;',
					            esc_html( $roweventextcolour ),
					            et_is_builder_plugin_active() ? ' !important' : ''
				                 ),
			                     ) );
		                        }
                                if ( '' !== $rowoddtextcolour ) {
			                    ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% tr:nth-child(odd)',
				                'declaration' => sprintf(
					            'color: %1$s%2$s!important;',
					            esc_html( $rowoddtextcolour ),
					            et_is_builder_plugin_active() ? ' !important' : ''
				                 ),
			                     ) );
		                        }
                                if ( '' !== $rowoddtextcolour ) {
			                    ET_Builder_Element::set_style( $function_name, array(
				                'selector'    => '%%order_class%% tr:nth-child(odd) th',
				                'declaration' => sprintf(
					            'color: %1$s%2$s!important;',
					            esc_html( $rowoddtextcolour ),
					            et_is_builder_plugin_active() ? ' !important' : ''
				                 ),
			                     ) );
		                        }
                                //////////////////////////////////////////////////////////////////////
                                  
                                ob_start();
                                woocommerce_product_additional_information_tab();
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
        
            new dc_additionalinfo_module();

?>