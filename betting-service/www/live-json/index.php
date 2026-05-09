<?php
if(!defined("ROOT")) define("ROOT", dirname( dirname( dirname( dirname(__FILE__) ) ) ) . '/');

define('LIVE_CLIENT', 1);

header("Content-type:text/json; charset=UTF-8");

require_once ROOT . 'betting-service/application/bootstrap-json.php';

