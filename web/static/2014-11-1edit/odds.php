<?php
include '_common.php';

head('Odds – YourLogo.com – A Pure Joy from Betting!');
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
									<a href="odds.php" class="ico ico-sport ico-soccer">Premier League <span class="count">145</span></a>
									<ul class="no-bg">
										<li><a href="odds.php" class="ico ico-item ico-star active">Barclays Premier <span class="count">145</span></a></li>
										<li><a href="odds.php" class="ico ico-item ico-star">CocaCola League <span class="count">145</span></a></li>
									</ul>
								</li>
								<li>
									<a href="odds.php" class="ico ico-sport ico-tennis">ATP500 <span class="count">145</span></a>
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

					<h2 class="boxheader">Sportbook Odds</h2>
					<div class="filter filter-cols-4">
						<form class="form-inline">
							<div class="checkbox"><label><input type="checkbox"> Match result</label></div>
							<div class="checkbox"><label><input type="checkbox"> Halftime result</label></div>
							<div class="checkbox"><label><input type="checkbox"> Double chance</label></div>
							<div class="checkbox"><label><input type="checkbox"> Handicap</label></div>
							<div class="checkbox"><label><input type="checkbox"> Halftime result</label></div>
							<div class="checkbox"><label><input type="checkbox"> Double chance</label></div>
							<div class="checkbox"><label><input type="checkbox"> Handicap</label></div>
							<div class="checkbox"><label><input type="checkbox"> Match result</label></div>
						</form>
						<a href="#" class="filter-hide">Hide Filter</a>
					</div>

					<h2 class="boxheader ico ico-flag flag-big ico-cze"><div class="ico ico-sport ico-soccer">Fotbal – <span class="text-normal">Česká republika – Synot Liga</span></div></h2>
					<div class="contentbox no-padding">
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
									<td class="match-icos">
										<strong>Real Madrid - Espanyol</strong>
										<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
										<span class="ico-self ico-tv" title="TV" data-toggle="tooltip" data-placement="top"></span>
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
									<td class="match-date">16. kvě<br>18:00</td>
									<td class="match-icos">
										<strong>FC Barcelona - Granada</strong>
										<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
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
									<td class="match-date">16. kvě<br>18:00</td>
									<td class="match-icos">
										<strong>Malaga - Lavente</strong>
										<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
									</td>
									<td class="odds"><a href="#">1.33</a></td>
									<td class="odds"><a href="#">3.50</a></td>
									<td class="odds"><a href="#">8.50</a></td>
									<td class="odds"><a href="#" class="selected">8.50</a></td>
									<td class="odds"><a href="#">8.50</a></td>
									<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
									<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
								</tr>
								<tr>
									<td class="match-date">16. kvě<br>18:00</td>
									<td class="match-icos">
										<strong>Manchester United - Nottingham</strong>
										<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
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
									<td class="match-date">16. kvě<br>18:00</td>
									<td class="match-icos">
										<strong>Chelsea - Southampton</strong>
										<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
									</td>
									<td class="odds"><a href="#">1.33</a></td>
									<td class="odds"><a href="#">3.50</a></td>
									<td class="odds"><a href="#">8.50</a></td>
									<td class="odds"><a href="#">8.50</a></td>
									<td class="odds"><a href="#">8.50</a></td>
									<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
									<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
								</tr>
							</tbody>
						</table>
					</div>

					<h2 class="boxheader ico ico-flag flag-big ico-bra"><div class="ico ico-sport ico-tennis">Tenis – <span class="text-normal">Brazílie – Synot Liga</span></div></h2>
					<div class="contentbox no-padding">
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
									<td class="match-icos">
										<strong>Real Madrid - Espanyol</strong>
										<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
										<span class="ico-self ico-tv" title="TV" data-toggle="tooltip" data-placement="top"></span>
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
									<td class="match-date">16. kvě<br>18:00</td>
									<td class="match-icos">
										<strong>FC Barcelona - Granada</strong>
										<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
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
									<td class="match-date">16. kvě<br>18:00</td>
									<td class="match-icos">
										<strong>Malaga - Lavente</strong>
										<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
									</td>
									<td class="odds"><a href="#">1.33</a></td>
									<td class="odds"><a href="#">3.50</a></td>
									<td class="odds"><a href="#">8.50</a></td>
									<td class="odds"><a href="#" class="selected">8.50</a></td>
									<td class="odds"><a href="#">8.50</a></td>
									<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
									<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
								</tr>
								<tr class="more-bets-content">
									<td colspan="10">
										<div>
											<table>
												<thead>
													<tr>
														<th colspan="2">Halftime Winner</th>
														<th class="text-center">1</th>
														<th class="text-center">1X</th>
														<th class="text-center">0</th>
														<th class="text-center">2X</th>
														<th class="text-center">2</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td class="match-date">16. kvě <span>18:00</span></td>
														<td>Malaga – Lavante</td>
														<td class="text-center"><a href="#" class="selected">1.33</a></td>
														<td class="text-center"><a href="#">3.50</a></td>
														<td class="text-center"><a href="#">8.50</a></td>
														<td class="text-center"><a href="#">1.33</a></td>
														<td class="text-center"><a href="#">3.50</a></td>
													</tr>
													</tr>
												</tbody>
											</table>
											<table>
												<thead>
													<tr>
														<th colspan="2">Second Halftime Winner</th>
														<th class="text-center">1</th>
														<th class="text-center">1X</th>
														<th class="text-center">0</th>
														<th class="text-center">2X</th>
														<th class="text-center">2</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td class="match-date">16. kvě <span>18:00</span></td>
														<td>Malaga – Lavante</td>
														<td class="text-center"><a href="#">1.33</a></td>
														<td class="text-center"><a href="#">3.50</a></td>
														<td class="text-center"><a href="#" class="selected">8.50</a></td>
														<td class="text-center"><a href="#">1.33</a></td>
														<td class="text-center"><a href="#">3.50</a></td>
													</tr>
													</tr>
												</tbody>
											</table>
											<table>
												<thead>
													<tr>
														<th colspan="2">Second Halftime Winner</th>
														<th class="text-center">1</th>
														<th class="text-center">1X</th>
														<th class="text-center">0</th>
														<th class="text-center">2X</th>
														<th class="text-center">2</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td class="match-date">16. kvě <span>18:00</span></td>
														<td>Malaga – Lavante</td>
														<td class="text-center"><a href="#">1.33</a></td>
														<td class="text-center"><a href="#">3.50</a></td>
														<td class="text-center"><a href="#">8.50</a></td>
														<td class="text-center"><a href="#">1.33</a></td>
														<td class="text-center"><a href="#">3.50</a></td>
													</tr>
													</tr>
												</tbody>
											</table>
											<table>
												<thead>
													<tr>
														<th colspan="2">Second Halftime Winner</th>
														<th class="text-center">1</th>
														<th class="text-center">1X</th>
														<th class="text-center">0</th>
														<th class="text-center">2X</th>
														<th class="text-center">2</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td class="match-date">16. kvě <span>18:00</span></td>
														<td>Malaga – Lavante</td>
														<td class="text-center"><a href="#">1.33</a></td>
														<td class="text-center"><a href="#">3.50</a></td>
														<td class="text-center"><a href="#">8.50</a></td>
														<td class="text-center"><a href="#">1.33</a></td>
														<td class="text-center"><a href="#">3.50</a></td>
													</tr>
													</tr>
												</tbody>
											</table>
											<table>
												<thead>
													<tr>
														<th colspan="2">Second Halftime Winner</th>
														<th class="text-center">1</th>
														<th class="text-center">1X</th>
														<th class="text-center">0</th>
														<th class="text-center">2X</th>
														<th class="text-center">2</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td class="match-date">16. kvě <span>18:00</span></td>
														<td>Malaga – Lavante</td>
														<td class="text-center"><a href="#">1.33</a></td>
														<td class="text-center"><a href="#">3.50</a></td>
														<td class="text-center"><a href="#">8.50</a></td>
														<td class="text-center"><a href="#">1.33</a></td>
														<td class="text-center"><a href="#">3.50</a></td>
													</tr>
													</tr>
												</tbody>
											</table>
										</div>
									</td>
								</tr>
								<tr>
									<td class="match-date">16. kvě<br>18:00</td>
									<td class="match-icos">
										<strong>Manchester United - Nottingham</strong>
										<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
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
									<td class="match-date">16. kvě<br>18:00</td>
									<td class="match-icos">
										<strong>Chelsea - Southampton</strong>
										<span class="ico-self ico-info" title="Informace" data-toggle="tooltip" data-placement="top"></span>
									</td>
									<td class="odds"><a href="#">1.33</a></td>
									<td class="odds"><a href="#">3.50</a></td>
									<td class="odds"><a href="#">8.50</a></td>
									<td class="odds"><a href="#">8.50</a></td>
									<td class="odds"><a href="#">8.50</a></td>
									<td class="text-center"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></td>
									<td class="text-right"><a href="#" class="more-bets" title="Více sázek" data-toggle="tooltip" data-placement="top"><span>+</span>48</a></td>
								</tr>
							</tbody>
						</table>
					</div>

					<h2 class="boxheader ico ico-flag flag-big ico-eng">Basketbal – <span class="text-normal">Anglie – Synot Liga</span></h2>
					<div class="contentbox no-padding">
						<div class="odds-big-info">
							<div class="odds-type">Přesný výsledek</div>
							<div class="odds-match"><strong>Sparta – Slavia</strong></div>
							<div class="match-date">18. kvě <span>18:00</span></div>
							<div class="odds-ico"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></div>
						</div>
						<table class="table table-striped table-hover matches odds-big">
							<tbody>
								<tr>
									<td class="odds"><a href="#"><span>1:0</span> <span>1.33</span></a></td>
									<td class="odds"><a href="#"><span>1:0</span> <span>1.33</span></a></td>
									<td class="odds"><a href="#" class="selected"><span>1:0</span> <span>1.33</span></a></td>
									<td class="odds"><a href="#"><span>1:0</span> <span>1.33</span></a></td>
									<td class="odds"><a href="#"><span>1:0</span> <span>1.33</span></a></td>
									<td class="odds"><a href="#"><span>1:0</span> <span>1.33</span></a></td>
								</tr>
								<tr>
									<td class="odds"><a href="#"><span>1:0</span> <span>1.33</span></a></td>
									<td class="odds"><a href="#"><span>1:0</span> <span>1.33</span></a></td>
									<td class="odds"><a href="#"><span>1:0</span> <span>1.33</span></a></td>
									<td class="odds"><a href="#"><span>1:0</span> <span>1.33</span></a></td>
									<td colspan="2"></td>
								</tr>
							</tbody>
						</table>

						<div class="odds-big-info">
							<div class="odds-type">Přesný výsledek</div>
							<div class="odds-match"><strong>Sparta – Slavia</strong> <a href="#" class="ico-self ico-live" title="Sledovat Live!" data-toggle="tooltip" data-placement="top"></a></div>
							<div class="match-date">18. kvě <span>18:00</span></div>
							<div class="odds-ico"><a href="#" class="ico-self ico-stats" title="Statistiky" data-toggle="tooltip" data-placement="top"></a></div>
						</div>
						<table class="table table-striped table-hover matches odds-big">
							<tbody>
								<tr>
									<td class="odds"><a href="#"><span>1:0</span> <span>1.33</span></a></td>
									<td class="odds"><a href="#"><span>1:0</span> <span>1.33</span></a></td>
									<td class="odds"><a href="#"><span>1:0</span> <span>1.33</span></a></td>
									<td class="odds"><a href="#"><span>1:0</span> <span>1.33</span></a></td>
									<td class="odds"><a href="#"><span>1:0</span> <span>1.33</span></a></td>
									<td class="odds"><a href="#"><span>1:0</span> <span>1.33</span></a></td>
								</tr>
								<tr>
									<td class="odds"><a href="#"><span>1:0</span> <span>1.33</span></a></td>
									<td class="odds"><a href="#" class="selected"><span>1:0</span> <span>1.33</span></a></td>
									<td class="odds"><a href="#"><span>1:0</span> <span>1.33</span></a></td>
									<td class="odds"><a href="#"><span>1:0</span> <span>1.33</span></a></td>
									<td colspan="2"></td>
								</tr>
							</tbody>
						</table>
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
							<div class="alert alert-success alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<h2>Well done!</h2> You successfully read this important alert message.
							</div>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
						</div>
						<div id="tab-betslip-multi" class="tab-pane fade no-padding">
							<ul class="betslip-bets">
								<li>
									<div class="bet-item">Dundee United – St Johnstone <span>8.50</span></div>
									<div class="bet-info">Match: 1</div>
									<div class="alert alert-danger" role="alert">Change a few things up and try submitting again.</div>
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
