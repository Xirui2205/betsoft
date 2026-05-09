<?php

/**
 * ContractParameter dao.
 * @author Pavel Klinger
 * @see Webservice_ContractParameter
 * 
 */
class Entities_ContractParameter extends Entities_AbstractEntity {
	
	/**
	 * Unique identifier of the contract parameter.
	 * @var integer
	 */
	public $parameterId;
	
	/**	 
	 * @var string
	 */
	public $name;
	
	/**	 
	 * @var string
	 */
	public $type;
	
	/**	 
	 * @var integer
	 */
	public $templateId;
	
	/**	 
	 * @var boolean
	 */
	public $isMandatory;
	
}
