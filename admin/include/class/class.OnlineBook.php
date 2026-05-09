<?php
// Commented out entire file by Martin on 31.8.2012 as it is not used.
// If no problems arise, this file can be probably deleted in the future
// This class is also the only place from where the method SazkaTicket->TicketForm is called.
// If this file gets deleted the above mentioned method should probably also be removed.
// This class is also called from class.Main.php. If this file gets deleted it should be also removed from there 










// 
// /**
//  * @package    book
//  */

// /**
//  * Trida pro monitorovani informaci pro book
//  *
//  * 
//  * <code>
//  * 
//  * </code>
//  *
//  * @package    Ciselniky
//  */
 
// class OnlineBook extends Template{

// /**
//  * navratova hodnota
//  * @access private
//  * @var string
//  */
// private  $vrat = "!!! THIS SECTION IS DEPRECATED !!!";

// /**
//  * spojeni na databazi admin
//  * @access private
//  * @var DB
//  */              
// private  $db;

// /**
//  * spojeni na databazi game
//  * @access private
//  * @var DB
//  */              
// private  $dbGame;

// /**
//  * aktualni sekce
//  * @access private
//  * @var int
//  */ 
// private  $section;                

// /**
//  * Javascriptovy kod
//  * @access private
//  * @var string
//  */ 
// private  $script = ''; 

// /**
//  * zadavani multisazky
//  * @access private
//  * @var bool
//  */ 
// private  $multisazka = false; 

// /**
// * Konstruktor
// *
// *Pokud neni identifikator spojeni predan vytvori se nove spojeni
// *
// * @param int $section id aktualni sekce
// * @param PEAR::DB $dbGame objekt spojeni s databazi
// */
//   public function __construct($section=0){

//     $this->section =  $section;
     
// /*
//     $this->dbGame = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB); 
//     if (DB::isError($this->dbGame)) {
//       throw new ExHandler($this->dbGame->getMessage(),"admin_ex_db");
//     }
//     $this->dbGame->setFetchMode(DB_FETCHMODE_ASSOC);
//     //$sql = "set names 'utf8'";
//     $sql = "SET CHARACTER SET 'utf8'";
//     $res =& $this->dbGame->query($sql);
//     if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");
    
   
//    if($this->section == "b12") $this->multisazka = true;

//    $this->db = DB::connect(DATABASE ."://". MY_USER .":". MY_PASS ."@". MY_HOST ."/". MY_DB);
//    if (DB::isError($this->db)) {
//      throw new ExHandler($this->db->getMessage(),"admin_ex_db");
//    }
//    $this->db->setFetchMode(DB_FETCHMODE_ASSOC);
//    $sql = "set names 'utf8'";
//    $res =& $this->db->query($sql);
//    if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");
// */

// ///////////////////

// 	  	//init stuff
// 	$dbGameZend = It6_Controller_Plugin_SetController::connectDbWeb();
// 	It6_Controller_Plugin_SetController::connectDbAdmin();

// 	$translate = new It6_Translate_Admin(1, $dbGameZend);
// 	Zend_Registry::set('translate', $translate);

// 	//TODO: use constant from config instead of class constants It6_WS::* here
// 	$autoloader = Zend_Loader_Autoloader::getInstance();

// 	Zend_Registry::set('autoloader', $autoloader);
// 	$autoloader->registerNamespace('It6_');	
// 	$autoloader->registerNamespace('Models_');
// 	$autoloader->registerNamespace('DecoratorForms_');
// 	$autoloader->pushAutoloader(new It6_AutoloaderAdmin());
	
// 	$client = new Zend_XmlRpc_Client(WEB_SERVICE_URL);
// 	Zend_Registry::set('ws', new It6_WS(WS_WRAPPER, $client));
// 	//init stuff end
  
//     $dbAdmin = It6_Controller_Plugin_SetController::connectDbAdmin();

// 	session_set_save_handler (
// 		array("It6_Session_Admin", "open"),
// 		array("It6_Session_Admin", "close"),
// 		array("It6_Session_Admin", "read"),
// 		array("It6_Session_Admin", "write"),
// 		array("It6_Session_Admin", "destroy"),
// 		array("It6_Session_Admin", "gc")
// 	);
// /*
// 	It6_Session_Admin::start($dbAdmin);
// */

// 	Zend_Registry::set('Zend_Locale', new Zend_Locale('cs_CZ'));
// 	$acl = It6_Acl_Factory::newAcl(array(
// 		'adminDb' => $dbAdmin, 'adminId' => It6_Session_Admin::getUserData('id')
// 	));
// 	Zend_Registry::set('acl', $acl);

//    $this->db = DbUtil::connectAdminDb();
//    $this->dbGame = DbUtil::connectWebDb();
// 	$acl = Zend_Registry::get('acl');

//    //$this->findSection(); // zjisteni aktualni sekce

//    #Prihlaseni a prochazeni stranek bookmakerem#
// 	$this->superbookmaker = $acl->userHasRole(It6_Acl_Admin::ROLE_SUPERBOOKMAKER);
// 	$this->bookmaker = $this->superbookmaker || $acl->userHasRole(It6_Acl_Admin::ROLE_BOOKMAKER);
// 	$this->superadmin = $acl->userHasRole(It6_Acl_Admin::ROLE_SUPERADMIN);
// 	$this->admin = $this->superadmin || $acl->userHasRole(It6_Acl_Admin::ROLE_ADMIN);

//    //$ob = new DepositBonus(20,100);
//    //$ob->InitBonus();

//    //if(!SesClass::open($this->db)) throw new ExHandler('Nepodarilo se inicializovat Session',"admin_ex_db"); //IT6: presunuto do bootstrapu

//    //SesClass::setUserStatus(2);$_SESSION['user_id']=3;$_SESSION['superadmin']=1;$_SESSION['nick']='kusa';

//    //if(SesClass::getUserStatus() == 3) $_SESSION['starttime'] = time();   //nastavime do sesison startovaci cas v adminu
// 	if(It6_Session_Admin::STATUS_JUST_AUTHENTICATED == It6_Session_Admin::getStatus())
// 		$_SESSION['starttime'] = time();   //nastavime do session startovaci cas v adminu

//    //if(SesClass::getUserStatus() == 2 || SesClass::getUserStatus() == 3){ //pouze pokud je prihlaseny
// 	if (It6_Session_Admin::isAuthenticated()) {
// 		$this->menu = new Menu($this->db, $this->section); //vytvoreni objektu menu a zjisteni prav k jednotlivym prvkum menu
// 	}

//    //if(SesClass::getUserStatus() == 1) $this->error = "Session has expired";  //vyprsela session
//    //if(SesClass::getUserStatus() == 4) {$this->error = $GLOBALS['ses_zprava'];unset($GLOBALS['ses_zprava']);} //vychozeny uzivatel
//    //if(SesClass::getUserStatus() == 5) {$this->error = "Účet byl zablokován";} //zakazany
// 	switch (It6_Session_Admin::getStatus()) {
// 	case It6_Session_Admin::STATUS_TIMEOUT:
// 		$this->error = "Session has expired";
// 		break;
// 	case It6_Session_Admin::STATUS_ABORTED:
// 		$this->error = It6_Session_Admin::getMessage();
// 		It6_Session_Admin::setMessage(null);
// 		break;
// 	case It6_Session_Admin::STATUS_BLOCKED:
// 		$this->error = "Účet byl zablokován";
// 		break;
// 	default:
// 		break;
// 	}

// 	if(isset($_SESSION["recent_sections"])) {
// 		if ($i = array_search($this->section, $_SESSION["recent_sections"]))
// 			unset($_SESSION["recent_sections"][$i]);
// 	}
// 	else
// 		$_SESSION["recent_sections"] = array();
// 	if(isset($this->section) && $this->section)
// 		$_SESSION["recent_sections"][] = $this->section;
// 	if (count($_SESSION["recent_sections"]) > 7)
// 		array_shift($_SESSION["recent_sections"]);

// 	// pro zpetnou kompatibilitu starych z admina
// 	$_SESSION['bookmaker'] = ($this->bookmaker ? It6_Session_Admin::getUserData('id') : 0);
// 	$_SESSION['superbookmaker'] = $this->superbookmaker;
// 	$_SESSION['admin'] = ($this->admin ? It6_Session_Admin::getUserData('id') : 0);
// 	$_SESSION['superadmin'] = $this->superadmin;
   
//   }

// 	private static function getCacheFileName($id) {
// 		return TEMP_DIRECTORY.'onlineBookCache_' . intval($id) . '.sbr';
// 	}

// /**
//  * Writes data into cache
//  * @param $cacheId id for cacheing, if null then $_GET['file_id'] will be used
//  * @param $data data to write
//  * @param $dieOnFailure if TRUE, failure will cause call of die()
//  * @return TRUE on success or FALSE on failure if not $dieOnFailure was TRUE
//  */
//   private static function writeCache($cacheId, $data, $dieOnFailure) {
// 	$fn = self::getCacheFileName(isset($cacheId) ? $cacheId : $_GET['file_id']);
//   	if (false === file_put_contents($fn, $data)) {
//   		if ($dieOnFailure)
//   			die("File not opened for writing: $fn");
// 		else
// 			return false;
//   	}
//   	else
//   		return true;
//   }

// /**
//  * Reads data from cache
//  * @param $cacheId id for cacheing, if null then $_GET['file_id'] will be used
//  * @param $dieOnFailure if TRUE, failure will cause call of die()
//  * @return data read on success or FALSE on failure if not $dieOnFailure was TRUE
//  */
//   private static function readCache($cacheId, $dieOnFailure) {
// 	$fn = self::getCacheFileName(isset($cacheId) ? $cacheId : $_GET['file_id']);
// 	if (!file_exists($fn))
// 		return '';
//   	$data = file_get_contents($fn);
//   	if (false === $data) {
//   		if ($dieOnFailure)
//   			die("File not opened for reading: $fn");
// 		else
// 			return false;
//   	}
//   	else
//   		return $data;
//   }

// /**
//  * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
//  * @return void
//  */
// public function runAction(){
// 	if(isset($_POST['ok'])){
// 		 $url = '';
		 
