<?php

class sb_et_woo_li_attribute_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Attributes', 'et_builder');
        $this->slug = 'et_pb_woo_attribute';

        $this->whitelisted_fields = array(
            'title',
            'background_layout',
            'attribute',
            'show_list',
            'separator',
            'module_id',
            'module_class',
            'prefix'
        );

        $this->fields_defaults = array();
        $this->main_css_element = '%%order_class%%';

        $this->custom_css_options = array(
            'attribute_text' => array(
                'label' => __('Attribute Text', 'et_builder'),
                'selector' => '.term-item',
            ),
            'term_prefix' => array(
                'label' => __('Attribute Prefix/Title', 'et_builder'),
                'selector' => '.term-prefix',
            ),
            'sb_woo_attribute_term_list' => array(
                'label' => __('Attribute Container', 'et_builder'),
                'selector' => '.sb_woo_attribute_term_list',
            ),
        );

        $this->options_toggles = array(
            'general' => array(
                'toggles' => array(
                    'main_settings' => esc_html__('Main Settings', 'et_builder'),
                ),
            ),
        );

        $this->advanced_options = array(
            'fonts' => array(
                'cntnt' => array(
                    'label' => esc_html__('Attribute Text', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} .term-item",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'prefix' => array(
                    'label' => esc_html__('Attribute Prefix', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} .sb_woo_attribute_term_list .term-prefix",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'headings' => array(
                    'label' => esc_html__('Heading/Prefix', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} h3",
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
        $attr = sb_et_woo_li_get_attributes();

        $fields = array(
            'admin_label' => array(
                'label' => __('Admin Label', 'et_builder'),
                'type' => 'text',
                'option_category' => 'layout',
                'description' => __('This will change the label of the module in the builder for easy identification.', 'et_builder'),
            ),
            'title' => array(
                'label' => __('Title', 'et_builder'),
                'type' => 'text',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'description' => __('If you want to include a title then use this setting and a heading will be added above the list of attributes', 'et_builder'),
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
            'prefix' => array(
                'label' => __('Prefix', 'et_builder'),
                'type' => 'text',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'description' => __('A string of text to be added immediately before the list of attributes. Can be used for lead in text or a slightly more subtle title.', 'et_builder'),
            ),
            'attribute' => array(
                'label' => esc_html__('Attribute', 'et_builder'),
                'type' => 'select',
                'options' => $attr,
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'description' => 'Which attribute should the system show? This will display a list of attributes if the product has them. If not the module wiull be hidden'
            ),
            'show_list' => array(
                'label' => __('Show as Bullet List?', 'et_builder'),
                'type' => 'yes_no_button',
                'toggle_slug' => 'main_settings',
                'options' => array(
                    'off' => __('No', 'et_builder'),
                    'on' => __('Yes', 'et_builder'),
                ),
                'affects' => array(
                    '#et_pb_separator'
                ),
                'description' => __('Should the attributes for this product be presented as a bullet point list?', 'et_builder'),
            ),
            'separator' => array(
                'label' => esc_html__('Separator', 'et_builder'),
                'type' => 'text',
                'option_category' => 'layout',
                'depends_show_if' => 'off',
                'toggle_slug' => 'main_settings',
                'description' => 'When there is more than one term to display what should separate them. Eg | or ,',
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

        //print_r($fields);

        return $fields;
    }

    function shortcode_callback($atts, $content = null, $function_name)
    {
        if (is_admin()) {
            return;
        }

        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];
        $title = $this->shortcode_atts['title'];
        $show_list = $this->shortcode_atts['show_list'];
        $prefix = $this->shortcode_atts['prefix'];
        $attribute = $this->shortcode_atts['attribute'];
        $separator = $this->shortcode_atts['separator'];
        $background_layout = $this->shortcode_atts['background_layout'];
        $output = $content = '';
        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);

        //////////////////////////////////////////////////////////////////////

        $product_terms = wp_get_object_terms(sb_et_woo_li_get_id(), $attribute);
        $term_array = array();

        if (!empty($product_terms)) {
            if (!is_wp_error($product_terms)) {
                foreach ($product_terms as $term) {
                    $term_array[] = ($show_list == 'on' ? '<li>':'') . '<span class="term-item ' . $term->slug . '">' . $term->name . '</span>' . ($show_list == 'on' ? '</li>':'');
                }

            }
        } else {
            if ($attr2 = get_post_meta(sb_et_woo_li_get_id(), '_product_attributes', true)) {
                $no_pa = substr($attribute, 3);

                if (isset($attr2[$no_pa]['value'])) {
                    if ($attr3 = $attr2[$no_pa]['value']) {

                        $term_array[] = ($show_list == 'on' ? '<li>':'') . '<span class="term-item">' . $attr3 . '</span>' . ($show_list == 'on' ? '</li>':'');
                    }
                }
            }
        }

        //////////////////////////////////////////////////////

        if (count($term_array) > 0) {
            if ($title) {
                $content .= '<h3>' . $title . '</h3>';
            }

            $content .= '<span class="sb_woo_attribute_term_list">';

            if ($prefix) {
                $content .= '<span class="term-prefix">' . $prefix . '</span>';
            }

            if ($show_list == 'on') {
                $content = '<ul class="wli_bullet_list wli_attribute_list">' . implode("\n", $term_array) . '</ul>';
            } else {
                $content .= wpautop(implode($separator, $term_array));
            }

            $content .= '</span>';
        }

        //////////////////////////////////////////////////////////////////////

        if ($content) {
            $output = sprintf(
                '<div%5$s class="et_pb_woo_attribute et_pb_bg_layout_' . $background_layout . ' %1$s%3$s%6$s">
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

new sb_et_woo_li_attribute_module();

?>