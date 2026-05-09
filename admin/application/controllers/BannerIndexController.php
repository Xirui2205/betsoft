<?php

class BannerIndexController extends controllers_BannerAbstractController {

	protected $indexSectionId	= 259;
	protected $updateSectionId	= 260;
	protected $insertSectionId	= 261;
	protected $indexCotrollerId	= 1;
	protected $locations;



	public function init() {
		parent::init();
		$this->locations = array(
			'1' => i18n::tr('Left'),
			'2' => i18n::tr('Right')
		);
	}



	public function indexAction() {
		$this->view->bannerForms	= Models_Banner::getIndexForms($this->updateSectionId, $this->locations);
		$this->view->languages		= Zend_Registry::get('ws')->Language->getAllActive();
	}



	public function updateAction() {
		$values	= $this->getRequest()->getPost();
		$form	= Models_Banner::getIndexForm($this->updateSectionId, $this->locations);

		if($form->isValid($values)) {
			$res = $this->ws->Banner->update($values);
			if(is_numeric($res))
				$this->view->feedbackMsg = UiUtil::printMessages(array('update-ok'));
			else
				$this->view->feedbackMsg = UiUtil::printErrors(array('update-error - ' . i18n::tr($res)));
		}
		else
			$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));

		$bannerForms = Models_Banner::getIndexForms($this->updateSectionId, $this->locations);
		$bannerForms[$values['languageId']][$values['locationId']][$values['bannerId']] = $form;

		$this->view->bannerForms	= $bannerForms;
		$this->view->languages		= Zend_Registry::get('ws')->Language->getAllActive();

		$this->render('index');
	}



	public function insertAction() {
		$values		= $this->getRequest()->getPost();
		$form		= Models_Banner::getIndexForm($this->insertSectionId, $this->locations);
		$languages	= $this->ws->Language->getAll();
		$languages	= It6_Models_Form_Util::getSelectOptions($languages, 'languageId', 'name');

		$langElement = $form
			->createElement('select', 'languageId')
			->setLabel(i18n::tr('Language'))
			->addMultiOptions($languages);
		$form->addElement($langElement);

		if(isset($values['submit'])) {
			if($form->isValid($values)) {
				$values['controllerId'] = $this->indexCotrollerId;
				$res = $this->ws->Banner->insert($values);
				if(is_numeric($res)) {
					$this->view->feedbackMsg	= UiUtil::printMessages(array('insert-ok'));
					$this->view->bannerForms	= Models_Banner::getIndexForms($this->updateSectionId, $this->locations);
					$this->view->languages		= Zend_Registry::get('ws')->Language->getAllActive();
					$this->render('index');
				}
				else
					$this->view->feedbackMsg = UiUtil::printErrors(array('insert-error - ' . i18n::tr($res)));
			}
			else
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
		}

		$this->view->bannerForm = $form;
	}
}
