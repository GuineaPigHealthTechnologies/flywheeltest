function SGCommentHelper() {
}

SGCommentHelper.prototype.init = function () {
	var that = this;

	jQuery('.sgrb-read-more').on('click', function(){
		that.showHideComment('sgrb-read-more');
	});

	jQuery('.sgrb-hide-read-more').on('click', function(){
		that.showHideComment('sgrb-hide-read-more');
	});

	jQuery('#wpcontent').find('.subsubsub').attr('style','margin-top:66px;position:absolute;');
};
/**
 * generateCommentHtml() generates comment html;
 * @param sgrbFakeId - unique review id;
 * @param obj - response, obj with comments data (name, id, rates...);
 * @param skinHtml - html of selected rate skin type (star, percent, point);
 */
SGCommentHelper.prototype.generateCommentHtml = function (sgrbFakeId, obj, skinHtml, reviewId, next, commentsPerPage, itemsRangeStart) {
	var sgrbMainWrapper = jQuery('#'+sgrbFakeId),
		commentByText = sgrbMainWrapper.find('.sgrb-comment-by-text').val(),
		loadMore = sgrbMainWrapper.find('.sgrb-comment-load'),
		noMoreText = sgrbMainWrapper.find('.sgrb-no-more-text').val(),
		showAllText = sgrbMainWrapper.find('.sgrb-show-all-text').val(),
		hideText = sgrbMainWrapper.find('.sgrb-hide-text').val();
	var isWidget = sgrbMainWrapper.hasClass('sgrb-widget-wrapper');

	var commentHtml = '';
	var formBackgroundColor = sgrbMainWrapper.find('.sgrb-rate-background-color').val();
	var formTextColor = sgrbMainWrapper.find('.sgrb-rate-text-color').val();
	if (!formBackgroundColor) {
		formBackgroundColor = '#fbfbfb';
	}
	if (!formTextColor) {
		formTextColor = '#4c4c4c';
	}
	if (jQuery.isEmptyObject(obj)) {
		sgrbMainWrapper.find('.sgrb-loading-spinner').hide();
		loadMore.attr({
			'disabled':'disabled',
			'style' : 'cursor:default;color:#c1c1c1;vertical-align: text-top;pointer-events: none;'
		}).text(noMoreText);
		return;
	}
	else {
		loadMore.removeAttr('disabled style');
		loadMore.attr('onclick','SGReviewHelper.prototype.ajaxLazyLoading(1,'+next+','+commentsPerPage+','+reviewId+','+sgrbFakeId+')');
	}
	for(var i in obj) {
		commentHtml += this.prepareDefaultHtml(sgrbFakeId, obj[i], skinHtml, formBackgroundColor, formTextColor);

		if (typeof count === 'undefined' || count == '') {
			count = obj[i].count;
		}
		continue;

		/* if widget review add rate skin html */
		if (isWidget) {
			commentHtml += '<input type="hidden" class="sgrb-each-comment-avg-widget" value="' + comment.rates + '">';
			commentHtml += '<div class="sg-row">' +
								'<div class="sg-col-12">' +
									'<div class="sgrb-comment-wrapper sgrb-each-comment-rate">' + skinHtml + '</div>' +
								'</div>' +
							'</div>';
		}
		else {
			if (sgrbMainWrapper.find('.sgrb-is-pro').val() && skinType == 1) {
				var eachCommentAvgRate = 0;
				for (var i = 0;i<comment.rates.length;i++) {
					eachCommentAvgRate += parseInt(comment.rates[i]);
				}
				eachCommentAvgRate = eachCommentAvgRate / parseInt(comment.rates.length);

				commentHtml += '<input type="hidden" class="sgrb-each-comment-avg" value="' + eachCommentAvgRate + '">';
				commentHtml += '<div class="sgrb-row-category">' +
									'<div class="sgrb-row-category-name"><i></i></div><div class="sgrb-rate-each-skin-wrapper">' + skinHtml + '</div>' +
								'</div>';
			}
		}
	}
	this.displayCommentHtml(sgrbFakeId, commentHtml, count);
};

/**
 * displayCommentHtml() display the result of generated comment html;
 * @param reviewId - review id;
 * @param commentHtml - comments html to append to wrapper;
 * @param commentsCount - integer, count of all comments for current review;
 */
