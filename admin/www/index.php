<?php
if(!defined("ROOT")) define("ROOT",dirname( dirname( dirname(__FILE__) ) ) . '/');
header("Content-type:text/html; charset=UTF-8");
#######Tohle se pak vyhodi jen pro vyvoj##################
//$polepass=Array("it6"=>"compbet");

/*
if (!IsSet($_SERVER['PHP_AUTH_USER']))
	{
	     Header("HTTP/1.0 401 Unauthorized");
	     Header("WWW-Authenticate: Basic realm='Basic'");
	     echo "Nonauthorize access";
	     exit;
	}
	else
	{
	  while(list($jmeno,$heslo)=Each($polepass)){
	   if($_SERVER['PHP_AUTH_USER']!=$jmeno || $_SERVER['PHP_AUTH_PW']!=$heslo){
		 	    //Header("HTTP/1.0 401 Unauthorized");
		    	//Header("WWW-Authenticate: Basic realm=''");
			    echo "bad";
				exit;}
	    }
	}
*/
/*
$auth = false;
if (isset($_SERVER['PHP_AUTH_USER'])) {
	while (list($jmeno,$heslo) = each($polepass)) {
		if ($_SERVER['PHP_AUTH_USER'] == $jmeno && $_SERVER['PHP_AUTH_PW'] == $heslo) {
			$auth = true;
			break;
		}
	}
}
if (!$auth) {
	header('HTTP/1.0 401 Unauthorized');
	header('WWW-Authenticate: Basic realm="Basic"');
	echo 'Nonauthorize access';
	exit;
}

*/
#########################################################
set_time_limit(0);
//error_reporting(E_ALL);
//if(!defined("ROOT")) define("ROOT","/var/www/html/admin/");
//set_time_limit();

require_once(ROOT.'common/includes.inc.php');
include_once(ROOT.'common/init-global-cache.inc.php');

require_once(ROOT.'admin/config_local.php');
//include "include_path.php";
include "common.php";
include "template/class.TemplatePower.inc.php";
require_once ROOT.'admin/application/bootstrap.php';
