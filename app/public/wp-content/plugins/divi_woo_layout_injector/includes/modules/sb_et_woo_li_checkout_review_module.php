<?php

class sb_et_woo_li_checkout_review_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Checkout Order Review', 'et_builder');
        $this->slug = 'et_pb_woo_checkout_review';

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
                'ctnt' => array(
                    'label' => esc_html__('Labels/Info', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} p, {$this->main_css_element} label, {$this->main_css_element} a, {$this->main_css_element} td, {$this->main_css_element} th",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'headings' => array(
                    'label' => esc_html__('Title', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} h2.module_title",
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
                'description' => __('If you want a title on the module then use this box and an H3 will be added above the module content.', 'et_builder'),
            ),
            'remove_borders' => array(
                'label' => __('Remove Borders?', 'et_builder'),
                'type' => 'yes_no_button',
                'option_category' => 'configuration',
                'options' => array(
                    'off' => __('No', 'et_builder'),
                    'on' => __('Yes', 'et_builder'),
                ),
                'toggle_slug' => 'main_settings',
                'description' => __('By default the WooCommerce review table has a light grey border around it. This setting will remove them.', 'et_builder'),
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

        if (is_admin() || !is_checkout()) {
            return;
        }

        $title = $this->shortcode_atts['title'];
        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];
        $remove_borders = $this->shortcode_atts['remove_borders'];

        if ($remove_borders == 'on') {
            $module_class .= ' remove_borders';
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

        do_action('woocommerce_checkout_order_review'); //for legacy reasons. all known hooks detatched leaving room for third party plugins to add their own
        do_action('sb_et_woo_li_checkout_order_review');
        do_action( 'woocommerce_checkout_after_order_review' );
        $content = ob_get_clean();

        //////////////////////////////////////////////////////////////////////

        if ($content) {
            $output = '<div ' . ($module_id ? 'id="' . esc_attr($module_id) . '"' : '') . ' class="' . $module_class . ' clearfix et_pb_module et_pb_woo_checkout_review">' . $content . '</div>';
        }

        return $output;
    }
}

new sb_et_woo_li_checkout_review_module();

?>