<?php
/**
 * Trida pro proplaceni tiketu
 */
class ProplatitTicket extends Template{

/**
 * navratova hodnota
 * @access private
 * @var string
 */
private  $vrat = "";

/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */
private  $dbGame;

/**
 * pole prodelecnych sazek nej 5
 * @access private
 * @var array
 */
private  $prodelalo_ar = array();

/**
 * pole vydelecnych sazek nej 5
 * @access private
 * @var array
 */
private  $vydelalo_ar = array();


/**
 * pole prekladu
 * @access private
 * @var array
 */
private  $preklad;

/**
 * User feedback
 * @access private
 * @var array
 */
private $warnings = array();

/**
 * The number of top tickets to show
 * @var int
 */
private $topShowCount = 20;

/**
 * Ticket payout batch size
 * @var int
 */
private $ticketBatchSize = 75;


/**
* Konstruktor
* Pokud neni identifikator spojeni predan vytvori se nove spojeni
* @param int $section id aktualni sekce
* @param PEAR::DB $dbGame objekt spojeni s databazi
*/
  public function __construct($section=0){
    $this->section =  $section;


    $this->dbGame = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
    if (DB::isError($this->dbGame)) {
      throw new ExHandler($this->dbGame->getMessage(),"admin_ex_db");
    }
    $this->dbGame->setFetchMode(DB_FETCHMODE_ASSOC);
    $sql = "set names 'utf8'";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");

   if($this->section == "b12") $this->multisazka = true;

  }

/**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
  public function runAction(){
   if(isset($_POST['priprava']))
      $this->PreparePayment();
   else if(isset($_POST['payment_ok']))
      $this->ProvePayment();
   else if(isset($_POST['proplatit']))
      $this->PrepareTicket();
   else if(isset($_POST['ticket_ok']))
      $this->ProveTicket();
   else
      $this->firstScreen();

   $this->dbGame->disconnect();

  }


// commneted out by Martin on 11.9.2012
// this method only seems to be called in a commented out line pf class.VratitSazku.php
 /**
 * Metoda zauctuje start limitu pro obnovene tikety pri ruseni vysledku nejake sazky
 * @param array $ticket  tento tiket
 * @param object $db  sesison databaze
 */
/*  final public static function ProcedureLimitRenewBet($ticket,$db){

    $l = new Limit($db);

	$sport = $castka_sport = array();

	if($ticket['system']!=0)
	  $vyhra = $ticket['system_win'];
	else
      $vyhra = $ticket['kurz_all']*$ticket['castka'];
	$vyhra = round($vyhra,2);
	echo
    $pocet_sazek =  $ticket['pocet_sazek'];
	$castka_sazka = ($ticket['castka']/$pocet_sazek);


    foreach($ticket['sport'] as $k=>$h){

	 $castka_sport[$k] =  ($h['pocet']*$castka_sazka);

	 $procento = (($h['rate']/$ticket['kurz_sum'])*100);
	 $sport[$k] = (($procento/100)*$vyhra);

	}

	foreach($ticket['sport'] as $k=>$h) {
		$l->betStart($k,$ticket['user'],($sport[$k]-$castka_sport[$k]),$ticket['mena']);
	}

  }
*/

// commneted out by Martin on 11.9.2012
// this method seems to be a zombie that doesnt get called from anywhere
  /**
 * Metoda zauctuje limity uzivatelu
 * @param array $ticket  obsahuje info o tiketu
 * @param int $v priznak 1=bud vyherni tiket nebo vyplacen cv kurzu 1; 0=prohra
 */
  /*final private function ProcedureLimit($ticket,$v=null){

	$sport = $sport_real = $castka_sport  = $castka_sport_real  = array();
	$l = new Limit($this->dbGame);

	if($ticket['system'] != 0){
	  $vyhra = $ticket['system_win'];
	  $vyhra = round($vyhra,2);
	  $vyhra_real = $ticket['system_win_real'];
	  $vyhra_real = round($vyhra_real,2);
	}else{
	  $vyhra = ($ticket['castka']*$ticket['kurz_celkem_real']);
	  $vyhra = round($vyhra,2);
	  $vyhra_real = ($ticket['castka']*$ticket['kurz_celkem']);
	  $vyhra_real = round($vyhra_real,2);
	}
	$pocet_sazek =  count($ticket['sazka']);
	$castka_sazka = ($ticket['castka']/$pocet_sazek);

	if($v == 0 && $ticket['system'] != 0 && $vyhra_real <= $ticket['castka'])
	  $castka_sazka_real = (($ticket['castka']-$vyhra_real)/$ticket['pocet_real']);
	else
	  $castka_sazka_real = ($ticket['castka']/$ticket['pocet_real']);

    foreach($ticket['sport'] as $k=>$h){

	 $castka_sport[$k] =  ($h['pocet']*$castka_sazka);
	 $procento = (($h['rate']/$ticket['kurz_sum'])*100);
	 $sport[$k] = (($procento/100)*$vyhra);

	 $sport_real[$k] = $castka_sport_real[$k] = 0;
	 if(isset($h['pocet_real'])){
	  $castka_sport_real[$k] =  ($h['pocet_real']*$castka_sazka_real);
	  $procento = (($h['rate_real']/$ticket['kurz_sum_real'])*100);
	  $sport_real[$k] = (($procento/100)*$vyhra_real);
	 }

	}

	if($v == 1){

	 foreach($ticket['sport'] as $k=>$h){

	  if($ticket['kurz_celkem'] == 1)
	   $l->betClose($k,$ticket['user_id'],0,($sport[$k]-$castka_sport[$k]),$ticket['mena_id']);
	  else
	   $l->betClose($k,$ticket['user_id'],($sport_real[$k]-$castka_sport_real[$k]),($sport[$k]-$castka_sport[$k]),$ticket['mena_id']);

	 }

	}
	else if($v == 0){

	 foreach($ticket['sport'] as $k=>$h){

	  $l->betClose($k,$ticket['user_id'],(-1*$castka_sport_real[$k]),($sport[$k]-$castka_sport[$k]),$ticket['mena_id']);

	 }

	}
	else{
	 $this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se zauctovat limity',"admin_ex_page");
	}

  }
*/

