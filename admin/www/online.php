<?php
//if(!defined("ROOT")) define("ROOT",dirname( dirname( dirname(__FILE__) ) ) . '/');
//include(ROOT.'common/includes.inc.php');

//include 'config_local.php';
//include 'common.php';

	if(!defined("ROOT")) define("ROOT",dirname( dirname( dirname(__FILE__) ) ) . '/');
	//include(ROOT.'common/includes.inc.php');

	include(ROOT.'common/includes.inc.php');
	include '../config_local.php';
	include "common.php";



header("Content-type:text/html; charset=UTF-8");
try{

 $ob = new OnlineBook();
 $ob->ShowInfo();

}catch(exHandler $e){

 $e->produceAllError();

}
?>
