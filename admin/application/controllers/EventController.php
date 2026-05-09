<?php

class EventController extends It6_Controller_Abstract {

	const SEO_URL_TYPE	= 3;

	protected $updateSectionId		= 289;
	protected $viewSectionId		= 288;
	protected $indexSectionId		= 290;
	protected $insertSectionId		= 291;
	protected $updatePositionsSectionId = 305;
	protected $updateTypeEventSectionId	= 330;

	protected $eventId;
	protected $ws;

	private $filterData		= array();
	private $paginatorData	= array('recsPerPage' => 30);
	private $orderData		= array('eventId ASC');

	private static $TBODY_LAYOUT = 'event-tbody';



	public function init() {
		parent::init();
		$this->ws = Zend_Registry::get('ws');

		if(!isset($this->eventId))
			$this->eventId = $this->getRequest()->getParam('eventId');

		$this->jsIncludes->eventAjax		= true;
		$this->jsIncludes->commonAjax		= true;

		$this->view->viewSectionId		= $this->viewSectionId;
		$this->view->updateSectionId	= $this->updateSectionId;
		$this->view->indexSectionId		= $this->indexSectionId;
		$this->view->updateTypeEventSectionId	= $this->updateTypeEventSectionId;

		$this->view->eventId			= $this->eventId;
	}

	/**
	 * @param string $list Semicolon separated ID values
	 * @return array List of IDs as array
	 */
	private function parseBetradarIds($list) {
		$ids = array();
		foreach (explode(';', $list) as $id) {
			$id = trim($id);
			if (!empty($id))
				$ids[] = $id;
		}
		return $ids;
	}

