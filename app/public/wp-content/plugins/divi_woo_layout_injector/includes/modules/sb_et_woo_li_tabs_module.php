<?php

class sb_et_woo_li_tabs_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Tabs', 'et_builder');
        $this->slug = 'et_pb_woo_tabs';

        $this->whitelisted_fields = array(
            'remove_reviews',
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
                'text' => array(
                    'label' => esc_html__('Text', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} p",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'headings' => array(
                    'label' => esc_html__('Headings', 'et_builder'),
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

        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];
        $remove_reviews = $this->shortcode_atts['remove_reviews'];

        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);

        //////////////////////////////////////////////////////////////////////

        if ($remove_reviews == 'on') {
            add_filter('woocommerce_product_tabs', 'sb_et_woo_li_remove_reviews', 98);
        }

        ob_start();
        woocommerce_output_product_data_tabs();
        $content = ob_get_clean();

        if ($remove_reviews == 'on') {
            remove_filter('woocommerce_product_tabs', 'sb_et_woo_li_remove_reviews', 98);
        }

        //////////////////////////////////////////////////////////////////////

        $output = sprintf(
            '<div%5$s class="%1$s%3$s%6$s">
                                %2$s
                            %4$s',
            'clearfix ',
            $content,
            esc_attr('et_pb_module'),
            '</div>',
            ('' !== $module_id ? sprintf(' id="%1$s"', esc_attr($module_id)) : ''),
            ('' !== $module_class ? sprintf(' %1$s', esc_attr($module_class)) : '')
        );

        return $output;
    }
}

new sb_et_woo_li_tabs_module();

?>