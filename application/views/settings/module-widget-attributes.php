<table id="sort" class="bordered highlight responsive-table tbl-data-set hide-on-med-and-down">
	<thead>	
	  <tr>
		  <th width='5%'>#</th>
		  <th>Attribute Name</th>
		  <th width="20%">Action</th>
	  </tr>
	</thead>

	<tbody>
		<?php
			/* Checking widget attributes array */
			if(!empty($strWidgetAttArr)){
				/* variable initialization */
				$intCoounter	= 1;
				/* Iterating the counter */
				foreach($strWidgetAttArr as $strWidgetAttArrKey => $strWidgetAttArrValue){?>
					<tr>
						<td class="index"><?php echo $intCoounter?></td>
						<td><?php echo $strWidgetAttArrValue['attri_slug_name']?></td>
						<td>
							<?php 
								/* variable initialization */
								$strIsSelected = '';
								/* if instance attribute is selected then do needful */
								if(isset($strModuleAttributesArr[$strWidgetAttArrValue['id']])){
									/* value overload */
									$strIsSelected = 'checked="checked"';
								}
							?>
							<label>
								<input class='validate' type='checkbox' name='txtWidgetAttributesCode[]' id='txtWidgetAttributesCode<?php echo getEncyptionValue($strWidgetAttArrValue['id'])?>' value="<?php echo getEncyptionValue($strWidgetAttArrValue['id'])?>" <?php echo $strIsSelected?> data-set="id" />&nbsp;<span for="txtWidgetAttributesCode<?php echo getEncyptionValue($strWidgetAttArrValue['id'])?>">&nbsp;</span>
							</label>
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

<script>

var fixHelperModified = function(e, tr) {
    var $originals = tr.children();
    var $helper = tr.clone();
    $helper.children().each(function(index) {
        $(this).width($originals.eq(index).width())
    });
    return $helper;
},
    updateIndex = function(e, ui) {
        $('td.index', ui.item.parent()).each(function (i) {
            $(this).html(i + 1);
        });
    };

$("#sort tbody").sortable({
    helper: fixHelperModified,
    stop: updateIndex
}).disableSelection();

</script>