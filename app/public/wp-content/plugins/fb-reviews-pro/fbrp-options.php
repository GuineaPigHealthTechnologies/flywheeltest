<!-- Review Options -->
<h4 class="rplg-options-toggle"><?php echo fbrp_i('Review Options'); ?></h4>
<div class="rplg-options" style="display:none">
    <div class="form-group">
        <div class="col-sm-12">
            <input id="<?php echo $this->get_field_id('rating_snippet'); ?>" name="<?php echo $this->get_field_name('rating_snippet'); ?>" type="checkbox" value="true" <?php checked('true', $rating_snippet); ?> class="form-control" />
            <label for="<?php echo $this->get_field_id('rating_snippet'); ?>"><?php echo fbrp_i('Enable Google Rich Snippets (schema.org)'); ?></label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <?php echo fbrp_i('Minimum Review Rating'); ?>
            <select id="<?php echo $this->get_field_id('min_filter'); ?>" name="<?php echo $this->get_field_name('min_filter'); ?>" class="form-control">
                <option value="" <?php selected('', $min_filter); ?>><?php echo fbrp_i('No filter'); ?></option>
                <option value="5" <?php selected('5', $min_filter); ?>><?php echo fbrp_i('5 Stars'); ?></option>
                <option value="4" <?php selected('4', $min_filter); ?>><?php echo fbrp_i('4 Stars'); ?></option>
                <option value="3" <?php selected('3', $min_filter); ?>><?php echo fbrp_i('3 Stars'); ?></option>
                <option value="2" <?php selected('2', $min_filter); ?>><?php echo fbrp_i('2 Stars'); ?></option>
                <option value="1" <?php selected('1', $min_filter); ?>><?php echo fbrp_i('1 Star'); ?></option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <?php echo fbrp_i('Minimum review letter count filter'); ?>
            <input type="text" id="<?php echo $this->get_field_id('min_letter'); ?>" name="<?php echo $this->get_field_name('min_letter'); ?>" value="<?php echo $min_letter; ?>" class="form-control" placeholder="for instance: 150" />
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <?php echo fbrp_i('Number of reviews per page'); ?>
            <input type="text" id="<?php echo $this->get_field_id('pagination'); ?>" name="<?php echo $this->get_field_name('pagination'); ?>" value="<?php echo $pagination; ?>" class="form-control" placeholder="25" />
        </div>
    </div>
</div>

<!-- Display Options -->
<h4 class="rplg-options-toggle"><?php echo fbrp_i('Display Options'); ?></h4>
<div class="rplg-options" style="display:none">
    <div class="form-group">
        <div class="col-sm-12">
            <input id="<?php echo $this->get_field_id('hide_photo'); ?>" name="<?php echo $this->get_field_name('hide_photo'); ?>" class="form-control" type="checkbox" value="1" <?php checked('1', $hide_photo); ?> />
            <label for="<?php echo $this->get_field_id('hide_photo'); ?>"><?php echo fbrp_i('Hide page photo'); ?></label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <input id="<?php echo $this->get_field_id('hide_avatar'); ?>" name="<?php echo $this->get_field_name('hide_avatar'); ?>" class="form-control" type="checkbox" value="1" <?php checked('1', $hide_avatar); ?> />
            <label for="<?php echo $this->get_field_id('hide_avatar'); ?>"><?php echo fbrp_i('Hide user avatars'); ?></label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <label>
                <input id="<?php echo $this->get_field_id('disable_user_link'); ?>" name="<?php echo $this->get_field_name('disable_user_link'); ?>" type="checkbox" value="1" <?php checked('1', $disable_user_link); ?> class="form-control"/>
                <?php echo fbrp_i('Disable user profile links'); ?>
            </label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <input id="<?php echo $this->get_field_id('dark_theme'); ?>" name="<?php echo $this->get_field_name('dark_theme'); ?>" type="checkbox" value="1" <?php checked('1', $dark_theme); ?> class="form-control" />
            <label for="<?php echo $this->get_field_id('dark_theme'); ?>"><?php echo fbrp_i('Dark background'); ?></label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <label for="<?php echo $this->get_field_id('text_size'); ?>"><?php echo fbrp_i('Review limit before \'read more\' link'); ?></label>
            <input id="<?php echo $this->get_field_id('text_size'); ?>" name="<?php echo $this->get_field_name('text_size'); ?>" value="<?php echo $text_size; ?>" class="form-control" type="text" placeholder="for instance: 120" />
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <?php echo fbrp_i('Widget theme'); ?>
            <select id="<?php echo $this->get_field_id('view_mode'); ?>" name="<?php echo $this->get_field_name('view_mode'); ?>" class="form-control">
                <option value="list" <?php selected('list', $view_mode); ?>><?php echo fbrp_i('Review List'); ?></option>
                <option value="slider" <?php selected('slider', $view_mode); ?>><?php echo fbrp_i('Reviews Slider'); ?></option>
                <option value="grid" <?php selected('grid', $view_mode); ?>><?php echo fbrp_i('Reviews Grid'); ?></option>
                <option value="badge" <?php selected('badge', $view_mode); ?>><?php echo fbrp_i('Facebook Badge: right'); ?></option>
                <option value="badge_left" <?php selected('badge_left', $view_mode); ?>><?php echo fbrp_i('Facebook Badge: left'); ?></option>
                <option value="badge_inner" <?php selected('badge_inner', $view_mode); ?>><?php echo fbrp_i('Facebook Badge: embed'); ?></option>
            </select>
        </div>
    </div>
    <?php if (isset($max_width)) { ?>
    <div class="form-group">
        <div class="col-sm-12">
            <label for="<?php echo $this->get_field_id('max_width'); ?>"><?php echo fbrp_i('Maximum width'); ?></label>
            <input id="<?php echo $this->get_field_id('max_width'); ?>" name="<?php echo $this->get_field_name('max_width'); ?>" class="form-control" type="text" placeholder="for instance: 300px" />
        </div>
    </div>
    <?php } ?>
    <?php if (isset($max_height)) { ?>
    <div class="form-group">
        <div class="col-sm-12">
            <label for="<?php echo $this->get_field_id('max_height'); ?>"><?php echo fbrp_i('Maximum height'); ?></label>
            <input id="<?php echo $this->get_field_id('max_height'); ?>" name="<?php echo $this->get_field_name('max_height'); ?>" class="form-control" type="text" placeholder="for instance: 500px" />
        </div>
    </div>
    <?php } ?>
