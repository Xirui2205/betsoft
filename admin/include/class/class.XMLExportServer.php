<?php
/**
 * @package    XML
 */

/**
 * Trida inicializuje bonusy
 *
 * 
 * <code>
 * 
 * </code>
 *
 * @package    Ciselniky
 */
 
class XMLExportServer{


/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */              
private  $dbGame;

/**
 * druhy sazek pro export
 * @access private
 * @var array
 */              
private  $druh_id;

/**
 * urcuje zda bylo spojeni na db predano nebo jsme si ho muslei vytvorit
 * @access private
 * @var bool
 */             
private $connect = false;

/**
 * urcuje zda byl vypsan clekovy vitez
 * @access private
 * @var bool
 */             
private $winner = false;

/**
 * xml pro celkvoeh viteze
 * @access private
 * @var bool
 */             
private $winner_ar = array();

/**
 * xml pro celkvoeh viteze
 * @access private
 * @var bool
 */             
private $timex;

/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param PEAR::DB $dbGame objekt spojeni s databazi
*/
public function __construct($dbGame = NULL) {

		$dbGameZend = It6_Controller_Plugin_SetController::connectDbWeb();
		It6_Controller_Plugin_SetController::connectDbAdmin();

		$admindb = Zend_Registry::get('zdb_admin');

		$translate = new It6_Translate_Admin(1, $dbGameZend);
		Zend_Registry::set('translate', $translate);

		//TODO: use constant from config instead of class constants It6_WS::* here
		$autoloader = Zend_Loader_Autoloader::getInstance();

		Zend_Registry::set('autoloader', $autoloader);
		$autoloader->registerNamespace('It6_');	
		$autoloader->registerNamespace('Models_');
		$autoloader->registerNamespace('DecoratorForms_');
		$autoloader->pushAutoloader(new It6_AutoloaderAdmin());
		
		$client = new Zend_XmlRpc_Client(WEB_SERVICE_URL);
		
	if($dbGame == NULL || !is_object($dbGame)) {
		$this->dbGame = DbUtil::connectWebDb();
		$this->connect = true;
	} else {
		$this->dbGame = $dbGame;
	}
}
  
  /**
 * Tato metoda nastavuje jake druhy budeme exportovat a jake callback je budou obsluhovat 
 * return void
 */
private function DataToExport() {
	$this->druh_id = array(
		19 => "ThreewayOdds",
		165 => "ThreewayOdds", // full time match
		18 => "HandicapOdds",
		47 => "SpreadOdds", // asian handicap
		20 => "OverUnder",
		45 => "OverUnder", // over under game
		22 => "TwowayOdds",
		25 => "SpreadOdds",
		31 => "WinnerOdds",
		24 => "DoubleChance",
		16 => "FirstHalf",
		32 => "WinnerOdds",
	);
}
 
