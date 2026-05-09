<?php
/**
 * @package    Book
 */



class ReklamaSazky extends Template{

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
 * aktualni sekce
 * @access private
 * @var int
 */
private  $section;

/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param int $section id aktualni sekce
* @param PEAR::DB $dbGame objekt spojeni s databazi
*/
  public function __construct($section=0){

    $this->section =  $section;


    $this->dbGame = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
    if (DB::isError($this->dbGame)) {
      throw new ExHandler($this->dbGame->getMessage(),"admin_ex_db");
    }
    $this->dbGame->setFetchMode(DB_FETCHMODE_ASSOC);
    $sql = "set names 'utf8'";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");



  }

/**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
  public function runAction(){


  	if(isset($_POST['edit'])){
  	 	 $this->Update();
  	}
    else if(isset($_POST['edit_hot']) && isset($_POST['hot']) && is_array($_POST['hot'])){
  	 	 $this->UpdateHot();
  	}

  	$this->ShowHot();

    $this->ShowData();





   $this->dbGame->disconnect();

  }


	/**
	*edituje data
	* @return void
	*/
	private function Update(){
		$this->dbGame->autocommit(false);
		$status = true;

		$sql = "DELETE FROM reklama_page_bet";
		$res =& $this->dbGame->query($sql);
		if(DB::isError($res))
			throw new ExHandler($sql.'Nepodarilo se provest dotaz: vlozeni nove udalosti',"admin_ex_db");

		foreach($_POST['sazka'] as $k=>$h) {
			foreach($h as $k2=>$h2){
				if(mb_strlen($h2)>0 && ctype_digit($h2)){
					$sql = "replace into reklama_page_bet values(".intval($k).",".intval($h2).") ";
					$res =& $this->dbGame->query($sql);
					if(DB::isError($res)) {
						$this->dbGame->rollback();
						throw new ExHandler($sql.'Nepodarilo se provest dotaz: vlozeni nove udalosti',"admin_ex_db");
					}
				}
			}
		}

		if(!$status) {
			$this->dbGame->rollback();
			$this->vrat .= "<div class=\"errormsg\">Editace se nepovedla</div><br />";
		}
		else {
			$this->dbGame->commit();
			$this->vrat .= "<div class=\"okmsg\">Editace byla provedena</div><br />";

			It6_Log::info(
				"Bet adds were updated.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('type' => intval($podtyp_id))
			);
		}
	}


	/**
	* edituje data hot sazek
	* @return void
	*/
	private function UpdateHot() {
		foreach($_POST['hot'] as $ternoBet) {
			if(!empty($ternoBet))
				$submitedBets[] = $ternoBet;
		}
	
		if(count(array_unique($submitedBets)) != count($submitedBets))
			$this->vrat = '<div class="errormsg">'.I18n::tr('bet_id_double_entry').'</div><br />';

		else {
			$placeholder = '	';
			foreach($submitedBets as $bet) {
				$placeholder .= '?,';
			}
			$placeholder = substr($placeholder,0,-1);
				
			$sql = "SELECT sazka_id FROM sazky WHERE sazka_id IN ($placeholder) AND typ_id IN (19,22)";
			$dbRes = $this->dbGame->query($sql, $submitedBets);
			if (DB::isError($dbRes))
				throw new ExHandler($dbRes->getMessage().'Unable to find existing ot bets.',"admin_ex_db");
			
			while($row = $dbRes->fetchRow()) {
				$approvedBets[] = $row['sazka_id'];
			}

			$nonAproovedBets = array_diff($submitedBets, $approvedBets);
			if(!empty($nonAproovedBets))
				$this->vrat = '<div class="errormsg">'.I18n::tr('bet_not_required_type').'</div><br />';
				
			else {
				$this->dbGame->autocommit(false);

				$sth = $this->dbGame->prepare("DELETE FROM hot_bet");
				if (DB::isError($sth))
					throw new ExHandler($sth->getMessage(),"admin_ex_db");

				$sth2 = $this->dbGame->prepare("INSERT INTO  hot_bet VALUES(?)");
				if (DB::isError($sth))
					throw new ExHandler($sth->getMessage(),"admin_ex_db");

				$res2 =& $this->dbGame->execute($sth);
				if (DB::isError($res2))
					throw new ExHandler($res2->getMessage().'Nepodarilo se smazat hot sazky',"admin_ex_db");

				foreach($_POST['hot'] as $h) {
					if(!empty($h)) {
						$h = intval($h);

						$res2 =& $this->dbGame->execute($sth2, array($h));
						if (DB::isError($res2))
							throw new ExHandler($res2->getMessage().'Nepodarilo se vlozit hot sazky',"admin_ex_db");
					}
				}

				It6_GlobalCache_Invalidator::invalidateTernoBets();
				$this->vrat .= '<div class="okmsg">Editace terno sázek byla provedena</div><br />';

				It6_Log::info(
					"Terno bets were updated.",
					It6_Log::TAG_ADMIN_OPERATION
				);

				$this->dbGame->commit();
			}
		}
	}