 /**
 * vyplati tikety na ucty
 * @return void
 */
private function ProveTicket(){
	$db = Zend_Registry::get('zdb_game');
	$dbAdmin = Zend_Registry::get('zdb_admin');
	It6_DbTransaction::begin($db);
	$failedTicket = array();
	$ticketIds = $this->getPayoutBatchTicketIds($db);

//TODO: rewrite not using ticket_pohled
   $sql = "SELECT
a.ticket_id,
a.ticket_handle,
a.type,
a.zalozen,
a.free_bet_bonus,
a.platna_do,
a.system,
a.banker,
a.mail,
a.sms,
a.sazka_id,
a.zruseno,
k.rate AS kurz,
a.udalost_id,
a.proplacena,
a.text,
a.user_id,
a.castka,
a.castka_body,
a.ticket_sazka_zrusena,
a.status,
a.vysledek,
a.sloupec_id,
a.group_id,
a.point_type_id,
a.rate_advance,
a.cash,
a.mp,
a.mp_win,
a.ticket_branch_id,
b.email,
b.lang_id,
b.mena_id,
b.anonymous,
c.iso,
d.nazev AS typ_nazev,
d.typ_id,
e.sport_id,
h.mena_text
FROM ticket_pohled a
INNER JOIN ticket_kurz k ON a.ticket_id=k.ticket_id AND a.sazka_id=k.sazka_id
INNER JOIN uzivatel b ON a.user_id = b.user_id
INNER JOIN mena h ON b.mena_id = h.mena_id
INNER JOIN jazyky c ON  b.lang_id = c.lang_id
INNER JOIN typ d ON  d.typ_id = a.typ_id
INNER JOIN udalost e ON  e.udalost_id = a.udalost_id
WHERE a.vyplacen = 0
  AND a.zruseno <> 1
  AND ".$db->quoteInto('a.ticket_id IN (?)', $ticketIds)."
  AND a.ticket_id NOT IN (
    SELECT DISTINCT t.ticket_id
    FROM ticket_pohled t
    WHERE t.vyplacen = 1 OR t.proplacena = 0
  )";

	try {
		$res = $db->query($sql);
	}
	catch (Exception $e) {
		It6_DbTransaction::rollback($db);
		throw new ExHandler($sql . ' Nepodarilo se provest dotaz: vyber ticket_pohled',"admin_ex_db");
	}

	$ticket = $ticket_id = $userTickets = $section =  $sloupec = $popular_ar = $full_sys = array();

	while ($row = $res->fetch()) {
		if(!isset($ticket[$row['ticket_id']])) {
			$ticket_id[] = "#".$row['ticket_id'];
			$ticket[$row['ticket_id']] = array(
				'handle' => $row['ticket_handle'],
				'zruseno' => $row['zruseno'],
				'user_id' => $row['user_id'],
				'mena_id' => $row['mena_id'],
				'castka' => $row['castka'],
				'zalozen' => $row['zalozen'],
				'mena_text' => $row['mena_text'],
				'kurz_celkem' => 1,
				'free_bet_bonus' => $row['free_bet_bonus'],
				'kurz_sum' => 0,  //soucet kurzu kvuli limitum
				'kurz_sum_real' => 0,
				'kurz_celkem_real' => 1,  //tento kurz slouzi pro limity
				'pocet_real' => 0,
				'consistenci_error' => 0,
				'system' => $row['system'],
				'banker_num' => 0,
				'system_win_real' => 0,
				'mail' => $row['mail'],
				'sms' => $row['sms'],
				'system_ar_all' => array(),
				'cash' => $row['cash'],
				'host_id' => $row['ticket_branch_id'],
				'helperData' => array(
					'ticket_id' => $row['ticket_id'],
					'userId' => $row['user_id'],
					'type' => $row['type'],
					'totalSum' => $row['castka'],
					'totalSumInPoints' => empty($row['castka_body']) ? null : $row['castka_body'],
					'pointType' => empty($row['point_type_id']) ? null : $row['point_type_id'],
					'rateAdvance' => empty($row['rate_advance']) ? null : $row['rate_advance'],
					'mp' => $row['mp'],
					'mpWin' => $row['mp_win'],
					'cash' => $row['cash'],
					'anonymous' => $row['anonymous'],
					'bet' => array(),
				),
			);

			$full_sys[$row['ticket_id']] = 1; // default = yes
		}

		if(It6_Date::fromDbAsTimestamp($row['platna_do']) < It6_Date::fromDbAsTimestamp($row['zalozen'])) {
			try {
				$data = array('ticket_sazka_zrusena' => 1, 'ticket_sazka_duvod_zruseni' => 'tiket zalozen po splatnosti sazky' );
				$db->update('ticket_kurz', $data, 'sazka_id=' . $row['sazka_id'] . ' AND ticket_id=' . $row['ticket_id']);
				$this->warnings[] = I18n::tr('Sázka {0} je po splatnosti. Tiket {1} byl zalozen po ukonceni sazky - vyplácím ho v kurzu 1.',$row['sazka_id'],$row['ticket_id']);
				It6_Log::info(
					"Ticket #'%ticket_id%' payed out in rate 1. Bet #'%bet_id%' expired before ticket was created",
					It6_Log::TAG_BOOKMAKER_OPERATION,
					array(
						'ticket_id' => $row['ticket_id'],
						'bet_id' => $row['sazka_id']
						)
				);
			}
			catch (Exception $e) {
				It6_DbTransaction::rollback($db);
				It6_Log::err(
					"Error ticket pay out in rate 1.",
					It6_Log::TAG_BOOKMAKER_OPERATION,
					array(
						'ticket_id' => $row['ticket_id'],
						'bet_id' => $row['sazka_id']
						),
					$e
				);
				throw new ExHandler('Error ticket pay out in rate 1.', 'admin_ex_db');
			}
			$row['ticket_sazka_zrusena'] = 1;
		}

		$betCanceled = (1 == $row['status'] || 1 == $row['zruseno'] || 1 == $row['ticket_sazka_zrusena']);

		#vysledek#
		$betResult = array();
		foreach (explode(";", $row['vysledek']) as $_result) {
			if (!empty($_result))
				$betResult[] = $_result;
		}
		$betWins = in_array($row['sloupec_id'], $betResult);

		if (!$betWins)
			$full_sys[$row['ticket_id']] = 0;

		$ticket[$row['ticket_id']]['kurz_celkem_real'] *= $row['kurz'];
		$ticket[$row['ticket_id']]['kurz_sum'] += $row['kurz'];

		if(!$betCanceled) {
			$ticket[$row['ticket_id']]['pocet_real']++;
			$ticket[$row['ticket_id']]['kurz_sum_real'] += $row['kurz'];
		}

		#urceni poctu sportu na tiketu#
		if (!isset($ticket[$row['ticket_id']]['sport'][$row['sport_id']])) {
			$ticket[$row['ticket_id']]['sport'][$row['sport_id']]['rate'] = $row['kurz'];
			$ticket[$row['ticket_id']]['sport'][$row['sport_id']]['pocet'] = 1;
		} else {
			$ticket[$row['ticket_id']]['sport'][$row['sport_id']]['rate'] += $row['kurz'];
			$ticket[$row['ticket_id']]['sport'][$row['sport_id']]['pocet']++;
		}

		#urceni realneho poctu sportu na tiketu#
		if (!$betCanceled) {
			if (!isset($ticket[$row['ticket_id']]['sport'][$row['sport_id']]['rate_real'])) {
				$ticket[$row['ticket_id']]['sport'][$row['sport_id']]['rate_real'] = $row['kurz'];
				$ticket[$row['ticket_id']]['sport'][$row['sport_id']]['pocet_real'] = 1;
			} else {
				$ticket[$row['ticket_id']]['sport'][$row['sport_id']]['rate_real'] += $row['kurz'];
				$ticket[$row['ticket_id']]['sport'][$row['sport_id']]['pocet_real']++;
			}
		}

		$ticket[$row['ticket_id']]['sazka'][$row['sazka_id']] = array(
			'ticket_sazka_zrusena' => $row['ticket_sazka_zrusena'],
			'sloupec_id' => $row['sloupec_id'],
			'udalost_id' => $row['udalost_id'],
			'status' => $row['status'],
			'banker' => $row['banker'],
			'kurz' => $row['kurz'],
			'sport_id' => $row['sport_id'],
			'text' => $row['text'],
			'typ_nazev' => $row['typ_nazev'],
			'typ_id' => $row['typ_id'],
			'vysledek' => $betResult,
			'proplacena' => $row['proplacena'],
		);
		$ticket[$row['ticket_id']]['helperData']['bet'][$row['sazka_id']] = array(
			'sazka_id' => $row['sazka_id'], 'id_col' => $row['sloupec_id'], 'amount' => $row['castka'], 'rate' => $row['kurz'],
			'group' => $row['group_id'], 'vysledek' => $row['vysledek'], 'canceled' => $betCanceled, 'paidOut' => $row['proplacena'],
			'eventId' => $row['udalost_id'],
		);

		if(!$betCanceled) {
			$ticket[$row['ticket_id']]['kurz_celkem'] *= $row['kurz'];
		}
	} // fetchRow


	It6_DbTransaction::commit($db);
	$vyherci_date = It6_Date::dbNow();

	foreach ($ticket as $k => &$h2) {
		if (!empty($h2['helperData'])) {
			$h2['helper'] = new It6_Models_Ticket($h2['helperData'], It6_Models_Ticket::DATA_ADMIN_TICKET, $h2['mena_id'], $db);
			$h2['helper']->computeAggregates();
			$h2['helper']->computeBetWonAndWinDistribution();
		}
	}
	unset($h2);

	$ws = Zend_Registry::get('ws');
	$ticketGamesRaw = $ws->Campaign->getValidTicketGames();
	$ticketGames = array();
	foreach($ticketGamesRaw as $game) {
		$ticketGames[$game['gameId']] = $game;
	}
	$gameTickets = array(); // map (game ID => list of ticket IDs) for cache invalidation
	$gameHandlers = array(); // spare DB call in cache invalidation by storing handlers in map (game ID => class name)

	foreach ($ticket as $k => $h2) {
		It6_DbTransaction::begin($db);
		It6_DbTransaction::begin($dbAdmin);
		try {
			$dbAdminCommited = false;
			try {
				$res = $db->query('SELECT vyplacen FROM ticket WHERE ticket_id=' . $k . ' FOR UPDATE');
			}
			catch (Exception $e) {
				throw new Exception($sql . 'Nepodarilo se provest dotaz: select vyplacen', 0, $e);
			}

			if ($row = $res->fetch())  {
				if (1 == $row['vyplacen']) {
					$ticket[$k]['consistence_error'] = 1;
					It6_DbTransaction::rollback($dbAdmin);
					It6_DbTransaction::rollback($db);
					continue;
				}
			}

			try {
				$payoutTs = time();
				$data = array(
					'vyplacen' => 1,
					'vyplacen_date' => It6_Date::timestampToDb($payoutTs),
					'vyplacen_bookmaker_id' => $_SESSION['bookmaker'],
					'is_loss' => 0,
					'cached_data' => serialize($h2['helper']),
				);
				$res = $db->update('ticket', $data, 'ticket_id=' . $k);

				foreach ($h2['helper']->bets as $bet) {
					$data = array(
						'won' => $bet['won'],
					);

					$group = isset($bet['group']) ? $bet['group'] : 1;
					$where = "`ticket_id` = '$k' AND " .
							"`sazka_id` = '{$bet['sazka_id']}' AND " .
							"`sloupec_id` = '{$bet['id_col']}' AND " .
							"`group_id` = '{$group}'";

					$db->update(
						'ticket_kurz',
						$data, $where);
				}
			}
			catch (Exception $e) {
				throw new Exception($sql . 'Nepodarilo se provest dotaz: update tiketu', 0, $e);
			}

			//wu_ticket_deposit
			$status = 1;
			$vyhra = 0.0;

			if ($h2['zruseno'] == 1) {
				$vyhra = $h2['castka'];
			}

			else {
				if (It6_Models_Ticket::RESULT_WON != $h2['helper']->result) {
					$status = 0;
				}
				$vyhra = $h2['helper']->won;
			}
			$vyhra = round($vyhra, 2);

			if (!isset($userTickets[$h2['user_id']]))
				$userTickets[$h2['user_id']] = array();
			$userTickets[$h2['user_id']][] = $k;


			if (1 == $status) { // ticket wins
				$vyhra = $h2['helper']->won;

				if (1 == $h2['free_bet_bonus']) {
					$vyhra = $vyhra - $h2['castka'];
				}

				if ($vyhra < 0) {
					$vyhra = 0;
				}

				//kontrola zda-li se nevyplaci to co jiz je v tabulce vyherci_sazky
				try {
					$res = $db->query('SELECT COUNT(ticket_id) AS c FROM vyherci_sazky WHERE ticket_id=' . $k);
					$row = $res->fetch();
				}
				catch (Exception $e) {
					throw new Exception('Nepodarilo se provest dotaz: select vyherci_sazky ', 0, $e);
				}

				if ( $row['c'] > 0 ) {
					throw new Exception('Tiket jiz vyplacen: ' . $k, 'admin_ex_game');
				}

				try {
					$ws->Transaction->make(array(
						'value' => $vyhra,
						'userId' => $h2['user_id'],
						'typeName' => 'other.ticket.payout',
						'currencyId' => $h2['mena_id'],
						'ticketId' => $k,
						'hostId' => $h2['host_id']));

					if ( floatval($h2['helper']->mpWin) > 0 && empty($h2['helper']->canceled) ) {

						// Jirka - uprava odecitani manipulacniho poplatku pri vratce u kombinatoru
						if ($h2['helper']->type == 'maxikombi') {

							$mpWinAmount = 0;
							foreach ($h2['helper']->combinations as $key => $comb) {
								foreach ($comb["data"] as $key_data => $value) {
									if (empty($value['canceled']) && $value['resultValue'] == 1 && $value['rate'] > 1) {
										$mpWinAmount += $comb["stake"]/10;
									}
								}
							}
							$h2['helper']->mpWinAmount = $mpWinAmount;

							if ($h2['helper']->mpWinAmount > 0) {
								$ws->Transaction->make(array(
									'value' => - $h2['helper']->mpWinAmount,
									'userId' => $h2['user_id'],
									'typeName' => 'other.ticket.payout-mp',
									'currencyId' => $h2['mena_id'],
									'ticketId' => $k,
									'hostId' => $h2['host_id']
								));
							}

						} else {

							$ws->Transaction->make(array(
								'value' => -$h2['helper']->mpWinAmount,
								'userId' => $h2['user_id'],
								'typeName' => 'other.ticket.payout-mp',
								'currencyId' => $h2['mena_id'],
								'ticketId' => $k,
								'hostId' => $h2['host_id']
							));

						}

					}

					if ( !$h2['cash'] ) {
						$ws->Transaction->make(array(
							'value' => $vyhra,
							'userId' => $h2['user_id'],
							'typeName' => 'user.ticket.collect-nocash',
							'currencyId' => $h2['mena_id'],
							'ticketId' => $k,
							'hostId' => $h2['host_id']));

						if ( floatval($h2['helper']->mpWin) > 0 && empty($h2['helper']->canceled) ) {
							$ws->Transaction->make(array(
								'value' => -$h2['helper']->mpWinAmount,
								'userId' => $h2['user_id'],
								'typeName' => 'user.ticket.collect-nocash-mp',
								'currencyId' => $h2['mena_id'],
								'ticketId' => $k,
								'hostId' => $h2['host_id']));
						}
					}

				}
				catch (Exception $e) {
					throw new ExHandler('Nepodarilo se provest transackci', 0, $e);
				}

				try {
					$data = array(
						'user_id' => $h2['user_id'],
						'ticket_id' => $k,
						'castka' => $vyhra,
						'datum' => $vyherci_date,
						'rate' => $h2['helper']->rate,
						'full_win' => $full_sys[$k],
					);
					$db->insert('vyherci_sazky', $data);
				}
				catch (Exception $e) {
					throw new Exception('Nepodarilo se provest dotaz: vlozeni vyherci_sazky (' . $h2['user_id'] . '/' . $k . ')', 0, $e);
				}

				if (1 == $h2['free_bet_bonus'] && 0 == $vyhra) {
					try {
						$data = array('vybral' => 0);
						$db->update('ticket_bonus_uzivatel', $data, 'ticket_bonus_id=1 AND user_id=' . $h2['user_id']);
					}
					catch (Exception $e) {
						throw new Exception('Nepodarilo se provest dotaz: update ticket_bonus_uzivatel', 0, $e);
					}
				}

				try {
					$data = array('win_real' => $vyhra, 'rate_real' => $h2['helper']->rate,'mp_win_amount' => $h2['helper']->mpWinAmount);
					$db->update('ticket', $data, 'ticket_id=' . $k);
				}
				catch (Exception $e) {
					throw new Exception('Nepodarilo se provest dotaz: update tiketu (real values)', 0, $e);
				}
			} // ticket is loss
			else {
				try{
					$db->update(
						'ticket',
						array('is_loss' => 1, 'win_real' => 0, 'cached_data' => serialize($h2['helper'])),
						array('ticket_id=?' => $k)
					);
				}
				catch (Exception $e) {
					throw new Exception('Ticket loss update failed.', 0, $e);
				}
			}

			try {
				$ws->TicketStatistics->payingout($k);
			}
			catch (Exception $e) {
				throw new Exception('Ticket statistics writing failed.', 0, $e);
			}
			$h2['helper']->paidOut = 1;
			$h2['helper']->payoutTime = $payoutTs;

			try {
				$time = It6_Date::dbDatetimeToTimestamp($h2['zalozen']);
				$ws->Campaign->addTicketToEntryBonusBalance($h2['user_id'], $h2['helper'], false, $time);
			}
			catch (Exception $e) {
				throw new Exception('Ticket not added to entry bonus balance.', 0, $e);
			}

			try {
				foreach ($ticketGames as $game) {
					if ($ws->Campaign->useTicketInGame($h2['helper'], $game)) {
						$gameTickets[$game['gameId']][] = $k;
						$gameHandlers[$game['gameId']] = $game['handlerClass'];
					}
				}
			}
			catch (Exception $e) {
				throw new Exception('Ticket game handler failed', 0, $e);
			}

			It6_DbTransaction::commit($dbAdmin);
			$dbAdminCommited = true;
			It6_DbTransaction::commit($db);
		}
		catch (Exception $e) {
			if (!empty($dbAdminCommited))
				It6_Log::emerg('Partial rollback', It6_Log::TAG_ADMIN_OPERATION, null, $e);
			else
				It6_DbTransaction::rollback($dbAdmin);
			It6_DbTransaction::rollback($db);
			It6_Log::notice(
				"Can't payout ticket '%ticketId%': %message%",
				It6_Log::TAG_ADMIN_OPERATION,
				array('ticketId' => $k, 'message' => $e->getMessage()));
			It6_Log::err($e);
			$failedTickets[$k] = $e->getMessage();
		}
	} // vyplaceni tiketu

	// invalidating ticket game related resources
	It6_GlobalCache_Invalidator::Campaign_ticketGame($gameTickets, $gameHandlers);

	$fnFilter = function ($ticketId) use ($ticket) { return !empty($ticket[$ticketId]['mail']); };
	foreach ($userTickets as $userId => $ticketIds) {
		$ids = array_filter($ticketIds, $fnFilter);
		if (!empty($ids))
			It6_Models_CronJob::scheduleBetResultEmail($ids);
	}
	
	$smsFilter = function ($ticketId) use ($ticket) { return !empty($ticket[$ticketId]['sms']); };
	foreach ($userTickets as $userId => $ticketIds) {
		$ids = array_filter($ticketIds, $smsFilter);
		if (!empty($ids))
			It6_Models_CronJob::scheduleBetResultSms($ids);
	}

	if (!empty($ticket))
		It6_Log::info(
			"Tickets '%tickets%' were paid out.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('tickets' => implode(';', $ticket_id))
		);

	//zavolam cleanup metody pro handlery ticket games
	/* foreach ($gameHandlers as $gameId => $handler_class) {
		$handler = new $handler_class($ticketGames[$gameId]);
		if (method_exists($handler, 'cleanUp'))
			$handler->cleanUp($db);
	} */

	$remainingTickets = $this->getTicketsForPayout(array('ticket_id'), $db);
	if(!empty($remainingTickets)) {
		$this->firstScreen(true);
	}
	$data = array(
		'warnings' => $this->warnings,
		'tickets' => $ticket,
		'isLastBatch' => (empty($remainingTickets) ? true : false),
		'failedTickets' => $failedTicket,
		'sectionId' => $this->section
	);
	$this->vrat = Utils::processTemplate('Template/ProplatitTicket/prove-ticket.phtml', $data, true);
}

/**
 * priprava k proplaceni tiketu
 * @return void
 */
private function PrepareTicket() {
	$ticket =  array();
	$db = Zend_Registry::get('zdb_game');
	It6_DbTransaction::begin($db);

	$kurz_ob = new Finance();
	$cols = array(
		'tp.ticket_id', 'tp.ticket_handle', 'tp.type', 'tp.sazka_id', 'tp.zalozen', 'tp.zruseno', 'tp.kurz',
		'tp.proplacena', 'tp.system', 'tp.banker', 'tp.user_id', 'tp.castka', 'tp.castka_body',
		'tp.ticket_sazka_zrusena', 'tp.status', 'tp.vysledek', 'tp.sloupec_id', 'tp.group_id',
		'tp.point_type_id','tp.rate_advance','u.mena_id', 'u.nick', 'u.user_id'
	);
	$ticketsRaw = $this->getTicketsForPayout($cols, $db);
	foreach($ticketsRaw as $row) {

		$betCanceled = ($row['zruseno'] == 1 || $row['ticket_sazka_zrusena'] == 1 || $row['status'] == 1);

		if (!isset($ticket[$row['ticket_id']])) {

			//TODO: port Finance::selectKurz() to Zend and port all calls of this function
			$res2 = $kurz_ob->selectKurz("WHERE platny_od<=NOW() AND platny_do>=NOW() AND mena_id=".$row['mena_id']);
			if ($row2 = &$res2->fetchRow()) {
				$kurz = $row2['kurz'];
			}	else {
				It6_DbTransaction::rollback($db);
				throw new ExHandler('Nepodarilo se najit kurz',"admin_ex_db");
			}

			$ticket[$row['ticket_id']] = array(
				'ticket_id' => $row['ticket_id'],
				'handle' => $row['ticket_handle'],
				'zruseno' => $row['zruseno'],
				'nick' => $row['nick'],
				'mena_id' => $row['mena_id'],
				'mena_kurz' => $kurz,
				'castka_eur' => ($row['castka']/$kurz),
				'castka' => $row['castka'],
				'vyhra' => 0,
				'kurz_celkem' => 1,
				'individual_delete' => 0,
				'system' => $row['system'],
				'banker_num' => 0,
				'banker_rate' => 1,
				'zalozen' => $row['zalozen'],
				'system_ar' => array(),
				'helperData' => array(
					'ticket_id' => $row['ticket_id'],
					'userId' => $row['user_id'],
					'type' => $row['type'],
					'totalSum' => $row['castka'],
					'totalSumInPoints' => empty($row['castka_body']) ? null : $row['castka_body'],
					'pointType' => empty($row['point_type_id']) ? null : $row['point_type_id'],
					'rateAdvance' => empty($row['rate_advance']) ? null : $row['rate_advance'],
					'bet' => array(),
				),
			);

			$system_status_ticket[$row['ticket_id']] = 0;
		}
		$ticket[$row['ticket_id']]['helperData']['bet'][$row['sazka_id']] = array(
			'sazka_id' => $row['sazka_id'], 'id_col' => $row['sloupec_id'], 'amount' => $row['castka'], 'rate' => $row['kurz'],
			'group' => $row['group_id'], 'vysledek' => $row['vysledek'], 'canceled' => $betCanceled, 'paidOut' => $row['proplacena'],
		);

		$ticket[$row['ticket_id']]['sazka'][$row['sazka_id']]['ticket_sazka_zrusena'] = $row['ticket_sazka_zrusena'];
		$ticket[$row['ticket_id']]['sazka'][$row['sazka_id']]['sloupec_id'] = $row['sloupec_id'];
		$ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['banker'] = $row['banker'];
		$ticket[$row['ticket_id']]['sazka'][$row['sazka_id']]['vysledek'] = explode(";", $row['vysledek']);
		if ($ticket[$row['ticket_id']]['sazka'][$row['sazka_id']]['vysledek'][0] == "")
			$ticket[$row['ticket_id']]['sazka'][$row['sazka_id']]['vysledek'] = array();
		$ticket[$row['ticket_id']]['sazka'][$row['sazka_id']]['status'] = $row['status'];
		$ticket[$row['ticket_id']]['sazka'][$row['sazka_id']]['kurz'] = $row['kurz'];
		//$ticket[$row['ticket_id']]['sazka'][$row['sazka_id']]['kurz'] = ($betCanceled ? 1.0 : $row['kurz']);
		$ticket[$row['ticket_id']]['sazka'][$row['sazka_id']]['proplacena'] = $row['proplacena'];

		if ($row['status'] != 1 && $row['ticket_sazka_zrusena'] != 1) {
			$ticket[$row['ticket_id']]['kurz_celkem'] *= $row['kurz'];
		}

		if ($row['ticket_sazka_zrusena'] == 1 && $row['zruseno'] == 0)
			++$ticket[$row['ticket_id']]['individual_delete'];
	}

	It6_DbTransaction::commit($db);

	foreach ($ticket as $id => $data) {
		$ticket[$id]['helper'] = new It6_Models_Ticket($ticket[$id]['helperData'], It6_Models_Ticket::DATA_ADMIN_TICKET, $data['mena_id'], $db);
		$ticket[$id]['helper']->computeAggregates();
	}

	$ticket_zruseno = $vyhra_pocet = $prohra_pocet = $vyhra_celkem = $prohra_celkem = 0;

	foreach ($ticket as $ticketId => $h) {

		if ($h['zruseno'] == 1) {
			//zruseny tiket
			$ticket[$ticketId]['stav'] = "Zrušený tiket";
			$ticket_zruseno++;
			$vyhra_celkem += $h['castka_eur'];
			continue;
		}
		if ($ticket[$ticketId]['individual_delete'] == count($ticket[$ticketId]['sazka'])) {
			 //vsechny sazky individualne zruseny
			$ticket[$ticketId]['stav'] = "Zrušený tiket";
			$ticket_zruseno++;
			$vyhra_celkem += $h['castka_eur'];
			continue;
		}

		$ticketWins = (It6_Models_Ticket::RESULT_WON == $h['helper']->result);

		$helper = &$h['helper'];
		if ($helper->isMaxicombinatorCompatible()) {
			$prohra = 0.0;
			foreach ($helper->combinations as $ck => $comb) {
				foreach ($comb['data'] as $data) {
					if (It6_Models_Ticket::RESULT_WON == $data['resultValue'])
						$prohra += $data['win'];
				}
			}
			if (It6_Models_Ticket::RESULT_WON == $helper->result) {
				++$prohra_pocet;
				$prohraEur = $prohra / $h['mena_kurz'];
				$prohra_celkem += $prohraEur;
				$this->loadTopTickets($ticketId, -1 * $prohraEur);
			}
			else {
				++$vyhra_pocet;
				$vyhra_celkem += $h['castka_eur'];
				$this->loadTopTickets($ticketId, $h['castka_eur']);
			}
		}
		else {
			if (It6_Models_Ticket::RESULT_WON == $helper->result) {
				$ticket[$ticketId]['stav'] = "Prohra";
				++$prohra_pocet;
				$prohra_celkem += $h['castka_eur'] * $helper->rate;
				$this->loadTopTickets($ticketId, -1 * $h['castka_eur'] * $helper->rate);
			}
			else {
				$ticket[$ticketId]['stav'] = "Výhra";
				++$vyhra_pocet;
				$vyhra_celkem += $h['castka_eur'];
				$this->loadTopTickets($ticketId, $h['castka_eur']);
			}
		}
	}

	$detailTicketId = intval($_POST['detailTicketId']);
	if(!in_array($detailTicketId, array_keys($ticket))) {
		$detailTicketId = null;
	}
	
	$data = array(
		'sectionId' => $this->section,
		'tickets' => $ticket,
		'cancelTicketCount' => $ticket_zruseno,
		'lossTicketCount' => $prohra_pocet,
		'winTicketCount' => $vyhra_pocet,
		'detailTicketId' => $detailTicketId,
		'winAmount' => $vyhra_celkem, //prohra_celkem from bewas point of view
		'lossAmount' => $prohra_celkem, //vyhra_celkem from bewas point of view
		'topWinTickets' => $this->prodelalo_ar, //prodelalo_ar from bewas point of view
		'topLossTickets' => $this->vydelalo_ar, //vydelalo_ar from bewas point of view
	);
	$this->vrat .= Utils::processTemplate('Template/ProplatitTicket/prepare-ticket.phtml', $data, true);
}

/**
 * povoli sazky k proplaceni
 * @return void
 */
private function ProvePayment() {
	$db = Zend_Registry::get('zdb_game');
	It6_DbTransaction::begin($db);
	
	try {
		$bets = $this->getBetsForPayout(array('betId', 'status'), $db);
		foreach($bets as $bet) {
			$sazky[] = $bet['betId'];
		}

		if(!empty($sazky)) {
			$data = array(
				'proplacena' => 1,
				'proplatil_bookmaker' => $_SESSION['bookmaker']
			);
			$updatedBetCount = $db->update('sazky', $data, array('sazka_id IN (?)' => $sazky));
	
			$sql =
				'INSERT INTO `vic_main`.`sazka_kombinace_archive` (`archived_on`, `sazka1_id`, `sazka2_id`, `kombinace_ticket`, `kombinace_show`, `update_platna_do`)
					(
						SELECT NOW(),`sazka1_id`, `sazka2_id`, `kombinace_ticket`, `kombinace_show`, `update_platna_do`
						FROM `vic_main`.`sazka_kombinace`
						WHERE sazka1_id IN (?)
						OR sazka2_id IN (?)
					)';
			$sql = $db->quoteInto($sql, $sazky);
			$sql = $db->quoteInto($sql, $sazky);
			$db->query($sql);

			$where = $db->quoteInto('sazka1_id IN (?) OR sazka2_id IN (?)', $sazky);
			$where = $db->quoteInto($where, $sazky);
			$db->delete('sazka_kombinace', $where);
			
			$betsString = '#'.implode("; #",$sazky);
			$this->vrat .= UiUtil::printMessages(
				I18n::tr('The tickets containing bet(s) {0} are ready to be paid out.', $betsString)
			);
			It6_Log::info(
				"Bets '%bets%' were paid out.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('bets' => $betsString)
			);
		}
		else
			$this->vrat .= UiUtil::printWarnings('no_bets_to_pay_out', TRUE);

		It6_DbTransaction::commit($db);
	}
	
	catch (Exception $e) {
		It6_DbTransaction::rollback($db);
		throw new ExHandler(
			'Nepodarilo se provest dotaz: update sazek: '.$e->getMessage(),
			'admin_ex_db'
		);
	}

	$this->firstScreen(true);
}

/**
* metoda vypise info o sazkach k proplaceni
* @return void
*/
private function PreparePayment() {
	$db = Zend_Registry::get('zdb_game');
	It6_DbTransaction::begin($db);

	$sazky = $sazky_status = $ticket_castka_sazka = $sys_ticket = $queriedBetTickets = array();
	$zruseno = $spravne = $spatne = $prodelalo = $vydelalo = 0;
	
	$queriedBetId = intval($_POST['detailBetId']);
	if(empty($queriedBetId)) {
		$queriedBetId = null;
	}

	$bets = $this->getBetsForPayout(array('betId', 'status'), $db);
	foreach($bets as $bet) {
		if($bet['status'] == 1) {
			$zruseno++;
		}
		$sazky['id'][] = $bet['betId'];
		$sazky_status[$bet['betId']] = $bet['status'];
		$sazky['ti'][$bet['betId']]['spravne'] = 0;
		$sazky['ti'][$bet['betId']]['spatne'] = 0;
		$sazky['ti'][$bet['betId']]['vydelalo'] = 0;
		$sazky['ti'][$bet['betId']]['prodelalo'] = 0;
	}

	try {
		$res = $db->select()
			->from('ticket_pohled',
				array('ticket_id', 'type', 'system', 'banker', 'user_id', 'castka', 'castka_body', 'zalozen', 'sazka_id', 'sloupec_id', 'kurz', 'ticket_sazka_zrusena', 'zruseno', 'status', 'group_id','point_type_id','rate_advance')
			)
			->where('vyplacen=0')
			->query();
	}
	catch (Exception $e) {
		$db->rollBack();
		throw new ExHandler('Nepodarilo se provest dotaz: vyber ticket_pohled',"admin_ex_db"); //TODO: localize
	}

	while ($row = $res->fetch()) {
		$betCanceled = (1 == $row['status'] || 1 == $row['zruseno'] || 1 == $row['ticket_sazka_zrusena']);
		if ($row['ticket_sazka_zrusena'] != 1 && $row['status'] != 1) {
			if(!isset($ticket_castka_sazka[$row['ticket_id']])) {
				$ticket_castka_sazka[$row['ticket_id']] = 1;
			}
			else {
				$ticket_castka_sazka[$row['ticket_id']]++;
			}
		}

		if (It6_Models_Ticket::isTypeMaxicombinatorCompatible($row['type'])) {
			$ticketId = $row['ticket_id'];

			if (!array_key_exists($ticketId, $sys_ticket)) {
				$sys_ticket[$ticketId] = array();
				$ticket = &$sys_ticket[$ticketId];
				$ticket['ticket_id'] = $row['ticket_id'];
				$ticket['userId'] = $row['user_id'];
				$ticket['type'] = $row['type'];
				$ticket['totalSum'] = $row['castka'];
				$ticket['totalSumInPoints'] = empty($row['castka_body']) ? null : $row['castka_body'];
				$ticket['pointType'] = empty($row['point_type_id']) ? null : $row['point_type_id'];
				$ticket['rateAdvance'] = empty($row['rate_advance']) ? null : $row['rate_advance'];
				$ticket['bet'] = array();
			}
			else
				$ticket = &$sys_ticket[$ticketId];

			$ticket['bet'][$row['sazka_id']] = array(
				'sazka_id' => $row['sazka_id'],
				'id_col' => $row['sloupec_id'],
				'rate' => $row['kurz'],
				'group' => $row['group_id'],
				'canceled' => $betCanceled
			);
		}

		if ( $queriedBetId && ($row['sazka_id'] == $queriedBetId) ) {
			$ticketId = $row['ticket_id'];
			if (!array_key_exists($ticketId, $queriedBetTickets)) {
				$queriedBetTickets[$ticketId] = array('id' => $ticketId, 'created' => $row['zalozen']);
			}
		}
	} //while fetchRow
	unset($ticket);

	foreach ($sys_ticket as $id => $data) {
		$sys_ticket[$id]['helper'] = new It6_Models_Ticket($data, It6_Models_Ticket::DATA_ADMIN_TICKET, 'user', $db);
		$helper = &$sys_ticket[$id]['helper'];
		$helper->computeAggregates();
	}

	if(isset($sazky['id']) && count($sazky['id']) > 0) {
		$sql = 'SELECT b.ticket_id, b.sazka_id, castka, kurz, sloupec_id, vysledek, mena_id, ticket_sazka_zrusena, system, type'
			. ' FROM ticket_pohled b INNER JOIN uzivatel c ON b.user_id = c.user_id'
			. ' WHERE b.sazka_id IN (' . implode(',', $sazky['id']) . ')';

		try {
			$res = $db->query($sql);
		}
		catch (Exception $e) {
			$db->rollBack();
			throw new ExHandler('Nepodarilo se provest dotaz: vyber ticket_pohled',"admin_ex_db"); //TODO: localize
		}
		$ticket = array();
		$kurz_ob = new Finance();

		while ($row = $res->fetch()) {
			//TODO: find all calls of Finance::selectKurz() and port those to Zend
			//BEGIN: to be ported to Zend
			$res2 = $kurz_ob->selectKurz("WHERE platny_od<=NOW() AND platny_do>=NOW() AND mena_id=".$row['mena_id']);

			if ($row2 =& $res2->fetchRow()) {
				$kurz = $row2['kurz'];
			} else {
				It6_DbTransaction::rollback($db);
				throw new ExHandler('Nepodarilo se najit kurz',"admin_ex_db");
			}
			//END: to be ported to Zend

			if ( isset($sazky['ti'][$row['sazka_id']])
					&& $sazky_status[$row['sazka_id']] != 1
					&& $row['ticket_sazka_zrusena'] != 1
					&& isset($ticket_castka_sazka[$row['ticket_id']]) ) {

				$row['vysledek'] = explode(";",$row['vysledek']);

				if (in_array($row['sloupec_id'], $row['vysledek'])) {
					$keyHit = 'spravne';
					$keyWin = 'prodelalo';
					$currencyRate = $row['kurz'];
				}
				else {
					$keyHit = 'spatne';
					$keyWin = 'vydelalo';
					$currencyRate = 1.0;
				}
				$sazky['ti'][$row['sazka_id']][$keyHit]++;
				if (It6_Models_Ticket::isTypeMaxicombinatorCompatible($row['type'])) {
					$helper = &$sys_ticket[$row['ticket_id']]['helper'];
					$sazky['ti'][$row['sazka_id']][$keyWin] += ($helper->bets[$row['sazka_id']]['riskAmount'] / $kurz) * $currencyRate;
				}
				else
					$sazky['ti'][$row['sazka_id']][$keyWin] += (($row['castka'] / $ticket_castka_sazka[$row['ticket_id']]) / $kurz) * $currencyRate;
			}
		} //while
	} //pokud jsou nejake sazky k vyplaceni

	if (isset($sazky['id'])) {
		foreach ($sazky['id'] as $k => $h) {
			$prodelalo += $sazky['ti'][$h]['prodelalo'];
			$vydelalo  += $sazky['ti'][$h]['vydelalo'];
			$spravne   += $sazky['ti'][$h]['spravne'];
			$spatne    += $sazky['ti'][$h]['spatne'];
			$this->loadTopTickets($h,($sazky['ti'][$h]['vydelalo']-$sazky['ti'][$h]['prodelalo']));
		}
	}

	It6_DbTransaction::commit($db);

	$data = array(
		'sectionId' => $this->section,
		'topLossBets' => $this->vydelalo_ar,
		'topWinBets' => $this->prodelalo_ar,
		'bets' => $sazky,
		'queriedBetId' => $queriedBetId,
		'queriedBetTickets' => $queriedBetTickets,
		'canceledBetCount' => $zruseno,
		'winBetCount' => $spravne, //spravne from bewa point of view
		'lossBetCount' => $spatne, //spatne from bewa point of view
		'winAmount' => $prodelalo, //prodelalo from bewa point of view
		'lossAmount' => $vydelalo, //vydelalo from bewa point of view
		'betStatuses' => $sazky_status
	);
	$this->vrat .=  Utils::processTemplate('Template/ProplatitTicket/prepare-payment.phtml', $data, true);
}

