<?php
/**
 * @package    cislenik
 */

/**
 * Trida pro praci s cisleniky
 *
 * 
 * <code>
 * 
 * </code>
 *
 * @package    Pomocne
 */
 


abstract class Ciselnik{

/**
 * navratova hodnota
 * @access protected
 * @var string
 */
protected $vrat = "";

/**
 * hlavicka cisleniku
 * @access protected
 * @var string
 */
protected $head;

/**
 * paticka cisleniku
 * @access protected
 * @var string
 */
protected $foot;

/**
 * Nadpis ciselniku
 * @access protected
 * @var string
 */
protected $headLine = "";

/**
 * prvek do ktereho se ma zapsat
 * @access protected
 * @var string
 */
protected $opener = "";

/**
 * pocet sloupcu
 * @access protected
 * @var int
 */
protected $colNum;

/**
 * objekt stránkování
 * @access protected
 * @var Page
 */
protected $page;

/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */              
protected  $dbGame;

protected function __construct(){
 
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
 * Vraci vystup do tridy main
 * @return string
 */
  abstract public function ShowResult();

 /**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
  abstract protected function RunAction();


 /**
 * metoda pripravi hlavicku a paticku dokumentu
 * @return void
 */
  protected function HeadFoot(){
   
   $tr = "";
   
   if(is_object($this->page)){
    
    $tr = "<tr>";
    if($this->colNum > 1)
	    $tr .= "<td>Záznamy: <strong>".$this->page->numRows."</td><td colspan=\"".($this->colNum-1)."\" class=\"textright\">".$this->page->getPage()."</td>";
	else 
	    $tr .= "<td class=\"textright\">Záznamy: <strong>".$this->page->numRows." - ".$this->page->getPage()."</td>";
	$tr .= "</tr>";
	
   }
  
   $this->head = "<table class='table-list' style=\"width:".$_GET['width']."px;\"><tr><td colspan=\"".$this->colNum."\"><strong>".$this->headLine."</strong></td></tr>".$tr;
   
  
   
   $this->foot = $tr."</table>";
   
  }
  
  /**
 * metoda vraci slozeny vysledek
 * @return string
 */
  protected function FillTable(){
  
   return $this->head.$this->vrat.$this->foot;
   
  }
  
}

?>
