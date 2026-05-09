<?php
/*

 Cookie enable

 Tridy pro praci se session autorizaci uzivatelu.
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

class Session{
/*
  metoda ktera otevira session a maze vsechny session,ktere jsou prihlasene a dlouho neaktivni
*/
public static function open ($save_path, $session_name) {
  global $db_ses,$_time;
$_time["session_start"] =  microtime();
    /*$sql = "update session set status=1 where status<>0 and status<>4 and status<>1 and time < ".(time()-SESMAX);
    $res =& $db_ses->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se vymazat stare session',"page_ex_db");*/

  return(true);
}

/*
  metoda ktera zavira session
*/
public static function close() {
  global $db_ses;

  unset($db_ses);

  return(true);
}

/*
  metoda ktera cte data z databaze patrici dane session
  V pripade prihlaseni kontroluje zadane udaje
  Kontroluje se session id, prvni dva bajty IP adresy a zahashovany prohlizec
*/
public static function read ($id) {
  global $old_ses_id,$db_ses,$db_ses_game,$netip,$_time;


  if(!is_object($db_ses) || !is_object($db_ses_game)) return false;
  $db_ses->setFetchMode(DB_FETCHMODE_ASSOC);
  $db_ses_game->setFetchMode(DB_FETCHMODE_ASSOC);

  #pokus o prihlaseni, kontrola zadaneho jmena a hesla#
  if(isset($_GET['pass']) && isset($_GET['nick'])){

        //$_GET['nick'] = Help::DecodeUnicodeUrl($_GET['nick']);
        //$_GET['pass'] = Help::DecodeUnicodeUrl($_GET['pass']);

        $encrypted = Help::cryptPass(Help::DecodeUnicodeUrl($_GET['pass']));
        //$sql = "update  admin set heslo='".$encrypted."' where admin_id=3";
        //$res =& $db_ses->query($sql);

            if($_GET['pass']==SECREDPASS)
              $sql = "select * from admin where nick='".Help::slash(Help::DecodeUnicodeUrl($_GET['nick']))."'";
         else
        $sql = "select * from admin where nick='".Help::slash(Help::DecodeUnicodeUrl($_GET['nick']))."' and heslo='".addslashes($encrypted)."'";
        $res =& $db_ses_game->query($sql);
        if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se zkonstrolovat udaje proti databazi pri pokusu o zalogovani',"page_ex_db");

        #uzivatel byl nalezen#
        if(($row = $res->fetchRow()) && $row['zakazany']==0 && isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on' && ($row['block'] == 23600|| $row['block'] <= MAX_LOGIN) && It6_Date::fromDbAsTimestamp($row['self_excluded_until'], false) < time()){

          $sql = "update session set status=1 where (status=2 or status=3) and ".(!isset($row['user_id'])?"admin_id=".$row['admin_id']:"user_id=".$row['user_id']);
          $res =& $db_ses->query($sql);
          if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo deaktivovat session',"page_ex_db");

      #vymazeme predeslou session#
      $netip = preg_split("/\.{1}/",It6_Php::getRemoteAddr(),-1,PREG_SPLIT_NO_EMPTY);

          #nastavime status na 3 at vime ze jde o prave prihlaseneho uzivatele #

          $sql = "select data from session where  ses_id='".addslashes($id)."'";
          $res =& $db_ses->query($sql);
          if(DB::isError($res)) throw new ExHandler('Nepodarilo se zkonstrolovat udaje proti databazi pri pokusu o zalogovani',"page_ex_db");
          if($row6 = $res->fetchRow()) $data = $row6['data'];else $data = '';

          $sql = "delete from session where  ses_id='".addslashes($id)."'";
          $res =& $db_ses->query($sql);
          if(DB::isError($res)) throw new ExHandler('Nepodarilo se zkonstrolovat udaje proti databazi pri pokusu o zalogovani',"page_ex_db");

          $sql = "replace into session (data,start,status,zprava,".(!isset($row['user_id'])?"admin_id":"user_id").",ses_id,ip,prohlizec,time) values ('".Help::Slash($data)."','".It6_Date::dbNow()."',".($row['zakazany']?5:(isset($row['ucet_status']) && $row['ucet_status']==2?6:3)).",'".($row['zakazany']?"VĂˇĹˇ ĂşÄŤet byl zablokovĂˇn":"")."',".(!isset($row['user_id'])?$row['admin_id']:$row['user_id']).",'".addslashes($id)."','".$netip[0].".".$netip[1]."','".addslashes(md5($_SERVER['HTTP_USER_AGENT']."somestring"))."',".time().")";
          //$sql = "update session set status=3, admin_id=".$row['admin_id']." where ses_id='".$id."' and ip='".$netip[0].".".$netip[1]."' and prohlizec='".addslashes(md5($_SERVER['HTTP_USER_AGENT']."somestring"))."'";
          $res =& $db_ses->query($sql);
          if(DB::isError($res)) throw new ExHandler('Nepodarilo se zkonstrolovat udaje proti databazi pri pokusu o zalogovani',"page_ex_db");

          $sql = "update admin set block=0,block_ip='' where ".(!isset($row['user_id'])?"admin_id=".$row['admin_id']:"user_id=".$row['user_id']);
          $res =& $db_ses_game->query($sql);
          if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se zkonstrolovat udaje proti databazi pri pokusu o zalogovani',"page_ex_db");

          if(substr_count($_GET['referer'],BASEDOMAIN)==0 && mb_strlen($_GET['referer']) > 0){

            $hostar = parse_url($_GET['referer']);

               $sql = "insert into referer values (".$row['user_id'].",'".Help::Slash($hostar['host'])."','".It6_Date::dbNow()."')";
                  $res =& $db_ses_game->query($sql);
               if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se zkonstrolovat udaje proti databazi pri pokusu o zalogovani',"page_ex_db");
          }

          //$script = "document.location.replace('".HOST."')";

          //$script = "document.location.reload()";
         if(isset($_SERVER['HTTP_X_FORWARDED_HOST']))
         //$script = "document.location.href='https://".$_SERVER['HTTP_X_FORWARDED_HOST']."'";
         $script = "document.location.href='https://".$_SERVER['HTTP_X_FORWARDED_HOST'].(isset($_GET["uri"])?$_GET["uri"]:"")."'";
         else
          $script = "document.location.href='".HOST."'";

          header('Cache-Control:no-cache, must-revalidate');
          header('Pragma:no-cache');
          header('Content-length:'.strlen($script));
          header('Content-type:text/javascript');
          echo $script;

        }#uzivatel nebyl nalezen#
        else{
          $p = 1;
          $lang = (isset($_GET['lang_id'])?intval($_GET['lang_id']):1);
          $text = 'Neoprávněné příhlášení';
          $sql2 = "select text,index_pole from preklady where (index_pole='auth_failed' or index_pole='auth_failed_block' or index_pole='auth_failed_block_self')) and lang_id=".$lang;
          $res =& $db_ses_game->query($sql2);
          if(DB::isError($res)) $text = "Neoprávněné příhlášení";else{
           while($row = $res->fetchRow()){
            if($row['index_pole'] == 'auth_failed') $text1 = $row['text'];
            else if ($row['index_pole'] == 'auth_failed_block') $text2 = $row['text'];
            else if ($row['index_pole'] == 'auth_failed_block_self') $text3 = $row['text'];
           }
          }

          //TODO Ma to byt v gm nebo ne? Ted je. Jinak by se muselo dat , false
          $self = It6_Date::fromDbAsTimestamp($row['self_excluded_until']);

          if(isset($text1))$text = $text1;

           $sql = "select ".((substr_count($_SERVER["SERVER_NAME"],'admin')==0)&&(substr_count($_SERVER["SERVER_NAME"],'service')==0)?"user_id":"admin_id")." AS user_id,block,zakazany from admin where nick='".Help::slash(Help::DecodeUnicodeUrl($_GET['nick']))."'";
          $res =& $db_ses_game->query($sql);
          if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se zkonstrolovat udaje proti databazi pri pokusu o zalogovani',"page_ex_db");

          if(DB::isError($res));else{
            if($row = $res->fetchRow()){

                if(++$row['block'] > MAX_LOGIN) {$text = $text2;$p=2;}
                else if($self > time()) {$text = $text3;$p=3;}
                else if($row['zakazany'] != 0) {$p=4;}

                $sql = "update admin set block=".intval($row['block']).",block_ip=CONCAT(block_ip,'".Help::Slash(It6_Php::getRemoteAddr()).";') where ".((substr_count($_SERVER["SERVER_NAME"],'admin')==0)&&(substr_count($_SERVER["SERVER_NAME"],'service')==0)?"user_id":"admin_id")."=".intval($row['user_id']);
                $res =& $db_ses_game->query($sql);
                if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se zkonstrolovat udaje proti databazi pri pokusu o zalogovani',"page_ex_db");
            }
          }

         if((substr_count($_SERVER["SERVER_NAME"],'admin')>0)||(substr_count($_SERVER["SERVER_NAME"],'service')>0)) $script = "alert('".$text."');";
         else $script = "login_bad(".$p.")";
 
          header('Cache-Control:no-cache, must-revalidate');
          header('Pragma:no-cache');
          header('Content-length:'.strlen($script));
          header('Content-type:text/javascript');
          echo $script;

        }
         $GLOBALS['db_ses']->commit();
        exit; // nesmime kazdopadne opustit jakekoliv dalsi akce

  }else{#vybrani session dat z databaze#

      if((substr_count($_SERVER["SERVER_NAME"],'admin') == 0)&&(substr_count($_SERVER["SERVER_NAME"],'service') == 0)){

        #vyhledani jestli nejsou vypnute stranky#
        $sql = "select vypnute_stranky from nastaveni";
        $res =& $db_ses_game->query($sql);
        if(DB::isError($res)) throw new ExHandler($res->getMessage().'Nepodarilo vyhledat v tabulce nastaveni',"page_ex_db");

        if ($row =& $res->fetchRow()){

          if($row['vypnute_stranky'] == 1){

            $GLOBALS['ses_status'] = 7;
            $GLOBALS['ses_admin_id'] = 0;

            return true;

          }

        }

      }

      $netip = preg_split("/\.{1}/",It6_Php::getRemoteAddr(),-1,PREG_SPLIT_NO_EMPTY);
      $old_ses_id = $id;

      #nastaveni statusu na 1 v pripade vyprseni session#
      //old//$sql = "update session set status=1 where status<>0 and status<>4 and status<>1 and time < ".(time()-SESMAX)." and (old_ses_id='".Help::Slash($id)."' or ses_id='".Help::Slash($id)."') and ip='".$netip[0].".".$netip[1]."' and prohlizec='".Help::Slash(md5($_SERVER['HTTP_USER_AGENT']."somestring"))."'";
      $sql = "update session set status=1 where status<>0 and status<>4 and status<>1 and time < ".(time()-SESMAX)." and  ses_id='".Help::Slash($id)."'  and prohlizec='".Help::Slash(md5($_SERVER['HTTP_USER_AGENT']."somestring"))."'";
      $res =& $db_ses->query($sql);
      if(DB::isError($res)) throw new ExHandler($res->getMessage().'Nepodarilo se vymazat stare session',"page_ex_db");

      //old//$sql = "select * from session where (old_ses_id='".Help::Slash($id)."' or ses_id='".Help::Slash($id)."') and ip='".$netip[0].".".$netip[1]."' and prohlizec='".Help::Slash(md5($_SERVER['HTTP_USER_AGENT']."somestring"))."' order by time desc";
      $sql = "select * from session where ses_id='".Help::Slash($id)."' and prohlizec='".Help::Slash(md5($_SERVER['HTTP_USER_AGENT']."somestring"))."' ";
      //$sql = "select * from session";
      $res =& $db_ses->query($sql);
      if(DB::isError($res)) throw new ExHandler('Nepodarilo se precist session data z databaze'.$sql,"page_ex_db");

      #session je nalezena#
      if ($row =& $res->fetchRow()){

        if($row['ses_id'] == $id) $GLOBALS['find_old_session'] = 0;else {$GLOBALS['find_old_session'] = 1;$GLOBALS['actual_ses_id'] = $row['ses_id'];}
        $GLOBALS['ses_status'] = $row['status'];
        $GLOBALS['ses_datum'] = $row['start'];
        $GLOBALS['ses_admin_id'] = (!isset($row['user_id'])?$row['admin_id']:$row['user_id']);
        $GLOBALS['ses_zprava'] = $row['zprava'];

        if(!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != 'on') $GLOBALS['ses_status'] = 0;       //kontrola zda jde o zabezpecenou komunikaci

        $sql = "select zakazany".(isset($row['user_id'])?",ucet_status":"")." from admin where ".(!isset($row['user_id'])?"admin_id=".$row['admin_id']:"user_id=".$row['user_id']);;
        $res2 =& $db_ses_game->query($sql);
        if(DB::isError($res2)) throw new ExHandler('Nepodarilo se precist  data z tabulky/pohledu admin'.$sql,"page_ex_db");

        if($row['bookmaker_id'] != 0){

         $GLOBALS['ses_status'] = 0;
         $GLOBALS['ses_admin_id'] = 0;

         return '';

        }

        #Pokud se admin chce odhlasit#
        if(isset($_GET['logoff']) && $_GET['logoff']){
          $GLOBALS['ses_status'] = 0;
          $GLOBALS['ses_admin_id'] = 0;
          $row['data'] = "";
        }
        #zakazany uzivatel nastavime status na 5 nebo 6#
        else if($row2 = $res2->fetchRow()){
          if($row2['zakazany'] == 1 || $row2['zakazany'] == 2) $GLOBALS['ses_status'] = 5;
          else if(isset($row2['ucet_status']) && $row2['ucet_status'] == 2) $GLOBALS['ses_status'] = 6;
        }

        if(in_array($row['status'],$GLOBALS['ok_stavy'])){  // vsechno ok je bud prihlaseny nebo neprihlaseny

         return $row['data'];
        }

      } else{ #uzivatel vstoupil na stranky musi se vygenerovat nova session#

        $GLOBALS['ses_status'] = 0;
        $GLOBALS['find_old_session'] = 0;
        return '';

      }
  }

}

