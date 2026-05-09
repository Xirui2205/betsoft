<?php
/**
 * Affiliate partners webervice.
 * @author Jiri Ulbrich
 */
class Webservice_AffiliatePartner extends Webservice_AbstractWebService  {

	public static $TABLE = "affiliate_partner";
	public static $TABLE_PREFIX = "ap";
	public static $ENTITY_NAME = "Entities_AffiliatePartner";
	public static $IDENTITY = "id";
	public static $PARTNER_BANNER_TABLE = 'affiliate_partner_banner';
	public static $PARTNER_BANNER_PREFIX = 'pb';
	public static $PARTNER_BANNER_USER_TABLE = 'affiliate_partner_banner_user';
	public static $PARTNER_BANNER_USER_PREFIX = 'pbu';
	public static $PARTNER_TICKET_USER_TABLE = 'affiliate_partner_user_ticket';
	public static $PARTNER_TICKET_USER_PREFIX = 'ptp';
	public static $ADMIN_DB = "vic_admin";

	protected static $CONV = array(
		'id' => 'affiliatePartnerId',
		'name' => 'name',
		'url' => 'url',
		'created' => 'created',
		'admin_id' => 'adminId',
		'CONCAT(ad.first_name, \' \', ad.surname)' => 'adminName'
	);

	protected static function defaultJoins($query) {
		$query = parent::defaultJoins($query);
		$query->joinLeft(
			array(Webservice_Admin::$TABLE_PREFIX => self::$ADMIN_DB.'.'.Webservice_Admin::$TABLE),
			Webservice_Admin::$TABLE_PREFIX.'.admin_id = '.self::$TABLE_PREFIX.'.admin_id',
			null
		);
		return $query;
	}

	/**
	 * Returns all partners in the system
	 * @return array array of the region structures
	 * @see Entities_AffiliatePartner
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Find partner by given identifier.
	 * @param integer $partnerId identifier of the partner
	 * @return struct partner structure
	 * @see Entities_AffiliatePartner
	 */
	public static function getById($partnerId, $extensions = null) {
		return parent::getById($partnerId, $extensions);
	}

	/**
	 * Insert new region. Value of the region identifier is ignored and new
	 * is generated.
	 * @param struct $partner structure of the partner
	 * @return integer partner identifier of the created partner
	 * @see Entities_AffiliatePartner
	 */
	public static function insert($partner) {
		return parent::insert($partner);
	}

	/**
	 * Update region.
	 * @param struct $partner structure of the region
	 * @return true on success
	 * @see Entities_AffiliatePartner
	 */
	public static function update($partner) {
		return parent::update($partner);
	}

	/**
	 * Delete partner.
	 * @param struct $partnerId idenetifier of the partner.
	 * @return true on success
	 * @see Entities_AffiliatePartner
	 */
	public static function delete($partnerId) {
		return parent::delete($partnerId);
	}

	/**
	 * Get assigned banners to partner.
	 * @param struct $partnerId idenetifier of the partner.
	 * @return struct banners structure
	 * @see Entities_AffiliatePartner
	 */
	public static function getAssignedBanners($partnerId) {
		try {
			$db = self::getDb();
			$select = $db->select()
				->from(array(Webservice_AffiliateBanner::$TABLE_PREFIX => Webservice_AffiliateBanner::$TABLE))
				->joinLeft(
					array(self::$PARTNER_BANNER_PREFIX => self::$PARTNER_BANNER_TABLE), 
					self::$PARTNER_BANNER_PREFIX.".banner_id = ".Webservice_AffiliateBanner::$TABLE_PREFIX.".".self::$IDENTITY,
					null
				)
				->where(self::$PARTNER_BANNER_PREFIX.'.partner_id = ?', $partnerId)
				->query()->fetchAll();

			return $select;

		} catch ( Exception $e) {
			throw new It6_XmlRpc_Exception("AffiliatePartner::getAssignedBanners.", 0, $e);
		}
	}

