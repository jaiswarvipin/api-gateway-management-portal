<!-- Add /Edit Modal Structure -->
<div id="divAPIDetailsModel" class="modal modal-fixed-footer" data-load-from-target="<?php echo SITE_URL?>mod/api-list/getAPIDetails" style="width:95% !important;height:600px !important;">
  <div class="modal-content">
		<h4><span>API Details</span></h4>
		<form class="col s12" method="post" action="<?php echo SITE_URL?>manage-widgets/insert-update-widget-details-by-widget-id/<?php echo $widgetId; ?>" name="<?php echo $moduleForm?>" id="<?php echo $moduleForm?>">			
            <table class="bordered highlight  responsive-table tbl-data-set hide-on-med-and-down">
				<tr>
					<td class="delivery-leader-spoc-">Delivery Leader(SPOC)</td>
					<td><label data-set="delivery-leader-spoc-">{}</label></td>
					<td class="secondary-spoc">Secondary SPOC</td>
					<td><label data-set="secondary-spoc">{}</label></td>
					<td class="system-vertical">System/Vertical</td>
					<td><label data-set="system-vertical">{}</label></td>
					<td class="function">Function Category</td>
					<td><label data-set="function">{}</label></td>
				</tr>
				<tr>
					<td class="description">API Fucntion Description</td>
					<td colspan="7"><label data-set="description">{}</label></td>
				</tr>
			</table>
			<br/>
			<table class="bordered highlight  responsive-table tbl-data-set hide-on-med-and-down">
				<tr>
					<td class="status">Status</td>
					<td><label data-set="status">{}</label></td>
					<td class="expected-max-volume">Expected Volume(Max Request)</td>
					<td><label data-set="expected-max-volume">{}</label></td>
					<td class="ha-exists">HA Exists</td>
					<td><label data-set="ha-exists">{}</label></td>
					<td class="api-document">API Document URL</td>
					<td><label data-set="api-document">{}</label></td>
					<td class="devops-user-story-id">User Story ID</td>
					<td><label data-set="devops-user-story-id">{}</label></td>
				</tr>
				<tr>
					<td class="type">Type</td>
					<td><label data-set="type">{}</label></td>
					<td class="max-usage-time">Max. Usage Time</td>
					<td><label data-set="max-usage-time">{}</label></td>
					<td class="ha-sync-">HA Sync.</td>
					<td><label data-set="ha-sync-">{}</label></td>
					<td class="api-document-date">API Document Date</td>
					<td><label data-set="api-document-date">{}</label></td>
					<td class="dev-closure-date">Dev Closure Date</td>
					<td><label data-set="dev-closure-date">{}</label></td>
				</tr>
				<tr>
					<td class="critical">Critical</td>
					<td><label data-set="critical">{}</label></td>
					<td class="consumer">Client</td>
					<td><label data-set="consumer">{}</label></td>
					<td class="dr-exiting">DR Exiting</td>
					<td><label data-set="dr-exiting">{}</label></td>
					<td class="apim-update">APIM Document URL</td>
					<td><label data-set="apim-update">{}</label></td>
					<td class="uat-released-date">UAT Released Date</td>
					<td><label data-set="uat-released-date">{}</label></td>
				</tr>
				<tr>
					<td class="consumer">Consumer</td>
					<td><label data-set="consumer">{}</label></td>
					<td>-</td>
					<td></td>
					<td class="dr-exiting">DR Sync.</td>
					<td><label data-set="dr-exiting">{}</label></td>
					<td>API Gateway</td>
					<td><label data-set="dr-exiting">{}</label></td>
					<td class="go-live-date">Go-Live Date</td>
					<td><label data-set="go-live-date">{}</label></td>
				</tr>
				<tr>
					<td class="consumer">Operation ID</td>
					<td><label data-set="consumer">{}</label></td>
					<td>Git Repo</td>
					<td></td>
					<td>-</td>
					<td>-</td>
					<td>VAPT</td>
					<td><label data-set="consumer">{}</label></td>
					<td>Tested</td>
					<td><label data-set="go-live-date">{}</label></td>
				</tr>
				<tr>
					<td>Remarks</td>
					<td colspan="9"><label data-set="consumer">{}</label></td>
				</tr>
			</table>
		</form>
    </div>
    <div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
    </div>  
</div>