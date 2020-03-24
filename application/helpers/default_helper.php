<?php 
/**********************************************************************/
/*Purpose 	: Print requested array / string on console.
/*Inputs	: $pStrDataArr :: requested array / string,
			: $pBlnExit	:: Terminate the execution.
/*Returns 	: None.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function debugVar($pStrDataArr = array(), $pBlnExit = false){
	echo '<pre>';
	print_r($pStrDataArr);
	echo '</pre>';
	/* if termination request then do needful */
	if($pBlnExit){
		exit;
	}
}

/**********************************************************************/
/*Purpose 	: convert the array in JSON and response to caller.
/*Inputs	: $pStrDataArr :: requested array / string,
			: $pBlnExit	:: Terminate the execution.
/*Returns 	: None.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function jsonReturn($pStrDataArr = array(), $pBlnExit = false){
	/* if status code passed with message then do needful */
	if(isset($pStrDataArr['status']) && isset($pStrDataArr['message'])){
		/* Set the new key */
		$pStrDataArr['requestProcessStatus']	= $pStrDataArr['status'];
		/* remove the status key */
		unset($pStrDataArr['status']);
	}
	/* if termination request then do needful */
	if($pBlnExit){
		/* convert array in json format */
		die(json_encode($pStrDataArr));
	}else{
		/* return the JSON encoded string to caller */
		return json_encode($pStrDataArr);
	}
}

/**********************************************************************/
/*Purpose 	: Return the loader.
/*Inputs	: None.
/*Returns 	: None.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getLoaderHTML(){
	/* return the loader HTML */
	return '<div class="preloader-wrapper small right hide">
              <div class="spinner-layer spinner-blue-only">
                <div class="circle-clipper left">
                  <div class="circle"></div>
                </div>
                <div class="gap-patch">
                  <div class="circle"></div>
                </div>
                <div class="circle-clipper right">
                  <div class="circle"></div>
                </div>
              </div>
            </div>';
}

/**********************************************************************/
/*Purpose 	: Return the loader.
/*Inputs	: None.
/*Returns 	: None.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getDeleteConfirmation($pStrAction){
	/* return the loader HTML */
	return '<div id="deleteModel" class="modal modal-fixed-footer" style="height: 30% !important;">
    			<div class="modal-content">
      				<h4>Delete Confirmation!!!</h4>
     	 			<p>Are you sure, you want to delete the selected record?</p>
     	 			<form method="post" action="'.$pStrAction.'" name="frmDelete" id="frmDelete">
     	 				<input type="hidden" name="txtDeleteRecordCode" id="txtDeleteRecordCode" value="" />
     	 			</form>
			    </div>
			    <div class="modal-footer">
			    	<a href="javascript:void(0);" class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
			    	<button class="btn waves-effect waves-light red cmdDeleteRecord" type="submit" name="cmdDeleteRecord" id="cmdStatusManagment" formName="frmDelete" >Delete<i class="material-icons right">delete</i></button>
			    </div>
			</div>';
}

/**********************************************************************/
/*Purpose 	: Displaying the form structure.
/*Inputs	: $pStrHelpsText :: Contains Helps text.
/*Returns 	: HTML Structure.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getWidgetFormHelpTextHTML($pStrHelpsText){
	/* Variable initialization */
	$srtReturnHTMLTemplate = '';
	
	/* if not help text pass the do needful */
	if(trim($pStrHelpsText) == ''){
		/* return the default html body */
		return $srtReturnHTMLTemplate;
	}else{
		/* Return the html struture */
		$srtReturnHTMLTemplate	= '<p class="help-text"><i class="material-icons Tiny"></i>'.$pStrHelpsText.'</p>';
	}
	/* return the hrml text */
	return $srtReturnHTMLTemplate;
}

