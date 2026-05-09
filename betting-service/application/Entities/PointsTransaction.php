<?php

/**
 * Transaction dao.
 * @author Pavel Klinger
 * @see Webservice_Transaction
 * 
 */
class Entities_PointsTransaction extends Entities_AbstractEntity {
	
		/**
	 * Unique identifier of the transaction
	 * @var integer
	 */
	public $pointsTransactionId;

	/**
	 * Per user transaction handle
	 */
	public $handle;
	
	/**
	 * Idnetifier of the transaction user 
	 * @var integer
	 * @see Entities_User 
	 */
	public $userId;

	/**
	 * Identifier of the branch where the transaction was done 
	 * @var integer
	 * @see Entities_Branch
	 */
	public $branchId;
	
	/**
	 * Identifier of the ticket joined with transaction 
	 * @var integer
	 * @see Entities_Ticket
	 */
	public $ticketId;
	
	
	public $ticketHandle;
	
	public $pointTypeId;
	
	/**
	 * Value of the transaction 
	 * @var float	 
	 */
	public $value;
	
	/** 
	 * @var float	 
	 */
	public $balance;

	public $typeName;
	
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

}