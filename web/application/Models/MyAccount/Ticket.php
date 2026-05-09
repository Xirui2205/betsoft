<?php

class Models_MyAccount_Ticket{

	/**
	 * strnaka
	 * @access private
	 * @var int
	 */
	private static $page = 0;

	/**
	 * posledni select
	 * @access private
	 * @var object
	 */
	private static $lastSelect;

	//public static $THEAD_LAYOUT	= 'standard-thead-noorder';
	public static $THEAD_LAYOUT	= 'tickets-thead';
	public static $TBODY_LAYOUT	= 'tickets-tbody';

	private static $filterData		= array();
	private static $paginatorData	= array('recsPerPage' => 10);
	private static $orderData		= array('createdTime DESC');

	/**
	 * Strankovani
	 * @return array
	 */

/*
	public static function pagging(){

		$row = self::$lastSelect->limit(0)->query()->fetchAll();

		return Models_Helpers_Pagging::page(count($row),PAGE_CALENDAR,self::$page);

	}
*/

	/**
	 * Zobrazeni detailu tiketu pouze ticket id
	 * @param int $ticket_id  id tiketu
	 * @return void
	 */
	public static function detailTicket($handle){
		global $systemAr;
		$xH = array();

		try {
			$row = Zend_Registry::get('db')->select()
				->from(
					array('l'=>'ticket'),
					array('system','ticket_id','handle','rate_real','win_real','castka','rate','win',
					'type','free_bet_bonus','castka','zruseno','vyplacen', 'is_loss', 'zalozen')
				)
				->where('l.handle=?',intval($handle))
				->query()->fetchAll();

			foreach($row as $k=>$h){
				$row[$k] = array_merge($h,self::addInfo($h));
				$row[$k]['match'] = self::match($h['ticket_id'],$xH);
				$row[$k] = array_merge($xH,$row[$k]);

				if($h['system'] != 0){
					$GLOBALS['system_special_ar'] = Array();
					$castka_rad = ($row[$k]['castka']/$systemAr[(count($row[$k]['match'])-$row[$k]['num_bank'])][$row[$k]['system']]);
					$system_total_amount =  Help::ReQSystem(0,$row[$k]['system'],0,$row[$k]['system_ar'],$row[$k]['banker_rate'],1,$castka_rad);
					$row[$k]['system_ar'] = $GLOBALS['system_special_ar'];
					$row[$k]['system_win'] = $system_total_amount;
				}
			}

			return $row;
		}
		catch ( Exception $e ) {
			Models_Exception_Handler::handle($e);
		}
	}


	/**
	 * Zobrazeni detailu tiketu
	 * @param int $ticket_id  id tiketu
	 * @return void
	 */
	public static function detail($handle){
		global $systemAr;
		//TODO: should use ws and should not use fetchAll and loop when only needing one ticket
		try{
			$db = Zend_Registry::get('db');
			$ticket_id = It6_Models_Ticket::getPrivateId($handle, $db);
			$xH = array();
			$userId = Zend_Registry::get('user_id');
			$row = $db->select()
				->from(
					array('l'=>'ticket'),
					array(
						'system','ticket_id','handle','rate_real','win_real',
						'castka','rate','win','type','free_bet_bonus','castka',
						'zruseno','vyplacen','is_loss','zalozen','point_type_id','rate_advance'
					)
				)
				->where('l.user_id=?', $userId)
				->where('l.handle=?',intval($handle))
				->query()->fetchAll();

			foreach($row as $k=>$h){
				$row[$k] = array_merge($h,self::addInfo($h));

				$row[$k]['match'] = self::match($h['ticket_id'],$xH);
				$row[$k]['match'] = It6_Models_Ticket::sortBetsByGroup($row[$k]['match'], $row[$k]['type']);
				$row[$k] = array_merge($xH,$row[$k]);

				if($h['system'] != 0) {
					$GLOBALS['system_special_ar'] = Array();
					//mapovani promennych
					//pokus o sjednoceni, ODLOZENO
					/*$hlp = array(
									'castka' => $row[$k]['castka'],
									'sazky' => $row[$k]['match'],
									'banker_num'=> $row[$k]['num_bank'],
									'system' => $row[$k]['system']
					);
					*/
					$castka_rad = ($row[$k]['castka']/$systemAr[(count($row[$k]['match'])-$row[$k]['num_bank'])][$row[$k]['system']]);
					$system_total_amount =  Help::ReQSystem(0,$row[$k]['system'],0,$row[$k]['system_ar'],$row[$k]['banker_rate'],1,$castka_rad);
					$row[$k]['system_ar'] = $GLOBALS['system_special_ar'];
					$row[$k]['system_win'] = $system_total_amount;
				}
				//if ('system' == $h['type'] || 'maxikombi' == $h['type']) {
					$row[$k]['userId'] = $userId;
					$helper = new It6_Models_Ticket($row[$k], It6_Models_Ticket::DATA_SLIP, 'user', $db);
					$helper->computeAggregates();
					$row[$k]['won'] = $helper->won;

					$helper->readBetColumnNames($db);
					$row[$k]['helper'] = $helper;
				//}
			}


			return $row;
		}

		catch ( Exception $e ) {
			Models_Exception_Handler::handle($e);
		}
	}