/**********************************************************************/
/*Purpose 	: Get Edit form for getting the requested details.
/*Inputs	: $pStrAction :: get data by code URL.
/*Returns 	: Edit Form HTML.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getEditContentForm($pStrAction){
	return '<form method="post" action="'.$pStrAction.'" name="frmGetDataByCode" id="frmGetDataByCode">
 				<input type="hidden" name="txtCode" id="txtCode" value="" />
 			</form>';
}

/**********************************************************************/
/*Purpose 	: Get data after filter.
/*Inputs	: $pStrAction :: get data by code URL,
			: $pstrFormName :: Form name.
/*Returns 	: Edit Form HTML.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getFormStrecture($pStrAction, $pstrFormName){
	return '<form method="post" action="'.$pStrAction.'" name="'.$pstrFormName.'" id="'.$pstrFormName.'"></form>';
}

/**********************************************************************/
/*Purpose 	: Return the No Record HTML found.
/*Inputs	: $intNumberOfColSpan	= Number of column span.
/*Returns 	: No record HTML.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getNoRecordFoundTemplate($intNumberOfColSpan = 1){
	return '<tr><td colspan="'.$intNumberOfColSpan.'" class="center">No Record Found.</td></tr>';
}

/**********************************************************************/
/*Purpose 	: Generating random string.
/*Inputs	: $pIntLength :: String length.
/*Returns 	: Random string.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getRamdomeString($pIntLength = 10){
	/* variable initialization */
	$strCharactersSet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	/* getting character set length */
    $intCharactersLength = strlen($strCharactersSet);
    /* variable initialization */
    $strRandomString = '';
    /* Iterating the loop for requested number of time */
    for ($intCounterForLoop = 0; $intCounterForLoop < $pIntLength; $intCounterForLoop++) {
    	/* String creating */
        $strRandomString .= $strCharactersSet[rand(0, $intCharactersLength - 1)];
    }
    /*return the string */
    return $strRandomString;
}

/**********************************************************************/
/*Purpose 	: Generating the array by key value pair.
/*Inputs	: $pstrDataArr :: data array,
			: $pStrKey :: Key value,
			: $pStrValue :: Value,
			: $pBlnEncryptionNeeded :: encryption to the key. default it will be none.
/*Returns 	: Array.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getArrByKeyvaluePairs($pstrDataArr = array(), $pStrKey, $pStrValue,$pBlnEncryptionNeeded=false){
	/* variable initialization */
	$strReturnArr	= array();

	/* if data set is empty then do needful */
	if(empty($pstrDataArr)){
		/* Return empty array */
		return $strReturnArr;
	}

	/* Checking shared key value exists */
	if((!isset($pstrDataArr[0][$pStrKey])) || (!isset($pstrDataArr[0][$pStrValue]))){
		/* Return empty array */
		return $strReturnArr;	
	}

	/* Iterating the loop */
	foreach($pstrDataArr as $pStrDataArrKey => $pStrDataArrValue){
		/* Setting values */
		if(!$pBlnEncryptionNeeded){
			/* Without encrypted */
			$strReturnArr[$pStrDataArrValue[$pStrKey]]	= $pStrDataArrValue[$pStrValue];
		}else{
			/* With encrypted */
			$strReturnArr[getEncyptionValue($pStrDataArrValue[$pStrKey])]	= $pStrDataArrValue[$pStrValue];
		}
	}
    /*return the string */
    return $strReturnArr;
}

