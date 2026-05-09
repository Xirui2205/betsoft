<?php
/**
 * Trida vraci jiz rozhodnutou a proplacenou sazku zpet do stavu nevyhodnocena a odebira penize z proplacenych tiketu
 * Nakonec posle vsem omluvny mail
 */
 
class VratitSazku{

/**
 * navratova hodnota
 * @access private
 * @var string
 */
private  $vrat = "";

/**
 * pravo zmeny v sekci
 * @access private
 * @var int
 */
private  $update = 0;
/**
 * pravo vymazani v sekci
 * @access private
 * @var int
 */		   
private  $delete = 0;

/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */
private  $dbGame;

/**
 * aktualni sekce
 * @access private
 * @var int
 */ 
private  $section;

/**
 * @param Zend_Db_Adapter $dbGame
 */
public function __construct($dbGame=null){
	$this->dbGame = (isset($dbGame) ? $dbGame : Zend_Registry::get('zdb_game'));
}
  
/**
 * Vraceni sazky zpet do stavu nevyhodnocena a odebrani penez kde to jde
 * @param int $betId id sazky 
 * @return void
 */
public function ReturnBet($betId){
	$bet = $this->dbGame->select()
		->from(
			array('s' => 'sazky'),
			array(
				'id'		=> 'sazka_id',
				'text'		=> 'text',
				'validTo'	=> 'platna_do'
			)
		)
		->join(
			array('u' => 'udalost'),
			's.udalost_id = u.udalost_id',
			array('event' => 'nazev')
		)
		->join(
			array('sp' => 'sport'),
			'u.sport_id = sp.sport_id',
			array('sport' => 'nazev')
		)
		->join(
			array('ob' => 'oblast'),
			'u.oblast_id = ob.oblast_id',
			array('region' => 'nazev')
		)
		->join(
			array('t' => 'typ'),
			's.typ_id = t.typ_id',
			array('type' => 'nazev')
		)
		->where('s.sazka_id = ?', $betId)
		->query()->fetch();
		
	if (empty($bet)) {
		$this->vrat .= UiUtil::printErrors('Tato sázka nemůže být vrácena');
		return;
	}

	$betInfo['id']		= $betId;
	$betInfo['type']	= $bet['type'];
	$betInfo['event']	= $bet['event'];
	$betInfo['text']	= $bet['text'];
	$betInfo['region']	= $bet['region'];
	$betInfo['sport']	= $bet['sport'];

	$stmt = $this->dbGame->select()
		->from(
			array('tk' => 'ticket_kurz'),
			array('sazka_id', 'sloupec_id', 'kurz' => 'rate', 'ticket_sazka_zrusena', 'group_id')
		)
		->join(
			array('t' => 'ticket'),
			't.ticket_id=tk.ticket_id',
			array(
				'ticket_id', 'castka', 'castka_body', 'user_id',
				'type', 'zruseno', 'stats', 'stats_user', 'free_bet_bonus',
				'point_type_id', 'rate_advance', 'mp', 'mp_win',
				'mp_win_amount', 'ticket_handle' => 'handle',
				'cached_data', 'ticket_branch_id' => 'host_id',
				'cash', 'zalozen', 'vyplacen_date', 'win_real',
			)
		)
		->join(
			array('s' => 'sazky'),
			'tk.sazka_id=s.sazka_id',
			array('status', 'proplacena', 'vysledek')
		)
		->join(
			array('u' => 'uzivatel'),
			't.user_id=u.user_id',
			array('mena_id')
		)
		->where('tk.sazka_id=?', $betId)
		->where('t.vyplacen<>0 AND (t.collection_time IS NULL OR NOT t.cash)')
		->query();

	$tickets = array();
	$users = array();
	$currencies = array();
	while ($row = $stmt->fetch()) {
		$ticketId = $row['ticket_id'];
		$currencyId = $row['mena_id'];
		$userId = $row['user_id'];
		$currencyRate = It6_Models_Currency::get($currencyId, 'rate', $this->dbGame);
		if (!array_key_exists($ticketId, $tickets)) {
			$tickets[$ticketId] = array(
				'rate' => 1,
				//'column' => $row['sloupec_id'],
				//'result' => $row['vysledek'],
				'amount' => $row['castka'],
				'amountCc' => $row['castka'] / $currencyRate,
				'currencyId' => $currencyId,
				'currencyRate' => $currencyRate,
				'betCount' => 0,
				'ticketHandle' => $row['ticket_handle'],
				'type' => $row['type'],
				'userId' => $userId,
				'winning' => true,
				'canceled' => $row['zruseno'],
				'canceledBet' => $row['ticket_sazka_zrusena'],
				'freeBet' => $row['free_bet_bonus'],
				'stats' => $row['stats'],
				'statsUser' => $row['stats_user'],
				'cachedData' => $row['cached_data'],
				'host_id' => $row['ticket_branch_id'],
				'createdAt' => $row['zalozen'],
				'mpWinAmount' => $row['mp_win_amount'],
				'paidOutAt' => $row['vyplacen_date'],
				'winReal' => $row['win_real'],
			);
			//IT6:TICKET_CACHE:
			if (true || !empty($row['cached_data'])) {
				$tickets[$ticketId]['helperData'] = array(
					'ticket_id' => $ticketId,
					'userId' => $userId,
					'type' => $row['type'],
					'totalSum' => $row['castka'],
					'totalSumInPoints' => empty($row['castka_body']) ? null : $row['castka_body'],  
					'pointType' => empty($row['point_type_id']) ? null : $row['point_type_id'],
					'rateAdvance' => empty($row['rate_advance']) ? null : $row['rate_advance'],
					'mp' => $row['mp'],
					'mpWin' => $row['mp_win'],
					'mpWinAmount' => $row['mp_win_amount'],
					'cash' => $row['cash'],
					'bet' => array(),
				);
			}
			if (!array_key_exists($userId, $users))
				$users[$userId] = array('tickets' => array());
			$users[$userId]['tickets'][$ticketId] = array();
		}
		$betCanceled = (1 == $row['status'] || 1 == $row['zruseno'] || 0 != $row['ticket_sazka_zrusena']);
		++$tickets[$ticketId]['betCount'];
		if (!empty($tickets[$ticketId]['helperData'])) {
			$tickets[$ticketId]['helperData']['bet'][$row['sazka_id']] = array(
				'sazka_id' => $row['sazka_id'], 'id_col' => $row['sloupec_id'], 'amount' => $row['castka'], 'rate' => $row['kurz'],
				'group' => $row['group_id'], 'vysledek' => $row['vysledek'], 'canceled' => $betCanceled, 'paidOut' => $row['proplacena']
			);
		}
	}

	It6_DbTransaction::begin($this->dbGame);
	try {
		$this->dbGame->update(
			'sazky',
			array(
				'vysledek' => '',
				'status' => 0,
				'proplacena' => 0,
				'overena' => 0,
			),
			array('sazka_id=?' => $betId)
		);

		$this->dbGame->update(
			'ticket_kurz',
			array(
				'ticket_sazka_zrusena' => null,
				'ticket_sazka_duvod_zruseni' => null
			),
			array('sazka_id=?' => $betId)
		);


		It6_GlobalCache_Invalidator::invalidateSportsbookByBet($betId);
		It6_GlobalCache_Invalidator::invalidateSportMenuFrame();

		It6_Models_BetChangelog::saveBetChangeOfStatus($betId, 0, 0, 0, $this->dbGame);

		$logTicketIds = array();
		foreach ($tickets as $ticketId => &$ticket) {
			if (!empty($ticket['helperData'])) {
				$ticket['helper'] = It6_Models_TicketFactory::newTicketCached($ticket['cachedData'], true, $ticket['helperData'], It6_Models_Ticket::DATA_ADMIN_TICKET, $ticket['currencyId'], $this->dbGame);
			}
			$this->dbGame->insert('vracene_sazky', array(
				'ticket_id' => $ticketId,
				'sazka_id' => $betId,
				'bookmaker_id' => Zend_Registry::get('acl')->getIdentity('admin'),
				'datum' => It6_Date::dbNow(),
			));


			$logTicketIds[] = $ticketId;
			Zend_Registry::get('ws')->TicketStatistics->payoutCancelation($ticketId);
		}

		$this->RemoveNoMoney($users, $tickets, $betInfo);

		$this->vrat .= UiUtil::printNotice('Bet #{0} was reverted to unevaluated.',$betId);
		It6_Log::info(
			"Trying to the bet #'%bet%' was reverted to unevaluated.",
			It6_Log::TAG_BOOKMAKER_OPERATION,
			array(
				'bet'	  => $betId,
				'ticketsAffected'   => (empty($logTicketIds) ? 'none' : $logTicketIds )
			)
		);
		
		It6_DbTransaction::commit($this->dbGame);
	}
	catch (Exception $e) {
		It6_DbTransaction::rollback($this->dbGame);
		It6_Log::notice('Bet revertion and unevaluation failed.');
		It6_Log::err($e);
		$this->vrat .= UiUtil::printErrors('Bet revertion and unevaluation failed.');
	}
}

/**
 * Takes money from user accounts where appropriate
 * @param array $users pole uzivatelu a jejich tiketu
 * @param array $tickets pole tiketu
 * @param array $betInfo info o sazce
 * @return void
 */
public function RemoveNoMoney(array $users, array $tickets, array $betInfo) {
	$ws = Zend_Registry::get('ws');
	$usersWithoutLoss = array();
	foreach ($users as $userId => &$user) {
		$userBalances = array(
			'currencies' => array(),
		);

		$rows = $this->dbGame->select()
			->from(array('u' => 'uzivatel'), array('mena_id'))
			->join(array('im' => 'uzivatel_im_data'), 'u.user_id=im.user_id', 'zustatek')
			->where('u.user_id=?', $userId)
			->query()
			->fetchAll();
		if (empty($rows))
			throw new ExHandler('Nepodarilo se provest dotaz: vyber zustatku uzivatele ID=' . $userId, 'admin_ex_db');
		$userBalances['currencies'][ $rows[0]['mena_id'] ] = $rows[0]['zustatek'];
		
		$userLosses = array( // overall amounts taken from user's account because of canceling his tickets
			'currencies' => array(),
		);
		foreach ($user['tickets'] as $ticketId => $ticket) {
			$ticket = &$tickets[$ticketId];
			$currencyId = $ticket['currencyId'];
			$helper = &$ticket['helper'];
			$freeBet = $ticket['freeBet'];
			$ticketWonNetto = $helper->won - $helper->stake;
			$ticketLoss = 0.0; // amount taken from user's account because of canceling this ticket

			// changed by Martin 1.10. 2012. mantis:1316
			// It seems to me that It6_Models_Ticket->computeAggregates()
			// behaves strangly when deciding if maxikombi ticket is winning.
			// Im to scared to change it though because no one knows what might depend on it. 
			// $winning = (It6_Models_Ticket::RESULT_WON == $helper->result && 0 < $ticketWonNetto);
			$winning = ($ticket['winReal'] > 0);

			$ticketStatsLossNoFbCc = 0.0;
			$ticketStatsLossCc = 0.0;
			if ($winning)
				$ticketLoss = ($freeBet ? $ticketWonNetto : $ticket['winReal']);
			else {
				if (It6_Models_Ticket::RESULT_WON == $helper->result) {
					$loss = -$ticketWonNetto;
					if (!$freeBet)
						$ticketLoss = $ticket['winReal'];
				}
				else
					$loss = $helper->stake;
				if (!$freeBet)
					$ticketStatsLossNoFbCc = $helper->convertTicketAmountToCentralCurrency($loss, $userId, $this->dbGame);
				$ticketStatsLossCc = $helper->convertTicketAmountToCentralCurrency($loss, $userId, $this->dbGame);
			}
			$ticketWonNettoNonNeg = ($ticketWonNetto < 0 ? 0 : $ticketWonNetto);
			$ticketWonNettoNonNegCc = $helper->convertTicketAmountToCentralCurrency($ticketWonNettoNonNeg, $userId, $this->dbGame, true);

			$this->dbGame->update(
				'ticket',
				array(
					'vyplacen' => 0,
					'vyplacen_date' => null,
					'stats' => 0,
					'stats_user' => 0
				),
				array('ticket_id=?' => $ticketId)
			);

			// Uprava statistik
			if (1 == $ticket['canceled'])
				$columnToDec = 'delete_ticket'; //"delete_ticket=delete_ticket-1";
			else if (!$winning)
				$columnToDec = 'lose_ticket'; //"lose_ticket=lose_ticket-1";
			else // winning
				$columnToDec = 'win_ticket'; //"win_ticket=win_ticket-1";

			if ($ticket['stats']) {
				$this->dbGame->update(
					'bet_stats',
					array(
						'ticket_num' => '(ticket_num-1)',
						'num_bet_ticket' => "(num_bet_ticket-{$ticket['betCount']})",
						$columnToDec => "($columnToDec-1)",
						'bet_total2' => '(bet_total2-' . ($ticket['canceled'] ? 0 : $ticket['amountCc']) . ')',
						'bet_total' => "(bet_total-{$ticket['amountCc']})",
						'pay_to_user' => "(pay_to_user-$ticketWonNettoNonNegCc)",
						'user_lose_acc' => "(user_lose_acc-$ticketStatsLossNoFbCc)",
						'user_lose_book' => "(user_lose_book-$ticketStatsLossCc)",
					),
					array()
				);
			}

			if ($ticket['statsUser']) {
				$this->dbGame->update(
					'uzivatel',
					array(
						'ticket_num' => '(ticket_num-1)',
						'num_bet_ticket' => "(num_bet_ticket-{$ticket['betCount']})",
						$columnToDec => "($columnToDec-1)",
						'bet_total2' => '(bet_total2-' . ($ticket['canceled'] ? 0 : $ticket['amountCc']) . ')',
						'bet_total' => "(bet_total-{$ticket['amountCc']})",
						'bet_stats_win' => "(bet_stats_win-$ticketWinNonNegCc)",
						'bet_stats_lose_acc' => "(bet_stats_lose_acc-$ticketStatsLossNoFbCc)",
						'bet_stats_lose_book' => "(bet_stats_lose_book-$ticketStatsLossCc)",
					),
					array('user_id=?' => $userId)
				);
			}

			$this->dbGame->delete('bet_stats_winners', array('ticket_id=?' => $ticketId));
			$this->dbGame->delete('vyherci_sazky', array('ticket_id=?' => $ticketId));

			$ws = Zend_Registry::get('ws');
			if ( $ticketLoss != 0 ) {
				//TODO: make transactions here instead in SendMail
				
				if (array_key_exists($currencyId, $userLosses['currencies']))
					$userLosses['currencies'][$currencyId] += $ticketLoss;
				else
					$userLosses['currencies'][$currencyId] = $ticketLoss;
				
				try {
					
					if ( empty($helper->cash) ) {
						
						$ws->Transaction->make(array(
							'value' => -$ticketLoss,
							'userId' => $userId,
							'typeName' =>'user.ticket.collect-nocash-cancel', 
							'currencyId' => $ticket['currencyId'],
							'ticketId' => $ticketId,
							'hostId' => $ticket['host_id']));

						if ( floatval($ticket['mpWinAmount']) > 0) {
							$ws->Transaction->make(array(
								'value' => $ticket['mpWinAmount'],
								'userId' => $userId,
								'typeName' =>'user.ticket.collect-nocash-cancel-mp', 
								'currencyId' => $ticket['currencyId'],
								'ticketId' => $ticketId,
								'hostId' => $ticket['host_id']));
						}
					}

					$ws->Transaction->make(array(
						'value' => -$ticketLoss,
						'userId' => $userId,
						'typeName' => Webservice_TransactionType::NAME_OTHER_TICKET_PAYOUT_CANCEL, 
						'currencyId' => $ticket['currencyId'],
						'ticketId' => $ticketId,
						'hostId' => $ticket['host_id']));

					if ( floatval($ticket['mpWinAmount']) > 0) {
						$ws->Transaction->make(array(
							'value' => $ticket['mpWinAmount'],
							'userId' => $userId,
							'typeName' => Webservice_TransactionType::NAME_OTHER_TICKET_PAYOUT_CANCEL_MP, 
							'currencyId' => $ticket['currencyId'],
							'ticketId' => $ticketId,
							'hostId' => $ticket['host_id']));
					}

				} catch (Exception $e) {
					$this->vrat .= UiUtil::printErrors('Error reverting online-ticket #{0} transaction!',$ticketId);
				}

				$user['tickets'][$ticketId]['loss']			= $ticketLoss;
				$user['tickets'][$ticketId]['handle']		= $ticket['ticketHandle'];
				$user['tickets'][$ticketId]['createdAt']	= $ticket['createdAt'];
				$user['tickets'][$ticketId]['type']			= $ticket['type'];
			}
			$time = It6_Date::dbDatetimeToTimestamp($ticket['createdAt']);
			if ($ws->Campaign->addTicketToEntryBonusBalance($userId, $helper, true, $time)) {
				$ws->Campaign->removeAppliedEntryBonus($userId);
			}
		}
		// removing all user ticket from all ticket games at once
		$ticketGames = $ws->Campaign->removeTicketFromGame(array_keys($user['tickets']));
		if (!empty($ticketGames)) {
			$gameTickets = array();
			foreach ($ticketGames as $ticketId => $gameIds) {
				foreach ($gameIds as $gameId) {
					$gameTickets[$gameId][$ticketId] = $ticketId;
				}
				It6_Log::info(
					'Ticket was removed from all games.',
					It6_Log::TAG_CAMPAIGN,
					array('ticketId' => $ticketId, 'gameIds' => $gameIds)
				);
			}
			It6_GlobalCache_Invalidator::Campaign_ticketGame($gameTickets);
		}

		$hasLoss = false;
		foreach ($userLosses as $types) {
			if (!empty($types)) {
				$hasLoss = true;
				break;
			}
		}
		if(!$hasLoss)
			$usersWithoutLoss[] = $userId;
		else {
			$user['balance'] =  $userBalances;
			$user['loss'] =  $userLosses;
		}
	}

	foreach ($usersWithoutLoss as $userId)
		unset($users[$userId]);
		

	$this->SendMail($betInfo, $users);
}

