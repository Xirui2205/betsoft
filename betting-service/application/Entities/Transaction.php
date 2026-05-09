<?php

/**
 * Transaction dao.
 * @author Pavel Klinger
 * @see Webservice_Transaction
 * 
 */
class Entities_Transaction extends Entities_AbstractEntity {
	
	/**
	 * Unique identifier of the transaction
	 * @var integer
	 */
	public $transactionId;

	/**
	 * Per user transaction handle
	 */
	public $handle;
	
	/**
	 * Cancel transaction ID, not null only for storno transactions
	 * @var integer
	 */
	public $cancelTransactionId;


	/**
	 * Idnetifier of the transaction user 
	 * @var integer
	 * @see Entities_User 
	 */
	public $userId;

	/**
	 * Identifier of the host where the transaction was done 
	 * @var integer
	 * @see Entities_Branch
	 */
	public $hostId;
	
	/**
	 * Identifier of the ticket joined with transaction 
	 * @var integer
	 * @see Entities_Ticket
	 */
	public $ticketId;
	
	/**
	 * Value of the transaction 
	 * @var float	 
	 */
	public $value;
	
	/** 
	 * @var float	 
	 */
	public $balance;
		
	
	/**
	 * @var integer
	 */
	public $typeId;
	
	/**
	 * Transaction time
	 * @var Datetime
	 */
	public $time;
	
	/**
	 * Transaction notes
	 * @var string	 
	 */
	public $notes;
	
	
	/**
	 * Currency of the transaction
	 * @var integer
	 * @see Entities_Currency
	 */
	public $currencyId;

	/**
	 * @var string
	 */
	public $currencyCode;

	/**
	 * Date of export, can be empty
	 * @var datetime
	 */
	public $exportDate;

	/**
	 * ID of admin who initially created the transaction
	 * @var integer
	 */
	public $createAdminId;

	/**
	 * ID of admin who confirmed/canceled the transaction
	 * @var integer
	 */
	public $confirmAdminId;

	/**
	 * ID of admin who finished/canceled transaction deposit
	 * @var integer
	 */
	public $depositAdminId;

	/**
	 * Name of admin who initially created the transaction
	 * @var integer
	 */
	public $createAdmin;

	/**
	 * Name of admin who confirmed/canceled the transaction
	 * @var integer
	 */
	public $confirmAdmin;

	/**
	 * Name of admin who finished/canceled transaction deposit
	 * @var integer
	 */
	public $depositAdmin;

	/**
	 * TRUE if transaction has already changed balance (to avoid double changes)
	 * @var boolean
	 */
	public $balanceChanged;
}