/*************************************************************************
/*Purpose	: Generating the pagination HTML.
/*Input		: $pIntNumberOfPecordsArr :: Number of records,
			: $pIntCurrentPageNumber :: Current page number,
			: $strFormName :: From name.
/*Returns	: Pagination HTML
/*Created By: Jaiswar Vipin Kumar R.
/*************************************************************************/
function getPagniation($pIntNumberOfPecordsArr = array(), $pIntCurrentPageNumber = 1, $strFormName = ''){
	/* Number of records */
	$intNumberofRecords	= (!empty($pIntNumberOfPecordsArr) && isset($pIntNumberOfPecordsArr[0]['recordCount']))?$pIntNumberOfPecordsArr[0]['recordCount']:0;
	
	/* if no record found then do needful */
	if($intNumberofRecords == 0){
		return '';
	}
	
	/* Setting number of pages */
	$intNumberOfpages	= ceil($intNumberofRecords / DEFAULT_RECORDS_ON_PER_PAGE);
	
	$strDefaultPageClass	= 'disabled';
	//
	/* Variable initialization */
	$strPagnationHTML	= '<div id="device-body-container" class="hide-on-large-only"></div>
							<ul class="pagination right">
							<!--li class="active green"><a>Total Records : '.$intNumberofRecords.'</a></li-->';
	
	/* if user on second page then do needful */
	if($pIntCurrentPageNumber > 1){
		/* Get previous page number */
		$intPreviousPageNumber	= $pIntCurrentPageNumber - 1;
		/* Checking previous page should not less then first page */ 
		if($intPreviousPageNumber < 1){
			/* Set first page a default */
			$intPreviousPageNumber	= 1;
		}
		/* Setting first page counter */
		$strPagnationHTML	.= '
									<li class="waves-effect"><a href="javascript:void(0);" onClick="goToPage(1,\''.$strFormName.'\',true);"><i class="material-icons">first_page</i></a></li>
									<li class="waves-effect"><a href="javascript:void(0);" onClick="goToPage('.$intPreviousPageNumber.',\''.$strFormName.'\',true);"><i class="material-icons">chevron_left</i></a></li>
								';
	}else{
		/* Setting first page counter */
		$strPagnationHTML	.= '
									<li class="'.$strDefaultPageClass.'"><a href="javascript:void(0);"><i class="material-icons">first_page</i></a></li>
									<li class="'.$strDefaultPageClass.'"><a href="javascript:void(0);"><i class="material-icons">chevron_left</i></a></li>
								';
	}
	
	/* If Number of pages found then show input box */
	/* if($intNumberOfpages > 0){
		//$strPagnationHTML	.= '<li class="active valign-wrapper"><input class="center-align" type="text" style="width: 20px;" value="'.$pIntCurrentPageNumber.'" /></li>';
		$strPagnationHTML	.= '<li class="">'.$pIntCurrentPageNumber.'</li>';
	}else{
		/* Show page number label *
		$strPagnationHTML	.= '<li class="">'.$pIntCurrentPageNumber.'</li>';
	}*/
	
	$strPagnationHTML	.= '<li class=""><a href="javascript:void(0);">'.$pIntCurrentPageNumber.' of '.$intNumberOfpages.'</a></li>';
	
	/* if user on second page then do needful */
	if(($pIntCurrentPageNumber >= 1) && ( $pIntCurrentPageNumber < $intNumberOfpages )) {
		/* Get next page number */
		$intNextPageNumber	= $pIntCurrentPageNumber + 1;
		/* Checking previous page should not less then first page */ 
		if($intNextPageNumber > $intNumberOfpages){
			/* Set first page a default */
			$intNextPageNumber	= $intNumberOfpages;
		}
		/* Setting first page counter */
		$strPagnationHTML	.= '
									<li class="waves-effect"><a href="javascript:void(0);" onClick="goToPage('.$intNextPageNumber.',\''.$strFormName.'\',true);"><i class="material-icons">chevron_right</i></a></li>
									<li class="waves-effect"><a href="javascript:void(0);" onClick="goToPage('.$intNumberOfpages.',\''.$strFormName.'\',true);"><i class="material-icons">last_page</i></a></li>
								';
	}else{
		/* Setting first page counter */
		$strPagnationHTML	.= '
									<li class="'.$strDefaultPageClass.'"><a href="javascript:void(0);"><i class="material-icons">chevron_right</i></a></li>
									<li class="'.$strDefaultPageClass.'"><a href="javascript:void(0);"><i class="material-icons">last_page</i></a></li>
								';
	}
							
	$strPagnationHTML	.= '</ul>';
	
	/* Return Pagination */
	return $strPagnationHTML;
}

/*************************************************************************
/*Purpose	: Getting encrypted value of human readable value.
/*Input		: $strValue :: Value.
/*Returns	: Encryption value
/*Created By: Jaiswar Vipin Kumar R.
/*************************************************************************/
function getEncyptionValue($strValue){
	/* Security Setting */ 
    $strEncryptMethod = "AES-256-CBC";
    $strKey = hash( 'sha256', md5(TOKEN) );
    $strIVValue = substr( hash( 'sha256', TOKEN ), 0, 16 );
	
	/* Encrypting the string */
	return base64_encode( openssl_encrypt( $strValue, $strEncryptMethod, $strKey, 0, $strIVValue ) );
}

/*************************************************************************
/*Purpose	: Getting decrypted value of human readable value.
/*Input		: $strValue :: Value.
/*Returns	: De-Encryption value
/*Created By: Jaiswar Vipin Kumar R.
/*************************************************************************/
function getDecyptionValue($strValue){
	/* Security Setting */ 
    $strEncryptMethod = "AES-256-CBC";
    $strKey = hash( 'sha256', md5(TOKEN) );
    $strIVValue = substr( hash( 'sha256', TOKEN ), 0, 16 );
	
	/* Decrypting the string */
	return openssl_decrypt( base64_decode($strValue), $strEncryptMethod, $strKey, 0, $strIVValue ) ;
}