 /**
 * Sends email to users notifying them of the ticket cancelation
 * @param array $betInfo info o sazce
 * @param array $users pole uzivatelu a jejich tiketu
 * @return void
 */
public function SendMail($betInfo, $users){
	$result = true;

	foreach ($users as $userId => $user) {
		$debts = array(
			'currencies' => array(),
		);
		foreach ($user['loss']['currencies'] as $currencyId => $loss) {
			if (!array_key_exists($currencyId, $user['balance']['currencies'])) {
				It6_Log::err("User has no balance for currency. user=$userId, currency=$currencyId" , It6_Log::TAG_ADMIN);
				continue;
			}
			$balance = $user['balance']['currencies'][$currencyId];
			//TODO: tolerance shouldn't  be hardcoded?
			if ($balance < $loss - 0.5)
				$debts['currencies'][$currencyId] = $loss - $balance;
		}

		$params = array(
			It6_Cron_Job_Email::PARAM_EMAIL_TYPE => 'ReturnedBetEmail',
			It6_Cron_Job_Email_UserEmail::PARAM_USER_ID => $userId,
			It6_Cron_Job_Email_ReturnedBetEmail::PARAM_RETURNED_BET_INFO => $betInfo,
			It6_Cron_Job_Email_ReturnedBetEmail::PARAM_LOSSES => $user['loss'],
			It6_Cron_Job_Email_ReturnedBetEmail::PARAM_DEBTS => $debts,
			It6_Cron_Job_Email_ReturnedBetEmail::PARAM_TICKETS => $user['tickets'],
		);
		$cronJob = array(
			'typeName' => It6_Models_CronJobType::TYPENAME_EMAIL,
			'date' => It6_Date::dbNow(),
			'attemptsMax' => 2,
			'params' => $params
		);

		try {
			if(!Zend_Registry::get('ws')->CronJob->insert($cronJob))
				$result = false;
		}
		catch (Exception $e) {
			It6_Log::err(It6_Log::TAG_ADMIN, 'Returned bet email not sheduled for user ID=' . $userId);
			$result = false;
		}
	}

	return $result;
}

/**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
public function runAction(){
	$this->dbGame->disconnect();
}

/**
 * Vraci vystup do tridy main
 * @return string
 */
public function getContent(){
	return $this->vrat;
}

} // class VratitSazku
