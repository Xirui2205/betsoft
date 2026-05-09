<?php

include_once(ROOT . 'common/config.php');

require_once(ROOT . 'common/library/Zend/Controller/Plugin/Abstract.php');

class Zend_Controller_Plugin_DbPLugin extends Zend_Controller_Plugin_Abstract 
{ 

	const CONFIG_MAIN = 1;
	const CONFIG_SESSION = 2;
	const CONFIG_ADMIN = 3;
	const CONFIG_LOG = 4;

	private static $_adapter = 'Pdo_Mysql';
	private static $_adapterNamespace = 'It6_Db_Adapter'; // set to empty value if default should be used
	private static $_configs = array(
		self::CONFIG_MAIN => array("host"=>GMY_HOST,"username"=>GMY_USER,"password"=>GMY_PASS,"dbname"=>GMY_DB,"port"=>GMY_PORT,'charset'=>'utf8'),
		self::CONFIG_SESSION => array("host"=>SESMY_HOST,"username"=>SESMY_USER,"password"=>SESMY_PASS,"dbname"=>SESMY_DB,"port"=>SESMY_PORT,'charset'=>'utf8'),
		self::CONFIG_ADMIN => array("host"=>MY_HOST,"username"=>MY_USER,"password"=>MY_PASS,"dbname"=>MY_DB,"port"=>MY_PORT,'charset'=>'utf8'),
		self::CONFIG_LOG => array(
			"host"=>MONGODB_LOG_HOST,
			"db"=>MONGODB_LOG_DB,
			"port"=>MONGODB_LOG_PORT
		),
	);

	public function __construct() {
	}

	public static function initDbConnection($registryEntry, $config, $makeDefault = false) {
		require_once(ROOT . 'common/library/Zend/Db.php');
		require_once(ROOT . 'common/library/Zend/Db/Adapter/Pdo/Mysql.php');
		require_once(ROOT . 'common/library/Zend/Db/Table.php');
		require_once(ROOT . 'common/library/Zend/Registry.php');
		$_config = (is_numeric($config) ? static::$_configs[$config] : $config);
		if (!empty(static::$_adapterNamespace))
			$_config['adapterNamespace'] = static::$_adapterNamespace;
		$db = Zend_Db::factory(static::$_adapter, $_config);
		if ($makeDefault)
			Zend_Db_Table::setDefaultAdapter($db);
		if (!empty($registryEntry))
			Zend_Registry::set($registryEntry, $db);
		return $db;
	}
	
	public static function initMongoDbConnection($registryEntry, $config, $makeDefault = false) {
		/*try {
			$_config = (is_numeric($config) ? static::$_configs[$config] : $config);
			$conn = new Mongo('mongodb://'.$_config['host'].':'.$_config['port']);
			$dbName = $_config['db'];
			$db = $conn->$dbName;
			if (!empty($registryEntry))
				Zend_Registry::set($registryEntry, $db);
	
			return $db;
		} catch( Exception $e ) {
			//TODO add alert.
		}*/
	}

	public static function connectDbMain() {
		return static::initDbConnection('db', self::CONFIG_MAIN, true);
	}

	public function connect() {
		try{
			static::connectDbMain();
			static::initDbConnection('dbSes', self::CONFIG_SESSION, false);
			static::initDbConnection('admindb', self::CONFIG_ADMIN, false);
			static::initMongoDbConnection('logdb', self::CONFIG_LOG, false);
		}
		catch (Zend_Exception $e) {
			echo $e->getMessage();
		}
	}
        
    /**
     * Called after Zend_Controller_Router exits.
     *
     * Called after Zend_Controller_Front exits from the router.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request){
       	Zend_Registry::get('db')->closeConnection();
       	Zend_Registry::get('dbSes')->closeConnection();
    }
        
 /**
     * Called before Zend_Controller_Front begins evaluating the
     * request against its routes.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
      {
             $this->connect();
        }
	
  

} 