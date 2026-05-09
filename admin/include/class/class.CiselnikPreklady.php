<?php
/**
 * @package    Jazyky
 */


class CiselnikPreklady extends Ciselnik{
  
  /**
 * text ve fulltextovem vyhledavani
 * @access private
 * @var string
 */
private  $fulltext = "";
  
  public function __construct(){
   
   parent::__construct();
   
   $this->runAction();
   
  }
  
 /**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
  protected function runAction(){
    
	$this->colNum = 2;
	
	$this->fulltext = (isset($_REQUEST['fulltext'])?$_REQUEST['fulltext']:"");
	
	$this->headLine = "Číselník překlady";
	
	$this->opener = (isset($_GET['obj'])?trim($_GET['obj']):"");
	
	$sql = "select index_pole from preklady ".(mb_strlen($this->fulltext) > 0?"where index_pole like '%".Help::Slash($this->fulltext)."%' or text like '%".Help::Slash($this->fulltext)."%' or short_text like '%".Help::Slash($this->fulltext)."%'":"")." group by index_pole";
    $res2 =& $this->dbGame->query($sql);
    if(DB::isError($res2)) throw new ExHandler($res->getMessage(),"admin_ex_db");
	
	$this->page = new Page($res2->numRows(),10,"width=".$_GET['width']."&cis_id=".$_GET['cis_id']."&fulltext=".($this->fulltext)."&obj=".$this->opener);
	
	$this->HeadFoot();
	
	$sql = "select index_pole from preklady ".(mb_strlen($this->fulltext) > 0?"where index_pole like '%".Help::Slash($this->fulltext)."%' or text like '%".Help::Slash($this->fulltext)."%' or short_text like '%".Help::Slash($this->fulltext)."%'":"")." group by index_pole LIMIT ".($this->page->page*10).", 10";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");
	
	$this->vrat .= '<tr><td colspan="2"><strong>Fulltext:</strong> <input type="text" class="sinput" name="fulltext" /> <input class="sinput2" type="submit" value="Vyhledat" /></td></tr>';
	
	$this->vrat .= '';
	
	$x = 1;
	
	while ($row =& $res->fetchRow()){
	   
	    $this->vrat .= '<tr >
		                <td width="25%">'.($x+(10*$this->page->page)).'</td>
		                <td><a href="javascript:window.opener.document.getElementById(\''.$this->opener.'\').value=\''.Help::Script($row['index_pole']).'\';self.close();void(0);">'.Help::Html($row['index_pole']).'</a></td></tr>';  
		$x++;  
	 }
	
	$this->dbGame->disconnect();

  }
  
 /**
 * Vraci vystup do tridy main
 * @return string
 */
  public function ShowResult(){
  
    return "<form method=\"post\">".$this->FillTable()."</form>";
	
  }
  
}

?>