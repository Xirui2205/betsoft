<?php

/**
 * ClientCard related static methods.
 *
 */
class Webservice_ClientCard extends Webservice_AbstractWebService  {
	
	public static $TABLE = "client_card_number_feed";
	public static $TABLE_PREFIX = "ccnf";
	public static $IDENTITY = "id";

	protected static $ENTITY_NAME = "Entities_ClientCard";
	protected static $CONV = array(
		'ccnf.id' => 'clientCardId',
		'generated' => 'generated',
		'admin_id' => 'adminId',
		'ccnf.branch_id' => 'branchId',
		'br.name' => 'branchName',
		'set' => 'set',
		'used' => 'used',
		'blocked' => 'blocked',
		'usr.nick' => 'userNick',
		'usr.jmeno' => 'userFirstName',
		'usr.prijmeni' => 'userLastName',
		'usr.user_id' => 'userId'
	);

	protected static function defaultJoins($query) {
		$query = parent::defaultJoins($query);
		$query
			->joinLeft(
				array(Webservice_Branch::$TABLE_PREFIX => 'vic_admin.'.Webservice_Branch::$TABLE),
				Webservice_Branch::$TABLE_PREFIX.'.id = '.self::$TABLE_PREFIX.'.branch_id',
				null
			)
			->joinLeft(
				array(Webservice_User::$TABLE_PREFIX => Webservice_User::$TABLE),
				Webservice_User::$TABLE_PREFIX.'.client_card_number = '.self::$TABLE_PREFIX.'.id',
				null
			);
		return $query;
	}
	
	/**
	 * Returns all clientCards in the system.
	 * @return array array of the clientCard structures
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Returns all clientCards in the system.
	 * @return array array of the clientCard structures
	 */
	public static function getAllWhereCond($where, $extensions = null) {
		return parent::getAllWhere($where, $extensions);
	}

	/**
	 * Find clientCard by given identifier.
	 * @param integer $clientCardId identifier of the clientCard
	 * @return struct type structure	 
	 */
	public static function getById($clientCardId, $extensions = null) {
		return parent::getById($clientCardId, $extensions);
	}

	/**
	 * Insert new clientCard.
	 * @param struct $values structure of the clientCard
	 * @param boolean $no_gen_id
	 * @return integer type identifier of the created clientCard
	 */
	public static function insert($values) {
		$db = static::getDb();

		try {
			$entity = new It6_ArrayWrapper($values);
			$data = static::removeTableNames(static::fromEntity($entity));

			$db = self::getDb();
			$usedCards = $db->select()
				->from(array(self::$TABLE_PREFIX => self::$TABLE))
				->where(array_search('clientCardId', self::$CONV).' = ?', $data[static::$IDENTITY])
				->query()->fetchAll();

			if (empty($usedCards)) {
				It6_DbTransaction::begin($db);
				$db->insert(static::$TABLE, $data);
				$ret = $data[static::$IDENTITY];
				It6_DbTransaction::commit($db);
				return $ret;
			} else {
				return false;
			}
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not insert entity (Entity: '".get_called_class()."')", 0, $e);
		}
	}

	/**
	 * Update clientCard.  
	 * @param struct $values structure of the clientCard 
	 * @return true on success
	 */
	public static function update($values) {
		return parent::update($values);
	}

	/**
	 * Blocks the specified card.
	 * @param int $clientCardId The identificator of the client card.
	 * @return boolean
	 * @throws It6_XmlRpc_Exception
	 */
	public static function block($clientCardId) {
		try {
			$canBeBlocked = self::getById($clientCardId);
			if (empty($canBeBlocked['blocked'])) {
				$data = array(array_search('blocked', self::$CONV) => It6_Date::dbNow());
				$where = array(static::$IDENTITY.'=?' => $clientCardId);
				self::getDb()->update(array(self::$TABLE_PREFIX => self::$TABLE), $data, $where);
				return true;
			} else {
				return false;
			}
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception($e);
		}
	}

	/**
	 * Unblocks the specified card.
	 * @param int $clientCardId The identificator of the client card.
	 * @return boolean
	 * @throws It6_XmlRpc_Exception
	 */
	public static function unblock($clientCardId) {
		try {
			$canBeUnblocked = self::getById($clientCardId);
			if (!empty($canBeUnblocked['blocked'])) {
				$data = array(array_search('blocked', self::$CONV) => null);
				$where = array(static::$IDENTITY.'=?' => $clientCardId);
				self::getDb()->update(array(self::$TABLE_PREFIX => self::$TABLE), $data, $where);
				return true;
			} else {
				return false;
			}
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception($e);
		}
	}

	/**
	 * 
	 * @param int $branchId identifier for the branch to which the client cards should be binded.
	 * @param array $clientCardsId Array with the ids of the client cards.
	 * @return boolean
	 * @throws It6_XmlRpc_Exception
	 */
	public static function cardsToBranch($branchId, $clientCardIds) {
		try {
			$db = self::getDb();
			$usedCards = $db->select()
				->from(array(self::$TABLE_PREFIX => self::$TABLE))
				->where(array_search('clientCardId', self::$CONV).' IN (?)', $clientCardIds)
				->where(array_search('used', self::$CONV).' IS NOT NULL');

			$usedCards = $usedCards->query()->fetchAll();
			if (empty($usedCards)) {
				// Cant search in self::$CONV beacuse branch_id in conf is ccnf.branch_id.
				$data = array('branch_id' => $branchId);
				$where = array('id IN (?)' => $clientCardIds);
				$db->update(array(self::$TABLE_PREFIX => self::$TABLE), $data, $where);
				return true;
			} else {
				return false;
			}
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception($e);
		}
	}

	/**
	 *
	 * @param array $clientCardsId Array with the ids of the client cards.
	 * @return boolean
	 * @throws It6_XmlRpc_Exception
	 */
	public static function blockMoreCards($clientCardIds) {
		try {
			$db = self::getDb();
			$data = array(array_search('blocked', self::$CONV) => It6_Date::dbNow());
			$where = array('id IN (?)' => $clientCardIds);
			$db->update(array(self::$TABLE_PREFIX => self::$TABLE), $data, $where);
			return true;
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception($e);
		}
	}
	

	/**
	 *
	 * @param array $clientCardsId Array with the ids of the client cards.
	 * @return boolean
	 * @throws It6_XmlRpc_Exception
	 */
	public static function unblockMoreCards($clientCardIds) {
		try {
			$db = self::getDb();
			$data = array(array_search('blocked', self::$CONV) => null);
			$where = array('id IN (?)' => $clientCardIds);
			$db->update(array(self::$TABLE_PREFIX => self::$TABLE), $data, $where);
			return true;
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception($e);
		}
	}
}