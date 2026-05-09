<?php

class It6_Controller_Plugin_SetController extends Zend_Controller_Plugin_Abstract
{

/**
* id menu
* @access private
* @var int
*/
private $c_id = 0;

private static function initDbConnection($host, $user, $passwd, $dbName, $registryEntry = null, $default = false) {
	$db = Zend_Db::factory('Pdo_Mysql', array(
		'host' => $host,
		'username' => $user,
		'password' => $passwd,
		'dbname' => $dbName
	));
	$db->query('SET NAMES utf8');
	$db->query('SET time_zone=\'GMT\'');
	$db->setFetchMode(Zend_Db::FETCH_ASSOC);
	if ($default)
		Zend_Db_Table::setDefaultAdapter($db);
	if (!empty($registryEntry))
		Zend_Registry::set($registryEntry, $db);
	return $db;
}

public static function connectDbWeb() {
	return static::initDbConnection(GMY_HOST, GMY_USER, GMY_PASS, GMY_DB, 'zdb_game');
}

public static function connectDbAdmin() {
	return static::initDbConnection(MY_HOST, MY_USER, MY_PASS, MY_DB, 'zdb_admin', true);
}

public static function connectDbSession() {
	return static::initDbConnection(SESMY_HOST, SESMY_USER, SESMY_PASS, SESMY_DB, 'zdb_sess');
}

/**
 * Called after Zend_Controller_Router exits.
 * Called after Zend_Controller_Front exits from the router.
 *
 * @param  Zend_Controller_Request_Abstract $request
 * @return void
 */
public function routeShutdown(Zend_Controller_Request_Abstract $request)
{

	$dbGameZend = static::connectDbWeb();
	$dbAdminZend = static::connectDbAdmin();
	$dbSessionZend = static::connectDbSession();

	$section = ( empty($_REQUEST['section']) ? 1 : $_REQUEST['section'] );
	$res = $dbAdminZend->select()->from('sekce', array('controller', 'action', 'sekce_id'))->where('sekce_id=?', $section)->query();
	DbUtil::testResult($res);
	$sectionData = $res->fetch();
	$oldAdmin = (empty($sectionData) || empty($sectionData['controller']));
	Zend_Registry::set('isOldAdmin', $oldAdmin);
	Zend_Registry::set('section', $sectionData['sekce_id']);
	if ($oldAdmin)
		$controller = 'old-admin';
	else
		$controller = $sectionData['controller'];

	$action = 'index';
	if (!empty($sectionData) && !empty($sectionData['action']))
		$action = $sectionData['action'];

	// !!! Na tento blok pozor !!!
	// Toto je zde pro zpetnou kompatibilitu zpracovani session.
	//$bookmaker = isset($_REQUEST['superb']);
	//Zend_Registry::set('isBookmaker', $bookmaker);
	//TODO: casem nahradit SessionBookmaker Zend-alternativou sjednocujici bookmakery a adminy
	//TODO: session sloucena odstranit
	//if($bookmaker) {
	//	session_set_save_handler (
	//		array("SessionBookmaker", "open"),
	//		array("SessionBookmaker", "close"),
	//		array("SessionBookmaker", "read"),
	//		array("SessionBookmaker", "write"),
	//		array("SessionBookmaker", "destroy"),
	//		array("SessionBookmaker", "gc")
	//	);
	//}

	// !!! Na tento blok pozor !!!
	// Toto je zde pro zpetnou kompatibilitu trid SesClass a Session
	// (Session -- pouziti jako session handler je automaticke z common.php),
	// bylo by potreba pro Zend napsat novou implementaci bez vyuziti PEAR::DB.
	// Dalsi neprijemnost je, ze trida Session ma nevhodne v sobe aplikacni logiku prihlaseni, takze je docela velka...
	// Ekvivalentni kod si vola stara verze (class Main) sama.
	//$db = DbUtil::connectAdminDb();
	//SesClass::open($db);
	It6_Session_Admin::start($dbSessionZend);

	$acl = It6_Acl_Factory::newAcl(array(
		'adminDb' => $dbAdminZend,
		'adminId' => It6_Session_Admin::getUserData('id'),
	));
	Zend_Registry::set('acl', $acl);

	if (!It6_Session_Admin::isAuthenticated()) {
		$controller = 'login';
		$action = 'index';
	}

	$this->getRequest()->setControllerName($controller);
	$this->getRequest()->setActionName($action);
	//TODO: vic_main.bookmaker and vic_admin.admin don't have column for user's language
	//      temporary solution -- fixed cs
	//$translate = new Translate($isoj[$_SESSION['lang']], $controller, $action);
	$translate = new It6_Translate_Admin(1, $dbGameZend);
	Zend_Registry::set('translate', $translate);

}

/*
      /**
     * Zde se definuji vyjimky pro menu resp. Controller a action
     *
.    *
     * @return void
     * /
    private function menuExceptions(){

       $select = Zend_Registry::get('db')->select()->from(array('a'=>'controller_convert'),array('a.c_id','a.real_controller','a.real_action','b.lang_id',"lang"=>'b.iso'))
	                                    ->join(array('b'=>'jazyky'),'a.lang_id=b.lang_id')
	                                    ->where('a.req_controller=?',$this->getRequest()->getControllerName())
	                                    ->where('b.iso=?',$_SESSION['lang']);
       $stm  = $select->query();
  	   $row = $stm->fetchAll();

  	    #Sazeni, Live sazeni#

  	    if(count($row) > 0){

    	if($row[0]['c_id'] == 9 || $row[0]['c_id'] == 2  || $row[0]['c_id'] == 4 || $row[0]['c_id'] == 5 || $row[0]['c_id'] == 6 || $row[0]['c_id'] == 7){
    		Zend_Registry::set('c_id', $row[0]['c_id']);
    		return true;
    	}

    	else
    	  return false;

  	    }

  	    return false;
    }
*/

} //class It6_Controller_Plugin_SetController
