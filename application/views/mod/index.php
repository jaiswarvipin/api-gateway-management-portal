<?php if(isset($exportStatus)){?>
	<div class="row">
		<div class="col s12 center"><?php echo $exportStatus?></div>
	</div>
<?php }?>
<div class="row">
	<div class="col s12">
		<table class="bordered highlight  responsive-table tbl-data-set hide-on-med-and-down">
			<thead>	
				<?php if(!empty($dataColumnSet)): ?>
					<tr>
						<?php $i =0; $cntCol = count($dataColumnSet); foreach($dataColumnSet as $dataColumn): ?>
							<?php if($dataColumn == $schemaName.'.id'): ?>
								<?php continue; ?>
							<?php endif; ?>
							<?php if($i == 0): ?>
								<th width='5%'>#</th>
							<?php endif; ?>
							<th><?php echo $dataColumn; ?></th>
							<?php if(++$i === $cntCol): ?>
								
							<?php endif; ?>
						<?php endforeach; ?>
						<th width='15%' class="center">Action</th>
					</tr>
				<?php endif; ?>
			</thead>

	        <tbody>
	        	<?php if(!empty($dataSet)){
					/* Checking for skip colum name array */
					$strSkipEncryptionArr	= (isset($skipEncryptionArr) && (!empty($skipEncryptionArr)))?$skipEncryptionArr:array();
					/* Set the counter */
					$intCoounter	= $intPageNumber;
					/* Remove the id column from column set */
					unset($dataColumnSet[$schemaName.'.id']);
					
					/* iterate the data set */
					foreach($dataSet as $dataSetKey => $dataSetValue){
						/* Set the data */
						$strDataSetArr	= $dataSetValue;
						/* Get the primary key value of schema */
						$dataId 		= $dataSetValue['id'];?>
						<tr>
		          			<td><?php echo $intCoounter?></td>
							<?php 
								foreach($strDataSetArr as $strDataSetArrKey => $strDataSetArrValue){
									if(isset($dataColumnSet[$schemaName.'.'.$strDataSetArrKey])){
										echo "<td>".$strDataSetArrValue."</td>";
									}
								}
							?>
							<td>
		            			<a href="javascript:void(0);" onclick="openEditModel('deleteModel','<?php echo $dataId; ?>',0);" class="waves-effect waves-circle waves-light secondary-content"  title="Delete - <?php echo strip_tags($moduleTitle)?>"><i class="material-icons">delete</i></a>&nbsp;
		            			<a href="javascript:void(0);" onclick="openEditModel('<?php echo $strDataAddEditPanel; ?>','<?php echo $dataId; ?>',1);" class="waves-effect waves-circle waves-light secondary-content"  title="Edit - <?php echo strip_tags($moduleTitle)?> Details"><i class="material-icons">edit</i></a>
								<?php 
									/* checking is custom action hooks is define */
									if(!empty($actionHooksArr)){ 
										/* Iterating the custom hooks */
										foreach($actionHooksArr as $actionHooksArrKey => $actionHooksArrValue){
											/* display the action element */
											echo processingWidgeHooks($actionHooksArrValue, $strDataSetArr, $strSkipEncryptionArr);
										}
									}
								?>
		            		</td>
		          		</tr>
						<?php $intCoounter++;?>
	        	<?php }
	        		}else{
	        			echo getNoRecordFoundTemplate(count($dataColumnSet)+1);
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
		<?php echo $moduleHTML;?>
    </div>
    <div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdSearchReset green lighten-2 hide" type="submit" name="cmdModuleSearchReset" id="cmdModuleSearchReset" formName="<?php echo $moduleForm?>" >Clear Filter<i class="material-icons right">find_replace</i></button>
    	<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdModuleManagement" id="cmdModuleManagement" formName="<?php echo $moduleForm?>" >Submit<i class="material-icons right">send</i></button>
    </div>  
</div>

<!-- Import Record Modal Structure -->
<div id="import-data-container" class="modal modal-fixed-footer" style="height: 50% !important;">
  <div class="modal-content">
		<h4><span class="spnActionText">Import </span> <?php echo $moduleTitle?> Data</h4>
		<form name='frmImportData' id='frmImportData' action="<?php echo $importDataURL?>" enctype="multipart/form-data">
			<div class="file-field col s12 input-field">
				<div class="btn">
					<span>Please select file</span>
					<input type="file" name="widgetDataImportFile" id="widgetDataImportFile" />
				</div>
				<div class="file-path-wrapper">
					<input class="file-path validate" type="text" />
				</div>
			</div>
			<div class="row">
				
			</div>
			<div class="row">
				 <div class="col s2">Note:</div>
				 <div class="col s12">1. <a href="<?php echo $importTemplate?>" target="_blank">Click here</a> to download the importing file template.</div>
			</div>
		</form>
    </div>
    <div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdSearchReset green lighten-2 hide" type="submit" name="cmdModuleSearchReset" id="cmdModuleSearchReset" formName="frmImportData" >Clear Filter<i class="material-icons right">find_replace</i></button>
    	<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdModuleManagement" id="cmdModuleManagement" formName="frmImportData" >Submit<i class="material-icons right">send</i></button>
    </div>  
</div>

<!-- Export Record Modal Structure -->
<div id="export-data-container" class="modal modal-fixed-footer" style="height: 30% !important;">
  <div class="modal-content">
  <h4><span class="spnActionText">Export </span> <?php echo $moduleTitle?> Data</h4>
  <form name='frmExportData' id='frmExportData' action="<?php echo $moduleUri; ?>">
    <div class="file-field col s12 input-field">
        <div>
            <span>Do you want to export the file?</span>
        </div>
    </div>
   <div class="row">
       
   </div>
  </form>
    </div>
    <div class="modal-footer">
     <a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
     <button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdModuleManagement" id="cmdModuleManagement" formName="frmExportData" >Export<i class="material-icons right">send</i></button>
    </div>  
</div>

<?php 
/* checking is custom widget panel hooks is define */
if(!empty($actionHooksArr)){ 
	/* Iterating the custom widget hooks */
	foreach($customWidgetHooksArr as $customWidgetHooksArrKey => $customWidgetHooksArrValue){
		/* display the widget element */
		echo $customWidgetHooksArrValue;
	}
}
?>