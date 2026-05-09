<?php

/**
 * ContractParameterValue dao.
 * @author Pavel Klinger
 * @see Webservice_Contract
 * 
 */
class Entities_ContractParameterValue extends Entities_AbstractEntity {
	
	/**
	 * Unique identifier of the contract parameter.
	 * @var integer
	 */
	public $id;
	
	/**	 
	 * @var string
	 */
	public $name;
	
	/**	 
	 * @var string
	 */
	public $value;		
	
	
	/**	 
	 * @var string
	 */
	public $type;
	
}