/*************************************************************************
/*Purpose	: identifying is request is AJAX /POST/ GET type.
/*Input		: None
/*Returns	: TRUE / FALSE
/*Created By: Jaiswar Vipin Kumar R.
/*************************************************************************/
function isAjaxRequest(){
	/* checking is request is ajax */
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){    
		/* return confirmation */
		return true;
	}
	/* return confirmation */
	return false;
}

/*************************************************************************
/*Purpose	: identifying is dataset is JSON type.
/*Input		: $pString :: Input String
/*Returns	: TRUE / FALSE
/*Created By: Vipin Kumar R. Jaiswar.
/*************************************************************************/
function isJSON($pString){
	/* return verification status */
	return is_string($pString) && (is_object(json_decode($pString)) || is_array(json_decode($pString))) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
}

/*************************************************************************
/*Purpose	: Return Yes / No.
/*Input		: $pIntValue :: Value
/*Returns	: Yes / No
/*Created By: Jaiswar Vipin Kumar R.
/*************************************************************************/
function getYesNo($pIntValue = 0){
	if($pIntValue == 1){
		return 'Yes';
	}else{
		return 'No';
	}
}
/*************************************************************************
/*Purpose	: Generating SLUG.
/*Input		: $pStrNormaString :: Normal String,
			: $pBlnForTableName :: used from table name generation.
/*Returns	: Slug value
/*Created By: Jaiswar Vipin Kumar R.
/*************************************************************************/
function getSlugify($pStrNormaString, $pBlnForTableName = false){
	/* if request for table name generation then do needful */
	if($pBlnForTableName){
		/* replace non letter or digits by - */
		$pStrNormaString = preg_replace('~[^\pL\d]+~u', '_', $pStrNormaString);
	}else{
		/* replace non letter or digits by - */
		$pStrNormaString = preg_replace('~[^\pL\d]+~u', '-', $pStrNormaString);
	}
	/* transliterate */
	$pStrNormaString = iconv('utf-8', 'us-ascii//TRANSLIT', $pStrNormaString);
	/* remove unwanted characters */
	$pStrNormaString = preg_replace('~[^-\w]+~', '', $pStrNormaString);
	/* trim */
	$pStrNormaString = trim($pStrNormaString, '_');
	/* Lower case */
	$pStrNormaString = strtolower($pStrNormaString);
	/* If string is empty then do needful */
	if (empty($pStrNormaString)) {
		return '';
	}
	/* Return String */
	return $pStrNormaString;
}

/**********************************************************************/
/*Purpose 	: Getting date format.
/*Inputs	: $pIntDate :: Date,
			: $intDateFormat	:: Date format type.
/*Returns 	: Format Date .
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getDateFormat($pIntDate = 0, $intDateFormat = 0){
	/*IF date is not passed then do needful */
	if($pIntDate == 0){
		/* Return empty */
		return '-';
	}
	
	/* Return date format type */
	switch($intDateFormat){
		default:
			return date('d M Y<\b\\r>H:i:s A',strtotime($pIntDate));
			break;
		/* Get database insertion format date and time  */
		case 1:
			return str_replace(array('/',':'),array('',''),$pIntDate);
			break;
		case 2:
			return date('d M Y',strtotime($pIntDate));
			break;
		case 3:
			return date('M-Y',strtotime($pIntDate));
			break;
		case 4:
			return date('d-M',strtotime($pIntDate));
			break;
		case 5:
			return date('Y/m/d',strtotime($pIntDate));
			break;
		case 6:
			return date('Ymd',strtotime($pIntDate));
			break;
		case 7:
			return date('Y-m-d',strtotime($pIntDate));
			break;
		case 8:
			return date('Ym',strtotime($pIntDate));
			break;
		case 9:
			return date('m/d/Y H:i:s A',strtotime($pIntDate));
			break;
	}
}

