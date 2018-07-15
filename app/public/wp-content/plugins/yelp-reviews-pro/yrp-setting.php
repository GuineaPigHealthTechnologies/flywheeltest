<?php

if (!current_user_can('manage_options')) {
    die('The account you\'re logged in to doesn\'t have permission to access this page.');
}

function yrp_has_valid_nonce() {
    $nonce_actions = array('yrp_reset', 'yrp_settings', 'yrp_active');
    $nonce_form_prefix = 'yrp-form_nonce_';
    $nonce_action_prefix = 'yrp-wpnonce_';
    foreach ($nonce_actions as $key => $value) {
        if (isset($_POST[$nonce_form_prefix.$value])) {
            check_admin_referer($nonce_action_prefix.$value, $nonce_form_prefix.$value);
            return true;
        }
    }
    return false;
}

function yrp_debug() {
    global $wpdb;
    $businesses = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "yrw_yelp_business");
    $businesses_error = $wpdb->last_error;
    $reviews = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "yrw_yelp_review");
    $reviews_error = $wpdb->last_error; ?>

DB Businesses: <?php echo print_r($businesses); ?>

DB Businesses error: <?php echo $businesses_error; ?>

DB Reviews: <?php echo print_r($reviews); ?>

DB Reviews error: <?php echo $reviews_error;
}

if (!empty($_POST)) {
    $nonce_result_check = yrp_has_valid_nonce();
    if ($nonce_result_check === false) {
        die('Unable to save changes. Make sure you are accessing this page from the Wordpress dashboard.');
    }
}

// Reset
if (isset($_POST['reset'])) {
    yrp_reset(isset($_POST['reset_db']));
    unset($_POST);
    ?>
    <div class="wrap">
        <h3><?php echo yrp_i('Yelp Reviews Pro Reset'); ?></h3>
        <form method="POST" action="?page=yrp">
            <?php wp_nonce_field('yrp-wpnonce_yrp_reset', 'yrp-form_nonce_yrp_reset'); ?>
            <p><?php echo yrp_i('Yelp Reviews Pro has been reset successfully.') ?></p>
            <ul style="list-style: circle;padding-left:20px;">
                <li><?php echo yrp_i('Local settings for the plugin were removed.') ?></li>
            </ul>
            <p>
                <?php echo yrp_i('If you wish to reinstall, you can do that now.') ?>
                <a href="?page=yrp">&nbsp;<?php echo yrp_i('Reinstall') ?></a>
            </p>
        </form>
    </div>
    <?php
    die();
}

// Validate, sanitize, escape

if (isset($_POST['yrp_active']) && isset($_GET['yrp_active'])) {
    update_option('yrp_active', ($_GET['yrp_active'] == '1' ? '1' : '0'));
}

if (isset($_POST['yrp_setting'])) {
    update_option('yrp_expired', '');
    update_option('yrp_license', $_POST['yrp_license']);
    update_option('yrp_language', $_POST['yrp_language']);
    update_option('yrp_api_key', trim(sanitize_text_field($_POST['yrp_api_key'])));
    $yrp_setting_page = true;
} else {
    $yrp_setting_page = false;
}

if (isset($_POST['yrp_install_db'])) {
    yrp_install_db();
}

wp_register_style('twitter_bootstrap3_css', plugins_url('/static/css/bootstrap.min.css', __FILE__));
wp_enqueue_style('twitter_bootstrap3_css', plugins_url('/static/css/bootstrap.min.css', __FILE__));

wp_register_style('rplg_wp_css', plugins_url('/static/css/rplg-wp.css', __FILE__));
wp_enqueue_style('rplg_wp_css', plugins_url('/static/css/rplg-wp.css', __FILE__));

wp_register_style('rplg_setting_css', plugins_url('/static/css/rplg-setting.css', __FILE__));
wp_enqueue_style('rplg_setting_css', plugins_url('/static/css/rplg-setting.css', __FILE__));

wp_enqueue_script('jquery');

wp_register_script('yrp_wpac_js', plugins_url('/static/js/wpac.js', __FILE__));
wp_enqueue_script('yrp_wpac_js', plugins_url('/static/js/wpac.js', __FILE__));

wp_register_script('yrp_finder_js', plugins_url('/static/js/yrw-finder.js', __FILE__));
wp_localize_script('yrp_finder_js', 'finderVars', array(
    'YRP_AVATAR' => YRP_AVATAR,
    'handlerUrl' => admin_url('options-general.php?page=yrp'),
    'actionPrefix' => 'yrp'
));
wp_enqueue_script('yrp_finder_js', plugins_url('/static/js/yrw-finder.js', __FILE__));

