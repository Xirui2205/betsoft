<?php

class It6_Models_Parameter extends It6_Models_Abstract {

const NAME_TICKET_THRESHOLD_SIMPLE = 'confirmation.ticketSimple.lowerTreshold.stake';
const NAME_TICKET_THRESHOLD_COMBI = 'confirmation.ticketCombi.lowerTreshold.stake';
const NAME_TICKET_MINIMAL_STAKE = 'validation.ticket.minimal.stake';
const NAME_TICKET_MINIMAL_STAKE_MAXIKOMBI = 'validation.ticket.minimal.stake.maxikombi';
const NAME_TICKET_MINIMAL_COMBINATION_STAKE = 'validation.ticket.minimal.combination.stake';
const NAME_TICKET_MAXIMAL_STAKE = 'validation.ticket.maximal.stake';
const NAME_TICKET_MAXIMAL_WIN = 'validation.ticket.maximal.win';
const NAME_TICKET_MAXIMAL_WIN_MAXIKOMBI = 'validation.ticket.maximal.win.maxikombi';
const NAME_TICKET_DUPLICATE_COUNT = 'duplicate.Ticket.count';
const NAME_TICKET_CONFIRM_TIME = 'confirmation.ticket.time';
const NAME_TICKET_CONFIRM_TIME_MORE = 'confirmation.ticket.timeMore';
const NAME_TICKET_MAX_BETS_SIMPLE = 'ticket.max.bet.count.simple';
const NAME_TICKET_MAX_BETS_AKO = 'ticket.max.bet.count.ako';
const NAME_TICKET_MAX_BETS_SYSTEM = 'ticket.max.bet.count.system';
const NAME_TICKET_MAX_BETS_MAXIKOMBI = 'ticket.max.bet.count.maxikombi';
const NAME_TICKET_MAX_GROUPS_MAXIKOMBI = 'ticket.max.group.count.maxikombi';
const NAME_TICKET_AKO_MINIMAL_RATE = 'validation.ticket.ako.minimal.rate';
const NAME_TICKET_CONFIRM_AFTER_USER_BET_HISTORY_COUNT = 'confirmation.ticket.user.bet.history.count';
const NAME_TICKET_CONFIRM_AFTER_USER_BET_HISTORY_BALANCE = 'confirmation.ticket.user.bet.history.balance';
const NAME_TICKET_CONFIRM_BET_OVER_RISK_LIMIT_PERCENT = 'confirmation.ticket.bet.risklimit.percent';
const NAME_ENTRY_BONUS_MIN_RATE = 'entry.bonus.minRate';
const NAME_ENTRY_BONUS_MIN_BETS = 'entry.bonus.minBets';
const NAME_ENTRY_BONUS_APPLICABLE_TIME = 'entry.bonus.applicableTime';
const NAME_ENTRY_BONUS_MAX_CHANGE_PER_DAY = 'entry.bonus.maxChangePerDay';
const NAME_ENTRY_BONUS_MAX_TOTAL = 'entry.bonus.maxTotal';
const NAME_ENTRY_BONUS_MIN_TOTAL_STAKES_RATIO = 'entry.bonus.minTotalStakesRatio';
const NAME_BANK_EXPORT_ACCOUNT_KB_USER = 'export.bank.account.kb.user';
const NAME_BANK_EXPORT_ACCOUNT_KB_HOST = 'export.bank.account.kb.host';
const NAME_BETRADAR_BET_BOOKMAKER_ID = 'betradar.Bet.BookmakerId';
const NAME_BETRADAR_QUERIED_LANGUAGE = 'betradar.queriedLanguage';
const NAME_BETRADAR_AUTOUPDATE_DEFAULT = 'betradar.Bet.Autoupdate.default';
const NAME_BETRADAR_EMAIL_RECIPIENTS = 'betradar.Email.notifyTo';
const NAME_BETRADAR_SEND_EMAIL_ON_ERROR = 'betradar.Email.OnError';
const NAME_BETRADAR_SEND_EMAIL_ALWAYS = 'betradar.Email.Always';
const NAME_BRANCH_APP_ALLOWED_VERSIONS = 'branch.allowed.app.versions';
const NAME_REGISTRATION_BONUS_AMOUNT = 'web.registrationBonusAmount';
const NAME_BIRTHDAY_BONUS_AMOUNT = 'web.birthdayBonusAmount';


protected static $_registryEntryDb = 'admindb';
protected static $_cache = array();

/**
 * Database name of table
 * @var string $_table
 */
protected static $_table = 'parameter';

/**
 * Array of that maps object names to database names ( objectName => dbName ).
 * Also it is list of properties oxposed by this class, also column list for SELECT.
 * @var array $_columns
 */
protected static $_columns = array(
	'id' => 'id',
	'name' => 'name',
	'value' => 'value',
	'isHost' => 'is_host',
	'isBranch' => 'is_branch',
	'isUser' => 'is_user',
	'isAdmin' => 'is_admin',
	'type' => 'type',
	'mandatory' => 'mandatory',
	'description' => 'description',
);

/**
 * Object name of primary key column. (default 'id')
 * @var string $_primaryKey
 */
protected static $_primaryKey = 'id';

/**
 * Array of objectName => tableName and objectName => array('table' => tableName, 'column' => dbName).
 * (Second version required when not part of $_columns.)
 * Table names will be dynamically replaced by reserved prefixes.
 * @var array $_joinedColumns
 */
protected static $_joinedColumns = array();

/**
 * Array tableName => joinConstraints.
 * @see addJoin() for description of joinConstraints.
 * @var array $_joinConstraints
 */
protected static $_joinConstraints = array();

// following variables are updated dynamically (use same initialization values if you don't know what are you doing)
protected static $_joinPrefix = false;
protected static $_joinPrefixes = false;
protected static $_readDataModifiers = array(); // see modifySelect()
protected static $_readDataAllModifiers = array();  // see modifySelectAll()
//END: override in derived class if needed (that's most of them)

/**
 * Retrive parameter/s by their name
 * @param string|array $name name or names
 * @param Zend_Db_Adapter $db
 * @return null|array For string name NULL or data, otherwise Array (name => data, ...)
 */
public static function getDataByName($name, &$db = null) {
	return static::getDataByUniqueField($name, 'name', $db);
}

} // class It6_Models_Parameter
