<?php
/**
 * @package    statistics
 */


 /**
 * Trida pro praci se statistikami vyber po dnech a mesicich
 *
 *
 * @package    main
 */
 
class GlobalStatDay{


/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */              
private  $dbGame;

/**
 * pocet dnu v jednotlivych mesicich
 * @access private
 * @var array
 */              
private  $month=array(1=>31,2=>28,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);

/**
 * nazvy mesicich
 * @access private
 * @var array
 */              
private  $monthName=array(1=>'Leden',2=>'Únor',3=>'Březen',4=>'Duben',5=>'Květen',6=>'Červen',7=>'Červenec',8=>'Srpen',9=>'Září',10=>'�?íjen',11=>'Listopad',12=>'Prosinec');

/**
 * Intervaly ktere se maji zobrazit
 * @access private
 * @var array
 */              
private  $interval=array();


/**
 * spojeni na databazi admin
 * @access private
 * @var DB
 */              
private  $db;

/**
 * navratova hodnota
 * @access private
 * @var string
 */              
private  $vrat ='';

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
     
     $this->Info();
 
     $this->dbGame->disconnect();
	
  }
  
     /**
 * Metoda vypise pozadovane sazky
 * @return void
 */
  public  function Info(){
  	
   	if(isset($_REQUEST['v1'])){
  		
  		$this->BetSaldo();
  		$this->vrat .= '<br />';
  	}
  	
    if(isset($_REQUEST['v4'])){
  		
  		$this->Vklady();
  		$this->vrat .= '<br />';
  	}
    if(isset($_REQUEST['v5'])){
  		
  		$this->Vybery();
  		$this->vrat .= '<br />';
  	}
    if(isset($_REQUEST['v6'])){
  		
  		$this->People();
  		$this->vrat .= '<br />';
  	}

    if(isset($_REQUEST['v8'])){
  		
  		$this->Udalost();
  		$this->vrat .= '<br />';
  	}
    if(isset($_REQUEST['v9'])){
  		
  		$this->Referer();
  		$this->vrat .= '<br />';
  	}

  	
  }
  
  
 /**
 * Vytvori hlavicku tabulky
 * @param int $col pocet kolonek v nejlejsim sloupci
 * @param int $col2 pocet kolonek v jednotlivych datech
 * @param string $head nadpisy lsoupecku
 * @return string
 */
  public  function TopTable($col=0,$col2=1,array $head){
  	
  	$vrat = '';
  	$month = array();
  	
  	$rok_span = 0;
  	
  	if(!isset($_REQUEST['mesic']) && $_REQUEST['type']==2){
  		
  		for($x=1;$x<=12;$x++){
  			
  			$rok_span += $this->month[$x];
  			
  		}
  		
  	}
  	else if(!isset($_REQUEST['mesic']) && $_REQUEST['type']==1){
  	  $rok_span = 12;
  	}
  	else if(isset($_REQUEST['mesic']) && $_REQUEST['type']==2){
  		
  		foreach($_REQUEST['mesic'] as $h){
  			
  			$rok_span += $this->month[$h];
  			
  		}
  		
  	}
  	else if(isset($_REQUEST['mesic']) && $_REQUEST['type']==1){
  		
  		$rok_span = count($_REQUEST['mesic']);
  		
  	}
  	#Tydny#
    else if(isset($_REQUEST['odw']) && isset($_REQUEST['pocetw']) &&  $_REQUEST['type']==3){
  		
    	$this->interval = array();
    	
    	$vrat .= '<table class="globalstat">';
    	
    	$timestamp = It6_Date::toTimestamp($_REQUEST['odw']);
    	
    	$vrat .= '<tr>';
        for($x=0;$x<$col;$x++){$vrat .= '<th>&nbsp</th>';}
    
    	for($x=0;$x<intval($_REQUEST['pocetw']);$x++){
    		
    		$end_timestamp = ($timestamp+604800);
    		
    		$klic = count($this->interval);
  			$this->interval[$klic][1]= date("d.m.Y H:i:s",$timestamp);
  		    $this->interval[$klic][2]= date("d.m.Y H:i:s",$end_timestamp);

  		    $vrat .= '<th colspan="'.$col2.'">'.date("d.m.Y H:i:s",$timestamp).' - '.date("d.m.Y H:i:s",$end_timestamp).'</th>'; 
  		    
  		    
    		$timestamp = $end_timestamp; 
    		
    		
    		
    	}
    	$vrat .= '</tr>';
    	
    	if(count($head)>0){
        $vrat .= '<tr>';
        for($x=0;$x<$col;$x++){$vrat .= '<th>&nbsp</th>';}
        for($x=0;$x<intval($_REQUEST['pocetw']);$x++){
        	
        	foreach($head as $h){
              $vrat .= '<th>'.Help::Html($h).'</th>';
        	}
        	
        }
        $vrat .= '</tr>';
    	}
    	
  		return $vrat;
  		
  	}
  	#End Tydny#

  	$rok_span  = $rok_span * $col2;
  	
  	$vrat .= '<table class="globalstat">';
  	
  	
  	
  	#ROK#
  	$vrat .= '<tr>';
  	
  	for($x=0;$x<$col;$x++){$vrat .= '<th>&nbsp</th>';}
  	
  	foreach($_REQUEST['rok'] as $h){$vrat .= '<th colspan="'.$rok_span.'">'.Help::Html($h).'</th>';}
   	
  	$vrat .= '</tr>';
  	#END ROK#
    
  	
  	#MESIC#
    $vrat .= '<tr>';
  	
    for($x=0;$x<$col;$x++){$vrat .= '<th>&nbsp</th>';}
    foreach($_REQUEST['rok'] as $xx){
  	
  	
  	
  	if($_REQUEST['type']==2){ 
  	  if(isset($_REQUEST['mesic'])){foreach($_REQUEST['mesic'] as $k=>$h){$month[$h]=1;$vrat .= '<th colspan="'.($this->month[$h]*$col2).'">'.Help::Html($this->monthName[$h]).'</th>';}}
   	  else {foreach($this->monthName as $k=>$h){$month[$k]=1;$vrat .= '<th colspan="'.($this->month[$k]*$col2).'">'.Help::Html($h).'</th>';}}
  	}else{
  		
  	  if(isset($_REQUEST['mesic'])){foreach($_REQUEST['mesic'] as $k=>$h){$month[$h]=1;$vrat .= '<th colspan="'.($col2).'">'.Help::Html($this->monthName[$h]).'</th>';}}
   	  else {foreach($this->monthName as $k=>$h){$month[$k]=1;$vrat .= '<th colspan="'.($col2).'">'.Help::Html($h).'</th>';}}
  		
  	}
  	
  	}
  	$vrat .= '</tr>';
  	#END MESIC#
     
  	if(count($this->interval) > 0) $interval_status = false;else $interval_status  = true;
  	
  	if($_REQUEST['type']==2){
  	
  	  #DEN#
  	  $vrat .= '<tr>';
  	  for($x=0;$x<$col;$x++){$vrat .= '<th>&nbsp</th>';}
  	  foreach($_REQUEST['rok'] as $xx){
  	  
  	  
  	
  	  if(isset($_REQUEST['mesic'])){
  	    foreach($_REQUEST['mesic'] as $k=>$h){
  	      
  	    	for($x=1;$x<=$this->month[$h];$x++)
  	    	    $vrat .= '<th colspan="'.($col2).'">'.$x.'</th>';
  	    
  	    }
  	  }else{ 
   	    foreach($this->monthName as $k=>$h){
   	      for($x=1;$x<=$this->month[$k];$x++)
  	    	    $vrat .= '<th colspan="'.($col2).'">'.$x.'</th>';
   	    }
  	  }
   	    
  	  }
  	  $vrat .= '</tr>';
  	  #END DEN#
      
  	  if(count($head)>0) {$vrat .= '<tr>';for($x=0;$x<$col;$x++){$vrat .= '<th>&nbsp</th>';}}
  	  foreach($_REQUEST['rok'] as $h){
  		    
  			foreach($month as $h2=>$gh){

  			 for($x=1;$x<=$this->month[$h2];$x++){
  			   
  			   if($interval_status){
  			     $klic = count($this->interval);
  			     $this->interval[$klic][1]= $x.'.'.$h2.'.'.$h.' 00:00:00';
  		         $this->interval[$klic][2]= $x.'.'.$h2.'.'.$h.' 23:59:59';
  			   }
  		       if(count($head)>0) for($xy=0;$xy<$col2;$xy++) $vrat .= '<th>'.Help::Html($head[$xy]).'</th>';
  		       
  			 }
  			 
  			}
  			
  	  }
  	 if(count($head)>0) $vrat .= '</tr>';
  	
  	}else{
  		
  	    if(count($head)>0) {$vrat .= '<tr>';for($x=0;$x<$col;$x++){$vrat .= '<th>&nbsp</th>';}}
  	
  		foreach($_REQUEST['rok'] as $h){
  		    
  			foreach($month as $h2=>$gh){
  			 
  			 if($interval_status){
  			   $klic = count($this->interval);
  			   $this->interval[$klic][1]= '1.'.$h2.'.'.$h.' 00:00:00';
  		       $this->interval[$klic][2]= $this->month[$h2].'.'.$h2.'.'.$h.' 23:59:59';
  			 }
  				
  		     if(count($head)>0) for($xy=0;$xy<$col2;$xy++) $vrat .= '<th>'.Help::Html($head[$xy]).'</th>';
  		     
  			}
  			
  		}
  		
  		if(count($head)>0) $vrat .= '</tr>';
  		
  	}
  	
  	
  	
    	
  	return $vrat;
  	
  }
  
  
  
  
