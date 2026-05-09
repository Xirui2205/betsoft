<?php
/**
 * @package    service
 */

/**
 * Trida pro proplaceni s AJAXEM
 *
 * 
 * <code>
 * 
 * </code>
 *
 * @package    AJAX
 */
 
class AjaxServerLiveBet{

/**
 * spojeni na databazi admin
 * @access private
 * @var DB
 */              
  private  $dbGame;

 /**
 * spojeni na databazi admin
 * @access private
 * @var DB
 */              
  private  $db;
  
  /**
 * kurzy meny
 * @access private
 * @var array
 */              
  private  $mena=array();
  
 /**
 * spojeni na databazi Session
 * @access private
 * @var DB
 */              
  private  $dbSession;
  
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

   /*
   $_GET['superb'] = 1;
   
   if(isset($_GET['work']) && $_GET['work']!=5){
     session_set_save_handler (
    array("SessionBookmaker", "open"),
    array("SessionBookmaker", "close"),
    array("SessionBookmaker", "read"),
    array("SessionBookmaker", "write"),
    array("SessionBookmaker", "destroy"),
    array("SessionBookmaker", "gc"));
   }
   */
	session_set_save_handler(
		array('It6_Session_Admin', 'open'),
		array('It6_Session_Admin', 'close'),
		array('It6_Session_Admin', 'read'),
		array('It6_Session_Admin', 'write'),
		array('It6_Session_Admin', 'destroy'),
		array('It6_Session_Admin', 'gc')
	);

  register_shutdown_function("session_write_close");

   //if(!SesClass::open($this->db)) throw new ExHandler('Nepodarilo se inicializovat Session',"admin_ex_db");
	if (!It6_Session_Admin::start($this->db)) throw new ExHandler('Nepodarilo se inicializovat Session', "admin_ex_db");

   //if(SesClass::getUserStatus() != 2) {echo "SESSION ERROR"; return;}
	if (!It6_Session_Admin::isAuthenticated()) {
		echo "SESSION ERROR";
		return;
	}
   
   
   $this->FindAction();
   
   //SesClass::close($this->db);        //uzavreni session nelze uz pridavat dalsi session
   It6_Session_Admin::end($this->db);
   
   $this->db->disconnect();  //uzavreni spojeni s db

