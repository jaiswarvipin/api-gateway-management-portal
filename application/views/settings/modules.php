<div class="row">
	<div class="col s12">
		<table class="bordered highlight  responsive-table tbl-data-set hide-on-med-and-down">
	        <thead>	
	          <tr>
	              <th width='5%'>#</th>
	              <th>Module Name</th>
	              <th>Parent Module</th>
	              <th width='15%'>Action</th>
	          </tr>
	        </thead>

	        <tbody>
	        	<?php if(!empty($dataSet)){
					$intCoounter	= $intPageNumber;
					foreach($dataSet as $dataSetKey => $dataSetValue){?>
						<tr>
		          			<td><?php echo $intCoounter?></td>
		            		<td><?php echo str_replace('[divider]','',$dataSetValue['description'])?></td>
		            		<td><?php echo (isset($strModuleArr[$dataSetValue['parent_code']])?$strModuleArr[$dataSetValue['parent_code']]:'-')?></td>
		            		<td>
								<?php if($dataSetValue['is_system'] != 1){?>
									<a href="javascript:void(0);" onclick="openEditModel('deleteModel','<?php echo getEncyptionValue($dataSetValue['id'])?>',0);" class="waves-effect waves-circle waves-light secondary-content"><i class="material-icons">delete</i></a>&nbsp;
									<a href="javascript:void(0);" onclick="openEditModel('<?php echo $strDataAddEditPanel?>','<?php echo getEncyptionValue($dataSetValue['id'])?>',1);" class="waves-effect waves-circle waves-light secondary-content"><i class="material-icons">edit</i></a>
								<?php }?>
		            			<a href="javascript:void(0);" onclick="openEditModel('divFieldMapping','<?php echo getEncyptionValue($dataSetValue['id'])?>',1);" class="waves-effect waves-circle waves-light secondary-content"><i class="material-icons">airplay</i></a>
		            		</td>
		          		</tr>
						<?php $intCoounter++;?>
	        	<?php }
	        		}else{
	        			echo getNoRecordFoundTemplate(4);
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
     	 <form class="col s12" method="post" action="<?php echo SITE_URL?>settings/modules/setModuesDetails" name="<?php echo $moduleForm?>" id="<?php echo $moduleForm?>">
            <div class='row'>
              <div class='col s12'>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='txtModuleName' id='txtModuleName' data-set="description" />
                <label for='txtModuleName'>Enter Module Name *</label>
              </div>
            </div>
			
			<!--div class='row no-search'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='txtModuleURL' id='txtModuleURL' data-set="module_url" />
                <label for='txtModuleName'>Enter Module URL *</label>
              </div>
            </div-->

            <div class='row'>
              <div class='input-field col s12'>
                <select name="cboParentModuleCode" id="cboParentModuleCode" data-set="parent_code"><?php echo $strParentMenu?></select>
                <label for='cboParentModuleCode'>Select Parent Module</label>
              </div>
            </div>

			<div class='row'>
				<label>Is Visible?*</label>
				<div class='input-field col s12'>
					<p>
						<label>
							<input class="" name="rdoisVisible" value="1" type="radio" id="rdoisVisibleYes" checked data-set="is_visiable" />
							<span for="rdoisVisibleYes">Yes</span>
						</label>
						<label>
							<input class="" name="rdoisVisible" value="0" type="radio" id="rdoisVisibleNo" data-set="is_visiable" />
							<span for="rdoisVisibleNo">No</span>
						</label>
					</p>
				</div>
			</div>
			
			<div class='row'>
				<label>Backup Data(Backup Data when data is updated or deleted)*</label>
				<div class='input-field col s12'>
					<p>
						<label>
							<input class="" name="rdoBackUpData" value="1" type="radio" id="isBackUpDataYes"  data-set="is_backup" />
							<span for="isBackUpDataYes">Yes</span>
						</label>
						<label>
							<input class="" name="rdoBackUpData" value="0" type="radio" id="isBackUpDataNo" checked data-set="is_backup" />
							<span for="isBackUpDataNo">No</span>
						</label>
					</p>
				</div>
			</div>

			<input type="hidden" name="txtModuleCode" id="txtModuleCode" value="" data-set="id" />
			<input type="hidden" name="txtSearch" id="txtSearch" value="" data-set="" />
          </form>
    </div>
    <div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdSearchReset green lighten-2 hide" type="submit" name="cmdSearchReset" id="cmdSearchReset" formName="<?php echo $moduleForm?>" >Clear Filter<i class="material-icons right">find_replace</i></button>
    	<button class="btn waves-effect waves-light cmdStatusManagment" type="submit" name="cmdStatusManagment" id="cmdStatusManagment" formName="<?php echo $moduleForm?>" >Submit<i class="material-icons right">send</i></button>
    </div>
</div>




<!-- Field Mapping Modal Structure -->
<div id="divFieldMapping" class="modal modal-fixed-footer" style="width: 1024px !important;">
    <div class="modal-content">
		<h4><span class="spnActionTexta">Mapping Fields</h4>
     	 <form class="col s12" method="post" action="<?php echo SITE_URL?>settings/modules/setModuesFieldsDetails" name="<?php echo $moduleForm?>fieldMapping" id="<?php echo $moduleForm?>fieldMapping">
            <div class='row'>
              <div class='col s12'>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <select name="cboLeadAttributeCode" id="cboLeadAttributeCode" data-set="widget_code" is-change-event="yes" action="settings/modules/getWidgetAttributesList"><?php echo $strWidgetArr?></select>
                <label for='txtModuleName'>Select Widget Attributes *</label>
              </div>
			  <!--div class='input-field col s3'>
                <button class="btn waves-effect waves-light cmdLeadAttributeAdding" type="button" name="cmdLeadAttributeAdding" id="cmdLeadAttributeAdding" formName="<?php echo $moduleForm?>fieldMapping" >Add Field<i class="material-icons right">send</i></button>
              </div-->
            </div>
			<div class='row'>
              <div class='input-field col s12 div-widget-attributes-list'></div>
			</div>
			<input type="hidden" name="txtModuleFieldCode" id="txtModuleFieldCode" value="" data-set="module_code" />
			<input type="hidden" name="txtSearch" id="txtSearch" value="" data-set="" />
          </form>
    </div>
    <div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdModuleFieldArr" id="cmdModuleFieldArr" formName="<?php echo $moduleForm?>fieldMapping" >Submit<i class="material-icons right">send</i></button>
    </div>
</div>