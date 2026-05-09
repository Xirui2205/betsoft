<?php
/**
 * TicketTip dao.
 * @author Pavel Klinger
 * @see Entities_Ticket
 */
class Entities_TicketTip extends Entities_AbstractEntity {
	
	/**
	 * Column id of the odds. 
	 * @var integer
	 * @see Entities_OddsOutcome
	 */
	public $oddsOutcomeId;
	
	/**
	 * Column name of the odds. 
	 * @var string
	 * @see Entities_OddsOutcome
	 */
	public $oddsOutcomeName;
	
	/**
	 * Column shortcut of the odds. 
	 * @var string
	 * @see Entities_OddsOutcome
	 */
	public $oddsOutcomeShortCut;	
	
	/**
	 * Type of odds
	 */
	public $oddsTypeName;
	
	/**
	 * @var Integer
	 */
	public $betId;
	
	/**
	 * @var String
	 */
	public $betName;
	
	/**
	 * @var integer
	 */
	public $ako;
	
	/**
	 *  
	 * @var float
	 */
	public $rate;
	
	/**
	 * Number of the group
	 * @var integer
	 */
	public $group;
	
	/**
	 * Won amount directly on this tip
	 * @var integer
	 */
	public $won;
	
	/**
	 * Amount directly on this tip	 
	 */
	public $amount;

	/**
	 * Bet is valid to this datetime
	 * @var string
	 */
	public $validTo;

	/**
	 * Bet alias (NNNN/NN)
	 * @var string
	 */
	public $alias;

	/**
	 * Bet text note (eg. 'goals' for over/under and fotbal)
	 * @var string|NULL
	 */
	public $betTextNote;
	
	/**
	 * Order of the tip on the ticket
	 */
	public $order;
}