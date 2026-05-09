<?php
include('common.php');
include('htmlMimeMail/htmlMimeMail.php');

require_once(ROOT . 'common/class/class.Help.php');
require_once(ROOT . 'common/class/class.I18n.php');
require_once(ROOT . 'admin/include/class/class.DbUtil.php');
require_once(ROOT . 'admin/include/class/class.Preklady.php');
require_once(ROOT . 'admin/include/class/class.Template.php');
require_once(ROOT . 'admin/include/class/class.KombinaceDruh.php');
require_once(ROOT . 'admin/include/class/class.XmlImportServerResult.php');

class XMLImportServer{


/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */              
private  $dbGame;

/**
 * xml
 * @access private
 * @var string
 */              
private  $xmlData;

/**
 * pole typu betradar a nase
 * @access private
 * @var array
 */              
private  $typ_id;

/**
 * pole sportu ktere byly improtovany
 * @access private
 * @var array
 */              
private  $ImportedData = array();

/**
 * pole chyb ktere nastaly
 * @access private
 * @var array
 */              
private  $ErrorData = array();

/**
 * pole sportu a udalosti
 * @access private
 * @var array
 */              
private $sportAr;

/**
 * pole id sazek do kombinace
 * @access private
 * @var array
 */              
private $con_ids;

/**
 * indikace problemu pri importu
 * @access private
 * @var 
 */              
private $ErrorInd = 0;

/**
 * urcuje zda bylo spojeni na db predano nebo jsme si ho muslei vytvorit
 * @access private
 * @var bool
 */             
private $connect = false;

/**
 * nazev aktualniho sportu
 * @access private
 * @var string
 */             
private $sportName = "";

/**
 * nazev aktualni udalosti
 * @access private
 * @var string
 */             
private $udalostName = "";

const TEAM_NAME_LANG = 'cs';

/**
 * import bet initial status: 2 => suspended, 0=>active		
 * @access private
 * @var integer
 */
private $betInitialStatus = 0;

/**
 * vychozi stav aktualizovani kurzu z betradaru
 * @access private
 * @var integer
 */
private $betUpdate = 1;

/**
 * pole BBAS typ ID, ktere nemaji mit zapnuty BR update
 * @access private
 * @var array
 */
private $typesWithNoBrUpdate = array();

/**
 * List of BBAS sport IDs that are to be autoupdated by BR (satisfying condition)
 * @var array
 */
private $sportsWithBrUpdateOn = array();

/**
 * Id bookmakera pod kterem se pridavaji sazky z betradaru
 * @access private
 * @var integer
 */
private $betradarBookmakerId = 1;

private $sendEmailOnError = true;

private $sendEmailAlways = true;

private $betParentId = NULL;

private $invalidateSportMenuFrame = false;

public $file = NULL;



/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param PEAR::DB $dbGame objekt spojeni s databazi
*/
	public function __construct($dbGame = NULL) {
		//10.10.2012 added by Martin to see if this class gets used. It should not as importing is now hndled by bimportd daemon.
		It6_Log::info(
			"admin/include/class/class.XMLImportServer.php got instantiated!!!",
			'TMP DEBUG LOG',
			array()
		);

		$this->runImport = false;
		$this->betParentId = 'NULL';
		$this->currentBetradarMatchId = 'NULL';

		$this->db = Zend_Registry::get('db');
		$this->ws = Zend_Registry::get('ws');
		
		$this->betradarBookmakerId = $this->ws->Parameter->getGlobalParameter('betradar.Bet.BookmakerId');
		$this->betInitialStatus = $this->ws->Parameter->getGlobalParameter('betradar.Bet.InitialStatus');
		$this->betUpdate = $this->ws->Parameter->getGlobalParameter('betradar.Bet.Autoupdate.default');
		$this->bookmakerEmail = $this->ws->Parameter->getGlobalParameter('betradar.Email.notifyTo');
		$this->sendEmailOnError = $this->ws->Parameter->getGlobalParameter('betradar.Email.OnError');
		$this->sendEmailAlways = $this->ws->Parameter->getGlobalParameter('betradar.Email.Always');

		if (empty($this->betUpdate) || empty($this->betInitialStatus) || empty($this->betUpdate)) {
			It6_Log::warn(
				"Betradar XML script panic - some parameters are not set! betradarBookmakerId,betUpdate,betInitialStatus",
				It6_Log::TAG_BETRADAR_OPERATION,
				array()
			);
			$this->ErrorData[] = "Betradar XML script panic - some parameters are not set! betradarBookmakerId,betUpdate,betInitialStatus";
			return;
		}
		
		if($dbGame == NULL || !is_object($dbGame)) {
			$this->dbGame =  DbUtil::connectWebDb();
			$this->connect = true;
		} else {
			$this->dbGame = $dbGame;
		}
	}


public function getTime() {
	$a = explode (' ',microtime());
	return(double) $a[0] + $a[1];
} 

public function GetDataFromFile() {

	$this->filename = BETRADAR_IMPORT_QUEUE . $this->file;

	$this->runImport = true;

	if (!empty($this->filename)) { 
		$this->doc = new DOMDocument();
		
		$this->xmlStr = file_get_contents($this->filename);
		if ($pos = strpos($this->xmlStr, '<'))                                                                                                                                                 
			$this->xmlStr = substr($this->xmlStr, $pos) ;
		
		if (!$this->doc->loadXML($this->xmlStr)) {
				It6_Log::warn(
				"!! Betradar unable to load XML file %file%",
				It6_Log::TAG_BETRADAR_OPERATION,
				array('file' => $this->filename)
			);
			if (copy($this->filename,BETRADAR_IMPORT_ERROR.$this->file)) {
				unlink($this->filename);
				It6_Log::notice(
					"!! Betradar damaged file XML file %file%, moved succesfully to error folder",
					It6_Log::TAG_BETRADAR_OPERATION,
					array('file' => $this->filename)
				);
			}
			$this->sendEmail('Betradar import error','Betradar error: unable to load XML file');
			exit;
		}
		$this->xpathData = new DOMXPath($this->doc);
	}
}

/**
* Tato metoda nastavuje potrebne konfiguracni parametry
* return bool
*/
public function SetConfig(){

	$this->typ_id = array(
	    "01"=>array("type"=>18,"callback"=>"Handicap"),
	    "02"=>array("type"=>23,"callback"=>"Result"),
	    "10"=>array("type"=>19,"callback"=>"Match"),
	    "20"=>array("type"=>22,"callback"=>"Winner"),
	    "60"=>array("type"=>20,"callback"=>"UnderOver"),
	    "70"=>array("type"=>25,"callback"=>"AsianHandicap")
	);

	$this->sportsWithBrUpdateOn = array(1001); // 1001 = soccer

	$this->typesWithNoBrUpdate = array(18, 20, 23, 25);

	#Sporty a udalosti#
	$sql = "select a.sport_id,a.betradar_sport_id,a.nazev AS sport_nazev,b.udalost_id,b.nazev AS udalost_nazev from sport a inner join udalost b on a.sport_id=b.sport_id";

	$res = $this->dbGame->query($sql);
	
	if(DB::isError($res)) {$this->ErrorInd = 1;$this->ErrorData[]="Unable to fetch sport and tournament data";}
	
	$preklad = new Preklady();

	define('CZ_LANG_ID',1);

	while ($row = $res->fetchRow()) {
		if(!isset($this->sportAr[$row['sport_id']]['name'])) {
			$r = $preklad->FindPreklad($row['sport_nazev'],CZ_LANG_ID);  
			$this->sportAr[$row['sport_id']]['name'] = $r[CZ_LANG_ID];
			$this->sportAr[$row['betradar_sport_id']]['name'] = $r[CZ_LANG_ID];
		}
		
		$r = $preklad->FindPreklad($row['udalost_nazev'],CZ_LANG_ID);  
		$this->sportAr[$row['sport_id']]['udalost'][$row['udalost_id']] = $r[CZ_LANG_ID];
	}
}

/**
 * Query text from given node
 * @param DOMNode $node Context node (must contain &lt;Texts&gt;&lt;Text Language="?"&gt;text to be queried&lt;/Text&gt;...&lt;/Texts&gt;)
 * @param string|NULL $language Language to be queried or NULL for default value from global parameter
 */
public function queryTextValue(DOMNode $node, $language = null) {
	static $cacheLanguage = false;
	if (!isset($language)) {
		if (false === $cacheLanguage) {
			$cacheLanguage = It6_Models_Parameter::getDataByName(It6_Models_BetradarImportLog::PARAM_QUERIED_LANGUAGE);
			if (!empty($cacheLanguage))
				$cacheLanguage = $cacheLanguage['value'];
			if (empty($cacheLanguage))
				$cacheLanguage = 'cs';
		}
		$language = $cacheLanguage;
	}
	$text = $this->xpathData->query("Texts/Text[@Language=\"$language\"]/Value", $node);
	if (false === $text || 0 == $text->length) {
		$text = $this->xpathData->query("Texts/Text[1]/Value", $node);
		if (false === $text || 0 == $text->length)
			return false;
	}
	return $text->item(0)->nodeValue;
}

