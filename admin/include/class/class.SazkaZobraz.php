<?php

/**
 * @package    book
 */


/**
 * Trida pro zobrazeni zadanych sazek
 *
 * <code>
 *
 * </code>
 *
 * @package    Ciselniky
 */

class SazkaZobraz extends Template{
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

private $dbAdmin;

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
 * zadavani multisazky
 * @access private
 * @var bool
 */

private  $filtrURL = '';

/**
 * Javascript na vyhernost
 * @access private
 * @var string
 */

public  $jsscript = 'var vyhernost = new Object();';

/**
 * Stránkovač
 * @var Lister
 */

public $lister;

/**
 * User feedback
 * @var array
 */

public $messages = array();

/**
 * User feedback
 * @var array
 */

public $errors = array();

public $betRatesToShowInDetail = array(126,107);

public $betTypeNames = array();

public $updatedBets = array(); // key is bet ID, value not used

/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param int $section id aktualni sekce
* @param PEAR::DB $dbGame objekt spojeni s databazi
*/
  public function __construct($section=0){

	$this->ws = Zend_Registry::get('ws');
    $this->section =  $section;

    $this->dbGame = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
	$this->db = Zend_Registry::get('db');
	//$this->dbGame  = $this->db;
    if (DB::isError($this->dbGame)) {
      throw new ExHandler($this->dbGame->getMessage(),"admin_ex_db");
    }
    $this->dbGame->setFetchMode(DB_FETCHMODE_ASSOC);
    $sql = "set names 'utf8'";
	$res = $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");

	$this->dbAdmin = Zend_Registry::get('zdb_admin');

	if($this->section == "b12") $this->multisazka = true;
	register_shutdown_function(array($this, 'cleanUp'));
  }

public function printFeedback($okCount = false, $errCount = false) {
	if (false == $okCount)
		$okCount = $this->processedBetsOk;
	if (false == $errCount)
		$errCount =$this->processedBetsError;
	
	$feedback = $this->vrat;
	$summary = UiUtil::printWarnings(i18n::tr('** TOTAL: {0} success / {1} errors.',$okCount, $errCount). UiUtil::wrapFeedback($feedback));

	$this->vrat = $summary ;
}

/**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */

public function runAction($nomenu=false){
	if (!empty($_POST['ajax'])) {
	// AJAX for event filter
		if ('sportEvents' == $_POST['ajax']) {
			echo It6_SportEventsFilter::loadEventsBySport(
				$_POST['sportId'],
				$_POST['addAllEventsOpt'],
				empty($_POST['eventValueCol']) ? null :  $_POST['eventValueCol'],
				empty($_POST['eventCols']) ? null : $_POST['eventCols']
			);
		}
		else if ('types' == $_POST['ajax']) {
			parse_str($_POST['form'], $form);
			$form['typ'] = isset($_POST['selected']) ? array_fill_keys($_POST['selected'], 'On') : array();
			$form['filtr'] = 1;
			$_sports = It6_SportEventsFilter::getSportEvents(empty($form['sport_opt']) ? null : $form['sport_opt']);
			
			$filterEvents = array();
			$filterEventIds = (!empty($form['udalost']) && is_array($form['udalost']) ? $form['udalost'] : array());
			$filterEventIds = array_filter($filterEventIds, function($item) { return 0 != $item; });
			foreach ($_sports as $s) {
				foreach ($s[2] as $r) {
					foreach ($r[2] as $e) {
						if (empty($filterEventIds) || in_array($e['eventId'], $filterEventIds)) {
							$filterEvents[$e['eventId']] = array(
									'eventId' => $e['eventId'],
									'sportId' => $s[0],
									'name' => $e['name'],
							);
						}
					}
				}
			}
			unset($_sports);
			
			$filter_where = $this->processFilterInput($form, $form);
			$where = $filter_where['where'];
			$where2 = $filter_where['where2'];
			
			$types_res = $this->loadTypes($filterEvents, $form, $form);
			$types = $types_res['types'];
			
			$typ = $this->getTypeFilterHTML($types, $where, $form, $form);
			
			echo $typ; 
		}
		exit;
	}

	$this->processedBetsOk = 0;
	$this->processedBetsError = 0;

	#Stanoveni vysledku#
	if(isset($_POST['vysledek']) && is_array($_POST['vysledek'])) {
		$this->SetResult();
	}
	#Stanoveni vice vysledku#
	if(isset($_POST['result_set'])) {
		$this->SetMultiResult();
	}
	#Stanoveni vice vysledku podle score#
	if(isset($_POST['result_aut_set'])){
		$this->SetMultiResultScore();
	}
	#Stanoveni vice vysledku podle score#
	if(isset($_POST['result_set_all'])) {
		$this->SetMultiResult();
		$this->SetMultiResultScore();
		$this->updateAllBet();
	}
	#Zrušení výsledku návrat do nevyhodnocená#
	if(isset($_POST['sazka']['clear_rate'])) {
		$this->ClearRate(intval(key($_POST['sazka']['clear_rate'])));
	}
	#Vraceni uz vyplacene  sazky#
	if(isset($_POST['sazka']['vratit'])) {
		$this->ReturnBet(intval(key($_POST['sazka']['vratit'])));
	}
	#Zruseni sazky#
	if(isset($_REQUEST['sazka']['zrusit'])) {
		$this->DeleteBet(intval(key($_REQUEST['sazka']['zrusit'])));
	}
	#Zruseni vice sazek najednou#
	if(isset($_POST['zrusit_all']) && isset($_POST['sazka']['all'])) {
		foreach($_POST['sazka']['all'] as $k=>$h) {
			$this->DeleteBet(intval($k));
		}

		$this->printFeedback();

	}
	#Obnoveni zrusene sazky#
	if(isset($_REQUEST['sazka']['obnovit'])) {
		$this->RetriveBet(intval(key($_REQUEST['sazka']['obnovit'])));
	}
	#Obnoveni vice zrusenych sazek najednou#
	if(isset($_POST['obnovit_all']) && isset($_POST['sazka']['all'])) {
		foreach($_POST['sazka']['all'] as $k=>$h) {
			$this->RetriveBet(intval($k));
		}
		$this->printFeedback();
	}
	#Ukonceni sazky#
	if(isset($_POST['sazka']['ukoncit'])) {
		$this->StopBet(intval(key($_POST['sazka']['ukoncit'])));
	}
	#Ukonceni vice sazek najednou#
	if(isset($_POST['ukoncit_all']) && isset($_POST['sazka']['all'])) {

		foreach($_POST['sazka']['all'] as $k=>$h) {
			$this->StopBet(intval($k));
		}
		$this->printFeedback();
	}
	#Povoleni ukoncene sazky#
	if(isset($_POST['sazka']['povolit'])) {
		$this->RunBet(intval(key($_POST['sazka']['povolit'])));
	}
	#Povoleni vice ukoncenych sazek najednou#
	if(isset($_POST['povolit_all']) && isset($_POST['sazka']['all'])) {

		foreach($_POST['sazka']['all'] as $k=>$h) {
			$this->RunBet(intval($k));
		}
	$this->printFeedback();

	}
	#Overeni sazky#
	if(isset($_POST['sazka']['overit'])) {
		$this->ProveBet(intval(key($_POST['sazka']['overit'])));
	}
	#Overeni vice sazek najednou#
	if(isset($_POST['overit_all']) && isset($_POST['sazka']['all'])) {
		foreach($_POST['sazka']['all'] as $k=>$h) {
			$this->ProveBet(intval($k));
		}
	}
	#Zruseni overeni sazky#
	if(isset($_POST['sazka']['neoverit'])) {
		$this->UnProveBet(intval(key($_POST['sazka']['neoverit'])));
	}
	#Zruseni overeni vice sazek najednou#
	if(isset($_POST['neoverit_all']) && isset($_POST['sazka']['all'])) {
		foreach($_POST['sazka']['all'] as $k=>$h) {
			$this->UnProveBet(intval($k));
		}
	}
	#Zruseni overeni vice sazek najednou#
	if(isset($_POST['update_all_view']) || isset($_POST['result_set'])) {
		$this->updateAllBet();
	}
	#Editace sazky/vytvoreni noveho kurzu#
	if( (isset($_POST['sazka']['zmenit'])) || (isset($_POST['sazka']['update'])) ) {
		if (isset($_POST['sazka']['zmenit']))
			$bId = key($_POST['sazka']['zmenit']);
		else
			$bId = key($_POST['sazka']['update']);

		$this->ChangeBet(intval($bId));
	}
	if(isset($_POST['update_all']) && isset($_POST['uall']) && is_array($_POST['uall'])) {
		foreach($_POST['uall'] as $h) {
			$this->ChangeBet(intval($h));
		}
	}
	//IT6: Zapnuti/vypnuti autoaktualizace z Betradaru
	if(isset($_REQUEST['sazka']['betradar_autoupdate'])) {
		$this->setBetradarAutoupdate(intval(key($_REQUEST['sazka']['betradar_autoupdate'])));
	}
	if(isset($_POST['betradar_autoupdate_all'])) {
			foreach($_POST['sazka']['all'] as $k=>$h) {
				$this->setBetradarAutoupdate(intval($k));
			}
			$this->printFeedback();
	}

	if(isset($_POST['mult_odd'])) {

		foreach($_POST['sazka']['mult_odd_count_orig'] as $betId => $oCount) {
			$colId = $_POST['sazka']['mult_odd_col_id'][$betId];
			$rCount = $_POST['sazka']['mult_odd_count_result'][$betId];
			if (
				isset($colId) && is_numeric($colId)
				&& isset($oCount) && is_numeric($oCount)
				&& isset($rCount) && is_numeric($rCount)
			) {
				$this->multiplicateOddByCounts($betId, $colId, $oCount, $rCount);
			}
		}
		$this->printFeedback();
	}
	if(!$nomenu) $this->ShowBet();
}

/**
 * Used for bets with multiple competitors finishing on same position
 * @param integer $betId
 * @param integer $columnId
 * @param integer $countOrig How many competitor originaly were subject of bet
 * @param integer $countResult How many competitor resulted as subject of bet (> $countOrig)
 */
private function multiplicateOddByCounts($betId, $columnId, $countOrig, $countResult) {
	$countOrig = intval($countOrig);
	$countResult = intval($countResult);
	if ($countOrig < 1 || $countResult < 1) {
		$this->errors[] = i18n::tr('Chybná hodnota pro přenásobení kurzu (musí být větší nebo rovno 1)');
		return;
	}
	if ($countOrig >= $countResult) {
		$this->errors[] = i18n::tr('Chybné hodnoty pro přenásobení kurzu (původní počet musí být menší než výsledný)');
		return;
	}
	$oddConst = $countOrig / $countResult;
	$this->multiplicateOddByConstant($betId, $columnId, $oddConst);
}

/**
 * Multiplicates bet's rate (or part over 1.0) by given koeficient
 * @param integer $betId
 * @param integer $columnId
 * @param float $oddConst
 * @param boolean $partOverOne TRUE if only rate part over 1.0 should be multiplied, multiplicates rate itself otherwise
 */
private function multiplicateOddByConstant($betId, $columnId, $oddConst, $partOverOne = false) {
	$betId = intval($betId);
	$columnId = intval($columnId);
	$oddConst = floatval($oddConst);

	It6_Log::info('Bet rate multiplicated by constant', It6_LOg::TAG_BOOKMAKER_OPERATION, array(
		'adminId' => Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_ADMIN),
		'betId' => $betId,
		'columnId' => $columnId,
		'constant' => $oddConst,
		'partOverOne' => $partOverOne,
	));

	$sql = ($partOverOne ? 'kurz=(kurz-1)*'.$oddConst.'+1' : 'kurz=kurz*'.$oddConst);
	$sql = "UPDATE sazka_kurz SET $sql, kurz_zmena=1 WHERE sazka_id=$betId AND sloupec_id=$columnId";
	$resBetRate = $this->db->query($sql);
	$this->messages[] = i18n::tr('Bet {0} column {1} rate updated', $betId, $columnId);

	$affectedTickets = array();

	$sql = "SELECT * FROM ticket_kurz WHERE sazka_id=$betId AND sloupec_id=$columnId";

	$res = $this->db->query($sql);
	$ticketRateToUpdate = $res->fetchAll();

	if (count($ticketRateToUpdate) > 0) {

		$sql = ($partOverOne ? "rate=(rate-1)*$oddConst+1" : "rate=rate*$oddConst");
		$sql = "UPDATE ticket_kurz SET $sql WHERE sazka_id=$betId AND sloupec_id=$columnId";

		$resTicketRate = $this->db->query($sql);

		foreach ($ticketRateToUpdate as $t)
			$affectedTickets[] = $t['ticket_id'];
			
		if ($resTicketRate && $resBetRate) {
			$this->messages[] = i18n::tr('Bet {0}, column {3}, all rates  were multiplied by {1}, affected tickets: {2}',$betId, $oddConst, implode(',',$affectedTickets),$columnId);
			
			$tips = array($betId, $columnId);

			$updatedTickets = Zend_Registry::get('ws')->Ticket->recalculateByTip($tips);
			$updatedTickets = It6_ArrayWrapper::toNativeArray($updatedTickets);

			if (count($updatedTickets['recalculated']) > 0)
				$this->messages[] = i18n::tr('Tickets {0}, updated succesfully.', implode(',',$updatedTickets['recalculated']));
			if (count($updatedTickets['paidOut']) > 0)
				$this->messages[] = i18n::tr('Tickets {0} needs to be canceled.', implode(',',$updatedTickets['paidOut']));
			if (count($updatedTickets['failed']) > 0)
				$this->errors[] = i18n::tr('Tickets {0} failed to be updated.', implode(',',$updatedTickets['failed']));

		} else {
			$this->errors[] = i18n::tr('Error while multiplicating Bet {0}, column {3}, by {1}, tickets: {2}',$betId, $oddConst,  implode(',',$affectedTickets),$columnId);
		}
	} else {
		$this->warnings[] = i18n::tr('No tickets updated');
	}

}
 /**
 * Stanoveni vysledku automaticky
 * @return void
 */

private function SetMultiResultScore() {
	$feedBackEvalAuto = '';
	//vyhodnoceni pridruzenych typu na zaklade skore
	if (isset($_POST['result'])) {
		foreach($_POST['result'] as $sazka_id=>$h) {
			$data = array();

			if(isset($h['ft']) && mb_strlen($h['ft']) > 0 ) $data['FT'] = $h['ft'];

			if(isset($h['ot']) && mb_strlen($h['ot']) > 0) $data['OT'] = $h['ot'];

			if(isset($h['ht']) && mb_strlen($h['ht']) > 0) $data['HT'] = $h['ht'];

			if(isset($h['1t']) && mb_strlen($h['1t']) > 0) $data['1P'] = $h['1t'];

			if(isset($h['2t']) && mb_strlen($h['2t']) > 0) $data['2P'] = $h['2t'];

			if(isset($h['3t']) && mb_strlen($h['3t']) > 0) $data['3P'] = $h['3t'];

			XmlImportServerResult::SetArray($data);

			$ob = XmlImportServerResult::SetResult(NULL,$sazka_id,$this->dbGame,true);
		
			if ($ob->processedBetsOk == 0) {
				//$this->vrat .= UiUtil::printWarnings(i18n::tr('no_bets_to_set_result'));
			}
			else {
				$feedback = UiUtil::printMessages($ob->getNotices()).UiUtil::printErrors($ob->getErrors());

				$this->vrat .= $feedback;
				$this->printFeedback($ob->processedBetsOk, $ob->processedBetsError);

				$feedBackEvalAuto = $this->vrat;
				$this->vrat = '';
			}
		}
	}

	$this->processedBetsOk = 0;
	$this->processedBetsError = 0;
	foreach($_POST['resultColumns'] as $betId => $resultColumns) {
		if (!empty($resultColumns)) {
			if ($this->isBetUnevaluated($betId)) {
				$columns = array();
				foreach (explode(';', $resultColumns) as $column) {
					$column = intval($column);
					if (!empty($column))
						$columns[$column] = $column;
				}
				$columns = implode(';', $columns);
				$sql = "UPDATE sazky SET status=3,vysledek='$columns' WHERE sazka_id=".intval($betId);
				$res = $this->dbGame->query($sql);
				DbUtil::testResult($res,'Nepodarilo se provest dotaz: vlozeni vysledku sazky');
				$this->vrat .=  UiUtil::printMessages(I18n::tr('The result of the bet {0} was set - columns {1}.', $betId, $columns));
				if (0 < $this->dbGame->affectedRows()) {
					It6_Models_BetChangelog::saveBetChangeOfStatus($betId, 0, 0, 0);
				}
				$this->processedBetsOk++;
				$this->updatedBets[$betId] = true;
				It6_Log::info(
					"Bet #'%bet%' result was set: '%sloupec%'",
					It6_Log::TAG_BOOKMAKER_OPERATION,
					array(
						'bet'		=> $betId,
						'sloupec'	=> $columns
					));
			} else {
				$this->processedBetsError++;
				$this->vrat .= UiUtil::printErrors(i18n::tr('bet_must_be_unevaluated {1}',$betId));
			}
		}
	}
	if ($this->processedBetsOk > 0) {

		$this->printFeedback();
	}
	$this->vrat = $feedBackEvalAuto . $this->vrat;
}


public function isBetMoreThan3Rates($bet) {
	if (in_array($bet['podtyp_id'],$this->betRatesToShowInDetail)
		|| ($bet['typ']=='presny_vysledek') || $bet['typ_id'] == 23)
		return true;
	else return false;
}
public function isStatusActiveOrSuspended($stav) {
	return ($stav['name'] == 'Aktuální') || ($stav['name'] == 'Pozastavena');
}

/**
 * Hromadna editace ve view
 * @return void
 */
private function updateAllBet(){
	if(isset($_POST['sazka_view']['overit']) && is_array($_POST['sazka_view']['overit']) ) {
		foreach($_POST['sazka_view']['overit'] as $k=>$h){
			 $this->ProveBet(intval($k));
		}
	}

	if(isset($_POST['sazka_view']['zrusit']) && is_array($_POST['sazka_view']['zrusit']) ) {
		foreach($_POST['sazka_view']['zrusit'] as $k=>$h) {
			$this->DeleteBet(intval($k));
		}
	}

	if(isset($_POST['sazka_view']['obnovit']) && is_array($_POST['sazka_view']['obnovit']) ) {
		foreach($_POST['sazka_view']['obnovit'] as $k=>$h) {
			$this->RetriveBet(intval($k));
		}
	}
}

/**
 * Editace sazky/vytvoreni noveho kurzu
 * @param int $sazka_id id sazky kterou chceme zmenit
 * @return void
 */
