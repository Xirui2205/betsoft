<?php
/**
 * @package    book
 */

/**
 * Trida pro vytvoreni sazek
 *
 *
 * <code>
 *
 * </code>
 *
 * @package    Ciselniky
 */

class SazkaVytvor extends Template{

/**
 * navratova hodnota
 * @access private
 * @var string
 */
private  $vrat = "";



/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */
private  $dbGame;

/**
 * jmena udalosti
 * @access private
 * @var DB
 */
private  $udalost_name=array();

/**
 * aktualni sekce
 * @access private
 * @var int
 */
private  $section;

/**
 * zadavani multisazky
 * @access private
 * @var bool
 */
private  $multisazka = false;

/**
 * Javascript na vyhernost
 * @access private
 * @var string
 */
private  $jsscript = 'var vyhernost = new Object();';

/**
 * sys. hlasky
 * @access private
 * @var array
 */
	private  $messages = Array();

/**
 * sys. hlasky
 * @access private
 * @var array
 */
	private  $errors = Array();

/**
 * Bet Ids that has been created
 * @access private
 * @var array
 */
	private  $addedBetIds = Array();

	private $processedBetsOk = 0;
	
	private $processedBetsError = 0;

	private $betCount = 1;

	private $jsLoadLiability = '';

	private $betTypesToCombine = array(21,31,32,49,52);
/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param int $section id aktualni sekce
* @param PEAR::DB $dbGame objekt spojeni s databazi
*/
	public function __construct($section=0){
		$this->section =  $section;
		$this->dbGame = DbUtil::connectWebDb();
		$this->multisazka = true;
		Main::getInstance()->getController()->registerJsInclude('jqueryUI', true);		
		register_shutdown_function(array($this, 'cleanUp'));
  }

/**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
	public function runAction() {
		if(isset($_POST['create'])) {
			$this->CreateBet();
		}
		$this->ShowBet();
		$this->dbGame->disconnect();
  }

 /**
 * Vytvoreni sazky
 * @return void
 */
	private function CreateBet() {
		$status = true;
		$this->dbGame->autoCommit(false);
		

		if(!isset($_POST['sazka']) || !is_array($_POST['sazka'])) {
			$this->errors[] = I18n::tr('No bet was selected');
			$status = false;
		}

		if (!isset($_POST['typ']))
			$_POST['typ'] = $_POST['globalTypeId'];

		if (!isset($_POST['podtyp']))
			$_POST['podtyp'] = $_POST['sazka-podtyp'];

		if(!isset($_POST['udalost']) || $_POST['udalost'] == 0) {
			$this->errors[] = I18n::tr('An event must be chosen.');
			$status = false;
		}

		//kontrola zda udalost nevyprsela a uzivatel ma pravo ji editovat
		if(isset($_POST['udalost']) && $_POST['udalost'] != 0) {
			$sql = 
				"SELECT *
				FROM udalost
				WHERE platne_do>'".It6_Date::dbNow()."'
					AND udalost_id=".intval($_POST['udalost']);

			$res =& $this->dbGame->query($sql);

			DbUtil::testResult($res);

			if (!$row =& $res->fetchRow()) {
				$this->errors[] = I18n::tr('The event is already finished. Unable to create bets');
				$status = false;
			}

			if($_SESSION['superbookmaker'] != 1){

				$sql =
					"SELECT *
					FROM prava_udalost
					WHERE bookmaker_id=".intval($_SESSION['bookmaker'])."
						AND povoleny=1
						AND udalost_id=".intval($_POST['udalost']);

				$res =& $this->dbGame->query($sql);
				DbUtil::testResult($res);

				if (!$row =& $res->fetchRow()) {
					$this->errors[] =  I18n::tr('You dont have priviliges to create bets for this event');
					$status = false;
				}
			}
		} //kontrola vyprseni konec

		
		foreach($_POST['sazka'] as $k=>$h) {
			$betParentId = '';
			if (isset($h['bet_parent_id']))
				$betParentId = intval($h['bet_parent_id']);
			else if (isset($_POST['groupParentId']))
				$betParentId = intval($_POST['groupParentId']);
			
			if(!empty($betParentId)) {
				$sql = "
					SELECT udalost_id
					FROM sazky
					WHERE sazka_id=".$betParentId;

				$res =& $this->dbGame->query($sql);
				DbUtil::testResult($res);
				$row = $res->fetchRow();
				
				if(empty($row)) {
					$this->errors[] =  I18n::tr('Sázka {0} -> Hlavní sázka musí existovat.',$k);
					$status = false;
				}
				if($row['udalost_id'] != $_POST['udalost']) {
					$this->errors[] =  I18n::tr('Sázka {0} -> Hlavní sázka musí mít stejné id události jako její podřazené sázky.',$k);
					$status = false;
				}
			}
			
			if(!isset($h['typ']) || $h['typ'] == 0) {
					$this->errors[] =  I18n::tr('Sázka {0} -> Druh musí být zvolen',$k);
					$status = false;
			}

			if(!isset($h['podtyp']) || $h['podtyp'] == 0) {
				$this->errors[] =  I18n::tr('Sázka {0} -> Podtyp musí být zvolen',$k);
				$status = false;
			}

			if(isset($h['risk']) && !is_numeric($h['risk'])) {
				$this->errors[] =  I18n::tr('Sázka {0} -> Risk limit musí být celé číslo',$k);
				$status = false;
			}

			if(isset($h['ako']) && !ctype_digit($h['ako'])) {
				$this->errors[] =  I18n::tr('Sázka {0} -> AKO musí být celé číslo',$k);
				$status = false;
			}

			if(isset($h['typ']) && $h['typ'] != 0 && isset($h['podtyp']) && $h['podtyp'] != 0) {  //kontrola zda typ a podtyp  jsou navzajem propojene
				$sql = "
					SELECT *
					FROM typ_podtyp
					WHERE sport_id=".intval($_POST['sport'])."
						AND typ_id=".intval($h['typ'])."
						AND podtyp_id=".intval($h['podtyp']);

				$res =& $this->dbGame->query($sql);
				DbUtil::testResult($res);

				if (!$row =& $res->fetchRow()) {
					$this->errors[] =  I18n::tr('Sázka {0} -> Spojení tohoto druhu a podtypu není povoleno',$k);
					$status = false;
				}
			}

			if ((!isset($h['radek']) || $h['radek'] == 0) && mb_strlen($h['text']) < 1) {
				$this->errors[] =  I18n::tr('Sázka {0} -> Text musí být u tothoto druhu sázky uveden',$k);
				$status = false;
			}

			if(!It6_Date::checkFormat($h['platna_od'])) {
				$this->errors[] =  I18n::tr('Sázka {0} -> Platné od nemá správný formát dd.mm.RRRR HH:mm:ss ({1}) {2}',$k,$h['platna_od'],'');
				$status = false;
			}

			if(!It6_Date::checkFormat($h['platna_do'])) {
				$this->errors[] =  I18n::tr('Sázka {0} -> Platné do nemá správný formát dd.mm.RRRR HH:mm:ss',$k);
				$status = false;
			}

			if (It6_Date::toTimestamp($h['platna_od']) >= It6_Date::toTimestamp($h['platna_do'])) {
				$this->errors[] =  I18n::tr('Sázka {0} -> Datum od nemůže být větší nebo roven datumu do',$k);
				$status = false;
			}
			if(!isset($h['sloupec']) || !is_array($h['sloupec'])) {
				$this->errors[] =  I18n::tr('Sázka {0} -> Nejsou uvedeny kurzy',$k);
				$status = false;
			}

			#MAX a MIN kurzu a vyhernosti#
			$sql =
				"SELECT kurz_min,kurz_max,vyhernost_min,vyhernost_max
				FROM bet_settings
				WHERE udalost_id=".intval($_POST['udalost'])."
					AND typ_id=".intval($h['typ'])."
					AND podtyp_id=".intval($h['podtyp']);

			$res =& $this->dbGame->query($sql);
			DbUtil::testResult($res);

			if($row =& $res->fetchRow()) {
				$min_kurz = $row['kurz_min'];
				$max_kurz = $row['kurz_max'];
				$min_vyhernost = $row['vyhernost_min'];
				$max_vyhernost = $row['vyhernost_max'];
			}
			else {
				$this->errors[] =  I18n::tr('Sázka {0} -> Nebyl nalezen min. a max kurz pro daný podtyp',$k);
				$status = false;
			}

			#kontrola kurzu#
			$sql =
				'SELECT psc.sloupec_id, psc.complement_id
				FROM podtyp_sloupec_complement psc
				JOIN podtyp_sloupce ps
					ON ps.sloupec_id=psc.sloupec_id
					AND ps.podtyp_id=' . intval($h['podtyp']);
			
			$res = $this->dbGame->query($sql);
			DbUtil::testResult($res);
			
			$complementCols = array();
			while ($row = $res->fetchRow())
				$complementCols[$row['sloupec_id']][] = $row['complement_id'];

			$sql =
				"SELECT sloupec_id
				FROM podtyp_sloupce
				WHERE podtyp_id=".intval($h['podtyp']);

			$res = $this->dbGame->query($sql);
			DbUtil::testResult($res);

			if ($res->NumRows() < 1) {
				$this->errors[] =  I18n::tr('Sázka {0} -> Nebyly nalezeny žádné sloupce pro daný podtyp',$k);
				$status = false;
			}

			$low = 10000000;
			$min_max_status = true;
			$vyhernost_num = 0;

			while ($row =& $res->fetchRow()) {
				$columnId = $row['sloupec_id'];
				if (!isset($h['sloupec'][$columnId]) || mb_strlen($h['sloupec'][$columnId]) < 1 || !is_numeric($h['sloupec'][$columnId])) {
					$low = false;
					$this->errors[] =  I18n::tr('Sázka {0} -> Kurz {1} nemá správný formát',$k,$h['sloupectext'][$columnId]);
					$status = false;
				}
				else if($low) {
					if ($low > $h['sloupec'][$columnId]) {
						$low = $h['sloupec'][$columnId];
					}
					else {
						$low = $low;
					}
				}

				if (($h['sloupec'][$columnId] < $min_kurz || $h['sloupec'][$columnId] > $max_kurz) && $h['sloupec'][$columnId] != 1) {
					$min_max_status = false;
				}

				if (empty($complementCols[$columnId])) {
					if ($h['sloupec'][$columnId] > 0) {
						$vyhernost_num += (1/$h['sloupec'][$columnId]);
					}
				}
				else {
					$sum = 0;
					foreach ($complementCols[$columnId] as $complColId) {
						$sum += 1 / $h['sloupec'][$complColId];
					}
					$rate = 1 / $sum;
					if ($rate < 1.0)
						$rate = 1.0;
					if (abs($rate - $h['sloupec'][$columnId]) >= 0.01) {
						$this->errors[] =  I18n::tr('Sázka {0} -> Dopočítané sloupce se liší (dodatečná kontrola): posláno {1}, kontrola {2}', $k, $h['sloupec'][$columnId], $rate);
						$status = false;
					}
				}
			}

			$vyhernost_num = round($vyhernost_num,2);

			//TODO: take list of subtypes from DB
			$betSubtypesWithoutSureBet = array(
				30,92,93,105,108,208,209,215,216,224,225,226,227, // V234 and similar subtypes
				229, // supertip
			);
			if (!in_array(intval($h['podtyp']), $betSubtypesWithoutSureBet)) {
				if ($low && $low >= count($h['sloupec']) && count($h['sloupec']) > 1) {
					$this->errors[] =  I18n::tr('Sázka {0} -> Kurzy jsou vypsány chybně, při vsazení na všechny možnosti dojde k výhře.',$k);
					$status = false;
				}
			}

			if (!$min_max_status) {
				$this->errors[] =  I18n::tr('Sázka {0} -> Kurzy jsou vyšší nebo nižší nežli povolené; MIN: {1} MAX: {2}',$k,$min_kurz,$max_kurz);
				$status = false;
			}

			if ($h['typ'] != 29 && ($vyhernost_num < $min_vyhernost || $vyhernost_num > $max_vyhernost)) {
				$this->errors[] =  I18n::tr('Sázka {0} -> Výhernost je vyšší nebo nižší nežli povolená; MIN výhernost {1}',$k,$min_vyhernost);
				$status = false;
			}

		} //end foreach

		
		if($status) {
			$combBetween = array();

			foreach ($_POST['sazka'] as $k=>$h) {
				$changelog = array();
				$komb = array();
				$platna_od = Help::slash(It6_Date::toDb($h['platna_od']));
				$platna_do = It6_Date::toDb($h['platna_do']);
				$isSimple = (isset($h['jednoducha'])?1:0);
				$initStatus = 2;
				$betParentId = '';
				if (!empty($h['bet_parent_id'])) {
					$betParentId = intval($h['bet_parent_id']);
				}
				else if (!empty($_POST['groupParentId'])) {
					$betParentId = intval($_POST['groupParentId']);
				}

				$realTypeId = (empty($h['real_typ']) ? '' : intval($h['real_typ']));
				if (empty($realTypeId))
					$realTypeId = 'NULL';
				$textNote = (empty($h['text_note']) ? 'NULL' : "'" . Help::Slash($h['text_note']) . "'");
				$betInfo = (isset($h['info']) ? "'" . Help::Slash(trim($h['info'])) . "'" : '');
				if (!isset($h['risk'])) $h['risk'] = RISK_LIMIT;
				$sql = "
					INSERT INTO sazky (platna_od,platna_do,bookmaker_id,udalost_id,typ_id,real_typ_id,podtyp_id,text,ticket_text,jednoducha,risk_limit,ako,parent_id,status,text_note,info)
					VALUES (
						'".Help::slash($platna_od)."',
						'".Help::slash($platna_do)."',
						".$_SESSION['bookmaker'].",
						".intval($_POST['udalost']).",
						".intval($h['typ']).",
						$realTypeId,
						".intval($h['podtyp']).",
						'".Help::Slash($h['text'])."',
						'".Help::Slash($h['ticket_text'])."',
						$isSimple,
						".intval($h['risk']).",
						".intval($h['ako']).",
						".(empty($betParentId) ? 'NULL' : $betParentId).",
						$initStatus,
						$textNote,
				        $betInfo)";

				$res =& $this->dbGame->query($sql);
				DbUtil::testResult($res);

				$sql = "SELECT LAST_INSERT_ID() AS id FROM sazky";
				$res_id =& $this->dbGame->query($sql);
				if (!$row =& $res_id->fetchRow()) {
					$this->dbGame->rollback();
					throw new ExHandler('Nepodarilo se ziskat identifikator typ',"admin_ex_db");
				}	else {
					$sazka_id = $row['id'];
				}
				$changelog['betId'] = $sazka_id;
				$changelog['status'] = $initStatus;
				$changelog['confirmed'] = 0;
				$changelog['paidOut'] = 0;
				$changelog['validFrom'] = $platna_od;
				$changelog['validTo'] = $platna_do;
				$changelog['text'] = $h['text'];
				$changelog['ticketText'] = $h['ticket_text'];
				$changelog['textNote'] = $textNote;
				$changelog['simple'] = $isSimple;
				$changelog['ako'] = intval($h['ako']);

				if ($res) {
					$this->messages[] = I18n::tr('Bet #{0}, {1} was created',$sazka_id,$h['text']);
					It6_Log::info(
						"Bet id '%betId%' was created.",
						It6_Log::TAG_BOOKMAKER_OPERATION,
						array(
							'betId' => $sazka_id,
							'betName' => $h['text'],
							'platna_od' => $h['platna_od'],
							'platna_do' => $h['platna_do'],
							'bookmaker_id' => $_SESSION['bookmaker'],
							'udalost_id' => intval($_POST['udalost']),
							'typ_id' => intval($h['typ']),
							'podtyp_id' => intval($h['podtyp']),
							'ticket_text' => Help::Slash($h['ticket_text']),
							'jednoducha' => $isSimple,
							'risk_limit' => intval($h['risk']),
							'ako' => intval($h['ako']),
							'parent_id' => $betParentId,
							'status' => $initStatus
						)
					);
				} else {
					$this->error[] = I18n::tr('Error creating bet {0}',$h['text']);
					It6_Log::info(
						"Error creating bet '%bet%'.",
						It6_Log::TAG_BOOKMAKER_OPERATION,
						array('bet' => $h['text'])
					);
				}

				$this->addedBetIds[] = $sazka_id;

				$sql = "
					SELECT MAX(poradi) AS m
					FROM sazka_kurz
					WHERE sazka_id=".$sazka_id."
					GROUP BY sazka_id";

				$res =& $this->dbGame->query($sql);

				if (!$row =& $res->fetchRow()) {
					$poradi = 1;
				}
				else {
					$poradi = $row['m'];
				}

				$changelog['rates'] = array();
				foreach ($h['sloupec'] as $k => $h2) {
					$columnId = intval($k);
					$rate = floatval($h2);

					$sql = "INSERT INTO sazka_kurz (sazka_id,sloupec_id,poradi,kurz,platny_od)
									VALUES ($sazka_id,$columnId,$poradi,$rate,'$platna_od')";
					$res =& $this->dbGame->query($sql);
					DbUtil::testResult($res);

					$sql = "INSERT INTO bet_column(sazka_id,sloupec_id,risk_limit_balance) VALUES ($sazka_id,$columnId,0)";
					$res =& $this->dbGame->query($sql);
					DbUtil::testResult($res);

					$changelog['rates'][$columnId] = $rate;
				}

				// pokud jsou zasktnuty kombinace, nebo se jedna o urcity druh sazky => kombinovat
				if (isset($h['kombinace']) || in_array($h['typ'],$this->betTypesToCombine)) {
					$combBetween[] = $sazka_id;
				}

				$show_kombinace = 0;
				if(isset($h['kombinace_show'])) $show_kombinace = 1;

				//create combinations
				$changelog['correlated'] = array();
				if (isset($h['kombinace_jine']) && mb_strlen($h['kombinace_jine']) > 0 && !isset($h['kombinace_indv']))
					$combOthers = explode(";",$h['kombinace_jine']);
				else
					$combOthers = array();
				if ( !empty($combOthers) || isset($h['kombinace']) || in_array($h['typ'],$this->betTypesToCombine) || ($betParentId != '')) {

					$komb = array_unique(array_merge($komb, $combOthers));
					$komb = array_unique(array_merge($komb, $combBetween));
					if ($betParentId != '') {
						$corrBets = It6_Models_Bet::readRealCorrelatedBets($betParentId,'list', true);
						if (!empty($corrBets)) {
							$corrBets = explode(',',$corrBets);
							$komb = array_unique(array_merge($komb, $corrBets));
						}
						if (!in_array($betParentId, $komb))
							$komb[] = $betParentId;
					}
					foreach($komb as $key => $combBetId) {
						if ($combBetId!='' && $combBetId != $sazka_id) {
							$sql = "
								REPLACE INTO sazka_kombinace (sazka1_id,sazka2_id,kombinace_show)
								VALUES (".$sazka_id.",".$combBetId.",". $show_kombinace.")";

							$res =& $this->dbGame->query($sql);
							DbUtil::testResult($res);
							if ($res) $this->messages[] = I18n::tr('Bet combination {0} - {1} was created.', $sazka_id, $combBetId);
							$changelog['correlated'][] = $combBetId;
						}
					}
					It6_Log::info(
						"Bet combinations to bet %betId% were created.",
						It6_Log::TAG_BOOKMAKER_OPERATION,
						array('betId' => $sazka_id, 'combinations' => $komb)
					);
				}

				$change = It6_Models_BetChangelog::getBetChange(null, $changelog);
				$changeId = It6_Models_BetChangelog::saveBetChange($change);

			} //foreach $post
		} //if status end

		$this->dbGame->autoCommit(true);
		$this->dbGame->commit();

		foreach ($this->addedBetIds as $betId) {
			//prirazeni aliasu
			$ws = Zend_Registry::get('ws');
			$alias = $ws->Bet->generateAlias($betId);

			if ($alias) {
				$this->messages[] = I18n::tr('Bet Id {0} got alias {1}.', $betId, $alias);
				It6_Log::info(
					"Bet '%bet%' got alias '%alias%'.",
					It6_Log::TAG_BOOKMAKER_OPERATION,
					array('bet' => $betId, 'alias' => $alias)
				);
				if ($ws->Bet->activateBet($betId)) {
					$this->messages[] = i18n::tr('Bet {0} was activated.',$betId);
					It6_Log::info(
						"Bet #'%bet%' was activated.",
						It6_Log::TAG_BOOKMAKER_OPERATION,
						array('bet'=> $betId)
					);
					$this->processedBetsOk++;
				} else {
					$this->errors[] = i18n::tr('Bet {0} : error activating bet.',$betId);
					$this->processedBetsError++;
				}
			} else {
				$this->errors[] = I18n::tr('Error by generating alias for bet Id {0}.', $betId);
				It6_Log::err(
					"Error generating alias of the bet '%bet%'.",
					It6_Log::TAG_BOOKMAKER_OPERATION,
					array('bet' => $betId)
				);
			}
			It6_GlobalCache_Invalidator::invalidateSportsbookByBet($betId);
		}
	}

public function printFeedback() {
	$okCount = $this->processedBetsOk;
	$errCount = $this->processedBetsError;
	if (0 == $this->processedBetsOk)
		$okCount = count($this->messages);
	if (0 == $this->processedBetsError)
		$errCount = count($this->errors);

	
	$feedback = UiUtil::printMessages($this->messages) . UiUtil::printErrors($this->errors);
	$out = UiUtil::printWarnings(i18n::tr('** TOTAL: {0} success / {1} errors.',$okCount, $errCount). UiUtil::wrapFeedback($feedback));

	return $out;
}

