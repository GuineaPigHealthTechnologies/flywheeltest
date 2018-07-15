<?php


class sb_et_woo_li_gallery_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Product Image', 'et_builder');
        $this->slug = 'et_pb_woo_product_image8';

        $this->whitelisted_fields = array(
            'module_id',
            'module_class',
            'image_size',
            'thumb_image_size',
            'thumb_columns',
            'hide_thumbnails',
            'disable_zoom',
            'disable_lightbox',
            'image_class',
            'link_images_to_product',
            'use_overlay',
            'overlay_icon_color',
            'hover_overlay_color',
            'hover_icon',
        );

        $this->fields_defaults = array(
            'et_pb_hide_thumbnails' => 'on'
        , 'use_overlay' => array('off')
        );

        $this->options_toggles = array(
            'general' => array(
                'toggles' => array(
                    'main_settings' => esc_html__('Main Settings', 'et_builder'),
                ),
            ),
        );

        $this->main_css_element = '%%order_class%%';
        $this->advanced_options = array(
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
        $options = $col_options = array();
        $sizes = get_intermediate_image_sizes();

        $options[0] = '- Default -';
        $col_options[0] = '- Default -';

        foreach ($sizes as $size) {
            $options[$size] = $size;
        }

        for ($i = 1; $i <= 6; $i++) {
            $col_options[$i] = $i;
        }


        $fields = array(
            'image_size' => array(
                'label' => __('Image Size', 'et_builder'),
                'type' => 'select',
                'options' => $options,
                'toggle_slug' => 'main_settings',
                'description' => __('Pick a size for the product image from the list. Leave blank for default.', 'et_builder'),
            ),
            'hide_thumbnails' => array(
                'label' => __('Hide Thumbnails?', 'et_builder'),
                'type' => 'yes_no_button',
                'option_category' => 'configuration',
                'options' => array(
                    'off' => __('No', 'et_builder'),
                    'on' => __('Yes', 'et_builder'),
                ),
                'toggle_slug' => 'main_settings',
                'description' => __('If you would like to hide the thumbnails (perhaps to show elsewhere) then select Yes', 'et_builder'),
                'affects' => array(
                    '#et_pb_thumb_image_size'
                , '#et_pb_thumb_columns'
                ),
            ),
            'thumb_image_size' => array(
                'label' => __('Thumbnail Image Size', 'et_builder'),
                'type' => 'select',
                'options' => $options,
                'toggle_slug' => 'main_settings',
                'description' => __('Pick a size for the thumbnail image from the list. Leave blank for default.', 'et_builder'),
                'depends_show_if' => 'off',
            ),
            'thumb_columns' => array(
                'label' => __('Thumbnail Columns', 'et_builder'),
                'type' => 'select',
                'options' => $col_options,
                'toggle_slug' => 'main_settings',
                'description' => __('Pick a number of columns for the thumbnails to occupy. Leave blank for default.', 'et_builder'),
                'depends_show_if' => 'off',
            ),
            'link_images_to_product' => array(
                'label' => __('Link to product page?', 'et_builder'),
                'type' => 'yes_no_button',
                'toggle_slug' => 'main_settings',
                'option_category' => 'configuration',
                'options' => array(
                    'off' => __('No', 'et_builder'),
                    'on' => __('Yes', 'et_builder'),
                ),
                'affects' => array(
                    '#et_pb_disable_zoom'
                    , '#et_pb_disable_lightbox'
                ),
                'description' => __('Should the images link to the product page or stay default?', 'et_builder'),
            ),
            'disable_zoom' => array(
                'label' => __('Disable Zoom?', 'et_builder'),
                'type' => 'yes_no_button',
                'option_category' => 'configuration',
                'depends_show_if'=>'off',
                'options' => array(
                    'off' => __('No', 'et_builder'),
                    'on' => __('Yes', 'et_builder'),
                ),
                'toggle_slug' => 'main_settings',
                'description' => __('If you would like to disable the zoom on this module select yes. You can also do this sitewide in the WLI settings. Doing so will override this setting though.', 'et_builder'),
            ),
            'disable_lightbox' => array(
                'label' => __('Disable Lightbox?', 'et_builder'),
                'type' => 'yes_no_button',
                'depends_show_if'=>'off',
                'option_category' => 'configuration',
                'options' => array(
                    'off' => __('No', 'et_builder'),
                    'on' => __('Yes', 'et_builder'),
                ),
                'toggle_slug' => 'main_settings',
                'description' => __('If you would like to disable the WLI lightbox on this module select yes.', 'et_builder'),
            ),
            'use_overlay' => array(
                'label' => esc_html__('Featured Image Overlay', 'et_builder'),
                'type' => 'yes_no_button',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'options' => array(
                    'off' => esc_html__('Off', 'et_builder'),
                    'on' => esc_html__('On', 'et_builder'),
                ),
                'affects' => array(
                    '#et_pb_overlay_icon_color',
                    '#et_pb_hover_overlay_color',
                    '#et_pb_hover_icon',
                ),
                'description' => esc_html__('If enabled, an overlay color and icon will be displayed when a visitors hovers over the featured image of a post.', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'overlay_icon_color' => array(
                'label' => esc_html__('Overlay Icon Color', 'et_builder'),
                'type' => 'color',
                'custom_color' => true,
                'depends_show_if' => 'on',
                'toggle_slug' => 'main_settings',
                'description' => esc_html__('Here you can define a custom color for the overlay icon', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'hover_overlay_color' => array(
                'label' => esc_html__('Hover Overlay Color', 'et_builder'),
                'type' => 'color-alpha',
                'custom_color' => true,
                'depends_show_if' => 'on',
                'toggle_slug' => 'main_settings',
                'description' => esc_html__('Here you can define a custom color for the overlay', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'hover_icon' => array(
                'label' => esc_html__('Hover Icon Picker', 'et_builder'),
                'type' => 'text',
                'option_category' => 'configuration',
                'class' => array('et-pb-font-icon'),
                'renderer' => 'et_pb_get_font_icon_list',
                'renderer_with_field' => true,
                'depends_show_if' => 'on',
                'toggle_slug' => 'main_settings',
                'description' => esc_html__('Here you can define a custom icon for the overlay', 'et_builder'),
                'toggle_slug' => 'main_settings',
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
            'image_class' => array(
                'label' => esc_html__('Image CSS Class', 'et_builder'),
                'type' => 'text',
                'option_category' => 'configuration',
                'tab_slug' => 'custom_css',
                'option_class' => 'et_pb_custom_css_regular',
                'description'=>'Much like CSS class but this is added directly onto the image rather than the module. Good for lightbox plugins'
            ),
        );

        return $fields;
    }

    function shortcode_callback($atts, $content = null, $function_name)
    {

        global $post;

        if (is_admin()) {
            return;
        }

        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];
        $image_class = $this->shortcode_atts['image_class'];
        $overlay_icon_color = $this->shortcode_atts['overlay_icon_color'];
        $hover_overlay_color = $this->shortcode_atts['hover_overlay_color'];
        $hover_icon = $this->shortcode_atts['hover_icon'];
        $use_overlay = $this->shortcode_atts['use_overlay'];
        $disable_zoom = $this->shortcode_atts['disable_zoom'];
        $disable_lightbox = $this->shortcode_atts['disable_lightbox'];
        $thumb_image_size = $this->shortcode_atts['thumb_image_size'];

        if (!$image_size = $this->shortcode_atts['image_size']) {
            $image_size = 'large';
        }

        $images = $output = '';
        $product_id = sb_et_woo_li_get_id();

        if (!$thumbnail_cols = $this->shortcode_atts['thumb_columns']) {
            $thumbnail_cols = 3;
        }

        if ('' !== $overlay_icon_color) {
            ET_Builder_Element::set_style($function_name, array(
                'selector' => '%%order_class%% .et_overlay:before',
                'declaration' => sprintf(
                    'color: %1$s !important;',
                    esc_html($overlay_icon_color)
                ),
            ));
        }

        if ('' !== $hover_overlay_color) {
            ET_Builder_Element::set_style($function_name, array(
                'selector' => '%%order_class%% .et_overlay',
                'declaration' => sprintf(
                    'background-color: %1$s;',
                    esc_html($hover_overlay_color)
                ),
            ));
        }

        if ('on' === $use_overlay) {
            $data_icon = '' !== $hover_icon
                ? sprintf(
                    ' data-icon="%1$s"',
                    esc_attr(et_pb_process_font_icon($hover_icon))
                )
                : '';

            $overlay_output = sprintf(
                '<a href="' . get_permalink($product_id) . '" class="et_overlay%1$s"%2$s></a>',
                ('' !== $hover_icon ? ' et_pb_inline_icon' : ''),
                $data_icon
            );
        }

        $overlay_class = 'on' === $use_overlay ? ' et_pb_has_overlay' : '';

        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);

        //////////////////////////////////////////////////////////////////////

        $gallery = get_post_meta(sb_et_woo_li_get_id(), '_product_image_gallery', true); //just so we can add the class to the container

        if (!$site_disable_zoom = get_option('sb_et_woo_li_disable_zoom', 0)) {
            if ($disable_zoom) {
                if ($disable_zoom == 'on') {
                    $site_disable_zoom = true;
                }
            }
        }

        if ($this->shortcode_atts['link_images_to_product'] == 'on') {
            $site_disable_zoom = $disable_lightbox = true;
        }

        if ($url = sb_woo_get_product_image($product_id, $image_size)) {
            $images .= '<div class="sb_woo_product_image ' . ($site_disable_zoom ? 'sb_woo_image_disable_zoom':'') . ' ' . ($disable_lightbox ? 'sb_woo_image_disable_lightbox':'') . ' ' . ($this->shortcode_atts['hide_thumbnails'] != 'on' && $gallery ? 'sb_woo_product_image_has_gallery' : '') . '">';

            $large_url = get_the_post_thumbnail_url($product_id, 'full');

            if ($this->shortcode_atts['link_images_to_product'] == 'on') {
                $images .= '<a class="sb-woo-images" rel="sb-woo-images" href="' . get_permalink($product_id) . '">';
            }

            $images .= '<img class="' . $image_class . '" data-large_img="' . $large_url . '" src="' . $url . '" />';

            if ($this->shortcode_atts['link_images_to_product'] == 'on') {
                $images .= '</a>';
            }

            if ('on' === $use_overlay) {
                $images .= $overlay_output;
            }

            ob_start();
            if (is_single()) {
                do_action('sb_et_woo_li_after_product_image');
            } else {
                do_action('sb_et_woo_li_loop_after_product_image');
            }
            $images .= ob_get_clean();

            $images = apply_filters( 'woocommerce_single_product_image_html', $images, get_the_ID());

            $images .= '</div>';
        }

        //gallery images
        if ($gallery && $this->shortcode_atts['hide_thumbnails'] != 'on') {
            $images .= sb_et_woo_li_get_gallery(sb_et_woo_li_get_id(), $thumbnail_cols, $thumb_image_size);
        }
        //end gallery images

        //////////////////////////////////////////////////////////////////////

        if ($images) {
            $output = '<div ' . ($module_id ? 'id="' . esc_attr($module_id) . '"' : '') . ' class="clearfix et_pb_module sb_woo_product_image_container ' . $overlay_class . ' ' . $module_class . '">' . $images . '</div>';
        }

        return $output;
    }
}

new sb_et_woo_li_gallery_module();

?>