 /**
 * Tato metoda naparsuje prijate xml
 * return bool
 */
public function commitImport(){
	if (!empty($this->file)) {

		register_shutdown_function(array($this, 'cleanUp'));

		It6_Models_BetradarImportLog::init($this->file);

		$dataReadingBegin = $this->getTime(); 
		$this->GetDataFromFile();
		$dataReadingEnd = $this->getTime();

		It6_Log::notice(
			"Betradar XML import commited by cron, file %file%",
			It6_Log::TAG_BETRADAR_OPERATION,
			array('file' => $this->filename)
		);
		
		$dataParsingTimeBegin = $this->getTime(); 
		$this->SetConfig();
		
		$act_datum = It6_Date::dbNow();

		$this->db = Zend_Registry::get('admindb');
		
		$items = $this->doc->getElementsByTagName('BetradarBetData');

		#Root#
		for ($i = 0; $i < $items->length; $i++) {

			$sports = $items->item($i)->getElementsByTagName('Sports');
			#Sports#
			for ($j = 0; $j < $sports->length; $j++) {
				$sport = $sports->item($j)->getElementsByTagName('Sport');
				 #Sport#
				for ($k = 0; $k < $sport->length; $k++) {

					$sport_id = $sport->item($k)->getAttribute("BetradarSportID");

					if(is_object($sport->item($k)) && is_object($sport->item($k)->getElementsByTagName('value')->item(0))) {
						$this->sportName = $sport->item($k)->getElementsByTagName('value')->item(0)->nodeValue;
					}

					$logSportName = $this->queryTextValue($sport->item($k));

					$category = $sport->item($k)->getElementsByTagName('Category');

					#Category#
					for ($l = 0; $l < $category->length; $l++) {

						$logRegionName = $this->queryTextValue($category->item($l));

						$outright = $category->item($l)->getElementsByTagName('Outright');   
		
						 #Outright# //IT6 WTF??
						for ($m = 0; $m < $outright->length; $m++) {
								It6_Log::warn(
									"Trying to import OUTRIGHT!! %date%",
									It6_Log::TAG_BETRADAR_OPERATION,
									array('date' => $act_datum )
								);
						}

						//TEST
						$this->con_ids = array();
						
						$tournament = $category ->item($l)->getElementsByTagName('Tournament');    
						 #Tournament#
						for ($m = 0; $m < $tournament->length; $m++) {
							
							$udalost_id = $tournament->item($m)->getAttribute("BetradarTournamentID");

							$this->udalostName =  $tournament->item($m)->getElementsByTagName('Value')->item(0)->nodeValue;

							$logEventName = $this->queryTextValue($tournament->item($m));

							$ud = true;
							
							//$sql = "select a.sport_id,b.udalost_id from sport a inner join udalost b on a.sport_id=b.sport_id where b.betradar_udalost_id=".intval($udalost_id)." and a.betradar_sport_id=".intval($sport_id);
							$sql = "SELECT a.sport_id,b.udalost_id
									FROM sport a
									INNER JOIN udalost b ON a.sport_id=b.sport_id
									INNER JOIN udalost_betradar c ON b.udalost_id=c.udalost_id
									WHERE c.betradar_udalost_id=".intval($udalost_id);
	//var_dump($sql);
							$res =& $this->dbGame->query($sql);

							if(DB::isError($res));

							if ($res->NumRows() == 0) {
								$ud = false;
								$this->ErrorData[] = "Nebyl nalezen sport a udalost v databazi (tab udalost_betradar): ". $this->sportAr[$sport_id]['name']." -> BetRadarSportID(".$sport_id.") ".$this->udalostName." -> BetRadarUdalostID(".$udalost_id.") \n";
								It6_Log::warn(
									"Bet import error: Unable to find event (#'%event_id%'-'%event%') and sport(#'%sport_id%'-'%sport%') in table 'udalost_betradar'.",
									It6_Log::TAG_BETRADAR_BET_OPERATION,
									array('sport' => $this->sportAr[$sport_id]['name'], 'sport_id' => $sport_id, 'event' => $this->udalostName, 'event_id' => $udalost_id)
								);
							}
							if ($row =& $res->fetchRow()) {
								$j_sport_id   = $row['sport_id'];
								$j_udalost_id = $row['udalost_id'];
							}

							$match = $tournament->item($m)->getElementsByTagName('Match');

							#Match#
							for ($o = 0; $o < $match->length; $o++) {
								if($ud == true)
									$this->AddMatch($match->item($o),$sport_id,$udalost_id,$act_datum,$j_sport_id,$j_udalost_id);
							} //match
							$this->createCombinations();

							It6_Models_BetradarImportLog::log($logSportName, $logRegionName, $logEventName, $udalost_id);
	
						} //tournament
					} //category
				}//sport
			}//sports
		}//root
		$dataParsingTimeEnd = $this->getTime(); 

		echo "<pre>";print_r($this->ImportedData);
		echo "<pre>".print_r($this->ErrorData);echo "</pre>";

		$this->Finalize();
		It6_Models_BetradarImportLog::save(false);

		It6_Log::notice(
			"Betradar XML import finished. File %file%.",
			It6_Log::TAG_BETRADAR_OPERATION,
			array('file' => $this->filename, 'performace' => array('dataReading' => number_format($dataReadingEnd - $dataReadingBegin,2 ).' s','dataParsing' => number_format($dataParsingTimeEnd - $dataParsingTimeBegin,2) . ' s'))
		);
		
		$this->dbGame->disconnect();

	}
} //parseData
  
  
 /**
 * Tato metoda vklada jednotlive zapasy
 * @param object $match objekt 
 dom s informaci o zapasu
 * @param int $sport_id id sportu u betradaru
 * @param int $udalost_id id udalosti u betradaru
 * @param string $act_datum aktualni datum
 * @param int $_sport_id id v nasi databazi
 * @param int $_udalost_id id v nasi databazi
 * return void
 */
private function AddMatch($match,$sport_id,$udalost_id,$act_datum,$j_sport_id,$j_udalost_id){
	//$this->con_ids = array();
	$team = array();
	$teams = array();
	
	$match_id = $match->getAttribute("BetradarMatchID"); 
	$this->con_ids[$match_id] = array();
	#Result#
	$result = $match->getElementsByTagName('Result');

	for ($i = 0; $i < $result->length; $i++) {
		XmlImportServerResult::SetResult($result->item($i),$match_id);
	}

	foreach (array('1','2') as $type) {
		$teamName = $this->xpathData->query("//Match[@BetradarMatchID='$match_id']//Fixture/Competitors/Texts/Text[@Type='$type']/Text[@Language='".self::TEAM_NAME_LANG."']/Value");
		$betradarTeamId = $this->xpathData->query("//Match[@BetradarMatchID='$match_id']//Fixture/Competitors/Texts/Text[@Type='$type']/@ID");

		$teamName = $teamName->item(0)->nodeValue;
		$betradarTeamId = $betradarTeamId->item(0)->nodeValue;
		
		$team = array (
			'betradar_id' => $betradarTeamId,
			'type' => $type,
			'name' => $teamName
		);
	
	
		$this->updateTeamDb($team, $j_udalost_id);
		
		//teams is legacy
		$teams[$type]['id'] = $betradarTeamId;
		$teams[$type]['text'] = $teamName;
	}

	//platna do
	$matchDate = $this->xpathData->query("//Match[@BetradarMatchID='$match_id']/Fixture/DateInfo/MatchDate");

	$date = $matchDate->item(0)->nodeValue;
	$datum = It6_Date::fromBetradarToDb($date);

	//IT6: inserting bet by type
	$bet = $match->getElementsByTagName('Bet');
	//when bet doesnt exists

		for ($i = 0; $i < $bet->length; $i++) {
			$oddsType = $bet->item($i)->getAttribute("OddsType");
			//betradarId neni unikatni ke kazdemu typu sazku, proto ho retezim s typem.
			$betradarBetId = $match_id.$oddsType;
			
			if(isset($this->typ_id[$oddsType])) {
				if (!$this->betExists($betradarBetId) || $this->isAutoUpdateEnabled($betradarBetId) ) {
					//call proper method (Handicap, ..) by the typId
					$fce = $this->typ_id[$oddsType]["callback"];
					$this->$fce($bet->item($i),$match_id,$j_udalost_id,$j_sport_id,$this->typ_id[$oddsType]["type"],$datum,$teams,$act_datum,$betradarBetId);
					
					//IT6: bogus with wrong combinations assignemnt
					//$this->createCombinations();
					
				} else $this->ErrorData[] = "Sazka BR-ID $betradarBetId jiz existuje a neni samoaktualizovatelná\n";
			} else {
				$this->ErrorData[] = "Druh ".$oddsType." není definován a nemohl být importován \n";
				It6_Log::warn(
					"Bet import error: Type %$oddsType% is not defined.",
					It6_Log::TAG_BETRADAR_BET_OPERATION,
					array(
						'oddsType' => $oddsType,
						'betradarBetId' => $betradarBetId,
						'sport' => $j_sport_id,
						'event' =>  $j_udalost_id)
				);
			}

			$sql = "select sazka_id from sazky where betradar_sazka_id=".intval($match_id);
			$res2 =& $this->dbGame->query($sql);
			if(DB::isError($res2));

			while ($row2 =& $res2->fetchRow()) {
				reset($teams);
				foreach($teams as $tk=>$th) {
					$sql = "replace into tymy_sazky  (betradar_tym_id,sazka_id) values(".intval($teams[$tk]['id']).",".$row2['sazka_id'].")";
					$res =& $this->dbGame->query($sql);
					if (DB::isError($res)) {
						$this->ErrorData[] = "Databázová chyba: Neprovedl se dotaz propojení­ týmů a sázek ".__LINE__."\n";
						It6_Log::warn(
							"Bet import error: tab. tymy_sazky error.",
							It6_Log::TAG_BETRADAR_BET_OPERATION,
							array(
								'betId' => $row2['sazka_id'],
								'team' => $teams[$tk]['id'])
						);
					}
				}
			}
		}
}
  
