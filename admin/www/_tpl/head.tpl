<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="cs" xml:lang="cs">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta http-equiv="Content-Language" content="cs" />
 <meta name="keywords" content="" />
 <meta name="description" content="" />
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
 <!-- OPERA - image resizing /-->
 <meta name='autosize' content='off' />
 <!-- BROWSER SPECIFIC FEATURES = end /-->
	<title>Admin.Vic.cz | {TITLE} </title>
<link rel="stylesheet" href="css/blueprint/screen.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="css/blueprint/print.css" type="text/css" media="print">

<!--[if lt IE 8]>
  <link rel="stylesheet" href="css/blueprint/ie.css" type="text/css" media="screen, projection">
<![endif]-->
 <link rel="stylesheet" href="css/adm.css" media="screen" type="text/css" />

 <script language="Javascript" type="text/javascript">
 <!--
 var host="{JSHOST}";
 var protocol="{PROTOCOL}";
 // -->
	</script>
	<script src="js/jquery.js" type="text/javascript" language="javascript"></script>
	<script src="js/jquery.dynDateTime.js" type="text/javascript" language="javascript"></script>
	<script src="js/calendar-en.js" type="text/javascript" language="javascript"></script>
	<script language="javascript" type="text/javascript" src="js/tinymce/jscripts/tiny_mce/jquery.tinymce.js"></script>
	<script src="js/editinplace.js" type="text/javascript" language="javascript"></script>
	<script src="js/mainscript.js" type="text/javascript" language="javascript"></script>
<link rel="stylesheet" href="css/calendar-green.css" type="text/css">
 <script>
 navHover = function() {
	var lis = document.getElementById("navmenu").getElementsByTagName("LI");
	for (var i=0; i<lis.length; i++) {
		lis[i].onmouseover=function() {
			this.className+=" iehover";
		}
		lis[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" iehover\\b"), "");
		}
	}
}
if (window.attachEvent) window.attachEvent("onload", navHover);
 </script>
</head>
{LOADING}
<body>

<!--Horni lista dokumentu-->
<!-- <div id="hlavicka">
  <div id="toptext">{TOPTEXT}</div>
  <div id="toplogo"><span>{TOPUSER}</span></div>
</div>
<div id="cara"><img src="_clip/nic.gif"  alt="" /></div>-->
<!--Konec Horni lista dokumentu-->



<!--Telo dokumentu-->

<!--Konec Telo dokumentu-->





<div id="container">
<div id="header">
  <img src="/_clip/logo-betservice-new.png"  class="leftBlock" />

	<div class="floatright" id="box-logout" style="height: 25px;width:100px;">
	<span>Bookmaker: petr</span>
	<a href="{HOST}?logoff=1{SUPERB}" class="logoff" >Odhlásit se</a>
	</div>
 <ul id="navmenu">
   <li><a href="{HOST}?section=1{SUPERB}">Home</a></li>
   {MENU}
 </ul>

 <div class="clear"></div>

</div>
  <div id="wrapper">
    <div id="content">

    <noscript><strong class="red">Musíte mít zapnutý Javascript</strong></noscript>
{SHORTWAY}


</div> <br />
{CONTENT}

    </div>
  </div>
  <!--<div id="navigation">
    <p><strong>Optimalozováno pro:</strong></p>
    <ul>
      <li>&nbsp;&nbsp;&nbsp;&nbsp;Firefox 3+</li>
      <li>&nbsp;&nbsp;&nbsp;&nbsp;Chrome 3+</li>
    </ul>
  </div>
  <div id="extra">
    <p><strong>Pravidla</strong></p>
    <p>Všechny aktivity v aplikaci jsou monitorovány. Autorizovaná osoba může kdykoliv zkontrolovat pohyby a akce uživatelů v aplikaci.
    Každý uživatel aplikace je poučen o právech a povinnostech v pracovní době a je zákázáno provádět činnosti, které by mohly být považovány za nežádoucí.</p>
  </div>-->
  <div id="footer">
    <p>&copy; BBAS {BBAS_YEAR}, v{ADMIN_VERSION}</p>
  </div>
</div>