	/**
	 * Info o zapasech
	 * @param int $ticket_id  id tiketu
	 * @param array $row  pole info k tiketu
	 * @return void
	 */

	private static function match($ticket_id,&$xH){

		$vAr = $xH = $system_ar = array();
		$banker_rate = 1;

		try{

			$row = Zend_Registry::get('db')->select()->from(array('t'=>'ticket_pohled'), array(
				'zruseno','t.status','t.ticket_sazka_zrusena','t.proplacena',
				't.platna_do','t.sazka_id',
				't.banker','t.kurz','t.text','t.sloupec_id','t.vysledek','t.live',
				't.group_count','t.group_t','t.group_id',
				'unazev'=>'TRANSLATE(u.nazev,'. $_SESSION['lang_id'] .')','tnazev'=>'TRANSLATE(h.nazev,'. $_SESSION['lang_id'] .')',
			))
			->join(array('u'=>'udalost'),'u.udalost_id=t.udalost_id')
			->join(array('h'=>'typ'),'h.typ_id=t.typ_id')
			->where('ticket_id=?',$ticket_id)
			->query()->fetchAll();


			$xH['num_bank'] = 0;
			$xH['system_num_match'] = 0;
			$xH['group_count'] = null;
			$xH['group_t'] = null;

			foreach($row as $k=>$h){

				$k = $h['sazka_id'];
				if($h['live'] == 1){



					$row2 = Zend_Registry::get('db')->select()->from(array('l'=>'live_event'),array('mtext'=>'CONCAT(home_team," - ",away_team)'))
					->join(array('x'=>'live_sazka'),'x.event_id=l.event_id')
					->where('s.sazka_id=?',$h['sazka_id'])
					->query()->fetchAll();
					if(count($row2) > 0) $h['text'] = $row2[0]['mtext'];
				}

				if($h['banker'] == 1)$xH['num_bank']++;else $xH['system_num_match']++;



				if($h['banker'] == 1){
					$kk = ($h['zruseno'] == 1 || $h['ticket_sazka_zrusena'] == 1 || $h['status'] == 1?1:$h['kurz']);
					$banker_rate = $banker_rate * $kk;
				}
				else{
					$klic = count($system_ar);
					$system_ar[$klic]['sazka_id'] = $h['sazka_id'];
					$system_ar[$klic]['rate'] = ($h['zruseno'] == 1 || $h['ticket_sazka_zrusena'] == 1 || $h['status'] == 1?1:$h['kurz']);
				}


				$xH['banker_rate'] = $banker_rate;
				$xH['system_ar'] = $system_ar;
				if (!isset($xH['group_count']))
					$xH['group_count'] = $h['group_count'];
				if (!isset($xH['group_t']))
					$xH['group_t'] = $h['group_t'];

				$vAr[$k]['sazka_id'] = $k;
				$vAr[$k]['banker_rate'] = $banker_rate;
				$vAr[$k]['text'] = $h['text'];
				$vAr[$k]['banker'] = $h['banker'];
				$vAr[$k]['kurz'] = $h['kurz'];
				$vAr[$k]['proplacena'] = $h['proplacena'];
				$vAr[$k]['unazev'] = $h['unazev'];
				$vAr[$k]['tnazev'] = $h['tnazev'];
				$vAr[$k]['zruseno'] = ($h['zruseno'] == 1 || $h['ticket_sazka_zrusena'] == 1 || $h['status'] == 1 ? 1 : -1);
				$vAr[$k]['tip'] = '';
				$vAr[$k]['trefil'] = -1;
				$vAr[$k]['group'] = $h['group_id'];
				$vAr[$k]['sloupec_id'] = $h['sloupec_id'];

				$row3 = Zend_Registry::get('db')->select()->from(array('t'=>'podtyp_sloupce'),array('nazev'=>'TRANSLATE(t.nazev,'. $_SESSION['lang_id'] .')'))
				->where('t.sloupec_id=?',$h['sloupec_id'])
				->query()->fetchAll();

				if(count($row3) > 0)  $vAr[$k]['tip'] = $row3[0]['nazev'];

				$vAr[$k]['vysledek_orig'] = $h['vysledek'];
				$v = explode(';',$h['vysledek']);

				$vAr[$k]['vysledek'] = '';

				foreach($v as $h2){

					if($h2 == $h['sloupec_id']) $vAr[$k]['trefil'] = 1;

					$row3 = Zend_Registry::get('db')->select()
					->from(
						array('t'=>'podtyp_sloupce'),
						array('nazev'=>'TRANSLATE(t.nazev,'. $_SESSION['lang_id'] .')'))
					->where('t.sloupec_id=?',intval($h2))
					->query()->fetchAll();

					if(count($row3) > 0){
						$vAr[$k]['vysledek'] .= $row3[0]['nazev'].',';
						$vAr[$k]['vysledek'] = mb_substr($vAr[$k]['vysledek'],0,-1);
					}
					else
						$vAr[$k]['vysledek'] = '-';
				}

				$vAr[$k]['platna_do'] = $h['platna_do'];
				$vAr[$k]['text'] 			= $h['text'];
			}


			return $vAr;


		}  catch ( Exception $e ) {

			Models_Exception_Handler::handle($e);

		}

	}


