<style>
	<!--
	.feedTbl{
		table-layout: fixed;
	}
	.feedTbl td{
		word-wrap:break-word;
	}
	-->
</style>

<?php if(!empty($feedsArr)): ?>
<table class="feedTbl">
	<thead>
		<tr>
			<th width="5%">#</th>
			<th width="50%">Desc</th>
			<!-- <th width="45%">Type</th> -->
		</tr>
	</thead>
	<tbody>
		<?php foreach ($feedsArr as $i => $feed): ?>
		<tr>
			<td>
				<label>
					<input type='radio' id='sel_feed_<?php echo $i; ?>' name='sel_feed[]' value='<?php echo getEncyptionValue($feed['id']); ?>' <?php echo isset($strSelectedFeedsArr[$feed['id']]) ? 'checked' : '' ?>>
					<span for='sel_feed_<?php echo $i; ?>'>&nbsp;</span>
				</label>
			</td>
			<td><?php echo (!empty($feed['data_type_desc']) && $feed['data_type_desc'] == 'Hash' ? '#' : '@') .''. $feed['data_desc']; ?></td>
			<!-- <td><?php echo $feed['data_type']; ?></td> -->
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>