<?php
/**
 * @package    book
 */

/**
 * Trida pro zobrazeni detailu o sazkach
 *
 *
 * <code>
 *
 * </code>
 *
 * @package    Sazky
 */

class SazkaInfo{

/**
 * navratova hodnota
 * @access private
 * @var string
 */
private  $vrat = "";



/**
 * spojeni na databazi game
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
 * pole info k tiketu stare kurzy
 * @access private
 * @var Array
 */
private  $ticket_old;

/**
 * pole info k tiketu nove kurzy
 * @access private
 * @var Array
 */
private  $ticket_new;

/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param int $section id aktualni sekce
* @param PEAR::DB $dbGame objekt spojeni s databazi
*/
  public function __construct(){

	//initiate all necessary stuff
	$dbAdmin = It6_Controller_Plugin_SetController::connectDbAdmin();

	session_set_save_handler (
	array("It6_Session_Admin", "open"),
	array("It6_Session_Admin", "close"),
	array("It6_Session_Admin", "read"),
	array("It6_Session_Admin", "write"),
	array("It6_Session_Admin", "destroy"),
	array("It6_Session_Admin", "gc")
	);
	It6_Session_Admin::start($dbAdmin);

	Zend_Registry::set('Zend_Locale', new Zend_Locale('cs_CZ'));
	$acl = It6_Acl_Factory::newAcl(array(
	'adminDb' => $dbAdmin, 'adminId' => It6_Session_Admin::getUserData('id')
	));
	Zend_Registry::set('acl', $acl);

	$this->db = DbUtil::connectAdminDb();
	$this->dbGame = DbUtil::connectWebDb();
	$acl = Zend_Registry::get('acl');
	$this->superbookmaker = $acl->userHasRole(It6_Acl_Admin::ROLE_SUPERBOOKMAKER);
	$this->bookmaker = $this->superbookmaker || $acl->userHasRole(It6_Acl_Admin::ROLE_BOOKMAKER);
	$this->superadmin = $acl->userHasRole(It6_Acl_Admin::ROLE_SUPERADMIN);
	$this->admin = $this->superadmin || $acl->userHasRole(It6_Acl_Admin::ROLE_ADMIN);
	//end of initiate all necessary stuff

    $this->db = DB::connect(DATABASE ."://". MY_USER .":". MY_PASS ."@". MY_HOST ."/". MY_DB);
    if (DB::isError($this->db)) {
      throw new ExHandler($this->db->getMessage(),"admin_ex_db");
    }
    $this->db->setFetchMode(DB_FETCHMODE_ASSOC);
    $sql = "set names 'utf8'";
    $res =& $this->db->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");

    $this->dbGame = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
    if (DB::isError($this->dbGame)) {
      throw new ExHandler($this->dbGame->getMessage(),"admin_ex_db");
    }
    $this->dbGame->setFetchMode(DB_FETCHMODE_ASSOC);
    $sql = "set names 'utf8'";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");

     session_set_save_handler (
    array("SessionBookmaker", "open"),
    array("SessionBookmaker", "close"),
    array("SessionBookmaker", "read"),
    array("SessionBookmaker", "write"),
    array("SessionBookmaker", "destroy"),
    array("SessionBookmaker", "gc"));

  }

	/**
	 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
	 * @return void
	 */
	public function runAction($nomenu=false) {
		if(!$nomenu){
			if(!$this->Iniciate()) {
				$this->vrat .= "<div class=\"errormsg\">Bookmakera se nepodařilo ověřit</div>";
				return;
			}
		}

		#editace a info o kurzech#
		if(isset($_GET['act']) && $_GET['act'] == "kurzy") {
			if(!isset($_GET['sazka']) || !isset($_GET['name']) || !is_numeric($_GET['sazka']))
				return;

			if(isset($_POST['delete']))
				$this->DeleteKurz(intval(key($_POST['delete'])));

			if(isset($_POST['send']) && isset($_POST['kurz']) && isset($_POST['datum']) && is_array($_POST['datum']) && is_array($_POST['kurz']))
				$this->EditKurz();

			$this->ShowKurzy();
		}

		#Info o sazkach hracu#
		else if(isset($_GET['act']) && $_GET['act'] == "sazky")
			$this->ShowTicket();

		#Interni chat#
		else if(isset($_GET['act']) && $_GET['act'] == "ichat" && isset($_GET['sazka'])) {
			if(isset($_POST['newchatmessage']))
				$this->CreateMessage();
			$this->InternalChat();
		}

		#Externi info k sazce#
		else if(isset($_GET['act']) && $_GET['act'] == "echat" && isset($_GET['sazka'])) {
			if(isset($_POST['newmessage']))
				$this->CreateMessageExternal();
			$this->ExternalChat();
		}

		//SesClass::close();
		It6_Session_Admin::end();
		$this->dbGame->disconnect();
		$this->db->disconnect();
	}

