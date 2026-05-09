<?php
/**
 * @package    statistics
 */


 /**
 * Trida pro praci se statistikami
 *
 *
 * @package    main
 */

class Statistiky{

/**
 * vystupni XML response
 * @access private
 * @var int
 */
 private $vrat;

/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */
private  $dbGame;

/**
 * spojeni na databazi gamewarehouse
 * @access private
 * @var DB
 */
private  $dbSession;

/**
 * spojeni na databazi admin
 * @access private
 * @var DB
 */
private  $db;

/**
 * aktualni sekce
 * @access private
 * @var int
 */
private  $section;

/**
 * objekt statistik Her
 * @access private
 * @var int
 */
private  $finance;

/**
 * objekt statistik Uzivatelu
 * @access private
 * @var int
 */
private  $uzivatele;

/**
 *ma se ukazat filtr formular
 * @access private
 * @var bool
 */
private  $nomenu=false;

/**
 * objekt hry
 * @access private
 * @var object
 */
private  $hra;

/**
 * cas mezi zacatkem a konce scriptu
 * @access private
 * @var int
 */
private  $time;

/**
 * data o hrach
 * @access private
 * @var array
 */
public  $gameInfo = array();

/**
 * pole online uzivatelu
 * @access private
 * @var array
 */
private  $onuzivatele;

/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param int $section id aktualni sekce
* @param PEAR::DB $dbGame objekt spojeni s databazi
* @param PEAR::DB $db objekt spojeni s databazi
*/
  public function __construct($section=0,$db=null,$dbGame=null){

  $this->section =  $section;
  $this->time = time();



   $this->db = DB::connect(DATABASE ."://". MY_USER .":". MY_PASS ."@". MY_HOST ."/". MY_DB);
   if (DB::isError($this->db)) {
     throw new ExHandler($this->db->getMessage(),"admin_ex_db");
   }
   $this->db->setFetchMode(DB_FETCHMODE_ASSOC);
   $sql = "set names 'utf8'";
   $res =& $this->db->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");



    $this->dbGame = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
    if (DB::isError($this->dbGame)) {
      throw new ExHandler($this->dbGame->getMessage(),"admin_ex_db");
    }
    $this->dbGame->setFetchMode(DB_FETCHMODE_ASSOC);
    $sql = "set names 'utf8'";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");



   $this->dbSession = DB::connect(SESDATABASE ."://". SESMY_USER .":". SESMY_PASS ."@". SESMY_HOST ."/". SESMY_DB);
   if (DB::isError($this->dbSession)) {
      throw new ExHandler($this->dbSession->getMessage(),"admin_ex_db");
   }
   $this->dbSession->setFetchMode(DB_FETCHMODE_ASSOC);
   $sql = "set names 'utf8'";
   $res =& $this->dbSession->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");

  }

 /**
 * Metoda spousti jednitlove metody podle stavu
 * @return void
 */
  public  function runAction(){

   if($this->section == 59 && ((isset($_GET['graph']) && $_GET['graph'] == 0) || !isset($_GET['graph']))){

   	if(isset($_GET['off_game']) || isset($_GET['on_game'])) $this->SwitchGame();
	 $this->HryStat();

   }
   else if($this->section == 59 && isset($_GET['graph']) && $_GET['graph'] == 1){

     $this->GetGameGraph();

   }
   else if($this->section == 61 && ((isset($_GET['graph']) && $_GET['graph'] == 0) || !isset($_GET['graph']))){

     $this->UserStat();

   }
   else if($this->section == 61 && isset($_GET['graph']) && $_GET['graph'] == 1){

     $this->UserGraph();

   }
   else if($this->section == 60){

     $this->FinanceStat();

   }
   else
      $this->AllStat();

   	 $this->dbGame->disconnect();
	 $this->db->disconnect();
	 $this->dbSession->disconnect();

  }

    /**
 * Vypne/Zapne hru
 * @return void
 */
  public function SwitchGame(){

  	   if(isset($_GET['off_game']))   {$id = intval($_GET['off_game']);$vyp = 1;}
  	   else if(isset($_GET['on_game'])) {$id = intval($_GET['on_game']);$vyp = 0;}

  	   $sql = "update hry set vypnuto=".$vyp." where hra_id=".intval($id);
       $res =& $this->dbGame->query($sql);
       if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

  }

  /**
 * Statistiky vseho
 * @return void
 */
  public function AllStat(){



  }

  /**
 * Statistiky financi
 * @return void
 */
  public function FinanceStat(){

	$this->vrat .= '<form method="get">Graf vklady/výběry v čase:
	               <input type="radio" class="no" onclick="this.form.submit()" name="graph" value="0" '.(!isset($_GET['graph']) || $_GET['graph'] == 0?"checked=\"checked\"":"").' />
					Graf vklady/výběry platební metody: <input type="radio" class="no" onclick="this.form.submit()" name="graph" value="1"  '.(isset($_GET['graph']) && $_GET['graph'] == 1?"checked=\"checked\"":"").' />
					Top info: <input type="radio" class="no" onclick="this.form.submit()" name="graph" value="2"  '.(isset($_GET['graph']) && $_GET['graph'] == 2?"checked=\"checked\"":"").' />
					<input type="hidden" name="section" value="'.$this->section.'"></form>';

    $this->GraphFinance();
    if(isset($_GET['graph']) && $_GET['graph'] == 2) $this->TopInfo();
  }


  /**
 * Top informace o vkladech a výběrech
 * @return void
 */
  public function TopInfo(){

	$mena = "";
	$mena_ar = array();

	$mena_ob = new Mena();
	$res = $mena_ob->selectData();

	while($row =& $res->fetchRow()){

	  $mena .= "<option value=\"".$row['mena_id']."\" ".(isset($_POST['mena']) && $_POST['mena']==$row['mena_id']?"selected=\"selected\"":"").">".Help::Html($row['mena_text'])."</option>";
	  $mena_ar[$row['mena_id']] = $row['mena_text'];

	}

	$this->vrat .= '<form method="post" action="?graph=2&section='.$this->section.'">
	                Měna: <select name="mena">
					<option>-- Vyberte měnu --</option>
					'.$mena.'
					</select>
					Typ: <select name="vklad">
					 <option>-- Vyberte typ --</option>
					  <option value="vklad" '.(isset($_POST['vklad']) && $_POST['vklad']=="vklad"?"selected=\"selected\"":"").'>Vklad</option>
					  <option value="vyber" '.(isset($_POST['vklad']) && $_POST['vklad']=="vyber"?"selected=\"selected\"":"").'>Výběr</option>
					</select>
					Počet záznamů <input type="text" name="pocet" value="'.(isset($_POST['pocet'])?$_POST['pocet']:"").'" class="inputs" />
					<input type="submit" value="Vyhledat" />
	                </form>';


   if(isset($_POST['vklad']) && ($_POST['vklad']=="vklad" || $_POST['vklad']=="vyber") && isset($_POST['pocet']) && isset($_POST['mena']) && is_numeric($_POST['mena'])){

	  $this->vrat .= ' <h3>Top '.intval($_POST['pocet']).' '.($_POST['vklad']=="vklad"?"vklad":"výběr").' </h3>';
	  $this->vrat .= '<div class="hra_top"><table class="hra_hraci"><thead>
	  <tr><td>&nbsp;</td><th>Nick</th><th>Metoda</th><th>Částka</th></tr></thead>';
      $x = 1;

	  $sql = " select nick, nazev, castka from user_balance where mena_id=".intval($_POST['mena'])." and vklad=".($_POST['vklad']=="vklad"?1:0)."  and user_id not in(".implode(",",$GLOBALS['EXCLUDEUSER']).") order by castka desc limit 0,".intval($_POST['pocet']);
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

	  while($row =& $res->fetchRow()){

	    $this->vrat .= '<tr onmouseover="this.style.backgroundColor=\'#909090\'" onmouseout="this.style.backgroundColor=\'\'">
	           <td class="textcenter">'.$x.'</td>
			   <td class="textcenter">'.Help::Html($row['nick']).'</td>
			   <td class="textcenter">'.Help::Html($row['nazev']).'</td>
			   <td class="textcenter">'.Help::Html($row['castka']).'</td></tr>';
	    $x++;
	  }

      $this->vrat .= '</table></div>';

	}


  }

