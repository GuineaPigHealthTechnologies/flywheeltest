jQuery(function () {
    jQuery('#wcbd_select_all').on('click', function () {
        jQuery(this).closest('form.wcbd-zip-form').find(':checkbox').not(':disabled').prop('checked', this.checked);
    });
});
jQuery(function() {
    jQuery('.wcbd_checkbox').click(function() {
        if (jQuery(this).is(':checked')) {
            jQuery('#wcbd_zip_button').removeAttr('disabled');
        } else {
            jQuery('#wcbd_zip_button').attr('disabled', 'disabled');
        }
    });
});