// 		 if(isset($_POST['on1'])) $url.= 'on1=1&';
		 
// 		 if(isset($_POST['on1']) && isset($_POST['on1_1'])) $url.= 'on1_1='.$_POST['on1_1'].'&';
		 
// 		 if(isset($_POST['on2'])) $url.= 'on2=1&';
		 
// 		 if(isset($_POST['on2_signal'])) {
// 			$url.= 'on2_signal='.intval($_POST['on2_signal']).'&file_id='.$_SESSION['bookmaker'].'&';
// 			unlink(self::getCacheFileName($_SESSION['bookmaker']));
// 		}
		
// 		if(isset($_POST['on2_min']) && intval($_POST['on2_min']) != 0) $url.= 'on2_min='.intval($_POST['on2_min']).'&';

// 		if(isset($_POST['on2_time']) && intval($_POST['on2_time']) != 0) $url.= 'on2_time='.intval($_POST['on2_time']).'&';

// 		if(isset($_POST['on3'])) $url.= 'on3=1&';

// 		if(isset($_POST['on3_pr']) && is_numeric($_POST['on3_pr'])) $url.= 'on3_pr='.floatval($_POST['on3_pr']).'&';

// 		if(isset($_POST['on4'])) $url.= 'on4=1&';

// 		if(isset($_POST['on4_signal'])) {
// 			$url.= 'on4_signal='.intval($_POST['on4_signal']).'&file_id='.$_SESSION['bookmaker'].'&';
// 			unlink(self::getCacheFileName($_SESSION['bookmaker']));
// 		}

// 		if(isset($_POST['on4_min']) && intval($_POST['on4_min']) != 0) $url.= 'on4_min='.intval($_POST['on4_min']).'&';

// 		if(isset($_POST['on4_time']) && intval($_POST['on4_time']) != 0) $url.= 'on4_time='.intval($_POST['on4_time']).'&';
 
// 		$this->vrat .= '<script language="Javascript" type="text/javascript">
// 						   window.onload = function() {window.open(\'online.php?'.$url.'\',\'\',\'width=\'+screen.availWidth+\',height=\'+screen.availWidth+\',resizable=yes,scrollbars=yes\');location.href=\'/?section=149\';}
// 						 </script>';
// 	}

// 	$this->vrat .= '<form method="post">
// 						<table class="table-detail">
// 							<tr><td><strong>Nejvíc oblíbené sázky vyjádřeno v penězích:</strong></td><td><input class="no"  name="on1" type="checkbox" value="" /></td><td>Kurz nad</td><td><select name="on1_1"><option value="1">1</option><option value="1.5">1.5</option><option value="2">2</option></select></td></tr>
// 							<!--<tr><td><strong>Tikety:</strong></td><td><input type="checkbox" class="no" name="on2" value="" /></td><td> Min. EUR :<input type="text" style="width:80px;" name="on2_min" /></td><td> Signál částka >:<input type="text" style="width:80px;" name="on2_signal" /></td><td> Max. stáří v minutách:<input type="text" style="width:80px;" name="on2_time" /></td></tr>
// 							<tr><td><strong>Tikety SCREEN:</strong></td><td><input type="checkbox" class="no" name="on4" value="" /></td><td> Min. EUR :<input type="text" style="width:80px;" name="on4_min" /></td><td> Signál částka >:<input type="text" style="width:80px;" name="on4_signal" /></td><td> Max. stáří v minutách:<input type="text" style="width:80px;" name="on4_time" /></td></tr>-->
// 							<tr><td><strong>Risk limit:</strong></td><td><input type="checkbox" class="no" name="on3" value="" /></td><td colspan="2"> % z limitu:<input type="text" style="width:80px;" name="on3_pr" /></td></tr>
// 							<tr><td colspan="4"><input type="submit" name="ok" value="Potvrdit výběr" /></td></tr>
// 						</table>
// 					</form>';

// 	$this->dbGame->disconnect();
// }
  

//    /**
//  * Vymazani vsech informaci
//  * @return void
//  */
//   public function ShowInfo(){
// /*
//   	 session_set_save_handler (
//     array("SessionBookmaker", "open"),
//     array("SessionBookmaker", "close"),
//     array("SessionBookmaker", "read"),
//     array("SessionBookmaker", "write"),
//     array("SessionBookmaker", "destroy"),
//     array("SessionBookmaker", "gc"));
// */
//   	//if(!SesClass::open($this->db)) throw new ExHandler('Nepodarilo se inicializovat Session',"admin_ex_db");
// 	if (!It6_Session_Admin::start($this->db)) throw new ExHandler('Nepodarilo se inicializovat Session',"admin_ex_db");
   
//     //if(SesClass::getUserStatus() != 2) {echo "SESSION ERROR"; return;}
// 	if (!It6_Session_Admin::isAuthenticated()) {
// 		echo "SESSION ERROR";
// 		return;
// 	}

// 	$vrat = '<div id="zvuk"></div><div style="" id="tinfo"></div>';

//    $vrat .= '<input type="button" onclick="if(stop==true){this.value=\'START\';stop=false;}else{this.value=\'STOP\';stop=true;}"  style="position:absolute;width:100px;top:0px;left:90%;" value="STOP"/>';
//    $vrat .= '
//      <script>
// 	 /*win_init();*/
//      /*winpage=new lib_doc_size();*/
//      var zIndexVar = 3;
//   function ReQSystem(index,n1,maxn1,betAr,bankerRate,rate,systemBetSum){

// 	 var sum = 0;
// 	 var ratex = 1;
	 
// 	 for(var x=index;x<=betAr.length;x++){
	   
// 	   if(parseInt(maxn1) == parseInt(n1)){
// 	    if(maxWin == 0)  maxWin =  parseFloat(rate*bankerRate*systemBetSum);
// 	    else if(parseFloat(rate*bankerRate*systemBetSum) > maxWin) maxWin = parseFloat(rate*bankerRate*systemBetSum);
	   
// 	    if(minWin == 0)  minWin =  parseFloat(rate*bankerRate*systemBetSum);
// 	    else if(parseFloat(rate*bankerRate*systemBetSum) < minWin) minWin = parseFloat(rate*bankerRate*systemBetSum);
	    
// 	    return parseFloat(rate*bankerRate*systemBetSum);
// 	   }else ratex = parseFloat(betAr[x]*rate);
	   
// 	   sum = parseFloat(sum) + parseFloat(this.ReQSystem((x+1),n1,(maxn1+1),betAr,bankerRate,ratex,systemBetSum)); 
	   
// 	 }
	 
// 	 return parseFloat(sum);
	 
//   }

//       ';
     
//     if(isset($_GET['on1'])) $vrat .= ' document.write(\'<div onclick="zIndexVar++;this.style.zIndex = zIndexVar;" style="z-index:1;background-color:#ffffcc;position:absolute;top:24px;border:4px solid orange;left:0px;width:620px;height:400px;overflow:auto;">'.Help::Script2($this->TopBet()).'</div>\');';
//     //if(isset($_GET['on2'])) $vrat .= ' addWindow("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tikety","'.$this->Ticket().'",10,(document.body.clientHeight-650),(document.body.clientWidth+200),650);';
// 	if(isset($_GET['on2'])) $vrat .= 'document.write(\'<div onclick="zIndexVar++;this.style.zIndex = zIndexVar;" style="z-index:3;position:absolute;top:157px;border:4px solid red;left:10px;width:\'+parseInt(window.screen.availWidth-50)+\'px;height:630px;">'.Help::Script2($this->Ticket()).'</div>\');';
//     if(isset($_GET['on3'])) $vrat .= 'document.write(\'<div onclick="zIndexVar++;this.style.zIndex = zIndexVar;" style="z-index:2;background-color:#ffffcc;position:absolute;top:24px;border:4px solid blue;left:640px;width:620px;height:400px;overflow:auto;">'.Help::Script2($this->RiskLimit()).'</div>\');;';
// 	if(isset($_GET['on4'])) $vrat .= 'document.write(\'<div onclick="zIndexVar++;this.style.zIndex = zIndexVar;" style="z-index:3;position:absolute;top:157px;border:4px solid red;left:10px;width:\'+parseInt(window.screen.availWidth-50)+\'px;height:630px;">'.Help::Script2($this->TicketScreen()).'</div>\');';
//    $vrat .= '
//       //create_window(wins-1,400,400,300,300);

// 	  var stop = true;
// 	  function Refresh(){
	    
// 		if(stop == true){document.location.href=document.location.href;}
	  
// 	  }
	  
//       var minWin = 0;  
//       var maxWin = 0;
// 	  function ShotTInfo(t_id){
	    
         
// 	 	var ht = "<table cellpadding=\"4\" class=\"t_show_table\">";
	
// 		for(vl in ticket[t_id]){
          
//           if(vl == "system" && parseInt(ticket[t_id][\'system\']) != 0) var system_x_win = ReQSystem(0,parseInt(ticket[t_id][\'system\']),0,ticket[t_id][\'sazky\'],parseFloat(ticket[t_id][\'banker_rate\']),1,ticket[t_id][\'castka_rad\']).toFixed(2);

//           if(vl == "vyplacen" && parseInt(ticket[t_id][\'vyplacen\']) == 1) ht += "<tr><td colspan=\"5\">VYPLACEN</td></tr>";
//           if(vl == "saldo") ht += "<tr><td colspan=\"5\" style=\"background:black\">Finance saldo: <strong>"+ticket[t_id][\'saldo\']+"</strong> EUR</td></tr>";
//           if(vl == "zalozen") ht += "<tr><td colspan=\"5\">Založen: "+ticket[t_id][\'zalozen\']+"</td></tr>";
//           if(vl == "div") ht += "<tr><td colspan=\"5\">"+ticket[t_id][\'div\']+"</td></tr>";
//           if(vl == "system" && parseInt(ticket[t_id][\'system\']) != 0) ht += "<tr><td colspan=\"5\" style=\"background:white;color:black;padding:5px;border 1px solid black\"><strong>"+(ticket[t_id][\'banker_num\']>0?ticket[t_id][\'banker_num\'].toString()+" Banker + ":"")+" System "+ticket[t_id][\'system\']+"/"+ticket[t_id][\'sazky\'].length+"</strong> "+minWin.toFixed(2)+" EUR - "+maxWin.toFixed(2)+" EUR</td></tr>";
		  
