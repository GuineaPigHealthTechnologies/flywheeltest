<?php

class sb_et_woo_li_tabs_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Tabs', 'et_builder');
        $this->slug = 'et_pb_woo_tabs';

        $this->whitelisted_fields = array(
            'title',
            'background_layout',
            'text_orientation',
            'remove_reviews',
            'remove_content',
            'remove_subheadings',
            'remove_standard_styling',
            'tab_title_alignment',
            'tab_title_bg',
            'tab_title_bg_active',
            'module_id',
            'module_class',
        );

        $this->options_toggles = array(
            'general' => array(
                'toggles' => array(
                    'main_settings' => esc_html__('Main Settings', 'et_builder'),
                ),
            ),
        );

        $this->fields_defaults = array();
        $this->main_css_element = '%%order_class%%';
        $this->advanced_options = array(
            'fonts' => array(
                'cntnt' => array(
                    'label' => esc_html__('Content', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} p",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'main_heading' => array(
                    'label' => esc_html__('Title', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} h2.module-title",
                    ),
                    'font_size' => array('default' => '30px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'headings' => array(
                    'label' => esc_html__('Headings', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} .woocommerce-Tabs-panel h1, {$this->main_css_element} .woocommerce-Tabs-panel h2, {$this->main_css_element} .woocommerce-Tabs-panel h3, {$this->main_css_element} .woocommerce-Tabs-panel h4",
                    ),
                    'font_size' => array('default' => '30px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'tbs' => array(
                    'label' => esc_html__('Tab Headings', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} .woocommerce-tabs > ul li a",
                        'important' => 'all',
                    ),
                    'font_size' => array('default' => '30px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'tbs_active' => array(
                    'label' => esc_html__('Tab (active) Headings', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} .woocommerce-tabs > ul li.active a",
                        'important' => 'all',
                    ),
                    'font_size' => array('default' => '30px'),
                    'line_height' => array('default' => '1.5em'),
                ),
            ),
            'button' => array(
                'button' => array(
                    'label' => esc_html__('Buttons', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} .et_pb_button, {$this->main_css_element} .button, {$this->main_css_element} input[type=\"button\"], {$this->main_css_element} input[type=\"submit\"]",
                        'plugin_main' => "{$this->main_css_element}",
                        'important' => 'all',
                    ),
                ),
            ),
            'background' => array(
                'settings' => array(
                    'color' => 'alpha',
                ),
                'css' => array(
                    'main' => $this->main_css_element . ' .woocommerce-Tabs-panel',
                    'important' => 'all',
                )
            ),
            'border' => array(),
            'custom_margin_padding' => array(
                'css' => array(
                    'important' => 'all',
                ),
            ),
        );
        $this->custom_css_options = array();
    }

    function get_fields()
    {
        $fields = array(
            'title' => array(
                'label' => __('Title', 'et_builder'),
                'type' => 'text',
                'toggle_slug' => 'main_settings',
                'description' => __('If you want a title to the module then use this box and an H2 will be added above the module.', 'et_builder'),
            ),
            'background_layout' => array(
                'label' => esc_html__('Text Color', 'et_builder'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => array(
                    'light' => esc_html__('Dark', 'et_builder'),
                    'dark' => esc_html__('Light', 'et_builder'),
                ),
                'toggle_slug' => 'main_settings',
                'description' => esc_html__('Here you can choose the colour of your text. If you are working with a dark background, then your text should be set to light. If you are working with a light background, then your text should be dark.', 'et_builder'),
            ),
            'text_orientation' => array(
                'label' => esc_html__('Text Orientation', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'options' => et_builder_get_text_orientation_options(),
                'description' => esc_html__('This controls the how your text is aligned within the module.', 'et_builder'),
            ),
            'remove_reviews' => array(
                'label' => 'Remove Reviews Tab'
            , 'type' => 'yes_no_button'
            , 'toggle_slug' => 'main_settings'
            , 'options' => array(
                    'off' => 'No'
                , 'on' => 'Yes'
                )
            , 'description' => 'This will remove the reviews tab from the tab system. You can use the Reviews module to show them anywhere else'
            ),
            'remove_content' => array(
                'label' => 'Remove Description Tab'
            , 'type' => 'yes_no_button'
            , 'toggle_slug' => 'main_settings'
            , 'options' => array(
                    'off' => 'No'
                , 'on' => 'Yes'
                )
            , 'description' => 'This will remove the product description tab from the tab system. You can use the Woo Content module to show it anywhere else'
            ),
            'remove_subheadings' => array(
                'label' => 'Remove Tab Content Heading'
            , 'type' => 'yes_no_button'
            , 'toggle_slug' => 'main_settings'
            , 'options' => array(
                    'off' => 'No'
                , 'on' => 'Yes'
                )
            , 'description' => 'This will remove the first heading within each tab. Normally a tab will have a heading and then the content pane will have a heading as well. Having those two close to each other may look odd so this removes the second one for a better visual appeal.'
            ),
            'remove_standard_styling' => array(
                'label' => 'Disable Standard Styling'
            , 'type' => 'yes_no_button'
            , 'toggle_slug' => 'main_settings'
            , 'options' => array(
                    'off' => 'No'
                , 'on' => 'Yes'
                )
            , 'description' => 'This will turn off the borders and grey backgrounds allowing you to style the tabs using CSS or the advanced design settings in the tab above. The design settings will still work if this is not changed but certain elements like borders and padding may not look right!'
            ),
            'tab_title_alignment' => array(
                'label'           => esc_html__( 'Tab Headings Alignment', 'et_builder' ),
                'type'            => 'text_align',
                'toggle_slug' => 'main_settings',
                'options'         => et_builder_get_text_orientation_options( array( 'justified' ) ),
                'description'     => esc_html__( 'If you\'d like the tab headings to be in the middle or right then this is the place to do it!', 'et_builder' ),
            ),
            'tab_title_bg' => array(
                'label' => esc_html__('Tab Title Background Color', 'et_builder')
            , 'type' => 'color-alpha'
            , 'custom_color' => true
            , 'toggle_slug' => 'main_settings'

            ),
            'tab_title_bg_active' => array(
                'label' => esc_html__('Tab Title (active) Background Color', 'et_builder')
            , 'type' => 'color-alpha'
            , 'custom_color' => true
            , 'toggle_slug' => 'main_settings'

            ),
            'admin_label' => array(
                'label' => __('Admin Label', 'et_builder'),
                'type' => 'text',
                'description' => __('This will change the label of the module in the builder for easy identification.', 'et_builder'),
            ),
            'module_id' => array(
                'label' => esc_html__('CSS ID', 'et_builder'),
                'type' => 'text',
                'option_category' => 'configuration',
                'tab_slug' => 'custom_css',
                'option_class' => 'et_pb_custom_css_regular',
            ),
            'module_class' => array(
                'label' => esc_html__('CSS Class', 'et_builder'),
                'type' => 'text',
                'option_category' => 'configuration',
                'tab_slug' => 'custom_css',
                'option_class' => 'et_pb_custom_css_regular',
            ),
        );

        return $fields;
    }

    function shortcode_callback($atts, $content = null, $function_name)
    {

        if (get_post_type() != 'product' || is_admin()) {
            return;
        }

        $output = '';
        $tab_title_alignment = isset( $this->shortcode_atts['tab_title_alignment'] ) ? $this->shortcode_atts['tab_title_alignment'] : '';
        $title = $this->shortcode_atts['title'];
        $background_layout = $this->shortcode_atts['background_layout'];
        $text_orientation = $this->shortcode_atts['text_orientation'];
        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];
        $remove_reviews = $this->shortcode_atts['remove_reviews'];
        $remove_content = $this->shortcode_atts['remove_content'];
        $remove_subheadings = $this->shortcode_atts['remove_subheadings'];
        $remove_standard_styling = $this->shortcode_atts['remove_standard_styling'];
        $tab_title_bg = $this->shortcode_atts['tab_title_bg'];
        $tab_title_bg_active = $this->shortcode_atts['tab_title_bg_active'];

        if ($remove_standard_styling == 'on') {
            $module_class .= ' et_woo_tabs_remove_styling';
        }

        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);

        if ($remove_subheadings == 'on') {
            $module_class .= ' remove-subheadings';
        }
        if ($tab_title_alignment) {
            $module_class .= ' tab_heading_alignment_'. $tab_title_alignment;
        }

        if ('' !== $tab_title_bg) {
            ET_Builder_Element::set_style($function_name, array(
                'selector' => '%%order_class%% .woocommerce-tabs ul.tabs li a',
                'declaration' => sprintf(
                    'background-color: %1$s !important;',
                    esc_html($tab_title_bg)
                ),
            ));
        }

        if ('' !== $tab_title_bg_active) {
            ET_Builder_Element::set_style($function_name, array(
                'selector' => '%%order_class%% .woocommerce-tabs ul.tabs li.active a',
                'declaration' => sprintf(
                    'background-color: %1$s !important;',
                    esc_html($tab_title_bg_active)
                ),
            ));
        }

        //////////////////////////////////////////////////////////////////////

        if ($remove_reviews == 'on') {
            add_filter('woocommerce_product_tabs', 'sb_et_woo_li_remove_reviews', 98);
        }
        if ($remove_content == 'on') {
            add_filter('woocommerce_product_tabs', 'sb_et_woo_li_remove_content', 98);
        }

        ob_start();
        woocommerce_output_product_data_tabs();
        $content = ob_get_clean();

        if ($content) {
            $content = ($title ? '<h2 class="module-title">' . $title . '</h2>' : '') . $content;
        }

        if ($remove_reviews == 'on') {
            remove_filter('woocommerce_product_tabs', 'sb_et_woo_li_remove_reviews', 98);
        }
        if ($remove_content == 'on') {
            remove_filter('woocommerce_product_tabs', 'sb_et_woo_li_remove_content', 98);
        }

        //////////////////////////////////////////////////////////////////////

        if ($content) {
            $output = sprintf(
                '<div%5$s class="%1$s%3$s%6$s">
                                %2$s
                            %4$s',
                'clearfix ',
                $content,
                esc_attr('et_pb_module et_pb_woo_tabs et_pb_bg_layout_' . $background_layout . ' et_pb_text_align_' . $text_orientation),
                '</div>',
                ('' !== $module_id ? sprintf(' id="%1$s"', esc_attr($module_id)) : ''),
                ('' !== $module_class ? sprintf(' %1$s', esc_attr($module_class)) : '')
            );
        }

        return $output;
    }
}

new sb_et_woo_li_tabs_module();

?>