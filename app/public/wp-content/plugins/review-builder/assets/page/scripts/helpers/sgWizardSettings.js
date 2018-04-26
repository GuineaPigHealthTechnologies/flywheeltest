function SGWizardSettings(){
	this.init();
}

SGWizardSettings.prototype.init = function() {
	var that = this;

	that.nextPreviousButtonHideShow(1);
	/* wizard tabs init  */
	jQuery('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
		var isFirstClick = true;
		var pageNum = jQuery(e.target).attr('aria-controls').slice(-1);
		isFirstClick = false;
		var $target = jQuery(e.target);
		if ($target.parent().hasClass('disabled')) {
			return false;
		}
	});

	jQuery('.round-tab').each(function(){
		jQuery(this).click(function(){
			/*var pageNum = jQuery(this).parent('a[data-toggle="tab"]').attr('aria-controls').slice(-1);
			jQuery('.sgrb-wizard-current-page').val(pageNum);
			SGWizardSettings.prototype.nextPreviousButtonHideShow(pageNum);*/
		});
	});

	/* next button init */
	jQuery(".next-step").click(function (e) {
		var $active = jQuery('.wizard .nav-tabs li.active');
		var pageNum = jQuery($active).find('a').attr('aria-controls').slice(-1);
		$active.next().removeClass('disabled');
		that.nextTab($active);
	});

	/* previous button init */
	jQuery(".prev-step").click(function (e) {
		var $active = jQuery('.wizard .nav-tabs li.active');
		var pageNum = jQuery($active).find('a').attr('aria-controls').slice(-1);
		that.prevTab($active);
	});

	jQuery('.sgrb-template-selector').on('click', function(){
		SGReview.prototype.templateTabReordering();
	});
	/* click on type icons, transition from tab1 to tab2, selecting review type */
	jQuery('.sgrb-review-type-selector').on('click', function(){
		/* var reviewType returns e.g.: 'simple_review' / 'product_review' */
		var reviewType = jQuery(this).data('review-type');

		if (jQuery('.sgrb-text-input.sgrb-title-input').length) {
			if (jQuery('.sgrb-text-input.sgrb-title-input').val() != '') {
				/* if edit, if user want to change review type */
				if (jQuery('.sgrb-id').val()) {
					if (jQuery('.sgrb-review-type').val() == 2 && reviewType == 'product_review'
						|| jQuery('.sgrb-review-type').val() == 1 && reviewType == 'simple_review'
						|| jQuery('.sgrb-review-type').val() == 3 && reviewType == 'social_review'
						|| jQuery('.sgrb-review-type').val() == 4 && reviewType == 'post_review'
						|| jQuery('.sgrb-review-type').val() == 5 && reviewType == 'woo_review') {
						SGReview.prototype.templateTabReordering(reviewType);
					}
					else {
						/* if change type of created review */
						alert('You cannot change the review type');
						return;
					}
				}
				else {
					SGReview.prototype.templateTabReordering(reviewType);
				}
			}
			else {
				alert('Title field is required');
				jQuery('.sgrb-text-input.sgrb-title-input').focus();
				jQuery('.sgrb-text-input.sgrb-title-input').css('background-color', '#ffbebe');
				return;
			}
		}
	});
};

SGWizardSettings.prototype.nextTab = function (elem) {
	if (!jQuery('.sgrb-review-type').val()) {
		alert('Select review type');
		return;
	}
	if (jQuery(elem).find('a[data-toggle="tab"]').attr('aria-controls').slice(-1) < 6) {
		var pageNum = jQuery(elem).next().find('a[data-toggle="tab"]').attr('aria-controls').slice(-1);
	}
	else {
		var pageNum = 6;
	}
	jQuery('.sgrb-wizard-current-page').val(pageNum);
	SGWizardSettings.prototype.nextPreviousButtonHideShow(pageNum);
	jQuery(elem).next().find('a[data-toggle="tab"]').click();
};

SGWizardSettings.prototype.prevTab = function (elem) {
	if (!jQuery('.sgrb-review-type').val()) {
		alert('Select review type');
		return;
	}
	var pageNum = jQuery(elem).prev().find('a[data-toggle="tab"]').attr('aria-controls').slice(-1);
	jQuery('.sgrb-wizard-current-page').val(pageNum);
	SGWizardSettings.prototype.nextPreviousButtonHideShow(pageNum);
	jQuery(elem).prev().find('a[data-toggle="tab"]').click();
};

