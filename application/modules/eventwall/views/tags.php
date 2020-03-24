<!-- Add /Edit Modal Structure -->
<div id="divTags" class="modal modal-fixed-footer" data-load-from-target="<?php echo SITE_URL?>mod/event-wall/processHookRequest/getTags">
  <div class="modal-content">
		<h4><span>Tag(s) Management</span></h4>
		<form class="col s12" method="post" action="<?php echo SITE_URL?>manage-widgets/insert-update-widget-details-by-widget-id/<?php echo $widgetId; ?>" name="<?php echo $moduleForm?>" id="<?php echo $moduleForm?>">			
            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='txtUserRole' id='txtUserRole' data-set="description" />
                <label for='txtStatusName'>Enter Role Description *</label>
              </div>
            </div>
			<input type="hidden" name="txtUserRoleCode" id="txtUserRoleCode" value="" data-set="id" />
			<input type="hidden" name="txtSearch" id="txtSearch" value="" data-set="" />
		</form>
    </div>
    <div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdSearchReset green lighten-2 hide" type="submit" name="cmdModuleSearchReset" id="cmdModuleSearchReset" formName="<?php echo $moduleForm?>" >Clear Filter<i class="material-icons right">find_replace</i></button>
    	<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdModuleManagement" id="cmdModuleManagement" formName="<?php echo $moduleForm?>" >Submit<i class="material-icons right">send</i></button>
    </div>  
</div>