/**********************************************************************/
/*Purpose 	: Getting diffrence between 2 dates.
/*Inputs	: $pIntFromDate :: From Date,
			: $pIntToDate :: To Date,
			: $pIntReturnType : 1: Day, 2:Month, 3:Year.
/*Returns 	: Return date diff in requested format.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getDateDiff($pIntFromDate = 0, $pIntToDate = 0, $pIntReturnType= 1){
	/* if from date or to date is missing then do needful */
	if(($pIntFromDate == 0) || ($pIntToDate == 0)){
		/* return the date */
		return 0;
	}
	/* set the date */
	$intDateDiff	= strtotime(getDateFormat($pIntToDate,7)) - strtotime(getDateFormat($pIntFromDate,7));
	
	/* based on the requested type return the diffrence */
	switch($pIntReturnType){
		/* Day */
		case 1:
			return $intDateDiff/86400;
			break;
		/* Month */
		case 2:
			return $intDateDiff/2592000;
			break;
	}
}

/**********************************************************************/
/*Purpose 	: Getting date based on the passed interval.
/*Inputs	: $pIntDayCount :: Day Count,
			: $intDateFormat	:: Date format type.
/*Returns 	: Requested in the requested Format Date .
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getDates($pIntDayCount = 0, $intDateFormat = 0){
	/* Creating the date */
	$intDate = date('YmdHis',mktime(date('H'),date('i'),date('s'),date('m'),(date('d')+$pIntDayCount),date('Y')));
	
	/* return the date format */
	return getDateFormat($intDate,$intDateFormat);
}

/**********************************************************************/
/*Purpose 	: Getting date by quater.
/*Inputs	: $pIntQuater :: Quater index,
			: $pBlnPreviousYear :: if value is negative then its Previous year.
/*Returns 	: Returns the data range.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getDatesIntervalByQauter($pIntQuater = 1, $pBlnPreviousYear = 0){
	/* Variable initialization */
	$intYear	= date('Y');
	
	/* if requested quater of previous year then do needful */
	if($pBlnPreviousYear < 0){
		/* setting the previous year */
		$intYear	= $intYear - 1;
	}
	
	/* Set the variables */
	$strDateArr	= array(
								'1'=>array('from'=>$intYear.'0401' , 'to'=>$intYear.'0630'),
								'2'=>array('from'=>$intYear.'0701' , 'to'=>$intYear.'0930'),
								'3'=>array('from'=>$intYear.'1001' , 'to'=>$intYear.'1231'),
								'4'=>array('from'=>($intYear+1).'0101' , 'to'=>($intYear+1).'0331')
							);
	
	/* return the date format */
	return isset($strDateArr[$pIntQuater])?$strDateArr[$pIntQuater]:array('from'=>getDates(0,6),'to'=>getDates(90,6));
}

/**********************************************************************/
/*Purpose 	: Getting Quater Number.
/*Inputs	: $pMonthNumber :: Month Number.
/*Returns 	: Quater number.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getQauter($pMonthNumber = 0){
	/* checking for passed value and setting default is current month */
	$pMonthNumber	= ($pMonthNumber == 0)?date('m'):$pMonthNumber;
	$pMonthNumber	= ($pMonthNumber < 0)?($pMonthNumber * -1):$pMonthNumber;
	$intReturnQuater=4;
	
	/* if first quater then do needful */
	if(($pMonthNumber>=4) && ($pMonthNumber<=6)){
		$intReturnQuater = 1;
	}elseif(($pMonthNumber>=7) && ($pMonthNumber<=9)){
		$intReturnQuater = 2;
	}elseif(($pMonthNumber>=10) && ($pMonthNumber<=12)){
		$intReturnQuater = 3;
	}
	/* if current month is not equal to the reqeusted month then do needful */
	if((date('m') >= 1) && (date('m') <= 3) && (date('m') != $pMonthNumber)){
		/* Move to the previous quater */
		$intReturnQuater = $intReturnQuater * -1;
	}
	
	/* return the quater */
	return $intReturnQuater;
}

/**********************************************************************/
/*Purpose 	: Get workign dates.
/*Inputs	: $pMonthNumber :: Number of working date.
/*Returns 	: Date array.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getWorkingDayAsDate($pIntnumberOfDays = 8){
	/* Variable initilization */
	$strReturnDateArr	= array();
	
	/* iterating the loop */
	for($intIntCouner = 1; $intIntCouner < 200; $intIntCouner++){
		$intDay	= date('N',mktime(date('H'),date('i'),date('s'),date('m'),(date('d')-$intIntCouner),date('Y')));
		if(!in_array($intDay, array(7))){
			$strReturnDateArr['display_date'][] =  getDates(($intIntCouner * -1), 4);
			$strReturnDateArr['filter_date'][] =  getDates(($intIntCouner * -1), 6);
		}
		
		if((isset($strReturnDateArr['display_date'])) && (count($strReturnDateArr['display_date']) >= $pIntnumberOfDays)){
			break;
		}
	}
	/* return the date */
	return $strReturnDateArr;
}