	/**
	 * Zobrazeni vsech tiketu hrace
	 * @param int $page  stranka
	 * @return void
	 */
/*
	public static function show($page){
		self::$page = $page;

		try{
			$select = Zend_Registry::get('db')->select()->from(array('l'=>'ticket'),array('ticket_id','handle','rate_real','win_real','castka','rate','win','type','free_bet_bonus','castka','zruseno','vyplacen','zalozen','point_type_id','rate_advance'))
			->where('l.user_id=?',Zend_Registry::get('user_id'))
			->order('l.zalozen desc')
			->limit(PAGE_CALENDAR,self::$page*PAGE_CALENDAR);

			self::$lastSelect = $select;

			$row  = $select->query()->fetchAll();

			foreach($row as $k=>$h){
				$row[$k] = array_merge($h,self::addInfo($h));
			}

			return $row;

		}  catch ( Exception $e ) {

			Models_Exception_Handler::handle($e);
		}
	}
*/



	public static function getAllByUser($values, $view) {
		$extensions	= array();
		$states		= array();

		//get list of states selected in filter
		//TODO: should be done differently, but the vic_main.ticekt is not really set up for it
		if(!empty($values['filter']['open']))
			$states[] = 1;
		if(!empty($values['filter']['win']))
			$states[] = 2;
		if(!empty($values['filter']['loss']))
			$states[] = 3;
		if(!empty($values['filter']['canceled']))
			$states[] = 4;
		//if required ticket state hasnt been specified, then get all
		if(empty($states))
			$states = array(1,2,3,4);


		//prepare wsForm data from $_POST
		if(!empty($values['filter']))
			self::$filterData = $values['filter'];
		if(!empty($values['paginator']))
			self::$paginatorData = $values['paginator'];

		//create filter
		$filter = new It6_WsForm_Filter(array(
			array('winning_tickets', 'win', 'checkbox', array()),
			array('created_from', 'timeFrom', 'date', array(
				array(array('DATE(?)' => 'createdTime'), '>= DATE(?)')), 'It6_Validate_Date'),
			array('opened_tickets', 'open', 'checkbox', array()),
			array('created_before', 'timeTo', 'date', array(
				array(array('DATE(?)' => 'createdTime'), '<= DATE(?)')), 'It6_Validate_Date'),
			array('loss_tickets', 'loss', 'checkbox', array()),
			array('ticket_id', 'ticketHandle', 'text', array(
				array('ticketHandle', '=')
			), 'Zend_Validate_Digit', null, null, null, 'input110'),
			array('canceled_tickets', 'canceled', 'checkbox', array())
		));
		$filter->getExtension(self::$filterData, $extensions);


		//create pagination
		$paginator = new It6_WsForm_Paginator(self::$paginatorData['recsPerPage']);
		$paginator->getExtension(self::$paginatorData, $extensions);


		//create table
		$table = new It6_WsForm_Table(array(
			array('created', 'createdTime'),
			array('ticket_id', 'ticketHandle'),
			array('type', 'type'),
			array('amount', 'amount', 'right'),
			array('rate', 'totalOdds', 'right'),
			array('total_win', 'won', 'right'),
			array('status', 'state')
		));
		$table->getColumnsExtension($extensions);
		$table->getOrderExtension(array('createdTime DESC'), $extensions);

		$userId = Zend_Registry::get('user_id');
		$tickets = Zend_Registry::get('ws')->ext($extensions)->Ticket->getAllByState(
				$states, array('userId = ?' => $userId));
		$tickets = It6_ArrayWrapper::toNativeArray($tickets);
//		foreach ($tickets as &$ticket)
//			$ticket['realState'] = It6_Models_Ticket::getRealTicketState($ticket);

		$view->tickets		= $tickets;
		$view->filter		= $filter->getLayout(null, self::$filterData);
		$view->paginator	= $paginator->getLayout(null, null, $extensions['paginator']->getResponse());
		$view->thead		= $table->getTheadLayout(self::$THEAD_LAYOUT);
		$view->tbody		= $table->getTbodyLayout(self::$TBODY_LAYOUT, $tickets);
	}



