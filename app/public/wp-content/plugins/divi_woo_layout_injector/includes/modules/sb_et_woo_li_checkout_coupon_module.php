<?php

class sb_et_woo_li_checkout_coupon_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Checkout Coupon', 'et_builder');
        $this->slug = 'et_pb_woo_checkout_coupon';

        $this->whitelisted_fields = array(
            'title',
            'background_layout',
            'text_orientation',
            'apply_text',
            'placeholder',
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
                        'main' => $this->main_css_element . '.et_pb_woo_checkout_coupon .button',
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
                'description' => __('If you want a title on the module then use this box and an H3 will be added above the module content.', 'et_builder'),
            ),
            'placeholder' => array(
                'label' => esc_html__('Placeholder', 'et_builder'),
                'type' => 'text',
                'toggle_slug' => 'main_settings',
                'description' => esc_html__('The text to show in the coupon field before you type anything into it. Defaults to "Coupon Code"', 'et_builder'),
            ),
            'apply_text' => array(
                'label' => esc_html__('Apply Button Text', 'et_builder'),
                'type' => 'text',
                'toggle_slug' => 'main_settings',
                'description' => esc_html__('The text to show on the coupon submit button. Defaults to "Apply Coupon"', 'et_builder'),
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

        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];
        $title = $this->shortcode_atts['title'];

        if (!$placeholder = $this->shortcode_atts['placeholder']) {
            $placeholder = __('Coupon code');
        }
        if (!$apply_text = $this->shortcode_atts['apply_text']) {
            $apply_text = __('Apply Coupon');
        }

        //$background_layout = $this->shortcode_atts['background_layout'];
        //$text_orientation = $this->shortcode_atts['text_orientation'];
        $form = '';

        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);

        //////////////////////////////////////////////////////////////////////

        if ($title) {
            $form .= '<h3 class="module_title">' . $title . '</h3>';
        }

        $form .= '<p class="form-row form-row-first">
                    <input type="text" onkeypress="sb_woo_maybe_submit_checkout_coupon();" class="input-text coupon-module" placeholder="' . $placeholder . '" value="" />
                </p>
    
                <p class="form-row form-row-last">
                    <input type="button" class="button" onclick="sb_woo_submit_checkout_coupon();" value="' . $apply_text . '" />
                </p>
    
                <div class="clear"></div>';

        //////////////////////////////////////////////////////////////////////

        if ($form) {
            $output = '<div ' . ($module_id ? 'id="' . esc_attr($module_id) . '"' : '') . ' class="' . $module_class . ' clearfix ' . ($title ? 'has_title':'') . ' et_pb_module et_pb_woo_checkout_fields et_pb_woo_checkout_coupon">' . $form . '</div>';
        }

        return $output;
    }
}

new sb_et_woo_li_checkout_coupon_module();

?>