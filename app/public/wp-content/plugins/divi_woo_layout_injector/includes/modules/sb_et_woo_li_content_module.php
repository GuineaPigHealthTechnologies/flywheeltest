<?php

class sb_et_woo_li_content_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Content', 'et_builder');
        $this->slug = 'et_pb_woo_text';

        $this->whitelisted_fields = array(
            'title',
            'background_layout',
            'text_orientation',
            'excerpt_only',
            'show_read_more',
            'read_more_label',
            'admin_label',
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

        $this->fields_defaults = array();
        $this->main_css_element = '%%order_class%%';

        $this->advanced_options = array(
            'fonts' => array(
                'cntnt' => array(
                    'label' => esc_html__('Content', 'et_builder'),
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
            'button' => array(
                'button' => array(
                    'label' => esc_html__('Button', 'et_builder'),
                    'css' => array(
                        'main' => $this->main_css_element . ' .et_pb_button.et_pb_more_button',
                        'plugin_main' => "{$this->main_css_element}.et_pb_module",
                    ),
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
                'description' => __('If you want a title to the module then use this box and an H2 will be added above the module.', 'et_builder'),
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
                'description' => esc_html__('Here you can choose the colour of your text. If you are working with a dark background, then your text should be set to light. If you are working with a light background, then your text should be dark.', 'et_builder'),
            ),
            'text_orientation' => array(
                'label' => esc_html__('Text Orientation', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'options' => et_builder_get_text_orientation_options(),
                'description' => esc_html__('This controls the how your text is aligned within the module.', 'et_builder'),
            ),
            'excerpt_only' => array(
                'label' => __('Excerpt Only?', 'et_builder'),
                'type' => 'yes_no_button',
                'option_category' => 'configuration',
                'options' => array(
                    'off' => __('No', 'et_builder'),
                    'on' => __('Yes', 'et_builder'),
                ),
                'toggle_slug' => 'main_settings',
                'description' => __('Should this show content only or excerpt?', 'et_builder'),
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

        $title = $this->shortcode_atts['title'];
        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];
        $excerpt_only = $this->shortcode_atts['excerpt_only'];
        $show_read_more = $this->shortcode_atts['show_read_more'];
        $read_more_label = $this->shortcode_atts['read_more_label'];
        $background_layout = $this->shortcode_atts['background_layout'];
        $text_orientation = $this->shortcode_atts['text_orientation'];
        $max_width = $this->shortcode_atts['max_width'];
        $max_width_tablet = $this->shortcode_atts['max_width_tablet'];
        $max_width_phone = $this->shortcode_atts['max_width_phone'];
        $content = '';
        $read_more_label = ($read_more_label ? $read_more_label : 'Read More');
        $is_page_builder_used = et_pb_is_pagebuilder_used(sb_et_woo_li_get_id());

        if ($is_page_builder_used) {
            $excerpt_only = 'on'; //we only want there to be access to the short content when the page builder is active to avoid infinite loops
        }

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

        //////////////////////////////////////////////////////////////////////

        $override = sb_et_woo_li_get_id_obj();

        if ($excerpt_only == 'on') {
            if (!$override) {
                $content = get_the_excerpt();
            } else {
                $content = $override->post_excerpt;
            }
        } else if ($excerpt_only == 'off') {
            if (!$override) {
                $content = get_the_content();
            } else {
                $content = $override->post_content;
            }

            $content = do_shortcode($content);
        }

        $content = wpautop($content);

        if ($show_read_more == 'on') {
            if (function_exists('get_permalink')) {
                $content .= '<p><a class="button et_pb_button et_pb_more_button" href="' . get_permalink(sb_et_woo_li_get_id()) . '">' . $read_more_label . '</a></p>';
            }
        }

        if ($content) {
            $content = ($title ? '<h2 class="module-title">' . $title . '</h2>' : '') . $content;
        }

        ob_start();

        if (is_single()) {
            do_action('sb_et_woo_li_after_content');
        } else {
            do_action('sb_et_woo_li_loop_after_content');
        }
        $content .= ob_get_clean();

        //////////////////////////////////////////////////////////////////////

        $output = sprintf(
            '<div%5$s class="%1$s%3$s%6$s">
                                        %2$s
                                    %4$s',
            'clearfix ',
            $content,
            esc_attr('et_pb_module woocommerce et_pb_woo_text et_pb_text et_pb_bg_layout_' . $background_layout . ' et_pb_text_align_' . $text_orientation),
            '</div>',
            ('' !== $module_id ? sprintf(' id="%1$s"', esc_attr($module_id)) : ''),
            ('' !== $module_class ? sprintf(' %1$s', esc_attr($module_class)) : '')
        );

        return $output;
    }
}

new sb_et_woo_li_content_module();

?>