<?php

class DepositRedirectController extends Zend_Controller_Action {

public function init(){
	$this->_helper->layout->disableLayout();
	It6_GlobalCache::turnOff();
}

public function muzoAction() {
	$orderId = $_GET['id'];
	$params = null;
	if (!empty($orderId)) {
		$model = new Models_Deposit_Card();
		$params = $model->createOrderMuzo($orderId, Zend_Registry::get('user_id'), $order, $client);
	}
	if (empty($params)) {
		$this->view->error = true;
		return;
	}
	if (!$model->progressOrderMuzo($order['id'])) {
		//TODO:I18N
		$this->view->error = 'TODO:I18N: objednavka nenalezena';
	}
	else {
		$transactionId = Zend_Registry::get('ws')->Transaction->make(array(
			'userId' => $order['userId'],
			'currencyId' => $order['currencyId'],
			'typeId' => $model->getTransactionTypeId(),
			'value' => $order['amount'] + $order['fee'],
			'fee' => $order['fee'],
			'hostId' => It6_Models_Host::ID_INTERNET
		));
		if (1 != $model->associateTransactionToOrderMuzo($orderId, $transactionId)) {
			It6_Log::error(
				'No transaction associated to WebPay order. orderId=' . $orderId . '; transactionId=' . $transactionId . ';',
				It6_Log::TAG_DEPOSIT
			);
		}
		$this->view->formAction = $client->getConfigValue('urlForRequest');
		$this->view->params = $params->getParams();
	}
}

} // class DepositRedirectController