   /**
 * Grafy financi
 * @return void
 */
  public function GraphFinance(){

	include ("shared/jpgraph/src/jpgraph.php");
    include ("shared/jpgraph/src/jpgraph_line.php");
    include ("shared/jpgraph/src/jpgraph_bar.php");

	$mena_ob = new Mena();
	$res = $mena_ob->selectData();
	$vv = $mm = "";

	require_once('Cache/Lite/Output.php');
	$options = array(
       'cacheDir' => ROOT."/tmp/",
       'lifeTime' => 1800
      );
	$cache = new Cache_Lite_Output($options);

	while($row =& $res->fetchRow()){

	 if(!isset($_GET['graph']) || $_GET['graph'] == 0){
	   if (!($cache->start('stat_cas'.$row['mena_id']))) {
	    $this->GraphVkladyVybery($row['mena_id'],$row['mena_text']);
		$cache->end();
	   }
	   $vv .= '<br /><br />';
       $vv.= '<img src="_graph/finance_balance_'.$row['mena_id'].'.png" class="img view" />';
	 }
	 else if(isset($_GET['graph']) && $_GET['graph'] == 1){
	   if (!($cache->start('stat_metody'.$row['mena_id']))) {
	     $this->GraphMetody($row['mena_id'],$row['mena_text']);
	     $cache->end();
	   }
	   $mm .= '<br /><br />';
       $mm.= '<img src="_graph/finance_metody_'.$row['mena_id'].'.png" class="img view" />';

	 }

    }



    $this->vrat .= $vv.$mm;

	$this->vrat .= '<br /><br />';


  }

  /**
 * Statistiky financi
 * @param int $mena_id id meny
 * @param string $mena_text nazev meny
 * @return void
 */
  public function GraphMetody($mena_id,$mena_text){

   #Vklad#
   $sql = "select metoda_id,SUM(castka) AS suma,DATE_FORMAT(datum, '%m %Y') AS datum,DATE_FORMAT(datum, '%m') AS mesic,DATE_FORMAT(datum, '%Y') AS rok from user_balance where mena_id=".$mena_id." and vklad=1  and user_id not in(".implode(",",$GLOBALS['EXCLUDEUSER']).") group by DATE_FORMAT(datum, '%m %Y'),metoda_id";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

   #Vyber#
   $sql = "select metoda_id,SUM(castka) AS suma,DATE_FORMAT(datum, '%m %Y') AS datum,DATE_FORMAT(datum, '%m') AS mesic,DATE_FORMAT(datum, '%Y') AS rok from user_balance where mena_id=".$mena_id." and vyber=1 and user_id not in(".implode(",",$GLOBALS['EXCLUDEUSER']).") group by DATE_FORMAT(datum, '%m %Y'),metoda_id";
   $res2 =& $this->dbGame->query($sql);
   if(DB::isError($res2)) throw new ExHandler($sql.$res2->getMessage(),"admin_ex_db");

   $vklad = $vyber = $datax = $datay = $metod = $bplot = $lplot = array();

   while($row =& $res->fetchRow()){

	 $vklad[intval($row['rok'])][intval($row['mesic'])][$row['metoda_id']] = $row['suma'];

   }

   while($row =& $res2->fetchRow()){

	 $vyber[intval($row['rok'])][intval($row['mesic'])][$row['metoda_id']] = $row['suma'];;

   }


   $m = new PlatebniMetodyKolekce();
   $res = $m->selectData();

   while($row =& $res->fetchRow()){

    $metod[$row['metoda_id']] = $row['nazev'];

   }

   $mes = intval(date("m"));
   $rok = intval(date("Y"));

   for($x=0;$x<12;$x++){

	 foreach($metod as $k=>$h){

	   $datay['vklad'][$k][] = (isset($vklad[$rok][$mes][$k])?$vklad[$rok][$mes][$k]:0);


	 }

	 $datax[] = $mes." ".$rok;

	 if(--$mes < 1) {$mes=12;$rok--;}

   }

   $mes = intval(date("m"));
   $rok = intval(date("Y"));

   for($x=0;$x<12;$x++){

	 foreach($metod as $k=>$h){

	   $datay['vyber'][$k][] = (isset($vyber[$rok][$mes][$k])?$vyber[$rok][$mes][$k]:0);


	 }

	 if(--$mes < 1) {$mes=12;$rok--;}

   }

   $datax = array_reverse($datax);

   // Create the graph.
   $graph = new Graph(730,480,"auto");
   $graph->SetScale("textlin");
   $graph->SetMarginColor('white');
   $graph->SetBackgroundImage(ROOT."www/_clip/loglogo.jpg",BGIMG_CENTER);
   // Adjust the margin slightly so that we use the
   // entire area (since we don't use a frame)
   $graph->SetMargin(50,166,10,50);

   // Box around plotarea
   $graph->SetBox();

   // No frame around the image
   $graph->SetFrame(false);

   $graph->legend->Pos(0.02,0.05,"right","top");
   $graph->legend->SetFont(FF_FONT1,FS_NORMAL,4);

   $graph->title->Set(mb_convert_encoding('Vklady a vběry u platebních metod - '.$mena_text,"ISO-8859-2")); //TODO: is this encoding handling OK? (more occurences in this file)


   // Setup the X and Y grid
   $graph->ygrid->SetFill(true,'#DDDDDD@0.5','#BBBBBB@0.5');
   $graph->ygrid->SetLineStyle('dashed');
   $graph->ygrid->SetColor('gray');
   $graph->xgrid->Show();
   $graph->xgrid->SetLineStyle('dashed');
   $graph->xgrid->SetColor('gray');

   // Setup month as labels on the X-axis
   $graph->xaxis->SetTickLabels($datax);
   $graph->xaxis->SetFont(FF_FONT1,FS_NORMAL);
   $graph->xaxis->SetLabelAngle(90);

   foreach($datay['vyber'] as $k=>$h){
     $klic = count($bplot);
	 $color = "#".dechex(rand(0,256)).dechex(rand(0,256)).dechex(rand(0,256));
     // Create a bar pot
	 $h = array_reverse($h);
     $bplot[$klic] = new BarPlot($h);
     $bplot[$klic]->value->Show();
     $bplot[$klic]->SetLegend(mb_convert_encoding("Výběr: ".$metod[$k],"ISO-8859-2"));
     $bplot[$klic]->SetWidth(0.6);
     $fcol= $color;
     $tcol= $color;

     $bplot[$klic]->SetFillGradient($fcol,$tcol,GRAD_LEFT_REFLECTION);

     // Set line weigth to 0 so that there are no border
     // around each bar
     $bplot[$klic]->SetWeight(0);

     $graph->Add($bplot[$klic]);
   }

   // Create filled line plot
  foreach($datay['vklad'] as $k=>$h){
     $klic = count($bplot);
	 $color = "#".dechex(rand(0,256)).dechex(rand(0,256)).dechex(rand(0,256));

	 $h = array_reverse($h);
     $lplot[$klic] = new LinePlot($h);
     $lplot[$klic]->value->Show();
     $lplot[$klic]->SetLegend(mb_convert_encoding("Vklad: ".$metod[$k],"ISO-8859-2"));
     $lplot[$klic]->SetFillColor($color.'@0.8');
     $lplot[$klic]->SetColor($color.'@0.7');
     $lplot[$klic]->SetBarCenter();

     $lplot[$klic]->mark->SetType(MARK_SQUARE);
     $lplot[$klic]->mark->SetColor($color.'@0.5');
     $lplot[$klic]->mark->SetFillColor($color);
     $lplot[$klic]->mark->SetSize(6);

     $graph->Add($lplot[$klic]);

   }

   // .. and finally send it back to the browser
   $graph->Stroke(ROOT."www/_graph/finance_metody_".$mena_id.".png");

  }

