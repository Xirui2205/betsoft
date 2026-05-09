<?php

class Models_BetInfo {


	public static function getGraph($bet, $betOdds) {
		require_once ("library/jpgraph/src/jpgraph.php");
		require_once ("library/jpgraph/src/jpgraph_line.php");
		require_once ("library/jpgraph/src/jpgraph_bar.php");

		$datax = array();
		$datay = array();
		$p = array();

		foreach($betOdds['rates'] as $rateOrder => $rate){
			$datax[] = It6_Date::fromDb($rate['validFrom']);
			foreach($rate['cols'] as $colId => $colRate) {
				$datay[$colId][] = $colRate;
			}
		}

		$datax[] = It6_Date::fromDb($bet['validToTime']);

		$graph = new Graph(930, 680, "auto");
		$graph->img->SetMargin(30, 30, 30, 130);
		$graph->SetScale("textlin");
		$graph->SetShadow();

		$graph->legend->Pos(0.09,0.00,"right","top");
		$graph->legend->SetFont(FF_FONT1,FS_NORMAL,7);

		$graph->xaxis->SetTickLabels($datax);
		$graph->xaxis->SetLabelAngle(90);
		$graph->xaxis->SetFont(FF_FONT1,FS_NORMAL,9);
		$graph->xaxis->SetColor('darkblue','black');

		$graph->yaxis->SetFont(FF_FONT1,FS_NORMAL,9);

		foreach($datay as $k=>$h){
			$h[] = $h[(count($h)-1)];

			$key = count($p);
			$color = "#".dechex(rand(0,256)).dechex(rand(0,256)).dechex(rand(0,256));
			 //"#".dechex(random(256)).dechex(random(256)).dechex(random(256));
			 // Create the first line
			$p[$key] = new LinePlot($h);
			$p[$key]->mark->SetType(MARK_FILLEDCIRCLE);
			$p[$key]->mark->SetFillColor($color);
			$p[$key]->mark->SetWidth(3);
			$p[$key]->SetColor($color);
			$p[$key]->SetCenter();
			//$p[$key]->SetLegend(mb_convert_encoding($betOdds['rates'][$k],"ISO-8859-2"));
			$p[$key]->value->SetFormat('%01.2f');
			$p[$key]->value->Show();
			$graph->Add($p[$key]);
		}

		// Output line
		$graph->Stroke(ROOT."admin/www/_graph/sazka_kurzy_".$bet['betId'].".png");
	}
}
