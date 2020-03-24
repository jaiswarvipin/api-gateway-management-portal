<div class="row">
	<div class="col s12">
		<table class="bordered highlight  responsive-table tbl-data-set hide-on-med-and-down">
	        <thead>	
	          <tr>
	              <th width='5%'>#</th>
	              <th>Name</th>
	              <th>Email</th>
				  <th>Role</th>
				  <th>Status</th>
				  <th width='7%'>Action</th>
	          </tr>
	        </thead>

	        <tbody>
	        	<?php if(!empty($dataSet)){
					$intCoounter	= $intPageNumber;
	        		foreach($dataSet as $dataSetKey => $dataSetValue){?>
						<tr>
		          			<td><?php echo $intCoounter?></td>
		            		<td><?php echo $dataSetValue['user_name']?></td>
							<td><?php echo $dataSetValue['user_email']?></td>
							<td><?php echo $dataSetValue['role_name']?></td>
							<td><?php echo $dataSetValue['is_active']?></td>
					  		<td>
		            			<a href="javascript:void(0);" onclick="openEditModel('deleteModel','<?php echo getEncyptionValue($dataSetValue['id'])?>',0);" class="waves-effect waves-circle waves-light secondary-content"><i class="material-icons">delete</i></a>&nbsp;
		            			<a href="javascript:void(0);" onclick="openEditModel('<?php echo $strDataAddEditPanel?>','<?php echo getEncyptionValue($dataSetValue['id'])?>',1);" class="waves-effect waves-circle waves-light secondary-content"><i class="material-icons">edit</i></a>
		            		</td>
		          		</tr>
						<?php $intCoounter++;?>
	        	<?php }
	        		}else{
	        			echo getNoRecordFoundTemplate(6);
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
     	 <form class="col s12" method="post" action="<?php echo SITE_URL?>settings/userprofiles/setUserProfile" name="<?php echo $moduleForm?>" id="<?php echo $moduleForm?>">			
            <div class='row'>
              <div class='col s12'>
              </div>
            </div>

            <div class='row'>
				<div class='input-field col s4'>
					<input class='validate' type='text' name='txtUserName' id='txtUserName' data-set="user_name" />
					<label for='txtUserName'>Enter User Name *</label>
				</div>
				<div class='input-field col s4'>
					<input class='validate' type='text' name='txtEmail' id='txtEmail' data-set="user_email" />
					<label for='txtEmail'>Enter Email ID *</label>
				</div>
				<div class='input-field col s4'>
					<input class='validate' type='password' name='txtPassword' id='txtPassword' data-set="" />
					<label for='txtPassword'>Password *</label>
				</div>
            </div>
			
			<div class='row'>
				<div class='input-field col s4'>
					<select name="cboRoleCode" id="cboRoleCode" data-set="role_code"><?php echo $strCustomRoleArr?></select>
					<label for='cboRoleCode'>Select User Role*</label>
				</div>
				<div class='input-field col s4'>
					<select name="cboUserStatus" id="cboUserStatus" data-set="is_active"><?php echo $strUserStatsArr?></select>
					<label for='cboUserStatus'>Select Status *</label>
				</div>
            </div>
			
			<?php if(!empty($strVerticalArr)){?>
				<h5>Vertical</h5>
				<div class='row'>
					<div class='input-field col s12'>
						<table border="0">
							<?php foreach($strVerticalArr as $strVerticalArrKey => $strVerticalArrValue){?>
								<tr>
									<td><?php echo str_replace('[divider]','',$strVerticalArrValue['name'])?></td>
									<td><label><input class='validate' type='checkbox' name='txtVertcalName[]' id='txtVertcalName<?php echo getEncyptionValue($strVerticalArrValue['id'])?>' value="<?php echo getEncyptionValue($strVerticalArrValue['id'])?>" data-set="vertical_code" />&nbsp;<span for="txtVertcalName<?php echo getEncyptionValue($strVerticalArrValue['id'])?>">&nbsp;</span></label></td>
								</tr>
							<?php }?>
						</table>
					</div>
				</div>
			<?php }?>
			
			<input type="hidden" name="txtUserCode" id="txtUserCode" value="" data-set="id" />
			<input type="hidden" name="txtSearch" id="txtSearch" value="" data-set="" />
		</form>
    </div>
    <div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdSearchReset green lighten-2 hide" type="submit" name="cmdUserProfileSearchReset" id="cmdUserProfileSearchReset" formName="<?php echo $moduleForm?>" >Clear Filter<i class="material-icons right">find_replace</i></button>
    	<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdUserProfileManagement" id="cmdUserProfileManagement" formName="<?php echo $moduleForm?>" >Submit<i class="material-icons right">send</i></button>
    </div>
</div>