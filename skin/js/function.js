var intCellIndex 		= 0;
var intModalId 			= 0;
		
/**************************************************************************
 Purpose 		: Processing customer request after system response.
 Inputs  		: pStrFormName :: Form Name.
				: pStrResponse :: System response object
 Return 		: none.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function processRequestAfterResponse(pStrFormName, pStrResponseObject){
	/* JONE decoding the response Arr */

	if (isJson(pStrResponseObject)) {
		var objResponse = jQuery.parseJSON(pStrResponseObject);
	}else{
		var objResponse = pStrResponseObject;
	}

	if(objResponse.requestProcessStatus == 0){
		if(($('.addItemInModule').length == 0) && ($('#txtRoleCode').length > 0)){
			$('#'+objectRefrence).find('input').each(function(){
				if(($(this).attr('type') == 'radio') || ($(this).attr('type') == 'checkbox')){
					$(this).removeAttr('checked');
				}
			});
		}
		
		if(objectRefrence == 'divFieldMapping'){
			$('#tblLeadAttribute').html('');
		}		
		showToast(objResponse.message);
	}else{
		$.fancybox.hideLoading();
		switch(pStrFormName){	
			case 'frmAuthencation':
						
				break;
			case 'frmCompanyRegistration':
				
				break;
			case 'frmGridConfig_user_feed':
				showToast(objResponse.message);
				if (objResponse.cell_html) {
					$("#cell_index_data_" + intCellIndex).html(objResponse.cell_html);
				}
				if (objResponse.requestProcessStatus == 1) {
					$('#' + intModalId).modal('close');
					
					if (objResponse.platform && objResponse.platform == 'set_timer') {

						if (objResponse.timer) {
							$("#cell_index_data_" + intCellIndex + " .timer_btn").attr("timer", objResponse.timer);
						}

						if (objResponse.timer_type) {
							$("#cell_index_data_" + intCellIndex + " .timer_btn").attr("timer_type", objResponse.timer_type);
						}
					}
				}
				break;
			case 'frmUserFeddManagement':
				showToast(objResponse.message);
				$('#'+$('#txtUserFeedCode').val()).find('.userFeed').html($('#txtUserFeedComments').val());
				$('#'+$('#txtUserFeedCode').val()).find('.userName').html($('#txtFeedUserName').val());
				$('#divUserFeedManagementContainer').find('.btn-flat').click();
				return false;
				break;
			case 'frmStatusClassification':
				/* Variable Initialization */
				var intResponseStatusParentCode = objResponse.isopen;
				/* Checking for open repose status */
				if(intResponseStatusParentCode == 1){
					$('.hideOnCloseStatus').show();
				}else{
					$('.hideOnCloseStatus').hide();
				}
				return;
				break;
			case 'frmDynamicEventDataSet':
				var strReturnArr	= (objResponse.message);
				/* If redirection URL is set then do needful */
				if (typeof objResponse.destinationURL != typeof undefined && objResponse.destinationURL != false && objResponse.destinationURL != '') {
					setTimeout(function(){
						/* Redirecting the URL */
						window.location.href =  objResponse.destinationURL;
					},intTimeToShowMessage);
				/* if module widget attributes layout then do needful */
				}else if($('#cboLeadAttributeCode').length > 0){
					/* Setting the attributes list */
					$('.div-widget-attributes-list').html(strReturnArr);
				}else{
					$('#'+objectRefrence).find("option").hide();
					
					$.each(strReturnArr, function(strKeyColumn, strColumnValue){
						if(objectRefrence == 'cboUSerCode'){
							$('#'+objectRefrence).append('<option value="'+strKeyColumn+'">'+strColumnValue+'</option>');
						}else{
							$('#'+objectRefrence).children('option[value="'+strColumnValue+'"]').show();
						}
					});
					$('#'+objectRefrence).formSelect();
				}
				
				return false;
				
				break;
			case 'frmGetDataByCode':
			case 'frmHookDataProcess':
				if(objResponse.message){
					showToast(objResponse.message);
				}else{
					
					var blnUserProfile	= false;
					if(objectRefrence == 'userProfileModel'){
						blnUserProfile = true;
					}
					
					if(objectRefrence == 'divAPIDetailsModel'){
						$('#'+objectRefrence).find('td').removeClass('error-text');
						$.each(objResponse, function(strKeyColumn, strColumnValue){
							$('#'+objectRefrence).find('label').each(function(){
								if($(this).attr('data-set') == strKeyColumn){
									$(this).html(strColumnValue);
								}
							});
						});
						
						if($('#'+objectRefrence).find('label[data-set="type"]').html()=='New'){
							$.each(objResponse, function(strKeyColumn, strColumnValue){
								$('#'+objectRefrence).find('label').each(function(){
									if(($(this).attr('data-set') == strKeyColumn) && (($(this).html() == '') || ($(this).html() == '-')  || ($(this).html() == '0'))){
										$('.'+strKeyColumn).addClass('error-text');
									}
								});
							});
						}
						return false;
					}
					
					if(objectRefrence == 'divFieldMapping'){
						$('#tblLeadAttribute').html('');
						$.each(objResponse, function(strKeyColumn, strColumnValue){
							$('#tblLeadAttribute').append(addFieldRowInTable(strColumnValue.attri_code, strColumnValue.attri_slug_name));
							$('#cmdAddLeadAttributeOptions').find('option[value="'+strColumnValue.attri_code+'"]').remove();
						});
						$('#cmdAddLeadAttributeOptions').formSelect();
					}
					
					if((($('.addItemInModule').length == 0) && ($('#txtRoleCode').length > 0)) || ($('#eventListModel').length > 0)){
						$('#'+objectRefrence).find('input').each(function(){
							if(($(this).attr('type') == 'radio') || ($(this).attr('type') == 'checkbox')){
								$(this).removeAttr('checked');
							}
						});
						
						$.each(objResponse, function(strKeyColumn, strColumnValue){
							$.each(strColumnValue, function(strColumnValueKey, strColumnValueArr){
								$('#'+objectRefrence).find('input').each(function(){
									if(($(this).attr('type') == 'radio') || ($(this).attr('type') == 'checkbox')){
										if(($(this).attr('data-set') == strColumnValueKey) && ($(this).val() == strColumnValueArr)){
											$(this).attr('checked','checked');
										}
									}
								});
							});
						});
						return false;
					}
					
					if($('#'+objectRefrence).find(':input').length > 0){
						$('#'+objectRefrence).find(':input:enabled:visible:first').focus();
						
						$.each(objResponse, function(strKeyColumn, strColumnValue){
							$('#'+objectRefrence).find('input').each(function(){
								if(($(this).attr('type') == 'radio') || ($(this).attr('type') == 'checkbox')){
									if(($(this).attr('data-set') == strKeyColumn) && ($(this).val() == strColumnValue)){
										$(this).attr('checked','checked');
									}
								}else if(($(this).attr('type') == 'file')){
									
								}else{
									if(blnUserProfile){
										if((strKeyColumn == '0')){
											var strObjectRefrence	= $(this);
											$.each(strColumnValue, function(strColumnValueKey, strColumnValueDetails){
												if($(strObjectRefrence).attr('data-set') == strColumnValueKey){
													$(strObjectRefrence).val(strColumnValueDetails);
													$(strObjectRefrence).addClass('active');
												}
											});
										}
									}else{
										if($(this).attr('data-set') == strKeyColumn){

											if('environmentModel' == objectRefrence && isJson(strColumnValue) && $(this).attr('id') == 'txtValueDescription'){
												jsonObject = JSON.parse(strColumnValue);
												for (var k in jsonObject) {
													if (jsonObject.hasOwnProperty(k)) {
														$('.jsonEnv').append('<div class="row"><div class="input-field col s12"><input type="text" name="ValueDescription['+k+']" id="'+k+'" value="'+jsonObject[k]+'" data-set="" /><label for="' + k + '">Enter Value ' + k + '</label></div></div>');
													}
												}
											}

											$(this).val(strColumnValue);
											$(this).addClass('active');
											M.updateTextFields();
										}
									}
								}
							});
						});
					}
					
					if($('#'+objectRefrence).find('select').length > 0){
						$.each(objResponse, function(strKeyColumn, strColumnValue){
							$('#'+objectRefrence).find('select').each(function(){
								if((strKeyColumn == '0')){
									if(blnUserProfile){
										var strObjectRefrence	= $(this);
										$.each(strColumnValue, function(strColumnValueKey, strColumnValueDetails){
											if($(strObjectRefrence).attr('data-set') == strColumnValueKey){
												$(strObjectRefrence).val(strColumnValueDetails);
												$(strObjectRefrence).formSelect();
											}
										});
									}else{
										var strObjectRefrence	= $(this);
										$.each(strColumnValue, function(strColumnValueKey, strColumnValueDetails){
											if($(strObjectRefrence).attr('data-set') == strColumnValueKey){
												$(strObjectRefrence).val(strColumnValueDetails);
												$(strObjectRefrence).formSelect();
											}
										});
									}
								}else if($(this).attr('data-set') == strKeyColumn){
									if((strKeyColumn == 'zone') || (strKeyColumn == 'region') || (strKeyColumn == 'city') || (strKeyColumn == 'area') || (strKeyColumn == 'branch')){
										$(this).html(strColumnValue);
									}else{
										if('environmentModel' == objectRefrence){
											$(this).html(strColumnValue);
										}else{
											$(this).val(strColumnValue);
										}
									}
									$(this).formSelect();
								}
							});
						});
					}
					
					if($('#'+objectRefrence).find('textarea').length > 0){
						$.each(objResponse, function(strKeyColumn, strColumnValue){
							$('#'+objectRefrence).find('textarea').each(function(){
								if($(this).attr('data-set') == strKeyColumn){
									$(this).val(strColumnValue);
									$(this).addClass('active');
								}
							});
						});
					}
					
					if($('#userProfileModel').length > 0){
						$('#'+objectRefrence).find('input').each(function(){
							if(($(this).attr('type') == 'radio') || ($(this).attr('type') == 'checkbox')){
								$(this).removeAttr('checked');
							}
						});
						
						$.each(objResponse, function(strKeyColumn, strColumnValue){
							$.each(strColumnValue, function(strColumnValueKey, strColumnValueArr){
								if(strColumnValueKey == 'vertical_codes'){
									$.each(strColumnValueArr, function(strColumnValueArrKey, strColumnValueArrKey){
										$.each(strColumnValueArrKey, function(strVerticalIndex, strVerticalValue){
											$('#'+objectRefrence).find('input').each(function(){
												if(($(this).attr('type') == 'radio') || ($(this).attr('type') == 'checkbox')){
													if(($(this).attr('data-set') == strVerticalIndex) && ($(this).val() == strVerticalValue)){
														$(this).attr('checked','checked');
													}
												}
											});
										});
									});
								}
							});
						});
						return false;
					}
					
					
					if(objectRefrence == 'widgetAttriuteModel'){
						var strAttrinuteArr = objResponse.attri_value_list;
						$('.divWidgetAttributesPanel').html('');
						if (objResponse.file_driver) {
							displayHideElement(null,'-2','divLeadAttributesContaierFileDriver');
							$.each(strAttrinuteArr, function (strKey , strValue){
								addFormElement('text','txtWidgetAttributesName[]','divWidgetAttributesPanel',strValue);

							});
							$("#cboAttributeType").trigger("change");
						}
						else if(strAttrinuteArr != ''){
							displayHideElement(null,'-1','divLeadAttributesContaier');
							$.each(strAttrinuteArr, function (strKey , strValue){
								addFormElement('text','txtWidgetAttributesName[]','divWidgetAttributesPanel',strValue);

							});
						}else{
							displayHideElement(null,'','divLeadAttributesContaier');
						}
					}
					M.updateTextFields();
				}
				return false;
				break;
			case 'frmCustom':
				$.each(objResponse, function(strKeyColumn, strColumnValue){
					if(strKeyColumn == 'dataset'){
						if(objectRefrence != ''){
							$('#'+objectRefrence).html(strColumnValue);
							$('#'+objectRefrence).formSelect();
						}
					}else if(strKeyColumn == 'reporting'){
						$('#cboReportingManager').html(strColumnValue);
						$('#cboReportingManager').formSelect();
					}
				});
				 
				return false;
				break;
			case 'frmDelete':
				if($('#modal_select_feed tr#'+$('#txtDeleteRecordCode').val()).length > 0){
					showToast(objResponse.message);
					$('#'+$('#txtDeleteRecordCode').val()).hide('slow',function(){
						$(this).remove();
					});
					$('#deleteModel').modal('close');
					return false;
				}
				break;
		}
		
		showToast(objResponse.message);

		/* If redirection URL is set then do needful */
		if (typeof objResponse.destinationURL != typeof undefined && objResponse.destinationURL != false && objResponse.destinationURL != '') {
			setTimeout(function(){
				/* Redirecting the URL */
				window.location.href =  objResponse.destinationURL;
			},intTimeToShowMessage)
		}else{
			setTimeout(function(){
				/* Redirecting the URL */
				window.location.reload();
			},intTimeToShowMessage)
		}
	}

	hideLoader();
	
	return false;
}