SGCommentHelper.prototype.displayCommentHtml = function (reviewId, commentHtml, commentsCount) {
	var wrapper = jQuery('#'+reviewId);
	var hideLoadingButton = false;
	var loadMore = wrapper.find('.sgrb-comment-load'),
		noMoreText = wrapper.find('.sgrb-no-more-text').val();
	if (wrapper.length > 0) {
		wrapper.find('.sgrb-approved-comments-to-show').append(commentHtml);
	}
	wrapper.find('.sgrb-approved-comments-wrapper').each(function(){
		if (jQuery(this).length) {
			var countOfLoadedComments = wrapper.find('.sgrb-approved-comments-wrapper').length;
			if (countOfLoadedComments && commentsCount && countOfLoadedComments == commentsCount) {
				hideLoadingButton = true;
				/*loadMore.attr({
					'disabled':'disabled',
					'style' : 'cursor:default;color:#c1c1c1;vertical-align: text-top;pointer-events: none;'
				}).text(noMoreText);*/
			}
		}
	});
	wrapper.find('.sgrb-approved-comments-to-show').addClass('sgrb-load-it');
	wrapper.find('.sgrb-loading-spinner').hide();
	if (hideLoadingButton) {
		loadMore.hide();
	}
	else {
		loadMore.show();
	}
};

/**
 * prepareDefaultHtml() prepare comment html
 * for default comment form;
 * @param sgrbFakeId - current review unique id;
 * @param comment - comment object with values;
 * @param skinHtml - rate skin html;
 * @param formBackgroundColor - from background color;
 * @param formTextColor - from text color;
 */
