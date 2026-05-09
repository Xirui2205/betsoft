<?php

class Models_MyAccount_CancelTicket {
	
	const FORM_NAME = 'cancelTicket';
	const INPUT_TICKET_HANDLE = 'ticketHandle';
	
	public $userId;
	
	
	
	public function __construct($view) {
		
	}

	public static function buildForm($view, $display = true) {

		$ws = Zend_Registry::get('ws');
		

		$form = new It6_Models_DecoratedForm_Table();

		$form->setName(static::FORM_NAME)->setAction('./')->setMethod('post');

		if (!$display) $form->setAttrib('style','display:none');


		$ticketHandle = $form->createElement('text', static::INPUT_TICKET_HANDLE, array('maxlength'=> 9, 'style' => 'width:80px;text-align:right'));
		$ticketHandle
			->setlabel('ticket_handle')
			->setValue(It6_Validate_Int::formatInteger(0))
			->setAttrib('id', static::INPUT_TICKET_HANDLE.static::FORM_NAME)
			->setRequired(true);
		$form->addElement($ticketHandle);

		$model = new Models_MyAccount_CancelTicket($view);

		
		$form->addElement('submit', 'submit', array('label' => 'send_request','class'=>'submit'));
		$form->getElement('submit')->setAttrib('id', 'submit'.$name);

		return $form;
	}

	public static function submitForm($view) {
		$ws = Zend_Registry::get('ws');
		$view->resultBranch = NULL;
		$form = static::buildForm($view, true);
		if ($form->isValid($_POST)) {
			$ticketHandle = $form->getValue(static::INPUT_TICKET_HANDLE);

			//dodatecna validace
			$userId = Zend_Registry::isRegistered('user_id') ? Zend_Registry::get('user_id') : 0;
			$ticket = $ws->Ticket->getByHandle($ticketHandle);

			if ( empty($ticket) || $ticket->userId != $userId || !empty($ticket->cash) ) {
				$msg = empty($ticket->cash) ? 'error_unknown_ticket_handle' : 'cash_ticket_can_not_be_canceled_from_the_web';
				$form->populate($_POST);
				$view->form = $form;
				$form->addError($view->trans($msg));
				$view->error = true;
				$view->result =It6_FeedbackMsg::printError($msg);
				It6_Log::notice(
					"Cancelation of  ticket '%ticketHandle%' failed.",
					It6_Log::TAG_USER_OPERATION,
					array(
						'userId' => $userId,
						'ticketHandle' => $ticketHandle,
					)
				);
			}
			else {
				try {
					$ws->Ticket->cancel($ticket->ticketId);
					$view->error = false;
					$view->success = true;
					$view->result = It6_FeedbackMsg::printNotice('ticket_canceled_successfuly');
					$view->form = '';
					It6_Log::info(
						"Ticket '%ticketHandle%' canceled successfuly.",
						It6_Log::TAG_USER_OPERATION,
						array(
							'userId' => $userId,
							'ticketHandle' => $ticketHandle,
							)
					);
				}
				catch (Exception $e) {
					$form->addError($e->getMessage());
					$view->error = true;
					$view->result = It6_FeedbackMsg::printError($e->getMessage());
				}
			}
		} else {
			$form->populate($_POST);
			$view->form = $form;
			$form->addError($view->trans('error_cancel_ticket'));
			$view->error = true;
			$view->result = It6_FeedbackMsg::printError('error_cancel_ticket');
		}
	}

}
