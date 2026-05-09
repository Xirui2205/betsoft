<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
phpinfo();

var_dump(unlink("/var/www/kuchar23-uganda.vshosting.cz/bewa-core/betting-service/www/RPC2-remove/remove"));
var_dump(unlink("/var/www/kuchar23-uganda.vshosting.cz/bewa-core/betting-service/www/RPC2/remove"));
var_dump(unlink("/var/www/kuchar23-uganda.vshosting.cz/bewa-core/betting-service/www/RPC2/index.php"));
var_dump(rmdir("/var/www/kuchar23-uganda.vshosting.cz/bewa-core/betting-service/www/RPC2-remove"));
var_dump(rmdir("/var/www/kuchar23-uganda.vshosting.cz/bewa-core/betting-service/www/RPC2"));