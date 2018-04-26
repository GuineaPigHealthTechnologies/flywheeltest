<?php

class sb_et_woo_li_ratings_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Rating', 'et_builder');
        $this->slug = 'et_pb_woo_rating';

        $this->whitelisted_fields = array(
            'title',
            'text_orientation',
            'hide_if_no_ratings',
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
                'text' => array(
                    'label' => esc_html__('Heading', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} h2.module_title",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'stars' => array(
                    'label' => esc_html__('Stars', 'et_builder'),
                    'css' => array(
                        'main' => ".woocommerce {$this->main_css_element}.et_pb_woo_rating .star-rating",
                        'color' => ".woocommerce {$this->main_css_element}.et_pb_woo_rating div.star-rating > span:before",
                    ),
                    'font_size' => array('default' => '16px'),
                    'line_height' => array('default' => '1em'),
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
            'title' => array(
                'label' => __('Title', 'et_builder'),
                'type' => 'text',
                'toggle_slug' => 'main_settings',
                'description' => __('A heading to introduce the star rating (optional)', 'et_builder'),
            ),
            'text_orientation' => array(
                'label' => esc_html__('Text Orientation', 'et_builder'),
                'type' => 'select',
                'toggle_slug' => 'main_settings',
                'option_category' => 'layout',
                'options' => et_builder_get_text_orientation_options(),
                'description' => esc_html__('This controls the how your text is aligned within the module.', 'et_builder'),
            ),
            'hide_if_no_ratings' => array(
                'label' => esc_html__('Hide if no Ratings?', 'et_builder'),
                'type' => 'yes_no_button',
                'toggle_slug' => 'main_settings',
                'options' => array(
                    'off' => esc_html__('No', 'et_builder'),
                    'on' => esc_html__('Yes', 'et_builder'),
                ),
                'description' => esc_html__('If there are no reviews in the system then should this module be hidden?', 'et_builder'),
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

        $title = $this->shortcode_atts['title'];
        $text_orientation = $this->shortcode_atts['text_orientation'];
        $hide_if_no_ratings = $this->shortcode_atts['hide_if_no_ratings'];
        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];
        $content = $output = '';

        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);

        if (is_rtl() && 'left' === $text_orientation) {
            $text_orientation = 'right';
        }

        //////////////////////////////////////////////////////////////////////

        $product = wc_get_product(sb_et_woo_li_get_id());

        $ratings = $product->get_rating_count();

        if ($hide_if_no_ratings == 'off' || ($hide_if_no_ratings == 'on' && $ratings)) {
            $average = $product->get_average_rating();

            $content = '<div class="star-rating">
		                <span style="width:' . (($average / 5) * 100) . '%"><strong class="rating">' . $average . '</strong>' . __('<br/>out of 5', 'woocommerce') . '</span>
                    </div>';
        }

        //////////////////////////////////////////////////////////////////////

        if ($content) {
            $output = '<div ' . ($module_id ? ' id="' . esc_attr($module_id) . '"' : '') . ' class="' . esc_attr('clearfix et_pb_module et_pb_woo_rating et_pb_text_align_' . $text_orientation . ' ' . $module_class) . '">' . ($title ? '<h2 class="module_title">' . $title . '</h2>' : '') . $content . '</div>';
        }

        return $output;
    }
}

new sb_et_woo_li_ratings_module();

?>