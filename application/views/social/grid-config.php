<style>
	<!--
	.feed_text_size{
		font-size: <?php echo $strUserAndCustomTextFeedCharLimitArr['user_feed']['font_size']; ?>;
		width: 100%;
		word-wrap: break-word;
		word-break: break-all;
	}

	.custom_text_size{
		font-size: <?php echo $strUserAndCustomTextFeedCharLimitArr['custom_text']['font_size']; ?>;
		width: 100%;
	}
	.dataContainer{
		vertical-align: text-top !important;
	}
	q {
	  quotes: "“" "”" "‘" "’";
	}
	q:before {
		content: open-quote;
	}
	q:after {
		content: close-quote;
	}
	-->
</style>

<?php if(!isset($blnFrontRequest)){?>
	<a title="Live Feed" class="modal-trigger waves-effect waves-light btn display_alert_btn right" href="#modal_select_feed" onclick="loadFeedOfEvent(this, '<?php echo getEncyptionValue($gridConfig[0]['id']); ?>', 0, 'modal_select_feed');" style="margin-top: -60px;">Live Feeds</a>
<?php }else{?>
	<div class="row"><div class="col s12"></div></div>
<?php }?>
<?php 
	/* Checking got configuration array */
	if(empty($gridConfig)){
		echo "<div class='center mt10'><h5>Looks Grid Matrix is not configured with Event.</h5></div>";
	}else{
		/* Variable initialization */
		$intRows		= isset($gridConfig[0]['rows'])?$gridConfig[0]['rows']:0;
		$intColumns		= isset($gridConfig[0]['rows'])?$gridConfig[0]['columns']:0;
		$intColumnSpan	= ((int)$intColumns > 12)?1:(round(12/$intColumns));
		$intCellCounter			= 0;
		//$eventCode 				= $gridConfig[0]['id'];
		$strEventCodeEnc = getEncyptionValue($gridConfig[0]['id']);		
		$strGridCode 	= getEncyptionValue($gridConfig[0]['grid_id']);		
		
		for($intCounterForRow = 0; $intCounterForRow < $intRows; $intCounterForRow++){?>
			<div class="row flex row_<?php echo ($intCounterForRow + 1) ?>" id="row_<?php echo ($intCounterForRow + 1) ?>">
				<?php for($intCounterForCol = 0; $intCounterForCol < $intColumns; $intCounterForCol++){
						/* increment the cell index */
						$intCellCounter++;?>
					<div class="box_<?php echo ($intCounterForCol + 1); ?> col s<?php echo $intColumnSpan; ?> m<?php echo $intColumnSpan; ?> l<?php echo $intColumnSpan; ?> divGridBoxs" id="col_<?php echo ($intCellCounter); ?>" style="height: 300px;">
						<?php 
							$dataViewArr = array();
							$dataViewArr['i'] 					= $intCellCounter;
							$dataViewArr['columns'] 			= $intColumns;
							$dataViewArr['strEventCodeEnc']	 	= $strEventCodeEnc;
							$dataViewArr['strGridCellDataArr']	= (!empty($strGridCellDataArr) && (isset($strGridCellDataArr[$intCellCounter]))) ? $strGridCellDataArr[$intCellCounter] : array();
							$this->view('social/grid-config-cell', $dataViewArr);
						?>
					</div>
				<?php }?>
			</div>
	<?php }?>
	<?php if(!isset($blnFrontRequest)){?>
		<!-- Begin, Event Display URL, Display Alert and Total Grid -->
		<div class="row">
			<div class="col l4 m4 s4">
				<?php if(!empty($eventUrl)): ?>
					<a class="waves-effect waves-light btn" target="_blank" title="Public Event wall view" href="<?= $eventUrl ?>">View Event Wall <i class="material-icons right">laptop_windows</i></a>
				<?php endif; ?>
			</div>
			<div class="col l4 m4 s4 center">
				<a id="display_alert_btn" title="Display Event Blocker" class="waves-effect waves-light btn	" href="<?= SITE_URL ?>social-wall/load-alert-msg-by-event-code/<?= $strEventCodeEnc ?>">Display Alter <i class="material-icons right">add_alert</i></a>
			</div>
			<div class="col l4 m4 s4">
				<?php if(!empty($intRows) && !empty($intColumns)): ?>
					<a id="display_alert_btn" title="Number of grids cell" class="waves-effect waves-light btn right" href="javascript:void(0);"><?php echo (int)($intRows*$intColumns) ?> Of Tiles <i class="material-icons right">grid_on</i></a>
				<?php endif; ?>
			</div>
		</div>
	<?php }else{?>
			<div class="row align-wrapper">
				<div class="col l2 m2 s2">
					<a class="waves-effect waves-light blue-text btn-flat f30" target="_blank" title="Public Event wall view" href="javascript:void(0);"><?php echo strtoupper($strEventCode)?></a>
				</div>
				<div class="col l8 m8 s8">
					<center><span class='center-align center'>Visit <strong><?php echo $_SERVER['SERVER_NAME']?></strong> or go to <strong><?php echo str_replace(array('http://','https://','/'),array(''),SITE_URL)?></strong> and enter your code</span></center>
				</div>
				<div class="col l2 m2 s2">
					<div class="row powerBy">
						<div class="col l6 m6 s6 right-align">POWERED BY </div>
						<div class="col l6 m6 s6 left-align nml20"><img src="<?php echo SITE_URL?>uploads/company/pa_logo.png" class="responsive-img" width="100px"></div>
					</div>
				</div>
			</div>
	<?php }?>
<?php }?>