$yrp_enabled = get_option('yrp_active') == '1';
$yrp_api_key = get_option('yrp_api_key');
$yrp_language = get_option('yrp_language');

$yrp_license = get_option('yrp_license');
$yrp_expired = get_option('yrp_expired');

if (strlen($yrp_license) > 1 && strlen($yrp_expired) < 1) {
    $request = wp_remote_post('https://api.richplugins.com/plugins/license-expired', array(
        'timeout' => 15,
        'sslverify' => false,
        'body' => array(
            'license' => $yrp_license,
            'slug' => 'yrp',
            'plugin' => 'Yelp Reviews Pro',
            'active' => '1',
            'siteurl' => get_option('siteurl')
        )
    ));

    if (!is_wp_error($request)) {
        $request = json_decode(wp_remote_retrieve_body($request));
    }
    if ($request && isset($request->expired)) {
        $yrp_expired = $request->expired;
        update_option('yrp_expired', $request->expired);
    } else {
        if (isset($request->error)) {
            $yrp_license_error = $request->error;
        }
        update_option('yrp_expired', 'false');
    }
}

if (isset($_POST['yrp_license_deactivate'])) {
    $request = wp_remote_post('https://api.richplugins.com/plugins/license-expired', array(
        'timeout' => 15,
        'sslverify' => false,
        'body' => array(
            'license' => $yrp_license,
            'slug' => 'yrp',
            'plugin' => 'Yelp Reviews Pro',
            'active' => '0'
        )
    ));

    if (!is_wp_error($request)) {
        $request = json_decode(wp_remote_retrieve_body($request));
    }
    if ($request && isset($request->error)) {
        $yrp_license_error = $request->error;
    } else {
        $yrp_license_deactivated = $yrp_license;
        update_option('yrp_expired', '');
        update_option('yrp_license', '');
        $yrp_expired = '';
        $yrp_license = '';
        $yrp_setting_page = true;
    }
}
?>

