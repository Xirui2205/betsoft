<?php
/**
 * @link       help
 
 
/** 
*Trida pro strankovani
*
*vytvari strankovaci listy
*   
*  Pr:
*  
*  $page = new Page($res2->numRows(),PAGE,"section=".$this->section);
*  $page->getPage(); //vysledek
*/


class Page{

private $listy;
private $str = "";
private $query = "";
public $page = 0;
public $numRows = 0;

/**
*param1 numrows: pocet zaznamu celkem
*param2 p: pocet vypisu na stranku
*param3 query: retezec k url
*/
  public function __construct($numrows,$p,$query){
    
	$this->page = (isset($_GET['page']) && intval($_GET['page']) >= 0 && is_numeric($_GET['page'])?$_GET['page']:0);
    $this->listy = floor((is_float($numrows/$p)?($numrows/$p):(($numrows/$p)-1)));
	$this->query = $query;
	$this->numRows = $numrows;
	
	$this->findNumbers();
	
    $this->findNextPage();
	
  }

  /*vytvori cisla stranek*/
  private function findNumbers(){
    
	define("NUMPAGE",5);
	
	 if(0 == $this->page) $this->str .= " <span style=\"font-weight:bold\">1</span>";
	 else                  $this->str .= " <a href=\"?".$this->query."&page=0\">1</a>";
	 
	  if($this->listy > 0)  $this->str .= "....";
	 
	 $y = 0;
	 
	 if(0 == $this->page) $y = 1;
	 else if($this->listy == $this->page) $y = $this->listy - NUMPAGE;
	 else if(($this->listy-2) >= (floor(NUMPAGE/2)+1) && ($this->page+floor(NUMPAGE/2)) >= $this->listy) $y = $this->page - (NUMPAGE-($this->listy-$this->page));
	 else $y = $this->page - floor(NUMPAGE/2);
	 
	 if($y < 1) $y = 1;
	 
	 for($x=0;$x<NUMPAGE;$x++,$y++){
	  
	  if($y < $this->listy){
	   if($y == $this->page) $this->str .= " <span style=\"font-weight:bold\">".($y+1)."</span> ";
	   else            $this->str .= " <a href=\"?".$this->query."&page=".($y)."\">".($y+1)."</a> ";
	  }
	  
	 }
	 
	 if($this->listy > 0){
	  if($this->listy == $this->page) $this->str .= "....<span style=\"font-weight:bold\">".($this->listy+1)."</span> ";
	  else                  $this->str .= "....<a href=\"?".$this->query."&page=".$this->listy."\">".($this->listy+1)."</a> ";
	 }
	 
  }
  
  /*spoji dalsi a predchozi plus cisla*/
  private function findNextPage(){
  
    $this->str = ($this->page>0?"<a href=\"?".$this->query."&page=".($this->page-1)."\" style=\"text-decoration:none\"><strong>&lt;</strong></a>":"")." ".$this->str." ".($this->page<$this->listy?"<a href=\"?".$this->query."&page=".($this->page+1)."\" style=\"text-decoration:none\"><strong>&gt;</strong></a>":"");
  
  }
  
  /*vrati vysledny retezec*/
  public function getPage(){
  
    return $this->str;
  
  }
  
}

?>