/**************************************************************************
 Purpose 		: Getting result set of requested page number.
 Inputs  		: intpageNumber :: page Number,
				: fromObject :: From Object,
				: blnSetValue :: Reset value from Se3arch JSON
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function goToPage(intpageNumber, fromObject, blnSetValue){
	if(isNaN(intpageNumber)){
		showToast('Invalid page number request.');
	}else{
		if(($('#txtSearchFilters').html() != '') && (blnSetValue)){
			var strSearchArr	= jQuery.parseJSON($('#txtSearchFilters').html());
			$.each(strSearchArr, function(strElementRefObj, strElementValue){
				$('#'+fromObject).find('#'+strElementRefObj).val(strElementValue);
			});
			$('#'+fromObject).find('select').formSelect();
		}
				
		$('#frmModuleSearch').html('');
		$('#frmModuleSearch').append('<input type="hidden" name="txtPageNumber" id="txtPageNumber" value="'+intpageNumber+'" />');
		
		if($('#'+fromObject).length > 0){		
			$('#'+fromObject).find('input').each(function(){
				if((($(this).attr('type') == 'checkbox') || ($(this).attr('type') == 'radio')) && ($(this).attr('checked'))){
					$('#frmModuleSearch').append('<input type="hidden" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'" value="'+$(this).val()+'" />');
				}else if((($(this).attr('type') != 'checkbox') && ($(this).attr('type') != 'radio'))){
					$('#frmModuleSearch').append('<input type="hidden" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'" value="'+$(this).val()+'" />');
				}
			});
			
			$('#'+fromObject).find('select').each(function(){
				if($(this).attr('name')){
					$('#frmModuleSearch').append('<input type="hidden" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'" value="'+$(this).val()+'" />');
				}
			});
			
			$('#'+fromObject).find('textarea').each(function(){
				if($(this).attr('name')){
					$('#frmModuleSearch').append('<input type="hidden" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'" value="'+$(this).val()+'" />');
				}
			});
		}
		
		$('#frmModuleSearch').submit();
	}
}

/**************************************************************************
 Purpose 		: Initialization.
 Inputs  		: None.
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function init(){
	$('.modal').modal();
	$('select').formSelect();
	$('checkbox').formSelect();
	$('ul.tabs').tabs();
	$('input#input_text, textarea#textarea1, .materialize-textarea-data-length').characterCounter();
	$(".button-collapse").sidenav();
	
	$('.datepicker').datepicker({
		selectMonths: true, // Creates a dropdown to control month
		selectYears: 15, // Creates a dropdown of 15 years to control year,
		today: 'Today',
		clear: 'Clear',
		close: 'Ok',
		format:'yyyy/mm/dd',
		closeOnSelect: false // Close upon selecting a date,
		,container: 'body',
	});

	var d = new Date();
	d.setFullYear( d.getFullYear() + 15 );
	$('.datepickerAm').datepicker({
		selectMonths: true, // Creates a dropdown to control month
		selectYears: true, // Creates a dropdown of 15 years to control year,
		today: 'Today',
		clear: 'Clear',
		close: 'Ok',
		format:'yyyy/mm/dd',
		closeOnSelect: false, // Close upon selecting a date,
		container: 'body',
		minDate: new Date(),
		defaultDate: new Date(),
		min: new Date(),
		max: d,
	});

	$('.timepicker').timepicker({
		default: 'now', // Set default time: 'now', '1:30AM', '16:30'
		fromnow: 0,       // set default time to * milliseconds from now (using with default = 'now')
		twelvehour: false, // Use AM/PM or 24-hour format
		donetext: 'OK', // text for done-button
		cleartext: 'Clear', // text for clear-button
		canceltext: 'Cancel', // Text for cancel-button
		autoclose: false, // automatic close timepicker
		ampmclickable: true, // make AM PM clickable
		aftershow: function(){} //Function for after opening timepicker
		,container: 'body',
	});
	
	/* Setting tool-tips */
	$('.tooltipped').tooltip({delay: 50});
	
	$('.divGridBoxs').mouseenter(function(){
		$(this).find('.fixed-action-btn-card-container').slideDown();
		
	}).mouseleave(function(){
		$(this).find('.fixed-action-btn-card-container').slideUp();
	});
	
	/* Register the events */
	setPullDownEvents();
	showItemOnChangeEvent();

	$('.dropdown-trigger').dropdown();
	
	/* Set device layout */
	getCardLayout('tbl-data-set','device-body-container');
	
	/* Set action */
	setActiveDeactiveAction();
}