/**********************************************************************/
/*Purpose 	: Decoding the array key to normal value.
/*Inputs	: $pStrValueSetArr	:: Value array
			: $isValueDecode : value need to decode.
/*Returns 	: Decoded array.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function decodeKeyValueArr($pStrValueSetArr = array(), $isValueDecode = false){
	/* Variable initialization */
	$strReturnArr	= array();
	
	/* if empty array found then do needful */
	if(empty($pStrValueSetArr)){
		/* return array */
		return $strReturnArr;
	}
	/* Iterating the loop */
	foreach($pStrValueSetArr as $pStrValueSetArrKey => $pStrValueSetArrValue){
		/* if value needs to decode then do needful */
		if($isValueDecode){
			/* Setting the value */
			$strReturnArr[$pStrValueSetArrKey]	= getDecyptionValue($pStrValueSetArrValue);
		}else{
			/* Setting the value */
			$strReturnArr[getDecyptionValue($pStrValueSetArrKey)]	= $pStrValueSetArrValue;
		}
	}
	/* return array */
	return $strReturnArr;
}

/**********************************************************************/
/*Purpose 	: Number Formatting.
/*Inputs	: $pNumber	:: Value,
			: $pIntFormatingType :: Formatting type.
/*Returns 	: Formatting number.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function numberFormating($pNumber = 0, $pIntFormatingType = 0){
	/* Checking passed value is not number */
	if(!is_numeric($pNumber)){
		/* setting default value */
		$pNumber	= 0;
	}
	
	/* based on the Formatting type doing processing */
	switch($pIntFormatingType){
		case 0:
		default:
			$pNumber	= number_format($pNumber, 2, '.','');
			break;
	}
	
	/* return formatted number */
	return $pNumber;
}

/**********************************************************************/
/*Purpose 	: Redirecting to other page .
/*Inputs	: $pStrDestinationURL	:: Destination URL.
/*Returns 	: None.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function redirect($pStrDestinationURL){
	die('<script language="JavaScript">window.location.href = "'.$pStrDestinationURL.'"</script>');
}

/**********************************************************************/
/*Purpose 	: Redirecting to other page .
/*Inputs	: $pStrDestinationURL	:: Destination URL.
/*Returns 	: None.
/*Created By: Jaiswar Vipin Kumar R.
/**********************************************************************/
function getViewPath($pStrViewName = ''){
	die('<script language="JavaScript">window.location.href = "'.$pStrDestinationURL.'"</script>');
}

/*************************************************************************************************************/
/*Purpose 	: Processing the widget HTML and replacing KEY place with operational values.
/*Inputs	: $pStrWidgetHooksHTML	:: Widget HTML,
			: $pStrKeyValueArr :: Data set,
			: $pStrSkipColumnArr :: Skip column array.
/*Returns 	: final widget HTML.
/*Created By: Jaiswar Vipin Kumar R.
/*************************************************************************************************************/
function processingWidgeHooks($pStrWidgetHooksHTML = '', $pStrKeyValueArr = array(), $pStrSkipColumnArr = array()){
	/* variable initialization */
	$strReturnHTML	= $pStrWidgetHooksHTML;
	
	/* if widget HTML or key value array is empty then do needful */
	if(($pStrWidgetHooksHTML == '') || (empty($pStrKeyValueArr))){
		/* Return the default widget HTML */
		return $strReturnHTML;
	}
	
	/* iterating the value */
	foreach($pStrKeyValueArr  as $pStrKeyValueArrKey => $pStrKeyValueArrValue){
		/* checking for skip encryption */
		if(in_array($pStrKeyValueArrKey, $pStrSkipColumnArr)){
			/* update the key with values */
			$strReturnHTML	= str_replace('{'.$pStrKeyValueArrKey.'}',($pStrKeyValueArrValue),$strReturnHTML);

		}else{
			/* update the key with values */
			$strReturnHTML	= str_replace('{'.$pStrKeyValueArrKey.'}',getEncyptionValue($pStrKeyValueArrValue),$strReturnHTML);
		}
	}
	
	/* return HTML */
	return $strReturnHTML;
}


