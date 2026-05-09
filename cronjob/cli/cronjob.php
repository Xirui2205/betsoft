<?php

if(!defined("ROOT")) define("ROOT",dirname( dirname( dirname(__FILE__) ) ) . '/');
define('RUNNING_FROM_CLI', 1);

require_once(ROOT.'common/includes.inc.php');
include_once(ROOT.'common/init-global-cache.inc.php');

require_once ROOT.'cronjob/application/bootstrap.php';
