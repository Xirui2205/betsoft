<?php
	header("Content-type:text/html; charset=UTF-8");
	if(!defined("ROOT")) define("ROOT",dirname( dirname( dirname(__FILE__) ) ) . '/');

	include(ROOT.'common/includes.inc.php');
	include ROOT . 'common/init-global-cache.inc.php';
	include '../config_local.php';
	include "common.php";
	
	Zend_Registry::set('db', It6_Controller_Plugin_SetController::connectDbWeb());
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="cs" xml:lang="cs"> 
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta http-equiv="Content-Language" content="cs" />
 <meta name="keywords" content="" />
 <meta name="description" content=" " />
 <meta name="abstract" content="" />
 <meta name="robots" content='index,follow' /> 
 <meta name="googlebot" content='index,follow,snippet,archive' /> 
 <meta name="Author" content="" />
 <!-- CACHE - MSIE /--> 
 <meta http-equiv='Cache-Control' content='must-revalidate, post-check=0, pre-check=0' /> 
 <meta http-equiv='Pragma' content='public' /> 
 <!-- CACHE - MSIE end /--> 
 <!-- CACHE - other browsers /--> 
 <meta http-equiv='Cache-Control' content='no-cache' /> 
 <meta http-equiv='Pragma' content='no-cache' /> 
 <meta http-equiv='Expires' content='-1' /> 
 <!-- CACHE - other browsers end /--> 
 <!-- BROWSER SPECIFIC FEATURES = ALL OFF /--> 
 <!-- MSIE - 'helpful' features /--> 
 <meta http-equiv='MSThemeCompatible' content='no' /> 
 <meta name='MSSmartTagsPreventParsing' content='TRUE' /> 
 <!-- OPERA - image resizing /--> 
 <meta name='autosize' content='off' /> 
 <!-- BROWSER SPECIFIC FEATURES = end /--> 
	<title> KURZY DETAIL </title>
 <link rel='shortcut icon' type='image/x-icon' href='' /> 
 <link rel="stylesheet" href="/css/universal.css?r=<?= RELEASE_REV ?>" media="screen" type="text/css" />
 <link rel="stylesheet" href="/css/print.css?r=<?= RELEASE_REV ?>" media="print" type="text/css" />
 <script src="<?= JQUERY_URI ?>" type="text/javascript" language="javascript"></script>
 <script src="/js/mainscript.js?r=<?= RELEASE_REV ?>" type="text/javascript" language="javascript"></script>
 <script language="javascript" type="text/javascript" src="/js/tinymce/jscripts/tiny_mce/tiny_mce.js?r=<?= RELEASE_REV ?>"></script>
 <style type="text/css">
 <!--
 
  body{background:white;padding:8px;}
 
 -->
 </style>
</head>
<body>

<?php
set_time_limit(908);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

$dbGameZend = It6_Controller_Plugin_SetController::connectDbWeb();
It6_Controller_Plugin_SetController::connectDbAdmin();

$admindb = Zend_Registry::get('zdb_admin');

$translate = new It6_Translate_Admin(1, $dbGameZend);
Zend_Registry::set('translate', $translate);

try{
   
   $ob = new SazkaInfo();
   $ob->runAction();
   echo $ob->GetContent();
   
}
catch(exHandler $e){

   $e->produceAllError();

}

?>


</body>
</html>
