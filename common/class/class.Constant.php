<?php



final class Constant{

private static $dbGame;

private static $constAr;

/**
 * spojeni s db
 *
 * @return void
 */
 private static function dbConnect(){

   require_once 'Zend/Db.php';
   Zend_Loader::loadClass('Zend_Db_Table');
   Zend_Loader::loadClass('Zend_Registry');

   $dbGameZend = Zend_Db::factory('MYSQLI', array(
    'host'             => GMY_HOST,
    'username'         => GMY_USER,
    'password'         => GMY_PASS,
    'dbname'           => GMY_DB
    ));
    Zend_Db_Table::setDefaultAdapter($dbGameZend);
    $dbGameZend->query('SET NAMES utf8');
    $dbGameZend->setFetchMode(Zend_Db::FETCH_ASSOC);
    Zend_Registry::set('db', $dbGameZend);

    $dbAdminZend = Zend_Db::factory('MYSQLI', array(
    'host'             => MY_HOST,
    'username'         => MY_USER,
    'password'         => MY_PASS,
    'dbname'           => MY_DB
    ));    
    $dbAdminZend->query('SET NAMES utf8');
    $dbAdminZend->setFetchMode(Zend_Db::FETCH_ASSOC);
    Zend_Registry::set('admindb', $dbAdminZend);
    
    Zend_Registry::set(
    	'logdb',
    	Zend_Controller_Plugin_DbPLugin::initMongoDbConnection('logdb', Zend_Controller_Plugin_DbPLugin::CONFIG_LOG, false)
    );
    
    return true;

 }

/**
 * vraci hodnotu konstanty
 *
 * @param string $name nazev konstanty
 * @param bool $force zda chci jit urcite do DB
 * @return bool
 */
 public static function get($name,$force=false){
     require_once('Cache/Lite.php');
     $options = array(
       'cacheDir' => CACHE_DIRECTORY.'/',
       'lifeTime' => 1800
     );
     $cache = new Cache_Lite($options);

     $id = 'constant_id';

     // chci hledat v cachi a je to tam
     if(!$force && ($data = $cache->get($id))) {
        eval($data);
        if(isset($poleConst[$name])) self::$constAr[$name] = $poleConst[$name];
        // zkusim sahnout do DB
        else $cache->save(self::loadFromDB($name),$id);

     }
     // taham z DB
     else $cache->save(self::loadFromDB($name),$id);

     return self::$constAr[$name];
 }

/**
 * vraci hodnotu konstanty
 *
 * @param string $name nazev konstanty
 * @param string $hodnota
 * @param bool $force zda chci ulozit take do cache
 * @return bool
 */
 public static function set($name,$hodnota,$force=true){
self::dbConnect();


     $sth = Zend_Registry::get('db')->update("t_cfg",array('dt_in'=>It6_Date::dbNow(),'s_hodnota'=>$hodnota)," where s_nazev='".Help::slash($name)."'");


     return self::get($name,$force);
 }

/**
 * natahne data z DB (zaroven ulozi do cache)
 *
 * @param string $name nazev konstanty
 * @return string cerstva data pro ulozeni do cache
 */
 private static function loadFromDB($name){
    self::dbConnect();

    $sth7 = Zend_Registry::get('db')->query("select s_nazev,s_hodnota,datovy_typ from t_cfg")->fetchAll();


    $data = '$poleConst = array(';
    foreach ($sth7 as $row7){
        settype($row7['s_hodnota'],$row7['datovy_typ']);
        if($row7['datovy_typ'] == 'integer' || $row7['datovy_typ'] == 'double') $data .='"'.$row7['s_nazev'].'"=>'. $row7['s_hodnota'].',';
        else $data .='"'.$row7['s_nazev'].'"=>"'. $row7['s_hodnota'].'",';
        if($name == $row7['s_nazev']) self::$constAr[$name] = $row7['s_hodnota'];
    }
    $data = substr($data,0,-1);
    $data .= ');';

    return $data;
  }

}