SGCommentHelper.prototype.prepareDefaultHtml = function (sgrbFakeId, comment, skinHtml, formBackgroundColor, formTextColor) {
	var sgrbMainWrapper = jQuery('#'+ sgrbFakeId),
		commentByText = sgrbMainWrapper.find('.sgrb-comment-by-text').val(),
		showAllText = sgrbMainWrapper.find('.sgrb-show-all-text').val(),
		hideText = sgrbMainWrapper.find('.sgrb-hide-text').val();
		commentBoxThemeOptions = sgrbMainWrapper.find('.sgrb-comment-box-theme-options').val();
	var commentBoxThemeOptions = jQuery.parseJSON(commentBoxThemeOptions);
	var isWidget = sgrbMainWrapper.hasClass('sgrb-widget-wrapper');
	var commentHtml = '';
	var avatarHtml = '';
	var commentWrapperClassIfAvatar = 'sg-col-12';
	commentHtml += '<div id="" class="sgrb-approved-comments-wrapper sgrb-comment-' + comment.id + '" style="background-color:' + formBackgroundColor + ';color:' + formTextColor + '">';
	/* if widget review add rate skin html */
	if (isWidget) {
		commentHtml += '<input type="hidden" class="sgrb-each-comment-avg-widget" value="' + comment.rates + '">';
		commentHtml += '<div class="sg-row">' +
							'<div class="sg-col-12">' +
								'<div class="sgrb-comment-wrapper sgrb-each-comment-rate">' + skinHtml + '</div>' +
							'</div>' +
						'</div>';
	}
	else {
		var commentBoxRateAlignment = '';
		var commentBoxTitleAlignment = '';
		var commentBoxTheme = commentBoxThemeOptions['comment-box-theme'];
		var commentBoxRateShow = commentBoxThemeOptions['comment-box-rate-show'];
		var commentBoxRateAlignment = commentBoxThemeOptions['comment-box-rate-alignment'];
		var commentBoxTitleShow = commentBoxThemeOptions['comment-box-title-show'];
		var commentBoxTitleAlignment = commentBoxThemeOptions['comment-box-title-alignment'];
		var commentBoxAvatar = commentBoxThemeOptions['comment-box-avatar'];
		var commentBoxText = commentBoxThemeOptions['comment-box-text'];
		var commentBoxAvatarAndTextAlignment = commentBoxThemeOptions['comment-box-avatar-and-text-alignment'];
		var commentBoxDateShow = commentBoxThemeOptions['comment-box-date-show'];
		var commentBoxCommentByShow = commentBoxThemeOptions['comment-box-comment-by-show'];
		var commentBoxDateAlignment = commentBoxThemeOptions['comment-box-date-alignment'];

		var skinType = sgrbMainWrapper.find('.sgrb-rating-type').val();
		var avatarUrl = sgrbMainWrapper.find('.sgrb-avatar-url').val();
		if (sgrbMainWrapper.find('.sgrb-is-pro').val() && skinType == 1) {
			commentWrapperClassIfAvatar = 'sg-col-10';

			var eachCommentAvgRate = 0;
			for (var i = 0;i<comment.rates.length;i++) {
				eachCommentAvgRate += parseInt(comment.rates[i]);
			}
			eachCommentAvgRate = eachCommentAvgRate / parseInt(comment.rates.length);

			var rateWrapperWidthStart = '';
			var rateWrapperWidthEnd = '';

			if (commentBoxRateShow) {
				if (commentBoxRateAlignment == 'center') {
					rateWrapperWidthStart = '<div style="width: 62%;">';
					rateWrapperWidthEnd = '</div>';
				}
				else if (commentBoxRateAlignment == 'left') {
					commentBoxRateAlignment = 'style="float:'+commentBoxRateAlignment+';"';
				}
				else {
					commentBoxRateAlignment = '';
				}
				commentHtml += '<input type="hidden" class="sgrb-each-comment-avg" value="' + eachCommentAvgRate + '">';
				commentHtml += '<div class="sgrb-row-category">'+ rateWrapperWidthStart +
									'<div class="sgrb-rate-each-skin-wrapper" '+commentBoxRateAlignment+'>' + skinHtml + '</div>' +
								'</div>'+rateWrapperWidthEnd;
			}
		}
	}
	/* add title section (header) */
	if (commentBoxTitleShow) {
		if (commentBoxTitleAlignment == 'center') {
			commentBoxTitleAlignment = 'style="text-align:'+commentBoxTitleAlignment+'"';
		}
		else if (commentBoxTitleAlignment == 'right') {
			commentBoxTitleAlignment = 'style="text-align:'+commentBoxTitleAlignment+'"';
		}
		else {
			commentBoxTitleAlignment = '';
		}
		commentHtml += '<div class="sg-row">' +
						'<div class="sg-col-12">' +
							'<div class="sgrb-comment-wrapper" '+commentBoxTitleAlignment+'>' +
								'<span>' +
									'<i><b>' + comment.title + ' </i></b>' +
								'</span>' +
							'</div>' +
						'</div>' +
					'</div>';
	}


	/* comment box,content section*/

	var avatarHtml = '<div class="sg-col-2">' +
					'<div style="text-align: center;">' +
						'<img src="'+avatarUrl+'" width="60px" height="100px">' +
					'</div>' +
				'</div>';
	var commentTextHtml = '<div class="'+commentWrapperClassIfAvatar+'">' +
						'<div class="sgrb-comment-wrapper">';
						/* if widget review make comment section length smaller (80 chars) */
						if (comment.comment.length >= 80 && isWidget) {
							commentTextHtml += '<input class="sgrb-full-comment" type="hidden" value="' + comment.comment + '">';
							commentTextHtml += '<span class="sgrb-comment-text-js sgrb-comment-max-height">'+
												comment.comment.substring(0, 80) +
												' ... <a onclick="SGCommentHelper.prototype.showHideComment(' + sgrbFakeId + ',' + comment.id + ', \'sgrb-read-more\')" href="javascript:void(0)" class="sgrb-read-more">'+showAllText+'&#9660</a>' +
											'</span>';
						}
						else if (comment.comment.length >= 200 && !isWidget) {
							/* else set comment section length to default (200 chars) */
							commentTextHtml += '<input class="sgrb-full-comment" type="hidden" value="' + comment.comment + '">';
							commentTextHtml += '<span class="sgrb-comment-text-js sgrb-comment-max-height">'+
												comment.comment.substring(0, 200) +
												' ... <a onclick="SGCommentHelper.prototype.showHideComment(' + sgrbFakeId + ',' + comment.id + ', \'sgrb-read-more\')" href="javascript:void(0)" class="sgrb-read-more">'+showAllText+'&#9660</a>' +
											'</span>';
						}
						else {
							/* if short comment, don't cut */
							commentTextHtml += '<span class="sgrb-comment-text">' + comment.comment + '</span>';
						}
	commentTextHtml += '</div></div>';

	if (commentBoxAvatar && commentBoxText) {
		commentWrapperClassIfAvatar = 'sg-col-10';
		commentTextHtml = '<div class="'+commentWrapperClassIfAvatar+'">' +
					'<div class="sgrb-comment-wrapper">';
					/* if widget review make comment section length smaller (80 chars) */
					if (comment.comment.length >= 80 && isWidget) {
						commentTextHtml += '<input class="sgrb-full-comment" type="hidden" value="' + comment.comment + '">';
						commentTextHtml += '<span class="sgrb-comment-text-js sgrb-comment-max-height">'+
											comment.comment.substring(0, 80) +
											' ... <a onclick="SGCommentHelper.prototype.showHideComment(' + sgrbFakeId + ',' + comment.id + ', \'sgrb-read-more\')" href="javascript:void(0)" class="sgrb-read-more">'+showAllText+'&#9660</a>' +
										'</span>';
					}
					else if (comment.comment.length >= 200 && !isWidget) {
						/* else set comment section length to default (200 chars) */
						commentTextHtml += '<input class="sgrb-full-comment" type="hidden" value="' + comment.comment + '">';
						commentTextHtml += '<span class="sgrb-comment-text-js sgrb-comment-max-height">'+
											comment.comment.substring(0, 200) +
											' ... <a onclick="SGCommentHelper.prototype.showHideComment(' + sgrbFakeId + ',' + comment.id + ', \'sgrb-read-more\')" href="javascript:void(0)" class="sgrb-read-more">'+showAllText+'&#9660</a>' +
										'</span>';
					}
					else {
						/* if short comment, don't cut */
						commentTextHtml += '<span class="sgrb-comment-text">' + comment.comment + '</span>';
					}
		commentTextHtml += '</div></div>';
		if (commentBoxAvatarAndTextAlignment == 2) {/* if reverse*/
			commentHtml += '<div class="sg-row">' + commentTextHtml + avatarHtml;
		}
		else {
			commentHtml += '<div class="sg-row">' + avatarHtml +commentTextHtml;
		}
	}
	else if (commentBoxAvatar && !commentBoxText) {
		commentHtml += '<div class="sg-row">' + avatarHtml;
	}
	else if (!commentBoxAvatar && commentBoxText) {
		commentWrapperClassIfAvatar = 'sg-col-12';
		commentTextHtml = '<div class="'+commentWrapperClassIfAvatar+'">' +
					'<div class="sgrb-comment-wrapper">';
					/* if widget review make comment section length smaller (80 chars) */
					if (comment.comment.length >= 80 && isWidget) {
						commentTextHtml += '<input class="sgrb-full-comment" type="hidden" value="' + comment.comment + '">';
						commentTextHtml += '<span class="sgrb-comment-text-js sgrb-comment-max-height">'+
											comment.comment.substring(0, 80) +
											' ... <a onclick="SGCommentHelper.prototype.showHideComment(' + sgrbFakeId + ',' + comment.id + ', \'sgrb-read-more\')" href="javascript:void(0)" class="sgrb-read-more">'+showAllText+'&#9660</a>' +
										'</span>';
					}
					else if (comment.comment.length >= 200 && !isWidget) {
						/* else set comment section length to default (200 chars) */
						commentTextHtml += '<input class="sgrb-full-comment" type="hidden" value="' + comment.comment + '">';
						commentTextHtml += '<span class="sgrb-comment-text-js sgrb-comment-max-height">'+
											comment.comment.substring(0, 200) +
											' ... <a onclick="SGCommentHelper.prototype.showHideComment(' + sgrbFakeId + ',' + comment.id + ', \'sgrb-read-more\')" href="javascript:void(0)" class="sgrb-read-more">'+showAllText+'&#9660</a>' +
										'</span>';
					}
					else {
						/* if short comment, don't cut */
						commentTextHtml += '<span class="sgrb-comment-text">' + comment.comment + '</span>';
					}
		commentTextHtml += '</div></div>';
		commentHtml += '<div class="sg-row">' + commentTextHtml;
	}
	/*comment box content sextion*/
	/* if user don't type his/her name, show as Guest (footer) */
	if (!comment.name) {
		var name = 'Guest';
		var addWidth = '';
	}
	else {
		var name = comment.name;
		if (name.length >= 100) {
			var addWidth = 'style="width:95%;"';
		}
		else {
			var addWidth = '';
		}
	}
	if (commentBoxDateShow || commentBoxCommentByShow) {
		dateHtml = '<b>' + comment.date + '</b> <i>';
		commentByHtml = commentByText +' </i><b>&nbsp;' + name + '</b>';
		if (commentBoxDateShow && commentBoxCommentByShow) {
			var dateCommentBySeparator = ' , ';
		}
		if (!commentBoxDateShow) {
			dateHtml = '';
			dateCommentBySeparator = '';
		}
		if (!commentBoxCommentByShow) {
			commentByHtml = '';
			dateCommentBySeparator = '';
		}
		if (commentBoxDateAlignment == 'left') {
			dateWrapperWidthStart = '';
			dateWrapperWidthEnd = '';
			commentBoxDateAlignment = '';
		}
		else if (commentBoxDateAlignment == 'center') {
			dateWrapperWidthStart = '<div style="text-align:center;">';
			dateWrapperWidthEnd = '</div>';
			commentBoxDateAlignment = '';
		}
		else if (commentBoxDateAlignment == 'right') {
			dateWrapperWidthStart = '<div style="text-align:right;">';
			dateWrapperWidthEnd = '</div>';
		}

		commentHtml += '</div>' +
			'<div class="sg-row">' +
				'<div class="sg-col-12">' +
					dateWrapperWidthStart+'<span class="sgrb-name-title-text" ' + addWidth + '>'+dateHtml+dateCommentBySeparator+ commentByHtml +' </span>' +dateWrapperWidthEnd+
				'</div>' +
			'</div>' +
		'</div>';
	}

	return commentHtml;
};


