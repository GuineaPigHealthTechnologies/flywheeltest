<?php

class sb_et_woo_li_related_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Related', 'et_builder');
        $this->slug = 'et_pb_woo_related';

        $this->whitelisted_fields = array(
            'to_show',
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

        $this->fields_defaults = array('to_show' => 3);
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
            'to_show' => array(
                'label' => __('Items to show', 'et_builder'),
                'type' => 'select',
                'toggle_slug' => 'main_settings',
                'options' => array(
                    1 => 1
                , 2 => 2
                , 3 => 3
                , 4 => 4
                , 5 => 5
                , 6 => 6
                ),
                'description' => __('the number of related items to show. Defaults to 3', 'et_builder'),
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

        if (get_post_type() != 'product') {
            return;
        }

        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];
        $to_show = $this->shortcode_atts['to_show'];
        $output = '';
        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);

        //////////////////////////////////////////////////////////////////////

        ob_start();

        $args = array();
        $defaults = array(
            'posts_per_page' => ($to_show ? $to_show : 3),
            'columns' => 3,
            'orderby' => 'rand'
        );

        $args = wp_parse_args($args, $defaults);

        woocommerce_related_products($args);

        //woocommerce_output_related_products();
        $content = ob_get_clean();

        //////////////////////////////////////////////////////////////////////

        if ($content) {
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
        }

        return $output;
    }
}

new sb_et_woo_li_related_module();

?>