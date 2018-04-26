<?php

class SGRB_StyleScriptLoader
{
	private static $controller;
	private static $action;

	public static function prepare($controller, $action)
	{
		$controllerActionList = array('Review'     => array('index', 'edit', 'reviewSetting', 'createReviewHtml'),
									'CommentForm'  => array('index', 'edit'),
									'Comment'      => array('index', 'save'),
									'ReviewWidget' => array('createFrontWidgetReviewHtml'),
									'TemplateDesign' => array('index', 'save')
								);

		if (!$controller) {
			$controller = 'Review';
			$action = 'index';
		}
		if ($controller == 'Review') {
			self::includeReviewCssJs($action);
		}
		else if ($controller == 'Comment') {
			self::includeCommentCssJs($action);
		}
		else if ($controller == 'CommentForm') {
			self::includeCommentFormCssJs($action);
		}
		else if ($controller == 'ReviewWidget') {
			self::includeReviewWidgetCssJs($action);
		}
		else if ($controller == 'TemplateDesign') {
			self::includeTemplateDesignCssJs($action);
		}
	}

	/*Review Controller css/js*/
	public static function includeReviewCssJs($action)
	{
		global $sgrb;
		if (SGRB_PRO_VERSION && ($action == 'edit' || $action == 'createReviewHtml')) {
			$sgrb->includeStyle('page/styles/general/bootstrap-formhelpers.min');
			$sgrb->includeScript('page/scripts/helpers/bootstrap-formhelpers.min');
		}
		if ($action == 'index') {
			$sgrb->includeStyle('page/styles/bootstrapTheme');
			$sgrb->includeScript('page/scripts/helpers/sgReviewHelper');
			$sgrb->includeScript('page/scripts/helpers/sgTemplateHelper');
			$sgrb->includeScript('page/scripts/helpers/sgCommentHelper');
			$sgrb->includeScript('page/scripts/helpers/sgRateSkin');
			$sgrb->includeScript('page/scripts/helpers/sgMainHelper');
			$sgrb->includeScript('page/scripts/helpers/sgWizardSettings');
			$sgrb->includeScript('page/scripts/sgReview');
			$sgrb->includeScript('page/scripts/sgComment');
			$sgrb->includeScript('page/scripts/sgTemplate');
			$sgrb->includeScript('page/scripts/sgForm');
			$sgrb->includeScript('core/scripts/main');
			$sgrb->includeScript('core/scripts/sgrbRequestHandler');
		}
		else if ($action == 'edit' || $action == 'reviewSetting') {
			$sgrb->includeStyle('core/styles/css/jquery.rateyo');
			$sgrb->includeStyle('core/styles/css/bars-1to10');
			$sgrb->includeStyle('core/styles/css/jquery-ui.min');
			$sgrb->includeStyle('core/styles/css/jquery-ui-slider-pips.min');
			$sgrb->includeStyle('page/styles/general/sg-box-cols');
			$sgrb->includeStyle('page/styles/bootstrap.min');
			$sgrb->includeStyle('page/styles/animate');
			$sgrb->includeStyle('page/styles/general');
			$sgrb->includeStyle('page/styles/font-awesome.min');
			$sgrb->includeStyle('page/styles/bootstrapTheme');
			$sgrb->includeStyle('page/styles/review/save');
			$sgrb->includeStyle('page/styles/general/sgrbWizardSettings');
			$sgrb->includeScript('core/scripts/jquery.rateyo');
			$sgrb->includeScript('core/scripts/jquery.barrating');
			$sgrb->includeScript('core/scripts/jquery-ui.min');
			$sgrb->includeScript('core/scripts/jquery-ui-slider-pips.min');
			$sgrb->includeScript('page/scripts/helpers/sgReviewHelper');
			$sgrb->includeScript('page/scripts/helpers/sgTemplateHelper');
			$sgrb->includeScript('page/scripts/helpers/sgCommentHelper');
			$sgrb->includeScript('page/scripts/helpers/sgRateSkin');
			$sgrb->includeScript('page/scripts/helpers/sgMainHelper');
			$sgrb->includeScript('page/scripts/helpers/sgWizardSettings');
			$sgrb->includeScript('page/scripts/sgReview');
			$sgrb->includeScript('page/scripts/sgComment');
			$sgrb->includeScript('page/scripts/sgTemplate');
			$sgrb->includeScript('page/scripts/sgForm');
			$sgrb->includeScript('page/scripts/bootstrap.min');
			$sgrb->includeScript('core/scripts/main');
			$sgrb->includeScript('core/scripts/sgrbRequestHandler');
		}
		else if ($action == 'createReviewHtml') {
			$sgrb->includeStyle('core/styles/css/main-front');
			$sgrb->includeStyle('page/styles/general/sg-box-cols');
			$sgrb->includeScript('page/scripts/helpers/sgRateSkin');
			$sgrb->includeScript('page/scripts/helpers/sgWizardSettings');
			$sgrb->includeScript('page/scripts/helpers/sgReviewHelper');
			$sgrb->includeScript('page/scripts/helpers/sgTemplateHelper');
			$sgrb->includeScript('page/scripts/helpers/sgCommentHelper');
			$sgrb->includeScript('page/scripts/helpers/sgMainHelper');
			$sgrb->includeScript('page/scripts/sgReview');
			$sgrb->includeScript('page/scripts/sgComment');
			$sgrb->includeScript('page/scripts/sgTemplate');
			$sgrb->includeScript('page/scripts/sgForm');
			$sgrb->includeScript('core/scripts/main');
			$sgrb->includeScript('core/scripts/sgrbRequestHandler');
		}

	}

