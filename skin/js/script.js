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
		showToast(objResponse.message);
	}else{
		$.fancybox.hideLoading();
		switch(pStrFormName){	
			case 'frmUserFeed':
				$('.user-feed-ajax-feeder').html(objResponse);
				return false;		
				break;
			case 'frmUserFeedManagement':
				$('#frmUserFeedManagement')[0].reset();
				break;
			case 'frmUserFeedLike':
				$("#alikeCount-"+objResponse.userFeedIdEnc).removeClass('grey-text').addClass('blue-text text-lighten-2');
				$("#likeCount-"+objResponse.userFeedIdEnc).html(objResponse.likeCount).removeClass('grey-text').addClass('blue-text text-lighten-2');
				return false;
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
 Purpose 		: Initialization.
 Inputs  		: None.
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function init(){
	//$('.modal').modal();
	$('input#input_text, textarea#textarea1, .materialize-textarea-data-length').characterCounter();
	M.updateTextFields();
	$('.modal').modal();
	
	var carousel =  $('.carousel').carousel({
		fullWidth: false,
		indicators: false,
		duration: 200,
	});
	
	setTimeout(function(){
		carousel.next()
	},3000);
	
	if($('.datepickerAm').length > 0){
		$('.datepickerAm').datepicker({
			container: 'body'
		});
	}
	
}

$(document).ready(function(){
	init();
	/* Submitting the from */
	$('#cmdEventCodeVerify, .cmdDMLAction').click(function(){
		
		/* Checking for custom attributes */
		var strFormName = $(this).attr('formName');
		/* Checking attributes values */
		if (typeof strFormName == typeof undefined || strFormName == false) {
			/* Displaying error message */
			showToast('formName attributes is missing on Action button.');
		}else if(strFormName == "frmExportData"){
			downloadCSV();
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
 
	/* if user feed screen view then do needful */
	if(($('.user-feed-ajax-feeder').length > 0) && (blnDoNotProcess)){
		/* calling the user feed method */
		getUserFeedsView('frmUserFeed');
	}else{
		if ((typeof blnDoNotProcess === 'undefined') && (typeof intNumberOfRows != 'undefined')){
			setGrid();
			startTime();
			
			setTimeout(function(){
				loadFeeder();
			},1000);
		}
	}
	
	if((typeof eventCodeEnc != 'undefined') ){
		setTimeout(function(){
			checkEventAlertMsg(eventCodeEnc);
		},5000);
	}
	
	$('.fEventEnterInput').focusin(function(){
		$(this).attr('placeholder','');
	}).focusout(function(){
		if($(this).val() == ''){
			$(this).attr('placeholder','000000');
		}
	})
	
	M.updateTextFields();
	
});

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
 Purpose 		: Post the request.
 Inputs  		: pStrFormName :: From name,
				: pStrParam	:: Parameters Name
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function getUserFeedsView(pStrFormName, pStrParam){
	$('#'+pStrFormName).find('#txtUserFeedsSortOrder').val(pStrParam);
	$.fancybox.showLoading();
	postUserRequest(pStrFormName);
	
	if(pStrParam == 'likes'){
		$('.recent_sort').removeClass('fBorder');
		$('.liked_sort').addClass('fBorder');
	}else{
		$('.recent_sort').addClass('fBorder');
		$('.liked_sort').removeClass('fBorder');
	}
}

/**************************************************************************
 Purpose 		: Upvoating.
 Inputs  		: pStrUserFeedCode :: User feed code.
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R
/**************************************************************************/
function SetUserUpvoat(pStrUserFeedCode){
	$('#txtUserFeedsCode').val(pStrUserFeedCode);
	postUserRequest('frmUserFeedLike');
}

function setGrid(){
	/* variable initialization */
	var intTotalNumberOfCell = intNumberOfRows * intNumberOfCols ; 
	/* iterating the loop */
	for(var intCounter = 1; intCounter <=intTotalNumberOfCell;  intCounter++){
		/* Set the loader */
		$('#col_'+intCounter+' > #cell_index_data_'+intCounter).html('<div class="preloader-wrapper small active" style="margin: auto;"><div class="spinner-layer spinner-green-only"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div></div>')
	}
}

function loadFeeder(){
	for (var i = 1; i <= gridBox; i++) {
		  loadFeedByCellNumber(i);
	}
}

function loadFeedByInterval(){

	$.ajax({
		  method: "GET",
		  url: "social_wall/load-grid-all-cell-data/1",
		  data: { 'event-code': "1" },
		  dataType: "json",
		  async: false,
		  cache: false,
	})
	.done(function( msg ) {
		  //alert( "Data Saved: " + msg );
		  //console.log(msg[0].content);

		  $.each(msg, function( index, value ) {
				//alert( index + ": " + value );
				console.log(value);
				if ($('.box_'+(index+1)).attr("feed-id") == value.id) {
					  return true; //continue; //This is same as 'continue'
				}
				$('.box_'+(index+1)).fadeOut(10);
				var contentParse = $.parseJSON(value.content);
				console.log(contentParse.oembed.html);
				$('.box_'+(index+1)).html(contentParse.oembed.html);
				$('.box_'+(index+1)).fadeIn(3000);
				$('.box_'+(index+1)).attr("feed-id", value.id);
		  });

	});

}

/**************************************************************************
 Purpose 		: Loading the feed by cell content.
 Inputs  		: cellNumber :: Cell number.
 Return 		: None.
 Created By 	: Vipin Kumar R. Jaiswar.
/**************************************************************************/
function loadFeedByCellNumber(cellNumber){
	$.ajax({
		method: "GET",
		url: "/getGridData/" + eventCodeEnc + "/" + gridIdEnc + "/" + cellNumber,
		data: { 'event-code': eventCodeEnc, 'cell-numer': cellNumber },
		dataType: "html",
		async: true,
		cache: false,
	}).done(function( responseHtml ) {
		$("#col_" + cellNumber + " > #cell_index_data_" + cellNumber).html(responseHtml);
		init();
		setTimeout(function(){
			$('.slider').slider({
					height: $("#col_" + cellNumber + " > #cell_index_data_" + cellNumber).height(),
					indicators: false,
					interval: 12000,
					duration: 500
			  });
		},1000);
	});
}


/**/
/**************************************************************************
 Purpose 		: Show Time Clock In Front End
 Inputs  		: 
 Return 		: None.
 Created By 	: Vipin Kumar R. Jaiswar.
/**************************************************************************/

function checkTime(i) {
    if (i < 10) {
        i = "0" + i;
    }
    return i;
}

function startTime() {
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    // add a zero in front of numbers<10
    m = checkTime(m);
    s = checkTime(s);
    document.getElementById('time').innerHTML = h + ":" + m + ":" + s;
    t = setTimeout(function () {
        startTime()
    }, 500);
}

/**************************************************************************
 Purpose 		: Check and Load Event Alert Message if active as per current date time
 Inputs  		: eventCodeEnc :: Event Code.
 Return 		: None.
 Created By 	: Vipin Kumar R. Jaiswar.
/**************************************************************************/
function checkEventAlertMsg(eventCodeEnc){
	$.ajax({
		method: "GET",
		url: "event/get-active-alert-message-by-event-code/" + eventCodeEnc,
		data: { 'event-code': eventCodeEnc },
		dataType: "json",
		async: true,
		cache: false,
	}).done(function( responseJson ) {

		if (responseJson.requestProcessStatus) {
			if (JSON.stringify(responseJson.alert_message) !== JSON.stringify(alertMsgData)) {

				$('#divAlertMsgContainer').modal({
					dismissible: false, // Modal can be dismissed by clicking outside of the modal
					opacity: 0.9, // Opacity of modal background
					/*
					inDuration: 300, // Transition in duration
					outDuration: 200, // Transition out duration
					*/
					startingTop: '4%', // Starting top style attribute
					endingTop: '10%', // Ending top style attribute
				});

				$('#divAlertMsgContainer').modal('open');
				/* console.log('Modal Opened !'); */
				alertMsgSet = true;
				alertMsgData = responseJson.alert_message;
				$("#divAlertMsgContainer h4 span").html(responseJson.alert_message.alert_message);
				/* $("#divAlertMsgContainer").attr('style','max-height: 100%; transform: translateY(50%) !important;'); */
			}
		}else{
			if (alertMsgSet) {
				loadFeeder();
				$("#alert_msg_text").html("");
				$('#divAlertMsgContainer').modal();
				$('#divAlertMsgContainer').modal('close');
				console.log('Modal Closed !');
			}
			alertMsgSet = false;
			alertMsgData = {};
		}

		setTimeout(function(){
			checkEventAlertMsg(eventCodeEnc);
		},5000);

	});
}

/**************************************************************************
 Purpose 		: Set timeout refresh for cell
 Inputs  		: cellNumber :: Cell Number.
 		  		: refresh_timeout :: TimeOut in second.
 Return 		: None.
 Created By 	: Vipin Kumar R. Jaiswar.
/**************************************************************************/
function setTimeoutForCell(cellNumber, refresh_timeout){

	setTimeout(function(){
		loadFeedByCellNumber(cellNumber);
	}, ((refresh_timeout)*1000)); // milliseconds

}

/**************************************************************************
 Purpose 		: Open the dialog.
 Inputs  		: pStrModelCode :: Model identifier.
 Return 		: None.
 Created By 	: Jaiswar Vipin Kumar R..
/**************************************************************************/
function openDialog(pStrModelCode){
	/* Open model */
	$('#'+pStrModelCode).modal('open');
}