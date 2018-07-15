function yrw_sidebar_init(data) {

    var el = document.querySelector('#' + data.widgetId);
    if (!el) return;

    var searchBtn = el.querySelector('.yrw-search-business');
    WPacFastjs.on(searchBtn, 'click', function() {
        var termEl = el.querySelector('.yrw-term'),
            locationEl = el.querySelector('.yrw-location');

        if (!termEl.value) {
            termEl.focus();
            return false;
        }
        if (!locationEl.value) {
            locationEl.focus();
            return false;
        }

        searchBtn.disabled = true;
        jQuery.get(finderVars.handlerUrl + '&cf_action=' + finderVars.actionPrefix + '_search', {
            term: termEl.value,
            location: locationEl.value,
            yrw_wpnonce: jQuery('#yrw_nonce').val()
        }, function(res) {
            searchBtn.disabled = false;
            var businessesEl = el.querySelector('.yrw-businesses');
            if (res && res.businesses && res.businesses.length) {
                businessesEl.innerHTML = '';
                WPacFastjs.each(res.businesses, function(business) {
                    var businessEl = document.createElement('div');
                    businessEl.className = 'yrw-business media';
                    businessEl.innerHTML = renderBusiness(business);
                    businessesEl.appendChild(businessEl);

                    var stars = businessEl.querySelector('.yrw-ystars');
                    stars.innerHTML = WPacStars.rating_render(business.rating, 14, 'd32323');

                    selectBusiness(el, business, businessEl, data.cb);
                });
            } else {
                businessesEl.innerHTML = 'Business not found.';
            }
        }, 'json');
        return false;
    });

    yrw_jquery_init(el, data.cb);
}

function selectBusiness(el, business, businessEl, cb) {
    WPacFastjs.on(businessEl, 'click', function() {
        var activeEl = businessEl.parentNode.querySelector('.yrw-active');
        WPacFastjs.remcl(activeEl, 'yrw-active');
        WPacFastjs.addcl(businessEl, 'yrw-active');

        jQuery.get(finderVars.handlerUrl + '&cf_action=' + finderVars.actionPrefix + '_reviews', {
            business_id: business.id,
            yrw_wpnonce: jQuery('#yrw_nonce').val()
        }, function(res) {
            var reviewsEl = el.querySelector('.yrw-reviews');
            if (res && res.reviews && res.reviews.length) {
                reviewsEl.innerHTML = '';
                for (var i = 0; i < res.reviews.length; i++) {
                    var reviewEl = document.createElement('div');
                    reviewEl.className = 'yrw-business media';
                    reviewEl.innerHTML = renderReview(res.reviews[i]);
                    reviewsEl.appendChild(reviewEl);

                    var stars = reviewEl.querySelector('.yrw-ystars');
                    stars.innerHTML = WPacStars.rating_render(res.reviews[i].rating, 14, 'd32323');
                }
                WPacFastjs.show2(el.querySelector('.yrw-three-reviews-note'));
                saveReviews(el, business, res.reviews, cb);
            } else {
                reviewsEl.innerHTML = 'Has no reviews yet.';
            }
        }, 'json');

        return false;
    });
}

function saveReviews(el, business, reviews, cb) {
    var saveBtnContainer = el.querySelector('.yrw-save-reviews-container');
    saveBtnContainer.innerHTML = '';
    var saveBtn = document.createElement('button');
    saveBtn.innerHTML = 'Save Business and Reviews';
    saveBtn.className = 'yrw-save-reviews btn btn-primary btn-block';
    saveBtnContainer.appendChild(saveBtn);
    WPacFastjs.on(saveBtn, 'click', function() {
        saveBtn.disabled = true;
        jQuery.post(finderVars.handlerUrl + '&cf_action=' + finderVars.actionPrefix + '_save', {
            business_id: business.id,
            yrw_wpnonce: jQuery('#yrw_nonce').val()
        }, function(res) {
            saveBtn.disabled = false;

            var businessId = el.querySelector('.yrw-business-id'),
                businessPhotoImg = el.querySelector('.yrw-business-photo-img'),
                businessPhoto = el.querySelector('.yrw-business-photo');

            businessId.value = business.id;
            businessPhotoImg.src = business.image_url;
            businessPhotoImg.style.display = '';
            businessPhoto.value = business.image_url;

            var controlEl = el.parentNode.parentNode.querySelector('.widget-control-actions');
            if (controlEl) {
                show_tooltip(el);
            }

            cb && cb(el, business.id);

        }, 'json');
        return false;
    });
}

