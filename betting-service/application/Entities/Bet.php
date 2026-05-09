<?php

/**
 * Bet dao.
 * @author Pavel Klinger
 * @see Webservice_Bet
 * 
 */
class Entities_Bet extends Entities_AbstractEntity {
	
	/**
	 * Unique identifier of the bet
	 * @var integer
	 */
	public $betId;
	
	
	/**
	 * If this bet is child returns identifier ogf the (parent) main bet. 
	 * otherwise null 
	 * @var itenteger Identifier of the main bet
	 */
	public $mainBet;
	
	/**
	 * Human readable identifier of the bet 
	 * @var integer
	 */
	public $alias;
	
	/**
	 * Bet is valid from
	 * @var datetime
	 */
	public $validFromTime;
	
	/**
	 * Bet is valid to time
	 * @var datetime
	 */
	public $validToTime;
	
	/**
	 * ???
	 * @var integer
	 */
	public $status;
	
	/**
	 * True if bet is live;
	 * @var boolean
	 */
	public $live;
	
	/**
	 * Idnetifier of the bookmaker who set the bet 
	 * @var integer
	 * @see Entities_Bookmaker 
	 */
	public $bookmakerId;

	/**
	 * Identifier of the bet event 
	 * @var integer
	 * @see Entities_Event
	 */
	public $eventId;
	
	/**
	 * Identifier of the bet sport
	 * @var integer
	 */
	public $sportId;
	
	/**
	 * Identifier of the bet region
	 * @var integer
	 */
	public $regionId;
	
	/**
	 * Identifier of the bet type
	 * @var integer
	 * @see Entities_BetType
	 */
	public $typeId;
	
	/**
	 * Identifier of the bet odds type
	 * @var integer
	 * @see Entities_BetOddsType
	 */
	public $oddsTypeId;
	
	/**
	 * ????
	 * @var integer
	 */
	public $verified;
	
	/**
	 * Name of the bet
	 * @var string	 
	 */
	public $name;

	/**
	 * Name of the bet to display on the ticket (shorter name)
	 * @var string	 
	 */
	public $ticketName;
	
	/**
	 * ????
	 * @var boolean
	 */
	public $simple;
	
	/**
	 * ????!!!!
	 * @var int (?string)
	 */
	public $result;

	/**
	 * True if bet was paid out
	 * @var boolean
	 */
	public $paidOut; 

	/**
	 * Identifier of the bookmaker who paid out the bet 
	 * @var int
	 */
	public $paidOutByBookmakerId;

	/**
	 * @var boolean
	 */
	public $canceled;

	/**
	 * Identifier of the bookmaker who canceled the bet 
	 * @var integer
	 */
	public $canceledByBookmakerId;

	/**
	 * @var string
	 */
	public $cancelationReason;

	/**
	 * Over this limit ticket bet has to be verified by bookmaker (???) 
	 * @var int
	 */
	public $riskLimit;
	
	/**
	 * ????
	 * @var float
	 */
	public $riskLimitBalance;
	
	
	/**
	 * ?????
	 * @var String
	 */
	public $info;
	
	/**
	 * ????
	 * @var String
	 */
	public $message;
	
	/**
	 * ????
	 * @var int
	 */
	public $ako;
		
	/**
	 * Array of bet odds;
	 * @var array  
	 * @see Entities_BetOdds
	 */
	public $odds;
		
	/**
	 * @var integer
	 */	
	public $parentId;

	/**
	 * @var integer
	 */	
	public $betradarMatchId;
	
	/**
	 * @var boolean
	 */	
	public $releaseAlias;
}
