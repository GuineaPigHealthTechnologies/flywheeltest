<?php
function fbrp_page($page_id, $page_name, $rating, $rating_snippet, $rating_count, $page_photo, $hide_photo, $open_link, $nofollow_link, $lazy_load_img, $show_powered = true) {
    ?>
    <?php if (!$hide_photo) { ?>
    <div class="wp-facebook-left">
        <?php fbrp_image($page_photo ? $page_photo : 'https://graph.facebook.com/' . $page_id . '/picture', $page_name, $lazy_load_img); ?>
    </div>
    <?php } ?>
    <div class="wp-facebook-right">
        <?php if ($rating_snippet) { ?>
        <div class="wp-facebook-name">
            <?php echo fbrp_anchor('https://fb.com/' . $page_id, '', '<span itemprop="name">' . $page_name . '</span>', $open_link, $nofollow_link); ?>
            <meta itemprop="image" content="https://graph.facebook.com/<?php echo $page_id; ?>/picture"/>
        </div>
        <div itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
            <span class="wp-facebook-rating" itemprop="ratingValue"><?php echo $rating; ?></span>
            <span class="wp-facebook-stars"><?php fbrp_stars($rating); ?></span>
            <meta itemprop="ratingCount" content="<?php echo $rating_count; ?>"/>
            <meta itemprop="bestRating" content="5"/>
        </div>
        <?php } else { ?>
        <div class="wp-facebook-name">
            <?php echo fbrp_anchor('https://fb.com/' . $page_id, '', '<span>' . $page_name . '</span>', $open_link, $nofollow_link); ?>
        </div>
        <div>
            <span class="wp-facebook-rating"><?php echo $rating; ?></span>
            <span class="wp-facebook-stars"><?php fbrp_stars($rating); ?></span>
        </div>
        <?php } ?>

        <?php if ($show_powered) { ?>
        <div class="wp-facebook-powered">powered by <span>Facebook</span></div>
        <?php } ?>
    </div>
    <?php
}

function fbrp_page_reviews($page_id, $reviews, $hide_avatar, $text_size, $min_filter, $min_letter, $pagination, $disable_user_link, $open_link, $nofollow_link, $lazy_load_img) {
    ?>
    <div class="wp-facebook-reviews">
    <?php
    $hr = false;
    if (count($reviews) > 0) {
        $i = 0;
        foreach ($reviews as $review) {
            if ($review->rating >= $min_filter && (!$min_letter || (isset($review->review_text) && strlen($review->review_text) > $min_letter))) {
                if ($pagination > 0 && $pagination <= $i++) {
                    $hr = true;
                }
                fbrp_page_review($page_id, $review, $hide_avatar, $text_size, $disable_user_link, $open_link, $nofollow_link, $lazy_load_img, $hr);
            }
        }
    }
    ?>
    </div>
    <?php if ($pagination > 0 && $hr) { ?>
    <a class="wp-facebook-url" href="#" onclick="return rplg_next_reviews.call(this, 'facebook', <?php echo $pagination; ?>);">
        <?php echo fbrp_i('Next Reviews'); ?>
    </a> <?php
    }
}

