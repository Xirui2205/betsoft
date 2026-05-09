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

class AjaxServer{

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
	 * stores the sounds to beused for incoming ticket notification in the confirmation window
	 * @access private
	 */

	private $confirmationSunds = array(
		'level1' => '',
		'level2' => '',
		'level3' => ''
	);

	/*
	private $confirmationSunds = array(
		'level1' => 'n1.ogg',
		'level2' => 'cannon_fire.ogg',
		'level3' => 'siren.ogg'
	);
	*/
	
	/**
	 * sets the default values for tresholds that determine which sound will be played for a given ticket
	 * @access private
	 */
	private $soundTresholds = array(
		'treshold1' => 2000,
		'treshold2' => 5000,
	);
	
	/**
	 * sets teh default sound volume
	 */
	private $defaultVolume = 0;

	/**
	 * Konstruktor
	 *
	 *Pokud neni identifikator spojeni predan vytvori se nove spojeni
	 *
	 * @param int $section id aktualni sekce
	 * @param PEAR::DB $db objekt spojeni s databazi
	 * @param PEAR::DB $dbGame objekt spojeni s databazi (DEPRECATED)
	 */
	public function __construct($section=0, $dbGame=null){

		$this->section =  $section;

		//IT6: for backward compatibility, could be removed if all use of PEAR::DB is rewritten to use of Zend_Db
		$this->dbGame = DbUtil::connectWebDb();
		$this->db = DbUtil::connectAdminDb();

		session_set_save_handler (
		array('It6_Session_Admin', 'open'),
		array('It6_Session_Admin', 'close'),
		array('It6_Session_Admin', 'read'),
		array('It6_Session_Admin', 'write'),
		array('It6_Session_Admin', 'destroy'),
		array('It6_Session_Admin', 'gc')
		);

		register_shutdown_function("session_write_close");
		
        $this->confirmationSunds['level1'] = Webservice_Parameter::getGlobalParameter('confirmation.sound.level1');
        $this->confirmationSunds['level2'] = Webservice_Parameter::getGlobalParameter('confirmation.sound.level2');
        $this->confirmationSunds['level3'] = Webservice_Parameter::getGlobalParameter('confirmation.sound.level3');

		if (!It6_Session_Admin::start($this->db)) throw new ExHandler('Nepodarilo se inicializovat Session',"admin_ex_db");

		if (!It6_Session_Admin::isAuthenticated()) {
			echo "SESSION ERROR";
			return;
		}

		$this->FindAction();

		It6_Session_Admin::end($this->db);

		//IT6: for backward compatibility, could be removed if all use of PEAR::DB is rewritten to use of Zend_Db
		$this->db->disconnect();
		$this->dbGame->disconnect();

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
		else if(isset($_GET['work']) && $_GET['work']==3 && isset($_GET['tab']) && $_GET['tab']==1){

			$this->BookCheckVal();

		}
		#Povolovani tiketu akce#
		else if(isset($_GET['work']) && $_GET['work']==3 && isset($_GET['tab']) && $_GET['tab']==2){

			$this->BookCheckAction();

		}
		#Povolovani tiketu#
		else if(isset($_GET['work']) && $_GET['work']==3){

			$this->BookCheck();

		}
		#Vyhledavani udalosti k danemu sportu#
		else if(isset($_GET['work']) && $_GET['work']==4){

			$this->UdalostLiveSelect();

		}
		#Vyhledani meny k uzivateli#
		else if(isset($_GET['work']) && $_GET['work']==5){

			$this->CurrencyUser();
		} elseif( array_key_exists('server',$_GET) && $_GET['server']=='anketa' ){
			$anketa=new AnketaAjaxServer();
			$anketa->runAction();
			print $anketa->getContent();
		}

	}

