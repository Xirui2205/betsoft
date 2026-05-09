<?php
/**
 * @author Łukasz Walkow (lukasz.walkow [at] wiwcom.pl) 2011
 * @author Pavel Klinger
 */
class Webservice_LivebettingRaw {

	/**
	 * @param string $sessionId
	 * @param float $sum
	 * @param string $login
	 * @param string $password
	 * @return struct
	 */
	public static function getStatus($sessionId, $sum, $login, $password) {
		static::checkSecurity($login, $password);
		$session = Webservice_Session::getByLiveSession($sessionId);
		if (!empty($session)) $limits = Webservice_SettingLimits::getUserLimits($session->user_id);

		$response = Webservice_Livebetting::getUserStatus($sessionId);

		//status logic
		$status = 0;
		if ( empty($response) ) {
			$status = 0;
		} else {
			if ($response["status"] == 2 || $response["status"] == 3) {
				if ($response["balance"] >= $sum) {
					$status = 1;
					if (!empty($limits)) {
						if (($limits->actualAmount + $sum) > $limits->limitAmount) {
							$status = 3;
						}
					}
				} else {
					$status = 2;
				}
			}
		}
		//-- end of status logic

		if ( $status == 0 )
			$responseSB = array('status' => 0);
		else
			$responseSB = array(
				"clientUserId" => $response["userId"],
				"currencyId" => $response["currencyId"],
				"balance" => $response["balance"],
				"nick" => $response["nick"],
				"status" => $status
			);

		return $responseSB;
	}

	/**
	 * Sets current matches offer
	 *
	 * @param string $login
	 * @param string $password
	 * @param struct $matchInfoArray
	 * @param boolean $onlyActive
	 * @return array
	 */
	public static function setMatches($login, $password, $matchInfoArray, $onlyActive = false) {
		static::checkSecurity($login, $password);

		$matchesIds = array_keys($matchInfoArray);
		$matchesSmartBetting = array();
		$matchesIt6 = array();
		for ($i = 0; $i < sizeof($matchesIds); ++$i) {
			$matchId = $matchesIds[$i];
			$match = $matchInfoArray[$matchId];
			$matchesIt6[] = array(
				'transactionId' => $matchId . '-' . time(),
				'match' => array(
					'id' => $match['live_sazka_id'],
					'start' => gmdate("Y-m-d H:i:s", $match["start"]),
					'status' => str_replace("LIVE_", "", $match['status']),
					'sport' => static::decodeUtf($match['sport']),
					'sportId' => $match['sport_id'],
					'league' => static::decodeUtf($match['league']),
					'homeTeam' => static::decodeUtf($match['home_team']),
					'awayTeam' => static::decodeUtf($match['away_team']),
					'minute' => $match['minute'],
					'specialId' => $match['special_id'],
					'scoreAway' => $match['score_away'],
					'scoreHome' => $match['score_home'],
					'region' => $match['region']
				)
			);
			$matchesSmartBetting[$matchId] = 1;
		}

		$response = Webservice_Livebetting::setMatches($matchesIt6, $onlyActive);
		return $matchesSmartBetting;
	}

	/**
	 * Places ticket and reduces balance
	 *
	 * @param string $sessionId
	 * @param string $login
	 * @param string $password
	 * @param struct $ticketObject
	 * @return boolean
	 */
	public static function reduceBalance($sessionId, $login, $password, $ticketObject) {
		static::checkSecurity($login, $password);

		$ticketIt6 = array(
			'userId' => $ticketObject["user_id"],
			'idLive' => $ticketObject["ticket_id"],
			'stake' => $ticketObject["stake"],
			'rate' => $ticketObject["rate"],
			'win' => $ticketObject["win"],
			'type' => $ticketObject["type"],
			'timeCreated' => gmdate("Y-m-d H:i:s", $ticketObject["date_created"]));

		for ($i = 0; $i < sizeof($ticketObject["detail"]); ++$i) {
			$matchDetail = $ticketObject["detail"][$i];
			$ticketIt6['bets'][] = array(
				'id' => $matchDetail["bet_id"],
				'players' => static::decodeUtf($matchDetail["players"]),
				'sport' => static::decodeUtf($matchDetail["sport"]),
				'region' => "",
				'event' => static::decodeUtf($matchDetail["league"]),
				'market' => static::decodeUtf($matchDetail["market"]),
				'tip' => static::decodeUtf($matchDetail["tip_text"]),
				'rate' => $matchDetail["rate"]
			);
		}
		try {
			$response = Webservice_Livebetting::createTicket($ticketIt6);
		} catch ( Exception $e ) {
			return false;
		}
		
		return true;
	}

