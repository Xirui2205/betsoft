<?php
/**
 * @package    main
 */

/**
 * Novinky
 *
 *
 * <code>
 *
 * </code>
 *
 * @package    Ciselniky
 */

class Novinky extends Template{

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
	
	$this->vrat .=  '<h3>'.I18n::tr('News').'</h3>';

   #Ulozeni nove novinky#
   if(isset($_REQUEST['create_news']) && isset($_REQUEST['n1'])){

     if($this->update){

        $this->CreateNews();

        $this->NewsForm();

     }else
        $this->vrat .= '<div class="errormsg">Nemáte právo provést tuto akci</div>';

   }
   #Vymazani novinky#
   else if(isset($_POST['delete']) && is_array($_POST['delete'])){

     if($this->delete){

        $this->DeleteNews(intval(key($_POST['delete'])));

        $this->ShowNews();

     }else
        $this->vrat .= '<div class="errormsg">Nemáte právo provést tuto akci</div>';

   }
   #Editace novinky#
   else if(isset($_POST['edit_news']) && isset($_POST['news_id'])){

     if($this->update){

        $this->UpdateNews(intval($_POST['news_id']));

        $this->EditNews(intval($_POST['news_id']));

     }else
        $this->vrat .= '<div class="errormsg">Nemáte právo provést tuto akci</div>';

   }

   #Detail novinky#
   else if(isset($_REQUEST['detail']) && is_array($_REQUEST['detail'])){

     if($this->update){

        $this->EditNews(intval(key($_REQUEST['detail'])));

     }else
        $this->vrat .= '<div class="errormsg">Nemáte právo provést tuto akci</div>';

   }
   #Vytvareni novinky#
   else if(isset($_REQUEST['create_news'])){

     $this->NewsForm();

   }
   else
      $this->ShowNews();



