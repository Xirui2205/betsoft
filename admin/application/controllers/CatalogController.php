<?php

class CatalogController extends It6_Controller_Abstract {

	private $indexSectionIdTeam	= 266;
	private $indexSectionIdComb	= 269;
	private $indexSectionIdBet	= 292;

/*
	private $insertSectionId	= 215;
	private $updateSectionId	= 216;
*/

	private $filterData		= array();
	private $paginatorData	= array('recsPerPage' => 15);
	private $orderDataTeam		= array('name ASC');
	private $orderDataCombination		= array('betId DESC');

	private static $TEAM_TBODY_LAYOUT = 'catalog-team-tbody';
	private static $COMB_TBODY_LAYOUT = 'catalog-combination-tbody';
	private static $BET_TBODY_LAYOUT = 'catalog-bet-tbody';
	
	private static $BET_STATUS_TO_SHOW = array(0,2);
	private static $BET_TYPES_TO_SHOW = array(19,21,22,29,30,31,32,165);
	private static $BET_TYPES_TO_SHOW_NAMES = array(
		19 => 'Zápas',
		22 => 'Vítěz zápasu',
		21 => 'Vítěz ligy',
		29 => 'Konečné umístění',
		30 => 'Ano-Ne',
		31 => 'Celkový vítěz',
		32 => 'Vítěz',
		165 => 'Zápas v zákl.hr.d.'
	);

	public function init() {
		parent::init();

		$this->jsIncludes->commonAjax	= true;
		$this->ws = Zend_Registry::get('ws');
		$this->db = Zend_Registry::get('db');

/*
		$this->teamId = $this->getRequest()->getPost('teamId');
		if (!empty($this->teamId))
			$this->view->teamId = $this->teamId;
*/


/*
		$this->view->insertSectionId	= $this->insertSectionId;
		$this->view->updateSectionId	= $this->updateSectionId;
*/
		Zend_Layout::getMvcInstance()->setLayout('catalog');

		//combinations

	}

