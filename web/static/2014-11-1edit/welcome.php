<?php
include '_common.php';

head('YourLogo.com – A Pure Joy from Betting!', array('welcome' => true));
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
						<li><a href="welcome.php" class="active">Homepage</a></li>
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
<div class="wrapper"><!-- div.wrapper zde nebyl, ale zda se, že to nevadí -->
			<div id="welcome">
				<div class="wrapper">
					<p class="welcome-big animate-in" data-anim-type="bounce-in-left" data-anim-delay="450">Bet your €25</p>
					<p class="welcome-big welcome-highlight animate-in" data-anim-type="bounce-in-right" data-anim-delay="900">Get FREE €25</p>
					<p class="welcome-text animate-in" data-anim-type="fade-in" data-anim-delay="1600">Open an account, make a deposit of €25 or more and you will be entitled to a 100% bonus on your qualifying deposit up to a maximum of €25.</p>
					<a href="account.php" class="btn animate-in" data-anim-type="fade-in" data-anim-delay="2000">Open Your Account!</a>
				</div>
			</div>
			
			<div class="clearfix"></div>
			
			<div class="boxes-4">
				<div class="container">
					<div class="box">
						<a href="#">
							<h2>Sportsbook</h2>
							<div class="box-image"><img src="images/boxes/sportsbook.jpg" alt="Sportsbook"></div>
							<p>Will Tottenham be walking in a Wembley wonderland?</p>
						</a>
					</div>
					<div class="box">
						<a href="#">
							<h2>Livebets</h2>
							<div class="box-image"><img src="images/boxes/livebets.jpg" alt="Livebets"></div>
							<p>Stay in the game until the last second and win!</p>
						</a>
					</div>
					<div class="box">
						<a href="welcome_casino.php">
							<h2>Casino Online</h2>
							<div class="box-image"><img src="images/boxes/casino_online.jpg" alt="Casino Online"></div>
							<p>Try your luck in our great online casino games!</p>
						</a>
					</div>
					<div class="box">
						<a href="#">
							<h2>Poker Games</h2>
							<div class="box-image"><img src="images/boxes/poker_games.jpg" alt="Poker Games"></div>
							<p>Ultimate challenge: Grab your Freeroll tickets!</p>
						</a>
					</div>
				</div>
			</div>

			<div class="boxes-4">
				<div class="container">
					<div class="box">
						<a href="#">
							<h2>Live Scores</h2>
							<div class="box-image"><img src="images/boxes/live_scores.jpg" alt="Live Scores"></div>
							<p>Will Tottenham be walking in a Wembley wonderland?</p>
						</a>
					</div>
					<div class="box">
						<a href="#">
							<h2>Mobile</h2>
							<div class="box-image"><img src="images/boxes/mobile.jpg" alt="Mobile"></div>
							<p>Stay in the game until the last second and win!</p>
						</a>
					</div>
					<div class="box">
						<a href="#">
							<h2>Bonus</h2>
							<div class="box-image"><img src="images/boxes/bonus.jpg" alt="Bonus"></div>
							<p>Try your luck in our great online casino games!</p>
						</a>
					</div>
					<div class="box">
						<a href="#">
							<h2>Games</h2>
							<div class="box-image"><img src="images/boxes/games.jpg" alt="Games"></div>
							<p>Ultimate challenge: Grab your Freeroll tickets!</p>
						</a>
					</div>
				</div>
			</div>
</div>
		</div>
	</main>
	
	<?php footer(true) ?>