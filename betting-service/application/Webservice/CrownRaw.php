<?php
class Webservice_CrownRaw extends Webservice_AbstractWebService {

	public static $error = false;
	public static $response = array();
	public static $notes = array();

	const OK = 'ok';
	const ERROR = 'chyba';
	const UNPROCESSED = 'nezpracovano';

	const PASSWORD_PARAM = 'crown.json-client.password';
	const IP_PARAM = 'crown.json-client.ip';

	public static function transactionImport($request) {

		$db = static::getDb();
		static::checkSecurity($request['autorizace']['heslo']);

		foreach ($request['transakce'] as $key => $transaction) {

			unset($notes);

			if (self::$error == true) {
				self::appendResult($key, self::UNPROCESSED);
				continue;
			}

			try {
				$user = Webservice_User::getAnonymousByBranchId($transaction['id_pobocka']);
			} catch(Exception $e) {
				self::$error = true;
				self::appendResult($key, self::ERROR);
				continue;
			}

			$currencyId = It6_Models_User::get($user->userId, 'currencyId', $db);
			$hosts = Webservice_Host::getByBranchId($transaction['id_pobocka']);
			foreach ($hosts as $host) $hostId = $host->hostId;

			$notes['crownTransactionId'] = $key;
			$notes['crownTicketId'] = $transaction['id_tiket'];
			$notes['crownMoveId'] = $transaction['id_pohyb_chance'];
			$notes['crownPaid'] = $transaction['zaplaceno'];

			try {
				switch ($transaction['typ_operace']) {
					case 'tiket_vyplata':
						Webservice_Transaction::make(array(
							'value' => -$transaction['financni_vyse'],
							'createAdminId' => $transaction['id_zamestnanec'],
							'hostId' => $hostId,
							'typeName' => (empty($transaction['financni_vyse'])) 
								? Webservice_TransactionType::NAME_CROWN_TICKET_COLLECT_NOCASH 
								: Webservice_TransactionType::NAME_CROWN_TICKET_COLLECT_CASH,
							'userId' => $user->userId,
							'currencyId' => $currencyId,
							'notes' => json_encode($notes)
						));
					break;
					case 'tiket_prijem':
						Webservice_Transaction::make(array(
							'value' => $transaction['financni_vyse'],
							'createAdminId' => $transaction['id_zamestnanec'],
							'hostId' => $hostId,
							'typeName' => Webservice_TransactionType::NAME_CROWN_TICKET_CREATE,
							'userId' => $user->userId,
							'currencyId' => $currencyId,
							'notes' => json_encode($notes)
						));
					break;
					case 'tiket_storno':
						Webservice_Transaction::make(array(
							'value' => -$transaction['financni_vyse'],
							'createAdminId' => $transaction['id_zamestnanec'],
							'hostId' => $hostId,
							'typeName' => Webservice_TransactionType::NAME_CROWN_TICKET_CANCEL,
							'userId' => $user->userId,
							'currencyId' => $currencyId,
							'notes' => json_encode($notes)
						));
					break;
					default:
						It6_Log::err(
							"The operation type '%operationType%' is unknow.",
							It6_Log::TAG_ALERT,
							array('operationType' => $transaction['typ_operace'])
						);
						self::$error = true;
						self::appendResult($key, self::ERROR);
					break;
				}
			} catch(Exception $e) {
				self::$error = true;
				self::appendResult($key, self::ERROR);
			}

			if (empty(self::$error)) self::appendResult($key, self::OK);
		}

		return self::$response;
	}

	private static function appendResult($crownTransactionId, $state) {
		self::$response[$crownTransactionId] = array('stav' => $state);
	}

	private static function checkSecurity($password) {
		if ( $password == Webservice_Parameter::getGlobalParameter(self::PASSWORD_PARAM) 
			&& in_array(It6_Php::getRemoteAddr(), explode(';', Webservice_Parameter::getGlobalParameter(self::IP_PARAM))) ) {
			return true;
		} else {
			throw new It6_XmlRpc_Exception('Wrong password or IP address.');
		}
	}
}