function fbrp_page_review($page_id, $review, $hide_avatar, $text_size, $disable_user_link, $open_link, $nofollow_link, $lazy_load_img, $hide_review=false) {
    ?>
    <div class="wp-facebook-review <?php if ($hide_review) { ?>wp-facebook-hide<?php } ?>">
        <?php if (!$hide_avatar) { ?>
        <div class="wp-facebook-left">
            <?php fbrp_image('https://graph.facebook.com/' . $review->reviewer->id . '/picture', $review->reviewer->name, $lazy_load_img); ?>
        </div>
        <?php } ?>
        <div class="wp-facebook-right">
            <?php
            if (!$disable_user_link) {
                $profile_url = 'https://facebook.com/' . $page_id . '/reviews';
                fbrp_anchor($profile_url, 'wp-facebook-name', $review->reviewer->name, $open_link, $nofollow_link);
            } else {
                ?><div class="wp-facebook-name"><?php echo $review->reviewer->name; ?></div><?php
            }
            ?>
            <div class="wp-facebook-time" data-time="<?php echo $review->created_time; ?>"><?php echo $review->created_time; ?></div>
            <div class="wp-facebook-feedback">
                <span class="wp-facebook-stars"><?php echo fbrp_stars($review->rating); ?></span>
                <?php if (isset($review->review_text)) { ?>
                <span class="wp-facebook-text"><?php echo fbrp_trim_text($review->review_text, $text_size); ?></span>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php
}

function fbrp_stars($rating) {
    ?><span class="wp-stars"><?php
    foreach (array(1,2,3,4,5) as $val) {
        $score = $rating - $val;
        if ($score >= 0) {
            ?><span class="wp-star"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="17" height="17" viewBox="0 0 1792 1792"><path d="M1728 647q0 22-26 48l-363 354 86 500q1 7 1 20 0 21-10.5 35.5t-30.5 14.5q-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z" fill="#4080ff"></path></svg></span><?php
        } else if ($score > -1 && $score < 0) {
            ?><span class="wp-star"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="17" height="17" viewBox="0 0 1792 1792"><path d="M1250 957l257-250-356-52-66-10-30-60-159-322v963l59 31 318 168-60-355-12-66zm452-262l-363 354 86 500q5 33-6 51.5t-34 18.5q-17 0-40-12l-449-236-449 236q-23 12-40 12-23 0-34-18.5t-6-51.5l86-500-364-354q-32-32-23-59.5t54-34.5l502-73 225-455q20-41 49-41 28 0 49 41l225 455 502 73q45 7 54 34.5t-24 59.5z" fill="#4080ff"></path></svg></span><?php
        } else {
            ?><span class="wp-star"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="17" height="17" viewBox="0 0 1792 1792"><path d="M1201 1004l306-297-422-62-189-382-189 382-422 62 306 297-73 421 378-199 377 199zm527-357q0 22-26 48l-363 354 86 500q1 7 1 20 0 50-41 50-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z" fill="#ccc"></path></svg></span><?php
        }
    }
    ?></span><?php
}

function fbrp_rstrpos($haystack, $needle, $offset) {
    $size = strlen ($haystack);
    $pos = strpos (strrev($haystack), $needle, $size - $offset);

    if ($pos === false)
        return false;

    return $size - $pos;
}

function fbrp_trim_text($text, $size) {
    if ($size > 0 && strlen($text) > $size) {
        $visible_text = $text;
        $invisible_text = '';
        $idx = fbrp_rstrpos($text, ' ', $size);
        if ($idx < 1) {
            $idx = $size;
        }
        if ($idx > 0) {
            $visible_text = substr($text, 0, $idx);
            $invisible_text = substr($text, $idx, strlen($text));
        }
        echo $visible_text;
        if (strlen($invisible_text) > 0) {
            ?><span class="wp-more"><?php echo $invisible_text; ?></span><span class="wp-more-toggle" onclick="this.previousSibling.className='';this.textContent='';"><?php echo fbrp_i('read more'); ?></span><?php
        }
    } else {
        echo $text;
    }
}

function fbrp_anchor($url, $class, $text, $open_link, $nofollow_link) {
    ?><a href="<?php echo $url; ?>" class="<?php echo $class; ?>" <?php if ($open_link) { ?>target="_blank"<?php } ?> <?php if ($nofollow_link) { ?>rel="nofollow"<?php } ?>><?php echo $text; ?></a><?php
}

function fbrp_image($src, $alt, $lazy) {
    ?><img <?php if ($lazy) { ?>class="rplg-blazy" data-<?php } ?>src="<?php echo $src; ?>" alt="<?php echo $alt; ?>" onerror="if(this.src!='<?php echo FBRP_AVATAR; ?>')this.src='<?php echo FBRP_AVATAR; ?>';"><?php
}
?>