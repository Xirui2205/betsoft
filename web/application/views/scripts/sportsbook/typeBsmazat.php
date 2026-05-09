


<?php
$mainBetId = Zend_Controller_Front::getInstance()->getRequest()->getParam('id');
foreach($this->odds as $sport_id=>$data) {
	foreach($data as $udalost_id=>$dataS) {
		foreach($dataS['type'] as $typ_id=>$dataT) {
			foreach($dataT as $podtyp_id=>$dataPT) {
				//echo "<pre>";
				//print_r($dataPT);

				if( $dataPT['radek_sloupec'] == 0) {
					//continue;
?>
					<table>
						<thead>
							<tr>
								<th colspan="2"><?=$dataPT["text"]?></th>
								<?php foreach ($dataPT["sloupec"] as $key) { ?>
									<th class="text-center"><?=$key?></th>
								<?php } ?>
							</tr>
						</thead>
<?php
					foreach($dataPT["date"] as $datum=>$dataDate) {
						foreach($dataDate as $bet_id=>$dataOdds) {
							$array_diff = array_diff_key($dataPT['sloupec'], $dataOdds['sloupec']);
							foreach ($array_diff as $key => $value) {
								$array_diff[$key] = 1;
							}
							$dataOdds['sloupec'] = $dataOdds['sloupec'] + $array_diff;
							ksort($dataOdds['sloupec']);

							//echo "<pre>";
							//print_r($dataOdds);
?>
							<tbody>
								<tr>
									<td class="match-date"><?=$datum?><span><?=$dataOdds["hour"]?></span></td>
									<td class="text-left"><?=$dataOdds['text']?></td>

									<?php foreach ($dataOdds['sloupec'] as $sloupec_id => $odds): ++$i; ?>
									<td id="b<?=$bet_id; ?>c<?=$sloupec_id; ?>" class="text-center odds" >
										<?php if($odds != 1) { ?>
											<a
												class=""
												href="javascript:void(0);"
												onclick="ticketUpdate(<?=$bet_id .','. $sloupec_id .',\''. $this->EscapeJs($dataOdds['text'], true, true) .'\',\''. $this->EscapeJs($this->Adata['fullText'], true, true) .'\',\''. $this->Adata['sloupec'][$sloupec_id] .'\',null,'.  $dataOdds['simple']; ?>);"
											><?=$odds?></a>
										<?php } else { ?>
											-
										<?php } ?>
									</td>
									<?php endforeach?>
								</tr>
							</tbody>
						</table>


<?php
						}
					}
				} else { 
?>
					<table>
						<thead>
							<tr>
								<th colspan="2"><?=$dataPT["text"]?></th>
								<?php foreach ($dataPT["sloupec"] as $key) { ?>
									<th class="text-center"><?=$key?></th>
								<?php } ?>
							</tr>
						</thead>
<?php
					foreach($dataPT["date"] as $datum=>$dataDate) {
						foreach($dataDate as $bet_id=>$dataOdds) {
							$dataOddsCount = count($dataOdds['sloupec']);
							$sl_pocet = ceil($dataOddsCount/$dataPT['sloupec_pocet_max']);
							$linesCount = ceil(($dataOddsCount) / $sl_pocet);
							$fillingCellsCount = ($sl_pocet - ($dataOddsCount % $sl_pocet)) % $sl_pocet;

						}
					}
?>
						<tbody>
							<tr>
								<td class="match-date" rowspan="3"><?=$datum?><span><?=$dataOdds["hour"]?></span></span></td>
								<td rowspan="4">Malaga – Lavante</td>
								<td class="text-center"><a href="#"><?=$this->escape($this->Adata['sloupec'][$sl_id]); ?></a></td>
							</tr>
							<tr>
								<?php foreach($dataOdds['sloupec'] as $sl_id=>$odds) { ?>
								<th class="text-center"><?$dataPT["sloupec"][$sl_id]?></th>
								<?php } ?>
							</tr>
							<tr>
								<?php foreach($dataOdds['sloupec'] as $sl_id=>$odds) { ?>
								<td class="text-center"><a href="#"><?=$odds?></a></td>
								<?php } ?>
							</tr>
						</tbody>
					</table>


<?php
					foreach($dataPT["date"] as $datum=>$dataDate) {
						foreach($dataDate as $bet_id=>$dataOdds) {
							$dataOddsCount = count($dataOdds['sloupec']);
							$sl_pocet = ceil($dataOddsCount/$dataPT['sloupec_pocet_max']);
							$linesCount = ceil(($dataOddsCount) / $sl_pocet);
							$fillingCellsCount = ($sl_pocet - ($dataOddsCount % $sl_pocet)) % $sl_pocet;
							/*echo "dataOddsCount:".$dataOddsCount;
							echo " sl_pocet:".$sl_pocet;
							echo " fillingCellsCount:".$fillingCellsCount;
							echo " linesCount:".$linesCount;*/
						}
					}
?>
		<table class="betOfferTable betOfferTypeResultsTable" cellspacing="0">
			<tbody>
				<?php $sl_help = 0?>
				<?php $sl_last = true; $first = true; $currentLine = 1; $currentCell = 0; ?>				
				<?php foreach($dataOdds['sloupec'] as $sl_id=>$odds) : $currentCell++; ?>
				
					<?php if ($sl_last) : ?>
						<tr<?=$first?' class="first"':''?>>
							<td class="alias borderRight"></td>
							<td class="leftPaddingCols<?= $sl_pocet ?> borderLeft"></td>
					<?php endif?>
							
					<?php $sl_last = (0 == ++$sl_help % $sl_pocet)
							|| $currentCell === $dataOddsCount?>
							
					<td class="resultB">
						<strong><?=$this->escape($this->Adata['sloupec'][$sl_id]); ?></strong>
					</td>
					<td id="b<?=$bet_id?>c<?=$sl_id?>" class="rateB">
						<?php if($odds != 1) : echo $dataPT["sloupec"][$sl_id];?>
							<a
								class="kurz"
								href="javascript:void(0);"
								onclick="ticketUpdate(<?=$bet_id .','. $sl_id .',\''. $this->EscapeJs($dataOdds['text'], true, true) .'\',\''. $this->EscapeJs($this->Adata['fullText'], true, true) .'\',\''. $this->Adata['sloupec'][$sl_id] .'\',null,'.	$dataOdds['simple']; ?>);"
							>
								<?=$odds?>
							</a>
						<?php else : ?>
							-
						<?php endif?>
					</td>
					
					<?php 
					if ($sl_last) {
					if ($currentLine == $linesCount) {
						for ($i = 0; $i < $fillingCellsCount; $i++) :
							?><td class="resultB"></td><td class="rateB"></td>
							<?php
						endfor;
					}
					$currentLine++;
					?>
					<td class="datetime datetimeB borderLeft borderRight"></td>
					<td class="plus borderLeft borderRight"></td>
					<td class="stats borderLeft"></td>
					</tr>
					<?php } ?>
				<?php $first = false; endforeach?>
			</tbody>
		</table>
<?php	
				}
			}
		}
	};
};
?>