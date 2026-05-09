<?php

/**
 * Region dao.
 * @author Martin Bohal
 * @see Webservice_BankAccount
 */
class Entities_BankAccount extends Entities_AbstractEntity {
	
	/**
	 * specifies if the account is in use
	 * @var int
	 */
	public $isCurrent;
	
	
	/**
	 * name of the bank
	 * @var string
	 */
	public $bankName;
	
	
	/**
	 * name of the bank branch
	 * @var string
	 */
	public $bankBranch;
	
	
	/**
	 * name of the bank branch
	 * @var string
	 */
	public $accountPrefix;
	
	
	/**
	 * name of the bank branch
	 * @var string
	 */
	public $accountNumber;
	
	
	/**
	 * name of the bank branch
	 * @var string
	 */
	public $bankCode;
	
	
	/**
	 * name of the bank branch
	 * @var string
	 */
	public $specSymbol;
	
	//FIXME to by melo bejt navazany na entitu currency
	/**
	 * name of the bank branch
	 * @var string
	 */
	public $currency;
	
	/**
	 * name of the bank branch
	 * @var string
	 */
	
	public $currencyIso;
	/**
	 * name of the bank branch
	 * @var string
	 */
	public $note;

	/**
	 * Datetime when created.
	 * @var string
	 */
	
	public $timeCreated;
}
