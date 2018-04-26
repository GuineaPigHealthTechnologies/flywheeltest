<?php

class sb_et_woo_li_notices_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Notices', 'et_builder');
        $this->slug = 'et_pb_woo_notices';

        $this->whitelisted_fields = array(
            'module_id',
            'module_class',
        );

        /*$this->options_toggles = array(
            'general' => array(
                'toggles' => array(
                    'main_settings' => esc_html__('Main Settings', 'et_builder'),
                ),
            ),
        );*/

        $this->main_css_element = '%%order_class%%';

    }

    function get_fields()
    {
        $fields = array(
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
        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);
        $output = '';

        //////////////////////////////////////////////////////////////////////

        ob_start();
        wc_print_notices();
        $content = ob_get_clean();

        //////////////////////////////////////////////////////////////////////

        if ($content) {
            $output = '<div class="clearfix et_pb_woo_notices et_pb_module ' . $module_class . '" ' . ($module_id ? 'id="' . $module_id . '"' : '') . '>' . $content . '</div>';
        }

        return $output;
    }
}

new sb_et_woo_li_notices_module();

?>