<div class="row">
	<div class="col s12">
		<table class="bordered highlight  responsive-table tbl-data-set hide-on-med-and-down">
	        <thead>	
	          <tr>
	              <th width='5%'>#</th>
	              <th>Description</th>
	              <th width='10%'>Action</th>
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
		            			<a href="<?php echo SITE_URL?>settings/widgetfileds?wIdGetCoDe=<?php echo getEncyptionValue($dataSetValue['id'])?>" class="waves-effect waves-circle waves-light secondary-content"><i class="material-icons">playlist_add</i></a>
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
     	 <form class="col s12" method="post" action="<?php echo SITE_URL?>settings/widgets/setWidgetDetails" name="<?php echo $moduleForm?>" id="<?php echo $moduleForm?>">			
            <div class='row'>
              <div class='col s12'>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='txtWidgetDescription' id='txtWidgetDescription' data-set="description" />
                <label for='txtStatusName'>Enter Module Description *</label>
              </div>
            </div>
			<input type="hidden" name="txtWidgetCode" id="txtWidgetCode" value="" data-set="id" />
			<input type="hidden" name="txtSearch" id="txtSearch" value="" data-set="" />
          </form>
    </div>
    <div class="modal-footer">
    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
		<button class="btn waves-effect waves-light cmdSearchReset green lighten-2 hide" type="submit" name="cmdModuleSearchReset" id="cmdModuleSearchReset" formName="<?php echo $moduleForm?>" >Clear Filter<i class="material-icons right">find_replace</i></button>
    	<button class="btn waves-effect waves-light cmdDMLAction" type="submit" name="cmdModuleManagement" id="cmdModuleManagement" formName="<?php echo $moduleForm?>" >Submit<i class="material-icons right">send</i></button>
    </div>  
</div>