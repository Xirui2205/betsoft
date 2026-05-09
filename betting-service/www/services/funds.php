<?php
set_time_limit(120);

if(!defined("ROOT")) define("ROOT", dirname( dirname( dirname( dirname(__FILE__) ) ) ) . '/');

define('LIVE_CLIENT', 1);

set_include_path (
		get_include_path() . PATH_SEPARATOR .
		ROOT . 'common/library/' . PATH_SEPARATOR .
		ROOT . 'betting-service/application/' . PATH_SEPARATOR .
		ROOT . 'betting-service/library/' . PATH_SEPARATOR .
		ROOT . 'admin/library/' . PATH_SEPARATOR
);

header("Content-type:text/json; charset=UTF-8");

require_once(ROOT.'common/includes.inc.php');
include_once(ROOT.'common/init-global-cache.inc.php');

//require_once(ROOT.'betting-service/config_local.php');
require_once(ROOT.'admin/config_local.php');

require_once ROOT.'common/config.php';
require_once 'Zend/Loader.php';
require_once 'Zend/Loader/Autoloader.php';
//error_reporting(0);
//ini_set('display_errors', 0);
//TODO this should be loaded dynakmicli by user profile
//TODO this is not acceptable way how set locale (who can read let's see PHP manual)
//setlocale(LC_ALL,'cs_CZ.utf8');
//date_default_timezone_set('Europe/Prague');

    $autoloader = Zend_Loader_Autoloader::getInstance();
Zend_Registry::set('autoloader', $autoloader);
$autoloader->registerNamespace('Webservice_');
$autoloader->registerNamespace('Entities_');
$autoloader->registerNamespace('Models_');
$autoloader->registerNamespace('It6_');
$autoloader->pushAutoloader(new It6_Autoloader());

//TODO: use user's language or language from session
Zend_Registry::set('Zend_Locale', new Zend_Locale('cs_CZ'));
date_default_timezone_set('Europe/Prague');

include_once ROOT . 'common/init-global-cache.inc.php';

//It6_Log::initialize();

$db = Zend_Controller_Plugin_DbPLugin::initDbConnection('db', Zend_Controller_Plugin_DbPLugin::CONFIG_MAIN, true);
$admindb = Zend_Controller_Plugin_DbPLugin::initDbConnection('admindb', Zend_Controller_Plugin_DbPLugin::CONFIG_ADMIN, false);
$dbSes = Zend_Controller_Plugin_DbPLugin::initDbConnection('dbSes', Zend_Controller_Plugin_DbPLugin::CONFIG_SESSION, false);

Zend_Registry::set('translate', new It6_Translate_Ws());

$client = new Zend_XmlRpc_Client(WEB_SERVICE_URL);
Zend_Registry::set('ws', new It6_WS(WS_WRAPPER, $client));

class SoapException extends Zend_Exception{}

// Instantiate server, etc.
class Funds {
    /**
     * Send donation or force withdraw from host ...
     *
     * @param array $host
     * @return array
     */
public function fundHost($hosts) {
        
        $ret = array();
        $db = Zend_Registry::get('db');
        $hosts = It6_ArrayWrapper::toNativeArray($hosts);
        //return $hosts;
        foreach($hosts as $host){
            if (empty($host["amount"]) || !is_float((float)$host["amount"]))
                throw new SoapException('Invalid param $amount');            
            elseif (!isset($host["transactionIspId"]))
                throw new SoapException('Invalid param $transactionIspId');
            elseif (!isset($host["hostId"]))
                throw new SoapException('Invalid param $hostId');
              

            //zabraneni znovu vytvoreni stejne dotace/odvodu
            $select = $db->select()
                        ->from('fund_transactions')
                        ->where('isp_id = ?', $host["transactionIspId"]);
            $res = $select->query()->fetchAll();

            if (count($res) > 0)
                continue;


            $params = array(
                    "value" => $host["amount"],
                    "typeName" => ($host["amount"] > 0) ? "branch.deposit" : "branch.withdraw",
                    "currencyId" => 8,//CZK             
                    "hostId" => $host["hostId"],
                    "createAdminId" => 223, 
                    "confirmAdminId" => 223 
                );

            $ws = Zend_Registry::get('ws');

            try {
                $transactionId = $ws->Transaction->make($params);
                if ($host["amount"] > 0)
                     $ws->Transaction->confirm($transactionId);

                $ret[] = array(
                        "transactionId" => $transactionId,
                        "hostId" => $host["hostId"],
                        "transactionIspId" => $host["transactionIspId"]
                    );

                                //zabraneni znovu vytvoreni stejne dotace/odvodu
                $db->insert("fund_transactions", array(
                        "isp_id" => $host["transactionIspId"],
                        "bewa_id" => $transactionId
                    ));
            }
            catch (Exception $e) {
                throw new SoapException($e->getMessage());
            }
        }

        return $ret;
    }

