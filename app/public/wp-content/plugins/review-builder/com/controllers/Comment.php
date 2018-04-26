<?php

global $sgrb;
$sgrb->includeController('Controller');
$sgrb->includeController('Review');
$sgrb->includeView('Admin');
$sgrb->includeView('Review');
$sgrb->includeView('Comment');
$sgrb->includeModel('Review');
$sgrb->includeModel('Comment');
$sgrb->includeModel('Comment_Rating');
$sgrb->includeModel('Template');
$sgrb->includeModel('Category');
$sgrb->includeModel('CommentForm');
$sgrb->includeLib('Input');
$sgrb->includeCore('StyleScriptLoader');

class SGRB_CommentController extends SGRB_Controller
{
	public function index()
	{
		global $sgrb;
		SGRB_StyleScriptLoader::prepare('Comment', 'index');

		$comment = new SGRB_CommentView();
		$createNewUrl = $sgrb->adminUrl('Comment/save');

		SGRB_AdminView::render('Comment/index', array(
			'createNewUrl' => $createNewUrl,
			'comment' => $comment
		));
	}

	public function ajaxSave()
	{
		global $wpdb;
		SGRB_Input::setSource($_POST);

		$ip = SGRB_ReviewController::getClientIpAddress();
		$reviewObj = new SGRB_ReviewController();

		if (count($_POST)) {

			$sgrbId = (int)SGRB_Input::get('sgrb-id');
			$sgrbComId = (int)SGRB_Input::get('sgrb-com-id');

			$title = SGRB_Input::getStripSlashed('title');
			$email = SGRB_Input::get('email');
			$comment = SGRB_Input::getStripSlashed('comment');
			$name = SGRB_Input::getStripSlashed('name');

			$review = SGRB_Input::get('review');//review id
			$rates = SGRB_Input::get('rates');
			$categories = SGRB_Input::get('categories');
			$post = SGRB_Input::get('post');
			$postCategory = SGRB_Input::get('post-category');
			$addPostId = SGRB_Input::get('addPostId');
			$reviewType = $reviewObj->getReviewOptionsByPk($sgrbId, 'review-type');
			if ($reviewType == SGRB_REVIEW_TYPE_SOCIAL) {
				$post = SGRB_Input::get('page');//if is social review => post <=> page
			}

			$isApproved = SGRB_Input::isIsset('isApproved');

			$sgrbComment = SGRB_CommentModel::finder()->findByPk($sgrbId);

			if (!$sgrbComId) {
				$sgrbComment = new SGRB_CommentModel();
			}
			else {
				$sgrbComment = SGRB_CommentModel::finder()->findByPk($sgrbComId);
			}

			$sgrbComment->setReview_id(sanitize_text_field($review));
			$sgrbComment->setCategory_id(sanitize_text_field($postCategory));
			$sgrbComment->setPost_id(sanitize_text_field($post));
			$sgrbComment->setTitle(sanitize_text_field($title));
			$sgrbComment->setEmail(sanitize_text_field($email));
			$sgrbComment->setComment(sanitize_text_field($comment));
			$sgrbComment->setName(sanitize_text_field($name));
			$time = current_time('mysql');
			if (!$time) {
				@date_default_timezone_set(get_option('timezone_string'));
				$time = date('Y-m-d-h-m-s');
			}
			if (!$sgrbComId) {
				$sgrbComment->setCdate(sanitize_text_field($time));
			}
			$sgrbComment->setApproved(sanitize_text_field($isApproved));
			$sgrbCommentRes = $sgrbComment->save();

			if ($sgrbComment->getId()) {
				$lastCommentId = $sgrbComment->getId();
			}
			else {
				if (!$sgrbCommentRes) return false;
				$lastCommentId = $wpdb->insert_id;
			}

			for ($i=0;$i<count($rates);$i++) {
				if (!$sgrbComId) {
					$commentRates = new SGRB_Comment_RatingModel();
					$commentRates->setComment_id(sanitize_text_field($lastCommentId));
					$commentRates->setCategory_id(sanitize_text_field($categories[$i]));
					$commentRates->setRate(sanitize_text_field($rates[$i]));
					$commentRates->save();
				}
				else {
					$commentRates = SGRB_Comment_RatingModel::finder()->findAll('comment_id = %d', $lastCommentId);
					$commentRates[$i]->setComment_id(sanitize_text_field($lastCommentId));
					$commentRates[$i]->setCategory_id(sanitize_text_field($categories[$i]));
					$commentRates[$i]->setRate(sanitize_text_field($rates[$i]));
					$commentRates[$i]->save();
				}
			}

			$newUser = new SGRB_Rate_LogModel();
			$allRateLogs = SGRB_Rate_LogModel::finder()->findAll();
			foreach ($allRateLogs as $singleRateLog) {
				if ($singleRateLog->getReview_id() == $review) {
					if ($singleRateLog->getComment_id() == $lastCommentId) {
						$rateLogId = $singleRateLog->getId();
						$newUser = SGRB_Rate_LogModel::finder()->findByPk($rateLogId);
					}
				}
			}
			$newUser->setReview_id(sanitize_text_field($review));
			if ($addPostId || $reviewType == SGRB_REVIEW_TYPE_SOCIAL) {
				$newUser->setPost_id(sanitize_text_field($post));
			}
			$newUser->setComment_id(sanitize_text_field($lastCommentId));
			$newUser->setIp(sanitize_text_field($ip));
			$newUser->save();
		}
		echo $lastCommentId;
		exit();
	}

