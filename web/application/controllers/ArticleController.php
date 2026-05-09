<?php
class ArticleController extends Zend_Controller_Action {
	
	private $ws;
	public static $articlesSectionId = 105;
	public static $articlesReadSectionId = 106;
	public static $articlesHpSectionId = 107;
	
	public function init() {
		$this->ws = Zend_Registry::get('ws');
		$this->db = Zend_Registry::get('db');
	}
	
	/**
	 * Article list
	 */
	public function indexAction() {
		Models_BasicRender::render($this->view,$this->_request);
		Models_Helpers_Panels::leftAndRightColAndNews($this->view);
		
		$articles = $this->ws->Article->getPublished(null, $_SESSION['lang_id'], true);
		
		$page = $this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($articles);
		$paginator->setItemCountPerPage(Zend_Registry::get('ws')->Parameter->getGlobalParameter('web.articlesArchivePageCount'));
		$paginator->setCurrentPageNumber($page);
		$this->view->paginator=$paginator;		
	}
	
	/**
	 * Read article (detail)
	 */
	public function readAction() {
		Models_BasicRender::render($this->view,$this->_request);
		Models_Helpers_Panels::leftAndRightColAndNews($this->view);
			
		$articleId = explode('/', $_SERVER['REQUEST_URI']);//print_r($articleId); die;
		$articleId = isset($articleId[3]) ? intval($articleId[3]) : false;
		
		$article = $this->ws->Article->getById($articleId);
		if (!$article) {
			$this->_redirect($this->view->urlSet(static::$articlesSectionId));
		}
		$this->view->article = It6_ArrayWrapper::toNativeArray($article);
		
		$this->view->title = $this->view->escape($this->view->article['articleTitle']);
		
		$this->view->tips = array();
		$this->view->betLoadUrl = '';
		
		if ($this->view->article['articleBetId'] > 0) {
			// bet url
			$this->view->betLoadUrl = $this->view->urlSet(84) .
									Zend_Registry::get('ws')->Bet->getSportRegionEventUrl($this->view->article['articleBetId'], $_SESSION['lang_id']) .
									"?betIdFilter=".$this->view->article['articleBetId'];
			
			// bookmaker tips
			$this->view->tips = It6_ArrayWrapper::toNativeArray($this->ws->Article->getBookmakerTipsByArticleId($article['articleId']));
		}

		// not published
		if (($article['articlePublic'] != 1 || strtotime($article['articlePublished']) > time()) // not published
				&&
			(!isset($_GET['k']) || $_GET['k'] !== $this->ws->Article->getPublisherKey($article['articleId'])) // wrong key
				) {
			$this->_redirect($this->view->urlSet(static::$articlesSectionId));
		}
	}
	
	/**
	 * Articles on homepage
	 */
	public function articlesHpAction() {
		$this->_helper->layout->disableLayout();
		
		$limit = intval(Webservice_Parameter::getGlobalParameter(Webservice_Article::PARAM_HOMEPAGE_COUNT));
		if (!$limit > 0) {
			exit;
		}
		
		$articles = It6_ArrayWrapper::toNativeArray($this->ws->Article->getPublished($limit, $_SESSION['lang_id']));
		$this->view->articles = $articles;
	}
	
}
