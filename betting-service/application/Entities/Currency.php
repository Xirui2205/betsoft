<?php

/**
 * Branch type dao.
 * @author Martin Bohal
 * @see Webservice_BranchType
 */
class Entities_Currency extends Entities_AbstractEntity {
	
	/**
	 * Unique identifier of the currency
	 * @var integer
	 */
	public $currencyId;

	/**
	 * Iso number of the currency
	 * @var string
	 */
	public $iso;
	
	/**
	 * Name of the currency
	 * @var string
	 */
	public $name;
	
	/**
	 * Info about the currency
	 * @var string
	 */
	public $description;
	
	
	/**
	 * Current rate to system currency
	 * @var float
	 */
	public $rate;
	
	
	/**
	 * Is currency used like system currency?
	 * @var boolean
	 */
	public $isSystemCurrency;
}
