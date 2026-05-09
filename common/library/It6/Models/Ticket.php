<?php

//TODO: replace It6_Models_Currency::getRegisteredName() by It6_Models_User::get($userId, 'currencyName')

/**
 *
 * @author Stastny
 *
 */
class It6_Models_Ticket extends It6_Models_DbDependent {

const DATA_AJAX = 'ajax';
const DATA_SLIP = 'slip';
const DATA_ADMIN_TICKET = 'admin_ticket';
const DATA_SERVICE = 'service';

const TYPE_SIMPLE = 'simple';
const TYPE_COMBI = 'kombi';
const TYPE_MAXI = 'maxikombi';
const TYPE_SYSTEM = 'system';

const RESULT_LOST = -1;
const RESULT_UNKNOWN = 0;
const RESULT_WON = 1;

const CHECK_OK = 0;
const CHECK_ERROR_NO_BETS = 1;
const CHECK_ERROR_MAX_BETS = 2;
const CHECK_ERROR_MAX_GROUPS = 3;
const CHECK_ERROR_MIN_NON_T = 4;

const COUPON_STATUS_NEW = 0; // before validation
const COUPON_STATUS_IN_ACCEPTATION = 1; // must be valid
const COUPON_STATUS_ACCEPTED = 2;
const COUPON_STATUS_REJECTED = 3;
const COUPON_STATUS_MODIFIED = 4;
const COUPON_STATUS_PROLONGED = 5;
const COUPON_STATUS_IN_ACCEPTATION_LIVE = 7;
const COUPON_STATUS_INTERRUPTED_ACCEPTATION = 8;
const COUPON_STATUS_GOING_TO_ACCEPTATION = 10;
const COUPON_STATUS_MARKED_AS_IN_ACCEPTATION = 11;
const COUPON_STATUS_MARKED_AS_ACCEPTED = 20;
const COUPON_STATUS_DELAYED = 21;
const COUPON_STATUS_MARKED_AS_REJECTED = 30;
const COUPON_STATUS_MARKED_AS_MODIFIED_ACCEPTED = 40;
const COUPON_STATUS_MARKED_AS_MODIFIED_REJECTED = 41;
const COUPON_STATUS_MARKED_AS_DELAYED_ACCEPTED = 50;
const COUPON_STATUS_MARKED_AS_DELAYED_REJECTED = 51;

const CONFIRMATION_REASON_LIVE = 'live';
const CONFIRMATION_REASON_STAKE = 'stake';
const CONFIRMATION_REASON_WATCHED = 'watched';
const CONFIRMATION_REASON_APPROVAL_GROUP = 'approval-group';
const CONFIRMATION_REASON_BET_HISTORY = 'bet-history';
const CONFIRMATION_REASON_EXCEEDED_RISK_LIMIT = 'risk-limit-exceeded';
const CONFIRMATION_REASON_WILL_EXCEED_RISK_LIMIT = 'will-exceed-risk-limit';

const MAIL_YES = 1;
const MAIL_NO = 0;

const SMS_YES = 1;
const SMS_NO = 0;

public $serializeVersion = 1; //TODO: in future implement custom unserialization function

public $userId = null;
public $currencyId = null;
public $pointType = null;
public $id = null;
public $type = null;
public $bets = array();
public $groups = false;
public $combinations = array();
public $stake = 0.0;
public $stakeInPoints = null;
public $rate = 0.0;
public $rateWithoutAdvance = null;
public $rateAdvance = null;
public $rateSum = 0.0;
public $win = 0.0; // win when all bets would be hit
public $won = 0.0; // win for all bets that was hit, not necessarily equal to what player will get
public $betCount = 0;
public $ticketCount = 0;
public $result = self::RESULT_UNKNOWN; // for MAXI RESULT_WON means that there was at least one winning combination
public $canceled = false; // all bets on ticket were canceled
public $hashes = array();
public $totalHash = null;
public $cash = null;
public $mail = self::MAIL_YES;
public $sms = self::SMS_YES;
public $mp = 0;
public $mpWin = 0;
public $mpWinAmount = 0;
public $mpWinAmountMax = 0;
public $givenStake = 0.0;
public $paidOut = null;
public $payoutTime = null; // UNIX timestamp
public $betUserHistoryUpdates = array(); // ( betId => ('ticketCount' => count of tickets with bet, 'stakeBalance' => sum of stakes from all subtickets with given bet) )
public $willExceedRiskLimit = null; // set by It6_Models_TicketValidator::riskLimit() : null==unknown, FALSE=won't, array=list of bet IDs that will exceed their limit
public $exceedesRiskLimitPercent = null; // set by It6_Models_TicketValidator::riskLimit() : null==unknown, FALSE=won't, array=list of bet IDs that will exceed their limit
public $anonymous = null; // if user for this ticket is anonymous

protected $sportData = false;

protected $statistics = false;


/**
 * Data that are public to web users
 * @see It6_Models_Ticket::getExposedData()
 * @var struct
 */
public static $exposedVars = null;

//TODO: add bet names into data
/**
 * @param string|int $currency Currency for rounding, 'central'|'default'|'user'|currency_id
 *									$data['currency'] has higher priority
 *									default is central
 * @param $data Content format depends on value of $dataType parameter:
 * DATA_AJAX:
 *		$data = array(
 *			 'userId' => user ID
 *			 'type' => TYPE_*
 *			 'bet' => array with keys: id_bet, id_col, rate, amount, [visible], [banker], group (0 = banker), [canceled], [couponTime]
 *			 'totalSum' => total stake
 *			 ['combinations'] => array (0 => array(), 1 => array('used' => true|false, 'stake' => stake on combination, ...)
 *					(will be read from DB if not specified and ticket ID is specified)
 *			 ['ticket_id'] => ticket ID
 *		)
 * DATA_ADMIN_TICKET:
 *		$data = array(
 *			 'userId' => user ID
 *			 'type' => TYPE_*
 *			 'ticket_id' => ticket ID
 *			 'totalSum' => total stake (required for kombi)
 *			 'paidOut' => empty/non-empty
 *			 'payoutTime' => DB datetime of payout/empty
 *			 'bet' => array with keys: sazka_id, id_col, [amount], [canceled], vysledek (semicolon separated values), group (0 = banker)
 *			 ['combinations'] => array (0 => array(), 1 => array('used' => true|false, 'stake' => stake on combination, ...)
 *					(will be read from DB if not specified and ticket ID is specified)
 *			 ['anonymous'] => NULL(=information not available)|boolean
 *		)
 * @NOTE: If changed parameters of this function, check It6_Models_TicketFactory class which delegates this function.
 */
public function __construct($data, $dataType = self::DATA_AJAX, $currency = null, &$db = null) {
	// COMMON PREAMBLE
	if ( !is_array($data) )
			$data = It6_ArrayWrapper::toNativeArray($data);
	if (!isset($data['userId']))
		throw new Exception('User ID not specified.');

	$this->userId = $data['userId'];
	if (array_key_exists('currency', $data))
		$this->currencyId = $data['currency'];
	else if (is_numeric($currency))
		$this->currencyId = $currency;
	else if (isset($currency)) {
		if ('central' == $currency)
			$this->currencyId = null;
		else {
			if ('user' == $currency && It6_Models_User::ID_INTERNET_ANONYMOUS == $this->userId)
				$currency = 'default';
			if ('default' == $currency)
				$this->currencyId = It6_Models_Currency::getCurrencyIdByIso(DEFAULT_CURRENCY_ISO, $db);
			else if ('user' == $currency) {
				$this->currencyId = It6_Models_User::get($this->userId, 'currencyId', $db);
				if (empty($this->currencyId))
					throw new Exception('Currency for user not found. userId=' . $this->userId);
			}
			else
				throw new Exception('Unknown currency specification.');
		}
	}
	if (empty($this->currencyId))
		$this->currencyId = It6_Models_Currency::getCentralCurrencyId($db);

	// DATA TYPE SPECIFIC
	if (self::DATA_AJAX == $dataType || self::DATA_ADMIN_TICKET == $dataType) {

		$this->id =	(array_key_exists('ticket_id', $data) ? $data['ticket_id'] : null);
		$this->pointType = array_key_exists('pointType', $data) ? $data['pointType'] : null;

		$this->mp = !empty($data['mp']) ? $data['mp'] : 0;
		$this->mpWin = !empty($data['mpWin']) ? $data['mpWin'] : 0;

		if (array_key_exists('type', $data))
			$this->type = $data['type'];
		if (array_key_exists('bet', $data))
			$this->bets = $data['bet'];
		if (array_key_exists('combinations', $data))
			$this->combinations = $data['combinations'];
		else if (!empty($this->id))
			$this->combinations = self::readCombinations($this->id, $db);
		if (array_key_exists('totalSum', $data))
			$this->stake = $data['totalSum'];
		if (array_key_exists('stake', $data))
			$this->stake = $data['stake'];
		if ( !empty($this->pointType) ) {
			if (array_key_exists('totalSumInPoints', $data))
				$this->stakeInPoints = $data['totalSumInPoints'];
			if (array_key_exists('stakeInPoints', $data))
				$this->stakeInPoints = $data['stakeInPoints'];
		}
		if (array_key_exists('win', $data))
			$this->win = $data['win'];
		$notVisibleBetIds = array();
		$order = 0;
		foreach ($this->bets as $betId => &$bet) {
			$bet['id'] = $bet[self::DATA_AJAX == $dataType ? 'id_bet' : 'sazka_id'];
			if (array_key_exists('visible', $bet) && 0 == $bet['visible']) {
				$notVisibleBetIds[] = $bet['id'];
				continue;
			}
			$order++;
			$bet['column'] = $bet['id_col'];
			$bet['resultValues'] = array();
			if (!empty($bet['canceled'])) {
				$bet['result'] = (empty($bet['paidOut']) ? self::RESULT_UNKNOWN : self::RESULT_WON);
				$bet['rate'] = 1.0;
			}
			else {
				if (!empty($bet['vysledek'])) {
					$bet['result'] = self::matchBetResult($bet['vysledek'], $bet['column']);
					$bet['resultValues'] = explode(';', trim($bet['vysledek'], ';'));
				}
				else if (!empty($bet['paidOut']))
					$bet['result'] = self::RESULT_LOST;
				else
					$bet['result'] = self::RESULT_UNKNOWN;
			}
			$bet['couponTime'] = (empty($bet['couponTime']) ? '' : strval($bet['couponTime']));
			if ( empty($bet['order']) ) {
				$bet['order'] = $order;
			}
			else {
				$order = $bet['order'];
			}
		}
		// remove not visible bets -- we should ignore them
		foreach ($notVisibleBetIds as $betId) {
			foreach ($this->bets as $key => &$bet) {
				if ($bet['id'] == $betId) {
					unset($this->bets[$key]);
					break;
				}
			}
		}
		if ( empty($this->pointType) )
			$this->rateAdvance = array_key_exists('rateAdvance', $data) ? $data['rateAdvance'] : null;
		if (self::DATA_ADMIN_TICKET == $dataType) {
			$this->paidOut = !empty($data['paidOut']);
			if (!empty($data['payoutTime'])) {
				$this->payoutTime = It6_Date::fromDbAsTimestamp($data['payoutTime']);
			} 
		}
	}
	else if (self::DATA_SLIP == $dataType) {
		$this->pointType = array_key_exists('point_type_id', $data) ? $data['point_type_id'] : null;

		$this->mp = !empty($data['mp']) ? $data['mp'] : 0;
		$this->mpWin = !empty($data['mpWin']) ? $data['mpWin'] : 0;

		if (array_key_exists('ticket_id', $data))
			$this->id = $data['ticket_id'];
		if (array_key_exists('type', $data))
			$this->type = $data['type'];
		$this->paidOut = !empty($data['vyplacen']);
		if (array_key_exists('match', $data)) {
			$order = 0;
			$this->bets = array();
			foreach ($data['match'] as $bet) {
				$order++;
				if (array_key_exists('trefil', $bet)) {
					switch($bet['trefil']) {
					case 1:
						$result = self::RESULT_WON;
						break;
					case 0:
						$result = self::RESULT_LOST;
						break;
					default:
						if (!empty($bet['proplacena']))
							$result = self::RESULT_LOST;
						else
							$result = self::RESULT_UNKNOWN;
						break;
					}
				}
				if (!empty($bet['vysledek_orig']))
					$result = self::matchBetResult($bet['vysledek_orig'], $bet['sloupec_id']);
				else if (!empty($bet['proplacena']))
					$result = self::RESULT_LOST;
				else
					$result = self::RESULT_UNKNOWN;
				if (array_key_exists('vysledek_orig', $bet))
					$resultValues = explode(';', trim($bet['vysledek_orig'], ';'));
				else
					$resultValues = array();
				$ako = (array_key_exists('ako', $bet) ? $bet['ako'] : false);
				$this->bets[] = array(
					'id' => $bet['sazka_id'], 'column' => $bet['sloupec_id'],
					'amount' => (empty($bet['amount']) ? 0.0 : $bet['amount']), 'rate' => $bet['kurz'],
					'group' => $bet['group'], 'result' => $result, 'resultValues' => $resultValues, 'ako' => $ako,
					'canceled' => (!empty($bet['zruseno']) && 1 == $bet['zruseno']),
					'order' => !empty($bet['order']) ? $bet['order'] : $order,
				);
				$order = !empty($bet['order']) ? $bet['order'] : $order;
			}
		}
		if (array_key_exists('combinations', $data))
			$this->combinations = $data['combinations'];
		else
			$this->combinations = self::readCombinations($data['ticket_id'], $db);
		if (array_key_exists('castka', $data))
			$this->stake = $this->roundStake($data['castka'], false, $db);
		if ( !empty($this->pointType) &&  array_key_exists('castkaInPoints', $data))
			$this->stake = $this->roundStakeInPoints($data['castkaInPoints'], $db);
		
		if ( empty($this->pointType) )
			$this->rateAdvance = array_key_exists('rate_advance', $data) ? $data['rate_advance'] : null;
	}
	else if (self::DATA_SERVICE == $dataType) {
		if ( !is_array($data) )
			$data = It6_ArrayWrapper::toNativeArray($data);

		if (array_key_exists('ticketId', $data))
			$this->id = $data['ticketId'];
		if (array_key_exists('type', $data))
			$this->type = $data['type'];
		$this->pointType = array_key_exists('pointTypeId', $data) ? $data['pointTypeId'] : null;

		$this->mp = !empty($data['mp']) ? $data['mp'] : 0;
		$this->mpWin = !empty($data['mpWin']) ? $data['mpWin'] : 0;

		if ( $this->type == self::TYPE_COMBI ) {
			$this->stake = $data['amount'];
			if ( !empty($this->pointType) && array_key_exists('pointsAmount', $data) ) {
				$this->stakeInPoints = $data['pointsAmount'];
			}
		}
		$this->paidOut = !empty($data['paidOut']);
		if (!empty($data['paidOutTime'])) {
			$this->payoutTime = It6_Date::fromDbAsTimestamp($data['paidOutTime']);
		} 

		if (array_key_exists('groups', $data)) {
			$this->bets = array();
			$order = 0;
			foreach ($data['groups'] as $group) {
				if (array_key_exists('tips', $group)) {
					foreach ($group['tips'] as $tip) {
						$order++;
						$bet = array();
						$bet['id'] = $tip['betId'];
						$bet['column'] = $tip['oddsOutcomeId'];
						if (isset($tip['amount']))
							$bet['amount'] = $tip['amount'];
						else if (isset($data['amount']))
							$bet['amount'] = $data['amount'];
						else
							$bet['amount'] = 0.0;
						$bet['rate'] = (empty($tip['rate'])
							? It6_Models_Bet::readBetRate($tip['betId'], $tip['oddsOutcomeId'], $data['createdTime'])
							: $tip['rate']);
						$bet['group'] = $tip['group'];
						$bet['ako'] = empty($tip['ako'])?false:$tip['ako'];
						$bet['canceled'] = empty($tip['canceled'])?false:true;

						$bet['resultValues'] = array();
						if (!empty($bet['canceled'])) {
							$bet['result'] = (empty($tip['paidOut']) ? self::RESULT_UNKNOWN : self::RESULT_WON);
							$bet['rate'] = 1.0;
						}
						else {
							if (!empty($tip['result'])) {
								$result = $tip['result'];
								$bet['result'] = self::matchBetResult($result, $bet['column']);
								if (!It6_ArrayWrapper::isArray($result))
									$result = explode(';', trim($result, ';'));
								$bet['resultValues'] = $result;
							}
							else if (!empty($tip['paidOut']))
								$bet['result'] = self::RESULT_LOST;
							else
								$bet['result'] = self::RESULT_UNKNOWN;
						}
						$bet['order'] = empty($tip['order']) ? $order : $tip['order'];
						$order = $bet['order'];
						$this->bets[] = $bet;
					}
				}
			}
		}
		if (array_key_exists('combinations', $data)) {
			$this->combinations = array();
			foreach ($data['combinations'] as $comb) {
				$k = $comb['k'];
				$this->combinations[$k] = array(
					'k' => $k,
					'stake' => (isset($comb['amount']) ? $comb['amount'] : $comb['stake']),
					'used' => (!array_key_exists('used', $comb) || !empty($comb['used'])),
				);
			}
		}
		else if (!empty($this->id))
			$this->combinations = self::readCombinations($this->id, $db);
		if ( empty($this->pointType) )
			$this->rateAdvance = array_key_exists('rateAdvance', $data) ? $data['rateAdvance'] : null;

	}

	// COMMON POSTAMBLE
	$this->cash = (array_key_exists('cash', $data) && $data['cash']);
	foreach ($this->bets as &$bet) {
		if (!empty($bet['amount']))
			$bet['amount'] = $this->roundStake($bet['amount'], ROUND_STAKES_ALWAYS_AS_CASH, $db);
		$bet['rate'] = $this->roundRate($bet['rate']);
		if (self::TYPE_SIMPLE == $this->type || self::TYPE_COMBI == $this->type)
			$bet['group'] = 1;
	}
	if (!empty($this->combinations)) {
		foreach ($this->combinations as &$comb)
			$comb['stake'] = (empty($comb['stake']) ? 0 : $this->roundStake($comb['stake'], ROUND_STAKES_ALWAYS_AS_CASH, $db));
	}
	if ( !empty($this->rateAdvance) )
		$this->rateAdvance = $this->roundRate($this->rateAdvance);

	$this->mail = (
		array_key_exists('mail', $data) && self::MAIL_NO == $data['mail'] ? self::MAIL_NO : self::MAIL_YES
	);
	$this->sms = (
		array_key_exists('sms', $data) && self::SMS_NO == $data['sms'] ? self::SMS_NO : self::SMS_YES
	);
	$this->givenStake = (array_key_exists('givenStake', $data) ? $data['givenStake'] : false);
	if (false !== $this->givenStake)
		$this->givenStake = $this->roundStake($this->givenStake, ROUND_STAKES_ALWAYS_AS_CASH || $this->cash, $db);

	if ( !empty($this->pointType) && !empty($this->stakeInPoints) && (empty($this->stake) || floatval($this->stake) == 0.0) ) {
		$this->stake = $this->convertStakeInPointsToStake(
			$this->pointType, $this->currencyId, $this->stakeInPoints, $db
		);	
	}
	else {
		$this->stake = $this->roundStake($this->stake, ROUND_STAKES_ALWAYS_AS_CASH || $this->cash, $db);
	}

	if (array_key_exists('anonymous', $data)) {
		$this->anonymous = $data['anonymous'];
	}
}

public static function isTypeMaxicombinatorCompatible($type) {
	switch ($type) {
	case self::TYPE_MAXI:
	case self::TYPE_SYSTEM:
		return true;
	default:
		return false;
	}
}

public function isMaxicombinatorCompatible() {
	return self::isTypeMaxicombinatorCompatible($this->type);
}

public static function trans($key) {
	if ( Zend_Registry::isRegistered('translate') ) {
		return Zend_Registry::get('translate')->trans($key, 'TICKET', It6_Translate_Web::DICTIONARY);
	}
	else {
		return $key;
	}
}

public static function transParam($key, $params) {
	if ( Zend_Registry::isRegistered('translate') ) {
		return Zend_Registry::get('translate')->transParams($key, $params, 'TICKET', It6_Translate_Web::DICTIONARY);
	}
	else {
		return $key;
	}
}

/**
 * Given all winning results (array or semicolon separated values) together with actual bet result this method returns appropriate RESULT_* value.
 */
public static function matchBetResult($winningValues, $resultValue) {
	if (empty($winningValues))
		return self::RESULT_UNKNOWN;
	if (is_array($winningValues))
		$winningValuesArray = $winningValues;
	else
		$winningValuesArray = explode(';', trim($winningValues, ';'));
	return (in_array($resultValue, $winningValuesArray) ? self::RESULT_WON : self::RESULT_LOST);
}

public static function getFinalResult($currentResult, $additionalResult) {
	switch ($additionalResult) {
	case self::RESULT_LOST:
		return self::RESULT_LOST;
	case self::RESULT_UNKNOWN:
		if (self::RESULT_LOST != $currentResult)
			return self::RESULT_UNKNOWN;
		break;
	case self::RESULT_WON:
		break;
	}
	return $currentResult;
}

public function getGroups() {
	if (false === $this->groups) {
		$this->groups = array();
		foreach ($this->bets as $bet) {
			$group = $bet['group'];
			$betCanceled = !empty($bet['canceled']);
//			if ($betCanceled) {
//				$betRate = 1.0;
//				$betResult = self::RESULT_WON;
//			}
//			else {
				$betRate = $bet['rate'];
				$betResult = (array_key_exists('result', $bet) ? $bet['result'] : self::RESULT_UNKNOWN);
//			}
			if (!isset($this->groups[$group]))
				$this->groups[$group] = array(
					'betCount' => 1, 'rate' => $betRate, 'result' => $betResult,
					'riskAmount' => 0.0, 'canceled' => $betCanceled, 'rateSum' => $betRate,
					'bets' => array($bet['id']),
				);
			else {
				++$this->groups[$group]['betCount'];
				$this->groups[$group]['rate'] *= $betRate;
				$this->groups[$group]['rateSum'] += $betRate;
				$this->groups[$group]['result'] = self::getFinalResult($this->groups[$group]['result'], $betResult);
				if (!$betCanceled)
					$this->groups[$group]['canceled'] = false;
				$this->groups[$group]['bets'][] = $bet['id'];
			}
		}
		ksort($this->groups, SORT_NUMERIC);
		foreach ($this->groups as &$group) {
			$r = &$group['rate'];
			$group['rateOrig'] = $r;
			$r = $this->roundRate($r);
		}
	}
	return $this->groups;
}

/**
 * @param bool $withGroupT TRUE if group 'T' should be counted in too
 * @returns int count of groups on ticket
 */
public function getGroupCount($withGroupT) {
	$this->getGroups();
	if (empty($this->groups))
		return 0;
	$count = sizeof($this->groups);
	if (!$withGroupT && $this->hasGroupT())
		--$count;
	return $count;
}

public function hasGroupT() {
	$this->getGroups();
	if (empty($this->groups))
		return false;
	return array_key_exists(0, $this->groups);
}

public function getGroupTSize() {
	return ($this->hasGroupT() ? $this->groups[0]['betCount'] : 0);
}

public function getGroupBets($groupId) {
	$this->getGroups();
	if (array_key_exists($groupId, $this->groups))
		return $this->groups[$groupId]['bets'];
	else
		return array();
}

public function hasBet($betId) {
	foreach ($this->bets as $bet) {
		if ($betId == $bet['id'])
			return true;
	}
	return false;
}

/**
 * Returns data of bets with given ID or data of bet with given ID and column ID.
 * @param integer $betId
 * @param integer|null $colId If NULL is given, array of bets is returned
 * @return array|boolean Bet data or array of bets data or FALSE if bet/s not matched
 */
public function getBet($betId, $colId) {
	if (isset($colId)) {
		foreach ($this->bets as $bet) {
			if ($betId == $bet['id'] && $colId == $bet['column'])
				return $bet;
		}
		return false;
	}
	else {
		$bets = array();
		foreach ($this->bets as $bet) {
				if ($betId == $bet['id'])
				$bets[] =	$bet;
		}
		return (empty($bets) ? false : $bets);
	}
}

public function getBetIds() {
	$ids = array();
	foreach ($this->bets as $bet) {
		if (!in_array($bet['id'], $ids))
			$ids[] = $bet['id'];
	}
	return $ids;
}

/**
 * @param integer|array $id Bet id (updates all columns) or array(betId, columnId)
 * @param array $data Data will be merged into bet's data
 */
public function setBetData($id, array $data) {
	if (is_array($id))
		list($id, $column) = $id;
	else
		$column = false;
	foreach ($this->bets as &$bet) {
		if ($id == $bet['id'] && (false === $column || $column == $bet['column'])) {
			foreach ($data as $k => $v)
				$bet[$k] = $v;
			return true;
		}
	}
	return false;
}

/**
 * Returns all needed data for bet user history update.
 * @param integer $betId
 * @return struct|null Fields: ticketCount, stakeBalance
 */
public function getDataForBetUserHistory($betId) {
	return (empty($this->betUserHistoryUpdates[$betId]) ? null : $this->betUserHistoryUpdates[$betId]);
}

public function isCombinationUsed($k) {
	return (
		!empty($this->combinations)
		&& !empty($this->combinations[$k])
		&& !empty($this->combinations[$k]['used'])
	);
}

public static function getGroupName($num) {
	if (0 == $num)
		return 'T';
	else
		return chr(ord('A') - 1 + $num);
}

public static function getCombinationName(array $combination, $hasGroupT) {
	$name = '';
	foreach ($combination as $num)
		$name .= self::getGroupName($num);
	if ($hasGroupT)
		$name .= 'T';
	return $name;
}

/**
 * Compute hash for one combination of bets.
 * @param array $hashItems array('order' => Array('id' => betId, 'column' => columnId), ...) Order should be numeric.
 * @return string Computed hash.
 */
public static function computeBetsHash(array $hashItems) {
	if (count($hashItems) > 1)
		ksort($hashItems, SORT_NUMERIC);
	$plain = '';
	foreach($hashItems as $item) {
		if (!empty($plain))
			$plain .= ':';
		$plain .= $item['id'] . ':' . $item['column'];
	}
	return md5($plain);
}

/**
 * Computes hash from array of strings. Strings are sorted before.
 * Special cases of $items:
 *	 array() -> null
 *	 array(hash) -> hash
 * @param array $hashes array(item1, item2, ...)
 * @return string hash
 */
public static function computeHash(array $items) {
	if (!is_array($items) || empty($items))
		return null;
	else if (1 == sizeof($items))
		return $items[0];
	else {
		sort($items, SORT_STRING);
		$plain = implode(':', $items);
		return md5($plain);
	}
}

public function computeTotalHash() {
	$this->totalHash = self::computeHash(array_values($this->hashes));
}

/**
 * @param array $comb array(groupId1, groupId2, ...) Group ID of group 'T' (ID = 0) will be included automatically if needed.
 * @return string hash
 */
public function getCombinationHash(array $comb) {
	$combBets = array();
	$groupT = false;
	foreach ($comb as $groupId) {
		if (0 == $groupId)
			$groupT = true;
		$combBets[] = $this->getGroupBets($groupId);
	}
	if (!$groupT && $this->hasGroupT())
		$combBets[] = $this->getGroupBets(0);
	$allBets = array();
	foreach ($combBets as $bets)
		$allBets = array_merge($allBets, $bets);
	$hashItems = array();
	foreach ($allBets as $betId) {
		$bet = $this->getBet($betId, null);
		if (1 != count($bet))
			throw new Exception('Same bet more times on this ticket forbidden');
		$bet = $bet[0];
		$hashItems[$betId] = array('id' => $betId, 'column' => $bet['column']);
	}
	return self::computeBetsHash($hashItems);
}

public function roundRate($rate) {
	return round($rate, 2);
}

/**
 * This method can be called statically, but in that case parameter $cash must be set.
 * @param float $stake Stake to be rounded
 * @param boolean|NULL $cash[optional] Round to currency smallest cash instead of currency smallest unit
 *													 If not set (default) then value is computed from values of this instance
 *													 and ROUND_STAKES_ALWAYS_AS_CASH config constant.
	* @return Rounded value
 */
public function roundStake($stake, $cash = null, &$db = null) {
	if (!isset($cash))
		$cash = (ROUND_STAKES_ALWAYS_AS_CASH || $this->cash);
	
	return It6_Models_Currency::round(
		$stake, It6_Models_Currency::ROUND_MATH, $this->currencyId,
		It6_Models_Currency::ROUND_PARAM_CURRENCY, $cash, $db
	);
}
/**
 * Round points stake
 * @param float $stakeInPoints amount in points
 * @return float Rounded value
 */
public function roundStakeInPoints($stakeInPoints) {
	return It6_Models_PointType::round(floatval($stakeInPoints));
}

/**
 * This method can be called statically, but in that case parameter $cash must be set.
 * @param float $mpAmount Manipulation fee (amount of money)
 * @param boolean|NULL $cash[optional] Round to currency smallest cash instead of currency smallest unit
 *													 If not set (default) then value is computed from values of this instance
 *													 and ROUND_STAKES_ALWAYS_AS_CASH config constant.
 * @return Rounded value
 */
public function roundMp($mpAmount, $cash = null, &$db = null) {
	if (!isset($cash))
		$cash = (ROUND_STAKES_ALWAYS_AS_CASH || $this->cash);

	return It6_Models_Currency::round(
		$mpAmount, It6_Models_Currency::ROUND_UP, $this->currencyId,
		It6_Models_Currency::ROUND_PARAM_CURRENCY, $cash, $db
	);

}

public static function formatRate($rate) {
	return sprintf('%01.2f', $rate);
}

/**
 * @param numeric $amount
 * @param integer $currencyId Currency ID
 * @param boolean $cash [optional] TRUE if formatting is for cash (ignored for points), default FALSE
 * @param Zend_Db_Adapter $db
 * @return string Formatted amount
 */
public static function formatAmount($amount, $currencyId, $cash = false, &$db = null) {	
	return It6_Models_Currency::formatAmount($amount, $currencyId, $cash, $db);
}

public function formatStake($amount, &$db = null) {
		return It6_Models_Currency::formatAmount($amount, $this->currencyId, $this->cash, $db);		
}

public function formatStakeInPoints($amount, &$db) {
	return It6_Models_PointType::formatAmount($amount, $this->pointType, $db);
}

/**
 * Computes overall ticket rate, stake, win; also for combinations' instances (creates their names too eg. ABCT)
 * @param Zend_Db_Adapter $db
 */
public function computeAggregates(&$db = null) {
	switch ($this->type) {
	case self::TYPE_SIMPLE:
		$this->stake = 0.0;
		$this->rate = 1.0;
		$this->rateTotal = 0;
		$this->rateSum = 0.0;
		$this->win = 0.0;
		$this->won = 0.0;
		$this->betCount = sizeof($this->bets);
		$this->ticketCount = $this->betCount;
		$this->result = self::RESULT_WON;
		$this->canceled = null;
		$this->hashes = array();
		$this->betUserHistoryUpdates = array();
		foreach ($this->bets as &$bet) {
			// each bet is in fact stand alone simple ticket
			$betId = $bet['id'];
			$this->stake += $this->roundStake($bet['amount'], false, $db);
			$rate = $this->roundRate($bet['rate']);
			$this->rate *= $this->roundRate($bet['rate']);
			$this->rateTotal += $bet['rate'] - 1;
			$this->rateSum += $rate;
			$win =	$this->roundStake($bet['amount'] * $bet['rate'], $this->cash, $db);
			$this->win += $win;
			$bet['win'] = $win;
			$betResult = (array_key_exists('result', $bet) ? $bet['result'] : self::RESULT_UNKNOWN);
			if (self::RESULT_WON == $betResult) {
				$this->won += $win;
				$bet['won'] = $win;
			}
			else
				$bet['won'] = 0;
			$this->result = self::getFinalResult($this->result, $betResult);
			$canceled = !empty($bet['canceled']);
			if (!isset($this->canceled))
				$this->canceled = $canceled;
			else if (!$canceled)
				$this->canceled = false;
			$bet['riskAmount'] = $bet['amount'];
			$bet['absoluteStake'] = $bet['amount'];
			$bet['inTicketCount'] = 1;
			$this->hashes[$betId . ':' . $bet['column']] = $this->computeBetsHash(array($bet));
			if (!isset($this->betUserHistoryUpdates[$betId]))
				$this->betUserHistoryUpdates[$betId] = array('ticketCount' => 1, 'stakeBalance' => $bet['amount']);
			else {
				++$this->betUserHistoryUpdates[$betId]['ticketCount'];
				$this->betUserHistoryUpdates[$betId]['stakeBalance'] += $bet['amount'];
			}
		}
		if (!isset($this->canceled))
			$this->canceled = false;
		if (count($this->bets) > 1) {
			$this->stake = $this->roundStake($this->stake, false, $db);
			$this->rate = $this->roundRate($this->rate);
			$this->win = $this->roundStake($this->win, $this->cash, $db);
			$this->won = $this->roundStake($this->won, $this->cash, $db);
		}
		$this->computeTotalHash();
		break;
	case self::TYPE_COMBI:
		$this->rate = 1.0;
		$this->rateTotal = 0;
		$this->rateSum = 0.0;
		$this->betCount = sizeof($this->bets);
		$this->ticketCount = 1;
		$this->result = self::RESULT_WON;
		$this->canceled = null;
		$this->betUserHistoryUpdates = array();
		$hashItems = array();
		foreach ($this->bets as &$bet) {
			$this->rate *= $bet['rate'];
			$this->rateTotal += $bet['rate'] - 1;
			$this->rateSum += $this->roundRate($bet['rate']);
			$this->betUserHistoryUpdates[$bet['id']] = array('ticketCount' => 1, 'stakeBalance' => $this->stake);
		}

		$this->rateWithoutAdvance = $this->rate;
		if ( !empty($this->rateAdvance) && $this->rateAdvance > 1 ) {
			$this->rate *= $this->rateAdvance;

		}
		$this->rate = $this->roundRate($this->rate);
		$this->rateWithoutAdvance = $this->roundRate($this->rateWithoutAdvance);

		if ( empty($this->stake) && !empty($this->win) && !empty($this->rate) ) {
			$this->stake = $this->win / $this->rate;
			$this->stake = $this->roundStake($this->stake, ROUND_STAKES_ALWAYS_AS_CASH, $db);
		}
		$this->win = $this->stake * $this->rate;

		foreach ($this->bets as &$bet) {
			$betResult = (array_key_exists('result', $bet) ? $bet['result'] : self::RESULT_UNKNOWN);
			$this->result = self::getFinalResult($this->result, $betResult);
			$canceled = !empty($bet['canceled']);
			if (!isset($this->canceled))
				$this->canceled = $canceled;
			else if (!$canceled)
				$this->canceled = false;
			
			if($this->rateTotal == 0)
				$bet['riskAmount'] = 0;
			else
				$bet['riskAmount'] = $this->stake * ($bet['rate'] - 1) / $this->rateTotal;
			$bet['absoluteStake'] = $this->stake;
			$bet['inTicketCount'] = 1;
			$hashItems[$bet['id']] = array('id' => $bet['id'], 'column' => $bet['column']);
		}
		if (!isset($this->canceled))
			$this->canceled = false;
		$this->won = (self::RESULT_WON == $this->result ? $this->win : 0.0);

		$this->stake = $this->roundStake($this->stake, false, $db);
		$this->win = $this->roundStake($this->win, $this->cash, $db);
		$this->won = $this->roundStake($this->won, $this->cash, $db);
		$this->hashes = array($this->getGroupName(1) => self::computeBetsHash($hashItems));
		$this->computeTotalHash();
		break;
	case self::TYPE_SYSTEM:
	case self::TYPE_MAXI:
		$this->stake = 0.0;
		$this->rate = 1.0;
		$this->rateTotal = 0;
		$this->rateSum = 0.0;
		$this->win = 0.0;
		$this->won = 0.0;
		$this->betCount = 0;
		$this->result = self::RESULT_LOST;
		$this->canceled = false;
		$this->betUserHistoryUpdates = array();
		$canceled = null;
		foreach ($this->bets as &$bet) {
			$rate = $bet['rate'];
			$this->rate *= $rate;
			$this->rateSum += $rate;
			$bet['riskAmount'] = 0.0;
			$bet['absoluteStake'] = 0.0;
			if (empty($bet['canceled']))
				$canceled = false;
			else if (!isset($canceled))
				$canceled = true;
		}
		if (isset($canceled))
			$this->canceled = $canceled;
		$this->rate = $this->roundRate($this->rate);
		if (!isset($this->canceled))
			$this->canceled = false;
		$this->getGroups();
		$hasGroupT = $this->hasGroupT();
		$groupIds = array();
		foreach (array_keys($this->groups) as $id) {
			if (0 != $id)
				$groupIds[] = $id;
			$this->groups[$id]['riskAmount'] = 0.0;
			$this->groups[$id]['inTicketCount'] = 0;
		}
		$n = $this->getGroupCount(false);
		foreach ($this->combinations as $k => &$comb) {
			if (!$this->isCombinationUsed($k))
				continue;
			$cs = It6_Array::getCombinations($n, $k, $groupIds);
			$cData = array();
			$cStake = $comb['stake'];
			$cGroupCount = $k + ($hasGroupT ? 1 : 0);
			$cStakePerGroup = $cStake / $cGroupCount;
			$cRateSum = 0.0;
			$cRateOverOne = 0;
			$cWinMin = 0;
			$cWinMax = 0;
			foreach ($cs as $c) {
				$cName = self::getCombinationName($c, $hasGroupT);
				$cHash = $this->getCombinationHash($c);
				$cRate = 1.0;
				$cResult = self::RESULT_WON;
				$cCanceled = true;
				$cBets = array();
				foreach ($c as $g) {
					$rate = $this->groups[$g]['rateOrig'];
					$cRate *= $rate;
					$cRateSum += $rate;
					$cResult = self::getFinalResult($cResult, $this->groups[$g]['result']);
					if (!$this->groups[$g]['canceled'])
						$cCanceled = false;
					$cBets = array_merge($cBets, $this->getGroupBets($g));
					++$this->groups[$g]['inTicketCount'];
				}
				if ($hasGroupT) {
					$rate = $this->groups[0]['rateOrig'];
					$cRate *= $rate;
					$cRateSum += $rate;
					$cResult = self::getFinalResult($cResult, $this->groups[0]['result']);
					if (!$this->groups[0]['canceled'])
						$cCanceled = false;
					$cBets = array_merge($cBets, $this->getGroupBets(0));
					++$this->groups[0]['inTicketCount'];
				}
				if (self::RESULT_WON == $cResult) {
					$cResultText = 'result_win';
					if (self::RESULT_UNKNOWN != $this->result)
						$this->result = self::RESULT_WON;
				}
				else if (self::RESULT_LOST == $cResult)
					$cResultText = 'result_loss';
				else {
					$cResultText = 'result_open';
					$this->result = self::RESULT_UNKNOWN;
				}
				$cRate = $this->roundRate($cRate);
				$cWin = $cStake * $cRate;
				$cWin = $this->roundStake($cWin, false, $db);
				$cWon = (self::RESULT_WON == $cResult ? $cWin : 0.0);
				if (0 == $cWinMin || $cWin < $cWinMin)
					$cWinMin = $cWin;
				if (0 == $cWinMax || $cWin > $cWinMax)
					$cWinMax = $cWin;
				
				$cData[] = array(
					'groups' => $c,
					'bets' => $cBets,
					'name' => $cName,
					'rate' => $cRate,
					'rateSum' => $cRateSum,
					'win' => $cWin,
					'won' => $cWon,
					'result' => $cResultText,
					'resultValue' => $cResult,
					'canceled' => $cCanceled,
					'hash' => $cHash,
				);
				$this->stake += $cStake;
				$this->win += $cWin;
				$this->won += $cWon;
				$this->hashes[$cName] = $cHash;
				
				foreach ($cBets as $betId) {
					if (!isset($this->betUserHistoryUpdates[$betId]))
						$this->betUserHistoryUpdates[$betId] = array('ticketCount' => 1, 'stakeBalance' => $cStake);
					else
						$this->betUserHistoryUpdates[$betId]['stakeBalance'] += $cStake;
				}
			}
			
			foreach($this->bets as $bet1){
				if(in_array($bet1['id'], $cBets))
					$cRateOverOne += $bet1['rate'] - 1;
			}
			
			$this->betCount += sizeof($cs);
			$comb['data'] = $cData;
			$comb['winMin'] = $cWinMin;
			$comb['winMax'] = $cWinMax;
			$comb['rateOverOne'] = $cRateOverOne;
		}


		foreach ($this->combinations as $k => &$comb) {
			if (!$this->isCombinationUsed($k))
				continue;
			foreach ($comb['data'] as &$c) {
				foreach ($this->bets as &$bet) {
					if (in_array($bet['id'], $c['bets'])) {
						if($comb['rateOverOne'] == 0)
							$riskAmount = 0;
						else
							$riskAmount = $comb['stake'] * ($bet['rate'] - 1) / $comb['rateOverOne'];
						$bet['riskAmount'] += $riskAmount;
						$bet['absoluteStake'] += $comb['stake'];
					}
				}
			}
		}

		foreach ($this->groups as $id => &$group) {
			foreach ($this->bets as &$bet) {
				if ($bet['group'] == $id) {
					$bet['inTicketCount'] = $group['inTicketCount'];
				}
			}
		}
		$this->ticketCount = $this->betCount;
		
		foreach ($this->bets as &$bet)
			$bet['riskAmount'] = $this->roundStake($bet['riskAmount'], false, $db);
		$this->stake = $this->roundStake($this->stake, false, $db);
		$this->win = $this->roundStake($this->win, $this->cash, $db);
		$this->won = $this->roundStake($this->won, $this->cash, $db);
		
		$this->computeTotalHash();
		break;
	default:
		break;
	}
	$this->computeMpWin(null, $db);
}

/**
 * Recalculates mpWinAmount and mpWinAmountMax, this instabnce must have already computeAggregates() called 
 * @param float $mpWin
 * @param Zend_Db_Adapter $db
 */
public function computeMpWin($mpWin = null, &$db = null) {
	if (!isset($mpWin))
		$mpWin = $this->mpWin;
	$this->mpWinAmount = 0.0;
	$this->mpWinAmountMax = 0.0;
	switch ($this->type) {
	case self::TYPE_SIMPLE:
		foreach ($this->bets as $bet) {
			// each bet is in fact stand alone simple ticket
			if (array_key_exists('result', $bet) && self::RESULT_WON == $bet['result'])
				$this->mpWinAmount += $bet['amount'];
			$this->mpWinAmountMax += $bet['amount'];
		}
		break;
	case self::TYPE_COMBI:
		if (self::RESULT_WON == $this->result)
			$this->mpWinAmount = $this->stake;
		$this->mpWinAmountMax = $this->stake;
		break;
	case self::TYPE_SYSTEM:
	case self::TYPE_MAXI:
		foreach ($this->combinations as $k => $comb) {
			if (!$this->isCombinationUsed($k))
				continue;
			$cStake = $comb['stake'];
			foreach ($comb['data'] as $c) {
				if (self::RESULT_WON == $c['resultValue'])
					$this->mpWinAmount += $cStake;
				$this->mpWinAmountMax += $cStake;
			}
		}
		break;
	}
	if (0 != $this->mpWinAmount) {
		if ($this->canceled)
			$this->mpWinAmount = 0;
		else
			$this->mpWinAmount = $this->roundMp($this->mpWinAmount * $mpWin, $this->cash, $db);
	}
	if (0 != $this->mpWinAmountMax)
		$this->mpWinAmountMax = $this->roundMp($this->mpWinAmountMax * $mpWin, $this->cash, $db);
}

public function setCombinationsStakes($amount, $fromTotal = false) {
	if ($fromTotal)
		$amount = $this->roundStake($amount / $this->betCount);
	foreach ($this->combinations as $k => &$comb) {
		if ($this->isCombinationUsed($k))
			$comb['stake'] = $amount;
	}
}

/**
 * Function needs computed aggragates.
 * @return NULL|boolean TRUE if coupon will win (at least partially), FALSE if there is no chance to win, NULL if not determined yet
 */
public function willWin() {
	switch($this->type) {
	case self::TYPE_SIMPLE:
		foreach ($this->bets as $bet) {
			if (self::RESULT_WON == $bet['result'])
				return true;
			else if (self::RESULT_UNKNOWN == $bet['result'])
				return null;
		}
		return false;
	case self::TYPE_COMBI:
		$unknown = false;
		foreach ($this->bets as $bet) {
			if (self::RESULT_LOST == $bet['result'])
				return false;
			else if (self::RESULT_UNKNOWN == $bet['result'])
				$unknown = true;
		}
		return ($unknown ? null : true);
	case self::TYPE_MAXI:
	case self::TYPE_SYSTEM:
		$unknown = false;
		foreach ($this->combinations as $k => $comb) {
			if (!$this->isCombinationUsed($k))
				continue;
			foreach ($comb['data'] as $c) {
				$result = $c['resultValue'];
				if (self::RESULT_WON == $result)
					return true;
				else if (self::RESULT_UNKNOWN == $result)
					$unknown = true;
			}
		}
		return ($unknown ? null : false);
	default:
		throw new Exception('Unknown coupon type: ' . $this->type);
	}
}

public static function compareBetsByGroup($bet1, $bet2) {
	$g1 = $bet1['group'];
	$g2 = $bet2['group'];
	if ($g1 == $g2)
		return 0;
	else if (0 == $g1)
		return 1;
	else if (0 == $g2)
		return -1;
	else if ($g1 < $g2)
		return -1;
	else
		return 1;
}

/**
 * for TYPE_MAXI sort bets by group ID, left unchanged otherwise
 */
public static function sortBetsByGroup($bets, $type) {
	$sorted = $bets;
	if (self::TYPE_MAXI == $type)
		uasort($sorted, 'self::compareBetsByGroup');
	return $sorted;
}

public function saveCombinations($ticketId = null, &$db = null) {
	if (!isset($ticketId))
		$ticketId = $this->id;
	static::assureDbParam($db);
	foreach ($this->combinations as $k => $comb) {
		if ($this->isCombinationUsed($k)) {
			$data = array();
			$data['ticket_id'] = $ticketId;
			$data['k'] = $k;
			$data['stake'] = $comb['stake'];
			/* IT6: combination data:
			$cData = array( 'rows' => array() );
			foreach ($comb['data'] as $row) {
				$cData['rows'][] = array(
					'name' => $row['name'],
					'rate' => $row['rate'],
					'win' => $row['win'],
					'won' => $row['won'],
				);
			} 
			$data['data'] = Zend_Json::encode($cData);
			*/
			$db->insert('ticket_combination', $data);
		}
	}
}

public static function readCombinations($ticketId, &$db = null) {
	static::assureDbParam($db);
	$rows = $db->select()
		->from('ticket_combination', array('k', 'stake' /* IT6: combination data: , 'data' */))
		->where('ticket_id=?', $ticketId)
		->order('k')
		->query()
		->fetchAll();
	$combinations = array();
	foreach ($rows as $row) {
		//IT6: combination data:
		//$data = Zend_Json::decode($row['data']);
		//$cData = array();
		//foreach ($data['rows'] as $cRow) {
		//	$cData[] = array(
		//		'name' => $cRow['name'],
		//		'rate' => $cRow['rate'],
		//		'win' => $cRow['win'],
		//		'won' => $cRow['won'],
		//	);
		//}
		$combinations[$row['k']] = array(
			'used' => true,
			'stake' => $row['stake'],
			//IT6: combination data:
			//'data' => $cData,
		);
	}
	return $combinations;
}

public static function getReadableId($dbId, &$db = null) {
	static::assureDbParam($db);
	return It6_NineDigitHandle::makeHandle($dbId, $db);
}

public static function getPrivateId($publicId, &$db = null) {
	static::assureDbParam($db);
	return It6_NineDigitHandle::decodeHandle($publicId, $db);
}

/**
 * Convert amount to central currency
 * @param float $amount
 * @param integer $currencyId
 * @param Zend_Db_Adapter $db
 */
public static function convertAmountToCentralCurrency($amount, $currencyId, &$db = null) {
	return It6_Models_Currency::convertAmountToCentralCurrencyFromCurrency($currencyId, $amount, $db);
}

/**
 * @return boolean Returns true if it is point ticket
 */

public function isPointTicket() {
	return !empty($this->pointType);
}

public function convertAmountToTicketCurrency($amount, $currencyId, &$db = null) {

	if (!empty($this->currencyId)) {
		$ticketCurrencyId = $this->currencyId;
	}
	else {
		$userId = $this->userId;
		if (empty($userId))
			$userId = Zend_Registry::get('user_id');

		$ticketCurrencyId = It6_Models_User::get($userId, 'currencyId', $db);
		if (empty($ticketcurrencyId))
			throw new Exception('Currency not found. user=' . $userId);
	}
	
	return It6_Models_Currency::convert($currencyId, $ticketCurrencyId, $amount, $db);
	
}

public function convertTicketAmountToCentralCurrency($amount, $userId = null, &$db = null, $clearCache = false) {
	static $currencyId = false;
	if ($clearCache) {
		$currencyId = false;
	}
	if (false === $currencyId) {	
		if ( !empty($this->currencyId) ) 
			$currencyId = $this->currencyId;
		else {
			if (!isset($userId))
				$userId = $this->userId;
			if (empty($userId))
				$userId = Zend_Registry::get('user_id');

			$currencyId = It6_Models_User::get($userId, 'currencyId', $db);
			if (empty($currencyId))
				throw new Exception('Currency not found. user=' . $userId);
		}
	}
	
	return self::convertAmountToCentralCurrency($amount, $currencyId, $db);
}

public function convertStakesToCentralCurrency($userId = null, &$db = null) {
	if (0 != $this->stake)
		$this->stake = $this->convertTicketAmountToCentralCurrency($this->stake, $userId, $db);
	foreach ($this->bets as &$bet) {
		if (!empty($bet['amount']))
			$bet['amount'] = $this->roundStake(
				$this->convertTicketAmountToCentralCurrency($bet['amount'], $userId, $db),
				false, $db
			);
	}
	if (!empty($this->combinations)) {
		foreach ($this->combinations as &$comb) {
			if (!empty($comb['stake']))
				$comb['stake'] = $this->roundStake(
					$this->convertTicketAmountToCentralCurrency($comb['stake'], $userId, $db),
					false, $db
				);
		}
	}
	if (!empty($this->sportData)) {
		foreach ($this->sportData as &$data)
			$data['riskAmount'] = $this->convertTicketAmountToCentralCurrency($data['riskAmount'], $userId, $db);
	}
	$this->currencyId = It6_Models_Currency::getCentralCurrencyId($db);
	$this->pointType = null;
}

private function convertStakeInPointsToStake($pointType, $currencyId, $stakeInPoints, &$db) {
	$stakeInPoints = $this->roundStakeInPoints($stakeInPoints);
	$ret = It6_Models_PointType::convertPointsToCurrency($pointType, $currencyId, $stakeInPoints);
	$this->roundStake($ret,false,$db);
	return $ret;
}

public function getCurrencyName(&$db = null) {
	if (!empty($this->currencyId)) {
		return It6_Models_Currency::get($this->currencyId, 'name', $db);
	}
	return '';
}

public function getPointName(&$db = null) {
	if (!empty($this->pointType)) {
		return It6_Models_PointType::get($this->pointType, 'name', $db);
	}
	return '';
}

/**
 * Tiket musi mit spocitany agregacni hodnoty v centralni mene!
 */
public function saveBetsRiskLimit($useTransaction = true, &$db = null, $sign = 1) {
	static::assureDbParam($db);
	if ($useTransaction)
		$db->beginTransaction();
	try {
//file_put_contents('/tmp/php-debug-limits.log', "Saving bets limits", FILE_APPEND);
		foreach ($this->bets as $bet) {
//file_put_contents('/tmp/php-debug-limits.log', "{$bet['id']}: {$bet['riskAmount']}\n", FILE_APPEND);
			It6_Models_Bet::saveRiskLimitBalanceChange($bet['id'], $sign * $bet['riskAmount'], $db);
		}
		if ($useTransaction)
			$db->commit();
		return true;
	}
	catch (Exception $e) {
		if ($useTransaction)
			$db->rollback();
		throw new Exception($e);
	}
}

/**
 * Tiket musi mit spocitany agregacni hodnoty v centralni mene!
 */
public function saveSportRiskLimits($userId, $useTransaction = true, &$db = null, $sign = 1) {
	static::assureDbParam($db);
	if ($useTransaction)
		$db->beginTransaction();
	try {
//file_put_contents('/tmp/php-debug-limits.log', "Saving user sport limits: $userId : " . print_r($sportRiskAmounts, true) . "\n", FILE_APPEND);
		$sportRiskAmounts = array();
		foreach ($this->getSportData(false, $db) as $sportId => $data)
			$sportRiskAmounts[$sportId] = $sign * $data['riskAmount'];
		$result = It6_Models_User::updateSportLimits($userId, $sportRiskAmounts, false, $db);
		if ($useTransaction)
			$db->commit();
		return $result;
	}
	catch (Exception $e) {
		if ($useTransaction)
			$db->rollback();
		throw new Exception($e);
	}
}

public function saveTicketHashes($useTransaction = true, $userId = null, &$db = null) {
	if (empty($this->hashes))
		return false;
	if (!isset($userId))
		$userId = $this->userId;
	if (!isset($userId))
		$userId = Zend_Registry::get('user_id');
	static::assureDbParam($db);
	if ($useTransaction)
		$db->beginTransaction();
	try {
		/*
		$ticketHashes = $db->select()
			->from(array('tht' => 'tickethash_ticket'), 'tickethash')
			->join( array('th' => 'tickethash'), 'tht.tickethash=th.tickethash AND tht.user_id=th.user_id', array('tickethash_count') )
			->where('tht.ticket_id=?', $this->id)
			->where('th.user_id=?', $userId)
			->query()
			->fetchAll();
		// delete ticket associations to hashes
		$db->delete('tickethash_ticket', 'ticket_id=' . $this->id . ' AND user_id=' . $userId);
		$dbHashes = array();
		if (!empty($this->hashes)) {
			// get existing hashes that colide
			$res = $db->select()
				->from('tickethash', array('tickethash', 'tickethash_count'))
				->where('user_id=?', $userId)
				->where('tickethash IN (?)', array_values($this->hashes))
				->query();
			while ($row = $res->fetch())
				$dbHashes[$row['tickethash']] = $row['tickethash_count'];
			// insert new hashes if not found previously in DB and insert ticket associations
			foreach ($this->hashes as $hash) {
				if (!array_key_exists($hash, $dbHashes)) {
					$db->insert('tickethash', array('tickethash' => $hash, 'user_id' => $userId, 'tickethash_count' => 0));
					$dbHashes[$hash] = 0;
				}
				$db->insert('tickethash_ticket', array('tickethash' => $hash, 'user_id' => $userId, 'ticket_id' => $this->id));
			}
		}
		// update hash associations counts (delete hash if no ticket association found)
		$rows = array();
		if (!empty($dbHashes)) {
			$rows = $db->select()
				->from( array('th' => 'tickethash'), array('th.tickethash') )
				->joinLeft(
					array('tht' => 'tickethash_ticket'),
					'th.tickethash=tht.tickethash AND th.user_id=tht.user_id',
					array('c' => 'COUNT(tht.ticket_id)')
				)
				->where('th.tickethash IN (?)', array_keys($dbHashes))
				->group('th.tickethash')
				->query()
				->fetchAll();
		}
		foreach ($rows as $row) {
			if (empty($row['c']))
				$db->delete('tickethash', array('tickethash=?', $row['tickethash']));
			else
				$db->update( 'tickethash', array('tickethash_count' => $row['c']), array('tickethash=?' => $row['tickethash']) );
		}
		*/

		$db->delete('tickethash_ticket', array('ticket_id=?' => $this->id, 'user_id=?' => $userId));
		foreach ($this->hashes as $hash) {
			$dbHash = $db->quote($hash);
			$dbUser = $db->quote($userId);
			$db->query(
				// insert new hash with count equal to one or update hash count to actual sum plus one
				"INSERT INTO tickethash(tickethash,user_id,tickethash_count) VALUES($dbHash,$dbUser,1)"
				. " ON DUPLICATE KEY UPDATE tickethash_count=(SELECT (COUNT(ticket_id)+1) FROM tickethash_ticket WHERE tickethash=$dbHash AND user_id=$dbUser)"
			);
			$db->insert('tickethash_ticket', array('tickethash' => $hash, 'user_id' => $userId, 'ticket_id' => $this->id));
		}

		if ($useTransaction)
			$db->commit();
		return true;
	}
	catch (Exception $e) {
		if ($useTransaction)
			$db->rollback();
		throw $e;
	}
}

public function deleteTicketHashes($useTransaction = true, &$db = null) {
	static::assureDbParam($db);
	if ($useTransaction)
		$db->beginTransaction();
	try {
		$userId = $this->userId;
		if (!isset($userId))
			$userId = Zend_Registry::get('user_id');
		$rows = $db->select()->from('tickethash_ticket', 'tickethash')
			->where('ticket_id=?', $this->id)
			->where('user_id=?', $userId)
			->group('tickethash')
			->query()
			->fetchAll();
		$hashes = array();
		foreach ($rows as $row)
			$hashes[$row['tickethash']] = true;
		$hashes = array_keys($hashes);
		if (!empty($hashes)) {
			$db->update(
				'tickethash',
				array('tickethash_count' => new Zend_Db_Expr('tickethash_count-1')),
				array('tickethash IN (?)' => $hashes, 'user_id=?' => $userId)
			);
			$db->delete('tickethash_ticket', array('ticket_id=?' => $this->id));
			//TODO: use some more secure deletion (this easily triggers contraint violation)
			//$db->delete('tickethash', array('tickethash IN (?)' => $hashes, 'tickethash_count<=0'));
		}
		if ($useTransaction)
			$db->commit();
		return true;
	}
	catch (Exception $e) {
		if ($useTransaction)
			$db->rollback();
		throw $e;
	}
}

public static function getStatusTimeout($status) {
	$timeout = 0;
	switch ($status) {
	case self::COUPON_STATUS_NEW:
		break;
	case self::COUPON_STATUS_GOING_TO_ACCEPTATION:
		//TODO: use WS global parameter
		$timeout = COUPON_LIFETIME;
		break;
	case self::COUPON_STATUS_IN_ACCEPTATION_LIVE:
		//TODO: use WS global parameter
		$timeout = Constant::get('LIVE_WAIT_TIME_NO_CONFIRM') + 1;
		break;
	case self::COUPON_STATUS_IN_ACCEPTATION:
	case self::COUPON_STATUS_PROLONGED:
		$map = array(
			self::COUPON_STATUS_IN_ACCEPTATION => It6_Models_Parameter::NAME_TICKET_CONFIRM_TIME,
			self::COUPON_STATUS_PROLONGED => It6_Models_Parameter::NAME_TICKET_CONFIRM_TIME_MORE,
		);
		$db = Zend_Registry::get('admindb');
		$param = It6_Models_Parameter::getDataByName($map[$status], $db);
		$timeout = intval($param['value']);
		break;
	case self::COUPON_STATUS_INTERRUPTED_ACCEPTATION:
		$timeout = COUPON_LIFETIME;
		break;
	case self::COUPON_STATUS_MARKED_AS_IN_ACCEPTATION:
	case self::COUPON_STATUS_MARKED_AS_ACCEPTED:
	case self::COUPON_STATUS_MARKED_AS_REJECTED:
	case self::COUPON_STATUS_MARKED_AS_MODIFIED_ACCEPTED:
	case self::COUPON_STATUS_MARKED_AS_MODIFIED_REJECTED:
	case self::COUPON_STATUS_ACCEPTED:
	case self::COUPON_STATUS_REJECTED:
	case self::COUPON_STATUS_MODIFIED:
		$timeout = COUPON_LIFETIME;
		break;
	default:
		throw new Exception('Unknown coupon status: ' . $couponStatus);
	}
	return $timeout;
}

public static function isStatusWaitingForConfirmd($status) {
	switch ($status) {
	case self::COUPON_STATUS_NEW:
	case self::COUPON_STATUS_GOING_TO_ACCEPTATION:
	case self::COUPON_STATUS_IN_ACCEPTATION:
	case self::COUPON_STATUS_IN_ACCEPTATION_LIVE:
	case self::COUPON_STATUS_PROLONGED:
	case self::COUPON_STATUS_ACCEPTED:
	case self::COUPON_STATUS_REJECTED:
	case self::COUPON_STATUS_MODIFIED:
	case self::COUPON_STATUS_INTERRUPTED_ACCEPTATION:
		return false;
	case self::COUPON_STATUS_MARKED_AS_IN_ACCEPTATION:
	case self::COUPON_STATUS_MARKED_AS_ACCEPTED:
	case self::COUPON_STATUS_MARKED_AS_REJECTED:
	case self::COUPON_STATUS_MARKED_AS_MODIFIED_ACCEPTED:
	case self::COUPON_STATUS_MARKED_AS_MODIFIED_REJECTED:
		return true;
	default:
		throw new Exception('Unknown coupon status: ' . $couponStatus);
	}
}

/**
 * Make decision about the need of confirmation by bookmaker.
 * NOTE: This instance must hold amounts in user's currency (as it is usual for coupons).
 * @param integer $couponStatus Currenct coupon status
 * @param integer $couponTimestamp UNIX timestamp of status
 * @param integer $cuponId ID of tested coupon (optional, for logging purposes)
 * @param array $reason [optional] Will receive array with one item. Key is text code of reason why coupon needs confirmation if needs any
 *                                 (using class constants CONFIRMATION_REASON_*), value is reason data (particular reason specific).<br/>
 *                                 Default info is simply TRUE value.<br/>
 *                                 <ul>
 *                                 <li>CONFIRMATION_REASON_BET_HISTORY returns $betData output parameter from checkBetHistory (@see It6_Models_Ticket::checkBetHistory)</li>
 *                                 <li>CONFIRMATION_REASON_EXCEEDED_RISK_LIMIT and CONFIRMATION_REASON_WILL_EXCEED_RISK_LIMIT returns list of bet IDs</li>
 *                                 </ul>
 * @param boolean $liveConfirm [optional] Will be updated as TRUE if all bets are live and OK, as FALSE otherwise (used only if confirmation needed)
 * @param Zend_Db_Adapter $db [optional]
 * @return boolean|NULL FALSE for coupon that can be accepted, TRUE if confirmation should start/continue, NULL if confirmation should not be started
 * TODO: Some checks for unexpected statuses? (like postconfirmation ones)
 */
public function needsConfirmation($couponStatus, $couponTimestamp, $couponId = null, &$reason = null, &$liveConfirm = null, &$db = null) {
	$userId = $this->userId;
	$fnLog = function ($msg) use($userId, $couponId) {
		It6_Log::debug(
			$msg,
			It6_Log::TAG_TICKET_APPROVAL,
			array('userId' => $userId, 'couponId' => $couponId)
		); 
	};

	$isAllLive = (self::COUPON_STATUS_IN_ACCEPTATION_LIVE == $couponStatus);
	if (self::COUPON_STATUS_NEW == $couponStatus
		|| self::COUPON_STATUS_GOING_TO_ACCEPTATION == $couponStatus
		|| self::COUPON_STATUS_INTERRUPTED_ACCEPTATION == $couponStatus) {
		$fnLog('Coupon should not enter confirmation');
		return null;
	}
	else if (self::COUPON_STATUS_MARKED_AS_IN_ACCEPTATION != $couponStatus
		&& self::COUPON_STATUS_IN_ACCEPTATION != $couponStatus
		&& self::COUPON_STATUS_PROLONGED != $couponStatus) {
		$fnLog('Coupon can be accepted - by status');
		return false;
	}


	$timeout = self::getStatusTimeout($couponStatus);
	if ($couponTimestamp < time() - $timeout) {
		$fnLog('Coupon can be accepted - timeout elapsed');
		return false;
	}

	$reason = array();
	if ( It6_Models_User::get($this->userId, 'watched', $db) ) {
		$fnLog('Coupon needs confirmation - watched user');
		$reason[self::CONFIRMATION_REASON_WATCHED] = true;
	}
	if (is_array($this->exceedesRiskLimitPercent)) {
		$fnLog('Coupon needs confirmation - risk limit exceeded');
		$reason[self::CONFIRMATION_REASON_EXCEEDED_RISK_LIMIT] = $this->exceedesRiskLimitPercent;
	}
	else if (is_array($this->willExceedRiskLimit)) {
		$fnLog('Coupon needs confirmation - watched user');
		$reason[self::CONFIRMATION_REASON_WILL_EXCEED_RISK_LIMIT] = $this->willExceedRiskLimit;
	}
	if (!empty($reason))
		return true;
	
	$dbAdmin = Zend_Registry::get('admindb');
	$paramNames = array(
		It6_Models_Parameter::NAME_TICKET_THRESHOLD_SIMPLE,
		It6_Models_Parameter::NAME_TICKET_THRESHOLD_COMBI,
		It6_Models_Parameter::NAME_TICKET_CONFIRM_AFTER_USER_BET_HISTORY_COUNT,
		It6_Models_Parameter::NAME_TICKET_CONFIRM_AFTER_USER_BET_HISTORY_BALANCE,
	);
	$params = It6_Models_Parameter::getDataByName($paramNames, $dbAdmin);
	foreach ($paramNames as $name) {
		if (empty($params[$name]))
		throw new Exception('Required parameter not found. name="' . $name . '"');
	}

	$helperCC = clone $this;
	$helperCC->convertStakesToCentralCurrency($this->userId, $db);
	$helperCC->computeAggregates();
	
	if ($helperCC->checkBetHistory(
		$params[It6_Models_Parameter::NAME_TICKET_CONFIRM_AFTER_USER_BET_HISTORY_COUNT]['value'],
		$params[It6_Models_Parameter::NAME_TICKET_CONFIRM_AFTER_USER_BET_HISTORY_BALANCE]['value'],
		$betData,
		$db
	)) {
		$fnLog('Coupon needs confirmation - bet history');
		$reason = array(self::CONFIRMATION_REASON_BET_HISTORY => $betData);
		return true;
	}
	
	static::assureDbParam($db);
	$betIds = $this->getBetIds();
	$betCount = count($betIds);
	$rows = $db->select()->from(array('e' => 'live_event'), array('limit_bet', 'limit_rate'))
		->join(array('s' => 'live_sazka'), 's.event_id=e.event_id', array('sazka_id'))
		->where('s.sazka_id IN (?)', $betIds)
		->query()
		->fetchAll();
	$liveConfirm = false;
	$liveCount = 0;
	$liveOk = true;
	foreach ($rows as $row) {
		++$liveCount;
		$betId = $row['sazka_id'];
		$bet = $this->getBetById($betId);
		$limitBet = It6_Models_Currency::convertAmountToUserCurrency($this->userId, $row['limit_bet'], $db);
		if ($bet['riskAmount'] > $limitBet)
			$liveOk = false;
		else if ($bet['rate'] > $row['limit_rate'])
			$liveOk = false;
	}
	$isAllLive = ($betCount == $liveCount);
	if (!$liveOk) {
		$fnLog('Coupon needs confirmation - live');
		$reason = array(self::CONFIRMATION_REASON_LIVE => true);
		return true;
	}

	if (!$isAllLive) {
		//TODO: use WS global params
		$limitTicketSimple = It6_Models_Currency::convertAmountToUserCurrency(
			$this->userId, floatval($params[It6_Models_Parameter::NAME_TICKET_THRESHOLD_SIMPLE]['value']), $db
		);
		$limitTicketCombi = It6_Models_Currency::convertAmountToUserCurrency(
			$this->userId, floatval($params[It6_Models_Parameter::NAME_TICKET_THRESHOLD_COMBI]['value']), $db
		);
		if (1 == $betCount && $this->stake > $limitTicketSimple) {
			$fnLog('Coupon needs confirmation - simple stake threshold');
			$reason = array(self::CONFIRMATION_REASON_STAKE => true);
			return true;
		}
		else if ($this->stake > $limitTicketCombi) {
			$fnLog('Coupon needs confirmation - kombi stake threshold');
			$reason = array(self::CONFIRMATION_REASON_STAKE => true);
			return true;
		}
	}

	if ($helperCC->checkApprovalGroups($db)) {
		$fnLog('Coupon needs confirmation - approval groups');
		$reason = array(self::CONFIRMATION_REASON_APPROVAL_GROUP => true);
		return true;
	}

	if ($isAllLive) {
		$liveConfirm = true;
		$fnLog('Coupon needs confirmation - all bets are live');
		$reason = array(self::CONFIRMATION_REASON_LIVE => true);
		return true;
	}
	else {
		$fnLog('Coupon can be accepted');
		return false;
	}
}

/**
 * Takes new total stake and recalculates new stakes in propper manner.
 * This function calls computeAggregates() automatically after recalculations.
 * @param array $modified Data for changes (stakes in user's currency)
array(
	'stake' => total_stake
	//IGNORE: [, 'bets' => array( array('id' => bet_id, 'column' => column_id [, 'stake' => simple_bet_stake] ) [, ...] ) ]
	//IGNORE: [, 'combinations' => array( array('k' => k, 'stake' => combination_stake) [, ...] ) ]
)
 * @param Zend_Db_Adapter $db [optional]
 * @returns string JSON with recalculated values, helper values are computed from particular non-helper values
SIMPLE:
json = {
	bets: [ { id: bet_id, column: col_id, stake: bet_stake } ],
	stake: total_stake, // helper
	win: win_amount, // helper
}

AKO:
json = {
	stake: total_stake,
	win: win_amount, // helper
}

SYSTEM:
json = {
	combinationsStake: combination_stake,
	stake: total_stake, // helper
	win: win_amount, // helper
}

MAXI:
json = {
	combinations: [ { k: k, stake: amount }, ... ], // k from 1..n
	stake: total_stake, // helper
	win: win_amount, // helper
}
 */
public function recalculatePreapproved($modified, $db = null) {
	static::assureDbParam($db);
	$currencyId = $this->currencyId;
	$csc = It6_Models_Currency::get($currencyId, $this->cash || ROUND_STAKES_ALWAYS_AS_CASH ? 'smallestCash' : 'smallestUnit', $db);
	$recompute = false;
	$approvedTotal = (empty($modified['stake']) ? 0 : floatval($modified['stake']));
	$approvedTotal = $this->roundStake($approvedTotal);
	if (!empty($approvedTotal)) {
		$amounts = array();
		switch ($this->type) {
		case self::TYPE_SIMPLE:
			foreach ($this->bets as $bet)
				$amounts[] = new It6_Amount($bet['amount'], 1, array($bet['id'], $bet['column']));
			break;
		case self::TYPE_COMBI:
			$amounts[] = new It6_Amount($this->stake, 1);
			break;
		case self::TYPE_SYSTEM:
			$n = $this->getGroupCount(false);
			$na = 0;
			$stake = false;
			foreach ($this->combinations as $k => $comb) {
				if (!$this->isCombinationUsed($k))
					continue;
				$na += It6_Array::getCombinationCount($n, $k);
				if (false === $stake)
					$stake = $comb['stake'];
				else if ($stake != $comb['stake'])
					throw new Exception('System ticket stakes differ');
			}
			$amounts[] = new It6_Amount($stake, $na);
			break;
		case self::TYPE_MAXI:
			$n = $this->getGroupCount(false);
			foreach ($this->combinations as $k => $comb) {
				if (!$this->isCombinationUsed($k))
					continue;
				$na = It6_Array::getCombinationCount($n, $k);
				$amounts[] = new It6_Amount($comb['stake'], $na, $k);
			}
			break;
		default:
			throw new Exception('Unknown ticket type. type=' . $this->type);
		}
		$amounts = new It6_Amounts($amounts);
		//TODO: use global parameters for stake limits (can be different for ticket types)
		$resultTotal = $amounts->setTotal($approvedTotal, 0, 1000000, $csc);
		$result = array();
		switch ($this->type) {
		case self::TYPE_SIMPLE:
			$data = array();
			$result['bets'] = array();
			foreach ($amounts as $amount) {
				$data['amount'] = $amount->amount;
				$this->setBetData($amount->id, $data);
				$result['bets'][] = array(
					'id' => $amount->id[0],
					'column' => $amount->id[1],
					'stake' => $amount->amount
				);
			}
			break;
		case self::TYPE_COMBI:
			$this->stake = $amounts[0]->amount;
			break;
		case self::TYPE_SYSTEM:
			$amount = $amounts[0]->amount;
			foreach ($this->combinations as $k => &$comb) {
				if (!$this->isCombinationUsed($k))
					continue;
				$comb['stake'] = $amount;
			}
			$result['combinationsStake'] = $amount;
			break;
		case self::TYPE_MAXI:
			$n = $this->getGroupCount(false);
			$result['combinations'] = array();
			foreach ($amounts as $amount) {
				foreach ($this->combinations as $k => &$comb) {
					if (!$this->isCombinationUsed($k) || $amount->id != $k)
						continue;
					$comb['stake'] = $amount->amount;
					$result['combinations'][] = array('k' => $k, 'stake' => $amount->amount);
					break;
				}
			}
			break;
		default:
			break;
		}
		$recompute = true;
	}

	if ($recompute)
		$this->computeAggregates();
	$result['stake'] = $this->stake;
	$result['win'] = $this->win;
	return Zend_Json::encode($result);
}

/**
 * Applies preapproved changes to this coupon.
 * This function doesn't call computeAggregates().
 * @param array|string $preapproved JSON or array with changes (@see It6_Models_Ticket::recalculatePreapproved() return value)
 */
public function mergePreapproved($preapproved) {
	$changes = (is_array($preapproved) ? $preapproved : Zend_Json::decode($preapproved));
	switch ($this->type) {
	case self::TYPE_SIMPLE:
		$data = array();
		foreach ($changes['bets'] as $betChange) {
			$data['amount'] = $betChange['stake'];
			$this->setBetData(array($betChange['id'], $betChange['column']), $data);
		}
		break;
	case self::TYPE_COMBI:
		$this->stake = $changes['stake'];
		break;
	case self::TYPE_SYSTEM:
		$stake = $changes['combinationsStake'];
		foreach ($this->combinations as $k => &$comb) {
			if (!$this->isCombinationUsed($k))
				continue;
			$comb['stake'] = $stake;
		}
		break;
	case self::TYPE_MAXI:
		$n = $this->getGroupCount(false);
		$result['combinations'] = array();
		foreach ($changes['combinations'] as $chComb) {
			$chK = $chComb['k'];
			foreach ($this->combinations as $k => &$comb) {
				if (!$this->isCombinationUsed($k) || $chK != $k)
					continue;
				$comb['stake'] = $chComb['stake'];
				break;
			}
		}
		break;
	default:
		throw new Exception('Unknown ticket type');
	}
}

/**
 * Translate messages in result structure returned from checkXXX function
 * @param array $result Structure that will be translated
 */
public function translateCheckResult(array &$result) {
	if (!$result['result']) {
		if (!is_array($result['message']))
			$result['message'] = self::trans($result['message']);
		else
			$result['message'] = self::transParam($result['message'][0], $result['message'][1]);
		if (!empty($result['bet'])) {
			foreach ($result['bet'] as &$err) {
				if (!is_array($err['message']))
					$err = self::trans($err['message']);
				else
					$err = self::transParam($err['message'][0], $err['message'][1]);
			}
		}
	}
}


/**
 * Check count of bets groups etc.
 * @returns array(error_code, error_number) where error_code is one of CHECK_* class constanst
 *					and error_number is limit value not pased (eg. used for error message).
 */
public function checkCounts(&$db = null) {

	if (0 == sizeof($this->bets))
		return array(self::CHECK_ERROR_NO_BETS, false);

	static::assureDbParam($db);

	$minNonTGroups = false;
	$maxGroups = false;

	$maxBetsAll = It6_Models_TicketValidator::getAllMaxBetNums();
	$maxBets = $maxBetsAll[$this->type];

	switch ($this->type) {
	case self::TYPE_SYSTEM:
		$minNonTGroups = 3;
		break;
	case self::TYPE_MAXI:
		$minNonTGroups = 2;
		$maxGroups = $maxBetsAll[It6_Models_Parameter::NAME_TICKET_MAX_GROUPS_MAXIKOMBI];
		break;
	default:
		break;
	}

	if (sizeof($this->bets) > $maxBets)
		return array(self::CHECK_ERROR_MAX_BETS, $maxBets);

	if (false !== $maxGroups && $this->getGroupCount(false) > $maxGroups)
		return array(self::CHECK_ERROR_MAX_GROUPS, $maxGroups);

	if (false !== $minNonTGroups && $this->getGroupCount(false) < $minNonTGroups)
		return array(self::CHECK_ERROR_MIN_NON_T, $minNonTGroups);

	return array(self::CHECK_OK, false);
}

/**
 * Checks amount if it is valid value
 * @param numeric $amount Amount to be checked
 * @param numeric $lowLimit
 * @param numeric $highLimit
 * @param string $currencyName [optional] Currency name optionally appended to error message param
 * @param int $errNumZero [optional] error number for zero stake, default 3
 * @param int $errNumLow [optional] error number for too low stake, default 17
 * @param int $errNumHigh [optional] error number for too high stake, default 2
 * @return boolean|array TRUE or standard error message data array('message' => string|array [, 'recommendedValue' => mixed])
 *															(Messages are untranslated resources)
 */
private function checkStake($amount, $lowLimit = null, $highLimit = null, $currencyName = null, $errNumZero = null, $errNumLow = null, $errNumHigh = null) {
	if (!isset($errNumZero))
		$errNumZero = 3;
	if (!isset($errNumLow))
		$errNumLow = 17;
	if (!isset($errNumHigh))
		$errNumHigh = 2;
	if (empty($amount) || !is_numeric($amount) || 0 >= $amount)
		return array('message' => 'ticket_er_'. $errNumZero);
	else if (isset($lowLimit) && $amount < $lowLimit)
		return array(
			'message' => array('ticket_er_' . $errNumLow, $lowLimit . (empty($currencyName) ? '' : ' ' . $currencyName)),
			'recommendedValue' => $lowLimit,
		);
	else if (isset($highLimit) && $amount > $highLimit)
		return array(
			'message' => array('ticket_er_' . $errNumHigh, $highLimit . (empty($currencyName) ? '' : ' ' . $currencyName)),
			'recommendedValue' => $highLimit,
		);
	else
		return true;
}

/**
 * Check if stake is present and meaningful value.
 * @param float $minStake Low limit for ticket stake (in central currency)
 * @param float $maxStake Max limit for ticket stake (in central currency)
 * @return array( 'result' => TRUE|FALSE [, 'message' => error_message [, 'bet' => array(betId => (colId => error_message), ...), ... ]] )
 */
public function checkStakes($minStake, $maxStake, $minStakeCombination = false, &$db = null) {
	$result = array('result' => true);
	$currencyName = $this->getCurrencyName($db);
	$centralCurrencyId = It6_Models_Currency::getCentralCurrencyId($db);
	$minStake = $this->convertAmountToTicketCurrency($minStake, $centralCurrencyId, $db);
	$minStake = $this->roundStake($minStake, null, $db);
	$maxStake = $this->convertAmountToTicketCurrency($maxStake, $centralCurrencyId, $db);
	$maxStake = $this->roundStake($maxStake, null, $db);
	if (self::TYPE_MAXI == $this->type) {
		$minStakeCombination = $this->convertAmountToTicketCurrency($minStakeCombination, $centralCurrencyId, $db);
		$minStakeCombination = $this->roundStake($minStakeCombination, null, $db);
	}
	if (self::TYPE_SIMPLE == $this->type) {
		$errBetIds = array();
		foreach ($this->bets as $bet) {
			$check = $this->checkStake($bet['amount'], $minStake, $maxStake, $currencyName);
			if (true !== $check)
				$betErrs[$bet['id']][$bet['column']] = $check;
		}
		if (!empty($betErrs)) {
			$result['result'] = false;
			$result['bet'] = array();
			foreach ($betErrs as $betId => $cols)
				foreach ($cols as $colId => $msg)
					$result['bet'][$betId][$colId] = $msg;
		}
	}
	else { //if (self::TYPE_COMBI == $this->type) {
		if (self::TYPE_MAXI == $this->type) {
			foreach ($this->combinations as $k => $comb) {
				if (!$this->isCombinationUsed($k))
					continue;
				$check = $this->checkStake($comb['stake'], $minStakeCombination, null, $currencyName, null, 30, null);
				if (true !== $check) {
					$result = $check;
					$result['result'] = false;
					break;
				}
			}
		}
		if (true === $result['result']) {
			$check = $this->checkStake($this->stake, $minStake, $maxStake, $currencyName);
			if (true !== $check) {
				$result = $check;
				$result['result'] = false;
			}
		}
	}
	return $result;
}

/**
 * Test if ticket contents forbidden combination(s) of bets.
 * @return array( 'result' => TRUE|FALSE [, 'message' => error_message [, 'bets' => array(betId => (coldId => error_message), ...), ... ]] )
 */
public function checkCorrelatedBets(&$db = null) {
	$result = array('result' => true);
	$doTest = false;
	if (self::TYPE_COMBI == $this->type)
		$doTest = true;
	else if (self::TYPE_SYSTEM == $this->type || self::TYPE_MAXI == $this->type) {
		foreach ($this->combinations as $k => $comb) {
			// when only k = 1 used, then ticket is equivalent to set of simple tickets (no correlations)
			if ($this->isCombinationUsed($k) && $k > 1) {
				$doTest = true;
				break;
			}
		}
	}
	if ($doTest) {
		static::assureDbParam($db);
		$betIds = $this->getBetIds($db);
		$simpleBets = It6_Models_Bet::readSimpleBets($betIds, $db);
		if (!empty($simpleBets)) {
			$result = array('result' => false, 'bet' => array());
			$msg = 'ticket_er_4';
			foreach ($this->bets as $bet) {
				if (in_array($bet['id'], $simpleBets))
					$result['bet'][$bet['id']][$bet['column']]['message'] = $msg;
			}
		}
		$correlatedBets =	It6_Models_Bet::readCorrelatedBets($betIds, $db);
		if (!empty($correlatedBets)) {
			if ($result['result'])
				$result = array('result' => false, 'bet' => array());
			$msg = 'ticket_er_4';
			foreach ($this->bets as $bet) {
				if (in_array($bet['id'], $correlatedBets))
					$result['bet'][$bet['id']][$bet['column']]['message'] = $msg;
			}
		}
	}
	return $result;
}

/**
 * Check user's limit on stake(s).
 * @return array( 'result' => TRUE|FALSE [, 'message' => error_message [, 'betId' => array(betId => (colId => error_message), ...), ... ]] )
 * TODO: ? move query to It6_Models_User ?
 */
public function checkUserLimit($userId = null, &$db = null) {
	if (!isset($userId))
		$userId = $this->userId;
	static::assureDbParam($db);

	$centralCurrencyId = It6_Models_Currency::getCentralCurrencyId($db);
	$maxStake = $this->convertAmountToTicketCurrency(
		It6_Models_User::get($userId, 'maxBet', $db),
		$centralCurrencyId,
		$db
	);

	$result = array('result' => true);
/*
	if ($this->isMaxicombinatorCompatible()) {
		if (!empty($this->combinations)) {
			$errCombs = array();
			foreach ($this->combinations as $k => $comb) {
				if (!$this->isCombinationUsed($k))
					continue;
				if ($comb['stake'] > $maxStake)
					$errCombs[$k] = true;
			}
			if (!empty($errCombs)) {
				$gc = $this->getGroupCount(false);
				$combList = '';
				foreach ($errCombs as $k => $dummy) {
					if (!empty($combList))
						$combList .= ',';
					$combList .= $k . '/' . $gc;
				}
				$result = array(
					'result' => false,
					'message' => array('ticket_er_2', $maxStake . ' ' . $this->getCurrencyName($db)),
					'recommendedValue' => $maxStake,
				);
			}
		}
	}
	else if (self::TYPE_SIMPLE == $this->type) {
*/
	if (self::TYPE_SIMPLE == $this->type) {
		foreach ($this->bets as $bet) {
			if ($bet['amount'] > $maxStake) {
				if ($result['result']) {
					$result = array('result' => false, 'bet' => array());
					$msg = array('ticket_er_2', $maxStake . ' ' . $this->getCurrencyName($db));
				}
				$result['bet'][$bet['id']][$bet['column']] = array('message' => $msg, 'recommendedValue' => $maxStake);
			}
		}
	}
	else {
		if ($this->stake > $maxStake) {
			$result = array(
				'result' => false,
				'message' => array('ticket_er_2', $maxStake . ' ' . $this->getCurrencyName($db)),
				'recommendedValue' => $maxStake,
			);
		}
	}
	return $result;
}

/**
 * Check user-limit setting
 * @return array( 'result' => TRUE|FALSE [, 'message' => error_message [, 'betId' => array(betId => (colId => error_message), ...), ... ]] )
 */
public function checkUserLimitSetting($userId = null, &$db = null) {
	if (!isset($userId)) $userId = $this->userId;
	static::assureDbParam($db);
	$result = array('result' => true);

	$centralCurrencyId = It6_Models_Currency::getCentralCurrencyId($db);

	$userLimit = Webservice_SettingLimits::getUserLimits($userId);
	if (!empty($userLimit)) {
		$limit = $this->convertAmountToTicketCurrency(
			$userLimit->limitAmount,
			$centralCurrencyId,
			$db
		);
		$actual = $this->convertAmountToTicketCurrency(
			$userLimit->actualAmount + $this->stake,
			$centralCurrencyId,
			$db
		);
		if ($actual > $limit) {
			$result = array(
				'result' => false,
				'message' => array('ticket_er_32', $limit.' '.$this->getCurrencyName($db)),
				'recommendedValue' => $limit,
			);
		}
	}
	return $result;
}

/**
 * Checks if this ticket has hashes duplicit to those in DB.
 * @return array Counts of ticket's hashes in DB, array(array('subject' => hashed_subject_name, 'hash' => hash, 'count' => hash_count), ...).
 */
public function getTicketHashCounts(&$db = null) {
	if (empty($this->hashes))
		throw new Exception('No ticket hashes computed');
	static::assureDbParam($db);
	try {
		$hashes =
		$rows = $db->select()
			->from( 'tickethash', array('h' => 'tickethash', 'c' => 'tickethash_count'))
			->where('tickethash IN (?)', array_values($this->hashes))
			->where('user_id=?', $this->userId)
			->query()
			->fetchAll();
		$counts = array();
		foreach ($rows as $row)
			$counts[ $row['h'] ] = $row['c'];
		$result = array();
		foreach ($this->hashes as $subject => $hash)
			$result[] = array(
				'subject' => $subject,
				'hash' => $hash,
				'count' => (empty($counts[$hash]) ? 0 : $counts[$hash])
		);
		return $result;
	}
	catch (Exception $e) {
		throw new Exception($e);
	}
}

/**
 * Checks bets' AKO constraints.
 * @param $akoMinRate Minimal rate for bet to be counted into AKO count
 */
public function checkBetsAko($akoMinRate) {
	$result = array('result' => true);
	if (self::TYPE_SIMPLE == $this->type || self::TYPE_COMBI == $this->type) {
		if (self::TYPE_COMBI == $this->type) {
			$count = 0;
			foreach ($this->bets as $bet) {
				if ($bet['rate'] >= $akoMinRate) {
					++$count;
				}
			}
		}
		else {
			$count = 1;
		}
		foreach ($this->bets as $bet) {
			$ako = $bet['ako'];
			if (false === $ako)
				continue;
			if ($ako > $count) {
				if ($result['result']) {
					$result = array('result' => false, 'bet' => array());
					$msg = 'ticket_er_13';
				}
				$result['bet'][$bet['id']][$bet['column']]['message'] = array($msg, array($ako, $akoMinRate));
			}
		}
		return $result;
	}
	else if (self::TYPE_SYSTEM == $this->type || self::TYPE_MAXI == $this->type) {
		foreach ($this->combinations as $k => $comb) {
			if (!$this->isCombinationUsed($k))
				continue;
			foreach ($comb['data'] as $data) {
				$count = 0;
				$bets = array();
				foreach ($data['bets'] as $betId) {
					$bet = $this->getBet($betId, null);
					if (1 != count($bet))
						throw new Exception('Same bet more times on this ticket forbidden');
					$bet = $bet[0];
					if ($bet['rate'] >= $akoMinRate) {
						++$count;
					}
					$bets[] = $bet;
				}
				foreach ($bets as $bet) {
					$ako = $bet['ako'];
					if (false === $ako)
						continue;
					if ($ako > $count) {
						if ($result['result']) {
							$result = array('result' => false, 'bet' => array());
							$msg = 'ticket_er_13';
						}
						$result['bet'][$bet['id']][$bet['column']]['message'] = array($msg, array($ako, $akoMinRate));
					}
				}
			}
		}
		return $result;
	}
}

/**
 * Fetches approval group data for each bet on ticket.
 * @returns array(
 *		'bets' => array( betId1 => approvalGroupIdForBet1, ...),
 *		'approvalGroups' => array(
 *			 approvalGroupId => instance of It6_Models_ApprovalGroup,
 *			 ...
 *		)
 * );
 */
private function getBetsApprovalGroups(&$db = null) {
	static::assureDbParam($db);
	$betIds = $this->getBetIds();
	$data = array(
		'bets' => array(),
		'approvalGroups' => array()
	);
	if (!empty($betIds)) {
		$rows = $db->select()
			->from(array('b' => 'sazky'), array('b_id' => 'sazka_id'))
			->join(array('u' => 'udalost'), 'b.udalost_id=u.udalost_id', array('u_id' => 'udalost_id', 'u_ag' => 'approval_group_id'))
			->join(array('s' => 'sport'), 'u.sport_id=s.sport_id', array('s_id' => 'sport_id', 's_ag' => 'approval_group_id'))
			->where('sazka_id IN (?)', $betIds)
			->query()
			->fetchAll();
		$agIds = array();
		foreach ($rows as $row) {
			$agId = null;
			if (!empty($row['u_ag']))
				$agId = $row['u_ag'];
			else if (!empty($row['s_ag']))
				$agId = $row['s_ag'];
			if (!empty($agId)) {
				$data['bets'][$row['b_id']] = $agId;
				if (!array_key_exists($agId, $agIds))
					$agIds[] = $agId;
			}
		}
		if (!empty($agIds)) {
			$dbData = It6_Models_ApprovalGroup::readApprovalGroup($agIds, $db);
			if (!empty($dbData)) {
				foreach ($dbData as $agId => $agData) {
					$data['approvalGroups'][$agId] = new It6_Models_ApprovalGroup($agId, $agData);
				}
			}
		}
	}
	return $data;
}

/**
 * Check each stake to be in limit given by approval group.
 * Stakes have to be in central currency!
 * @returns TRUE|FALSE
 */
public function checkApprovalGroups(&$db = null) {
	$data = $this->getBetsApprovalGroups($db);
	foreach ($data['bets'] as $betId => $agId) {
		if (array_key_exists($agId, $data['approvalGroups'])) {
			$bets = $this->getBet($betId, null);
			foreach ($bets as $bet) {
				$ag = &$data['approvalGroups'][$agId];
				if ($ag->toBeApproved($bet['rate'], $bet['riskAmount'])) {
					It6_Log::debug(
						"To be approved: agId '%agId%'",
						It6_Log::TAG_TICKET_APPROVAL,
						array('betId' => $betId, 'betRate' => $bet['rate'],'riskAmount' => $bet['riskAmount'],'agId' => $agId)
					);
					return true;
				}
			}
		}
	}
	return false;
}

/**
 * Checks if coupon needs confirmation according to user bet history.
 * This method requires conputeAggregates() to be called before.
 * @param integer $allowedBetTicketCount Maximal count of user bet tickets in history to bypass confirmation
 * @param float $allowedBetStakeBalance Maximal user bet stake balance from history to bypass confirmation
 * @param array $betData Will receive info for bets with invalid user history (betId => struct). Struct fields: ticketCount, stakeBalance 
 * @param Zend_Db_Adapter $db
 * @return boolean TRUE if coupon needs confirmation, FALSE otherwise
 */
public function checkBetHistory($allowedBetTicketCount, $allowedBetStakeBalance, &$betData = null, &$db = null) {
	static::assureDbParam($db);
	$betData = array();
	$betIds = $this->getBetIds();
	if (It6_Models_User::get($this->userId, 'anonymous', $db)) {
		foreach ($betIds as $betId) {
			$count = It6_Models_Bet::getCountLastFiveMinuts($this->userId, $betId, $db);
			$count = It6_ArrayWrapper::toNativeArray($count);
			if ($count["count"] >= $allowedBetTicketCount) return true;
		}
	} else {
		$histories = It6_Models_Bet::getUserHistory($this->userId, $betIds, $db);
		$historyUpdates = array();
		foreach ($betIds as $betId) {
			if (isset($histories[$this->userId][$betId])) {
				$history = &$histories[$this->userId][$betId];
				$newTicketCount = $history['ticketCount'];
				$newStakeBalance = $history['stakeBalance'];
			}
			else {
				$newTicketCount = 0;
				$newStakeBalance = 0.0;
			}
			$historyUpdate = $this->getDataForBetUserHistory($betId);
			$newTicketCount += $historyUpdate['ticketCount'];
			$newStakeBalance += $historyUpdate['stakeBalance'];
			if ($newTicketCount > $allowedBetTicketCount && $newStakeBalance > $allowedBetStakeBalance) {
				$betData[$betId] = array(
					'ticketCount' => $newTicketCount,
					'stakeBalance' => $newStakeBalance,
				);
			}
		}
	}
	return !empty($betData);
}

/**
 * Checks if rates has changed
 * @param int $userId [optional] Owner of ticket
 * @param int|NULL $timestamp [optional] Rates are queried to specified timestamp, NULL for current timestamp
 * @returns
 */
public function checkRateChanges($userId = null, $timestamp = null, &$db = null) {
	static::assureDbParam($db);
	if (empty($this->bets))
		return true;
	if (!isset($userId))
		$userId = $this->user_id;
	if (!isset($timestamp))
		$timestamp = time();
	$validTo = It6_Date::timestampToDb($timestamp);
	$result = array('result' => true);
	$select = $db->select()->from('sazka_kurz',	array(
		'betId' => 'sazka_id',
		'columnId' => 'sloupec_id',
		'rate' => 'kurz',
		'validFrom' => 'platny_od',
	));
	$where = array();
	foreach ($this->bets as &$bet) {
		$where[] = '(sazka_id=' . intval($bet['id']) . ' AND sloupec_id=' . intval($bet['column']) . ')';
	}
	$select->where(implode(' OR ', $where))->where('platny_od<=?', $validTo);
	$rows = $select->query()->fetchAll();
	$rates = array();
	foreach ($rows as $row) {
		$betId = $row['betId'];
		$colId = $row['columnId'];
		$validFrom = $row['validFrom'];
		$update = false;
		if (isset($rates[$betId][$colId])) {
			if ($rates[$betId][$colId]['validFrom'] < $validFrom) {
				$update = true;
			}
		}
		else {
			$update = true;
		}
		if ($update) {
			$rates[$betId][$colId] = array('rate' => $row['rate'], 'validFrom' => $validFrom);
		}
	}
	foreach ($this->bets as &$bet) {
		$betId = $bet['id'];
		$colId = $bet['column'];
		if (!isset($rates[$betId][$colId]))
			continue;
		$rate = $this->roundRate($rates[$betId][$colId]['rate']);
		if ($rate != $bet['rate']) {
			if ($result['result']) {
				$result = array(
					'result' => false,
					'bet' => array()
				);
			}
			$result['bet'][$betId][$colId] = array(
				'message' => array('ticket_er_12', $bet['rate'] . ' -> ' . $rate),
				'type' => 'warning',
				'actions' => array(array('name' => 'rateChanged', 'betId' => $betId, 'columnId' => $colId, 'rate' => $rate)),
			);

//			$result['script'] .= "\ntick.changeBetRate($betId, $colId, $rate);\n"
//				. "tick.bet[$betId][$colId]['rate'] = parseFloat(rate).toFixed(2);\n"
//				. "tick.bet[$betId][$colId]['rate2'] = rate.toString();\n"
//				. "tick.UpdateAll();\n";
			$bet['rate'] = $rate;
		}
	}
	if (!$result['result'])
		$this->computeAggregates();
	return $result;
}

private function getWinLimitValue($limits, $betCount, array $limitCache = null, &$db = null) {
	if (isset($limitCache) && array_key_exists($betCount, $limitCache))
		return $limitCache[$betCount];
	$limit = null;
	foreach ($limits as $interval) {
		if ($interval[0] <= $betCount) {
			if (!isset($limit))
				$limit = $interval;
			else if ($interval[0] > $limit[0])
				$limit = $interval;
		}
	}
	if (!isset($limit))
		throw new Exception('Limit for max.win not found');
	else {
		$centralCurrencyId = It6_Models_Currency::getCentralCurrencyId($db);
		$limit = $this->convertAmountToTicketCurrency($limit[1], $centralCurrencyId, $db);
		$limit = $this->roundStake($limit, null, $db);
		if (isset($limitCache))
			$limitCache[$betCount] = $limit;
		return $limit;
	}
}

/**
 * Compute maximal possible stake to not exceed given netto win
 * @param float $win Upper (inclusive) limit of netto win
 * @param float $rate Just rate
 * @return float New stake
 */
private function computeStakeFromNettoWin($win, $rate, &$db = null) {
	if ($rate <= 1.0)
		return 0.0;
	$r = $rate - 1;
	$_s = $win / $r;
	$s = $this->roundStake($_s, null, $db);
	if ($s * $r > $win) {
		$cash = (ROUND_STAKES_ALWAYS_AS_CASH || $this->cash);
		$s = It6_Models_Currency::round(
			$_s, It6_Models_Currency::ROUND_DOWN, $this->currencyId, It6_Models_Currency::ROUND_PARAM_CURRENCY, $cash, $db
		);
	}
	return $s;
}

/**
 * Checks ticket win amount(s)
 * @param float|array $limit Maximal value in central currency or intervals of bet count and their max.values: array(array(minBetCount, maxWin), ...)
 * @param float $limitMaxikombi Maximal value in central currency for total netto win of maxicombinator (can be NULL if ticket is not maxicombinator)
 * @param Zend_Db_Adapter $db [optional]
 * @return Standard validation result array
 */
public function checkMaxWin($limits, $limitMaxikombi = null, &$db = null) {
	if (is_numeric($limits))
		$limits = array(array(1, $limits));
	$currencyName = ' ' . $this->getCurrencyName($db);
	$result = array('result' => true);
	if ($this->isMaxicombinatorCompatible()) {
		if (!empty($this->combinations)) {
			$limitCache = array();
			$errCombs = array();
			foreach ($this->combinations as $k => $comb) {
				if (!$this->isCombinationUsed($k))
					continue;
				foreach ($comb['data'] as $c) {
					$limit = $this->getWinLimitValue($limits, count($c['bets']), $limitCache, $db);
					$netto = $c['win'] - $comb['stake'];
					if ($netto > $limit)
						$errCombs[$k] = true;
				}
			}
			if (!empty($errCombs)) {
				$gc = $this->getGroupCount(false);
				$combList = '';
				foreach ($errCombs as $k => $dummy) {
					if (!empty($combList))
						$combList .= ',';
					$combList .= $k . '/' . $gc;
				}
				$result = array(
					'result' => false,
					'message' => array('ticket_er_24', $this->formatStake($limit, $db) . $currencyName . " ($combList)"),
					'recommendedValue' => $limit,
				);
			}
		}
		if (true === $result['result']) {
			$centralCurrencyId = It6_Models_Currency::getCentralCurrencyId($db);
			$limitMaxikombi = $this->convertAmountToTicketCurrency($limitMaxikombi, $centralCurrencyId, $db);
			$limitMaxikombi = $this->roundStake($limitMaxikombi, null, $db);
			if ($this->win - $this->stake > $limitMaxikombi) {
				$result = array(
					'result' => false,
					'message' => array('ticket_er_24', $this->formatStake($limitMaxikombi, $db) . $currencyName),
					'recommendedValue' => $limitMaxikombi, 'type' => 'warning',
					//'actions' => array(array(
					//	'name' => 'stakeChanged',
					//	'stake' => $this->computeStakeFromNettoWin($limit, $this->rate, $db),
					//)),
				);
			}
		}
	}
	else if (self::TYPE_SIMPLE == $this->type) {
		if (!empty($this->bets)) {
			$limit = $this->getWinLimitValue($limits, 1, null, $db);
			foreach ($this->bets as $bet) {
				$netto = $bet['win'] - $bet['amount'];
				if ($netto > $limit) {
					if ($result['result'])
						$result = array('result' => false, 'bet' => array());
					$result['bet'][$bet['id']][$bet['column']] = array(
						'message' => array('ticket_er_24', $this->formatStake($limit, $db) . $currencyName),
						'recommendedValue' => $limit, 'type' => 'warning',
						'actions' => array(array(
							'name' => 'betStakeChanged', 'bet' => $bet['id'], 'column' => $bet['column'],
							'stake' => $this->computeStakeFromNettoWin($limit, $bet['rate'], $db),
						)),
					);
				}
			}
		}
	}
	else if (self::TYPE_COMBI == $this->type) {
		$limit = $this->getWinLimitValue($limits, count($this->bets), null, $db);
		if ($this->win - $this->stake > $limit) {
			$result = array(
				'result' => false,
				'message' => array('ticket_er_24', $this->formatStake($limit, $db) . $currencyName),
				'recommendedValue' => $limit, 'type' => 'warning',
				'actions' => array(array(
					'name' => 'stakeChanged',
					'stake' => $this->computeStakeFromNettoWin($limit, $this->rate, $db),
				)),
			);
		}
	}
	else
		throw new Exception('Unknown ticket type: ' . $this->type);
	return $result;
}
/**
 * @return array( sportId => array(sportId=>, name=>, bets=>array(bet IDs), riskAmount=>) )
 */
public function getSportData($clearCache = false, &$db = null) {
	static::assureDbParam($db);
	if (false === $this->sportData || $clearCache) {
		$this->sportData = array();
		$betIds = $this->getBetIds();
		if (!empty($betIds)) {
			$rows = $db->select()
				->from( array('s'=>'sazky'), array('sazka_id') )
				->join( array('u'=>'udalost'), 's.udalost_id=u.udalost_id', array() )
				->join( array('sp' => 'sport'), 'u.sport_id=sp.sport_id', array('sport_id', 'nazev') )
				->where('s.sazka_id IN (?)', $betIds)
				->query()
				->fetchAll();
		}
		foreach ($rows as $row) {
			$sportId = $row['sport_id'];
			$betId = $row['sazka_id'];
			$bets = $this->getBet($betId, null);
			if (!array_key_exists($sportId, $this->sportData))
				$this->sportData[$sportId] = array(
					'sportId' => $row['sport_id'],
					'name' => $row['nazev'],
					'bets' => array($betId),
					'riskAmount' => 0.0,
				);
			else
				$this->sportData[$sportId]['bets'][] = $betId;
			foreach ($bets as $bet)
				$this->sportData[$sportId]['riskAmount'] += $bet['riskAmount'];
		}
		foreach ($this->sportData as &$data)
			$data['riskAmount'] = $this->roundStake($data['riskAmount'], $this->cash, $db);
	}
	return $this->sportData;
}

/**
 * Ten kdo napise takovouhle dokumentaci, tak at se nes*re do programovani:
 * Returns ticket statistics data
 */
public function getStatistics($clearCache = false, &$db = null) {
	static::assureDbParam($db);
	if (false === $this->statistics || $clearCache) {

		$this->statistics = array();
		$betIds = $this->getBetIds();

		if ( !empty($betIds) ) {
			$query = $db->select()
				->from( array('s'=>'sazky'), null )
				->join( array('u'=>'udalost'), 's.udalost_id=u.udalost_id', null )
				->columns(array(
					'betId' => 's.sazka_id',
					'typeId' => 's.typ_id',
					'eventId' => 'u.udalost_id',
					'sportId' => 'u.sport_id',
				))
				->where('s.sazka_id IN (?)', $betIds)
				->query();

			$_ = array();
			$losts = array();
			$wins = array();
			while ( $row = $query->fetchObject() )	{

				if ( !array_key_exists($row->sportId, $_) )
					$_[$row->sportId] = array();
				if ( !array_key_exists($row->eventId, $_[$row->sportId]) )
					$_[$row->sportId][$row->eventId] = array();
				if ( !array_key_exists($row->typeId, $_[$row->sportId][$row->eventId]) )
					$_[$row->sportId][$row->eventId][$row->typeId] = array(
						'sportId' => $row->sportId,
						'eventId' => $row->eventId,
						'typeId' => $row->typeId,
						'amount' => 0,
						'won' => 0,
						'win' => 0,
						'count' => 0,
						'wonCount' => 0,
						'lostCount' => 0,
						'winTipCount' => 0,
						'lostTipCount' => 0
					);

				$item = &$_[$row->sportId][$row->eventId][$row->typeId];

				foreach ( $this->getBet($row->betId, null) as $bet ) {

					$item['amount'] += $bet['riskAmount'];
					if ( !empty($bet['win']) && !is_nan($bet['win']) ) {
						$item['win'] += $bet['win'];
					}
					$item['count'] = 1;
					if ( !empty($bet['won']) && !is_nan($bet['won']) ) {
						++$item['winTipCount'];
						$item['won'] += $bet['won'];
						if ( !in_array($bet['group'],$wins) ) {
							++$item['wonCount'];
							$wins[] = $bet['group'];
						}
					}
					else {
						++$item['lostTipCount'];
						if ( !in_array($bet['group'],$losts) ) {
							++$item['lostCount'];
							$losts[] = $bet['group'];
						}
					}
				}

				unset($item);
			}
			foreach ( $_ as $sportId => $v1 )
				foreach ( $v1 as $eventId => $v2 )
					foreach ( $v2 as $typeId => $item )
						$this->statistics[] = $item;
		}
	}
	return $this->statistics;
}

/**
 * This implementation relyes on fact, that risk amounts are distributed evenly between groups and bets.
 * Group_risk_amount = Stake_on_combination / Number_of_groups_in_combination
 * Bet_risk_amount = Sum_over_groups_with_bet( Group_risk_amount / Number_of_bets_in_group )
 * @param $betId ID of bet where riskAmount was over limit at most
 * @param $colId ID of bet where riskAmount was over limit at most
 * @param $newRiscAmount New risk amount for given bet
 * @param $currency [optional] @see It6_Models_Currency::round()
 * @return array('stake' => totalStake, 'combinations' => array(1 => stake_1, ..., n => stake_n ) Computed stakes to match change.
 */
public function computeStakesFromChangedBetRiscAmount($betId, $colId, $newRiscAmount, $currency = null) {
	$bet = $this->getBet($betId, $colId);
	$ratio = $newRiscAmount / $bet['riskAmount'];
	$newStakes = array(
		'stake' => It6_Models_Currency::round($this->stake * $ratio, It6_Models_Currency::ROUND_DOWN,
			$this->currencyId, It6_Models_Currency::ROUND_PARAM_CURRENCY, $this->cash),
		'combinations' => array()
	);
	if (!empty($this->combinations)) {
		foreach ($this->combinations as $k => $comb) {
			if ($this->isCombinationUsed($k))
				$newStakes['combinations'][$k] = $comb['stake'] * $ratio;
		}
	}
	return $newStakes;
}

/**
 * Updates bets 'won' and 'win'
 * TODO: recheck implementation for simple
 * bet's won part = (betRate / sum_of_betRates) * totalWon
 * for win analogically
 */
public function computeBetWonAndWinDistribution() {
	switch ($this->type) {
	case self::TYPE_SIMPLE:
		foreach ($this->bets as $betId => &$bet) {
			$bet['win'] = $bet['rate'] * $bet['amount'];
			//TODO add multisimple support
			$bet['won'] = $this->won;
		}
		break;
	case self::TYPE_COMBI:
		foreach ($this->bets as $betId => &$bet) {
			$bet['win'] = $this->win * ($bet['rate'] / $this->rateSum);
			$bet['won'] = $this->won * ($bet['rate'] / $this->rateSum);
		}
		break;
	case self::TYPE_SYSTEM:
	case self::TYPE_MAXI:
		foreach ($this->bets as &$bet)
			$bet['win'] = $bet['won'] = 0.0;
		foreach ($this->combinations as $k => $comb) {
			if (!$this->isCombinationUsed($k))
				continue;
			foreach ($comb['data'] as $c) {
				foreach($this->bets as &$bet) {
					if (in_array($bet['id'], $c['bets'])) {
						$tmp = $c['win'] * ($bet['rate'] / $c['rateSum']);
						if ( !empty($tmp) && !is_nan($tmp) )
							$bet['win'] += $tmp;
						$tmp = $c['won'] * ($bet['rate'] / $c['rateSum']);
						if ( !empty($tmp) && !is_nan($tmp) )
							$bet['won'] += $tmp;
					}
				}
			}
		}
		break;
	default:
		throw new Exception('Unknown ticket type: ' . $this->type);
	}
}

/**
 * Reads column names for all bets. (added keys 'columnName' and 'resultNames' to arrays in $this->bets)
 */
public function readBetColumnNames(&$db = null) {
	$columnIds = array();
	foreach ($this->bets as $bet) {
		if (!in_array($bet['column'], $columnIds))
			$columnIds[] = $bet['column'];
		$columnIds = array_merge($columnIds, $bet['resultValues']);
	}
	if (!empty($columnIds))
		$columnNames = It6_Models_Bet::getResultName($columnIds, $db);
	else
		$columnNames = array();
	foreach ($this->bets as &$bet) {
		$bet['columnName'] =	array_key_exists($bet['column'], $columnNames)
			? $columnNames[$bet['column']] : $bet['column'];
		$bet['resultNames'] = array();
		foreach ($bet['resultValues'] as $result) {
			$bet['resultNames'][$result] = array_key_exists($result, $columnNames)
				? $columnNames[$result] : $result;
		}
	}
}

public function readBetSportEvents(&$db = null) {
	$betIds = $this->getBetIds();
	static::assureDbParam($db);
	$rows = $db->select()
		->from(array('sz' => 'sazky'), array('betId' => 'sazka_id'))
		->join(array('u' => 'udalost'), 'sz.udalost_id=u.udalost_id', array('eventId' => 'udalost_id', 'eventName' => 'nazev'))
		->join(array('o' => 'oblast'), 'o.oblast_id=u.oblast_id', array('oblastId' => 'oblast_id', 'regionName' => 'nazev'))
		->join(array('sp' => 'sport'), 'u.sport_id=sp.sport_id', array('sportId' => 'sport_id', 'sportName' => 'nazev'))
		->where('sz.sazka_id IN (?)', $betIds)
		->query()
		->fetchAll();
	$data = array();
	foreach ($rows as $row) {
		$betId = $row['betId'];
		unset($row[$betId]);
		$data[$betId] = $row;
	}
	foreach ($this->bets as &$bet)
		$bet = array_merge($bet, $data[$bet['id']]);
}

/**
 * Reads sport, league names
 */
public function readBetDataForConfirmation(&$db = null) {
	static::assureDbParam($db);
	$result = array();
	$betIds = array();
	$betWheres = array();
	foreach ($this->bets as $bet) {
		$betIds[] = $bet['id'];
		$betWheres[] = '(b.sazka_id=' . intval($bet['id'])
			. ' AND c.sloupec_id=' . intval( $bet['column']) . ')';
	}
	$select = $db->select()
		->from(array('b' => 'sazky'), array('betId' => 'sazka_id', 'betName' => 'text'))
		->join(array('t' => 'typ'), 'b.typ_id=t.typ_id', array('typeName' => 'nazev'))
		->join(
			array('c' => 'podtyp_sloupce'),
			'b.podtyp_id=c.podtyp_id',
			array('columnId' => 'sloupec_id', 'columnName' => 'nazev')
		)
		->join(
			array('r' => 'sazka_kurz_aktualni'),
			'r.sazka_id=b.sazka_id AND r.sloupec_id=c.sloupec_id',
			array('rate' => 'kurz')
		)
		->join(array('e' => 'udalost'), 'b.udalost_id=e.udalost_id', array('eventName' => 'nazev'))
		->join(array('s' => 'sport'), 's.sport_id=e.sport_id', array('sportName' => 'nazev'))
		->join(array('rg' => 'oblast'), 'rg.oblast_id=e.oblast_id', array('regionName' => 'nazev'))
		->where(implode(' OR ', $betWheres));
	$rows = $select->query()
		->fetchAll();
	$columnRiskAmounts = It6_Models_Bet::readBetColumnRiskAmounts($betIds, $db);
	foreach ($rows as $row) {
		$betId = $row['betId'];
		$columnId = $row['columnId'];
		$bet = $this->getBet($betId, $columnId);
		$result[$betId][$columnId] = array(
			'id' => $betId,
			'column' => $columnId,
			'isLive' => 0, // legacy
			'rate' => $row['rate'],
			'amount' => (empty($bet['amount']) ? 0.0 : $bet['amount']),
			'group' => $this->getGroupName($bet['group']),
			'columnRiskAmounts' => $columnRiskAmounts[$betId],
			'texts' => array(
				'betText' => $row['betName'],
				'columnName' => $row['columnName'],
				'typeName' => $row['typeName'],
				'sportName' => $row['sportName'],
				'eventName' => $row['eventName'],
				'regionName' => $row['regionName'],
			),
		);
	}
	return $result;
}

/**
 * if ticket does not contain data needed for complete validation,
 * this method will read them from DB.
 */
public function readDataForValidation(&$db = null) {
	if (!empty($this->bets)) {
		$data = It6_Models_Bet::readBetsValidationData($this->getBetIds(), $db);
		foreach ($this->bets as &$bet) {
			$item = &$data[$bet['id']];
			$bet['ako'] = $item['ako'];
			$bet['eventId'] = $item['eventId'];
			$bet['subtypeId'] = $item['subtypeId'];
		}
	}
}

/**
 * @returns array
 */
public function getCombinationsDataForConfirmation() {
	$data = array(
		'groupT' => $this->getGroupTSize(),
		'groupCount' => $this->getGroupCount(false), // n
		'combinations' => array(), // array( k => array( 'winMin' => win_min, 'winMax' => win_max), ... )
	);
	if (!empty($this->combinations)) {
		foreach ($this->combinations as $k => $comb) {
			if ($this->isCombinationUsed($k)) {
				$data['combinations'][$k] = array(
					'winMin' => $comb['winMin'],
					'winMax' => $comb['winMax'],
				);
			}
		}
	}
	return $data;
}

/**
 * @return boolean TRUE if user associated with coupon is known and not anonymous
 */
public function isUserLoggedIn() {
	return (!empty($this->userId) && It6_Models_User::ID_INTERNET_ANONYMOUS != $this->userId);
}

/**
 * @param struct $dataDescr If not set, static variable with exposed data definition is used (and is initialized before if null).
 *							 @see It6_StructFilter::filter()
 * @return struct Exposed data (useable eg. for JSON)
 */
public function getExposedData(array $dataDescr = null) {
	if (!isset($dataDescr)) {
		if (!isset(self::$exposedVars))
			self::$exposedVars = array(
				'pointType', 'type','stake', 'rate', new It6_NotNull('rateWithoutAdvance'),
				 new It6_NotNull('rateAdvance'), 'rateSum', 'win', 'won', 'result', 'mail', 'sms', 'givenStake',
				 new It6_NotNull('anonymous'),
				'bets' => array(array(
					'id', 'column', 'amount', 'rate', 'group', 'result', 'resultValues',
					new It6_NotNull('ako'), new It6_NotNull('canceled'), new It6_NotNull('couponTime'),
					'order',
				 )),
				'combinations' => array(array('used', 'stake')),
			);
		$dataDescr = self::$exposedVars;
	}
	$exposed = array();
	It6_StructFilter::filter($dataDescr, $this, $exposed);
	$exposed['userLoggedIn'] = $this->isUserLoggedIn();
	return $exposed;
}

public static function getTypeName($type, $translate = true, $short = false) {
	switch ($type) {
	case self::TYPE_SIMPLE:
		$key = 'ticket_simple';
		break;
	case self::TYPE_COMBI:
		$key = 'ticket_combi';
		break;
	case self::TYPE_SYSTEM:
		$key = 'ticket_system';
		break;
	case self::TYPE_MAXI:
		$key = 'ticket_maxicombinator';
		break;
	default:
		return $type;
	}
	if ($short)
		$key .= '_short';
	return self::trans($key);
}


public static function getRealTicketState($ticket) {
	return ('ticket_state_1W' == $ticket['state'] || 'ticket_state_1L' == $ticket['state']
		? 'ticket_state_1'
		: $ticket['state']
	);
}

/**
* Data for maxicombi tickets
*/
public function getDataForCampaign() {

	$ret = array();
	foreach ( $this->combinations as $comb ) {
		if ( empty($comb['data']) ) continue;
		
		foreach ( $comb['data'] as $data ) {
			$cmb['rate'] = $data['rate'];
			$cmb['stake'] = $comb['stake'];
			$cmb['betCount'] = count($data['bets']);
			$ret[] = $cmb;
		}
	}

	return $ret;
}

public function getCssClass($ticketState) {
	/* ticketState   | old class
	--------------------------------------
	ticket_state_1   | ticket-open-tag
	ticket_state_1w  | result-open-win
	ticket_state_1l  | result-open-loss
	ticket_state_2'  | ticket-win-tag
	ticket_state_3   | ticket-loss-tag
	ticket_state_4   | ticket-canceled-tag
	ticket_state_5   | ticket-win-tag
	ticket_state_6   | ticket-canceled-tag */
	
	/* css třídy v novém designu
	 * -----------------------------
	 * result-na - otazník
	 * result-question - zákaz
	 * result-ko - červený křížek
	 * result-ok - zelená fajfka
	 */
	
	$stateClass = array(
		'ticket_state_1' => 'result-na',
		'ticket_state_1w' => 'result-ok',
		'ticket_state_1l' => 'result-ko',
		'ticket_state_2' => 'result-ok',
		'ticket_state_3' => 'result-ko',
		'ticket_state_4' => 'result-question',
		'ticket_state_5' => 'result-ok',
		'ticket_state_6' => 'result-question'
	);
	
	return $stateClass[$ticketState];
}

} // class It6_Models_Ticket
