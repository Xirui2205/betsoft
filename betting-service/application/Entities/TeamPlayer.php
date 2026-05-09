<?php

/**
 * Team player.
 * @author Tomas Polz
 * @see Webservice_TeamPlayer
 * 
 */
class Entities_TeamPlayer extends Entities_AbstractEntity {
	
	/**
	 * Unique identifier of the team player
	 * @var integer
	 */
	public $teamPlayerId;
	
	/**
	 * realation to team of the player
	 * @var integer
	 */
	public $teamId;
			
	
	/**
	 * Name of the player 
	 * @var string	 
	 */
	public $name;
	
}