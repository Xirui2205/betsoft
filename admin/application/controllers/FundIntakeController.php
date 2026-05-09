<?php

class FundIntakeController extends It6_Controller_Abstract {


	public function init() {
		parent::init();

		$this->ws = Zend_Registry::get('ws');
		$this->registerJsInclude('fundTakeinsAjax');
	}



	public function indexAction() {
		$inData				= $this->getRequest()->getParams();
		$allOddBetTypes		= array();
		$allBetTypes		= array();
		$ordBets			= array();


		if(empty($inData['filter']['event-ids']) || in_array('0', $inData['filter']['event-ids']))
			$selectedEventIds = array();
		else
			$selectedEventIds = $inData['filter']['event-ids'];

		if(empty($inData['filter']['type-ids']) || $inData['filter']['type-ids'] == 'null')
			$selectedTypeIds = array();
		else
			$selectedTypeIds = $inData['filter']['type-ids'];
		
		if(empty($inData['filter']['odd-type-ids']) || $inData['filter']['odd-type-ids'] == 'null')
			$selectedOddTypeIds = array();
		else
			$selectedOddTypeIds = $inData['filter']['odd-type-ids'];

		if(!empty($inData['filter']['sport-id']))
			$selectedSportId = $inData['filter']['sport-id'];
		else
			$selectedSportId = array();


		if(!empty($inData['submit']) || !empty($inData['ajax'])) {
			$extensions = array();
			$filterDef = array(array('?'=> array('status' => array(1,3)), 'OP' => 'NOT IN (?)'));
			if(!empty($selectedTypeIds))
				$filterDef[] = array('?'=> array('typeId' => $inData['filter']['type-ids']), 'OP' => 'IN (?)');
			if(!empty($selectedOddTypeIds))
				$filterDef[] = array('?'=> array('oddsTypeId' => $selectedOddTypeIds), 'OP' => 'IN (?)');
			if(!empty($selectedSportId))
				$filterDef[] = array('?'=> array('sportId' => $selectedSportId), 'OP' => '=');
			if(!empty($selectedEventIds))
				$filterDef[] = array('?'=> array('eventId' => $inData['filter']['event-ids']), 'OP' => 'IN (?)');
			if(!empty($inData['filter']['bet-alias']))
				$filterDef[] = array('?'=> array('alias' => $inData['filter']['bet-alias']), 'OP' => '=');
			if(!empty($inData['filter']['bet-id']))
				$filterDef[] = array('?'=> array('betId' => $inData['filter']['bet-id']), 'OP' => '=');

			$extensions[] = new It6_WsExtension_Client_Filter('def-filter', $filterDef);
			$extensions[] = new It6_WsExtension_Client_Columns('columns', array(
				'betId', 'name', 'typeId', 'typeName', 'oddsTypeId', 'alias', 'sportName', 'regionName', 'eventName'
			));

			$betsRaw = $this->ws->ext($extensions)->Bet->getAll();
			$betsRaw = It6_ArrayWrapper::toNativeArray($betsRaw);


			if(!empty($betsRaw)) {
				$tableHeadArr		= array();
				$oddTypeColCount	= array();
				$typeColCount		= array();
				
				
				$betIds = array();
				foreach($betsRaw as $bet) {
					$betIds[] = $bet['betId'];
					$bets[$bet['betId']] = $bet;
				}
				$betCols = $this->ws->Bet->getBetColumnData($betIds, null, true, $inData['order']['col-type']);
				$betCols = It6_ArrayWrapper::toNativeArray($betCols);

				$oddTypeIds = array();
				foreach($bets as $bet) {
					$oddTypeIds[] = $bet['oddsTypeId'];
				}
				$oddTypes = It6_Models_Bet::getSubtypesAndColumns($oddTypeIds);

				foreach($betCols as $key => $betCol) {
					$betId = $betCol['betId'];
					foreach($oddTypes[$bets[$betId]['oddsTypeId']]['columns'] as $colId => $col) {
						$colName			= $oddTypes[$bets[$betId]['oddsTypeId']]['columns'][$colId];
						$oddTypeName		= $oddTypes[$bets[$betId]['oddsTypeId']]['interni_nazev'];
						$oddTypeId			= $bets[$betId]['oddsTypeId'];
						$typeName			= $bets[$betId]['typeName'];
						$typeId				= $bets[$betId]['typeId'];

						$ordBets[$key]['sportName']		= $bets[$betId]['sportName'];
						$ordBets[$key]['regionName']	= $bets[$betId]['regionName'];
						$ordBets[$key]['eventName']		= $bets[$betId]['eventName'];
						$ordBets[$key]['alias']			= $bets[$betId]['alias'];
						$ordBets[$key]['betId']			= $betId;
						$ordBets[$key]['typeId']		= $typeId;
						$ordBets[$key]['oddTypeId']		= $oddTypeId;
						$ordBets[$key]['name']			= $bets[$betId]['name'];
						if(!empty($betCol['cols'][$colId]))
	 						$ordBets[$key]['cols'][$colId] = $betCol['cols'][$colId];
	 					else {
	 						$ordBets[$key]['cols'][$colId]['weightedStake']	= '0.00';
	 						$ordBets[$key]['cols'][$colId]['absoluteStake']	= '0.00';
	 						$ordBets[$key]['cols'][$colId]['betCount']		= '0';
	 					}

						$tableHeadArr[$typeId]['name'] = $typeName;
						$tableHeadArr[$typeId]['oddTypes'][$oddTypeId]['name'] = $oddTypeName;
						$tableHeadArr[$typeId]['oddTypes'][$oddTypeId]['cols'][$colId] = $colName;

						$allBetTypes[$typeId] = $typeId;

						$typeColCount[$typeId][$oddTypeId.'-'.$colName] = $colId;
						$oddTypeColCount[$typeId][$oddTypeId][$colName] = $colId;
					}
					
					ksort($tableHeadArr[$typeId]['oddTypes']);
					ksort($tableHeadArr);
					ksort($oddTypeColCount[$typeId]);
					ksort($typeColCount[$typeId]);
				}

				$this->view->tableHeadArr	= $tableHeadArr;
				$this->view->oddTypeColCount= $oddTypeColCount;
				$this->view->typeColCount	= $typeColCount;
			}
			
			switch ($inData['order']['col-type']) {
				case 'risk_limit_balance':
					$colType = 'weightedStake';
					break;
				case 'absolute_stake':
					$colType = 'absoluteStake';
					break;
				case 'bet_count':
					$colType = 'betCount';
					break;
			}
		}
		
		foreach($ordBets as $i => $bet) {
			$topColVal = 0;
			$ordBets[$i]['topColType'] = $colType;
			foreach($bet['cols'] as $colId => $col) {
				if((float)$col[$colType] > $topColVal) {
					$ordBets[$i]['topColId'] = $colId;
					$topColVal = $col[$colType];
				}
			}
		}

		$this->view->filtOrdData = $inData;
		$this->view->bets = $ordBets;
		$this->view->selectedTypeIds = $selectedTypeIds;
		$this->view->selectedOddTypeIds = $selectedOddTypeIds;
		
		$betTypesRaw = $this->ws->BetType->getActive($selectedEventIds);
		$betTypesRaw = It6_ArrayWrapper::toNativeArray($betTypesRaw);
		$betTypes = array();
		foreach($betTypesRaw as $type) {
			$betTypes[$type['betTypeId']] = $type; 
		}
		$this->view->types = $betTypes;

		$sports = $this->ws->Event->getSportFilterEvents($selectedSportId, DEFAULT_LANG_ID, false);
		$filterHtml = It6_SportEventsFilter::getSportFilterHtml($sports, $selectedSportId, $selectedEventIds, false, true);
		$this->view->eventFilter= $filterHtml;
		
		$this->view->oddTypes = It6_Models_Bet::getActiveOddTypes($selectedTypeIds, $selectedEventIds);

		if(!empty($inData['ajax'])) {
			$this->_helper->layout->setLayout('empty');
			$this->render('data-table');
		}
	}

	
	public function getActiveOddTypesAction() {
		$this->_helper->layout->setLayout('empty');
		$this->_helper->viewRenderer->setNoRender(true);
		
		$typeIds = $this->getRequest()->getPost('typeIds');
		$eventIds = $this->getRequest()->getPost('eventIds');

		if(empty($eventIds) || in_array('0', $eventIds))
			$eventIds = null;

		$oddTypes = It6_Models_Bet::getActiveOddTypes($typeIds, $eventIds);
		echo json_encode($oddTypes);
	}
	
	
	public function getActiveTypesAction() {
		$this->_helper->layout->setLayout('empty');
		$this->_helper->viewRenderer->setNoRender(true);

		$eventIds = $this->getRequest()->getPost('eventIds');
		$sportId  = $this->getRequest()->getPost('sportId');
		
		if(empty($eventIds) || in_array('0', $eventIds))
			$eventIds = null;
		if(empty($sportId))
			$sportId = null;

		$types = $this->ws->BetType->getActive($eventIds, $sportId);
		$types = It6_ArrayWrapper::toNativeArray($types);
		echo json_encode($types);
	}
}
