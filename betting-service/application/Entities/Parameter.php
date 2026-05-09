<?php

/**
 * Parameter dao.
 * @author Filip Vesely
 * @see Webservice_Parameter
 * 
 */
class Entities_Parameter extends Entities_AbstractEntity {
	
	/**
	 * The id of the parameter.
	 * @var integer
	 */
	public $parameterId;
	
	/**
	 * The name of the parameter.
	 * @var string
	 */
	public $name;

	/**
	 * The value of the parameter.
	 * @var string
	 */
	public $value;

	/**
	 * Internal type of the parameter.
	 * @var string
	 */
	public $type;

	/**
	 * Mandatory flag of the parameter.
	 * @var bool
	 */
	public $mandatory;

	/**
	 * Mandatory flag of the parameter.
	 * @var bool
	 */
	public $isDefault;

	/**
	 * description of the parameter.
	 * @var string
	 */
	public $description;
}