  /**
 * Statistiky financi
 * @param int $mena_id id meny
 * @param string $mena_text nazev meny
 * @return void
 */
  public function GraphVkladyVybery($mena_id,$mena_text){

   #Vklad#
   $sql = "select SUM(castka) AS suma,DATE_FORMAT(datum, '%m %Y') AS datum,DATE_FORMAT(datum, '%m') AS mesic,DATE_FORMAT(datum, '%Y') AS rok from user_balance where mena_id=".$mena_id." and vklad=1  and user_id not in(".implode(",",$GLOBALS['EXCLUDEUSER']).") group by DATE_FORMAT(datum, '%m %Y')";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

   #Vyber#
   $sql = "select SUM(castka) AS suma,DATE_FORMAT(datum, '%m %Y') AS datum,DATE_FORMAT(datum, '%m') AS mesic,DATE_FORMAT(datum, '%Y') AS rok from user_balance where mena_id=".$mena_id." and vyber=1 and user_id not in(".implode(",",$GLOBALS['EXCLUDEUSER']).") group by DATE_FORMAT(datum, '%m %Y')";
   $res2 =& $this->dbGame->query($sql);
   if(DB::isError($res2)) throw new ExHandler($sql.$res2->getMessage(),"admin_ex_db");

   $vklad = $vyber = $datax = $datay = array();

   while($row =& $res->fetchRow()){

	 $vklad[intval($row['rok'])][intval($row['mesic'])] = $row['suma'];

   }

   while($row =& $res2->fetchRow()){

	 $vyber[intval($row['rok'])][intval($row['mesic'])] = $row['suma'];;

   }

   $mes = intval(date("m"));
   $rok = intval(date("Y"));

   for($x=0;$x<24;$x++){

	 $datay['vklad'][] = (isset($vklad[$rok][$mes])?$vklad[$rok][$mes]:0);
	 $datax[] = $mes." ".$rok;

	 if(--$mes < 1) {$mes=12;$rok--;}

   }

   $mes = intval(date("m"));
   $rok = intval(date("Y"));

   for($x=0;$x<24;$x++){

	 $datay['vyber'][] = (isset($vyber[$rok][$mes])?$vyber[$rok][$mes]:0);

	 if(--$mes < 1) {$mes=12;$rok--;}

   }

   $datay['vyber'] = array_reverse($datay['vyber']);
   $datay['vklad'] = array_reverse($datay['vklad']);
   $datax = array_reverse($datax);

   $graph = new Graph(730,480,"auto");
   $graph->img->SetMargin(50,130,30,70);
   $graph->SetBackgroundImage(ROOT."www/_clip/loglogo.jpg",BGIMG_CENTER);

   // Adjust brightness and contrast for background image
   // must be between -1 <= x <= 1, (0,0)=original image
   $graph->AdjBackgroundImage(0,0);

   //$graph->img->SetAntiAliasing("white");
   $graph->SetScale("textlin");
   $graph->SetShadow();
   $graph->title->Set(mb_convert_encoding("Vklady a výběry v čase - ".$mena_text,"ISO-8859-2"));

   // Use built in font
   $graph->title->SetFont(FF_FONT1,FS_BOLD,9);

   $graph->xaxis->SetTickLabels($datax);
   $graph->xaxis->SetLabelAngle(90);
   $graph->xaxis->SetFont(FF_FONT1,FS_NORMAL,9);
   $graph->xaxis->SetColor('darkblue','black');
   $graph->yaxis->SetFont(FF_FONT1,FS_NORMAL,9);
   // Slightly adjust the legend from it's default position in the
   // top right corner.
   $graph->legend->Pos(0.05,0.00,"right","top");
   $graph->legend->SetFont(FF_FONT1,FS_NORMAL,9);

   $color = "#".dechex(rand(0,256)).dechex(rand(0,256)).dechex(rand(0,256));
	 //"#".dechex(random(256)).dechex(random(256)).dechex(random(256));
     // Create the first line
   $p1 = new LinePlot($datay['vklad']);
   $p1->mark->SetType(MARK_FILLEDCIRCLE);
   $p1->mark->SetFillColor($color);
   $p1->mark->SetWidth(3);
   $p1->SetColor($color);
   $p1->SetCenter();
   $p1->SetLegend(mb_convert_encoding("Vklady","ISO-8859-2"));
   $p1->value->Show();
   $graph->Add($p1);

   $color = "#".dechex(rand(0,256)).dechex(rand(0,256)).dechex(rand(0,256));
	 //"#".dechex(random(256)).dechex(random(256)).dechex(random(256));
     // Create the first line
   $p2 = new LinePlot($datay['vyber']);
   $p2->mark->SetType(MARK_FILLEDCIRCLE);
   $p2->mark->SetFillColor($color);
   $p2->mark->SetWidth(3);
   $p2->SetColor($color);
   $p2->SetCenter();
   $p2->SetLegend(mb_convert_encoding("Výběry","ISO-8859-2"));
   $p2->value->Show();
   $graph->Add($p2);

    // Output line
    $graph->Stroke(ROOT."www/_graph/finance_balance_".$mena_id.".png");

  }

   /**
 * Vyhozeni uzivatele
 * @param int $nazev nazev hry
 * @return void
 */
	public function VyhodUser($user_id) {
		//TODO: use some class like It6_Session_Web
		try {
			Zend_Registry::get('zdb_sess')->update(
				'session',
				array('status' => 4, 'zprava' => 'Admin delete'),
				array(
					'status IN (?)' => array(2, 3),
					'user_id=?' => $userId
				)
			);
		}
		catch (Exception $e) {
			throw new ExHandler($e->getMessage(), "admin_ex_db");
		}

		$this->vrat .= "<div class=\"okmsg\">Uživatel byl úspěšně vyhozen</div><br />";

		It6_Log::info(
			"User '%id%' was removed.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('id' => $user_id)
		);
	}