/**************************************************************************
 Purpose 		: Opening the model in edit case.
 Inputs  		: pModelRefenceObject :: model reference object.,
				: pIntRecordCode :: Record then needs to be edit,
				: isEdit :: is edit request.
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R.
/**************************************************************************/
function openEditModel(pModelRefenceObject, pIntRecordCode, isEdit){
	var blnisHookRequest	= (pModelRefenceObject.indexOf('hooks-') >=0)?true:false;
	pModelRefenceObject		= pModelRefenceObject.replace('hooks-','');
	var objFrom				= $('#'+$('#'+pModelRefenceObject).find('form').attr('id'));
	
	/* Mass updated */
	if(pIntRecordCode == 'selected'){	
		/* Iterating the code */
		if($('input[name="chkLeadCode[]"]:checked').length  == 0){
			showToast('Atleast one lead should selected.');
		}else{
			var strleadArr	= strLeadCode	= strLeadOwnerCode = '';
			
			$('input[name="chkLeadCode[]"]:checked').each(function (){
				var strleadArr	= $(this).val().split(DELIMITER);
				if(strLeadCode == ''){
					strLeadCode			= strleadArr[0];
					strLeadOwnerCode	= strleadArr[1];
				}else{
					strLeadCode			= strLeadCode + DELIMITER + strleadArr[0];
					strLeadOwnerCode	= strLeadOwnerCode + DELIMITER + strleadArr[1];
				}
			});
			
			$(objFrom).find('#txtLeadCode').val(strLeadCode);
			$(objFrom).find('#txtLeadOwnerCode').val(strLeadOwnerCode);
			$('#'+pModelRefenceObject).modal('open');
		}
		return false;
	/* Mass updated */
	}else if(pIntRecordCode == 'dashboard'){
		$('#'+pModelRefenceObject).modal('open');
	}else{
		
		/* if multiple value need to pass with post method then do needful */
		if(pIntRecordCode.indexOf(DELIMITER) >0 ){
			/* Variable initialization */
			var strParamertsArr	= pIntRecordCode.split(DELIMITER);
			/* Value over writing */
			pIntRecordCode		= strParamertsArr[0];
			
			/* Iterating the loop */
			for(var intCounterForLoop = 1; intCounterForLoop < (pIntRecordCode.length)-1; intCounterForLoop++){
				/* if index exists then do needful */
				if(strParamertsArr[intCounterForLoop]){
					/* getting field name and value array */
					var strFieldArr	= strParamertsArr[intCounterForLoop].split(':');
					/* setting field name and value array */
					$('#frmGetDataByCode').append('<input type="hidden" name="'+strFieldArr[0]+'" id="'+strFieldArr[0]+'" value="'+strFieldArr[1]+'" />');
				}
			}
		}

		if($('#blnCSV').val() != '1'){
			$('#'+pModelRefenceObject).modal('open');
		}
		
		$('#txtDeleteRecordCode').val(pIntRecordCode);
		$('#txtEventAssocationFlag').remove();
		$('#txtCode').val(pIntRecordCode);
		$('.cmdSearchReset').addClass('hide');
		$('.no-search').removeClass('hide');
		$('.no-add').addClass('hide');
		objectRefrence	= null;
		
		/* checking widget and module association screen */
		if($('.div-widget-attributes-list').length > 0){
			/* initialization of the container */
			$('.div-widget-attributes-list').html('');
		}
		
		if(($('.addItemInModule').length == 0) && ($('#txtRoleCode').length > 0)){
			$('#txtRoleCode').val(pIntRecordCode);
		}
		
		if($('#txtModuleFieldCode').length > 0){
			$('#txtModuleFieldCode').remove();
		}
		
		if($('#txtEventRoleCode').length > 0){
			$('#txtEventRoleCode').val(pIntRecordCode);
		}
		
		if('divFieldMapping' == pModelRefenceObject){
			$('#txtModuleFieldCode').val(pIntRecordCode);
			$('#frmGetDataByCode').append('<input type="hidden" name="txtModuleFieldCode" id="txtModuleFieldCode" value="'+pIntRecordCode+'" />');
			$('#frmModulesfieldMapping').append('<input type="hidden" name="txtModuleFieldCode" id="txtModuleFieldCode" value="'+pIntRecordCode+'" />');
		}
		
		if('divAPIDetailsModel' == pModelRefenceObject){
			$('#frmGetDataByCode').append('<input type="hidden" name="txtDetailView" id="txtDetailView" value="true" />');
		}
		
		switch(parseInt(isEdit)){
			case 1:
				showLoader();
				postUserRequest('frmGetDataByCode');
				objectRefrence	= pModelRefenceObject;
				$('.spnActionText').html('Edit');
				break;
			case 2:
				if($('#frmLeadsColumnSearch').length == 1){
					objFrom	= 'frmAddNewLead';
					$('.cmdDMLAction').attr('formname','frmAddNewLead');
				}
				$('.spnActionText').html('Add New');
				$(objFrom)[0].reset();
				var strMailCode	= '';
				if($('#eMaIlCoDe').length > 0){
					strMailCode	= $('#eMaIlCoDe').val();
				}
				$(objFrom).find('input[type=hidden]').val('');
				$('#eMaIlCoDe').val(strMailCode);
				$(objFrom).find('select').formSelect();
				break;
			case 3:
				
				if($('#frmLeadsColumnSearch').length == 1){
					objFrom	= 'frmLeadsColumnSearch';
					$('.cmdDMLAction').attr('formname','frmLeadsColumnSearch');
					$('#'+objFrom).find('input[id="txtSearch"]').val('1');
				}else{
					$(objFrom).find("#txtSearch").val('1');
				}
				
				if(('leadModules' == pModelRefenceObject) || ('taskModules' == pModelRefenceObject)){
					objFrom	= $('#frmLeadsColumnSearch');
				}
				$('.spnActionText').html('Search');
				$('.cmdSearchReset').removeClass('hide');
				$('.no-search').addClass('hide');
				$('.no-add').removeClass('hide');
				if($('#txtSearchFilters').html() != ''){
					$(objFrom).find(':input:enabled:visible:first').focus();
					var strSearchArr	= jQuery.parseJSON($('#txtSearchFilters').html());  
					$.each(strSearchArr, function(strElementRefObj, strElementValue){
						if (strElementRefObj == 'widgetData' && $.isPlainObject(strElementValue)) {
							$.each(strElementValue, function(strElementValueObj, strElementValueValue){
								$(objFrom).find('#txtWidget'+strElementValueObj).val(strElementValueValue);
							});
						}else{
							$(objFrom).find('#'+strElementRefObj).val(strElementValue);
						}
					});
					$(objFrom).find('select').formSelect();
				}
				
				if($('#frmLeadsColumnSearch').length == 1){
					$('#'+objFrom).find('input[id="txtSearch"]').val('1');
				}else{
					$(objFrom).find("#txtSearch").val('1');
				}
				
				break;
			/* Processing Hooks widget Request  */
			case 4:
				if($('#'+pModelRefenceObject).attr('data-load-from-target')){
					showLoader();
					$('#frmHookDataProcess').attr('action',$('#'+pModelRefenceObject).attr('data-load-from-target'));
					$('#frmHookDataProcess').append('<input type="hidden" name="txtHookModuleActionCode" id="txtHookModuleActionCode" value="'+pIntRecordCode+'" />');
					postUserRequest('frmHookDataProcess');
					objectRefrence	= pModelRefenceObject;
				}
				break;
			case 5:
				$('#txtUserFeedComments').val($('#'+pIntRecordCode).find('.userFeed').html());
				$('#txtFeedUserName').val($('#'+pIntRecordCode).find('.userName').html());
				$('#txtUserFeedCode').val(pIntRecordCode);
				break;
		}
	}
	M.updateTextFields();
}

