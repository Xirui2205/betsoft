<?php

class BetradarImportLogController extends It6_Controller_Abstract {

const SECTION_ID = 142;
const LOGGED_LANG = 'cs';

private $paginatorData	= array('recsPerPage' => 30);
private $orderData = array('sport', 'region', 'event', 'importedAt');

private static $TBODY_LAYOUT = 'br-import-log-tbody';

public function init() {
	parent::init();

	$this->view->indexSectionId = self::SECTION_ID;
	//$this->jsIncludes->commonAjax = true;
}

public function indexAction() {
	$inData			= $this->getRequest()->getParams();
	//if(!empty($inData['filter']))
	//	$this->filterData = $inData['filter'];
	//if(!empty($inData['brImportLogPager']))
	//	$this->paginatorData = $inData['brImportLogPager'];
	if(!empty($inData['order']))
		$this->orderData = array_keys($inData['order']);

	$extensions = array();

	//$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage'], 'brImportLogPager');
	//$extPaginator = $paginator->getExtension($this->paginatorData, $extensions);

	//create table
	$table = new It6_WsForm_Table(array(
		//array(null, null),
		//array('id', 'brImportLogId'),
		array('sport', 'sport'),
		array('region', 'region'),
		array('event', 'event'),
		array('Betradar ID', 'brTournamentId'),
		array('time', 'importedAt'),
	));
	$table->getColumnsExtension($extensions);
	$table->getOrderExtension($this->orderData, $extensions);

	$ws = Zend_Registry::get('ws');
	$logs = $ws->ext($extensions)->BetradarImportLog->getLatest(null);
	$logs = It6_ArrayWrapper::toNativeArray($logs);
	foreach ($logs as &$log)
		$log['importedAt'] = It6_Date::fromDb($log['importedAt']);

	//$this->view->filter		= $filter->getLayout(null, $this->filterData);
	//$this->view->paginator	= $paginator->getLayout(null, null,  $extPaginator->getResponse());
	$this->view->tHead		= $table->getTheadLayout('standard-thead-noorder');
	$this->view->tBody		= $table->getTbodyLayout(self::$TBODY_LAYOUT, $logs);

}

} // class