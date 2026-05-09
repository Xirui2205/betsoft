<?php
class BettingStatisticsController extends It6_Controller_Abstract {

	protected $indexSectionId = 345;
	protected $viewSectionId = 346;
	protected $viewCurrentSectionId = 347;
	protected $toolTipSectionId = 348;

	protected $ws;

	private $filterData = array();
	private $paginatorData = array('recsPerPage' => 10);
	private $orderData = array('betId');

	private $limitColumns = 8;

	private $TBODY_LAYOUT = 'betting-statistics-tbody';
	private $THEAD_LAYOUT = 'betting-statistics-thead';

	public function init() {
		parent::init();
		$this->ws = Zend_Registry::get('ws');

		if (!isset($this->betId)) $this->betId = $this->getRequest()->getParam('betId');

		$this->jsIncludes->bettingStatisticsAjax = true;
		$this->jsIncludes->highCharts = true;
		$this->jsIncludes->highChartsExporting = true;
		$this->jsIncludes->commonAjax = true;
		$this->jsIncludes->jqueryUI = true;

		$this->view->indexSectionId = $this->indexSectionId;
		$this->view->viewSectionId = $this->viewSectionId;
		$this->view->viewCurrentSectionId = $this->viewCurrentSectionId;

		$this->view->betId = $this->betId;
	}

