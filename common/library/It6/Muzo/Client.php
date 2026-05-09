<?php

class It6_Muzo_Client {

private $config = null;
private $lang = null;

/**
 * @param It6_Muzo_Config $cfg MUZO configuration instance
 * @param string $lang Language for response (used when creating URL for response)
 */
public function __construct(It6_Muzo_Config $cfg, $lang = null) {
	$this->config = clone $cfg;
	if (empty($lang) && !empty($cfg->defaultLang))
		$lang = $cfg->defaultLang;
	$this->lang = $lang;
}

public function getSignedDigest(It6_Muzo_ParameterSet $params) {
	$digest = new It6_Muzo_Digest($this->config->certificateFile, $this->config->privateKeyFile, $this->config->privateKeyPassword);
	$textForDigest = $params->getTextForDigest();
	$signedDigest = $digest->sign($textForDigest);
	if (false === $signedDigest) {
		throw new Exception('Signing digest failed!');
	}
	return base64_encode($signedDigest);
}

public function verifySignedDigest(It6_Muzo_ParameterSet $params, &$textForDigest = null, &$receivedDigest = null) {
	$digest = new It6_Muzo_Digest($this->config->certificateFile, $this->config->privateKeyFile, $this->config->privateKeyPassword);
	$textForDigest = $params->getTextForDigest();
	$receivedDigest = $params->getParam('DIGEST');
	return $digest->verify($textForDigest, base64_decode($receivedDigest));
}

/**
 * @returns Zend_Http_Response
 */
public function sendRequest(It6_Muzo_ParameterSet $params) {
	//TODO: client needs to set session cookie when redirecting back to WebPay!!!
	$client = new Zend_Http_Client($this->config->urlForRequest, array(
		'maxredirects' => 0,
		'timeout' => 30,
		'useragent' => 'CompBet HTTP Client/1.0'
	));
	//$client->setMethod(Zend_Http_Client::POST);
	//$client->setParameterPost($params->getParams());
	$client->setParameterGet($params->getParams());
	return $client->request();
}

/**
 * @returns mixed value from config that this instance was constructed with
 */
public function getConfigValue($name) {
	return $this->config->$name;
}

/**
 * @returns string Complete URL with query string for GET method request.
 */
public function getUrl(It6_Muzo_ParameterSet $params, $escapeHtml = false) {
	$uri = Zend_Uri_Http::fromString($this->config->urlForRequest);
	$uri->addReplaceQueryParameters($params->getParams());
	$str = $uri->getUri();
	return ($escapeHtml ? htmlspecialchars($str) : $str);
}

/**
 * @param It6_Muzo_ParameterSet $params Message parameters recieved from MUZO
 * @param string $textForDigest [optional] Output for text created from parameters for which digest was computed and verified.
 * @param string $receivedDigest [optional] Output for received base64 encoded digest for verification of message data.
 * @returns FALSE or instance of It6_Muzo_ParameterSet (or derived class)
 */
public function receiveResponse(It6_Muzo_ParameterSet $params, &$textForDigest = null, &$receivedDigest = null) {
	$this->revalidateParams($params);
	return ($this->verifySignedDigest($params, $textForDigest, $receivedDigest) ? $params : false);
}

private function revalidateParams(It6_Muzo_ParameterSet $params) {
	if (!$params->revalidate()) {
		$msg = "Request parameters' validation failed!\n";
		foreach ($params->getValidationMessages() as $paramName => $message)
			$msg .= "\t$paramName : $message\n";
		throw new Exception($msg);
	}
}

/**
 * Finds proper URL for response in config (uses language to decide)
 * @param string type name (only 'CREATE_ORDER' implemented)
 * @returns string URL for response
 */
private function findUrlForResponse($responseType) {
	$cfgItem = null; // variable name in config
	switch ($responseType) {
	case 'CREATE_ORDER':
		$cfgItem = 'urlForResponse';
		break;
	default:
		break;
	}
	if (empty($cfgItem))
		return '';
	if (is_array($this->config->$cfgItem)) {
		$urls = $this->config->$cfgItem;
		if (!empty($this->lang) && array_key_exists($this->lang, $urls))
			return $urls[$this->lang];
		else if (!empty($this->config->defaultLang) && array_key_exists($this->config->defaultLang, $urls))
			return $urls[$this->config->defaultLang];
		else {
			reset($urls);
			return current($urls);
		}
	}
	else
		return $this->config->$cfgItem;
}

/**
 * @param int $orderId unique ID for MUZO
 * @param int $amount in smallest units of currency (eg. cents for USD)
 * @param int $currency use It6_Muzo_CurrencyCode constants
 * @param int $internalOrderId [optional] internal ID in merchant's system
 * @returns Zend_Http_Response
 */
public function createRequestParamsForCreateOrder($orderId, $amount, $currency, $internalOrderId = null) {
	$params = new It6_Muzo_CreateOrderRequestParams();
	$params->setParam('MERCHANTNUMBER', $this->config->merchantNumber, true);
	$params->setParam('ORDERNUMBER', $orderId, true);
	$params->setParam('AMOUNT', round($amount), true);
	$params->setParam('CURRENCY', $currency, true);
	$params->setParam('DEPOSITFLAG', 1, true);
	$params->setParam('URL', $this->findUrlForResponse('CREATE_ORDER'), true);
	if (!empty($internalOrderId))
		$params->setParam('MERORDERNUM', $orderId, true);
	// optional DESCRIPTION, MD could be added
	$params->setParam('DIGEST', $this->getSignedDigest($params), true);
	$this->revalidateParams($params);
	return $params;
}

/**
 * @returns Zend_Http_Response
 */
public function sendCreateOrder($orderId, $amount, $currency, $internalOrderId = null) {
	$params = $this->createRequestParamsForCreateOrder($orderId, $amount, $currency, $internalOrderId);
	return $this->sendRequest($params);
}

/**
 * @returns string
 */
public function getUrlForCreateOrder($orderId, $amount, $currency, $internalOrderId = null) {
	$params = $this->createRequestParamsForCreateOrder($orderId, $amount, $currency, $internalOrderId);
	return $this->getUrl($params);
}

/**
 * @param array $data Associative array containing parameters received from request.
 * @returns FALSE|It6_Muzo_CreateOrderResponseParams
 */
public function receiveCreateOrder(array $data, &$textForDigest = null, &$receivedDigest = null) {
	return $this->receiveResponse( new It6_Muzo_CreateOrderResponseParams($data), $textForDigest, $receivedDigest );
}

} // class It6_Muzo_Client
