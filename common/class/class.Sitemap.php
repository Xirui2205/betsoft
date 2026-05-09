<?php

 
class Sitemap{

/**
 * zde se naplnuje xml
 * @access private
 * @var string
 */
private  $xml = "";

/**
 * template pro xml
 * @access private
 * @var int
 */
private  $xml_template = "<url>
   <loc>%s</loc>
   <lastmod>%s</lastmod>
    <changefreq>daily</changefreq>
    <priority>%s</priority>
  </url>\n";
/**
 * hlavicka xml dokumentu
 * @access private
 * @var string
 */              
private  $xml_head = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">%s</urlset>';

/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */              
private  $dbGame;

/**
 * aktualni cas
 * @access private
 * @var string
 */              
private  $actTime;      

/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param int $section id aktualni sekce
* @param PEAR::DB $dbGame objekt spojeni s databazi
*/
  public function __construct($dbGame=null){
  
  
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
   
   $this->actTime = date('Y-m-d');
        
  }
  
    /**
 * Metoda vraci xml jako retezec
 * @return string
 */
 public function getString(){

    $this->createBasicNav();
    $this->createSeoUrlNav();
 	
 	$this->xml = sprintf($this->xml_head,$this->xml.$this->v);
 	

 	return $this->xml;
   
 }
 
     /**
 * Metoda vraci url pro navigacni url
 * @return string
 */
 private function createSeoUrlNav(){
 	 

          $sport = $oblast = $udalost = $druh = $lang = array();       
 	
      	  $sth3 = $this->dbGame->prepare("select 	 url,type,lang_id 	from seo_url");
          if (PEAR::isError($sth3))  {throw new ExHandler($res->getMessage().'Nepodarilo se nacist preklady menu',"admin_ex_db");}
      	  $res3 =& $this->dbGame->execute($sth3);
          if (PEAR::isError($res3))  {throw new ExHandler($res->getMessage().'Nepodarilo se  nacist preklady menu',"admin_ex_db");}
          
          while ($row =& $res3->fetchRow()){
            	
          	$this->xml .= sprintf($this->xml_template,'http://'.WEBHOST.$row['url'],$this->actTime,'0.5');

          	/*if($row['type'] == 1) $sport[$row['lang_id']][] = $row['url'];
          	else if($row['type'] == 2) $oblast[$row['lang_id']][] = $row['url'];
          	else if($row['type'] == 3) $udalost[$row['lang_id']][] = $row['url'];
          	else if($row['type'] == 4) $druh[$row['lang_id']][] = $row['url'];*/
          	
          	if(!isset($lang[$row['lang_id']]) && $row['lang_id'] == 2) $lang[$row['lang_id']] = 1;
          	
          }
 }
 
     /**
 * Metoda vraci url pro zakladni adresy v ramci 
 * @return string
 */
 private function createBasicNav(){
 	 
      	  $sth3 = $this->dbGame->prepare("select menu_id, uri from preklady_menu");
          if (PEAR::isError($sth3))  {throw new ExHandler($res->getMessage().'Nepodarilo se nacist preklady menu',"admin_ex_db");}
      	  $res3 =& $this->dbGame->execute($sth3);
          if (PEAR::isError($res3))  {throw new ExHandler($res->getMessage().'Nepodarilo se  nacist preklady menu',"admin_ex_db");}
          
          while ($row =& $res3->fetchRow()){
            	
          	$this->xml .= sprintf($this->xml_template,'http://'.WEBHOST.$row['uri'],$this->actTime,self::getPriority($row["menu_id"]));
            	
          }
 	
 }
 private function getPriority($menu_id) {
 	return in_array($menu_id,array(181, 192, 143, 159, 115, 10, 37, 170, 171, 172, 174, 176))?1:0.5;
 }
  
}

?>