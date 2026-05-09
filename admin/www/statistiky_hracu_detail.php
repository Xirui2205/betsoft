<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
define('RUNNING_FROM_CLI', true);
define('ROOT', dirname(dirname(dirname(__FILE__))) . '/');

set_include_path (
		get_include_path() . PATH_SEPARATOR .
		ROOT . 'common/library/' . PATH_SEPARATOR .
		ROOT . 'betting-service/application/' . PATH_SEPARATOR .
		ROOT . 'betting-service/library/' . PATH_SEPARATOR .
		ROOT . 'admin/library/' . PATH_SEPARATOR
);

include(ROOT . 'common/config_util.inc.php');

$appEnv = getAppEnv();
if (empty($appEnv)) {
    echo "APPLICATION_ENVIRONMENT environment variable not set!\n";
    exit(1);
}

$test = in_array('test', $argv);

include(ROOT . 'common/config.php');
include(ROOT . 'common/includes.inc.php');

require_once 'Zend/Loader.php';
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Models_');
$autoloader->registerNamespace('It6_');
$autoloader->registerNamespace('Webservice_');
$autoloader->registerNamespace('Entities_');
$autoloader->registerNamespace('WarpTurn_');
$db = Zend_Controller_Plugin_DbPLugin::initDbConnection('db', Zend_Controller_Plugin_DbPLugin::CONFIG_MAIN);
$dbAdmin = Zend_Controller_Plugin_DbPLugin::initDbConnection('admindb', Zend_Controller_Plugin_DbPLugin::CONFIG_ADMIN);

// run from terminal
//APPLICATION_ENVIRONMENT=DEVEL_LOCAL php import_from_service.php
//APPLICATION_ENVIRONMENT=BBAS_PRODUCTION php statistiky_hracu.php
//APPLICATION_ENVIRONMENT=BBAS_TESTING php statistiky_hracu.php

$od = date("Y-m-d", strtotime("-2 months"));
$do = date("Y-m-d", time());

if ( isset($_POST["zobraz"]) ) {
	if ( isset($_POST["od"]) ) {
		$od = $_POST["od"];
	}

	if ( isset($_POST["do"]) ) {
		$do = $_POST["do"];
	}
}

/*$sql = "select u.user_id, u.jmeno,u.prijmeni, sum(castka) as saz_castka from ticket as t 
			INNER JOIN `vic_main`.`uzivatel` AS `u` ON t.user_id = u.user_id 
			INNER JOIN `vic_admin`.`branch` AS `b` ON u.branch_id = b.id 
			WHERE u.anonymous!=1 AND u.block=0 AND zalozen>='" . $od . "' AND zalozen<='" . $do . "' GROUP BY u.user_id  ORDER BY saz_castka";
*/
$sql = "select u.user_id, u.jmeno,u.prijmeni, sum(castka) as saz_castka from ticket as t 
			INNER JOIN `vic_main`.`uzivatel` AS `u` ON t.user_id = u.user_id 
			INNER JOIN `vic_admin`.`branch` AS `b` ON u.branch_id = b.id 
			WHERE u.anonymous!=1 AND u.block=0 AND zalozen>='" . $od . "' AND zalozen<='" . $do . "' GROUP BY u.user_id  ORDER BY saz_castka";

$usersTickets = $db->query($sql)->fetchAll();

$do100 = $do500 = $do1000 = $do5000 = $nad5000 = 0;
$aUsers = array();
foreach ($usersTickets as $ticket) {
	switch ($ticket["saz_castka"]) {
	    case ($ticket["saz_castka"] <= 100): 
	    	$do100++;
	    	$aUsers[100][]	 = $ticket;
	    	break;
	    case ($ticket["saz_castka"] > 100 && $ticket["saz_castka"] <= 500):
	    	$do500++;
	    	$aUsers[500][]	 = $ticket;
	    	break;
	    case ($ticket["saz_castka"] > 500 && $ticket["saz_castka"] <= 1000): 
	    	$do1000++;
	    	$aUsers[1000][]	 = $ticket;
	    	break;
	    case ($ticket["saz_castka"] > 1000 && $ticket["saz_castka"] <= 5000): 
	    	$do5000++;
	    	$aUsers[5000][]	 = $ticket;
	    	break;
	    case ($ticket["saz_castka"] > 5000):
	    	$nad5000++;
	    	$aUsers[nad5000][]	 = $ticket;
	    	break;
	}
}

?>

<form action="<?php echo $_SERVER["PHP_SELF"]?>" method="post" enctype="multipart/form-data">
	od: <input type="text" name="od" value="<?=$od?>" />
	do: <input type="text" name="do" value="<?=$do?>" />
	<input type="checkbox" name="detail" value="0" <?php if(isset($_POST["detail"])) { echo "checked"; } ?> />detail
	<input type="submit" name="zobraz" value="zobraz" />
</form>

<?php
if ( isset($_POST["detail"]) ) {
	echo "do 100 kc: " . $do100 . " hracu<br/>";
	foreach ($aUsers[100] as $usr) {
		echo '<div style="padding-left: 50px; font-size: 10px;">';
		echo $usr["user_id"]. " | " . $usr["saz_castka"] . " | " . $usr["jmeno"] . " " . $usr["prijmeni"] . "<br/>";
		echo '</div>';
	}
	echo "do 500 kc: " . $do500 . " hracu<br/>";
	foreach ($aUsers[500] as $usr) {
		echo '<div style="padding-left: 50px; font-size: 10px;">';
		echo $usr["user_id"]. " | " . $usr["saz_castka"] . " | " . $usr["jmeno"] . " " . $usr["prijmeni"] . "<br/>";
		echo '</div>';
	}
	echo "do 1000 kc: " . $do1000 . " hracu<br/>";
	foreach ($aUsers[1000] as $usr) {
		echo '<div style="padding-left: 50px; font-size: 10px;">';
		echo $usr["user_id"]. " | " . $usr["saz_castka"] . " | " . $usr["jmeno"] . " " . $usr["prijmeni"] . "<br/>";
		echo '</div>';
	}
	echo "do 5000 kc: " . $do5000 . " hracu<br/>";
	foreach ($aUsers[5000] as $usr) {
		echo '<div style="padding-left: 50px; font-size: 10px;">';
		echo $usr["user_id"]. " | " . $usr["saz_castka"] . " | " . $usr["jmeno"] . " " . $usr["prijmeni"] . "<br/>";
		echo '</div>';
	}
	echo "nad 5000 kc: " . $nad5000 . " hracu<br/>";
	foreach ($aUsers[nad5000] as $usr) {
		echo '<div style="padding-left: 50px; font-size: 10px;">';
		echo $usr["user_id"]. " | " . $usr["saz_castka"] . " | " . $usr["jmeno"] . " " . $usr["prijmeni"] . "<br/>";
		echo '</div>';
	}
} else {
	echo "do 100 kc: " . $do100 . " hracu<br/>";
	echo "do 500 kc: " . $do500 . " hracu<br/>";
	echo "do 1000 kc: " . $do1000 . " hracu<br/>";
	echo "do 500 kc: " . $do5000 . " hracu<br/>";
	echo "nad 5000 kc: " . $nad5000 . " hracu<br/>";
}