	/**
	 * Get registered users of partner.
	 * @param struct $partnerId idenetifier of the partner.
	 * @return struct users structure
	 * @see Entities_AffiliatePartner
	 */
	public static function getRegisteredUsers($partnerId = null) {

		try {
			$db = static::getMainDb();
			$subSelect = $db->select()
				->from(
					array('t' => Webservice_Ticket::$TABLE),
					array(
						't.user_id',
						'pocet' => 'COUNT(*)',
						'castka' => 'SUM(castka)',
						'vyhra' => 'SUM(win_real)',
						'provize' => '(SUM(castka) - SUM(win_real))/10',
					)
				)
				->joinRight(
					array(self::$PARTNER_TICKET_USER_PREFIX => self::$PARTNER_TICKET_USER_TABLE),
					self::$PARTNER_TICKET_USER_PREFIX.'.ticket_id = '.Webservice_Ticket::$TABLE_PREFIX.'.ticket_id',
					null
				)
				->where('vyplacen = 1')
				->group('t.user_id');

			$select = $db->select()
				->from(
					array(Webservice_User::$TABLE_PREFIX => Webservice_User::$TABLE),
					array('*', 'pocet' => 'a.pocet', 'castka' => 'a.castka', 'vyhra' => 'a.vyhra', 'provize' => 'a.provize')
				)
				->joinRight(
					array(self::$PARTNER_BANNER_USER_PREFIX => self::$PARTNER_BANNER_USER_TABLE),
					self::$PARTNER_BANNER_USER_PREFIX.".user_id = ".Webservice_User::$TABLE_PREFIX.".".Webservice_User::$IDENTITY,
					null
				)
				->joinLeft(
					array('a' => new Zend_Db_Expr('('.$subSelect->assemble().')')),
					'a.user_id = '.self::$PARTNER_BANNER_USER_PREFIX.'.user_id',
					null
				);

			if (!empty($partnerId)) $select = $select->where(self::$PARTNER_BANNER_USER_PREFIX.'.partner_id = ?', $partnerId);

			$select = $select->query()->fetchAll();
			return $select;

		} catch ( Exception $e) {
			throw new It6_XmlRpc_Exception("AffiliatePartner::getRegisteredUsers.", 0, $e);
		}
	}

	/**
	 * Assign banner to partner.
	 * @param struct $values.
	 * @return boolean
	 * @see Entities_AffiliatePartner
	 */
	public static function assignBanner($values) {
		try {
			$db = self::getDb();
			$select = $db->select()
				->from(
					array(self::$PARTNER_BANNER_PREFIX => self::$PARTNER_BANNER_TABLE),
					array("count" => "COUNT(*)")
				)
				->where('partner_id = ?', $values["affiliatePartnerId"])
				->where('banner_id = ?', $values["affiliateBannerId"])
				->limit(1)
				->query()->fetch();

			if ($select["count"] == 0) {
				return $db->insert(
					self::$PARTNER_BANNER_TABLE,
					array(
						'partner_id' => $values['affiliatePartnerId'],
						'banner_id' => $values['affiliateBannerId']
					)
				);
			} else {
				return false;
			}
		} catch ( Exception $e) {
			throw new It6_XmlRpc_Exception("AffiliatePartner::assignedBanner.", 0, $e);
		}
	}

	/**
	 * Insert user to partner after registration.
	 * @param int user, partner, banner.
	 * @return boolean
	 * @see Entities_AffiliatePartner
	 */
	public static function insertUser($user, $partner, $banner) {
		try {
			$db = self::getDb();
			return $db->insert(
				self::$PARTNER_BANNER_USER_TABLE,
				array(
					'partner_id' => $partner,
					'banner_id' => $banner,
					'user_id' => $user
				)
			);
		} catch ( Exception $e) {
			throw new It6_XmlRpc_Exception("AffiliatePartner::insertUser.", 0, $e);
		}
	}

