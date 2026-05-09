<?php

/**
 * Contract related static methods.
 * @author Pavel Klinger
 * @see Entities_Contract
 *
 */
class Webservice_Contract extends Webservice_AbstractWebService  {

	public static $TABLE							= "contract";
	public static $TABLE_PREFIX						= "cn";
	public static $TABLE_CONTRACT_TEMPLATE			= "contract_template";
	public static $TABLE_CONTRACT_TEMPLATE_PREFIX	= "ct";
	public static $TABLE_CONTRACT_PARAMETER			= "contract_parameter";
	public static $TABLE_CONTRACT_PARAMETER_VALUE	= "contract_parameter_value";
	public static $IDENTITY = "id";

	protected static $ENTITY_NAME = "Entities_Contract";

	protected static $CONV = array(
		'id'				=> 'contractId',
		'date_valid_from'	=> 'dateValidFrom',
		'date_valid_to'		=> 'dateValidTo',
		'date_signed'		=> 'dateSigned',
		'date_canceled'		=> 'dateCanceled',
		'template_id'		=> 'templateId',
		'branch_id'			=> 'branchId',
		'ct.name'			=> 'templateName'
	);



	protected static $PARAMETER_VALUE_CONV = array(
		'id'		=> 'parameterId',
		'name'		=> 'name',
		'value'		=> 'value'
	);



	protected static function getDb() {
		return static::getAdminDb();
	}




	protected static function defaultJoins($query) {
		return parent::defaultJoins($query)
			->join(
				array(self::$TABLE_CONTRACT_TEMPLATE_PREFIX => self::$TABLE_CONTRACT_TEMPLATE),
				self::$TABLE_PREFIX.'.template_id = '.self::$TABLE_CONTRACT_TEMPLATE_PREFIX.'.id'
			);
	}




	/**
	 * Returns all contracts in the system.
	 * @return array array of the ticket structures
	 * @see Entities_Sport
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}



	/**
	 * Find contract by given identifier.
	 * @param array $contractId identifier of the contract
	 * @return struct contract structure
	 * @see Entities_Contract
	 */
	public static function getById($contractId, $extensions = null) {
		return parent::getById($contractId, $extensions);
	}



	/**
	 * Find contract by given branch.
	 * @param array $branchId identifier of the branch
	 * @return struct contract structure
	 * @see Entities_Contract
	 */
	public static function getByBranch($branchId, $extensions = null) {
		return static::getAllWhere(
			array('branch_id = ?' => $branchId), $extensions);
	}



	/**
	 * Find opened (editable) contracts by given branch.
	 * @param array $branchId identifier of the branch
	 * @return struct object of contract structure
	 * @see Entities_Contract
	 */
	public static function getOpenByBranch($branchId, $extensions = null) {
		return static::getAllWhere(array(
			'branch_id = ?' => $branchId,
			'date_canceled IS NULL',
			'date_signed IS NULL',
			), $extensions);
	}



	/**
	 * Find not active but signed contracts by given branch.
	 * @param array $branchId identifier of the branch
	 * @return struct object of contract structure
	 * @see Entities_Contract
	 */
	public static function getSignedButNotActiveByBranch($branchId, $extensions = null) {
		return static::getAllWhereOrder(
			array(
				'branch_id = ?' => $branchId,
				'date_canceled IS NULL',
				'date_signed IS NOT NULL', // 'date_signed <= ?' => It6_Date::dbNowAsDate(),
				'date_valid_from > ?' => It6_Date::dbNowAsDate()
			),
			array('date_valid_from ASC', 'date_signed DESC'),
			$extensions
		);
	}



	/**
	 * Find active (current) contract by given branch.
	 * @param array $branchId identifier of the branch
	 * @return struct contract structure
	 * @see Entities_Contract
	 */
	public static function getActiveByBranch($branchId, $extensions = null) {
		return static::getAllWhere(array(
			'branch_id = ?' => $branchId,
			'date_canceled IS NULL',
			'date_signed IS NOT NULL',
			'date_valid_from <= ?' => It6_Date::dbNow() ,
			'date_valid_to >= ?' => It6_Date::dbNow()
			), $extensions);
	}