	public function indexAction() {
		$inData = $this->getRequest()->getParams();
		$extensions = array();

		if (!empty($inData['filter']['typeId']) && !in_array('0', $inData['filter']['typeId'])) $selectedEventIds = $inData['filter']['typeId'];
		else $selectedEventIds = array();

		if (!empty($inData['filter']['sportId'])) $selectedSportId = $inData['filter']['sportId'];
		else $selectedSportId = array();

		if (!empty($inData['filter']['betId'])) $betId = $inData['filter']['betId'];
		else $betId = '';

		if (!empty($inData['filter']['alias'])) $betAlias = $inData['filter']['alias'];
		else $betAlias = '';

		if (!empty($inData['filter']['od_start'])) $odStart = $inData['filter']['od_start'];
		else $odStart = '';

		if (!empty($inData['filter']['od_end'])) $odEnd = $inData['filter']['od_end'];
		else $odEnd = '';

		if (!empty($inData['filter']['do_start'])) $doStart = $inData['filter']['do_start'];
		else $doStart = '';

		if (!empty($inData['filter']['do_end'])) $doEnd = $inData['filter']['do_end'];
		else $doEnd = '';

		//prepare wsForm data from $_POST
		if (!empty($inData['filter']))
			$this->filterData = $inData['filter'];
		if (!empty($inData['paginator']))
			$this->paginatorData = $inData['paginator'];
		if (!empty($inData['order']))
			$this->orderData = array_keys($inData['order']);

		$sports = $this->ws->Event->getSportFilterEvents($selectedSportId, DEFAULT_LANG_ID, false);
		$filterHtml = It6_SportEventsFilter::getSportFilterHtml($sports, $selectedSportId, $selectedEventIds, false, true);
		$this->view->eventFilter = $filterHtml;
		$this->view->betIdValue = $betId;
		$this->view->betAliasValue = $betAlias;
		$this->view->odStart = $odStart;
		$this->view->odEnd = $odEnd;
		$this->view->doStart = $doStart;
		$this->view->doEnd = $doEnd;

		if (!empty($odStart)) $odStart = It6_Date::toDb($odStart);
		if (!empty($odEnd)) $odEnd = It6_Date::toDb($odEnd);
		if (!empty($doStart)) $doStart = It6_Date::toDb($doStart);
		if (!empty($doEnd)) $doEnd = It6_Date::toDb($doEnd);

		if (!empty($selectedSportId))
			$filterDef[] = array('?' => array('sportId' => $selectedSportId), 'OP' => '=');
		if (!empty($selectedEventIds))
			$filterDef[] = array('?' => array('eventId' => $selectedEventIds), 'OP' => 'IN (?)');
		if (!empty($betId))
			$filterDef[] = array('?' => array('betId' => $betId), 'OP' => '=');
		if (!empty($betAlias))
			$filterDef[] = array('?' => array('alias' => $betAlias), 'OP' => '=');
		if (!empty($odStart))
			$filterDef[] = array('?' => array('validFromTime' => $odStart), 'OP' => '>=');
		if (!empty($odEnd))
			$filterDef[] = array('?' => array('validFromTime' => $odEnd), 'OP' => '<');
		if (!empty($doStart))
			$filterDef[] = array('?' => array('validToTime' => $doStart), 'OP' => '>=');
		if (!empty($doEnd))
			$filterDef[] = array('?' => array('validToTime' => $doEnd), 'OP' => '<');

		if (isset($inData["filter"]["stav"])) : switch($inData["filter"]["stav"]) {
			case 1:
				$filterDef[] = array(
					array('?' => array('validToTime' => It6_Date::dbNow()), 'OP' => '>'),
					array('?' => array('status' => 0), 'OP' => '=')
				);
			break;
			case 2:
				$filterDef[] = array(
					array('?' => array('validToTime' => It6_Date::dbNow()), 'OP' => '<='),
					array(
						'OP' => 'OR',
						array('?' => array('status' => 0), 'OP' => '='),
						array('?' => array('status' => 2), 'OP' => '=')
					),
				);
			break;
			case 3:
				$filterDef[] = array(
					array('?' => array('verified' => 0), 'OP' => '='),
					array('?' => array('status' => 3), 'OP' => '=')
				);
			break;
			case 4:
				$filterDef[] = array(
					array('?' => array('verified' => 0), 'OP' => '<>'),
					array('?' => array('payedOff' => 1), 'OP' => '<>'),
					array('?' => array('status' => 3), 'OP' => '=')
				);
			break;
			case 500:
				$filterDef[] = array(
					array('?' => array('validToTime' => It6_Date::dbNow()), 'OP' => '>'),
					array('?' => array('status' => 2), 'OP' => '='),
					array('?' => array('statusExt' => 1), 'OP' => '<>')
				);
			break;
			case 501:
				$filterDef[] = array(
					array('?' => array('validToTime' => It6_Date::dbNow()), 'OP' => '>'),
					array('?' => array('status' => 2), 'OP' => '='),
					array('?' => array('statusExt' => 1), 'OP' => '=')
				);
			break;
			case 6:
				$filterDef[] = array(
					array('?' => array('status' => 1), 'OP' => '='),
				);
			break;
			case 8:
				$filterDef[] = array(
					array('?' => array('payedOff' => 1), 'OP' => '='),
				);
			break;
		} endif;

		if (!empty($filterDef)) {
			$extensions[] = new It6_WsExtension_Client_Filter('def-filter', $filterDef);
			$extensions[] = new It6_WsExtension_Client_Columns('columns', array(
				'betId', 'name', 'typeId', 'validFromTime', 'validToTime'
			));

			$types = $this->ws->Event->getTypeEventsByEventOrSportId($selectedEventIds, $selectedSportId, $this->limitColumns);

			$columns = array();
			array_push($columns, 
				array('id', 'betId'), array('name', 'name'), array('validFromTime', 'validFromTime'), 
				array('validToTime', 'validToTime')
			);
			foreach ($types as $type) {
				array_push($columns, array($type->typeName, $type->typeId));
			}

			//create pagination
			$paginator = new It6_WsForm_Paginator($this->paginatorData['recsPerPage']);
			$paginator->getExtension($this->paginatorData, $extensions);

			//create table
			$table = new It6_WsForm_Table($columns);

			$table->getColumnsExtension($extensions, array('isForbiden'));
			$table->getOrderExtension($this->orderData, $extensions);

			//get type list by WS
			$bets = $this->ws->ext($extensions)->Bet->getMainAll();
			$bets = It6_ArrayWrapper::toNativeArray($bets);

			$i = 0;
			foreach ($bets as $bet) {
				// ziskani statistik pro hlavni sazku
				$value = $this->ws->Bet->getStatisticsRelevant($bet["betId"]);
				if (!empty($value)) {
					$bets[$i][$bet["typeId"]] = array();
					array_push($bets[$i][$bet["typeId"]], $value, $bet["betId"]);
				}

				// ziskani vsech vytvorenych podsazek sazky
				$child_bets = $this->ws->Bet->getAllChildBets($bet["betId"]);

				// ziskani statistik pro vsechny podsazky
				foreach ($child_bets as $child_bet) {
					$value = $this->ws->Bet->getStatisticsRelevant($child_bet->betId);
					if (!empty($value)) {
						$bets[$i][$child_bet->typeId] = array();
						array_push($bets[$i][$child_bet->typeId], $value, $child_bet->betId);
					}
				}
				$i++;
			}

			$this->view->paginator = $paginator->getLayout(null, null,  $extensions['paginator']->getResponse());
			$this->view->tHead = $table->getTheadLayout($this->THEAD_LAYOUT, $this->orderData);
			$this->view->tBody = $table->getTbodyLayout($this->TBODY_LAYOUT, $bets);
		} else {
			$this->view->choice = UiUtil::printWarnings(I18n::tr('warn_no_bets_found'));
		}
	}

