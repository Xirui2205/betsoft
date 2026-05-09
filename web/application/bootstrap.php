<?php
error_reporting(E_ALL|E_STRICT);

ini_set('display_startup_errors', DISPLAY_ERRORS);
ini_set('display_errors', DISPLAY_ERRORS);

//Pridal Martin aby mohl vypnout cachovani v Promo. 15.4. 2010
define('CACHING', 'on');

//TODO this should be loaded dynakmicli by user profile
//TODO this is not acceptable way how set locale (who can read let's see PHP manual)
//setlocale(LC_ALL,'cs_CZ.utf8');
//date_default_timezone_set('Europe/Prague');

require_once ROOT.'common/config.php';
require_once ROOT.'common/class/class.Help.php';
require_once ROOT.'common/class/class.Constant.php';
require_once ROOT.'common/class/class.Log.php';
require_once ROOT.'common/class/Ip.php';
require_once ROOT.'common/class/class.Session.php';
require_once 'Zend/Loader.php';
require_once 'Zend/Loader/Autoloader.php';

class Bootstrap {

	private static $frontController;

	public static function run() {
		self::init();
		self::dispatch();
	}

	public static function init() {

		static $firstRun = true;

		if ( !$firstRun ) return;
		$firstRun = false;

		if (empty($_SERVER['HTTP_USER_AGENT']))
			$_SERVER['HTTP_USER_AGENT'] = 'unknown';

		Zend_Loader::loadClass('Zend_Debug');
		Zend_Loader::loadClass('Zend_Controller_Front');

		$autoloader = Zend_Loader_Autoloader::getInstance();
		Zend_Registry::set('autoloader', $autoloader);
		$autoloader->registerNamespace('Models_');
		$autoloader->registerNamespace('It6_');
		$autoloader->registerNamespace('Webservice_');
		$autoloader->registerNamespace('Entities_');
		$autoloader->registerNamespace('WarpTurn_');
		$autoloader->autoload('Models_Session_SesClass');
		$autoloader->autoload('Models_Session_Session');
		$autoloader->autoload('Models_Helpers_Help');

		//TODO: use user's language or language from session
		Zend_Registry::set('Zend_Locale', new Zend_Locale('cs_CZ'));
		date_default_timezone_set('Europe/Prague');
		mb_internal_encoding("UTF-8");

		It6_Log::initialize();

		//NOTE: SSL certificate is needed for authentization but wouldn't be better other way for DIRECT mode?
		$client = null;
		if (It6_WS::XML_RPC == WS_WRAPPER) {
			$sslAdapter = new Zend_Http_Client_Adapter_Socket();
			$sslAdapter->setStreamContext(array(
				'ssl' => array('local_cert' => SSL_CERT, 'passphrase' => SSL_CERT_PASSWD)
			));
			$httpClient = new Zend_Http_Client();
			$httpClient->setAdapter($sslAdapter);
			$client = new Zend_XmlRpc_Client(WEB_SERVICE_URL, $httpClient);
			//$client->setSkipSystemLookup(true);
		}
		Zend_Registry::set('ws',new It6_WS(WS_WRAPPER, $client));
        
        // FirePHP log; usage: Zend_Registry::get('fl')->info($myVariable);
        Zend_Registry::set('fl', new Zend_Log(new Zend_Log_Writer_Firebug()));
        		
		//nastavim httponly pro cookiens kvuli XSS
		Zend_Session::setOptions(array('cookie_httponly' => true));

		session_set_save_handler (
			array("Models_Session_Session", "open"),
			array("Models_Session_Session", "close"),
			array("Models_Session_Session", "read"),
			array("Models_Session_Session", "write"),
			array("Models_Session_Session", "destroy"),
			array("Models_Session_Session", "gc")
		);

		register_shutdown_function('session_write_close');

		// Initialise Zend_Layout's MVC helpers
		$layout = Zend_Layout::startMvc(array(
			'layoutPath'	=> ROOT . 'web/application/views/layouts',
			'layout'		=> 'default'
		));

		self::$frontController = Zend_Controller_Front::getInstance();

		$route = new Zend_Controller_Router_Route(
			':lang/:controller/:action/*',
			array(
				'controller'=>'index',
				'action' => 'index',
				'module'=>'default',
				'lang' => DEFAULT_LANG
			)
		);
		$router = self::$frontController->getRouter();
		$router->addRoute('default', $route);
		self::$frontController->setRouter($router);

		self::$frontController->registerPlugin(new Zend_Controller_Plugin_DbPLugin())
			->registerPlugin(new Zend_Controller_Plugin_SetController())
			->registerPlugin(new Zend_Controller_Plugin_SessionPlugin());
			//->registerPlugin(new Zend_Controller_Plugin_HtmlToXhtml());

		self::$frontController->throwExceptions(true);
		self::$frontController->setControllerDirectory(ROOT . 'web/application/controllers/');
		$layout->getView()->addHelperPath('It6/View/Helper', 'It6_View_Helper_');
		$layout->getView()->doctype()->setDoctype(Zend_View_Helper_Doctype::XHTML1_STRICT);
	}

	public static function dispatch() {
		try {
			$f = self::$frontController;
			self::$frontController->dispatch(
				new Zend_Controller_Request_Http(),
				new Zend_Controller_Response_Http());

			$req = self::$frontController->getRequest();
			if (isset($req->rateMin) && isset($req->rateMax) && $req->send == 'OK') {
				$_SESSION['rateMin'] = $req->rateMin;
				$_SESSION['rateMax'] = $req->rateMax;
				It6_GlobalCache_Invalidator::invalidateSportMenuFrame();
				It6_GlobalCache_Invalidator::invalidateSportsbook();
			}
			if (isset($req->send) && $req->send == 'X') {
				unset($_SESSION['rateMin']);
				unset($_SESSION['rateMax']);
				It6_GlobalCache_Invalidator::invalidateSportMenuFrame();
				It6_GlobalCache_Invalidator::invalidateSportsbook();
			}
		}
		catch (Zend_Controller_Dispatcher_Exception $e) {
			if (false === strpos($e->getMessage(), '__redirect__'))
				throw $e;
			self::$frontController->getResponse()->sendResponse();
		}
		catch (It6_Controller_Exception_ImmediateExit $e) {
			//self::$frontController->getResponse()->setRawHeader
			$r = self::$frontController->getResponse();
			$r->setHeader('Cache-Control', 'no-cache, must-revalidate', true);
			$r->setHeader('Pragma', 'no-cache', true);
			$r->setHeader('Expires', '0', true);
			self::$frontController->getResponse()->setBody($e->getMessage())->sendResponse();
		}
		catch (Exception $e) {
			It6_GlobalCache::turnOff();
			It6_Log::err(It6_Log::exceptionToString($e));
			header('HTTP/1.1 503 Service not available');
			readfile(ROOT . 'web/application/views/scripts/503.phtml');
		}
	}

	public static function close() {
		Models_Session_SesClass::close();
	}
}