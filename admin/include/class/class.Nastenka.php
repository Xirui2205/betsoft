<?php

//
class Nastenka  {

	private $_db = null;
	private $_content = null;
	private $_params = array();


	public function __construct(){
		$this->_db = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
		$this->_db->setFetchMode(DB_FETCHMODE_ASSOC);
   	$sql = "SET NAMES 'utf8'";
  	$this->_db->query($sql);
		$this->init();
	}


	public function init(){

		if(isset($_POST['btnAdd']))
			$this->_addRow($_POST['s_nazev'], $_POST['s_text'], $_POST['nl_priorita'], $_SESSION['bookmaker']);

		$grid = null;

		if(isset($_POST['btnSearch']))
			$grid = $this->_getRows($_POST['nl_priorita'], $_POST['nl_bookmaker'], $_POST['date_from'], $_POST['date_to'], $_POST['nl_order'], $_POST['nl_orderType']);
		else
			$grid = $this->_getRows();

		$this->_params['rows'] = $grid;
		$this->_params['bookmakers'] = $this->_getBookmakers();
	}


	private function _getBookmakers(){
		$sql = "
			SELECT a.jmeno,a.prijmeni, a.bookmaker_id
			FROM bookmaker a
			WHERE a.zakazany = 0
		";

		return $this->_db->query($sql);
	}


	private function _addRow($subject, $body, $priority, $bookmaker_id){
		$body = nl2br($body);
		$subject = Help::Slash($subject);
		$body = Help::Slash($body);
		$priority = intval($priority);
		$bookmaker_id = intval($bookmaker_id);
		$sql = "INSERT INTO book_board (subject, body, priority, bookmaker_id, date) VALUES ('$subject', '$body', $priority, $bookmaker_id, '" . It6_Date::dbNow() . "')";
		$this->_db->query($sql);
	}


	private function _getRows($priority = null, $bookmakerId = null, $date_from = null, $date_to = null, $orderBy = 1, $typeOrder = 0){
		$priority  = intval($priority);
		if ($priority < 1)
			$priority = 'NULL';

		$orderType = 'DESC';
		if($typeOrder == 1){
			$orderType = 'ASC';
		}

		if(intval($bookmakerId) < 1)
			$bookmakerId = 'NULL';

		if(empty($date_from) && !It6_Date::checkFormat($date_from)){
			$date_from = 'NULL';
		}
		else{
			$date_from = "'" . It6_Date::toDb($date_from) . "'";
		}

		if(empty($date_to) && !It6_Date::checkFormat($date_to)){
			$date_to = 'NULL';
		}
		else{
			$date_to = "'" . It6_Date::toDb($date_to) . "'";
		}

		$orderBy  = intval($orderBy);
		if ($orderBy < 1)
			$orderBy = 1;

		$sql = "SELECT
					a.id,
					a.bookmaker_id,
					a.date,
					a.subject,
					a.body,
					a.priority,
					b.jmeno,
					b.prijmeni,
					b.nick
				FROM book_board a
				JOIN bookmaker b ON (a.bookmaker_id = b.bookmaker_id)
				WHERE (a.bookmaker_id = $bookmakerId OR $bookmakerId IS NULL)
					AND ((a.date >=$date_from AND a.date<=$date_to) OR ($date_from IS NULL AND $date_to IS NULL))
					AND (a.priority = $priority OR $priority IS NULL)
				ORDER BY $orderBy $orderType
				LIMIT 50";

		return $this->_db->query($sql);

	}

	public function getContent(){
		extract($this->_params);
		ob_start();
			require 'Template/Nastenka/index.phtml';
			$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}



}
