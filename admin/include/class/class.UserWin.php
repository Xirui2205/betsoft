<?php
/**
 * @package    Sazky
 */



class UserWin{
	
/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */              
private  $dbGame;

/**
 * vraci pole s udaji
 * @access private
 * @var array
 */              
private  $pole;

/**
 * vraci obrazkovy div
 * @access private
 * @var string
 */              
private  $div;

  public function __construct($db=NULL){
  
    if($db == NULL){
  	
    $this->dbGame = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
    if (DB::isError($this->dbGame)) {
      throw new ExHandler($this->dbGame->getMessage(),"admin_ex_db");
    }
    $this->dbGame->setFetchMode(DB_FETCHMODE_ASSOC);
    $sql = "set names 'utf8'";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");
   
    }else
      $this->dbGame = $db;
   
  }

  
  /**
 * metoda vypocita udaje k danemu hraci
 * @param int $uid id hrace
 * @return boolean
 */
  private function findUser($uid=NULL){
   
   $this->pole['vyhernost'] = 0;
   $this->pole['month'] = 0;
   $this->pole['week'] = 0;
   
   if($uid == NULL || intval($uid)==0) return array();
   
   $sql = "select vyhernost,castka_m,castka_w from uzivatel where user_id=".$uid;
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
   
   if ($row =& $res->fetchRow()){
   
   	  $this->pole['vyhernost'] = $row['vyhernost'];
   	  $this->pole['month'] = $row['castka_m'];
   	  $this->pole['week'] = $row['castka_w'];
   	  
   }
   
   return true;
   
  }
  
 /**
 * metoda vraci udaje k danemu hraci
 * @param int $uid id hrace
 * @return array
 */
  public function getArray($uid=NULL){
    
  	$this->findUser($uid);
  	
  	return $this->pole;
  	
  }
 
 /**
 * metoda vraci udaje barevne odlisene
 * @param int $uid id hrace
 * @return string
 */
  public function getDiv($uid=NULL){
    
  	$this->findUser($uid);
  	
  	$vrat = '';
  	if($this->pole['vyhernost']<=80) $vrat .= '<div style="float:left;width:70px;color:black;background-color:#00ff7f;text-align:center;border:1px dotted black">'.round($this->pole['vyhernost'],2).'%</div>';
  	else if($this->pole['vyhernost']>80 && $this->pole['vyhernost']<=95) $vrat .= '<div style="float:left;width:70px;color:black;background-color:#00cc00;text-align:center;border:1px dotted black">'.round($this->pole['vyhernost'],2).'%</div>';
  	else if($this->pole['vyhernost']>95 && $this->pole['vyhernost']<=105) $vrat .= '<div style="float:left;width:70px;color:black;background-color:#f5f700;text-align:center;border:1px dotted black">'.round($this->pole['vyhernost'],2).'%</div>';
  	else if($this->pole['vyhernost']>105 && $this->pole['vyhernost']<=120) $vrat .= '<div style="float:left;width:70px;color:black;background-color:#ff9933;text-align:center;border:1px dotted black">'.round($this->pole['vyhernost'],2).'%</div>';
  	else if($this->pole['vyhernost']>120) $vrat .= '<div style="float:left;width:70px;color:black;background-color:#ff3300;text-align:center;border:1px dotted black">'.round($this->pole['vyhernost'],2).'%</div>';
  	
  	if($this->pole['month']>=3000 || $this->pole['week']>=1000) $vrat .= '<div style="float:left;width:200px;color:black;background-color:#6699ff">M:'.intval($this->pole['month']).' EUR; W:'.intval($this->pole['week']).' EUR</div>';
  	
  	return  $vrat;
  	
  }
  
}

?>