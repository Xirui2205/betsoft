<?php

/**
 * Contract dao.
 * @author Pavel Klinger
 * @see Webservice_Contract
 * 
 */
class Entities_Contract extends Entities_AbstractEntity {
	
	/**
	 * Unique identifier of the contract.
	 * @var integer
	 */
	public $contractId;
	
	/**	 
	 * @var date
	 */
	public $dateValidFrom;
	
	/**	 
	 * @var date
	 */
	public $dateValidTo;
	
	/**	 
	 * @var date
	 */
	public $dateSigned;
	
	/**	 
	 * @var date
	 */
	public $dateCanceled;
	
	/**	 
	 * @var integer
	 */
	public $branchId;
	
	/**
	 * @var array
	 */
	public $parameters;

	/**
	 * @var string
	 */
	public $templateName;
	
	/**
	 * @var string
	 */
	public $templateId;
}
