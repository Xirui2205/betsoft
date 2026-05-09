<?php
/**
 * @package    help
 */

/**
 * Trida pro praci s Chybami a Logy
 *
 * Tabulka Chyby a logy sloupec typ
 *prefix page,game,admin
 *
 * <code>
 *
 * </code>
 *
 * @package    Main
 */

class ChybyLogy extends Template{

/**
 * navratova hodnota
 * @access private
 * @var string
 */
private  $vrat = "";

/**
 * pravo zmeny v sekci
 * @access private
 * @var int
 */
private  $update = 0;
/**
 * pravo vymazani v sekci
 * @access private
 * @var int
 */
private  $delete = 0;

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
 * aktualni sekce
 * @access private
 * @var int
 */
private  $section;

/**
 * adresa
 * @access private
 * @var string
 */
private  $url = "";

/**
 * pole chyb v db admin
 * @access private
 * @var array
 */
private  $chybyAdmin = array();

/**
 * pole chyb v db game
 * @access private
 * @var array
 */
private  $chybyGame = array();

/**
 * celkovy pocet chyb v db admin
 * @access private
 * @var int
 */
public  $ChybyAdminCelkem;

/**
 * celkovy pocet chyb v db game
 * @access private
 * @var int
 */
public  $ChybyGameCelkem;

/**
 * celkovy pocet nevyrizenych chyb v db admin
 * @access private
 * @var int
 */
public  $ChybyAdminCelkemNev;

/**
 * celkovy pocet nevyrizenych chyb v db game
 * @access private
 * @var int
 */
public  $ChybyGameCelkemNev;
/**
 * top x novych a nevyrizenych chyb v db admin
 * @access private
 * @var int
 */
public  $ChybyAdminCelkemArray;

/**
 * top x novych a nevyrizenych chyb v db game
 * @access private
 * @var int
 */
public  $ChybyGameCelkemArray;
/**
 * Pocet top vypisu
 * @access private
 * @var int
 */
public  $pocetTop = 10;
/**
 * Objekt strankovani
 * @access private
 * @var Page
 */
private  $page;

/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param int $section id aktualni sekce
* @param PEAR::DB $dbGame objekt spojeni s databazi
* @param PEAR::DB $db objekt spojeni s databazi
*/
  public function __construct($section=0,$db=null,$dbGame=null){
		$_REQUEST['show'] = "logy";
		$this->section =  $section;

		if($db == null){

			$this->db = DB::connect(DATABASE ."://". MY_USER .":". MY_PASS ."@". MY_HOST ."/". MY_DB);
			if (DB::isError($this->db)) {
			 throw new ExHandler($this->db->getMessage(),"admin_ex_db");
			}
			$this->db->setFetchMode(DB_FETCHMODE_ASSOC);
			$sql = "set names 'utf8'";
			$res =& $this->db->query($sql);
			if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");
		}
		else
			$this->db =  $db;


		if($dbGame == null){

			$this->dbGame = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
			if (DB::isError($this->dbGame)) {
				throw new ExHandler($this->dbGame->getMessage(),"admin_ex_db");
			}
			$this->dbGame->setFetchMode(DB_FETCHMODE_ASSOC);
			$sql = "set names 'utf8'";
			$res =& $this->dbGame->query($sql);
			if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");
		}
		else
			$this->dbGame =  $dbGame;

  }

/**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
  public function runAction(){

    #vymazani chyb#
    if(isset($_POST['delete']['chyby'])){
			if($this->delete)
				$this->DeleteChyby();
			else
				$this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro mazání v této sekci</div>\n";
		}

		#vymazani logu#
    else if(isset($_POST['delete']['logy'])){
			if($this->delete)
				$this->DeleteLogy();
			else
				$this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro mazání v této sekci</div>\n";
		}

		#editace chyb#
    else if(isset($_POST['save']) || isset($_POST['vyridit']) || isset($_POST['nevyridit'])){
			if($this->update)
				$this->UpdateChyby();
			else
				$this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro editování v této sekci</div>\n";
		}

		#editace logu#
    else if(isset($_POST['vyriditlog']) || isset($_POST['nevyriditlog'])){
			if($this->update)
				$this->UpdateLogy();
			else
				$this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro editování v této sekci</div>\n";
		}

    $this->GetPage();

		$this->dbGame->disconnect();
		$this->db->disconnect();
	}


   /**
 * Slozeni stranky
 * @return void
 */
  private function GetPage(){
//TODO: tohle zda se vzdycky vrati ShowLogy(). ShowChyby se tedy nevyuziva?
		$_REQUEST['show'] = "logy";
		$_POST['show'] = "logy";

    $this->vrat .= '<form method="post" action="?section='.$this->section.'">';
		//$this->vrat .= 'Chyby: <input type="radio" class="no" onclick="this.form.submit()" name="show" value="chyby" '.((isset($_REQUEST['show']) && $_REQUEST['show'] == "chyby") || !isset($_REQUEST['show'])?'checked="checked"':"").' />
		//Logy: <input type="radio" class="no" onclick="this.form.submit()" name="show" value="logy" '.((isset($_REQUEST['show']) && $_REQUEST['show'] == "logy")?'checked="checked"':"").' />';
		$this->vrat .= '</form>';

		if((isset($_POST['show']) && $_POST['show'] == "chyby") || (!isset($_POST['show']) && !isset($_GET['show'])))
				$this->ShowChyby();
		else if(isset($_REQUEST['show']) && $_REQUEST['show'] == "logy")
			$this->ShowLogy();
  }