     /**
 * Vlozeni info ke sazce
 * @return void
 */
  private function CreateMessageExternal(){



       	 $sql = "update sazky set info='".Help::Slash($_POST['text'])."' where sazka_id=".intval($_GET['sazka'])."";
         $res =& $this->dbGame->query($sql);
         if(DB::isError($res)) { $this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: vyber sazky',"admin_ex_db");}

		 It6_GlobalCache_Invalidator::invalidateSportsbookByBet($_GET['sazka']);


  	  $this->vrat .= '<script>self.close();</script>';
  }

     /**
 * Vlozeni noveho dotazu
 * @return void
 */
  public function CreateMessage(){

  	  if(!isset($_POST['text']) || mb_strlen($_POST['text'])>400 || mb_strlen($_POST['text'])<1)$this->vrat .= "<div class=\"errormsg\">Text musí mít 1 - 400 znaků</div><br />";
  	  else{

  	   $sql = "insert into sazka_chat values(".intval($_GET['sazka']).",'".Help::Slash($_POST['text'])."',1,".$_SESSION['bookmaker'].",'".It6_Date::dbNow()."')";
       $res =& $this->dbGame->query($sql);echo $sql;
       if(DB::isError($res)) { $this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: vyber sazky',"admin_ex_db");}

  	  }

  	  $this->vrat .= '<script>self.close();</script>';
  }

     /**
 * Info k sazce
 * @return void
 */
  private function ExternalChat(){

       $sql = "select info from sazky where  sazka_id=".intval($_GET['sazka'])." ";
       $res =& $this->dbGame->query($sql);
       if(DB::isError($res)) { $this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: vyber sazky',"admin_ex_db");}
       if ($row =& $res->fetchRow());

  		$this->vrat .= '#'.intval($_GET['sazka']).' <strong>Info k sázce na stránkách</strong><br /><form method="post" action="?act=echat&sazka='.intval($_GET['sazka']).'">
                    <textarea cols="40" name="text" rows="8">'.Help::Html($row['info']).'</textarea><br />
                     <input type="submit" name="newmessage" value="Odeslat" />
                   </form>';

  }

   /**
 * Interni chat
 * @return void
 */
  public function InternalChat($nomenu=false){


  	$this->vrat .= '#'.intval($_GET['sazka']).' <strong>Nový text</strong><br />';
  	if(!$nomenu)$this->vrat .='<form method="post" action="?act=ichat&sazka='.intval($_GET['sazka']).'">';
    $this->vrat .='<textarea cols="40" name="text" rows="6"></textarea>
                     <input type="submit" name="newchatmessage" value="Odeslat" />  ';
    if(!$nomenu)$this->vrat .='</form>';

   #Vyber sloupcu a nazvu k dane sazce#
   $sql = "select bookmaker_id,nick from bookmaker";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) { $this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: vyber sazky',"admin_ex_db");}

   $book = array();

   while ($row =& $res->fetchRow()){

     $book[$row['bookmaker_id']] = $row['nick'];

   }


   $sql = "select * from sazka_chat where type=1 and sazka_id=".intval($_GET['sazka'])." order by datum desc";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) { $this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: vyber sazky',"admin_ex_db");}

   $this->vrat .= '<table style="border-collapse:collapse">';
   while ($row =& $res->fetchRow()){

   	$this->vrat .= '<tr><td style="color:black;border:0px;">'.It6_Date::fromDb($row['datum']).'</td><td style="color:black;border:0px;"> | '.Help::Html($book[$row['book_id']]).'</td></tr><tr><td colspan="2" style="color:black;border:0px;">'.nl2br($row['text']).'</td></tr>';
	$this->vrat .= '<tr><td colspan="2" style="color:black;border:0px;"><hr /></td></tr>';

   }
   $this->vrat .= '</table>';

  }

 /**
 * Zobrazi info o kurzech
 * @return void
 */
  private function ShowTicket(){

   $this->dbGame->autoCommit(false);

   $celkovy_pocet =  $celkova_castka = 0;
   $sloupce_pocet = $ticket = $sloupec = $sazka = $mena  = $ticket_sazka_sloupec = $ticket_sazka_sloupec2 = $ticket_sazka_sloupec3 = $ticket_sazka_sloupec4 = $sazka_sloupec_poradi = array();

   $preklady = new Preklady();

   #Vyber sloupcu a nazvu k dane sazce#
   $sql = "select sloupec_id,nazev from sazka_pohled where sazka_id=".intval($_GET['sazka'])." group by sloupec_id";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) { $this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: vyber sazky',"admin_ex_db");}


   while ($row =& $res->fetchRow()){


	  $res2 = $preklady->FindPreklad($row['nazev'],1);
	  if($res2[1]  == "Translation not found") $res2[1] = $row['nazev'];
	  $sloupec[$row['sloupec_id']] = $res2[1];

	}


   #Kolikrat bylo celkem na tuto sazku vsazeno resp. na kolika tiketech se nachazi#
   $sql = "select COUNT(*) AS celkovy_pocet from  ticket_pohled  a where sazka_id=".intval($_GET['sazka'])." group by a.sazka_id";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) { $this->dbGame->autoCommit(true);throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber z pohledu  sazka_pohled',"admin_ex_db");}

   if($row = $res->fetchRow()) $celkovy_pocet = $row['celkovy_pocet'];


   #Kolikrat bylo vsazeno na jednotlive sloupce#
   $sql = "select sloupec_id,COUNT(*) AS sloupce_pocet from  ticket_pohled  a where sazka_id=".intval($_GET['sazka'])." group by a.sloupec_id";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) { $this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: vyber z pohledu  sazka_pohled',"admin_ex_db");}

   while($row = $res->fetchRow()){

    $sloupce_pocet[$row['sloupec_id']] = $row['sloupce_pocet'];

   }

   #Vyberu vsechny tickety na nichz je dana sazka#
   $sql = "select ticket_id,sloupec_id from ticket_pohled where sazka_id=".intval($_GET['sazka'])." group by ticket_id" ;
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) { $this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: vyber z pohledu  ticket_pohled',"admin_ex_db");}

   while($row = $res->fetchRow()){

     $ticket[] = $row['ticket_id'];
     $ticket_sazka_sloupec[$row['ticket_id']] = $row['sloupec_id'];

   }

   if(count($ticket) > 0){

	$sql = "SELECT g.id_mena,g.kurz FROM kurz f INNER JOIN kurz_mena g ON f.id_kurz = g.id_kurz WHERE f.platny_od <= now( ) AND f.platny_do > now()";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) { $this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky mena',"admin_ex_db");}

    while($row = $res->fetchRow()){

	 $mena[$row['id_mena']] = $row['kurz'];

    }

    #jaky ma podil z castky vsazeneho na ticketu 1 sazka#
    $sql = "SELECT  a.ticket_id,b.mena_id,castka,(castka/count(*)) AS podil FROM ticket_pohled a inner join uzivatel b on a.user_id=b.user_id where a.ticket_id in(".implode(",",$ticket).") group by a.ticket_id";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) { $this->dbGame->autoCommit(true);throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber z pohledu  sazka_pohled',"admin_ex_db");}


    while($row = $res->fetchRow()){

	  if(!isset($mena[$row['mena_id']])) throw new ExHandler('Neni definovan kurz na EUR',"admin_ex_page");

	  if($celkova_castka == 0) $celkova_castka = ($row['podil']/$mena[$row['mena_id']]);
      else $celkova_castka += ($row['podil']/$mena[$row['mena_id']]);

	  if(!isset($ticket_sazka_sloupec2[$ticket_sazka_sloupec[$row['ticket_id']]])) $ticket_sazka_sloupec2[$ticket_sazka_sloupec[$row['ticket_id']]] = ($row['podil']/$mena[$row['mena_id']]);
	  else $ticket_sazka_sloupec2[$ticket_sazka_sloupec[$row['ticket_id']]] += ($row['podil']/$mena[$row['mena_id']]);

	  if(!isset($ticket_sazka_sloupec3[$ticket_sazka_sloupec[$row['ticket_id']]])) $ticket_sazka_sloupec3[$ticket_sazka_sloupec[$row['ticket_id']]] = ($row['castka']/$mena[$row['mena_id']]);
	  else $ticket_sazka_sloupec3[$ticket_sazka_sloupec[$row['ticket_id']]] += ($row['castka']/$mena[$row['mena_id']]);

    }

   }

   #Vyberu vsechny tickety na nichz je dana sazka#
   $sql = "select a.ticket_id,a.castka,a.kurz,a.sloupec_id,a.sazka_id,c.mena_id from ticket_pohled a inner join uzivatel c on a.user_id=c.user_id where a.ticket_id in (select b.ticket_id from ticket_pohled b where b.sazka_id=".intval($_GET['sazka']).") " ;
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) { $this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: vyber z pohledu  ticket_pohled',"admin_ex_db");}

   $ticket_kurz = array();
   while($row = $res->fetchRow()){

     if(!isset($ticket_kurz[$row['ticket_id']])){

           $ticket_kurz[$row['ticket_id']]['castka'] = $row['castka']/$mena[$row['mena_id']];
           $ticket_kurz[$row['ticket_id']]['rate_sum'] = 0;
           $ticket_kurz[$row['ticket_id']]['bet_rate'] = 0;

     }

     if($row['sazka_id'] == intval($_GET['sazka']))$ticket_kurz[$row['ticket_id']]['bet_rate'] = $row['kurz'];


     $ticket_kurz[$row['ticket_id']]['rate_sum'] += $row['kurz'];

   }

   foreach($ticket as $t_id){

     $pr_pomer = ($ticket_kurz[$t_id]['bet_rate']/$ticket_kurz[$t_id]['rate_sum'])*100;

     $castka_pomer  = ($pr_pomer/100)*$ticket_kurz[$t_id]['castka'];

     $ticket_sazka_sloupec4[$ticket_sazka_sloupec[$t_id]] = $castka_pomer;

   }


   #vyber sloupcu a poradi na ktere je vsazeno#
   $sql = "SELECT ticket_id, sazka_id,sloupec_id, MAX( poradi ) AS max_poradi from ticket_pohled where sazka_id=".intval($_GET['sazka'])." and zalozen >= platny_od GROUP BY sazka_id, ticket_id";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) { $this->dbGame->autoCommit(true);throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber z pohledu ticket_pohled',"admin_ex_db");}

   while($row = $res->fetchRow()){

    if(!isset($sazka_sloupec_poradi[$row['max_poradi']][$row['sloupec_id']]))
	  $sazka_sloupec_poradi[$row['max_poradi']][$row['sloupec_id']] = 1;
	else
      $sazka_sloupec_poradi[$row['max_poradi']][$row['sloupec_id']]++;

   }

   #vyber vsech kurzu vypsanych na danou sazku#
   $sql = "select * from sazka_pohled where sazka_id=".intval($_GET['sazka'])." order by platny_od";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) { $this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: vyber sazky',"admin_ex_db");}

   while ($row =& $res->fetchRow()){

	$sazka[$row['poradi']]['platny_od'] = $row['platny_od'];
    $sazka[$row['poradi']]['sloupec'][$row['sloupec_id']] = $row['kurz'];

   }

   ##########################

   $this->vrat .= '#'.intval($_GET['sazka']).' '.$_GET['name']."<br><br />";
   $this->vrat .= '<strong>Celkem se na tuto sázko vsadilo:</strong> <span class="blue">'.$celkovy_pocet.' x</span><br /><br />';

   $this->vrat .= '<br /><h3>Vsazeno v jednotlivych měnách: (výpočet dělením částky vsazené na tiket a počtu sázek na tiketu)</h3><br />';

   $this->vrat .= '<em>Vsazeno na sloupce:</em><br />';

   foreach($sloupec as $k=>$h){

	 $this->vrat .= '<strong>'.$h.'</strong>: '.(isset($sloupce_pocet[$k])?$sloupce_pocet[$k]:0).' x <br />';

	 $this->vrat .= '&nbsp;&nbsp;&nbsp;&nbsp;<em>EUR</em>: '.(isset($ticket_sazka_sloupec2[$k])?round($ticket_sazka_sloupec2[$k],2):0).'  (Castka/pocet sazek)<br />';
	 $this->vrat .= '&nbsp;&nbsp;&nbsp;&nbsp;<em>EUR</em>: '.(isset($ticket_sazka_sloupec3[$k])?round($ticket_sazka_sloupec3[$k],2):0).'  (Castka na cely tiket)<br />';
	 $this->vrat .= '&nbsp;&nbsp;&nbsp;&nbsp;<em>EUR</em>: '.(isset($ticket_sazka_sloupec4[$k])?round($ticket_sazka_sloupec4[$k],2):0).'  (Castka pomer podil kurzu)<br />';


   }

   $this->vrat .= '<br /><em>Celkem:</em><br />';

   $this->vrat .= '<strong>EUR</strong>: '.(isset($celkova_castka)?round($celkova_castka,2):0).'  <br />';



   $this->vrat .= '<br /><h3>Počet sázek na vypsané kurzy a sloupce</h3><br />';

   $this->vrat .= '<table border="1"><tr><th>&nbsp;</th><th>Platný od</th>';

   foreach($sloupec as $k=>$h){

	$this->vrat .= '<th>'.$h.'</th>';

   }

   $this->vrat .= '</tr>';

   foreach($sazka as $k=>$h){  //$k poradi

	$this->vrat .= '<tr><td>#SP'.$k.'</td><td>'.It6_Date::fromDb($h['platny_od']).'</td>';

    foreach($sloupec as $k2=>$h2){   //$k2 sloupec_id

	  $this->vrat .= '<td>'.$h['sloupec'][$k2].' '.(isset($sazka_sloupec_poradi[$k][$k2])?"<span class=\"red\">(".$sazka_sloupec_poradi[$k][$k2]." x)</span>":"").'</td>';

	 }

	 $this->vrat .= '</tr>';

   }

   $this->vrat .= '</table>';

   $this->dbGame->autoCommit(true);
   $this->dbGame->commit();

  }

 /**
 * Edituje kurz
 * @return void
 */
  private function EditKurz(){

   $data = array();
   $status = true;

   $sql = "select bookmaker_id,sloupec_id,poradi,platna_od,platna_do,podtyp_id,typ_id,udalost_id,overena,status,platny_od from sazka_pohled where sazka_id=".intval($_GET['sazka']);
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z pohledu  sazka_pohled',"admin_ex_db");

   while($row = $res->fetchRow()){

    $data['bookmaker'] = $row['bookmaker_id'];
	$data['overena'] = $row['overena'];
	$data['status'] = $row['status'];
	$data['platna_od'] = It6_Date::fromDb($row['platna_od']);
	$data['platna_do'] = It6_Date::fromDb($row['platna_do']);
    $data['sloupec'][$row['sloupec_id']] = 1;
	$data['poradi'][$row['poradi']] = It6_Date::fromDb($row['platny_od']);
	$data['podtyp'] = $row['podtyp_id'];
	$data['typ'] = $row['typ_id'];
	$data['udalost'] = $row['udalost_id'];

   }

   #kontrola jestli ma pravo menit#
   if($data['bookmaker'] ==  $_SESSION['bookmaker'] || $_SESSION['superbookmaker'] == 1); else{

	$this->vrat .= "<div class=\"errormsg\">Nemáte právo editovat kurz</div><br />";$status = false;

   }
   #kontrola zda neni overena#
   if($data['overena'] != 0 || $data['status'] == 1){$this->vrat .= "<div class=\"errormsg\">Změnit lze pouze neověřenou a nezrušenou sázku</div><br />";$status = false;}

   if($status){

	$this->dbGame->autoCommit(false);

    $sth = $this->dbGame->prepare('update sazka_kurz set kurz=?,platny_od=? where poradi=? and sazka_id=? and sloupec_id=?');
    if (PEAR::isError($sth))  {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler($sth->getMessage(),"admin_ex_db");}

	#MAX a MIN kurzu a vyhernosti#
	$sql = "select kurz_min,kurz_max,vyhernost_min,vyhernost_max from bet_settings where udalost_id=".intval($data['udalost'])." and typ_id=".intval($data['typ'])." and  podtyp_id=".intval($data['podtyp']);
    $res22 =& $this->dbGame->query($sql);
	if(DB::isError($res22)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: vyber podtypu',"admin_ex_db");}
    if($row22 =& $res22->fetchRow()){$min_kurz = $row22['kurz_min'];$max_kurz = $row22['kurz_max'];$min_vyhernost = $row22['vyhernost_min'];$max_vyhernost = $row22['vyhernost_max'];}else {$this->vrat .= "<div class=\"errormsg\"> Sázka <strong>".intval($_GET['sazka'])."</strong> -> Nebyl nalezen min. a max kurz pro daný podtyp</div><br />";$status = false;}


	$this->UpdateLimit(intval($_GET['sazka']),0);

	//echo "<pre>";print_r($_POST['kurz']);
    foreach($_POST['kurz'] as $k=>$h){ //$k poradi

	  if(!isset($data['poradi'][$k])) $status = false;
	  #kontrola format data#
	  if(!isset($_POST['datum'][$k]) || !It6_Date::checkFormat($_POST['datum'][$k])){$this->vrat .= "<div class=\"errormsg\">Identifikátor #SP".$k.": špatný formát data</div><br />";$status = false;}
	  if($status && $k == 1) $_POST['datum'][$k] = $data['poradi'][$k];
	  #kontrola zda datum neni mensi nebo rovno pocatecnimu datu#
	  if($k != 1 && It6_Date::toTimestamp($_POST['datum'][$k]) <= It6_Date::toTimestamp($data['platna_od'])){$this->vrat .= "<div class=\"errormsg\">Identifikátor #SP".$k.": datum musí být větší nežli datum prvního kurzu</div><br />";$status = false;}
	  #kontrola zda datum neni vetsi nebo rovno konecnemu datu#
	  if($k != 1 && It6_Date::toTimestamp($_POST['datum'][$k]) >= It6_Date::toTimestamp($data['platna_do'])){$this->vrat .= "<div class=\"errormsg\">Identifikátor #SP".$k.": datum musí být menší nežli datum ukončení sázky (".$data['platna_do'].")</div><br />";$status = false;}


	  $kurzy_sum = 100000000;$vyhernost_num = 0;

      foreach($h as $k2=>$h2){  //$k2 sloupec id, $h2 kurz

		#kontorla formatu kurzu#
		if(mb_strlen($h2) < 1 || !is_numeric($h2)){$this->vrat .= "<div class=\"errormsg\"> Identifikátor #SP".$k.": kurz má špatný formát</div>";$status = false;break;}
		if(($h2 < $min_kurz || $h2 > $max_kurz) && $h2 != 1) {$this->vrat .= "<div class=\"errormsg\"> Kurz ".$h2." nespl�?uje pravidlo max. amin kurzu u této sázky. MIN: ".$min_kurz." MAX: ".$max_kurz."</div>";$status = false;continue;}
		$vyhernost_num += (1/$h2);

		if(!isset($data['sloupec'][$k2])) $status = false;
	    $kurzy_sum = ($kurzy_sum > $h2?$h2:$kurzy_sum);

		if($status){

	      settype($h2,"float");
	     $res =& $this->dbGame->execute($sth,array($h2,It6_Date::toDb($_POST['datum'][$k]),$k,intval($_GET['sazka']),$k2));
         if (PEAR::isError($res))  {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler($res->getMessage(),"admin_ex_db");}

	    }

	  }


	  $vyhernost_num = round($vyhernost_num,2);

	  if(($vyhernost_num/count($h)) != 1 && ($vyhernost_num < $min_vyhernost || $vyhernost_num > $max_vyhernost)){$this->vrat .= "<div class=\"errormsg\"> Výhernost je vyšší nebo nižší nežli povolená; MIN výhernost: ".$min_vyhernost." MAX výhernost: ".$max_vyhernost."</div><br />";$status = false;}

	  #kontrola zda jsou kurzy sprave vypsany#
	  if($status && ($kurzy_sum*10) >= (count($data['sloupec'])*10)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);$this->vrat .= "<div class=\"errormsg\"> Identifikátor #SP".$k.": kurzy jsou vypsány chybně, při vsazení na všechny možnosti dojde k výhře.</div>";$status = false;}
	  else if(!$status){
	   $this->dbGame->rollback();
	  }
    }

   }

   #Aktualizace limitu - je potreba vsechny tikety na kterych je sazka znovu spocitat limity#

   if($status){

    $this->UpdateLimit(intval($_GET['sazka']),1);

	$this->RunUpdateLimit();



		$this->vrat .= "<div class=\"okmsg\">Identifikátor #SP".$k.": editace proběhla v pořádku</div><br />";

		$this->dbGame->commit();
		It6_Log::info(
			"Identifier #SP '%key%' (sázka #'%sazkaNum%'): date and rate updated.",
			It6_Log::TAG_ADMIN_OPERATION,
			array(
				'type'		=> intval($podtyp_id),
				'sazkaNum'	=> intval($_GET['sazka'])
			)
		);

		Zend_Registry::get('ws')->Alert->assert('RateChange',
			 	array('oddsId' => $podtyp_id, 'betId' => intval($_GET['sazka']), 'bookmaker' => $_SESSION['bookmaker']));

   }

   $this->dbGame->autoCommit(true);



  }

  /**
 * Provede aktualizace limitu
 * @return void
 */
  public function RunUpdateLimit($sazka_id,$iden=NULL){

	//echo "<pre>";print_r($this->ticket_new);
	if(is_array($this->ticket_old) && is_array($this->ticket_new)){

	 foreach($this->ticket_new as $k=>$h){

	   if(isset($this->ticket_old[$k]))
         self::ProcedureLimitChangeRate($this->ticket_old[$k],$this->ticket_new[$k],$this->dbGame);
	   else{

	     $this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se najit tiket',"admin_ex_page");

	   }

	 }

	}else{

		$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se najit tiket',"admin_ex_page");

	}

  }

	/**
	 * Metoda zauctuje limity uzivatelu pri zmene kurzu u sazky
	 * @param array $ticket_old  tento tiket
	 * @param array $ticket_new  obsahuje info o tiketu
	 * @param object $db  sesison databaze
	 */
	final public static function ProcedureLimitChangeRate($ticket_old, $ticket_new, $db) {
		$l = new Limit($db);
		$sport_new = $castka_sport = $sport_old = array();

		if($ticket_old['system'] != 0) {
			$vyhra_old = $ticket_old['system_win'];
		}
		else {
			$vyhra_old = ($ticket_old['castka'] * $ticket_old['kurz_celkem']);
		}
		$vyhra_old = round($vyhra_old, 2);
		if($ticket_new['system'] != 0) {
			$vyhra_new = $ticket_new['system_win'];
		}
		else {
			$vyhra_new = ($ticket_new['castka'] * $ticket_new['kurz_celkem']);
		}
		$vyhra_new  = round($vyhra_new, 2);
		$pocet_sazek = $ticket_old['pocet_sazek'];
		$castka_sazka = ($ticket_old['castka']/$pocet_sazek);
	
		foreach($ticket_old['sport'] as $k=>$h) {
			$castka_sport[$k] =  ($h['pocet']*$castka_sazka);
			$procento = (($ticket_old['sport'][$k]['rate']/$ticket_old['kurz_sum'])*100);
			$sport_old[$k] = (($procento/100)*$vyhra_old);
			$procento = (($ticket_new['sport'][$k]['rate']/$ticket_new['kurz_sum'])*100);
			$sport_new[$k] = (($procento/100)*$vyhra_new);
		}
	
		foreach($ticket_old['sport'] as $k=>$h) {
			$l->betClose($k,$ticket_old['user_id'],0,($sport_old[$k]-$castka_sport[$k]),$ticket_old['mena_id']);
			$l->betStart($k,$ticket_old['user_id'],($sport_new[$k]-$castka_sport[$k]),$ticket_old['mena_id']);
		}
	}
  
   /**
 * Vymaze u dane sazky jeden vypsany kurz
 * @param int $sazka_id id sazky který chceme rusit
 * @param int $iden  0 = stare kurzy 1= nove kurzy rozlisuje tikety pred a po editaci kurzu
 * @return void
 */
  public function UpdateLimit($sazka_id,$iden=8){
   global $systemAr;


   if($iden != 8){

     #vyber tikety#
     $sql = "select a.ticket_id,a.sazka_id,a.kurz,a.user_id,a.castka,a.system,a.banker,a.win,b.mena_id,e.sport_id from ticket_pohled a inner join uzivatel b on a.user_id=b.user_id  inner join udalost e on e.udalost_id=a.udalost_id where a.ticket_id in (select c.ticket_id from ticket_pohled c where c.vyplacen=0 and c.sazka_id=".$sazka_id.")";
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber ticket_pohled',"admin_ex_db");}


	 $ticket = array();

	 while ($row =& $res->fetchRow()){

       if(!isset($ticket[$row['ticket_id']])){

	     $ticket[$row['ticket_id']]['user_id'] = $row['user_id'];
	     $ticket[$row['ticket_id']]['mena_id'] = $row['mena_id'];
	     $ticket[$row['ticket_id']]['castka'] = $row['castka'];
	     $ticket[$row['ticket_id']]['kurz_celkem'] = 1;
	     $ticket[$row['ticket_id']]['kurz_sum'] = 0;  //soucet kurzu kvuli limitum
	   	 $ticket[$row['ticket_id']]['pocet_sazek'] = 0;
	   	 $ticket[$row['ticket_id']]['pocet_sazek'] = 0;
		 $ticket[$row['ticket_id']]['system'] = $row['system'];
		 $ticket[$row['ticket_id']]['system_win'] = 0;
		 $ticket[$row['ticket_id']]['banker_rate'] = 1;
		 $ticket[$row['ticket_id']]['system_ar'] = array();

	   }

	   $ticket[$row['ticket_id']]['kurz_celkem'] *= $row['kurz'];
	   $ticket[$row['ticket_id']]['kurz_sum'] += $row['kurz'];
	   $ticket[$row['ticket_id']]['pocet_sazek']++;

	   #urceni postu sportu na tiketu#
	   if(!isset($ticket[$row['ticket_id']]['sport'][$row['sport_id']])){
	     $ticket[$row['ticket_id']]['sport'][$row['sport_id']]['rate'] = $row['kurz'];
	     $ticket[$row['ticket_id']]['sport'][$row['sport_id']]['pocet'] = 1;
	   }else{
	     $ticket[$row['ticket_id']]['sport'][$row['sport_id']]['rate'] += $row['kurz'];
	     $ticket[$row['ticket_id']]['sport'][$row['sport_id']]['pocet']++;
	   }

	    if($row['system'] != 0){

     	if($row['banker'] == 1){
	 	 $ticket[$row['ticket_id']]['banker_rate'] = $ticket[$row['ticket_id']]['banker_rate'] * $row['kurz'];
	    }else{
	     $klic = count($ticket[$row['ticket_id']]['system_ar']);
      	 $ticket[$row['ticket_id']]['system_ar'][$klic]['sazka_id'] = $row['sazka_id'];
      	 $ticket[$row['ticket_id']]['system_ar'][$klic]['rate'] = $row['kurz'];
	    }

       }

     }

     foreach($ticket as $k=>$h){

   	  if($h['system'] != 0){

	   $castka_rad = ($h['castka']/$systemAr[count($ticket[$k]['system_ar'])][$h['system']]);
	   $GLOBALS['system_special_ar'] = Array();

	   $ticket[$k]['system_win'] =  Help::ReQSystem(0,$h['system'],0,$ticket[$k]['system_ar'],$h['banker_rate'],1,$castka_rad);

	  }

     }

	 if($iden == 0){

	   $this->ticket_old = $ticket;

	 }
	 else if($iden == 1){

	   $this->ticket_new = $ticket;

	 }


   }


  }

 /**
 * Vymaze u dane sazky jeden vypsany kurz
 * @param int $kurz_id id kurzu který chceme rusit
 * @return void
 */
  private function DeleteKurz($kurz_id){

   $status = true;

   $sql = "select bookmaker_id,overena from sazky where sazka_id=".intval($_GET['sazka']);
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky  sazky',"admin_ex_db");

   if(($row = $res->fetchRow()) && ($row['bookmaker_id'] ==  $_SESSION['bookmaker'] || $_SESSION['superbookmaker'] == 1)); else{

	$this->vrat .= "<div class=\"errormsg\">Nemáte právo vymazat kurz</div><br />";$status = false;

   }

   if($kurz_id == 1){$this->vrat .= "<div class=\"errormsg\">První vypsaný kurz nelze smazat</div><br />";$status = false;}

   if($row['overena'] != 0){$this->vrat .= "<div class=\"errormsg\">Kurzy lze mazat dokud sázka není ověřena</div><br />";$status = false;}


   if($status){

   	$this->UpdateLimit(intval($_GET['sazka']),0);

    $sql = "delete from sazka_kurz where poradi=".$kurz_id." and sazka_id=".intval($_GET['sazka']);
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: zruseni  sazky',"admin_ex_db");

	if($this->dbGame->affectedRows()){

      $this->vrat .= "<div class=\"okmsg\">Kurz byl vymazán</div><br />";

		It6_Log::info(
			"Rate deleted (bet id:'%bet%', rate: '%rate%').",
			It6_Log::TAG_ADMIN_OPERATION,
			array(
				'bet'	=> intval($_GET['sazka']),
				'rate'	=> $kurz_id
			)
		);
	}else
	     $this->vrat .= "<div class=\"errormsg\">Kurz se nepodařilo smazat</div><br />";

	 $this->UpdateLimit(intval($_GET['sazka']),1);

   }

  }

   /**
 * Zobrazi vsechny vypsane kurzy na danou sázku
 * @return void
 */
  private function ShowKurzy(){

   $preklady = new Preklady();

   $sql = "select * from sazka_pohled where sazka_id=".intval($_GET['sazka'])." order by platny_od";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) {throw new ExHandler('Nepodarilo se provest dotaz: vyber sazky',"admin_ex_db");}

   $sazka = $sloupec = $pred = array();

   $this->vrat .= '#'.intval($_GET['sazka']).' '.$_GET['name'];

   while ($row =& $res->fetchRow()){

    $sazka[$row['poradi']]['platny_od'] =  $row['platny_od'];
	$sazka[$row['poradi']]['platna_od'] =  $row['platna_od'];
	$sazka[$row['poradi']]['platna_do'] =  $row['platna_do'];
    $sazka[$row['poradi']]['sloupec'][$row['sloupec_id']] = $row['kurz'];

	if(!isset($sloupec[$row['sloupec_id']])){

	  $res2 = $preklady->FindPreklad($row['nazev'],1);
	  if($res2[1]  == "Translation not found") $res2[1] = $row['nazev'];
	  $sloupec[$row['sloupec_id']] = $res2[1];

	}

   }

   $this->vrat .= '<form method="post" action="?act=kurzy&sazka='.intval($_GET['sazka']).'&name='.urlencode($_GET['name']).'"><table border="1">';

   $this->vrat .= '<tr><th>Identifikátor</th><th>Platný od</th>';
   ksort($sloupec);
   foreach($sloupec as $h) $this->vrat .= '<th>'.$h.'</th>';
   //$this->vrat .= '<th>&nbsp;</th><th>&nbsp;</th>';
   $this->vrat .= '</tr>';

   foreach($sazka as $k=>$h){

	 $javascript = "[";

     $this->vrat .= '<tr><th>#SP'.$k.'</th><th id="'.$k.'_datum_od">'.It6_Date::fromDb($h['platny_od']).'</th>';
     $javascript .= "'".$k."_datum_od',";

	 ksort($h['sloupec']);
	 foreach($h['sloupec'] as $k2=>$h2){

	   if(!isset($pred[$k2])) $pred[$k2] = $h2;

	   if($pred[$k2] > $h2)         $img = '<img src="_clip/down.gif" alt="Růst" class="img" />';
	   else if($pred[$k2] < $h2)    $img = '<img src="_clip/up.gif" alt="Pokles" class="img" />';
	   else if($pred[$k2] == $h2)      $img = '<strong>-</strong>';

	   $this->vrat .= '<td id="'.$k.$k2.'_kurz">'.$h2.' '.$img.'</td>';
	   $javascript .= "'".$k.$k2."_kurz',";

	   $pred[$k2] = $h2;

	 }

	 $javascript = substr($javascript,0,-1); $javascript .= "]";

	/* $this->vrat .= '<td><input type="submit" name="delete['.$k.']" onclick="if(!confirm(\'Opravdu chcete vymzat tento kurz?\'))return false;" value="Vymazat"></td>
	 <td id="submit_'.$k.'"><input type="button" onmouseup="EditKurz('.$javascript.',this);"  value="Edit" /></td>';
	 * */
	 $this->vrat .= '</tr>';

   }

   $this->vrat .= '</table></form>';
   $this->vrat .= '
   <script language="Javascript" type="text/javascript">
    function EditKurz(pole,t){

	 var poradi;
	 var sloupec = "";
	 var x;
	 var ob;
	 var help;

	 for(x=0;x<pole.length;x++){

	   sloupec = ""
	   ob = new getObj(pole[x]);
	   hodnota = ob.obj.innerHTML;

	  if(x == 0){

		poradi = pole[x].charAt(0);
	    ob.obj.innerHTML = \'<input type="text" value="\'+hodnota+\'" name="datum[\'+poradi+\']" />\';

	  }else{

		hodnota = parseFloat(hodnota);
		poradi = pole[x].charAt(0);
		help = parseInt(pole[x]).toString();
		for(var i=1;i<help.length;i++)
		 sloupec += help.charAt(i);
		ob.obj.innerHTML = \'<input type="text" value="\'+hodnota+\'" style="width:35px;" name="kurz[\'+poradi+\'][\'+sloupec+\']" />\';

	  }

	 }

	 ob = new getObj(\'submit_\'+poradi);
	 ob.obj.innerHTML = \'<input type="submit" value="Edituj vše!" name="send" />\';

	}
   </script>';
   $this->vrat .= '<br /><br />';

   $this->GrafKurzy($sazka,$sloupec,intval($_GET['sazka']));

  }

   /**
 * Priprava pro grafy
 *
 * @param array $sazka pole kurzu pro danou sazku
 * @param array $sloupec pole nazvu sloupcu
 * @param int $sazka_id id sazky
 * @return void
 */
 private function GrafKurzy($sazka,$sloupec,$sazka_id){

    include ("jpgraph/src/jpgraph.php");
    include ("jpgraph/src/jpgraph_line.php");
    include ("jpgraph/src/jpgraph_bar.php");

	$this->KurzyTime($sazka,$sloupec,$sazka_id);

	$this->vrat .= '<img src="_graph/sazka_kurzy_'.$sazka_id.'.png" class="img view" />';

 }

    /**
 * Graf kurzu v case
 *
 * @param array $sazka pole kurzu pro danou sazku
 * @param array $sloupec pole nazvu sloupcu
 * @param int $sazka_id id sazky
 * @return bool
 */
 private function KurzyTime($sazka,$sloupec,$sazka_id){


   $datax = $datay = $p = array();

   foreach($sazka as $h){

     $datax[] = It6_Date::fromDb($h['platny_od']);

	 ksort($h['sloupec']);
	 foreach($h['sloupec'] as $k2=>$h2){

	   $datay[$k2][] = $h2;

	 }


   }

   $datax[] = It6_Date::fromDb($sazka[1]['platna_do']);

   $graph = new Graph(930,680,"auto");
   $graph->img->SetMargin(50,130,30,130);
   $graph->SetBackgroundImage(ROOT."admin/www/_clip/loglogo.jpg",BGIMG_CENTER);

   // Adjust brightness and contrast for background image
   // must be between -1 <= x <= 1, (0,0)=original image
   $graph->AdjBackgroundImage(0,0);

   //$graph->img->SetAntiAliasing("white");
   $graph->SetScale("textlin");
   $graph->SetShadow();
   $graph->title->Set(mb_convert_encoding("Kurzy #".$sazka_id,"ISO-8859-2"));

   // Use built in font
   $graph->title->SetFont(FF_FONT1,FS_BOLD,9);

   $graph->xaxis->SetTickLabels($datax);
   $graph->xaxis->SetLabelAngle(90);
   $graph->xaxis->SetFont(FF_FONT1,FS_NORMAL,9);
   $graph->xaxis->SetColor('darkblue','black');
   $graph->yaxis->SetFont(FF_FONT1,FS_NORMAL,9);
   // Slightly adjust the legend from it's default position in the
   // top right corner.
   $graph->legend->Pos(0.09,0.00,"right","top");
   $graph->legend->SetFont(FF_FONT1,FS_NORMAL,7);

   ksort($datay);
   foreach($datay as $k=>$h){

	$h[] = $h[(count($h)-1)];

	$key = count($p);
	$color = "#".dechex(rand(0,256)).dechex(rand(0,256)).dechex(rand(0,256));
	 //"#".dechex(random(256)).dechex(random(256)).dechex(random(256));
     // Create the first line
    $p[$key] = new LinePlot($h);
    $p[$key]->mark->SetType(MARK_FILLEDCIRCLE);
    $p[$key]->mark->SetFillColor($color);
    $p[$key]->mark->SetWidth(3);
    $p[$key]->SetColor($color);
    $p[$key]->SetCenter();
    $p[$key]->SetLegend(mb_convert_encoding($sloupec[$k],"ISO-8859-2"));
	$p[$key]->value->SetFormat('%01.2f');
    $p[$key]->value->Show();
    $graph->Add($p[$key]);

   }


    // Output line
    $graph->Stroke(ROOT."admin/www/_graph/sazka_kurzy_".$sazka_id.".png");

 }

 /**
 * Kontrola session, vraci false pri neuspechu
 * @return bool
 */
  private function Iniciate(){

	//if(!SesClass::open($this->db)) throw new ExHandler('Nepodarilo se inicializovat Session',"admin_ex_db");
	if(!It6_Session_Admin::start($this->db)) throw new ExHandler('Nepodarilo se inicializovat Session',"admin_ex_db");



	//if(SesClass::getUserStatus() == 2 || SesClass::getUserStatus() == 3)
	// return true;
	//else
	// return false;
	return It6_Session_Admin::isAuthenticated();

  }

  /**
 * Vraci vystup do tridy main
 * @return string
 */
  public function getContent(){

    return $this->vrat;

  }

  public function __destruct(){


  }

}

?>
