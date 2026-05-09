<?php

class StaticPageController extends Zend_Controller_Action {

	const PAGENAME_404 = 'not-found';
	const PAGENAME_503 = 'forbidden';
	const PAGENAME_SESSION_TIMEOUT = 'session-timeout';

	const LEFT_RIGHT_NEWS = 'leftRightNews';



	public function init() {
		$this->view->addHelperPath('views/helpers', 'My_View_Helper');
		Models_BasicRender::render($this->view,$this->_request);
		Models_Ajax_Ticket::get($this->view);		
	}

	private function getPageData($name, $langId) {
		$page = Zend_Registry::get('db')->select()
			->from(array('sp' => 'static_page'))
			->joinLeft(
				array('spl' => 'static_page_layout'),
				'sp.layout_id = spl.layout_id'
			)
			->joinLeft(
				array('cc' => 'controller_convert'),
				"sp.lang_id=cc.lang_id AND cc.real_controller='static-page' AND cc.real_action COLLATE utf8_general_ci=sp.page_name",
				array('title', 'description', 'keywords')
			)
			->where('sp.page_name = ?' , It6_Text::camelCaseToDashed($name))
			->where('sp.lang_id = ?', $langId)
			->query()
			->fetchAll();
		return $page;
	}

	private function renderTemplate($page) {
		$template	= $page['template'];
		$layout		= str_replace('.phtml', '', $page['layout_file']);
		/*switch ($template) {
		case 'index':
			Models_Helpers_Panels::leftCol($this->view);
			Models_Helpers_Panels::news($this->view);
			Models_Helpers_Panels::rightCol($this->view);
			break;
		default:
			break;
		}*/
		
		// *** na statické stránky je navržen 3sloupcový layout
		// *** v případě potřeby jiného layoutu viz $page[] a zakomentovaný kód
		Models_Helpers_Panels::leftAndRightColAndNews($this->view);
		//$this->prepareRender($page);
		//$this->_helper->layout->setLayout($layout);
		$this->view->page = $page;
		$this->render($template);
	}

	/**
	 * According to configuration constant GLOBAL_CACHE_ERROR_PAGE_EXPIRATION
	 * this method sets global cache expiration for error pages (4XX, 5XX).
	 */
	private function setErrorPageCacheExpiration() {
		if (defined('GLOBAL_CACHE_ERROR_PAGE_EXPIRATION')) {
			if (0 > GLOBAL_CACHE_ERROR_PAGE_EXPIRATION) {
				It6_GlobalCache::turnOff();
			}
			else {
				It6_GlobalCache::maxExpiration(GLOBAL_CACHE_ERROR_PAGE_EXPIRATION);
			}
		}
	}

	public function __call($method, $args) {
		if (1 == preg_match('/^(.+)Action$/', $method, $matches)) {
			$langId = $_SESSION['lang_id'];
			$rows = $this->getPageData($matches[1], $langId);
			if (!empty($rows)) {
				$page = $rows[0];
				if (!$page['visible']) {
					$rows = $this->getPageData(self::PAGENAME_503, $langId);
					$page = $rows[0];
				}
			}
			else {
				$rows = $this->getPageData(self::PAGENAME_404, $langId);
				$page = $rows[0];
			}
			$name = (empty($page) || empty($page['page_name']) ? '' : $page['page_name']);
			switch ($name) {
			case self::PAGENAME_404:
				$this->getResponse()->setHttpResponseCode(404);
				It6_GlobalCache::setResponseCode(404);
				$this->view->googleAnalyticsTracking = "['_trackEvent', 'Error', '404', 'page: ' + document.location.pathname + document.location.search + ' ref: ' + document.referrer ]";
				$this->setErrorPageCacheExpiration();
				break;
			case self::PAGENAME_503:
				$this->getResponse()->setHttpResponseCode(503);
				It6_GlobalCache::setResponseCode(503);
				$this->setErrorPageCacheExpiration();
				break;
			case self::PAGENAME_SESSION_TIMEOUT:
				if (2 == $GLOBALS['ses_status'] || 3 == $GLOBALS['ses_status'])
					$this->getResponse()->setRedirect($this->view->urlSet(49));
				break;
			default:
				break;
			}
			$this->renderTemplate($page);
			return;
		}

		throw new Exception("Invalid method $method called", 500);
	}

	private function prepareRender($page) {
		Models_BasicRender::render($this->view,$this->_request);

		$data = explode(';', $page['required_data']);
		if(in_array(self::LEFT_RIGHT_NEWS, $data))
			Models_Helpers_Panels::leftAndRightColAndNews($this->view);

	}

}
