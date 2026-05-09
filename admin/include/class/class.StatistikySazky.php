<?php
/**
 * @package    statistics
 */


class StatistikySazky{

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
 *ma se ukazat filtr formular
 * @access private
 * @var bool
 */
private  $nomenu=false;


/**
 * cas mezi zacatkem a konce scriptu
 * @access private
 * @var int
 */
private  $time;

/**
 * pole top vyhernich tiketu
 * @access private
 * @var array
 */
private  $vyhra_ar;

/**
 * pole top prohernich tiketu
 * @access private
 * @var array
 */
private  $prohra_ar;

/**
 * pole s informacemi
 * @access private
 * @var array
 */
public  $infoGlobal;

/**
 * identifikator zda vyheldavame podle parametru
 * @access private
 * @var bool
 */
private $dateBool = false;

/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param int $section id aktualni sekce
* @param PEAR::DB $dbGame objekt spojeni s databazi
* @param PEAR::DB $db objekt spojeni s databazi
*/
  public function __construct($section){

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
 * Metoda spousti jednitlove metody podle stavu

 * @return void
 */
  public function runAction(){

    $this->time = time();

    $this->vrat .= '
      <form method="get">
        <input type="hidden" name="section" value="'.$this->section.'">
      </form>
    ';

   if(!isset($_GET['graph']) || $_GET['graph'] == 0){
     $this->TicketInfo();
   }
   else if(isset($_GET['graph']) && $_GET['graph'] == 1){
     $this->BetInfo();
   }
   else if(isset($_GET['graph']) && $_GET['graph'] == 3){
     $this->BookmakerInfo();
   }

    $this->dbGame->disconnect();
  }

 /**
 * Bookmakeri info
 *
 *
 *
 *
 * @return void
 */
  public function BookmakerInfo(){

    $sport_ar       = array();
    $udalost_ar     = array();
    $udalost_sport  = array();
    $sazky          = array();
    $user_ar        = array();
    $sazky_pole     = array();
    $ticket         = array();
    $bookmaker_ar   = array();
    $sport          = "";
    $udalost        = "";
    $book           = "";

    $preklad = new Preklady();

    #Vyber bookmakeru#
    $sql = "select jmeno,prijmeni,nick,bookmaker_id from bookmaker";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res))
      throw new ExHandler('Nepodarilo se provest dotaz: vyber bookmakera',"admin_ex_db");

    while ($row =& $res->fetchRow()){
      $book .= "<option  value=\"".$row['bookmaker_id']."\" ".(isset($_POST['book']) && $_POST['book']==$row['bookmaker_id']?"selected=\"selected\"":"").">".Help::Html($row['jmeno'].' '.$row['prijmeni'].' ('.$row['nick'].')')."</option>";
      if(isset($_POST['book']) && $_POST['book'] != 0 && $row['bookmaker_id'] == $_POST['book']) $bookmaker_ar[$row['bookmaker_id']]['name'] = $row['jmeno'].' '.$row['prijmeni'].' ('.$row['nick'].')';
      else if(!isset($_POST['book']) || $_POST['book'] == 0) $bookmaker_ar[$row['bookmaker_id']]['name'] = $row['jmeno'].' '.$row['prijmeni'].' ('.$row['nick'].')';
    }

    #Vyber sport#
    $sql = "select a.sport_id,a.nazev,b.udalost_id,b.nazev AS nazev_udalost from sport a inner join udalost b on a.sport_id=b.sport_id order by sport_id";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res))
      throw new ExHandler('Nepodarilo se provest dotaz: vyber sportu a udalosti',"admin_ex_db");

    $help = "";

    while ($row =& $res->fetchRow()){

      if($help == "")
        $help = $row['sport_id'];

      if(!isset($sport_ar[$row['sport_id']]))
        $pr = $preklad->FindPreklad($row['nazev'],1);

      $pr_ud = $preklad->FindPreklad($row['nazev_udalost'],1);

      if($row['sport_id'] != $help)
        $udalost .= '</optgroup>';

      if(!isset($sport_ar[$row['sport_id']]))
        $udalost .= '<optgroup label="'.$pr[1].'">';

      $udalost .= "<option  value=\"".$row['udalost_id']."\" ".(isset($_POST['udalost']) && $_POST['udalost']==$row['udalost_id']?"selected=\"selected\"":"").">".Help::Html($pr_ud[1])."</option>";
      $udalost_ar[$row['udalost_id']]['nazev'] = $pr_ud[1];

      if(!isset($sport_ar[$row['sport_id']])){
        $sport .= "<option  value=\"".$row['sport_id']."\" ".(isset($_POST['sport']) && $_POST['sport']==$row['sport_id']?"selected=\"selected\"":"").">".Help::Html($pr[1])."</option>";
        $sport_ar[$row['sport_id']]['nazev'] = $pr[1];
      }

      $help = $row['sport_id'];
      $udalost_sport[$row['sport_id']][] = $row['udalost_id'];
      $udalost_ar[$row['udalost_id']]['sport'] = $row['sport_id'];

    }

    $where = "";
    if(isset($_POST['od']) && It6_Date::checkFormat($_POST['od']))
      $where .= "platna_od>='".It6_Date::toDb($_POST['od'])."' and ";
    if(isset($_POST['do']) && It6_Date::checkFormat($_POST['do']))
      $where .= "platna_od<='".It6_Date::toDb($_POST['do'])."' and ";
    if(isset($_POST['sport']) && $_POST['sport'] != 0 && (!isset($_POST['udalost']) || $_POST['udalost'] == 0))
      $where .= "udalost_id in(".implode(',',$udalost_sport[intval($_POST['sport'])]).") and ";
    if(isset($_POST['udalost']) && $_POST['udalost'] != 0)
      $where .= "udalost_id=".intval($_POST['udalost'])." and ";
    if(isset($_POST['book']) && $_POST['book'] != 0)
      $where .= "bookmaker_id=".intval($_POST['book'])." and ";

    $where = substr($where,0,-4);
    if(mb_strlen($where) < 1) $where = "1";

    $sql = "SELECT * FROM sazky WHERE ".$where." ORDER BY platna_od";
