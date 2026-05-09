<?php
include_once(ROOT . 'common/config_util.inc.php');

// application specific includes
set_include_path(
	get_include_path()
	. PATH_SEPARATOR . ROOT . 'betting-service/application'
	. PATH_SEPARATOR . ROOT . 'betting-service/library'
);

if(!defined('MAIN_LOG_ERROR_LEVEL')) define('MAIN_LOG_ERROR_LEVEL', 6);

$GLOBALS['LOG_PREFIX'] = defined('LIVE_CLIENT') ? 'live-betting-service' : 'betting-service';

require_once(ROOT.'common/library/It6/Log.php');
$GLOBALS['LOG_CONFIG'] = array(
	"mainlog" => array(
		'writerParams' => array(
		'stream'   => ROOT.'errorlog/'.$GLOBALS['LOG_PREFIX'].'-%F.log')),
	'muzolog' => array(
		'writerName' => 'Stream',
		'writerNamespace' => 'It6_Log_Writer',
		'writerParams' => array('stream' => ROOT.'errorlog/'.$GLOBALS['LOG_PREFIX'].'-input-%F.log'),
		'filterName' => 'Tag',
		'filterNamespace' => 'It6_Log_Filter',
		'filterParams' => array('tag' => It6_Log::TAG_WEBSERVICE_INPUT)),
	"phpOutput" => array(
		'writerParams' => array(
		'stream'   => ROOT.'errorlog/'.$GLOBALS['LOG_PREFIX'].'-php-%F.log')
	),
);

