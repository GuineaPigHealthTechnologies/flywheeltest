<?php

global $sgrb;
$sgrb->includeModel('Review');
$sgrb->includeView('Review');

class SGRB_CommentFormView extends SGRB_Review
{
	public function __construct()
	{

	}

	public function customizeRow(&$row)
	{

	}

	public function customizeQuery(&$query)
	{
		//$query .= ' LEFT JOIN wp_sgrb_comment ON wp_sgrb_comment.review_id='.$this->tablename.'.id';
	}
}

