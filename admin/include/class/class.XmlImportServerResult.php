<?php
/**
 * @package    XML
 */

/**
 * Trida pro stanoveni vysledku sazek
 *
 * 
 * <code>
 * 
 * </code>
 *
 * @package    Ciselniky
 */

class XMLImportServerResult{
	
/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */              
private  $dbGame;

/**
 * id zapasu
 * @access private
 * @var integer
 */              
private  $match_id;

/**
 * identifikator ktery urcuje zda jde o vysledek pres XML nebo ne
 * @access private
 * @var bool
 */              
private static $noXmlResult = false;

/**
 * asociativni pole s vysledky
 * @access private
 * @var array
 */              
private static $noXmlArray = array();

/**
 * pole vysledku
 * @access private
 * @var integer
 */              
private  $result_ar = Array();

/**
 * pole sportu ktere se pocitaji podle vysledku v prodlouzeni u viteze zapasu
 * @access private
 * @var array
 */              
private  $sport_ar = Array(1006,1011,1026,1025);

/**
 * pole sportu ktere se pocitaji podle vysledku v prodlouzeni u vsecho
 * @access private
 * @var array
 */              
private  $sport_ar2 = Array(1006,1026,1025);

public $processedBetsOk = 0;

public $processedBetsError = 0;

private $notices = array();

private $errors = array();
/**
* Pokud neni identifikator spojeni predan vytvori se nove spojeni
*/
 public function __construct($result,$match_id){
  	
  	$this->dbGame = Zend_Registry::get('zdb_game');
  	$this->match_id = $match_id;
/*
	echo 'result constr';
*/
  	if(!self::$noXmlResult){
  		
  	  $scoreInfo = $result->getElementsByTagName('ScoreInfo');
  	
      $score = $scoreInfo ->item(0)->getElementsByTagName('Score');
  	 	
      for ($i = 0; $i < $score->length; $i++){
    	
    	$atr = $score->item($i)->getAttribute("Type");
    	$val = $score->item($i)->nodeValue;
    	
    	$this->result_ar[$atr] = $val;
//    	var_dump($val);
      }
    
  	}else{
  		
  		foreach(self::$noXmlArray as $k=>$h){
  			
  			$this->result_ar[$k] = $h;
  			
  		}
  		
  	}
  	
  	return $this->SetTypes();
/*
    echo 'matchId:'.$match_id."<br>";
  	 print_r($this->result_ar);
*/
 }

	public function getNotices() {
		return $this->notices;
	}
	
