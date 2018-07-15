<?php

class sb_et_woo_li_account_nav_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Account Navigation', 'et_builder');
        $this->slug = 'et_pb_woo_account_nav';

        $this->whitelisted_fields = array(
            'title',
            'text_orientation',
            'show_as_buttons',
            'display_inline',
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
                        'main' => "{$this->main_css_element} p, {$this->main_css_element} p label, {$this->main_css_element} label, {$this->main_css_element} td, {$this->main_css_element} th",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'headings' => array(
                    'label' => esc_html__('Title', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} h2, {$this->main_css_element} h3",
                    ),
                    'font_size' => array('default' => '30px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'links' => array(
                    'label' => esc_html__('Links', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} a",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.4em'),
                ),
            ),
            'background' => array(
                'settings' => array(
                    'color' => 'alpha',
                ),
            ),
            'button' => array(
                'button' => array(
                    'label' => esc_html__('Buttons', 'et_builder'),
                    'css' => array(
                        'main' => $this->main_css_element . ' .et_pb_button',
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
            'text_orientation' => array(
                'label' => esc_html__('Text Orientation', 'et_builder'),
                'type' => 'select',
                'toggle_slug' => 'main_settings',
                'option_category' => 'layout',
                'options' => et_builder_get_text_orientation_options(),
                'description' => esc_html__('This controls the how your text is aligned within the module.', 'et_builder'),
            ),
            'show_as_buttons' => array(
                'label' => esc_html__('Show as Buttons', 'et_builder'),
                'type' => 'yes_no_button',
                'toggle_slug' => 'main_settings',
                'options' => array(
                    'off' => esc_html__('No', 'et_builder'),
                    'on' => esc_html__('Yes', 'et_builder'),
                ),
                'affects' => array('#et_pb_display_inline'),
                'description' => 'By default the navigation will be shown as a list with bullet points. Setting this to "Yes" will show the items as buttons instead.',
            ),
            'display_inline' => array(
                'label' => esc_html__('Show Inline', 'et_builder'),
                'type' => 'yes_no_button',
                'toggle_slug' => 'main_settings',
                'depends_show_if' => 'on',
                'options' => array(
                    'off' => esc_html__('No', 'et_builder'),
                    'on' => esc_html__('Yes', 'et_builder'),
                ),
                'description' => 'When showing buttons should they be shown one per line or adjacent to each other. Adjacent works well when the buttons are along the top of the page',
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
        $text_orientation = $this->shortcode_atts['text_orientation'];
        $module_class = $this->shortcode_atts['module_class'];
        $title = $this->shortcode_atts['title'];
        $buttons = $this->shortcode_atts['show_as_buttons'];
        $inline = $this->shortcode_atts['display_inline'];

        $output = '';

        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);

        //////////////////////////////////////////////////////////////////////

        ob_start();

        if ($title) {
            echo '<h3 class="module_title">' . $title . '</h3>';
        }

        do_action('woocommerce_before_account_navigation');

        if ($buttons == 'on') {

            foreach (wc_get_account_menu_items() as $endpoint => $label) {
                if ($inline != 'on') {
                    echo '<p class="wli-button-divider">';
                }
                echo '<a class="' . ($inline == 'on' ? 'inline-button' : '') . ' et_pb_button" href="' . esc_url(wc_get_account_endpoint_url($endpoint)) . '">' . esc_html($label) . '</a>';

                if ($inline != 'on') {
                    echo '</p>';
                }
            }

        } else {
            echo '<nav class="woocommerce-MyAccount-navigation">
                <ul>';

            foreach (wc_get_account_menu_items() as $endpoint => $label) {
                echo '<li class="' . wc_get_account_menu_item_classes($endpoint) . '">
                            <a href="' . esc_url(wc_get_account_endpoint_url($endpoint)) . '">' . esc_html($label) . '</a>
                        </li>';
            }

            echo '</ul>
            </nav>';
        }

        do_action('woocommerce_after_account_navigation');

        $content = ob_get_clean();

        //////////////////////////////////////////////////////////////////////

        if ($content) {
            $output = '<div ' . ($module_id ? 'id="' . esc_attr($module_id) . '"' : '') . ' class="' . $module_class . ' et_pb_text_align_' . $text_orientation . ' clearfix ' . ($title ? 'has_title' : '') . ' et_pb_module et_pb_woo_account_nav">' . $content . '</div>';
        }

        return $output;
    }
}

new sb_et_woo_li_account_nav_module();

?>