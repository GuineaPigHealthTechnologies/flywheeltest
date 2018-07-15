<?php

function sb_et_woo_li_submenu_tabs()
{
    $i = 0;

    echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
    echo '<h2>Woo Layout Injector - V' . SB_ET_WOO_LI_VERSION . ' - Tab Manager</h2>';

    echo '<div id="poststuff">';

    echo '<div id="post-body" class="metabox-holder columns-2">';

    if (isset($_POST['sb_et_woo_li_tabs_submit'])) {
        if (isset($_POST['sb_et_woo_li_tabs'])) {
            foreach ($_POST['sb_et_woo_li_tabs'] as $i => $tab) {
                if (!$tab['label']) {
                    unset($_POST['sb_et_woo_li_tabs'][$i]);
                } else if (!trim($tab['name'])) {
                    $_POST['sb_et_woo_li_tabs'][$i]['name'] = sanitize_title(str_replace(' ', '_', strtolower($tab['label'])));
                }
            }
        }

        //print_r($_POST['sb_et_woo_li_tabs']);

        update_option('sb_et_woo_li_tabs', @$_POST['sb_et_woo_li_tabs']);

        echo '<div id="message" class="updated fade"><p>Tabs updated successfully</p></div>';
    }

    $tabs = sb_et_woo_li_get_tabs();

    echo '<p>This page is part of the Woo Layout Injector. It allows you to add tabs to the Woo Tabs module within the Divi Builder. Each tab name you add below will add a new WYSIWYG (editor) to the product edit pages (towards the bottom). When populated it will show in the tab system. When empty it won\'t show so don\'t worry about adding as many tabs as you like.</p>';

    echo '<form method="POST">';

    echo '<div style="clear: both;">
            <p id="submit"><input type="submit" name="sb_et_woo_li_tabs_submit" class="button-primary" value="Save Tabs" /></p>
          </div>';

    echo sb_et_woo_li_box_start('Tab Settings');

    echo '<p>To add a tab simply enter the title above and save. The page will refresh, your selection will be saved and you can add more as necessary.</p>';

    echo '<table class="form-table widefat">
            <thead>
            <tr>
                <td><strong>Title</strong></td>
                <td><strong>Name</strong></td>
                <td>&nbsp;</td>
            </tr>
            </thead>
            <tbody>';

    foreach ($tabs as $i => $tab) {
        echo '<tr>
                <td><input type="text" style="width: 300px;" name="sb_et_woo_li_tabs[' . $i . '][label]" value="' . $tab['label'] . '" /></td>
                <td><input type="text" style="width: 300px;" name="sb_et_woo_li_tabs[' . $i . '][name]" value="' . $tab['name'] . '" /></td>
                <td><a style="cursor: pointer;" onclick="if (confirm(\'If you remove this tab the data will remain in the database but the tabs will not show. To get the tab back just create a new tab with the same name and it will return. Once complete just hit save to confirm the delete.\')) { jQuery(this).closest(\'tr\').remove(); }">Delete Tab</a></td>
              </tr>';
    }

    $i++; //increment by 1 from above for the empty row

    echo '<tr>
            <td><input type="text" style="width: 300px;" name="sb_et_woo_li_tabs[' . $i . '][label]" value="" /></td>
            <td><em>(This will be generated automatically)</em></td>
            <td>&nbsp;</td>
          </tr>';

    echo '</tbody>
        </table>';

    echo sb_et_woo_li_box_end();

    echo '<div style="clear: both;">
            <p id="submit"><input type="submit" name="sb_et_woo_li_tabs_submit" class="button-primary" value="Save Tabs" /></p>
          </div>';

    echo '</form>';

    echo '</div>';

    echo '</div>';
    echo '</div>';
}

function sb_et_woo_li_get_tabs()
{
    return get_option('sb_et_woo_li_tabs', array());
}

function sb_et_woo_li_meta_box_tab_content($post)
{
    wp_nonce_field(plugin_basename(__FILE__), 'sb_et_woo_li_noncename');

    $sb_et_woo_li_tabs = sb_et_woo_li_get_tabs();

    foreach ($sb_et_woo_li_tabs as $i => $tab) {
        $value = get_post_meta($post->ID, '_' . $tab['name'], true);

        echo '<p><label for="sb_et_woo_li_new_field"><strong>' . $tab['label'] . ' ' . __("Content", 'sb_awt') . '</strong></label></p>';

        $settings = array(
            'quicktags' => array('buttons' => 'em,strong,link'),
            'textarea_name' => $tab['name'],
            'quicktags' => true,
            'tinymce' => true,
            'editor_css' => '<style>#wp-' . $tab['name'] . '-editor-container .wp-editor-area{height:175px; width:100%;}</style>'
        );

        wp_editor(htmlspecialchars_decode($value), $tab['name'], $settings);

    }
}

/* When the post is saved, saves our custom data */
function sb_et_woo_li_save_tab_postdata($post_id)
{
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (!isset($_POST['sb_et_woo_li_noncename']) || !wp_verify_nonce($_POST['sb_et_woo_li_noncename'], plugin_basename(__FILE__))) {
        return;
    }

    $sb_et_woo_li_tabs = sb_et_woo_li_get_tabs();

    foreach ($sb_et_woo_li_tabs as $i => $tab) {
        $tab_content = $_POST[$tab['name']];
        update_post_meta($post_id, '_' . $tab['name'], $tab_content);
    }
}

function sb_et_woo_li_tab_content($tab_name)
{

    $sb_et_woo_li_tabs = sb_et_woo_li_get_tabs();

    foreach ($sb_et_woo_li_tabs as $i => $tab) {
        if ($tab['name'] == $tab_name) {

            if ($content = get_post_meta(get_the_ID(), '_' . $tab_name, true)) {
                echo do_shortcode($content);
            }

            break;
        }
    }

}

function sb_et_woo_li_new_tabs($tabs)
{
    $base_order = 100;
    $sb_et_woo_li_tabs = sb_et_woo_li_get_tabs();

    foreach ($sb_et_woo_li_tabs as $i => $tab) {

        if ($content = get_post_meta(get_the_ID(), '_' . $tab['name'], true)) {
            $tabs[$tab['name']] = array(
                'title' => __($tab['label'], 'woocommerce'),
                'priority' => ($i * $base_order),
                'callback' => 'sb_et_woo_li_tab_content'
            );
        }

    }

    return $tabs;
}

?>