	public function getErrors() {
		return $this->errors;
	}
 /**
  * Funkce ktera nastavi vysledky pro jednotlive druhy
  */

public function SetTypes(){
	if($this->match_id == NULL) return;
	
	$betTypes = array(
		16 => array('method' => 'FirstHalfResult', 'subTypeId' => 23), 		//1. poločas
		17 => array('method' => 'SecondHalfResult', 'subTypeId' => 23),  	// 2. poločas
		18 => array('method' => 'HandicapResult', 'subTypeId' => 23),		// handicap
		19 => array('method' => 'MatchResult', 'subTypeId' => 23), 			// Zápas
		20 => array('method' => 'UnderOverResult', 'subTypeId' => 25), 		// Více/méně
		22 => array('method' => 'WinnerResult', 'subTypeId' => 29), 		// Vítěz zápasu
		23 => array('method' => 'ExactResult', 'subTypeId' => 0), 			// Přesný výsledek
		24 => array('method' => 'DblMatchResult', 'subTypeId' => 24), 		// Dvojitá šance
		25 => array('method' => 'AsianHandicapResult', 'subTypeId' => 29), 	// Asijský handicap
		26 => array('method' => 'FirstThirdResult', 'subTypeId' => 23), 	// 1. třetina
		27 => array('method' => 'SecondThirdResult', 'subTypeId' => 23), 	// 2. třetina
		28 => array('method' => 'ThirdThirdResult', 'subTypeId' => 23), 	// 3. třetina
		// Advance generally has not be determined by single result, but it could more than one result
		//33 => array('method' => 'Advance', 'subTypeId' => 29), 				// Advance
		35 => array('method' => 'DrawNoBet', 'subTypeId' => 29), 			// Sázka bez remízy
		50 => array('method' => 'HalfTimeEnd', 'subTypeId' => 107),  // Poločas-konec
		51 => array('method' => 'FirstHalfUnderOverResult', 'subTypeId' => 25), // 1. poločas více/méně
		88 => array('method' => 'FirstThirdUnderOverResult', 'subTypeId' => 25), 	// 1. třetina více/méně
		89 => array('method' => 'SecondThirdUnderOverResult', 'subTypeId' => 25), 	// 2. třetina více/méně
		90 => array('method' => 'ThirdThirdUnderOverResult', 'subTypeId' => 25), 	// 3. třetina více/méně
		96 =>  array('method' => 'EvenOddResult', 'subTypeId' => 121), // 96 / lichá/sudá
		115 => array('method' => 'BothTeamsGoal', 'subTypeId' => 173),  // Oba týmy vstřelí gólx
		116 => array('method' => 'HomeTeamGoal', 'subTypeId' => 173),  // Tým 1 vstřelí gól
		117 => array('method' => 'AwayTeamGoal', 'subTypeId' => 173),  // Tým 2 vstřelí gól
	);
	$allowedBetTypes = implode(',',array_values(array_keys($betTypes)));

/*
		if ($subtypeId == 23 && $typeId == 16)    	 $this->FirstHalfResult($betId);
		else if ($subtypeId == 23 && $typeId == 17)	$this->SecondHalfResult($betId);  //IT6
		else if ($subtypeId == 23 && $typeId == 18) $this->HandicapResult($betId, $text);
		else if ($subtypeId == 23 && $typeId == 19)	$this->MatchResult($betId);
		else if ($subtypeId == 25 && $typeId == 20)	$this->UnderOverResult($betId, $eventId, $text);
		else if ($subtypeId == 29 && $typeId == 22)	$this->WinnerResult($betId, $eventId);

		else if ($subtypeId == 24 && $typeId == 24)	$this->DblMatchResult($betId);
		else if ($subtypeId == 29 && $typeId == 25)	$this->AsianHandicapResult($betId, $eventId, $text);
		else if ($subtypeId == 23 && $typeId == 26)	$this->FirstThirdResult($betId);
		else if ($subtypeId == 23 && $typeId == 27)	$this->SecondThirdResult($betId);
		else if ($subtypeId == 23 && $typeId == 28) $this->ThirdThirdResult($betId);
		else if ($subtypeId == 29 && $typeId == 33)	$this->Advance($betId, $eventId);
		else if ($subtypeId == 29 && $typeId == 35)	$this->DrawNoBet($betId);
*/

/*
	$this->typ_id = array(
	    "01"=>array("type"=>18,"callback"=>"Handicap"),
	    "02"=>array("type"=>23,"callback"=>"Result"),
	    "10"=>array("type"=>19,"callback"=>"Match"),
	    "20"=>array("type"=>22,"callback"=>"Winner"),
	    "60"=>array("type"=>20,"callback"=>"UnderOver"),
	    "70"=>array("type"=>25,"callback"=>"AsianHandicap")
	);
*/


	$sazky = $sazky_typ = array();

	if(!self::$noXmlResult) {
		$sql = "SELECT sazka_id
				FROM sazky
				WHERE
					status<>3 AND
					status<>1 AND
					overena=0 AND platna_do<now() AND
					typ_id IN (".$allowedBetTypes.") AND
					betradar_match_id=".intval($this->match_id);
	} else {
		$sql = "SELECT sazka_id
				FROM sazky
				WHERE status<>3 AND
					status<>1 AND
					overena=0 AND platna_do<now() AND
					sazka_id=".intval($this->match_id);
	}
	
	try {
		$res = $this->dbGame->query($sql);
	}
	catch (Exception $e) {
		It6_Log::warn(
			"Error setting score betradarMatchId/sazkaId #'%betradarMatchId%' was not ready for setting score.",
			It6_Log::TAG_BETRADAR_SCORE_OPERATION,
			array('betradarMatchId' => $this->match_id)
		);
		return;
	}

	foreach ($res->fetchAll() as $row)
		$sazky[ $row['sazka_id'] ] = true;

	foreach (array_keys($sazky) as $betId) {
		$sql = "select sazka1_id,sazka2_id from sazka_kombinace where sazka1_id=$betId or sazka2_id=$betId";
		try {
			$res = $this->dbGame->query($sql);
			while($row = $res->fetch()) {
				$sazky[ $row['sazka1_id'] ] = true;
				$sazky[ $row['sazka2_id'] ] = true;
			}
		}
		catch (Exception $e) {}
	}
	$sazky = array_keys($sazky);

	if(0 == count($sazky))
		return;

	$sql = "
		SELECT sazka_id,typ_id,podtyp_id,text,udalost_id
		FROM sazky
		WHERE status<>3 AND status<>1 AND overena=0
			AND typ_id IN (".$allowedBetTypes.") AND sazka_id IN (".implode(",",$sazky).")";

	try {
		$res = $this->dbGame->query($sql);
		if (0 == $res->rowCount())
			return;
	}
	catch(Exception $e) {
		return;
	}

	while($row = $res->fetch()) {
		// Zruseny zapas
		if(isset($this->result_ar['C']))
			break;
		
		#Urceni vysledku jedn sazek#
		$subtypeId = $row['podtyp_id'];
		$typeId = $row['typ_id'];
		$betId = $row['sazka_id'];
		$eventId = $row['udalost_id'];
		$text = $row['text'];

		$method = $betTypes[$typeId]['method'];
		$subType = $betTypes[$typeId]['subTypeId'];

		if ($subType == $subtypeId || $subType == 0)
			$this->$method($betId, $eventId, $text, $subtypeId);
		
/*
		if ($subtypeId == 23 && == 16)    	 $this->FirstHalfResult($betId);
		else if ($subtypeId == 23 && $typeId == 17)	$this->SecondHalfResult($betId);  //IT6
		else if ($subtypeId == 23 && $typeId == 18) $this->HandicapResult($betId, $text);
		else if ($subtypeId == 23 && $typeId == 19)	$this->MatchResult($betId);
		else if ($subtypeId == 25 && $typeId == 20)	$this->UnderOverResult($betId, $eventId, $text);
		else if ($subtypeId == 29 && $typeId == 22)	$this->WinnerResult($betId, $eventId);

		else if ($subtypeId == 24 && $typeId == 24)	$this->DblMatchResult($betId);
		else if ($subtypeId == 29 && $typeId == 25)	$this->AsianHandicapResult($betId, $eventId, $text);
		else if ($subtypeId == 23 && $typeId == 26)	$this->FirstThirdResult($betId);
		else if ($subtypeId == 23 && $typeId == 27)	$this->SecondThirdResult($betId);
		else if ($subtypeId == 23 && $typeId == 28) $this->ThirdThirdResult($betId);
		else if ($subtypeId == 29 && $typeId == 33)	$this->Advance($betId, $eventId);
		else if ($subtypeId == 29 && $typeId == 35)	$this->DrawNoBet($betId);
*/

		$sazky_typ[$betId] = $typeId;
	}

	foreach($sazky as $betId){
/*
		var_dump('** davam vysledek: '.$betId);
*/
		$sql_data = '';
		if(isset($this->result_ar['FT'])) $sql_data .= ',"'.Help::Slash($this->result_ar['FT']).'"';else $sql_data .= ",NULL";
		if(isset($this->result_ar['HT'])) $sql_data .= ',"'.Help::Slash($this->result_ar['HT']).'"';else $sql_data .= ",NULL";
		if(isset($this->result_ar['OT'])) $sql_data .= ',"'.Help::Slash($this->result_ar['OT']).'"';else $sql_data .= ",NULL";
		if(isset($this->result_ar['1P'])) $sql_data .= ',"'.Help::Slash($this->result_ar['1P']).'"';else $sql_data .= ",NULL";
		if(isset($this->result_ar['2P'])) $sql_data .= ',"'.Help::Slash($this->result_ar['2P']).'"';else $sql_data .= ",NULL";
		if(isset($this->result_ar['3P'])) $sql_data .= ',"'.Help::Slash($this->result_ar['3P']).'"';else $sql_data .= ",NULL"; 
		if(isset($this->result_ar['AP'])) $sql_data .= ',"'.Help::Slash($this->result_ar['AP']).'"';else $sql_data .= ",NULL"; 

		$resultDetail = array(
			'FT'=> (isset($this->result_ar['FT']) ? $this->result_ar['FT'] : 'N/A'),
			'HT'=> (isset($this->result_ar['HT']) ? $this->result_ar['HT'] : 'N/A'),
			'OT'=> (isset($this->result_ar['OT']) ? $this->result_ar['OT'] : 'N/A'),
			'1P'=> (isset($this->result_ar['1P']) ? $this->result_ar['1P'] : 'N/A'),
			'2P'=> (isset($this->result_ar['2P']) ? $this->result_ar['2P'] : 'N/A'),
			'3P'=> (isset($this->result_ar['3P']) ? $this->result_ar['3P'] : 'N/A'),
			'AP'=> (isset($this->result_ar['AP']) ? $this->result_ar['AP'] : 'N/A')
		);

		if (array_key_exists($betId, $sazky_typ) && ($sazky_typ[$betId] == 19 || $sazky_typ[$betId] == 22)) {
			$sql = "replace into sazky_result (sazka_id,ft,ht,ot,1t,2t,3t,ap) values (".intval($betId)."".$sql_data.")";
			try {
				$res = $this->dbGame->query($sql);
			}
			catch (Exception $e) {}

			if (isset($this->result_ar['FT'])) {
				if (Zend_Registry::get('ws')->Bet->setBetScore($betId,$this->result_ar['FT'])) {
					$this->notices[] = i18n::tr('Bet {0} score (FT) {1} was set.',$betId, $this->result_ar['FT']);
					
					It6_Log::info(
						"Bet #'%bet%' score '%score%' was set.",
						It6_Log::TAG_BETRADAR_SCORE_OPERATION,
						array(
							'bet'=> $betId,
							'score' => $this->result_ar['FT'],
							'betradarMatchId' => $this->match_id,
							'moreResultInfo' => $resultDetail
							)
					);
				}
				else {
					if ($this->sendEmailOnError)
						$this->sendEmail('[Betradar] score import error', "bet : $betId, score: ". $this->result_ar['FT'].", betradarMatchId: ".$this->match_id.", moreResultInfo: $resultDetail");
					$this->errors[] = i18n::tr('Bet {0} error setting score (FT) {1}.',$betId, $this->result_ar['FT']);
					It6_Log::warn(
						"Error bet #'%bet%' setting score '%score%'.",
						It6_Log::TAG_BETRADAR_SCORE_OPERATION,
						array(
							'bet'=> $betId,
							'score' => $this->result_ar['FT'],
							'betradarMatchId' => $this->match_id,
							'moreResultInfo' => $resultDetail)
					);
				}
			}
		}
	}

	//return UiUitl::printMessages($this->notices).UiUitl::printErrors($this->errors);
}

private function getEventSportId($eventId, $clearCache = false) {
	static $cache = array();
	if ($clearCache)
		$cache = array();
	if (array_key_exists($eventId, $cache))
		return $cache[$eventId];
	else {
		$sql = "select sport_id from udalost where udalost_id=".intval($eventId);
		try {
			$row = $this->dbGame->query($sql)->fetchAll();
			$sportId = (empty($row) ? false : $row[0]['sport_id']);
			$cache[$eventId] = $sportId;
			return $sportId;
		}
		catch (Exception $e) {
			return false;
		}
	}
}

private function getHomeAway($sportId, &$home, &$away, $typeForce = 'FT') {
	if (!in_array($sportId, $this->sport_ar2)) {
		if(!isset($this->result_ar[$typeForce]))
			return false;
		list($home,$away) = explode(":",$this->result_ar[$typeForce]);
	} else {
		if (isset($this->result_ar['OT']))      list($home,$away) = explode(":",$this->result_ar['OT']);
		else if (isset($this->result_ar['AP'])) list($home,$away) = explode(":",$this->result_ar['AP']);
		else if (isset($this->result_ar['FT'])) list($home,$away) = explode(":",$this->result_ar['FT']);
		else return false;
	}
	return true;
}

/**
* Funkce ktera nastavi vysledky pro Postup
*
* prodlouzeni se zapocitava u basketu u vsech typu sazek, u hokeje u viteze zapasu, u baseballu u vseho, u americkeho fotbalu u vseho
*
* @param int $sazka_id id sazky
* @param int $udalost_id id udalosti
*/

public function Advance($sazka_id,$udalost_id){

	if ( false === ($sportId = $this->getEventSportId($udalost_id)) )
		return;

	if (!$this->getHomeAway($sportId, $home, $away))
		return;

	if (!is_numeric($home) || !is_numeric($away))
		return;
	
	if ($home > $away) $won = "152";
	else if($home < $away) $won = "153";
	else return;

	$this->setBetResult($won, $sazka_id);
	/*if(DB::isError($res)) {
		It6_Log::warn(
		"Error bet update #'%bet%' setting result '%result%'.",
		It6_Log::TAG_BETRADAR_OPERATION,
		array( 'bet'=> $sazka_id, 'result' => $won)
		);
		} else {
		It6_Log::info(
		"Bet #'%bet%' result was set '%result%'",
		It6_Log::TAG_BETRADAR_OPERATION,
		array( 'bet'=> $sazka_id, 'result' => $won)
		);
	}*/
}
 
/**
* Funkce ktera nastavi vysledky pro Sazka bez remizy
*
* prodlouzeni se zapocitava u basketu u vsech typu sazek, u hokeje u viteze zapasu, u baseballu u vseho, u americkeho fotbalu u vseho
*
* @param int $sazka_id id sazky
*/

public function DrawNoBet($sazka_id){

	if(!isset($this->result_ar['FT']))
		return;

	list($home,$away) = explode(":", $this->result_ar['FT']);
	if (!is_numeric($home) || !is_numeric($away))
		return;

	if($home == $away){
		$sql = "update sazky set status=1,vysledek='' where sazka_id=".intval($sazka_id);
		$res = $this->dbGame->query($sql);

		//IT6: this is probably bad refactoring, it seems this shouldn't set status=3
		//$this->setBetResult($won,$sazka_id);

		/*if(DB::isError($res)) {
		 It6_Log::warn(
			"Error bet update #'%bet%' setting result '%result%'.",
			It6_Log::TAG_BETRADAR_OPERATION,
			array( 'bet'=> $sazka_id, 'result' => $won)
			);
			} else {
			It6_Log::info(
			"Bet #'%bet%' result was set '%result%'",
			It6_Log::TAG_BETRADAR_OPERATION,
			array( 'bet'=> $sazka_id, 'result' => $won)
			);
			}*/
			
	}else{
		$won = ($home > $away ? "152" : "153");
		$this->setBetResult($won,$sazka_id);
	}
}


/**
* Funkce ktera nastavi vysledky pro Under over
*
* prodlouzeni se zapocitava u basketu u vsech typu sazek, u hokeje u viteze zapasu, u baseballu u vseho, u americkeho fotbalu u vseho
*
* @param int $sazka_id id sazky
* @param int $udalost_id id udalosti
* @param string $text text
*/
public function UnderOverResult($sazka_id, $udalost_id, $text, $subtypeId = null, $type = 'FT'){

	$total_score = false;

	if ( false === ($sportId = $this->getEventSportId($udalost_id)) )
		return;

	if (!$this->getHomeAway($sportId, $home, $away, $type))
		return;

	$t = $this->getAndValidateResult($type);

	if ($t == false) return;
	$home = $t['home'];
	$away = $t['away'];

	if(!mb_ereg('^(.+)-(.+)[[:space:]]+<?([0-9]+[,.]?[0-9]*)>?$',$text,$ar)) {
		It6_Log::warn(
			"Unable to parse value of under-over: betId %betId%, name %name%.",
			It6_Log::TAG_BOOKMAKER_OPERATION,
			array(
				'betId' => $sazka_id,
				'name' => $text
				)
		);
		return;
	}
	$ar[3] = str_replace(",",".",$ar[3]);
	$total_score = $home + $away;

	if(false === $total_score)
		return;

	$won = ($total_score > $ar[3] ? "144" : "145");
	$this->setBetResult($won,$sazka_id);
}

public function FirstHalfUnderOverResult($sazka_id, $udalost_id, $text) {
	$this->UnderOverResult($sazka_id, $udalost_id, $text, null, 'HT');
}

/**
* Funkce ktera nastavi vysledky pro Asijsky handicap
*
* prodlouzeni se zapocitava u basketu u vsech typu sazek, u hokeje u viteze zapasu, u baseballu u vseho, u americkeho fotbalu u vseho
*
* @param int $sazka_id id sazky
* @param int $udalost_id id udalosti
* @param string $text text
*/
public function AsianHandicapResult($sazka_id,$udalost_id,$text){

	if ( false === ($sportId = $this->getEventSportId($udalost_id)) )
		return;

	if (!$this->getHomeAway($sportId, $home, $away))
		return;

	if(!mb_ereg('^(.+)[[:space:]]+([-+]?[0-9]+[,.]?[0-9]*) -(.+)[[:space:]]+([-+]?[0-9]+[,.]?[0-9]*)$',$text,$ar)) {
		It6_Log::warn(
			"Unable to parse value of asian handicap: betId %betId%, name %name%.",
			It6_Log::TAG_BOOKMAKER_OPERATION,
			array (
				'betId' => $sazka_id,
				'name' => $text
			)
		);
		return;
	}
	$ar[2] = str_replace(",",".",$ar[2]);
	$home = $home  + $ar[2];

	if (!is_numeric($home) || !is_numeric($away))
		return;

	$won = ($home > $away ? "152" : $won = "153");
	$this->setBetResult($won,$sazka_id);
}

public function setBetResult($won,$sazka_id) {
	$sql = "update sazky set status=".It6_Models_Bet::STATUS_EVALUATED.",vysledek='".$won.";' where sazka_id=".intval($sazka_id);
	try {
		$res = $this->dbGame->query($sql);
		$this->processedBetsOk++;
		It6_Log::info(
			"Bet #'%bet%' result was set '%result%'",
			It6_Log::TAG_BETRADAR_RESULT_OPERATION,
			array( 'bet'=> $sazka_id, 'result' => $won)
		);
		$this->notices[] = i18n::tr('Bet {0} result {1} was set.',$sazka_id, $won);
	}
	catch (Exception $e) {
		$this->processedBetsError++;
		It6_Log::warn(
			"Error bet update #'%bet%' setting result '%result%'.",
			It6_Log::TAG_BETRADAR_RESULT_OPERATION,
			array( 'bet'=> $sazka_id, 'result' => $won)
		);
		$this->errors[] = i18n::tr('Bet {0} error setting result {1}.',$sazka_id, $won);
	}
}

/**
* Funkce ktera nastavi vysledky pro Viteze zapasu
*
* prodlouzeni se zapocitava u basketu u vsech typu sazek, u hokeje u viteze zapasu, u baseballu u vseho, u americkeho fotbalu u vseho
*
* @param int $sazka_id id sazky
* @param int $udalost_id id udalosti
*/

public function WinnerResult($sazka_id,$udalost_id){
	if ( false === ($sportId = $this->getEventSportId($udalost_id)) )
		return;

	if (!$this->getHomeAway($sportId, $home, $away))
		return;
	if(!is_numeric($home) || !is_numeric($away))
		return;
	if ($home == $away)
		return;
	$won = ($home > $away ? "152" : "153");

	$this->setBetResult($won, $sazka_id);
}

/**
* Funkce ktera nastavi vysledky pro prvni tretinu
* @param int $sazka_id id sazky
*/

public function FirstThirdResult($sazka_id){
	$per1 = $this->getAndValidateResult('1P');
	if ($per1 == false) return;
	$home = $per1['home'];
	$away = $per1['away'];
	
	if ($home > $away)      $won = "138";
	else if ($home < $away) $won = "140";
	else                    $won = "139";

	$this->setBetResult($won,$sazka_id);
}

/**
* Funkce ktera nastavi vysledky pro druhou tretinu
* @param int $sazka_id id sazky
*/

public function SecondThirdResult($sazka_id){
	$per2 = $this->getAndValidateResult('2P');
	if ($per2 == false) return;
	$home = $per2['home'];
	$away = $per2['away'];

 	if($home > $away)      $won = "138";
 	else if($home < $away) $won = "140";
 	else                   $won = "139";

	$this->setBetResult($won,$sazka_id);
}

/**
* Funkce ktera nastavi vysledky pro treti tretinu
* @param int $sazka_id id sazky
*/
public function ThirdThirdResult($sazka_id) {
	$per3 = $this->getAndValidateResult('3P');
	if ($per3 == false) return;
	$home = $per3['home'];
	$away = $per3['away'];

	if($home > $away)      $won = "138";
	else if($home < $away) $won = "140";
	else                   $won = "139";

	$this->setBetResult($won, $sazka_id);
}
 
public function FirstThirdUnderOverResult($sazka_id, $udalost_id,$text){
	$this->UnderOverResult($sazka_id, $udalost_id, $text, null, '1P');
}

public function SecondThirdUnderOverResult($sazka_id, $udalost_id, $text){
	$this->UnderOverResult($sazka_id, $udalost_id, $text, null, '2P');
}

public function ThirdThirdUnderOverResult($sazka_id, $udalost_id, $text){
	$this->UnderOverResult($sazka_id, $udalost_id, $text, null, '3P');
}

/**
* Funkce ktera nastavi vysledky pro prvni polocas
*
* @param int $sazka_id id sazky
*/
public function FirstHalfResult($sazka_id) {
	$ht = $this->getAndValidateResult('HT');
	if ($ht == false) return;
	$home = $ht['home'];
	$away = $ht['away'];

	if($home > $away)      $won = "138";
	else if($home < $away) $won = "140";
	else                   $won = "139";
	
	$this->setBetResult($won,$sazka_id);
}

public function SecondHalfResult($sazka_id) {
	$ht = $this->getAndValidateResult('HT');
	if ($ht == false) return;
	$homeFirst = $ht['home'];
	$awayFirst = $ht['away'];

	$ft = $this->getAndValidateResult('FT');
	if ($ft == false) return;
	$homeFull = $ft['home'];
	$awayFull = $ft['away'];
	
	$home = $homeFull - $homeFirst;
	$away = $awayFull - $awayFirst;

	if ( ($home < 0) || ($away < 0) ) {
		It6_Log::warn(
			"Bet %betId%  result bogus. First/Second half result mismatch.",
			It6_Log::TAG_BOOKMAKER_OPERATION,
			array(
				'betId' => $betId,
				'ft' => $ft,
				'ht' => $ht )
		);
		return;
	}
	
	if($home > $away)      $won = "138";
	else if($home < $away) $won = "140";
	else                   $won = "139";
	
	$this->setBetResult($won,$sazka_id);
}


public function ExactResult($sazka_id, $eventId = NULL, $sportId = NULL, $subTypeId) {
	$ft = $this->getAndValidateResult('FT');
	if ($ft == false) return;

	$cols = It6_Models_Bet::getColumnsAndNames($subTypeId);
	$score = $ft['home'].':'.$ft['away'];

	$won = false;
	foreach ($cols as $k => $v) {
		if ($v['nazev'] == $score ) {
			$won = $v['sloupec_id'];
			break;
		}
	}
	if ($won) {
		$this->setBetResult($won,$sazka_id);
	} else {
		It6_Log::warn(
			"Bet %betId%  exact result bogus. Couldnt find colName %colId%.",
			It6_Log::TAG_BOOKMAKER_OPERATION,
			array(
				'betId' => $sazka_id,
				'colId' => $subTypeId,
				'ft' => $ft )
		);
		return;
	}
}

public function HalfTimeEnd($sazka_id) {

/*
1/1 - domácí tým povede v poločase a vyhraje i celý zápas
1/X - domácí tým povede v poločase a zápas skončí remízou
1/2 - v poločase povede domácí tým, ale nakonec vyhrají hosté
X/X - v poločase i na konci zápasu bude remíza
X/1 - v poločase nerozhodný stav a nakonec zápas vyhrají domácí
X/2 - v poločase nerozhodný stav a nakonec zápas vyhrají hosté
2/2 - hostující tým povede v poločase a vyhraje i celý zápas
2/X - hostující tým povede v poločase a zápas skončí remízou
2/1 - v poločase povede hostující tým, ale nakonec vyhrají domácí 
*/

	$ht = $this->getAndValidateResult('HT');
	if ($ht == false) return;
	$homeFirst = $ht['home'];
	$awayFirst = $ht['away'];

	$ft = $this->getAndValidateResult('FT');
	if ($ft == false) return;
	$homeFull = $ft['home'];
	$awayFull = $ft['away'];

	$home = $homeFull - $homeFirst;
	$away = $awayFull - $awayFirst;

	if ( ($home < 0) || ($away < 0) ) {
		It6_Log::warn(
			"Bet %betId%  result bogus. First/Second half result mismatch.",
			It6_Log::TAG_BOOKMAKER_OPERATION,
			array(
				'betId' => $betId,
				'ft' => $ft,
				'ht' => $ht )
		);
		return;
	}

	if($homeFirst > $awayFirst) {
		if ($homeFull > $awayFull)
			$won ='1606'; // 1/1
		else if ($homeFull < $awayFull)
			$won = '1608'; // 1/2
		else
			$won = '1607'; // 1/x
	}
	else if($homeFirst < $awayFirst) {
		if ($homeFull > $awayFull)
			$won ='1612'; // 2/1
		else if ($homeFull < $awayFull)
			$won = '1614'; // 2/2
		else
			$won = '1613'; // 2/x
	}
	else {
		if ($homeFull > $awayFull)
			$won ='1609'; // x/1
		else if ($homeFull < $awayFull)
			$won = '1611'; // x/2
		else
			$won = '1611'; // x/x
	}

	$this->setBetResult($won,$sazka_id);
	
}

public function EvenOddResult($sazka_id){
	$ft = $this->getAndValidateResult('FT');
	if ($ft == false) return;
	$home = $ft['home'];
	$away = $ft['away'];
	$total = $home + $away;

	if ($total % 2) 
		$won = "1668";
	else
		$won = "1669";
	
	$this->setBetResult($won,$sazka_id);
}

public function BothTeamsGoal($sazka_id) {
	$ht = $this->getAndValidateResult('HT');
	if ($ht == false) return;
	$home = $ht['home'];
	$away = $ht['away'];

	if (($home > 0) && ($away > 0))
		$won = "1881";
	else
		$won = "1882";
	
	$this->setBetResult($won,$sazka_id);
}

public function HomeTeamGoal($sazka_id) {
	$ht = $this->getAndValidateResult('HT');
	if ($ht == false) return;
	$home = $ht['home'];
	$away = $ht['away'];

	if ($home > 0)
		$won = "1881";
	else
		$won = "1882";
	$this->setBetResult($won,$sazka_id);
}

public function AwayTeamGoal($sazka_id) {
	$ht = $this->getAndValidateResult('HT');
	if ($ht == false) return;
	$home = $ht['home'];
	$away = $ht['away'];

	if ($away > 0)
		$won = "1881";
	else
		$won = "1882";
	$this->setBetResult($won,$sazka_id);
}

public function getAndValidateResult($type = 'FT') {
	if(!isset($this->result_ar[$type]))
		return  false;
		
	list($home,$away) = explode(':', $this->result_ar[$type]);

	if(!is_numeric($home) || !is_numeric($away))
		return false;
	else return array('home' => $home, 'away' => $away);
}

/**
* Funkce ktera nastavi vysledky pro handicap
*
* @param int $sazka_id id sazky
* @param string $text text
*/
public function HandicapResult($sazka_id,$eventId = null,$text) {
	if(!isset($this->result_ar['FT']))
		return;

	if (!mb_ereg('^(.+)-(.+)[[:space:]]+<?([0-9]+[,.]?[0-9]*:[0-9]+[,.]?[0-9]*)>?$',$text,$ar)) {
		It6_Log::warn(
			"Unable to parse value of handicap: betId %betId%, name %name%.",
			It6_Log::TAG_BOOKMAKER_OPERATION,
			array(
				'betId' => $sazka_id,
				'name' => $text
				)
		);
		return;
	}
		
 	list($handicap_home,$handicap_away) = explode(":",$ar[3]);

 	list($home,$away) = explode(":",$this->result_ar['FT']);

	$home = $home + $handicap_home;
	$away = $away + $handicap_away;

	if (!is_numeric($home) || !is_numeric($away))
		return;

	if($home > $away)      $won = "138";
	else if($home < $away) $won = "140";
	else                   $won = "139";

	$this->setBetResult($won,$sazka_id);
}

/**
* Funkce ktera nastavi vysledky pro zapas
*
* @param int $sazka_id id sazky
*/

public function MatchResult($sazka_id){ 
	if (!isset($this->result_ar['FT']))
		return;

	list($home,$away) = explode(":",$this->result_ar['FT']);
	if(!is_numeric($home) || !is_numeric($away))
		return;

	if($home > $away)      $won = "138";
	else if($home < $away) $won = "140";
	else                   $won = "139";

	$this->setBetResult($won,$sazka_id);
}

/**
* Funkce ktera nastavi vysledky pro dvojitou sanci
*
* @param int $sazka_id id sazky
*/
public function DblMatchResult($sazka_id){
	if(!isset($this->result_ar['FT']))
		return;
	list($home,$away) = explode(":",$this->result_ar['FT']);
 	if(!is_numeric($home) || !is_numeric($away))
 		return;

 	if($home > $away)      $won = "141;142";
 	else if($home < $away) $won = "143;142";
 	else                   $won = "143;141";

	$this->setBetResult($won,$sazka_id);
}

/**
* Funkce ktera vytvari objekt Result
*
*
* @param DOM::XML $result objekt dom xml, muze byt NULL kdyz nepredavame xml data
* @param integer $match_id objekt spojeni s databazi
* @param PEAR::DB $dbGame objekt spojeni s databazi
* @param PEAR::DB $noxml objekt spojeni s databazi
*/
static public function SetResult($result=NULL,$match_id = NULL,$noxml=false){
	//echo "ok";exit;
	self::$noXmlResult = $noxml;
	$ob = new XMLImportServerResult($result,$match_id);
	return $ob;
}

/**
* Funkce ktera nastavi asociativni pole vysledku
*
*
* @param array $data pole s vysledky
*/
static public function SetArray($data){
	//self::$noXmlArray = array(); // WTF? next row overwrites this...
	self::$noXmlArray = $data;
}

/*
 public function sendEmail($subject,$body) {
		$mmail = new htmlMimeMail();
		$mmail->setTextCharset("UTF-8");
		$mmail->setHeadCharset("UTF-8");
		$mmail->setHTMLCharset("UTF-8");  
		$mmail->html_charset = "UTF-8";$mmail->text_encoding = "UTF-8";
		$mmail->setHTMLEncoding("quoted-printable");
		$mmail->setText($body);
		$mmail->setSubject($subject." ".It6_Date::dbNow());

		
		$mmail->send(array(BOOKMAIL));
	}
*/
}
