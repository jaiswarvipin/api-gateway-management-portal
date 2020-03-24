<?php if(!empty($feedsArr)): ?>
	<div style="text-align:right; <?php if($box_number > 0){?>margin-top: -50px;<?php }?>">
		<a href="javascript:void(0);" onclick="loadFeedOfEvent(this, '<?php echo $eventCodeEnc; ?>', <?php echo $box_number; ?>, 'modal_select_feed', 'recent','','user_feed');" class="waves-effect waves-green btn<?php if($strFilterBy == 'likes'){ echo '-flat';};?> recent_sort active"><i class="material-icons left">cloud</i>Most Recent</a>
		<a href="javascript:void(0);" onclick="loadFeedOfEvent(this, '<?php echo $eventCodeEnc; ?>', <?php echo $box_number; ?>, 'modal_select_feed', 'likes','','user_feed');" class="waves-effect waves-green btn<?php if($strFilterBy == 'recent'){ echo '-flat';};?> liked_sort"><i class="material-icons left">thumb_up</i>Most Liked</a>
	</div>
	<table class="feedTbl">
		<thead>
			<tr>
				<?php if($box_number > 0){?>
					<th width="5%">#</th>
				<?php }?>
				<th width="70%">Feed</th>
				<th width="10%">Feeder Name</th>
				<th width="6%">Likes #</th>
				<?php if($box_number > 0){?>
					<th width="10%">Action</th>
				<?php }?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($feedsArr as $i => $feed): ?>
			<tr id="<?php echo getEncyptionValue($feed['id']); ?>">
				<?php if($box_number > 0){?>
					<td>
						<label>
							<input type='checkbox' id='sel_feed_<?php echo $i; ?>' name='sel_feed[]' value='<?php echo getEncyptionValue($feed['id']); ?>' <?php echo (isset($strSelectedFeedsArr[$feed['id']]) ? 'checked' : '') ?>>
							<span for='sel_feed_<?php echo $i; ?>'>&nbsp;</span>
						</label>
					</td>
				<?php }?>
				<td class="userFeed"><?php echo $feed['title']; ?></td>
				<td class="userName"><?php echo $feed['feeder_name']; ?></td>
				<td><?php echo $feed['likes_count']; ?></td>
				<?php if($box_number > 0){?>
					<td>
						<a href="javascript:void(0);" onclick="openEditModel('deleteModel','<?php echo getEncyptionValue($feed['id']); ?>',0);" class="waves-effect waves-circle waves-light btn-floating secondary-content red"  title="Delete - User Feed"><i class="material-icons">delete</i></a>&nbsp;
						<a href="javascript:void(0);" onclick="openEditModel('divUserFeedManagementContainer','<?php echo getEncyptionValue($feed['id']); ?>',5);" class="waves-effect waves-circle waves-light btn-floating secondary-content"  title="Edit - User Feed Details"><i class="material-icons">edit</i></a>
					</td>
				<?php }?>
			</tr>
			<!-- modal-action modal-close -->
			<?php endforeach; ?>
		</tbody>
	</table>
<?php else: ?>No Data Found<?php endif; ?>