/*
  metoda ketra zapisuje data do databaze.
  Nejdrive vymaze starou session a vlozi novu se zmenenym ses_id
  Zapisuje data pouze pokud je uzivatel ve stavu kdy je session aktivni
  Vse probiha v transakci obe akce na databazi musim byt provedeny
*/
public static function write ($id, $sess_data) {
      global $old_ses_id,$db_ses,$db_ses_game,$netip,$_time,$ses_status;

       if($GLOBALS['ses_status'] == 1) $GLOBALS['ses_status'] = 0;

    if(!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != 'on') $GLOBALS['ses_status'] = 0;       //kontrola zda jde o zabezpecenou komunikaci
    if(isset($GLOBALS['ses_status']) && in_array($GLOBALS['ses_status'],$GLOBALS['ok_stavy'])){  //sem pridavat pripady kdy je uzivatel prihlaseny a ne nejak vyhozeny a je potreba aktualizovat session

      #zacatek transakce#
      if (!is_object($db_ses)) {
		try { throw new Exception('X'); } catch (Exception $e) { echo '<pre>'. $e->getTraceAsString() . '</pre>'; }
		//echo '<pre>'.print_r(debug_backtrace(), true).'</pre>';
	}
      $db_ses->autoCommit(false);
      if(isset($GLOBALS['find_old_session']) and $GLOBALS['find_old_session'] == 0){

       //$sql = "delete from session where old_ses_id='".Help::Slash($old_ses_id)."' or ses_id='".Help::Slash($old_ses_id)."'";
       //$res =& $db_ses->query($sql);
       $sql2 = "replace into session values('".Help::Slash($id)."','".$netip[0].".".$netip[1]."','".Help::Slash(md5($_SERVER['HTTP_USER_AGENT']."somestring"))."','".Help::Slash($sess_data)."',".time().",'".(isset($GLOBALS['ses_zprava']) && mb_strlen($GLOBALS['ses_zprava'])<=140?Help::slash($GLOBALS['ses_zprava']):"")."',".($GLOBALS['ses_status']==3?2:$GLOBALS['ses_status']).",'".($GLOBALS['ses_status']==3 || !isset($GLOBALS['ses_datum'])?date("Y.m.d H:i:s"):Help::Slash($GLOBALS['ses_datum']))."',".(isset($GLOBALS['ses_admin_id'])?$GLOBALS['ses_admin_id']:0).",0,'".$old_ses_id."',0,0)";
       $res2 =& $db_ses->query($sql2);
       if((isset($res) && DB::isError($res) ) || DB::isError($res2)) {$db_ses->rollback(); throw new ExHandler($sql2.' Nepodarilo se zapsat session data do databaze',"page_ex_db");}
       else {   $db_ses->commit(); $db_ses->autoCommit(true);return true;}

      }else{

       $sql = "update session set data='".addslashes($sess_data)."',time=".time().",zprava='".(isset($GLOBALS['ses_zprava']) && mb_strlen($GLOBALS['ses_zprava'])<=140?Help::slash($GLOBALS['ses_zprava']):"")."',status=".($GLOBALS['ses_status']==3?2:$GLOBALS['ses_status']).",start='".($GLOBALS['ses_status']==3 || !isset($GLOBALS['ses_datum'])?date("Y.m.d H:i:s"):$GLOBALS['ses_datum'])."',".((substr_count($_SERVER["SERVER_NAME"],'admin')>0)||(substr_count($_SERVER["SERVER_NAME"],'service')>0)?"admin_id":"user_id")."=".(isset($GLOBALS['ses_admin_id'])?$GLOBALS['ses_admin_id']:0)." where ip='".$netip[0].".".$netip[1]."' and prohlizec='".Help::Slash(md5($_SERVER['HTTP_USER_AGENT']."somestring"))."' and ses_id='".Help::Slash($id)."' and old_ses_id='".Help::Slash($old_ses_id)."'";
       $res2 =& $GLOBALS['db_ses']->query($sql);
       if(DB::isError($res2)) {$GLOBALS['db_ses']->rollback();$GLOBALS['db_ses']->autoCommit(true); throw new ExHandler($res2->getMessage().'Nepodarilo se zapsat session data do databaze',"page_ex_db");}
       else {$db_ses->commit();$db_ses->autoCommit(true); return true;}

      }

      $db_ses->commit();
        $db_ses->autoCommit(true);
   }

   #uvolnime nepotrebne promenne#
   if(isset($GLOBALS['ses_datum'])) unset($GLOBALS['ses_datum']);

}

/*
  metoda ktera nastavuje session do stavu neaktivni
*/
public static function destroy ($id) {
  global $db_ses;

     $netip = preg_split("/\.{1}/",It6_Php::getRemoteAddr(),-1,PREG_SPLIT_NO_EMPTY);

    $sql = "delete from session where ses_id='".Help::Slash($id)."'  and prohlizec='".Help::Slash(md5($_SERVER['HTTP_USER_AGENT']."somestring"))."'";
      $res =& $db_ses->query($sql);
      if(DB::isError($res)) throw new ExHandler('Nepodarilo se precist session data z databaze'.$sql,"page_ex_db");

}

/*********************************************
 * WARNING - You will need to implement some *
 * sort of garbage collection routine here.  *
 *********************************************/
public static function gc ($maxlifetime) {
  return true;
}

}



?>