/**
 * Referer info
 * @return string
 */
  public  function Referer(){
  	
  	$mena = $pole = array();
  	
    $sql = "select e.kurz,e.mena_id from game.kurzmena e where e.platny_od<=now() and e.platny_do>=now() ";
    $res =& $this->dbGame->query($sql);
    while ($row =& $res->fetchRow()){
    	$mena[$row['mena_id']] = $row['kurz'];
    }
    
        
    $this->vrat .= '<h3>Referer</h3>';
  	
  	$this->vrat .= $this->topTable(1,6,array('Počet login','Počet reg','Vklad abs.','Vklad EUR ','Tiket abs.','Tiket EUR'));
  	
  	$xx = 0;
  	
  	foreach($this->interval as $k3=>$h3){
  	
  		
  	$xx++;
  	
  	$_REQUEST['od'] = $h3[1];
  	$_REQUEST['do'] = $h3[2];
  		
  		
    $where = '1 and';
  	
    if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toDb($_REQUEST['od']);$where .= " r.date>'".$od."' and";}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = It6_Date::toDb($_REQUEST['do']);$where .= " r.date<'".$do."' and";}
  	
  	$where = substr($where,0,-3);
  	
  	$where2 = '1 and';
  	
  	if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toTimestamp($_REQUEST['od']);$where2 .= " accTrans_T>".intval($od)." and";}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = It6_Date::toTimestamp($_REQUEST['do']);$where2 .= " accTrans_T<".intval($do)." and";}
  	
  	$where2 = substr($where2,0,-3);
  	
    $where3 = '1 and';
  	
    if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toDb($_REQUEST['od']);$where3 .= " zalozen>'".$od."' and";}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = It6_Date::toDb($_REQUEST['do']);$where3 .= " zalozen<'".$do."' and";}
  	
  	$where3 = substr($where3,0,-3);
  	
    $sql = "select r.referer_url,r.user_id,u.mena_id,u.datum_registrace from referer r inner join uzivatel u on r.user_id=u.user_id where u.e_testovaci = 'ne' and ".$where." group by r.user_id,r.referer_url order by r.referer_url";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
    
    $user = $url = array();
    
    while ($row =& $res->fetchRow()){
    	
    	if(!isset($url[$row['referer_url']])){
    	   $url[$row['referer_url']]['pocet_reg'] = 0;
    	   $url[$row['referer_url']]['pocet'] = 0;
    	   $url[$row['referer_url']]['dep_abs'] = 0;
    	   $url[$row['referer_url']]['dep_amount'] = 0;
    	   $url[$row['referer_url']]['ticket_abs'] = 0;
    	   $url[$row['referer_url']]['ticket_amount'] = 0;
    	}
    	
    	//TODO Ma to byt v gm nebo ne? Ted je. Jinak by se muselo dat , false
    	if(!isset($user[$row['user_id']]) && ( (!isset($_REQUEST['od']) && !isset($_REQUEST['do'])) || (isset($_REQUEST['od']) &&  It6_Date::toTimestamp($_REQUEST['od'])<It6_Date::fromDbAsTimestamp(($row['datum_registrace']))) ||  (isset($_REQUEST['do']) && It6_Date::toTimestamp($_REQUEST['do'])>It6_Date::fromDbAsTimestamp($row['datum_registrace'])))  ) $url[$row['referer_url']]['pocet_reg']++;
    	
    	if(!isset($user[$row['user_id']])){
    		
    	   $user[$row['user_id']]['dep_abs'] = 0;
    	   $user[$row['user_id']]['dep_amount'] = 0;
    	   $user[$row['user_id']]['ticket_abs'] = 0;
    	   $user[$row['user_id']]['ticket_amount'] = 0;
    	   
    	   $sql = "select acctrans_amount from accTrans  where acctrans_sign=1 and acctrans_USR=".$row['user_id']." and ".$where2 ;
           $res3 =& $this->dbGame->query($sql);
           if(DB::isError($res3)) {throw new ExHandler($sql.$res3->getMessage(),"admin_ex_db");}
    	   
           while ($row3 =& $res3->fetchRow()){
           	 
           	$user[$row['user_id']]['dep_abs']++;
           	$user[$row['user_id']]['dep_amount']+= ($row3['acctrans_amount']/$mena[$row['mena_id']]);
           	
           }

    	   $sql = "select castka from ticket  where user_id=".$row['user_id']." and ".$where3 ;
           $res3 =& $this->dbGame->query($sql);
           if(DB::isError($res)) {throw new ExHandler($sql.$res3->getMessage(),"admin_ex_db");}
    	   
           while ($row3 =& $res3->fetchRow()){
           	 
           	$user[$row['user_id']]['ticket_abs']++;
           	$user[$row['user_id']]['ticket_amount']+= ($row3['castka']/$mena[$row['mena_id']]);
           	
           }
           
    	}
    	
    	
    	
    	$url[$row['referer_url']]['pocet']++;
    	$url[$row['referer_url']]['dep_abs']+=$user[$row['user_id']]['dep_abs'];
    	$url[$row['referer_url']]['dep_amount']+=$user[$row['user_id']]['dep_amount'];
    	$url[$row['referer_url']]['ticket_abs']+=$user[$row['user_id']]['ticket_abs'];
    	$url[$row['referer_url']]['ticket_amount']+=$user[$row['user_id']]['ticket_amount'];
    	
    }
    
  	


    foreach($url as $k=>$h){
    	
    	if(!isset($pole[$k])) {$pole[$k]['data'] = '';$pole[$k]['xx']=0;}
    	$pole[$k]['xx']++;
    	
    	if($pole[$k]['xx']<$xx){
    	  for($y=0;$y<($xx-$pole[$k]['xx']);$y++)
    	   $pole[$k]['data'] .= '<td> 0 </td><td> 0 </td><td> 0 </td><td> 0 </td><td> 0 </td><td> 0 </td>';
    	   
    	  $pole[$k]['xx'] = $xx;
    	}
    	
    	$pole[$k]['data'] .= '<td> '.$h['pocet'].' </td><td> '.$h['pocet_reg'].' </td><td> '.$h['dep_abs'].' </td><td> '.round($h['dep_amount'],1).' </td><td> '.$h['ticket_abs'].' </td><td> '.round($h['ticket_amount'],1).' </td>';
         
     	 
    }
    
    
  	}
  	
  	
  	
  	foreach($pole as $k=>$h){
  		
  		$this->vrat .= '<tr ><td width="40%" style="background:#D70E00;color:white"> <strong>'.Help::Html($k).'</strong> </td>'.$h['data'].'</tr>'; 
  		    	 
  	}
    $this->vrat .= '</table>';

  	
  }
  
  
  /**
 * Udalost
 * @return string
 */
  public  function Udalost(){

 	$this->vrat .= '<h3>Sport/Událost</h3>';
  	
 	if(!isset($_POST['sport'])) return;
 	
 	$this->vrat .= $this->topTable(1,2,array('Počet sázek','Vsazeno EUR'));
 	
    $sport = $udalost = $mena = array();
  	 
  	$sql = "select e.kurz,e.mena_id from game.kurzmena e where e.platny_od<=now() and e.platny_do>=now() ";
    $res =& $this->dbGame->query($sql);
    while ($row =& $res->fetchRow()){
    	$mena[$row['mena_id']] = $row['kurz'];
    }
    
 	$preklad = new Preklady();
  	
  	
  	foreach($this->interval as $k3=>$h3){
  		
  	$_REQUEST['od'] = $h3[1];
  	$_REQUEST['do'] = $h3[2];
  	
  	$ticket = array();
  	 
  	$sql = "select e.kurz,e.mena_id from game.kurzmena e where e.platny_od<=now() and e.platny_do>=now() ";
    $res =& $this->dbGame->query($sql);
    while ($row =& $res->fetchRow()){
    	$mena[$row['mena_id']] = $row['kurz'];
    }
    
    
    
  	$sql = "select s.sport_id,c.udalost_id,c.nazev AS unazev,s.nazev AS snazev from  udalost c  inner join sport s on s.sport_id=c.sport_id where c.sport_id=".intval($_POST['sport'])." order by s.sport_id,c.pozice";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
    
    while ($row =& $res->fetchRow()){
    	
    	$pr = $preklad->FindPreklad($row['unazev'],1);
    	$prs = $preklad->FindPreklad($row['snazev'],1);
    	
    	$udalost[$row['udalost_id']]['name'] = $pr[1];
    	$udalost[$row['udalost_id']]['sum'][$k3] = 0;
    	$udalost[$row['udalost_id']]['num'][$k3] = 0;
    	$sport[$row['sport_id']]['name'] = $prs[1];
    	$sport[$row['sport_id']]['sum'][$k3] = 0;
    	$sport[$row['sport_id']]['num'][$k3] = 0;
    	if(!in_array($row['udalost_id'],$sport[$row['sport_id']]['udalost']))$sport[$row['sport_id']]['udalost'][] = $row['udalost_id'];
    	
    	
    }
    
  	$where = '1 and';
  	
    if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toDb($_REQUEST['od']);$where .= " a.zalozen>'".$od."' and";}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = It6_Date::toDb($_REQUEST['do']);$where .= " a.zalozen<'".$do."' and";}
  	if(isset($_REQUEST['only_live'])) {$where .= " a.live=1 and";}
  	
  	$where = substr($where,0,-3);
  	
  	$sql = "select a.castka,a.sazka_id,a.udalost_id,c.sport_id,a.ticket_id,b.mena_id from ticket_pohled a inner join udalost c on a.udalost_id=c.udalost_id inner join uzivatel b on a.user_id=b.user_id where b.e_testovaci = 'ne' and c.sport_id=".intval($_POST['sport'])." and ".$where;
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
   
    while ($row =& $res->fetchRow()){
    	
    	if(!isset($ticket[$row['ticket_id']])) {$ticket[$row['ticket_id']]['num'] = 0;$ticket[$row['ticket_id']]['castka']=($row['castka']/$mena[$row['mena_id']]);}
    	
    	$ticket[$row['ticket_id']]['num']++;
    	$sport[$row['sport_id']]['num'][$k3]++;
    	$udalost[$row['udalost_id']]['num'][$k3]++;
    	
    	$ticket[$row['ticket_id']]['sazka'][$row['sazka_id']]['udalost'] =  $row['udalost_id'];
    	$ticket[$row['ticket_id']]['sazka'][$row['sazka_id']]['sport'] =  $row['sport_id'];
    	
    }
  	
  	foreach($ticket as $h){
  		
  		$podil = ($h['castka']/$h['num']);
  		
  		foreach($h['sazka'] as $h2){
  			
  			$udalost[$h2['udalost']]['sum'][$k3] += $podil;
  			$sport[$h2['sport']]['sum'][$k3] += $podil;
  			
  		}
  		
  		
  	}
    
  	
  	}
  	


    foreach($sport as $k=>$h){
    	
    	 $this->vrat .= '<tr style="background:#D70E00;color:white"><td > <strong>'.Help::Html($h['name']).'</strong> </td>';
         
    	 foreach($this->interval as $k3=>$h3){
    	 	 
    	 	if(!isset($h['num'][$k3])) $this->vrat .= '<td> 0</td>';else $this->vrat .= '<td> '.$h['num'][$k3].' </td>';
    	 	if(!isset($h['sum'][$k3])) $this->vrat .= '<td> 0</td>';else $this->vrat .= '<td> '.round($h['sum'][$k3],2).' </td>';
    	 	
    	 }
    	 
    	 $this->vrat .= '</tr>';
    	 
    	 foreach($h['udalost'] as $h2){
    	 	
    	 	 $this->vrat .= '<tr><td > &nbsp;&nbsp;'.Help::Html($udalost[$h2]['name']).' </td>';
    	 	 
    	     foreach($this->interval as $k3=>$h3){
    	 	 
    	 	   if(!isset($udalost[$h2]['num'][$k3])) $this->vrat .= '<td> 0</td>';else $this->vrat .= '<td> '.$udalost[$h2]['num'][$k3].' </td>';
    	 	   if(!isset($udalost[$h2]['sum'][$k3])) $this->vrat .= '<td> 0</td>';else $this->vrat .= '<td> '.round($udalost[$h2]['sum'][$k3],2).' </td>';
    	 	
    	     }
    	 	 
    	 	 $this->vrat .= '</tr>';
    	 	 
    	 }
    	 
    }
    
    $this->vrat .= '</table>';
    
  	
  }
  
     
 /**
 * Zákazníci
 * @return string
 */
  public  function People(){
  	
  	
  	$this->topTable(1,1);
  	$this->vrat .= '<h3>Zákazníci</h3>';
  	$preklad = new Preklady();
  	
  	$rcelkem = $acelkem = $bcelkem = '';
    $rzeme = $azeme = $bzeme = $twin = $tlose = $tbet = array();

    
  	foreach($this->interval as $k3=>$h3){
  	
  	$_REQUEST['od'] = $h3[1];
  	$_REQUEST['do'] = $h3[2];
  	
  	$where = '1 and';
  	$vrat = '';
  	
  	$reg_zeme = $akt_zeme = $bet_zeme = $top_bet = $top_lose = $top_win =  array();
  	$reg_celkem = $akt_celkem = $bet_celkem = 0;

  	
  	
  	
  	#Registrace#
  	if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toDb($_REQUEST['od']);$where .= " a.datum_registrace>'".$od."' and";}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = It6_Date::toDb($_REQUEST['do']);$where .= " a.datum_registrace<'".$do."' and";}
  	
  	$where = substr($where,0,-3);
  	$sql = "select a.*,b.nazev from uzivatel a inner join zeme b on a.zeme_id=b.zeme_id where a.e_testovaci = 'ne' and ".$where;
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
    
    while ($row =& $res->fetchRow()){
    	
    	
    	if(!isset($reg_zeme[$row['zeme_id']])){
    		$pr = $preklad->FindPreklad($row['nazev'],1);
    		$reg_zeme[$row['zeme_id']]['num'] = 0;
    		$reg_zeme[$row['zeme_id']]['name'] = $pr[1];
    	}
    	
    	$reg_zeme[$row['zeme_id']]['num']++;
    	$reg_celkem++;
    	
    }
    #End registrace#
  	
    #Aktivni#
    $where = '1 and';
   
    if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toDb($_REQUEST['od']);$where .= " a.posledni_prihlaseni>'".$od."' and";}
  	//if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = Help::It6_Date::toDb($_REQUEST['do']);$where .= " a.datum_registrace<".$do." and";}
  	
  	$where = substr($where,0,-3);
  	$sql = "select a.*,b.nazev from uzivatel a inner join zeme b on a.zeme_id=b.zeme_id where a.e_testovaci = 'ne' and ".$where;
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
    
    while ($row =& $res->fetchRow()){
    	
    	if(!isset($akt_zeme[$row['zeme_id']])){
            $pr = $preklad->FindPreklad($row['nazev'],1);
    		$akt_zeme[$row['zeme_id']]['num'] = 0;
    		$akt_zeme[$row['zeme_id']]['name'] = $pr[1];
    	}
    	
    	$akt_zeme[$row['zeme_id']]['num']++;
    	$akt_celkem++;
    	
    }
    
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
            $pr = $preklad->FindPreklad($row['nazev'],1);
    		$bet_zeme[$row['zeme_id']]['num'] = 0;
    		$bet_zeme[$row['zeme_id']]['name'] = $pr[1];
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
    
    if(!isset($betCount[0]['nazev']))  $betCount[0]['nazev'] = 'Vsazeno 10 a více';
    if(!isset($betCount[1]['nazev']))  $betCount[1]['nazev'] = 'Vsazeno 5 a více';
    if(!isset($betCount[2]['nazev']))  $betCount[2]['nazev'] = 'Vsazeno 3 a více';
    if(!isset($betCount[3]['nazev']))  $betCount[3]['nazev'] = 'Vsazeno 1 a více';
    
    $betCount[0]['data'][$k3] = $bet10;
    $betCount[1]['data'][$k3] = $bet5;
    $betCount[2]['data'][$k3] = $bet3;
    $betCount[3]['data'][$k3] = $bet1;
    
    #End sazkari#
    
    #Top sazky info#
    
    $where = '1 and';
    if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toDb($_REQUEST['od']);$where .= " b.datum>'".$od."' and";}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = It6_Date::toDb($_REQUEST['do']);$where .= " b.datum<'".$do."' and";}
  	
    $where = substr($where,0,-3);
  	$sql = "SELECT a.user_id,a.nick,a.jmeno,a.prijmeni,(b.castka/(SELECT g.kurz FROM game.kurz f INNER JOIN game.kurz_mena g ON f.id_kurz = g.id_kurz WHERE f.platny_od <= now( ) AND f.platny_do >= now( ) AND g.id_mena=a.mena_id)) AS ecastka
            FROM game.uzivatel a inner join game.vyherci_sazky b  on a.user_id=b.user_id where a.e_testovaci = 'ne' and ".$where." order by ecastka  desc limit 3  ";
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
  	$sql = "SELECT a.user_id,a.nick,a.jmeno,a.prijmeni,(b.castka/(SELECT g.kurz FROM game.kurz f INNER JOIN game.kurz_mena g ON f.id_kurz = g.id_kurz WHERE f.platny_od <= now( ) AND f.platny_do >= now( ) AND g.id_mena=a.mena_id)) AS ecastka
            FROM game.uzivatel a inner join game.ticket b  on a.user_id=b.user_id where a.e_testovaci = 'ne' and ".$where." order by ecastka  desc limit 3  ";
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
  	$sql = "select d.ticket_id from game.vyherci_sazky d where ".$where;
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
    
    while ($row =& $res->fetchRow()) $vyherci[]=$row['ticket_id'];
    
    $where = '1 and';
    if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toDb($_REQUEST['od']);$where .= " b.zalozen>'".$od."' and";}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = It6_Date::toDb($_REQUEST['do']);$where .= " b.zalozen<'".$do."' and";}
  	
  	
    $where = substr($where,0,-3);
    if(count($vyherci)==0)$vyherci[0]=0;
  	$sql = "select a.user_id,a.nick,a.jmeno,a.prijmeni,(b.castka/(SELECT g.kurz FROM game.kurz f INNER JOIN game.kurz_mena g ON f.id_kurz = g.id_kurz WHERE f.platny_od <= now( ) AND f.platny_do >= now( ) AND g.id_mena=a.mena_id)) AS ecastka
    from game.uzivatel a inner join game.ticket b  on a.user_id=b.user_id where a.e_testovaci = 'ne' and b.vyplacen=1 and b.ticket_id not in (".implode(",",$vyherci).") and ".$where." order by ecastka  desc limit 3  ";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
    
    while ($row =& $res->fetchRow()){
        
    	$klic = count($top_lose);
    	$top_lose[$klic]['jmeno'] = $row['jmeno'].' '.$rowp['prijmeni'].'('.$row['nick'].')';
    	$top_lose[$klic]['bet'] = round($row['ecastka'],2);
    	
    }
    
    #End top sazky info#
      
      
      $rcelkem .= '<td> '.$reg_celkem.' </td>';
      $acelkem .= '<td> '.$akt_celkem.' </td>';
      $bcelkem .= '<td> '.$bet_celkem.' </td>';
      
       foreach($reg_zeme as $k=>$h){
       	  if(!isset($rzeme[$k])){$rzeme[$k]['nazev'] = $h['name'];$rzeme[$k]['data']=array();}
       	  $rzeme[$k]['data'][$k3] = '<td> '.intval($h['num']).' </td>';
       }
  	   foreach($akt_zeme as $k=>$h){
       	  if(!isset($azeme[$k])){$azeme[$k]['nazev'] = $h['name'];$azeme[$k]['data']=array();}
       	  $azeme[$k]['data'][$k3] = '<td> '.intval($h['num']).' </td>';
       }
  	   foreach($bet_zeme as $k=>$h){
       	  if(!isset($bzeme[$k])){$bzeme[$k]['nazev'] = $h['name'];$bzeme[$k]['data']=array();} 
       	  $bzeme[$k]['data'][$k3] = '<td> '.intval($h['num']).' </td>';
       }
       
  	   foreach($top_win as $k=>$h){
    	  if(!isset($twin[$k])) $twin[$k] = '';
    	  $twin[$k]  .= '<td> '.Help::Html($h['jmeno']).' </td><td> '.$h['win'].' </td>';
    
       }
  	   foreach($top_lose as $k=>$h){
    	  if(!isset($tlose[$k])) $tlose[$k] = '';
    	  $tlose[$k]  .= '<td> '.Help::Html($h['jmeno']).' </td><td> '.$h['bet'].' </td>';
    
       }
  	   foreach($top_bet as $k=>$h){
    	  if(!isset($tbet[$k])) $tbet[$k] = '';
    	  $tbet[$k]  .= '<td> '.Help::Html($h['jmeno']).' </td><td> '.$h['bet'].' </td>';
    
       }
  	}
  	
  	$this->vrat .= $this->topTable(1,1);

  	$this->vrat .= '<tr><th> REGISTRACE</th></tr>';

    foreach($rzeme as $k=>$h){
    	 
    	 $this->vrat .= '<tr><td> '.Help::Html($h['nazev']).' </td>';
         
    	 foreach($this->interval as $k3=>$h3){
    	    if(!isset($h['data'][$k3]))  $this->vrat .= '<td>0</td>';
    	    else $this->vrat .= $h['data'][$k3];
    	 }
         $this->vrat .= '</tr>';
         
    }
    
    $this->vrat .= '<tr><td> <strong>Celkem</strong> </td>'.$rcelkem.' </tr>';
    $this->vrat .= '</table>';
    
    
    $this->vrat .= $this->topTable(1,1);
  	$this->vrat .= '<tr><th>Aktivni uzivatele (filtr pouze od)</th></tr>';
    foreach($azeme as $k=>$h){
    	
    	 $this->vrat .= '<tr><td> '.Help::Html($h['nazev']).' </td>';
         
    	 foreach($this->interval as $k3=>$h3){
    	    if(!isset($h['data'][$k3]))  $this->vrat .= '<td>0</td>';
    	    else $this->vrat .= $h['data'][$k3];
    	 }
         $this->vrat .= '</tr>';
    
    }
    $this->vrat .= '<tr><td> <strong>Celkem</strong> </td>'.$acelkem.' </tr>';
    $this->vrat .= '</table>';
    
    $this->vrat .= $this->topTable(1,1);
  	$this->vrat .= '<tr><th>Aktivní sázkaři - podal 1 tiket a více</th></tr>';
    foreach($bzeme as $k=>$h){
    	
    	 $this->vrat .= '<tr><td> '.Help::Html($h['nazev']).' </td>';
         
    	 foreach($this->interval as $k3=>$h3){
    	    if(!isset($h['data'][$k3]))  $this->vrat .= '<td>0</td>';
    	    else $this->vrat .= $h['data'][$k3];
    	 }
         $this->vrat .= '</tr>'; 
    
    }
    $this->vrat .= '<tr><td> <strong>Celkem</strong> </td>'.$bcelkem.' </tr>';
    $this->vrat .= '</table>';
    
    $this->vrat .= $this->topTable(1,2,array('Absolutně','Poměr'));
    $this->vrat .= '<tr><th>Aktivní sázkaři</th></tr>';
    
    foreach($betCount as $k=>$h){
    	
    	 $this->vrat .= '<tr><td> '.Help::Html($h['nazev']).' </td>';
         
    	 foreach($this->interval as $k3=>$h3){
    	    if(!isset($h['data'][$k3]))  $this->vrat .= '<td>0</td><td>0</td>';
    	    else $this->vrat .= '<td>'.$h['data'][$k3].'</td><td>'.round((($h['data'][$k3]/$bcelkem)*100),2).'%</td>';
    	 }
         $this->vrat .= '</tr>'; 
    
    }

    $this->vrat .= '</table>';
    
    $this->vrat .= $this->topTable(1,2,array('Uživatel','Výhra EUR'));
  	$this->vrat .= '<tr><th>Top výherci sázky</th></tr>';

    $this->vrat .= '<tr><td>1</td>'.$twin[0].' </tr>';
    $this->vrat .= '<tr><td>2</td>'.$twin[1].' </tr>';
    $this->vrat .= '<tr><td>3</td>'.$twin[2].' </tr>';
    
    $this->vrat .= '</table>';

    
    $this->vrat .= $this->topTable(1,2,array('Uživatel','Sázka EUR'));
  	$this->vrat .= '<tr><th>Top prohranná částka na jeden tiket sázky</th></tr>';
    
  	$this->vrat .= '<tr><td>1</td>'.$tlose[0].' </tr>';
    $this->vrat .= '<tr><td>2</td>'.$tlose[1].' </tr>';
    $this->vrat .= '<tr><td>3</td>'.$tlose[2].' </tr>';
    
    $this->vrat .= '</table>';

    
    $this->vrat .= $this->topTable(1,2,array('Uživatel','Sázka EUR'));
  	$this->vrat .= '<tr><th>Top vsazesná částka na jeden tiket sázky</th></tr>';

  	$this->vrat .= '<tr><td>1</td>'.$tbet[0].' </tr>';
    $this->vrat .= '<tr><td>2</td>'.$tbet[1].' </tr>';
    $this->vrat .= '<tr><td>3</td>'.$tbet[2].' </tr>';
    
    $this->vrat .= '</table>';
    
    
    
    
  }
  
 /**
 * Vybery
 * @return string
 */
  public  function Vybery(){
  	
  	$preklad = new Preklady();
  	$this->vrat .= '<h3>Výběry</h3>';
  	$this->topTable(1,2,array('Celkem abs.','Celkem v EUR'));
  	
  	$celkem = $met = $zeme = $menax = array();$celkem[0]= $celkem[1] = $celkem[2] = '';
  	

  	foreach($this->interval as $k3=>$h3){ 
  		
  	
  	$_REQUEST['od'] = $h3[1];
  	$_REQUEST['do'] = $h3[2];
  	
  	
    $mena = $vklady =  $met_abs = $met_eur = $zeme_abs = $zeme_eur = $mena_abs = $mena_eur = array();
  	
    $celkem_eur = 0;
    
  	$sql = "select e.kurz,e.mena_id from game.kurzmena e where e.platny_od<=now() and e.platny_do>=now() ";
    $res =& $this->dbGame->query($sql);
    while ($row =& $res->fetchRow()){
    	$mena[$row['mena_id']] = $row['kurz'];
    }
    
  	$vrat = '';
  	
  	$where = '1 and';
  	
  	if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toTimestamp($_REQUEST['od']);$where .= " accTrans_T>".intval($od)." and";}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = It6_Date::toTimestamp($_REQUEST['do']);$where .= " accTrans_T<".intval($do)." and";}
  	
  	$where = substr($where,0,-3);
  	
  	#Vybery#
    $sql = "select a.*,b.met_name,c.zeme_id from accTrans a inner join method b on a.acctrans_met=b.met_namex  inner join uzivatel c on a.acctrans_USR=c.user_id where c.e_testovaci = 'ne' and acctrans_ST<>3 and b.met_sign=2 and c.user_id not in(".PAY_NOTUSR.") and a.acctrans_sign=2 and ".$where." order by c.zeme_id";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
  	
    while ($row =& $res->fetchRow()){
    	
    	$celkem_eur += ($row['acctrans_amount']/$mena[$row['acctrans_CUR']]);
    	
    	if(!isset($met_abs[$row['acctrans_met']])){
    		//echo $row['accTrans_met'].'<br />';
    		$pr = $preklad->FindPreklad($row['met_name'],1);
    		$met_abs[$row['acctrans_met']]['name'] = $pr[1];
    		$met_abs[$row['acctrans_met']]['num'] = 0;
    		$met_eur[$row['acctrans_met']]['num'] = 0;		
    		
    	}
    	
    	if(!isset($zeme_abs[$row['zeme_id']])){
    		 		 
  	         $sql = "select nazev from zeme where zeme_id=".$row['zeme_id'];
             $res2 =& $this->dbGame->query($sql);
             if(DB::isError($res2)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
             if ($row2 =& $res2->fetchRow()) $pr = $preklad->FindPreklad($row2['nazev'],1);
             
             $zeme_abs[$row['zeme_id']]['name'] = $pr[1];
    		 $zeme_abs[$row['zeme_id']]['num'] = 0;
    		 $zeme_eur[$row['zeme_id']]['num'] = 0;			
             
    	}
    	
        if(!isset($mena_abs[$row['acctrans_CUR']])){
    		 		 
  	         $sql = "select mena_text from mena where mena_id=".$row['acctrans_CUR'];
             $res2 =& $this->dbGame->query($sql);
             if(DB::isError($res2)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
             if ($row2 =& $res2->fetchRow()) $mena_abs[$row['acctrans_CUR']]['name'] = $row2['mena_text'];;
             
             
    		 $mena_abs[$row['acctrans_CUR']]['num'] = 0;
    		 $mena_eur[$row['acctrans_CUR']]['num'] = 0;			
             
    	}
    	
    	$met_abs[$row['acctrans_met']]['num']++;
    	$met_eur[$row['acctrans_met']]['num'] += ($row['acctrans_amount']/$mena[$row['acctrans_CUR']]);
    	
    	$zeme_abs[$row['zeme_id']]['num']++;
        $zeme_eur[$row['zeme_id']]['num'] += ($row['acctrans_amount']/$mena[$row['acctrans_CUR']]);	
        
        $mena_abs[$row['acctrans_CUR']]['num']++;
    	$mena_eur[$row['acctrans_CUR']]['num'] += ($row['acctrans_amount']/$mena[$row['acctrans_CUR']]);
    	
    }
    
  	 $celkem[0] .= '<td>'.$res->numRows().'</td><td>'.round($celkem_eur,2).'</td>';
     
  	 foreach($met_abs as $k=>$h){
     	
     	
  	    if(!isset($met[$k])){
     		
     		$met[$k]['name'] = Help::Html($h['name']); 
     		$met[$k]['data']= array();
     	
     	}
     	
     	$met[$k]['data'][$k3] = '<td>'.$h['num'].'</td><td>'.round($met_eur[$k]['num'],2).'</td>';
     	
     }
    
     
  	 foreach($zeme_abs as $k=>$h){
     	
     	if(!isset($zeme[$k])){
     		
     		$zeme[$k]['name'] = Help::Html($h['name']);
     		$zeme[$k]['data']= array();
     	
     	}
     	
     	$zeme[$k]['data'][$k3] = '<td>'.$h['num'].'</td><td>'.round($zeme_eur[$k]['num'],2).'</td>';
     	
     }
     
  	 foreach($mena_abs as $k=>$h){
     	
     	if(!isset($menax[$k])){
     		
     		$menax[$k]['name'] = Help::Html($h['name']);
     		$menax[$k]['data']= array();
     	
     	}
     	
     	$menax[$k]['data'][$k3] = '<td>'.$h['num'].'</td><td>'.round($mena_eur[$k]['num'],2).'</td>';
     	
     }
     

  	}
  	
  	
    $this->vrat .= $this->topTable(1,2,array('Celkem abs.','Celkem v EUR'));
 
    $this->vrat .= '<tr><th> Celkem </th>'.$celkem[0].'</tr>';
     
    $this->vrat .= '</table>';
    
    $this->vrat .= $this->topTable(1,2,array('Celkem abs.','Celkem v EUR'));
    
    foreach($met as $k=>$h){
    	
    	 $this->vrat .= '<tr><th> '.Help::Html($h['name']).' </th> ';
    	 
    	 foreach($this->interval as $k3=>$h3){
    	 	
    	   if(!isset($h['data'][$k3]))  $this->vrat .= '<td>0</td><td>0</td>';
    	   else $this->vrat .= $h['data'][$k3];
    	 }
         $this->vrat .= '</tr>';
    
    }
    
    $this->vrat .=  '</table>';
    
    
    $this->vrat .= $this->topTable(1,2,array('Celkem abs.','Celkem v EUR'));
    
    foreach($zeme as $k=>$h){
    	
    	 $this->vrat .= '<tr><th> '.Help::Html($h['name']).' </th> ';
    	 
    	 foreach($this->interval as $k3=>$h3){
    	   if(!isset($h['data'][$k3]))  $this->vrat .= '<td>0</td><td>0</td>';
    	   else $this->vrat .= $h['data'][$k3];
    	 }
         $this->vrat .= '</tr>';
    
    }
    
    $this->vrat .=  '</table>';
    
    
    $this->vrat .= $this->topTable(1,2,array('Celkem abs.','Celkem v EUR'));
    
    foreach($menax as $k=>$h){
    	
    	 $this->vrat .= '<tr><th> '.Help::Html($h['name']).' </th> ';
    	 
    	 foreach($this->interval as $k3=>$h3){
    	   if(!isset($h['data'][$k3]))  $this->vrat .= '<td>0</td><td>0</td>';
    	   else $this->vrat .= $h['data'][$k3];
    	 }
         $this->vrat .= '</tr>';
    
    }
    
    $this->vrat .= '</table>';
    
  	
  }
  
    /**
 * Vklady
 * @return string
 */
  public  function Vklady(){
  	
  	$preklad = new Preklady();
  	
  	$this->topTable(1,2,array('Celkem abs.','Celkem v EUR'));
  	
  	$this->vrat .= '<h3>Vklady</h3>';
  	
  	$celkem = $celkem_reg = $met = $zeme  = $menax = array();$celkem[0]= $celkem[1] = $celkem[2] = '';$celkem_reg[0]= $celkem_reg[1] = $celkem_reg[2] = '';
    
   
  	foreach($this->interval as $k3=>$h3){
  		
    $mena = $vklady =  $met_abs = $met_eur = $zeme_abs = $zeme_eur = $mena_abs = $mena_eur = $user = array();
  	
    $_REQUEST['od'] = $h3[1];
  	$_REQUEST['do'] = $h3[2];
  	
  	
    $celkem_eur = $celkem_eur_reg = $num_user = $num_deposit_reg =0;
    
  	$sql = "select e.kurz,e.mena_id from game.kurzmena e where e.platny_od<=now() and e.platny_do>=now() ";
    $res =& $this->dbGame->query($sql);
    while ($row =& $res->fetchRow()){
    	$mena[$row['mena_id']] = $row['kurz'];
    }
    
  	
  	$where = '1 and';
  	
  	if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toDb($_REQUEST['od']);$where .= " datum_registrace>'".$od."' and";}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = It6_Date::toDb($_REQUEST['do']);$where .= " datum_registrace<'".$do."' and";}
  	
  	$where = substr($where,0,-3);
  	
  	#Pocet uzivatelu#
    $sql = "select user_id from uzivatel where e_testovaci = 'ne' and ".$where;
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
    $num_user = $res->numRows();
  	while ($row =& $res->fetchRow()){$user[]=$row['user_id'];}
  	
  	$where = '1 and';
  	
  	if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toTimestamp($_REQUEST['od']);$where .= " accTrans_T>".intval($od)." and";}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = It6_Date::toTimestamp($_REQUEST['do']);$where .= " accTrans_T<".intval($do)." and";}
  	
  	$where = substr($where,0,-3);
  	
  	#Vklady#
    $sql = "select a.*,b.met_name,c.zeme_id,c.user_id from accTrans a inner join method b on a.acctrans_met=b.met_namex  inner join uzivatel c on a.acctrans_USR=c.user_id where c.e_testovaci = 'ne' and acctrans_ST<>3 and b.met_sign=1 and c.user_id not in(".PAY_NOTUSR.") and a.acctrans_sign=1 and ".$where." order by c.zeme_id";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
  	
    while ($row =& $res->fetchRow()){
    	
    	$celkem_eur += ($row['acctrans_amount']/$mena[$row['acctrans_CUR']]);
        if(in_array($row['user_id'],$user)) {$num_deposit_reg++;$celkem_eur_reg += ($row['acctrans_amount']/$mena[$row['acctrans_CUR']]);}
    
    	if(!isset($met_abs[$row['acctrans_met']]) && substr_count($row['acctrans_note'],"PBT") == 0 && substr_count($row['acctrans_note'],"MBD") == 0){
               
    		   $pr = $preklad->FindPreklad($row['met_name'],1);
    		   $met_abs[$row['acctrans_met']]['name'] = $pr[1];
    		   $met_abs[$row['acctrans_met']]['num'] = 0;
    		   $met_eur[$row['acctrans_met']]['num'] = 0;	
     		
    	}
    	else if(substr_count($row['acctrans_note'],"PBT") > 0 && !isset($met_abs[23])){

    			$met_abs[23]['name'] = "Vklad banka MB";
    		    $met_abs[23]['num'] = 0;
                $met_eur[23]['num'] = 0;

    	}
        else if(substr_count($row['acctrans_note'],"MBD") > 0 && !isset($met_abs[88])){

    			$met_abs[88]['name'] = "Přímé metody MB";
    		    $met_abs[88]['num'] = 0;
                $met_eur[88]['num'] = 0;
    		
    	}
    	
    	if(!isset($zeme_abs[$row['zeme_id']])){
    		 		 
  	         $sql = "select nazev from zeme where zeme_id=".$row['zeme_id'];
             $res2 =& $this->dbGame->query($sql);
             if(DB::isError($res2)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
             if ($row2 =& $res2->fetchRow()) $pr = $preklad->FindPreklad($row2['nazev'],1);
             
             $zeme_abs[$row['zeme_id']]['name'] = $pr[1];
    		 $zeme_abs[$row['zeme_id']]['num'] = 0;
    		 $zeme_eur[$row['zeme_id']]['num'] = 0;			
             
    	}
    	
        if(!isset($mena_abs[$row['acctrans_CUR']])){
    		 		 
  	         $sql = "select mena_text from mena where mena_id=".$row['acctrans_CUR'];
             $res2 =& $this->dbGame->query($sql);
             if(DB::isError($res2)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
             if ($row2 =& $res2->fetchRow()) $mena_abs[$row['acctrans_CUR']]['name'] = $row2['mena_text'];;
             
             
    		 $mena_abs[$row['acctrans_CUR']]['num'] = 0;
    		 $mena_eur[$row['acctrans_CUR']]['num'] = 0;			
             
    	}
    	
       		if(substr_count($row['acctrans_note'],"PBT") > 0){
       	       $met_abs[23]['num']++;
    	       $met_eur[23]['num'] += ($row['acctrans_amount']/$mena[$row['acctrans_CUR']]);
    		}
    		else if(substr_count($row['acctrans_note'],"MBD") > 0){
    	       $met_abs[88]['num']++;
    	       $met_eur[88]['num'] += ($row['acctrans_amount']/$mena[$row['acctrans_CUR']]);
    		}
    		else{
    	       $met_abs[$row['acctrans_met']]['num']++;
    	       $met_eur[$row['acctrans_met']]['num'] += ($row['acctrans_amount']/$mena[$row['acctrans_CUR']]);
    		}

    	
    	$zeme_abs[$row['zeme_id']]['num']++;
        $zeme_eur[$row['zeme_id']]['num'] += ($row['acctrans_amount']/$mena[$row['acctrans_CUR']]);	
        
        $mena_abs[$row['acctrans_CUR']]['num']++;
    	$mena_eur[$row['acctrans_CUR']]['num'] += ($row['acctrans_amount']/$mena[$row['acctrans_CUR']]);
    	
    }
    
    
     $celkem[0] .= '<td>'.$res->numRows().'</td><td>'.round($celkem_eur,2).'</td>';
     $celkem[2] .= '<td colspan="3">'.round(($celkem_eur/$num_user),2).'</td>';
     $celkem_reg[0] .= '<td>'.$num_deposit_reg.'</td><td>'.round($celkem_eur_reg,2).'</td>';
     $celkem_reg[2] .= '<td colspan="2">'.round(($celkem_eur_reg/$num_user),2).'</td>';
     
    $sql = "select SUM(a.acctrans_amount/(select e.kurz from game.kurzmena e where e.platny_od<=now() and e.platny_do>=now() and e.mena_id=b.mena_id)) AS 'castka' from accTrans a	
            inner join uzivatel b on a.acctrans_USR=b.user_id where b.e_testovaci = 'ne' and ".$where." and a.acctrans_sign=1 and a.acctrans_CHB_Amount is not null";
    $res55 =& $this->dbGame->query($sql);
    if(DB::isError($res55)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
  	
    $chargeback = 0;
    
    while ($row =& $res55->fetchRow()){
      $chargeback = $row['castka'];
    }
    $celkem[0] .= '<td>'.round($chargeback,2).'</td>';
    
     foreach($met_abs as $k=>$h){
     	
     	if(!isset($met[$k])){
     		
     		$met[$k]['name'] = Help::Html($h['name']); 
     		$met[$k]['data']= array();
     	
     	}
     	
     	$met[$k]['data'][$k3] = '<td>'.$h['num'].'</td><td>'.round($met_eur[$k]['num'],2).'</td>';
     	
     }
    
     
  	 foreach($zeme_abs as $k=>$h){
     	
     	if(!isset($zeme[$k])){
     		
     		$zeme[$k]['name'] = Help::Html($h['name']);
     		$zeme[$k]['data']= array();
     	
     	}
     	
     	$zeme[$k]['data'][$k3] = '<td>'.$h['num'].'</td><td>'.round($zeme_eur[$k]['num'],2).'</td>';
     	
     }
     
  	 foreach($mena_abs as $k=>$h){
     	
     	if(!isset($menax[$k])){
     		
     		$menax[$k]['name'] = Help::Html($h['name']);
     		$menax[$k]['data']= array();
     	
     	}
     	
     	$menax[$k]['data'][$k3] = '<td>'.$h['num'].'</td><td>'.round($mena_eur[$k]['num'],2).'</td>';
     	
     } 
     
   }
    
    $this->vrat .= $this->topTable(1,2,array('Celkem abs.','Celkem v EUR'));
 
    $this->vrat .= '<tr><th> Celkem (REGISTROVANÝ) </th>'.$celkem_reg[0].'</tr>';
    $this->vrat .= '<tr><th> (REGISTROVANÝ) Vloženo na jednoho uživatele zaregistrovaného ve vybraném období </th>'.$celkem_reg[2].' </tr>';
     
    $this->vrat .= '</table>';
    
    $this->vrat .= $this->topTable(1,3,array('Celkem abs.','Celkem v EUR','Chargeback v EUR'));
 
    $this->vrat .= '<tr><th> Celkem </th>'.$celkem[0].'</tr>';
    $this->vrat .= '<tr><th> Vloženo na jednoho uživatele zaregistrovaného ve vybraném období </th>'.$celkem[2].' </tr>';
     
    $this->vrat .= '</table>';
    
    $this->vrat .= $this->topTable(1,2,array('Celkem abs.','Celkem v EUR'));
 
    
    foreach($met as $k=>$h){
    	
    	 $this->vrat .= '<tr><th> '.Help::Html($h['name']).' </th> ';
    	 
    	 foreach($this->interval as $k3=>$h3){
    	 	
    	   if(!isset($h['data'][$k3]))  $this->vrat .= '<td>0</td><td>0</td>';
    	   else $this->vrat .= $h['data'][$k3];
    	 }
         $this->vrat .= '</tr>';
    
    }
    
    $this->vrat .=  '</table>';
    
    
    $this->vrat .= $this->topTable(1,2,array('Celkem abs.','Celkem v EUR'));
    
    foreach($zeme as $k=>$h){
    	
    	 $this->vrat .= '<tr><th> '.Help::Html($h['name']).' </th> ';
    	 
    	 foreach($this->interval as $k3=>$h3){
    	   if(!isset($h['data'][$k3]))  $this->vrat .= '<td>0</td><td>0</td>';
    	   else $this->vrat .= $h['data'][$k3];
    	 }
         $this->vrat .= '</tr>';
    
    }
    
    $this->vrat .=  '</table>';
    
    
    $this->vrat .= $this->topTable(1,2,array('Celkem abs.','Celkem v EUR'));
    
    foreach($menax as $k=>$h){
    	
    	 $this->vrat .= '<tr><th> '.Help::Html($h['name']).' </th> ';
    	 
    	 foreach($this->interval as $k3=>$h3){
    	   if(!isset($h['data'][$k3]))  $this->vrat .= '<td>0</td><td>0</td>';
    	   else $this->vrat .= $h['data'][$k3];
    	 }
         $this->vrat .= '</tr>';
    
    }
    
    $this->vrat .= '</table>';
    
    
  	
  }
  
  

  
  /**
 * Saldo sazek
 * @return string
 */
  public  function BetSaldo(){
  	
    $this->vrat .= '<h3 >Sázky saldo finance</h3>';
    		
  	$ob = new StatistikySazky();
  	$this->vrat .= $this->topTable(1,1);
  	
  	//$this->vrat .= '<tr>';
  	
  	$r1 = $r2 = $r3 = $r4 = $r5 = $r6 = $r7 = '';
  	
  	foreach($this->interval as $h){
  	//echo $h[1].' - '.$h[2].'<br />';
  	$_POST['od'] = $h[1];
  	$_POST['do'] = $h[2];
  	
  	$ob->TicketInfo();
  	
 	$r1 .= '<td>'.$ob->infoGlobal[0].'</td>';
  	$r2 .= '<td>'.$ob->infoGlobal[6].'</td>';
  	$r3 .= '<td>'.$ob->infoGlobal[1].'</td>';
  	$r4 .= '<td>'.$ob->infoGlobal[2].'</td>';
  	$r5 .= '<td>'.$ob->infoGlobal[3].'</td>';
  	$r6 .= '<td>'.$ob->infoGlobal[4].'</td>';
  	$r7 .= '<td>'.$ob->infoGlobal[5].'</td>';
  	   	 	 	 	 	 	
  	}
  	
  	$this->vrat .= '<tr><th nowrap="nowrap">Celkem tiketů</th>'.$r1.'</tr>'.'<tr><th nowrap="nowrap">Free tikety</th>'.$r2.'</tr>'.'<tr><th nowrap="nowrap">Celkem vsazeno EUR (včetně free tiketů)</th>'.$r3.'</tr>'.'<tr><th nowrap="nowrap">Skutečně bylo vyplaceno EUR</th>'.$r4.'</tr>'.'<tr><th nowrap="nowrap">Skutečně hráči prohráli EUR</th>'.$r5.'</tr>'.'<tr><th nowrap="nowrap">Čistá výhra EUR</th>'.$r6.'</tr>'.'<tr><th nowrap="nowrap">Výhernost %</th>'.$r7.'</tr>';
  	
  	$this->vrat .= '</table>';
  	
  }
  
   /**
 * Vyhledavaci formular
 * @return void
 */
  public  function searchForm(){
  	
   $this->vrat .= '<form method="post" class="noprint" action="?section='.$this->section.'">
                  <table style="border:1px solid #E3E3E3;font-size:0.9em;background:#708090;color:white">';
   $this->vrat .= ' <tr><td class="textleft" style="background:#929EAD;" colspan="4">Datum</td></tr>';
   $this->vrat .= ' <tr><td class="textleft" style="background:#929EAD;" colspan="4">Formát data dd.mm.rrrr hh:mm:ss</td></tr>';
   $this->vrat .= ' <tr><td><input type="text" class="input" name="od" id="od" maxlength="19" value="'.(isset($_REQUEST['od'])?Help::Html($_REQUEST['od']):"").'" /><a href="javascript:window.open(\'calendar.php?cas=od\',\'\',\'width=140,height=247\');void(0);"><img src="_clip/calendar.gif" alt="Kalendář" class="img" /></a></td><td><input type="text" maxlength="19" class="input" name="do" id="do" value="'.(isset($_REQUEST['do'])?Help::Html($_REQUEST['do']):"").'" /><a href="javascript:window.open(\'calendar.php?cas=do\',\'\',\'width=140,height=247\');void(0);"><img src="_clip/calendar.gif" alt="Kalendář" class="img" /></a></td></tr>';				
   
   $this->vrat .= ' <tr><td valign="top">Měsíc</td><td><select name="mesic[]" size="3" multiple="multiple"><option value="1">Leden</option><option value="2">Únor</option><option value="3">Březen</option><option value="4">Duben</option><option value="5">Květen</option><option value="6">Červen</option><option value="7">Červenec</option><option value="8">Srpen</option><option value="9">Září</option><option value="10">�?íjen</option><option value="11">Listopad</option><option value="12">Prosinec</option></select></td><td valign="top">Rok</td><td><select name="rok[]" size="3" multiple="multiple"><option value="2007">2007</option><option value="2008">2008</option><option value="2009">2009</option><option value="2010">2010</option><option value="2011">2011</option><option value="2012">2012</option><option value="2013">2013</option><option value="2014">2014</option><option value="2015">2015</option><option value="2016">2016</option><option value="2017">2017</option></select></td></tr>';
   $this->vrat .= ' <tr><td colspan="4">Měsíc <input type="radio" class="no" name="type" value="1"/> Den <input type="radio" class="no" name="type" value="2"/></td></tr>';  
   
   $this->vrat .= ' <tr><td class="textleft" style="background:#929EAD;" colspan="4">Sestavy</td></tr>';
   $this->vrat .= ' <tr><td>Sázky saldo finance</td><td><input type="checkbox" class="no" name="v1" /></td><td>Zákazníci</td><td><input type="checkbox" class="no"  name="v6" /></td></tr>';
   $this->vrat .= ' <tr><td>Vklady</td><td><input type="checkbox" class="no"  name="v4" /></td><td>Výběry</td><td><input type="checkbox" class="no"  name="v5" /></td></tr>';
   $this->vrat .= ' <tr><td>Aktivita</td><td><input type="checkbox" class="no"  name="v7" /></td><td>Sporty/Události</td><td><input type="checkbox" class="no"  name="v8" /></td></tr>';
   $this->vrat .= '<tr><td colspan="4"><input type="submit" name="filtr" class="inputs" value="Filtr" /></td></tr>';
   $this->vrat .= '</table></form>';
   
    
   
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