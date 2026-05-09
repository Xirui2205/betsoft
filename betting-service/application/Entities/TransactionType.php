<?php

/**
 * TransactionType dao.
 * @author Pavel Klinger
 * @see Webservice_Transaction
 * 
 */
class Entities_TransactionType extends Entities_AbstractEntity {

	/**
	 * Unique identifier of the transaction type
	 * @var integer
	 */
	public $transactionTypeId;

	/**
	 * @var string	 
	 */
	public $name;

	/**
	 * @var string
	 */
	public $note;

	/**
	 * @var string
	 */
	public $from;

	/**
	 * @var string
	 */
	public $thru;

	/**
	 * @var string
	 */
	public $to;

	/**
	 * @var boolean
	 */
	public $needConfirm;

	/**
	 * @var boolean
	 */
	public $debiting;
	
	/**
	 * 1..branch, 2..user
	 * @var integer
	 */
	public $accountType;
	
	/**
	 * @var float
	 */
	public $highLimit;
	
	/**
	 * @var float
	 */
	public $lowLimit;

	/**
	 * If transaction influences bonus
	 * @var boolean
	 */
	public $changingBonus;

	/**
	 * First or later status which should change balance
	 * Subset of Transaction.status (without failure statuses).
	 * @var string
	 */
	public $statusChangingBalance;

	/**
	 * First or later status which should used for accounting
	 * Subset of Transaction.status (without failure statuses).
	 * @var string
	 */
	public $statusForAccounting;

}