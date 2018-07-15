<?php

class sb_et_woo_li_thumbnail_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Thumbnails', 'et_builder');
        $this->slug = 'et_pb_woo_thumbs5';

        $this->whitelisted_fields = array(
            'module_id',
            'module_class',
            'admin_label',
            'thumb_image_size',
            'thumb_columns',
            'limit',
            'offset',
        );

        $this->options_toggles = array(
            'general' => array(
                'toggles' => array(
                    'main_settings' => esc_html__('Main Settings', 'et_builder'),
                ),
            ),
        );

        $this->fields_defaults = array();
        $this->main_css_element = '.et_pb_woo_thumbs';
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
            'thumb_image_size' => array(
                'label' => __('Thumbnail Image Size', 'et_builder'),
                'type' => 'select',
                'options' => $options,
                'toggle_slug' => 'main_settings',
                'description' => __('Pick a size for the thumbnail image from the list. Leave blank for default.', 'et_builder'),
            ),
            'thumb_columns' => array(
                'label' => __('Thumbnail Columns', 'et_builder'),
                'type' => 'select',
                'options' => $col_options,
                'toggle_slug' => 'main_settings',
                'description' => __('Pick a number of columns for the thumbnails to occupy. Leave blank for default.', 'et_builder'),
            ),
            'limit' => array(
                'label' => esc_html__('Number of Thumbnails', 'et_builder'),
                'type' => 'text',
                'toggle_slug' => 'main_settings',
                'description' => __('How many thumbnails to show. Defaults to all but you can limit the number and show the rest elsewhere on the page using the "offset" setting below', 'et_builder'),
            ),
            'offset' => array(
                'label' => esc_html__('Thumbnail Number Offset', 'et_builder'),
                'type' => 'text',
                'toggle_slug' => 'main_settings',
                'description' => __('This works in conjunction with the number of thumbnails setting above. If you had, for example, 5 thumbnails and wanted to show 2 in one location and 3 in another you would set the limit to 2 in one module and the limit to 3 with an offset of 2 in the second.', 'et_builder'),
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

        $content = $output = '';
        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];
        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);

        if (!$thumbnail_cols = $this->shortcode_atts['thumb_columns']) {
            $thumbnail_cols = 3;
        }
        if (!$thumb_image_size = $this->shortcode_atts['thumb_image_size']) {
            $thumb_image_size = 'large';
        }
        if (!$limit = $this->shortcode_atts['limit']) {
            $limit = 0;
        }
        if (!$offset = $this->shortcode_atts['offset']) {
            $offset = 0;
        }

        //////////////////////////////////////////////////////////////////////

        if ($thumbs = sb_et_woo_li_get_gallery(sb_et_woo_li_get_id(), $thumbnail_cols, $thumb_image_size, $limit, $offset)) {
            $content .= $thumbs;
        }

        //////////////////////////////////////////////////////////////////////

        $overlay_class = '';

        if ($content) {
            $output = '<div ' . ($module_id ? 'id="' . esc_attr($module_id) . '"' : '') . ' class="' . $module_class . ' ' . $overlay_class . ' clearfix et_pb_module et_pb_woo_thumbs5 et_pb_woo_thumbs">' . $content . '</div>';
        }

        return $output;
    }
}

new sb_et_woo_li_thumbnail_module();

?>