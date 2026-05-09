<?php
/**

 * @package    XML
 */

/**
 * Trida improtuje sazky
 *
 *
 * <code>
 *
 * </code>
 *
 * @package    Ciselniky
 */

class BetRadarLiveBetting{


/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */
private static $dbGame;

/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */
private static $tplRoot = '';

/**
 * identifikator spojeni
 * @access private
 * @var object
 */
private static $sock = null;

/**
 * pole poslanych upozorneni
 * @access private
 * @var array
 */
private static $notice = array();

/**
 * zdali jde o testovaci prostredi
 * @access private
 * @var bool
 */
private static $testEnv = false;

/**
 * debug rezim
 * @access private
 * @var bool
 */
public static $debug = false;

/**
 * posledni nacteny status z Betradaru
 * @access private
 * @var string
 */
private static $lastStatus = '';

/**
 * cas kdy prisel posledni alive message
 * @access private
 * @var int
 */
private static $lastAliveTime = 0;

/**
 * cas v sec. po kterem se kontroluje obnova dat/ alive zprava
 * @access private
 * @var int
 */
private static $aliveTime = 30;

/**
 * cas v sec. po kterem se zada o nove sazky na dalsi tyden
 * @access private
 * @var int
 */
private static $metaTime = 43200;

/**
 * cas kdy prisel posledni meta message
 * @access private
 * @var int
 */
private static $lastMetaTime = 0;

/**
 * cas kdy prisel posledni status message
 * @access private
 * @var int
 */
private static $lastStatusTime = 0;

/**
 * cas kola
 * @access private
 * @var int
 */
private static $timestamp = 0;

/**
 * cas v sec. po kterem se posila u otevrenych sazek jejich stav 0=ok 1=critical 2=closed
 * @access private
 * @var int
 */
private static $statusTime = 60;

/**
 * Hranice v procentech kdy je sazka povazovana za critical
 * @access private
 * @var int
 */
private static $riskLimit = 80;

/**
 * Id bookmakera
 * @access private
 * @var int
 */
private static $bookId;

/**
 * Prihlasovaci heslo
 * @access private
 * @var int
 */
private static $key;

/**
 * xml objekt
 * @access private
 * @var object
 */
private static $xml;

/**
 * pole druhu z pohledu Betradaru
 * @access public
 * @var array
 */
public static  $betTypeReverse = array();

/**
 * pole druhu
 * @access public
 * @var array
 */
public static $betType = array();

/**
 * pole sportu prevod z betradar id na  id
 * @access public
 * @var array
 */
public static $sport = array();

/**
 * pole sportu prevod z  id
 * @access public
 * @var array
 */
public static $sportReverse = array();

/**
 * data
 * @access private
 * @var object
 */
private static $rawData;

  /**
 * maily adminsitratoru
 * @access private
 * @var string
 */
private  static $administrator = '';

  /**
 * config
 * @access private
 * @var string
 */
public  static $config = '';

 public function __construct(){}

/**
* Spojeni s databazi
*
*return bool
*/
  public static function dbConnect(){

     try{

      self::$dbGame = Zend_Db::factory(self::$config->db->adapter, self::$config->db->config->toArray());
      Zend_Db_Table::setDefaultAdapter(self::$dbGame);
      self::$dbGame->query('SET NAMES utf8');
      self::$dbGame->setFetchMode(Zend_Db::FETCH_ASSOC);

     }catch(Zend_Exception $e){

        self::Error("Database connection failed: " . $e->getMessage()  . "\n",'Live bet / error');exit;

     }

    return true;

  }

 /**
 * Nastaveni konfiguracnich promennych
 * @param bool $testEnv  jde o test vyvoj
 * @param bool $debug  debug
 * @param string $tplRoot  cestak templatum
 * @param object $sock   spojeni
 * return bool
 */
  public static function setConfig($bookId,$key,$testEnv=false,$debug=false,$tplRoot='',$aliveTime=30,$metaTime=43200,$statusTime = 60,$riskLimit=80,$sock){


     self::$bookId = $bookId;
     self::$key = $key;
     self::$testEnv = $testEnv;
     self::$debug = $debug;
     self::$tplRoot = $tplRoot;
     self::$sock = $sock;
     self::$aliveTime = $aliveTime;
     self::$metaTime = $metaTime;
     self::$statusTime = $statusTime;
     self::$riskLimit = $riskLimit;

  }


   /**
 * Tato metoda se snazi zalogovat
 * @param string $data  xml
 * return void
 */
  public static function generalParse($data){

    self::$lastStatus = '';
    /*$tidy = new tidy;
    $config = array('indent' => TRUE,
                'output-xml' => TRUE);

    $tidy = tidy_parse_string($buffer, $config, 'UTF8');

    $tidy->cleanRepair();*/

    self::$xml = simplexml_load_string($data);
    self::$rawData = $data;

    if(!self::$xml || !is_object(self::$xml)) {self::$lastStatus = false;return;}


    foreach(self::$xml->attributes() as $k=>$h){

        if($k == 'status') self::$lastStatus = $h;

    }

  }


 /**
 * Tato metoda startuje cteni
 * return void
 */
  public static function start(){


    self::debug(false,"Socket reading started");
    $data  =  fread(self::$sock,5000000);
    self::debug(false,"Socket reading finished, bytes read: ".strlen($data));
    //$data .= fread(self::$sock,5000000);
    //$data = stream_get_contents(self::$sock);

    self::$timestamp = time();

    self::debug(false,"General parse start()");
    self::generalParse($data);

    #Alive#
    if(self::$lastAliveTime == 0) {self::$lastAliveTime = time();}
    if((time() - self::$lastAliveTime) > self::$aliveTime) self::checkAliveStatus();

    #Status from client#
    if(self::$lastStatusTime == 0) self::$lastStatusTime = time();
   // if((time() - self::$lastStatusTime) > self::$statusTime) self::clientSystemStatus();


    #Get meta info#
    if( (self::$lastMetaTime == 0 || ((time() - self::$lastMetaTime) > self::$metaTime) ) ) {
        self::getMetaInfo();
        self::$dbGame->closeConnection();
        self::dbConnect();
    }

    //if((self::$lastStatus=="change" || self::$lastStatus=="rollback" || self::$lastStatus=="clearbet")
    if(method_exists('BetRadarLiveBetting',self::$lastStatus.'Status')) {self::debug(false,"Process status:".self::$lastStatus);eval('self::'.self::$lastStatus.'Status();');}


    self::debug(" \n --BEGIN-- \n". It6_Date::dbNow() ." \n" . self::$rawData ." \n --END-- \n");


  }