	/**
	 * Loads data into an ordered list of top win top loss entities (bets or tickets).
	 * @param int $id id sazky nebo tiketu
	 * @param int $sum rozdil mezi tim co sazka/tiket vydelala a prodela
	 * @return void
	 */
	private function loadTopTickets($id, $sum) {
		if ($sum > 0) {
			for ($x=0; $x < $this->topShowCount; $x++) {
				if (!isset($this->vydelalo_ar[$x])) {
					$this->vydelalo_ar[$x]['suma'] = $sum;
					$this->vydelalo_ar[$x]['id'] = $id;
					break;
				}
				$help_sum = $this->vydelalo_ar[$x]['suma'];
				$help_sazka = $this->vydelalo_ar[$x]['id'];
				if ($sum > $this->vydelalo_ar[$x]['suma']) {
					$this->vydelalo_ar[$x]['suma'] = $sum;
					$this->vydelalo_ar[$x]['id'] = $id;
					$sum = $help_sum;
					$id = $help_sazka;
				}
			}
		}
		else if ($sum < 0) {
			$sum *= -1;
			for ($x=0; $x < $this->topShowCount; $x++) {
				if (!isset($this->prodelalo_ar[$x])) {
					$this->prodelalo_ar[$x]['suma'] = $sum;
					$this->prodelalo_ar[$x]['id'] = $id;
					break;
				}
				$help_sum = $this->prodelalo_ar[$x]['suma'];
				$help_sazka = $this->prodelalo_ar[$x]['id'];
				if ($sum > $this->prodelalo_ar[$x]['suma']) {
					$this->prodelalo_ar[$x]['suma'] = $sum;
					$this->prodelalo_ar[$x]['id'] = $id;
					$sum = $help_sum;
					$id = $help_sazka;
				}
			}
		}
	}