	/**
	 * Sets ticket status and increases/decreases balance
	 *
	 * @param string $login
	 * @param string $password
	 * @param struct $transactionObject
	 * @return array
	 */
	public static function setTicketStatus($login, $password, $ticketStatusObject) {
		static::checkSecurity($login, $password);
		
		$ticketStatusIt6 = array();
		$responseToSmartbetting = array();

		$transactionIds = array_keys($ticketStatusObject);

		for ($i = 0; $i < sizeof($transactionIds); ++$i) {
			$transactionId = $transactionIds[$i];
			$transaction = $ticketStatusObject[$transactionId];

			$status = $transaction['status'];
			$bets = array();
			for ($j = 0; $j < sizeof($transaction["detail"]); ++$j) {
				$bet = $transaction["detail"][$j];
				$bets[$j] = array('id' => $bet["bet_id"]);
				//if ($bet["cancel"] != "0") {
				$bets[$j]['canceled'] = $bet["cancel"];
				//}
				if (isset($bet["result_text"]) && $bet["result_text"] != "") {
					$bets[$j]['result'] = static::decodeUtf($bet["result_text"]);
					$bets[$j]['resultValid'] = true;
				}
				else
					$bets[$j]['resultValid'] = false;
			}
			$ticketStatusIt6[$i] =
					array(
						'transactionId' => $transactionId,
						'ticket' => array(
							'id' => ltrim($transaction['ticket_id'], '0'),
							'bets' => $bets
					));


			$ticketStatusIt6[$i]['ticket']['win'] = $transaction["win"];
			$ticketStatusIt6[$i]['ticket']['rate'] = $transaction["rate"];
			if ($status == 2) { //CREATED
				$ticketStatusIt6[$i]['ticket']['win'] = $transaction["win"];
				$ticketStatusIt6[$i]['ticket']['rate'] = $transaction["rate"];
				$ticketStatusIt6[$i]['ticket']['status'] = Webservice_Livebetting::STATUS_CONFIRM_CREATION;
			} else if ($status == 7) { //STORNO
				$ticketStatusIt6[$i]['ticket']['timeCanceled'] = gmdate("Y-m-d H:i:s");
				$ticketStatusIt6[$i]['ticket']['status'] = Webservice_Livebetting::STATUS_CANCELED;
			} else if ($status == 3) { //WINING
				$ticketStatusIt6[$i]['ticket']['status'] = Webservice_Livebetting::STATUS_WINING;
			} else if ($status == 4) { //LOST
				$ticketStatusIt6[$i]['ticket']['status'] = Webservice_Livebetting::STATUS_PAID;
				//Datum u prohranych tiketu se nepredava.
				//$ticketStatusIt6[$i]['ticket']['timePaid'] = //gmdate("Y-m-d H:i:s", $transaction["date_paid"]);
				$ticketStatusIt6[$i]['ticket']['isLoss'] = 1;
			} else if ($status == 8) { //WIN
				$ticketStatusIt6[$i]['ticket']['status'] = Webservice_Livebetting::STATUS_PAID;
				$ticketStatusIt6[$i]['ticket']['timePaid'] = gmdate("Y-m-d H:i:s", $transaction["date_paid"]);
				$ticketStatusIt6[$i]['ticket']['isLoss'] = 0;
			} else if ($status == 6) { //RESETTLED
				$ticketStatusIt6[$i]['ticket']['status'] = Webservice_Livebetting::STATUS_RESETTLED;
				$ticketStatusIt6[$i]['ticket']['timeResettled'] = gmdate("Y-m-d H:i:s", $transaction["date_resettled"]);
			} else {
				$ticketStatusIt6[$i]['ticket']['status'] = Webservice_Livebetting::STATUS_UNKNOWN;
			}
		}

		$responses = Webservice_Livebetting::changeTicketStatus($ticketStatusIt6);

		for ($i = 0; $i < sizeof($responses); ++$i) {
			$response = $responses[$i];
			if ( !empty($response['success']) ) {
				$responseToSmartbetting[$response['transactionId']] = 1;
			} else {
				$responseToSmartbetting[$response['transactionId']] = 0;
			}
		}

		return $responseToSmartbetting;
	}

	private static function checkSecurity($login, $password) {
		$ip = It6_Php::getRemoteAddr();
		if ($login == LIVE_CLIENT_LOGIN && $password == LIVE_CLIENT_PASSWORD
				&& in_array($ip, explode(';', LIVE_CLIENT_IP))) {
			return true;
		} else {
			throw new It6_XmlRpc_Exception('Wrong login or password or ip (' . $ip . ').');
		}
	}

	public static function decodeUtf($string) {
		//return  preg_replace("#(\\\\x[0-9A-Fa-f]{2})#e", "chr(hexdec('\\1'))", $string);
		$cb = function($match) {
			if ('u' == $match[1])
				return mb_convert_encoding("&#x{$match[2]};", 'UTF-8', 'HTML-ENTITIES');
			else if ('x' == $match[1])
				return chr(hexdec($match[2]));
			else
				return $match[0];
		};
		return preg_replace_callback("#\\\\(?:(u)([0-9A-Fa-f]{4})|(x)([0-9A-F]{2}]))#", $cb, $string);
	}

}