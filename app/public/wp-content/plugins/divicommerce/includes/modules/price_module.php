<?php

class dc_price_module extends ET_Builder_Module {
                function init() {
                    $this->name = __( 'Product Price', 'et_builder' );
                    $this->slug = 'et_pb_price';
                    $this->fb_support      = true;
            
                    $this->whitelisted_fields = array(
                        'title',
                        'module_id',
                        'module_class',
                    );
            
                    $this->fields_defaults = array();
                    $this->main_css_element = '%%order_class%%';
                    $this->advanced_options = array(
                                        'fonts' => array(
                                                'text'   => array(
                                                                'label'    => esc_html__( 'Price', 'et_builder' ),
                                                                'css'      => array(
                                                                        'main' => "{$this->main_css_element} p.price, {$this->main_css_element} p.price span",
                                                                        'important' => 'all',
                                                                ),
                                                                'font_size' => array('default' => '14px'),
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
                        
                                $module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );
                        
                                //////////////////////////////////////////////////////////////////////

                                ob_start();
                                woocommerce_template_single_price();
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
        
            new dc_price_module();

?>