<?php

/**
 * Ticket related static methods. Ticket is set of the user bets and related data.
 * @author Pavel Klinger
 * @see Entities_Ticket
 *
 */
class Webservice_Ticket extends Webservice_AbstractWebService {

	public static $TABLE = "ticket";
	public static $STATISTICS_CASHFLOW_TABLE = 'ticket_statistics_cashflow';
	public static $STATISTICS_PAYOUT_TABLE = 'ticket_statistics_payout';
	public static $TICKETHASH_TABLE = 'tickethash';
	public static $TICKETHASH_TICKET_TABLE = 'tickethash_ticket';
	public static $TABLE_VIEW = "ticket_pohled";
	public static $TABLE_PREFIX = "t";
	public static $TIPS_TABLE = "ticket_kurz";
	public static $COMBINATIONS_TABLE = "ticket_combination";
	public static $IDENTITY = "ticket_id";

	const PARAMETER_TICKET_CANCELATION_LIMIT = 'ticket.cancelation.limit';
	const PARAMETER_TICKET_FORFEIT_LIMIT = 'ticket.forfeit.limit';

	public static $CREATE_USER_POINTS_TRANSACTION_NUMBER = 1;

	/**
	 * Deprecated
	 */
	public static $MONTH_TICKET_COUNT = 3;

	public static $TICKET_STATE_OPEN		= 1;
	public static $TICKET_STATE_WIN			= 2;
	public static $TICKET_STATE_LOSS		= 3;
	public static $TICKET_STATE_CANCELED	= 4;

	protected static $CONV = array (
		't.ticket_id'               => 'ticketId',
		't.handle'                  => 'ticketHandle',
		't.user_id'                 => 'userId',
		'castka'                    => 'amount',
		'ceil(castka * (1 + mp))'	=> 'amountMp',
		'castka_cash'               => 'cashAmount',
		'castka_body'               => 'pointsAmount',
		'zalozen'                   => 'createdTime',
		'vyplacen'                  => 'paidOut',
		'zruseno'                   => 'canceled',
		'is_loss'                   => 'isLoss',
		'zrusil_bookmaker_id'       => 'canceledByBookmakerId',
		'duvod_zruseni'             => 'reasonOfCancelation',
		'free_bet_bonus'            => 'freeBetBonus',
		'type'                      => 'type',
		'win'                       => 'winAmount',
		'rate'                      => 'totalOdds',
		'win_real'                  => 'realWinAmount',
		'rate_real'                 => 'realTotalOdds',
		'stats'                     => 'stats',
		'stats_user'                => 'userStats',
		'vyplacen_date'             => 'paidOutTime',
		'vyplacen_bookmaker_id'     => 'paidOutBookmakerId',
		'mail'                      => 'mail',
		'sms'						=> 'sms',
		'tickethash'                => 'ticketHash',
		'collection_time'           => 'collectionTime',
		'collection_account_user_id'=> 'collectionAccountUserId',
		'forfeit'                   => 'forfeit',
		'mp'                        => 'mp',
		'mp_amount'                 => 'mpAmount',
		'mp_win'                    => 'mpWin',
		'mp_win_amount'             => 'mpWinAmount',
		'collection_host_id'        => 'collectionHostId',
		'forced_collection_host_id' => 'forcedCollectionHostId',
		'cash'                      => 'cash',
		'u.anonymous'               => 'anonymous',
		'point_type_id'             => 'pointTypeId',
		'rate_advance'              => 'rateAdvance',
		'admin_id'                  => 'adminId',
		'coupon_id'                 => 'couponId',
		'hs.branch_id'              => 'branchId',
		't.host_id'                 => 'hostId',
		'hs.name'                   => 'hostName',

		'hs.branch_id'              => 'branchId',
		'zb.username'               => 'canceledByBookmakerNick',
		'hsc.branch_id'             => 'collectionBranchId',
		'cancel_allowed'            => 'cancelAllowed',
		'cached_data'               => 'cachedData',
		'coupon_admin_id'           => 'couponAdminId',
		'confirm_date'              => 'confirmDate',
		'confirm_bookmaker_id'      => 'confirmBookmakerId',

		'point_create'             => 'pointCreate',
		'point_branch_visit'       => 'pointBranchVisit',

		'IF(pt.provision_paid_out = 0, \'ne\', \'ano\')' => 'affiliateProvisionPaidOut',
		'ROUND(((castka - win_real)/10),2)' => 'affiliateProvision',
		'CONCAT(u.jmeno, \' \', u.prijmeni)' => 'nameOfUser',
		'pt.partner_id' => 'affiliatePartnerId',
		'ap.name' => 'affiliatePartnerName',
	);

	protected static $STATS_GROUP_CONV = array (
		't.user_id'     => 'userId',
		't.sport_id'    => 'sportId',
		't.udalost_id'  => 'eventId',
		't.typ_id'      => 'typeId',
		't.location_id' => 'locationId',
		't.host_id'     => 'hostId',
		't.branch_id'   => 'branchId',
		't.type'        => 'type',
	);

	protected static $STATS_GROUP_NAME = array (
		'userId'      => 'u.nick',
		'sportId'     => 'TRANSLATE(s.nazev,1)', //TODO language dynamicly
		'eventId'     => 'TRANSLATE(e.nazev,1)',
		'typeId'      => 'tp.nazev',
		'locationId'  => 'bl.name',
		'hostId'      => 'hs.name',
		'branchId'    => 'br.name',
		'type'        => 'type'
	);

	// Branch application compatible ticket status
	const STATUS_WAITING = 0;
	const STATUS_CANCELED = 1;
	const STATUS_ACCEPTED = 2;
	const STATUS_ACCEPTED_MODIFIED = 3;
	const STATUS_TIMEOUT = 4;
	const STATUS_FORFEIT = 5;

	public static $STATUS_MAP = array(
		It6_Models_Ticket::COUPON_STATUS_IN_ACCEPTATION => self::STATUS_WAITING,
		It6_Models_Ticket::COUPON_STATUS_MARKED_AS_IN_ACCEPTATION => self::STATUS_WAITING,
		It6_Models_Ticket::COUPON_STATUS_MARKED_AS_ACCEPTED => self::STATUS_WAITING,
		It6_Models_Ticket::COUPON_STATUS_MARKED_AS_REJECTED => self::STATUS_WAITING,
		It6_Models_Ticket::COUPON_STATUS_PROLONGED => self::STATUS_WAITING,
		It6_Models_Ticket::COUPON_STATUS_ACCEPTED => self::STATUS_ACCEPTED,
		It6_Models_Ticket::COUPON_STATUS_DELAYED => self::STATUS_ACCEPTED,
		It6_Models_Ticket::COUPON_STATUS_REJECTED => self::STATUS_CANCELED,
		It6_Models_Ticket::COUPON_STATUS_MODIFIED => self::STATUS_ACCEPTED_MODIFIED,
		It6_Models_Ticket::COUPON_STATUS_MARKED_AS_MODIFIED_ACCEPTED => self::STATUS_ACCEPTED,
		It6_Models_Ticket::COUPON_STATUS_MARKED_AS_MODIFIED_REJECTED => self::STATUS_CANCELED,
		It6_Models_Ticket::COUPON_STATUS_MARKED_AS_DELAYED_ACCEPTED => self::STATUS_ACCEPTED,
		It6_Models_Ticket::COUPON_STATUS_MARKED_AS_DELAYED_REJECTED => self::STATUS_CANCELED,
	);

	protected static $_paidOutResultsOnly = false;
	protected static $_columnsWithCachedData = false;

	/**
	 * This function changes toEntity() behavior.
	 * Call this function before code that use toEntity()
	 * to consider result of bet as known only after bet was paid out.
	 * Don't forget to call this function without parameter to restore default behavior.
	 * @param boolean|NULL $only TRUE to use result only of paid out bets, NULL for default behavior
	 */
	protected static function setPaidOutResultsOnly($only = null) {
		static::$_paidOutResultsOnly = (isset($only) ? $only : false);
	}

	/**
	 * This function changes toEntity() behavior.
	 * Call this function before code that use toEntity()
	 * to include 'cachedData' field in returned entity.
	 * Don't forget to call this function without parameter to restore default behavior.
	 * @param boolean|NULL $with TRUE to include 'cachedData', NULL for default behavior
	 */
	protected static function setColumnsWithCachedData($with = null) {
		static::$_columnsWithCachedData = (isset($with) ? $with : false);
	}

	protected static function defaultJoins($query) {
		return parent::defaultJoins($query)
			->joinLeft(
				array('zb' => 'vic_admin.admin'),
				'zrusil_bookmaker_id = zb.admin_id',
				null)
			->joinLeft(
				array('hs' => 'vic_admin.host'),
				't.host_id = hs.id',
				null)
			->joinLeft(
				array('hsc' => 'vic_admin.host'),
				't.collection_host_id = hsc.id',
				null)
			->joinLeft(
				array('u' => 'vic_main.uzivatel'),
				'u.user_id = t.user_id',
				null)
			->joinLeft(
				array('pt' => 'vic_main.affiliate_partner_user_ticket'),
				'pt.ticket_id = t.ticket_id',
				null)
			->joinLeft(
				array('ap' => 'vic_main.affiliate_partner'),
				'ap.id = pt.partner_id',
				null);
	}

	/**
	 * Returns all tickets in the system.
	 * @return array array of the ticket structures
	 * @see Entities_Ticket
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Returns all tickets in the system with the given ticket state.
	 * @param integer $state describes the ticket state (0:open, 1:win, 2:loss, 3:canceled)
	 * @return array array of the ticket structures
	 * @see Entities_Ticket
	 */
	public static function getAllByState(array $states=array(), array $where = array(), $extensions = null) {
		if(in_array(self::$TICKET_STATE_OPEN, $states))
			$wheres[] = array(
				'paidOut'	=> '0',
				'canceled'	=> '0'
			);
		if(in_array(self::$TICKET_STATE_WIN, $states)) {
			$wheres[] = array(
				'paidOut'	=> '1',
				'canceled'	=> '0',
				'isLoss'	=> '0'
			);
		}
		if(in_array(self::$TICKET_STATE_LOSS, $states)) {
			$wheres[] = array(
				'paidOut'	=> '1',
				'canceled'	=> '0',
				'isLoss'	=> '1'
			);
		}
		if(in_array(self::$TICKET_STATE_CANCELED, $states)) {
			$wheres[] = array(
				'canceled'	=> '1'
			);
		}


		$whereSql = '';
		$j = 1;
		foreach($wheres as $whereState) {
			$i = 1;
			$whereSql .= '(';
			foreach($whereState as $whereCol => $whereVal) {
				$whereSql .= Zend_Registry::get('db')->quoteInto('('.$whereCol.' = ?)', $whereVal);
				if($i != count($whereState))
					$whereSql .= ' AND ';
				$i++;
			}$whereSql .= ')';
			if($j != count($wheres))
				$whereSql .= ' OR ';
			$j++;
		}

		$where[] = $whereSql;

		static::setPaidOutResultsOnly(true);
		static::setColumnsWithCachedData(true);
		$tickets = parent::getAllWhere($where, $extensions);
		static::setPaidOutResultsOnly();
		static::setColumnsWithCachedData();
		$extensionData = $tickets['__extensions'];
		unset($tickets['__extensions']);
		self::getComplete($tickets, false);
		$tickets['__extensions'] = $extensionData;

		return $tickets;
	}

	/**
	 * Find ticket by given identifier.
	 * @param integer $ticketId identifier of the ticket
	 * @return struct ticket structure
	 * @see Entities_Ticket
	 */
	public static function getById($ticketId, $extensions = null) {
		return parent::getById($ticketId, $extensions);
	}

