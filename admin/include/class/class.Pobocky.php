f<?php
/**
 * @package    main
 */


class Pobocky{

/**
 * navratova hodnota
 * @access private
 * @var string
 */
private  $vrat = "";

/**
 * pole chyb zakaldniho ormulare
 * @access private
 * @var array
 */
private  $basicError = array();

/**
 * pravo zmeny v sekci
 * @access private
 * @var int
 */
private  $update = 0;
/**
 * pravo vymazani v sekci
 * @access private
 * @var int
 */
private  $delete = 0;


/**
 * aktualni sekce
 * @access private
 * @var int
 */
private  $section;

/**
 * pole chyb v poli editace
 * @access private
 * @var array
 */
private  $errorField = array();


/**
 * pole ktera s emaji naplnovat jinak select
 * @access private
 * @var array
 */
private  $selectField = array('smlouva_kraj','smlouva_zalohy','smlouva_vyuctovani');

/**
 * pole ktera s emaji naplnovat jinak checkbox
 * @access private
 * @var array
 */
private  $checkboxField = array('smlouva_provoz','smlouva_provoz_po','smlouva_provoz_ut','smlouva_provoz_st','smlouva_provoz_ct','smlouva_provoz_pa','smlouva_provoz_so','smlouva_provoz_ne');

/**
 * pole ktera s emaji naplnovat jinak radio
 * @access private
 * @var array
 */
private  $radioField = array('smlouva_vernostni_program');

/**
 * obdobi
 * @access private
 * @var array
 */
private  $obdobi = array('denní','týdenní','měsíční','čtvrtletní','pololetní','roční');


/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param int $section id aktualni sekce
* @param PEAR::DB $dbGame objekt spojeni s databazi
*/
  public function __construct($section=0,$dbGame=null){

  $this->section =  $section;


  }


   /**
 * Zobrazit pobocky
 * @return void
 */
  public function zobrazitPobocku(){

  	$valide = true;

  	if(!isset($_REQUEST['pobocka_id']) || !ctype_digit($_REQUEST['pobocka_id'])) {

       $this->vrat .= $this->listForm($this->listPobocek());


  	}
  	else if(isset($_REQUEST['pobocka_id']) && isset($_REQUEST['page']) && $_REQUEST['page'] == 1) {

  		$this->vrat .= $this->listPracovnici();

  	}
  	else{

  		$_REQUEST['filtr_id'] = $_REQUEST['pobocka_id'];

  		$valied = true;

  		if(isset($_POST['send'])) {

  		 $valide = $this->validePobocka();

  		 if($valide) $valide = $this->updatePobocka($_POST);

  		}

  		$dataX = $this->listPobocek();

  		$this->vrat .= $this->basicForm('Profil pobočky #'.$_REQUEST['pobocka_id'],($valide?$dataX[0]:$_POST));


  	}

  }

   /**
 * Vraci linkzy pro danou pobocku
 * @return string
 */
  public function pobockaLinks(){



  	$select = Zend_Registry::get('zdb_game')->select()->from(array("a"=>'uzivatel_pobocka'),array('a.user_id','l.nick'))
  	                                        ->join(array('l' => 'uzivatel'),'a.user_id = l.user_id')
	                                        ->where('a.pobocka_id=?',intval($_REQUEST['pobocka_id']));
    $stm  = $select->query();
    $rowD = $stm->fetchAll();

  	$d = '<a href="/?section=40&user_id='.$rowD[0]['user_id'].'" target="_blank">Uživatel: '.$rowD[0]['nick'].'</a> | ';

  	$d .= '<a href="/?section=128&page=1&pobocka_id='.$_REQUEST['pobocka_id'].'" >Pracovníci pobočky</a>';


  	return $d;

  }

