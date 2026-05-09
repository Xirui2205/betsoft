<?php

/**
 * @package    statistics
 */


 /**
 * Trida pro praci se statistikami Veci co uzivatel udelal
 *
 *
 * @package    main
 */

class StatistikyUzivatelAkce{

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
 * spojeni na databazi arena
 * @access private
 * @var DB
 */
private  $dbArena;

/**
 * spojeni na databazi Betwarehosue
 * @access private
 * @var DB
 */
private  $dbBetWare;

/**
 * pole top vyhernich tiketu
 * @access private
 * @var array
 */
private  $vyhra_ar;

/**
 *ma se ukazat filtr formular
 * @access private
 * @var bool
 */
private  $nomenu=false;

/**
 * pole top prohernich tiketu
 * @access private
 * @var array
 */
private  $prohra_ar;

/**
 * pole uzivatelskych uctu a balanci
 * @access private
 * @var array
 */
private  $user_balance = array();

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


     $this->dbBetWare = DB::connect(WDATABASE ."://". WMY_USER .":". WMY_PASS ."@". WMY_HOST ."/". WMY_DB);
     if (DB::isError($this->dbBetWare)) {
      throw new ExHandler($this->dbBetWare->getMessage(),"admin_ex_db");
     }
     $this->dbBetWare->setFetchMode(DB_FETCHMODE_ASSOC);
     $sql = "set names 'utf8'";
     $res =& $this->dbBetWare->query($sql);
     if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");



  }

   /**
 * Metoda spousti jednitlove metody podle stavu
 * @param int $nomenu
 * @return void
 */
  public  function runAction($nomenu=false){

  	$this->nomenu = $nomenu;

  	if(isset($_GET['x']) && $_GET['x'] == 'runitnow'){
      //$radek = file('/var/www/release/admin/tmp/chyba.csv');
      //$uuu = '0,';
      //foreach($radek as $klic=>$hodnota){
      //$uuu .= intval($hodnota).',';
      //}
      //$uuu .= substr($uuu,0,-1);

      $sql = "
        SELECT a.user_id,a.nick,b.zustatek,b.zetony,(
          SELECT g.kurz
          FROM kurz f
          INNER JOIN kurz_mena g ON f.id_kurz = g.id_kurz
          WHERE f.platny_od <= now( )
            AND f.platny_do >= now( )
            AND g.id_mena=a.mena_id) AS kurz
        FROM uzivatel a
        INNER JOIN uzivatel_im_data b ON a.user_id=b.user_id
        ORDER BY a.user_id
      ";

      $res =& $this->dbGame->query($sql);
      if(DB::isError($res))
        throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

      while ($row =& $res->fetchRow()){
        $      $fp_REQUEST['user'] = $row['user_id'];
        $this->user_balance[$row['user_id']]['saldo'] = 0;
        $this->user_balance[$row['user_id']]['nick'] = $row['nick'];
        $this->user_balance[$row['user_id']]['zustatek'] =  intval($row['zustatek'] + ($row['zetony']*$row['kurz']));
        $this->Info();
        $this->user_balance[$row['user_id']]['saldo'] = intval($this->user_balance[$row['user_id']]['saldo']);
      }

      $fp = fopen('/var/www/release/admin/tmp/text.txt');
      fputs($fp,'<table><tr><th>USER ID</th><th>NICK</th><th>ZUSTATEK</th><th>SALDO</th></tr>');

      foreach ($this->user_balance as $k=>$h) {
        if($h['saldo'] != $h['zustatek'])
          fputs($fp,'<tr><td>'.$k.'</td><td> '.$h['nick'].'</td><td>'.$h['zustatek'].'</td><td>:'.$h['saldo'].'</td></tr>');
      }
     fputs($fp,'</table>');
     fclose($fp);
    }

    //echo "<pre>";print_r($this->user_balance);
    //if(!$fp)
      //throw new ExHandler('Nepodarilo se otevrit soubor pro zapis',"admin_ex_file");;
    //echo "<table><tr><th>USER ID</th><th>NICK</th><th>ZUSTATEK</th><th>SALDO</th></tr>";
    //foreach ($this->user_balance as $k=>$h) {
      //if($h['saldo'] > $h['zustatek']){
        //$sql = "update uzivatel_im_data set zustatek=zustatek+".($h['saldo']-$h['zustatek'])." where user_id=".$k;echo $sql."<br>";
        //$res =& $this->dbGame->query($sql);
        //if(DB::isError($res))
          //throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
      //}
      //if($h['saldo'] != $h['zustatek'])
        //echo "<tr><td>".$k.'</td><td> '.$h['nick'].'</td><td>'.$h['zustatek'].'</td><td>:'.$h['saldo']."</td></tr>";
    //}
    //echo "</table>";

    $this->vrat = '';
    $this->Info();

    $this->dbGame->disconnect();
  }




  /**
 * Statistiky Happy hours
 *
 *
 * @return void
 */
  public function Info(){
    global $systemAr;

  	if(isset($_REQUEST['user']))
      $_POST['user'] = $_REQUEST['user'];

  	if(isset($_POST['user']) && intval($_POST['user']) == 0)
      unset($_POST['user']);


  	if(!isset($_REQUEST['user'])){
      $_REQUEST['user'] = 0;
      $_POST['user'] = 0;
    }

  	$user       = '';
    $user_ar    = array();
    $zeme_ar    = array();
    $ticket     = array();
    $uid        = array();
    $usr_gr     = array();
    $date_gr    = array();
    $deleteAkce = array();

    $preklad = new Preklady();

    if(isset($_POST['user']) && ctype_digit($_POST['user'])){
      $stmt = Zend_Registry::get('zdb_game')->query('select od,do from uzivatel_smazane_akce where user_id='.$_POST['user']);
      while ($row = $stmt->fetch()) {
      	//TODO Ma to byt v gm nebo ne? Ted je. Jinak by se muselo dat , false
        $deleteAkce[] = array(It6_Date::fromDbAsTimestamp($row['od']),It6_Date::fromDbAsTimestamp($row['do']));
      }
    }

    #Vyber uzivatelu#
    $sql = "
      SELECT
        a.jmeno,
        a.prijmeni,
        a.nick,
        a.user_id,
        a.zeme_id,
        b.mena_text,
        c.zustatek,
        c.zustatek_bonus
      FROM uzivatel a
      INNER JOIN uzivatel_im_data c ON a.user_id=c.user_id
      INNER JOIN mena b ON a.mena_id=b.mena_id
      WHERE a.user_id=".Help::Slash($_REQUEST['user'])."
        AND a.user_id NOT IN(
          ".implode(",",$GLOBALS['EXCLUDEUSER']).")
          ".(isset($_POST['zeme']) && $_POST['zeme'] != 0?"AND a.zeme_id=".intval($_POST['zeme']):"")."
      ORDER BY nick
    ";

    $res =& $this->dbGame->query($sql);
    if(DB::isError($res))
      throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

    while ($row =& $res->fetchRow()){
      //$user .= "<option  value=\"".$row['user_id']."\" ".(isset($_POST['user']) && $_POST['user']==$row['user_id']?"selected=\"selected\"":"").">".Help::Html($row['nick'])."</option>";
      $user_ar[$row['user_id']]['nick'] = $row['nick'];
      $user_ar[$row['user_id']]['jmeno'] = $row['jmeno']." ".$row['prijmeni'];
      $user_ar[$row['user_id']]['mena'] = $row['mena_text'];
      $user_ar[$row['user_id']]['zeme'] = $row['zeme_id'];
      $user_ar[$row['user_id']]['zustatek'] = $row['zustatek'];
      $user_ar[$row['user_id']]['zustatek_bonus'] = $row['zustatek_bonus'];
      $uid[] = $row['user_id'];
    }

    if(!isset($_POST['user']) || intval($_POST['user']) == 0)
      $this->vrat .="Musíte zvolit uživatele";
    else{
      #Vyber akci#
      $where = "";
      if(isset($_POST['user']) && $_POST['user'] != 0)
        $where .= "b.user_id=".intval($_POST['user'])." and ";
      if(isset($_POST['od']) && It6_Date::checkFormat($_POST['od']))
        $where .= ' b.zalozen>"'.It6_Date::toDb($_POST['od']).'" and';
      if(isset($_POST['do']) && It6_Date::checkFormat($_POST['do']))
        $where .= ' b.zalozen<"'.It6_Date::toDb($_POST['do']).'" and';


      $where = substr($where,0,-4);
      if(mb_strlen($where) < 1)
        $where = "1";

      if(isset($_POST['filtr']) || (isset($_POST['user'])&& intval($_POST['user']) != 0)){
        $user_action = array();

        $sql = "
          SELECT b.mena_text,b.mena_id
          FROM uzivatel a
          INNER JOIN mena b ON a.mena_id=b.mena_id
          WHERE a.user_id=".intval($_REQUEST['user'])
        ;

        $res2 =& $this->dbGame->query($sql);
        if(DB::isError($res2))
          throw new ExHandler('Nepodarilo se provest dotaz: vzber her',"admin_ex_db");

        if ($row =& $res2->fetchRow()){
          $mena = $row['mena_text'];
          $mena_id = $row['mena_id'];
        }
        else{
          $mena     = "měna nenalezena";
          $mena_id  = 0;
        }

        $sql = "
          SELECT g.kurz FROM kurz f
          INNER JOIN kurz_mena g ON f.id_kurz = g.id_kurz
          WHERE f.platny_od <= now( )
            AND f.platny_do >= now( )
            AND g.id_mena=".$mena_id
        ;

        $res3 =& $this->dbGame->query($sql);
        if(DB::isError($res3))
          throw new ExHandler('<br /><br />'.$sql.'<br /><br />Nepodarilo se provest dotaz: vyber kurzu',"admin_ex_db");

        if ($row2 =& $res3->fetchRow())
          $rate = $row2['kurz'];
        else
          $this->vrat ='Uživatel nenalezen';


        #Info balance#
        if(isset($_GET['balance'])){
          $where3 = "";
          if(isset($_POST['user']) && $_POST['user'] != 0)
            $where3 .= "user_id=".intval($_POST['user'])." and ";
          if(isset($_POST['od']) && It6_Date::checkFormat($_POST['od']))
            $where3 .= ' datum>"'.It6_Date::toDb($_POST['od']).'" and';
          if(isset($_POST['do']) && It6_Date::checkFormat($_POST['do']))
            $where3 .= ' datum<"'.It6_Date::toDb($_POST['do']).'" and';


          $where3 = substr($where3,0,-4);
          if(mb_strlen($where3) < 1) $where3 = "1";

          $sql = "SELECT castka,zetony,dluh,datum FROM balance_log WHERE ".$where3;
          $res2 =& $this->dbBetWare->query($sql);
          if(DB::isError($res2))
            throw new ExHandler('Nepodarilo se provest dotaz: vzber her',"admin_ex_db");


          while ($row =& $res2->fetchRow()){
            $row['datum'] = It6_Date::fromDb($row['datum']);
            $user_action[It6_Date::toTimestamp($row['datum'])]['balance']['castka'] = $row['castka'];
            $user_action[It6_Date::toTimestamp($row['datum'])]['balance']['zetony'] = $row['zetony'];
            $user_action[It6_Date::toTimestamp($row['datum'])]['balance']['dluh']   = $row['dluh'];
          }
        }



        #Info sazky#

        $ticket     = array();
        $system_ar  = array();

        $sql = "SELECT * FROM ticket_pohled b WHERE ".$where;
        $res2 =& $this->dbGame->query($sql);
        if(DB::isError($res2))
          throw new ExHandler('Nepodarilo se provest dotaz: vzber her',"admin_ex_db");

        while ($row =& $res2->fetchRow()){
          if(!isset($ticket[$row['ticket_id']])){
            $ticket[$row['ticket_id']]['vraceny'] = '';
            $sql = "SELECT datum,sazka_id FROM vracene_sazky WHERE ticket_id=".$row['ticket_id'];

            $res =& $this->dbGame->query($sql);
             if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: update  sazky',"admin_ex_db");}

            while ($row2 =& $res->fetchRow()){
              $ticket[$row['ticket_id']]['vraceny'] .= '
                <br />Vráceno: '.It6_Date::fromDb($row2['datum']) .'(#'.$row2['sazka_id'].')
              ';
            }

            $ticket[$row['ticket_id']]['kurz'] = 1;
            $ticket[$row['ticket_id']]['status'] = true;
            $ticket[$row['ticket_id']]['banker_rate'] = 1;
            $ticket[$row['ticket_id']]['system'] = $row['system'];
            $ticket[$row['ticket_id']]['banker_num'] = 0;
            $system_status_ticket[$row['ticket_id']] = 0;
          }

          $vysledek = explode(";",$row['vysledek']);
          if($row['status'] != 1 && $row['zruseno'] != 1 && $row['ticket_sazka_zrusena'] == 0 && !in_array($row['sloupec_id'],$vysledek))
            $ticket[$row['ticket_id']]['status'] = false;


          if($row['status'] == 1);
          else if($row['ticket_sazka_zrusena'] == 1);
          else if($row['zruseno'] == 1);
          else $ticket[$row['ticket_id']]['kurz'] = $ticket[$row['ticket_id']]['kurz'] * $row['kurz'];

          if($row['system'] != 0){

            if($row['status'] != 1 && $row['zruseno'] != 1 && $row['ticket_sazka_zrusena'] == 0 && !in_array($row['sloupec_id'],$vysledek) && $row['vyplacen'] == 1){
              if($row['banker']==1){
                $system_status_ticket[$row['ticket_id']]=1;
                $ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['trefa'] = 2;
              }
            }  //spatny tip
            else $ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['trefa'] = 1;  //spravny tip

            if($row['banker'] == 1){
              $row['kurz'] = ($row['zruseno'] == 1 || $row['ticket_sazka_zrusena'] == 1 || $row['status'] == 1?1:$row['kurz']);
              $ticket[$row['ticket_id']]['banker_rate'] = $ticket[$row['ticket_id']]['banker_rate'] * $row['kurz'];
              $ticket[$row['ticket_id']]['banker_num']++;
            }
            else{
              $klic = count($system_ar[$row['ticket_id']]);
              $system_ar[$row['ticket_id']][$klic]['sazka_id'] = $row['sazka_id'];
              $system_ar[$row['ticket_id']][$klic]['rate'] = ($row['zruseno'] == 1 || $row['ticket_sazka_zrusena'] == 1 || $row['status'] == 1?1:$row['kurz']);
            }
          }

        }

        $sql = "SELECT * FROM ticket_pohled b WHERE ".$where;
        $res2 =& $this->dbBetWare->query($sql);
        if(DB::isError($res2))
          throw new ExHandler('Nepodarilo se provest dotaz: vzber her',"admin_ex_db");

        while ($row =& $res2->fetchRow()){
          if(!isset($ticket[$row['ticket_id']])){
            $ticket[$row['ticket_id']]['kurz'] = 1;
            $ticket[$row['ticket_id']]['status'] = true;
            $ticket[$row['ticket_id']]['banker_rate'] = 1;
            $ticket[$row['ticket_id']]['system'] = $row['system'];
            $ticket[$row['ticket_id']]['banker_num'] = 0;
            $system_status_ticket[$row['ticket_id']] = 0;
          }

          $vysledek = explode(";",$row['vysledek']);
          if($row['status'] != 1 && $row['zruseno'] != 1 && $row['ticket_sazka_zrusena'] == 0 && !in_array($row['sloupec_id'],$vysledek))
            $ticket[$row['ticket_id']]['status'] = false;

          if($row['status'] == 1);
          else if($row['ticket_sazka_zrusena'] == 1);
          else if($row['zruseno'] == 1);
          else $ticket[$row['ticket_id']]['kurz'] = $ticket[$row['ticket_id']]['kurz'] * $row['kurz'];

          if($row['system'] != 0){

            if($row['status'] != 1 && $row['zruseno'] != 1 && $row['ticket_sazka_zrusena'] == 0 && !in_array($row['sloupec_id'],$vysledek) && $row['vyplacen'] == 1) {
              if($row['banker']==1){
                $system_status_ticket[$row['ticket_id']]=1;
                $ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['trefa'] = 2;  //spatny tip
              }
            }
            else
              $ticket[$row['ticket_id']]['sazky'][$row['sazka_id']]['trefa'] = 1;  //spravny tip

            if($row['banker'] == 1){
              $row['kurz'] = ($row['zruseno'] == 1 || $row['ticket_sazka_zrusena'] == 1 || $row['status'] == 1?1:$row['kurz']);
              $ticket[$row['ticket_id']]['banker_rate'] = $ticket[$row['ticket_id']]['banker_rate'] * $row['kurz'];
              $ticket[$row['ticket_id']]['banker_num']++;
            }
            else{
              $klic = count($system_ar[$row['ticket_id']]);
              $system_ar[$row['ticket_id']][$klic]['sazka_id'] = $row['sazka_id'];
              $system_ar[$row['ticket_id']][$klic]['rate'] = ($row['zruseno'] == 1 || $row['ticket_sazka_zrusena'] == 1 || $row['status'] == 1?1:$row['kurz']);
            }
          }
        }

        $sql = "
          SELECT
            ticket_id,
            zalozen,
            castka,
            vyplacen,
            zruseno,
            system,
            free_bet_bonus,
            vyplacen_date
          FROM ticket b
          WHERE ".$where
        ;

        $res2 =& $this->dbGame->query($sql);
        if(DB::isError($res2))
          throw new ExHandler('Nepodarilo se provest dotaz: vyber her',"admin_ex_db");

        while ($row =& $res2->fetchRow()){
          $system_win = 0;

          if($row['system'] != 0){
            $castka_rad = ($row['castka']/$systemAr[count($system_ar[$row['ticket_id']])][$row['system']]);
            $GLOBALS['system_special_ar'] = Array();
            $vyhra_celkem =  Help::ReQSystem(0,$row['system'],0,$system_ar[$row['ticket_id']],$ticket[$row['ticket_id']]['banker_rate'],1,$castka_rad);
            if($row['vyplacen'] == 1){

              foreach($GLOBALS['system_special_ar'] as $h3){
                if($system_status_ticket[$row['ticket_id']] == 1)
                  break;
                $help_status_system = true;

                foreach($h3['sazky'] as $h4){
                  if($ticket[$row['ticket_id']]['sazky'][$h4]['trefa'] == 2)
                    $help_status_system = false;
                  }
                if($help_status_system)
                  $system_win += $h3['vyhra'];
              }
            }
          }

          $row['zalozen'] =  It6_Date::fromDb($row['zalozen']);
          $row['vyplacen_date'] =  It6_Date::fromDb($row['vyplacen_date']);

          $klic = count($user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky']);
          $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['castka'] = $row['castka'];
          $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['ticket_id'] = $row['ticket_id'];
          $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['system'] = $row['system'];
          $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['system_win'] = $system_win;
          $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['vyplacen'] = (isset($_GET['allinfo'])?0:$row['vyplacen']);
          //$user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['vyplacen'] = $row['vyplacen'];
          $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['zruseno'] = $row['zruseno'];
          $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['free_bet_bonus'] = $row['free_bet_bonus'];
          $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['stav'] = $ticket[$row['ticket_id']]['status'];
          $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['kurz'] = $ticket[$row['ticket_id']]['kurz'];
          $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['vraceny'] = $ticket[$row['ticket_id']]['vraceny'];

          if($row['vyplacen'] == 1 && isset($_GET['allinfo'])){
            if($row['vyplacen_date'] == '00.00.0000 00:00:00')
              $row['vyplacen_date'] = $row['zalozen'];
            $klic = count($user_action[It6_Date::toTimestamp($row['vyplacen_date'])]['sazky']);
            $user_action[It6_Date::toTimestamp($row['vyplacen_date'])]['sazky'][$klic]['castka'] = $row['castka'];
            $user_action[It6_Date::toTimestamp($row['vyplacen_date'])]['sazky'][$klic]['ticket_id'] = $row['ticket_id'];
            $user_action[It6_Date::toTimestamp($row['vyplacen_date'])]['sazky'][$klic]['system'] = $row['system'];
            $user_action[It6_Date::toTimestamp($row['vyplacen_date'])]['sazky'][$klic]['system_win'] = $system_win;
            $user_action[It6_Date::toTimestamp($row['vyplacen_date'])]['sazky'][$klic]['vyplacen'] = 1;
            $user_action[It6_Date::toTimestamp($row['vyplacen_date'])]['sazky'][$klic]['zruseno'] = $row['zruseno'];
            $user_action[It6_Date::toTimestamp($row['vyplacen_date'])]['sazky'][$klic]['free_bet_bonus'] = $row['free_bet_bonus'];
            $user_action[It6_Date::toTimestamp($row['vyplacen_date'])]['sazky'][$klic]['stav'] = $ticket[$row['ticket_id']]['status'];
            $user_action[It6_Date::toTimestamp($row['vyplacen_date'])]['sazky'][$klic]['kurz'] = $ticket[$row['ticket_id']]['kurz'];
            $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['vraceny'] = $ticket[$row['ticket_id']]['vraceny'];
          }
        }

        $sql = "
          SELECT
            ticket_id,
            zalozen,
            castka,
            vyplacen,
            zruseno,
            system,
            free_bet_bonus,
            vyplacen_date
          FROM ticket b
          WHERE ".$where
        ;

        $res2 =& $this->dbBetWare->query($sql);
        if(DB::isError($res2))
          throw new ExHandler('Nepodarilo se provest dotaz: vyber her',"admin_ex_db");

        while($row =& $res2->fetchRow()){
          $system_win = 0;
          if($row['system'] != 0){
            $castka_rad = ($row['castka']/$systemAr[count($system_ar[$row['ticket_id']])][$row['system']]);
            $GLOBALS['system_special_ar'] = Array();
            $vyhra_celkem =  Help::ReQSystem(0,$row['system'],0,$system_ar[$row['ticket_id']],$ticket[$row['ticket_id']]['banker_rate'],1,$castka_rad);

            if($row['vyplacen'] == 1){

              foreach($GLOBALS['system_special_ar'] as $h3){
                if($system_status_ticket[$row['ticket_id']] == 1)
                  break;
                $help_status_system = true;

                foreach($h3['sazky'] as $h4){
                  if($ticket[$row['ticket_id']]['sazky'][$h4]['trefa'] == 2)
                    $help_status_system = false;
                }
                if($help_status_system)
                  $system_win += $h3['vyhra'];
              }
            }
          }

          $row['zalozen'] =  It6_Date::fromDb($row['zalozen']);
          $row['vyplacen_date'] =  It6_Date::fromDb($row['vyplacen_date']);

          $klic = count($user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky']);
          $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['castka'] = $row['castka'];
          $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['ticket_id'] = $row['ticket_id'];
          $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['system'] = $row['system'];
          $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['system_win'] = $system_win;
          $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['vyplacen'] = (isset($_GET['allinfo'])?0:$row['vyplacen']);
          // $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['vyplacen'] = $row['vyplacen'];
          $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['zruseno'] = $row['zruseno'];
          $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['free_bet_bonus'] = $row['free_bet_bonus'];
          $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['stav'] = $ticket[$row['ticket_id']]['status'];
          $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['kurz'] = $ticket[$row['ticket_id']]['kurz'];
          $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['vraceny'] = $ticket[$row['ticket_id']]['vraceny'];

          if($row['vyplacen'] == 1 && isset($_GET['allinfo'])){
            if($row['vyplacen_date'] == '00.00.0000 00:00:00')
              $row['vyplacen_date'] = $row['zalozen'];
            $klic = count($user_action[It6_Date::toTimestamp($row['vyplacen_date'])]['sazky']);
            $user_action[It6_Date::toTimestamp($row['vyplacen_date'])]['sazky'][$klic]['castka'] = $row['castka'];
            $user_action[It6_Date::toTimestamp($row['vyplacen_date'])]['sazky'][$klic]['ticket_id'] = $row['ticket_id'];
            $user_action[It6_Date::toTimestamp($row['vyplacen_date'])]['sazky'][$klic]['system'] = $row['system'];
            $user_action[It6_Date::toTimestamp($row['vyplacen_date'])]['sazky'][$klic]['system_win'] = $system_win;
            $user_action[It6_Date::toTimestamp($row['vyplacen_date'])]['sazky'][$klic]['vyplacen'] = 1;
            $user_action[It6_Date::toTimestamp($row['vyplacen_date'])]['sazky'][$klic]['zruseno'] = $row['zruseno'];
            $user_action[It6_Date::toTimestamp($row['vyplacen_date'])]['sazky'][$klic]['free_bet_bonus'] = $row['free_bet_bonus'];
            $user_action[It6_Date::toTimestamp($row['vyplacen_date'])]['sazky'][$klic]['stav'] = $ticket[$row['ticket_id']]['status'];
            $user_action[It6_Date::toTimestamp($row['vyplacen_date'])]['sazky'][$klic]['kurz'] = $ticket[$row['ticket_id']]['kurz'];
            $user_action[It6_Date::toTimestamp($row['zalozen'])]['sazky'][$klic]['vraceny'] = $ticket[$row['ticket_id']]['vraceny'];
          }
        }
          //print_r($user_action);
       #Info finance#

        $where = "";
        if(isset($_POST['user']) && $_POST['user'] != 0)
          $where .= "a.user_id=".intval($_POST['user'])." and ";
        if(isset($_POST['od']) && It6_Date::checkFormat($_POST['od']))
          $where .= ' a.datum>"'.It6_Date::toDb($_POST['od']).'" and';
        if(isset($_POST['do']) && It6_Date::checkFormat($_POST['do']))
          $where .= ' a.datum<"'.It6_Date::toDb($_POST['do']).'" and';


        $where = substr($where,0,-4);
        if(mb_strlen($where) < 1)
          $where = "1";
/*
        $sql = "
          SELECT a.*,TRANSLATE(b.nazev,1) AS PM
          FROM finacni_transakce a
          INNER JOIN platebni_metody b ON a.metoda_id=b.metoda_id
          WHERE ".$where
        ;

        $res2 =& $this->dbGame->query($sql);
        if(DB::isError($res2))
          throw new ExHandler('Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

        while ($row =& $res2->fetchRow()){
          //TODO Ma to byt v gm nebo ne? Ted je. Jinak by se muselo dat , false
          $row['acctrans_T'] =  It6_Date::fromDbAsTimestamp($row['datum']);
          $user_action[$row['acctrans_T']]['finance']['status'] =  ($row['typ_platby']==1?"Vklad":"Výběr");
          $user_action[$row['acctrans_T']]['finance']['priznak'] =  "OK";
          $user_action[$row['acctrans_T']]['finance']['metoda'] =  $row['PM'];
          $user_action[$row['acctrans_T']]['finance']['castka'] =  $row['castka'];
          $user_action[$row['acctrans_T']]['finance']['poplatky'] =  $row['poplatky'];
          $user_action[$row['acctrans_T']]['finance']['dluh'] =  0;
        }
*/
      }
    }

    $this->vrat .= '';
    if($this->nomenu==false){
      $this->vrat .= '
        <form method="post" action="?section='.$this->section.'">
          <table class="filtr">
            <tr>
              <td class="head" >Filtr</td>
            </tr>

            <tr>
              <td>
                <input
                  type="text"
                  class="input dateTime"
                  name="od"
                  maxlength="19"
                  value="'.(isset($_REQUEST['od'])?Help::Html($_REQUEST['od']):"").'"
                />
                <img src="_clip/calendar.gif" class="calendar-icon">
              </td>
              <td>
                <input
                  type="text"
                  maxlength="19"
                  class="input dateTime"
                  name="do"
                  value="'.(isset($_REQUEST['do'])?Help::Html($_REQUEST['do']):"").'"
                />
                <img src="_clip/calendar.gif" class="calendar-icon">
              </td>
            </tr>

            <tr>
              <td>Uživatel:</td>
              <td>
                <input type="text" name="user" id="user" size="4" value="'.Help::Html($_REQUEST['user']).'">
              </td>
            </tr>

            <tr>
              <td colspan="4">
                <input type="submit" name="filtr" class="inputs" value="Filtr" />

              </td>
            </tr>
          </table>
        </form>
      ';

      if(isset($_REQUEST['user'])){
        $this->vrat .= '
          Zůstatek: '.$user_ar[$_REQUEST['user']]['zustatek'].'
          <br />
          <a href="?section=40&user_id='.$_REQUEST['user'].'">Uživatel data</a>&nbsp;&nbsp;
          <a href="?section=88&user='.$_REQUEST['user'].'">Zkrácený výpis</a>&nbsp;&nbsp;
          <a href="?section=88&user='.$_REQUEST['user'].'&allinfo">Kompletní výpis</a>&nbsp;&nbsp;
          <a href="?section=88&user='.$_REQUEST['user'].'&allinfo&balance">Zobrazit zůstatky / Kompletní výpis</a>
        ';
      }
    }

    $this->vrat .= '
      <table class="unitable">
				<tr>
          <th>Datum</th>
          <th>Druh</th>
          <th>Informace</th>
          <th>Saldo</th>
        </tr>
    ';

    $saldo = 0;
    ksort($user_action);
    // $user_action = array_reverse($user_action,true);

    foreach($user_action as $k=>$h){ //$k cas
      foreach($h as $k2=>$h2){ //$k2 druh
        $dAction = true;

        foreach($deleteAkce as $h){
          if($k>=$h[0] && $k<=$h[1]) $dAction = false;
        }

        if($k2 == 'sazky'){
          foreach($h2 as $k3=>$h3){
            if($h3['system'] != 0){
              $system_text = '';
              if($ticket[$h3['ticket_id']]['banker_num']>0){
                $system_text .= $ticket[$h3['ticket_id']]['banker_num'].' Banker + ';
                $system_text .= 'Systém '.$h3['system'].'/'.count($system_ar[$h3['ticket_id']]);
              }
            }

            if($h3['vyplacen']==1){
              if($h3['system'] != 0){
                $system_win_real = ($h3['system_win']-$h3['castka']);
                if($system_win_real < 0 && $h3['free_bet_bonus'] == 1)
                  $system_win_real = 0;
                if($system_win_real > 0 || !isset($_GET['allinfo'])){
                  if($dAction)
                    $saldo += $system_win_real;
                }

                if(isset($_GET['allinfo']) && $h3['free_bet_bonus'] != 1 && $system_win_real > 0){
                  if($dAction)
                    $saldo += $h3['castka'];
                }

                if(isset($_GET['allinfo']) && $h3['free_bet_bonus'] != 1 && $system_win_real < 0){
                  if($dAction)
                    $saldo += $h3['castka']+$system_win_real;
                }

              }
              else{

                if(!isset($_GET['allinfo'])){
                  if($dAction){
                    $saldo += ($h3['stav'] == false?($h3['free_bet_bonus']==1?0:-$h3['castka']):(round(($h3['castka']*$h3['kurz']),2)-$h3['castka']));
                  }
                }
                else{
                  if($dAction){
                    $saldo += ($h3['stav'] == false?($h3['free_bet_bonus']==1?0:0):(round(($h3['castka']*$h3['kurz']),2)));
                  }
                  if($h3['free_bet_bonus']==1 && $h3['stav'] == true){
                    if($dAction)
                      $saldo-=$h3['castka'];
                  }
                }
              }
            }

            else{
              if($dAction)
                $saldo -= ($h3['free_bet_bonus']==1?0:$h3['castka']);
            }


            $stav = ($h3['vyplacen'] && $h3['zruseno'] == 0?"Vyplacen":($h3['vyplacen'] && $h3['zruseno'] == 1?"Zrušeno vyplacen v kurzu 1":"Podal tiket"));

            if($h3['system'] != 0)
              $this->vrat .= '<tr><td class="textcenter">'.date("d.m.Y H:i:s",$k).' - '.round($h3['system_win'],2).' '.(!$dAction?"<strong style=\"color:red\">CANCELED </strong>":"").'</td><td class="textcenter">Sázky</td><td><table><tr><td style="width:180px;border:0px;">Tiket ID: <a href="/?'.($this->nomenu==false?'section=90':'section=b15').'&ticket_id_search='.$h3['ticket_id'].'" target="_blank">'.$h3['ticket_id'].'</a> ('.($stav == 'Podal tiket' || !isset($_GET['allinfo'])?Help::EnTicket($h3['ticket_id'],date("Y-m-d H:i:s",$k)):'' ).'); '.$system_text.'; '.($h3['free_bet_bonus']==1?'Byl použit Free Bet Bonus':'').';</td><td style="width:110px;border:0px;"> Vsazeno:'.$h3['castka'].' '.$mena.';</td><td style="width:240px;border:0px;"> Stav:'.$stav.' '.($h3['vyplacen']==1?($system_win_real<=0?"; Prohra ".round($system_win_real,2)." ".$mena:"; Výhra čistá ".round($system_win_real,2)." ".$mena):"").' '.$h3['vraceny'].';</td></tr></table></td><td class="textcenter">'.round($saldo,2)." ".$mena.'</td></tr>';
            else
              $this->vrat .= '<tr><td class="textcenter">'.date("d.m.Y H:i:s",$k).' '.(!$dAction?"<strong style=\"color:red\">CANCELED </strong>":"").'</td><td class="textcenter">Sázky</td><td><table><tr><td style="width:180px;border:0px;">Tiket ID: <a href="/?'.($this->nomenu==false?'section=90':'section=b15').'&ticket_id_search='.$h3['ticket_id'].'" target="_blank">'.$h3['ticket_id'].'</a> ('.($stav == 'Podal tiket' || !isset($_GET['allinfo'])?Help::EnTicket($h3['ticket_id'],date("Y-m-d H:i:s",$k)):'' ).') '.($h3['free_bet_bonus']==1?'Byl použit Free Bet Bonus':'').';</td><td style="width:110px;border:0px;"> Vsazeno:'.$h3['castka'].' '.$mena.';</td><td style="width:240px;border:0px;"> Stav:'.$stav.' '.($h3['vyplacen']==1?($h3['stav'] == false?"; Prohra -".($h3['free_bet_bonus']==1?0:$h3['castka'])." ".$mena:"; Výhra čistá ".(round(($h3['castka']*$h3['kurz']),2)-$h3['castka'])." ".$mena):"").' '.$h3['vraceny'].';</td></tr></table></td><td class="textcenter">'.round($saldo,2)." ".$mena.'</td></tr>';
          }
        }

        else if($k2 == 'finance'){
          if($h2['status'] == "Vklad"){
            if($dAction)
              $saldo = $saldo + ($h2['castka'] - $h2['poplatky'])+$h2['dluh'] ;
          }
          else{
            if($dAction)
              $saldo = $saldo - ($h2['castka'] + $h2['poplatky']);
          }
          $this->vrat .= '
            <tr>
              <td class="textcenter" style="'. ($h2['status'] == "Vklad"?'background:green':'background:red') .'" >
                '.date("d.m.Y H:i:s",$k).'
                '.(!$dAction?"<strong style=\"color:red\">CANCELED </strong>":"").'
              </td>
              <td class="textcenter">Vklady/Výběry</td>
              <td>
                <table>
                  <tr>
                    <td style="width:80px;border:0px;">
                      Akce: '.$h2['status'].';
                    </td>
                    <td style="width:210px;border:0px;">
                      Metoda:'.$h2['metoda'].' '.(isset($h2['cardNo'])?'('.$h2['cardNo'].')':'').';
                    </td>
                    <td style="width:110px;border:0px;">
                      Částka:'.round($h2['castka'],2).' '.$mena.';
                    </td>
                    <td style="width:110px;border:0px;">
                      Poplatky:'.round($h2['poplatky'],2).' '.$mena.' '. ($h2['dluh']>0?'(Fee for debt!)':'') .'
                    </td>
                    <td style="width:110px;border:0px;">
                      Příznak:'.$h2['priznak'].'
                    </td>
                  </tr><
                /table>
              </td>
              <td class="textcenter">'.round($saldo,2)." ".$mena.'</td>
            </tr>
          ';
        }

        else if($k2 == 'balance'){
          $this->vrat .= '
            <tr style="background:red">
              <td class="textcenter" style="'. (intval($h2['castka']) != intval($saldo)?'background:yellow;color:black':'') .'" >
                '.date("d.m.Y H:i:s",$k).'
              </td>
              <td class="textcenter">
                BALANCE
              </td>
              <td>
                Zůstatek: '.$h2['castka'].'; Žetony: '.$h2['zetony'].' ; Dluh: '.$h2['dluh'].'
              </td>
              <td class="textcenter">
                '.$h2['castka'].' / '.$h2['zetony'].'
              </td>
            </tr>
          ';
        }

        else if($k2 == 'bonus'){
          if($dAction && $h2['deposit_type'] == 1)
            $saldo = $saldo + $h2['win'];

          $this->vrat .= '
            <tr>
              <td class="textcenter" style="background:purple">
                '.date("d.m.Y H:i:s",$k).'
                '.(!$dAction?"<strong style=\"color:red\">CANCELED </strong>":"").'
              </td>
              <td class="textcenter">
                Bonus
              </td>
              <td>
                <table>
                  <tr>
                    <td style="width:200px;border:0px;">
                      Typ: '.$h2['type'].';
                    </td>
                    <td style="width:110px;border:0px;">
                      Částka: '.round($h2['win'],2).' '.$mena.';
                    </td>
                  </tr>
                </table>
              </td>
              <td class="textcenter">
                '.round($saldo,2)." ".$mena.'
              </td>
            </tr>
          ';
        }
      }

      $this->user_balance[$_REQUEST['user']]['saldo'] = $saldo;
    }

    $this->vrat .= '</table>';
    $this->vrat .= '<br /><br />';
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