function array_to_csv($array, $download = ""){

    if ($download != "")
    {    
        header('Content-Description: File Transfer');
        header("Content-type: application/vnd.ms-excel; charset=utf-8");
        header('Content-Disposition: attachement; filename="' . $download . '"');
        header('Content-Transfer-Encoding: binary');
    }        

    ob_start();
    $f = fopen('php://output', 'w') or show_error("Can't open php://output");
    $n = 0;        
    foreach ($array as $line)
    {
        $n++;
        if ( ! fputcsv($f, $line))
        {
            show_error("Can't write line $n: $line");
        }
    }
    fclose($f) or show_error("Can't close php://output");
    $str = ob_get_contents();
    ob_end_clean();

    if ($download == "")
    {
        return $str;    
    }
    else
    {    
        // print "\xEF\xBB\xBF"; // UTF-8 BOM
        print $str;
        exit;
    }        
}




/*************************************************************************************************************/
/*Purpose 	: Get IP Address of atual user not balance server
/*Inputs	: 
/*Returns 	: IP Address of Client
/*Created By: Prashant S. Pawar
/*************************************************************************************************************/
function get_client_ip() {
	$ipaddress = '';
	if (getenv('HTTP_CLIENT_IP'))
		$ipaddress = getenv('HTTP_CLIENT_IP');
	else if(getenv('HTTP_X_FORWARDED_FOR'))
		$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	else if(getenv('HTTP_X_FORWARDED'))
		$ipaddress = getenv('HTTP_X_FORWARDED');
	else if(getenv('HTTP_FORWARDED_FOR'))
		$ipaddress = getenv('HTTP_FORWARDED_FOR');
	else if(getenv('HTTP_FORWARDED'))
		$ipaddress = getenv('HTTP_FORWARDED');
	else if(getenv('REMOTE_ADDR'))
		$ipaddress = getenv('REMOTE_ADDR');
	else
		$ipaddress = 'UNKNOWN';
	return $ipaddress;
}

function verifyDate($date, $format = 'Y/m/d'){
	return (DateTime::createFromFormat($format, $date) !== false);
}

function verifyDateWithReturnObj($date, $format = 'Y/m/d')
{
    return DateTime::createFromFormat($format, $date);
}

/**********************************************************************/
/*Purpose 	: convert the array in JSON and response to caller.
/*Inputs	: $pStrDataArr :: requested array / string,
			: $returnContentType	:: Return content type
			: $statusCode	:: Return http status code
			: $pBlnExit	:: Terminate the execution.
/*Returns 	: None.
/*Created By: Vipin Kumar R. Jaiswar
/**********************************************************************/
function jsonHttpReturn($pStrDataArr = array(), $returnContentType = 'application/json', $statusCode = 200, $pBlnExit = false){

	$CI = & get_instance();

	return $CI->output
					->set_content_type($returnContentType)
					->set_status_header($statusCode)
					->set_output(json_encode($pStrDataArr));

	/* if termination request then do needful */
	if($pBlnExit){
		/* convert array in json format */
		die(json_encode($pStrDataArr));
	}else{
		/* return the JSON encoded string to caller */
		return json_encode($pStrDataArr);
	}

}

/**********************************************************************/
/*Purpose 	: get the length for a text string.
/*Inputs	: strText the string text for which length needs to be calculated.
/*Returns 	: Length of the string.
/*Created By: Vipin Kumar R. Jaiswar.
/**********************************************************************/
function calculateStringLength($strText){
	//
	$length = mb_strlen(str_replace(array("\r", "\n", "\t"), array(" "), $strText));
	return $length;
}

/**********************************************************************/
/*Purpose 	: Return the delivery status class name based on value.
/*Inputs	: $intValue :: Value.
/*Returns 	: Length of the string.
/*Created By: Vipin Kumar R. Jaiswar.
/**********************************************************************/
function getDeliveryStatusColour($intValue = 0){
	if($intValue >= 0){
		return 'white-text green darken-1';
	}else if(($intValue < 0) && ($intValue >= -5)){
		return 'yellow darken-1';
	}else{
		return 'white-text red darken-1';
	}
}