  /**
 * Seznam pracovniku
 * @return string
 */
  private function listPracovnici(){

  	 $tpl = new TemplatePower( '_tpl/pracovniciList.html', T_BYFILE );
     $tpl->prepare();
  	 $tpl->assign("ACTION",'/?page='.$_GET['page'].'&section='.$this->section);
  	 $tpl->assign("POBOCKA_ID",intval($_REQUEST['pobocka_id']));


  	 $d = '<a href="/?section=128&pobocka_id='.$_REQUEST['pobocka_id'].'" >Detail pobočky</a>';
  	 $tpl->assign("DETAIL",$d);

  	 if(isset($_GET['ok'])) $tpl->assign("OK","Pracovník uložen");

  	 if(isset($_POST['insert'])){
  	 if($this->insertPracovnik())  {header('location:/?page=1&section=128&ok=1&pobocka_id='.$_REQUEST['pobocka_id']);}
  	 else{


  	  		$tpl->assign("ERROR",implode("<br />",$this->basicError));
  	  		$tpl->assign("DISPLAY",'block');

  	  		$tpl->assign("JMENO",$_POST['jmeno']);
  	  		$tpl->assign("PRIJMENI",$_POST['prijmeni']);
  	  		$tpl->assign("NICK",$_POST['nick']);
  	  		$tpl->assign("EMAIL",$_POST['email']);


      }
  	 }else
  	   $tpl->assign("DISPLAY",'none');

  	 if(isset($_GET['ok2'])) $tpl->assign("OK","Heslo zmeneno");
  	 if(isset($_GET['ok3'])) $tpl->assign("OK","Změna uložena");

  	 if(isset($_GET['pass']) && isset($_GET['uid'])){

  	 	if($this->changePracovnikHeslo($_GET['uid'],$_GET['pass'])) header('location:/?page=1&section=128&ok2=1&pobocka_id='.$_REQUEST['pobocka_id']);
  	 	else $tpl->assign("ERROR","Heslo se nepodařilo změnit 6 - 16 znaků");

  	 }

  	 if(isset($_GET['prove']) || isset($_GET['unprove'])){

  	 	if($this->proveUnprove()) header('location:/?page=1&section=128&ok3=1&pobocka_id='.$_REQUEST['pobocka_id']);
  	 	else $tpl->assign("ERROR","Akce nebyla provedena");

  	 }

  	 $select = Zend_Registry::get('zdb_game')->select()->from(array('pobocka_uzivatel'),array('*'))
  	                                                    ->where('pobocka_id=?',intval($_REQUEST['pobocka_id']));
  	  $stm  = $select->query();
      $rowD = $stm->fetchAll();

      foreach($rowD as $h){
//TODO:ADMIN
      	  $select = Zend_Registry::get('zdb_admin')->select()->from(array('session'),array('pocet'=>'count(ses_id)'))
  	                                                    ->where('status=?',2)
  	                                                    ->where('pobocka_user_id=?',$h['pobocka_user_id']);
  	      $stm2  = $select->query();
          $rowD2 = $stm2->fetchAll();

      	  $tpl->newBlock( "user" );

      	  if($h['zakazany'] != 0)$tpl->assign( "BACKGROUND","red;color:white;"  );
      	  $tpl->assign( "ID", $h['pobocka_user_id'] );
      	  $tpl->assign( "JM", $h['jmeno'] );
      	  $tpl->assign( "PR",$h['prijmeni']  );
      	  $tpl->assign( "NC", $h['nick'] );
      	  $tpl->assign( "EM",$h['email']  );
      	  $tpl->assign( "PASS", '<input type="text" class="digit6" id="passChange_'. $h['pobocka_user_id'] .'" /> <a href="javascript:document.location.href=\'?section=128&page=1&pobocka_id=1&uid='. $h['pobocka_user_id'] .'&pass=\'+$(\'#passChange_'. $h['pobocka_user_id'] .'\').attr(\'value\')">změnit</a>');
          $tpl->assign( "OT", '<a href="javascript:if(confirm(\'Opravdu chcete akci provést?\'))document.location.href=\'?section=128&page=1&pobocka_id=1&'.($h['zakazany']==0?'unprove':'prove').'='. $h['pobocka_user_id'] .'\'">'.($h['zakazany']==0?'zakázat':'povolit').'</a>');
          $tpl->assign( "ONLINE",($rowD2[0]['pocet']>0?'<span class="blue">Online</span>':'<span class="red">Offline</span>')  );
      }

      $tpl->gotoBlock( "_ROOT" );

     return $tpl->getOutputContent();
  }




