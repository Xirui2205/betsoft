<?php

class LogController extends It6_Controller_Abstract {

	protected $viewSectionId = 254;

	private $filterData		= array();
	private $paginatorData	= array('recsPerPage' => 10);
	private $orderData		= array('time DESC');

	private static $TBODY_LAYOUT = 'log-tbody';
	private static $THEAD_LAYOUT = 'log-thead';

	public function init() {
		parent::init();
		$this->view->viewSectionId	= $this->viewSectionId;
		$this->jsIncludes->wsForm	= true;
	}


	public function  viewAction() {
		$inData			= $this->getRequest()->getPost();
		$extensions		= array();

		//prepare wsForm data from $_POST
		if(!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if(!empty($inData['paginator']))
			$this->paginatorData = $inData['paginator'];
		if(!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);


		$constants = It6_Reflection::getClassConstants('It6_Log');
		$tags = array(0 => i18n::tr('Any'));
		foreach ($constants as $name => $c) {
			if (1 == preg_match('/^TAG_/', $name))
				$tags[$c] = $c;
		}

		$filter = new It6_WsForm_Filter(array(
			array('Log Id', 'logId', 'text', array(array('logId', '=')), 'Zend_Validate_Int'),
			array('Tag', 'tag', 'select', array(array('tag', '=')), NULL, NULL, $tags),
			array('IP', 'ip', 'text', array(array('ip', '=')), NULL, NULL, NULL),
			array('User ID', 'user', 'text', array(array('userId', '=')), NULL, NULL, NULL),
			array('Admin ID', 'admin', 'text', array(array('adminId', '=')), NULL, NULL, NULL),
			array('Host ID', 'host', 'text', array(array('hostId', '=')), NULL, NULL, NULL),
			array('Bet ID', 'bet', 'text', array(array('betId', '=')), NULL, NULL, NULL),
			array('Ticket ID', 'ticket', 'text', array(array('ticketId', '=')), NULL, NULL, NULL),
			array('Coupon ID', 'coupon', 'text', array(array('couponId', '=')), NULL, NULL, NULL),
			array('Priority', 'priority', 'text', array(array('priority', '=')), 'Zend_Validate_Int'),
			array('Time From', 'timeFrom', 'dateTime', array(
				array(array('DATE(?)' => 'time'), '>= DATE(?)')), 'It6_Validate_Date'),
			array('Time To', 'timeTo', 'dateTime', array(
				array(array('DATE(?)' => 'time'), '<= DATE(?)')), 'It6_Validate_Date'),
			array('Message', 'message', 'text', array(array('message', 'LIKE', '%?%'))),
			array('Arguments', 'args', 'text', array(array('args', 'LIKE', '%?%'))),
			array('Exception', 'exception', 'text', array(array('exception', '>'))),
			array('File', 'file', 'text', array(array('file', '='))),
			array('Line', 'line', 'text', array(array('line', '=')), 'Zend_Validate_Int')
		));
		$filter->getExtension($this->filterData, $extensions);


		//create pagination
		$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
		$paginator->getExtension($this->paginatorData, $extensions);


		//create table
		$table = new It6_WsForm_Table(array(
			array('Log Id', 'log_id'),
			array('Time', 'time'),
			array('Ip', 'ip'),
			array('User', 'user_id'),
			array('Admin', 'admin_id'),
			array('Host', 'host_id'),
			array('Bet', 'bet_id'),
			array('Ticket', 'ticket_id'),
			array('Coupon', 'coupon_id'),
			array('Tag', 'tag'),
			array('Priority', 'priority'),
			array('Message', 'message'),
		/*	array('Arguments', 'args'),
			array('Exception', 'exception'),
			array('File', 'file'),
			array('Line', 'line')*/
		));
		$table->getColumnsExtension($extensions);
		$table->getOrderExtension($this->orderData, $extensions);


		$findQuery = array();
		if ( !empty($inData['filter']) ) {
			foreach( $inData['filter'] as $k => $v ) {
				if ( empty($v) ) continue;
				switch ( $k ) {
					case 'logId'     : $findQuery['_id'] = $v;  break;
					case 'tag'       : $findQuery['tag'] = $v; break;
					case 'ip'        : $findQuery['ip'] = $v; break;
					case 'user'      : $findQuery['user_id'] = $v; break;
					case 'admin'     : $findQuery['admin_id'] = $v; break;
					case 'host'      : $findQuery['host_id'] = $v; break;
					case 'bet'       : $findQuery['bet_id'] = $v; break;
					case 'ticket'    : $findQuery['ticket_id'] = $v; break;
					case 'coupon'    : $findQuery['coupon_id'] = $v; break;
					case 'priority'  : $findQuery['priority'] = $v; break;
					case 'timeFrom'  : 
						if ( empty($findQuery['time']) )
						$findQuery['time'] = array(); 
						$findQuery['time']['$gt'] = new MongoDate(It6_Date::toTimestamp($v));
						break;
					case 'timeTo'    :
						if ( empty($findQuery['time']) )
						$findQuery['time'] = array(); 
						$findQuery['time']['$lt'] = new MongoDate(It6_Date::toTimestamp($v));
						break;
					case 'message'   : $findQuery['message'] = MongoRegex("/$v/"); break;
					case 'args'      : $findQuery['args'] = MongoRegex("/$v/"); break;
					case 'exception' : $findQuery['args'] = MongoRegex("/$v/"); break;
					case 'file'      : $findQuery['file'] = $v; break;
					case 'line'      : $findQuery['line'] = $v; break;
				}
			}
		}

		$pageNum = 0;
		$recsPerPage = 10;
		if ( !empty($inData['paginator']) ) {
			foreach( $inData['paginator'] as $k => $v ) {
				switch( $k ) {
					case 'recsPerPage':
						$recsPerPage = $v;
						break;
					case 'pageNum':
						$pageNum = intval($v) - 1;
						break;
				}
			}
		}
		
		$orderQuery = array();
		if ( !empty($inData['order']) ) {
			foreach( $inData['order'] as $k => $v ) {
				$matches = array();
				$matched = preg_match("/^(\\w*)_(ASC|DESC)$/",$k,$matches);
				if ( 0 == $matched )
					continue;
				$col = $matches[1];
				$dir = $matches[2];
				$orderQuery[$col] = $dir=='DESC'?-1:1;
			}
		}


		if ( empty($orderQuery) ) {
		    $orderQuery['time'] = -1;
		}
		

		//get user list by WS
		if ( !empty($inData['filter']) ) {
			//$logs = Zend_Registry::get('ws')->ext($extensions)->Log->getAll();
			$logs = Zend_Registry::get('ws')->Log->getAllWhereOrder($findQuery,$orderQuery,$recsPerPage,$recsPerPage * $pageNum);
		}
		else {
			$logs = array();
			$this->view->notFilter = true;
		}
		
		
		
		//$logs = It6_ArrayWrapper::toNativeArray($logs);

//		var_dump($logs);

		
		$paginatorData = array(
			It6_WsExtension_Pagination::PARAM_PAGE => $pageNum,
			It6_WsExtension_Pagination::PARAM_OFFSET => $recsPerPage * $pageNum,
			It6_WsExtension_Pagination::PARAM_TOTAL => Zend_Registry::get('ws')->Log->getAllWhereCount($findQuery),
		);
		
		$this->view->filter		= $filter->getLayout(null, $this->filterData);
		$this->view->paginator	= $paginator->getLayout(null, null, $paginatorData);
		$this->view->thead		= $table->getTheadLayout(self::$THEAD_LAYOUT,$this->orderData);
		$this->view->tbody		= $table->getTbodyLayout(self::$TBODY_LAYOUT, $logs);
	}

}
