<?php
/**
 * @package    statistics
 */


 /**
 * Trida pro praci se statistikami Uzivatelu
 *
 *
 * @package    main
 */
 
class StatistikyUzivatele{


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
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param int $section id aktualni sekce
* @param PEAR::DB $dbGame objekt spojeni s databazi
* @param PEAR::DB $db objekt spojeni s databazi
*/
  public function __construct($db=null,$dbGame=null){

  $this->section =  $section;
  
   if($db == null){
   
   $this->db = DB::connect(DATABASE ."://". MY_USER .":". MY_PASS ."@". MY_HOST ."/". MY_DB);
   if (DB::isError($this->db)) {
     throw new ExHandler($this->db->getMessage(),"admin_ex_db");
   }
   $this->db->setFetchMode(DB_FETCHMODE_ASSOC);
   $sql = "set names 'utf8'";
   $res =& $this->db->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");
   
   
   }else
        $this->db =  $db;
   
   
   if($dbGame == null){
   
    $this->dbGame = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
    if (DB::isError($this->dbGame)) {
      throw new ExHandler($this->dbGame->getMessage(),"admin_ex_db");
    }
    $this->dbGame->setFetchMode(DB_FETCHMODE_ASSOC);
    $sql = "set names 'utf8'";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");
   
   
   }else
        $this->dbGame =  $dbGame;
  
  }
  
   
  public function __destruct(){
    
	

	 
  }
  
}

?>