$(document).ready(function(){
	init();
	/* Submitting the from */
	$('#cmdLogin, #cmdCompanyRegister, #cmdStatusManagment, #cmdDeleteRecord, .cmdDMLAction').click(function(){
		
		/* Checking for custom attributes */
		var strFormName = $(this).attr('formName');
		/* Checking attributes values */
		if (typeof strFormName == typeof undefined || strFormName == false) {
			/* Displaying error message */
			showToast('formName attributes is missing on Action button.');
		}else if(strFormName == "frmExportData"){
			downloadCSV();
		}else if(strFormName == "frmDashboard"){
			$('#'+strFormName).submit();
		}else{
			showLoader();
			clearAllToast();
			if($('#'+strFormName).find('input[id="txtSearch"]').val() == '1'){
				goToPage(0,strFormName, false);
				//$('#'+strFormName).submit();
				return;
			}else if($('#'+strFormName).find('input[type="file"]')){
				postUserDocumentRequest(strFormName);
			}else{
				postUserRequest(strFormName);
			}
		}
		return false;
	});


	$('#cmdEventCodeVerify').click(function(){

		/* Checking for custom attributes */
		var strFormId = $(this).attr('formName');

		var strDestionPath	= $('#'+strFormId).attr('action');

		var formElem = $('form#'+strFormId);

		$.ajax({
			type: "POST",
			url: strDestionPath,
			data:new FormData(formElem[0]),
			processData:false,
			contentType:false,
			dataType: "json",
			cache:false,
			async:false,
			success: function(output) {
				showToast(output.message);
				if (output.requestProcessStatus) {
					//window.location.replace(output.redirect_url);
					window.location = output.redirect_url;
				}else{
					formElem[0].reset();
				}
			}
		});

		return false;
	});


	/* Setting search filter */
	$('.cmdSearchReset').click(function(){
		
		$('.maintain').each(function(){
				$('#frmModuleSearch').append('<input type="hidden" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'" value="'+$(this).val()+'" />')
		});
		$('#frmModuleSearch').submit();
	});
	
	/* Filed Mapping */
	$('#cmdLeadAttributeAdding').click(function(){
		if($('#cboLeadAttributeCode').val() != ''){
			var leadAttributeObj	= $('#cboLeadAttributeCode option:selected');
			$('#tblLeadAttribute').append(addFieldRowInTable($(leadAttributeObj).val(), $(leadAttributeObj).text()));
			$(leadAttributeObj).remove();
			$('#cboLeadAttributeCode').formSelect();
		}
	});
	
	/* Select all leads of page */
	$('#chkBoxSelectAllLeads').click(function(){
		var blnIsCheked	= $(this).is(':checked');
		$('input[name="chkLeadCode[]"]').each(function(){
			$(this).removeAttr('checked');
			if((blnIsCheked) && (!$(this).is(':disabled'))){
				$(this).attr('checked','checked');
			}
		});
	});
	
	/* Checking for action items */
	if($('.dlActionList li').length == 0){
		$('.aActionContainer').hide();
	}

	$("body #social-wall-grid").on( "click", '.likeUserFeed', function() {
		var userFeedIdEnc 	= $(this).attr('userFeedId');
		var cellNumber 		= $(this).attr('cellNumber');

		/**/
		$.ajax({
			method: "GET",
			url: "/social-wall/like-user-feed/" + userFeedIdEnc,
			data: { 'user-feed-id': userFeedIdEnc },
			dataType: "json",
			async: true,
			cache: false,
		})
		.done(function( responseJson ) {
			console.log(responseJson);
			if (responseJson.requestProcessStatus) {
				//$("#like_cnt_"+userFeedIdEnc).html(responseJson.likes_count);
				loadFeedByCellNumber(cellNumber);
			}else{
				alert("Sorry, Something went wrong !");
			}
		});
		/**/

	});

	$("body .user-feed-and-comment-form").on( "click", '.likeUserFeed', function() {
		var userFeedIdEnc 	= $(this).attr('userFeedId');

		$.ajax({
			method: "GET",
			url: "/social-wall/like-user-feed/" + userFeedIdEnc,
			data: { 'user-feed-id': userFeedIdEnc, 'like_cnt': true },
			dataType: "json",
			async: true,
			cache: false,
		})
		.done(function( responseJson ) {
			console.log(responseJson);
			if (responseJson.requestProcessStatus) {
				$("#like_cnt_"+userFeedIdEnc).html(responseJson.likes_count);
			}else{
				alert("Sorry, Something went wrong !");
			}
		});
		/**/

	});
	
	$("body .user-feed-and-comment-form").on( "click", '.likeUserFeedPublic', function() {
		var userFeedIdEnc 	= $(this).attr('userFeedId');

		$.ajax({
			method: "GET",
			url: "/event/like-user-feed/" + userFeedIdEnc,
			data: { 'user-feed-id': userFeedIdEnc, 'like_cnt': true },
			dataType: "json",
			async: true,
			cache: false,
		})
		.done(function( responseJson ) {
			console.log(responseJson);
			if (responseJson.requestProcessStatus) {
				$("#like_cnt_"+userFeedIdEnc).html(responseJson.likes_count);
			}else{
				alert("Sorry, Something went wrong !");
			}
		});
		/**/

	});
	
	/* setting the floating button */
	if($('.cell_index_data').length > 0 ){ 
		$('.event-edit').click(function(){
			var strActionPalenObj	= $(this).parent().parent().find('ul');
			var intCellCounter		= $(strActionPalenObj).attr('actionset');
			$(this).show();
			
			if(!$(strActionPalenObj).is(":visible")){
				$(strActionPalenObj).show();
				$(this).find('i').text('close');
			}else{
				$(strActionPalenObj).hide();
				$(this).find('i').text('mode_edit');
				
				if($(strActionPalenObj).hasClass('contentPreset')){
					$(this).hide();
					$('#reset_edit_btn_'+intCellCounter).show();
				}
			}
		});
	}
	
	/* Submitting the Event Grid from */
	$('.cmsAddUpdateGridBox').click(function(e){
		var strFormId 			= $(this).attr('formId');
		var intCellIndex 		= $(this).attr('cellindex');
		var intModalId 			= $(this).attr('modal_id');
		var strActionURLArr		= $('#'+strFormId).attr('action').split('/');
		
		if(strActionURLArr[6] == ''){
			strActionURLArr[6]	= intCellIndex;
		}
		strActionURLArr			= strActionURLArr.join('/');
		$('#'+strFormId).attr('action',strActionURLArr);
		
		if($('#'+strFormId).attr('enctype')){
			postUserDocumentRequest(strFormId);
		}else{
			postUserRequest(strFormId);
		}
		return false;
	});

	drawChart();

	/*
	$('.datepicker').datepicker({
		container: 'body'
	});
	$('.timepicker').timepicker();
	*/

});

/**************************************************************************
 Purpose 		: Navigate the page to destination with key value.
 Inputs  		: strDestiantionURl : Destination URL,
				: strJSONEKeyValuepariString : Key value pair string
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function setNavigation(strDestiantionUrl, strJSONEKeyValuepariString){
	/* Converting keyValue String to Array */
	var strValueArr	= (strJSONEKeyValuepariString).split('~V~');
	var strReturnString = '<form name="frmNavigatetoDestination" id="frmNavigatetoDestination" method="post" action="'+strDestiantionUrl+'">';
	
	/* Iterating the loop */
	$.each(strValueArr , function(strElementKey , strElementValue){
		var strKeyValue = strElementValue.split('=>');
		strReturnString +='<input type="hidden" name="'+strKeyValue[0]+'" id="'+strKeyValue[0]+'" value="'+strKeyValue[1]+'" />';
	});
	
	/* Closing the form */
	strReturnString +='</form>';
	
	$(document).find('body').append(strReturnString);
	$('#frmNavigatetoDestination').submit();
}


