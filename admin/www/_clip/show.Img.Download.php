<?php
    /*
    * PRIDAT SESSION
    */
    include("shared/framework.php");

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
    if (!$authenticated)
        echo $stav.': Login first!';
    }
    else{
        /*
        * REGULARITA VSTUPU
        * pokud obsahuje / nebo .. 
        */
        $file = $_GET['docid'].$_GET['name'];
        if(strpos($file,'/') OR strpos($file,'..')){
          echo 'n/a';
        }
        else{    
            $fName = DOKUM_DIR.$file;
            include_once('shared/common.php');
            Help::OutputFile($fName,$file);
        }
    }
?>