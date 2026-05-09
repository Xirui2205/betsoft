<?php

/**
 * @package    book
 */

/**
 * Trida pro praci s preklady
 *
 * 
 * <code>
 *   $this->obj =  new Redirect();
 *  $this->obj->runAction();
 *  $this->obj->redirect;
 * </code>
 *
 * @package   main
 */

class Redirect{

/**
 * navratova hodnota
 * @access private
 * @var string
 */
private  $vrat = "";

/**
 * pole menu
 * @access private
 * @var array
 */
private  $menu = array();

/**
 * navratove pole
 * @access private
 * @var array
 */
public  $redirect = array();

/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */              
private  $dbGame;

  public function __construct($section=1,$dbGame=null){
 
   $this->section =  $section;
   
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
  
  /**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @param string $url chybna adresa
 * @param string $lang jazyk stranek
 * @param string $sekce v jake sekci se nachazime
 * @return void
 */
  public function runAction($url,$lang="en"){

 	$sql = "select a.iso,a.alt_text,a.lang_id,b.text,b.uri,b.menu_id from jazyky a inner join preklady_menu b on a.lang_id=b.lang_id where a.zobrazeno=1 and b.zobrazeno=1";
    $res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: smazani prekladu z databáze',"admin_ex_db");
	
	$match = array();
	
	while ($row =& $res->fetchRow() && mb_strlen($url) > 0){
	  

	  
	    if(isset($this->menu[$row['iso']])) $klic = count($this->menu[$row['iso']]);else $klic = 0;
	  
	    $this->menu[$row['iso']]['republika'] = $row['alt_text'];
	    $this->menu[$row['iso']][$klic]['url'] = $row['uri'];
	    $this->menu[$row['iso']][$klic]['text'] = $row['text'];
	  

	  
	}
    
	
	$sql = "select a.iso,a.alt_text,a.lang_id,b.url,b.type,b.event_id from jazyky a inner join seo_url b on a.lang_id=b.lang_id where a.lang_id=".intval($_SESSION['lang_id'])." and a.zobrazeno=1";
    $res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler('<br>Nepodarilo se provest dotaz: smazani prekladu z databáze',"admin_ex_db");
	
		
	while ($row =& $res->fetchRow() && mb_strlen($url) > 0){
	  $text = '';
	  

	  
	    if(isset($this->menu[$row['iso']])) $klic = count($this->menu[$row['iso']]);else $klic = 0;
	     
	    if($row['type'] == 1){
	    	
	    	$sql = "select a.text from preklady a where a.index_pole=(select b.nazev from sport b where b.sport_id=".$row['event_id']." ) and lang_id=".$row['lang_id'];
            $res2 =& $this->dbGame->query($sql);
	        if(DB::isError($res2)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sportu',"admin_ex_db");
	    	if ($row2 =& $res2->fetchRow()) $text = $row2['text'];
	    	
	    	
	    }
	    if($row['type'] == 2){
	    	
	    	$sql = "select a.text from preklady a where a.index_pole=(select b.nazev from oblast b where b.oblast_id=".$row['event_id']." ) and lang_id=".$row['lang_id'];
            $res2 =& $this->dbGame->query($sql);
	        if(DB::isError($res2)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sportu',"admin_ex_db");
	    	if ($row2 =& $res2->fetchRow()) $text = $row2['text'];
	    	
	    }
	    if($row['type'] == 3){
	    	
	    	$sql = "select a.text from preklady a where a.index_pole=(select b.nazev from udalost b where b.udalost_id=".$row['event_id']." ) and lang_id=".$row['lang_id'];
            $res2 =& $this->dbGame->query($sql);
	        if(DB::isError($res2)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sportu',"admin_ex_db");
	        if ($row2 =& $res2->fetchRow()) $text = $row2['text'];
	    	
	    }
	    if($row['type'] == 4){
	    	
	   $sql = "select a.text from preklady a where a.index_pole=(select b.nazev from typ b where b.typ_id=".$row['event_id']." ) and lang_id=".$row['lang_id'];
            $res2 =& $this->dbGame->query($sql);
	        if(DB::isError($res2)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sportu',"admin_ex_db");
	        if ($row2 =& $res2->fetchRow()) $text = $row2['text'];
	    	
	    }
	    
	    $this->menu[$row['iso']]['republika'] = $row['alt_text'];
	    $this->menu[$row['iso']][$klic]['url'] = $row['url'];
	    $this->menu[$row['iso']][$klic]['text'] = $text;
	  

	  
	}
	

	
	$url = "/".$url;
	   
//echo "<pre>";	print_r($this->menu);
	$key = metaphone(basename($url));
	
	foreach($this->menu as $k2=>$h2){
	  
	 foreach($h2 as $k=>$h){
	  
	  similar_text($h['url'],$url,$p);
	  
	  if(metaphone($h['url']) == $key || $p>60){ 
	   
	    if(isset($match[$k2])) $klic = count($match[$k2]);else $klic = 0;
	   
	   $match[$k2][$klic]['url'] =  $h['url'];
	   $match[$k2][$klic]['text'] =  $h['text'];
	   $match[$k2][$klic]['per'] =  round($p,2);
	   
	  }
	
	 }
	
	}
	 //echo "<pre>"; 
	//print_r($match);

	$this->redirect = $match;

   $this->dbGame->disconnect();
  
  }
 
  /**
 * Vraci nazev pro jazyk
 * @param string $lang jazyk stranek
 * @return string
 */
  public function getLang($lang="en"){
  
   return (isset($this->menu[$lang])?$this->menu[$lang]:"");
  
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
