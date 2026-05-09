<?php
class SmsController extends controllers_AdminAbstractController {
    
    const MODE_DEFAULT = 'default';

	public $indexSectionId = 353;
	public $viewSectionId = 354;

	var $orderColumns = array(
		'id' => array('order'=>'asc', 'active' => ''),
		'type_id' => array('order'=>'asc', 'active' => '')
	);

	private $filterData		= array();
	private $paginatorData	= array('recsPerPage' => 30);
	private $orderData		= array('smsSend DESC');

	private static $TBODY_LAYOUT = 'sms-tbody';

	protected $mode = self::MODE_DEFAULT;
    
    public function init() {
        
		parent::init();
        
        $this->smsId = $this->getRequest()->getPost('smsId');
        $this->view->smsId = $this->smsId;
        $this->smsDetail = null;
        
		//ordering columns
		$col = $this->getRequest()->getPost('column');
		$ord = $this->getRequest()->getPost('order');
		$this->order = array($ord);
        
		$this->view->order = $this->order;

		$this->orderColumns[$col]['order'] = $ord;
		$this->view->orderButton = Models_Utils::getOrderButtons($this->orderColumns, $this->indexSectionId,$col);
        
        $this->jsIncludes->smsAjax = true;        
	}
    
    public function indexAction() {
        $inData			= $this->getRequest()->getParams();
		$extensions		= array();
        
		//process user actions performed from the user lisiting table
		// (no actions)
		
		//prepare wsForm data from $_POST
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['paginator']))
			$this->paginatorData = $inData['paginator'];
		if(!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);

		// make possible to select user from GET param
		if (!isset($this->filterData['id'])) {
			$id = intval($this->getRequest()->getParam('id'));
			if (!empty($id)) {
				$this->filterData['id'] = $inData['filter']['id'] = $id;
			}
		}

        /*
         *  filtry:                     system names (for translation, filters):
         * ---------------------------------------------------------------------
         * Typ SMS (select)             smsTypeName
         * Telefonní číslo (like)       smsPhoneNumber
         * Text zprávy (like)           smsMessageText
         * SMS id                       smsId
         * Odesláno od                  smsSendFrom
         * Odesláno do                  smsSendTo
         */
        
		//create filter        
        $smsTypeOptions	= Models_Utils::getSelectOptions($this->ws->SmsType->getAll(), 'smsTypeId', 'smsTypeName');
		$smsTypeOptions[0]	= i18n::tr('select');
		ksort($smsTypeOptions);
        
		$filterCfg = array(
			array(i18n::tr('sms_type_name'), 'smsTypeName', 'select', array(array('smsTypeIdSms', '=')), null, null, $smsTypeOptions),
            array(i18n::tr('sms_phone_number'), 'smsPhoneNumber', 'text', array(array('smsPhoneNumber', 'LIKE', '%?%'))),
			array(i18n::tr('sms_message_text'), 'smsMessageText', 'text', array(array('smsMessageText', 'LIKE', '%?%'))),
            array(i18n::tr('sms_send_from'), 'smsSendFrom', 'dateTime', array(array('smsSend', '>=', '?')), 'It6_Validate_Date', null, 'start'),
            array(i18n::tr('sms_send_to'), 'smsSendTo', 'dateTime', array(array('smsSend', '<', '?')), 'It6_Validate_Date', null, 'to')
		);
		
		$filter = new It6_WsForm_Filter($filterCfg);
        
		$filter->getExtension($this->filterData, $extensions);

		//create pagination
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
		$paginator->getExtension($this->paginatorData, $extensions);

		//create table
		$tableCfg = array(
			/*array(null, null),*/
            array('sms_phone_number', 'smsPhoneNumber'),
			array('sms_message_text', 'smsMessageText'),
			array('sms_type_name', 'smsTypeName'),
			array('sms_send', 'smsSend', 'dateTime'),
		);
		
		$table = new It6_WsForm_Table($tableCfg);
		$table->getColumnsExtension($extensions, array('isForbiden'));
		$table->getOrderExtension($this->orderData, $extensions);

		//get sms list by WS
		if ( !empty($inData) ) {    
			$sms = $this->ws->ext($extensions)->Sms->getAll();
			$this->view->paginator	= $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
		} else {
			$sms = array();
			$this->view->notFilter = true;
			$this->view->paginator = false;
		}
		
		$sms = It6_ArrayWrapper::toNativeArray($sms);
        
		$this->view->filter		= $filter->getLayout(null, $this->filterData);
		$this->view->tHead		= $table->getTheadLayout(null,$this->orderData);
		$this->view->tBody		= $table->getTbodyLayout(self::$TBODY_LAYOUT, $sms);
    }
    
    public function viewAction() {
		$this->_helper->layout->setLayout('empty');
		$smsDetail = $this->ws->Sms->getById( (integer)$this->smsId );
		$this->view->smsDetail  = It6_ArrayWrapper::toNativeArray($smsDetail);
	}
    
}