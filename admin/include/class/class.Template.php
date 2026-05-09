<?php
/**
 * @package    main
 */


 /**
 * Trida pro praci s xxxxxx
 *
 * priklad ziskani aktualniho datumu pro vlozeni do MySQL ve spravnem formatu (ISO)
 * <code>
 * Date::getIsoDateNow();
 * </code>
 *
 * @package    main
 */
 
abstract class Template{

/**
 * vystupni XML response
 * @access private
 * @var int
 */
 private $vrat;

/**
 * vytvori chybovy XML response podle vyhozene vyjimky
 * @param DB_PEAR $db spojeni na databazi
 * @return bool potvrzeni spojeni
 */
  public function __construct(){}
  
 /**
 * Metoda spousti jednitlove metody podle stavu
 * @return void
 */
  public abstract function runAction();
  
  
//comented out by Martin on 6.9. 2012
//db based ACL authentication is used, this method should not be needed any more
 /**
 * Nastaveni prav k sekci
 * @param int $update pravo zapisu
 * @param int $delete pravo smazani
 * @return void
 */
//public abstract function setPrivileges($update,$delete);
  
 /**
 * Vraci vystup do tridy main
 * @return string
 */
  public abstract function getContent();
   
  public function __destruct(){

  }
  
}

?>