   $this->dbGame->disconnect();

  }

    /**
 * Aktualizace novinky
 * @param int $n_id id novinky
 * return void
 */
  private function UpdateNews($n_id){

    $status = true;

    if(!isset($_POST['platna_od']) || !It6_Date::checkFormat($_POST['platna_od'])) {$this->vrat .= "<div class=\"errormsg\"><strong>Platná od</strong> nemá správný formát</div><br />";$status = false;}
    if(!isset($_POST['platna_do']) || !It6_Date::checkFormat($_POST['platna_do'])) {$this->vrat .= "<div class=\"errormsg\"><strong>Platná do</strong> nemá správný formát</div><br />";$status = false;}
    if(!isset($_POST['nadpis']) || !is_array($_POST['nadpis'])) {$this->vrat .= "<div class=\"errormsg\">Žádná data</div><br />";$status = false;}
    if(It6_Date::toTimestamp($_POST['platna_od']) >= It6_Date::toTimestamp($_POST['platna_do'])){$this->vrat .= "<div class=\"errormsg\">Platná do nemůže být menší nežliplatná od</div><br />";$status = false;}


    if($status){

      $this->dbGame->autoCommit(false);

      $sql = "update novinky a set platna_od='".It6_Date::toDb($_POST['platna_od'])."',platna_do='".It6_Date::toDb($_POST['platna_do'])."',
              zobrazeno=".(isset($_POST['zobrazeno_all'])?1:0).",homepage=".(isset($_POST['hp'])?1:0).",
              sazky=".(isset($_POST['sazky'])?1:0).",
              hp_image='".Help::Slash($_POST['obr_hp'])."',promo_image='".Help::Slash($_POST['obr_other'])."',udalost_id=".intval($_POST['udalost']).",page_id=".intval($_POST['page'])." where a.novinka_id=".$n_id ;
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('aktualizace novinky',"admin_ex_db");}

      foreach($_POST['nadpis'] as $k2=>$h2){

        foreach($h2 as $k=>$h){

         if((isset($_POST['nadpis'][$k2][$k]) && mb_strlen($_POST['nadpis'][$k2][$k]) > 200) || (isset($_POST['anotace'][$k2][$k]) && mb_strlen($_POST['anotace'][$k2][$k]) > 500)) {$this->vrat .= "<div class=\"errormsg\">Nadpis může mít max. 200 znaků; Anotace 255 znaků</div><br />";$status = false;$this->dbGame->rollback();$this->dbGame->autoCommit(true);break;}

         $sql = "replace into  novinky_jazyk (data,anotace,nadpis,zobrazeno_jazyk,lang_id,novinka_id) values('".Help::Slash($_POST['html'][$k2][$k])."','".Help::Slash($_POST['anotace'][$k2][$k])."','".Help::Slash($_POST['nadpis'][$k2][$k])."',".(isset($_POST['zobrazeno'][$k2][$k])?1:0).",".$k.",".$n_id.")";
         $res =& $this->dbGame->query($sql);
         if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: update novinky_jazyk',"admin_ex_db");}


        }

      }

      $this->vrat .= "<div class=\"okmsg\">Novinka byla aktualizována</div><br />";

		It6_Log::info(
			"News story '%story%' was updated.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('story' => $n_id)
		);
      $this->dbGame->autoCommit(true);
      $this->dbGame->commit();

    }

  }


  /**
 * Editace novinky
 * @param int $n_id id novinky
 * return void
 */
  private function EditNews($n_id){

    $data = array();

    $sql = "
      SELECT *
      FROM novinky a
      INNER JOIN novinky_jazyk b ON a.novinka_id=b.novinka_id
      WHERE a.novinka_id=".$n_id
    ;
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vymazat novinku',"admin_ex_db");

    while($row =& $res->fetchRow()){

      if(!isset($data['platna_od'])){
        if($row['homepage'] == 1) $data['hp'] = 1;
        if($row['zobrazeno'] == 1) $data['zobrazeno_all'] = 1;
        if($row['sazky'] == 1) $data['sazky'] = 1;

        $data['obr_hp'] = $row['hp_image'];
        $data['obr_other'] = $row['promo_image'];
        $data['udalost'] = $row['udalost_id'];
        $data['page'] = $row['page_id'];
        $data['platna_od'] = It6_Date::fromDb($row['platna_od']);
        $data['platna_do'] = It6_Date::fromDb($row['platna_do']);
      }

      if($row['zobrazeno_jazyk'] == 1)
        $data['lang'][$row['lang_id']]['zobrazeno'] = 1;

      $data['lang'][$row['lang_id']]['nadpis'] = $row['nadpis'];
      $data['lang'][$row['lang_id']]['navstevnost'] = $row['navstevnost'];
      $data['lang'][$row['lang_id']]['anotace'] = $row['anotace'];
      $data['lang'][$row['lang_id']]['html'] = $row['data'];
    }

    $this->NewsForm($data,$n_id);
  }

  /**
 * Vymazani novinky
 * @param int $n_id id novinky
 * return void
 */
  private function DeleteNews($n_id){

   $sql = "DELETE FROM novinky WHERE novinka_id=".$n_id;
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vymazat novinku',"admin_ex_db");

   if($this->dbGame->affectedRows()){

      $this->vrat .= "<div class=\"okmsg\">Novinka byla úspěšně vymazána</div><br />";

		It6_Log::info(
			"News story '%story%' was deleted.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('story' => $n_id)
		);
   }

  }

   /**
 * Vytvoreni novinky
 * return void
 */
  private function CreateNews(){

    $status = true;

    if(!isset($_POST['platna_od']) || !It6_Date::checkFormat($_POST['platna_od'])) {$this->vrat .= "<div class=\"errormsg\"><strong>Platná od</strong> nemá správný formát</div><br />";$status = false;}
    if(!isset($_POST['platna_do']) || !It6_Date::checkFormat($_POST['platna_do'])) {$this->vrat .= "<div class=\"errormsg\"><strong>Platná do</strong> nemá správný formát</div><br />";$status = false;}
    if(!isset($_POST['nadpis']) || !is_array($_POST['nadpis'])) {$this->vrat .= "<div class=\"errormsg\">Žádná data</div><br />";$status = false;}
    if(It6_Date::toTimestamp($_POST['platna_od']) >= It6_Date::toTimestamp($_POST['platna_do'])){$this->vrat .= "<div class=\"errormsg\">Platná do nemůže být menší nežliplatná od</div><br />";$status = false;}

    if($status){

      $this->dbGame->autoCommit(false);

      $sql = "insert into novinky (platna_od,platna_do,zobrazeno,homepage,sazky,hp_image,promo_image,udalost_id,page_id)
              values('".It6_Date::toDb($_POST['platna_od'])."','".It6_Date::toDb($_POST['platna_do'])."',
              ".(isset($_POST['zobrazeno_all'])?1:0).",".(isset($_POST['hp'])?1:0).",".(isset($_POST['sazky'])?1:0).",
              '".Help::Slash($_POST['obr_hp'])."','".Help::Slash($_POST['obr_other'])."',".intval($_POST['udalost']).",".intval($_POST['page']).")";
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: vlozeni novinky',"admin_ex_db");}

      $sql = "select MAX(novinka_id) AS maxi from novinky";
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: max z novinek',"admin_ex_db");}

      if ($row =& $res->fetchRow()) $id = $row['maxi'];else{$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: max z novinek',"admin_ex_db");}


      foreach($_POST['nadpis'] as $k2=>$h2){

        foreach($h2 as $k=>$h){

         if((isset($_POST['nadpis'][$k2][$k]) && mb_strlen($_POST['nadpis'][$k2][$k]) > 200) || (isset($_POST['anotace'][$k2][$k]) && mb_strlen($_POST['anotace'][$k2][$k]) > 500)) {$this->vrat .= "<div class=\"errormsg\">Nadpis může mít max. 200 znaků; Anotace 255 znaků</div><br />";$status = false;$this->dbGame->rollback();$this->dbGame->autoCommit(true);break;}

         $sql = "insert into novinky_jazyk (novinka_id,lang_id,data,anotace,nadpis,zobrazeno_jazyk)
              values(".$id.",".$k.",'".Help::Slash($_POST['html'][$k2][$k])."','".Help::Slash($_POST['anotace'][$k2][$k])."',
              '".Help::Slash($_POST['nadpis'][$k2][$k])."',".(isset($_POST['zobrazeno'][$k2][$k])?1:0).")";
         $res =& $this->dbGame->query($sql);
         if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: vlozeni novinky_jazyk',"admin_ex_db");}


        }

      }

      $this->vrat .= "<div class=\"okmsg\">Novinka byla vytvorena</div><br />";

		It6_Log::info(
			"New news story '%story%' was created.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('story' => $n_id)
		);
      $this->dbGame->autoCommit(true);
      $this->dbGame->commit();

    }

  }

 /**
 * Formular detailu novinky
 * @param array $data pole obsahu pro vsechny jazyky
 * @param int $news_id id novinky
 * return void
 */
  private function NewsForm(array $data=NULL,$news_id=NULL){

     $jazyky = $pjazyky = $js = array();

     if($news_id==NULL) $news_id = 0;

     $sql = "select lang_id,iso,alt_text from jazyky where zobrazeno=1 order by lang_id";
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: update galerie',"admin_ex_db");

     while ($row =& $res->fetchRow()){

        $jazyky[$row['lang_id']] = $row['alt_text'].': '.$row['iso'];
        $pjazyky[] = $row['lang_id'];
        $js[] = '\'html['.$news_id.']['.$row['lang_id'].']\'';

     }

     $this->vrat .= '<script language="javascript" type="text/javascript">
                     <!--
                     SetWysiwig2(['.implode(",",$js).']);
                    // -->
                    </script>';
     $this->vrat .= "<form method=\"post\"  action\"?create_news=1&section=".$this->section."\">";

     $x = 0;

    foreach($jazyky as $k=>$h){
      $this->vrat .= '
        <input
          type="button"
          id="data_b_'.$k.'"
          name="data_b_'.$k.'"
          onclick="NewsLang(new Array('.implode(',',$pjazyky).'),'.$k.')" '.($x==0?'style="color:black;font-weight:bold;border:2px solid black"':'').'
          class="sinput3"
          value="'.Help::Html($h).'" />
        &nbsp;&nbsp;';

      $x++;
    }

	$this->vrat .= '
		<div class="right">
			<h3>Konfigurace</h3>
			<table>

				<tr>
					<td>Platná od:</td>
					<td>
						<input
							type="text"
							class="mandatory dateTime"
							name="platna_od"
							id="platna_od"
							value="'.(isset($_POST['platna_od'])?Help::Html($_POST['platna_od']):(isset($data['platna_od'])?Help::Html($data['platna_od']):"")).'"
						/>
						<img src="_clip/calendar.gif" class="calendar-icon">
						&nbsp;
					</td>
				</tr>

				<tr>
					<td>Platná do:</td>
					<td>
						<input
							type="text"
							class="mandatory dateTime"
							name="platna_do"
							id="platna_do"
							value="'.(isset($_POST['platna_do'])?Help::Html($_POST['platna_do']):(isset($data['platna_do'])?Help::Html($data['platna_do']):"")).'"
						/>
						<img src="images/ico/calendar.gif" class="calendar-icon">
						&nbsp;
					</td>
				</tr>

				<tr>
					<td>Zobrazeno:</td>
					<td>
						<input
							type="checkbox"
							class="no"
							'.(isset($_POST['zobrazeno_all']) || isset($data['zobrazeno_all'])?"checked=\"checked\"":"").'
							name="zobrazeno_all"
						/>
					</td>
				</tr>

<!--
  <tr><td>Homepage:</td><td> <input type="checkbox" '.(isset($_POST['hp']) || isset($data['hp'])?"checked=\"checked\"":"").' class="no" name="hp" /></td></tr>
  <tr><td>Sázky:</td><td> <input type="checkbox" '.(isset($_POST['sazky']) || isset($data['sazky'])?"checked=\"checked\"":"").' class="no" name="sazky" /></td></tr>
-->

				<tr>
					<td>Obrázek:</td>
					<td>
						<input
							type="text"
							value="'.(isset($_POST['obr_hp'])?Help::Html($_POST['obr_hp']):(isset($data['obr_hp'])?Help::Html($data['obr_hp']):"")).'"
							name="obr_hp"
							id="obr_hp"
						/>
						<a href="javascript:window.open(\'/galery.php?type=1&node=obr_hp\',\'\',\'width=640,height=480,scrollbars=yes\');void(0);">
							<img src="_clip/image_aktivni.gif" alt="" class="img" />
						</a>
					</td>
				</tr>
<!--
  <tr><td>Obrázek ostatní:</td><td> <input type="text" value="'.(isset($_POST['obr_other'])?Help::Html($_POST['obr_other']):(isset($data['obr_other'])?Help::Html($data['obr_other']):"")).'" name="obr_other" id="obr_other" /> <a href="javascript:window.open(\'/galery.php?type=1&node=obr_other\',\'\',\'width=640,height=480,scrollbars=yes\');void(0);"><img src="_clip/image_aktivni.gif" alt="" class="img" /></a></td></tr>
-->
				<tr>
					<td>Idetifikátor ligy:</td>
					<td>
						<input
							type="text"
							value="'.(isset($_POST['udalost'])?Help::Html($_POST['udalost']):(isset($data['udalost'])?Help::Html($data['udalost']):"")).'"
							name="udalost"
							id="udalost"
						/>
						<a href="javascript:window.open(\'/udalost.php?node=udalost\',\'\',\'width=640,height=480,scrollbars=yes\');void(0);">
							Zvolit
						</a>
					</td>
				</tr>

				<tr>
					<td>Identifikátor stránky:</td>
					<td>
						<input
							type="text"
							value="'.(isset($_POST['page'])?Help::Html($_POST['page']):(isset($data['page'])?Help::Html($data['page']):"")).'"
							name="page"
							id="page"
						/>
					</td>
				</tr>
			</table>
		</div>';
      $x = 0;
      foreach($jazyky as $k=>$h){

        $this->vrat .= '
          <div style="'.($x==0?'display:block':'display:none').'" id="data_'.$k.'" name="data_'.$k.'">
            <br />
            Zobrazeno:
            <input
              type="checkbox"
              '.(isset($_POST['zobrazeno'][$news_id][$k]) || isset($data['lang'][$k]['zobrazeno']) || ($news_id == 0 && !isset($_POST['zobrazeno'][$news_id][$k]) && !isset($_POST['platna_od']))?"checked=\"checked\"":"").'
              class="no"
              name="zobrazeno['.$news_id.']['.$k.']" />
            '.($data != NULL && isset($data['lang'][$k]['navstevnost'])?'<br />Návštěvnost: '.$data['lang'][$k]['navstevnost']:'').'
            <br />
            Nadpis:
            <input
              type="text"
              maxlength="100"
              name="nadpis['.$news_id.']['.$k.']"
              value="'.(isset($_POST['nadpis'][$news_id][$k])?Help::Html($_POST['nadpis'][$news_id][$k]):(isset($data['lang'][$k]['nadpis'])?Help::Html($data['lang'][$k]['nadpis']):"")).'" />
            <br />
            Anotace:
            <br />
            <textarea name="anotace['.$news_id.']['.$k.']" cols="70" rows="5">'.(isset($_POST['anotace'][$news_id][$k])?Help::Html($_POST['anotace'][$news_id][$k]):(isset($data['lang'][$k]['anotace'])?Help::Html($data['lang'][$k]['anotace']):"")).'</textarea>
            <br />
            Text:
            <br />
            <textarea name="html['.$news_id.']['.$k.']" cols="70" rows="5">'.(isset($_POST['html'][$news_id][$k])?$_POST['html'][$news_id][$k]:(isset($data['lang'][$k]['html'])?$data['lang'][$k]['html']:"")).'</textarea></div>
        ';

        $x++;
      }



     if($news_id == NULL) $this->vrat .= '<input type="submit" value="Vytvořit" name="create_news" /><input type="hidden" name="n1" value="1" />';
     else $this->vrat .= '<input type="submit" value="Uložit" name="edit_news" /><input type="hidden" name="news_id" value="'.$news_id.'" />';

     $this->vrat .= '</form>';

  }

  /**
 * Zobrazeni novinek
 * return void
 */
  private function ShowNews(){

    $this->vrat .= "<form method=\"post\"  action\"?section=".$this->section."\">";

    $this->vrat .= '<table class="table-list">';

    $this->vrat .= '<thead><tr><th><a href="?section='.$this->section.'&order=6'.(isset($_GET['order']) && $_GET['order'] == 6 && isset($_GET['asc'])?"&desc=1":"&asc=1").'">ID</a></th><th>Nadpis</th><th><a href="?section='.$this->section.'&order=5'.(isset($_GET['order']) && $_GET['order'] == 5 && isset($_GET['asc'])?"&desc=1":"&asc=1").'">Platná od</a></th><th><a href="?section='.$this->section.'&order=4'.(isset($_GET['order']) && $_GET['order'] == 4 && isset($_GET['asc'])?"&desc=1":"&asc=1").'">Platná do</a></th><th><a href="?section='.$this->section.'&order=3'.(isset($_GET['order']) && $_GET['order'] == 3 && isset($_GET['asc'])?"&desc=1":"&asc=1").'">Zobrazeno</a></th><th><a href="?section='.$this->section.'&order=1'.(isset($_GET['order']) && $_GET['order'] == 1 && isset($_GET['asc'])?"&desc=1":"&asc=1").'"></a></th><th colspan="2"></th></tr></thead>';

    $sql = "select novinka_id from novinky";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: slelect z novinek',"admin_ex_db");

    $page = new Page($res->numRows(),PAGE,"section=".$this->section.(isset($_GET['order'])?"&order=".intval($_GET['order']):"").(isset($_GET['asc'])?"&desc=1":""));

    $order = "a.novinka_id desc";

    if(isset($_GET['order'])){

      switch($_GET['order']){

       case 1:$order = "a.hry";break;
       case 2:$order = "a.homepage";break;
       case 3:$order = "a.zobrazeno";break;
       case 4:$order = "a.platna_do";break;
       case 5:$order = "a.platna_od";break;
       case 6:$order = "a.novinka_id";break;
       case 7:$order = "a.sazky";break;

      }

    }

    if(isset($_GET['desc'])) $order .= " desc";

    $sql = "select * from novinky a inner join novinky_jazyk b on a.novinka_id=b.novinka_id where b.lang_id=1 order by ".$order." limit ".(PAGE*$page->page).",".PAGE;
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: slelect z novinek',"admin_ex_db");

    $this->vrat .= $page->getPage().' Počet záznamů: '.$page->numRows.'<br /><br />';

    while ($row =& $res->fetchRow()){

      $this->vrat .= '<tr><td>#'.$row['novinka_id'].'</td><td>'.Help::Html($row['nadpis']).'</td><td>'.It6_Date::fromDb($row['platna_od']).'</td><td>'.It6_Date::fromDb($row['platna_do']).'</td><td>'.($row['zobrazeno']==1?"ANO":"NE").'</td><td></td><td><input type="submit" name="delete['.$row['novinka_id'].']" value="Smazat" onclick="if(!confirm(\'Opravdu chcete smazat novinku?\'))return false;" class="sbutton" /></td><td><input type="submit" name="detail['.$row['novinka_id'].']" value="Detail" class="sbutton" /></td></tr>';
    }

    $this->vrat .= '</table>';

    $this->vrat .= '<br />'.$page->getPage().'<br /><br />';
    $this->vrat .= '<input type="submit" value="Vytvořit novinku" name="create_news" />';
    $this->vrat .= '</form>';

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