SGWizardSettings.prototype.nextPreviousButtonHideShow = function (currentPage) {
	jQuery('.sgrb-simple-review-categories-wrapper').hide();
	var selectedReviewType = jQuery('.sgrb-review-type').val();
	var skipButton = jQuery('.sgrb-skip-step');
	var previousButton = jQuery('.sgrb-previous-step');
	var saveContinueButton = jQuery('.sgrb-next-step');
	var isEdit = jQuery('.sgrb-id').val();/* isEdit = review id (if is edit) */

	var allowToSaveForm = false;
	if (currentPage == 1) {
		allowToSaveForm = false;
		this.nextPreviousButtonsTab1(isEdit, selectedReviewType, skipButton, previousButton, saveContinueButton);
	}
	else if (currentPage == 2) {
		allowToSaveForm = false;
		this.nextPreviousButtonsTab2(isEdit, selectedReviewType, skipButton, previousButton, saveContinueButton);
	}
	else if (currentPage == 3) {
		allowToSaveForm = false;
		this.nextPreviousButtonsTab3(isEdit, selectedReviewType, skipButton, previousButton, saveContinueButton);
	}
	else if (currentPage == 4 || currentPage == 5) {
		allowToSaveForm = false;
		this.nextPreviousButtonsTab4(isEdit, selectedReviewType, skipButton, previousButton, saveContinueButton);
	}
	else if (currentPage == 6) {
		allowToSaveForm = true;
		this.nextPreviousButtonsTab6(isEdit, selectedReviewType, skipButton, previousButton, saveContinueButton);
	}
	jQuery('.sgrb-wizard-current-page').val(currentPage);

	if (allowToSaveForm && !isEdit) {
		jQuery('.sgrb-next-step').text('Save');
		jQuery('.sgrb-next-step').addClass('sgrb-review-js-update');
		jQuery('.sgrb-next-step').removeClass('next-step sgrb-next-step');
		jQuery('.sgrb-review-js-update').on('click', function(){
			SGReview.prototype.save();
		});
	}
};

SGWizardSettings.prototype.nextPreviousButtonsTab1 = function(isEdit, selectedReviewType, skipButton, previousButton, saveContinueButton) {
	jQuery('#sgrb-wizard-step-3').removeAttr('style');
	jQuery('.sgrb-text-input.sgrb-title-input').on('change keyup keydown', function(){
		if (jQuery(this).val().replace(/\s/g, "").length <= 0) {
			jQuery('.sgrb-text-input.sgrb-title-input').css('background-color', '#ffbebe');
		}
		else {
			jQuery('.sgrb-text-input.sgrb-title-input').css('background-color', '#ffffff');
		}
	});
	/* if edit, has selected review type */
	var emptyTitle = jQuery('.sgrb-text-input.sgrb-title-input');
	if (emptyTitle.length) {
		if (emptyTitle.val() != '') {
			if (selectedReviewType) {
				if (isEdit) {
					previousButton.addClass('disabled');
					skipButton.removeClass('disabled');
					saveContinueButton.removeClass('disabled');
				}
				previousButton.addClass('disabled');
				skipButton.removeClass('disabled');
				saveContinueButton.removeClass('disabled');
			}
			else if (!selectedReviewType) {
				previousButton.addClass('disabled');
				skipButton.addClass('disabled');
				saveContinueButton.addClass('disabled');
			}
			saveContinueButton.text('Continue');
		}
		else {
			previousButton.addClass('disabled');
			skipButton.addClass('disabled');
			saveContinueButton.addClass('disabled');
		}
	}
};

