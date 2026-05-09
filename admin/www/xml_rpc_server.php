<?php
if(!defined("ROOT")) define("ROOT",dirname( dirname( dirname(__FILE__) ) ) . '/');

require_once 'XML/RPC/Server.php';
if(file_exists('config_local.php')) require_once 'config_local.php';
//TODO: WTF?
require_once "configAdmin.php";

 /**
 * Odeslani souboru na admin server
 * @param string $key klic k porovnani
 * @param string $file_name jmeno souboru
 * @param string $file_path cesta k souboru
 * return string
 */
function UploadDocument($params){

     $status = 1;

     $key =  XML_rpc_decode($params->getParam(0));

     if(RC4CRYPTKEY != $key) {$status = 2;$errstring = "Key does not match";}
     
     $im_name =  XML_rpc_decode($params->getParam(1));
     $data =  base64_decode(XML_rpc_decode($params->getParam(2)));

     if(file_exists(TMP_ROOT.$im_name))  {$status = 3;$errstring = "Image allready exists";}
     else{
  
       $fp = fopen(TMP_ROOT.$im_name,'wb');
       if(!$fp) {$status = 2;$errstring = "Folder permission denied ";}else{

       if(fputs($fp,$data)){$val = new XML_RPC_Value("ok");}else {$status = 2;$errstring = "Image was not upload ";}
       fclose($fp);
       
       $fp = fopen(TMP_ROOT_SOURCE.$im_name,'wb');
       if(!$fp) {$status = 2;$errstring = "Folder permission denied ";}else{

       if(fputs($fp,$data)){$val = new XML_RPC_Value("ok");}else {$status = 2;$errstring = "Image was not upload ";}
       fclose($fp);

       }

     }
     }
     if($status == 1){
         
 return new XML_RPC_Response($val);
      }
     else{
      return new XML_RPC_Response (0, $status , $errstring);
       }
}



$server = new XML_RPC_Server(
    array(
        'UploadDoc' =>
            array(
                'function' => 'UploadDocument'
            )
    )
);
?>
