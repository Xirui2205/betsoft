<?php
include '_common.php';

head('Sportsbook – YourLogo.com – A Pure Joy from Betting!');
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
						<li><a href="sportsbook.php" class="active">Sportsbook</a></li>
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
					<h2 class="boxheader">Top Bets</h2>
					<ul class="nav nav-tabs" role="tablist">
						<li class="active"><a href="#tab-odds" role="tab" data-toggle="tab">Odds</a></li>
						<li><a href="#tab-live" role="tab" data-toggle="tab">Live</a></li>
					</ul>
					<div class="tab-content">
						<div id="tab-odds" class="tab-pane fade in active no-padding">
							<ul class="nav icos">
								<li>
									<a href="odds.php" class="ico ico-sport ico-soccer parent">Premier League <span class="count">145</span></a>
									<ul class="no-bg">
										<li><a href="odds.php" class="ico ico-item ico-star">Barclays Premier <span class="count">145</span></a></li>
										<li><a href="odds.php" class="ico ico-item ico-star">CocaCola League <span class="count">145</span></a></li>
									</ul>
								</li>
								<li>
									<a href="odds.php" class="ico ico-sport ico-tennis parent">ATP500 <span class="count">145</span></a>
									<ul class="no-bg">
										<li><a href="odds.php" class="ico ico-item ico-star">Australian Open <span class="count">145</span></a></li>
										<li><a href="odds.php" class="ico ico-item ico-star">Melbourne <span class="count">145</span></a></li>
									</ul>
								</li>
							</ul>
						</div>
						<div id="tab-live" class="tab-pane fade no-padding">
							<ul class="nav icos">
								<li>
									<a href="#" class="ico ico-sport ico-soccer">Premier League <span class="count">145</span></a>
									<ul class="no-bg">
										<li><a href="odds.php" class="ico ico-item ico-star">Barclays Premier <span class="count">145</span></a></li>
										<li><a href="odds.php" class="ico ico-item ico-star">CocaCola League <span class="count">145</span></a></li>
									</ul>
								</li>
							</ul>
						</div>
					</div>

					<h3 class="boxheader">Time Filter</h3>
					<div class="contentbox small-padding">
						<form class="form-inline" role="form">
							<div class="checkbox highlight"><label><input type="checkbox" checked> Today</label></div>
							<div class="checkbox"><label><input type="checkbox"> Tommorow</label></div>
						</form>
					</div>

					<h3 class="boxheader"><span class="highlight">25 787</span> Bets</h3>
					<ul class="nav icos">
						<li>
							<a href="#" class="ico ico-sport ico-soccer parent">Soccer <span class="count">145</span></a>
							<ul>
								<li><a href="#" class="ico ico-flag ico-cze">Czech <span class="count">145</span></a></li>
								<li><a href="#" class="ico ico-flag ico-bra">Brasil <span class="count">145</span></a></li>
								<li>
									<a href="#" class="ico ico-flag ico-eng">England <span class="count">145</span></a>
									<ul>
										<li><a href="#">Barclays Premier <span class="count">145</span></a></li>
										<li><a href="#">CocaCola League <span class="count">145</span></a></li>
									</ul>
								</li>

							</ul>
						</li>
						<li><a href="#" class="ico ico-sport ico-tennis parent">Tennis <span class="count">145</span></a></li>
						<li><a href="#" class="ico ico-sport ico-basketball parent">Basketball <span class="count">145</span></a></li>
						<li><a href="#" class="ico ico-sport ico-icehockey parent">Ice Hockey <span class="count">145</span></a></li>
						<li><a href="#" class="ico ico-sport ico-handball parent">Handball <span class="count">145</span></a></li>
						<li><a href="#" class="ico ico-sport ico-volleyball parent">Volleyball <span class="count">145</span></a></li>
						<li><a href="#" class="ico ico-sport ico-boxing parent">Boxing <span class="count">145</span></a></li>
						<li><a href="#" class="ico ico-sport ico-rugby parent">Rugby Union <span class="count">145</span></a></li>
						<li><a href="#" class="ico ico-sport ico-golf parent">Golf <span class="count">145</span></a></li>
						<li><a href="#" class="ico ico-sport ico-racing parent">Formula 1 <span class="count">145</span></a></li>
						<li><a href="#" class="ico ico-sport ico-floorball parent">Floorball <span class="count">145</span></a></li>
						<li><a href="#" class="ico ico-sport ico-amfootball parent">American Football <span class="count">145</span></a></li>
						<li><a href="#" class="ico ico-sport ico-badminton parent">Badminton <span class="count">145</span></a></li>
					</ul>
					<div class="contentbox show-more"><a href="#" class="more">Show more sports</a></div>

					<div class="boxheader">Bet &amp; Win Live!</div>
					<div class="contentbox no-padding">
						<a href="#"><img src="temp/left_banner.jpg" alt="Bet &amp; Win Live!"></a>
					</div>
					<div class="contentbox description">
						Here could be a description of banner above
					</div>
				</div>
				
				<div id="center">
					<h2 class="boxheader">Hot Betting News</h2>
					<div id="carousel-sportsbook" class="carousel slide" data-ride="carousel">
						<ol class="carousel-indicators">
							<li data-target="#carousel-sportsbook" data-slide-to="0" class="active"></li>
							<li data-target="#carousel-sportsbook" data-slide-to="1"></li>
							<li data-target="#carousel-sportsbook" data-slide-to="2"></li>
							<li data-target="#carousel-sportsbook" data-slide-to="3"></li>
						</ol>
						<div class="carousel-inner">
							<div class="item active">
								<a href="#">
									<img src="temp/sportsbook_photo.jpg" alt="Real Madrid">
									<div class="carousel-caption">
										<div class="carousel-info">Champions League Final:</div>
										<h3>Real Madrid<br>Atletico Madrid</h3>
										<p>Despite playing for half an hour with ten men, Burton edged to a vital 1-0 home success over Southend in their League Two play-off semi-final  first leg. Can the Brewers go through at Roots Hall?</p>
									</div>
								</a>
								<ul class="carousel-bets">
									<li class="highlight"><a href="#">Real Madrid <span>1.33</span></a></li>
									<li><a href="#">Draw <span>4.55</span></a></li>
									<li><a href="#">Atletico Madrid <span>1.90</span></a></li>
								</ul>
							</div>
							<div class="item">
								<a href="#">
									<img src="temp/sportsbook_photo.jpg" alt="Real Madrid">
									<div class="carousel-caption">
										<div class="carousel-info">Champions League Final:</div>
										<h3>Real Madrid<br>Atletico Madrid</h3>
										<p>Despite playing for half an hour with ten men, Burton edged to a vital 1-0 home success over Southend in their League Two play-off semi-final  first leg. Can the Brewers go through at Roots Hall?</p>
									</div>
								</a>
								<ul class="carousel-bets">
									<li class="highlight"><a href="#">1 <span>1.33</span></a></li>
									<li><a href="#">10 <span>4.55</span></a></li>
									<li><a href="#">0 <span>1.90</span></a></li>
									<li><a href="#">20 <span>4.55</span></a></li>
									<li><a href="#">2 <span>1.90</span></a></li>
								</ul>
							</div>
							<div class="item">
								<a href="#">
									<img src="temp/sportsbook_photo.jpg" alt="Real Madrid">
									<div class="carousel-caption">
										<div class="carousel-info">Champions League Final:</div>
										<h3>Real Madrid<br>Atletico Madrid</h3>
										<p>Despite playing for half an hour with ten men, Burton edged to a vital 1-0 home success over Southend in their League Two play-off semi-final  first leg. Can the Brewers go through at Roots Hall?</p>
									</div>
								</a>
							</div>
							<div class="item">
								<a href="#">
									<img src="temp/sportsbook_photo_2.jpg" alt="Real Madrid">
									<div class="carousel-caption carousel-caption-right">
										<div class="carousel-info">Champions League Final:</div>
										<h3>Real Madrid<br>Atletico Madrid</h3>
										<p>Despite playing for half an hour with ten men, Burton edged to a vital 1-0 home success over Southend in their League Two play-off semi-final  first leg. Can the Brewers go through at Roots Hall?</p>
									</div>
								</a>
							</div>
						</div>
					</div>

					<h2 class="boxheader">Top Betting Odds</h2>
					<ul class="nav nav-tabs" role="tablist">
						<li class="active"><a href="#tab-top-live" role="tab" data-toggle="tab">Live bets</a></li>
						<li><a href="#tab-top-last" role="tab" data-toggle="tab">Last minute</a></li>
						<li><a href="#tab-top-best" role="tab" data-toggle="tab">Best Odds</a></li>
						<li><a href="#tab-top-friendly" role="tab" data-toggle="tab">Friendly</a></li>
						<li><a href="#tab-top-opportunity" role="tab" data-toggle="tab">Opportunity</a></li>
					</ul>
					<div class="tab-content">
						<div id="tab-top-live" class="tab-pane fade in active contentbox no-padding">
							<table class="table table-striped table-hover matches calendar">
								<thead>
									<tr>
										<th>Zápas</th>
										<th class="text-center">1</th>
										<th class="text-center">10</th>
										<th class="text-center">0</th>
										<th class="text-center">20</th>
										<th class="text-center">2</th>
										<th class="text-center">Stav</th>
										<th>Čas</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="ico ico-sport ico-tennis"><strong>Kvitová – Williams</strong><br>WTA | Wimbledon</td>
										<td class="odds"><a href="#" class="selected">1.1</a></td>
										<td class="odds"><a href="#">2.2</a></td>
										<td class="odds"><a href="#">2.2</a></td>
										<td class="odds"><a href="#">3.3</a></td>
										<td class="odds"><a href="#">3.3</a></td>
										<td class="text-center">1:2</td>
										<td>2. set</td>
									</tr>
									<tr>
										<td class="ico ico-sport ico-tennis"><strong>Kvitová – Williams</strong><br>WTA | Wimbledon</td>
										<td class="odds"><a href="#">1.1</a></td>
										<td class="odds"><a href="#">2.2</a></td>
										<td class="odds"><a href="#">2.2</a></td>
										<td class="odds"><a href="#" class="selected">3.3</a></td>
										<td class="odds"><a href="#">3.3</a></td>
										<td class="text-center">1:2</td>
										<td>2. set</td>
									</tr>
									<tr>
										<td class="ico ico-sport ico-soccer"><strong>Sparta – Slavia</strong><br>1. česká liga</td>
										<td class="odds"><a href="#">1.1</a></td>
										<td class="odds"><a href="#">2.2</a></td>
										<td class="odds"><a href="#">2.2</a></td>
										<td class="odds"><a href="#">3.3</a></td>
										<td class="odds"><a href="#">3.3</a></td>
										<td class="text-center">2:1</td>
										<td>1. poločas</td>
									</tr>
									<tr>
										<td class="ico ico-sport ico-soccer"><strong>Real Madrid - Espanyol</strong><br>1. španělská liga</td>
										<td class="odds"><a href="#">1.1</a></td>
										<td class="odds"><a href="#">2.2</a></td>
										<td class="odds"><a href="#">2.2</a></td>
										<td class="odds"><a href="#">3.3</a></td>
										<td class="odds"><a href="#">3.3</a></td>
										<td class="text-center">3:0</td>
										<td>2. poločas</td>
									</tr>
									<tr>
										<td class="ico ico-sport ico-tennis"><strong>Kvitová – Williams</strong><br>WTA | Wimbledon</td>
										<td class="odds"><a href="#">1.1</a></td>
										<td class="odds"><a href="#">2.2</a></td>
										<td class="odds"><a href="#">2.2</a></td>
										<td class="odds"><a href="#">3.3</a></td>
										<td class="odds"><a href="#">3.3</a></td>
										<td class="text-center">1:2</td>
										<td>2. set</td>
									</tr>
									<tr>
										<td class="ico ico-sport ico-tennis"><strong>Kvitová – Williams</strong><br>WTA | Wimbledon</td>
										<td class="odds"><a href="#" class="selected">1.1</a></td>
										<td class="odds"><a href="#">2.2</a></td>
										<td class="odds"><a href="#">2.2</a></td>
										<td class="odds"><a href="#">3.3</a></td>
										<td class="odds"><a href="#">3.3</a></td>
										<td class="text-center">1:2</td>
										<td>2. set</td>
									</tr>
									<tr>
										<td class="ico ico-sport ico-tennis"><strong>Kvitová – Williams</strong><br>WTA | Wimbledon</td>
										<td class="odds"><a href="#">1.1</a></td>
										<td class="odds"><a href="#">2.2</a></td>
										<td class="odds"><a href="#">2.2</a></td>
										<td class="odds"><a href="#" class="selected">3.3</a></td>
										<td class="odds"><a href="#">3.3</a></td>
										<td class="text-center">1:2</td>
										<td>2. set</td>
									</tr>
									<tr>
										<td class="ico ico-sport ico-soccer"><strong>Sparta – Slavia</strong><br>1. česká liga</td>
										<td class="odds"><a href="#">1.1</a></td>
										<td class="odds"><a href="#">2.2</a></td>
										<td class="odds"><a href="#">2.2</a></td>
										<td class="odds"><a href="#">3.3</a></td>
										<td class="odds"><a href="#">3.3</a></td>
										<td class="text-center">2:1</td>
										<td>1. poločas</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div id="tab-top-last" class="tab-pane fade contentbox no-padding">
							<table class="table table-striped table-hover matches">
								<thead>
									<tr>
										<th>Čas</th>
										<th>Zápas</th>
										<th class="text-center">1</th>
										<th class="text-center">10</th>
										<th class="text-center">0</th>
										<th class="text-center">20</th>
										<th class="text-center">2</th>
										<th colspan="2"></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<span class="ico-self ico-tv" title="TV" data-toggle="tooltip" data-placement="top"></span>
											<strong>Real Madrid - Espanyol</strong><br>1. španělská liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#" class="selected">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>19:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Sparta - Slavia</strong><br>1. česká liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-tennis">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Kvitová – Williams</strong><br>WTA | Wimbledon
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<span class="ico-self ico-tv" title="TV" data-toggle="tooltip" data-placement="top"></span>
											<strong>Real Madrid - Espanyol</strong><br>1. španělská liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>19:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Sparta - Slavia</strong><br>1. česká liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-tennis">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Kvitová – Williams</strong><br>WTA | Wimbledon
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#" class="selected">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Real Madrid - Espanyol</strong><br>1. španělská liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>19:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Sparta - Slavia</strong><br>1. česká liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-tennis">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Kvitová – Williams</strong><br>WTA | Wimbledon
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div id="tab-top-best" class="tab-pane fade contentbox no-padding">
							<table class="table table-striped table-hover matches">
								<thead>
									<tr>
										<th>Čas</th>
										<th>Zápas</th>
										<th class="text-center">1</th>
										<th class="text-center">10</th>
										<th class="text-center">0</th>
										<th class="text-center">20</th>
										<th class="text-center">2</th>
										<th colspan="2"></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<span class="ico-self ico-tv" title="TV" data-toggle="tooltip" data-placement="top"></span>
											<strong>Real Madrid - Espanyol</strong><br>1. španělská liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#" class="selected">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>19:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Sparta - Slavia</strong><br>1. česká liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-tennis">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Kvitová – Williams</strong><br>WTA | Wimbledon
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<span class="ico-self ico-tv" title="TV" data-toggle="tooltip" data-placement="top"></span>
											<strong>Real Madrid - Espanyol</strong><br>1. španělská liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>19:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Sparta - Slavia</strong><br>1. česká liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-tennis">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Kvitová – Williams</strong><br>WTA | Wimbledon
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#" class="selected">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Real Madrid - Espanyol</strong><br>1. španělská liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>19:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Sparta - Slavia</strong><br>1. česká liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-tennis">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Kvitová – Williams</strong><br>WTA | Wimbledon
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div id="tab-top-friendly" class="tab-pane fade contentbox no-padding">
							<table class="table table-striped table-hover matches">
								<thead>
									<tr>
										<th>Čas</th>
										<th>Zápas</th>
										<th class="text-center">1</th>
										<th class="text-center">10</th>
										<th class="text-center">0</th>
										<th class="text-center">20</th>
										<th class="text-center">2</th>
										<th colspan="2"></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<span class="ico-self ico-tv" title="TV" data-toggle="tooltip" data-placement="top"></span>
											<strong>Real Madrid - Espanyol</strong><br>1. španělská liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#" class="selected">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>19:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Sparta - Slavia</strong><br>1. česká liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-tennis">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Kvitová – Williams</strong><br>WTA | Wimbledon
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<span class="ico-self ico-tv" title="TV" data-toggle="tooltip" data-placement="top"></span>
											<strong>Real Madrid - Espanyol</strong><br>1. španělská liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>19:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Sparta - Slavia</strong><br>1. česká liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-tennis">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Kvitová – Williams</strong><br>WTA | Wimbledon
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#" class="selected">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Real Madrid - Espanyol</strong><br>1. španělská liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>19:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Sparta - Slavia</strong><br>1. česká liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-tennis">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Kvitová – Williams</strong><br>WTA | Wimbledon
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div id="tab-top-opportunity" class="tab-pane fade contentbox no-padding">
							<table class="table table-striped table-hover matches">
								<thead>
									<tr>
										<th>Čas</th>
										<th>Zápas</th>
										<th class="text-center">1</th>
										<th class="text-center">10</th>
										<th class="text-center">0</th>
										<th class="text-center">20</th>
										<th class="text-center">2</th>
										<th colspan="2"></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<span class="ico-self ico-tv" title="TV" data-toggle="tooltip" data-placement="top"></span>
											<strong>Real Madrid - Espanyol</strong><br>1. španělská liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#" class="selected">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>19:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Sparta - Slavia</strong><br>1. česká liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-tennis">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Kvitová – Williams</strong><br>WTA | Wimbledon
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<span class="ico-self ico-tv" title="TV" data-toggle="tooltip" data-placement="top"></span>
											<strong>Real Madrid - Espanyol</strong><br>1. španělská liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>19:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Sparta - Slavia</strong><br>1. česká liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-tennis">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Kvitová – Williams</strong><br>WTA | Wimbledon
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#" class="selected">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Real Madrid - Espanyol</strong><br>1. španělská liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>19:00</td>
										<td class="match-icos ico ico-sport ico-soccer">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Sparta - Slavia</strong><br>1. česká liga
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
									<tr>
										<td class="match-date">16. kvě<br>18:00</td>
										<td class="match-icos ico ico-sport ico-tennis">
											<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
											<strong>Kvitová – Williams</strong><br>WTA | Wimbledon
										</td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">1.33</a></td>
										<td class="odds"><a href="#">3.50</a></td>
										<td class="odds"><a href="#">8.50</a></td>
										<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
										<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="contentbox show-more"><a href="#" class="more">Show more odds</a></div>

					<h2 class="boxheader">Try Our New Poker Tables</h2>
					<div class="contentbox promo promo-left">
						<a href="#"><img src="temp/center_banner.jpg" alt="Texas Hold'em"></a>
						<h3 class="highlight">Texas Hold'em</h3>
						<p>Despite playing for half an hour with ten men, Burton edged to a vital success over Southend in their League Two play-off semi-final  first leg. Can the Brewers go through at Roots Hall?</p>
						<a href="#" class="btn btn-big">Join Us Now!</a>
					</div>

					<h2 class="boxheader">Try Our New Poker Tables</h2>
					<div class="contentbox promo promo-right">
						<a href="#"><img src="temp/center_banner.jpg" alt="Texas Hold'em"></a>
						<h3 class="highlight">Texas Hold'em</h3>
						<p>Despite playing for half an hour with ten men, Burton edged to a vital success over Southend in their League Two play-off semi-final  first leg. Can the Brewers go through at Roots Hall?</p>
						<a href="#" class="btn btn-big">Join Us Now!</a>
					</div>
				</div>

				<div id="rightcol">
					<h2 class="boxheader yellow ico ico-item ico-unlock">Bet Slip</h2>
					<ul class="nav nav-tabs yellow" role="tablist">
						<li class="active"><a href="#tab-betslip-single" role="tab" data-toggle="tab">Single</a></li>
						<li><a href="#tab-betslip-multi" role="tab" data-toggle="tab">Multi</a></li>
						<li><a href="#tab-betslip-system" role="tab" data-toggle="tab">System</a></li>
					</ul>
					<div class="tab-content yellow">
						<div id="tab-betslip-single" class="tab-pane fade in active no-padding">
							<ul class="betslip-bets">
								<li>
									<input type="text" value="150">
									<a href="#" class="updown betslip-up">+</a>
									<a href="#" class="updown betslip-down">-</a>
									<div class="bet-item">Dundee United – St Johnstone <span>8.50</span></div>
									<div class="bet-info">Match: 1<br>Win: 1255 Eur</div>
									<button type="button" class="close" data-dismiss="alert" title="Delete"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								</li>
								<li>
									<input type="text" value="150">
									<a href="#" class="updown betslip-up">+</a>
									<a href="#" class="updown betslip-down">-</a>
									<div class="bet-item">Dundee United – St Johnstone <span>8.50</span></div>
									<div class="bet-info">Match: 2<br>Win: 1255 Eur</div>
									<button type="button" class="close" data-dismiss="alert" title="Delete"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								</li>
							</ul>
							<div class="betslip-summary">
								<div class="form-group"><label>Vklad na sázku</label> <input type="text" value="50"></div>
								<div class="stake"><span>Vklad</span> <span class="value">100.00 EUR</span></div>
								<div class="possible"><span>Celková výhra</span> <span class="value">1 500.00 EUR</span></div>
							</div>
							<div class="betslip-buttons">
								<a href="#">Remove All</a>
								<a href="#" class="btn pull-right">Place Bet</a>
							</div>
						</div>
						<div id="tab-betslip-multi" class="tab-pane fade no-padding">
							<ul class="betslip-bets">
								<li>
									<div class="bet-item">Dundee United – St Johnstone <span>8.50</span></div>
									<div class="bet-info">Match: 1</div>
									<button type="button" class="close" data-dismiss="alert" title="Delete"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								</li>
								<li>
									<div class="bet-item">Dundee United – St Johnstone <span>8.50</span></div>
									<div class="bet-info">Match: 1</div>
									<button type="button" class="close" data-dismiss="alert" title="Delete"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								</li>
							</ul>
							<div class="betslip-summary">
								<div class="totalodds"><span>Celkový kurz</span> <span class="value">4.44</span></div>
								<div class="form-group">
									<a href="#" class="updown betslip-up">+</a>
									<a href="#" class="updown betslip-down">-</a>
									<label>Vklad</label> <input type="text" value="50">
								</div>
								<div class="possible"><span>Celková výhra</span> <span class="value">1 500.00 EUR</span></div>
							</div>
							<div class="betslip-buttons">
								<a href="#">Remove All</a>
								<a href="#" class="btn pull-right">Place Bet</a>
							</div>
						</div>
						<div id="tab-betslip-system" class="tab-pane fade no-padding">
							<ul class="betslip-bets">
								<li>
									<div class="bet-item">Dundee United – St Johnstone <span>8.50</span></div>
									<div class="bet-info">Match: 1</div>
									<button type="button" class="close" data-dismiss="alert" title="Delete"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								</li>
								<li>
									<div class="bet-item">Dundee United – St Johnstone <span>8.50</span></div>
									<div class="bet-info">Match: 1</div>
									<button type="button" class="close" data-dismiss="alert" title="Delete"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								</li>
							</ul>
							<div class="betslip-summary">
								<div>
									<label>Druh sázky</label>
									<select>
										<option>2 z 3</option>
										<option>3 z 4</option>
										<option>3 z 5</option>
									</select>
								</div>
								<div class="form-group"><label>Vklad na řádek</label> <input type="text" value="50"></div>
								<div class="form-group"><label>Celkový vklad</label> <input type="text" value="50"></div>
								<div class="possible"><span>Celková výhra</span> <span class="value">1 500.00 EUR</span></div>
							</div>
							<div class="betslip-buttons">
								<a href="#">Remove All</a>
								<a href="#" class="btn pull-right">Place Bet</a>
							</div>
						</div>
					</div>

					<h2 class="boxheader">Live Betting:</h2>
					<ul class="nav nav-tabs" role="tablist">
						<li class="active"><a href="#tab-playing-now" role="tab" data-toggle="tab">Playing Now</a></li>
						<li><a href="#tab-following" role="tab" data-toggle="tab">Following</a></li>
					</ul>
					<div class="tab-content">
						<div id="tab-playing-now" class="tab-pane fade in active contentbox no-padding">
							<h3 class="innerheader"><a href="#" class="ico ico-sport ico-soccer">Soccer</a></h3>
							<a href="#" class="topmatch match-soccer">
								World Championships – Men World
								<span class="matchdetail">Sweden <span>1:0</span> Slovakia</span>
								1<sup>st</sup> halftime – 24 min
							</a>
							<table>
								<tr><td colspan="3"><a href="#">FC Brasov — Universitatea Cluj</a></td></tr>
								<tr><td>0:0</td><td>2<sup>nd</sup> Half</td><td class="text-right">72 min</td></tr>
								<tr><td colspan="3"><a href="#">Dinamo Bucharest — Sageata</a></td></tr> 
								<tr><td>0:0</td><td>2<sup>nd</sup> Half</td><td class="text-right">72 min</td></tr>
								<tr><td colspan="3"><a href="#">Birkerød — SC Egedal</a></td></tr>
								<tr><td>0:0</td><td>2<sup>nd</sup> Half</td><td class="text-right">72 min</td></tr>
								<tr><td colspan="3"><a href="#">FC Brasov — Universitatea Cluj</a></td></tr>
								<tr><td>0:0</td><td>2<sup>nd</sup> Half</td><td class="text-right">72 min</td></tr>
								<tr><td colspan="3"><a href="#">Dinamo Bucharest — Sageata</a></td></tr>
								<tr><td>0:0</td><td>2<sup>nd</sup> Half</td><td class="text-right">72 min</td></tr>
								<tr><td colspan="3"><a href="#">Birkerød — SC Egedal</a></td></tr>
								<tr><td>0:0</td><td>2<sup>nd</sup> Half</td><td class="text-right">72 min</td></tr>
							</table>
							<h3 class="innerheader"><a href="#" class="ico ico-sport ico-icehockey">Ice Hockey</a></h3>
							<table>
								<tr><td colspan="3"><a href="#">FC Brasov — Universitatea Cluj</a></td></tr>
								<tr><td>0:0</td><td>2<sup>nd</sup> Half</td><td class="text-right">72 min</td></tr>
								<tr><td colspan="3"><a href="#">Dinamo Bucharest — Sageata</a></td></tr> 
								<tr><td>0:0</td><td>2<sup>nd</sup> Half</td><td class="text-right">72 min</td></tr>
								<tr><td colspan="3"><a href="#">Birkerød — SC Egedal</a></td></tr>
								<tr><td>0:0</td><td>2<sup>nd</sup> Half</td><td class="text-right">72 min</td></tr>
								<tr><td colspan="3"><a href="#">FC Brasov — Universitatea Cluj</a></td></tr>
								<tr><td>0:0</td><td>2<sup>nd</sup> Half</td><td class="text-right">72 min</td></tr>
							</table>
							<h3 class="innerheader collapsed"><a href="#" class="ico ico-sport ico-baseball">Baseball</a></h3>
						</div>
						<div id="tab-following" class="tab-pane fade contentbox no-padding">
							<h3 class="innerheader"><a href="#" class="ico ico-sport ico-tennis">Tennis</a></h3>
							<a href="#" class="topmatch match-tennis">
								World Championships – Men World
								<span class="matchdetail">Sweden <span>1:0</span> Slovakia</span>
								1<sup>st</sup> halftime – 24 min
							</a>
							<table>
								<tr><td colspan="2"><a href="#">FC Brasov — Universitatea Cluj</a></td></tr>
								<tr><td>15. 9. 2014</td><td class="text-right">15:22:33</td></tr>
								<tr><td colspan="2"><a href="#">Dinamo Bucharest — Sageata</a></td></tr> 
								<tr><td>15. 9. 2014</td><td class="text-right">15:22:33</td></tr>
								<tr><td colspan="2"><a href="#">Birkerød — SC Egedal</a></td></tr>
								<tr><td>15. 9. 2014</td><td class="text-right">15:22:33</td></tr>
								<tr><td colspan="2"><a href="#">FC Brasov — Universitatea Cluj</a></td></tr>
								<tr><td>15. 9. 2014</td><td class="text-right">15:22:33</td></tr>
								<tr><td colspan="2"><a href="#">Dinamo Bucharest — Sageata</a></td></tr>
								<tr><td>15. 9. 2014</td><td class="text-right">15:22:33</td></tr>
								<tr><td colspan="2"><a href="#">Birkerød — SC Egedal</a></td></tr>
								<tr><td>15. 9. 2014</td><td class="text-right">15:22:33</td></tr>
							</table>
						</div>
					</div>
					<div class="contentbox show-more"><a href="#" class="more">Show more live bets</a></div>
				</div>

			</div>
		</div>
	</main>
	
	<?php footer() ?>