<span class="version pro"><?php echo yrp_i('Pro Version: %s', esc_html(YRP_VERSION)); ?></span>
<div class="yrw-setting container-fluid">
    <img src="<?php echo YRP_PLUGIN_URL . '/static/img/yelp-logo.png'; ?>" alt="Yelp" style="height:45px">
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation"<?php if (!$yrp_setting_page) { ?> class="active"<?php } ?>>
            <a href="#about" aria-controls="about" role="tab" data-toggle="tab"><?php echo yrp_i('About'); ?></a>
        </li>
        <li role="presentation"<?php if ($yrp_setting_page) { ?> class="active"<?php } ?>>
            <a href="#setting" aria-controls="setting" role="tab" data-toggle="tab"><?php echo yrp_i('Setting'); ?></a>
        </li>
        <li role="presentation">
            <a href="#shortcode" aria-controls="shortcode" role="tab" data-toggle="tab"><?php echo yrp_i('Shortcode Builder'); ?></a>
        </li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane<?php if (!$yrp_setting_page) { ?> active<?php } ?>" id="about">
            <div class="row">
                <div class="col-sm-6">
                    <h4><?php echo yrp_i('Yelp Reviews Pro for WordPress'); ?></h4>
                    <p><?php echo yrp_i('Yelp Reviews plugin is an easy and fast way to integrate Yelp business reviews right into your WordPress website. This plugin works instantly and keep all Yelp businesses and reviews in WordPress database thus it has no depend on external services.'); ?></p>
                    <p>To use Yelp Reviews Pro, please do follow:</p>
                    <ol>
                        <li>Go to menu <b>"Appearance"</b> -> <b>"Widgets"</b></li>
                        <li>Move "Yelp Reviews Pro" widget to sidebar</li>
                        <li>Enter 'Search Term' and 'Location' and click 'Search Business'</li>
                        <li>Select your found business in the panel below and click 'Save Business and Reviews'</li>
                        <li>'Business ID' must be filled, if so click 'Save' widget button</li>
                    </ol>
                    <p><?php echo yrp_i('Feel free to contact us by email <a href="mailto:support@richplugins.com">support@richplugins.com</a>.'); ?></p>
                    <p><?php echo yrp_i('<b>Like this plugin? Give it a like on social:</b>'); ?></p>
                    <div class="row">
                        <div class="col-sm-4">
                            <div id="fb-root"></div>
                            <script>(function(d, s, id) {
                              var js, fjs = d.getElementsByTagName(s)[0];
                              if (d.getElementById(id)) return;
                              js = d.createElement(s); js.id = id;
                              js.src = "//connect.facebook.net/en_EN/sdk.js#xfbml=1&version=v2.6&appId=1501100486852897";
                              fjs.parentNode.insertBefore(js, fjs);
                            }(document, 'script', 'facebook-jssdk'));</script>
                            <div class="fb-like" data-href="https://richplugins.com/" data-layout="button_count" data-action="like" data-show-faces="true" data-share="false"></div>
                        </div>
                        <div class="col-sm-4 twitter">
                            <a href="https://twitter.com/richplugins" class="twitter-follow-button" data-show-count="false">Follow @RichPlugins</a>
                            <script>!function (d, s, id) {
                                    var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                                    if (!d.getElementById(id)) {
                                        js = d.createElement(s);
                                        js.id = id;
                                        js.src = p + '://platform.twitter.com/widgets.js';
                                        fjs.parentNode.insertBefore(js, fjs);
                                    }
                                }(document, 'script', 'twitter-wjs');</script>
                        </div>
                        <div class="col-sm-4 googleplus">
                            <div class="g-plusone" data-size="medium" data-annotation="inline" data-width="200" data-href="https://plus.google.com/101080686931597182099"></div>
                            <script type="text/javascript">
                                window.___gcfg = { lang: 'en-US' };
                                (function () {
                                    var po = document.createElement('script');
                                    po.type = 'text/javascript';
                                    po.async = true;
                                    po.src = 'https://apis.google.com/js/plusone.js';
                                    var s = document.getElementsByTagName('script')[0];
                                    s.parentNode.insertBefore(po, s);
                                })();
                            </script>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <br>
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="//www.youtube.com/embed/nVyxAHmYQkU?rel=0" allowfullscreen=""></iframe>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane<?php if ($yrp_setting_page) { ?> active<?php } ?>" id="setting">
            <h4><?php echo yrp_i('Yelp Reviews Pro Setting'); ?></h4>
            <!-- Configuration form -->
            <form method="POST" enctype="multipart/form-data">
                <?php wp_nonce_field('yrp-wpnonce_yrp_settings', 'yrp-form_nonce_yrp_settings'); ?>
                <div class="form-group">
                    <label class="control-label" for="yrp_license"><?php echo yrp_i('Pro license'); ?></label>
                    <input class="form-control" type="text" id="yrp_license" name="yrp_license" value="<?php echo esc_attr($yrp_license); ?>">
                </div>
                <?php if (strlen($yrp_expired) > 0 && $yrp_expired != 'false') { ?>
                <div class="alert alert-dismissible alert-success">
                    <strong>Your Pro license is active until <u><?php echo date(get_option('date_format'), $yrp_expired / 1000); ?></u></strong><br>
                    * Plugin automatically updates<br>
                    * Access to priority support <a href="mailto:priority@richplugins.com">priority@richplugins.com</a><br>
                    <button name="yrp_license_deactivate" type="submit" class="button-primary button" onclick="return confirm('Are you sure you want to deactivate the license?');">Deactivate License</button>
                </div>
                <?php } elseif (isset($yrp_license_error) && strlen($yrp_license_error) > 0) { ?>
                <div class="alert alert-dismissible alert-danger">
                    <strong>Activation error </strong><br><?php echo $yrp_license_error; ?>
                </div>
                <?php } elseif (isset($yrp_license_deactivated) && strlen($yrp_license_deactivated) > 0) { ?>
                <div class="alert alert-dismissible alert-success">
                    <strong>The license was deactivated: </strong> <?php echo $yrp_license_deactivated; ?>
                </div>
                <?php } else { ?>
                <p>Activate your Pro license to receive automatic plugin updates and priority support for the life of your license.</p>
                <?php } ?>
                <div class="form-group">
                    <label class="control-label" for="yrp_api_key"><?php echo yrp_i('API Key'); ?></label>
                    <input class="form-control" type="text" id="yrp_api_key" name="yrp_api_key" value="<?php echo esc_attr($yrp_api_key); ?>">
                    <small><?php echo yrp_i('To fill this field, please go to Yelp developers and '); ?><a href="https://www.yelp.com/developers/v3/manage_app" target="_blank"><?php echo yrp_i('Create New App'); ?></a></small>
                </div>
                <div class="form-group">
                    <label class="control-label"><?php echo yrp_i('Yelp Reviews API language'); ?></label>
                    <select class="form-control" id="yrp_language" name="yrp_language">
                        <option value="" <?php selected('', $yrp_language); ?>><?php echo yrp_i('Disable'); ?></option>
                        <option value="cs_CZ" <?php selected('cs_CZ', $yrp_language); ?>><?php echo yrp_i('Czech Republic: Czech'); ?></option>
                        <option value="da_DK" <?php selected('da_DK', $yrp_language); ?>><?php echo yrp_i('Denmark: Danish'); ?></option>
                        <option value="de_AT" <?php selected('de_AT', $yrp_language); ?>><?php echo yrp_i('Austria: German'); ?></option>
                        <option value="de_CH" <?php selected('de_CH', $yrp_language); ?>><?php echo yrp_i('Switzerland: German'); ?></option>
                        <option value="de_DE" <?php selected('de_DE', $yrp_language); ?>><?php echo yrp_i('Germany: German'); ?></option>
                        <option value="en_AU" <?php selected('en_AU', $yrp_language); ?>><?php echo yrp_i('Australia: English'); ?></option>
                        <option value="en_BE" <?php selected('en_BE', $yrp_language); ?>><?php echo yrp_i('Belgium: English'); ?></option>
                        <option value="en_CA" <?php selected('en_CA', $yrp_language); ?>><?php echo yrp_i('Canada: English'); ?></option>
                        <option value="en_CH" <?php selected('en_CH', $yrp_language); ?>><?php echo yrp_i('Switzerland: English'); ?></option>
                        <option value="en_GB" <?php selected('en_GB', $yrp_language); ?>><?php echo yrp_i('United Kingdom: English'); ?></option>
                        <option value="en_HK" <?php selected('en_HK', $yrp_language); ?>><?php echo yrp_i('Hong Kong: English'); ?></option>
                        <option value="en_IE" <?php selected('en_IE', $yrp_language); ?>><?php echo yrp_i('Republic of Ireland: English'); ?></option>
                        <option value="en_MY" <?php selected('en_MY', $yrp_language); ?>><?php echo yrp_i('Malaysia: English'); ?></option>
                        <option value="en_NZ" <?php selected('en_NZ', $yrp_language); ?>><?php echo yrp_i('New Zealand: English'); ?></option>
                        <option value="en_PH" <?php selected('en_PH', $yrp_language); ?>><?php echo yrp_i('Philippines: English'); ?></option>
                        <option value="en_SG" <?php selected('en_SG', $yrp_language); ?>><?php echo yrp_i('Singapore: English'); ?></option>
                        <option value="en_US" <?php selected('en_US', $yrp_language); ?>><?php echo yrp_i('United States: English'); ?></option>
                        <option value="es_AR" <?php selected('es_AR', $yrp_language); ?>><?php echo yrp_i('Argentina: Spanish'); ?></option>
                        <option value="es_CL" <?php selected('es_CL', $yrp_language); ?>><?php echo yrp_i('Chile: Spanish'); ?></option>
                        <option value="es_ES" <?php selected('es_ES', $yrp_language); ?>><?php echo yrp_i('Spain: Spanish'); ?></option>
                        <option value="es_MX" <?php selected('es_MX', $yrp_language); ?>><?php echo yrp_i('Mexico: Spanish'); ?></option>
                        <option value="fi_FI" <?php selected('fi_FI', $yrp_language); ?>><?php echo yrp_i('Finland: Finnish'); ?></option>
                        <option value="fil_PH" <?php selected('fil_PH', $yrp_language); ?>><?php echo yrp_i('Philippines: Filipino'); ?></option>
                        <option value="fr_BE" <?php selected('fr_BE', $yrp_language); ?>><?php echo yrp_i('Belgium: French'); ?></option>
                        <option value="fr_CA" <?php selected('fr_CA', $yrp_language); ?>><?php echo yrp_i('Canada: French'); ?></option>
                        <option value="fr_CH" <?php selected('fr_CH', $yrp_language); ?>><?php echo yrp_i('Switzerland: French'); ?></option>
                        <option value="fr_FR" <?php selected('fr_FR', $yrp_language); ?>><?php echo yrp_i('France: French'); ?></option>
                        <option value="it_CH" <?php selected('it_CH', $yrp_language); ?>><?php echo yrp_i('Switzerland: Italian'); ?></option>
                        <option value="it_IT" <?php selected('it_IT', $yrp_language); ?>><?php echo yrp_i('Italy: Italian'); ?></option>
                        <option value="ja_JP" <?php selected('ja_JP', $yrp_language); ?>><?php echo yrp_i('Japan: Japanese'); ?></option>
                        <option value="ms_MY" <?php selected('ms_MY', $yrp_language); ?>><?php echo yrp_i('Malaysia: Malay'); ?></option>
                        <option value="nb_NO" <?php selected('nb_NO', $yrp_language); ?>><?php echo yrp_i('Norway: Norwegian'); ?></option>
                        <option value="nl_BE" <?php selected('nl_BE', $yrp_language); ?>><?php echo yrp_i('Belgium: Dutch'); ?></option>
                        <option value="nl_NL" <?php selected('nl_NL', $yrp_language); ?>><?php echo yrp_i('The Netherlands: Dutch'); ?></option>
                        <option value="pl_PL" <?php selected('pl_PL', $yrp_language); ?>><?php echo yrp_i('Poland: Polish'); ?></option>
                        <option value="pt_BR" <?php selected('pt_BR', $yrp_language); ?>><?php echo yrp_i('Brazil: Portuguese'); ?></option>
                        <option value="pt_PT" <?php selected('pt_PT', $yrp_language); ?>><?php echo yrp_i('Portugal: Portuguese'); ?></option>
                        <option value="sv_FI" <?php selected('sv_FI', $yrp_language); ?>><?php echo yrp_i('Finland: Swedish'); ?></option>
                        <option value="sv_SE" <?php selected('sv_SE', $yrp_language); ?>><?php echo yrp_i('Sweden: Swedish'); ?></option>
                        <option value="tr_TR" <?php selected('tr_TR', $yrp_language); ?>><?php echo yrp_i('Turkey: Turkish'); ?></option>
                        <option value="zh_HK" <?php selected('zh_HK', $yrp_language); ?>><?php echo yrp_i('Hong Kong: Chinese'); ?></option>
                        <option value="zh_TW" <?php selected('zh_TW', $yrp_language); ?>><?php echo yrp_i('Taiwan: Chinese'); ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <input class="form-control" type="checkbox" id="yrp_install_db" name="yrp_install_db" >
                    <label class="control-label" for="yrp_install_db"><?php echo yrp_i('Re-create the DB tables for the plugin (service option)'); ?></label>
                </div>
                <p class="submit" style="text-align: left">
                    <input name="yrp_setting" type="submit" value="Save" class="button-primary button" tabindex="4">
                </p>
            </form>
            <hr>
            <!-- Enable/disable Yelp Reviews Pro toggle -->
            <form method="POST" action="?page=yrp&amp;yrp_active=<?php echo (string)((int)($yrp_enabled != true)); ?>">
                <?php wp_nonce_field('yrp-wpnonce_yrp_active', 'yrp-form_nonce_yrp_active'); ?>
                <span class="status">
                    <?php echo yrp_i('Yelp Reviews Pro are currently <b>'). ($yrp_enabled ? yrp_i('enable') : yrp_i('disable')) . '</b>'; ?>
                </span>
                <input type="submit" name="yrp_active" class="button" value="<?php echo $yrp_enabled ? yrp_i('Disable') : yrp_i('Enable'); ?>" />
            </form>
            <hr>
            <!-- Debug information -->
            <button class="btn btn-primary btn-small" type="button" data-toggle="collapse" data-target="#debug" aria-expanded="false" aria-controls="debug">
                <?php echo yrp_i('Debug Information'); ?>
            </button>
            <div id="debug" class="collapse">
                <textarea style="width:90%; height:200px;" onclick="this.select();return false;" readonly><?php
                    rplg_debug(YRP_VERSION, yrp_options(), 'widget_yrp_widget'); yrp_debug(); ?>
                </textarea>
            </div>
            <div style="max-width:700px"><?php echo yrp_i('Feel free to contact support team by support@richplugins.com for any issues but please don\'t forget to provide debug information that you can get by click on \'Debug Information\' button.'); ?></div>
            <hr>
            <!-- Reset form -->
            <form action="?page=yrp" method="POST">
                <?php wp_nonce_field('yrp-wpnonce_yrp_reset', 'yrp-form_nonce_yrp_reset'); ?>
                <p>
                    <input type="submit" value="Reset" name="reset" onclick="return confirm('<?php echo yrp_i('Are you sure you want to reset the Yelp Reviews Pro plugin?'); ?>')" class="button" />
                    <?php echo yrp_i('This removes all plugin-specific settings.') ?>
                </p>
                <p>
                    <input type="checkbox" id="reset_db" name="reset_db">
                    <label for="reset_db"><?php echo yrp_i('Remove all data including Yelp Reviews'); ?></label>
                </p>
            </form>
        </div>
        <div role="tabpanel" class="tab-pane" id="shortcode">
            <?php wp_nonce_field('yrw_wpnonce', 'yrw_nonce'); ?>
            <h4><?php echo yrp_i('Shortcode Builder'); ?></h4>
            <?php if (!empty($yrp_api_key)) { ?>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form form-horizontal">
                        <?php include(dirname(__FILE__) . '/yrp-finder.php'); ?>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form form-horizontal">
                        <?php
                        class foo {
                            function get_field_id($id) {
                                return $id;
                            }
                            function get_field_name($name) {
                                return $name;
                            }
                            function render() {
                                $business_id          = '';
                                $business_photo       = '';
                                $dark_theme           = '';
                                $open_link            = '';
                                $nofollow_link        = '';
                                $auto_load            = '';
                                $rating_snippet       = '';
                                $pagination           = '';
                                $sort                 = '';
                                $min_filter           = '';
                                $text_size            = '';
                                $hide_photo           = '';
                                $hide_avatar          = '';
                                $view_mode            = '';
                                $max_width            = '';
                                $max_height           = '';
                                $hide_float_badge     = '';
                                $lazy_load_img        = '';
                                include(dirname(__FILE__) . '/yrp-options.php');
                            }
                        }
                        $bar = new foo;
                        $bar->render();
                        ?>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form form-horizontal">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <textarea id="yrw-shortcode" onclick="this.select();return false;" readonly></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } else { ?>
            <p><?php echo yrp_i('To use shortcode, please first fill \'API Key\' field on Setting tab'); ?></p>
            <?php }?>
            <script type="text/javascript">
            function shortcode(el) {
                if (!el.querySelector('.yrw-business-id').value) {
                    return;
                }
                var args = '', ctrls = el.querySelectorAll('.form-control[name]');
                for (var i = 0; i < ctrls.length; i++) {
                    var ctrl = ctrls[i];
                    if (ctrl.type == 'checkbox') {
                        if (ctrl.checked) {
                            args += ' ' + ctrl.getAttribute('name') + '=' + ctrl.checked;
                        }
                    } else if (ctrl.value) {
                        args += ' ' + ctrl.getAttribute('name') + '=';
                        if (ctrl.value.indexOf(' ') > -1) {
                            args += '"' + ctrl.value + '"';
                        } else {
                            args += ctrl.value;
                        }
                    }
                }
                var shortcodeEl = document.getElementById('yrw-shortcode');
                shortcodeEl.innerHTML = '[yelp-reviews-pro' + args + ']';
            }

            jQuery(document).ready(function($) {
                $('a[data-toggle="tab"]').on('click', function(e)  {
                    var active = $(this).attr('href');
                    $('.tab-content ' + active).addClass('active').show().siblings().hide();
                    $(this).parent('li').addClass('active').siblings().removeClass('active');
                    e.preventDefault();
                });
                $('button[data-toggle="collapse"]').click(function () {
                    $target = $(this);
                    $collapse = $target.next();
                    $collapse.slideToggle(500);
                });

                var shortcodeEl = document.getElementById('shortcode'),
                    yrw_sidebar_init_async = function(attempts) {
                        if (!window.yrw_sidebar_init) {
                            if (attempts > 0) {
                                setTimeout(function() { yrw_sidebar_init_async(attempts - 1); }, 300);
                            }
                            return;
                        }
                        yrw_sidebar_init({
                            widgetId: 'shortcode',
                            cb: function(el, businessId) {
                                shortcode(shortcodeEl);
                            }
                        });
                    };

                yrw_sidebar_init_async(10);

                $('#shortcode input.form-control[type="text"]').keyup(function() {
                    shortcode(shortcodeEl);
                });
                $('#shortcode input.form-control[type="checkbox"],select.form-control').change(function() {
                    shortcode(shortcodeEl);
                });
            });
            </script>
        </div>
    </div>
</div>
