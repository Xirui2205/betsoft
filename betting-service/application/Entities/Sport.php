<?php

/**
 * Sport dao.
 * @author Pavel Klinger
 * @see Webservice_Sport
 * 
 */
class Entities_Sport extends Entities_AbstractEntity {
	
	/**
	 * Unique identifier of the sport.
	 * @var integer
	 */
	public $sportId;
	
	/**
	 * Human readable identifier of the sport 
	 * @var string
	 */
	public $handle;
	
	/**
	 * Sport name.
	 * @var integer
	 */
	public $name;
	
	/**
	 * By this variable are entities sorted in the navigation.
	 * @var integer
	 * @see Entities_Sport::$navigationVisible
	 * @see Entities_Sport::$navigationHightlight
	 */
	public $navigationOrder;
			
	
	/**
	 * If true spot is highlighted in the navigation. ???
	 * @var boolean
	 * @see Entities_Sport::$navigationOrder
	 * @see Entities_Sport::$navigationVisible	 
	 */
	public $navigationHighlight;
	
	/**
	 * If true sport is visible in the navigation. ???
	 * @var boolean
	 * @see Entities_Sport::$navigationOrder
	 * @see Entities_Sport::$navigationHihghlight	 
	 */
	public $navigationVisible;
	
	/**
	 * Betradar identifier of this sport.
	 * @var integer
	 */
	public $betradarId;
	
	/**
	 * If true the bets related to this sport can be live. ???
	 * @var boolean
	 */
	public $live;

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