	/**
	 * Get actual provision for partner.
	 * @param struct $partnerId idenetifier of the partner, date from, date to
	 * @return struct users structure
	 * @see Entities_AffiliatePartner
	 */
	public static function getActualProvision($partnerId) {

		try {
			$fnFixDate = function($d) {
				if (1 == preg_match('/^(\\d{4})-(\\d{1,2})-(\\d{1,2})$/', $d, $matches)) {
					return It6_Date::timestampToDate(mktime(0,0,0,$matches[2],$matches[3],$matches[1]));
				} else {
					return $d;
				}
			};
			$dateFrom = $fnFixDate(date('Y-m-d', strtotime('first day of this month', time())));
			$dateTo = $fnFixDate(date('Y-m-d', strtotime('last day of this month', time())));

			list($dateFrom, $dateTo) = It6_Date::dateToDbInterval($dateFrom, $dateTo);

			$db = static::getMainDb();
			$select = $db->select()
				->from(
					array(Webservice_Ticket::$TABLE_PREFIX => Webservice_Ticket::$TABLE),
					array(
						'pocet' => 'COUNT(*)', 
						'castka' => 'SUM(castka)', 
						'vyhra' => 'SUM(win_real)',
						'provize' => 'ROUND((SUM(castka) - SUM(win_real))/10, 2)',
					)
				)
				->joinRight(
					array(self::$PARTNER_BANNER_USER_PREFIX => self::$PARTNER_BANNER_USER_TABLE),
					self::$PARTNER_BANNER_USER_PREFIX.".user_id = ".Webservice_Ticket::$TABLE_PREFIX.".user_id",
					null
				)
				->joinRight(
					array(self::$PARTNER_TICKET_USER_PREFIX => self::$PARTNER_TICKET_USER_TABLE),
					self::$PARTNER_TICKET_USER_PREFIX.'.ticket_id = '.Webservice_Ticket::$TABLE_PREFIX.'.ticket_id',
					null
				)
				->where(self::$PARTNER_TICKET_USER_PREFIX.'.provision_paid_out = 0')
				->where(self::$PARTNER_BANNER_USER_PREFIX.'.partner_id = ?', $partnerId)
				->where(Webservice_Ticket::$TABLE_PREFIX.'.vyplacen_date >= ?', $dateFrom)
				->where(Webservice_Ticket::$TABLE_PREFIX.'.vyplacen_date <= ?', $dateTo)
				->where(Webservice_Ticket::$TABLE_PREFIX.'.vyplacen = 1')
				->query()->fetchObject();

			return $select;

		} catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("AffiliatePartner::getActualProvision.", 0, $e);
		}
	}

	/**
	 * Check user if is from partner
	 * @param int userId
	 * @return struct with partnerId
	 * @see Entities_AffiliatePartner
	 */
	public static function checkUserPartner($userId) {

		try {
			$db = static::getMainDb();
			$select = $db->select()
				->from(
					array(self::$PARTNER_BANNER_USER_PREFIX => self::$PARTNER_BANNER_USER_TABLE),
					array('partnerId' => 'partner_id')
				)
				->where('user_id = ?', $userId)
				->limit(1)
				->query()->fetchObject();

			return $select;

		} catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("AffiliatePartner::checkUserPartner.", 0, $e);
		}
	}
	
	/**
	 * Insert ticket to user of affiliate partner
	 * @param int partnerId, ticketId, userId
	 * @return 
	 * @see Entities_AffiliatePartner
	 */
	public static function insertTicket($partnerId, $ticketId, $userId) {

		try {
			$db = static::getMainDb();
			return $db->insert(
				self::$PARTNER_TICKET_USER_TABLE,
				array(
					'partner_id' => $partnerId,
					'ticket_id' => $ticketId,
					'user_id' => $userId,
				)
			);

		} catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("AffiliatePartner::insertTicket.", 0, $e);
		}
	}
}