	/**
	 * Vyhledani meny k uzivateli
	 * @return string
	 */
	public  function CurrencyUser(){

		$w = "1";
		if(isset($_GET['info']) && $_GET['info'] == "uid") $w = "b.user_id=".intval($_GET['dat']);
		if(isset($_GET['info']) && $_GET['info'] == "nick") $w = "b.nick='".Help::SLash($_GET['dat'])."'";
		//      die(var_dump($w));

		$sth = $this->dbGame->prepare("SELECT mena_text FROM mena a INNER JOIN uzivatel b ON a.mena_id=b.mena_id where ".$w);
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
		$udalost = $help = $sport_str = "";
		$prava_udalost = $sport_udalost = array();


		$sql = "select a.sport_id,a.nazev,e.text AS udalost_nazev,b.udalost_id,d.text AS onazev,c.oblast_id from sport a inner join udalost b on a.sport_id=b.sport_id inner join oblast c on c.oblast_id=b.oblast_id inner join preklady d on d.index_pole=c.nazev inner join preklady e on e.index_pole=b.nazev where d.lang_id=1 and e.lang_id=1 and a.sport_id=". intval($_GET['sport']) ." and platne_do>'".It6_Date::dbNow()."'  order by a.sport_id,trim(d.text),trim(e.text)";
		$res =& $this->dbGame->query($sql);
		if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sportu a udalosti',"admin_ex_db");

		$help2 = '';
		while ($row =& $res->fetchRow()){

	  $sport_udalost[$row['udalost_id']] = $row['sport_id'];

	  if($help == "" || $help != $row['sport_id']){
	   $help = $row['sport_id'];;
	   $r = $preklad->selectData("where lang_id=1 and index_pole='".Help::Slash($row['nazev'])."'");
	   if (!$row2 =& $r->fetchRow() || mb_strlen($row2['text']) < 0) $row2['text'] = "Překlad nenalezen";

	   if((isset($_POST['sportx']) && $_POST['sportx']!=0 && $_POST['sportx']==$row['sport_id']) || !isset($_POST['sportx']) || $_POST['sportx']==0)$udalost .= "<optgroup label=\"".Help::Html(mb_strtoupper($row2['text']))."\">";
	   $sport_str  .= "<option  value=\"".$row['sport_id']."\" ".(isset($_POST['sportx']) && $_POST['sportx'] == $row['sport_id']?"selected=\"selected\"":"").">".Help::Html($row2['text'])."</option>";

	  }


	  if(($help2 == '' || $help2 != $row['oblast_id']) && ((isset($_POST['sportx']) && $_POST['sportx']!=0 && $_POST['sportx']==$row['sport_id']) || !isset($_POST['sportx']) || $_POST['sportx']==0)){
	   if (ctype_digit($help2)) $udalost .= '</optgroup>';
	   $udalost .= '<optgroup label="'.Help::Html($row['onazev']).'">';
	   $help2 = $row['oblast_id'];
	  }
	  if((isset($_POST['sportx']) && $_POST['sportx']!=0 && $_POST['sportx']==$row['sport_id']) || !isset($_POST['sportx']) || $_POST['sportx']==0) {$this->udalost_name[$row['udalost_id']] = Help::Html($row2['text']);$udalost .= "<option  value=\"".$row['udalost_id']."\" ".(isset($_POST['udalost']) && $_POST['udalost'] == $row['udalost_id']?"selected=\"selected\"":"").">".Help::Html($row['udalost_nazev']). "</option>";}

	  if($help != $row['sport_id']){
	   if((isset($_POST['sportx']) && $_POST['sportx']!=0 && $_POST['sportx']==$row['sport_id']) || !isset($_POST['sportx']) || $_POST['sportx']==0)$udalost .= "</optgroup>";
	  }

		}

		echo $udalost;
		return;

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
	//IT6: Tak toto je vysmech, webservice ktera musi bezet na stejnem stroji jako klient (data se predavaji pres lokalni soubor)
	//     Navic nikde nejsou definovany konstanty WEBSERVICE_*.
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


	private function updateCouponData($db, $couponId, $status, $modified = false) {
		$data = array(
			'status' => $status,
			'confirm_date' => It6_Date::dbNow(),
			'bookmaker_id' => It6_Session_Admin::getUserData('id'),
		);
		if (It6_Models_Ticket::COUPON_STATUS_PROLONGED == $status)
			$data['prolonged'] = 1;
		if (false !== $modified)
			$data['modified'] = (is_array($modified) ? Zend_Json::encode($modified) : $modified);
		return $db->update('coupon_data', $data,
			array(
				'coupon_id=?' => $couponId,
				'status IN (?)' => array(
					It6_Models_Ticket::COUPON_STATUS_IN_ACCEPTATION,
					It6_Models_Ticket::COUPON_STATUS_PROLONGED,
				),
			)
		);
	}

	public static function computeTicketHash($couponId, $userId, $adminId, $modified, $data) {
		return md5($couponId . ':' . $userId . ':' . $adminId . ':' . md5($modified)  . ':' . md5($data));
	}

	/**
	 * Provede akce
	 *
	 *
	 */
	public function BookCheckAction(){

		if(!isset($_GET['action']) || !isset($_GET['info']) || !isset($_GET['id']))
		return;

		if(isset($_SESSION['last_call']) && $_SESSION['last_call'] == time())
		return;

		$_SESSION['last_call'] = time();

		// action:
		//0 resetovat na STATUS_NEW
		//1 povolit
		//2 zamitnout
		//3 prodlouzit povolovani
		//4 jina castka
		$actionNames = array(
			0 => 'new',
			1 => 'accept',
			2 => 'reject',
			3 => 'prolong',
			4 => 'modify',
		);

		$action = $_GET['action'];
		$info = $_GET['info'];
		//$userId = $_GET['user']; // ticket owner
		//$adminId = (empty($_GET['admin']) ? 0 : $_GET['admin']); // ticket creator
		$couponId = $_GET['id'];

		$dbGame = Zend_Registry::get('zdb_game');
		$dbGame->beginTransaction();
		try {
			//if (1 == $action || 2 == $action || 4 == $action || 3 == $action) {
			$hash = '';
			$rows = $dbGame->select()
				//->forUpdate()
				->from('coupon_data', array('coupon_id', 'data', 'modified', 'user_id', 'admin_id', 'prolonged'))
				->where('coupon_id=?', $couponId)
				->query()
				->fetchAll();
			if (empty($rows)) {
				$dbGame->rollback();
				return;
			}
			else {
				$row = $rows[0];
				$couponId = $row['coupon_id'];
				$userId = $row['user_id'];
				$adminId = $row['admin_id'];
				$data = Zend_Json::decode($row['data']);
				$data['userId'] = $userId;
				$hash = self::computeTicketHash($couponId, $userId, $adminId, $row['modified'], $row['data']);
				$bets = '';
				foreach ($data['bet'] as $bet) {
					$bets .= $bet['id_bet'] . ';';
				}
				//if (3 != $action) {
				if (1 == $action || 2 == $action || 4 == $action) {
					$bookmakerId = It6_Session_Admin::getUserData('id');
					//TODO: use Log class, log adminId from coupon_data too
					$amount = $_GET['amount'];
					$proveAmount = (4 == $action ? floatval($info) : floatval($amount));
					It6_Log::info(
						'Confirmation: bookmaker action - ' . $actionNames[$action],
						It6_Log::TAG_TICKET_APPROVAL,
						array(
							'userId' => $userId,
							'adminId' => $bookmakerId,
							'couponId' => $couponId,
							'coupon' => $row['data'],
							'modified' => $row['modified'],
							'prolonged' => $row['prolonged'],
						)
					);
					$dbGame->insert('bookmaker_ticket_prove_log', array(
					//TODO: add coupon_id
						'user_id' => $userId,
						'bookmaker_id' => $bookmakerId,
						'sazky' => $bets,
						'req_amount' => $amount,
						'prove_amount' => $proveAmount,
						'status' => $action,
						'date' => It6_Date::dbNow()
					));
				}
			}
			//}

			if (!isset($_GET['hs']) || $_GET['hs'] != trim($hash))
				$action = 2;

			$errors = array();

			if (1 == $action) {
				$this->updateCouponData($dbGame, $couponId, It6_Models_Ticket::COUPON_STATUS_MARKED_AS_ACCEPTED);
				//TODO: check what this does and get rid of this
				//$this->callService(intval($_GET['user']));
			}
			else if(2 == $action){
				$this->updateCouponData($dbGame, $couponId, It6_Models_Ticket::COUPON_STATUS_MARKED_AS_REJECTED);
			}
			else if(3 == $action){
				$this->updateCouponData($dbGame, $couponId, It6_Models_Ticket::COUPON_STATUS_PROLONGED);
			}
			else if(4 == $action){
				$amount = It6_Models_Currency::convertAmountToUserCurrency($userId, $info, $dbGame);
				if (!empty($amount)) {
					$helper = new It6_Models_Ticket($data, It6_Models_Ticket::DATA_AJAX, 'user', $dbGame);
					$helper->computeAggregates();
					$origAmount = $helper->stake;
					if ($amount < $origAmount) {
						$changes = array('stake' => $amount);
						$modified = $helper->recalculatePreapproved($changes, $dbGame);
						//TODO: if recalculation was unsuccessful then indicate error(s) to bookmaker
						//TODO: ? validate coupon (and display error messages to bookmaker), update coupon status only if validation is OK
						if (empty($modified))
							$errors[] = 'Internal error: recalculation failed';
						else {
							$validator = new It6_Models_TicketValidator($helper);
							//NOTE: possible to make more or all checks
							$result = $validator->isProveTicket();
							if (true !== $result) {
								foreach ($result as $error) {
									if (!empty($error['errorMessage'])) {
										$msg = (empty($error['errorMessageParam'])
											? I18n::trans($error['errorMessage'])
											: I18n::transParam($error['errorMessage'], $error['errorMessageParam'])
										);
										if (!empty($error['field']))
											$msg .= " (bet ID #{$error[field]})";
										$errors[] = $msg;
									}
								}
							}
							else
								$this->updateCouponData($dbGame, $couponId, It6_Models_Ticket::COUPON_STATUS_MODIFIED, $modified);
						}
					}
				}
				else
					throw new ExHandler('Amount not converted to user currency! user_id=' . $userId);
			}
			else if(0 == $action){
				$this->updateCouponData($dbGame, $couponId, It6_Models_Ticket::COUPON_STATUS_NEW);
			}

			if (!empty($errors)) {
				if (!array_key_exists('confirmationErrors', $_SESSION))
					$_SESSION['confirmationErrors'] = array();
				$_SESSION['confirmationErrors'][$couponId] = array();
				foreach ($errors as $error) {
					$_SESSION['confirmationErrors'][$couponId][] = $error;
				}
			}
			else
				unset($_SESSION['confirmationErrors'][$couponId]);
			
			$dbGame->commit();
		}
		catch (Exception $e) {
			$dbGame->rollback();
			throw $e;
		}
	}


	/**
	 * Kontrola tiketu vracenid at
	 * saldo = "soucet financnich vkladu" - "soucet financich vyberu" - "zustatek herniho uctu" = kolik penez prinesl uzivatel sazkovce
	 * @param int $uid id uzivatele
	 *
	 */
	public function UserSaldo($uid){

		//    if(count($this->mena)==0){
		//      $sql = "select e.kurz,e.mena_id from kurzmena e where e.platny_od<=now() and e.platny_do>=now() ";
		//      $res =& $this->dbGame->query($sql);
		//      while ($row =& $res->fetchRow()){
		//    	$this->mena[$row['mena_id']] = $row['kurz'];
		//      }
		//
		//    }

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

		 $saldo = round( ( ($vklad - $vyber - $zustatek) /$this->mena[$mena_id]) ,2);
		 */
		//	$saldo = round( ( (-$zustatek) /$this->mena[$mena_id]) ,2);
		$rate = It6_Models_Currency::get($mena_id, 'rate', $this->dbGame);
		$saldo = round(-$zustatek / $rate, 2);

		return $saldo;
	}

	/**
	 * Kolik bylo jiz na dany sloupec nasazeno
	 *
	 * @param int $betid id sazky
	 * @param array $mena kurzy meny
	 *
	 * return float
	 */
	public function sazkaVsazeno($betid,$mena){

		$ticket = $ticket_sazka_sloupec = $ticket_sazka_sloupec2 = $sloupec = array();

		#Vyberu vsechny tickety na nichz je dana sazka#
		$sql = "select ticket_id,sloupec_id from ticket_pohled where sazka_id=".intval($betid)." group by ticket_id" ;
		$res =& $this->dbGame->query($sql);
		if(DB::isError($res)) { $this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: vyber z pohledu  ticket_pohled',"admin_ex_db");}

		$ticket[] = 0;

		while($row = $res->fetchRow()){

			$ticket[] = $row['ticket_id'];
			$ticket_sazka_sloupec[$row['ticket_id']] = $row['sloupec_id'];

		}


		#jaky ma podil z castky vsazeneho na ticketu 1 sazka#
		$sql = "SELECT  a.ticket_id,b.mena_id,castka,(castka/count(*)) AS podil FROM ticket_pohled a inner join uzivatel b on a.user_id=b.user_id where a.ticket_id in(".implode(",",$ticket).") group by a.ticket_id";
		$res =& $this->dbGame->query($sql);
		if(DB::isError($res)) { $this->dbGame->autoCommit(true);throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber z pohledu  sazka_pohled',"admin_ex_db");}


		while($row = $res->fetchRow()){

	  if(!isset($mena[$row['mena_id']])) throw new ExHandler('Neni definovan kurz na EUR',"admin_ex_page");


	  if(!isset($ticket_sazka_sloupec2[$ticket_sazka_sloupec[$row['ticket_id']]])) $ticket_sazka_sloupec2[$ticket_sazka_sloupec[$row['ticket_id']]] = ($row['podil']/$mena[$row['mena_id']]);
	  else $ticket_sazka_sloupec2[$ticket_sazka_sloupec[$row['ticket_id']]] += ($row['podil']/$mena[$row['mena_id']]);



		}

		#Vyber sloupcu a nazvu k dane sazce#
		$sql = "select sloupec_id,nazev from sazka_pohled where sazka_id=".intval($betid)." group by sloupec_id";
		$res =& $this->dbGame->query($sql);
		if(DB::isError($res)) { $this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: vyber sazky',"admin_ex_db");}

		$preklady = new Preklady();
		while ($row =& $res->fetchRow()){


	  $res2 = $preklady->FindPreklad($row['nazev'],1);
	  if($res2[1]  == "Translation not found") $res2[1] = $row['nazev'];
	  $sloupec[$row['sloupec_id']] = $res2[1];

		}

		$vrat = '<span style="color:#ADFF2F;font-size:14px;">| ';


		foreach($sloupec as $k=>$h){

		 $vrat .=  '<span style="color:#FFEFD5;font-size:14px;font-weight:bold">'.$h.'</span> - '.(isset($ticket_sazka_sloupec2[$k])?round($ticket_sazka_sloupec2[$k],2):0).'  |  ';

		}

		$vrat .= ' </span>';

		return $vrat;

	}

	/**
	 * @return array Cached values of confirmation times array(time, time_more)
	 */
	public function getTimes() {
		static $times = null;
		if (!isset($times)) {
			$dbAdmin = Zend_Registry::get('zdb_admin');
			$rows = $dbAdmin->select()
				->from('parameter', array('name', 'value'))
				->where('name IN (?)', array(
					It6_Models_Parameter::NAME_TICKET_CONFIRM_TIME,
					It6_Models_Parameter::NAME_TICKET_CONFIRM_TIME_MORE,
				))
				->query()
				->fetchAll();
			$params = array();
			foreach ($rows as $row)
				$params[$row['name']] = $row['value'];
			$times = array(
				intval($params[It6_Models_Parameter::NAME_TICKET_CONFIRM_TIME]),
				intval($params[It6_Models_Parameter::NAME_TICKET_CONFIRM_TIME_MORE])
			);
		}
		return $times;
	}

	/**
	 * Converts array value to string code of status message.
	 * @param string|array $statusMessage
	 * @param string
	 */
	public function getCouponStatusMessage($statusMessage) {
		if (is_array($statusMessage)) {
			$codes = array_keys($statusMessage); // should have just zero or one items
			return (empty($codes) ? '' : $codes[0]);
		}
		else
			return $statusMessage;
	}

	/**
	 * Retrieves all bet status messages (and data) for all bets for status message that contains it.  
	 * @param string|array $statusMessage
	 * @return array (betId => struct) Struct fields:
	 *               <ul>
	 *                 <li>messages ... array of string messages</li>
	 *                 <li>[ticketCount] ... integer Ticket count from bet user history</li>
	 *                 <li>[stakeBalance] ... float Bet user history stake balance</li>
	 *               </ul>
	 */
	public function getCouponBetStatusMessages($statusMessage) {
		if (is_array($statusMessage)) {
			$bets = array();
			foreach ($statusMessage as $code => $data) {
				switch ($code) {
				case It6_Models_Ticket::CONFIRMATION_REASON_BET_HISTORY:
					foreach ($data as $betId => $info) {
						$bets[$betId]['messages'][] = $code;
						$bets[$betId] = array_merge($bets[$betId], $info);
					}
					break;
				case It6_Models_Ticket::CONFIRMATION_REASON_WILL_EXCEED_RISK_LIMIT:
				case It6_Models_Ticket::CONFIRMATION_REASON_EXCEEDED_RISK_LIMIT:
					foreach ($data as $betId) {
						$bets[$betId]['messages'][] = $code;
					}
					break;
				default:
					// no bet specific messages
					break;
				}
			}
			return $bets;
		}
		else
			return array();
	}

	/**
	 * @param string $statusMessage
	 * @return string HTML for given status message
	 */
	public function getCouponStatusMessageHtml($statusMessage) {
		$type = 'unknown';
		switch ($statusMessage) {
		case It6_Models_Ticket::CONFIRMATION_REASON_APPROVAL_GROUP:
			$type = 'normal';
			$text = 'Approval group';
			break;
		case It6_Models_Ticket::CONFIRMATION_REASON_BET_HISTORY:
			$type = 'warning';
			$text = 'Opakovaná sázka';
			break;
		case It6_Models_Ticket::CONFIRMATION_REASON_LIVE:
			$type = 'normal';
			$text = 'Live';
			break;
		case It6_Models_Ticket::CONFIRMATION_REASON_STAKE:
			$type = 'normal';
			$text = 'Výše vkladu';
			break;
		case It6_Models_Ticket::CONFIRMATION_REASON_WATCHED:
			$type = 'warning';
			$text = 'Sledován';
			break;
		case It6_Models_Ticket::CONFIRMATION_REASON_WILL_EXCEED_RISK_LIMIT:
		case It6_Models_Ticket::CONFIRMATION_REASON_EXCEEDED_RISK_LIMIT:
			$type = 'warning';
			$text = 'Risk limit';
			break;
		default:
			break;
		}
		switch ($type) {
		case 'normal':
			$html = " <span class=\"confirm-reason confirm-reason-normal\">$text<span>";
			break;
		case 'warning':
			$html = " <span class=\"confirm-reason confirm-reason-warning\">$text<span>";
			break;
		default:
			$html = " <span class=\"confirm-reason\">$text<span>";
			break;
		}
		return $html;
	}

	/**
	 * Creates HTML from status message data for given bet.
	 * @param integer $betId
	 * @param struct $betStatusMessages @see getCouponBetStatusMessages return value
	 * @return string HTML with staus message(s) for given bet
	 */
	public function getCouponBetStatusMessagesHtml($betId, $betStatusMessages) {
		if (empty($betStatusMessages[$betId]))
			return '';
		$html = '';
		foreach ($betStatusMessages[$betId]['messages'] as $msg) {
			$html .= $this->getCouponStatusMessageHtml($msg);
		}
		return $html;
	}

	/**
	 * Kontrola tiketu vraceni dat
	 *
	 * status  0:nic,1:zacne se zobrazovat,2:povoleny,3:nepovoleny,4:povoleny s jinou castkou 5:pozastaveno resi se
	 */
	public function BookCheckVal(){

		$dbGame = Zend_Registry::get('zdb_game');
		$ws = Zend_Registry::get('ws');

		$liveIds = (isset($_GET['live']) ? explode('X', $_GET['live']) : false);
		$betradar = isset($_GET['br']);

		list($confirmTime, $confirmTimeMore) = $this->getTimes();

		$res = $dbGame->select()
			->from(array('u' => 'uzivatel'), array('mena_id', 'nick', 'e_testovaci', 'jmeno', 'prijmeni', 'vyhernost', 'user_id', 'book_info', 'anonymous'))
			->join(array('c' => 'coupon_data'), 'u.user_id=c.user_id', array('admin_id', 'host_id', 'coupon_id', 'data', 'status', 'date', 'modified', 'status_message'))
			->join(array('h' => MY_DB.'.host'), 'c.host_id=h.id', array())
			->join(array('b' => MY_DB.'.branch'), 'h.branch_id=b.id', array('branchId' => 'id', 'branchName' => 'name'))
			->where('c.live=?', $betradar ? 1 : 0)
			->where('(c.status=' . It6_Models_Ticket::COUPON_STATUS_IN_ACCEPTATION
				. ' AND c.date>=\'' . It6_Date::dbNow(-$confirmTime)
				. '\') OR (c.status=' . It6_Models_Ticket::COUPON_STATUS_PROLONGED
				. ' AND c.date>=\'' . It6_Date::dbNow(-$confirmTimeMore) . '\')'
			)
			->query();

		$tickets = array();
		$couponIds = array();
		$translateKeys = array();

		while ($row = $res->fetch()) {
			$data = Zend_Json::decode($row['data']);
			if ($betradar) {
				// check that all bets are live betradar events as requested
				$betIds = array();
				foreach ($data['bet'] as $betId => $bet)
					$betIds[] = $bet['id_bet'];
				$select = $dbGame->select()
					->from(array('l' => 'live'), array('betId' => 'l.sazka_id', 'c' => 'COUNT(l.event_id)'))
					->joinLeft(array('b' => 'live_betradar_event'), 'l.event_id=b.event_id', array())
					->where('l.sazka_id IN (?)', $betIds);
				if (!empty($liveIds))
					$select->where('l.event_id IN (?)', $liveIds);
				$rows2 = $select->group('l.sazka_id')->query()->fetchAll();
				$betradarBets = array();
				foreach ($rows2 as $row2)
					$betradarBets[$row2['betId']] = $row2['c'];
				$skip = false;
				foreach ($betIds as $betId) {
					if (!array_key_exists($betId, $betradarBets) || 0 == $betradarBets[$betId]) {
						$skip = true;
						break;
					}
				}
				if ($skip) // found bet from betradar that has no live betradar events
					continue;
			}
			$data['userId'] = $row['user_id'];
			$helper = new It6_Models_Ticket($data, It6_Models_Ticket::DATA_AJAX, $row['mena_id'], $dbGame);
			$helper->convertStakesToCentralCurrency($row['user_id'], $dbGame);
			$helper->computeAggregates();

			$bookInfo = Help::Script(nl2br($row['book_info']));
			$couponId = $row['coupon_id'];
			$couponIds[] = $couponId;
			try {
				$winRatioData = Zend_Registry::get('ws')->User->getWinRatioData($row['user_id']);
				if (It6_ArrayWrapper::isArray($winRatioData)) {
					$winRatio = $winRatioData['winRatio'];
					$totalStake = floatval($winRatioData['stake']);
				}
				else {
					$winRatio = false;
					$totalStake = 0.0;
				}
				if (false !== $winRatio && null !== $winRatio) {
					$winRatio = round( $winRatio * 100, 2 ); // only part above/below 100%
				}
			}
			catch (Exception $e) {
				$winRatio = false;
			}
			if (false === $winRatio || null === $winRatio) {
				$winRatio = '?';
				$winRationCssClass = 'win-ratio-unknown';
			}
			else if (!empty($row['anonymous'])) {
				$winRationCssClass = 'win-ratio-unknown';
			}
			else {
				if ($totalStake <= 10000) {
					$winRationCssClass = 'win-ratio-neutral';
				}
				else {
					if ($totalStake <= 30000) {
						$levels = array(10, 13, 17, 20);
					}
					else if ($totalStake <= 60000) {
						$levels = array(8, 11, 14, 17);
					}
					else if ($totalStake <= 100000) {
						$levels = array(6, 8, 10, 12);
					}
					else if ($totalStake <= 200000) {
						$levels = array(5, 7, 9, 10);
					}
					else {
						$levels = array(4, 5, 6, 7);
					}
					if ($winRatio <= $levels[0]) {
						$winRationCssClass = 'win-ratio-harmless';
					}
					else if ($winRatio <= $levels[1]) {
						$winRationCssClass = 'win-ratio-rookie';
					}
					else if ($winRatio <= $levels[2]) {
						$winRationCssClass = 'win-ratio-mediocre';
					}
					else if ($winRatio <= $levels[3]) {
						$winRationCssClass = 'win-ratio-good';
					}
					else {
						$winRationCssClass = 'win-ratio-hitman';
					}
				}
			}
			if (is_numeric($winRatio) && 0 < $winRatio) {
				$winRatio = "+$winRatio";
			}
			$statusMessage = Webservice_Coupon::decodeStatusMessage($row['status_message']);
			$betStatusMessages = $this->getCouponBetStatusMessages($statusMessage);
			$statusMessage = $this->getCouponStatusMessage($statusMessage);
			$ticket = array(
				'id' => $couponId,
				'type' => $data['type'],
				'user' => $row['user_id'],
				'admin' => $row['admin_id'],
				'branch' => $row['branchName'] . ' (' . $row['branchId'] . ')',
				'book_info' => $bookInfo,
				'hash' => self::computeTicketHash($row['coupon_id'], $row['user_id'], $row['admin_id'], $row['modified'], $row['data']),
				//TODO Ma to byt v gm nebo ne? Ted je. Jinak by se muselo dat , false
				//TOBEDONE v db je coupon.date jako DATETIME, uklada se v
				//   {Model_Ajax_Create::ticketSave, Models_Control_Ticket, ... kdo vi ... } jako date()
				'datum' => It6_Date::fromDbAsTimestamp($row['date']),
				'jmeno' => $row['jmeno'] . ' ' . $row['prijmeni']
				. ' (' .$row['nick'] . ' ' . ($row['e_testovaci']<>'test' ? '' : ' - <strong>TEST</strong>'). ') '
				. ( mb_strlen($bookInfo) > 0 ? '<span style="color:yellow">[i]</span>' : ''),
				//TODO: reimplement UserSaldo -- use WS
				//'vyhernost' => 'TODO: reimplement using WS' . '<br />'.$this->UserSaldo($row['user_id']).' <br />'.round($row['vyhernost'],2),
				'vyhernost' => $winRatio,
				'winRatioCssClass' => $winRationCssClass,
				'win' => $helper->win,
				'vsazeno' => $helper->stake,
				'pointType' => empty($helper->pointType) ? '' : 'Point ticket',
				'rateAdvance' => empty($helper->rateAdvance) ? '' : 'Rate advance: ' . (($helper->rateAdvance-1)*100) . '%',
				'bet' => array(),
				'errors' => (empty($_SESSION['confirmationErrors']) || empty($_SESSION['confirmationErrors'][$couponId])
					? array() : $_SESSION['confirmationErrors'][$couponId]),
				'statusMessage' => $statusMessage,
				'statusMessageHtml' => $this->getCouponStatusMessageHtml($statusMessage),
			);
			$confirmData = $helper->readBetDataForConfirmation($dbGame);
			foreach ($data['bet'] as $bet) {
				$betId = $bet['id_bet'];
				$columnId = $bet['id_col'];
				if (!array_key_exists($betId, $confirmData) || !array_key_exists($columnId, $confirmData[$betId]))
				continue;
				$ticket['bet'][$betId][$columnId] = $confirmData[$betId][$columnId];
				$betConfirmData = &$ticket['bet'][$betId][$columnId];
				$craNames = array();
				foreach ($betConfirmData['columnRiskAmounts'] as $cra) {
					$craNames[] = $cra['name'];
				}
				$translateKeys = array_merge($translateKeys,
					array(
						$betConfirmData['texts']['columnName'],
						$betConfirmData['texts']['eventName'],
						$betConfirmData['texts']['sportName'],
						$betConfirmData['texts']['regionName'],
					),
					$craNames
				);
			}
			//TODO:PETR pridat info pro maxikombi - z helperu
			if ($helper->isMaxicombinatorCompatible()) {
				$combConfirmData = $helper->getCombinationsDataForConfirmation();
				foreach ($combConfirmData as $key => $ccData) {
					$ticket[$key] = $ccData;
				}
				unset($combConfirmData);
			}
			$tickets[] = $ticket;
		}
		//TODO: use current language
		$tr = It6_Models_Translator::translate($translateKeys, 1, $dbGame);
		$lang = 1;
		foreach ($tickets as &$ticket) {
			foreach ($ticket['bet'] as $betId => &$betCols) {
				foreach ($betCols as $colId => &$bet) {
					$ratios = '<span style="color:#ADFF2F;font-size:14px;">| ';
					foreach ($bet['columnRiskAmounts'] as $cra)
					$ratios .=  '<span style="color:#FFEFD5;font-size:14px;font-weight:bold">'.$tr[ $cra['name'] ].'</span> - '
					. $cra['riskAmount'] . '  |  ';
					$ratios .= '</span>';

					$bet['typ'] = $bet['texts']['typeName'];
					$bet['sloupec'] = $tr[ $bet['texts']['columnName'] ];
					$bet['kurz'] = $bet['rate'];
					$bet['live'] = (empty($bet['isLive']) ? '' : '[LIVE]');
					$bet['vsazeno'] = ('simple' == $ticket['type'] ? $bet['amount'] : 0);
					$bet['podil_sazek'] = $ratios;
					$bet['text'] = '<strong>' . $tr[ $bet['texts']['sportName'] ]
						. '->' . $tr[ $bet['texts']['regionName'] ]
						. '->' . $tr[ $bet['texts']['eventName'] ] . '</strong><br />';
					if (!empty($bet['isLlive']))
					$bet['text'] .= $bet['texts']['homeTeam'] . ' - ' .  $bet['texts']['awayTeam'] . ' ' . $bet['texts']['betText'];
					else
					$bet['text'] .= $bet['texts']['betText'];
					$bet['statusMessageHtml'] = $this->getCouponBetStatusMessagesHtml($betId, $betStatusMessages);
					unset($bet['texts']); // not to be sent to client
				}
			}
		}
		if (!empty($_SESSION['confirmationErrors'])) {
			$oldIds = array();
			foreach ($_SESSION['confirmationErrors'] as $id => $_) {
				if (!in_array($id, $couponIds))
					$oldIds[] = $id;
			}
			foreach ($oldIds as $id)
				unset($_SESSION['confirmationErrors'][$id]);
		}
		
		$jsonData = array(
			'update-time' => It6_Date::nowAsTime(),
			'tickets' => $tickets
		);
		echo Zend_Json::encode($jsonData);
	}

	/**
	 * Kontrola tiketu
	 *
	 */
	public function BookCheck(){

		$live = '';
		if(isset($_GET['live']) )
		$live = ', live:\''. $_GET['live'] . '\'';
		$betradar = '';
		if (isset($_GET['br']))
		$betradar = ', br:1';

		list($confirmTime, $confirmTimeMore) = $this->getTimes();

		//TODO: move following into separate PHTML template?
		?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<title>Povolování tiketů</title>
<script type="text/javascript">
   var protocol = '<?= PROTOCOL ?>';
   var host = '<?= ADMINHOST ?>';
 </script>
<script src="<?= JQUERY_URI ?>"></script>
<script src="/js/jquery.dynDateTime.js?r=<?= RELEASE_REV ?>" type="text/javascript" language="javascript"></script>
<script src="/js/mainscript.js?r=<?= RELEASE_REV ?>" type="text/javascript" language="javascript"></script>
<style>
  .confirm-reason { }
  .confirm-reason-normal { }
  .confirm-reason-warning { text-transform: uppercase; text-decoration: blink; font-weight: bold; color: red; background-color: white; }
</style>
<script>
  // <![CDATA[
  var blockClick = false;
  var json_stop = new Object();
  var json_info_coupon = new Array();

  $(document).ready(function() {

     var  json_info_pole = new Object();
     var  json_info_new = new Array();
     var  json_info_old = new Array();

     jQuery.extend({
       LoadAjaxData: function() {

        blockClick = true;

        $('#load').css("display","block");

        $.ajax({
         url: 'ajax.server.php',
         data: { work:3, tab:1<?= $betradar . $live ?> },
         type: 'GET',
         dataType: 'json',
         error: function() { window.status = 'Error loading XML document'; },
         success: function(json) {
           $('#last-update-time').html(json['update-time']);
           /*if(typeof json[0] != \'undefined\'){window.focus();}*/
           $('#load').css('display', 'none');
           /*window.status = "OK";*/
           $('tr[id^="coupon_"]').removeClass('new-coupon');
           json_info_new = new Array();
           json_info_old = new Array();

           for (var y=0; y<json_info_coupon.length; y++){
             var cid = json_info_coupon[y];
             var found = false;
             for (vl in json['tickets']) {
               if (parseInt(json['tickets'][vl]['id']) == cid) {
                 found = true;
                 break;
               }
             }
             if (!found)
               json_info_old.push(cid);
           }

           for (vl in json['tickets']) {
              var statusx = true;
              var found = false;
              var cid = parseInt(json['tickets'][vl]['id']);
              for (var y=0; y<json_info_coupon.length; y++){
                if (json_info_coupon[y] == cid) {
                  found = true;
                  if (json['tickets'][vl]['datum'] == json_info_pole[cid]['datum'])
                    statusx = false;
                }
              }

              if (statusx == true) {
                if (json['tickets'][vl]['vsazeno'] <= $('#new-ticket-notice-treshold-1').val()) {
                    var soundFile = '<?=$this->confirmationSunds['level1']?>';
                }
                else if (json['tickets'][vl]['vsazeno'] > $('#new-ticket-notice-treshold-1').val() && json['tickets'][vl]['vsazeno'] <= $('#new-ticket-notice-treshold-2').val()) {
                    var soundFile = '<?=$this->confirmationSunds['level2']?>';
                }
                else {
                    var soundFile = '<?=$this->confirmationSunds['level3']?>';
                }
                volume = $('#new-ticket-notice-volume').val() / 100;
                if(volume != 0) {
                    audioEl = new Audio('audio/' + soundFile);
                    audioEl.volume = volume;
                    audioEl.play();
                }
                
                for(var y=0; y<json_info_coupon.length; y++) {
                  if (json_info_coupon[y] == cid)
                    json_info_coupon[y] = 0;
                }
                $('#coupon_' + cid + ',#couponError_' + cid).remove();
                $('tr[name="bet_' + cid + '"]').remove();

                json_l = json_info_coupon.length;
                json_info_coupon[json_l] = cid;
                json_info_new[json_info_new.length] = cid;
                json_info_pole[cid] = json['tickets'][vl];
              }
           }
           jQuery.ShowBet();
           blockClick = false;
         }
        });

       }
     });

     jQuery.extend({
       ShowBet:function(){
         for (var y=0; y<json_info_old.length; ++y) {
           var cid = json_info_old[y];
           $("#coupon_"+cid+",#couponError_"+cid).remove();
           $("tr[name=\'bet_"+cid+"\']").remove();
           if (json_info_pole[cid] !== undefined)
             delete json_info_pole[cid];
           var i = json_info_coupon.indexOf(cid);
           if (-1 != i)
             json_info_coupon.splice(i, 1);
         }
         $('tr[id^="couponError_"]').remove();
         for(var y=0; y<json_info_new.length; y++){
           var id = json_info_new[y];
           var type = json_info_pole[id]['type'];
           var text = '';
           if (type == 'simple') {
             $('#bookdata').append(
               '<tr id="coupon_' + id + '" onmouseover="BetShow(' + id + ')" class="new-coupon">'
               + '<td class="' + json_info_pole[id]['winRatioCssClass'] + '">'
               + '<span id="bi_' + json_info_pole[id]['id'] + '" '
                 + 'onmouseover="jQuery.BInfo(1,\'' + json_info_pole[id]['book_info'] + '\', ' + json_info_pole[id]['id'] + ')" '
                 + 'onmouseout="jQuery.BInfo(0)">' + json_info_pole[id]['jmeno']
               + '</span> Pobočka: ' + json_info_pole[id]['branch']
               + ' <strong>YIELD: ' + json_info_pole[id]['vyhernost'] + '% </strong>' + json_info_pole[id]['statusMessageHtml']
               + '</td>'
               + '<td>' + json_info_pole[id]['vsazeno'].toFixed(2) + '</td>'
               + '<td>' + json_info_pole[id]['win'].toFixed(2) + '</td>'
               + '<td>Jednoduchá</td><td><a href="javascript:jQuery.UseBet(' + id + ',1,'
                 + json_info_pole[id]['vsazeno'].toFixed(2) + ',\'' + json_info_pole[id]['hash'] + '\');">'
                 + '<img src="_clip/check_yes.gif" alt="Povolit" class="no" /></a> &nbsp;&nbsp;'
                 + '<a href="javascript:jQuery.UseBet(' + id + ',2,' + json_info_pole[id]['vsazeno'].toFixed(2) + ','
                 + '\'' + json_info_pole[id]['hash'] + '\');"><img src="_clip/check_no.gif" alt="Zamítnout" class="no" /></a> &nbsp;&nbsp; '
                 + '<a href="javascript:jQuery.UseBet(' + id + ',3,' + json_info_pole[id]['vsazeno'].toFixed(2)+ ','
                 + '\'' + json_info_pole[id]['hash'] + '\');"><img src="_clip/check_clock.gif" alt="Zastavit" class="no" /></a> &nbsp;&nbsp;'
                 + '<input type="text" class="json_text" value="1" id="other_' + id + '"/>'
                 + '<a href="javascript:jQuery.UseBet(' + id + ',4,' + json_info_pole[id]['vsazeno'].toFixed(2) + ','
                 + '\'' + json_info_pole[id]['hash'] + '\');" ><img src="_clip/check_euro.gif" nat="1" alt="Jiná částka" class="no" /></a>'
               + '</td>'
               + '<td>&nbsp;</td>'
               + '<td name="cas_' + id + '" hodnota="' + id + '" >0</td>'
               + '</tr>'
             );
              // $(\'#bookdata\').append(\'<tr id="nick_\'+json_info_new[y]+\'"   ><td style="background-color:red">\'+json_info_pole[json_info_new[y]][\'jmeno\']+\' </strong>\'+json_info_pole[json_info_new[y]][\'vyhernost\']+\'%</strong></td><td>\'+json_info_pole[json_info_new[y]][\'vsazeno\'].toFixed(2)+\'</td><td>\'+json_info_pole[json_info_new[y]][\'win\'].toFixed(2)+\'</td><td>Jednoduchá</td><td><a href="javascript:jQuery.UseBet(\'+json_info_new[y]+\',1,\'+json_info_pole[json_info_new[y]][\'vsazeno\'].toFixed(2)+\');"><img src="_clip/check_yes.gif" alt="Povolit" class="no" /></a> &nbsp;&nbsp; <a href="javascript:jQuery.UseBet(\'+json_info_new[y]+\',2,\'+json_info_pole[json_info_new[y]][\'vsazeno\'].toFixed(2)+\');"><img src="_clip/check_no.gif" alt="Zamítnout" class="no" /></a> &nbsp;&nbsp; <a href="javascript:jQuery.UseBet(\'+json_info_new[y]+\',3,\'+json_info_pole[json_info_new[y]][\'vsazeno\'].toFixed(2)+\');"><img src="_clip/check_clock.gif" alt="Zastavit" class="no" /></a> &nbsp;&nbsp;  <input type="text" class="json_text" value="1" id="other_\'+json_info_new[y]+\'"/> <a href="javascript:jQuery.UseBet(\'+json_info_new[y]+\',4,\'+json_info_pole[json_info_new[y]][\'vsazeno\'].toFixed(2)+\');"><img src="_clip/check_euro.gif" nat="1" alt="Jiná částka" class="no" /></a></td><td>&nbsp;</td><td name="cas_\'+json_info_new[y]+\'" hodnota="\'+json_info_new[y]+\'" >0</td></tr>\');

             for (var bid in json_info_pole[id]['bet']) {
               for (var cid in json_info_pole[id]['bet'][bid]) {
                 var data = json_info_pole[id]['bet'][bid][cid];
                 $('#bookdata').append(
                   '<tr name="bet_' + id + '" style="display:none">'
                   +  '<td colspan="3" class="json_bet">' + data['text'] + ' ' + data['live'] + data['statusMessageHtml'] + '</td>'
                   + '<td class="json_bet" colspan="2">(' + data['sloupec']
                     + ' - ' + data['typ'] + ') <br />'
                     + data['podil_sazek']
                   + '</td>'
                   + '<td class="json_bet">' + data['kurz'] + '</td>'
                   + '<td class="json_bet">' + data['vsazeno'] + '</td>'
                   + '</tr>'
                 );
                //  $(\'#bookdata\').append(\'<tr name="bet_\'+json_info_new[y]+\'" ><td  class="json_bet"  colspan="7" >\'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'text\']+\'(\'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'sloupec\']+\' - \'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'typ\']+\') \'+json_info_pole[json_info_new[y]][\'bet\'][vl][\'kurz\']+\' </td></tr>\');
               }
             }
           }
           else if (type == 'kombi') {
             $('#bookdata').append(
               '<tr id="coupon_' + id + '" onmouseover="BetShow(' + id + ')" class="new-coupon">'
               + '<td class="' + json_info_pole[id]['winRatioCssClass'] + '"><span id="bi_' + json_info_pole[id]['id'] + '" '
                 + 'onmouseover="jQuery.BInfo(1,\'' + json_info_pole[id]['book_info'] + '\','
                 + json_info_pole[id]['id'] + ')" '
                 + 'onmouseout="jQuery.BInfo(0)">' + json_info_pole[id]['jmeno'] + '</span> '
                 + '</span> Pobočka: ' + json_info_pole[id]['branch']
                 + ' <strong>YIELD: ' + json_info_pole[id]['vyhernost'] + '%</strong>' + json_info_pole[id]['statusMessageHtml']
               + '</td>'
               + '<td>' + json_info_pole[id]['vsazeno'].toFixed(2) + '</td>'
               + '<td>' + json_info_pole[id]['win'].toFixed(2) + '</td>'
               + '<td>Kombinovaná</td>'
               + '<td><a href="javascript:jQuery.UseBet(' + id + ',1,'
                 + json_info_pole[id]['vsazeno'].toFixed(2) + ',\'' + json_info_pole[id]['hash'] + '\');">'
                 + '<img src="_clip/check_yes.gif" alt="Povolit" class="no" /></a> &nbsp;&nbsp;'
                 + '<a href="javascript:jQuery.UseBet(' + id + ',2,'
                 + json_info_pole[id]['vsazeno'].toFixed(2) + ',\'' + json_info_pole[id]['hash'] + '\');">'
                 + '<img src="_clip/check_no.gif" alt="Zamítnout" class="no" /></a> &nbsp;&nbsp;'
                 + '<a href="javascript:jQuery.UseBet(' + id + ',3,'
                 + json_info_pole[id]['vsazeno'].toFixed(2) + ',\'' + json_info_pole[id]['hash'] + '\');">'
                 + '<img src="_clip/check_clock.gif" alt="Zastavit" class="no" /></a> &nbsp;&nbsp;'
                 + '<input type="text" class="json_text" value="1" id="other_' + id + '"/>'
                 + '<a href="javascript:jQuery.UseBet(' + id + ',4,'
                 + json_info_pole[id]['vsazeno'].toFixed(2) + ',\'' + json_info_pole[id]['hash'] + '\');">'
                 + '<img src="_clip/check_euro.gif" nat="1" alt="Jiná částka" class="no" /></a>'
               + '</td>'
               + '<td>' + json_info_pole[id]['pointType'] + json_info_pole[id]['rateAdvance'] + '</td>'
               + '<td name="cas_' + id + '" hodnota="' + id + '" >0</td>'
               + '</tr>'
             );
             for (var bid in json_info_pole[id]['bet']) {
               for (var cid in json_info_pole[id]['bet'][bid]) {
                 var data = json_info_pole[id]['bet'][bid][cid];
                 $('#bookdata').append(
                   '<tr name="bet_' + id + '" style="display:none">'
                   + '<td colspan="4" class="json_bet">' + data['text'] + ' ' + data['live'] + data['statusMessageHtml'] + '</td>'
                   + '<td class="json_bet" colspan="2">('
                     + data['sloupec'] + ' - '
                     + data['typ'] + ') <br />'
                     + data['podil_sazek']
                   + '</td>'
                   + '<td class="json_bet">' + data['kurz'] + '</td>'
                   + '</tr>'
                 );
               }
             }
           }
           else if (type == 'system' || type == 'maxikombi'){
                var info_system = '';
                var banker = json_info_pole[id]['groupT'];
                banker = (0 < banker ? banker + 'x banker + ' : '');
                var n = json_info_pole[id]['groupCount'];
                for (var k = 1; k <= n; ++k) {
                  if (undefined !== json_info_pole[id]['combinations'][k]) {
                    info_system += banker + k + ' z ' + n + ': '
                      + json_info_pole[id]['combinations'][k]['winMin'].toFixed(2)
                      + ' - ' + json_info_pole[id]['combinations'][k]['winMax'].toFixed(2)
                      + '<br />';
                  }
                }

                $('#bookdata').append(
                  '<tr id="coupon_' + id+ '" onmouseover="BetShow(' + id + ')" class="new-coupon">'
                  + '<td class="' + json_info_pole[id]['winRatioCssClass'] + '"><span id="bi_' + json_info_pole[id]['id'] + '" '
                    + 'onmouseover="jQuery.BInfo(1,\'' + json_info_pole[id]['book_info'] + '\','
                    + json_info_pole[id]['id'] + ')" '
                    + 'onmouseout="jQuery.BInfo(0)">' + json_info_pole[id]['jmeno']
                    + '</span> Pobočka: ' + json_info_pole[id]['branch']
                  + ' <strong>YIELD: ' + json_info_pole[id]['vyhernost'] + '%</strong>' + json_info_pole[id]['statusMessageHtml']
                  + '</td>'
                  + '<td>' + json_info_pole[id]['vsazeno'].toFixed(2) + '</td>'
                  + '<td>' + json_info_pole[id]['win'].toFixed(2) + '</td>'
                  + '<td>' + ('system' == type ? 'Systém' : 'Maxikombinátor') + '</td>' //TODO: translate
                  + '<td><a href="javascript:jQuery.UseBet(' + id + ',1,'
                    + json_info_pole[id]['vsazeno'].toFixed(2) + ',\'' + json_info_pole[id]['hash'] + '\');">'
                    + '<img src="_clip/check_yes.gif" alt="Povolit" class="no" /></a> &nbsp;&nbsp;'
                    + '<a href="javascript:jQuery.UseBet(' + id + ',2,' + json_info_pole[id]['vsazeno'].toFixed(2) + ',\''
                    + json_info_pole[id]['hash'] + '\');"><img src="_clip/check_no.gif" alt="Zamítnout" class="no" /></a> &nbsp;&nbsp;'
                    + '<a href="javascript:jQuery.UseBet(' + id + ',3,' + json_info_pole[id]['vsazeno'].toFixed(2) + ',\''
                    + json_info_pole[id]['hash'] + '\');"><img src="_clip/check_clock.gif" alt="Zastavit" class="no" /></a> &nbsp;&nbsp;'
                    + '<input type="text" class="json_text" value="1" id="other_' + id + '"/> '
                    + '<a href="javascript:jQuery.UseBet(' + id + ',4,' + json_info_pole[id]['vsazeno'].toFixed(2) + ',\''
                    + json_info_pole[id]['hash'] + '\');"><img src="_clip/check_euro.gif" nat="1" alt="Jiná částka" class="no" /></a>'
                  + '</td>'
                  + '<td nowrap="nowrap">' + info_system + '</td>'
                  + '<td name="cas_' + id + '" hodnota="' + id + '" >0</td>'
                  + '</tr>'
                );
                for (var bid in json_info_pole[id]['bet']) {
                  for (var cid in json_info_pole[id]['bet'][bid]) {
                    var data = json_info_pole[id]['bet'][bid][cid];
                    $('#bookdata').append(
                      '<tr name="bet_' + id + '" style="display:none">'
                      + '<td colspan="3" class="json_bet">' + data['text'] + ' ' + data['live'] + data['statusMessageHtml'] + '</td>'
                      + '<td class="json_bet">' + data['group'] + '</td>'
                      + '<td class="json_bet" colspan="2">(' + data['sloupec'] + ' - '
                        + data['typ'] + ')<br />'
                        + data['podil_sazek']
                      + '</td>'
                      + '<td class="json_bet">' + data['kurz'] + ' ' + (data['banker'] == 1 ? '<br /><strong>BANKER<strong>' : '') + '</td>'
                      + '</tr>'
                    );
                  }
                }
             }
             window.focus()
         }
         for (var id in json_info_pole) {
           if (undefined === json_info_pole[id] || 0 == $('#coupon_' + id).length)
             continue;
           var html = '';
           for (var i in json_info_pole[id]['errors']) {
             html += String(json_info_pole[id]['errors'][i]) + '<br/>';
           }
           if (0 < html.length)
             $('#coupon_' + id).before('<tr id="couponError_' + id + '"><td colspan="7" class="coupon-error">' + html + '</td></tr>');
         }
         <?= empty($betradar) ? '' : '$("img[nat]").hide();' ?>
       }
     });

     jQuery.extend({
       UseBet: function(cid, type, amount, hash) {
         if (blockClick == true) {
           alert('Právě se načítájí data .. opakujte za pár sekund');
           return;
         }
         var i = 0;
         if (type==1 || type==2) {
           for(var y=0; y<json_info_coupon.length; y++) {
             if (json_info_coupon[y] == cid)
               json_info_coupon[y] = 0;
           }
           $('#coupon_' + cid).html('');
           json_info_pole[cid]['errors'] = [];
           $('#couponError_' + cid).remove();
           $('tr[name="bet_' + cid + '"]').html('');
         }
         else if (type==3)
           json_stop[cid] = 1;
         else if (type==4) {
           i = $('#other_' + cid).attr('value');
           for(var y=0; y<json_info_coupon.length; y++) {
             if (json_info_coupon[y] == cid)
               json_info_coupon[y] = 0;
           }
           $('#coupon_' + cid).html('');
           json_info_pole[cid]['errors'] = [];
           $('#couponError_' + cid).remove();
           $('tr[name="bet_' + cid + '"]').html('');
         }

         $.ajax({
           url: 'ajax.server.php',
           data: { work:3, tab:2, action:type, amount:amount, info:i, id:cid, hs:hash },
           type: 'GET',
           dataType: 'json',
           success: function(json) {}
         });
       }
     });

     //jQuery.LoadAjaxData();
     var timex = setInterval('jQuery.LoadAjaxData()', 6000);
     var timex2 = setInterval('BetStopky()', 1000);
  }); // $(document).ready()

  function BetStopky() {
    $('td[name^="cas_"]').each(function() {
      var cid = $(this).attr('hodnota');
      var t = parseInt($(this).text()) + 1;
      $(this).text(t);
      if(t > <?= $confirmTimeMore ?>) {
        $.ajax({
          url: 'ajax.server.php',
          data: { work:3, tab:2, action:0, info:'', id:cid},
          type: 'GET',
          dataType: 'json',
          success: function(json) {}
        });
        for (var y=0; y<json_info_coupon.length; y++) {
          if (json_info_coupon[y] == cid)
            json_info_coupon[y] = 0;
        }
        $("#coupon_"+cid+",#couponError_"+cid).remove();
        $("tr[name=\'bet_"+cid+"\']").remove();
        if (json_stop[cid] != 'undefined')
          delete json_stop[cid];
      }

      if (t > <?= $confirmTime ?>
        && typeof json_stop[cid] == 'undefined') {

        for(var y=0; y<json_info_coupon.length; y++) {
          if(json_info_coupon[y] == cid)
            json_info_coupon[y] = 0;
        }
        $('#coupon_'+cid).remove();
        $('tr[name="bet_' + cid + '"]').remove();
        if (json_stop[cid] != 'undefined')
          delete json_stop[cid];
      }
    });
  }

  function BetShow(cid){
    $('tr[name^="bet_' + cid + '"]').css('display', 'none');
    $('tr[name="bet_' + cid + '"]').css('display', '');
  }
  // ]]>
 </script>
<link rel="stylesheet" href="/css/blueprint/screen.css?r=<?= RELEASE_REV ?>" type="text/css"
	media="screen, projection">
<link rel="stylesheet" href="/css/blueprint/print.css?r=<?= RELEASE_REV ?>" type="text/css"
	media="print">
<!--[if lt IE 8]>
  <link rel="stylesheet" href="/css/blueprint/ie.css?r=<?= RELEASE_REV ?>" type="text/css" media="screen, projection">
<![endif]-->

<link rel="stylesheet" href="/css/adm.css?r=<?= RELEASE_REV ?>" media="screen" type="text/css" />

</head>
<body>
<div id="binfomes"></div>
<div id="load"><img src="_clip/loading.gif" alt="Nahrávám" class="no" /></div>

<div class="ticket-confirm-header last-update-time">
	<?=i18n::tr('last_update')?>: <span id="last-update-time"><?=It6_Date::nowAsTime()?></span>
</div>
<div class="ticket-confirm-header audio">
	<input type="range" min="0" max="100" value="50" step="10" id="new-ticket-notice-volume" />
</div>
<div class="ticket-confirm-header audio">
	<b><?=I18n::tr('tresholds')?>:</b>
	level 1
	<input type="text" id="new-ticket-notice-treshold-1" value="<?=$this->soundTresholds['treshold1']?>" class="treshold" />
	level 2
	<input type="text" id="new-ticket-notice-treshold-2" value="<?=$this->soundTresholds['treshold2']?>" class="treshold" />
	level 3
	<b><?=I18n::tr('volume')?>:</b>
</div>

<table id="bookdata" class="bvfd">
	<tr>
		<th>Zákazník</th>
		<th>Vsadil</th>
		<th>Výhra</th>
		<th>Druh</th>
		<th>Činnost</th>
		<th>Info</th>
		<th>Čas</th>
	</tr>

</table>

</body>
</html>
           <?php

	} // end of function

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
			echo '<script>'.$ob->jsscript.'</script>';
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
	 * Shoda
	 * @param int $uid user_id
	 * @return void
	 */
	private function shoda($uid = 0){
		global $ipd;

		$vrat = '';
		$user = array();
		$status = false;


		$sth3 = $this->dbGame->prepare("select user_id,nick,xforward,persistent_id,ts from user_tracking where user_id=? ");
		if (PEAR::isError($sth3))  {throw new ExHandler($res->getMessage().'Nepodarilo se nacist preklady menu',"admin_ex_db");}
		$res3 =& $this->dbGame->execute($sth3,array($uid));
		if (PEAR::isError($res3))  {throw new ExHandler($res->getMessage().'Nepodarilo se  nacist preklady menu',"admin_ex_db");}

		$user['per'][] = "'-'";
		$user['ip'][]  = "'-'";
		while ($row =& $res3->fetchRow()){

			if(mb_strlen(trim($row['persistent_id'])) == 0 ) continue;

			$status = true;
			if(!isset($user['user_id'])) {$user['user_id'] = $row['user_id'];$user['per'][]="'-'";}

			$user['ip'][] = "'".$row['xforward']."'";
			$user['per'][] = "'".$row['persistent_id']."'";
			$user['time'][] = $row['ts'];

		}

		$sth = $this->dbGame->prepare("select a.user_id,a.nick,b.jmeno,b.prijmeni from user_tracking a inner join uzivatel b on a.user_id=b.user_id where a.persistent_id<>'' and a.persistent_id in (".implode(',',$user['per']).") and a.user_id<>?  and a.xforward not in('',".implode(",",$ipd).") group by a.user_id");
		if (PEAR::isError($sth))  {throw new ExHandler($res->getMessage().'Nepodarilo se nacist preklady menu',"admin_ex_db");}
		$res3 =& $this->dbGame->execute($sth,array($uid));
		if (PEAR::isError($res3))  {throw new ExHandler($res3->getMessage().'Nepodarilo se  nacist user_tracking',"admin_ex_db");}

		if($status) $vrat .= '';

		// print_r($user);
		while ($row =& $res3->fetchRow()){

			$vrat .= '<p><a href="/?section=40&user_id='.$row['user_id'].'" target="_blank"> #'.$row['user_id'].' '.$row['nick'].' ('.$row['jmeno'].' '.$row['prijmeni'].') </a></p>';

		}
		if($status) $vrat .= '';

		$sth = $this->dbGame->prepare("select a.user_id,a.nick,b.jmeno,b.prijmeni from user_tracking a inner join uzivatel b on a.user_id=b.user_id where a.persistent_id<>'' and a.xforward in (".implode(',',$user['ip']).") and a.user_id<>?  and a.xforward not in('',".implode(",",$ipd).") group by a.user_id");
		if (PEAR::isError($sth))  {throw new ExHandler($res->getMessage().'Nepodarilo se nacist preklady menu',"admin_ex_db");}
		$res3 =& $this->dbGame->execute($sth,array($uid));
		if (PEAR::isError($res3))  {throw new ExHandler($res3->getMessage().'Nepodarilo se  nacist user_tracking',"admin_ex_db");}

		if($status) $vrat .= '';

		// print_r($user);
		while ($row =& $res3->fetchRow()){

			$vrat .= '<p><a href="/?section=40&user_id='.$row['user_id'].'" target="_blank"> #'.$row['user_id'].' '.$row['nick'].' ('.$row['jmeno'].' '.$row['prijmeni'].') </a></p>';

		}

		if($status) $vrat .= '';

		if(!$status) $vrat .= 'Žádné shody';

		return $vrat;

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
				$data .= '<tr><td><strong>Indv. limit:</strong></td><td>'.Help::Html($row3['max_bet']).' </td><td><strong>&nbsp;</strong></td><td>&nbsp;</td></tr>';
				$data .= '<tr><td valign="top"><strong>Shoda:</strong></td><td colspan="3">'.$this->shoda($row3['user_id']).'</td></tr>';
				$data .= '<tr><td valign="top"><strong>Poznámky:</strong></td><td colspan="3">'.Help::Html($row3['book_info']).'</td></tr>';
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
	  <link rel="stylesheet" href="/css/onlinebook.css?r=' . RELEASE_REV . '" type="text/css" media="screen" >

  <script src="' . JQUERY_URI . '"></script>
 <script src="/js/mainscript.js?r=' . RELEASE_REV . '" type="text/javascript" language="javascript"></script>
  <script>
  $(document).ready(function(){
       $("#container-1 > ul").tabs({ unselected: '.(mb_strlen($data_text)>0?'false':'true').',spinner: \'Nahrávám...\',fxFade: false ,show: function(i, s,h){$("#container-1 > ul").tabsLoad($("#container-1 > ul").tabsSelected( ) ,\'ajax.server.php?work={WORK}&id={ID}&type=\'+$("#container-1 > ul").tabsSelected( ));} });
	 // $("#container-1 > ul").tabs(0,{ unselected: false,spinner: \'Nahrávám...\' });
     '.($data==false?'':'$("#container-1 > ul").tabsClick(1);').'
  });
  </script>

</head>
<body>
  <link rel="stylesheet" href="/js/jquery.ui/themes/flora/flora.all.css?r=' . RELEASE_REV . '" type="text/css" media="screen" title="Flora (Default)">

<script type="text/javascript" src="/js/jquery.ui/ui.tabs.js?r=' . RELEASE_REV . '"></script>
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
