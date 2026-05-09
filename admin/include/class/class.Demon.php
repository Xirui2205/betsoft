<?php
/**
*
 * @package	service
 */

/**
 * demin pro ruzne periodicke akce
 * zpracovava pozadovane akce od klienta, vraci chybovy stav
 *
 * @package	main
 */

class Demon {

  /**
 * spojeni na databazi admin
 * @access private
 * @var DB
 */
  private  $dbGame = null;

 /**
 * spojeni na databazi betwarehouse
 * @access private
 * @var DB
 */
  private  $dbBetWare = null;

 /**
 * spojeni na databazi gamewarehouse
 * @access private
 * @var DB
 */
  private  $dbGameWare = null;

	/**
 * spojeni na databazi casgam
 * @access private
 * @var DB
 */
  private  $dbCasGam = null;

 /**
 * spojeni na databazi Session
 * @access private
 * @var DB
 */
  private  $dbSession = null;


/**
 * spojeni na databazi admin
 * @access private
 * @var DB
 */
 private  $db = null;

  /**
 * spojeni na databazi affiliate
 * @access private
 * @var DB
 */
 private $dbAf = null;

	/**
 * spojeni na databazi game replikacni
 * @access private
 * @var DB
 */
 private $dbGameRep = null;

/**
 * test pro zapsani do logo
 * @access public
 * @var string
 */
 public  $logtext = "";

/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param int $section id aktualni sekce
* @param PEAR::DB $db objekt spojeni s databazi
* @param PEAR::DB $dbGame objekt spojeni s databazi
*/
  public function __construct($section=0,$dbGme=null){

  $this->section =  $section;

	
	$this->dbGame = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
	if (DB::isError($this->dbGame)) {
	  throw new ExHandler($this->dbGame->getMessage(),"admin_ex_db");
	}
	$this->dbGame->setFetchMode(DB_FETCHMODE_ASSOC);
	$sql = "set names 'utf8'";
	$res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");


	$this->db = DB::connect(DATABASE ."://". MY_USER .":". MY_PASS ."@". MY_HOST ."/". MY_DB);
	if (DB::isError($this->db)) {
	 throw new ExHandler($this->db->getMessage(),"admin_ex_db");
	}
	$this->db->setFetchMode(DB_FETCHMODE_ASSOC);
	$sql = "set names 'utf8'";
	$res =& $this->db->query($sql);
	if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");


	$this->dbGameWare = DB::connect(WDATABASE ."://". WMY_USER .":". WMY_PASS ."@". WMY_HOST ."/". WMY_DB);
	if (DB::isError($this->dbGameWare)) {
	  throw new ExHandler($this->dbGameWare->getMessage(),"admin_ex_db");
	}
	$this->dbGameWare->setFetchMode(DB_FETCHMODE_ASSOC);
	$sql = "set names 'utf8'";
	$res =& $this->dbGameWare->query($sql);
	if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");



	$this->dbSession = DB::connect(SESDATABASE ."://". SESMY_USER .":". SESMY_PASS ."@". SESMY_HOST ."/". SESMY_DB);
	if (DB::isError($this->dbSession)) {
	  throw new ExHandler($this->dbSession->getMessage(),"admin_ex_db");
	}
	$this->dbSession->setFetchMode(DB_FETCHMODE_ASSOC);
	$sql = "set names 'utf8'";
	$res =& $this->dbSession->query($sql);
	if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");

	$this->dbBetWare = DB::connect(WDATABASE ."://". WMY_USER .":". WMY_PASS ."@". WMY_HOST ."/". WMY_DB);
	if (DB::isError($this->dbBetWare)) {
	  throw new ExHandler($this->dbBetWare->getMessage(),"admin_ex_db");
	}
	$this->dbBetWare->setFetchMode(DB_FETCHMODE_ASSOC);
	$sql = "set names 'utf8'";
	$res =& $this->dbBetWare->query($sql);
	if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");


	$dbPlugin = new Zend_Controller_Plugin_DbPLugin();
	$db = $dbPlugin->initDbConnection('db', Zend_Controller_Plugin_DbPLugin::CONFIG_MAIN, true);
	$dbAdmin = $dbPlugin->initDbConnection('admindb', Zend_Controller_Plugin_DbPLugin::CONFIG_ADMIN, false);
	Zend_Registry::set('db', $db);
	Zend_Registry::set('zdb_game', $db);
	
  }


/**
 *  Testovaci
 */
public function test() {

}


/**
 *  Negativni balance nebo spatny zustatek
 *  @deprecated
 */
public function negativeBalance() {
	$ob = new StatistikyUzivatelAkce2();
	$ob->runAction(2);
}


/**
 *  Tiket vikendu
 *  @deprecated
 */
public function weekTicket(){

	$sth4 = $this->dbGame->prepare(
		"SELECT e.mena_id,e.kurz ". 
		"FROM mena_kurz e ".
		"ORDER BY timestamp DESC ". 
		"LIMIT 20");
	
	if (PEAR::isError($sth4))
		throw new ExHandler(
			$sth4->getMessage().'Nepodarilo se nacist week_month_winners',"admin_ex_db");
	$res4 =& $this->dbGame->execute($sth4);
	if (PEAR::isError($res4))  {
		throw new ExHandler(
			$res4->getMessage().'Nepodarilo se  nacist week_month_winners',"admin_ex_db");}
	
	$mena = array();

	while ($row4 =& $res4->fetchRow()){
		if(!isset($mena[$row4['mena_id']])) $mena[$row4['mena_id']] = $row4['kurz'];
	}

	$sth4 = $this->dbGame->prepare(
		"SELECT UNIX_TIMESTAMP(date_to) AS date_to ".
		"FROM week_month_winners ".
		"WHERE type='w' ".
		"ORDER BY UNIX_TIMESTAMP(date_to) desc ".
		"LIMIT 1");
	if (PEAR::isError($sth4))
		throw new ExHandler($sth4->getMessage().'Nepodarilo se nacist week_month_winners',"admin_ex_db");
		
	$res4 =& $this->dbGame->execute($sth4);
	if (PEAR::isError($res4))
		throw new ExHandler($res4->getMessage().'Nepodarilo se  nacist week_month_winners',"admin_ex_db");

	$lastDate = false;

	if ($row4 =& $res4->fetchRow()){

		$lastDate = $row4['date_to'];

	}

	if($lastDate != false) $next_week_end	= ($lastDate+(604800));

	if($lastDate != false) $next_week_start = ($lastDate+(86400));

	//echo date('Y-m-d',$next_week_start)."<br>";
	//echo date('Y-m-d',$next_week_end)."<br>";
	if(!$lastDate || $lastDate > time() || $next_week_end > time()) return false;
	//echo "AAA";
	$sth5 = $this->dbGame->prepare(
		"SELECT ticket_id,kurz ".
		"FROM ticket_pohled ".
		"WHERE ticket_id = ?");
	if (PEAR::isError($sth5))
		throw new ExHandler($sth5->getMessage().'Nepodarilo se nacist ticket_pohled',"admin_ex_db");

	$sth6 = $this->dbGame->prepare(
		"SELECT castka ".
		"FROM ticket ".
		"WHERE ticket_id = ?");
	if (PEAR::isError($sth6))
		throw new ExHandler($sth5->getMessage().'Nepodarilo se nacist ticket_pohled',"admin_ex_db");

	$sth4 = $this->dbGame->prepare(
		"SELECT a.ticket_id,a.user_id,b.mena_id,b.lang_id ".
		"FROM vyherci_sazky a ".
		"INNER JOIN uzivatel b on a.user_id=b.user_id ".
		"WHERE a.full_win=1 AND a.datum>='".date('Y-m-d',$next_week_start)." 00:00:00' and a.datum<='".date('Y-m-d',$next_week_end)." 23:59:59' ");
	if (PEAR::isError($sth4))
		throw new ExHandler($sth4->getMessage().'Nepodarilo se nacist week_month_winners',"admin_ex_db");
	$res4 =& $this->dbGame->execute($sth4);
	if (PEAR::isError($res4))
		throw new ExHandler($res4->getMessage().'Nepodarilo se  nacist week_month_winners',"admin_ex_db");

	$ticket = array();

	while ( $row4 =& $res4->fetchRow() ){

		$res5 =& $this->dbGame->execute($sth6,array($row4['ticket_id']));
		if (PEAR::isError($res5))  {throw new ExHandler($res5->getMessage().'Nepodarilo se  nacist week_month_winners',"admin_ex_db");}

		if ($row5 =& $res5->fetchRow()){

		if(($row5['castka']/$mena[$row4['mena_id']]) < 3) continue; //TODO: value 3 should be read from config - constant in central currency

		}

		$ticket[$row4['ticket_id']]['user'] = $row4['user_id'];
		$ticket[$row4['ticket_id']]['mena'] = $row4['mena_id'];
		$ticket[$row4['ticket_id']]['lang'] = $row4['lang_id'];
		$ticket[$row4['ticket_id']]['rate'] = 1;
		$ticket[$row4['ticket_id']]['num'] = 0;
		$ticket[$row4['ticket_id']]['hight'] = 1;

		$res5 =& $this->dbGame->execute($sth5,array($row4['ticket_id']));
		if (PEAR::isError($res5))  {throw new ExHandler($res5->getMessage().'Nepodarilo se  nacist week_month_winners',"admin_ex_db");}

		while ($row5 =& $res5->fetchRow()){

		 $ticket[$row4['ticket_id']]['rate'] *= $row5['kurz'];
		 $ticket[$row4['ticket_id']]['num']++;

		 if($row5['kurz'] > $ticket[$row4['ticket_id']]['hight']) $ticket[$row4['ticket_id']]['hight'] = $row5['kurz'];

		}

	}

	$this->dbGame->autocommit(true);

	$high = array();
	foreach($ticket as $k=>$h){

		if(count($high) == 0) {
			$high = $h['rate'];
			$thigh = $k;
			$nhight = $h['num'];
			$rhight = $h['hight'];
			$uhight = $h['user'];
		}
		else {

			if($high < $h['rate']) { 
				$high = $h['rate'];
				$thigh = $k;
				$nhight = $h['num'];
				$rhight = $h['hight'];
				$uhight = $h['user'];
			}
			else if ( $high == $h['rate'] ) {

				if ( $nhight < $h['num'] ) {
					$high = $h['rate'];
					$thigh = $k;
					$nhight = $h['num'];
					$rhight = $h['hight'];
					$uhight = $h['user'];
				}
				else if ( $rhight < $h['hight'] ) {
					$high = $h['rate'];
					$thigh = $k;
					$nhight = $h['num'];
					$rhight = $h['hight'];
					$uhight = $h['user'];
				}
				else if ( $uhight > $h['user'] ) {
					$high = $h['rate'];
					$thigh = $k;
					$nhight = $h['num'];
					$rhight = $h['hight'];
					$uhight = $h['user'];
				}
		}
	 }

	}

	$mena = array();
	$sql = "select e.kurz,e.mena_id from game.kurzmena e where e.platny_od<=now() and e.platny_do>=now() ";
	$res =& $this->dbGame->query($sql);
	while ($row =& $res->fetchRow()){
		$mena[$row['mena_id']] = $row['kurz'];
	}


	if(isset($high)){

		$castka = floor(Constant::get('WINNER_WEAK_FREEBET')*$mena[$ticket[$thigh]['mena']]);

		$preklad = new Preklady();
		$t = $preklad->FindPreklad('mail_winner_week',$ticket[$thigh]['lang']); $t = $t[$ticket[$thigh]['lang']];

		$t = str_replace("{WEEK}",date('d.m.Y',$next_week_start)." - ".date('d.m.Y',$next_week_end),$t);

		$ob = new PridelFreeBet();
		$ob->pridelBonus($uhight,$castka,$t,'week_winner');

		$sth4 = $this->dbGame->prepare("insert into week_month_winners values(?,?,?,?,?,?)");
		if (PEAR::isError($sth4))  {throw new ExHandler($sth4->getMessage().'Nepodarilo se nacist week_month_winners',"admin_ex_db");}


		$res4 =& $this->dbGame->execute($sth4,array(date('Y-m-d',$next_week_start),date('Y-m-d',$next_week_end),intval($uhight),$castka,'w',intval($thigh)));

		if (PEAR::isError($res4))  {throw new ExHandler($res4->getMessage().'Nepodarilo se  vlozit do week_month_winners',"admin_ex_db");}


	}

	$this->dbGame->commit();

	return true;
  }




	


 

