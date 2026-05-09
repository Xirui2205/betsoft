<?php

class  ApprovalGroupController extends It6_Controller_Abstract {

	public $viewSectionId = 194;
	public $insertSectionId = 196;
	public $updateSectionId = 197;
	public $insertThresholdSectionId = 198;
	public $updateThresholdSectionId = 199;
	public $deleteThresholdSectionId = 200;
	public $assignSportSectionId = 201;
	public $assignEventSectionId = 202;

	public function init() {

		$this->ws = Zend_Registry::get('ws');

		$this->groupId = $this->getRequest()->getPost('approvalGroupId');
		$this->view->groupId = $this->groupId;
		$this->thresholdId = $this->getRequest()->getPost('thresholdId');
		$this->view->thresholdId = 	$this->thresholdId;

		$this->view->viewSectionId = 	$this->viewSectionId;
		$this->view->insertSectionId = 	$this->insertSectionId;
		$this->view->updateSectionId = 	$this->updateSectionId;

		$this->view->assignSportSectionId = $this->assignSportSectionId;
		$this->view->assignEventSectionId = $this->assignEventSectionId;


		$this->view->insertThresholdSectionId = $this->insertThresholdSectionId;
		$this->view->updateThresholdSectionId = $this->updateThresholdSectionId;
		$this->view->deleteThresholdSectionId = $this->deleteThresholdSectionId;

		$this->view->groups = $this->ws->ApprovalGroup->getAll();
		
		//var_dump($this->view->groups);
		$this->view->groupsCollection = Models_Utils::getCollection($this->view->groups,'approvalGroupId');
		parent::init();
		$this->jsIncludes->commonAjax = true;
	}

	public function indexAction() {

		if ($this->groupId) {
			$this->_helper->layout->setLayout('empty');

			$gr = $this->ws->ApprovalGroup->getById($this->groupId);
			$this->view->group = $gr;
			$this->view->interval = Models_ApprovalGroup::getInterval($gr->thresholdTable);

		}

		$this->view->empty = (count($this->view->groups)==0);
	}

	public function insertAction() {
		$this->_helper->layout->setLayout('empty');

		$form = new Models_Form_ApprovalGroup($this->insertSectionId, NULL);

		if ($this->getRequest()->getPost('submit')) {

			$data = $this->getRequest()->getPost();

			if ($form->isValid($data)) {
				unset($data['submit']);

				if ($id = $this->ws->ApprovalGroup->insert($data)) {
					$this->view->feedbackMsg = UiUtil::printMessages(array('insert-ok'));
					It6_Log::info(
						"Approval Group insert success : group '%ag_id%' name '%name%'.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'ag_id'		=> $id,
							'name'	=> $data['name']
						)
					);
				}
				else {
					$this->view->feedbackMsg = UiUtil::printErrors(array('insert-error'));
					It6_Log::err(
						"Approval Group insert error : group '%ag_id%'  name '%name%'.",
						It6_Log::TAG_ADMIN_OPERATION,
							array(
							'ag_id'		=> $id,
							'name'	=> $data['name']
						)
					);
				}
			}
			else {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
				$form->populate($data);
				$this->view->form = $form;
			}
		} else {
				$this->view->form = $form;
		}

