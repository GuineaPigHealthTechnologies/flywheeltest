function SGForm(){
}

SGForm.prototype.init = function() {
	var that = this;

	jQuery('#sgrb-comment-box-rate-show').on('change', function(){
		var showRate = jQuery(this).attr('checked');
		that.hideShowRateSection(showRate);
	});
	jQuery('#sgrb-comment-box-title-show').on('change', function(){
		var showTitle = jQuery(this).attr('checked');
		that.hideShowTitleSection(showTitle);
	});
	jQuery('#sgrb-comment-box-avatar-show').on('change', function(){
		var showAvatar = jQuery(this).attr('checked');
		var showComments = jQuery('#sgrb-comment-box-text-show').attr('checked');
		that.hideShowAvatarSection(showAvatar, showComments);
	});
	jQuery('#sgrb-comment-box-text-show').on('change', function(){
		var showComments = jQuery(this).attr('checked');
		var showAvatar = jQuery('#sgrb-comment-box-avatar-show').attr('checked');
		that.hideShowCommentTextSection(showComments, showAvatar);
	});
	jQuery('#sgrb-comment-box-date-show').on('change', function(){
		var showDate = jQuery(this).attr('checked');
		var showCommentBy = jQuery('#sgrb-comment-box-comment-by-show').attr('checked');
		that.hideShowDateSection(showDate, showCommentBy);
	});
	jQuery('#sgrb-comment-box-comment-by-show').on('change', function(){
		var showCommentBy = jQuery(this).attr('checked');
		var showDate = jQuery('#sgrb-comment-box-date-show').attr('checked');
		that.hideShowCommentBySection(showCommentBy, showDate);
	});

	jQuery('.comment-box-rate-alignment').click(function(){
		var currentId = jQuery(this).attr('id');
		jQuery('.comment-box-rate-alignment').each(function(){
			if (jQuery(this).attr('id') == currentId) {
				jQuery(this).attr('checked', 'checked');
			}
			else {
				jQuery(this).removeAttr('checked');
			}
		});
	});
	jQuery('.comment-box-title-alignment').click(function(){
		var currentId = jQuery(this).attr('id');
		jQuery('.comment-box-title-alignment').each(function(){
			if (jQuery(this).attr('id') == currentId) {
				jQuery(this).attr('checked', 'checked');
			}
			else {
				jQuery(this).removeAttr('checked');
			}
		});
	});
	jQuery('.comment-box-avatar-and-text-alignment').click(function(){
		var currentId = jQuery(this).attr('id');
		jQuery('.comment-box-avatar-and-text-alignment').each(function(){
			if (jQuery(this).attr('id') == currentId) {
				jQuery(this).attr('checked', 'checked');
			}
			else {
				jQuery(this).removeAttr('checked');
			}
		});
	});
	jQuery('.comment-box-date-alignment').click(function(){
		var currentId = jQuery(this).attr('id');
		jQuery('.comment-box-date-alignment').each(function(){
			if (jQuery(this).attr('id') == currentId) {
				jQuery(this).attr('checked', 'checked');
			}
			else {
				jQuery(this).removeAttr('checked');
			}
		});
	});
	jQuery('.sgrb-comment-box-type').click(function(){
		var currentId = jQuery(this).attr('id');
		jQuery('.sgrb-comment-box-type').each(function(){
			if (jQuery(this).attr('id') == currentId) {
				jQuery(this).attr('checked', 'checked');
			}
			else {
				jQuery(this).removeAttr('checked');
			}
		});
	});

	jQuery('.sgrb-comment-form-js-update').click(function(){
		that.save();
	});
};

