<?php

class TeamController extends It6_Controller_Abstract {

	private $indexSectionId		= 214;
	private $insertSectionId	= 215;
	private $updateSectionId	= 216;
	private $playersSectionId	= 322;
	private $deletePlayerSectionId = 323;
	private $updatePlayerSectionId = 324;
	
	private $filterData		= array();
	private $paginatorData	= array('recsPerPage' => 15);
	private $orderData		= array('name ASC');

	private static $TBODY_LAYOUT = 'team-tbody';
	private static $TBODY_PLAYERS_LAYOUT = 'players-tbody';
	
	public function init() {
		parent::init();

		$this->jsIncludes->commonAjax	= true;
		$this->jsIncludes->jqueryUI	= true;
		$this->ws = Zend_Registry::get('ws');

		$this->teamId = $this->getRequest()->getPost('teamId');
		if (!empty($this->teamId))
			$this->view->teamId = $this->teamId;

		$this->teamPlayerId = $this->getRequest()->getPost('teamPlayerId');
		if (!empty($this->teamPlayerId))
			$this->view->teamPlayerId = $this->teamPlayerId;
		
		$this->view->indexSectionId		= $this->indexSectionId;
		$this->view->insertSectionId	= $this->insertSectionId;
		$this->view->updateSectionId	= $this->updateSectionId;		
		$this->view->playersSectionId	= $this->playersSectionId;
		$this->view->deletePlayerSectionId = $this->deletePlayerSectionId;
		$this->view->updatePlayerSectionId = $this->updatePlayerSectionId;	
	}