/**
 * showHideComment() show more or less comment;
 * @param reviewId - review id;
 * @param commentId - comment id;
 * @param className - class, which shows is less or more;
 */
SGCommentHelper.prototype.showHideComment = function (reviewId,commentId,className) {
	var currentComment = jQuery('#'+reviewId).find('.sgrb-comment-'+commentId),
		showAllText = jQuery('#'+reviewId).find('.sgrb-show-all-text').val(),
		hideText = jQuery('#'+reviewId).find('.sgrb-hide-text').val();

	if (currentComment.parentsUntil('.sgrb-user-rate-js-form').hasClass('sgrb-widget-wrapper')) {
		var cutTextSize = 80;
	}
	else {
		var cutTextSize = 200;
	}
	if (className == 'sgrb-read-more') {
		var fullText = currentComment.find('.sgrb-read-more')
		.parent()
		.parent()
		.find('.sgrb-full-comment')
		.val();
		currentComment.find('.sgrb-read-more')
		.parent()
		.parent()
		.find('.sgrb-comment-text-js')
		.empty()
		.removeClass('sgrb-comment-max-height')
		.html(''+fullText+' <a onclick="SGCommentHelper.prototype.showHideComment('+reviewId+', '+commentId+',\'sgrb-hide-read-more\')" href="javascript:void(0)" class="sgrb-hide-read-more">'+hideText+'&#9650</a>');
	}

	if (className == 'sgrb-hide-read-more') {
		var fullText = currentComment.find('.sgrb-hide-read-more').parent().parent().find('.sgrb-full-comment').val();
		var cuttedText = fullText.substr(0, cutTextSize);
		currentComment.find('.sgrb-hide-read-more').parent().parent().find('.sgrb-comment-text-js').empty().addClass('sgrb-comment-max-height').html(cuttedText+' ... <a onclick="SGCommentHelper.prototype.showHideComment('+reviewId+', '+commentId+',\'sgrb-read-more\')" href="javascript:void(0)" class="sgrb-read-more">'+showAllText+'&#9660</a>');
	}
};

SGCommentHelper.prototype.lazyLoadingLoadMoreButton = function () {

};
