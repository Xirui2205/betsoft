<?php

/**
 * Team dao.
 * @author Pavel Klinger
 * @see Webservice_Team
 * 
 */
class Entities_BetHandleRange extends Entities_AbstractEntity {
	
	/**
	 * Unique identifier of the bet handle range
	 * @var integer
	 */
	public $betHandleRangeId;
	
	/**
	 * Unique identifier of the event
	 * @var integer
	 */
	public $eventId;

	/** Unique identifier of the sport
	 * @var integer
	 */
	public $sportId;
	
	/**
	 * Unique identifier of the typ
	 * @var integer
	 */
	public $typId;
	
	/**
	 * Range start
	 * @var integer
	 */
	public $from;
	
	/**
	 * Range end
	 * @var integer
	 */
	public $to;		

}
