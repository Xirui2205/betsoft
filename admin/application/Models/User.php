<?php

class Models_User {

	public static function getUSupplyValue($userId, $name) {
		if(count($row = Zend_Registry::get('db')->select()
			->from('uzivatel_supply', array('s_data_value','dt_updated'))
			->where('user_id = ?', $userId)
			->where('s_data_name = ?', $name)
			->limit(1)
			->query()->fetchAll())) {

			return array(
				'value'	=>$row[0]['s_data_value'],
				'ts'	=>$row[0]['dt_updated']
			);
		}
		return false;
	}



	public static function isUserOnline($userId) {

		if($userId && is_numeric($userId)) {

			$select = Zend_Registry::get('zdb_sess')->select()
				->from('session', array('user_id'))
				->where('status=2 OR status=3')
				->where('user_id=?', intval($userId))
				->where('user_id=?', implode(",",$GLOBALS['EXCLUDEUSER']))
				->query()->fetchAll();

			if(count($select) > 0)
				return true;
		}

		return false;
	}



	public static function getFsb($userId){
		$select = Zend_Registry::get('db')->select()
			->from(
				'ticket_bonus_uzivatel',
				array('kod','vybral','bonus_castka','ticket_bonus_id','poznamka','rel','dt_in')
			)
			->where('user_id = ?',$userId)
			->where('ticket_bonus_id = 2 OR ticket_bonus_id = 1')
			->order('id ASC')
			->query()->fetchAll();

		$fsb = array();
		foreach($select as $row){
			$fsb[] = array(
				'dtIn'			=>$row['dt_in'],
				'id'			=>$row['ticket_bonus_id'],
				'rel'			=>$row['rel'],
				'code'			=>$row['kod'],
				'withdrawed'	=>$row['vybral'],
				'bonusAmmount'	=>$row['bonus_castka'],
				'note'			=>$row['poznamka']
			);
		}

		return $fsb;
	}



	public static function getSaldo($userId) {
		$deposit	= 0;
		$withdrawal	= 0;

		$select = Zend_Registry::get('db')->select()
			->from('financial_transaction')
			->where('user_id = ?', $userId)
			->query()->fetchAll();

		foreach($select as $row) {
			if($row['type_id'] == 1)
				$deposit += $row['value'];

			else if($row['type_id'] == 2)
				$withdrawal += $row['value'];
		}


		return array(
			'deposit'		=> $deposit,
			'withdrawal'	=> $withdrawal,
			'balance'		=> $deposit - $withdrawal
		);
	}



	public static function _getTemplateMessages(){

		$select = Zend_Registry::get('db')->select()
			->from(
				'poznamky_template',
				array('id', 'predmet', 'telo')
			)
			->query()->fetchAll();

		return $select;
	}



	/**
	 * Change password of the given user.
	 * @param integer $userId identifier of the user (not login)
	 * @param string $newPassword new password
	 * @return boolean true on success otherwise false
	 */
	public static function changePassword($userId, $newPassword) {

		try {
			$db = static::getDb();

			$db->update(
				static::$TABLE,
				array('passwd' => It6_Models_Admin::cryptPasswd($newPassword)),
				array( static::$IDENTITY . '= ?' => $userId)
			);

			return true;
		}

		catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("changePassword.", 0, $e);
		}
	}
}