	/*CommentForm Controller css/js*/
	public static function includeCommentFormCssJs($action)
	{
		global $sgrb;
		if ($action == 'index') {
			$sgrb->includeScript('page/scripts/helpers/sgReviewHelper');
			$sgrb->includeScript('page/scripts/helpers/sgTemplateHelper');
			$sgrb->includeScript('page/scripts/helpers/sgCommentHelper');
			$sgrb->includeScript('page/scripts/helpers/sgRateSkin');
			$sgrb->includeScript('page/scripts/helpers/sgMainHelper');
			$sgrb->includeScript('page/scripts/helpers/sgWizardSettings');
			$sgrb->includeScript('page/scripts/sgReview');
			$sgrb->includeScript('page/scripts/sgComment');
			$sgrb->includeScript('page/scripts/sgTemplate');
			$sgrb->includeScript('page/scripts/sgForm');
			$sgrb->includeScript('core/scripts/sgrbRequestHandler');
			$sgrb->includeStyle('page/styles/bootstrapTheme');
		}
		else if ($action == 'edit') {
			$sgrb->includeScript('page/scripts/helpers/sgReviewHelper');
			$sgrb->includeScript('core/scripts/jquery.rateyo');
			$sgrb->includeScript('page/scripts/helpers/sgTemplateHelper');
			$sgrb->includeScript('page/scripts/helpers/sgCommentHelper');
			$sgrb->includeScript('page/scripts/helpers/sgRateSkin');
			$sgrb->includeScript('page/scripts/helpers/sgMainHelper');
			$sgrb->includeScript('page/scripts/helpers/sgWizardSettings');
			$sgrb->includeScript('page/scripts/sgReview');
			$sgrb->includeScript('page/scripts/sgComment');
			$sgrb->includeScript('page/scripts/sgTemplate');
			$sgrb->includeScript('page/scripts/sgForm');
			$sgrb->includeScript('core/scripts/main');
			$sgrb->includeScript('core/scripts/sgrbRequestHandler');
			$sgrb->includeStyle('page/styles/commentForm/save');
			$sgrb->includeStyle('page/styles/general/sg-box-cols');
		}
	}

	/*Comment Controller css/js*/
	public static function includeCommentCssJs($action)
	{
		global $sgrb;
		if ($action == 'index') {
			$sgrb->includeScript('page/scripts/helpers/sgReviewHelper');
			$sgrb->includeScript('page/scripts/helpers/sgTemplateHelper');
			$sgrb->includeScript('page/scripts/helpers/sgCommentHelper');
			$sgrb->includeScript('page/scripts/helpers/sgRateSkin');
			$sgrb->includeScript('page/scripts/helpers/sgMainHelper');
			$sgrb->includeScript('page/scripts/helpers/sgWizardSettings');
			$sgrb->includeScript('page/scripts/sgReview');
			$sgrb->includeScript('page/scripts/sgComment');
			$sgrb->includeScript('page/scripts/sgTemplate');
			$sgrb->includeScript('page/scripts/sgForm');
			$sgrb->includeScript('core/scripts/main');
			$sgrb->includeScript('core/scripts/sgrbRequestHandler');
			$sgrb->includeStyle('page/styles/comment/save');
			$sgrb->includeStyle('page/styles/bootstrapTheme');
		}
		else if ($action == 'save') {
			$sgrb->includeScript('page/scripts/helpers/sgReviewHelper');
			$sgrb->includeScript('page/scripts/helpers/sgTemplateHelper');
			$sgrb->includeScript('page/scripts/helpers/sgCommentHelper');
			$sgrb->includeScript('page/scripts/helpers/sgRateSkin');
			$sgrb->includeScript('page/scripts/helpers/sgMainHelper');
			$sgrb->includeScript('page/scripts/helpers/sgWizardSettings');
			$sgrb->includeScript('page/scripts/sgReview');
			$sgrb->includeScript('page/scripts/sgComment');
			$sgrb->includeScript('page/scripts/sgTemplate');
			$sgrb->includeScript('page/scripts/sgForm');
			$sgrb->includeStyle('page/styles/comment/save');
			$sgrb->includeStyle('page/styles/general/sg-box-cols');
			$sgrb->includeScript('core/scripts/main');
			$sgrb->includeScript('core/scripts/sgrbRequestHandler');
		}
	}

