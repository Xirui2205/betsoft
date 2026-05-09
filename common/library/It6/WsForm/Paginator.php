<?php

class It6_WsForm_Paginator {

	private $recsPerPage = 10;
	private $offset;
	private $extName;
	private $recsPerPageOpts = array(
		10 => 10,
		20 => 20,
		30 => 30,
		0 => 'all'
	);

	private static $LAYOUT_FOLDER		= 'ws-form/';
	private static $PAGINATOR_CONTROLS	= 'standard-paginator-controls';
	private static $LAYOUT				= 'standard-paginator';



	public function __construct($recsPerPage, $extName=null) {
		if(is_numeric($recsPerPage)) {
			$this->recsPerPage = (integer)$recsPerPage;
			}
		
		$this->extName = (empty($extName) ? 'paginator' : $extName);
	}

	public function getLayout($paginatorControls=null, $layout=null, $paginatorReturnData) {
		if($paginatorControls == null)
			$paginatorControls = self::$PAGINATOR_CONTROLS;
		if($layout == null)
			$layout = self::$LAYOUT;



		$layoutName = self::$LAYOUT_FOLDER.$layout;
		$layout = new Zend_Layout();
		$layout->setLayoutPath(LAYOUT_PATH);
		$layout->setLayout($layoutName);

		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($paginatorReturnData[It6_WsExtension_Pagination::PARAM_TOTAL]));
		$recsPerPage = !empty($this->recsPerPage) ? $this->recsPerPage : $paginatorReturnData[It6_WsExtension_Pagination::PARAM_TOTAL];
		$paginator->setDefaultItemCountPerPage($recsPerPage);
		if (isset($paginatorReturnData[It6_WsExtension_Pagination::PARAM_OFFSET]))
			$this->offset = $paginatorReturnData[It6_WsExtension_Pagination::PARAM_OFFSET];
		else if (isset($paginatorReturnData[It6_WsExtension_Pagination::PARAM_PAGE]))
			$this->offset = ($paginatorReturnData[It6_WsExtension_Pagination::PARAM_PAGE] - 1) * $this->recsPerPage;
		$paginator->setCurrentPageNumber($this->offset/$recsPerPage + 1);

		$layout->paginatorName		= $this->extName;
		$layout->paginator			= $paginator;
		$layout->recsPerPage		= $this->recsPerPage;
		$layout->recsPerPageOpts	= $this->recsPerPageOpts;
		$layout->paginatorControls	= self::$LAYOUT_FOLDER.$paginatorControls.'.phtml';

		return $layout->render();
	}

	public function getExtName() {
		return $this->extName;
	}

	public function getExtension($paginatorData, &$extensions) {
		if(isset($paginatorData['pageNum']))
			$this->offset = ($paginatorData['pageNum']-1) * $this->recsPerPage;
		else if(isset($paginatorData['pageNext']))
			$this->offset = ($paginatorData['pageNumNext']-1) * $this->recsPerPage;
		else if(isset($paginatorData['pagePrev']))
			$this->offset = ($paginatorData['pageNumPrev']-1) * $this->recsPerPage;
		else if(isset($paginatorData['pageFirst']))
			$this->offset = ($paginatorData['pageNumFirst']-1) * $this->recsPerPage;
		else if(isset($paginatorData['pageLast']))
			$this->offset = ($paginatorData['pageNumLast']-1) * $this->recsPerPage;
		else if(isset($paginatorData['pageNumCur']))
			$this->offset = ($paginatorData['pageNumCur']-1) * $this->recsPerPage;
		else
			$this->offset = 0;

		return ($extensions[$this->extName] = new It6_WsExtension_Client_Pagination($this->extName, $this->recsPerPage, $this->offset));
	}



	public function getRecsPerPage() {
		return $this->recsPerPage();
	}
}
