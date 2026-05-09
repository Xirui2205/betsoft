<?php
/**
 * @package    book
 */

/**
 * Trida pro praci s cislenikem udalosti
 *
 * 
 * <code>
 * 
 * </code>
 *
 * @package    Book
 */
 
class BookHp extends Template{

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
  public function __construct($section=0){
  
    $this->section =  $section;
  
   
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
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
  public function runAction(){
   
   
   
    #Editace#
    if(isset($_POST['send'])){
	 
	    $this->Edit();
		
    } 
   
  
	if (getAppEnv() !== 'DEVEL_LOCAL') {
		$this->Show();
	} else {
		// výchozí stránka "Ovládací centrum nodů" - na testu a localu je potřeba ji nejčasteji zobrazovat
		header('Location: ?section=331');
		exit;
	}

   
   $this->dbGame->disconnect();
   
  }
  
 
   /**
 * Edit 
 * @return void
 */
  private function Edit(){
  
   $status = true;
   
   	
   if(!isset($_POST['limit_l']) ) {$this->vrat .= "<div class=\"errormsg\"> <strong>Limit low</strong> musí být uveden</div><br />";$status = false;}
   if(!isset($_POST['limit_h']) ) {$this->vrat .= "<div class=\"errormsg\"> <strong>Limit high</strong> musí být uveden</div><br />";$status = false;}
    
   if($status && intval($_POST['limit_l']) < 1) $_POST['limit_l'] = 1;
   if($status && intval($_POST['limit_h']) < 1) $_POST['limit_h'] = 1;
   
   #vsechno je  vporadku muzeme editovat#
	if ( $status ) {

		$sql = "update nastaveni set ticket_limit_low=".intval($_POST['limit_l']).",ticket_limit_high=".intval($_POST['limit_h']);
		$res =& $this->dbGame->query($sql);
		if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: editace nastaveni',"admin_ex_db");

		$this->vrat .= "<div class=\"okmsg\">Editace proběhla úspěšně</div><br />";
		It6_Log::info(
			"Limits in the table 'nastaveni' changed to %low - %high.",
			It6_Log::TAG_DEPRECATED_OPERATION,
			array(
				'low' => intval($_POST['limit_l']),
				'high' => intval($_POST['limit_h']),
				'evenName' => $_POST['udalost'][$ud_id]['nazev']));
   } else
		$this->vrat .= "<div class=\"errormsg\">Nastala chyba</div><br />";
  }
     
 /**
 * metoda vypise vsechny zadane udalosti
 * @return void
 */
public function Show(){

/*
	$this->vrat .= '
<form action="?superb=1" method="post">
<table>';
  	
   $oblast = array();
   $sql = "select ticket_limit_low,ticket_limit_high from nastaveni";
   $res =& $this->dbGame->query($sql);
   
   if ($row =& $res->fetchRow()){	

   	$this->vrat .= '
<tr>
	<td>Limit jednoduchý tiket:		         </td> <td> <input type="text" class="mandatory sinput3" name="limit_l" value="'.$row['ticket_limit_low'].'" /> </td>
		<td>Limit kombinovaný tiket:			 </td> <td> <input type="text" class="mandatory sinput3" name="limit_h" value="'.$row['ticket_limit_high'].'" /> </td>
</tr>'; 
     }
      
      $this->vrat .= '
 <tr><td colspan="2"> <input type="submit"  name="send" value="Uložit" /> </td></tr>

</table></form>'; 
*/

	$this->vrat = '<h3>' . I18n::tr('Welcome to BBAS') . '</h3>';
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