  /**
 * 1 0 2 zapas
 * @param object $bet data
 * @param int $match_id
 * @param int $udalost_id 
 * @param int $sport_id 
 * @param int $typ_id
 * @param date $datum datum udalosti
 * @param array $teams  souperi
 * @param string $act_datum aktualni datum
 * return string  
 */
  private function Match($bet,$match_id,$udalost_id,$sport_id,$typ_id,$datum,$teams,$act_datum,$betradarBetId) {
//	 echo "* Match <br/>";
	 
	$podtyp_id = 23;
	$market = ' ZAPAS ';
	$odd = $bet->getElementsByTagName('Odds');

	$odds = array();

	//IT6: proc 3?
	//IT6: nacteni kurzu
	for ($i = 0; $i < 3; $i++) {
		if( !is_object($odd->item($i)) ) {
			$this->ErrorData[] = "Sázka BetRadarId(".$betradarBetId.") druh Zápas a Dvojitá šance nebyl importován: chybí­ kurz";
			It6_Log::warn(
				"Error bet import betradarBetId %betradarBetId%, market %market%, rate does not exists",
				It6_Log::TAG_BETRADAR_BET_OPERATION,
				array('betradarBetId' => $betradarBetId,'market' => $market)
			);
			return;
		}
		$odds[$odd->item($i)->getAttribute("OutCome")] = str_replace(",",".",$odd->item($i)->nodeValue);
	}

	$sql = "select vyhernost_min from bet_settings where typ_id=".intval($typ_id)." and podtyp_id=".intval($podtyp_id)." and sport_id=".intval($sport_id)." and udalost_id=".intval($udalost_id);
//	echo $sql;
	$res =& $this->dbGame->query($sql);
	if(DB::isError($res));

	if ($row =& $res->fetchRow())
		$vyhernost = $row['vyhernost_min'];
	 else
		$vyhernost = MIN_WON;
		
	reset($teams);

	$text = "";
	
	foreach($teams as $k=>$h) {
		
		if(!isset($h['preklad']))
			$text .= trim($h['text']);
		else
			$text .= "[".$h['preklad']."]";
		
		if($k == 1)  $text .= " - ";
	}
//echo 'xxx';
	//vypoctou se kurzy vcetne dvojite sance (tu betradar nedodava)
	$odds = Help::GetOdd($odds,$vyhernost);

	if(!$odds) {
		$this->ErrorData[] = "Nepodařilo se vypočí­tat kurzy pro druh Zápas a Dvojitá šance  a nebyl importován BetRadarId(".$match_id."): ".$text." \n";
		It6_Log::warn(
			"Error bet import betradarBetId %betradarBetId%, market %market%. Error in odd calculations (Help::GetOdd)",
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array(
				'betradarBetId' => $betradarBetId,
				'market' => $market
			)
		);
		return;
	}

	#vlozeni 1 0 2#
	$betColumns = array(
		0 => array( 'columnId' => '138', 'columnName' => '1' ),
		1 => array( 'columnId' => '139', 'columnName' => 'X' ),
		2 => array( 'columnId' => '140', 'columnName' => '2' )
	);
	
	//$this->dbGame->autoCommit(false);

	/*$sql = "select risk_limit from bet_settings where udalost_id=".intval($udalost_id)." and typ_id=".intval($typ_id)." and podtyp_id=23";
	$res =& $this->dbGame->query($sql);

	if ($row =& $res->fetchRow()) 
		$risk = $row['risk_limit'];
	else
		$risk = RISK_LIMIT;
	*/

	$risk = $this->getBetRiskLimit($udalost_id, $typ_id, $podtyp_id);

	//$betradarBetId = $match_id.$udalost_id;

	$bet = array(
		'currentDate' => $act_datum,
		'date' => $datum,
		'eventId' => $udalost_id,
		'typId' => $typ_id,
		'podtypId' => $podtyp_id,
		'text' => $text,
		'matchId' => $match_id,
		'risk' => $risk,
		'market' => $market,
		'betradarBetId' => $betradarBetId,
		'betColumns' => $betColumns,
		'odds' => $odds,
		'sportId' => $sport_id
	);
//	var_dump($odds);
	$this->manageBetOdds($bet, TRUE);
	
	/*if ($betId = $this->insertBet($act_datum, $datum, $udalost_id, $typ_id, $podtyp_id, $text, $match_id, $risk, $market, $betradarBetId)) {
		echo "* Vkladam $market $text $betId<br/>";*/
/*		$sql = "select max(sazka_id) AS maxi from sazky";
		$res =& $this->dbGame->query($sql);
		if(DB::isError($res));*/
	  
//		if ($row =& $res->fetchRow()) {
			/*$betId = $row['maxi'];
			var_dump($betId);*/
	/*		if ($this->atLeastOneRateChanged($betId, $betColumns, $odds)) {
				foreach ($betColumns as $betColumn) {
					$this->insertUpdateOdd($betId, $betradarBetId, $betColumn['columnId'], $odds[$betColumn['columnName']], $act_datum);
				}
			} else {
				$this->ErrorData[] = "VĹĄechny kurzy jsou uptodate pro druh ZĂĄpas BetRadarId(".$betradarBetId.") \n";
			}*/
			/*
			$sql = "insert into sazka_kurz (sazka_id,sloupec_id,poradi,kurz,platny_od)
			   values (".$row['maxi'].",138,1,".floatval($odds["1"]).",'".$act_datum."')";
			$res2 =& $this->dbGame->query($sql);
			if (DB::isError($res2)) {
				$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh ZĂĄpas BetRadarId(".$match_id.") \n";
				$this->dbGame->rollback();
				$this->dbGame->autoCommit(true);
				return;
			}
			
			$sql = "insert into sazka_kurz (sazka_id,sloupec_id,poradi,kurz,platny_od)
			   values (".$row['maxi'].",139,1,".floatval($odds["X"]).",'".$act_datum."')";
			$res2 =& $this->dbGame->query($sql);
			if(DB::isError($res2)) {
				$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh ZĂĄpas BetRadarId(".$match_id.") \n";
				$this->dbGame->rollback();
				$this->dbGame->autoCommit(true);
				return;
			}
			
			$sql = "insert into sazka_kurz (sazka_id,sloupec_id,poradi,kurz,platny_od)
			   values (".$row['maxi'].",140,1,".floatval($odds["2"]).",'".$act_datum."')";
			$res2 =& $this->dbGame->query($sql);
			if(DB::isError($res2)){$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh ZĂĄpas BetRadarId(".$match_id.") \n";$this->dbGame->rollback();$this->dbGame->autoCommit(true);return;}
			
			*/
		/*	
			if(!isset($this->ImportedData[$sport_id][$udalost_id]['zapas']['pocet']))
				$this->ImportedData[$sport_id][$udalost_id]['zapas']['pocet'] = 1;
			else $this->ImportedData[$sport_id][$udalost_id]['zapas']['pocet']++;
			
			$this->ImportedData[$sport_id][$udalost_id]['zapas']['id'][$betId] = 1;
			$this->con_ids[] =  $betId;
			
			KombinaceDruh::makeKomb(intval($udalost_id),intval($typ_id),$betId,$this->dbGame);*/
	/*	} else {
			$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh ZĂĄpas BetRadarId(".$match_id.") \n";
			$this->dbGame->rollback();
			$this->dbGame->autoCommit(true);
			return;
		}*/
/*	} else {
		$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh ZĂĄpas BetRadarId(".$match_id.") \n";
		$this->dbGame->rollback();
		$this->dbGame->autoCommit(true);
		return;
	}
	
	*/


/*
	$this->createCombinations();
*/
	
	#konec vlozeni 1 0 2#
	
	#vlozeni 01 20 12#
	
	//$this->dbGame->autoCommit(false);
	/*
	$sql = "select risk_limit from bet_settings where udalost_id=".intval($udalost_id)." and typ_id=24 and podtyp_id=24";
	$res =& $this->dbGame->query($sql);
	if ($row =& $res->fetchRow()){$risk = $row['risk_limit'];}else $risk = RISK_LIMIT;*/

	$betColumns = array(
		0 => array( 'columnId' => '141', 'columnName' => '01' ),
		1 => array( 'columnId' => '142', 'columnName' => '12' ),
		2 => array( 'columnId' => '143', 'columnName' => '02' )
	);
	$typ_id = 24;
	$podtyp_id = 24;
	$market = ' DVOJITA SANCE ';

	$risk = $this->getBetRiskLimit($udalost_id, $typ_id, $podtyp_id);
	
/*	$sql = "insert into sazky (status,platna_od,platna_do,bookmaker_id,udalost_id,typ_id,podtyp_id,text,betradar_sazka_id,risk_limit)
		   values (".$this::BET_INIT_STATUS.",'".$act_datum."','".Help::Slash($datum)."',1,".intval($udalost_id).",24,24,'".Help::Slash($text)."',".intval($match_id).",".intval($risk).")";
	$res =& $this->dbGame->query($sql);*/

	//betradar nema ekvivalent dvojite sance proto bude mit stejne betradarBetId jako zapas.
	$betradarBetId = $betradarBetId.'1';

	$bet = array(
		'currentDate' => $act_datum,
		'date' => $datum,
		'eventId' => $udalost_id,
		'typId' => $typ_id,
		'podtypId' => $podtyp_id,
		'text' => $text,
		'matchId' => $match_id,
		'risk' => $risk,
		'market' => $market,
		'betradarBetId' => $betradarBetId,
		'betColumns' => $betColumns,
		'odds' => $odds,
		'sportId' => $sport_id
	);
	
	$this->manageBetOdds($bet);
	
	/*if ($betId = $this->insertBet($act_datum, $datum, $udalost_id, $typ_id, $podtyp_id, $text, $match_id, $risk, $market, $betradarBetId)) {
		echo "* Vkladam $market  $text $betId<br/>";

		if ($this->atLeastOneRateChanged($betId, $betColumns, $odds)) {
			foreach ($betColumns as $betColumn) {
				$this->insertUpdateOdd($betId, $betradarBetId, $betColumn['columnId'], $odds[$betColumn['columnName']], $act_datum);
			}
		} else {
				$this->ErrorData[] = "VĹĄechny kurzy jsou uptodate pro druh ZĂĄpas BetRadarId(".$match_id.") \n";
		}

		if(!isset($this->ImportedData[$sport_id][$udalost_id]['dvojita_sance']['pocet']))
			$this->ImportedData[$sport_id][$udalost_id]['dvojita_sance']['pocet'] = 1;
		else
			$this->ImportedData[$sport_id][$udalost_id]['dvojita_sance']['pocet']++;
			
		$this->ImportedData[$sport_id][$udalost_id]['dvojita_sance']['id'][$betId] = 1;
		
		$this->con_ids[] =  $betId;
		KombinaceDruh::makeKomb(intval($udalost_id), 24, $betId, $this->dbGame);
	}*/
	
	/*if(DB::isError($res)) {
		$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh DvojitĂĄ ĹĄance BetRadarId(".$match_id.") \n";
	} else {
	 
		$sql = "select max(sazka_id) AS maxi from sazky";
		$res =& $this->dbGame->query($sql);
		if(DB::isError($res));
	    
		if ($row =& $res->fetchRow()) {
		 
			$sql = "insert into sazka_kurz (sazka_id,sloupec_id,poradi,kurz,platny_od)
			   values (".$row['maxi'].",141,1,".floatval($odds["01"]).",'".$act_datum."')";
			$res2 =& $this->dbGame->query($sql);
			if(DB::isError($res2)){$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh DvojitĂĄ ĹĄance BetRadarId(".$match_id.") \n";$this->dbGame->rollback();$this->dbGame->autoCommit(true);return;}
			
			$sql = "insert into sazka_kurz (sazka_id,sloupec_id,poradi,kurz,platny_od)
			   values (".$row['maxi'].",143,1,".floatval($odds["02"]).",'".$act_datum."')";
			$res2 =& $this->dbGame->query($sql);
			if(DB::isError($res2)){$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh DvojitĂĄ ĹĄance BetRadarId(".$match_id.") \n";$this->dbGame->rollback();$this->dbGame->autoCommit(true);return;}
			
			$sql = "insert into sazka_kurz (sazka_id,sloupec_id,poradi,kurz,platny_od)
			   values (".$row['maxi'].",142,1,".floatval($odds["12"]).",'".$act_datum."')";
			$res2 =& $this->dbGame->query($sql);
			if(DB::isError($res2)){$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh DvojitĂĄ ĹĄance BetRadarId(".$match_id.") \n";$this->dbGame->rollback();$this->dbGame->autoCommit(true);return;}
			
			if(!isset($this->ImportedData[$sport_id][$udalost_id]['dvojita_sance']['pocet']))$this->ImportedData[$sport_id][$udalost_id]['dvojita_sance']['pocet'] = 1;else $this->ImportedData[$sport_id][$udalost_id]['dvojita_sance']['pocet']++;
			
			$this->ImportedData[$sport_id][$udalost_id]['dvojita_sance']['id'][$row['maxi']] = 1;
			
			$this->con_ids[] =  $row['maxi'];
			KombinaceDruh::makeKomb(intval($udalost_id),24,$row['maxi'],$this->dbGame);
		
		} else {
			$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh DvojitĂĄ ĹĄance BetRadarId(".$match_id.") \n";
			$this->dbGame->rollback();
			$this->dbGame->autoCommit(true);
			return;
		}
	}*/

	//$this->dbGame->commit();   
	//$this->dbGame->autoCommit(true);

	#konec vlozeni 01 20 12#   
}
  
   /**
 * 1 0 2 handicap
 * @param object $bet data
 * @param int $match_id
 * @param int $udalost_id 
 * @param int $sport_id 
 * @param int $typ_id
 * @param date $datum datum udalosti
 * @param array $teams  souperi
 * @param string $act_datum aktualni datum
 * return string
 */
private function Handicap($bet,$match_id,$udalost_id,$sport_id,$typ_id,$datum,$teams,$act_datum,$betradarBetId){
	
	$podtyp_id = 23;
	$market = ' HANDICAP ';
	$betColumns = array(
		0 => array( 'columnId' => '138', 'columnName' => '1' ),
		1 => array( 'columnId' => '139', 'columnName' => 'X' ),
		2 => array( 'columnId' => '140', 'columnName' => '2' )
	);
	
	$odd = $bet->getElementsByTagName('Odds');
   
   $odds = array();

   for ($i = 0; $i < 3; $i++){
     
	if( !is_object($odd->item($i)) ) {
		$this->ErrorData[] = "Sázka BetRadarId(".$betradarBetId.") druh Handicap nebyl importován: chybí­ kurz";
		It6_Log::warn(
			"Error bet import betradarBetId %betradarBetId%, market %market%, rate does not exists",
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array('betradarBetId' => $betradarBetId,'market' => $market)
		);
		return;
	}
	 
	 $odds[$odd->item($i)->getAttribute("OutCome")] = str_replace(",",".",$odd->item($i)->nodeValue);
	 $odds['SpecialBetValue'] = $odd->item($i)->getAttribute("SpecialBetValue");
	 
   }
  
   reset($teams);
   
   $text = "";
   foreach($teams as $k=>$h){
     
	 if(!isset($h['preklad'])) $text .= trim($h['text']);else $text .= "[".$h['preklad']."]";
	 if($k == 1)  $text .= " - ";
	 
   }
   
   $text .= " ".$odds['SpecialBetValue'];
   
   $sql = "select vyhernost_min from bet_settings where typ_id=".intval($typ_id)." and podtyp_id=".intval($podtyp_id)." and sport_id=".intval($sport_id)." and udalost_id=".intval($udalost_id);
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res));
   
   if ($row =& $res->fetchRow()){
   
    $vyhernost = $row['vyhernost_min'];
    
   } else $vyhernost = MIN_WON;
   
   $odds = Help::GetOdd($odds,$vyhernost);

	if(!$odds) {
		$this->ErrorData[] = "Nepodařilo se vypočí­tat kurzy pro druh Handicap a nebyl importován BetRadarId(".$match_id."): ".$text." \n";
		It6_Log::warn(
			"Error bet import betradarBetId %betradarBetId%, market %market%. Error in odd calculations (Help::GetOdd)",
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array(
				'betradarBetId' => $betradarBetId,
				'market' => $market
			)
		);
		return;
	}
   
   $risk = $this->getBetRiskLimit($udalost_id,$typ_id,$podtyp_id);
   
   #vlozeni Handicap 1 0 2#
  // 
  // $this->dbGame->autoCommit(false);

   	$bet = array(
		'currentDate' => $act_datum,
		'date' => $datum,
		'eventId' => $udalost_id,
		'typId' => $typ_id,
		'podtypId' => $podtyp_id,
		'text' => $text,
		'matchId' => $match_id,
		'risk' => $risk,
		'market' => $market,
		'betradarBetId' => $betradarBetId,
		'betColumns' => $betColumns,
		'odds' => $odds,
		'sportId' => $sport_id
	);
