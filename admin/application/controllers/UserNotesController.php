<?php

class UserNotesController extends controllers_UserAbstractController {

	protected $viewSectionId		= 229;
	protected $updateSectionId		= 230;

	protected $userId;
	protected $ws;


	public function init() {
		parent::init();
		
		if (Zend_Registry::get('acl')->userHasRole(It6_Acl_Admin::ROLE_CALLCENTRUM))
			$this->view->callcenterLogged = true;
		else
			$this->view->callcenterLogged = false;
		
		if (Zend_Registry::get('acl')->userHasRole(It6_Acl_Admin::ROLE_SUPERVISION))
			$this->view->supervisionLogged = true;
		else
			$this->view->supervisionLogged = false;
	}



	public function viewAction() {
		$this->_helper->layout->setLayout('empty');

		$notes = $this->ws->UserNote->getByUserId( (integer)$this->userId );
		$notes = It6_ArrayWrapper::toNativeArray($notes);

		foreach($notes as $key => $note) {
			$notes[$key]['date'] = It6_Date::fromDb($note['date']);
			$notes[$key]['text'] = nl2br($note['text']);
		}
		$this->view->userNotes = $notes;
	}



	public function updateAction() {
		$this->_helper->layout->setLayout('empty');



		if($this->getRequest()->getPost('saveTemplate')) {
			$values			= $this->getRequest()->getPost();
			$userKolekce	= new Models_Userkolekce;
			$result			= $userKolekce->_addTemplateMessage($values['s_nazev'], $values['s_telo']);

			if($result)
				$this->view->feedbackMsg = UiUtil::printMessages(array('template-insert-ok'));
			else
				$this->view->feedbackMsg = UiUtil::printErrors(array('template-insert-error'));
		}

		if($this->getRequest()->getPost('removeTemplate')) {
			$templateId		= $this->getRequest()->getPost('removeTemplate');
			$userKolekce	= new Models_Userkolekce;
			$result			= $userKolekce->_deleteTemplateMessage($templateId);

			if($result)
				$this->view->feedbackMsg = UiUtil::printMessages(array('template-delete-ok'));
			else
				$this->view->feedbackMsg = UiUtil::printErrors(array('template-delete-error'));
		}

		else if($this->getRequest()->getPost('save')) {

			$userKolekce = new Models_Userkolekce;
			$result = $userKolekce->CreatePoznamka($this->userId);

			if($result === true) {
				$this->view->feedbackMsg = UiUtil::printMessages(array('note-insert-ok'));
				$this->_forward('view');
			}
			else
				$this->view->feedbackMsg = UiUtil::printErrors(array('note-insert-error'.$result));
		}



		$templates = Models_User::_getTemplateMessages();
		foreach($templates as $key => $template) {
			$telo = "'".addslashes($template['telo'])."'";
			$telo = str_replace("\n", "\\n", $telo);
			$telo = str_replace("\r", "\\r", $telo);
			$templates[$key]['telo'] = $telo;
		}
		$this->view->templateList = $templates;
	}
}