/**************************************************************************
 Purpose 		: Creating the dependency data fill method.
 Inputs  		: pObjectRefrence : Action object reference name,
				: pDestinationObject : Object data needs to fill,
				: pExtraParameters :: Extra parameter
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function getDependencyData(pObjectRefrence, pDestinationObject, pExtraParameters){
	/* Variable initialization */
	var strSelectValueString = $(pObjectRefrence).val();
	/* Setting the mandatory */
	$('#frmCustom').append('<input type="hidden" name="txtDataCodes" id="txtDataCodes" value="'+strSelectValueString+'" />');
	/* Setting optional parameter */
	if(pExtraParameters != ''){
		/* setting parameter */
		$('#frmCustom').append('<input type="hidden" name="txtExtraParam" id="txtExtraParam" value="'+pExtraParameters+'" />');
	}
	objectRefrence	= pDestinationObject;
	postUserRequest('frmCustom');
}

/**************************************************************************
 Purpose 		: Remove options / row from table.
 Inputs  		: objRefrence : Row reference 
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function removeOptions(objRefrence){
	var currentRowRefrence = $(objRefrence).parent().parent();
	var strLabel		   = $(currentRowRefrence).find('span').html();
	var strValue		   = $(currentRowRefrence).find('input').val();
	$(currentRowRefrence).remove();
	$('#cboLeadAttributeCode').append('<option value="'+strValue+'">'+strLabel+'</option>').formSelect();
}

/**************************************************************************
 Purpose 		: Adding attributes field .
 Inputs  		: pAttriniteCode : Attribute code,
				: pStrAttributeName :: Attribute Name.
 Return 		: ROW HTML.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function addFieldRowInTable(pAttriniteCode , pStrAttributeName){
	return '<tr><td><input type="hidden" id="txtFiledCode[]" name="txtFiledCode[]" value="'+pAttriniteCode+'" /><span>'+pStrAttributeName+'</span></td><td><a href="javascript:void(0);" onclick="removeOptions(this);" class="waves-effect waves-circle waves-light btn-floating secondary-content red"><i class="material-icons">delete</i></a>&nbsp;</td></tr>';
}

/**************************************************************************
 Purpose 		: Adding the pull down event on change event based on object.
 Inputs  		: pFormRefrence :: Form Reference.
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function setPullDownEvents(){
	/* Checking the select object to check the dependency */
	$('select').each(function(){
		/* checking the dependency the target element */
		if($(this).attr('check-dependency') && $(this).attr('dependency-element')){
			var strAction	= $(this).attr('check-dependency');
			objectRefrence	= $(this).attr('dependency-element');
			$(this).bind('change',function(){
				$('#frmDynamicEventDataSet').append('<input type="hidden" name="txtRegionCode" id="txtRegionCode" value="'+$(this).val()+'" />');
				$('#frmDynamicEventDataSet').attr('action',SITE_URL+'leadsoperation/leadsoperation/'+strAction);
				postUserRequest('frmDynamicEventDataSet');
				
				/* if current drop down is lead transfer region or branch box then do below */
				if($(this).attr('id') == 'cboTransferRegionCode'){
					setTimeout(function(){
						$('#frmDynamicEventDataSet').attr('action',SITE_URL+'settings/locations/getUserListByLocation');
						objectRefrence	= 'cboUSerCode';
						postUserRequest('frmDynamicEventDataSet');
					},1000);
				}
			});
		}
		
	})
}