private function ChangeBet($sazka_id){
	$status = true;

	$sql = "
		SELECT platna_od,platna_do,status,jednoducha,bookmaker_id,podtyp_id,typ_id,udalost_id,overena,betradar_autoupdate
		FROM sazky
		WHERE sazka_id=".$sazka_id;
	$res = $this->db->query($sql);

	if ($row = $res->fetch()) {
		$podtyp = $row['podtyp_id'];
		$typ = $row['typ_id'];
		$udalost = $row['udalost_id'];
	}
	else
		throw new Exception('Sazka nenalezena: ' . $sazka_id);

	if($_SESSION['superbookmaker'] != 1 && $_SESSION['bookmaker'] != $row['bookmaker_id']) {
		$this->errors[] = I18n::tr('Nemáte právo měnit tuto sázku');
		$status = false;
	}
	if (!(isset($_POST['sazka'][$sazka_id]['platna_od']) && It6_Date::checkFormatDB(It6_Date::toDb($_POST['sazka'][$sazka_id]['platna_od'])))) {
		$this->errors[] = I18n::tr('Sazka #{0}: Platné od nemá správný formát RRRR-mm-dd HH:mm:ss',$sazka_id);
		$status = false;
	}
    if(!isset($_POST['sazka'][$sazka_id]['platna_do']) || !It6_Date::checkFormatDB(It6_Date::toDb($_POST['sazka'][$sazka_id]['platna_do']))) {
		$this->errors[] = I18n::tr('Sazka #{0}: Platné do nemá správný formát RRRR-mm-dd HH:mm:ss',$sazka_id);
		$status = false;
	}
    if(!isset($_POST['sazka'][$sazka_id]['risk_limit']) || !is_numeric($_POST['sazka'][$sazka_id]['risk_limit'])) {
		$this->errors[] = I18n::tr('Sazka #{0}: Risk limit musí být celé číslo',$sazka_id);
		$status = false;
	}
	if(!isset($_POST['sazka'][$sazka_id]['ako']) || !is_numeric($_POST['sazka'][$sazka_id]['ako'])) {
		$this->errors[] = I18n::tr('Sazka #{0}: AKO musí být celé číslo',$sazka_id);
		$status = false;
	}
	if(!isset($_POST['sazka'][$sazka_id]['text']) || mb_strlen($_POST['sazka'][$sazka_id]['text']) < 1) {
		$this->errors[] = I18n::tr('Sazka #{0}: Text musí mít minimálnì  1 znak',$sazka_id);
		$status = false;
	}

	if(It6_Date::fromDbAsTimestamp($row['platna_od']) != It6_Date::toTimestamp($_POST['sazka'][$sazka_id]['platna_od']) && $row['status'] == 3) {
		$this->errors[] = I18n::tr('Sazka #{0}: Platnost od nelze změnit',$sazka_id);
		$status = false;
	}
	if(It6_Date::fromDbAsTimestamp($row['platna_do']) != It6_Date::toTimestamp($_POST['sazka'][$sazka_id]['platna_do']) && $row['status'] == 3) {
		$this->errors[] = I18n::tr('Sazka #{0}: Platnost do nelze změnit',$sazka_id);
		$status = false;
	}
	if((It6_Date::toTimestamp($_POST['sazka'][$sazka_id]['platna_od'])+60)  >= It6_Date::toTimestamp($_POST['sazka'][$sazka_id]['platna_do'])) {
		$this->errors[] = I18n::tr('Sazka #{0}: Platnost:platnost od musí být minimálně o 1 min. menší nežli platnost do',$sazka_id);
		$status = false;
	}
	if(It6_Date::toTimestamp($_POST['sazka'][$sazka_id]['platna_od']) <= time() && ( (isset($_POST['sazka'][$sazka_id]['jednoducha']) && $row['jednoducha'] != 1) || (!isset($_POST['sazka'][$sazka_id]['jednoducha']) && $row['jednoducha'] != 0) )) {
		$this->errors[] = I18n::tr('Sazka #{0}: Status zda je sázka jednoduchá nebo ne lze měnit pouze do platnost od',$sazka_id);
		$status = false;
	}

	if($status) {
		It6_DbTransaction::begin($this->db);
		try {
			$changelog = array();
			if(!isset($_POST['sazka'][$sazka_id]['cas']))
				$_POST['sazka'][$sazka_id]['cas'][] = 0;

			//get previous bet settings
			$prevBetSql = "
				SELECT status,overena,proplacena,alias, platna_od, platna_do, text, ticket_text, text_note, jednoducha, risk_limit, ako, parent_id, real_typ_id
				FROM sazky
				WHERE sazka_id=$sazka_id";

			$prevRes = $this->db->query($prevBetSql);
			$prevBetSettings = $prevRes->fetch();
			$prevValidFromDb = $prevBetSettings['platna_od'];
			$prevValidToDb = $prevBetSettings['platna_do'];
			$prevBetSettings['platna_od'] = It6_Date::fromDb($prevValidFromDb);
			$prevBetSettings['platna_do'] = It6_Date::fromDb($prevValidToDb);
			
			$parentId = $prevBetSettings['parent_id'];
			$parentIdSql = '';
			$realTypeId = $prevBetSettings['real_typ_id'];
			$realTypeIdSql = '';
			if(isset($_POST['sazka'][$sazka_id]['parent_id']) && $_POST['sazka'][$sazka_id]['parent_id'] != $prevBetSettings['parent_id']) {
				$parentId = (empty($_POST['sazka'][$sazka_id]['parent_id']) ? null : intval($_POST['sazka'][$sazka_id]['parent_id']));
				$parentIdSql = "parent_id=" . (empty($parentId) ? 'NULL' : $parentId) . ",";
				if (!empty($prevBetSettings['real_typ_id'])) {
					$freeRealTypeIds = It6_Models_BetType::getParentBetFreeDerivedTypeIds($parentId, $typ);
					if (empty($freeRealTypeIds)) {
						$this->errors[] = I18n::tr('Sazka #{0}: Není volný typ-alias', $sazka_id);
						return;
					}
					sort($freeRealTypeIds);
					$realTypeId = $freeRealTypeIds[0];
					$realTypeIdSql = "real_typ_id=$realTypeId,";
				}
			}

			$validFromDb = It6_Date::toDb($_POST['sazka'][$sazka_id]['platna_od']);
			$validToDb = It6_Date::toDb($_POST['sazka'][$sazka_id]['platna_do']);
			$scoreNote = $_POST['sazka'][$sazka_id]['score_note'];
			$text = $_POST['sazka'][$sazka_id]['text'];
			$ticketText = $_POST['sazka'][$sazka_id]['ticket_text'];
			$textNote = (empty($_POST['sazka'][$sazka_id]['text_note']) ? null : $_POST['sazka'][$sazka_id]['text_note']);
			$textNoteSql = (isset($textNote) ? "'" . Help::Slash($textNote) . "'": 'NULL');
			$simple = (isset($_POST['sazka'][$sazka_id]['jednoducha']) ? 1 : 0);
			$ako = intval($_POST['sazka'][$sazka_id]['ako']);
			$sql = "
				UPDATE sazky
				SET
					platna_od = '".Help::slash($validFromDb)."',
					platna_do = '".Help::slash($validToDb)."',
					live = ".(isset($_POST['sazka'][$sazka_id]['live'])?1:0).",
					jednoducha = $simple,
					text = '".Help::slash($text)."',
					score_note = '".Help::slash($scoreNote)."',
					ticket_text = '" . Help::slash($ticketText) . "',
					$parentIdSql
					$realTypeIdSql
					risk_limit = ".intval($_POST['sazka'][$sazka_id]['risk_limit']).",
					ako = $ako,
					text_note = $textNoteSql
				WHERE
					proplacena=0
					AND sazka_id=".$sazka_id;

			$res = $this->db->query($sql);

			// changelog structures
			$oldBet = array(
				'betId' => $sazka_id,
				'status' => $prevBetSettings['status'],
				'confirmed' => $prevBetSettings['overena'],
				'paidOut' => $prevBetSettings['proplacena'],
				'validFrom' => $prevValidFromDb,
				'validTo' => $prevValidToDb,
				'text' => $prevBetSettings['text'],
				'ticketText' => $prevBetSettings['ticket_text'],
				'textNote' => $prevBetSettings['text_note'],
				'simple' => $prevBetSettings['jednoducha'],
				'ako' => $prevBetSettings['ako'],
				'rates' => array(),
				'correlated' => array(),
			);
			$newBet = array(
				'betId' => $sazka_id,
				'status' => $prevBetSettings['status'],
				'confirmed' => $prevBetSettings['overena'],
				'paidOut' => $prevBetSettings['proplacena'],
				'validFrom' => $validFromDb,
				'validTo' => $validToDb,
				'text' => $text,
				'ticketText' => $ticketText,
				'textNote' => $textNote,
				'simple' => $simple,
				'ako' => $ako,
				'rates' => array(),
				'correlated' => array(),
			);

			$existingBets = array();
			$corrBetsArr = array();
			$corrBetsStr = '';
			$combShow = array();
			if (isset($_POST['sazka'][$sazka_id]['kombinace'])) {
				$prevCorrBetsArr = array();
				$sql = "SELECT sazka1_id, sazka2_id FROM sazka_kombinace WHERE sazka1_id=".$sazka_id." OR sazka2_id=".$sazka_id;
				try {
					$res = $this->db->query($sql);
					while($row = $res->fetch()) {
						if ($row['sazka1_id'] != $sazka_id) {
							$prevCorrBetsArr[$row['sazka1_id']] = $row['sazka1_id'];
						}
						else {
							$prevCorrBetsArr[$row['sazka2_id']] = $row['sazka2_id'];
						}
					}
					$oldBet['correlated'] = array_values($prevCorrBetsArr);
				}
				catch (Exception $e) {
					throw new Exception('Error while reading correlated bets');
				}

				if (!empty($_POST['sazka'][$sazka_id]['kombinace'])) {
					if(substr($_POST['sazka'][$sazka_id]['kombinace'], -1) == ';')
						$corrBetsRaw = substr($_POST['sazka'][$sazka_id]['kombinace'], 0, -1);
					else
						$corrBetsRaw = $_POST['sazka'][$sazka_id]['kombinace'];

					$corrBetsArr = explode(';', $corrBetsRaw);
					$corrBetsArr = array_map(function($i) { return trim($i); }, $corrBetsArr);
					$corrBetsArr = array_filter($corrBetsArr, function($i) use($sazka_id) {
							return !empty($i) && ctype_digit($i) && ($i != $sazka_id); 
					});
					$corrBetsStr = implode(',', $corrBetsArr);
					$sql = "
						SELECT s.sazka_id
						FROM sazky s
						WHERE sazka_id IN ($corrBetsStr)";

					$res = $this->db->query($sql);

					while($row = $res->fetch()) {
						$existingBets[$row['sazka_id']] = $row['sazka_id'];
						$combShow[$row['sazka_id']] = 1;//$rowComb['kombinace_show'];
					}

					$missingBets = array_diff($corrBetsArr, $existingBets);
					if(!empty($missingBets)) {
						//throw new Exception('Correlated bet doesnt exist');
						$missingBetsStr = implode(',', $missingBets);
						$this->errors[] = I18n::tr("Bet #{0}: error creating combination with #{1}, bet does not exists. Skipping....", $sazka_id, $missingBetsStr);
						It6_Log::notice(
							"Error creating combination error between bets #%betId% - #%nonExistingBets%. BetId #%nonExistingBets% does not exist." ,
							It6_Log::TAG_BOOKMAKER_OPERATION,
							array('betId' =>  $sazka_id, 'nonExistingBets' => $missingBetsStr)
						);
					}
					$corrBetsArr = $existingBets;
				}
				$newBet['correlated'] = array_values($existingBets);
				$sql = "DELETE FROM sazka_kombinace WHERE sazka1_id=".$sazka_id." OR sazka2_id=".$sazka_id;
				try {
					$res = $this->db->query($sql);
					It6_Log::notice(
						"Correlated bets to bet #%betId% deleted successfully.",
						It6_Log::TAG_BOOKMAKER_OPERATION,
						array('betId' =>  $sazka_id, 'deletedCorBets' => array_values($existingBets))
					);
				}
				catch (Exception $e) {
					throw new Exception('Error while deleting correlated bets');
					$this->errors[] = I18n::tr('Sazka #{0}: Chyba při vytváření kombinace',$sazka_id);
					It6_Log::warn(
						"Error while deleting correlated bets",
						It6_Log::TAG_BOOKMAKER_OPERATION,
						array('betId' =>  $sazka_id)
					);
				}

				//nastavit implicitni zobrazeni kombinaci
				foreach ($corrBetsArr as $betCombId)
					$combShow[$betCombId] = 0;

				if (isset($_POST['kombinace_single_show'])) {
					foreach ($corrBetsArr as $betCombId) {
						//$combShow[$betCombId] = 0;
						if (isset($_POST['kombinace_single_show'][$sazka_id][$betCombId])) {
								$betShow = $_POST['kombinace_single_show'][$sazka_id][$betCombId];
								$combShow[$betCombId] = ($betShow == 'on') ? 1 : 0;
						}
					}
					It6_Log::notice(
						"Correlated bets visibility changed successfully.",
						It6_Log::TAG_BOOKMAKER_OPERATION,
						array('betId' =>  $sazka_id, 'corBets' => $corrBetsArr, 'showComb' => $combShow)
					);
				}

				$combNoValidTo = array();
				if (isset($_POST['kombinace_single_no_valid_to'][$sazka_id])) {
					foreach ($_POST['kombinace_single_no_valid_to'][$sazka_id] as $betCombId => $value)
						$combNoValidTo[$betCombId] = true;
				}

				if (!empty($_POST['sazka'][$sazka_id]['kombinace'])) {
					It6_DbTransaction::begin();
					try {

						$sql = "INSERT INTO sazka_kombinace (sazka1_id, sazka2_id, kombinace_show, update_platna_do) VALUES(?,?,?,?)";
						$stm = $this->db->prepare($sql);
						foreach($corrBetsArr as $k => $corrBet) {
							$updateValidTo = (empty($combNoValidTo[$corrBet]) ? 1 : 0);
							$stm->execute(array($corrBet, $sazka_id, intval($combShow[$k]), $updateValidTo));
						}

						It6_Log::notice(
							"Correlated bets to bet #%betId% created successfully.",
							It6_Log::TAG_BOOKMAKER_OPERATION,
							array('betId' =>  $sazka_id, 'corBets' => $corrBetsArr, 'showComb' => $combShow, 'dontUpdateValidTo' => $combNoValidTo)
						);
						It6_DbTransaction::commit();
					}
					catch (Exception $e) {
						//throw new Exception('Error while creating correlated bets');
						$this->errors[] = I18n::tr('Sazka #{0}: Chyba při vytváření kombinace',$sazka_id);
						It6_Log::warn(
							"Error while creating correlated bets",
							It6_Log::TAG_BOOKMAKER_OPERATION,
							array('betId' =>  $sazka_id, 'corBets' => $corrBetsArr, 'showComb' => $combShow)
						);
						It6_DbTransaction::rollback();
						$status = false;
					}
				}

				if(isset($_POST['sazka'][$sazka_id]['cas']) && $corrBetsStr!='') {
					$validTo = $_POST['sazka'][$sazka_id]['platna_do'];
					$validToTs = It6_Date::toTimestamp($validTo);
					$validToDb = It6_Date::timestampToDb($validToTs);
					if($typ == 19 || $typ == 22) {
						$corrBetsForValidTo = $corrBetsArr;
						$betExceptions = 'none';
						if (!empty($combNoValidTo)) {
							$betExceptions = array();
							foreach ($combNoValidTo as $betCombId => $flag) {
								$betExceptions[] = intval($betCombId);
								unset($corrBetsForValidTo[$betCombId]);
							}
						}
						
						$updateValidToBets = array();
						if(!empty($corrBetsForValidTo)) {
							$sql = 'SELECT s.sazka_id,u.betradar_time_offset AS timeOffset,t.betradar_time_offset AS timeOffsetCoef'
								. ' FROM sazky s'
								. ' JOIN typ t ON s.typ_id=t.typ_id'
								. ' JOIN udalost u ON s.udalost_id=u.udalost_id'
								. ' WHERE s.proplacena=0 AND s.typ_id NOT IN ('
								. implode(',', It6_Models_BetType::getAllToNotUpdateValidToTimeByParent())
								. ') AND s.sazka_id IN (' . implode(',', $corrBetsForValidTo) . ')';
							$res = $this->db->query($sql);
							while ($row = $res->fetch()) {
								$offset = round(intval($row['timeOffset']) * 60 * floatval($row['timeOffsetCoef']));
								$corrValidToDb = It6_Date::timestampToDb($validToTs + $offset);
								$updateValidToBets[$corrValidToDb][] = $row['sazka_id'];
							}
						}
						
						if (!empty($updateValidToBets)) {
							try {
								foreach ($updateValidToBets as $corrValidToDb => $corrBets) {
									$sql = "UPDATE sazky SET platna_do='$corrValidToDb'"
										. " WHERE sazka_id IN (" . implode(',', $corrBets) . ')'
										. " AND platna_do<>'$corrValidToDb'";
									$res7 = $this->db->query($sql);
									$this->messages[] = I18n::tr(
										'Time bet valid_to was updated to bets {0}. New time {1}',
										$sazka_id .': '. implode(',', $corrBets),
										$corrValidToDb
									);
								}
								It6_Log::info(
									"Time bet valid_to was updated.",
									It6_Log::TAG_BOOKMAKER_OPERATION,
									array(
										'betId' =>  $sazka_id,
										'corBets' => $corrBetsArr,
										'corBetsExcept' => $betExceptions,
										'corBetsUpdated' => $updateValidToBets,
										'newTime' => $validTo,
									)
								);
							}
							catch (Exception $e) {
								$this->errors[] = I18n::tr('Error updating time. Time {0}', $validTo);
								It6_Log::warn(
									"Error while updating bet valid_to.",
									It6_Log::TAG_BOOKMAKER_OPERATION,
									array(
										'betId' =>  $sazka_id,
										'corBets' => $corrBetsArr,
										'corBetsExcept' => $betExceptions,
										'corBetsUpdated' => $updateValidToBets,
										'newTime' => $validTo,
									)
								);
							}
							foreach ($updateValidToBets as $corrValidToDb => $corrBets) {
								foreach ($corrBets as $betId) { 
									It6_Models_BetChangelog::saveBetChangeOfValidity($betId, null, $corrValidToDb);
								}
							}
						}
						else {
							$this->messages[] = I18n::tr('No correlated bet needs valid_to to be updated.');
							It6_Log::info(
								'No correlated bet needs valid_to to be updated.',
								It6_Log::TAG_BOOKMAKER_OPERATION,
								array(
									'betId' =>  $sazka_id,
									'corBets' => $corrBetsArr,
									'corBetsExcept' => $betExceptions,
									'newTime' => $validTo
								)
							);
						}
					}
				}

			}
			else if(
				isset($_POST['sazka'][$sazka_id]['kombinace'])
				&& mb_strlen($_POST['sazka'][$sazka_id]['kombinace']) > 0
				&& isset($_POST['sazka'][$sazka_id]['kombinace_indv'])
			) {
				var_dump('wrong branch? A');
				exit;
				$pole_ch = explode(";",$_POST['sazka'][$sazka_id]['kombinace']);

				foreach($pole_ch as $s_ch_id) {
					if(!is_numeric($s_ch_id) || mb_strlen(trim($s_ch_id)) == 0)
						continue;

					$sql = "
						SELECT sazka1_id,sazka2_id
						FROM sazka_kombinace
						WHERE
							sazka1_id=".intval($sazka_id)."
							OR sazka2_id=".intval($sazka_id);

					$res6 = $this->db->query($sql);
					$sttt = true;

					while ($row = $res6->fetch()) {
						if(
							($row['sazka1_id'] == $sazka_id and $row['sazka2_id'] == intval($s_ch_id))
							|| ($row['sazka2_id'] == $sazka_id and $row['sazka1_id'] == intval($s_ch_id))
							|| intval($s_ch_id) == $sazka_id
						)
							$sttt = false;
					}

					if(intval($s_ch_id) == $sazka_id)
						$sttt = false;

					if ($sttt == true) {
						$sql = "
							REPLACE INTO sazka_kombinace (sazka1_id,sazka2_id,kombinace_show)
							VALUES(".intval($s_ch_id).",".intval($sazka_id).",".$show_kombinace.")";

						try {
							$res7 = $this->db->query($sql);
						}
						catch (Exception $e) {
							$this->errors[] = I18n::tr('Sazka #{0}: Chyba při vytváření kombinace',$sazka_id);
						}
					}
				}
			} else {
				var_dump('wrong branch? B');
				$sql = "
					SELECT sazka1_id,sazka2_id
					FROM sazka_kombinace
					WHERE
						sazka1_id=".$sazka_id."
						OR sazka2_id=".$sazka_id;

				$res6 = $this->db->query($sql);

				while ($row = $res6->fetch()){
					if($row['sazka1_id'] == $sazka_id)
						$comb = $row['sazka2_id'];
					else
						$comb = $row['sazka1_id'];

					if($typ == 19 || $typ == 22) {
						$sql = "
							UPDATE sazky
							SET platna_do='".Help::slash(It6_Date::toDb($_POST['sazka'][$sazka_id]['platna_do']))."'
							WHERE
								proplacena=0
								AND typ_id IN(".implode(",",$_POST['sazka'][$sazka_id]['cas']).")
								AND sazka_id=".$comb;

						$res7 = $this->db->query($sql);
					}
				}
			}

			$this->messages[] = I18n::tr('Sázka #{0} byla úspěšně editována',$sazka_id);

			$this->updatedBets[$sazka_id] = true;

			It6_Log::info(
				"Bet #'%bet%' was updated.",
				It6_Log::TAG_BOOKMAKER_OPERATION,
				array(
					'bet'=> $sazka_id,
					'validFrom'=>$_POST['sazka'][$sazka_id]['platna_od'],
					'validTo'=>$_POST['sazka'][$sazka_id]['platna_do'],
					'text' => $_POST['sazka'][$sazka_id]['text'],
					'ticket_text' => $_POST['sazka'][$sazka_id]['ticket_text'],
					'parent_id' => $_POST['sazka'][$sazka_id]['parent_id'],
					'isSimple' => (isset($_POST['sazka'][$sazka_id]['jednoducha'])?1:0),
					'riskLimit' => $_POST['sazka'][$sazka_id]['risk_limit'],
					'ako' => $_POST['sazka'][$sazka_id]['ako'],
					'prevBetSettings' => $prevBetSettings
					)
			);

			$change = It6_Models_BetChangelog::getBetChange($oldBet, $newBet);
			if (!empty($change)) {
				It6_Models_BetChangelog::saveBetChange($change);
			}
			It6_DbTransaction::commit($this->db);
		} catch(Exception $e) {
			It6_DbTransaction::rollback($this->db);
			It6_Log::err(
				"Error bet edit betId #'%bet%' action : '%action%'.",
				It6_Log::TAG_BOOKMAKER_OPERATION,
				array(
					'bet'		=> $sazka_id,
					'action'	=> ($rateAction[$i] == 'edit'?"updated":"created"),
					'old_rate' => $oldRate[$i],
					'new_rate' => $newRate[$i],
					'column_rate' => $columnId[$i],
					'rateOrder' => $rateOrder[$i]
				)
			);
			throw $e;
		}

		if(isset($_POST['sazka'][$sazka_id]['new_bet']) && !isset($_POST['sazka']['update'])) {
			It6_DbTransaction::begin($this->db);
			try {

			 #editace/vytvoreni kurzu#

				$sql = 'SELECT psc.sloupec_id, psc.complement_id FROM podtyp_sloupec_complement psc'
					. ' JOIN podtyp_sloupce ps ON ps.sloupec_id=psc.sloupec_id AND ps.podtyp_id=' . intval($podtyp);
				$res = $this->dbGame->query($sql);
				DbUtil::testResult($res);
				$complementCols = array();
				while ($row = $res->fetchRow())
					$complementCols[$row['sloupec_id']][] = $row['complement_id'];
				
			   #MAX a MIN kurzu a vyhernosti#

			   $sql = "select kurz_min,kurz_max,vyhernost_min,vyhernost_max from bet_settings where udalost_id=".$udalost." and typ_id=".$typ." and  podtyp_id=".$podtyp;

			   $res22 = $this->db->query($sql);

			   if($row22 = $res22->fetch()) {
					$min_kurz = $row22['kurz_min'];
					$max_kurz = $row22['kurz_max'];
					$min_vyhernost = $row22['vyhernost_min'];
					$max_vyhernost = $row22['vyhernost_max'];
				} else {
					$this->vrat .= "<div class=\"errormsg\"> <strong>".$sazka_id."</strong> -> Nebyl nalezen min. a max kurz pro daný podtyp</div><br />";
					$status = false;
				}

				$min_max_status = true;$vyhernost_num = 0;

				#nove kurzy se daji stanovit az po platnost od#

			   //if($_POST['sazka'][$sazka_id]['new_bet'] == 'new' && (It6_Date::toTimestamp($_POST['sazka'][$sazka_id]['platna_od']) > time() || It6_Date::toTimestamp($_POST['sazka'][$sazka_id]['platna_do']) <= time()))	  {$this->vrat .= "<div class=\"errormsg\"> <strong>#".$sazka_id."</strong> -> Nové kurzy se dají vypisovat až po platnost od a maximálnì do platnost do</div><br />";$status = false;}

				if ($_POST['sazka'][$sazka_id]['new_bet'] == 'new' &&
					(It6_Date::toTimestamp($_POST['sazka'][$sazka_id]['platna_od']) > time())) {
						$this->vrat .= "<div class=\"errormsg\"> <strong>#".$sazka_id."</strong> -> Nové kurzy se dají vypisovat až po platnost od</div><br />";
						$status = false;
				}

				if($row['overena'] != 0) {
					$this->vrat .= "<div class=\"errormsg\"> <strong>#".$sazka_id."</strong> -> Kurzy lze měnit pouze pokud sázka není ověřená </div><br />";
					$status = false;
				}

				if(!$status) {
					It6_DbTransaction::rollback($this->db);
					return;
				}

				if($_POST['sazka'][$sazka_id]['new_bet'] == 'edit') {
					$obj = new SazkaInfo();
					$obj->UpdateLimit($sazka_id,0);
				}
				//$resHelp = $res2;
				$sql = "SELECT *, a.poradi AS maxi
						FROM sazka_kurz a
						WHERE a.sazka_id=".$sazka_id." and a.poradi = (select MAX(b.poradi) FROM sazka_kurz b where b.sazka_id=".$sazka_id." )
						GROUP BY a.sloupec_id,a.poradi
						ORDER BY platny_od desc";

				$res2 = $this->db->query($sql);
				
				$kurzy_prove = 1000000000;
				$kurzy_num = 0;
				$por = 0;
				$sloupce = array();
				$datum_insert = It6_Date::dbNow();

				$columnId = $rateAction = $oldRate = $newRate = $rateOrder = $rates = array();
				$i = 0;

				$rateCols = $_POST['sazka'][$sazka_id]['sloupec'];

				foreach ($rateCols as $colId => $rate) {

					if(!isset($rate))
						throw new ExHandler('Chybí sloupec',"admin_ex_page");

					if (mb_strlen($rate) < 1 || !is_numeric($rate)) {
							$this->vrat .= $this->errors[] = i18n::tr('#{0} kurz má špatný formát',$sazka_id);
							$status = false;
							break;
					}

					if ($rate < $min_kurz || $rate > $max_kurz) {
						$min_max_status = false;
						$status = false;
						break;
					}
					if ($kurzy_prove > $rate) $kurzy_prove = $rate;

					if (empty($complementCols[$colId]))
						$vyhernost_num += (1/$rate);
					else {
						$sum = 0;
						foreach ($complementCols[$colId] as $complColId) {
							$sum += 1 / $rateCols[$complColId];
						}
						$complRate = 1 / $sum;
						if ($complRate < 1.0)
							$complRate = 1.0;
						if (abs($complRate - $rate) >= 0.01) {
							$this->errors[] =  I18n::tr('Sázka {0} -> Dopočítané sloupce se liší (dodatečná kontrola): posláno {1}, kontrola {2}', $k, $rate, $complRate);
							$status = false;
						}
					}
					
					if (false == $status)
						throw new Exception('Rate not valid');
					
				}

				$vyhernost_num = round($vyhernost_num,2);

				if (($vyhernost_num/count($_POST['sazka'][$sazka_id]['sloupec'])) != 1 && !$min_max_status) {
					//$this->dbGame->rollback();
					$this->errors[] = i18n::tr('rate_update_error {0}. Kurzy jsou vyšší nebo nižší nežli povolené; MIN: {1} MAX: {2}',$sazka_id,$min_kurz,$max_kurz);
					$status = false;
					throw new ExHandler('Kurzy jsou vyšší nebo nižší nežli povolené',"admin_ex_db");
				}

				if ($typ != 29 && $udalost!= 1182 &&
					($vyhernost_num/count($_POST['sazka'][$sazka_id]['sloupec'])) != 1 &&
					($vyhernost_num < $min_vyhernost || $vyhernost_num > $max_vyhernost)) {
						//$this->dbGame->rollback();
						$this->errors[] = i18n::tr('rate_update_error {0}. Výhernost je vyšší nebo nižší nežli povolená; Výhernost {3} MIN: {1} MAX: {2}',$sazka_id,$min_vyhernost,$max_vyhernost,$vyhernost_num);
						$status = false;
						throw new ExHandler('Výhernost je vyšší nebo nižší nežli povolená',"admin_ex_db");
				}
				
				if (false == $status)
					throw new Exception('Rate not valid');

				$currentOdds = $res2->fetchAll();

				$oldRates = array();
				foreach ($currentOdds as $r) {
					$oldRates[$r['sloupec_id']] = $r['kurz'];
				}
				$oldBet['rates'] = $oldRates;
				
				$rateDiff = array();
				foreach ($oldRates as $id => $rate) {
					if ($rate != $rateCols[$id])
						$rateDiff[$id] = $rateCols[$id];
				}

				$areRatesUptodate = (count($rateDiff) == 0 ? true : false);
				if ($areRatesUptodate) {
					$this->messages[] = I18n::tr('BetId {0} rates are uptodate.',$sazka_id);
					$status = false;
					$newBet['rates'] = $oldRates;
				}
				else {
					$newBet['rates'] = array();
					foreach ($currentOdds as $row) {
	
						if($por != 0 && $por != $row['poradi']) continue;
	
						$por = $row['poradi'];
						$kurzy_num++;
	
						$colId = $row['sloupec_id'];
						$rate = $_POST['sazka'][$sazka_id]['sloupec'][$row['sloupec_id']];
						
						$sloupce[$row['sloupec_id']] = $rate;
	
						$rateAction[$i] = $_POST['sazka'][$sazka_id]['new_bet'];
						$oldRate[$i] = $row['kurz'];//'N/A';
						$newRate[$i] = 'N/A';
						$rateOrder[$i] = 'N/A';
						//$rateBet[$sazka_id] = $sazka_id
	
						if ($status) {
							if($_POST['sazka'][$sazka_id]['new_bet'] == 'edit') { #Prepsat stavajici#
								$rates[] = $i;
								$sql = "UPDATE sazka_kurz
										SET kurz=".$rate."
										WHERE sloupec_id=".$colId." and poradi=".$row['poradi']." and sazka_id=".$sazka_id;
	
								$columnId[$i] = $row['sloupec_id'];
								$newRate[$i] = $_POST['sazka'][$sazka_id]['sloupec'][$row['sloupec_id']];
								$rateOrder[$i] = $row['poradi'];
	
								$res = $this->db->query($sql);
	
								$this->updatedBets[$sazka_id] = true;
	
							} else if($_POST['sazka'][$sazka_id]['new_bet'] == 'new') { #Vytvorit novou#
								$rates[] = $i;
								$sql = "INSERT INTO sazka_kurz (sazka_id,sloupec_id,poradi,kurz,platny_od)
										VALUES ("
										.$sazka_id.","
										.$colId.","
										.($row['maxi']+1).","
										.$rate.",'"
										.$datum_insert."')";
	
								$columnId[$i] = $row['sloupec_id'];
								$newRate[$i] = $rate;
								$rateOrder[$i] = ($row['maxi']+1);
	
								$res = $this->db->query($sql);
	
								$this->updatedBets[$sazka_id] = true;
	
							}
							$newBet['rates'][$colId] = $rate;
						}
						$i++;
					} //while
				}

				if (It6_Models_BetChangelog::ratesChanged($oldBet['rates'], $newBet['rates'])) {
					It6_Models_BetChangelog::saveBetChangeOfRates($sazka_id, $newBet['rates']);
				}

				if($status) {

					if($typ == 19 && $podtyp == 23) $this->ChangeDblMatch($sazka_id,$sloupce,$datum_insert);

					if($_POST['sazka'][$sazka_id]['new_bet'] == 'edit'){
						$obj->UpdateLimit(intval($sazka_id),1);
						$obj->RunUpdateLimit($sazka_id);
					}

					$invalidate = false;
					foreach ($rates as $i) {
							$this->messages[] = I18n::tr('BetId {0} rates were changed. Action {5} column {1} (order {4}) : {2} => {3}',
								$sazka_id, $columnId[$i], $oldRate[$i], $newRate[$i], $rateOrder[$i], $rateAction[$i]);
							It6_Log::info(
								"The rate for bet #'%bet%' was '%action%'.",
								It6_Log::TAG_BOOKMAKER_OPERATION,
								array(
									'bet'		=> $sazka_id,
									'action'	=> ($rateAction[$i] == 'edit'?"updated":"created"),
									'old_rate' => $oldRate[$i],
									'new_rate' => $newRate[$i],
									'column_id' => $columnId[$i],
									'rateOrder' => $rateOrder[$i]
								)
							);

							$invalidate = true;
					}

					$this->ws->Alert->assert(
						'RateChange',
						array(
							'betId' => $sazka_id,
							'oddsIds' => $columnId,
							'oldRates' => $oldRate,
							'newRates' => $newRate,
							'bookmaker' => $_SESSION['bookmaker']
					));

					if ( $invalidate ) {
						$this->updatedBets[$sazka_id] = true;
					}

				} else {
					//throw new Exception('Status=false'); //,"admin_ex_db");
				}

				It6_DbTransaction::commit($this->db);
			} catch (Exception $e) {
				It6_DbTransaction::rollback($this->db);
				foreach ($rates as $i) {
					$this->errors[] = I18n::tr('Error rate change betId {0} : action {5}, new Rate for column {1} (order {4}) : {2} => {3}',
						$sazka_id,
						$columnId[$i],
						$oldRate[$i],
						$newRate[$i],
						$rateOrder[$i],
						($_POST['sazka'][$sazka_id]['new_bet'] == 'edit'?"updated":"created")
					);
					It6_Log::err(
						"Error rate change betId #'%bet%' action : '%action%'.",
						It6_Log::TAG_BOOKMAKER_OPERATION,
						array(
							'bet'		=> $sazka_id,
							'action'	=> ($rateAction[$i] == 'edit'?"updated":"created"),
							'old_rate' => $oldRate[$i],
							'new_rate' => $newRate[$i],
							'column_rate' => $columnId[$i],
							'rateOrder' => $rateOrder[$i]
						)
					);

				}
				//throw new ExHandler($e.'Nepodarilo se provest dotaz: insert into sazka_kurz',"admin_ex_db");
			} //catch
		}//new_bet
	}//status
}
    /**
 * Aktualizace dvojite sance
 * @param int $sazka_id id sazky
 * @param array $sloupce sloupce a kurzy
 * @param array $datum_insert datum
 * @return void
 */

  private function ChangeDblMatch($sazka_id,$sloupce,$datum_insert){

      $sql = "select * from sazky a where a.typ_id=24 and a.podtyp_id=24 and (a.sazka_id in (select b.sazka1_id from sazka_kombinace b where b.sazka2_id=".intval($sazka_id).") or a.sazka_id in (select d.sazka2_id from sazka_kombinace d where d.sazka1_id=".intval($sazka_id)."))";

      $res = $this->dbGame->query($sql);

	  if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: zjisteni existence dvojite sance',"admin_ex_db");}

	 foreach($sloupce as $k=>$h){

	   	 if($k == 138)         $home = $h;

	   	 else if($k == 139)    $draw = $h;

	   	 else if($k == 140)    $visit = $h;

	  }

	   $vynosnost = ( (1/$home) + (1/$draw) + (1/$visit) );

       $odds = Help::GetOdd(array("1"=>$home,"X"=>$draw,"2"=>$visit),$vynosnost);
       $oddsColumns = array('01' => 141, '12' => 142, '02' => 143);

	  if ($row = $res->fetchRow()){

	  	//$sql = "select poradi from sazka_kurz a where a.sazka_id=".$row['sazka_id']." group by sloupec_id,poradi order by platny_od desc limit 0,1";
	  	$sql = "SELECT sazka_id,sloupec_id,poradi,kurz FROM sazka_kurz_aktualni WHERE sazka_id={$row['sazka_id']}";
	  	
	  	$res2 = $this->dbGame->query($sql);
	  	
	  	if(DB::isError($res2)) {
	  		$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: zjisteni existence dvojite sance',"admin_ex_db");
	  	}
	  	
	  	$dbRates = array();
	  	
	  	while ($row2 = $res2->fetchRow()) {
	  		$dbRates[$row2['sloupec_id']] = array(
	  			'rate' => $row2['kurz'],
	  	  		'order' => $row2['poradi'], 
	  		);
	  	}

	  	$ratesChanged = array();
	  	$order = 0;
	  	$_odds = array();
	  	foreach ($odds as $column => $rate) {
	  		if (isset($oddsColumns[$column])) {
		  		$columnId = $oddsColumns[$column];
		  		$_odds[$columnId] = floatval($rate);
		  		if (!empty($dbRates[$columnId])) {
		  			$dbOrder = $dbRates[$columnId]['order'];
		  			if ($dbOrder > $order) {
		  				$order = $dbOrder;
		  			}
		  			if ($dbRates[$columnId]['rate'] != $rate) {
		  				$ratesChanged[] = $columnId;
		  			}
		  		}
	  		}
	  	}
	  	$odds = $_odds;
	  	unset($_odds);

		if (!empty($ratesChanged)) {
			if($_POST['sazka'][$sazka_id]['new_bet'] == 'edit') {
				foreach ($ratesChanged as $columnId) {
					$rate = floatval($odds[$columnId]);
					$order = $dbRates[$columnId]['order'];
					$sql = "UPDATE sazka_kurz SET kurz=$rate WHERE sloupec_id=$columnId AND sazka_id={$row['sazka_id']} AND poradi=$order";
					$res3 = $this->dbGame->query($sql);
					if (DB::isError($res3)) {
						$this->dbGame->rollback();
						$this->dbGame->autoCommit(true);
						throw new ExHandler('Nepodarilo se provest dotaz: aktualizace kurzu dvojita sance',"admin_ex_db");
					}
				}
			}
			else if($_POST['sazka'][$sazka_id]['new_bet'] == 'new') {
	        	$sql = 'INSERT INTO sazka_kurz (sazka_id,poradi,sloupec_id,kurz,platny_od) VALUES(?,?,?,?,?)';
                $stmt = $this->dbGame->prepare($sql);
	            if (DB::isError($res3)) {
	            	$this->dbGame->rollback();
	            	$this->dbGame->autoCommit(true);
	            	throw new ExHandler('Nepodarilo se provest dotaz: aktualizace kurzu dvojita sance',"admin_ex_db");
	            }
                foreach ($odds as $columnId => $rate) {
                	$this->dbGame->execute($stmt, array(
                		$row['sazka_id'], ($order + 1), $columnId, $rate, $datum_insert
                	));
                	if (DB::isError($res3)) {
                		$this->dbGame->rollback();
                		$this->dbGame->autoCommit(true);
                		throw new ExHandler('Nepodarilo se provest dotaz: aktualizace kurzu dvojita sance',"admin_ex_db");
                	}
                }
	        }
	        $changelogRates = array();
	        foreach ($odds as $columnId => $rate) {
	        	$changelogRates[$columnId] = $rate;
	        }
	        It6_Models_BetChangelog::saveBetChangeOfRates($row['sazka_id'], $changelogRates);
		}
  	}
}

 /**
 * Vraceni sazky zpet do stavu nevyhodnocena a odebrani penez kde to jde
 * @param int $sazka_id id sazky
 * @return void
 */

	private function ReturnBet($sazka_id){
		//$ob = new VratitSazku($this->dbGame);
		$ob = new VratitSazku();
		$ob->ReturnBet($sazka_id);
		$this->vrat = $ob->getContent();
	}

  /**
 * Zruseni overeni sazky
 * @param int $sazka_id id sazky kterou chceme overit zrusit
 * @return void
 */

  private function UnProveBet($sazka_id){

   $status = true;

   $sql = "select sazka_id,status,proplacena,overena from sazky where sazka_id=".$sazka_id;

   $res = $this->dbGame->query($sql);

   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");

   if($row = $res->fetchRow()); else throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");

   #Overit lze pouze vyhodnocenou sazku#

   if($row['status'] == 3 && $row['overena'] != 0 && $row['proplacena'] != 1){

     #test zda se nejedna o superbookmakera#

     if($_SESSION['superbookmaker'] != 1 && $_SESSION['bookmaker'] != $row['overena']) {



	  $this->vrat .= "<div class=\"errormsg\">(#".$sazka_id.") Nemáte právo rušit ovìøení sázky</div><br />";$status = false;

     }

   }else $status = false;

   if($status){

    $sql = "update sazky set overena=0 where sazka_id=".$sazka_id;

    $res = $this->dbGame->query($sql);

    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: zruseni  overeni sazky',"admin_ex_db");
    if (0 < $this->dbGame->affectedRows()) {
    	It6_Models_BetChangelog::saveBetChangeOfStatus($sazka_id, 3, 0, 0);
    }

	$this->updatedBets[$sazka_id] = true;

    $this->vrat .= "<div class=\"okmsg\">Sázka (#".$sazka_id.") ověření bylo zrušeno</div><br />";

		It6_Log::info(
			"The rate for bet #'%bet%' was '%action%'.",
			It6_Log::TAG_BOOKMAKER_OPERATION,
			array(
				'bet'		=> $sazka_id,
				'action'	=> ($_POST['sazka'][$sazka_id]['new_bet'] == 'edit'?"updated":"created")
			)
		);
  }

  }

 /**
 * Overeni sazky
 * @param int $sazka_id id sazky kterou chceme overit
 * @return void
 */

  private function ProveBet($sazka_id){
	$status = true;

	$sql = "select sazka_id,status,proplacena,overena,platna_do from sazky where sazka_id=".$sazka_id;
   
	$res = $this->dbGame->query($sql);
   
	if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");
   
	if($row = $res->fetchRow());
	else throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");

   #Overit lze  vyhodnocenou i nevyhodnocenou sazku#
	if ($row['overena'] == 0 && $row['proplacena'] != 1 && It6_Date::fromDbAsTimestamp($row['platna_do'], false) <= time()) {
		#test zda se nejedna o superbookmakera#
		if($_SESSION['superbookmaker'] != 1) {
			$this->vrat .= "<div class=\"errormsg\"> Nemáte právo ovìøit tuto sázku</div><br />";
			$status = false;
		}
	} else $status = false;
   
   if($status) {
   	$confirmed = intval($_SESSION['bookmaker']);
    $sql = "update sazky set overena=$confirmed,status=3 where sazka_id=".$sazka_id;
    $res = $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: zruseni  sazky',"admin_ex_db");
//    $this->vrat .= "<div class=\"okmsg\">Sázka (#".$sazka_id.") byla ověřena</div><br />";

    if (0 < $this->dbGame->affectedRows()) {
    	It6_Models_BetChangelog::saveBetChangeOfStatus($sazka_id, 3, $confirmed, 0);
    }
    
	$this->updatedBets[$sazka_id] = true;

	$this->messages[] = I18n::tr('The bet {0} was confirmed.',$sazka_id);

	It6_Log::info(
		"Bet #'%bet%' was authorized.",
		It6_Log::TAG_BOOKMAKER_OPERATION,
		array('bet'=> $sazka_id)
	);
   }
  }

private function setBetradarAutoupdate($sazkaId) {
	$bet = $this->ws->Bet->getById($sazkaId);
	$currVal = $bet['betradarAutoupdate'];

	$sql = "UPDATE sazky SET betradar_autoupdate=NOT(betradar_autoupdate) WHERE sazka_id=".$sazkaId;
	$res = $this->dbGame->query($sql);
	if(DB::isError($res)) {
		$this->errors[] = I18n::tr("Error: Bet Id #{0} was not set for autoupdate from Betradar",$sazkaId);
		$this->processedBetsError++;
		It6_Log::err(
			"Bet #'%bet%' : Error by changing betradar autoupdate value.",
			It6_Log::TAG_BOOKMAKER_OPERATION,
			array('bet'=> $sazkaId)
		);
		throw new ExHandler('Nepodarilo se provest dotaz: zruseni  sazky',"admin_ex_db");
	} else
		$this->processedBetsOk++;

	if ($currVal == 0) {
		$this->messages[] = I18n::tr("BetId #{0} : Betradar autoupdate enabled",$sazkaId);
		It6_Log::info(
			"Bet #'%bet%' : Betradar autoupdate enabled.",
			It6_Log::TAG_BOOKMAKER_OPERATION,
			array('bet'=> $sazkaId)
		);
	} else {
		$this->messages[] = I18n::tr("BetId #{0} : Betradar autoupdate disabled.",$sazkaId);
		It6_Log::info(
			"Bet #'%bet%' : Betradar autoupdate disabled.",
			It6_Log::TAG_BOOKMAKER_OPERATION,
			array('bet'=> $sazkaId)
		);
	}

}

 /**
 * Povoleni ukoncene sazky
 * @param int $sazka_id id sazky kterou chceme povolit
 * @return void
 */
private function RunBet($sazka_id) {
	$ws = Zend_Registry::get('ws');

	$status = true;
	$sql = "select sazka_id,bookmaker_id,status,platna_do from sazky where sazka_id=".$sazka_id;
	$res = $this->dbGame->query($sql);

	if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");

	if($row = $res->fetchRow());
	else throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");

	#Ukoncit lze pouze aktualni sazku#
	if($row['status'] == 2) {
		#test zda patri sazka danemu bookmakerovi pokud se nejedna o superbookmakera#
		if($_SESSION['superbookmaker'] != 1) {
			$sql = "select sazka_id,proplacena from sazky where sazka_id=".$sazka_id." and bookmaker_id=".intval($_SESSION['bookmaker']);
			$res = $this->dbGame->query($sql);
			if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");

			if($_SESSION['bookmaker'] != $row['bookmaker_id']) {
				$this->vrat .= "<div class=\"errormsg\"> Nemáte právo pozastavit tuto sázku</div><br />";
				$this->vrat .= UiUtil::printErrors(i18n::tr('Bet {0}  insufficient credentials!',$sazka_id));
				$status = false;
			}
		}
		#Test zda uz neni sazka po platnosti#

		if(!It6_Date::checkFormatDB($row['platna_do']) || It6_Date::fromDbAsTimestamp($row['platna_do'], false) < time()) {
			//$this->vrat .= "<div class=\"errormsg\"> Sázka už je po platnosti a nemá smysl rušit ukonèení</div><br />";
			$this->vrat .= UiUtil::printErrors(i18n::tr('Bet {0}  už je po platnosti a nemá smysl rušit ukončení.',$sazka_id));
			$status = false;
		}
	} else $status = false;

	if ($status) {

		try {
			if ($newParentId = $this->ws->Bet->updateBetPackHierarchy($sazka_id)) {
				$this->vrat .= UiUtil::printMessages(i18n::tr('New parent #{0} for bet #{1} was found.',$newParentId, $sazka_id));
				It6_Log::info(
					"New parent (#'%parent%') for bet  #'%bet%' has been resolved'.",
					It6_Log::TAG_BOOKMAKER_OPERATION,
					array('bet'=> $sazka_id, 'parent' => $newParentId)
				);
			}
		}
		catch (Exception $e) {
			$status = false;
			$this->errors[] = I18n::tr("Error: Bet Id #{0} could not be activated - parent bet issues", $sazka_id);
			It6_Log::err(
				'Cannot update bet pack hierarchy',
				It6_Log::TAG_BOOKMAKER_OPERATION,
				array('bet'=> $sazka_id),
				$e
			);
		}
	}

	if ($status) {
		$allowRunBet = false;
		if ($this->ws->Bet->isSuspendedAndOpened($sazka_id)) {

			if (!$this->ws->Bet->hasAlias($sazka_id)) {
				//resolve alias
				$alias = $this->generateBetAlias($sazka_id);
				$allowRunBet = (false !== $alias);
			} else {
				$allowRunBet = true;
			}
		} else {
			//do nothing
		}

		if ($allowRunBet)
			$this->activateBet($sazka_id);

	} //status
}

private function activateBet($sazka_id) {
	if ($this->ws->Bet->activateBet($sazka_id)) {
		$this->vrat .= UiUtil::printMessages(i18n::tr('Bet {0} was activated.',$sazka_id));
		It6_Log::info(
			"Bet #'%bet%' was activated.",
			It6_Log::TAG_BOOKMAKER_OPERATION,
			array('bet'=> $sazka_id)
		);
		$this->processedBetsOk++;
		//NOTE: cache is invalidated in WS
	} else {
		$this->vrat .= UiUtil::printError(i18n::tr('Bet {0} : error activating bet.',$sazka_id));
		$this->processedBetsError++;
	}
}

private function RunBets($bets) {
	$this->processedBetsOk = 0;
	$this->processedBetsError = 0;
	
	$status = true;
	$sql = "select sazka_id,bookmaker_id,status,platna_do from sazky where sazka_id IN (".$bets.")";
	$res = $this->db->query($sql);
	$bets_arr = $res->fetchAll();
	
	if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");

	/*if($row = $res->fetchRow());
	else throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");
*/
	foreach ($bets_arr as $row){
		$sazka_id = $row['sazka_id'];
		#Ukoncit lze pouze aktualni sazku#
		if($row['status'] == 2) {
			#test zda patri sazka danemu bookmakerovi pokud se nejedna o superbookmakera#
			if ($_SESSION['superbookmaker'] != 1) {
				if ($_SESSION['bookmaker'] != $row['bookmaker_id']) {
					//$this->vrat .= "<div class=\"errormsg\"> Nemáte právo pozastavit tuto sázku</div><br />";
					$this->vrat .= UiUtil::printErrors(i18n::tr('Bet {0}  insufficient credentials!',$sazka_id));
					$status = false;
				}
			}
			#Test zda uz neni sazka po platnosti#

			if(!It6_Date::checkFormatDB($row['platna_do']) || It6_Date::fromDbAsTimestamp($row['platna_do'], false) < time()) {
				//$this->vrat .= "<div class=\"errormsg\"> Sázka už je po platnosti a nemá smysl rušit ukonèení</div><br />";
				$this->vrat .= UiUtil::printErrors(i18n::tr('Bet {0}  už je po platnosti a nemá smysl rušit ukončení.',$sazka_id));
				$status = false;
			}
		} else $status = false;

		if ($status) {
			$sql = "update sazky set status=0,info='' where sazka_id=".$sazka_id;
			$res = $this->dbGame->query($sql);
			if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: pozastaveni sazky',"admin_ex_db");
			$this->vrat .= UiUtil::printMessages(i18n::tr('Bet {0} was activated.',$sazka_id));
			//"<div class=\"okmsg\">Sázka (#".$sazka_id.") pozastavení sázky bylo zrušeno</div><br />";
			if (0 < $this->dbGame->affectedRows()) {
				It6_Models_BetChangelog::saveBetChangeOfStatus($sazka_id, 0, 0, 0);
			}
			$this->updatedBets[$sazka_id] = true;

			//setting up alias to bets from betradar
			//	if (sazka je z betradaru && je suspended && alias)
			//		{prirad alias}
			if ($ws->Bet->isBetradarBet($sazka_id) &&
					$ws->Bet->isSuspendedAndOpened($sazka_id) &&
						!$ws->Bet->hasAlias($sazka_id)) {
				//TODO resolve alias
				if ($alias = $ws->Bet->generateAlias($sazka_id)) {
					$this->vrat .= UiUtil::printMessages(i18n::tr('Bet {0} got alias {1}.',$sazka_id, $alias));
					It6_Log::info(
						"Bet #'%bet%' got alias '%alias%'.",
						It6_Log::TAG_BOOKMAKER_OPERATION,
						array('bet'=> $sazka_id, 'alias' => $alias)
					);
				}
				else {
					$this->vrat .= UiUtil::printErrors(i18n::tr('Bet {0} error by setting alias.',$sazka_id));
					It6_Log::err(
						"Bet #'%bet%' error by setting alias",
						It6_Log::TAG_BOOKMAKER_OPERATION,
						array('bet'=> $sazka_id)
					);
				}
			} else {
				//die('false');
			}

			It6_Log::info(
				"Bet #'%bet%' was set to active.",
				It6_Log::TAG_BOOKMAKER_OPERATION,
				array('bet'=> $sazka_id)
			);
		}
	}
}
  /**
 * Ukonceni sazky
 * @param int $sazka_id id sazky kterou chceme ukoncit
 * @return void
 */

private function StopBet($sazka_id) {
	$status = true;
	$sql = "select sazka_id,bookmaker_id,status,platna_do from sazky where sazka_id=".$sazka_id;
	$res = $this->dbGame->query($sql);

	if(DB::isError($res)) {
		$this->processedBetsError++;
		throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");
	}

	if($row = $res->fetchRow());
	else {
		$this->processedBetsError++;
		throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");
	}

	#Ukoncit lze pouze aktualni sazku#
	if($row['status'] == 0) {
		#test zda patri sazka danemu bookmakerovi pokud se nejedna o superbookmakera#
		if($_SESSION['superbookmaker'] != 1) {
			$sql = "select sazka_id,proplacena from sazky where sazka_id=".$sazka_id." and bookmaker_id=".intval($_SESSION['bookmaker']);
			$res = $this->dbGame->query($sql);
			if(DB::isError($res)) {
				$this->processedBetsError++;
				throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");
			}

			if($_SESSION['bookmaker'] != $row['bookmaker_id']) {
				$this->vrat .= "<div class=\"errormsg\"> Nemáte právo pozastavit tuto sázku</div><br />";
				$status = false;
			}
		}

		#Test zda uz neni sazka po platnosti#
		if(!It6_Date::checkFormatDB($row['platna_do']) || It6_Date::fromDbAsTimestamp($row['platna_do'], false) < time()) {
			$this->vrat .= UiUtil::printErrors(i18n::tr('Sázka {0} už je po platnosti a nemá smysl ukonèovat.',$sazka_id));
			$status = false;
		}
	}
	else $status = false;

	if ($status) {
		//TODO: rewrite
		$sql = "SELECT nick FROM bookmaker where bookmaker_id=".intval($_SESSION['bookmaker']);
		$res = $this->dbGame->query($sql);

		if(DB::isError($res)) {
			$this->processedBetsError++;
			throw new ExHandler('Nepodarilo se provest dotaz: vyhledani uzivatele',"admin_ex_db");
		}

		if($row = $res->fetchRow());
		else $row['nick'] = '';

		//$sql = "update sazky set status=2,info='!Manuálně ukončeno - ".Help::Slash($row['nick'])." !' where sazka_id=".$sazka_id;
		$sql = "update sazky set status=2 where sazka_id=".$sazka_id;

		$res = $this->dbGame->query($sql);

		if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: zruseni  sazky',"admin_ex_db");
		if (0 < $this->dbGame->affectedRows()) {
			It6_Models_BetChangelog::saveBetChangeOfStatus($sazka_id, 2, 0, 0);
		}

		$this->updatedBets[$sazka_id] = true;

		$this->vrat .= UiUtil::printMessages(i18n::tr('Bet {0} was suspended.',$sazka_id));

		It6_Log::info(
			"Bet #'%bet%' was suspended.",
			It6_Log::TAG_BOOKMAKER_OPERATION,
			array('bet'=> $sazka_id)
		);
		$this->processedBetsOk++;
	}
}

/**
 * Takes new alias and updates bet. Prints UI messages.
 * This function mustn't be called on bets that already has alias or can't get alias.
 * @param integer $sazka_id
 * @return integer|boolean Alias or FALSE on error
 */
private function generateBetAlias($sazka_id) {
	// dočasné odpojení kvůli chybám (zakomentvání celé metody) - pak zprovoznit
	return 123;
	/*try {
		$alias = $this->ws->Bet->generateAlias($sazka_id);

		//generate alias success
		$this->vrat .= UiUtil::printMessages(i18n::tr('Bet {0} got alias {1}.', $sazka_id, $alias));
		It6_Log::info(
			"Bet #'%bet%' got alias '%alias%'.",
			It6_Log::TAG_BOOKMAKER_OPERATION,
			array('bet'=> $sazka_id, 'alias' => $alias)
		);
		return $alias;
	} catch (Exception $e) {
		//generate alias error
		$this->vrat .= UiUtil::printErrors(i18n::tr('Bet {0} activation error by running generateAlias. Probably out of bet aliases. The bet is still suspended.',$sazka_id));
		It6_Log::err(
			"Bet #'%bet%' error by generating alias",
			It6_Log::TAG_BOOKMAKER_OPERATION,
			array('bet'=> $sazka_id)
		);
		$this->processedBetsError++;
		return false;
	}*/
}

  /**
 * Obnoveni zrusene sazky
 * @param int $sazka_id id sazky kterou chceme rusit
 * @return void
 */

private function RetriveBet($sazka_id) {
	$status = true;
	$sql = "select sazka_id,overena,proplacena,bookmaker_id,status,vysledek,alias,alias_released from sazky where sazka_id=".$sazka_id;
	$res = $this->dbGame->query($sql);

	if(DB::isError($res)) {
		$this->processedBetsError++;
		throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");
	}
	
	if($row = $res->fetchRow());
	else {
		$this->processedBetsError++;
		throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");
	}

	#test zda patri sazka danemu bookmakerovi pokud se nejedna o superbookmakera#

	if($_SESSION['superbookmaker'] != 1) {
		$sql = "select sazka_id,proplacena from sazky where sazka_id=".$sazka_id." and bookmaker_id=".intval($_SESSION['bookmaker']);
		$res = $this->dbGame->query($sql);
		if(DB::isError($res)) {
			$this->processedBetsError++;
			throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");
		}
		
		if($_SESSION['bookmaker'] != $row['bookmaker_id']) {
			$this->vrat .= UiUtil::printErrors(i18n::tr('Insufficient privileges to cancel bet {0}.',$sazka_id));
			$status = false;
		}
	}
	#Test zda uz neni proplacena#
	if($row['proplacena']  == 1) {
		$this->vrat .= UiUtil::printErrors(i18n::tr('Bet {0} is already paid out and cannot be renewed.',$sazka_id));
		$status = false;
	}
	
	if($row['status'] != 1)
		$status = false;
	
	if($status) {
		if (strlen($row['vysledek']) > 0) $st = 3;
		else $st = 0;

		if (0 == $st && !$this->ws->Bet->hasAlias($sazka_id)) {
			$alias = $this->generateBetAlias($sazka_id);
			if (false === $alias) {
				$this->vrat .= UiUtil::printErrors(i18n::tr('Bet {0} was not renewed.',$sazka_id));
				It6_Log::err(
					"Bet #'%bet%' was not renewed.",
					It6_Log::TAG_BOOKMAKER_OPERATION,
					array('bet'=> $sazka_id)
				);
				return false;
			}
		}

		$sql = "update sazky set status=".$st." where sazka_id=".$sazka_id;
		$res = $this->dbGame->query($sql);
		
		$sql2 = "update ticket_kurz set ticket_sazka_zrusena=0 where sazka_id=$sazka_id;";
		$res2 = $this->dbGame->query($sql2);
		
		$sql3 = "update ticket_kurz set ticket_sazka_zrusil_bookmaker_id=null where sazka_id=$sazka_id;";
		$res3 = $this->dbGame->query($sql3);

		if(DB::isError($res) || DB::isError($res2) || DB::isError($res3)) throw new ExHandler('Nepodarilo se provest dotaz: obnoveni  sazky',"admin_ex_db");

		if (0 < $this->dbGame->affectedRows()) {
			It6_Models_BetChangelog::saveBetChangeOfStatus($sazka_id, $st, $row['overena'], 0);
		}
		
		$this->updatedBets[$sazka_id] = true;

		
		$this->vrat .= UiUtil::printMessages(i18n::tr('Bet {0} was renewed.',$sazka_id));

		It6_Log::info(
			"Bet #'%bet%' was renewed.",
			It6_Log::TAG_BOOKMAKER_OPERATION,
			array('bet'=> $sazka_id, 'admin'=>intval($_SESSION['bookmaker']))
		);
		$this->processedBetsOk++;
	}
}

   /**
 * Zruseni sazky
 * @param int $sazka_id id sazky kterou chceme rusit
 * @return void
 */

private function DeleteBet($sazka_id){
	$status = true;
	$sql = "select sazka_id,proplacena,bookmaker_id,status from sazky where sazka_id=".$sazka_id;
	$res = $this->dbGame->query($sql);

	if(DB::isError($res)) {
		$this->processedBetsError++;
		throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");
	}

	if($row = $res->fetchRow()); else {
		$this->processedBetsError++;
		throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");
	}

	#test zda patri sazka danemu bookmakerovi pokud se nejedna o superbookmakera#

	if($_SESSION['superbookmaker'] != 1) {
		$sql = "select sazka_id,proplacena from sazky where sazka_id=".$sazka_id." and bookmaker_id=".intval($_SESSION['bookmaker']);
		$res = $this->dbGame->query($sql);
		if(DB::isError($res)) {
			$this->processedBetsError++;
			throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");
		}
		
		if($_SESSION['bookmaker'] != $row['bookmaker_id']) {
			$this->vrat .=  UiUtil::printErrors(i18n::tr('Insufficient privileges to cancel bet {0}.',$sazka_id));
			//"<div class=\"errormsg\"> Nemáte právo rušit tuto sázku</div><br />";$status = false;
			$status = false;
		}
	}

	#Test zda uz neni proplacena#
	if($row['proplacena']  == 1) {
		$this->vrat .= UiUtil::printErrors(i18n::tr('Bet {0} is already paid out and cannot be cancelled.',$sazka_id));
		//"<div class=\"errormsg\"> Sázka už je proplacena a nelze ji zrušit</div><br />";
		$status = false;
	}

	if ($row['status'] == 1)
		$status = false;

	if($status) {
		$sql = "update sazky set status=1 where sazka_id=$sazka_id;";
		$res = $this->dbGame->query($sql);
		
		$sql2 = "update ticket_kurz set ticket_sazka_zrusena=1 where sazka_id=$sazka_id;";
		$res2 = $this->dbGame->query($sql2);
		
		$sql3 = "update ticket_kurz set ticket_sazka_zrusil_bookmaker_id=".intval($_SESSION['bookmaker'])." where sazka_id=$sazka_id;";
		$res3 = $this->dbGame->query($sql3);

		$this->updatedBets[$sazka_id] = true;

		It6_Models_BetChangelog::saveBetChangeOfStatus($sazka_id, It6_Models_Bet::STATUS_CANCELED, 0, 0);

		if(DB::isError($res) || DB::isError($res2) || DB::isError($res3)) throw new ExHandler('Nepodarilo se provest dotaz: zruseni  sazky',"admin_ex_db");

		$this->vrat .= UiUtil::printMessages(i18n::tr('Bet {0} was cancelled.',$sazka_id));

		It6_Log::info(
			"Bet #'%bet%' was canceled.",
			It6_Log::TAG_BOOKMAKER_OPERATION,
			array('bet'=> $sazka_id)
		);
		$this->processedBetsOk++;
	}
}



   /**
 * Navrat do stavu nevyhodnocená
 * @param int $sazka_id id sazky kterou chceme oeditovat
 * @return void
 */

  private function ClearRate($sazka_id){

   $status = true;

   #test zda patri sazka danemu bookmakerovi pokud se nejedna o superbookmakera#

   if($_SESSION['superbookmaker'] != 1) {

    $sql = "select sazka_id from sazky where sazka_id=".intval($sazka_id)." and bookmaker_id=".intval($_SESSION['bookmaker']);

    $res = $this->dbGame->query($sql);

    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");

	if(!$res->fetchRow()){

	 $this->vrat .= "<div class=\"errormsg\"> Nemáte právo editovat sázku</div><br />";$status = false;

	}

   }

   #test zda sazka ma status vyhodnocena#

   $sql = "select sazka_id from sazky where sazka_id=".intval($sazka_id)." and status=3 and overena=0";

   $res = $this->dbGame->query($sql);

   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");

   if(!$res->fetchRow()){

	   $this->vrat .= "<div class=\"errormsg\"> Rušení výsledku je možné pouze u neověřené sázky</div><br />";$status = false;

   }

   if($status) {

    $sql = "update sazky set status=0,vysledek='' where sazka_id=".intval($sazka_id);

    $res = $this->dbGame->query($sql);

    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");

	$this->updatedBets[$sazka_id] = true;

    $sql = "delete from sazky_result  where sazka_id=".intval($sazka_id);

    $res = $this->dbGame->query($sql);

    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");

	It6_Models_BetChangelog::saveBetChangeOfStatus($sazka_id, It6_Models_Bet::STATUS_NEW, 0, 0);

    $this->vrat .= UiUtil::printMessages("Výsledky byly vynulovány");

	It6_Log::info(
		"Bet #'%bet%' result was canceled.",
		It6_Log::TAG_BOOKMAKER_OPERATION,
		array('bet'=> $sazka_id)
	);

   }

  }


   /**
 * Urceni vice vysledku sazky
 * @return void
 */

private function SetMultiResult() {
	
	$p1 = explode(";",$_POST['result_set_hid']);
	$sazky = array();

	foreach($p1 as $h) {
		
		$sloupec_id = false;
		if(substr_count($h,':') == 0) continue;

		list($sazka_id,$sloupec_id) = explode(":",$h);

		$sloupec_id = intval($sloupec_id);

		if ($sloupec_id == 0 || !$sloupec_id || !is_numeric($sloupec_id) || !is_numeric($sazka_id) || strlen($h) == 0 ||
				(isset($sazky[$sazka_id]) && mb_substr_count($sazky[$sazka_id],$sloupec_id)>0))
			continue;

		if (!isset($sazky[$sazka_id])) $sazky[$sazka_id] = "";

		$sazky[$sazka_id] .= $sloupec_id.";";
	}

	foreach($sazky as $sazka_id => $h) {
		$status = true;
		//$h = substr($h,0,-1);

		#test zda patri sazka danemu bookmakerovi pokud se nejedna o superbookmakera#
		if ($_SESSION['superbookmaker'] != 1) {
			$sql = "select sazka_id from sazky where sazka_id=".intval($sazka_id)." and bookmaker_id=".intval($_SESSION['bookmaker']);
			$res = $this->dbGame->query($sql);

			if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");
			if (!$res->fetchRow()) {
				$this->vrat .= "<div class=\"errormsg\"> Nemáte právo určit výsledek sázky #".$sazka_id."</div><br />";$status = false;
			}
		}

		#test zda sazka ma status nevyhodnocena#
		$sql = "select sazka_id from sazky where sazka_id=".intval($sazka_id)." and ( (platna_do <= now() and (status=0 or status=2)) or (status=3 and overena=0) )";
		$res = $this->dbGame->query($sql);
		if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");

		if(!$res->fetchRow()){
			$this->vrat .= "<div class=\"errormsg\"> Výsledek lze stanovit pouze u nevyhodnocené a vyhodnocené sázky #".$sazka_id."</div><br />";$status = false;
		}

		if($status) {
			$sql = "update sazky set status=3,vysledek=CONCAT('".$h."',vysledek) where sazka_id=".intval($sazka_id);
			$res = $this->dbGame->query($sql);
			if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");

			if (0 < $this->dbGame->affectedRows()) {
				It6_Models_BetChangelog::saveBetChangeOfStatus($sazka_id, 3, 0, 0);
			}

			//    $this->vrat .= "<div class=\"okmsg\">Výsledek byl úspěšně stanoven #".$sazka_id."</div><br />";
			$this->messages[] = I18n::tr('The result of the bet {0} was set.',$sazka_id);
			$this->updatedBets[$sazka_id] = true;

			It6_Log::info(
				"Bet #'%bet%' result was set: '%sloupec%'.",
				It6_Log::TAG_BOOKMAKER_OPERATION,
				array(
					'bet'		=> $sazka_id,
					'sloupec'	=> $h
				)
			);
		}
	}
}

 /**
 * Urceni vysledku sazky
 * @return void
 */

public function isBetUnevaluated($sazkaId) {
	#test zda sazka ma status nevyhodnocena#
	$sql = "SELECT sazka_id FROM sazky
			WHERE sazka_id=".$sazkaId." AND ( (platna_do <= now() AND (status=0 OR status=2)) OR (status=3 AND overena=0) )";
	$res = $this->dbGame->query($sql);

	DbUtil::testResult($res,'Nepodarilo se provest dotaz: vyber z tabulky  sazky');

	if(!$res->fetchRow()) {
		$this->vrat .= "<div class=\"errormsg\"> Výsledek lze stanovit pouze u nevyhodnocené a vyhodnocené sázky</div><br />";
		return false;
	} else return true;
	
}

private function SetResult() {
	$status = true;
	//$sazkaId = intval($_GET['sazka_id']);
	$sazkaId = key($_POST['vysledek']);
	$sloupecId = key($_POST['vysledek'][$sazkaId]);
	$score = $_POST['vysledek'][$sazkaId][$sloupecId];

	#test zda patri sazka danemu bookmakerovi pokud se nejedna o superbookmakera#
	if($_SESSION['superbookmaker'] != 1) {
		$sql = "SELECT sazka_id FROM sazky WHERE sazka_id=".$sazkaId." AND bookmaker_id=".intval($_SESSION['bookmaker']);
		$res = $this->dbGame->query($sql);

		DbUtil::testResult($res,'Nepodarilo se provest dotaz: vyber z tabulky  sazky');

		if(!$res->fetchRow()) {
			$this->vrat .= "<div class=\"errormsg\"> Nemáte právo určit výsledek této sázky</div><br />";
			$status = false;
		}
	}



	$status = $this->isBetUnevaluated($sazkaId);


	if($status) {
		// Just change score
		if ( 'setScore' == $sloupecId ) {
			$sql = "UPDATE sazky SET score='".$score."' WHERE sazka_id=".$sazkaId;
			$res = $this->dbGame->query($sql);

			DbUtil::testResult($res,'Nepodarilo se provest dotaz: zmena vysledku sazky');

		//    $this->vrat .= "<div class=\"okmsg\">Výsledek byl úspěšně stanoven</div><br />";
			$this->messages[] = I18n::tr('The score of the bet {0} was changed: {1}.', $sazkaId, $score);

			It6_Log::info(
				"Bet #'%bet%' score was changed: '%score%'.",
				It6_Log::TAG_BOOKMAKER_OPERATION,
				array(
					'bet'		=> $sazkaId,
					'score'	=> $score
				)
			);
		} else {
			//$sloupecId = intval($_GET['sloupec_id']);
			$sql = "UPDATE sazky SET status=3,vysledek=CONCAT('".$sloupecId.";',vysledek),score='".$score."' WHERE sazka_id=".$sazkaId;
			$res = $this->dbGame->query($sql);

			DbUtil::testResult($res,'Nepodarilo se provest dotaz: vlozeni vysledku sazky');

		//    $this->vrat .= "<div class=\"okmsg\">Výsledek byl úspěšně stanoven</div><br />";
			$this->messages[] = I18n::tr('The result of the bet {0} was set - column {1}, result {2}.', $sazkaId, $sloupecId, $score);

			if (0 < $this->dbGame->affectedRows()) {
				It6_Models_BetChangelog::saveBetChangeOfStatus($sazkaId, 3, 0, 0);
			}
			$this->updatedBets[$sazkaId] = true;

			It6_Log::info(
				"Bet #'%bet%' result was set: '%sloupec%', score '%score%'.",
				It6_Log::TAG_BOOKMAKER_OPERATION,
				array(
					'bet'		=> $sazkaId,
					'sloupec'	=> $sloupecId,
					'score'	=> $score
				)
			);
		}
	}
}

/**
 * metoda vypise vsechny zadane typy
 * @return void
 */
public function ShowBet(){

	$pagLimitLow = 0;
	$pagLimitHigh = 10;
	$preklad = new Preklady();
	//SU: $udalost = 
	$help = $bookmaker = $typ = "";

	//SU: $sport_udalost = $sport = $sport_opt = 
	$book = $sazka = $help_ar = $typ_ar = $podtyp = $sloupec = $sazka_poradi = array();

	$this->filtrURL .= 'filtr=1&';

	$bookmakers = It6_Models_Admin::readDataAllForRoles(array('bookmaker', 'superbookmaker'), false, $this->dbAdmin);
	foreach ($bookmakers as $data) {
		$id = $data['id'];

		$book[$id]['nick'] = $data['username'];
		$book[$id]['jmeno'] = $data['firstName'] . ' ' . $data['surname'];
	}

	$_sports = It6_SportEventsFilter::getSportEvents(empty($_REQUEST['sport_opt']) ? null : $_REQUEST['sport_opt']);
	list($htmlSports, $htmlEvents) = It6_SportEventsFilter::getSportFilterHtml(
		$_sports,
		(empty($_REQUEST['sport_opt']) ? null : $_REQUEST['sport_opt']),
		(empty($_POST['udalost']) ? null : $_POST['udalost']),
		true,
		true
	);
	$filterEventsSports = array();
	$filterEvents = array();
	$filterEventIds = (!empty($_POST['udalost']) && is_array($_POST['udalost']) ? $_POST['udalost'] : array());
	$filterEventIds = array_filter($filterEventIds, function($item) { return 0 != $item; });
	foreach ($_sports as $s) {
		foreach ($s[2] as $r) {
			foreach ($r[2] as $e) {
				if (empty($filterEventIds) || in_array($e['eventId'], $filterEventIds)) {
					if (!array_key_exists($s[0], $filterEventsSports))
						$filterEventsSports[$s[0]] = array('id' => $s[0], 'name' => $s[1]);
					$filterEvents[$e['eventId']] = array(
						'eventId' => $e['eventId'],
						'sportId' => $s[0],
						'name' => $e['name'],
					);
				}
			}
		}
	}
	unset($_sports);

   #Vyber sazek#

   $where = $where2 = "";

	$filter_where = $this->processFilterInput($_REQUEST, $_POST, $_GET);
	$where = $filter_where['where'];
	$where2 = $filter_where['where2'];
	
	$typ .= "<script type=\"text/javascript\">
				function setChckAll(onready){
					var allChecked = true;
					var allUnchecked = true;
					$('.betTypeChck').each(function () {
						if (!$(this).prop('checked')) {
							allChecked = false;
						} else {
							allUnchecked = false;
						}
					});
					$('#chAll').prop('checked', allChecked);
					if(onready && allUnchecked){
						// check all
						$('#chAll, .betTypeChck').prop('checked', true);
					}
				}
				function checkAllIfNothingChecked() {
					var somethingChecked = false;
					$('.betTypeChck').each(function () {
						if ($(this).prop('checked')) {
							somethingChecked = false;
						}
					});
				}
	
				$(function() {
					loadTypeOpts([" . (isset($_REQUEST['typ']) ? implode(',', array_keys($_REQUEST['typ'])) : '') . "]);
					/*$('#chAll').click (function () {
						var checkedStatus = this.checked;
						$('.betTypeChck').each(function () {
							$(this).prop('checked', checkedStatus);
						});
					});
					$('.betTypeChck').click(function () {
						setChckAll(false);
						checkAllIfNothingChecked();
					});
					setChckAll(true);*/
				});
	
			</script>
			<table class=\"bet-types\" style=\"visibility:hidden;\">";
	$types_res = $this->loadTypes($filterEvents, $_REQUEST, $_POST);
	$typ_ar = $types_res['typ_ar'];
	

   $typ .= "</table>";



   #Vyber podtypu#

   $sql = "select podtyp_id,radek_sloupec,sloupec_pocet_max,text from podtyp";

   $res2 = $this->dbGame->query($sql);

   if(DB::isError($res2)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky podtyp',"admin_ex_db");

	$subtypeTexts = array();
	while ($row = $res2->fetchRow()){
		$podtyp[$row['podtyp_id']] = array(
			'radek' => $row['radek_sloupec'],
			'sloupec_pocet_max' => $row['sloupec_pocet_max'],
			'text' => $row['text'],
		);
		$subtypeTexts[$row['text']] = $row['text'];

	}
	$res = $this->dbGame->query('SELECT `index_pole`,`text` FROM preklady WHERE index_pole IN (\'' . implode('\',\'', array_keys($podtyp)) . '\') AND lang_id=1');
	while ($row = $res->fetchRow())
		$subtypeTexts[$row['index_pole']] = $row['text'];
	array_walk($podtyp, function(&$item, $key) use ($subtypeTexts) { $item['text'] = (empty($subtypeTexts[$item['text']]) ? '' : $subtypeTexts[$item['text']]); });

	//pagination defaultCount;
	$listerCount = $this->ws->Parameter->getAdminParameter('pagination.Bet.count',$_SESSION['bookmaker']);

	if ( (isset($_REQUEST['filtr']) || isset($_REQUEST['result_set'])) && isset($_REQUEST['udalost'])) {

		$sql = "SELECT COUNT(s.sazka_id) c FROM sazky s
					JOIN udalost u ON u.udalost_id = s.udalost_id 
					WHERE $where ".(isset($_REQUEST['typ']) && !isset($_REQUEST['typ'][0]) && 
					mb_strlen($where2) > 0 ? " AND $where2" : "");

		$rowCount = $this->dbGame->query($sql)->fetchRow();
		$rowCount = intval($rowCount['c']);

		$this->lister = new Lister('sz', $listerCount, $rowCount, 0, 15);
		$this->lister->setPost(true);
		$this->lister->updateFromParams($_POST);

		if (!empty($_POST['filtr'])) $this->lister->setFrom(0);

		$from = $this->lister->getFrom(); // exclusive
		$to = $this->lister->getCount(); // inclusive
		
		$sql_ids = "SELECT s.sazka_id FROM sazky s
					JOIN udalost u ON u.udalost_id = s.udalost_id 
					WHERE $where ".(isset($_REQUEST['typ']) && !isset($_REQUEST['typ'][0]) && 
					mb_strlen($where2) > 0 ? " AND $where2" : ""). " ORDER BY s.platna_do " . $_REQUEST["order"] . ", s.sazka_id,s.status LIMIT $from,$to";
		$res_ids = $this->dbGame->query($sql_ids);

		if(DB::isError($res_ids))
			throw new ExHandler($sql_ids.'Nepodarilo se provest dotaz: vyber sazek',"admin_ex_db");
		$sazka_ids = array();
		while ($row = $res_ids->fetchRow()) {
			$sazka_ids[] = $row["sazka_id"];
		}
		
		if (!empty($sazka_ids))
			$sazka_ids_where = ' AND s.sazka_id IN (' . implode(",", $sazka_ids) . ')';
		else 
			$sazka_ids_where = ' AND 0=1';
		$sql =
			'SELECT
						s.sazka_id,
						s.proplatil_bookmaker,
						s.info,
						s.proplacena,
						s.platna_od,
						s.risk_limit,
						s.ako,
						s.risk_limit_balance,
						s.platna_do,
						s.status,
						s.live,
						s.bookmaker_id,
						s.vysledek,
						s.udalost_id,
						s.typ_id,
						s.real_typ_id,
						s.podtyp_id,
						s.overena,
						s.text,
						s.ticket_text,
						s.parent_id,
						s.jednoducha,
						s.betradar_autoupdate,
						s.betradar_match_id,
						s.score,
						s.score_note,
						s.alias,
						s.alias_new,
						s.text_note,
						sk.poradi,
						sk.kurz,
						sk.kurz_zmena,
						sk.platny_od,
						pts.nazev,
						pts.sloupec_id,
						pts.poradi AS poradi_sloupec,
						/*LPAD(t.typ_alias_id,2,\'0\') AS typ_alias_id,*/
						t.typ_alias_id,
						bs.kurz_min,
						bs.kurz_max,
						bs.vyhernost_min,
						bs.vyhernost_max,
						r.ft,
						r.ht,
						r.ot,
						r.1t,
						r.2t,
						r.3t,
						r.ap,
						ob.nazev AS regionName
					FROM
						sazky s
					JOIN sazka_kurz_aktualni sk
						ON s.sazka_id = sk.sazka_id
					JOIN podtyp_sloupce pts
						ON sk.sloupec_id = pts.sloupec_id
					JOIN typ t
						ON t.typ_id = COALESCE(s.real_typ_id, s.typ_id)
					JOIN udalost u
						ON s.udalost_id = u.udalost_id
					JOIN oblast ob
						ON u.oblast_id = ob.oblast_id
					LEFT JOIN bet_settings bs
						ON bs.udalost_id = u.udalost_id
						AND bs.typ_id = s.typ_id
						AND bs.podtyp_id = s.podtyp_id
					LEFT JOIN sazky_result r
						ON s.sazka_id = r.sazka_id
					WHERE
						'.$where
						.(isset($_REQUEST['typ']) && !isset($_REQUEST['typ'][0]) && mb_strlen($where2) > 0 ? " AND $where2" : "").'
						'.$sazka_ids_where.'
					ORDER BY s.platna_do '.$_REQUEST['order'].', s.sazka_id, poradi_sloupec';
	}
	else {
		$this->lister = new Lister('sz', $listerCount, 0, 0, 15);
		$this->lister->setPost(true);
		$this->lister->updateFromParams($_POST);
		$this->lister->setFrom(0);
		$sql = "SELECT * FROM sazky WHERE 0=1";
	}

	$res2 = $this->dbGame->query($sql);
	if(DB::isError($res2))
		throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber z tabulky sazky, sazka_kurz a podtyp_sloupec',"admin_ex_db");

	while ($row = $res2->fetchRow()) {
		if (strlen($row['typ_alias_id']) == 1) $row['typ_alias_id'] = '0'.$row['typ_alias_id'];

		$help_ar[$row['sazka_id']] = 1;

		if(
			isset($_POST['udalost'])
			&& is_array($_POST['udalost'])
			&& !in_array(0,$_POST['udalost'])
			&& !in_array($row['udalost_id'],$_POST['udalost'])
		)
			continue;

		if(!isset($sazka[$row['sazka_id']]['udalost'])){
			$event = $filterEvents[$row['udalost_id']];
			$sazka[$row['sazka_id']] = array(
				'podtyp_radek' => $podtyp[$row['podtyp_id']]['radek'],
				'podtyp_max' => $podtyp[$row['podtyp_id']]['sloupec_pocet_max'],
				'podtyp_text' => $podtyp[$row['podtyp_id']]['text'],
				'platna_od' => It6_Date::fromDb($row['platna_od']),
				'platna_do' => It6_Date::fromDb($row['platna_do']),
				'status' => $row['status'],
				'info' => $row['info'],
				'bookmaker' => (empty($book[$row['bookmaker_id']])
					? ''
					: $book[$row['bookmaker_id']]['jmeno']." (".$book[$row['bookmaker_id']]['nick'].")"
				),
				'udalost_id' => $row['udalost_id'],
				'udalost'			=> $event['name'],
				'regionName'		=> $row['regionName'],
				'sport_id'			=> $event['sportId'],
				'sport'				=> $filterEventsSports[ $event['sportId'] ]['name'],
				'live'				=> $row['live'],
				'risk_limit'		=> $row['risk_limit'],
				'ako'				=> $row['ako'],
				'risk_limit_balance'=> $row['risk_limit_balance'],
				'typ'				=> $typ_ar[$row['typ_id']],
				'typ_id'			=> $row['typ_id'],
				'real_typ_id'		=> $row['real_typ_id'],
				'podtyp_id'			=> $row['podtyp_id'],
				'text'				=> $row['text'],
				'ticket_text'		=> $row['ticket_text'],
				'parent_id'			=> $row['parent_id'],
				'overena'			=> $row['overena'],
				'alias'				=> $row['alias'].ALIAS_DELIMITER.$row['typ_alias_id'],
				'alias_new'			=> $row['alias_new'],
				'ft'				=> $row['ft'],
				'ot'				=> $row['ot'],
				'ht'				=> $row['ht'],
				'1t'				=> $row['1t'],
				'2t'				=> $row['2t'],
				'3t'				=> $row['3t'],
				'ap'				=> $row['ap'],
				'kurz_min'			=> $row['kurz_min'],
				'kurz_max'			=> $row['kurz_max'],
				'vyhernost_min'		=> $row['vyhernost_min'],
				'vyhernost_max'		=> $row['vyhernost_max'],
				'proplacena' 		=> $row['proplacena'],
				'jednoducha'		=> $row['jednoducha'],
				'vysledek'			=> explode(";",$row['vysledek']),
				'betradar_bet_id'	=> $row['betradar_match_id'],
				'betradar_autoupdate'=> $row['betradar_autoupdate'],
				'score'				=> $row['score'],
				'score_note'		=> $row['score_note'],
				'text_note'			=> $row['text_note'],
			);

			if ($row['overena'] != 0) {
				if (isset($book[$row['overena']]['jmeno'])) {
					$sazka[$row['sazka_id']]['overil'] = $book[$row['overena']]['jmeno']." (".$book[$row['overena']]['nick'].")";
				}
				else
					$sazka[$row['sazka_id']]['overil'] = '';
			}

			if (isset($book[$row['proplatil_bookmaker']])) {
				$sazka[$row['sazka_id']]['proplatil'] = $book[$row['proplatil_bookmaker']]['jmeno']." (".$book[$row['proplatil_bookmaker']]['nick'].")";
			}

			$sazka_poradi[$row['udalost_id']][$row['typ_id']][$row['podtyp_id']][] = $row['sazka_id'];

			if($row['live'] == 1) {
				$sql = "SELECT home_team,away_team FROM live WHERE sazka_id=".$row['sazka_id'];
				$res59 = $this->dbGame->query($sql);
				DbUtil::testResult($res59,'Nepodarilo se provest dotaz: vyber sportu a udalosti');
				if ($row59 = $res59->fetchRow()) {
					$sazka[$row['sazka_id']]['text'] = $row59['home_team'] .' - '. $row59['away_team']. ' '. $sazka[$row['sazka_id']]['text'];
				}
			}
		}

		if(!isset($sazka[$row['sazka_id']]['kurzy'][$row['poradi']]['platny_od'])) {
			$sazka[$row['sazka_id']]['kurzy'][$row['poradi']]['platny_od'] = $row['platny_od'];
			//$sazka[$row['sazka_id']]['kurzy'][$row['poradi']]['zruseny'] = $row['zruseny'];
		}
		$sazka[$row['sazka_id']]['max_poradi'] = (!isset($sazka[$row['sazka_id']]['max_poradi']) || $sazka[$row['sazka_id']]['max_poradi']<$row['poradi']?$row['poradi']:$sazka[$row['sazka_id']]['max_poradi']);

		$sazka[$row['sazka_id']]['kurzy'][$row['poradi']]['sloupec'][$row['sloupec_id']] = $row['kurz'];

		if(!isset($sloupec[$row['sloupec_id']])) {
			$r = $preklad->selectData("WHERE lang_id=1 AND index_pole='".Help::Slash($row['nazev'])."'");
			if (!($row2 = $r->fetchRow()) || mb_strlen($row2['text']) <= 0)
				$row2['text'] = $row['nazev'];
			$sloupec[$row['sloupec_id']] = $row2['text'];
		}
	}

	if(isset($_REQUEST['propojene']) && !isset($_POST['filtr']) && $_REQUEST['zobrazeni'] == 'edit') {
		$this->filtrURL = '&zobrazeni=edit&propojene=1&udalost=0&filtr=1&sazka_id='.$_REQUEST['sazka_id'];
	}
	else if(isset($_REQUEST['propojene']) || $this->showCombinations) {
		$this->filtrURL = 'sazka_id='.$_REQUEST['sazka_id'].'&udalost=0&propojene=1&filtr=1&zobrazeni='.$_REQUEST['zobrazeni'];
	}

	$feedback = UiUtil::printMessages($this->messages).UiUtil::printErrors($this->errors);
	$this->vrat .= $feedback;

	$this->vrat .= '
		<form
			method="post"
			onsubmit="if(submitProve==false) return false;"
			action="?section='.$this->section.'&'.$this->filtrURL.'"
			id="formSazkyFiltr">
		<!--<div>
				'.I18n::tr('Detail view').'
			<input
				type="radio"
				onclick="submitProve = true;this.form.submit();"
				name="zobrazeni"
				'.(!isset($_REQUEST['zobrazeni']) || $_REQUEST['zobrazeni'] == "edit"?"checked=\"checked\"":"").'
				class="no"
				value="edit"
			/>
			'.I18n::tr('List view').'
			<input
				type="radio"
				onclick="submitProve = true;this.form.submit();"
				class="no"
				name="zobrazeni"
				'.(isset($_REQUEST['zobrazeni']) && $_REQUEST['zobrazeni'] == "view"?"checked=\"checked\"":"").'
				value="view"
			/>
		</div>-->
			<table class="table-filter" style="margin-bottom: 0; border-bottom: none;">';

		$this->vrat .= '
			<tr>
				<td colspan="4">
					<table class="table-filter" style="border: none;">
						<tr>
							<th>'.I18n::tr('Sport').'</th>
							<td>
								<select
									name="sport_opt"
									class="filter-multiple" onchange="loadEventOptsForSport(this, \'[name=\\\'udalost[]\\\']\')">
									'.$htmlSports.'
								</select>
								<!--<input type="checkbox"
									'.(isset($_REQUEST['live'])?"checked='checked'":"").'
									class="no"
									name="live"
								/>
								'.I18n::tr('Live events').'-->
							</td>
							<th title="'.Help::Html(I18n::tr('bet_valid_from_hint')).'">'.I18n::tr('Valid_from').':</th>
							<td>
								<input
									onchange="loadTypeOpts()"
									type="text"
									name="od_start"
									style="font-size:10px;width:100px"
									id="od_start"
									class="sinput3 dateTime"
									value="'.(isset($_REQUEST['od_start'])?Help::Html($_REQUEST['od_start']):"").'"
								/>
								<img src="images/ico/calendar.gif" class="calendar-icon">
							<th>&ndash;</th>
							<td>
								<input
									onchange="loadTypeOpts()"
									type="text"
									name="od_end"
									id="od_end"
									style="font-size:10px;width:100px"
									class="sinput3 dateTime"
									value="'.(isset($_REQUEST['od_end'])?Help::Html($_REQUEST['od_end']):"").'"
								/>
								<img src="images/ico/calendar.gif" class="calendar-icon">
							</td>
							<th>'.I18n::tr('bet_id').'</th>
							<td>
								<input
									onchange="loadTypeOpts()"
									type="text"
									style="width:100px"
									name="sazka_id"
									value="'.(isset($_REQUEST['sazka_id'])?Help::Html($_REQUEST['sazka_id']):"").'"
								/>
							</td>
						</tr>
						<tr>
							<th>'.I18n::tr('order').'</th>
							<td>
								<input type="radio" name="order" 
									'.(!isset($_REQUEST['order']) || $_REQUEST['order'] == "asc" ? "checked=\"checked\"" : "").'
									value="asc" />'.I18n::tr('order_asc').'
								<br />
								<input type="radio" name="order" 
									'.(isset($_REQUEST['order']) && $_REQUEST['order'] == "desc" ? "checked=\"checked\"" : "").'
									value="desc" />'.I18n::tr('order_desc').'
							</td>
							<th title="'.Help::Html(I18n::tr('bet_valid_to_hint')).'">'.I18n::tr('Valid_to').':</th>
							<td>
								<input
									onchange="loadTypeOpts()"
									type="text"
									name="do_start"
									style="font-size:10px;width:100px"
									id="do_start"
									class="sinput3 dateTime"
									value="'.(isset($_REQUEST['do_start'])?Help::Html($_REQUEST['do_start']):"").'"
								/>
								<img src="images/ico/calendar.gif" class="calendar-icon">
							<th>&ndash;</th>
							<td>
								<input
									onchange="loadTypeOpts()"
									type="text"
									name="do_end"
									id="do_end"
									style="font-size:10px;width:100px"
									class="sinput3 dateTime"
									value="'.(isset($_REQUEST['do_end'])?Help::Html($_REQUEST['do_end']):"").'"
								/>
								<img src="images/ico/calendar.gif" class="calendar-icon">
							</td>
							<th>
								'.I18n::tr('match_alias').'
							</th>
							<td>
								<input
									onchange="loadTypeOpts()"
									type="text"
									style="width:100px"
									name="alias"
									value="'.(isset($_REQUEST['alias'])?Help::Html($_REQUEST['alias']):"").'"
								/>
							</td>
							<th>
								'.I18n::tr('bet_alias').'
							</th>
							<td>
								<input
									onchange="loadTypeOpts()"
									type="text"
									style="width:100px"
									name="match"
									value="'.(isset($_REQUEST['match'])?Help::Html($_REQUEST['match']):"").'"
								/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<th>'.I18n::tr('events').'</th>
				<th>'.I18n::tr('ticket_status').'</th>
				<th>'.I18n::tr('bet_type').'</th>

				<th>'.I18n::tr('group_combinations').'
					<input
						onchange="loadTypeOpts()"
						type="checkbox"
						name="showCombinations"
						onClick = "if (this.checked==true) this.value=1; else this.value=0"
						'.($this->showCombinations ? 'checked="checked"' : '').'
						value="'.($this->showCombinations ? '1' : '0').'"
						/>
				</th>
			</tr>
			<tr>
				<td rowspan="2" style="vertical-align:top;">
					<select onchange="loadTypeOpts()" name="udalost[]" multiple="multiple" size="26" class="sel-1" style="width:350px;">
						'.$htmlEvents.'
					</select>
				</td>';

		$this->vrat .= '
				<td class="vlgntp">
					<select onchange="loadTypeOpts()" class="sel-1 left" name="stav[]" size="9" multiple="multiple">
						<option value="0" '.((!isset($_REQUEST['stav']) || isset($_REQUEST['stav']) && in_array(0,$_REQUEST['stav'])?"selected=\"selected\"":"")).'>
						'.I18n::tr('all').'
						</option>
						<option value="1" '.((isset($_REQUEST['stav']) && in_array(1,$_REQUEST['stav']))?"selected=\"selected\"":"").'>
						'.I18n::tr('bet_status_current').'
						</option>
						<option value="2" '.(isset($_REQUEST['stav']) && in_array(2,$_REQUEST['stav'])?"selected=\"selected\"":"").'>
						'.I18n::tr('bet_status_unevaluated').'
						</option>
						<option value="3" '.(isset($_REQUEST['stav']) && in_array(3,$_REQUEST['stav'])?"selected=\"selected\"":"").'>
						'.I18n::tr('bet_status_evaluated').'
						</option>
						<option value="4" '.(isset($_REQUEST['stav']) && in_array(4,$_REQUEST['stav'])?"selected=\"selected\"":"").'>
						'.I18n::tr('bet_status_confirmed').'
						</option>
						<option value="500" '.(isset($_REQUEST['stav']) && in_array(500,$_REQUEST['stav'])?"selected=\"selected\"":"").'>
						'.I18n::tr('bet_status_suspended').'
						</option>
						<option value="501" '.(isset($_REQUEST['stav']) && in_array(501,$_REQUEST['stav'])?"selected=\"selected\"":"").'>
						'.I18n::tr('bet_status_suspended_br').'
						</option>
						<option value="6" '.(isset($_REQUEST['stav']) && in_array(6,$_REQUEST['stav'])?"selected=\"selected\"":"").'>
						'.I18n::tr('bet_status_canceled').'
						</option>
						<option value="8" '.(isset($_REQUEST['stav']) && in_array(8,$_REQUEST['stav'])?"selected=\"selected\"":"").'>
						'.I18n::tr('bet_status_paid_out').'
						</option>
					</select>
				</td>
				<td class="vlgntp" colspan="2" rowspan="2" style="width:480px;">
					'.$typ.'
				</td>
			</tr>

			<tr>
				<td style="vertical-align:bottom;padding-bottom:20px;">
					<input type="submit" onclick="submitProve = true;" name="filtr" class="inputs" value="'.I18n::tr('apply_bet_filter').'" />
				</td>
			</tr>
		</table>
	';

	if (!empty($nomenu))
		$this->vrat .= $this->getActionBox();
	else
		$this->vrat .= $this->getMassManipButtons();

	$this->vrat .= $this->lister->getOutput(null, 0, array('formId' => 'formSazkyFiltr', 'jsPreSubmit' => "submitProve = true;\n"));


	if(isset($_GET['propojene']) && isset($_GET['sazka_id']))
		$this->vrat .= '<br /><h3>Kombinované sázky</h3>';

	foreach ($help_ar as $k => $h) {
		$this->vrat .= '<tr><td><input type="hidden" name="uall[]"  value="'.$k.'" />';
   }
		
	$this->vrat .= $this->BetForm($sazka,$sloupec);
	
	$this->vrat .= $this->lister->getOutput(null, 1, array('formId' => 'formSazkyFiltr', 'jsPreSubmit' => "submitProve = true;\n"));

	$this->vrat .= '<input type="hidden" name="pocet" value="'.(isset($_POST['pocet']) ? $_POST['pocet'] : 0 ).'" /></form>
					<a name="bo"></a>';
	$this->vrat .= '<script language="Javascript" type="text/javascript">'.$this->jsscript.'</script>';
  }


