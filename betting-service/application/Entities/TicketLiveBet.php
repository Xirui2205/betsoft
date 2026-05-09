<?php

/**
 * LiveTicket dao
 * @author Pavel Klinger
 * @see Webservice_Livebetting
 */
class Entities_TicketLiveBet extends Entities_AbstractEntity {
	
	/**
	 * Unique identifier of the bet
	 * @var integer
	 */
	public $id;

	/** 
	 * @var string
	 */
	public $players;

	/**
	 * @var string
	 */
	public $sport;
	
	/**
	 * @var string
	 */
	public $event;
	
	/**
	 * @var string
	 */
	public $region;

	/**
	 * @var float
	 */
	public $rate;

	/** 
	 * @var tip
	 */
	public $tip;

	/**
	 * @var boolean
	 */
	public $canceled;
	
	/**
	 * @var string
	 */
	public $result;
	
	/**
	 * @var boolean
	 */
	public $resultValid;


}
