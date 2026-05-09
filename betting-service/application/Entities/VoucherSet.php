<?php

/**
 * Voucher dao.
 * @author Pavel Klinger
 * @see Webservice_Voucher
 *
 */
class Entities_VoucherSet extends Entities_AbstractEntity {

	/**
	 * Unique identifier of the voucher set
	 * @var integer
	 */
	public $voucherSetId;

	/**
	 * Voucher set name
	 * @var string
	 */
	public $name;
	
	/** 
	 * Time valid to.
	 * @var string
	 */
	public $validTo;
	
	/**
	 * Time valid from.
	 * @var string
	 */
	public $validFrom;
	
	
	/** 
	 * Count of voutchers from one set that can use one user, zero means unlimited.
	 * @var integer
	 */
	public $multiuse;
	
	/**
	 * Size of the generated handle
	 * @var integer
	 */
	public $handleSize;

}