	/**
	 * Shows the initial screen
	 * @param boolean $hideBetPayout Weather to hide the button that allows to run bet payout
	 * @return void
	 */
	private function firstScreen($hideBetPayout=false) {
		$data = array(
			'sectionId' => $this->section,
			'hideBetPayout' => $hideBetPayout
		);
		$this->vrat .= Utils::processTemplate('Template/ProplatitTicket/first-screen.phtml', $data, true);
	}

	/**
	 * Returns html generated by this class
	 * @return string
	 */
	public function getContent(){
		return $this->vrat;
	}

	/**
	 * Returns bets that are ready for payout
	 * @param array $cols An array of column names to get
	 * @return array An array of structs containing the bet data
	 */
	private function getBetsForPayout(array $cols, $db=null) {
		if($db === null) {
			$db = Zend_Registry::get('zdb_game');
		}
		
		$filter = array(
			array('?' => array('payedOff' => 0)),
			array(
				'OP' => 'OR',
				array('?' => array('verified' => '0'), 'OP' => '<>'),
				array(
					array('?' => array('status' => 1)),
					array('?' => 'validToTime', 'OP' => '< NOW()'),
				)
			)
		);
		$exts = array(
			new It6_WsExtension_Client_Filter('filter', $filter),
			new It6_WsExtension_Client_Columns('columns', $cols)
		);
		$bets = Zend_Registry::get('ws')->ext($exts)->Bet->getAll();
		$bets = It6_ArrayWrapper::tonativeArray($bets);
		
		return $bets;
	}