	public function save()
	{
		global $sgrb;
		SGRB_StyleScriptLoader::prepare('Comment', 'save');

		$sgrbId = 0;
		$sgrbDataArray = array();
		$createNewUrl = $sgrb->adminUrl('Comment/save');

		$sgrbId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

		$sgrbSaveUrl = $sgrb->adminUrl('Comment/save');
		$sgrbDataArray = array();
		$attributes = array();
		$ratingType = '';

		if ($sgrbId) {
			$sgrbComment = SGRB_CommentModel::finder()->findByPk($sgrbId);

			$title = $sgrbComment->getTitle();
			$email = $sgrbComment->getEmail();
			$comment = $sgrbComment->getComment();
			$name = $sgrbComment->getName();

			$isApproved = $sgrbComment->getApproved();
			$reviewId = $sgrbComment->getReview_id();
			$postCategoryId = $sgrbComment->getCategory_id();
			$postId = $sgrbComment->getPost_id();

			$category = new SGRB_CategoryModel();
			$sgrbReview = new SGRB_ReviewModel();
			if ($reviewId) {
				$category = SGRB_CategoryModel::finder()->findAll('review_id = %d', $reviewId);
				$ratings = SGRB_Comment_RatingModel::finder()->findAll('comment_id = %d', $sgrbId);
				$sgrbReview = SGRB_ReviewModel::finder()->findByPk($reviewId);
				$sgrbReviewTitle = $sgrbReview->getTitle();
				$sgrbOptions = $sgrbReview->getOptions();
				$sgrbOptions = json_decode($sgrbOptions, true);
				$ratingType = $sgrbOptions['rate-type'];
			}

			if ($ratingType == SGRB_RATE_TYPE_STAR) {
				$ratingType = 'star';
			}
			else if ($ratingType == SGRB_RATE_TYPE_PERCENT) {
				$ratingType = 'percent';
			}
			else if ($ratingType == SGRB_RATE_TYPE_POINT) {
				$ratingType = 'point';
			}

			$sgrbDataArray['review_id'] = $reviewId;
			$sgrbDataArray['review-title'] = $sgrbReviewTitle;
			$sgrbDataArray['ratingType'] = $ratingType;
			$sgrbDataArray['isApproved'] = $isApproved;
			$sgrbDataArray['title'] = $title;
			$sgrbDataArray['email'] = $email;
			$sgrbDataArray['comment'] = $comment;
			$sgrbDataArray['name'] = $name;

			// if it is post type review
			if ($postId) {
				$postIdFirst = get_the_category($postId);
				$postIdFirst = $postIdFirst[0];
				$sgrbDataArray['post-category-title'] = $postIdFirst->name;
				$sgrbDataArray['post-title'] = get_post($postId)->post_title;
				$sgrbDataArray['post-category-id'] = $postCategoryId;
				$sgrbDataArray['post-id'] = $postId;
			}

			$sgrbDataArray['category'] = $category;
			$sgrbDataArray['ratings'] = $ratings;
		}
		else {
			$sgrbComment = new SGRB_CommentModel();
			$sgrbDataArray['category'] = array();
			$sgrbDataArray['ratings'] = array();

		}

		$allReviews = SGRB_ReviewModel::finder()->findAll();
		foreach ($allReviews as $key => $notSocialReview) {
			$options = json_decode($notSocialReview->getOptions(), true);
			if ($options['review-type'] == SGRB_REVIEW_TYPE_SOCIAL) {
				unset($allReviews[$key]);
			}
		}

		if ($ratingType == SGRB_RATE_TYPE_STAR) {
			$ratingType = 'star';
		}
		else if ($ratingType == SGRB_RATE_TYPE_PERCENT) {
			$ratingType = 'percent';
		}
		else if ($ratingType == SGRB_RATE_TYPE_POINT) {
			$ratingType = 'point';
		}

		SGRB_AdminView::render('Comment/save', array(
			'sgrbDataArray' => $sgrbDataArray,
			'sgrbCommentId' => $sgrbId,
			'allReviews' => $allReviews,
			'sgrbSaveUrl' => $sgrbSaveUrl,
			'createNewUrl' => $createNewUrl
		));
	}

