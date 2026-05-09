<?php
include '_common.php';

head('Ticket #1245646484 – YourLogo.com – A Pure Joy from Betting!');
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
					<ul>
						<li class="ico ico-item ico-user">Jan Novák</li>
						<li class="ico ico-item ico-money">1500 USD</li>
						<li class="dropdown">
							<a href="#" class="arrow" data-target="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Vklad</a>
							<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
								<li><a href="#">Lorem ipsum</a></li>
								<li><a href="#">Dolor sit amet</a></li>
								<li><a href="#">Consectetuer</a></li>
							</ul>
						</li>
						<li class="dropdown">
							<a href="#" class="arrow" data-target="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Účet</a>
							<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
								<li><a href="#">Lorem ipsum</a></li>
								<li><a href="#">Dolor sit amet</a></li>
								<li><a href="#">Consectetuer</a></li>
							</ul>
						</li>
						<li><a href="#">Odhlásit</a></li>
					</ul>
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
		
				<div id="left">
					<h2 class="boxheader ico ico-item ico-user">Account</h2>
					<nav id="left-nav">
						<ul class="nav arrows">
							<li><a href="#">Profil</a></li>
							<li><a href="#" class="active">Transakce</a></li>
							<li><a href="#">Platby</a></li>
							<li><a href="#">Vklad</a></li>
							<li><a href="#">Výběr</a></li>
						</ul>
					</nav>

					<h2 class="boxheader ico ico-item ico-user">Balance</h2>
					<div class="contentbox balance">
						Your<br>Current Balance
						<div>1500 USD</div>
					</div>
				</div>
				
				<div id="right">
					<h2 class="boxheader">Detail tiketu <span class="betslip-result result-na">Nevyhodnoceno</span></h2>
					<div class="contentbox padding">
						<h1 class="betslip-result result-na">Tiket #1245646484</h1>
						<table class="table table-striped table-hover">
							<thead>
								<tr>
									<th class="text-right">#</th>
									<th>Zápas</th>
									<th>Soutěž</th>
									<th class="text-center">Kurz</th>
									<th class="text-center">Tip</th>
									<th class="text-center">Výsledek</th>
									<th></th>
									<th></th>
									<th class="text-right">Datum vyhodnocení</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="text-right">1.</td>
									<td>Sparta – Slavia</td>
									<td>1. česká liga</td>
									<td class="text-center">2,55</td>
									<td class="text-center">1</td>
									<td class="text-center">1</td>
									<td class="text-center"><img src="images/icons/success.png" alt="Výhra" title="Výhra" data-toggle="tooltip" data-placement="left"></td>
									<td class="text-center"><img src="images/icons/money.png" alt="Money" class="ico-disabled"> <a href="#" class="ico-img" title="Sledovat Live!" data-toggle="tooltip" data-placement="top"><img src="images/icons/live.png" alt="Live!"></a></td>
									<td class="text-right">—</td>
								</tr>
								<tr>
									<td class="text-right">2.</td>
									<td>Sparta – Slavia</td>
									<td>1. česká liga</td>
									<td class="text-center">2,55</td>
									<td class="text-center">Under</td>
									<td class="text-center">Over</td>
									<td class="text-center"><img src="images/icons/error.png" alt="Prohra" title="Prohra" data-toggle="tooltip" data-placement="left"></td>
									<td class="text-center"><a href="#" class="ico-img" title="Money…" data-toggle="tooltip" data-placement="top"><img src="images/icons/money.png" alt="Money"></a> <img src="images/icons/live.png" alt="Live!" class="ico-disabled"></td>
									<td class="text-right">15. 9. 2014 15:22:33</td>
								</tr>
								<tr>
									<td class="text-right">3.</td>
									<td>Sparta – Slavia</td>
									<td>1. česká liga</td>
									<td class="text-center">2,55</td>
									<td class="text-center">1</td>
									<td class="text-center">—</td>
									<td class="text-center"><img src="images/icons/question.png" alt="Nevyhodnoceno" title="Nevyhodnoceno" data-toggle="tooltip" data-placement="left"></td>
									<td class="text-center"><a href="#" class="ico-img" title="Money…" data-toggle="tooltip" data-placement="top"><img src="images/icons/money.png" alt="Money"></a> <img src="images/icons/live.png" alt="Live!" class="ico-disabled"></td>
									<td class="text-right">15. 9. 2014 15:22:33</td>
								</tr>
								<tr>
									<td class="text-right">4.</td>
									<td>Sparta – Slavia</td>
									<td>1. česká liga</td>
									<td class="text-center">2,55</td>
									<td class="text-center">1</td>
									<td class="text-center">Storno</td>
									<td class="text-center"><img src="images/icons/block.png" alt="Storno" title="Storno" data-toggle="tooltip" data-placement="left"></td>
									<td class="text-center"><a href="#" class="ico-img" title="Money…" data-toggle="tooltip" data-placement="top"><img src="images/icons/money.png" alt="Money"></a> <img src="images/icons/live.png" alt="Live!" class="ico-disabled"></td>
									<td class="text-right">15. 9. 2014 15:22:33</td>
								</tr>
							</tbody>
						</table>
						<div class="summary">
							<table class="table">
									<tr>
										<td colspan="6">Založeno</td>
										<td class="text-right">15. 9. 2014 15:22:33</td>
									</tr>
									<tr>
										<td colspan="6">Vyhodnoceno</td>
										<td class="text-right">15. 9. 2014 15:22:33</td>
									</tr>
									<tr>
										<td colspan="6">Vyplaceno</td>
										<td class="text-right">15. 9. 2014 15:22:33</td>
									</tr>
									<tr>
										<td colspan="7" class="summary-money">Vsazeno <span>355,00 EUR</span></td>
									</tr>
									<tr>
										<td colspan="7" class="summary-money highlight">Výhra <span>1 355,00 EUR</span></td>
									</tr>
								</tbody>
							</table>
						</div>
						<table class="table">
							<thead>
								<tr>
									<th></th>
									<th>Vklad</th>
									<th>Min. výhra</th>
									<th>Max. výhra</th>
									<th>Stav</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>2 z 4</td>
									<td></td>
									<td></td>
									<td></td>
									<td>0</td>
								</tr>
								<tr>
									<td>3 z 5</td>
									<td></td>
									<td></td>
									<td></td>
									<td>1500</td>
								</tr>
							</tbody>
						</table>

					</div>
				</div>

			</div>
		</div>
	</main>
	
	<?php footer() ?>
