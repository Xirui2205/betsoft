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
//APPLICATION_ENVIRONMENT=BBAS_TESTING php import_from_service.php
//APPLICATION_ENVIRONMENT=BBAS_PRODUCTION php import_from_service.php

$newParameters = array(1 =>array("name" => "EMAIL", "data" => 15),
					   2 =>array("name" => "EMAIL_PSWD", "data" => 16),
 					   3 =>array("name" => "EMAIL_SMTP_USER", "data" => 15),
 					   4 =>array("name" => "EMAIL_SMTP_PSWD", "data" => 16)
 					  );

// check if exist for new parameters
foreach ($newParameters as $key => $value) {
	$checkquery = $dbAdmin->select()
	   ->from("parameter")
	   ->where("name = ?", $newParameters[$key]["name"]);
	$checkrequest = $dbAdmin->fetchRow($checkquery);
	// if not exist then insert otherwise update
	if ( count($checkrequest) > 1 ) {
		$newParameters[$key]["id"] = $checkrequest["id"];
	} else {
		echo "Parameter (" . $newParameters[$key]["name"] . ") added to table parameter \n";
		$sql = "INSERT INTO `parameter` (`id`, `name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`, `is_editable`, `description`, `manually_inserted`)
			 VALUES (NULL, '" . $newParameters[$key]['name'] . "', '', '0', '1', '0', NULL, '1', '', '1', '', '0')";
		$dbAdmin->query($sql);
		$newParameters[$key]["id"] = $dbAdmin->lastInsertId();
	}
}

$total = $notFound = $incorrect = $notEqual = $imported = $updated = $inserted = 0;
$row = 1;
if (($handle = fopen(ROOT."/db/servis/passwd", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ":")) !== FALSE) {        
        $row++;
        $total++;

        $data[15] = $data[0]."@compbet.com";
        echo "EMAIL:".$data[15]."\n";

        $data[16] = str_replace("{plain}","",$data[1]);
        echo "PASS:".$data[16]."\n";

        preg_match_all('!\d+!', $data[0], $matches);
		if ( isset($matches[0][0]) ) {
			$data[1] = $matches[0][0];
		}
		echo "ID:".$data[1]."\n";
		echo "---------------\n";

        /*
        [1]  - pob_cislo (handle)
		[15] - vic_email
		[16] - vic_email_pas
		*/

		$checkquery = $dbAdmin->select()
		   ->from("branch", array("num"=>"COUNT(*)"))
		   ->where("handle = ?", $data[1]);
		$checkrequest = $dbAdmin->fetchRow($checkquery);
		
		if ( $checkrequest["num"] > 0 ) {
			preg_match_all('!\d+!', $data[15], $matches);
			if ( isset($matches[0][0]) ) { // check email ID
				if ( $data[1] == $matches[0][0] ) { // branch handle = email handle
					$imported++;
					// get branch ID
					$branchId = Webservice_Branch::getIdByHandle($data[1]);
					// check all new parameters
					foreach ($newParameters as $key => $value) {
						if ( isset($newParameters[$key]["data"]) ) { // only records where the data is entered
							// control parameter if there are in the table branch_has_parameter
							$checkquery = $dbAdmin->select()
							   ->from("branch_has_parameter")
							   ->where("branch_id = ?", $branchId)
							   ->where("parameter_id = ?", $newParameters[$key]["id"]);
							$checkrequest = $dbAdmin->fetchRow($checkquery);
							// if found in the table branch_has_parameter then update otherwise insert
							

							if ( count($checkrequest) > 1 ) {
								//$sql = "UPDATE `branch_has_parameter` SET `value` = '" . $data[$newParameters[$key]['data']] ."' 
								//	WHERE `branch_has_parameter`.`branch_id` =" . $branchId . " AND `branch_has_parameter`.`parameter_id` =" . $newParameters[$key]["id"];

								It6_DbTransaction::begin($dbAdmin);

								try {
									$dbAdmin->update(
										'branch_has_parameter',
										array('value' => $data[$newParameters[$key]['data']]),
										array('branch_id=?' => $branchId,
											  'parameter_id=?' => $newParameters[$key]["id"])
									);
									//It6_DbTransaction::commit($dbAdmin->query($sql));
									It6_DbTransaction::commit($dbAdmin);
									It6_Log::info("UPDATE OK - handle: " . $data[1] . " | vic_email: " . $data[15] . " | vic_email_pas: " . $data[16] . "\n",It6_Log::TAG_ADMIN_OPERATION);
									$updated++;
								}
								catch ( Exception $e ) {
									It6_DbTransaction::rollback($dbAdmin);
									throw $e;
								}

							} else {
								//$sql = "INSERT INTO `branch_has_parameter` (`branch_id`, `parameter_id`, `value`)
								//	 VALUES ('$branchId', '" . $newParameters[$key]['id'] . "', '" . $data[$newParameters[$key]["data"]] . "')";

								It6_DbTransaction::begin($dbAdmin);

								try {
									$dbAdmin->insert('branch_has_parameter', array(
										'branch_id' => $branchId,
										'parameter_id' => $newParameters[$key]['id'],
										'value' => $data[$newParameters[$key]["data"]]
									));
									//It6_DbTransaction::commit($dbAdmin->query($sql));
									It6_DbTransaction::commit($dbAdmin);
									It6_Log::info("IMPORT OK - handle: " . $data[1] . " | vic_email: " . $data[15] . " | vic_email_pas: " . $data[16] . "\n",It6_Log::TAG_ADMIN_OPERATION);
									$inserted++;
								}
								catch ( Exception $e ) {
									It6_DbTransaction::rollback($dbAdmin);
									throw $e;
								}
							}

						}
					}
				} else {
					$notEqual++;
					It6_Log::info("ERROR branch HANDLE is not equal to email ID - handle: " . $data[1] . " | vic_email: " . $data[15] . " | vic_email_pas: " . $data[16] . "\n",It6_Log::TAG_ADMIN_OPERATION);
				}
			} else {
				$incorrect++;
				It6_Log::info("ERROR incorrect email - handle: " . $data[1] . " | vic_email: " . $data[15] . " | vic_email_pas: " . $data[16] . "\n",It6_Log::TAG_ADMIN_OPERATION);
			}
		} else {
			$notFound++;
			It6_Log::info("ERROR branch is not found in database - handle: " . $data[1] . " | vic_email: " . $data[15] . " | vic_email_pas: " . $data[16] . "\n",It6_Log::TAG_ADMIN_OPERATION);
		}
    }

    echo "\ntotal: " . $total . "\n";
    echo "not found in database: " . $notFound . "\n";
    echo "branch HANDLE is not equal to email ID: " . $notEqual . "\n";
    echo "incorrect email: " . $incorrect . "\n";
    echo "succesfully imported: " . $imported . " ( inserted: " . $inserted/2 . " / updated: " . $updated/2 . " )\n";

    fclose($handle);
}