SGForm.prototype.save = function() {
	var isEdit = false;
	var form = jQuery('.sgrb-js-form');

	var saveAction = 'CommentForm_ajaxSave';
	var ajaxHandler = new sgrbRequestHandler(saveAction, form.serialize());
	ajaxHandler.dataIsObject = false;
	ajaxHandler.dataType = 'html';
	jQuery('.sgrb-loading-spinner').show();
	var sgrbSaveUrl = jQuery('input[name=sgrbSaveUrl]').val();
	ajaxHandler.callback = function(response){
		//If success
		if(response) {
			isEdit = true;
			location.href=sgrbSaveUrl+'&edit='+isEdit;
			jQuery('.sgrb-loading-spinner').hide();
		}
		else {
			alert('Could not save settings');
			jQuery('.sgrb-loading-spinner').hide();
		}
	};
	ajaxHandler.run();
};

SGForm.prototype.hideShowRateSection = function(showRate) {
	if (showRate) {
		/*if selected show rate its align selectBoxes disable*/
		jQuery('.sgrb-rate-box-disable-js').removeClass('sgrb-disabled');
		jQuery('.sgrb-comment-box-stars-hide-show-js').removeClass('sgrb-disabled');
	}
	else {
		jQuery('.sgrb-rate-box-disable-js').addClass('sgrb-disabled');
		jQuery('.sgrb-comment-box-stars-hide-show-js').addClass('sgrb-disabled');
	}
};

SGForm.prototype.hideShowTitleSection = function(showTitle) {
	if (showTitle) {
		/*if selected show rate its align selectBoxes disable*/
		jQuery('.sgrb-title-box-disable-js').removeClass('sgrb-disabled');
		jQuery('.sgrb-comment-box-title-hide-show-js').removeClass('sgrb-disabled');
	}
	else {
		jQuery('.sgrb-title-box-disable-js').addClass('sgrb-disabled');
		jQuery('.sgrb-comment-box-title-hide-show-js').addClass('sgrb-disabled');
	}
}

SGForm.prototype.hideShowAvatarSection = function(showAvatar, showComments) {
	if (showAvatar) {
		/*if selected show rate its align selectBoxes disable*/
		if (showComments) {
			jQuery('.sgrb-avatar-text-disable-js').removeClass('sgrb-disabled');
		}
		jQuery('.sgrb-avatar-disable-js').removeClass('sgrb-disabled');
	}
	else {
		jQuery('.sgrb-avatar-text-disable-js').addClass('sgrb-disabled');
		jQuery('.sgrb-avatar-disable-js').addClass('sgrb-disabled');
	}
}

SGForm.prototype.hideShowCommentTextSection = function(showComments, showAvatar) {
	if (showComments) {
		/*if selected show rate its align selectBoxes disable*/
		if (showAvatar) {
			jQuery('.sgrb-avatar-text-disable-js').removeClass('sgrb-disabled');
		}
		jQuery('.sgrb-comment-text-disable-js').removeClass('sgrb-disabled');
	}
	else {
		if (showAvatar) {
			jQuery('.sgrb-avatar-text-disable-js').addClass('sgrb-disabled');
		}
		jQuery('.sgrb-comment-text-disable-js').addClass('sgrb-disabled');
	}
}

SGForm.prototype.hideShowDateSection = function(showDate, showCommentBy) {
	if (showDate) {
		jQuery('.sgrb-date-comment-by-disable-js').removeClass('sgrb-disabled');
		jQuery('.sgrb-date-hide-show-js').removeClass('sgrb-disabled');
	}
	else {
		if (!showCommentBy) {
			jQuery('.sgrb-date-comment-by-disable-js').addClass('sgrb-disabled');
		}
		jQuery('.sgrb-date-hide-show-js').addClass('sgrb-disabled');
	}
}

SGForm.prototype.hideShowCommentBySection = function(showCommentBy, showDate) {
	if (showCommentBy) {
		jQuery('.sgrb-date-comment-by-disable-js').removeClass('sgrb-disabled');
		jQuery('.sgrb-comment-by-hide-show-js').removeClass('sgrb-disabled');
	}
	else {
		if (!showDate) {
			jQuery('.sgrb-date-comment-by-disable-js').addClass('sgrb-disabled');
		}
		jQuery('.sgrb-comment-by-hide-show-js').addClass('sgrb-disabled');
	}
}
