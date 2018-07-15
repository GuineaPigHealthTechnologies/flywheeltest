<?php

class sb_et_woo_li_checkout_payment_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Checkout Payment', 'et_builder');
        $this->slug = 'et_pb_woo_checkout_payment';

        $this->whitelisted_fields = array(
            'title',
            'remove_default_styling',
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
                'ctnt' => array(
                    'label' => esc_html__('Labels/Info', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} p, {$this->main_css_element} label, {$this->main_css_element} a",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'headings' => array(
                    'label' => esc_html__('Title', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} h3.module_title",
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
            'button' => array(
                'button' => array(
                    'label' => esc_html__( 'Button', 'et_builder' ),
                    'css' => array(
                        'main' => $this->main_css_element . '.et_pb_woo_checkout_payment .woocommerce-checkout-payment #place_order',
                        'plugin_main' => "{$this->main_css_element}.et_pb_module",
                    ),
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
                'description' => __('If you want a title on the module then use this box and an H2 will be added above the module content.', 'et_builder'),
            ),
            'remove_default_styling' => array(
                'label' => __('Remove default styling?', 'et_builder'),
                'type' => 'yes_no_button',
                'option_category' => 'configuration',
                'options' => array(
                    'off' => __('No', 'et_builder'),
                    'on' => __('Yes', 'et_builder'),
                ),
                'toggle_slug' => 'main_settings',
                'description' => __('By default the WooCommerce payment box has a grey background and slightly rounded corners. This looks ok but may conflict with other styling options you intend to make for the module. This will remove the grey background and rounded corners for a better and more fluid integration into your site.', 'et_builder'),
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

        if (is_admin() || !is_checkout()) {
            return;
        }

        $title = $this->shortcode_atts['title'];
        $remove_default_styling = $this->shortcode_atts['remove_default_styling'];
        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];

        if ($remove_default_styling == 'on') {
            $module_class .= ' remove_core_styling';
        }

        /*$show_read_more = $this->shortcode_atts['show_read_more'];
        $read_more_label = $this->shortcode_atts['read_more_label'];
        $background_layout = $this->shortcode_atts['background_layout'];
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

        ob_start();
        do_action( 'woocommerce_checkout_before_order_review' );

        if ($title) {
            echo '<h3 class="module_title">' . $title . '</h3>';
        }

        do_action('sb_et_woo_li_checkout_order_payment');
        do_action( 'woocommerce_checkout_after_order_review' );
        $content = ob_get_clean();

        //////////////////////////////////////////////////////////////////////

        if ($content) {
            $output = '<div ' . ($module_id ? 'id="' . esc_attr($module_id) . '"' : '') . ' class="' . $module_class . ' clearfix et_pb_module et_pb_woo_checkout_payment">' . $content . '</div>';
        }

        return $output;
    }
}

new sb_et_woo_li_checkout_payment_module();

?>