$GLOBALS['SERVICE_METHODS'] = array(
	'supervisor' => array(
		'Webservice_User' => array('User',
			array('getAll','getBanned','getById','getByHandle','getByLoginName','getByClientCartNumber','getByLoginNameOrCardNumber','getByBranchId','setUserBranchId','insert','update','delete','allowInternet','changeClientCart','getAllTickets','getOpenedTickets','getCanceledTickets','getPaidOutTickets','getAllTransactions','getTransactions','getAllPointsTransactions','getPointsTransactions','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getTable','getIdentity','getConvTable','getWinRatio','changePassword','allowUsers','banUsers','sendAuthorizationSms','canWithdraw','cleanDayIndividualRiskLimits','cleanDayDuplicateTickets','depositCash','withdrawCash','getWithdraws','getBalance','getTransactionsSum','banWithNote','generateClientCardNumbersToFeed','blockClientCard','agreementPrinted')),
		'Webservice_Sport' => array('Sport',
			array('getAll','getById','getByHandle','insert','update','delete','getRelevant','getAllEvents','getRelevantEvents','getEventsInRegion','getRelevantEventsInRegion','getAllRegions','getAllRelevantRegions','getByApprovalGroupId','setApprovalGroupId','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getTable','getIdentity','getConvTable',)),
		'Webservice_Region' => array('Region',
			array('getAll','getRelevant','getById','insert','update','delete','getByHandle','getAllEvents','getRelevantEvents','getEventsBySport','getRelevantEventsBySport','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getTable','getIdentity','getConvTable',)),
		'Webservice_Event' => array('Event',
			array('getAll','getById','getByHandle','insert','update','delete','getRelevantQuery','getRelevant','getAllBets','getOpenedBets','getOpenedMainBets','getClosedBets','getClosedMainBets','getOpenedBetTypes','getOpenedBetTypesTranslated','getOpenedBetsByType','toBetTypeEntities','toBetTypeEntity','setApprovalGroupId','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getTable','getIdentity','getConvTable',)),
		'Webservice_Admin' => array('Admin',
			array('getAll', 'getById','login','changePassword','insert','update','delete','getAllBets','getAllCanceledTickets','getAllPayedOfTickets','getTimesheet','setArrival','setDeparture','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getTable','getIdentity','getConvTable',)),
		'Webservice_Bet' => array('Bet',
			array('getAll','getById','getByAlias','getByIdWithCorrelated','getByAliasWithCorrelated','generateAlias','releaseAliases','releaseAlias','getFullAlias','insert','update','delete','getOpened','getMainOpened','getClosed','getMainClosed','getCorrelatedBets','getChildBets','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getTable','getIdentity','getConvTable','getMinBetTypId','getBetPack','updateBetPackParentId','updateBetParentId','updateBetPackHierarchy','activateBet','cleanDayRiskLimits','setBetScore','getMergedChanges','getMergedChangesAfterId')),
		'Webservice_BetType' => array('BetType',
			array('getAll','getById','insert','update','delete','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getTable','getIdentity','getConvTable',)),
		'Webservice_BetOdds' => array('BetOdds',
			array('getRateChanges',)),
		'Webservice_Ticket' => array('Ticket',
			array('getAll','getById','cloneTicket','getByHandle','insert','calculate','validate','refresh','checkAuthorisationStatus','acceptAutorisedTicket','update','setForcedCollectionHostId','delete','collect','cancelCollect','cancel','getOpened','getCanceled','getPaidOut','getAllBets','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getTable','getIdentity','getConvTable','getStatistics','forfeitNotCollected','getDuplicateTickets','rewnewCanceledBet','cancelBet','allowCancel','cancelAllBets','getIdsByCouponId','recalculateByTip')),
		'Webservice_CronJob' => array('CronJob',
			array('insert','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAll','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getById','getTable','getIdentity','update','delete','getConvTable',)),
		'Webservice_Branch' => array('Branch',
			array('getAll','getById','getIdByHandle','getEmployees','insert','update','delete','getAllHosts','getAllTickets','getOpenedTickets','getOpenedTicketsByUser','getCanceledTickets','getPaidOutTickets','getCollectedTickets','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getTable','getIdentity','getConvTable','getAllWithdrawEnabled','getBanned','getTicketsCount','getProvisions')),
		'Webservice_Parameter' => array('Parameter',
			array('setGlobalParameter','getGlobalParameter','getHostParameter','getBranchParameter','getUserParameter','getAdminParameter','getHostParameters','getByBranchId','getByUserId','getByAdminId','getParametersByUser','getUserParameterNames','getBranchParameterNames','updateDefaultParameters','updateDefaultParameter','updateBranchParameters','updateUserParameters','updateUserParameter','updateBranchParameter','resetBranchParameters','resetBranchParameter','resetUserParameters','resetUserParameter','getParameterIdByName','getDefaultSystemParameters','getDefaultUserParameters','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAll','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getById','insert','getTable','getIdentity','update','delete','getConvTable','getSystemParameterNames','getAllEffectiveParameters')),
		'Webservice_BranchLocation' => array('BranchLocation',
			array('getAll','getAllAsAssociativeArray','getById','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','insert','getTable','getIdentity','update','delete','getConvTable',)),
		'Webservice_HostMessage' => array('HostMessage',
			array('getAll','getById','getNewByHost','setRed','insert')),
		'Webservice_BranchType' => array('BranchType',
			array('getAll','getAllAsAssociativeArray','getById','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','insert','getTable','getIdentity','update','delete','getConvTable',)),
		'Webservice_BankAccount' => array('BankAccount',
			array('insert','update','getByBranch','getTypeById','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAll','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getById','getTable','getIdentity','delete','getConvTable',)),
		'Webservice_Currency' => array('Currency',
			array('getAll','getRate','getAllAsAssociativeArray','getById','insert','update','delete','getAllUsers','getCurrentRate','getRateForDate','getSystem','getSystemId','exchange','exchangeToSystem','exchangeFromSystem','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getTable','getIdentity','getConvTable',)),
		'Webservice_Contract' => array('Contract',
			array('getAll','getById','getByBranch','getOpenByBranch','getSignedButNotActiveByBranch','getActiveByBranch','getExpiredByBranch','getCanceledByBranch','insert','update','delete','toEntity','getDefaultParameters','resetContractParameters','resetContractParameter','fromEntity','fetchAllEntities','toEntities','fromEntities','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getTable','getIdentity','getConvTable',)),
		'Webservice_ContractTemplate' => array('ContractTemplate',
			array('getAll','getById','insert','update','delete','toEntity','getParamsById','fromEntity','fetchAllEntities','toEntities','fromEntities','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getTable','getIdentity','getConvTable',)),
		'Webservice_Host' => array('Host',
			array('getConvTable','getAll','getById','getByBranchId','getAllTransactions','getTransactions','insert','update','delete','getAllowed','getOnline','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getTable','getIdentity','isAllowed','isInAllowed','isOutAllowed','ban','banIn','banOut','allowIn','allowOut','allow','banAll','banAllIn','banAllOut','getDeposits','getWithdraws','deposit','withdraw','getBalance','getBalancing','getTransactionsSum','getNotWithdrawedCash','selfWithdraw','setFingerprint','getInOut','getResourcesOnTheWay')),
		'Webservice_Timesheet' => array('Timesheet',
			array('toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAll','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getById','insert','getTable','getIdentity','update','delete','getConvTable',)),
		'Webservice_ApprovalGroup' => array('ApprovalGroup',
			array('getAll','getById','getAllRelevant','insert','update','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getTable','getIdentity','delete','getConvTable',)),
		'Webservice_ApprovalGroupThreshold' => array('ApprovalGroupThreshold',
			array('getByGroupId','insert','update','delete','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAll','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getById','getTable','getIdentity','getConvTable',)),
		'Webservice_TransactionType' => array('TransactionType',
			array('getByGroupId','getByIdAndCurrency','insert','update','delete','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAll','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getById','getByName','getTable','getIdentity','getConvTable',)),
		'Webservice_Transaction' => array('Transaction',
			array('getAll','getAllOk','getAllCanceled','getAllPending','getAllPreDeposit','getAllNoDeposit','getById','findSimilar','make','cancelTransaction','confirm','cancel','deposit','getDayBook','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getTable','getIdentity','getConvTable','getAllHistory','getAllForExport','setExported','getCashBook',)),
		'Webservice_PointsTransactionType' => array('PointsTransactionType',
			array('getByGroupId','insert','update','delete','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAll','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getById','getByName','getTable','getIdentity','getConvTable',)),
		'Webservice_PointsType' => array('PointsType',
			array('insert','update','delete','getRate','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAll','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getById','getByName','getTable','getIdentity','getConvTable','exchangeToMoney','exchangeFromMoney',)),
		'Webservice_PointsTransaction' => array('PointsTransaction',
			array('getAll','getById','make','findSimilar','createPointAccounts','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getTable','getIdentity','getConvTable','getMoneyTicketCreateTransaction','getValueLimits',)),
		'Webservice_Team' => array('Team',
			array('getAll','getBySport','getByEvent','getById','insert','update','delete','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getTable','getIdentity','getConvTable',)),
		'Webservice_BetHandleRange' => array('BetHandleRange',
			array('getAll','getByEvent','getById','insert','update','delete','toEntity','fromEntity','fetchAllEntities','toEntities','fromEntities','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns','getTable','getIdentity','getConvTable',)),
		'Webservice_Log' => array('Log',
			array('getAll','getById','getAllWhere','getAllOrder','getAllColumns','getAllWhereOrder','getAllWhereOrderColumns',)),
		'Webservice_Alert' => array('Alert',
			array('assert','check',)),
		'Webservice_Campaign' => array('Campaign',
			array('putOn','validate','get','getPreferenceRateValidSizes','validatePointTicket')),
		'Webservice_BankAccountType' => array('BankAccountType',
			array('getAll','getById',)),
		'Webservice_UserFinance' => array('UserFinance',
			array('getAll','getById','getAllColumns')),
		'Webservice_UserNote' => array('UserNote',
			array('getAll','getById','getByUserId')),
		'Webservice_Language' => array('Language',
			array('getAll','getById','getAllActive','insert','update','delete','getAllUsers')),
		'Webservice_Country' => array('Country',
			array('getAll','getById',)),
		'Webservice_AccessController' => array('AccessController',
			array('getAllPrivileges', 'getIdentity')),
		'Webservice_UserBankAccount' => array('UserBankAccount',
			array('getByUser','insert')),
		'Webservice_Bank' => array('Bank',
			array('getAll')),
		'Webservice_Banner' => array('Banner',
			array('getByControllerAndLanguage','getById','update','insert')),
		'Webservice_Session' => array('Session',
			array('cleanUp', 'getByLiveSession')),
		'Webservice_Livebetting' => array('Livebetting',
			array('getAll','getAllWhere','getById','createTicket','changeTicketStatus','cancelTicket','winingTicket','renewCanceledTicket','payoutTicket','resettleTicket','cancelBets','renewCanceledBets','invalidateBetResult','setBetResult','isTransactionAlreadyDone','getUserStatus','setMatches','deleteMatch','insertMatch','updateMatch','getById','getByHandle','getByLiveId','getBets')),
		'Webservice_MatchLive' => array('MatchLive',
			array('getAll','getById')),
		'Webservice_UserTracking' => array('UserTracking',
			array('getAllByUserId','getMatchedTracks')),
		'Webservice_TicketStatistics' => array('TicketStatistics',
			array('creation','cancelation','payingout','payoutCancelation')),
		'Webservice_LivebettingRaw' => array('LivebettingRow',
			array('getStatus','setMatches','reduceBalance','setTicketStatus')),
		'Webservice_Coupon' => array('Coupon',
			array('requestCancelation','isCancelationRequested')),
		'Webservice_HappyHour' => array('HappyHour',
			array('generate','isRunningNow','useHappyHour')),
		'Webservice_Voucher' => array('Voucher',
			array('getAll','getById','getByHandle','insert','useVoucher')),
		'Webservice_Stem' => array('Stem',
			array('getAll','getById','insert','update')),
		'Webservice_Document' => array('Document',
			array('getAll')
			),
	),
	'livebetting' => array(
		'Webservice_LivebettingRaw' => array('core',
			array('getStatus','setMatches','reduceBalance','setTicketStatus')),
	),
);

