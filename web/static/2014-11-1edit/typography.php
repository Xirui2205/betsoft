<?php
include '_common.php';

head('Typography – YourLogo.com – A Pure Joy from Betting!');
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
						<li><a href="typography.php" class="active">Bonus</a></li>
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
					<div class="contentbox">
						<h1>Main Headline Caps Only!</h1>
						<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit nisl ut aliquip ex ea commodo consequat.</p>
						<h2 class="highlight">Headline H2</h2>
						<p>Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p>
						<h3>Headline H3</h3>
						<p>Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p>
						<h4>Headline H4</h4>
						<p>Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p>
						<img src="temp/article_photo.jpg" alt="Photo" class="img-left">
						<h4>Headline H4</h4>
						<p>Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p>
						<table class="table">
							<caption>Table Caption Headline</caption>
							<thead>
								<tr>
									<th>Header</th>
									<th>Header</th>
									<th>Header</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Duis autem vel eum iriurin hendrerit</td>
									<td>Neum iriurin hendrerit</td>
									<td>12-546</td>
								</tr>
								<tr>
									<td>Duis autem vel eum iriurin hendrerit</td>
									<td>Neum iriurin hendrerit</td>
									<td>12-546</td>
								</tr>
								<tr>
									<td>Duis autem vel eum iriurin hendrerit</td>
									<td>Neum iriurin hendrerit</td>
									<td>12-546</td>
								</tr>
							</tbody>
						</table>
						<h3>LISTS &amp; BULLETS</h3>
						<p>Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p>
						<ul class="list col-half">
							<li>Praesent vestibulum molestie lacus</li>
							<li>Fusce suscipit varius natoque</li>
							<li>Morbi nunc odio gravida at cursus penatibus magnis parturient</li>
						</ul>
						<ol class="list col-half">
							<li>Praesent vestibulum molestie lacus</li>
							<li>Fusce suscipit varius natoque</li>
							<li>Morbi nunc odio gravida at cursus penatibus magnis parturient</li>
						</ol>
						<p>
							<a href="#" class="btn clearfix">Button Design Normal</a>
							<a href="#" class="btn btn-big">Button Design Big</a>
						</p>
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h2>Well done!</h2> You successfully read this important alert message.
						</div>
						<div class="alert alert-info alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h2>Heads up!</h2>
							This alert needs your attention, but it's not super important.
						</div>
						<div class="alert alert-warning alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h2>Warning!</h2>
							Better check yourself, you're not looking too good.
						</div>
						<div class="alert alert-danger alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h2>Oh snap!</h2>
							Change a few things up and try submitting again.
						</div>
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
								<a href="#" class="btn">Place Bet</a>
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
								<a href="#" class="btn">Place Bet</a>
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
