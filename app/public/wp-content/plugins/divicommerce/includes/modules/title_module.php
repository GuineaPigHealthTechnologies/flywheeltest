<?php

class dc_title_module extends ET_Builder_Module {
                function init() {
                                $this->name = __( 'Product Title', 'et_builder' );
                                $this->slug = 'et_pb_title';
                                $this->fb_support       = true;
                                $this->main_css_element = '%%order_class%% .et_pb_product_title h1.product_title.entry-title';
                        
                                $this->whitelisted_fields = array(
                                            'background_layout',
                                            'module_id',
                                            'module_class',
                                );
                    
                                $this->fields_defaults = array(
                                                'background_layout' => array( 'light' ),
                                                'text_orientation'  => array( 'left' ),
                                );
										
                                $this->main_css_element = '%%order_class%%';
                                
                                $this->advanced_options = array(
                                        'fonts' => array(
                                                'headings'   => array(
                                                  'label'    => esc_html__( 'Headings', 'et_builder' ),
                                                    'css'      => array(
                                                         'main' => "{$this->main_css_element} h1.product_title.entry-title",
                                                   ),
                                                'font_size' => array('default' => '30px'),
                                                'line_height'    => array('default' => '1.5em'),
                                                ),
                                        
                                        ),
                                ); 
                }
            
                function get_fields() {
                    $fields = array(
                                'background_layout' => array(
                                                'label'             => esc_html__( 'Text Color', 'et_builder' ),
                                                'type'              => 'select',
                                                'option_category'   => 'configuration',
                                                'options'           => array(
                                                  'light' => esc_html__( 'Dark', 'et_builder' ),
                                                  'dark'  => esc_html__( 'Light', 'et_builder' ),
                                                ),
                                                'description'       => esc_html__( 'Here you can choose the value of your text. If you are working with a dark background, then your text should be set to light. If you are working with a light background, then your text should be dark.', 'et_builder' ),
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
				$background_layout    = $this->shortcode_atts['background_layout'];
            
				$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );
            
                                //////////////////////////////////////////////////////////////////////
                                ob_start();
                                woocommerce_template_single_title ();
                                $content = ob_get_clean();
                                //////////////////////////////////////////////////////////////////////
            
                                $output = sprintf(
                                                '<div%5$s class="et_pb_product_title %1$s%3$s%6$s">
                                                    %2$s
                                                %4$s',
                                                'clearfix ',
                                                $content,
                                                esc_attr( 'et_pb_module et_pb_bg_layout_' . $background_layout . ' et_pb_text_align_' ),
                                                '</div>',
                                                ( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
                                                ( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
                                );
                        
                                return $output;
                }

                public function _add_additional_shadow_fields() {
                        
                                }
                        
                                protected function _add_additional_border_fields() {
                                        return false;
                                }
                        
                                function process_advanced_border_options( $function_name ) {
                                        return false;
                                }
            }
        
            new dc_title_module();

?>