   /**
 * Statistiky uzivatelu grafy
 * @return void
 */
  public function UserGraph(){

  	  include ("shared/jpgraph/src/jpgraph.php");
      include ("shared/jpgraph/src/jpgraph_line.php");
      include ("shared/jpgraph/src/jpgraph_bar.php");

	  $this->vrat .= '<form method="get"></form>';

      require_once('Cache/Lite/Output.php');

      $options = array(
       'cacheDir' => ROOT."/tmp/",
       'lifeTime' => 1
      );

	  $cache = new Cache_Lite_Output($options);

	  if (!($cache->start('uzivatel'))) {
	   $this->UserGeo();
	   $this->GraphUserReg();
	   $cache->end();
	  }

	 $this->vrat .= '<br /><br />';
	 $this->vrat .= '<img style="position:absolute;" src="_graph/user_country.png" class="img view" />';
     $this->vrat .= '<br /><br />';
	 $this->vrat .= '<img style="position:absolute;top:800px" src="_graph/user_reg.png" class="img view" />';
     $this->vrat .= '<br /><br />';

  }

  /**
  * vytvori graf registraci v case
  *
  * @return void
  */
  public function GraphUserReg(){


   $sql = "SELECT count(user_id) AS pocet, DATE_FORMAT(datum_registrace, '%m %Y') AS datum,DATE_FORMAT(datum_registrace, '%m') AS mesic,DATE_FORMAT(datum_registrace, '%Y') AS rok  from uzivatel where user_id not in(".implode(",",$GLOBALS['EXCLUDEUSER']).") group by  DATE_FORMAT(datum_registrace, '%m %Y')";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

   $data = array();

   while ($row =& $res->fetchRow()){

	$data[intval($row['rok'])][intval($row['mesic'])] = $row['pocet'];

   }

   $rok = intval(date("Y"));
   $mes = intval(date("m"));
   // Some data
   $datax = array();
   $datay = array();

   for($x=0;$x<24;$x++){

	if(isset($data[$rok][$mes])){
	  $datay[] = $data[$rok][$mes];
	  $datax[] = $mes." ".$rok;
	}else{
	  $datay[] = 0;
	  $datax[] = $mes." ".$rok;
	}

	$mes--;

	if($mes == 0) {$mes = 12;$rok--;}

   }
   $datax = array_reverse($datax);
   $datay = array_reverse($datay);

   // A nice graph with anti-aliasing
   $graph = new Graph(930,480,"auto");
   $graph->img->SetMargin(50,50,40,80);
   $graph->SetBackgroundImage(ROOT."www/_clip/loglogo.jpg",BGIMG_CENTER);

   // Adjust brightness and contrast for background image
   // must be between -1 <= x <= 1, (0,0)=original image
   $graph->AdjBackgroundImage(0,0);

   //$graph->img->SetAntiAliasing("white");
   $graph->SetScale("textlin");
   $graph->SetShadow();
   $graph->title->Set(mb_convert_encoding ("Nové registrace v čase za poslední 2 roky","iso-8859-2","UTF-8"));

   // Use built in font
   $graph->title->SetFont(FF_FONT1,FS_BOLD);

   $graph->xaxis->SetTickLabels($datax);
   $graph->xaxis->SetLabelAngle(90);
   $graph->xaxis->SetFont(FF_FONT1);
   $graph->xaxis->SetColor('darkblue','black');
   // Slightly adjust the legend from it's default position in the
   // top right corner.
   $graph->legend->Pos(0.02,0.02,"right","top");

   // Create the first line
   $p1 = new LinePlot($datay);
   $p1->mark->SetType(MARK_FILLEDCIRCLE);
   $p1->mark->SetFillColor("black");
   $p1->mark->SetWidth(3);
   $p1->SetColor("blue");
   $p1->SetCenter();
   //$p1->SetLegend("Počet her");
   $p1->value->Show();
   $graph->Add($p1);

    // Output line
    $graph->Stroke(ROOT."www/_graph/user_reg.png");


  }

  /**
 * Graf rozlozeni uzivatelu podle zemi
 * @return void
 */
  public function UserGeo(){

	 $zeme = new Zeme();
     $res = $zeme->SelectData();
     $z = array();
     $preklad = new Preklady();

     while ($row =& $res->fetchRow()){

	  $res3 = $preklad->SelectData("where lang_id=1 and index_pole='".Help::Slash($row['nazev'])."'");
	  if ($row3 =& $res3->fetchRow());else $row3['text'] = "Překlad nenalazen";
      $z[$row['zeme_id']] = mb_convert_encoding ($row3['text'],"iso-8859-2","UTF-8");

	 }


	 $user = new UserKolekce();
     $res = $user->SelectData("where user_id not in(".implode(",",$GLOBALS['EXCLUDEUSER']).")");
     $u = array();

     while ($row =& $res->fetchRow()){

	   if(isset($u[$row['zeme_id']])) $u[$row['zeme_id']]++;
	   else $u[$row['zeme_id']] = 1;

	 }

	 foreach($z as $k=>$h){

	 	if(!isset($u[$k])) unset($z[$k]);

	 }

	ksort($z);
	reset($z);
	ksort($u);
	reset($u);
    $z = array_values($z);
	$u = array_values($u);
    // Create the graph.
    $graph = new Graph(1830,580,auto);
    $graph->title->Set(mb_convert_encoding ("Počet registorvaných uživatelů z jednotlivých zemích","iso-8859-2","UTF-8"));
    $graph->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->img->SetMargin(40,30,30,140);
	$graph->SetScale("textint");
    $graph->SetShadow();
    $graph->SetBackgroundImage(ROOT."www/_clip/loglogo.jpg",BGIMG_CENTER);


    // Setup Y and Y2 scales with some "grace"
    $graph->SetScale("textlin");
    $graph->yaxis->scale->SetGrace(100);

    $graph->xaxis->SetTickLabels($z);
    $graph->xaxis->SetLabelAngle(90);
    $graph->xaxis->SetFont(FF_FONT1);

    //$graph->ygrid->Show(true,true);
    $graph->ygrid->SetColor('gray','lightgray@0.5');

    // Setup graph colors
    $graph->SetMarginColor('white');


    // Create the "dummy" 0 bplot
    //$bplotzero = new BarPlot($u);
    // Create a bar pot
    $bplot = new BarPlot($u);
    $bplot->SetFillColor("orange");
    $bplot->SetWidth(0.1);
    $bplot->SetShadow();
    $bplot->value->SetFormat('%01.0d');
    // Setup the values that are displayed on top of each bar
    $bplot->value->Show();
    // Must use TTF fonts if we want text at an arbitrary angle
    $bplot->value->SetFont(FF_FONT1,FS_BOLD);
    // Black color for positive values and darkred for negative values
    $bplot->value->SetColor("black","darkred");
    $graph->Add($bplot);

    // .. and finally stroke the image back to browser
    $graph->Stroke(ROOT."www/_graph/user_country.png");

  }

