<?php
final class main{

/**
 * objekt databaze spojeni s databazi game
 * @access private
 * @var PEAR::DB
 */
private  $dbGame;

/**
 * spojeni na databazi admin
 * @access private
 * @var DB
 */
private  $db;

private static $instance = null; // legacy code singleton parody

private $section = 0;       // sekce ve ktere jsme default je 0 zadna sekce
private $obsah = "";        //zde bude obsah strabek ziskany z trid
private $layout = null;     //If non-default layout is to be used, its name will be stored in this var
private $obj;               //objekt pro ruzne tridy podle sekce kde jsme
private $menu;              //objekt pro inicializaci menu a prav k menu
private $error = "";        //muze obsahovat chybovou zpravu
protected $tpl;             //objekt pro praci s templaty
/**
 * titulek strankz
 * @access private
 * @var string
 */
private $title = "Administration";

/**
 * priznak zda je prihlasen bookmaker
 * @access private
 * @var bool
 */
private $bookmaker = false;
private $superbookmaker = false;
private $admin = false;
private $superadmin = false;
private $controller = null;

public function __construct($bool = false, $controller = null){

	if (!isset(static::$instance)) {
		static::$instance = $this;
	}

   if(isset($_SERVER['HTTP_X_FORWARDED_HOST'])) $_SERVER["SERVER_NAME"] = $_SERVER['HTTP_X_FORWARDED_HOST'];

   if(!$bool) throw new ExHandler("Lze vytvorit pouze jednu instanci objektu","admin_ex_class");

   $this->controller = $controller;

   $this->db = DbUtil::connectAdminDb();
   $this->dbGame = DbUtil::connectWebDb();
   $acl = Zend_Registry::get('acl');

   $this->findSection(); // zjisteni aktualni sekce

   #Prihlaseni a prochazeni stranek bookmakerem#
	$this->superbookmaker = $acl->userHasRole(It6_Acl_Admin::ROLE_SUPERBOOKMAKER);
	$this->bookmaker = $this->superbookmaker || $acl->userHasRole(It6_Acl_Admin::ROLE_BOOKMAKER);
	$this->superadmin = $acl->userHasRole(It6_Acl_Admin::ROLE_SUPERADMIN);
	$this->admin = $this->superadmin || $acl->userHasRole(It6_Acl_Admin::ROLE_ADMIN);

   //$ob = new DepositBonus(20,100);
   //$ob->InitBonus();

   //if(!SesClass::open($this->db)) throw new ExHandler('Nepodarilo se inicializovat Session',"admin_ex_db"); //IT6: presunuto do bootstrapu

   //SesClass::setUserStatus(2);$_SESSION['user_id']=3;$_SESSION['superadmin']=1;$_SESSION['nick']='kusa';

   //if(SesClass::getUserStatus() == 3) $_SESSION['starttime'] = time();   //nastavime do sesison startovaci cas v adminu
	if(It6_Session_Admin::STATUS_JUST_AUTHENTICATED == It6_Session_Admin::getStatus())
		$_SESSION['starttime'] = time();   //nastavime do session startovaci cas v adminu

   //if(SesClass::getUserStatus() == 2 || SesClass::getUserStatus() == 3){ //pouze pokud je prihlaseny
	if (It6_Session_Admin::isAuthenticated()) {
		$this->menu = new Menu($this->db, $this->section); //vytvoreni objektu menu a zjisteni prav k jednotlivym prvkum menu
	}

   //if(SesClass::getUserStatus() == 1) $this->error = "Session has expired";  //vyprsela session
   //if(SesClass::getUserStatus() == 4) {$this->error = $GLOBALS['ses_zprava'];unset($GLOBALS['ses_zprava']);} //vychozeny uzivatel
   //if(SesClass::getUserStatus() == 5) {$this->error = "Účet byl zablokován";} //zakazany
	switch (It6_Session_Admin::getStatus()) {
	case It6_Session_Admin::STATUS_TIMEOUT:
		$this->error = "Session has expired";
		break;
	case It6_Session_Admin::STATUS_ABORTED:
		$this->error = It6_Session_Admin::getMessage();
		It6_Session_Admin::setMessage(null);
		break;
	case It6_Session_Admin::STATUS_BLOCKED:
		$this->error = "Účet byl zablokován";
		break;
	default:
		break;
	}

	if(isset($_SESSION["recent_sections"])) {
		if ($i = array_search($this->section, $_SESSION["recent_sections"]))
			unset($_SESSION["recent_sections"][$i]);
	}
	else
		$_SESSION["recent_sections"] = array();
	if(isset($this->section) && $this->section)
		$_SESSION["recent_sections"][] = $this->section;
	if (count($_SESSION["recent_sections"]) > 7)
		array_shift($_SESSION["recent_sections"]);

	// pro zpetnou kompatibilitu starych z admina
	$_SESSION['bookmaker'] = ($this->bookmaker ? It6_Session_Admin::getUserData('id') : 0);
	$_SESSION['superbookmaker'] = $this->superbookmaker;
	$_SESSION['admin'] = ($this->admin ? It6_Session_Admin::getUserData('id') : 0);
	$_SESSION['superadmin'] = $this->superadmin;

   #pokud je prihlasen bookmaker#
	//if($this->bookmaker && (SesClass::getUserStatus() == 2 || SesClass::getUserStatus() == 3)){
	if (It6_Session_Admin::isAuthenticated()) {

		//$this->menu->CreateBookMenu();

		$this->section = (isset($_GET['section'])?$_GET['section']:"");

		###########################################################################
		#Zde zacina volani jednotlivych modulu podle sekce kde jsme pro bookmakery#
		###########################################################################

		if(!$this->menu->isSectionAllowed(empty($this->section) ? 1 : $this->section))
			$this->obsah = "<div class=\"errormsg\">Nemáte dostatečné právo pro vstup do této sekce</div>\n";
		else {

			//$bm = $this->menu->GetBMenu();

			#HP#
			if($this->section == '1' || $this->section == '' || !isset($this->section)){
			$this->obj =  new BookHp($this->section);
			$this->obj->runAction();
			$this->obsah = $this->obj->getContent();
			}

			#Prace s cislenikem sportu#
			if($this->section == '134'){
			$this->obj =  new Sport($this->section);
			$this->obj->runAction();
			$this->obsah = $this->obj->getContent();
			}

			#Prace s cislenikem udalosti#
			if($this->section == '136' || $this->section == 'b10'){
				$this->obj =  new Udalost($this->section);
				$this->obj->runAction();
				$this->obsah = $this->obj->getContent();
			}

			#Prace s cislenikem udalosti prava#
			if($this->section == '137') {
				$this->obj =  new Udalost($this->section);
				$this->obj->runAction();
				$this->obsah = $this->obj->getContent();
			}

			#Prace s cislenikem podtypu#
			if($this->section == '139'){
			$this->obj =  new PodTyp($this->section);
			$this->obj->runAction();
			$this->obsah = $this->obj->getContent();
			}

			#Prace s vyhernost a min. max k sazce#
			if($this->section == '140'){
			$this->obj =  new Vyhernost($this->section);
			$this->obj->runAction();
			$this->obsah = $this->obj->getContent();
			}

			#Vytvoreni sazky#
			if($this->section == '144' || $this->section == '145'){
			$this->obj =  new Sazky($this->section);
			$this->obj->VytvorSazku();
			$this->obsah = $this->obj->getContent();
			}

			#Zobrazeni sazky#
			if($this->section == '146'){
			$this->obj =  new Sazky($this->section);
			$this->obj->ZobrazSazku();
			$this->obsah = $this->obj->getContent();
			}

			#Zobrazeni tiketu a infa k nim#
			if($this->section == '148'){
			$this->obj =  new Sazky($this->section, $this->controller);
			$this->obj->ZobrazTicket();
			$this->obsah = $this->obj->getContent();
			}
			#Nastenka#
			if($this->section == '158'){
			$this->obj =  new Nastenka($this->section);
			$this->obsah = $this->obj->getContent();
			}

			#Proplaceni ticketu#
			if($this->section == '147'){
			$this->obj =  new Sazky($this->section);
			$this->obj->Proplaceni();
			$this->obsah = $this->obj->getContent();
			}

			#Vytvoreni live sazky#
			if($this->section == '155'){
			$this->obj =  new CreateLiveBet($this->section);
			$this->obj->RunAction();
			$this->obsah = $this->obj->getContent();
			}
			
// Commented out by Martin on 31.8.2012 as this section is not used
// If no problems arise, this block can be probably deleted in the future
// This class is also the only place from where the method SazkaTicket->TicketForm is called.
// If this block gets deleted the above mentioned method should probably also be removed.
// This class is defined in class.OnlineBook.php. If this block gets deleted, the definition should be also removed
//			#Online sledovani#
//			if($this->section == '149'){
//			$this->obj =  new OnlineBook($this->section);
//			$this->obj->RunAction();
//			$this->obsah = $this->obj->getContent();
//			}

			#Kombinace druh#
			if($this->section == '141'){
			$this->obj =  new KombinaceDruh($this->section);
			$this->obj->RunAction();
			$this->obsah = $this->obj->getContent();
			}
			#Tournamnets#
			if($this->section == '142'){
			$this->obsah = '<iframe src="tournament.php" width="100%" height="750"></iframe>';
			}
			#Povolovani tiketu
			if($this->section == '150'){
			$this->obsah = "<script>window.open('ajax.server.php?work=3','','width=720,height=800,top=0,left=400');</script>";
			}
			#Povolovani tiketu Betradar live
			if($this->section == '151'){
			$this->obsah = "<script>window.open('ajax.server.php?work=3&br=1','','width=720,height=800,top=0,left=400');</script>";
			}
			#Povolovani tiketu live sazky
			if($this->section == 'b124'){ //TODO: this section is not in DB
			$this->obsah = "<script>window.open('ajax.server.php?work=6','','width=720,height=800,top=0,left=400');</script>";
			}

			#Zobrazeni live sazky#
			if($this->section == '156'){
			$this->obj =  new ShowLiveBet($this->section);
			$this->obj->RunAction();
			$this->obsah = $this->obj->getContent();
			}
			#Reklama sazky
			if($this->section == '153'){
				$this->obj =  new ReklamaSazky($this->section);
				$this->obj->RunAction();
				$this->obsah = $this->obj->getContent();
			}
			#Oblast
			if($this->section == '135'){
				$this->obj =  new Oblast($this->section);
				$this->obj->RunAction();
				$this->obsah = $this->obj->getContent();
			}
			#statistika povolovani
			if($this->section == '152'){
				$this->obj =  new PovolovaniStat($this->section);
				$this->obj->RunAction();
				$this->obsah = $this->obj->getContent();
			}

			#Betradar live betting
			if($this->section == '157'){
				$this->obj =  new BetRadarLive($this->section);
				$this->obj->RunAction();
				$this->obsah = $this->obj->getContent();
			}

		}
	}
   ############################################################
   #Zde zacina volani jednotlivych modulu podle sekce kde jsme#
   ############################################################

   //if(SesClass::getUserStatus() != 1 && SesClass::getUserStatus() != 4 && SesClass::getUserStatus() != 0 && SesClass::getUserStatus() != 5){ //pouze kdyz je session aktivni
	if (It6_Session_Admin::isAuthenticated()) {

      #Uvodni stranka Homepage#
      //if((SesClass::getUserStatus() == 3 || $this->section == 1) && $this->menu->getPriv(2,'read')){
		if ( (It6_Session_Admin::STATUS_JUST_AUTHENTICATED == It6_Session_Admin::getStatus() || $this->section == 1) && $this->menu->getPriv(2,'read') ) {

/*
  $this->obj = new Redirect();
  $this->obj->runAction("kontakt");
  //print_r($this->obj->redirect);
  $this->obj =  new UserKolekce($this->section);
  $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
  $this->obj->runAction();
  $this->obsah = $this->obj->getContent();
*/
		}

      #Prace s menu#
      else if($this->section == 3 && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new MenuDef($this->db,$this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction($this->menu->getMenuArray(),$this->menu->zobrazeno);
        $this->obsah = $this->obj->getContent();
      }

      #Prace s uzivatelskym menu#
      else if($this->section == 45 && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new UzivatelskeMenu($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Prace s administratory#
      else if($this->section == 14 && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new AdminKolekce($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Prace s jazyky#
      else if($this->section == 41 && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new Jazyky($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Prace s Měnou#
      else if($this->section == 42 && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new Mena($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Prace se Zememi#
      else if($this->section == 43 && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new Zeme($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

     #Prace s uzivateli#
     else if(($this->section == 40 && $this->bookmaker) || (($this->section == 40 || $this->section == 44) && $this->menu->getPriv($this->section,'read'))){
      $this->obj =  new UserKolekce($this->section);
      $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
      $this->obj->runAction();
      $this->obsah = $this->obj->getContent();
     }

      #Prace s preklady#
      else if(($this->section == 46 || $this->section == 72) && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new Preklady($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }


      #Prace s chybami a logy#
      else if($this->section == 48 && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new ChybyLogy($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Prace s dalsim nastavenim#
      else if($this->section == 49 && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new Nastaveni($this->section,$this->db);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Statistiky#
        else if(($this->section == 57 || $this->section == 59 || $this->section == 60 || $this->section == 61) && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new Statistiky($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Finance#
      else if(($this->section == 58 || $this->section == 62 || $this->section == 63 || $this->section == 64 || $this->section == 65) && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new Finance($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Hledani shody mezi uzivateli#
      else if($this->section == 73 && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new Shoda($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Sprava Bookmakeru#
      else if($this->section == 75 && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new Bookmaker($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Statistiky Sazek#
      else if(($this->section == 76 && $this->bookmaker) || (($this->section == 76) && $this->menu->getPriv($this->section,'read'))){
        $this->obj =  new StatistikySazky($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Limity#
      else if($this->section == 77){
        $this->obj =  new Limity($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Sprava Galerie#
      else if($this->section == 80 && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new Galerie($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Sprava Novinek#
      else if($this->section == 82 && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new Novinky($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Statisitky Uzivatelovych akci #
      else if(($this->section == 88 && $this->bookmaker) || ($this->section == 88 && $this->menu->getPriv($this->section,'read'))){
        $this->obj =  new StatistikyUzivatelAkce($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Zobrazeni tiketu a infa k nim#
      else if(($this->section == 90 && $this->bookmaker) || ($this->section == 90 && $this->menu->getPriv($this->section,'read'))){
        $this->obj =  new Sazky($this->section);
        $this->obj->ZobrazTicket();
        $this->obsah = $this->obj->getContent();
      }

      #Zobrazeni tiketu a infa k nim#
      else if($this->section == 91 && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new Calendar($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Statistiky global#
      else if($this->section == 94 && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new GlobalStat($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

	#Promo#
	else if(
		(
			$this->section == 98
			|| $this->section == 99
			|| $this->section == 162
			|| $this->section == 297
			|| $this->section == 298
			|| $this->section == 321
		)
		&& $this->menu->getPriv($this->section,'read')
	) {
		$this->obj = new Promo($this->section, null, $this->controller);
		$this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
		$this->obj->runAction();
		$this->obsah = $this->obj->getContent();
		$this->layout = $this->obj->getLayout();
	}

      #Prideleni free bet#
      else if($this->section == 105 && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new PridelFreeBet($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Export#
      else if($this->section == 107 && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new ExportStat($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Konstanty#
      else if($this->section == 109 && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new Konstanty($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Shoda
      else if($this->section == 121 && $this->menu->getPriv($this->section,'read')){
        $this->obj =  new ShodaUser($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Chat#
      else if($this->section == 127){
        $this->obj =  new Chat($this->section);
        $this->obsah = $this->obj->getContent();
      }

      #Pobocky#
      else if($this->section == 128){
        $this->obj =  new Pobocky($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->zobrazitPobocku();
        $this->obsah = $this->obj->getContent();
      }

      #Pobocky vytvorit novou#
      else if($this->section == 129){
        $this->obj =  new Pobocky($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->vytvoritPobocku();
        $this->obsah = $this->obj->getContent();
      }

      #Staticke stranky#

      else if ($this->section == 131) {
        $this->obj =  new StatickeStranky($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }

      #Obsah#
      else if(
        $this->section == 160
        || $this->section == 161
      ){
        $this->obj =  new Obsah($this->section);
        $this->obj->setPrivileges($this->menu->getPriv($this->section,'update'),$this->menu->getPriv($this->section,'delete'));
        $this->obj->runAction();
        $this->obsah = $this->obj->getContent();
      }
   }

   ###########################################################
   #Zde konci volani jednotlivych modulu podle sekce kde jsme#
   ###########################################################

 /*  $this->obsah .= "<div class=\"print\" style=\"clear:both\"><a href=\"javascript:self.print();void(0);\"><img src=\"".HOST."/_clip/print.gif\" alt=\"Tisk\" class=\"img\" /></a></div>";*/

   //$this->processTemplate(); //konecna faze naplneni templatu a vypsani na obrazovku

   //SesClass::close($this->db);        //uzavreni session nelze uz pridavat dalsi session //IT6: presunuto do bootstrap.php
   $this->db->disconnect();  //uzavreni spojeni s db
   $this->dbGame->disconnect();  //uzavreni spojeni s dbGame
    Zend_Registry::get('zdb_game')->closeConnection();

 }

 /**
 *Funkce zjistuje aktualni sekci a v pripade nalezeni naplni promenno section
 */
 private function findSection(){

   if(isset($_GET['section']) && is_numeric($_GET['section']))
      $this->section = $_GET['section'];

 }

 /**
 *Vlozi text do horni listy vedle loga
 *Datum, posledni akce
 */
 private function getTopText(){
  $navrat = "";

  $navrat .= '<img src="'.HOST.'_clip/clock.gif"  class="img floatleft">';
  $navrat .= date("H:i:s d.m.Y")."<br>";
  $spread = (time()-$_SESSION['starttime']);
  $hod = floor((($spread/60)/60));
  if($hod > 0) $spread -= ($hod * 3600);
  $min = floor(($spread/60));

  $navrat .= "Čas v adminu: ".($hod>0?$hod." hod.":"")." ".($min>0?$min." min.":"");

  if(isset($_SESSION['lastaction'])){
    $pole = array_reverse ($_SESSION['lastaction']);

    $navrat .= "<div id=\"lastaction\">";
    $x = 0;
    foreach($pole as $k => $h){
       if($x >3) break;
       $navrat .= "<strong>".(++$x).".</strong> ".substr(Help::Html($pole[$k]),0,48)."<br />";
    }
    $navrat .= "</div>";
  }

  return $navrat;
 }

 /**
 * metoda vraci title stranky
 * @return string
 */
  protected function getTitle(){

   $this->title = $this->menu->getName($this->section);

   $this->title .= " (".date("H:i:s d.m.Y").")";

   return $this->title;

  }

   /**
 * metoda vraci nahravaci sekvenci
 * @return string
 */
  protected function Loading(){

    return "<script> if(document.addEventListener)  window.addEventListener('load',Loading,false);
            else if(document.attachEvent)  window.attachEvent('onload',Loading);
            else window.onload = Loading();</script>
            <div id=\"load\" style=\"position:absolute;top:0px;left:863px;\"><img src=\"_clip/load.gif\" alt=\"Loading\" class=\"img\" /></div>";

  }

 /**
 *Metoda vytvari instanci tridy Main (singleton) (hahaha, pseudosingleton)
 * @return object
 */
 public static function getInstance($controller = null){
  if(!is_object(static::$instance))
     static::$instance = new Main(true, $controller);
  return static::$instance;
 }

	public function getContent() {
		return $this->obsah;
	}

	public function getError() {
		return $this->error;
	}
	
	public function getLayout() {
		return $this->layout;
	}

	public function getController() {
		return $this->controller;
	}
}
