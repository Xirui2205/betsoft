<?php
/**
 * User bank account related static methods.
 * @author Martin Bohal
 * @see Entities_UserBankAccount
 */
class Webservice_UserBankAccount extends Webservice_AbstractWebService  {

	public static $TABLE				= "user_bank_account";
	public static $TABLE_BANKS			= "bank";
	public static $TABLE_BANKS_PREFIX	= "bnk";
	public static $TABLE_PREFIX			= "uba";
	public static $ENTITY_NAME			= "Entities_UserBankAccount";
	public static $IDENTITY				= "account_id";

	public static $CONV = array(
		'account_id'		=> 'accountId',
		'account_number'	=> 'accountNumber',
		'account_prefix'	=> 'accountPrefix',
		'bank_id'			=> 'bankId',
		'bnk.bank_name'		=> 'bankName',
		'bnk.bank_code'		=> 'bankCode',
	);



	/**
	 * Insert bank account.
	 * @param struct $bankAccount structure of the account
	 * @return true on success
	 * @see Entities_BankAccount
	 */
	public static function insert($bankAccount) {

		try {
			It6_DbTransaction::begin(static::getDB());

			$data = array(
				'account_id'		=> '',
				'user_id'			=> $bankAccount['userId'],
				'account_prefix'	=> $bankAccount['accountPrefix'],
				'account_number'	=> $bankAccount['accountNumber'],
				'bank_id'			=> $bankAccount['bankId'],
				'is_current'		=> '1'
			);
			static::getDb()->insert(
				self::$TABLE,
				$data
			);


			It6_DbTransaction::commit(static::getDB());
			return true;
		}

		catch ( Exception $e ) {
			It6_DbTransaction::rollback(static::getDB());
			throw new It6_XmlRpc_Exception("Can not update user bank account.", 0, $e);
		}
	}



	/**
	 * Returns the bank accounts for a particuler user
	 * @param integer $userId id of the user
	 * @return array populated by the respective account info
	 */
	public static function getByUserId($userId){

		try {
			$entity = static::defaultQuery(static::getDb()->select())
				->where(self::$TABLE_PREFIX.'.user_id = ?', $userId)
				->where(self::$TABLE_PREFIX.'.is_current = ?', true)
				->query()->fetch();

			return static::toEntity($entity);
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception("getByUser (Entity: '".get_called_class()."', Id: '$id')", 0, $e);
		}
	}



	protected static function defaultJoins($query) {
		$query = parent::defaultJoins($query);
		$query = $query->joinLeft(
			array(self::$TABLE_BANKS_PREFIX => self::$TABLE_BANKS),
			self::$TABLE_PREFIX.'.bank_id = '.self::$TABLE_BANKS_PREFIX.'.bank_id',
			null
		);
		return $query;
	}
}