//           if(vl != "vyplacen" && vl != "system" && vl != "div" && vl != "saldo" && vl != "sazky" && vl != "zalozen" && vl != "vyhra" && vl != "banker_num" && vl != "banker_rate" && vl != "castka_rad")ht += "<tr><td><a href=\"javascript:AjaxInfo(1,"+ticket[t_id][vl][\'sazka\']+");void(0);\" style=\"color:white\">#"+ticket[t_id][vl][\'sazka\']+"</a> </td><td nowrap=\"nowrap\"><strong>"+ticket[t_id][vl][\'name\']+"</strong></td><td nowrap=\"nowrap\" style=\"border-right:1px solid black\"> ("+ticket[t_id][vl][\'pnazev\']+" - "+ticket[t_id][vl][\'tnazev\']+")</td><td> "+ticket[t_id][vl][\'kurz\'].toFixed(2)+" "+ticket[t_id][vl][\'live\']+"</td><td><strong>"+ticket[t_id][vl][\'vysl\']+" </strong> "+ticket[t_id][vl][\'podtyp\']+"</td></tr>";
          
//           if(vl == "sazky" && parseInt(ticket[t_id][\'system\']) != 0) {ht += "<tr><td colspan=\"2\">Výhra:</td><td colspan=\"2\">"+ system_x_win+" EUR</td></tr>";}
//           if(vl == "vyhra" && parseInt(ticket[t_id][\'system\']) == 0) ht += "<tr><td colspan=\"2\">Výhra:</td><td colspan=\"2\">"+parseFloat(ticket[t_id][\'vyhra\'])+" EUR</td></tr>";
          
// 		}
        
// 		ht += "</table>";

// 		document.getElementById(\'tinfo\').innerHTML = ht ;
// 		document.getElementById(\'tinfo\').style.left = 50+"px";
// 		document.getElementById(\'tinfo\').style.top = 0+"px";
// 		document.getElementById(\'tinfo\').style.visibility = \'visible\';
		
// 		maxWin = 0;minWin = 0;
// 	  }
	  
// 	  function HideTInfo(){
	    
		
// 		document.getElementById(\'tinfo\').style.visibility = \'hidden\';
		
// 	  }
	  
// 	  var  ticket_c = new Array();
	  
// 	  '.$this->script.'
	  
// 	  function Blik(){
		
// 		  for(var x=0;x<ticket_c.length;x++){

// 		    if(document.getElementById(ticket_c[x]).style.backgroundColor.toLowerCase() != \'#cc0000\'){

// 			  document.getElementById(ticket_c[x]).style.backgroundColor = \'#CC0000\';
// 			  document.getElementById(ticket_c[x]).style.borderLeft = \'1px solid black\';
// 			  document.getElementById(ticket_c[x]).style.borderRight = \'1px solid black\';
// 			  document.getElementById(ticket_c[x]).style.borderTop = \'1px solid black\';

			  
// 			}else{
			  
// 			  document.getElementById(ticket_c[x]).style.backgroundColor = \'#ffff00\';
// 			  document.getElementById(ticket_c[x]).style.borderLeft = \'1px solid #CC0000\';
// 			  document.getElementById(ticket_c[x]).style.borderRight = \'1px solid #CC0000\';
// 			  document.getElementById(ticket_c[x]).style.borderTop = \'1px solid #CC0000\';

			  
// 			}
		   
// 		  }
		   
// 	  }
	  
		  
		 
// 	  window.onload = function(){
	    
// 		setInterval("Refresh()",15000);
	  
// 	   if(ticket_c.length > 0){
	      
//           document.getElementById(\'zvuk\').autostart = true;
// 		  document.getElementById(\'zvuk\').innerHTML = \'<embed src="_clip/online.wav" autostart="true"  hidden />\';
		 
// 		  setInterval("Blik()",1000);
		  
// 	   }
	   
// 	  }
	  
	  
//       </script>
//    ';
       
//    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
// <html xmlns="http://www.w3.org/1999/xhtml" lang="cs" xml:lang="cs"> 
// <head>
//  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
//  <meta http-equiv="Content-Language" content="cs" />
//  <meta name="keywords" content="on-line casino, games, offshore, secure, legal, chance, licensed, regulated, on-line," />
//  <meta name="description" content="Play on-line games for real money or for free. " />
//  <meta name="abstract" content="" />
//  <meta name="Author" content="" />
//  	<title> ON - LINE INFORMACE </title>
//  <link rel="stylesheet" href="/css/windown.css?r=' . RELEASE_REV . '" media="screen" type="text/css" />
//  <!--<script src="/js/windown.js?r=' . RELEASE_REV . '" type="text/javascript" language="javascript"></script>-->
// <script src="' . JQUERY_URI . '"></script>
// <script src="/js/jquery.ui/ui.dialog.js?r=' . RELEASE_REV . '"></script>
// <script type="text/javascript" src="/js/jquery.ui/jquery.dimensions.js?r=' . RELEASE_REV . '"></script>
// <script type="text/javascript" src="/js/jquery.ui/ui.resizable.js?r=' . RELEASE_REV . '"></script>
// <script type="text/javascript" src="/js/jquery.ui/ui.mouse.js?r=' . RELEASE_REV . '"></script>
// <script type="text/javascript" src="/js/jquery.ui/ui.draggable.js?r=' . RELEASE_REV . '"></script>
// <script type="text/javascript" src="/js/jquery.ui/ui.tabs.js?r=' . RELEASE_REV . '"></script>
// <script language="Javascript">
// var dialogBetClick = false;
// var dialogUserClick = false;
// function AjaxInfo(type,id){
  

//   if(type==1){
//      if(dialogBetClick)$("#info_bet").dialogClose();
//      $("#info_bet").empty( ); 
//      $("#info_bet").attr(\'title\',\'Sázka informace #\'+id);
//      $("#info_bet").append("<iframe src=\"ajax.server.php?work=1&id="+id+"\"  width=\"100%\" height=\"570px\"></iframe>");
//      $("#info_bet").css(\'display\',\'block\');
//      $("#info_bet").dialog({height:650,width:850,position:\'center\'})
//      dialogBetClick=true;
//   }
//   else if (type==2){

//      if(dialogUserClick)$("#info_user").dialogClose();
//      $("#info_user").empty( ); 
//      $("#info_user").attr(\'title\',\'Uživatel informace #\'+id);
//      $("#info_user").append("<iframe src=\"ajax.server.php?work=2&id="+id+"\"  width=\"100%\" height=\"570px\"></iframe>");
//      $("#info_user").css(\'display\',\'block\');
//      $("#info_user").dialog({height:650,width:850,position:\'center\'})
//      dialogUserClick =true;
//   }


// }
// </script>
// <link rel="stylesheet" href="/js/jquery.ui/themes/flora/flora.all.css?r=' . RELEASE_REV . '" type="text/css" media="screen" title="Flora (Default)">
// </head>
// <body>
// <div id="info_user" class="flora" style="display:none;z-index:30"  title="Uživatel informace">



// </div>

// <div id="info_bet" class="flora" style="display:none;z-index:30"  title="Sázka informace">



// </div>

// ';
//    echo $vrat;
//    echo '</body></html>';
   
//    //SesClass::close($this->db);        //uzavreni session nelze uz pridavat dalsi session
// 	It6_Session_Admin::end($this->db);
   
//    $this->db->disconnect();  //uzavreni spojeni s db

//    $this->dbGame->disconnect();
   
//   }
 
//   /**
//  * Risk limit
//  * @return void
//  */
//   private function RiskLimit(){
    
//      if(isset($_GET['on3_pr'])) $pr = floatval($_GET['on3_pr']);else $pr = 75;
     
//     $vrat = "";
     
//     $sql = "select sazka_id,text,risk_limit,risk_limit_balance from sazky where platna_od<now() and platna_do>now() and status=0 and ((risk_limit_balance/risk_limit)*100)>".$pr;
//     $res =& $this->dbGame->query($sql); 
//     if(DB::isError($res)) { throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber sazky  ticket_pohled',"admin_ex_db");} 
        
//     $vrat .= '<table width="100%" class="tab1"><tr><th>Sázka ID</th><th>Text</th><th>Risk limit CZK</th><th>Vsazeno CZK</th></tr>';
    
//     while ($row =& $res->fetchRow()){ 
      
//       $vrat .= '<tr><td align="center">#'.$row['sazka_id'].'</td><td align="center">'.Help::Html(Help::TranslateString($row['text'],CZ_LANG_ID,$this->dbGame)).'</td><td align="center">'.$row['risk_limit'].'</td><td align="center">'.$row['risk_limit_balance'].'</td></tr>';  
    
//     }
     
//      $vrat .= '</table>';
     
//      $vrat = mb_ereg_replace('"','\"',$vrat);
     
//     return $vrat;
 
//   }
  
//     /**
//  * Top sazky
//  * @return void
//  */
//   private function TicketScreen(){
//   global $systemAr;
  
//    $vrat = '';
//    $min = false;
   
//    if(isset($_GET['on4_min']) && is_numeric($_GET['on4_min']) && intval($_GET['on4_min']) != 0) $min = intval($_GET['on4_min']);
//    if(isset($_GET['on4_signal']) &&  is_numeric($_GET['on4_signal']) && $_GET['on4_signal'] != 0) $signal = intval($_GET['on4_signal']);else $signal = 1000;
//    if(isset($_GET['on4_time']) &&  is_numeric($_GET['on4_time']) && $_GET['on4_time'] != 0) $minutes = intval($_GET['on4_time']);else $minutes = 30;
   
//    $mena = array();
//    $sql = "select e.kurz,e.mena_id from kurzmena e where e.platny_od<=now() and e.platny_do>=now() ";
//    $res =& $this->dbGame->query($sql);
//    while ($row =& $res->fetchRow()){
//     	$mena[$row['mena_id']] = $row['kurz'];
//    }
     