  /**
 * Zobrazeni Logu
 * @return void
 */
  private function ShowLogy(){

		$this->SelectDataChybyGameSum("logy");
		$this->SelectDataChybyAdminSum("logy");

		$cA = (isset($_GET['ca'])?$_GET['ca']:0);
    $cG = (isset($_GET['cg'])?$_GET['cg']:0);

		$resAdmin = $this->SelectDataChybyAdmin("logy");
		$resGame  = $this->SelectDataChybyGame("logy");

		$chybyAdmin = array();
		while ($row =& $resAdmin->fetchRow()){
			$chybyAdmin[] = $row;
			$cA = $row['vse'];
		}

		$chybyGame = array();
		while ($row =& $resGame->fetchRow()){
			$chybyGame[] = $row;
			$cG = $row['vse'];
		}

		$this->page = new Page(($cA>$cG?$cA:$cG),PAGE,"section=".$this->section.$this->url."&ca=".$cA."&cg=".$cG."&show=logy");

		$admin = "";
		$admin_array = It6_Models_Admin::readAll();

		foreach ($admin_array as $a) {
			$admin .= "<option value=\"".$a['id']."\" ".(isset($_REQUEST['vyresil']) && is_array($_REQUEST['vyresil']) && in_array($a['id'],$_REQUEST['vyresil'])?"selected=\"selected\"":"").">".Help::Html($a['username'])."</option>";
		}

		$c = $this->selectCode("logy");
		$code = "";

		foreach($c as $h){
      if(mb_strlen($h) < 1) $h = "Nezarazeno";
			$code .= "<option value=\"".Help::Html($h)."\" ".(isset($_REQUEST['code']) && is_array($_REQUEST['code']) && in_array($h,$_REQUEST['code'])?"selected=\"selected\"":"").">".Help::Html($h)."</option>";
		}

    $detail = "";

     /*hlavicka*/
		$this->vrat .= '
			<form method="post" action="?section='.$this->section.'">
				<table >
					<tr>
						<th colspan="7">
							<h2>LOGY</h2>
						</th>
					</tr>

					<tr>
						<th colspan="7" class="textleft">
							<table class="filtr">
								<tr>
									<td class="head" colspan="4">Rozšířené hledání</td>
								</tr>

								<tr>
									<td>&nbsp;</td>
									<td>Od</td>
									<td>Do</td>
								</tr>

								<tr>
									<td class="textleft" >Vyhledat od-do</td>
									<td>
										<input
											type="text"
											class="sinput dateTime"
											name="od"
											maxlength="10"
											value="'.(isset($_REQUEST['od'])?Help::Html($_REQUEST['od']):"").'"
										/>
										<img src="_clip/calendar.gif" class="calendar-icon">
									</td>
									<td>
										<input
											type="text"
											maxlength="10"
											class="sinput dateTime"
											name="do"
											value="'.(isset($_REQUEST['do'])?Help::Html($_REQUEST['do']):"").'"
										/>
										<img src="_clip/calendar.gif" class="calendar-icon">
									</td>
									<td>&nbsp;</td>
								</tr>

								<tr>
									<td class="textleft" valign="top">Ověřil</td>
									<td class="textleft">
										<select size="4" name="vyresil[]" multiple="multiple">
											<option value="all">--Všichni--</option>
											'.$admin.'
										</select>
									</td>
									<td class="textleft" valign="top">
										Kód logu
									</td>
									<td class="textleft">
										<select size="4" name="code[]" multiple="multiple">
											<option value="all">--Vše--</option>
											'.$code.'
										</select>
									</td>
								</tr>

								<tr>
									<td class="textleft">
										Pouze neověřené
									</td>
									<td class="textleft">
										<input
											type="checkbox"
											class="no"
											name="nevyrizene"
											'.(isset($_REQUEST['nevyrizene'])?"checked=\"checked\"":"").'
										/>
									</td>
									<td class="textleft" >&nbsp;</td><td class="textleft">&nbsp;</td>
								</tr>

								<tr>
									<td colspan="4" class="textright">
										<input type="submit" name="ok" class="inputs" value="Potvrdit"/>
									</td>
								</tr>

								<tr></tr>
							</table>
						</th>
					</tr>

					<tr>
						<td colspan="8" class="textright">'.$this->page->getPage().'</td>
					</tr>

					<tr>
						<th colspan="7">
							<h3>LOGY ADMIN</h3>
						</th>
					</tr>

					<tr>
						<td colspan="8">
							Počet záznamů: <strong>'.$cA.'</strong>
						</td>
					</tr>

					<tr>
						<th>&nbsp;</th>
						<th class="textleft">
							<a href="?section='.$this->section.'&order=datum&show=logy&desc='.(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc").$this->url.'">
								Datum
							</a>
						</th>
						<th class="textleft">
							<a href="?section='.$this->section.'&order=akce&show=logy&desc='.(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc").$this->url.'">
								Akce
							</a>
						</th>
						<th class="textleft">
							<a href="?section='.$this->section.'&order=code&show=logy&desc='.(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc").$this->url.'">
								Log
							</a>
						</th>
						<th class="textleft">
							<a href="?section='.$this->section.'&order=admin_id&show=logy&desc='.(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc").$this->url.'">
								Ověřil
							</a>
						</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
		';

		$x = 1;
		foreach($chybyAdmin as $h){

			$detail .= '
				<tr>
					<form method="post" action="?section='.$this->section.'">
						<table id="log_admin_'.$h['log_id'].'" style="position:absolute;display:none;z-index:20;top:530px;left:227px;background:#7cb6a4">
							<tr>
								<td><strong>Status:</strong></td>
								<td>'.(!$h['status']?"Neověřeno":"Ověřeno").'</td>
							</tr>

							<tr>
								<td>
									<strong>Code:</strong>
								</td>
								<td>'.Help::Html($h['code']).'</td>
							</tr>

							'.($h['status']?'<tr><td><strong>Ověřil:</strong></td><td> '.Help::Html($admin_array[$h['admin_id']]).'</td></tr>':'').'

							<tr>
								<td colspan="2">
									<strong>Text:</strong>
								</td>
							</tr>

							<tr>
								<td colspan="2">'.Help::Html($h['text']).'</td>
							</tr>

							<tr>
								<td colspan="2">
									'.($h['status']?'<input type="submit" name="nevyriditlog" value="Neověřené" />':'<input type="submit" name="vyriditlog" value="Ověřit" />').'
								</td>
							</tr>
						</table>

						<input type="hidden" name="log_id" value="'.$h['log_id'].'"/>
						<input type="hidden" name="type" value="admin"/>
						<input type="hidden" name="show" value="logy"/>
					</form>
				</tr>

				<tr style="font-size:0.8em;">
					<td '.(!$h['status']?"style=\"background:#7cb6a4;color:white;\"":"").' class="suda">
						'.($this->page->page*PAGE+$x).'
					</td>
					<td class="licha" nowrap="nowrap">
						'.$h['datum'].'
					</td>
					<td class="suda">
						'.Help::Html($h['akce']).'
					</td>
					<td class="licha">
						'.(mb_strlen($h['code']) > 0?Help::Html($h['code']):"Nedefinován").'
					</td>
					<td class="suda">
						'.(isset($admin_array[$h['admin_id']])?Help::Html($admin_array[$h['admin_id']]):"-").'
					</td>
					<td class="licha">
						<input
							type="button"
							class="sinput"
							name=""
							onclick="var oo = new getObj(\'log_admin_'.$h['log_id'].'\');displayObj(oo);"
							value="Detail"
						/>
					</td>
					<td class="suda">
						<input
							type="submit"
							class="sinput"
							name="delete[logy][admin]['.$h['log_id'].']"
							onclick="if(!confirm(\'Opravdu chcete záznam smazat?\')) return false;"
							value="Smazat"
						/>
					</td>
				</tr>
			';
			$x++;
		}

		/*logy page*/
		$this->vrat .= '
			<tr>
				<th colspan="8">
					<h3>LOGY PAGE</h3>
				</th>
			</tr>
			<tr>
				<td colspan="8">
					Počet záznamů:
					<strong>'.$cG.'</strong>
				</td>
			</tr>
		';


		$x = 1;
		foreach($chybyGame as $h){
			$detail .= '
				<form method="post" action="?section='.$this->section.'">
					<tr>
						<table id="log_game_'.$h['log_id'].'" style="position:absolute;display:none;z-index:20;top:530px;left:227px;background:#7cb6a4">
							<tr>
								<td>
									<strong>Status:</strong>
								</td>
								<td>
									'.(!$h['status']?"Neověřeno":"Ověřeno").'
								</td>
							</tr>

							<tr>
								<td>
									<strong>Code:</strong>
								</td>
								<td>
									'.Help::Html($h['code']).'
								</td>
							</tr>

							'.($h['status']?'<tr><td><strong>Ověřil:</strong></td><td> '.Help::Html($admin_array[$h['admin_id']]).'</td></tr>':'').'

							<tr>
								<td colspan="2">
									<strong>Text:</strong>
								</td>
							</tr>

							<tr>
								<td colspan="2">
									'.Help::Html($h['text']).'
								</td>
							</tr>

							<tr>
								<td colspan="2">
									'.($h['status']?'<input type="submit" name="nevyriditlog" value="Neověřené" />':'<input type="submit" name="vyriditlog" value="Ověřit" />').'
								</td>
							</tr>
						</table>
						<input type="hidden" name="log_id" value="'.$h['log_id'].'"/>
						<input type="hidden" name="type" value="game"/>
						<input type="hidden" name="show" value="logy"/>
					</tr>

					<tr style="font-size:0.8em;">
						<td '.(!$h['status']?"style=\"background:#7cb6a4;color:white;\"":"").' class="suda">
							'.($this->page->page*PAGE+$x).'
						</td>
						<td class="licha" nowrap="nowrap">
							'.It6_Date::fromDb($h['datum']).'
						</td>
						<td class="suda">
							'.Help::Html($h['akce']).'
						</td>
						<td class="licha">
							'.(mb_strlen($h['code']) > 0?Help::Html($h['code']):"Nedefinován").'
						</td>
						<td class="suda">
							'.(isset($admin_array[$h['admin_id']])?Help::Html($admin_array[$h['admin_id']]):"-").'
						</td>
						<td class="licha">
							<input
								type="button"
								class="sinput"
								name=""
								onclick="var
								oo = new getObj(\'log_game_'.$h['log_id'].'\');displayObj(oo);"
								value="Detail"
							/>
						</td>
						<td class="suda">
							<input
								type="submit"
								class="sinput"
								name="delete[logy][game]['.$h['log_id'].']"
								onclick="if(!confirm(\'Opravdu chcete záznam smazat?\')) return false;"
								value="Smazat"
							/>
						</td>
					</tr>
				</form>
			';
			$x++;
		}


/*spodek*/
		$this->vrat .= '
					<tr>
						<td colspan="8" class="textright">
							'.$this->page->getPage().'
						</td>
					</tr>
					<tr>
						<td colspan="4"></td>
						<td colspan="4"></td>
					</tr>
					<input type="hidden" name="show" value="logy"/>
				</table>
			</form>

			<br /><br />
		';

	 $this->vrat .= $detail;

  }

 /**
 * vymazani logy
 * @return void
 */
  public function DeleteLogy(){

	 $id = (isset($_POST['delete']['logy']['game'])?key($_POST['delete']['logy']['game']):key($_POST['delete']['logy']['admin']));
	 $sql = "delete from logy where log_id=".intval($id);

	 if(isset($_POST['delete']['logy']['game']))
       $res = $this->dbGame->query($sql);
     else if(isset($_POST['delete']['logy']['admin']))
	   $res = $this->db->query($sql);
     if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");

	 $this->vrat .= "<div class=\"okmsg\">Log ".Help::Html($id)." byl úspěšně vymazána</div><br />";

		It6_Log::info(
			"Log '%log%' was deleted.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('log' => $id)
		);
  }

   /**
 * vymazani chyby
  * @param int $metoda_id id platebni metody
 * @return object
 */
  public function DeleteChyby(){

	 $id = (isset($_POST['delete']['chyby']['game'])?key($_POST['delete']['chyby']['game']):key($_POST['delete']['chyby']['admin']));
	 $sql = "DELETE from chyby WHERE chyba_id=".intval($id);

	 if(isset($_POST['delete']['chyby']['game']))
       $res = $this->dbGame->query($sql);
     else if(isset($_POST['delete']['chyby']['admin']))
	   $res = $this->db->query($sql);
     if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");

	 $this->vrat .= "<div class=\"okmsg\">Chyba ".Help::Html($id)." byl úspěšně vymazána</div><br />";

		It6_Log::info(
			"Error '%error%' was deleted.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('error' => $id)
		);
  }

 /**
 * aktualizace logu
 * @return object
 */
  public function  UpdateLogy(){

	 if(isset($_POST['log_id']) && isset($_POST['type']) && ($_POST['type'] == "admin" || $_POST['type'] == "game")){


	 $sql = "update logy set admin_id=".((isset($_POST['vyriditlog'])?It6_Session_Admin::getUserData('id'):0)).",status=".(isset($_POST['vyriditlog'])?1:0)." where log_id=".intval($_POST['log_id']);

	 if($_POST['type'] == "game")
	    $res = $this->dbGame->query($sql);
     else
	    $res = $this->db->query($sql);
     if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");

	 $this->vrat .= "<div class=\"okmsg\">Log ".intval($_POST['log_id'])." byl úspěšně aktualizována</div><br />";

		It6_Log::info(
			"Log '%log%' was edited: '%state%'.",
			It6_Log::TAG_ADMIN_OPERATION,
			array(
				'error'	=> intval($_POST['log_id']),
				'state'	=> (isset($_POST['vyriditlog'])?"Authorized":(isset($_POST['nevyriditlog'])?"Unauthorized":""))
			)
		);
	}

  }

 /**
 * ulozeni poznamky
 * @return object
 */
  public function  UpdateChyby(){

	 if(isset($_POST['chyba_id']) && isset($_POST['type']) && ($_POST['type'] == "admin" || $_POST['type'] == "game")){


	 if(isset($_POST['save']))
	   $sql = "UPDATE chyby SET poznamka='".Help::Slash($_POST['poznamka'])."' WHERE chyba_id=".intval($_POST['chyba_id']);
	 else
	   $sql = "UPDATE chyby SET poznamka='".Help::Slash($_POST['poznamka'])."',admin_id=".((isset($_POST['vyridit'])?It6_Session_Admin::getUserData('id'):0)).",status=".(isset($_POST['vyridit'])?1:0)." where chyba_id=".intval($_POST['chyba_id']);

	 if($_POST['type'] == "game")
	    $res = $this->dbGame->query($sql);
     else
	    $res = $this->db->query($sql);
     if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");

	 $this->vrat .= "<div class=\"okmsg\">Chyba ".intval($_POST['chyba_id'])." byl úspěšně aktualizována</div><br />";

		It6_Log::info(
			"Error '%error%' was edited: '%state%'.",
			It6_Log::TAG_ADMIN_OPERATION,
			array(
				'error'	=> intval($_POST['chyba_id']),
				'state'	=> (isset($_POST['vyridit'])?"Authorized":"Unauthorized")
			)
		);
	}

  }

  /**
 * vyber chyby z databaze admin celkove sumy
 * @param string $tab jmeno tabulky ze ktere s emaji data sbirat
   * @param string $and dalsi podminka do sql dotazu
 * @return object
 */
  public function SelectDataChybyAdminSum($tab="chyby",$and=""){

	 $this->db->autocommit(false);

	 $sql = "SELECT * FROM ".$tab;
     $res =& $this->db->query($sql);
	 if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky chyby',"admin_ex_db");

	 $this->ChybyAdminCelkem = $res->numRows();

	 $sql = "SELECT * FROM ".$tab." where status=0".$and;
     $res =& $this->db->query($sql);
	 if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky chyby',"admin_ex_db");

	 $this->ChybyAdminCelkemNev = $res->numRows();

	 $sql = "SELECT * FROM ".$tab." WHERE status=0 ".$and." ORDER BY datum DESC LIMIT 0,".$this->pocetTop;
     $res =& $this->db->query($sql);
	 if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky chyby',"admin_ex_db");

	 while ($row =& $res->fetchRow()){
	   $this->ChybyAdminCelkemArray[] = $row;
	 }

	 $this->db->commit();

  }

  /**
 * vyber chyby z databaze admin
 * @param string $tab jmeno tabulky ze ktere s emaji data sbirat
 * @param string $and dalsi podminka do sql dotazu
 * @return object
 */
  public function SelectDataChybyAdmin($tab="chyby",$and=""){

	 $where = "WHERE ";

	 if(isset($_REQUEST['od']) && mb_strlen($_REQUEST['od'])>0 && Help::checkDatumTime($_REQUEST['od'])){
	   $where .= "datum>'".It6_Date::toDb($_REQUEST['od'])."' AND ";
	   $this->url .="&od=".urlencode($_REQUEST['od']);
	 }
	 if(isset($_REQUEST['do']) && mb_strlen($_REQUEST['do'])>0 && Help::checkDatumTime($_REQUEST['do'])){
	   $where .= "datum<'".It6_Date::toDb($_REQUEST['do'])."' AND ";
	   $this->url .="&do=".urlencode($_REQUEST['do']);
	 }
	 if(isset($_REQUEST['nevyrizene'])){
	   $where .= "status=0 and ";
	   $this->url .="&nevyrizene=1";
	 }
	 if(isset($_REQUEST['fulltext']) && mb_strlen($_REQUEST['fulltext'])>0){
	   $where .= "MATCH (message,poznamka,text) AGAINST ('".Help::Slash($_REQUEST['fulltext'])."' IN BOOLEAN MODE) and ";
	   $this->url .="&fulltext=".urlencode($_REQUEST['fulltext']);
	 }
	 if(isset($_REQUEST['vyresil']) && is_array($_REQUEST['vyresil']) && !in_array("all",$_REQUEST['vyresil'])){
	   $where .= "(";
	   foreach($_REQUEST['vyresil'] as $h){
	      $where .= "admin_id=".intval($h)." or ";
		  $this->url .="&vyresil[]=".urlencode($h);
	   }
	   $where = substr($where,0,mb_strlen($where)-4);
	   $where .= ") and ";
	 }
	 if(isset($_REQUEST['code']) && is_array($_REQUEST['code']) && !in_array("all",$_REQUEST['code'])){
	   $where .= "(";
	   foreach($_REQUEST['code'] as $h){
	      $where .= "code='".Help::Slash($h)."' or ";
		  $this->url .="&code[]=".urlencode($h);
	   }
	   $where = substr($where,0,mb_strlen($where)-4);
	   $where .= ") and ";
	 }

	 if($where != "where ")$where = substr($where,0,mb_strlen($where)-4);else $where = "";

		$sql = "
			SELECT *,(
				SELECT COUNT(*)
				FROM ".$tab." ".$where.$and."
			) AS vse
			FROM ".$tab." ".$where.$and."
			ORDER BY ".(isset($_GET['order']) && isset($_GET['desc'])?$_GET['order']." ".$_GET['desc']:($tab == "logy"?"log_id desc":"chyba_id DESC"))."
			LIMIT ".(PAGE*(isset($_GET['page'])?$_GET['page']:0)).",".PAGE
		;

    $res = $this->db->query($sql);
    if(DB::isError($res)) throw new ExHandler($res->getMessage().$sql,"admin_ex_db");

		return $res;
  }

     /**
 * vyber chyby z databaze game celkove sumy
  * @param string $tab jmeno tabulky ze ktere s emaji data sbirat
   * @param string $and dalsi podminka do sql dotazu
  * @return object
 */
  public function SelectDataChybyGameSum($tab="chyby",$and=""){

	 $this->dbGame->autocommit(false);

	 $sql = "select * from ".$tab;
     $res =& $this->dbGame->query($sql);
	 if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber z tabulky chyby',"admin_ex_db");

	 $this->ChybyGameCelkem = $res->numRows();

	 $sql = "select * from ".$tab." where status=0".$and;
     $res =& $this->dbGame->query($sql);
	 if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky chyby',"admin_ex_db");

	 $this->ChybyGameCelkemNev = $res->numRows();

	 $sql = "select * from ".$tab." where status=0 ".$and." order by datum desc limit 0,".$this->pocetTop;
     $res =& $this->dbGame->query($sql);
	 if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky chyby',"admin_ex_db");

	 while ($row =& $res->fetchRow()){
	   $this->ChybyGameCelkemArray[] = $row;
	 }

	 $this->dbGame->commit();

  }

   /**
 * vyber chyby z databaze game
  * @param string $tab jmeno tabulky ze ktere s emaji data sbirat
   * @param string $and dalsi podminka do sql dotazu
 * @return object
 */
  public function SelectDataChybyGame($tab="chyby",$and=""){

	 $where = "where ";

	 if(isset($_REQUEST['od']) && mb_strlen($_REQUEST['od'])>0 && Help::checkDatumTime($_REQUEST['od'])){
	   $where .= "datum>'".It6_Date::toDb($_REQUEST['od'])."' AND ";
	 }
	 if(isset($_REQUEST['do']) && mb_strlen($_REQUEST['do'])>0 && Help::checkDatumTime($_REQUEST['do'])){
	   $where .= "datum<'".It6_Date::toDb($_REQUEST['do'])."' AND ";
	 }
	 if(isset($_REQUEST['nevyrizene'])){
	   $where .= "status=0 and ";
	 }
	 if(isset($_REQUEST['fulltext']) && mb_strlen($_REQUEST['fulltext'])>0){
	   $where .= "MATCH (message,poznamka,text) AGAINST ('".Help::Slash($_REQUEST['fulltext'])."' IN BOOLEAN MODE) and ";
	   $this->url .="&fulltext=".urlencode($_REQUEST['fulltext']);
	 }
	 if(isset($_REQUEST['vyresil']) && is_array($_REQUEST['vyresil']) && !in_array("all",$_REQUEST['vyresil'])){
	   $where .= "(";
	   foreach($_REQUEST['vyresil'] as $h){
	      $where .= "admin_id=".intval($h)." or ";
	   }
	   $where = substr($where,0,mb_strlen($where)-4);
	   $where .= ") and ";
	 }
	 if(isset($_REQUEST['code']) && is_array($_REQUEST['code']) && !in_array("all",$_REQUEST['code'])){
	   $where .= "(";
	   foreach($_REQUEST['code'] as $h){
	      $where .= "code='".Help::Slash($h)."' or ";
	   }
	   $where = substr($where,0,mb_strlen($where)-4);
	   $where .= ") and ";
	 }

	 if($where != "where ")$where = substr($where,0,mb_strlen($where)-4);else $where = "";

     $sql = "select *,(select count(*) from ".$tab." ".$where.$and.") as vse from ".$tab." ".$where.$and." order by ".(isset($_GET['order']) && isset($_GET['desc'])?$_GET['order']." ".$_GET['desc']:($tab == "logy"?"log_id desc":"chyba_id desc"))." limit ".(PAGE*(isset($_GET['page'])?$_GET['page']:0)).",".PAGE;
     $res = $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");

	 return $res;

  }

   /**
 * vyber kodu
 * @param string $tab jmeno tabulky ze ktere s emaji data sbirat
 * @return object
 */
  public function selectCode($tab="chyby"){

	 $poleAdmin = array();
	 $poleGame = array();

	 $sql = "select code from ".$tab." group by code";
     $res =& $this->db->query($sql);
     if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky '.$tab,"admin_ex_db");

	 while ($row =& $res->fetchRow()){
	   $poleAdmin[$row['code']] = $row['code'];
	 }

	 $sql = "select code from ".$tab." group by code";
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky '.$tab,"admin_ex_db");

	 while ($row =& $res->fetchRow()){
	   $poleGame[$row['code']] = $row['code'];
	 }

	 return array_merge($poleAdmin,$poleGame);

  }

    /**
 * Zobrazeni Chyb
 * @return void
 */
  private function ShowChyby(){

   	 $this->SelectDataChybyGameSum();
	 $this->SelectDataChybyAdminSum();

	 $cA = (isset($_GET['ca'])?$_GET['ca']:0);
     $cG = (isset($_GET['cg'])?$_GET['cg']:0);

	 $resAdmin = $this->SelectDataChybyAdmin();
	 $resGame  = $this->SelectDataChybyGame();

	$chybyAdmin = array();
	 while ($row =& $resAdmin->fetchRow()){
	  $chybyAdmin[] = $row;
	  $cA = $row['vse'];
	 }

	$chybyGame = array();
	 while ($row =& $resGame->fetchRow()){
	  $chybyGame[] = $row;
	  $cG = $row['vse'];
	 }

	 $this->page = new Page(($cA>$cG?$cA:$cG),PAGE,"section=".$this->section.$this->url."&ca=".$cA."&cg=".$cG);

	 $admin = "";
	 $admin_array = It6_Models_Admin::readAll();

	 foreach ($admin_array as $a) {
	   $admin .= "<option value=\"".$a['id']."\" ".(isset($_REQUEST['vyresil']) && is_array($_REQUEST['vyresil']) && in_array($a['id'],$_REQUEST['vyresil'])?"selected=\"selected\"":"").">".Help::Html($a['username'])."</option>";
	 }

	$c = $this->selectCode();
	$code = "";

	foreach($c as $h){

       if(mb_strlen($h) < 1) $h = "Nezarazeno";
	   $code .= "<option value=\"".$h."\" ".(isset($_REQUEST['code']) && is_array($_REQUEST['code']) && in_array($h,$_REQUEST['code'])?"selected=\"selected\"":"").">".Help::Html($h)."</option>";

	}

    $detail = "";

     /*hlavicka*/
	 $this->vrat .= '<table >

	              	<tr><th colspan="8"><h2>CHYBY</h2></th></tr>
					<tr><th colspan="8" class="textleft">
					 <form method="post" action="?section='.$this->section.'">
					  <table class="filtr">
					    <tr><td  class="head" colspan="4">Rozšířené hledání</td></tr>
					    <tr><td>&nbsp;</td><td>Od (dd.mm.YYYY)</td><td>Do (dd.mm.YYYY)</td></tr>
					    <tr><td class="textleft" >Vyhledat od-do</td><td><input type="text" class="sinput" name="od" maxlength="10" value="'.(isset($_REQUEST['od'])?Help::Html($_REQUEST['od']):"").'" /></td><td><input type="text" maxlength="10" class="sinput" name="do" value="'.(isset($_REQUEST['do'])?Help::Html($_REQUEST['do']):"").'" /></td><td>&nbsp;</td></tr>
						<tr>
						  <td class="textleft" valign="top">Vyřešil</td><td class="textleft"><select size="4" name="vyresil[]" multiple="multiple"><option value="all">--Všichni--</option>'.$admin.'</select></td>
						  <td class="textleft" valign="top">Kód chyby</td><td class="textleft"><select size="4" name="code[]" multiple="multiple"><option value="all">--Vše--</option>'.$code.'</select></td>
						</tr>
						  <tr>
						  <td class="textleft">Pouze nevyřízené</td><td class="textleft"><input type="checkbox" class="no" name="nevyrizene" '.(isset($_REQUEST['nevyrizene'])?"checked=\"checked\"":"").' /></td>
						  <td class="textleft" >Počet výpisů</td><td class="textleft"><input type="text" name="vypisy" class="sinput" value="'.(isset($_REQUEST['vypisy'])?Help::Html($_REQUEST['vypisy']):"").'" maxlength="3" /></td>
						</tr>
						<tr><td class="textleft">Fulltext</td><td class="textleft" colspan="3"><input type="text" value="'.(isset($_REQUEST['fulltext'])?Help::Html($_REQUEST['fulltext']):"").'" name="fulltext" maxlength="60" /></td></tr>
						<tr><td colspan="4" class="textleft"><strong>Nápovědat fulltext:</strong><br />
						<em>+ před slovem znamená, že slovo musí být přítmono<br />
						- před slovem znamená, že slovo nemusí být přítmono<br />
						() se používají pro uzavření skupiny slov v podvýrazech<br />
						* zastupuje jakékoliv znaky<br />
						"" musí obsahovat frázi ve závorkách ve stejné posloupnosti jak je uvedena<br />
						bez operátorů znamená obsahuje aspoň jedno ze slov</em>
						</td></tr>
						<tr><td colspan="4" class="textright"><input type="submit" name="ok" class="inputs" value="Potvrdit"/></td></tr>
						<tr></tr>
					  </table>
					</th></tr>';
	 /*chyby admin*/
	 $this->vrat .= '<tr><td colspan="8" class="textright">'.$this->page->getPage().'</td></tr>
	                  <tr>
					  <th colspan="8"><h3>CHYBY ADMIN</h3></th></tr>
					  <tr><td colspan="8">Počet záznamů: <strong>'.$cA.'</strong></td></tr>
	                 <tr><th>&nbsp;</th><th class="textleft"><a href="?section='.$this->section.'&order=datum&desc='.(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc").$this->url.'">Datum</a></th><th class="textleft"><a href="?section='.$this->section.'&order=radek&desc='.(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc").$this->url.'">Řádek</a></th><th class="textleft"><a href="?section='.$this->section.'&order=soubor&desc='.(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc").$this->url.'">Soubor</a></th><th class="textleft"><a href="?section='.$this->section.'&order=message&desc='.(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc").$this->url.'">Chyba</a></th><th class="textleft"><a href="?section='.$this->section.'&order=code&desc='.(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc").$this->url.'">Kód chyby</a></th><th>&nbsp;</th><th>&nbsp;</th>';

	 $x = 1;
	 foreach($chybyAdmin as $h){

	   $detail .= '<form method="post" action="?section='.$this->section.'">
	               <table id="chyba_admin_'.$h['chyba_id'].'" style="position:absolute;display:none;z-index:20;top:530px;left:227px;background:#9DB5D7">
				    <tr><td><strong>Status:</strong></td><td> '.(!$h['status']?"Nevyřízeno":"Vyřízeno").'</td></tr>
					<tr><td><strong>Code:</strong></td><td> '.Help::Html($h['code']).'</td></tr>
					'.($h['status']?'<tr><td><strong>Vyřešil:</strong></td><td> '.Help::Html($admin_array[$h['admin_id']]).'</td></tr>':'').'
					<tr><td colspan="2"><strong>Zpráva:</strong></td></tr>
					<tr><td colspan="2"> '.Help::Html($h['message']).'</td></tr>
					<tr><td colspan="2"><strong>Zdroj chyby:</strong></td></tr>
					<tr><td colspan="2"> '.Help::Html($h['text']).'</td></tr>
					<tr><td colspan="2"><strong>Poznámky</strong>:</td></tr>
					<tr><td colspan="2"> <textarea name="poznamka" cols="50" rows="6" >'.Help::Html($h['poznamka']).'</textarea></td></tr>
					<tr><td colspan="2"><input type="submit" name="save" value="Uložit poznámku" />
					'.($h['status']?'<input type="submit" name="nevyridit" value="Nevyřízené" />':'<input type="submit" name="vyridit" value="Vyřízené" />').'</td></tr>
				   </table>
				   <input type="hidden" name="chyba_id" value="'.$h['chyba_id'].'"/>
				   <input type="hidden" name="type" value="admin"/>
	               <input type="hidden" name="show" value="chyby"/>
				   </form>';

	   $this->vrat .= '
	                 <tr  style="font-size:0.8em;"><td '.(!$h['status']?"style=\"background:#BE2424;color:white;\"":"").' class="suda">'.($this->page->page*PAGE+$x).'</td><td class="licha" nowrap="nowrap">'.Help::Html($h['datum']).'</td><td class="suda">'.Help::Html($h['radek']).'</td><td class="licha">'.Help::Html(basename($h['soubor'])).'</td><td class="suda">'.substr($h['message'],0,40).' ...</td><td class="licha">'.(mb_strlen($h['code']) > 0?Help::Html($h['code']):"Nedefinován").'</td><td class="suda"><input type="button" class="sinput" name=""  onclick="var oo = new getObj(\'chyba_admin_'.$h['chyba_id'].'\');displayObj(oo);" style="width:60px" value="Detail"/></td><td class="licha"><input type="submit" style="width:60px" class="sinput" name="delete[chyby][admin]['.$h['chyba_id'].']" onclick="if(!confirm(\'Opravdu chcete záznam smazat?\')) return false;" value="Smazat"/></td></tr>
					 ';
	  $x++;
	 }

 	 /*chyby page*/
	 $this->vrat .= '<tr><th colspan="8"><h3>CHYBY PAGE</h3></th></tr>
	              <tr><td colspan="8">Počet záznamů: <strong>'.$cG.'</strong></td></tr>';


	 $x = 1;
	 foreach($chybyGame as $h){

	   	   $detail .= '<form method="post" action="?section='.$this->section.'">
	               <table id="chyba_game_'.$h['chyba_id'].'" style="position:absolute;display:none;z-index:20;top:530px;left:227px;background:#9DB5D7">
				    <tr><td><strong>Status:</strong></td><td> '.(!$h['status']?"Nevyřízeno":"Vyřízeno").'</td></tr>
					<tr><td><strong>Code:</strong></td><td> '.Help::Html($h['code']).'</td></tr>
					'.($h['status']?'<tr><td><strong>Vyřešil:</strong></td><td> '.Help::Html($admin_array[$h['admin_id']]).'</td></tr>':'').'
					<tr><td colspan="2"><strong>Zpráva:</strong></td></tr>
					<tr><td colspan="2"> '.Help::Html($h['message']).'</td></tr>
					<tr><td colspan="2"><strong>Zdroj chyby:</strong></td></tr>
					<tr><td colspan="2"> '.Help::Html($h['text']).'</td></tr>
					<tr><td colspan="2"><strong>Poznámky</strong>:</td></tr>
					<tr><td colspan="2"> <textarea name="poznamka" cols="50" rows="6" >'.Help::Html($h['poznamka']).'</textarea></td></tr>
					<tr><td colspan="2"><input type="submit" name="save" value="Uložit poznámku" />
					 '.($h['status']?'<input type="submit" name="nevyridit" value="Nevyřízené" />':'<input type="submit" name="vyridit" value="Vyřízené" />').'</td></tr>
				   </table>
				   <input type="hidden" name="chyba_id" value="'.$h['chyba_id'].'"/>
				   <input type="hidden" name="type" value="game"/>
	               <input type="hidden" name="show" value="chyby"/>
				   </form>';

	   $this->vrat .= '
	                 <tr  style="font-size:0.8em;"><td '.(!$h['status']?"style=\"background:#BE2424;color:white;\"":"").' class="suda">'.($this->page->page*PAGE+$x).'</td><td class="licha" nowrap="nowrap">'.Help::Html($h['datum']).'</td><td class="suda">'.Help::Html($h['radek']).'</td><td class="licha">'.Help::Html(basename($h['soubor'])).'</td><td class="suda">'.substr($h['message'],0,40).' ...</td><td class="licha">'.(mb_strlen($h['code']) > 0?Help::Html($h['code']):"Nedefinován").'</td><td class="suda"><input type="button" class="sinput" name=""  onclick="var oo = new getObj(\'chyba_game_'.$h['chyba_id'].'\');displayObj(oo);" value="Detail" style="width:60px" /></td><td class="licha"><input type="submit" class="sinput" style="width:60px" name="delete[chyby][game]['.$h['chyba_id'].']" onclick="if(!confirm(\'Opravdu chcete záznam smazat?\')) return false;" value="Smazat"/></td></tr>
					 ';
	  $x++;
	 }


     /*spodek*/
	 $this->vrat .= '<tr><td colspan="8" class="textright">'.$this->page->getPage().'</td></tr>
	                 <tr><td colspan="4"><input type="submit" value="Exportovat do (.csv)"></td><td colspan="4"><input type="submit" value="Exportovat do (.xml)"></td></tr><input type="hidden" name="show" value="chyby"/></form></table><br /><br />';

	 $this->vrat .= $detail;

  }

 /**
 * Nastaveni prav k sekci
 * @param int $update pravo zapisu
 * @param int $delete pravo smazani
 * @return void
 */
  public function setPrivileges($update,$delete){

   $this->update = $update;
   $this->delete = $delete;

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
