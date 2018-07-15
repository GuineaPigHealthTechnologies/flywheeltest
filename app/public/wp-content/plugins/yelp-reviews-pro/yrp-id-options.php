<?php global $wp_version; if (version_compare($wp_version, '3.5', '>=')) { wp_enqueue_media(); ?>
<div class="form-group">
    <div class="col-sm-12">
        <img id="<?php echo $this->get_field_id('business_photo_img'); ?>" src="<?php echo $business_photo; ?>" alt="<?php echo $business_id; ?>" class="yrw-business-photo-img" style="display:<?php if ($business_photo) { ?>inline-block<?php } else { ?>none<?php } ?>;width:32px;height:32px;border-radius:50%;">
        <a id="<?php echo $this->get_field_id('business_photo_btn'); ?>" href="#" class="yrw-business-photo-btn"><?php echo yrp_i('Change business photo'); ?></a>
        <input type="hidden" id="<?php echo $this->get_field_id('business_photo'); ?>" name="<?php echo $this->get_field_name('business_photo'); ?>" value="<?php echo $business_photo; ?>" class="form-control yrw-business-photo" tabindex="2"/>
    </div>
</div>
<?php } ?>

<div class="form-group">
    <div class="col-sm-12">
        <input type="text" id="<?php echo $this->get_field_id('business_id'); ?>" name="<?php echo $this->get_field_name('business_id'); ?>" value="<?php echo $business_id; ?>" class="form-control yrw-business-id" placeholder="<?php echo yrp_i('Business ID (e.g. benjamin-steakhouse-white-plains)'); ?>" readonly />
    </div>
</div>