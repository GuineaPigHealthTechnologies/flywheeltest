<?php

global $sgrb;
$sgrb->includeLib('Review');
$sgrb->includeController('Review');
$sgrb->includeModel('Review');

class SGRB_ReviewReviewView extends SGRB_Review
{
	public function __construct()
	{
		parent::__construct('sgrb');
		$sgrbPro = '';
		if (!SGRB_PRO_VERSION) {
			$sgrbPro = '<i style="color:#ff0000"> (PRO) </i>';
		}

		$this->setRowsPerPage(10);
		$this->setTablename(SGRB_ReviewModel::TABLE);
		$this->setColumns(array(
			'id',
			'title',
			'type'
		));
		$this->setBulk();
		$this->setDisplayColumns(array(
			'sgrb-id' => 'ID',
			'title' => __('Title', 'sgrb'),
			'type' => __('Review type', 'sgrb'),
			'total_rate' => __('Total rate', 'sgrb'),
			'comment' => '<i class="vers comment-grey-bubble"></i>'.__('Comments', 'sgrb'),
			'shortcode' => 'Shortcode',
			'options' => 'Widget '.$sgrbPro

		));
		$this->setSortableColumns(array(
			'id' => array('id', false),
			'title' => array('title', true),
			'type' => array('type', false)
		));
		$this->setInitialSort(array(
			'id' => 'DESC'
		));

	}

	public function customizeRow(&$row)
	{
		global $sgrb;
		$id = $row[0];
		$reviewType = '';
		$socialStyles = '';
		$totalRate = 'no rates';
		$commentUrl = $sgrb->adminUrl('Comment/index','id='.$id);
		$comments = SGRB_CommentModel::finder()->findAll('review_id = %d', $id);
		$commentsCount = '';
		foreach ($comments as $val) {
			$commentsCount = count($comments);
			if ($commentsCount) {
				$commentsCount = '::'.$commentsCount;
			}
		}
		if ($row[2] == SGRB_REVIEW_TYPE_SIMPLE) {
			$reviewType = 'Simple';
		}
		else if ($row[2] == SGRB_REVIEW_TYPE_PRODUCT) {
			$reviewType = 'Product';
		}
		else if ($row[2] == SGRB_REVIEW_TYPE_SOCIAL) {
			$reviewType = 'Social';
			$socialStyles = 'style="pointer-events: none;cursor: default;color: #ccc;"';
		}
		else if ($row[2] == SGRB_REVIEW_TYPE_POST) {
			$reviewType = 'Post';
		}
		else if ($row[2] == SGRB_REVIEW_TYPE_WOO) {
			$reviewType = 'WooCommerce';
		}
		$row[2] = $reviewType;
		$totalRate = $this->getTotalRate($id);
		$row[3] = round($totalRate, 1);

		$row[4] = '<a '.$socialStyles.' href="'.$commentUrl.'">'.$commentsCount.'</a>';
		$editUrl = $sgrb->adminUrl('Review/edit','id='.$id);
		$row[5] = "<input type='text' onfocus='this.select();' style='font-size:12px;' readonly value='[sgrb_review id=".$id."]' class='sgrb-large-text code'>";
		if (SGRB_PRO_VERSION) {
			$row[6] = "<input type='text' onfocus='this.select();' style='font-size:12px;' readonly value='[sgrb_widget id=".$id."]' class='sgrb-large-text code'>";
		}
		else {
			$row[6] = "<input type='text' style='font-size:12px;color: #ff0000;' readonly value='[shortcode]' class='sgrb-large-text code'>";
		}
		$row[1] .= '<p class="sgrb-show-hide-option-links-js" style="margin:0;visibility:hidden;"><a href="'.$editUrl.'">'.__('Edit', 'sgrb').'</a>&nbsp;|&nbsp;
					<a href="#" onclick="SGReview.ajaxDelete('.$id.')">'.__('Delete', 'sgrb').'</a>&nbsp;|&nbsp;
					<a href="#" onclick="SGReview.prototype.ajaxCloneReview('.$id.')">'.__('Clone', 'sgrb').'</a></p>';
	}