	/*ReviewWidget Controller css/js*/
	public static function includeReviewWidgetCssJs($action)
	{
		global $sgrb;
		if ($action == 'createFrontWidgetReviewHtml') {
			if (SGRB_PRO_VERSION) {
				$sgrb->includeStyle('page/styles/general/bootstrap-formhelpers.min');
				$sgrb->includeScript('page/scripts/helpers/bootstrap-formhelpers.min');
			}
			$sgrb->includeStyle('core/styles/css/main-front');
			$sgrb->includeStyle('page/styles/general/sg-box-cols');

			$sgrb->includeScript('page/scripts/helpers/sgRateSkin');
			$sgrb->includeScript('page/scripts/helpers/sgWizardSettings');
			$sgrb->includeScript('page/scripts/helpers/sgReviewHelper');
			$sgrb->includeScript('page/scripts/helpers/sgTemplateHelper');
			$sgrb->includeScript('page/scripts/helpers/sgCommentHelper');
			$sgrb->includeScript('page/scripts/helpers/sgMainHelper');
			$sgrb->includeScript('page/scripts/sgReview');
			$sgrb->includeScript('page/scripts/sgComment');
			$sgrb->includeScript('page/scripts/sgTemplate');
			$sgrb->includeScript('page/scripts/sgForm');
			$sgrb->includeScript('core/scripts/main');
			$sgrb->includeScript('core/scripts/sgrbRequestHandler');
			$sgrb->includeScript('core/scripts/jquery.rateyo');
			$sgrb->includeStyle('core/styles/css/jquery.rateyo');
		}
	}

	/*TemplateDesign Controller css/js*/
	public static function includeTemplateDesignCssJs($action)
	{
		global $sgrb;
		if ($action == 'index') {
			$sgrb->includeScript('page/scripts/helpers/sgReviewHelper');
			$sgrb->includeScript('page/scripts/helpers/sgTemplateHelper');
			$sgrb->includeScript('page/scripts/helpers/sgCommentHelper');
			$sgrb->includeScript('page/scripts/helpers/sgRateSkin');
			$sgrb->includeScript('page/scripts/helpers/sgMainHelper');
			$sgrb->includeScript('page/scripts/helpers/sgWizardSettings');
			$sgrb->includeScript('page/scripts/sgReview');
			$sgrb->includeScript('page/scripts/sgComment');
			$sgrb->includeScript('page/scripts/sgTemplate');
			$sgrb->includeScript('page/scripts/sgForm');
			$sgrb->includeStyle('page/styles/review/save');
			$sgrb->includeScript('core/scripts/main');
			$sgrb->includeScript('core/scripts/sgrbRequestHandler');
			$sgrb->includeStyle('page/styles/bootstrapTheme');
		}
		else if ($action == 'save') {
			$sgrb->includeStyle('page/styles/review/save');
			$sgrb->includeStyle('page/styles/comment/save');
			$sgrb->includeStyle('page/styles/general/sg-box-cols');
			$sgrb->includeScript('page/scripts/helpers/sgReviewHelper');
			$sgrb->includeScript('page/scripts/helpers/sgTemplateHelper');
			$sgrb->includeScript('page/scripts/helpers/sgCommentHelper');
			$sgrb->includeScript('page/scripts/helpers/sgRateSkin');
			$sgrb->includeScript('page/scripts/helpers/sgMainHelper');
			$sgrb->includeScript('page/scripts/helpers/sgWizardSettings');
			$sgrb->includeScript('page/scripts/sgReview');
			$sgrb->includeScript('page/scripts/sgComment');
			$sgrb->includeScript('page/scripts/sgTemplate');
			$sgrb->includeScript('page/scripts/sgForm');
			$sgrb->includeScript('core/scripts/main');
			$sgrb->includeScript('core/scripts/sgrbRequestHandler');
		}
	}
}

?>
