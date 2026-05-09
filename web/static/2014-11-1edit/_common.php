<?php

function head($title, $params = array()){
?><!doctype html>
<!--[if IE 7 ]> <html lang="en-gb" class="isie ie7 oldie no-js"> <![endif]-->
<!--[if IE 8 ]> <html lang="en-gb" class="isie ie8 oldie no-js"> <![endif]-->
<!--[if IE 9 ]> <html lang="en-gb" class="isie ie9 no-js"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en-gb" class="no-js"> <!--<![endif]-->

<head>
	<title><?= $title ?></title>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="keywords" content="">
	<meta name="description" content="">

	<?php
		$cyrillic = '';
		if(isset($params['welcome'])){ 
			$cyrillic = ',cyrillic-ext';
		}
	?>
	<link href="http://fonts.googleapis.com/css?family=Roboto+Condensed:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic&amp;subset=latin,latin-ext<?php echo $cyrillic ?>" rel="stylesheet" type="text/css">
	<link href="http://fonts.googleapis.com/css?family=Roboto:400,400italic&amp;subset=latin,latin-ext<?php echo $cyrillic ?>" rel="stylesheet" type="text/css">

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<link href="css/reset.css" rel="stylesheet">
	<link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
	<link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
	<link href="css/animations.min.css" rel="stylesheet">
	<link href="css/main.css" rel="stylesheet">
	<?php  if(isset($params['welcome'])){ ?><link href="css/welcome.css" rel="stylesheet"><?php } ?>
	
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
	
	<link href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/le-frog/jquery-ui.css" rel="stylesheet" type="text/css" media="screen" />
	
	<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>	
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>-->
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.13.0/jquery.validate.min.js"></script>
	<script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.13.0/localization/messages_<?= LANG_BROWSER ?>.js"></script>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	
	
	
	<script src="js/appear.min.js"></script>
	<script src="js/animations.min.js"></script>
	<script src="js/main.js"></script>
</head>
<?php
}

function footer($welcome = false){
?>
	<div class="clearfix"></div>

	<footer id="footer">
		<div id="true-footer">
			<div class="wrapper">
				<nav id="footer-nav">
					<ul class="nav navbar-nav<?php if($welcome){ ?> logos<?php } ?>">
						<li><a href="#">Aktuality</a></li>
						<li><a href="#">Mobilní verze</a></li>
						<li><a href="#">Partnerství</a></li>
						<li><a href="#">Zaměstnání</a></li>
						<li><a href="#">Sportbets</a></li>
						<li><a href="livebets.php">Livebets</a></li>
						<li><a href="#">Casino</a></li>
						<li><a href="#">Poker</a></li>
					</ul>
				</nav>
				<p>Přístupem, pokračovaným užíváním nebo prohlížením této stránky souhlasíte, že využíváme určitých cookies pro zdokonalení služeb pro naše zákazníky. Náš server používá výhradně cookies, které vylepšují chod stránky a nenarušují vaše soukromí. Více informací o využití, spracování a případném zakázání cookies můžete najít zde.</p>
				<p class="bottom">NAME (licence: XXXXXXXXX) je licencována britskou licenční radou Gambling Commission.<br>Copyright &copy; 2014 NAME. Design by <a href="//www.wda.cz">WDA Czech, s.r.o.</a> All rights reserved.</p>
			</div>
		</div>
	</footer>
	
	<a class="scrollup" href="#"><i class="fa fa-chevron-circle-up"></i></a>

	<!-- js - presunuto do head -->
	<script>
		$(document).ready(function () {
			// datum narození
			$('#datepicker').datepicker({
				changeMonth: true, // zobrazí selecty pro měsíc 
				changeYear: true, // a rok
				showMonthAfterYear: true, // kvůli omezení měsíců ve výchozím roce pro 18tiny
				minDate: "-120Y",
				maxDate: "-18Y",
				yearRange: '-120:-18', // zobrazí v selectu hned všechny roky (bez překlikávání)
				altField  : '#date_of_birth_system_format',
				altFormat : 'yy-mm-dd'
			});
			
			// form tooltip
			/*$('td *').tooltip({
				position: {
					my: "left top",
					at: "right+5 top-5"
				}
			});*/
		});
	</script>
	<!-- ** -->
	
	<div id="welcome-bg" class="animate-in slow-mo" data-anim-type="fade-in"></div>

</body>
</html>
<?php
}