		$this->view->groups = $this->ws->ApprovalGroup->getAll();
	}

	public function updateAction() {
		$this->_helper->layout->setLayout('empty');
		$form = new Models_Form_ApprovalGroup($this->updateSectionId,$this->groupId);

		if ($this->groupId) {
			$data = $this->ws->ApprovalGroup->getById($this->groupId);
			$form->populate(It6_ArrayWrapper::toNativeArray($data));
		}

		if ($this->getRequest()->getPost('submit')) {

			$data = $this->getRequest()->getPost();

			if ($form->isValid($data)) {

				unset($data['submit']);

				if ($id = $this->ws->ApprovalGroup->update($data)) {
					$this->view->feedbackMsg = UiUtil::printMessages(array('update-ok'));
					It6_Log::info(
						"Approval Group insert success : group '%ag_id%' name '%name%'.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'ag_id'		=> $id,
							'name'	=> $data['name']
						)
					);
				}
				else {
					$this->view->feedbackMsg = UiUtil::printErrors(array('update-error'));
					It6_Log::err(
						"Approval Group insert error : group '%ag_id%'  name '%name%'.",
						It6_Log::TAG_ADMIN_OPERATION,
							array(
							'ag_id'		=> $id,
							'name'	=> $data['name']
						)
					);
				}
			}
			else
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
		}

		$form->populate($data);

		$this->view->form = $form;

		$this->view->groups = $this->ws->ApprovalGroup->getAll();
	}

	public function insertThresholdAction() {
		$this->_helper->layout->setLayout('empty');

		$form = new Models_Form_ApprovalGroupThreshold($this->insertThresholdSectionId,$this->groupId);

		if ($this->getRequest()->getPost('submit')) {

			$data = $this->getRequest()->getPost();

			$this->groupId = $data['approvalGroupId'];

			if ($form->isValid($data)) {
				$data['oddUpperThreshold'] = str_replace(',','.',$data['oddUpperThreshold']);
				$data['stakeLowerThreshold'] = str_replace(',','.',$data['stakeLowerThreshold']);
				unset($data['submit']);

				if ($id = $this->ws->ApprovalGroupThreshold->insert($data)) {
					$this->view->feedbackMsg = UiUtil::printMessages(array('insert-ok'));
					It6_Log::info(
						"Approval Group Treshold insert success : group '%ag_id%' threshold '%agt_id%'.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'agt_id'		=> $id,
							'ag_id' 		=> $this->groupId,
							'oddUpperThreshold'	=> $data['oddUpperThreshold'],
							'stakeLowerThreshold'	=> $data['stakeLowerThreshold']
						)
					);
				}
				else {
					$this->view->feedbackMsg = UiUtil::printErrors(array('insert-error'));
					It6_Log::err(
						"Approval Group Treshold insert error : group '%ag_id%' threshold '%agt_id%'.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'agt_id'		=> $id,
							'ag_id' 		=> $this->groupId,
							'oddUpperThreshold'	=> $data['oddUpperThreshold'],
							'stakeLowerThreshold'	=> $data['stakeLowerThreshold']
						)
					);
				}

			}
			else {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
				$form->populate($data);
				$this->view->form = $form;
			}
		} else {
				$this->view->form = $form;
		}
		$gr = $this->ws->ApprovalGroup->getById($this->groupId);
		$this->view->group = $gr;
		$this->view->interval = Models_ApprovalGroup::getInterval($gr->thresholdTable);
	}

	public function updateThresholdAction() {
		$this->_helper->layout->setLayout('empty');

		$form = new Models_Form_ApprovalGroupThreshold($this->updateThresholdSectionId,$this->groupId);

		if ($this->thresholdId) {
			$data = $this->ws->ApprovalGroupThreshold->getById($this->thresholdId);
			$data['oddUpperThreshold'] = str_replace('.',',',$data['oddUpperThreshold']);
			$data['stakeLowerThreshold'] = str_replace('.',',',$data['stakeLowerThreshold']);
			$thrIdElm = $form->createElement('hidden', 'thresholdId')->setValue($this->thresholdId);
			$form->addElement($thrIdElm);

			$form->populate(It6_ArrayWrapper::toNativeArray($data));
		}

		if ($this->getRequest()->getPost('submit')) {

			$data = $this->getRequest()->getPost();
			

			$this->groupId = $data['approvalGroupId'];

			if ($form->isValid($data)) {
				$data['oddUpperThreshold'] = str_replace(',','.',$data['oddUpperThreshold']);
				$data['stakeLowerThreshold'] = str_replace(',','.',$data['stakeLowerThreshold']);
				unset($data['submit']);

				if ($id = $this->ws->ApprovalGroupThreshold->update($data)) {
					$this->view->feedbackMsg = UiUtil::printMessages(array('update-ok'));
					It6_Log::info(
						"Approval Group Treshold update success : group '%ag_id%' threshold '%agt_id%'.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'agt_id'		=> $id,
							'ag_id' 		=> $this->groupId,
							'oddUpperThreshold'	=> $data['oddUpperThreshold'],
							'stakeLowerThreshold'	=> $data['stakeLowerThreshold']
						)
					);
				}
				else {
					$this->view->feedbackMsg = UiUtil::printErrors(array('update-error'));
					It6_Log::err(
						"Approval Group Treshold update error : group '%ag_id%' threshold '%agt_id%'.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'agt_id'		=> $id,
							'ag_id' 		=> $this->groupId,
							'oddUpperThreshold'	=> $data['oddUpperThreshold'],
							'stakeLowerThreshold'	=> $data['stakeLowerThreshold']
						)
					);
				}

			}
			else {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
				$form->populate($data);
				$this->view->form = $form;
			}
		} else {
				$this->view->form = $form;
		}
		$gr = $this->ws->ApprovalGroup->getById($this->groupId);
		$this->view->group = $gr;
		$this->view->interval = Models_ApprovalGroup::getInterval($gr->thresholdTable);
	}

	public function deleteThresholdAction() {
		$this->_helper->layout->setLayout('empty');

		if ($id = $this->ws->ApprovalGroupThreshold->delete($this->thresholdId)) {
			$this->view->feedbackMsg = UiUtil::printMessages(array('delete-ok'));
			It6_Log::info(
				"Approval Group  delete success : group '%ag_id%'.",
				It6_Log::TAG_ADMIN_OPERATION,
				array(
					'ag_id'		=> $id
				)
			);
		}
		else {
			$this->view->feedbackMsg = UiUtil::printErrors(array('delete-error'));
			It6_Log::err (
				"Approval Group  delete error : group '%ag_id%'.",
				It6_Log::TAG_ADMIN_OPERATION,
				array(
					'ag_id'		=> $id
				)
			);
		}

		$gr = $this->ws->ApprovalGroup->getById($this->groupId);
		$this->view->group = $gr;
		$this->view->interval = Models_ApprovalGroup::getInterval($gr->thresholdTable);
	}

	public function assignSportAction() {

		$this->_helper->layout->setLayout('empty');

		$data = $this->getRequest()->getPost('sports');
		$groupId = $this->getRequest()->getPost('group');

		if ($this->getRequest()->getPost('submit')) {

			if ($data && isset($groupId)) {
				if ($this->ws->Sport->setApprovalGroupId($data, $groupId)) {
					if ($groupId == 0) {
						$this->view->feedbackMsg = UiUtil::printMessages(array('Approval group was removed from sport(s).'));
						It6_Log::info(
							"Approval Group was removed from sport(s): '%sports%'.",
							It6_Log::TAG_BOOKMAKER_OPERATION,
							array(
								'ag_id'		=> $groupId,
								'sports'	=> implode(',',$data)
							)
						);
					} else {
						$this->view->feedbackMsg = UiUtil::printMessages(array('Approval group was assigned successfully.'));
						It6_Log::info(
							"Approval Group  assign to a sport ('%sports%') success : group '%ag_id%'.",
							It6_Log::TAG_BOOKMAKER_OPERATION,
							array(
								'ag_id'		=> $groupId,
								'sports'	=> implode(',',$data)
							)
						);
					}
				}
				else {
					$this->view->feedbackMsg = UiUtil::printErrors(array('assign-error'));
					It6_Log::err(
						"Approval Group  assign to a sport error : group '%ag_id%'.",
						It6_Log::TAG_BOOKMAKER_OPERATION,
						array(
							'ag_id'		=> $groupId,
							'sports'	=> implode(',',$data)
						)
					);
				}
			} else
				$this->view->feedbackMsg = UiUtil::printErrors(array('Choose sport and group!'));
		}

		//comboboxy
/*
		$allSports = $this->ws->Sport->getAll();
		$this->view->sports = Models_ApprovalGroup::getSportsMultiCheckBox($allSports,$this->groupId,$this->view->groupsCollection);
*/

		$this->view->sportOpts = Models_ApprovalGroup::getSportsMultiSelectWithApprovalGroup('sports[]',30);
		$gr = $this->ws->ApprovalGroup->getAllRelevant();
		$this->view->groups = Models_ApprovalGroup::getGroupComboBox($gr);

	}

	public function assignEventAction() {

		$this->_helper->layout->setLayout('empty');

		$filter = NULL;
		$sportIds = $this->getRequest()->getPost('sports');
		$regionIds = $this->getRequest()->getPost('regions');
		$data = $this->getRequest()->getPost('events');
		$groupId = $this->getRequest()->getPost('group');

		if ($this->getRequest()->getPost('submit')) {
			if ($data && isset($groupId)) {
				if ($this->ws->Event->setApprovalGroupId($data, $groupId)) {
					if ($groupId == 0) {
						$this->view->feedbackMsg = UiUtil::printMessages(array('Approval group was removed from sport(s).'));
						It6_Log::info(
							"Approval Group was removed from sport(s): '%sports%'.",
							It6_Log::TAG_BOOKMAKER_OPERATION,
							array(
								'ag_id'		=> $groupId,
								'sports'	=> implode(',',$data)
							)
						);
					} else {
						$this->view->feedbackMsg = UiUtil::printMessages(array('Approval group was assigned successfully.'));
						It6_Log::info(
							"Approval Group  assign to a sport ('%sports%') success : group '%ag_id%'.",
							It6_Log::TAG_BOOKMAKER_OPERATION,
							array(
								'ag_id'		=> $groupId,
								'sports'	=> implode(',',$data)
							)
						);
					}
				}
				else {
					$this->view->feedbackMsg = UiUtil::printErrors(array('assign-error'));
					$this->view->feedbackMsg = UiUtil::printErrors(array('assign-error'));
					It6_Log::err(
						"Approval Group  assign to events error : group '%ag_id%'.",
						It6_Log::TAG_ADMIN_OPERATION,
						array(
							'ag_id'		=> $groupId,
							'events'	=> implode(',',$data)
						)
					);
				}
			} else {
				$filter = array('sports' => $sportIds, 'regions' => $regionIds);
			}
				//$this->view->feedbackMsg = UiUtil::printErrors(array('Choose sport and group!'));
				
		}

/*
		$allSports = $this->ws->Sport->getAll();
		$this->view->sports = Models_ApprovalGroup::getSportsComboBox($allSports, $sportId, $this->view->groupsCollection);
*/
		//comboboxy
/*
		if ($sportId) {
			$allEvents = $this->ws->Event->getAllWhere(array('sport_id=?' => $sportId));
			$this->view->events = Models_ApprovalGroup::getEventsComboBox($allEvents, $this->groupId, $this->view->groupsCollection);
		} else {
			$this->view->events = UiUtil::printWarnings(array('To see the events, choose a sport first.'));
		}
*/

		$this->view->sportOpts = It6_Gui_Sport::getMultiSelect(20);
		$this->view->regionOpts = It6_Gui_Region::getMultiSelect('regions', 20);
		$this->view->eventOpts = Models_ApprovalGroup::getEventsMultiSelectWithApprovalGroup('events[]',$filter);

		$gr = $this->ws->ApprovalGroup->getAllRelevant();
		$this->view->groups = Models_ApprovalGroup::getGroupComboBox($gr);

	}
}
