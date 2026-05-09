<?php
/**
 * @package    statistics
 */


 /**
 * Trida pro praci se statistikami vseho
 *
 *
 * @package    main
 */

class GlobalStat{


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

   if($db == null){

   $this->db = DB::connect(DATABASE ."://". MY_USER .":". MY_PASS ."@". MY_HOST ."/". MY_DB);
   if (DB::isError($this->db)) {
     throw new ExHandler($this->db->getMessage(),"admin_ex_db");
   }
   $this->db->setFetchMode(DB_FETCHMODE_ASSOC);
   $sql = "set names 'utf8'";
   $res =& $this->db->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");


   }else
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


   }else
        $this->dbGame =  $dbGame;



  }

   /**
 * Metoda spousti jednitlove metody podle stavu
 * @return void
 */
  public  function runAction(){

  	 $this->vrat .= '<style>table.globalstat{border-collapse:collapse;margin:8px 0px;}
                            table.globalstat td{border:1px solid black;padding:4px;font-size:0.7em}
                           table.globalstat th{border:1px solid black;padding:4px;background:#F1A600;font-size:0.7em;}
                          h3{width:300px}
                            body{width:100000px;}
                            #body{width:100000px;}
                          </style>';

     $this->Info();



    $this->dbGame->disconnect();

  }

     /**
 * Metoda vypise pozadovane sazky
 * @return void
 */
  public  function Info(){
  	#135,113,17741,115,114,1632#
  	$this->searchForm();

  	if( (isset($_REQUEST['rok']) && isset($_REQUEST['type']) ) || (isset($_REQUEST['odw']) && isset($_REQUEST['type']) && $_REQUEST['type']==3)){

  		$ob = new GlobalStatDay();
  		$ob->runAction();

  		$this->vrat .= '<div style="">';
  	    $this->vrat .=  $ob->getContent();
  		$this->vrat .= '</div>';
  		return;

  	}



  	if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od']))
      $this->vrat .= '<strong style="color:black;font-size:1.1em;">od '.$_REQUEST['od'].'</strong>';
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do']))
      $this->vrat .= '<strong style="color:black;font-size:1.1em;">do '.$_REQUEST['do'].'</strong>';

  	$this->vrat .= '<br /><br /><br />';

  	if(isset($_REQUEST['v1'])){

  		$this->vrat .= $this->BetSaldo();

  	}
  	if(isset($_REQUEST['v4'])){

  		$this->vrat .= $this->Vklady();

  	}
    if(isset($_REQUEST['v5'])){

  		$this->vrat .= $this->Vybery();

  	}
    if(isset($_REQUEST['v6'])){

  		$this->vrat .= $this->People();

  	}
    if(isset($_REQUEST['v7'])){

  		$this->vrat .= $this->Activity();

  	}
    if(isset($_REQUEST['v8'])){

  		$this->vrat .= $this->Udalost();

  	}



  }




     /**
 * Udalost
 * @return string
 */
  public  function Udalost(){

  	$vrat = '';
  	$preklad = new Preklady();

  	$sport = $udalost = $mena = $ticket = array();

  	$sql = "select e.kurz,e.mena_id from kurzmena e where e.platny_od<=now() and e.platny_do>=now() ";
    $res =& $this->dbGame->query($sql);
    while ($row =& $res->fetchRow()){
    	$mena[$row['mena_id']] = $row['kurz'];
    }



  	$sql = "select s.sport_id,c.udalost_id,c.nazev AS unazev,s.nazev AS snazev from  udalost c  inner join sport s on s.sport_id=c.sport_id where s.sport_id in(0".implode(",",$_REQUEST['sport']).") order by s.sport_id,c.pozice";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

    while ($row =& $res->fetchRow()){

    	$pr = $preklad->FindPreklad($row['unazev'],2);
    	$prs = $preklad->FindPreklad($row['snazev'],2);

    	$udalost[$row['udalost_id']]['name'] = $pr[2];
    	$udalost[$row['udalost_id']]['sum'] = 0;
    	$udalost[$row['udalost_id']]['sum_pay'] = 0;
    	$udalost[$row['udalost_id']]['num'] = 0;
    	$udalost[$row['udalost_id']]['sum_all'] = 0;
    	$udalost[$row['udalost_id']]['win_num'] = 0;
    	$udalost[$row['udalost_id']]['sum_win_ticket'] = 0;
    	$sport[$row['sport_id']]['name'] = $prs[2];
    	$sport[$row['sport_id']]['sum'] = 0;
    	$sport[$row['sport_id']]['sum_pay'] = 0;
    	$sport[$row['sport_id']]['num'] = 0;
    	$sport[$row['sport_id']]['sum_all'] = 0;
    	$sport[$row['sport_id']]['win_num'] = 0;
    	$sport[$row['sport_id']]['sum_win_ticket'] = 0;
    	$sport[$row['sport_id']]['udalost'][] = $row['udalost_id'];


    }

  	$where = '1 and';

    if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])){
      $od = It6_Date::toDb($_REQUEST['od']);
      $where .= " a.zalozen>'".$od."' and";
    }
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])){
      $do = It6_Date::toDb($_REQUEST['do']);
      $where .= " a.zalozen<'".$do."' and";
    }

    if(isset($_REQUEST['only_live'])) {$where .= " a.live=1 and";}

  	$where = substr($where,0,-3);

  	$sql = "select a.castka,a.vyplacen,a.win,a.kurz,a.sloupec_id,a.vysledek,a.castka,a.sazka_id,a.udalost_id,c.sport_id,a.ticket_id,b.mena_id from ticket_pohled a inner join udalost c on a.udalost_id=c.udalost_id inner join uzivatel b on a.user_id=b.user_id where b.e_testovaci = 'ne' and c.sport_id in(0".implode(",",$_REQUEST['sport']).") and ".$where;
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

    while ($row =& $res->fetchRow()){



    	if(!isset($ticket[$row['ticket_id']])) {
    	  $ticket[$row['ticket_id']]['vyherci_castka'] = false;

    	  $ticket[$row['ticket_id']]['num'] = 0;$ticket[$row['ticket_id']]['castka']=($row['castka']/$mena[$row['mena_id']]);
    	  if($row['vyplacen']==1) $ticket[$row['ticket_id']]['vyplacen'] = 1;else $ticket[$row['ticket_id']]['vyplacen'] = 0;

    	      $sql = "select castka from vyherci_sazky where ticket_id=".$row['ticket_id'];
    	 	  $res2 =& $this->dbGame->query($sql);
              if(DB::isError($res2)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber tiketu',"admin_ex_db");

              if ($row2 =& $res2->fetchRow()) {$ticket[$row['ticket_id']]['vyherci_castka'] = $row2['castka'];$ticket[$row['ticket_id']]['vyherni'] = 1;} else  $ticket[$row['ticket_id']]['vyherni'] = 0;

    	}

    	$vysledek = explode(";",$row['vysledek']);

    	if(in_array($row['sloupec_id'],$vysledek)){

    		$sport[$row['sport_id']]['win_num']++;
    		$udalost[$row['udalost_id']]['win_num']++;

    		$sql = "select kurz,sazka_id from ticket_pohled where ticket_id=".$row['ticket_id'];
    		$res2 =& $this->dbGame->query($sql);
            if(DB::isError($res2)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber vyherce sazky',"admin_ex_db");

            $t_pocet = $res2->numRows();
            $kurz_celkem = 0;
            while ($row2 =& $res2->fetchRow()){

            	$kurz_celkem += $row2['kurz'];

            }

            if($kurz_celkem!=0){
             $t_procento = ( ($row['kurz']/$kurz_celkem)* 100);

             $vypocet = ((($t_procento/100)*($row['win']-$row['castka'])) /$mena[$row['mena_id']] );

             $sport[$row['sport_id']]['sum_all']     += $vypocet;
             $udalost[$row['udalost_id']]['sum_all'] += $vypocet;

            }

            if($row['vyplacen']==1){


              if ($ticket[$row['ticket_id']]['vyherci_castka'] && $kurz_celkem!=0){
               $vypocet = ((($t_procento/100)*($ticket[$row['ticket_id']]['vyherci_castka']-$row['castka'])) /$mena[$row['mena_id']] );

               $sport[$row['sport_id']]['sum_win_ticket']    += $vypocet;
               $udalost[$row['udalost_id']]['sum_win_ticket']+= $vypocet;

               $ticket[$row['ticket_id']]['vyherni'] = 1;

              }

            }

        }

    	$ticket[$row['ticket_id']]['num']++;
    	$sport[$row['sport_id']]['num']++;
    	$udalost[$row['udalost_id']]['num']++;

    	$ticket[$row['ticket_id']]['sazka'][$row['sazka_id']]['udalost'] =  $row['udalost_id'];
    	$ticket[$row['ticket_id']]['sazka'][$row['sazka_id']]['sport'] =  $row['sport_id'];

    }

  	foreach($ticket as $h){

  		$podil = ($h['castka']/$h['num']);

  		foreach($h['sazka'] as $h2){

  			if($h['vyplacen'] == 1 && $h['vyherni']==0){
  			  $udalost[$h2['udalost']]['sum_pay'] += $podil;
  			  $sport[$h2['sport']]['sum_pay'] += $podil;
  			}
  			$udalost[$h2['udalost']]['sum'] += $podil;
  			$sport[$h2['sport']]['sum'] += $podil;

  		}


  	}

    $vrat .= '<h3>Události</h3>';


  	$vrat .= '<table class="unitable">';
  	$vrat .= '<tr><th> &nbsp;</th><th>Počet tiketů </th><th> Vsazeno </th><th> Správné tipy </th><th>Možná výhra maximum</th><th> Výhra </th><th> Zisk</th></tr>';

    foreach($sport as $k=>$h){

    	 $_vynosS = round($h['sum_pay']-$h['sum_win_ticket'],2);
    	 if($_vynosS < 0) $_vynosS = '<span style="color:red;background:white;">'.$_vynosS.'</span>';
    	 if($h['num']!=0)$vrat .= '<tr style="background:#7cb6a4;color:white"><td width="40%" > <strong>'.Help::Html($h['name']).'</strong> </td><td> '.$h['num'].' </td><td> '.round($h['sum'],2).' </td><td> '.$h['win_num'].' </td><td> '.round($h['sum_all'],2).' </td><td> '.round($h['sum_win_ticket'],2).' </td><td> '.$_vynosS.' </td></tr>';

    	 foreach($h['udalost'] as $h2){

    	 	$_vynosU = round($udalost[$h2]['sum_pay']-$udalost[$h2]['sum_win_ticket'],2);
    	    if($_vynosU < 0) $_vynosU = '<span style="color:red">'.$_vynosU.'</span>';
    	 	if($udalost[$h2]['num']!=0)  $vrat .= '<tr><td width="40%"> &nbsp;&nbsp;'.Help::Html($udalost[$h2]['name']).' </td><td> '.$udalost[$h2]['num'].' </td><td> '.round($udalost[$h2]['sum'],2).' </td><td> '.$udalost[$h2]['win_num'].' </td><td> '.round($udalost[$h2]['sum_all'],2).' </td><td> '.round($udalost[$h2]['sum_win_ticket'],2).' </td><td> '.$_vynosU.' </td></tr>';

    	 }

    }

    $vrat .= '</table>';

  	return $vrat;

  }

   /**
 * Aktivita
 * @return string
 */
  public  function Activity(){

  	$vrat = '';

  	$where = '1 and';
  	$preklad = new Preklady();

  	if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toTimestamp($_REQUEST['od']);$where .= " acctrans_t>".intval($od)." and";}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = It6_Date::toTimestamp($_REQUEST['do']);$where .= " acctrans_t<".intval($do)." and";}

  	$where = substr($where,0,-3);

  	$user = $user_dep = $deposit = $deposit_zeme = $witdep_zeme = $zeme = array();
  	$deposit[3] = 0;$deposit[2]=0;$deposit[1] = 0;
  	$witdep = 0;

  	#Vlozeni vice nez#
  	/*
    $sql = "select a.*,c.zeme_id,c.user_id from finacni_transakce a inner join uzivatel c on a.user_id=c.user_id where  c.e_testovaci = 'ne'   and ".$where." order by c.zeme_id";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

    while ($row =& $res->fetchRow()){

    	if(!isset($zeme[$row['zeme_id']])){

    		$sql = "select nazev from  zeme where zeme_id=".$row['zeme_id'];
            $res2 =& $this->dbGame->query($sql);
            if(DB::isError($res2)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
             if($row2 =& $res2->fetchRow())	{$pr = $preklad->FindPreklad($row2['nazev'],2);$zeme[$row['zeme_id']]=$pr[2];}

    	}
    	if(!isset($user[$row['user_id']])) {$user[$row['user_id']]['num']=0;$user[$row['user_id']]['zeme']=$row['zeme_id'];
    	$user_dep[$row['user_id']]['zeme']=$row['zeme_id'];}

    	if($row['typ_platby']==1){
    	 $user[$row['user_id']]['num']++;
    	 //TODO Ma to byt v gm nebo ne? Ted je. Jinak by se muselo dat , false
    	 $user_dep[$row['user_id']]['vklad'][] =  It6_Date::fromDbAsTimestamp($row['datum']);
    	}
    	else if($row['typ_platby']==2){

    	  if(!isset($user_dep[$row['user_id']]['vyber']) ||  $user_dep[$row['user_id']]['vyber']>It6_Date::fromDbAsTimestamp($row['datum']))$user_dep[$row['user_id']]['vyber'] = It6_Date::fromDbAsTimestamp($row['datum']);

    	}


    }
	*/
    foreach($user_dep as $k=>$h){

    	if(!isset($witdep_zeme[$h['zeme']])){
    	 $witdep_zeme[$h['zeme']]['name'] = $zeme[$h['zeme']];
    	 $witdep_zeme[$h['zeme']]['num'] = 0;
        }

        foreach($h['vklad'] as $h2){

        	if(isset($h['vyber']) && $h2>$h['vyber']){

        		$witdep++;
        		$witdep_zeme[$h['zeme']]['num']++;
        		break;
        	}

        }

    }

    foreach($user as $k=>$h){

    	if(!isset($deposit_zeme[$h['zeme']])){
    	 $deposit_zeme[$h['zeme']]['name']=$zeme[$h['zeme']];
    	 $deposit_zeme[$h['zeme']]['dep'][1] = 0;
    	 $deposit_zeme[$h['zeme']]['dep'][2] = 0;
    	 $deposit_zeme[$h['zeme']]['dep'][3] = 0;
        }

    	if($h['num']>=3){
    	   $deposit[3]++;
    	   $deposit_zeme[$h['zeme']]['dep'][3]++;
    	}
       	else if($h['num']>=2){
    	   $deposit[2]++;
    	   $deposit_zeme[$h['zeme']]['dep'][2]++;
    	}
    	else{
    	   $deposit[1]++;
    	   $deposit_zeme[$h['zeme']]['dep'][1]++;
    	}

    }

  	$vrat .= '<h3>Aktivity</h3>';

  	$vrat .= '<table class="unitable">';
  	$vrat .= '<tr><th colspan="2">Počet uživatelů kteří vybrali a zase vložili (come back)</th></tr>';

    $vrat .= '<tr><td width="40%"> Celkem </td><td> '.$witdep.' </td></tr>';

    $vrat .= '</table>';

    $vrat .= '<table class="unitable">';
  	$vrat .= '<tr><th colspan="2"> Počet uživatelů kteří vybrali a zase vložili - země</th></tr>';
    foreach($witdep_zeme as $k=>$h){

    	 $vrat .= '<tr><td width="40%" > '.Help::Html($h['name']).' </td><td > '.$h['num'].' </td></tr>';

    }

    $vrat .= '</table>';


  	$vrat .= '<table class="unitable">';
  	$vrat .= '<tr><th colspan="2">Počet uživatelů co vložili x krát</th></tr>';

    $vrat .= '<tr><td width="40%"> <em>1x</em> </td><td> '.$deposit[1].' </td></tr>';
    $vrat .= '<tr><td width="40%"> <em>2x</em> </td><td> '.$deposit[2].' </td></tr>';
    $vrat .= '<tr><td width="40%"> <em>3 a více</em> </td><td> '.$deposit[3].' </td></tr>';

    $vrat .= '</table>';


    $vrat .= '<table class="unitable">';
  	$vrat .= '<tr><th colspan="3"> Počet uživatelů co vložili x krát - zem2</th></tr>';
    foreach($deposit_zeme as $k=>$h){

    	 $vrat .= '<tr><td colspan="3"> <strong>'.Help::Html($h['name']).' </strong></td></tr>';
    	 $vrat .= '<tr><td > &nbsp; </td><td > 1x </td><td > '.$h['dep'][1].' </td></tr>';
    	 $vrat .= '<tr><td > &nbsp; </td><td > 2x </td><td > '.$h['dep'][2].' </td></tr>';
    	 $vrat .= '<tr><td > &nbsp; </td><td > 3 a více </td><td > '.$h['dep'][3].' </td></tr>';

    }

    $vrat .= '</table>';


    return $vrat;

  }

 /**
 * Zákazníci
 * @return string
 */
  public  function People(){

  	$where = '1 and';
  	$vrat = '';

  	$reg_zeme = $akt_zeme = $game_zeme = $bet_zeme = $top_bet = $top_lose = $top_win = array();
  	$reg_celkem = $akt_celkem = $game_celkem = $bet_celkem = 0;


  	$preklad = new Preklady();

  	#Registrace#
  	if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toDb($_REQUEST['od']);$where .= " a.datum_registrace>'".$od."' and";}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = It6_Date::toDb($_REQUEST['do']);$where .= " a.datum_registrace<'".$do."' and";}

  	$where = substr($where,0,-3);
  	$sql = "select a.*,b.nazev from uzivatel a inner join zeme b on a.zeme_id=b.zeme_id where a.e_testovaci = 'ne' and ".$where;
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

    while ($row =& $res->fetchRow()){

    	if(!isset($reg_zeme[$row['zeme_id']])){

    		$pr = $preklad->FindPreklad($row['nazev'],2);

    		$reg_zeme[$row['zeme_id']]['nazev'] = $pr[2];
    		$reg_zeme[$row['zeme_id']]['num'] = 0;

    	}

    	$reg_zeme[$row['zeme_id']]['num']++;
    	$reg_celkem++;

    }
    #End registrace#

    #Aktivni#
    $where = '1 and';

    if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toDb($_REQUEST['od']);$where .= " a.posledni_prihlaseni>'".$od."' and";}
  	//if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = It6_Date::toDb($_REQUEST['do']);$where .= " a.datum_registrace<".$do." and";}

  	$where = substr($where,0,-3);
  	$sql = "select a.*,b.nazev from uzivatel a inner join zeme b on a.zeme_id=b.zeme_id where a.e_testovaci = 'ne' and ".$where;
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

    while ($row =& $res->fetchRow()){

    	if(!isset($akt_zeme[$row['zeme_id']])){

    		$pr = $preklad->FindPreklad($row['nazev'],2);

    		$akt_zeme[$row['zeme_id']]['nazev'] = $pr[2];
    		$akt_zeme[$row['zeme_id']]['num'] = 0;

    	}

    	$akt_zeme[$row['zeme_id']]['num']++;
    	$akt_celkem++;

    }

    #End aktivni#




    #End aktivni#

    #Sazkari#

    $where = '1 and';
    if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toDb($_REQUEST['od']);$where .= " b.zalozen>'".$od."' and";}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = It6_Date::toDb($_REQUEST['do']);$where .= " b.zalozen<'".$do."' and";}

    $where = substr($where,0,-3);
  	$sql = "select a.*,c.nazev from uzivatel a inner join zeme c on a.zeme_id=c.zeme_id inner join ticket b on a.user_id=b.user_id where a.e_testovaci = 'ne' and ".$where." group by b.user_id";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");


    while ($row =& $res->fetchRow()){

    	if(!isset($bet_zeme[$row['zeme_id']])){

    		$pr = $preklad->FindPreklad($row['nazev'],2);

    		$bet_zeme[$row['zeme_id']]['nazev'] = $pr[2];
    		$bet_zeme[$row['zeme_id']]['num'] = 0;

    	}


    	$bet_zeme[$row['zeme_id']]['num']++;
    	$bet_celkem++;

    }

    $sql = "select b.user_id  from  ticket b where ".$where." ";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

    $user_bet_celkem = array();
    while ($row =& $res->fetchRow()){


    	if(!isset($user_bet_celkem[$row['user_id']])) $user_bet_celkem[$row['user_id']]=0;else $user_bet_celkem[$row['user_id']]++;


    }

    $bet1 = $bet3 = $bet5 = $bet10 = 0;

    foreach($user_bet_celkem as $bm){

    	if      ($bm >=10 )  $bet10++;
    	else if ($bm >= 5)   $bet5++;
    	else if ($bm >= 3)   $bet3++;
    	else                 $bet1++;

    }

    #End sazkari#

    #Top sazky info#

    $where = '1 and';
    if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toDb($_REQUEST['od']);$where .= " b.datum>'".$od."' and";}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = It6_Date::toDb($_REQUEST['do']);$where .= " b.datum<'".$do."' and";}

    $where = substr($where,0,-3);
  	$sql = "SELECT a.user_id,a.nick,a.jmeno,a.prijmeni,(b.castka/(SELECT g.kurz FROM kurz f INNER JOIN kurz_mena g ON f.id_kurz = g.id_kurz WHERE f.platny_od <= now( ) AND f.platny_do >= now( ) AND g.id_mena=a.mena_id)) AS ecastka
            FROM uzivatel a inner join vyherci_sazky b  on a.user_id=b.user_id where a.e_testovaci = 'ne' and ".$where." order by ecastka  desc limit 3  ";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

    while ($row =& $res->fetchRow()){

    	$klic = count($top_win);
    	$top_win[$klic]['jmeno'] = $row['jmeno'].' '.$rowp['prijmeni'].'('.$row['nick'].')';
    	$top_win[$klic]['win'] = round($row['ecastka'],2);

    }


    $where = '1 and';
    if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toDb($_REQUEST['od']);$where .= " b.zalozen>'".$od."' and";}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = It6_Date::toDb($_REQUEST['do']);$where .= " b.zalozen<'".$do."' and";}

    $where = substr($where,0,-3);
  	$sql = "SELECT a.user_id,a.nick,a.jmeno,a.prijmeni,(b.castka/(SELECT g.kurz FROM kurz f INNER JOIN kurz_mena g ON f.id_kurz = g.id_kurz WHERE f.platny_od <= now( ) AND f.platny_do >= now( ) AND g.id_mena=a.mena_id)) AS ecastka
            FROM uzivatel a inner join ticket b  on a.user_id=b.user_id where a.e_testovaci = 'ne' and ".$where." order by ecastka  desc limit 3  ";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

    while ($row =& $res->fetchRow()){

    	$klic = count($top_bet);
    	$top_bet[$klic]['jmeno'] = $row['jmeno'].' '.$rowp['prijmeni'].'('.$row['nick'].')';
    	$top_bet[$klic]['bet'] = round($row['ecastka'],2);

    }


    $where = '1 and';
    if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toDb($_REQUEST['od']);$where .= " d.datum>'".$od."' and";}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = It6_Date::toDb($_REQUEST['do']);$where .= " d.datum<'".$do."' and";}

    $where = substr($where,0,-3);

    $vyherci = array();
  	$sql = "select d.ticket_id from vyherci_sazky d where ".$where;
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

    while ($row =& $res->fetchRow()) $vyherci[]=$row['ticket_id'];

    $where = '1 and';
    if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toDb($_REQUEST['od']);$where .= " b.zalozen>'".$od."' and";}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = It6_Date::toDb($_REQUEST['do']);$where .= " b.zalozen<'".$do."' and";}


    $where = substr($where,0,-3);
  	$sql = "select a.user_id,a.nick,a.jmeno,a.prijmeni,(b.castka/(SELECT g.kurz FROM kurz f INNER JOIN kurz_mena g ON f.id_kurz = g.id_kurz WHERE f.platny_od <= now( ) AND f.platny_do >= now( ) AND g.id_mena=a.mena_id)) AS ecastka
    from uzivatel a inner join ticket b  on a.user_id=b.user_id where a.e_testovaci = 'ne' and b.vyplacen=1 and b.ticket_id not in (".implode(",",$vyherci).") and ".$where." order by ecastka  desc limit 3  ";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

    while ($row =& $res->fetchRow()){

    	$klic = count($top_lose);
    	$top_lose[$klic]['jmeno'] = $row['jmeno'].' '.$rowp['prijmeni'].'('.$row['nick'].')';
    	$top_lose[$klic]['bet'] = round($row['ecastka'],2);

    }

    #End top sazky info#

    $vrat .= '<h3>Customers</h3>';


  	$vrat .= '<table class="unitable">';
  	$vrat .= '<tr><th colspan="2"> Registrace</th></tr>';
    $vrat .= '<tr><td width="40%"> <strong>Celkem</strong> </td><td> '.$reg_celkem.' </td></tr>';
    foreach($reg_zeme as $k=>$h){

    	 $vrat .= '<tr><td width="40%"> '.Help::Html($h['nazev']).' </td><td> '.$h['num'].' </td></tr>';

    }

    $vrat .= '</table>';


    $vrat .= '<table class="unitable">';
  	$vrat .= '<tr><th colspan="2">Aktivní hráči - přihlášení</th></tr>';
    $vrat .= '<tr><td width="40%"> <strong>Celkem</strong> </td><td> '.$akt_celkem.' </td></tr>';
    foreach($akt_zeme as $k=>$h){

    	 $vrat .= '<tr><td width="40%"> '.Help::Html($h['nazev']).' </td><td> '.$h['num'].' </td></tr>';

    }

    $vrat .= '</table>';

    $vrat .= '<table class="unitable">';
  	$vrat .= '<tr><th colspan="2">Aktivní sázkaři</th></tr>';
    $vrat .= '<tr><td width="40%"> <strong>Celkem</strong> </td><td> '.$bet_celkem.' </td></tr>';
    foreach($bet_zeme as $k=>$h){

    	 $vrat .= '<tr><td width="40%"> '.Help::Html($h['nazev']).' </td><td> '.$h['num'].' </td></tr>';

    }

    $vrat .= '<table class="unitable">';
  	$vrat .= '<tr><th>Aktivní sázkaři</th><th>Abs.</th><th>Relace</th></tr>';
    $vrat .= '<tr><td width="40%"> <strong>Vsazeno 10 a více</strong> </td><td> '.$bet10.' </td><td> '.round((($bet10/$bet_celkem)*100),2).'% </td></tr>';
    $vrat .= '<tr><td width="40%"> <strong>Vsazeno 5 a více</strong> </td><td> '.$bet5.' </td><td> '.round((($bet5/$bet_celkem)*100),2).'% </td></tr>';
    $vrat .= '<tr><td width="40%"> <strong>Vsazeno 3 a více</strong> </td><td> '.$bet3.' </td><td> '.round((($bet3/$bet_celkem)*100),2).'% </td></tr>';
    $vrat .= '<tr><td width="40%"> <strong>Vsazeno 1 a více</strong> </td><td> '.$bet1.' </td><td> '.round((($bet1/$bet_celkem)*100),2).'% </td></tr>';

    $vrat .= '</table>';

   // $vrat .= '<table class="unitable">';
  	//$vrat .= '<tr><th colspan="2">Active players Casino/Games</th></tr>';
    //$vrat .= '<tr><td width="40%"> <strong>Total</strong> </td><td> '.$game_celkem.' </td></tr>';
    //foreach($game_zeme as $k=>$h){

    	// $vrat .= '<tr><td width="40%"> '.Help::Html($h['nazev']).' </td><td> '.$h['num'].' </td></tr>';

    //}



    $vrat .= '<table class="unitable">';
  	$vrat .= '<tr><th colspan="3">Top výherci</th></tr>';
    $vrat .= '<tr><th> &nbsp; </th><th>  Uživatel </th><th> Výhra  </th></tr>';
    foreach($top_win as $k=>$h){

    	 $vrat .= '<tr><td>'.($k+1).'</td><td width="40%"> '.Help::Html($h['jmeno']).' </td><td> '.$h['win'].' </td></tr>';

    }

    $vrat .= '</table>';

    $vrat .= '<table class="unitable">';
  	$vrat .= '<tr><th colspan="3">Top prohra</th></tr>';
    $vrat .= '<tr><th> &nbsp; </th><th>  Uživatel </th><th> Sázka   </th></tr>';
    foreach($top_lose as $k=>$h){

    	 $vrat .= '<tr><td>'.($k+1).'</td><td width="40%"> '.Help::Html($h['jmeno']).' </td><td> '.$h['bet'].' </td></tr>';

    }

    $vrat .= '</table>';




    $vrat .= '<table class="unitable">';
  	$vrat .= '<tr><th colspan="3">Top sázky najeden tiket</th></tr>';
    $vrat .= '<tr><th> &nbsp; </th><th>  Uživatel </th><th> Sázka EUR  </th></tr>';
    foreach($top_bet as $k=>$h){

    	 $vrat .= '<tr><td>'.($k+1).'</td><td width="40%"> '.Help::Html($h['jmeno']).' </td><td> '.$h['bet'].' </td></tr>';

    }

    $vrat .= '</table>';



    return $vrat;

  }

 /**
 * Vybery
 * @return string
 */
  public  function Vybery(){

  	$preklad = new Preklady();

    $mena = $vklady =  $met_abs = $met_mena_abs = $met_eur = $zeme_abs = $zeme_eur = $mena_abs = $mena_eur = array();

    $celkem_eur = 0;

  	$sql = "select e.kurz,e.mena_id from kurzmena e where e.platny_od<=now() and e.platny_do>=now() ";
    $res =& $this->dbGame->query($sql);
    while ($row =& $res->fetchRow()){
    	$mena[$row['mena_id']] = $row['kurz'];
    }

  	$vrat = '';

  	$where = '1 and';

  	if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$where .= ' a.datum>"'.It6_Date::toDb($_REQUEST['od']).'" and';}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$where .= ' a.datum<"'.It6_Date::toDb($_REQUEST['do']).'" and';}

  	$where = substr($where,0,-3);

  	/*
  	#Vybery#
    $sql = "select a.*,TRANSLATE(b.nazev,1) AS met_name,c.zeme_id from finacni_transakce a inner join platebni_metody b on a.metoda_id=b.metoda_id  inner join uzivatel c on a.user_id=c.user_id where c.e_testovaci = 'ne' and  a.typ_platby=2 and ".$where." order by c.zeme_id";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

    while ($row =& $res->fetchRow()){

    	$celkem_eur += ($row['castka']/$mena[$row['mena_id']]);

    	if(!isset($met_abs[$row['metoda_id']])){
    		//echo $row['accTrans_met'].'<br />';

    		$met_abs[$row['metoda_id']]['name'] = $row['met_name'];
    		$met_abs[$row['metoda_id']]['num'] = 0;
    		$met_eur[$row['metoda_id']]['num'] = 0;

    	}

    	$sql = "select mena_text from mena where mena_id=".$row['mena_id'];
        $res2 =& $this->dbGame->query($sql);
        if(DB::isError($res2)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
        if ($row2 =& $res2->fetchRow()) $mMame = $row2['mena_text'];

        if(!isset($met_mena_abs[$row['metoda_id']][$row['mena_id']])){

            $met_mena_abs[$row['metoda_id']][$row['mena_id']]['name'] = $mMame;
    		$met_mena_abs[$row['metoda_id']][$row['mena_id']]['num'] = 0;

    	}
		

    	if(!isset($zeme_abs[$row['zeme_id']])){

  	         $sql = "select TRANSLATE(nazev,1) AS nazev from zeme where zeme_id=".$row['zeme_id'];
             $res2 =& $this->dbGame->query($sql);
             if(DB::isError($res2)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
             if ($row2 =& $res2->fetchRow());

             $zeme_abs[$row['zeme_id']]['name'] = $row2['nazev'];
    		 $zeme_abs[$row['zeme_id']]['num'] = 0;
    		 $zeme_eur[$row['zeme_id']]['num'] = 0;

    	}

        if(!isset($mena_abs[$row['mena_id']])){

  	         $sql = "select mena_text from mena where mena_id=".$row['mena_id'];
             $res2 =& $this->dbGame->query($sql);
             if(DB::isError($res2)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
             if ($row2 =& $res2->fetchRow()) $mena_abs[$row['mena_id']]['name'] = $row2['mena_text'];;


    		 $mena_abs[$row['mena_id']]['num'] = 0;
    		 $mena_eur[$row['mena_id']]['num'] = 0;

    	}

    	$met_mena_abs[$row['metoda_id']][$row['mena_id']]['num'] += $row['castka'];

    	$met_abs[$row['metoda_id']]['num']++;
    	$met_eur[$row['metoda_id']]['num'] += ($row['castka']/$mena[$row['mena_id']]);

    	$zeme_abs[$row['zeme_id']]['num']++;
        $zeme_eur[$row['zeme_id']]['num'] += ($row['castka']/$mena[$row['mena_id']]);

        $mena_abs[$row['mena_id']]['num']++;
    	$mena_eur[$row['mena_id']]['num'] += ($row['castka']/$mena[$row['mena_id']]);

    }
    */

    $vrat .= '<h3>Výběry</h3>';

    $vrat .= '<table class="unitable"><tr><th> &nbsp; </th><th> Celkem abs. </th><th> Celkem </th></tr>';

     $vrat .= '<tr><td width="40%"> Celkem </td><td> '.$res->numRows().' </td><td> '.round($celkem_eur,2).' </td></tr>';

    $vrat .= '</table>';

    $vrat .= '<table class="unitable"><tr><th> Metoda  </th><th> Celkem abs. </th><th> Celkem </th></tr>';

    foreach($met_abs as $k=>$h){

    	 $vrat .= '<tr><td width="40%"> '.Help::Html($h['name']).' </td><td> '.$h['num'].' </td><td> '.round($met_eur[$k]['num'],2).' </td></tr>';

        foreach($met_mena_abs[$k] as $h2){


    	 	  $vrat .= '<tr><td width="40%"> &nbsp; </td><td> '.$h2['name'].' </td><td> '.$h2['num'].' </td></tr>';

    	 }

    }

    $vrat .= '</table>';

    $vrat .= '<table class="unitable"><tr><th>Země</th><th> Celkem abs. </th><th> Celkem </th></tr>';

    foreach($zeme_abs as $k=>$h){

    	 $vrat .= '<tr><td width="40%"> '.Help::Html($h['name']).' </td><td> '.$h['num'].' </td><td> '.round($zeme_eur[$k]['num'],2).' </td></tr>';

    }

    $vrat .= '</table>';


    $vrat .= '</table>';

    $vrat .= '<table class="globalstat"><tr><th> Měna </th><th> Celkem abs. </th><th> Celkem </th></tr>';

    foreach($mena_abs as $k=>$h){

    	 $vrat .= '<tr><td width="40%"> '.Help::Html($h['name']).' </td><td> '.$h['num'].' </td><td> '.round($mena_eur[$k]['num'],2).' </td></tr>';

    }

    $vrat .= '</table>';


  	return $vrat;

  }

    /**
 * Vklady
 * @return string
 */
  public  function Vklady(){

  	$preklad = new Preklady();

    $mena = $vklady =  $met_abs = $met_mena_abs = $met_eur = $zeme_abs = $zeme_eur = $mena_abs = $mena_eur = $user = array();

    $celkem_eur = $celkem_eur_reg = $num_user = $num_deposit_reg = 0;

  	$sql = "select e.kurz,e.mena_id from kurzmena e where e.platny_od<=now() and e.platny_do>=now() ";
    $res =& $this->dbGame->query($sql);
    while ($row =& $res->fetchRow()){
    	$mena[$row['mena_id']] = $row['kurz'];
    }

  	$vrat = '';

  	$where = '1 and';

  	if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toDb($_REQUEST['od']);$where .= " datum_registrace>'".$od."' and";}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = It6_Date::toDb($_REQUEST['do']);$where .= " datum_registrace<'".$do."' and";}

  	$where = substr($where,0,-3);

  	#Pocet uzivatelu#
    $sql = "select user_id from uzivatel where e_testovaci = 'ne' and ".$where;
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
    while ($row =& $res->fetchRow()){$user[]=$row['user_id'];}
    $num_user = $res->numRows();

  	$where = '1 and';

  	if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$where .= ' a.datum>"'.It6_Date::toDb($_REQUEST['od']).'" and';}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$where .= ' a.datum<"'.It6_Date::toDb($_POST['do']).'" and';}

  	$where = substr($where,0,-3);

  	#Vklady#
    //$sql = "select a.*,b.met_name,c.zeme_id,c.user_id from accTrans a inner join method b on a.acctrans_met=b.met_namex  inner join uzivatel c on a.acctrans_USR=c.user_id where c.e_testovaci = 'ne' and acctrans_ST<>3 and b.met_sign=1 and c.user_id not in(".PAY_NOTUSR.") and a.acctrans_sign=1 and ".$where." order by c.zeme_id";
    /*$sql = "select a.*,TRANSLATE(b.nazev,1) AS met_name,c.zeme_id from finacni_transakce a inner join platebni_metody b on a.metoda_id=b.metoda_id  inner join uzivatel c on a.user_id=c.user_id where c.e_testovaci = 'ne' and  a.typ_platby=1 and ".$where." order by c.zeme_id";

    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");

    while ($row =& $res->fetchRow()){

    	$celkem_eur += ($row['castka']/$mena[$row['mena_id']]);
    	if(in_array($row['user_id'],$user)) {$num_deposit_reg++;$celkem_eur_reg += ($row['castka']/$mena[$row['mena_id']]);}

    	$sql = "select mena_text from mena where mena_id=".$row['mena_id'];
        $res2 =& $this->dbGame->query($sql);
        if(DB::isError($res2)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
        if ($row2 =& $res2->fetchRow()) $mMame = $row2['mena_text'];


    	if(!isset($met_mena_abs[$row['metoda_id']][$row['mena_id']]) ){

            $met_mena_abs[$row['metoda_id']][$row['mena_id']]['name'] = $mMame;
    		$met_mena_abs[$row['metoda_id']][$row['mena_id']]['num'] = 0;

    	}


    	if(!isset($met_abs[$row['metoda_id']]) ){

    		   $met_abs[$row['metoda_id']]['name'] = $row['met_name'];
    		   $met_abs[$row['metoda_id']]['num'] = 0;
    		   $met_eur[$row['metoda_id']]['num'] = 0;



    	}




    	if(!isset($zeme_abs[$row['zeme_id']])){

  	         $sql = "select TRANSLATE(nazev,1) AS nazev from zeme where zeme_id=".$row['zeme_id'];
             $res2 =& $this->dbGame->query($sql);
             if(DB::isError($res2)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
             if ($row2 =& $res2->fetchRow()) ;

             $zeme_abs[$row['zeme_id']]['name'] = $row2['nazev'];
    		 $zeme_abs[$row['zeme_id']]['num'] = 0;
    		 $zeme_eur[$row['zeme_id']]['num'] = 0;

    	}

        if(!isset($mena_abs[$row['mena_id']])){

  	         $sql = "select mena_text from mena where mena_id=".$row['mena_id'];
             $res2 =& $this->dbGame->query($sql);
             if(DB::isError($res2)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
             if ($row2 =& $res2->fetchRow()) $mena_abs[$row['mena_id']]['name'] = $row2['mena_text'];


    		 $mena_abs[$row['mena_id']]['num'] = 0;
    		 $mena_eur[$row['mena_id']]['num'] = 0;

    	}



    	       $met_abs[$row['metoda_id']]['num']++;
    	       $met_eur[$row['metoda_id']]['num'] += ($row['castka']/$mena[$row['mena_id']]);
    	       $met_mena_abs[$row['metoda_id']][$row['mena_id']]['num'] += $row['castka'];





    	$zeme_abs[$row['zeme_id']]['num']++;
        $zeme_eur[$row['zeme_id']]['num'] += ($row['castka']/$mena[$row['mena_id']]);

        $mena_abs[$row['mena_id']]['num']++;
    	$mena_eur[$row['mena_id']]['num'] += ($row['castka']/$mena[$row['mena_id']]);

    }*/





    $vrat .= '<h3>Deposit</h3>';

    $vrat .= '<table class="unitable"><tr><th> Pouze registrovaní </th><th> Celkem abs. </th><th> Total in EUR </th></tr>';

    $vrat .= '<tr><td width="40%"> Total </td><td> '.$num_deposit_reg.' </td><td> '.round($celkem_eur_reg,2).' </td></tr>';
    $vrat .= '<tr><td colspan="2"> Vklad jeden uživatel </td><td> '.round(($celkem_eur_reg/$num_user),2).' </td></tr>';

    $vrat .= '</table>';

    $vrat .= '<table class="unitable"><tr><th> &nbsp; </th><th> Total abs. </th><th> Total in EUR </th><th> </th></tr>';

    $vrat .= '<tr><td width="40%"> Celkem </td><td> '.$res->numRows().' </td><td> '.round($celkem_eur,2).' </td><td> </td></tr>';
    $vrat .= '<tr><td colspan="3"> Vklad jeden uživatel v daném čase </td><td> '.round(($celkem_eur/$num_user),2).' </td></tr>';

    $vrat .= '</table>';

    $vrat .= '<table class="unitable"><tr><th> Metoda </th><th> Celkem abs. </th><th> Celkem </th></tr>';

    foreach($met_abs as $k=>$h){

    	 $vrat .= '<tr><td width="40%"> '.Help::Html($h['name']).' </td><td> '.$h['num'].' </td><td> '.round($met_eur[$k]['num'],2).' </td></tr>';

    	 foreach($met_mena_abs[$k] as $h2){


    	 	  $vrat .= '<tr><td width="40%"> &nbsp; </td><td> '.$h2['name'].' </td><td> '.$h2['num'].' </td></tr>';

    	 }
    }

    $vrat .= '</table>';

    $vrat .= '<table class="globalstat"><tr><th>Country </th><th> Total abs. </th><th> Total in EUR </th></tr>';

    foreach($zeme_abs as $k=>$h){

    	 $vrat .= '<tr><td width="40%"> '.Help::Html($h['name']).' </td><td> '.$h['num'].' </td><td> '.round($zeme_eur[$k]['num'],2).' </td></tr>';

    }

    $vrat .= '</table>';


    $vrat .= '<table class="unitable"><tr><th> Měna </th><th> Celkem abs. </th><th> Total in EUR </th></tr>';

    foreach($mena_abs as $k=>$h){

    	 $vrat .= '<tr><td width="40%"> '.Help::Html($h['name']).' </td><td> '.$h['num'].' </td><td> '.round($mena_eur[$k]['num'],2).' </td></tr>';

    }

    $vrat .= '</table>';


  	return $vrat;

  }



  /**
 * Saldo sazek
 * @return string
 */
  public  function BetSaldo(){

  	$_POST['od'] = $_REQUEST['od'];
  	$_POST['do'] = $_REQUEST['do'];

  	$vrat = '';

  	$ob = new StatistikySazky();
  	$ob->TicketInfo();


  	$vrat .= '<h3>Betting</h3>';
  	$vrat .= '<table class="unitable">
            <tr><th>Počet tiketů</th><th>Free tikety</th><th>Celkem vsazeno (s free ticket)</th><th>Vyplaceno</th><th>Výhra</th><th>čistá výjra</th><th>Výhernost</th></tr>
           <tr><td>'.$ob->infoGlobal[0].'</td><td>'.$ob->infoGlobal[6].'</td><td>'.$ob->infoGlobal[1].' EUR</td><td>'.$ob->infoGlobal[2].' EUR</td><td>'.$ob->infoGlobal[3].' EUR</td><td>'.$ob->infoGlobal[4].' EUR</td><td>'.$ob->infoGlobal[5].' %</td></tr>
           </table>';

  	return $vrat;

  }

   /**
 * Vyhledavaci formular
 * @return void
 */
  public  function searchForm(){

   $preklad = new Preklady();
   $sql = "select sport_id,nazev from sport";
   $res =& $this->dbGame->query($sql);
   $sport = '';
   while ($row =& $res->fetchRow()){
    	$pr = $preklad->FindPreklad($row['nazev'],2);
    	$sport .= '<option value="'.$row['sport_id'].'" '.(isset($_POST['sport']) && $_POST['sport']==$row['sport_id']?'selected="selected"':'').'>'.$pr[2].'</option>';
    }

    $this->vrat .= '
      <a href="javascript:($(\'#specform\').css(\'display\')==\'block\'?$(\'#specform\').css(\'display\',\'none\'):$(\'#specform\').css(\'display\',\'block\'));void(0);">
        Zobrazit/Skrýt
      </a>

      <form method="post" id="specform" style="display:block" class="noprint" action="?section='.$this->section.'">
        <table class="filtr">
          <tr>
            <td class="head"  colspan="4">Datum</td>
          </tr>

          <tr>
            <td>
              <input
                type="text"
                class="input dateTime"
                name="od"
                id="od"
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
                id="do"
                value="'.(isset($_REQUEST['do'])?Help::Html($_REQUEST['do']):"").'"
              />
              <img src="_clip/calendar.gif" class="calendar-icon">
            </td>
          </tr>

          <tr>
            <td class="head" colspan="4">Sestavy</td>
          </tr>

          <tr>
            <td>Betting</td>
            <td>
              <input type="checkbox" class="no" name="v1" />
            </td>
            <td>Customers</td>
            <td>
              <input type="checkbox" class="no"  name="v6" />
            </td>
          </tr>

          <tr>
            <td>Deposit</td>
            <td>
              <input type="checkbox" class="no"  name="v4" />
            </td>
            <td>Withdraw</td>
            <td>
              <input type="checkbox" class="no"  name="v5" />
            </td>
          </tr>

          <tr>
            <td>Activity (funguje pouze od - do)</td>
            <td>
              <input type="checkbox" class="no"  name="v7" />
            </td>
            <td>Sports/Events</td>
            <td valign="top">
              <input type="checkbox" class="no"  name="v8" />
              <select name="sport[]" multiple="multiple">
                '.$sport.'
              </select>
              Live
              <input type="checkbox" class="no" name="only_live" />
            </td>
          </tr>

          <tr>
            <td colspan="4">
              <input type="submit" name="filtr" class="inputs" value="Odeslat" />
            </td>
          </tr>
        </table>
      </form>
    ';
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