/**************************************************************************
 Purpose 		: Setting the lead follow up view based on selected status.
 Inputs  		: pObjectRefrence :: Status element Reference.
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function setFollowUpView(pObjectRefrence){
	var strValue = {'statusCode':$(pObjectRefrence).val()};
	
	postUserRequestVirualForm('frmStatusClassification',strValue,SITE_URL+'leadsoperation/leadsoperation/isOpenStatusCheck');
}

/**************************************************************************
 Purpose 		: Draw chat based on request.
 Inputs  		: None.
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function drawChart(){
	/* Lead Report - Parent status v/s date */
	if($('#divParentStatusVSDateContainer').length > 0){
		setLeadChart();
	}
	
	/* Lead Report - Parent status v/s date */
	if($('#divTaskClassifcationContainer').length > 0){
		setTaskChart();
	}
	
			
}
/**************************************************************************
 Purpose 		: Setting the content visibility based on the selected record.
 Inputs  		: objRefrence = Current object reference,
				: objTargetObjectRerence :: Target object reference.
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function showRelatedRecord(objRefrence, objTargetObjectRerence){
	/* variable initialization */
	var strValue = $(objRefrence).val();
	
	/* no value found then show values from target drop down */
	if(strValue == ''){
		/* Show all values */
		$('#'+objTargetObjectRerence).find('option').show();
	}
	
	/* Iterating the value */
	$('#'+objTargetObjectRerence).find('option').each(function(){
		var strValues	= $(this).attr('value');
		/* if user code from transfer / assign the lead */
		if(objTargetObjectRerence == 'cboUSerCode'){
			strValues	= strValues.split(DELIMITER);
			strValues	= strValues[0];
		}
		
		/* Setting the value */
		if(strValue == strValues){
			$(this).show();
		}else{
			$(this).hide();
		}
		
	});
	
	if(objTargetObjectRerence == 'cboUSerCode'){
		$('#cboUSerCode').formSelect();
	}
}

