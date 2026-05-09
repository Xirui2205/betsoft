<?php
class It6_Cron_Job_Sms_TicketsResults extends It6_Cron_Job_Sms_Abstract {
	
	protected $_ticketIds;
	protected $_smsLang;
	protected $_smsBatch = array();
	
	protected $_tickets;
	
	protected $_userData;
	
	public function __construct(array $ticketIds) {
		if ( is_array($ticketIds) ) {
			$this->_ticketIds = $ticketIds;
		} else {
			$this->_ticketIds = array($ticketIds);
		}
	}
	
	public function createSmsBatch() {
		//hardcoded czech lang for now
		$this->_smslLang = 'cs';
		$this->init();
		
		$this->_tickets = $this->_ws->Ticket->getByIdAndUserComplete($this->_ticketIds, null, null, true);
		$this->_tickets = reset($this->_tickets);
		
		if (empty($this->_tickets))
			throw new Exception('Error: The ticket does not exist!');
		else {
			foreach($this->_tickets as $key => $ticket) {
				if(
						$ticket['stateNo'] == '1'
						|| $ticket['stateNo'] == '1L'
						|| $ticket['stateNo'] == '1W'
						|| $ticket['hostId'] != 1

				)
					unset($this->_tickets[$key]);
			}
		
			if(!empty($this->_tickets)) {
				foreach ( $this->_tickets as $key => $ticket ) {
					
					$user = $this->_ws->User->getById($ticket['userId']);
					$phone_number = $user['phone'];
					if ( !empty($phone_number) ) {
						$text = '';
						$text .= "Tiket cislo: " . $ticket['ticketHandle'] . ", ";
						$text .= "vsazeno: " . It6_Date::fromDbAsDate($ticket['createdTime']) . ", ";
						$text .= "kurs: " . $ticket['totalOdds'] . ", ";
						
						$tips = 0;
						$mistakes = 0;
						foreach ( $ticket['groups'] as $tgs ) {
							foreach( $tgs['tips'] as $t ) {
								$tips++;
								if ($t['winResult'] != '1') $mistakes++;
							}
						}
						
						$text .= "tipu: " . $tips . ", ";
						$text .= "chyb: " . $mistakes . ", ";
						$text .= "vklad: " . $ticket['amount'] . ", ";
						$text .= "vyhra: " . $ticket['realWinAmount'] . ", ";
						$text .= 'CompBet';
						
						$this->_smsBatch[] = array($phone_number, $text);
					}
				}
			}
		}
		
		
	}
	
	public function sendSmsBatch() {
		if ( empty($this->_smsBatch) ) {
			It6_Log::warn('Sending ticket results in sms : SMS batch empty!');
			return false;
		} else {
			foreach ($this->_smsBatch as $smsData) {
				$sms = new It6_Sms_PlainSms();
				$sms->setToNumber($smsData[0])->setText($smsData[1], It6_Sms_PlainSms::SMS_TYPE_TICKET_RESULT);
				$sms->sendSms();
			}
			return true;
		}
	}
	
	private function getUserData($userId) {
		if (!isset($this->userData[$userId])) {
			$stmt = Zend_Registry::get('db')
				->select()
				->from( array('a' => 'uzivatel') )
				->where('a.user_id=?', $userId)
				->query();
			$this->_userData[$userId] = $stmt->fetch();
		}
		return $this->_userData[$userId];
	}

	private function getUserPhone($userId){
		$userData = $this->getUserData($userId);
		return $userData['telefon'];
	}
	
	public function setTicketIds(array $ids) {
		if ( is_array($ids)) {
			$this->_ticketIds = $ids;
		}
	}
}