<div id="cell_index_data_<?= $i; ?>" class="cell_index_data cell_index_data_<?= $i; ?> valign-wrapper center-align white z-depth-1 p10" style="height: 100% !important; position: relative !important;">

	<?php 
	$cssCell = '';
	$cssTimer = 'style=display:none;';
	if(!empty($strGridCellDataArr)){
		$cssCell = 'style=display:none;';
		$cssTimer = '';
	}
	?>
		<input type="hidden" name="columns" id="columns_<?php echo $i; ?>" value="<?php echo $columns; ?>" />
		
		<?php if(!isset($blnFrontRequest)){?>
			<!-- ACTION SECTION -->
			<div <?php echo $cssCell; ?> class="reset_cell" id="reset_cell_<?php echo $i; ?>">
				<div class="fixed-action-btn-card feedTypeButtons_<?php echo $i; ?>" id="feedTypeButtons_<?php echo $i; ?>" style="width: 100% !important;">
					<div class="fixed-action-btn-card-container">
						<div style="float:left;margin-right:10px;" class="actionController">
							<a title="Click here to, configure the cell details" class="btn-floating btn-large teal lighten-5 event-edit"><i class="large material-icons">mode_edit</i></a>
						</div>
						<div style="float:left;margin-bottom:-14px;">
							<ul class="<?php if(!empty($cssCell)){ echo 'contentPreset';};?>" actionset="<?php echo $i;?>">
								<li><a title="Add custom text" class="btn-floating red modal-trigger custom_text_btn" href="#modal_custom_text" onclick="loadCustomeTextEntry(this, '<?php echo $strEventCodeEnc; ?>', <?php echo $i; ?>, 'modal_custom_text');" feed_id = "<?= !empty($cssCell) ? getEncyptionValue($strGridCellDataArr['id']) : '' ?>" feed_platform_id = "<?= !empty($cssCell) ? getEncyptionValue($strGridCellDataArr['platform_id']) : '' ?>"><i class="material-icons">title</i></a></li>
								<li><a title="Add custom image" class="btn-floating yellow darken-1 modal-trigger admin_image_btn"  href="#modal_admin_image" onclick="loadImageTextEntry(this, '<?php echo $strEventCodeEnc; ?>', <?php echo $i; ?>, 'modal_admin_image');" feed_id = "<?= !empty($cssCell) ? getEncyptionValue($strGridCellDataArr['id']) : '' ?>" feed_platform_id = "<?= !empty($cssCell) ? getEncyptionValue($strGridCellDataArr['platform_id']) : '' ?>"><i class="material-icons">image</i></a></li>
								<li><a title="Configure user / custom feeds" class="btn-floating green modal-trigger select_feed_btn" href="#modal_select_feed" onclick="loadFeedOfEvent(this, '<?php echo $strEventCodeEnc; ?>', <?php echo $i; ?>, 'modal_select_feed',3,'','user_feed');" feed_id = "<?= !empty($cssCell) ? getEncyptionValue($strGridCellDataArr['id']) : '' ?>" feed_platform_id = "<?= !empty($cssCell) ? getEncyptionValue($strGridCellDataArr['platform_id']) : '' ?>"><i class="material-icons">face</i></a></li>
								<li><a title="Configure Twitter handler(s) or handle(s)" class="btn-floating blue  modal-trigger select_twitter_feed_btn" href="#modal_select_twitter" onclick="loadFeedOfEvent(this, '<?php echo $strEventCodeEnc; ?>', <?php echo $i; ?>, 'modal_select_twitter',4,'','social_twitter');" feed_id = "<?= !empty($cssCell) ? getEncyptionValue($strGridCellDataArr['id']) : '' ?>" feed_platform_id = "<?= !empty($cssCell) ? getEncyptionValue($strGridCellDataArr['platform_id']) : '' ?>"><i class="material-icons">filter_drama</i></a></li>
							</ul>
						</div>
						
					</div> 

					<input type="hidden" name="platform" id="platform_<?php echo $i; ?>" value="" />
					<input type="hidden" name="refresh_timeout" id="refresh_timeout_<?php echo $i; ?>" value="" />
					<input type="hidden" name="refresh_timeout_type" id="refresh_timeout_type_<?php echo $i; ?>" value="" />
				</div>
			</div>
			<!-- ACTION SECTION -->
		<?php }?>
		
 		<?php if(!empty($cssCell)) : $jsFunctionName = "loadFeedOfEvent"; $jsModelName = "modal_custom_text"; $strPlateformType='admin_text';$sort='-1';?>
			<div class="reset_edit_btn" id="reset_edit_btn_<?php echo $i; ?>" style="width: 100% !important">
				<div class="dataContainer">
					<?php if($strGridCellDataArr['platform_id'] == 1): ?>
						<!-- Admin Text :  -->
						<div class="custom_text_size" id="feed_text_<?php echo $i; ?>"><?php echo $strGridCellDataArr['feed']; ?></div>
					<?php elseif ($strGridCellDataArr['platform_id'] == 2): $jsFunctionName = "loadFeedOfEvent"; $jsModelName = "modal_admin_image"; $strPlateformType='admin_image';$sort='-1';?>
						<!-- Admin Image -->
						<img src=<?php echo SITE_URL . $strGridCellDataArr['feed']; ?> width="250" class="responsive-img" />
					<?php elseif ($strGridCellDataArr['platform_id'] == 3): $jsFunctionName = "loadFeedOfEvent"; $jsModelName = "modal_select_feed"; $strPlateformType = 'user_feed';$sort='';?>
						<div>
							<!-- User Feed: -->
							<div class="feed_text_size flow-text"><?php echo $strGridCellDataArr['feed']; ?></div>
							<?php if(!empty($strGridCellDataArr['feed_cnt']) && $strGridCellDataArr['feed_cnt'] > 1): ?>
								<div>Total <strong><?php echo ($strGridCellDataArr['feed_cnt']); ?></strong> feeds are set.</div>
							<?php endif; ?>
						</div>
					<?php elseif ($strGridCellDataArr['platform_id'] == 4): $jsFunctionName = "loadFeedOfEvent"; $jsModelName = "modal_select_twitter"; $strPlateformType = 'social_twitter';$sort='';?>
						<div>
							<!-- Twitter Handle/ Hash Tag: -->
							<div style="word-wrap: break-word;"><?php echo !empty($strGridCellDataArr['data_type_desc']) && $strGridCellDataArr['data_type_desc']=='Hash' ? '#' : '@'; ?><?php echo $strGridCellDataArr['feed']; ?></div>
							<?php if(!empty($strGridCellDataArr['feed_cnt']) && $strGridCellDataArr['feed_cnt'] > 1): ?>
								<div>Total <strong><?php echo ($strGridCellDataArr['feed_cnt']); ?></strong> tags/handel are set.</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>

				<input type="hidden" name="feed_id_<?php echo $i; ?>" value="<?php echo getEncyptionValue($strGridCellDataArr['id']); ?>" />
				<input type="hidden" name="feed_platform_id_<?php echo $i; ?>" value="<?php echo getEncyptionValue($strGridCellDataArr['platform_id']); ?>" />
				
				<div class="fixed-action-btn-card 	fixed-action-btn-card-container">
					<a title="reset the cell to blank" onclick="resetCell(this, <?php echo $i; ?>, 'reset_cell_<?php echo $i; ?>');" class="btn-floating yellow" href="javascript:void(0);" feed_id = "<?= !empty($cssCell) ? getEncyptionValue($strGridCellDataArr['id']) : '' ?>" feed_platform_id = "<?= !empty($cssCell) ? getEncyptionValue($strGridCellDataArr['platform_id']) : '' ?>"><i class="large material-icons">autorenew</i></a>
					<a title="edit the configured cell details/ values" href="#<?php echo $jsModelName?>" onclick="<?php echo $jsFunctionName; ?>(this, '<?php echo $strEventCodeEnc; ?>', <?php echo $i; ?>, '<?php echo $jsModelName; ?>',<?php echo $strGridCellDataArr['platform_id']; ?>, '<?php echo $sort?>','<?php echo $strPlateformType?>');" class="btn-floating green modal-trigger" feed_id = "<?= !empty($cssCell) ? getEncyptionValue($strGridCellDataArr['id']) : '' ?>" feed_platform_id = "<?= !empty($cssCell) ? getEncyptionValue($strGridCellDataArr['platform_id']) : '' ?>"><i class="large material-icons">mode_edit</i></a>
					<a id="timer_btn_<?= $i ?>" title="set cell update frequency" <?php echo $cssTimer; ?> onclick="setTimerPlatform(this, <?php echo $i; ?>, 'modal_set_timer');" class="btn-floating red modal-trigger timer_btn" href="#modal_set_timer" refresh_timeout="<?= (!empty($strGridCellDataArr['refresh_timeout']) ? $strGridCellDataArr['refresh_timeout'] : '') ?>" timer="<?= (!empty($strGridCellDataArr['refresh_timeout']) ? ($strGridCellDataArr['refresh_timeout'] > 60 ? ($strGridCellDataArr['refresh_timeout']/60) : $strGridCellDataArr['refresh_timeout']) : '') ?>" timer_type = "<?= (!empty($strGridCellDataArr['refresh_timeout']) ? ($strGridCellDataArr['refresh_timeout'] > 60 ? 'min' : 'sec') : '') ?>" refresh_timeout="<?= (!empty($strGridCellDataArr['refresh_timeout']) ? ($strGridCellDataArr['refresh_timeout'] > 60 ? ($strGridCellDataArr['refresh_timeout']/60) : $strGridCellDataArr['refresh_timeout']) : '') ?>"><i class="material-icons">timer</i></a>
				</div>

			</div>
		<?php endif;?>
		<!-- <?php /* ?>
	</form>
	<?php */ ?> -->
</div>
