<div class="col s6 right module-action" style="margin-top: -61px !important; margin-right: 11px !important;">
		<!-- Dropdown Trigger -->
		<a class='dropdown-trigger btn right w200 aActionContainer hide-on-med-and-down' href='javascript:void(0);' data-target='dropdown1'><i class="material-icons"></i>Action</a>
		<!-- Dropdown Structure -->
		<ul id='dropdown1' class='dropdown-content dlActionList'>
			<li><a class="addItemInModule" href="javascript:void(0);" onclick="openEditModel('<?php echo $strDataAddEditPanel?>','',2);"><i class="material-icons">add_circle</i>Add New Alert Message</a></li>
			<li class="divider"></li>
		</ul>
</div>

<table class="feedTbl">
	<thead>
		<tr>
			<th width="65%">Message</th>
			<th width="10%">From Date</th>
			<th width="10%">To Date</th>
			<?php if($blnShowAction){?>
				<th width="10%">Action</th>
			<?php } ?>
		</tr>
	</thead>
	
	<tbody>
		<?php if(!empty($eventAlterMsgArr)): ?>
			<?php foreach ($eventAlterMsgArr as $i => $eventAlterMsg): ?>
			<tr>
				<td><?php echo $eventAlterMsg['alert_message']; ?></td>
				<td><?php echo getDateFormat($eventAlterMsg['from_date']); ?></td>
				<td><?php echo getDateFormat($eventAlterMsg['to_date']); ?></td>
				<?php if($blnShowAction){?>
					<td>
						<a href="javascript:void(0);" onclick="openEditModel('deleteModel','<?= getEncyptionValue($eventAlterMsg['id']); ?>',0);" class="waves-effect waves-circle waves-light btn-floating secondary-content red"  title="Delete - Alert Message"><i class="material-icons">delete</i></a>&nbsp;
						<a href="javascript:void(0);" onclick="openEditModel('divAlterMsg','<?= getEncyptionValue($eventAlterMsg['id']); ?>',1);" class="waves-effect waves-circle waves-light btn-floating secondary-content"  title="Edit - Alert Message Details"><i class="material-icons">edit</i></a>
					</td>
				<?php } ?>
			</tr>
			<?php endforeach; ?>
		<?php else: echo getNoRecordFoundTemplate(4); endif; ?>
	</tbody>
</table>


<!-- Add /Edit Modal Structure -->
<div id="divAlterMsg" class="modal modal-fixed-footer" data-load-from-target="<?php echo SITE_URL?>mod/event-wall/processHookRequest/getAlterMsg">
  <div class="modal-content">
		<h4><span>Event Alert Message(s) Management</span></h4>
		<div class="row">

		<form class="col s12" method="post" action="<?= SITE_URL?>social-wall/add-update-alert-message/<?= $eventCodeEnc ?>" name="<?= $moduleForm?>" id="<?= $moduleForm?>">

				<div class="timer">

					<div class="row">

						<div class="input-field col s12">
							<textarea autocomplete="off" class="materialize-textarea validate alert_msg" name="alert_msg" id="alert_msg" data-set="alert_message"></textarea>
							<label for="alert_msg" class="active">Enter Message *</label>
						</div>

					</div>

					<div class="row">

						<div class="input-field col s6">
							<input autocomplete="off" type="text" class="datepicker validate datepickerAm" name="from_date" id="from_date" data-set="from_date" />
							<label for="from_date" class="active">Enter From Date *</label>
						</div>

						<div class="input-field col s6">
							<input autocomplete="off" type="text" class="timepicker validate timepickerAm" name="from_time" id="from_time" data-set="from_time" />
							<label for="from_time" class="active">Enter From Time *</label>
						</div>

					</div>

					<div class="row">

						<div class="input-field col s6">
							<input autocomplete="off" type="text" class="datepicker validate datepickerAm" name="to_date" id="to_date" data-set="to_date" />
							<label for="to_date" class="active">Enter End Date *</label>
						</div>

						<div class="input-field col s6">
							<input autocomplete="off" type="text" class="timepicker validate timepickerAm" name="to_time" id="to_time" data-set="to_time" />
							<label for="to_time" class="active">Enter End Time *</label>
						</div>

					</div>

					<input type="hidden" name="alert_msg_id" id="alert_msg_id" value="" data-set="id" />

				</div>

			<input type="hidden" name="txtUserRoleCode" id="txtUserRoleCode" value="" data-set="id" />
			<input type="hidden" name="txtSearch" id="txtSearch" value="" data-set="" />
		</form>

		</div>
    </div>
    <div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdSearchReset green lighten-2 hide" type="submit" name="cmdModuleSearchReset" id="cmdModuleSearchReset" formName="<?php echo $moduleForm?>" >Clear Filter<i class="material-icons right">find_replace</i></button>
    	<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdModuleManagement" id="cmdModuleManagement" formName="<?php echo $moduleForm?>" >Submit<i class="material-icons right">send</i></button>
    </div>  
</div>

<script type="text/javascript">
	window.onload = function() {
		$(document).ready(function(){
			//$('.modal').modal();
			/*
			$('.datepickerAm').datepicker({
				container: 'body'
			});
			*/
		});
	}
</script>