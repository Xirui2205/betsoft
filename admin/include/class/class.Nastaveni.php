<?php
/**
 * @package    ciselnik
 */

/**
 * Trida pro praci s dalsim nastaveni
 *
 * 
 * <code>
 * 
 * </code>
 *
 * @package    Main
 */
 
class Nastaveni extends Template{

/**
 * navratova hodnota
 * @access private
 * @var string
 */
private  $vrat = "";

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
* @param PEAR::DB $db objekt spojeni s databazi
* @param PEAR::DB $dbGame objekt spojeni s databazi
*/
  public function __construct($section=0,$db=null,$dbGame=null){



  }
  
/**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
  public function runAction(){
   

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