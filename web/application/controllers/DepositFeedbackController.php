<?php

class DepositFeedbackController extends Zend_Controller_Action {

public function init(){
	//$this->_helper->layout->disableLayout();
	It6_GlobalCache::turnOff();
	Models_BasicRender::render($this->view,$this->_request);
}

public function muzoAction() {
	$this->view->oneColumn = true;
	global $MUZO_CONFIG;

	if (isset($_GET['DEBUG_VIEW'])) {
		switch ($_GET['DEBUG_VIEW']) {
		case 'failed':
			$this->view->result = 'card_deposit_failed';
			break;
		case 'success':
		default:
			$this->view->result = 'card_deposit_success';
			break;
		}
		return;
	}

	$client = new It6_Muzo_Client(new It6_Muzo_Config($MUZO_CONFIG), $_SESSION['lang']);
	// receiveCreateOrder() will throw exception if request data are malformed
	// return value is digest verification result
	$params = $client->receiveCreateOrder($_REQUEST, $digestText, $digest);
	if (false === $params) {
		It6_Log::warn("NOT verified. DATA='$digestText', DIGEST='$digest'", It6_Log::TAG_MUZO_VERIFY);
		$id = (empty($_REQUEST['ORDERNUMBER']) ? 'unknown' :  $_REQUEST['ORDERNUMBER']);
		//TODO: don't throw exception, show message to user?
		throw new Exception('MUZO response cannot be verified. webpayOrderId: ' . $id);
	}
	else
		It6_Log::info("Verified. DATA='$digestText', DIGEST='$digest'", It6_Log::TAG_MUZO_VERIFY);
	$orderId = $params->getParam('ORDERNUMBER');
	$db = Zend_Registry::get('db');
	$order = It6_Models_WebPayOrder::getData($orderId, $db);
	if (empty($order)) {
		$this->view->error = 'card_deposit_order_not_found';
		return;
	}
	else if (It6_Models_WebPayOrder::STATUS_IN_PROGRESS != $order['status']) {
		$this->view->error = 'card_deposit_invalid_order_state';
		return;
	}
	$prCode = $params->getParam('PRCODE');
	$resultText = $params->getParam('RESULTTEXT');
	if (It6_Muzo_PrCode::OK == $prCode) {
		$deposit = true;
		$status = It6_Models_WebPayOrder::STATUS_OK;
	}
	else {
		$deposit = false;
		$status = It6_Models_WebPayOrder::STATUS_REJECTED;
	}
	try {
		$data = array('updated' => It6_Date::dbNow(), 'status' => $status);
		if (isset($resultText))
			$data['resultText'] = $resultText;
		It6_Models_WebPayOrder::update($orderId, $data, $db);
	}
	catch (Exception $e) {
		It6_Log::error(
			'WebPay order status not updated. (ID=%id%)',
			It6_Log::TAG_DEPOSIT,
			$params->getParams()
		);
	}
	$ws = Zend_Registry::get('ws');
	$ws->Transaction->deposit($order['transactionId'], $deposit);

	$this->view->result = ($deposit ? 'card_deposit_success' : 'card_deposit_failed');
	$this->view->url = $this->view->UrlSet(50);
}

} // class DepositFeedbackController
