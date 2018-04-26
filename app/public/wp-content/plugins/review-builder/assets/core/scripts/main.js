(function(){
	jQuery(document).ready( function(){
		'use strict';
		var sgrb = new SGRB();
		var sgRateSkin = new SGRateSkin();
		var sgReview = new SGReview();
		var sgReviewHelper = new SGReviewHelper();
		var sgWizardSettings = new SGWizardSettings();

		SGTemplate.prototype.init();
		SGTemplateHelper.prototype.init();
		SGMainHelper.prototype.init();
		SGComment.prototype.init();
		SGCommentHelper.prototype.init();
		SGForm.prototype.init();
	});
})();