/**
 * Vypis formulare
 * @param array $sazka pole sazek
 * @param string $sloupec nazvy sloupcu
 * @param bool $nomenu jeslti se maji zobrazovat tlacitka
 * @return string
 */
public function BetForm(array $sazka, array $sloupec, $nomenu=false) {
	static $xzv = 0;
	$colspan = 0;
	$vrat = $stav = $info = $js_script = "";
	$x = 1;
	$podtyp = array();
	$sql = "SELECT podtyp_id,interni_nazev FROM podtyp";
	$res = $this->dbGame->query($sql);
	if(DB::isError($res))
		throw new ExHandler('Nepodarilo se provest dotaz: vyber bet_settings',"admin_ex_db");

	while($row = $res->fetchRow()){
		$podtyp[$row['podtyp_id']] = $row['interni_nazev'];
	}

	$zobrazeni = (!isset($_REQUEST['zobrazeni']) || $_REQUEST['zobrazeni'] == "edit"?"edit":"view");

	if	(count($sazka)==0) {
		$this->vrat .= UiUtil::printWarnings(I18n::tr('warn_no_bets_found'));
		return ;
	}

/* listing bets begin */
	$vrat .= "<script type=\"text/javascript\">\nvar complementColumns = [];\n</script>\n";
	$betIndex = 0;
	$sazkaTicket = new SazkaTicket();
	foreach ($sazka as $k => $h) {
		$betIndex++;
		$min = $max = $vyh_min = $vyh_max = 0;

		$min = $h['kurz_min'];
		$max = $h['kurz_max'];
		$vyh_min = $h['vyhernost_min'];
		$vyh_max = $h['vyhernost_max'];

		$stav = $this->getBetStatus($h);
		$info = '';

		$combinationsInfo = $this->printBetCombinations($zobrazeni, $k);


		if ( empty($h['proplatil']) )
			$h['proplatil'] = '';

		$brTextButton = '';
		if (($h['betradar_bet_id'] != '')) {
			if ( $h['betradar_autoupdate'] == 1 ) {
				$brTextButton .= '<span class="fltrt txtrt"><input
					type="submit"
					class="smaller"
					name="sazka[betradar_autoupdate]['.$k.']"
					value="'.I18n::tr('disable_betradar_update').'" /></span>';
			} else {
				$brTextButton .= '<span class="fltrt txtrt"><input
					type="submit"
					class="smaller"
					name="sazka[betradar_autoupdate]['.$k.']"
					value="'.I18n::tr('enable_betradar_update').'" /></span>';
			}
		}

		$sazka_id = $k;
		$typ_id = $h['typ_id'];
		$tretina = array();
		$polovina = array();
		$vysledek = '';
		$vysledek2t3t  = '';
		if(!isset($typ_id_control)) $typ_id_control = '';

		if ($typ_id == 19 || $typ_id == 22) {
			$vysledek = '
			<table class="score">
				<tr>
					<th>FT '.$typ_id_control.'</th>
					<td>
						<input
							type="text"
							class="sinput2"
							onkeyup="InsertDv(this.value,this);"
							style="width:50px"
							name="result['.$sazka_id.'][ft]"
							value="'.(isset($h['ft'])?Help::Html($h['ft']):'').'" />
					</td>
				</tr>';

			if ($sazka[$sazka_id]['sport_id']== 1001 || $sazka[$sazka_id]['sport_id']== 1035 || $sazka[$sazka_id]['sport_id']== 1020) {
				$vysledek .= '
				<tr>
					<th>HT</th>
					<td>
						<input
							type="text"
							class="sinput2"
							style="width:50px"
							name="result['.$sazka_id.'][ht]"
							onkeyup="InsertDv(this.value,this);"
							value="'.(isset($h['ht'])?Help::Html($h['ht']):'').'" />
					</td>
				<tr/>';
			}
			
			$vysledek .= '
				<tr>
					<th>OT</th>
					<td>
						<input
							type="text"
							class="sinput2"
							onkeyup="InsertDv(this.value,this);"
							style="width:50px"
							name="result['.$sazka_id.'][ot]"
							value="'.(isset($h['ot'])?Help::Html($h['ot']):'').'" />
					</td>
				</tr>';

			if ($sazka[$sazka_id]['sport_id']== 1011 || $sazka[$sazka_id]['sport_id']== 1022) {
				$vysledek .= '
				<tr>
					<th>1T</th>
					<td>
						<input
							type="text"
							class="sinput2"
							style=""
							name="result['.$sazka_id.'][1t]"
							onkeyup="InsertDv(this.value,this);"
							value="'.(isset($h['1t'])?Help::Html($h['1t']):'').'" />
					</td></tr>';

				$vysledek2t3t = '
				<table class="score">
				<tr><th>2T</th>
					<td>
						<input type="text"
							class="sinput2"
							style="width:40px;font-size: 1.1em"
							name="result['.$sazka_id.'][2t]"
							onkeyup="InsertDv(this.value,this);"
							value="'.(isset($h['2t'])?Help::Html($h['2t']):'').'" />
					</td>
					<th>3T</th>
					<td>
						<input type="text"
							class="sinput2"
							style="width:40px;font-size: 1.1em"
							name="result['.$sazka_id.'][3t]"
							onkeyup="InsertDv(this.value,this);"
							value="'.(isset($h['3t'])?Help::Html($h['3t']):'').'" />
					</td>
				</tr></table>
				';
			}
			$vysledek  .= '</table>';
		}

		$sazkaTicket = new SazkaTicket();
		
		$betActions = '';
		if ($h['overena'] == 0 && $h['status'] == 3) {
			$betActions .= '
				<input
					type="submit"
					name="sazka[clear_rate]['.$k.']"
					class="sinput3"
					onclick="submitProve = true;if(!confirm(\'Opravdu chcete vratit sázku do stavu nevyhodnocená?\'))return false;"
					value="Nevyhodnocená" />';
			$betActions .= '<br/>';
		}

		if($h['status'] != 1 && $h['proplacena'] != 1) {
			$betActions .= $this->printInputConfirm('sazka[zrusit]['.$k.']','Do you really want to cancel the bet?','bet_change_status_cancel');
			
		}

		if($h['status'] == 1 && $h['proplacena'] != 1) {
			$betActions .= $this->printInputConfirm('sazka[obnovit]['.$k.']','Do you really want to renew the bet?','bet_change_status_renew');
			$betActions .= '<br/>';
		}

		if (It6_Date::toTimestamp($h['platna_do']) > time() && $h['status'] == 0) {
			$betActions .= '<br/>';
			$betActions .= $this->printInputConfirm('sazka[ukoncit]['.$k.']','Do you really want to suspend the bet?','bet_change_status_suspend');
			$betActions .= '<br/>';
		}

		if (It6_Date::toTimestamp($h['platna_do']) > time() && $h['status'] == 2) {
			$betActions .= $this->printInputConfirm('sazka[povolit]['.$k.']','Do you really want to activate the bet?','bet_change_status_activate');
			$betActions .= '<br/>';
		}

		if ( ( ( $h['overena'] == 0 && $h['status'] == 3) ||
				(FALSE && It6_Date::toTimestamp($h['platna_do']) <= time() && ($h['status'] == 0 || $h['status'] == 2)) )
					&& $h['proplacena'] != 1 && $_SESSION['superbookmaker'] == 1)  {
			$betActions .= $this->printInputConfirm('sazka[overit]['.$k.']','Do you really want to confirm the bet?','bet_change_status_confirm');
			$betActions .= '<br/>';
		}

		if ($h['overena'] != 0 && $h['status'] == 3 && $h['proplacena'] != 1 && $_SESSION['superbookmaker'] == 1) {
			$betActions .= $this->printInputConfirm('sazka[neoverit]['.$k.']','Do you really want to cancel the confirmation the bet?','bet_change_status_cancel_confirmation');
			$betActions .= '<br/>';
		}

		if ($h['proplacena'] == 1 && $h['status'] != 0) {
			$betActions .= $this->printInputConfirm('sazka[vratit]['.$k.']','Do you really want to reopen the bet? All ticket will be canceled.','bet_change_status_revert_to_unevaluated');
			$betActions .= '<br/>';
		}

		$vrat .= '
			<div class="bet-detail" id="bet'.$k.'">
				<a name="sazka'.$x.'"></a>
				<div class="head">
					<span class="bl">
						<input type="checkbox" class="checkboxBets no" name="sazka[all]['.$k.']" />
					</span>
					<span class="bl">
						'.$h['alias'].' - ';

						if (isset($h['alias_new'])) $vrat .= $h['alias_new'].' - ';

						if(count($sazkaTicket->getTicketData('s.sazka_id = '.$k, '', 0, 1, 't.ticket_id ASC')) > 0)
							$vrat .= '<a href="/?section=148&betId='.$k.'" target="_blank">'.$k.'</a>';
						else
							$vrat .= $k;

				$vrat .= '
					</span>
					<span class="bl">
						'.$h['sport'].' - '.I18n::tr($h['regionName']).' - '.$h['udalost'].' - '.$h['typ'].'
					</span>
					<span class="bet-status" style="color: '.$stav['color'].';" >'.$stav['name'].'</span>';

		$vrat .= '</div> <!-- bet-head -->';
		
		$vrat .= '
			<div class="main">
				<div class="title">
					<input
						type="text"
						name="sazka['.$k.'][text]"
						id="sazka['.$k.'][text]"
						class="bet-text bet-input '.($h['podtyp_radek'] == 0?"mandatory":"").'"
						value="'.Help::Html($h['text']).'"
					/><br/>'.$this->combCount.'
					<!--<br/>'.(!$nomenu? UIUtil::printCatalogIcon('TEAM','sazka['.$k.'][text]','sazka['.$k.'][ticket_text]') : "" ).'-->
					<span class="fltrt">'.i18n::tr('Valid_to').
					':
					<input
						type="text"
						id="sazka['.$k.'][platna_do]"
						style="width:120px"
						name="sazka['.$k.'][platna_do]"
						class="mandatory dateTime"
						value="'.Help::Html($h['platna_do']).'"
					/>
					<img src="images/ico/calendar.gif" class="calendar-icon" />
					</span>
				</div> <!-- title -->
				<div class="rates">';
				
					$this->columnIdsOpts = '';
					foreach ($h['kurzy'][$h['max_poradi']]['sloupec'] as $colId => $colIdVal) {
						$this->columnIdsOpts .= '<option value="'.$colId.'">'.$sloupec[$colId].'</option>';
					}
					
					$rateTable = $this->printBetRates($h, $k, $sloupec, $stav['name'], $nomenu, $zobrazeni, $betIndex);
					$betColsCount = count($h['kurzy'][$h['max_poradi']]['sloupec']);

					if ($this->isBetMoreThan3Rates($h)) {
						$vrat .= i18n::tr('rate_table_detail_hint').'<br/>';
					} else {
						$vrat .= '<table class="'.($h['podtyp_radek'] == 1 ? "rates-col" : "rates-row").'">
									'.$rateTable.'
									</table>';
					}
					$liab = '<div class="liab">
						'.$vyh_min.' - '.$vyh_max.'
						<input
							type="text"
							id="vyhernost_'.$k.'"
							readonly="readonly"
							class="sinput3" style="width:30px"
						/>
					</div> <!-- liab -->';

				 if ($this->isStatusActiveOrSuspended($stav))
					$vrat .= $liab;

				$vrat .= '
						<input
							type="hidden"
							id="vyhernost_ctrl_'.$k.'"
							value="'.$vyh_min.'"
						/>
						<input
							type="hidden"
							id="rateToUpdate_'.$k.'" value=""
						/>
						<input
							type="hidden"
							id="colsCount_'.$k.'"
							value="'.$betColsCount .'"
						/>
						<input
							type="hidden"
							name="resultColumns['.$k.']"
							id="resultColumns['.$k.']"
							value=""
						/>';
				
				$vrat .= '</div> <!-- rates -->';					
					$vrat .= '<div class="score">';

					if($typ_id == 19 || $typ_id == 22) {
						$vrat .= $vysledek;

					} else {
						$vrat .= '<div class="score-wrapper"><span class="score2">'.( ($h['score']=='' || $h['score']=='-') ? '-:-' : $h['score']).'</span></div>';
						
						if ($stav['name']=='Vyhodnocená' || $stav['name']=='Neyhodnocená')
							$vrat .= '<input class="smaller" type="submit" name="vysledek['.$k.'][setScore]" value="'.I18n::tr('set_score').'" onclick="var n=prompt(\''.I18n::tr('Do you really want to change the score?').'\',\''.$h['score'].'\');if(n){ this.value=n;return true;} else return false;"/>';
					}

					if (!$this->isStatusActiveOrSuspended($stav))
						$brTextButton = ($h['betradar_autoupdate'] ? '<span class="fltrt txtrt">(Betradar)</span>' : '');
					
					$vrat .= '</div> <!-- score -->';					
					$vrat .= '<div class="expander">
					<button class="bet-toggler" id="b'.$k.'" onclick="return false">'.i18n::tr('+').'</button>
					</div> <!-- expander -->';
					$vrat .= '<div class="action">
					'.$brTextButton.'
				<br/>
						'.$betActions.'						
					</div>  <!-- action -->';					
					$vrat .= '<div class="update"> <!-- update bet -->';
					if ($h['betradar_autoupdate'] == 0) {
						$vrat .= '
						<!--<strong>'.I18n::tr('Update').'</strong>
						<input type="radio" value="edit" name="sazka['.$k.'][new_bet]"  />
						<strong>'.I18n::tr('New').'</strong>-->
						<input style="display:none" type="radio" checked="checked" value="new" name="sazka['.$k.'][new_bet]"  />
						<input
						type="submit"
						class="boldBtn"
						name="sazka[zmenit]['.$k.']"
						onclick="submitProve = true;"
						value="'.I18n::tr('edit_bet').'"
						/>';
					}
					else {
						$vrat .= '<input
						type="submit"
						class="boldBtn"
						name="sazka[update]['.$k.']"
						onclick="submitProve = true;"
						value="'.I18n::tr('edit_bet').'"
						/>';
					
					
					}
					$vrat .= '</div> <!-- update -->';
					$vrat .= '<div class="rest">
					<span>
					'.i18n::tr('RS').'
					<input
					type="text"
					name="sazka['.$k.'][risk_limit]"
					id="sazka['.$k.'][risk_limit]"
					value="'.intval($h['risk_limit']).'"
					class="bet-input sinput2"
					/>
					/ <strong>'.Help::roundPrice($h['risk_limit_balance']).'</strong>
					</span><br />
					'.$vysledek2t3t.'
					</div> <!-- rest -->';
		$vrat .= '</div> <!-- main -->';

		$vrat .= '</div> <!--bet-detail -->';
		$vrat .='<div id="bet-more'.$k.'" class="bet-more" style="display:none;">';
				
		if ($this->isBetMoreThan3Rates($h)) {
			$vrat .= '<table style="float:left" class="'.($h['podtyp_radek'] == 1 ? "rates-col" : "rates-row").'">
						'.$rateTable.'
						</table>';
		}

		$vrat .= '
			<table class="table-detail" style="float:left">
				<tr>
					<th>'.I18n::tr('bet_ticket_text').'</th>
					<td>
						<input
							type="text"
							style="width:200px;"
							name="sazka['.$k.'][ticket_text]"
							id="sazka['.$k.'][ticket_text]"
							class="sinput"
							value="'.Help::Html($h['ticket_text']).'"
						/>
					</td>
				</tr><tr>
					<th>'.I18n::tr('score_note').'</th>
					<td>
						<input
							 type="text"
							 class="bet-input" 
							 style="width:100px;" 
							 maxlength="20" 
							 name="sazka['.$k.'][score_note]" 
							 value="' . $h['score_note'] . '"
						 />
					</td>
				</tr>
				<tr>		
					<th>'.i18n::tr('Valid_from'). '</th>
					<td>
						<input
							type="text"
							id="sazka['.$k.'][platna_od]"
							style="width:140px"
							name="sazka['.$k.'][platna_od]"
							class="mandatory sinput3 dateTime"
							value="'.Help::Html($h['platna_od']).'"
						/>
						<img src="images/ico/calendar.gif" class="calendar-icon">
					</td>
				</tr>
				<tr>
					<th>'.i18n::tr('ako').'</th>
					<td>
						<input
							type="text"
							name="sazka['.$k.'][ako]"
							id="sazka['.$k.'][ako]"
							value="'.intval($h['ako']).'"
							class="sinput2"
						/>
					</td>
				</tr>
				<tr>
					<th>'.i18n::tr('individual_bet').'</th>
					<td>
						<input
							type="checkbox"
							name="sazka['.$k.'][jednoducha]" '.($h['jednoducha'] == 1?"checked=\"checked\"":"").' class="no"  />
					</td>
				</tr>
				<tr>
					<th>
						'.I18n::tr('min_max_rate').' 
					</th>
					<td>
						<strong>'.$min.' - '.$max.'</strong>
					</td>
				</tr>
				<tr>
					<th>'.i18n::tr('last_rate_change').'
					</th>
					<td>
						'.It6_Date::fromDb($h['kurzy'][$h['max_poradi']]['platny_od']).'&nbsp;
						<img
							src="_clip/kurzy.gif"
							alt="'.I18n::tr('Kurzy historie').'"
							class="img"
							onClick="window.open(
								\'/?section=278&betId='.$k.'\',
								\'myWindow\',
								\'status = 0, height = 800\'
							)"
						/>
					</td>
				</tr>
				<tr>
					<th>'.i18n::tr('notes').'</th>
					<td><a href="javascript:void(0);" onclick="window.open(\'/sazky.php?act=ichat&sazka='.$k.'\',\'\',\'height=300,width=600,scrollbars=yes\');" >
							<img src="_clip/chat.png" alt="Interní chat" class="img" />
						</a>
						<a href="javascript:void(0);" onclick="window.open(\'/sazky.php?act=echat&sazka='.$k.'\',\'\',\'height=300,width=600,scrollbars=yes\');">
							<img src="_clip/chat_ext.png" alt="Externí info" class="img" />
						</a>
					</td>
				</tr>';
			if (!$this->isStatusActiveOrSuspended($stav)) {
				$vrat .= '<tr class="red-highlight">
					<th>'.i18n::tr('multiplicate_odd_constant').'</th>
					<td>
						<select name="sazka[mult_odd_col_id]['.$k.']">'.$this->columnIdsOpts.'</select>
						* <input class="shortest" type="text" name="sazka[mult_odd_count_orig]['.$k.']" title="Počet závodníků na stejném místě v dané sázce"/>
						/ <input class="shortest" type="text" name="sazka[mult_odd_count_result]['.$k.']" title="Počet závodníků na stejném místě"/>';

						$vrat .= $this->printInputConfirm('mult_odd','multiplicate_odd_constant_question','multiplicate_odd_constant');
					$vrat .= '</td>
				</tr>';
			}
			$vrat .= '</table>';

	$js_script .= 'valid_from[valid_from.length] = '.$k.';';
	$vrat .= '<table class="bet-insider">
					<tr>
					<td>'.$combinationsInfo.'
					<table class="table-detail"><tr><th>'.I18n::tr('Bet parent id').'</th><td>
						<input
							type="text"
							style="width:50px;"
							name="sazka['.$k.'][parent_id]"
							id="sazka['.$k.'][parent_id]"
							class="sinput3"
							value="'.($h['parent_id'] == 0 ? '' : Help::Html($h['parent_id'])).'"
							'.($this->isParentIdChangeAllowed($k) ? '' : 'disabled="disabled"').'
						/></td><td><strong>';
	$vrat .= (isset($sazka[$h['parent_id']]['text'])==true ? $sazka[$h['parent_id']]['text'] : I18n::tr('no-parent')).'</strong></td></tr>';
	$vrat .= '<tr><th>'.I18n::tr('Hranice/poznámka').'</th><td colspan="2">
						<input
							type="text"
							style="width:100px;"
							name="sazka['.$k.'][text_note]"
							id="sazka['.$k.'][text_note]"
							class="sinput3"
							value="'.(empty($h['text_note']) ? '' : Help::Html($h['text_note'])).'"
						/></td></tr></table>
								<!--Kombinace :
								<input
									type="text"
									name="sazka['.$k.'][kombinace]"
									id="sazka['.$k.'][kombinace]"
									style="width:500px"
									value=""
									class="sinput2"
								/>
								<input
									type="checkbox"
									name="sazka['.$k.'][kombinace_indv]"
									id="sazka['.$k.'][kombinace_indv]"
									value=""
									class="no"
								/>
								Indv. kombinace
								<input
									type="checkbox"
									name="sazka['.$k.'][kombinace_show]"
									checked="checked"
									id="sazka['.$k.'][kombinace_show]"
									value=""
									class="no"
								/>
								Zobrazit kombinaci
								<input
									type="checkbox"
									onclick="if(this.checked){return confirm(\'Opravdu chcete smazat kombinace?\');}"
									name="sazka['.$k.'][kombinace_delete]"
									id="sazka['.$k.'][kombinace_delete]"
									value=""
									class="no"
								/>
								Vymazat kombinace-->
							</td></tr></table>';
			$vrat .= '</td></tr></table><! -- bet-insider -->';
	
			$vrat .= '<div>
				Událost ID
				<input
					type="text"
					name="sazka['.$k.'][kombinace_udalost]"
					id="sazka['.$k.'][kombinace_udalost]"
					value=""
					class="sinput2"
				/>
				Trh ID
				<input
					type="text"
					name="sazka['.$k.'][kombinace_typ]"
					id="sazka['.$k.'][kombinace_typ]"
					value=""
					class="sinput2"
				/><input type="hidden" name="sazka['.$k.'][cas][]" class="no" value="19"  />
			<!--
				<strong>Datum aktualizovat:</strong>
				Zápas:
				<input type="checkbox" checked="checked" name="sazka['.$k.'][cas][]" class="no" value="19"  />
				Dvojitá šance:
				<input type="checkbox" checked="checked" name="sazka['.$k.'][cas][]" class="no" value="24"   />
				1.poločas:
				<input type="checkbox" checked="checked" name="sazka['.$k.'][cas][]" class="no" value="16"  />
				1. třetina:
				<input type="checkbox" name="sazka['.$k.'][cas][]" checked="checked" class="no" value="26"   />
				Asijský handicap:
				<input type="checkbox" checked="checked" name="sazka['.$k.'][cas][]" class="no" value="25"   />
				Více/méně:
				<input type="checkbox" checked="checked" name="sazka['.$k.'][cas][]" class="no" value="20"   />
				Přesný výsledek:
				<input type="checkbox" checked="checked" name="sazka['.$k.'][cas][]" class="no" value="23"   />
				Handicap:
				<input type="checkbox" checked="checked" name="sazka['.$k.'][cas][]" class="no" value="18"   /> <br />
				Vítěz zápasu:
				<input type="checkbox" checked="checked" name="sazka['.$k.'][cas][]" class="no" value="22"   />
				Postup:
				<input type="checkbox" checked="checked" name="sazka['.$k.'][cas][]" class="no" value="33"   />
				Vítěz poháru:
				<input type="checkbox" checked="checked" name="sazka['.$k.'][cas][]" class="no" value="37"   />
				Draw no bet:
				<input type="checkbox" checked="checked" name="sazka['.$k.'][cas][]" class="no" value="35"  />
				Asijský handicap hry:
				<input type="checkbox" checked="checked" name="sazka['.$k.'][cas][]" class="no" value="47"  />
				Asijský handicap sety:
				<input type="checkbox" checked="checked" name="sazka['.$k.'][cas][]" class="no" value="48"  />
				Více/méně hry:
				<input type="checkbox" checked="checked" name="sazka['.$k.'][cas][]" class="no" value="45"  />
				Více/méně sety:
				<input type="checkbox" checked="checked" name="sazka['.$k.'][cas][]" class="no" value="46"  />
				První set:
				<input type="checkbox" checked="checked" name="sazka['.$k.'][cas][]" class="no" value="97"  />-->
			
			
				'.I18n::tr('Created by'). ' ' .$h['bookmaker'].'
				'.($h['proplacena']?'; <span class="red">Proplacená:</span> '.$h['proplatil']:'').
				($h['typ_id']==23?', DRUH TRHU:<a href="?superb=1&section=b6&udalost='.$h['udalost_id'].'" target="_blank">'.$podtyp[$h['podtyp_id']].'</a>':'');

		$vrat .= '</div>';
		$vrat .= '<div class="cleaner"></div> ';
		
		$vrat .= '</div> <!--bet-more-info -->';

		$x++;
	} /* listing bets end */

