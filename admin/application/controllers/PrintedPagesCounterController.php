<?php
class PrintedPagesCounterController extends It6_Controller_Abstract {

	protected $indexSectionId = 392;
	protected $viewSectionId = 393;

	protected $ws;

	private $filterData = array();
	private $paginatorData = array('recsPerPage' => 10);
	private $orderData = array('id');

	private $TBODY_LAYOUT = 'printed-pages-count-tbody';
	private $THEAD_LAYOUT = 'printed-pages-count-thead';

	public static $TABLE = "parameter";
	public static $TABLE_PREFIX = "pr";
	public static $IDENTITY = "id";
	public static $ENTITY_NAME = "Entities_Parameter";
	public static $CONV = array(
		'id'			    => 'parameterId',
		'name'			    => 'name',
		'value'			    => 'value',
		'type' 			    => 'type',
		'mandatory'		    => 'mandatory',
		'is_editable'	    => 'isEditable',
		'description'	    => 'description',
		'manually_inserted' => 'manually_inserted'
	);
	
	public function init() {
		parent::init();
		$this->ws = Zend_Registry::get('ws');

		if (!isset($this->betId)) $this->betId = $this->getRequest()->getParam('betId');
		$this->jsIncludes->commonAjax = true;
		$this->jsIncludes->jqueryUI = true;

		$this->view->indexSectionId = $this->indexSectionId;
		$this->view->viewSectionId = $this->viewSectionId;

		$this->view->betId = $this->betId;
	}

	public function indexAction() {
		$inData = $this->getRequest()->getParams();
		$extensions = array();

		$hostId = $dateStart = $dateEnd = '';

		if ( !empty($inData['filter']['from']) ) {
			$dateStart = $inData['filter']['from'];
		}

		if ( !empty($inData['filter']['to']) ) {
			$dateEnd = $inData['filter']['to'];
		}

		if ( !empty($inData['filter']['host_id']) ) {
			$hostId = $inData['filter']['host_id'];
		}

		if ( isset($inData['exportXLS']) ) {
			$printedPage = Webservice_PrintedPagesCounter::getData($hostId, $dateStart, $dateEnd);

			// Export to XLS
			header("Expires: 0");
			header("Cache-control: private");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Description: File Transfer");
			header("Content-Type: application/vnd.ms-excel");
			header("Content-disposition: attachment; filename=printed-pages.xls");
		
			print "Datum,host_id,document_type,page_count". "\n";
			foreach($printedPage as $row){
				print It6_Date::fromDb($row['datum']) . "," . $row['host_id'] . "," . $row['document_type'] . "," . $row['page_count'] . "\n";
			}
			exit;
		}

		$this->view->from = $dateStart;
		$this->view->to = $dateEnd;
		$this->view->hostId = $hostId;
	}

}