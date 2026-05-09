<?php

class Entities_TicketInsertResult {
	
	/**
	 * Identifier of the inserted ticket
	 * @var integer
	 */
	public $tickedId;
	
	/**
	 * False if ticked doesn;t need authorisation otherwise true.
	 * @var boolean
	 */	
	public $waitingForTHeAutorisation;
}
