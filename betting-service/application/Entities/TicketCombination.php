<?php
/**
 * TicketTip dao.
 * @author Pavel Klinger
 * @see Entities_Ticket
 */
class Entities_TicketCombination extends Entities_AbstractEntity {
	
	/**
	 * k tuple of n 
	 * @var integer	 
	 */
	public $k;
	
	/**
	 * Stake on this combination
	 * @var float	 
	 */
	public $amount;

	/**
	 * IT6: combination data: disabled
	 * Additional data for each row in group
	 * @var array of struct
	 */
	//public $rows;
}