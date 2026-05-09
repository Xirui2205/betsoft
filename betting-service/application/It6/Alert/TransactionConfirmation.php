<?php
class It6_Alert_TransactionConfirmation extends It6_Alert_MailAbstract {

	public static function check($params, $userId = null, $branchId = null) {
		$transactions = Webservice_Transaction::getAllWhere(array("status = 'pending'"));

		if(!empty($transactions))
			return true;
		else
			return false;
	}

	protected static function getMailParameters($params, $userId = null, $branchId = null) {
		$ret = parent::getMailParameters($params, $userId, $branchId);


		$extensions = array();
		$columns = array(
			'transactionId',
			'time',
			'typeId',
			'typeName',
			'value',
			'currencyCode',
			'userHandle',
			'hostId',
			'userNick',
			'branchName',
			'ticketId',
			'bankAccountPrefix',
			'bankAccountPrefix',
			'bankAccountNumber',
			'bankAccountBankCode',
			'notes',
		);
		$columnExt = new It6_WsExtension_Client_Columns('columns', $columns);
		$extensions[] = $columnExt->getWsCallArguments();

		$types = Webservice_TransactionType::getAllWhere(array('needConfirm = 1'));
		$filterTypes = array();
		foreach($types as $type) {
			$filterTypes[] = $type->transactionTypeId;
		}

		$filterDef = array(
			array('?' => array('status' => 'pending'), 'OP' => '='),
			array('?' => array('typeId' => $filterTypes), 'OP' => 'IN (?)')
		);
		$filterExt = new It6_WsExtension_Client_Filter('filter', $filterDef);
		$extensions[] = $filterExt->getWsCallArguments();

		$orderExt = new It6_WsExtension_Client_Order('filter', array('time'));
		$extensions[] = $orderExt->getWsCallArguments();

		$transactions = Webservice_Transaction::getAll($extensions);

		$ret['transactions'] = $transactions;

		return $ret;
	}
}