	public function customizeQuery(&$query)
	{
		//$query .= ' LEFT JOIN wp_sgrb_comment ON wp_sgrb_comment.review_id='.$this->tablename.'.id';
	}

	public function getTotalRate($reviewId)
	{
		global $sgrb;
		$total = 0;
		$rateCount = 0;
		$currentReviewCategories = SGRB_CategoryModel::finder()->findAll('review_id = %d', $reviewId);
		foreach ($currentReviewCategories as $category) {
			$rates = SGRB_Comment_RatingModel::finder()->findAll('category_id = %d', $category->getId());
			foreach ($rates as $rate) {
				$total += $rate->getRate();
				$rateCount++;
			}
		}

		if ($total) {
			$total = $total/$rateCount;
		}
		return $total;
	}

	public static function prepareCommentForm($reviewId, $currentCommentId = 0, $sgrbFakeId, $formOptions, $captchaHtml, $postId, $categoryHtml, $isWidget)
	{
		$titleRequiredAsterisk = '';
		$emailRequiredAsterisk = '';
		$sgrvNotApprovedMessage = '';
		$commentData = SGRB_CommentController::getFrontCommentData($currentCommentId);
		$commentBoxThemeOptions = get_option('sgrb-comment-box-theme');
		$fieldAsterisk = '<i class="sgrb-comment-form-asterisk">*</i>';
		$userLoggedIn = $formOptions['required-login-checkbox'];
		$hideCommentForm = @$formOptions['hide-comment-form'];
		$addReviewText = @$formOptions['add-review-text'];
		$editReviewText = @$formOptions['edit-review-text'];
		$reviewType = @$formOptions['review-type'];
		$user = wp_get_current_user();

		if ($formOptions['transparent-background']) {
			$formOptions['total-rate-background-color'] = 'transparent';
		}
		if (@$formOptions['required-title-checkbox']) {
			$titleRequiredAsterisk = $fieldAsterisk;
		}
		if (@$formOptions['required-email-checkbox']) {
			$emailRequiredAsterisk = $fieldAsterisk;
		}
		if ($currentCommentId) {
			$addReviewText = $editReviewText;
		}
		if (!@$commentData['approved'] && $currentCommentId) {
			$sgrvNotApprovedMessage = '<span class="sgrb-not-approved-message">Your comment has not been approved yet</span>';
		}
		$html = '<div class="sgrb-user-comment-wrapper" style="background-color: '.@$formOptions['total-rate-background-color'].';color: '.@$formOptions['rate-text-color'].';">
							<div class="sgrb-hide-show-wrapper">
								<div id="sgrb-review-form-title" class="sgrb-front-comment-rows">
									<span class="sgrb-comment-title">'.$addReviewText.'</span>
								</div>';
		$html .= '<div class="sgrb-show-hide-comment-form">';
		$html .= '<div class="sgrb-notice-rates"><span class="sgrb-notice-rates-text"></span></div>'.$categoryHtml;
		if ($hideCommentForm || $isWidget) {
			$html = '<input type="hidden" class="sgrb-captcha-on" name="captcha-on" value="'.@$formOptions['captcha-on'].'">
					<input type="hidden" name="current-user-comment-id" value="'.$commentData['id'].'">
					<input type="hidden" class="detect-user-by-ip" name="detect-user-by-ip" value="'.@$forMoptions['user-detect-by'].'">
					<input type="hidden" class="sgrb-thank-text" value="'.esc_attr(@$formOptions['success-comment-text']).'">
					<input type="hidden" class="sgrb-no-rate-text" value="'.esc_attr(@$formOptions['no-category-text']).'">
					<input type="hidden" class="sgrb-no-name-text" value="'.esc_attr(@$formOptions['no-name-text']).'">
					<input type="hidden" class="sgrb-no-email-text" value="'.esc_attr(@$formOptions['no-email-text']).'">
					<input type="hidden" class="sgrb-no-title-text" value="'.esc_attr(@$formOptions['no-title-text']).'">
					<input type="hidden" class="sgrb-no-comment-text" value="'.esc_attr(@$formOptions['no-comment-text']).'">
					<input type="hidden" class="sgrb-comment-by-text" value="'.esc_attr(@$formOptions['comment-by-text']).'">
					<input type="hidden" class="sgrb-no-captcha-text" value="'.esc_attr(@$formOptions['no-captcha-text']).'">
					<textarea style="display:none" class="sgrb-comment-box-theme-options">'.@$commentBoxThemeOptions.'</textarea>';
			return $html;
		}
		if ($reviewType == SGRB_REVIEW_TYPE_SOCIAL) {
			$postId = get_the_ID();
			$html .= @$captchaHtml;
			$html .= '<input name="addPostId" type="hidden" value="'.@$postId.'">'.$sgrvNotApprovedMessage.'
				<div class="sgrb-post-comment-button">
					<input type="hidden" class="sgrb-captcha-on" name="captcha-on" value="'.@$formOptions['captcha-on'].'">
					<input type="hidden" class="sgrb-review-type" name="review-type" value="'.@$formOptions['review-type'].'">
					<input type="hidden" name="current-user-comment-id" value="'.$commentData['id'].'">
					<input type="hidden" class="detect-user-by-ip" name="detect-user-by-ip" value="'.@$forMoptions['user-detect-by'].'">
					<input type="hidden" class="sgrb-thank-text" value="'.esc_attr(@$formOptions['success-comment-text']).'">
					<input type="hidden" class="sgrb-no-rate-text" value="'.esc_attr(@$formOptions['no-category-text']).'">
					<input type="hidden" class="sgrb-no-name-text" value="'.esc_attr(@$formOptions['no-name-text']).'">
					<input type="hidden" class="sgrb-no-email-text" value="'.esc_attr(@$formOptions['no-email-text']).'">
					<input type="hidden" class="sgrb-no-title-text" value="'.esc_attr(@$formOptions['no-title-text']).'">
					<input type="hidden" class="sgrb-no-comment-text" value="'.esc_attr(@$formOptions['no-comment-text']).'">
					<input type="hidden" class="sgrb-comment-by-text" value="'.esc_attr(@$formOptions['comment-by-text']).'">
					<input type="hidden" class="sgrb-no-captcha-text" value="'.esc_attr(@$formOptions['no-captcha-text']).'">
					<textarea style="display:none" class="sgrb-comment-box-theme-options">'.@$commentBoxThemeOptions.'</textarea>
					<input data-sgrb-id="'.$sgrbFakeId.'" type="button" value="'.esc_attr(@$formOptions['post-button-text']).'" onclick="SGReview.prototype.ajaxUserRate('.@$reviewId.','.'0'.','.$sgrbFakeId.')" class="sgrb-user-comment-submit sgrb-social-review-button">
				</div></div></div></div>';

			return $html;
		}
		if ($userLoggedIn) {
			if (!$user->exists()) {
				$html .= '<input type="hidden" class="sgrb-captcha-on" name="captcha-on" value="'.@$formOptions['captcha-on'].'">
					<input type="hidden" name="current-user-comment-id" value="'.$commentData['id'].'">
					<input type="hidden" class="detect-user-by-ip" name="detect-user-by-ip" value="'.@$forMoptions['user-detect-by'].'">
					<input type="hidden" class="sgrb-thank-text" value="'.esc_attr(@$formOptions['success-comment-text']).'">
					<input type="hidden" class="sgrb-no-rate-text" value="'.esc_attr(@$formOptions['no-category-text']).'">
					<input type="hidden" class="sgrb-no-name-text" value="'.esc_attr(@$formOptions['no-name-text']).'">
					<input type="hidden" class="sgrb-no-email-text" value="'.esc_attr(@$formOptions['no-email-text']).'">
					<input type="hidden" class="sgrb-no-title-text" value="'.esc_attr(@$formOptions['no-title-text']).'">
					<input type="hidden" class="sgrb-no-comment-text" value="'.esc_attr(@$formOptions['no-comment-text']).'">
					<input type="hidden" class="sgrb-comment-by-text" value="'.esc_attr(@$formOptions['comment-by-text']).'">
					<input type="hidden" class="sgrb-no-captcha-text" value="'.esc_attr(@$formOptions['no-captcha-text']).'">
					<textarea style="display:none" class="sgrb-comment-box-theme-options">'.@$commentBoxThemeOptions.'</textarea>';
				return $html;
			}
		}
		$html .= $sgrvNotApprovedMessage;
		$html .= '<div class="sgrb-front-comment-rows">
					<span class="sgrb-comment-title">'.esc_html(@$formOptions['name-text']).' </span>'.$fieldAsterisk.'
					<span class="sgrb-each-field-notice">
						<input class="sgrb-add-fname" name="addName" type="text" value="'.esc_attr($commentData['name']).'" placeholder="'.esc_attr(@$formOptions['name-placeholder-text']).'" autocomplete="off">
						<i></i>
					</span>
				</div>
				<div class="sgrb-front-comment-rows">
					<span class="sgrb-comment-title">'.esc_html(@$formOptions['email-text']).' </span>'.$emailRequiredAsterisk.'
					<span class="sgrb-each-field-notice">
						<input class="sgrb-add-email" name="addEmail" type="email" value="'.esc_attr($commentData['email']).'" placeholder="'.esc_attr(@$formOptions['email-placeholder-text']).'" autocomplete="off">
						<i></i>
					</span>
				</div>
				<div class="sgrb-front-comment-rows">
					<span class="sgrb-comment-title">'.esc_html(@$formOptions['title-text']).' </span>'.$titleRequiredAsterisk.'
					<span class="sgrb-each-field-notice">
						<input class="sgrb-add-title" name="addTitle" type="text" value="'.esc_attr($commentData['title']).'" placeholder="'.esc_attr(@$formOptions['title-placeholder-text']).'" autocomplete="off">
						<i></i>
					</span>
				</div>
				<div class="sgrb-front-comment-rows">
					<span class="sgrb-comment-title">'.esc_html(@$formOptions['comment-text']).' </span>'.$fieldAsterisk.'
					<textarea class="sgrb-add-comment" name="addComment" placeholder="'.esc_attr(@$formOptions['comment-placeholder-text']).'" autocomplete="off">'.esc_html(@$commentData['comment']).'</textarea><i></i>'.@$captchaHtml.'
				</div>';

		$html .= '<input name="addPostId" type="hidden" value="'.@$postId.'">
				<div class="sgrb-post-comment-button">
					<input type="hidden" class="sgrb-captcha-on" name="captcha-on" value="'.@$formOptions['captcha-on'].'">
					<input type="hidden" name="current-user-comment-id" value="'.$commentData['id'].'">
					<input type="hidden" class="detect-user-by-ip" name="detect-user-by-ip" value="'.@$forMoptions['user-detect-by'].'">
					<input type="hidden" class="sgrb-thank-text" value="'.esc_attr(@$formOptions['success-comment-text']).'">
					<input type="hidden" class="sgrb-no-rate-text" value="'.esc_attr(@$formOptions['no-category-text']).'">
					<input type="hidden" class="sgrb-no-name-text" value="'.esc_attr(@$formOptions['no-name-text']).'">
					<input type="hidden" class="sgrb-no-email-text" value="'.esc_attr(@$formOptions['no-email-text']).'">
					<input type="hidden" class="sgrb-no-title-text" value="'.esc_attr(@$formOptions['no-title-text']).'">
					<input type="hidden" class="sgrb-no-comment-text" value="'.esc_attr(@$formOptions['no-comment-text']).'">
					<input type="hidden" class="sgrb-comment-by-text" value="'.esc_attr(@$formOptions['comment-by-text']).'">
					<input type="hidden" class="sgrb-no-captcha-text" value="'.esc_attr(@$formOptions['no-captcha-text']).'">
					<textarea style="display:none" class="sgrb-comment-box-theme-options">'.@$commentBoxThemeOptions.'</textarea>
					<input data-sgrb-id="'.$sgrbFakeId.'" type="button" value="'.esc_attr(@$formOptions['post-button-text']).'" onclick="SGReview.prototype.ajaxUserRate('.@$reviewId.','.'0'.','.$sgrbFakeId.')" class="sgrb-user-comment-submit" style="background-color: '.@$forMoptions['total-rate-background-color'].';color: '.@$forMoptions['rate-text-color'].';">
				</div>';
		$html .= '</div>
					</div>
				</div>';
		return $html;
	}