//SANDBOX END//////////////////////////

	$vrat .= '
		<script>
			var valid_from = new Array();
			'.$this->jsscript
			.$js_script.'
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

			$("input[name=\"uall\\[\\]\"]").each(
				function(index,value) {
					var betId = $(this).val();
					SetVyhernost(betId, complementColumns[betId]);
				}
			);
		</script>';

	return $vrat;
}

	/* BEGIN TODO presunout tuto tridu nekam jinam */

	public function getBetStatus(Array $h) {
		$status = array();
		if (It6_Date::toTimestamp($h['platna_do']) > time() && $h['status'] == 0) {
			$status['name'] = "Aktuální";
			$status['color'] = "#d2f5b0";
		}	else if (It6_Date::toTimestamp($h['platna_do']) <= time() && ($h['status'] == 0 || $h['status'] == 2)) {
			$status['name']  = "Nevyhodnocená";
			$status['color'] = "#e3b7eb";
		}	else if($h['overena'] == 0 && $h['status'] == 3) {
			$status['name']  = "Vyhodnocená";
			$status['color'] = "#ffcd85";
		}	else if($h['overena'] != 0 && $h['status'] == 3 && $h['proplacena'] == 0) {
			$status['name']  = "Ověřená: ".$h['overil'];
			$status['color'] = "#fff494";
		} else if(It6_Date::toTimestamp($h['platna_do']) > time() && $h['status'] == 2) {
			$status['name']  = "Pozastavena";
			$status['color'] = "#fcbdbd";
		}	else if($h['status'] == 1) {
			$status['name']  = "Zrušená";
			$status['color'] = "#c9ccc4";
		}	else if($h['overena'] != 0 && $h['proplacena'] == 1) {
			$status['name']  = "Proplacená ".(empty($h['proplatil']) ? '' : $h['proplatil']);
			$status['color'] = "#c2dfff";
		}
		return $status;

	}

	public function printBetOddStatus($sloupec, $k, $k2, $h, $zobrazeni, $nomenu, $stav) {

		if (($stav == "Nevyhodnocená" || $stav == "Vyhodnocená") && !in_array($k2,$h['vysledek']) && !$nomenu) { //moznost kliknutim na nazev sloupce vybrat vysledek

			$x = '<button onclick="SetResultColumn('.$k.','.$k2.');return false;">&nbsp;'.$sloupec[$k2].'&nbsp;</button>';
		} else {  // pouze nazev sloupce
			$x = $sloupec[$k2];
		}
		return $x;
	}

	public function printOddInput($k,$k2,$h2,$xzv,$readOnly,$i) {
		if ('disabled' == $readOnly)
			$readOnlyHtml = 'disabled="disabled"';
		else if ('readonly' == $readOnly)
			$readOnlyHtml = 'readonly="readonly"';
		else
			$readOnlyHtml = '';
		return	'
			<input
				type="text" ' . $readOnlyHtml . '
				name="sazka['.$k.'][sloupec]['.$k2.']"
				id="sazka['.$k.'][sloupec]['.$k2.']"
				tabindex="'.($i.$xzv).'"
				onkeyup="fixDecimalComa(this); SetLastRateByLiabilty('.$k.', complementColumns['.$k.'], this, true);";
				onfocus="saveRateToEdit('.$k.', complementColumns['.$k.']); SetLastRateByLiabilty('.$k.', complementColumns['.$k.'])";
				class="bet-input rate-input"
				value="'.$h2.'"
			/>';
	}

	public function printInputConfirm($name,$msg, $val) {
		return '
			<input
				type="submit"
				name="'.$name.'"
				onclick="submitProve = true;if(!confirm(\''.I18n::tr($msg).'\'))return false;"
				class="sinput3"
				value="'.I18n::tr($val).'"
			/>';
	}

	public function printBetCombinations($zobrazeni, $sazka_id) {

		$combTab = '<table class="table-detail">';
		$combTab .= '<tr><th colspan="4">Kombinace</th></tr>';
		$combTab .= '<tr>
						<th>ID</th>
						<th class="border-left">'.i18n::tr('alias').'</th>
						<th class="border-left">'.i18n::tr('bet_name').'</th>
						<th class="border-left">'.i18n::tr('bet_type').'</th>
						<th class="border-left">'.i18n::tr('bet_show_comb').'
							<input type="checkbox" onclick="$(\'.combShow_'.$sazka_id.'\').attr( {checked  : this.checked});" />
						</th>
						<th class="border-left">'.I18n::tr('dont_update_valid_to').'</th>
					</tr>';

		$corrBets = It6_Models_Bet::readRealCorrelatedBetsDetail($sazka_id,TRUE);
		$corrBetsIds = array();
		if (!empty($corrBets)) {
			foreach ($corrBets as $cb) {
				$corrBetsIds[] = $cb['id'];
				$combTab .= '<tr>
								<td>'.$cb['id'].'</td><td class="border-left">'.$cb['alias'].'</td>
								<td class="border-left">'.$cb['text'].'</td>
								<td class="border-left">'.i18n::tr($this->betTypeNames[$cb['type']]['name']).'</td>';
								
				$combTab .= '<td class="border-left"><input type="checkbox" class="combShow_'.$sazka_id.'" name="kombinace_single_show['.$sazka_id.']['.$cb['id'].']" '.(($cb['kombinace_show']==1) ? 'checked="checked"' : '').'" /></td>';
				$combTab .= '<td class="border-left"><input type="checkbox" class="combNoValidTo_'.$sazka_id.'" name="kombinace_single_no_valid_to['.$sazka_id.']['.$cb['id'].']" value="1" ' . (empty($cb['update_platna_do']) ? 'checked="checked" ' : '') . '/></td></tr>';
			}
			$combTab .= '</table>';

			$combCount = count($corrBets);
			$propojene = '
				<a target="_blank" href="?section='.$this->section.'&zobrazeni='.$zobrazeni.'&propojene=1&udalost=0&filtr=1&sazka_id='.$sazka_id.'">
					(+'.$combCount.')
				</a>';
		} else {
			$corrBets = NULL;
			$combCount = 0;
			$propojene = '0';
			$combTab = '';
		}

		$vrat = 'Kombinace '.$propojene.': <input
							type="text"
							name="sazka['.$sazka_id.'][kombinace]"
							id="sazka['.$sazka_id.'][kombinace]"
							style="width:500px"
							value="'.(empty($corrBetsIds)==true ? '' : implode(';', $corrBetsIds).';').'"
							class="sinput2"
						/>';
		$vrat .= UiUtil::printCatalogIcon('COMB','sazka['.$sazka_id.'][kombinace]');

		$this->combCount = $propojene;
		if ($combCount)
			$vrat .= $combTab;
		return $vrat;
	}

	public function printBetRates($h,$k,$sloupec,$stav,$nomenu,$zobrazeni,$betIndex) {

		$this->jsscript .= 'vyhernost['.$k.'] = new Object();';
		$vrat = '';

		$complementCols = It6_Models_BetSubtype::getComplementColumns($h['podtyp_id']);
		$vrat .= "<script type=\"text/javascript\">\ncomplementColumns[$k] = [];\n";
		foreach ($complementCols as $colId => $complCols) {
			$vrat .= "complementColumns[$k][$colId] = [" . implode(',', $complCols) . "];\n";
		}
		$vrat .= "</script>\n";

		if($h['podtyp_radek'] == 1) { //sloupcovy vypis

			$tr = array();
			$y = 0;
			$colspan = 0;
			$i = 0;
			$xzv = 0;
		//	$betColsCount = count($h['kurzy'][$h['max_poradi']]['sloupec']);
			foreach ($h['kurzy'][$h['max_poradi']]['sloupec'] as $k2 => $h2) {
				$i++;
				$xzv++;

				if ($h['betradar_autoupdate'])
					$readOnly = 'disabled';
				else if (!empty($complementCols[$k2]))
					$readOnly = 'readonly';
				else
					$readOnly = '';

				if(!isset($tr[$y])) {
					$tr[$y] = "";
				}
				$cl = ( ($i%2) ? 'odd':'even' );
				$cl .= (BetUtil::isBetWinning(array('sloupec_id'=>$k2,'vysledek' => $h['vysledek'])) ? ' odd-win' : '');
				$tr[$y] .= '
					<td class="txtrt '.$cl.'">'.
						$this->printBetOddStatus($sloupec,$k,$k2,$h,$zobrazeni,$nomenu,$stav).'
					</td>
					<td class="'.$cl.'">
						'.$this->printOddInput($k,$k2,$h2,$xzv,$readOnly,$betIndex).
					'</td>';

				$y++;

				if ($y == $h['podtyp_max']) {
					if ($colspan == 0)
						$colspan = $y*2;
					$y = 0;
				}

				$this->jsscript .= 'vyhernost['.$k.']['.$k2.'] = 1;';
			}

			foreach($tr as $h3) {
				$vrat .= '<tr>'.$h3.'</tr>';
			}

		} else { //radkovy vypis

			$vrat .= '<tr class="cols">';

			$i = 0;

			foreach ($h['kurzy'][$h['max_poradi']]['sloupec'] as $k2 => $h2 ) {
				$i++;
				$cl = ( ($i%2) ? 'odd':'even' );
				$cl .= (BetUtil::isBetWinning(array('sloupec_id'=>$k2,'vysledek' => $h['vysledek'])) ? ' odd-win' : '');
				$vrat .= '
					<td class="'.$cl.'">'.
						$this->printBetOddStatus($sloupec,$k,$k2,$h,$zobrazeni,$nomenu,$stav).
					'</td>';

			}

			$vrat .= '</tr>';
			$vrat .= '<tr>';

			$i = 0;
			$xzv = 0;
			foreach ($h['kurzy'][$h['max_poradi']]['sloupec'] as $k2 => $h2 ) {
				$xzv++;
				$i++;

				if ($h['betradar_autoupdate'])
					$readOnly = 'disabled';
				else if (!empty($complementCols[$k2]))
					$readOnly = 'readonly';
				else
					$readOnly = '';

				$cl = ( ($i%2) ? 'odd':'even' );
				$cl .= (BetUtil::isBetWinning(array('sloupec_id'=>$k2,'vysledek' => $h['vysledek'])) ? ' odd-win' : '');
				$vrat .= '
					<td class="'.$cl.'">'.
						$this->printOddInput($k,$k2,$h2,$xzv,$readOnly, $betIndex).
					'</td>';

			$this->jsscript .= 'vyhernost['.$k.']['.$k2.'] = 1;';

			}

			$vrat .= '</tr>';

		}

		return $vrat;
	}


 /**
 * vyber dat z databaze
 * @return object
 */

  public function selectData($where=""){
    $sql = "
	    SELECT a.typ_id,b.sport_id,a.nazev,a.zobrazeno,b.vychozi,b.poradi
		FROM `typ` a
		LEFT JOIN typ_udalost b ON a.typ_id=b.typ_id
		".$where."
		ORDER BY b.typ_id,b.sport_id
	";

	$res = $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");
	return $res;
  }





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


	public function getActionBox() {
		$r = '
			<div class="actions">
				<input
					type="submit"
					name="result_set"
					onclick="submitProve = true;SetResSubmit();"
					value="Stanovit výsledky - Akt" />
				<input type="submit" name="result_aut_set"  onclick="submitProve = true;" value="Aut. vyhodnocení" />
				<input type="submit" name="result_set_all"  onclick="submitProve = true;SetResSubmit();" value="Vše" />
			</div>';
		return $r;
	}

	public function getMassManipButtons() {
			//Tlacitka na hromadnou manipulaci

			$vrat = '
				<table class="table-filter">
					<caption>'.I18n::tr('Actions').'</caption>
					<tr>
						<td>
							<input
								type="button"
								value="'.I18n::tr('bet_set_date').'"
								class="sinput3"
								onclick="SetDatum()"
							/>
							<img src="images/ico/calendar.gif" class="calendar-icon">
							'.I18n::tr('Valid from').'
							<input type="text" value="" id="valid_from" class="sinput3 dateTime" />
							<img src="images/ico/calendar.gif" class="calendar-icon">
							'.I18n::tr('Valid to').'
							<input type="text" value="" id="valid_to" class="sinput3 dateTime" />
							<img src="images/ico/calendar.gif" class="calendar-icon">
						</td>
					</tr>
					<tr>
						<td>
							<input
								type="checkbox"
								value="Označit vše"
								class="sinput3"
								onclick="CheckAll(this)"
							/>';

			$vrat .= $this->printInputConfirm('zrusit_all','Opravdu chcete oznaèené zrušit?','Zrušit označené');

			$vrat .= $this->printInputConfirm('obnovit_all','Opravdu chcete oznaèené obnovit?','Obnovit označené');
			$vrat .= '</td></tr><tr><td>';
			$vrat .= $this->printInputConfirm('ukoncit_all','Opravdu chcete oznaèené pozastavit?','Pozastavit označené');

			$vrat .= $this->printInputConfirm('povolit_all','Opravdu chcete oznaèené zrušit pozastavení?','Zrušit pozastavení označené');

			$vrat .= $this->printInputConfirm('overit_all','Opravdu chcete oznaèené ovìřit?','Ověřit označené');

			$vrat .= $this->printInputConfirm('neoverit_all','Opravdu chcete zrušit ověření?','Zrušit ověření označených');

			$vrat .= '</td><td>';

			$vrat .= '<input
									type="submit"
										onclick="submitProve = true"
										name="update_all"
										value="Aktualizovat vše" />';

			$vrat .= '</td></tr></table>';


			///////////////////////

			$vrat = '<div class="actions">
							<input
								type="button"
								value="'.I18n::tr('bet_set_date').'"
								class="sinput3"
								onclick="SetDatum()"
							/>
							<img src="images/ico/calendar.gif" class="calendar-icon">
							'.I18n::tr('Valid from').'
							<input type="text" value="" id="valid_from" class="sinput3 dateTime" />
							<img src="images/ico/calendar.gif" class="calendar-icon">
							'.I18n::tr('Valid to').'
							<input type="text" value="" id="valid_to" class="sinput3 dateTime" />
							<img src="images/ico/calendar.gif" class="calendar-icon">
					</div>
					<div class="actions">
							<input
								type="checkbox"
								class="sinput3"
								onclick="CheckAll(this)"
							/>'.I18n::tr('bet_select_all');

			$vrat .= $this->printInputConfirm('povolit_all',i18n::tr('question_activate_selected_bets'),'bet_activate_selected');
			$vrat .= $this->printInputConfirm('ukoncit_all',i18n::tr('question_suspend_selected_bets'),'bet_suspend_selected');
			$vrat .= $this->printInputConfirm('zrusit_all',i18n::tr('question_cancel_selected_bets'),'bet_cancel_selected');
			$vrat .= $this->printInputConfirm('obnovit_all',i18n::tr('question_renew_selected_bets'),'bet_renew_selected');
			$vrat .= $this->printInputConfirm('overit_all','question_confirm_selected_bets','bet_confirm_selected');
			$vrat .= $this->printInputConfirm('neoverit_all','question_cancel_confirmation_selected_bets','bet_cancel_confirmation_selected');

			$vrat .= '<input
									type="submit"
										onclick="submitProve = true"
										name="update_all"
										value="'.I18n::tr('bet_update_all').'" />';

			$vrat .= '<button class="all-bet-toggler" onclick="return false;">'.I18n::tr('expand_collapse_all').'</button>';
			
			$vrat .= $this->printInputConfirm('betradar_autoupdate_all','question_betradar_autoupdate_selected_bets','set_betradar_autoupdate_selected');
			$vrat .= $this->printInputConfirm('result_aut_set',i18n::tr('question_set_score_selected_bets'),'set_score_all');
			$vrat .= '<input type="hidden" id="result_set_hid" name="result_set_hid" value="" />';
			$vrat .= '</div>';
		return $vrat;
	}

  public function __destruct(){
  }

