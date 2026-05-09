<?php

/**
 * PointsTransactionType dao.
 * @author Pavel Klinger
 * @see Webservice_Transaction
 * 
 */
class Entities_PointsTransactionType extends Entities_AbstractEntity {

	/**
	 * Unique identifier of the transaction type
	 * @var integer
	 */
	public $pointsTransactionTypeId;

	/**
	 * @var string	 
	 */
	public $name;
	
	/**
	 * @var string	 
	 */
	public $kind;
	
	/**
	 * @var integer
	 */
	public $pointType;

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
	public $debiting;

	
	/**
	 * @var float
	 */
	public $highLimit;
	
	/**
	 * @var float
	 */
	public $lowLimit;
}