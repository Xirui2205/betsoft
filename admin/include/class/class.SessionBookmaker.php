<?php
/*
 * @link       service
 
 Cookie enable
 
 Tridy pro praci se session autorizaci bookmakeru.
 Vsechny data se ukladaji v db, pouze ses_id v Cookie
 
 Pro kazdeho uzivatele je pri vstupu na stranky vygenerovana session.
 Ta vstupuje do ruznych stavu. Kazdy stav vyjadruje jinou situaci nebo roli ve ktere uzivatel je.
 
 !!V pripade pridani novych stavu se script musi na mnoha mistech opravit!!
 
 OK STAVY (stavy ve kterych je session stale aktivni)
 Pozor se stavy se napriklad pocita u statistik her kdy se napr kontroluji on-line hraci
 status = 0 neprihlaseny
 status = 2 prihlaseny ok 
 status = 3 prave zalogovany
 
 VYHOZENI STAVY (takova session je jiz neaktivni a uzivatel na ni nemuze dale provadet zadne akce)
 status = 1 vyhozeny vyprsela session
 status = 4 zniceni session z nejakeho duvodu je uvedeno ve sloupci zprava
 status = 5 zablokovany ucet
 status = 6 vygenerovane nove heslo musi zmenit
 status = 7 vypnute stranky
  
*/


/*
Trida poskytujici rozhranni, ktere je pouzito ve scriptech.
Umoznuje inicializaci, zruseni session a vraci aktualni status uzivatele

Priklad:

 if(!SesClass::open($this->db)) throw new ExHandler('Nepodarilo se inicializovat Session');
   $_SESSION['jmeno'] = "Ondrawewe";
   $_SESSION['dum'] = "Olomouc";
   SesClass::destroyVariable('jmeno');
   
    SesClass::close();
    echo $_SESSION['jmeno'];
	echo SesClass::getUserStatus();
*/



#Pole stavu, ktere neznamenaji ukonceni dane session#
$GLOBALS['ok_stavy'] = array(0,2,3,6); 

/*
  trida ktera je stanovena jako handler pro praci se session
*/

