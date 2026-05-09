<?php

/**
 * Parameters related static methods. Parameters are global string constants,
 * overideble per host or user.
 * @author Filip Vesely
 */
class Webservice_Parameter extends Webservice_AbstractWebService {

	const PARAM_NAME_POINT_CURRENCY_RATE = 'point-currency-rate';
	const PARAM_NAME_USER_DEPOSIT_CASH_MAX = 'user-deposit-cash-max';
	const PARAM_NAME_USER_WITHDRAW_CASH_MAX = 'user-withdraw-cash-max';
	
	public static $TABLE = "parameter";
	public static $TABLE_PREFIX = "pr";
	public static $IDENTITY = "id";
	public static $ENTITY_NAME = "Entities_Parameter";
	public static $CONV = array(
		'id'			    => 'parameterId',
		'name'			    => 'name',
		'value'			    => 'value',
		'type' 			    => 'type',
		'mandatory'		    => 'mandatory',
		'is_editable'	    => 'isEditable',
		'description'	    => 'description',
		'manually_inserted' => 'manually_inserted'
	);
	
	public static $TABLE_LOG   = "parameter_log";
	public static $TABLE_ADMIN = "admin";

	protected static function getDb() {
		return static::getAdminDb();
	}

	/**
	 * Value of the parameter with given name.
	 * @param string|array $name Name of the parameter or list of names
	 * @return string|array|NULL If one parameter was requested, its value
	 *                           is returned or NULL if not found.
	 *                           If more parameters were requested, map
	 *                           (name => value) is returned, not found
	 *                           parameters will have NULL values. 
	 */
	public static function getGlobalParameter($name) {
		$rows = static::getDb()->select()
			->from(
				array('p'=>self::$TABLE),
				array('name', 'value'))
			->where('p.name IN (?)', $name)
			->query()
			->fetchAll();
		if (is_array($name)) {
			$params = array();
			foreach ($rows as $row) {
				$params[ strtolower($row['name']) ] = $row['value'];
			}
			$result = array();
			foreach ($name as $_name) {
				$lcName = strtolower($_name);
				$result[$_name] = (isset($params[$lcName]) ? $params[$lcName] : null);
			}
			return $result;
		}
		else {
			return (empty($rows) ? null : $rows[0]['value']);
		}
		return empty($ret) ? null : $ret['value'];
	}

	/**
	 * Value of the parameter with given name.
	 * @param string $name name of the parameter
	 * @param string $value value of the parameter
	 * @return string
	 */
	public static function setGlobalParameter($name, $value) {
		try {
			static::getDb()->update(
				static::$TABLE,
				array('value' => $value),
				array('name = ?' => $name));
		}
		catch ( Excetpion $e ) {
			throw new It6_XmlRpc_Exception('setGlobalParameter',0,$e);
		}
	}

