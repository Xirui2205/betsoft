<?php
/**
 * class Models_Helpers_UserImData
 * User IM data wrapper
 */

class Models_Helpers_UserImData {

	const DBTABLE = 'uzivatel_im_data';

	public $userId;
	public $balance;
	public $chips;
	public $debt;
	public $balanceBonus;

	/**
	 * Constructor - default values initialization
	 */
	public function __construct($userId = 0) {
		$this->userId = $userId;
		$this->balance = 0.0;
		$this->chips = 0.0;
		$this->debt = 0.0;
		$this->balanceBonus = 0.0;
	}

	/**
	 * Convert member variables to array( db_column_1 => value_1, ... )
	 */
	public function toArray($includeUserId) {
		$data = array(
			'zustatek' => $this->balance,
			'zetony' => $this->chips,
			'dluh' => $this->debt,
			'zustatek_bonus' => $this->balanceBonus,
		);
		if ($includeUserId)
			$data['user_id'] = $this->userId;
		return $data;
	}

	/**
	 * Inserts new user IM data into DB.
	 * @returns true on success
	 */
	public function writeDb() {
		$db = Zend_Registry::get('db');
		$data = $this->toArray(true);
		$rows = $db->insert(self::DBTABLE, $data);
		return (1 == $rows);
	}

	/**
	 * Update user IM data in DB
	 * @returns true on success
	 */
	public function updateDb() {
		$db = Zend_Registry::get('db');
		$data = $this->toArray(false);
		$rows = $db->update(self::DBTABLE, $data, 'user_id='.$this->id);
		return (1 == $rows);
	}

	/**
	 * Reads user IM data from DB.
	 * @param $userId user ID
	 * @returns true if user was found and read
	 */
	public function readDb($userId) {
		$db = Zend_Registry::get('db');
		$rows = $db->select()
		->from( self::DBTABLE, array( 'user_id', 'zustatek', 'zetony', 'dluh', 'zustatek_bonus') )
		->where('user_id = ?', $userId)
		->query()->fetchAll();
		if (empty($rows))
			return false;
		else
			return $this->readArray($rows[0]);
	}

	/**
	 * Reads user IM data from array.
	 * @param $data associative array, keys are database columns
	 * @returns true if user was found and read
	 */
	public function readArray(array $data) {
		$this->userId = $data['user_id'];
		$this->balance = $data['zustatek'];
		$this->chips = $data['zetony'];
		$this->debt = $data['dluh'];
		$this->balanceBonus = $data['zustatek_bonus'];
		return $this;
	}

}
