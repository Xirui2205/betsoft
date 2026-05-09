<?php
/** 
 * @author Pavel Klinger
 * @see Entities_Ticket#validate
 */
class Entities_TicketValidationError extends Entities_AbstractEntity {
	/**
	 * Field concernig error (eg. name of field or bet ID)
	 * @var mixed
	 */
	public $field;
	
	/**
	 * Field extended specification (eg. column for bet)
	 * @var mixed|NULL
	 */
	public $fieldSpec;

	/**
	 * Number of the error
	 * @var integer
	 */
	public $error;
	
	/**
	 * Error message
	 * @var string
	 */
	public $errorMessage;

	/**
	 * Parameter if message is to be parameterized
	 * @var mixed
	 */
	public $errorMessageParam;
	
	/**
	 *  Recommended valuue of the wrong valued field 
	 * @var unknown_type
	 */
	public $recommendedValue;

	/**
	 * @var string|NULL 'error' (default) or 'warning'
	 */
	public $type;

	/**
     * @var array|NULL array('name' => string, ... other action specific parameters ...)
	 */
	public $actions;
}