SGWizardSettings.prototype.nextPreviousButtonsTab2 = function(isEdit, selectedReviewType, skipButton, previousButton, saveContinueButton) {
	/* if selected review type equal to 1 (1 = simple review) */
	jQuery('#sgrb-wizard-step-3').attr('style', 'pointer-events:none;');
	if (selectedReviewType && (selectedReviewType == 1 || selectedReviewType == 3)) {
		if (isEdit) {
			previousButton.removeClass('disabled');
			skipButton.removeClass('disabled');
			saveContinueButton.removeClass('disabled');
		}
		SGReviewHelper.prototype.uploadImageButton(1);
		SGReviewHelper.prototype.removeImageButton(1);
		previousButton.removeClass('disabled');
		skipButton.removeClass('disabled');
		saveContinueButton.removeClass('disabled');
		this.checkSimpleSocialReviewSettings(selectedReviewType, skipButton, saveContinueButton);
	}
	/* if selected review type equal to 2 (2 = product review) */
	else if (selectedReviewType && selectedReviewType == 2) {
		/*if (isEdit) {

		}*/
		jQuery('.sgrb-image-review').each(function(){
			if ((jQuery(this).attr('style') == 'background-image:url();') || (jQuery(this).attr('style') == '')) {
				jQuery(this).attr('style', 'border: 2px dashed #ccc;border-radius: 10px;');
			}
		});
		SGReviewHelper.prototype.uploadImageButton();
		SGReviewHelper.prototype.removeImageButton();
		previousButton.removeClass('disabled');
		skipButton.removeClass('disabled');
		saveContinueButton.removeClass('disabled');
	}
	else if (selectedReviewType && (selectedReviewType == 4 || selectedReviewType == 5)) {
		previousButton.removeClass('disabled');
		skipButton.removeClass('disabled');
		saveContinueButton.removeClass('disabled');
	}
	saveContinueButton.text('Continue');
};

SGWizardSettings.prototype.nextPreviousButtonsTab3 = function(isEdit, selectedReviewType, skipButton, previousButton, saveContinueButton) {
	/* if selected review type equal to 1 (1 = simple review) */
	jQuery('#sgrb-wizard-step-3').removeAttr('style');
	if (selectedReviewType && selectedReviewType == 1) {
		if (isEdit) {
			previousButton.removeClass('disabled');
			skipButton.removeClass('disabled');
			saveContinueButton.removeClass('disabled');
		}
		else {
			previousButton.removeClass('disabled');
			if (!jQuery('.sgrb-simple-review-categories-wrapper').find('.sgrb-field-name').val()) {
				jQuery('#sgrb-wizard-step-4').addClass('disabled');
				skipButton.addClass('disabled');
				saveContinueButton.addClass('disabled');
			}
		}
		jQuery('.sgrb-main-review-categories-wrapper').hide();
		jQuery('.sgrb-simple-review-categories-wrapper').show();
		/* if is empty or not category field */
		jQuery('.sgrb-field-name').on('change keyup keydown', function(){
			if (jQuery(this).val().replace(/\s/g, "").length <= 0) {
				jQuery('#sgrb-wizard-step-4').addClass('disabled');
				skipButton.addClass('disabled');
				saveContinueButton.addClass('disabled');
			}
			else {
				jQuery('#sgrb-wizard-step-4').removeClass('disabled');
				skipButton.removeClass('disabled');
				saveContinueButton.removeClass('disabled');
			}
		});
	}
	/* if selected review type equal to 2 (2 = product review) */
	else if (selectedReviewType && (selectedReviewType == 2 || selectedReviewType == 3 || selectedReviewType == 4 || selectedReviewType == 5)) {
		if (isEdit) {
			previousButton.removeClass('disabled');
			skipButton.removeClass('disabled');
			saveContinueButton.removeClass('disabled');
		}
		else {
			if (selectedReviewType == 3) {
				jQuery('.sgrb-hide-options-for-social-review-js').hide();
			}
			else {
				jQuery('.sgrb-hide-options-for-social-review-js').show();
			}
			previousButton.removeClass('disabled');
			if (!jQuery('.sgrb-main-review-categories-wrapper').find('.sgrb-field-name').val()) {
				jQuery('#sgrb-wizard-step-4').addClass('disabled');
				skipButton.addClass('disabled');
				saveContinueButton.addClass('disabled');
			}
		}
		jQuery('.sgrb-main-review-categories-wrapper').show();
		jQuery('.sgrb-simple-review-categories-wrapper').hide();
		if (!isEdit) {
			/* if is empty or not category fields */
			jQuery('.sgrb-field-name').each(function(){
				jQuery(this).on('change keyup keydown', function(){
					if (jQuery(this).val().replace(/\s/g, "").length <= 0) {
						jQuery('#sgrb-wizard-step-4').addClass('disabled');
						skipButton.addClass('disabled');
						saveContinueButton.addClass('disabled');
					}
					else {
						jQuery('#sgrb-wizard-step-4').removeClass('disabled');
						skipButton.removeClass('disabled');
						saveContinueButton.removeClass('disabled');
					}
				});
			});
		}
	}
	saveContinueButton.text('Continue');
};