if(getAppEnv() == 'UNIT_TESTING') {
	if(!defined('GMY_DB')) define('GMY_DB','vic_ut_main');
	if(!defined('SESMY_DB')) define('SESMY_DB','vic_ut_session');
	if(!defined('MY_DB')) define('MY_DB','vic_ut_admin');
	if(!defined('WMY_DB')) define('WMY_DB','vic_ut_warehouse');
}

def('MOBILEM_API_USERNAME', 'smartbetting');
def('MOBILEM_API_PASSWORD', 'smart12');
def('MOBILEM_API_DEFAULT_PREFIX', '+420');

$GLOBALS['SEND_AUTORIZATION_SMS_TEXT'] = array('1' => "Vas kod je: '%code%'.");

def('ACL_FACTORY_CLASS', 'It6_Acl_Factory_Webservice');
def('LIVE_CLIENT_LOGIN', 'dw45SDwwcs');
def('LIVE_CLIENT_PASSWORD', 'pass1_58sss4wwefd9swewe04587');
def('LIVE_CLIENT_IP', '10.10.10.14;10.10.10.20;10.10.10.101;10.10.10.102;127.0.0.1');

// define to non-empty (or empty) value to send bet cache activation (or deactivation) parameter, 
// do not define to not send parameter to branches
def('BRANCH_BET_CHANGELOG', 1);