<?php

class sb_et_woo_li_account_page_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Account Page', 'et_builder');
        $this->slug = 'et_pb_woo_account_page';

        $this->whitelisted_fields = array(
            'title',
            'admin_label',
            'text_orientation',
            'orders_layout',
            'downloads_layout',
            'address_layout',
            'account_details_layout',
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
                        'main' => "{$this->main_css_element} address, {$this->main_css_element} legend, {$this->main_css_element} p, {$this->main_css_element} p label, {$this->main_css_element} label, {$this->main_css_element} td, {$this->main_css_element} th",
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
            'button' => array(
                'button' => array(
                    'label' => esc_html__('Button', 'et_builder'),
                    'css' => array(
                        'main' => $this->main_css_element . ' .et_pb_button,' . $this->main_css_element . ' .button',
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
        $options = array(0 => '-- None/Default --');
        $layout_query = array(
            'post_type' => 'et_pb_layout'
        , 'posts_per_page' => -1
        , 'meta_query' => array(
                array(
                    'key' => '_et_pb_predefined_layout',
                    'compare' => 'NOT EXISTS',
                ),
            )
        );

        if ($layouts = get_posts($layout_query)) {
            foreach ($layouts as $layout) {
                $options[$layout->ID] = $layout->post_title;
            }
        }

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
            'orders_layout' => array(
                'label' => esc_html__('Orders Layout', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'options' => $options,
                'description' => esc_html__('Choose a layout to use for the Orders part of the My Account pages', 'et_builder'),
            ),
            'downloads_layout' => array(
                'label' => esc_html__('Downloads Layout', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'options' => $options,
                'description' => esc_html__('Choose a layout to use for the Downloads part of the My Account pages', 'et_builder'),
            ),
            'address_layout' => array(
                'label' => esc_html__('Addresses Layout', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'options' => $options,
                'description' => esc_html__('Choose a layout to use for the Addresses / Edit Addresses part of the My Account pages', 'et_builder'),
            ),
            'account_details_layout' => array(
                'label' => esc_html__('Account Details Layout', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'options' => $options,
                'description' => esc_html__('Choose a layout to use for the Account Details part of the My Account pages', 'et_builder'),
            ),
        );

        return $fields;
    }

    function shortcode_callback($atts, $content = null, $function_name)
    {
        global $sb_et_woo_li_account_layouts;

        if (is_admin()) {
            return;
        }

        $text_orientation = $this->shortcode_atts['text_orientation'];
        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];
        $title = $this->shortcode_atts['title'];

        if ($orders_layout = $this->shortcode_atts['orders_layout']) {
            $sb_et_woo_li_account_layouts['orders'] = $orders_layout;
            remove_action('woocommerce_account_orders_endpoint', 'woocommerce_account_orders');
            add_action('woocommerce_account_orders_endpoint', 'sb_et_woo_li_get_orders_layout_html');
        }
        if ($downloads_layout = $this->shortcode_atts['downloads_layout']) {
            $sb_et_woo_li_account_layouts['downloads'] = $downloads_layout;
            remove_action('woocommerce_account_downloads_endpoint', 'woocommerce_account_downloads');
            add_action('woocommerce_account_downloads_endpoint', 'sb_et_woo_li_get_downloads_layout_html');
        }
        if ($address_layout = $this->shortcode_atts['address_layout']) {
            $sb_et_woo_li_account_layouts['addresses'] = $address_layout;
            remove_action('woocommerce_account_edit-address_endpoint', 'woocommerce_account_edit_address');
            add_action('woocommerce_account_edit-address_endpoint', 'sb_et_woo_li_get_addresses_layout_html');
        }
        if ($account_details_layout = $this->shortcode_atts['account_details_layout']) {
            $sb_et_woo_li_account_layouts['edit_account'] = $account_details_layout;
            remove_action('woocommerce_account_edit-account_endpoint', 'woocommerce_account_edit_account');
            add_action('woocommerce_account_edit-account_endpoint', 'sb_et_woo_li_get_edit_account_layout_html');
        }

        $output = '';

        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);

        //////////////////////////////////////////////////////////////////////

        ob_start();

        if ($title && sb_et_woo_li_get_current_account_page() == 'dashboard') {
            echo '<h3 class="module_title">' . $title . '</h3>';
        }

        //wc_print_notices();

        //do_action( 'woocommerce_account_navigation' );

        echo '<div class="woocommerce-MyAccount-content">';
        do_action('woocommerce_account_content');
        echo '</div>';

        $content = ob_get_clean();

        //////////////////////////////////////////////////////////////////////

        if ($content) {
            $output = '<div ' . ($module_id ? 'id="' . esc_attr($module_id) . '"' : '') . ' class="' . $module_class . ' clearfix ' . ($title ? 'has_title' : '') . ' et_pb_text_align_' . $text_orientation . ' et_pb_module et_pb_woo_account_page">' . $content . '</div>';
        }

        return $output;
    }
}

new sb_et_woo_li_account_page_module();

?>