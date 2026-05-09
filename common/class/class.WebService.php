<?php


class WebService{
	
/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */  
 private static $dbGame;
	
/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */  
 private static $dbSession;
 
/**
 * urcuje pod jakym loginem je uzivatel rpihlasen a podle toho muze urcovat prava na metody
 * @access private
 * @var int
 */  
 public static $loginStatus;

/**
 * spojeni na DB
 * 
 * 
 * @return array
 */
 public static function DbConnect(){
 
    self::$dbGame = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
    if (DB::isError(self::$dbGame)) {
      throw new ExHandler(self::$dbGame->getMessage(),"admin_ex_db");
    }
    self::$dbGame->setFetchMode(DB_FETCHMODE_ASSOC);
    $sql = "set names 'utf8'";
    $res =& self::$dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");
    
    self::$dbSession = DB::connect(SESDATABASE ."://". SESMY_USER .":". SESMY_PASS ."@". SESMY_HOST ."/". SESMY_DB);
    if (DB::isError(self::$dbSession)) {
      throw new ExHandler(self::$dbSession->getMessage(),"admin_ex_db");
    }
    self::$dbSession->setFetchMode(DB_FETCHMODE_ASSOC);
    $sql = "set names 'utf8'";
    $res =& self::$dbSession->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");
   
 }

 /**
 * Nastavuje login
 * 
 * @param int $ls   status prihlaseni
 * 
 * @return void
 */
 public static function setLoginStatus($ls = 0){
   
   self::$loginStatus = $ls;

 }

 /**
 * Nastavuje login
 * 
 * @return int
 */
 public static function getLoginStatus($ses){
   
     $sth = self::$dbSession->prepare("select loginStatus from  webservice_session where ses_id=?");
     if (PEAR::isError($sth))  return 0;

     $res2 =& self::$dbSession->execute($sth,array($ses));
     if (PEAR::isError($res2))  return 0;
     
     if ($row =& $res2->fetchRow()){

         return $row['loginStatus'];        

     }else return 0;
 }

/**
 * Nastavuje session
 * 
 * @param string $token   ses id
*  @param int loginStatus   jake prihlaseni
 * 
 * @return bool
 */
 public static function SetSession($token,$loginStatus=0){
     
 	 self::DbConnect();
 	
     $sth = self::$dbSession->prepare("insert into webservice_session values (?,?,?)");
     if (PEAR::isError($sth))  return false;

     $res2 =& self::$dbSession->execute($sth,array($token,time(),$loginStatus));
     if (PEAR::isError($res2))  return false;
     else return true;
     
 }
 
 public static function getLive($token){
     
 	 self::DbConnect();
 	 
     $sth = self::$dbGame->prepare("SELECT
										a.event_id,
									    a.home_team,
									    a.away_team,
									    date_format(a.start_date,'%d.%m.%y %H:%i') AS start_date    
									FROM live_event a
									WHERE a.start_date > Now()
									ORDER BY a.event_id ASC
									LIMIT 4");
     if (PEAR::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");

     $res2 =& self::$dbGame->execute($sth);
     if (PEAR::isError($res2))  throw new ExHandler($res2->getMessage().'Nepodarilo se se selectnout live saznky',"admin_ex_db");
     
     $string = '';
     
     while ($row =& $res2->fetchRow()){
     	$string .= $row['event_id'] . ';' . 
     			   $row['start_date'] . ';' . 
     			   $row['home_team'] . ';' .
     			   $row['away_team'] . ';';
     }
     
     return $string;
     
 }
 
/**
 * Vytvori tikcket, ktery byl povolen
 * 
 * @param string $user_id id uzivatele
 * @param string $ses_id id session
 * @return int
 */
 public static function createTicket($user_id,$ses){
    self::DbConnect();

    if(self::getLoginStatus($ses) != 2) return 0;
    
    $_G["lang_id"] = DEFAULT_LANG_ID;
    $_G["uid"] = $user_id;
    $_G["crws"] = true;

	$tlc = new TicketLifeCycle(self::$dbGame, $_G);
	
	$tlc->open("");
	if($tlc->checkTicket()) { // test ok
		if($tlc->storeTicket()) { // zalozit tiket se podarilo
			$ret = 1;
		} else { // chyba pri zakladani
    		$ret = 0;
		}
	} else { // chyba pri overeni
		$ret = 0;
	}
	$tlc->close();
	return $ret; 

 }


/**
 * Vraci Hot sazky
 * 
 * @param string $token   ses id
 * 
 * @return string
 */
 public static function GetHot($token){
     
 	 self::DbConnect();
 	 
     $sth = self::$dbGame->prepare("select b.sazka_id,b.sloupec_id,b.text,date_format(b.platna_do,'%d.%m.%y %H:%i') AS datum,b.kurz,b.nazev,b.poradi AS nsloupec from hot_bet a inner join sazka_pohled b on a.sazka_id=b.sazka_id  where b.typ_id=19 order by poradi desc;");
     if (PEAR::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");

     $res2 =& self::$dbGame->execute($sth);
     if (PEAR::isError($res2))  throw new ExHandler($res2->getMessage().'Nepodarilo se vytvorit live sazku',"admin_ex_db");
     
     $bet = array();
     
     while ($row =& $res2->fetchRow()){
         
        if(!isset($bet[$row['sazka_id']]['tx'])){
     	 	$bet[$row['sazka_id']]['tx'] = $row['text'];
     	 }
     	 
     	 if(!isset($bet[$row['sazka_id']]['sl'][$row['sloupec_id']])){
     	 	$bet[$row['sazka_id']]['sl'][$row['sloupec_id']] = $row['kurz'];
     	 }
     	
     }
     
     $vrat = '';
     
     foreach($bet as $h){
     	
     	$vrat .= $h['tx'] . ';';
     	
     	foreach($h['sl'] as $h2){
     		
     		$vrat .= $h2 . ';';
     		
     	}
     	
     	//$vrat .= ';';
     	
     }
     
     return $vrat;
     
 }
 
/**
 * Hleda session
 * 
 * @param string $token   ses id
 * 
 * @return bool
 */
 public static function GetSession($token){
     
 	 self::DbConnect();
 	
     $sth = self::$dbSession->prepare("select ses_id from webservice_session where ses_id=? and time>?");
     if (PEAR::isError($sth))  return false;

     $res2 =& self::$dbSession->execute($sth,array($token,(time()-3600)));
     if (PEAR::isError($res2))  return false;
     
     if($res2->numRows() == 0)
      return false;
     else 
      return true;
     
 }
 
/**
 * Vymaze session
 * 
 * @param string $token   ses id
 * 
 * @return bool
 */
 public static function DeleteSession($token){
     
 	 self::DbConnect();
 	
     $sth = self::$dbSession->prepare("delete from webservice_session where ses_id=?");
     if (PEAR::isError($sth))  return false;

     $res2 =& self::$dbSession->execute($sth,array($token));
     if (PEAR::isError($res2))  return false;
     
     if(self::$dbSession->affectedRows() == 0)
      return false;
     else 
      return true;
     
 }
 
/**
 * vraci seznam povolenych uzivatelu
 * 
 * 
 * @return array
 */
 public static function GetNick(){
 
 	return array(
 	  
 	    1=>"loader",
           2=>"ticketCreator",
 	
 	);

 }

/**
 * vraci seznam hesel k povolenych uzivatelu
 * 
 * 
 * @return array
 */
 public static function GetPass(){
 
 	return array(
 	  
 	    1=>"specialpassforloaderyeah",
           2=>"thisisthespecialpasswordforsomesuperman"
 	
 	);

 }
 
}

?>