 /**
 * Povoleni nebo zakazani pracovnika
 * @return bool
 */
  private function proveUnprove(){
  	if(isset($_GET['prove']) ) $id = $_GET['prove'];else $id = $_GET['unprove'];


  		$dataX = array();
        if(isset($_GET['prove']) )
    	$dataX['zakazany'] = 0;
    	else $dataX['zakazany'] = 1;

     try{

         Zend_Registry::get('zdb_game')->update('pobocka_uzivatel', $dataX,'pobocka_user_id='.intval($id));

     }catch(Zend_Exception $e){

          throw new ExHandler($e->getMessage()  . __LINE__,"admin_ex_db");

     }



  	return true;

  }


       /**
 * Zakladni formular
 * @param int $uid id pracovnika
 * @param string $pass nove heslo
 * @return bool
 */
  private function changePracovnikHeslo($uid,$pass){

  	if(!Help::checkPass($pass)) return false;
  	else{

  		$dataX = array();

    	$dataX['heslo'] = Help::cryptPass($pass);

     try{

         Zend_Registry::get('zdb_game')->update('pobocka_uzivatel', $dataX,'pobocka_user_id='.intval($uid));

     }catch(Zend_Exception $e){

          throw new ExHandler($e->getMessage()  . __LINE__,"admin_ex_db");

     }

  	}

  	return true;

  }

    /**
 * Vlozeni pracovnika
 * @return string
 */
  private function insertPracovnik(){


  	if(!isset($_POST['jmeno']) || mb_strlen($_POST['jmeno']) < 1)  $this->basicError[] = 'Jméno musí být uvedeno';
  	if(!isset($_POST['prijmeni']) || mb_strlen($_POST['prijmeni']) < 1)  $this->basicError[] = 'Přijmení musí být uvedeno';
  	if(!isset($_POST['nick']) || !Help::checkNick($_POST['nick']))  $this->basicError[] = 'Nick má špatný formát 4 - 20 znaků';
  	if(!isset($_POST['heslo']) || !Help::checkPass($_POST['heslo']))  $this->basicError[] = 'Heslo má špatný formát 6-16 znaků';
  	if($_POST['heslo'] != $_POST['heslo_znovu'])  $this->basicError[] = 'Potvrzení hesla neosuhlasí';
  	if(isset($_POST['email']) && mb_strlen(($_POST['email'])) > 0&&  !Help::valideMail($_POST['email']))  $this->basicError[] = 'Email má špatný formát';

    if(count($this->basicError) == 0){

    	$dataX = array();
    	$dataX['pobocka_id'] = $_REQUEST['pobocka_id'];
    	$dataX['jmeno'] = $_POST['jmeno'];
    	$dataX['prijmeni'] = $_POST['prijmeni'];
    	$dataX['email'] = $_POST['email'];
    	$dataX['nick'] = $_POST['nick'];
    	$dataX['heslo'] = Help::cryptPass($_POST['heslo']);

     try{

         Zend_Registry::get('zdb_game')->insert('pobocka_uzivatel', $dataX);

     }catch(Zend_Exception $e){

          throw new ExHandler($e->getMessage()  . __LINE__,"admin_ex_db");

     }


    }

    if(count($this->basicError) == 0) return true;else return false;

  }

     /**
 * Zakladni formular
 * @param array $data pole dat
 * @return void
 */
  private function listForm($data){

  	  $tpl = new TemplatePower( '_tpl/pobockaList.html', T_BYFILE );
      $tpl->prepare();

      $tpl->assign("FILTR_FULLTEXT",$_REQUEST['filtr_fulltext']);
      $tpl->assign("FILTR_ID",$_REQUEST['filtr_id']);
      if(isset($_REQUEST['filtr_aktivni'])) $tpl->assign("FILTR_AKTIVNI",'checked="checked"');
      $tpl->assign("PAGE",$this->paging);
      $tpl->assign("ACTION",'/?section='.$this->section);

      foreach($data as $h){

      	$tpl->newBlock( "list" );
      	$tpl->assign( "POBOCKA_ID", $h['pobocka_id'] );
      	$tpl->assign( "JMENO", $h['jmeno'] );
      	$tpl->assign( "ULICE", $h['ulice'] );
      	$tpl->assign( "MESTO", $h['mesto'] );
      	$tpl->assign( "EMAIL", $h['email'] );
      	$tpl->assign( "TELEFON", $h['telefon_predvolba'].' '.$h['telefon'] );

      	$stav = '';
      	if($h['pobocka_aktivni'] == 0) $stav .= '<span class="red">Neaktivní</span>';else $stav .= '<span class="blue">Aktivní</span>';
      	if($h['povoleny_naber'] == 0) $stav .= ' / <span class="red">Nepovolený náběr</span>';else $stav .= ' / <span class="blue">Povolený náběr</span>';
      	$tpl->assign( "STAV", $stav );

      }

      $tpl->gotoBlock( "_ROOT" );

      return $tpl->getOutputContent();

  }