/**************************************************************************
 Purpose 		: Creating the elements on request.
 Inputs  		: pStrElementType : Element Type,
				: pStrElementName :: ElementName,
				: pStrTargetContiner :: Target container name,
				: pStrDefaultValue :: Default Value
 Return 		: ROW HTML.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function addFormElement(pStrElementType, pStrElementName, pStrTargetContiner, pStrDefaultValue){
	/* variable initialization */
	var strElementHTML = '';
	/* based on the element type generating the HTML tag */
	switch(pStrElementType){
		case 'text':
			strElementHTML	= '<tr><td><input type="text" name="'+pStrElementName+'" id="'+pStrElementName+'" value="'+pStrDefaultValue+'" width="100%"/></td><td><a href="javascript:void(0);" onclick="removeTableRow(this);" class="waves-effect waves-circle waves-light btn-floating secondary-content red"><i class="material-icons">delete</i></a></td></tr>';
			break;
	}
	/* Adding to target element */
	$('.'+pStrTargetContiner).append(strElementHTML);
}

/**************************************************************************
 Purpose 		: Removing the table row on demand.
 Inputs  		: pObjectRefrence :: Table row object instance.
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function removeTableRow(pObjectRefrence){
	/* Removed the row */
	$(pObjectRefrence).parent().parent().remove();
}

