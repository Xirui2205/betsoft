<?php
	if(!defined("ROOT")) define("ROOT",dirname( dirname( dirname(__FILE__) ) ) . '/');
	//include(ROOT.'common/includes.inc.php');

	include(ROOT.'common/includes.inc.php');
	include '../config_local.php';
	include "common.php";

	//init stuff
	$dbGameZend = It6_Controller_Plugin_SetController::connectDbWeb();
	It6_Controller_Plugin_SetController::connectDbAdmin();

	$admindb = Zend_Registry::get('zdb_admin');

	$translate = new It6_Translate_Admin(1, $dbGameZend);
	Zend_Registry::set('translate', $translate);

	//TODO: use constant from config instead of class constants It6_WS::* here
	$autoloader = Zend_Loader_Autoloader::getInstance();

	Zend_Registry::set('autoloader', $autoloader);
	$autoloader->registerNamespace('It6_');	
	$autoloader->registerNamespace('Models_');
	$autoloader->registerNamespace('DecoratorForms_');
	$autoloader->pushAutoloader(new It6_AutoloaderAdmin());
	
	$client = new Zend_XmlRpc_Client(WEB_SERVICE_URL);
	Zend_Registry::set('ws', new It6_WS(WS_WRAPPER, $client));
	//init stuff end


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="cs" xml:lang="cs">

<head>
	<meta name="keywords" content="on-line casino, games, offshore, secure, legal, chance, licensed, regulated, on-line," />
	<meta name="description" content="Play on-line games for real money or for free. " />
	<meta name="robots" content='index,follow' />
	<meta name="googlebot" content='index,follow,snippet,archive' />

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

	<title>Insert Image</title>

	<script type="text/javascript" language="javascript" src="<?= JQUERY_URI ?>"></script>
	<script type="text/javascript" language="javascript"  src="/js/mainscript.js?r=<?= RELEASE_REV ?>"></script>
	<script type="text/javascript" language="javascript" src="/js/jquery.dynDateTime.js?r=<?= RELEASE_REV ?>"></script>
	<script type="text/javascript" language="javascript">
		function insertImage(imageArr){
			<?php
				if(!empty($_GET['node'])) {
					echo "\$('#{$_GET['node']}', window.opener.document).val(imageArr['name']);\n";
				}
				else if(!empty($_GET['cbfunc'])) {
					echo "window.opener.{$_GET['cbfunc']}(imageArr);\n";
				}
			?>
			self.close();
		}
	</script>


  <link rel="stylesheet" href="css/blueprint/screen.css?r=<?= RELEASE_REV ?>" type="text/css" media="screen, projection">
  <link rel="stylesheet" href="css/blueprint/print.css?r=<?= RELEASE_REV ?>" type="text/css" media="print">

  <!--[if lt IE 8]>
  <link rel="stylesheet" href="css/blueprint/ie.css?r=<?= RELEASE_REV ?>" type="text/css" media="screen, projection">
  <![endif]-->

  <link rel="stylesheet" href="css/adm.css?r=<?= RELEASE_REV ?>" media="screen" type="text/css" />

</head>

<body>
		<?php

			require ROOT.'admin/include/class/class.Galerie.php';
			$ob = new Galerie();
			$ob->imagePicker();
			echo $ob->getContent();

		?>
</body>

</html>