	/**
	 * Value of the parameter with given name in the given host context.
	 * @param string $name name of the parameter
	 * @param integer $hostId identifier of the host
	 * @return string
	 */
	public static function getHostParameter($name, $hostId) {
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Value of the parameter .
	 * @param integer $paramId identifier of the parameter
	 * @param integer $branchId identifier of the branch
	 * @return string
	 */
	public static function info($paramId, $branchId) {
		try {
			// ******************
			// Get parameter name
		    // ******************
			$paramName = static::getDb()->select()
				->from(self::$TABLE)
				->where('id = ?',$paramId)
				->query()->fetchAll();

			$param['paramName'] = $paramName[0]['name'];
	
			// *****************
			// Get parameter log
			// *****************
			$infoParams = static::getDb()->select()
				->from(array('pl' => self::$TABLE_LOG))
			    ->joinLeft(array('p' => 'parameter'),
			            'p.id = pl.param_id',
			            array('p.name'))
			    ->joinLeft(array('adm' => 'admin'),
			            'adm.admin_id = pl.admin_id',
			            array('adm.first_name', 'adm.surname'))
				->where('pl.param_id = ?', $paramId)
				->order('pl.time DESC');

				if ( !is_null($branchId) ) {	
	 				$infoParams->where('pl.branch_id = ?', $branchId);
	 			}
				
			$param['paramData'] = It6_ArrayWrapper::toNativeArray($infoParams->query()->fetchAll());
			return $param;
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception($e);
		}

	}

	/**
	 * Common implementation for get{Branch,User,Admin}Parameter.
	 * Returns branch/user/admin parameter, if not found/exists,
	 * global parameter is returned.
	 * @param string $name Parameter name
	 * @param integer $id Branch/user/admin id
	 * @param string $table Name of DB table of 1:N relation
	 * @param string $idColumn Name of column in $table that should has $id value
	 * @param string $flagColumn Name of column in global parameter table that has "there is 1:N relation" meaning
	 * @return string|NULL Parameter value
	 * @throws It6_XmlRpc_Exception
	 */
	private static function getOverloadedParameter($name, $id, $table, $idColumn, $flagColumn) {
		static $cache = array(); // table -> name -> id
		if (isset($cache[$table][$name]) && array_key_exists($id, $cache[$table][$name])) {
			return $cache[$table][$name][$id];
		}
		try {
			$db = static::getDb();
			$id = intval($id);
			$idColumn = $db->quoteIdentifier($idColumn);
			$flagColumn = $db->quoteIdentifier($flagColumn);
			$row = $db->select()
				->from(
					array('p' => self::$TABLE),
					array('value')
				)
				->joinLeft(
					array('op' => $table),
					"op.parameter_id = p.id AND op.$idColumn = $id",
					array('oValue' => 'value')
				)
				->where('p.name = ?', $name)
				->where("p.$flagColumn<>0")
				->limit(1)
				->query()->fetch();
			if (empty($row)) {
				$result = null; 
			}
			else {
				$result = (isset($row['oValue']) ? $row['oValue'] : $row['value']);
			}
			$cache[$table][$name][$id] = $result;
			return $result;
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception($e);
		}
	}

		/**
	 * Common implementation for get{Branch,User,Admin}Parameters.
	 * Returns branch/user/admin parameter, if not found/exists,
	 * global parameter is returned.
	 * @param string $name Parameter name
	 * @param integer $id Branch/user/admin id
	 * @param string $table Name of DB table of 1:N relation
	 * @param string $idColumn Name of column in $table that should has $id value
	 * @param string $flagColumn Name of column in global parameter table that has "there is 1:N relation" meaning
	 * @return string|NULL Parameter value
	 * @throws It6_XmlRpc_Exception
	 */

		private static function getOverloadedParameters($name, $id, $table, $idColumn, $flagColumn) {
		try {
			$db = static::getDb();
			if (!is_array($id))
				$id = array($id);

			if (!is_array($name))
				$name = array($name);

			$idColumn = $db->quoteIdentifier($idColumn);
			$flagColumn = $db->quoteIdentifier($flagColumn);
			$row = $db->select()
				->from(
					array('p' => self::$TABLE),
					array('value', "p.name","op.branch_id")
				)
				->joinLeft(
					array('op' => $table),
					"op.parameter_id = p.id",
					array('oValue' => 'value')
				)
				->where('p.name IN (?)', $name)
				->where("p.$flagColumn<>0")
				// ->where("op.$idColumn IN (?)", $id)
				->query()->fetchAll();
			return $row;
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception($e);
		}
	}

	/**
	 * Value of the parameter with given name in the given branch context.
	 * @param string $name name of the parameter
	 * @param integer $branchId identifier of the branch
	 * @return string
	 */
	public static function getBranchParameter($name, $branchId) {
		return static::getOverloadedParameter(
			$name, $branchId, Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER, 'branch_id', 'is_branch'
		);
	}

		/**
	 * Value of the parameters with given names in the given branches context.
	 * @param string $name name of the parameter
	 * @param integer $branchId identifier of the branch
	 * @return string
	 */
	public static function getBranchParameters($names, $branchIds) {
		return static::getOverloadedParameters(
			$names, $branchIds, Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER, 'branch_id', 'is_branch'
		);
	}

	/**
	 * Value of the parameter with given name in the given user context.
	 * @param string $name name of the parameter
	 * @param integer $userId identifier of the user
	 * @return string
	 */
	public static function getUserParameter($name, $userId) {
		return static::getOverloadedParameter(
			$name, $userId, Webservice_User::$TABLE_USER_HAS_PARAMETER, 'user_id', 'is_user'
		);
	}

	/**
	 * Value of the parameter with given name in the given admin context.
	 * @param string $name name of the parameter
	 * @param integer $adminId identifier of the admin
	 * @return string
	 */
	public static function getAdminParameter($name, $adminId) {
		return static::getOverloadedParameter(
			$name, $adminId, Webservice_Admin::$TABLE_ADMIN_HAS_PARAMETER, 'admin_id', 'is_admin'
		);
	}

	/**
	 * Get all parameters in the given host context.
	 * All host/branch specific parameters together with
	 * 	PARAM_NAME_POINT_CURRENCY_RATE
	 *	PARAM_NAME_USER_DEPOSIT_CASH_MAX
	 *  PARAM_NAME_USER_WITHDRAW_CASH_MAX
	 *  (see these class constants for key values)
	 * @return struct parameters map from name to value
	 */
	public static function getHostParameters() {
		$acl = Zend_Registry::get('acl');
		$hostId = $acl->getIdentity(It6_Acl::IDNAME_HOST);
		//$branchId = $acl->getIdentity(It6_Acl::IDNAME_ADMIN);
		$branchId = $acl->getIdentity(It6_Acl::IDNAME_BRANCH);

		try {
			$db = static::getDb();
			
			$ret = $db->select()
				->from(
					array('p'=>self::$TABLE),
					null)
				->joinLeft(
					array('bp' => Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER),
					"bp.parameter_id = p.id AND bp.branch_id = $branchId",
					null)
				->joinLeft(
					array('hp' => Webservice_Host::$TABLE_HOST_HAS_PARAMETER),
					"hp.parameter_id = p.id AND hp.host_id = $hostId",
					null)
				->columns(array(
					'name' => 'p.name',
					'pvalue' => 'p.value',
					'hvalue' => 'hp.value',
					'bvalue' => 'bp.value',
					'hprior' => 'p.is_host',
					'bprior' => 'p.is_branch'
					))
				->where('p.is_host <> ? OR p.is_branch <> ?', 0)
				->orWhere('p.name IN (?)', array(
					// specific global parameters, that should be returned among host parameters
					It6_Models_Parameter::NAME_TICKET_MAXIMAL_WIN,
					It6_Models_Parameter::NAME_TICKET_MAXIMAL_WIN_MAXIKOMBI,
					It6_Models_Parameter::NAME_TICKET_MAX_GROUPS_MAXIKOMBI,
				))
				->query()
				->fetchAll();


			$out = $o = array();
			foreach ($ret as $k => $r) {

				$o['name'] = $r['name'];

				if (($r['hprior'] >= $r['bprior']) && !empty($r['hvalue']))
					$o['value'] = $r['hvalue'];
				else if (($r['hprior'] < $r['bprior']) && !empty($r['bvalue']))
					$o['value'] = $r['bvalue'];
				else 	$o['value'] = $r['pvalue'];

				$out[] = $o;
			}


			/*foreach ($out as $key => $value) {
				if ($value['name']=="e") {
					$out[$key]['value'] = "666";
				}
			}*/

			$pointType = Webservice_PointsType::getById(Webservice_PointsType::DEFAULT_POINT_TYPE_ID);
			$out[] = array(
				'name' => static::PARAM_NAME_POINT_CURRENCY_RATE,
				'value' => $pointType['rate']
			);

			// limits for selected financial transaction types
			$ftTypes = Webservice_TransactionType::getAllWhere(array(
				'name IN (?)' => array(
					Webservice_TransactionType::NAME_USER_DEPOSIT_CASH,
					Webservice_TransactionType::NAME_USER_WITHDRAW_CASH,
				)
			));
			foreach ($ftTypes as $type) {
				switch ($type['name']) {
					case Webservice_TransactionType::NAME_USER_DEPOSIT_CASH:
						$out[] = array(
							'name' => static::PARAM_NAME_USER_DEPOSIT_CASH_MAX,
							'value' => $type['highLimit'],
						);
						break;
					case Webservice_TransactionType::NAME_USER_WITHDRAW_CASH:
						$out[] = array(
							'name' => static::PARAM_NAME_USER_WITHDRAW_CASH_MAX,
							'value' => -$type['lowLimit'],
						);
						break;
				}
			}

			// control of bet changelog usage
			if (defined('BRANCH_BET_CHANGELOG')) {
				$out[] = array(
					'name' => 'CACHE',
					'value' => (BRANCH_BET_CHANGELOG ? '1' : '0'),
				);
			}

			$out[] = array(
					'name' => 'NONSTOP',
					'value' => '0'
				);

			return $out;
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception($e);
		}
	}

	/**
	 * Get all parameters in the given host context.
	 * @param array $hostId identifier of the host
	 * @return struct parameters map from name to value
     */
	public static function getByBranchId($branchId) {
//TODO: could be done with routines at the SQL layer so that the query is just one and clean with no need for neste looping
		try {
			$defParams = static::getDb()->select()
				->from(
					array('p'=>self::$TABLE),
					array('parameterId'=>'id', 'name', 'value','description','manually_inserted')
				)

				//->from(self::$TABLE, array('parameterId'=>'id', 'name', 'value','description','manually_inserted'))
				->joinLeft(
					array('bp' => Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER),
					"bp.parameter_id = p.id",
				null)
				->where('is_branch <> ?', 0)
	            ->where('bp.branch_insert IS NULL')
				->query()
				->fetchAll();

			$localParams = static::getDb()->select()
				->from(
					array('bp' => Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER),
					array('parameterId'=>'parameter_id', 'value', 'branch_insert')
				)
				->joinLeft(
					array('p'=>self::$TABLE),
					"p.id = bp.parameter_id",
					array('name','description','manually_inserted')
				)
				->where('branch_id = ?', $branchId)
				->query()
				->fetchAll();

			$aDefaultParams = $aLocalParams = array();
			foreach ($defParams as $param) {
				$aDefaultParams[$param['parameterId']] = $param;
				$aDefaultParams[$param['parameterId']]['local_parameter'] = 0;
			}
			foreach ($localParams as $param) {
				$aLocalParams[$param['parameterId']] = $param;
				$aLocalParams[$param['parameterId']]['local_parameter'] = 1;
			}

			foreach ($aDefaultParams as $key => $value) {
				if ( isset($aLocalParams[$key]) ) {
					$aLocalParams[$key]['default_value'] = $value['value'];
					$aLocalParams[$key]['local_parameter'] = 2; // changed value
					unset($aDefaultParams[$key]);
				}
			}

			return array_merge($aLocalParams, $aDefaultParams);
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception($e);
		}
	}


	/**
	 * Get all parameters in the given user context.
	 * @param array $userId identifier of the user
	 * @return struct parameters map from name to value
	 */
	public static function getByUserId($userId) {
		try {
			$defParams = static::getDb()->select()
					->from(self::$TABLE, array('parameterId'=>'id', 'name', 'value'))
					->where('is_user <> ?', 0)
					->query()->fetchAll();


			$localParams = static::getDb()->select()
				->from(
					Webservice_User::$TABLE_USER_HAS_PARAMETER,
					array('parameterId'=>'parameter_id', 'value')
				)
				->where('user_id = ?', $userId)
				->query()->fetchAll();


			foreach($defParams as $dKey => $dParam){
				$defParams[$dKey]['isDefault'] = 1;
				foreach($localParams as $lKey => $lParam){
					if($lParam['parameterId'] == $dParam['parameterId']){
						$defParams[$dKey]['value'] = $lParam['value'];
						$defParams[$dKey]['isDefault'] = 0;
						break;
					}
				}
			}

			return $defParams;
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception($e);
		}
	}

	/**
	 * Get all parameters in the given admin context.
	 * @param array $adminId identifier of the admin
	 * @return struct parameters map from name to value
	 */
	public static function getByAdminId($adminId) {


		$defParam = static::getDb()->select()
			->from(
				array('p'=>self::$TABLE),
				array('id', 'name', 'type', 'mandatory', 'value')
			)
			->where('p.is_admin <> ?', 0)
			->query()->fetchAll();


		$localParam = static::getDb()->select()
			->from(
				array('bp'=>Webservice_Admin::$TABLE_ADMIN_HAS_PARAMETER),
				array('id'=>'parameter_id', 'value')
			)
			->where('bp.admin_id=?',$adminId)
			->query()->fetchAll();


		foreach($defParam as $dKey => $dParam){
			$defParam[$dKey]['isDefault'] = 1;
			foreach($localParam as $lKey => $lParam){
				if($lParam['id'] == $dParam['id']){
					$defParam[$dKey]['value'] = $lParam['value'];
					$defParam[$dKey]['isDefault'] = 0;
					break;
				}
				else
					$defParam[$dKey]['isDefault'] = 1;
			}
		}

		return static::toEntities($defParam);
	}


 	/**
	 * Get all parameters in the given user context.
	 * @return struct parameters map from name to value
	 */
	public static function getUserParameterNames() {
		$admindb = static::getDb();

		$rows = $admindb->select()
		->from(
			array('p'=>self::$TABLE),
			array('name','id'))
		->where('p.is_user <> ?', 0)
		->query()->fetchAll();

		return $rows;
	}

	/**
	 * Get all parameter name in the given branch context.
	 * @return struct parameters map from name to value
	 */
	public static function getBranchParameterNames() {
		$admindb = static::getDb();

		$rows = $admindb->select()
		->from(
			array('p'=>self::$TABLE),
			array('name','id'))
		->where('p.is_branch <> ?', 0)
		->query()->fetchAll();

		//return static::toEntities($rows);
		return $rows;
	}

	/**
	 * Get all parameter name in the given branch context.
	 * @param int $branchId identifier of the branch
	 * @return struct parameters map from name to value
	 */
	public static function getBranchParameterNamesForSettings($branchId) {
			$defParams = static::getDb()->select()
				->from(
					array('p'=>self::$TABLE),
					array('parameterId'=>'id', 'name', 'value','description','manually_inserted')
				)

				->joinLeft(
					array('bp' => Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER),
					"bp.parameter_id = p.id",
				null)
				->where('is_branch <> ?', 0)
	            ->where('bp.branch_insert IS NULL')
				->query()
				->fetchAll();

			$localParams = static::getDb()->select()
				->from(
					array('bp' => Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER),
					array('parameterId'=>'parameter_id', 'value', 'branch_insert')
				)
				->joinLeft(
					array('p'=>self::$TABLE),
					"p.id = bp.parameter_id",
					array('name','description','manually_inserted')
				)
				->where('branch_id = ?', $branchId)
				->query()
				->fetchAll();

			$aDefaultParams = $aLocalParams = array();
			foreach ($defParams as $param) {
				$aDefaultParams[$param['parameterId']] = $param;
				$aDefaultParams[$param['parameterId']]['local_parameter'] = 0;
			}
			foreach ($localParams as $param) {
				$aLocalParams[$param['parameterId']] = $param;
				$aLocalParams[$param['parameterId']]['local_parameter'] = 1;
			}

			foreach ($aDefaultParams as $key => $value) {
				if ( isset($aLocalParams[$key]) ) {
					$aLocalParams[$key]['default_value'] = $value['value'];
					$aLocalParams[$key]['local_parameter'] = 2; // changed value
					unset($aDefaultParams[$key]);
				}
			}

			return array_merge($aLocalParams, $aDefaultParams);
	}

	/**
	 * Update custom branch parameters .
	 * @param struct $params array of param names and values
	 * @param bool $updateNoEditable only if is true noEditable parameters are updated
	 * @return bool parameters map from name to value
     */
	public static function updateDefaultParameters($params, $updateNoEditable = false) {

		$admindb = static::getDb();
		It6_DbTransaction::begin($admindb);

		try {
			$res = true;
			foreach ($params as $parId => $parValue) {
				$res = $res && self::updateDefaultParameter($parId, $parValue, $updateNoEditable, true);
			}

			It6_DbTransaction::commit($admindb);

			return $res;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($admindb);
			throw new It6_XmlRpc_Exception("Can not update default parameters.", 0, $e);
		}
	}

	/**
	 * Update default branch parameter.
	 * @param string $name name of param
	 * @param string $value value of the param
	 * @param bool $updateNoEditable only if is true noEditable parameters are updated
	 * @return bool parameters map from name to value
     */
	public static function updateDefaultParameter($id, $value, $updateNoEditable = false) {

		$admindb = static::getDb();
		It6_DbTransaction::begin($admindb);

		try {

			$data = array(
				'value' => $value
			);
			$where = array();
			$where['id = ?'] = $id;
			if ( !$updateNoEditable ) {
				$where['is_editable = ?'] = 1;
			}
			$res = $admindb->update(self::$TABLE, $data, $where);
			It6_GlobalCache::deleteKey(It6_GlobalCache::KEY_PREFIX_GPARAM . $id);

			It6_DbTransaction::commit($admindb);
			It6_GlobalCache_Invalidator::invalidateHomePage();

			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($admindb);
			throw new It6_XmlRpc_Exception("Can not update default parameter.", 0, $e);
		}
	}

	public static function insertBranchParameters($params, $branchId, $updateNoEditable = false) {

	}

	/**
	 * Update custom branch parameters .
	 * @param struct $params array of param names and values
	 * @param integer $branchId identifier of the host
	 * @param bool $updateNoEditable only if is true noEditable parameters are updated
	 * @return bool parameters map from name to value
     */
	public static function updateBranchParameters($params, $branchId, $updateNoEditable = false) {

		$admindb = static::getDb();
		It6_DbTransaction::begin($admindb);

		try {
			$res = true;
			foreach ($params as $paramId => $parValue) {
				$res = self::updateBranchParameter($paramId, $parValue, $branchId, $updateNoEditable);
			}

			It6_DbTransaction::commit($admindb);

			return $res;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($admindb);
			throw new It6_XmlRpc_Exception("Can not update parameters.", 0, $e);
		}
	}


	/**
	 * Update custom branch parameters .
	 * @param struct $params array of param names and values
	 * @param integer $userId identifier of the user
	 * @param bool $updateNoEditable only if is true noEditable parameters are updated
	 * @return bool parameters map from name to value
     */
	public static function updateUserParameters($params, $userId, $updateNoEditable = false) {

		$admindb = static::getDb();
		It6_DbTransaction::begin($admindb);

		try {
			$res = true;
			foreach ($params as $paramId => $parValue) {
				$res = self::updateUserParameter($paramId, $parValue, $userId, $updateNoEditable);
			}

			It6_DbTransaction::commit($admindb);
			//It6_GlobalCache_Invalidator::invalidateHomePage();

			return $res;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($admindb);
			throw new It6_XmlRpc_Exception("Can not update parameters.", 0, $e);
		}
	}


	/**
	 * Update custom user parameter .
	 * @param integer $paramId name of param
	 * @param integer $value value of the param
 	 * @param integer $userId identifier of the user
 	 * @param bool $updateNoEditable only if is true noEditable parameters are updated
	 * @return bool parameters map from name to value
     */
	public static function updateUserParameter($paramId, $value, $userId, $updateNoEditable = false) {

		$admindb = static::getDb();
		It6_DbTransaction::begin($admindb);

		try {

			if ($paramId == 0) {
				$res = false;
			}
			else {

				if ( !$updateNoEditable ) {
					$tmp = $admindb->select()
						->from(self::$TABLE,array('count' => 'count(*)'))
						->where('is_editable=1')
						->where('id = ?',$paramId)
						->query()->fetch();

					if ( 1 != $tmp['count'] ) {
						return true;
					}
				}

				$row = $admindb->select()
				->from(
					array('bp'=>Webservice_User::$TABLE_USER_HAS_PARAMETER),
					array('user_id', 'value'))
				->where('bp.user_id=?',$userId)
				->where('bp.parameter_id=?',$paramId)
				->query()->fetch();

				if (empty($row)) { //insert
					$data = array(
						'parameter_id' => $paramId,
						'user_id' => $userId,
						'value' => $value
					);
					$res = $admindb->insert(Webservice_User::$TABLE_USER_HAS_PARAMETER, $data);
				}
				else if($row['value'] != $value){ //update
					$data = array(
						'value' => $value
					);
					$where = array();
					$where['user_id=?'] = $userId;
					$where['id=?'] = $paramId;
					$res = $admindb->update(Webservice_User::$TABLE_USER_HAS_PARAMETER, $data, $where);
				}
				else
					$res = true;
			}
			It6_DbTransaction::commit($admindb);
			return $res;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($admindb);
			throw new It6_XmlRpc_Exception("Can not update parameter.", 0, $e);
		}
	}



	/**
	 * Update custom branch parameter .
	 * @param integer $name name of param
	 * @param integer $value value of the param
 	 * @param integer $branchId identifier of the branchId
 	 * @param bool $updateNoEditable only if is true noEditable parameters are updated
	 * @return bool parameters map from name to value
     */
	public static function updateBranchParameter($paramId, $value, $branchId, $updateNoEditable = false, $isStartedTransaction = true) {

		$admindb = static::getDb();
		
		if (!$isStartedTransaction)
			It6_DbTransaction::begin($admindb);

		try {

			if ($paramId == 0) {
				$res = false;
			}
			else {

				if ( !$updateNoEditable ) {
					$tmp = $admindb->select()
						->from(self::$TABLE,array('count' => 'count(*)'))
						->where('is_editable=1')
						->where('id = ?',$paramId)
						->query()->fetch();

					if ( 1 != $tmp['count'] ) {
						return true;
					}
				}

				$row = $admindb->select()
				->from(
					array('bp'=>Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER),
					array('branch_id', 'value'))
				->where('bp.branch_id=?',$branchId)
				->where('bp.parameter_id=?',$paramId)
				->query()->fetch();

				if (empty($row)) { //insert
					$data = array(
						'parameter_id' => $paramId,
						'branch_id' => $branchId,
						'value' => $value
					);
					$res = $admindb->insert(Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER, $data);
				}
				else if($row['value'] != $value){ //update
					$data = array(
						'value' => $value
					);
					$where = array();
					$where['branch_id=?'] = $branchId;
					$where['parameter_id=?'] = $paramId;
					$res = $admindb->update(Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER, $data, $where);
				}
				else
					$res = true;
			}
			if (!$isStartedTransaction)
				It6_DbTransaction::commit($admindb);
				
			return $res;
		}
		catch ( Exception $e ) {
			if (!$isStartedTransaction)
				It6_DbTransaction::rollback($admindb);
			throw new It6_XmlRpc_Exception("Can not update parameter.", 0, $e);
		}
	}



	/**
	 * Reset current branch parameters to the default ones.
	 * @param array $ids identifier of the parameter ids
	 * @param integer $branchId identifier of the branch
	 * @param bool $updateNoEditable only if is true noEditable parameters are updated
	 * @return bool parameters map from name to value
     */
	public static function resetBranchParameters($ids, $branchId, $updateNoEditable = false) {

		$admindb = static::getDb();
		It6_DbTransaction::begin($admindb);

		try {
			$res = true;
			foreach ($ids as $id) {
				$res = $res && self::resetBranchParameter($id, $branchId, $updateNoEditable, true);
			}

			It6_DbTransaction::commit($admindb);

			return $res;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($admindb);
			throw new It6_XmlRpc_Exception("Can not reset parameters.", 0, $e);
		}
	}



	/**
	 * Reset current branch parameter to the default one.
	 * @param string $paramId identifier of the parameter name
	 * @param integer $branchId identifier of the branch
	 * @param bool $updateNoEditable only if is true noEditable parameters are updated
	 * @return bool parameters map from name to value
     */
	public static function resetBranchParameter($paramId, $branchId, $updateNoEditable = false, $isStartedTransaction = true) {

		$admindb = static::getDb();
		
		if (!$isStartedTransaction)
			It6_DbTransaction::begin($admindb);

		try {

			if ( !$updateNoEditable ) {
				$tmp = $admindb->select()
					->from(self::$TABLE,array('count' => 'count(*)'))
					->where('is_editable=1')
					->where('id = ?',$paramId)
					->query()->fetch();

				if ( 1 != $tmp['count'] ) {
					return true;
				}
			}

			$where = array();
			$where['branch_id=?'] = $branchId;
			$where['parameter_id=?'] = $paramId;
			$res = $admindb->delete(Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER, $where);

			if (!$isStartedTransaction)
				It6_DbTransaction::commit($admindb);
// allways returns true as we cant tell if the row we are attempting to delete even exists.
//If it doesnt exist, its OK, we just skip it and there is no need for an error.
			return true;

		}
		catch ( Exception $e ) {
			if (!$isStartedTransaction)
				It6_DbTransaction::rollback($admindb);
				
			throw new It6_XmlRpc_Exception("Can not reset parameter.", 0, $e);
		}
	}



	/**
	 * Reset current user parameters to the default ones.
	 * @param array $ids identifier of the parameter ids
	 * @param integer $userId identifier of the user
	 * @param bool $updateNoEditable only if is true noEditable parameters are updated
	 * @return bool parameters map from name to value
     */
	public static function resetUserParameters($ids, $userId, $updateNoEditable = false) {

		$admindb = static::getDb();
		It6_DbTransaction::begin($admindb);

		try {
			$res = true;
			foreach ($ids as $id) {
				$res = $res && self::resetUserParameter($id, $userId, $updateNoEditable);
			}

			It6_DbTransaction::commit($admindb);

			return $res;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($admindb);
			throw new It6_XmlRpc_Exception("Can not reset parameters.", 0, $e);
		}
	}



	/**
	 * Reset current user parameter to the default one.
	 * @param string $paramId identifier of the parameter name
	 * @param integer $userId identifier of the user
	 * @param bool $updateNoEditable only if is true noEditable parameters are updated
	 * @return bool parameters map from name to value
     */
	public static function resetUserParameter($paramId, $userId, $updateNoEditable = false) {

		$admindb = static::getDb();
		It6_DbTransaction::begin($admindb);

		try {
			if ( !$updateNoEditable ) {
				$tmp = $admindb->select()
					->from(self::$TABLE,array('count' => 'count(*)'))
					->where('is_editable=1')
					->where('id = ?',$paramId)
					->query()->fetch();

				if ( 1 != $tmp['count'] ) {
					return true;
				}
			}

			$where = array();
			$where['user_id=?'] = $userId;
			$where['parameter_id=?'] = $paramId;
			$res = $admindb->delete(Webservice_User::$TABLE_USER_HAS_PARAMETER, $where);

			It6_DbTransaction::commit($admindb);
// allways returns true as we cant tell if the row we are attempting to delete even exists.
//If it doesnt exist, its OK, we just skip it and there is no need for an error.
			return true;

		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($admindb);
			throw new It6_XmlRpc_Exception("Can not reset parameter.", 0, $e);
		}
	}



	/**
	 * Reset parameter Id by name
	 * @param string $name identifier of the parameter name
	 * @return integer id of the parameter
     */
	public static function getParameterIdByName($name) {
		$admindb = static::getDb();
		$row = $admindb->select()
		->from(
			array('p'=>self::$TABLE)
			)
		->where('p.name LIKE ?',$name)
		->query()->fetch();

		if (!empty($row))
			return $row['id'];
		else
			return 0;
	}

	/**
	 * New parameter 
	 * @param array $parameterData
	 * @return boolean
     */
	public static function newBranchParameter($parameterData) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);