 /**
 * Vraci seznam pobocek
 * @return array
 */
  private function listPobocek(){



  	$select = Zend_Registry::get('zdb_game')->select()->from(array('pobocka'),array("pocet"=>'count(*)'));


  	if(isset($_REQUEST['filtr_id']) && mb_strlen($_REQUEST['filtr_id'])>0){

  		$select = $select->where('pobocka_id=?',intval($_REQUEST['filtr_id']));

  	}
  	if(isset($_REQUEST['filtr_fulltext']) && mb_strlen($_REQUEST['filtr_fulltext'])>0){

  		$select =  $select->orWhere('jmeno like "%'.Help::slash($_REQUEST['filtr_fulltext']).'%"')
  		                  ->orWhere('ulice like "%'.Help::slash($_REQUEST['filtr_fulltext']).'%"')
  		                  ->orWhere('mesto like "%'.Help::slash($_REQUEST['filtr_fulltext']).'%"')
  		                  ->orWhere('email like "%'.Help::slash($_REQUEST['filtr_fulltext']).'%"')
  		                  ->orWhere('umisteni like "%'.Help::slash($_REQUEST['filtr_fulltext']).'%"');

  	}
    if(isset($_REQUEST['filtr_aktivni'])){

    	$select = $select->where('pobocka_aktivni=?',1);
    }

    $stm  = $select->query();
    $rowD = $stm->fetchAll();

    $page = new Page($rowD[0]['pocet'],PAGE,"section=".$this->section.(isset($_REQUEST['filtr_aktivni'])?"&filtr_aktivni=1":"").'&'.(isset($_REQUEST['filtr_id']) && mb_strlen($_REQUEST['filtr_id']) > 0?"&filtr_id=".$_REQUEST['filtr_id']:"").'&'.(isset($_REQUEST['filtr_fulltext']) && mb_strlen($_REQUEST['filtr_fulltext']) > 0?"&filtr_fulltext=".$_REQUEST['filtr_fulltext']:"").'&');

    $this->paging = $page->getPage();

	$select = Zend_Registry::get('zdb_game')->select()->from(array('pobocka'),array('*'))
	               ->order('pobocka_id')
    	           ->limit(PAGE*$page->page, PAGE);

    if(isset($_REQUEST['filtr_id']) && mb_strlen($_REQUEST['filtr_id'])>0){

  		$select = $select->where('pobocka_id=?',intval($_REQUEST['filtr_id']));

    }
  	if(isset($_REQUEST['filtr_fulltext']) && mb_strlen($_REQUEST['filtr_fulltext'])>0){

  		$select =  $select->orWhere('jmeno like "%'.Help::slash($_REQUEST['filtr_fulltext']).'%"')
  		                  ->orWhere('ulice like "%'.Help::slash($_REQUEST['filtr_fulltext']).'%"')
  		                  ->orWhere('mesto like "%'.Help::slash($_REQUEST['filtr_fulltext']).'%"')
  		                  ->orWhere('email like "%'.Help::slash($_REQUEST['filtr_fulltext']).'%"')
  		                  ->orWhere('umisteni like "%'.Help::slash($_REQUEST['filtr_fulltext']).'%"');

  	}
    if(isset($_REQUEST['filtr_aktivni'])){

    	$select = $select->where('pobocka_aktivni=?',1);
    }

    $stm  = $select->query();
    $rowD = $stm->fetchAll();
//var_dump($rowD);
    return $rowD;

  }


 /**
 * Vytvoreni pobocky
 * @return void
 */
  public function vytvoritPobocku(){

  	$valide = true;

  	if(isset($_POST['send'])) {

  		$valide = $this->validePobocka();

  		if($valide)
        $valide = $this->updatePobocka($_POST);
  	}



  	$this->vrat .= $this->basicForm('Vytvořit pobočku',($valide?array():$_POST));

  }