	/**
	 * Returns tickets that are ready for payout
	 * @param array $cols An array of column names to get
	 * @param object $db Database adapter
	 * @return array An array of structs containing the bet data
	 */
	private function getTicketsForPayout(array $cols, $db=null) {
		if($db === null) {
			$db = Zend_Registry::get('zdb_game');
		}
		$query = $this->getTicketsForPayoutQuery($cols, $db);
		$tickets = $query->query()->fetchAll();
		$tickets = It6_ArrayWrapper::tonativeArray($tickets);

		return $tickets;
	}

	/**
	 * Returns the ids of tickets that are to be paid
	 * @param object $db Database adapter
	 * @return array An array of the ticket ids.
	 */
	private function getPayoutBatchTicketIds($db=null) {
		$query = $this->getTicketsForPayoutQuery('tp.ticket_id', $db);
		$tickets = $query
			->group('tp.ticket_id')
			->limit($this->ticketBatchSize)
			->query()->fetchAll();
		$ticketIds = array();
		foreach($tickets as $ticket) {
			$ticketIds[] = $ticket['ticket_id'];
		}

		return $ticketIds;
	}

	/**
	 * Returns a select statement used as the base for getting tickets for payout
	 * @param array $cols array of strings identifying the columns to be retreived
	 * @param object $db Database adapter
	 * @return Zend_Db_Select
	 */
	private function getTicketsForPayoutQuery($cols, $db) {
		if($db === null) {
			$db = Zend_Registry::get('zdb_game');
		}

		$query = $db->select()
			->from(
				array('tp' => 'ticket_pohled'),
				$cols
			)
			->join(
				array('u' => 'uzivatel'),
				'tp.user_id=u.user_id'
			)
			->where('tp.vyplacen=0')
			->where('tp.zruseno <> 1')
			->where(
				'tp.ticket_id NOT IN (
					SELECT DISTINCT tp2.ticket_id
					FROM ticket_pohled tp2
					WHERE tp2.vyplacen=1
						OR tp2.proplacena=0
				)'
			);