  /**
	* pousti ukoncovaciho demona
	* loguje balance
	* @deprecated
	*/
  public function BalLog(){


	$datum = It6_Date::dbNow();

	$sql = " select *  from uzivatel_im_data";
	$res3 =& $this->dbGame->query($sql);
	if(DB::isError($res3)) throw new ExHandler($sql.$res2->getMessage('Nepodarilo se vlozit pomocny radek  u her tanulka: '.$row['table_name']),"admin_ex_demon");

	while ($row =& $res3->fetchRow()){

	 $sql = "insert into balance_log values(".$row['user_id'].",".$row['zustatek'].",".$row['zetony'].",".$row['dluh'].",'".$datum."') ";
	 $res =& $this->dbBetWare->query($sql);
	 if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage('Nepodarilo se vlozit balance log '),"admin_ex_demon");


	}



	return true;

  }




	/**
	* pousti ukoncovaciho demona
	* nastaveni vyhernosti hracum
  */
  public function UBetWIN(){
	global $systemAr;

	$user_ar = $ticket = $mena = $system_ar = $system_status_ticket = array();

	$sql = "select mena_id  FROM mena";
	$res2 =& $this->dbGame->query($sql);
	if(DB::isError($res2)) throw new ExHandler('<br /><br />'.$sql.'<br /><br />Nepodarilo se provest dotaz: vyber z pohledu ticket_pohled, sazka_kurz a podtyp_sloupec',"admin_ex_db");

	while ($row =& $res2->fetchRow()){

	  $sql = "SELECT g.kurz FROM kurz f INNER JOIN kurz_mena g ON f.id_kurz = g.id_kurz WHERE f.platny_od <= now() AND f.platny_do > now( ) AND g.id_mena =".intval($row['mena_id']);
	  $res4 =& $this->dbGame->query($sql);
	  if(DB::isError($res4)) throw new ExHandler('Nepodarilo se provest dotaz: vyber kurzy',"admin_ex_db");
	  if ($row4 =& $res4->fetchRow()) $mena[$row['mena_id']] = $row4['kurz'];

	}

	$sql = "select k.*,x.mena_id  FROM `ticket_pohled` k inner join uzivatel x on k.user_id=x.user_id where  stats_user=0";
	$res2 =& $this->dbGame->query($sql);
	if(DB::isError($res2)) throw new ExHandler('<br /><br />'.$sql.'<br /><br />Nepodarilo se provest dotaz: vyber z pohledu ticket_pohled, sazka_kurz a podtyp_sloupec',"admin_ex_db");


	while ($row =& $res2->fetchRow()){



	if(!isset($ticket[$row['ticket_id']])){

	  $ticket[$row['ticket_id']]['user_id'] = $row['user_id'];
	  $ticket[$row['ticket_id']]['zalozen'] = It6_Date::fromDb($row['zalozen']);
	  $ticket[$row['ticket_id']]['zruseno'] = $row['zruseno']; //zruseny cely ticket
	  $ticket[$row['ticket_id']]['castka'] = ($row['castka']/$mena[$row['mena_id']]);
	  $ticket[$row['ticket_id']]['vyplacen'] = $row['vyplacen'];
	  $ticket[$row['ticket_id']]['kurz_celkem'] = 1;
	  $ticket[$row['ticket_id']]['mena_kurz'] = $mena[$row['mena_id']];
	  $ticket[$row['ticket_id']]['banker_rate'] = 1;
	  if(isset($row['vyhra_ticket']))$ticket[$row['ticket_id']]['vyhra_ticket'] = $row['vyhra_ticket'];
	  $ticket[$row['ticket_id']]['result'] = 1;
	  $ticket[$row['ticket_id']]['sazka'] = 0;
	  $ticket[$row['ticket_id']]['free_bet_bonus'] =$row['free_bet_bonus'];
	  $ticket[$row['ticket_id']]['system'] = $row['system'];
	  $ticket[$row['ticket_id']]['banker_num'] = 0;
	  $system_status_ticket[$row['ticket_id']] = 0;

	}

	 if($row['status'] == 1 || $row['zruseno'] == 1)		  {$ticket[$row['ticket_id']]['kurz_celkem'] *= 1;}
	 else if($row['ticket_sazka_zrusena'] == 1)				{$ticket[$row['ticket_id']]['kurz_celkem'] *= 1;}
	 else													 {$ticket[$row['ticket_id']]['kurz_celkem'] *= $row['kurz'];}


	$row['vysledek'] = explode(";",$row['vysledek']);

	if($row['vyplacen'] == 1 && $row['status'] != 1 && $row['ticket_sazka_zrusena'] != 1 && $row['zruseno'] != 1 && !in_array($row['sloupec_id'],$row['vysledek'])) {$ticket[$row['ticket_id']]['result'] = 0;}
	$ticket[$row['ticket_id']]['sazka']++;

	$ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['banker'] = $row['banker'];

	 if($row['system'] != 0){

		if($row['ticket_sazka_zrusena'] != 1 && $row['status'] != 1 && $row['zruseno'] != 1 && !in_array($row['sloupec_id'],$row['vysledek']) && $row['vyplacen'] == 1) {if($row['banker']==1)$system_status_ticket[$row['ticket_id']]=1;$ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['trefa'] = 2;}  //spatny tip
		else if($row['vyplacen'] == 1)	$ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['trefa'] = 1;  //spravny tip

		if($row['banker'] == 1){
		$row['kurz'] = ($row['zruseno'] == 1 || $row['ticket_sazka_zrusena'] == 1 || $row['status'] == 1?1:$row['kurz']);
		$ticket[$row['ticket_id']]['banker_rate'] = $ticket[$row['ticket_id']]['banker_rate'] * $row['kurz'];
		}
		else{
		$klic = count($system_ar[$row['ticket_id']]);
		$system_ar[$row['ticket_id']][$klic]['sazka_id'] = $row['sazka_id'];
		$system_ar[$row['ticket_id']][$klic]['rate'] = ($row['zruseno'] == 1 || $row['ticket_sazka_zrusena'] == 1 || $row['status'] == 1?1:$row['kurz']);
		}

	 }

	}

	$week_t  = (time() - 604800);
	$month_t = (time() - 2592000);

	foreach($ticket as $k=>$h){

	  $ticket[$k]['vyhra_ticket'] = ($h['castka']*$h['kurz_celkem']);

	  $plus = $win = $skutecna_prohra = $skutecna_prohra2 = 0;

	  if($h['system'] != 0 && $h['vyplacen'] == 1){

		$castka_rad = ($h['castka']/$systemAr[count($system_ar[$k])][$h['system']]);

		$GLOBALS['system_special_ar'] = Array();
		$vyhra_celkem =  Help::ReQSystem(0,$h['system'],0,$system_ar[$k],$h['banker_rate'],1,$castka_rad);

		if($h['vyplacen'] == 1 && $h['zruseno'] != 1){

		$system_win = 0;
		foreach($GLOBALS['system_special_ar'] as $h3){

			if($system_status_ticket[$k] == 1) break;

			$help_status_system = true;
			foreach($h3['sazky'] as $h4){

				if($ticket[$k]['sazky'][$h4]['trefa'] == 2) $help_status_system = false;

			}

			if($help_status_system) {$system_win += $h3['vyhra'];}
		}

		$plus = ($system_win - $h['castka']);

		}

		if($plus<0){
			if($h['free_bet_bonus'] == 0) $skutecna_prohra = (-1*$plus);
			$skutecna_prohra2 = $skutecna_prohra = (-1*$plus);
		}else $win=1;

	  }
	  else if($h['system'] == 0 && $h['vyplacen'] == 1){

		  if($h['result'] == 1) {$plus = ($ticket[$k]['vyhra_ticket'] - $h['castka']);$win=1;}else{

			if($h['free_bet_bonus'] == 0) $skutecna_prohra = $h['castka'];
			$skutecna_prohra2 = $skutecna_prohra = $h['castka'];

		  }

	  }

	  if($h['vyplacen'] == 1){

		//$ppp = 0;
		//if($plus < 0 ) {$ppp = ($plus);if($h['user_id'] == 20)echo "papa";$plus = 0;}

		if(!isset($user_ar[$h['user_id']]['vyhra'])){

		$user_ar[$h['user_id']]['vyhra'] = 0;
		$user_ar[$h['user_id']]['prohra'] = 0;
		$user_ar[$h['user_id']]['vsazeno_celkem'] = 0;

		}

		$user_ar[$h['user_id']]['vyhra'] = $user_ar[$h['user_id']]['vyhra'] + $plus;
		//if($plus <= 0 && $h['system'] != 0) $user_ar[$h['user_id']]['prohra'] = $user_ar[$h['user_id']]['prohra'] + ($h['castka']+$ppp);
		if($plus == 0 && $h['system'] == 0 && $h['result'] == 0)  $user_ar[$h['user_id']]['prohra'] = $user_ar[$h['user_id']]['prohra'] + $h['castka'];


		$user_ar[$h['user_id']]['vsazeno_celkem'] =  $user_ar[$h['user_id']]['vsazeno_celkem'] + $h['castka'];


	  }





	  #Statistiky#

	  if($h['vyplacen']==1){

	  $this->dbGame->autoCommit(false);

	  $sql = "update ticket set stats_user=1 where ticket_id=".$k;
	  $res =& $this->dbGame->query($sql);
	  if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: update  sazky',"admin_ex_db");}

	  if($h['zruseno'] == 1)			 $podminka = "delete_ticket=delete_ticket+1";
	  else if($win == 0)				 $podminka = "lose_ticket=lose_ticket+1";
	  else if($win == 1)				 $podminka = "win_ticket=win_ticket+1";

	  $sql = "update uzivatel set ticket_num=(ticket_num+1),num_bet_ticket=(num_bet_ticket+".$h['sazka']."),".$podminka.",bet_total2=bet_total2+".($h['zruseno']!=1?$h['castka']:0).",bet_total=bet_total+".$h['castka'].",bet_stats_win=bet_stats_win+".($plus>0?$plus:0).",bet_stats_lose_acc=bet_stats_lose_acc+".$skutecna_prohra.",bet_stats_lose_book=bet_stats_lose_book+".$skutecna_prohra2."  where user_id=".intval($h['user_id']);
	  $res =& $this->dbGame->query($sql);
	  if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

	  $this->dbGame->commit();
	  $this->dbGame->autoCommit(true);

	  }

	  ############
	}

	//echo "<pre>";print_r($user_ar);
	$sth = $this->dbGame->prepare("update uzivatel set vyhernost=?,castka_m=?,castka_w=? where user_id=?");
	if (PEAR::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");

	$week_t  = (time() - 604800);
	$month_t = (time() - 2592000);

	$sql = "select user_id,mena_id  from uzivatel";
	$res2 =& $this->dbGame->query($sql);
	if(DB::isError($res2)) throw new ExHandler('<br /><br />'.$sql.'<br /><br />Nepodarilo se provest dotaz: vyber z pohledu ticket_pohled, sazka_kurz a podtyp_sloupec',"admin_ex_db");


	while ($row =& $res2->fetchRow()){

	  if(!isset($user_ar[$row['user_id']]['week']) || !isset($user_ar[$row['user_id']])){
		$user_ar[$row['user_id']]['week'] = 0;
		$user_ar[$row['user_id']]['month'] = 0;
	  }

	  $sql = "select t.castka,t.zalozen  from  ticket t where DATE_SUB(now(), INTERVAL 31 DAY)<t.zalozen and t.user_id=".$row['user_id'];
	  $res3 =& $this->dbGame->query($sql);
	  if(DB::isError($res3)) throw new ExHandler('<br /><br />'.$sql.'<br /><br />Nepodarilo se provest dotaz: vyber z pohledu ticket_pohled, sazka_kurz a podtyp_sloupec',"admin_ex_db");

	  while ($row2 =& $res3->fetchRow()){

		$row2['castka'] = floatval(($row2['castka']/$mena[$row['mena_id']]));

		//TODO Ma to byt v gm nebo ne? Ted je. Jinak by se muselo dat , false
		$timestamp = It6_Date::fromDbAsTimestamp($row['zalozen']);

		if($week_t  < $timestamp)  $user_ar[$row['user_id']]['week'] = $user_ar[$row['user_id']]['week'] + $row2['castka'];
		if($month_t < $timestamp)  $user_ar[$row['user_id']]['month'] = $user_ar[$row['user_id']]['month'] + $row2['castka'];

	  }

	}

	foreach($user_ar as $k=>$h){

	  $sql = "select bet_total2,bet_stats_win,bet_stats_lose_book from uzivatel where  user_id=".$k;
	  $res =& $this->dbGame->query($sql);
	  if(DB::isError($res)) {$this->dbBetWare->rollback();$this->dbGame->rollback();throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");}

	  if($row =& $res->fetchRow()){

		$rozdil = ($row['bet_stats_win'] - $row['bet_stats_lose_book']);
		if($row['bet_total2']!=0)$vyhernost = ((($row['bet_total2']+$rozdil)/$row['bet_total2'])*100);else $vyhernost = 0;

	  }

	  $sql = "select bet_total,bet_stats_win,bet_stats_lose_book from uzivatel where  user_id=".$k;
	  $res =& $this->dbGame->query($sql);
	  if(DB::isError($res)) {$this->dbBetWare->rollback();$this->dbGame->rollback();throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");}

	  //$rozdil = ($user_ar[$k]['vyhra'] - $user_ar[$k]['prohra']);
	  //$vyhernost = ((($user_ar[$k]['vsazeno_celkem']+$rozdil)/$user_ar[$k]['vsazeno_celkem'])*100);

	  $res2 =& $this->dbGame->execute($sth,array(round($vyhernost,2),$user_ar[$k]['month'],$user_ar[$k]['week'],$k));
	  if (PEAR::isError($res2))  throw new ExHandler($res2->getMessage().'Nepodarilo se aktualizovat adminy',"admin_ex_db");

	}
  // echo  $user_ar[5152]['vyhra']."<br>";
  //	 echo  $user_ar[5152]['prohra'];
	return true;

  }


  public function SeoUrl(){

	$lang = array();

	$sql = "select iso_homepage,lang_id FROM jazyky";
	$res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler('<br /><br />'.$sql.'<br /><br />Nepodarilo se provest dotaz: vyber z pohledu ticket_pohled, sazka_kurz a podtyp_sloupec',"admin_ex_db");

	while ($row =& $res->fetchRow()){
		$lang[$row['lang_id']] = $row['iso_homepage'];
	}

	$sql = "select udalost_id,nazev FROM udalost";
	$res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler('<br /><br />'.$sql.'<br /><br />Nepodarilo se provest dotaz: vyber z pohledu ticket_pohled, sazka_kurz a podtyp_sloupec',"admin_ex_db");



	$preklady = new Preklady();

	while ($row =& $res->fetchRow()){

		$p = $preklady->findPreklad($row['nazev']);

		foreach($p as $k=>$h){


		  if($k==15) $h =  mb_ereg_replace("([[:space:]])+","-",$p[2])."-gr";
		  if($k==17) $h =  mb_ereg_replace("([[:space:]])+","-",$p[2])."-ua";
		  if($k==18) $h =  mb_ereg_replace("([[:space:]])+","-",$p[2])."-ru";
		  else $h =  mb_ereg_replace("([[:space:]])+","-",$h);


		  /*$h =  mb_ereg_replace("?","e",$h);
		  $h =  mb_ereg_replace("“","",$h);
		  $h =  mb_ereg_replace("?","",$h);
		  $h =  mb_ereg_replace("ł","l",$h);
		  $h =  mb_ereg_replace("„","",$h);
		  $h =  mb_ereg_replace("ę","e",$h);
		  $h =  mb_ereg_replace("ż","z",$h);*/

		  $h =  mb_ereg_replace("´","",$h);


			$h = Help::seoUrl($h);
			$h = trim($h);


			do{

				$sql = "select url from seo_url where url='/".Help::Slash($h)."/'";
				$res4 =& $this->dbGame->query($sql);
				if(DB::isError($res4)) throw new ExHandler('Nepodarilo se provest dotaz: editace oblastu',"admin_ex_db");

				if($res4->numRows()!=0) $h .= '-'.rand(0,100);

			}while($res4->numRows()!=0);



			$sql = "insert into seo_url (lang_id,type,url,event_id) values (".$k.",3,'/".Help::Slash($h)."/',".$row['udalost_id'].")";
			$res2 =& $this->dbGame->query($sql);


		}

	}


  /* $sql = "select betradar_tym_id,name,udalost_id FROM tymy where betradar_tym_id not in( select a.event_id from  seo_url a where a.type=6) ";
	$res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler('<br /><br />'.$sql.'<br /><br />Nepodarilo se provest dotaz: vyber z pohledu ticket_pohled, sazka_kurz a podtyp_sloupec',"admin_ex_db");

	while ($row =& $res->fetchRow()){


	$h = Help::seoUrl($row['name']);
	$h =  mb_ereg_replace("´","",$h);
	$h = trim($h);


	foreach($lang as $id=>$lll){

		$hhh = $h;
		$hhh .= '-'.$lll;

	do{


	  $sql = "select url from seo_url where url='/".Help::Slash($hhh)."/'";
	  $res4 =& $this->dbGame->query($sql);
	  if(DB::isError($res4)) throw new ExHandler('Nepodarilo se provest dotaz: editace oblastu',"admin_ex_db");

	  if($res4->numRows()!=0) $hhh .= '-'.rand(0,1000);

	}while($res4->numRows()!=0);

			$sql = "insert into seo_url (lang_id,type,url,event_id) values (".$id.",6,'/".Help::Slash($hhh)."/',".$row['betradar_tym_id'].")";
			$res2 =& $this->dbGame->query($sql);

	}

	}*/

	return true;

  }



/**
* pousti ukoncovaciho demona
* nastavi vsem aktualnim sazkam risk limit
*/
public function RiskLimit() {
	$sql = "select sazka_id,udalost_id,typ_id,podtyp_id from sazky where platna_od<now() and platna_do>now() and (status=0 or status=2)";
	$res =& $this->dbGame->query($sql);
	if(DB::isError($res)) { throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber sazky  ticket_pohled',"admin_ex_db");}

	$rl = array();

	while ($row =& $res->fetchRow()) {
		if(!isset($rl[$row['udalost_id']][$row['typ_id']][$row['podtyp_id']])) {
			$sql = "select risk_limit from bet_settings where udalost_id=".intval($row['udalost_id'])." and typ_id=".intval($row['typ_id'])." and podtyp_id=".intval($row['podtyp_id']);
			$res2 =& $this->dbGame->query($sql);
			if(DB::isError($res2)) { throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber sazky  ticket_pohled',"admin_ex_db");}

			if ($row2 =& $res2->fetchRow()){
				$rl[$row['udalost_id']][$row['typ_id']][$row['podtyp_id']] = $row2['risk_limit'];
			}
		}

		if (isset($rl[$row['udalost_id']][$row['typ_id']][$row['podtyp_id']])) {
			$sql = "update sazky set risk_limit=".intval($rl[$row['udalost_id']][$row['typ_id']][$row['podtyp_id']])." where sazka_id=".$row['sazka_id'];
			$res3 =& $this->dbGame->query($sql);
			if(DB::isError($res)) { throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber sazky  ticket_pohled',"admin_ex_db");}
		}
	}
	return true;
}

  /**
	* pousti ukoncovaciho demona
	*
	* pretahne stare sazky a tikety
	*/
  public function CleanBet(){

	 $cas = time();

	 $this->dbBetWare->autocommit(false);
	 $this->dbGame->autocommit(false);

	 #tikety#
	 $sql = "select * from ticket t where t.vyplacen=1 and t.vyplacen_date<>'0000-00-00 00:00:00' and t.vyplacen_date<'".date("Y-m-d H:i:s",($cas-BETLIFETIME))."'";
	 $res6 =& $this->dbGame->query($sql);
	 if(DB::isError($res6)) {$this->dbBetWare->rollback();$this->dbGame->rollback();$this->dbBetWare->autocommit(true);$this->dbGame->autocommit(true);throw new ExHandler($sql.$res6->getMessage(),"admin_ex_db");}

	 $this->logtext .= "Počet tiketu:".$res6->numrows()."\n";

	 while ($row =& $res6->fetchRow()){

	 $sql = "insert into ticket values (".$row['ticket_id'].",".$row['user_id'].",".$row['castka'].",'".$row['zalozen']."',".$row['vyplacen'].",
			 ".$row['zruseno'].",".intval($row['zrusil_bookmaker_id']).",'".Help::Slash($row['duvod_zruseni'])."',".$row['free_bet_bonus'].",".$row['system'].",'".$row['type']."',".$row['win'].",".$row['rate'].",".floatval($row['rate_real']).",".floatval($row['win_real']).",".$row['stats'].",".$row['stats_user'].",".$row['cupon_id'].",'".$row['vyplacen_date']."',".$row['vyplacen_bookmaker_id'].",".$row['mail'].")";
	  $res12 =& $this->dbBetWare->query($sql);
	  if(DB::isError($res12)) {$this->dbBetWare->rollback();$this->dbGame->rollback();$this->dbBetWare->autocommit(true);$this->dbGame->autocommit(true);throw new ExHandler($sql.$res12->getMessage(),"admin_ex_db");}

	  $sql = "select * from ticket_kurz where ticket_id=".$row['ticket_id'];
	  $res9 =& $this->dbGame->query($sql);
	  if(DB::isError($res9)) {$this->dbBetWare->rollback();$this->dbGame->rollback();$this->dbBetWare->autocommit(true);$this->dbGame->autocommit(true);throw new ExHandler($sql.$res9->getMessage(),"admin_ex_db");}

	  while ($row2 =& $res9->fetchRow()){

		if(mb_strlen($row2['ticket_sazka_duvod_zruseni']) < 1 ) $row2['ticket_sazka_duvod_zruseni'] = "";

		$sql = "insert into ticket_kurz values (".$row2['ticket_id'].",".$row2['sazka_id'].",".$row2['sloupec_id'].",".$row2['ticket_sazka_zrusena'].",".intval($row2['ticket_sazka_zrusil_bookmaker_id']).",'".Help::Slash($row2['ticket_sazka_duvod_zruseni'])."',".intval($row2['banker']).")";
		$res8 =& $this->dbBetWare->query($sql);
		if(DB::isError($res8)) {$this->dbBetWare->rollback();$this->dbGame->rollback();$this->dbBetWare->autocommit(true);$this->dbGame->autocommit(true);throw new ExHandler($sql.$res8->getMessage(),"admin_ex_db");}

	  }

	  $sql = "delete from ticket where ticket_id=".$row['ticket_id'];
	  $res11 =& $this->dbGame->query($sql);
	  if(DB::isError($res11)) {$this->dbBetWare->rollback();$this->dbGame->rollback();$this->dbBetWare->autocommit(true);$this->dbGame->autocommit(true);throw new ExHandler($sql.$res11->getMessage(),"admin_ex_db");}


	 }




	 #sazky#
	 $sql = "select * from sazky s where  s.proplacena=1 and s.platna_do<'".date("Y-m-d H:i:s",(time()-BETLIFETIME))."'";
	 $res =& $this->dbGame->query($sql);
	 if(DB::isError($res)) {$this->dbBetWare->rollback();$this->dbGame->rollback();throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");}


	 $sazkaids2 = $rowt2 = array();
	 $cas = time();

	 while ($row =& $res->fetchRow()){

		$sazkaids2[$row['sazka_id']] = $row['sazka_id'];
		$rowt2[$row['sazka_id']] = $row;

	 }

	 if(count($sazkaids2) == 0) $sazkaids2[] = 0;

	 foreach ($rowt2 as $row){


	  $sql = "replace into sazky values (".$row['sazka_id'].",'".Help::Slash($row['platna_od'])."','".Help::Slash($row['platna_do'])."',".$row['status'].",".$row['live'].",".$row['bookmaker_id'].",
			 ".$row['udalost_id'].",".$row['typ_id'].",".$row['podtyp_id'].",".$row['overena'].",'".Help::Slash($row['text'])."',".$row['jednoducha'].",'".$row['vysledek']."',".$row['proplacena'].",".$row['proplatil_bookmaker'].",
			 ".intval($row['betradar_sazka_id']).",".intval($row['betradar_statistic_id']).",".$row['risk_limit'].",".$row['risk_limit_balance'].",'".Help::Slash($row['info'])."','".Help::Slash($row['message'])."')";
	  $res6 =& $this->dbBetWare->query($sql);
	  if(DB::isError($res6)) {$this->dbBetWare->rollback();$this->dbGame->rollback();$this->dbBetWare->autocommit(true);$this->dbGame->autocommit(true);throw new ExHandler($sql.$res6->getMessage(),"admin_ex_db");}


	  $sql = "select * from sazka_kurz where sazka_id=".$row['sazka_id'];
	  $res2 =& $this->dbGame->query($sql);
	  if(DB::isError($res2)) {$this->dbBetWare->rollback();$this->dbGame->rollback();$this->dbBetWare->autocommit(true);$this->dbGame->autocommit(true);throw new ExHandler($sql.$res2->getMessage(),"admin_ex_db");}

	  while ($row2 =& $res2->fetchRow()){

		$sql = "replace into sazka_kurz(sazka_id,sloupec_id,poradi,kurz,platny_od,kurz_zmena) values (".$row2['sazka_id'].",".$row2['sloupec_id'].",".$row2['poradi'].",".$row2['kurz'].",'".$row2['platny_od']."',".$row2['kurz_zmena'].")";
		$res3 =& $this->dbBetWare->query($sql);
		if(DB::isError($res3)) {$this->dbBetWare->rollback();$this->dbGame->rollback();$this->dbBetWare->autocommit(true);$this->dbGame->autocommit(true);throw new ExHandler($sql.$res3->getMessage(),"admin_ex_db");}

	  }

	  $sql = "select * from sazka_kombinace where sazka1_id=".$row['sazka_id']." or sazka2_id=".$row['sazka_id'];
	  $res4 =& $this->dbGame->query($sql);
	  if(DB::isError($res4)) {$this->dbBetWare->rollback();$this->dbGame->rollback();$this->dbBetWare->autocommit(true);$this->dbGame->autocommit(true);throw new ExHandler($sql.$res4->getMessage(),"admin_ex_db");}

	  while ($row3 =& $res4->fetchRow()){

		$sql = "replace into sazka_kombinace (sazka1_id,sazka2_id,kombinace_ticket,kombinace_show)
				values (".$row3['sazka1_id'].",".$row3['sazka2_id'].",".$row3['kombinace_ticket'].",".$row3['kombinace_show'].")";
		$res5 =& $this->dbBetWare->query($sql);
		if(DB::isError($res5)) {$this->dbBetWare->rollback();$this->dbGame->rollback();$this->dbBetWare->autocommit(true);$this->dbGame->autocommit(true);throw new ExHandler($sql.$res5->getMessage(),"admin_ex_db");}

	  }

	 }


	 #sazky#
	 $sql = "select * from sazky s where  s.sazka_id not in(select t.sazka_id from ticket_pohled t where t.sazka_id=s.sazka_id group by t.sazka_id) and s.proplacena=1 and s.platna_do<'".date("Y-m-d H:i:s",(time()-BETLIFETIME))."'";
	 $res =& $this->dbGame->query($sql);
	 if(DB::isError($res)) {$this->dbBetWare->rollback();$this->dbGame->rollback();throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");}


	 $sazkaids = $rowt = array();


	 while ($row =& $res->fetchRow()){

		$sazkaids[$row['sazka_id']] = $row['sazka_id'];
		$rowt[$row['sazka_id']] = $row;

	 }

	 if(count($sazkaids) == 0) $sazkaids[] = 0;

	 foreach ($rowt as $row){

//	 $sql = "select t.sazka_id from ticket_pohled t where t.ticket_id in (select d.ticket_id from ticket_pohled d where d.sazka_id=".$row['sazka_id']." group by d.ticket_id) and t.vyplacen=1 and t.sazka_id not in(".implode(",",$sazkaids).")";
//	 $res2 =& $this->dbGame->query($sql);
//	 if(DB::isError($res2)) {$this->dbBetWare->autocommit(true);$this->dbGame->autocommit(true);throw new ExHandler($sql.$res2->getMessage(),"admin_ex_db");}
//
//	 if($res2->numrows() > 0) {unset($sazkaids[$row['sazka_id']]);continue;}


	  $sql = "delete from sazky where sazka_id=".$row['sazka_id'];
	  $res7 =& $this->dbGame->query($sql);
	  if(DB::isError($res7)) {$this->dbBetWare->rollback();$this->dbGame->rollback();$this->dbBetWare->autocommit(true);$this->dbGame->autocommit(true);throw new ExHandler($sql.$res7->getMessage(),"admin_ex_db");}

	 }


	 $this->logtext .= "Počet sázek:".($sazkaids[0] == 0?0:count($sazkaids))."\n";
	 if(count($sazkaids) == 0) $sazkaids[] = 0;


	 // echo nl2br($this->logtext);
	 $this->dbBetWare->commit();
	 $this->dbGame->commit();

	 $this->dbBetWare->autocommit(true);
	 $this->dbGame->autocommit(true);

	 return true;

  }

  /**
	* pousti ukoncovaciho demona
	* Cisti systemove kombonace starsi 24 hod
	* @deprecated
	*
	*/
  public function CleanSystem(){

	 $sql = "delete from system_user_bets where now()>DATE_ADD(datum, INTERVAL 1 DAY)";
	 $res3 =& $this->dbGame->query($sql);
	 if(DB::isError($res3)) throw new ExHandler($sql.'Nepodarilo se vlozit happy hours',"admin_ex_demon");

	 return true;

  }


	  /**
	* pousti ukoncovaciho demona
	* najde hry ktere moc vyhravaji a upozorni
	*
	*/
  public function HryStat(){

	$obj =  new HryKolekce(0,$this->dbGame);
	$res = $obj->SelectData();

	$this->hra =  array();
	$preklady = new Preklady();

	while ($row =& $res->fetchRow()){

	$nazev = $preklady->FindPreklad($row['nazev'],1);

	$game_temp = new $row['trida']();
	$online_temp = $game_temp->getOnlinePlayers();
	$user_info = $game_temp->getGame();
	$sum_temp = $game_temp->getTotalWin();

	if($sum_temp['vsazeno'] != 0) $vyhernost_temp = round(((($sum_temp['vyhra']+$sum_temp['vsazeno'])/$sum_temp['vsazeno'])*100),2);
	else $vyhernost_temp = 0;

	if($row['vypnuto']==0 && ($sum_temp['vyhra']>200 || $vyhernost_temp>105)){

	  $this->hra[$row['hra_id']]['nazev'] = $nazev[1];
	  $this->hra[$row['hra_id']]['vsazeno'] = $sum_temp['vsazeno'];
	  $this->hra[$row['hra_id']]['vyhra'] = $sum_temp['vyhra'];
	  $this->hra[$row['hra_id']]['vyhernost'] = $vyhernost_temp;

	}

	}

	$zprava = '';
	foreach($this->hra as $k=>$h){

		if($h['vyhra'] > 15000){
		  $sql = "update hry set vypnuto=1 where hra_id=".$k;
		  $res =& $this->dbGame->query($sql);
		  if(DB::isError($res)) {throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber ticket_pohled',"admin_ex_db");}
		}
		$zprava .= 'Nazev: '.mb_convert_encoding($h['nazev'], "iso-8859-2", "UTF-8").' - Vyhra cista: '.$h['vyhra'].' - Vyhernost: '.$h['vyhernost']."\n\n";


	}

	if(mb_strlen($zprava)>0) mail(ADMINMAIL,'!!VYHERNOST HRY!!',$zprava);

  }
	/**
	* pousti ukoncovaciho demona
	* ukonci session ktere jsou dlouho neaktivni
	* posleze prevede ukoncene session do skladu dat
	*/
  public function VycistiSession(){

	  $this->dbSession->autocommit(false);
	  $this->dbGameWare->autocommit(false);

	  $sth = $this->dbSession->prepare("update session set status=? where (status<>? or status<>?) and time < ?");
	  if (PEAR::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");

	  $res =& $this->dbSession->execute($sth,array(1,2,3,(time()-SESMAX)));
	  if (PEAR::isError($res))  throw new ExHandler('Nepodarilo se aktualizovat stare sessionv Demonu : 1',"admin_ex_db");

	  //$sth = $this->dbGameWare->prepare("insert into session values(?,?,?,?,?,?,?,?,?,?)");
	  //if (PEAR::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");

	  $sth2 = $this->dbSession->prepare("delete from session where ses_id=? and ip=? and prohlizec=?");
	  if (PEAR::isError($sth2))  throw new ExHandler($sth2->getMessage().'Nepodarilo se aktualizovat stare sessionv Demonu : 2',"admin_ex_db");

	  $sql = "select * from session where status<>2 and status<>3 and status<>0";
	  $res2 =& $this->dbSession->query($sql);
	  if(DB::isError($res2)) throw new ExHandler($sql.$res2->getMessage().'Nepodarilo se aktualizovat stare sessionv Demonu : 3',"admin_ex_db");

	  while ($row =& $res2->fetchRow()){

		//$res =& $this->dbGameWare->execute($sth,array($row['ses_id'],$row['ip'],$row['prohlizec'],$row['data'],$row['time'],$row['zprava'],$row['status'],$row['start'],$row['user_id'],$row['bookmaker_id']));
		//if (PEAR::isError($res))  throw new ExHandler('Nepodarilo se vymazat stare session',"admin_ex_db");

		$res =& $this->dbSession->execute($sth2,array($row['ses_id'],$row['ip'],$row['prohlizec']));
		if (PEAR::isError($res))  throw new ExHandler('Nepodarilo se aktualizovat  stare sessionv Demonu : 4',"admin_ex_db");

	  }

	  $this->dbSession->commit();
	  $this->dbGameWare->commit();

	  $this->dbSession->autocommit(true);
	  $this->dbGameWare->autocommit(true);

	  $sql = "select user_id from uzivatel where zakazany=1 and close_mail=0";
	  $res =& $this->dbGame->query($sql);
	  if(DB::isError($res)) {throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber ticket_pohled',"admin_ex_db");}

	  include('shared/htmlMimeMail/htmlMimeMail.php');
	  while ($row =& $res->fetchRow()){

		//V supportu chteli maily zrusit tak komentuji//$this->SendMail($row['user_id'],1);

		$sql = "update uzivatel set close_mail=1 where user_id=".$row['user_id'];
		$res2 =& $this->dbGame->query($sql);
		if(DB::isError($res2)) {throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber ticket_pohled',"admin_ex_db");}


	  }

	  return true;

  }




  


  /**
 * Metoda posila uzivateli mail
 * @param int $user_id id uzivatele
 * @param int $type typ mialu, ktery se ma poslat
 * @param string $text
 * @return void
 */
 private function SendMail($user_id,$type,$text=''){

	#Friendshipbonus#
	if($type == 2){

		 $section = $menu = $email = array();

		 $sql = "select nick,email,lang_id from uzivatel where user_id=".intval($user_id);
		 $res =& $this->dbGame->query($sql);
		 if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");

		 if($row =& $res->fetchRow()){

			$email[] = $row['email'];
			$lang =  $row['lang_id'];
			$nick =  $row['nick'];

		 }else throw new ExHandler('Nebyl nalezen email',"admin_ex_page");


		 $sql = "select * from preklady_menu where (menu_id=24 ) and lang_id=".$lang;
		 $res2 =& $this->dbGame->query($sql);
		 if(DB::isError($res2)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler($sql.'Nepodarilo se provest dotaz: update tiketu',"admin_ex_db");}

		 if ($row2 =& $res2->fetchRow()){
		  $menu[$row2['menu_id']] = $row2['uri'];
		 }

		 include_once "shared/template/class.TemplatePower.inc.php";
		 $stpl = new TemplatePower("_tpl/mail_fbs.tpl");
		 $stpl->prepare();


		 $preklad = new Preklady();


		 $begin  = $preklad->FindPreklad('mail_fbs_anotace',$lang); $begin  = $begin [$lang];
		 $osloveni  = $preklad->FindPreklad('mail_osloveni',$lang); $osloveni = $osloveni[$lang];
		 $sub  = $preklad->FindPreklad('mail_fb_subject',$lang); $sub = $sub[$lang]; //predmet
		 $body = $preklad->FindPreklad('mail_fb_body',$lang); $body = $body[$lang]; //telo
		 $mail_footer = $preklad->FindPreklad('mail_footer',$lang); $mail_footer = $mail_footer[$lang]; //paticka
		 $copy  = $preklad->FindPreklad('sys_copyright',$lang); $copy  = $copy [$lang];


		 $body = mb_ereg_replace("{URL}","<a href=\"".WEBHOST.$menu[24]."\">".WEBHOST.$menu[24]."</a>",$body);
		 $body = mb_ereg_replace("{CODE}",'<table style="margin:10px 0px;"><tr><td style="padding:10px;background-color:#252525;width:auto;">'.$text.'</td></tr></table>',$body);

		 $stpl->assign("FBSNAME",$osloveni);
		 $stpl->assign("BEGIN",$begin );
		 //$stpl->assign("CODE",'');
		 $stpl->assign("TEXT",$body);
		 $stpl->assign("FOOT",$mail_footer);
		 $stpl->assign("COPY",$copy);

		 $mail_t = $stpl->getOutputContent();

		 $mail = new htmlMimeMail();


		$mail->addHTMLImage($mail->getFile($_SERVER["DOCUMENT_ROOT"].'/_clip_mail/logo2.gif'),'logo2.gif','image/jpeg');
		$mail->setTextCharset("UTF-8");
		$mail->setHeadCharset("UTF-8");
		$mail->setHTMLCharset("UTF-8");  $mail->html_charset = "UTF-8";$mail->text_encoding = "UTF-8";
		$mail->setHTMLEncoding("base64");
		$mail->setFrom(NOREPLY);
		$mail->setReturnPath(NOREPLY);
		$mail->setHtml($mail_t);
		$mail->setSubject($sub);

		$mail->send($email);


	}

	#Zavreni uctu#
	else if($type == 1){

		 $section = $menu = $email = array();

		 $sql = "select nick,email,lang_id from uzivatel where user_id=".intval($user_id);
		 $res =& $this->dbGame->query($sql);
		 if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");

		 if($row =& $res->fetchRow()){

			$email[] = $row['email'];
			$lang =  $row['lang_id'];
			$nick =  $row['nick'];

		 }else throw new ExHandler('Nebyl nalezen email',"admin_ex_page");



		 $sql = "select * from preklady_menu where (menu_id=24 ) and lang_id=".$lang;
		 $res2 =& $this->dbGame->query($sql);
		 if(DB::isError($res2)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler($sql.'Nepodarilo se provest dotaz: update tiketu',"admin_ex_db");}

		 if ($row2 =& $res2->fetchRow()){
		  $menu[$row2['menu_id']] = $row2['uri'];
		 }




		 $preklad = new Preklady();
		 $sub  = $preklad->FindPreklad('mail_close_acc_sub',$lang); $sub = $sub[$lang]; //predmet
		 $body = $preklad->FindPreklad('mail_close_acc_body',$lang); $body = $body[$lang]; //telo
		 $mail_footer = $preklad->FindPreklad('mail_footer',$lang); $mail_footer = $mail_footer[$lang]; //paticka

		 $body = mb_ereg_replace("{URL}","<a href=\"".WEBHOST.$menu[24]."\">".WEBHOST.$menu[24]."</a>",$body);
		 $body = mb_ereg_replace("{NICK}",$nick,$body);

		 $mail = new htmlMimeMail();

		 $mail_t = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta name="keywords" content="on-line casino, games, offshore, secure, legal, chance, licensed, regulated, on-line," />
 <meta name="description" content="Play on-line games for real money or for free. " />
 <meta name="abstract" content="" />
 <meta name="robots" content=\'index,follow\' />
 <meta name="googlebot" content=\'index,follow,snippet,archive\' />
 <meta name="Author" content="" />


 <body style="font-family: Arial, sans-serif;font-size: 0.8em;color: #333333;margin: 0px;padding: 0px;_height:100%;background:white;background-repeat: repeat-x;">
	<!--Top panel with logo-->
	<div style=" padding:10px;border-bottom:1px solid #E4E9EF;">
	<a href="#"><img src="logo2.gif"  style="border:0px;" alt="logo" /></a>

	</div>
	<!--End Top panel-->

	<br />'.nl2br($body).'  <br />

<div style="width:auto; padding:1px;margin:8px 0px;font-size:0.8em;clear:both;color:#9E9E9E;border-top:1px solid #E4E9EF;">
 '. INFOMAIL .'
</div>
<div style="float:right;"><a href="'.WEBHOST.'">'.PROTOCOL.WEBHOST.'</a></div>
	<!--End Content of the page-->
<div style="background:#DFE3E8;margin:20px 0px 0px 0px;padding:10px;clear:both;font-size:0.85em;">
  '.$mail_footer.'
</div>

 </body>
 </html>';

		$mail->addHTMLImage($mail->getFile($_SERVER["DOCUMENT_ROOT"].'/_clip_mail/logo2.gif'),'logo2.gif','image/jpeg');
		$mail->setTextCharset("UTF-8");
		$mail->setHeadCharset("UTF-8");
		$mail->setHTMLCharset("UTF-8");  $mail->html_charset = "UTF-8";$mail->text_encoding = "UTF-8";
		$mail->setHTMLEncoding("base64");
		$mail->setFrom(NOREPLY);
		$mail->setReturnPath(NOREPLY);
		$mail->setHtml($mail_t);
		$mail->setSubject($sub);

		$mail->send($email);


	}


 }

	  /**
	* pousti ukoncovaciho demona
	* ukonci session ktere jsou dlouho neaktivni u adminu
	*/
  public function VycistiSessionAdmin(){

	  $sth = $this->db->prepare("update session set status=? where (status<>? or status<>?) and time < ?");
	  if (PEAR::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");

	  $res =& $this->db->execute($sth,array(1,2,3,(time()-SESMAX)));
	  if (PEAR::isError($res))  throw new ExHandler('Nepodarilo se vymazat stare session',"admin_ex_db");



	  return true;

  }



	/**
	* aktualizace uzivatelu v ostatnich datbazich
	*
	*/
  public function Users(){

	  $u = new UserKolekce();
	  $res = $u->selectData();

	  $sth = $this->dbGameWare->prepare("replace into uzivatel (user_id,zeme_id,nick) values (?,?,?)");
	  if (PEAR::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");

	  //$sth2 = $this->dbCasGam->prepare("replace into uzivatel (user_id,zeme_id,nick) values (?,?,?)");
	  //if (PEAR::isError($sth2))  throw new ExHandler($sth2->getMessage(),"admin_ex_db");


	  while ($row = $res->fetchRow()){

		 $res2 =& $this->dbGameWare->execute($sth,array($row['user_id'],$row['zeme_id'],$row['nick']));
		 if (PEAR::isError($res2))  throw new ExHandler('Nepodarilo se aktualizovat adminy',"admin_ex_db");

		 //$res3 =& $this->dbCasGam->execute($sth2,array($row['user_id'],$row['zeme_id'],$row['nick']));
		 //if (PEAR::isError($res3))  throw new ExHandler('Nepodarilo se aktualizovat adminy',"admin_ex_db");

	  }



	  return true;
  }










  /**
	* Metoda v pripade uspechu zapise zpravu o provedeni akce do logu
	*
	* @param string $db databaze do ktere se ma zapsat
	* @param string $akce provedena akce
	* @param string $code kod akce
	* @param string $text doplnujicitext
	* @return void
	*/
  public function InsertLog($db="admin",$akce="",$code="",$text=""){

	It6_Log::info(
		$text,
		It6_Log::TAG_DEMON, array(
		'action' => $akce,
		'code' => $code));
	/*
	$sql = "insert into logy (datum,akce,code,text) values ('".It6_Date::dbNow()."','".Help::Slash($akce)."','".Help::Slash($code)."','".Help::Slash($text)."')";
	if($db == "admin") $res =& $this->db->query($sql);
	else  {$res =& $this->dbGame->query($sql);}
	if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");
	*/
  }

  private function closeDB($conn){
	if(isset($conn) && is_object($conn) && (get_class($conn) == 'DB')) $conn->disconnect();
  }

  public function EndDemon(){
	$this->closeDB($this->dbGame);
	$this->closeDB($this->dbGameWare);
	$this->closeDB($this->dbBetWare);
	$this->closeDB($this->dbAf);
	$this->closeDB($this->dbSession);
	$this->closeDB($this->db);
	$this->closeDB($this->dbCasGam);
  }

}
