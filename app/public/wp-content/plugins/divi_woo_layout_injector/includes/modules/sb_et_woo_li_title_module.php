<?php

class sb_et_woo_li_title_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Title', 'et_builder');
        $this->slug = 'et_pb_woo_title';

        $this->whitelisted_fields = array(
            'background_layout',
            'text_orientation',
            'module_id',
            'module_class',
            'max_width',
            'max_width_tablet',
            'max_width_phone',
        );

        $this->options_toggles = array(
            'general' => array(
                'toggles' => array(
                    'main_settings' => esc_html__('Main Settings', 'et_builder'),
                ),
            ),
        );

        $this->fields_defaults = array(
            'background_layout' => array('light'),
            'text_orientation' => array('left'),
        );

        $this->main_css_element = '%%order_class%%';

        $this->advanced_options = array(
            'fonts' => array(
                'headings' => array(
                    'label' => esc_html__('Headings', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} h1.product_title, {$this->main_css_element} h2.product_title",
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
    }

    function get_fields()
    {
        $fields = array(
            'background_layout' => array(
                'label' => esc_html__('Text Color', 'et_builder'),
                'type' => 'select',
                'toggle_slug' => 'main_settings',
                'option_category' => 'configuration',
                'options' => array(
                    'light' => esc_html__('Dark', 'et_builder'),
                    'dark' => esc_html__('Light', 'et_builder'),
                ),
                'description' => esc_html__('Here you can choose the value of your text. If you are working with a dark background, then your text should be set to light. If you are working with a light background, then your text should be dark.', 'et_builder'),
            ),
            'text_orientation' => array(
                'label' => esc_html__('Text Orientation', 'et_builder'),
                'type' => 'select',
                'toggle_slug' => 'main_settings',
                'option_category' => 'layout',
                'options' => et_builder_get_text_orientation_options(),
                'description' => esc_html__('This controls the how your text is aligned within the module.', 'et_builder'),
            ),
            'max_width' => array(
                'label' => esc_html__('Max Width', 'et_builder'),
                'type' => 'text',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'mobile_options' => true,
                'tab_slug' => 'advanced',
                'validate_unit' => true,
            ),
            'max_width_tablet' => array(
                'type' => 'skip',
                'tab_slug' => 'advanced',
            ),
            'max_width_phone' => array(
                'type' => 'skip',
                'tab_slug' => 'advanced',
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

        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];
        $background_layout = $this->shortcode_atts['background_layout'];
        $text_orientation = $this->shortcode_atts['text_orientation'];
        $max_width = $this->shortcode_atts['max_width'];
        $max_width_tablet = $this->shortcode_atts['max_width_tablet'];
        $max_width_phone = $this->shortcode_atts['max_width_phone'];

        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);

        if ('' !== $max_width_tablet || '' !== $max_width_phone || '' !== $max_width) {
            $max_width_values = array(
                'desktop' => $max_width,
                'tablet' => $max_width_tablet,
                'phone' => $max_width_phone,
            );

            et_pb_generate_responsive_css($max_width_values, '%%order_class%%', 'max-width', $function_name);
        }

        if (is_rtl() && 'left' === $text_orientation) {
            $text_orientation = 'right';
        }

        $title = get_the_title();

        if ($override = sb_et_woo_li_get_id_obj()) {
            $title = $override->post_title;
        }

        $title = apply_filters('sb_et_woo_li_title', $title, $this->shortcode_atts);

        //////////////////////////////////////////////////////////////////////

        ob_start();

        $s_tag = apply_filters('sb_et_woo_li_single_title_tag', 'h1', $this->shortcode_atts);
        $a_tag = apply_filters('sb_et_woo_li_archive_title_tag', 'h2', $this->shortcode_atts);

        if (is_archive() || is_search() || $override) {
            echo '<' . $a_tag . ' itemprop="name" class="product_title entry-title"><a href="' . get_permalink(sb_et_woo_li_get_id()) . '">' . $title . '</a></' . $a_tag . '>';
        } else {
            echo '<' . $s_tag . ' itemprop="name" class="product_title entry-title">' . $title . '</' . $s_tag . '>';
        }

        if (is_single()) {
            do_action('sb_et_woo_li_after_title');
        } else {
            do_action('sb_et_woo_li_loop_after_title');
        }

        $content = ob_get_clean();

        //////////////////////////////////////////////////////////////////////

        if ($content) {
            $output = sprintf(
                '<div%5$s class="%1$s%3$s%6$s">
                                                    %2$s
                                                %4$s',
                'clearfix ',
                $content,
                esc_attr('et_pb_module et_pb_woo_title et_pb_bg_layout_' . $background_layout . ' et_pb_text_align_' . $text_orientation),
                '</div>',
                ('' !== $module_id ? sprintf(' id="%1$s"', esc_attr($module_id)) : ''),
                ('' !== $module_class ? sprintf(' %1$s', esc_attr($module_class)) : '')
            );
        }

        return $output;
    }
}

new sb_et_woo_li_title_module();

?>