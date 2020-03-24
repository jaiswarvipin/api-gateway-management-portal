<!--div id="loader_div" class="loader_div" style="display: none;">
  <div class="preloader-wrapper small active">
    <div class="spinner-layer spinner-green-only">
      <div class="circle-clipper left">
        <div class="circle"></div>
      </div><div class="gap-patch">
        <div class="circle"></div>
      </div><div class="circle-clipper right">
        <div class="circle"></div>
      </div>
    </div>
  </div>

</div-->
<div class="fUserFeedMainContainer user-feed-and-comment-form">
	<div class="row">
		<div class="col s12"></div>
	</div>
	<div class="row">
		<div class="col l7 m12 s12">
			<div class="fUserFeedContainer <?php if((isset($blnShowPostInput)) && ($blnShowPostInput)) { echo 'hide-on-med-and-down';};?>">
				<div class="row device-alignment">
					<div class="col l6 s12 m12"><strong class="nml10">Browse public questions or topics</strong></div>
					<div class="col l6 s12 m12 align-wrapper fUserFeedActionContainer">
						<form name="frmUserFeed" id="frmUserFeed" method="post" action="<?php SITE_URL?>/event/user-feed-ajax/<?php echo $eventCodeEnc?>">
							<input type="hidden" name="txtUserFeedsSortOrder" id="txtUserFeedsSortOrder" value="" />
						</form>
						<form name="frmUserFeedLike" id="frmUserFeedLike" method="post" action="<?php SITE_URL?>/event/like-user-feed/">
							<input type="hidden" name="txtUserFeedsCode" id="txtUserFeedsCode" value="" />
						</form>
						<div class="right-align">
							<a href="javascript:void(0);" onclick="getUserFeedsView('frmUserFeed','recent');" class="waves-effect btn-flat recent_sort userFeedColorSorting white text-bold fBorder">Most Recent</a>
							<a href="javascript:void(0);" onclick="getUserFeedsView('frmUserFeed','likes');" class="waves-effect btn-flat liked_sort userFeedColorSorting text-bold white">Most Upvotes</a>
						</div>
					</div>
				</div>
				<div class="row">
					
					<div class="col s12 l12 m12 user-feed-ajax-feeder">
						
						<div class="row white valign-wrapper">
							<div class="col s12">
								<div class="timeline-item">
									<div class="animated-background">
										<div class="background-masker content-top"></div>
										<div class="background-masker content-first-end"></div>
										<div class="background-masker content-second-line"></div>
										<div class="background-masker content-second-end"></div>
										<div class="background-masker content-third-line"></div>
										<div class="background-masker content-third-end"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col s1 hide-on-med-and-down">
			<div class="row align-wrapper">
				<div class="col s12"><div class="center-align"><strong>OR</strong></div></div>
				<div class="col l12 m12 s12">&nbsp;</div>
				<div class="col s12"><div class="center-align seperator v-seperator"></div></div>
			</div>
		</div>
		<div class="col l4 m12 s12 comment-form <?php if((isset($blnShowPostInput)) && (!$blnShowPostInput)) { echo 'hide-on-med-and-down';};?>">
			<div class="row align-wrapper">
				<div class="col l12 m12 s12 center-align-custom sm-f15"><strong>Submit your own question topic</strong></div>
				<div class="col l12 m12 s12">&nbsp;</div>
				<div class="col l12 m12 s12 center-align-custom sm-f15"><span class="f12">Your message will be added to the public list of questions and can be upvoted by the rest of the  attendees. Your message will potentially display on the public will if selected.</span></div>
				<div class="col l12 m12 s12">&nbsp;</div>
				<div class="col l12 m12 s12">
					<form enctype="multipart/form-data" action="/event/add-user-feed-comment/<?php echo $eventCodeEnc; ?>" id="frmUserFeedManagement" name="frmUserFeedManagement" method="post" >
						<div class="input-field col l12 m12 s12">
							<input class="validate white" required="" aria-required="true" type="text" name="feeder_name" id="feeder_name" data-set="" placeholder="Full name (max 50 characters)" />
							<label for="feeder_name" data-error="wrong" data-success="right" data-error="Please enter your name for display." >Your Display Name</label>
						</div>
						<div class="input-field col l12 m12 s12">
						<label for="txtComment" data-error="wrong" data-success="right" data-error="Please enter your message." >Your Message</label>
							<textarea class="materialize-textarea materialize-textarea-data-length validate white" required="true" name="txtComment" id="txtComment" placeholder="Question or comments (max <?php echo $strFeedLength['user_feed']['char']; ?> characters)"  data-length="<?php echo $strFeedLength['user_feed']['char']; ?>" maxlength="<?php echo $strFeedLength['user_feed']['char']; ?>" style="height:100px !important"></textarea>
						</div>
						<div class="col l12 m12 s12 center-align"><span class="f12">By submitting the data you given permission for it to be displayed on the public board for this event and accept our <a href="javascript:void(0);" class="blue-text text-darken-2 text-bold" onclick="openDialog('divTermsAndCondition');">terms and conditions</a>.</span></div>
						<div class="col l12 m12 s12">&nbsp;</div>
						<div class="col l12 m12 s12 center-align-custom center-align">
							<button class="btn waves-effect waves-light addCommentUserFeed blue darken-4 cmdDMLAction w50p" type="submit" name="addCommentUserFeed" cellindex = "" id="addCommentUserFeed" formName="frmUserFeedManagement"  formId="frmUserFeedManagement" >Post</button><br />
							<a class="hide-on-med-and-up" href="<?php echo $siteURL?>" class="waves-effect btn-flat">Cancel</a>
						</div>
						<div class="col l12 m12 s12">&nbsp;</div>
						<div class="col l16 m6 s6 right-align powerBy">POWERED BY&nbsp;</div>
						<div class='col l6 m6 s6 left-align nml20'><img src="<?php echo SITE_URL?>uploads/company/pa_logo.png" class="responsive-img" width="100px" /></div>
					</form>
				</div>
				
			</div>
		</div>
	</div>
</div>
<?php if((isset($blnDevice)) && ($blnDevice) && (!$blnShowPostInput)){?>
	<div class="row fix-footer aling-wrapper">
		<div class="col l12 m12 s12 center-align"><a href="?pOsTyOuRoWn=tRuE" class="waves-effect btn blue darken-4" onclick="">Post your own</a></div>
	</div>
<?php }?>

<script language="JavaScript">
	var blnDoNotProcess = <?php if(($blnDevice) && ($blnShowPostInput)) { echo "false"; }else{ echo "true";};?>
</script>

<!-- Modal Structure -->
<div id="divTermsAndCondition" class="modal modal-fixed-footer" style="width: 70% !important;">
	<div class="modal-content">
		<h4>Terms and Condition</h4>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
	</div>
	<div class="modal-footer">
		<a href="javascript:void(0);" class="modal-close waves-effect waves-green btn-flat">Disagree</a>
		<a href="javascript:void(0);" class="modal-close waves-effect waves-green btn-flat">Agree</a>
	</div>
</div>