    /**
 * vypise hot sazky
 * @return void
 */
  private function ShowHot(){


  	$this->vrat .= '<h3>Terno sázky na Homepage</h3>';

  	$this->vrat .= '<em>Zadávejte pouze <strong>1 X 2</strong> a <strong>1 2</strong></em>';

  	$this->vrat .= '<form method="post" action="?section=153"><table class="unitable" >';

  	$sth = $this->dbGame->prepare("SELECT h.sazka_id,s.text,s.platna_do,(s.platna_do>NOW()) AS is_valid FROM hot_bet h LEFT JOIN sazky s USING(sazka_id) ORDER BY s.platna_do ASC, h.sazka_id ASC");
    if (DB::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");

//    $sth2 = $this->dbGame->prepare("select status from sazky where platna_do>now() and sazka_id=?");
//    if (DB::isError($sth2))  throw new ExHandler($sth2->getMessage(),"admin_ex_db");

    $res2 =& $this->dbGame->execute($sth);
    if (DB::isError($res2))  throw new ExHandler($res2->getMessage().'Nepodarilo se aktualizovat adminy',"admin_ex_db");

    $hot = array();
    while ($row =& $res2->fetchRow()){
    	$hot[] = array(
    		'id' => $row['sazka_id'],
    		'name' => $row['text'],
    		'isValid' => !empty($row['is_valid']),
    		'validTo' => It6_Date::fromDb($row['platna_do']),
    	);
    }

    $colCount = 2;
    $open = false;
    for($x=0; $x<10; $x++){

    	if(isset($hot[$x]))
    		$bet = $hot[$x];
    	else
    		$bet = null;

    	if (!$open) {
    		$this->vrat .= '<tr>';
    		$open = true;
    	}

    	$this->vrat .= '<td><input type="text" name="hot[]" '.(isset($bet) && !$bet['isValid']?'style="border:3px solid red; background: #fdd"':'').' value="'.(isset($bet)?$bet['id']:'').'"/></td>';
    	$this->vrat .= '<td>' . (isset($bet) ? "{$bet['name']} ({$bet['validTo']})" : '') . '</td>';

    	if (0 == ($x + 1) % $colCount) {
    		$this->vrat .= '</tr>';
    		$open = false;
    	}
    }
	if ($open)
    	$this->vrat .= '</tr>';
	$this->vrat .= '<tr><td colspan="2"><input type="submit" name="edit_hot" value="Uložit"/></td></tr>';
  	$this->vrat .= '<table></form><br /><br />';

  }

  /**
 * vypise data
 * @return void
 */
  private function ShowData(){

  	 $sth2 = $this->dbGame->prepare("select status from sazky where platna_do>now() and status=0 and sazka_id=?");
     if (DB::isError($sth2))  throw new ExHandler($sth2->getMessage(),"admin_ex_db");

  	 $page = array();
  	 $x = $y = $v = "";

  	 $sql = "select a.name,a.url,b.sazka_id,a.page_id from reklama_page a left join reklama_page_bet b on a.page_id=b.page_id order by a.page_id";
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vlozeni nove udalosti',"admin_ex_db");
     while ($row =& $res->fetchRow()){

     	if(!isset($page[$row['page_id']])){

     		 $x .=   "<td >#<input type=\\\"text\\\"  name=\\\"sazka[".$row['page_id']."][]\\\"  /></td>";
     		 $y .=   "<th><a href=\"".$row['url']."\">".Help::Html($row['name'])."</a></th>";
     		 $v .= '<td >#<input type="text"  name="sazka['.$row['page_id'].'][]"  /></td>';
     	}

     	if(ctype_digit($row['sazka_id']))$page[$row['page_id']][] = $row['sazka_id'];



     }

  	$this->vrat .= '<script>
                     function PlusBet(){
                      $("#myTab").append("<tr>'.$x.'<tr>");
                     }
                    </script>';


  	/* $this->vrat .= '';
  	 $this->vrat .= '<a href="javascript:PlusBet()">+</a>';
  	 $this->vrat .= '<form method="post" action="?section=153"><table >';
  	 $this->vrat .= '<tr>'.$y.'</tr>';

  	 $kl = array();


  	 foreach($page as $k2=>$h){

  	 	foreach($h as $k=>$h2){
  	 	   if(!isset($kl[$k])) $kl[$k] = '';
  	 	   $res2 =& $this->dbGame->execute($sth2,array($h2));
           if (DB::isError($res2))  throw new ExHandler($res2->getMessage().'Nepodarilo se aktualizovat adminy',"admin_ex_db");
           if ($row =& $res2->fetchRow()){$status=true;}else {$status=false;}

           $kl[$k][$k2] .= '<td >#<input type="text"  '.($status==false?'style="border:1px solid red"':'').' name="sazka['.$k2.'][]" value="'.$h2.'" /></td>';

  	 	}

  	 }

  	 $this->vrat .= '<tr id="myTab">'.$v.'<tr>';

  	// echo "<pre>";
  	 //



  	 foreach($kl as $k3=>$h){ //$k3 = radek
  	   $hod = '';

 	    foreach($page as $p_id=>$p_h){
  	   	 if(!isset($kl[$k3][$p_id]))  $hod  .= '<td>&nbsp;</td>';
  	     else $hod  .= $kl[$k3][$p_id];
  	    }

  	   $this->vrat .= '<tr>'.$hod.'</tr>';
  	 }

  	 $this->vrat .= '<tr><td colspan="1"><input type="submit" name="edit" value="Uložit"/></td></tr>';
  	 $this->vrat .= '<table></form>';


  	 */

  }


  /**
 * Nastaveni prav k sekci
 * @param int $update pravo zapisu
 * @param int $delete pravo smazani
 * @return void
 */
  public function setPrivileges($update,$delete){



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
