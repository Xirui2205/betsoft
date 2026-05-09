<?php
/**
 * @package    ciselnik
 */


/**
 * Trida pokud neni definován žádný číselník
 *
 * 
 * <code>
 * 
 * </code>
 *
 * @package    Pomocne
 */
class CiselnikNenalezen extends Ciselnik{
  
  public function __construct(){
   
   parent::__construct();
   
   $this->runAction();
   
  }
  
  /**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
  protected function runAction(){
   
   $this->colNum = 1;
   
   $this->HeadFoot();
   
   $this->vrat = "<tr><td><strong>Tento číselník neexistuje</strong></td></tr>";
   
   	$this->dbGame->disconnect();
	
  }
  
 /**
 * Vraci vystup do tridy main
 * @return string
 */
  public function ShowResult(){
  
    return $this->FillTable();
	
  }
  
}

?>