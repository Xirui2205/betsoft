<?php
/**
 * Transaction type related static methods
 * @author Pavel Klinger
 * @see Entities_TransactionType
 *
 */
class Webservice_TransactionType extends Webservice_AbstractWebService  {

	const ACCOUNT_TYPE_HOST = 'host';
	const ACCOUNT_TYPE_USER = 'user';
	const ACCOUNT_TYPE_OTHER = 'other';

	const HAS_FEE_NEVER = 'never';
	const HAS_FEE_SOMETIMES = 'sometimes';
	const HAS_FEE_ALWAYS = 'always';

	const NAME_USER_BONUS_ENTRY = 'user.bonus.entry';
	const NAME_USER_DEPOSIT_CARD = 'user.deposit.card';
	const NAME_USER_DEPOSIT_CASH = 'user.deposit.cash';
	const NAME_USER_DEPOSIT_CASH_TICKET_WIN = 'user.deposit.cash-ticket-win';
	const NAME_USER_DEPOSIT_CASH_TICKET_WIN_CANCEL = 'user.deposit.cash-ticket-win-cancel';
	const NAME_USER_DEPOSIT_BANK = 'user.deposit.bank';
	const NAME_USER_DEPOSIT_MANUAL = 'user.deposit.manual';
	const NAME_USER_WITHDRAW_BANK = 'user.withdraw.bank';
	const NAME_USER_WITHDRAW_CASH = 'user.withdraw.cash';	
	const NAME_USER_WITHDRAW_MANUAL = 'user.withdraw.manual';
	const NAME_USER_EXCHANGE_FROM_POINTS = 'user.exchange-from-points';
	const NAME_USER_EXCHANGE_FROM_POINTS_CANCEL = 'user.exchange-from-points-cancel';
	const NAME_USER_TICKET_CREATE = 'user.ticket.create';
	const NAME_USER_TICKET_CREATE_MP = 'user.ticket.create-mp';
	const NAME_USER_TICKET_CANCEL = 'user.ticket.cancel';
	const NAME_USER_TICKET_CANCEL_MP = 'user.ticket.cancel-mp';
	const NAME_USER_TICKET_RENEW_CANCELED = 'user.ticket.renew-canceled';
	const NAME_USER_TICKET_RENEW_CANCELED_MP = 'user.ticket.renew-canceled-mp';
	const NAME_USER_TICKET_COLLECT_NOCASH = 'user.ticket.collect-nocash';
	const NAME_USER_TICKET_COLLECT_NOCASH_MP = 'user.ticket.collect-nocash-mp';
	const NAME_USER_TICKET_COLLECT_NOCASH_CANCEL = 'user.ticket.collect-nocash-cancel';
	const NAME_USER_TICKET_COLLECT_NOCASH_CANCEL_MP = 'user.ticket.collect-nocash-cancel-mp';
	const NAME_BRANCH_DEPOSIT = 'branch.deposit';
	const NAME_BRANCH_WITHDRAW = 'branch.withdraw';
	const NAME_BRANCH_TICKET_CREATE_CASH = 'branch.ticket.create-cash';		
	const NAME_BRANCH_TICKET_CREATE_CASH_MP = 'branch.ticket.create-cash-mp';
	const NAME_BRANCH_TICKET_COLLECT = 'branch.ticket.collect';
	const NAME_BRANCH_TICKET_COLLECT_MP = 'branch.ticket.collect-mp';
	const NAME_BRANCH_TICKET_COLLECT_CANCEL = 'branch.ticket.collect-cancel';
	const NAME_BRANCH_TICKET_CANCEL_CASH = 'branch.ticket.cancel-cash';
	const NAME_BRANCH_TICKET_CANCEL_CASH_MP = 'branch.ticket.cancel-cash-mp';
	const NAME_BRANCH_TICKET_RENEW_CANCELED_CASH = 'branch.ticket.renew-canceled-cash';
	const NAME_BRANCH_TICKET_RENEW_CANCELED_CASH_MP = 'branch.ticket.renew-canceled-cash-mp';
	const NAME_BRANCH_USER_WITHDRAW_CASH = 'branch.user.withdraw-cash';	
	const NAME_BRANCH_USER_DEPOSIT_CASH = 'branch.user.deposit-cash';
	const NAME_BRANCH_USER_DEPOSIT_CASH_TICKET_WIN = 'branch.user.deposit-cash-ticket-win';
	const NAME_BRANCH_USER_DEPOSIT_CASH_TICKET_WIN_CANCEL = 'branch.user.deposit-cash-ticket-win-cancel';
	const NAME_OTHER_TICKET_FORFEIT = 'other.ticket.forfeit';
	const NAME_OTHER_TICKET_FORFEIT_MP = 'other.ticket.forfeit-mp';
	const NAME_OTHER_TICKET_PAYOUT = 'other.ticket.payout';
	const NAME_OTHER_TICKET_PAYOUT_MP = 'other.ticket.payout-mp';
	const NAME_OTHER_TICKET_PAYOUT_CASH = 'other.ticket.payout-cash';
	const NAME_OTHER_TICKET_PAYOUT_CASH_MP = 'other.ticket.payout-cash-mp';
	const NAME_OTHER_TICKET_PAYOUT_CASH_CANCEL = 'other.ticket.payout-cash-cancel';	
	const NAME_OTHER_TICKET_PAYOUT_CASH_CANCEL_MP = 'other.ticket.payout-cash-cancel-mp';
	const NAME_OTHER_TICKET_PAYOUT_CANCEL = 'other.ticket.payout-cancel';
	const NAME_OTHER_TICKET_PAYOUT_CANCEL_MP = 'other.ticket.payout-cancel-mp';