   $this->dbGame->disconnect();  //uzavreni spojeni s dbGame

  }

   /**
   *Vybere akci
   *  
  */
  public function FindAction(){

  	#Sazky infromace#
  	if(isset($_GET['work']) && $_GET['work']==1){
  		$this->BetInfo();
  	}
  	#Uzivatel informace#
  	else if(isset($_GET['work']) && $_GET['work']==2){
  		
  		$this->UserInfo();
  		
  	}
    #Povolovani tiketu vraceni dat#
  	else if(isset($_GET['work']) && $_GET['work']==6 && isset($_GET['tab']) && $_GET['tab']==1){
  		
  		$this->BookCheckVal();
  		
  	}
    #Povolovani tiketu akce#
  	else if(isset($_GET['work']) && $_GET['work']==6 && isset($_GET['tab']) && $_GET['tab']==2){
  		
  		$this->BookCheckAction();
  		
  	}
    #Povolovani tiketu#
  	else if(isset($_GET['work']) && $_GET['work']==6){
  		
  		$this->BookCheck();
  		
  	}
    #Vyhledavani udalosti k danemu sportu#
  	else if(isset($_GET['work']) && $_GET['work']==4){
  		
  		$this->UdalostLiveSelect();
  		
  	}
    #Vyhledani meny k uzivateli#
  	else if(isset($_GET['work']) && $_GET['work']==5){
  		
  		$this->CurrencyUser();
  		
  	}
  	
  }
  
  /**
 * Vyhledani meny k uzivateli
 * @return string
 */
  public  function CurrencyUser(){
  	  
  	  $w = "1";
  	  if(isset($_GET['info']) && $_GET['info'] == "uid") $w = "b.user_id=".$_GET['dat'];
  	  if(isset($_GET['info']) && $_GET['info'] == "nick") $w = "b.nick=".$_GET['dat'];
  	  
  	  $sth = $this->dbGame->prepare("select mena_text from mena a inner join uzivatel b on a.mena_id=b.mena_id where ".$w);
      if (PEAR::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");
  	  
      $res2 =& $this->dbGame->execute($sth);
      if (PEAR::isError($res2))  throw new ExHandler($res2->getMessage().'Nepodarilo se vytvorit live sazku',"admin_ex_db");
      
      if ($row =& $res2->fetchRow()){
      	
      	 echo $row["mena_text"];  
      	
      }
      
      
      return '';
      
  }
  
  
      /**
 * Vybere udalosti k danemu sportu
 * @return string
 */
  public  function UdalostLiveSelect(){
  	
  	if(!isset($_GET['sport'])) echo '<option>Nebyl nalezen žádný záznam</option>';
  	
  	$preklad = new Preklady();
  	

    $sql = "select udalost_id,nazev from udalost where sport_id=".intval($_GET['sport']);
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz:  udalosti',"admin_ex_db");
    
    $udalost = array();
    while($row =& $res->fetchRow()){
    	 
    	$r = $preklad->FindPreklad($row['nazev'],1); 
    	$udalost[$row['udalost_id']] = $r[1];
    	
    	
    }
    
    
    setlocale(LC_COLLATE, "cs_CZ.utf8");
	uasort($udalost,'strcoll');
    
	foreach($udalost as $k=>$h){
		
		echo '<option value="'.$k.'">'.$h.'</option>';
		
	}
	
  }
  
   /**
*
*SLuzba online zalozeni tiketu
*
* @param int $user_id id uzivatele
* return bool
*/
  public function callService($user_id=0){
  	echo "1";
  	
  	if($user_id == 0) return false;
    
  	  	
  	usleep(rand(0,1500));
  	
  	$fp = fopen(ROOT."/tmp/callService_".date("Y_m_d"),"a");
   
  	$d = "Book: ".$_SESSION['bookmaker']."\n";
  	$d .= "Date: ".It6_Date::dbNow()."\n";
  	$d .= "USER: ".$user_id;
  	$d .= "\n\n\n\n";
    fwrite($fp,$d);
    fclose($fp);
  
    ###
    
    $postdata = array(
     'uid' => $user_id,
     'webservicepass' => WEBSERVICE_PASS
     );

	 $opts = array(
	    CURLOPT_POST           => true,
		CURLOPT_POSTFIELDS     => $postdata,
        CURLOPT_RETURNTRANSFER => true
	 );
	 $ch = curl_init(WEBSERVICE_URL);
	 curl_setopt_array($ch, $opts);
	 $response = curl_exec($ch);
	 curl_close($ch);
    	
	 if($response == 1) return true;else return false;
	 	
    ###
    

   
  }

public static function computeTicketHash($userId, $adminId, $amount, $data) {
	return md5($userId . ':' . $adminId . ':' . $amount  . ':' . $data);
}

     /**
  * Provede akce
  *  
  *
  */
  public function BookCheckAction(){
  	
  	if(!isset($_GET['action']) || !isset($_GET['info']) || !isset($_GET['user'])) return;
  	
  	if(isset($_SESSION['last_call']) && $_SESSION['last_call'] == time()) return;
  	
  	$_SESSION['last_call'] = time();
  	
  	//1 povolit
    //2 zamitnout

    //4 jina castka

    $this->dbGame->autocommit(false); // bacuse of FOR UPDATE some lines below
    $adminId = (empty($_GET['admin']) ? 0 : intval($_GET['admin']));
    
    if($_GET['action'] == 1 || $_GET['action'] == 2){
    	
    	$hash = '';
    	$sql = "select data,amount,user_id,admin_id from  coupon_data  where user_id=".intval($_GET['user'])." AND admin_id=" . $adminId . " for update";
        $res2 =& $this->dbGame->query($sql);
        if(DB::isError($res2)) throw new ExHandler('Nepodarilo se provest dotaz: update coupon_data',"admin_ex_db");
        if($row =& $res2->fetchRow()){
        	$hash = self::computeTicketHash($row['user_id'], $row['admin_id'], $row['amount'], $row['data']);
        	$data = Zend_Json::decode($row['data']);
        	$sazky = '';
        	foreach($data['bet'] as $k=>$h){
        		$sazky .= $h['id_bet'].';';
        	}
        
        $newAmount = floatval($_GET['amount']);
        //TODO: log adminId too
        $sql = "insert into bookmaker_ticket_prove_log values(".intval($_GET['user']).",".$_SESSION['bookmaker'].",'".$sazky."',".round($_GET['amount'],2).",".$newAmount.",".$_GET['action'].",'".It6_Date::dbNow()."')";
        $res2 =& $this->dbGame->query($sql); 
        if(DB::isError($res2)) throw new ExHandler('Nepodarilo se provest dotaz: update bookmaker_ticket_prove_log',"admin_ex_db");
        }
    }

    if(!isset($_GET['hs']) || $_GET['hs'] != $hash) $_GET['action'] = 2;
    
  	if($_GET['action'] == 1){
  		
  		$sql = "update coupon_data set status=2 where user_id=".intval($_GET['user']) . " AND admin_id=" . $adminId;
        $res2 =& $this->dbGame->query($sql);
        if(DB::isError($res2)) throw new ExHandler('Nepodarilo se provest dotaz: update coupon_data',"admin_ex_db");
  		
  	   // $serviceHelp = false;
        //for($x=0;$x<3;$x++){
         //if($x == 0 || !$serviceHelp) $serviceHelp = $this->callService(intval($_GET['user']));
         //else break;
       // }
        $this->callService(intval($_GET['user']));
  	}
    else if($_GET['action'] == 2){
  		
  		$sql = "update coupon_data set status=3 where user_id=".intval($_GET['user']) . " AND admin_id=" . $adminId;
        $res2 =& $this->dbGame->query($sql);
        if(DB::isError($res2)) throw new ExHandler('Nepodarilo se provest dotaz: update coupon_data',"admin_ex_db");
  		
  	}
    else if($_GET['action'] == 3){
  		
  		$sql = "update coupon_data set status=5 where user_id=".intval($_GET['user']) . " AND admin_id=" . $adminId;
        $res2 =& $this->dbGame->query($sql);
        if(DB::isError($res2)) throw new ExHandler('Nepodarilo se provest dotaz: update coupon_data',"admin_ex_db");
  		
  	}
    else if($_GET['action'] == 0){
  		
  		$sql = "update coupon_data set status=0 where user_id=".intval($_GET['user']) . " AND admin_id=" . $adminId;
        $res2 =& $this->dbGame->query($sql);
        if(DB::isError($res2)) throw new ExHandler('Nepodarilo se provest dotaz: update coupon_data',"admin_ex_db");
  		
  	}

  	$this->dbGame->commit();
  	$this->dbGame->autocommit(true);
  	
  }
  
  
      /**
  * Kontrola tiketu vracenid at 
  * 
  * @param int $uid id uzivatele
  * 
  */
  public function UserSaldo($uid=null){
  	
    if(count($this->mena)==0){   
      $sql = "select e.kurz,e.mena_id from kurzmena e where e.platny_od<=now() and e.platny_do>=now() ";
      $res =& $this->dbGame->query($sql);
      while ($row =& $res->fetchRow()){
    	$this->mena[$row['mena_id']] = $row['kurz'];
      }
   
    }
    
  	$vklad = $vyber = $saldo = $zustatek = $mena_id = 0;
  	
  	$sql = "select b.zustatek,b.zetony,a.mena_id from uzivatel a inner join uzivatel_im_data b  on a.user_id=b.user_id where a.user_id=".intval($uid);
	$res4 =& $this->dbGame->query($sql);
	if(DB::isError($res4)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky uzivatel',"admin_ex_db");
	if($row4 =& $res4->fetchRow()){
		$zustatek = $row4['zustatek'] + $row4['zetony'];
		$mena_id = $row4['mena_id'];
	}
	/*
    $sql = "select a.* from finacni_transakce a  where a.user_id=".intval($uid);
	$res4 =& $this->dbGame->query($sql);
	if(DB::isError($res4)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky uzivatel',"admin_ex_db");
	while($row4 =& $res4->fetchRow()){
		
		if($row4['typ_platby'] == 1)     $vklad += $row4['castka'];
		else if($row4['typ_platby'] == 2) $vyber += $row4['castka'];
		
	}
	//TODO: ? DRY, use It6_Models_Currency::convertAmountToCentralCurrency(intval($uid), $vklad - $vyber - $zustatek, Zend_Registry::get('zdb_game'));
	$saldo = round( ( ($vklad - $vyber - $zustatek) /$this->mena[$mena_id]) ,2);
	*/
	$saldo = round( ( (- $zustatek) /$this->mena[$mena_id]) ,2);
	
	return $saldo;
  }
  
    /**
  * Kontrola tiketu vracenid at 
  *  
  * status  0:nic,1:zacne se zobrazovat,2:povoleny,3:nepovoleny,4:povoleny s jinou castkou 5:pozastaveno resi se
  */
  public function BookCheckVal(){
  	
  	$attr = $mena = $system_ar =  array();
  	$preklady = new Preklady();
  	
  	$sql = "select e.kurz,e.mena_id from kurzmena e where e.platny_od<=now() and e.platny_do>=now() ";
    $res =& $this->dbGame->query($sql);
    while ($row =& $res->fetchRow()){
    	$mena[$row['mena_id']] = $row['kurz'];
    }
    
    $this->dbGame->autocommit(false);
    
    
  	$sql = "select a.admin_id,a.data,a.status,a.date,a.amount,b.mena_id,b.nick,b.jmeno,b.prijmeni,b.e_testovaci,b.vyhernost,b.user_id,b.book_info from coupon_data a inner join uzivatel b on a.user_id=b.user_id where  ( (a.status=1 and a.date>date_sub(now(),INTERVAL ".(LIVE_WAIT_TIME)." SECOND)))";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sportu a udalosti',"admin_ex_db");
    
    while ($row =& $res->fetchRow()){
        
    	
    	$status_your_live = false; //priznak zda je na tiketu live sazka daneho bookmakera
    	$data = Zend_Json::decode($row['data']);
    	
    	$status_your_live = false;
    	foreach($data['bet'] as $k=>$h){

        	if($this->validYourLiveBet($h['id_bet'])) $status_your_live = true;
        }
        
        if($status_your_live == false) continue;
        
    
    	$totalWin = 0;
    	$totalBet = 0;
    	$rate = $banker_rate = 1;
    	$klic = count($attr);
    	    	
        $attr[$klic]['hash'] = self::computeTicketHash($row['user_id'], $row['admin_id'], $row['amount'], $row['data']);
    	$attr[$klic]['user'] = $row['user_id'];
    	$attr[$klic]['admin'] = $row['admin_id'];
    	$attr[$klic]['book_info'] = Help::Script(nl2br($row['book_info']));
    	//TODO Ma to byt v gm nebo ne? Ted je. Jinak by se muselo dat , false
    	$attr[$klic]['datum'] = It6_Date::fromDbAsTimestamp($row['date']);
    	$attr[$klic]['jmeno'] = $row['jmeno'].' '.$row['prijmeni'].' ('.$row['nick'].' '.($row['e_testovaci']<>'test'?'':' - <strong>TEST</strong>').') '.(mb_strlen($attr[$klic]['book_info'])>0?'<span style="color:yellow">[i]</span>':'');
    	//TODO: reimplement UserSaldo -- use WS (same changes as in class AjaxServer)
    	$attr[$klic]['vyhernost'] = 'TODO: reimplement using WS'; //'<br />'.$this->UserSaldo($row['user_id']).' <br />'.$row['vyhernost'];
    	$attr[$klic]['bet'] = array();
    	//echo "<pre>";print_r($data);echo "</pre>";
    	if($data['type'] == 'simple'){
    		
    		$attr[$klic]['type'] = 'simple';
    		
    		foreach($data['bet'] as $k=>$h){

    			if($h['amount'] == 0 || $h['visible'] == 0) continue;
    			
    			$sql = "select home_team,away_team from live_event where event_id=(select x.event_id from live_sazka x where x.sazka_id=".intval($h['id_bet']).")";
                $res36 =& $this->dbGame->query($sql);
                if(DB::isError($res36)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sportu a udalosti',"admin_ex_db");
    
                $livet_text_add = '';
                if ($row36 =& $res36->fetchRow()){
    	           $livet_text_add = ' '.$row36['home_team'].' - '.$row36['away_team'].' ';
                }
        
    			$sql = "select a.*,b.nazev AS udalost_nazev from sazka_pohled a inner join typ b on a.typ_id=b.typ_id where a.sazka_id=".intval($h['id_bet'])."  and a.sloupec_id=".intval($h['id_col'])." and a.platny_od<now() order by a.platny_od desc limit 1";
                $res2 =& $this->dbGame->query($sql);
                if(DB::isError($res2)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sazky',"admin_ex_db");
    		    
                if($row2 =& $res2->fetchRow()){
                 
                 $text_add = '';
                 $sql = "select b.nazev AS unazev,c.nazev from sazky a inner join udalost b on a.udalost_id=b.udalost_id inner join sport c on b.sport_id=c.sport_id where a.sazka_id=".intval($h['id_bet']);
                 $res4 =& $this->dbGame->query($sql);
                 if(DB::isError($res4)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sazky',"admin_ex_db");
                 if($row4 =& $res4->fetchRow()){
                  	 
                 	$p = $preklady->findPreklad($row4['unazev'],1);$p = $p[1];
                    $text_add = '<strong>'.$row4['nazev'].'->'.$p.'</strong><br/>';
                 }
                 
                 $klic2 = count($attr[$klic]['bet']);
                 $attr[$klic]['bet'][$klic2]['text'] = $text_add.$livet_text_add.$row2['text'];
                 $attr[$klic]['bet'][$klic2]['typ'] = $row2['udalost_nazev'];
                 $attr[$klic]['bet'][$klic2]['sloupec'] = $row2['nazev'];
                 $attr[$klic]['bet'][$klic2]['kurz'] = $row2['kurz'];
                 $attr[$klic]['bet'][$klic2]['live'] = ($row2['live'] == 1?"[LIVE]":"");
                 $attr[$klic]['bet'][$klic2]['vsazeno'] = ($h['amount']/$mena[$row['mena_id']]); //TODO: ? use It6_Models_Currency
                 $totalBet += ($h['amount']/$mena[$row['mena_id']]);
                 $totalWin += (($h['amount']/$mena[$row['mena_id']]) * $row2['kurz']);
                }
                
    		}
    		
    		$attr[$klic]['win'] = $totalWin;
    		$attr[$klic]['vsazeno'] = $totalBet;
    	
    	}
    	else if($data['type'] == 'kombi'){
    		
    		$attr[$klic]['vsazeno'] = ($data['totalSum']/$mena[$row['mena_id']]); //TODO: ? use It6_Models_Currency
    	    $attr[$klic]['type'] = 'kombi';
    	    
    		foreach($data['bet'] as $k=>$h){

    			if($h['visible'] == 0) continue;
    			 
    			$sql = "select home_team,away_team from live_event where event_id=(select x.event_id from live_sazka x where x.sazka_id=".intval($h['id_bet']).")";
                $res36 =& $this->dbGame->query($sql);
                if(DB::isError($res36)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sportu a udalosti',"admin_ex_db");
    
                $livet_text_add = '';
                if ($row36 =& $res36->fetchRow()){
    	           $livet_text_add = ' '.$row36['home_team'].' - '.$row36['away_team'].' ';
                }
                
    			$sql = "select a.*,b.nazev AS udalost_nazev from sazka_pohled a inner join typ b on a.typ_id=b.typ_id where a.sazka_id=".intval($h['id_bet'])."  and a.sloupec_id=".intval($h['id_col'])." and a.platny_od<now() order by a.platny_od desc limit 1";
                $res2 =& $this->dbGame->query($sql);
                if(DB::isError($res2)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sazky',"admin_ex_db");
    		    
                if($row2 =& $res2->fetchRow()){
                 
                 $text_add = '';
                 $sql = "select b.nazev AS unazev,c.nazev from sazky a inner join udalost b on a.udalost_id=b.udalost_id inner join sport c on b.sport_id=c.sport_id where a.sazka_id=".intval($h['id_bet']);
                 $res4 =& $this->dbGame->query($sql);
                 if(DB::isError($res4)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sazky',"admin_ex_db");
                 if($row4 =& $res4->fetchRow()){
                  	 
                 	$p = $preklady->findPreklad($row4['unazev'],1);$p = $p[1];
                    $text_add = '<strong>'.$row4['nazev'].'->'.$p.'</strong><br/>';
                 }
                
                 $klic2 = count($attr[$klic]['bet']);
                 $attr[$klic]['bet'][$klic2]['text'] = $text_add.$livet_text_add.$row2['text'];
                 $attr[$klic]['bet'][$klic2]['typ'] = $row2['udalost_nazev'];
                 $attr[$klic]['bet'][$klic2]['live'] = ($row2['live'] == 1?"[LIVE]":"");
                 $attr[$klic]['bet'][$klic2]['sloupec'] = $row2['nazev'];
                 $attr[$klic]['bet'][$klic2]['kurz'] = $row2['kurz'];
                 $rate *= $row2['kurz'];
                }
                
    		}
    		
    		$attr[$klic]['win'] = ($attr[$klic]['vsazeno']*$rate);
    	}
        else if($data['type'] == 'system'){
    		
        	$bankerNum = $totaRows = 0;
        	
    		$attr[$klic]['vsazeno'] = ($data['totalSum']/$mena[$row['mena_id']]); //TODO: ? use It6_Models_Currency
    	    $attr[$klic]['type'] = 'system';
  
    	     
    	    foreach($data['system'] as $k2=>$h2){
    	      
    	     
    	      
    	      $banker_rate = 1;
    		  $attr[$klic]['system'][$k2]['min'] = 0;
    	      $attr[$klic]['system'][$k2]['max'] = 0;
    	    
    	      foreach($data['bet'] as $k=>$h){

    			if($h['visible'] == 0) continue;
    			 
    			$sql = "select home_team,away_team from live_event where event_id=(select x.event_id from live_sazka x where x.sazka_id=".intval($h['id_bet']).")";
                $res36 =& $this->dbGame->query($sql);
                if(DB::isError($res36)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sportu a udalosti',"admin_ex_db");
    
                $livet_text_add = '';
                if ($row36 =& $res36->fetchRow()){
    	           $livet_text_add = ' '.$row36['home_team'].' - '.$row36['away_team'].' ';
                }
                
    			$sql = "select a.*,b.nazev AS udalost_nazev from sazka_pohled a inner join typ b on a.typ_id=b.typ_id where a.sazka_id=".intval($h['id_bet'])."  and a.sloupec_id=".intval($h['id_col'])." and a.platny_od<now() order by a.platny_od desc limit 1";
                $res2 =& $this->dbGame->query($sql);
                if(DB::isError($res2)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sazky',"admin_ex_db");
    		    
                if($row2 =& $res2->fetchRow() && $k2 == 0){
                  
                  $text_add = '';
                  $sql = "select b.nazev AS unazev,c.nazev from sazky a inner join udalost b on a.udalost_id=b.udalost_id inner join sport c on b.sport_id=c.sport_id where a.sazka_id=".intval($h['id_bet']);
                  $res4 =& $this->dbGame->query($sql);
                  if(DB::isError($res4)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sazky',"admin_ex_db");
                  if($row4 =& $res4->fetchRow()){
                  	 
                 	$p = $preklady->findPreklad($row4['unazev'],1);$p = $p[1];
                    $text_add = '<strong>'.$row4['nazev'].'->'.$p.'</strong><br/>';
                  }
                 
                  $klic2 = count($attr[$klic]['bet']);
                  $attr[$klic]['bet'][$klic2]['text'] = $text_add.$livet_text_add.$row2['text'];
                  $attr[$klic]['bet'][$klic2]['typ'] = $row2['udalost_nazev'];
                  $attr[$klic]['bet'][$klic2]['live'] = ($row2['live'] == 1?"[LIVE]":"");
                  $attr[$klic]['bet'][$klic2]['sloupec'] = $row2['nazev'];
                  $attr[$klic]['bet'][$klic2]['kurz'] = $row2['kurz'];
                  
                  if($h['banker']==1){$banker_rate = $banker_rate * $row2['kurz'];$attr[$klic]['bet'][$klic2]['banker'] = 1;$bankerNum++;}else{
                   $attr[$klic]['bet'][$klic2]['banker'] = 0;
                   $klic3 = count($system_ar);
      	           $system_ar[$klic3]['sazka_id'] = intval($h['id_bet']);
      	           $system_ar[$klic3]['rate'] = $row2['kurz'];
                  }
                 
                }
                
    		  }
    		  
    		  $attr[$klic]['system'][$k2]['info'] = ($bankerNum>0?$bankerNum.' banker + ':'').'system '.$h2['num'].' z '.$h2['from'];
    		  $totaRows += $h2['count'];
    		  
    	    }
    	    
    	    $castka_rad = ($attr[$klic]['vsazeno']/$totaRows);
    	   
    		foreach($data['system'] as $k2=>$h2){
    			
	           $GLOBALS['system_special_ar'] = Array();
            
	           $attr[$klic]['system'][$k2]['win'] =  Help::ReQSystem(0,$h2['num'],0,$system_ar,$banker_rate,1,$castka_rad);
	           $totalWin +=  $attr[$klic]['system'][$k2]['win'];
              	 
	           foreach($GLOBALS['system_special_ar'] as $h4){
	           	  if($attr[$klic]['system'][$k2]['min'] == 0 || $attr[$klic]['system'][$k2]['min']>$h4['vyhra'])$attr[$klic]['system'][$k2]['min'] = $h4['vyhra'];
    	          if($attr[$klic]['system'][$k2]['max'] == 0 || $attr[$klic]['system'][$k2]['max']<$h4['vyhra'])$attr[$klic]['system'][$k2]['max'] = $h4['vyhra'];
	           }
	           
    	    }
    	    
    	
    	    
    		$attr[$klic]['win'] =  $totalWin;
    	
        }
    	
  
  
        
    	if(count($attr[$klic]['bet']) == 0 ) unset($attr[$klic]);
    	
    }
    
    $this->dbGame->commit(false);
    $this->dbGame->autocommit(true);
    
    echo json_encode($attr);
  }
  
    /**
  * Kontrola zda obsahuje sazky k dane live sazce
  *
  * @param array $id id sazky
  * return bool
  */
  public function validYourLiveBet($id){
      
  	  if(!isset($_GET['live'])) return;  
  	
      $sql = "select event_id from live where event_id=".intval($_GET['live'])." and sazka_id=".$id;
      $res2 =& $this->dbGame->query($sql);
      if(DB::isError($res2)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sazky',"admin_ex_db");
    		    
      if($res2->numRows() > 0) return true; else return false;
  	
  }
  
  /**
  * Kontrola tiketu 
  *  
  */
  public function BookCheck(){
  	
  	   	  	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta http-equiv="Content-Style-Type" content="text/css">
        <meta http-equiv="Content-Script-Type" content="text/javascript">
<title>Povolování tiketů - '.$_GET['team'].'</title>
		  <script src="/js/jquery.js"></script>
 <script src="/js/mainscript.js" type="text/javascript" language="javascript"></script>
 <script> 
  
  json_stop = new Object();

  $(document).ready(function(){
     
     var  json_info_pole = new Object();
       json_info_user = new Array();
     var  json_info_new = new Array();


     jQuery.extend({
       LoadAjaxData: function() { 
    
        $(\'#load\').css("display","block");

        $.ajax({
         url: "ajax.server.php",
         data:{work:6,tab:1,live:'.$_GET['live'].'},
         type: "GET",
         dataType: "json",
         error: function(){
         window.status = "Error loading XML document";
         },
         success: function(json){
       
           /*if(typeof json[0] != \'undefined\'){window.focus();}*/
           $(\'#load\').css("display","none");
           /*window.status = "OK";*/
           $("tr[id^=\'nick_\'] > td").css("background", "white");
           json_info_new = new Array();

           for(vl in json){
              statusx = true;
             
              for(var y=0;y<json_info_user.length;y++){
                if(json_info_user[y] == json[vl][\'user\'] && json[vl][\'datum\']== json_info_pole[parseInt(json[vl][\'user\'])][\'datum\']) statusx=false;
                
              }
          
              if(statusx == true){
              
              for(var y=0;y<json_info_user.length;y++){
                if(json_info_user[y] == json[vl][\'user\']) json_info_user[y] = 0;
                
              }
              $("#nick_"+json[vl][\'user\']).remove();
              $("tr[name=\'bet_"+json[vl][\'user\']+"\']").remove();

              json_l = json_info_user.length;
              json_info_user[json_l] = json[vl][\'user\'];
              json_info_new[json_info_new.length] = json[vl][\'user\'];
              json_info_pole[parseInt(json[vl][\'user\'])] = json[vl];              
                                  
              
              }
              
           }

           jQuery.ShowBet();   

         }
        });

       }
     });
     
     jQuery.extend({
       ShowBet:function(){ 
           
         

           for(var y=0;y<json_info_new.length;y++){
             var text = "";
            
             if(json_info_pole[json_info_new[y]][\'type\'] == "simple"){
                
                $(\'#bookdata\').append(\'<tr id="nick_\'+json_info_new[y]+\'"  onmouseover="BetShow(\'+json_info_new[y]+\')" ><td style="background-color:#db7093"><span id="bi_\'+json_info_pole[json_info_new[y]][\'user\']+\'" onmouseover="jQuery.BInfo(1,\\\'\'+json_info_pole[json_info_new[y]][\'book_info\']+\'\\\',\'+json_info_pole[json_info_new[y]][\'user\']+\')" onmouseout="jQuery.BInfo(0)">\'+json_info_pole[json_info_new[y]][\'jmeno\']+\'</span> </strong>\'+json_info_pole[json_info_new[y]][\'vyhernost\']+\'%</strong></td><td>\'+json_info_pole[json_info_new[y]][\'vsazeno\'].toFixed(2)+\'</td><td>\'+json_info_pole[json_info_new[y]][\'win\'].toFixed(2)+\'</td><td>Jednoduchá</td><td><a href="javascript:jQuery.UseBet(\'+json_info_new[y]+\',1,\'+json_info_pole[json_info_new[y]][\'vsazeno\'].toFixed(2)+\',\\\'\'+json_info_pole[json_info_new[y]][\'hash\']+\'\\\');"><img src="_clip/check_yes.gif" alt="Povolit" class="no" /></a> &nbsp;&nbsp; <a href="javascript:jQuery.UseBet(\'+json_info_new[y]+\',2,\'+json_info_pole[json_info_new[y]][\'vsazeno\'].toFixed(2)+\',\\\'\'+json_info_pole[json_info_new[y]][\'hash\']+\'\\\');"><img src="_clip/check_no.gif" alt="Zamítnout" class="no" /></a> &nbsp;&nbsp;</td><td>&nbsp;</td><td name="cas_\'+json_info_new[y]+\'" hodnota="\'+json_info_new[y]+\'" >0</td></tr>\');       
                 // $(\'#bookdata\').append(\'<tr id="nick_\'+json_info_new[y]+\'"   ><td style="background-color:red">\'+json_info_pole[json_info_new[y]][\'jmeno\']+\' </strong>\'+json_info_pole[json_info_new[y]][\'vyhernost\']+\'%</strong></td><td>\'+json_info_pole[json_info_new[y]][\'vsazeno\'].toFixed(2)+\'</td><td>\'+json_info_pole[json_info_new[y]][\'win\'].toFixed(2)+\'</td><td>Jednoduchá</td><td><a href="javascript:jQuery.UseBet(\'+json_info_new[y]+\',1,\'+json_info_pole[json_info_new[y]][\'vsazeno\'].toFixed(2)+\');"><img src="_clip/check_yes.gif" alt="Povolit" class="no" /></a> &nbsp;&nbsp; <a href="javascript:jQuery.UseBet(\'+json_info_new[y]+\',2,\'+json_info_pole[json_info_new[y]][\'vsazeno\'].toFixed(2)+\');"><img src="_clip/check_no.gif" alt="Zamítnout" class="no" /></a> &nbsp;&nbsp; <a href="javascript:jQuery.UseBet(\'+json_info_new[y]+\',3,\'+json_info_pole[json_info_new[y]][\'vsazeno\'].toFixed(2)+\',\'+json_info_pole[json_info_new[y]][\'hash\']+\');"><img src="_clip/check_clock.gif" alt="Zastavit" class="no" /></a> &nbsp;&nbsp;  <input type="text" class="json_text" value="1" id="other_\'+json_info_new[y]+\'"/> <a href="javascript:jQuery.UseBet(\'+json_info_new[y]+\',4,\'+json_info_pole[json_info_new[y]][\'vsazeno\'].toFixed(2)+\');"><img src="_clip/check_euro.gif" alt="Jiná částka" class="no" /></a></td><td>&nbsp;</td><td name="cas_\'+json_info_new[y]+\'" hodnota="\'+json_info_new[y]+\'" >0</td></tr>\');       
                for(var vl in json_info_pole[json_info_new[y]][\'bet\']){
                     
                  $(\'#bookdata\').append(\'<tr name="bet_\'+json_info_new[y]+\'" style="display:none"><td colspan="3" class="json_bet">\'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'text\']+\' \'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'live\']+\'</td><td class="json_bet" colspan="2">(\'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'sloupec\']+\' - \'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'typ\']+\') </td><td class="json_bet">\'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'kurz\']+\' </td><td class="json_bet">\'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'vsazeno\']+\' EUR (TODO: use central currency) </td></tr>\');
                 //  $(\'#bookdata\').append(\'<tr name="bet_\'+json_info_new[y]+\'" ><td  class="json_bet"  colspan="7" >\'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'text\']+\'(\'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'sloupec\']+\' - \'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'typ\']+\') \'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'kurz\']+\' </td></tr>\');
                }


             }
             else if(json_info_pole[json_info_new[y]][\'type\'] == "kombi"){
                
                $(\'#bookdata\').append(\'<tr id="nick_\'+json_info_new[y]+\'"  onmouseover="BetShow(\'+json_info_new[y]+\')" ><td style="background-color:#db7093"><span id="bi_\'+json_info_pole[json_info_new[y]][\'user\']+\'" onmouseover="jQuery.BInfo(1,\\\'\'+json_info_pole[json_info_new[y]][\'book_info\']+\'\\\',\'+json_info_pole[json_info_new[y]][\'user\']+\')" onmouseout="jQuery.BInfo(0)">\'+json_info_pole[json_info_new[y]][\'jmeno\']+\'</span> </strong>\'+json_info_pole[json_info_new[y]][\'vyhernost\']+\'%</strong></td><td>\'+json_info_pole[json_info_new[y]][\'vsazeno\'].toFixed(2)+\'</td><td>\'+json_info_pole[json_info_new[y]][\'win\'].toFixed(2)+\'</td><td>Kombinovaná</td><td><a href="javascript:jQuery.UseBet(\'+json_info_new[y]+\',1,\'+json_info_pole[json_info_new[y]][\'vsazeno\'].toFixed(2)+\',\\\'\'+json_info_pole[json_info_new[y]][\'hash\']+\'\\\');"><img src="_clip/check_yes.gif" alt="Povolit" class="no" /></a> &nbsp;&nbsp;<a href="javascript:jQuery.UseBet(\'+json_info_new[y]+\',2,\'+json_info_pole[json_info_new[y]][\'vsazeno\'].toFixed(2)+\',\\\'\'+json_info_pole[json_info_new[y]][\'hash\']+\'\\\');"><img src="_clip/check_no.gif" alt="Zamítnout" class="no" /></a> &nbsp;&nbsp;</td><td>&nbsp;</td><td name="cas_\'+json_info_new[y]+\'" hodnota="\'+json_info_new[y]+\'" >0</td></tr>\');       
               
                for(var vl in json_info_pole[json_info_new[y]][\'bet\']){
                     
                  $(\'#bookdata\').append(\'<tr name="bet_\'+json_info_new[y]+\'" style="display:none"><td colspan="4" class="json_bet">\'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'text\']+\' \'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'live\']+\'</td><td class="json_bet" colspan="2">(\'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'sloupec\']+\' - \'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'typ\']+\') </td><td class="json_bet">\'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'kurz\']+\' </td></tr>\');

                }

             }
             else if(json_info_pole[json_info_new[y]][\'type\'] == "system"){
                
                var info_system = \'\';
                
                for(var vll in json_info_pole[json_info_new[y]][\'system\']) {

                   info_system += json_info_pole[json_info_new[y]][\'system\'][vll][\'info\']+\': \'+json_info_pole[json_info_new[y]][\'system\'][vll][\'win\'].toFixed(2)+\' ( \'+json_info_pole[json_info_new[y]][\'system\'][vll][\'min\'].toFixed(2)+\' - \'+json_info_pole[json_info_new[y]][\'system\'][vll][\'max\'].toFixed(2)+\' ) <br /> \';            

                }               

                $(\'#bookdata\').append(\'<tr id="nick_\'+json_info_new[y]+\'"  onmouseover="BetShow(\'+json_info_new[y]+\')" ><td style="background-color:#db7093"><span id="bi_\'+json_info_pole[json_info_new[y]][\'user\']+\'" onmouseover="jQuery.BInfo(1,\\\'\'+json_info_pole[json_info_new[y]][\'book_info\']+\'\\\',\'+json_info_pole[json_info_new[y]][\'user\']+\')" onmouseout="jQuery.BInfo(0)">\'+json_info_pole[json_info_new[y]][\'jmeno\']+\' </span></strong>\'+json_info_pole[json_info_new[y]][\'vyhernost\']+\'%</strong></td><td>\'+json_info_pole[json_info_new[y]][\'vsazeno\'].toFixed(2)+\'</td><td>\'+json_info_pole[json_info_new[y]][\'win\'].toFixed(2)+\'</td><td>Systém</td><td><a href="javascript:jQuery.UseBet(\'+json_info_new[y]+\',1,\'+json_info_pole[json_info_new[y]][\'vsazeno\'].toFixed(2)+\',\\\'\'+json_info_pole[json_info_new[y]][\'hash\']+\'\\\');"><img src="_clip/check_yes.gif" alt="Povolit" class="no" /></a> &nbsp;&nbsp;<a href="javascript:jQuery.UseBet(\'+json_info_new[y]+\',2,\'+json_info_pole[json_info_new[y]][\'vsazeno\'].toFixed(2)+\',\\\'\'+json_info_pole[json_info_new[y]][\'hash\']+\'\\\');"><img src="_clip/check_no.gif" alt="Zamítnout" class="no" /></a> &nbsp;&nbsp;</td><td nowrap="nowrap">\'+info_system+\'</td><td name="cas_\'+json_info_new[y]+\'" hodnota="\'+json_info_new[y]+\'" >0</td></tr>\');       
               
                for(var vl in json_info_pole[json_info_new[y]][\'bet\']){
                     
                  $(\'#bookdata\').append(\'<tr name="bet_\'+json_info_new[y]+\'" style="display:none"><td colspan="4" class="json_bet">\'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'text\']+\' \'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'live\']+\'</td><td class="json_bet" colspan="2">(\'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'sloupec\']+\' - \'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'typ\']+\') </td><td class="json_bet">\'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'kurz\']+\' \'+(json_info_pole[json_info_new[y]][\'bet\'][vl][\'banker\']==1?"<br /><strong>BANKER<strong>":"")+\'</td></tr>\');

                }

             }
              
            window.focus()

           }
          
       }
     });
     
     jQuery.extend({
       UseBet:function(uid,type,amount,hash){
           
          i = 0;          
         
         if(type==1 || type==2){
           
              for(var y=0;y<json_info_user.length;y++){
                
                if(json_info_user[y] == uid) json_info_user[y] = 0;
                
              }

            $("#nick_"+uid).html(\'\'); 
            $("tr[name=\'bet_"+uid+"\']").html(\'\');

         } 
         else if(type==4){
           i = $("#other_"+uid).attr("value");
           for(var y=0;y<json_info_user.length;y++){
                
                if(json_info_user[y] == uid) json_info_user[y] = 0;
                
              }

            $("#nick_"+uid).html(\'\');
            $("tr[name=\'bet_"+uid+"\']").html(\'\');
         }
         
         if(type==3){
          json_stop[uid] = 1;
         }

         $.ajax({
         url: "ajax.server.php",
         data:{work:6,tab:2,action:type,amount:amount,info:i,user:uid,hs:hash},
         type: "GET",
         dataType: "json",
         success: function(json){
  
         }
        });

       } 
     });

     //jQuery.LoadAjaxData();
     timex = setInterval("jQuery.LoadAjaxData()",5000);  
     var timex2 = setInterval("BetStopky()",1000);

  });
     
     function BetStopky(){
            
       $("td[name^=\'cas_\']").each(function(){
          $(this).text( parseInt($(this).text()) + 1 );
           if(parseInt($(this).text()) > '.(Constant::get('BOOK_CHECK_TIME_MORE')+Constant::get('BOOK_CHECK_TIME')).'){
                  
            $.ajax({
             url: "ajax.server.php",
             data:{work:6,tab:2,action:0,info:\'\',user:$(this).attr("hodnota")},
             type: "GET",
             dataType: "json",
             success: function(json){
  
             }
             });  
            for(var y=0;y<json_info_user.length;y++){
                if(json_info_user[y] == $(this).attr("hodnota")) json_info_user[y] = 0;
                
              }

            $("#nick_"+$(this).attr("hodnota")).remove();
            $("tr[name=\'bet_"+$(this).attr("hodnota")+"\']").remove();
            if(json_stop[$(this).attr("hodnota")] != \'undefined\')delete json_stop[$(this).attr("hodnota")];

           }

           if(parseInt($(this).text()) > '.(Constant::get('LIVE_WAIT_TIME_WITH_CONFIRM')+5).' && typeof json_stop[$(this).attr("hodnota")] == \'undefined\'){
 
            for(var y=0;y<json_info_user.length;y++){
                if(json_info_user[y] == $(this).attr("hodnota")) json_info_user[y] = 0;
                
            }

            $("#nick_"+$(this).attr("hodnota")).remove();
            $("tr[name=\'bet_"+$(this).attr("hodnota")+"\']").remove();
            if(json_stop[$(this).attr("hodnota")] != \'undefined\')delete json_stop[$(this).attr("hodnota")];

           }

       });

     }

     function BetShow(n){

      $("tr[name^=\'bet_\']").css("display","none");
      $("tr[name=\'bet_"+n+"\']").css("display","");

     }
  </script>
  <link rel="stylesheet" href="css/blueprint/screen.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="css/blueprint/print.css" type="text/css" media="print">
<!--[if lt IE 8]>
  <link rel="stylesheet" href="css/blueprint/ie.css" type="text/css" media="screen, projection">
<![endif]-->

 <link rel="stylesheet" href="css/adm.css" media="screen" type="text/css" />


</head>
<body>
<div id="binfomes"></div>
	<div id="load"><img src="_clip/loading.gif" alt="Nahrávám" class="no" /></div>
   <table id="bookdata" class="bvfd2">
       <tr><th>Zákazník</th><th>Vsadil</th><th>Výhra</th><th>Druh</th><th>Činnost</th><th>Info</th><th>Čas</th></tr>

   </table>

</body>
</html>';
  	
  }
  
  /**
  * Vypise pozadovane informace o sázce
  *  
  */
  public function BetInfo(){
  	
  	$tabs = array(1=>'Info',2=>'Nasázeno',3=>'Tikety',4=>'Chat');
  	
    if(!isset($_GET['id'])) {echo "ID NOT SPECIFIED"; return;}
  	
    #zakaldni info o sazce#
    if(!isset($_GET['type']) || $_GET['type']==1){
      
      $preklad = new Preklady();
      
      $sazka = $sloupec = $book = $podtyp = array();
      
      $ob = new SazkaZobraz();
      $ob->runAction(true);
       
      #Vyber bookmakeru#
      $sql = "select jmeno,prijmeni,nick,bookmaker_id,super from bookmaker";
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sportu a udalosti',"admin_ex_db");
   
      while ($row =& $res->fetchRow()){
   
       $book[$row['bookmaker_id']]['nick'] = $row['nick'];
	   $book[$row['bookmaker_id']]['jmeno'] = $row['jmeno']." ".$row['prijmeni'];
	  
      }
   
      $where = "a.sazka_id=".intval($_GET['id']);
      $sql = "select a.sazka_id,a.proplatil_bookmaker,a.info,a.proplacena,a.platna_od,a.risk_limit,a.risk_limit_balance,a.platna_do,a.status,a.live,a.bookmaker_id,a.vysledek,a.udalost_id,a.typ_id,a.podtyp_id,a.overena,a.text,a.jednoducha,b.poradi,b.kurz,b.platny_od,c.nazev,c.sloupec_id,c.poradi AS poradi_sloupec from sazky a inner join(sazka_kurz b inner join podtyp_sloupce c on b.sloupec_id=c.sloupec_id) on a.sazka_id=b.sazka_id inner join udalost u on u.udalost_id=a.udalost_id where ".$where." and b.platny_od=(select d.platny_od from sazka_kurz d where d.sazka_id=a.sazka_id order by d.platny_od desc limit 1)  order by a.udalost_id,a.typ_id,a.sazka_id,a.podtyp_id,c.poradi";
      $res2 =& $this->dbGame->query($sql);
      if(DB::isError($res2)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber z tabulky sazky, sazka_kurz a podtyp_sloupec',"admin_ex_db");
      
       while ($row =& $res2->fetchRow()){ 
     
	 if(!isset($help_ar[$row['sazka_id']])){
	 
	  if(!isset($sport_udalost[$row['udalost_id']]['pocet']))$sport_udalost[$row['udalost_id']]['pocet'] = 1;
	  else $sport_udalost[$row['udalost_id']]['pocet']++;
    
	 }
	 
	 $help_ar[$row['sazka_id']] = 1;
	 
	 if(isset($_REQUEST['udalost']) && is_array($_REQUEST['udalost']) && !in_array(0,$_REQUEST['udalost']) && !in_array($row['udalost_id'],$_REQUEST['udalost']) ) continue; 
	
	
	if(!isset($sazka[$row['sazka_id']]['udalost'])){
	 
     //$sazka[$row['sazka_id']]['podtyp_id'] = $podtyp[$row['podtyp_id']][''];
	 $sazka[$row['sazka_id']]['podtyp_radek'] = $podtyp[$row['podtyp_id']]['radek'];
	 $sazka[$row['sazka_id']]['podtyp_max'] = $podtyp[$row['podtyp_id']]['sloupec_pocet_max'];
	 $sazka[$row['sazka_id']]['podtyp_text'] = $podtyp[$row['podtyp_id']]['text'];
	 $sazka[$row['sazka_id']]['platna_od'] = It6_Date::fromDb($row['platna_od']);
	 $sazka[$row['sazka_id']]['platna_do'] = It6_Date::fromDb($row['platna_do']);
	 $sazka[$row['sazka_id']]['status'] = $row['status'];
	 $sazka[$row['sazka_id']]['info'] = $row['info'];
	 $sazka[$row['sazka_id']]['bookmaker'] = $book[$row['bookmaker_id']]['jmeno']." (".$book[$row['bookmaker_id']]['nick'].")";
	 $sazka[$row['sazka_id']]['udalost'] =  $sport_udalost[$row['udalost_id']]['udalost_nazev'];
	 $sazka[$row['sazka_id']]['udalost_id'] =  $row['udalost_id'];
	 $sazka[$row['sazka_id']]['sport'] =  $sport_udalost[$row['udalost_id']]['sport_nazev'];
	 $sazka[$row['sazka_id']]['live'] = $row['live'];
	 $sazka[$row['sazka_id']]['risk_limit'] = $row['risk_limit'];
	 $sazka[$row['sazka_id']]['risk_limit_balance'] = $row['risk_limit_balance'];
	 $sazka[$row['sazka_id']]['typ'] = $typ_ar[$row['typ_id']];
	 $sazka[$row['sazka_id']]['typ_id'] = $row['typ_id'];
	 $sazka[$row['sazka_id']]['podtyp_id'] = $row['podtyp_id'];
	 $sazka[$row['sazka_id']]['text'] = $row['text'];
	 $sazka[$row['sazka_id']]['overena'] = $row['overena'];
	 if($row['overena'] != 0) $sazka[$row['sazka_id']]['overil'] = $book[$row['overena']]['jmeno']." (".$book[$row['overena']]['nick'].")";
	 $sazka[$row['sazka_id']]['proplacena'] = $row['proplacena'];
	 if(isset($book[$row['proplatil_bookmaker']]))$sazka[$row['sazka_id']]['proplatil'] = $book[$row['proplatil_bookmaker']]['jmeno']." (".$book[$row['proplatil_bookmaker']]['nick'].")";
	 $sazka[$row['sazka_id']]['jednoducha'] = $row['jednoducha'];
	 $sazka[$row['sazka_id']]['vysledek'] = explode(";",$row['vysledek']);
	 
	 $sazka_poradi[$row['udalost_id']][$row['typ_id']][$row['podtyp_id']][] = $row['sazka_id'];
	 
	}
    
	if(!isset($sazka[$row['sazka_id']]['kurzy'][$row['poradi']]['platny_od'])){
	
	  $sazka[$row['sazka_id']]['kurzy'][$row['poradi']]['platny_od'] = $row['platny_od'];
	 // $sazka[$row['sazka_id']]['kurzy'][$row['poradi']]['zruseny'] = $row['zruseny'];
	
	}
    
	$sazka[$row['sazka_id']]['max_poradi'] = (!isset($sazka[$row['sazka_id']]['max_poradi']) || $sazka[$row['sazka_id']]['max_poradi']<$row['poradi']?$row['poradi']:$sazka[$row['sazka_id']]['max_poradi']);
		 
	$sazka[$row['sazka_id']]['kurzy'][$row['poradi']]['sloupec'][$row['sloupec_id']] = $row['kurz'];
	
	if(!isset($sloupec[$row['sloupec_id']])){
	  
	  $r = $preklad->selectData("where lang_id=1 and index_pole='".Help::Slash($row['nazev'])."'"); 
	  if (!$row2 =& $r->fetchRow() || mb_strlen($row2['text']) < 0) $row2['text'] = $row['nazev'];
	  $sloupec[$row['sloupec_id']] = $row2['text'];
	
	}
	
   }
     
      
      
      
      
      
      if(!isset($_GET['type']) && !isset($_GET['fillagain'])) $this->FillTemplate($tabs,1,intval($_GET['id']),true);
      else if(isset($_GET['fillagain'])){ $this->FillTemplate($tabs,1,intval($_GET['id']),false,$ob->getContent().'<form method="post" action="ajax.server.php?work=1&id='.intval($_GET['id']).'&fillagain=1&type='.$_GET['type'].'">'.$ob->BetForm($sazka,$sloupec,true).'</form><script>'.$ob->jsscript.'</script>');}
      else {echo $ob->getContent();echo  '<form method="post" action="ajax.server.php?work=1&id='.intval($_GET['id']).'&'.($_GET['type']==1?'fillagain=1&':'').'type='.$_GET['type'].'">'.$ob->BetForm($sazka,$sloupec,true).'</form><script>'.$ob->jsscript.'</script>';}
    	
    }
    #Nasazeno#
    else if(isset($_GET['type']) && $_GET['type']==2){
    	
    	$_GET['act'] = "sazky";
        $_GET['sazka'] = intval($_GET['id']);
    	$_GET['name'] = '';
    	
    	$ob = new SazkaInfo();
    	$ob->runAction(true);
    	
    	echo $ob->getContent();
    	
    }
    
    #Tikety#
    else if(isset($_GET['type']) && $_GET['type']==3){
    	
    	$_POST['sazka_id'] = intval($_GET['id']);
  		$_POST['top'] = 1000000;
  		$ob = new SazkaTicket();
  		$ob->RunAction(true);
  		echo $ob->getContent();
    	
    }
    #Tikety#
    else if(isset($_GET['type']) && $_GET['type']==4){
    	
    	$_POST['sazka'] = intval($_GET['id']);
        $_GET['sazka'] = intval($_GET['id']);
  
  		$ob = new SazkaInfo();
  		if(isset($_POST['newchatmessage'])) $ob->CreateMessage();
  		$ob->InternalChat(true);
  		
  		if(isset($_GET['fillagain'])) $this->FillTemplate($tabs,1,intval($_GET['id']),false,'<form method="post" action="ajax.server.php?work=1&id='.intval($_GET['id']).'&'.($_GET['type']==4?'fillagain=1&':'').'type='.$_GET['type'].'">'.$ob->getContent().'</form>');
  		else{
  		  
  		  echo '<form method="post" action="ajax.server.php?work=1&id='.intval($_GET['id']).'&'.($_GET['type']==4?'fillagain=1&':'').'type='.$_GET['type'].'">';
  		  echo $ob->getContent();
    	  echo '</form>';
  		
  		}
    	
    }
    
  }
  
  /**
  * Vypise pozadovane informace o uzivateli
  *  
  */
  public function UserInfo(){
  	
  	if(!isset($_GET['id'])) {echo "ID NOT SPECIFIED"; return;}
  	
  	$data = '';
  	
  	#Zakladni data o uzivateli#
  	if(!isset($_GET['type']) || $_GET['type']==1){

  	  $sql = "select a.*,b.nazev AS zeme_nazev,c.zustatek,c.zetony,d.mena_text from uzivatel a inner join mena d on a.mena_id=d.mena_id inner join uzivatel_im_data c on a.user_id=c.user_id inner join zeme b on a.zeme_id=b.zeme_id where a.user_id=".intval($_GET['id']);
      $res3 =& $this->dbGame->query($sql);
      if(DB::isError($res3)) { $this->dbGame->rollback();throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber z pohledu  ticket_pohled',"admin_ex_db");}
  	  
      $preklady = new Preklady();
      
      if ($row3 =& $res3->fetchRow()){
        
      	$zeme = $preklady->FindPreklad($row3['zeme_nazev'],1);
      	
      	$data .= '<table cellpadding="4">';
      	$data .= '<tr><td><strong>Jméno:</strong></td><td>'.Help::Html($row3['jmeno']).'</td><td><strong>Přijmení:</strong></td><td>'.Help::Html($row3['prijmeni']).'</td></tr>';
      	$data .= '<tr><td><strong>Nick:</strong></td><td>'.Help::Html($row3['nick']).'</td><td><strong>Email:</strong></td><td>'.Help::Html($row3['email']).'</td></tr>';
      	$data .= '<tr><td><strong>Země:</strong></td><td>'.Help::Html($zeme[1]).'</td><td><strong>Místo</strong></td><td>'.Help::Html($row3['misto']).'</td></tr>';
      	$data .= '<tr><td><strong>Ulice:</strong></td><td>'.Help::Html($row3['ulice']).'</td><td><strong>Datum narození:</strong></td><td>'.It6_Date::fromDbAsDate($row3['datum_narozeni']).'</td></tr>';
      	$data .= '<tr><td><strong>Zůstatek</strong></td><td>'.Help::Html($row3['zustatek']).'</td><td><strong>Žetony</strong></td><td>'.Help::Html($row3['zetony']).'</td></tr>';
      	$data .= '<tr><td><strong>Měna:</strong></td><td>'.Help::Html($row3['mena_text']).'</td><td><strong>Pohlaví:</strong></td><td>'.Help::Html($row3['pohlavi']).'</td></tr>';
      	$data .= '<tr><td><strong>Výhernost sázky:</strong></td><td>'.Help::Html($row3['vyhernost']).'</td><td><strong>Finance rating:</strong></td><td>'.Help::Html($row3['finance_rating']).'</td></tr>';

      	$data .= '<table>';
      
      }
      
      $tabs = array(1=>'Info',2=>'Akce',3=>'Tikety',4=>'Stat. sázky',5=>'Stat. hry');

                
      if(!isset($_GET['type'])) $this->FillTemplate($tabs,2,intval($_GET['id']),true);
      else echo $data;
  		
  	}
    #Akce#
  	else if(isset($_GET['type']) && $_GET['type']==2){
  		$_REQUEST['user'] = intval($_GET['id']);
  		$ob = new StatistikyUzivatelAkce();
  		$ob->RunAction(true);
  		echo $ob->getContent();
  		
  	}
    #Tikety#
  	else if((isset($_GET['type']) && $_GET['type']==3) ){
  		$_POST['user'] = intval($_GET['id']);
  		$_POST['top'] = 1000000;
  		$ob = new SazkaTicket();
  		$ob->RunAction(true);
  		echo $ob->getContent();
  		
  	}
  	
    #Sazky statisitky#
  	else if((isset($_GET['type']) && $_GET['type']==4) ){
  		$_POST['user'] = intval($_GET['id']);
  		$ob = new StatistikySazky();
  		$ob->TicketInfo(true);
  		echo $ob->getContent();
  		
  	}
    #Hry statisitky#
  	else if((isset($_GET['type']) && $_GET['type']==5) ){
  		$_POST['user'] = intval($_GET['id']);
  		$_POST['top_info'] = 1;
  		$ob = new Statistiky();
  		$ob->HryStat(true);
  		echo $ob->getContent();
  		
  	}
  	
  }
  
    /**
  * Vypise na obrazovku pozadovana data
  *  
  * @param array $tabs zalozky
  * @param int $work druh informaci
  * @param int $uid  id 
  * @param bool $data ma se default kliknout prvni zalozka
  * @param string $data_text text ktery se da do 1 fragmentu
  */
   public function FillTemplate(array $tabs,$work=NULL,$uid=NULL,$data=false,$data_text=''){
   	
   	$li = $tb = '';
   	
   	foreach($tabs as $k=>$h){
       
   	 	$li .= ' <li><a href="#fragment-'.$k.'"><span>'.Help::Html($h).'</span></a></li>';
   		$tb .= ' <div id="fragment-'.$k.'">'.($k==1 || $k==4?$data_text:'').'</div>';
   		
   	}
   	
   	  	$temp = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta http-equiv="Content-Style-Type" content="text/css">
        <meta http-equiv="Content-Script-Type" content="text/javascript">
	  <link rel="stylesheet" href="/css/onlinebook.css" type="text/css" media="screen" >

     <title>Povolování Live sázek - '.$_GET['team'].'</title>
 
  <script src="/js/jquery.js"></script>
 <script src="/js/mainscript.js" type="text/javascript" language="javascript"></script>
  <script>
  $(document).ready(function(){
       $("#container-1 > ul").tabs({ unselected: '.(mb_strlen($data_text)>0?'false':'true').',spinner: \'Nahrávám...\',fxFade: false ,show: function(i, s,h){$("#container-1 > ul").tabsLoad($("#container-1 > ul").tabsSelected( ) ,\'ajax.server.php?work={WORK}&id={ID}&type=\'+$("#container-1 > ul").tabsSelected( ));} });
	 // $("#container-1 > ul").tabs(0,{ unselected: false,spinner: \'Nahrávám...\' });
     '.($data==false?'':'$("#container-1 > ul").tabsClick(1);').'
  });
  </script>
  
</head>
<body>
  <link rel="stylesheet" href="/js/jquery.ui/themes/flora/flora.all.css" type="text/css" media="screen" title="Flora (Default)">

<script type="text/javascript" src="/js/jquery.ui/ui.tabs.js"></script>
<h1>Info</h1>
        <div id="container-1">
            <ul>
                '.$li.'
            </ul>
      '.$tb.'
        </div>
		
</body>
</html>';
   	  	
   	  	$temp = str_replace("{ID}",$uid,$temp);
   	  	$temp = str_replace("{WORK}",$work,$temp);
   	  	echo $temp;
   	  	
   }
   
}

?>