 /**
 * Statistiky uzivatelu tabulky
 * @return void
 */
  public function UserStat(){

	if(isset($_GET['vyhod'])){

	  	  if($this->delete)
	        $this->VyhodUser(intval($_GET['vyhod']));
	      else
	       $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro mazání v této sekci</div>\n";

	}


	$online = $this->OnlineUsers();
	$sum = $this->SumUsers();

	$celkemzeny = 0;
	$celkemmuzi = 0;

	foreach($sum as $k=>$h){

	  if($k == "m") $celkemmuzi += $h['pocet'];
	  else        $celkemzeny += $h['pocet'];

	}

	$user = new UserKolekce();
	$res = $user->selectData("where datum_registrace between '".date("Y")."-".date("m")."-".date("d")." 00:00:00' and '".date("Y")."-".date("m")."-".date("d")." 23:59:59'");
	$dnesni = $res->numRows();

	$this->vrat .= '<form method="get"></form>';


	$this->vrat .= '<div style="width:500px;"><div class="hra_top">Celkem uživatelů <strong>'.($celkemmuzi+$celkemzeny).'</strong> uživatelů<br /><br />';
	$this->vrat .= 'Dnes se registrovalo <strong>'.$dnesni.'</strong> uživatelů<br /><br />';
	$this->vrat .= 'Mužů <em class="blue">'.$celkemmuzi.'</em><br/>';
	$this->vrat .= 'Žen <em class="red">'.$celkemzeny.'</em></div></div><br/>';

	$this->vrat .= '<div style="width:500px;">';
	$this->vrat .= '<div class="hra_top"><strong>On-line uživatelé:</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span> '.Help::Html($online).'</span></div>';

	if($online > 0){
	  $this->vrat .= '<div class="hra_top" style="overflow:auto;max-height:500px;_height:500px;">
	                   <table class="unitable">
					   <thead><tr><th>&nbsp;</th><th>Nick</th><th>Přihlášení</th><th>Odhlásit</th></tr><thead>';

	  $x = 1;
	  foreach($this->onuzivatele as $k=>$h){

	    $this->vrat .= '<tr><td>'.$x.'</td><td class="textcenter">'.Help::Html($h['nick']).'</td><td class="textcenter">'.Help::Html($h['start']).'</td><td class="textcenter"><a href="?section='.$this->section.'&nick='.urlencode($h['nick']).'&vyhod='.$k.'" onclick="if(!confirm(\'Opravdu chcete uživatele vyhodit?\')) return false;" >X</a></td></tr>';

	    $x++;

	  }

	  $this->vrat .= '</table></div>';
	}

	$this->vrat .= '</div>';

	$this->vrat .= '<br /><br />';

  }

  /**
 * Pocet uzivatelu celkem a z toho zen a muzu
 * @return void
 */
  public function SumUsers(){

   $u =  new UserKolekce();
   $res = $u->SelectPohlavi("where user_id not in(".implode(",",$GLOBALS['EXCLUDEUSER']).")");

   $pohlavi = array();

   while ($row =& $res->fetchRow()){

	 $pohlavi[$row['pohlavi']]['pocet'] = $row['pohlaviPocet'];
	 $pohlavi[$row['pohlavi']]['registrace'] = $row['registrace'];

   }

   return $pohlavi;

  }

 /**
 * Prave prihlaseni uzivatele
 * @return void
 */
  public function OnlineUsers(){

	$sql = "select user_id, start from session where (status=2 or status=3) and user_id not in(".implode(",",$GLOBALS['EXCLUDEUSER']).")";
    $res =& $this->dbSession->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

	 while ($row =& $res->fetchRow()){

	   $ob = new UserKolekce();
	   $res2 = $ob->selectData("where user_id=".$row['user_id']);

	   if ($row2 =& $res2->fetchRow()){

		  $this->onuzivatele[$row['user_id']]['nick'] = $row2['nick'];
		  $this->onuzivatele[$row['user_id']]['start'] = $row['start'];

	   }

	 }

	 return count($this->onuzivatele);
  }