//    $podtyp = array();
//     $sql = "select podtyp_id,interni_nazev from podtyp";
//     $res =& $this->dbGame->query($sql);
//     if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber bet_settings',"admin_ex_db");
    
//     while($row =& $res->fetchRow()){
//     	$podtyp[$row['podtyp_id']] = $row['interni_nazev'];
//     }
   
//    $prev_ticket = false;
  
//    $klic = 0;
//    $data = self::readCache(null, true);
//    $prev_ticket = ($data!=""?unserialize($data):false);
   
//    if(isset($_POST['back'])){
     
//      array_splice ($prev_ticket, -8);
	 
//    }
   
//    #Vyber sloupcu#
//    $sloupec_ar = array();
//    $preklad = new Preklady();
//    $sql = "select nazev,sloupec_id from podtyp_sloupce";
//    $res =& $this->dbGame->query($sql);
//    if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky typ',"admin_ex_db");
   
//     while ($row =& $res->fetchRow()){
    
// 	 $r = $preklad->FindPreklad($row['nazev'],1); 
//      if($r[1] == 'Translation not found') $r[1] = $row['nazev'];
// 	 $sloupec_ar[$row['sloupec_id']] = $r[1];
	
//     }
    

//    //echo "<pre>";print_r($prev_ticket);
	
// 	$sql = "select a.ticket_id from ticket_pohled a inner join uzivatel b on a.user_id=b.user_id inner join kurzmena g on b.mena_id=g.mena_id where g.platny_od <= now( ) AND g.platny_do > now( ) ". ($min != false?"and (a.castka/g.kurz)>".$min:"") ." and DATE_ADD('".It6_Date::dbNow()."',INTERVAL -".$minutes." MINUTE)<a.zalozen ".($prev_ticket != false?"and a.ticket_id not in(".implode(",",$prev_ticket).")":"")." group by a.ticket_id  limit 0,8";
//     $res =& $this->dbGame->query($sql);
//     if(DB::isError($res)) { throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber z pohledu  ticket_pohled',"admin_ex_db");}
    
//     if($res->numrows()<8 && is_array($prev_ticket))  array_splice ($prev_ticket, -((8-intval($res->numrows()))));
    
//     $sql = "select a.ticket_id,b.mena_id,b.zustatek,a.user_id,a.castka,a.zalozen,b.jmeno,b.prijmeni,a.system,a.vyplacen,b.nick,b.kurz,(a.castka/b.kurz) AS eur_castka,POW(2.718281828459,sum(ln(a.kurz))) AS kurz_celkem from ticket_pohled a inner join view_admin_ticket b on a.user_id=b.user_id where 1 ". ($min != false?"and (a.castka/b.kurz)>".$min:"") ." and DATE_ADD('".It6_Date::dbNow()."',INTERVAL -".$minutes." MINUTE)<a.zalozen ".($prev_ticket != false?"and a.ticket_id not in(".implode(",",$prev_ticket).")":"")." group by a.ticket_id order by a.ticket_id limit 0,8";
//     $res =& $this->dbGame->query($sql);
//     if(DB::isError($res)) { throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber z pohledu  ticket_pohled',"admin_ex_db");}
    
// 	$tik = "";$y = 0;
// 	$u_info = new UserWin();
	
// 	$this->script .= 'var ticket = new Object();';
	
// 	while ($row =& $res->fetchRow()){
	
// 	$vklad = $vyber = $saldo = 0;/*
//     $sql = "select a.* from finacni_transakce a  where a.user_id=".$row['user_id'];
// 	$res4 =& $this->dbGame->query($sql);
// 	if(DB::isError($res4)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky uzivatel',"admin_ex_db");
// 	while($row4 =& $res4->fetchRow()){
		
// 		if($row4['typ_platby'] == 1)     $vklad += $row4['castka'];
// 		else if($row4['typ_platby'] == 2) $vyber += $row4['castka'];
		
// 	}*/
	
// 	$saldo = round( ( ($vklad - $vyber - $row['zustatek']) /$mena[$row['mena_id']]) ,2);
	
// 	$this->script .= 'ticket['.$row['ticket_id'].'] = new Object();';
	
// 	   if(!isset($_POST['back'])) $prev_ticket[] = $row['ticket_id'];
	 
// 	 $y++;
	 
// 	 if($row['eur_castka'] <= 10){$height = $this->TicketHeight(0,10,$row['eur_castka'],0);}
// 	 else if($row['eur_castka'] <= 25){$height = $this->TicketHeight(10,25,$row['eur_castka'],1);}
// 	 else if($row['eur_castka'] <= 50){$height = $this->TicketHeight(25,50,$row['eur_castka'],2);}
// 	 else if($row['eur_castka'] <= 100){$height = $this->TicketHeight(50,100,$row['eur_castka'],3);}
// 	 else if($row['eur_castka'] <= 250){$height = $this->TicketHeight(100,250,$row['eur_castka'],4);}
// 	 else if($row['eur_castka'] <= 500){$height = $this->TicketHeight(250,500,$row['eur_castka'],5);}
// 	 else if($row['eur_castka'] <= 1000){$height = $this->TicketHeight(500,1000,$row['eur_castka'],6);}
// 	 else if($row['eur_castka'] <= 2000){$height = $this->TicketHeight(1000,2000,$row['eur_castka'],7);}
// 	 else if($row['eur_castka'] <= 3000){$height = $this->TicketHeight(2000,3000,$row['eur_castka'],8);}
// 	 else if($row['eur_castka'] <= 4000){$height = $this->TicketHeight(3000,4000,$row['eur_castka'],9);}
// 	 else $height =  600;

// 		 $sql = "select a.sazka_id,a.live,a.ticket_id,a.udalost_id,a.text,a.typ_id,a.banker,a.kurz,a.vysledek,b.nazev AS tnazev,c.nazev AS pnazev from ticket_pohled a inner join typ b on a.typ_id=b.typ_id inner join podtyp_sloupce c on a.sloupec_id=c.sloupec_id where a.ticket_id=".$row['ticket_id']." order by a.kurz desc";
// 	 $res2 =& $this->dbGame->query($sql);
//      if(DB::isError($res2)) { throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber z pohledu  ticket_pohled',"admin_ex_db");}
	 
     
// 	 $this->script .= ' ticket['.$row['ticket_id'].'][\'vyplacen\'] = '.Help::Script($row['vyplacen']).';
//                         ticket['.$row['ticket_id'].'][\'system\'] = parseInt('.Help::Script($row['system']).');
//                         ticket['.$row['ticket_id'].'][\'vyhra\'] = parseFloat('.round(($row['kurz_celkem']*$row['eur_castka']),2).');
//                         ticket['.$row['ticket_id'].'][\'banker_num\'] = 0;
//                         ticket['.$row['ticket_id'].'][\'banker_rate\'] = 1;
//                         ticket['.$row['ticket_id'].'][\'div\'] = \''.$u_info->getDiv($row['user_id']).'\';
//                         ticket['.$row['ticket_id'].'][\'saldo\'] = '.$saldo.';
//                         ticket['.$row['ticket_id'].'][\'zalozen\'] = "'.It6_Date::fromDb($row['zalozen']).'";
//                         ticket['.$row['ticket_id'].'][\'sazky\'] = new Array();';
	 
// 	 $cr = 0;
// 	 while ($row2 =& $res2->fetchRow()){
	 	
//       $sql = "select home_team,away_team from live where sazka_id=".intval($row2['sazka_id']);
//       $res42 =& $this->dbGame->query($sql);
//       if(DB::isError($res42)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sportu a udalosti',"admin_ex_db");
    
//       $livet_text_add = '';
//       if ($row42 =& $res42->fetchRow()){
//     	$livet_text_add = ' '.$row42['home_team'].' - '.$row42['away_team'].' ';
//       }
    
	 	
// 	  $vv = '';
// 	  $row2['vysledek'] = explode(";",$row2['vysledek']);
// 	  foreach($row2['vysledek'] as $h){if(isset($sloupec_ar[$h])) $vv .= $sloupec_ar[$h].',';}$vv = substr($vv,0,-1);
	  
// 	  if($row['system'] != 0 && $row2['banker'] != 1)$cr++; 
// 	  $this->script .= '
// 	                    ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'] = new Object();
// 	                    ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'][\'name\'] = "'.$livet_text_add.Help::Script(Help::TranslateString($row2['text'],CZ_LANG_ID,$this->dbGame)).'";
// 						ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'][\'sazka\'] = '.Help::Script($row2['sazka_id']).';
// 						ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'][\'kurz\'] = '.Help::Script($row2['kurz']).';
//                         ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'][\'tnazev\'] = "'.Help::Script($row2['tnazev']).'";
//                         ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'][\'pnazev\'] = "'.Help::Script($row2['pnazev']).'";
//                         ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'][\'banker\'] = '.Help::Script($row2['banker']).';
//                         ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'][\'vysl\'] = "'.Help::Script($vv).'";
//                         ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'][\'live\'] = "'.($row2['live']==1?"[LIVE]":"").'";
//                         ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'][\'podtyp\'] = "";               
// 						'.($row['system'] != 0 && $row2['banker'] != 1?'ticket['.$row2['ticket_id'].'][\'sazky\'][ticket['.$row2['ticket_id'].'][\'sazky\'].length] = '.$row2['kurz'].';':'').' 
//                        '.($row2['banker']==1?'ticket['.$row2['ticket_id'].'][\'banker_num\'] = parseInt(ticket['.$row2['ticket_id'].'][\'banker_num\']) + 1;ticket['.$row2['ticket_id'].'][\'banker_rate\'] = parseInt(ticket['.$row2['ticket_id'].'][\'banker_rate\'])*'.$row2['kurz'].';':'').'
//                               ';
	 
// 	 if($row2['typ_id']==23){
      	
//     	$sql = "select podtyp_id from sazky where sazka_id=".$row2['sazka_id'];
//         $res5 =& $this->dbGame->query($sql);
//         if(DB::isError($res5)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky typ',"admin_ex_db");
//         if ($row5 =& $res5->fetchRow()) $this->script .= 'ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'][\'podtyp\'] ="<a href=\"/?superb=1&section=b6&udalost='.$row2['udalost_id'].'\" target=\"_blank\">'.$podtyp[$row5['podtyp_id']].'</a>";';
         
