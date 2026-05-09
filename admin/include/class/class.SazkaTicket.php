<?php

include_once(ROOT.'common/library/It6/Models/Ticket.php');


/**
 * @package    book
 */

/**
 * Trida pro zobrazeni tiketu uzivatelu
 *
 *
 * <code>
 *
 * </code>
 *
 * @package    Ciselniky
 */

class SazkaTicket extends Template{

private $controller = null;

/**
 * navratova hodnota
 * @access private
 * @var string
 */
private  $vrat = "";

/**
 * sys. hlasky
 * @access private
 * @var array
 */
private  $messages = Array();

/**
 * sys. hlasky
 * @access private
 * @var array
 */
private  $errors = Array();

/**
 * vsechny prilezitosti
 * @access private
 * @var array
 */
private  $bets = Array();

/**
 * vsechny tikety
 * @access private
 * @var array
 */
private  $tickets = Array();

/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */
private  $dbGame;

/**
 * spojeni na databazi Betwarehosue
 * @access private
 * @var DB
 */
private  $dbBetWare;

/**
 * aktualni sekce
 * @access private
 * @var int
 */
private  $section;

/**
 *ma se ukazat filtr formular
 * @access private
 * @var bool
 */
private  $nomenu=false;

/**
 * zadavani multisazky
 * @access private
 * @var bool
 */
private  $multisazka = false;

/**
 * URL wrapper for this page
 */
private $url;

/**
 * TRUE if logged in admin has gov-supervisor role
 * @var boolean
 */
private $isGovSupervisor;

private $winningSqlSnippet =
	'(CASE
		WHEN vyplacen = 1 AND is_loss = 1 THEN 0
		WHEN vyplacen = 1 AND is_loss = 0 THEN t.win_real
		ELSE t.win 
	END)';

private $totalRateSqlSnippet =
	'(CASE
		WHEN vyplacen = 1 THEN t.rate_real
		ELSE t.rate
	END)';

private $orderOpts = array(
	1 => array(
		'label' => 'ticket_id',
		'col' => 't.ticket_id'
	),
	2 => array(
		'label' => 'eva',
		'col' => null //gets loade in constructor to prevent code duplicity
	),
	3 => array(
		'label' => 'stake',
		'col' => 't.castka',
	),
	4 => array(
		'label' => 'rate',
		'col' => null //gets loade in constructor to prevent code duplicity
	)
);

/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param int $section id aktualni sekce
* @param PEAR::DB $dbGame objekt spojeni s databazi
*/
	public function __construct($section = 0, $controller = null) {
		// loaded here to prevent code duplicity
		$this->orderOpts[4]['col'] = $this->totalRateSqlSnippet;
		$this->orderOpts[2]['col'] = $this->winningSqlSnippet;
		
		$this->section =  $section;
		$this->controller = $controller;
		$this->dbGame = DbUtil::connectWebDb();
		$this->dbBetWare  = DbUtil::connectWarehouseDb();
		$this->ws = Zend_Registry::get('ws');
		$this->acl = Zend_Registry::get('acl');
		$this->db = Zend_Registry::get('zdb_game');
		$this->dbAdmin = Zend_Registry::get('zdb_admin');

		$urlParams = array( 'section' => $this->section );

		if($this->section == "b12") {
			$this->multisazka = true;
		}

		$this->url = new Url(null, $urlParams);
		$this->isGovSupervisor = Zend_Registry::get('acl')->userHasRole(It6_Acl_Admin::ROLE_GOV_SUPERVISOR);
	}