	/**
	 * Find expired contracts by given branch.
	 * @param array $branchId identifier of the branch
	 * @return struct object of contract structure
	 * @see Entities_Contract
	 */
	public static function getExpiredByBranch($branchId, $extensions = null) {

		return static::getAllWhere(array(
			'branch_id = ?' => $branchId,
			'date_canceled IS NULL',
			'date_valid_to < ?' => It6_Date::dbNow()
			), $extensions);
	}



	/**
	 * Find suspended contracts by given branch.
	 * @param array $branchId identifier of the branch
	 * @return struct object of contract structure
	 * @see Entities_Contract
	 */
	public static function getCanceledByBranch($branchId, $extensions = null) {
		return static::getAllWhere(array(
			'branch_id = ?' => $branchId,
			'date_canceled IS NOT NULL'
			), $extensions);
	}



	/**
	 * Insert new contract. Value of the contract identifier is ignored and new
	 * is generated.
	 * @param struct $contract structure of the contract
	 * @return integer contract identifier of the created contract
	 * @see Entities_Contract
	 */
	public static function insert($contract) {
		//TODO Add parameter mandatority check

		$admindb = static::getDb();

		It6_DbTransaction::begin($admindb);


		try {
			$contract = new It6_ArrayWrapper($contract);
			$data = static::fromEntity($contract);

			$data = self::prepareData($data);
			if(is_string($data) || !is_array($data)){
				It6_DbTransaction::rollback($admindb);
				return $data;
			}


			$admindb->insert(self::$TABLE, $data);
			$newId = $admindb->lastInsertId();

			$parameters = array();
			foreach($contract->parameters as $param) {
				if(!empty($param['value']))
					$parameters[] = $param;
			}

			static::insertManyToManyWithParameter(
				$admindb,
				static::$TABLE_CONTRACT_PARAMETER_VALUE,
				'contract_id', $newId,
				'parameter_id', 'parameterId',
				'value', 'value', $parameters);

			It6_DbTransaction::commit($admindb);
			return $newId;
		}

		catch ( Exception $e ) {
			It6_DbTransaction::rollback($admindb);
			throw new It6_XmlRpc_Exception("Can not insert contract.", 0, $e);
		}
	}



	/**
	 * Update contract.
	 * @param struct $contract structure of the contract
	 * @return true on success
	 * @see Entities_Contract
	 */
	public static function update($contract) {
		//TODO Add parameter mandatority check

		$admindb = static::getDb();

		It6_DbTransaction::begin($admindb);

		try {
			$contract = new It6_ArrayWrapper($contract);
			$data = static::fromEntity($contract);

			$data = self::prepareData($data);
//var_dump($data);
			if(is_string($data)) {
				It6_DbTransaction::rollback($admindb);
				return $data;
			}

			$admindb->update(
				static::$TABLE,
				$data,
				array( self::$IDENTITY . '=?' => $contract->contractId)
			);

			$parameters = array();
			if(!empty($contract->parameters)) {
				foreach($contract->parameters as $param) {
					if(!empty($param['value']))
						$parameters[] = $param;
				}
			}

			static::updateManyToManyWithParameter(
				$admindb,
				static::$TABLE_CONTRACT_PARAMETER_VALUE,
				'contract_id', $contract->contractId,
				'parameter_id', 'parameterId',
				'value', 'value', $parameters
			);


			It6_DbTransaction::commit($admindb);
			return $contract->contractId;
		}

		catch ( Exception $e ) {
			It6_DbTransaction::rollback($admindb);
			throw new It6_XmlRpc_Exception("Can not update contract.", 0, $e);
		}
	}