//       }
     
// 	 }
	 
// 	 if($row['system'] != 0){ 
// 	  $castka_rad = ($row['castka']/$systemAr[$cr][$row['system']]);
// 	   $this->script .= ' ticket['.$row['ticket_id'].'][\'castka_rad\'] = '.($castka_rad/$row['kurz']).';';
// 	 }
	 
// 	 $tik .= '<td rowspan="10" valign="bottom"><div class="ticket_column2" onmouseover="ShotTInfo('.$row['ticket_id'].')" onmouseout="" id="ticket_'.$row['ticket_id'].'" style="height:'.intval($height).'px;">
// 	          <div class="ticket_id" style="text-align:center"><strong>#'.$row['ticket_id'].'</strong></div>
// 			  <div class="ticket_rate" style="text-align:center">'.round($row['kurz_celkem'],2).'</div>
// 			  <div class="ticket_user" style="text-align:center"><strong>'.round($row['eur_castka'],2).'</strong></div>
// 			  <div class="ticket_user" style="text-align:center"><a href="javascript:AjaxInfo(2,'.$row['user_id'].');void(0);">'.$row['jmeno'].' '.$row['prijmeni'].' ('.$row['nick'].')</a></div>
			  
			  
// 			  </div></td>';
			 
// 	 if($row['eur_castka'] >= $signal) $this->script .= 'ticket_c[ticket_c.length] = \'ticket_'.$row['ticket_id'].'\';';
	 
// 	 $idxx = $row['ticket_id'];
// 	}
	
// 	$this->script .= ' ShotTInfo('.$idxx.');';
// 	$vrat .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post"><table id="tickettab" style="border-bottom:1px solid black;">
// 	            <tr><td height="10%" valign="top">4000</td>'.$tik.'</tr>
//                 <tr><td height="10%" valign="top">3000</td></tr>
// 				<tr><td height="10%" valign="top">2000</td></tr>
// 				<tr><td height="10%" valign="top">1000</td></tr>
// 				<tr><td height="10%" valign="top">500</td></tr>
// 				<tr><td height="10%" valign="top">250</td></tr>
// 				<tr><td height="10%" valign="top">100</td></tr>
// 				<tr><td height="10%" valign="top">50</td></tr>
// 				<tr><td height="10%" valign="top">25</td></tr>
// 				<tr><td height="10%" valign="top">10</td></tr>
// 	         </table>';
   
//    $_SERVER['REQUEST_URI'] = mb_ereg_replace("back=1","",$_SERVER['REQUEST_URI']);
//    if($prev_ticket != false && is_array($prev_ticket) && count($prev_ticket) > 0)$vrat .= '<input type="submit" name="back" style="font-size:0.8em" value="Zpět" />';
//    $vrat .= '</form>';
   
//    $vrat = mb_ereg_replace('"','\"',$vrat);
//    $vrat = mb_ereg_replace('\n','',$vrat);
//    //echo "<pre>";print_r($prev_ticket);
   
//    self::writeCache(null, serialize($prev_ticket), true);
   
//    return $vrat;
  
//   }
  
//     /**
//  * Top sazky
//  * @return void
//  */
//   private function Ticket(){
//   global $systemAr;
  
//    $vrat = '';
//    $min = false;
   
//    if(isset($_GET['on2_min']) && is_numeric($_GET['on2_min']) && intval($_GET['on2_min']) != 0) $min = intval($_GET['on2_min']);
//    if(isset($_GET['on2_signal']) &&  is_numeric($_GET['on2_signal']) && $_GET['on2_signal'] != 0) $signal = intval($_GET['on2_signal']);else $signal = 1000;
//    if(isset($_GET['on2_time']) &&  is_numeric($_GET['on2_time']) && $_GET['on2_time'] != 0) $minutes = intval($_GET['on2_time']);else $minutes = 30;
   
//    $mena = array();
//    $sql = "select e.kurz,e.mena_id from kurzmena e where e.platny_od<=now() and e.platny_do>=now() ";
//    $res =& $this->dbGame->query($sql);
//    while ($row =& $res->fetchRow()){
//     	$mena[$row['mena_id']] = $row['kurz'];
//    }
     
//    $podtyp = array();
//     $sql = "select podtyp_id,interni_nazev from podtyp";
//     $res =& $this->dbGame->query($sql);
//     if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber bet_settings',"admin_ex_db");
    
//     while($row =& $res->fetchRow()){
//     	$podtyp[$row['podtyp_id']] = $row['interni_nazev'];
//     }
   
//    $prev_ticket = false;
  
//    $klic = 0;
//    $data = self::readCache(null, true);
//    $prev_ticket = ($data != '' ? unserialize($data) : false);
   
//    if(isset($_POST['back'])){
     
//      array_splice ($prev_ticket, -12);
	 
//    }
   
//    #Vyber sloupcu#
//    $sloupec_ar = array();
//    $preklad = new Preklady();
//    $sql = "select nazev,sloupec_id from podtyp_sloupce";
//    $res =& $this->dbGame->query($sql);
//    if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky typ',"admin_ex_db");
   
//     while ($row =& $res->fetchRow()){
    
// 	 $r = $preklad->FindPreklad($row['nazev'],1); 
//      if($r[1] == 'Translation not found') $r[1] = $row['nazev'];
// 	 $sloupec_ar[$row['sloupec_id']] = $r[1];
	
//     }
    

//    //echo "<pre>";print_r($prev_ticket);
	
// 	$sql = "select a.ticket_id from ticket_pohled a inner join uzivatel b on a.user_id=b.user_id inner join kurzmena g on b.mena_id=g.mena_id where g.platny_od <= now( ) AND g.platny_do > now( ) ". ($min != false?"and (a.castka/g.kurz)>".$min:"") ." and DATE_ADD('".It6_Date::dbNow()."',INTERVAL -".$minutes." MINUTE)<a.zalozen ".($prev_ticket != false?"and a.ticket_id not in(".implode(",",$prev_ticket).")":"")." group by a.ticket_id  limit 0,12";
//     $res =& $this->dbGame->query($sql);
//     if(DB::isError($res)) { throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber z pohledu  ticket_pohled',"admin_ex_db");}
    
//     if($res->numrows()<12 && is_array($prev_ticket))  array_splice ($prev_ticket, -((12-intval($res->numrows()))));
    
//    // $sql = "select a.ticket_id,b.mena_id,j.zustatek,a.user_id,a.castka,a.zalozen,b.jmeno,b.prijmeni,a.system,a.vyplacen,b.nick,g.kurz,(a.castka/g.kurz) AS eur_castka,POW(2.718281828459,sum(ln(a.kurz))) AS kurz_celkem from ticket_pohled a inner join uzivatel b on a.user_id=b.user_id inner join uzivatel_im_data j on b.user_id=j.user_id inner join kurzmena g on b.mena_id=g.mena_id where g.platny_od <= now( ) AND g.platny_do > now( ) ". ($min != false?"and (a.castka/g.kurz)>".$min:"") ." and DATE_ADD('".It6_Date::dbNow()."',INTERVAL -".$minutes." MINUTE)<a.zalozen ".($prev_ticket != false?"and a.ticket_id not in(".implode(",",$prev_ticket).")":"")." group by a.ticket_id order by a.ticket_id limit 0,12";
//     $sql = "select a.ticket_id,b.mena_id,b.zustatek,a.user_id,a.castka,a.zalozen,b.jmeno,b.prijmeni,a.system,a.vyplacen,b.nick,b.kurz,(a.castka/b.kurz) AS eur_castka,POW(2.718281828459,sum(ln(a.kurz))) AS kurz_celkem from ticket_pohled a inner join view_admin_ticket b on a.user_id=b.user_id  where 1  ". ($min != false?"and (a.castka/b.kurz)>".$min:"") ." and DATE_ADD('".It6_Date::dbNow()."',INTERVAL -".$minutes." MINUTE)<a.zalozen ".($prev_ticket != false?"and a.ticket_id not in(".implode(",",$prev_ticket).")":"")." group by a.ticket_id order by a.ticket_id limit 0,12";
//     $res =& $this->dbGame->query($sql);
//     if(DB::isError($res)) { throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber z pohledu  ticket_pohled',"admin_ex_db");}

// 	$tik = "";$y = 0;
// 	$u_info = new UserWin();
	
// 	$this->script .= 'var ticket = new Object();';
	
// 	while ($row =& $res->fetchRow()){
	
// 	$vklad = $vyber = $saldo = 0;/*
//     $sql = "select a.* from finacni_transakce a  where a.user_id=".$row['user_id'];
// 	$res4 =& $this->dbGame->query($sql);
// 	if(DB::isError($res4)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky uzivatel',"admin_ex_db");
// 	while($row4 =& $res4->fetchRow()){
		
// 		if($row4['typ_platby'] == 1)     $vklad += $row4['castka'];
// 		else if($row4['typ_platby'] == 2) $vyber += $row4['castka'];
		
// 	}
// 	*/
// 	$saldo = round( ( ($vklad - $vyber - $row['zustatek']) /$mena[$row['mena_id']]) ,2);
	
// 	$this->script .= 'ticket['.$row['ticket_id'].'] = new Object();';
	
// 	   if(!isset($_POST['back'])) $prev_ticket[] = $row['ticket_id'];
	 
// 	 $y++;
	 
// 	 if($row['eur_castka'] <= 10){$height = $this->TicketHeight(0,10,$row['eur_castka'],0);}
// 	 else if($row['eur_castka'] <= 25){$height = $this->TicketHeight(10,25,$row['eur_castka'],1);}
// 	 else if($row['eur_castka'] <= 50){$height = $this->TicketHeight(25,50,$row['eur_castka'],2);}
// 	 else if($row['eur_castka'] <= 100){$height = $this->TicketHeight(50,100,$row['eur_castka'],3);}
// 	 else if($row['eur_castka'] <= 250){$height = $this->TicketHeight(100,250,$row['eur_castka'],4);}
// 	 else if($row['eur_castka'] <= 500){$height = $this->TicketHeight(250,500,$row['eur_castka'],5);}
// 	 else if($row['eur_castka'] <= 1000){$height = $this->TicketHeight(500,1000,$row['eur_castka'],6);}
// 	 else if($row['eur_castka'] <= 2000){$height = $this->TicketHeight(1000,2000,$row['eur_castka'],7);}
// 	 else if($row['eur_castka'] <= 3000){$height = $this->TicketHeight(2000,3000,$row['eur_castka'],8);}
// 	 else if($row['eur_castka'] <= 4000){$height = $this->TicketHeight(3000,4000,$row['eur_castka'],9);}
// 	 else $height =  600;