public function cleanUp() {
	if (!empty($this->updatedBets)) {
		foreach ($this->updatedBets as $betId => $_)
			It6_GlobalCache_Invalidator::invalidateSportsbookByBet($betId);
		It6_GlobalCache_Invalidator::invalidateSportMenuFrame();
	}
}


private function isParentIdChangeAllowed($betId) {
	$bet = $this->ws->Bet->getById($betId);
	
	if(
		$bet->alias == null
		|| $bet->realTypeId == null
	)
		return true;
	else
		return false;
}

	private function loadTypes($filterEvents, $request, $post) {
		$typ_ar = array();
		
		$ud_sp_sql = (
			empty($filterEvents) 
				? 
				'' : 'WHERE tv.udalost_id IN(' . implode(',', array_keys($filterEvents)) . ')'
		);
		
		#Vyber typu#
		
		if((isset($request['filtr']) || isset($request['result_set'])) && isset($post['udalost'])){
			$sql = "SELECT tv.nazev, tv.typ_id, 0 AS pocet FROM  view_typ_id tv ".$ud_sp_sql." GROUP BY tv.typ_id";
		} else
			$sql = "SELECT t.nazev, t.typ_id, 0 AS pocet FROM typ t";
			
		$res = $this->dbGame->query($sql);
		
		if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky sazky a typ',"admin_ex_db");
		
		$types = array();
		$typeNames = array();
				
		while ($row = $res->fetchRow()) {
			$types[$row['typ_id']] = array(
				'id' => $row['typ_id'],
				'name' => $row['nazev'],
			);
			$typeNames[$row['nazev']] = $row['nazev'];
		}
		
		if (!empty($types)) {
			$typeNames = It6_Models_Translator::translate(array_keys($typeNames), 1);
			
			foreach ($types as $id => $type) {
				$typ_ar[$id] = $typeNames[$type['name']];
				$types[$id]['translation'] = $typeNames[$type['name']];
			}
			$this->betTypeNames = $types;
		}
		return array(
			'typ_ar' => $typ_ar,
			'types' => $types
		);
	}
	
	private function getTypeFilterHTML($types, $where, $request, $post) {
		$typeBetCounts = array();
		$total = 0;

		if (!empty($types)) {
			$sql = 'SELECT s.typ_id, COUNT(DISTINCT s.sazka_id) AS c FROM sazky s
					JOIN udalost u ON u.udalost_id = s.udalost_id
					WHERE '.$where.' AND s.live = '.(isset($post['live']) ? 1 : 0).'
					AND s.typ_id IN ('.implode(',', array_keys($types)).') GROUP BY s.typ_id';
			$res = $this->dbGame->query($sql);
		
			while ($row = $res->fetchRow()) {
				$typeBetCounts[$row['typ_id']] = $row['c'];
				$total += $row['c'];
			}
		}
				
		$typ = 
					"<tr>
						<td colspan=\"6\">
							<input id=\"chAll\"
								type=\"checkbox\"
								name=\"typ[0]\" ".(!isset($request['typ'])?'checked="checked"':"")." />
							<label for=\"chAll\">".I18n::tr('All').' ('.$total.')</label>
						</td>
					</tr>';
		
		$help_num = 5;
				
		if (!empty($types)) {
			$row_count = 0;
			foreach ($types as $id => $type) {
				
				if (!empty($typeBetCounts[$id])) {
					if($row_count == 0) {
						$typ .= '<tr>';
					}
					$row_count++;
					$typ .= 
						'<td>
							<input class="betTypeChck" id="betTypeChck-'.$id.'"
								type="checkbox"
								name="typ['.$id.']" '.((isset($request['typ'][$id]) || !isset($request['typ'])) ?'checked="checked"':"").' />
							<label for="betTypeChck-'.$id.'"><span title="#'.$id.'">'.$type['translation'].' ('.$typeBetCounts[$id].')</span></label>
						</td>';
		
					if(($help_num%3) == 0 && $help_num !=3) {
						if (3-$row_count > 0) $typ .= '<td colspan="' . (3-$row_count) . '" style="width:' . intval(((3-$row_count) * 100)/3) . '%"></td>';
						$typ .= '</tr>';
						$row_count=0;
					}
		
					$help_num++;
				}
			}
			if (3-$row_count > 0 && $row_count != 0) $typ .= '<td colspan="' . (3-$row_count) . '" style="width:' . intval(((3-$row_count) * 100)/3) . '%"></td></tr>';
		}
		
		return $typ;
	}
	
	private function processFilterInput($request, $post, $get = array()) {
		$where = $where2 = '';
		$this->showCombinations = (isset($request['propojene']) && ($request['propojene'] == 1) );
				
		if(isset($request['sazka_id']) && mb_strlen($request['sazka_id']) > 0 && $this->showCombinations==false){
			$this->filtrURL .= 'sazka_id='.$request['sazka_id'].'&';
			$where .= "s.sazka_id=".intval($request['sazka_id'])." AND ";
		}
		
		if(isset($request['alias']) && mb_strlen($request['alias']) > 0 && $this->showCombinations==false){
			$this->filtrURL .= 'alias='.$request['alias'].'&';
			$where .= "s.alias=".intval($request['alias'])." AND ";
		}
		
		if(isset($request['match']) && mb_strlen($request['match']) > 0 && $this->showCombinations==false){
			$this->filtrURL .= 'match='.$request['match'].'&';
			$typAlias = substr($request['match'], -2);
			$betAlias = substr_replace($request['match'], '', -2);
			$bets = Webservice_Bet::getAllBetsByAlias($betAlias, $typAlias);
			$betIds = array();
			foreach ($bets as $bet) array_push($betIds, $bet->betId);
			if (!empty($bet)) $where .= "s.sazka_id IN (".implode(',', $betIds).") AND ";
			else $where .= "s.sazka_id IS NULL AND ";
		}
		
		if (isset($request['bookmaker']) && $request['bookmaker'] != 0) {
			$this->filtrURL .= 'bookmaker='.$request['bookmaker'].'&';
			$where .= "s.bookmaker_id=".intval($request['bookmaker'])." AND ";
		}
		
		if (isset($request['sport_opt']) && $request['sport_opt'] != 0) {
			$this->filtrURL .= 'sport_opt='.$request['sport_opt'].'&';
			$where .= "u.sport_id=".intval($request['sport_opt'])." AND ";
		}
		
		if (isset($request['live'])) {
			$this->filtrURL .= 'live=1&';
			$where .= "s.live=1  AND ";
		}
		
		foreach (array(
				'od_start' => 's.platna_od>=',
				'od_end' => 's.platna_od<=',
				'do_start' => 's.platna_do>=',
				'do_end' => 's.platna_do<='
		) as $k => $v) {
			if (isset($request[$k]) && It6_Date::checkFormat($request[$k])) {
				$dt = It6_Date::toDb($request[$k]);
				$this->filtrURL .= urlencode($k) . '=' . urlencode($request[$k]) . '&';
				$where .= "$v'$dt' AND ";
			}
		}
		
		if (isset($request['last'])) {
			$this->filtrURL .= 'last='.$request['last'].'&';
			$where .= "'".It6_Date::dbNow()."'>=date_add(s.platna_do,INTERVAL ".Help::Slash($request['last']).") AND '".It6_Date::dbNow()."'<=s.platna_do AND ";
		}
		
		if (isset($post['udalost']) && is_array($post['udalost']) && count($post['udalost']) > 0 && !in_array(0,$post['udalost'])) {
			$where .= 's.udalost_id in ('.implode(",",$post['udalost']).') and ';
			foreach($post['udalost'] as $h8) {
				$this->filtrURL .= 'udalost[]='.intval($h8).'&';
			}
		}
		
		if ( (isset($request['propojene']) && isset($get['sazka_id']) && !isset($post['filtr'])) ||
		( $this->showCombinations && isset($get['sazka_id']) ) ) {
			$where .="(s.sazka_id IN
					(select sazka2_id FROM sazka_kombinace WHERE sazka1_id=".intval($get['sazka_id'])." OR sazka2_id=".intval($get['sazka_id']).")
				OR s.sazka_id IN
					(select sazka1_id FROM sazka_kombinace WHERE sazka1_id=".intval($get['sazka_id'])." OR sazka2_id=".intval($get['sazka_id']).")
				) and ";
		}
		
		if(isset($request['typ']) && !isset($request['typ'][0])) {
			$where2 .= "(";
			foreach($request['typ'] as $k=>$h) {
				if(!isset($post['typ'][$k]) && isset($post['filtr'])) {
					unset($request['typ'][$k]);continue;
				}
				// $this->filtrURL .= 'typ['.$k.']=1&';
				$where2 .= "s.typ_id=".intval($k)." or ";
			}
			$where2 = substr($where2,0,-3);
			if (mb_strlen($where2) > 0)
				$where2 .= ")";
		}
		
		if(isset($request['stav']) && is_array($request['stav']) && !in_array("0",$request['stav'])){
			$where .= "(";
			foreach($request['stav'] as $h) {
				$this->filtrURL .= 'stav[]='.$h.'&';
		
				switch($h) {
					case 1:$where .= "(s.platna_do>'".It6_Date::dbNow()."' and s.status=0) or ";break;
					case 2:$where .= "(s.platna_do<='".It6_Date::dbNow()."' and (s.status=0 or s.status=2)) or ";break;
					case 3:$where .= "(s.overena=0 and s.status=3) or ";break;
					case 4:$where .= "(s.overena<>0 and s.proplacena<>1 and s.status=3) or ";break;
					case 500:$where .= "(s.platna_do>'".It6_Date::dbNow()."' and s.status=2 and COALESCE(s.status_ext, 0)<>1) or ";break;
					case 501:$where .= "(s.platna_do>'".It6_Date::dbNow()."' and s.status=2 and s.status_ext=1) or ";break;
					case 6:$where .= "s.status=1 or ";break;
					case 7:$where .= "(s.platna_do<='".It6_Date::dbNow()."' and (s.status=0 or s.status=2) and date_add(s.platna_do,INTERVAL 2 DAY)<='".It6_Date::dbNow()."') or ";break;
					case 8:$where .= "(s.proplacena=1) or ";break;
				}
			}
			$where = substr($where,0,-3);
			$where .= ") and";
		}
		$where = substr($where,0,-4);
		if(mb_strlen($where) < 1) $where = "1=1";
	
		return array(
			'where' => $where,
			'where2' => $where2
		);
	}

}