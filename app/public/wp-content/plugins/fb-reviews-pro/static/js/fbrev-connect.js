function fbrev_popup(url, width, height, cb) {
    var top = top || (screen.height/2)-(height/2),
        left = left || (screen.width/2)-(width/2),
        win = window.open(url, '', 'location=1,status=1,resizable=yes,width='+width+',height='+height+',top='+top+',left='+left);
    function check() {
        if (!win || win.closed != false) {
            cb();
        } else {
            setTimeout(check, 100);
        }
    }
    setTimeout(check, 100);
}

function fbrev_connect(el, cb) {
    fbrev_popup('https://app.widgetpack.com/auth/fbrev?scope=manage_pages,pages_show_list', 670, 520, function() {
        WPacXDM.get('https://embed.widgetpack.com', 'https://app.widgetpack.com/widget/facebook/accesstoken', {}, function(res) {
            WPacFastjs.jsonp('https://graph.facebook.com/me/accounts', {access_token: res.accessToken, limit: 250}, function(res) {

                var pagesEl = el.querySelector('.fbrev-pages'),
                    idEl = el.querySelector('.fbrev-page-id'),
                    nameEl = el.querySelector('.fbrev-page-name'),
                    tokenEl = el.querySelector('.fbrev-page-token'),
                    pagePhoto = el.querySelector('.fbrev-page-photo'),
                    pagePhotoImg = el.querySelector('.fbrev-page-photo-img');

                WPacFastjs.each(res.data, function(page) {
                    var pageEL = WPacFastjs.create('div', 'fbrev-page');
                    pageEL.innerHTML = '<img src="https://graph.facebook.com/' + page.id +  '/picture" class="fbrev-page-photo">' +
                                       '<div class="fbrev-page-name">' + page.name + '</div>';
                    pagesEl.appendChild(pageEL);
                    WPacFastjs.on(pageEL, 'click', function() {
                        idEl.value = page.id;
                        nameEl.value = page.name;
                        tokenEl.value = page.access_token;
                        jQuery(tokenEl).change();

                        if (pagePhoto) {
                            pagePhoto.value = '';
                            pagePhotoImg.src = 'https://graph.facebook.com/' + page.id +  '/picture';
                            WPacFastjs.show2(pagePhotoImg);
                        }

                        WPacFastjs.remcl(pagesEl.querySelector('.active'), 'active');
                        WPacFastjs.addcl(pageEL, 'active');

                        cb && cb();
                        return false;
                    });
                });
            });
        });
    });
    return false;
}

function fbrev_init(data) {

    var el = document.querySelector('#' + data.widgetId);
    if (!el) return;

    var connectBtn = el.querySelector('.fbrev-connect');
    WPacFastjs.on(connectBtn, 'click', function() {
        fbrev_connect(el, data.cb);
        return false;
    });

    jQuery(document).ready(function($) {

        var file_frame;
        $('.fbrev-page-photo-btn', el).on('click', function(e) {
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
                var place_photo_hidden = $('.fbrev-page-photo', el),
                    place_photo_img = $('.fbrev-page-photo-img', el);
                attachment = file_frame.state().get('selection').first().toJSON();
                place_photo_hidden.val(attachment.url);
                place_photo_img.attr('src', attachment.url);
                place_photo_img.show();

                // To make sav button enable in the widget
                jQuery(place_photo_hidden).change();

                data.cb && data.cb();
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