	public static function getCommentsWrapperBox($reviewId, $postId, $reviewMainOptions, $isWidget)
	{
		global $sgrb;
		$allApprovedComments = '';
		if ($reviewMainOptions['review-type'] == SGRB_REVIEW_TYPE_POST) {
			$commentsArray = SGRB_CommentModel::finder()->findAll('review_id = %d && approved = %d && post_id = %d ', array($reviewId, 1, $postId));//SGRB_CommentController::getCommentsByReviewId($reviewId);
		}
		else {
			$commentsArray = SGRB_CommentModel::finder()->findAll('review_id = %d && approved = %d ', array($reviewId, 1));//SGRB_CommentController::getCommentsByReviewId($reviewId);
		}

		if (!@$reviewMainOptions['show-comments'] || $reviewMainOptions['review-type'] == SGRB_REVIEW_TYPE_SOCIAL) {
			return '';
		}
		if (!$reviewMainOptions['total-rate-background-color']) {
			$reviewMainOptions['total-rate-background-color'] = '#fbfbfb';
		}
		if ($reviewMainOptions['transparent-background']) {
			$reviewMainOptions['total-rate-background-color'] = 'transparent';
		}
		if (!$reviewMainOptions['rate-text-color']) {
			$reviewMainOptions['rate-text-color'] = '#4c4c4c';
		}
		$allApprovedComments = '<div class="sgrb-approved-comments-to-show">
								<input class="sgrb-no-more-text" type="hidden" value="'.$reviewMainOptions['no-more-text'].'">
								<input class="sgrb-show-all-text" type="hidden" value="'.$reviewMainOptions['show-all-text'].'">
								<input class="sgrb-hide-text" type="hidden" value="'.$reviewMainOptions['hide-text'].'">
								<input class="sgrb-comments-count" type="hidden" value="'.@$reviewMainOptions['comments-count-to-show'].'">
								<input class="sgrb-comments-count-load" type="hidden" value="'.@$reviewMainOptions['comments-count-to-load'].'">';
		$allApprovedComments .= '</div>';
		if (empty($commentsArray)) {
			return $allApprovedComments;
		}
		if (!@$reviewMainOptions['comments-count-to-show']) {
			@$reviewMainOptions['comments-count-to-show'] = SGRB_COMMENTS_PER_PAGE;
		}
		if (!@$reviewMainOptions['comments-count-to-load']) {
			!@$reviewMainOptions['comments-count-to-load'] = 3;
		}

		if (!empty($commentsArray) && @$reviewMainOptions['show-comments'] && !$isWidget) {
			$tmp = ceil(count($commentsArray)/SGRB_COMMENTS_PER_PAGE);
			$allApprovedComments .= '<div class="sgrb-pagination" style="background-color:'.esc_attr($reviewMainOptions['total-rate-background-color']).';color: '.$reviewMainOptions['rate-text-color'].';">';
			$allApprovedComments .= '<input class="sgrb-comments-per-page" type="hidden" value="'.@$reviewMainOptions['comments-count-to-show'].'">';
			$perPage = @$reviewMainOptions['comments-count-to-show'];
			$allApprovedComments .= '<input class="sgrb-page-count" type="hidden" value="'.$tmp.'">';
			$allApprovedComments .= '<input class="sgrb-comments-count" type="hidden" value="'.@$reviewMainOptions['comments-count-to-show'].'">';
			$allApprovedComments .= '<input class="sgrb-comments-count-load" type="hidden" value="'.@$reviewMainOptions['comments-count-to-load'].'">';
			$allApprovedComments .= '<input class="sgrb-post-id" type="hidden" value="'.$postId.'">';
			$allApprovedComments .= '<i class="sgrb-loading-spinner"><img src='.$sgrb->app_url.'assets/page/img/comment-loader.gif></i>';
			$allApprovedComments .= '<a class="sgrb-comment-load" href="javascript:void(0)">'.$reviewMainOptions['load-more-text'].'</a>';
			$allApprovedComments .= '</div>';
		}
		return $allApprovedComments;
	}
}