	public function teamAction() {

		$this->view->indexSectionId	= $this->indexSectionIdTeam;

		$inData			= $this->getRequest()->getPost();
		$extensions		= array();

		//prepare wsForm data from $_POST
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['paginator']))
			$this->paginatorData = $inData['paginator'];
		if(!empty($inData['order']))
			$this->orderDataTeam = array_keys($inData['order']);

		$sports = It6_ArrayWrapper::toNativeArray($this->ws->Sport->getAll());
		$sports = Models_Utils::getCollection($sports,'sportId','name');
		$this->view->sportsCollection = $sports;

		$filter = new It6_WsForm_Filter(array(
			array('Team Id', 'teamId', 'text', array(array('teamId', '=')), 'Zend_Validate_Int'),
			array('Betradar Id', 'betradarId', 'text', array(array('betradarId', '=')), 'Zend_Validate_Int'),
			array('Sport', 'sportId', 'select', array(array('sportId', '=')), 'Zend_Validate_Int', NULL, $sports),
			//array('Sportme', 'sportName', array(array('text', 'sportName')), 'LIKE'),
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
			array('Sport Id', 'sportId'),
			array('Sport Name', 'sportName'),
			array('Name', 'name'),
			array('Short Name', 'shortName'),
		));
		$table->getColumnsExtension($extensions);
		$table->getOrderExtension($this->orderDataTeam, $extensions);

		//get user list by WS
		$teams = $this->ws->ext($extensions)->Team->getAll();
		$teams = It6_ArrayWrapper::toNativeArray($teams);

		$this->view->opener = $this->_request->getParam('opener');
		$this->view->opener2 = $this->_request->getParam('opener2');

		$this->view->filter		= $filter->getLayout(null, $this->filterData);
		$this->view->paginator	= $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
		$this->view->tHead		= $table->getTheadLayout(null,$this->orderDataTeam);
		$this->view->tBody		= $table->getTbodyLayout(self::$TEAM_TBODY_LAYOUT, $teams);
	}

	public function combinationAction() {

		$this->view->typesTranslation = static::$BET_TYPES_TO_SHOW_NAMES;

		$this->view->indexSectionId	= $this->indexSectionIdComb;

		$this->view->opener = $this->_request->getParam('opener');

		$this->view->event_id = $this->_request->getParam('event_id');
		
		
		$this->view->typesToShow = static::$BET_TYPES_TO_SHOW;
		
		$inData	= $this->getRequest()->getPost();
		$extensions = array();

		//prepare wsForm data from $_POST
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['paginator']))
			$this->paginatorData = $inData['paginator'];
		if(!empty($inData['order']))
			$this->orderDataCombination = array_keys($inData['order']);
		else $this->orderDataCombination = array('validToTime_ASC');

		$filter = new It6_WsForm_Filter(array(
			array('Bet Id', 'betId', 'text',  array(array('betId', '=')), 'Zend_Validate_Int'),
			array('Bet', 'name', 'text', array(array('name', 'LIKE'))),
			array('Valid from', 'validFromTime', 'dateTime', array(array('validFromTime', '<')), 'It6_Validate_Date'),
			array('Valid To', 'validToTime', 'dateTime', array(array('validToTime', '>')), 'It6_Validate_Date'),
			array('Alias', 'alias', 'text', array(array('alias', '=')), 'Zend_Validate_Int'),
			array('Risk limit', 'riskLimit', 'text', array(array('riskLimit', '=')), 'Zend_Validate_Int'),
			array('Typ Id', 'typeId', 'text', array(array('typeId', '=')), 'Zend_Validate_Int'),
			array('Event Id', 'eventId', 'text', array(array('eventId', '=')), 'Zend_Validate_Int'),

		));
		$filter->getExtension($this->filterData, $extensions);

		//create pagination
		$paginator = new It6_WsForm_Paginator(10000);
		$paginator->getExtension($this->paginatorData, $extensions);

		//create table
		$table = new It6_WsForm_Table(array(
			array(),
			array('Bet Id', 'betId'),
			array('Alias', 'alias'),
			array('Bet', 'name'),
/*
			array('Vaid from', 'validFromTime'),
*/
			array('Valid to', 'validToTime', 'DESC'),
			//array('Sport', 'name'),
			array('Type Id', 'typeId'),
/*
			array('Event Id', 'eventId'),
			array('Status', 'status'),
*/
/*
			array('parentId', 'parentId'),
*/
		));
		$table->getColumnsExtension($extensions);
		$table->getOrderExtension($this->orderDataCombination, $extensions);

		//get bet list by WS
		$betFilter = array(
			'validToTime > ?'=> It6_Date::dbNow(),
			'status IN (?)' => static::$BET_STATUS_TO_SHOW,
		);
		if ($this->view->event_id != NULL)
			$betFilter['eventId = ?'] = $this->view->event_id;
		$combs = $this->ws->ext($extensions)->Bet->getAllWhere($betFilter);
		
		$combs = It6_ArrayWrapper::toNativeArray($combs);

		$this->view->opt_par = count($combs);
				
		if(isset($combs['__extensions'])) {
			$extensionsRetData = $combs['__extensions'];
			unset($combs['__extensions']);
		}

		$this->view->filter		= '';//$filter->getLayout(null, $this->filterData);
		$this->view->paginator	= '';//$paginator->getLayout(null, null, $extensions['paginator']->getResponse());
		$this->view->tHead		= $table->getTheadLayout(null,$this->orderDataCombination);
		$this->view->tBody		= $table->getTbodyLayout(self::$COMB_TBODY_LAYOUT, $combs);
	}


	public function betAction() {
		$this->view->typesTranslation = static::$BET_TYPES_TO_SHOW_NAMES;

		$this->view->indexSectionId	= $this->indexSectionIdComb;
		$this->view->opener = $this->_request->getParam('opener');
		$this->view->event_id = $this->_request->getParam('event_id');
		$multiple = (intval($this->_request->getParam('opt_par')) > 1);
		$this->view->multiple = $multiple;
		$this->view->typesToShow = static::$BET_TYPES_TO_SHOW; //TODO: use all non-group types or some other static list?
		$inData	= $this->getRequest()->getPost();
		$extensions = array();

		//prepare wsForm data from $_POST
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['paginator']))
			$this->paginatorData = $inData['paginator'];
		if(!empty($inData['order']))
			$this->orderDataCombination = array_keys($inData['order']);
		else $this->orderDataCombination = array('validToTime_ASC');

		$filter = new It6_WsForm_Filter(array(
			array('Bet Id', 'betId', 'text',  array(array('betId', '=')), 'Zend_Validate_Int'),
			array('Bet', 'name', 'text', array(array('name', 'LIKE'))),
			array('Valid from', 'validFromTime', 'dateTime', array(array('validFromTime', '<')), 'It6_Validate_Date'),
			array('Valid To', 'validToTime', 'dateTime', array(array('validToTime', '>')), 'It6_Validate_Date'),
			array('Alias', 'alias', 'text', array(array('alias', '=')), 'Zend_Validate_Int'),
			array('Risk limit', 'riskLimit', 'text', array(array('riskLimit', '=')), 'Zend_Validate_Int'),
			array('Typ Id', 'typeId', 'text', array(array('typeId', '=')), 'Zend_Validate_Int'),
			array('Event Id', 'eventId', 'text', array(array('eventId', '=')), 'Zend_Validate_Int'),

		));
		$filter->getExtension($this->filterData, $extensions);

		//create pagination
		$paginator = new It6_WsForm_Paginator(10000);
		$paginator->getExtension($this->paginatorData, $extensions);

		//create table
		$table = new It6_WsForm_Table(array(
			array(),
			array('Bet Id', 'betId'),
			array('Alias', 'alias'),
			array('Bet', 'name'),
/*
			array('Vaid from', 'validFromTime'),
*/
			array('Valid to', 'validToTime', 'DESC'),
			//array('Sport', 'name'),
			//array('Type Id', 'typeId'),
			array('Bet type', 'typeName'),
/*
			array('Event Id', 'eventId'),
			array('Status', 'status'),
*/
/*
			array('parentId', 'parentId'),
*/
		));
		$table->getColumnsExtension($extensions);
		$table->getOrderExtension($this->orderDataCombination, $extensions);

		//get bet list by WS
		$betFilter = array(
			'validToTime > ?'=> It6_Date::dbNow(),
			'status IN (?)' => static::$BET_STATUS_TO_SHOW,
			'typeId IN (?)' => static::$BET_TYPES_TO_SHOW,
		);
		if ($this->view->event_id != NULL)
			$betFilter['eventId = ?'] = $this->view->event_id;
				$combs = $this->ws->ext($extensions)->Bet->getAllWhere($betFilter);
		
		$combs = It6_ArrayWrapper::toNativeArray($combs);

		$this->view->opt_par = $multiple;

		if(isset($combs['__extensions'])) {
			$extensionsRetData = $combs['__extensions'];
			unset($combs['__extensions']);
		}

		$this->view->filter		= '';//$filter->getLayout(null, $this->filterData);
		$this->view->paginator	= '';//$paginator->getLayout(null, null, $extensions['paginator']->getResponse());
		$this->view->tHead		= $table->getTheadLayout(null,$this->orderDataCombination);
		$this->view->tBody		= $table->getTbodyLayout(self::$BET_TBODY_LAYOUT, $combs);
	}

}
