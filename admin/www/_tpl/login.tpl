<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta http-equiv="Content-Language" content="en" />
 <meta name="keywords" content="" />
 <meta name="description" content=". " />
 <meta name="abstract" content="" />
 <meta name="robots" content='index,follow' />
 <meta name="googlebot" content='index,follow,snippet,archive' />
 <meta name="Author" content="" />
 <!-- CACHE - MSIE /-->
 <meta http-equiv='Cache-Control' content='must-revalidate, post-check=0, pre-check=0' />
 <meta http-equiv='Pragma' content='public' />

 <meta http-equiv='MSThemeCompatible' content='no' />
 <meta name='MSSmartTagsPreventParsing' content='TRUE' />
 <!-- OPERA - image resizing /-->
 <meta name='autosize' content='off' />
 <!-- BROWSER SPECIFIC FEATURES = end /-->
	<title>Administration </title>
 <link rel='shortcut icon' type='image/x-icon' href='' />
 <link rel="stylesheet" href="/css/blueprint/screen.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="/css/blueprint/print.css" type="text/css" media="print">
<!--[if lt IE 8]>
  <link rel="stylesheet" href="/css/blueprint/ie.css" type="text/css" media="screen, projection">
<![endif]-->
 <link rel="stylesheet" href="/css/adm.css" media="screen" type="text/css" />

  <script language="Javascript" type="text/javascript">
<!--
	var host="{JSHOST}";
	var protocol="{PROTOCOL}";
// -->
	</script>
	<script src="/js/jquery.js" type="text/javascript" language="javascript"></script>
	<script src="/js/mainscript.js" type="text/javascript" language="javascript"></script>
	<script src="js/jquery.dynDateTime.js" type="text/javascript" language="javascript"></script>
</head>

<body>
<div id="container">
	<div id="header">
		<img src="/_clip/logo-betservice-new.png"  class="leftBlock" />
		<h1>BBAS v{ADMIN_VERSION}</h1>
	</div>
	<noscript><strong>You must enable Javascript support in your browser</strong></noscript>
	
	
	<div class="{ERRORCLASS}">{ERRORMSG}</div>
	<form action="{HOST}" method="get" style="display:none">
	 <input type="text" id="nick" /><br />
	 <input type="password" id="password" /><br /><br />
	 <input type="checkbox" id="superb" /><br /><br />
	 <input type="button" value="Log On" class="logbutton" onclick="NavigatorAlert();AdminLogin();">
	</form>

	<div class="container left" style="margin-left: 200px">
	 <div class="span-16">&nbsp;</div>
	 <div class="span-16">
		<label for="nick1">Administrátor</label>
		<br />
		<input type="text" id="nick1" onkeypress="return ub_login_enter(event,1,1);" />
		<br />
		<input type="password" id="password1"  onkeypress="return ub_login_enter(event,1,2);" />
	 </div>
	 <div class="span-16">
		<input type="button" value="Log On" class="logbutton" onclick="ub_submit(1);">
	 </div>
	
	  <div class="span-16">&nbsp; </div>
	 <div class="span-16"><hr /> </div>
	 <div class="span-16">&nbsp; </div>
	 <div class="span-16">
		<label for="2">Bookmaker</label>
		<br />
		<input type="text" id="nick2" onkeypress="return ub_login_enter(event,2,1);" />
		<br />
		<input type="password" id="password2" onkeypress="return ub_login_enter(event,2,2);" />
	
	 </div>
	 <div class="span-16">
		<input type="button" value="Log On" class="logbutton" onclick="ub_submit(2);">
	 </div>
	  <div class="span-16 last">&nbsp;</div>
	</div>
</div> <!-- end of container -->

<script type="text/javascript">
$(document).ready(function(){
	$('#nick1').focus();
});
function ub_login_enter(e,fo,fi) {
    var KeyID = (window.event) ? event.keyCode : e.keyCode;
    if(KeyID!=13) return true;
    nick = $('#nick'+fo); pwd = $('#password'+fo);
    if(nick.val() && pwd.val()) { ub_submit(fo); return true; }
    if(nick.val() && !pwd.val() && fi==1 ) { pwd.focus();  return false; }
    if(!nick.val() && pwd.val() && fi==2 ) { nick.focus();  return false; }
    return false;
}
function ub_submit(fo) {
	$('#nick').val( $('#nick'+fo).val() );
	$('#password').val( $('#password'+fo).val() );
	if(fo==2) $('#superb').attr('checked','checked');
	NavigatorAlert(); AdminLogin();
}
</script>


