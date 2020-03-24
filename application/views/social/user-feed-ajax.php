<div class="user-feed">
	<?php 
		/* user feeds is not empty then do needful */
		if(!empty($dataSet)) {
			/* iterating the loop */
			foreach($dataSet as $strUserFeedArrKey => $strUserFeedArrValue){ ?>
				<div class="row white valign-wrapper">
					<?php if((isset($blnDevice)) && ($blnDevice)){?>
						<div class="pt10 pb10 w100p">
							<div class="col l12 m12 s12"><?php echo html_escape($strUserFeedArrValue['title']) ?></div>
							<div class="col l12 m12 s12 npl mt10">
								<div class="col l8 s8 m8 left-align"><span class="f10">Submitted by </span> <span class="blue-text"><?php echo $strUserFeedArrValue['feeder_name'] ?></span></div>
								<div class="col l4 s4 m4 right-align pb10"  onclick="SetUserUpvoat('<?php echo getEncyptionValue($strUserFeedArrValue['id']) ?>');">
									<a href="javascript:void(0);" class="waves-effect grey-text nmt5" id="alikeCount-<?php echo getEncyptionValue($strUserFeedArrValue['id']) ?>"><li class="material-icons like text-bold">arrow_upward</li></a>
									<a href="javascript:void(0);"><span class="grey-text" id="likeCount-<?php echo getEncyptionValue($strUserFeedArrValue['id']) ?>"><?php echo $strUserFeedArrValue['likes_count'] ?></span></a>
								</div>
							</div>
						</div>
								 
						 
					<?php }else{?>
						<div class="col l10 m10 s10 v-seperator-feeds">
							<div><p class="feedText"><?php echo $strUserFeedArrValue['title'] ?></p></div>
							<div><p><span class="f12">Submitted by </span> <span class="blue-text text-darken-4"><?php echo $strUserFeedArrValue['feeder_name'] ?></span></p></div>
						</div>
						<div class="col 12 m2 s2 center-align"  onclick="SetUserUpvoat('<?php echo getEncyptionValue($strUserFeedArrValue['id']) ?>');">
							<a href="javascript:void(0);" class="waves-effect grey-text nmt5" id="alikeCount-<?php echo getEncyptionValue($strUserFeedArrValue['id']) ?>"><li class="material-icons like text-bold">arrow_upward</li></a>
							<a href="javascript:void(0);"><span class="grey-text" id="likeCount-<?php echo getEncyptionValue($strUserFeedArrValue['id']) ?>"><?php echo $strUserFeedArrValue['likes_count'] ?></span></a>
						</div>
					<?php }?>
				</div>
		<?php }
		}else {
			echo "No User feeds found.";
		}
	?>
</div>
