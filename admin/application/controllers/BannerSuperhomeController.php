<?php

class BannerSuperhomeController extends controllers_BannerAbstractController {

	public $indexSectionId	= 161;
	public $updateSectionId	= 258;
	public $locations;



	public function init() {
		parent::init();
		$this->locations = array(
			'1' => i18n::tr('Left'),
			'2' => i18n::tr('Right'),
			'3' => i18n::tr('Gold'),
		);
	}



	public function indexAction() {
		$this->view->bannerForms	= Models_Banner::getSuperhomeForms($this->updateSectionId, $this->locations);
		$this->view->languages		= Zend_Registry::get('ws')->Language->getAllActive();
	}



	public function updateAction() {
		$values	= $this->getRequest()->getPost();
		$form	= Models_Banner::getSuperhomeForm($this->updateSectionId);
		$form->setDescription(i18n::tr('Position').': '.$this->locations[$values['locationId']]);

		if($form->isValid($values)) {
			$res = $this->ws->Banner->update($values);
			if(is_numeric($res))
				$this->view->feedbackMsg = UiUtil::printMessages(array('update-ok'));
			else
				$this->view->feedbackMsg = UiUtil::printErrors(array('update-error - ' . i18n::tr($res)));
		}
		else
			$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));

		$bannerForms = Models_Banner::getSuperhomeForms($this->updateSectionId, $this->locations);
		$bannerForms[$values['languageId']][$values['locationId']][$values['bannerId']] = $form;

		$this->view->bannerForms	= $bannerForms;
		$this->view->languages		= Zend_Registry::get('ws')->Language->getAllActive();

		$this->render('index');
	}
}
