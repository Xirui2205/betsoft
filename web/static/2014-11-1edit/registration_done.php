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
					<h1>New Registration <small>Step 3 of 3</small></h1>
					<div class="contentbox">
						<ul class="steps">
							<li>Step 1</li>
							<li>Step 2</li>
							<li class="active">Done!</li>
						</ul>

						<div class="cols-msg clearfix">
							<div class="msg-text">
								<h2 class="msg-success">Congratulation!</h2>
								<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci suscipit nisl ut ex ea commodo consequat.</p>
								<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh exerci tation suscipit nisl ut aliquip ex ea consequat.</p>
								<a href="#" class="btn btn-biggest">Go to Bet!</a>
								<a href="#" class="btn btn-biggest">Deposit</a>
								<a href="#" class="btn btn-biggest">Hotline</a>
								<p class="msg-note">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam!</p>
							</div>
							<div class="msg-imgs">
								<h2>Where Next?</h2>
								<a href="#"><img src="images/next_sportbetting.jpg"></a>
								<a href="#"><img src="images/next_live_betting.jpg"></a>
								<a href="#"><img src="images/next_online_casino.jpg"></a>
								<a href="#"><img src="images/next_poker_game.jpg"></a>
							</div>

					</div>
				</div>

			</div>
		</div>
	</main>
	
	<?php footer() ?>
