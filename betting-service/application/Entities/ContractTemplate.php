<?php

/**
 * ContractTemplate dao.
 * @author Pavel Klinger
 * @see Webservice_ContractTemplate
 * 
 */
class Entities_ContractTemplate extends Entities_AbstractEntity {
	
	/**
	 * Unique identifier of the contract template.
	 * @var integer
	 */
	public $id;
	
	/**	 
	 * @var string
	 */
	public $name;
	
	
	/**	 
	 * @var array array of Entities_Parameter structures
	 */
	public $parameters;	
	
}