<?php

class BranchHostController extends controllers_BranchAbstractController {

	var $viewSectionId = 180;
	var $updateSectionId = 181;
	var $insertSectionId = 182;

	public function init() {
		parent::init();

		$this->hostId = $this->getRequest()->getPost('hostId');

		$this->view->branchId = $this->branchId;
		$this->view->hostId = $this->hostId;
		
		if (Zend_Registry::get('acl')->userHasRole(It6_Acl_Admin::ROLE_CALLCENTRUM))
			$this->view->callcenterLogged = true;
		else
			$this->view->callcenterLogged = false;
	}

	public function indexAction() {
	}

	public function viewAction() {
		$this->_helper->layout->setLayout('empty');
		$hosts = $this->ws->Host->getByBranchId( (integer)$this->branchId );
		if (!empty($hosts)) {
			$hosts = It6_ArrayWrapper::toNativeArray($hosts);
			Models_Host::addOnlineStatus($hosts, true);
		}
		$this->view->hosts = $hosts;

/*		$this->view->branchId = $this->branchId;
		$this->view->hostId = $this->hostId;
*/
		if ($this->hostId) {
			$host = $this->ws->Host->getById( (integer)$this->hostId );
			if (!empty($host)) {
				$host = It6_ArrayWrapper::toNativeArray($host);
				Models_Host::addOnlineStatus($host, false);
			}
			$this->view->host = $host;
		}
	}

	public function insertAction() {
		$this->_helper->layout->setLayout('empty');
		$form = new Models_Form_BranchHost($this->insertSectionId);
		$bIdElm = $form->createElement('hidden', 'branchId')->setValue($this->branchId);
		$form->addElement($bIdElm);

		if ($this->getRequest()->getPost('submit')) {

			$data = $this->getRequest()->getPost();

			if ($form->isValid($data)) {
				unset($data['submit']);
				if ($this->ws->Host->insert($data))
					$this->view->feedbackMsg = UiUtil::printMessages(I18n::tr('form_insert_success_general'));
				else
					$this->view->feedbackMsg = UiUtil::printErrors(I18n::tr('form_insert_error_general'));
			}
			else {
				$this->view->feedbackMsg = UiUtil::printErrors(I18n::tr('error_form_not_valid_general'));
				$form->populate($data);
				$this->view->form = $form;
			}
		} else {
				//commented out by Marti 21.11. 2010 if this is insert only it should not be needed ant it causes error messages
				//$form->populate($data);
				$this->view->form = $form;
		}

		$this->view->hosts = $this->ws->Host->getByBranchId( (integer)$this->branchId );
	}

	public function updateAction() {
		$this->_helper->layout->setLayout('empty');
		$form = new Models_Form_BranchHost($this->updateSectionId);

		$hId = $form->createElement('hidden', 'hostId')->setValue($this->hostId);
		$form->addElement($hId);

		$bId = $form->createElement('hidden', 'branchId')->setValue($this->branchId);
		$form->addElement($bId);

/*		$this->view->branchId = $this->branchId;
		$this->view->hostId = $this->hostId;
*/
		if ($this->hostId) {
			$data = $this->ws->Host->getById( (integer)$this->hostId );
			$form->populate(It6_ArrayWrapper::toNativeArray($data));
		}

		if ($this->getRequest()->getPost('submit')) {

			$data = $this->getRequest()->getPost();
			$data['hostId'] = $this->hostId;

			if ($form->isValid($data)) {

				unset($data['submit']);

				if ($this->ws->Host->update($data))
					$this->view->feedbackMsg = UiUtil::printMessages(I18n::tr('form_update_success_general'));
				else
					$this->view->feedbackMsg = UiUtil::printErrors(I18n::tr('form_update_error_general'));
			}
			else
				$this->view->feedbackMsg = UiUtil::printErrors(I18n::tr('error_form_not_valid_general'));
		}

		$this->view->hosts = $this->ws->Host->getByBranchId( (integer)$this->branchId );

		$this->view->form = $form;
	}
}
