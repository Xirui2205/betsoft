<?php



 /**
 * Trida pro praci s financemi
 *
 * V této třídě se používají číselné názvy sekcí,
 * kdyby došlo k jejich změně musí se natvrdo změni i zde.
 * Metoda Finnace::RunAction
 *
 * @package    main
 */

class Finance{

/**
 * vystupni XML response
 * @access private
 * @var int
 */
 private $vrat;

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
* @param PEAR::DB $db objekt spojeni s databazi
*/
  public function __construct($section=0,$db=null,$dbGame=null){

  $this->section =  $section;


   if($dbGame == null){

    $this->dbGame = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
    if (DB::isError($this->dbGame)) {
      throw new ExHandler($this->dbGame->getMessage(),"admin_ex_db");
    }
    $this->dbGame->setFetchMode(DB_FETCHMODE_ASSOC);
    $sql = "set names 'utf8'";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");


   }else
        $this->dbGame =  $dbGame;

  }

 /**
 * Metoda spousti jednitlove metody podle stavu
 * @return void
 */
  public  function runAction(){

    if($this->section == 62){

      if(isset($_POST['new'])) $this->NovyKurz();
      else if(isset($_POST['vyber']) && isset($_POST['id_kurz']) && is_numeric($_POST['id_kurz']) && isset($_POST['od']) && isset($_POST['do']) && mb_strlen($_POST['od']) > 0 && mb_strlen($_POST['do']) > 0) $this->EditKurz();

      $this->ShowKurz();

    }
    else if($this->section == 58){
      return;
        $this->ShowBalance();

    }

    else if($this->section == 63){
//return;
        //$this->ShowUser();
        $this->ShowUserBalanceStat();

    }

    else if($this->section == 64){
        return;
        if(isset($_POST['send'])){

          if($this->update)
            $this->SendOrder();
          else
           $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro editování v této sekci</div>\n";

        }

        $this->PlatebniOperace();

    }

    else if($this->section == 65){
        return;
      $this->Vypis();

    }
    else if($this->section == 129){

      $this->_getPosledniUzitiMetod();

    }

    $this->dbGame->disconnect();
  }

  private function _getPosledniUzitiMetod(){


      $sql = "SELECT
                FROM_UNIXTIME(MAX(a.acctrans_T)) AS dt_posledni_pouziti,
                b.`mena_text` AS s_mena,
                d.text AS s_metoda,
                CASE WHEN a.`acctrans_sign` = 1 THEN 'vklad' ELSE 'výběr' END AS s_smer
                FROM accTrans a
                JOIN mena b ON (a.`acctrans_CUR` = b.`mena_id`)
                JOIN method c ON (c.`met_sign` = a.`acctrans_sign` AND c.`met_namex` = a.`acctrans_met`)
                JOIN (SELECT * FROM preklady a
                        WHERE
                            a.`index_pole` LIKE 'pay_method%' AND
                            a.`lang_id` = 1) d ON (d.index_pole = CONCAT('pay_method_', c.`met_namex`))
                JOIN uzivatel e ON (e.user_id = a.`acctrans_USR`)
                WHERE e.`e_testovaci` = 'ne'
                GROUP BY a.`acctrans_CUR`, a.`acctrans_met`, a.`acctrans_sign`
                ORDER BY a.`acctrans_sign` ASC,  1 ASC";

      $res =& $this->dbGame->query($sql);
      if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

      $a = 0;
      $ret = "<table width=\"100%\" style=\"border-collapse:collapse;\">
                <tr style=\"background-color: #9BE1FB\">
                    <td>Poslední použití</td>
                    <td>Měna</td>
                    <td>Metoda</td>
                    <td>Směr</td>
                </tr>";
      while ($row =& $res->fetchRow()){

        if(Help::isNewNode($row['s_smer']) && $a++ > 0)
            $ret .= "<tr><td colspan=\"4\">-----------------------------------------------------------</td></tr>";

        $ret .= "<tr>
            <td>" . $row['dt_posledni_pouziti'] . "</td>
            <td>" . $row['s_mena'] . "</td>
            <td>" . $row['s_metoda'] . "</td>
            <td>" . $row['s_smer'] . "</td>
            </tr>";

      }
      $ret .= "</table>";

      $this->vrat = $ret;

  }

