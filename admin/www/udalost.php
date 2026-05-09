<?php
if(!defined("ROOT")) define("ROOT",dirname( dirname( dirname(__FILE__) ) ) . '/');

include(ROOT.'common/includes.inc.php');
include 'config_local.php';
include "common.php";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="cs" xml:lang="cs">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta http-equiv="Content-Language" content="cs" />
 <meta name="keywords" content="" />
 <meta name="description" content=" " />
 <meta name="abstract" content="" />
 <meta name="robots" content='index,follow' />
 <meta name="googlebot" content='index,follow,snippet,archive' />
 <meta name="Author" content="" />
 <!-- CACHE - MSIE /-->
 <meta http-equiv='Cache-Control' content='must-revalidate, post-check=0, pre-check=0' />
 <meta http-equiv='Pragma' content='public' />
 <!-- CACHE - MSIE end /-->
 <!-- CACHE - other browsers /-->
 <meta http-equiv='Cache-Control' content='no-cache' />
 <meta http-equiv='Pragma' content='no-cache' />
 <meta http-equiv='Expires' content='-1' />
 <!-- CACHE - other browsers end /-->
 <!-- BROWSER SPECIFIC FEATURES = ALL OFF /-->
 <!-- MSIE - 'helpful' features /-->
 <meta http-equiv='MSThemeCompatible' content='no' />
 <meta name='MSSmartTagsPreventParsing' content='TRUE' />
 <!-- OPERA - image resizing /-->
 <meta name='autosize' content='off' />
 <!-- BROWSER SPECIFIC FEATURES = end /-->
    <title> </title>
 <link rel='shortcut icon' type='image/x-icon' href='' />
    <style type="text/css">
    <!--
     .floatleft{
      float:left;
     }
     .img{
      border:0px;
     }
    -->
    </style>
    <?php

     if(!isset($_GET['node']) || mb_strlen($_GET['node']) == 0) echo '<script language="javascript" type="text/javascript" src="/js/tinymce/jscripts/tiny_mce/tiny_mce_popup.js?r=' . RELEASE_REV . '"></script>';

    ?>
</head>
<body>
        <h3>Výběr události</h3>
        <div class="mceActionPanel">
            <div>
            <form method="GET">
            <table><tr><th>Sport</th><th>Oblast</th><th>Událost</th></tr><tr><td valign="top">
                <?php
                
                $preklad = new Preklady();
                $sport = new Sport();
                $sArr = $sport->getSport();
                $sArr[0]['sport_id'] = '';
                $sArr[0]['nazev'] = '*';
                ksort($sArr);
                foreach($sArr as $id => $val){
                  echo '<input onClick="submit();" type="radio" name="sport_id" value="'.$val['sport_id'].'"'.((isset($_GET['sport_id']) && ($_GET['sport_id'] == $val['sport_id']))?' checked':'').' /> '.$val['nazev'].'<br />';
                }

                echo '</td><td valign="top">';

                $oblast = new Oblast();
                $oArr = $oblast->getOblast();
                $oArr[0]['oblast_id'] = '';
                $oArr[0]['nazev'] = '*';
                ksort($oArr);
                foreach($oArr as $id => $val){
                  echo '<input onClick="submit();" type="radio" name="oblast_id" value="'.$val['oblast_id'].'"'.((isset($_GET['oblast_id']) && ($_GET['oblast_id'] == $val['oblast_id']))?' checked':'').' /> '.$preklad->findTrans($val['nazev'],1).'<br />';
                }

                echo '</td><td valign="top">';
                if(isset($_GET['sport_id']) || isset($_GET['oblast_id'])) {
                  $where = '';
                  if(isset($_GET['sport_id']) && ($_GET['sport_id'] != '')) $where .= 'sport_id = '.$_GET['sport_id'];
                  if(isset($_GET['oblast_id']) && ($_GET['oblast_id'] != '')) {
                    if($where != '') $where .= ' AND ';
                    $where .= 'oblast_id = '.$_GET['oblast_id'];
                  }
                  if($where != '') $where = 'where '.$where;
                  $udalost = new Udalost();
                  $res = $udalost->selectData($where);
                  while ($row =& $res->fetchRow()){
                    echo '<input onClick="window.opener.document.getElementById(\''.$_GET['node'].'\').value=this.value;" type="radio" name="udalost_id" value="'.$row['udalost_id'].'"'.((isset($_GET['udalost_id']) && ($_GET['udalost_id'] == $row['udalost_id']))?' checked':'').' /> '.$preklad->findTrans($row['nazev'],1).' ('.$row['udalost_id'].')<br />';
                  }

                }
                else echo '&nbsp;';

                echo '</td></tr></table><p>';
                echo '<input type="hidden" name="node" value="'.$_GET['node'].'" />';
                if(!isset($_GET['node'])) echo '<input type="button" id="cancel" name="cancel" value="Zavřít" onclick="tinyMCEPopup.close();" />';
                else echo '<input type="button" id="cancel" name="cancel" value="Zavřít" onclick="self.close();" />';
                echo '</p></form>';
                ?>
            </div>
        </div>
</body>
</html>
