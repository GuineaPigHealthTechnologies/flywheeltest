<?php

class dc_my_account_module extends ET_Builder_Module {
                function init() {
                    $this->name = __( 'My Account', 'et_builder' );
					$this->slug = 'et_pb_my_account';
					$this->fb_support      = true;
            
                    $this->whitelisted_fields = array(
                        'title',
                        'module_id',
                        'module_class',
						'accountstyles',
						'nav_bg',
						'nav_link_hover_bg',
						'main_content_bg',
						'highlighted_text_bg',
						'notice_bg',
						'table_header_bg',
						'table_row_odd_bg',
						'table_row_even_bg',
						'form_input_bg',
						'form_select_dd_bg',
						'form_select_dd_selected_bg',
						'lr_login_bg',
						'lr_register_bg',
                    );
                    $this->fields_defaults = array();
					$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'style'  => esc_html__( 'Select Style', 'et_builder' ),
					'background' => array(
						'title' => esc_html__( 'Backgrounds', 'et_builder' ),
					),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'backgrounds-toggle' => array(
						'title' => esc_html__( 'Backgrounds', 'et_builder' ),
						'priority' => 1,
					),
					'forms-toggle' => array(
						'title' => esc_html__( 'Forms', 'et_builder' ),
						'priority' => 3,
					),
					'tables-toggle' => array(
						'title' => esc_html__( 'Tables', 'et_builder' ),
						'priority' => 2,
					),
					'notices-toggle' => array(
						'title' => esc_html__( 'Notices', 'et_builder' ),
						'priority' => 5,
					),
					'login-toggle' => array(
						'title' => esc_html__( 'Login & Registration', 'et_builder' ),
						'priority' => 5,
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
                    $this->main_css_element = '%%order_class%%';
                    $this->advanced_options = array(
                        'fonts' => array(
                            'navigationlinks'   => array(
								'hide_line_height' => true,
								'hide_letter_spacing' => true,
							'label'    => esc_html__( 'Navigation Links', 'et_builder' ),
							'css'      => array(
								'main' => "{$this->main_css_element} li.woocommerce-MyAccount-navigation-link a",
							),
							),
                            'navigationlinkshover'   => array(
								'hide_line_height' => true,
								'hide_letter_spacing' => true,
							'label'    => esc_html__( 'Navigation Links hover', 'et_builder' ),
							'css'      => array(
								'main' => "{$this->main_css_element} li.woocommerce-MyAccount-navigation-link:hover a",
							),
							),
							'headingsh2'   => array(
								'hide_line_height' => true,
								'hide_letter_spacing' => true,
							'label'    => esc_html__( 'Headings', 'et_builder' ),
							'css'      => array(
								'main' => "{$this->main_css_element} h2.woocommerce-order-details__title, {$this->main_css_element} section.woocommerce-customer-details h2, {$this->main_css_element} div.woocommerce-MyAccount-content form h3, {$this->main_css_element} header.woocommerce-Address-title.title h3, {$this->main_css_element} h3.woocommerce-column__title",
							),
							),
                            'maintext'   => array(
								'hide_line_height' => true,
								'hide_letter_spacing' => true,
							'label'    => esc_html__( 'Main', 'et_builder' ),
							'css'      => array(
								'main' => "{$this->main_css_element} div.woocommerce-MyAccount-content p, {$this->main_css_element} section.woocommerce-customer-details address, {$this->main_css_element} .u-column1.col-1.woocommerce-Address address",
							),
							),
                            'links'   => array(
								'hide_line_height' => true,
								'hide_letter_spacing' => true,
							'label'    => esc_html__( 'Links', 'et_builder' ),
							'css'      => array(
								'main' => "{$this->main_css_element} div.woocommerce-MyAccount-content p a",
							),
							),
                            'linkshover'   => array(
								'hide_line_height' => true,
								'hide_letter_spacing' => true,
							'label'    => esc_html__( 'Links Hover', 'et_builder' ),
							'css'      => array(
								'main' => "{$this->main_css_element} div.woocommerce-MyAccount-content p a:hover",
							),
							),
                            'tableheadertext'   => array(
								'hide_line_height' => true,
								'hide_letter_spacing' => true,
							'label'    => esc_html__( 'Tables Header Text', 'et_builder' ),
							'css'      => array(
								/*Come back to this download headers not changing*/
								'main' => ".et_pb_module{$this->main_css_element} .woocommerce-orders-table thead tr th span.nobr,.entry-content .et_pb_module{$this->main_css_element} .woocommerce-MyAccount-downloads thead tr th span.nobr, {$this->main_css_element} .woocommerce-table.woocommerce-table--order-details.shop_table.order_details thead tr th",
							),
							),
                            'tabletext'   => array(
								'hide_line_height' => true,
								'hide_letter_spacing' => true,
							'label'    => esc_html__( 'Table Text', 'et_builder' ),
							'css'      => array(
								'main' => "",
							),
							),
                            'loginheading'   => array(
								'hide_line_height' => true,
								'hide_letter_spacing' => true,
							'label'    => esc_html__( 'Login Heading', 'et_builder' ),
							'css'      => array(
								'main' => "{$this->main_css_element} li.woocommerce-MyAccount-navigation-link a",
							),
							),
                            'registerheading'   => array(
								'hide_line_height' => true,
								'hide_letter_spacing' => true,
							'label'    => esc_html__( 'Register Heading', 'et_builder' ),
							'css'      => array(
								'main' => "{$this->main_css_element} li.woocommerce-MyAccount-navigation-link a",
							),
							),
							'formlabels'   => array(
								'hide_line_height' => true,
								'hide_letter_spacing' => true,
							'label'    => esc_html__( 'Form Labels', 'et_builder' ),
							'css'      => array(
								'main' => "{$this->main_css_element} .nf-field-label label",
							),
							),
                            'forminputtext'   => array(
								'hide_line_height' => true,
								'hide_letter_spacing' => true,
							'label'    => esc_html__( 'Form Input Text', 'et_builder' ),
							'css'      => array(
								'main' => "{$this->main_css_element} .nf-field-label label",
							),
							),
                            'formselecttext'   => array(
								'hide_line_height' => true,
								'hide_letter_spacing' => true,
							'label'    => esc_html__( 'Form Drop Down Text', 'et_builder' ),
							'css'      => array(
								'main' => "{$this->main_css_element} .nf-field-label label",
							),
							),
                        ),
                    );
                    $this->custom_css_options = array();
                }
                function get_fields() {

						$accountstyle_options_list = array();

					// List of animation options
						$accountstyles_options = array(
							''    => esc_html__( 'Default', 'et_builder' ),
							'tabbedaccountbg'   => esc_html__( 'Side tabs with background', 'et_builder' ),
						);

						$accountstyle_option_name       = sprintf( '%1$s', $this->slug );
						$default_accountstyle = ET_Global_Settings::get_value( $accountstyle_option_name );

						// If user modifies default animation option via Customizer, we'll need to change the order
						if ( '' !== $default_accountstyle && ! empty( $default_accountstyle ) && array_key_exists( $default_accountstyle, $accountstyle_options_list ) ) {
						// The options, sans user's preferred direction
						$accountstyle_options_wo_default = $accountstyle_options_list;
						unset( $accountstyle_options_wo_default[ $default_accountstyle ] );

						// All animation options
						$accountstyle_options = array_merge(
						array( $default_accountstyle => $accountstyle_options_list[$default_accountstyle] ),
						$accountstyle_options_wo_default
						);
						} else {
						// Simply copy the animation options
						$accountstyle_options = $accountstyle_options_list;
						}
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
						'accountstyles' => array(
							'label'             => esc_html__( 'Predefined Style', 'et_builder' ),
							'type'              => 'select',
							'option_category'   => 'configuration',
							'options'           => $accountstyles_options,
							'toggle_slug'     => 'style',
							'description'       => esc_html__( 'This controls the direction of the lazy-loading animation.', 'et_builder' ),
						),
						'nav_bg' => array(
							'label'             => esc_html__( 'Navigation Area Background', 'et_builder' ),
							'type'              => 'color-alpha',
							'custom_color'      => true,
							'toggle_slug'       => 'backgrounds-toggle',
							'tab_slug'        => 'advanced',
						),
						'nav_link_hover_bg' => array(
							'label'             => esc_html__( 'Menu Item Hover Background', 'et_builder' ),
							'type'              => 'color-alpha',
							'custom_color'      => true,
							'toggle_slug'       => 'backgrounds-toggle',
							'tab_slug'        => 'advanced',
						),
						'main_content_bg' => array(
							'label'             => esc_html__( 'Main Content Area', 'et_builder' ),
							'type'              => 'color-alpha',
							'custom_color'      => true,
							'toggle_slug'       => 'backgrounds-toggle',
							'tab_slug'        => 'advanced',
						),
						'highlighted_text_bg' => array(
							'label'             => esc_html__( 'Highlight Background', 'et_builder' ),
							'type'              => 'color-alpha',
							'custom_color'      => true,
							'toggle_slug'       => 'backgrounds-toggle',
							'tab_slug'        => 'advanced',
						),
						'notice_bg' => array(
							'label'             => esc_html__( 'Notices Background', 'et_builder' ),
							'type'              => 'color-alpha',
							'custom_color'      => true,
							'toggle_slug'       => 'background',
							'toggle_slug'       => 'notices-toggle',
							'tab_slug'        => 'advanced',
						),
						'table_header_bg' => array(
							'label'             => esc_html__( 'Table Header Background', 'et_builder' ),
							'type'              => 'color-alpha',
							'custom_color'      => true,
							'toggle_slug'       => 'tables-toggle',
							'tab_slug'        => 'advanced',
						),
						'table_row_odd_bg' => array(
							'label'             => esc_html__( 'Table Odd Row Background ', 'et_builder' ),
							'type'              => 'color-alpha',
							'custom_color'      => true,
							'toggle_slug'       => 'tables-toggle',
							'tab_slug'        => 'advanced',
						),
						'table_row_even_bg' => array(
							'label'             => esc_html__( 'Table Even Row Background', 'et_builder' ),
							'type'              => 'color-alpha',
							'custom_color'      => true,
							'toggle_slug'       => 'tables-toggle',
							'tab_slug'        => 'advanced',
						),
						/*'table_footer_bg' => array(
							'label'             => esc_html__( 'Table Even Row Background', 'et_builder' ),
							'type'              => 'color-alpha',
							'custom_color'      => true,
							'toggle_slug'       => 'tables-toggle',
							'tab_slug'        => 'advanced',
						),*/
						'form_input_bg' => array(
							'label'             => esc_html__( 'Input Feilds Background', 'et_builder' ),
							'type'              => 'color-alpha',
							'custom_color'      => true,
							'toggle_slug'       => 'forms-toggle',
							'tab_slug'        => 'advanced',
						),
						'form_select_dd_bg' => array(
							'label'             => esc_html__( 'Drop Down Background', 'et_builder' ),
							'type'              => 'color-alpha',
							'custom_color'      => true,
							'toggle_slug'       => 'forms-toggle',
							'tab_slug'        => 'advanced',
						),
						'form_select_dd_selected_bg' => array(
							'label'             => esc_html__( 'Selected Drop Down Background', 'et_builder' ),
							'type'              => 'color-alpha',
							'custom_color'      => true,
							'toggle_slug'       => 'forms-toggle',
							'tab_slug'        => 'advanced',
						),
						'lr_login_bg' => array(
							'label'             => esc_html__( 'Login Form Background', 'et_builder' ),
							'type'              => 'color-alpha',
							'custom_color'      => true,
							'toggle_slug'       => 'login-toggle',
							'tab_slug'        => 'advanced',
						),
						'lr_register_bg' => array(
							'label'             => esc_html__( 'register Form Background', 'et_builder' ),
							'type'              => 'color-alpha',
							'custom_color'      => true,
							'toggle_slug'       => 'login-toggle',
							'tab_slug'        => 'advanced',
						),
                    );

                    return $fields;
                }
                function shortcode_callback( $atts, $content = null, $function_name ) {

                $module_id          = $this->shortcode_atts['module_id'];
                $module_class       = $this->shortcode_atts['module_class'];
                $accountstyles 		= $this->shortcode_atts['accountstyles'];
			/*	$accountstyles_options = $this->shortcode_atts[''];
				$accountstyle_options_list = $this->shortcode_atts[''];
			*/	$navbg 				= $this->shortcode_atts['nav_bg'];
				$navlinkhoverbg 	= $this->shortcode_atts['nav_link_hover_bg'];
				$maincontentareabg  = $this->shortcode_atts['main_content_bg'];
				$highlightedbg 		= $this->shortcode_atts['highlighted_text_bg'];
				$noticebg 			= $this->shortcode_atts['notice_bg'];
				$tableheaderbg 		= $this->shortcode_atts['table_header_bg'];
				$tablerowoddbg 		= $this->shortcode_atts['table_row_odd_bg'];
				$tablerowevenbg 	= $this->shortcode_atts['table_row_even_bg'];
				/*$tablefooterbg 		= $this->shortcode_atts['table_footer_bg'];*/
				$forminputbg 		= $this->shortcode_atts['form_input_bg'];
				$formdropdownbg 	= $this->shortcode_atts['form_select_dd_bg'];
				$formdropdownselectedbg = $this->shortcode_atts['form_select_dd_selected_bg'];
				$loginbg 			= $this->shortcode_atts['lr_login_bg'];
				$registerbg 		= $this->shortcode_atts['lr_register_bg'];
			

				if ( '' !== $navbg ) {
			        ET_Builder_Element::set_style( $function_name, array(
				        'selector'    => '%%order_class%% .woocommerce nav.woocommerce-MyAccount-navigation ul',
				        'declaration' => sprintf(
					    'background-color: %1$s%2$s!important;',
					     esc_html( $navbg ),
					     et_is_builder_plugin_active() ? ' !important' : ''
				        ),
			            ) );
		        }
				if ( '' !== $navlinkhoverbg ) {
			        ET_Builder_Element::set_style( $function_name, array(
				        'selector'    => '%%order_class%% .woocommerce li.woocommerce-MyAccount-navigation-link:hover, %%order_class%% li.woocommerce-MyAccount-navigation-link.is-active',
				        'declaration' => sprintf(
					    'background: %1$s%2$s!important;',
					     esc_html( $navlinkhoverbg ),
					     et_is_builder_plugin_active() ? ' !important' : ''
				        ),
			            ) );
		        }
				if ( '' !== $maincontentareabg ) {
			        ET_Builder_Element::set_style( $function_name, array(
				        'selector'    => '%%order_class%% .woocommerce .woocommerce-MyAccount-content',
				        'declaration' => sprintf(
					    'background-color: %1$s%2$s!important;',
					     esc_html( $maincontentareabg ),
					     et_is_builder_plugin_active() ? ' !important' : ''
				        ),
			            ) );
		        }
				if ( '' !== $highlightedbg ) {
			        ET_Builder_Element::set_style( $function_name, array(
				        'selector'    => '%%order_class%% mark.order-number, %%order_class%% mark.order-date, %%order_class%% mark.order-status',
				        'declaration' => sprintf(
					    'background-color: %1$s%2$s!important;',
					     esc_html( $highlightedbg ),
					     et_is_builder_plugin_active() ? ' !important' : ''
				        ),
			            ) );
		        }
				if ( '' !== $noticebg ) {
			        ET_Builder_Element::set_style( $function_name, array(
				        'selector'    => '%%order_class%% .woocommerce-message',
				        'declaration' => sprintf(
					    'background-color: %1$s%2$s!important;',
					     esc_html( $noticebg ),
					     et_is_builder_plugin_active() ? ' !important' : ''
				        ),
			            ) );
		        }
				if ( '' !== $tableheaderbg ) {
			        ET_Builder_Element::set_style( $function_name, array(
				        'selector'    => '%%order_class%% .woocommerce-orders-table thead, %%order_class%% .woocommerce-MyAccount-downloads thead, %%order_class%% .woocommerce-table.woocommerce-table--order-details.shop_table.order_details thead',
				        'declaration' => sprintf(
					    'background: %1$s%2$s!important;',
					     esc_html( $tableheaderbg ),
					     et_is_builder_plugin_active() ? ' !important' : ''
				        ),
			            ) );
		        }
				if ( '' !== $tablerowoddbg ) {
			        ET_Builder_Element::set_style( $function_name, array(
				        'selector'    => '%%order_class%% .woocommerce-orders-table__row:nth-child(odd), %%order_class%% .woocommerce-MyAccount-downloads tbody tr:nth-child(odd), %%order_class%% .woocommerce-table.woocommerce-table--order-details.shop_table.order_details tbody tr:nth-child(odd), %%order_class%% .woocommerce-table.woocommerce-table--customer-details.shop_table.customer_details tbody tr:nth-child(odd)',
				        'declaration' => sprintf(
					    'background: %1$s%2$s!important;',
					     esc_html( $tablerowoddbg ),
					     et_is_builder_plugin_active() ? ' !important' : ''
				        ),
			            ) );
		        }
				if ( '' !== $tablerowevenbg ) {
			        ET_Builder_Element::set_style( $function_name, array(
				        'selector'    => '%%order_class%% .woocommerce-orders-table__row:nth-child(even), %%order_class%% .woocommerce-MyAccount-downloads tbody tr:nth-child(even), %%order_class%% .woocommerce-table.woocommerce-table--order-details.shop_table.order_details tbody tr:nth-child(odd), %%order_class%% .woocommerce-table.woocommerce-table--customer-details.shop_table.customer_details tbody tr:nth-child(odd)',
				        'declaration' => sprintf(
					    'background: %1$s%2$s!important;',
					     esc_html( $tablerowevenbg ),
					     et_is_builder_plugin_active() ? ' !important' : ''
				        ),
			            ) );
		        }
				/*if ( '' !== $tablefooterbg ) {
			        ET_Builder_Element::set_style( $function_name, array(
				        'selector'    => '%%order_class%% .woocommerce-table.woocommerce-table--order-details.shop_table.order_details tfoot tr',
				        'declaration' => sprintf(
					    'background-color: %1$s%2$s!important;',
					     esc_html( $tablefooterbg ),
					     et_is_builder_plugin_active() ? ' !important' : ''
				        ),
			            ) );
		        }*/
				if ( '' !== $forminputbg ) {
			        ET_Builder_Element::set_style( $function_name, array(
				        'selector'    => '%%order_class%% input.ninja-forms-field.nf-element',
				        'declaration' => sprintf(
					    'background: %1$s%2$s!important;',
					     esc_html( $forminputbg ),
					     et_is_builder_plugin_active() ? ' !important' : ''
				        ),
			            ) );
		        }
				if ( '' !== $formdropdownbg ) {
			        ET_Builder_Element::set_style( $function_name, array(
				        'selector'    => '%%order_class%% input.ninja-forms-field.nf-element',
				        'declaration' => sprintf(
					    'background: %1$s%2$s!important;',
					     esc_html( $formdropdownbg ),
					     et_is_builder_plugin_active() ? ' !important' : ''
				        ),
			            ) );
		        }
				if ( '' !== $formdropdownselectedbg ) {
			        ET_Builder_Element::set_style( $function_name, array(
				        'selector'    => '%%order_class%% input.ninja-forms-field.nf-element',
				        'declaration' => sprintf(
					    'background: %1$s%2$s!important;',
					     esc_html( $formdropdownselectedbg ),
					     et_is_builder_plugin_active() ? ' !important' : ''
				        ),
			            ) );
		        }
				if ( '' !== $loginbg ) {
			        ET_Builder_Element::set_style( $function_name, array(
				        'selector'    => '%%order_class%% .woocommerce #customer_login div.u-column1.col-1',
				        'declaration' => sprintf(
					    'background-color: %1$s%2$s!important;',
					     esc_html( $loginbg ),
					     et_is_builder_plugin_active() ? ' !important' : ''
				        ),
			            ) );
		        }
				if ( '' !== $registerbg ) {
			        ET_Builder_Element::set_style( $function_name, array(
				        'selector'    => '%%order_class%% .woocommerce #customer_login div.u-column2.col-2',
				        'declaration' => sprintf(
					    'background-color: %1$s%2$s!important;',
					     esc_html( $registerbg ),
					     et_is_builder_plugin_active() ? ' !important' : ''
				        ),
			            ) );
		        }

                $module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );
                        
                //////////////////////////////////////////////////////////////////////

                ob_start();
                  echo  do_shortcode( '[woocommerce_my_account]' );
               $content = ob_get_clean();
                                  
                ////////////////////////////////////////////////////////////////////// 

                $output = sprintf(
                    '<div%5$s class="et_pb_module et_pb_my_account %1$s%3$s%6$s">
                        %2$s
                        %4$s',
                        'clearfix ',
                        $content,
                        esc_attr( "{$accountstyles}"  ),
                        '</div>',
                        ( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
						( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
                    );
            
                    return $output;
                }
            };
        
            new dc_my_account_module();
?>