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
$sgrb->includeModel('Template');
$sgrb->includeModel('Category');
$sgrb->includeModel('Comment_Rating');
$sgrb->includeModel('Rate_Log');
$sgrb->includeCore('StyleScriptLoader');

class SGRB_TemplateDesignController extends SGRB_Controller
{

	public function index()
	{
		global $sgrb;
		SGRB_StyleScriptLoader::prepare('TemplateDesign', 'index');

		$template = new SGRB_TemplateDesignView();
		$createNewUrl = $sgrb->adminUrl('TemplateDesign/save');

		SGRB_AdminView::render('TemplateDesign/index', array(
			'createNewUrl' => $createNewUrl,
			'template' => $template
		));
	}

	public function save()
	{
		global $sgrb;
		SGRB_StyleScriptLoader::prepare('TemplateDesign', 'save');

		$sgrbTemplateId = 0;
		$sgrbTemplateDataArray = array();

		isset($_GET['id']) ? $sgrbTemplateId = (int)$_GET['id'] : 0;
		$sgrbTemplate = SGRB_TemplateDesignModel::finder()->findByPk($sgrbTemplateId);
		if ($sgrbTemplate) {

			$templateName = $sgrbTemplate->getName();
			$templateImage = $sgrbTemplate->getImg_url();
			$templateHtmlContent = $sgrbTemplate->getThtml();
			$templateCssContent = $sgrbTemplate->getTcss();

			$sgrbTemplateDataArray['templateName'] = $templateName;
			$sgrbTemplateDataArray['templateImage'] = $templateImage;
			$sgrbTemplateDataArray['templateHtmlContent'] = $templateHtmlContent;
			$sgrbTemplateDataArray['templateCssContent'] = $templateCssContent;
		}
		else {
			$sgrbTemplate = new SGRB_TemplateDesignModel();
		}
		SGRB_AdminView::render('TemplateDesign/save', array(
			'sgrbTemplateId' => $sgrbTemplateId,
			'sgrbTemplateDataArray' => $sgrbTemplateDataArray
		));
	}

	public function ajaxDeleteTemplate()
	{
		global $sgrb;

		$id = (int)$_POST['id'];
		$canDelete = true;

		$deletedTemplate = SGRB_TemplateDesignModel::finder()->findByPk($id);
		$deletedTemplateName = $deletedTemplate->getName();
		$allDeleted = SGRB_TemplateModel::finder()->findAll('name = %s', $deletedTemplateName);

		if ($allDeleted) {
			$canDelete = false;
		}

		if ($canDelete) {
			SGRB_TemplateDesignModel::finder()->deleteByPk($id);
		}
		echo $canDelete;
		exit();
	}

	public function ajaxSave()
	{
		global $wpdb;
		global $sgrb;
		$sgrbTemplateId = 0;
		$templateName = '';
		$templateHtml = '';
		$templateCss = '';
		$templateNameInUse = '';
		$replaceString = array('  ', '   ','	');

		SGRB_Input::setSource($_POST);
		if (count($_POST)) {
			$templateName = SGRB_Input::get('sgrbTemplateName');
			$templateImage = SGRB_Input::get('sgrbTemplateImage');
			$templateHtml = SGRB_Input::getStripSlashed('sgrbHtmlContent');
			$templateCss = SGRB_Input::getStripSlashed('sgrbCssContent');

			$sgrbTemplateId = (int)SGRB_Input::get('sgrbTemplateId');

			$template = new SGRB_TemplateDesignModel();
			$isUpdate = false;

			if ($sgrbTemplateId) {
				$isUpdate = true;
				$template = SGRB_TemplateDesignModel::finder()->findByPk($sgrbTemplateId);
				$templateNameInUse = $template->getName();
				if (!$template) {
					echo false;
					exit();
				}
			}

			$coincideTemplates = SGRB_TemplateDesignModel::finder()->findAll('name = %s', $templateName);
			$coincideTemplateInUse = SGRB_TemplateModel::finder()->findAll('name = %s', $templateNameInUse);
			if (!empty($coincideTemplateInUse)) {
				foreach ($coincideTemplateInUse as $templateInUse) {
					$templateInUse->setName($templateName);
					$templateInUse->save();
				}
			}
			if (!$coincideTemplates || ($isUpdate && $coincideTemplates)) {
				$template->setName($templateName);
				$template->setSgrb_pro_version(1);
				$template->setImg_url($templateImage);
				$template->setThtml(str_replace($replaceString, '', $templateHtml));
				$template->setTcss($templateCss);
				$res = $template->save();
				if ($template->getId()) {
					$lastId = $template->getId();
				}
				else {
					if (!$res) return false;
					$lastId = $wpdb->insert_id;
				}
			}
			else {
				echo false;
				exit();
			}
		}
		echo $lastId;
		exit();
	}
}

