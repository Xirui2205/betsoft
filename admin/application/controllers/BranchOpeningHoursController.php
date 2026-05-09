<?php

class BranchOpeningHoursController extends controllers_BranchAbstractController {


	var $viewSectionId = 286;
	var $updateSectionId = 287;


	public function init() {
		parent::init();

		$this->view->branchId = $this->branchId;
		
		if (Zend_Registry::get('acl')->userHasRole(It6_Acl_Admin::ROLE_CALLCENTRUM))
			$this->view->callcenterLogged = true;
		else
			$this->view->callcenterLogged = false;
	}



	public function indexAction() {

	}



	public function viewAction() {
		$this->_helper->layout->setLayout('empty');
		$this->view->openingHours = $this->ws->Branch->getOpeningHoursByBranchId( (integer)$this->branchId );
	}



	public function updateAction() {
		$this->_helper->layout->setLayout('empty');
		$form = new Models_Form_BranchOpeningHours($this->updateSectionId);
		$bId = $form->createElement('hidden', 'branchId')->setValue($this->branchId);
		$form->addElement($bId);

		$openingHours = $this->ws->Branch->getOpeningHoursByBranchId( (integer)$this->branchId );
		$openingHours =It6_ArrayWrapper::toNativeArray($openingHours);
		foreach($openingHours as &$dayHours) {
			foreach($dayHours as &$interval) {
				$interval = implode('-',$interval);
			}
			$dayHours = implode(';',$dayHours);
		}
		$form->populate($openingHours);

		if ($this->getRequest()->getPost('submit')) {
			$data = $this->getRequest()->getPost();

			if ($form->isValid($data)) {
				unset($data['submit']);
				unset($data['branchId']);

				if ($this->ws->Branch->updateOpeningHours($this->branchId, $data))
					$this->view->feedbackMsg = UiUtil::printMessages(I18n::tr('form_update_success_general'));
				else
					$this->view->feedbackMsg = UiUtil::printErrors(I18n::tr('form_update_error_general'));
			}
			else
				$this->view->feedbackMsg = UiUtil::printErrors(I18n::tr('error_form_not_valid_general'));
		}

		
		$this->view->form = $form;
	}
}