   /**
 * Vytvoreni/Aktualizace dat
 * @param array $data pole dat
 * @return void
 */
  public function updatePobocka($data){

  	$dataX = array();

    if(Help::CheckDatum($data['smlouva_datum']))
      $data['smlouva_datum'] = It6_Date::toDbAsDate($data['smlouva_datum']);
    if(Help::CheckDatum($data['smlouva_datum_ucinnosti']))
      $data['smlouva_datum_ucinnosti'] = It6_Date::toDbAsDate($data['smlouva_datum_ucinnosti']);
    if(Help::CheckDatum($data['smlouva_datum']))
      $data['smlouva_datum_podpisu'] = It6_Date::toDbAsDate($data['smlouva_datum_podpisu']);
    if(Help::CheckDatum($data['smlouva_datum_ukonceni']))
      $data['smlouva_datum_ukonceni'] = It6_Date::toDbAsDate($data['smlouva_datum_ukonceni']);
    if(Help::CheckDatum($data['platnost_od']))
      $data['platnost_od'] = It6_Date::toDbAsDate($data['platnost_od']);


  	if(!Help::CheckDatumDB($data['smlouva_datum_ukonceni']))
      unset($data['smlouva_datum_ukonceni']);

  	foreach($data as $index=>$hodnota){
  		if($index == 'send')
        continue;
      if(in_array($index,$this->checkboxField)){
        foreach($hodnota as $k=>$h){
          $dataX[$index.'_'.$h] = 1;
        }
      }
      else
        $dataX[$index] = $hodnota;
    }

  	if(isset($_REQUEST['pobocka_id']) && ctype_digit($_REQUEST['pobocka_id'])){ //update
      try{
        Zend_Registry::get('zdb_game')->update('pobocka', $dataX,"pobocka_id=".intval($_REQUEST['pobocka_id']));
      }
      catch(Zend_Exception $e){
     	  throw new ExHandler($e->getMessage()  . __LINE__,"admin_ex_db");
      }

    }
    else{  //insert

      Zend_Registry::get('zdb_game')->beginTransaction();
      try{
        Zend_Registry::get('zdb_game')->insert('pobocka', $dataX);
      }
      catch(Zend_Exception $e){
     	  throw new ExHandler($e->getMessage()  . __LINE__,"admin_ex_db");
      }

      $pobocka_id = Zend_Registry::get('zdb_game')->lastInsertId();

      $uData = array();
      $uData['nick']     = $data['jmeno'];
      $uData['jmeno']    = $data['jmeno'];
      $uData['prijmeni'] = 'Pobočka #'.$pobocka_id;
      $uData['heslo']    = Help::cryptPass('pobocka'.$pobocka_id);
      $uData['email']    = $data['email'];
      $uData['ulice']    = $data['ulice'];
      $uData['psc']      = $data['psc'];
      $uData['zeme_id']  = 3; //TODO: don't use hardwired value
      $uData['mena_id']  = 8; //TODO: don't use hardwired value
      $uData['datum_registrace'] = It6_Date::dbNow();
      $uData['e_testovaci'] = 'pobocka';


      UzivatelHandle::insertUser($uData,array(),$pobocka_id);
      Zend_Registry::get('zdb_game')->commit();
    }

  	header('location:/?section='. $this->section .'&sucess=1&'.(isset($_REQUEST['pobocka_id']) && ctype_digit($_REQUEST['pobocka_id'])?'pobocka_id='.$_REQUEST['pobocka_id']:''));

    return true;
  }

