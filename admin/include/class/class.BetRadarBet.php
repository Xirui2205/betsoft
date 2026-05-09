<?php


/**
 * Trida pro praci se sazkou
 *
 * 
 * <code>
 * 
 * </code>
 *
 * @package    Ciselniky
 */
 
class BetRadarLiveBet{
	
/**
 * Tato metoda se snazi zalogovat
 * @param object $dbGame  spojeni na databazi
 * @param object $odds  objekt kurzu
 * @param int $sport_id  id sportu
 * @param int $udalost_id  id udalosti
 * @param int $event_id  id live sazky
 * @param int $event_id  id live sazky
 * return bool
 */
  public static function addOdds($dbGame,$odds,$sport_id,$udalost_id,$event_id,$betstatus='stopped'){

  	if(isset(BetRadarLiveBetting::$betTypeReverse[intval($odds['typeid'])]['type'])){

  		$typ    = BetRadarLiveBetting::$betTypeReverse[intval($odds['typeid'])]['type'][0];
  		$podtyp = BetRadarLiveBetting::$betTypeReverse[intval($odds['typeid'])]['type'][1];
  		
  	}
  	else if(isset(BetRadarLiveBetting::$betTypeReverse[intval($odds['typeid'])]['subtype'])){

  		if(isset(BetRadarLiveBetting::$betTypeReverse[intval($odds['typeid'])]['subtype'][intval($odds['subtype'])])){
  		 $typ    = BetRadarLiveBetting::$betTypeReverse[intval($odds['typeid'])]['subtype'][intval($odds['subtype'])][0];
  		 $podtyp = BetRadarLiveBetting::$betTypeReverse[intval($odds['typeid'])]['subtype'][intval($odds['subtype'])][1];
  		}else 
  		     return false;
  		
  	}
  	else return false;
  	

  	

  	 $date =  It6_Date::dbNow();
  	
  	   	  	            
  	 $select = $dbGame->select()->from(array('a'=>'live_sazka'),array('a.close','a.sazka_id','a.no_update')) 
  			                                  ->where("a.event_id=?",$event_id)
  			                                  ->where("a.betradar_sazka_id=?",intval($odds['id']));
     $stm  = $select->query();
     $row = $stm->fetchAll();
	  
     if(count($row) > 0 && $odds['changed'] == 'false' && $row[0]['close'] == 0 && $row[0]['no_update'] == 0){
       	$data = array();
  	    $data['status'] = (intval($odds['active']) == 0 || $betstatus == 'stopped'?2:0);
  	    
  	    try{
  	 	 
  	    	$dbGame->update('sazky', $data,"sazka_id=".$row[0]['sazka_id']);
  	    
  	    }catch(Zend_Exception $e){

                	BetRadarLiveBetting::Error("Nepodarilo se updatovat sazky sazku id: ".$row[0]['sazka_id']." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
  	  	 	            return false;
  	    }
     }
      
     $dbGame->beginTransaction();
     
     if(!isset($odds->OddsField)) {$dbGame->commit();return false;}
       
  	 if(count($row) > 0 && $odds['changed'] == 'true' && $row[0]['close'] == 0){ //update
        
  	 	if($row[0]['no_update'] == 1) {$dbGame->commit();return false;}
  	 	
  	 	$data = array();
  	    $data['aktualizace'] =  $date;
  	    $data['aktualizace_sazka'] =  $date;
  	    
  	    try{
  	 	 
  	    	$dbGame->update('live_sazka', $data,"event_id=".$event_id." and betradar_sazka_id=".intval($odds['id']));
  	    
  	    }catch(Zend_Exception $e){

                	BetRadarLiveBetting::Error("Nepodarilo se updatovat live_sazky sazku id: ".$row[0]['sazka_id']." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
  	 	            $dbGame->rollBack();
  	 	            return false;
  	    }

  	    $data = array();
  	    $data['status'] = (intval($odds['active']) == 0 || $betstatus == 'stopped'?2:0);
  	    
  	    try{
  	 	 
  	    	$dbGame->update('sazky', $data,"sazka_id=".$row[0]['sazka_id']);
  	    
  	    }catch(Zend_Exception $e){

                	BetRadarLiveBetting::Error("Nepodarilo se updatovat sazky sazku id: ".$row[0]['sazka_id']." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
  	 	            $dbGame->rollBack();
  	 	            return false;
  	    }
  	    
  	    $fce = BetRadarLiveBetting::$betType[$typ][$podtyp][5];
   	    self::$fce($dbGame,$odds,$row[0]['sazka_id'],$date); 
  
  	 }
  	 else if(count($row) == 0) { //insert
 
  	 	$select = $dbGame->select()->from(array('a'=>'bet_settings'),array('a.risk_limit')) 
  			                                  ->where("a.udalost_id=?",$udalost_id)
  			                                  ->where("a.typ_id=?",$typ)
  			                                  ->where("a.podtyp_id=?",$podtyp);
        $stm  = $select->query();
        $row = $stm->fetchAll();
     

        if (count($row)>0){$risk = $row[0]['risk_limit'];}else $risk = 300000;
   
  	   	$data = array();
  	    $data['platna_od'] = $date;
  	    $data['platna_do'] = '2020-02-02';
  	    $data['status'] = (intval($odds['active']) == 0 || $betstatus == 'stopped'?2:0);
  	    $data['live'] = 1;
  	    $data['bookmaker_id'] = 1;
  	    $data['udalost_id'] = $udalost_id;
  	    $data['typ_id'] = $typ;
  	    $data['podtyp_id'] = $podtyp;
  	    $data['text'] = (BetRadarLiveBetting::$betType[$typ][$podtyp][6] == 1?$odds['specialoddsvalue']:'');
  	    $data['jednoducha'] = 1;
  	    $data['risk_limit'] = $risk;
  	    
  	    try{

  	    	$dbGame->insert('sazky', $data);

    	    $sazka_id = $dbGame->lastInsertId();
  	    
  	    }catch(Zend_Exception $e){

                	BetRadarLiveBetting::Error("Nepodarilo se insert sazky sazku live id: ". $event_id ."  ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
  	 	            $dbGame->rollBack();
  	 	            return false;
  	    }
  	    
  	  	$data = array();
  	    $data['aktualizace'] =  $date;
  	    $data['aktualizace_sazka'] =  $date;
  	    $data['event_id'] =  intval($event_id);
  	    $data['sazka_id'] =  intval($sazka_id);
  	    $data['betradar_sazka_id'] =  intval($odds['id']);
  	    $data['no_comb'] =  1;
   	    
  	    try{
  	 	 
  	    	$dbGame->insert('live_sazka', $data);

  	    }catch(Zend_Exception $e){

                	BetRadarLiveBetting::Error("Nepodarilo se updatovat live_sazky sazku id: ".$sazka_id." (".intval($odds['id']).") (".intval($event_id).") ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
  	 	            $dbGame->rollBack();
  	 	            return false;
  	    }
  	    
  	    $fce = BetRadarLiveBetting::$betType[$typ][$podtyp][5];
   	    self::$fce($dbGame,$odds,$sazka_id,$date); 
  	 	
  	 }
  	
  	 
  	 $dbGame->commit();
  	 
  }
  
    /**
 * Tato metoda zaklada/aktualizuje kurzy pro 1/2
 * @param object $dbGame  spojeni na databazi
 * @param object $odds  objekt kurzu
 * @param int $sazka_id  id sazky
 * @param string $date  akt datum
 * return array
 */
  public static function betWinner($dbGame,$odds,$sazka_id,$date){
  	   
  	

  	   	$select = $dbGame->select()->from(array('a'=>'sazka_kurz'),array('mporadi'=>'max(poradi)')) 
  			                                  ->where("a.sazka_id=?",$sazka_id);
        $stm  = $select->query();
        $row = $stm->fetchAll();
	   	     
        if(count($row) == 0) $poradi = 1; else $poradi = ($row[0]['mporadi']+1);
            
  	   	foreach($odds->OddsField as $oddsfield){
  	   	
  	     $data = array();
  	     $data['sazka_id'] =  $sazka_id;
  	     
  	     if($oddsfield['type'] == "1") $data['sloupec_id'] =  152;
  	     else if($oddsfield['type'] == "2") $data['sloupec_id'] =  153;
  
  	     $data['poradi'] =  $poradi;
   	     $data['kurz'] =  $oddsfield;
   	     $data['platny_od'] =  $date;
   	    
  	     try{
  	 	 
  	    	$dbGame->insert('sazka_kurz', $data);
  	    
  	     }catch(Zend_Exception $e){

                	BetRadarLiveBetting::Error("Nepodarilo se updatovat sazka_kurz sazku id: ".$sazka_id." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
  	 	            $dbGame->rollBack();
  	 	            return false;
  	     }
  	   	 
  	     
  	     
  	   	}

  	   
  	   return true;
  	   
  }
  
       /**
 * Tato metoda zaklada/aktualizuje kurzy pro sety  tenisPV2
 * @param object $dbGame  spojeni na databazi
 * @param object $odds  objekt kurzu
 * @param int $sazka_id  id sazky
 * @param string $date  akt datum
 * return array
 */
  public static function tenisPV2($dbGame,$odds,$sazka_id,$date){
  	   
  	

  	   	$select = $dbGame->select()->from(array('a'=>'sazka_kurz'),array('mporadi'=>'max(poradi)')) 
  			                                  ->where("a.sazka_id=?",$sazka_id);
        $stm  = $select->query();
        $row = $stm->fetchAll();
	   	     
        if(count($row) == 0) $poradi = 1; else $poradi = ($row[0]['mporadi']+1);
            
  	   	foreach($odds->OddsField as $oddsfield){
  	   	
  	     $data = array();
  	     $data['sazka_id'] =  $sazka_id;
  	     
  	     if($oddsfield['type'] == "2:0") $data['sloupec_id'] =  1781;
  	     else if($oddsfield['type'] == "2:1") $data['sloupec_id'] =  1782;
         else if($oddsfield['type'] == "0:2") $data['sloupec_id'] =  1784;
         else if($oddsfield['type'] == "1:2") $data['sloupec_id'] =  1783;
         
  	     $data['poradi'] =  $poradi;
   	     $data['kurz'] =  $oddsfield;
   	     $data['platny_od'] =  $date;
   	    
  	     try{
  	 	 
  	    	$dbGame->insert('sazka_kurz', $data);
  	    
  	     }catch(Zend_Exception $e){

                	BetRadarLiveBetting::Error("Nepodarilo se updatovat sazka_kurz sazku id: ".$sazka_id." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
  	 	            $dbGame->rollBack();
  	 	            return false;
  	     }
  	   	 
  	     
  	     
  	   	}

  	   
  	   return true;
  	   
  }
  
     /**
 * Tato metoda zaklada/aktualizuje kurzy pro sety  tenisPV3
 * @param object $dbGame  spojeni na databazi
 * @param object $odds  objekt kurzu
 * @param int $sazka_id  id sazky
 * @param string $date  akt datum
 * return array
 */
  public static function tenisPV3($dbGame,$odds,$sazka_id,$date){
  	   
  	

  	   	$select = $dbGame->select()->from(array('a'=>'sazka_kurz'),array('mporadi'=>'max(poradi)')) 
  			                                  ->where("a.sazka_id=?",$sazka_id);
        $stm  = $select->query();
        $row = $stm->fetchAll();
	   	     
        if(count($row) == 0) $poradi = 1; else $poradi = ($row[0]['mporadi']+1);
            
  	   	foreach($odds->OddsField as $oddsfield){
  	   	
  	     $data = array();
  	     $data['sazka_id'] =  $sazka_id;
  	     
  	     if($oddsfield['type'] == "3:0") $data['sloupec_id'] =  1785;
  	     else if($oddsfield['type'] == "3:1") $data['sloupec_id'] =  1786;
         else if($oddsfield['type'] == "3:2") $data['sloupec_id'] =  1787;
         else if($oddsfield['type'] == "0:3") $data['sloupec_id'] =  1790;
         else if($oddsfield['type'] == "1:3") $data['sloupec_id'] =  1789;
         else if($oddsfield['type'] == "2:3") $data['sloupec_id'] =  1788;
         
  	     $data['poradi'] =  $poradi;
   	     $data['kurz'] =  $oddsfield;
   	     $data['platny_od'] =  $date;
   	    
  	     try{
  	 	 
  	    	$dbGame->insert('sazka_kurz', $data);
  	    
  	     }catch(Zend_Exception $e){

                	BetRadarLiveBetting::Error("Nepodarilo se updatovat sazka_kurz sazku id: ".$sazka_id." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
  	 	            $dbGame->rollBack();
  	 	            return false;
  	     }
  	   	 
  	     
  	     
  	   	}

  	   
  	   return true;
  	   
  }
  
   /**
 * Tato metoda zaklada/aktualizuje kurzy pro vice/mene kde posilaji 1 2 
 * @param object $dbGame  spojeni na databazi
 * @param object $odds  objekt kurzu
 * @param int $sazka_id  id sazky
 * @param string $date  akt datum
 * return array
 */
  public static function betTotalWinner($dbGame,$odds,$sazka_id,$date){
  	   
  	

  	   	$select = $dbGame->select()->from(array('a'=>'sazka_kurz'),array('mporadi'=>'max(poradi)')) 
  			                                  ->where("a.sazka_id=?",$sazka_id);
        $stm  = $select->query();
        $row = $stm->fetchAll();
	   	     
        if(count($row) == 0) $poradi = 1; else $poradi = ($row[0]['mporadi']+1);
            
  	   	foreach($odds->OddsField as $oddsfield){
  	   	
  	     $data = array();
  	     $data['sazka_id'] =  $sazka_id;
  	     
  	     if($oddsfield['type'] == "2") $data['sloupec_id'] =  144;
  	     else if($oddsfield['type'] == "1") $data['sloupec_id'] =  145;
  
  	     $data['poradi'] =  $poradi;
   	     $data['kurz'] =  $oddsfield;
   	     $data['platny_od'] =  $date;
   	    
  	     try{
  	 	 
  	    	$dbGame->insert('sazka_kurz', $data);
  	    
  	     }catch(Zend_Exception $e){

                	BetRadarLiveBetting::Error("Nepodarilo se updatovat sazka_kurz sazku id: ".$sazka_id." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
  	 	            $dbGame->rollBack();
  	 	            return false;
  	     }
  	   	 
  	     
  	     
  	   	}

  	   
  	   return true;
  	   
  }
  
  /**
 * Tato metoda zaklada/aktualizuje kurzy pro vice/mene
 * @param object $dbGame  spojeni na databazi
 * @param object $odds  objekt kurzu
 * @param int $sazka_id  id sazky
 * @param string $date  akt datum
 * return array
 */
  public static function betTotal($dbGame,$odds,$sazka_id,$date){
  	   
  	

  	   	$select = $dbGame->select()->from(array('a'=>'sazka_kurz'),array('mporadi'=>'max(poradi)')) 
  			                                  ->where("a.sazka_id=?",$sazka_id);
        $stm  = $select->query();
        $row = $stm->fetchAll();
	   	     
        if(count($row) == 0) $poradi = 1; else $poradi = ($row[0]['mporadi']+1);
            
  	   	foreach($odds->OddsField as $oddsfield){
  	   	
  	     $data = array();
  	     $data['sazka_id'] =  $sazka_id;
  	     
  	     if($oddsfield['type'] == "o") $data['sloupec_id'] =  144;
  	     else if($oddsfield['type'] == "u") $data['sloupec_id'] =  145;
  
  	     $data['poradi'] =  $poradi;
   	     $data['kurz'] =  $oddsfield;
   	     $data['platny_od'] =  $date;
   	    
  	     try{
  	 	 
  	    	$dbGame->insert('sazka_kurz', $data);
  	    
  	     }catch(Zend_Exception $e){

                	BetRadarLiveBetting::Error("Nepodarilo se updatovat sazka_kurz sazku id: ".$sazka_id." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
  	 	            $dbGame->rollBack();
  	 	            return false;
  	     }
  	   	 
  	     
  	     
  	   	}

  	   
  	   return true;
  	   
  }
  
  /**
 * Tato metoda zaklada/aktualizuje kurzy pro handicap
 * @param object $dbGame  spojeni na databazi
 * @param object $odds  objekt kurzu
 * @param int $sazka_id  id sazky
 * @param string $date  akt datum
 * return array
 */
  public static function betHandicap($dbGame,$odds,$sazka_id,$date){
  	   
  	

  	   	$select = $dbGame->select()->from(array('a'=>'sazka_kurz'),array('mporadi'=>'max(poradi)')) 
  			                                  ->where("a.sazka_id=?",$sazka_id);
        $stm  = $select->query();
        $row = $stm->fetchAll();
	   	     
        if(count($row) == 0) $poradi = 1; else $poradi = ($row[0]['mporadi']+1);
            
  	   	foreach($odds->OddsField as $oddsfield){
  	   	
  	     $data = array();
  	     $data['sazka_id'] =  $sazka_id;
  	     
  	     if($oddsfield['type'] == "1") $data['sloupec_id'] =  138;
  	     else if($oddsfield['type'] == "x") $data['sloupec_id'] =  139;
  	     else if($oddsfield['type'] == "2") $data['sloupec_id'] =  140;
  	     $data['poradi'] =  $poradi;
   	     $data['kurz'] =  $oddsfield;
   	     $data['platny_od'] =  $date;
   	    
  	     try{
  	 	 
  	    	$dbGame->insert('sazka_kurz', $data);
  	    
  	     }catch(Zend_Exception $e){

                	BetRadarLiveBetting::Error("Nepodarilo se updatovat sazka_kurz sazku id: ".$sazka_id." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
  	 	            $dbGame->rollBack();
  	 	            return false;
  	     }
  	   	 
  	     
  	     
  	   	}

  	   
  	   return true;
  	   
  }
  
  /**
 * Tato metoda zaklada/aktualizuje kurzy pro zapas 1x2
 * @param object $dbGame  spojeni na databazi
 * @param object $odds  objekt kurzu
 * @param int $sazka_id  id sazky
 * @param string $date  akt datum
 * return array
 */
  public static function bet3way($dbGame,$odds,$sazka_id,$date){
  	   
  
  	   	$select = $dbGame->select()->from(array('a'=>'sazka_kurz'),array('mporadi'=>'max(poradi)')) 
  			                                  ->where("a.sazka_id=?",$sazka_id);
        $stm  = $select->query();
        $row = $stm->fetchAll();
	   	     
        if(count($row) == 0) $poradi = 1; else $poradi = ($row[0]['mporadi']+1);
            
  	   	foreach($odds->OddsField as $oddsfield){
  	   	
  	     $data = array();
  	     $data['sazka_id'] =  $sazka_id;
  	     
  	     if($oddsfield['type'] == "1") $data['sloupec_id'] =  138;
  	     else if($oddsfield['type'] == "x") $data['sloupec_id'] =  139;
  	     else if($oddsfield['type'] == "2") $data['sloupec_id'] =  140;
  	     $data['poradi'] =  $poradi;
   	     $data['kurz'] =  $oddsfield;
   	     $data['platny_od'] =  $date;
   	    
  	     try{
  	 	 
  	    	$dbGame->insert('sazka_kurz', $data);
  	    
  	     }catch(Zend_Exception $e){

                	BetRadarLiveBetting::Error("Nepodarilo se updatovat sazka_kurz sazku id: ".$sazka_id." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
  	 	            $dbGame->rollBack();
  	 	            return false;
  	     }
  	   	 
  	     
  	     
  	   	}

  	   
  	   return true;
  	   
  }
  
  
/**
 * Tato metoda vraci  vsechny sazky vyjma tech ktere nezacali
 * @param object $dbGame  spojeni na databazi
 * return array
 */
  public static function getBetStartedLive($dbGame){
  	
  	$select = $dbGame->select()->from(array('a' => 'live_betradar_event'),array('a.betradar_event_id','l.betradar_sazka_id','l.aktualizace','l.sazka_id','l.typ_id','l.podtyp_id','l.risk_limit','l.close','l.risk_limit_balance','l.status','l.platna_od','l.platna_do'))
  		                                 ->join(array('l' => 'live'),'a.event_id = l.event_id')
  		                                 ->where('l.stav <> ?', 2)
  		                                 ->where('l.status = ?', 0)
  		                                 ->where('l.betradar_sazka_id <> ?', 0);
  		                              
  	 $stm    = $select->query();

  	 return $stm->fetchAll();
  	
  }
  
/**
 * Tato metoda vraci  vsechny  sazky ktere bezi
 * @param object $dbGame  spojeni na databazi
 * return array
 */
  public static function getBetRunLive($dbGame){
  	
  	$select = $dbGame->select()->from(array('a' => 'live_betradar_event'),array('a.betradar_event_id','l.betradar_sazka_id','l.sazka_id','l.typ_id','l.podtyp_id','l.risk_limit','l.close','l.risk_limit_balance','l.status','l.platna_od','l.platna_do'))
  		                                 ->join(array('l' => 'live'),'a.event_id = l.event_id')
  		                                 ->where('l.status <> ?', LIVE_END)
  		                                 ->where('l.status <> ?', LIVE_UNFINISHED)
  		                                 ->where('l.status <> ?', LIVE_NOT_STARTED)
  		                                 ->where('l.betradar_sazka_id <> ?', 0);
  	 $stm    = $select->query();

  	 return $stm->fetchAll();
  	
  }
  
/**
 * Tato metoda vraci sazky ktere jsou platne u bezicich live sazek
 * @param object $dbGame  spojeni na databazi
 * return array
 */
  public static function getRunBet($dbGame){
  	
  	$select = $dbGame->select()->from(array('a' => 'live_betradar_event'),array('l.sazka_id'))
  		                                 ->join(array('l' => 'live_sazka'),'a.event_id = l.event_id')
  		                                 ->join(array('s' => 'sazky'),'l.sazka_id = s.sazka_id')
  		                                 ->where('s.status = ?', 0)
  		                                 ->where('platna_od > ?', It6_Date::dbNow())
  		                                 ;
  	 $stm    = $select->query();

  	 return $stm->fetchAll();
  	
  }
  
/**
 * Tato metoda vraci sazky ktere jsou pozatavene u bezicich live sazek
 * @param object $dbGame  spojeni na databazi
 * return array
 */
  public static function getStopBet($dbGame){
  	
  	$select = $dbGame->select()->from(array('a' => 'live_betradar_event'),array('l.sazka_id'))
  		                                 ->join(array('l' => 'live_sazka'),'a.event_id = l.event_id')
  		                                 ->join(array('s' => 'sazky'),'l.sazka_id = s.sazka_id')
  		                                 ->where('s.status = ?', 2)
  		                                 ->where('platna_od > ?', It6_Date::dbNow())
  		                                 ;
  	 $stm    = $select->query();

  	 return $stm->fetchAll();
  	
  }
  
/**
 * Tato metoda se snazi zalogovat
 * @param object $dbGame  spojeni na databazi
 * @param int $sazka_id  id sazky
 * return bool
 */
  public static function stopBet($dbGame,$sazka_id){
  	
  	
  	$select = $dbGame->select()->from(array('a' => 'live_sazka'),array('a.no_update'))
     		                                 ->where("a.sazka_id=?",$sazka_id);
    $stm  = $select->query();
  	$row = $stm->fetchAll();
  	       
  
    foreach($row as $h){if($h['no_update'] == 1) return;}
    	    
  	$data = array(
             'status'      => 2
    );
    
    
    $rows_affected = 0;
    
    try{
    	
    	  $where = array();
    	  $where[] = 'sazka_id='.intval($sazka_id);
    	  $where[] = 'status=0';
    	  $where[] = "platna_do > '". It6_Date::dbNow()."'";

    	  
  	      $rows_affected = $dbGame->update('sazky', $data,$where);

    }catch(Zend_Exception $e){
  	 	
  	 	   BetRadarLiveBetting::Error("Database error: " . $e->getMessage()  . " " .__LINE__ . "\n",'Live bet / error');
  	 	
  	}
  	if($rows_affected == 0) return false;
  	
  	return true;
  	
  }
	
/**
 * Tato metoda ozivuje live sazku
 * @param object $dbGame  spojeni na databazi
 * @param int $sazka_id  id sazky
 * return bool
 */
  public static function runBet($dbGame,$sazka_id){
  	
  	$select = $dbGame->select()->from(array('a' => 'live_sazka'),array('a.no_update'))
     		                                 ->where("a.sazka_id=?",$sazka_id);
    $stm  = $select->query();
  	$row = $stm->fetchAll();
  	       
  
    foreach($row as $h){if($h['no_update'] == 1) return;}
    
  	$data = array(
             'status'      => 0
    );
    
    $rows_affected = 0;
    
    try{
    	 
    	  $where = array();
    	  $where[] = 'sazka_id='.intval($sazka_id);
    	  $where[] = 'status=2';
    	  $where[] = "platna_do > '". It6_Date::dbNow()."'";
  	      $rows_affected = $dbGame->update('sazky', $data,$where);
  	      
    }catch(Zend_Exception $e){
  	 	
  	 	   BetRadarLiveBetting::Error("Database error: " . $e->getMessage()  . " " .__LINE__ . "\n",'Live bet / error');
  	 	
  	}
  	
  	if($rows_affected == 0) return false;
  	
  	return true;
  	
  }
  
}