	/**
	 * Make copy of the given ticked.
	 * @param integer $ticketId identifier of the ticket
	 * @return struct ticket structure
	 * @see Entities_Ticket
	 */
	public static function cloneTicket($ticketId) {
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Find ticket by given handle
	 * @param string $ticketHandle
	 * @return struct ticket structure
	 * @see Entities_Ticket
	 * @see Entities_Ticket#handle
	 */
	public static function getByHandle($ticketHandle, $extensions = null) {
		if (!It6_Validate_NineDigitHandle::isValidString($ticketHandle))
			return null;
		It6_NineDigitHandle::fixHandle($ticketHandle);
		return static::getOneBy($ticketHandle, self::$TABLE_PREFIX.'.handle', $extensions);
	}

	/**
	 * Find ticket by given coupon ID.
	 * @param integer $couponId Coupon ID
	 * @param array $columns [optional] Only specified fields
	 * @return array Array of ticket structures
	 * @see Entities_Ticket
	 */
	public static function getByCouponId($couponId, $columns = null) {
// As toEntity() is not compatible with columns...
//		$entities = parent::getAllWhereColumns(array('couponId = ?' => $couponId), $columns);
//		$n = count($entities);
//		if (0 == $n)
//			return false;
//		else if (1 == $n)
//			return $entities[0];
//		else
//			throw new It6_XmlRpc_Exception('Tickets with duplicate coupon ID found. couponId=' . $couponId);
		$db = static::getDb();
		$rows = static::defaultQuery($db->select(), $columns)
			->where(static::convQuery('couponId = ?'), $couponId)
			->query()
			->fetchAll();
		$n = count($rows);
		if (0 == $n)
			return false;
		else
			return new It6_ArrayWrapper($rows);
	}

	/**
	 * Find ticket ID(s) by given coupon ID.
	 * @param integer $couponId Coupon ID
	 * @return array Array of ticket IDs
	 */
	public static function getIdsByCouponId($couponId) {
		$db = static::getDb();
		$rows = $db->select()->from(static::$TABLE, array('id' => 'ticket_id'))
			->where(static::convQuery('couponId = ?'), $couponId)
			->query()
			->fetchAll();
		$ids = array();
		foreach ($rows as $row)
			$ids[] = $row['id'];
		return new It6_ArrayWrapper($ids);
	}

	/**
	 * Creates cache key fot coupon validity flag.
	 * @param integer $couponId
	 * @return string Cache key
	 */
	private static function getCacheKeyForCouponValidity($couponId) {
		return It6_GlobalCache::KEY_PREFIX_CONFIRMD . "C:VALID:$couponId";
	}

	/**
	 * @return integer|boolean TTL in seconds (can be zero) or FALSE if cache is disabled
	 */
	private static function getCouponValidityCacheTtl() {
		static $ttl = null;
		if (!isset($ttl)) {
			if (defined('CACHE_COUPON_VALIDITY_TIMEOUT')) {
				$ttl = intval(CACHE_COUPON_VALIDITY_TIMEOUT);
			}
			else
				$ttl = false;
		}
		return $ttl;
	}

	/**
	 * Store flag that coupon was already validated,
	 * flag lasts cache TTL (hardwired 10 seconds).
	 * @param integer $couponId
	 */
	public static function setCacheForCouponValidity($couponId) {
		$ttl = static::getCouponValidityCacheTtl();
		if (false !== $ttl) {
			return It6_GlobalCache::setKey(
					static::getCacheKeyForCouponValidity($couponId), time(), $ttl
				);
		}
		else {
			return true;
		}
	}

	/**
	 * Check cached flag for coupon validity.
	 * @param integer $couponId
	 * @return boolean TRUE if cached validity flag is set
	 */
	public static function isCouponValidityCached($couponId) {
		$valid = It6_GlobalCache::getKey(static::getCacheKeyForCouponValidity($couponId), $fetched);
		return ($fetched && $valid);
	}

	/**
	 * Insert new coupon. Value of the coupon identifier is ignored and new
	 * is generated.
	 * @param struct $ticket structure of the coupon
	 * @return integer|array coupon identifier otherwise array of the errors
	 * @see Entities_Ticket
	 */
	public static function insert($ticket) {
		//$ticket = self::calculate($ticket, $helper);
		$db = static::getDb();
		$acl = Zend_Registry::get('acl');

		try {
			$branchId = $acl->getIdentity(It6_Acl::IDNAME_BRANCH);
			if ( empty($ticket['userId']) ) {
				$user = Webservice_User::getOneWhere(array(
					'branchId = ?' => $branchId,
					'anonymous = 1'));
				$ticket['userId'] = $user['userId'];
				$ticket['cash'] = 1;
			}
			else {
				$user = Webservice_User::getById($ticket['userId']);
				if ( !empty($user['anonymous']) && $user['branchId'] != $branchId ) {
					throw new Exception('Given user is not anonymous user of this branch.');
				}
				if ( empty($user['anonymous']) && empty($user['activationTime']) ) {
					throw new Exception("User is not activated.");
				}
			}
		}
		catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("Cannot insert coupon.", 0, $e);
		}

		if ( empty($ticket['createdTime']) )
			$ticket['createdTime'] = It6_Date::dbNow();
		
		if ( !empty($ticket['pointTypeId']) && !empty($ticket['pointsAmount']) ) {
			unset($ticket['amount']);
		}

		$helper = new It6_Models_Ticket($ticket, It6_Models_Ticket::DATA_SERVICE, 'user', $db);
		$helper->computeAggregates();
		$validationResult = self::validate($ticket['userId'], $helper);
		if (true !== $validationResult) {
			It6_Log::info(
				'Coupon not inserted - validation failed.',
				It6_Log::TAG_USER_OPERATION,
				array('validationResult' => $validationResult, 'couponData' => $ticket)
			);
			return $validationResult;
		}

		$hostId = $acl->getIdentity(It6_Acl::IDNAME_HOST);
		if (!empty($helper->cash) && !Webservice_Host::isInAllowed($hostId))
			throw new It6_XmlRpc_Exception("In transactions are disabled for this host.");

		It6_DbTransaction::begin($db);
		try {
			$adminId = $acl->getIdentity(It6_Acl::IDNAME_ADMIN);
			$coupon = Webservice_Coupon::fromWebserviceTicket($ticket, $adminId, $hostId);
			$db->delete(Webservice_Coupon::$TABLE, array(
				'user_id=?' => $ticket['userId'],
				'admin_id=?' => $adminId
			));
			$coupon->couponId = It6_Models_CouponSequence::nextId($db);
			$coupon->status = It6_Models_Ticket::COUPON_STATUS_MARKED_AS_IN_ACCEPTATION;
			$db->insert(Webservice_Coupon::$TABLE, Webservice_Coupon::fromEntity($coupon));
			static::setCacheForCouponValidity($coupon->couponId);
			It6_DbTransaction::commit($db);
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Cannot insert coupon.", 0, $e);
		}

		It6_Log::info(
			'Coupon created',
			It6_Log::TAG_USER_OPERATION,
			array(
				'userId' => $ticket['userId'],
				'adminId' => $adminId,
				'couponId' => $coupon->couponId,
				'coupon' => serialize($coupon)
			)
		);

		//$acceptation = static::checkCouponAcceptation($coupon, $helper);
		return $coupon->couponId;
	}

/* If this wasn't helper method, but exposed XML_RPC method, then uncomment
	public static function calculate($ticket, &$helper = null) {
		try {
			$ticket['createdTime'] = It6_Date::dbNow();
			if (!isset($helper)) {
				$helper = new It6_Models_Ticket($ticket, It6_Models_Ticket::DATA_SERVICE); // handle currency param
				$helper->computeAggregates();
			}
			$ticket['totalOdds'] = $helper->rate;
			$ticket['winAmount'] = $helper->win;
			return $ticket;
		} catch (Exception $e) {
			throw new It6_XmlRpc_Exception("Can not calculate ticket.", 0, $e);
		}
	}
*/

	/**
	 * Takes coupon, creates appropriate ticket and clears coupon.
	 * Coupon must be already validated!
	 * @param struct $coupon Coupon entity
	 * @param It6_Models_Ticket $helper Helper instance coupled with passed coupon
	 * @param integer|NULL $creationTimestamp Explicit timestamp for ticket creation
	 * @return integer|array One or array of ticket IDs
	 */
	private static function createTicketFromCoupon($coupon, $helper, $creationTimestamp) {
		$db = static::getMainDb();
		$dbAdmin = static::getAdminDb();
		It6_DbTransaction::begin($db);
		It6_DbTransaction::begin($dbAdmin);
		try {
			if (It6_Models_Ticket::TYPE_SIMPLE == $helper->type && count($helper->bets) > 1) {
				$ids = array();
				foreach ($helper->bets as $bet) {
					$simpleHelper = clone $helper;
					$simpleHelper->bets = array($bet);
					$simpleHelper->computeAggregates();
					$archive = (count($ids) + 1 == count($helper->bets)); // archive coupon when creating last ticket
					$ids[] = static::createTicketFromCouponImpl($coupon, $simpleHelper, $archive, $creationTimestamp);
				}
				$result = new It6_ArrayWrapper($ids);
			}
			else
				$result = static::createTicketFromCouponImpl($coupon, $helper, true, $creationTimestamp);
			It6_DbTransaction::commit($dbAdmin);
			$dbAdminCommited = true;
			It6_DbTransaction::commit($db);
			return $result;
		}
		catch (Exception $e) {
			if (empty($dbAdminCommited))
				It6_DbTransaction::rollback($dbAdmin);
			else
				It6_Log::emerg('Partial rollback');
			It6_DbTransaction::rollback($db);
			throw $e;
		}
	}

	/**
	 * Takes coupon, creates appropriate ticket.
	 * Coupon must be already validated!
	 * This functions requires simple type of coupon to have only one bet,
	 * this function is not intented to be called directly, use createTicketFromCoupon() instead.
	 * @param struct $coupon Coupon entity
	 * @param It6_Models_Ticket $helper Helper instance coupled with passed coupon
	 * @param bool $archive If coupon should be moved to archive
	 * @param integer|NULL Explicit timestamp for ticket creation, empty value for using current timestamp
	 * @return integer|array One or array of ticket IDs
	 */
	private static function createTicketFromCouponImpl($coupon, $helper, $archive = true, $creationTimestamp = null) {
		$db = static::getDb();
		//It6_DbTransaction::begin($db);
		try {

			if (empty($coupon))
				throw new It6_XmlRpc_Exception('Invalid param: coupon', 0);

			$couponId = $coupon->couponId;
			$acl = Zend_Registry::get('acl');
			$hostId = $coupon->hostId;
			if (empty($hostId))
				throw new It6_XmlRpc_Exception('No host ID specified');
			$host = Webservice_Host::getById($hostId);
			$branchId = $host['branchId'];
			$branch = Webservice_Branch::getById($branchId);
			$adminId = $acl->getIdentity(It6_Acl::IDNAME_ADMIN);
			if (empty($adminId))
				$adminId = It6_Models_Admin::ID_INTERNET; // if there is no admin authenticated, we assume internet coupon
			$userId = $coupon->userId;
			//TODO: specify use of user ID for anonymous user
			$anonymousUser = false;
			if (empty($userId)) {
				$userId = Webservice_User::getAnonymousByBranchId($branchId);
				if (empty($userId))
					throw new It6_XmlRpc_Exception('No user ID specified');
				$anonymousUser = true;
			}

			$cash = ($helper->cash ? 1 : 0);

			$ticketId = It6_Models_TicketSequence::nextId($db);
			$mp = $branch->mp;
			$mpWin = $branch->mpWin;

			$helper->computeMpWin($mpWin, $db);

			$dbCreationTime = It6_Date::timestampToDb(empty($creationTimestamp) ? time() : $creationTimestamp);
			$data = array(
				'ticket_id'     => $ticketId,
				'user_id'       => $userId,
				'castka'        => $helper->stake,
				'castka_body'   => $helper->isPointTicket() ? $helper->stakeInPoints : null,
				'type'          => $helper->type,
				'zalozen'       => $dbCreationTime,
				'win'           => $helper->win,
				'rate'          => $helper->rate,
				'tickethash'    => $helper->totalHash,
				'host_id'       => $hostId,
				'cash'          => $cash,
				'admin_id'      => $adminId,
				'coupon_id'     => $couponId,
				'group_count'   => $helper->getGroupCount(false),
				'group_t'       => ($helper->hasGroupT() ? 1 : 0),
				'mail'          => (It6_Models_Ticket::MAIL_YES == $helper->mail ? 1 : 0),
				'sms'          => ((It6_Models_Ticket::SMS_YES == $helper->sms && $hostId == 1) ? 1 : 0),
				'mp'            => $mp,
				'mp_win'        => $mpWin,
				'mp_win_amount' => $helper->mpWinAmountMax,
				'point_type_id' => empty($helper->pointType) ? null : $helper->pointType,
				'rate_advance'  => empty($helper->rateAdvance) ? null : $helper->rateAdvance,
				'coupon_admin_id' => $coupon['adminId'],
				'confirm_date' => (empty($coupon['confirmDate']) ? null : $coupon['confirmDate']),
				'confirm_bookmaker_id' => $coupon['bookmakerId'],
			);

			$db->insert(Webservice_Ticket::$TABLE, $data);

			$helper->id = $ticketId;
			$ticketHandle = It6_NineDigitHandle::makeHandle($ticketId, $db);
			$db->update(
				Webservice_Ticket::$TABLE,
				array('handle' => $ticketHandle),
				array('ticket_id=?' => $ticketId)
			);
			if ($helper->isMaxicombinatorCompatible())
				$helper->saveCombinations(null, $db);
			$helper->saveTicketHashes(false, $userId, $db);
			$helper->computeBetWonAndWinDistribution();

			$helperCc = clone $helper;
			if ($helperCc->currencyId != It6_Models_Currency::getCentralCurrencyId($db)) {
				$helperCc->convertStakesToCentralCurrency($userId, $db);
				$helperCc->computeAggregates();
			}
			$helperCc->saveBetsRiskLimit(false, $db);
			$helperCc->saveSportRiskLimits($userId, false, $db);

			foreach ($helper->bets as $bet) {
				$data = array(
					'sazka_id'   => $bet['id'],
					'sloupec_id' => $bet['column'],
					'rate'       => $bet['rate'],
					'won'        => $bet['won'],
					'win'        => $bet['win'],
					'amount'     => $bet['riskAmount'],
					'ticket_id'  => $ticketId,
					'group_id'   => isset($bet['group']) ? $bet['group'] : null,
					'order'      => empty($bet['order']) ? 0 : $bet['order'],
				);
				$db->insert(Webservice_Ticket::$TIPS_TABLE, $data);
			}
			foreach ($helperCc->bets as $bet) {
				$betId = $bet['id'];
				$changes = array(
					'riskLimitBalance'	=> $bet['riskAmount'],
					'absoluteStake'		=> $bet['absoluteStake'],
					'betCount'			=> $bet['inTicketCount']
				);
				Webservice_Bet::changeBetColumnData($betId, $bet['column'], $changes);

				if (!$anonymousUser) {
					$history = $helperCc->getDataForBetUserHistory($betId);
					if (!empty($history))
						It6_Models_Bet::changeUserHistory($userId, $betId, $history, false, $db);
				}
			}
			if ($archive)
				Webservice_Coupon::archiveCoupon($couponId);

			$adminId = $coupon['adminId'];
			$currencyId = It6_Models_User::get($userId, 'currencyId', $db);
			$isCashBalanceTicket = $cash && !$helper->isPointTicket();
			if ( $isCashBalanceTicket ) {
				Webservice_Transaction::make(array(
					'value' => $helper->stake,
					'createAdminId' => $adminId,
					'hostId' => $hostId,
					'userId' => $userId,
					'ticketId' => $ticketId,
					'typeName' => Webservice_TransactionType::NAME_BRANCH_TICKET_CREATE_CASH,
					'currencyId' => $currencyId,
				));
				if ( floatval($mp) > 0 ) {
					Webservice_Transaction::make(array(
						'value' => $helper->roundMp($mp * $helper->stake, null, $db),
						'createAdminId' => $adminId,
						'hostId' => $hostId,
						'userId' => $userId,
						'ticketId' => $ticketId,
						'typeName' => Webservice_TransactionType::NAME_BRANCH_TICKET_CREATE_CASH_MP,
						'currencyId' => $currencyId,
					));
				}
			}
			
			if ( $helper->isPointTicket() ) {
				$rates = array();
				foreach ( $helper->bets as $bet )
					$rates[] = $bet['rate'];

				if ( !Webservice_Campaign::putOn(
						'CreatePointTicket',
						Webservice_Campaign::NAMESPACE_SPEND,
						array(
							'type'          => $helper->type,
							'pointType'     => $helper->pointType,
							'betCount'      => $helper->betCount,
							'stake'         => $helper->stake,
							'stakeInPoints' => $helper->stakeInPoints,
							'rates'         => $rates,
							'userId'        => $userId,
							'hostId'        => $hostId,
							'ticketId'      => $ticketId,
							'currencyId'    => $currencyId
						) 
					) 
				) {

					throw new Exception('Point ticket creation impossible.');
				}
			}

			if (!$isCashBalanceTicket) {
				Webservice_Transaction::make(array(
					'value' => -$helper->stake,
					'createAdminId' => $adminId,
					'userId' => $userId,
					'hostId' => $hostId,
					'ticketId' => $ticketId,
					'typeName' => Webservice_TransactionType::NAME_USER_TICKET_CREATE,
					'currencyId' => $currencyId
				));
	
				if ( floatval($mp) > 0 ) {
					Webservice_Transaction::make(array(
						'value' => -$helper->roundMp($mp * $helper->stake, null, $db),
						'createAdminId' => $adminId,
						'hostId' => $hostId,
						'userId' => $userId,
						'ticketId' => $ticketId,
						'typeName' => Webservice_TransactionType::NAME_USER_TICKET_CREATE_MP,
						'currencyId' => $currencyId,
					));
				}
			}


			if ( !empty($helper->rateAdvance) && $helper->rateAdvance > 1 ) {
				if ( !Webservice_Campaign::putOn(
						'PreferenceRate',
						Webservice_Campaign::NAMESPACE_SPEND,
						array(
							'type'      => $helper->type,
							'betCount'  => $helper->betCount,
							'stake'     => $helper->stake,
							'rate'      => $helper->rate,
							'preferenceSize' => round((100 * ($helper->rateAdvance - 1))),
							'userId' => $userId,
							'hostId' => $hostId,
							'ticketId' => $ticketId,
							'pointTicket' => $helper->isPointTicket() 
						)
					)
				) {

					throw new Exception('Preference rate impossible.');
				}
			}

			if ( !$anonymousUser ) {
				
				$crc = array(
					'userId'      => $userId,
					'hostId'      => $hostId,
					'ticketId'    => $ticketId,
					'pointTicket' => $helper->isPointTicket()
				);
				
				if ( $helper->type == It6_Models_Ticket::TYPE_MAXI ) {
					$crc['combinations'] = $helper->getDataForCampaign();
				}
				else {
					$crc['betCount']  = $helper->betCount;
					$crc['stake'] = $helper->stake;
					$crc['rate'] = $helper->rate;
				}
				
				$data = array();
				
				if ( Webservice_Campaign::validate('CreateMoneyTicket', Webservice_Campaign::NAMESPACE_GET, $crc) ) {
					$data['point_create'] = Webservice_Campaign::get(
						'CreateMoneyTicket',
						Webservice_Campaign::NAMESPACE_GET,
						'Value',
						$crc
					);
				}
				Webservice_Campaign::putOn(
					'CreateMoneyTicket',
					Webservice_Campaign::NAMESPACE_GET,
					$crc
				);

				$crc = array(
					'userId'    => $userId,
					'hostId'    => $hostId,
					'ticketId'  => $ticketId
				);
				if ( Webservice_Campaign::validate('BranchVisit', Webservice_Campaign::NAMESPACE_GET, $crc) ) {				
					$data['point_branch_visit'] = Webservice_Campaign::get(
						'BranchVisit',
						Webservice_Campaign::NAMESPACE_GET,
						'Value',
						$crc
					);
				}
				Webservice_Campaign::putOn(
					'BranchVisit',
					Webservice_Campaign::NAMESPACE_GET,
					$crc
				);
				
				if ( !empty($data) ) {
					$db->update(
						Webservice_Ticket::$TABLE,
						$data,
						array('ticket_id = ?' => $ticketId)
					);
				}
			}

			$ticketStatData = new Entities_Ticket();
			$ticketStatData->userId = $userId;
			$ticketStatData->branchId = $branchId;
			$ticketStatData->hostId = $hostId;
			$ticketStatData->type = $helper->type;
			$ticketStatData->createdTime = $dbCreationTime;
			$ticketStatData->paidOutTime = null;
			Webservice_TicketStatistics::creation(
				$ticketId,
				array(
					'ticket' => $ticketStatData,
					'helperCc' => $helperCc,
					'branch' => $branch,
				)
			);

			Webservice_Alert::assert('TicketCreated',
				array('ticketId' => $ticketId, 'ticketAmount' => $helperCc->stake), $userId, $branchId);

			//FIXME: there is not CronJob email implementation so this is helpless,
			//       furthermore implementation of this alert is really bad (performance unaware),
			//       and it has to be rewritten completely
			//Webservice_Alert::assert('TicketDuplicate',
			//	array('ticketId' => $ticketId), $userId, $branchId);

			$logData = array(
				'userId' => $userId,
				'ticketId' => $ticketId,
				'ticket' => serialize($coupon)
			);
			if (It6_Models_Ticket::TYPE_SIMPLE == $helper->type)
				$logData['simpleTicketBet'] = $helper->bets;

			It6_Log::info(
				'Ticket created',
				It6_Log::TAG_USER_OPERATION,
				$logData
			);

			// check partner user
			$partner = Webservice_AffiliatePartner::checkUserPartner($userId);
			if (!empty($partner->partnerId)) {

				$logData["partnerId"] = $partner->partnerId;
				unset($logData["ticket"]);
				unset($logData["simpleTicketBet"]);

				if (Webservice_AffiliatePartner::insertTicket($partner->partnerId, $ticketId, $userId)) {
					It6_Log::info(
						'Ticket inserted to affiliate partner.',
						It6_Log::TAG_USER_OPERATION,
						$logData
					);
				}
			}

			// It6_DbTransaction::commit($db);

			return $ticketId;

		}
		catch ( Exception $e ) {
			// It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception('Can not create ticket. couponId=' . $couponId, 0, $e);
		}
	}