   /**
 * Ziskani live sazek na dalsi obdobi
 * return void
 */
  public static function getMetaInfo(){

    self::debug(false,"call getMetaInfo()");

    $stpl = new TemplatePower(self::$tplRoot.'meta_info.tpl');
    $stpl->prepare();

    $stpl->assign("BID",self::$bookId);

    $out = $stpl->getOutputContent();


    self::debug($out,"Write  meta info");
    $out = mb_ereg_replace("[\t\n\r]+","\n",$out);
    fwrite(self::$sock,$out);

    self::debug(false,"Read  meta info");

    $d = '';
    //do{
     $d .= fread(self::$sock,5000000);flush();
    if(self::$lastMetaTime != 0)$d .= fread(self::$sock,5000000).fread(self::$sock,5000000); flush();
     //$d .= fread(self::$sock,2048);
    //  ob_end_flush();
    //}while(!feof(self::$sock));
    //self::debug($d);


    self::debug(false,"General parse getMetaInfo()");
    self::generalParse($d);


    if(self::$lastStatus == 'meta'){


          self::metaStatus();

    }

    self::$lastMetaTime = time();

  }

     /**
 * Kdyz prijde status undocancelbet
 * return void
 */
  public static function undocancelbetStatus(){

    self::debug(false,"Process status: undocancelbet");

    if(isset(self::$xml['starttime'])) return;

    foreach(self::$xml->Match as $match){

        foreach($match->Odds as $odds){



            $select = self::$dbGame->select()->from(array('a'=>'live'),array('a.event_id','a.sazka_id','a.close'))
                                           ->join(array('l' => 'live_betradar_event'),'a.event_id = l.event_id')
                                           ->join(array('s' => 'sazky'),'a.sazka_id = s.sazka_id')
                                           ->where("s.proplacena=?",0)
                                           ->where("s.overena=?",0)
                                           ->where("l.no_update=?",0)
                                           ->where("a.betradar_sazka_id=?",intval($odds['id']))
                                           ->where("l.betradar_event_id=?",intval($match['matchid']));
          $stm  = $select->query();
          $row = $stm->fetchAll();

          if(count($row)>0){

              self::$dbGame->beginTransaction();



              $date =  It6_Date::dbNow();
              $data = array();
              $data['aktualizace'] =  $date;
              $data['aktualizace_sazka'] =  $date;
              $data['close'] =  0;

              try{

                    self::$dbGame->update('live_sazka', $data,"event_id=".$row[0]['event_id']." and betradar_sazka_id=".intval($odds['id']));

              }catch(Zend_Exception $e){

                    BetRadarLiveBetting::Error("Nepodarilo se cancel live_sazky sazka id: ".$row[0]['sazka_id']." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
                    self::$dbGame->rollBack();
                    continue;
              }


              $data = array();
              $data['platna_do'] =  '2020-02-02';
              $data['status'] =  ($odds['active'] == 1?0:2);

              try{

                    self::$dbGame->update('sazky', $data,"sazka_id=".$row[0]['sazka_id']." and proplacena=0");

              }catch(Zend_Exception $e){

                    BetRadarLiveBetting::Error("Nepodarilo se cancel sazky sazka id: ".$row[0]['sazka_id']." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
                    self::$dbGame->rollBack();
                    continue;
              }


              self::$dbGame->commit();

          }

        }

    }

  }


     /**
 * Kdyz prijde status cancelbet
 * return void
 */
  public static function cancelbetStatus(){

    self::debug(false,"Process status: cancelbet");

    foreach(self::$xml->Match as $match){

        foreach($match->Odds as $odds){

            $select = self::$dbGame->select()->from(array('a'=>'live'),array('a.event_id','a.sazka_id','a.close'))
                                           ->join(array('l' => 'live_betradar_event'),'a.event_id = l.event_id')
                                           ->where("a.betradar_sazka_id=?",intval($odds['id']))
                                           ->where("l.no_update=?",0)
                                           ->where("l.betradar_event_id=?",intval($match['matchid']));
          $stm  = $select->query();
          $row = $stm->fetchAll();

          if(count($row)>0){

              self::$dbGame->beginTransaction();

              $date =  It6_Date::dbNow();
              $data = array();
              $data['aktualizace'] =  $date;
              $data['aktualizace_sazka'] =  $date;
              $data['close'] =  1;

              try{

                    self::$dbGame->update('live_sazka', $data,"event_id=".$row[0]['event_id']." and betradar_sazka_id=".intval($odds['id']));

              }catch(Zend_Exception $e){

                    BetRadarLiveBetting::Error("Nepodarilo se cancel live_sazky sazka id: ".$row[0]['sazka_id']." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
                    self::$dbGame->rollBack();
                    continue;
              }


              $data = array();
              if($row[0]['close'] != 1) $data['platna_do'] =  $date;
              $data['status'] =  1;

              try{

                    self::$dbGame->update('sazky', $data,"sazka_id=".$row[0]['sazka_id']." and proplacena=0");

              }catch(Zend_Exception $e){

                    BetRadarLiveBetting::Error("Nepodarilo se cancel sazky sazka id: ".$row[0]['sazka_id']." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
                    self::$dbGame->rollBack();
                    continue;
              }


              self::$dbGame->commit();

          }

        }

    }

  }

   /**
 * Kdyz prijde status rollback
 * return void
 */
  public static function rollbackStatus(){

    self::debug(false,"Process status: rollback");

    foreach(self::$xml->Match as $match){

        foreach($match->Odds as $odds){

          $select = self::$dbGame->select()->from(array('a'=>'live'),array('a.event_id','a.sazka_id','a.typ_id','a.podtyp_id'))
                                           ->join(array('l' => 'live_betradar_event'),'a.event_id = l.event_id')
                                           ->where("a.betradar_sazka_id=?",intval($odds['id']))
                                           ->where("l.no_update=?",0)
                                           ->where("l.betradar_event_id=?",intval($match['matchid']));
          $stm  = $select->query();
          $row = $stm->fetchAll();

          if(count($row)>0){

            self::$dbGame->beginTransaction();

            if(BetRadarResult::betRollback(self::$dbGame,$row[0]['sazka_id'])){

              $date =  It6_Date::dbNow();
              $data = array();
              $data['aktualizace'] =  $date;
              $data['aktualizace_sazka'] =  $date;

              try{

                    self::$dbGame->update('live_sazka', $data,"event_id=".$row[0]['event_id']." and betradar_sazka_id=".intval($odds['id']));

              }catch(Zend_Exception $e){

                    BetRadarLiveBetting::Error("Nepodarilo se updatovat live_sazky sazku id: ".$row[0]['sazka_id']." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
                    self::$dbGame->rollBack();
                    continue;
              }



            }

            self::$dbGame->commit();


          }

        }

    }


  }

 /**
 * Kdyz prijde status clearbet
 * return void
 */
  public static function clearbetStatus(){

    self::debug(false,"Process status: clearbet");

    foreach(self::$xml->Match as $match){

        foreach($match->Odds as $odds){

          $select = self::$dbGame->select()->from(array('a'=>'live'),array('a.event_id','a.sazka_id','a.typ_id','a.podtyp_id','a.no_update'))
                                           ->join(array('l' => 'live_betradar_event'),'a.event_id = l.event_id')
                                           ->where("a.betradar_sazka_id=?",intval($odds['id']))
                                           ->where("l.no_update=?",0)
                                           ->where("l.betradar_event_id=?",intval($match['matchid']))
                                           ->group('a.sazka_id');
          $stm  = $select->query();
          $row = $stm->fetchAll();

          if(count($row)>0 && $row[0]['no_update'] != 1){

            self::$dbGame->beginTransaction();

            $time = intval(substr(self::$xml['timestamp'],0,-3)) -  intval(self::$xml['time']);

            $fce = self::$betType[$row[0]['typ_id']][$row[0]['podtyp_id']][5].'Result';

            if(BetRadarResult::$fce(self::$dbGame,$odds,$row[0]['sazka_id'],$time)){

              $date =  It6_Date::dbNow();
              $data = array();
              $data['aktualizace'] =  $date;
              $data['aktualizace_sazka'] =  $date;
              $data['close'] =  1;

              try{

                    self::$dbGame->update('live_sazka', $data,"event_id=".$row[0]['event_id']." and betradar_sazka_id=".intval($odds['id']));

              }catch(Zend_Exception $e){

                    BetRadarLiveBetting::Error("Nepodarilo se updatovat live_sazky sazku id: ".$row[0]['sazka_id']." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
                    self::$dbGame->rollBack();
                    continue;
              }



            }

            self::$dbGame->commit();

          }


        }


    }


  }

    /**
 * Kdyz prijde status cards
 * return void
 */
  public static function cardsStatus(){
    self::debug(false,"Process status: card");
    foreach(self::$xml->Match as $match){
        self::debug(false,"Process status: card -> Match ".$match['matchid']);
        $select = self::$dbGame->select()->from(array('a'=>'live_betradar_event'),array('a.event_id','l.l_sport_id','l.minute'))
                                              ->join(array('l' => 'live_event'),'a.event_id = l.event_id')
                                              ->where("a.no_update=?",0)
                                              ->where("a.betradar_event_id=?",intval($match['matchid']));
        $stm  = $select->query();
        $row = $stm->fetchAll();

        if(count($row) > 0 && ($row[0]['l_sport_id'] == 1001) && isset($match->Score)){

            foreach($match->Score as $score){



                     $select = self::$dbGame->select()->from(array('a'=>'live_betradar_score_card'),array('a.sc_id'))
                                                      ->where("a.sc_type=?",2)
                                                      ->where("a.betradar_event_id=?",intval($match['matchid']))
                                                      ->where("a.sc_id=?",intval($score['id']) );
                     $stm  = $select->query();
                     $row2 = $stm->fetchAll();

                     if(count($row2) == 0){

                        $text = (isset($score['player']) && $score['player'] != "-1"?$score['player']:'');

                        if($score['team'] == 'home' && $score['type'] == 'yellow')$preklad = BetRadarLiveBetting::$sportReverse[$row[0]['l_sport_id']]['card'][0];
                        else if($score['team'] == 'home' && ($score['type'] == 'yellowred' || $score['type'] == 'red') )$preklad = BetRadarLiveBetting::$sportReverse[$row[0]['l_sport_id']]['card'][1];
                        else if($score['team'] == 'away' && $score['type'] == 'yellow')$preklad = BetRadarLiveBetting::$sportReverse[$row[0]['l_sport_id']]['card'][2];
                        else if($score['team'] == 'away' && ($score['type'] == 'yellowred' || $score['type'] == 'red'))$preklad = BetRadarLiveBetting::$sportReverse[$row[0]['l_sport_id']]['card'][3];

                        $minute = intval($score['time']);


                        try {


                         $data = array();
                         if($score['team'] == 'home' && $score['type'] == 'yellow')$data['yellow_card_home'] =  'yellow_card_home+1';
                         else if($score['team'] == 'away' && $score['type'] == 'yellow')$data['yellow_card_away'] =  'yellow_card_away+1';
                         else if($score['team'] == 'home' && ($score['type'] == 'yellowred' || $score['type'] == 'red') )$data['red_card_home'] =  'red_card_home+1';
                         else if($score['team'] == 'away' && ($score['type'] == 'yellowred' || $score['type'] == 'red'))$data['red_card_away'] =  'red_card_away+1';

                         self::$dbGame->update('live_'.$row[0]['l_sport_id'], $data,'event_id='.$row[0]['event_id']);

                         $data = array();
                         $data['aktualizace'] =  It6_Date::dbNow();

                         self::$dbGame->update('live_event', $data,'event_id='.$row[0]['event_id']);

                         $data = array();
                         $data['sc_type'] =  2;
                         $data['betradar_event_id'] =  intval($match['matchid']);
                         $data['sc_id'] =  intval($score['id']);

                         self::$dbGame->insert('live_betradar_score_card', $data);

                         $data = array();
                         $data['event_id'] =  $row[0]['event_id'];
                         $data['time'] =  ($minute==0?$row[0]['minute']:$minute);
                         $data['text'] =  $preklad;
                         $data['book_text'] =  $text;

                         self::$dbGame->insert('live_info', $data);


                        }catch (Exception $e) {

                         BetRadarLiveBetting::Error("Nepodarilo se aktualizovat score: ".$e->getMessage().__LINE__,'Live bet / error');

                        }


                     }


                     if(isset($score['canceled']) && $score['canceled']=="true"){

                         self::$dbGame->delete('live_betradar_score_card', 'sc_type=2 and betradar_event_id='.intval($match['matchid']).' and sc_id='.intval($score['id']));

                     }

            }


        }


    }



  }

    /**
 * Kdyz prijde status score
 * return void
 */
  public static function scoreStatus(){

    self::debug(false,"Process status: score");

    foreach(self::$xml->Match as $match){
        self::debug(false,"Process status: score -> Match ".$match['matchid']);
        $select = self::$dbGame->select()->from(array('a'=>'live_betradar_event'),array('a.event_id','l.l_sport_id','l.minute'))
                                              ->join(array('l' => 'live_event'),'a.event_id = l.event_id')
                                              ->where("a.no_update=?",0)
                                              ->where("a.betradar_event_id=?",intval($match['matchid']));
        $stm  = $select->query();
        $row = $stm->fetchAll();

        if(count($row) > 0 && ($row[0]['l_sport_id'] == 1001 || $row[0]['l_sport_id'] == 1011) && isset($match->Score)){

            foreach($match->Score as $score){


                if($score['type'] == "live"){

                     $select = self::$dbGame->select()->from(array('a'=>'live_betradar_score_card'),array('a.sc_id'))
                                                      ->where("a.sc_type=?",1)
                                                      ->where("a.betradar_event_id=?",intval($match['matchid']))
                                                      ->where("a.sc_id=?",intval($score['id']) );
                     $stm  = $select->query();
                     $row2 = $stm->fetchAll();

                     if(count($row2) == 0){

                        $text = (isset($score['player']) && $score['player'] != "-1"?$score['player']:'');

                        $preklad = ($score['scoringteam'] == 'home'?BetRadarLiveBetting::$sportReverse[$row[0]['l_sport_id']]['score'][0]:BetRadarLiveBetting::$sportReverse[$row[0]['l_sport_id']]['score'][1]);

                        $minute = intval($score['time']);


                        try {

                         list($hm,$aw) = explode(":",$match['score']);
                         $data = array();
                         $data['score_home'] =  $hm;
                         $data['score_away'] =  $aw;

                         self::$dbGame->update('live_'.$row[0]['l_sport_id'], $data,'event_id='.$row[0]['event_id']);

                         $data = array();
                         $data['aktualizace'] =  It6_Date::dbNow();
                         if(isset($match['matchtime']))  $data['minute'] = $match['matchtime'];

                         self::$dbGame->update('live_event', $data,'event_id='.$row[0]['event_id']);

                         $data = array();
                         $data['sc_type'] =  1;
                         $data['betradar_event_id'] =  intval($match['matchid']);
                         $data['sc_id'] =  intval($score['id']);

                         self::$dbGame->insert('live_betradar_score_card', $data);

                         $data = array();
                         $data['event_id'] =  $row[0]['event_id'];
                         $data['time'] =  ($minute==0 || $minute==-1?$row[0]['minute']:$minute);
                         $data['text'] =  $preklad;
                         $data['book_text'] =  $text;

                         self::$dbGame->insert('live_info', $data);


                        }catch (Exception $e) {

                         BetRadarLiveBetting::Error("Nepodarilo se aktualizovat score: ".$e->getMessage().__LINE__,'Live bet / error');

                        }

                     }

                }
                else if($score['type'] == "ft"){

                        try {

                         list($hm,$aw) = explode(":",$match['score']);

                         $data = array();
                         $data['score_home'] =  intval($score['home']);
                         $data['score_away'] =  intval($score['away']);

                         self::$dbGame->update('live_'.$row[0]['l_sport_id'], $data,'event_id='.$row[0]['event_id']);

                         $data = array();
                         $data['aktualizace'] =  It6_Date::dbNow();


                         self::$dbGame->update('live_event', $data,'event_id='.$row[0]['event_id']);


                        }catch (Exception $e) {

                         BetRadarLiveBetting::Error("Nepodarilo se aktualizovat score: ".$e->getMessage().__LINE__,'Live bet / error');

                        }

                }
                else if($score['type'] == "ht" && $row[0]['l_sport_id'] == 1001){

                        try {

                         list($hm,$aw) = explode(":",$match['score']);

                         $data = array();
                         $data['first_half_home'] =  intval($score['home']);
                         $data['first_half_away'] =  intval($score['away']);

                         self::$dbGame->update('live_'.$row[0]['l_sport_id'], $data,'event_id='.$row[0]['event_id']);

                         $data = array();
                         $data['aktualizace'] =  It6_Date::dbNow();


                         self::$dbGame->update('live_event', $data,'event_id='.$row[0]['event_id']);


                        }catch (Exception $e) {

                         BetRadarLiveBetting::Error("Nepodarilo se aktualizovat score: ".$e->getMessage().__LINE__,'Live bet / error');

                        }

                }


                if(isset($score['canceled']) && $score['canceled']=="true"){

                         self::$dbGame->delete('live_betradar_score_card', 'sc_type=1 and betradar_event_id='.intval($match['matchid']).' and sc_id='.intval($score['id']));

                }

            }


        }


    }



  }

  /**
 * Kdyz prijde status betstart
 * return void
 */
  public static function betstartStatus(){
    
  	return; //zatim nenecham provadet spoustelo to i spatne kurzy asi maji v dokumentaci spatne popsane nema spoustet vsechny sazky
  	
    if(!isset(self::$xml->Match)){

        $row = BetRadarLiveBet::getStopBet();

        foreach($row as $h){

            BetRadarLiveBet::runBet(self::$dbGame,$h['sazka_id']);

        }

    }else{

        foreach(self::$xml->Match as $match){

            $select = self::$dbGame->select()->from(array('a'=>'live_betradar_event'),array('l.sazka_id','a.event_id'))
                                              ->join(array('l' => 'live_sazka'),'a.event_id = l.event_id')
                                              ->where("a.no_update=?",0)
                                              ->where("a.betradar_event_id=?",intval($match['matchid']));
            $stm  = $select->query();
            $row = $stm->fetchAll();

            foreach($row as $h){

              BetRadarLiveBet::runBet(self::$dbGame,$h['sazka_id']);

              $date =  It6_Date::dbNow();
              $data = array();
              $data['aktualizace'] =  $date;
              $data['aktualizace_sazka'] =  $date;

              try{

               self::$dbGame->update('live_sazka', $data,"event_id=".$h['event_id']." and sazka_id=".intval($h['sazka_id']));

              }catch(Zend_Exception $e){

                    BetRadarLiveBetting::Error("Nepodarilo se updatovat live_sazky sazku id: ".$row[0]['sazka_id']." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
                    $dbGame->rollBack();
                    return false;
              }

            }

        }

    }

  }

  /**
 * Kdyz prijde status betstop
 * return void
 */
  public static function betstopStatus(){

    if(!isset(self::$xml->Match)){
        self::debug(false,"Stop match: #1");
        $row = BetRadarLiveBet::getRunBet();

        foreach($row as $h){

            BetRadarLiveBet::stopBet(self::$dbGame,$h['sazka_id']);
              
              $date =  It6_Date::dbNow();
              $data = array();
              $data['aktualizace'] =  $date;
              $data['aktualizace_sazka'] =  $date;

              try{

               self::$dbGame->update('live_sazka', $data,"sazka_id=".intval($h['sazka_id']));

              }catch(Zend_Exception $e){

                    BetRadarLiveBetting::Error("Nepodarilo se updatovat live_sazky sazku id: ".$row[0]['sazka_id']." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
                  
              }
              
        }

    }else{

        foreach(self::$xml->Match as $match){
            self::debug(false,"Stop match: ".intval($match['matchid']));
            $select = self::$dbGame->select()->from(array('a'=>'live_betradar_event'),array('l.sazka_id','a.event_id'))
                                              ->join(array('l' => 'live_sazka'),'a.event_id = l.event_id')
                                              ->where("a.no_update=?",0)
                                              ->where("a.betradar_event_id=?",intval($match['matchid']));
            $stm  = $select->query();
            $row = $stm->fetchAll();

            foreach($row as $h){
            self::debug(false,"Stop bet: ".$h['sazka_id']);
              BetRadarLiveBet::stopBet(self::$dbGame,$h['sazka_id']);

              $date =  It6_Date::dbNow();
              $data = array();
              $data['aktualizace'] =  $date;
              $data['aktualizace_sazka'] =  $date;

              try{

               self::$dbGame->update('live_sazka', $data,"event_id=".$h['event_id']." and sazka_id=".intval($h['sazka_id']));

              }catch(Zend_Exception $e){

                    BetRadarLiveBetting::Error("Nepodarilo se updatovat live_sazky sazku id: ".$row[0]['sazka_id']." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
                    $dbGame->rollBack();
                    return false;
              }

            }


        }

    }

  }

   /**
 * Kdyz prijde status meta
 * return void
 */
  public static function metaStatus(){

        self::debug(false,"Process status: meta");
        
        $mail = '';
        
        foreach(self::$xml->Match as $match){


            if($match['status'] == "not_started"){

                if($match['active'] == 0){

                    $select = self::$dbGame->select()->from('live_betradar_event',array('event_id'))->where("no_update=?",0)->where("betradar_event_id=?",intval($match['matchid']));
                    $stm  = $select->query();
                    $row = $stm->fetchAll();

                    if(count($row) > 0){

                     //  $where = self::$dbGame->quoteInto('event_id = ?', $row[0]['event_id']);
                      // $where .= ' and '.self::$dbGame->quoteInto('stav = ?', LIVE_NOT_STARTED);
                     //  $rows_affected = self::$dbGame->delete('live_event', $where);

                    }

                }else{



                $select = self::$dbGame->select()->from(array('a'=>'udalost'),array('a.udalost_id'))
                                                 ->join(array('b'=>'udalost_betradar'),'a.udalost_id=b.udalost_id')
                                                 ->where("b.betradar_udalost_id=?",$match->MatchInfo->Tournament['id']);
                $stm  = $select->query();
                $row = $stm->fetchAll();

                if(!isset($row[0]['udalost_id'])) BetRadarLiveBetting::Error("Nepodarilo se najit betradar udalost id  v databazi: #".$match->MatchInfo->Tournament['id'].'('.$match->MatchInfo->Sport.' - >'.$match->MatchInfo->Tournament.')'.__LINE__,'Live bet / error');
                else{

                    $select = self::$dbGame->select()->from(array('a'=>'live_betradar_event'),array('eid'=>'a.event_id','b.start_date'))
                                                     ->join(array('b'=>'live_event'),'a.event_id=b.event_id')
                                                     ->where("betradar_event_id=?",$match['matchid']);
                    $stm  = $select->query();
                    $row2 = $stm->fetchAll();

                    if(count($row2) > 0 || $match['status'] != 'not_started'){
                    	
                      try {
                       
                       if(date("Y-m-d H:i:s",substr($match->MatchInfo->DateOfMatch,0,-3)) != $row2[0]['start_date']){	
                      	
                       $data = array(
                        'start_date'      => date("Y-m-d H:i:s",substr($match->MatchInfo->DateOfMatch,0,-3))
                        );
                       
                        self::$dbGame->update('live_event', $data,'event_id='.$row2[0]['eid']);
                        $subject = 'ZMĚNA ČASU LIVE ZÁPASU ' ;
                        $mail .= 'Změna času zápasu: '.date("Y-m-d H:i:s",intval(substr($match->MatchInfo->DateOfMatch,0,-3))).' - > '.$match->MatchInfo->Sport.' - > '.$match->MatchInfo->Tournament. " - > " . $match->MatchInfo->HomeTeam ." : ".$match->MatchInfo->AwayTeam." \n";
                        
                       }
                        
                      }catch (Exception $e) {

      
                        BetRadarLiveBetting::Error("Nepodarilo se aktualizovat live udalost: ".$e->getMessage().__LINE__,'Live bet / error');

                   
                      
                      }
                      
                    }
                    else{
                     
                    	$subject = 'IMPORT NOVE LIVE SAZKY ' ;
                    	
                    self::$dbGame->beginTransaction();

                      try {

                      $data = array(
                        'start_date'      => date("Y-m-d H:i:s",intval(substr($match->MatchInfo->DateOfMatch,0,-3))),

                        'l_sport_id'      => self::$sport[intval($match->MatchInfo->Sport['id'])]['id'],
                        'l_udalost_id'    =>  $row[0]['udalost_id'],
                        'home_team'       => $match->MatchInfo->HomeTeam,
                        'away_team'       => $match->MatchInfo->AwayTeam,
                        'vypnuto'       => 1

                       );
                       self::$dbGame->insert('live_event', $data);
                       $id = self::$dbGame->lastInsertId();

                       $data = array(
                          'event_id'          => $id,
                          'betradar_event_id' => $match['matchid'],
                          'last_act'          =>  time(),
                          'no_update'         => 1

                       );
                       self::$dbGame->insert('live_betradar_event', $data);



                      }catch (Exception $e) {

                        self::$dbGame->rollBack();

                        BetRadarLiveBetting::Error("Nepodarilo se zalozit live udalost v databazi  v databazi: ".$e->getMessage().__LINE__,'Live bet / error');

                      }


                      try{

                         $data = array(
                           'event_id'          => $id,
                           'score_home'      => 0,
                           'score_away'      => 0
                          );

                          $n = self::$dbGame->insert('live_'.self::$sport[intval($match->MatchInfo->Sport['id'])]['id'], $data);

                          self::$dbGame->commit();

                       }catch (Exception $e) {

                        self::$dbGame->rollBack();

                        BetRadarLiveBetting::Error("Nepodarilo se zalozit live udalost live_sportid v databazi  v: ".$e->getMessage().__LINE__,'Live bet / error');

                       }

                       $mail .= date("Y-m-d H:i:s",intval(substr($match->MatchInfo->DateOfMatch,0,-3))).' - > '.$match->MatchInfo->Sport.' - > '.$match->MatchInfo->Tournament. " - > " . $match->MatchInfo->HomeTeam ." : ".$match->MatchInfo->AwayTeam." \n";
                       
                    }



                  }

                }

            }

        }
  
       if(mb_strlen($mail) > 0) mail('info@compbet.com',$subject.date('d.m.Y H:i:s'),$mail  );
        
  }


 /**
 * Zaslani statusu rozbehnutych sazek, risk limit status / ukoncne sazky
 * return void
 */
  public static function clientSystemStatus(){

    self::debug(false,"call clientSystemStatus()");


    $bets = BetRadarLiveBet::getBetStartedLive(self::$dbGame);


    $stpl = new TemplatePower(self::$tplRoot.'bs_status.tpl');
    $stpl->prepare();

    $stpl->assign("BID",self::$bookId);
    $stpl->assign("TIME",self::$timestamp);

    $bet_ar = $event_ar = array();
    foreach($bets as $h){

        $aktualizace = It6_Date::toDb($h['aktualizace']);

        if(self::$lastStatusTime > ($aktualizace+(4*self::$aliveTime)) && ($h['status'] == LIVE_UNFINISHED || $h['status'] == LIVE_END)) continue; //ukoncene zapasy se nebudou porad posilat

        if(!isset($event_ar[$h['betradar_event_id']])){
            $event_ar[$h['betradar_event_id']]['id'] = $h['betradar_event_id'];
            if($h['status'] == LIVE_UNFINISHED || $h['status'] == LIVE_END) $event_ar[$h['betradar_event_id']]['active'] = 0;
            else $event_ar[$h['betradar_event_id']]['active'] = 1;
        }

        if(!isset($bet_ar[$h['sazka_id']])){
        if($h['risk_limit_balance'] < $h['risk_limit']) $risk = ($h['risk_limit_balance']/$h['risk_limit'])*100;else $risk  = 100;

        if(($h['close'] != 1 || ($h['status']!=0 && $h['status']!=2)) && $risk < self::$riskLimit) $bet_ar[$h['sazka_id']]['active'] = 1;//active = 0
        else if($h['risk_limit_balance'] >= $h['risk_limit']) $bet_ar[$h['sazka_id']]['active'] = 2;//active 3
        else if($risk >= self::$riskLimit) $bet_ar[$h['sazka_id']]['active'] = 3;//active 2

        if($h['close'] != 1 || ($h['status']!=0 && $h['status']!=2)) $bet_ar[$h['sazka_id']]['match_active'] = 1;
        else $bet_ar[$h['sazka_id']]['match_active'] = 0;



        $bet_ar[$h['sazka_id']]['betradar_sazka_id'] = $h['betradar_sazka_id'];
        $bet_ar[$h['sazka_id']]['typ_id'] = $h['typ_id'];
        $bet_ar[$h['sazka_id']]['podtyp_id'] = $h['podtyp_id'];
        }
    }


    foreach($event_ar as $h2){

      $stpl->newBlock( "name_row" );
      $stpl->assign("MATCHACTIVE",$h2['active']);
      $stpl->assign("MATCHID",$h2['id']);
      $odds = '';

      foreach($bet_ar as $h){


        $stpl_odds = new TemplatePower(self::$tplRoot.self::$betType[$h['typ_id']][$h['podtyp_id']][3]);
        $stpl_odds->prepare();

        $stpl_odds->assign("ACTIVE",$h['match_active']);
        $stpl_odds->assign("STATUSO",$h['active']);
        $stpl_odds->assign("ID",$h['betradar_sazka_id']);

        $odds .= $stpl_odds->getOutputContent();
      }

      $stpl->assign("ODDS",$odds);

    }

    $stpl->gotoBlock( "_ROOT" );



    self::$lastStatusTime = time();

    $out = $stpl->getOutputContent();


    self::debug($out,"Write  client status");
    $out = mb_ereg_replace("[\t\n\r]+","\n",$out);
    fwrite(self::$sock,$out);
    self::debug(false,"Read  client status");

  }

 /**
 * Kdyz prijde status change
 * return void
 */
  public static function changeStatus(){

    self::debug(false,"Process status: change");

    foreach(self::$xml->Match as $match){



            try{

              $select = self::$dbGame->select()->from(array('a'=>'live_betradar_event'),array('a.event_id','l_sport_id'=>'u.sport_id','l.l_udalost_id','l.stav'))
                                              ->join(array('l' => 'live_event'),'a.event_id = l.event_id')
                                              ->join(array('u' => 'udalost'),'u.udalost_id = l.l_udalost_id')
                                              ->where("a.no_update=?",0)
                                              ->where("a.betradar_event_id=?",intval($match['matchid']));
              $stm  = $select->query();
              $row = $stm->fetchAll();

            }catch(Zend_Exception $e){

              self::Error("Database error: " . $e->getMessage()  . __LINE__."\n",'Live bet / error');

            }

            if(count($row) > 0){

                self::$dbGame->beginTransaction();

               if(isset($match['msgnr'])){



                try{

                    $n = self::$dbGame->update('live_betradar_event', array("msgnr"=>intval($match['msgnr'])),"betradar_event_id=".intval($match['matchid']));

                 }catch(Zend_Exception $e){

                    self::Error("Database error: " . $e->getMessage()  . __LINE__. "\n",'Live bet / error');

                    self::$dbGame->rollBack();
                    continue;

                 }

                }

                $status = 0;$xx = -1;
                foreach(BetRadarLiveBetting::$sport as $h){

                    if($h['id'] == $row[0]['l_sport_id']) {$status = $h['status'][strval($match['status'])];$xx =1;break;}

                }

                if($xx == -1) {BetRadarLiveBetting::Error("Nepodarilo se najit status zapasu: #".$match['matchid']."; status:".$match['status'],'Live bet / error');continue;}


                $data = array();
                $data['aktualizace'] =  It6_Date::dbNow();
                $data['stav'] =  $status;
                if(isset($match['matchtime']))  $data['minute'] = $match['matchtime'];

                try{

                    self::debug($row[0]['event_id'],"Write  match id");

                    $n = self::$dbGame->update('live_event', $data,"event_id=".$row[0]['event_id']);

                    if($status != LIVE_NOT_STARTED){

                        if(!self::combineBets($row[0]['event_id'])) {self::$dbGame->rollBack();continue;}

                    }
                    
                    if($status != LIVE_NOT_STARTED && !isset(self::$notice[$row[0]['event_id']])){

                        self::sendNotice($row[0]['event_id']);

                    }
                    

                }catch(Zend_Exception $e){

                    self::Error("Database error: " . $e->getMessage()  . __LINE__."\n",'Live bet / error');
                    self::$dbGame->rollBack();
                    continue;
                }

                list($hm,$aw) = explode(":",$match['score']);
                $data = array();


                $data['score_home'] =  $hm;
                $data['score_away'] =  $aw;


                if(isset($match['setscores']) && ($row[0]['l_sport_id'] == 1003 || $row[0]['l_sport_id'] == 1011) ){  //sety pro tenis | tretiny u hokeje

                    $set = explode("-",$match['setscores']);

                    foreach($set as $k=>$h){

                        list($hs,$as) = explode(":",$h);

                        if($row[0]['l_sport_id'] == 1003){
                          $data['set_'.($k+1).'_home'] =  intval($hs);
                          $data['set_'.($k+1).'_away'] =  intval($as);
                          $data['score_home'] =  intval($hs);
                          $data['score_away'] =  intval($as);
                        }
                        else if($row[0]['l_sport_id'] == 1011){
                          $data['tretina_'.($k+1).'_home'] =  intval($hs);
                          $data['tretina_'.($k+1).'_away'] =  intval($as);
                        }

                    }

                }
                else if($row[0]['l_sport_id'] == 1001){ //fotbal prvni a druhy polocas


                    $select = self::$dbGame->select()->from(array('a'=>'live_1001'),array('a.first_half_home','a.first_half_away'))
                                                     ->where("a.event_id=?",intval($row[0]['event_id']));
                    $stm  = $select->query();
                    $row8 = $stm->fetchAll();

                    if(count($row8) > 0){

                          if($row[0]['stav'] == LIVE_2_HALF){

                             $data['second_half_home'] = $data['score_home'] - $row8[0]['first_half_home'];
                             $data['second_half_away'] = $data['score_away'] - $row8[0]['first_half_away'];

                           }
                           else if($row[0]['stav'] == LIVE_1_HALF){

                             $data['first_half_home']  = $data['score_home'];
                             $data['first_half_away'] =  $data['score_away'];

                            }

                    }

                }

               if(isset($match['server']) && $row[0]['l_sport_id'] == 1003){

                  $select = self::$dbGame->select()->from(array('a'=>'live_'.$row[0]['l_sport_id']),array('a.first_service'))
                                                   ->where("a.event_id=?",$row[0]['event_id']);
                  $stm2  = $select->query();
                  $row2 = $stm2->fetchAll();

                  if(mb_strlen($row2[0]['first_service']) == 0)$data['first_service'] =  (intval($match['server']) == 1?"home":"away");

               }


                try{

                    $n = self::$dbGame->update('live_'.$row[0]['l_sport_id'], $data,"event_id=".$row[0]['event_id']);

                    self::$dbGame->commit();


                }catch(Zend_Exception $e){

                    self::Error("Database error: " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
                    self::$dbGame->rollBack();
                    continue;
                }

                foreach($match->Odds as $odds){

                    if(isset(BetRadarLiveBetting::$betTypeReverse[intval($odds['typeid'])])){
                      BetRadarLiveBet::addOdds(self::$dbGame,$odds,$row[0]['l_sport_id'],$row[0]['l_udalost_id'],$row[0]['event_id'],$match['betstatus']);
                    }

                }


            }



    }

    self::debug(false,"End Write  match");

  }
  
  /**
 * Posila pripominku
 * @param int $event_id id live sazky
 * return void
 */
  private function sendNotice($event_id){

  	     self::debug(false,"sendNotice()");
  	     
  	     $preklad = new Preklady();
     
	  
	    $sub = $body = $l = $sport = $udalost = array(); 
	 
  	
  	     try{

              $select = self::$dbGame->select()->from(array('a'=>'live_notice'),array('a.user_id','b.email','b.lang_id'))
                                              ->join(array('b'=>'uzivatel'),'b.user_id=a.user_id')
                                              ->where("a.event_id=?",$event_id);
              $stm  = $select->query();
              $row = $stm->fetchAll();

              $sub = $body = $l = $sport = $udalost = array();  
               
              foreach($row as $h){

     	
              	      $select = self::$dbGame->select()->from(array('a'=>'mail_template_log'),array('a.user_id'))
                                                      ->where("a.user_id=?",$h['user_id'])
                                                      ->where("a.mail_id=?",11)
              	                                      ->where("a.subtype=?",$event_id);
                      $stm  = $select->query();
                      $row2 = $stm->fetchAll();
                      
                      if(count($row2) > 0) continue;
                      
                      if(!isset($sub[$h['lang_id']])) {$sub[$h['lang_id']]  = $preklad->FindPreklad('mail_live_notice_sub',$h['lang_id']); $sub[$h['lang_id']]  = $sub [$h['lang_id']][$h['lang_id']];}	 
                      if(!isset($body[$h['lang_id']])) {$body[$h['lang_id']]  = $preklad->FindPreklad('mail_live_notice_b',$h['lang_id']); $body[$h['lang_id']]  = $body [$h['lang_id']][$h['lang_id']];}
      
                      $l[$h['lang_id']][] = $h['email'];
                       
                      
                      self::$dbGame->insert('mail_template_log',array('date'=>It6_Date::dbNow(),'user_id'=>$h['user_id'],'mail_id'=>11,'subtype'=>$event_id));
              }
     
              $select = self::$dbGame->query("select a.event_id,a.home_team,a.away_team,a.start_date,b.nazev as unazev,c.nazev as snazev from live_event a inner join udalost b on a.l_udalost_id=b.udalost_id inner join sport c on a.l_sport_id=c.sport_id  where a.event_id=".$event_id);
              $row2 = $select->fetchAll();
     
             foreach ($row2 as $h2){
     	
     	      $team = '<a href="#">'.$h2['home_team'].' - '.$h2['away_team'].'</a>'; 
     	      $team2 = $h2['home_team'].' - '.$h2['away_team'];
     	      $date = It6_Date::fromDb($h2['start_date']);
     	      $sp =  $h2['snazev'];
     	      $ud =  $h2['unazev'];
     	
             }
     
           // print_r($l);
           
             foreach($l as $k=>$h){
     	
              $email = array();

             $sp  = $preklad->FindPreklad($sp,$k); $sp  = $sp[$k];
             $ud  = $preklad->FindPreklad($ud,$k); $ud  = $ud[$k];
        
             $mail = new htmlMimeMail();
 	   
             $s = str_replace('{TEAM}',$team2,$sub[$k]);
       
             $b = str_replace('{TEAM}',$team,$body[$k]);
             $b = str_replace('{SPORT}',$sp,$b);
             $b = str_replace('{UDALOST}',$ud,$b);
             $b = str_replace('{DATE}',$date,$b);
       
             $mail->setTextCharset("UTF-8");
	         $mail->setHeadCharset("UTF-8");
	         $mail->setHTMLCharset("UTF-8");  $mail->html_charset = "UTF-8";$mail->text_encoding = "UTF-8";
	         $mail->setHTMLEncoding("base64");
	         $mail->setFrom(NOREPLY);
	         $mail->setReturnPath(NOREPLY);
             $mail->setHtml($b);
	         $mail->setSubject($s);
       
	         foreach($h as $h2){
	   	 
	   	      $email[] = $h2; 
	   	
	         }
	   
	         $mail->send($email);
	   
    
     

            }

            self::$notice[$event_id] = 1;
            
            }catch(Zend_Exception $e){

                echo "Database error: " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error';
                return false;

            }

  }
      /**
 *Zkombinuje sazky a odstrani jednoducha
 * @param int $event_id id live sazky
 * return void
 */
  private function combineBets($event_id){

           try{

              $select = self::$dbGame->select()->from(array('a'=>'live_sazka'),array('a.sazka_id'))
                                              ->where("a.close=?",0)
                                              ->where("a.event_id=?",$event_id);
              $stm  = $select->query();
              $row = $stm->fetchAll();

              $sazky = $sazky_all = array();
              foreach($row as $h){

                    self::$dbGame->delete('sazka_kombinace','sazka1_id='. $h['sazka_id'] .' or sazka2_id='.$h['sazka_id']);
                    $stm  = $select->query();

                    $data = array();
                    $data['jednoducha'] = 0;
                    self::$dbGame->update('sazky', $data,"sazka_id=".$h['sazka_id']);

                    $data = array();
                    $data['no_comb'] = 1;
                    self::$dbGame->update('live_sazka', $data,"sazka_id=".$h['sazka_id']);

                    $sazky[] = $h['sazka_id'];
                    $sazky_all[] = $h['sazka_id'];

              }

            }catch(Zend_Exception $e){

                echo "Database error: " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error';
              return false;

            }

            $x = 0;
            foreach($sazky_all as $h){


             foreach($sazky as $k=>$h2){

                if($h == $h2 || $h2 == 0) continue;

              try{

                    self::$dbGame->query('insert into sazka_kombinace(sazka1_id,sazka2_id,kombinace_show) values ('.$h.','.$h2.',0)');


               }catch(Zend_Exception $e){
                   echo "Database error: " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error';
                    return false;
               }

             }

             $sazky[$x] = 0;
             $x++;

            }

            return true;

  }

   /**
 * Kdyz prijde status alive
 * @param bool $first_check  prvni zapis casu do databaze
 * return void
 */
  public static function aliveStatus($first_check = false){

     $data = array(
             'last_alive_time'      => time()
     );

     try{

       $n = self::$dbGame->update('live_betradar_setting', $data,"setting_id=1");

     }catch(Zend_Exception $e){

       self::Error("Database error: " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');

     }

     if(!$first_check){


        foreach(self::$xml->Match as $match){

            $select = self::$dbGame->select()->from(array('a' => 'live_betradar_event'),array('a.event_id','l.sazka_id','l.l_sport_id'))
                                             ->join(array('l' => 'live'),'a.event_id = l.event_id')
                                             ->where("l.status=?",0)
                                             ->where("a.no_update=?",0)
                                             ->where("a.betradar_event_id=?",$match['matchid']);
            $stm  = $select->query();
            $row = $stm->fetchAll();

            $bets = array();$event_id = 0;$sport_id = 0;
            foreach($row as $h)  {$bets[] = $h['sazka_id'];$event_id = $h['event_id'];$sport_id = $h['l_sport_id'];}


            if($match['betstatus'] == 'stopped'){

               foreach($bets as $h){
                if(!BetRadarLiveBet::stopBet(self::$dbGame,$h)) BetRadarLiveBetting::Error("Nepodarilo se zastavit sazku: #".$h,'Live bet / error');

                $date =  It6_Date::dbNow();
                $data = array();
                $data['aktualizace'] =  $date;
                $data['aktualizace_sazka'] =  $date;

                try{

                 self::$dbGame->update('live_sazka', $data,"sazka_id=".intval($h['sazka_id']));

                }catch(Zend_Exception $e){

                    BetRadarLiveBetting::Error("Nepodarilo se updatovat live_sazky sazku id: ".$row[0]['sazka_id']." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
                    
                }
              
               }

            }

            if(isset($match['matchtime'])){
     		
     		    $data = array();
  	            $data['aktualizace'] =  It6_Date::dbNow();
                $data['minute'] = $match['matchtime'];
                
                try{
                   
                    	 
                	self::$dbGame->update('live_event', $data,"event_id=".$event_id);
  	          	   

                	
                }catch(Zend_Exception $e){
  	 	
  	 	            self::Error("Database error: " . $e->getMessage()  . __LINE__."\n",'Live bet / error');

  	            }
     		}
     		
            /*if($sport_id != 0){

              list($hm,$aw) = explode(":",$match['score']);
              $data = array(
               'score_home'      => intval($hm),
               'score_away'      => intval($aw)
              );

              $n = self::$dbGame->update('live_'.$sport_id, $data,"event_id=".$event_id);

            }*/
        }

     }


  }

  /**
 * Kontrola kdy naposledy prisel alive status a pripadne zavreni vsech sazek
 * return void
 */
  public static function checkAliveStatus(){



    self::debug('Checking Alive Status',"Start");

    try{

      $select = self::$dbGame->select()->from('live_betradar_setting',array('last_alive_time'))->where("setting_id=?",1);
      $stm    = $select->query();
      $row    = $stm->fetchAll();

    }catch(Zend_Exception $e){

                    self::Error("Database error: " . $e->getMessage()  . "; " .__LINE__ ."\n",'Live bet / error');

    }

    if(!isset($row[0]['last_alive_time'])){

        $acdt = time();

        try{

            self::$dbGame->insert('live_betradar_setting', array("last_alive_time"=>$acdt));

         }catch(Zend_Exception $e){

            self::Error("Database error: " . $e->getMessage()  . "; " .__LINE__ ." \n",'Live bet / error');

         }

         $row[0]['last_alive_time'] = $acdt;

    }
    
    $xmlMessageTime = intval(substr(self::$xml['timestamp'],0,-3));
    
    if(($row[0]['last_alive_time'] + self::$aliveTime) < time() || ($xmlMessageTime != 0 && $xmlMessageTime < (time()-20)) ){

        $rows    = BetRadarLiveBet::getRunBet(self::$dbGame);

        foreach($rows as $h){

            if(!BetRadarLiveBet::stopBet(self::$dbGame,$h['sazka_id'])) BetRadarLiveBetting::Error("Nepodarilo se zastavit sazku: #".$h['sazka_id'],'Live bet / error');

              $date =  It6_Date::dbNow();
              $data = array();
              $data['aktualizace'] =  $date;
              $data['aktualizace_sazka'] =  $date;

              try{

               self::$dbGame->update('live_sazka', $data,"sazka_id=".intval($h['sazka_id']));

              }catch(Zend_Exception $e){

                    BetRadarLiveBetting::Error("Nepodarilo se updatovat live_sazky sazku id: ".$row[0]['sazka_id']." ; " . $e->getMessage()  . "; " .__LINE__ . "\n",'Live bet / error');
                    
              }
              
        }


        self::debug('Checking Alive Status: closing bets: ',"End");
    }
     else
        self::debug('Checking Alive Status: No action: ',"End");

    self::$lastAliveTime = time();

  }


 /**
 * Tato metoda se snazi zalogovat
 * return bool
 */
  public static function login(){

    $stpl = new TemplatePower(self::$tplRoot.'bs_login.tpl');
    $stpl->prepare();

    $stpl->assign("BID",self::$bookId);
    $stpl->assign("KEY",self::$key);

    $out = $stpl->getOutputContent();
    $out = mb_ereg_replace("[\t\n\r]+","\n",$out);
    self::debug($out,"Write");
    fwrite(self::$sock,$out);
    $d = '';
    self::debug(false,"Read");

    $d =  fread(self::$sock,1024);

    self::debug($d);

    self::debug(false,"General parse login()");
    self::generalParse($d);


    if(self::$lastStatus != 'loginok'){BetRadarLiveBetting::Error("Login failed \n",'Live bet / error');exit;}

  }

 /**
 * Tato metoda se snazi zalogovat na testovaci server
 * @param int $testEnvNum  cislo serveru
 * return bool
 */
  public static function testServer($testEnvNum){

    $stpl = new TemplatePower(self::$tplRoot.'testServer.tpl');
    $stpl->prepare();

    $stpl->assign("NUM",$testEnvNum);

    $out = $stpl->getOutputContent();

    $out = mb_ereg_replace("[\t\n\r]+","\n",$out);

    self::debug($out,"Write");
    fwrite(self::$sock,$out);

  }


 /**
 * Debug
 * @param string $text  text ktery se posila/prijima
 * @param string $type  cteni/zapis
 * return bool
 */
  public static function debug($text=false,$type=""){

  	
  	if(self::$debug){

  	/*	echo "--time: ".It6_Date::dbNow()." Betradar:". date('Y-m-d H:i:s',intval(substr(self::$xml['timestamp'],0,-3))) ."\n";

        if($text == false)   echo $type." \n\n";
        else if($type == "") echo $text." \n\n";
        else                 echo $type." \n ".$text." \n\n";*/

$fp = fopen("/rest/log/logXML_". date('Y-m-d-H') .".log","a");

fwrite($fp,It6_Date::dbNow()." Betradar:".date('Y-m-d H:i:s',intval(substr(self::$xml['timestamp'],0,-3)))."\n".$text);
fclose($fp);
return;
        
        /*      try {


      	
      	$data = array(
                        'date'      => It6_Date::dbNow(),
                        'text'    =>  ($text != false?$text:$type)

                       );
                      self::$dbGame->insert('live_betradar_log', $data);


        }catch (Exception $e) {

                        BetRadarLiveBetting::Error("Nepodarilo se vlozit log  v databazi: ".$e->getMessage().__LINE__,'Live bet / error');

         }*/

      }

  }

 /**
 * Odesle chybu
 * @param string $text  chyba popis
 * return bool
 */
  public static function Error($text,$type){

     mail(self::$administrator,$type,$text);
     echo $text."\n";
  }

 /**
 * Nastavuje adminsitratory
 * @param string $admin
 * return void
 */
  public static function addAdminitrators($admin){

     self::$administrator = $admin;

  }

  public function __destruct(){




  }

}

?>
