<div class="row">
	<div class="col s12 m12">
		<table class="bordered highlight responsive-table">
			<thead>
				<tr>
					<th>System/Vertical</th>
					<th>Delivery Leader(SPOC)</th>
					<?php if((isset($strStatusArr['keyvalue'])) && (!empty($strStatusArr['keyvalue']))){?>
						<?php foreach($strStatusArr['keyvalue'] as $strStatusKey => $strStatusValues){?>
							<th class="center"><?php echo $strStatusValues?></th>
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
								$intCounter = 0;
								/* Iterating the status data array */
								foreach($strStatusArr['keyvalue'] as $strStatusKey => $strStatusValues){
									/* Display the dataset */
									if(isset($strSPCDataArr[$strStatusKey])){
										/* displaying the value */
										echo '<td class="centre">'.$strSPCDataArr[$strStatusKey].'</td>';
										/* row count variable */
										$intCounter+=$strSPCDataArr[$strStatusKey];
										/* checking status wise count index set */
										if(isset($strStatusWiseCountArr[$strStatusKey])){
											/* incrementing the status wise count */
											$strStatusWiseCountArr[$strStatusKey] += $strSPCDataArr[$strStatusKey];
										}else{
											/* Setting the status wise count */
											$strStatusWiseCountArr[$strStatusKey] = $strSPCDataArr[$strStatusKey];
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
						foreach($strStatusArr['keyvalue'] as $strStatusKey => $strStatusValues){
							/* if status column value is set the do needful */
							if(isset($strStatusWiseCountArr[$strStatusKey])){
								/* display the column value */
								echo '<td class="center">'.$strStatusWiseCountArr[$strStatusKey].'</td>';
							}else{
								/* display empty values */
								echo '<td class="center txt-gary">-</td>';
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