</div>

<!-- Slider Options -->
<h4 class="rplg-options-toggle"><?php echo fbrp_i('Slider Options'); ?></h4>
<div class="rplg-options" style="display:none">
    <div class="form-group">
        <div class="col-sm-12">
            <label>
                <input type="checkbox" <?php checked('slider', $view_mode); ?> class="form-control" onchange="(function(el, el2) { if (el.checked) el2.value = 'slider'; else el2.value = 'list'; }(this, this.parentNode.parentNode.parentNode.parentNode.parentNode.querySelector('#<?php echo $this->get_field_id('view_mode'); ?>')));"/>
                <?php echo fbrp_i('Use Reviews Slider theme'); ?>
            </label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <label>
                <input id="<?php echo $this->get_field_id('slider_hide_pagin'); ?>" name="<?php echo $this->get_field_name('slider_hide_pagin'); ?>" type="checkbox" value="1" <?php checked('1', $slider_hide_pagin); ?> class="form-control"/>
                <?php echo fbrp_i('Hide pagination dots'); ?>
            </label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <label><?php echo fbrp_i('Slider speed in second'); ?></label>
            <input id="<?php echo $this->get_field_id('slider_speed'); ?>" name="<?php echo $this->get_field_name('slider_speed'); ?>" value="<?php echo $slider_speed; ?>" type="text" placeholder="for instance: 5" class="form-control"/>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <label><?php echo fbrp_i('Number of reviews per view'); ?></label>
            <input id="<?php echo $this->get_field_id('slider_count'); ?>" name="<?php echo $this->get_field_name('slider_count'); ?>" value="<?php echo $slider_count; ?>" type="text" placeholder="for instance: 3" class="form-control"/>
        </div>
    </div>
</div>

<!-- Advance Options -->
<h4 class="rplg-options-toggle"><?php echo fbrp_i('Advance Options'); ?></h4>
<div class="rplg-options" style="display:none">
    <div class="form-group">
        <div class="col-sm-12">
            <label>
                <input id="<?php echo $this->get_field_id('open_link'); ?>" name="<?php echo $this->get_field_name('open_link'); ?>" type="checkbox" value="1" <?php checked('1', $open_link); ?> class="form-control" />
                <?php echo fbrp_i('Open links in new Window'); ?>
            </label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <label>
                <input id="<?php echo $this->get_field_id('nofollow_link'); ?>" name="<?php echo $this->get_field_name('nofollow_link'); ?>" type="checkbox" value="1" <?php checked('1', $nofollow_link); ?> class="form-control" />
                <?php echo fbrp_i('User no follow links'); ?>
            </label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <label>
                <input id="<?php echo $this->get_field_id('hide_float_badge'); ?>" name="<?php echo $this->get_field_name('hide_float_badge'); ?>" type="checkbox" value="1" <?php checked('1', $hide_float_badge); ?> class="form-control" />
                <?php echo fbrp_i('Hide float badge on mobile'); ?>
            </label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <label>
                <input id="<?php echo $this->get_field_id('lazy_load_img'); ?>" name="<?php echo $this->get_field_name('lazy_load_img'); ?>" type="checkbox" value="1" <?php checked('1', $lazy_load_img); ?> class="form-control" />
                <?php echo fbrp_i('Lazy load images to improve performance'); ?>
            </label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <?php echo fbrp_i('Cache data'); ?>
            <select id="<?php echo $this->get_field_id('cache'); ?>" name="<?php echo $this->get_field_name('cache'); ?>" class="form-control">
                <option value="1" <?php selected('1', $cache); ?>><?php echo fbrp_i('1 Hour'); ?></option>
                <option value="3" <?php selected('3', $cache); ?>><?php echo fbrp_i('3 Hours'); ?></option>
                <option value="6" <?php selected('6', $cache); ?>><?php echo fbrp_i('6 Hours'); ?></option>
                <option value="12" <?php selected('12', $cache); ?>><?php echo fbrp_i('12 Hours'); ?></option>
                <option value="24" <?php selected('24', $cache); ?>><?php echo fbrp_i('1 Day'); ?></option>
                <option value="48" <?php selected('48', $cache); ?>><?php echo fbrp_i('2 Days'); ?></option>
                <option value="168" <?php selected('168', $cache); ?>><?php echo fbrp_i('1 Week'); ?></option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <label><?php echo fbrp_i('Facebook Page Ratings API limit'); ?></label>
            <input id="<?php echo $this->get_field_id('api_ratings_limit'); ?>" name="<?php echo $this->get_field_name('api_ratings_limit'); ?>" value="<?php echo $api_ratings_limit; ?>" type="text" placeholder="By default: <?php echo FBRP_API_RATINGS_LIMIT; ?>" class="form-control"/>
        </div>
    </div>
</div>