<?php

/**
 * Voucher related static methods.
 * @author PavelKlinger
 * @see Entities_Voucher
 *
 */
class Webservice_Voucher extends Webservice_AbstractWebService  {
	const VOUCHER_TRANSACTION_TYPE = 14;

	public static $TABLE					= "voucher";
	public static $TABLE_PREFIX				= "vch";
	public static $ENTITY_NAME				= "Entities_Voucher";
	public static $IDENTITY					= "id";


	protected static $CONV = array(
		'vch.id'			   => 'voucherId',
		'voucher_set_id'       => 'setId',
		'vchs.name'            => 'setName',
		'vch.handle'		   => 'handle',
		'vch.created'          => 'created',
		'vch.valid_to'         => 'validTo',
		'vch.valid_from'       => 'validFrom',
		'used'                 => 'used',
		'used_time'            => 'usedTime',
		'amount'               => 'amount',
		'point_type_id'        => 'pointTypeId',
		'vch.user_id'          => 'userId',
		'u.nick'               => 'userNick',
		'point_transaction_id' => 'pointTransactionId',
		'vchs.multiuse'        => 'multiuse'
		
	);

	protected static function defaultJoins($query) {
		return parent::defaultJoins($query)		
			->joinLeft(
				array('u' => 'uzivatel'),
				'u.user_id = vch.user_id',
				null
			)
			->joinLeft(
				array('vchs' => 'voucher_set'),
				'vchs.id = vch.voucher_set_id',
				null
			);
	}
	
	/**
	 * Returns all vouchers.
	 * @return struct object of the currency structures
	 * @see Entities_Voucher
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}	

	/**
	 * Find voucher by given id.
	 * @param integer $voucherId identifier of the voucher
	 * @return struct voucher structure
	 * @see Entities_Voucher
	 */
	public static function getById($voucherId, $extensions = null) {		
		return parent::getById($voucherId, $extensions);
	}
	
	/**
	 * Find voucher by given handle.
	 * @param integer $handle handle of the voucher
	 * @return struct voucher structure
	 * @see Entities_Voucher
	 */
	public static function getByHandle($handle, $extensions = null) {
		return static::getOneBy($handle, 'handle', $extensions);
	}
	
	
	/**
	 * Insert new voucher. I not set handle handle is generated.
	 * @param struct $voucher structure of the voucher
	 * @return integer voucher identifier of the created voucher
	 * @see Entities_Voucher
	 */
	public static function insert($voucher) {
		if ( empty($voucher['handle']) ) {
			if ( !empty($voucher['setId']) ) {
				$voucherSet = Webservice_VoucherSet::getById($voucher['setId']);
				$size = $voucherSet['handleSize'];
			}	
			else 
				$size = 16;
			$voucher['handle'] = static::_generateHandle(16);
			
		}
		
		if ( empty($voucher['validTo']) )
			throw new It6_XmlRpc_Exception("Can't insert voucher. Missing 'validTo' time.");
		
		if ( empty($voucher['amount']) )
			throw new It6_XmlRpc_Exception("Can't insert voucher. Missing 'amount'");
		
		$voucher['created'] = It6_Date::dbNow();
		if ( empty($voucher['validFrom']) ) {
			$voucher['validFrom'] = $voucher['created'];
		}
		
		if ( empty($voucher['pointTypeId']) ) {
			$voucher['pointTypeId'] = Webservice_PointsType::DEFAULT_POINT_TYPE_ID;
		}
		
		$voucher['used'] = false;
		unset($voucher['usedTime']);
		unset($voucher['userId']);
		unset($voucher['pointTransactionId']);

		return parent::insert($voucher);
	}
	
	/**
	 * Is voucher valid?
	 * @param string $handle
	 * @param insteger $userId
	 * @return boolean true on valid otherwise exception
	 */
	public static function validate($handle, $userId) {
		$now = It6_Date::dbNow();
		$voucher = static::getOneWhere(array(
					'handle = ?' => $handle,
					'validTo > ?' => $now,
					'validFrom < ?' => $now,	
		));
		
		
		if ( empty($voucher) ) {
			throw new It6_XmlRpc_Exception('Voucher is not valid, or out of date.');	
		}
		
		if ( !empty($voucher['used']) ) {
			throw new It6_XmlRpc_Exception('Voucher is already used.');
		}
		
		if ( !empty($voucher['multiuse']) ) {
			$count = static::getAllWhereCount(array(
				'setId = ?' => $voucher['setId'],
				'userId = ?' => $userId
			));
			if ( $count >= $voucher['multiuse'] ) {
				throw new It6_XmlRpc_Exception('Voucher set multiuse overflow.');
			}
		}
		
		return true;
	}
	
	/**
	 * Use voacher for given user
	 * @param string $handle
	 * @param integer $userId
	 * @return boolean|integer If of transaction on success, False if voucher doesn't exists or is not valid
	 */
	public static function useVoucher($handle, $userId) {
		
		try {
			static::validate($handle, $userId);
		}
		catch( Exception $e ) {
			return false;
		}

		$voucher = static::getByHandle($handle);
		
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$id = Webservice_PointsTransaction::make(array(
				'userId' => $userId,
				'value'  => $voucher['amount'],
				'typeId' => static::VOUCHER_TRANSACTION_TYPE
			));
			
			$data = array();
			$data['used'] = 1;
			$data['used_time'] = It6_Date::dbNow();
			$data['user_id'] = $userId;
			$data['point_transaction_id'] = $id;

			$db->update(
				static::$TABLE,
				$data,
				array(
					'id = ?' => $voucher['voucherId']
				)
			);
			
			It6_DbTransaction::commit($db);
			return $id;
			
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception('Can not use voacher.',$e);
		}		
	}
	
	protected static function _generateHandle($size) {
		do {			
			$ret = substr(md5('|--'.microtime().'|'.rand().'==|'),0,$size);
			$voucher = static::getByHandle($ret);
		} while ( !empty($voucher) );
		return $ret; 
	}
}
