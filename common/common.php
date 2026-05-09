<?php
require_once 'pear/DB.php';
//require_once 'DB.php';
require_once 'config.php';

error_reporting(E_ALL);

function __autoload($class_name) {
	$lib = $class_name;
	$files = explode("_",$class_name);

	if($files[0] == 'Zend'
		or $files[0] == 'Core'
		or $files[0] == 'Ntf'
		or $files[0] == 'Ntw'
		or $files[0] == 'It6'){
			$lib = implode('/', $files) . '.php';
			require_once $lib;
	}
	else {
		if(file_exists(INC_DIR."class/class.".$lib.".php"))
			require_once INC_DIR.'class/class.'.$lib . '.php';
		elseif(file_exists("../".INC_DIR."class/class.".$lib.".php"))
			require_once "../".INC_DIR.'class/class.'.$lib . '.php';
		else {
//    		fb::log($lib);
		require_once 'class/class.'.$lib . '.php';
		}
	}
}

/*
 funkce pro zachytavani systemovych chyb
*/

function myErrorHandler($errno, $errstr, $errfile, $errline)
{


 $zprava = "(".date("Y.m.d H:i:s").")";

 switch ($errno) {
  case E_USER_ERROR:
    $zprava .= " My ERROR [$errno] $errstr;";
    $zprava .= "  Fatal error in line $errline of file $errfile";
    $zprava .= ", PHP " . PHP_VERSION . " (" . PHP_OS . "); ";
    $zprava .= "Aborting...\n";
    break;
  case E_USER_WARNING:
    $zprava .= "My WARNING [$errno] $errstr line $errline file $errfile\n";
    break;
  case E_USER_NOTICE:
    $zprava .= "My NOTICE [$errno] $errstr line $errline file $errfile\n";
    break;
  case E_STRICT:
    $zprava = false;
    break;
  default:
    $zprava .= "Unkown error type: [$errno] $errstr line $errline file $errfile\n";
    break;
  }

  /*
   posle zpravu do error logu na serveru
 */

 if($zprava){
  $fname = ROOT."errorlog/sys.log";
  $fp = fopen($fname,"a");

  if($fp){
   fputs($fp,$zprava);
   fclose($fp);
//   chgrp($fname,WEBMINS_GROUP); // TODO: nezabira, groupa zustava stale www-data
  }


 //echo nl2br($zprava);

  /*
   kdyz jde o fatalni chybu posle se i na mail
  */
  if($errno == "E_USER_ERROR"){
    $headers  = "MIME-Version: 1.0\n";
    $headers .= "Content-type: text/plain; charset=iso-8859-1\n";
    $headers .= "X-Priority: 3\n";
    $headers .= "X-MSMail-Priority: Normal\n";
    $headers .= "X-Mailer: php\n";
    $headers .= "From: ".INFOMAIL." \n";

    mail(ADMINERRORMAIL,"System error ",$zprava,$headers);

    exit;

   }
 }
}

/*
 nastaveni funkce pro zachytavani sys. chyb
*/
//DEPRECATED: by It6_Log
//set_error_handler("myErrorHandler");


#Inicializace session handleru#

//IT6 should be set in bootstrap and using It6_Session class
//session_set_save_handler (
// array("Session", "open"),
// array("Session", "close"),
// array("Session", "read"),
// array("Session", "write"),
// array("Session", "destroy"),
// array("Session", "gc"));*/
