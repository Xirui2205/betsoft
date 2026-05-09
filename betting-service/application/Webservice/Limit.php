<?php

class Webservice_Limit extends Webservice_AbstractWebService {
	
	public static $TABLE = "limity";
	public static $TABLE_PREFIX = "lim";
	
	protected static $IDENTITY = 'sport_id';
	
	protected static $CONV = array(
			'lim.sport_id' 	=> 'limSportId',
			'lim.limit_den'	=> 'limitDen',
	);
	
	protected static function defaultJoins($query) {
		return parent::defaultJoins($query)
			->joinRight(
				array('sp' => 'sport'),
				static::$TABLE_PREFIX . '.sport_id = sp.sport_id',
				null);
	}
	
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}
	
	public static function getAllWhere($where, $extensions = null, $joins = null) {
		return parent::getAllWhere($where, $extensions, $joins);
	}
	
	public static function getOneWhere($where, $extensions = null, $joins = null) {
		return parent::getOneWhere($where, $extensions, $joins);
	}
	
	/**
	 * Metoda vrací na základě zadaného ID uživatele jeho individuální limity 
	 * @param int $userId ID uživatele v systému BBAS
	 */
	public static function getLimitsByUserId($userId) {
		$ret = static::getDb()->select()
		->from(array('lu' => 'limity_user'))
		->join(
				array('sp' => 'sport'),
				'sp.sport_id = lu.sport_id',
				null)
				->where('lu.user_id = '.$userId)
				->query()->fetchAll();
		return $ret;
	}
	
	/**
	 * Metoda slouží k ukládání/updatování limitů v databázi<br>
	 * Pokud limit nalezne, updatne jej, pokud ne, provede insert
	 * @param array $data data k vložení/update 
	 */
	public static function update($data) {
		$limit = It6_ArrayWrapper::toNativeArray(self::getOneWhere(array('limSportId = ?' => $data['limSportId'])));
		if ($limit['limitDen'] != null) {
			return parent::update($data);
		} else {
			return parent::insert($data);
		}
		
	}
	
	/**
	 * Metoda vloží individuální limity uživatele
	 * @param int $user user_id
	 * @param array $data data k vložení do db
	 */
	public static function insertUserLimits($user, $data) {
		try {
			$data['user_id'] = $user;
			$ret = static::getDb()->insert('limity_user', $data);
			return $data;
		} catch (Exception $e) {
			throw new It6_XmlRpc_Exception("Can not insert user limit (user_id: ".$data['user_id'].", sport_id: ".$data['sport_id'].", limit: ".$data['limit_den_individual'].")", 0, $e);
		}
	}
	
	/**
	 * Metoda updatuje indivituální limity uživatele
	 * @param int $user user_id
	 * @param int $sport sport_id
	 * @param int $limit hodnota limitu
	 */
	public static function updateUserLimits($user, $sport, $limit) {
		try {
			$ret = static::getDb()->update('limity_user', array('limit_den_individual' => $limit), array('user_id = ?' => $user, 'sport_id = ?' => $sport));
			return $ret;
		} catch (Exception $e) {
			throw new It6_XmlRpc_Exception("Can not update user limit (user_id: $user, sport_id: $sport, limit: $limit)", 0, $e);
		}
	}
}