//var_dump($sql);
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z sazky',"admin_ex_db");

    while ($row =& $res->fetchRow()){

      $bookmaker_ar[$row['bookmaker_id']]['sazky'][] = $row['sazka_id'];
      $sazky[$row['sazka_id']]['status'] = $row['status'];
      //TODO Ma to byt v gm nebo ne? Ted je. Jinak by se muselo dat , false
      $sazky[$row['sazka_id']]['platna_od'] = It6_Date::fromDbAsTimestamp($row['platna_od']);
      $sazky[$row['sazka_id']]['platna_do'] = It6_Date::fromDbAsTimestamp($row['platna_do']);
      $sazky[$row['sazka_id']]['proplacena'] = $row['proplacena'];
      $sazky[$row['sazka_id']]['vysledek'] = explode(";",$row['vysledek']);
      $sazky[$row['sazka_id']]['udalost_id'] = $row['udalost_id'];
      $sazky[$row['sazka_id']]['sport'] = $sport_ar[$udalost_ar[$row['udalost_id']]['sport']]['nazev'];
      $sazky[$row['sazka_id']]['udalost'] = $udalost_ar[$row['udalost_id']]['nazev'];
      $sazky[$row['sazka_id']]['overena'] = $row['overena'];
      $sazky[$row['sazka_id']]['udalost_name'] = $udalost_ar[$row['udalost_id']]['nazev'];
      $sazky[$row['sazka_id']]['dobry_tip'] = 0;
      $sazky[$row['sazka_id']]['spatny_tip'] = 0;
      $sazky[$row['sazka_id']]['vydelalo_herne'] = 0.00;
      $sazky[$row['sazka_id']]['prodelalo_herne'] = 0.00;

      $sazky_pole[] = $row['sazka_id'];

    }

    #Vyber uzivatelu#
    $sql = "
      SELECT a.user_id,a.jmeno,a.prijmeni,a.nick,b.mena_text
      FROM uzivatel a
      INNER JOIN mena b ON a.mena_id=b.mena_id
    ";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res))
      throw new ExHandler('Nepodarilo se provest dotaz: vyber z uzivatel a mena',"admin_ex_db");

    while ($row =& $res->fetchRow()){
      $user_ar[$row['user_id']]['jmeno'] = $row['jmeno'].' '.$row['prijmeni'];
      $user_ar[$row['user_id']]['nick'] = $row['nick'];
      $user_ar[$row['user_id']]['mena'] = $row['mena_text'];
    }

   $sql = "select *,(k.castka/k.akt_kurz) AS realna_castka,(k.kurz_celkem * (k.castka/k.akt_kurz)) AS vyhra_ticket from (SELECT *,(select POW(2.718281828459,c.hodnota)  from (select b.ticket_id,sum(ln(b.kurz)) AS hodnota from (SELECT d.ticket_id,d.kurz FROM `ticket_pohled` d where d.status<>1 and d.ticket_sazka_zrusena <>1 group by d.ticket_id,d.sazka_id) b group by b.ticket_id) c where c.ticket_id = a.ticket_id)  AS kurz_celkem,(SELECT g.kurz FROM kurz f INNER JOIN kurz_mena g ON f.id_kurz = g.id_kurz WHERE f.platny_od <= now( ) AND f.platny_do > now( ) AND g.id_mena = (SELECT h.mena_id FROM uzivatel h WHERE h.user_id=a.user_id )) AS akt_kurz FROM `ticket_pohled` a GROUP BY a.sazka_id, a.ticket_id) k  where k.ticket_id in (SELECT b.ticket_id FROM ticket_pohled b ".(count($sazky_pole)>0?"where b.sazka_id in(".implode(",",$sazky_pole).")":"")." group by b.ticket_id) order by k.zalozen desc";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z ticket_pohled',"admin_ex_db");

   while ($row =& $res->fetchRow()){

     if(!isset($ticket[$row['ticket_id']])){

      $ticket[$row['ticket_id']]['user_id'] = $row['user_id'];
      $ticket[$row['ticket_id']]['user'] = $user_ar[$row['user_id']]['jmeno'].' ('.$user_ar[$row['user_id']]['nick'].')';
      $ticket[$row['ticket_id']]['mena'] = $user_ar[$row['user_id']]['mena'];
      $ticket[$row['ticket_id']]['zalozen'] = It6_Date::fromDb($row['zalozen']);
      $ticket[$row['ticket_id']]['zruseno'] = $row['zruseno']; //zruseny cely ticket
      $ticket[$row['ticket_id']]['duvod_zruseni'] = $row['duvod_zruseni'];
      $ticket[$row['ticket_id']]['castka'] = $row['castka'];
      $ticket[$row['ticket_id']]['vyplacen'] = $row['vyplacen'];
      $ticket[$row['ticket_id']]['kurz_celkem'] = $row['kurz_celkem'];
      $ticket[$row['ticket_id']]['vyhra_ticket'] = $row['vyhra_ticket'];
      $ticket[$row['ticket_id']]['realna_castka'] = $row['realna_castka'];
      $ticket[$row['ticket_id']]['result'] = 0;
      $ticket[$row['ticket_id']]['kurz_soucet'] = 0;

     }

     $row['vysledek'] = explode(";",$row['vysledek']);
     if(!in_array($row['sloupec_id'],$row['vysledek']))  $ticket[$row['ticket_id']]['result']++; //pocet spatnych tipu na tiketu

     if($row['ticket_sazka_zrusena'] == 0) $ticket[$row['ticket_id']]['kurz_soucet'] += $row['kurz'];

     $ticket[$row['ticket_id']]['sazka'][$row['sazka_id']]['kurz'] = $row['kurz'];
     $ticket[$row['ticket_id']]['sazka'][$row['sazka_id']]['ticket_sazka_zrusena'] = $row['ticket_sazka_zrusena'];
     $ticket[$row['ticket_id']]['sazka'][$row['sazka_id']]['sloupec_id'] = $row['sloupec_id'];

   }

   foreach($ticket as $k=>$h){  //$k ticket_id

     //$pocet_sazek = count($h['sazky']);

    foreach($h['sazka'] as $k2=>$h2){ //$k2 sazka_id

     if(isset($sazky[$k2]) && $sazky[$k2]['status'] != 1 && $h['zruseno'] == 0 && $h2['ticket_sazka_zrusena'] == 0 && $h['vyplacen'] == 1){

       if(in_array($h2['sloupec_id'],$sazky[$k2]['vysledek'])){
          $sazky[$k2]['dobry_tip']++;
          if($h['result'] == 0) $sazky[$k2]['prodelalo_herne'] += (((($h2['kurz']/$h['kurz_soucet'])*100)/100)*$h['vyhra_ticket']);
       }else{
          $sazky[$k2]['spatny_tip']++;
          $sazky[$k2]['vydelalo_herne'] += ($h['realna_castka']/$h['result']);
       }

     }

    }

   }


   $this->vrat .= '<em>
                   Všechny částky jsou přepočítány na CZK <br />
                   Kolikrat se nekdo trefil kolikrat ne (nepocitaji se zrusene sazky, zrusene tikety, individualne zrusene sazky na tiketu , a tiket musí být proplacen).<br />
                   Kolik nam jeho sazky vydelaly a kolik nam prodelaly (nepocitaji se zrusene tikety, individualne zrusene sazky na tiketu, a tiket musí být proplacen)..<br />
                   Kolik nam sazka vydelala se spocita vydelenim castky na tiketu/pocet neuspesnych sazek na tiketu<br />
                   Kolik nam sazka prodelala se spocita: Vyjadrime % podil kurzu na souctu vsech kurzu na tiketu vyjma zrusenych. Z vyherni castky urcime % castku podle predesleho vypoctu.<br />
                   Jako prodelecne se pocitaji pouze sazky ktere jsou na vyhernich tiketech. Kdyz uzivatel spravne trefi vysledek, ale sazka je na nevyhernim tiketu nepocita se sazka jako vyherni ani proherni pro hernu.<br />
                   Na spravny a spatny tip v celych cislech nema vliv jestli je tiket vyherni nebo proherni. Udava pouze kolikrát se uživatele trefili resp. netrefili<br />
                  </em>';
   $this->vrat .= "<form method=\"post\" action=\"?graph=".intval($_GET['graph'])."&section=".$this->section."\">";

   $this->vrat .= '<table style="border:1px solid #E3E3E3;font-size:0.9em;background:#708090;color:white">';
   $this->vrat .= '<tr><td class="textleft" style="background:#929EAD;" colspan="4">Filtr</td></tr>';
   $this->vrat .= ' <tr><td class="textleft" style="background:#929EAD;" colspan="4">Formát data dd.mm.rrrr hh:mm:ss</td></tr>';
   $this->vrat .= '<tr><td>Od:</td><td> <input type="text" id="od" name="od" class="sinput3" value="'.(isset($_POST['od'])?Help::Html($_POST['od']):"").'" /><a href="javascript:window.open(\'calendar.php?cas=od\',\'\',\'width=140,height=247\');void(0);"><img src="_clip/calendar.gif" alt="Kalendář" class="img" /></a></td><td>Do:</td><td><input type="text" name="do" id="do" class="sinput3" value="'.(isset($_POST['do'])?Help::Html($_POST['do']):"").'" /><a href="javascript:window.open(\'calendar.php?cas=do\',\'\',\'width=140,height=247\');void(0);"><img src="_clip/calendar.gif" alt="Kalendář" class="img" /></a></td></tr>';
   $this->vrat .= '<tr><td>Bookmaker: </td><td colspan="3"><select style="font-size:0.8em" name="book"><option value="0">Všechny</option>'.$book.'</select></td></tr>';
   $this->vrat .= '<tr><td>Sport: </td><td colspan="3"><select style="font-size:0.8em" name="sport"><option value="0">Všechny</option>'.$sport.'</select></td></tr>';
   $this->vrat .= '<tr><td>Událost: </td><td colspan="3"><select style="font-size:0.8em" name="udalost"><option value="0">Všechny</option>'.$udalost.'</select></td></tr>';
   $this->vrat .= '<tr><td>Vypsat jednotlivé sázky: </td><td colspan="3"><input type="checkbox" class="no" name="bet_all" id="bet_all" '.(isset($_POST['bet_all'])?'checked="checked"':'').' /></td></tr>';
   $this->vrat .= '<tr><td colspan="4"><input type="submit" name="filtr" class="inputs" value="Filtr" /></td></tr>';
   $this->vrat .= '</table></form>';


   foreach($bookmaker_ar as $k=>$h){

    $bookmaker_ar[$k]['celkem_spravne_tip'] = $bookmaker_ar[$k]['celkem_spatne_tip'] = $bookmaker_ar[$k]['zrusene'] = $bookmaker_ar[$k]['aktualni'] = $bookmaker_ar[$k]['nevyhodnocene'] = $bookmaker_ar[$k]['vyhodnocene'] = $bookmaker_ar[$k]['overene'] = $bookmaker_ar[$k]['ukoncene'] = $bookmaker_ar[$k]['proplacene'] = 0;
    $bookmaker_ar[$k]['celkem_vydelalo'] = $bookmaker_ar[$k]['celkem_prodelalo'] = 0.00;

    if(isset($h['sazky']) && is_array($h['sazky'])){

    foreach($h['sazky'] as $h2){

     $bookmaker_ar[$k]['celkem_spravne_tip'] += $sazky[$h2]['dobry_tip'];
     $bookmaker_ar[$k]['celkem_spatne_tip'] += $sazky[$h2]['spatny_tip'];
     $bookmaker_ar[$k]['celkem_vydelalo'] += $sazky[$h2]['vydelalo_herne'];
     $bookmaker_ar[$k]['celkem_prodelalo'] += $sazky[$h2]['prodelalo_herne'];

     if($sazky[$h2]['status'] == 1) $bookmaker_ar[$k]['zrusene']++;
     else if($sazky[$h2]['platna_do'] > time() && $sazky[$h2]['status'] == 0) $bookmaker_ar[$k]['aktualni']++;
     else if($sazky[$h2]['platna_do'] > time() && $sazky[$h2]['status'] == 2) $bookmaker_ar[$k]['ukoncene']++;
     else if($sazky[$h2]['platna_do'] <= time() && ($sazky[$h2]['status'] == 0 || $sazky[$h2]['status'] == 2)) $bookmaker_ar[$k]['nevyhodnocene']++;
     else if($sazky[$h2]['overena'] == 0 && $sazky[$h2]['status'] == 3) $bookmaker_ar[$k]['vyhodnocene']++;
     else if($sazky[$h2]['overena'] != 0 && $sazky[$h2]['status'] == 3) $bookmaker_ar[$k]['overene']++;

     if($sazky[$h2]['proplacena'] == 1) $bookmaker_ar[$k]['proplacene']++;

    }
    }

   }

   foreach($bookmaker_ar as $k=>$h){
    if(!isset($h['sazky']) || !is_array($h['sazky'])) $h['sazky'] = array();
    $this->vrat .= '<br /><h3>'.$h['name'].'</h3><br />';
  $this->vrat .= 'Celkem vystavil sázek: '.count($h['sazky']).'<br />';
    $this->vrat .= 'Celkem správných tipů: '.$h['celkem_spravne_tip'].'<br />';
    $this->vrat .= 'Celkem špatných tipů: '.$h['celkem_spatne_tip'].'<br />';
    $this->vrat .= 'Celkem vydělalo herně: <span class="blue">'.round($h['celkem_vydelalo'],2).'</span> CZK<br />';
    $this->vrat .= 'Celkem prodělalo herně: <span class="red">'.round($h['celkem_prodelalo'],2).'</span> CZK<br />';
    $this->vrat .= 'Celkem zrušených sázek: '.$h['zrusene'].'<br />';
    $this->vrat .= 'Celkem aktuálních sázek: '.$h['aktualni'].'<br />';
    $this->vrat .= 'Celkem ukončených sázek: '.$h['ukoncene'].'<br />';
    $this->vrat .= 'Celkem nevyhodnocených sázek:'.$h['nevyhodnocene'].'<br />';
    $this->vrat .= 'Celkem vyhodnocených sázek:'.$h['vyhodnocene'].'<br />';
    $this->vrat .= 'Celkem ověřených sázek:'.$h['overene'].'<br />';
    $this->vrat .= 'Celkem proplacených sázek:'.$h['proplacene'].'<br />';

    if(isset($_POST['bet_all'])){

      $this->vrat .= '<hr /><br />';
      foreach($h['sazky'] as $h2){

       $this->vrat .= '<strong>Sázka #'.$h2.' '.$sazky[$h2]['sport'].' -> '.$sazky[$h2]['udalost'].':</strong><br />';
       $this->vrat .= 'Správných tipů: '.$sazky[$h2]['dobry_tip'].'<br />';
       $this->vrat .= 'Špatných tipů: '.$sazky[$h2]['spatny_tip'].'<br />';
       $this->vrat .= 'Vydělalo herně: <span class="blue">'.round($sazky[$h2]['vydelalo_herne'],2).'</span> CZK<br />';
       $this->vrat .= 'Prodělalo herně: <span class="red">'.round($sazky[$h2]['prodelalo_herne'],2).'</span> CZK<br />';
       $this->vrat .= '<br /><br />';

      }

    }

   }

  }




 /**
 * Statistiky sazek
 *
 * Na jaky sport nebo udalost ve sportu se nejvice sazi a filtr podle zemi a casu. Vyjadreno v cislech I penezich.
 * Kdo nejvic prosazel v penezich a v poctu tiketu
 * Kolik sazek se zverejnilo v jake obdobi  filtr podle casu. Pak podle sportu a take podle udalosti.
 *
 *
 * @return void
 */
  public function BetInfo(){

   $zeme_ar = $sport_ar = $udalost_ar = $user = $typ = $udalost_sport = array();
   $zeme = $sport = $udalost = "";
   $sazky = 0;

   $preklad = new Preklady();

   #Vyber zeme#
   $sql = "select a.nazev,a.zeme_id from zeme a";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

   while ($row =& $res->fetchRow()){

     $pr = $preklad->FindPreklad($row['nazev'],1);
     $zeme .= "<option  value=\"".$row['zeme_id']."\" ".(isset($_POST['zeme']) && $_POST['zeme']==$row['zeme_id']?"selected=\"selected\"":"").">".Help::Html($pr[1])."</option>";
     $zeme_ar[$row['zeme_id']]['nazev'] = $pr[1];

   }

   #Vyber typu#
   $sql = "select a.nazev,a.typ_id from typ a";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber typu',"admin_ex_db");

   while ($row =& $res->fetchRow()){

     $pr = $preklad->FindPreklad($row['nazev'],1);
     $typ[$row['typ_id']]['nazev'] = $pr[1];

   }

   #Vyber sport#
   $sql = "select a.sport_id,a.nazev,b.udalost_id,b.nazev AS nazev_udalost from sport a inner join udalost b on a.sport_id=b.sport_id order by sport_id";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sportu a udalosti',"admin_ex_db");
   $help = "";

   while ($row =& $res->fetchRow()){

     if($help == "") $help = $row['sport_id'];

     if(!isset($sport_ar[$row['sport_id']])) $pr = $preklad->FindPreklad($row['nazev'],1);
     $pr_ud = $preklad->FindPreklad($row['nazev_udalost'],1);

     if($row['sport_id'] != $help) $udalost .= '</optgroup>';
     if(!isset($sport_ar[$row['sport_id']])) $udalost .= '<optgroup label="'.$pr[1].'">';
     $udalost .= "<option  value=\"".$row['udalost_id']."\" ".(isset($_POST['udalost']) && $_POST['udalost']==$row['udalost_id']?"selected=\"selected\"":"").">".Help::Html($pr_ud[1])."</option>";
     $udalost_ar[$row['udalost_id']]['nazev'] = $pr_ud[1];

     if(!isset($sport_ar[$row['sport_id']])){
      $sport .= "<option  value=\"".$row['sport_id']."\" ".(isset($_POST['sport']) && $_POST['sport']==$row['sport_id']?"selected=\"selected\"":"").">".Help::Html($pr[1])."</option>";
      $sport_ar[$row['sport_id']]['nazev'] = $pr[1];
     }

     $help = $row['sport_id'];
     $udalost_sport[$row['sport_id']][] = $row['udalost_id'];
     $udalost_ar[$row['udalost_id']]['sport'] = $row['sport_id'];

   }

   ####Info tikety####
   $where = "";
   if(isset($_POST['od']) && It6_Date::checkFormat($_POST['od'])) $where .= "k.zalozen>='".It6_Date::toDb($_POST['od'])."' and ";
   if(isset($_POST['do']) && It6_Date::checkFormat($_POST['do'])) $where .= "k.zalozen<='".It6_Date::toDb($_POST['do'])."' and ";
   if(isset($_POST['sport']) && $_POST['sport'] != 0 && (!isset($_POST['udalost']) || $_POST['udalost'] == 0)) $where .= "k.udalost_id in(".implode(',',$udalost_sport[intval($_POST['sport'])]).") and ";
   if(isset($_POST['udalost']) && $_POST['udalost'] != 0) $where .= "k.udalost_id=".intval($_POST['udalost'])." and ";
   if(isset($_POST['zeme']) && $_POST['zeme'] != 0) $where .= "d.zeme_id=".intval($_POST['zeme'])." and ";

   $where = substr($where,0,-4);
   if(mb_strlen($where) < 1) $where = "1";

   $sql = "select k.udalost_id,k.typ_id,d.zeme_id,d.user_id,d.nick,(select b.castka/count(b.ticket_id) from ticket_pohled b where k.ticket_id=b.ticket_id group by b.ticket_id) / (SELECT g.kurz FROM kurz f INNER JOIN kurz_mena g ON f.id_kurz = g.id_kurz WHERE f.platny_od <= now( ) AND f.platny_do > now( ) AND g.id_mena=d.mena_id) AS vsazeno_sazka from ticket_pohled k inner join uzivatel d on k.user_id=d.user_id where ".$where." order by k.ticket_id";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z ticket_pohled',"admin_ex_db");

   while ($row =& $res->fetchRow()){

    if(!isset($sport_ar[$udalost_ar[$row['udalost_id']]['sport']]['pocet'])){
      $sport_ar[$udalost_ar[$row['udalost_id']]['sport']]['pocet'] = 1;
      $sport_ar[$udalost_ar[$row['udalost_id']]['sport']]['suma'] = $row['vsazeno_sazka'];
    }else{
      $sport_ar[$udalost_ar[$row['udalost_id']]['sport']]['pocet']++;
      $sport_ar[$udalost_ar[$row['udalost_id']]['sport']]['suma'] += $row['vsazeno_sazka'];
    }

    if(!isset($udalost_ar[$row['udalost_id']]['pocet'])){
      $udalost_ar[$row['udalost_id']]['pocet'] = 1;
      $udalost_ar[$row['udalost_id']]['suma'] = $row['vsazeno_sazka'];
    }else{
      $udalost_ar[$row['udalost_id']]['pocet']++;
      $udalost_ar[$row['udalost_id']]['suma'] += $row['vsazeno_sazka'];
    }

    if(!isset($user[$row['user_id']]['pocet'])){
      $user[$row['user_id']]['nazev'] = $row['nick'];
      $user[$row['user_id']]['pocet'] = 1;
      $user[$row['user_id']]['suma'] = $row['vsazeno_sazka'];
    }else{
      $user[$row['user_id']]['pocet']++;
      $user[$row['user_id']]['suma'] += $row['vsazeno_sazka'];
    }

    if(!isset($zeme_ar[$row['zeme_id']]['pocet'])){
      $zeme_ar[$row['zeme_id']]['pocet'] = 1;
      $zeme_ar[$row['zeme_id']]['suma'] = $row['vsazeno_sazka'];
    }else{
      $zeme_ar[$row['zeme_id']]['pocet']++;
      $zeme_ar[$row['zeme_id']]['suma'] += $row['vsazeno_sazka'];
    }

    if(!isset($typ[$row['typ_id']]['pocet'])){
      $typ[$row['typ_id']]['pocet'] = 1;
      $typ[$row['typ_id']]['suma'] = $row['vsazeno_sazka'];
    }else{
      $typ[$row['typ_id']]['pocet']++;
      $typ[$row['typ_id']]['suma'] += $row['vsazeno_sazka'];
    }

   }
   ####End Info tikety####

   ####Info sazky####
   $where = "";
   if(isset($_POST['od']) && It6_Date::checkFormat($_POST['od'])) $where .= "k.platna_od>='".It6_Date::toDb($_POST['od'])."' and ";
   if(isset($_POST['do']) && It6_Date::checkFormat($_POST['do'])) $where .= "k.platna_do<='".It6_Date::toDb($_POST['do'])."' and ";
   if(isset($_POST['sport']) && $_POST['sport'] != 0 && (!isset($_POST['udalost']) || $_POST['udalost'] == 0)) $where .= "k.udalost_id in(".implode(',',$udalost_sport[intval($_POST['sport'])]).") and ";
   if(isset($_POST['udalost']) && $_POST['udalost'] != 0) $where .= "k.udalost_id=".intval($_POST['udalost'])." and ";

   $where = substr($where,0,-4);
   if(mb_strlen($where) < 1) $where = "1";

   $sql = "select k.udalost_id from sazky k where ".$where;
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z sazky',"admin_ex_db");


   while ($row =& $res->fetchRow()){

    $sazky++;

    if(!isset($sport_ar[$udalost_ar[$row['udalost_id']]['sport']]['sazky_pocet'])){
      $sport_ar[$udalost_ar[$row['udalost_id']]['sport']]['sazky_pocet'] = 1;
    }else{
      $sport_ar[$udalost_ar[$row['udalost_id']]['sport']]['sazky_pocet']++;
    }

    if(!isset($udalost_ar[$row['udalost_id']]['sazky_pocet'])){
      $udalost_ar[$row['udalost_id']]['sazky_pocet'] = 1;
    }else{
      $udalost_ar[$row['udalost_id']]['sazky_pocet']++;
    }

   }
   ####End Info sazky####

   $this->vrat .= '<em>Ve filtru má přednost Událost před sportem<br />
                    Částka na sázku se vzpočítává jako počet sázek na tiketu/vsazená částka<br />
                    Do výsledků jsou započítány i zrušené sázky a tikety
                   </em>';

   $this->vrat .= "<form method=\"post\" action=\"?graph=".intval($_GET['graph'])."&section=".$this->section."\">";

   $this->vrat .= '<table style="border:1px solid #E3E3E3;font-size:0.9em;background:#708090;color:white">';
   $this->vrat .= '<tr><td class="textleft" style="background:#929EAD;" colspan="4">Filtr</td></tr>';
   $this->vrat .= ' <tr><td class="textleft" style="background:#929EAD;" colspan="4">Formát data dd.mm.rrrr hh:mm:ss</td></tr>';
   $this->vrat .= '<tr><td>Od:</td><td> <input type="text" id="od" name="od" class="sinput3" value="'.(isset($_POST['od'])?Help::Html($_POST['od']):"").'" /><a href="javascript:window.open(\'calendar.php?cas=od\',\'\',\'width=140,height=247\');void(0);"><img src="_clip/calendar.gif" alt="Kalendář" class="img" /></a></td><td>Do:</td><td><input type="text" name="do" id="do" class="sinput3" value="'.(isset($_POST['do'])?Help::Html($_POST['do']):"").'" /><a href="javascript:window.open(\'calendar.php?cas=do\',\'\',\'width=140,height=247\');void(0);"><img src="_clip/calendar.gif" alt="Kalendář" class="img" /></a></td></tr>';
   $this->vrat .= '<tr><td>Sport: </td><td colspan="3"><select style="font-size:0.8em" name="sport"><option value="0">Všechny</option>'.$sport.'</select></td></tr>';
   $this->vrat .= '<tr><td>Událost: </td><td colspan="3"><select style="font-size:0.8em" name="udalost"><option value="0">Všechny</option>'.$udalost.'</select></td></tr>';
   $this->vrat .= '<tr><td class="textleft" style="background:#929EAD;" colspan="4">Kolik se vsazeno</td></tr>';
   $this->vrat .= '<tr>Země: </td><td colspan="3"><select style="font-size:0.8em" name="zeme"><option value="0">Všechny</option>'.$zeme.'</select></td></tr>';

   $this->vrat .= '<tr><td colspan="4"><input type="submit" name="filtr" class="inputs" value="Filtr" /></td></tr>';
   $this->vrat .= '</table></form>';

   $this->vrat .= '<h3>Vsazeno na</h3>';

   #Sport a udalosti#
   $this->vrat .= '<div class="bet_info"><table><tr><td colspan="2" class="bet_info_headline"> Sport/Událost</td></tr>';
   foreach($sport_ar as $k=>$h){
     if(!isset($h['pocet'])){$h['pocet'] = 0;$h['suma'] = 0.00;}
     $this->vrat .= '<tr><td class="bet_info_td_left"><strong>'.$h['nazev'].'</strong></td><td class="bet_info_td_right">'.$h['pocet'].'x - '.round($h['suma'],2).' CZK</td></tr>';
     foreach($udalost_sport[$k] as $h2){
       if(!isset($udalost_ar[$h2]['pocet'])){$udalost_ar[$h2]['pocet'] = 0;$udalost_ar[$h2]['suma'] = 0.00;}
       $this->vrat .= '<tr><td class="bet_info_td_left">'.$udalost_ar[$h2]['nazev'].'</td><td class="bet_info_td_right">'.$udalost_ar[$h2]['pocet'].'x - '.round($udalost_ar[$h2]['suma'],2).' CZK</td></tr>';
     }
   }

   $this->vrat .= '</table></div>';

   #Uzivatele#
   $this->vrat .= '<div class="bet_info"><table><tr><td colspan="2" class="bet_info_headline">Uživatelé (Počet sázek na tiketech - celková suma vsazená na tiketech)</td></tr>';
   foreach($user as $k=>$h){
     if(!isset($h['pocet'])){$h['pocet'] = 0;$h['suma'] = 0.00;}
     $this->vrat .= '<tr><td class="bet_info_td_left"><strong>'.$h['nazev'].'</strong></td><td class="bet_info_td_right">'.$h['pocet'].'x - '.round($h['suma'],2).' CZK</td></tr>';
   }

   $this->vrat .= '</table></div>';

   #Zeme#
   $this->vrat .= '<div class="bet_info"><table><tr><td colspan="2" class="bet_info_headline">Země</td></tr>';
   foreach($zeme_ar as $k=>$h){
     if(!isset($h['pocet'])){$h['pocet'] = 0;$h['suma'] = 0.00;}
     $this->vrat .= '<tr><td class="bet_info_td_left"><strong>'.$h['nazev'].'</strong></td><td class="bet_info_td_right">'.$h['pocet'].'x - '.round($h['suma'],2).' CZK</td></tr>';
   }

   $this->vrat .= '</table></div>';

   #Typ#
   $this->vrat .= '<div class="bet_info"><table><tr><td colspan="2" class="bet_info_headline">Typ</td></tr>';
   foreach($typ as $k=>$h){
     if(!isset($h['pocet'])){$h['pocet'] = 0;$h['suma'] = 0.00;}
     $this->vrat .= '<tr><td class="bet_info_td_left"><strong>'.$h['nazev'].'</strong></td><td class="bet_info_td_right">'.$h['pocet'].'x - '.round($h['suma'],2).' CZK</td></tr>';
   }

   $this->vrat .= '</table></div>';
   $this->vrat .= '<br /><br />';
   $this->vrat .= '<h3>Sum sázky</h3>';

   #Sport a udalosti#
   $this->vrat .= '<p>Celkem vypsaných sázek: '.$sazky.'</p>';
   $this->vrat .= '<div class="bet_info"><table><tr><td colspan="2" class="bet_info_headline"> Sport/Událost</td></tr>';
   foreach($sport_ar as $k=>$h){
     if(!isset($h['sazky_pocet'])){$h['sazky_pocet'] = 0;$h['suma'] = 0.00;}
     $this->vrat .= '<tr><td class="bet_info_td_left"><strong>'.$h['nazev'].'</strong></td><td class="bet_info_td_right">'.$h['sazky_pocet'].'x</td></tr>';
     foreach($udalost_sport[$k] as $h2){
       if(!isset($udalost_ar[$h2]['sazky_pocet'])){$udalost_ar[$h2]['sazky_pocet'] = 0;$udalost_ar[$h2]['suma'] = 0.00;}
       $this->vrat .= '<tr><td class="bet_info_td_left">'.$udalost_ar[$h2]['nazev'].'</td><td class="bet_info_td_right">'.$udalost_ar[$h2]['sazky_pocet'].'x</td></tr>';
     }
   }

   $this->vrat .= '</table></div>';
   $this->vrat .= '<br /><br />';

}