 /**
 * Statistiky her
 * @return void
 */
  public function HryStat($nomenu){

  	$this->nomenu = $nomenu;
  	$this->gameInfo = array();



  	if(!isset($_POST['od'])) $_POST['od'] = date('d.m.Y 00:00:00');
  	if(!isset($_POST['do'])) $_POST['do'] = '1.1.2100 00:00:00';

    if(isset($_POST['od']) && mb_strlen($_POST['od']) > 0 &&!It6_Date::checkFormat($_POST['od'])) $this->vrat .= "<div class=\"errormsg\">Datum 'od' nemá správný formát</div><br />";
	if(isset($_POST['do']) && mb_strlen($_POST['do']) > 0 &&!It6_Date::checkFormat($_POST['do'])) $this->vrat .= "<div class=\"errormsg\">Datum 'do' nemá správný formát</div><br />";
	if(isset($_REQUEST['user'])) $_POST['user'] = $_REQUEST['user'];
	if(!is_numeric($_POST['user']))$_POST['user'] = "all";

  if($this->nomenu==false) $this->vrat .= '<form method="get">Tabulky: <input type="radio" class="no" onclick="this.form.submit()" name="graph" value="0" checked="checked" />
					Grafy: <input type="radio" class="no" onclick="this.form.submit()" name="graph" value="1"  /><input type="hidden" name="section" value="'.$this->section.'"></form>';

   $obj =  new HryKolekce($this->section);
   $res = $obj->SelectData();
   if($this->nomenu==false)$this->vrat .= $obj->SearchForm(0,array("datum"=>1,"user"=>1,"top"=>1));

   if(!isset($_POST['filtr'])) return;

   $this->hra =  array();
   $user = array();
   $top = array();
   $preklady = new Preklady();

   while ($row =& $res->fetchRow()){

	$nazev = $preklady->FindPreklad($row['nazev'],1);
	$_POST['od'] = $_POST['od'];
	$_POST['do'] = $_POST['do'];
	$game_temp = new $row['trida']();
	$online_temp = $game_temp->getOnlinePlayers();
	$user_info = $game_temp->getGame();
	$sum_temp = $game_temp->getTotalWin();
	if(isset($_POST['top_info'])){
	$top['vsazeno'][$row['hra_id']] = $game_temp->getTopInfo("vsazeno","desc",intval($_POST['top_pocet']),'where datum>"'. It6_Date::toDb($_POST['od']).'" and datum<="'.It6_Date::toDb($_POST['do']).'"');
	$top['vsazeno'][$row['hra_id']]['nazev'] = $nazev[1];
	$top['vyhra'][$row['hra_id']] = $game_temp->getTopInfo("vyhra","desc",intval($_POST['top_pocet']),'where datum>"'.It6_Date::toDb($_POST['od']).'" and datum<="'.It6_Date::toDb($_POST['do']).'"');
	$top['vyhra'][$row['hra_id']]['nazev'] = $nazev[1];
	$top['vyhernost'][$row['hra_id']] = $game_temp->getTopInfo("vyhernost","desc",intval($_POST['top_pocet']),'where datum>"'.It6_Date::toDb($_POST['od']).'" and datum<="'.It6_Date::toDb($_POST['do']).'"');
	$top['vyhernost'][$row['hra_id']]['nazev'] = $nazev[1];
	}
	if($sum_temp['vsazeno'] != 0) $vyhernost_temp = round(((($sum_temp['vyhra']+$sum_temp['vsazeno'])/$sum_temp['vsazeno'])*100),2);
	else $vyhernost_temp = 0;

	if(isset($_POST['user'])&& $_POST['user'] != "all"){
	 foreach($user_info as $h){

  	  $user[$h['user_id']]['nick'] = $h['nick'];

	  if(!isset($user[$h['user_id']][$row['hra_id']]['vsazeno']))
	    $user[$h['user_id']][$row['hra_id']]['vsazeno'] =  $h['vsazeno'];
	  else
	    $user[$h['user_id']][$row['hra_id']]['vsazeno'] +=  $h['vsazeno'];

	  if(!isset($user[$h['user_id']][$row['hra_id']]['vyhra']))
	    $user[$h['user_id']][$row['hra_id']]['vyhra'] =  $h['vyhra'];
	  else
	    $user[$h['user_id']][$row['hra_id']]['vyhra'] +=  $h['vyhra'];

	 }
	}

    $this->hra[$row['hra_id']]['nazev'] = $nazev[1];
    $this->hra[$row['hra_id']]['vypnuto'] = $row['vypnuto'];
    $this->hra[$row['hra_id']]['online'] = $online_temp;
	$this->hra[$row['hra_id']]['vsazeno'] = $sum_temp['vsazeno'];
	$this->hra[$row['hra_id']]['vyhra'] = $sum_temp['vyhra'];
	$this->hra[$row['hra_id']]['totalgames'] = $game_temp->getTotalGames();

   }
   //echo "<pre>";print_r($this->hra);exit;
   $this->vrat .= '<h3>Statistika her</h3>';
   $this->vrat .= '<table class="hra_hraci" style="font-size:0.9em;"><col style="background:\'#909090\'">';
   $this->vrat .= '<head><tr><th>&nbsp;</th><th>Vsazeno</th><th>Výhra (čistá)</th><th>Výhernost</th><th>Počet her</th><th>On-line hráči</th><th>Stav</th></tr></head>';

   $total_vsazeno = 0;
   $total_vyhra = 0;
   $total_games = 0;
   $total_online = 0;
   foreach($this->hra as $k=>$h){

	 $total_vsazeno += $h['vsazeno'];
     $total_vyhra += $h['vyhra'];
	 $total_games += $h['totalgames'];
     $total_online += $h['online'];
     //if($h['vsazeno'] == 0) $h['vsazeno'] = 1;
	 $vyhernost = round(((($h['vyhra']+$h['vsazeno'])/$h['vsazeno'])*100),2);
     $this->vrat .= '<tr style="'.($h['vypnuto'] == 1?"background-color:#909090":"").'" onmouseover="this.style.backgroundColor=\'#909090\'" '.($h['vypnuto'] == 1?'':'onmouseout="this.style.backgroundColor=\'\'"').'><td class="textleft">'.Help::Html($h['nazev']).' </td><td class="textcenter">'.$h['vsazeno'].'</td><td class="textcenter">'.$h['vyhra'].'</td><td class="textcenter '.($vyhernost>200?"warn":"").'">'.$vyhernost.'</td><td class="textcenter">'.$h['totalgames'].'</td><td class="textcenter">'.$h['online'].'</td>
                     <td class="textcenter">'.($h['vypnuto'] == 1?'<a href="?section=59&on_game='.$k.'"><strong>VYPNUTO</strong></a>':'<a href="?section=59&off_game='.$k.'"><strong>ZAPNUTO</strong></a>').'</td></tr>';

    $klic = count($this->gameInfo);
    $this->gameInfo[$klic][] = $h['nazev'];
    $this->gameInfo[$klic][] = $h['vsazeno'];
    $this->gameInfo[$klic][] = $h['vyhra'];
    $this->gameInfo[$klic][] = $vyhernost;

   }

   $vyhernost = round(((($total_vyhra+$total_vsazeno)/$total_vsazeno)*100),2);
   $this->vrat .= '<tr style="background:#929EAD;"><th class="textleft"><strong>Celkem</strong></th><td class="textcenter"><strong>'.$total_vsazeno.'</strong></td><td class="textcenter"><strong>'.$total_vyhra.'</strong></td><td class="textcenter  '.($vyhernost>200?"warn":"").'"><strong>'.$vyhernost.'</strong></td><td class="textcenter"><strong>'.$total_games.'</strong></td><td class="textcenter"><strong>'.$total_online.'</strong></td></tr>';
   $this->vrat .= '</table>';

   $this->vrat .= '<br /><br />';

   $this->vrat .= '<h3>Statistika hráčů</h3>';

   $this->StatistikaHracu($user);

   $this->vrat .= '<br /><br />';

   $this->TopVyhry($top['vyhra']);

   $this->vrat .= '<br /><br />';

   $this->TopVsazeno($top['vsazeno']);

   $this->vrat .= '<br /><br />';

   $this->TopVyhernost($top['vyhernost']);

   $this->vrat .= '<br /><br /><small>Výhernost nad 200% je označena červeně</small>';

  }

