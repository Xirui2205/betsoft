<?php
if(!defined("ROOT")) define("ROOT",dirname( dirname( dirname(__FILE__) ) ) . '/');

include(ROOT.'common/includes.inc.php');
require_once 'XML/RPC.php';
if(file_exists('config_local.php')) require_once 'config_local.php';


$fault = "";

$img_name = "ticket.jpg"; //jmeno obrazku na serveru
$obr = $_SERVER["DOCUMENT_ROOT"]."/ticket.jpg";  //cesta k obrazku

$params = array(new XML_RPC_Value(XML_RPC_NAME),new XML_RPC_Value(XML_RPC_PASS));
$msg = new XML_RPC_Message('SetConnect', $params);

$cli = new XML_RPC_Client(XML_RPC_PATH,XML_RPC_HOST,XML_RPC_PORT);
$cli->setDebug(0);
$resp = $cli->send($msg);
//print_r($cli);
if (!$resp) {
   $fault .= 'Communication error1: ' . $cli->errstr;
   
}

if (strlen($fault)<1 && !$resp->faultCode()) {
    
	$session = XML_rpc_decode($resp->value());
	
    $fp = fopen($obr,"rb");
    $data = base64_encode(fread($fp,filesize($obr)));
    fclose($fp);
	
	$params = array(new XML_RPC_Value($session),new XML_RPC_Value($img_name),new XML_RPC_Value($data));
    $msg = new XML_RPC_Message('SetImage', $params);

	$resp = $cli->send($msg);

    if (!$resp) {
      $fault .= 'Communication error2: ' . $cli->errstr;
    }

	if (strlen($fault)<1 && !$resp->faultCode()) ;
	else{
     if(strlen($fault)<1) $fault .= $resp->faultString();
	}
	
} else {
   if(strlen($fault)<1) $fault .= $resp->faultString().'cccc';
}

if(isset($session)){

 #Ukonceni na serveru#
 $params = array(new XML_RPC_Value($session));
 $msg = new XML_RPC_Message('Unload',$params);
 $resp = $cli->send($msg);
  if (!$resp) {
       $fault .= 'Communication error3: ' . $cli->errstr;
  }

 if (strlen($fault)<1 && !$resp->faultCode()) ;
 else {
  if(strlen($fault)<1)$fault .= $resp->faultString();
 }

}
 
if(strlen($fault) < 1) echo "ok";
else echo $fault;

?>



