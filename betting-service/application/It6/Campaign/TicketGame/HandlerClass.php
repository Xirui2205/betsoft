<?php

abstract class It6_Campaign_TicketGame_HandlerClass {

protected $game = null;

/**
 * @param struct $game Ticket game data (eg. gameName)
 */
public function __contruct($game) {
	$this->game = $game;
}

/**
 * Returns handled game structure (initialized in constructor)
 * @return struct Game data
 */
public function getGame() {
	return $this->game;
}

/**
 * Evaluate ticket for ticket game.
 * Method is just delegates to _evaluateTicket().
 * @param It6_Models_Ticket $ticket
 * @param Zend_Db_Adapter $db
 * @return boolean|float FALSE if ticket wasn't used in ticket game,
 *                       numeric evaluation otherwise
 */
public function evaluateTicket($ticket, $db = null) {
	return $this->_evaluateTicket($ticket, $db);
}

/**
 * Worker method called from evaluateTicket().
 * To be overriden in particular game handler class.
 * @param It6_Models_Ticket $ticket
 * @param Zend_Db_Adapter $db
 * @return boolean|float FALSE if ticket wasn't used in ticket game,
 *                       numeric evaluation otherwise
 */
abstract protected function _evaluateTicket($ticket, $db = null);

/**
 * Prepares all data needed for rendering view 
 * @param Zend_View $view
 */
public function prepareView($view) {
	$view->game = $this->game;
}

/**
 * This method returns list of ticket IDs that should be queried
 * when performing game reevaluation. If there are tickets
 * that surely cannot get non-zero evaluation, they don't have
 * to be included (and it is advised to not include them).
 * Override in derived classes.
 * @param Zend_Db_Adapter $db
 * @return array List of ticket IDs that should be reevaluated (can be empty)
 */
abstract public function getTicketsForReevaluation(&$db);

/**
 * Invalidate cached resources in global cache etc.
 * Default implementation is coupled with default saveCache().
 * @param integer $gameId ID of game that is being invalidated
 * @param integer|array|NULL $ticketId One or more ticket IDs that were used in/removed from game, NULL for all
 */
public static function invalidateCache($gameId, $ticketId) {
	It6_GlobalCache_Invalidator::Campaign_ticketGameBox($gameId);
	It6_GlobalCache_Invalidator::Campaign_ticketGameTopTicket($gameId, $ticketId);
}

} // class
