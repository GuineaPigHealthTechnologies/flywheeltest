<?php

class sb_et_woo_li_breadcrumb_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Breadcrumb', 'et_builder');
        $this->slug = 'et_pb_woo_breadcrumb';

        $this->whitelisted_fields = array(
            'text_orientation',
            'background_layout',
            'module_id',
            'module_class',
            'delimiter',
        );

        $this->options_toggles = array(
            'general' => array(
                'toggles' => array(
                    'main_settings' => esc_html__('Main Settings', 'et_builder'),
                ),
            ),
        );

        $this->main_css_element = '%%order_class%%';
        $this->fields_defaults = array();
        $this->advanced_options = array(
            'fonts' => array(
                'cntnt' => array(
                    'label' => esc_html__('Link', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} .woocommerce-breadcrumb, {$this->main_css_element} .woocommerce-breadcrumb a",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'cntnt_hover' => array(
                    'label' => esc_html__('Link (hover_', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} .woocommerce-breadcrumb a:hover",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.5em'),
                ),
            ),
            'background' => array(
                'settings' => array(
                    'color' => 'alpha',
                ),
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
            'text_orientation' => array(
                'label' => esc_html__('Text Orientation', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'options' => et_builder_get_text_orientation_options(),
                'description' => esc_html__('This controls the how your text is aligned within the module.', 'et_builder'),
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
            'delimiter' => array(
                'label' => __('Delimiter', 'et_builder'),
                'type' => 'text',
                'toggle_slug' => 'main_settings',
                'description' => __('The divider between each breadcrumb. Defaults to / if left blank. HTML character codes preferred. HTML itself may be stripped out by the Divi Builder', 'et_builder'),
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

        $text_orientation = $this->shortcode_atts['text_orientation'];
        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];
        $background_layout = $this->shortcode_atts['background_layout'];

        if ($delimiter = $this->shortcode_atts['delimiter']) {
            $delimiter = '&nbsp;' . trim($delimiter) . '&nbsp;';
        } else {
            $delimiter = ' / ';
        }

        if (is_rtl() && 'left' === $text_orientation) {
            $text_orientation = 'right';
        }

        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);

        //////////////////////////////////////////////////////////////////////

        $args = array(
            'delimiter'   => $delimiter
        );

        ob_start();
        woocommerce_breadcrumb($args);
        $content = ob_get_clean();

        //////////////////////////////////////////////////////////////////////

        $output = '<div ' . ($module_id ? 'id="' . esc_attr($module_id) . '"':'') . ' class="clearfix et_pb_module et_pb_woo_breadcrumb ' . $module_class . ' et_pb_text_align_' . $text_orientation . ' et_pb_bg_layout_' . $background_layout . '">' . $content . '</div>';

        return $output;
    }
}

new sb_et_woo_li_breadcrumb_module();

?>