// 		 $sql = "select a.sazka_id,a.live,a.ticket_id,a.udalost_id,a.text,a.typ_id,a.banker,a.kurz,a.vysledek,b.nazev AS tnazev,c.nazev AS pnazev from ticket_pohled a inner join typ b on a.typ_id=b.typ_id inner join podtyp_sloupce c on a.sloupec_id=c.sloupec_id where a.ticket_id=".$row['ticket_id']." order by a.kurz desc";
// 	 $res2 =& $this->dbGame->query($sql);
//      if(DB::isError($res2)) { throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber z pohledu  ticket_pohled',"admin_ex_db");}
	 
     
// 	 $this->script .= ' ticket['.$row['ticket_id'].'][\'vyplacen\'] = '.Help::Script($row['vyplacen']).';
//                         ticket['.$row['ticket_id'].'][\'system\'] = parseInt('.Help::Script($row['system']).');
//                         ticket['.$row['ticket_id'].'][\'vyhra\'] = parseFloat('.round(($row['kurz_celkem']*$row['eur_castka']),2).');
//                         ticket['.$row['ticket_id'].'][\'banker_num\'] = 0;
//                         ticket['.$row['ticket_id'].'][\'banker_rate\'] = 1;
//                         ticket['.$row['ticket_id'].'][\'div\'] = \''.$u_info->getDiv($row['user_id']).'\';
//                         ticket['.$row['ticket_id'].'][\'saldo\'] = '.$saldo.';
//                         ticket['.$row['ticket_id'].'][\'zalozen\'] = "'.It6_Date::fromDb($row['zalozen']).'";
//                         ticket['.$row['ticket_id'].'][\'sazky\'] = new Array();';
	 
// 	 $cr = 0;
// 	 while ($row2 =& $res2->fetchRow()){
	 	
// 	 $sql = "select home_team,away_team from live where sazka_id=".intval($row2['sazka_id']);
//       $res42 =& $this->dbGame->query($sql);
//       if(DB::isError($res42)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sportu a udalosti',"admin_ex_db");
    
//       $live_text_add = '';
//       if ($row42 =& $res42->fetchRow()){
//     	$live_text_add = ' '.$row42['home_team'].' - '.$row42['away_team'].' ';
//       }
      
    
// 	  $vv = '';
// 	  $row2['vysledek'] = explode(";",$row2['vysledek']);
// 	  foreach($row2['vysledek'] as $h){if(isset($sloupec_ar[$h])) $vv .= $sloupec_ar[$h].',';}$vv = substr($vv,0,-1);
	  
// 	  if($row['system'] != 0 && $row2['banker'] != 1)$cr++; 
// 	  $this->script .= '
// 	                    ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'] = new Object();
// 	                    ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'][\'name\'] = "'.$live_text_add.Help::Script(Help::TranslateString($row2['text'],CZ_LANG_ID,$this->dbGame)).'";
// 						ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'][\'sazka\'] = '.Help::Script($row2['sazka_id']).';
// 						ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'][\'kurz\'] = '.Help::Script($row2['kurz']).';
//                         ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'][\'tnazev\'] = "'.Help::Script($row2['tnazev']).'";
//                         ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'][\'pnazev\'] = "'.Help::Script($row2['pnazev']).'";
//                         ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'][\'banker\'] = '.Help::Script($row2['banker']).';
//                         ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'][\'vysl\'] = "'.Help::Script($vv).'";
//                         ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'][\'live\'] = "'.($row2['live']==1?"[LIVE]":"").'";
//                         ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'][\'podtyp\'] = "";               
// 						'.($row['system'] != 0 && $row2['banker'] != 1?'ticket['.$row2['ticket_id'].'][\'sazky\'][ticket['.$row2['ticket_id'].'][\'sazky\'].length] = '.$row2['kurz'].';':'').' 
//                        '.($row2['banker']==1?'ticket['.$row2['ticket_id'].'][\'banker_num\'] = parseInt(ticket['.$row2['ticket_id'].'][\'banker_num\']) + 1;ticket['.$row2['ticket_id'].'][\'banker_rate\'] = parseInt(ticket['.$row2['ticket_id'].'][\'banker_rate\'])*'.$row2['kurz'].';':'').'
//                               ';
	 
// 	 if($row2['typ_id']==23){
      	
//     	$sql = "select podtyp_id from sazky where sazka_id=".$row2['sazka_id'];
//         $res5 =& $this->dbGame->query($sql);
//         if(DB::isError($res5)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky typ',"admin_ex_db");
//         if ($row5 =& $res5->fetchRow()) $this->script .= 'ticket['.$row2['ticket_id'].']['.$row2['sazka_id'].'][\'podtyp\'] ="<a href=\"/?superb=1&section=b6&udalost='.$row2['udalost_id'].'\" target=\"_blank\">'.$podtyp[$row5['podtyp_id']].'</a>";';
         
//       }
     
// 	 }
	 
// 	 if($row['system'] != 0){ 
// 	  $castka_rad = ($row['castka']/$systemAr[$cr][$row['system']]);
// 	   $this->script .= ' ticket['.$row['ticket_id'].'][\'castka_rad\'] = '.($castka_rad/$row['kurz']).';';
// 	 }
	 
// 	 $tik .= '<td rowspan="10" valign="bottom"><div class="ticket_column" onmouseover="ShotTInfo('.$row['ticket_id'].')" onmouseout="" id="ticket_'.$row['ticket_id'].'" style="min-height:85px;height:'.intval($height).'px;">
// 	          <div class="ticket_id" style="text-align:center"><strong>#'.$row['ticket_id'].'</strong></div>
// 			  <div class="ticket_rate" style="text-align:center">'.round($row['kurz_celkem'],2).'</div>
// 			  <div class="ticket_user" style="text-align:center"><strong>'.round($row['eur_castka'],2).'</strong></div>
// 			  <div class="ticket_user" style="text-align:center"><a href="javascript:AjaxInfo(2,'.$row['user_id'].');void(0);">'.$row['jmeno'].' '.$row['prijmeni'].' ('.$row['nick'].')</a></div>
			  
			  
// 			  </div></td>';
			 
// 	 if($row['eur_castka'] >= $signal) $this->script .= 'ticket_c[ticket_c.length] = \'ticket_'.$row['ticket_id'].'\';';
	 
// 	}
	
// 	$vrat .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post"><table id="tickettab" style="border-bottom:1px solid black;">
// 	            <tr><td height="10%" valign="top">4000</td>'.$tik.'</tr>
//                 <tr><td height="10%" valign="top">3000</td></tr>
// 				<tr><td height="10%" valign="top">2000</td></tr>
// 				<tr><td height="10%" valign="top">1000</td></tr>
// 				<tr><td height="10%" valign="top">500</td></tr>
// 				<tr><td height="10%" valign="top">250</td></tr>
// 				<tr><td height="10%" valign="top">100</td></tr>
// 				<tr><td height="10%" valign="top">50</td></tr>
// 				<tr><td height="10%" valign="top">25</td></tr>
// 				<tr><td height="10%" valign="top">10</td></tr>
// 	         </table>';
   
//    $_SERVER['REQUEST_URI'] = mb_ereg_replace("back=1","",$_SERVER['REQUEST_URI']);
//    if($prev_ticket != false && is_array($prev_ticket) && count($prev_ticket) > 0)$vrat .= '<input type="submit" name="back" style="font-size:0.8em" value="Zpět" />';
//    $vrat .= '</form>';
   
//    $vrat = mb_ereg_replace('"','\"',$vrat);
//    $vrat = mb_ereg_replace('\n','',$vrat);
//    //echo "<pre>";print_r($prev_ticket);
   
//    self::writeCache(null, serialize($prev_ticket), true);
   
//    return $vrat;
  
//   }
//    /**
//  * Top sazky
//  * @return void
//  */
//   private function TopBet(){

// 	$vrat = '';
	
// 	$mena = $bet = $bet_text = $p = array();
	
// 	if(isset($_POST['on1_1'])) $kurz = floatval($_POST['on1_1']);else $kurz = 1;
	
// 	 $sql = "select e.kurz,e.mena_id from kurzmena e where e.platny_od<=now() and e.platny_do>=now() ";
//      $res =& $this->dbGame->query($sql);
//      while ($row =& $res->fetchRow()){
//     	$mena[$row['mena_id']] = $row['kurz'];
//      }
    
//    $sql = "SELECT  c.ticket_id,c.sazka_id,c.text,b.mena_id,c.castka
// from  ticket_pohled c inner join uzivatel b on c.user_id=b.user_id  inner join sazky d
// on c.sazka_id=d.sazka_id where c.vyplacen=0 and c.kurz>".$kurz." and c.status=0 and d.platna_do>now()";
//      $res =& $this->dbGame->query($sql);
//     if(DB::isError($res)) { $this->dbGame->autoCommit(true);throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber z pohledu  sazka_pohled',"admin_ex_db");}
    
//    while ($row =& $res->fetchRow()){
   	  
//    	  if(!isset($p[$row['ticket_id']])){
//    	  $sql = "select count(*) as pocet from ticket_pohled f where f.ticket_id=".$row['ticket_id'];
//    	  $res3 =& $this->dbGame->query($sql);
    
//    	  if ($row3 =& $res3->fetchRow())$p[$row['ticket_id']]=$row3['pocet'];
//    	  }
   	  
//    	  if(!isset($bet[$row['sazka_id']])) $bet[$row['sazka_id']] = 0;
   	  
