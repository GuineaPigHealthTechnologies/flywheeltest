<?php

class sb_et_woo_li_single_product extends ET_Builder_Module
{
    function init()
    {
        $this->name = esc_html__('Woo Single Product', 'et_builder');
        $this->slug = 'et_pb_woo_single_product';

        $this->whitelisted_fields = array(
            'title',
            'product',
            'use_loop_layout',
            'loop_layout',
            'show_atc',
            'show_price',
            'show_image',
            'show_title',
            'show_rm',
            'show_rm_label',
            'admin_label',
            'module_id',
            'module_class',
        );

        $this->main_css_element = '%%order_class%%';

        $this->options_toggles = array(
            'general' => array(
                'toggles' => array(
                    'main_settings' => esc_html__('Main Settings', 'et_builder'),
                ),
            ),
        );

        $this->fields_defaults = array();

        $this->advanced_options = array(
            'fonts' => array(
                /*'text' => array(
                    'label' => esc_html__('Text', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} p",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.5em'),
                ),*/
                'price' => array(
                    'label' => esc_html__('Price', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} .price .woocommerce-Price-amount.amount",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'buttons' => array(
                    'label' => esc_html__('Read More', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} .et_pb_button.woo_li_read_more",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'headings' => array(
                    'label' => esc_html__('Product Title', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} .products .product .woocommerce-LoopProduct-link h2.woocommerce-loop-product__title",
                    ),
                    'font_size' => array('default' => '30px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'title' => array(
                    'label' => esc_html__('Module Title', 'et_builder'),
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

    }

    function get_fields()
    {
        $options = $products = array();

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

        $yes_no = array('yes' => 'Yes', 'no' => 'No');

        if ($layouts = get_posts($layout_query)) {
            foreach ($layouts as $layout) {
                $options[$layout->ID] = $layout->post_title;
            }
        }

        if ($product_array = get_posts(array('post_type' => 'product', 'posts_per_page' => SB_ET_WOO_LI_PRODUCT_COUNT))) {
            foreach ($product_array as $product_obj) {
                $products[$product_obj->ID] = $product_obj->post_title;
            }
        }

        $fields = array(
            'title' => array(
                'label' => __('Title', 'et_builder'),
                'type' => 'text',
                'toggle_slug' => 'main_settings',
                'description' => __('If you want a title on this module then use this box and an H2 will be added above the product image. Otherwise leave this blank.', 'et_builder'),
            ),
            'product' => array(
                'label' => esc_html__('Product', 'et_builder'),
                'type' => 'select',
                'toggle_slug' => 'main_settings',
                'options' => $products,
                'description' => esc_html__('Which product should be shown?', 'et_builder'),
            ),
            'use_loop_layout' => array(
                'label' => esc_html__('Use Loop Layout', 'et_builder'),
                'type' => 'yes_no_button',
                'toggle_slug' => 'main_settings',
                'options' => array(
                    'off' => esc_html__('No', 'et_builder'),
                    'on' => esc_html__('Yes', 'et_builder'),
                ),
                'affects' => array(
                    '#et_pb_loop_layout',
                    '#et_pb_show_price',
                    '#et_pb_show_image',
                    '#et_pb_show_title',
                    '#et_pb_show_atc',
                    '#et_pb_show_rm',
                ),
                'description' => esc_html__('If you use a loop layout then you can select a layout from the Divi Library to power this module. If not the default layout will be used.', 'et_builder'),
            ),
            'loop_layout' => array(
                'label' => esc_html__('Loop Layout', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'depends_show_if' => 'on',
                'options' => $options,
                'description' => esc_html__('Choose a layout to use for this product', 'et_builder'),
            ),
            'show_price' => array(
                'label' => esc_html__('Show Price?', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'depends_show_if' => 'off',
                'options' => $yes_no,
                'description' => esc_html__('Should we show the price to the user in this module', 'et_builder'),
            ),
            'show_atc' => array(
                'label' => esc_html__('Show Add to Cart?', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'depends_show_if' => 'off',
                'options' => $yes_no,
                'description' => esc_html__('Should we show the ATC button to the user in this module', 'et_builder'),
            ),
            'show_image' => array(
                'label' => esc_html__('Show Images?', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'depends_show_if' => 'off',
                'options' => $yes_no,
                'description' => esc_html__('Should we show the images to the user in this module', 'et_builder'),
            ),
            'show_title' => array(
                'label' => esc_html__('Show Product Title?', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'depends_show_if' => 'off',
                'options' => $yes_no,
                'description' => esc_html__('Should we show the title to the user in this module', 'et_builder'),
            ),
            'show_rm' => array(
                'label' => esc_html__('Show Read More?', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'depends_show_if' => 'off',
                'options' => $yes_no,
                'affects' => array(
                    '#et_pb_show_rm_label',
                ),
                'description' => esc_html__('Should we show a read more button to the user in this module', 'et_builder'),
            ),
            'show_rm_label' => array(
                'label' => __('Read More Label', 'et_builder'),
                'type' => 'text',
                'toggle_slug' => 'main_settings',
                'depends_show_if' => 'yes',
                'description' => __('To edit the text on the read more button type it here. Defaults to "Find Out More" if left blank', 'et_builder'),
            ),
            'admin_label' => array(
                'label' => esc_html__('Admin Label', 'et_builder'),
                'type' => 'text',
                'description' => esc_html__('This will change the label of the module in the builder for easy identification.', 'et_builder'),
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

        $use_loop_layout = $this->shortcode_atts['use_loop_layout'];
        $loop_layout = $this->shortcode_atts['loop_layout'];
        $title = $this->shortcode_atts['title'];
        $the_product = $this->shortcode_atts['product'];
        $show_atc = $this->shortcode_atts['show_atc'];
        $show_price = $this->shortcode_atts['show_price'];
        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];
        $content = $output = '';
        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);
        $show_read_more = $this->shortcode_atts['show_rm'];
        $show_img = $this->shortcode_atts['show_image'];
        $show_title = $this->shortcode_atts['show_title'];

        if (!$rm_label = $this->shortcode_atts['show_rm_label']) {
            $rm_label = 'Find Out More';
        }

        //$this_id = sb_et_woo_li_get_id();
        $the_product = get_post($the_product); //convert product id to post object

        //'woocommerce_shortcode_before_product_loop'

        if ($the_product) {

            if ($use_loop_layout == 'on') {
                sb_et_woo_li_remove_actions(true); //we want to clear the default woo actions to add our own
                sb_et_woo_li_set_id($the_product->ID); //set the $product array and the override globals

                $content = do_shortcode('[et_pb_section global_module="' . $loop_layout . '"][/et_pb_section]');

                sb_et_woo_li_clear_id();
            } else {
                if ($show_atc == 'yes') {
                    add_action('woocommerce_shortcode_after_product_loop', 'woocommerce_template_single_add_to_cart', 10); //add atc
                }
                if ($show_img == 'no') {
                    remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
                }
                if ($show_title == 'no') {
                    remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
                }
                if ($show_price == 'no') {
                    remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10); //remove price
                }

                $content = do_shortcode('[product id="' . $the_product->ID . '"]');

                if ($show_read_more == 'yes') {
                    $content .= '<a class="et_pb_button woo_li_read_more" href="' . get_permalink($the_product->ID) . '">' . __($rm_label, 'woo_li') . '</a>';
                }
                if ($show_img == 'no') {
                    add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
                }
                if ($show_title == 'no') {
                    add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
                }
                if ($show_price == 'no') {
                    add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10); //add price again
                }
                if ($show_atc == 'yes') {
                    remove_action('woocommerce_shortcode_after_product_loop', 'woocommerce_template_single_add_to_cart', 10); //remove atc
                }
            }
        }

        wp_reset_postdata();

        if ($content) {
            $output = (!defined('SB_ET_WOO_LI_NOTICES_SHOWN') ? do_shortcode('[shop_messages]') : '') . '<div class="clearfix et_pb_woo_single_product et_pb_module ' . $module_class . '" ' . ($module_id ? 'id="' . $module_id . '"' : '') . '>' . ($title ? '<h2 class="module_title">' . $title . '</h2>' : '') . $content . '</div>';
        }

        if (!defined('SB_ET_WOO_LI_NOTICES_SHOWN')) {
            define('SB_ET_WOO_LI_NOTICES_SHOWN', 1);
        }

        return $output;
    }
}

new sb_et_woo_li_single_product;

?>