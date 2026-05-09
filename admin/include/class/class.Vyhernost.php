<?php
/**
 * @package    ciselnik
 */

/**
 * Trida pro praci s cislenikem typu
 *
 *
 * <code>
 *
 * </code>
 *
 * @package    Ciselniky
 */

class Vyhernost extends Template{

/**
 * navratova hodnota
 * @access private
 * @var string
 */
private  $vrat = "";



/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */
private  $dbGame;

/**
 * aktualni sekce
 * @access private
 * @var int
 */
private  $section;

/**
 * pole chyb
 * @access private
 * @var int
 */
private  $error;

/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param int $section id aktualni sekce
* @param PEAR::DB $dbGame objekt spojeni s databazi
*/
public function __construct($section=0) {
	$this->section =  $section;
	$this->dbGame = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
	if (DB::isError($this->dbGame)) {
		throw new ExHandler($this->dbGame->getMessage(),"admin_ex_db");
	}
	$this->dbGame->setFetchMode(DB_FETCHMODE_ASSOC);
	$sql = "set names 'utf8'";
	$res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");
}

/**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
public function runAction() {
    //$logger = new Zend_Log(new Zend_Log_Writer_Firebug());
	//$logger->info('runAction');
	
	if ( !empty($_POST['oblastId']) && $_POST['oblastId'] == 'null'  )
		$_POST['oblastId'] = null;
	if (!empty($_POST['sportId']) && !empty($_POST['oblastId'])) {
		echo $this->loadEventsBySportAndRegion($_POST['sportId'], $_POST['oblastId']);
		exit;
	}
	else if (!empty($_POST['oblastId'])) {
		echo $this->loadEventsByRegion($_POST['oblastId']);
		exit;
	}
	else if(!empty($_POST['sportId'])) {
		echo $this->loadEventsBySport($_POST['sportId']);
		exit;
	}
	else {
		if(isset($_POST['edit']) && isset($_POST['vyhernost']) && is_array($_POST['vyhernost'])){
			
			$this->Edit();
		}
		$this->ShowVyhernost();
	}

	$this->dbGame->disconnect();
}

 /**
 * Edit zaznamu
 * @return void
 */
private function Edit(){
	
	$sth = $this->dbGame->prepare('
			REPLACE INTO bet_settings (
				typ_id,
				sport_id,
				podtyp_id,
				udalost_id,
				kurz_min,
				kurz_max,
				vyhernost_min,
				vyhernost_max,
				risk_limit
			) VALUES (?,?,?,?,?,?,?,?,?)');
	if (PEAR::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");

	foreach($_POST['vyhernost'] as $sport_id => $h){

		foreach($h as $udalost_id => $h2){
			foreach($h2 as $typ_id => $h3){
				foreach($h3 as $podtyp_id => $h4){
					if(!is_numeric($h4['min']) || $h4['min'] < MIN_KURZ || !preg_match("/^\d{1,4}\.?\d{0,2}$/",$h4['min'])){
						$this->error[$udalost_id][$typ_id][$podtyp_id] = 1;
						continue;
					}
					if(!is_numeric($h4['max']) || $h4['max'] > MAX_KURZ || !preg_match("/^\d{1,4}\.?\d{0,2}$/",$h4['max'])){
						$this->error[$udalost_id][$typ_id][$podtyp_id] = 1;
						continue;
					}
					if(!is_numeric($h4['vyh_min']) || $h4['min'] > $h4['max'] || !preg_match("/^[10]\.?\d{0,2}$/",$h4['vyh_min'])){
						$this->error[$udalost_id][$typ_id][$podtyp_id] = 1;
						continue;
					}
					if(!is_numeric($h4['vyh_max']) || $h4['vyh_min'] > $h4['vyh_max'] || !preg_match("/^\d+\.?\d{0,2}$/",$h4['vyh_max'])){
						$this->error[$udalost_id][$typ_id][$podtyp_id] = 1;
						continue;
					}
					if(!is_numeric($h4['risk'])){
						$this->error[$udalost_id][$typ_id][$podtyp_id] = 1;
						continue;
					}


					$res = $this->dbGame->execute($sth,array($typ_id,$sport_id,$podtyp_id,$udalost_id,$h4['min'],$h4['max'],$h4['vyh_min'],$h4['vyh_max'],intval($h4['risk'])));
					if (DB::isError($res)) {
						It6_Log::warn(
							"Error by editing Výhernost $typ_id",
							It6_Log::TAG_BOOKMAKER_OPERATION,
							array('data' => array($typ_id,$sport_id,$podtyp_id,$udalost_id,$h4['min'],$h4['max'],$h4['vyh_min'],$h4['vyh_max'],intval($h4['risk'])))
						);
						throw new ExHandler($res->getMessage(),"admin_ex_db");
					} else {
						It6_Log::notice(
							"Výhernost edited successfully $typ_id",
							It6_Log::TAG_BOOKMAKER_OPERATION,
							array('data' => array('typId' => $typ_id,
									'sportId' => $sport_id,
									'podtypId' => $podtyp_id,
									'eventId' => $udalost_id,
									'bet' => array('min' => $h4['min'], 'max' => $h4['max'], 'vyh_min' => $h4['vyh_min'], 'vyh_max' => $h4['vyh_max'], 'riskLimit' => intval($h4['risk'])
									)))
							);
					}
				}
			}
		}
	}
	
	
	$this->vrat .= UiUtil::printNotice('Data byla aktualizována.');
	'<div class="okmsg">
		Data byla aktualizována.
		<br />
		<span class="red">Červeně</span> jsou označené řádky, které nebyly aktualizované.
	</div>';
	
}


  /**
 * metoda vypise vsechny data
 * @return void
 */
public function ShowVyhernost() {
    //$logger = new Zend_Log(new Zend_Log_Writer_Firebug());

	$sport 					= array();
	$sport_podtyp 	= array();
	$typ 						= array();
	$podtyp 				= array();
	$settings 			= array();
	$constraints 		= array();

	$sport_opt 			= '';
	$udalost_opt 		= '';

	#vyber settings#
	$sql = 'SELECT * FROM bet_settings';

	if (!empty($_REQUEST['sport_opt']))
		$constraints['sport_id'] = $_REQUEST['sport_opt'];
	if (!empty($_REQUEST['udalost']))
		$constraints['udalost_id'] = "'".implode('\',\'',$_REQUEST['udalost'])."'";
	

/*
		var_dump($constraints);
*/
	if (!empty($constraints)){
		$where = '';
		foreach ($constraints as $col => $val){
			 $where .= (empty($where) ? ' WHERE ' : ' AND ');
			 $where .= "$col IN (" . $val.")"; // all constraints numeric
		}
		$sql .= " $where";
	}

	$sql .= ' LIMIT 200';

	//var_dump($sql);

	$res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: editace spotu a typu',"admin_ex_db");

	$i = 0;
	while ($row =& $res->fetchRow()){
		++$i;

		if (empty($settings[$row['udalost_id']]) ) {
			$settings[$row['udalost_id']] = array();
		}
		
		if (empty($settings[$row['udalost_id']][$row['typ_id']]) ) {
			$settings[$row['udalost_id']][$row['typ_id']] = array();
		}

		$settings[$row['udalost_id']][$row['typ_id']][$row['podtyp_id']] = array(
			'min_kurz' => $row['kurz_min'],
			'max_kurz' => $row['kurz_max'],
			'min_vyhernost' => $row['vyhernost_min'],
			'max_vyhernost' => $row['vyhernost_max'],
			'risk' => $row['risk_limit']
		);


		//	$s = &$settings[$row['udalost_id']][$row['typ_id']][$row['podtyp_id']];
		//	$s['min_kurz'] = $row['kurz_min'];
		//	$s['max_kurz'] = $row['kurz_max'];
		//	$s['min_vyhernost'] = $row['vyhernost_min'];
		//	$s['max_vyhernost'] = $row['vyhernost_max'];
		//	$s['risk'] = $row['risk_limit'];

		//	$settings[$row['udalost_id']][$row['typ_id']][$row['podtyp_id']] = ['min_kurz'] = $row['kurz_min'];
		//	$settings[$row['udalost_id']][$row['typ_id']][$row['podtyp_id']]['max_kurz'] = $row['kurz_max'];
		//	$settings[$row['udalost_id']][$row['typ_id']][$row['podtyp_id']]['min_vyhernost'] = $row['vyhernost_min'];
		//	$settings[$row['udalost_id']][$row['typ_id']][$row['podtyp_id']]['max_vyhernost'] = $row['vyhernost_max'];
		//	$settings[$row['udalost_id']][$row['typ_id']][$row['podtyp_id']]['risk'] = $row['risk_limit'];
	}

	$preklad = new Preklady('b6',$this->dbGame);

	#vyber sportu#
	$sql = '
		SELECT a.sport_id,a.nazev AS snazev,b.udalost_id,b.nazev
		FROM sport a
		INNER JOIN udalost b ON a.sport_id=b.sport_id';
		//INNER JOIN sazky s ON b.udalost_id=s.udalost_id';		
		

	$where = ' WHERE 1';
	//$where .= ' AND s.platna_do > NOW()'; 

	if ( isset($_REQUEST['udalost']) && $_REQUEST['udalost'] != 0 )
		$where .= ' AND b.udalost_id IN ('.$constraints['udalost_id'].')';
	if ( isset($_REQUEST['oblast']) && $_REQUEST['oblast'] != 0 )
		$where .= ' AND b.oblast_id IN ('.implode(',',$_REQUEST['oblast']).')';
	
	$sql .= $where;

	//$logger->info($sql);
	
	$res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: editace spotu a typu',"admin_ex_db");

	while ($row =& $res->fetchRow()){

		if(!isset($sport[$row['sport_id']])){
			$pr = $preklad->FindPreklad($row['snazev'],1);
			$sport[$row['sport_id']]['nazev'] =  $pr[1];
			$sport_opt .= '<option '.(isset($_POST['sport_opt']) && intval($_POST['sport_opt']) == $row['sport_id']?'selected="selected"':'').' value="'.$row['sport_id'].'">'.Help::Html($pr[1]).'</option>';
		}

		$pr = $preklad->FindPreklad($row['nazev'],1);
		$sport[$row['sport_id']]['udalost'][$row['udalost_id']] =  $pr[1];
	}

    if(isset($_REQUEST['udalost']) && $_REQUEST['udalost'] != 0){
			$sql = 'select sport_id from udalost where udalost_id IN ('.$constraints['udalost_id'].')';
			$res =& $this->dbGame->query($sql);
			if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: editace spotu a typu',"admin_ex_db");
			if ($row =& $res->fetchRow())$_POST['sport_opt'] = $row['sport_id'];
    }

    if(isset($_POST['sport_opt']) && $_POST['sport_opt']!=0) {
			#vyber sportu, typu a podtypu#
			$sql =
'SELECT a.sport_id, c.nazev, c.typ_id, e.interni_nazev AS tnazev, e.podtyp_id
FROM sport a
INNER JOIN udalost u ON u.sport_id=a.sport_id
INNER JOIN typ_udalost tu ON u.udalost_id=tu.udalost_id
INNER JOIN typ c ON c.typ_id=tu.typ_id
INNER JOIN typ_podtyp d ON d.typ_id=tu.typ_id AND d.sport_id=u.sport_id
INNER JOIN podtyp e ON d.podtyp_id=e.podtyp_id
WHERE
  a.sport_id='.intval($_POST['sport_opt']).'
  AND tu.is_binded=1
ORDER BY c.typ_alias_id';

			$res =& $this->dbGame->query($sql);
			if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: editace spotu a typu',"admin_ex_db");

			while ($row =& $res->fetchRow()){
				if(!isset($typ[$row['typ_id']])) $typ[$row['typ_id']] = $row['nazev'];
				if(!isset($podtyp[$row['podtyp_id']])) $podtyp[$row['podtyp_id']] = $row['tnazev'];
				$sport_podtyp[$row['sport_id']][$row['typ_id']][$row['podtyp_id']] = 1;
			}

    }

		if ( empty($_REQUEST['sport_opt']) && empty($_REQUEST['oblast'])) {
			$eventSelect = It6_Gui_Event::getMultiSelect('udalost',20);
		}
		else if ( empty($_REQUEST['sport_opt']) ) {
			$eventSelect= '<select name="udalost[]" id="udalost" multiselect size="20">'.$this->loadEventsByRegion($_REQUEST['oblast']).'</select>';
		}
		else if ( empty($_REQUEST['oblast']) ) {
			$eventSelect= '<select name="udalost[]" id="udalost" multiselect size="20">'.$this->loadEventsBySport($_REQUEST['sport_opt']).'</select>';
		}
		else {
			$eventSelect= '<select name="udalost[]" id="udalost" multiselect size="20">'.$this->loadEventsBySportAndRegion($_REQUEST['sport_opt'],$_REQUEST['oblast']).'</select>';;
		}
		
		$regionSelect = It6_Gui_Region::getMultiSelect('oblast',20,'onChange="selectOptgroupBySport()"');
		$sportSelect = It6_Gui_Sport::getSelect('sport_opt',20, 'onChange="selectOptgroupBySport()"');
		/*hlavicka*/
		$this->vrat .= "
	
	<h3>Výhernost</h3>
	<form method=\"post\" action=\"?superb=1&section=".$this->section."\">
		<table class=\"table-filter\">
			<tr>
				<td>$sportSelect</td>
				<td>$regionSelect</td>
				<td>$eventSelect</td>
			</tr>
		</table>
		<!--<script>selectOptgroupBySport()</script>-->
		<!--<div id=\"udalost_container\">".'aa'."</div>-->
		<div class=\"actions\"><input type=\"submit\" value=\"Potvrdit\" /></div>
		";

	if(!isset($_POST['sport_opt'])) $sport = array();

	if(count($sport)>0) {

		$this->vrat .=
		'<table class="table-detail">
			<thead>
				<tr>
					<td colspan="6" align="left">
						<a href="demon.php?action[]=risk" target="_blank">Nastavit risk limit (nastaví výchozí hodnoty)</a>
					</td>
				</tr>
				<tr><td colspan="9" class=""><br /><div class="actions"><input type="submit" name="edit" value="Aktualizovat"></div></td></tr>
				<tr>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>Min. kurz</th>
					<th>Max. kurz</th>
					<th>Min. výhernost</th>
					<th>Max. výhernost</th>
					<th>Risk limit</th>
				</tr>
			</thead>';

		foreach($sport as $sport_id=>$sport_ar){

			if(intval($_POST['sport_opt']) != $sport_id) continue;

			$this->vrat .= '<tr><td colspan="9" class="sbet"><strong>'.Help::Html($sport_ar['nazev']).'</strong></td></tr>';

			foreach($sport_ar['udalost'] as $udalost_id=>$udalost_name) {
				$this->vrat .= '<tr><td>&nbsp;</td><td colspan="8" class="sbet2"> <strong>'.Help::Html($udalost_name).'</strong></td></tr>';

				foreach($sport_podtyp[$sport_id] as $typ_id=>$podtyp_ar) {

					$pr = $preklad->FindPreklad($typ[$typ_id],1);
					$this->vrat .= '<tr><td>&nbsp;</td><td>&nbsp;</td><td colspan="7" class="sbet3"> '.Help::Html($pr[1]).'</td></tr>';

						foreach($podtyp_ar as $podtyp_id=>$h){

							$this->vrat .= '
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td class="textleft">
										<span '.(isset($this->error[$udalost_id][$typ_id][$podtyp_id])?'class="red"':'').'>
											'.Help::Html($podtyp[$podtyp_id]).'
										</span>
									</td>
								<td class="textcenter">
										<input
											type="text"
											onkeyup="this.value=RemoveCarka(this.value);"
											name="vyhernost['.$sport_id.']['.$udalost_id.']['.$typ_id.']['.$podtyp_id.'][min]"
											class="sinput2"
											value="'.(isset($settings[$udalost_id][$typ_id][$podtyp_id]['min_kurz'])?$settings[$udalost_id][$typ_id][$podtyp_id]['min_kurz']:MIN_KURZ).'" />
									</td>
									<td class="textcenter">
										<input
											type="text"
											onkeyup="this.value=RemoveCarka(this.value);"
											name="vyhernost['.$sport_id.']['.$udalost_id.']['.$typ_id.']['.$podtyp_id.'][max]"
											class="sinput2"
											value="'.(isset($settings[$udalost_id][$typ_id][$podtyp_id]['max_kurz'])?$settings[$udalost_id][$typ_id][$podtyp_id]['max_kurz']:MAX_KURZ).'" />
									</td>
									<td class="textcenter">
										<input
											type="text"
											onkeyup="this.value=RemoveCarka(this.value);"
											name="vyhernost['.$sport_id.']['.$udalost_id.']['.$typ_id.']['.$podtyp_id.'][vyh_min]"
											class="sinput2"
											value="'.(isset($settings[$udalost_id][$typ_id][$podtyp_id]['min_vyhernost'])?$settings[$udalost_id][$typ_id][$podtyp_id]['min_vyhernost']:MIN_WON).'" />
									</td>
									<td class="textcenter">
										<input
											type="text"
											onkeyup="this.value=RemoveCarka(this.value);"
											name="vyhernost['.$sport_id.']['.$udalost_id.']['.$typ_id.']['.$podtyp_id.'][vyh_max]" class="sinput2"
											value="'.(isset($settings[$udalost_id][$typ_id][$podtyp_id]['max_vyhernost'])?$settings[$udalost_id][$typ_id][$podtyp_id]['max_vyhernost']:MAX_WON).'"/>
									</td>
									<td class="textcenter">
										<input
											type="text"
											onkeyup="this.value=RemoveCarka(this.value);"
											name="vyhernost['.$sport_id.']['.$udalost_id.']['.$typ_id.']['.$podtyp_id.'][risk]"
											class="sinput2"
											value="'.(isset($settings[$udalost_id][$typ_id][$podtyp_id]['risk'])?$settings[$udalost_id][$typ_id][$podtyp_id]['risk']:RISK_LIMIT).'" />
									</td>
								</tr>
							';
					}
				}
			}
		}


   /*spodek*/
   $this->vrat .= '<tr><td colspan="9" class=""><br /><div class="actions"><input type="submit" name="edit" value="Aktualizovat"></div></td></tr>
	                  </table></form>';

	} else {
		$this->vrat .= UiUtil::printWarnings('no-data');
	}
  }



//commented out by Martin 13.5. 2011 it shoudl not be needed, now handled by ws in method loadEventsBySport()
	/**
 * Vyber udalosti
 * @return sting
 */
/*
  private function RetUdalost($n=0){


  	static $sport;

		$vrat				= '';
		$preklad		= new Preklady();
		$where			= " ";
		$sport_n		= array();
		$oblast			= array();

		if($n != 0){
			$where .= "and s.sport_id=".$n;
		}

//TODO by Martin 2.6. 2010 - why are there two sql statements? The first one is presumably to be deleted.
		$sql = "
			SELECT s.nazev as snazev,u.nazev as unazev,u.udalost_id,s.sport_id
			FROM sport s
			INNER JOIN udalost u ON s.sport_id=u.sport_id
			WHERE ".$where;

		$sql = "
			SELECT s.nazev as snazev,e.text AS udalost_nazev,u.udalost_id,s.sport_id,d.text AS onazev,c.oblast_id
			FROM sport s
			INNER JOIN udalost AS u ON s.sport_id=u.sport_id
			INNER JOIN oblast AS c ON c.oblast_id=u.oblast_id
			LEFT JOIN(
				SELECT text,index_pole FROM preklady e WHERE lang_id=1
			) AS e ON u.nazev=e.index_pole

			LEFT JOIN(
				SELECT text,index_pole FROM preklady d WHERE lang_id=1
			) AS d ON c.nazev=d.index_pole
			".$where."
			ORDER BY s.sport_id,trim(d.text),trim(e.text)";


		$sql = "
			SELECT s.nazev as snazev,e.text AS udalost_nazev,u.udalost_id,s.sport_id,d.text AS onazev,c.oblast_id
			FROM sport s
			INNER JOIN udalost u ON s.sport_id=u.sport_id
			INNER JOIN oblast c ON c.oblast_id=u.oblast_id
			INNER JOIN preklady d ON d.index_pole=c.nazev
			INNER JOIN preklady e ON e.index_pole=u.nazev
			WHERE d.lang_id=1
			AND e.lang_id=1
			".$where."
			ORDER BY s.sport_id,trim(d.text),trim(e.text)";



		$res =& $this->dbGame->query($sql);
		if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: update galerie',"admin_ex_db");

			while ($row =& $res->fetchRow()){

				if(!isset($sport_n[$row['sport_id']])){
					$s = $preklad->FindPreklad($row['snazev'],1);
					$sport_n[$row['sport_id']] = $s[1];
				}

        if(!isset($oblast[$row['oblast_id']])){
					$oblast[$row['oblast_id']] = $row['onazev'];
				}

				$sport[$row['sport_id']][$row['oblast_id']][$row['udalost_id']]['nazev'] = $row['udalost_nazev'];
			}

			$vrat .= '
				<select size="20" onchange="" id="udalost" name="udalost">
					<option value="0">Vyber událost</option>
			';

			foreach($sport as $s_id=>$h){
				$vrat .= '<optgroup label='.$sport_n[$s_id].'>';

				foreach($h as $o_id=>$h2){
					$vrat .= '<optgroup label='.$oblast[$o_id].'>';

					foreach($h2 as $u_id=>$h3){
						$vrat .= '<option '.(isset($_POST['udalost']) && $_POST['udalost']==$u_id?'selected="selected"':'').' value="'.$u_id.'">'.Help::Html($h3['nazev']).'</option>';
					}

					$vrat .= '</optgroup>';
       	}

				$vrat .= '</optgroup>';
			}

			$vrat .= '</select>';

  	return $vrat;
  }
*/



	/**
	* Nastaveni prav k sekci
	* @param int $update pravo zapisu
	* @param int $delete pravo smazani
	* @return void
	*/
	public function setPrivileges($update,$delete){

	}



	/**
	* Vraci vystup do tridy main
	* @return string
	*/
	public function getContent(){
		return $this->vrat;
	}



	public function __destruct(){

	}



	private function loadEventsBySport($sportId) {
		$exts = array(new It6_WsExtension_Client_Order('eo', array('regionId', '(TRIM(name))')));
		$events		= Zend_Registry::get('ws')->ext($exts)->Event->getAllBySport($sportId);
		$outHtml	= '';

		$regionId = 0;
		foreach($events as $event) {
			if ($regionId != $event['regionId']) {
				if (0 != $regionId)
					$outHtml .= '</optgroup>';
				$outHtml .= "<optgroup label=\"{$event['regionName']}\">";
				$regionId = $event['regionId'];
			}
			$outHtml .= '<option  value="' . $event['eventId'] .'" '
					. (isset($_POST['udalost']) && ($_POST['udalost'] == $event['eventId'] || in_array($event['eventId'],$_POST['udalost']) ) ? 'selected="selected"' : '') . '>'
					. Help::Html($event['name']) .'</option>';
		}
		if (0 != $regionId)
			$outHtml .= '</optgroup>';

		return $outHtml;
	}

	private function loadEventsBySportAndRegion($sportId, $regionId) {
		
		$exts = array(new It6_WsExtension_Client_Order('eo', array('regionId', '(TRIM(name))')));
		$events		= Zend_Registry::get('ws')->ext($exts)->Event->getAllWhere(
			array('sportId = ?' => $sportId, 'regionId IN (?)' => $regionId, 'platne_do >= ?' => It6_Date::dbNow()));

		$outHtml	= '';

		$regionId = 0;
		foreach($events as $event) {
			if ($regionId != $event['regionId']) {
				if (0 != $regionId)
					$outHtml .= '</optgroup>';
				$outHtml .= "<optgroup label=\"{$event['regionName']}\">";
				$regionId = $event['regionId'];
			}

			$outHtml .= '<option  value="' . $event['eventId'] .'" '
					. (isset($_POST['udalost']) && ($_POST['udalost'] == $event['eventId'] || in_array($event['eventId'],$_POST['udalost']) ) ? 'selected="selected"' : '') . '>'
					. Help::Html($event['name']) .'</option>';
		}
		if (0 != $regionId)
			$outHtml .= '</optgroup>';

		return $outHtml;
	}
	
	private function loadEventsRegion($regionId) {
		
		$exts = array(new It6_WsExtension_Client_Order('eo', array('regionId', '(TRIM(name))')));
		$events		= Zend_Registry::get('ws')->ext($exts)->Event->getAllWhere(
			array('regionId IN (?)' => $regionId));
		$outHtml	= '';

		$regionId = 0;
		foreach($events as $event) {
			if ($regionId != $event['regionId']) {
				if (0 != $regionId)
					$outHtml .= '</optgroup>';
				$outHtml .= "<optgroup label=\"{$event['regionName']}\">";
				$regionId = $event['regionId'];
			}
			$outHtml .= '<option  value="' . $event['eventId'] .'" '
					. (isset($_POST['udalost']) && ($_POST['udalost'] == $event['eventId'] || in_array($event['eventId'],$_POST['udalost']) ) ? 'selected="selected"' : '') . '>'
					. Help::Html($event['name']) .'</option>';
		}
		if (0 != $regionId)
			$outHtml .= '</optgroup>';

		return $outHtml;
	}

}