/** Statistiky tiketu
 * Kolik bylo vsazeno a kolik vyplaceno s moznosti filtrace obdobi, zeme uzivatele atd...
 * Kolik tiketu bylo vyhernich kolik prohernich a kolik je neukoncenych s moznosti filtrace obdobi
 * Kolik je prumerne sazek na tiketu
 * Kolik sazkaru vsadilo
 * @param int $nomenu
 * @return void */
public function TicketInfo($nomenu = false) {

	global $systemAr;

	$this->nomenu = $nomenu;
	$this->infoGlobal[6] = 0;

	if (isset($_REQUEST['user'])) $_POST['user'] = $_REQUEST['user'];

	$user = $zeme = '';
	$user_ar = $zeme_ar = $ticket = $uid = array();

	$preklad = new Preklady();

	#Vyber uzivatelu#
	$sql = "SELECT a.jmeno, a.prijmeni, a.nick, a.user_id, a.zeme_id, b.mena_text FROM uzivatel a
			INNER JOIN mena b ON a.mena_id = b.mena_id WHERE a.e_testovaci = 'ne' 
			AND user_id NOT IN (".implode(",",$GLOBALS['EXCLUDEUSER']).")"
			.(isset($_POST['zeme']) && $_POST['zeme'] != 0?"AND a.zeme_id=".intval($_POST['zeme']):"");
	$res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

	while ($row =& $res->fetchRow()) {
		$user .= "<option  value=\"".$row['user_id']."\" ".(isset($_POST['user']) && $_POST['user']==$row['user_id']?"selected=\"selected\"":"").">".Help::Html($row['nick'])."</option>";
		$user_ar[$row['user_id']]['nick'] = $row['nick'];
		$user_ar[$row['user_id']]['jmeno'] = $row['jmeno']." ".$row['prijmeni'];
		$user_ar[$row['user_id']]['mena'] = $row['mena_text'];
		$user_ar[$row['user_id']]['zeme'] = $row['zeme_id'];
		$uid[] = $row['user_id'];
	}

	#Vyber zeme#
	$sql = "select nazev,zeme_id from zeme";
	$res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

	while ($row =& $res->fetchRow()) {
		$pr = $preklad->FindPreklad($row['nazev'],1);
		$zeme .= "<option  value=\"".$row['zeme_id']."\" ".(isset($_POST['zeme']) && $_POST['zeme']==$row['zeme_id']?"selected=\"selected\"":"").">".Help::Html($pr[1])."</option>";
		$zeme_ar[$row['zeme_id']] = $pr[1];
	}

	$this->dateBool = true;

	#Vyber sazek#
	$where = $where2 = "";
	if(isset($_POST['od']) && It6_Date::checkFormat($_POST['od'])) {
		$where .= "k.zalozen>='".It6_Date::toDb($_POST['od'])."' and ";
		$this->dateBool = true;
	}
	if(isset($_POST['do']) && It6_Date::checkFormat($_POST['do'])) {
		$where .= "k.zalozen<='".It6_Date::toDb($_POST['do'])."' and ";
		$this->dateBool = true;
	}
	if(isset($_POST['user']) && $_POST['user'] != 0) $where .= "k.user_id=".intval($_POST['user'])." and ";
	if(isset($_POST['zeme']) && $_POST['zeme'] != 0) $where .= "k.user_id in(".implode(',',$uid).") and ";

	$where = substr($where,0,-4);
	if(mb_strlen($where) < 1) $where = "1";

	$where2 = str_replace("k.","b.",$where);

	$sql = "SELECT k.*,x.mena_id FROM `ticket_pohled` k INNER JOIN uzivatel x ON k.user_id=x.user_id 
			WHERE x.e_testovaci = 'ne' 
			AND ".$where." ".($this->dateBool == false ? (isset($_POST['user']) && $_POST['user'] != 0 ? "AND k.stats_user=0" : "AND k.stats=0") : "");

	$res2 =& $this->dbGame->query($sql);
	if (DB::isError($res2)) throw new ExHandler('<br /><br />'.$sql.'<br /><br />Nepodarilo se provest dotaz: vyber z pohledu ticket_pohled, sazka_kurz a podtyp_sloupec',"admin_ex_db");

	$mena = $system_ar = $system_status_ticket = array();

	while ($row =& $res2->fetchRow()) {
		if (!isset($mena[$row['mena_id']])) {
			$sql = "SELECT g.kurz FROM kurz f INNER JOIN kurz_mena g ON f.id_kurz = g.id_kurz 
					WHERE f.platny_od <= now() AND f.platny_do > now( ) AND g.id_mena =".intval($row['mena_id']);
			$res4 =& $this->dbGame->query($sql);
			if (DB::isError($res4)) throw new ExHandler('Nepodarilo se provest dotaz: vyber kurzy',"admin_ex_db");
			if ($row4 =& $res4->fetchRow()) $mena[$row['mena_id']] = $row4['kurz'];
		}

		if (!isset($ticket[$row['ticket_id']])) {
			$ticket[$row['ticket_id']]['user_id'] = $row['user_id'];
			$ticket[$row['ticket_id']]['user'] = $user_ar[$row['user_id']]['jmeno'].' ('.$user_ar[$row['user_id']]['nick'].')';
			$ticket[$row['ticket_id']]['mena'] = $user_ar[$row['user_id']]['mena'];
			$ticket[$row['ticket_id']]['zalozen'] = It6_Date::fromDb($row['zalozen']);
			$ticket[$row['ticket_id']]['zruseno'] = $row['zruseno']; //zruseny cely ticket
			$ticket[$row['ticket_id']]['duvod_zruseni'] = $row['duvod_zruseni'];
			$ticket[$row['ticket_id']]['castka'] = $row['castka'];
			$ticket[$row['ticket_id']]['vyplacen'] = $row['vyplacen'];
			$ticket[$row['ticket_id']]['free_bet_bonus'] = $row['free_bet_bonus'];
			$ticket[$row['ticket_id']]['kurz_celkem'] = 1;
			$ticket[$row['ticket_id']]['mena_kurz'] = $mena[$row['mena_id']];
			$ticket[$row['ticket_id']]['banker_rate'] = 1;
			$ticket[$row['ticket_id']]['vyhra_ticket'] = 0;//$row['vyhra_ticket'];
			$ticket[$row['ticket_id']]['win_real'] = $row['win_real'] - $row['mp_win_amount'];
			$ticket[$row['ticket_id']]['realna_castka'] = ($row['castka']/$mena[$row['mena_id']]);
			$ticket[$row['ticket_id']]['result'] = 1;
			$ticket[$row['ticket_id']]['sazka'] = 0;
			$ticket[$row['ticket_id']]['system'] = $row['system'];
			$ticket[$row['ticket_id']]['banker_num'] = 0;
			$system_status_ticket[$row['ticket_id']] = 0;
			if ($row['free_bet_bonus']==1) $this->infoGlobal[6]++;
		}

		if ($row['status'] == 1 || $row['zruseno'] == 1) $ticket[$row['ticket_id']]['kurz_celkem'] *= 1;
		else if($row['ticket_sazka_zrusena'] == 1) $ticket[$row['ticket_id']]['kurz_celkem'] *= 1;
		else $ticket[$row['ticket_id']]['kurz_celkem'] *= $row['kurz'];

		$row['vysledek'] = explode(";",$row['vysledek']);

		if($row['vyplacen'] == 1 && $row['zruseno'] != 1 && $row['ticket_sazka_zrusena'] != 1 && $row['status'] != 1 && !in_array($row['sloupec_id'],$row['vysledek'])) $ticket[$row['ticket_id']]['result'] = 0;
		$ticket[$row['ticket_id']]['sazka']++;

		$ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['banker'] = $row['banker'];

		if ($row['system'] != 0) {
			if ($row['zruseno'] != 1 && $row['ticket_sazka_zrusena'] != 1 && $row['status'] != 1 && !in_array($row['sloupec_id'],$row['vysledek']) && $row['vyplacen'] == 1) {
				if ($row['banker']==1) $system_status_ticket[$row['ticket_id']] = 1;
				$ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['trefa'] = 2; //spatny tip
			} else if ($row['vyplacen'] == 1) {
				$ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['trefa'] = 1; //spravny tip
			}

			if ($row['banker'] == 1) {
				$row['kurz'] = ($row['zruseno'] == 1 || $row['ticket_sazka_zrusena'] == 1 || $row['status'] == 1 ? 1 : $row['kurz']);
				$ticket[$row['ticket_id']]['banker_rate'] = $ticket[$row['ticket_id']]['banker_rate'] * $row['kurz'];
			} else {
				$dbtimeticket = It6_Date::toTimestamp($ticket[$row['ticket_id']]['zalozen']);
				$dbtimeend = It6_Date::toTimestamp('31.3.2009 23:59:59');

				if ($dbtimeticket < $dbtimeend) {
					if (isset($system_ar[$row['ticket_id']])) {
						$klic = count($system_ar[$row['ticket_id']]);
						$system_ar[$row['ticket_id']][$klic]['sazka_id'] = $row['sazka_id'];
						$system_ar[$row['ticket_id']][$klic]['rate'] = ($row['zruseno'] == 1 || $row['ticket_sazka_zrusena'] == 1 || $row['status'] == 1 ? 1 : $row['kurz']);
					}
				} else {
					if (!isset($system_ar[$row['ticket_id']])) $system_ar[$row['ticket_id']] = array();
					$klic = count($system_ar[$row['ticket_id']]);
					$system_ar[$row['ticket_id']][$klic]['sazka_id'] = $row['sazka_id'];
					$system_ar[$row['ticket_id']][$klic]['rate'] = ($row['zruseno'] == 1 || $row['ticket_sazka_zrusena'] == 1 || $row['status'] == 1 ? 1 : $row['kurz']);
				}
			}
		}
	}

	foreach ($ticket as $k => $h) {
		$ticket[$k]['vyhra_ticket'] = ($h['realna_castka']*$h['kurz_celkem']);
	}

	$vsazeno_celkem = $vsazeno_celkem2 = $ticket_num = $vsazeno_celkem_pay2 = 
	$pocet_sazek_tiket_pay = $vsazeno_celkem_pay = $vyhra_celkem = $skutecna_vyhra = 
	$skutecna_prohra = $skutecna_prohra2 = $vyherni_tiket = $proherni_tiket = 
	$nevyplaceny_tiket = $zruseny_tiket = $pocet_sazek_tiket = 
	$vyhodnocene_amount = $vyhodnocene_win_amount = 0;
	$sazkar = array();

	$this->dbGame->autocommit(false);

	foreach ($ticket as $k => $h) {
		if ($h['system'] != 0) {
			$castka_rad = ($h['realna_castka']/$systemAr[count($system_ar[$k])][$h['system']]);
			$GLOBALS['system_special_ar'] = Array();
			$vyhra_celkem +=  Help::ReQSystem(0,$h['system'],0,$system_ar[$k],$h['banker_rate'],1,$castka_rad);

			if ($h['vyplacen'] == 1 && $h['zruseno'] != 1) {
				$system_win = 0;

				foreach ($GLOBALS['system_special_ar'] as $h3) {
					if ($system_status_ticket[$k] == 1) break;
					$help_status_system = true;
					foreach ($h3['sazky'] as $h4) {
						if ($ticket[$k]['sazky'][$h4]['trefa'] == 2) $help_status_system = false;
					}
					if ($help_status_system) $system_win += $h3['vyhra'];
				}

				$plus = ($system_win - $h['realna_castka']);

				if ($plus >= 0) {
					$this->TopInfoTicket($system_win,$k,1,(isset($_POST['top']) && is_numeric($_POST['top'])?intval($_POST['top']):20));
					$skutecna_vyhra = $skutecna_vyhra + $plus;
					$vyherni_tiket++;
				} else if($plus < 0 && $h['free_bet_bonus'] == 0) {
					$this->TopInfoTicket((-1*$plus),$k,0,(isset($_POST['top']) && is_numeric($_POST['top'])?intval($_POST['top']):20),$ticket[$k]['user']);
					$skutecna_prohra += (-1*$plus);
					$proherni_tiket++;
					$skutecna_prohra2 = $skutecna_prohra2 + (-1*$plus);
				} else if($plus < 0) {
					$skutecna_prohra2 = $skutecna_prohra2 +(-1*$plus);
				}
			}
		}

		$vsazeno_celkem += $h['realna_castka'];

		if ($h['vyplacen'] == 1 && $h['zruseno'] != 1 &&  $h['free_bet_bonus'] == 0) $vsazeno_celkem2 += $h['realna_castka'];

		if ($h['system'] == 0 && $h['vyplacen'] == 1 && $h['result'] == 1 && $h['zruseno'] == 0) {
			$this->TopInfoTicket($h['vyhra_ticket'],$k,1,(isset($_POST['top']) && is_numeric($_POST['top'])?intval($_POST['top']):20),$ticket[$k]['user']);
			$skutecna_vyhra = $skutecna_vyhra + ($h['vyhra_ticket']-$h['realna_castka']);
			$vyhodnocene_win_amount += $h['win_real'];
			$vyhodnocene_amount += $h['castka'];
			$vyherni_tiket++;
		} else if($h['system'] == 0 && $h['vyplacen'] == 1 && $h['free_bet_bonus'] == 0 && $h['result'] == 0 && $h['zruseno'] == 0) {
			$this->TopInfoTicket($h['realna_castka'],$k,0,(isset($_POST['top']) && is_numeric($_POST['top'])?intval($_POST['top']):20),$ticket[$k]['user']);
			$skutecna_prohra += $h['realna_castka'];
			$vyhodnocene_win_amount += $h['win_real'];
			$vyhodnocene_amount += $h['castka'];
			$proherni_tiket++;
			$skutecna_prohra2 = $skutecna_prohra2 + $h['realna_castka'];
		} else if($h['system'] == 0 && $h['vyplacen'] == 1 && $h['result'] == 0 && $h['zruseno'] == 0) {
			$skutecna_prohra2 = $skutecna_prohra2 + $h['realna_castka'];
			$vyhodnocene_amount += $h['castka'];
		} else if($h['vyplacen'] == 1 && $h['zruseno'] == 1) {
			$zruseny_tiket++;
			$vyhodnocene_amount += $h['castka'];
		}

		if ($h['vyplacen'] == 0) $nevyplaceny_tiket++;

		$sazkar[$h['user_id']] = 1;
		$pocet_sazek_tiket += $h['sazka'];

		if ($h['vyplacen'] == 1 && $this->dateBool == false) {
			if ($h['zruseno'] != 1) $vsazeno_celkem_pay2 += $h['realna_castka'];
			$ticket_num++;
			$vsazeno_celkem_pay += $h['realna_castka'];
			$pocet_sazek_tiket_pay += $h['sazka'];

			if (isset($_POST['user']) && $_POST['user'] != 0) {
				$sql = "update ticket set stats_user=1 where ticket_id=".$k;
				$res =& $this->dbGame->query($sql);
				if (DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
			} else {
				$sql = "update ticket set stats=1 where ticket_id=".$k;
				$res =& $this->dbGame->query($sql);
				if (DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
			}
		}
	}

	if ($this->dateBool == false) {

		if (isset($_POST['user']) && $_POST['user'] != 0) {

			$sql = "select ticket_num,bet_stats_win,bet_stats_lose_acc,bet_stats_lose_book,bet_total,bet_total2,win_ticket,lose_ticket,delete_ticket,num_bet_ticket from uzivatel where user_id=".intval($_POST['user']);
			$res =& $this->dbGame->query($sql);
			if (DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

			if ($row =& $res->fetchRow()) {
				$skutecna_vyhra += $row['bet_stats_win'];
				$skutecna_prohra += $row['bet_stats_lose_acc'];
				$skutecna_prohra2 += $row['bet_stats_lose_book'];
				$vsazeno_celkem_pay += $row['bet_total'];
				$vsazeno_celkem_pay2 += $row['bet_total2'];
				$vsazeno_celkem += $row['bet_total'];
				$vsazeno_celkem2 += $row['bet_total2'];
				$vyherni_tiket  += $row['win_ticket'];
				$proherni_tiket += $row['lose_ticket'];
				$zruseny_tiket  += $row['delete_ticket'];
				$pocet_sazek_tiket+= $row['num_bet_ticket'];
				$pocet_sazek_tiket_pay += $row['num_bet_ticket'];
				$ticket_num += $row['ticket_num'];
				$celkem_tiket = count($ticket) + $row['ticket_num'];

				$sql = "update uzivatel set ticket_num=".$ticket_num.",num_bet_ticket=".$pocet_sazek_tiket_pay.",win_ticket=".$vyherni_tiket.",lose_ticket=".$proherni_tiket.",delete_ticket=".$zruseny_tiket.",bet_total2=".$vsazeno_celkem_pay2.",bet_total=".$vsazeno_celkem_pay.",bet_stats_win=".$skutecna_vyhra.",bet_stats_lose_acc=".$skutecna_prohra.",bet_stats_lose_book=".$skutecna_prohra2." where user_id=".intval($_POST['user']);
				$res =& $this->dbGame->query($sql);
				if (DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
			} else {
				throw new ExHandler('Nepodarilo se najit uzivatele',"admin_ex_page");
			}
		} else {

			$sql = "select ticket_num,bet_total,bet_total2,pay_to_user,user_lose_acc,user_lose_book,win_ticket,lose_ticket,delete_ticket,num_bet_ticket from bet_stats";
			$res =& $this->dbGame->query($sql);
			if (DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

			if ($row =& $res->fetchRow()) {
				$skutecna_vyhra += $row['pay_to_user'];
				$skutecna_prohra += $row['user_lose_acc'];
				$skutecna_prohra2 += $row['user_lose_book'];
				$vsazeno_celkem_pay += $row['bet_total'];
				$vsazeno_celkem_pay2 += $row['bet_total2'];
				$vsazeno_celkem += $row['bet_total'];
				$vsazeno_celkem2 += $row['bet_total2'];
				$vyherni_tiket  += $row['win_ticket'];
				$proherni_tiket += $row['lose_ticket'];
				$zruseny_tiket  += $row['delete_ticket'];
				$pocet_sazek_tiket+= $row['num_bet_ticket'];
				$pocet_sazek_tiket_pay += $row['num_bet_ticket'];
				$ticket_num += $row['ticket_num'];
				$celkem_tiket = count($ticket) + $row['ticket_num'];

				$sql = "update bet_stats set ticket_num=".$ticket_num.",num_bet_ticket=".$pocet_sazek_tiket_pay.",win_ticket=".$vyherni_tiket.",lose_ticket=".$proherni_tiket.",delete_ticket=".$zruseny_tiket.",bet_total2=".$vsazeno_celkem_pay2.",bet_total=".$vsazeno_celkem_pay.",pay_to_user=".$skutecna_vyhra.",user_lose_acc=".$skutecna_prohra.",user_lose_book=".$skutecna_prohra2;
				$res =& $this->dbGame->query($sql);
				if (DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
			}
		}

		#pocet sazkaru#
		$sql = "select count(user_id) AS pocet from uzivatel where vyhernost IS NOT NULL";
		$res =& $this->dbGame->query($sql);
		if (DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
		if ($row =& $res->fetchRow()) $sazkari = $row['pocet'];
	}

	$this->dbGame->commit();

	$cista_vyhra = ($skutecna_vyhra - $skutecna_prohra);
	$cista_vyhra2 = ($skutecna_vyhra - $skutecna_prohra2);
	$vyhernost_pr = ($vsazeno_celkem2) ? ((($vsazeno_celkem2 + $cista_vyhra) / $vsazeno_celkem2 ) *100) : 0;
	$vyhernost_pr2 = ($vsazeno_celkem2) ? ((($vsazeno_celkem2 + $cista_vyhra2) / $vsazeno_celkem2 ) *100) : 0;

	if ($this->nomenu == false) {
		$this->vrat .= '
		Všechny částky jsou převedeny na CZK
		<form method="post" action="?section='.$this->section.'">
			<table class="filtr">
				<tr><td class="head" colspan="4">Filtr</td></tr>
				<tr>
					<td>UID:</td>
					<td>
						<input type="text" name="user" 
						value="'.(isset($_POST['user']) ? Help::Html($_POST['user']) : "").'" />
					</td>
					<td>Země:</td>
					<td>
						<select style="font-size:0.8em" name="zeme">
							<option value="0">Všechny</option>'.$zeme.'
						</select>
					</td>
				</tr>
				<tr>
					<td>Od:</td>
					<td>
						<input type="text" id="od" name="od" class="sinput3 dateTime"
							value="'.(isset($_POST['od'])?Help::Html($_POST['od']):"").'" />
						<img src="_clip/calendar.gif" class="calendar-icon">
					</td>
					<td>Do:</td>
					<td>
						<input type="text" name="do" id="do" class="sinput3 dateTime" 
							value="'.(isset($_POST['do'])?Help::Html($_POST['do']):"").'" />
						<img src="_clip/calendar.gif" class="calendar-icon">
					</td>
				</tr>
				<tr>
					<td>Vypsat top:</td>
					<td colspan="3">
						<input type="text" class="sinput3" name="top" 
							value="'.(isset($_POST['top']) && is_numeric($_POST['top'])?intval($_POST['top']):"20").'" />
					</td>
				</tr>
				<tr>
					<td colspan="4"><input type="submit" name="filtr" class="inputs" value="Filtr" /></td>
				</tr>
			</table>
		</form>';
	}

	if ($this->dateBool == false) {

		$this->infoGlobal[0] = $celkem_tiket;
		$this->infoGlobal[1] = round($vsazeno_celkem,2);
		$this->infoGlobal[2] = round($skutecna_vyhra,2);
		$this->infoGlobal[3] = round($skutecna_prohra,2);
		$this->infoGlobal[4] = round($cista_vyhra,2);
		$this->infoGlobal[5] = round($vyhernost_pr,2) ;

		$this->vrat .= '<table>';
		$this->vrat .= '<tr><td><strong>Celkem tiketů:</strong> </td><td>'.$celkem_tiket.'</td></tr>';
		$this->vrat .= '<tr><td><strong>Celkem vsazeno:</strong></td><td> '.round($vsazeno_celkem,2).' CZK</td></tr>';
		$this->vrat .= '<tr><td colspan="2"><h3>FINANCE</h3></td></tr>';
		$this->vrat .= '<tr><td><strong>Skutečně bylo vyplaceno:</strong></td><td> '.round($skutecna_vyhra,2).' CZK</td></tr>';
		$this->vrat .= '<tr><td><strong>Skutečně hráči prohráli:</strong></td><td> '.round($skutecna_prohra,2).' CZK</td></tr>';
		$this->vrat .= '<tr><td><strong>Čistá výhra:</strong></td><td> '.round($cista_vyhra,2).' CZK</td></tr>';
		$this->vrat .= '<tr><td style="width:300px;"><strong>Výhernost:</strong></td><td> '.round($vyhernost_pr,2).' %</td></tr>';
		$this->vrat .= '<tr><td colspan="2"><h3>BOOKMAKERS TEAM</h3></td></tr>';
		$this->vrat .= '<tr><td><strong>Skutečně bylo vyplaceno:</strong></td><td> '.round($skutecna_vyhra,2).' CZK</td></tr>';
		$this->vrat .= '<tr><td><strong>Skutečně hráči prohráli:</strong></td><td> '.round($skutecna_prohra2,2).' CZK</td></tr>';
		$this->vrat .= '<tr><td><strong>Čistá výhra:</strong></td><td> '.round($cista_vyhra2,2).' CZK</td></tr>';
		$this->vrat .= '<tr><td><strong>Výhernost:</strong></td><td> '.round($vyhernost_pr2,2).' %</td></tr>';
		$this->vrat .= '<tr><td colspan="2"><h3>OTHER INFO</h3></td></tr>';
		$this->vrat .= '<tr><td><strong>Výherních tiketů:</strong></td><td> '.$vyherni_tiket.'</td></tr>';
		$this->vrat .= '<tr><td><strong>Proherních tiketů:</strong></td><td> '.$proherni_tiket.'</td></tr>';
		$this->vrat .= '<tr><td><strong>Zrušených tiketů:</strong></td><td> '.$zruseny_tiket.'</td></tr>';
		$this->vrat .= '<tr><td><strong>Nevyplacených tiketů:</strong></td><td> '.($celkem_tiket-$ticket_num).'</td></tr>';
		$this->vrat .= '<tr><td><strong>Průmerně na tiketu sázek:</strong></td><td> '.round(($pocet_sazek_tiket/$celkem_tiket),2).'</td></tr>';
		$this->vrat .= '<tr><td><strong>Celkem sázkařů:</strong></td><td> '.$sazkari.'</td></tr>';
		$this->vrat .= '</table>';

		$sql = "select * from bet_stats_winners where wl=1 order by amount desc limit 0,".(isset($_POST['top']) && is_numeric($_POST['top'])?intval($_POST['top']):20);
		$res =& $this->dbGame->query($sql);
		if (DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

		$this->vrat .= '<h3>Top '.(isset($_POST['top']) && is_numeric($_POST['top'])?intval($_POST['top']):20).' výher</h3>';
		while ($row =& $res->fetchRow()) {
			$this->vrat .= 'Tiket <a href="?section=148&ticket_id_search='.$row['ticket_id'].'">#'.$row['ticket_id'].'</a>: '.round($row['amount'],2).' CZK : '.$row['user'].'<br />';
		}

		$this->vrat .= '<br />';

		$sql = "select * from bet_stats_winners where wl=0 order by amount desc limit 0,".(isset($_POST['top']) && is_numeric($_POST['top'])?intval($_POST['top']):20);
		$res =& $this->dbGame->query($sql);
		if (DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

		$this->vrat .= '<h3>Top '.(isset($_POST['top']) && is_numeric($_POST['top'])?intval($_POST['top']):20).' proher</h3>';

		while ($row =& $res->fetchRow()) {
			$this->vrat .= 'Tiket <a href="?section=148&ticket_id_search='.$row['ticket_id'].'">#'.$row['ticket_id'].'</a>: '.round($row['amount'],2).' CZK : '.$row['user'].'<br />';
		}

	} else {

		$this->infoGlobal[0] = count($ticket);
		$this->infoGlobal[1] = round($vsazeno_celkem,2);
		$this->infoGlobal[2] = round($skutecna_vyhra,2);
		$this->infoGlobal[3] = round($skutecna_prohra,2);
		$this->infoGlobal[4] = round($cista_vyhra,2);
		$this->infoGlobal[5] = round($vyhernost_pr,2) ;

		$this->vrat .= '<table>';
		$this->vrat .= '<tr><td><strong>Celkem tiketů:</strong> </td><td>'.count($ticket).'</td></tr>';
		$this->vrat .= '<tr><td><strong>Celkem vsazeno:</strong></td><td> '.number_format($vsazeno_celkem, 2, '.', ' ').' CZK</td></tr>';
		$this->vrat .= '<tr><td colspan="2"><br /><h3>VYHODNOCENÉ</h3></td></tr>';
		$this->vrat .= '<tr><td><strong>Počty tiketů - vsazeno / vyhráno / prohráno:</strong></td><td>'.($vyherni_tiket+$proherni_tiket).' / '.$vyherni_tiket.' / '.$proherni_tiket.'</td></tr>';
		$this->vrat .= '<tr><td><strong>Vsazeno:</strong></td><td>'.number_format($vyhodnocene_amount, 2, '.', ' ').' CZK</td></tr>';
		$this->vrat .= '<tr><td><strong>Vyhráno:</strong></td><td>'.number_format($vyhodnocene_win_amount, 2, '.', ' ').' CZK</td></tr>';
		$this->vrat .= '<tr><td><strong>YIELD:</strong></td><td>';
		$this->vrat .= ($vyhodnocene_amount > 0) ? round(((($vyhodnocene_win_amount-$vyhodnocene_amount)/$vyhodnocene_amount)*100),2) : 0;
		$this->vrat .= '%</td></tr>';
		//$this->vrat .= '<tr><td><strong>Skutečně bylo vyplaceno:</strong></td><td> '.round($skutecna_vyhra,2).' CZK</td></tr>';
		//$this->vrat .= '<tr><td><strong>Skutečně hráči prohráli:</strong></td><td> '.round($skutecna_prohra2,2).' CZK</td></tr>';
		//$this->vrat .= '<tr><td><strong>Čistá výhra:</strong></td><td> '.round($cista_vyhra2,2).' CZK</td></tr>';
		//$this->vrat .= '<tr><td><strong>Výhernost:</strong></td><td> '.round($vyhernost_pr2,2).' %</td></tr>';
		$this->vrat .= '<tr><td colspan="2"><br /><h3>DALŠÍ INFORMACE</h3></td></tr>';
		$this->vrat .= '<tr><td><strong>Zrušených tiketů:</strong></td><td> '.$zruseny_tiket.'</td></tr>';
		$this->vrat .= '<tr><td><strong>Nevyplacených tiketů:</strong></td><td> '.$nevyplaceny_tiket.'</td></tr>';
		$this->vrat .= '<tr><td><strong>Průmerně na tiketu sázek:</strong></td><td>';
		$this->vrat .= (count($ticket) > 0) ? round(($pocet_sazek_tiket/count($ticket)),2) : 0;
		$this->vrat .= '</td></tr>';
		$this->vrat .= '<tr><td><strong>Celkem sázkařů:</strong></td><td> '.count($sazkar).'</td></tr>';
		$this->vrat .= '</table>';

		$this->vrat .= '<h3>Top '.(isset($_POST['top']) && is_numeric($_POST['top'])?intval($_POST['top']):20).' výher</h3>';
		if (isset($this->vyhra_ar)) {
			foreach ($this->vyhra_ar as $h) {
				$this->vrat .= 'Tiket <a href="?section=148&ticket_id_search='.$h['ticket'].'">#'.$h['ticket'].'</a>: '.round($h['suma'],2).' CZK : '.$ticket[$h['ticket']]['user'].($ticket[$h['ticket']]['system'] != 0?' SYSTEM ':'').'<br />';
			}
		}

		$this->vrat .= '<br />';

		$this->vrat .= '<h3>Top '.(isset($_POST['top']) && is_numeric($_POST['top'])?intval($_POST['top']):20).' proher</h3>';
		if (isset($this->prohra_ar)) {
			foreach ($this->prohra_ar as $h) {
				$this->vrat .= 'Tiket <a href="?section=148&ticket_id_search='.$h['ticket'].'"> #'.$h['ticket'].'</a>: '.round($h['suma'],2).' CZK : '.$ticket[$h['ticket']]['user'].($ticket[$h['ticket']]['system'] != 0?' SYSTEM ':'').'<br />';
			}
		}
	}
}

/**
 * metoda zjisti top prodelky a vydelky u tiketu
 * @param float $sum id sazky nebo tiketu
 * @param int $id  rozdil mezi tim co sazka/tiket vydelala a prodela
 * @param int $akce 1=vyhra tiketu;0=prohra tiketu
 * @param int $top pocet vypisu
 * @param string $user uzivatel
 * @return void
 */
private function TopInfoTicket($sum,$id,$akce,$top=20,$user) {

	if ($this->dateBool == false) {

		if ($akce == 1) {
			$sql = "replace into bet_stats_winners values (".intval($id).",".floatval($sum).",'".Help::Slash($user)."',1)";
			$res =& $this->dbGame->query($sql);
			if (DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
		} else if($akce == 0) {
			$sql = "replace into bet_stats_winners values (".intval($id).",".floatval($sum).",'".Help::Slash($user)."',0)";
			$res =& $this->dbGame->query($sql);
			if (DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
		}

	} else {

		if ($akce == 1) {
			for ($x=0; $x<$top; $x++) {
				if (!isset($this->vyhra_ar[$x])) {
					$this->vyhra_ar[$x]['suma'] = $sum;
					$this->vyhra_ar[$x]['ticket'] = $id;
					break;
				}
				$help_sum = $this->vyhra_ar[$x]['suma'];
				$help_tiket = $this->vyhra_ar[$x]['ticket'];
				if ($sum > $this->vyhra_ar[$x]['suma']) {
					$this->vyhra_ar[$x]['suma'] = $sum;
					$this->vyhra_ar[$x]['ticket'] = $id;
					$sum = $help_sum;
					$id = $help_tiket;
				}
			}
		} else if($akce == 0) {
			for ($x=0; $x<$top; $x++) {
				if (!isset($this->prohra_ar[$x])) {
					$this->prohra_ar[$x]['suma'] = $sum;
					$this->prohra_ar[$x]['ticket'] = $id;
					break;
				}
				$help_sum = $this->prohra_ar[$x]['suma'];
				$help_tiket = $this->prohra_ar[$x]['ticket'];
				if ($sum > $this->prohra_ar[$x]['suma']) {
					$this->prohra_ar[$x]['suma'] = $sum;
					$this->prohra_ar[$x]['ticket'] = $id;
					$sum = $help_sum;
					$id = $help_tiket;
				}
			}
		}
	}
}


/**
 * Nastaveni prav k sekci
 * @param int $update pravo zapisu
 * @param int $delete pravo smazani
 * @return void
 */
public function setPrivileges($update,$delete) {
	$this->update = $update;
	$this->delete = $delete;
}

/**
 * Vraci vystup do tridy main
 * @return string
 */
public function getContent() {
	$this->time = time() - $this->time;
	if($this->nomenu==false)$this->time = "Požadavek se vyřizoval <em>".$this->time."</em> sekund<br /><br />";
	return $this->vrat;
}

public function __destruct() {}

}