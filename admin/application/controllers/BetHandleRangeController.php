<?php

class BetHandleRangeController extends It6_Controller_Abstract {

	var $viewSectionId = 218;
	var $insertSectionId = 219;
	var $updateSectionId = 220;

	public function init() {

		parent::init();

		$this->ws = Zend_Registry::get('ws');

		$this->betHandleRangeId = $this->getRequest()->getPost('betHandleRangeId');
		$this->sportId = $this->getRequest()->getPost('sportId');

		$this->view->betHandleRangeId = $this>betHandleRangeId;

		$this->view->viewSectionId = 	$this->viewSectionId;
		$this->view->insertSectionId = 	$this->insertSectionId;
		$this->view->updateSectionId = 	$this->updateSectionId;

		$this->view->betHandleRanges = $this->ws->BetHandleRange->getAll();
		$this->view->empty = (count($this->view->betHandleRanges)==0);

		$this->jsIncludes->commonAjax = true;
	}

	public function indexAction() {
		if ($this->teamId) {
			$this->_helper->layout->setLayout('empty');
			$this->view->team = $this->ws->Team->getById($this->teamId);
		}
	}

	public function insertAction() {
		$showForm = true;

		$this->_helper->layout->setLayout('empty');

		$this->betHandleRangeId = 0;

		$form = new Models_Form_BetHandleRange($this->insertSectionId);

		if ($this->getRequest()->getPost('submit')) {

			$data = $this->getRequest()->getPost();

			if ($form->isValid($data)) {
				$this->sportId = $data['sportId'];
				unset($data['submit']);
				unset($data['sportId']);
				$data['typId']=16;

				if ($this->ws->BetHandleRange->insert($data)) {
					$this->view->feedbackMsg = UiUtil::printMessages(array('insert-ok'));

					//prepare form for inserting teams
					$this->rangeLowerLimit = $data['from'];
					$this->rangeUpperLimit = $data['to'];
					$this->view->formTeams = new Models_Form_TeamHasBetHandleRange($this->insertSectionId);
				}
				else {
					$this->view->feedbackMsg = UiUtil::printErrors(array('insert-error'));
				}
			}
			else {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
			}

			if ($showForm) {
				$form->populate($data);
				$this->view->form = $form;
			}
		} else {
			$this->view->form = $form;
		}
	}

	public function updateAction() {
		$this->_helper->layout->setLayout('empty');

		$form = new Models_Form_BetHandleRange($this->updateSectionId);

		if ($this->betHandleRangeId) {
			$data = $this->ws->BetHandleRange->getById($this->betHandleRangeId);
			$this->sportId = $data['sportId'];
			$form = new Models_Form_BetHandleRange($this->updateSectionId);
			$form->populate(It6_ArrayWrapper::toNativeArray($data));
			//prepare form for inserting teams
			$this->rangeLowerLimit = $data['from'];
			$this->rangeUpperLimit = $data['to'];

			$formTeams = new Models_Form_TeamHasBetHandleRange($this->updateSectionId);
			$helpPopulate = array();
			for($i = 0; $i<=$this->rangeUpperLimit-$this->rangeLowerLimit; $i++) {
				$helpPopulate['teamId_'.$i] = $data->teams[$i]['team_id'];
			}
			$formTeams->populate($helpPopulate);

			$this->view->formTeams = $formTeams;
		}

		if ($this->getRequest()->getPost('submit')) {

			$data = $this->getRequest()->getPost();
//var_dump($_POST);
			if ($form->isValid($data)) {
				unset($data['submit']);

				if ($this->ws->BetHandleRange->update($data))
					$this->view->feedbackMsg = UiUtil::printMessages(array('update-ok'));
				else
					$this->view->feedbackMsg = UiUtil::printErrors(array('update-error'));
			}
			else
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
		}

		$form->populate($data);
		$this->view->form = $form;
	}
}
