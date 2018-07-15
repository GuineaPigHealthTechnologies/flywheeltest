<?php
if ($lazy_load_img && strpos($view_mode, 'badge') !== 0) {
    wp_register_script('blazy_js', plugins_url('/static/js/blazy.min.js', __FILE__));
    wp_enqueue_script('blazy_js', plugins_url('/static/js/blazy.min.js', __FILE__));
}

wp_register_script('rplg_js', plugins_url('/static/js/rplg.js', __FILE__));
wp_enqueue_script('rplg_js', plugins_url('/static/js/rplg.js', __FILE__));

include_once(dirname(__FILE__) . '/yrp-reviews-helper.php');

if ($min_filter > 0) {
    $min_filter_where = ' AND rating >= ' . $min_filter;
} else {
    $min_filter_where = '';
}
switch ($sort) {
    case '1': $sort_order = ' ORDER BY time DESC'; break;
    case '2': $sort_order = ' ORDER BY time ASC'; break;
    case '3': $sort_order = ' ORDER BY rating DESC'; break;
    case '4': $sort_order = ' ORDER BY rating ASC'; break;
    default: $sort_order = '';
}
if ($view_mode == 'grid' && $pagination > 0) {
    $sort_order = $sort_order . ' LIMIT ' . $pagination;
}
$business = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "yrw_yelp_business WHERE business_id = %s", $business_id));
if (!$business) {
    ?>
    <div class="yrw-error" style="padding:10px;color:#B94A48;background-color:#F2DEDE;border-color:#EED3D7;">
        <?php echo yrp_i('Business not found by BusinessID: ') . $business_id; ?>
    </div>
    <?php
    return;
}

$reviews = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "yrw_yelp_review WHERE yelp_business_id = %d" . $min_filter_where . $sort_order, $business->id));

$rating = number_format((float)$business->rating, 1, '.', '');

$business_img = isset($business_photo) && strlen($business_photo) > 0 ? $business_photo : $business->photo;
?>

