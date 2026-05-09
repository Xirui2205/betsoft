<?php

/**
 * Voucher dao.
 * @author Pavel Klinger
 * @see Webservice_Voucher
 *
 */
class Entities_Voucher extends Entities_AbstractEntity {

	/**
	 * Unique identifier of the voucher
	 * @var integer
	 */
	public $voucherId;

	/**
	 * Voucher code
	 * @var string
	 */
	public $handle;
	
	/**
	 * Time created.
	 * @var string
	 */
	public $created;
	
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
	 * True on used voucher otherwise false
	 * @var boolean
	 */
	public $used;
	
	/**
	 * Time whan the voucher was used
	 * @var String
	 */
	public $usedTime;
	
	/**	 
	 * @var float
	 */
	public $amount;
	
	/** 
	 * @var integer
	 */
	public $pointTypeId;
	
	/**
	 * Identfier of the user who has the woucher (null when not used)
	 * @var integer
	 */
	public $userId;
	
	/** 
	 * Identifier of the point transaction ((null when not used)
	 * @var integer
	 */
	public $pointTransactionId;

}
