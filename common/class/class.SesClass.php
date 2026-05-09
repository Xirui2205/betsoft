<?php
/*

 Cookie enable

 Tridy pro praci se session autorizaci uzivatelu.
 Vsechny data se ukladaji v db, pouze ses_id v Cookie

 Pro kazdeho uzivatele je pri vstupu na stranky vygenerovana session.
 Ta vstupuje do ruznych stavu. Kazdy stav vyjadruje jinou situaci nebo roli ve ktere uzivatel je.

 !!V pripade pridani novych stavu se script musi na mnoha mistech opravit!!

 OK STAVY (stavy ve kterych je session stale aktivni)
 status = 0 neprihlaseny
 status = 2 prihlaseny ok
 status = 3 prave zalogovany

 VYHOZENI STAVY (takova session je jiz neaktivni a uzivatel na ni nemuze dale provadet zadne akce)
 status = 1 vyhozeny vyprsela session
 status = 4 zniceni session z nejakeho duvodu je uvedeno ve sloupci zprava

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

class SesClass{

 /*
  metoda otevira nebo vytvari session.
  * @param $db objekt pro praci s databazi session. Podporuje poze PEAR::DB je povinny
  * @param $dbGame objekt pro praci s centralni databazi. Podporuje poze PEAR::DB je povinny
  * @param $mobile 1 = jde o prihlaseni z mobilu 0 = prihlaseni z klasickych stranek
  vraci true pri uspechu false pri neuspechu
 */
 public function open(&$db,&$dbGame=NULL,$mobile=0){

  if(!is_resource($db) && !is_object($db)){

   return false;

  }else{

   $GLOBALS['mobile_connect'] = $mobile;

   $GLOBALS['db_ses'] = &$db;

   if($dbGame != NULL) $GLOBALS['db_ses_game'] = &$dbGame;

   else if((substr_count($_SERVER["SERVER_NAME"],'admin') > 0)) {
     $GLOBALS['db_ses_game'] = $db;
   }

  // $GLOBALS['db_ses']->autocommit(false);
   session_start();

   $regeneate = true;
   //if(isset($GLOBALS['find_old_session']) && $GLOBALS['find_old_session'] == 0) session_regenerate_id();
   if(isset($GLOBALS['find_old_session']) && $GLOBALS['find_old_session'] == 1 && isset($GLOBALS['actual_ses_id'])) session_id($GLOBALS['actual_ses_id']);

	if( $GLOBALS['ses_status'] == 0) {
		$GLOBALS['ses_admin_id'] = 0;
		$GLOBALS['ses_bookmaker_id'] = 0;
	}
	if (isset($GLOBALS['ses_admin_id'])) {
		//if(!isset($_SESSION['user_id']))
			$_SESSION['user_id'] = $GLOBALS['ses_admin_id'];
	}
	else if (isset($GLOBALS['ses_bookmaker_id'])) {
		//if (!isset($_SESSION['user_id']))
			$_SESSION['user_id'] = $GLOBALS['ses_bookmaker_id'];
	}

   return $regeneate;

  }

 }

  /*
  metoda zapise session do databaze
  po zavolani teto metody neni mozne vytvaret dalse session promenne
 */
 public function close(&$db=NULL){

  if($db != NULL) $GLOBALS['db_ses'] = &$db;
  session_write_close();
  //$GLOBALS['db_ses']->commit();

 }

 /*
  metoda vraci status daneho uzivatele
 */
 public function getUserStatus(){

  return $GLOBALS['ses_status'];

 }

  /*
  metoda nastavuje status
 */
 public function setUserStatus($status=0){

  $GLOBALS['ses_status'] = $status;

 }

 /*
  metoda odregistruje danou promennou
  * @param $var jmeno promenne
 */
 public function destroyVariable($var){

  return session_unregister($var);

 }

}


?>