//    	  $castka = (($row['castka']/$mena[$row['mena_id']])/$p[$row['ticket_id']]);
//    	  $bet[$row['sazka_id']] = $bet[$row['sazka_id']] + $castka;
//    	  $bet_text[$row['sazka_id']] = $row['text'];
   	  
//    }
  
//    asort($bet,SORT_NUMERIC);
//    $bet = array_reverse($bet,true);
 
// //	  $sql = "SELECT  c.sazka_id,c.text,SUM(((c.castka/(select count(*) from game.ticket_pohled f where f.ticket_id=c.ticket_id group by f.ticket_id))/
//    //        (select e.kurz from game.kurzmena e where e.platny_od<=now() and e.platny_do>=now() and e.mena_id=b.mena_id))) AS soucet
//   //          from
//  // game.ticket_pohled c inner join game.uzivatel b on c.user_id=b.user_id  inner join game.sazky d on c.sazka_id=d.sazka_id where c.kurz>".$kurz." and c.status=0 and d.platna_do>now() group by c.sazka_id order by soucet desc limit 0,30";

// 	#Kolik se na sazku vsadilo#
//    /* $sql = "SELECT  c.sazka_id,c.text,
// 	       SUM(((c.castka/(select count(*) from ticket_pohled f where f.ticket_id=c.ticket_id group by f.ticket_id))/
//            (select e.kurz from kurzmena e where e.platny_od<=now() and e.platny_do>=now() and e.mena_id=c.mena_id))) AS soucet
// 		   FROM (SELECT a.sazka_id,a.text,a.ticket_id,a.castka,b.mena_id FROM `ticket_pohled` a inner join uzivatel b on a.user_id=b.user_id 
//                  where a.kurz>".$kurz." and a.status=0 group by a.sazka_id,a.ticket_id) c inner join sazky d on c.sazka_id=d.sazka_id where d.status=0 and d.platna_do>now()
//      group by c.sazka_id order by soucet desc limit 0,30";*/
//     $res =& $this->dbGame->query($sql);
//     if(DB::isError($res)) { $this->dbGame->autoCommit(true);throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber z pohledu  sazka_pohled',"admin_ex_db");}
    
    
    
// 	$vrat .= '<table width="100%" class="tab1"><tr><th>Sázka ID</th><th>Text</th><th>Vsazeno</th></tr>';
//     foreach ($bet as $k=>$h){
	
// 	   $vrat .= '<tr><td align="center">#'.$k.'</td><td align="center">'.Help::Html(Help::TranslateString($bet_text[$k],CZ_LANG_ID,$this->dbGame)).'</td><td align="center"><span class="important">'.Help::Html(round($h,2)).'</span></td></tr>';
	   
// 	}
//     $vrat .= '</table>';
	
// 	$vrat = mb_ereg_replace('"','\"',$vrat);
// 	return $vrat;
  
//   }

//    /**
//  * metoda vypocita vysku sloupce grafu
//  * @param int $odecet cislo ktere se odecita z top hodnoty v danem radku
//  * @param dloat $castka castka tiketu
//  * @param float $castka castka tiketu
//  * @param int $nas kolika se nasobi konstatni vyska sloupce
//  * @return void
//  */
//   private function TicketHeight($odecet=0,$hodnota=NULL,$castka=NULL,$nas=0){
  
//    if($castka == NULL || !is_numeric($castka)) return false;
//    if($hodnota == NULL || !is_numeric($hodnota)) return false;
   
//    $rozdil = $hodnota-$odecet;
   
//    $pr = (( ($castka-$odecet) /($rozdil) ) * 100);
   
//    $height = ($nas*60 + ( ($pr/100) *60) );
   
   
   
//    return $height;
  
//   }
  
//  /**
//  * metoda vypise vsechny zadane tickety podle filtru
//  * @return void
//  */
//   public function ShowTicket(){
   
//    $preklad = new Preklady();
//    $udalost = $help  = $user = "";
//    $prumer = 0;
//    $sport_udalost = $sport = $ticket = $help_ar = $typ_ar =  $bookmaker = $sloupec_ar = $sloupec = $sazka_poradi = $user_ar = $podtyp = array();
   

	

    
//    #Vyber sportu a udalosti#
//    $sql = "select a.sport_id,a.nazev,b.nazev AS udalost_nazev,b.udalost_id from sport a inner join udalost b on a.sport_id=b.sport_id  order by a.pozice,b.pozice";
//    $res =& $this->dbGame->query($sql);
//    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sportu a udalosti',"admin_ex_db");
   

//    while ($row =& $res->fetchRow()){
	   
// 	  $sport_udalost[$row['udalost_id']]['sport_id'] = $row['sport_id'];
// 	  $sport[$row['sport_id']][] = $row['udalost_id'];
	  
// 	  if(!isset($sport_udalost[$row['udalost_id']]['sport_nazev'])){

// 	   $r = $preklad->selectData("where lang_id=1 and index_pole='".Help::Slash($row['nazev'])."'"); 
// 	   if (!$row2 =& $r->fetchRow() || mb_strlen($row2['text']) < 0) $row2['text'] = "Translation not found";
// 	   $sport_udalost[$row['udalost_id']]['sport_nazev'] = $row2['text'];
	   
// 	  }
	  
// 	  $r = $preklad->selectData("where lang_id=1 and index_pole='".Help::Slash($row['udalost_nazev'])."'"); 
// 	  if (!$row2 =& $r->fetchRow() || mb_strlen($row2['text']) < 0) $row2['text'] = "Translation not found";
	  
// 	  $sport_udalost[$row['udalost_id']]['udalost_nazev'] = $row2['text'];
	  
//    }
   
//    #Vyber uzivatelu#
//    $sql = "select a.jmeno,a.prijmeni,a.nick,a.user_id,b.mena_text from uzivatel a inner join mena b on a.mena_id=b.mena_id where user_id not in(".implode(",",$GLOBALS['EXCLUDEUSER']).")";
//    $res =& $this->dbGame->query($sql);
//    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
   
//    while ($row =& $res->fetchRow()){
   
//      $user .= "<option  value=\"".$row['user_id']."\" ".(isset($_POST['user']) && $_POST['user'] == $row['user_id']?"selected=\"selected\"":"").">".Help::Html($row['nick'])."</option>";
//      $user_ar[$row['user_id']]['nick'] = $row['nick'];
// 	 $user_ar[$row['user_id']]['jmeno'] = $row['jmeno']." ".$row['prijmeni'];
// 	 $user_ar[$row['user_id']]['mena'] = $row['mena_text'];
	 
//    }
   
//    #Vyber bookmakeru#
//    $sql = "select a.jmeno,a.prijmeni,a.nick,a.bookmaker_id from bookmaker a";
//    $res =& $this->dbGame->query($sql);
//    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber bookmakera',"admin_ex_db");
   
//    while ($row =& $res->fetchRow()){
   
//      $bookmaker[$row['bookmaker_id']]['nick'] = $row['nick'];
// 	 $bookmaker[$row['bookmaker_id']]['jmeno'] = $row['jmeno']." ".$row['prijmeni'];
	 
//    }
   
//    #Vyber sazek#
//    $where = $where2 = "";
//    if(isset($_POST['od']) && It6_Date::checkFormat($_POST['od'])) $where .= "k.zalozen>='".It6_Date::toDb($_POST['od'])."' and ";
//    if(isset($_POST['do']) && It6_Date::checkFormat($_POST['do'])) $where .= "k.zalozen<='".It6_Date::toDb($_POST['do'])."' and ";
//    if(isset($_POST['user']) && $_POST['user'] != 0) $where .= "k.user_id=".intval($_POST['user'])." and ";
//    if(isset($_POST['proplacene']) && $_POST['proplacene'] == 1) $where .= "k.vyplacen<>0 and ";
//    if(isset($_POST['proplacene']) && $_POST['proplacene'] == 2) $where .= "k.vyplacen=0 and ";
   
//    $where = substr($where,0,-4);
//    if(mb_strlen($where) < 1) $where = "1";
   
//    $where2 = str_replace("k.","b.",$where);
//    if(isset($_POST['udalost']) && $_POST['udalost'] != 0) $where2 .= " and b.udalost_id=".intval($_POST['udalost']);
   
//    #Vyber typu#
//    $sql = "select nazev,typ_id from typ";
//    $res =& $this->dbGame->query($sql);
//    if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky typ',"admin_ex_db");
   
//    while ($row =& $res->fetchRow()){
    
// 	 $r = $preklad->FindPreklad($row['nazev'],1); 

// 	 $typ_ar[$row['typ_id']] = $r[1];
	
//    }

//    #Vyber sloupcu#
//    $sql = "select nazev,sloupec_id from podtyp_sloupce";
//    $res =& $this->dbGame->query($sql);
//    if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky typ',"admin_ex_db");
   
//    while ($row =& $res->fetchRow()){
    
// 	 $r = $preklad->FindPreklad($row['nazev'],1); 
//      if($r[1] == 'Translation not found') $r[1] = $row['nazev'];
// 	 $sloupec_ar[$row['sloupec_id']] = $r[1];
	