		return $query;
	}

	//commented out by Martin 6.9.2012
	//this method is not used now, emails are handled by CronJob
	/**
	* Odesle email vitezum
	* @return string
	*/
  	/*
	public function sendEmailToWinners($user_ticket) {
	//TODO tuhle silenost prekopat do sablony a samostatne notif tridy
	#Poslani mailu s vysledky#
		#Do spatnych se nepocitaji zrusene tikety a tikety, ktere maji vsechny individualne zrusene sazky#
		$r 		= array();
		$m 		= array();
		$u 		= array();
		$typ 	= array();


		foreach ($user_ticket as $k => $h ) {

			if(!isset($r[$h['lang']])) {

				//tiket prohra
				$r[$h['lang']][1] = $this->preklad->FindPreklad('t_lose',$h['lang']);
				$r[$h['lang']][1] = $r[$h['lang']][1][$h['lang']];

				//tiket výhra
				$r[$h['lang']][2] = $this->preklad->FindPreklad('t_win',$h['lang']);
				$r[$h['lang']][2] = $r[$h['lang']][2][$h['lang']];

				//zruseny tiket
				$r[$h['lang']][3] = $this->preklad->FindPreklad('t_delete',$h['lang']);
				$r[$h['lang']][3] = $r[$h['lang']][3][$h['lang']];

				//tiket vyplacen v kurzu 1
				$r[$h['lang']][4] = $this->preklad->FindPreklad('t_rate_1',$h['lang']);
				$r[$h['lang']][4] = $r[$h['lang']][4][$h['lang']];

				//kurz
				$r[$h['lang']][7] = $this->preklad->FindPreklad('bet_kurz',$h['lang']);
				$r[$h['lang']][7] = $r[$h['lang']][7][$h['lang']];

				//kurz celkem
				$r[$h['lang']][8] = $this->preklad->FindPreklad('t_rate_sum',$h['lang']);
				$r[$h['lang']][8] = $r[$h['lang']][8][$h['lang']];

				//vyhra
				$r[$h['lang']][9] = $this->preklad->FindPreklad('t_win_sum',$h['lang']);
				$r[$h['lang']][9] = $r[$h['lang']][9][$h['lang']];

				//zrusena sazka
				$r[$h['lang']][10] = $this->preklad->FindPreklad('t_delete_bet',$h['lang']);
				$r[$h['lang']][10] = $r[$h['lang']][10][$h['lang']];

				//vklad
				$r[$h['lang']][11] = $this->preklad->FindPreklad('vv_vklad',$h['lang']);
				$r[$h['lang']][11] = $r[$h['lang']][11][$h['lang']];

				//free bet bonus
				$r[$h['lang']][13] = $this->preklad->FindPreklad('free_bet_bonus_mail',$h['lang']);
				$r[$h['lang']][13] = $r[$h['lang']][13][$h['lang']];

				//tiket
				$r[$h['lang']][12] = $this->preklad->FindPreklad('bet_ticket',$h['lang']);
				$r[$h['lang']][12] = $r[$h['lang']][12][$h['lang']];

				//predmet
				$m[$h['lang']][5] = $this->preklad->FindPreklad('mail_subject_ticket',$h['lang']);
				$m[$h['lang']][5] = $m[$h['lang']][5][$h['lang']];

				//paticka
				$m[$h['lang']][6] = $this->preklad->FindPreklad('mail_footer',$h['lang']);
				$m[$h['lang']][6] = $m[$h['lang']][6][$h['lang']];

				//sazky
				$u[$h['lang']][1] = $this->preklad->FindPreklad('tit_bet',$h['lang']);
				$u[$h['lang']][1] = $u[$h['lang']][1][$h['lang']];

				//kasino
				$u[$h['lang']][2] = $this->preklad->FindPreklad('tit_casino',$h['lang']);
				$u[$h['lang']][2] = $u[$h['lang']][2][$h['lang']];

				//hry
				$u[$h['lang']][3] = $this->preklad->FindPreklad('tit_games',$h['lang']);
				$u[$h['lang']][3] = $u[$h['lang']][3][$h['lang']];

				//udalost
				$u[$h['lang']][4] = $this->preklad->FindPreklad('event',$h['lang']);
				$u[$h['lang']][4] = $u[$h['lang']][4][$h['lang']];

				//ti
				$u[$h['lang']][5] = $this->preklad->FindPreklad('event_tip',$h['lang']);
				$u[$h['lang']][5] = $u[$h['lang']][5][$h['lang']];

				//vysledek
				$u[$h['lang']][6] = $this->preklad->FindPreklad('event_result',$h['lang']);
				$u[$h['lang']][6] = $u[$h['lang']][6][$h['lang']];

				//druh sazky
				$u[$h['lang']][7] = $this->preklad->FindPreklad('event_type_bet',$h['lang']);
				$u[$h['lang']][7] = $u[$h['lang']][7][$h['lang']];

				//datum
				$u[$h['lang']][8] = $this->preklad->FindPreklad('datum',$h['lang']);
				$u[$h['lang']][8] = $u[$h['lang']][8][$h['lang']];

				//vklad
				$u[$h['lang']][9] = $this->preklad->FindPreklad('conto_vklad',$h['lang']);
				$u[$h['lang']][9] = $u[$h['lang']][9][$h['lang']];

				//kombinovana
				$u[$h['lang']][10] = $this->preklad->FindPreklad('bet_t_combi',$h['lang']);
				$u[$h['lang']][10] = $u[$h['lang']][10][$h['lang']];

				//jednoducha
				$u[$h['lang']][11] = $this->preklad->FindPreklad('bet_t_simple',$h['lang']);
				$u[$h['lang']][11] = $u[$h['lang']][11][$h['lang']];

				//oblibene sazky
				$u[$h['lang']][12] = $this->preklad->FindPreklad('favorite_bet',$h['lang']);
				$u[$h['lang']][12] = $u[$h['lang']][12][$h['lang']];

				//text k oblibene sazky
				$u[$h['lang']][13] = $this->preklad->FindPreklad('mail_favorite_info',$h['lang']);
				$u[$h['lang']][13] = $u[$h['lang']][13][$h['lang']];

				//uzavreni sazek
				$u[$h['lang']][14] = $this->preklad->FindPreklad('bet_close',$h['lang']);
				$u[$h['lang']][14] = $u[$h['lang']][14][$h['lang']];

				//System
				$u[$h['lang']][15] = $this->preklad->FindPreklad('bet_t_system1',$h['lang']);
				$u[$h['lang']][15] = $u[$h['lang']][15][$h['lang']];

				//Banker
				$u[$h['lang']][16] = $this->preklad->FindPreklad('bet_t_banker',$h['lang']);
				$u[$h['lang']][16] = $u[$h['lang']][16][$h['lang']];

				//Zruseny
				$u[$h['lang']][17] = $this->preklad->FindPreklad('ti_st_zruseny',$h['lang']);
				$u[$h['lang']][17] = $u[$h['lang']][17][$h['lang']];
			}

			$pocet_tiketu = count($h['ticket']);
			$pocet_spatnych = (isset($h['ticket_status'])?count($h['ticket_status']):0);
			//1-same vyhrane;2-same prohrane;3-same zrusene;4-vyhrane a prohranne nebo zrusene
			$stav = 1;

			$mail_body = '
				<div id="topanel" style="width:100%;height:80px;background-color:#000000;">
					<a href="'.WEBHOST.'">
						<img src="logo.jpg" style="float:left;margin:0px 0px 0px 0px;border:0px;" alt="'.WEBHOST.'" />
					</a>
					<div id="main_sec" style="height:32px;_height:80px;_he\ight:32px;float:right;width:270px;padding:48px 0px 0px 0px;margin:0px 10px 0px 0px;clear:right;">
						<a href="'.WEBHOST.'" target="_blank" style="width:88px;height:15px;text-align:center;font-size:12px;font-weight:500;float:left;color:white;text-decoration:none;margin-right:2px;padding:9px 0px 8px 0px;background:#BE4040;">
							'.Help::Html(mb_strtoupper($u[$h['lang']][1],'UTF-8')).'
						</a>
						<a href="'.WEBHOST.'/" target="_blank" style="width:88px;height:15px;text-align:center;font-size:12px;font-weight:500;float:left;color:white;text-decoration:none;margin-right:2px;padding:9px 0px 8px 0px;background:#EC6840;">
							'.Help::Html(mb_strtoupper($u[$h['lang']][2],'UTF-8')).'
						</a>
						<a href="'.WEBHOST.'/" target="_blank" style="width:88px;height:15px;text-align:center;font-size:12px;font-weight:500;float:left;color:white;text-decoration:none;margin-right:2px;padding:9px 0px 8px 0px;background:#EFAE40;">
							'.Help::Html(mb_strtoupper($u[$h['lang']][3],'UTF-8')).'
						</a>
					</div>
				</div>
				<div style="width:auto;margin:10px 10px 0px 10px;">
					<h1 style="font-size:1.35em;color:#3B3B3B;padding:10px 0px 10px 0px;margin:0px 0px 10px 0px;">
						'.Help::Html(mb_strtoupper($u[$h['lang']][1],'UTF-8')).'
					</h1>
			';

		$send_mail = false;

		foreach($h['ticket'] as $h2){  //$h2 ticket_id
			//if($ticket[$h2]['consistenci_error'] == 1 || $ticket[$h2]['mail'] == 0) continue;
			if($ticket[$h2]['consistence_error'] == 1 || $ticket[$h2]['mail'] == 0) continue;
			$send_mail = true;
			$pocet_sazek = count($ticket[$h2]['sazka']);

			if($ticket[$h2]['banker_num'] > 0)
				$banker = $ticket[$h2]['banker_num'].' '.Help::Html($u[$h['lang']][16]).' + ';
			else
				$banker='';

			$mail_body .= '
				<table style="width:800px;border-collapse:collapse;margin:0px 0px 20px 0px;">
					<tr>
						<th style="width:180px;text-align:center;border-right:1px solid #E5E8EC;background:#D11704;padding:2px 10px;color:white;font-size:12px;font-weight: normal;">'.Help::Html(ucfirst($u[$h['lang']][4])).'</th>
						<th style="width:80px;text-align:center;border-right:1px solid #E5E8EC;background:#D11704;padding:2px 10px;color:white;font-size:12px;font-weight: normal;">'.Help::Html(ucfirst($u[$h['lang']][1])).'</th>
						<th style="width:80px;text-align:center;border-right:1px solid #E5E8EC;background:#D11704;padding:2px 10px;color:white;font-size:12px;font-weight: normal;">'.Help::Html(ucfirst($u[$h['lang']][5])).'</th>
						<th style="width:80px;text-align:center;border-right:1px solid #E5E8EC;background:#D11704;padding:2px 10px;color:white;font-size:12px;font-weight: normal;">'.Help::Html(ucfirst($u[$h['lang']][6])).'</th>
						<th style="width:80px;text-align:center;border-right:1px solid #E5E8EC;background:#D11704;padding:2px 10px;color:white;font-size:12px;font-weight: normal;border-width:0px;" >'.Help::Html(ucfirst($r[$h['lang']][7])).'</th>
						<td rowspan="'.($pocet_sazek+1).'"  valign="top" style="border-right:1px solid #E5E8EC;padding:4px 8px;font-size:12px;text-align:center;border-width:0px;">
							<table style="width:300px;border-collapse:collapse;margin:0px 10px;">
								<tr><th style="text-align:left;border:0px;border-bottom: 1px dotted #E5E8EC;background:white;padding:2px 10px;color:#333333;font-size:12px;font-weight: bold;">'.Help::Html(mb_strtoupper($u[$h['lang']][7],'UTF-8')).':</th><td style="padding:4px 8px;border:0px;border-bottom: 1px dotted #E5E8EC;font-size:12px;text-align: left;">'.($ticket[$h2]['system']!=0?$banker.Help::Html($u[$h['lang']][15],'UTF-8').' '.$ticket[$h2]['system_type']:($pocet_sazek>1?Help::Html($u[$h['lang']][10]):Help::Html($u[$h['lang']][11])) ).'</td></tr>
								<tr><th style="text-align:left;border:0px;border-bottom: 1px dotted #E5E8EC;background:white;padding:2px 10px;color:#333333;font-size:12px;font-weight: bold;">'.Help::Html(mb_strtoupper($u[$h['lang']][8],'UTF-8')).':</th><td style="padding:4px 8px;border:0px;border-bottom: 1px dotted #E5E8EC;font-size:12px;text-align: left;">'.It6_Date::fromDb($ticket[$h2]['zalozen']).'</td></tr>
								<tr><th style="text-align:left;border:0px;border-bottom: 1px dotted #E5E8EC;background:white;padding:2px 10px;color:#333333;font-size:12px;font-weight: bold;">'.Help::Html(mb_strtoupper($u[$h['lang']][9],'UTF-8')).':</th><td style="padding:4px 8px;border:0px;border-bottom: 1px dotted #E5E8EC;font-size:12px;text-align: left;">'.$ticket[$h2]['castka'].' '.$ticket[$h2]['mena_text'].'</td></tr>
				'. ($ticket[$h2]['system']!=0?'':'<tr><th style="text-align:left;border:0px;border-bottom: 1px dotted #E5E8EC;background:white;padding:2px 10px;color:#333333;font-size:12px;font-weight: bold;">'.Help::Html(mb_strtoupper($r[$h['lang']][7],'UTF-8')).':</th><td style="padding:4px 8px;border:0px;border-bottom: 1px dotted #E5E8EC;font-size:12px;text-align: left;">'.round($ticket[$h2]['kurz_celkem'],2).'</td></tr>').'
				'.($ticket[$h2]['system']!=0?'':'<tr><th style="text-align:left;border:0px;border-bottom: 1px dotted #E5E8EC;background:white;padding:2px 10px;color:#333333;font-size:12px;font-weight: bold;">'.Help::Html(mb_strtoupper($u[$h['lang']][6],'UTF-8')).':</th><td style="padding:4px 8px;border:0px;border-bottom: 1px dotted #E5E8EC;font-size:12px;text-align: left;">'.($ticket[$h2]['zruseno'] == 1?'<span style="color:#333333;font-weight:bold;">'.$u[$h['lang']][17].'</span>':(isset($user_ticket[$k]['delete_status'][$h2]) || $ticket[$h2]['kurz_celkem'] == 1?'<span style="color:#333333;font-weight:bold;">'.Help::Html($r[$h['lang']][3].' - '.$r[$h['lang']][4]).'</span>':(isset($user_ticket[$k]['ticket_status'][$h2])?'<span style="color:#D11704;	font-weight:bold;">'.Help::Html($r[$h['lang']][1]).'</span>':'<span style="color:#D11704;font-weight:bold;">'.Help::Html($r[$h['lang']][2]).'</span>'))).'</td></tr>');
		if(!isset($user_ticket[$k]['delete_status'][$h2]) && $ticket[$h2]['system']==0 && !isset($user_ticket[$k]['ticket_status'][$h2]))   $mail_body .= '<tr><th style="text-align:left;border:0px;border-bottom: 1px dotted #E5E8EC;background:white;padding:2px 10px;color:#333333;font-size:12px;font-weight: bold;">'.Help::Html(mb_strtoupper($r[$h['lang']][9],'UTF-8')).'</th><td style="padding:4px 8px;border:0px;border-bottom: 1px dotted #E5E8EC;font-size:12px;text-align: left;">'.round(($ticket[$h2]['system_win_real']),2).' '.$ticket[$h2]['mena_text'].'</td></tr>';
		else if($ticket[$h2]['system'] != 0) $mail_body .= '<tr><th style="text-align:left;border:0px;border-bottom: 1px dotted #E5E8EC;background:white;padding:2px 10px;color:#333333;font-size:12px;font-weight: bold;">'.Help::Html(mb_strtoupper($r[$h['lang']][9],'UTF-8')).'</th><td style="padding:4px 8px;border:0px;border-bottom: 1px dotted #E5E8EC;font-size:12px;text-align: left;">'.round(($ticket[$h2]['system_win_real']),2).' '.$ticket[$h2]['mena_text'].'</td></tr>';

		if($ticket[$h2]['free_bet_bonus'] == 1 && !isset($user_ticket[$k]['delete_status'][$h2]) && !isset($user_ticket[$k]['ticket_status'][$h2])) $mail_body .= '<tr><th style="text-align:left;border:0px;border-bottom: 1px dotted #E5E8EC;background:white;padding:2px 10px;color:#333333;font-size:10px;font-weight: normal;" colspan="2">'.Help::Html($r[$h['lang']][13]).'</th></tr>';

		$mail_body .= '</table></td></tr>';


			if($h['delete_individual'][$h2] == count($ticket[$h2]['sazka'])) $user_ticket[$k]['delete_status'][$h2] = 1;

			foreach($ticket[$h2]['sazka'] as $k4=>$h3){

				if($h3['ticket_sazka_zrusena'] == 1 && !isset($h['delete_status'][$h2]))  $vysl = $r[$h['lang']][10];
			else if($h3['status'] == 1 && !isset($h['delete_status'][$h2]))  $vysl = $r[$h['lang']][10];
			else if(!is_array($h3['vysledek']) || count($h3['vysledek']) < 1) $vysl = "-";
			else{

					$sss = "";$sss_help = array();
					foreach($h3['vysledek'] as $vvv){ if($vvv != "" && !in_array($vvv,$sss_help)){$sss .= $sloupec[$vvv][$h['lang']].",";$sss_help[] = $vvv;}}
					if(mb_strlen($sss) == 0) $sss = '-,';
					$vysl = substr($sss,0,-1);

			}
			if(!isset($typ[$h3['typ_id']][$h['lang']])){
			$typ[$h3['typ_id']][$h['lang']] = $this->preklad->FindPreklad($h3['typ_nazev'],$h['lang']); $typ[$h3['typ_id']][$h['lang']] = $typ[$h3['typ_id']][$h['lang']][$h['lang']]; //jednoducha
			}

				$mail_body .= '<tr>
													<td style="border-right:1px solid #E5E8EC;padding:4px 8px;font-size:12px;text-align:center;width:160px;"><a href="'.WEBHOST.'/?type_id='.$h3['typ_id'].'&event_id='.$h3['udalost_id'].'" style="color:#E61F00;font-size:12px;">'.Help::Html(Help::TranslateString($h3['text'],$h['lang'],$this->dbGame)).'</a></td>
													<td style="border-right:1px solid #E5E8EC;padding:4px 8px;font-size:12px;text-align:center;">'.Help::Html($typ[$h3['typ_id']][$h['lang']]).'</td>
													<td style="border-right:1px solid #E5E8EC;padding:4px 8px;font-size:12px;text-align:center;">'.Help::Html($sloupec[$h3['sloupec_id']][$h['lang']]).' </td>
													<td style="border-right:1px solid #E5E8EC;padding:4px 8px;font-size:12px;text-align:center;">'.Help::Html($vysl).'</td>
													<td style="border-right:1px solid #E5E8EC;padding:4px 8px;font-size:12px;text-align:center;border:0px;">'.($ticket[$h2]['system']!=0 && $ticket[$h2]['sazka'][$k4]['banker']==1?'<div style="float:left;padding:0px;margin:0px;"><strong>B</strong></div>':'<div style="float:left;padding:0px;margin:0px;">&nbsp;</div>').''.$h3['kurz'].'</td>';
				$mail_body .= '</tr>';

			}

			$mail_body .= '</table>';
		}

		$popular = '';$x = 1;$y=0;
		foreach($popular_ar as $k4=>$h4){
			$y++;
			if($x == 1)$popular .= '<tr>';
			$popular .= '<td style="padding:4px 8px;border:0px;font-size:12px;text-align: left;"><a href="'.WEBHOST.$h4['seotext'].'" style="color:#E61F00;font-size:12px;">'.Help::Html(Help::TranslateString($h4['text'],$h['lang'],$this->dbGame)).'</a></td>
									<td style="padding:4px 8px;border:0px;font-size:12px;text-align: left;border-right: 1px solid #E5E8EC;">'.$h4['platna_do'].'</td>';
			if($x == 2 || count($popular_ar) <= $y){$popular .= '</tr>';$x=0;}
			$x++;
		}

		$popular_head = '<tr><th style="text-align:left;border:0px;background:white;padding:2px 10px;color:#333333;font-size:12px;font-weight: bold;">'.Help::Html(ucfirst($u[$h['lang']][4])).'</th><th style="text-align:left;border:0px;background:white;padding:2px 10px;color:#333333;font-size:12px;font-weight: bold;">'.Help::Html(ucfirst($u[$h['lang']][14])).'</th><th style="text-align:left;border:0px;background:white;padding:2px 10px;color:#333333;font-size:12px;font-weight: bold;">'.Help::Html(ucfirst($u[$h['lang']][4])).'</th><th style="text-align:left;border:0px;background:white;padding:2px 10px;color:#333333;font-size:12px;font-weight: bold;">'.Help::Html(ucfirst($u[$h['lang']][14])).'</th></tr>';

		$mail_body .= '<hr style="color:#E5E8EC;height:6px;clear:both;" /><h1 style="font-size:1.35em;color:#3B3B3B;padding:10px 0px 10px 0px;margin:0px 0px 10px 0px;">'.Help::Html(mb_strtoupper( $u[$h['lang']][12],'UTF-8')).'</h1>
									<table style="border-collapse:collapse;margin:0px 0px;"><tr><td colspan="4" style="padding:10px 8px;border:0px;font-size:12px;text-align: left;font-weight:bold;background:#EFF1F4;">'.Help::Html(mb_strtoupper($u[$h['lang']][13],'UTF-8')).'</td></tr>
										'.$popular_head.$popular.'
								</table></div><div style="background:#DFE3E8;margin:20px 0px 0px 0px;padding:10px;clear:both;font-size:11px;">'.Help::Html($m[$h['lang']][6]).'</div>';

		$pocet_smazanych = (isset($user_ticket[$k]['delete_status'])?count($user_ticket[$k]['delete_status']):0);

		if($pocet_smazanych == $pocet_tiketu) $stav = 3; //same zrusene tikety
		else if(($pocet_spatnych+$pocet_smazanych) == 0) $stav = 1; //same vyhrane tikety
		else if(($pocet_spatnych+$pocet_smazanych) > 0 && ($pocet_spatnych+$pocet_smazanych) < $pocet_tiketu) $stav = 4; //proherni i vyherni i zrusene tikety
		else if(($pocet_spatnych+$pocet_smazanych) > 0 && ($pocet_spatnych+$pocet_smazanych) == $pocet_tiketu) $stav = 2; //same proherni tikety nebo proherni a zrusene


		$subjectEncoding = 'UTF-8';
		$mail = new htmlMimeMail();
		$mail->addHTMLImage($mail->getFile($_SERVER["DOCUMENT_ROOT"].'/_clip_mail/logo.jpg'),'logo.jpg','image/jpeg');
		$mail->setTextCharset("UTF-8");
		$mail->setHeadCharset("UTF-8");
		$mail->setHTMLCharset("UTF-8");
			$mail->html_charset = "UTF-8";$mail->text_encoding = "UTF-8";
		$mail->setHTMLEncoding("base64");
		$mail->setFrom(INFOMAIL);
		$mail->setReturnPath(INFOMAIL);
			$mail->setHtml(HTMLHEAD.$mail_body.HTMLFOOT);
		$mail->setSubject($m[$h['lang']][5]);

		}
		#Konec Poslani mailu s vysledky#
	}
*/
}
