<?php
/**
 * TicketTip dao.
 * @author Pavel Klinger
 * @see Entities_Ticket
 */
class Entities_TicketTipGroup extends Entities_AbstractEntity {
	
	/**
	 * Number of the group 
	 * @var integer	 
	 */
	public $group;
	
	/**
	 * Array of all ticket tips in the group
	 * @var array
	 * @see Entities_TicketTip
	 */
	public $tips;
	
}