		try {	
			$db->insert(Webservice_Parameter::$TABLE, array(
				'name'              => $parameterData['name'],
				'value'             => $parameterData['value'],
				'description'       => $parameterData['description'],
				'is_host'           => 0,
				'is_branch'         => 1,
				'is_user'           => 0,
				'is_admin'          => 0,
				'is_editable'       => 1,
				'type'              => 1,
				'mandatory'         => 1,
				'manually_inserted' => 1,
			));

	        $insertedId = $db->lastInsertId();
			It6_DbTransaction::commit($db);
			return $insertedId;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
            return $e->getMessage();
		}
	}

	/**
	 * New branch_has_parameter 
	 * @param int $branchId
	 * @param int $parameterId
	 * @param string $parameterId
	 * @return boolean
     */
	public static function newBranchHasParameter($branchId , $parameterId, $value) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);

		try {	
			$db->insert(Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER, array(
				'branch_id'     => $branchId,
				'parameter_id'  => $parameterId,
				'value'         => $value,
				'branch_insert' => 1,
			));

			It6_DbTransaction::commit($db);
			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
            return false; //$e->getMessage();
		}
	}


	/**
	 * Delete parameter 
	 * @param int $paramId identifier of the parameter
	 * @return boolean
     */
	public static function deleteBranchParameter($paramId) {
		//parent::delete($paramId);

		$db = static::getDb();
		It6_DbTransaction::begin($db);

		try {	
			$db->delete(Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER, array('parameter_id = ?' => $paramId));
			$db->delete(Webservice_Parameter::$TABLE, array(Webservice_Parameter::$IDENTITY . ' = ?' => $paramId));
			It6_DbTransaction::commit($db);
			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not delete parameter (Parameter: '".get_called_class()."')", 0, $e);
		}
	}

	/**
	 * Delete branch has parameter 
	 * @param int $paramId identifier of the parameter
	 * @return boolean
     */
	public static function deleteBranchHasParameter($paramId, $branchId) {
		//parent::delete($paramId);
		$localParams = static::getDb()->select()
			->from(
				array('bp' => Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER),
				array('parameterId'=>'parameter_id', 'value', 'branch_insert')
			)
			->where('parameter_id = ?', $paramId)
			->query()
			->fetchAll();

		$db = static::getDb();
		It6_DbTransaction::begin($db);

		try {	
			$db->delete(Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER, array('branch_id = ?' => $branchId, 'parameter_id = ?' => $paramId));
			if ( !is_null($localParams[0]['branch_insert']) ) {
				$db->delete(Webservice_Parameter::$TABLE, array(Webservice_Parameter::$IDENTITY . ' = ?' => $paramId));
			}
			It6_DbTransaction::commit($db);
			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not delete parameter (Parameter: '".get_called_class()."')", 0, $e);
		}
	}

	/**
	 * Insert to parameter log
	 * @param int $paramId identifier of the parameter
	 * @param string $oldValue parameter old value
	 * @param string $newValue parameter new value
	 * @param string $action identifier action value
	 * @param string $message message value
	 * @return boolean
     */
	public static function logParameters($paramId = null, $branchId = null, $oldValue = null, $newValue = null, $action = null, $message = null) {
    	$adminId = Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_ADMIN);
        $ip=$_SERVER['REMOTE_ADDR'];
        $sql = "INSERT INTO parameter_log (ip, admin_id, param_id, branch_id, time, old_value, new_value, action, message)
                     VALUES ('$ip', '$adminId', '$paramId', '$branchId', NOW(), '$oldValue', '$newValue', '$action', '$message')";                          	
  	    Zend_Registry::get('zdb_admin')->query($sql);
	}

