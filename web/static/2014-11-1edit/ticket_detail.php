<?php
include '_common.php';

head('Ticket #1245646484 – YourLogo.com – A Pure Joy from Betting!');
?>

<body>

	<a href="#content" class="sr-only sr-only-focusable">Skip to main content</a>
	
	<header id="header" class="sticky">
		<div id="true-header">
			<div class="wrapper">
				<h1><a href="welcome.php"><img src="images/logo.png" alt="YourLogo.com – A Pure Joy from Betting!"></a></h1>
				<nav id="header-nav">
					<ul class="nav navbar-nav">
						<li><a href="#">Help</a></li>
						<li><a href="#">Contact</a></li>
						<li><a href="#">Responsible Gaming</a></li>
						<li><a href="#" class="langs lang-en">Change Language</a></li>
						<li><a href="#">Forgotten Your Password?</a></li>
						<li><a href="registration.php">Register Now!</a></li>
					</ul>
				</nav>
				<div id="userbar">
					<ul>
						<li class="ico-user">Jan Novák</li>
						<li class="ico-money">1500 USD</li>
						<li><a href="#" class="arrow">Vklad</a></li>
						<li><a href="#" class="arrow">Účet</a></li>
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
						<li><a href="#">Livebets</a></li>
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
					<h2 class="boxheader ico-user">Account</h2>
					<nav id="left-nav">
						<ul class="nav arrows">
							<li><a href="#">Profil</a></li>
							<li><a href="#" class="active">Transakce</a></li>
							<li><a href="#">Platby</a></li>
							<li><a href="#">Vklad</a></li>
							<li><a href="#">Výběr</a></li>
						</ul>
					</nav>

					<h2 class="boxheader ico-user">Balance</h2>
					<div class="contentbox balance">
						Your<br>Current Balance
						<div>1500 USD</div>
					</div>
				</div>
				
				<div id="right">
					<h2 class="boxheader">Detail tiketu <span class="betslip-result result-ok">Výhra</span></h2>
					<div class="contentbox padding">
						<h1 class="betslip-result result-ok">Tiket #1245646484</h1>
						<table>
							<thead>
								<tr>
									<th class="text-right">#</th>
									<th>Zápas</th>
									<th>Soutěž</th>
									<th class="text-center">Kurz</th>
									<th class="text-center">Tip</th>
									<th>Výsledek</th>
									<th></th>
									<th class="text-right">Vyhodnoceno</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="text-right">1.</td>
									<td>Sparta – Slavia</td>
									<td>1. česká liga</td>
									<td class="text-center">2,55</td>
									<td class="text-center">1</td>
									<td><i class="fa fa-question result-na"></i></td>
									<td class="text-center"><span class="ico ico-money ico-disabled"></span><a href="#" class="ico ico-live"></a></td>
									<td class="text-right">—</td>
								</tr>
								<tr>
									<td class="text-right">2.</td>
									<td>Sparta – Slavia</td>
									<td>1. česká liga</td>
									<td class="text-center">2,55</td>
									<td class="text-center">Under</td>
									<td><i class="fa fa-times result-ko"></i> Over</td>
									<td class="text-center"><a href="#" class="ico ico-money"></a><span class="ico ico-live ico-disabled"></span></td>
									<td class="text-right">15. 9. 2014 15:22:33</td>
								</tr>
								<tr>
									<td class="text-right">3.</td>
									<td>Sparta – Slavia</td>
									<td>1. česká liga</td>
									<td class="text-center">2,55</td>
									<td class="text-center">1</td>
									<td><i class="fa fa-check result-ok"></i> 1</td>
									<td class="text-center"><a href="#" class="ico ico-money"></a><span class="ico ico-live ico-disabled"></span></td>
									<td class="text-right">15. 9. 2014 15:22:33</td>
								</tr>
								<tr>
									<td class="text-right">4.</td>
									<td>Sparta – Slavia</td>
									<td>1. česká liga</td>
									<td class="text-center">2,55</td>
									<td class="text-center">1</td>
									<td><i class="fa fa-ban result-question"></i> Storno</td>
									<td class="text-center"><a href="#" class="ico ico-money"></a><span class="ico ico-live ico-disabled"></span></td>
									<td class="text-right">15. 9. 2014 15:22:33</td>
								</tr>
							</tbody>
						</table>
						<div class="summary">
							<table>
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
						<table>
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