    /**
    * Return in and out for host(s)
    *
    *@param integer hostId  
    *@return array
    */

    public function getInOut($hostId = null){
        
        $ws = Zend_Registry::get('ws');

        if (isset($hostId)) {

            return $ws->Host->getInOut($hostId);
        }
        else {

            $ret = array();

            $hosts = $ws->Host->getAll();

            foreach ($hosts as $host) {
                $inOut = $ws->Host->getInOut($host->hostId);

                 $ret[$host->hostId] = $inOut;
            }

            return It6_ArrayWrapper::toNativeArray($ret);

        }
    }

    public function getBalancing($hostId = null, $dateFrom, $dateTo, $datesForCalculation = true) {

         $ws = Zend_Registry::get('ws');

        if (isset($hostId)) {

            return It6_ArrayWrapper::toNativeArray($ws->Host->getBalancing($hostId, $dateFrom, $dateTo, null, null, false, $datesForCalculation));
        }
        else {

            $ret = array();

            $hosts = $ws->Host->getAll();

            foreach ($hosts as $host) {

                $balancing = $ws->Host->getBalancing($host->hostId, $dateFrom, $dateTo, null, null, false, $datesForCalculation);

                $ret[$host->hostId] = $balancing;
            }
            return $ret;
            //return It6_ArrayWrapper::toNativeArray($ret);

        }

    }

    public function getBranchLiveBalancing($branchId, $dateFrom, $dateTo){
        $ws = Zend_Registry::get('ws');

        return It6_ArrayWrapper::toNativeArray($ws->Host->getLiveBalancing($branchId, $dateFrom, $dateTo));
    }

    public function getBranchInternetBalancing ($branchId, $dateFrom, $dateTo, $datesForCalculation = true){
        $ws = Zend_Registry::get('ws');

        return It6_ArrayWrapper::toNativeArray($ws->Host->getBalancing(null, $dateFrom, $dateTo, $branchId, null, true, $datesForCalculation));
    }

 /**
    * Return branch calculation by branchId
    *
    * @param integer branchId
    * @param string dateFrom
    * @param string dateTo
    * @return array
    */

    public function getCalculation($branchId, $dateFrom, $dateTo, $datesForCalculation = true) {
        $ws = Zend_Registry::get('ws');

        $ret = array();

        $hosts = $ws->Host->getAllWhere(array('branch_id = ?' => $branchId));

        foreach ($hosts as $host) {
            $ret["hosts"][$host->hostId] = $this->getBalancing($host->hostId, $dateFrom, $dateTo, $datesForCalculation);
        }
        $ret["internet"] = $this->getBranchInternetBalancing($branchId, $dateFrom, $dateTo, $datesForCalculation);
        $ret["live"] = $this->getBranchLiveBalancing($branchId, $dateFrom, $dateTo);
        
        return It6_ArrayWrapper::toNativeArray($ret);
    }


    /**
    * Return branch calculation
    *
    * @param integer branchId
    * @param string dateFrom
    * @param string dateTo
    * @return array
    */
    public function getCalculation2($branchId = null, $dateFrom = null, $dateTo = null, $datesForCalculation = true) {

        if (!empty($branchId)) {
            $branchCalculation = $this->getCalculation($branchId, $dateFrom, $dateTo, $datesForCalculation);
            return array($branchId => $branchCalculation);
        }
        else {
            $ws = Zend_Registry::get('ws');
            $branches = $ws->Branch->getAll();

            $ret = array();

            foreach($branches as $branch) {

                if ($branch->isActive)
                    $ret[$branch->branchId] = $this->getCalculation($branch->branchId, $dateFrom, $dateTo, $datesForCalculation);
            }

            return $ret;
        }

    }

