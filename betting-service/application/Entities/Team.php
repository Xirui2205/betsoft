<?php

/**
 * Team dao.
 * @author Pavel Klinger
 * @see Webservice_Team
 * 
 */
class Entities_Team extends Entities_AbstractEntity {
	
	/**
	 * Unique identifier of the team
	 * @var integer
	 */
	public $teamId;
	
	/**
	 * Betradar identifier of the team
	 * @var integer
	 */
	public $betradarId;
			
	/**
	 * Identifier of the sport
	 * @var integer
	 */
	public $sportId;
	
	/**
	 * Name of the team 
	 * @var string	 
	 */
	public $name;
	
	/**
	 * Shortcut name of the team 
	 * @var string
	 */
	public $shortName;

}