function renderBusiness(business) {
    var loc = business.location,
        address = [loc.address1, loc.city, loc.state, loc.zip_code].join(' ');

    return '' +
        '<div class="media-left">' +
            '<img class="media-object" src="' + business.image_url + '" alt="' + business.name + '" style="width:32px;height:32px;">' +
        '</div>' +
        '<div class="media-body">' +
            '<h5 class="media-heading">' + business.name + '</h5>' +
            '<div>' +
                '<span class="yrw-yrating">' + business.rating + '</span>' +
                '<span class="yrw-ystars"></span>' +
            '</div>' +
            '<small class="text-muted">' + address + '</small>' +
        '</div>';
}

function renderReview(review) {
    return '' +
        '<div class="media-left">' +
            '<img class="media-object" src="' + review.user.image_url + '" alt="' + review.user.name + '" ' +
            'onerror="if(this.src!=\'' + finderVars.YELP_AVATAR + '\')this.src=\'' + finderVars.YELP_AVATAR + '\';">' +
        '</div>' +
        '<div class="media-body">' +
            '<div class="media-heading">' +
                '<a href="' + review.url + '" target="_blank">' + review.user.name + '</a>' +
            '</div>' +
            '<div class="yrw-ytime">' + review.time_created + '</div>' +
            '<div class="yrw-ytext">' +
                '<span class="yrw-ystars"></span> ' + review.text +
            '</div>' +
        '</div>';
}

function show_tooltip(el) {
    var insideEl = WPacFastjs.parents(el, 'widget-inside');
    if (insideEl) {
        var controlEl = insideEl.querySelector('.widget-control-actions');
        if (controlEl) {
            var tooltip = WPacFastjs.create('div', 'yrw-tooltip');
            tooltip.innerHTML = '<div class="yrw-corn1"></div>' +
                                '<div class="yrw-corn2"></div>' +
                                '<div class="yrw-close">×</div>' +
                                '<div class="yrw-text">Please don\'t forget to <b>Save</b> the widget.</div>';
            controlEl.appendChild(tooltip);
            setTimeout(function() {
                WPacFastjs.addcl(tooltip, 'yrw-tooltip-visible');
            }, 100);
            WPacFastjs.on2(tooltip, '.yrw-close', 'click', function() {
                WPacFastjs.rm(tooltip);
            });
        }
    }
}

/*
 * Init photo upload link and options toggles
 */
function yrw_jquery_init(el, cb) {

    jQuery(document).ready(function($) {

        var file_frame;
        $('.yrw-business-photo-btn', el).on('click', function(e) {
            e.preventDefault();
            if (file_frame) {
                file_frame.open();
                return;
            }

            file_frame = wp.media.frames.file_frame = wp.media({
                title: $(this).data('uploader_title'),
                button: {text: $(this).data('uploader_button_text')},
                multiple: false
            });

            file_frame.on('select', function() {
                var business_photo_hidden = $('.yrw-business-photo', el),
                    business_photo_img = $('.yrw-business-photo-img', el);
                attachment = file_frame.state().get('selection').first().toJSON();
                business_photo_hidden.val(attachment.url);
                business_photo_img.attr('src', attachment.url);
                business_photo_img.show();

                // To make 'Save' button enable in the widget
                jQuery(business_photo_hidden).change();

                cb && cb();
            });
            file_frame.open();
            return false;
        });

        $('.rplg-options-toggle').unbind('click').click(function () {
            $(this).toggleClass('toggled');
            $(this).next().slideToggle();
        });
    });
}