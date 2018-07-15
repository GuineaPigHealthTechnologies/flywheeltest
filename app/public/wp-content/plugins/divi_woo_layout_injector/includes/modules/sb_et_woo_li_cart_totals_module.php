<?php

class sb_et_woo_li_cart_totals_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Cart Totals', 'et_builder');
        $this->slug = 'et_pb_woo_cart_totals';

        $this->whitelisted_fields = array(
            'title',
            'remove_borders',
            'admin_label',
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
                'headings' => array(
                    'label' => esc_html__('Title', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} h2.module_title",
                    ),
                    'font_size' => array('default' => '30px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'table_text' => array(
                    'label' => esc_html__('Table Text', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} table td, {$this->main_css_element} table td a, {$this->main_css_element} table th",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.4em'),
                ),
            ),
            'button' => array(
                'button' => array(
                    'label' => esc_html__('Button', 'et_builder'),
                    'css' => array(
                        'main' => $this->main_css_element . ' .checkout-button.button',
                        'plugin_main' => "{$this->main_css_element}.et_pb_module",
                    ),
                ),
            ),
            'background' => array(
                'settings' => array(
                    'color' => 'alpha',
                ),
            ),
            'border' => array(
                'css' => array(
                    'important' => 'all',
                ),
            ),
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
                'description' => __('If you want a title on the module then use this box and an H3 will be added above the module content.', 'et_builder'),
            ),
            'remove_borders' => array(
                'label' => esc_html__('Remove Borders?', 'et_builder'),
                'type' => 'yes_no_button',
                'toggle_slug' => 'main_settings',
                'options' => array(
                    'off' => esc_html__('No', 'et_builder'),
                    'on' => esc_html__('Yes', 'et_builder'),
                ),
                'description' => 'Should the borders on the table be removed?',
            ),
            /*'background_layout' => array(
                'label' => esc_html__('Text Color', 'et_builder'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => array(
                    'light' => esc_html__('Dark', 'et_builder'),
                    'dark' => esc_html__('Light', 'et_builder'),
                ),
                'toggle_slug' => 'main_settings',
                'description' => esc_html__('Here you can choose the value of your text. If you are working with a dark background, then your text should be set to light. If you are working with a light background, then your text should be dark.', 'et_builder'),
            ),
            'text_orientation' => array(
                'label' => esc_html__('Text Orientation', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'options' => et_builder_get_text_orientation_options(),
                'description' => esc_html__('This controls the how your text is aligned within the module.', 'et_builder'),
            ),
            'show_read_more' => array(
                'label' => __('Show Read More?', 'et_builder'),
                'type' => 'yes_no_button',
                'option_category' => 'configuration',
                'options' => array(
                    'off' => __('No', 'et_builder'),
                    'on' => __('Yes', 'et_builder'),
                ),
                'toggle_slug' => 'main_settings',
                'affects' => array('#et_pb_read_more_label'),
                'description' => __('Should a read more button be shown below the content?', 'et_builder'),
            ),
            'read_more_label' => array(
                'label' => __('Read More Label', 'et_builder'),
                'type' => 'text',
                'depends_show_if' => 'on',
                'toggle_slug' => 'main_settings',
                'description' => __('What should the read more button be labelled as? Defaults to "Read More".', 'et_builder'),
            ),
            'max_width' => array(
                'label' => esc_html__('Max Width', 'et_builder'),
                'type' => 'text',
                'option_category' => 'layout',
                'mobile_options' => true,
                'tab_slug' => 'advanced',
                'toggle_slug' => 'main_settings',
                'validate_unit' => true,
            ),
            'max_width_tablet' => array(
                'type' => 'skip',
                'tab_slug' => 'advanced',
            ),
            'max_width_phone' => array(
                'type' => 'skip',
                'tab_slug' => 'advanced',
            ),*/
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

        if (is_admin() || !is_cart()) {
            return;
        }

        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];
        $title = $this->shortcode_atts['title'];
        $remove_borders = $this->shortcode_atts['remove_borders'];
        //$remove_thumbs = $this->shortcode_atts['remove_thumbs'];
        /*$background_layout = $this->shortcode_atts['background_layout'];
        $text_orientation = $this->shortcode_atts['text_orientation'];
        $max_width = $this->shortcode_atts['max_width'];
        $max_width_tablet = $this->shortcode_atts['max_width_tablet'];
        $max_width_phone = $this->shortcode_atts['max_width_phone'];*/

        $output = '';

        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);

        /*if ('' !== $max_width_tablet || '' !== $max_width_phone || '' !== $max_width) {
            $max_width_values = array(
                'desktop' => $max_width,
                'tablet' => $max_width_tablet,
                'phone' => $max_width_phone,
            );

            et_pb_generate_responsive_css($max_width_values, '%%order_class%%', 'max-width', $function_name);
        }*/

        //////////////////////////////////////////////////////////////////////

        //if ($remove_company == 'on') {
        //add_filter('woocommerce_checkout_fields', 'sb_et_woo_li_checkout_remove_company');
        //}

        ob_start();

        if ($title) {
            echo '<h3 class="module_title">' . $title . '</h3>';
        }

        echo '<div class="cart-collaterals">';
        do_action('woocommerce_cart_collaterals');
        echo '</div>';

        $content = ob_get_clean();

        //////////////////////////////////////////////////////////////////////

        if ($content) {
            $output = '<div ' . ($module_id ? 'id="' . esc_attr($module_id) . '"' : '') . ' class="' . $module_class . ' clearfix ' . ($title ? 'has_title':'') . ($remove_borders == 'on' ? ' hide-borders ' : '') . 'et_pb_module et_pb_woo_cart_totals">' . $content . '</div>';
        }

        return $output;
    }
}

new sb_et_woo_li_cart_totals_module();

?>