/**
 * name: getDefaultBranchParameters
 * Returns the default values for branch parameters
 * @return struct array of default values
 */
	public static function getDefaultBranchParameters() {
		$admindb = static::getDb();

		$res = $admindb->select()
			->from(
				array('p'=>self::$TABLE),
				array('id','name','value','is_editable','description','manually_inserted')
			)
			->joinLeft(
				array('bp' => Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER),
				"bp.parameter_id = p.id",
				null)
			->where('is_branch <> ?', 0)
			//->where('bp.branch_insert != ?', 1)
            ->Where('bp.branch_insert IS NULL')
			->query()
			->fetchAll();

		return self::prepareDefaultParameterArray($res);
	}



/**
 * name: getDefaultBranchParameters
 * Returns the default values for user parameters
 * @return struct array of default values
 */
	public static function getDefaultUserParameters() {
		$admindb = static::getDb();

		$res = $admindb->select()
			->from(
				self::$TABLE,
				array('id','name','value','is_editable','description','manually_inserted')
			)
			->where('is_user <> ?', 0)
			->query()->fetchAll();

		return self::prepareDefaultParameterArray($res);
	}



/**
 * name: getDefaultSystemParameters
 * Returns the default values for system parameters
 * @return struct array of default values
 */
	public static function getDefaultSystemParameters() {
		$admindb = static::getDb();

		$res = $admindb->select()
			->from(
				self::$TABLE,
				array('id','name','value','is_editable','description','manually_inserted')
			)
			->where('is_user=?',0)
			->where('is_branch=?',0)
			->where('is_host=?',0)
			->query()->fetchAll();

		return self::prepareDefaultParameterArray($res);
	}



	/**
	 * Get all parameter name in the system context.
	 * @return struct parameters map from name to value
	 */
	public static function getSystemParameterNames() {
		$admindb = static::getDb();

		$rows = $admindb->select()
		->from(
			array('p'=>self::$TABLE),
			array('name','id'))
		->where('p.is_branch=?',0)
		->where('p.is_user=?',0)
		->where('p.is_host=?',0)
		->query()->fetchAll();

		//return static::toEntities($rows);
		return $rows;
	}



	/**
	 * Returns all parameters in the system.
	 * @return struct parameters structure
	 * @see Entities_Parameter
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}



	/**
	 * name: getLocalBranchParametersByBranchId
	 * Returns the local values for branch parameters
	 * $param $branchId integer the identifier of the branch
	 * @return struct array of arrays defining the individual local params
	 */
	public static function getLocalBranchParametersByBranchId($branchId) {
		$outdata = array();
		$params = static::getDb()->select()
			->from(
				Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER,
				array('parameter_id', 'value')
			)
			->where(Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER.'.branch_id = ?', $branchId)
			->query()->fetchAll();

		if(!empty($params)) {
			foreach($params as $param ){
				$outdata[$param['parameter_id']]['value'] = $param['value'];
				$outdata[$param['parameter_id']]['id'] = $param['parameter_id'];
			}
		}

		return $outdata;
	}

	/**
	 * name: getLocalUserParametersByuserId
	 * Returns the local parameters for the given user
	 * $param $userId integer the identifier of the user
	 * @return struct array of arrays defining the individual local params
	 */
	public static function getLocalUserParametersByUserId($userId) {
		$outdata = array();
		$params = static::getDb()->select()
			->from(
				Webservice_User::$TABLE_USER_HAS_PARAMETER,
				array('parameter_id', 'value')
			)
			->where(Webservice_User::$TABLE_USER_HAS_PARAMETER.'.user_id = ?', $userId)
			->query()->fetchAll();

		if(!empty($params)) {
			foreach($params as $param ){
				$outdata[$param['parameter_id']]['value'] = $param['value'];
				$outdata[$param['parameter_id']]['id'] = $param['parameter_id'];
			}
		}

		return $outdata;
	}

	/**
	 * Returns all efffective parameter. Branch, Host and Admin are geted from acl.
	 * @return struct associative array name => value
	 */
	public static function getAllEffectiveParameters() {

		$acl = Zend_Registry::get('acl');
		$db = static::getDb();

		$identity = array(
			'host'  => $acl->getIdentity(It6_Acl::IDNAME_HOST),
			'admin' => $acl->getIdentity(It6_Acl::IDNAME_ADMIN),
			'branch' => $acl->getIdentity(It6_Acl::IDNAME_BRANCH));

		$ret = array();
		$names = $db->select()->from(self::$TABLE, 'name')->query()->fetchAll();
		foreach ( $names as $name )
			$ret[$name['name']] = static::getEffectiveValue($name, $identity);

		return $ret;
	}

	/**
	 * Retrieves parameter value with defined priorities for value overriding.
	 * Priorities are defined according to is_(host|branch|user|admin) columns numeric values: zero=value not defined, higher number=higher priority.
	 * Same priority column values have this implicit priority (from highest): user, admin, host, branch, default.
	 * If all priority column are zero or no one is found, default value is used.
	 * Parameter values will be fetched and used in priority calculations only for identities supplied in $identity.
	 * @param string $name
	 * @param array $identity Possible keys names: {'host','branch','user','admin'} and values are particular IDs (eg. 'host' => host_id)
	 * @throws It6_XmlRpc_Exception
	 * @return NULL|string Parameter value or NULL if not found
	 */
	public static function getEffectiveValue($name, array $identity) {
		static $implicitOrder = array('user', 'admin', 'host', 'branch'); // in priority order from highest
		try {
			$db = static::getDb();
			// remove from identity unsupported components
			foreach ($identity as $idName => $id) {
				if (!in_array($idName, $implicitOrder)) {
					unset($identity[$idName]);
				}
			}
			$select = $db->select()
				->from(array('p' => self::$TABLE), array(
					'id',
					'name',
 					'prt_host' => 'is_host',
 					'prt_branch' => 'is_branch',
 					'prt_user' => 'is_user',
 					'prt_admin' => 'is_admin',
					'value_global' => 'value',
				));
			$i = 0;
			foreach ($identity as $idName => $id) {
				++$i;
				$short = "p$i";
				$id = intval($id);
				$select->joinLeft(
					array($short => "{$idName}_has_parameter"),
					"p.is_{$idName}>0 AND {$short}.parameter_id=p.id AND {$short}.{$idName}_id=$id",
					array("value_{$idName}" => 'value')
				);
			}
			$rows = $select->where('p.name=?', $name)
				->query()
				->fetchAll();
			if (empty($rows))
				return null;
			$data = $rows[0];
			$priorities = array();
			foreach ($implicitOrder as $idName) {
				if (!empty($identity[$idName])) {
					$prt = intval($data["prt_{$idName}"]);
					if (0 != $prt) {
						$priorities[$prt][] = $idName;
					}
				}
			}
			krsort($priorities, SORT_NUMERIC);
			foreach ($priorities as $prt => $idNames) {
				foreach ($idNames as $idName) {
					if (isset($data["value_{$idName}"])) {
						return $data["value_{$idName}"];
					}
				}
			}
			return $data['value_global'];
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception($e);
		}
	}


	private static function prepareDefaultParameterArray($dbRes) {
		foreach($dbRes as $param ){
			$outdata[$param['id']]['value'] = $param['value'];
			$outdata[$param['id']]['name'] = $param['name'];
			$outdata[$param['id']]['isEditable'] = $param['is_editable'];
			$outdata[$param['id']]['description'] = $param['description'];
			$outdata[$param['id']]['manually_inserted'] = $param['manually_inserted'];
		}

		return $outdata;
	}

	/**
	 * Printed page counter.
	 * @param integer $value value of the param
	 * @param integer $hostId identifier of the host
	 * @param string $value value of the parameter
	 * @return boolean
	 */
	public static function printedPagesCounter($paramId, $hostId, $value) {

		$row = $admindb->select()
		->from(
			array('bp'=>Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER),
			array('branch_id', 'value'))
		->where('bp.branch_id=?',$branchId)
		->where('bp.parameter_id=?',$paramId)
		->query()->fetch();

		if (empty($row)) { //insert
			$data = array(
				'parameter_id' => $paramId,
				'branch_id' => $branchId,
				'value' => $value
			);
			$res = $admindb->insert(Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER, $data);
		} else { 
			$data = array(
				'value' => $value
			);
			$where = array();
			$where['branch_id=?'] = $branchId;
			$where['parameter_id=?'] = $paramId;
			$res = $admindb->update(Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER, $data, $where);
		}

	}
}