 /**
 * Tato metoda najde vsechna potrebna data
 * return bool
 */
public function PrepareData(){
	$im = $xml = "";
	$data = array();

	$this->DataToExport();

	foreach($this->druh_id as $k=>$h) {
		$im .= $k.",";
	}

	$im = substr($im,0,-1);

	/* $sql = "SELECT a.sazka_id, a.platna_od, a.platna_do, a.udalost_id, a.typ_id, a.podtyp_id, a.text, a.kurz, a.nazev, a.sloupec_id, b.nazev AS udalost_nazev, e.nazev AS oblast_nazev, c.nazev AS sport_nazev, c.sport_id
	FROM sazka_pohled a
	INNER JOIN udalost b ON a.udalost_id = b.udalost_id
	INNER JOIN oblast e ON b.oblast_id = e.oblast_id
	INNER JOIN sport c ON b.sport_id = c.sport_id
	WHERE a.platna_do > '".It6_Date::dbNow()."'
	AND a.status =0
	AND a.overena =0
	AND a.live =0
	AND a.typ_id
	IN ( ".$im." )
	AND a.platny_od = (
	SELECT d.platny_od
	FROM sazka_kurz d
	WHERE d.sazka_id = a.sazka_id
	ORDER BY d.platny_od DESC
	LIMIT 1 )
	ORDER BY a.typ_id";*/

	$sql = "SELECT a.sazka_id, sk.platny_od AS platna_od, a.platna_do, a.udalost_id, a.typ_id, a.podtyp_id, a.text, sk.kurz, ps.nazev, sk.sloupec_id, b.nazev AS udalost_nazev, e.nazev AS oblast_nazev, c.nazev AS sport_nazev, c.sport_id
	FROM sazky a
	INNER JOIN udalost b ON a.udalost_id = b.udalost_id
	INNER JOIN oblast e ON b.oblast_id = e.oblast_id
	INNER JOIN sport c ON b.sport_id = c.sport_id
	INNER JOIN sazka_kurz_aktualni sk ON sk.sazka_id = a.sazka_id
	INNER JOIN podtyp_sloupce ps ON sk.sloupec_id = ps.sloupec_id
	WHERE a.platna_do > '".It6_Date::dbNow()."'
	AND a.status =0
	AND a.overena =0
	AND a.live =0
	AND a.typ_id IN ( ".$im." )
	ORDER BY a.typ_id";

	$res =& $this->dbGame->query($sql);
	if (DB::isError($res)) {
		$this->dbGame->rollback();
		$this->dbGame->autoCommit(true);
		$this->dbGame->disconnect();
		return false;
	}

	$preklad = new Preklady();

	while ($row =& $res->fetchRow()) {
		if (!isset($this->druh_id[$row['typ_id']])) continue;
		if (isset($_GET['sport']) && ctype_digit($_GET['sport']) && $_GET['sport'] != $row['sport_id']) continue;

		if (!isset($data[$row['sazka_id']])) {
			if (isset($_GET['lang_id'])) {
				$help = $preklad->FindPreklad($row['udalost_nazev'],intval($_GET['lang_id'])); $row['udalost_nazev'] = trim($help[intval($_GET['lang_id'])]);
				$help = $preklad->FindPreklad($row['sport_nazev'],intval($_GET['lang_id'])); $row['sport_nazev'] = trim($help[intval($_GET['lang_id'])]);
				$help = $preklad->FindPreklad($row['oblast_nazev'],intval($_GET['lang_id'])); $row['oblast_nazev'] = trim($help[intval($_GET['lang_id'])]);
			} else {
				 $help = $preklad->FindPreklad($row['udalost_nazev'],CZ_LANG_ID); $row['udalost_nazev'] = $help[CZ_LANG_ID];
				 $help = $preklad->FindPreklad($row['sport_nazev'],CZ_LANG_ID); $row['sport_nazev'] = $help[CZ_LANG_ID];
				 $help = $preklad->FindPreklad($row['oblast_nazev'],CZ_LANG_ID); $row['oblast_nazev'] = $help[CZ_LANG_ID];
			}

			$data[$row['sazka_id']]['platna_od'] = strftime('%Y-%m-%d %H:%M:%S',It6_Date::fromDbAsTimestamp($row['platna_od']));
			$data[$row['sazka_id']]['platna_do'] = strftime('%Y-%m-%d %H:%M:%S',It6_Date::fromDbAsTimestamp($row['platna_do']));
			$data[$row['sazka_id']]['udalost_id'] = $row['udalost_id'];
			$data[$row['sazka_id']]['typ_id'] = $row['typ_id'];
			$data[$row['sazka_id']]['podtyp_id'] = $row['podtyp_id'];
			$data[$row['sazka_id']]['text'] = Help::TranslateString($row['text'],CZ_LANG_ID,$this->dbGame);
			$data[$row['sazka_id']]['sport'] = $row['sport_nazev'];
			$data[$row['sazka_id']]['udalost'] = $row['udalost_nazev'];
			$data[$row['sazka_id']]['oblast'] = $row['oblast_nazev'];
			$data[$row['sazka_id']]['sport_id'] = $row['sport_id'];
			$data[$row['sazka_id']]['sazka_id'] = $row['sazka_id'];
		}

		$data[$row['sazka_id']]['sloupec'][$row['sloupec_id']] = round($row['kurz'],2);
		$data[$row['sazka_id']]['sloupec_name'][$row['sloupec_id']] = $row['nazev'];
	}

	foreach ($data as $h) {
		if (method_exists($this,$this->druh_id[$h['typ_id']])) {
			$fce = $this->druh_id[$h['typ_id']];
			$xml .= $this->$fce($h);
		}
	}

	if ($this->winner == true) $xml .= $this->WinnerOddsRun($this->winner_ar);

	$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n<list>\n".$xml."\n</list>";

	$this->PrintXml($xml);

	$key = 'BRE:IDSYNC';
	It6_LocalCache::get($key, $fetched);
	if (!$fetched) {
		if (defined('EXPORT_BR_ID_SYNC_INTERVAL')) {
			$interval = intval(EXPORT_BR_ID_SYNC_INTERVAL);
			if (!empty($interval)) {
				It6_LocalCache::set($key, 1, $interval);
			}
		}
		$this->CompareId();
	}

	$this->dbGame->disconnect();
}
  
