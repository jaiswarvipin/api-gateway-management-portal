<div class="row">
	<div class="col s12">
		<table class="bordered highlight  responsive-table tbl-data-set hide-on-med-and-down">
	        <thead>	
	          <tr>
	              <th width='5%'>#</th>
	              <th>Description</th>
				  <th width='10%' class='center'>Action</th>
	          </tr>
	        </thead>

	        <tbody>
	        	<?php if(!empty($dataSet)){
					$intCoounter	= $intPageNumber;
	        		foreach($dataSet as $dataSetKey => $dataSetValue){?>
						<tr>
		          			<td><?php echo $intCoounter?></td>
		            		<td><?php echo $dataSetValue['description']?></td>
					  		<td>
		            			<a href="javascript:void(0);" onclick="openEditModel('deleteModel','<?php echo $dataSetValue['id']?>',0);" class="waves-effect waves-circle waves-light secondary-content"><i class="material-icons">delete</i></a>&nbsp;
		            			<a href="javascript:void(0);" onclick="openEditModel('<?php echo $strDataAddEditPanel?>','<?php echo $dataSetValue['id']?>',1);" class="waves-effect waves-circle waves-light secondary-content"><i class="material-icons">edit</i></a>
		            		</td>
		          		</tr>
						<?php $intCoounter++;?>
	        	<?php }
	        		}else{
	        			echo getNoRecordFoundTemplate(3);
	        		}
				?>
	        </tbody>
	      </table>
	      <?php echo $pagination; ?>
	</div>
</div>


<!-- Add /Edit Modal Structure -->
<div id="<?php echo $strDataAddEditPanel?>" class="modal modal-fixed-footer">
    <div class="modal-content">
		<h4><span class="spnActionText">Add New</span> <?php echo $moduleTitle?></h4>
     	 <form class="col s12" method="post" action="<?php echo SITE_URL?>settings/userrole/setUserRole" name="<?php echo $moduleForm?>" id="<?php echo $moduleForm?>">			
            <div class='row'>
              <div class='col s12'>
              </div>
            </div>

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
		<button class="btn waves-effect waves-light cmdSearchReset green lighten-2 hide" type="submit" name="cmdUserRoleSearchReset" id="cmdUserRoleSearchReset" formName="<?php echo $moduleForm?>" >Clear Filter<i class="material-icons right">find_replace</i></button>
    	<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdUserRoleManagement" id="cmdUserRoleManagement" formName="<?php echo $moduleForm?>" >Submit<i class="material-icons right">send</i></button>
    </div>
</div>


<!-- Events Modal Structure -->
<div id="eventListModel" class="modal modal-fixed-footer">
    <div class="modal-content">
		<h4><span class="spnActionText">Events List</span></h4>
     	<form class="col s12" method="post" action="<?php echo SITE_URL?>settings/userrole/setUserRoleEvent" name="frmEventAssocation" id="frmEventAssocation">			
            <div class='row'>
              <div class='col s12'>
              </div>
            </div>
			<?php 
				/* if event list present then do needful */
				if(!empty($strEventArr)){?>
					<div class='row'>
						<div class='input-field col s12'>
							<table border="0">
								<tr>
									<th width="25%">Event Name</th>
									<th>&nbsp;</th>
								</tr>
								<?php 
								/* iterating the loop */
								foreach($strEventArr as $strEventArrKey => $strEventArrValues){?>
									<tr>
										<td><?php echo str_replace('[divider]','',$strEventArrValues['name'])?></td>
										<td><label><input class='validate' type='checkbox' name='txtEventName[]' id='txtEventName<?php echo getEncyptionValue($strEventArrValues['id'])?>' value="<?php echo getEncyptionValue($strEventArrValues['id'])?>" data-set="event_code" />&nbsp;<span for="txtEventName<?php echo getEncyptionValue($strEventArrValues['id'])?>">&nbsp;</span></label></td>
									</tr>
							<?php } ?>
							</table>
						</div>
					</div>
			<?php } ?>
			<input type="hidden" name="txtEventRoleCode" id="txtEventRoleCode" value=""/>
        </form>
    </div>
    <div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
    	<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdUserRoleEventManagement" id="cmdUserRoleEventManagement" formName="frmEventAssocation" >Submit<i class="material-icons right">send</i></button>
    </div>
</div>