 /**
 * metoda vypise vsechny zadane typy
 * @return void
 */
	public function ShowBet(){

		$preklad = new Preklady();
		$udalost = $help = $sport_str = "";
		$prava_udalost = $sport_udalost = array();

		if($_SESSION['superbookmaker'] != 1){
			$sql = "select udalost_id from prava_udalost where  bookmaker_id=".intval($_SESSION['bookmaker'])." and povoleny=1";
			$res2 =& $this->dbGame->query($sql);
			if(DB::isError($res2)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky prava_udalost',"admin_ex_db");
			while ($row2 =& $res2->fetchRow()) 
				$prava_udalost[$row2['udalost_id']] = $row2['udalost_id'];
		}
	$eventId = (!empty($_POST['udalost']) ? $_POST['udalost'] : null);

	$_sports = It6_SportEventsFilter::getSportEvents(empty($_POST['sportx']) ? null : $_POST['sportx']);
	list($htmlSports, $htmlEvents) = It6_SportEventsFilter::getSportFilterHtml(
		$_sports,
		(empty($_POST['sportx']) ? null : $_POST['sportx']),
		(empty($eventId) ? null : $eventId),
		'choose_sport',
		'choose_event'
	);
	$this->filterEventsSports = array();
	$this->filterEvents = array();
	if (!empty($eventId)) {
		foreach ($_sports as $s) {
			foreach ($s[2] as $r) {
				foreach ($r[2] as $e) {
					if ($e['eventId'] == $eventId) {
						if (!array_key_exists($s[0], $this->filterEventsSports))
							$this->filterEventsSports[$s[0]] = array('id' => $s[0], 'name' => $s[1]);
						$this->filterEvents[$e['eventId']] = array(
							'eventId' => $e['eventId'],
							'sportId' => $s[0],
							'name' => $e['name'],
						);
					}
				}
			}
		}
	}
	unset($_sports);

	$d = 0;
	if (isset($_POST['plus']))
		$d = 1;
	else if (isset($_POST['minus']))
		$d = -1;
	if (0 != $d && isset($_POST['betCurrentCount']) && $_POST['betCurrentCount'] > 0)
		$this->betCount = $_POST['betCurrentCount'] + $d;
	else if (isset($_POST['groupBetCount']) && intval($_POST['groupBetCount']) > 0)
		$this->betCount = intval($_POST['groupBetCount']);
	else if (isset($_POST['betCount']) && $_POST['betCount'] > 0)
		$this->betCount = $_POST['betCount'];
	else if (isset($_POST['pocet']) && intval($_POST['pocet']) > 0)
		$this->betCount = intval($_POST['pocet']);

	//multi-sazka
	if($this->multisazka && isset($_POST['sazka'])) {
		if(isset($_POST['multipocet']))$_POST['pocet'] = intval($_POST['multipocet']);
		if(!isset($_POST['multipocet']) || $_POST['pocet'] == 0) $_POST['pocet'] = 1;

		if(isset($_POST['multipocet']) && isset($_POST['minus'])) $_POST['pocet'] = intval($_POST['pocet']-1);
		else if(isset($_POST['multipocet']) && isset($_POST['plus'])) $_POST['pocet'] = intval($_POST['pocet']+1);

/*
		for($x = 1;$x <= $this->betCount; $x++){
			$_POST['sazka'][$x]['typ']   =  $_POST['sazka'][1]['typ'];
			$_POST['sazka'][$x]['podtyp'] =  $_POST['sazka'][1]['podtyp'];

		}
*/
	}

	$this->vrat .= '<h3>'.I18n::tr('Create bet').'</h3>';

/*
	$this->vrat .= UiUtil::printMessages($this->messages);
	
	$this->vrat .= ;
*/

	if ( (count($this->messages) > 0) || (count($this->errors) > 0) )
		$this->vrat .= $this->printFeedback();

	$this->vrat .= "<form method=\"post\" action=\"?superb=1&section=".$this->section."\">";

	$this->vrat .= '<div class="actions">';
	$this->vrat .= '<select name="sportx" class="left m-1"  onchange="this.form.submit()">' . $htmlSports . '</select>';
	$this->vrat .= '<select name="udalost" class="sel-1" size="20" onchange="this.form.submit()">' . $htmlEvents . '</select>';
	$this->vrat .= '</div>';

	if (!empty($eventId) && !empty($this->filterEvents[$eventId]))
		$sportId = $this->filterEvents[$eventId]['sportId']; //$sport_udalost[$_POST['udalost']];
	else
		$sportId = null;

	$betId = (isset($_POST['sazka']) ? $_POST['sazka'] : array());

	$this->vrat .= $this->BetForm($sportId,$eventId,$betId,$this->betCount);

	$this->vrat .= '<input type="hidden" name="sport" value="'.(!empty($sportId) ? $sportId : 0).'" />
					<input type="hidden" name="pocet" value="'.$this->betCount.'" />
					<input type="hidden" name="betCurrentCount" value="'.$this->betCount.'" />
					<input type="hidden" name="multi" value="1" />
					</form><a name="bo"></a>';

  $this->vrat .= '<script language="Javascript" type="text/javascript">'.$this->jsscript.$this->jsLoadLiability.'</script>';
}

 /**
 * Vypis formulare
 * @param int $sport_id sport id
 * @param int $udalost_id udalost id
 * @param array $sazky pole obsahuje typ a podtyp + kurzy pokud jsou zadany
 * @param int $pocet_sazek pocet sazek na strance
 * @return string
 */
 private function BetForm($sport_id=null,$udalost_id=null,array $sazka,$pocet_sazek = 1) {
	$db = Zend_Registry::get('db');
	$ws = Zend_Registry::get('ws');

	$sportId = $sport_id;
	$eventId = $udalost_id;

	
  $preklad = new Preklady();
  $vrat = $js_script = $jsLoadLiability = "";
  $ArrayTyp = $ArrayPodTyp = array();
  $submit = true;

  if($sport_id != null && $udalost_id != null) {

		$types = It6_Models_BetType::getAllForEvent($eventId, 1, $db);
		list($nonderivedTypes, $groupMasters, $typeGroups) = It6_Models_BetType::getNonderivedAndGroups($types);

		$prevTypeId = intval(empty($_POST['currentTypeId']) ? 0 : $_POST['currentTypeId']);
		$typeId = (isset($_POST['globalTypeId']) ? $_POST['globalTypeId'] : 0 );
		$groupParentId = (empty($_POST['groupParentId']) ? 0 : intval($_POST['groupParentId']));
		$vrat .= '<input type="hidden" name="currentTypeId" value="'.$typeId.'" />';
		
		if (empty($types[$typeId])) {
			$typeIsGroup = false;
			$typeIsMaster = false;
		}
		else {
			$typeIsGroup = isset($types[$typeId]['aliasGroup']);
			$typeIsMaster = !empty($types[$typeId]['groupMaster']);
		}

		if (!empty($groupParentId) && !empty($typeId))
			$freeGroupTypeIds = It6_Models_BetType::getParentBetFreeDerivedTypeIds($groupParentId, $typeId, $db);
			
		else
			$freeGroupTypeIds = false;

		$vrat .= '
			<div class="actions">
				<input type="button" value="Označit kombinace" class="sinput3" onclick="jQuery.SetCombClick(1);"   />
				&nbsp;
				
				<input type="button" value="Označit zobrazení kombinace" class="sinput3 "  onclick="jQuery.SetCombClick(2);" />
				&nbsp;
				<input type="button" value="Nastavit datum" class="sinput3" onclick="SetDatum()" />
				&nbsp;
				Platná od:
				<input type="text" value="" id="valid_from" name="valid_from" class="sinput3 dateTime" />
				<img src="images/ico/calendar.gif" class="calendar-icon">
				Platná do:
				<input type="text" value="" id="valid_to" name="valid_to" class="sinput3 dateTime" />
				<img src="images/ico/calendar.gif" class="calendar-icon">'
				. ($typeIsGroup ? '' : '<input type="button" value="Nastavit ID hlavní sázky" class="sinput3" onclick="massSet(\'bet_parent_id\')" />
				&nbsp;
				<input type="text" style="width:70px" value="" id="bet_parent_id" name="bet_parent_id" class="sinput3" />')
				. '&nbsp;
				<input type="button" value="Rozkopírovat první" class="sinput3" onclick="massCopyFirstBet();"   />
				&nbsp;
			</div>
		';

		$typesHtml = '';
		foreach($nonderivedTypes as $id => $type){
			if (empty($type['groupMaster']))
				$_name = $type['nameLocal'];
			else {
				$_name = $type['aliasGroupLocal'];
				$_group = $type['aliasGroup'];
				$_count = count($typeGroups[$_group]);
				if ($id == $typeId && false !== $freeGroupTypeIds)
					$_count = count($freeGroupTypeIds) . "/$_count";
			}
			$typesHtml .= '
				<option  value="'.$id.'" '.($typeId == $id ? 'selected="selected"' : '').'>
					'.Help::Html(
						empty($type['groupMaster'])
						? $type['nameLocal']
						: $type['aliasGroupLocal'] . " ($_count)"
					)
				.'</option>
			';
		}
			$vrat .= '
				<div class="actions">
					<select
						name="globalTypeId"
						class="sel-1"
						onchange="this.form.action=this.form.action;$(\'#sazka-podtyp\').val(23);this.form.submit();"
					>
						<option  value="0">'.I18n::tr('bet_type').'</option>
						'.$typesHtml.'
					</select>
					<!--<select
						name="xsas"
						class="sel-1"
						onchange="this.form.action=this.form.action+\'#sazka\';$(\'#sazka-podtyp\').val(23);this.form.submit();"
					>
						<option  value="0">'.I18n::tr('bet_type').'</option>
						'.$typesHtml.'
					</select>-->
			';

			$subtypes = $this->getBetSubTypes($typeId, $sportId);
			$vrat .= '&nbsp;<strong>'.$subtypes['element'].'</strong>&nbsp;&nbsp;';
			$subTypeCount = $subtypes['count'];
			$defaultSubTypeId = $subtypes['default'];
			$ArrayPodTyp = $subtypes['array'];
			

			$subTypeId = $defaultSubTypeId;
			
			if ($subTypeId==0)
				$subTypeId = (!empty($_POST['sazka-podtyp']) ? $_POST['sazka-podtyp'] : 0);

			if (empty($subTypeId))
				$complementColumns = array();
			else
				$complementColumns = It6_Models_BetSubtype::getComplementColumns($subTypeId);
			$vrat .= "<script type=\"text/javascript\">\nvar complementColumns = [];\n";
			foreach ($complementColumns as $columnId => $complementIds) {
				$vrat .= "complementColumns[$columnId] = [" . implode(',', $complementIds) . "];\n";
			}
			$vrat .= "</script>\n";
/*
			if($this->multisazka && isset($sazka[$x]['podtyp'])) {
*/
			if (!$typeIsGroup) {
				if ($subTypeCount > 0) {
					$vrat .= "Počet sázek tohoto typu:
								<input type=\"text\" class=\"short\" value=\"".(isset($_POST['betCount']) ? ($_POST['betCount']) : "1")."\" name=\"betCount\" />
								<input type=\"submit\" class=\"sinput2\" value=\"OK\" name=\"multi\" />";
				}
				//}
			}
			$vrat .= '</div>';
	//$vrat .= '</div>';

			$prevGroupParentId = (empty($_POST['currentGroupParentId']) ? 0 : intval($_POST['currentGroupParentId']));
			$vrat .= '<input type="hidden" id="currentGroupParentId" name="currentGroupParentId" value="'.$groupParentId.'" />';
			
			if ($typeIsGroup) {
				$_count = count($typeGroups[$nonderivedTypes[$typeId]['aliasGroup']]);
				$vrat .=
					'<div class="actions">
						<label for="groupParentId">Parent bet ID:</label>
						<input type="text" size="8" id="groupParentId" name="groupParentId" value="'.(0 == $groupParentId ? '' : $groupParentId).'" />
						<img class="img catalog"
							onclick="openCatalog(\'/?section=292\',\'groupParentId\',800,600,\'\',\'\',\'1\',\''. intval($udalost_id) . '\');"
							alt="Nadřazená sázka" title="Nadřazená sázka" src="_clip/translate.gif"
						/>
						<label for="groupBetCount">Group bet count:</label>
						<input type="text" size="3" id="groupBetCount" name="groupBetCount" value="'.$this->betCount.'" />
						<input type="button" value="Odeslat"
							onclick="
								if ( $(\'#groupBetCount\').val() > '.$_count.') {
									alert(\'Max count is: '.$_count.'\');
									return false;
								};
								$(\'#currentGroupParentId\').val(\'\');this.form.submit();"
						/>';
				if($typeId == 143) {
					$vrat .= 
						'<img class="img catalog"
							onclick="openCatalog(\'/?section=316\',\'groupParentId\',800,600,\'\',\'\',\'1\',\''. intval($udalost_id) . '\');"
							alt="Střelci" title="Střelci" src="_clip/translate.gif"
						/>';
				}
				$vrat .= '</div>';
			}

			if ($typeIsGroup && !empty($groupParentId)) {
				$groupParent = $ws->Bet->getById($groupParentId);
				if (empty($groupParent)) {
					$vrat .= UiUtil::printErrors('Parent bet not found');
					$groupParentId = 0;
				}
			}

			if ($typeIsGroup) {
				$groupId = $nonderivedTypes[$typeId]['aliasGroup'];
				$groupTypeIds = $typeGroups[$groupId];
				$groupSize = count($groupTypeIds);
				$groupFreeCount = count($freeGroupTypeIds);
				$this->betCount = intval($this->betCount);
				if ($this->betCount < 1)
					$this->betCount = 1;
				if ($prevTypeId != $typeId || $this->betCount > $groupFreeCount)
					$this->betCount = $groupFreeCount;
			}

			if (!$typeIsGroup || !empty($groupParentId)) {
				if (empty($sazka)) {
					for ($x = 1; $x <= $this->betCount; ++$x)
						$sazka[$x] = array();
				}

				if ($typeIsGroup) {
					if ($prevGroupParentId != $groupParentId) {
						foreach ($sazka as &$bet) {
							$bet['text'] = $groupParent['name'];
							$bet['platna_do'] = It6_Date::fromDb($groupParent['validToTime']);
							$bet['kombinace'] = 1;
						}
						unset($bet);
					}
				}
/*
		if(isset($sazka[$x]['typ']) && $sazka[$x]['typ'] != 0 && mb_strlen($podtyp) > 0) {
			if ($podtypPocet == 1)
				$vrat .= $podtyp_jeden;
			else {
				$vrat .=
					'<select
						name="sazka['.$x.'][podtyp]"
						style="font-size:0.9em"
						id="sazka-podtyp"
						onchange="this.form.action=this.form.action+\'#sazka\'+'.($x).';this.form.submit();"
					>
						<option  value="0">'.I18n::tr('bet_subtype').'</option>
						'.$podtyp.'
					</select>
				';
			}
		}
		if(isset($sazka[$x]['typ']) && $sazka[$x]['typ'] != 0 && mb_strlen($podtyp) < 1) {
			$vrat .= UiUtil::printErrors('Pro tento druh není definován žádný podtyp');
		}
		if($this->multisazka && isset($sazka[$x]['podtyp'])) {
			$vrat .= "Počet sázek tohoto typu:
						<input type=\"text\" class=\"short\" value=\"".(isset($_POST['pocet'])?Help::Html($_POST['pocet']):"")."\" name=\"multipocet\" />
						<input type=\"submit\" class=\"sinput2\" value=\"OK\" name=\"multi\" />";
		}
		$vrat .= '</div>';
*/


	for($x=1; $x<=$this->betCount; $x++) {
		$realTypeId = ($typeIsGroup ? $freeGroupTypeIds[$x - 1] : $typeId);
		$typ = $podtyp = "";
		$ArrayPodTyp = array();
		$min = $max = $vyh_min = $vyh_max = $risk = 0;
		//if(!isset($sazka[$x]['typ']) || !isset($sazka[$x]['podtyp'])) continue;
		//if (!(isset($udalost_id) && isset($sazka[$x]['typ']) && isset($sazka[$x]['podtyp']))) break;

		$betSettings = $this->getBetSettings($eventId, $typeId, $subTypeId);
		if (empty($sazka[$x]['text_note'])) {
			$textNote = It6_Models_Bet::getDefaultTextNote($typeId, $eventId);
			if (!isset($textNote))
				$textNote = '';
		}
		else {
			$textNote = $sazka[$x]['text_note'];
		}
/*
		$sql = "
			SELECT kurz_min, kurz_max, vyhernost_min, vyhernost_max, risk_limit
			FROM bet_settings
			WHERE udalost_id=".intval($udalost_id)."
				AND typ_id=".(isset($sazka[$x]['typ']) ? intval($sazka[$x]['typ']) : 0)."
				AND podtyp_id=".( ((isset($sazka[$x]['podtyp'])) && (!$sazka[$x]['podtyp']==0) ) ? intval($sazka[$x]['podtyp']) : $podtypInit);

		$res =& $this->dbGame->query($sql);
		
		//if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber bet_settings',"admin_ex_db");
*/

		$min = $betSettings['kurz_min'];
		$max = $betSettings['kurz_max'];
		$vyh_min = $betSettings['vyhernost_min'];
		$vyh_max = $betSettings['vyhernost_max'];
		$risk = $betSettings['risk_limit'];


		

/*
			$vrat .= '
				<div class="actions">
					<select
						name="sazka['.$x.'][typ]"
						class="sel-1"
						onchange="this.form.action=this.form.action+\'#sazka\'+'.($x).';$(\'#sazka-podtyp\').val(23);this.form.submit();"
					>
						<option  value="0">'.I18n::tr('bet_type').'</option>
						'.$typ.'
					</select>
			';
$vrat .= '</div>';
*/
/*
		if(isset($sazka[$x]['typ']) && $sazka[$x]['typ'] != 0){
			$sql = "
				SELECT a.podtyp_id, a.interni_nazev, a.radek_sloupec, a.sloupec_pocet_max
				FROM podtyp a
				INNER JOIN typ_podtyp b
					ON a.podtyp_id=b.podtyp_id
				WHERE b.typ_id=".intval($sazka[$x]['typ'])."
					AND b.sport_id=".intval($sport_id)."
				ORDER BY (a.interni_nazev)
			";

			$res =& $this->dbGame->query($sql);
			if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber podtypu',"admin_ex_db");

			$podtypPocet = $res->numRows();
			while ($row =& $res->fetchRow()){
				$podtyp .= '
					<option
						value="'.$row['podtyp_id'].'"
						'.(isset($sazka[$x]['podtyp']) && $sazka[$x]['podtyp'] == $row['podtyp_id'] ? 'selected="selected"' : '').'
						'.($res->numRows() == 1 ? 'selected="selected"' : '').'
					>
						'.Help::Html($row['interni_nazev']).'
						'.($row['radek_sloupec'] ? "(vertikál)" : "(horizontál)").'
					</option>
				';

				if($podtypPocet  == 1) {
					$sazka[$x]['podtyp'] = $row['podtyp_id'];
					$podtyp_jeden = Help::Html($row['interni_nazev']).($row['radek_sloupec'] ? "(vertikál)" : "(horizontál)").'<input type="hidden" name="sazka-podtyp" id="sazka-podtyp" value="'.$row['podtyp_id'].'" />';
				}
			}
		}
*/


		$vrat .=  '<a name="sazka'.$x.'"></a>';

	/*	if(!$this->multisazka || ($this->multisazka && $x == 1)){*/
/*
			$vrat .= '
				<div class="actions">
					<select
						name="sazka['.$x.'][typ]"
						class="sel-1"
						onchange="this.form.action=this.form.action+\'#sazka\'+'.($x).';$(\'#sazka-podtyp\').val(23);this.form.submit();"
					>
						<option  value="0">'.I18n::tr('bet_type').'</option>
						'.$typ.'
					</select>
			';
*/

/*			if(isset($sazka[$x]['typ']) && $sazka[$x]['typ'] != 0 && mb_strlen($podtyp) > 0) {
				if ($podtypPocet == 1)
					$vrat .= $podtyp_jeden;
				else {
					$vrat .=
						'<select
							name="sazka['.$x.'][podtyp]"
							style="font-size:0.9em"
							id="sazka-podtyp"
							onchange="this.form.action=this.form.action+\'#sazka\'+'.($x).';this.form.submit();"
						>
							<option  value="0">'.I18n::tr('bet_subtype').'</option>
							'.$podtyp.'
						</select>
					';
				}
			}
			if(isset($sazka[$x]['typ']) && $sazka[$x]['typ'] != 0 && mb_strlen($podtyp) < 1) {
				$vrat .= UiUtil::printErrors('Pro tento druh není definován žádný podtyp');
			}
			if($this->multisazka && isset($sazka[$x]['podtyp'])) {
				$vrat .= "Počet sázek tohoto typu:
							<input type=\"text\" class=\"short\" value=\"".(isset($_POST['pocet'])?Help::Html($_POST['pocet']):"")."\" name=\"multipocet\" />
							<input type=\"submit\" class=\"sinput2\" value=\"OK\" name=\"multi\" />";
			}
			$vrat .= '</div>';
		}*/

		//if($this->multisazka && $x > 1) $vrat .= '<input type="hidden" name="sazka['.$x.'][typ]" value="'.(isset($sazka[$x]['typ'])?$sazka[$x]['typ']:0).'" /><input type="hidden" name="sazka['.$x.'][podtyp]" value="'.(isset($sazka[$x]['podtyp'])?$sazka[$x]['podtyp']:0).'" />';
 
		if(!$this->multisazka || ($this->multisazka && (isset($_REQUEST['multi']) || isset($_POST['create'])))){

		//$ArrayPodTyp = $ArrayPodTyp[0];
		
		//if(isset($sazka[$x]['podtyp']) && $sazka[$x]['podtyp'] != 0 && mb_strlen($podtyp) > 1) {
			//exit;
			$sql = "
				SELECT a.text, a.radek_sloupec, a.sloupec_pocet_max, b.sloupec_id, b.nazev
				FROM podtyp a
				INNER JOIN podtyp_sloupce b
					ON a.podtyp_id=b.podtyp_id
				WHERE b.podtyp_id=".intval($subTypeId)."
				ORDER by b.poradi
			";
			$res =& $this->dbGame->query($sql);

			if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber podtypu',"admin_ex_db");

			while ($row =& $res->fetchRow()) {
				if(!isset($ArrayPodTyp['text'])) {
					$r = $preklad->selectData("WHERE lang_id=1 AND index_pole='".Help::Slash($row['text'])."'");

					if (!$row2 =& $r->fetchRow() || mb_strlen($row2['text']) < 0)
						$row2['text'] = "";

					$ArrayPodTyp['text'] = $row2['text'];
					$ArrayPodTyp['radek'] = $row['radek_sloupec'];
					$ArrayPodTyp['radek_max'] = $row['sloupec_pocet_max'];
				}

				$r = $preklad->selectData("WHERE lang_id=1 AND index_pole='".Help::Slash($row['nazev'])."'");

				if (!$row2 =& $r->fetchRow() || mb_strlen($row2['text']) < 0)
					$row2['text'] = $row['nazev'];

				$ArrayPodTyp['sloupec'][$row['sloupec_id']] = $row2['text'];
			}
		//}
/*
		else
			$submit = false;
*/
	  }
	  //Pokud se jedna o typy sazky Nejlepší střelec a Střelci, tak nactu hrace tymu pro jquery autocomplete 
	  if ($typeId == 52 || $typeId == 143) {
	  	$ws = Zend_Registry::get('ws');

		if (!empty($groupParentId))
			$players = It6_ArrayWrapper::toNativeArray($ws->TeamPlayer->getByBet($groupParentId));
		else 
			$players = It6_ArrayWrapper::toNativeArray($ws->TeamPlayer->getByEvent(intval($udalost_id)));
		
	  	$players = Models_Utils::getSelectOptions($players, 'teamPlayerId', 'name');

	  	$availablePlayersJs = '["' . implode('","', $players) . '"]';

	  	$vrat .= '<script>
			$(function() {
				var availableTags = ' . $availablePlayersJs .';
				function split( val ) {
					return val.split( /:\s*/ );
				}
				function extractLast( term ) {
					return split( term ).pop();
				}
				
				$( "#sazka\\\['.$x.'\\\]\\\[text\\\]" )
				.bind( "keydown", function( event ) {
				if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).data( "autocomplete" ).menu.active ) {
					event.preventDefault();
				}
			}).autocomplete({
					minLength: 0,
				source: function( request, response ) {
					// delegate back to autocomplete, but extract the last term
					response( $.ui.autocomplete.filter(
						availableTags, extractLast( request.term ) ) );
				},
				focus: function() {
					// prevent value inserted on focus
					return false;
				},
				select: function( event, ui ) {
					var terms = split( this.value );
					// remove the current input
					terms.pop();
					// add the selected item
					terms.push( ui.item.value );
					// add placeholder to get the comma-and-space at the end
					terms.push( "" );
					this.value = terms.join( ":" ).replace(/:$/, "");
					return false;
				}
				});
			});
				
	</script>';
	  }
	  #vypis sazecich formularu#
	if ($subTypeCount > 0) {
		$vrat .= '<table class="bet-detail" style="width:100%">';
		if ($typeIsGroup)
			$vrat .= '<tr>
					<td colspan="2" class="bettabhead2">
						Sázka <strong>'.$x.'</strong>,
						'.$this->filterEvents[$udalost_id]['name'].'
					</td>
					<td class="bettabhead2">'.Help::Html($types[$realTypeId]['nameLocal']).'</td>
				</tr>';
		else
			$vrat .= '<tr>
					<td colspan="3" class="bettabhead2">
						Sázka <strong>'.$x.'</strong>,
						'.$this->filterEvents[$udalost_id]['name'].'
					</td>
				</tr>';
		$vrat .= (mb_strlen($ArrayPodTyp['text'])>0?'<tr><td colspan="3" class="bettabhead">'.Help::Html($ArrayPodTyp['text']).'</td></tr>':"").'
				<tr>
					<td colspan="3">
						'.I18n::tr('Bet text').'
						<input
							autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true"
							type="text"
							id="sazka['.$x.'][text]"
							name="sazka['.$x.'][text]"
							style="font-size:1.1em;width:500px;"
							class="ui-autocomplete-input bet-input bet-text '.($ArrayPodTyp['radek'] == 0?"mandatory":"").'"
							value="'.(isset($sazka[$x]['text'])?Help::Html($sazka[$x]['text']):"").'"
						/>
	<!--
						<a href="javascript:openWin(\'ciselnik.php\',\'sazka['.$x.'][text]\',\'tymy\',800,500,\'sazka['.$x.'][ticket_text]\');void(0);">
							<img src="_clip/translate.gif" alt="Týmy" class="img" />
						</a>
	-->

						'.UiUtil::printCatalogIcon('TEAM','sazka['.$x.'][text]','sazka['.$x.'][ticket_text]','').'

						Od:
						<input
							type="text"
							style="width:130px"
							id="sazka['.$x.'][platna_od]" name="sazka['.$x.'][platna_od]"
							class="mandatory sinput3 dateTime"
							name=""
							value="'.(isset($sazka[$x]['platna_od'])?Help::Html($sazka[$x]['platna_od']):It6_Date::now()).'"
						/>';

					$vrat .='<img src="images/ico/calendar.gif" class="calendar-icon">

						Do:
						<input
							type="text"
							style="width:130px"
							id="sazka['.$x.'][platna_do]" name="sazka['.$x.'][platna_do]"
							class="mandatory sinput3 dateTime"
							name=""
							value="'.(isset($sazka[$x]['platna_do'])?Help::Html($sazka[$x]['platna_do']):"").'"
						/>
						<img src="images/ico/calendar.gif" class="calendar-icon">
					</td>
				</tr>
				<tr>
					<td colspan="3">';

//var_dump($ArrayPodTyp);

	$vrat .= '<span style="float: left; margin-right:8px">'.I18n::tr('Bet text ticket').'
						<input
							type="text"
							id="sazka['.$x.'][ticket_text]"
							name="sazka['.$x.'][ticket_text]"
							style="font-size:1.1em;width:250px;"
							class="bet-input bet-text"
							value="'.(isset($sazka[$x]['ticket_text'])?Help::Html($sazka[$x]['ticket_text']):"").'"
						/></span>';
						
	$vrat .= '<span style="float: left; margin-right:8px; clear:left;">'.I18n::tr('Hranice/poznámka')
		.'<input type="text" id="sazka['.$x.'][text_note]" name="sazka['.$x.'][text_note]" class="bet-input bet-text" style="width:100px;" value="'.htmlspecialchars($textNote).'" /></span>';
	$vrat .= '<span style="float: left; margin-right:8px; clear:left;">'.I18n::tr('Poznámky').'<img class="img" alt="Externí info" src="_clip/chat_ext.png">' 
		.'<input type="text" id="sazka['.$x.'][info]" name="sazka['.$x.'][info]" class="bet-input bet-text" style="width:150px;" value="'.(isset($sazka[$x]['info']) ? htmlspecialchars($sazka[$x]['info']) : '').'" /></span>';
	$vrat .= '<div class="rates-create" style="clear:left;float:left; margin: 7px 2px">
			<table class="'.($ArrayPodTyp['radek'] == 1?"rates-col":"rates-row").'">';
						
			$this->jsscript .= 'vyhernost['.$x.'] = new Object();';

			//sloupcovy vypis
			if($ArrayPodTyp['radek'] == 1) { 
				$tr = array();$y = $xxx = ($x*40);
				foreach ($ArrayPodTyp['sloupec'] as $k => $h) {
					if(!isset($tr[$y])) $tr[$y] = "";
					$ro = (empty($complementColumns[$k]) ? '' : 'readonly="readonly"'); // complementary column are read only
					$tr[$y] .= '<th valign="top">'.$h.'<input type="hidden"  name="sazka['.$x.'][sloupectext]['.$k.']" value="'.$h.'" /></th><td valign="top"><input type="text" tabindex="'.$xxx.'" style="height:20px;font-size:14px;" name="sazka['.$x.'][sloupec]['.$k.']" id="sazka['.$x.'][sloupec]['.$k.']" onkeyup="fixDecimalComa(this); SetLastRateByLiabilty('.$x.', complementColumns, this, true);this.focus();" class="sinput2" value="'.(isset($sazka[$x]['sloupec'][$k])?Help::Html($sazka[$x]['sloupec'][$k]):"")."\" $ro />" . ' <input type="text" id="pravdepodobnost_'.$x.'_'.$k.'" title="Pravděpodobnost" readonly="readonly" class="hidden sinput2" /></td>';
					$y++;
					if($y == $ArrayPodTyp['radek_max']) $y = 0;
					$this->jsscript .= 'vyhernost['.$x.']['.$k.'] = 1;';
					$xxx++;
				}
				foreach($tr as $h) 
					$vrat .= '<tr>'.$h.'</tr>';
			} else { //radkovy vypis
				$vrat .= '<tr>';
				foreach($ArrayPodTyp['sloupec'] as $k=>$h) {
					$vrat .= '<th>'.$h.'<input type="hidden" name="sazka['.$x.'][sloupectext]['.$k.']" value="'.$h.'" /></th>';
				}
				$vrat .= '</tr><tr>';
				$xxx = ($x*40);
				$colNumber = 0;
				foreach ($ArrayPodTyp['sloupec'] as $k => $h) {
					$colNumber++;
					$ro = (empty($complementColumnss[$k]) ? '' : 'readonly="readonly"'); // complementary column are read only
					$vrat .= '<td><input type="text" 
					id="sazka['.$x.'][sloupec]['.$k.']" 
					onfocus="saveRateToEdit('.$x.',complementColumns); SetLastRateByLiabilty('.$x.', complementColumns, this, true, ['.implode(",", array_keys( $ArrayPodTyp['sloupec'])).'], ' . $colNumber . ');" 
					style="height:20px;font-size:14px;" name="sazka['.$x.'][sloupec]['.$k.']" 
					class="sinput2"  
					tabindex="'.$xxx.'" 
					onkeyup="fixDecimalComa(this); saveRateToEdit('.$x.',complementColumns); SetLastRateByLiabilty('.$x.', complementColumns, this, true, ['.implode(",", array_keys( $ArrayPodTyp['sloupec'])).'], ' . $colNumber . ');" 
					value="'.(isset($sazka[$x]['sloupec'][$k])?Help::Html($sazka[$x]['sloupec'][$k]):"")."\" $ro />" . '<br /><input type="text" id="pravdepodobnost_'.$x.'_'.$k.'" 
					readonly="readonly" title="Pravděpodobnost" class="hidden sinput2" /></td>';
					$this->jsscript .= 'vyhernost['.$x.']['.$k.'] = 1;';
					$xxx++;
				}
				$vrat .= '</tr>';
			}

	/*
			$liabilityCombo = '<select id="vyhernost_ctrl_'.$x.'" onchange="SetLastRateByLiabilty('.$x.');">';
			for ($i = $vyh_min; $i<=$vyh_max ; $i = $i +0.01)
				$liabilityCombo .= '<option value="'.$i.'">'.$i.'</option>';
				
			$liabilityCombo .= '</select>';
	*/
				

			$js_script .= 'valid_from[valid_from.length] = '.$x.';';
			$this->jsLoadLiability .= 'fillInLiability('.$x.', complementColumns);';
			$vrat .='			</table></div><!-- rates-create -->';
			if ($typeIsGroup) {
				$betParentId = (empty($groupParentId) ? '' : $groupParentId);
				$betParentReadOnly = 'disabled="disabled"';
			}
			else {
				$betParentId = (isset($sazka[$x]['bet_parent_id']) ? Help::Html($sazka[$x]['bet_parent_id']) : '');
				$betParentReadOnly = '';
			} 
			$vrat .= '<input type="hidden" id="vyhernost_ctrl_'.$x.'" value="'.$vyh_min.'" />
						<input type="hidden" id="rateToUpdate_'.$x.'" value="" />
						<input type="hidden" id="colsCount_'.$x.'" value="'.count($ArrayPodTyp['sloupec']).'" />
						<input type="hidden" name="sazka['.$x.'][typ]" value="'.$typeId.'" />
						<input type="hidden" name="sazka['.$x.'][podtyp]" value="'.$subTypeId.'" />';
			if ($typeIsGroup)
				$vrat .= '<input type="hidden" name="sazka['.$x.'][real_typ]" value="'.($typeIsGroup ? $realTypeId : 0).'" />';
			$vrat .= '
					<table class="table-detail" style="margin: 0;">
					<tr>
						<td>Výhernost:</td>
						<td>'.$vyh_min.' - '.$vyh_max.' <input type="text" disabled="disabled" id="vyhernost_'.$x.'"  value="" style="width:40px;" />
						</td>
						<td>'.I18n::tr('Bet Parent Id').'</td>
						<td>
							<input
								type="text"
								style="width:70px"
								id="sazka['.$x.'][bet_parent_id]" name="sazka['.$x.'][bet_parent_id]"
								name=""
								value="'.$betParentId.'" '.$betParentReadOnly.'
							/>
						</td>
						<td>Kombinace další:</td>
					</tr>
					<tr>
						<td>Min - Max kurz:</td>
						<td>'.$min.' - '.$max.'</td>
						<td >AKO:</td>
						<td>
							<input type="text" id="sazka['.$x.'][ako]"  name="sazka['.$x.'][ako]" value="'.(isset($sazka[$x]['ako'])?Help::Html($sazka[$x]['ako']):0).'" class="sinput2"  />
						</td>
						<td rowspan="2">
							<textarea type="text" style="width:150px" name="sazka['.$x.'][kombinace_jine]" id="sazka['.$x.'][kombinace_jine]">'.(isset($sazka[$x]['kombinace_jine'])?Help::Html($sazka[$x]['kombinace_jine']):"").'</textarea>
							<!--<a href="javascript:openWin(\'ciselnik.php\',\'sazka['.$x.'][kombinace_jine]\',\'kombinace\',600,600,\'&sazka='.$x.'&'.($this->multisazka?'multiple=1&':'').'udalost='.$udalost_id.'\');void(0);">
								<img src="_clip/comb.gif" alt="Vyber sázky do kombinace" class="img" />
							</a>-->
							'//.($typeIsGroup ? '' : UiUtil::printCatalogIcon('COMB','sazka['.$x.'][kombinace_jine]','',(isset($_POST['betCount']) ? $_POST['betCount'] : 1),$_POST['udalost'])).'
							.UiUtil::printCatalogIcon('COMB','sazka['.$x.'][kombinace_jine]','',(isset($_POST['betCount']) ? $_POST['betCount'] : 1),$_POST['udalost']).'
						</td>
					</tr>
					<tr>
						<td colspan="2">
						</td>
						
						<td>Risk limit:</td>
						<td><input type="text" id="sazka['.$x.'][risk]"  name="sazka['.$x.'][risk]" value="'.(isset($sazka[$x]['risk'])?Help::Html($sazka[$x]['risk']):$risk).'" class="sinput2"  /></td>
					
					</tr>
					<tr>
						<td colspan="5">
						Kombinace:
							<input type="checkbox" name="sazka['.$x.'][kombinace]" '.(isset($sazka[$x]['kombinace'])?"checked=\"checked\"":"").' class="no"  />
							Samostatně:
							<input type="checkbox" name="sazka['.$x.'][jednoducha]" '.(isset($sazka[$x]['jednoducha'])?"checked=\"checked\"":"").' class="no"  />
							<!--Indv. kombinace:
							<input type="checkbox" name="sazka['.$x.'][kombinace_indv]" '.(isset($sazka[$x]['kombinace_indv'])?"checked=\"checked\"":"").' class="no"  />-->
							Zobrazit kombinaci:
							<input type="checkbox" name="sazka['.$x.'][kombinace_show]" '.(isset($sazka[$x]['kombinace_show']) || !isset($_POST['create'])?"checked=\"checked\"":"").' class="no"  />
						</td>
					</tr>
				</table>
					</td>
				</tr>
				<tr>
					<td valign="top" rowspan="6" style="vertical-align:top">

				</td>
				</tr>
				</table>';

/*
				var_dump($sazka);
*/
	} //count subtypes > 0 

	if(isset($ArrayPodTyp['radek'])) $vrat .='<input type="hidden" name="sazka['.$x.'][radek]" value="'.$ArrayPodTyp['radek'].'" />';

	}

/*
	if(!$this->multisazka) {
		$vrat .= '<div class="actions">
					<input type="submit"
						onclick="this.form.action=this.form.action+\'#sazka\'+'.($pocet_sazek+1).'"
						name="plus"
						title="Přidat sázku do kombinace"
						class="sinput3"
						value="+"
					/>
					&nbsp;&nbsp;';
			if ($pocet_sazek > 1) {
				$vrat .= '<input type="submit"
							title="Odebrat sázku do kombinace"
							onclick="this.form.action=this.form.action+\'#sazka\'+'.($pocet_sazek-1).'"
							class="sinput3"
							name="minus"
							value="-"
						/>';
			}
			
		} else {
*/
			$vrat .= '<br /><br />
				<input
					type="submit"
					onclick="this.form.action=this.form.action+\'&multi=1#sazka\'+'.($this->betCount+1).'"
					name="plus"
					title="Přidat sázku do kombinace"
					class="sinput3"
					value="+"
				/>
				&nbsp;&nbsp;
				<input type="submit"
					title="Odebrat sázku do kombinace"
					onclick="this.form.action=this.form.action+\'&multi=1#sazka\'+'.($this->betCount-1).'"
					class="sinput3"
					name="minus"
					value="-"
				/>';
/*
		}
*/

		if($submit) $vrat .= '<div class="actions"><input type="submit" name="create" value="Vytvořit" /></div>';
	} // !$typeIsGroup || !empty($groupParentId)
		$vrat .='</div>';
	}

  $vrat .= '<script>
      var valid_from = new Array();
      '.$js_script.'
     function SetDatum(){

     var valid_f = document.getElementById("valid_from").value;
     var valid_t = document.getElementById("valid_to").value;

     for(var xx=0;xx<valid_from.length;xx++){

      var ob_valid_from = document.getElementById("sazka["+valid_from[xx]+"][platna_od]");
      var ob_valid_to = document.getElementById("sazka["+valid_from[xx]+"][platna_do]");

		if (valid_f != \'\')
			ob_valid_from.value = valid_f;
		if (valid_t != \'\')
			ob_valid_to.value = valid_t;

     }
    }
	function massSet(elmSrc){
		var elmVal = document.getElementById(elmSrc).value;
		for(var i= 0; i < valid_from.length; i++) {
			//alert("sazka["+valid_from[i]+"]["+elmSrc+"]");
			var elmToSet = document.getElementById("sazka["+valid_from[i]+"]["+elmSrc+"]");
			elmToSet.value = elmVal;
		}
	}

	function massCopyFirstBet() {
		var fields = [\'text\',\'ticket_text\',\'platna_od\', \'platna_do\',\'bet_parent_id\',\'kombinace_jine\',\'text_note\'];
		//alert($("#sazka\\[1\\]\\[text\\]").val());
		alert(document.getElementById("sazka[1][text]").value);
		var origVal = 0;
		for(var i= 2; i <= valid_from.length; i++) {
			
			for (j in fields) {
				//alert($("#sazka\\[1\\]\\[\'text\'\\]").val());
				origVal = document.getElementById("sazka[1]["+fields[j]+"]").value;
				
				

				 document.getElementById("sazka["+i+"]["+fields[j]+"]").value = origVal;
				
			}
		}
	}
  </script>';

   return $vrat;

 }

//START - comented out by Martin 2.8.2012 - this method should not be needed
 /** * vyber dat z databaze
 * @return object
 */
//  public function selectData($where=""){
//
//     $sql = "SELECT a.typ_id, b.sport_id, a.nazev, a.zobrazeno, b.vychozi, b.poradi FROM `typ` a left join typ_sport b  on a.typ_id=b.typ_id ".$where." order by b.typ_id,b.sport_id";
//     $res =& $this->dbGame->query($sql);
//     if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");
//
//	 return $res;
//
//  }
//END - comented out by Martin 2.8.2012 - this method should not be needed

  /**
 * Nastaveni prav k sekci
 * @param int $update pravo zapisu
 * @param int $delete pravo smazani
 * @return void
 */
  public function setPrivileges($update,$delete){



  }

 /**
 * Vraci vystup do tridy main
 * @return string
 */
  public function getContent(){

    return $this->vrat;

  }


  public function __destruct(){




  }
	public function getBetSubTypes($type, $sportId) {
		$podtyp = '';
		$defaultSubTypeId = 0;
		$ret = array();
		
		$sql = "
			SELECT a.podtyp_id, a.interni_nazev, a.radek_sloupec, a.sloupec_pocet_max
			FROM podtyp a
			INNER JOIN typ_podtyp b
				ON a.podtyp_id=b.podtyp_id
			WHERE b.typ_id=".intval($type)."
				AND b.sport_id=".intval($sportId)."
			ORDER BY (a.interni_nazev)
		";
		$res =& $this->dbGame->query($sql);
		if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber podtypu',"admin_ex_db");

		$subTypeCount = $res->numRows();
		
		if ($subTypeCount==0) 
			$podtyp = UiUtil::printErrors('Pro tento druh není definován žádný podtyp');
		else {
			while ($row =& $res->fetchRow()){

				if($subTypeCount  == 1) {
					$defaultSubTypeId = $row['podtyp_id'];
					$podtyp = Help::Html($row['interni_nazev']).($row['radek_sloupec'] ? "(vertikál)" : "(horizontál)").'<input type="hidden" name="sazka-podtyp" id="sazka-podtyp" value="'.$row['podtyp_id'].'" />';
					break;
				}

				$ret[] = $row;
				$podtyp .= '
					<option
						value="'.$row['podtyp_id'].'"
						'.($_POST['sazka-podtyp'] == $row['podtyp_id'] ? 'selected="selected"' : '').'
						'.($res->numRows() == 1 ? 'selected="selected"' : '').'
					>
						'.Help::Html($row['interni_nazev']).'
						'.($row['radek_sloupec'] ? "(vertikál)" : "(horizontál)").'
					</option>
				';
			}
			if($subTypeCount  > 1)
				$podtyp = '<select name="sazka-podtyp" onchange="this.form.action=this.form.action;this.form.submit();"><option value="">'.i18n::tr('bet_subtype').'</option>'.$podtyp.'</select>';
		}

		return array('count' => $subTypeCount, 'element' => $podtyp, 'default' => $defaultSubTypeId, 'array' => $ret);
	}

	public function getBetSettings($eventId, $typeId, $subTypeId) {
		$sql = "
			SELECT kurz_min, kurz_max, vyhernost_min, vyhernost_max, risk_limit
			FROM bet_settings
			WHERE udalost_id=".intval($eventId)."
				AND typ_id=".intval($typeId)."
				AND podtyp_id=".intval($subTypeId);

		$res =& $this->dbGame->query($sql);
		
		if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber bet_settings',"admin_ex_db");

		$row = &$res->fetchRow();

		return array(
			'kurz_min' => $row['kurz_min'],
			'kurz_max' => $row['kurz_max'],
			'vyhernost_min' => $row['vyhernost_min'],
			'vyhernost_max' => $row['vyhernost_max'],
			'risk_limit' => $row['risk_limit']
		);
	}

	public function cleanUp() {
		if (!empty($this->addedBetIds)) {
			foreach ($this->addedBetIds as $betId)
				It6_GlobalCache_Invalidator::invalidateSportsbookByBet($betId);
			It6_GlobalCache_Invalidator::invalidateSportMenuFrame();
		}
	}
}