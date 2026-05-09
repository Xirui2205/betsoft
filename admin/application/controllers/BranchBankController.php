<?php

class BranchBankController extends controllers_BranchAbstractController {

	var $viewSectionId = 164;
	var $updateSectionId = 169;



	public function init() {
		parent::init();
	}



	public function indexAction() {
	}



	public function viewAction() {
		$this->_helper->layout->setLayout('empty');
		$this->view->bank = Models_Branch::getBankInfo($this->branchId);
		$this->view->branchId = $this->branchId;
	}



	public function updateAction() {

		$this->_helper->layout->setLayout('empty');
		$this->view->ws			= $this->ws;
		$branchId 				= $this->getRequest()->getPost('branchId');
		$this->view->branchId	= $branchId;
		$bank					= Models_Branch::getBankInfo($this->branchId);
		$branchBankForm			= array();
		$accountTypes			= $this->ws->BankAccountType->getAll();

		foreach($accountTypes as $account) {
			$bank[$account['name']]['branchId']		= $branchId;
			$bank['name']['typeName'] 				= $account['id'];
			if($account['name'] == 'provision')
				$branchBankForm[$account['name']] = new Models_Form_BranchBank($this->updateSectionId, $account);
			else
				$branchBankForm[$account['name']] = new Models_Form_BranchBank($this->updateSectionId, $account, true);

			$branchBankForm[$account['name']]->populate($bank[$account['name']]);
		}

		$this->view->branchBankForm 	= $branchBankForm;


		if ($this->getRequest()->getPost('save')) {
			if (!$branchBankForm[$this->getRequest()->getPost('save')]->isValid( $this->getRequest()->getPost() ))
				$this->view->feedbackMsg = UiUtil::printErrors(array('error_form_not_valid_general'));

			else {
				$values = $branchBankForm[$this->getRequest()->getPost('save')]->getValues();

				if ( $this->ws->BankAccount->update($values)){
					$this->view->feedbackMsg	= UiUtil::printMessages(I18n::tr('form_update_success_general'));
					$this->view->bank 			= Models_Branch::getBankInfo($this->branchId);
					$this->view->trView			= i18n::tr('View');

					return $this->render('view');
				}
				else
					$this->view->feedbackMsg = UiUtil::printErrors(I18n::tr('form_update_error_general'));
			}
		}

		return $this->render('update');
	}
}
