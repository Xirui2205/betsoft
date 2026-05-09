<?php
class GlobalCacheFrameController extends Zend_Controller_Action {

	const COOKIE_SUPER_HP = 'SuperHpVisited';

	public function init() {
		$this->view->addHelperPath('views/helpers/', 'My_View_Helper');
		$this->_helper->layout->disableLayout();
	}

	public function logoutAction() {
		Models_BasicRender::render($this->view, $this->_request);
		It6_GlobalCache::useSession();

		It6_GlobalCache::maxExpiration(60);

		$userId = (Zend_Registry::isRegistered('user_id') ? Zend_Registry::get('user_id') : 0);
		$this->view->bonus = (empty($userId) ? false : Zend_Registry::get('ws')->Campaign->getEntryBonusInfo($userId));
		if ( Zend_Registry::isRegistered('user_id') && intval(Zend_Registry::get('user_id')) > 0 ) {
			
			$this->view->showVisitHappyHour = Zend_Registry::get('ws')->Campaign->validate(
				'HappyHourVisit',
				'get',
				array(
					'userId' => intval(Zend_Registry::get('user_id'))
				)
			);
		}
		//if ( Zend_Registry::isRegistered('user_id') )
		//It6_GlobalCache::setUserTag(Zend_Registry::get('user_id'));
	}

	public function headAction() {
		It6_GlobalCache::useSession();
	}

	public function liveCalendarSmallAction() {
		It6_GlobalCache::useSession();
		$lang = $this->_request->getParam('lang');
		$key = It6_GlobalCache::createLocalizedKey(It6_GlobalCache::KEY_PREFIX_LIVE_CALENDAR_SMALL, $lang);
		$html = It6_GlobalCache::getKey($key, $fetched);
		//if (!$fetched) {
			$this->view->liveOnline = Zend_Registry::get('ws')->MatchLive->getHpCalendarSmallOnLine();
			$this->view->liveComing = Zend_Registry::get('ws')->MatchLive->getHpCalendarSmallComing();
			$html = $this->view->render('global-cache-frame/live-calendar-small-content.phtml');
			unset($this->view->liveOnline);
			unset($this->view->liveComing);
			It6_GlobalCache::setKey($key, $html);
		//}
		$this->view->content = It6_Models_Livebetting::replaceSessionIdPlaceholders($html);
		It6_Controller_Util::setResponseExpiration($this->_response, time() + 5*60);
	}

	public function liveCalendarAction() {
		It6_GlobalCache::useSession();
		$lang = $this->_request->getParam('lang');
		$key = It6_GlobalCache::createLocalizedKey(It6_GlobalCache::KEY_PREFIX_LIVE_CALENDAR, $lang);
		$html = It6_GlobalCache::getKey($key, $fetched);
		//if (!$fetched) {
			$this->view->liveCalendarMatches = Zend_Registry::get('ws')->MatchLive->getHpCalendar();
			//$this->view->liveCom = Models_LiveBetting_Calendar::getComming(false);
			$html = $this->view->render('global-cache-frame/live-calendar-content.phtml');
			unset($this->view->liveCalendarMatches);
			It6_GlobalCache::setKey($key, $html);
		//}
		$this->view->content = It6_Models_Livebetting::replaceSessionIdPlaceholders($html);
		It6_Controller_Util::setResponseExpiration($this->_response, time() + 5*60);
	}


	public function footAction(){
		It6_GlobalCache::useSession();
		$ws = Zend_Registry::get("ws");
		$this->view->newspapersFileName = $ws->Newspapers->getActualFilePath();
	}
}