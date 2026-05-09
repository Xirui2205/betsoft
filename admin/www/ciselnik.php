<?php
	header("Content-type:text/html; charset=UTF-8");
	if(!defined("ROOT")) define("ROOT",dirname( dirname( dirname(__FILE__) ) ) . '/');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="cs" xml:lang="cs">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta http-equiv="Content-Language" content="cs" />
 <meta name="keywords" content="on-line casino, games, offshore, secure, legal, chance, licensed, regulated, on-line," />
 <meta name="description" content="Play on-line games for real money or for free. " />
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
	<title> Administration </title>
 <link rel='shortcut icon' type='image/x-icon' href='_img/' />
 <link rel="stylesheet" href="/css/ciselnik.css?r=<?= RELEASE_REV ?>" media="screen" type="text/css" />
 <script src="<?= JQUERY_URI ?>" type="text/javascript" language="javascript"></script>
 <script src="/js/mainscript.js?r=<?= RELEASE_REV ?>" type="text/javascript" language="javascript"></script>

</head>

<body>
<?php
//if(!defined("ROOT")) define("ROOT","");
	include(ROOT.'common/includes.inc.php');
	include '../config_local.php';
	include "common.php";
?>

	<input type="hidden" name="teamCount" id="teamCount" value="0" />

<?php
	try {
		//Ciselnik prekladu#
		if (isset($_GET['cis_id']) && $_GET['cis_id'] == "preklady") {
			$ob = new CiselnikPreklady;

		}
		//Ciselnik Kombinaci k sazkam#
		else if (isset($_GET['cis_id']) && $_GET['cis_id'] == "kombinace") {
			$ob = new CiselnikKombinace;
		}
		//Ciselnik Tymu#
		else if (isset($_GET['cis_id']) && $_GET['cis_id'] == "tymy") {
			$ob = new CiselnikTymy;
		//	$ob->runAction();
		}
		else {
			$ob = new CiselnikNenalezen;
		}
		echo $ob->ShowResult();
	}
	catch(exHandler $e) {
		$e->produceAllError();
	}
?>
</body>
</html>