//	var_dump($odds);
	$this->manageBetOdds($bet);
   
 /*  $sql = "select risk_limit from bet_settings where udalost_id=".intval($udalost_id)." and typ_id=".intval($typ_id)." and podtyp_id=23";
   $res =& $this->dbGame->query($sql);
   if ($row =& $res->fetchRow()){$risk = $row['risk_limit'];}else $risk = RISK_LIMIT;
   
   $sql = "insert into sazky (status,platna_od,platna_do,bookmaker_id,udalost_id,typ_id,podtyp_id,text,betradar_sazka_id,risk_limit)
           values (".$this::BET_INIT_STATUS.",'".$act_datum."','".Help::Slash($datum)."',1,".intval($udalost_id).",".intval($typ_id).",23,'".Help::Slash($text)."',".intval($match_id).",".intval($risk).")";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res))$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Handicap BetRadarId(".$match_id.") \n";else{
     
    $sql = "select max(sazka_id) AS maxi from sazky";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res));
    
	if ($row =& $res->fetchRow()){
	 
	   $sql = "insert into sazka_kurz (sazka_id,sloupec_id,poradi,kurz,platny_od)
           values (".$row['maxi'].",138,1,".floatval($odds["1"]).",'".$act_datum."')";
       $res2 =& $this->dbGame->query($sql);
       if(DB::isError($res2)){$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Handicap BetRadarId(".$match_id.") \n";$this->dbGame->rollback();$this->dbGame->autoCommit(true);return;}
	  
	   $sql = "insert into sazka_kurz (sazka_id,sloupec_id,poradi,kurz,platny_od)
           values (".$row['maxi'].",139,1,".floatval($odds["X"]).",'".$act_datum."')";
       $res2 =& $this->dbGame->query($sql);
       if(DB::isError($res2)){$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Handicap BetRadarId(".$match_id.") \n";$this->dbGame->rollback();$this->dbGame->autoCommit(true);return;}
	  
	   $sql = "insert into sazka_kurz (sazka_id,sloupec_id,poradi,kurz,platny_od)
           values (".$row['maxi'].",140,1,".floatval($odds["2"]).",'".$act_datum."')";
       $res2 =& $this->dbGame->query($sql);
       if(DB::isError($res2)){$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Handicap BetRadarId(".$match_id.") \n";$this->dbGame->rollback();$this->dbGame->autoCommit(true);return;}
	  
	   if(!isset($this->ImportedData[$sport_id][$udalost_id]['handicap']['pocet']))$this->ImportedData[$sport_id][$udalost_id]['handicap']['pocet'] = 1;else $this->ImportedData[$sport_id][$udalost_id]['handicap']['pocet']++;
	   
	   $this->ImportedData[$sport_id][$udalost_id]['handicap']['id'][$row['maxi']] = 1;
	   $this->con_ids[] =  $row['maxi'];
	   KombinaceDruh::makeKomb(intval($udalost_id),intval($typ_id),$row['maxi'],$this->dbGame);
	    
	}else {$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Handicap BetRadarId(".$match_id.") \n";$this->dbGame->rollback();$this->dbGame->autoCommit(true);return;}
	
   }*/

  // $this->dbGame->commit();
  // $this->dbGame->autoCommit(true);
   
   #konec vlozeni Handicap 1 0 2#
  
  }
  
   /**
 * 1 2 vitez zapasu
 * @param object $bet data
 * @param int $match_id
 * @param int $udalost_id 
 * @param int $sport_id 
 * @param int $typ_id
 * @param date $datum datum udalosti
 * @param array $teams  souperi
 * @param string $act_datum aktualni datum
 * return string
 */
  private function Winner($bet,$match_id,$udalost_id,$sport_id,$typ_id,$datum,$teams,$act_datum,$betradarBetId){
	$podtyp_id = 29;
	$market = ' WINNER ';
	$betColumns = array(
		0 => array( 'columnId' => '152', 'columnName' => '1' ),
		1 => array( 'columnId' => '153', 'columnName' => '2' )
	);
	
   $odd = $bet->getElementsByTagName('Odds');

   $odds = array();

   for ($i = 0; $i < 2; $i++){
     
	if( !is_object($odd->item($i)) ) {
		$this->ErrorData[] = "Sázka BetRadarId(".$match_id.") druh Vítěz zápasu nebyl importován: chybí­ kurz";
		It6_Log::warn(
			"Error bet import betradarBetId %matchId%, market %market%, rate does not exists",
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array('matchId' => $match_id,'market' => $market)
		);
		return;
	}
	 
	 $odds[$odd->item($i)->getAttribute("OutCome")] = str_replace(",",".",$odd->item($i)->nodeValue);
	 
   }
  
   reset($teams);
   
   $text = "";
   foreach($teams as $k=>$h){
     
	 if(!isset($h['preklad'])) $text .= trim($h['text']);else $text .= "[".$h['preklad']."]";
	 if($k == 1)  $text .= " - ";
	 
   }
   
   $sql = "select vyhernost_min from bet_settings where typ_id=".intval($typ_id)." and podtyp_id=29 and sport_id=".intval($sport_id)." and udalost_id=".intval($udalost_id);
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res));
   
   if ($row =& $res->fetchRow()){
   
    $vyhernost = $row['vyhernost_min'];
    
   } else $vyhernost = MIN_WON;
   
   $odds["X"] = 1000;
   $odds = Help::GetOdd($odds,$vyhernost);
   if(!$odds) {
		$this->ErrorData[] = "Nepodařilo se vypočí­tat kurzy pro druh Vítěz zápasu a nebyl importován BetRadarId(".$match_id."): ".$text." \n";
		It6_Log::warn(
			"Error bet import betradarBetId %betradarBetId%, market %market%. Error in odd calculations (Help::GetOdd)",
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array(
				'betradarBetId' => $betradarBetId,
				'market' => $market
			)
		);
		return;
	}
   
   
   #vlozeni 1 2#
   $risk = $this->getBetRiskLimit($udalost_id,$typ_id,$podtyp_id);
   
 //  $this->dbGame->autoCommit(false);

   	$bet = array(
		'currentDate' => $act_datum,
		'date' => $datum,
		'eventId' => $udalost_id,
		'typId' => $typ_id,
		'podtypId' => $podtyp_id,
		'text' => $text,
		'matchId' => $match_id,
		'risk' => $risk,
		'market' => $market,
		'betradarBetId' => $betradarBetId,
		'betColumns' => $betColumns,
		'odds' => $odds,
		'sportId' => $sport_id
	);
//	var_dump($odds);
	$this->manageBetOdds($bet);
	
   /*
   $sql = "select risk_limit from bet_settings where udalost_id=".intval($udalost_id)." and typ_id=".intval($typ_id)." and podtyp_id=29";
   $res =& $this->dbGame->query($sql);
   if ($row =& $res->fetchRow()){$risk = $row['risk_limit'];}else $risk = RISK_LIMIT;
   
   $sql = "insert into sazky (status,platna_od,platna_do,bookmaker_id,udalost_id,typ_id,podtyp_id,text,betradar_sazka_id,risk_limit)
           values (".$this->betInitialStatus.",'".$act_datum."','".Help::Slash($datum)."',1,".intval($udalost_id).",".intval($typ_id).",29,'".Help::Slash($text)."',".intval($match_id).",".intval($risk).")";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res))$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh VĂ­Â­tÄ�z zĂĄpasu BetRadarId(".$match_id.") \n";else{
     
    $sql = "select max(sazka_id) AS maxi from sazky";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res));
    
	if ($row =& $res->fetchRow()){
	 
	   $sql = "insert into sazka_kurz (sazka_id,sloupec_id,poradi,kurz,platny_od)
           values (".$row['maxi'].",152,1,".floatval($odds["1"]).",'".$act_datum."')";
       $res2 =& $this->dbGame->query($sql);
       if(DB::isError($res2)){$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh VĂ­Â­tÄ�z zĂĄpasu BetRadarId(".$match_id.") \n";$this->dbGame->rollback();$this->dbGame->autoCommit(true);return;}
	  
	   $sql = "insert into sazka_kurz (sazka_id,sloupec_id,poradi,kurz,platny_od)
           values (".$row['maxi'].",153,1,".floatval($odds["2"]).",'".$act_datum."')";
       $res2 =& $this->dbGame->query($sql);
       if(DB::isError($res2)){$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh VĂ­Â­tÄ�z zĂĄpasu BetRadarId(".$match_id.") \n";$this->dbGame->rollback();$this->dbGame->autoCommit(true);return;}
	  

	   if(!isset($this->ImportedData[$sport_id][$udalost_id]['vitez_zapas']['pocet']))$this->ImportedData[$sport_id][$udalost_id]['vitez_zapas']['pocet'] = 1;else $this->ImportedData[$sport_id][$udalost_id]['vitez_zapas']['pocet']++;
	   
	   $this->ImportedData[$sport_id][$udalost_id]['vitez_zapas']['id'][$row['maxi']] = 1;
	   $this->con_ids[] =  $row['maxi'];
	   KombinaceDruh::makeKomb(intval($udalost_id),intval($typ_id),$row['maxi'],$this->dbGame);
	    
	}else {$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh VĂ­Â­tÄ�z zĂĄpasu BetRadarId(".$match_id.") \n";$this->dbGame->rollback();$this->dbGame->autoCommit(true);return;}
	
   }
*/
 //  $this->dbGame->commit();
 //  $this->dbGame->autoCommit(true);
   
   #konec vlozeni 1 2#
   
  
  }

/**
 * Pod Nad
 * @param object $bet data
 * @param int $match_id
 * @param int $udalost_id 
 * @param int $sport_id 
 * @param int $typ_id
 * @param date $datum datum udalosti
 * @param array $teams  souperi
 * @param string $act_datum aktualni datum
 * return string
 */
  private function UnderOver($bet,$match_id,$udalost_id,$sport_id,$typ_id,$datum,$teams,$act_datum,$betradarBetId){
	  $betExists = $this->betExists($betradarBetId);
  	$podtyp_id = 25;
	$market = ' UNDER/OVER ';
	$betColumns = array(
		0 => array( 'columnId' => '145', 'columnName' => 'Under' ),
		1 => array( 'columnId' => '144', 'columnName' => 'Over' )
	);
	
   $odd = $bet->getElementsByTagName('Odds');
   
   $odds = $odds_help = array();

   for ($i = 0; $i < 2; $i++){
     
	if( !is_object($odd->item($i)) ) {
		$this->ErrorData[] = "Sázka BetRadarId(".$betradarBetId.") druh Under/Over nebyl importován: chybíÂ­ kurz";
		It6_Log::warn(
			"Error bet import betradarBetId %matchId%, market %market%, rate does not exists",
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array('matchId' => $betradarBetId,'market' => $market)
		);
		return;
	}
	 
	 $odds[$odd->item($i)->getAttribute("OutCome")] = str_replace(",",".",$odd->item($i)->nodeValue);
	 $odds['SpecialBetValue'] = $odd->item($i)->getAttribute("SpecialBetValue");
	 
   }
  
   reset($teams);
   
   $text = "";
   foreach($teams as $k=>$h){
     
	 if(!isset($h['preklad'])) $text .= trim($h['text']);else $text .= "[".$h['preklad']."]";
	 if($k == 1)  $text .= " - ";
	 
   }
   
   $text .= " ".$odds['SpecialBetValue'];
   
   
   $sql = "select vyhernost_min from bet_settings where typ_id=".intval($typ_id)." and podtyp_id=25 and sport_id=".intval($sport_id)." and udalost_id=".intval($udalost_id);
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res));
   
   if ($row =& $res->fetchRow()){
   
    $vyhernost = $row['vyhernost_min'];
    
   } else $vyhernost = MIN_WON;
   
   $odds_help["1"] = $odds["Over"];
   $odds_help["X"] = 1000;
   $odds_help["2"] = $odds["Under"];
   $odds_help = Help::GetOdd($odds_help,$vyhernost);

	if(!$odds_help) {
		$this->ErrorData[] = "Nepodařilo se vypočítat kurzy pro druh Pod/Nad a nebyl importován BetRadarId(".$betradarBetId."): ".$text." \n";
		It6_Log::warn(
			"Error bet import betradarBetId %betradarBetId%, market %market%. Error in odd calculations (Help::GetOdd)",
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array(
				'betradarBetId' => $betradarBetId,
				'market' => $market
			)
		);
		return;
   }
   
   $odds["Over"] = $odds_help["1"];
   $odds["Under"] = $odds_help["2"];
   
   
   #vlozeni Under/Over#
   
 //  $this->dbGame->autoCommit(false);

    $risk = $this->getBetRiskLimit($udalost_id,$typ_id,$podtyp_id);
   
   #vlozeni Handicap 1 0 2#
   
 //  $this->dbGame->autoCommit(false);

   	$bet = array(
		'currentDate' => $act_datum,
		'date' => $datum,
		'eventId' => $udalost_id,
		'typId' => $typ_id,
		'podtypId' => $podtyp_id,
		'text' => $text,
		'matchId' => $match_id,
		'risk' => $risk,
		'market' => $market,
		'betradarBetId' => $betradarBetId,
		'betColumns' => $betColumns,
		'odds' => $odds,
		'sportId' => $sport_id
	);
