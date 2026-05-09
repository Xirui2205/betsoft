<?php

/**
 * BetOdds dao.
 * @author Pavel Klinger
 * 
 */
class Entities_BetOdds extends Entities_AbstractEntity {
	
	/**
	 * Column id of the odds. 
	 * @var integer
	 * @see Entities_OddsOutcome
	 */
	public $oddsOutcomeId;
	
	
	public $oddsOutcomeName;
	
	public $oddsOutcomeShortCut;	
	
	/**
	 *  
	 * @var float
	 */
	public $rate;
		
}