	/**
	 * Delete contract.
	 * @param struct $contractId idenetifier of the contract.
	 * @return true on success
	 * @see Entities_Contract
	 */
	public static function delete($contractId) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}



	public static function toEntity($contract, $columns = null) {

		try {
			$defParam = static::getDb()->select()
				->from(
					self::$TABLE_CONTRACT_PARAMETER,
					null
				)
				->where('template_id=?', $contract['templateId']);

			$defParam = static::defaultColumns($defParam, self::$PARAMETER_VALUE_CONV)
				->query()->fetchAll();

			$localParam = static::getDb()->select()
				->from(
					self::$TABLE_CONTRACT_PARAMETER_VALUE,
					array('parameterId'=>'parameter_id', 'value')
				)
				->where('contract_id=?',$contract['contractId'])
				->query()->fetchAll();


			foreach($defParam as $dKey => $dParam){
				$defParam[$dKey]['isDefault'] = 1;
				$defParam[$dKey]['value'] =  str_replace(",", ".", $defParam[$dKey]['value']);
				foreach($localParam as $lKey => $lParam){
					if($lParam[self::$PARAMETER_VALUE_CONV['id']] == $dParam[self::$PARAMETER_VALUE_CONV['id']]){
						$defParam[$dKey]['value'] = str_replace(",", ".", $lParam['value']);
						$defParam[$dKey]['isDefault'] = 0;
						break;
					}
					else
						$defParam[$dKey]['isDefault'] = 1;
				}
			}

			$contract['parameters'] = $defParam;
			//	return $contract;
			return parent::toEntity($contract);
/*
			$ret->parameters = array();

			foreach ( $defParam as $param ) {
				$entity = new Entities_ContractParameterValue();
				$entity = static::mapDbArray2Entity($param, $entity, static::$PARAMETER_VALUE_CONV);
				$ret->parameters[] = $entity;
return true;
			}

			return $ret;
*/

		}
		catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("toEntity.", 0, $e);
		}

	}



/**
 * Prepares data for insert and update functions
 * @param struct $data array of data to be put in the database
 * @return struct data with data to be inserted or error array
 */
	protected static function prepareData($data) {

		if(isset($data['date_signed']) && $data['date_signed'] > $data['date_valid_from'])
			return 'Date Signed must be before contract becomes effective.';
		if(isset($data['date_valid_to']) && $data['date_valid_to'] <= $data['date_valid_from'])
			return 'Date valid from must be before date valid to.';

		if(!empty($data['date_signed'])) {
			$activeContract		= self::getActiveByBranch($data['branch_id']);
			if (!empty($activeContract)) {
				$activeContract		= reset($activeContract);
				$signedNAContracts	= self::getSignedButNotActiveByBranch($data['branch_id']);
	
				if( ($activeContract['dateValidTo'] > $data['date_valid_from'])  &&  ($activeContract['contractId'] != $data['id']) )
					return 'The date valid from is in conflict with current active contract.';
	
				foreach($signedNAContracts as $contract){
					if(
						($contract['dateValidTo'] > $data['date_valid_from']
						|| $contract['dateValidFrom'] < $data['date_valid_to'])
						&& ($activeContract['contractId'] != $data['id']) )
						return 'The date valid from is in conflict with a signed, but no yet active contract.';
				}
			}
		}

		unset($data['template_name']);
		unset($data['id']);
		unset($data['parameters']);
		unset($data['name']);

		foreach($data as $key => $dat){
			if(empty($dat))
				unset($data[$key]);
		}

		return $data;
	}



	public static function getDefaultParameters($templateId) {

		$db = static::getDb();
		$defParams = $db->select()
			->from(
				'contract_parameter',
				array('id','value')
			)
			->where('template_id=?', $templateId)
			->query()->fetchAll();

		foreach($defParams as $param ){
			$outdata[$param['id']] = $param['value'];
		}

		return $outdata;
	}



	/**
	 * Reset current contract parameters to the default ones.
	 * @param array $ids identifier of the parameter ids
	 * @param integer $contractId identifier of the contract
	 * @return bool parameters map from name to value
	*/
	public static function resetContractParameters($ids, $contractId) {

		$admindb = static::getDb();
		It6_DbTransaction::begin($admindb);

		try {
			$res = true;
			foreach ($ids as $id) {
				$res = $res && self::resetContractParameter($id, $contractId);
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
	 * Reset current contract parameter to the default one.
	 * @param integer $paramId identifier of the parameter name
	 * @param integer $contractId identifier of the branch
	 * @return bool parameters map from name to value
	*/
	public static function resetContractParameter($paramId, $contractId) {

		$admindb = static::getDb();
		It6_DbTransaction::begin($admindb);

		try {
			$where = array();
			$where['contract_id=?'] = $contractId;
			$where['parameter_id=?'] = $paramId;
			$res = $admindb->delete(self::$TABLE_CONTRACT_PARAMETER_VALUE, $where);

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
}