SGWizardSettings.prototype.nextPreviousButtonsTab4 = function(isEdit, selectedReviewType, skipButton, previousButton, saveContinueButton) {
	var hideButtons = false;
	jQuery('#sgrb-wizard-step-3').removeAttr('style');
	if (selectedReviewType && selectedReviewType == 2) {/* template customize shadow option (only for product review) */
		jQuery('.sgrb-shadow-left-right-top-bottom-js').each(function(){
			if (jQuery(this).val() == '' || jQuery(this).val() == 0) {
				hideButtons = true;
			}
		});
		jQuery('#sgrb-template-shadow-on').on('change', function(){
			if (jQuery(this).is(':checked')) {
				jQuery('.sgrb-template-shadow-options-js').removeClass('sgrb-disabled');

				jQuery('.sgrb-shadow-left-right-top-bottom-js').each(function(){
					if (jQuery(this).val() == '' || jQuery(this).val() == 0) {
						hideButtons = true;
					}
					else {
						hideButtons = false;
					}
				});
				if (hideButtons) {
					skipButton.addClass('disabled');
					saveContinueButton.addClass('disabled');
					jQuery('.sgrb-review-save-button').addClass('disabled');
				}
				else {
					skipButton.removeClass('disabled');
					saveContinueButton.removeClass('disabled');
					jQuery('.sgrb-review-save-button').removeClass('disabled');
				}
			}
			else {
				jQuery('.sgrb-template-shadow-options-js').addClass('sgrb-disabled');
				skipButton.removeClass('disabled');
				saveContinueButton.removeClass('disabled');
				jQuery('.sgrb-review-save-button').removeClass('disabled');
			}
		});
		jQuery('.sgrb-shadow-left-right-top-bottom-js').on('change keydown keyup', function(){
			jQuery('.sgrb-shadow-left-right-top-bottom-js').each(function(){
				if (jQuery(this).val() == '') {
					hideButtons = true;
				}
				else {
					hideButtons = false;
				}
			});
			if (hideButtons) {
				skipButton.addClass('disabled');
				saveContinueButton.addClass('disabled');
				jQuery('.sgrb-review-save-button').addClass('disabled');
			}
			else {
				skipButton.removeClass('disabled');
				saveContinueButton.removeClass('disabled');
				jQuery('.sgrb-review-save-button').removeClass('disabled');
			}
		});
	}
	else {
		previousButton.removeClass('disabled');
		skipButton.removeClass('disabled');
		saveContinueButton.removeClass('disabled');
		saveContinueButton.text('Continue');
	}
};

SGWizardSettings.prototype.nextPreviousButtonsTab5 = function(isEdit, selectedReviewType, skipButton, previousButton, saveContinueButton) {
	jQuery('#sgrb-wizard-step-3').removeAttr('style');

	previousButton.removeClass('disabled');
	skipButton.removeClass('disabled');
	saveContinueButton.removeClass('disabled');
	saveContinueButton.text('Continue');
};

SGWizardSettings.prototype.nextPreviousButtonsTab6 = function(isEdit, selectedReviewType, skipButton, previousButton, saveContinueButton) {
	jQuery('#sgrb-wizard-step-3').removeAttr('style');

	previousButton.removeClass('disabled');
	skipButton.addClass('disabled');
	saveContinueButton.removeClass('disabled');
	saveContinueButton.text('Save');
};

