<?php

//TODO:IT6: there was not found any use of this class

class WebserviceController extends Zend_Controller_Action {

	public function init() {
		 
		$this->view->addHelperPath('views/helpers', 'My_View_Helper');
		$this->_helper->layout->disableLayout();

		$GLOBALS['ses_status'] = $this->view->log = 2;
		Zend_Registry::set('user_id',intval($_POST['uid']));
		Models_BasicRender::render($this->view,$this->_request);
		
		It6_GlobalCache::turnOff();

	}

	public function indexAction() {		 
		Zend_Registry::set('translate',array());
		 
		if ( !isset( $_POST['webservicepass'] ) || $_POST['webservicepass'] != WEBSERVICE_PASS ) {
			$this->view->response = 5;
		} 
		else {			 
			It6_DbTransaction::begin(Zend_Registry::get('db'));

			$get = Models_Ajax_Ticket::get($this->view);

			if ( !$get ) {
				$this->view->createTicket['get']=8;
				$this->view->response = 6;
				Zend_Registry::get('db')->commit();
				return;
			}

			$this->view->error = Models_Ajax_Ticket::control($this->view->createTicket);
			$this->view->createTicket = Models_Control_Ticket::$ticket;

			if ( !$this->view->error ) {

				$ticketId = Models_Ajax_Create::ticket($this->view->createTicket);
				$this->view->createMessage = Models_Ajax_Create::$message;
				if ( isset( $this->view->createMessage['ok'] ) 
						&& $this->view->createMessage['ok'] == 1) {
							
						$this->view->response = 1;
						Models_Ajax_Ticket::setEmpty();
				}
				else {
					$this->view->response = 8;
				}

			} else {
				$this->view->response = 9;
			}

			It6_DbTransaction::commit(Zend_Registry::get('db'));

			if ( !empty($ticketId) ) {
				Zend_Registry::get('ws')->Alert->assert('TicketCreated',
						array('ticketId' => $ticketId), Zend_Registry::get('user_id'));
			}

			echo $this->view->response; 
			exit;

		}
	}

}