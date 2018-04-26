<?php

class sb_et_woo_li_product_category_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Category List (single product)', 'et_builder');
        $this->slug = 'et_pb_woo_product_category';

        $this->whitelisted_fields = array(
            'module_id',
            'module_class',
            'link_terms',
            'text_wrapper',
            'text_wrapper_end',
            'delimiter',
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
            'admin_label' => array(
                'label' => __('Admin Label', 'et_builder'),
                'type' => 'text',
                'description' => __('This will change the label of the module in the builder for easy identification.', 'et_builder'),
            ),
            'link_terms' => array(
                'label' => __('Link Category Names?', 'et_builder'),
                'type' => 'yes_no_button',
                'toggle_slug' => 'main_settings',
                'options' => array(
                    'off' => __('No', 'et_builder'),
                    'on' => __('Yes', 'et_builder'),
                ),
                'description' => __('Should each category name link to it\'s respective archive page?', 'et_builder'),
            ),
            'text_wrapper' => array(
                'label' => __('Text Wrapper', 'et_builder'),
                'type' => 'text',
                'toggle_slug' => 'main_settings',
                'description' => __('If you\'d like your links to be encapsulated and formatted then add some html here which will be prepended. This may be an icon or image or the opening tag of some html you\'d liket to use for formatting such as an h2. eg: &lt;h2&gt;.', 'et_builder'),
            ),
            'text_wrapper_end' => array(
                'label' => __('Text Wrapper End', 'et_builder'),
                'type' => 'text',
                'toggle_slug' => 'main_settings',
                'description' => __('As above but the closing tag.. Not required but if you opened an h2 for example above then you should close it here.', 'et_builder'),
            ),
            'delimiter' => array(
                'label' => __('Delimiter', 'et_builder'),
                'type' => 'text',
                'toggle_slug' => 'main_settings',
                'description' => __('This character will be used to join multiple categories together. Most will use a comma. Make sure to add a leading or trailing space if necessary', 'et_builder'),
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

        $content = $output = '';

        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];

        $delimiter = $this->shortcode_atts['delimiter'];
        $text_wrapper = $this->shortcode_atts['text_wrapper'];
        $text_wrapper_end = $this->shortcode_atts['text_wrapper_end'];
        $link_terms = $this->shortcode_atts['link_terms'];

        if (!$link_terms) {
            $link_terms = 'on';
        }
        if (!$text_wrapper) {
            $text_wrapper = '<span class="sb-woo-product-category-link">';
        }
        if (!$text_wrapper_end) {
            $text_wrapper_end = '</span>';
        }

        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);

        //////////////////////////////////////////////////////////////////////

        $terms = get_the_terms(sb_et_woo_li_get_id(), 'product_cat');

        foreach ($terms as $term) {
            if ($link_terms == 'on') {
                $link = get_term_link($term);
                $term_output = '<a href="' . $link . '">' . $term->name . '</a>';
            } else {
                $term_output = $term->name;
            }

            if ($text_wrapper && $text_wrapper_end) {
                $term_output = $text_wrapper . $term_output . $text_wrapper_end;
            }

            $categories[] = $term_output;
        }

        if ($categories) {
            $content = implode($delimiter, $categories);
        }

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

new sb_et_woo_li_product_category_module();

?>