 /**
 * Vypis plateb
 * @return void
 */
  private function Vypis(){

   $mena_ob = new Mena();
   $res = $mena_ob->selectData();
   $mena = array();
   $menaopt = "";

   while($row =& $res->fetchRow()){

     $mena[$row['mena_id']] = $row['mena_text'];
     $menaopt .= "<option  value=\"".$row['mena_id']."\" ".(isset($_REQUEST['mena']) && $_REQUEST['mena']==$row['mena_id']?"selected=\"selected\"":"" ).">".Help::Html($row['mena_text'])."</option>";

   }

   $plm_ob = new PlatebniMetodyKolekce();
   $res = $plm_ob->selectData();
   $plmetoda = "";

   while($row =& $res->fetchRow()){

     $plmetoda .= "<option  value=\"".$row['metoda_id']."\" ".(isset($_REQUEST['platebni_metoda']) && $_REQUEST['platebni_metoda']==$row['metoda_id']?"selected=\"selected\"":"" ).">".Help::Html($row['nazev'])."</option>";

   }


   $admin = It6_Models_Admin::readAll();
//   $admin = array();
//   while($row =& $res->fetchRow()){
//
//    $admin[$row['admin_id']] = $row['nick'];
//
//   }

   $user_ob = new UserKolekce();
   $res = $user_ob->selectData();
   $uzivatel = "";
   while($row =& $res->fetchRow()){

     $uzivatel .= "<option  value=\"".$row['user_id']."\" ".(isset($_REQUEST['user']) && $_REQUEST['user']==$row['user_id']?"selected=\"selected\"":"" ).">".Help::Html($row['nick'])."</option>";

   }

    $this->vrat .= '
      <h2>Výpis</h2>
        <form method="post" action="?section='.$this->section.'">
          <table style="border:1px solid #E3E3E3;font-size:0.9em;background:#708090;color:white">
            <tr>
              <td class="textleft" style="background:#929EAD;" colspan="2">Vyhledávání</td>
            </tr>

            <tr>
              <td><strong>Datum od</strong></td>
              <td>
                <input
                  type="text"
                  value="'.(isset($_REQUEST['datumod']) && mb_strlen($_REQUEST['datumod']) > 0?Help::Html($_REQUEST['datumod']):"").'"
                  name="datumod"
                />
                <a href="javascript:window.open(\'calendar.php?cas=datumod\',\'\',\'width=140,height=247\');void(0);">
                  <img src="images/ico/calendar.gif" alt="Kalendář" class="img" /></a></td></tr>';
    $this->vrat .= '<tr><td><strong>Datum do (dd.mm.RRRR HH:mm:ss)</strong> </td><td><input type="text" value="'.(!isset($_REQUEST['datumdo']) || mb_strlen($_REQUEST['datumdo']) < 1?date("d.m.Y H:i:s"):(isset($_REQUEST['datumdo']) && mb_strlen($_REQUEST['datumdo']) > 0?Help::Html($_REQUEST['datumdo']):"")).'" name="datumdo" /><a href="javascript:window.open(\'calendar.php?cas=datumdo\',\'\',\'width=140,height=247\');void(0);"><img src="images/ico/calendar.gif" alt="Kalendář" class="img" /></a></td></tr>';
    $this->vrat .= '<tr><td valign="top"><strong>Uživatel</strong> </td><td><select name="user" size="4">
                    <option value="all" >-- Vše --</option>
                    '.$uzivatel.'
                    </select></td></tr>';
    $this->vrat .= '<tr><td valign="top"><strong>Platební metoda</strong> </td><td><select name="platebni_metoda">
                    <option value="all" >-- Vše --</option>
                    '.$plmetoda.'
                    </select></td></tr>';
    $this->vrat .= '<tr><td><strong>Vyhledat</strong> </td><td><select name="typ">
                        <option value="all">-- Vše --</option>
                        <option value="1" '.(isset($_REQUEST['typ']) && $_REQUEST['typ'] == 1?"selected=\"selected\"":"").'>Bez manuálních příkazů</option>
                        <option value="2" '.(isset($_REQUEST['typ']) && $_REQUEST['typ'] == 2?"selected=\"selected\"":"").'>Pouze manuální příkazy</option>
                        <option value="3" '.(isset($_REQUEST['typ']) && $_REQUEST['typ'] == 3?"selected=\"selected\"":"").'>Vklady</option>
                        <option value="4" '.(isset($_REQUEST['typ']) && $_REQUEST['typ'] == 4?"selected=\"selected\"":"").'>Výběry</option>
                        </select></td></tr>';
    $this->vrat .= '<tr><td><strong>Měna</strong> </td><td><select name="mena">
                        <option value="all">-- Vše --</option>'.$menaopt.'</td></tr>';
    $this->vrat .= '<tr><td><strong>Nezobrazovat book uživatele</strong> </td><td><input type="checkbox" '.(isset($_REQUEST['book'])?"checked=\"checked\"":"").' class="no" name="book" value="" /></td></tr>';
    $this->vrat .= '<tr><td colspan="2"><input type="submit" name="search"  value="Vyhledat"></td></tr>';

    $this->vrat .= '</table><br /><br />';

    $this->vrat .= "</form>";

    if(isset($_REQUEST['datumod']) && It6_Date::checkFormat($_REQUEST['datumod']))
      list($den,$mesic,$rok,$hod,$min,$sec) = split("[.: ]",$_REQUEST['datumod']);
    else $_REQUEST['datumod'] = "";
    if(isset($_REQUEST['datumdo']) && It6_Date::checkFormat($_REQUEST['datumdo']))
      list($den2,$mesic2,$rok2,$hod2,$min2,$sec2) = split("[.: ]",$_REQUEST['datumdo']);
    else $_REQUEST['datumdo'] = "";

    $where = "where ";

    if(isset($_REQUEST['user']) && $_REQUEST['user'] != "all") $where .= "user_id=".intval($_REQUEST['user'])." and ";
    if(isset($_REQUEST['platebni_metoda']) && $_REQUEST['platebni_metoda'] != "all") $where .= "metoda_id=".intval($_REQUEST['platebni_metoda'])." and ";
    if($_REQUEST['datumod'] != "") $where .= "datum>='".$rok."-".$mesic."-".$den." ".$hod.":".$min.":".$sec."' and ";
    if($_REQUEST['datumdo'] != "") $where .= "datum<='".$rok2."-".$mesic2."-".$den2." ".$hod2.":".$min2.":".$sec2."' and ";
    if(isset($_REQUEST['mena']) && is_numeric($_REQUEST['mena'])) $where .= "mena_id=".$_REQUEST['mena']." and ";
    if(isset($_REQUEST['book'])) $where .= "user_id not in(".implode(",",$GLOBALS['bookUSER']).") and";
    if(isset($_REQUEST['typ'])){

      switch($_REQUEST['typ']){
       case 1: $where .= " manual=0 and";break;
       case 2: $where .= " manual<>0 and";break;
       case 3: $where .= " vklad=1 and vyber=0 and";break;
       case 4: $where .= " vklad=0 and vyber=1 and";break;
      }

    }

    $where = substr($where,0,-4);

    $sql = "select * from user_balance ".$where;
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky user_balance',"admin_ex_db");

    $page = new Page($res->numRows(),PAGE,"section=".$this->section.(isset($_REQUEST['book'])?"&book=1":"")."&datumod=".(isset($_REQUEST['datumod'])?urlencode($_REQUEST['datumod']):"")."&datumdo=".(isset($_REQUEST['datumdo'])?urlencode($_REQUEST['datumdo']):"")."&typ=".(isset($_REQUEST['typ'])?urlencode($_REQUEST['typ']):"")."&mena=".(isset($_REQUEST['mena'])?urlencode($_REQUEST['mena']):""));

    $vkladsum = $vybersum = "NaN";
    if(isset($_REQUEST['mena']) && is_numeric($_REQUEST['mena'])){
     $sql = "select SUM(castka) AS suma,vklad  from user_balance ".$where." group by vklad";
     $res3 =& $this->dbGame->query($sql);
     if(DB::isError($res3)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky user_balance',"admin_ex_db");

     while($row3 =& $res3->fetchRow()){

       if($row3['vklad'] == 1) $vkladsum = $row3['suma'];
       else $vybersum = $row3['suma'];

     }
    }

    $sql = "select * from user_balance ".$where." order by ".(isset($_GET['order']) && isset($_GET['desc'])?$_GET['order']." ".$_GET['desc']:"datum desc")." limit ".(PAGE*$page->page).",".PAGE;
    $res2 =& $this->dbGame->query($sql);
    if(DB::isError($res2)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky user_balance',"admin_ex_db");

    $this->vrat .= '<em>Veškeré vklady a výběry nepatří sem transakce s žetony.<br />Celkové veličiny se zobrazují pouze v případě filtru podle určité měny.<br />Tečka odděluje desetinná místa</em><br /><br />';

    $this->vrat .= '<table style="width:100%">';
    $this->vrat .= '<tr><td colspan="2">Počet záznamů: <strong>'.$res->numRows().'</strong></td><td colspan="2" class="textcenter" style="background:#909090;color:white;">Výběr ('.$vybersum.') Vklad ('.$vkladsum.')</td><td colspan="3" class="textright">'.$page->getPage().'</td></tr>';
    $this->vrat .= "<tr><th><br></th>
                    <th><a href=\"?section=".$this->section.(isset($_REQUEST['book'])?"&book=1":"")."&order=datum&desc=".(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc")."&datumod=".(isset($_REQUEST['datumod'])?urlencode($_REQUEST['datumod']):"")."&datumdo=".(isset($_REQUEST['datumdo'])?urlencode($_REQUEST['datumdo']):"")."&typ=".(isset($_REQUEST['typ'])?urlencode($_REQUEST['typ']):"")."&mena=".(isset($_REQUEST['mena'])?urlencode($_REQUEST['mena']):"")."\">Datum</a></th>
                    <th><a href=\"?section=".$this->section.(isset($_REQUEST['book'])?"&book=1":"")."&order=user_id&desc=".(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc")."&datumod=".(isset($_REQUEST['datumod'])?urlencode($_REQUEST['datumod']):"")."&datumdo=".(isset($_REQUEST['datumdo'])?urlencode($_REQUEST['datumdo']):"")."&typ=".(isset($_REQUEST['typ'])?urlencode($_REQUEST['typ']):"")."&mena=".(isset($_REQUEST['mena'])?urlencode($_REQUEST['mena']):"")."\">Uživatel</a></th>
                    <th><a href=\"?section=".$this->section.(isset($_REQUEST['book'])?"&book=1":"")."&order=metoda_id&desc=".(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc")."&datumod=".(isset($_REQUEST['datumod'])?urlencode($_REQUEST['datumod']):"")."&datumdo=".(isset($_REQUEST['datumdo'])?urlencode($_REQUEST['datumdo']):"")."&typ=".(isset($_REQUEST['typ'])?urlencode($_REQUEST['typ']):"")."&mena=".(isset($_REQUEST['mena'])?urlencode($_REQUEST['mena']):"")."\">Metoda</a></th>
                    <th><a href=\"?section=".$this->section.(isset($_REQUEST['book'])?"&book=1":"")."&order=castka&desc=".(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc")."&datumod=".(isset($_REQUEST['datumod'])?urlencode($_REQUEST['datumod']):"")."&datumdo=".(isset($_REQUEST['datumdo'])?urlencode($_REQUEST['datumdo']):"")."&typ=".(isset($_REQUEST['typ'])?urlencode($_REQUEST['typ']):"")."&mena=".(isset($_REQUEST['mena'])?urlencode($_REQUEST['mena']):"")."\">Částka</a></th>
                    <th>Typ</th>
                    <th><a href=\"?section=".$this->section."&order=manual&desc=".(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc")."&datumod=".(isset($_REQUEST['datumod'])?urlencode($_REQUEST['datumod']):"")."&datumdo=".(isset($_REQUEST['datumdo'])?urlencode($_REQUEST['datumdo']):"")."&typ=".(isset($_REQUEST['typ'])?urlencode($_REQUEST['typ']):"")."&mena=".(isset($_REQUEST['mena'])?urlencode($_REQUEST['mena']):"")."\">Manual</a></th></tr>";
    $x = 1;
    while($row =& $res2->fetchRow()){

      $this->vrat .= '<tr onmouseover="this.style.backgroundColor=\'#909090\';this.style.color=\'white\'" onmouseout="this.style.backgroundColor=\'\';this.style.color=\'#515B73\'">
                      <td class="textcenter" style="width:30px;">'.($x+(PAGE*$page->page)).'.</td>
                      <td class="textcenter">'.Help::Html($row['datum']).'</td><td class="textcenter">'.Help::Html($row['nick']).'</td>
                      <td class="textcenter">'.Help::Html($row['nazev']).'</td>
                      <td class="textcenter">'.($row['vklad']==1?" ":"- ").$row['castka'].' '.$mena[$row['mena_id']].'</td>
                      <td class="textcenter">'.($row['vklad']==1?"vklad":"výběr").'</td>
                      <td class="textcenter">'.Help::Html($admin[$row['manual']]).'</td></tr>';

     $x++;
    }

    $this->vrat .= '<tr><td colspan="7" class="textright">'.$page->getPage().'</td></tr>';
    $this->vrat .= '</table>';
    $this->vrat .= '<br /><br />';

  }

 /**
 * Odeslání manuální platební operace
 * @return void
 */
  private function SendOrder(){

    $status = true;

    if (!isset($_POST['typ']) || ($_POST['typ'] != "vklad" && $_POST['typ'] != "vyber")){$this->vrat .= "<div class=\"errormsg\">Typ platby je povinná položka</div><br />";$status = false;}
    if (!isset($_POST['metoda']) || !is_numeric($_POST['metoda'])){$this->vrat .= "<div class=\"errormsg\">Platební metoda je povinná položka</div><br />";$status = false;}
    if (!isset($_POST['castka']) || !is_numeric($_POST['castka'])){$this->vrat .= "<div class=\"errormsg\">Částka musí být uvedena a obsahovat pouze čísla a desetinné tečky</div><br />";$status = false;}
    if (!isset($_POST['datum']) || !It6_Date::checkFormat($_POST['datum'])){$this->vrat .= "<div class=\"errormsg\">Datum musí být uvedeno a mít správný tvar (Př: 12.4.2005 01:20:00)</div><br />";$status = false;}
    if (!isset($_POST['user']) || !is_numeric($_POST['user'])){$this->vrat .= "<div class=\"errormsg\">Vyberte uživatele</div><br />";$status = false;}
    if (!isset($_POST['mena']) || !is_numeric($_POST['mena'])){$this->vrat .= "<div class=\"errormsg\">Chybí měna</div><br />";$status = false;}
    if (isset($_POST['typ']) && $_POST['typ'] == "vyber" && isset($_POST['castka']) && is_numeric($_POST['castka'])){

      $book = $this->bookSum($_POST['mena']);
      $vklad_celkem = $this->SumVklad($_POST['mena']);
      $vyber_celkem = $this->SumVyber($_POST['mena']);

      $disp = ($vklad_celkem-$vyber_celkem+($book['vklad']-$book['vyber']));
       //echo $disp;
      if($_POST['castka'] > $disp){
         $this->vrat .= "<div class=\"errormsg\">Pokoušíte se vybrat částku větší nežli je disponibilní zůstatek</div><br />";$status = false;
      }



    }
    if(isset($_POST['pripsat']) && $_POST['typ'] == "vyber" && $status && !in_array($_POST['user'],$GLOBALS['BOOK'])){

      $sql = "select zustatek from uzivatel_im_data where user_id=".$_POST['user'];
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

      if ($row =& $res->fetchRow()){
        if($row['zustatek']<(round($_POST['castka'],4))){
          $this->vrat .= "<div class=\"errormsg\">Pokoušíte se u uživatele vybrat částku větší nežli je jeho zůstatek</div><br />";$status = false;
        }
      }

    }

    #Kontrola zapnute platebni metody pro vklad/vyber a zapnute platebni metody pro menu uzivatele#
    $sql = "select vklad,vyber,zobrazeno from platebni_metody where ".($_POST['typ'] == "vklad"?"vklad":"vyber")."=1 and metoda_id=".intval($_POST['metoda']);
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

    if ($data = $res->fetchRow()){
      if ($data['zobrazeno'] == 0){
        $this->vrat .= "<div class=\"errormsg\">Platební metoda je vypnutá </div><br />";$status = false;
      }
    }else{

       $this->vrat .= "<div class=\"errormsg\">Platební metoda je vypnutá pro ".($_POST['typ'] == "vklad"?"vklad":"výběr")."</div><br />";$status = false;

    }
    ####
    #Kontrola prirazene zeme a meny k metode neplati pro  uzivatele#
    $poplatky = 0;
    if(!in_array($_POST['user'],$GLOBALS['BOOK']) && $status){

     $uzivatel = new UserKolekce();
     $res = $uzivatel->selectData("where user_id=".$_POST['user']);
     if ($data = $res->fetchRow()) {$vyber_status = $data['vyber_status'];$zeme = $data['zeme_id'];$mena = $data['mena_id'];} else {$zeme = 0;$mena =0;}

     $sql = "select * from platebni_metoda_zeme where zeme_id=".intval($zeme)." and metoda_id=".intval($_POST['metoda']);
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

     if (!$res->fetchRow()){
           $this->vrat .= "<div class=\"errormsg\">Pro tuto platební metodu není povolena země zvoleného uživatele</div><br />";$status = false;
     }

     $sql = "select * from platebni_metoda_mena where vyber_vklad=".($_POST['typ'] == "vklad"?1:2)." and mena_id=".intval($mena)." and metoda_id=".intval($_POST['metoda']);
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");
     if (!$data = $res->fetchRow()){
           $this->vrat .= "<div class=\"errormsg\">Tato platební metoda není pro měnu zvoleného uživatele přístupná pro ".($_POST['typ'] == "vklad"?"vklad":"výběr")."</div><br />";$status = false;
     }else{

      $castka = round($_POST['castka'],4);
      $poplatky = $data['poplatky'];

      if($castka < $data['mini']){
               $this->vrat .= "<div class=\"errormsg\">Částka je nižší nežli povinná minimální (min. ".$data['mini']." ".$data['mena_text'].")</div><br />";$status = false;
      }
      else if($castka > $data['maxi']){
               $this->vrat .= "<div class=\"errormsg\">Částka je vyšší nežli povinná maximální (max. ".$data['maxi']." ".$data['mena_text'].")</div><br />";$status = false;
      }

     }

    }else $vyber_status = 1;
    ###

    if($_POST['typ'] == "vyber" && (!isset($vyber_status) || $vyber_status == 0)){

          $this->vrat .= "<div class=\"errormsg\">Tento uživatel není ověřen pro výběry a nemůže vybírat</div><br />";$status = false;

    }

    if($status){

      list($den,$mesic,$rok,$hod,$min,$sec) = split("[.: ]",$_POST['datum']);

      $this->dbGame->autocommit(false);

      if($_POST['typ'] == "vyber"  && $poplatky != 0 && $poplatby <= 100){
       $castka = ($_POST['castka'] * (1-($poplatky/100)));
      }else $castka = $_POST['castka'];

      $sql = "insert into balance(user_id,metoda_id,datum,castka,vklad,vyber,manual) values("
      	.$_POST['user'].","
      	.$_POST['metoda'].",'"
      	.$rok."-".$mesic."-".$den." ".$hod.":".$min.":".$sec."',"
      	.round($castka,4).","
      	.($_POST['typ'] == "vklad"?1:0).",".($_POST['typ'] == "vklad"?0:1).","
      	.It6_Session_Admin::getUserData('id')
      	.")";
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

      //FIXME pokud se nekdy pouzije tak predelat na web servisi transakce
      if(isset($_POST['pripsat']) && !in_array($_POST['user'],$GLOBALS['BOOK'])){

        if($_POST['typ'] == "vklad")
          $sql = "update uzivatel_im_data set zustatek=zustatek+".round(($_POST['castka'] * (1-($poplatky/100))),4)." where user_id=".$_POST['user'];
        else
          $sql = "update uzivatel_im_data set zustatek=zustatek-".round($_POST['castka'],4)." where user_id=".$_POST['user'];
        $res =& $this->dbGame->query($sql);
        if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

      }

      #Pripsani sazkoveho bonusu#
      $sql = "select user_id from bonus_bet where user_id=".$_POST['user'];
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

      if($res->numRows() == 0 && isset($mena) && $_POST['typ'] == "vklad"){

       $sql = "select min,bonus from bonus_bet_rules where min <= ".$castka." and mena_id=".$mena." order by min desc limit 1";
       $res =& $this->dbGame->query($sql);
       if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

       if ($data = $res->fetchRow()){

           $sql = "insert into bonus_bet (user_id,prvni_vklad_castka,bonus) values(".$_POST['user'].",".(8*$data['bonus']).",".$data['bonus'].")";
           $res =& $this->dbGame->query($sql);
           if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");

           $this->vrat .= "<div class=\"okmsg\">Byl to první vklad a uživateli se začal počítat sázkový bonus</div><br />";

       }else{

           $this->vrat .= "<div class=\"errormsg\">Byl to první vklad, ale nesplňoval podmínky sázkového bonusu uživatel už nemůže tento bonus získat</div><br />";
           $sql = "insert into bonus_bet (user_id,prvni_vklad_castka,bonus,pripsano) values(".$_POST['user'].",0,0,1)";
           $res =& $this->dbGame->query($sql);
           if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");

       }

      }

      #Pripsani herniho bonusu#
      $sql = "select user_id from bonus_wg where user_id=".$_POST['user'];
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

      if($res->numRows() == 0 && isset($mena) && $_POST['typ'] == "vklad"){

       $sql = "select min,bonus from bonus_wg_rules where min <= ".$castka." and mena_id=".$mena." order by min desc limit 1";
       $res =& $this->dbGame->query($sql);
       if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

       if ($data = $res->fetchRow()){

        $res = $this->selectKurz("where platny_od<=now() and platny_do>=now() and mena_id=".$mena);

        if ($data2 = $res->fetchRow()){

           $sql = "insert into bonus_wg (user_id,prvni_vklad_zetony,bonus) values(".$_POST['user'].",".round((($data['min']/$data2['kurz'])+$data['bonus']),2).",".$data['bonus'].")";
           $res =& $this->dbGame->query($sql);
           if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");

           $this->vrat .= "<div class=\"okmsg\">Byl to první vklad a uživateli se začal počítat herní bonus</div><br />";

        }else throw new ExHandler("Neni mozne pripsat bonus, nebyl nalezen kurz","admin_ex_page");

       }else{

           $this->vrat .= "<div class=\"errormsg\">Byl to první vklad, ale nesplňoval podmínky herního bonusu uživatel už nemůže tento bonus získat</div><br />";
           $sql = "insert into bonus_wg (user_id,prvni_vklad_zetony,bonus,pripsano) values(".$_POST['user'].",0,0,1)";
           $res =& $this->dbGame->query($sql);
           if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");

       }

      }

		$this->dbGame->commit();

		$this->vrat .= "<div class=\"okmsg\">Manuální příkaz byl proveden</div><br />";

		It6_Log::info(
			"Manual transaction (type: %type%, amount: %amount%, user: %user%, method: %method%).",
			It6_Log::TAG_DEPRECATED_OPERATION,
			array(
				'type' => $_POST['typ'] == "vklad"?"deposit":"withdraw",
				'amount' => round($_POST['castka'],4),
				'user' => $_POST['user'],
				'method' => $_POST['metoda']));

    }

  }

 /**
 * Manuální převod z a na účet
 * @return void
 */
  private function PlatebniOperace(){

   $mena_ob = new Mena();
   $res = $mena_ob->selectData();
   $mena = array();

   while($row =& $res->fetchRow()){

     $mena[$row['mena_id']] = $row['mena_text'];

   }

   $user = new UserKolekce();
   $res = $user->selectData();

   $user = "";

   while ($row =& $res->fetchRow()){

       $user .= "<option  value3=\"".$row['mena_id']."\" value2=\"".$mena[$row['mena_id']]."\" value=\"".$row['user_id']."\" >".Help::Html($row['nick'])."</option>";


   }

   $metoda = new PlatebniMetodyKolekce();
   $res = $metoda->selectData();
   $met = "";
   while($row =& $res->fetchRow()){

      $met .= "<option value=\"".$row['metoda_id']."\">".Help::Html($row['nazev'])."</option>";

   }

    $this->vrat .= '<form method="post" action="?section='.$this->section.'">
    <em>Pokud jsou u metody dane nejake poplatky tak
<br />
<strong>Při vkladu:</strong> <br />
Do uživatelovi balance je připsána částka včetně poplatků.<br />
Uživateli na účet  je připsána částka stržena o poplatky.
<br /><br />

<strong>Při výběru:</strong><br />
Do balance zapsána vybraná částka odečtená od poplatků <br />
Uživateli poslána stržená částka<br />
Z účtu uživatele stržena celá částka i s poplatky<br /><br />

<strong>Bonusy</strong><br />
I v manuálních platbách je normálně počítáno s bonusy (např: první vklad).<br /><br />

</em>
             <table style="border:1px solid #E3E3E3;font-size:0.9em;background:#708090;color:white">
             <tr><td class="textleft" style="background:#929EAD;" colspan="1">Uživatel</td></tr>';
   $this->vrat .= '<tr><td colspan="1"><select name="user" onchange="var x = this.options[this.selectedIndex].getAttribute(\'value2\');var y = this.options[this.selectedIndex].value3;ob=new getObj(\'menatext\');ob.obj.innerHTML=x;ob2=new getObj(\'mena\');ob2.obj.value=y;" size="10">';
   $this->vrat .= $user;
   $this->vrat .= '</select></td></tr>';
   $this->vrat .= "</table><br /><br />";

   $this->vrat .= '<table style="border:1px solid #E3E3E3;font-size:0.9em;background:#708090;color:white">
                    <tr><td class="textleft" style="background:#929EAD;" colspan="2">Příkaz</td></tr>';

   $this->vrat .= '<tr><td><strong>Typ platby:</strong> </td><td class="mandatory"><select name="typ"><option>-- Zvolte typ --</option><option value="vklad">Vklad</option><option value="vyber">Výběr</option></select></td></tr>';
   $this->vrat .= '<tr><td><strong>Platební metoda:</strong> </td><td class="mandatory"><select name="metoda"><option>--Zvolte platební metodu--</option>'.$met.'</select></td></tr>';
   $this->vrat .= '<tr><td><strong>Částka (max 4 des. místa)</strong> </td><td><input type="text" class="mandatory" name="castka"> <span id="menatext"></span><input type="hidden" name="mena" id="mena"  /></td></tr>';
   $this->vrat .= '<tr><td><strong>Datum (dd.mm.RRRR HH:mm:ss)</strong> </td><td><input type="text" class="mandatory" name="datum" id="datum" /><a href="javascript:window.open(\'calendar.php?cas=datum\',\'\',\'width=140,height=247\');void(0);"><img src="images/ico/calendar.gif" alt="Kalendář" class="img" /></a></td></tr>';
      $this->vrat .= '<tr><td><strong>Připsat/odepsat peníze na účet uživatele?</strong> </td><td><input type="checkbox" class="no" name="pripsat" id="pripsat" /></td></tr>';
   $this->vrat .= '<tr><td colspan="2"><input type="submit" name="send" onclick="if(!confirm(\'Opravdu chcete operaci provést? Příkaz bude normálně řazen do balance \n a pokud by nebyl skutečně proveden může dojít ke zkreslení údajů\'))return false;" value="Provést"></td></tr>';
   $this->vrat .= '</table><br /><br /><img src="https://'.ADMINHOST.'/_clip/mandatory.gif" class="img" /> <small>Takto označený údaj je povinný</small>';

   $this->vrat .= "</form>";

  }

/**
 * Vypis statistik zustatku uzivatelu - rozdeleny na uzavrene a otevrene ucty
 * @return void
 */
  private function ShowUserBalanceStat(){
    $this->vrat .= '<p><strong><font size="2">Celkové zůstatky na účtech k vybranému dni</font></strong><br />';
    $this->vrat .= '<form method="GET">';
    $this->vrat .= 'Vyberte den<br /><input type="text" id="datum" name="datum" value="'.(isset($_GET['datum'])?$_GET['datum']:'').'" /> <a href="javascript:window.open(\'calendar.php?cas=datum&hodiny=\',\'\',\'width=140,height=247\');void(0);"><img src="images/ico/calendar.gif" alt="Kalendář" class="img" /></a> ';
    $this->vrat .= '<input type="hidden" name="section" value="'.$_GET['section'].'" /><br />';
    $this->vrat .= '<input type="submit" value="Spočítat" /> ';
    $this->vrat .= '</form></p>';

    if(isset($_GET['datum'])){
      // nacteni prevodniho kurzu
      $sql = 'SELECT mena_id, kurz, max(timestamp) FROM mena_kurz GROUP BY mena_id';
      $res = $this->dbGame->query($sql);
      while($row =& $res->fetchRow()){
        $kurz[$row['mena_id']] = $row['kurz'];
      }
      //var_dump($kurz);

      // nacteni uzivatelu a rozdeleni na zakazane
      $sql = 'SELECT user_id, nick, mena_id, zakazany FROM uzivatel WHERE e_testovaci = \'ne\'';
      $res = $this->dbGame->query($sql);
      while($row = $res->fetchRow()){
        if($row['zakazany'] == 0) $users[$row['user_id']]['zakazany'] = 'povoleny';
        else $users[$row['user_id']]['zakazany'] = 'zakazany';
        $users[$row['user_id']]['mena'] = $row['mena_id'];
      }

      // vycteni dat z balance logu
      $pom = explode(" ",Date::format2ISO($_GET['datum']));
      $sql = 'SELECT user_id, castka, zetony, dluh, datum FROM balance_log WHERE (castka != 0 OR zetony != 0 OR dluh != 0) AND (datum LIKE \''.$pom[0].'%\')';
      $rows = Ntw_Db_Manager::getConnection('betwarehouse')->query($sql)->fetchAll();
      $ret = array();
      if(count($rows) > 0){
        foreach($rows as $row){
          // timto vyhazim testovaci uzivatele, ktere jsem nenacetl z uzivatelu, ale mam je v balance logu
          if(isset($users[$row['user_id']])){
            if(($users[$row['user_id']]['mena'] != 6) && ($users[$row['user_id']]['mena'] != 7)){
              $ret[$users[$row['user_id']]['zakazany']][$row['user_id']][$row['datum']]['zetony'] = $row['zetony'];
              $ret[$users[$row['user_id']]['zakazany']][$row['user_id']][$row['datum']]['castka'] = $row['castka']/$kurz[$users[$row['user_id']]['mena']];
              $ret[$users[$row['user_id']]['zakazany']][$row['user_id']][$row['datum']]['dluh'] = $row['dluh']/$kurz[$users[$row['user_id']]['mena']];
            }
          }
        }
      }
      Ntw_Db_Manager::disconnect('betwarehouse');

      // vypis
      foreach($ret as $typ => $rettype){
        foreach($rettype as $user => $rest){
          foreach($rest as $datum => $data){
            if(! isset($ret0[$datum])) $ret0[$datum] = 0;
            $ret0[$datum] += $data['zetony']+$data['castka']-$data['dluh'];
          }
        }
        $this->vrat .= "<p><strong>$typ</strong><br />";
        ksort($ret0);
        foreach($ret0 as $datum => $suma)
          $this->vrat .= "$datum - ".round($suma,2)." EUR<br />";
        $this->vrat .= '</p>';
      }
    }
  }


/**
 * Vypis uživatelů a jejich výběrů a vkladů a platebních metod jaké použili
 * @return void
 */
  private function ShowUser(){

   $vklad_celkem = $vyber_celkem = $vklad_celkem_metoda = $vyber_celkem_metoda = $zustatky = $mena = $u_array = $kurz = $met = array();

   $metoda = new PlatebniMetodyKolekce();
   $res = $metoda->selectData();

   while($row =& $res->fetchRow()){

      $met[$row['metoda_id']] = $row['nazev'];

   }

   $user = new UserKolekce();
   $res = $user->selectData();

   $user = "<option value=\"all\">--Všichni--</option>";
/*
   while ($row =& $res->fetchRow()){

       $user .= "<option value=\"".$row['user_id']."\" ".(isset($_POST['user']) && in_array($row['user_id'],$_POST['user'])?"selected=\"selected\"":"").">".Help::Html($row['nick'])."</option>";

       if(!isset($_POST['user']) || in_array("all", $_POST['user'])) $u_array[$row['user_id']] = array("mena"=>$row['mena_id'],"nick"=>$row['nick']);
       else if(isset($_POST['user']) && is_array($_POST['user']) && in_array($row['user_id'],$_POST['user'])) $u_array[$row['user_id']] = array("mena"=>$row['mena_id'],"nick"=>$row['nick']);

   }
*/
   $this->vrat .= '<form method="post" action="?section='.$this->section.'">
             <table style="border:1px solid #E3E3E3;font-size:0.9em;background:#708090;color:white">
             <tr><td class="textleft" style="background:#929EAD;" colspan="1">Vyhledávání</td></tr>';
   $this->vrat .= '<tr><td>Uživatel</td></tr>';
   $this->vrat .= '<tr><td colspan="1"><select name="user[]" size="10" multiple="multiple">';
   $this->vrat .= $user;
   $this->vrat .= '</select></td></tr>';
   $this->vrat .= '<tr><td colspan="1"><input type="submit" name="filtr" value="Vyhledat" /></td></tr>';
   $this->vrat .= "</form></table><br /><br />";

   if(!isset($_POST['filtr'])) return;

   $mena_ob = new Mena();
   $res = $mena_ob->selectData();

   while($row =& $res->fetchRow()){

     $mena[$row['mena_id']] = $row['mena_text'];

     $res2 =& $this->selectKurz("where mena_id=".$row['mena_id']." and platny_od<='".date("Y")."-".date("m")."-".date("d")."' and platny_do>='".date("Y")."-".date("m")."-".date("d")."'");
     if ($row2 =& $res2->fetchRow()) $kurz[$row['mena_id']] = $row2['kurz'];
     else{
       $this->vrat .= "<div class=\"errormsg\">Pro měnu ".Help::Html($row['mena_text'])." nebyl vypsán kurz pro dnešní datum a výpočty nemohou být provedeny</div>\n";
     }
   }

  foreach($u_array as $k=>$h){

     $vklad_celkem[$k][$h['mena']] = $this->SumVklad($h['mena'],$k);
     $vyber_celkem[$k][$h['mena']] = $this->SumVyber($h['mena'],$k);
     $vklad_celkem_metoda[$k][$h['mena']] = $this->SumVklad($h['mena'],$k,null,null,1);
     $vyber_celkem_metoda[$k][$h['mena']] = $this->SumVyber($h['mena'],$k,null,null,1);
     $zustatky[$k][$h['mena']] = $this->Zustatek($h['mena'],$k);

     if(isset($kurz[$h['mena']])){
       $zustatky[$k][$h['mena']]['sumazustatek'] = round(($zustatky[$k][$h['mena']]['sumazustatek']+($zustatky[$k][$h['mena']]['sumazetony']*$kurz[$h['mena']])),4);
     }
     else{
       $zustatky[$k][$h['mena']]['sumazustatek'] = "NaN";
       $vklad_celkem[$k][$h['mena']] = "NaN";
       $vyber_celkem[$k][$h['mena']] = "NaN";
     }

   }

   $this->vrat .= '<div style="width:760px;"><div class="hra_top">Finance - Uživatelé</div>';
   $this->vrat .= '<table class="hra_hraci" style="font-size:0.9em;"><col style="background:\'#909090\'">';
   $this->vrat .= '<head><tr><th>Uživatel</th><th>Měna</th><th>Vklad</th><th>Výběry</th><th>Zůstatek na účtech</th><th>Žetony</th><th>Zisk/Ztráta</th></tr></head>';

   foreach($u_array as $k=>$h){

     $zisk = ($vklad_celkem[$k][$h['mena']]-$vyber_celkem[$k][$h['mena']]-$zustatky[$k][$h['mena']]['sumazustatek']);
     $this->vrat .= '<tr onmouseover="this.style.backgroundColor=\'#909090\'" onmouseout="this.style.backgroundColor=\'\'">
                   <td class="textcenter"><a href="javascript:ob =  new getObj(\'metod_'.$k.'\');displayObj(ob);void(0);">'.Help::Html($h['nick']).'</a></td>
                   <td class="textcenter">'.Help::Html($mena[$h['mena']]).'</td>
                   <td class="textcenter">'.$vklad_celkem[$k][$h['mena']].'</td>
                   <td class="textcenter">'.$vyber_celkem[$k][$h['mena']].'</td>
                   <td class="textcenter">'.$zustatky[$k][$h['mena']]['sumazustatek'].'</td>
                   <td class="textcenter">'.$zustatky[$k][$h['mena']]['sumazetony'].'</td>
                   <td class="textcenter">'.$zisk.'</td></tr>';
     $this->vrat .= '<tr><td colspan="7"><table class="methods" id="metod_'.$k.'" style="display:none"><col style="width:30%;" >
                      <col style="width:35%;"><col style="width:35%;">';
     $this->vrat .= '<tr><th>&nbsp;</th><th>Vklad</th><th>Výběr</th></tr>';

     reset($met);

     foreach($met as $k2=>$h2){

      $this->vrat .= '<tr><td class="textcenter">'.Help::Html($h2).'</td><td class="textcenter">'.$vklad_celkem_metoda[$k][$h['mena']][$k2].'</td><td class="textcenter">'.$vyber_celkem_metoda[$k][$h['mena']][$k2].'</td></tr>';

     }

     $this->vrat .= '</table></td></tr>';

   }

   $this->vrat .= '</table></div>';

   $this->vrat .= '<br /><br />';

  }

 /**
 * Vypis informaci o financich
 * @return void
 */
  private function ShowBalance(){

    $mena_ob = new Mena();
    $res = $mena_ob->selectData();

    $vklad_celkem = $vyber_celkem = $vklad_celkem_metoda = $vyber_celkem_metoda = $zustatky = $book = $mena = array();

    while($row =& $res->fetchRow()){

      $mena[$row['mena_id']] = $row['mena_text'];
      $book[$row['mena_id']] = $this->bookSum($row['mena_id']);
      $book_metoda[$row['mena_id']] = $this->bookSum($row['mena_id'],null,null,1);
      $vklad_celkem[$row['mena_id']] = $this->SumVklad($row['mena_id']);
      $vyber_celkem[$row['mena_id']] = $this->SumVyber($row['mena_id']);
      $vklad_celkem_metoda[$row['mena_id']] = $this->SumVklad($row['mena_id'],null,null,null,1);
      $vyber_celkem_metoda[$row['mena_id']] = $this->SumVyber($row['mena_id'],null,null,null,1);
      $zustatky[$row['mena_id']] = $this->Zustatek($row['mena_id']);

      $res2 =& $this->selectKurz("where mena_id=".$row['mena_id']." and platny_od<='".date("Y")."-".date("m")."-".date("d")."' and platny_do>='".date("Y")."-".date("m")."-".date("d")."'");
      if ($row2 =& $res2->fetchRow()) $zustatky[$row['mena_id']]['sumazustatek'] = round(($zustatky[$row['mena_id']]['sumazustatek']+($zustatky[$row['mena_id']]['sumazetony']*$row2['kurz'])),4);
      else{
       $zustatky[$row['mena_id']]['sumazustatek'] = "NaN";
       $vklad_celkem[$row['mena_id']] = "NaN";
       $vyber_celkem[$row['mena_id']] = "NaN";
       $this->vrat .= "<div class=\"errormsg\">Pro měnu ".Help::Html($row['mena_text'])." nebyl vypsán kurz pro dnešní datum a výpočty nemohou být provedeny</div>\n";
      }

    }

    $this->vrat .= '<div style="width:760px;"><div class="hra_top">Finance</div>';
    $this->vrat .= '<div class="hra_bottom"><em>
                    <strong>Vklady</strong> jsou veškeré vklady uživatelů nepatří sem  uživatelé<br />
                    <strong>Výběry</strong> jsou veškeré výběry uživatelů nepatří sem  uživatelé<br />
                    <strong>Zůstatek na účtech</strong> jsou zůstatky na účtě včetně žetonů převedených podle aktuálního kurzu<br />
                    <strong>Zisk/Ztráta</strong> Vklady - Výběry - Zůstatky na účtech <br />
                    <strong> vklad/výběr</strong> vklad resp. výběr uživatelem  <br />
                    <strong>Disponibiln zůstatek</strong> Vklady - Výběry + ( vklad -  výběr) <br />
                   </em></div>';
    $this->vrat .= '<table class="hra_hraci" style="font-size:0.9em;"><col style="background:\'#909090\'">';
    $this->vrat .= '<head><tr><th>Měna</th><th>Vklad</th><th>Výběry</th><th>Zůstatek na účtech</th><th>Zisk/Ztráta</th><th> vklad</th><th> výběr</th><th>Disponibilní zůstatek</th></tr></head>';

    foreach($mena as $k=>$h){

      $disp = ($vklad_celkem[$k]-$vyber_celkem[$k]+($book[$k]['vklad']-$book[$k]['vyber']));
      $zisk = ($vklad_celkem[$k]-$vyber_celkem[$k]-$zustatky[$k]['sumazustatek']);

      $this->vrat .= '<tr onmouseover="this.style.backgroundColor=\'#909090\'" onmouseout="this.style.backgroundColor=\'\'"><td class="textcenter">'.Help::Html($h).'</td>
      <td class="textcenter">'.$vklad_celkem[$k].'</td><td class="textcenter">'.$vyber_celkem[$k].'</td><td class="textcenter">'.$zustatky[$k]['sumazustatek'].'</td><td class="textcenter">'.$zisk.'</td><td class="textcenter">'.$book[$k]['vklad'].'</td><td class="textcenter">'.$book[$k]['vyber'].'</td><td class="textcenter">'.$disp.'</td></tr>';
      if($disp < $zustatky[$k]['sumazustatek']) $this->vrat .= '<tr style="background:#929EAD;"><td colspan="8" class="red">Pozor na účtě ('.Help::Html($h).') není dostatek hotovosti na krytí chybí: '.($zustatky[$k]['sumazustatek']-$disp).'</td></tr>';

    }

    $this->vrat .= '</table></div>';

    $this->vrat .= '<br /><br />';

    $metoda = new PlatebniMetodyKolekce();
    $res = $metoda->selectData();

    $met = array();

    while($row =& $res->fetchRow()){

      $met[$row['metoda_id']] = $row['nazev'];

    }


    reset($met);
    foreach($met as $k=>$h){

      $this->vrat .= '<div><div class="hra_top">'.Help::Html($h).'</div>';
      $this->vrat .= '<table class="hra_hraci" style="font-size:0.9em;">
                      <col style="background:\'#909090\';width:100px;" >
                      ';
      $this->vrat .= '<tr><th>&nbsp;</th><th>Vklad</th><th>Výběr</th><th>book vklad</th><th>book výběr</th><th>Disponibilní zůstatek</th></tr>';

      foreach($mena as $k2=>$h2){

        if(!isset($book_metoda[$k2][$k]['vklad'])) $book_metoda[$k2][$k]['vklad'] = 0;
        if(!isset($book_metoda[$k2][$k]['vyber'])) $book_metoda[$k2][$k]['vyber'] = 0;
        if(!isset($vklad_celkem_metoda[$k2][$k])) $vklad_celkem_metoda[$k2][$k] = 0;
        if(!isset($vyber_celkem_metoda[$k2][$k])) $vyber_celkem_metoda[$k2][$k] = 0;
        $disp = ($vklad_celkem_metoda[$k2][$k]-$vyber_celkem_metoda[$k2][$k] + ($book_metoda[$k2][$k]['vklad']-$book_metoda[$k2][$k]['vyber']));
        $this->vrat .= '<tr><td class="textcenter">'.Help::Html($h2).'</td><td class="textcenter">'.$vklad_celkem_metoda[$k2][$k].'</td><td class="textcenter">'.$vyber_celkem_metoda[$k2][$k].'</td>
                        <td class="textcenter">'.$book_metoda[$k2][$k]['vklad'].'</td><td class="textcenter">'.$book_metoda[$k2][$k]['vyber'].'</td><td class="textcenter '.($disp<0?"warn":"").'">'.$disp.'</td></tr>';

      }

      $this->vrat .= '</table></div>';
      $this->vrat .= '<br />';

    }



    $this->vrat .= '<br /><br />';

  }

 /**
 * Vklady a vybery pro uzivatele book
 * @param int $mena_id id meny
 * @param string $od datum od
 * @param string $do datum do
 * @param int $met sestrideni podle platebni metody
 * @return void
 */
  public function bookSum($mena_id = null,$od=null,$do=null,$met=0){

   if($mena_id != null && is_numeric($mena_id)){

     $pole = array();

     if($met == 0){

       #vklad#
       $sql = "select SUM(castka) AS suma from user_balance where mena_id=".$mena_id." and vklad=1  and user_id=".$GLOBALS['bookUSER'][$mena_id]." ".($od != null && It6_Date::checkFormat($od)?"and datum>'".$od."'":"").($do != null && It6_Date::checkFormat($do)?"and datum>'".$do."'":"")." group by mena_id";
       $res =& $this->dbGame->query($sql);
       if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

       if($row =& $res->fetchRow()) $pole['vklad'] = $row['suma'];
       else $pole['vklad'] = 0;

       #vyber#
       $sql = "select SUM(castka) AS suma from user_balance where mena_id=".$mena_id." and vyber=1 and user_id=".$GLOBALS['bookUSER'][$mena_id]." ".($od != null && It6_Date::checkFormat($od)?"and datum>'".$od."'":"").($do != null && It6_Date::checkFormat($do)?"and datum>'".$do."'":"")." group by mena_id";
       $res =& $this->dbGame->query($sql);
       if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

       if($row =& $res->fetchRow()) $pole['vyber'] = $row['suma'];
       else $pole['vyber'] = 0;

     }

     else if($met == 1){

       #vklad#
       $sql = "select SUM(castka) AS suma,metoda_id from user_balance where mena_id=".$mena_id." and vklad=1 and vyber=0 and user_id=".$GLOBALS['bookUSER'][$mena_id]." ".($od != null && It6_Date::checkFormat($od)?"and datum>'".$od."'":"").($do != null && It6_Date::checkFormat($do)?"and datum>'".$do."'":"")." group by metoda_id";
       $res =& $this->dbGame->query($sql);
       if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

       while($row =& $res->fetchRow()) $pole[$row['metoda_id']]['vklad'] = $row['suma'];

       #vyber#
       $sql = "select SUM(castka) AS suma,metoda_id from user_balance where mena_id=".$mena_id." and vyber=1 and vklad=0 and user_id=".$GLOBALS['bookUSER'][$mena_id]." ".($od != null && It6_Date::checkFormat($od)?"and datum>'".$od."'":"").($do != null && It6_Date::checkFormat($do)?"and datum>'".$do."'":"")." group by metoda_id";
       $res =& $this->dbGame->query($sql);
       if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

       while($row =& $res->fetchRow()) $pole[$row['metoda_id']]['vyber'] = $row['suma'];

     }

     return $pole;
   }

    return false;

  }

  /**
 * Vyber zustatek na ucte pro danou menu
 * @param int $mena_id id meny
 * @param int $user_id id uzivatele
 * @return void
 */
  public function Zustatek($mena_id = null,$user_id=null){

    if($mena_id != null && is_numeric($mena_id)){

     $sql = "select SUM(zustatek) AS sumazustatek, SUM(zetony) AS sumazetony from uzivatel_data where mena_id=".$mena_id." ".($user_id != null && is_numeric($user_id)?" and user_id=".$user_id:" and user_id not in(".implode(",",$GLOBALS['bookUSER']).")")." group by mena_id";
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

     if($row =& $res->fetchRow()) return $row;
     else return array('sumazustatek' => 0,'sumazetony' => 0);

   }

  }

 /**
 * Vyber pro danou menu
 * @param int $mena_id id meny
 * @param int $user_id id uzivatele
 * @param string $od datum od
 * @param string $do datum do
 * @param int $metoda seskupit podle metody
 * @return void
 */
  public function SumVyber($mena_id = null,$user_id=null,$od=null,$do=null,$metoda=null){

   if($mena_id != null && is_numeric($mena_id)){

     $sql = "select SUM(castka) AS suma,metoda_id from user_balance where mena_id=".$mena_id." and vyber=1 ".($user_id != null && is_numeric($user_id)?" and user_id=".$user_id:" and user_id not in(".implode(",",$GLOBALS['bookUSER']).")").($od != null && It6_Date::checkFormat($od)?"and datum>'".$od."'":"").($do != null && It6_Date::checkFormat($do)?"and datum>'".$do."'":"")." group by ".($metoda == null?"mena_id":"metoda_id");
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

     if($metoda == null){

      if($row =& $res->fetchRow()) return $row['suma'];
      else return 0;

     }else{

      $pole = array();

      while($row =& $res->fetchRow())  $pole[$row['metoda_id']] = $row['suma'];

      return $pole;

     }


   }

  }

   /**
 * Vklad pro danou menu
 * @param int $mena_id id meny
 * @param int $user_id id uzivatele
 * @param string $od datum od
 * @param string $do datum do
 * @param int $metoda seskupit podle metody
 * @return void
 */
  public function SumVklad($mena_id = null,$user_id=null,$od=null,$do=null,$metoda=null){

   if($mena_id != null && is_numeric($mena_id)){

     $sql = "select SUM(castka) AS suma,metoda_id from user_balance where mena_id=".$mena_id." and vklad=1 ".($user_id != null && is_numeric($user_id)?" and user_id=".$user_id:" and user_id not in(".implode(",",$GLOBALS['bookUSER']).")").($od != null && It6_Date::checkFormat($od)?"and datum>'".$od."'":"").($do != null && It6_Date::checkFormat($do)?"and datum>'".$do."'":"")." group by ".($metoda == null?"mena_id":"metoda_id");
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

     if($metoda == null){

      if($row =& $res->fetchRow()) return $row['suma'];
      else return 0;

     }else{

      $pole = array();

      while($row =& $res->fetchRow())  $pole[$row['metoda_id']] = $row['suma'];

      return $pole;

     }

   }

  }


  /**
 * Zmena data pro kurz
 * @return void
 */
  private function EditKurz(){

    $status = true;

    if(!Help::CheckDatum($_POST['od'])) {$this->vrat .= "<div class=\"errormsg\"> <strong>Platný od</strong> musí mít správný formát</div><br />";$status = false;}
    if(!Help::CheckDatum($_POST['do'])) {$this->vrat .= "<div class=\"errormsg\"> <strong>Platný do</strong> musí mít správný formát</div><br />";$status = false;}
    if(Help::CheckDatum($_POST['od']) && Help::CheckDatum($_POST['do'])){

    list($den1,$mesic1,$rok1) = explode(".",$_POST['od']);
    list($den2,$mesic2,$rok2) = explode(".",$_POST['do']);

    if(mktime (0,0,0,$mesic1,$den1,$rok1) > mktime (0,0,0,$mesic2,$den2,$rok2)){$this->vrat .= "<div class=\"errormsg\"> Platný od nemůže být větší nežli platný do</div><br />";$status = false;}

    #1 prekrizeni#
    $sql = "select id_kurz,platny_od,platny_do AS max from kurzmena where id_kurz<>".$_POST['id_kurz']." and platny_od<='".$rok1."-".$mesic1."-".$den1."' and platny_do>='".$rok1."-".$mesic1."-".$den1."' and platny_do<'".$rok2."-".$mesic2."-".$den2."' group by id_kurz";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

    if($row =& $res->fetchRow()){
      $this->vrat .= "<div class=\"errormsg\"> Došlo k 1. překřížení s existujícím kurzem (".Help::Html($row['id_kurz']).") <img src=\"_clip/kriz1.gif\" class=\"img\" \></div><br />";
      $status = false;
    }
    #Konec 1 prekrizeni#

    #2 prekrizeni#
    $sql = "select id_kurz,platny_od,platny_do AS max from kurzmena where id_kurz<>".$_POST['id_kurz']." and platny_od>'".$rok1."-".$mesic1."-".$den1."' and platny_od<='".$rok2."-".$mesic2."-".$den2."' and platny_do>='".$rok2."-".$mesic2."-".$den2."' group by id_kurz";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

    if($row =& $res->fetchRow()){
      $this->vrat .= "<div class=\"errormsg\"> Došlo k 2. překřížení s existujícím kurzem (".Help::Html($row['id_kurz']).") <img src=\"_clip/kriz2.gif\" class=\"img\" \></div><br />";
      $status = false;
    }
    #Konec 2 prekrizeni#

    #3 prekrizeni#
    $sql = "select id_kurz,platny_od,platny_do AS max from kurzmena where id_kurz<>".$_POST['id_kurz']." and ((platny_od<'".$rok1."-".$mesic1."-".$den1."' and platny_do>'".$rok2."-".$mesic2."-".$den2."') or (platny_od<='".$rok1."-".$mesic1."-".$den1."' and platny_do>'".$rok2."-".$mesic2."-".$den2."') or (platny_od<'".$rok1."-".$mesic1."-".$den1."' and platny_do>='".$rok2."-".$mesic2."-".$den2."')) group by id_kurz";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

    if($row =& $res->fetchRow()){
      $this->vrat .= "<div class=\"errormsg\"> Došlo k 3. překřížení s existujícím kurzem (".Help::Html($row['id_kurz']).") <img src=\"_clip/kriz3.gif\" class=\"img\" \></div><br />";
      $status = false;
    }
    #Konec 3 prekrizeni#

    #4 prekrizeni#
    $sql = "select id_kurz,platny_od,platny_do AS max from kurzmena where id_kurz<>".$_POST['id_kurz']." and platny_od>'".$rok1."-".$mesic1."-".$den1."' and platny_do<'".$rok2."-".$mesic2."-".$den2."' group by id_kurz";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

    if($row =& $res->fetchRow()){
      $this->vrat .= "<div class=\"errormsg\"> Došlo k 4. překřížení s existujícím kurzem (".Help::Html($row['id_kurz']).") <img src=\"_clip/kriz4.gif\" class=\"img\" \></div><br />";
      $status = false;
    }
    #Konec 4 prekrizeni#

    #5 prekrizeni#
    $sql = "select id_kurz,platny_od,platny_do AS max from kurzmena where id_kurz<>".$_POST['id_kurz']." and platny_od='".$rok1."-".$mesic1."-".$den1."' and platny_do='".$rok2."-".$mesic2."-".$den2."' group by id_kurz";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

    if($row =& $res->fetchRow()){
      $this->vrat .= "<div class=\"errormsg\"> Došlo k 5. překřížení s existujícím kurzem (".Help::Html($row['id_kurz']).") <img src=\"_clip/kriz5.gif\" class=\"img\" \></div><br />";
      $status = false;
    }
    #Konec 5 prekrizeni#

     #6 prekrizeni#
    $sql = "select id_kurz,platny_od,platny_do AS max from kurzmena where id_kurz=".Help::Html($_POST['id_kurz'])." and platny_od='".$rok1."-".$mesic1."-".$den1."' and platny_do='".$rok2."-".$mesic2."-".$den2."' group by id_kurz";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

    if($row =& $res->fetchRow()){
      $status = false;
    }
    #Konec 6 prekrizeni#

   }


   if($status){

    list($den1,$mesic1,$rok1) = explode(".",$_POST['od']);
    list($den2,$mesic2,$rok2) = explode(".",$_POST['do']);

    $sql = "update kurz set platny_od='".$rok1."-".$mesic1."-".$den1."',platny_do='".$rok2."-".$mesic2."-".$den2."' where id_kurz=".$_POST['id_kurz'];
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

    $this->vrat .= "<div class=\"okmsg\">Datum bylo změněno</div><br />";

	It6_Log::info(
		"Currency rate '%id%' date range changed to (%from% - %to%).",
		It6_Log::TAG_ADMIN_OPERATION,
		array(
			'id' => $_POST['id_kurz'],
			'from' => $rok1 . "-" . $mesic1 . "-" . $den1,
			'to' => $rok2 . "-" . $mesic2 . "-" . $den2));

   }

  }

   /**
 * Pridani noveho kurzu
 * @return void
 */
  private function NovyKurz(){

   $status = true;

    if(!isset($_POST['platny_od']) || !Help::CheckDatum($_POST['platny_od'])){
      $this->vrat .= "
        <div class=\"errormsg\">
          <strong>Platný od</strong> musí být uveden a mít správný formát
        </div><br />
      ";
      $status = false;
    }
    if(!isset($_POST['platny_do']) || !Help::CheckDatum($_POST['platny_do'])){
      $this->vrat .= "
        <div class=\"errormsg\">
          <strong>Platný do</strong>
          musí být uveden a mít správný formát
        </div>
        <br />
      ";
      $status = false;
    }


    if(Help::CheckDatum($_POST['platny_od']) &&  Help::CheckDatum($_POST['platny_do'])){


      $date_from  = It6_Date::toDbAsDate($_POST['platny_od']);
      $date_to    = It6_Date::toDbAsDate($_POST['platny_do']);

      if($date_from > $date_to){
        $this->vrat .= "<div class=\"errormsg\"> Platný od nemůže být větší nežli platný do</div><br />";
        $status = false;
      }

      #1 prekrizeni#
      $sql = "
        SELECT id_kurz,platny_od,platny_do AS max
        FROM kurzmena
        WHERE platny_od<='".$rok1."-".$mesic1."-".$den1."'
          AND platny_do>='".$rok1."-".$mesic1."-".$den1."'
          AND platny_do<'".$rok2."-".$mesic2."-".$den2."'
        GROUP BY id_kurz
      ";
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

      if($row =& $res->fetchRow()){
        $this->vrat .= "
          <div class=\"errormsg\">
            Došlo k 1. překřížení s existujícím kurzem (".$row['id_kurz'].")
            <img src=\"_clip/kriz1.gif\" class=\"img\" \>
          </div>
          <br />
        ";
        $status = false;
      }


      #2 prekrizeni#
      $sql = "
        SELECT id_kurz,platny_od,platny_do AS max
        FROM kurzmena
        WHERE platny_od>'".$date_from."'
          AND platny_od<='".$date_to."'
          AND platny_do>='".$date_to."'
        GROUP BY id_kurz
      ";
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

      if($row =& $res->fetchRow()){
        $this->vrat .= "
          <div class=\"errormsg\">
            Došlo k 2. překřížení s existujícím kurzem (".$row['id_kurz'].")
            <img src=\"_clip/kriz2.gif\" class=\"img\" \>
          </div>
          <br />
        ";
        $status = false;
      }


      #3 prekrizeni#
      $sql = "
        SELECT id_kurz,platny_od,platny_do AS max
        FROM kurzmena
        WHERE (platny_od<'".$date_from."'
          AND platny_do>'".$date_to."')
        OR (platny_od<='".$date_from."'
          AND platny_do>'".$date_to."')
        OR (platny_od<'".$date_from."'
          AND platny_do>='".$date_to."')
        GROUP BY id_kurz
      ";
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

      if($row =& $res->fetchRow()){
        $this->vrat .= "
          <div class=\"errormsg\">
            Došlo k 3. překřížení s existujícím kurzem (".$row['id_kurz'].")
            <img src=\"_clip/kriz3.gif\" class=\"img\" \>
          </div>
          <br />
        ";
        $status = false;
      }


      #4 prekrizeni#
      $sql = "
        SELECT id_kurz,platny_od,platny_do AS max
        FROM kurzmena
        WHERE platny_od>'".$date_from."'
          AND platny_do<'".$date_to."'
        GROUP BY id_kurz
      ";
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

      if($row =& $res->fetchRow()){
        $this->vrat .= "
          <div class=\"errormsg\">
            Došlo k 4. překřížení s existujícím kurzem (".$row['id_kurz'].")
            img src=\"_clip/kriz4.gif\" class=\"img\" \>
          </div>
          <br />
        ";
        $status = false;
      }


      #5 prekrizeni#
      $sql = "
        SELECT id_kurz,platny_od,platny_do AS max
        FROM kurzmena
        WHERE platny_od='".$date_from."'
          AND platny_do='".$date_to."'
        GROUP BY id_kurz
      ";
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

      if($row =& $res->fetchRow()){
        $this->vrat .= "
          <div class=\"errormsg\">
            Došlo k 5. překřížení s existujícím kurzem (".$row['id_kurz'].")
            <img src=\"_clip/kriz5.gif\" class=\"img\" \>
          </div>
          <br />
        ";
        $status = false;
      }
    }

    if($status){
      $this->dbGame->autocommit(false);

      $sql = "
        INSERT into kurz (platny_od,platny_do)
        VALUES('".$date_from."','".$date_to."')
      ";
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res))
        throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

      $sql = "SELECT max(id_kurz) AS max FROM kurz";
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res))
        throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

      if($row =& $res->fetchRow())
        $id = $row['max'];
      else throw
        new ExHandler($sql.$res->getMessage(),"admin_ex_data");

      foreach($_POST['newkurz'] as $k=>$h){
        if(is_numeric($h)){
          $sql = "
            INSERT into kurz_mena (id_kurz,id_mena,kurz)
            VALUES(".$id.",".intval($k).",".floatval(number_format($h, 4, '.', '')).")
          ";
          $res =& $this->dbGame->query($sql);
          if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");
        }
        else{
          $this->dbGame->rollback();
          $this->vrat .= "
            <div class=\"errormsg\">
              <strong>".(mb_strlen($h)>0?"\"".Help::Html($h)."\"":"Prázdný řetězec")."</strong>
              není povolená hodnota. Vložení se nezdařilo
            </div>
            <br />
          ";
          $status = false;
        }
      }

      if($status){
        $this->vrat .= "
          <div class=\"okmsg\">Nový kurz byl úspěšně vložen</div>
          <br />
        ";

		It6_Log::info(
			"New rate (#%id%) '%value%' of currency #%currencyId% inserted.",
			It6_Log::TAG_ADMIN_OPERATION,
			array(
				'id' => $id,
				'value' => floatval(number_format($h, 4, '.', '')),
				'currencyId' => intval($k)));
	}

      $this->dbGame->commit();
      $this->dbGame->autocommit(true);
    }
  }



 /**
 * Vypise stranku
 * @return void
 */
  private function ShowKurz(){

    $this->vrat .= '
      <table class=\"unitable\">
        <tr>
          <th>Kurz</th>
          <th style="background:;color:white">Nový kurz</th>
        </tr>

        <tr>
          <td>
            <em>Můžete změnit datum platnosti kurzu<br /> Kurz se počítá 1  (EUR)/x měna</em>
          </td>
          <td><em></em>
          </td>
        </tr>

        <tr>
          <td valign="top">

            <table>
              <form method="post" action"?section='.$this->section.'">
                <tr>
                  <td colspan="2">Vyberte den</td>
                </tr>
                <tr>
                  <td>
                    <input
                      type="text"
                      name="den"
                      id="den"
                      class="dateOnly"
                      value="'.(isset($_POST['den'])?$_POST['den']:"").'"
                    />
                    <img src="images/ico/calendar.gif" class="calendar-icon">
                  </td>
                  <td>
                  <input
                    type="submit"
                    style="width:40px"
                    name="vyber"
                    value="ok"
                  />
                </td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <th>Platný od</th>
                <th>Platný do</th>
              </tr>
    ';

    if(!isset($_POST['den']) || !Help::CheckDatum($_POST['den']))
      $_POST['den'] = date("d.m.Y");

    $day = It6_Date::toDbAsDate($_POST['den']);

    $res =& $this->selectKurz("WHERE platny_od<='".$day."' AND platny_do>='".$day."'");

    if($res->numRows() < 1){
      $this->vrat .= '
        <tr>
          <td colspan="2">Kurzy na toto datum nebyly vypsány</td>
        </tr>
      ';
    }
    else{
      $x = 1;
      while($row =& $res->fetchRow()){
        if($x==1){
          $date_from = It6_Date::fromDbAsDate($row['platny_od']);
          $date_to = It6_Date::fromDbAsDate($row['platny_do']);

          $this->vrat .= '
            <tr>
              <td>
                <input
                  type="hidden"
                  name="id_kurz"
                  value="'.$row['id_kurz'].'"
                />
                <input
                  type="text"
                  name="od"
                  id="od"
                  class="dateOnly"
                  value="'.Help::Html($date_from).'"
                />
                <img src="images/ico/calendar.gif" class="calendar-icon">
              </td>
              <td>
                <input
                  type="text"
                  name="do"
                  id="do"
                  class="dateOnly"
                  value="'.Help::Html($date_to).'"
                />
                <img src="images/ico/calendar.gif" class="calendar-icon">
              </td>
            </tr>
          ';
        }

        $this->vrat .= '
          <tr>
            <td>'.Help::Html($row['mena_text']).'</td>
            <td>'.Help::Html($row['kurz']).'</td>
          </tr>
        ';

        $x++;
      }
    }

    $this->vrat .= '</table></td>';

    $mena = new Mena();
    $res = $mena->selectData();

    $this->vrat .= '
      <td style="background:#BFBD93;color:white">
        <table>
          <tr>
            <td>Platný od</td>
            <td>Platný do</td>
          </tr>

          <tr>
            <td>
              <input
                type="text"
                name="platny_od"
                class="dateOnly"
                value="'.(isset($_POST['platny_od'])?Help::Html($_POST['platny_od']):'').'"
              />
              <img src="images/ico/calendar.gif" class="calendar-icon">
            </td>
            <td>
              <input
                type="text"
                name="platny_do"
                class="dateOnly"
                value="'.(isset($_POST['platny_do'])?Help::Html($_POST['platny_do']):'').'"
              />
              <img src="images/ico/calendar.gif" class="calendar-icon">
            </td>
          </tr>

          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
    ';

    while ($row =& $res->fetchRow()){

     $this->vrat .= '<tr><td>'.$row['mena_text'].'</td><td><input type="text" name="newkurz['.$row['mena_id'].']" value="'.(isset($_POST['newkurz'][$row['mena_id']])?$_POST['newkurz'][$row['mena_id']]:"").'" /></td></tr>';

   }

   $this->vrat .= '<tr><td>&nbsp;</td><td><input type="submit" name="new" value="Uložit"></td></tr>';
   $this->vrat .= '</form></table></td>';

   $this->vrat .= '</tr>';

   $this->vrat .= "</table>";

  }

  /**
 * vyber dat z databaze
 * @return object
 */
  public function selectKurz($where=""){

     $sql = "select * from kurzmena ".$where." order by mena_text";
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

     return $res;

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