<div class="wp-yrw wpac" <?php if ($rating_snippet) { ?>itemscope="" itemtype="http://schema.org/LocalBusiness"<?php } ?> style="<?php if (isset($max_width) && strlen($max_width) > 0) { ?>max-width:<?php echo $max_width;?>!important;<?php } ?><?php if (isset($max_height) && strlen($max_height) > 0) { ?>max-height:<?php echo $max_height;?>!important;overflow-y:auto!important;<?php } ?>">

    <?php if ($view_mode == 'list') { ?>

    <div class="wp-yelp-list<?php if ($dark_theme) { ?> wp-dark<?php } ?>">
        <div class="wp-yelp-place">
            <?php yrp_page($business, $rating, $business_img, $rating_snippet, $hide_photo, $open_link, $nofollow_link, $lazy_load_img); ?>
        </div>
        <div class="wp-yelp-content-inner">
            <?php yrp_page_reviews($reviews, $pagination, $business_id, $min_filter, $hide_avatar, $text_size, $open_link, $nofollow_link, $lazy_load_img); ?>
        </div>
    </div>

    <?php } elseif ($view_mode == 'grid') { ?>

    <style>
    .wp-yrw .wp-yelp-grid {
        display: -webkit-flex!important;
        display: -ms-flexbox!important;
        display: flex!important;
        -webkit-flex-flow: row wrap!important;
        -ms-flex-flow: row wrap!important;
        flex-flow: row wrap!important;
        margin: 0 auto!important;
        -webkit-align-items: stretch!important;
        -ms-flex-align: stretch!important;
        align-items: stretch!important;
    }
    @media (min-width: 840px) {
        .wp-yrw .wp-yelp-grid {
            padding: 8px!important;
        }
    }
    .wp-yrw .wp-yelp-col {
        box-sizing: border-box!important;
    }
    @media (min-width: 840px) {
        .wp-yrw .wp-yelp-col-4 {
            margin: 8px!important;
            width: calc(33.3333333333% - 16px)!important;
        }
    }
    @media (max-width: 839px) and (min-width: 480px) {
        .wp-yrw .wp-yelp-col-4 {
            margin: 8px!important;
            width: calc(50% - 16px)!important;
        }
    }
    @media (max-width: 479px) {
        .wp-yrw .wp-yelp-col-4 {
            margin: 8px!important;
            width: calc(100% - 16px)!important;
        }
    }
    .wp-yrw .wp-yelp-col-6 {
        margin: 8px!important;
        width: calc(50% - 16px)!important;
    }
    </style>
    <div class="wp-yelp-grid<?php if ($dark_theme) { ?> wp-dark<?php } ?>">
        <?php
        switch (count($reviews)) {
            case 1:
                $col = 12;
                break;
            case 2:
                $col = 6;
                break;
            default:
               $col = 4;
        }
        $i = 1;
        $count_rem = count($reviews) % 3;
        foreach ($reviews as $review) {
            $col_class = 'wp-yelp-col-' . $col; ?>
            <div class="wp-yelp-col <?php echo $col_class; ?>">
                <?php yrp_page_review($review, $hide_avatar, $text_size, $open_link, $nofollow_link, $lazy_load_img); ?>
            </div>
        <?php } ?>
    </div>

    <?php } else { ?>

    <div class="rplg-badge">
        <div class="wp-yelp-badge<?php if ($view_mode != 'badge_inner') { ?> wp-yelp-<?php echo $view_mode; ?>-fixed<?php } ?><?php if ($hide_float_badge) { ?> wp-yelp-badge-hide<?php } ?>">
            <div class="wp-yelp-border"></div>
            <div class="wp-yelp-badge-btn">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="44" height="44" viewBox="0 0 533.33 533.33" style="enable-background:new 0 0 533.33 533.33;" xml:space="preserve">
                    <path d="M317.119,340.347c-9.001,9.076-1.39,25.586-1.39,25.586l67.757,113.135c0,0,11.124,14.915,20.762,14.915   c9.683,0,19.246-7.952,19.246-7.952l53.567-76.567c0,0,5.395-9.658,5.52-18.12c0.193-12.034-17.947-15.33-17.947-15.33   l-126.816-40.726C337.815,335.292,325.39,331.994,317.119,340.347z M310.69,283.325c6.489,11.004,24.389,7.798,24.389,7.798   l126.532-36.982c0,0,17.242-7.014,19.704-16.363c2.415-9.352-2.845-20.637-2.845-20.637l-60.468-71.225   c0,0-5.24-9.006-16.113-9.912c-11.989-1.021-19.366,13.489-19.366,13.489l-71.494,112.505   C311.029,261.999,304.709,273.203,310.69,283.325z M250.91,239.461c14.9-3.668,17.265-25.314,17.265-25.314l-1.013-180.14   c0,0-2.247-22.222-12.232-28.246c-15.661-9.501-20.303-4.541-24.79-3.876l-105.05,39.033c0,0-10.288,3.404-15.646,11.988   c-7.651,12.163,7.775,29.972,7.775,29.972l109.189,148.831C226.407,231.708,237.184,242.852,250.91,239.461z M224.967,312.363   c0.376-13.894-16.682-22.239-16.682-22.239L95.37,233.079c0,0-16.732-6.899-24.855-2.091c-6.224,3.677-11.738,10.333-12.277,16.216   l-7.354,90.528c0,0-1.103,15.685,2.963,22.821c5.758,10.128,24.703,3.074,24.703,3.074L210.37,334.49   C215.491,331.048,224.471,330.739,224.967,312.363z M257.746,361.219c-11.315-5.811-24.856,6.224-24.856,6.224l-88.265,97.17   c0,0-11.012,14.858-8.212,23.982c2.639,8.552,7.007,12.802,13.187,15.797l88.642,27.982c0,0,10.747,2.231,18.884-0.127   c11.552-3.349,9.424-21.433,9.424-21.433l2.003-131.563C268.552,379.253,268.101,366.579,257.746,361.219z" fill="#D80027"/>
                </svg>
                <?php if ($rating_snippet) { ?>
                <meta itemprop="name" content="<?php echo $business->name; ?>"/>
                <meta itemprop="image" content="<?php echo $business->photo; ?>"/>
                <div class="wp-yelp-badge-score" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
                    <div><?php echo yrp_i('Yelp Rating'); ?></div>
                    <span class="wp-yelp-rating" itemprop="ratingValue"><?php echo $rating; ?></span>
                    <span class="wp-yelp-stars"><?php yrp_stars($rating); ?></span>
                    <meta itemprop="ratingCount" content="<?php echo $business->review_count; ?>"/>
                    <meta itemprop="bestRating" content="5"/>
                </div>
                <?php } else { ?>
                <div class="wp-yelp-badge-score">
                    <div><?php echo yrp_i('Yelp Rating'); ?></div>
                    <span class="wp-yelp-rating"><?php echo $rating; ?></span>
                    <span class="wp-yelp-stars"><?php yrp_stars($rating); ?></span>
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="wp-yelp-form <?php if ($view_mode == 'badge_left') { ?>wp-yelp-form-left<?php } ?>" style="display:none">
            <div class="wp-yelp-head">
                <div class="wp-yelp-head-inner">
                    <?php yrp_page($business, $rating, $business_img, false, $hide_photo, $open_link, $nofollow_link, $lazy_load_img, false); ?>
                </div>
                <button class="wp-yelp-close" type="button" onclick="this.parentNode.parentNode.style.display='none'">×</button>
            </div>
            <div class="wp-yelp-body"></div>
            <div class="wp-yelp-content">
                <div class="wp-yelp-content-inner">
                    <?php yrp_page_reviews($reviews, $pagination, $business_id, $min_filter, $hide_avatar, $text_size, $open_link, $nofollow_link, $lazy_load_img); ?>
                </div>
            </div>
            <div class="wp-yelp-footer">
                <div class="wp-yelp-logo">
                    <?php echo yrp_anchor($business->url, '', '<img src="' . YRP_PLUGIN_URL . '/static/img/yelp-logo.png" alt="Yelp logo">', $open_link, $nofollow_link); ?>
                </div>
            </div>
        </div>
        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" onload="(function(el) { var t = setInterval(function () {if (window.rplg_badge_init){rplg_badge_init(el, 'yelp', 'wp-yrw');clearInterval(t);}}, 100); })(this.parentNode);" style="display:none">
    </div>
    <?php } ?>
</div>

<?php if ($auto_load) { ?>
<script type="text/javascript">
setTimeout(function() {
    var script = document.createElement('script');
    script.async = true;
    script.src = '?cf_action=yrp_auto_save&business_id=<?php echo $business_id; ?>&min_filter=<?php echo $min_filter; ?>&ver=' + new Date().getTime();
    var firstScript = document.getElementsByTagName('script')[0];
    firstScript.parentNode.insertBefore(script, firstScript);
}, 2000);
</script>
<?php } ?>