<?php if(!isset($blnFrontRequest)){?>
	<div id="modal-set" class="modal-set">
		<!-- USER FEED SELECTIONS -->
		<div id="modal_select_feed" class="modal modal_select_feed modal modal-fixed-footer" style="width: 90% !important;height: 100% !important;">
			<form action="<?php echo SITE_URL?>social-wall/add-update-grid-cell/<?= $strEventCodeEnc ?>/" id="frmGridConfig_user_feed" name="frmGridConfig_user_feed" method="post" >
				<div class="modal-content">
					<h4><span>Select Feed for cell - <span class="cell_number" id="modal_feed_cell_id"></span></h4>
					<div class="feed"></div>

					<input type="hidden" name="cellindex" id="cellindex" value="" />
					<input type="hidden" name="platform" id="platform" value="user_feed" />
					<input type="hidden" name="refresh_timeout" id="refresh_timeout_user_feed" value="" />
					<input type="hidden" name="refresh_timeout_type" id="refresh_timeout_type_user_feed" value="" />
					<input type="hidden" name="feed_id" value="" />
					<input type="hidden" name="feed_platform_id" value="" />
				</div>
				<div class="modal-footer">
					<a href="javascript:void(0);" class="modal-close waves-effect waves-green btn-flat">Cancel</a>
					<button class="btn waves-effect waves-light cmsAddUpdateGridBox" type="submit" name="cmsAddUpdateGridBox" modal_id="modal_select_feed" cellindex = "" id="cmsAddUpdateGridBox_user_feed" formName="frmGridConfig_user_feed"  formId="frmGridConfig_user_feed" >Submit<i class="material-icons right">send</i></button>
				</div>
			</form>
		</div>
		<!-- USER FEED SELECTIONS -->
		
		<!-- TWITTER HANDELLER SELECTIONS -->
		<div id="modal_select_twitter" class="modal modal_select_twitter modal-fixed-footer">
			<form action="<?php echo SITE_URL?>social-wall/add-update-grid-cell/<?= $strEventCodeEnc ?>/" id="frmGridConfig_twitter_feed" name="frmGridConfig_twitter_feed" method="post" >
				<div class="modal-content">
					<h4><span>Select Twitter handle's/ Hash tag's for cell - <span class="cell_number" id="modal_twitter_feed_cell_id"></span></h4>
					<div class="feed"></div>
					<!--input type="hidden" name="cellindex" id="cellindex_twitter_feed" value="" />
					<input type="hidden" name="platform" id="platform_twitter_feed" value="social_twitter" /-->
					<input type="hidden" name="cellindex" id="cellindex" value="" />
					<input type="hidden" name="platform" id="platform" value="social_twitter" />
					<input type="hidden" name="refresh_timeout" id="refresh_timeout_twitter_feed" value="" />
					<input type="hidden" name="refresh_timeout_type" id="refresh_timeout_type_twitter_feed" value="" />
					<input type="hidden" name="feed_id" value="" />
					<input type="hidden" name="feed_platform_id" value="" />
				</div>
				<div class="modal-footer">
					<a href="javascript:void(0);" class="modal-close waves-effect waves-green btn-flat">Cancel</a>
					<button class="btn waves-effect waves-light cmsAddUpdateGridBox" type="submit" name="cmsAddUpdateGridBox" modal_id="modal_select_twitter" cellindex = "" id="cmsAddUpdateGridBox_twitter_feed" formName="frmGridConfig_twitter_feed" formId="frmGridConfig_twitter_feed" >Submit<i class="material-icons right">send</i></button>
				</div>
			</form>
		</div>
		<!-- TWITTER HANDELLER SELECTIONS -->


		<!-- CUSTOM CONTENT -->
		<div id="modal_custom_text" class="modal modal_custom_text modal-fixed-footer" style="width: 50% !important;">
			<form action="<?php echo SITE_URL?>social-wall/add-update-grid-cell/<?= $strEventCodeEnc ?>/" id="frmGridConfig_custom_text" name="frmGridConfig_custom_text" method="post" >
				<div class="modal-content">
					<h4><span>Enter Custom Content for cell - <span class="cell_number" id="modal_custom_text_cell_id"></span></h4>
					<div class="row">
						<div class="input-field col s12">
							<div class="custom_text">
								<div class="custom_text_box" id="custom_text_box">
									<textarea name="custom_text" id="custom_text" class="materialize-textarea materialize-textarea-data-length validate" placeholder="Custom Text" data-length="<?php echo $strUserAndCustomTextFeedCharLimitArr['custom_text']['char']; ?>" maxlength="<?php echo $strUserAndCustomTextFeedCharLimitArr['custom_text']['char']; ?>"></textarea>
									<label for="custom_text" data-error="wrong" data-success="right" data-error="Please enter Custom Text." >Custom Text</label>
								</div>
							</div>
						</div>
					</div>
					<input type="hidden" name="cellindex" id="cellindex" value="" />
					<input type="hidden" name="platform" id="platform" value="admin_text" />
					<!--input type="hidden" name="cellindex" id="cellindex_custom_text" value="" />
					<input type="hidden" name="platform" id="platform_custom_text" value="admin_text" /-->
					<input type="hidden" name="refresh_timeout" id="refresh_timeout_custom_text" value="" />
					<input type="hidden" name="refresh_timeout_type" id="refresh_timeout_type_custom_text" value="" />
					<input type="hidden" name="feed_id" value="" />
					<input type="hidden" name="feed_platform_id" value="" />
				</div>
				<div class="modal-footer">
					<a href="javascript:void(0);" class="modal-close waves-effect waves-green btn-flat">Cancel</a>
					<button class="btn waves-effect waves-light cmsAddUpdateGridBox" type="submit" name="cmsAddUpdateGridBox" modal_id="modal_custom_text" cellindex = "" id="cmsAddUpdateGridBox_custom_text" formName="frmGridConfig_custom_text"  formId="frmGridConfig_custom_text" >Submit<i class="material-icons right">send</i></button>
				</div>
			</form>
		</div>
			 
		<!-- CUSTOM CONTENT -->
		
		<!-- UPLAOD THE IMAGE -->
		<div id="modal_admin_image" class="modal modal_admin_image modal-fixed-footer" style="width: 50% !important;">
			<form enctype="multipart/form-data" action="<?php echo SITE_URL?>social-wall/add-update-grid-cell/<?= $strEventCodeEnc ?>/" id="frmGridConfig_admin_image" name="frmGridConfig_admin_image" method="post" >
				<div class="modal-content">
					<h4><span>Select and Upload Image for box cell - <span class="cell_number" id="modal_feed_cell_id"></span></h4>
					<div class="admin_image">
						<div class="admin_image_box" id="admin_image_box">
							<label>Image Upload</label>
							<div class = "file-field input-field">
								<div class = "btn">
									<span>Browse</span>
									<input type = "file" id="admin_image" name="admin_image" accept="image/*" />
								</div>
								<div class = "file-path-wrapper">
									<input class = "file-path validate" type = "text" placeholder = "Upload file" />
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="javascript:void(0);" class="modal-close waves-effect waves-green btn-flat">Cancel</a>
					<button class="btn waves-effect waves-light cmsAddUpdateGridBox" type="submit" name="cmsAddUpdateGridBox" modal_id="modal_admin_image" cellindex = "" id="cmsAddUpdateGridBox_admin_image" formName="frmGridConfig_admin_image"  formId="frmGridConfig_admin_image" >Submit<i class="material-icons right">send</i></button>
				</div>
				<!--input type="hidden" name="cellindex" id="cellindex_admin_image" value="" />
				<input type="hidden" name="platform" id="cellindex" value="admin_image" /-->
				<input type="hidden" name="cellindex" id="cellindex" value="" />
				<input type="hidden" name="platform" id="platform" value="admin_image" />
				<input type="hidden" name="refresh_timeout" id="refresh_timeout_admin_image" value="" />
				<input type="hidden" name="refresh_timeout_type" id="refresh_timeout_type_admin_image" value="" />
				<input type="hidden" name="feed_id" value="" />
				<input type="hidden" name="feed_platform_id" value="" />
			</form>
		</div>
		<!-- UPLAOD THE IMAGE -->

		<!-- SETTING CONTENT REFRESH TIME INTERVAL -->
		<div id="modal_set_timer" class="modal modal_set_timer modal-fixed-footer">
			<form action="<?php echo SITE_URL?>social-wall/add-update-grid-cell/<?= $strEventCodeEnc ?>/" id="frmGridConfig_set_timer" name="frmGridConfig_set_timer" method="post" >
				<div class="modal-content"  style="height: 240px;">
					<h4><span>Set Refresh timer interval for cell - <span class="cell_number" id="modal_set_timer_cell_id"></span></span></h4>
					<div class="timer">
						<div class="row">
							<div class="input-field col s3">
								<select  id="timer" name="timer">
									<option value="" disabled selected>Choose your option</option>
									<?php for ($iTimer=1; $iTimer <= 60; $iTimer++): ?>
										<option id="timer_<?php echo $iTimer; ?>" value="<?php echo $iTimer; ?>"><?php echo $iTimer; ?></option>
									<?php endfor; ?>
								</select>
							</div>
							<div class="input-field col s3">
								<select id="timer_type" name="timer_type">
									<option value="" disabled selected>Choose your option</option>
									<option id="timer_sec" value="sec">Second(s)</option>
									<option id="timer_min" value="min">Minute(s)</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="javascript:void(0);" class="modal-close waves-effect waves-green btn-flat">Cancel</a>
					<button class="btn waves-effect waves-light cmsAddUpdateGridBox" type="submit" name="cmsAddUpdateGridBox"  modal_id="modal_set_timer" cellindex = "" id="cmsAddUpdateGridBox_set_timer" formName="frmGridConfig_set_timer"  formId="frmGridConfig_set_timer" >Submit<i class="material-icons right">send</i></button>
				</div>
				<!--input type="hidden" name="cellindex" id="cellindex_set_timer" value="" />
				<input type="hidden" name="platform" id="platform_set_timer" value="set_timer" /-->
				<input type="hidden" name="cellindex" id="cellindex" value="" />
				<input type="hidden" name="platform" id="platform" value="set_timer" />
				<input type="hidden" name="refresh_timeout" id="refresh_timeout_set_timer" value="" />
				<input type="hidden" name="refresh_timeout_type" id="refresh_timeout_type_set_timer" value="" />
				<input type="hidden" name="feed_id" value="" />
				<input type="hidden" name="feed_platform_id" value="" />
			</form>
		</div>
		<!-- SETTING CONTENT REFRESH TIME INTERVAL -->

		<!-- SETTING ALTER MESSAGE -->
		<div id="modal_alert_msg" class="modal modal_alert_msg" style="height: 400px;">

			<div id = "dv_list_alert_msg" class = "dv_list_alert_msg">

			</div>

			<div id = "dv_frm_alert_msg" class = "dv_frm_alert_msg" style="display: none;">

			<form enctype="multipart/form-data" action="<?php echo SITE_URL?>social-wall/add-update-alert-message/<?= $strEventCodeEnc ?>/" id="frmGridConfig_set_alert_msg" name="frmGridConfig_set_alert_msg" method="post" >
				<div class="modal-content" >
					<h4><span>Set Alter Message</span></h4>
					<div class="timer">

						<div class="input-field col s6">
							<textarea class="materialize-textarea validate alert_msg" name="alert_msg" id="alert_msg" data-set="message"></textarea>
							<label for="alert_msg" class="active">Enter Message *</label>
						</div>

						<div class="input-field col s6">
							<input type="text" class="datepicker validate datepickerAm" name="from_date" id="from_date" data-set="from_date" />
							<label for="from_date" class="active">Enter From Date *</label>
						</div>

						<div class="input-field col s6">
							<input type="text" class="datepicker validate datepickerAm" name="end_date" id="end_date" data-set="end_date" />
							<label for="end_date" class="active">Enter End Date *</label>
						</div>

						<input type="hidden" name="alert_msg_id" id="alert_msg_id" value="" data-set="id" />

					</div>
				</div>

				<div class="modal-footer">
					<a href="javascript:void(0);" class="modal-close waves-effect waves-green btn-flat">Cancel</a>
					<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmsAddUpdateAlertMsg" modal_id="modal_alert_msg" cellindex = "" id="cmsAddUpdateAlertMsg_set_timer" formName="frmGridConfig_set_alert_msg" formId="frmGridConfig_set_alert_msg" >Submit<i class="material-icons right">send</i></button>
				</div>

			</form>

			</div>

		</div>
		<!-- SETTING ALTER MESSAGE -->

		<!-- End, Event Display URL, Display Alert and Total Grid -->
	</div>

	<!-- MANAGING USER FEEDS -->
	<div id="divUserFeedManagementContainer" class="modal">
		<div class="modal-content">
			<h4><span>Update User Feed</span></h4>
			<div class="row">
				<form action="<?php echo SITE_URL?>social-wall/setUserFeedDetails" id="frmUserFeddManagement" name="frmUserFeddManagement" method="post" >
					<div class="modal-content" >
						<div class="row">
							<div class="input-field col s12">
								<textarea class="materialize-textarea materialize-textarea-data-length validate" rows="5" required="" aria-required="true" name="txtUserFeedComments" id="txtUserFeedComments" placeholder="Please Enter Your Comment"  data-set="" data-length="<?php echo $strUserAndCustomTextFeedCharLimitArr['user_feed']['char']; ?>" maxlength="<?php echo $strUserAndCustomTextFeedCharLimitArr['user_feed']['char']; ?>" style="height:100px !important"></textarea>
								<label for="comment" data-error="wrong" data-success="right" data-error="Please enter user name." >User Comment</label>
							</div>
							<div class="input-field col s12">
								<input type="text" class="validate active valid" name="txtFeedUserName" id="txtFeedUserName" data-set="from_date" />
								<label for="txtFeedUserName" data-error="wrong" data-success="right" data-error="Please enter user name." >User Name</label>
							</div>
						</div>
					</div>
					<div class="modal-footer" >
						<div class="col s12">
							<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
							<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdUserFeddManagement" id="cmdUserFeddManagement" formName="frmUserFeddManagement" >Submit<i class="material-icons right">send</i></button>
						</div>
					</div>
					<input type="hidden" name="txtUserFeedCode" id="txtUserFeedCode" value="" />
				</form>
			</div>
		</div>
	</div>
	<!-- MANAGING USER FEEDS -->
<?php }else{?>
	<!-- BEGIN, ALERT MESSAGE FULL SCREEN -->
	<div id="divAlertMsgContainer" class="modal">
		<div class="modal-content" style="height: 300px !important;">
			
			<div class="row valign-wrapper">
				<div class="col l12 m23 s12 center-align">
					<h4 class=""><span>Alert Message</span></h4>
				</div>
			</div>
		</div>
	</div>
	<!-- END, ALERT MESSAGE FULL SCREEN -->


	<script type="text/javascript">
		var intNumberOfRows 	= <?php echo $intRows?>;
		var intNumberOfCols 	= <?php echo $intColumns?>;
		var gridBox 			= <?php echo ($intRows * $intColumns); ?>;
		var eventCodeEnc 		= '<?php echo $strEventCodeEnc; ?>';
		var gridIdEnc 			= '<?php echo $strGridCode; ?>';
		var alertMsgSet 		= false;
		var alertMsgData 		= {};
</script>
<?php }?>