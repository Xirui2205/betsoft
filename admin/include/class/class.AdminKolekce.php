<?php
/**
 * @package    ciselnik
 */

//TODO:ADMIN cela trida


class AdminKolekce{

private  $db;                      //objekt databaze
private  $vrat = "";               //navratova hodnota z tridy
private  $section;                 //aktualni sekce
private  $admin;                   //objekt administrator
private  $menuObj;                 //objekt menu
private  $menu = array();          //pole menu
private  $sekce_id = array();          //pole vsech sekci
private  $vratMenu = "";           //pomocna promenna pro praci s menu
private  $javascript = "";         // string s javascriptovym codem

  public function __construct($section=0){

   $this->section = $section;
	
   /*
   $this->db = DB::connect(DATABASE ."://". MY_USER .":". MY_PASS ."@". MY_HOST ."/". MY_DB);
   if (DB::isError($this->db)) {
     throw new ExHandler($this->db->getMessage(),"admin_ex_db");
   }
   $this->db->setFetchMode(DB_FETCHMODE_ASSOC);
   $sql = "set names 'utf8'";
   $res =& $this->db->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");
   */
	
  }
  
  /*
  Metoda provede ruzne akce pro praci  s admnini
  */
  public function runAction(){

   if($_SESSION['superadmin']){ //pouze superadmin muze vstoupit a pracovat v teto sekci

	 #Vytvoreni noveho admina#
	 if(isset($_POST['create'])){
	 
	   $this->createAdmin();
	  
	 }
	
	 #Vymazani admina#
	 else if(isset($_POST['submit']['delete'])){
	  
	   $this->deleteAdmin(intval(key($_POST['submit']['delete'])));
	  
	 }
	
	 #Editace vice adminu#
	 else if(isset($_POST['editall']) && isset($_POST['admin']) && is_array($_POST['admin'])){
	  
	   $this->editAdmins();
	  
	 }
	 
	 #Vypis na stranku, vyjimka kdyz jdeme na detail#
	 if(!isset($_POST['submit']['edit'])){ 
    
	   $this->showAdmins();
	
	 }
	 #Detail administratora#
	 else{
	 
	  $this->showAdminDetail(key($_POST['submit']['edit']));
	  
	 }
   
    $this->vrat .= "";
	
   }else
	    $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro vstup do této sekce</div>\n";
   
   // $this->db->disconnect();
   
  }
  
  
  /*
    detail administratora
  */
  private function showAdminDetail($adminId = false){
      
	  if(!$adminId || !is_numeric($adminId)) return;
	
	  //$this->admin = new Administrator($admin_id,$this->db);
	  $this->admin = It6_Models_Admin::readData($adminId);

	/*vyber polozek menu*/
//IT6: prirazeni prav se bude muset predelat, nyni se musi brat v uvahu dedicnost prav z roli
//      $sql = "select sekce_id,parent_id,nazev,zobrazeno from sekce order by poradi";
//      $res =& $this->db->query($sql);
//      if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky sekce',"admin_ex_db");
//	  
//      while ($row =& $res->fetchRow()){
//	   $this->menu[$row['parent_id']][$row['sekce_id']] = $row['nazev'];
//	   $this->sekce_id[] = $row['sekce_id'];
//      }
   
	  #Povoleni nebo zakazani administratora#
	if(isset($_POST['povolit'])){
		It6_Models_Admin::setAccess($this->admin['id'], true);
		$this->vrat .= "<div class=\"okmsg\">Administrátor ".Help::Html($_POST['admin']['username'])." byl úspěšně povolen</div><br />";
		It6_Log::info(
			"Admin '%nick%' allowed.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('nick' => $_POST['admin']['nick']));
	}
	else if(isset($_POST['zakazat'])){
		It6_Models_Admin::setAccess($this->admin['id'], false);
		$this->vrat .= "<div class=\"okmsg\">Administrátor ".Help::Html($_POST['admin']['username'])." byl úspěšně zakázán</div><br />";
		It6_Log::info(
			"Admin '%nick%' banned.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('nick' => $_POST['admin']['nick']));
	}
	  #Konec povoleni nebo zakazani administratora#
	  #editace administratora#
	  else if(isset($_POST['edit'])){
	   
	   $this->editAdmin($adminId);
	  
	  }
  
	#vymazani administratora#
	if (isset($_POST['delete'])) {
		if (!Zend_Registry::get('acl')->userHasRole(It6_Acl_Admin::ROLE_SUPERADMIN, $this->admin['id'])) {
			$this->deleteAdmin($admin_id);
			$this->showAdmins();
			return;
		}
		else 
			$this->vrat .= "<div class=\"errormsg\">Superadmin nemůže být vymazán</div><br />";
	}
	#Konec vymazani administratora#

	  //$this->adminMenu($admin_id,$this->admin->superadmin,1);

/*
	  $this->vrat .= "<div class=\"margin-bottom:10px;clear:both\">
	  <a href=\"javascript:show('admprofil',['admprava','admhistory']);void(0);\" class=\"rightMenu\">Profil</a>
	  <a href=\"javascript:show('admprava',['admprofil','admhistory']);void(0);\" class=\"rightMenu\">Práva</a>
	  <a href=\"javascript:show('admhistory',['admprava','admprofil']);void(0);\" class=\"rightMenu\">Historie</a></div><br /><br />";
	  $this->vrat .= "<form method=\"post\" action=\"?section=".$this->section."\"><table style=\"display:block\" id=\"admprofil\">";
*/
	$this->vrat .= '<h3>'.I18n::tr('Administrator detail ({1})',$this->admin['username']).'</h3>';
	$this->vrat .= '<ul class="idTabs" style="display:block">
						<li><a href="#profile">'.I18n::tr('Profile').'</a></li>
						<!--<li><a href="#privileges">'.I18n::tr('Privileges').'</a></li>-->
						<li><a href="#history">'.I18n::tr('History').'</a></li>
					</ul>';
	$this->vrat .= '<div id="tabs-container">';
	$this->vrat .= "<form method=\"post\" action=\"?section=".$this->section."\">";
	$this->vrat .= '<div id="profile" style="display:none;">';
	$this->vrat .= '<table class="table-detail">';
	$this->vrat .= '<tr>
					<td><label for="admin[jmeno]">Jméno:</label> </td>
					  <td><input type="text" class="classic" maxlength="50" id="admin[jmeno]" name="admin[firstName]" value="'.Help::Html($this->admin['firstName']).'" /></td>
	                  </tr><tr>
					  <td><label for="admin[prijmeni]">Přijmení:</label> </td>
					  <td><input type="text" class="classic" maxlength="60" id="admin[prijmeni]" name="admin[surname]" value="'.Help::Html($this->admin['surname']).'" /></td>
					  </tr><tr>
					  <td><label for="admin[nick]">Nick:</label> </td>
					  <td><input type="text" class="classic mandatory" maxlength="20" id="admin[nick]" name="admin[username]" value="'.Help::Html($this->admin['username']).'" /></td>
					  </tr><tr>
					  <td><label for="admin[heslo]">Heslo:</label> </td>
					  <td><input type="password" class="classic mandatory" maxlength="16" id="admin[heslo]" name="admin[passwd]" value="" /></td>
					  </tr><tr>
					  <td><label for="admin[email]">Email:</label> </td>
					  <td><input type="text" class="classic mandatory" maxlength="80" id="admin[email]" name="admin[email]" value="'.Help::Html($this->admin['email']).'" /></td>
					  </tr><tr>
					  <td><label for="admin[telefon]">Telefon:</label> </td>
					  <td><input type="text" class="classic" maxlength="80" id="admin[telefon]" name="admin[phone]" value="'.Help::Html($this->admin['phone']).'" /></td>
					  </tr>'
					  //IT6: now handled by ACL and role assignment
					  //<tr>
					  //<td><label for="admin[superadmin]">Superadmin:</label> </td>'
					  //<td> <input type="checkbox" onclick="setPravo(this.checked);" id="admin[superadmin]" name="admin[superadmin]" '.($this->admin->superadmin?"checked=\"checked\"":"").' /></td>
					  //</tr>
					  .'</tr></table>';
					  
	$this->vrat .= '</div>'; //profil
	
/*
	$this->vrat .= '<div id="privileges">';
	   $this->vrat .= "<table class=\"table-list\"><tr><th>&nbsp;</th><th>Read</th><th>Update</th><th>Delete</th></tr>".$this->vratMenu."</table>";
	
	$this->vrat .= '</div>'; //privileges
*/
	
	$this->vrat .= '<div id="history">';
	$this->vrat .= "<table>".$this->adminAkce($adminId)."</table>";
	$this->vrat .= '</div>'; //history
	
	   $this->vrat .= "<input type=\"hidden\" name=\"submit[edit][".$adminId."]\">";
	   $this->vrat .= '<div class="actions">
						<input type="submit" name="edit" class="sbutton" value="Uložit změny" />
						<input type="submit" name="delete" class="sbutton" onclick="if(!confirm(\'Opravdu chcete smazat: '.Help::Html($this->admin['username']).'?\')) return false;" value="Smazat" />
						  '.(0 != $this->admin['access']?'?<input type="submit" name="povolit" class="sbutton" value="Povolit">':'<input type="submit" name="zakazat" class="sbutton" value="Zakázat" />').'

					</div>';

	$this->vrat .= '</form>'; //history  
	
	$this->vrat .= '</div>'; //tabs-container
  }
  
  
  /*
    editace jednoho Admina
	*param1: admin_id=id adminsitrator
  */
  private function editAdmin($admin_id){
   
   
   if(isset($_POST['admin']) and is_array($_POST['admin'])){
      $db = Zend_Registry::get('zdb_admin');
	  $status = true;
	  $h = $_POST['admin'];
	  
      if((!isset($h['passwd']) || !Help::checkPass($h['passwd'])) && mb_strlen(trim($h['passwd'])) > 0) {$this->vrat .= "<div class=\"errormsg\"> <strong>Heslo</strong> má špatný formát (6-16 znaků, nesmí začínat číslem, minimálně 2 čísla)</div><br />";$status = false;}
	  if(!isset($h['username']) || !Help::checkNick($h['username']))  {$this->vrat .= "<div class=\"errormsg\">  <strong>Nick</strong> má špatný formát (4-20 znaků, nesmí začínat číslem)</div><br />";$status = false;}
	  if(!isset($h['email']) || !Help::valideMail($h['email']) || mb_strlen($h['email']) > 80)  {$this->vrat .= "<div class=\"errormsg\"><strong>Mail</strong> má špatný formát</div><br />";$status = false;}
      if(!isset($h['firstName']) || mb_strlen(trim($h['firstName']),'utf-8') > 50)  {$this->vrat .= "<div class=\"errormsg\">  <strong>Jméno</strong> špatná délka (max 50 znaků)</div><br />";$status = false;}
	  if(!isset($h['surname']) || mb_strlen(trim($h['surname']),'utf-8') > 60)  {$this->vrat .= "<div class=\"errormsg\">  <strong>Přijmení</strong> špatná délka (max 60 znaků)</div><br />";$status = false;}
	  if(!isset($h['phone']) || mb_strlen(trim($h['phone']),'utf-8') > 20)  {$this->vrat .= "<div class=\"errormsg\"> <strong>Telefon</strong> špatná délka (max 20 znaků)</div><br />";$status = false;}
	  if(isset($h['username'])){
	  
	    $sql = "select COUNT(admin_id) AS c from admin where username='".Help::slash($h['username'])."' and not admin_id=".$admin_id;
	    try {
			$res = $db->query($sql)->fetchAll();
		}
		catch (Exception $e) {
			throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani admina z databáze',"admin_ex_db");
		}
	    
		if($res[0]['c'] > 0){ $this->vrat .= "<div class=\"errormsg\">Tento \"Nick\"  (".Help::Html($h['username']).") již existuje</div><br />";$status = false;}
	  
	  }
	  
	  #vsechno je  vporadku muzeme aktualizovat#
	  if($status){

		$encrypted = It6_Models_Admin::cryptPasswd($h['passwd']);

		#update osobnich udaju#
		//$sql = "update admin set jmeno='".Help::slash($h['jmeno'])."',prijmeni='".Help::slash($h['prijmeni'])."',nick='".Help::slash($h['nick'])."',".(mb_strlen($h['heslo'])>0?"heslo='".addslashes($encrypted)."',":"")."email='".addslashes($h['email'])."',telefon='".addslashes($h['telefon'])."', superadmin=".(isset($h['superadmin'])?1:0)." where admin_id=".$admin_id;
		/*
		$sql = "update admin set first_name='".Help::slash($h['firstName'])."',surname='".Help::slash($h['surname'])."',username='".Help::slash($h['username'])."',".(mb_strlen($h['passwd'])>0?"passwd='".addslashes($encrypted)."',":"")."email='".addslashes($h['email'])."',phone='".addslashes($h['phone'])."' where admin_id=".$admin_id;
		$res =& $this->db->query($sql);
		if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vlozeni noveho administratora',"admin_ex_db");
		*/
		$data = array(
			'firstName' => $h['firstName'],
			'surname' => $h['surname'],
			'username' => $h['username'],
			'email' => $h['email'],
			'phone' => $h['phone'],
		);
		if (mb_strlen($h['passwd'])>0)
			$data['passwd'] = $encrypted;
		try {
			It6_Models_Admin::update($admin_id, $data, $db);
			$this->vrat .= "<div class=\"okmsg\">Administrátor ".Help::Html($h['username'])." byl úspěšně aktualizován</div><br />";
			It6_Log::info(
				"Admin '%nick%' updated.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('nick' => $h['username'], 'data' => Zend_Json::encode($data))
			);
		}
		catch (Exception $e) {
			throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vlozeni noveho administratora',"admin_ex_db");
		}
		
	  
	    #update prav k menu#
	    //TODO: refactor
	    /*
		$sth = $this->db->prepare('replace into prava (admin_id,sekce_id,readx,updatex,deletex) values (?,?,?,?,?)');
	if (PEAR::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");
		  
		foreach($this->sekce_id as $h){

			$read =   (isset($_POST['pravo'][$h]['read']) || isset($_POST['admin']['superadmin'])?1:0);
			$update = (isset($_POST['pravo'][$h]['update']) || isset($_POST['admin']['superadmin'])?1:0);
			$delete = (isset($_POST['pravo'][$h]['delete']) || isset($_POST['admin']['superadmin'])?1:0);
			$this->menuObj->privAll[$h]['read'][$admin_id] = (isset($_POST['pravo'][$h]['read']) || isset($_POST['admin']['superadmin'])?1:0);
			$this->menuObj->privAll[$h]['update'][$admin_id] = (isset($_POST['pravo'][$h]['update']) || isset($_POST['admin']['superadmin'])?1:0);
			$this->menuObj->privAll[$h]['delete'][$admin_id] = (isset($_POST['pravo'][$h]['delete']) || isset($_POST['admin']['superadmin'])?1:0);

			$res =& $this->db->execute($sth,array($admin_id,$h,$read,$update,$delete));
			if (PEAR::isError($res))  throw new ExHandler($res->getMessage(),"admin_ex_db");

	    }
	    */
			  
	  }
   
   }
   
  }
  
  
  /**
    *vyjede vsechny akce daneho administratora
	*
	* @param int $admin_id id administratora
    * @return string
  */
  private function adminAkce($admin_id){
  
   //$sql = "select data,time,start,status,zprava from session where admin_id=".$admin_id." order by start desc";
   //$res =& $this->db->query($sql);
   //if(DB::isError($res)) throw new ExHandler('<br>Nepodarilo se provest dotaz: vyber z tabulky session',"admin_ex_db");
	$res = Zend_Registry::get('zdb_admin')->select()
		->from('session', array('data', 'time', 'start', 'status', 'zprava'))
		->where('admin_id=?', $admin_id)
		->query();
   
   $vrat = "";
   
   $vrat .= "<table id=\"admhistory\" class=\"table-list\">";   
   
   while ($row = $res->fetch()){
   
     $row['data'] = Help::session_real_decode($row['data']);
	 
	 switch($row['status']){
	  case It6_Session_Admin::STATUS_AUTHENTICATED: $status =  "Aktivní";break;
	  case It6_Session_Admin::STATUS_JUST_AUTHENTICATED: $status =  "Právě přihlášen";break;
	  case It6_Session_Admin::STATUS_TIMEOUT: $status =  "Vypršela session";break;
	  case It6_Session_Admin::STATUS_ABORTED: $status =  "Vyhozený (".$row['zprava'].")";break;
	  default: $status =  "Nepřihlášený";
	 }
	 
     $vrat .= "<tr><td>".$row['start']."</td><td class=\"border-left\" onclick=\"displayObj(this.childNodes[1]);\" class=\"historyli\">".$status."";
     
	 $vrat .= "<ul style=\"display:none\">";
	 
	 if(isset($row['data']['lastaction'])){
	   ksort($row['data']['lastaction'],"Help::Kcmp");
	   $row['data']['lastaction'] = array_reverse($row['data']['lastaction'],true);
	   
	   foreach($row['data']['lastaction'] as $k=>$h){
	     $vrat .= "<li class=\"historyli2\">".date("H:i:s Y.m.d",$k)." ".$h."</li>";
	   }
	   
	 }else $vrat.= "<li>Žádné akce</li>";
	 
	 $vrat .= "</ul></li>

	 </td></tr>";

   }
   
   $vrat .= "</table>";
   
   return $vrat;
   
  }
  
   /*
    sestaveni menu a prav
	*param1: admin_id=id adminsitrator
	*param2: superadmin= je superadmin
	*param3: akce= akce ktera se me provest
  */
  private function adminMenu($admin_id,$superadmin,$akce = 1){
  
   $this->javascript .= "<script laguage=\"Javascript\" type=\"text/javascript\"> var pravo = new Array();\n";
   $this->builtMenu(1,'&nbsp;&nbsp;&nbsp;&nbsp;',$admin_id,$superadmin);
   $this->javascript .= "</script>";
   
  }
  
  /*
  samotne sestaveni menu
  nejvyssi sekce Home ma id = 1
  *param1 p_id: id rodice
  *param2 level: uroven ve ktere prave jsme 
  */
  private function builtMenu($p_id=1,$level='',$admin_id,$superadmin){
    
	
	if(isset($this->menu[$p_id])){
	 
	 foreach($this->menu[$p_id] as $sec_id=>$text){
	     $this->javascript .= "pravo[pravo.length] = ".$sec_id."\n";
	     $this->vratMenu .= $this->createLink($level,$sec_id,$text,$admin_id,$superadmin);
		 if(isset($this->menu[$sec_id])) $this->builtMenu($sec_id,($level.'&nbsp;&nbsp;&nbsp;&nbsp;'),$admin_id,$superadmin); //kdyz ma zvoleny prvek potomky
	   
	 }
	 
	}
	
  }

  /*vraci sestaveny odkaz*/
  private function createLink($level,$sec_id,$text,$admin_id,$superadmin){
   return sprintf('<tr onmouseover="this.style.backgroundColor=\'#909090\';" onmouseout="this.style.backgroundColor=\'\';"><td>%s<span>%s</span></td>
   <td class="textcenter"><input type="checkbox" name="pravo['.$sec_id.'][read]"   class="no" value="" %s %s /></td>
   <td class="textcenter"><input type="checkbox" name="pravo['.$sec_id.'][update]" class="no" value="" %s %s /></td>
   <td class="textcenter"><input type="checkbox" name="pravo['.$sec_id.'][delete]" class="no" value="" %s %s />
   </td></tr>',(isset($this->menu[$sec_id])?substr($level,0,(strlen($level)-6))."+ ":$level),$text,($this->menuObj->getPrivAll($sec_id,"read",$admin_id) || $superadmin?"checked=\"checked\"":""),($superadmin?"readonly=\"readonly\"":""),
   ($this->menuObj->getPrivAll($sec_id,"update",$admin_id) || $superadmin?"checked=\"checked\"":""),($superadmin?"readonly=\"readonly\"":""),($this->menuObj->getPrivAll($sec_id,"delete",$admin_id) || $superadmin?"checked=\"checked\"":""),($superadmin?"readonly=\"readonly\"":"")
   );
  
  }
  
  
   /*
    editace vice adminu
  */
	private function editAdmins(){
   
		$x = 0;
   
		foreach ($_POST['admin'] as $adminId => $h) {
			$x++;
			$status = true;
	  
			if ((!isset($h['passwd']) || !Help::checkPass($h['passwd'])) && mb_strlen(trim($h['passwd'])) > 0) {
				$this->vrat .= "<div class=\"errormsg\">(".$h['username'].") <strong>Heslo</strong> má špatný formát (6-16 znaků, nesmí začínat číslem, minimálně 2 čísla)</div><br />";
				$status = false;
			}
			if (!isset($h['username']) || !Help::checkNick($h['username'])) {
				$this->vrat .= "<div class=\"errormsg\">(".$h['username'].")  <strong>Nick</strong> má špatný formát (4-20 znaků, nesmí začínat číslem)</div><br />";
				$status = false;
			}
			if (!isset($h['email']) || !Help::valideMail($h['email']) || mb_strlen($h['email']) > 80) {
				$this->vrat .= "<div class=\"errormsg\">(".$h['username'].")  <strong>Mail</strong> má špatný formát</div><br />";
				$status = false;
			}
			if (!isset($h['firstName']) || mb_strlen(trim($h['firstName']),'utf-8') > 50) {
				$this->vrat .= "<div class=\"errormsg\">(".$h['username'].")  <strong>Jméno</strong> špatná délka (max 50 znaků)</div><br />";
				$status = false;
			}
			if (!isset($h['surname']) || mb_strlen(trim($h['surname']),'utf-8') > 60) {
				$this->vrat .= "<div class=\"errormsg\">(".$h['username'].")  <strong>Příjmení</strong> špatná délka (max 60 znaků)</div><br />";
				$status = false;
			}
//			if (isset($h['username'])) {
//	  
//	    $sql = "select nick from admin where nick='".Help::slash($h['username'])."' and not admin_id=".$k;
//       $res =& $this->db->query($sql);
//	    if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani admina z databáze',"admin_ex_db");
//	    
//		if($res->numRows() > 0){ $this->vrat .= "<div class=\"errormsg\">Tento \"Nick\"  (".Help::Html($h['nick']).") již existuje</div><br />";$status = false;}
//	  
//	  }

			if($status){
				$data = array(
					'firstName' => $h['firstName'],
					'surname' => $h['surname'],
					'username' => $h['username'],
					'email' => $h['email']
				);
				if (!empty($h['passwd']))
					$data['passwd'] = $h['passwd'];
				if (!It6_Models_Admin::update($adminId, $data))
					throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: upravy administratora', "admin_ex_db");
				$this->vrat .= "<div class=\"okmsg\">Administrátor ".Help::Html($h['username'])." byl úspěšně aktualizován</div><br />";
				$_SESSION['lastaction'][time()+$x] = "Aktualizace administrátora: ".$h['username'];
	  
			}
   
		}
   
	}
  
   /*
    vymazani konkretniho uzivatele
	*param1 admin_id: idecko admina
  */
	private function deleteAdmin($adminId) {
	
		if (!It6_Models_Admin::deleteAdmin($adminId))
			throw new ExHandler('Nepodarilo se provest dotaz: smazani administratora',"admin_ex_db");

		$this->vrat .= "<div class=\"okmsg\">Administrátor byl úspěšně smazán</div><br />";
		It6_Log::info(
			"Admin '%nick%' deleted.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('nick' => (!isset($_POST['admin'][$adminId]['username'])?$_POST['admin']['username']:$_POST['admin'][$adminId]['username']))
		);
	}
  
    /**
 * vyber dat z databaze
  * @param string $where podminka dotazu
 * @return object
 */
 /* IT6: disabled -- fix all error caused by calling this function by using It6_Models_Admin
  public function selectData($where=""){
  
	 $sql = "select admin_id,nick from admin ".$where;
     $res =& $this->db->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky admin',"admin_ex_db");
	
	 return $res;
	 
  }
  */
  
  /*
  vypis vsech adminu, strankovani
  */
  private function showAdmins(){
  
     //$this->db->autocommit(false);
	$order = (empty($_GET['order']) ? 'id' : $_GET['order']);
	$orderDesc = (!empty($_GET['desc']) && (0 == strcasecmp('asc', $_GET['desc'])) ? false : true);
	$limit = null; //array('page' => $this->page, 'pageSize' => PAGE);
	$admins = It6_Models_Admin::readDataAllOrdered($order, (!$orderDesc ? 'ASC' : 'DESC'), $limit);

//     $sql = "select * from admin";
//     $res2 =& $this->db->query($sql);
//	 if(DB::isError($res2)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky admin',"admin_ex_db");
	 
	 //$page = new Page($res2->numRows(),PAGE,"section=".$this->section);
	 
//	 $sql = "select admin_id,jmeno,prijmeni,nick,email,heslo from admin order by ".(isset($_GET['order']) && isset($_GET['desc'])?$_GET['order']." ".$_GET['desc']:"admin_id desc")."  limit ".(PAGE*$page->page).",".PAGE;
//     $res =& $this->db->query($sql);
//     if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky admin',"admin_ex_db");
	 
//	 $this->db->commit();
	 
	/*hlavicka*/ 
	//$this->vrat .= "<form method=\"post\" action\"?section=".$this->section."\"><table class=\"table-default table-admin\">
	//				 <thead><tr><th class=\"span-1\">&nbsp;</th><th class=\"span-2\"><a href=\"?section=".$this->section."&order=jmeno&desc=".(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc")."\">Jméno</a></th><th class=\"span-2\"><a href=\"?section=".$this->section."&order=prijmeni&desc=".(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc")."\">Přijmení</a></th><th class=\"span-2\"><a href=\"?section=".$this->section."&order=nick&desc=".(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc")."\">Nick</a></th class=\"span-2\"><th>Heslo</th><th class=\"span-2\">Email</th><th>&nbsp;</th><th>&nbsp;</th></tr></thead><tbody>";
/*
	var_dump($admins);
*/
	$head = array(
		array('order' => 'firstName', 'text' => 'Jméno'),
		array('order' => 'surname', 'text' => 'Příjmení'),
		array('order' => 'username', 'text' => 'Nick'),
		array('order' => 'email', 'text' => 'Email'),
	);
	$this->vrat .= '<h3>'.I18n::tr('Administrators').'</h3>';
	$this->vrat .= '<form method="post" action="?section='.$this->section.'"><table class="table-list">';
	$this->vrat .= '<tr><th></th><th>'.I18n::tr('ID').'</th>';
	foreach ($head as $data) {
		$this->vrat .= '<th>';
		if (false !== $data['order']) {
			if ($order != $data['order'])
				$desc = 'asc';
			else
				$desc = ($orderDesc ? 'asc' : 'desc');
			$descImg = '<img src="_img/adm_' . $desc . '.png" />';
			$this->vrat .= '<a href="?section=' . $this->section . '&order=' . $data['order'] . '&desc=' . $desc . '">' . $data['text'] . $descImg . '</a>';
		}
		else
			$this->vrat .= $data['text'];
		$this->vrat .= '</th>';
	}
		$this->vrat .= '<th>přístup</th>';
	$this->vrat .= '</tr>';
	  
	 $x = 1;
	 
	 /*telo*/
	 /*
	 while ($row =& $res->fetchRow()){
	  
	  $this->vrat .= '<tr>
	                  <td>'.((PAGE*$page->page)+$x).'.</td>
	                  <td><input type="text" class="sinput" maxlength="50" name="admin['.$row['admin_id'].'][jmeno]" value="'.Help::Html($row['jmeno']).'" /></td>
	                  <td><input type="text" class="sinput" maxlength="60" name="admin['.$row['admin_id'].'][prijmeni]" value="'.Help::Html($row['prijmeni']).'" /></td>
					  <td><input type="text" class="sinput" maxlength="20" name="admin['.$row['admin_id'].'][nick]" value="'.Help::Html($row['nick']).'" /></td>
					  <td><input type="password" class="sinput" maxlength="16" name="admin['.$row['admin_id'].'][heslo]" value="" /></td>
					  <td><input type="text" class="sinput" maxlength="80" name="admin['.$row['admin_id'].'][email]" value="'.Help::Html($row['email']).'" /></td>
					  <td><input type="submit" name="submit[delete]['.$row['admin_id'].']" class="sbutton" onclick="if(!confirm(\'Opravdu chcete smazat: '.Help::Script($row['nick']).'?\')) return false;" value="Smazat" /></td>
					  <td><input type="submit" name="submit[edit]['.$row['admin_id'].']" class="sbutton" value="Detail" /></td>
					  </tr>';
	  $x++;
	  
	 }
	 */
	foreach ($admins as $admin) {
/*
		$this->vrat .= '<tr>
	                  <td>'.$admin['id'].'</td>
	                  <td><input type="text" class="sinput" maxlength="50" name="admin['.$admin['id'].'][firstName]" value="'.Help::Html($admin['firstName']).'" /></td>
	                  <td><input type="text" class="sinput" maxlength="60" name="admin['.$admin['id'].'][surname]" value="'.Help::Html($admin['surname']).'" /></td>
					  <td><input type="text" class="sinput" maxlength="20" name="admin['.$admin['id'].'][username]" value="'.Help::Html($admin['username']).'" /></td>
					  <td><input type="password" class="sinput" maxlength="16" name="admin['.$admin['id'].'][passwd]" value="" /></td>
					  <td><input type="text" class="sinput" maxlength="80" name="admin['.$admin['id'].'][email]" value="'.Help::Html($admin['email']).'" /></td>
					  <td><input type="submit" name="submit[delete]['.$admin['id'].']" class="sbutton" onclick="if(!confirm(\'Opravdu chcete smazat: '.Help::Script($admin['username']).'?\')) return false;" value="Smazat" /></td>
					  <td><input type="submit" name="submit[edit]['.$admin['id'].']" class="sbutton" value="Detail" /></td>
					  </tr>';
*/
		$this->vrat .= '<tr>
					<td><input type="submit" name="submit[edit]['.$admin['id'].']" class="sbutton" value="Detail" /></td>
					<td>'.$admin['id'].'</td>
					<td>'.Help::Html($admin['firstName']).'</td>
					<td>'.Help::Html($admin['surname']).'</td>
					<td>'.Help::Html($admin['username']).'</td>
					<td>'.Help::Html($admin['email']).'</td>
					<td>'.($admin['access']==0 ? '<span class="blue">povolen</span>' : '<span class="red">zakázán</span>').'</td>
					</tr>';
	}
	  
	  
	  /*spodek*/ 
	  $this->vrat .= '
					  <tr><td colspan="8" class=""><br /><!--<input type="submit" style="width:280px;" name="editall" value="Aktualizovat vše" />--></td></tr>
					  <tr><th colspan="8" class=""><strong>Nový administrátor</strong></th></tr>
	                  <tr>
					  <td>&nbsp;</td>
	                  <td><input type="text" class="sinput" maxlength="50" name="firstName" value="'.(isset($_POST['firstName'])?Help::Html($_POST['firstName']):"").'" /></td>
	                  <td><input type="text" class="sinput" maxlength="60" name="surname" value="'.(isset($_POST['surname'])?Help::Html($_POST['surname']):"").'" /></td>
					  <td><input type="text" class="sinput mandatory" maxlength="20" name="username" value="'.(isset($_POST['username'])?Help::Html($_POST['username']):"").'" /></td>
					  <td><input type="password" class="sinput mandatory" maxlength="16" name="passwd" value="" /></td>
					  <td><input type="text" class="sinput mandatory" maxlength="80" name="email" value="'.(isset($_POST['email'])?Help::Html($_POST['email']):"").'" /></td>
					  <td><input type="submit" name="create" class="sbutton" value="Vytvořit" /></td>
					  </tr>
					  </tbody></table></form>'; 
	  
		
   
  }
  
  /*
  vytvoreni noveho administratora
  kontrolo zadanych udaju
  */
	private function createAdmin() {
  
		$status = true;
		if (!isset($_POST['passwd']) || !Help::checkPass($_POST['passwd'])) {
			$this->vrat .= "<div class=\"errormsg\"><strong>Heslo</strong> má špatný formát (6-16 znaků, nesmí začínat číslem, minimálně 2 čísla)</div><br />";
			$status = false;
		}
		if (!isset($_POST['username']) || !Help::checkNick($_POST['username'])) {
			$this->vrat .= "<div class=\"errormsg\"><strong>Nick</strong> má špatný formát (4-20 znaků, nesmí začínat číslem)</div><br />";
			$status = false;
		}
		if (!isset($_POST['email']) || !Help::valideMail($_POST['email']) || mb_strlen($_POST['email']) > 80) {
			$this->vrat .= "<div class=\"errormsg\"><strong>Mail</strong> má špatný formát</div><br />";
			$status = false;
		}
		if (!isset($_POST['firstName']) || mb_strlen($_POST['firstName'],'utf-8') > 50) {
			$this->vrat .= "<div class=\"errormsg\"><strong>Jméno</strong> špatná délka (max 50 znaků)</div><br />";
			$status = false;
		}
		if (!isset($_POST['surname']) || mb_strlen($_POST['surname'],'utf-8') > 60) {
			$this->vrat .= "<div class=\"errormsg\"><strong>Přijmení</strong> špatná délka (max 60 znaků)</div><br />";
			$status = false;
		}
		if (isset($_POST['username'])) {
			if (It6_Models_Admin::adminExists($_POST['username'])) {
				$this->vrat .= "<div class=\"errormsg\">Tento \"Nick\" již existuje</div><br />";
				$status = false;
			}
		}

		if ($status) {
			$data = array(
				'firstName' => $_POST['firstName'],
				'surname' => $_POST['surname'],
				'username' => $_POST['username'],
				'passwd' => $_POST['passwd'],
				'email' => $_POST['email']
			);

			//TODO: set parent roles too
			$adminId = It6_Models_Admin::create($data);
			if (!$adminId)
				throw new ExHandler('Nepodarilo se provest dotaz: vlozeni noveho administratora', "admin_ex_db");
			$this->vrat .= "<div class=\"okmsg\">Administrátor byl úspěšně vložen</div><br />";
			It6_Log::info(
				"New admin '%nick%' inserted.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('nick' => $_POST['username'])
			);
			unset($_POST['firstName']);
			unset($_POST['surname']);
			unset($_POST['username']);
			unset($_POST['passwd']);
			unset($_POST['email']);
		}
	}
  
  public function setPrivileges($menu){
	//IT6: only for backward compatibility
  }
  
   
  public function getContent(){
    return $this->vrat;
  }
    
}