	const NAME_OTHER_TICKET_COLLECT_INDIVIDUAL = 'other.ticket.collect-individual';
	const NAME_OTHER_TICKET_COLLECT_INDIVIDUAL_MP = 'other.ticket.collect-individual-mp';
	const NAME_FEE_GENERAL = 'fee.general';

	const NAME_CROWN_TICKET_CREATE = 'crown.ticket.create';
	const NAME_CROWN_TICKET_CANCEL = 'crown.ticket.cancel';
	const NAME_CROWN_TICKET_COLLECT_CASH = 'crown.ticket.collect-cash';
	const NAME_CROWN_TICKET_COLLECT_NOCASH = 'crown.ticket.collect-nocash';

	public static $TABLE = "financial_transaction_type";
	public static $ENTITY_NAME = "Entities_TransactionType";
	public static $TABLE_PREFIX = "tt";
	public static $IDENTITY = "id";
	protected static $CONV = array(
		'id' => 'transactionTypeId',
		'name' => 'name',
		'note' => 'note',
		'from' => 'from',
		'thru' => 'thru',
		'to' => 'to',
		'need_confirm' => 'needConfirm',
		'late_deposit' => 'lateDeposit',
		'debiting' => 'debiting',
		'account_type' => 'accountType',
		'low_limit' => 'lowLimit',
		'high_limit' => 'highLimit',
		'has_fee' => 'hasFee',
		'fee_fix' => 'feeFix',
		'fee_rel' => 'feeRel',
		'is_fee_editable' => 'isFeeEditable',
		'is_limit_editable' => 'isLimitEditable',
		'note_daybook' => 'noteDaybook',
		'changing_bonus' => 'changingBonus',
		'status_changing_balance' => 'statusChangingBalance',
		'status_for_accounting' => 'statusForAccounting',
	);

	/**
	 * Returns all transaction types in the system.
	 * @return array array of transaction structs
	 * @see Entities_Transaction
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Find transaction type by given identifier.
	 * @param integer $id identifier of the transaction type
	 * @return struct team structure
	 * @see Entities_Transaction
	 */
	public static function getById($id, $extensions = null) {
		return parent::getById($id, $extensions);
	}

	/**
	 * Find transaction type by given identifier.
	 * @param integer $id identifier of the transaction type
	 * @param integer $currencyId identifier of the currency
	 * @return struct team structure
	 * @see Entities_Transaction
	 */
	public static function getByIdAndCurrency($id, $currencyId) {

		$entity = static::defaultQuery(static::getDb()->select())
			//->reset(Zend_Db_Select::COLUMNS)
			->columns(array(
				'feeFix' => 'IF(ISNULL(ftc.fee_fix),tt.fee_fix,ftc.fee_fix)',
				'feeRel' => 'IF(ISNULL(ftc.fee_rel),tt.fee_rel,ftc.fee_rel)',
				'lowLimit' => 'IF(ISNULL(ftc.low_limit),tt.low_limit,ftc.low_limit)',
				'highLimit' => 'IF(ISNULL(ftc.high_limit),tt.high_limit,ftc.high_limit)'))
			->joinLeft(array(
				'ftc' => 'financial_transaction_type_currency'),
				"ftc.financial_transaction_type_id = tt.id AND ftc.currency_id = $currencyId",
				null)
			->where(static::$IDENTITY . ' = ?', $id)
			->query()->fetch();

		return static::toEntity($entity);
	}

	/**
	 * Find transaction type by given name.
	 * @param string $name unique name of the transaction type
	 * @return struct
	 * @see Entities_Transaction
	 */
	public static function getByName($name) {
		try {
			$entity = static::defaultQuery(static::getDb()->select())
				->where('name = ?', $name)
				->query()->fetch();
			return static::toEntity($entity);
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception('getByName', 0, $e);
		}
	}

	public static function update($entity) {
		return parent::update($entity);
	}
}