<?php

class UserDuplicityController extends controllers_UserAbstractController {

	protected $viewSectionId		= 241;
	protected $updateSectionId		= 240;
	protected $viewMatchedSectionId	= 276;

	protected $userId;
	protected $ws;


	public function init() {
		parent::init();
		$this->view->viewMatchedSectionId = $this->viewMatchedSectionId;
	}

	public function viewAction() {
		$this->_helper->layout->setLayout('empty');

		$params = $this->_request->getPost();
		$exts = array();
		$extOrder = new It6_WsExtension_Client_Order('order1', array('time DESC'));
		$exts[] = $extOrder;

/*
		$lister = new Lister('udup', 20, 0, 0, 15);
		$lister->setPost(true);
		$lister->updateFromParams($params);
		$extPager = new It6_WsExtension_Client_Pagination('pager1', $lister->getCount(), $lister->getFrom());
		$exts[] = $extPager;
		$tracks = $this->ws->ext($exts)->UserTracking->getAllByUserId($this->userId);
		$lister->setTotalCount($extPager->getParam(It6_WsExtension_Pagination::PARAM_TOTAL));
		$this->view->lister = $lister;
*/
		$pagerData = (empty($params['udupPager']) ? array('recsPerPage' => 20) : $params['udupPager']);
		$pager = new It6_WsForm_Paginator($pagerData['recsPerPage'], 'udupPager');
		$pager->getExtension($pagerData, $exts);
		
		$tracks = $this->ws->ext($exts)->UserTracking->getAllByUserId($this->userId);

		$this->view->pager	= $pager->getLayout(null, null,  $exts['udupPager']->getResponse());
		$this->view->tracks = $tracks;
	}

	public function viewMatchedAction() {
		$params = $this->_request->getPost();
		$this->_helper->layout->setLayout('empty');
		$this->view->matchByCookie = !empty($params['byCookie']);
		$this->view->matchByIp = !empty($params['byIp']);
		$pagerData = (empty($params['umatchPager']) ? array('recsPerPage' => 20) : $params['umatchPager']);
		$limit = $pagerData['recsPerPage'];
		$offset = (empty($pagerData['pageNum']) ? 0 : $pagerData['pageNum'] - 1) * $limit;
		$matches = $this->ws->UserTracking->getMatchedTracks(
			$this->userId, $this->view->matchByCookie, $this->view->matchByIp, $limit, $offset
		);
		$pager = new It6_WsForm_Paginator($pagerData['recsPerPage'], 'umatchPager');
		$this->view->pager	= $pager->getLayout(null, null, array(
			It6_WsExtension_Pagination::PARAM_TOTAL => $matches['total'],
			It6_WsExtension_Pagination::PARAM_OFFSET => $offset,
		));
		$this->view->matches = $matches['data'];
	}

	public function updateAction() {
		$this->_helper->layout->setLayout('empty');

	}
}
