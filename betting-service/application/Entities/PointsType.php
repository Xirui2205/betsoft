<?php

/**
 * PointsType dao.
 * @author Pavel Klinger
 * @see Webservice_PointsTransaction
 * 
 */
class Entities_PointsType extends Entities_AbstractEntity {

	/**
	 * Unique identifier of the points type
	 * @var integer
	 */
	public $pointsTypeId;

	/**
	 * @var string	 
	 */
	public $name;
	
	/**
	 * @var float
	 */
	public $rate;
}