	/**
	 * Zobrazeni vsech tiketu hrace
	 * stavy tiketu 1=nevyhodnoceny 2=vyhra 3=prohra 4=zruseny
	 * @param array $data  data o tiektu
	 * @return void
	 */
	 //For the purposes of the getAllByUser this function is already part of the Webservice_Ticket::toEntity.
	 //The version here is for the methods here that still use it. We have here therefore some code
	 //duplicity
	 //Martin 16.2.2011
	private static function addInfo($data){
		$vAr = array();

		try{
			if($data['vyplacen'] == 0) {
				$vAr['state'] = 'ticket_state_1';
				$vAr['state_no'] = 1;
			}
			else if($data['vyplacen'] == 1 && $data['zruseno'] == 1) {
				$vAr['state_no'] = 4;
				$vAr['state'] = 'ticket_state_4';
				$vAr['rate'] = 1;
				$vAr['win'] = $data['castka'];
			}
			else if($data['vyplacen'] == 1 && $data['zruseno'] == 0 && $data['is_loss'] == 0) {
				$vAr['state_no'] = 2;
				$vAr['state'] = 'ticket_state_2';
				$vAr['rate'] = $data['rate_real'];
				$vAr['win'] = $data['win_real'];
			}
			else if($data['vyplacen'] == 1 && $data['zruseno'] == 0 && $data['is_loss'] == 1) {
				$vAr['state_no'] = 3;
				$vAr['state'] = 'ticket_state_3';
				$vAr['rate'] = $data['rate'];
				$vAr['win'] = $data['win'];
			}

			if(It6_Models_Ticket::TYPE_SYSTEM == $data['type']
				|| It6_Models_Ticket::TYPE_MAXI == $data['type'])
				$vAr['rate'] = '-';

			$vAr['typex'] = It6_Models_Ticket::getTypeName($data['type']);

			return $vAr;
		}

		catch ( Exception $e ) {
			Models_Exception_Handler::handle($e);
		}
	}



	public static function getTicketByHandleAndUser(&$view, $handle) {
		$userId	= Zend_Registry::get('user_id');
		if ( empty($userId) )
			return null;

		$ticket = Zend_Registry::get('ws')->Ticket->getByHandleAndUserComplete($handle, $userId, $_SESSION['lang_id'], true);
		if (!empty($ticket)) {
			$ticket = It6_ArrayWrapper::toNativeArray($ticket);
			$ticket['groups'] = It6_Models_Ticket::sortBetsByGroup($ticket['groups'], $ticket['type']);
	
			$view->currency		= (empty($ticket['currencyName']) ? '' : $ticket['currencyName']);
			$view->ticketState	= (empty($ticket['state']) ? '' : $ticket['state']); //It6_Models_Ticket::getRealTicketState($ticket);
			$view->printUrl		= $view->UrlSet(74).'?t='.$handle;
			$view->ticket		= $ticket;
			$view->ticketHandle	= $handle;
			$view->userId		= $userId;
		}
		else
			$view->ticket = false;
	}
	
	public static function getTicketByHandle(&$view, $handle) {
		$ticket = Zend_Registry::get('ws')->Ticket->getByHandleAndUserComplete($handle,null, $_SESSION['lang_id'], true);
		if (!empty($ticket)) {
			$ticket = It6_ArrayWrapper::toNativeArray($ticket);
			$ticket['groups'] = It6_Models_Ticket::sortBetsByGroup($ticket['groups'], $ticket['type']);

			$view->currency		= (empty($ticket['currencyName']) ? '' : $ticket['currencyName']);
			$view->ticketState	= (empty($ticket['state']) ? '' : $ticket['state']); //It6_Models_Ticket::getRealTicketState($ticket);
			$view->printUrl		= $view->UrlSet(74).'?t='.$handle;
			$view->ticket		= $ticket;
			$view->ticketHandle	= $handle;
		}
		else
			$view->ticket = false;
	}
}
