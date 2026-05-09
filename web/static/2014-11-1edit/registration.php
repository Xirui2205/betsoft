<?php
include '_common.php';

head('New Registration – YourLogo.com – A Pure Joy from Betting!');
?>

<body>

	<header id="header" class="sticky">
		<div id="true-header">
			<div class="wrapper">
				<h1><a href="welcome.php"><img src="images/logo.png" alt="YourLogo.com – A Pure Joy from Betting!"></a></h1>
				<nav id="header-nav">
					<ul class="nav navbar-nav">
						<li><a href="#">Help</a></li>
						<li><a href="#">Contact</a></li>
						<li><a href="#">Responsible Gaming</a></li>
						<li class="langs dropdown">
							<a href="#" class="ico ico-flag ico-eng" data-target="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Change Language</a>
							<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
								<li><a href="#" class="ico ico-flag ico-eng active">English</a></li>
								<li><a href="#" class="ico ico-flag ico-cze">Česky</a></li>
								<li><a href="#" class="ico ico-flag ico-svk">Slovensky</a></li>
								<li><a href="#" class="ico ico-flag ico-ger">Deutsch</a></li>
								<li><a href="#" class="ico ico-flag ico-rus">русский</a></li>
							</ul>
						</li>
						<li><a href="#">Forgotten Your Password?</a></li>
						<li><a href="registration.php">Register Now!</a></li>
					</ul>
				</nav>
				<div id="userbar">
					<form action="" method="get">
						<input name="" type="text" placeholder="your username">
						<input name="" type="password" placeholder="password">
						<input name="" type="button" class="btn" value="Login">
					</form>
				</div>
			</div>
		</div>
		<nav id="main-nav">
			<div id="true-nav">
				<div class="wrapper">
					<ul class="nav navbar-nav">
						<li><a href="welcome.php">Homepage</a></li>
						<li><a href="sportsbook.php">Sportsbook</a></li>
						<li><a href="livebets.php">Livebets</a></li>
						<li><a href="welcome_casino.php">Casino</a></li>
						<li><a href="#">Poker</a></li>
						<li><a href="typography.php">Bonus</a></li>
					</ul>
					<div id="searchbar">
						<form action="" method="get">
							<input name="search" type="text" placeholder="player or team">
							<input name="" type="image" value="Search" src="images/search.png">
						</form>
					</div>
				</div>
			</div>
		</nav>
	</header>
	
	<div class="clearfix"></div>

	<main id="content" role="main">
		<div id="true-content" class="welcome">
			<div class="wrapper">
		
				<div id="full">
					<h1>New Registration</h1>
					<div class="contentbox">
						<div class="alert alert-danger alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h2>Oh snap!</h2>
							Change a few things up and try submitting again.
						</div>
						<form class="form-horizontal form-table clearfix" role="form">
							<div class="col-half">
								<h2>Personal Data:</h2>
								<table>
									<tr>
										<td class="col-xs-4"><label for="">Item name:</label></td>
										<td><input type="text" id="datepicker" placeholder="Placeholder text" title="nepiš"></td>
									</tr>
									<tr>
										<td><label for="">Password:</label></td>
										<td><input type="password" id="" placeholder="Placeholder text"></td>
									</tr>
									<tr class="form-error">
										<td><label for="">Item name88:</label></td>
										<td><input type="text" id="w8" placeholder="Placeholder text"><img src="images/icons/input_alert.png" title="chyba7"></td>
									</tr>
									<tr>
										<td><label for="">Item name:</label></td>
										<td>
											<select class="w22p">
												<option>1</option>
												<option>31</option>
											</select>
											<select class="w40p">
												<option>leden</option>
												<option>prosinec</option>
											</select>
											<select class="w30p">
												<option>2000</option>
											</select>
										</td>
									</tr>
								</table>
							</div>
							<div class="col-half">
								<h2>Other Data:</h2>
								<table>
									<tr class="form-error" class="form-error">
										<td class="col-xs-4"><label for="">Item name:</label></td>
										<td><input type="text" id="" placeholder="Placeholder text"><img src="images/icons/input_alert.png" title="Chybová hláška"></td> <!--  data-toggle="tooltip" data-placement="top" -->
									</tr>
									<tr>
										<td><label for="">Item name:</label></td>
										<td><input type="text" id="" placeholder="Placeholder text"></td>
									</tr>
									<tr>
										<td><label for="">Item name:</label></td>
										<td><input type="text" id="" placeholder="Placeholder text"></td>
									</tr>
									<tr>
										<td><label for="">Item name:</label></td>
										<td><input type="text" id="" placeholder="Placeholder text"></td>
									</tr>
								</table>
							</div>
							<div class="form-note clearfix">
								<p class="note-header">Privacy policy:</p>
								<div class="checkbox">
									<input type="checkbox" id="privacy-policy">
									<label for="privacy-policy">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit nisl ut aliquip ex ea commodo consequat. Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</label>
								</div>
								<button class="btn btn-biggest">Go to next step</button>
							</div>
						</form>
					</div>
				</div>

			</div>
		</div>
	</main>
	
	<script type="text/javascript">
	// zobrazit chybu u prvního inputu
	$('img', 'tr.form-error:first').tooltip('show');
	
	// zobrazování / skrývání chyb v tooltipu
	// řešeno zde kvůli spouštění na <tr> a zobrazní nad <img>
	$('tr.form-error').mouseenter(function (){
		$('img', this).tooltip('show');
	});
	$('tr.form-error').mouseleave(function (){
		$('img', this).tooltip('hide');
	});
	</script>
	
	<?php footer() ?>