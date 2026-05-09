<?php

class SuperhomeController extends Zend_Controller_Action {

	public function init() {
		$this->view->addHelperPath('views/helpers/', 'My_View_Helper');

		#Nacte data z tiketu#
		Models_Ajax_Ticket::get($this->view);
		#User Data#

		$this->view->freeBet = Models_Ajax_Ticket::freeBet($this->view);

		require_once "class/class.Date.php";

		/* Initialize action controller here */

		/*  $response = $this->getResponse();
		 $response->insert('sidebar', $this->view->render('sidebar.phtml'));
		 <?php echo $this->layout()->sidebar; ?>
		 */
	}

	public function indexAction() {

		$paramId = 105; // web.superHp.enabled
		$superHp = It6_GlobalCache::getKey(It6_GlobalCache::KEY_PREFIX_GPARAM . $paramId, $fetched);
		if (!$fetched) {
			$superHp = Zend_Registry::get('ws')->Parameter->getGlobalParameter('web.superHp.enabled');
			It6_GlobalCache::setKey(It6_GlobalCache::KEY_PREFIX_GPARAM . $paramId, $superHp);
		}
		if (!$superHp) {
			$this->_redirect($this->view->UrlSet(1));
		}


		Models_BasicRender::render($this->view,$this->_request);

		$menu = new Models_Navigation_SportMenu;

		//44 = id of this controller/action combination; see `vic_main`.`controller_convert`.`c_id`
		$this->view->promo		= Models_Marketing_Promo::getPromos(44, $_SESSION['lang_id']);
		$banners				= Zend_Registry::get('ws')->Banner->getByControllerAndLanguage(44, $_SESSION['lang_id']);
		$banners				= It6_ArrayWrapper::toAssocLikeArray($banners, 'locationId');

		$this->view->content1	= reset($banners[1]);
		$this->view->content2	= reset($banners[2]);
		$this->view->content3	= reset($banners[3]);
	}
}
