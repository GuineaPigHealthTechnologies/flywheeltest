<?php

global $sgrb;
$sgrb->includeModel('Review');
$sgrb->includeModel('TemplateDesign');
$sgrb->includeView('Review');

class SGRB_TemplateDesignView extends SGRB_Review
{
	public function __construct()
	{
		parent::__construct('sgrb');
		$this->setRowsPerPage(10);
		$this->setTablename('template_design');
		$this->setColumns(array(
			'id',
			'name'
		));
		$this->setDisplayColumns(array(
			'id' => 'ID',
			'name' => __('Title', 'sgrb'),
			'preview' => __('Preview', 'sgrb')

		));
		$this->setSortableColumns(array(
			'id' => array('id', false),
			'name' => array('name', true)
		));
		$this->setInitialSort(array(
			'sgrb_pro_version' => 'DESC'
		));
	}

	public function customizeRow(&$row)
	{
		global $sgrb;
		$id = $row[0];
		$template = SGRB_TemplateDesignModel::finder()->findByPk($id);
		$row[2] = '<i class="sgrb-preview-eye"><img width="30px" height="30px" src="'.$sgrb->app_url.'assets/page/img/preview.png'.'">';
		if ($template->getImg_url()) {
			$tempImage = $template->getImg_url();
		}
		else {
			$tempImage = $sgrb->app_url.'assets/page/img/custom_template.jpeg';
		}
		$row[2] .= '<div class="sgrb-template-icon-preview" style="background-image:url('.$tempImage.');right:12%;"></div>';
		$row[2] .= '</i>';
	}

	public function customizeQuery(&$query)
	{
		$query .= " WHERE name<>'simple_review'";
	}
}