   /**
 * Zakladni formular
 * @param string $head nazev sekce
 * @param array $data pole dat
 * @return void
 */
  private function basicForm($head='',$data){

  	  $tpl = new TemplatePower( '_tpl/pobockaBasicForm.html', T_BYFILE );
      $tpl->prepare();

      $tpl->assign("HEADLINE",$head);
  	  $tpl->assign("ACTION",'/?section='.$this->section);

  	  if(isset($_REQUEST['pobocka_id'])){
  	  	$tpl->assign("POBOCKA_ID",intval($_REQUEST['pobocka_id']));
  	  	$tpl->assign("DETAIL",$this->pobockaLinks());
  	  }

  	  if(count($this->basicError) > 0){

  	  	$tpl->assign("ERROR",'Opravte následující údaje');

  	  	foreach($this->basicError as $k=>$h){

  	  		$error_string = '';
  	  		foreach($h as $h2){

  	  			$error_string .= $h2.'<br />';

  	  		}

  	  		$tpl->assign("ERROR_". $k,$error_string);

  	  	}

  	  }
  	  else if(isset($_GET['sucess'])){

  	  	 $tpl->assign("SUCCESS",'Data byla úspěšně nahrána');

  	  }

      #SELECT#
      foreach($this->obdobi as $h){

      	$tpl->newBlock( "smlouva_vyuctovani" );
      	$tpl->assign( "VAL", $h );

      	if(isset($data['smlouva_vyuctovani']) && $data['smlouva_vyuctovani'] == $h) $tpl->assign( "SEL", 'selected="selected"');

      }

      $tpl->gotoBlock( "_ROOT" );


      foreach($this->obdobi as $h){

      	$tpl->newBlock( "smlouva_zalohy" );

      	$tpl->assign( "VAL", $h );

      	if(isset($data['smlouva_zalohy']) && $data['smlouva_zalohy'] == $h) $tpl->assign( "SEL", 'selected="selected"');

      }

      $tpl->gotoBlock( "_ROOT" );

      $kraj = $this->kraj();
      foreach($kraj as $k=>$h){

      	$tpl->newBlock( "smlouva_kraj" );
      	$tpl->assign( "VAL", $h );
      	$tpl->assign( "VAL2", $k );

      	if(isset($data['smlouva_kraj']) && $data['smlouva_kraj'] == $k) $tpl->assign( "SEL", 'selected="selected"');

      }

      $tpl->gotoBlock( "_ROOT" );

      #END SELECT#

  	  foreach($data as $index=>$hodnota){


  	  	if(in_array($index,$this->selectField));
  	  	else if(in_array($index,$this->checkboxField)){

  	  		if(is_array($hodnota)){
  	  		  foreach($hodnota as $k=>$h){

  	  			$tpl->assign( mb_strtoupper($index).'_'.mb_strtoupper($k), 'checked="checked"' );

  	  		  }
  	  		}else{

  	  		   if($hodnota == 1)$tpl->assign( mb_strtoupper($index), 'checked="checked"' );

  	  		}
  	  	}
    	else if(in_array($index,$this->radioField)){

  	  		$tpl->assign( mb_strtoupper($index).'_'.mb_strtoupper($hodnota), 'checked="checked"' );

  	  	}
  	  	else{

  	  		$tpl->assign( mb_strtoupper($index), $hodnota );

  	  	}

  	  }

      return $tpl->getOutputContent();

  }

     /**
 * Vraci kraje
 * @return array
 */
  public function kraj(){

  	 $select = Zend_Registry::get('zdb_game')->select()->from(array("a"=>'kraj'),array('a.kraj_id','a.nazev'));

    $stm  = $select->query();
    $rowD = $stm->fetchAll();


    $pole = array();
    foreach($rowD as $h){

    	$pole[$h['kraj_id']] = $h['nazev'];

    }

    return $pole;

  }


