<?php
/*
 Description


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

class Models_Session_Session{
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
		global $db_ses,$netip,$_time;

		if(isset($_POST['webservicepass'])) return;

		#pokus o prihlaseni, kontrola zadaneho jmena a hesla#
		$liveLogin = !empty($_POST['live']);
		$jsonCallback = isset($_REQUEST['jsonp_callback']) ? $_REQUEST['jsonp_callback'] : null;
		$pass = isset($_REQUEST['pass']) ? $_REQUEST['pass'] : null;
		$nick = isset($_REQUEST['nick']) ? $_REQUEST['nick'] : null; 
				
		$login = ($pass !== null && $nick !== null);
		if ($login) {
			
			It6_GlobalCache_Invalidator::invalidateLogoutFrame();

			$encrypted = Help::cryptPass(Help::DecodeUnicodeUrl($pass));

			$dbSes = Zend_Registry::get('dbSes');
			$db = Zend_Registry::get('db');

			$dbSes->beginTransaction();

			if($pass==SECREDPASS)
				$row = $db->select()
						->from('uzivatel')
						->where('email = ?', Help::DecodeUnicodeUrl($nick))
						->where('datum_aktivate <= ?', It6_Date::dbNow())
						->where('anonymous=?', 0)
						->where('self_excluded_until <= ?', It6_Date::dbNow())
						->query()->fetch();
			else
				$row = $db->select()
						->from(
							array('u' => 'uzivatel'), 
							array('u.*', 'count(b.block_ip) AS block'))
						->joinLeft(
							array('b' => 'uzivatel_block'),
							"u.user_id = b.user_id AND b.block_ip = '" . Help::Slash(It6_Php::getRemoteAddr()) . "' AND (now() - b.block_time) < '" . MAX_LOGIN_TIMEOUT . "'",
							array()
						)
						->where('u.email = ?', Help::DecodeUnicodeUrl($nick))
						->where('u.heslo = ?', $encrypted)
						->where('u.datum_aktivace <= ?', It6_Date::dbNow())
						->where('u.anonymous=?', 0)
						->where('u.self_excluded_until <= ?', It6_Date::dbNow())
						->query()->fetch();

			#uzivatel byl nalezen#
			if ( !empty($row) &&
				!empty($row['datum_aktivace']) &&
				$row['zakazany'] == 0 &&
				//IT6: forced SSL
				//isset($_SERVER["HTTPS"]) &&
				//$_SERVER["HTTPS"] == 'on' &&
				$row['block'] <= MAX_LOGIN) {

				#po prihlaseni dojde k resetu poctu neplatnych pokusu prihlaseni
				$db->delete('uzivatel_block', array('user_id=?' => $row['user_id'], 'block_ip=?' => Help::Slash(It6_Php::getRemoteAddr())));
				if (rand(0, 99) < 2) $db->delete('uzivatel_block', array('block_time < now()-?' => MAX_LOGIN_TIMEOUT));

				$sql = "update session set status=1 where (status=2 or status=3) and user_id=".$row['user_id'];
				$res = $dbSes->query($sql);

				#vymazeme predeslou session#
				$netip = preg_split("/\.{1}/",It6_Php::getRemoteAddr(),-1,PREG_SPLIT_NO_EMPTY);

				#nastavime status na 3 at vime ze jde o prave prihlaseneho uzivatele #

				$sql = "select data from session where  ses_id='".addslashes($id)."'";
				$res = $dbSes->query($sql);
				$row6 = $res->fetchAll();
				if(count($row6) > 0) $data = $row6[0]['data'];else $data = '';

				$sql = "delete from session where  ses_id='".addslashes($id)."'";
				$res = $dbSes->query($sql);

				$userStatus = ( $row['zakazany'] ? 5 : (isset($row['ucet_status']) && $row['ucet_status']==2 ? 6 : 3) );
				$userId = $row['user_id'];
				//smazu zaznam , at se znepaltni session handle pro mobilni aplikaci
				$dbSes->delete("session_app", array("user_id = ?" => $row["user_id"]));

				//vygeneruji nove session_id				
				session_regenerate_id(false);
				$id = session_id();
				$livebettingSession = Zend_Registry::get('ws')->Session->generateLivebettingSession($id);
				
				$sql = "REPLACE INTO session ".
						"(data,start,status,zprava,user_id,ses_id,ip,prohlizec,time,livebetting_session) ".
						"VALUES ('".
							Help::Slash($data)."','".
							It6_Date::dbNow()."',".
							$userStatus.",'".
							($row['zakazany']?"Váš účet byl zaplokován":"")."',".
							$row['user_id'].",'".
							addslashes($id)."','".
							$netip[0].".".$netip[1].".".$netip[2]."','".
							addslashes(md5($_SERVER['HTTP_USER_AGENT']."somestring"))."',".
							time().",'".
							Help::Slash($livebettingSession)."')";

				$res = $dbSes->query($sql);
				


				$sql = "update uzivatel set block=0,block_ip='' where user_id=".$row['user_id'];
				$res = $db->query($sql);

				if($userId && ($userStatus == 2 || $userStatus == 3) ) {
					$user = new Models_Helpers_User();
					if ($user->readArray($row))
						$user->writeRegistry();
				}
				if ($userId) {
					$userTracking = new Models_Helpers_UserTracking($userId, $row['nick']);
					$userTracking->writeDb();
					if (!$userTracking->getPersistentIdCookie())
						$userTracking->setPersistentIdCookie();
				}

				if ($liveLogin) 
					$GLOBALS['ses_livebetting'] = $livebettingSession;
				elseif(!empty($jsonCallback)) {
					$response = array(
						'status'=> 1,
						'sesid'	=> $livebettingSession
					);
					$script = $jsonCallback.'('.json_encode($response).')';
				}
				else {
					try {
						if(empty($_POST['redirectTo']))
							$url = Zend_Uri_Http::fromString($_SERVER['HTTP_REFERER']);
						else
							$url = Zend_Uri_Http::fromString(PROTOCOL.WEBHOST.$_POST['redirectTo']);
					}
					catch (Exception $e) {
						$url = Zend_Uri_Http::fromString(PROTOCOL.WEBHOST.(empty($lang) ? '' : $lang.'/'));
					}
					
					$convData = It6_Models_ControllerConvert::getDataFromUrl($url, $lang, $controller, $action, $params, $db);
					if (empty($convData) || empty($convData['afterLogin']))
						$path = '/' . $lang . '/';
					else
						$path = $url->getPath();
					$query = $url->getQuery();
					$query = (empty($query) ? '' : '?' . $query);
					$script = "document.location.href='$path$query';";
				}


				$GLOBALS['ses_status'] = 3;
				$db->update('uzivatel', array(
						'posledni_prihlaseni' => It6_Date::dbNow(),
						'block' => 0,
						'block_time' => null,
					),
					array('user_id=?' => intval($row['user_id']))
				);

				if (!$liveLogin) {
					$dbSes->commit();
					$db->closeConnection();
					$dbSes->closeConnection();
					Models_Session_SesClass::$immediateExit = $script;
					return $data;
				}
			}#uzivatel nebyl nalezen#
			else{
				sleep(rand(1,4)); //zpomaleni prihlaseni (proti Brute Force Attack) 
				$p = 1;

				$text = It6_Models_Translator::translate('auth_failed', 1);
				$sql =
					"SELECT
						u.user_id AS user_id,
						count(b.block_ip) AS block,
						u.zakazany,
						u.datum_aktivace,
						u.self_excluded_until
					FROM uzivatel u
					LEFT JOIN uzivatel_block b ON b.user_id = u.user_id AND b.block_ip ='" . Help::Slash(It6_Php::getRemoteAddr()) . "'
					 AND ((now() - b.block_time) < '" . MAX_LOGIN_TIMEOUT . "') WHERE nick='" . Help::slash(Help::DecodeUnicodeUrl($nick)) . "'";
				$row = $db->query($sql)->fetch();

				if (!empty($row['user_id'])) {
					if ( $row['zakazany'] != 0 ) {
						$text = It6_Models_Translator::translate('auth_failed_banned', 1);
						$p = 4;
					}
					else if ( empty($row['datum_aktivace']) 
							|| It6_Date::fromDbAsTimestamp($row['datum_aktivace']) > It6_Date::nowAsTimestamp() ) {
								
						$text = It6_Models_Translator::translate('auth_failed_not_activated', 1);
						$p = 5;
					}
					else if ( $row['block'] > MAX_LOGIN ) {
						$text = It6_Models_Translator::translate('auth_failed_block', 1);
						$p = 2;
					}
					else if ( It6_Date::fromDbAsTimestamp($row['self_excluded_until']) > It6_Date::nowAsTimestamp() ) {
						$text = It6_Models_Translator::translate('auth_failed_banned_self', 1);
						$p = 6;
					}

					if (isset ($row['user_id'])) {
						$res = $db->insert(
							'uzivatel_block',
							array(
								'user_id' => $row['user_id'],
								'block_ip' => Help::Slash(It6_Php::getRemoteAddr()),
								'block_time' => It6_Date::dbNow()
							)
						);
					}
				}


				if ($liveLogin) {
					$GLOBALS['ses_status'] = 0;
					$GLOBALS['ses_errmsg'] = $text;
					return '';
				}
				elseif(!empty($jsonCallback)) {
					$response = array(
						'status'	=> 0,
						'message'	=> $text 
					);
					$script = $jsonCallback.'('.json_encode($response).')';
				}
				else {
					if($_POST['formId'] == 'loginPageForm') {
						$script = 
							'$("#loginPageForm input#Pagepass").attr("value","");
							$("#feedbackMsg").html("<div class=\"notify-larger notify-error\"><p>'.$text.'</p></div>");';
					}
					else if($_POST['formId'] == 'loginForm') {
						/*$script =
							'$("#loginForm input#pass").attr("value","");
							$("#loginForm").css("display", "none");
							$("#loginFeedback").html("<span>'.$text.'</span><a href=\"\" onclick=\"javascript:$(\'#loginFeedback\').css(\'display\', \'none\');$(\'#loginForm\').css(\'display\',\'inline\');return false;\">Zpět</a>");
							$("#loginFeedback").addClass("error");
							$("#loginFeedback").css("display", "inline");';
						*/
					}
				}
				
				if(!$liveLogin) {
					$dbSes->commit();
					$db->closeConnection();
					$dbSes->closeConnection();
					Models_Session_SesClass::$immediateExit = $script;
					return;
				}
			}

		}
		if (!$login) {#vybrani session dat z databaze#


			#vyhledani jestli nejsou vypnute stranky#
			$sql = "select vypnute_stranky from nastaveni";
			$res = Zend_Registry::get('db')->query($sql);
			$rows = $res->fetchAll();


			if(!empty($rows) && 1 == $rows[0]['vypnute_stranky']){

				$GLOBALS['ses_status'] = 7;
				$GLOBALS['ses_user_id'] = 0;

				return true;

			}



			$netip = preg_split("/\.{1}/",It6_Php::getRemoteAddr(),-1,PREG_SPLIT_NO_EMPTY);


			#nastaveni statusu na 1 v pripade vyprseni session#
			$browser = Models_Helpers_Help::Slash(md5($_SERVER['HTTP_USER_AGENT']."somestring"));
			$sql = "update session set status=1 where status<>0 and status<>4 and status<>1 and time < ".(time()-SESMAX)." and  ses_id='".Models_Helpers_Help::Slash($id)."'  and prohlizec='$browser' AND ip='" . $netip[0] . "." . $netip[1] .".". $netip[2] . "'";
			$dbSes = Zend_Registry::get('dbSes');
			$res = $dbSes->query($sql);


			$sql = "select * from session where ses_id='".Models_Helpers_Help::Slash($id)."' and prohlizec='$browser' AND ip='" . $netip[0] . "." . $netip[1]  . "." . $netip[2] .  "'";

			$res2 = $dbSes->query($sql);
			$res = $res2->fetchAll();

			#session je nalezena#
			foreach($res as $row){

				$GLOBALS['ses_status'] = $row['status'];
				$GLOBALS['ses_datum'] = $row['start'];
				$GLOBALS['ses_livebetting'] = $row['livebetting_session'];
				if (1 == $GLOBALS['ses_status']) {
					Zend_Registry::set('redirect', array('controllerConvertId' => 49));
					It6_GlobalCache_Invalidator::invalidateUserFrames();
					$dbSes->update('session', array('status' => 0), array('ses_id=?' => $row['ses_id'], 'prohlizec=?' => $row['prohlizec'], 'ip=?' => $row['ip']));					
				}
				if($row['status'] == 2 || $row['status'] == 3) {
					//Zend_Registry::set('user_id',$row['user_id']);
					$user = new Models_Helpers_User();
					if ($user->readDb($row['user_id'])) {
						$user->writeRegistry();
					}
				}

				//IT6: forced SSL
				//if(!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != 'on') $GLOBALS['ses_status'] = 0;       //kontrola zda jde o zabezpecenou komunikaci

				if (!Zend_Registry::isRegistered('user_id'))
					Zend_Registry::set('user_id', It6_Models_User::ID_INTERNET_ANONYMOUS);

				if(in_array($row['status'],$GLOBALS['ok_stavy'])){  // vsechno ok je bud prihlaseny nebo neprihlaseny

					return $row['data'];

				}

				return;

			}

			if(count($res) == 0){ #uzivatel vstoupil na stranky musi se vygenerovat nova session#

				$GLOBALS['ses_status'] = 0;
				$GLOBALS['find_old_session'] = 0;
				if (!Zend_Registry::isRegistered('user_id'))
					Zend_Registry::set('user_id', It6_Models_User::ID_INTERNET_ANONYMOUS);
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
		global $db_ses,$netip,$_time,$ses_status;
		if(isset($_POST['webservicepass'])) return;
		if($GLOBALS['ses_status'] == 1){//vyprsela session
			$GLOBALS['ses_status'] = 0;
			//vygeneruji nove session_id
			Zend_Session::regenerateId();
			$id = Zend_Session::getId();
		}

		//IT6: forced SSL
		//if(!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != 'on') $GLOBALS['ses_status'] = 0;       //kontrola zda jde o zabezpecenou komunikaci
		if(isset($GLOBALS['ses_status']) && in_array($GLOBALS['ses_status'],$GLOBALS['ok_stavy'])){  //sem pridavat pripady kdy je uzivatel prihlaseny a ne nejak vyhozeny a je potreba aktualizovat session

		$livebettingSession = Zend_Registry::get('ws')->Session->generateLivebettingSession($id);

		$sql2 = "REPLACE INTO session (ses_id,ip,prohlizec,data,time,status,start,user_id,livebetting_session)
       values('".Help::Slash($id)."','".$netip[0].".".$netip[1].".".$netip[2]."','".Help::Slash(md5($_SERVER['HTTP_USER_AGENT']."somestring"))."',
       '".Help::Slash($sess_data)."',".time().",".($GLOBALS['ses_status']==3?2:$GLOBALS['ses_status']).",
       '".($GLOBALS['ses_status']==3 || !isset($GLOBALS['ses_datum'])?date("Y.m.d H:i:s"):Help::Slash($GLOBALS['ses_datum']))."',
       ".(Zend_Registry::isRegistered('user_id')?Zend_Registry::get('user_id'): It6_Models_User::ID_INTERNET_ANONYMOUS).",'".Help::Slash($livebettingSession)."')";
		
			$res2 = $GLOBALS['db_ses']->query($sql2);

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

		$sql = "delete from session where ses_id='".Models_Helpers_Help::Slash($id)."'  and prohlizec='".Models_Helpers_Help::Slash(md5($_SERVER['HTTP_USER_AGENT']."somestring"))."' AND ip='" . $netip[0] . "." . $netip[1] . "." . $netip[2] . "'";
		$GLOBALS['db_ses']->query($sql);


	}


	public static function gc ($maxlifetime) {
		return true;
	}

}



?>
