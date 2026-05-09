<?php
class Models_Admin {
	private static $columnName = "";
	private static $columnOrder = "";

	/**
	 * Returns admins provision.
	 * @param array $inData
	 * @param array $filter
	 * @return array of the admins provision.
	 */
	public static function getAdminProvision($inData, $filter) {
		$_filter = $_order = '';
		$dateFrom = $dateTo = It6_Date::dbNow();

		// Filter setting
		if ( !empty($inData["filter"]["fromDate"]) ) {
			$dateFrom = It6_Date::toDb($inData["filter"]["fromDate"]);
		}
		if ( !empty($inData["filter"]["toDate"]) ) {
			$dateTo = It6_Date::toDb($inData["filter"]["toDate"]);
		}

		$db = Zend_Registry::get('zdb_admin')->select();

		$admins = array();
		$admins = Zend_Registry::get('db')->select()
			->from(array(Webservice_User::$TABLE_PREFIX => Webservice_User::$TABLE), 
				array('user_id','allowed_by_admin_id','branch_id')
			)
			->join(
				array(Webservice_Ticket::$TABLE_PREFIX => Webservice_Ticket::$TABLE),
				Webservice_Ticket::$TABLE_PREFIX.'.user_id = '.Webservice_User::$TABLE_PREFIX.'.user_id',
				array('sum(castka) as provision'))
			->joinLeft(
				array(Webservice_Admin::$TABLE_PREFIX => Webservice_Admin::$TABLE),
				Webservice_Admin::$TABLE_PREFIX.'.user_id = '.Webservice_User::$TABLE_PREFIX.'.allowed_by_admin_id',
				array('jmeno', 'prijmeni', 'telefon', 'email'))
			->joinLeft(
				array(Webservice_Branch::$TABLE_PREFIX => 'vic_admin.'.Webservice_Branch::$TABLE),
				Webservice_Branch::$TABLE_PREFIX.'.id = '.Webservice_User::$TABLE_PREFIX.'.branch_id',
				array('handle', 'name'))
			->where('allowed_by_admin_id != ?', 0)
			->where('zalozen >= ?', $dateFrom)
			->where('zalozen <= ?', $dateTo)
			->order($_order)
			->group('user_id')
			->query()
			->fetchAll();

		$provisionForService = Webservice_Parameter::getGlobalParameter('amount.of.remuneration.for.service');
		$provisionLimit = Webservice_Parameter::getGlobalParameter('limit.rewards.for.service');

		$adminProvision = array();
		foreach ($admins as $key => $value) {
			if ( $value["provision"] >= $provisionLimit ) {
				if ( !isset($adminProvision[$value["allowed_by_admin_id"]]) ) {
					$adminProvision[$value["allowed_by_admin_id"]]["admin_id"] = $value["allowed_by_admin_id"];
					$adminProvision[$value["allowed_by_admin_id"]]["branch_id"] = $value["branch_id"];
					$adminProvision[$value["allowed_by_admin_id"]]["handle"] = $value["handle"];
					$adminProvision[$value["allowed_by_admin_id"]]["name"] = $value["name"];
					$adminProvision[$value["allowed_by_admin_id"]]["jmeno"] = $value["jmeno"];
					$adminProvision[$value["allowed_by_admin_id"]]["prijmeni"] = $value["prijmeni"];
					$adminProvision[$value["allowed_by_admin_id"]]["email"] = $value["email"];
					$adminProvision[$value["allowed_by_admin_id"]]["telefon"] = $value["telefon"];
				}

				$adminProvision[$value["allowed_by_admin_id"]]["playerCount"] = isset($adminProvision[$value["allowed_by_admin_id"]]["playerCount"]) ? $adminProvision[$value["allowed_by_admin_id"]]["playerCount"] + 1 : 1;
				$adminProvision[$value["allowed_by_admin_id"]]["provision"] = isset($adminProvision[$value["allowed_by_admin_id"]]["provision"]) ? $adminProvision[$value["allowed_by_admin_id"]]["provision"] + $provisionForService : $provisionForService;
			}
		}

		// Order setting
		if ( isset($inData["order"]) ) {
			foreach ($inData["order"] as $key => $value) {
				if ( strpos($key, '_') ) {
					$arrayOrder = explode("_", $key);
					self::$columnName = $arrayOrder[0];
					self::$columnOrder = $arrayOrder[1];
					usort($adminProvision, 'self::cust_sort');
				}
			}
		}

		return $adminProvision;
	}

	/**
	 * Returns ordered array
	 * The comparison function must return an integer less than, equal to, or greater than zero if the first argument is considered to be respectively less than, equal to, or greater than the second. 
	 * @param array $a
	 * @param array $b
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	public static function cust_sort($a, $b) {
		if ( self::$columnOrder == "ASC" ) {
			return strtolower($a[self::$columnName]) > strtolower($b[self::$columnName]);
		} else {
			return strtolower($a[self::$columnName]) < strtolower($b[self::$columnName]);	    	
		}
	}
}