/**************************************************************************
 Purpose 		: Show / Hide requested Element.
 Inputs  		: pObjectRefrence :: Object reference,
				: pTargetElement :: elements that's needs to be hidden.
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function displayHideElement(pObjectRefrence, pStrValue, pTargetElement){
	/* Variable initialization */
	var strValueArr		= pStrValue.split(DELIMITER);
	var pTargetElement 	= pTargetElement.split(DELIMITER);
	var intTargetIndex	= 0;
	var intOtherIndex	= 1;
	
	/* if requested value as selected then do needful */
	if((strValueArr.length == 1) && ($(pObjectRefrence).val() == pStrValue)){
		/* Show requested Elements */
		$('.'+pTargetElement).removeClass('hide');
		/* if value is array then */
	}else if(strValueArr.length > 1){
		/* Variable initialization */
		var blnValueFound 	= false;
		/* Iterating the loop */
		$.each(strValueArr, function(strKey, strValue){
			/* Checking the selected value and current index value */
			if($(pObjectRefrence).val() == strValue){
				/* value overwriting */
				blnValueFound	= true;

				/* if file elements is selected then do needful */
				if(strValue == $(pObjectRefrence).attr('data-encry-element')){
					/* Value over writing */
					intTargetIndex = 1;
					intOtherIndex  = 0; 
				}
			}
		});

		/* Checking selected value is in the proposed list */
		if(blnValueFound){
			/* Show requested Elements */
			$('.'+pTargetElement[intTargetIndex]).removeClass('hide');
			$('.'+pTargetElement[intOtherIndex]).addClass('hide');
		}else{
			/* Show requested Elements */
			$('.'+pTargetElement[intTargetIndex]).addClass('hide');
			$('.'+pTargetElement[intOtherIndex]).addClass('hide');
		}
	}else if(pStrValue == '-1'){
		/* Show requested Elements */
		$('.'+pTargetElement[intTargetIndex]).removeClass('hide');
	}else if(pStrValue == '-2'){
		/* Show requested Elements */
		$('.'+pTargetElement[intTargetIndex]).removeClass('hide');
	}else{
		/* Show requested Elements */
		$('.'+pTargetElement[intTargetIndex]).addClass('hide');
	}
}

/**************************************************************************
 Purpose 		: Converting the table into card layout for devices.
 Inputs  		: pObjectRefrence :: Object reference,
				: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function getCardLayout(pObjectRefrence, pContainerObjectRef){
	/* variable initialization */
	var strReturn	= '';
	
	/* if requested object exists then do needful  */
	if($('.'+pObjectRefrence).length > 0){
		var strBody	= '';
		/* Iterating the header */
		$('.'+pObjectRefrence).find('thead').find('th').each(function(key){
			if($(this).find('input').length == 1){
				
			}else{
				/* Creating the header */
				strBody	= strBody + '<div class="row">';
				strBody	= strBody + '<div class="col s5 m5">'+$(this).html()+'</div>';
				strBody	= strBody + '<div class="col s7 m7">{'+key+'}</div>';
				strBody	= strBody + '</div>';
			}
		});
		/* Iterating the table body */
		$('.'+pObjectRefrence).find('tbody').find('tr').each(function(key){
			/* Creating container */
			strReturn			= strReturn + '<div class="row card-bg-color pt10"><div class="col s12 m12">';
			var strInternalBody	= strBody;
			
			/* Iterating the table cell */
			$(this).find('td').each(function(cellIndex){
				/* Creating record set */
				strInternalBody	= strInternalBody.replace('{'+cellIndex+'}',$(this).html());
				
			});
			strReturn	= strReturn + strInternalBody;
			/* Closing container */
			strReturn	= strReturn + '</div></div>';
		});
		
		/* setting the device card structure */
		$('#'+pContainerObjectRef).html(strReturn);
	}
}

/**************************************************************************
 Purpose 		: Displaying items on select element change event.
 Inputs			: None.
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function showItemOnChangeEvent(){
	/* Checking the select object to check the dependency */
	$('select').each(function(){
		/* checking the dependency the target element */
		if($(this).attr('is-change-event') && $(this).attr('action')){
			var strAction	= $(this).attr('action');
			//objectRefrence	= $(this).attr('is-change-event');
			$(this).bind('change',function(){
				$('#frmDynamicEventDataSet').append('<input type="hidden" name="txtElementCode" id="txtElementCode" value="'+$(this).val()+'" />');
				/* if module and widget attributes configuration module exist then do needful */
				if($('#cboLeadAttributeCode').length > 0){
					$('#frmDynamicEventDataSet').append('<input type="hidden" name="txtModuleFieldCode" id="txtModuleFieldCode" value="'+$('#txtModuleFieldCode').val()+'" />');
				}
				$('#frmDynamicEventDataSet').attr('action',SITE_URL+'/'+strAction);
				postUserRequest('frmDynamicEventDataSet');
			});
		}
	});
}

/**************************************************************************
 Purpose 		: Sending request for exporting the records of the modules.
 Inputs			: None.
 Return 		: None.
 Created By 	: Vipin Kumar R. Jaiswar.
/**************************************************************************/
function downloadCSV(){
    $('#frmWidget').append('<input type="hidden" name="blnCSV" id="blnCSV" value="1" />');
	goToPage(0,"frmWidget", true);
}

/**************************************************************************
 Purpose 		: Chceking for Json String.
 Inputs			: str :: String.
 Return 		: Json String else error.
 Created By 	: Vipin Kumar R. Jaiswar.
/**************************************************************************/
function isJson(str) {
	try {
		JSON.parse(str);
	} catch (e) {
		return false;
	}
	return true;
}


/**************************************************************************
 Purpose 		: DIsabliing the action by=utton.
 Inputs			: None.
 Return 		: Json String else error.
 Created By 	: Vipin Kumar R. Jaiswar.
/**************************************************************************/
function setActiveDeactiveAction() {
	/* if requested contains exists then do do needful */
	if($('.actionValudate').length > 0){
		$('.responsive-table').find('a').each(function(){
			if(($(this).attr('isvalue') == '') || ($(this).attr('isvalue') == '0') || ($(this).attr('isvalue') == '-')){
				$(this).attr('href','javascript:void(0);');
				$(this).addClass('disabled');
			}
		});
	}
}