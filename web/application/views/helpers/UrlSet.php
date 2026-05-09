<?php
class Zend_View_Helper_UrlSet extends Zend_View_Helper_Abstract {

	public $view;

	/**
	 *  
	 * @param integer $uid
	 * @param string $params
	 * @param string $protocol ignored
	 * @param string $paramsUrl
	 * @return string
	 */ 
	public function urlSet($uid, $params='', $protocol='http://', $paramsUrl='', $withHost = false) {

		static $cache = null;

		if ( null == $cache ) {
			$select = Zend_Registry::get('db')->select()
				->from(
					array('a' => 'controller_convert'),
					array('c_id','a.req_controller','a.req_action'))
				->join(
					array('b' => 'jazyky'),
					"a.lang_id = b.lang_id AND b.iso = '{$_SESSION['lang']}'",
					null);

			$stm  = $select->query();
			$cache = array();
			while ( $row = $stm->fetchObject() ) $cache[$row->c_id] = $row;
		}

		if ( !array_key_exists($uid, $cache) ) {
			It6_Log::err("SetUrl: Unknown uid '$uid'");
			return;
		}

		$h = &$cache[$uid];
		$url = ($withHost ? PROTOCOL . WEBHOST : '') . "/{$_SESSION['lang']}/";
		if ( $h->req_controller != 'index' ) $url .= $h->req_controller . '/';
		if ( $h->req_action != 'index' ) $url .= $h->req_action .'/';
		if (!empty($params) && is_array($params)) $params = It6_Models_ControllerConvert::arrToGetString($params);

		$return_url = $url . $paramsUrl .(!empty($params) > 0 ? "?$params" : '');

		return $return_url;
	}

	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
}