<?php

class sb_et_woo_li_price_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Price', 'et_builder');
        $this->slug = 'et_pb_woo_price';

        $this->whitelisted_fields = array(
            'title',
            'text_orientation',
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
                    'label' => esc_html__('Price', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} p.price, {$this->main_css_element} p.price span, {$this->main_css_element} p.price span.woocommerce-Price-amount.amount",
                        'important' => 'all',
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'headings' => array(
                    'label' => esc_html__('Heading', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} h1, {$this->main_css_element} h2, {$this->main_css_element} h3, {$this->main_css_element} h4",
                    ),
                    'font_size' => array('default' => '30px'),
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
            'title' => array(
                'label' => __('Title', 'et_builder'),
                'type' => 'text',
                'toggle_slug' => 'main_settings',
                'description' => __('If you want a title on the price module then use this box and an H2 will be added above the price.', 'et_builder'),
            ),
            'text_orientation' => array(
                'label' => esc_html__('Text Orientation', 'et_builder'),
                'type' => 'select',
                'toggle_slug' => 'main_settings',
                'option_category' => 'layout',
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

        global $product;

        if (is_admin() || !function_exists('woocommerce_template_single_price')) {
            return;
        }

        $module_id = $this->shortcode_atts['module_id'];
        $text_orientation = $this->shortcode_atts['text_orientation'];
        $module_class = 'et_pb_woo_price ' . $this->shortcode_atts['module_class'];
        $title = $this->shortcode_atts['title'];

        if (is_rtl() && 'left' === $text_orientation) {
            $text_orientation = 'right';
        }

        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);

        //////////////////////////////////////////////////////////////////////


        $product = wc_get_product(sb_et_woo_li_get_id());

        ob_start();
        woocommerce_template_single_price();
        $content = ob_get_clean();

        //////////////////////////////////////////////////////////////////////

        $output = ' <div ' . ($module_id ? 'id="' . esc_attr($module_id) . '"' : '') . ' class="' . ($module_class ? esc_attr($module_class) . ' et_pb_text_align_' . $text_orientation : '') . ' ' . esc_attr('et_pb_module') . ' clearfix">
                        ' . ($title ? '<h2>' . $title . '</h2>' : '') . $content . '
                    </div>';

        return $output;
    }
}

new sb_et_woo_li_price_module();

?>