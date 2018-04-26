jQuery(document).ready(function () {

    if (jQuery('.single-product .sb_woo_product_image').length) {
        if (!jQuery('.single-product .sb_woo_product_image').hasClass('sb_woo_image_disable_zoom')) {
            jQuery('.single-product .sb_woo_product_image').zoom({
                callback: function () {
                    jQuery(this).colorbox({
                        href: jQuery('.single-product .sb_woo_product_image img').attr('src')
                    });
                }
            });
        } else {
            jQuery(this).colorbox({
                href: jQuery('.single-product .sb_woo_product_image img').attr('src')
            });
        }
    }

    if (jQuery('.single-product .cart.variations_form')) {

        ////////////////////////////////////////////////

        jQuery('.single-product .cart.variations_form .variations .value select').each(function (index, attr) {
            jQuery(this).change(function () {
                sb_woo_variation_image();
            });
        });

        ////////////////////////////////////////////////

        //console.log(sb_woo_attr_data);
    }

    //to handle removing items from the cart with a blank response. Note to edit this if no empty cart layout specified
    jQuery( document.body ).on( 'wc_fragments_refreshed', function() {
        if (jQuery('body.woocommerce-cart').length && (!jQuery('.woocommerce-cart-form').length && !jQuery('.sb_et_woo_li_cart_empty').length)) {
            console.log('Woo Injector Refreshing Cart');
            location.reload(); //refresh the page
        }
    } );

});

function sb_woo_variation_image() {
    //get variation data and store in sb_woo_attr_data
    var sb_woo_attr_data = jQuery('.single-product .cart.variations_form').data('product_variations');
    var sb_woo_attr_val = '';
    var sb_woo_attr_id = '';
    var sb_woo_attr_name = '';
    var sb_woo_attr_set = [];
    var sb_woo_attr_set_l = 0;
    var sb_woo_attr_set_matched = 0;
    var sb_woo_found_set = [];
    var sb_woo_large_image = '';

    ////////////////////////////////////////////////////

    //cache current variation choices in "sb_woo_attr_set"
    jQuery('.single-product .cart.variations_form .variations .value select').each(function (index2, attr2) {
        sb_woo_attr_val = jQuery(this).val();
        sb_woo_attr_id = jQuery(this).attr('id');
        sb_woo_attr_name = 'attribute_' + sb_woo_attr_id;

        if (sb_woo_attr_val) {
            sb_woo_attr_set.push([sb_woo_attr_name, sb_woo_attr_val]);
            sb_woo_attr_set_l++;
        }
    });

    ////////////////////////////////////////////////////

    if (sb_woo_attr_set_l > 0) { //foreach of the stored attribute variables
        jQuery(sb_woo_attr_data).each(function (index3, attr3) { //loop variation prices
            var sb_woo_attrs = attr3.attributes;
            sb_woo_attr_set_matched = 0; //reset to 0

            //loop attributes linked to this attribute set
            jQuery(sb_woo_attrs).each(function (index4, attr4) {
                jQuery(attr4).each(function (index4, attr4) {
                    jQuery(sb_woo_attr_set).each(function (index5, attr5) {
                        if (attr4[attr5[0]] == attr5[1] || attr4[attr5[0]] == "") {
                            sb_woo_attr_set_matched++;
                        }
                    });
                });
            });

            if (sb_woo_attr_set_matched >= sb_woo_attr_set_l) {
                sb_woo_found_set = attr3; //we found a matching set... store it!
            }
        });

        if (typeof sb_woo_found_set.image !== 'undefined') {
            sb_woo_large_image = sb_woo_found_set.image.full_src;
        } else {
            sb_woo_large_image = jQuery('.sb_woo_product_thumb_col_num_1 a').data('large_image');
        }

        sb_woo_product_thumb_replace_by_url(sb_woo_large_image);

    }

}

function sb_woo_product_thumb_replace_by_url(large_image) {
    if (jQuery('.single-product .sb_woo_product_image img').attr('src') == large_image) {
        return;
    }

    jQuery('.sb_woo_product_image img').trigger('zoom.destroy'); // remove zoom

    var image_height = jQuery('.sb_woo_product_image img').height();
    jQuery('.sb_woo_product_image').css('height',  image_height + 'px');

    jQuery('.sb_woo_product_image img').fadeOut(400, function () {
        jQuery('.single-product .sb_woo_product_image img').attr('src', large_image);

        jQuery('.single-product .sb_woo_product_image').imagesLoaded(function () {
            var image_height = jQuery('.sb_woo_product_image img').height();
            jQuery('.sb_woo_product_image').css('height',  image_height + 'px');

            jQuery('.sb_woo_product_image img').fadeIn(400, function () {
                if (!jQuery('.single-product .sb_woo_product_image').hasClass('sb_woo_image_disable_zoom')) {
                    jQuery('.single-product .sb_woo_product_image').zoom({
                        callback: function () {
                            jQuery(this).colorbox({
                                href: jQuery('.single-product .sb_woo_product_image img').attr('src')
                            });
                        }
                    });
                } else {
                    jQuery(this).colorbox({
                        href: jQuery('.single-product .sb_woo_product_image img').attr('src')
                    });
                }
            });
        });
    });
}

function sb_woo_product_thumb_replace(image_object) {
    var large_image = image_object.data('large_image');

    sb_woo_product_thumb_replace_by_url(large_image);
}