	/**
	 * TODO: move to It6_Models_Helper?
	 * @param integer $userId Owner of ticket (ignored when subject is It6_Models_Ticket)
	 * @param It6_Models_Ticket|struct $subject Instance of helper or ticket entity for helper creation
	 * @param integer|NULL Timestamp for time dependent validation tests, NULL for current timestamp
	 */
	public static function validate($userId, $subject, $timestamp = null) {
		try {

			if ( defined('BETTING_FORBIDDEN') && 1 == BETTING_FORBIDDEN )
				return array(It6_Models_TicketValidator::newError(1000,'Betting forbidden'));

			if (empty($timestamp))
				$timestamp = time();

			if ($subject instanceof It6_Models_Ticket)
				$helper = &$subject;
			else {
				$subject['userId'] = $userId;
				$helper = new It6_Models_Ticket($subject, It6_Models_Ticket::DATA_SERVICE, 'user');
				$helper->computeAggregates();
			}

			$helper->readDataForValidation();
			$vhelper = new It6_Models_TicketValidator($helper, $timestamp);

			/* if all errors are ment to be returned then OK (but web doesn't want all at once)
			return self::joinValidatorResults(array(
				$vhelper->isFreebet(),
				$vhelper->numBet(),
				$vhelper->isAko(),
				$vhelper->isNotSame(),
				$vhelper->riskLimit(),
				$vhelper->isProveTicket(),
				$vhelper->limitDay(),
				$vhelper->isIndvLimit(),
				$vhelper->sameTicket(),
				$vhelper->isValidBet(),
				$vhelper->userHasMoney(),
				$vhelper->checkRateChanges(),
			)); */

			//IT6: freebet not supported anymore (?)
			//if (true !== ($result = $vhelper->isFreebet())) return $result;
			if (true !== ($result = $vhelper->numBet())) return $result;
			if (true !== ($result = $vhelper->isValidBet())) return $result;
			if (true !== ($result = $vhelper->hasNoSameEvent())) return $result;
			if (true !== ($result = $vhelper->isAko())) return $result;
			//if (true !== ($result = $vhelper->isNotSame())) return $result; // obsolete

			$result = array();
			$result[] = $vhelper->riskLimit();
			$result[] = $vhelper->isProveTicket();
			$result[] = $vhelper->limitDay();
			$result[] = $vhelper->isIndvLimit();
			$result[] = $vhelper->isUserLimitSetting();
			$result = $vhelper->mergeMaxStakeErrors($result);
			if (true !== $result) return $result;

			if (true !== ($result = $vhelper->hasNotCorrelatedBets())) return $result;
			if (true !== ($result = $vhelper->maxWinLimit())) return $result;
			if (true !== ($result = $vhelper->sameTicket())) return $result;
			if (true !== ($result = $vhelper->checkCampaign())) return $result;
			if (true !== ($result = $vhelper->userHasMoney())) return $result;
			if (true !== ($result = $vhelper->checkRateChanges())) return $result;
			if (true !== ($result = $vhelper->checkColumns())) return $result;
			return true;
		} catch (Exception $e) {
			It6_Log::err($e);
			return $vhelper->newError(-1,"'ticket_er_-1'");
		}
	}

	private static function joinValidatorResults($results) {
		$ret = true;
		foreach ($results as $result) {
			if ( true !== $result ) {
				if ( $ret === true ) $ret = array();
				$ret[] = $result;
			}
		}
		return $ret;
	}

	/**
	 * Retrives data of ticket or coupon (useful when bookmaker modified coupon)
	 * @param integer $couponId
	 * @return struct|array One or array of ticket structures if valid, array of the errors otherwise
	 */
	public static function refresh($couponId) {
		$tickets = static::getByCouponId($couponId);
		if (!empty($tickets))
			return $tickets;

		$coupon = Webservice_Coupon::getById($couponId);
		if (!empty($coupon))
			return Webservice_Coupon::toWebserviceTicket($coupon);

		throw new It6_XmlRpc_Exception('No data for given coupon ID found. couponId=' . $couponId);
	}