 /**
 * Grafy pro hry
 * @return void
 */
  private function GetGameGraph(){

	  include ("shared/jpgraph/src/jpgraph.php");
      include ("shared/jpgraph/src/jpgraph_line.php");
      include ("shared/jpgraph/src/jpgraph_bar.php");
	  include ("shared/jpgraph/src/jpgraph_pie.php");

	  $this->vrat .= '<form method="get">Tabulky: <input type="radio" class="no" onclick="this.form.submit()" name="graph" value="0"  />
					Grafy: <input type="radio" class="no" onclick="this.form.submit()" name="graph" value="1" checked="checked" /><input type="hidden" name="section" value="'.$this->section.'"></form>';

	 require_once('Cache/Lite/Output.php');

      $options = array(
       'cacheDir' => ROOT."/tmp/",
       'lifeTime' => 10000
      );
	  $cache = new Cache_Lite_Output($options);

	 if (!($cache->start('gamegraf'))) {

       $this->TotalGamesGraph();
	   $this->CountryGame();

	   $cache->end();

	 }

	 $this->vrat .= '<br /><br />';
	 $this->vrat .= '<img src="_graph/total_games.png" class="img view" />';
     $this->vrat .= '<br /><br />';
	 $this->vrat .= '<img src="_graph/country_game.png" class="img view" />';
     $this->vrat .= '<br /><br />';

  }

/**
 * Graf pomeru her v jednotlivych zemich
 * @return void
 */
  private function CountryGame(){


   $obj =  new HryKolekce($this->section);
   $res = $obj->SelectData();
   $hry = array(); //uchova nazvy her
   $zeme = array();
   $pocet_zeme = 0;
   $preklad = new Preklady();

   $z = new Zeme();
   $res2 = $z->selectData();
   while ($row =& $res2->fetchRow()){

	$res3 = $preklad->FindPreklad($row['nazev'],1);
    $zeme[$row['zeme_id']]['nazev'] =  $res3[1];
	$zeme[$row['zeme_id']]['vsazeno'] = 0;
	$zeme[$row['zeme_id']]['game'] = 0;
   }

   while ($row =& $res->fetchRow()){
    $res3 = $preklad->FindPreklad($row['nazev'],1);
	$hry[$row['hra_id']]['nazev'] = $res3[1];
	$game_temp = new $row['trida']();
	$user_info = $game_temp->getGame();

	foreach($user_info as $h){

	  $u = new UserKolekce();
	  $res3 = $u->selectData("where user_id=".$h['user_id']);

	  if ($row3 =& $res3->fetchRow()){



		if($zeme[$row3['zeme_id']]['game'] == 0){
		  $pocet_zeme++;
	      $zeme[$row3['zeme_id']]['vsazeno'] = $h['vsazeno'];
		  $zeme[$row3['zeme_id']]['game'] = 1;
		  $hry[$row['hra_id']][$row3['zeme_id']]['vsazeno'] = $h['vsazeno'];
		  $hry[$row['hra_id']][$row3['zeme_id']]['game'] = 1;
	    }else{
		  $zeme[$row3['zeme_id']]['vsazeno'] += $h['vsazeno'];
		  $zeme[$row3['zeme_id']]['game'] += 1;
	      $hry[$row['hra_id']][$row3['zeme_id']]['vsazeno'] += $h['vsazeno'];
		  $hry[$row['hra_id']][$row3['zeme_id']]['game'] += 1;
	    }

	  }

	}

  }


  $height = (ceil($pocet_zeme/2)*300);

    // Create the Pie Graph.
  $graph = new PieGraph(730,$height,"auto");
  $graph->SetBackgroundImage(ROOT."www/_clip/loglogo.jpg",BGIMG_COPY);
  $graph->SetShadow();

  // Set A title for the plot
  $graph->title->Set(mb_convert_encoding("Jak jsou oblíbené hry v jednotlivých zemích","ISO-8859-2"));
  $graph->title->SetFont(FF_FONT1,FS_BOLD);
  $graph->legend->Pos(0.01,0.00,"right","top");
  // Create plots
  $size=50;

  $p = array();
  $pos1 = array(0.25,0.65);
  $pos2 = "0.54";
  $y = 0;
  $x=1;
  $legend = $data = array();
  //echo "<pre>";print_r($zeme);print_r($hry);
  foreach($zeme as $k=>$h){

    if($y == 2) {$y = 0;$pos2 *= 1.8;}
    $klic = count($p);

	reset($hry);
	$data = array();
	foreach($hry as $k2=>$h2){ // Some data

	  $data[] = (!isset($h2[$k])?0:round((($h2[$k]['game']/$h['game'])*100),2));
      if($x == 1) $legend[] = mb_convert_encoding($h2['nazev'],"ISO-8859-2");
	}

	//echo "<pre>";print_r($legend);echo "</pre>";

    $pocet = 0;
	foreach($data as $h4){
	 $pocet += $h4;
	}

	if($pocet>0){
	$p[$klic] = new PiePlot($data);
    if($x == 1)$p[$klic]->SetLegends($legend);
    $p[$klic]->SetSize($size*2);
    $p[$klic]->SetCenter($pos1[$y],$pos2);
    $p[$klic]->value->SetFont(FF_FONT0);
    $p[$klic]->title->Set(mb_convert_encoding($h['nazev'],"ISO-8859-2"));
    $graph->Add($p[$klic]);

	$y++;$x++;
	}

  }

  $graph->Stroke(ROOT."www/_graph/country_game.png");

  }

 /**
 * Graf celkoveho poctu her v case (mesice)
 * @return void
 */
  private function TotalGamesGraph(){

   $hry = array();
   $preklad = new Preklady();
   $obj =  new HryKolekce($this->section);
   $res = $obj->SelectData();

   while ($row =& $res->fetchRow()){

     $res3 = $preklad->FindPreklad($row['nazev'],1);
	 $hry[$row['hra_id']] = $res3[1];

	 $game_temp = new $row['trida']();

	 $res2 = $game_temp->SelectGraphVisit();

	 $data = array();

     while ($row2 =& $res2->fetchRow()){

	   $data[intval($row2['rok'])][intval($row2['mesic'])] = $row2['pocet'];

     }

	 $rok = intval(date("Y"));
     $mes = intval(date("m"));
     // Some data
     $datax = array();
     $datay[$row['hra_id']] = array();

     for($x=0;$x<12;$x++){

	  if(isset($data[$rok][$mes])){
	    $datay[$row['hra_id']][] = $data[$rok][$mes];
	  }else{
	    $datay[$row['hra_id']][] = 0;
	  }

	  $datax[] = $mes." ".$rok;
	  $mes--;

	  if($mes == 0) {$mes = 12;$rok--;}

     }

     $datax = array_reverse($datax);
     $datay[$row['hra_id']] = array_reverse($datay[$row['hra_id']]);

   }


   //$data2y = array(14,18,33,29,39,55);

   // A nice graph with anti-aliasing
   $graph = new Graph(730,480,"auto");
   $graph->img->SetMargin(50,130,30,70);
   $graph->SetBackgroundImage(ROOT."www/_clip/loglogo.jpg",BGIMG_CENTER);

   // Adjust brightness and contrast for background image
   // must be between -1 <= x <= 1, (0,0)=original image
   $graph->AdjBackgroundImage(0,0);

   //$graph->img->SetAntiAliasing("white");
   $graph->SetScale("textlin");
   $graph->SetShadow();
   $graph->title->Set(mb_convert_encoding("Celkový počet her v čase","ISO-8859-2"));

   // Use built in font
   $graph->title->SetFont(FF_FONT1,FS_BOLD,9);

   $graph->xaxis->SetTickLabels($datax);
   $graph->xaxis->SetLabelAngle(90);
   $graph->xaxis->SetFont(FF_FONT1,FS_NORMAL,9);
   $graph->xaxis->SetColor('darkblue','black');
   $graph->yaxis->SetFont(FF_FONT1,FS_NORMAL,9);
   // Slightly adjust the legend from it's default position in the
   // top right corner.
   $graph->legend->Pos(0.01,0.00,"right","top");
   $graph->legend->SetFont(FF_FONT1,FS_NORMAL,9);
   $p = array();
   foreach($hry as $k=>$h){
     $klic = count($p);
	 $color = "#".dechex(rand(0,256)).dechex(rand(0,256)).dechex(rand(0,256));
	 //"#".dechex(random(256)).dechex(random(256)).dechex(random(256));
     // Create the first line
     $p[$klic] = new LinePlot($datay[$k]);
     $p[$klic]->mark->SetType(MARK_FILLEDCIRCLE);
     $p[$klic]->mark->SetFillColor($color);
     $p[$klic]->mark->SetWidth(5);
     $p[$klic]->SetColor($color);
     $p[$klic]->SetCenter();
     $p[$klic]->SetLegend(mb_convert_encoding($h,"iso-8859-2"));
     $p[$klic]->value->Show();
     $graph->Add($p[$klic]);

   }

    // Output line
    $graph->Stroke(ROOT."www/_graph/total_games.png");


  }

