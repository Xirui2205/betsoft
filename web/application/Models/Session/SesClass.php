<?php
/*
 Description
 * @author     tkbc.com
 * @date       25.7.2009
 * @copyright  TKBC
 * @version    1.0
 * @link       http://tkbc.cz

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

class Models_Session_SesClass{

	public static $immediateExit = false;
	/*
	 metoda otevira nebo vytvari session.
	 * @param $db objekt pro praci s databazi session. Podporuje poze PEAR::DB je povinny
	 * @param $dbGame objekt pro praci s centralni databazi. Podporuje poze PEAR::DB je povinny
	 * @param $mobile 1 = jde o prihlaseni z mobilu 0 = prihlaseni z klasickych stranek
	 vraci true pri uspechu false pri neuspechu
	 */
	public static function open(){
		static $first = true;

		if ( $first ) {
			$first = false;
			
			$GLOBALS['db_ses'] =  Zend_Registry::get('dbSes');
			$GLOBALS['ses_status'] = 0;
			try{
				//session_start();
				Zend_Session::start();
			}
			catch(Zend_Exception $e){
				Models_Exception_Handler::handle($e->getMessage(). ': '. __FILE__ .': '. __LINE__ );
			}

			if (false !== self::$immediateExit)
				throw new It6_Controller_Exception_ImmediateExit(self::$immediateExit);

			return true;
		}
		

	}

	/*
	 metoda zapise session do databaze
	 po zavolani teto metody neni mozne vytvaret dalse session promenne
	 */
	public static function close(){

		try{

			session_write_close();

		}catch(Zend_Exception $e){

			Models_Exception_Handler::handle($e->getMessage(). ': '. __FILE__ .': '. __LINE__ );

		}
	}

	/*
	 metoda vraci status daneho uzivatele
	 */
	public static function getUserStatus(){

		return $GLOBALS['ses_status'];

	}

	/*
	 metoda nastavuje status
	 */
	public static function setUserStatus($status=0){

		$GLOBALS['ses_status'] = $status;

	}

	/*
	 metoda odregistruje danou promennou
	 * @param $var jmeno promenne
	 */
	public static function destroyVariable($var){

		return session_unregister($var);

	}

}


?>