  /**
 * Zjiteni a sparovani id nas a betradar
 * return void
 */
private function CompareId() {
	$xml = "";
	$ch = curl_init(EXPORT_URL);
	//$ch = curl_init('http://www.bild.de');

	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt($ch,CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

	$xml = curl_exec ($ch);

	curl_close ($ch);

	$doc = new DOMDocument();
	$doc->loadXML($xml);

	$ids = array();
	foreach ($doc->getElementsByTagName('Match') as $match) {
		$id = $match->attributes->getNamedItem('idOfSource')->value;
		$brId = $match->attributes->getNamedItem('betradarMatchid')->value;
		$ids[$id] = '(' . $this->dbGame->quoteSmart($id) . ',' .  $this->dbGame->quoteSmart($brId) . ')';
	}
	if (empty($ids)) {
		// nothing to synchronize
		return;
	}
	
	$this->dbGame->query('DELETE FROM bet_br_id_sync WHERE 1=1');
	$this->dbGame->query(
			'INSERT INTO bet_br_id_sync(sazka_id,betradar_statistic_id) VALUES ' . implode(',', $ids)
		);
	
	$this->dbGame->query(
			'UPDATE sazky s JOIN bet_br_id_sync m'
			. ' ON s.sazka_id=m.sazka_id AND s.betradar_statistic_id<>m.betradar_statistic_id'
			. ' SET s.betradar_statistic_id=m.betradar_statistic_id'
		);
	$this->dbGame->query(
			'UPDATE bet_br_id_sync m'
			. ' JOIN sazka_kombinace k'
			. ' ON m.sazka_id=k.sazka1_id OR m.sazka_id=k.sazka2_id'
			. ' JOIN sazky s2'
			. ' ON IF(m.sazka_id=k.sazka1_id,k.sazka2_id,k.sazka1_id)=s2.sazka_id AND s2.betradar_statistic_id<>m.betradar_statistic_id'
			. ' SET s2.betradar_statistic_id=m.betradar_statistic_id'
		);
		
	$this->dbGame->query('DELETE FROM bet_br_id_sync WHERE 1=1');
}

/**
 * Celkovy vitez
 * @param array $data sata k sazce
 * return string
 */
public function WinnerOdds(array $data) {
	$xml = '';

	foreach($data['sloupec'] as $k=>$h) {
		//$xml .= "<Competitor url=\"" . DEFAULT_HOST . "/sportsbook/?bet=".$data['sazka_id']."&amp;s=".$k."&amp;e_add=".$data['udalost_id']."\" Team=\"".htmlspecialchars($data['text'])."\" Odds=\"".$h."\" />\n";
		$xml .= "<Competitor Team=\"".htmlspecialchars($data['text'])."\" Odds=\"".$h."\" />\n";
	}

	$xml .= '';

	if (!isset( $this->winner_ar[$data['sport_id']][$data['udalost_id']]['xml']))
		$this->winner_ar[$data['sport_id']][$data['udalost_id']]['xml'] = '';

	$this->winner_ar[$data['sport_id']][$data['udalost_id']]['sazka_id'] = 0;
	$this->winner_ar[$data['sport_id']][$data['udalost_id']]['sport'] = $data['sport'];
	$this->winner_ar[$data['sport_id']][$data['udalost_id']]['oblast'] = $data['oblast'];
	$this->winner_ar[$data['sport_id']][$data['udalost_id']]['udalost'] = $data['udalost'];
	$this->winner_ar[$data['sport_id']][$data['udalost_id']]['platna_do'] = $data['platna_do'];
	$this->winner_ar[$data['sport_id']][$data['udalost_id']]['text'] = $data['text'];
	$this->winner_ar[$data['sport_id']][$data['udalost_id']]['xml'] .= $xml;
	$this->winner = true;
}

/**
 * Konecne umisteni
 * return string
 */
public function WinnerOddsRun($data) {
	$xml = '';

	foreach ($this->winner_ar as $k => $h) {
		foreach ($h as $k2 => $h2) {
			$xml_help ='<!-- Winner odds -->
				<OddsType>Outright</OddsType>
				<OddsData>'.$h2['xml'].'</OddsData>';
			$xml .= $this->HeadOdds($h2, $xml_help);
		}
	}
	return $xml;
}

/**
 * Under over
 * @param array $data sata k sazce
 * return string
 */
  public function SpreadOdds(array $data){
   
/*
   $xml ='<!-- Spread odds -->
          <OddsType>Spread</OddsType>
		  <OddsData>
           <HomeTeam>%s</HomeTeam>
           <AwayTeam>%s</AwayTeam>
		   <SpreadHome>%s</SpreadHome>
		   <SpreadAway>%s</SpreadAway>
           <SpreadOddsHome url="' . DEFAULT_HOST . '/sportsbook/?bet=%d&amp;s=%d&amp;e_add=%d">%.2f</SpreadOddsHome>
		   <SpreadOddsAway url="' . DEFAULT_HOST . '/sportsbook/?bet=%d&amp;s=%d&amp;e_add=%d">%.2f</SpreadOddsAway>
          </OddsData>';
*/
   $xml ='<!-- Spread odds -->
          <OddsType>Spread</OddsType>
		  <OddsData>
           <HomeTeam>%s</HomeTeam>
           <AwayTeam>%s</AwayTeam>
		   <SpreadHome>%s</SpreadHome>
		   <SpreadAway>%s</SpreadAway>
           <SpreadOddsHome>%.2f</SpreadOddsHome>
		   <SpreadOddsAway>%.2f</SpreadOddsAway>
          </OddsData>';
   
	//if(!mb_ereg('^(.+)-(.+)[[:space:]]+<?([-+][0-9]+[,.]?[0-9]*)/([-+][0-9]+[,.]?[0-9]*)>?$',$data['text'],$ar)) return '';
    if(!mb_ereg('^(.+)[[:space:]]+([-+]?[0-9]+[,.]?[0-9]*) -(.+)[[:space:]]+([-+]?[0-9]+[,.]?[0-9]*)$',$data['text'],$ar)) return '';

	$ar[2] = str_replace(",",".",$ar[2]);
	$ar[4] = str_replace(",",".",$ar[4]);
	
	$p = mb_split(" - ",$ar[1],2);
	if(count($p)>1) {
		$ar[3] = (empty($p[2]) ? '' : $p[2]) . $ar[3];
		$ar[1] = $p[1];
	}
	
	//$xml = @sprintf($xml,htmlspecialchars(trim($ar[1])),htmlspecialchars(trim($ar[3])),$ar[2],$ar[4],$data['sazka_id'],152,$data['udalost_id'],$data['sloupec'][152],$data['sazka_id'],153,$data['udalost_id'],$data['sloupec'][153]);
	$xml = @sprintf($xml,htmlspecialchars(trim($ar[1])),htmlspecialchars(trim($ar[3])),$ar[2],$ar[4],$data['sloupec'][152],$data['sloupec'][153]);

	
    return $this->HeadOdds($data,$xml);
  }
  
  
 /**
 * 1 2 sazky
 * @param array $data sata k sazce
 * return string
 */
  public function TwowayOdds(array $data){
   
/*
   $xml ='<!-- Twoway odds -->
          <OddsType>2W</OddsType>
		  <OddsData>
           <HomeTeam>%s</HomeTeam>
           <AwayTeam>%s</AwayTeam>
           <HomeOdds url="' . DEFAULT_HOST . '/sportsbook/?bet=%d&amp;s=%d&amp;e_add=%d">%.2f</HomeOdds>
           <AwayOdds url="' . DEFAULT_HOST . '/sportsbook/?bet=%d&amp;s=%d&amp;e_add=%d">%.2f</AwayOdds>
           </OddsData>
		  ';
*/
   $xml ='<!-- Twoway odds -->
          <OddsType>2W</OddsType>
		  <OddsData>
           <HomeTeam>%s</HomeTeam>
           <AwayTeam>%s</AwayTeam>
           <HomeOdds>%.2f</HomeOdds>
           <AwayOdds>%.2f</AwayOdds>
           </OddsData>
		  ';
   
	list($data['home'],$data['away']) = mb_split("-",$data['text'],2);
	
	
	//$xml = @sprintf($xml,htmlspecialchars(trim($data['home'])),htmlspecialchars(trim($data['away'])),$data['sazka_id'],152,$data['udalost_id'],$data['sloupec'][152],$data['sazka_id'],153,$data['udalost_id'],$data['sloupec'][153]);
	$xml = @sprintf($xml,htmlspecialchars(trim($data['home'])),htmlspecialchars(trim($data['away'])),$data['sloupec'][152],$data['sloupec'][153]);

    return $this->HeadOdds($data,$xml);
  }
  
   /**
 * Under over
 * @param array $data sata k sazce
 * return string
 */
  public function OverUnder(array $data){
   
/*
   $xml ='<!-- Total odds(over/under) -->
          <OddsType>Total</OddsType>
		  <OddsData>
           <HomeTeam>%s</HomeTeam>
           <AwayTeam>%s</AwayTeam>
		   <Total>%.1f</Total>
		   <UnderOdds url="' . DEFAULT_HOST . '/sportsbook/?bet=%d&amp;s=%d&amp;e_add=%d">%.2f</UnderOdds>
		   <OverOdds  url="' . DEFAULT_HOST . '/sportsbook/?bet=%d&amp;s=%d&amp;e_add=%d">%.2f</OverOdds>
          </OddsData>';
*/
   $xml ='<!-- Total odds(over/under) -->
          <OddsType>Total</OddsType>
		  <OddsData>
           <HomeTeam>%s</HomeTeam>
           <AwayTeam>%s</AwayTeam>
		   <Total>%.1f</Total>
		   <UnderOdds>%.2f</UnderOdds>
		   <OverOdds>%.2f</OverOdds>
          </OddsData>';
   
	if(!mb_ereg('^(.+)-(.+)[[:space:]]+<?([0-9]+[,.]?[0-9]*)>?$',$data['text'],$ar)) return '';

	$p = mb_split("-",$ar[1],2);
	if(count($p)>1) {$ar[2] = $p[1] . $ar[2];$ar[1] = $p[0];}
	
	$ar[3] = str_replace(",",".",$ar[3]);
	
	//$xml = @sprintf($xml,htmlspecialchars(trim($ar[1])),htmlspecialchars(trim($ar[2])),$ar[3],$data['sazka_id'],145,$data['udalost_id'],$data['sloupec'][145],$data['sazka_id'],144,$data['udalost_id'],$data['sloupec'][144]);
	$xml = @sprintf($xml,htmlspecialchars(trim($ar[1])),htmlspecialchars(trim($ar[2])),$ar[3],$data['sloupec'][145],$data['sloupec'][144]);

    return $this->HeadOdds($data,$xml);
  }
  
  
 /**
 * 1 0 2 handicap sazky
 * @param array $data sata k sazce
 * return string
 */
  public function HandicapOdds(array $data){
   
/*
   $xml ='<!-- Handicap odds -->
          <OddsType>HC</OddsType>
		  <OddsData>
           <HomeTeam>%s</HomeTeam>
           <AwayTeam>%s</AwayTeam>
		   <Handicap>%s</Handicap>
           <HomeOdds url="' . DEFAULT_HOST . '/sportsbook/?bet=%d&amp;s=%d&amp;e_add=%d" >%.2f</HomeOdds>
           <DrawOdds url="' . DEFAULT_HOST . '/sportsbook/?bet=%d&amp;s=%d&amp;e_add=%d" >%.2f</DrawOdds>
           <AwayOdds url="' . DEFAULT_HOST . '/sportsbook/?bet=%d&amp;s=%d&amp;e_add=%d" >%.2f</AwayOdds>
          </OddsData>';
*/
   $xml ='<!-- Handicap odds -->
          <OddsType>HC</OddsType>
		  <OddsData>
           <HomeTeam>%s</HomeTeam>
           <AwayTeam>%s</AwayTeam>
		   <Handicap>%s</Handicap>
           <HomeOdds>%.2f</HomeOdds>
           <DrawOdds>%.2f</DrawOdds>
           <AwayOdds>%.2f</AwayOdds>
          </OddsData>';
   
	if(!mb_ereg('^(.+)-(.+)[[:space:]]+<?([0-9]+[,.]?[0-9]*:[0-9]+[,.]?[0-9]*)>?$',$data['text'],$ar)) return '';
	
	$p = mb_split("-",$ar[1],2);
	if(count($p)>1) {$ar[2] = $p[1] . $ar[2];$ar[1] = $p[0];}
	
	//$xml = @sprintf($xml,htmlspecialchars(trim($ar[1])),htmlspecialchars(trim($ar[2])),$ar[3],$data['sazka_id'],138,$data['udalost_id'],$data['sloupec'][138],$data['sazka_id'],139,$data['udalost_id'],$data['sloupec'][139],$data['sazka_id'],140,$data['udalost_id'],$data['sloupec'][140]);
	$xml = @sprintf($xml,htmlspecialchars(trim($ar[1])),htmlspecialchars(trim($ar[2])),$ar[3],$data['sloupec'][138],$data['sloupec'][139],$data['sloupec'][140]);
	
    return $this->HeadOdds($data,$xml);
  }
  
 /**
 * Prvni polocas
 * @param array $data sata k sazce
 * return string
 */
  public function FirstHalf(array $data){
   
/*
   $xml ='<!-- first half odds -->
          <OddsType>FH</OddsType>
		  <OddsData>
           <HomeTeam>%s</HomeTeam>
           <AwayTeam>%s</AwayTeam>
           <HomeOdds url="' . DEFAULT_HOST . '/sportsbook/?bet=%d&amp;s=%d&amp;e_add=%d">%.2f</HomeOdds>
           <DrawOdds url="' . DEFAULT_HOST . '/sportsbook/?bet=%d&amp;s=%d&amp;e_add=%d">%.2f</DrawOdds>
           <AwayOdds url="' . DEFAULT_HOST . '/sportsbook/?bet=%d&amp;s=%d&amp;e_add=%d">%.2f</AwayOdds>
          </OddsData>
		  ';
*/
   $xml ='<!-- first half odds -->
          <OddsType>FH</OddsType>
		  <OddsData>
           <HomeTeam>%s</HomeTeam>
           <AwayTeam>%s</AwayTeam>
           <HomeOdds>%.2f</HomeOdds>
           <DrawOdds>%.2f</DrawOdds>
           <AwayOdds>%.2f</AwayOdds>
          </OddsData>';
    
	list($data['home'],$data['away']) = mb_split('-',$data['text'],2);
	
	
	//$xml = @sprintf($xml,htmlspecialchars(trim($data['home'])),htmlspecialchars(trim($data['away'])),$data['sazka_id'],138,$data['udalost_id'],$data['sloupec'][138],$data['sazka_id'],139,$data['udalost_id'],$data['sloupec'][139],$data['sazka_id'],140,$data['udalost_id'],$data['sloupec'][140]);
	$xml = @sprintf($xml,htmlspecialchars(trim($data['home'])),htmlspecialchars(trim($data['away'])),$data['sloupec'][138],$data['sloupec'][139],$data['sloupec'][140]);
	
    return $this->HeadOdds($data,$xml);
  }
  
 /**
 * Dvojitasance
 * @param array $data sata k sazce
 * return string
 */
  public function DoubleChance(array $data){
   
/*
   $xml ='<!-- Double chance odds -->
          <OddsType>DC</OddsType>
		  <OddsData>
           <HomeTeam>%s</HomeTeam>
           <AwayTeam>%s</AwayTeam>
            <HomeDrawOdds url="' . DEFAULT_HOST . '/sportsbook/?bet=%d&amp;s=%d&amp;e_add=%d">%.2f</HomeDrawOdds>
           <AwayDrawOdds url="' . DEFAULT_HOST . '/sportsbook/?bet=%d&amp;s=%d&amp;e_add=%d">%.2f</AwayDrawOdds>
           <HomeAwayOdds url="' . DEFAULT_HOST . '/sportsbook/?bet=%d&amp;s=%d&amp;e_add=%d">%.2f</HomeAwayOdds>
          </OddsData>
		  ';
*/
   $xml ='<!-- Double chance odds -->
          <OddsType>DC</OddsType>
		  <OddsData>
           <HomeTeam>%s</HomeTeam>
           <AwayTeam>%s</AwayTeam>
            <HomeDrawOdds>%.2f</HomeDrawOdds>
           <AwayDrawOdds>%.2f</AwayDrawOdds>
           <HomeAwayOdds>%.2f</HomeAwayOdds>
          </OddsData>';
   
	list($data['home'],$data['away']) = mb_split('-',$data['text'],2);
	
	
	//$xml = @sprintf($xml,htmlspecialchars(trim($data['home'])),htmlspecialchars(trim($data['away'])),$data['sazka_id'],141,$data['udalost_id'],$data['sloupec'][141],$data['sazka_id'],143,$data['udalost_id'],$data['sloupec'][143],$data['sazka_id'],142,$data['udalost_id'],$data['sloupec'][142]);
	$xml = @sprintf($xml,htmlspecialchars(trim($data['home'])),htmlspecialchars(trim($data['away'])),$data['sloupec'][141],$data['sloupec'][143],$data['sloupec'][142]);
	
    return $this->HeadOdds($data,$xml);
  }
  
 /**
 * 1 0 2 sazky
 * @param array $data sata k sazce
 * return string
 */
  public function ThreewayOdds(array $data){
   
/*
   $xml ='<!-- Threeway odds -->
          <OddsType>3W</OddsType>
		  <OddsData>
           <HomeTeam>%s</HomeTeam>
           <AwayTeam>%s</AwayTeam>
            <HomeOdds url="' . DEFAULT_HOST . '/sportsbook/?bet=%d&amp;s=%d&amp;e_add=%d">%.2f</HomeOdds>
           <DrawOdds url="' . DEFAULT_HOST . '/sportsbook/?bet=%d&amp;s=%d&amp;e_add=%d">%.2f</DrawOdds>
           <AwayOdds url="' . DEFAULT_HOST . '/sportsbook/?bet=%d&amp;s=%d&amp;e_add=%d">%.2f</AwayOdds>
           </OddsData>
		  ';
*/
   $xml3w ='<!-- Threeway odds -->
          <OddsType>3W</OddsType>
		  <OddsData>
           <HomeTeam>%s</HomeTeam>
           <AwayTeam>%s</AwayTeam>
            <HomeOdds>%.2f</HomeOdds>
           <DrawOdds>%.2f</DrawOdds>
           <AwayOdds>%.2f</AwayOdds>
           </OddsData>';
   
	list($data['home'],$data['away']) = mb_split('-',$data['text'],2);
	
	
	//$xml = @sprintf($xml,htmlspecialchars(trim($data['home'])),htmlspecialchars(trim($data['away'])),$data['sazka_id'],138,$data['udalost_id'],$data['sloupec'][138],$data['sazka_id'],139,$data['udalost_id'],$data['sloupec'][139],$data['sazka_id'],140,$data['udalost_id'],$data['sloupec'][140]);
	//$xml = @sprintf($xml,htmlspecialchars(trim($data['home'])),htmlspecialchars(trim($data['away'])),$data['sloupec'][138],$data['sloupec'][139],$data['sloupec'][140]);
	if (218 == $data['podtyp_id']) { // 6-column subtype
   		//$xmlDc ='<!-- Double chance odds -->
        //	  <OddsType>DC</OddsType>
		// 	  <OddsData>
        //   	    <HomeTeam>%s</HomeTeam>
        //   	    <AwayTeam>%s</AwayTeam>
        //        <HomeDrawOdds>%.2f</HomeDrawOdds>
        //        <AwayDrawOdds>%.2f</AwayDrawOdds>
        //        <HomeAwayOdds>%.2f</HomeAwayOdds>
        //      </OddsData>';
		$xml3w = @sprintf($xml3w,htmlspecialchars(trim($data['home'])),htmlspecialchars(trim($data['away'])),$data['sloupec'][1857],$data['sloupec'][1858],$data['sloupec'][1859]);
		//$xmlDc = @sprintf($xmlDc,htmlspecialchars(trim($data['home'])),htmlspecialchars(trim($data['away'])),$data['sloupec'][1860],$data['sloupec'][1862],$data['sloupec'][1861]);
		//return $this->HeadOdds($data,$xml3w) . $this->HeadOdds($data,$xmlDc);
		return $this->HeadOdds($data,$xml3w);
	}
	else {
		$xml3w = @sprintf($xml3w,htmlspecialchars(trim($data['home'])),htmlspecialchars(trim($data['away'])),$data['sloupec'][138],$data['sloupec'][139],$data['sloupec'][140]);
	    return $this->HeadOdds($data,$xml3w);
	}
  }

	/**
	* sestavuje hlavicku kazde sazky
	* @param array $data sata k sazce
	* @param array $data sata k sazce
	* return string
	*/
	public function HeadOdds(array $data,$xml_data){
		$xml ='
			<OddsObject>
				<Sport>%s</Sport>
				<Category>%s</Category>
				<Tournament>%s</Tournament>
				<MatchId>%d</MatchId>
				<Date>%s</Date>
			'.$xml_data.'
			</OddsObject>';

		$xml = @sprintf($xml,$data['sport'],$data['oblast'],$data['udalost'],$data['sazka_id'],$data['platna_do']);

		return $xml;
	}

	/**
	* Vystup XML
	* @param string $xml vypise xml
	* return string
	*/
	public function PrintXml($xml){
		echo $xml;
	}

	/**
	* Vypsani vystupu v html
	* @param string $xml vypise prevede znaky na entity
	* return string
	*/
	public function PrintHtml($xml){
		echo "<pre>". Help::HTML($xml)."</pre> ";
	}

	public function __destruct() {}
}