	public function indexAction() {
		$inData			= $this->getRequest()->getPost();
		$extensions		= array();

		//prepare wsForm data from $_POST
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['paginator']))
			$this->paginatorData = $inData['paginator'];
		if(!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);


		$sports = It6_ArrayWrapper::toNativeArray($this->ws->Sport->getAllOrder(array('name')));
		$sports = Models_Utils::getCollection($sports, 'sportId','name',array('' => '--Select--'));
		
		$filter = new It6_WsForm_Filter(array(
			array('Team Id', 'teamId', 'text', array(array('teamId', '=')), 'Zend_Validate_Int'),
			array('Betradar Id', 'betradarId', 'text', array(array('betradarId', '=')), 'Zend_Validate_Int'),
			array('Sport', 'sportId', 'select', array(array('sportId', '=')), 'Zend_Validate_Int', NULL, $sports),
			//array('Sport Name', 'sportName', 'text', array(array('text', 'sportName')), 'LIKE'),
			array('Name', 'name', 'text', array(array('name', 'LIKE', '%?%'))),
			array('Short Name', 'shortName', 'text', array(array('shortName', 'LIKE', '%?%'))),
		));
		$filter->getExtension($this->filterData, $extensions);

		//create pagination
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
		$paginator->getExtension($this->paginatorData, $extensions);

		//create table
		$table = new It6_WsForm_Table(array(
			array(),
			array('Team Id', 'teamId'),
			array('Betradar Id', 'betradarId'),
		//	array('Sport Id', 'sportId'),
			array('Sport', 'sportName'),
			array('Name', 'name'),
			array('Short Name', 'shortName'),
		));
		$table->getColumnsExtension($extensions);
		$table->getOrderExtension($this->orderData, $extensions);

		//get user list by WS
		$teams = Zend_Registry::get('ws')->ext($extensions)->Team->getAll();
		$teams = It6_ArrayWrapper::toNativeArray($teams);

		$this->view->filter		= $filter->getLayout(null, $this->filterData);
		$this->view->paginator	= $paginator->getLayout(null, null, $extensions['paginator']->getResponse());
		$this->view->tHead		= $table->getTheadLayout(null,$this->orderData);
		$this->view->tBody		= $table->getTbodyLayout(self::$TBODY_LAYOUT, $teams);
	}



	public function insertAction() {
		$showForm = true;

		$this->_helper->layout->setLayout('empty');

		$form = new Models_Form_Team($this->insertSectionId, $this->indexSectionId);

		if ($this->getRequest()->getPost('submit')) {

			$data = $this->getRequest()->getPost();

			if ($form->isValid($data)) {
				unset($data['submit']);

				if ($this->ws->Team->insert($data)) {
					$this->view->feedbackMsg = UiUtil::printMessages(array('insert-ok'));
					It6_Log::notice(
						"Team insert %team% success.",
						It6_Log::TAG_BOOKMAKER_OPERATION,
						array('team'=>$data['name'],'data' => $data)
					);
					$showForm = false;
				}
				else {
					$this->view->feedbackMsg = UiUtil::printErrors(array('insert-error'));
					It6_Log::warn(
						"Error inserting team %team%",
						It6_Log::TAG_BOOKMAKER_OPERATION,
						array('team'=>$data['name'],'data' => $data)
					);
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

		$form = new Models_Form_Team($this->updateSectionId, $this->indexSectionId);

		if ($this->teamId) {
			$data = $this->ws->Team->getById($this->teamId);
			$data = It6_ArrayWrapper::toNativeArray($data);
			$form->populate(It6_ArrayWrapper::toNativeArray($data));
		}

		if ($this->getRequest()->getPost('submit') && !$this->getRequest()->getPost('teamPlayerId')) {

			$data = $this->getRequest()->getPost();

			if ($form->isValid($data)) {

				unset($data['submit']);

				if ($this->ws->Team->update($data)) {
					$this->view->feedbackMsg = UiUtil::printMessages(array('update-ok'));
					It6_Log::notice(
						"Team update %team% success.",
						It6_Log::TAG_BOOKMAKER_OPERATION,
						array('team'=>$data['name'],'data' => $data)
					);
				}
				else {
					$this->view->feedbackMsg = UiUtil::printErrors(array('update-error'));
					It6_Log::warn(
						"Error updating team %team%",
						It6_Log::TAG_BOOKMAKER_OPERATION,
						array('team'=>$data['name'],'data' => $data)
					);
				}
			}
			else
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));

			$form->populate($data);
		}


		$this->view->form = $form;
	}
	
	public function playersAction() {
		$this->_helper->layout->setLayout('empty');
		$form = new Models_Form_TeamPlayer($this->playersSectionId, $this->indexSectionId );
		
		$params = $this->getRequest()->getParams();

		if ($this->getRequest()->getPost('submit') && empty($params['teamPlayerId'])) {
		
			$data = $this->getRequest()->getPost();
			if ($form->isValid($data)) {
				unset($data['submit']);
		
				if ($this->ws->TeamPlayer->insert($data)) {
					$this->view->feedbackMsg = UiUtil::printMessages(array('insert-ok'));
					It6_Log::notice(
							"Player insert %team% success.",
							It6_Log::TAG_BOOKMAKER_OPERATION,
							array('teamPlayer'=>$data['name'],'data' => $data)
					);
					$showForm = false;
				}
				else {
					$this->view->feedbackMsg = UiUtil::printErrors(array('insert-error'));
					It6_Log::warn(
							"Error inserting team player %team%",
							It6_Log::TAG_BOOKMAKER_OPERATION,
							array('teamPlayer'=>$data['name'],'data' => $data)
					);
				}
			}
			else {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
			}
		}
		
	
		if ($this->teamId) {
			$form->populate(array('teamId' => $this->teamId));
			$this->view->form = $form;
				
			$extensions		= array();
			//create table
			$table = new It6_WsForm_Table(array(
					array(),
					array('Player Id', 'teamPlayerId'),
					array('Name', 'name'),
			));
			$table->getColumnsExtension($extensions);
			$table->getOrderExtension($this->orderData, $extensions);
			
			//get user list by WS
			$players = Zend_Registry::get('ws')->ext($extensions)->TeamPlayer->getByTeam($this->teamId);
			$players = It6_ArrayWrapper::toNativeArray($players);
			
			$this->view->tHead		= $table->getTheadLayout();
			$this->view->tBody		= $table->getTbodyLayout(self::$TBODY_PLAYERS_LAYOUT, $players);
		}
		
		$form = new Models_Form_TeamPlayer($this->playersSectionId, $this->indexSectionId, $this->teamId);
		
		
		
	}
	
	public function updatePlayerAction() {
		$this->_helper->layout->setLayout('empty');
	
		$form = new Models_Form_TeamPlayer($this->updatePlayerSectionId, $this->indexSectionId );
	
		if ($this->teamPlayerId){
			$data = $this->ws->TeamPlayer->getById($this->teamPlayerId);
			$data = It6_ArrayWrapper::toNativeArray($data);
			$form->populate(It6_ArrayWrapper::toNativeArray($data));
			$this->view->form = $form;
		}
	
		if ($this->getRequest()->getPost('submit')) {
			$data = $this->getRequest()->getPost();
	
			if ($form->isValid($data)) {
				unset($data['submit']);
	
				if ($this->ws->TeamPlayer->update($data)) {
					$this->view->feedbackMsg = UiUtil::printMessages(array('update-ok'));
					It6_Log::notice(
							"Player update %teamPlayer% success.",
							It6_Log::TAG_BOOKMAKER_OPERATION,
							array('teamPlayer'=>$data['name'],'data' => $data)
					);					
					$this->_forward('players');
				}
				else {
					$this->view->feedbackMsg = UiUtil::printErrors(array('update-error'));
					It6_Log::warn(
							"Error update team player %teamPlayer%",
							It6_Log::TAG_BOOKMAKER_OPERATION,
							array('teamPlayer'=>$data['name'],'data' => $data)
					);
				}
			}
			else {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
			}
		} else {
			$this->view->form = $form;
		}
	}
	
	public function deletePlayerAction(){
		if ($this->teamPlayerId) {			 
			if ($this->ws->TeamPlayer->delete($this->teamPlayerId)) {
					$this->view->feedbackMsg = UiUtil::printMessages(array('delete-ok'));
					It6_Log::notice(
							"Player delete %teamPlayer% success.",
							It6_Log::TAG_BOOKMAKER_OPERATION,
							array('teamPlayer'=>$data['name'],'data' => $data)
					);
					$this->_forward('players');
				}
				else {
					$this->view->feedbackMsg = UiUtil::printErrors(array('delete-error'));
					It6_Log::warn(
							"Error delete team player %teamPlayer%",
							It6_Log::TAG_BOOKMAKER_OPERATION,
							array('teamPlayer'=>$data['name'],'data' => $data)
					);
				}
		}
	}
}
