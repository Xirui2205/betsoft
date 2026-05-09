<?php
/**
 * @package    ciselnik
 */


/**
 * Trida pro praci s ciselnikem tymu
 *
 * 
 * <code>
 * 
 * </code>
 *
 * @package    Pomocne
 */
class CiselnikTymy extends Ciselnik{
  
  /**
 * text ve fulltextovem vyhledavani
 * @access private
 * @var string
 */
private  $fulltext = "";
  
  public function __construct(){
   
   parent::__construct();
   $this->dbGame = DbUtil::connectWebDb();
   //var_dump($this->dbGame);exit;
   $this->runAction();
   
  }
  
 /**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
  protected function runAction(){
    
	$this->colNum = 2;
	$this->itemsPerPage = 30;
	
	$this->fulltext = (isset($_REQUEST['fulltext'])?$_REQUEST['fulltext']:"");
	
	$this->headLine = "Číselník týmů";
	
	$this->opener = (isset($_GET['obj'])?trim($_GET['obj']):"");
	
//	$sql = "select index_pole from preklady where ".(mb_strlen($this->fulltext) > 0?"(index_pole like '%".Help::Slash($this->fulltext)."%' or text like '%".Help::Slash($this->fulltext)."%' or short_text like '%".Help::Slash($this->fulltext)."%') and ":"")." index_pole like 'team_%' group by index_pole";
	$sql = "SELECT id as index_pole, name as text FROM team WHERE name LIKE '%".Help::Slash($this->fulltext)."%'";
    $res2 =& $this->dbGame->query($sql);
    if(DB::isError($res2)) throw new ExHandler($res2->getMessage(),"admin_ex_db");
	
	$this->page = new Page($res2->numRows(),$this->itemsPerPage,"width=".$_GET['width']."&cis_id=".$_GET['cis_id']."&fulltext=".($this->fulltext)."&obj=".$this->opener);
	
	$this->HeadFoot();
	
	//$sql = "select index_pole,text from preklady where ".(mb_strlen($this->fulltext) > 0?"(index_pole like '%".Help::Slash($this->fulltext)."%' or text like '%".Help::Slash($this->fulltext)."%' or short_text like '%".Help::Slash($this->fulltext)."%') and":"")." index_pole like 'team_%' and lang_id=".CZ_LANG_ID." group by index_pole LIMIT ".($this->page->page*10).", 10";
	
	//FIXME add table myIsam
//	$sql = "SELECT id as index_pole, name as text FROM team WHERE MATCH(name) AGAINST( '".$this->fulltext."*' IN BOOLEAN MODE)";
	/*$sql = "SELECT id as index_pole, name as text FROM team WHERE name LIKE '%".Help::Slash($this->fulltext)."%'";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");*/

	$sql = "SELECT id as index_pole, name as text FROM team WHERE name LIKE '%".Help::Slash($this->fulltext)."%'  LIMIT ".($this->page->page*$this->itemsPerPage).", ".$this->itemsPerPage;
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");
    
	$this->vrat .= '<tr>
						<td colspan="2"><strong>Fulltext:</strong>
							<input type="text" class="sinput" name="fulltext" /> <input class="sinput2" type="submit" value="Vyhledat" />
						</td>
					</tr>';
	
	$this->vrat .= '<tr><th class="textleft">ID</th><th class="textleft">Tým</th></tr>';
	
	$x = 1;
	
	while ($row =& $res->fetchRow()) {
		$this->vrat .= '<tr class="clickable">
		                <td width="25%">'.($x+($this->itemsPerPage*$this->page->page)).'</td>
		                <td><span';
		if ($this->opener=='domaci' || $this->opener=='hoste') {
			$this->vrat .= ' onclick="
		                javascript:window.opener.document.getElementById(\''.$this->opener.'\').value+=\''.Help::Script($row['text']).'\';
		                { self.close(); void(0); } "';
		} else {
			$this->vrat .= ' onclick="
		                javascript:window.opener.document.getElementById(\''.$this->opener.'\').value+=\''.Help::Script($row['text']).'\';
		                 var tc= document.getElementById(\'teamCount\'); tc.value = parseInt(tc.value)+1;
		                if (document.getElementById(\'teamCount\').value == 1) { javascript:window.opener.document.getElementById(\''.$this->opener.'\').value+=\' - \'; void(0); } else if (document.getElementById(\'teamCount\').value == 2) { self.close(); void(0); } "';
		}
		$this->vrat .= '>'.Help::Html($row['text']).'</span></td></tr>';  
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