	public function ajaxDelete()
	{
		global $sgrb;
		SGRB_Input::setSource($_POST);
		$id = (int)SGRB_Input::get('id');
		SGRB_CommentModel::finder()->deleteByPk($id);
		SGRB_Comment_RatingModel::finder()->deleteAll('comment_id = %d', $id);
		SGRB_Rate_LogModel::finder()->deleteAll('comment_id = %d', $id);
		exit();
	}

	public function ajaxApproveComment()
	{
		global $sgrb;
		SGRB_Input::setSource($_POST);
		$id = (int)SGRB_Input::get('id');
		$currentComment = SGRB_CommentModel::finder()->findByPk($id);
		$isApproved = $currentComment->getApproved();
		if ($isApproved == 1) {
			$currentComment->setApproved(0);
		}
		else if ($isApproved == 0) {
			$currentComment->setApproved(1);
		}
		$currentComment->save();
		exit();
	}

	public function ajaxSelectReview()
	{
		global $sgrb;
		$sgrb->includeScript('page/scripts/sgComment');
		$sgrb->includeScript('core/scripts/main');
		$sgrb->includeScript('core/scripts/sgrbRequestHandler');

		$sgrbDataArray = array();
		$attributes = array();
		SGRB_Input::setSource($_POST);
		$id = (int)SGRB_Input::get('id');
		$review = SGRB_ReviewModel::finder()->findByPk($id);

		$categories = SGRB_CategoryModel::finder()->findAll('review_id = %d', $id);

		$sgrbOptions = $review->getOptions();
		$sgrbOptions = json_decode($sgrbOptions, true);
		$ratingType = @$sgrbOptions['rate-type'];

		$count = 0;

		$isPostReview = @$sgrbOptions['post-category'];

		if ($ratingType == SGRB_RATE_TYPE_STAR) {
			$ratingType = 'star';
			$count = 5;
		}
		else if ($ratingType == SGRB_RATE_TYPE_PERCENT) {
			$ratingType = 'percent';
			$count = 100;
		}
		else if ($ratingType == SGRB_RATE_TYPE_POINT) {
			$ratingType = 'point';
			$count = 10;
		}

		$sgrbDataArray['category'] = $categories;

		$html = '';

		$i = 0;
		$arr = array();

		if ($isPostReview) {
			$allPosts = get_posts();
			$allCategories = get_terms(array('get'=>'all'));
			foreach ($allCategories as $category) {
				$i++;
				$arr['postCategoies'][$i]['postCategoryId'] = esc_attr($category->term_id);
				$arr['postCategoies'][$i]['postCategoryTitle'] = esc_attr($category->name);
			}
			foreach ($allPosts as $singlePost) {
				$arrPost = wp_get_post_categories($singlePost->ID);
				$arrPost = $arrPost[0];
				if ($arrPost == $isPostReview) {
					$i++;
					$arr['posts'][$i]['postTitle'] = esc_attr($singlePost->post_title);
					$arr['posts'][$i]['postId'] = esc_attr($singlePost->ID);
				}
			}
		}

		foreach ($sgrbDataArray['category'] as $category) {
			$i++;
			$arr[$i]['categoryId'] = esc_attr($category->getId());
			$arr[$i]['name'] = esc_attr($category->getName());
			$arr[$i]['ratingType'] = esc_attr($ratingType);
			$arr[$i]['count'] = esc_attr($count);
		}
		if (SGRB_PRO_VERSION && !empty($attributes)) {
			$arr['fields'] = $attributes;
		}
		$html = json_encode($arr);

		echo $html;
		exit();
	}