   /**
 * Validace zakladniho formulare
 * @return void
 */
  public function validePobocka(){

  	  if(!isset($_POST['jmeno']) || mb_strlen($_POST['jmeno']) < 1)  $this->basicError[0][] = 'Název pobočky musí být uveden';
  	  if(!isset($_POST['ulice']) || mb_strlen($_POST['ulice']) < 1)  $this->basicError[0][] = 'Ulice musí být uvedena';
  	  if(!isset($_POST['psc'])   || !ctype_digit($_POST['psc']))      $this->basicError[0][] = 'PSČ má špatný formát';
  	  if(!isset($_POST['mesto']) || mb_strlen($_POST['mesto']) < 1)  $this->basicError[0][] = 'Město musí být uvedeno';
  	  if(!isset($_POST['umisteni']) || mb_strlen($_POST['umisteni']) < 1) $this->basicError[0][] = 'Umístění musí být uvedeno';
  	  if(!isset($_POST['platnost_od']) || !Help::CheckDatum($_POST['platnost_od'])) $this->basicError[0][] = 'Datum platnosti má špatný formát';
  	  if(isset($_POST['email']) && mb_strlen($_POST['email']) > 0 && !Help::valideMail($_POST['email'])) $this->basicError[0][] = 'Email má špatný formát';
  	  if(!isset($_POST['telefon']) || !ctype_digit($_POST['telefon'])) $this->basicError[0][] = 'Telefon musí obsahovat pouze čísla';

  	  if(isset($_POST['bank_predcisly']) && mb_strlen($_POST['bank_predcisly']) > 0 && !ctype_digit($_POST['bank_predcisly'])) $this->basicError[1][] = 'Předčíslý musí být pouze číslo';
  	  if(isset($_POST['bank_cislouctu']) && mb_strlen($_POST['bank_cislouctu']) > 0 && !ctype_digit($_POST['bank_cislouctu'])) $this->basicError[1][] = 'Číslo účtu může být pouze číslo';
  	  if(isset($_POST['bank_kod_banky']) && mb_strlen($_POST['bank_kod_banky']) > 0 && !ctype_digit($_POST['bank_kod_banky'])) $this->basicError[1][] = 'Kód banky může být pouze číslo';
  	  if(isset($_POST['bank_mena']) && mb_strlen($_POST['bank_mena']) > 0 && !ctype_alpha($_POST['bank_mena'])) $this->basicError[1][] = 'Měna nesmí obsahovat čísla';

  	  if(isset($_POST['provize_predcisly']) && mb_strlen($_POST['provize_predcisly']) > 0 && !ctype_digit($_POST['provize_predcisly'])) $this->basicError[2][] = 'Předčíslý musí být pouze číslo';
  	  if(isset($_POST['provize_cislouctu']) && mb_strlen($_POST['provize_cislouctu']) > 0 && !ctype_digit($_POST['provize_cislouctu'])) $this->basicError[2][] = 'Číslo účtu může být pouze číslo';
  	  if(isset($_POST['provize_kod_banky']) && mb_strlen($_POST['provize_kod_banky']) > 0 && !ctype_digit($_POST['provize_kod_banky'])) $this->basicError[2][] = 'Kód banky může být pouze číslo';
  	  if(isset($_POST['provize_mena']) && mb_strlen($_POST['provize_mena']) > 0 && !ctype_alpha($_POST['provize_mena'])) $this->basicError[2][] = 'Měna nesmí obsahovat čísla';


  	  if(!isset($_POST['smlouva_datum']) || !Help::CheckDatum($_POST['smlouva_datum'])) $this->basicError[3][] = 'Datum smlouvy nemá správný formát';
  	  if(!isset($_POST['smlouva_datum_ucinnosti']) || !Help::CheckDatum($_POST['smlouva_datum_ucinnosti'])) $this->basicError[3][] =  'Datum účinnosti nemá správný formát' ;
  	  if(!isset($_POST['smlouva_datum_podpisu']) || !Help::CheckDatum($_POST['smlouva_datum_podpisu'])) $this->basicError[3][] = 'Datum podpisu nemá správný formát';
  	  if(isset($_POST['smlouva_datum_ukonceni']) && mb_strlen($_POST['smlouva_datum_ukonceni']) > 0 && !Help::CheckDatum($_POST['smlouva_datum_ukonceni'])) $this->basicError[3][] = 'Datum ukončení nemá správný formát';
  	  if(!isset($_POST['smlouva_manipulacni_poplatek']) || !ctype_digit($_POST['smlouva_manipulacni_poplatek'])) $this->basicError[3][] = 'Manipulační poplatek má špatný formát';
  	  if(!isset($_POST['smlouva_vyuctovani']) || $_POST['smlouva_vyuctovani'] == "0" || !in_array($_POST['smlouva_vyuctovani'],$this->obdobi)) $this->basicError[3][] = 'Zvolte  vyučtování';
  	  if(!isset($_POST['smlouva_zalohy']) || $_POST['smlouva_zalohy'] == "0" || !in_array($_POST['smlouva_zalohy'],$this->obdobi)) $this->basicError[3][] = 'Zvolte zálohy ';
      if(!isset($_POST['smlouva_kraj']) || $_POST['smlouva_kraj'] == "0") $this->basicError[3][] = 'Zvolte kraj';

  	  if(count($this->basicError) > 0)
        return false;
      else
        return true;
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

    return $this->vrat;

  }



}

?>