  /**
 * Top Vyhernost
 * @param array $top udaje o uzivatelych a hrach
 * @return void
 */
  private function TopVyhernost($top){

  $this->vrat .= '<h3>Top '.intval($_POST['top_pocet']).' výhernost - celkem/hra</h3>';
   $tvyhernost = array();//echo "<pre>";print_r($top);echo "</pre>";
   foreach($top as $h){

	foreach($h as $h2){

	   $h2['nazev'] = $h['nazev'];
       if(is_numeric($h2['vyhernost']))$tvyhernost[$h2['vyhernost']][] = $h2;

	 }

   }

   ksort($tvyhernost);
   $tvyhernost = array_reverse($tvyhernost,true);

   $this->vrat .= '<div class="hra_top"><table class="hra_hraci"><thead><tr><td>&nbsp;</td><th>Nick</th><th>Výhernost</th><th>Hra</th></tr></thead>';
   $x = 1;
   foreach($tvyhernost as $h){
   if($x > intval($_POST['top_pocet'])) break;
    foreach($h as $h2){
     $this->vrat .= '<tr onmouseover="this.style.backgroundColor=\'#909090\'" onmouseout="this.style.backgroundColor=\'\'">
	           <td class="textcenter">'.$x.'</td>
			   <td class="textcenter">'.Help::Html($h2['nick']).'</td>
			   <td class="textcenter">'.Help::Html($h2['vyhernost']).'</td>
			   <td class="textcenter">'.Help::Html($h2['nazev']).'</td></tr>';
	  $x++;
	 }

   }

   $this->vrat .= '</table></div>';

  }

 /**
 * Top Vsazeno
 * @param array $top udaje o uzivatelych a hrach
 * @return void
 */
  private function TopVsazeno($top){

      $this->vrat .= '<h3>Top '.intval($_POST['top_pocet']).' vsazeno - celkem/hra</h3>';
   $tvsazeno = array();//echo "<pre>";print_r($h);echo "</pre>";
   foreach($top as $h){

	foreach($h as $h2){

	   $h2['nazev'] = $h['nazev'];
       if(is_numeric($h2['vsazeno']))$tvsazeno[$h2['vsazeno']][] = $h2;

	 }

   }

   ksort($tvsazeno);
   $tvsazeno = array_reverse($tvsazeno,true);

   $this->vrat .= '<div class="hra_top"><table class="hra_hraci"><thead><tr><td>&nbsp;</td><th>Nick</th><th>Vsazeno</th><th>Hra</th></tr></thead>';
   $x = 1;
   foreach($tvsazeno as $h){
   if($x > intval($_POST['top_pocet'])) break;
    foreach($h as $h2){
     $this->vrat .= '<tr onmouseover="this.style.backgroundColor=\'#909090\'" onmouseout="this.style.backgroundColor=\'\'">
	           <td class="textcenter">'.$x.'</td>
			   <td class="textcenter">'.Help::Html($h2['nick']).'</td>
			   <td class="textcenter">'.Help::Html($h2['vsazeno']).'</td>
			   <td class="textcenter">'.Help::Html($h2['nazev']).'</td></tr>';
	 $x++;
	}

   }

   $this->vrat .= '</table></div>';

  }

 /**
 * Top Vyhry
 * @param array $top udaje o uzivatelych a hrach
 * @return void
 */
  private function TopVyhry($top){

   $this->vrat .= '<h3>Top '.intval($_POST['top_pocet']).' výhry - celkem/hra</h3>';
   $tvyhry = array();
   foreach($top as $h){

	foreach($h as $h2){

	  $h2['nazev'] = $h['nazev'];
      if($h2['vyhra'] > 0 && is_numeric($h2['vyhra']))  $tvyhry[$h2['vyhra']][] = $h2;

	}

   }

   ksort($tvyhry);
   $tvyhry = array_reverse($tvyhry,true);

   $this->vrat .= '<div class="hra_top"><table class="hra_hraci"><thead><tr><td>&nbsp;</td><th>Nick</th><th>Výhra</th><th>Hra</th></tr></thead>';
   $x = 1;
   foreach($tvyhry as $h){
   if($x > intval($_POST['top_pocet'])) break;
    foreach($h as $h2){
     $this->vrat .= '<tr onmouseover="this.style.backgroundColor=\'#909090\'" onmouseout="this.style.backgroundColor=\'\'">
	           <td class="textcenter">'.$x.'</td>
			   <td class="textcenter">'.Help::Html($h2['nick']).'</td>
			   <td class="textcenter">'.Help::Html($h2['vyhra']).'</td>
			   <td class="textcenter">'.Help::Html($h2['nazev']).'</td></tr>';
	  $x++;
	 }

   }

   $this->vrat .= '</table></div>';

  }
     /**
 * Statistika hracu
  * @param array $user udaje pro uzivatele
 * @return void
 */
  private function StatistikaHracu($user){

   $this->vrat .= '<table class="hra_hraci" style="font-size:0.9em;">';

   $hlavicka = $hlavicka2 = "";
   reset($this->hra);
   $x = 0;
   foreach($this->hra as $h){
     if($x == 4) break;
	 $hlavicka .= '<th colspan="3">'.Help::Html($h['nazev']).'</th>';
     $hlavicka2 .= '<th>Vsazeno</th><th>Výhra</th><th>Výhernost</th>';
    $x++;
	}

   $this->vrat.= '<thead><tr><td>&nbsp;</td>'.$hlavicka.'</tr><tr><td>&nbsp;</td>'.$hlavicka2.'</tr></thead>';


  reset($this->hra);
  $delete_index =  array();

  foreach($user as $k=>$h){
   $this->vrat.= '<tr onmouseover="this.style.backgroundColor=\'#909090\'" onmouseout="this.style.backgroundColor=\'\'"><td><strong>'.Help::Html($h['nick']).'</strong></td>';

	 $x = 0;
	 foreach($this->hra as $k2=>$h2){
	   if($x == 4) break;
	   if(isset($h[$k2])){
	    $vyhernost = round(((($h[$k2]['vyhra']+$h[$k2]['vsazeno'])/$h[$k2]['vsazeno'])*100),2);

        $this->vrat .= '<td class="textcenter">'.$h[$k2]['vsazeno'].'</td><td class="textcenter">'.$h[$k2]['vyhra'].'</td><td class="textcenter '.($vyhernost>200?"warn":"").'">'.$vyhernost.'</td>';
	   }else{

	     $this->vrat .= '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';

	   }
	  $delete_index[] = $k2;
	  $x++;
	 }

    $this->vrat.= '</tr>';
   }

   $this->vrat .= '</table>';

   $this->vrat .= '<br /><br />';

   if(count($user) < 1){
     foreach($this->hra as $k2=>$h2){
	   $delete_index[] = $k2;
	 }
   }

   foreach($delete_index as $h) unset($this->hra[$h]);
   if(count($this->hra)>0)  $this->StatistikaHracu($user);
  }

 /**
 * Nastaveni prav k sekci
 * @param int $update pravo zapisu
 * @param int $delete pravo smazani
 * @return void
 */
  public function setPrivileges($update,$delete){

   $this->update = $update;
   $this->delete = $delete;

  }

 /**
 * Vraci vystup do tridy main
 * @return string
 */
  public function getContent(){

    $this->time = time() - $this->time;
	if($this->nomenu==false) $this->time = "Požadavek se vyřizoval <em>".$this->time."</em> sekund<br /><br />";
    return $this->vrat;

  }


  public function __destruct(){




  }

}

?>
