<?php
/**
 * @package    book
 */

/**
 * Sazky
 *
 *
 * <code>
 *
 * </code>
 *
 * @package    Ciselniky
 */

class Sazky extends Template{

private $controller = null;
/**
 * navratova hodnota
 * @access private
 * @var string
 */
private  $vrat = "";



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
*/
  public function __construct($section=0, $controller = null){
    $this->section =  $section;
    $this->controller = $controller;
  }

/**
 * metoda zavola prislusne tridy podle zvolene akce
 * @param int $action co se bude se sazkami delat  1=vytvoreni
 * @return void
 */
  public function runAction(){

  }

 /**
 * pretizeni volani akci pro jednotlive sazky
 * @param string $trida jmeno tridy
 * @param array $param parametry tridy
 * @return void
 */
  function __call($trida,$param){

	if($trida == "VytvorSazku"){ // metoda zavola tridu pro vytvoreni sazky

	   $ob = new SazkaVytvor($this->section);
	   $ob->runAction();
       $this->vrat = $ob->getContent();

	}

	else if($trida == "ZobrazSazku"){ // metoda zavola tridu pro vytvoreni sazky

	  $ob = new SazkaZobraz($this->section);
	  $ob->runAction();
		$this->vrat = $ob->getContent();

	}

	else if($trida == "ZobrazTicket"){ // metoda zavola tridu pro vytvoreni sazky

	   $ob = new SazkaTicket($this->section, $this->controller);
	   $ob->runAction();
       $this->vrat = $ob->getContent();

	}

	else if($trida == "Proplaceni"){ // metoda zavola tridu pro vytvoreni sazky

	   $ob = new ProplatitTicket($this->section);
	   $ob->runAction();
       $this->vrat = $ob->getContent();

	}

  }


  /**
 * Nastaveni prav k sekci
 * @param int $update pravo zapisu
 * @param int $delete pravo smazani
 * @return void
 */
  public function setPrivileges($update,$delete){



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