//	var_dump($odds);
	$this->manageBetOdds($bet);
   /*
   $sql = "select risk_limit from bet_settings where udalost_id=".intval($udalost_id)." and typ_id=".intval($typ_id)." and podtyp_id=25";
   $res =& $this->dbGame->query($sql);
   if ($row =& $res->fetchRow()){$risk = $row['risk_limit'];}else $risk = RISK_LIMIT;
   
   $sql = "insert into sazky (status,platna_od,platna_do,bookmaker_id,udalost_id,typ_id,podtyp_id,text,betradar_sazka_id,risk_limit)
           values (".$this::BET_INIT_STATUS.",'".$act_datum."','".Help::Slash($datum)."',1,".intval($udalost_id).",".intval($typ_id).",25,'".Help::Slash($text)."',".intval($match_id).",".$risk.")";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res))$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Under/Over BetRadarId(".$match_id.") \n";else{
     
    $sql = "select max(sazka_id) AS maxi from sazky";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res));
    
	if ($row =& $res->fetchRow()){
	 
	   $sql = "insert into sazka_kurz (sazka_id,sloupec_id,poradi,kurz,platny_od)
           values (".$row['maxi'].",144,1,".floatval($odds["Over"]).",'".$act_datum."')";
       $res2 =& $this->dbGame->query($sql);
       if(DB::isError($res2)){$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Under/Over BetRadarId(".$match_id.") \n";$this->dbGame->rollback();$this->dbGame->autoCommit(true);return;}
	  
	   $sql = "insert into sazka_kurz (sazka_id,sloupec_id,poradi,kurz,platny_od)
           values (".$row['maxi'].",145,1,".floatval($odds["Under"]).",'".$act_datum."')";
       $res2 =& $this->dbGame->query($sql);
       if(DB::isError($res2)){$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Under/OverBetRadarId(".$match_id.") \n";$this->dbGame->rollback();$this->dbGame->autoCommit(true);return;}
	  
	   if(!isset($this->ImportedData[$sport_id][$udalost_id]['under_over']['pocet']))$this->ImportedData[$sport_id][$udalost_id]['under_over']['pocet'] = 1;else $this->ImportedData[$sport_id][$udalost_id]['under_over']['pocet']++;
	   
	   $this->ImportedData[$sport_id][$udalost_id]['under_over']['id'][$row['maxi']] = 1;
	   $this->con_ids[] =  $row['maxi'];
	   KombinaceDruh::makeKomb(intval($udalost_id),intval($typ_id),$row['maxi'],$this->dbGame);
	    
	}else {$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Under/Over BetRadarId(".$match_id.") \n";$this->dbGame->rollback();$this->dbGame->autoCommit(true);return;}
	
   }*/
   
 //  $this->dbGame->commit();
 //  $this->dbGame->autoCommit(true);
   
   
   #konec vlozeni Under/Over#
  
  }

	
   /**
 * Asijsky handicap
 * @param object $bet data
 * @param int $match_id
 * @param int $udalost_id 
 * @param int $sport_id 
 * @param int $typ_id
 * @param date $datum datum udalosti
 * @param array $teams  souperi
 * @param string $act_datum aktualni datum
 * return string
 */
private function AsianHandicap($bet, $match_id, $udalost_id, $sport_id, $typ_id, $datum, $teams, $act_datum, $betradarBetId){
	$podtyp_id = 29;
	$market = ' ASIAN HANDICAP ';
	$betColumns = array(
		0 => array( 'columnId' => '152', 'columnName' => '1' ),
		1 => array( 'columnId' => '153', 'columnName' => '2' )
	);
   $odd = $bet->getElementsByTagName('Odds');

	$betExists = $this->betExists($betradarBetId);
   
   $odds = $odds_help = array();

	for ($i = 0; $i < 2; $i++) {
		if( !is_object($odd->item($i)) ) {
			$this->ErrorData[] = "Sázka BetRadarId(".$betradarBetId.") druh Asian handicap nebyl importován: chybí­ kurz";
			It6_Log::warn(
				"Error bet import betradarBetId %betradarBetId%, market %market%, rate does not exists",
				It6_Log::TAG_BETRADAR_BET_OPERATION,
				array('betradarBetId' => $betradarBetId,'market' => $market)
			);
			return;
		}
		$odds_help[$odd->item($i)->getAttribute("OutCome")] = str_replace(",",".",$odd->item($i)->nodeValue);
		$odds[$odd->item($i)->getAttribute("OutCome")]['SpecialBetValue'] = $odd->item($i)->getAttribute("SpecialBetValue");
	}
  
   reset($teams);
   
   $text = "";
   foreach($teams as $k=>$h){
     
	 if(!isset($h['preklad'])) $text .= trim($h['text']);else $text .= "[".$h['preklad']."]";
	 if($k == 1)  $text .= " ".$odds["1"]['SpecialBetValue']." - ";else $text .= " ".$odds["2"]['SpecialBetValue']."";
	 
   }
   
   //$text .= " (".$odds["1"]['SpecialBetValue']."/".$odds["2"]['SpecialBetValue'].")";
   
   $sql = "select vyhernost_min from bet_settings where typ_id=".intval($typ_id)." and podtyp_id=".intval($podtyp_id)." and sport_id=".intval($sport_id)." and udalost_id=".intval($udalost_id);
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res));
   
   if ($row =& $res->fetchRow()){
   
    $vyhernost = $row['vyhernost_min'];
    
   } else $vyhernost = MIN_WON;
   
   
   $odds_help["X"] = 1000;
   $odds_help = Help::GetOdd($odds_help,$vyhernost);
   //TODO, tady to zkejsne
//   var_dump($odds_help);

	if(!$odds_help) {
		$this->ErrorData[] = "Nepodařilo se vypočí­tat kurzy pro druh Asijsky handicap a nebyl importován BetRadarId(".$match_id."): ".$text." \n";
		It6_Log::warn(
			"Error bet import betradarBetId %betradarBetId%, market %market%. Error in odd calculations (Help::GetOdd)",
			It6_Log::TAG_BETRADAR_BET_OPERATION,
			array(
				'betradarBetId' => $betradarBetId,
				'market' => $market
			)
		);
		return;
	}
   
   
   #vlozeni Asijsky handicap#
   
	//$this->dbGame->autoCommit(false);

	$bet = array(
		'currentDate' => $act_datum,
		'date' => $datum,
		'eventId' => $udalost_id,
		'typId' => $typ_id,
		'podtypId' => $podtyp_id,
		'text' => $text,
		'matchId' => $match_id,
		'risk' => $risk,
		'market' => $market,
		'betradarBetId' => $betradarBetId,
		'betColumns' => $betColumns,
		'odds' => $odds_help,
		'sportId' => $sport_id
	);
