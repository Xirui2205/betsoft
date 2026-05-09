<?php

class Zend_Controller_Plugin_SetController extends Zend_Controller_Plugin_Abstract
{

	/**
	* id menu
	* @access private
	* @var int
	*/
	private $c_id = 0;

	private function redirect($url, $code = 302) {
		$this->getResponse()->setRedirect($url, $code);
		$this->getRequest()->setControllerName('__redirect__');
	}

    /**
     * Called after Zend_Controller_Router exits.
     *
     * Called after Zend_Controller_Front exits from the router.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {

	$db = Zend_Registry::get('db');

     $select = $db->select()->from(array('a'=>'jazyky'),array('a.iso','a.lang_id','a.alt_text','a.flag_oblast_id','a.collation'));
     $stm  = $select->query();
  	 $row = $stm->fetchAll();

	$isoj = array();
	$l = array();
	foreach($row as $h) {
		//if($h['iso']=='cs') {
			$j[] = $h['iso'];
			$isoj[$h['iso']] = $h['lang_id'];
			$collationj[$h['iso']] = $h['collation'];
			$l[$h['iso']] = array('text' => $h['alt_text'], 'flag_oblast_id' => $h['flag_oblast_id']);
		//}
	}
	Zend_Registry::set('languages', $l);

	$lang = $this->getRequest()->getParam('lang');
	$newLang = DEFAULT_LANG;
	if ('browser' == $lang && !empty($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
		$langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		foreach ($langs as $l) {
			$l = substr($l, 0, 2);
			if (in_array($l, $j)) {
				$newLang = $l;
				break;
			}
		}
	}
	
	$gAdParams = It6_Models_ControllerConvert::getGoogleAdParams();
	$redirect = Zend_Registry::isRegistered('redirect');
	$pageNotFound = false;
	if(
		!$redirect
		&& (
			mb_strlen($_SERVER['REQUEST_URI']) == 0
			|| $_SERVER['REQUEST_URI'] == '/'
			|| 'browser' == $lang
		)
	) {
		$url = It6_Models_ControllerConvert::buildUrl($newLang, null, null, true, $gAdParams);
		$this->redirect($url, 301); //$this->redirect(PROTOCOL . $_SERVER['HTTP_HOST'] . "/$newLang/", 301);
		return;
	}
	else if (!$redirect && !in_array($lang,$j)) {
		$pageNotFound = true;
	}
	else {
		if ('browser' == $lang)
			$lang = $newLang;
		$reqParams = preg_split("!/+!",$this->getRequest()->getPathInfo());
		$reqController = $this->getRequest()->getControllerName();
		$reqAction = $this->getRequest()->getActionName();

		$url = array($lang, $reqController, $reqAction, $reqParams);
		$targetData = It6_Models_ControllerConvert::getDataFromUrl($url, $dummy, $controller, $action, $params, $db);

		$redirected = false;
		if ($redirect) {
			$redirect = Zend_Registry::get('redirect');
			if (array_key_exists('controllerConvertId', $redirect)) {
				if ($redirect['controllerConvertId'] != $targetData['controllerId']) {
					$redirectData = It6_Models_ControllerConvert::getByIdAndLang($redirect['controllerConvertId'], $lang);
					if (!empty($redirectData)) {
						$url = It6_Models_ControllerConvert::buildUrl($lang, $redirectData['reqController'], $redirectData['reqAction'], true, $gAdParams);
						if ('ajax' == $targetData['realController']) {
							//$this->getRequest()->setControllerName('ajax');
							//$this->getRequest()->setActionName('redirect');
							//$redirect['data'] = $url;
							//Zend_Registry::set('redirect', $redirect);
						}
						else {
							$redirected = true;
							$this->redirect($url);
							return;
						}
					}
				}
			}
		}
		if (!$redirected) {
			if (!empty($targetData) && !empty($targetData['visible'])) {
				$this->c_id = $targetData['controllerId'];
				$this->getRequest()
				->setControllerName($targetData['realController'])
				->setActionName($targetData['realAction'])
				->setParams($params);
			}
			else
				$pageNotFound = true;
		}
	}

	if ($pageNotFound) {
		$cc = It6_Models_ControllerConvert::getByIdAndLang(46, $newLang, $db); // 404 page
		if (empty($cc))
			throw new Exception('No controller for 404 page found');
		$lang = $newLang;
		$reqParams = array();
		$reqController = $cc['reqController'];
		$reqAction = $cc['reqAction'];
		$this->c_id = $cc['controllerId'];
		$this->getRequest()
			->setControllerName($cc['realController'])
			->setActionName($cc['realAction'])
			->setParam('lang', $lang);
		$this->getResponse()->setHttpResponseCode(404);
	}

	Zend_Registry::set('params', $reqParams);
	Zend_Registry::set('req_controller', $reqController);
	Zend_Registry::set('req_action', $reqAction);
	$_SESSION['lang'] = $lang;
	$_SESSION['lang_id'] = $isoj[$lang];
	$_SESSION['lang_collation'] = $collationj[$lang];

 	Zend_Registry::set('c_id', $this->c_id);

  	// echo $this->getRequest()->getControllerName()." - ".$this->getRequest()->getActionName();exit;

	$acl = It6_Acl_Factory::newAcl(array(
		'adminDb' => Zend_Registry::get('admindb'),
	));
	Zend_Registry::set('acl', $acl);

  	 //$transFile = ROOT."web/application/translate/translate_".$this->getRequest()->getControllerName()."_".$this->getRequest()->getActionName()."_".$isoj[$_SESSION['lang']].".php";
  	 //if ( file_exists($transFile) )
  	 //	require_once($transFile);
  	 $translate = new It6_Translate_Web($isoj[$_SESSION['lang']], $this->getRequest()->getControllerName(), $this->getRequest()->getActionName());
  	 Zend_Registry::set('translate', $translate);
  	 Zend_Registry::set('Zend_Translate', new Zend_Translate(array(
  	 	'adapter' => 'It6_Translate_Adapter_BetService',
  	 	'content' => 'dummy',
  	 	'locale' => $_SESSION['lang'],
  	 	'disableNotices' => true,
  	 	'registryKey' => 'translate'
  	 )));

	}

	/**
	* Zde se definuji vyjimky pro menu resp. Controller a action
	* @return void
	*/
	private function menuExceptions() {
		$select = Zend_Registry::get('db')->select()
			->from(array('a' => 'controller_convert'),array('a.c_id', 'a.real_controller', 'a.real_action', 'b.lang_id', "lang" => 'b.iso'))
			->join(array('b' => 'jazyky'), 'a.lang_id = b.lang_id')
			->where('a.req_controller = ?', $this->getRequest()->getControllerName())
			->where('b.iso = ?', $_SESSION['lang']);
		$stm = $select->query();
		$row = $stm->fetchAll();

		#Sazeni, Live sazeni#
		if (count($row) > 0) {
			if($row[0]['c_id'] == 9 || $row[0]['c_id'] == 2  || $row[0]['c_id'] == 4 || $row[0]['c_id'] == 5 || $row[0]['c_id'] == 6 || $row[0]['c_id'] == 7) {
				Zend_Registry::set('c_id', $row[0]['c_id']);
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
}