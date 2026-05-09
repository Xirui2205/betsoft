<?php

/**
 * LiveTicket dao
 * @author Pavel Klinger
 * @see Webservice_Livebetting
 */
class Entities_TicketLive extends Entities_AbstractEntity {
	
	/**
	 * Unique internal private identifier of the ticket
	 * @var integer
	 */
	public $id;

	/**
	 * Unique identifier of the ticket
	 * @var integer
	 */
	public $idLive;
	
	/** 
	 * Unique public identifier of the ticket
	 * @var string nine digits
	 */
	public $handle;

	/**
	 * @var float
	 */
	public $stake;

	/**
	 * @var float
	 */
	public $rate;

	/**
	 * @var float
	 */
	public $win;

	/**
	 * One of these values: 'new', 'canceled','paid','resettled'
	 * @var string
	 */
	public $status;
	
	/**
	 * One of these values: 'simple', 'kombi'
	 * @var string
	 */
	public $type;
	
	/**
	 * @var String
	 */
	public $timeCreated;
	
	/**
	 * @var String
	 */
	public $timePaid;
	
	/**
	 * @var String
	 */
	public $timeCanceled;
	
	/**
	 * @var String
	 */
	public $timeResettled;
	
	/**
	 * @var array
	 * @see Entities_TicketBetLive
	 */
	public $bets;

}
