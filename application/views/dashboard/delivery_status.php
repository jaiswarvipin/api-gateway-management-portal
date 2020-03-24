<div class="row">
	<div class="col s12 m12">
		<table class="bordered highlight responsive-table">
			<thead>
				<tr>
					<th>System/Vertical</th>
					<th>Delivery Leader(SPOC)</th>
					<?php if((isset($strStatusArr)) && (!empty($strStatusArr))){?>
						<?php foreach($strStatusArr as $strStatusKey => $strStatusValues){?>
							<th class='center'><?php echo strtoupper(str_replace('_',' ',$strStatusKey))?></th>
						<?php }?>
					<?php }?>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>
				<?php 
					/* checking for delivery data */
					if(!empty($strDataArr)){
						/* variable initialization */
						$strStatusWiseCountArr = array();
						/* iterating the vertical data array */
						foreach($strDataArr as $strVerticalKey => $strVerticalDataArr){
							/* iterating the delivery spoc data array */
							foreach($strVerticalDataArr as $strSPOCDataArr => $strSPCDataArr){
								echo '<tr>';
								echo '<td>'.$strVerticalKey.'</td>';
								echo '<td>'.$strSPOCDataArr.'</td>';
								/* variable initialization */
								$intCounter = $intTAT = 0;
								/* Iterating the status data array */
								foreach($strStatusArr as $strStatusKey => $strStatusValuesArr){
									/* Variable initialization */
									$intCellTotal	= 0;
									/* iterating the status loop */
									foreach($strStatusValuesArr as $strStatusValuesArrKey => $strStatusIndex){
										/* Display the dataset */
										if(isset($strSPCDataArr[$strStatusIndex])){
											/* set the value */
											$intCellTotal	+= $strSPCDataArr[$strStatusIndex]['count'];
											$intTAT			+= $strSPCDataArr[$strStatusIndex]['tat'];
											/* row count variable */
											$intCounter+=$strSPCDataArr[$strStatusIndex]['count'];
										}
									}
									/* if value is not found the do needful */
									if($intCellTotal > 0){
										/* displaying the value */
										echo '<td class="centre '.getDeliveryStatusColour($intTAT).'">'.$intCellTotal.'</td>';
										
										/* checking status wise count index set */
										if(isset($strStatusWiseCountArr[$strStatusKey])){
											/* incrementing the status wise count */
											$strStatusWiseCountArr[$strStatusKey] += $intCellTotal;
										}else{
											/* Setting the status wise count */
											$strStatusWiseCountArr[$strStatusKey] = $intCellTotal;
										}
									}else{
										/* display empty value */
										echo '<td class="txt-gary center">-</td>';
									}
								}
								if(isset($strStatusWiseCountArr['total'])){
									$strStatusWiseCountArr['total'] +=$intCounter;
								}else{
									$strStatusWiseCountArr['total']	= $intCounter;
								}
								echo '<td class="bg-dark-gray-level-2 center">'.$intCounter.'</td>';
								echo '</tr>';
							}
						}
						/* displaying the finall summary row */
						echo '<tr class="bg-dark-gray">';
						echo '<td colspan="2">Total</td>';
						/* Iterating the status data array */
						foreach($strStatusArr as $strStatusKey => $strStatusValues){
							/* if status column value is set the do needful */
							if(isset($strStatusWiseCountArr[$strStatusKey])){
								/* display the column value */
								echo '<td class="center">'.$strStatusWiseCountArr[$strStatusKey].'</td>';
							}else{
								/* display empty values */
								echo '<td class="txt-gary center">-</td>';
							}
						}
						/* dislay the grand total */
						echo '<td class="center">'.$strStatusWiseCountArr['total'].'</td>';
						echo '</tr>';
					}
				?>
			</tbody>
		</table>
	</div>
</div>