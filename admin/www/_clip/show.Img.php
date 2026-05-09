<?php
    /*
    * PRIDAT SESSION
    */
    include("shared/common.php");

    /*
    * DB PRIPOJENI GAME
    * Nutne!
    */
    $db = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
    if (DB::isError($db)){throw new ExHandler($db->getMessage());}
    $db ->setFetchMode(DB_FETCHMODE_ASSOC); // asociativni pole
    $db ->query("SET NAMES 'utf8'");

    /*
    * DB PRIPOJENI SESION
    */
    $dbSES = DB::connect(SESDATABASE ."://". SESMY_USER .":". SESMY_PASS ."@". SESMY_HOST ."/". SESMY_DB);
    if (DB::isError($dbSES)){throw new ExHandler($dbSES->getMessage());}
    $dbSES ->setFetchMode(DB_FETCHMODE_ASSOC); // asociativni pole
    $dbSES ->query("SET NAMES 'utf8'");

    /*
    * DB PRIPOJENI ADMIN
    * Pripojeni k databazi
    */
    $dbADM = DB::connect(DATABASE ."://". MY_USER .":". MY_PASS ."@". MY_HOST ."/". MY_DB);
    if (DB::isError($dbADM)){throw new ExHandler($dbADM->getMessage());}
    $dbADM ->setFetchMode(DB_FETCHMODE_ASSOC); // asociativni pole
    $dbADM ->query("SET NAMES 'utf8'");

    /*
    * SESSION
    */
    //if(!SesClass::open($dbADM)) throw new ExHandler('Nepodarilo se inicializovat Session',"admin_ex_db");
    if (!It6_Session_Admin::start($dbADM)) throw new ExHandler('Nepodarilo se inicializovat Session',"admin_ex_db");

    /*
    * JE UZIVATEL ONLINE
    * 0 neprihlaseny,
    * 1 odhlaseny
    * 2 prihlseny
    * 4 prave odhlaseny
    */
    //$stav = SesClass::getUserStatus();
    //SesClass::close();
    $authenticated = It6_Session_Admin::isAuthenticated();
    It6_Session_Admin::end();

    //if($stav != 2){
    //    echo $stav.': Login first!';
    if(!$authenticated){
        echo $stav.': Login first!';
    }
    else{
        /*
        * REGULARITA VSTUPU
        * pokud obsahuje / nebo ..
        */
        $file = $_GET['docid'].$_GET['name'];
        if(strpos($file,'/') OR strpos($file,'\\')){
          echo 'n/a';
        }
        else{
            $fName = DOKUM_DIR.$file;
            if(is_file($fName)){
              $imgInfo = getimagesize($fName);
              header('Content-Type: '.$imgInfo['mime']);
              header('Content-Length: '.filesize($fName));
              $fp = fopen($fName,'rb');
              fpassthru($fp);
              fclose($fp);
            }
        }
    }