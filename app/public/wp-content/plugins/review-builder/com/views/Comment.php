<?php

global $sgrb;
$sgrb->includeLib('Review');
$sgrb->includeModel('Review');
$sgrb->includeView('Review');
$sgrb->includeModel('Comment');

class SGRB_CommentView extends SGRB_Review
{
	public function __construct()
	{
		parent::__construct('sgrb');
		$reviewView = new SGRB_ReviewReviewView();
		$reviewTablename = $reviewView->getTablename();

		$this->setRowsPerPage(10);
		$this->setTablename(SGRB_CommentModel::TABLE);
		$this->setColumns(array(
			$this->tablename.'.name',
			$reviewTablename.'.title AS review_title',
			$this->tablename.'.title',
			$this->tablename.'.comment',
			$this->tablename.'.cdate',
			$this->tablename.'.id',
			$this->tablename.'.approved'
		));
		$this->setDisplayColumns(array(
			'name' => __('Author', 'sgrb'),
			'review_title' => __('Review', 'sgrb'),
			'title' => __('Title', 'sgrb'),
			'comment' => '<i class="vers comment-grey-bubble"></i> '.__('Comment', 'sgrb'),
			'cdate' => __('Date', 'sgrb'),
			'options' => __('Options', 'sgrb')
		));
		$this->setSortableColumns(array(
			'name' => array('name', true),
			'review_title' => array('review_title', true),
			'cdate' => array('cdate', true),
		));
		global $sgrb;
		if ((int)@$_GET['id']) {
			$comments = SGRB_CommentModel::finder()->count('review_id = %d',$_GET["id"]);
			if (!$comments) {
				return;
			}
			$approved = SGRB_CommentModel::finder()->count('review_id=%d and approved=%d ',array($_GET["id"],1));
			$disapproved = $comments - $approved;

			$this->setViews(array(
				'all' => '<a href="'.$sgrb->adminUrl("Comment/index","id=".$_GET["id"]).'">All <span class="sgrb-count">('.$comments.')</span></a>',
				'approved' => '<a href="'.$sgrb->adminUrl("Comment/index","id=".$_GET["id"]."&app=1").'">Approved <span class="sgrb-count">('.$approved.')</span></a>',
				'disapproved' => '<a href="'.$sgrb->adminUrl("Comment/index","id=".$_GET["id"]."&app=0").'">Disapproved <span class="sgrb-count">('.$disapproved.')</span></a>'
			));
		}
	}

	public function customizeRow(&$row)
	{
		global $sgrb;

		$id = $row[5];
		$isApproved = $row[6];
		unset($row[6]);//don't show this column
		$approvedLinkText = '';
		$editUrl = $sgrb->adminUrl('Comment/save','id='.$id);
		if ($row[3] != '') {
			$row[3] = '<textarea class="sgrb-displayedComment" readonly>'.$row[3].'</textarea>';
		}

		if ($isApproved == 1) {
			$approvedLinkText = __('Disapprove', 'sgrb');
		}
		else if ($isApproved == 0) {
			$approvedLinkText = __('Approve', 'sgrb');
		}

		$row[5] = '<a href="'.$editUrl.'">'.__('Edit', 'sgrb').' </a>&nbsp;/&nbsp;
					<a href="#" onclick="SGComment.ajaxDelete('.$id.')">'.__('Delete', 'sgrb').' </a>&nbsp;/&nbsp;
					<a href="#" onclick="SGComment.prototype.ajaxApproveComment('.$id.')">'.$approvedLinkText.'</a>';
	}

	public function customizeQuery(&$query)
	{
		$reviewView = new SGRB_ReviewReviewView();
		$reviewTablename = $reviewView->getTablename();
		$query .= ' LEFT JOIN '.$reviewTablename.' ON '.$reviewTablename.'.id='.$this->tablename.'.review_id';
		if ((int)@$_GET["id"]) {
			$query .= ' WHERE '.$this->tablename.'.review_id='.$_GET["id"];
			if (isset($_GET["app"])) {
				if ($_GET["app"] == 1) {
					$query .= ' and approved=1';
				}
				else if ($_GET["app"] == 0) {
					$query .= ' and approved=0';
				}
			}
		}
		else {
			$query .= ' WHERE '.$reviewTablename.'.type <> '.SGRB_REVIEW_TYPE_SOCIAL;
		}

	}
}
