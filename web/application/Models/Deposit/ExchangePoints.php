<?php

class Models_Deposit_ExchangePoints {

	const AMOUNT = 'amount';
	
	private static function buildForm() {
		
		$form = new Zend_Form();
		$form->setAction('./')->setMethod('post');
		$form->addElement('text', self::AMOUNT,array('validators' => array('int')));

		return $form;
	}
	
	public static function render($view) {
		$ws = Zend_Registry::get('ws');
		$userId = Zend_Registry::get('user_id');
		
		$view->maximum = $ws->Campaign->get(
			'ToMoney', 'exchange','amountLimit', array('userId' => $userId));
		
		$view->rate = $ws->Campaign->get(
			'ToMoney', 'exchange','rate', array('userId' => $userId));
		
		$view->maxMoney = $view->rate * $view->maximum;
		
		
		//FIXME dont't hardcode
		$view->currency = 'CZK';
	}
	
	public static function submit($view) {
		$ws = Zend_Registry::get('ws');
		$userId = Zend_Registry::get('user_id');
		
		$form = self::buildForm();
		if ($form->isValid($_POST)) {
			$amount = $form->getValue(self::AMOUNT); 
			if ( $amount <= 0 ) { 
				$form->addError($view->trans('exchange_request_error'));
				$view->result = It6_FeedbackMsg::printError('exchange_must_positive');
			}
			else {
				try {
					if ( $ws->Campaign->putOn(
							'ToMoney', 'exchange', array('userId' => $userId, 'amount' => $amount)) == true ) {
						$view->result = It6_FeedbackMsg::printNotice('success_exchange');
					}
					else {
						$form->addError($view->trans('exchange_request_error'));
						$view->result = It6_FeedbackMsg::printError('exchange_maximum_overflow');
					}
				}
				catch (Exception $e) {
					$form->addError($e->getMessage());
					$view->result = It6_FeedbackMsg::printError('exchange_unexpected_error');
					It6_Log::err($e);
				}
			}
		}
	}

}
