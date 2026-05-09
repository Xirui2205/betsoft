<?php

/**
 * Transaction related static methods.
 * @author Martin Bohal
 * @see Entities_Bank
 *
 */
class Webservice_Bank extends Webservice_AbstractWebService  {

	public static $TABLE					= "bank";
	public static $TABLE_PREFIX				= "bnk";
	public static $ENTITY_NAME				= "Entities_Bank";
	public static $IDENTITY					= "bank_id";


	protected static $CONV = array(
		'bank_id'		=> 'bankId',
		'bank_name'		=> 'bankName',
		'bank_code'		=> 'bankCode'
	);

	/**
	 * Returns all banks in the system.
	 * @return struct bank structure
	 * @see Entities_Bank
	 */
	public static function getAll($extensions = null) {
		$ret = parent::getAll($extensions);
		$default = new It6_ArrayWrapper(array('bankId' => 0, 'bankName' => '', 'bankCode' => 0));
		array_unshift($ret, $default);
		return $ret;
	}
}
