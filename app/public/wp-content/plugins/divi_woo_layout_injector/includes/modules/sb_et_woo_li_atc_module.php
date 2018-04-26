<?php

class sb_et_woo_li_atc_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Add To Cart', 'et_builder');
        $this->slug = 'et_pb_woo_atc';

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
                /*'text'   => array(
                                'label'    => esc_html__( 'Text', 'et_builder' ),
                                'css'      => array(
                                        'main' => "{$this->main_css_element} p",
                                ),
                                'font_size' => array('default' => '14px'),
                                'line_height'    => array('default' => '1.5em'),
                ),*/
                'vprice' => array(
                    'label' => esc_html__('Variable Price', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} .woocommerce-variation-price, {$this->main_css_element} .woocommerce-variation-price span",
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
                'buttons' => array(
                    'label' => esc_html__('Add to Cart Button', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} .single_add_to_cart_button, {$this->main_css_element} button",
                        'important' => 'all',
                    ),
                    'font_size' => array('default' => '20px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'buttons_disabled' => array(
                    'label' => esc_html__('Add to Cart Button (disabled)', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} .single_add_to_cart_button.disabled, {$this->main_css_element} .button.disabled, {$this->main_css_element} .button.wc-variation-selection-needed",
                        'important' => 'all',
                    ),
                    'font_size' => array('default' => '20px'),
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
                'description' => __('If you want a title on the add to cart module then use this box and an H2 will be added above the form.', 'et_builder'),
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

        if (is_admin()) {
            return;
        }

        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];
        $title = $this->shortcode_atts['title'];
        $text_orientation = $this->shortcode_atts['text_orientation'];

        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);

        if (is_rtl() && 'left' === $text_orientation) {
            $text_orientation = 'right';
        }

        //////////////////////////////////////////////////////////////////////

        ob_start();

        //woocommerce_template_single_add_to_cart(); //calls directly but doesn't allow people to turn things off etc.
        //moving to an action based approach
        if (is_single()) {

            do_action('woocommerce_before_single_product'); //for add to cart buttons
            do_action('woocommerce_single_product_summary'); //for add to cart buttons
        } else if (is_page()) { //for pages
            sb_et_woo_li_get_id(); //refresh the product object.. you know...just in case Woo hates me!

            do_action('woocommerce_single_product_summary'); //for add to cart buttons
        } else { //for archive pages
            do_action('woocommerce_after_shop_loop_item'); //for add to cart buttons
        }

        $content = ob_get_clean();

        //////////////////////////////////////////////////////////////////////

        if ($content) {
            $output = '<div ' . ($module_id ? ' id="' . esc_attr($module_id) . '"' : '') . ' class="clearfix et_pb_module woocommerce et_woo_atc ' . esc_attr($module_class) . ' et_pb_text_align_' . $text_orientation . '">' . ($title ? '<h2>' . $title . '</h2>' : '') . $content . '</div>';
        }

        return $output;
    }
}

new sb_et_woo_li_atc_module();

?>