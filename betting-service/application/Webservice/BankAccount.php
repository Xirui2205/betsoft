<?php
/**
 * Bank account related static methods.
 * @author Martin Bohal
 * @see Entities_BankAccount
 */
class Webservice_BankAccount extends Webservice_AbstractWebService  {

	public static $TABLE							= "bank_account";
	public static $TABLE_PREFIX						= "a";
	public static $TABLE_BRANCH_HAS_BANK_ACCOUNT	= "branch_has_bank_account";
	public static $ENTITY_NAME						= "Entities_BankAccount";
	public static $IDENTITY							= "account_id";

	public static $CONV = array(
		'account_id'				=> 'accountId',
		'bank_name'					=> 'bankName',
		'bank_branch'				=> 'bankBranch',
		'account_prefix'			=> 'accountPrefix',
		'account_number'			=> 'accountNumber',
		'bank_code'					=> 'bankCode',
		'specific_symbol'			=> 'specificSymbol',
		'currency'					=> 'currency',
		'note'						=> 'note',
		'cur.mena_text'				=> 'currencyIso',
		'bat.type_name'				=> 'typeName',
		'bhba.time_created'			=> 'timeCreated',
		'bhba.branch_id'			=> 'branchId',
		'bhba.bank_account_type'	=> 'typeId',
		'bhba.is_current'			=> 'isCurrent',
		'bhba.branch_id'			=> 'branchId',
	);



	protected static function getDb() {
		return static::getAdminDb();
	}



	/**
	 * Update bank account.
	 * @param struct $bankAccount structure of the account
	 * @return true on success
	 * @see Entities_BankAccount
	 */
	public static function update($bankAccount) {

		try {
			It6_DbTransaction::begin(static::getDB());

			if(!empty($bankAccount['typeId'])){
				static::getDB()->update(
					self::$TABLE_BRANCH_HAS_BANK_ACCOUNT,
					array('is_current' => '0'),
					array(
						'bank_account_type	= ?' => $bankAccount['typeId'],
						'branch_id			= ?' => $bankAccount['branchId']
					)
				);
			}


			$data = array(
				'account_id'		=>'',
				'bank_name'			=> $bankAccount['bankName'],
				'bank_branch'		=> $bankAccount['bankBranch'],
				'account_prefix'	=> $bankAccount['accountPrefix'],
				'account_number'	=> $bankAccount['accountNumber'],
				'bank_code'			=> $bankAccount['bankCode'],
				'specific_symbol'	=> NULL,
				'currency'			=> $bankAccount['currency'],
				'note'				=> $bankAccount['note']
			);
			static::getDb()->insert(
				self::$TABLE,
				$data
			);
			$account_id = static::getDB()->lastInsertId();


			$data = array(
				'branch_id'			=> $bankAccount['branchId'],
				'bank_account_id'	=> $account_id,
				'bank_account_type'	=> $bankAccount['typeId'],
				'is_current'		=> 1,
				'time_created' 		=> strftime('%Y-%m-%d %H:%M:%S')
			);
			static::getDB()->insert(
				self::$TABLE_BRANCH_HAS_BANK_ACCOUNT,
				$data
			);

			It6_DbTransaction::commit(static::getDB());

			//FIXME melo by vracet lastInsertId
			return true;
		}

		catch ( Exception $e ) {
			It6_DbTransaction::rollback(static::getDB());
			throw new It6_XmlRpc_Exception("Can not update bank account.", 0, $e);
		}
	}



	/**
	 * Returns the bank accounts for particuler branch
	 * @param array $branchId identifier of the branch
	 * @return array with elements [provisions],[balance] and [archive] populated by the respective accounts
	 */
	public static function getByBranch($branchId){

		try {
			$entity = static::defaultQuery(static::getDb()->select())

				->joinLeft(
					array('bhba' => self::$TABLE_BRANCH_HAS_BANK_ACCOUNT),
					'a.account_id=bhba.bank_account_id'
				)
				->joinLeft(
					array('cur' => Webservice_Currency::$TABLE),
					'a.currency = cur.mena_id'
				)
				->joinLeft(
					array('bat' => Webservice_BankAccountType::$TABLE),
					'bhba.bank_account_type = bat.type_id'
				)
				->where('bhba.branch_id=?', $branchId)
				->order(array('bhba.time_created DESC'))
				->query()->fetchAll();

		return static::toEntities($entity);

		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception("getByBranch (Entity: '".get_called_class()."', Id: '$id')", 0, $e);
		}
	}




	protected static function defaultJoins($query) {
		$query = parent::defaultJoins($query);
		$query = $query->joinLeft(
					array('b' => self::$TABLE_BRANCH_HAS_BANK_ACCOUNT),
					static::$TABLE_PREFIX . '.account_id = b.bank_account_id', null);
		return $query->joinLeft(
					array('u' => Webservice_Currency::$TABLE),
					static::$TABLE_PREFIX . '.currency = u.mena_id', null);
	}

}