/**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
	public function runAction($nomenu=false) {

		$this->nomenu = $nomenu;

		$ws = Zend_Registry::get('ws');

		$updateEnabled = $this->acl->isResourceAllowed('section:275','update');

		#Vymazani celeho tiketu#
		if (!empty($_GET['ticketDetail']) && is_numeric($_GET['ticketDetail'])) {
			$this->ShowTicket();
			$this->dbGame->disconnect();
			return;
		}
		else if(isset($_POST['delete_all_bets']) && $updateEnabled ) {
			$this->DeleteAllBetsOnTicket(intval(key($_POST['delete_all_bets'])));
		}
		else if(isset($_POST['delete']) && $updateEnabled ) {
			$ticketId = intval(key($_POST['delete']));
			$reason = (isset($_POST['duvod_zruseni'][$ticketId]) ? $_POST['duvod_zruseni'][$ticketId] : '');
			$this->DeleteTicket($ticketId, $reason);
		}
		#Obnova celeho tiketu#
		else if(isset($_POST['renew']) && $updateEnabled ) {
			$this->RenewTicket(intval(key($_POST['renew'])));
		}
		#Vymazani sazky na tiketu#
		else if(isset($_POST['delete_bet']) && $updateEnabled ) {

			$t = intval(key($_POST['delete_bet']));
			$s = intval(key($_POST['delete_bet'][$t]));

			$this->DeleteBetTicket($t,$s);
		}
		#Obnova sazky na tiketu#
		else if(isset($_POST['renew_bet']) && $updateEnabled ) {
			$t = intval(key($_POST['renew_bet']));
			$s = intval(key($_POST['renew_bet'][$t]));
			$this->RenewBetTicket($t,$s);
		}
		//INdividualni vyplaceni tiketu
		else if(isset($_POST['collect']) && $updateEnabled ) {
			$t = intval(key($_POST['collect']));
			$ws->Ticket->collect($t,true);
		}
		else if(isset($_POST['cancelCollect']) && $updateEnabled) {
			
			try {
				$isSuperAdmin = $this->acl->userHasRole(It6_Acl_Admin::ROLE_SUPERADMIN);
				if ( !$isSuperAdmin )
					throw new Exception("Just superadmin can cancel collect.");
				$t = intval(key($_POST['cancelCollect']));
				$ws->Ticket->cancelCollect($t);
				$this->messages[] = I18n::tr('Ticket #{0} collect was canceled',$t);
				It6_Log::info(
					"Ticket '%t%' collect was canceled successfuly.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('ticket'	=> $t )
				);
			}
			catch(Exception $e) {
				$this->errors[] = I18n::tr('Ticket #{0} collect cancelation failed.',$t);
				It6_Log::warn(
					"Error while ticket '%t%'  collect was canceled.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('ticket'	=> $t ),$e
				);
			}
		}
		else if(isset($_POST['allow_cancelation']) && $updateEnabled ) {
			$t = intval(key($_POST['allow_cancelation']));
			if ($ws->Ticket->allowCancel($t)) {
				$this->messages[] = I18n::tr('Ticket #{0} cancelation was allowed',$t);
				It6_Log::info(
					"Ticket '%t%' was allowed for cancelation.",
					It6_Log::TAG_BOOKMAKER_OPERATION,
					array('ticket'	=> $t )
				);
			}
			else {
				$this->errors[] = I18n::tr('Ticket #{0} error by allowing cancelation',$t);
				It6_Log::warn(
					"Error while ticket '%t%'  allowing for cancelation.",
					It6_Log::TAG_BOOKMAKER_OPERATION,
					array('ticket'	=> $t )
				);
			}
		}
		else if(!empty($_POST) ) {
			//Individualne vyplacene tikety
			if ( !empty($_POST['indcollection']) && $updateEnabled ) {
				foreach ( $_POST['indcollection'] as $t => $hostId ) {
					if ( $hostId == '' )
						$hostId = null;

					$ticket = $ws->Ticket->getById($t);

					if ( $ticket['forcedCollectionHostId'] != $hostId ) {
						if ($ws->Ticket->setForcedCollectionHostId($t, $hostId)) {
							$this->messages[] = I18n::tr('Ticket #{0} was set to collect on host {1}',$t,$hostId);
							It6_Log::info(
								"Ticket #%t% was set to collect on host %hostId%.",
								It6_Log::TAG_BOOKMAKER_OPERATION,
								array('ticket'	=> $t, 'hostId' => $hostId)
							);
						}
						else {
							$this->errors[] = I18n::tr('Ticket #{0} error forcing collection on host {1}',$t,$hostId);
							It6_Log::warn(
								"Ticket #%t%: error forcing collection on host %hostId%.",
								It6_Log::TAG_BOOKMAKER_OPERATION,
								array('ticket'	=> $t, 'hostId' => $hostId)
							);
						}
					}
				}
			}
		}

		$this->ShowTicket();

		$this->dbGame->disconnect();

	}

	private function testTicketNotPaidOut($ticketId, $actionDescr) {
		if (TicketUtil::isPaidOut($ticketId, $this->db)) {
			$this->errors[] = I18n::tr('Unable to ' . $actionDescr . '. The ticket #{0} was already paid out.', $ticketId);
			return false;
		}
		return true;
	}

/**
 * Vymazani sazky na tiketu
 * @param int $ticket_id id tiketu
 * @param int $sazka_id_id id sazky
 * @return void
 */
	private function DeleteBetTicket($ticket_id,$sazka_id) {


			if($this->testTicketNotPaidOut($ticket_id, 'delete bet')) {

				$this->ws->Ticket->cancelBet($ticket_id, $sazka_id);


				$this->messages[] = I18n::tr('The bet #{0} on the ticket #{1} was cancelled.', $sazka_id, $ticket_id );

				It6_Log::info(
					"Bet '%bet%' on ticket '%ticket%' was canceled.",
					It6_Log::TAG_ADMIN_OPERATION,
					array(
						'bet'		=> $sazka_id,
						'ticket'	=> $ticket_id
					)
				);
			}

	}

	private function DeleteAllBetsOnTicket($ticket_id) {

		try {
			if($this->testTicketNotPaidOut($ticket_id, 'delete bet')) {


				$this->ws->Ticket->cancelAllBets($ticket_id);


				$this->messages[] = I18n::tr('All bets on the ticket #{0} was rated as 1.', $ticket_id );

				It6_Log::info(
					"All bets on ticket '%ticket%' was rated as 1.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('ticket'	=> $ticket_id)
				);
			}
		} catch (Exception $e) {
			It6_Log::notice(
				"Rating all bets as 1 on ticket '%ticket%' failed.",
				array('ticket'	=> $ticket_id, 'message' => $e->getMessage()));
			$this->errors[] = I18n::tr('Rating all bets as 1 on ticket ${0} failed.',$ticket_id);
		}
	}

  /**
 * Obnova sazky na tiketu
 * @param int $ticket_id id tiketu
 * @param int $sazka_id_id id sazky
 * @return void
 */
	private function RenewBetTicket($ticket_id,$sazka_id){

		try {
			if($this->testTicketNotPaidOut($ticket_id, 'renew bet')) {

				$this->ws->Ticket->rewnewCanceledBet($ticket_id, $sazka_id);

				$this->messages[] =  I18n::tr('The bet #{0} on the ticket #{1} was renewed.', $sazka_id, $ticket_id );

				It6_Log::info(
					"Bet '%bet%' on ticket '%ticket%' was renewed.",
					It6_Log::TAG_ADMIN_OPERATION,
					array(
						'bet'		=> $sazka_id,
						'ticket'	=> $ticket_id
					)
				);
			}
		}
		catch(Exception $e) {
			It6_Log::notice(
				"Renewal of bet '%bet' ticket '%ticket%' failed: %message%.",
				array('%bet%' => $sazka_id, 'ticket'	=> $ticket_id, 'message' => $e->getMessage()));
			$this->errors[] = I18n::tr('Renewal on ticket ${0} failed: ${1}',$ticket_id, $e->getMessage());
		}

	}


 /**
 * Vymazani celeho tiketu
 * @param int $ticket_id id tiketu
 * @return void
 */
	private function DeleteTicket($ticket_id, $reason = '') {

		try {
			$isSuperAdmin = $this->acl->userHasRole(It6_Acl_Admin::ROLE_SUPERADMIN);
			if($isSuperAdmin || $this->testTicketNotPaidOut($ticket_id, 'delete ticket')) {

				$this->ws->Ticket->cancel($ticket_id, $reason);

				/*
				$sql = "
					UPDATE
						ticket
					SET
						zruseno = 1,
						zrusil_bookmaker_id = ".intval($_SESSION['bookmaker']).",
						duvod_zruseni = '".Help::Slash($_POST['duvod_zruseni'][$ticket_id])."'
					WHERE ticket_id = ".$ticket_id;

				$res =& $this->dbGame->query($sql);

				DbUtil::testResult($res);
				*/

				$this->messages[] =  I18n::tr('Ticket #{0} was cancelled.', $ticket_id );

				It6_Log::info(
					"Ticket '%ticket%' was canceled.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('ticket' => $ticket_id)
				);
			}
		}
		catch(Exception $e) {
			It6_Log::notice(
				"Cancelation of ticket '%ticket%' failed: %message%.",
				array('ticket'	=> $ticket_id, 'message' => $e->getMessage()));
			$this->errors[] = I18n::tr('Cancelation on ticket ${0} failed: ${1}',$ticket_id, $e->getMessage());
		}

	}

  /**
 * Obnova celeho tiketu
 * @param int $ticket_id id tiketu
 * @return void
 */
  private function RenewTicket($ticket_id) {

  	try {
		if($this->testTicketNotPaidOut($ticket_id, 'renew ticket')) {

			$this->ws->Ticket->renewCanceled($ticket_id);

			/*
			$sql = "
				UPDATE
					ticket
				SET
					zruseno = 0,
					zrusil_bookmaker_id = NULL,
					duvod_zruseni=''
				WHERE
					ticket_id=".$ticket_id;

			$res =& $this->dbGame->query($sql);

			DbUtil::testResult($res);
			*/

			$this->messages[] =  I18n::tr('The ticket #{0} was renewed.', $ticket_id );

			It6_Log::info(
				"Ticket '%ticket%' was renewed.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('ticket' => $ticket_id)
			);
		}
  	}
	catch(Exception $e) {
		It6_Log::notice(
			"Renewal of ticket '%ticket%' failed: %message%.",
			array('ticket'	=> $ticket_id, 'message' => $e->getMessage()));
		$this->errors[] = I18n::tr('Renewal on ticket ${0} failed: ${1}',$ticket_id, $e->getMessage());
	}

  }



	private function getCurrencies() {
		$currencies = array();
		$sql ='
			SELECT m.mena_id,m.mena_text,km.kurz
			FROM mena m
			JOIN kurz_mena km
			ON km.id_mena=m.mena_id
			JOIN kurz k
			ON k.id_kurz = km.id_kurz
			WHERE k.platny_od <= \'' . It6_Date::dbNow() . '\' AND k.platny_do > \'' . It6_Date::dbNow() . '\'
		';
		$res = $this->db->query($sql);

		while ($row = $res->fetch()) {
			$currencies[$row['mena_id']] = array(
				'rate' => $row['kurz'],
				'text' => $row['mena_text'],
			);
		}

		return $currencies;
	}
	
	
	
	private function getPointTypes() {
		$pointTypes = array();
		$rows = $this->db->query('SELECT id,name as `text`,rate FROM point_type')->fetchAll();
		foreach ($rows as $row)
			$pointTypes[$row['id']] = $row;
			
		return $pointTypes;
	}
	
	
	
	private function getUsers($users) {
		$sql = 'SELECT user_id,jmeno,prijmeni,nick,mena_id FROM uzivatel WHERE user_id IN (' . implode(',', array_keys($users)) . ')';
		$res = $this->db->query($sql);
		$users = array();
		
		while ($row = $res->fetch()) {
			$users[$row['user_id']] = array(
				'name' => $row['jmeno'] . ' ' . $row['prijmeni'],
				'nick' => $row['nick'],
				'currencyId' => $row['mena_id'],
			);
		}
		
		return $users;
	}
	
	
	
	private function getBookmakers($bookmakers) {
		$sql = 'SELECT first_name,surname,username,admin_id FROM admin WHERE admin_id IN (' . implode(',', array_keys($bookmakers)) . ')';
		$res = $this->dbAdmin->query($sql);
		$bookmakers = array();
		
		while ($row = $res->fetch()) {
			$bookmakers[$row['admin_id']] = array(
				'nick' => $row['username'],
				'name' => $row['first_name'] . ' ' . $row['surname'],
			);
		}
		
		return $bookmakers;
	}
	
	
	
	/**
	* metoda vypise vsechny zadane tickety podle filtru
	* @return void
	*/
	public function ShowTicket() {
		$branchIdIsHandle = (isset($_POST['branch_id_is_handle']) && 'yes' == $_POST['branch_id_is_handle']);

		if(
			isset($_POST['filtr'])
			|| !empty($_GET['ticket_id_search'])
			|| !empty($_GET['ticket_handle_search'])
			|| !empty($_GET['betId'])
			|| isset($_REQUEST['branch_id'])
			|| (!empty($_GET['ticketDetail']) && is_numeric($_GET['ticketDetail']))
		) {
			if (isset($this->controller))
				$this->controller->registerJsInclude('commonAjax');
			$db = Zend_registry::get('db');
			$now = It6_Date::dbNow();


			//get query where statement
			$where	= '1';
			if(isset($_POST['od']) && It6_Date::checkFormat($_POST['od'])) {
				$where .= " AND t.zalozen >= '".It6_Date::toDb($_POST['od'])."'";
			}
			if(isset($_POST['do']) && It6_Date::checkFormat($_POST['do'])) {
				$where .= " AND t.zalozen <= '".It6_Date::toDb($_POST['do'])."'";
			}
			if(isset($_POST['od_payout']) && It6_Date::checkFormat($_POST['od_payout'])) {
				$where .= " AND t.vyplacen_date >= '".It6_Date::toDb($_POST['od_payout'])."'";
			}
			if(isset($_POST['do_payout']) && It6_Date::checkFormat($_POST['do_payout'])) {
				$where .= " AND t.vyplacen_date <= '".It6_Date::toDb($_POST['do_payout'])."'";
			}
			if(isset($_POST['od_collect']) && It6_Date::checkFormat($_POST['od_collect'])) {
				$where .= " AND t.collection_time >= '".It6_Date::toDb($_POST['od_collect'])."'";
			}
			if(isset($_POST['do_collect']) && It6_Date::checkFormat($_POST['do_collect'])) {
				$where .= " AND t.collection_time <= '".It6_Date::toDb($_POST['do_collect'])."'";
			}
			if(isset($_POST['user']) && $_POST['user'] != 0) {
				$where .= " AND t.user_id = ".intval($_POST['user']);
			}
			if(isset($_POST['uid']) && $_POST['uid'] != 0) {
				$where .= " AND t.user_id = ".intval($_POST['uid']);
			}
			if(isset($_REQUEST['branch_id']) && $_REQUEST['branch_id'] != 0) {
				$sql = 'SELECT h.id FROM `host` h JOIN branch b ON h.branch_id=b.id WHERE ' . ($branchIdIsHandle ? 'b.handle' : 'b.id') . '=' . intval($_REQUEST['branch_id']);
				$rows = $this->dbAdmin->query($sql)->fetchAll();
				$hosts = array();
				
				foreach ($rows as $row)
					$hosts[] = $row['id'];
				
				if (!empty($hosts))
					$where .= ' AND t.host_id IN (' . implode(',', $hosts) . ')';
				else
					$where .= ' AND 1=0';
			}
			if(!empty($_POST['proplacene'])) {
				$where .= ' AND (t.vyplacen <> 0';
			}
			if(!empty($_POST['not_proplacene'])) {
				if(!empty($_POST['proplacene'])) {
					$where .= ' OR';
				}
				else {
					$where .= ' AND (';
				}
				$where .= ' t.vyplacen = 0)';
	
			}
			elseif(!empty($_POST['proplacene'])) {
				$where .= ')';
			}
	
			if(isset($_POST['is_loss'])) {
				$where .= ' AND t.is_loss = 1';
			}
			if(isset($_POST['is_canceled'])) {
				$where .= ' AND t.zruseno = 1';
			}
			
			if(isset($_POST['win_t'])) {
				$where .= ' AND t.vyplacen <> 0 AND t.is_loss=0';
			}
			if(isset($_POST['collected'])) {
				$where .= ' AND t.collection_time IS NOT NULL';
			}
			if(isset($_POST['not_collected'])) {
				$where .= ' AND t.vyplacen <> 0 AND t.is_loss=0 AND t.collection_time IS NULL AND t.forfeit IS NULL';
			}
			if(!empty($_POST['win_greater_then'])) {
				$where .= ' AND '.$this->winningSqlSnippet.' > ' . intval($_REQUEST['win_greater_then']);
			}
			if(!empty($_POST['rate_greater_then'])) {
				$where .= ' AND '.$this->totalRateSqlSnippet.' > ' . intval($_REQUEST['rate_greater_then']);
			}
			if(!empty($_POST['stake_greater_then'])) {
				$where .= ' AND t.castka > ' . intval($_REQUEST['stake_greater_then']);
			}
			if(isset($_POST['forfeited'])) {
				$where .= ' AND t.forfeit IS NOT NULL';
			}
			if(!empty($_REQUEST['betId'])) {
				$where .= ' AND tk.sazka_id = '.intval($_REQUEST['betId']);
			}
			if(isset($_REQUEST['ticket_id_search']) && is_numeric($_REQUEST['ticket_id_search'])) {
				$where .= ' AND t.ticket_id=' . intval($_REQUEST['ticket_id_search']);
			}
			if(isset($_GET['ticketDetail']) && is_numeric($_GET['ticketDetail'])) {
				$where .= ' AND t.ticket_id=' . intval($_GET['ticketDetail']);
			}
			else if(isset($_REQUEST['ticket_handle_search']) && is_numeric($_REQUEST['ticket_handle_search'])) {
				$handle = intval($_REQUEST['ticket_handle_search']);
				It6_NineDigitHandle::fixHandle($handle);
				$where .= " AND t.handle='$handle'";
			}
	
			//get lister
			$total		= $this->getTicketCount($where);
			$listerCount = $this->ws->Parameter->getAdminParameter(
				'pagination.Ticket.count',
				$this->acl->getIdentity(It6_Acl::IDNAME_ADMIN)
			);
			$lister = new Lister('tk', $listerCount, $total, 0, 15);
			$lister->setPost(true);
			$lister->updateFromParams($_POST);
			if (!empty($_POST['filtr']))
				$lister->setFrom(0);
			$from	= $lister->getFrom();
			$count	= $lister->getCount();
	
			//get query order
			if(empty($_POST['order_col'])) {
				$order = $this->orderOpts[1]['col'];
			}
			else {
				$order = $this->orderOpts[$_POST['order_col']]['col'];
			}
			if(isset($_POST['order_dir']) && $_POST['order_dir'] == 'ASC') {
				$orderDir = 'ASC';
			}
			else {
				$orderDir = 'DESC';
			}
			
			//get query coluns
			$columns = '
				t.user_id,
				t.handle AS ticket_handle,
				t.zalozen,
				t.zruseno,
				t.duvod_zruseni,
				t.cancel_allowed,
				t.zrusil_bookmaker_id,
				t.vyplacen_date,
				t.vyplacen_bookmaker_id,
				t.rate_real,
				t.rate,
				t.is_loss,
				t.win_real,
				t.win,
				t.castka,
				t.castka_body,
				t.type,
				t.vyplacen,
				t.free_bet_bonus,
				t.system,
				t.forced_collection_host_id,
				t.point_type_id,
				t.cash,
				t.collection_time,
				t.rate_advance,
				t.forfeit,
				t.group_count,
				t.cached_data,
				t.host_id,
				h.name AS hostName,
				tk.banker,
				tk.sazka_id,
				s.status,
				s.live,
				'.$this->winningSqlSnippet.' AS winning,
				'.$this->totalRateSqlSnippet.' AS total_rate,
			';

			$orderComp		= $order.' '.$orderDir;
			$dbRes			= $this->getTicketData($where, $columns, $from, $count, $orderComp);
			$ticketCount	= 0;
			$stakeCcSum		= 0;
			$bookmakers		= array();
			$users			= array();

			$this->tickets = array();
			foreach ($dbRes as $row) {
				$ticketId = $row['ticket_id'];
				if (!array_key_exists($ticketId, $this->tickets)) {
					$this->tickets[$ticketId] = array();
					$ticket = &$this->tickets[$ticketId];

					$ticket['ticket_id']			= $ticketId;
					$ticket['ticket_handle']		= $row['ticket_handle'];
					$userId							= $row['user_id'];
					$users[$userId]					= true;
					$ticket['user_id']				= $userId;
					$ticket['host_id']		= $row['host_id'];
					$ticket['hostName']				= $row['hostName'];
					$ticket['zalozen']				= It6_Date::fromDb($row['zalozen']);
					$ticket['live']					= $row['live'];
					$ticket['zruseno']				= $row['zruseno']; //zruseny cely ticket
					$ticket['duvod_zruseni']		= $row['duvod_zruseni'];
					$ticket['cancel_allowed']		= $row['cancel_allowed'];
					$bookmakerId					= $row['zrusil_bookmaker_id'];
					$ticket['zrusil_bookmaker_id'] 	= $bookmakerId;

					if (!empty($bookmakerId))
						$bookmakers[$bookmakerId] = true;

					if(It6_Date::isNullDbDatetime($row['vyplacen_date']))
						$ticket['vyplacen_date'] = '';
					else
						$ticket['vyplacen_date'] = It6_Date::fromDb($row['vyplacen_date']);

					$bookmakerId						= $row['vyplacen_bookmaker_id'];
					$ticket['vyplacen_bookmaker_id']	= $bookmakerId;
					
					if (!empty($bookmakerId))
						$bookmakers[$bookmakerId] = true;

// commented out by Martin on 17.10.2012
// this is now handled at the SQL level 
// 					if (!empty($row['vyplacen'])) {
// 						$ticket['total_rate']	= $row['rate_real'];
// 						$ticket['winning']		= ($row['is_loss'] ? 0 : $row['win_real']);
// 					}
// 					else {
// 						$ticket['total_rate']	= $row['rate'];
// 						$ticket['winning']		= $row['win'];
// 					}

					$ticket['winning']						= $row['winning'];
					$ticket['total_rate']					= $row['total_rate'];
					$ticket['isLoss']						= $row['is_loss'];
					$ticket['stake']						= $row['castka'];
					$ticket['stakeInPoints']				= $row['castka_body'];
					$ticket['type']							= $row['type'];
					$ticket['vyplacen']						= $row['vyplacen'];
					$ticket['kurz_celkem']					= 1;
					$ticket['vyhra_ticket']					= 0;
					$ticket['free_bet_bonus']				= $row['free_bet_bonus'];
					$ticket['system']						= $row['system'];
					$ticket['banker_num']					= 0;
					$ticket['forced_collection_host_id']	= $row['forced_collection_host_id'];
					$ticket['pointTypeId']					= $row['point_type_id'];
					$ticket['cash']							= $row['cash'];
					$ticket['collection_time']				= $row['collection_time'];
					$ticket['rateAdvance']					= $row['rate_advance'];
					$ticket['forfeit']						= $row['forfeit'];
					$ticket['group_count']					= (empty($row['group_count']) ? 1 : $row['group_count']);
					$ticket['betCount']						= 1;
					$ticket['betEvalCount']					= (0 == $row['status'] ? 0 : 1);
					$ticket['cached_data']					= $row['cached_data'];

					++$ticketCount;
				}
				else {
					$ticket = &$this->tickets[$ticketId];
					++$ticket['betCount'];
					$ticket['betEvalCount'] += (0 == $row['status'] ? 0 : 1);
				}

				if (1 == $row['banker'])
					++$ticket['banker_num'];
			}
		}

		if (!empty($this->tickets)) {
			if (!empty($users))
				$users = $this->getUsers($users);

			if (!empty($bookmakers))
				$bookmakers = $this->getBookmakers($bookmakers);

			$currencies = $this->getCurrencies();
			$pointTypes = $this->getPointTypes();
			foreach ($this->tickets as $ticketId => &$ticket) {
				$userId			= $ticket['user_id'];
				$user			= $users[$userId];
				$ticket['user']	= "{$user['name']} ({$user['nick']})";
				$currencyId		= $user['currencyId'];
				$pointTypeId	= $ticket['pointTypeId'];

				$currency = $currencies[$currencyId];				
				$ticket['currencyName']	= $currency['text'];
				if (!empty($pointTypeId))
					$ticket['currencyName'] .= ' (' . $pointTypes[$pointTypeId]['text'] . ')';
				
				$ticket['mena_id']		= $currencyId;
				$stakeCc				= (empty($currency['rate']) ? null : $row['castka'] / $currency['rate']);
				$ticket['stakeCc']		= $stakeCc;
				$stakeCcSum				+= (empty($stakeCc) ? 0 : $stakeCc);
				$bookmakerId			= $ticket['zrusil_bookmaker_id'];
				$ticket['zrusil']		= (empty($bookmakerId) ? '' : "{$bookmakers[$bookmakerId]['name']} ({$bookmakerId})");
				$bookmakerId			= $ticket	['vyplacen_bookmaker_id'];
				$ticket['vyplatil']		= (empty($bookmakerId) ? '' : "{$bookmakers[$bookmakerId]['name']} ({$bookmakerId})");
			}
		}
		
		
		$this->vrat .= UiUtil::printMessages($this->messages);
		$this->vrat .= UiUtil::printErrors($this->errors);

		$this->vrat .= $this->filterForm($branchIdIsHandle);

		$ticketCount = count($this->tickets);
		if ($ticketCount > 0)
			$this->vrat .= $lister->getOutput(null, 0, array('formId' => 'formTiketyFiltr'));

		$this->vrat .= $this->ticketForm($this->tickets);

		if ($ticketCount > 0)
			$this->vrat .= $lister->getOutput(null, 1, array('formId' => 'formTiketyFiltr'));

		if($this->nomenu == false)
			$this->vrat .= '</form><a name="bo"></a>';
	}


	/**
	 * Returns the total number of tickets matching the given conditions
	 * @param string $where The WHERE part of the sql query
	 * @return int
	 */
	public function getTicketCount($where='1') {
		$sql = "
			SELECT Count(*) AS count
				FROM (
					SELECT t.ticket_id 
					FROM ticket t 
					JOIN `vic_admin`.`host` h 
						ON `h`.`id`=`t`.`host_id` 
					JOIN `vic_admin`.`branch` b
						ON `b`.`id`=`h`.`branch_id`
					JOIN `ticket_kurz` tk 
						ON tk.ticket_id = t.ticket_id
					JOIN `sazky` s
						ON tk.sazka_id = s.sazka_id
					WHERE ".$where."
					GROUP BY t.ticket_id
				) AS T";
		$ticketCount = $this->db->query($sql)->fetch();
		$ticketCount = $ticketCount['count'];
		
		return $ticketCount;
	}


	/**
	 * Returns tickets matching the arguments
	 * @param string $where The WHERE part of the sql query
	 * @param string $columns Comma separated list of columns
	 * @param int $from The offset part of the LIMIT sql statement
	 * @param int $count The total count part of LIMIT sql statement
	 * @param string $order The ORDER part of the sql query
	 * @return array
	 */
	public function getTicketData($where, $columns, $from=null, $count=null, $order=null) {
		//limit nad vnorenym dotazem kvuli optimalizaci, paradoxne je to tak vyrazne rychlejsi
		$sql = "
			SELECT * FROM 
			(SELECT
				t.ticket_id
			FROM ticket t
			JOIN `vic_admin`.`host` h
				ON `h`.`id`=`t`.`host_id`
			JOIN `vic_admin`.`branch` b
				ON `b`.`id`=`h`.`branch_id`
			JOIN `ticket_kurz` tk
				ON tk.ticket_id = t.ticket_id
			JOIN `sazky` s
					ON tk.sazka_id = s.sazka_id
			WHERE ".$where."
			GROUP BY t.ticket_id";
		if($order !== null)
			$sql .= " ORDER BY $order) tickets";
		if($from !== null && $count !== null)
			$sql .= " LIMIT $from, $count";

		$dbRes = $this->db->query($sql)->fetchAll();;
		foreach($dbRes as $row) {
			$tickets[$row['ticket_id']] = $row;
		}

		if(!empty($tickets)) {
			$sql = "
				SELECT
					".$columns."
					tk.ticket_id
				FROM ticket t
				JOIN `vic_admin`.`host` h
					ON `h`.`id`=`t`.`host_id`
				JOIN `ticket_kurz` tk
					ON tk.ticket_id = t.ticket_id
				JOIN `sazky` s
					ON tk.sazka_id = s.sazka_id
				WHERE tk.ticket_id IN (".implode(',', array_keys($tickets)).")
				";
			if($order !== null)
				$sql .= " ORDER BY $order";

			$ticketsFull = array();
			$dbRes = $this->db->query($sql)->fetchAll();
			foreach($dbRes as $row) {
				$ticketsFull[$row['ticket_id']] = $row;
			}
		}
		else
			$ticketsFull = array();

		return $ticketsFull;
	}

	
	/**
	 * Returns the html with the filter form
	 * @return string
	 */
	private function filterForm($branchIdIsHandle) {
		if($this->nomenu == false) {
			$data = array(
				'userId' => (isset($_POST['uid']) ? Help::Html($_POST['uid']) : ""),
				'branchId' => (isset($_REQUEST['branch_id']) ? Help::Html($_REQUEST['branch_id']) : ""),
				'branchIdIsHandle' => $branchIdIsHandle,
				'isWin' => (!empty($_POST['win_t']) ? $_POST['win_t'] : 0),
				'isCollected' => (!empty($_POST['collected']) ? $_POST['collected'] : 0),
				'isNotCollected' => (!empty($_POST['not_collected']) ? $_POST['not_collected'] : 0),
				'isForfeited' => (!empty($_POST['forfeited']) ? $_POST['forfeited'] : 0),
				'isPaidOut' => (!empty($_POST['proplacene']) ? $_POST['proplacene'] : 0),
				'isNotPaidOut' => (!empty($_POST['not_proplacene']) ? $_POST['not_proplacene'] : 0),
				'ticketId' => (isset($_POST['ticket_id_search']) ? Help::Html($_POST['ticket_id_search']) : (isset($_GET['ticketDetail']) ? Help::Html($_GET['ticketDetail']) : "")),
				'ticketHandle' => (isset($_POST['ticket_handle_search']) ? Help::Html($_POST['ticket_handle_search']) : ""),
				'stakeGreaterThen' => (isset($_POST['stake_greater_then']) ? Help::Html($_POST['stake_greater_then']) : ""),
				'rateGreaterThen' => (isset($_POST['rate_greater_then']) ? Help::Html($_POST['rate_greater_then']) : ""),
				'winGreaterThen' => (isset($_POST['win_greater_then']) ? Help::Html($_POST['win_greater_then']) : ""),
				'payoutFrom' => (isset($_POST['od_payout']) ? Help::Html($_POST['od_payout']) : ""),
				'payoutTo' => (isset($_POST['do_payout']) ? Help::Html($_POST['do_payout']) : ""),
				'collectFrom' => (isset($_POST['od_collect']) ? Help::Html($_POST['od_collect']) : ""),
				'collectTo' => (isset($_POST['do_collect']) ? Help::Html($_POST['do_collect']) : ""),
				'betId' => (isset($_REQUEST['betId']) ? Help::Html($_REQUEST['betId']) : ""),
				'createdFrom' => (isset($_POST['od']) ? Help::Html($_POST['od']) : ""),
				'createdTo' => (isset($_POST['do']) ? Help::Html($_POST['do']) : ""),
				'orderCol' => (isset($_POST['order_col']) ? Help::Html($_POST['order_col']) : ""),
				'orderDir' => (isset($_POST['order_dir']) ? Help::Html($_POST['order_dir']) : ""),
				'orderOpts' => $this->orderOpts,
				'url' => $this->url->getUrl(true),
				'isCanceled' => (isset($_POST['is_canceled']) ? Help::Html($_POST['is_canceled']) : ""),
				'isLoss' => (isset($_POST['is_loss']) ? Help::Html($_POST['is_loss']) : ""),
			);
			
			return Utils::processTemplate('Template/SazkaTicket/filter.phtml', $data, true);
		}
		else {
			return '';
		}
	}
	

	/**
	 * Returns the html with the table of tickets
	 * @param array $tickets Tickets to be displayed
	 * @param Lister $lister The lister object
	 * @return string
	 */
	private function ticketForm(array $tickets) {
		require_once('controllers/TicketsController.php');
		
		$data = array(
			'ticketDetailSectionId' => TicketsController::TICKET_DETAIL_SECTION_ID,
			'tickets' => $tickets,
			'canUpdate' => $this->acl->isResourceAllowed('section:275','update'),
			'isSuperAdmin' => $this->acl->userHasRole(It6_Acl_Admin::ROLE_SUPERADMIN),
			'isGovSupervisor' => $this->isGovSupervisor,
			'nomenu' => $this->nomenu
		);
		
		return Utils::processTemplate('Template/SazkaTicket/ticketForm.phtml', $data, true);
	}

	public function getTicketBetsTotals($id,$type,$total_stake, $cash) {
		$total = array();
		//$total['stake'] = 0;
		$total['rate_total'] = 1;
		$total['winning'] = 0;

		if ( ($type == 'simple') || ($type == 'kombi') ) {
			foreach ($this->tickets[$id]['bet'] as $b) {
				if ( true == $b['canceled'] )
					continue;
				$total['rate_total'] *= $b['kurz'];
			}
			$total['rate_total'] = It6_Models_Ticket::roundRate($total['rate_total']);
			$total['winning'] = It6_Models_Ticket::roundStake($total['rate_total'] * $total_stake, $cash, $this->dbGame) ; //Help::roundPrice($total['rate_total'] * $total_stake);
			$total['stake'] = $total_stake;
		} else {
			$total['rate_total'] = $type.' N/A';
			$total['winning'] = $type.' N/A';
			$total['stake'] = $type.' N/A';
		}
		return $total;
	}



	public function getTicketBets($ticket, Array $bets) {
		$ticketId = $ticket['ticket_id'];
		$type = $ticket['type'];
		$stake = $ticket['stake'];
		$ticbet = array();
		foreach ($bets as $b) {
			if ($b['ticket_id'] == $ticketId) {
				if (It6_Models_Ticket::TYPE_SIMPLE == $type)
					$b['amount'] = $stake;
				$ticbet[] = $b;
			}
		}
		return $ticbet;
	}



 /**
 * Vraci vystup do tridy main
 * @return string
 */
	public function getContent(){
		return $this->vrat;
	}



 /**
 * vyber dat z databaze
 * @return object
 */
	public function selectData($where=""){

	}



  /**
 * Nastaveni prav k sekci
 * @param int $update pravo zapisu
 * @param int $delete pravo smazani
 * @return void
 */
	public function setPrivileges($update,$delete){

	}

	public function __destruct(){

	}
}