	public function ajaxSelectPosts()
	{
		SGRB_Input::setSource($_POST);
		$categoryId = SGRB_Input::get('categoryId');
		$allPosts = get_posts(array('category' => $categoryId));
		$html = '';
		$i = 0;
		$arr = array();
		foreach ($allPosts as $post) {
			$i++;
			$arr[$i]['postId'] = esc_attr($post->ID);
			$arr[$i]['postTitle'] = esc_attr($post->post_title);
		}

		$html = json_encode($arr);

		echo $html;
		exit();
	}

	public function getAttributes ($shortcode)
	{
		$array = explode(' ', $shortcode);
		$options = array();

		foreach ($array as $key) {
			$key = explode('=', $key);
			if ($key[0] == 'label') {
				$options['label'] = $key[1];
			}
			if ($key[0] == 'placeholder') {
				$options['placeholder'] = $key[1];
			}
		}
		return $options;
	}

	public static function getFrontCommentData($currentCommentId = 0)
	{
		$options = array();
		$options['id'] = '';
		$options['title'] = '';
		$options['email'] = '';
		$options['name'] = '';
		$options['comment'] = '';
		$options['approved'] = '';
		$options['category_id'] = '';
		$options['post_id'] = '';

		$comment = SGRB_CommentModel::finder()->findByPk($currentCommentId);
		if (!$comment) {
			return $options;
		}

		$options['id'] = $comment->getId();
		$options['title'] = $comment->getTitle();
		$options['email'] = $comment->getEmail();
		$options['name'] = $comment->getName();
		$options['comment'] = $comment->getComment();
		$options['approved'] = $comment->getApproved();
		$options['category_id'] = $comment->getCategory_id();
		$options['post_id'] = $comment->getPost_id();

		return $options;
	}

	public static function getCommentsByReviewId($reviewId, $all = false)
	{
		$approvedComments = array();
		$reviewOptions = SGRB_ReviewController::getAllReviewOptionsAssocArray($reviewId);
		$start = (int)@$_POST['itemsRangeStart'];
		$perPage = (int)@$_POST['perPage'];
		$postId = (int)@$_POST['postId'];
		if (!$start) {//first comment
			$start = 0;
		}
		if (!$perPage) {
			$perPage = $reviewOptions['comments-count-to-show'];
			if (!$perPage) {
                $perPage = 10;
            }
		}


		if (!$all) {
			if ($reviewOptions['review-type'] == SGRB_REVIEW_TYPE_POST) {
				$approvedComments = SGRB_CommentModel::finder()->findAll('review_id = %d && approved = %d && post_id = %d ORDER BY id DESC LIMIT '.$start.', '.$perPage.' ', array($reviewId, 1, $postId));
			}
			else {
				$approvedComments = SGRB_CommentModel::finder()->findAll('review_id = %d && approved = %d ORDER BY id DESC LIMIT '.$start.', '.$perPage.' ', array($reviewId, 1));
			}
		}
		else {
			if ($reviewOptions['review-type'] == SGRB_REVIEW_TYPE_POST) {
				$approvedComments = SGRB_CommentModel::finder()->findAll('review_id = %d &&  post_id = %d ORDER BY id DESC ' , array($reviewId, $postId));
			}
			else {
				$approvedComments = SGRB_CommentModel::finder()->findAll('review_id = %d ORDER BY id DESC ' , array($reviewId));
			}
		}
		return $approvedComments;
	}
}
