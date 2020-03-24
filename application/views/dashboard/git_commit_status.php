<div class="row">
	<div class="col s12 m12">
		<table class="bordered highlight responsive-table">
			<thead>
				<tr>
					<th>System/Vertical</th>
					<th>Delivery Leader(SPOC)</th>
					<?php if(!empty($displayDateArr)){?>
						<?php foreach($displayDateArr['display_date'] as $displayDateArrKey => $displayDateArrValues){?>
							<th class='center'><?php echo $displayDateArrValues?></th>
						<?php }?>
					<?php }?>
				</tr>
			</thead>
			<?php 
				if((isset($strVerticalArr)) && (!empty($strVerticalArr))){
					foreach($strVerticalArr as $strVerticalArrKey => $strVerticalArrValues){
						if(!isset($strVerticalArrValues['total_developer'])){
							unset($strVerticalArr[$strVerticalArrKey]);
						}
					}
				}
			?>
			<tbody>
				<?php 
					/* checking for delivery data */
					if(!empty($strDataArr)){
						/* iterating the vertical data array */
						foreach($strVerticalArr as $strVerticalArrKey => $strVerticalArrValue){
							echo '<tr>';
							echo '<td>'.$strVerticalArrValue['name'].'</td>';
							echo '<td>'.$strVerticalArrValue['user_name'].'</td>';
							foreach($displayDateArr['filter_date'] as $displayDateArrKey => $displayDateArrValues){
								if(isset($strDataArr[$strVerticalArrValue['vertical_code']]['commit_history'][$displayDateArrValues])){
									$intCommitPercentage	= (($strDataArr[$strVerticalArrValue['vertical_code']]['commit_history'][$displayDateArrValues]['commit_count'] * 100) / $strVerticalArrValue['total_developer']);
									$intTAT	= (((int)$intCommitPercentage >= 100 )? 0: ((($intCommitPercentage >= 40 ) && ($intCommitPercentage <= 99 )) ? -4:-6));
									echo '<td class="centre '.getDeliveryStatusColour($intTAT).'">'.numberFormating($intCommitPercentage).'%</td>';
								}else{
									echo '<td class="centre '.getDeliveryStatusColour(-6).'"></td>';
								}
							}
							echo '</tr>';
						}
					}
				?>
			</tbody>
		</table>
	</div>
</div>