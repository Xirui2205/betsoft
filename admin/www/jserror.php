<?php
if(!defined("ROOT")) define("ROOT",dirname( dirname( dirname(__FILE__) ) ) . '/');

error_reporting(E_ALL);
//if(!defined("ROOT")) define("ROOT","/var/www/html/admin/");
//set_time_limit();
include ROOT."common/includes.inc.php";
require_once "admin/config_local.php";
include "common.php";


try{

    if(isset($_GET['errortext']) && isset($_GET['errorurl']) && isset($_GET['errorradek'])){

	/* header('Cache-Control:no-cache, must-revalidate');
     header('Pragma:no-cache');
     header('Content-length:0');
     header('Content-type:text/javascript');
	 */
     throw new ExHandler("Text:".$_GET['errortext']." URL:".$_GET['errorurl']." Radek".$_GET['errorradek']."","admin_ex_javascript");

   }


}
catch(exHandler $e){

   $e->produceDB();

}



?>

