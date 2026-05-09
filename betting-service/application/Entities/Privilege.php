<?php

/**
 * Bet dao.
 * @author Pavel Klinger
 * @see Webservice_Bet
 * 
 */
class Entities_Privilege extends Entities_AbstractEntity {
	
	/**
	 * Unique identifier of the bet
	 * @var integer
	 */
	public $privilegeId;
	
	public $bookmakerId;
	
	public $usecase;
}