//    }
   
   
//    #vyber tiketu#
//    //$sql = "SELECT *,(select ROUND(POW(2.718281828459,c.hodnota),2) from (select b.ticket_id,sum(ln(b.kurz)) AS hodnota from (SELECT d.ticket_id,d.kurz FROM `ticket_pohled` d where d.status<>1 and d.ticket_sazka_zrusena <>1 group by d.ticket_id,d.sazka_id) b  group by b.ticket_id) c where c.ticket_id = a.ticket_id) AS kurz_celkem, (SELECT g.kurz FROM kurz f INNER JOIN kurz_mena g ON f.id_kurz = g.id_kurz WHERE f.platny_od <= now( ) AND f.platny_do > now( ) AND g.id_mena = (SELECT h.mena_id FROM uzivatel h WHERE h.user_id =a.user_id )) AS akt_kurz, (a.castka/a.akt_kurz) AS realna_castka,(a.kurz_celkem * a.realna_castka) AS vyhra_ticket FROM `ticket_pohled` a where ".$where."  and a.ticket_id in (SELECT b.ticket_id FROM ticket_pohled b where ".$where2." group by b.ticket_id) GROUP BY a.sazka_id, a.ticket_id order by ".(isset($_POST['sort']) && $_POST['sort'] == 1?"a.zalozen":(isset($_POST['sort']) && $_POST['sort'] == 2?"a.vyhra_ticket":(isset($_POST['sort']) && $_POST['sort'] == 3?"a.castka":"a.ticket_id")))." desc limit 0,".(isset($_POST['top']) && is_numeric($_POST['top'])?intval($_POST['top']):"20");
//    $sql = "select *,(k.castka/k.akt_kurz) AS realna_castka,round((k.kurz_celkem * (k.castka/k.akt_kurz)),2) AS vyhra_ticket from (SELECT *,(select POW(2.718281828459,c.hodnota)  from (select b.ticket_id,sum(ln(b.kurz)) AS hodnota from (SELECT d.ticket_id,d.kurz FROM `ticket_pohled` d where d.status<>1 and d.ticket_sazka_zrusena <>1 group by d.ticket_id,d.sazka_id) b group by b.ticket_id) c where c.ticket_id = a.ticket_id)  AS kurz_celkem,(SELECT g.kurz FROM kurz f INNER JOIN kurz_mena g ON f.id_kurz = g.id_kurz WHERE f.platny_od <= now( ) AND f.platny_do > now( ) AND g.id_mena = (SELECT h.mena_id FROM uzivatel h WHERE h.user_id=a.user_id )) AS akt_kurz FROM `ticket_pohled` a GROUP BY a.sazka_id, a.ticket_id) k  where ".$where." and k.ticket_id in (SELECT b.ticket_id FROM ticket_pohled b where ".$where2." group by b.ticket_id) order by ".(isset($_POST['sort']) && $_POST['sort'] == 1?"k.zalozen":(isset($_POST['sort']) && $_POST['sort'] == 2?"vyhra_ticket":(isset($_POST['sort']) && $_POST['sort'] == 3?"realna_castka":"k.ticket_id")))." desc";
//    $res2 =& $this->dbGame->query($sql);
//    if(DB::isError($res2)) throw new ExHandler('<br /><br />'.$sql.'<br /><br />Nepodarilo se provest dotaz: vyber z pohledu ticket_pohled, sazka_kurz a podtyp_sloupec',"admin_ex_db");
   
//    //(select ROUND(POW(2.718281828459,c.hodnota),2) from (select b.ticket_id,sum(ln(kurz)) AS hodnota from (SELECT d.ticket_id,d.kurz FROM `ticket_pohled` d where d.status<>1 group by d.ticket_id,d.sazka_id) b  group by b.ticket_id) c where c.ticket_id = a.ticket_id)
   
//    $xx = 0;
//    if(!isset($_POST['top']) || !is_numeric($_POST['top'])) $_POST['top'] = 20;
   
//    while ($row =& $res2->fetchRow()){ 
    
		
// 	if(!isset($ticket[$row['ticket_id']])){ 
      
// 	  if($_POST['top'] <= $xx) break;
	  
// 	  //$ticket[$row['ticket_id']]['udalost'] = $row['udalost_id'];
// 	  $ticket[$row['ticket_id']]['user'] = $user_ar[$row['user_id']]['jmeno'].' ('.$user_ar[$row['user_id']]['nick'].')';
// 	  $ticket[$row['ticket_id']]['mena'] = $user_ar[$row['user_id']]['mena'];
// 	  $ticket[$row['ticket_id']]['zalozen'] = It6_Date::fromDb($row['zalozen']);
// 	  $ticket[$row['ticket_id']]['zruseno'] = $row['zruseno']; //zruseny cely ticket
// 	  $ticket[$row['ticket_id']]['duvod_zruseni'] = $row['duvod_zruseni'];
// 	  if(is_numeric($row['zrusil_bookmaker_id']))$ticket[$row['ticket_id']]['zrusil'] = $bookmaker[$row['zrusil_bookmaker_id']]['jmeno'].' ('.$bookmaker[$row['zrusil_bookmaker_id']]['nick'].')';
// 	  $ticket[$row['ticket_id']]['castka'] = $row['castka'];
// 	  $ticket[$row['ticket_id']]['vyplacen'] = $row['vyplacen'];
// 	  $ticket[$row['ticket_id']]['kurz_celkem'] = $row['kurz_celkem'];
// 	  $ticket[$row['ticket_id']]['vyhra_ticket'] = $row['vyhra_ticket'];
// 	  $ticket[$row['ticket_id']]['realna_castka'] = $row['realna_castka'];
	  
// 	  $prumer += $row['realna_castka'];
// 	  $xx++;
	  
//     }
    

    
// 	if(!isset($sport_udalost[$row['udalost_id']]['pocet'])) $sport_udalost[$row['udalost_id']]['pocet'] = 1;
// 	else $sport_udalost[$row['udalost_id']]['pocet']++;
	
// 	$ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['sloupec_name'] = $sloupec_ar[$row['sloupec_id']];
// 	$ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['sloupec_id'] = $row['sloupec_id'];
// 	$ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['kurz'] = $row['kurz'];
// 	$ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['status'] = $row['status'];
// 	$ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['text'] = $row['text'];
// 	$ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['typ'] = $typ_ar[$row['typ_id']];
// 	$ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['vysledek'] = $row['vysledek'];
// 	$ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['ticket_sazka_zrusena'] = $row['ticket_sazka_zrusena'];
// 	$ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['ticket_sazka_duvod_zruseni'] = $row['ticket_sazka_duvod_zruseni'];
// 	if(isset($bookmaker[$row['ticket_sazka_zrusil_bookmaker_id']]) && is_numeric($row['ticket_sazka_zrusena']))$ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['zrusil_bookmaker'] = $bookmaker[$row['ticket_sazka_zrusil_bookmaker_id']]['jmeno'].' ('.$bookmaker[$row['ticket_sazka_zrusil_bookmaker_id']]['nick'].')';
	
		
//    }
   
//   //echo "<pre>";print_r($sport_udalost);
//    foreach($sport as $k=>$h2){
	   
// 	  foreach($h2 as $h){
	  
// 	    if($help == "" || $help != $k){
	  
// 	     $help = $k;
// 	     $udalost .= "<optgroup label=\"".Help::Html($sport_udalost[$h]['sport_nazev'])."\">";
	   
// 	    }
	    
// 		if(!isset($sport_udalost[$h]['pocet'])) $sport_udalost[$h]['pocet'] = 0;
//         $udalost .= "<option  value=\"".$h."\" ".(isset($_POST['udalost']) && $_POST['udalost'] == $h?"selected=\"selected\"":"").">".Help::Html($sport_udalost[$h]['udalost_nazev'])." (".intval($sport_udalost[$h]['pocet']).")</option>";
	  
// 	    if($help != $k){
// 	     $udalost .= "</optgroup>";
// 	    }
	  
// 	  }
	  
//    }
   
   
//    $this->vrat .= "<form method=\"post\" action=\"?superb=1&section=".$this->section."\">";
//    $this->vrat .= "<a href=\"#bo\">Dolů</a><br /><br />";

   
//    $this->vrat .= '<table style="border:1px solid #E3E3E3;font-size:0.9em;background:#708090;color:white">';
//    $this->vrat .= ' <tr><td class="textleft" style="background:#929EAD;" colspan="4">Filtr</td></tr>';
   
//    $this->vrat .= '<tr><td><select style="font-size:0.8em" name="user" size="4"><option value="0">Všichni</option>'.$user.'</select></td></tr>';
//    $this->vrat .= "<tr><td><select name=\"udalost\" style=\"font-size:0.8em\" ><option  value=\"0\">Všechny Turnaj/UdĂˇlost</option>".$udalost."</select></td></tr>";
//    $this->vrat .= '<tr><td><strong>VytvoĹ™enĂ© (dd.mm.rrrr hh::mm:ss):</strong> <input type="text" name="od" class="sinput3" value="'.(isset($_POST['od'])?Help::Html($_POST['od']):"").'" /> - <input type="text" name="do" class="sinput3" value="'.(isset($_POST['do'])?Help::Html($_POST['do']):"").'" />';
   
//    $this->vrat .= '<tr><td><strong>ProplacenĂ©:</strong><input type="checkbox" class="no" name="proplacene" '.(isset($_POST['proplacene']) && $_POST['proplacene']==1?"checked=\"checked\"":"").' value="1" /> <strong>Neproplacené:</strong> <input type="checkbox" class="no" name="proplacene" '.(isset($_POST['proplacene']) && $_POST['proplacene']==2?"checked=\"checked\"":"").' value="2" /> ';
//    $this->vrat .= '<tr><td>Top:<input type="text" class="sinput3" name="top" value="'.(isset($_POST['top']) && is_numeric($_POST['top'])?intval($_POST['top']):"20").'" />
//                     <select name="sort" style="font-size:0.8em"><option value="1" '.(isset($_POST['sort']) && $_POST['sort'] == 1?"selected=\"selected\"":"").' >Podle data</option><option value="2" '.(isset($_POST['sort']) && $_POST['sort'] == 2?"selected=\"selected\"":"").' >Podle vĂ˝hry</option><option value="3" '.(isset($_POST['sort']) && $_POST['sort'] == 3?"selected=\"selected\"":"").' >Podle sázky</option></select></td></tr>';
//    $this->vrat .= '<tr><td><input type="submit" name="filtr" class="inputs" value="Filtr" />';
//    $this->vrat .= '</table>';
   
//    $this->TicketForm($ticket,$prumer);
   
//    $this->vrat .= '</form><a name="bo"></a>'; 
  
  

   
//   }
 
 
 
  
//  /**
//  * vyber dat z databaze
//  * @return object
//  */
//   public function selectData($where=""){

	 
//   }
  
  
//   /**
//  * Nastaveni prav k sekci
//  * @param int $update pravo zapisu
//  * @param int $delete pravo smazani
//  * @return void
//  */
//   public function setPrivileges($update,$delete){
   
   
  
//   }
  
//  /**
//  * Vraci vystup do tridy main
//  * @return string
//  */
//   public function getContent(){
   
//     return $this->vrat;
	
//   }
    
   
//   public function __destruct(){
    
	
	 
	 
//   }
  
// }

?>