	public function indexAction() {
		$inData			= $this->getRequest()->getParams();
		$extensions		= array();


		if(isset($inData['delete']) || (isset($inData['delete_selected']) && isset($inData['select']))) {
			$events = isset($inData['delete_selected']) ? $inData['select'] : array_keys($inData['delete']);
			$list = '[' . implode(',',$events) . ']';

			if ($this->ws->Event->cancelValidity($events)) {
				$this->view->feedbackMsg = UiUtil::printMessages( i18n::tr("event_delete_ok") . ' ' . $list);
				It6_Log::info(
					"Events %events% were successfully deleted.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('events' => $list)
				);
			}
			else {
				$this->view->feedbackMsg = UiUtil::printErrors( i18n::tr("event_delete_error") . ' ' . $list);
				It6_Log::warn(
					"Error while deleting events %events%.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('events' => $list)
				);
			}
		}
		if(isset($inData['show']) || (isset($inData['show_selected']) && isset($inData['select']))) {
			$events = isset($inData['show_selected']) ?  $inData['select'] : array_keys($inData['show']);
			$list = '[' . implode(',',$events) . ']';
				
			if ($this->ws->Event->show($events)) {
				$this->view->feedbackMsg = UiUtil::printMessages(i18n::tr('event_show_ok') . ' ' . $list);
				It6_Log::info(
					"Events '%events%' was set to be visible.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('events' => $list)
				);
			}
			else {
				$this->view->feedbackMsg = UiUtil::printErrors(i18n::tr( 'event_show_error') . ' ' . $list);
				It6_Log::warn(
					"Error setting events '%events%' to visible.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('events' => $list)
				);
			}
		}
		if(isset($inData['hide']) || (isset($inData['hide_selected']) && isset($inData['select']))) {
			$events = isset($inData['hide_selected']) ? $inData['select'] : array_keys($inData['hide']);
			$list = '[' . implode(',',$events) . ']';
				
			if ($this->ws->Event->hide($events)) {
				$this->view->feedbackMsg = UiUtil::printMessages(i18n::tr('event_hide_ok') . ' ' . $list);
				It6_Log::info(
					"Events '%events%' was set to be hidden.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('events' => $list)
				);
			}
			else {
				$this->view->feedbackMsg = UiUtil::printErrors(i18n::tr( 'event_hide_error') . ' ' . $list);
				It6_Log::warn(
					"Error setting events '%events%' to be hidden.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('events' => $list)
				);
			}
		}

		if(isset($inData['changeOrder'])) {
			$event = array_keys($inData['changeOrder']);
			$eventId = reset($event);

			$data = array(
				'navigationOrder'			=> $inData['navigationOrder'][$eventId],
				'navigationOrderOffergen'	=> $inData['navigationOrderOffergen'][$eventId],
				'eventId'					=> $eventId,
			);

			$this->ws->Event->update($data, true);
		}




		//prepare wsForm data from $_POST
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['paginator']))
			$this->paginatorData = $inData['paginator'];
		if(!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);


		//create filter
		$sportOptions = It6_ArrayWrapper::toNativeArray($this->ws->Sport->getAllOptions(DEFAULT_LANG_ID));
		$regionOptions = It6_ArrayWrapper::toNativeArray($this->ws->Region->getAllOptions(DEFAULT_LANG_ID));
		
		//asort($sportOptions);
		//asort($regionOptions);
		$sportOptions = array('0'=>I18n::tr('Any'))+$sportOptions;
		$regionOptions = array('0'=>I18n::tr('Any'))+$regionOptions;

		$betradarIds = (empty($this->filterData['betradarId']) ? '' : trim($this->filterData['betradarId']));
		$betradarIds = (empty($betradarIds) ? array() : $this->parseBetradarIds($betradarIds));

		$filter = new It6_WsForm_Filter(array(
			array('Sport', 'sport', 'select', array(array('sportId', '=')), null, null, $sportOptions),
			array('Region', 'region', 'select', array(array('regionId', '=')), null, null, $regionOptions),
			array('Keyword', 'keyword', 'text', array(array('name', 'LIKE', '%?%'), array('key', 'LIKE', '%?%')), null, null, null, array('OP', 'OR')),
			array('betradar_event_id', 'betradarId', 'text', array())
		));
		$filter->getExtension($this->filterData, $extensions);


		//create pagination
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
		$paginator->getExtension($this->paginatorData, $extensions);


		//create table
		$table = new It6_WsForm_Table(array(
			array('<input type="checkbox" onClick="CheckAll(this, \'.row-selector\')" />', null),
			array(null, null),
			array(null, null),
			array(null, null),
			array('Region', 'regionName'),
			array('name', 'name'),
			array('betradar_event_id', 'betradarId'),
			array('bet_alias_from', 'betAliasFrom'),
			array('bet_alias_to', 'betAliasTo'),
			array('navigation_visible', 'navigationVisible'),
			array('valid_from', 'validFromTime'),
			array('valid_to', 'validToTime'),
			array('betradar_time_offset', 'betradarTimeOffset'),
			array('sport', 'sportName'),
			array('position', 'navigationOrder'),
			array('position_in_print', 'navigationOrderOffergen'),
			array(null, null),
			array('event_marked', 'navigationHighlight'),
			array('event_separate', 'navigationDelimiter'),
		));
		$table->getColumnsExtension($extensions, array('isForbiden'));
		$table->getOrderExtension($this->orderData, $extensions);


		//get event list by WS
		if (!empty($betradarIds))
			$events = $this->ws->ext($extensions)->Event->getAllWhereAndByBetradarId($betradarIds, array());
		else
			$events = $this->ws->ext($extensions)->Event->getAll();
		$events = It6_ArrayWrapper::toNativeArray($events);
		foreach( $events as &$event ) {
			$event['betradarId'] = implode(';',$event['betradarId']);
			unset($event);
		}


		$this->view->shoda		= new UserTrack();
		$this->view->filter		= $filter->getLayout(null, $this->filterData);
		$this->view->paginator	= $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
		$this->view->tHead		= $table->getTheadLayout(null,$this->orderData);
		$this->view->tBody		= $table->getTbodyLayout(self::$TBODY_LAYOUT, $events);
	}



	public function viewAction() {
		$this->_helper->layout->setLayout('empty');

		$event 					= $this->ws->Event->getById( (integer)$this->eventId );
		$event					= It6_ArrayWrapper::toNativeArray($event);
		$event['betradarId']	= implode(';',$event['betradarId']);
		$event['ticketGames']   = $this->ws->Campaign->getEventTicketGameDependency($this->eventId, true, DEFAULT_LANG_ID);
		$langs					= $this->ws->Language->getAllActive();
		$seoUrl					= $this->ws->Event->getSeoUrl($event['eventId']);
		
		foreach ( $seoUrl as $lang => $url )
			$event['seoUrl'.$lang] = $url;

		$this->view->langs	= $langs;
		$this->view->event	= $event;
	}



	public function updateAction() {
		$this->_helper->layout->setLayout('empty');

		$event					= $this->ws->Event->getById( (integer)$this->eventId );
		$eventMainForm			= new Models_Form_EventMain($this->updateSectionId, $this->eventId, false, self::SEO_URL_TYPE);
		$event['validFromTime']	= It6_Date::fromDb($event['validFromTime']);
		$event['validToTime']	= It6_Date::fromDb($event['validToTime']);
		$event['betradarId']	= implode(';',It6_ArrayWrapper::toNativeArray($event['betradarId']));
		$oldTicketGames   = $this->ws->Campaign->getEventTicketGameDependency($this->eventId, true, DEFAULT_LANG_ID);
		$oldTicketGames   = array_map(
			function($i) { return $i['id'];	},
			It6_ArrayWrapper::toNativeArray($oldTicketGames)
		);
		$event['ticketGames']   = $oldTicketGames;
		$seoUrl 				= $this->ws->Event->getSeoUrl($event['eventId']);
		$postData				= $this->getRequest()->getPost();

		foreach ( $seoUrl as $lang => $url )
			$event['seoUrl'.$lang] = $url;

		if (isset($postData['submit'])) {
			$langs = $this->ws->Language->getAllActive();
			foreach ( $langs as $lang ) {
				$eventMainForm
					->getElement('seoUrl'.$lang['languageId'])
					->addValidator(new It6_Validate_EventSeoUrl($postData['eventId'], $postData['regionId'], $postData['sportId']));
			}
			
			if (!$eventMainForm->isValid($postData))
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
			else {
				$values = $eventMainForm->getValues();
				unset($values['save']);
				$values['eventId'] = intval(trim($this->getRequest()->getPost('eventId')));
				$values['validFromTime'] = It6_Date::toDb($values['validFromTime']);
				$values['validToTime'] = It6_Date::toDb($values['validToTime']);
				$values['betradarId'] = $this->parseBetradarIds($values['betradarId']);
				$ticketGames = array();
				if (!empty($values['ticketGames']) && is_array($values['ticketGames'])) {
					foreach ($values['ticketGames'] as $gameId) { 
						if ( $gameId = intval(trim($gameId)) ) {
							$ticketGames[] = $gameId;
						}
					}
				}
				unset($values['ticketGames']);

				$err = empty($values['eventId']);
				if (!$err && !empty($values['betradarId'])) {
					$duplicities = array();
					foreach ($this->ws->Event->getBetradarIdEventCount($values['betradarId'], $values['eventId']) as $brId => $count) {
						if (0 < $count)
							$duplicities[] = $brId;
					}
					if (!empty($duplicities))
						$err = array(I18n::tr('error_duplicate_event_br_id'), '[' . implode(',', $duplicities) . ']');
				}

				if (!$err) {
					sort($ticketGames);
					sort($oldTicketGames);
					if ($oldTicketGames != $ticketGames) {
						$res = $this->ws->Campaign->setEventTicketGameDependency(
							array($this->eventId => $ticketGames),
							true
						);
						if ($res) {
							It6_Log::info(
								'Updated event-ticket_game associations',
								It6_Log::TAG_ADMIN_OPERATION,
								array(
									'eventId' => $this->eventId,
									'oldGames' => $oldTicketGames,
									'newGames' => $ticketGames,
								)
							);
							//TODO: trigger recomputing of game results?
							$invalidation = array();
							foreach (array_merge($ticketGames, $oldTicketGames) as $gameId) {
								$invalidation[$gameId] = null; // all tickets
							}
							It6_GlobalCache_Invalidator::Campaign_ticketGame($invalidation);
						}
						else {
							$err = array(I18n::tr('error_event_ticket_game_update'));
						}
					}
				}

				if (!$err) {
					if ($this->eventId = $this->ws->Event->update($values)) {
						$this->view->feedbackMsg = UiUtil::printMessages( array('update-ok') );
						It6_Log::info(
							"Event '%eventId%' was updated.",
							It6_Log::TAG_ADMIN_OPERATION,
							array(
								'eventId' => $this->eventId,
								'oldData' => Zend_Json::encode($event),
								'newData'=>  Zend_Json::encode($values),
							)
						);
	
						$this->_forward('view', null, null, array('eventId' => $this->eventId));
					}
					else
						$err = array('update-error');
				}
				if ($err) {
					$this->view->feedbackMsg = UiUtil::printErrors($err);
					It6_Log::err(
						"Event update error.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('data'=>  Zend_Json::encode($values))
					);
				}
				$values['ticketGames'] = $ticketGames;
			}
		}
		else
			$eventMainForm->populate(It6_ArrayWrapper::toNativeArray($event));

		$this->view->eventMainForm = $eventMainForm;
	}



	public function insertAction() {
		$this->_helper->layout->setLayout('empty');
		$eventMainForm = new Models_Form_EventMain($this->insertSectionId, 0, true, self::SEO_URL_TYPE);
		
		if ($this->getRequest()->getPost('submit')) {
			if (!$eventMainForm->isValid( $this->getRequest()->getPost() ))
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
			else {
				$values = $eventMainForm->getValues();
				unset($values['save']);
				$values['validFromTime'] = It6_Date::toDb($values['validFromTime']);
				$values['validToTime'] = It6_Date::toDb($values['validToTime']);
				$values['betradarId'] = $this->parseBetradarIds($values['betradarId']);

				$err = false;
				if (!empty($values['betradarId'])) {
					$duplicities = array();
					foreach ($this->ws->Event->getBetradarIdEventCount($values['betradarId'], null) as $brId => $count) {
						if (0 < $count)
							$duplicities[] = $brId;
					}
					if (!empty($duplicities)) {
						$this->view->feedbackMsg = UiUtil::printErrors(array(I18n::tr('error_duplicate_event_br_id'), '[' . implode(',', $duplicities) . ']'));
						$err = true;
					}
				}
				if (!$err) {
					try {
						if ($this->eventId = $this->ws->Event->insert($values)) {
							$this->view->feedbackMsg = UiUtil::printMessages( array('insert-ok') );
							It6_Log::info(
								"Event '%eventId%' was inserted.",
								It6_Log::TAG_ADMIN_OPERATION,
								array('eventId' => $this->eventId, 'newData'=>  Zend_Json::encode($values))
							);
		
							$this->_forward('view', null, null, array('eventId' => $this->eventId));
						}
						else
							throw new Exception('');
					}
					catch ( Exception $e ) {
						$this->view->feedbackMsg = UiUtil::printErrors( array('insert-error: ' . $e->getMessage()) );
						It6_Log::notice(
							"Event insert error.",
							It6_Log::TAG_ADMIN_OPERATION,
							array('data'=>  Zend_Json::encode($values), 'message' => $e->getMessage())
						);
					}
				}
			}
		}
		
		$this->view->eventMainForm = $eventMainForm;
	}

	/**
	 * Intented for AJAX calls.
	 * Output is JSON encoded string with following structure: { 'infos': [string], 'errors': [string] } 
	 */
	public function updatePositionsAction() {
		$this->_helper->layout->disableLayout();
		$events = $this->getRequest()->getPost('events');
		$result = array('errors' => array(), 'infos' => array());
		if (empty($events) || !is_array($events)) {
			$result['errors'][] = i18n::tr('invalid_param');
		}
		else {
			foreach ($events as $event) {
				$sportId = ( empty($event['sportId']) ? 0 : intval(trim($event['sportId'])) );
				$regionId = ( empty($event['regionId']) ? 0 : intval(trim($event['regionId'])) );
				$eventId = ( empty($event['eventId']) ? 0 : intval(trim($event['eventId'])) );
				$position = ( empty($event['position']) ? 0 : intval(trim($event['position'])) );
				$positionOffergen = ( empty($event['positionOffergen']) ? 0 : intval(trim($event['positionOffergen'])) );
				if (!$sportId || !$regionId || !$eventId || !$position || !$positionOffergen) {
					$result['errors'][] = i18n::tr('invalid_param') . " (#$eventId)";
					continue;
				}
				try {
					$event = array(
						'sportId' => $sportId,
						'regionId' => $regionId,
						'eventId' => $eventId,
						'navigationOrder' => $position,
						'navigationOrderOffergen' => $positionOffergen,
					);
					Zend_Registry::get('ws')->Event->update($event, true);
					$result['infos'][] = i18n::tr('position_updated') . " (#$eventId)";
				}
				catch (Exception $e) {
					$result['errors'][] = i18n::tr('error') . " (#$eventId) " . $e->getMessage();
				}
			}
		}
		$this->view->result = Zend_Json::encode($result);
	}
	
	
	public function eventSelectorPopupAction() {
		$this->jsIncludes->popup = true;
		$this->_helper->layout->setLayout('no-menu');
		$langId			= 1; //TODO: load from config or something?s
		$sportId		= $this->getRequest()->getParam('sportId');
		$eventValueCol	= $this->getRequest()->getParam('eventValueCol');
		$sports			= Zend_Registry::get('ws')->Event->getSportFilterEvents($sportId, $langId, $eventValueCol);
		$filterHtml		= It6_SportEventsFilter::getSportFilterHtml($sports, $sportId, null, true, true, $eventValueCol);
	
		$this->view->filter			= $filterHtml;
		$this->view->eventValueCol	= $eventValueCol;
		$this->view->eventId		= $this->getRequest()->getParam('eventId');
		$this->view->submitButton	= $this->getRequest()->getParam('submitButton');
	}


	public function updateTypeEventAction() {
		$this->_helper->layout->setLayout('empty');
		
		$typeEventForm = new Models_Form_TypeEvent($this->updateTypeEventSectionId, $this->eventId);
		$isSubmit = $this->getRequest()->getPost('submit');
		if(!empty($isSubmit)) {
			$inData = $this->getRequest()->getPost();
		}

		if(!empty($inData)) {
			if($typeEventForm->isValid($inData)) {
				$values = $typeEventForm->getValues();
				if($inData['submit'] == 'sport') {
					$applyToSport = true;
				}
				else {
					$applyToSport = false;
				}
				try {
					if ( $this->ws->Event->updateTypeEvent($values, $applyToSport)) {
						$this->view->feedbackMsg = UiUtil::printMessages( array('update-ok') );
						It6_Log::info(
							"Type-event was updated.",
							It6_Log::TAG_ADMIN_OPERATION,
							array('data'=>  Zend_Json::encode($values))
						);
					}
					else {
						$this->view->feedbackMsg = UiUtil::printErrors( array('update-error') );
						It6_Log::err(
							"Type-event update error.",
							It6_Log::TAG_ADMIN_OPERATION,
							array('data'=>  Zend_Json::encode($values))
						);
					}
				}
				catch( Exception $e ) {
					$this->view->feedbackMsg = UiUtil::printErrors( array('update-error: '.$e->getMessage()) );
					It6_Log::err(
						"Type-event update error.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('data'=>  Zend_Json::encode($values))
					);
				}
			}
			else {
				$this->view->feedbackMsg = UiUtil::printErrors(array('form-not-valid'));
			}
		}
		
		$typeEvents = Zend_Registry::get('ws')->Event->getTypeEventsByEventId($this->eventId);
		$typeEvents = It6_ArrayWrapper::toNativeArray($typeEvents);
		$typeEventForm->populate($typeEvents);
		$this->view->typeEventForm = $typeEventForm;
	}
}