class SessionBookmaker{

/*
  metoda ktera otevira session a maze vsechny session,ktere jsou prihlasene a dlouho neaktivni
*/
function open ($save_path, $session_name) {
  global $db_ses;

    /*$sql = "update session set status=1 where status<>0 and status<>4 and status<>1 and time < ".(time()-SESMAX);
    $res =& $db_ses->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se vymazat stare session',"page_ex_db");*/

  return(true);
}

/*
  metoda ktera zavira session
*/
function close() {
  global $db_ses;

  unset($db_ses);

  return(true);
}

/*
  metoda ktera cte data z databaze patrici dane session
  V pripade prihlaseni kontroluje zadane udaje
  Kontroluje se session id, prvni dva bajty IP adresy a zahashovany prohlizec
*/
function read ($id) {
  global $old_ses_id,$db_ses,$netip;
   
  $db_ses->setFetchMode(DB_FETCHMODE_ASSOC);
 
  $dbGame = DbUtil::connectWebDb();
 
  #pokus o prihlaseni, kontrola zadaneho jmena a hesla#
  if(isset($_GET['pass']) && isset($_GET['nick'])){ 
		
        $encrypted = Help::cryptPass($_GET['pass']);
        //$sql = "update  admin set heslo='".$encrypted."' where admin_id=3";
        //$res =& $db_ses->query($sql);
	    
        $ldap_connect = false;

         
        if($ldap_connect){
          $sql = "select * from bookmaker where ldap_nick='".Help::slash($_GET['nick'])."'";
        }else
		$sql = "select * from bookmaker where nick='".Help::slash($_GET['nick'])."' and heslo='".Help::Slash($encrypted)."'";
	    $res =& $dbGame->query($sql);
		if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se zkonstrolovat udaje proti databazi pri pokusu o zalogovani',"page_ex_db");
		
		#uzivatel byl nalezen#
		if(($row = $res->fetchRow()) && isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on'){
		  
		  $sql = "update session set status=1 where (status=2 or status=3) and bookmaker_id=".$row['bookmaker_id'];
	      $res =& $db_ses->query($sql);
		  if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo deaktivovat session',"page_ex_db");
		   
		  #nastavime status na 3 at vime ze jde o prave prihlaseneho uzivatele #
		  $netip = preg_split("/\.{1}/",It6_Php::getRemoteAddr(),-1,PREG_SPLIT_NO_EMPTY);
		  $sql = "replace into session (status,zprava,bookmaker_id,ses_id,ip,prohlizec,time) values (".($row['zakazany']?5:3).",'".($row['zakazany']?"VĂˇĹˇ ĂşÄŤet byl zablokovĂˇn":"")."',".$row['bookmaker_id'].",'".Help::Slash($id)."','".$netip[0].".".$netip[1]."','".addslashes(md5($_SERVER['HTTP_USER_AGENT']."somestring"))."',".time().")";
	      $res =& $db_ses->query($sql);
		  if(DB::isError($res)) throw new ExHandler('Nepodarilo se zkonstrolovat udaje proti databazi pri pokusu o zalogovani',"page_ex_db");
	      
		  $script = "document.location.replace('".HOST."?superb=1')";
          header('Cache-Control:no-cache, must-revalidate');
          header('Pragma:no-cache');
          header('Content-length:'.strlen($script));
          header('Content-type:text/javascript');
          echo $script;
		  
		}#uzivatel nebyl nalezen#
		else{  
		
		  $script = "alert('Neoprávněné příhlášení');";
          header('Cache-Control:no-cache, must-revalidate');
          header('Pragma:no-cache');
          header('Content-length:'.strlen($script));
          header('Content-type:text/javascript');
          echo $script;
		
		}
		 
		exit; // nesmime kazdopadne opustit jakekoliv dalsi akce
		
  }else{#vybrani session dat z databaze# 
     
  
	  $netip = preg_split("/\.{1}/",It6_Php::getRemoteAddr(),-1,PREG_SPLIT_NO_EMPTY);
      $old_ses_id = $id;
      
	  #nastaveni statusu na 1 v pripade vyprseni session#
	  //$sql = "update session set status=1 where status<>0 and status<>4 and status<>1 and time < ".(time()-SESMAX)." and ses_id='".Help::Slash($id)."'  and prohlizec='".addslashes(md5($_SERVER['HTTP_USER_AGENT']."somestring"))."'";
      //$res =& $db_ses->query($sql);
      //if(DB::isError($res)) throw new ExHandler('Nepodarilo se vymazat stare session',"page_ex_db");
	
      $sql = "select * from session where ses_id='".Help::Slash($id)."' and prohlizec='".addslashes(md5($_SERVER['HTTP_USER_AGENT']."somestring"))."'";
	  $res =& $db_ses->query($sql);
	  if(DB::isError($res)) throw new ExHandler('Nepodarilo se precist session data z databaze'.$sql,"page_ex_db");
	
	  #session je nalezena#
	  if ($row =& $res->fetchRow()){
	    
	  	$_GET['superb'] = 1;
	  	//if($row['ses_id'] == $id) $GLOBALS['find_old_session'] = 0;else {$GLOBALS['find_old_session'] = 1;$GLOBALS['actual_ses_id'] = $row['ses_id'];} 
	   
		$GLOBALS['ses_status'] = $row['status'];
	    $GLOBALS['ses_datum'] = $row['start'];
		$GLOBALS['ses_bookmaker_id'] = $row['bookmaker_id'];
		$GLOBALS['ses_zprava'] = $row['zprava'];
	    
		if(!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != 'on') $GLOBALS['ses_status'] = 0;       //kontrola zda jde o zabezpecenou komunikaci		
	    
		$sql = "select zakazany from bookmaker where bookmaker_id=".$row['bookmaker_id'];
	    $res2 =& $dbGame->query($sql);
	    if(DB::isError($res2)) throw new ExHandler('Nepodarilo se precist  data z tabulky/pohledu admin'.$sql,"page_ex_db");
	
		
		if($row['bookmaker_id'] == 0){
		 
		 $GLOBALS['ses_status'] = 4;
		 
		 return true;
		 
		}
		
		#Pokud se admin chce odhlasit#
		if(isset($_GET['logoff']) && $_GET['logoff']){
		  $GLOBALS['ses_status'] = 0;
		  $GLOBALS['ses_bookmaker_id'] = 0;
		  $row['data'] = "";
		}
		#zakazany uzivatel nastavime status na 5 nebo 6#
		else if(($row2 = $res2->fetchRow()) && $row2['zakazany'] == 1){
		       $GLOBALS['ses_status'] = 5;
		}
		
		if(in_array($row['status'],$GLOBALS['ok_stavy'])){  // vsechno ok je bud prihlaseny nebo neprihlaseny
	     return $row['data'];
	    }
		
	  } else{ #uzivatel vstoupil na stranky musi se vygenerovat nova session#
	   
		$GLOBALS['ses_status'] = 0;
		
		return true;
	
	  }
  }
      
}

/*
  metoda ketra zapisuje data do databaze.
  Nejdrive vymaze starou session a vlozi novu se zmenenym ses_id
  Zapisuje data pouze pokud je uzivatel ve stavu kdy je session aktivni
  Vse probiha v transakci obe akce na databazi musim byt provedeny
*/
function write ($id, $sess_data) {
      global $old_ses_id,$db_ses,$netip;

	if(!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != 'on') $GLOBALS['ses_status'] = 0;       //kontrola zda jde o zabezpecenou komunikaci
	if(isset($GLOBALS['ses_status']) && in_array($GLOBALS['ses_status'],$GLOBALS['ok_stavy'])){  //sem pridavat pripady kdy je uzivatel prihlaseny a ne nejak vyhozeny a je potreba aktualizovat session

	  #zacatek transakce#
	  $db_ses->autoCommit(false); 
	  if(!isset($_GET['logoff'])){
	   //$sql = "delete from session where ses_id='".Help::Slash($old_ses_id)."'";
	   //$res4 =& $db_ses->query($sql);
	  }else{
	   $sql = "update session set status=1,ses_id='".time().$GLOBALS['ses_bookmaker_id']."' where ses_id='".Help::Slash($old_ses_id)."'";
	   $res =& $db_ses->query($sql);

	  }
	   $sql = "replace into session (ses_id,ip,prohlizec,data,time,zprava,status,start,bookmaker_id) values('".Help::Slash($id)."','".$netip[0].".".$netip[1]."','".Help::Slash(md5($_SERVER['HTTP_USER_AGENT']."somestring"))."','".Help::Slash($sess_data)."',".time().",'".(isset($GLOBALS['ses_zprava']) && mb_strlen($GLOBALS['ses_zprava'])<=140?Help::slash($GLOBALS['ses_zprava']):"")."',".($GLOBALS['ses_status']==3?2:$GLOBALS['ses_status']).",'".($GLOBALS['ses_status']==3 || !isset($GLOBALS['ses_datum'])?date("Y.m.d H:i:s"):$GLOBALS['ses_datum'])."',".(isset($GLOBALS['ses_bookmaker_id'])?$GLOBALS['ses_bookmaker_id']:0).")";
	  $res2 =& $GLOBALS['db_ses']->query($sql);
	  if( (isset($res) && DB::isError($res) ) || DB::isError($res2)) ;//{$db_ses->rollback();$db_ses->autoCommit(true); throw new ExHandler($res2->getMessage().$sql.'Nepodarilo se zapsat session data do databaze',"page_ex_db");}
	  else {$db_ses->commit();$db_ses->autoCommit(true); return true;}
   
   }
   
   #uvolnime nepotrebne promenne#
   if(isset($GLOBALS['ses_datum'])) unset($GLOBALS['ses_datum']);
   
}

/*
  metoda ktera nastavuje session do stavu neaktivni
*/
function destroy ($id) {
  global $db_ses;       

  $sql = "update session set status=4 where ses_id='".Help::Slash($id)."'";
  $res =& $db_ses->query($sql);
  if(DB::isError($res)) return false; else return true;

}

/*********************************************
 * WARNING - You will need to implement some *
 * sort of garbage collection routine here.  *
 *********************************************/
function gc ($maxlifetime) {
  return true;
}

}



?>