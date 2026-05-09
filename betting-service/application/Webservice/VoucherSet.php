<?php

/**
 * VoucherSet related static methods.
 * @author PavelKlinger
 * @see Entities_VoucherSet
 *
 */
class Webservice_VoucherSet extends Webservice_AbstractWebService  {
	const VOUCHER_TRANSACTION_TYPE = 14;

	public static $TABLE					= "voucher_set";
	public static $TABLE_PREFIX				= "vchs";
	public static $ENTITY_NAME				= "Entities_VoucherSet";
	public static $IDENTITY					= "id";


	protected static $CONV = array(
		'id'			       => 'voucherSetId',
		'name'                 => 'name',
		'valid_to'             => 'validTo',
		'valid_from'           => 'validFrom',
		'amount'               => 'amount',
		'count'                => 'count',
		'multiuse'             => 'multiuse',
		'handle_size'          => 'handleSize',		
	);
	
	/**
	 * Returns all voucher sets.
	 * @return struct object of the currency structures
	 * @see Entities_VoucherSet
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}	

	/**
	 * Find voucher set by given id.
	 * @param integer $voucherId identifier of the voucher
	 * @return struct voucher set structure
	 * @see Entities_VoucherSet
	 */
	public static function getById($voucherId, $extensions = null) {		
		return parent::getById($voucherId, $extensions);
	}
	
	/**
	 * Find voucher set by given name.
	 * @param string $name name of the voucher set
	 * @return struct voucher set structure
	 * @see Entities_VoucherSet
	 */
	public static function getByName($handle, $extensions = null) {
		return static::getOneBy($handle, 'name', $extensions);
	}
	
	
	/**
	 * Insert new voucher set. Generates all vouchers 
	 * @param struct $voucher structure of the voucher
	 * @return integer voucher identifier of the created voucher set
	 * @see Entities_VoucherSet
	 */
	public static function insert($voucherSet) {
		$db = static::getDb();
		
		It6_DbTransaction::begin($db);
		try {
			$ret = parent::insert($voucherSet);
			for ( $i = 0; $i < $voucherSet['count']; ++$i ) {
				$ret = Webservice_Voucher::insert(array(
					'setId' => $ret,
					'amount' => $voucherSet['amount'],
					'validFrom' => $voucherSet['validFrom'],
					'validTo' => $voucherSet['validTo'],
				));
			}
			It6_DbTransaction::commit($db);
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw $e;
		}
		
		return $ret;
	}
	
}