    /**
     * get branches or branch by id
     * @return array
     */

    public function getBranches($branchId = null) {
        $ws = Zend_Registry::get('ws');

        $branches = array();

        if (!(is_null($branchId)))
            $branches[] = $ws->Branch->getById($branchId);
        else
            $branches = $ws->Branch->getAll();

        $branchesArray = It6_ArrayWrapper::toNativeArray($branches);
        foreach($branchesArray as &$branch) {
            $branchAccounts = $ws->BankAccount->getByBranch($branch['branchId']);
            $branch['bankAccounts'] = It6_ArrayWrapper::toNativeArray($branchAccounts);
            
            $branchEmployees = $ws->Branch->getEmployees($branch['branchId']);
            $branch['employees'] = It6_ArrayWrapper::toNativeArray($branchEmployees);
            
            /*$branchPlayers = Webservice_User::getByBranchId($branch['branchId']);
            $branch['players'] = It6_ArrayWrapper::toNativeArray($branchPlayers);*/
            
            $branchHosts = $ws->Host->getByBranchId($branch['branchId']);
            $branch['hosts'] = It6_ArrayWrapper::toNativeArray($branchHosts);
            
        }

        return $branchesArray;
    }

    /**
    * Return all branch ids
    * @return array
    */

    public function getBranchIds() {
        $ws = Zend_Registry::get('ws');

        $ret = array();

        $branches = $ws->Branch->getAllWhere(array('is_active = ?' => 1));

        foreach ($branches as $branch) {
            $ret[]= $branch->branchId;
        }

        return $ret;
    }

        /**
    * Return all host ids
    * @param integer branchId
    * @return array
    */

    public function getHostIds($branchId) {
        if (!empty($branchId)) {
            $ws = Zend_Registry::get('ws');

            $ret = array();

            $hosts = $ws->Host->getAllWhere(array('branch_id = ?' => $branchId));

            foreach ($hosts as $host) {
                $ret[]= $host->hostId;
            }

            return $ret;
        }
        else return array();
    }

     /**
    * Return branch Id by its handle
    * @param integer branchHandle
    * @return integer
    */

    public function getIdByHandle($branchHandle) {

        return Zend_Registry::get('ws')->Branch->getIdByHandle($branchHandle);
    }


    /**
     * get transaction status 
     * @param array transaction 
     * @return array
     */

    public function getTransactionStatus($transactionIspId){
        if (!is_array($transactionIspId))
            $transactionIspId = array($transactionIspId);

        $db = Zend_Registry::get("db");

        $select = $db->select()
                        ->from(array('f' => 'fund_transactions'), array(
                                'f.isp_id',
                                'tt.name',
                                't.status',
                                't.okTime'
                            ))                        
                        ->join(array('t' => 'financial_transaction'),'f.bewa_id = t.transaction_id', null)
                        ->join(array('tt' => 'financial_transaction_type'), 'tt.id = t.type_id', null)
                        ->where('isp_id in (?)', $transactionIspId);
        $res = $select->query()->fetchAll();

        $ret = array();

        foreach ($res as $row) {
            
                $ret[$row["isp_id"]] = array(
                        "acceptedByBranch" => ($row["status"] == Webservice_Transaction::STATUS_OK) ? 1 : 0,
                        "okTime" => It6_Date::fromDb($row["okTime"])
                    );
        }

        return $ret;
    }
}

// pointing to the current file here
$server = new Zend_Json_Server("https://svc-compbet.com/services/funds.php");
$server->setClass('Funds');

if ("GET" == $_SERVER["REQUEST_METHOD"]) {
    // Indicate the URL endpoint, and the JSON-RPC version used:
    $server->setTarget('/services/funds.php')
           ->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);
 
    // Grab the SMD
    $smd = $server->getServiceMap();
 
    // Return the SMD to the client
    header('Content-Type: application/json');
    echo $smd;
    return;
}
 
$server->handle();