	public function viewAction() {
		$this->_helper->layout->setLayout('empty');
		$bet = $this->ws->Bet->getById($this->betId);
		$this->view->bet = $bet;

		// ziskani statistik pro hlavni sazku
		$main_bet = $this->ws->Bet->getAllColumnsStatisticsBet($bet->betId, $bet->oddsTypeId);
		$main_bet = It6_ArrayWrapper::toNativeArray($main_bet);
		$this->view->main_bet = $main_bet;

		// ziskani vsech vytvorenych podsazek sazky
		$child_bets = $this->ws->Bet->getAllChildBets($bet["betId"]);
		if (count($child_bets) == 0) $this->view->feedbackMsg = UiUtil::printErrors(array(i18n::tr('no_childbets'))); 
		$this->view->childbets = $child_bets;

		// ziskani sloupcu a statistik pro vsechny podsazky
		foreach ($child_bets as $child_bet) {
			$columns[$child_bet->betId] = $this->ws->Bet->getAllColumnsStatisticsBet($child_bet->betId, $child_bet->oddsTypeId);
		}
		if (!empty($columns)) $this->view->columns = $columns;
	}

	public function viewCurrentAction() {
		$this->_helper->layout->setLayout('empty');
		$bet = $this->ws->Bet->getById($this->betId);
		$this->view->bet = $bet;

		// ziskani statistik pro sazku
		$current_bet = $this->ws->Bet->getAllColumnsStatisticsBet($bet->betId, $bet->oddsTypeId);
		$this->view->current_bet = $current_bet;
	}

	public function toolTipAction() {
		$this->_helper->layout->setLayout('empty');
		$bet = $this->ws->Bet->getById($this->betId);
		$this->view->bet = $bet;

		// ziskani statistik pro hlavni sazku
		$main_bet = $this->ws->Bet->getAllColumnsStatisticsBet($bet->betId, $bet->oddsTypeId);
		$main_bet = It6_ArrayWrapper::toNativeArray($main_bet);
		$this->view->main_bet = $main_bet;

		// ziskani vsech vytvorenych podsazek sazky
		$child_bets = $this->ws->Bet->getAllChildBets($bet["betId"]);
		if (count($child_bets) == 0) $this->view->feedbackMsg = UiUtil::printErrors(array(i18n::tr('no_childbets'))); 
		$this->view->childbets = $child_bets;

		// ziskani sloupcu a statistik pro vsechny podsazky
		foreach ($child_bets as $child_bet) {
			$columns[$child_bet->betId] = $this->ws->Bet->getAllColumnsStatisticsBet($child_bet->betId, $child_bet->oddsTypeId);
		}
		if (!empty($columns)) $this->view->columns = $columns;
	}
}