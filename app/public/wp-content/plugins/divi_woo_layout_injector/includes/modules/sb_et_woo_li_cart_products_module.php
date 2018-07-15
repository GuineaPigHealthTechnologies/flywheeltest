<?php

class sb_et_woo_li_cart_products_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Cart Products', 'et_builder');
        $this->slug = 'et_pb_woo_cart_products';

        $this->whitelisted_fields = array(
            'title',
            'image_size',
            'remove_link',
            'remove_thumbs',
            'remove_coupon',
            'remove_borders',
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
                'table_text' => array(
                    'label' => esc_html__('Table Text', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} table td, {$this->main_css_element} table th, {$this->main_css_element} table td a",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.4em'),
                ),
            ),
            'button' => array(
                'button' => array(
                    'label' => esc_html__('Button', 'et_builder'),
                    'css' => array(
                        'main' => $this->main_css_element . ' input[type="submit"], ' . $this->main_css_element . ' .et_pb_button.button',
                        'plugin_main' => "{$this->main_css_element}.et_pb_module",
                    ),
                ),
                'buttond' => array(
                    'label' => esc_html__('Button (disabled)', 'et_builder'),
                    'css' => array(
                        'main' => $this->main_css_element . ' input[type="submit"]:disabled',
                        'plugin_main' => "{$this->main_css_element}.et_pb_module",
                    ),
                ),
            ),
            'background' => array(
                'settings' => array(
                    'color' => 'alpha',
                ),
            ),
            'border' => array(
                'css' => array(
                    'important' => 'all',
                ),
            ),
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
            'remove_thumbs' => array(
                'label' => esc_html__('Remove Product Image?', 'et_builder'),
                'type' => 'yes_no_button',
                'toggle_slug' => 'main_settings',
                'options' => array(
                    'off' => esc_html__('No', 'et_builder'),
                    'on' => esc_html__('Yes', 'et_builder'),
                ),
                'affects' => array(
                    '#et_pb_image_size'
                ),
                'description' => 'Should the product image field be removed from the table?',
            ),
            'image_size' => array(
                'label' => esc_html__('Image Size', 'et_builder'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => array(
                    'original' => esc_html__('-- Default --', 'et_builder'),
                    'small' => esc_html__('Small', 'et_builder'),
                    'medium' => esc_html__('Medium', 'et_builder'),
                    'large' => esc_html__('large', 'et_builder'),
                ),
                'depends_show_if' => 'off',
                'toggle_slug' => 'main_settings',
                'description' => esc_html__('Here you can choose the colour of your text. If you are working with a dark background, then your text should be set to light. If you are working with a light background, then your text should be dark.', 'et_builder'),
            ),
            'remove_link' => array(
                'label' => esc_html__('Remove Product Links?', 'et_builder'),
                'type' => 'yes_no_button',
                'toggle_slug' => 'main_settings',
                'options' => array(
                    'off' => esc_html__('No', 'et_builder'),
                    'on' => esc_html__('Yes', 'et_builder'),
                ),
                'description' => 'Should the product links be removed from the product name field?',
            ),
            'remove_coupon' => array(
                'label' => esc_html__('Remove Coupon Form?', 'et_builder'),
                'type' => 'yes_no_button',
                'toggle_slug' => 'main_settings',
                'options' => array(
                    'off' => esc_html__('No', 'et_builder'),
                    'on' => esc_html__('Yes', 'et_builder'),
                ),
                'description' => 'Should the coupon form be removed from the table?',
            ),
            'remove_borders' => array(
                'label' => esc_html__('Remove Borders?', 'et_builder'),
                'type' => 'yes_no_button',
                'toggle_slug' => 'main_settings',
                'options' => array(
                    'off' => esc_html__('No', 'et_builder'),
                    'on' => esc_html__('Yes', 'et_builder'),
                ),
                'description' => 'Should the borders on the table be removed?',
            ),
            /*'remove_company' => array(
                'label' => esc_html__('Remove Company Field?', 'et_builder'),
                'type' => 'yes_no_button',
                'toggle_slug' => 'main_settings',
                'options'         => array(
                    'off' => esc_html__( 'No', 'et_builder' ),
                    'on'  => esc_html__( 'Yes', 'et_builder' ),
                ),
                'description' => 'Should the company field be removed from the form?',
            ),*/
            /*'background_layout' => array(
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
            ),*/
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

        if (is_admin() || !is_cart()) {
            return;
        }

        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];
        $title = $this->shortcode_atts['title'];
        $remove_thumbs = $this->shortcode_atts['remove_thumbs'];
        $remove_link = $this->shortcode_atts['remove_link'];
        $remove_coupon = $this->shortcode_atts['remove_coupon'];
        $remove_borders = $this->shortcode_atts['remove_borders'];
        $image_size = $this->shortcode_atts['image_size'];
        //$remove_company = $this->shortcode_atts['remove_company'];
        /*$background_layout = $this->shortcode_atts['background_layout'];
        $text_orientation = $this->shortcode_atts['text_orientation'];
        $max_width = $this->shortcode_atts['max_width'];
        $max_width_tablet = $this->shortcode_atts['max_width_tablet'];
        $max_width_phone = $this->shortcode_atts['max_width_phone'];*/

        if ($remove_link == 'on') {
            add_filter('woocommerce_cart_item_permalink', '__return_false', 99, 99);
        }

        $output = '';

        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);

        /*if ('' !== $max_width_tablet || '' !== $max_width_phone || '' !== $max_width) {
            $max_width_values = array(
                'desktop' => $max_width,
                'tablet' => $max_width_tablet,
                'phone' => $max_width_phone,
            );

            et_pb_generate_responsive_css($max_width_values, '%%order_class%%', 'max-width', $function_name);
        }*/

        //////////////////////////////////////////////////////////////////////

        //if ($remove_company == 'on') {
        //add_filter('woocommerce_checkout_fields', 'sb_et_woo_li_checkout_remove_company');
        //}

        ob_start();

        if ($title) {
            echo '<h3 class="module_title">' . $title . '</h3>';
        }

        echo '<table class="woocommerce shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
                    <thead>
                    <tr>
                        <th class="product-remove">&nbsp;</th>';

        if ($remove_thumbs != 'on') {
            echo '<th class="product-thumbnail">&nbsp;</th>';
        }

        echo '          <th class="product-name">' . __('Product', 'woocommerce') . '</th>
                        <th class="product-price">' . __('Price', 'woocommerce') . '</th>
                        <th class="product-quantity">' . __('Quantity', 'woocommerce') . '</th>
                        <th class="product-subtotal">' . __('Total', 'woocommerce') . '</th>
                    </tr>
                    </thead>
                    <tbody>';
        do_action('woocommerce_before_cart_contents');

        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
            $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

            if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                echo '<tr class="woocommerce-cart-form__cart-item ' . esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)) . '">';

                echo '<td class="product-remove">';
                echo apply_filters('woocommerce_cart_item_remove_link', sprintf(
                    '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                    esc_url(WC()->cart->get_remove_url($cart_item_key)),
                    __('Remove this item', 'woocommerce'),
                    esc_attr($product_id),
                    esc_attr($_product->get_sku())
                ), $cart_item_key);
                echo '</td>';

                if ($remove_thumbs != 'on') {
                    echo '<td class="product-thumbnail">';
                    $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

                    if (!$product_permalink) {
                        echo $thumbnail;
                    } else {
                        printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail);
                    }
                    echo '</td>';
                }

                echo '<td class="product-name" data-title="' . __('Product', 'woocommerce') . '">';
                if (!$product_permalink) {
                    echo apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;';
                } else {
                    echo apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key);
                }

                // Meta data
                if (function_exists('wc_get_formatted_cart_item_data')) {
                    echo wc_get_formatted_cart_item_data($cart_item);
                } else {
                    echo WC()->cart->get_item_data($cart_item);
                }

                // Backorder notification
                if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                    echo '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>';
                }
                echo '</td>';

                echo '<td class="product-price" data-title="' . __('Price', 'woocommerce') . '">';
                echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                echo '</td>';

                echo '<td class="product-quantity" data-title="' . __('Quantity', 'woocommerce') . '">';

                if ($_product->is_sold_individually()) {
                    $product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
                } else {
                    $product_quantity = woocommerce_quantity_input(array(
                        'input_name' => "cart[{$cart_item_key}][qty]",
                        'input_value' => $cart_item['quantity'],
                        'max_value' => $_product->get_max_purchase_quantity(),
                        'min_value' => '0',
                    ), $_product, false);
                }

                echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item);
                echo '</td>';

                echo '<td class="product-subtotal" data-title="' . __('Total', 'woocommerce') . '">';
                echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
                echo '</td>
                            </tr>';
            }
        }

        do_action('woocommerce_cart_contents');

        echo '<td colspan="6" class="actions">';

        if (wc_coupons_enabled() && $remove_coupon != 'on') {
            echo '<div class="coupon">';
            echo '<label for="coupon_code">' . __('Coupon:', 'woocommerce') . '</label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="' . __('Coupon code', 'woocommerce') . '" /> <input type="submit" class="button" name="apply_coupon" value="' . __('Apply coupon', 'woocommerce') . '" />';
            do_action('woocommerce_cart_coupon');
            echo '</div>';
        }

        echo '<input type="submit" class="button et_pb_button" name="update_cart" value="' . __('Update cart', 'woocommerce') . '" />';

        do_action('woocommerce_cart_actions');

        wp_nonce_field('woocommerce-cart');
        echo '</td>
                    </tr>';

        do_action('woocommerce_after_cart_contents');
        echo '</tbody>
                </table>';
        do_action('woocommerce_after_cart_table');
        do_action('woocommerce_after_cart');

        $content = ob_get_clean();

        //////////////////////////////////////////////////////////////////////

        if ($content) {
            $output = '<div ' . ($module_id ? 'id="' . esc_attr($module_id) . '"' : '') . ' class="' . $module_class . ' clearfix ' . ($title ? 'has_title' : '') . ($image_size ? ' image_size_' . $image_size : '') . ($remove_borders == 'on' ? ' hide-borders ' : '') . ' et_pb_module et_pb_woo_cart_products">' . $content . '</div>';
        }

        return $output;
    }
}

new sb_et_woo_li_cart_products_module();

?>