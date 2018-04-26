<?php

global $sgrb;
$sgrb->includeModel('Model');
$sgrb->includeModel('Review');

class SGRB_CommentModel extends SGRB_Model
{
	const TABLE = 'comment';
	protected $id;
	protected $review_id;
	protected $category_id;
	protected $post_id;
	protected $approved;
	protected $title;
	protected $cdate;
	protected $name;
	protected $email;
	protected $comment;
	protected $user_image;

	public static function finder($class = __CLASS__)
	{
		return parent::finder($class);
	}

	public static function create($blogId = '')
	{
		global $sgrb;
		global $wpdb;
		$tablename = $sgrb->tablename(self::TABLE, $blogId);
		$reviewTable = $sgrb->tablename(SGRB_ReviewModel::TABLE, $blogId);

		$query = "CREATE TABLE IF NOT EXISTS $tablename (
					`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`review_id` INT(10) unsigned NOT NULL,
					`category_id` INT(10) NULL,
					`post_id` INT(10) NULL,
					`approved` tinyint(4) unsigned NOT NULL,
					`title` VARCHAR(255) NULL,
					`cdate` datetime NOT NULL,
					`name` varchar(255) NOT NULL,
					`email` varchar(255) NOT NULL,
					`comment` text NOT NULL,
					 PRIMARY KEY (`id`)
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
		$query2 = "ALTER TABLE $tablename ADD INDEX(`review_id`);";
				$query3 = "ALTER TABLE $tablename ADD FOREIGN KEY (`review_id`)
				REFERENCES $reviewTable (`id`) ON DELETE CASCADE ON UPDATE CASCADE;";
		$wpdb->query($query);
		$wpdb->query($query2);
		$wpdb->query($query3);
	}

	public static function drop($blogId = '')
	{
		global $sgrb;
		global $wpdb;
		$tablename = $sgrb->tablename(self::TABLE, $blogId);
		$query = "DROP TABLE $tablename";
		$wpdb->query($query);
	}

	public static function changeColumnType()
	{
		global $sgrb;
		global $wpdb;

		$tablename = $sgrb->tablename(self::TABLE);
		$query = "ALTER TABLE $tablename
					MODIFY COLUMN `cdate` varchar(255)";
		$wpdb->query($query);
	}

	public static function getAllComments($reviewId, $approved = '')
	{
		global $sgrb;
		$postId = SGRB_ReviewController::getCurrentPostId();
		$options = SGRB_ReviewController::getAllReviewOptionsAssocArray($reviewId);
		if ($options['review-type'] == SGRB_REVIEW_TYPE_SOCIAL) {
			$postId = get_the_ID();
		}
		$allComments = array();

		if ($postId && ($options['review-type'] == SGRB_REVIEW_TYPE_SOCIAL || $options['review-type'] == SGRB_REVIEW_TYPE_POST)) {
			if ($approved) {
				$allComments = self::finder()->findAll('review_id = %d && approved = %d && post_id = %d ' , array($reviewId, 1, $postId));
			}
			else {
				$allComments = self::finder()->findAll('review_id = %d && post_id = %d', array($reviewId, $postId));
			}
		}
		else {
			if ($approved) {
				$allComments = self::finder()->findAll('review_id = %d && approved = %d ' , array($reviewId, 1));
			}
			else {
				$allComments = self::finder()->findAll('review_id = %d', array($reviewId));
			}
		}
		return $allComments;
	}
}
