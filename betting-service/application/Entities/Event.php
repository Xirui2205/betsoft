<?php

/**
 * Event dao.
 * @author Pavel Klinger
 * @see Webservice_Event
 * 
 */
class Entities_Event extends Entities_AbstractEntity {
	
	/**
	 * Unique identifier of the event.
	 * @var integer
	 */
	public $eventId;

	/**
	 * Human readable identifier of the event 
	 * @var string
	 */
	public $handle;
	
	/**
	 * Identifier of the sport related to this event.
	 * @var integer
	 * @see Entities_Sport
	 */
	public $sportId;
	
	/**
	 * By this variable are entities sorted in the navigation.
	 * @var integer
	 * @see Entities_Event::$navigationVisible
	 * @see Entities_Event::$navigationHihghlight
	 * @see Entities_Event::$navigationDelimiter	 
	 */
	public $navigationOrder;
	
	/**
	 * If true event is visible in the navigation. ???
	 * @var boolean
	 * @see Entities_Event::$navigationOrder
	 * @see Entities_Event::$navigationHihghlight
	 * @see Entities_Event::$navigationDelimiter
	 */
	public $navigationVisible;
	
	/**
	 * Name of the event.
	 * @var string
	 */
	public $name;
	
	/**
	 * Time since the event is valid. 
	 * @var datetime
	 */
	public $validFromTime;
	
	/**
	 * Time to the event is valid.
	 * @var datetime
	 */
	public $validToTime;
	
	
	/**
	 * If true event is highlighted in the navigation. ???
	 * @var boolean
	 * @see Entities_Event::$navigationOrder
	 * @see Entities_Event::$navigationVisible
	 * @see Entities_Event::$navigationDelimiter
	 */
	public $navigationHighlight;
	
	/**
	 * If true event is delimited in the navigation. ???
	 * @var boolean
	 * @see Entities_Event::$navigationOrder
	 * @see Entities_Event::$navigationVisible
	 * @see Entities_Event::$navigationHighlight
	 */
	public $navigationDelimiter;
	
	/**
	 * Betradar identifiers of this event.
	 * @var array
	 */
	public $betradarId;

	/**
	 * Time offset (in minutes) for betradar "valid to" update of special types.
	 * @var integer
	 */
	public $betradarTimeOffset;

	/**
	 * Identifier of the event related region. 
	 * @var regionId
	 * @see Entities_Region
	 */
	public $regionId;

	/**
	 * Odds approval group ID
	 * @var integer
	 */
	public $approvalGroupId;
	
		/**	 
	 * @var integer
	 */
	public $betAliasFrom;
	
	/**	 
	 * @var integer
	 */
	public $betAliasTo;
}
