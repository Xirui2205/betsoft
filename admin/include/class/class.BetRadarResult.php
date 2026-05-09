<?php
/**
 * @package    livebetradar
 */


 
class BetRadarResult{
	

    /**
 * Tato metoda stanovuje vysledek pro  1/2
 * @param object $dbGame  spojeni na databazi
 * @param object $odds  objekt kurzu
 * @param int $sazka_id  id sazky
 * @param int $time  cas kdy se stalo
 * return bool
 */
  public static function betWinnerResult($dbGame,$odds,$sazka_id,$time){
  	   
  	$result = '';
  	
  	foreach($odds->OddsField as $oddsfield){
  		
  	  if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == '1') {$result = '152';break;}
  	  else if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == '2') {$result = '153';break;}
  		
  	}
    
  	$data = array();
  	$data['status'] = 3; 
  	$data['vysledek'] = $result;
  	$data['platna_do'] = date("Y-m-d H:i:s",$time);
  	
  	try{
  		
  	    $dbGame->update('sazky', $data,'sazka_id='.$sazka_id);
  	
    }catch (Exception $e) {

        BetRadarLiveBetting::Error("Nepodarilo se aktualizovat vysledek  sazky: #".$sazka_id."; ".$e->getMessage().__LINE__,'Live bet / error');
        return false;
                  
    }
                        
  	return true;   
  	
  }
  
    
      /**
 * Tato metoda stanovuje vysledek pro  tenisPV3
 * @param object $dbGame  spojeni na databazi
 * @param object $odds  objekt kurzu
 * @param int $sazka_id  id sazky
 * @param int $time  cas kdy se stalo
 * return bool
 */
  public static function tenisPV3Result($dbGame,$odds,$sazka_id,$time){
  	   
  	$result = '';
  	
  	foreach($odds->OddsField as $oddsfield){
         
  	  if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == '3:0') {$result = '1785';break;}
  	  else if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == '3:1') {$result = '1786';break;}
  	  else if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == '3:2') {$result = '1787';break;}
  	  else if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == '0:3') {$result = '1790';break;}
      else if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == '1:3') {$result = '1789';break;}
  	  else if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == '2:3') {$result = '1788';break;}	
  	  	
  	}
    
  	$data = array();
  	$data['status'] = 3; 
  	$data['vysledek'] = $result;
  	$data['platna_do'] = date("Y-m-d H:i:s",$time);
  	
  	try{
  		
  	    $dbGame->update('sazky', $data,'sazka_id='.$sazka_id);
  	
    }catch (Exception $e) {

        BetRadarLiveBetting::Error("Nepodarilo se aktualizovat vysledek  sazky: #".$sazka_id."; ".$e->getMessage().__LINE__,'Live bet / error');
        return false;
                  
    }
                        
  	return true;   
  	
  }
  
      /**
 * Tato metoda stanovuje vysledek pro  tenisPV2
 * @param object $dbGame  spojeni na databazi
 * @param object $odds  objekt kurzu
 * @param int $sazka_id  id sazky
 * @param int $time  cas kdy se stalo
 * return bool
 */
  public static function tenisPV2Result($dbGame,$odds,$sazka_id,$time){
  	   
  	$result = '';
  	
  	foreach($odds->OddsField as $oddsfield){
  		
         
  	  if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == '2:0') {$result = '1781';break;}
  	  else if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == '2:1') {$result = '1782';break;}
  	  else if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == '0:2') {$result = '1784';break;}
  	  else if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == '1:2') {$result = '1783';break;}
  		
  	}
    
  	$data = array();
  	$data['status'] = 3; 
  	$data['vysledek'] = $result;
  	$data['platna_do'] = date("Y-m-d H:i:s",$time);
  	
  	try{
  		
  	    $dbGame->update('sazky', $data,'sazka_id='.$sazka_id);
  	
    }catch (Exception $e) {

        BetRadarLiveBetting::Error("Nepodarilo se aktualizovat vysledek  sazky: #".$sazka_id."; ".$e->getMessage().__LINE__,'Live bet / error');
        return false;
                  
    }
                        
  	return true;   
  	
  }
  
    /**
 * Tato metoda stanovuje vysledek pro  vice/mene kde posilaji 1 2 
 * @param object $dbGame  spojeni na databazi
 * @param object $odds  objekt kurzu
 * @param int $sazka_id  id sazky
 * @param int $time  cas kdy se stalo
 * return bool
 */
  public static function betTotalWinnerResult($dbGame,$odds,$sazka_id,$time){
  	   
  	$result = '';
  	
  	foreach($odds->OddsField as $oddsfield){
  		
  	  if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == '2') {$result = '144';break;}
  	  else if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == '1') {$result = '145';break;}
  		
  	}
    
  	$data = array();
  	$data['status'] = 3; 
  	$data['vysledek'] = $result;
  	$data['platna_do'] = date("Y-m-d H:i:s",$time);
  	
  	try{
  		
  	    $dbGame->update('sazky', $data,'sazka_id='.$sazka_id);
  	
    }catch (Exception $e) {

        BetRadarLiveBetting::Error("Nepodarilo se aktualizovat vysledek  sazky: #".$sazka_id."; ".$e->getMessage().__LINE__,'Live bet / error');
        return false;
                  
    }
                        
  	return true;   
  	
  }
  
  /**
 * Tato metoda stanovuje vysledek pro  vice/mene
 * @param object $dbGame  spojeni na databazi
 * @param object $odds  objekt kurzu
 * @param int $sazka_id  id sazky
 * @param int $time  cas kdy se stalo
 * return bool
 */
  public static function betTotalResult($dbGame,$odds,$sazka_id,$time){
  	   
  	$result = '';
  	
  	foreach($odds->OddsField as $oddsfield){
  		
  	  if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == 'o') {$result = '144';break;}
  	  else if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == 'u') {$result = '145';break;}
  		
  	}
    
  	$data = array();
  	$data['status'] = 3; 
  	$data['vysledek'] = $result;
  	$data['platna_do'] = date("Y-m-d H:i:s",$time);
  	
  	try{
  		
  	    $dbGame->update('sazky', $data,'sazka_id='.$sazka_id);
  	
    }catch (Exception $e) {

        BetRadarLiveBetting::Error("Nepodarilo se aktualizovat vysledek  sazky: #".$sazka_id."; ".$e->getMessage().__LINE__,'Live bet / error');
        return false;
                  
    }
                        
  	return true;   
  	
  }
  
  
  /**
 * Tato metoda stanovuje vysledek pro  handicap
 * @param object $dbGame  spojeni na databazi
 * @param object $odds  objekt kurzu
 * @param int $sazka_id  id sazky
 * @param int $time  cas kdy se stalo
 * return bool
 */
  public static function betHandicapResult($dbGame,$odds,$sazka_id,$time){
  	   
  	$result = '';
  	
  	foreach($odds->OddsField as $oddsfield){
  		
  	  if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == '1') {$result = '138';break;}
  	  else if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == 'x') {$result = '139';break;}
  	  else if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == '2') {$result = '140';break;}
  		
  	}
    
  	$data = array();
  	$data['status'] = 3; 
  	$data['vysledek'] = $result;
  	$data['platna_do'] = date("Y-m-d H:i:s",$time);
  	
  	try{
  		
  	    $dbGame->update('sazky', $data,'sazka_id='.$sazka_id);
  	
    }catch (Exception $e) {

        BetRadarLiveBetting::Error("Nepodarilo se aktualizovat vysledek  sazky: #".$sazka_id."; ".$e->getMessage().__LINE__,'Live bet / error');
        return false;
                  
    }
                        
  	return true;   
  	
  }
  
  
  /**
 * Tato metoda stanovuje vysledek pro zapas 1x2
 * @param object $dbGame  spojeni na databazi
 * @param object $odds  objekt kurzu
 * @param int $sazka_id  id sazky
 * @param int $time  cas kdy se stalo
 * return bool
 */
  public static function bet3wayResult($dbGame,$odds,$sazka_id,$time){
  	   
  	$result = '';
  	
  	foreach($odds->OddsField as $oddsfield){
  		
  	  if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == '1') {$result = '138';break;}
  	  else if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == 'x') {$result = '139';break;}
  	  else if(intval($oddsfield['outcome']) == 1 && $oddsfield['type'] == '2') {$result = '140';break;}
  		
  	}
    
  	$data = array();
  	$data['status'] = 3; 
  	$data['vysledek'] = $result;
  	$data['platna_do'] = date("Y-m-d H:i:s",$time);
  	
  	try{
  		
  	    $dbGame->update('sazky', $data,'sazka_id='.$sazka_id);
  	
    }catch (Exception $e) {

        BetRadarLiveBetting::Error("Nepodarilo se aktualizovat vysledek  sazky: #".$sazka_id."; ".$e->getMessage().__LINE__,'Live bet / error');
        return false;
                  
    }
                        
  	return true;   
  	
  }
  
  
  /**
 * Tato metoda resetuje vysledek
 * @param object $dbGame  spojeni na databazi
 * @param int $sazka_id  id sazky
 * return bool
 */
  public static function betRollback($dbGame,$sazka_id){
  	   
  	  	
    $data = array();
  	$data['status'] = 0; 
  	$data['vysledek'] = '';
  	
  	try{
  		
  	    $dbGame->update('sazky', $data,'sazka_id='.$sazka_id);
  	
    }catch (Exception $e) {

        BetRadarLiveBetting::Error("Nepodarilo se aktualizovat rollback vysledek sazky: #".$sazka_id."; ".$e->getMessage().__LINE__,'Live bet / error');
        return false;
                  
    }

  	return true; 
  }
  
}