//	var_dump($odds);
	$this->manageBetOdds($bet);

  /* $sql = "select risk_limit from bet_settings where udalost_id=".intval($udalost_id)." and typ_id=".intval($typ_id)." and podtyp_id=29";
   $res =& $this->dbGame->query($sql);
   if ($row =& $res->fetchRow()){$risk = $row['risk_limit'];}else $risk = RISK_LIMIT;
   
   $sql = "insert into sazky (status,platna_od,platna_do,bookmaker_id,udalost_id,typ_id,podtyp_id,text,betradar_sazka_id,risk_limit)
           values (".$this::BET_INIT_STATUS.",'".$act_datum."','".Help::Slash($datum)."',1,".intval($udalost_id).",".intval($typ_id).",29,'".Help::Slash($text)."',".intval($match_id).",".intval($risk).")";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res))$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Asian handicap BetRadarId(".$match_id.") \n";else{
     
    $sql = "select max(sazka_id) AS maxi from sazky";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res));
    
	if ($row =& $res->fetchRow()){
	 
	   $sql = "insert into sazka_kurz (sazka_id,sloupec_id,poradi,kurz,platny_od)
           values (".$row['maxi'].",152,1,".floatval($odds_help["1"]).",'".$act_datum."')";
       $res2 =& $this->dbGame->query($sql);
       if(DB::isError($res2)){$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Asian handicap BetRadarId(".$match_id.") \n";$this->dbGame->rollback();$this->dbGame->autoCommit(true);return;}
	  
	   $sql = "insert into sazka_kurz (sazka_id,sloupec_id,poradi,kurz,platny_od)
           values (".$row['maxi'].",153,1,".floatval($odds_help["2"]).",'".$act_datum."')";
       $res2 =& $this->dbGame->query($sql);
       if(DB::isError($res2)){$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Asian handicap BetRadarId(".$match_id.") \n";$this->dbGame->rollback();$this->dbGame->autoCommit(true);return;}
	  
	   if(!isset($this->ImportedData[$sport_id][$udalost_id]['asian_handicap']['pocet']))$this->ImportedData[$sport_id][$udalost_id]['asian_handicap']['pocet'] = 1;else $this->ImportedData[$sport_id][$udalost_id]['asian_handicap']['pocet']++;
	   
	   $this->ImportedData[$sport_id][$udalost_id]['asian_handicap']['id'][$row['maxi']] = 1;
	   $this->con_ids[] =  $row['maxi'];
	   KombinaceDruh::makeKomb(intval($udalost_id),intval($typ_id),$row['maxi'],$this->dbGame);
	    
	}else {$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Asian handicap BetRadarId(".$match_id.") \n";$this->dbGame->rollback();$this->dbGame->autoCommit(true);return;}
	
   }*/
   
  // $this->dbGame->commit();
 //  $this->dbGame->autoCommit(true);
   
   
   #konec vlozeni Asijsky handicap#
  
  }
  
  /**
 * Presny vysledek
 * @param object $bet data
 * @param int $match_id
 * @param int $udalost_id 
 * @param int $sport_id 
 * @param int $typ_id
 * @param date $datum datum udalosti
 * @param array $teams  souperi
 * @param string $act_datum aktualni datum
 * return string
 */
  private function Result($bet,$match_id,$udalost_id,$sport_id,$typ_id,$datum,$teams,$act_datum,$betradarBetId){

	//$betradarBetId = $match_id.$udalost_id;
	$betExists = $this->betExists($betradarBetId);
  
	$odd = $bet->getElementsByTagName('Odds');

	$odds = array();

//	$this->dbGame->autoCommit(false);

	$where = $and = "";
	$pole = $pole2 = array();
   
	for ($i = 0; $i < $odd->length; $i++) {
		$odds[$odd->item($i)->getAttribute("OutCome")]['rate'] = str_replace(",",".",$odd->item($i)->nodeValue);
		$pole[] = $odd->item($i)->getAttribute("OutCome");
	}
  
	$sql = "SELECT t.podtyp_id,nazev
		FROM typ_podtyp a
		INNER JOIN podtyp_sloupce t ON a.podtyp_id=t.podtyp_id
		WHERE a.typ_id=23 AND t.podtyp_id IN (
			SELECT c.podtyp_id FROM podtyp_sloupce c WHERE c.nazev='".$pole[0]."'
		)";
	$res2 =& $this->dbGame->query($sql);
	
	if(DB::isError($res2)){
		$this->ErrorData[] = "Nepodařilo se vložit sázku pro druh Presny vysledek BetRadarId(".$match_id."): chyba databáze 1\n";/*$this->dbGame->rollback();$this->dbGame->autoCommit(true);*/return;
	}
   
	while ($row =& $res2->fetchRow()){
		$pole2[$row['podtyp_id']][] = $row['nazev']; 
	}
	$id_exists = false;
	
	foreach($pole2 as $k=>$h) {
		$pocet = array_diff($pole,$h);
		if(count($pocet)==0 && count($pole) == count($h)){
			$id_exists=true;
			$podtyp_id = $k;
			break;
		}
	}

	if ($id_exists==true) {
		$sql = "SELECT nazev,sloupec_id FROM podtyp_sloupce where podtyp_id=".$podtyp_id;
		$res =& $this->dbGame->query($sql);

		if(DB::isError($res)) {
			$this->ErrorData[] = "Nepodařilo se vloĹžit sázku pro druh Presny vysledek BetRadarId(".$match_id."): chyba databáze 2\n";
			//$this->dbGame->rollback();$this->dbGame->autoCommit(true);
			return;
		}

		$betColumns = array();

		while ($row2 =& $res->fetchRow()) {
			$odds[$row2['nazev']] = Help::roundOdd($odds[$row2['nazev']]['rate']);
			$betColumns[] = array( 'columnId' => $row2['sloupec_id'], 'columnName' => $row2['nazev'] );
		}
	} else {
		if (!$betExists) {
			$sloupec_pocet = (count($odds)/4);
			$text = "Přesný výsledek ".time();

			#Zalozime novy podtyp#
			$sql = "INSERT INTO podtyp (radek_sloupec,sloupec_pocet_max,interni_nazev) values(1,".$sloupec_pocet.",'".$text."')";
			$res2 =& $this->dbGame->query($sql);
			if(DB::isError($res2)){$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Presny vysledek BetRadarId(".$match_id."): chyba databĂĄze 7 \n";/*$this->dbGame->rollback();$this->dbGame->autoCommit(true);*/return;}

			$sql = "select max(podtyp_id) AS maxi from podtyp";
			$res =& $this->dbGame->query($sql);
			if(DB::isError($res)){$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Presny vysledek BetRadarId(".$match_id."): chyba databĂĄze 3\n";/*$this->dbGame->rollback();$this->dbGame->autoCommit(true);*/return;}

			if ($row =& $res->fetchRow()) $podtyp_id = $row['maxi'];

			$sql = "INSERT INTO typ_podtyp (typ_id,podtyp_id,sport_id) values(".$typ_id.",".$podtyp_id.",".$sport_id.")";
			$res2 =& $this->dbGame->query($sql);
			if(DB::isError($res2)){$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Presny vysledek BetRadarId(".$match_id."): chyba databĂĄze 11 \n";/*$this->dbGame->rollback();$this->dbGame->autoCommit(true);*/return;}

			$x=1;
			foreach ($odds as $k=>$h) {
				$sql = "INSERT INTO podtyp_sloupce (podtyp_id,nazev,poradi) values(".$podtyp_id.",'".Help::Slash($k)."',".$x.")";
				$res2 =& $this->dbGame->query($sql);
				if(DB::isError($res2)){$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Presny vysledek BetRadarId(".$match_id."): chyba databĂĄze 4 \n";/*$this->dbGame->rollback();$this->dbGame->autoCommit(true);*/return;}

				$sql = "select max(sloupec_id) AS maxi from podtyp_sloupce";
				$res =& $this->dbGame->query($sql);
				if(DB::isError($res)){$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Presny vysledek BetRadarId(".$match_id."): chyba databĂĄze 5\n";/*$this->dbGame->rollback();$this->dbGame->autoCommit(true);*/return;}

				if ($row =& $res->fetchRow()) {
					$odds[$k]['sloupec_id'] =  $row['maxi'];
				} else {
					$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Presny vysledek BetRadarId(".$match_id."): chyba databĂĄze 6\n";
					/*$this->dbGame->rollback();
					$this->dbGame->autoCommit(true);*/
					return;
				}
				$x++;
			}
			$this->ImportedData[$sport_id][$udalost_id]['presny_vysledek']['novy_podtyp'] = "Byl zalozen novy podtyp: ".$text." ID(".$podtyp_id.")";
			It6_Log::info(
				"New oddType created: '%typeName%'",
				It6_Log::TAG_BETRADAR_OPERATION,
				array('typeName' => $text, 'subOddTypeId' => $podtyp_id, 'betradarMatchId' => $match_id)
			);
		}
	}
	reset($teams);

	$text = "";
	foreach($teams as $k=>$h) {
		if(!isset($h['preklad'])) $text .= trim($h['text']);else $text .= "[".$h['preklad']."]";
		if($k == 1)  $text .= " - ";
	}

	#vlozeni Presny vysledek#
	$risk = $this->getBetRiskLimit($udalost_id,$typ_id,$podtyp_id);
   
/*	$sql = "insert into sazky (status,platna_od,platna_do,bookmaker_id,udalost_id,typ_id,podtyp_id,text,betradar_sazka_id,risk_limit)
	values (".$this::BET_INIT_STATUS.",'".$act_datum."','".Help::Slash($datum)."',1,".intval($udalost_id).",".intval($typ_id).",".$podtyp_id.",'".Help::Slash($text)."',".intval($match_id).",".intval($risk).")";

	$res =& $this->dbGame->query($sql);

	if(DB::isError($res))$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Presny vysledek BetRadarId(".$match_id."): chyba databĂĄze 8 \n";*/
	//$betradarBetId = $match_id.$udalost_id;
	$market = ' PRESNY VYSLEDEK ';

	$bet = array(
		'currentDate' => $act_datum,
		'date' => $datum,
		'eventId' => $udalost_id,
		'typId' => $typ_id,
		'podtypId' => $podtyp_id,
		'text' => $text,
		'matchId' => $match_id,
		'risk' => $risk,
		'market' => $market,
		'betradarBetId' => $betradarBetId,
		'betColumns' => $betColumns,
		'odds' => $odds,
		'sportId' => $sport_id
	);
	
	$this->manageBetOdds($bet);
//echo 'mgmt end';
	
/*	if ($betId = $this->insertBet($act_datum, $datum, $udalost_id, $typ_id, $podtyp_id, $text, $match_id, $risk, $market, $betradarBetId)) {

		echo "* Vkladam $market $text $betId <br/>";
	*/	
		/*$sql = "select max(sazka_id) AS maxi from sazky";
		$res =& $this->dbGame->query($sql);
		if(DB::isError($res));*/

	//	if ($row =& $res->fetchRow()) {
//var_dump($this->atLeastOneRateChanged($betId, $betColumns, $odds));
//exit;

	/*	if ($this->atLeastOneRateChanged($betId, $betColumns, $odds)) {
			foreach($odds as $k=>$h) {
				$this->insertUpdateOdd($betId, $betradarBetId, $h['sloupec_id'], $h["rate"], $act_datum);
		*/		/*
				$sql = "insert into sazka_kurz (sazka_id,sloupec_id,poradi,kurz,platny_od) values (".$row['maxi'].",".$h['sloupec_id'].",1,".floatval($h["rate"]).",'".$act_datum."')";
				$res2 =& $this->dbGame->query($sql);
				if(DB::isError($res2)) {
					$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Presny vysledek BetRadarId(".$match_id."): chyba databĂĄze 9 \n";$this->dbGame->rollback();$this->dbGame->autoCommit(true);
					return;
				}*/
	/*		}
		} else {
				$this->ErrorData[] = "VĹĄechny kurzy jsou uptodate pro druh ZĂĄpas BetRadarId(".$match_id.") \n";
		}

			
			if (!isset($this->ImportedData[$sport_id][$udalost_id]['presny_vysledek']['pocet']))
				$this->ImportedData[$sport_id][$udalost_id]['presny_vysledek']['pocet'] = 1;
			else
				$this->ImportedData[$sport_id][$udalost_id]['presny_vysledek']['pocet']++;

			$this->ImportedData[$sport_id][$udalost_id]['presny_vysledek']['id'][$betId] = 1;
			$this->con_ids[] =  $betId;
			KombinaceDruh::makeKomb(intval($udalost_id),intval($typ_id),$betId,$this->dbGame);*/
		/*} else {
			$this->ErrorData[] = "NepodaĹ�ilo se vloĹžit sĂĄzku pro druh Presny vysledek BetRadarId(".$match_id."): chyba databĂĄze 10 \n";$this->dbGame->rollback();$this->dbGame->autoCommit(true);return;
		}*/
	//}

/*	$this->dbGame->commit();
	$this->dbGame->autoCommit(true);*/

	#konec vlozeni Presny vysledek#
}
  
/**
 * Tato metoda vyhodnoti uspesnost exportu vrati vysledek posle maily atd.
 * return void
 */
  private function Finalize(){
   if (BETADAR_DEBUG)
		var_dump($this->ErrorData);
		
   $mail = "";
   
   foreach($this->ImportedData as $sport_id=>$h){  
     
	 $mail .= mb_strtoupper($this->sportAr[$sport_id]['name'])."\n";
	 
	 foreach($h as $udalost_id=>$h2){ 
	   $mail .= "----\n".$this->sportAr[$sport_id]['udalost'][$udalost_id]."\n";
	   foreach($h2 as $druh=>$h3){
		 $mail .= $druh.": ".$h3['pocet']." zĂĄznamĹŻ\n";
	     if(isset($h3['novy_podtyp'])) $mail .= $h3['novy_podtyp']."\n";
		 foreach($h3['id'] as $k => $h4) $mail .= "#".$k."; ";
		 $mail .= "\n\n";
	   }
	 }
	 
	 $mail .= "\n\n\r";
	 
	}
   $mail .= "=========================================================================\n";

   if (count($this->ErrorData)==0) $mail.= 'BEZ CHYB'."\n";
   else $mail .= "CHYBY \n\n";
   foreach($this->ErrorData as $h){
     
	 $mail .= $h." \n";
   
   }
   
   if (($this->sendEmailOnError && (count($this->ImportedData)!=0 || count($this->ErrorData)!=0)) || $this->sendEmailAlways) {
	   $this->sendEmail("Import sazek",$mail);
  }
   

   
  }
  
  
public function updateTeamDb(Array $team, $my_udalost_id) {
	$sportId = $this->getSportByUdalostId($my_udalost_id);

	$sql = "SELECT betradar_id,name,short_name
	FROM team WHERE betradar_id=".intval($team['betradar_id']);
	$res = $this->dbGame->query($sql);

	if ($res) {
		$currTeam = $res->fetchRow();
		$isUpdateInsertTeam = ($currTeam['name'] != $team['name']);
		if (empty($currTeam['short_name']) || $currTeam['short_name']=='???') {
			$shortName = $team['name'];
		} else {
			$shortName = $currTeam['short_name'];
		}
	} else {
		$isUpdateInsertTeam = true;
	}
	
	if ($isUpdateInsertTeam) {
		$sql = "REPLACE INTO
		team (betradar_id, sport_id, name, short_name)
		VALUES (".intval($team['betradar_id']).",".$sportId.",'".Help::Slash($team['name'])."', '".$shortName."')";
	
		$res = $this->dbGame->query($sql);
		
		if(DB::isError($res)) {
			It6_Log::warn(
				"Team import error: team '%team%'",
				It6_Log::TAG_BETRADAR_TEAM_OPERATION,
				array('team' => $team['name'])
			);
			$this->ErrorData[] = "IT6 Error: setTeam (".$team['name']." ".$sql.")".__LINE__.'. ' . $res->getMessage() . "\n";
		} else {
			It6_Log::info(
				"Team insert/update success: team '%team%'",
				It6_Log::TAG_BETRADAR_TEAM_OPERATION,
				array('team' => $team['name'], 'teamOldName' =>$currTeam['name'])
			);
		}
	} else {
		if (BETADAR_DEBUG)
			It6_Log::info(
				"Team '%team%' is up to date",
				It6_Log::TAG_BETRADAR_TEAM_OPERATION,
				array('team' => $team['name'])
			);
	}
}

	private function deleteBet($betId) {
		$sql = "DELETE FROM sazky WHERE sazka_id = $betId";	
		$res = $this->dbGame->query($sql);
		if(DB::isError($res)) {
			It6_Log::warn(
				"Error while deleting bet %bet%- rates were removed (-1)!",
				It6_Log::TAG_BETRADAR_RATE_OPERATION,
				array('bet'=> $betId)
			);
		} else {
			It6_Log::info(
				"Bet %bet% deleted succesfully - rates were removed (-1)!",
				It6_Log::TAG_BETRADAR_RATE_OPERATION,
				array('bet'=> $betId)
			);
		}
	}

	private function manageBetOdds($bet,$isParentBet = FALSE) {
		//$this->dbGame->autoCommit(false);
		
		$act_datum = $bet['currentDate'];
		
		//cas chodi v lokalnim DB formatu, musime prevest do UTC
		$datum = $bet['date'];
		$udalost_id = $bet['eventId'];
		$typ_id = $bet['typId'];
		$podtyp_id = $bet['podtypId'];
		$text = $bet['text'];
		$match_id = $bet['matchId'];
		$risk = $bet['risk'];
		$market = $bet['market'];
		$betradarBetId = $bet['betradarBetId'];
		$betColumns = $bet['betColumns'];
		$odds = $bet['odds'];
		$sport_id = $bet['sportId'];
			
		$this->currentBetradarMatchId = $match_id;
		try {
			if ($betId = $this->insertBet($act_datum, $datum, $sport_id, $udalost_id, $typ_id, $podtyp_id, $text, $match_id, $risk, $market, $betradarBetId, $bet)) {

					if ($this->atLeastOneRateChanged($betId, $betColumns, $odds)) {

						foreach ($betColumns as $betColumn) {
							$columnNames = array_keys($odds);
							if ($betColumn['columnName'] == '-1') { //If the odds are removed, the outcome will be -1 for the removed odds.
								$this->deleteBet($betId);
								It6_Log::warn(
									"Betradar rates import error - rates were removed (-1)!",
									It6_Log::TAG_BETRADAR_RATE_OPERATION,
									array(
										'bet'=> $betId,
										'betradar_bet_id'=> $betradarBetId,
										'columnName'=> $betColumn['columnName'],
										'odds' => $odds)
								);
								$this->ErrorData[] = "Kurzy byly zrušeny pro BR-ID $betradarBetId, sázku nepřidávám. \n";
								throw new Exception('bet was deleted');
							}

							if (array_key_exists($betColumn['columnName'],$odds))
								$this->insertUpdateOdd($betId, $betradarBetId, $betColumn['columnId'], $odds[$betColumn['columnName']], $act_datum);
							else {
								It6_Log::warn(
									"Betradar rates import error - unknown columnName!",
									It6_Log::TAG_BETRADAR_RATE_OPERATION,
									array(
										'bet'=> $betId,
										'betradar_bet_id'=> $betradarBetId,
										'columnName'=> $betColumn['columnName'],
										'odds' => $odds)
								);
							}
						}

						$this->invalidateSportMenuFrame = true;
						It6_GlobalCache_Invalidator::invalidateSportsbookByBet($betId);

					} else {
						$this->ErrorData[] = "Všechny kurzy jsou uptodate pro druh Zápas BetRadarId(".$betradarBetId.") \n";
					}
					
					if(!isset($this->ImportedData[$sport_id][$udalost_id][$market]['pocet']))
							$this->ImportedData[$sport_id][$udalost_id][$market]['pocet'] = 1;
					else $this->ImportedData[$sport_id][$udalost_id][$market]['pocet']++;
					
					$this->ImportedData[$sport_id][$udalost_id][$market]['id'][$betId] = 1;
					
					
					KombinaceDruh::makeKomb(intval($udalost_id),intval($typ_id),$betId,$this->dbGame);
					
					//pokud vse dopadlo dobre urcim parenta
					if ($betId && ($newParentId = $this->ws->Bet->updateBetPackHierarchy(intval($betradarBetId),FALSE))) {
						It6_Log::info(
							"Bet parent bet change:  bet  #'%bet%' (betradarId #'%betradar_bet_id%') got parent (#'%parent%').",
							It6_Log::TAG_BETRADAR_BET_OPERATION,
							array('betradar_bet_id'=> $betradarBetId,'bet'=> $betId, 'parent' => $newParentId)
						);
						$this->con_ids[$match_id][$betId] = $newParentId;
					} else {
						It6_Log::warn(
							"Bet parent change error - running updateBetPackHierarchy :  bet  #'%bet%' (betradarId #'%betradar_bet_id%') newParent (#'%parent%').",
							It6_Log::TAG_BETRADAR_BET_OPERATION,
							array('betradar_bet_id'=> $betradarBetId,'bet'=> $betId, 'parent' => $newParentId)
						);
					}
			} else {
				$this->ErrorData[] = "Nepodařilo se vložit sázku pro druh Zápas BetRadarId(".$betradarBetId.") \n";
			/*	$this->dbGame->rollback();
				$this->dbGame->autoCommit(true);
				return;*/
			}
		} catch (Exception $e) {
		}

		

		//$this->dbGame->commit();
		//$this->dbGame->autoCommit(true);

	}

	public function sendEmail($subject,$body) {

		$mail = new Zend_Mail();

		$mail->setBodyText($body);

		$mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);

		$recipients = explode(',',$this->bookmakerEmail);
		foreach ($recipients as $r)
			$mail->addTo($r, $r);

		$mail->setSubject(MAIL_SUBJECT_PREFIX. ' ' . $subject);

		$mail->send();
		
/*
		$mmail = new htmlMimeMail();
		$mmail->setTextCharset("UTF-8");
		$mmail->setHeadCharset("UTF-8");
		$mmail->setHTMLCharset("UTF-8");  
		$mmail->html_charset = "UTF-8";$mmail->text_encoding = "UTF-8";
		$mmail->setHTMLEncoding("quoted-printable");
		$mmail->setText($body);
		$mmail->setSubject($subject." ".It6_Date::dbNow());

		
		$mmail->send(array($this->bookmakerEmail));
*/
	}
  
	private function insertUpdateOdd($betId, $betradarBetId, $columnId, $odd, $act_datum) {
		$sql = "SELECT MAX(poradi) as poradi,sazka_id,MAX(kurz) as odd FROM sazka_kurz WHERE sazka_id = $betId AND sloupec_id = $columnId GROUP BY sazka_id";
		
		$res = &$this->dbGame->query($sql);
		$row = &$res->fetchRow();
		if ($row['poradi']!=NULL) {
	  		$poradi = $row['poradi']+1;
	  		//pokud je kurz jiny nez soucasny, a je sazka updatovatelna betraderem
	  		$oddToUpdate = (round($odd,2) == $row['odd'] ? false : $this->isAutoUpdateEnabled($betradarBetId));
	  	}
		else {
			$poradi = 1;
			$oddToUpdate = true;
		}

		$sql = "INSERT INTO sazka_kurz (sazka_id,sloupec_id,poradi,kurz,platny_od)
				VALUES (".$betId.",".$columnId.",".$poradi.",".floatval($odd).",'".$act_datum."')";

		$res2 =& $this->dbGame->query($sql);

		if (DB::isError($res2)) {
			It6_Log::warn(
				"Rate import error: betId #'%bet%' unable to insert rate value '%odd%' columnId '%column_id%'.",
				It6_Log::TAG_BETRADAR_RATE_OPERATION,
				array('bet' => $betId, 'column_id'=> $columnId, 'odd' => floatval($odd))
			);
			$this->ErrorData[] = "Nepodařilo se vložit kurz pro druh Zápas BetRadarId(".$betId.") \n";
			return false;
		} else {
			It6_Log::info(
				"Rate ".(($poradi>1) ? 'update' : 'insert') ." success : betId #'%bet%' value '%odd%' columnId '%column_id%', update #%order%'.",
				It6_Log::TAG_BETRADAR_RATE_OPERATION,
				array('bet' => $betId, 'column_id'=> $columnId, 'odd' => floatval($odd), 'order' => $poradi)
			);
			return true;
		}
	}
  
  private function betExists($betradarBetId) {  
  	$sql = "SELECT sazka_id,betradar_sazka_id FROM sazky WHERE betradar_sazka_id = $betradarBetId";
	$res = &$this->dbGame->query($sql);
	$row = &$res->fetchRow();
	if (empty($row))
		return false;
	else return $row['sazka_id'];
  }
  
	private function isAutoUpdateEnabled($betradarMatchId) {
		$sql = "SELECT betradar_autoupdate FROM sazky WHERE betradar_sazka_id = $betradarMatchId";
		$res = &$this->dbGame->query($sql);
		$row = &$res->fetchRow();
	//	var_dump($row);
		return intval($row['betradar_autoupdate']);
	}

	private function getSportByUdalostId($my_udalost_id) {
		$sql = "SELECT sport_id FROM udalost WHERE udalost_id = $my_udalost_id";
		$res =& $this->dbGame->query($sql);
	
		if(DB::isError($res))$this->ErrorData[] = "IT6 Error: getSportIdByUdalostId ($teamName)".__LINE__.'. ' . $res->getMessage() . "\n";
			
		$sport_id = $res->fetchRow();
		return $sport_id['sport_id'];
	}

	private function betNeedsUpdate($betOld, $betNew) {
		$res = false;
		$betId = $betOld['sazka_id'];
		$bo = array_slice(array_values($betOld),1,count($betNew));
		$betNew = array_values($betNew);
		$diff = array_diff($bo,$betNew);

		if (count($diff)==0) {
			if (BETADAR_DEBUG)
				It6_Log::notice(
					"Tried to update bet #'%bet%', but is up to date.",
					It6_Log::TAG_BETRADAR_BET_OPERATION,
					array('bet' => $betId,'betOld' => $bo, 'betNew' => $betNew, 'res' => $res)
				);
			return false;
		} else {
			if (BETADAR_DEBUG)
				It6_Log::notice(
					"GOING TO UPDATE BET #'%bet%', but is up to date.",
					It6_Log::TAG_BETRADAR_BET_OPERATION,
					array('bet' => $betId,'betOld' => $bo, 'betNew' => $betNew, 'res' => $res)
				);
			return true;
		}
	}

	private function usingBetradarAutoupdate($sport_id, $typ_id) {
		return (
			in_array($sport_id, $this->sportsWithBrUpdateOn) || !in_array($typ_id, $this->typesWithNoBrUpdate)
		);
	}

	private function updateBet($betOld, $bet, $betId) {
		$validTo = Help::Slash($bet['validTo']);
		$sql = "UPDATE sazky SET
			platna_do = \"$validTo\",
			udalost_id = ".intval($bet['eventId']).",
			typ_id = ".intval($bet['typeId']).",
			podtyp_id = ".intval($bet['subTypeId']). //",
			//text = \"".Help::Slash($bet['text']).
			' WHERE betradar_sazka_id = '.$bet['betradarBetId'];
			
		if (BETADAR_DEBUG) {
			It6_Log::notice(
				"SQL.",
				It6_Log::TAG_BETRADAR_BET_OPERATION,
				array('sql' => $sql)
			);
		}

		$res = $this->dbGame->query($sql);
		if (!DB::isError($res)) {
			It6_Log::notice(
				"Bet update success: bet #'%bet%'.",
				It6_Log::TAG_BETRADAR_BET_OPERATION,
				array('bet' => $betId,'oldBet' => $betOld, 'betNew' => $bet)
			);
		} else {
			It6_Log::warn(
				"Bet update error: bet #'%bet%'.",
				It6_Log::TAG_BETRADAR_BET_OPERATION,
				array('bet' => $betId,'oldBet' => $betOld, 'betNew' => $bet)
			);
		}

		$toNotUpdate = It6_Models_BetType::getAllToNotUpdateValidToTimeByParent();
		if (false !== $toNotUpdate) {
			$toNotUpdateSql = (empty($toNotUpdate) ? '' : " AND typ_id NOT IN (" . implode(',', $toNotUpdate) . ')');
	
			$sql = "UPDATE sazky SET platna_do='$validTo' WHERE proplacena=0 $toNotUpdateSql"
				. " AND parent_id=" . intval($betId)
				. " AND platna_do<>'$validTo'";
			$res2 = $this->dbGame->query($sql);
			if (DB::isError($res2)) {
				It6_Log::warn(
					"Derived bets validTo update error: parentBet #'%bet%'.",
					It6_Log::TAG_BETRADAR_BET_OPERATION,
					array('bet' => $betId)
				);
			}
			else {
				It6_Log::notice(
					"Derived bets validTo updated: parentBet #'%bet%', count=$res2.",
					It6_Log::TAG_BETRADAR_BET_OPERATION,
					array('bet' => $betId)
				);
			}
		}
		return $res;
	}
	private function insertBet($act_datum, $datum, $sport_id, $udalost_id, $typ_id, $podtyp_id, $text, $match_id, $risk, $market, $betradarBetId, $betData) {
		$BET_TYPE_MASTERS = array(18,20);
		$real_typ_id = 'NULL';
		if (in_array($typ_id,$BET_TYPE_MASTERS)) $real_typ_id = $typ_id;
		
		if ($this->betExists($betradarBetId)) {
			$sql = "SELECT
				sazka_id,platna_do,udalost_id,typ_id,podtyp_id,text
				FROM sazky
				WHERE betradar_sazka_id=$betradarBetId";
			$res =& $this->dbGame->query($sql);
			$row = $res->fetchRow();
			$betId = $row['sazka_id'];
			$rowNoText = $row;
			unset($row['text']);

			$betNew = array(
				'betradarBetId' => $betradarBetId,
				'validTo' => $datum,
				'eventId' => $udalost_id,
				'typeId' => $typ_id,
				'subTypeId' => $podtyp_id,
				'text' => $text );
			$betNewNoText = $betNew;
			unset($betNewNoText['text']);
			
			if ($this->betNeedsUpdate($rowNoText, $betNewNoText) &&
					$this->updateBet($row, $betNew, $betId)) {
				return $betId;
			} elseif ($this->isAutoUpdateEnabled($betradarBetId)) {
				return $betId;
			} else {
				return false;
			}
		} else {

			if ($datum <= $act_datum) { //pokud je nahodou datum konani v minulosti, coz uz se jednou stalo
				$this->ErrorData[] = "Datum konání sázky je v minulosti: BetRadarId(".$betradarBetId.") - ".$market."; platna_od: $act_datum, platna_do: $datum  \n";
				It6_Log::warn(
					"Bet import mismatch dates: bet #'%bet%', typeId #'%type_id%', market '%market%'.",
					It6_Log::TAG_BETRADAR_BET_OPERATION,
					array('bet' => $betradarBetId,'type_id' => $typ_id, 'market' => $market, 'event' => $udalost_id, 'valid_from' => $datum, 'valid_to' => $act_datum)
				);
			} else { //insert
				$textNote = It6_Models_Bet::getDefaultTextNote($typ_id, $udalost_id);
				if (!isset($textNote))
					$textNote = 'NULL';
				else
					$textNote = "'" . Help::Slash($textNote) . "'";
				$brUpdateOn = ($this->betUpdate && $this->usingBetradarAutoupdate($sport_id, $typ_id) ? 1 : 0);
				$sql = "INSERT INTO sazky
					(status,platna_od,platna_do,bookmaker_id,udalost_id,typ_id,real_typ_id,podtyp_id,text,betradar_sazka_id,betradar_match_id,risk_limit,betradar_autoupdate,text_note)
					VALUES (".$this->betInitialStatus.",'".$act_datum."','".Help::Slash($datum)."',".$this->betradarBookmakerId.",".intval($udalost_id).",".intval($typ_id).",".$real_typ_id.",".$podtyp_id.",'".Help::Slash($text)."',".$betradarBetId.",".$match_id.",".intval($risk).",".$brUpdateOn.",".$textNote.")";
				$res = &$this->dbGame->query($sql);

				if(DB::isError($res)) {
					It6_Log::warn(
						"Bet import error: bet #'%bet%', typeId #'%type_id%', market '%market%'.",
						It6_Log::TAG_BETRADAR_BET_OPERATION,
						array('bet' => $betradarBetId,'type_id' => $typ_id, 'market' => $market, 'event' => $udalost_id, 'betData'=>$betData)
					);
					$this->ErrorData[] = "Nepodařilo se vložit sázku pro druh ".$market." BetRadarId(".$betradarBetId.") \n";
					
					return false;
				} else {
					$sql = "SELECT LAST_INSERT_ID() AS id";
					$res =& $this->dbGame->query($sql);
					$row = $res->fetchRow();
					$sazka_id = $row['id'];

					$sql = "INSERT INTO bet_column(sazka_id, sloupec_id, risk_limit_balance)"
						. " SELECT $sazka_id, sloupec_id, 0 FROM podtyp_sloupce WHERE podtyp_id=$podtyp_id";
					$res = $this->dbGame->query($sql);

					It6_Log::info(
						"Bet import success: bet #'%bet%', typeId #'%type_id%', market '%market%'.",
						It6_Log::TAG_BETRADAR_BET_OPERATION,
						array('bet' => $betradarBetId,'type_id' => $typ_id, 'market' => $market, 'event' => $udalost_id, 'betId'=>$sazka_id,'betData'=>$betData)
					);

					if(DB::isError($res)) return false;
					else return $sazka_id;
				}
			}
		}
	}

	private function getBetRiskLimit($udalost_id,$typ_id,$podtyp_id) {
		$sql = "SELECT risk_limit
				FROM bet_settings
				WHERE udalost_id=".intval($udalost_id)." AND typ_id=".intval($typ_id)." AND podtyp_id=".intval($podtyp_id);
				
		$res =& $this->dbGame->query($sql);
		if ($row =& $res->fetchRow()) {
			return $row['risk_limit'];
		}
		else return RISK_LIMIT;
	}

	private function atLeastOneRateChanged($betId, $betColumns, $odds) {
		$colIds = array();
		$newOdds = array();
		
		foreach ($betColumns as $betColumn) {
			$colIds[] = $betColumn['columnId'];
			$newOdds[] = $odds[$betColumn['columnName']];
		}
		$colIds = implode(',',$colIds);
		//var_dump($odds);
		$sql = "SELECT sloupec_id, poradi, sazka_id, kurz as odd
				FROM sazka_kurz
				WHERE sazka_id = $betId AND sloupec_id IN ($colIds)
				AND poradi = (
					SELECT MAX(poradi) as poradi
					FROM sazka_kurz
					WHERE sazka_id = $betId AND sloupec_id IN ($colIds)
				)";

//		var_dump($sql);

		$res = &$this->dbGame->query($sql);

		if (!DB::isError($res)) {
			$currentOdds = array();
			while ($row = &$res->fetchRow()) {
				$currentOdds[] = $row['odd'];
			}
//		var_dump(implode(',',$currentOdds));
//		var_dump(implode(',',$newOdds));
		
		$co = implode(',',$currentOdds);
		$o = implode(',',$newOdds);
//		var_dump(!($o == $co));
//		exit;
			return !($o == $co);
		} else return true;
//		return ((implode(',',$currentOdds)) == (implode(',',$newOdds)));*/

		
	/*	if ($res) {
			foreach ($row = $res->fetchRow()) {
				echo $row['odd'].' xx ';
			}
		} else return true;
		return true;*/
	}

	public function createCombinations() {
/*
		var_dump($this->currentBetradarMatchId);
*/
		foreach ($this->con_ids as $brMatchId => $bbasIdsToComb) {
			$this->currentBetradarMatchId = $brMatchId;
			$parentIds = array();
			foreach ($bbasIdsToComb as $betId => $parentId)
				$parentIds[$parentId] = $parentId;
			$parentIds = array_keys($parentIds);
			if ($this->currentBetradarMatchId!=NULL) {
				$combSql = 'SELECT * FROM vic_main.sazka_kombinace WHERE (sazka1_id = ? AND sazka2_id = ?) OR (sazka1_id = ? AND sazka2_id = ?)';
				$sth = $this->dbGame->prepare('replace into sazka_kombinace (sazka1_id,sazka2_id) values(?,?)');
				
				//find bets under the same match id or parent id - these are for sure in combinations
				$combIds = It6_Models_Bet::getBetsByBetradarMatchIdOrParentIds($this->currentBetradarMatchId, $parentIds, $this->zdb);
				//if (!empty($existingComb))
				//	$combIds = array_merge($combIds,$existingComb);
				/*
				var_dump('*******************');
				var_dump($this->currentBetradarMatchId);
				var_dump($existingComb);
		*/
				$combCount = count($combIds);
				for($x = 0; $x < $combCount; $x++) {
					for($y = 0; $y < $combCount; $y++) {
						if ($x == $y) continue;
						
						$betId1 = $combIds[$x];
						$betId2 = $combIds[$y];
						if ($betId1 != $betId2) {
							$comb = $this->db->query($combSql,array($betId1, $betId2, $betId2, $betId1));
							$combRow = $comb->fetch();
							if (empty($combRow)) { //pokud kombinace neni v db (at v obou moznych prohozeni)
								$res2 =& $this->dbGame->execute($sth,array($betId1,$betId2));
								if (DB::isError($res2)) {
									$this->ErrorData[] = "Nepodarilo se vytvorit nektere podpurne sazky  MatchID($betId1)  and OurMatchID($betId2)\n";
									
									It6_Log::warn(
										"Error creating Betradar combinations %betId% - %betId2%: replace sazka_kombinace error.",
										It6_Log::TAG_BETRADAR_BET_OPERATION,
										array('betId' => $betId1,'betId2' => $betId2)
									);
								} else {
									It6_Log::info(
										"Combination between bets %betId% - %betId2% created.",
										It6_Log::TAG_BETRADAR_BET_OPERATION,
										array('betId' => $betId1,'betId2' => $betId2)
									);
								}
							}
						}
					}
				}
			} else {
				It6_Log::warn(
					"Error creating Betradar combinations currentBetradarMatchId empty.",
					It6_Log::TAG_BETRADAR_BET_OPERATION,
					array('betId' => $this->currentBetradarMatchId,'combs' => $combIds)
				);
			}
		}//it6
	}

	public function __destruct() {
	}

	public function cleanUp() {
		if ($this->invalidateSportMenuFrame)
			It6_GlobalCache_Invalidator::invalidateSportMenuFrame();
	}
}
?>
