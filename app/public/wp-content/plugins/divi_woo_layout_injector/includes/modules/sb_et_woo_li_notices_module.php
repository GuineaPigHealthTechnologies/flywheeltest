<?php

class sb_et_woo_li_notices_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Notices', 'et_builder');
        $this->slug = 'et_pb_woo_notices';

        $this->whitelisted_fields = array(
            'text_orientation',
            'module_id',
            'module_class',
        );

        $this->main_css_element = '%%order_class%%';

        $this->options_toggles = array(
            'general' => array(
                'toggles' => array(
                    'main_settings' => esc_html__('Main Settings', 'et_builder'),
                ),
            ),
        );

        $this->advanced_options = array(
            'fonts' => array(
                'cntnt' => array(
                    'label' => esc_html__('Text', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} .woocommerce-message, {$this->main_css_element} .woocommerce-info, {$this->main_css_element} .woocommerce-error",
                        'important' => 'all',
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.5em'),
                ),
            ),
            'button' => array(
                'button' => array(
                    'label' => esc_html__('Button', 'et_builder'),
                    'css' => array(
                        'main' => ".woocommerce-message a.button, .woocommerce-info a.button, .woocommerce-error a.button, .woocommerce-message a.button:after, .woocommerce-info a.button:after, .woocommerce-error a.button:after",
                        'plugin_main' => "{$this->main_css_element} ",
                        'important' => 'all',
                    ),
                ),
            ),
            'background' => array(
                'settings' => array(
                    'color' => 'alpha',
                ),
                'css' => array(
                    'main' => $this->main_css_element . ' .woocommerce-message, ' . $this->main_css_element . ' .woocommerce-info, ' . $this->main_css_element . ' .woocommerce-error'
                , 'important' => 'all',
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
            'text_orientation' => array(
                'label' => esc_html__('Text Orientation', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'options' => et_builder_get_text_orientation_options(),
                'description' => esc_html__('This controls the how your text is aligned within the module.', 'et_builder'),
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

        if (is_admin() || get_post_type() != 'product') {
            return;
        }

        $text_orientation = $this->shortcode_atts['text_orientation'];
        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];
        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);
        $module_class .= ' et_pb_text_align_' . $text_orientation;

        $output = '';

        //////////////////////////////////////////////////////////////////////

        ob_start();
        wc_print_notices();
        $content = ob_get_clean();

        //////////////////////////////////////////////////////////////////////

        if ($content) {
            $output = '<div class="clearfix et_pb_woo_notices et_pb_module ' . $module_class . '" ' . ($module_id ? 'id="' . $module_id . '"' : '') . '>' . $content . '</div>';
        }

        return $output;
    }
}

new sb_et_woo_li_notices_module();

?>