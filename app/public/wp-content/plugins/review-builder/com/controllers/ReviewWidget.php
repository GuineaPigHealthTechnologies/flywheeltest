<?php
global $sgrb;
$sgrb->includeController('Controller');
$sgrb->includeCore('Template');
$sgrb->includeLib('Input');
$sgrb->includeView('Admin');
$sgrb->includeView('Review');
$sgrb->includeView('TemplateDesign');
$sgrb->includeModel('TemplateDesign');
$sgrb->includeModel('Review');
$sgrb->includeModel('Comment');
$sgrb->includeModel('CommentForm');
$sgrb->includeModel('Template');
$sgrb->includeModel('Category');
$sgrb->includeModel('Comment_Rating');
$sgrb->includeModel('Rate_Log');
$sgrb->includeModel('Page_Review');
$sgrb->includeCore('StyleScriptLoader');

class SGRB_ReviewWidgetController extends SGRB_Controller
{

	public function sgrbWidgetShortcode($atts, $content)
	{
		global $sgrb;

		$attributes = shortcode_atts(array(
			'id' => '1',
			'link' => '#'
		), $atts);
		$sgrbId = (int)$attributes['id'];
		$sgrbReviewLink = $attributes['link'];
		$sgrbRev = SGRB_ReviewModel::finder()->findByPk($sgrbId);
		if(!$sgrbRev){
			return false;
		}
		$arr = array();
		$title = $sgrbRev->getTitle();
		$templateId = $sgrbRev->getTemplate_id();
		$options = $sgrbRev->getOptions();
		$template = SGRB_TemplateModel::finder()->findByPk($templateId);

		$arr['title'] = $title;
		$arr['id'] = $sgrbId;
		$arr['link'] = $sgrbReviewLink;
		$arr['template-id'] = $templateId;
		$arr['options'] = json_decode($options,true);
		$arr['template'] = $template;
		$sgrbDataArray[] = $arr;

		$html = $this->createFrontWidgetReviewHtml($sgrbDataArray);
		return $html;
	}

	public function createFrontWidgetReviewHtml($review, $isWidget=false)
	{
		global $sgrb;
		$mainHtml = '';

		if (!SGRB_PRO_VERSION) {
			return $mainHtml;
		}
		SGRB_StyleScriptLoader::prepare('ReviewWidget', 'createFrontWidgetReviewHtml');

		if (!$review) {
			return false;
		}
		$userIps = array();
		$newUser = false;
		$reviewMainOptions = $review[0]['options'];
		SGRB_Input::setSource($reviewMainOptions);

		$ratesArray = array();
		$reviewTitle = @$review[0]['title'];
		$widgetLink = @$review[0]['link'];
		$widgetLinkAddText = SGRB_Input::get('widget-link-add-text');
		$widgetLinkEditText = SGRB_Input::get('widget-link-edit-text');

		$categories = SGRB_CategoryModel::finder()->findAll('review_id = %d', $review[0]['id']);
		$commentRatingModel = new SGRB_Comment_RatingModel();
		$commentTablename = $sgrb->tablename($commentRatingModel::TABLE);

		if ($reviewMainOptions['total-rate'] || SGRB_PRO_VERSION) {
			$countRates = 0;
			foreach ($categories as $category) {
				$approvedComments = SGRB_CommentModel::finder()->findAll('review_id = %d && approved = %d', array($review[0]['id'], 1));
				$sgrbIndex = 0;
				foreach ($approvedComments as $approvedComment) {
					$sgrbIndex++;
					$rates = SGRB_Comment_RatingModel::finder()->findAll('category_id = %d && comment_id = %d', array($category->getId(), $approvedComment->getId()));
					$eachRates = SGRB_Comment_RatingModel::finder()->findBySql('SELECT AVG(rate) AS average, category_id FROM '.$commentTablename.' WHERE category_id='.$category->getId().' GROUP BY category_id');
					$ratesArray[] = $rates;
					$eachRatesArray[$category->getId()][] = $eachRates;
				}
			}
			$countRates = 0;
			$rating = 0;

			foreach ($ratesArray as $rate) {
				$countRates += 1;
				if (!empty($rate)) {
					$rating += $rate[0]->getRate();
				}
			}
			if (!$countRates) {
				$totalRate = 0;
			}
			else {
				$totalRate = round($rating / $countRates);
			}

		}

		$userIps = SGRB_Rate_LogModel::finder()->findAll('review_id = %d', $review[0]['id']);
		if (empty($userIps)) {
			$newUser = true;
		}
		foreach ($userIps as $userIp) {
			if (SGRB_ReviewController::getClientIpAddress() == $userIp->getIp()) {
				$newUser = false;
				$currentCommentId = $userIp->getComment_id();
				if (!$currentCommentId) {
					$newUser = true;
				}
			}
		}
		if ($newUser) {
			$widgetLinkText = $widgetLinkAddText;
		}
		else {
			$widgetLinkText = $widgetLinkEditText;
		}

		$mainHtml .= $this->prepareWidgetHtml($reviewTitle, $widgetLink, $widgetLinkText, $totalRate, $approvedComments);

		return $mainHtml;
	}

	public function prepareWidgetHtml($reviewTitle, $widgetLink, $widgetLinkText, $totalRate, $approvedComments)
	{
		$html = '';
		$html .= '<div class="sgrb-widgets-wrapper">
						<div class="sg-row">
							<div class="sg-col-12 sgrb-widget-center-align-text">
								'.$reviewTitle.'
							</div>
						</div>
						<div class="sg-row">
							<div class="sg-col-6">
								<div class="sgrb-widget-total-rate"></div>
								<input type="hidden" value="'.$totalRate.'">
							</div>
							<div class="sg-col-6">
								<span class="sgrb-widget-total-rate-text">'.$totalRate.' out of 5 stars</span>
							</div>
						</div>
						<div class="sg-row">
							<div class="sg-col-6 sgrb-widget-center-align-text">
								<span class="sgrb-widget-count-text">'.count($approvedComments).' reviews</span>
							</div>';
							if ($widgetLink && $widgetLink != '#') {
								$html .= '<div class="sg-col-6">
										<a href="'.$widgetLink.'" id="sgrb-widget-leave-a-review-link">'.$widgetLinkText.'</a>
									</div>';
							}

						$html .= '</div>
					</div>';
		return $html;
	}
}
