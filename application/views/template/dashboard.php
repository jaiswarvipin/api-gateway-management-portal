<?php include_once('header.php'); ?>
	<div class="row">
		<div class="col s4 m4">
			<table class="bordered highlight responsive-table">
				<thead>
					<tr>
						<th>Total Hosted APIs</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>200</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col s4 m4">
			<table class="bordered highlight responsive-table">
				<thead>
					<tr>
						<th>Total Hosted APIs</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>200</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col s4 m4">
			<table class="bordered highlight responsive-table">
				<thead>
					<tr>
						<th>Total Hosted APIs</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>200</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col s4 m4">
			<table class="bordered highlight responsive-table">
				<thead>
					<tr>
						<th>Total Hosted APIs</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>200</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col s12 m12">
			<a name="delivery">&nbsp;</a>
			<table class="bordered highlight responsive-table">
				<thead>
					<tr>
						<th width="30%">Traffic</th>
						<th width="70%"><?php echo $strFilter?></th>
						<th width="30%"><a href="javascript:void(0);" onclick="openEditModel('<?php echo $strDataAddEditPanel?>','dashboard',1);" class="waves-effect waves-circle waves-light btn-floating secondary-content"><i class="material-icons">search</i></a></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="3"><?php echo $strDeliveryHTML?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col s12 m12">
			<table class="bordered highlight responsive-table">
				<thead>
					<tr>
						<th width="30%">Delivery Status</th>
						<th width="70%"><?php echo $strFilter?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2"><?php echo $strDeliveryTrackHTML?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col s12 m12">
			<table class="bordered highlight responsive-table">
				<thead>
					<tr>
						<th width="30%">Code Sync Status (Git Commit)</th>
						<th width="70%"><?php echo $strFilter?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2"><?php echo $strCodeSyncGitHTML?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col s12 m12">
			<table class="bordered highlight responsive-table">
				<thead>
					<tr>
						<th width="30%">API Document</th>
						<th width="70%"><?php echo $strFilter?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2"></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col s12 m12">
			<table class="bordered highlight responsive-table">
				<thead>
					<tr>
						<th width="30%">API Utilization</th>
						<th width="70%"><?php echo $strFilter?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2"></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col s12 m12">
			<table class="bordered highlight responsive-table">
				<thead>
					<tr>
						<th width="30%">API Gateway</th>
						<th width="70%"><?php echo $strFilter?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2"></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	
	<!-- Add /Edit Modal Structure -->
	<div id="<?php echo $strDataAddEditPanel?>" class="modal modal-fixed-footer">
		<div class="modal-content">
			<h4><span class="spnActionText">Search filter of </span> <?php echo $moduleTitle?></h4>
			 <form class="col s12" method="post" action="<?php echo SITE_URL?>dashboard" name="<?php echo $moduleForm?>" id="<?php echo $moduleForm?>">			
				<div class='row'>
				  <div class='col s12'>
				  </div>
				</div>

				<div class='row'>
				  <div class='input-field col s12'>
					<select name="cboVertical" id="cboVertical" data-set="is_active"><?php echo $strVerticalList?></select>
					<label for='cboVertical'>Select Vertical *</label>
				  </div>
				</div>
				<div class='row'>
				  <div class='input-field col s12'>
					<select name="cboSPOC" id="cboSPOC" data-set="is_active"><?php echo $strSPOCList?></select>
					<label for='cboSPOC'>Delivery Leader(SPOC) *</label>
				  </div>
				</div>
				<div class='row'>
				  <div class='input-field col s6'>
					<input class="datepicker validate" type="text" name="txtFromDate" id="txtFromDate" data-set="txtFromDate" />
					<label for="txtFromDate">Select From Date</label>
				  </div>
				  <div class='input-field col s6'>
					<input class="datepicker validate" type="text" name="txtToDate" id="txtToDate" data-set="txtToDate" />
					<label for="txtToDate">Select To Date</label>
				  </div>
				</div>
				<input type="hidden" name="txtSearch" id="txtSearch" value="" data-set="" />
			  </form>
		</div>
		<div class="modal-footer">
			<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
			<button class="btn waves-effect waves-light cmdSearchReset green lighten-2" type="submit" name="cmdDashboardSearchReset" id="cmdDashboardSearchReset" formName="<?php echo $moduleForm?>" >Clear Filter<i class="material-icons right">find_replace</i></button>
			<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdDashboardSearchManagement" id="cmdDashboardSearchManagement" formName="<?php echo $moduleForm?>" >Submit<i class="material-icons right">send</i></button>
		</div>
	</div>
<?php include_once('footer.php');?>