	/**
	 * Start acceptation of coupon if needed or accept coupon that can bypass bookmaker (low amount, timeout etc.).
	 * Coupon status is updated if changed (both in database and passed entity).
	 * Coupon must have been validated before call of this method.
	 * @param struct|integer $coupon Coupon entity if coupon data are available, coupon ID if aren't (only created ticket will be searched)
	 * @param It6_Models_Ticket $helper [optional] Helper instance coupled with passed coupon (user's currency amounts), required if coupon data were specified.
	 * @param integer|NULL $creationTimestamp Explicit timestamp for ticket creation
	 * @return integer|array|boolean|NULL One or array of ticket IDs if acceptation was completed and ticket created (0 = no ticket/s created (yet) ),
	 *                         TRUE if acceptation was started, FALSE if acceptation in progress, NULL if acceptation cannot be started yet.
	 */
	private static function checkCouponAcceptation(&$coupon, $helper = null, $creationTimestamp = null) {
		$db = static::getDb();
		try {
			$onlyId = is_numeric($coupon);
			$couponId = ($onlyId ? $coupon : $coupon->couponId);
//file_put_contents('/tmp/php-debug.log', 'ID:' . print_r($couponId, true) . "\n" . print_r($coupon, true), FILE_APPEND);
			// check if ticket was already created
			$tickets = static::getByCouponId($couponId, array('ticketId'));
			if (!empty($tickets)) {
				// ticket for coupon found, so status must be ACCEPTED
				if (!$onlyId)
					$coupon->status = It6_Models_Ticket::COUPON_STATUS_ACCEPTED;
				if (It6_ArrayWrapper::isArray($tickets)) {
					$ids = array();
					foreach ($tickets as $ticket)
						$ids[] = $ticket['ticketId'];
					return new It6_ArrayWrapper($ids);
				}
				else
					return $tickets['ticketId'];
			}
			else if ($onlyId)
				return 0;

			// no ticket was created from coupon yet

			$status = $coupon->status;
			switch ($status) {
			case It6_Models_Ticket::COUPON_STATUS_REJECTED:
			case It6_Models_Ticket::COUPON_STATUS_MODIFIED:
			case It6_Models_Ticket::COUPON_STATUS_DELAYED:
				return 0;
			case It6_Models_Ticket::COUPON_STATUS_MARKED_AS_ACCEPTED:
			case It6_Models_Ticket::COUPON_STATUS_MARKED_AS_MODIFIED_ACCEPTED:
			case It6_Models_Ticket::COUPON_STATUS_MARKED_AS_DELAYED_ACCEPTED:
				$accepted = true;
				break;
			case It6_Models_Ticket::COUPON_STATUS_ACCEPTED:
				throw new It6_XmlRpc_Exception('Coupon with status ACCEPTED must have ticket created');
			default:
				$accepted = false;
				break;
			}

			$delayAcceptation = (It6_Models_Admin::ID_INTERNET != $coupon->adminId);

			$userId = $coupon->userId;
			$adminId = $coupon->adminId;
			$rpcAdminId = Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_ADMIN);
			if (!$accepted) {
				if (!isset($helper))
					throw new It6_XmlRpc_Exception('Ticket helper instance required but null given. couponId=' . $couponId, 0);
				$timestamp = It6_Date::fromDbAsTimestamp($coupon->date);
				//$data = Zend_Json::decode($coupon['data']);
				$liveConfirm = false;
				if (It6_Models_Ticket::COUPON_STATUS_MARKED_AS_REJECTED == $status
					|| It6_Models_Ticket::COUPON_STATUS_MARKED_AS_MODIFIED_REJECTED == $status
					|| It6_Models_Ticket::COUPON_STATUS_MARKED_AS_DELAYED_REJECTED == $status) {
					$newStatus = It6_Models_Ticket::COUPON_STATUS_REJECTED;
					Webservice_Coupon::updateStatus($couponId, $newStatus);
					Webservice_Coupon::archiveCoupon($couponId);
					$msg = 'Coupon rejected';
					if (It6_Models_Ticket::COUPON_STATUS_MARKED_AS_MODIFIED_REJECTED == $status)
						$msg .= ' - modified';
					else if (It6_Models_Ticket::COUPON_STATUS_MARKED_AS_DELAYED_REJECTED == $status)
						$msg .= ' - delayed';
					It6_Log::info(
						$msg,
						It6_Log::TAG_TICKET_APPROVAL,
						array(
							'userId' => $userId,
							'adminId' => $adminId,
							'rpcAdminId' => $rpcAdminId,
							'couponId' => $couponId,
							'coupon' => serialize($coupon)
						)
					);
					return 0;
				}
				else if ($helper->needsConfirmation($status, $timestamp, $couponId, $reason, $liveConfirm, $db)) {
					if (It6_Models_Ticket::COUPON_STATUS_MARKED_AS_IN_ACCEPTATION == $status) {
						// entering acceptation
						if ($liveConfirm)
							$newStatus = It6_Models_Ticket::COUPON_STATUS_IN_ACCEPTATION_LIVE;
						else
							$newStatus = It6_Models_Ticket::COUPON_STATUS_IN_ACCEPTATION;
						Webservice_Coupon::updateStatus($couponId, $newStatus, null, $reason);
						$coupon->status = $newStatus;
						It6_Log::info(
							'Coupon entered acceptation',
							It6_Log::TAG_TICKET_APPROVAL,
							array(
								'userId' => $userId,
								'adminId' => $adminId,
								'rpcAdminId' => $rpcAdminId,
								'couponId' => $couponId,
								'coupon' => serialize($coupon),
								'reason' => $reason,
							)
						);
						return true;
					}
					else if (It6_Models_Ticket::COUPON_STATUS_IN_ACCEPTATION == $status)
						// still in acceptation
						return false;
					else
						// cannot enter acceptation
						return null;
				}
				else if (It6_Models_Ticket::COUPON_STATUS_PROLONGED == $status
					|| It6_Models_Ticket::COUPON_STATUS_IN_ACCEPTATION == $status) {
					// confirmation timeout elapsed => reject
					$newStatus = It6_Models_Ticket::COUPON_STATUS_REJECTED;
					$coupon->status = $newStatus;
					Webservice_Coupon::updateStatus($couponId, $newStatus);
					Webservice_Coupon::archiveCoupon($couponId);
					$msg = 'Coupon rejected - '
						. (It6_Models_Ticket::COUPON_STATUS_PROLONGED == $status ? 'prolonged' : '')
						. ' acceptation timeout';
					It6_Log::info(
						$msg,
						It6_Log::TAG_TICKET_APPROVAL,
						array(
							'userId' => $userId,
							'adminId' => $adminId,
							'rpcAdminId' => $rpcAdminId,
							'couponId' => $couponId,
							'coupon' => serialize($coupon)
						)
					);
					return 0;
				}
				else if (It6_Models_Ticket::COUPON_STATUS_MARKED_AS_IN_ACCEPTATION == $status) {
					// can bypass acceptation
					$msg = 'Coupon bypassed acceptation';
					if ($delayAcceptation) {
						$newStatus = It6_Models_Ticket::COUPON_STATUS_DELAYED;
						$msg .= ' - delayed';
					}
					else
						$newStatus = It6_Models_Ticket::COUPON_STATUS_ACCEPTED;
					$coupon->status = $newStatus;
					Webservice_Coupon::updateStatus($couponId, $newStatus);
					It6_Log::info(
						$msg,
						It6_Log::TAG_TICKET_APPROVAL,
						array(
							'userId' => $userId,
							'adminId' => $adminId,
							'rpcAdminId' => $rpcAdminId,
							'couponId' => $couponId,
							'coupon' => serialize($coupon)
						)
					);
					if ($delayAcceptation)
						return 0;
				}
				else
					return null;
			}
			else { // $accepted
				if ($delayAcceptation && It6_Models_Ticket::COUPON_STATUS_MARKED_AS_ACCEPTED == $status) {
					$newStatus = It6_Models_Ticket::COUPON_STATUS_DELAYED;
					$coupon->status = $newStatus;
					Webservice_Coupon::updateStatus($couponId, $newStatus);
					It6_Log::info(
						'Coupon delayed',
						It6_Log::TAG_TICKET_APPROVAL,
						array(
							'userId' => $userId,
							'adminId' => $adminId,
							'rpcAdminId' => $rpcAdminId,
							'couponId' => $couponId,
							'coupon' => serialize($coupon)
						)
					);
					return 0;
				}
				else if (It6_Models_Ticket::COUPON_STATUS_MARKED_AS_ACCEPTED == $status
					|| It6_Models_Ticket::COUPON_STATUS_MARKED_AS_MODIFIED_ACCEPTED == $status
					|| It6_Models_Ticket::COUPON_STATUS_MARKED_AS_DELAYED_ACCEPTED == $status) {

					$msg = 'Coupon accepted';
					if (It6_Models_Ticket::COUPON_STATUS_MARKED_AS_MODIFIED_ACCEPTED == $status)
						$msg .= ' - modified';
					else if (It6_Models_Ticket::COUPON_STATUS_MARKED_AS_DELAYED_ACCEPTED == $status)
						$msg .= ' - after delay';

					$newStatus = It6_Models_Ticket::COUPON_STATUS_ACCEPTED;
					$coupon->status = $newStatus;
					Webservice_Coupon::updateStatus($couponId, $newStatus);
					It6_Log::info(
						$msg,
						It6_Log::TAG_TICKET_APPROVAL,
						array(
							'userId' => $userId,
							'adminId' => $adminId,
							'rpcAdminId' => $rpcAdminId,
							'couponId' => $couponId,
							'coupon' => serialize($coupon)
						)
					);
				}
			}
			// coupon was just accepted
			return static::createTicketFromCoupon($coupon, $helper, $creationTimestamp);
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception('Cannot check coupon acceptation: id=' . $couponId, 0, $e);
		}
	}

	/**
	 * Updates status of coupon acceptation and returns current status
	 * NOTE: this function should not be accessed by other clients than confirmd daemon
	 * <ul>
	 * <li>0 - waiting</li>
	 * <li>1 - canceled</li>
	 * <li>2 - accepted</li>
	 * <li>3 - accepted with modifications</li>
	 * <li>4 - timeout</li>
	 * </ul>
	 * @param integer|struct $coupon Identifier of coupon or coupon struct
	 * @param integer|NULL $creationTimestamp Explicit timestamp for ticket creation
	 * @return integer|array Status of the ticket or array of errors
	 */
	public static function checkAuthorisationProcess($coupon, $creationTimestamp = null) {
		if (is_numeric($coupon)) {
			$couponId = $coupon;
			$coupon = Webservice_Coupon::getById($couponId);
		}
		else
			$couponId = $coupon['couponId'];
		if (false === $coupon) {
			// coupon not found but still there could be a ticket that was created from coupon
			$acceptation = static::checkCouponAcceptation($couponId);
			if (is_bool($acceptation))
				return self::STATUS_WAITING;
			else if (empty($acceptation))
				return self::STATUS_CANCELED;
			else
				return self::STATUS_ACCEPTED;
		}

		$data = Zend_Json::decode($coupon['data']);
		$data['userId'] = $coupon['userId'];
		$helper = new It6_Models_Ticket($data, It6_Models_Ticket::DATA_AJAX, 'user');
		$status = $coupon['status'];
		if (It6_Models_Ticket::COUPON_STATUS_MODIFIED == $status
			|| It6_Models_Ticket::COUPON_STATUS_MARKED_AS_MODIFIED_ACCEPTED == $status
			|| It6_Models_Ticket::COUPON_STATUS_MARKED_AS_MODIFIED_REJECTED == $status
			)
			$helper->mergePreapproved($coupon['modified']);
		$helper->computeAggregates();
		if (!static::isCouponValidityCached($couponId)) {
			$helper->readDataForValidation();
			if (empty($creationTimestamp))
				$creationTimestamp = time();
			$validationResult = static::validate($coupon->userId, $helper, $creationTimestamp);
			if (true !== $validationResult)
				return $validationResult;
			static::setCacheForCouponValidity($couponId); 
		}
		try {
			$acceptation = static::checkCouponAcceptation($coupon, $helper, $creationTimestamp);
			$status = $coupon['status'];
			if (!array_key_exists($status, self::$STATUS_MAP))
				throw new It6_XmlRpc_Exception('Unknown coupon status was found: id=' . $couponId . '; status=' . $status, 0, $e);
			return self::$STATUS_MAP[$status];
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception('Cannot check coupon status: id=' . $couponId, 0, $e);
		}
	}

	/**
	 * Reads and returns current status data of coupon.
	 * <pre>
	 * {
	 *   "status" : integer, // see below
	 *   "time" : integer, // seconds status is lasting
	 *   "timeMax" : integer // seconds status can last
	 * }
	 * </pre>
	 * Time fields are present only if status is "waiting".
	 * Status field:
	 * <ul>
	 *  <li>0 - waiting</li>
	 *  <li>1 - canceled</li>
	 *  <li>2 - accepted</li>
	 *  <li>3 - accepted with modifications</li>
	 *  <li>4 - timeout</li>
	 * </ul>
	 * @param integer $couponId Identifier of coupon
	 * @return struct|array Status structure of the ticket or array of errors
	 */
	public static function checkAuthorisationStatus($couponId) {
		if (is_numeric($couponId)) {
			$coupon = Webservice_Coupon::getById($couponId);
			if (false === $coupon) {
				// coupon not found but still there could be a ticket that was created from coupon
				$coupon = self::getByCouponId($couponId);
				if (false === $coupon)
					throw new It6_XmlRpc_Exception('Coupon not found. couponId='. $couponId);
				else
					return array('status' => self::STATUS_ACCEPTED);
			}
			else {
				if (It6_Models_Ticket::COUPON_STATUS_INTERRUPTED_ACCEPTATION == $coupon['status'])
					return array(It6_Models_TicketValidator::newError(1, 'Ticket is not valid'));
				else {
					if (It6_Models_Ticket::COUPON_STATUS_ACCEPTED == $coupon['status']
						|| It6_Models_Ticket::COUPON_STATUS_REJECTED == $coupon['status'])
						Webservice_Coupon::delete($couponId);
					$status = self::$STATUS_MAP[$coupon['status']];
					$result = array('status' => $status);
					if (self::STATUS_WAITING == $status) {
						$dbAdmin = static::getAdminDb();
						$paramName = $coupon['prolonged']
							? It6_Models_Parameter::NAME_TICKET_CONFIRM_TIME_MORE
							: It6_Models_Parameter::NAME_TICKET_CONFIRM_TIME;
						$param = It6_Models_Parameter::getDataByName($paramName, $dbAdmin);
						if (empty($param))
							throw new It6_XmlRpc_Exception('Required system parameter not found. name=' . $paramName);
						$result['time'] = time() - It6_Date::fromDbAsTimestamp($coupon['date']);
						$result['timeMax'] = intval($param['value']);
					}
					return $result;
				}
			}
		}
		throw new It6_XmlRpc_Exception('Invalid coupon ID format');
	}

	/**
	 * Retrieves ticket ID on base of passed coupon ID or accept ticket modified by bookmaker (starts transaction).
	 * @param integer $couponId
	 * @param boolean $reject boolean true if ticket should be rejected
	 * @return integer|NULL|array One or array of ticket IDs if ticket was accepted, zero if rejected,
	 *                            NULL if transaction was started or is still in in progess, array of errors otherwise.
	 */
	public static function acceptAutorisedTicket($couponId, $reject = false) {
		static $waitCounter = 0;
		$waitForConfirmd = false;
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$tickets = static::getByCouponId($couponId, array('ticketId'));
			if (!empty($tickets)) {
				if (It6_ArrayWrapper::isArray($tickets)) {
					$ids = array();
					foreach ($tickets as $ticket)
						$ids[] = $ticket['ticketId'];
					$result = new It6_ArrayWrapper($ids);
				}
				else
					$result = $tickets['ticketId'];
				It6_DbTransaction::commit($db);
				return $result;
			}

			$coupon = Webservice_Coupon::getById($couponId);
			if (false === $coupon)
				throw new It6_XmlRpc_Exception('Coupon not found: ' . $couponId, 0);
			if (It6_Models_Ticket::COUPON_STATUS_INTERRUPTED_ACCEPTATION == $coupon['status'])
				throw new It6_XmlRpc_Exception($coupon['statusMessage'], 0);

			if (It6_Models_Ticket::COUPON_STATUS_MODIFIED == $coupon->status) {
				if ($reject) {
					//$coupon->status = It6_Models_Ticket::COUPON_STATUS_MARKED_AS_MODIFIED_REJECTED;
					$coupon->status = It6_Models_Ticket::COUPON_STATUS_REJECTED;
					Webservice_Coupon::updateStatus($coupon->couponId, $coupon->status);
					Webservice_Coupon::archiveCoupon($coupon->couponId, true);
					It6_Log::info(
						'Modified coupon rejected by user.',
						It6_Log::TAG_TICKET_APPROVAL,
						array(
							'userId' => $coupon->userId,
							'adminId' => $coupon->adminId,
							'couponId' => $coupon->couponId,
							'coupon' => serialize($coupon)
						)
					);
					$result = null;
				}
				else {
					$data = Zend_Json::decode($coupon->data);
					$data['userId'] = $coupon->userId;
					$helper = new It6_Models_Ticket($data, It6_Models_Ticket::DATA_AJAX, 'user');
					$modified = Zend_Json::decode($coupon->modified);
					$helper->mergePreapproved($modified);
					$helper->computeAggregates();
					$validationResult = static::validate($coupon->userId, $helper);
					if (true !== $validationResult)
						$result = $validationResult;
					else {
						$coupon->status = It6_Models_Ticket::COUPON_STATUS_MARKED_AS_MODIFIED_ACCEPTED;
						Webservice_Coupon::updateStatus($coupon->couponId, $coupon->status);
						It6_Log::info(
							'Modified coupon accepted by user.',
							It6_Log::TAG_TICKET_APPROVAL,
							array(
								'userId' => $coupon->userId,
								'adminId' => $coupon->adminId,
								'couponId' => $coupon->couponId,
								'coupon' => serialize($coupon)
							)
						);
						$result = null;
					}
				}
			}
			else if (It6_Models_Ticket::COUPON_STATUS_DELAYED == $coupon->status) {
				if ($reject) {
					//$coupon->status = It6_Models_Ticket::COUPON_STATUS_MARKED_AS_DELAYED_REJECTED;
					$coupon->status = It6_Models_Ticket::COUPON_STATUS_REJECTED;
					Webservice_Coupon::updateStatus($coupon->couponId, $coupon->status);
					Webservice_Coupon::archiveCoupon($coupon->couponId, true);
					It6_Log::info(
						'Delayed coupon rejected.',
						It6_Log::TAG_TICKET_APPROVAL,
						array(
							'userId' => $coupon->userId,
							'adminId' => $coupon->adminId,
							'couponId' => $coupon->couponId,
							'coupon' => serialize($coupon)
						)
					);
					$result = null;
				}
				else {
					$coupon->status = It6_Models_Ticket::COUPON_STATUS_MARKED_AS_DELAYED_ACCEPTED;
					Webservice_Coupon::updateStatus($coupon->couponId, $coupon->status);
					It6_Log::info(
						'Delayed coupon accepted.',
						It6_Log::TAG_TICKET_APPROVAL,
						array(
							'userId' => $coupon->userId,
							'adminId' => $coupon->adminId,
							'couponId' => $coupon->couponId,
							'coupon' => serialize($coupon)
						)
					);
					$result = null;
					$waitForConfirmd = true;
				}
			}
			else if (It6_Models_Ticket::COUPON_STATUS_MARKED_AS_MODIFIED_ACCEPTED == $coupon->status
				|| It6_Models_Ticket::COUPON_STATUS_MARKED_AS_MODIFIED_REJECTED == $coupon->status
				|| It6_Models_Ticket::COUPON_STATUS_MARKED_AS_DELAYED_ACCEPTED == $coupon->status
				|| It6_Models_Ticket::COUPON_STATUS_MARKED_AS_DELAYED_REJECTED == $coupon->status)
				$result = null;
			else if (It6_Models_Ticket::COUPON_STATUS_ACCEPTED == $coupon->status)
				throw new It6_XmlRpc_Exception('Internal error. Coupon was accepted, but ticket was not found. couponId=' . $coupon->couponId, 0);
			else if (It6_Models_Ticket::COUPON_STATUS_REJECTED == $coupon->status)
				$result = 0;
				//throw new It6_XmlRpc_Exception('Coupon was already rejected. couponId=' . $coupon->couponId, 0);
			else
				throw new It6_XmlRpc_Exception('Coupon in incosistent state. couponId=' . $coupon->couponId . ' status=' . $status, 0);

			It6_DbTransaction::commit($db);
		}
		catch (Exception $e) {
			It6_DbTransaction::rollback($db);
			//throw new It6_XmlRpc_Exception('Cannot accept/reject authorized coupon: couponId=' . $couponId, 0, $e);
			return 0;
		}
		if ($waitForConfirmd) {
			if ($waitCounter < 1) { // max one retry
				++$waitCounter;
				usleep(250000);
				return static::acceptAutorisedTicket($couponId, $reject);
			}
		}
		return $result;
	}

	/**
	 * Update ticket.
	 * @param struct $ticket structure of the ticket
	 * @return true on success
	 * @see Entities_Ticket
	 */
	public static function update($ticket) {
		//TODO: ? abandone and not implement me ?
		// not sure if this will be allowed
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Delete ticket.
	 * @param integer $ticketId identifier of the ticket.
	 * @return true on success
	 * @see Entities_Ticket
	 */
	public static function delete($ticketId) {
		//TODO: ? abandone and not implement me ?
		// not sure if this will be allowed
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Collect the paid out ticket.
	 * @param integer $ticketId identifier of the ticket.
	 * @param boolean $nohost collection from the internet
	 * @param integer $depositUserId if null money are payed in cash to user, otherwise money are deposited to the fiven user account, for non annonymou tickets userId must be same like this id
	 * @return boolean|array TRUE on success, array of tip data if some tip wasn't paid out [[tipId,tipAlias,tipName],...]
	 */
	public static function collect($ticketId, $nohost = false, $depositUserId = null) {

		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {

			if ( !Webservice_Host::isOutAllowed() )
				throw new Exception("Collection is disabled for this host.");

			$acl = Zend_Registry::get('acl');
			$hostId = $acl->getIdentity(It6_Acl::IDNAME_HOST);
			$branchId = $acl->getIdentity(It6_Acl::IDNAME_BRANCH);

			$ticket = static::getById($ticketId);
			$notPaidOutTips = array();
			foreach ($ticket['groups'] as $group) {
				foreach ($group['tips'] as $tip) {
					if (empty($tip['paidOut']))
						$notPaidOutTips[$tip['betId']] = array(
							$tip['betId'], $tip['alias'], $tip['betName']
						);
				}
			}
			if (!empty($notPaidOutTips))
				throw new It6_XmlRpc_InvalidTipsException(array_values($notPaidOutTips), 'Not all tips have been paid out', 1);

			if ( false == $ticket )
				throw new Exception('Unknown ticket id.\''.$ticketId.'\'');

			else if ( empty($ticket['paidOutTime']) ) {
				throw new Exception("Ticket was not paid out.");
			}
			else if ( !empty($ticket['collectionTime']) ) {
				throw new Exception("Ticket already collected.");
			}
			else if ( empty($ticket['cash']) ) {
				throw new Exception("This is not cash ticket.");
			}
			else if ( !empty($ticket['forfeit']) ) {
				throw new Exception("This ticket already forfeit.");
			}
			else if ( !empty($ticket['isLoss']) || empty($ticket['realWinAmount']) || 0 >= $ticket['realWinAmount']) {
				throw new Exception("This is not wining ticket.");
			}


			if ( !empty($depositUserId) ) {
				if ( empty($ticket['anonymous']) && $depositUserId != $ticket['userId'] )
					throw new Exception("Can not deposit user's account from non anonymous foreign ticket.");

				$depositUser = Webservice_User::getById($depositUserId);
				if ( $depositUser['anonymous'] )  
					throw new Exception("Can not deposit annonymous user.");
				else if ( empty($depositUser['activationTime']) )  
					throw new Exception("Can not deposit user. User is not activated.");
			}

			$helper = new It6_Models_Ticket($ticket, It6_Models_Ticket::DATA_SERVICE, 'user', $db);
			$helper->computeAggregates();

			$data = array();
			$data['collection_time'] = It6_Date::dbNow();
			$data['collection_host_id'] = $hostId;

			if ( !empty($ticket['anonymous']) && !empty($depositUserId) ) {
				$data['user_id'] = $depositUserId;
				$data['collection_account_user_id'] = $depositUserId;
			}
			
			if ( !$nohost  && $ticket['forcedCollectionHostId'] != null &&
				 	$ticket['forcedCollectionHostId'] != $hostId ) {

				throw new Exception('Ticket must be collected in host: ' . $ticket['forcedCollectionHostId']);
			}

			else if ( $nohost && $ticket['forcedCollectionHostId'] != It6_Models_Host::ID_INTERNET ) {

				throw new Exception('Ticket must not be collected from the internet.');
			}

			$db->update(
				static::$TABLE,
				$data,
				array( static::$IDENTITY . '= ?' => $ticketId)
			);

			$currencyId = It6_Models_User::get($ticket->userId, 'currencyId', $db);
			$value = -$ticket->realWinAmount;
			if ( floatval($ticket->mpWin) > 0 && empty($helper->canceled) ) {
				$value += $ticket->mpWinAmount;
			}
			if ( !$nohost ) {
				Webservice_Transaction::make(array(
						'value' => $value,
						'hostId' => $hostId,
						'userId' => $ticket->userId,
						'ticketId' => $ticket->ticketId,
						'typeName' => Webservice_TransactionType::NAME_BRANCH_TICKET_COLLECT,
						'currencyId' => $currencyId
					));
			}
			else {
				if ( $hostId != It6_Models_Host::ID_INTERNET )
					throw new Exception('Just internet can make this operation');
				
				Webservice_Transaction::make(array(
						'value' => $value,
						'hostId' => It6_Models_Host::ID_INTERNET,
						'userId' => $ticket->userId,
						'ticketId' => $ticket->ticketId,
						'typeName' => Webservice_TransactionType::NAME_OTHER_TICKET_COLLECT_INDIVIDUAL,
						'currencyId' => $currencyId
					));
			}
			
			if ( !empty($depositUserId) ) {

				Webservice_Transaction::make(array(
						'value' => -$value,
						'hostId' => $hostId,
						'userId' => $depositUserId,
						'ticketId' => $ticket->ticketId,
						'typeName' => Webservice_TransactionType::NAME_USER_DEPOSIT_CASH_TICKET_WIN,
						'currencyId' => $currencyId
					));

				Webservice_Transaction::make(array(
						'value' => -$value,
						'hostId' => $hostId,
						'userId' => $depositUserId,
						'ticketId' => $ticket->ticketId,
						'typeName' => Webservice_TransactionType::NAME_BRANCH_USER_DEPOSIT_CASH_TICKET_WIN,
						'currencyId' => $currencyId
					));
			}

			It6_DbTransaction::commit($db);

			if ( empty($depositUserId) ) {
				It6_Log::info(
						"Ticket '%ticket%' was collected.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('ticket' => $ticketId,'user' => $ticket->userId)
					);
			}
			else {
				It6_Log::info(
						"Ticket '%ticket%' was collected and deposited to user: '%user%'",
						It6_Log::TAG_ADMIN_OPERATION,
						array('ticket' => $ticketId,'user' => $depositUserId)
					);
			}
			
			return true;
		}
		catch ( It6_XmlRpc_InvalidTipsException $e ) {
			It6_DbTransaction::rollback($db);
			return $e->getTips();
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			It6_Log::notice(
						"Ticket '%ticket%' collection failed.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('ticket' => $ticketId)
					);
			throw new It6_XmlRpc_Exception("Can not collect ticket", 0, $e);
		}
	}

	/**
	 * Cancel collect of the ticket, back to just paid out ticket.
	 * @param integer $ticketId identifier of the collected ticket.
	 * @return boolean TRUE on success
	 */
	public static function cancelCollect($ticketId) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {

			$acl = Zend_Registry::get('acl');
			$hostId = $acl->getIdentity(It6_Acl::IDNAME_HOST);
			$branchId = $acl->getIdentity(It6_Acl::IDNAME_BRANCH);

			$ticket = static::getById($ticketId);

			if ( $hostId != It6_Models_Host::ID_INTERNET ) 
				throw new Exception('Cancelation of collection is not allowed from this host.');
			else if ( false == $ticket )
				throw new Exception('Unknown ticket id.\''.$ticketId.'\'');
			else if ( empty($ticket['cash']) )
				throw new Exception("This is not cash ticket.");
			else if ( empty($ticket['collectionTime']) )
				throw new Exception("Ticket was not collected.");

			$helper = new It6_Models_Ticket($ticket, It6_Models_Ticket::DATA_SERVICE, 'user', $db);
			$helper->computeAggregates();

			$data = array();
			$data['collection_time'] = null;
			$data['collection_host_id'] = null;
			$data['collection_account_user_id'] = null;

			$db->update(
				static::$TABLE,
				$data,
				array( static::$IDENTITY . '= ?' => $ticketId)
			);

			$currencyId = It6_Models_User::get($ticket->userId, 'currencyId', $db);
			$value = -$ticket->realWinAmount;
			if ( floatval($ticket->mpWin) > 0 && empty($helper->canceled) ) {
				$value += $ticket->mpWinAmount;
			}
			
			Webservice_Transaction::make(array(
					'value' => -$value,
					'hostId' => $ticket->collectionHostId,
					'userId' => $ticket->userId,
					'ticketId' => $ticket->ticketId,
					'typeName' => Webservice_TransactionType::NAME_BRANCH_TICKET_COLLECT_CANCEL,
					'currencyId' => $currencyId
				));

			
			if ( !empty($ticket->collectionAccountUserId) ) {

				Webservice_Transaction::make(array(
						'value' => $value,
						'hostId' => $ticket->collectionHostId,
						'userId' => $ticket->collectionAccountUserId,
						'ticketId' => $ticket->ticketId,
						'typeName' => Webservice_TransactionType::NAME_USER_DEPOSIT_CASH_TICKET_WIN_CANCEL,
						'currencyId' => $currencyId
					));

				Webservice_Transaction::make(array(
						'value' => $value,
						'hostId' => $ticket->collectionHostId,
						'userId' => $ticket->collectionAccountUserId,
						'ticketId' => $ticket->ticketId,
						'typeName' => Webservice_TransactionType::NAME_BRANCH_USER_DEPOSIT_CASH_TICKET_WIN_CANCEL,
						'currencyId' => $currencyId
					));
			}

			It6_DbTransaction::commit($db);

			if ( empty($ticket->collectionAccountUserId) ) {
				It6_Log::info(
						"Ticket '%ticket%' collection was canceled.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('ticket' => $ticketId,'user' => $ticket->userId)
					);
			}
			else {
				It6_Log::info(
						"Ticket '%ticket%' collection and depositing of user: '%user%' ware canceled.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('ticket' => $ticketId,'user' => $depositUserId)
					);
			}
			
			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			It6_Log::notice(
						"Cancelation of ticket '%ticket%' collection failed.",
						It6_Log::TAG_ADMIN_OPERATION,
						array('ticket' => $ticketId)
					);
			throw new It6_XmlRpc_Exception("Can not cancel collected ticket", 0, $e);
		}
	}
	
	/**
	 * Allow cancelation of old ticket
	 * @param integer $ticketId unique identifier of the ticket
	 * @return boolean true on success
	 */
	public static function allowCancel($ticketId) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$ticket = static::getById($ticketId);
			if ( false == $ticket )
				throw new Exception('Unknown ticket id.\''.$ticketId.'\'');
			if ( !empty($ticket->canceled) )
				throw new Exception('Ticket already canceled.');
			if ( !empty($ticket->cancelAllowed) )
				throw new Exception('Ticket canceletion already alowed.');
			if ( !It6_Date::isNullDbDatetime($ticket['paidOutTime']) )
				throw new Exception('Paid out tickets can not be canceled.');

			$db->update(
				static::$TABLE,
				array('cancel_allowed' => 1),
				array( static::$IDENTITY . '= ?' => $ticketId)
			);

			It6_Log::info(
					"Ticket '%ticket%' cancelation was allowed.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('ticket' => $ticketId));

			It6_DbTransaction::commit($db);

			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Cannot allow ticket cancelation", 0, $e);
		}
	}

	/**
	 * Cancel ticket.
	 * @param integer $ticketId identifier of the ticket
	 * @param string $reason reason to cancel ticket for for renew ignored, default empty
	 * @param boolean $renew if true ticket is renewed, default false
	 * @return true on success
	 */
	public static function cancel($ticketId, $reason = '', $renew = false) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$acl = Zend_Registry::get('acl');
			$adminId = $acl->getIdentity(It6_Acl::IDNAME_ADMIN);
			$branchId = $acl->getIdentity(It6_Acl::IDNAME_BRANCH);
			$canCancelOld = !Webservice_Admin::isSystem($adminId) && $acl->userHasRole(It6_Acl_Admin::ROLE_SUPERADMIN);

			static::setColumnsWithCachedData(true);
			$ticket = static::getById($ticketId);
			static::setColumnsWithCachedData();

			if ( false == $ticket )
				throw new Exception('Unknown ticket id.\''.$ticketId.'\'');
			if ( !$renew && !empty($ticket->canceled) )
				throw new Exception('Ticket already canceled.');
			if ( $renew && empty($ticket->canceled) )
				throw new Exception('Ticket is not canceled.');
			if ( !empty($ticket->paidOut) ) {
				if ( $renew )
					throw new Exception('Paid out tickets cannot be renewed.');
				if ( !$canCancelOld ) 
					throw new Exception('Only privileged admin can cancel paid out tickets.');
			}
			if ( $ticket->cash && !empty($ticket->collectionTime) )
				throw new Exception('Collected cash tickets can not be canceled or renewed.');

			if ( !$renew ) {
				if (!$canCancelOld) {
					$cancelationLimit = intval(Webservice_Parameter::getBranchParameter(
						static::PARAMETER_TICKET_CANCELATION_LIMIT, $branchId));
					if ( $cancelationLimit < It6_Date::nowAsTimestamp() - It6_Date::fromDbAsTimestamp($ticket->createdTime) ) {
						if (empty($ticket->cash))
							$forbidden = true;
						else
							$forbidden = empty($ticket->cancelAllowed);
						if ($forbidden)
							throw new Exception('Ticket is too old to be canceled without allowing.');
					}
				}
			}

			$userId = $ticket['userId'];

			$helper = It6_Models_TicketFactory::newTicketCached($ticket['cachedData'], true, $ticket, It6_Models_Ticket::DATA_SERVICE, 'user', $db);
			//$helper = new It6_Models_Ticket($ticket, It6_Models_Ticket::DATA_SERVICE, 'user', $db);
			//$helper->computeAggregates($db);
			if (!$canCancelOld) {
				$helper->readDataForValidation();
				$vhelper = new It6_Models_TicketValidator($helper);
				if ( true !== $vhelper->isValidBet() )
					throw new Exception('Invalid bets on ticket');
			}

			if (It6_Models_Currency::getCentralCurrencyId($db) != $helper->currencyId) {
				$helperCc = clone $helper;
				$helperCc->convertStakesToCentralCurrency(null, $db);
				$helperCc->computeAggregates($db);
			}
			else
				$helperCc = &$helper;
			$helperCc->saveBetsRiskLimit(false, $db, $renew ? 1 : -1);
			$helperCc->saveSportRiskLimits($userId, false, $db, $renew ? 1 : -1);

			$anonymousUser = It6_Models_User::get($userId, 'anonymous', $db);
			foreach ($helperCc->bets as $bet) {
				$betId = $bet['id'];
				$changes = array(
					'riskLimitBalance'	=> ($renew ? 1 : -1) * $bet['riskAmount'],
					'absoluteStake'		=> ($renew ? 1 : -1) * $bet['absoluteStake'],
					'betCount'		=> ($renew ? 1 : -1) * $bet['inTicketCount'],
				);
				Webservice_Bet::changeBetColumnData($betId, $bet['column'], $changes);

				if (!$anonymousUser) {
					$history = $helperCc->getDataForBetUserHistory($betId);
					if (!empty($history))
						It6_Models_Bet::changeUserHistory($userId, $betId, $history, $renew ? false: true, $db);
				}
			}

			$data = array();
			$data['zruseno'] = $renew ? 0 : 1;
			$data['zrusil_bookmaker_id'] = $renew ? null : $adminId;
			$data['duvod_zruseni'] = $renew ? '' : $reason;
			$data['cancel_time'] = $renew ? null : It6_Date::dbNow();
			$data['vyplacen'] = null;
			$data['vyplacen_date'] = null;
			$data['collection_time'] = null;

			$db->update(
				static::$TABLE,
				$data,
				array( static::$IDENTITY . '= ?' => $ticketId)
			);

			$sign = $renew ? 1 : -1;

			$currencyId = It6_Models_User::get($userId, 'currencyId', $db);
			if ( $ticket->cash && !$helper->isPointTicket() ) {

				Webservice_Transaction::make(array(
					'value' => $sign * $ticket['amount'],
					'userId' => $userId,
					'hostId' => $ticket['hostId'],
					'ticketId' => $ticketId,
					'typeName' => $renew
									? Webservice_TransactionType::NAME_BRANCH_TICKET_RENEW_CANCELED_CASH
									: Webservice_TransactionType::NAME_BRANCH_TICKET_CANCEL_CASH,
					'currencyId' => $currencyId
				));

				if ( floatval($ticket['mp']) > 0 ) {
					Webservice_Transaction::make(array(
						'value' => $sign * $ticket['mp'] * $ticket['amount'],
						'userId' => $userId,
						'hostId' => $ticket['hostId'],
						'ticketId' => $ticketId,
						'typeName' => $renew
										? Webservice_TransactionType::NAME_BRANCH_TICKET_RENEW_CANCELED_CASH_MP
										: Webservice_TransactionType::NAME_BRANCH_TICKET_CANCEL_CASH_MP,
						'currencyId' => $currencyId
					));
				}

				//NOTE: no cancelations of collect-time transactions are performed because we don't allow to cancel collected cash tickets
			}
			else {

				Webservice_Transaction::make(array(
					'value' => -$sign * $ticket['amount'],
					'userId' => $userId,
					'hostId' => $ticket['hostId'],
					'ticketId' => $ticketId,
					'typeName' => $renew
									? Webservice_TransactionType::NAME_USER_TICKET_RENEW_CANCELED
									: Webservice_TransactionType::NAME_USER_TICKET_CANCEL,
					'currencyId' => $currencyId
				));

				if ( floatval($ticket['mp']) > 0 ) {
					Webservice_Transaction::make(array(
						'value' => -$sign * $ticket['mp'] * $ticket['amount'],
						'userId' => $userId,
						'hostId' => $ticket['hostId'],
						'ticketId' => $ticketId,
						'typeName' => $renew
										? Webservice_TransactionType::NAME_USER_TICKET_RENEW_CANCELED_MP
										: Webservice_TransactionType::NAME_USER_TICKET_CANCEL_MP,
						'currencyId' => $currencyId
					));
				}

				if ( !$renew && !empty($ticket->paidOut) && floatval($helper->mpWinAmount) > 0 && empty($helper->canceled) ) {
					Webservice_Transaction::make(array(
						'value' => -$helper->mpWinAmount,
						'userId' => $userId,
						'hostId' => $ticket['hostId'],
						'ticketId' => $ticketId,
						'typeName' => Webservice_TransactionType::NAME_USER_TICKET_COLLECT_NOCASH_CANCEL_MP,
						'currencyId' => $currencyId,
					));
				}
			}

			if (!$renew && !empty($ticket->paidOut) && empty($ticket->isLoss)) {
				Webservice_Transaction::make(array(
					'value' => -$ticket['realWinAmount'],
					'userId' => $userId,
					'hostId' => $ticket['hostId'],
					'ticketId' => $ticketId,
					'typeName' => Webservice_TransactionType::NAME_OTHER_TICKET_PAYOUT_CANCEL,
					'currencyId' => $currencyId
				));
				if (empty($ticket->cash)) {
					Webservice_Transaction::make(array(
						'value' => -$ticket['realWinAmount'],
						'userId' => $userId,
						'hostId' => $ticket['hostId'],
						'ticketId' => $ticketId,
						'typeName' => Webservice_TransactionType::NAME_USER_TICKET_COLLECT_NOCASH_CANCEL,
						'currencyId' => $currencyId
					));
				}
				if ( floatval($helper->mpWinAmount) > 0 && empty($helper->canceled) ) {
					Webservice_Transaction::make(array(
						'value' => $helper->mpWinAmount,
						'userId' => $userId,
						'hostId' => $ticket['hostId'],
						'ticketId' => $ticketId,
						'typeName' => Webservice_TransactionType::NAME_OTHER_TICKET_PAYOUT_CANCEL_MP,
						'currencyId' => $currencyId,
					));
				}
			}
		
			if ( !$renew ) {
				Webservice_Campaign::putOn(
					'CancelPointTicket',
					Webservice_Campaign::NAMESPACE_SPEND,
					array(
						'ticketId'  => $ticketId
					)
				);
				
				Webservice_Campaign::putOn(
					'CancelMoneyTicket',
					Webservice_Campaign::NAMESPACE_GET,
					array(
						'ticketId'  => $ticketId
					)
				);
				Webservice_Campaign::putOn(
					'PreferenceRateCancel',
					Webservice_Campaign::NAMESPACE_SPEND,
					array(
						'ticketId'  => $ticketId
					)
				);
			}
			else {
				Webservice_Campaign::putOn(
					'CreateMoneyTicket',
					Webservice_Campaign::NAMESPACE_GET,
					array(
						'type'        => $helper->type,
						'betCount'    => $helper->betCount,
						'stake'       => $helper->stake,
						'rate'        => $helper->rate,
						'userId'      => $userId,
						'hostId'      => $ticket['hostId'],
						'ticketId'    => $ticketId,
						'pointTicket' => $helper->isPointTicket(),
					)
				);
			}

			static::updateHashCounts($helper, $renew, false);

			if ( !$renew )
				//TODO: if paid out ticket was canceled, it is possible that some statistics are not updated but should be (wins etc.)
				Webservice_TicketStatistics::cancelation($ticketId);
			else
				Webservice_TicketStatistics::creation($ticketId);

			$gameIds = Webservice_Campaign::removeTicketFromGame($ticketId);
			if (!empty($gameIds)) {
				It6_Log::info(
					'Ticket was removed from all games.',
					It6_Log::TAG_CAMPAIGN,
					array('ticketId' => $ticketId, 'gameIds' => $gameIds)
				);
				$gameTickets = array();
				foreach ($gameIds as $gameId) {
					$gameTickets[$gameId][] = $ticketId;
				}
				It6_GlobalCache_Invalidator::Campaign_ticketGame($gameTickets);
			}

			if ( $renew )
				It6_Log::info(
					"Ticket '%ticket%' was renewed.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('ticket' => $ticketId));
			else
				It6_Log::info(
					"Ticket '%ticket%' was canceled.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('ticket' => $ticketId));

			It6_DbTransaction::commit($db);

			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			It6_Log::notice(
				"Ticket '%ticket' cancelation/renewal failed.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('ticket' => $ticketId), $e);
			throw new It6_XmlRpc_Exception("Cannot cancel or renew ticket", 0, $e);
		}
	}


	/**
	 * Renew canceled ticket.
	 * @param integer $ticketId identifier of the ticket
	 * @return true on success
	 */
	public static function renewCanceled($ticketId) {
		return static::cancel($ticketId,'',true);
	}

	/**
	 * Function decreases/increases duplicity counter because of cancelation/renewal
	 * @param It6_Models_Ticket $helper
	 * @param boolean $renew
	 */
	private static function updateHashCounts($helper, $renew, $useTransaction = true) {
		$db = static::getDb();
		if ($useTransaction)
			It6_DbTransaction::begin($db);
		try {
			if ($renew)
				$helper->saveTicketHashes(false, null, $db);
			else
				$helper->deleteTicketHashes(false, $db);
			if ($useTransaction)
				It6_DbTransaction::commit($db);
			return true;
		}
		catch (Exception $e) {
			if ($useTransaction)
				It6_DbTransaction::rollback($db);
			return false;
		}
	}

	/**
	 * Cancel each bet in the ticket. Same like you call cancelBet on each bets.
	 * @param integer $ticketId identifier of the ticket
	 * @param string $reason
	 * @return true on success
	 */
	public static function cancelAllBets($ticketId, $reason = '') {
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {

			if ( empty($ticketId) )
				throw new Excpetion('Missing $ticketId.');

			$ticket = static::getById($ticketId);

			if ( empty($ticket) )
				throw new Exception('Unknown $ticketId.');

			foreach ( $ticket->groups as $group ) {
				foreach ( $group->tips as $tip ) {
					if ( empty($tip->canceled) ) {
						static::cancelBet($ticketId, $tip->betId, $reason);
					}
				}
			}

			It6_Log::info(
				"All bets on ticket '%ticket%' was canceled (rated as 1).",
				It6_Log::TAG_ADMIN_OPERATION,
				array('ticket' => $ticketId));

			It6_DbTransaction::commit($db);

			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			It6_Log::notice(
				"Cancelation of all bets on ticket '%ticket%' failed.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('ticket' => $ticketId), $e);
			throw new It6_XmlRpc_Exception("Cannot cancel all bets on the ticket: '$ticketId'", 0, $e);
		}
	}

	/**
	 * Cancel bet on ticket.
	 * @param integer $ticketId identifier of the ticket
	 * @param integer $betId identifier of the bet
	 * @param string $reason reason to cancel for renew is ignored
	 * @param boolean $renew if true the bet on the ticket is reopened
	 * @return true on success
	 */
	public static function cancelBet($ticketId, $betId, $reason = '', $renew = false) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$acl = Zend_Registry::get('acl');
			$adminId = $acl->getIdentity(It6_Acl::IDNAME_ADMIN);

			$ticket = It6_ArrayWrapper::toNativeArray(static::getById($ticketId));

			if ( false == $ticket )
				throw new Exception('Unknown ticket id.\''.$ticketId.'\'');

			if ( !empty($ticket->canceled) ) {
				throw new Exception("Ticket is canceled.");
			}
			else if ( !It6_Date::isNullDbDatetime($ticket['paidOutTime']) ) {
				throw new Exception("Bets on paid out tickets can not be canceled or renewed.");
			}

			$data = array();
			$data['ticket_sazka_zrusena'] = $renew ? 0 : 1;
			$data['ticket_sazka_zrusil_bookmaker_id'] = $renew ? null : $adminId;
			$data['ticket_sazka_duvod_zruseni'] = $renew ? '' : $reason;
			//$data['cancel_time'] = It6_Date::dbNow();

			$db->update(
				static::$TIPS_TABLE,
				$data,
				array(
					'sazka_id = ?' => $betId,
					'ticket_id = ?' => $ticketId));

			foreach ( $ticket['groups'] as &$group ) {
				$group = (array)$group;
				foreach ( $group['tips'] as &$tip ) {
					$tip = (array)$tip;
					if ( $tip['betId'] == $betId ) {
						$tip['canceled'] = !$renew;
						break;
					}
				}
			}

			$helper = new It6_Models_Ticket($ticket, It6_Models_Ticket::DATA_SERVICE, 'user', $db);
			$helper->computeAggregates();

			foreach ($helper->bets as $bet) {
				$betId = $bet['id'];
				$changes = array(
						'riskLimitBalance'	=> ($renew ? 1 : -1) * $bet['riskAmount'],
						'absoluteStake'		=> ($renew ? 1 : -1) * $bet['absoluteStake'],
						'betCount'			=> ($renew ? 1 : -1) * $bet['inTicketCount'],
				);
				Webservice_Bet::changeBetColumnData($betId, $bet['column'], $changes);
			}
			
			$data = array();
			$data['win'] = $helper->win;
			$data['rate'] = $helper->rate;


			$db->update(
				static::$TABLE,
				$data,
				array('ticket_id = ?' => $ticketId));

			if ( $renew ) {
				It6_Log::info(
					"Bet '%bet% on ticket '%ticket%' was renewed.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('ticket' => $ticketId, 'bet' => $betId));
			}
			else {
				It6_Log::info(
					"Bet '%bet% on ticket '%ticket%' was canceled (rated as 1).",
					It6_Log::TAG_ADMIN_OPERATION,
					array('ticket' => $ticketId, 'bet' => $betId));
			}

			It6_DbTransaction::commit($db);

			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			It6_Log::notice(
				"Cancelation/renewal of bet '%bet' on ticket '%ticket%' failed.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('ticket' => $ticketId, 'bet' => $betId), $e);
			throw new It6_XmlRpc_Exception("Cannot cancel/renew bet on the ticket", 0, $e);
		}
	}


	/**
	 * Cancel canceleation of bet on ticket.
	 * @param integer $ticketId identifier of the ticket
	 * @param integer $betId identifier of the bet
	 * @return true on success
	 */
	public static function rewnewCanceledBet($ticketId, $betId) {
		return static::cancelBet($ticketId, $betId, '', true);
	}

	/**
	 * Returns all open (not canceled and not payed off) tickets in the system.
	 * @return array array of the ticket structures
	 * @see Entities_Ticket
	 */
	public static function getOpened($extensions = null) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Returns all canceled tickets in the system.
	 * @return array array of the ticket structures
	 * @see Entities_Ticket
	 */
	public static function getCanceled($extensions = null) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Returns all payed off tickets in the system.
	 * @return array array of the ticket structures
	 * @see Entities_Ticket
	 */
	public static function getPaidOut($extensions = null) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Returns all bets on the given tickets.
	 * @param integer $tickedId identifier of the ticket
	 * @return array array of the bet structures
	 * @see Entities_Bet
	 */
	public static function getAllBets($ticketId, $extensions = null) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}


	public static function toEntity($ticket, $columns = null) {
		$db = static::getDb();
		$ret = parent::toEntity($ticket, $columns);
		if (!static::$_columnsWithCachedData)
			unset($ret->cachedData);

		try {
			if($ticket['paidOut'] == 0 && $ticket['canceled'] ==  0) {
				$ret->state = 'ticket_state_1';
				$ret->stateNo = 1;
			}
			else if($ticket['canceled'] == 1) {
				$ret->stateNo = 4;
				$ret->state = 'ticket_state_4';
				$ret->rate = 1;
				$ret->win = $ticket['amount'];
			}
			else if(!empty($ticket['forfeit'])) {
				$ret->stateNo = 6;
				$ret->state = 'ticket_state_6';
				$ret->rate = $ticket['realTotalOdds'];
				$ret->win = $ticket['realWinAmount'];
			} else if($ticket['paidOut'] == 1 && $ticket['canceled'] == 0 && $ticket['isLoss'] == 0) {
				if ( empty($ticket['cash']) || !empty($ticket['collectionTime']) ) {
					$ret->stateNo = 2;
					$ret->state = 'ticket_state_2';
				}
				else {
					$ret->stateNo = 5;
					$ret->state = 'ticket_state_5';
				}
				
				$ret->rate = $ticket['realTotalOdds'];
				$ret->win = $ticket['realWinAmount'];
			}
			else if($ticket['paidOut'] == 1 && $ticket['canceled'] == 0 && $ticket['isLoss'] == 1) {
				$ret->stateNo = 3;
				$ret->state = 'ticket_state_3';
				$ret->rate = $ticket['totalOdds'];
				$ret->win = $ticket['winAmount'];
			}

			if($ticket['type'] == 'simple')
				$ret->typex = 'ticket_simple';
			else if($ticket['type'] == 'kombi')
				$ret->typex = 'ticket_combi';
			else if($ticket['type'] == 'system')
				$ret->typex = 'ticket_system';
			else if ('maxikombi' == $ticket['type'])
				$ret->typex = 'ticket_maxicombi';
			else $ret->typex = '';

			if (!empty($ticket['username']))
				$ret->username = $ticket['username'];

			$tips = $db->select()
				->from(
					array('t' => Webservice_Ticket::$TIPS_TABLE),
					array(
						'sloupec_id',
						'sazka_id',
						'group_id',
						'rate',
						'ticket_sazka_zrusena',
						'ticket_sazka_zrusil_bookmaker_id',
						'ticket_sazka_duvod_zruseni',
						'order'))
				->join(
					array('s' => Webservice_Bet::$OUTCOME_TABLE),
					's.sloupec_id = t.sloupec_id', array('nazev') )
				->join(
					array('sz' => Webservice_Bet::$TABLE),
					't.sazka_id = sz.sazka_id',
					array('ako', 'betName' => "IF(sz.ticket_text IS NULL OR sz.ticket_text='',sz.text,sz.ticket_text)", 'result' => 'vysledek',
						'validTo' => 'platna_do', 'paidOut' => 'proplacena',
						'paidOutByBookmakerId' => 'proplatil_bookmaker',
						'status' => 'status', 'alias' => 'alias', 'alias_new' => 'alias_new',
						'betTextNote' => 'text_note', 'score'))
				->join(array('u' => 'udalost'), 'u.udalost_id=sz.udalost_id', array('eventName' => 'nazev'))
				->join(array('sp' => 'sport'), 'sp.sport_id=u.sport_id', array('sportName' => 'nazev'))
				->join(array('o' => 'oblast'), 'o.oblast_id=u.oblast_id', array('regionName' => 'nazev'))
				->join(
					array('tp' => 'podtyp'),
					'sz.podtyp_id = tp.podtyp_id', array('oddsTypeName' => 'tp.interni_nazev') )
				->join(
					array('tpt' => 'typ_podtyp'),
					'tpt.podtyp_id=tp.podtyp_id', array()
				)->join(
					array('tt' => 'typ'),
					'tt.typ_id=sz.typ_id', array('alias2' => 'typ_alias_id', 'typeName' => 'nazev')
				)->where('t.ticket_id = ?', $ret->ticketId)
				->group('t.sloupec_id')
				->group('t.sazka_id')
				->query()->fetchAll();

			$groups = array();
			$dictionary = array();
			//$ret->tips = $tips;
			$order = 0;
			foreach ( $tips as $tip ) {
				$order = empty($tip['order']) ? $order + 1 : $tip['order'];
				
				$tipEntity = new Entities_TicketTip();

				$tipEntity->oddsOutcomeId = $tip['sloupec_id'];
				$tipEntity->oddsOutcomeName = $tip['nazev'];
				$tipEntity->oddsOutcomeShortCut = $tip['nazev'];
				$tipEntity->betId = $tip['sazka_id'];
				$tipEntity->betName = $tip['betName'];
				$tipEntity->oddsTypeName = $tip['oddsTypeName'];
				$tipEntity->paidOut = !empty($tip['paidOut']);
				$tipEntity->paidOutByBookmakerId = $tip['paidOutByBookmakerId'];
				$tipEntity->canceled = !empty($tip['ticket_sazka_zrusena'])
					|| (It6_Models_Bet::STATUS_CANCELED == $tip['status'])
					|| (It6_Date::fromDbAsTimestamp($tip['validTo']) < It6_Date::fromDbAsTimestamp($ret->createdTime));
				$tipEntity->canceledByBookmakerId = $tip['ticket_sazka_zrusil_bookmaker_id'];
				$tipEntity->cancelationReason = $tip['ticket_sazka_duvod_zruseni'];
				if (static::$_paidOutResultsOnly)
					$tipEntity->result = ($tip['paidOut'] ? $tip['result'] : null);
				else
					$tipEntity->result = $tip['result'];
				if (isset($tip['group_id']))
					$tipEntity->group = $tip['group_id'];
				else
					$tipEntity->group = 1;
				$tipEntity->rate = $tip['rate'];
				$tipEntity->ako = $tip['ako'];
				$tipEntity->validTo = $tip['validTo'];
				//$tipEntity->alias = It6_Models_Bet::formatAlias($tip['alias'], $tip['alias2']);
				$tipEntity->alias = $tip['alias_new'];
				$tipEntity->betTextNote = $tip['betTextNote'];
				$tipEntity->typeName = $tip['typeName'];
				$tipEntity->sportName = $tip['sportName'];
				$tipEntity->regionName = $tip['regionName'];
				$tipEntity->eventName = $tip['eventName'];
				$tipEntity->order = $order;
				$tipEntity->score = $tip['score'];
				$dictionary[$tip['typeName']] = true;
				$dictionary[$tip['sportName']] = true;
				$dictionary[$tip['regionName']] = true;
				$dictionary[$tip['eventName']] = true;
				$dictionary[$tip['nazev']] = true;

				if ( !isset($groups[$tipEntity->group]) ) {
					$groups[$tipEntity->group] = new Entities_TicketTipGroup();
					$groups[$tipEntity->group]->group = $tipEntity->group;
					$groups[$tipEntity->group]->tips = array();
				}

				$groups[$tipEntity->group]->tips[] = $tipEntity;
			}
			if (isset($groups[0])) {
				$groupT = $groups[0];
				unset($groups[0]);
				$groups[0] = $groupT;
			}
			$ret->groups = $groups;

			if (It6_Models_Ticket::isTypeMaxicombinatorCompatible($ret->type)) {
				$combinations = $db->select()->from(Webservice_Ticket::$COMBINATIONS_TABLE)
					->where('ticket_id = ?', $ret->ticketId)
					->query()->fetchAll();
				$ret->ticketCount = 0;
				$combinationEntities = array();
				foreach ( $combinations as $combination ) {
					$combinationEntity = new Entities_TicketCombination();
					$combinationEntity->k = $combination['k'];
					$combinationEntity->amount = $combination['stake'];
					//IT6: combination data:
					//$data = Zend_Json::decode($combination['data']);
					//$combinationEntity->rows = $data['rows'];
					$combinationEntities[] = $combinationEntity;
				}
				$ret->combinations = new It6_ArrayWrapper($combinationEntities);
			}

			$dictionary = It6_Models_Translator::translate(array_keys($dictionary), 1, $db);
			foreach ($ret->groups as $iGroup => $group) {
				foreach ($group->tips as $iTip => $tip) {
					$ptr = &$ret->groups[$iGroup]->tips[$iTip];
					$ptr->typeName = $dictionary[$tip['typeName']];
					$ptr->sportName = $dictionary[$tip['sportName']];
					$ptr->regionName = $dictionary[$tip['regionName']];
					$ptr->eventName = $dictionary[$tip['eventName']];
					$ptr->oddsOutcomeName =
						$ptr->oddsOutcomeShortCut = $dictionary[$tip['oddsOutcomeName']];
				}
			}
			return $ret;
		}

		catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("toEntity.", 0, $e);
		}
	}

	/**
	 * Set host where the ticket must be collected.
	 * Null mean everywhere, 0 nowhere (in the office)
	 * @param integer $ticketId
	 * @param integer $hostId
	 */
	public static function setForcedCollectionHostId($ticketId, $hostId) {
		$db = static::getDb();
		try {
			$data = array('forced_collection_host_id' => $hostId);
			if ($db->update(
					static::$TABLE,
					$data,
					array( static::$IDENTITY . '= ?' => $ticketId)
				))
				return true;
			else
				return false;
		} catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("setForceCollectionHostId", 0, $e);
		}
	}


	/**
	 * @param string $group1
	 * @param string $group2
	 * @param struct $filter
	 * @param string $order default amount DESC
	 * @param bool $cashFlow
	 * @param integer $limit1 limit of group1 items, null means all, null is default
	 * @param integer $limit2 limit of group2 items, null means all, null is default
	 * @return array
	 */
	public static function getStatistics($group1, $group2, $filter, $order = 'amount DESC', $cashFlow = true, $limit1 = null, $limit2 = null) {
		try {
			$conv = array_flip(static::$STATS_GROUP_CONV);
			$db = static::getDb();

			$table = $cashFlow
				? static::$STATISTICS_CASHFLOW_TABLE
				: static::$STATISTICS_PAYOUT_TABLE;
			$select = $db->select()
				->from(array('t'  => $table), null)
				->join(array('hs' => 'vic_admin.host'), 'hs.id = t.host_id', null)
				->join(array('u'  => 'uzivatel'), 'u.user_id = t.user_id', null)
				->join(array('e'  => 'udalost'), 'e.udalost_id = t.udalost_id', null)
				->join(array('s'  => 'sport'), 's.sport_id = t.sport_id', null)
				->join(array('tp' => 'typ'), 'tp.typ_id = t.typ_id', null)
				->join(array('br' => 'vic_admin.branch'), 'br.id = hs.branch_id', null)
				->join(array('bl' => 'vic_admin.branch_location'), 'bl.id = br.branch_location_id', null)
				->columns(array(
					'amount'       => 'SUM(t.amount)',
					'won'          => 'SUM(t.won)',
					'balance'      => 'SUM(t.won) - SUM(t.amount)',
					'risk'         => 'SUM(t.win)',
					'count'        => 'SUM(t.count)',
					'winRatio'     => 'SUM(t.won)/SUM(t.amount) - 1',
					'winTipCount'  => 'SUM(win_tip_count)',
					'lostTipCount' => 'SUM(lost_tip_count)',
					'wonCount'     => 'SUM(won_count)',
					'lostCount'    => 'SUM(lost_count)'))
				->columns(array(
						$group1  => $conv[$group1],
						'group1' => static::$STATS_GROUP_NAME[$group1]))
				->group($conv[$group1])
				->order($conv[$group1])
				->order($order);

			if ( !empty($group2) ) {
				$select = $select->columns(array(
					$group2  => $conv[$group2],
					'group2'  => static::$STATS_GROUP_NAME[$group2]));
				$select = $select->group($conv[$group2]);
			}

			if ( !empty($filter) ) {

				if ( !empty($filter['timeFrom']) ) {
					$select = $select->where('hour >= ?',
						floor(strtotime($filter['timeFrom'])/3600));
				}
				if ( !empty($filter['timeTo']) ) {
					$select = $select->where('hour <= ?',
						floor(strtotime($filter['timeTo'])/3600));
				}

				if ( !empty($filter[$group1]) ) {
					$select = $select->where($conv[$group1] . ' IN (?)', $filter[$group1]);
				}
				if ( !empty($group2) && !empty($filter[$group2]) ) {
					$select = $select->where($conv[$group2] . ' IN (?)', $filter[$group2]);
				}
			}

			$query = $select->query();
			$ret = array();
			$ret2 = array();
			$oldId = null;
			$oldName = null;

			$riskTotal = 0;
			$amountTotal = 0;

			$retItem = $RET_ITEM_INIT = array(
				'amount'       => 0,
				'won'          => 0,
				'balance'      => 0,
				'risk'         => 0,
				'count'        => 0,
				'winRatio'     => 0,
				'winTipCount'  => 0,
				'lostTipCount' => 0);

			$i = 0;
			while ( $item = $query->fetch() ) {
				if ( empty($group2) ) {
					$amountTotal += $item['amount'];
					$riskTotal += $item['risk'];
					$ret[] = $item;
				}
				else {
					if ( null != $oldId && $oldId != $item[$group1] ) {
						$retItem['balance'] = $retItem['won']-$retItem['amount'];
						$retItem['winRatio'] = $retItem['won']/$retItem['amount'] - 1;
						$retItem[$group1] = $oldId;
						$retItem['group1'] = $oldName;
						$retItem['items'] = $ret2;
						$ret[] = $retItem;
						$retItem = $RET_ITEM_INIT;
						$ret2 = array();
						$i = 0;
					}
					$oldId = $item[$group1];
					$oldName = $item['group1'];
					$retItem['amount'] += $item['amount'];
					$retItem['won'] += $item['won'];
					$retItem['risk'] += $item['risk'];
					$retItem['count'] += $item['count'];
					$retItem['winTipCount'] += $item['winTipCount'];
					$retItem['lostTipCount'] += $item['lostTipCount'];
					$amountTotal += $item['amount'];
					$riskTotal += $item['risk'];
					if ( empty($limit2) || $limit2 > $i )
						$ret2[] = $item;
					++$i;
				}
				unset($item);
			}
			if ( !empty($group2) && !empty($oldId) ) {
				$retItem['balance'] = $retItem['won']-$retItem['amount'];
				$retItem['winRatio'] = $retItem['won']/$retItem['amount'] - 1;
				$retItem[$group1] = $oldId;
				$retItem['group1'] = $oldName;
				$retItem['items'] = $ret2;
				$ret[] = $retItem;
			}

			foreach ( $ret as &$item ) {
				$item['share'] = $item['risk'] / $riskTotal;
				$item['amountRel'] = $item['amount'] / $amountTotal;
				if ( !empty($item['items']) ) {
					foreach ( $item['items'] as &$item2 ) {
						$item2['share'] = $item2['risk'] / $riskTotal;
						$item2['amountRel'] = $item2['amount'] / $amountTotal;
					}
				}
				unset($item);
			}

			usort($ret, function ($a, $b) use($order) {
				$tmp = explode(' ',$order);
				return !empty($tmp[1]) && 'DESC' == strtoupper($tmp[1])
							? $a[$tmp[0]] < $b[$tmp[0]]
							: $a[$tmp[0]] > $b[$tmp[0]];
			});

			if ( !empty($limit1) )
				array_splice($ret, $limit1);

			return $ret;
		} catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("getStatistics.", 0, $e);
		}


	}

	/**
	 * forfeitNotCollected
	 */
	public static function forfeitNotCollected() {
		static::setColumnsWithCachedData(true);
		$dayLimit = intval(Webservice_Parameter::getGlobalParameter(
					static::PARAMETER_TICKET_FORFEIT_LIMIT));
		
		$tickets = static::getAllWhere(array(
			'paidOut=1',
			'paidOutTime < DATE_SUB(CURDATE(), INTERVAL ? Day)' => $dayLimit,
			'is_loss=0',
			'cash = 1',
			'collection_time IS NULL OR collection_time=\'0000-00-00 00:00:00\'',
			'forfeit IS NULL OR forfeit=\'0000-00-00 00:00:00\''));
		static::setColumnsWithCachedData();

		$db = static::getDb();

		foreach ( $tickets as $ticket ) {
			It6_DbTransaction::begin($db);
			try {

				$db->update(
					static::$TABLE,
					array(
						'forfeit' => It6_Date::dbNow()),
					array('ticket_id = ?' => $ticket->ticketId));

				$helper = It6_Models_TicketFactory::newTicketCached($ticket['cachedData'], false, $ticket, It6_Models_Ticket::DATA_SERVICE, 'user', $db);
				//$helper = new It6_Models_Ticket($ticket, It6_Models_Ticket::DATA_SERVICE, 'user', $db);

				$value = $ticket->realWinAmount;
				if ( floatval($ticket->mpWin) > 0 && empty($helper->canceled) ) {
					$value -= $ticket->mpWinAmount;
				}

				Webservice_Transaction::make(array(
					'ticketId' => $ticket->ticketId,
					'value' => $value,
					'userId' => $ticket->userId,
					'hostId' => $ticket->hostId,
					'currencyId' => Webservice_Currency::getSystemId(),
					'typeName' => Webservice_TransactionType::NAME_OTHER_TICKET_FORFEIT));

				if ( floatval($ticket->mpWin) > 0 && empty($helper->canceled) ) {
					Webservice_Transaction::make(array(
						'ticketId' => $ticket->ticketId,
						'value' => $ticket->mpWinAmount,
						'userId' => $ticket->userId,
						'hostId' => $ticket->hostId,
						'currencyId' => Webservice_Currency::getSystemId(),
						'typeName' => Webservice_TransactionType::NAME_OTHER_TICKET_FORFEIT_MP));
				}

				It6_Log::info(
					"Ticket forfeit successfull.",
					It6_Log::TAG_ADMIN_OPERATION,
					array(
						'ticketId' => $ticket->ticketId));

				It6_DbTransaction::commit($db);
			}
			catch (Exception $e) {
				It6_DbTransaction::rollback($db);
				It6_Log::err(
					"Forfeit of ticket failed.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('ticketId' => $ticket->ticketId),
					$e);
				//throw $e;
			}
		}
	}

	/**
	 * Gets duplicate tickets.
	 * @param integer $minDuplicity minmal duplicty
	 * @return struct key is count of tickets value is ticket
	 */
	public static function getDuplicateTickets($minDuplicity) {
		$db = static::getDb();
		$items = $db->select()
			->from(
				array('th' => static::$TICKETHASH_TICKET_TABLE),
				array(
					'count' => 'count(*)',
					'ticketId' => 'ticket_id',
					'userId' => 'th.user_id'))
			->join(
				array('u' => Webservice_User::$TABLE),
				'u.user_id = th.user_id',
				array('userName' => 'u.nick'))
			->group('tickethash','user_id')
			->having('count(*) >= ?', $minDuplicity)
			->order('count DESC')
			->query()
			->fetchAll();

		$ret = array();
		foreach ( $items as $item ) {
			$tmp = Webservice_Ticket::getbyId($item['ticketId']);
			$tmp['userId'] = $item['userId'];
			$tmp['userName'] = $item['userName'];
			$tmp['count'] = $item['count'];
			$ret[] = $tmp;
		}

		return $ret;
	}


	public static function getByHandleAndUserComplete($handles, $userId=null, $langId=null, $resultsForPaidOutOnly=false) {
		if (empty($handles))
			return null;
		if (It6_ArrayWrapper::isArray($handles)) {
			foreach ($handles as $handle) {
				if (!It6_Validate_NineDigitHandle::isValidString($handle))
					return null;
			}
		}
		else if (!It6_Validate_NineDigitHandle::isValidString($handles))
			return null;

		It6_NineDigitHandle::fixHandle($handles);
		$query = static::defaultQuery(static::getDb()->select())
			->where(self::$TABLE_PREFIX.'.handle IN (?)', $handles);

		if($userId != null)
			$query->where(self::$TABLE_PREFIX.'.user_id = ?', $userId);
			
		$res = $query->query()->fetchAll();
		static::setPaidOutResultsOnly($resultsForPaidOutOnly);
		static::setColumnsWithCachedData(true);
		$res = static::toEntities($res, null);
		static::setPaidOutResultsOnly();
		static::setColumnsWithCachedData();
		self::getComplete($res, true, $langId);

		return reset($res);
	}


	public static function getByIdAndUserComplete($ids, $userId=null, $langId=null, $resultsForPaidOutOnly=false) {
		$query = static::defaultQuery(static::getDb()->select())
			->columns(array('username' => 'u.nick'))
			->where('t.ticket_id IN (?)', $ids);

		if($userId != null)
			$query->where('user_id = ?', $userId);

		$res = $query->query()->fetchAll();
		static::setPaidOutResultsOnly($resultsForPaidOutOnly);
		static::setColumnsWithCachedData(true);
		$res = static::toEntities($res, null);
		static::setPaidOutResultsOnly();
		static::setColumnsWithCachedData();

		self::getComplete($res, true, $langId);
		return $res;
	}



	public static function getMonthTicketComplete($rank=null, $langId=null) {
		$query = static::defaultQuery(static::getDb()->select())
			->join(
				array(Webservice_User::$TABLE_PREFIX => Webservice_User::$TABLE),
				Webservice_User::$TABLE_PREFIX.'.user_id = '.self::$TABLE_PREFIX.'.user_id',
				array('username' => 'nick')
			)
			->where('MONTH(vyplacen_date) = MONTH(?)',It6_Date::dbNow())
			->where('zruseno = 0')
			->where('vyplacen = 1')
			->where('is_loss = 0')
			->where(Webservice_User::$TABLE_PREFIX.'.anonymous = 0')
			->where(Webservice_User::$TABLE_PREFIX.'.e_testovaci = ?','ne')
			->where('type <> ?', 'maxikombi')
			->order('rate_real DESC');

		if($rank != null)
			$query->limit(1, $rank-1);
		else
			$query->limit(Webservice_Parameter::getGlobalParameter('web.monthTicketCount'));


		$res = $query->query()->fetchAll();
		static::setColumnsWithCachedData(true);
		$res = static::toEntities($res, null, false);
		static::setColumnsWithCachedData();
		self::getComplete($res, true, $langId);
		return $res;
	}



	private static function getComplete(&$tickets, $readBetColumnsName=true, $langId = null) {
		$db = Zend_Registry::get('db');
		$dbAdmin = Zend_Registry::get('admindb');
		$dictionary = array();
		foreach ($tickets as &$ticket) {
			$helper = It6_Models_TicketFactory::newTicketCached($ticket['cachedData'], true, $ticket, It6_Models_Ticket::DATA_SERVICE, 'user', $db);
			unset($ticket['cachedData']);
			//$helper = new It6_Models_Ticket($ticket, It6_Models_Ticket::DATA_SERVICE, 'user', $db);
			//$helper->computeAggregates($db);
			if($readBetColumnsName) {
				$helper->readBetColumnNames($db);
				$helper->readBetSportEvents($db);
			}

			if($helper->rateAdvance)
				$ticket['rateAdvance'] = $helper->rateAdvance;
			if($helper->rateWithoutAdvance)
				$ticket['rateWithoutAdvance'] = $helper->rateWithoutAdvance;

			$ticket['currencyName']	= $helper->getCurrencyName($db);
			$ticket['combinations']	= $helper->combinations;
			$ticket['totalOdds']	= $helper->rate;
			$ticket['won']			= ($ticket['canceled'] ? $helper->stake : $helper->won);
			$ticket['ticketCount'] = $helper->ticketCount;
			$bookmakers = array();
			if (!empty($ticket['couponAdminId']))
				$bookmakers[$ticket['couponAdminId']] = true;
			if (!empty($ticket['confirmBookmakerId']))
				$bookmakers[$ticket['confirmBookmakerId']] = true;
			//array_sort($ticket['groups']);
			foreach ($ticket['groups'] as $groupKey => $group) {
				foreach ($group['tips'] as $tipKey => $tip) {
					$bet = $helper->getBet($tip['betId'], $tip['oddsOutcomeId']);
					$tipRef = &$ticket['groups'][$groupKey]->tips[$tipKey];
					$tipRef->winResult = $bet['result'];
					if(isset($bet['resultNames']))
						$tipRef->resultNames = $bet['resultNames'];
					$tipRef->groupName = $helper->getGroupName($group['group']);

					if(isset($bet['sportName']))
						$tipRef->sportName = $bet['sportName'];
					if(isset($bet['regionName']))
						$tipRef->regionName = $bet['regionName'];
					if(isset($bet['eventName']))
						$tipRef->eventName = $bet['eventName'];
					if (!empty($langId)) {
						if (!empty($bet['sportName']) &&  !in_array($bet['sportName'], $dictionary))
							$dictionary[] = $bet['sportName'];
						if (!empty($bet['regionName']) && !in_array($bet['regionName'], $dictionary))
							$dictionary[] = $bet['regionName'];
						if (!empty($bet['eventName']) && !in_array($bet['eventName'], $dictionary))
							$dictionary[] = $bet['eventName'];
					}
					if (!empty($tip['paidOutByBookmakerId']))
						$bookmakers[$tip['paidOutByBookmakerId']] = true;
					if (!empty($tip['canceledByBookmakerId']))
						$bookmakers[$tip['canceledByBookmakerId']] = true;
				}
			}

			if (!empty($bookmakers)) {
				$objs = It6_Models_Admin::getData(array_keys($bookmakers), $dbAdmin);
				$bookmakers = array();
				foreach ($objs as $obj)
					$bookmakers[$obj['id']] = $obj;
			}
			$fnAddBookmaker = function($id, $field, &$container) use ($bookmakers) {
				if (empty($bookmakers[$id]))
					$container->$field = Webservice_Admin::BOOKMAKER_ROBOT_NAME;
				else {
					$bookmaker = &$bookmakers[$id];
					$container->$field = "{$bookmaker['firstName']} {$bookmaker['surname']}";
				}
			};
			$fnAddBookmaker($ticket['couponAdminId'], 'couponAdmin', $ticket);
			$fnAddBookmaker($ticket['confirmBookmakerId'], 'confirmBookmaker', $ticket);
			foreach ($ticket['groups'] as $groupKey => $group) {
				foreach ($group['tips'] as $tipKey => $_tip) {
					$tip = &$ticket['groups'][$groupKey]->tips[$tipKey];
					$fnAddBookmaker($tip['paidOutByBookmakerId'], 'paidOutByBookmaker', $tip);
					$fnAddBookmaker($tip['canceledByBookmakerId'], 'canceledByBookmaker', $tip);
				}
			}
			unset($tip);
			if ('ticket_state_1' == $ticket['state']) {
				$willWin = $helper->willWin();
				if (true === $willWin) {
					$ticket['stateNo']	= '1w';
					$ticket['state']	= 'ticket_state_1w';

				}
				else if (false === $willWin) {
					$ticket['stateNo']	= '1l';
					$ticket['state']	= 'ticket_state_1l';
				}
			}
		}
		if (!empty($langId)) {
			if($langId == 'GET_FROM_USER') {
				$user	= Webservice_User::getById($ticket['userId']);
				$langId	= $user['languageId'];
			}
			if(is_numeric($langId)) {
				$dictionary = It6_Models_Translator::translate($dictionary, $langId, $db);
				foreach ($tickets as &$ticket) {
					foreach ($ticket['groups'] as $groupKey => $group) {
						foreach ($group['tips'] as $tipKey => $tip) {
							$bet = &$ticket['groups'][$groupKey]->tips[$tipKey];
							if (!empty($bet->sportName))
								$bet->sportName = $dictionary[$tip['sportName']];
							if (!empty($bet->regionName))
								$bet->regionName = $dictionary[$tip['regionName']];
							if (!empty($bet->eventName))
								$bet->eventName = $dictionary[$tip['eventName']];
						}
					}
				}
			}
		}
	}

	/**
	 * Recalculates total win for tickets containing bet(s)
	 * @param array $tips array(betId,columnId) or array( array(betId,columnId), ... )
	 * @return array array('recalculated' => array(IDs), 'paidOut' => array(IDs), 'failed' => array(IDs)) Three arrays of ticket IDs
	 *               @see return value of recalculateById()
	 */
	public static function recalculateByTip($tips) {
		if (empty($tips))
			return array('recalculated' => array(), 'paidOut' => array(), 'failed' => array());

		if (!is_array($tips[0])) // just one tip (int, int)
			$tips = array($tips);
		$db = static::getDb();
		$sqlTuples = array();
		foreach ($tips as $tip)
			$sqlTuples[] = '(' . intval($tip[0]) . ',' . intval($tip[1]) . ')';
		$sqlTuples = implode(',', $sqlTuples);
		$res = $db->select()
			->from(array('t' => static::$TABLE), array('id' => 'ticket_id'))
			->join(array('tr' => static::$TIPS_TABLE), 't.ticket_id=tr.ticket_id', array())
			->where("(tr.sazka_id,tr.sloupec_id) IN ($sqlTuples)")
			->group('t.ticket_id')
			->query();
		$tickets = array();
		while ($row = $res->fetch())
			$tickets[] = $row['id'];
		return static::recalculateById($tickets);
	}

	/**
	 * Recalculates total win for specified ticket(s)
	 * @param integer|array $ticketId One ticket ID or array of ticket IDs
	 * @return array array('recalculated' => array(IDs), 'paidOut' => array(IDs), 'failed' => array(IDs)) Three arrays of ticket IDs
	 *               Paid out tickets tickets will be contained also in failed or in recalculated tickets.
	 *               Note that in current implementation paid out ticket cannot be reverted so will end in failed tickets.
	 */
	public static function recalculateById($ticketId) {
		if (empty($ticketId))
			return array('recalculated' => array(), 'paidOut' => array(), 'failed' => array());
		if (!is_array($ticketId)) // just one ticket
			$ticketId = array($ticketId);
		$db = static::getDb();
		$helpers = It6_Models_TicketFactory::newTicket($ticketId, It6_Models_Ticket::DATA_ADMIN_TICKET, false, true);
		$updated = array();
		$failed = array();
		$paidOut = array();
		foreach ($helpers as $helper) {
			$id = $helper->id;
			$update = true;
			if (!empty($helper->paidOut)) {
				try {
					//TODO: ? cancel payout ? there isn't such a functionality in WS, only in section in admin...
					//static::cancelPayout($id, 'Bet rate updated');
					$paidOut[] = $id;
					$failed[] = $id; // remove after pay out cancelation will be available
					$update = false; // remove after pay out cancelation will be available
				}
				catch (Exception $e) {
					$failed[] = $id;
					$update = false;
				}
			}
			if ($update) {
				try {
					$db->update(
						static::$TABLE,
						array(
							'rate' => $helper->rate,
							'win' => $helper->win,
						),
						array('ticket_id=?' => $id)
					);
					$updated[] = $id;
				}
				catch (Exception $e) {
					It6_Log::err(
						'Ticket was not updated when trying to recalcute',
						null,
						array('ticketId' => $id)
					);
					$failed[] = $id;
				}
			}
		}
		return array(
			'recalculated' => $updated,
			'paidOut' => $paidOut,
			'failed' => $failed,
		);
	}
	
	/**
	 * Gets last created tickets.
	 * @param integer $from as timestamp
	 * @param integer $to as timestamp
	 * @return array array of the tickets
	 */
	public static function getLastTickets($from,$to,$limit,$stakeFrom = null, $stakeTo = null, $rateFrom = null, $rateTo = null, $winFrom = null, $winTo = null ) {
		$db = static::getDb();
		$extensions[] = array(
			'class' => 'Pagination',
			'id' => '_limit',
			'params' => array(It6_WsExtension_Pagination::PARAM_LIMIT => $limit));

		$where = array(
			'createdTime >= ?' => It6_Date::timestampToDb($from),
			'createdTime < ?' => It6_Date::timestampToDb($to)
		);

		if ( !empty($stakeFrom) )
			$where['amount >= ?'] = $stakeFrom;
		if ( !empty($stakeTo) )
			$where['amount <= ?'] = $stakeTo;
		if ( !empty($rateFrom) )
			$where['totalOdds >= ?'] = $rateFrom;
		if ( !empty($rateTo) )
			$where['totalOdds <= ?'] = $rateTo;
		if ( !empty($winFrom) )
			$where['winAmount >= ?'] = $winFrom;
		if ( !empty($winTo) )
			$where['winAmount <= ?'] = $winTo;
		
		$ret = static::getAllWhereOrder(
			$where,
			array('createdTime DESC'),
			$extensions
		);

		return $ret;
	}
}