/*SGWizardSettings.prototype.checkSimpleReviewSettings = function(skipButton, saveContinueButton) {
	jQuery('.sgrb-sample-template-type').on('change', function(){
		var simpleTemplateType = jQuery(this).val();
		if (simpleTemplateType == 'empty') {
			jQuery('#sgrb-sample-template-type-image').removeAttr('checked');
			jQuery('#sgrb-sample-template-type-text').removeAttr('checked');
			jQuery('.sgrb-sample-template-text').val('');
			skipButton.removeClass('disabled');
			saveContinueButton.removeClass('disabled');
			jQuery('.sgrb-review-save-button').removeClass('disabled');
		}
		else if (simpleTemplateType == 'text') {
			jQuery('#sgrb-sample-template-type-empty').removeAttr('checked');
			jQuery('#sgrb-sample-template-type-image').removeAttr('checked');
			var textTypeValue = jQuery('.sgrb-sample-template-text');
			if (textTypeValue.val().replace(/\s/g, "").length <= 0) {
				skipButton.addClass('disabled');
				saveContinueButton.addClass('disabled');
				jQuery('.sgrb-review-save-button').addClass('disabled');
			}
			else {
				skipButton.removeClass('disabled');
				saveContinueButton.removeClass('disabled');
				jQuery('.sgrb-review-save-button').removeClass('disabled');
			}
			textTypeValue.on('change keydown keyup', function(){
				if (jQuery(this).val().replace(/\s/g, "").length <= 0) {
					skipButton.addClass('disabled');
					saveContinueButton.addClass('disabled');
					jQuery('.sgrb-review-save-button').addClass('disabled');
				}
				else {
					skipButton.removeClass('disabled');
					saveContinueButton.removeClass('disabled');
					jQuery('.sgrb-review-save-button').removeClass('disabled');
				}
			});

		}
		else if (simpleTemplateType == 'image') {
			jQuery('.sgrb-sample-template-text').val('');
			jQuery('#sgrb-sample-template-type-text').removeAttr('checked');
			jQuery('#sgrb-sample-template-type-empty').removeAttr('checked');
			if (jQuery('#sgrb_image_url_simple').val() == '') {
				skipButton.addClass('disabled');
				saveContinueButton.addClass('disabled');
				jQuery('.sgrb-review-save-button').addClass('disabled');
			}
		}
		if (!jQuery(this).attr('checked')) {
			jQuery('.sgrb-sample-template-default-value').attr('checked', 'checked');
		}
	});
};*/

SGWizardSettings.prototype.checkSimpleSocialReviewSettings = function(reviewType, skipButton, saveContinueButton) {
	/* simple review = 1; social review = 3; */
	var classIndex = 'sample';
	jQuery('.sgrb-'+classIndex+'-template-type').on('change', function(){
		var simpleTemplateType = jQuery(this).val();
		if (simpleTemplateType == 'empty') {
			jQuery('#sgrb-'+classIndex+'-template-type-image').removeAttr('checked');
			jQuery('#sgrb-'+classIndex+'-template-type-text').removeAttr('checked');
			jQuery('.sgrb-'+classIndex+'-template-text').val('');
			skipButton.removeClass('disabled');
			saveContinueButton.removeClass('disabled');
			jQuery('.sgrb-review-save-button').removeClass('disabled');
		}
		else if (simpleTemplateType == 'text') {
			jQuery('#sgrb-'+classIndex+'-template-type-empty').removeAttr('checked');
			jQuery('#sgrb-'+classIndex+'-template-type-image').removeAttr('checked');
			var textTypeValue = jQuery('.sgrb-'+classIndex+'-template-text');
			if (textTypeValue.val().replace(/\s/g, "").length <= 0) {
				skipButton.addClass('disabled');
				saveContinueButton.addClass('disabled');
				jQuery('.sgrb-review-save-button').addClass('disabled');
			}
			else {
				skipButton.removeClass('disabled');
				saveContinueButton.removeClass('disabled');
				jQuery('.sgrb-review-save-button').removeClass('disabled');
			}
			textTypeValue.on('change keydown keyup', function(){
				if (jQuery(this).val().replace(/\s/g, "").length <= 0) {
					skipButton.addClass('disabled');
					saveContinueButton.addClass('disabled');
					jQuery('.sgrb-review-save-button').addClass('disabled');
				}
				else {
					skipButton.removeClass('disabled');
					saveContinueButton.removeClass('disabled');
					jQuery('.sgrb-review-save-button').removeClass('disabled');
				}
			});

		}
		else if (simpleTemplateType == 'image') {
			jQuery('.sgrb-'+classIndex+'-template-text').val('');
			jQuery('#sgrb-'+classIndex+'-template-type-text').removeAttr('checked');
			jQuery('#sgrb-'+classIndex+'-template-type-empty').removeAttr('checked');
			if (jQuery('#sgrb_image_url_'+classIndex+'').val() == '') {
				skipButton.addClass('disabled');
				saveContinueButton.addClass('disabled');
				jQuery('.sgrb-review-save-button').addClass('disabled');
			}
		}
		if (!jQuery(this).attr('checked')) {
			jQuery('.sgrb-'+classIndex+'-template-default-value').attr('checked', 'checked');
		}
	});
};
