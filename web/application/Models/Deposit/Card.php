<?php

class Models_Deposit_Card {

protected $_transactionType = null;

public $currencies = null;
public $minAmount = false;
public $maxAmount = false;
public $feeFix = 0; // central currency
public $feeRel = 0; // percent

public function __construct() {
	$transactionType = Zend_Registry::get('ws')->TransactionType
		->getByName(Webservice_TransactionType::NAME_USER_DEPOSIT_CARD);
	if (empty($transactionType))
		throw new ExHandler('Transaction type "' . Webservice_TransactionType::NAME_USER_DEPOSIT_CARD . '" not found');
	
	$this->_transactionType = $transactionType;
	$this->minAmount = (isset($transactionType->lowLimit) ? $transactionType->lowLimit : false);
	$this->maxAmount = (isset($transactionType->highLimit) ? $transactionType->highLimit : false);
	$this->feeFix = (empty($transactionType->feeFix) ? 0 : $transactionType->feeFix);
	$this->feeRel = (empty($transactionType->feeRel) ? 0 : $transactionType->feeRel);

	$this->currencies = array();
	foreach (It6_Models_Currency::readDataAll() as $id => $data) {
		if ($data['allowed'] && $data['webpayMerchant']) {
			$rate = $data['rate'];
			$data2 = array(
				'feeFix' => $this->feeFix * $data['rate'],
				'feeRel' => $this->feeRel,
			);
			if (false !== $this->minAmount)
				$data2['minAmount'] = $this->minAmount * $rate;
			if (false !== $this->maxAmount)
				$data2['maxAmount'] = $this->maxAmount * $rate;
			$this->currencies[$id] = array_merge($data, $data2);
		}
	}
}

public function currencyIsoCodeToId($isoCode) {
	foreach ($this->currencies as $id => $data) {
		if ($data['isoCode'] == $isoCode)
			return $id;
	}
	return false;
}

public function getTransactionTypeId() {
	return $this->_transactionType->transactionTypeId;
}

/**
 * @param int $currencyId
 * @param float $amount in currency given by $currencyId
 * @returns float total fee in currency given by $currencyId
 */
public function getFee($currencyId, $amount) {
	if (!array_key_exists($currencyId, $this->currencies))
		throw new ExHandler('Unknown currency ID: ' . $currencyId);
	$currency = $this->currencies[$currencyId];
	$fee = $currency['rate'] * $this->feeFix + $amount * $this->feeRel;
	return It6_Models_Currency::round(
		$fee,
		It6_Models_Currency::ROUND_MATH,
		$currencyId,
		It6_Models_Currency::ROUND_PARAM_CURRENCY,
		false,
		$db
	);
}

public function newMuzoClient($merchantNumber) {
	global $MUZO_CONFIG;
	$config = new It6_Muzo_Config($MUZO_CONFIG);
	$config->merchantNumber = $merchantNumber;
	if (!$config->isValid($messages))
		throw new Exception('Invalid MUZO configuration: ' . implode("\n", $messages));
	return new It6_Muzo_Client($config, $_SESSION['lang']);
}

/**
 * Registers new MUZO order (reserves ID).
 * @param int $userId user that is willing to deposit
 * @param int $currencyId
 * @param float $amount Amount to be added to user's account (in currency given by $currencyId)
 * @returns int ID of WebPay order
 */
public function registerOrderMuzo($userId, $currencyId, $amount) {
	$db = Zend_Registry::get('db');
	$amount = It6_Models_Currency::round(
		$amount,
		It6_Models_Currency::ROUND_MATH,
		$currencyId,
		It6_Models_Currency::ROUND_PARAM_CURRENCY,
		false,
		$db
	);
	$fee = $this->getFee($currencyId, $amount);
	$data = array(
		'userId' => $userId,
		'currencyId' => $currencyId,
		'amount' => $amount,
		'fee' => $fee,
		'created' => It6_Date::dbNow(),
		'status' => It6_Models_WebPayOrder::STATUS_PRE_REQUEST,
	);
	return It6_Models_WebPayOrder::create($data, $db);
}

/**
 * Creates all needes data for HTTP request.
 * @param int $orderId ID of order with STATUS_PRE_REQUEST
 * @param int $userId ID of user
 * @param array $order [optional] returned order data from DB
 * @param It6_Muzo_Client $client [optional] instance of MUZO client that is configured to handle given order
 * @returns It6_Muzo_CreateOrderRequestParams or FALSE
 */
public function createOrderMuzo($orderId, $userId, &$order = null, &$client = null) {
	$order = It6_Models_WebPayOrder::getData($orderId);
	if (empty($order) || ($userId != $order['userId']))
		return false;
	$currencyId = $order['currencyId'];
	$currency = $this->currencies[$currencyId];
	$client = $this->newMuzoClient($currency['webpayMerchant']);
	$total = $order['amount'] + $order['fee'];
	return $client->createRequestParamsForCreateOrder($orderId, intval($total * $currency['smallestUnit']), $currency['isoCode']);
}

/**
 * Changes status of order from STATUS_PRE_REQUEST to STATUS_IN_PROGRESS.
 * @param int $orderId
 * @returns int number of affected rows
 */
public function progressOrderMuzo($orderId) {
	$db = Zend_Registry::get('db');
	return It6_Models_WebPayOrder::update(
		$orderId,
		array('status' => It6_Models_WebPayOrder::STATUS_IN_PROGRESS),
		$db,
		array(It6_Models_WebPayOrder::objectToDatabase('status', false) . '=?' => It6_Models_WebPayOrder::STATUS_PRE_REQUEST)
	);
}

/**
 * Sets financial transaction ID that should associated to given PayMUZO order.
 * @param int $orderId ID of order to be updated
 * @param int $transactionId ID of transaction that should be associated with order
 * @returns int number of rows affected
 */
public function associateTransactionToOrderMuzo($orderId, $transactionId) {
	$db = Zend_Registry::get('db');
	return It6_Models_WebPayOrder::update(
		$orderId,
		array('transactionId' => $transactionId),
		$db
	);
}

} // class Models_Deposit_Card

/*

Postup vkladu kartou:

castka - deposit.card.minAmount, deposit.card.maxAmount, deposit.card.cost
mena
info o poplatku za transakci
info o celkove castce

[Odeslat]

Financni transakce - vytvoreni objednavky WebPay = pre-deposit
                   - potvrzeni objednavky WebPay - provedeno = OK
                                                 - zamitnuto = no-deposit
                   - ukladat ID financni transkace

Nezapomenout logovat transakce i do souboru

*/